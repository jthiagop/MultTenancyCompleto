<?php

namespace App\Jobs;

use App\Enums\StatusDomusDocumento;
use App\Events\Ia\DocumentoProcessado;
use App\Models\DomusDocumento;
use App\Models\Tenant;
use App\Models\WhatsappAuthRequest;
use App\Services\Ai\DocumentExtractorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class AnalyzeDocumentWithAi implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Tempo total permitido por tentativa (extração de imagem/PDF + chamada extra
     * para mensagem natural). PDFs com até 5 páginas + retries internos da OpenAI
     * podem facilmente passar de 2 minutos.
     */
    public $timeout = 240;

    /**
     * Número de tentativas externas (a OpenAI já é tentada 3x dentro do
     * DocumentExtractorService).
     */
    public $tries = 2;

    /**
     * Backoff progressivo entre tentativas (segundos) — evita martelar a OpenAI
     * caso a primeira falha tenha sido por rate-limit.
     */
    public $backoff = [30, 120];

    /**
     * Limite de tamanho de arquivo (mantido alinhado com DocumentExtractorService).
     */
    private const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB

    /**
     * Status de documento que indicam que NÃO devemos reprocessar.
     */
    private const STATUS_IDEMPOTENTES = [
        'processado',
        'lancado',
        'arquivado',
    ];

    protected string $tenantId;
    protected string $filePath;
    protected string $userPhone;
    protected ?int $companyId;

    public function __construct(string $tenantId, string $filePath, string $userPhone, ?int $companyId = null)
    {
        $this->tenantId = $tenantId;
        $this->filePath = $filePath;
        $this->userPhone = $userPhone;
        $this->companyId = $companyId;
    }

    public function handle(): void
    {
        $tenant = Tenant::find($this->tenantId);

        if (!$tenant) {
            Log::error("Tenant {$this->tenantId} não encontrado no Job de IA.");
            $this->sendWhatsAppReply("❌ Erro: Tenant não encontrado.");
            return;
        }

        $tenant->run(function () {
            $extractor = new DocumentExtractorService($this->companyId);

            try {
                Log::info("🤖 Iniciando análise de IA", [
                    'file_path' => $this->filePath,
                    'tenant_id' => $this->tenantId,
                ]);

                // 1. Localizar registro ANTES da extração — permite idempotência
                //    e gravação de erro caso algo falhe.
                $documento = DomusDocumento::where('caminho_arquivo', $this->filePath)->first();

                if (!$documento) {
                    throw new \RuntimeException("Registro em domus_documentos não encontrado para: {$this->filePath}");
                }

                // 2. Idempotência — se já foi processado/lançado/arquivado, abortar.
                $statusAtual = $documento->getRawOriginal('status') ?? 'pendente';
                if (in_array($statusAtual, self::STATUS_IDEMPOTENTES, true)) {
                    Log::info("Documento já processado anteriormente — pulando re-análise", [
                        'documento_id' => $documento->id,
                        'status_atual' => $statusAtual,
                    ]);
                    return;
                }

                // 3. Validar arquivo no storage
                if (!Storage::disk('public')->exists($this->filePath)) {
                    throw new \RuntimeException("Arquivo não encontrado no storage: {$this->filePath}");
                }

                $mimeType = Storage::disk('public')->mimeType($this->filePath);
                $fileSize = Storage::disk('public')->size($this->filePath);

                // 4. Validar tipo
                $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'application/pdf'];
                if (!in_array($mimeType, $allowedTypes, true)) {
                    $this->markDocumentoAsError($documento, "Tipo de arquivo não suportado: {$mimeType}");
                    $this->sendWhatsAppReply("⚠️ Tipo de arquivo não suportado. Envie uma foto (JPG/PNG) ou PDF.");
                    return;
                }

                // 5. Validar tamanho — evita estourar token-limit/contexto da OpenAI
                if ($fileSize > self::MAX_FILE_SIZE) {
                    $sizeMb = round($fileSize / (1024 * 1024), 2);
                    $maxMb = round(self::MAX_FILE_SIZE / (1024 * 1024), 2);
                    $msg = "Arquivo muito grande ({$sizeMb}MB). Limite: {$maxMb}MB.";
                    $this->markDocumentoAsError($documento, $msg);
                    $this->sendWhatsAppReply("⚠️ {$msg} Por favor, envie um arquivo menor.");
                    return;
                }

                $fileContent = Storage::disk('public')->get($this->filePath);
                $base64Content = base64_encode($fileContent);

                // 6. Extração estruturada com GPT-4o
                $extractedData = $extractor->extractData($base64Content, $mimeType);

                if (empty($extractedData)) {
                    throw new \RuntimeException('A IA não retornou dados válidos.');
                }

                // 7. Atualizar registro com dados extraídos
                $financeiro = $extractedData['financeiro'] ?? [];
                $estabelecimento = $extractedData['estabelecimento'] ?? [];
                $nomeArquivo = $extractor->generateNameFromExtractedData($extractedData);

                $documento->update([
                    'nome_arquivo' => $nomeArquivo,
                    'dados_extraidos' => $extractedData,
                    'tipo_documento' => $extractedData['tipo_documento'] ?? 'OUTRO',
                    'estabelecimento_nome' => $estabelecimento['nome'] ?? null,
                    'estabelecimento_cnpj' => $estabelecimento['cnpj'] ?? null,
                    'data_emissao' => $financeiro['data_emissao'] ?? null,
                    'valor_total' => $financeiro['valor_total'] ?? null,
                    'forma_pagamento' => $financeiro['forma_pagamento'] ?? null,
                    'status' => StatusDomusDocumento::PROCESSADO,
                    'processado_em' => now(),
                    'erro_processamento' => null,
                ]);

                Log::info('Documento processado pela IA', [
                    'documento_id' => $documento->id,
                    'tipo_documento' => $documento->tipo_documento,
                    'tem_retencao' => ($financeiro['impostos_retidos'] ?? 0) > 0,
                ]);

                // 8. Mensagem natural (cérebro duplo — etapa 2)
                $naturalMessage = $this->generateNaturalResponse($extractedData);
                $this->sendWhatsAppReply($naturalMessage);

                // 9. Notificar front-end em tempo real (Echo/Reverb) — falha
                //    silenciosa se nenhum driver de broadcast estiver configurado.
                $this->broadcastStatus($documento->id, 'processado', $naturalMessage);

            } catch (Throwable $e) {
                $this->logException($e);

                // Marca o documento como ERRO somente se ainda não foi marcado
                if (isset($documento) && $documento instanceof DomusDocumento) {
                    $this->markDocumentoAsError($documento, $e->getMessage());
                    $this->broadcastStatus($documento->id, 'erro', $e->getMessage());
                }

                $this->sendWhatsAppReply('😕 Não consegui ler este documento. Tente enviar uma foto mais clara ou digite os dados manualmente.');
            }
        });
    }

    /**
     * Callback chamado pelo Laravel quando todas as tentativas se esgotam.
     * Garante que o documento fique com status ERRO e que o usuário receba
     * uma resposta final, mesmo que o erro tenha ocorrido fora do try/catch
     * (ex.: timeout do worker, OOM).
     */
    public function failed(Throwable $exception): void
    {
        $this->logException($exception, true);

        $tenant = Tenant::find($this->tenantId);
        if (!$tenant) {
            return;
        }

        $tenant->run(function () use ($exception) {
            $documento = DomusDocumento::where('caminho_arquivo', $this->filePath)->first();
            if ($documento) {
                $statusAtual = $documento->getRawOriginal('status') ?? 'pendente';
                if (!in_array($statusAtual, self::STATUS_IDEMPOTENTES, true)) {
                    $this->markDocumentoAsError(
                        $documento,
                        'Falha definitiva após retries: ' . $exception->getMessage()
                    );
                    $this->broadcastStatus($documento->id, 'erro', $exception->getMessage());
                }
            }
        });

        $this->sendWhatsAppReply('😕 Não consegui processar este documento agora. Tente novamente em alguns minutos ou lance manualmente pelo painel.');
    }

    /**
     * Dispara o evento DocumentoProcessado para o canal privado do tenant.
     * Encapsulado em try/catch porque, sem driver de broadcast configurado
     * (ou em ambientes onde Reverb/Pusher caiu), o broadcast pode lançar e
     * isso NUNCA deve quebrar o fluxo principal do job.
     */
    private function broadcastStatus(int $documentoId, string $status, ?string $mensagem = null): void
    {
        try {
            event(new DocumentoProcessado(
                tenantId: $this->tenantId,
                documentoId: $documentoId,
                status: $status,
                mensagem: $mensagem ? mb_substr($mensagem, 0, 500) : null,
                userPhone: $this->userPhone,
            ));
        } catch (Throwable $e) {
            Log::warning('Broadcast DocumentoProcessado falhou (broadcast driver indisponível?)', [
                'documento_id' => $documentoId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Marca o documento com status ERRO e grava a mensagem do erro.
     */
    private function markDocumentoAsError(DomusDocumento $documento, string $message): void
    {
        try {
            $documento->update([
                'status' => StatusDomusDocumento::ERRO,
                'erro_processamento' => mb_substr($message, 0, 1000),
            ]);
        } catch (Throwable $e) {
            Log::error('Falha ao marcar documento como ERRO', [
                'documento_id' => $documento->id ?? null,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Loga uma exceção sem expor o trace completo em logs (LGPD).
     * Em DEBUG=true ainda inclui o trace para facilitar diagnóstico em dev.
     */
    private function logException(Throwable $e, bool $final = false): void
    {
        $context = [
            'exception' => get_class($e),
            'message' => $e->getMessage(),
            'file' => basename($e->getFile()) . ':' . $e->getLine(),
            'documento_path' => $this->filePath,
            'final' => $final,
        ];

        if (config('app.debug')) {
            $context['trace'] = $e->getTraceAsString();
        }

        Log::error($final ? '❌ Falha definitiva no Job de IA' : '❌ Erro na Análise de IA', $context);
    }

    /**
     * Resolve o par (phone_number_id, access_token) do tenant atual via
     * WhatsappAuthRequest. Faz fallback para config('services.meta.*') caso
     * o registro não exista (compatível com setups single-tenant antigos).
     *
     * @return array{0: ?string, 1: ?string}
     */
    private function resolveWhatsAppCredentials(): array
    {
        try {
            $authRequest = WhatsappAuthRequest::query()
                ->where('tenant_id', $this->tenantId)
                ->where('wa_id', $this->userPhone)
                ->where('status', 'active')
                ->orderByDesc('updated_at')
                ->first();

            if ($authRequest && $authRequest->phone_number_id && $authRequest->access_token) {
                return [$authRequest->phone_number_id, $authRequest->access_token];
            }

            // Fallback para o phone_number_id do tenant (sem filtrar por wa_id)
            $authByTenant = WhatsappAuthRequest::query()
                ->where('tenant_id', $this->tenantId)
                ->where('status', 'active')
                ->orderByDesc('updated_at')
                ->first();

            if ($authByTenant && $authByTenant->phone_number_id) {
                return [
                    $authByTenant->phone_number_id,
                    $authByTenant->access_token ?? config('services.meta.token'),
                ];
            }
        } catch (Throwable $e) {
            Log::warning('Falha ao resolver credenciais WhatsApp do tenant, usando fallback do .env', [
                'tenant_id' => $this->tenantId,
                'error' => $e->getMessage(),
            ]);
        }

        return [
            config('services.meta.phone_id'),
            config('services.meta.token'),
        ];
    }

    /**
     * Gera uma mensagem natural para WhatsApp usando IA (gpt-4o-mini)
     * Esta é a segunda parte do "Cérebro Duplo": após extrair dados precisos,
     * gera uma mensagem conversacional amigável
     */
    private function generateNaturalResponse(array $extractedData): string
    {
        try {
            $apiKey = config('services.openai.key');
            if (empty($apiKey)) {
                return $this->generateFallbackMessage($extractedData);
            }

            $financeiro = $extractedData['financeiro'] ?? [];
            $estabelecimento = $extractedData['estabelecimento'] ?? [];
            $classificacao = $extractedData['classificacao'] ?? [];

            $valorTotal = $financeiro['valor_total'] ?? 0;
            $valorFormatado = number_format($valorTotal, 2, ',', '.');
            $entidadeNome = $estabelecimento['nome'] ?? 'Fornecedor Desconhecido';

            $dataFormatada = 'Data n/d';
            if (!empty($financeiro['data_emissao'])) {
                try {
                    $data = new \DateTime($financeiro['data_emissao']);
                    $dataFormatada = $data->format('d/m/Y');
                } catch (\Exception $e) {
                    // mantém 'Data n/d'
                }
            }

            // --- Retenção de impostos ---
            $impostosRetidos = $financeiro['impostos_retidos'] ?? 0.00;
            $alertasFiscais = '';

            if ($impostosRetidos > 0) {
                $valorLiquido = $valorTotal - $impostosRetidos;
                $valorLiquidoFmt = number_format($valorLiquido, 2, ',', '.');
                $impostosFmt = number_format($impostosRetidos, 2, ',', '.');

                $alertasFiscais = "\n\n⚠️ ALERTA DE RETENÇÃO:\n"
                    . "- Este documento tem R$ {$impostosFmt} de impostos retidos.\n"
                    . "- O Valor BRUTO é R$ {$valorFormatado}.\n"
                    . "- O Valor LÍQUIDO (a pagar ao fornecedor) é R$ {$valorLiquidoFmt}.\n"
                    . 'AVISE O USUÁRIO PARA NÃO PAGAR O VALOR TOTAL!';
            }

            $contextoDados = "Fornecedor: {$entidadeNome}\n"
                . "Valor Total da Nota: R$ {$valorFormatado}\n"
                . "Data: {$dataFormatada}\n";

            if (!empty($classificacao['descricao_detalhada'])) {
                $contextoDados .= "Descrição: {$classificacao['descricao_detalhada']}\n";
            }

            if (!empty($alertasFiscais)) {
                $contextoDados .= $alertasFiscais;
            }

            $detalhesExtras = [];
            if (($financeiro['juros'] ?? 0) > 0) {
                $detalhesExtras[] = 'Juros: R$ ' . number_format($financeiro['juros'], 2, ',', '.');
            }
            if (($financeiro['multa'] ?? 0) > 0) {
                $detalhesExtras[] = 'Multa: R$ ' . number_format($financeiro['multa'], 2, ',', '.');
            }
            if (($financeiro['desconto'] ?? 0) > 0) {
                $detalhesExtras[] = 'Desconto: R$ ' . number_format($financeiro['desconto'], 2, ',', '.');
            }

            $parcelamento = $extractedData['parcelamento'] ?? [];
            if (!empty($parcelamento['is_parcelado']) && ($parcelamento['total_parcelas'] ?? 0) > 1) {
                $detalhesExtras[] = "Parcela: {$parcelamento['parcela_atual']}/{$parcelamento['total_parcelas']}";
            }

            if (!empty($detalhesExtras)) {
                $contextoDados .= "\n\nDetalhes Extras:\n" . implode("\n", $detalhesExtras);
            }

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

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])
                ->timeout(30)
                ->retry(2, 500, function ($exception) {
                    if ($exception instanceof \Illuminate\Http\Client\RequestException
                        && isset($exception->response)) {
                        return in_array($exception->response->status(), [429, 500, 502, 503, 504], true);
                    }
                    return false;
                }, throw: false)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        [
                            'role' => 'user',
                            'content' => "Gere uma mensagem natural para WhatsApp com estes dados:\n\n{$contextoDados}",
                        ],
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 250,
                ]);

            if ($response && $response->successful()) {
                $message = trim($response->json('choices.0.message.content', ''));
                if (!empty($message)) {
                    Log::info('Mensagem natural gerada pela IA', [
                        'message_length' => strlen($message),
                    ]);
                    return $message;
                }
            } elseif ($response) {
                Log::warning('Erro ao gerar mensagem natural, usando fallback', [
                    'status' => $response->status(),
                    'error_type' => $response->json('error.type'),
                ]);
            }
        } catch (Throwable $e) {
            Log::warning('Erro ao gerar mensagem natural, usando fallback', [
                'error' => $e->getMessage(),
            ]);
        }

        return $this->generateFallbackMessage($extractedData);
    }

    /**
     * Gera mensagem de fallback (simples) quando a IA não está disponível
     */
    private function generateFallbackMessage(array $extractedData): string
    {
        $financeiro = $extractedData['financeiro'] ?? [];
        $estabelecimento = $extractedData['estabelecimento'] ?? [];

        $valorTotal = $financeiro['valor_total'] ?? 0;
        $valorFormatado = number_format($valorTotal, 2, ',', '.');
        $entidadeNome = $estabelecimento['nome'] ?? 'Fornecedor Desconhecido';

        $dataFormatada = 'Data n/d';
        if (!empty($financeiro['data_emissao'])) {
            try {
                $data = new \DateTime($financeiro['data_emissao']);
                $dataFormatada = $data->format('d/m/Y');
            } catch (\Exception $e) {
                // mantém 'Data n/d'
            }
        }

        $msg = "✅ *Leitura Concluída!*\n\n";
        $msg .= "🏢 *{$entidadeNome}*\n";
        $msg .= "💰 *R$ {$valorFormatado}*\n";
        $msg .= "📅 {$dataFormatada}\n";

        // Alerta de retenção também no fallback (importante para tesoureiro)
        $impostosRetidos = $financeiro['impostos_retidos'] ?? 0;
        if ($impostosRetidos > 0) {
            $valorLiquido = $valorTotal - $impostosRetidos;
            $impostosFmt = number_format($impostosRetidos, 2, ',', '.');
            $liquidoFmt = number_format($valorLiquido, 2, ',', '.');
            $msg .= "\n⚠️ *RETENÇÃO DE IMPOSTOS*\n";
            $msg .= "Impostos retidos: R$ {$impostosFmt}\n";
            $msg .= "Valor LÍQUIDO a pagar: *R$ {$liquidoFmt}*\n";
        }

        $msg .= "\n📝 O documento foi processado e está disponível para revisão no painel.";

        return $msg;
    }

    /**
     * Envia resposta via WhatsApp usando as credenciais corretas do tenant.
     */
    private function sendWhatsAppReply(string $message): void
    {
        try {
            [$phoneNumberId, $token] = $this->resolveWhatsAppCredentials();

            if (!$phoneNumberId || !$token) {
                Log::error('Configuração WhatsApp não encontrada para envio de resposta', [
                    'tenant_id' => $this->tenantId,
                ]);
                return;
            }

            $response = Http::withToken($token)
                ->timeout(10)
                ->retry(2, 200, function ($exception) {
                    if ($exception instanceof \Illuminate\Http\Client\RequestException
                        && isset($exception->response)) {
                        return in_array($exception->response->status(), [429, 500, 502, 503, 504], true);
                    }
                    return false;
                }, throw: false)
                ->post("https://graph.facebook.com/v21.0/{$phoneNumberId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'to' => $this->userPhone,
                    'type' => 'text',
                    'text' => ['body' => $message],
                ]);

            if ($response && $response->successful()) {
                Log::info('Resposta enviada via WhatsApp', [
                    'tenant_id' => $this->tenantId,
                    'to' => $this->userPhone,
                ]);
            } else {
                Log::error('Erro ao enviar resposta WhatsApp', [
                    'status' => $response?->status(),
                    'error' => $response?->json('error.message'),
                ]);
            }
        } catch (Throwable $e) {
            Log::error('Erro ao enviar resposta WhatsApp: ' . $e->getMessage());
        }
    }
}
