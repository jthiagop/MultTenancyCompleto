<?php

namespace App\Jobs;

use App\Models\Tenant;
use App\Models\DomusDocumento;
use App\Services\Ai\DocumentExtractorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;

class AnalyzeDocumentWithAi implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120; // 2 minutos (IA pode demorar)
    public $tries = 2;     // Tentar apenas 2x para não gastar API à toa

    protected $tenantId;
    protected $filePath;
    protected $userPhone;
    protected $companyId;

    public function __construct($tenantId, $filePath, $userPhone, $companyId = null)
    {
        $this->tenantId = $tenantId;
        $this->filePath = $filePath;
        $this->userPhone = $userPhone;
        $this->companyId = $companyId;
    }

    public function handle()
    {
        $tenant = Tenant::find($this->tenantId);

        if (!$tenant) {
            Log::error("Tenant {$this->tenantId} não encontrado no Job de IA.");
            $this->sendWhatsAppReply("❌ Erro: Tenant não encontrado.");
            return;
        }

        $tenant->run(function () {
            // Instanciar o serviço com o companyId correto (em queue não há sessão)
            $extractor = new DocumentExtractorService($this->companyId);

            try {
                Log::info("🤖 Iniciando análise de IA para: {$this->filePath}");

                // 1. Validar e Ler Arquivo
                if (!Storage::disk('public')->exists($this->filePath)) {
                    throw new Exception("Arquivo não encontrado no storage: {$this->filePath}");
                }

                $mimeType = Storage::disk('public')->mimeType($this->filePath);

                // Validação de tipo (O GPT-4o Vision aceita png, jpeg, webp, gif, pdf)
                $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/gif', 'application/pdf'];
                if (!in_array($mimeType, $allowedTypes)) {
                    $this->sendWhatsAppReply("⚠️ Tipo de arquivo não suportado. Envie uma foto (JPG/PNG) ou PDF.");
                    return;
                }

                $fileContent = Storage::disk('public')->get($this->filePath);
                $base64Content = base64_encode($fileContent);

                // 2. Extrair Dados com GPT-4o
                $extractedData = $extractor->extractData($base64Content, $mimeType);

                if (empty($extractedData)) {
                    throw new Exception("A IA não retornou dados válidos.");
                }

                // 3. Buscar registro em domus_documentos pelo caminho_arquivo
                $documento = DomusDocumento::where('caminho_arquivo', $this->filePath)->first();

                if (!$documento) {
                    throw new Exception("Registro em domus_documentos não encontrado para: {$this->filePath}");
                }

                // 4. Gerar nome descritivo a partir dos dados extraídos
                $nomeArquivo = $extractor->generateNameFromExtractedData($extractedData);

                // 5. Extrair campos principais dos dados extraídos
                $financeiro = $extractedData['financeiro'] ?? [];
                $estabelecimento = $extractedData['estabelecimento'] ?? [];

                // 6. Atualizar registro em domus_documentos
                $documento->update([
                    'nome_arquivo' => $nomeArquivo,
                    'dados_extraidos' => $extractedData,
                    'tipo_documento' => $extractedData['tipo_documento'] ?? 'OUTRO',
                    'estabelecimento_nome' => $estabelecimento['nome'] ?? null,
                    'estabelecimento_cnpj' => $estabelecimento['cnpj'] ?? null,
                    'data_emissao' => $financeiro['data_emissao'] ?? null,
                    'valor_total' => $financeiro['valor_total'] ?? null,
                    'forma_pagamento' => $financeiro['forma_pagamento'] ?? null,
                    'status' => \App\Enums\StatusDomusDocumento::PROCESSADO,
                    'processado_em' => now(),
                ]);

                Log::info("Registro em domus_documentos atualizado com dados da IA", [
                    'documento_id' => $documento->id,
                    'nome_arquivo' => $nomeArquivo,
                    'status' => \App\Enums\StatusDomusDocumento::PROCESSADO->value,
                ]);

                // 7. Gerar mensagem natural usando IA (Cérebro Duplo - Parte 2)
                $naturalMessage = $this->generateNaturalResponse($extractedData);

                // 8. Enviar a mensagem gerada pela IA
                $this->sendWhatsAppReply($naturalMessage);

            } catch (\Exception $e) {
                Log::error("❌ Erro na Análise de IA: " . $e->getMessage(), [
                    'trace' => $e->getTraceAsString(),
                    'file_path' => $this->filePath,
                ]);
                $this->sendWhatsAppReply("😕 Não consegui ler este documento. Tente enviar uma foto mais clara ou digite os dados manualmente.");
            }
        });
    }

    /**
     * Gera uma mensagem natural para WhatsApp usando IA (gpt-4o-mini)
     * Esta é a segunda parte do "Cérebro Duplo": após extrair dados precisos,
     * gera uma mensagem conversacional amigável
     *
     * @param array $extractedData
     * @return string
     */
    private function generateNaturalResponse(array $extractedData): string
    {
        try {
            $apiKey = config('services.openai.key');
            if (empty($apiKey)) {
                // Fallback para mensagem simples se API não estiver configurada
                return $this->generateFallbackMessage($extractedData);
            }

            // Extrair dados do array extraído
            $financeiro = $extractedData['financeiro'] ?? [];
            $estabelecimento = $extractedData['estabelecimento'] ?? [];
            $classificacao = $extractedData['classificacao'] ?? [];

            $valorTotal = $financeiro['valor_total'] ?? 0;
            $valorFormatado = number_format($valorTotal, 2, ',', '.');
            $entidadeNome = $estabelecimento['nome'] ?? 'Fornecedor Desconhecido';

            // Formatar data
            $dataFormatada = 'Data n/d';
            if (!empty($financeiro['data_emissao'])) {
                try {
                    $data = new \DateTime($financeiro['data_emissao']);
                    $dataFormatada = $data->format('d/m/Y');
                } catch (\Exception $e) {
                    // Manter 'Data n/d' se não conseguir parsear
                }
            }

            // --- LÓGICA DE RETENÇÃO DE IMPOSTOS ---
            $impostosRetidos = $financeiro['impostos_retidos'] ?? 0.00;
            $alertasFiscais = "";

            if ($impostosRetidos > 0) {
                // Calculamos o Líquido para facilitar a vida do tesoureiro
                $valorLiquido = $valorTotal - $impostosRetidos;
                $valorLiquidoFmt = number_format($valorLiquido, 2, ',', '.');
                $impostosFmt = number_format($impostosRetidos, 2, ',', '.');

                $alertasFiscais = "\n\n⚠️ ALERTA DE RETENÇÃO:\n";
                $alertasFiscais .= "- Este documento tem R$ {$impostosFmt} de impostos retidos.\n";
                $alertasFiscais .= "- O Valor BRUTO é R$ {$valorFormatado}.\n";
                $alertasFiscais .= "- O Valor LÍQUIDO (a pagar ao fornecedor) é R$ {$valorLiquidoFmt}.\n";
                $alertasFiscais .= "AVISE O USUÁRIO PARA NÃO PAGAR O VALOR TOTAL!";
            }
            // ---------------------------------------

            // Preparar contexto com dados principais
            $contextoDados = "Fornecedor: {$entidadeNome}\n";
            $contextoDados .= "Valor Total da Nota: R$ {$valorFormatado}\n";
            $contextoDados .= "Data: {$dataFormatada}\n";

            if (!empty($classificacao['descricao_detalhada'])) {
                $contextoDados .= "Descrição: {$classificacao['descricao_detalhada']}\n";
            }

            // Adicionar alerta fiscal se houver retenção
            if (!empty($alertasFiscais)) {
                $contextoDados .= $alertasFiscais;
            }

            // Adicionar detalhes extras se existirem
            $detalhesExtras = [];
            if (($financeiro['juros'] ?? 0) > 0) {
                $detalhesExtras[] = "Juros: R$ " . number_format($financeiro['juros'], 2, ',', '.');
            }
            if (($financeiro['multa'] ?? 0) > 0) {
                $detalhesExtras[] = "Multa: R$ " . number_format($financeiro['multa'], 2, ',', '.');
            }
            if (($financeiro['desconto'] ?? 0) > 0) {
                $detalhesExtras[] = "Desconto: R$ " . number_format($financeiro['desconto'], 2, ',', '.');
            }

            // Verificar parcelamento
            $parcelamento = $extractedData['parcelamento'] ?? [];
            if (!empty($parcelamento['is_parcelado']) && $parcelamento['total_parcelas'] > 1) {
                $detalhesExtras[] = "Parcela: {$parcelamento['parcela_atual']}/{$parcelamento['total_parcelas']}";
            }

            if (!empty($detalhesExtras)) {
                $contextoDados .= "\n\nDetalhes Extras:\n" . implode("\n", $detalhesExtras);
            }

            // Prompt do sistema para personalidade
            $systemPrompt = <<<'PROMPT'
Você é o "Domus IA", um assistente financeiro digital da Paróquia e conventos, eficiente, educado e profissional.

Sua tarefa é gerar mensagens curtas e naturais para WhatsApp sobre documentos fiscais processados.

REGRAS:
1. NUNCA altere os valores financeiros - use exatamente os dados fornecidos
2. SEMPRE mencione que é um "Rascunho" que precisa de confirmação no painel
3. Use emojis moderadamente (2-4 emojis por mensagem)
4. Seja conciso (máximo 200 caracteres)
5. Varie as frases de início e fim para não parecer robótico
6. Se houver "Detalhes Extras" (juros, multa, parcelamento), mencione-os sutilmente
7. Use tom profissional mas amigável
8. Retorne APENAS a mensagem, sem explicações adicionais

Personalidade:
    - Respeitoso, Servidor e Profissional.
    - Use um tom acolhedor, mas focado na organização.
    - Evite gírias mundanas. Prefira 'Prezado(a)', 'Fratello', 'Paz e bem', 'Tudo em paz?', 'Obrigado'.
    - Use emojis que remetam à organização e à igreja (⛪, 📝, ✅, 🕯️, 🙏).

Instruções de Resposta:
    1. Confirme o recebimento do documento citando o Fornecedor e Valor.
    2. Se identificar itens litúrgicos (velas, hóstias, vinho), pode fazer um breve comentário sutil (ex: 'Materiais para a liturgia identificados').
    3. SEMPRE lembre que o lançamento é um RASCUNHO e precisa de conferência no painel.
    4. Finalize com uma saudação cordial (ex: 'Fico à disposição', 'Deus abençoe', 'Paz e bem').

Instruções Críticas sobre Impostos:
    1. Se receber um 'ALERTA DE RETENÇÃO', você DEVE destacar isso na mensagem de forma clara e visível.
    2. Diga explicitamente: 'Atenção: Há impostos retidos'.
    3. Informe o valor LÍQUIDO que deve ser pago ao fornecedor para evitar prejuízo.
    4. Use um emoji de alerta (⚠️ ou 🛑) nessa linha.
    5. Seja enfático: é importante não pagar o valor total quando há retenção.

Formato da mensagem:
- Título/Confirmação (com emoji)
- Fornecedor
- Valor
- Data
- Detalhes extras (se houver)
- Lembrete sobre rascunho
PROMPT;

            // Fazer requisição para gpt-4o-mini (modelo mais leve e rápido)
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini', // Modelo mais leve e barato
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $systemPrompt,
                    ],
                    [
                        'role' => 'user',
                        'content' => "Gere uma mensagem natural para WhatsApp com estes dados:\n\n{$contextoDados}",
                    ],
                ],
                'temperature' => 0.7, // Um pouco de criatividade para variar as mensagens
                'max_tokens' => 250, // Permitir mensagens um pouco maiores
            ]);

            if ($response->successful()) {
                $responseData = $response->json();
                $message = trim($responseData['choices'][0]['message']['content'] ?? '');

                if (!empty($message)) {
                    Log::info("Mensagem natural gerada pela IA", [
                        'message_length' => strlen($message),
                    ]);
                    return $message;
                }
            } else {
                Log::warning("Erro ao gerar mensagem natural, usando fallback", [
                    'status' => $response->status(),
                    'error' => $response->json(),
                ]);
            }
        } catch (\Exception $e) {
            Log::warning("Erro ao gerar mensagem natural, usando fallback", [
                'error' => $e->getMessage(),
            ]);
        }

        // Fallback para mensagem simples se a IA falhar
        return $this->generateFallbackMessage($extractedData);
    }

    /**
     * Gera mensagem de fallback (simples) quando a IA não está disponível
     *
     * @param array $extractedData
     * @return string
     */
    private function generateFallbackMessage(array $extractedData): string
    {
        // Extrair dados do array extraído
        $financeiro = $extractedData['financeiro'] ?? [];
        $estabelecimento = $extractedData['estabelecimento'] ?? [];

        $valorFormatado = number_format($financeiro['valor_total'] ?? 0, 2, ',', '.');
        $entidadeNome = $estabelecimento['nome'] ?? 'Fornecedor Desconhecido';

        // Formatar data
        $dataFormatada = 'Data n/d';
        if (!empty($financeiro['data_emissao'])) {
            try {
                $data = new \DateTime($financeiro['data_emissao']);
                $dataFormatada = $data->format('d/m/Y');
            } catch (\Exception $e) {
                // Manter 'Data n/d' se não conseguir parsear
            }
        }

        $msg = "✅ *Leitura Concluída!*\n\n";
        $msg .= "🏢 *{$entidadeNome}*\n";
        $msg .= "💰 *R$ {$valorFormatado}*\n";
        $msg .= "📅 {$dataFormatada}\n\n";
        $msg .= "📝 O documento foi processado e está disponível para revisão no painel.";

        return $msg;
    }

    /**
     * Envia resposta via WhatsApp
     *
     * @param string $message
     * @return void
     */
    private function sendWhatsAppReply(string $message): void
    {
        try {
            $phoneNumberId = config('services.meta.phone_id');
            $token = config('services.meta.token');

            if (!$phoneNumberId || !$token) {
                Log::error("Configuração WhatsApp não encontrada para envio de resposta");
                return;
            }

            $response = Http::withToken($token)
                ->timeout(10)
                ->post("https://graph.facebook.com/v21.0/{$phoneNumberId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'to' => $this->userPhone,
                    'type' => 'text',
                    'text' => ['body' => $message]
                ]);

            if ($response->successful()) {
                Log::info("Resposta enviada via WhatsApp para {$this->userPhone}");
            } else {
                Log::error("Erro ao enviar resposta WhatsApp: " . $response->body());
            }
        } catch (\Exception $e) {
            Log::error("Erro ao enviar resposta WhatsApp: " . $e->getMessage());
        }
    }
}


