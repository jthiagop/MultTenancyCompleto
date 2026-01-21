<?php

namespace App\Jobs;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Company;
use App\Models\Integracao;
use App\Models\WhatsappAuthRequest;
use App\Models\DomusDocumento;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Stancl\Tenancy\Facades\Tenancy;

class ProcessWhatsAppWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3; // Tentar até 3 vezes em caso de falha
    public $backoff = [60, 300]; // Esperar 1 min e 5 min entre tentativas

    protected $tenantId;
    protected $payload;

    /**
     * Create a new job instance.
     */
    public function __construct(string $tenantId, array $payload)
    {
        $this->tenantId = $tenantId;
        $this->payload = $payload;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $tenant = Tenant::find($this->tenantId);

        if (!$tenant) {
            Log::error("Tenant não encontrado: {$this->tenantId}");
            return;
        }

        // Inicializar tenancy dentro do Job
        $tenant->run(function () {
            try {
                Log::info("Processando webhook WhatsApp para tenant: {$this->tenantId}");

                // Processar cada entrada
                foreach ($this->payload['entry'] as $entry) {
                    if (!isset($entry['changes']) || !is_array($entry['changes'])) {
                        continue;
                    }

                    foreach ($entry['changes'] as $change) {
                        if (!isset($change['value']) || !isset($change['field'])) {
                            continue;
                        }

                        $value = $change['value'];

                        // Processar mensagens recebidas
                        if (isset($value['messages']) && is_array($value['messages'])) {
                            foreach ($value['messages'] as $message) {
                                $this->processIncomingMessage($message, $value);
                            }
                        }

                        // Processar status de mensagens enviadas
                        if (isset($value['statuses']) && is_array($value['statuses'])) {
                            foreach ($value['statuses'] as $status) {
                                $this->processMessageStatus($status);
                            }
                        }
                    }
                }

                Log::info("Webhook processado com sucesso para tenant: {$this->tenantId}");
            } catch (\Exception $e) {
                Log::error("Erro ao processar webhook no Job: " . $e->getMessage(), [
                    'tenant_id' => $this->tenantId,
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e; // Re-throw para fazer retry
            }
        });
    }

    /**
     * Processar mensagem recebida com deduplicação
     */
    private function processIncomingMessage($message, $value)
    {
        $wamid = $message['id']; // WhatsApp Message ID (único)
        $from = $message['from'];
        $timestamp = $message['timestamp'];
        $type = $message['type'];

        Log::info("Processando mensagem wamid: {$wamid}, tipo: {$type}, de: {$from}");

        // Verificar deduplicação: se wamid já foi processado, pular
        if ($this->isMessageProcessed($wamid)) {
            Log::info("Mensagem wamid {$wamid} já foi processada. Pulando deduplicação.");
            return;
        }

        // Marcar como processada antes de processar (para evitar race condition)
        $this->markMessageAsProcessed($wamid);

        // Processar diferentes tipos de mensagem
        switch ($type) {
            case 'text':
                $text = $message['text']['body'];
                $this->handleTextMessage($from, $text, $wamid);
                break;

            case 'image':
            case 'document':
            case 'audio':
            case 'video':
                $this->handleMediaMessage($from, $message, $type);
                break;

            default:
                Log::info("Tipo de mensagem não suportado: {$type}");
        }
    }

    /**
     * Processar mensagem de texto
     */
    private function handleTextMessage($from, $text, $messageId)
    {
        $text = trim($text);
        $texto = strtolower($text);
        $textLower = mb_strtolower($text);

        // 1. Filtro PHP: Agradecimentos (Responde sem gastar IA, hardcoded no PHP)
        $agradecimentos = ['obrigado', 'obg', 'valeu', 'amém', 'deus abençoe', 'obrigada', 'valeu mesmo', 'amém irmão', 'deus te abençoe'];
        if (in_array($texto, $agradecimentos)) {
            $this->sendTextMessage($from, "Por nada! 🙏 Conte comigo.");
            return; // NÃO CHAMA O GPT
        }

        // 2. Filtro PHP: Ajuda (Comando fixo)
        if ($texto === 'ajuda' || $texto === 'menu' || $texto === 'help' || $texto === 'comandos') {
            $msg = "Olá! Eu sou o Domus IA. ⛪\n\n" .
                   "📷 *Envie uma foto* de um cupom ou boleto para eu processar.\n" .
                   "📄 *Envie um PDF* para eu ler.\n\n" .
                   "No momento, não consigo conversar, apenas processar documentos.";
            $this->sendTextMessage($from, $msg);
            return; // NÃO CHAMA O GPT
        }

        // 3. DETECTOR DE CURIOSIDADE: "QUEM É VOCÊ?"
        // Detecta variações: "quem é vc", "quem eh voce", "quem sois vos", "qual seu nome"
        if (preg_match('/(quem|qual).+(voce|vc|tu|nome|sois)/i', $textLower) || $textLower === 'quem é você?') {
            $mensagemEngracada = "Paz e Bem! 🙏\n\n" .
                "Eu sou o **Fratello Domus**, o irmão caçula (e digital) da nossa congregação! 🤖⛪\n\n" .
                "Minha vocação é simples: enquanto vocês cuidam da salvação das almas, eu cuido da salvação do **Caixa**! 💸\n\n" .
                "Minha penitência... digo, minha missão é ajudar frades e religiosos a lançar as contas sem dor de cabeça.\n\n" .
                "Fui ordenado — ou melhor, programado — pelo mestre **José Thiago** no 'Mosteiro dos Códigos' para garantir que nenhum centavo se perca.\n\n" .
                "Pode mandar seus recibos que eu processo tudo com *santo* silêncio! 🤐📜";

            $this->sendTextMessage($from, $mensagemEngracada);

            // Marca como lida e encerra para não processar como documento
            $this->markMessageAsRead($messageId);
            return;
        }

        // Verificar se a mensagem contém UUID (código de vinculação)
        $uuidPattern = '/[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}/i';

        if (preg_match($uuidPattern, $text, $matches)) {
            $uuid = $matches[0];
            Log::info("UUID detectado na mensagem: {$uuid} de {$from}");

            // Buscar solicitação no banco central
            $authRequest = WhatsappAuthRequest::where('verification_code', $uuid)->first();

            Log::info("🔍 Busca por código de vinculação", [
                'verification_code' => substr($uuid, 0, 20) . '...',
                'encontrado' => $authRequest ? 'sim' : 'não',
                'auth_request_id' => $authRequest->id ?? null,
                'status' => $authRequest->status ?? null,
                'wa_id' => $authRequest->wa_id ?? null,
                'tenant_id' => $authRequest->tenant_id ?? null,
            ]);

            if ($authRequest) {
                if ($authRequest->isExpired()) {
                    Log::warning("⏰ Código de vinculação expirado (Job)", [
                        'auth_request_id' => $authRequest->id,
                        'verification_code' => substr($uuid, 0, 20) . '...',
                        'from' => $from,
                        'status' => $authRequest->status,
                        'wa_id' => $authRequest->wa_id,
                        'updated_at' => $authRequest->updated_at->toDateTimeString(),
                        'age_minutes' => now()->diffInMinutes($authRequest->updated_at),
                    ]);
                    $this->sendTextMessage($from, "❌ Este código de vinculação expirou. Por favor, gere um novo código no sistema Dominus. Os códigos são válidos por 10 minutos.");

                    // NÃO DELETAR: Manter registro para auditoria
                    // O comando whatsapp:clean-expired-codes irá limpar códigos expirados não vinculados
                    Log::info("⚠️  Código expirado - mantido para auditoria (será limpo pelo comando scheduler)");
                    return;
                }

                Log::info("Vinculando usuário {$authRequest->user_id} do tenant {$authRequest->tenant_id} ao número {$from}");

                try {
                    // Verificar se estamos no tenant correto
                    if (tenancy()->tenant->id !== $authRequest->tenant_id) {
                        Log::warning("Tenant mismatch. Esperado: {$authRequest->tenant_id}, Atual: " . tenancy()->tenant->id);
                        return;
                    }

                    Log::info("👤 Buscando usuário para vinculação", [
                        'user_id' => $authRequest->user_id,
                        'tenant_id' => $authRequest->tenant_id,
                        'from' => $from,
                    ]);

                    $user = User::find($authRequest->user_id);
                    if ($user) {
                        Log::info("✅ Usuário encontrado, iniciando vinculação", [
                            'user_id' => $user->id,
                            'user_name' => $user->name,
                        ]);

                        $user->whatsapp_number = $from;
                        $user->save();

                        Log::info("✅ Número WhatsApp salvo no usuário", [
                            'user_id' => $user->id,
                            'whatsapp_number' => $from,
                        ]);

                        // Atualizar whatsapp_auth_requests (não deletar!)
                        // Isso permite resolver tenant pelo wa_id em mensagens futuras
                        $statusAntigo = $authRequest->status;
                        $waIdAntigo = $authRequest->wa_id;

                        Log::info("📝 Preparando atualização do registro whatsapp_auth_requests", [
                            'auth_request_id' => $authRequest->id,
                            'status_antes' => $statusAntigo,
                            'wa_id_antes' => $waIdAntigo,
                            'wa_id_novo' => $from,
                        ]);

                        $authRequest->wa_id = $from;
                        $authRequest->status = 'active';

                        // Garantir que phone_number_id está salvo (caso não esteja)
                        if (!$authRequest->phone_number_id) {
                            $phoneNumberId = $this->extractPhoneNumberId();
                            if ($phoneNumberId) {
                                $authRequest->phone_number_id = $phoneNumberId;
                            }
                        }

                        Log::info("💾 Salvando registro whatsapp_auth_requests", [
                            'auth_request_id' => $authRequest->id,
                            'status' => 'active',
                            'wa_id' => $from,
                            'phone_number_id' => $authRequest->phone_number_id,
                        ]);

                        $authRequest->save();

                        Log::info("✅ Registro whatsapp_auth_requests atualizado (vinculação concluída)", [
                            'auth_request_id' => $authRequest->id,
                            'tenant_id' => $authRequest->tenant_id,
                            'user_id' => $authRequest->user_id,
                            'wa_id_antes' => $waIdAntigo,
                            'wa_id_depois' => $from,
                            'status_antes' => $statusAntigo,
                            'status_depois' => 'active',
                            'phone_number_id' => $authRequest->phone_number_id,
                            'updated_at' => $authRequest->updated_at->toDateTimeString(),
                        ]);

                        // Salvar ou atualizar integração WhatsApp
                        $this->saveOrUpdateIntegracao($user->id, $from);

                        // Enviar mensagem de boas-vindas
                        $welcomeMessage = "Paz e Bem! 🙏\n\n" .
                            "Código validado com sucesso! Agora seu WhatsApp está oficialmente conectado ao Sistema Dominus.\n\n" .
                            "Eu sou o Fratello Domus, seu assistente digital. Minha missão é ajudar você a organizar o financeiro da paróquia/comunidade sem dor de cabeça. 🤖⛪\n\n" .
                            "Como eu funciono:\n" .
                            "📷 Basta me enviar uma foto de qualquer nota fiscal, recibo, cupom ou boleto.\n" .
                            "🧠 Eu leio os dados, organizo e deixo tudo pronto lá no seu painel como rascunho.\n\n" .
                            "Que tal fazermos um teste? Pode me enviar sua primeira foto agora! 👇";
                        
                        $this->sendTextMessage($from, $welcomeMessage);

                        // Marcar mensagem como lida
                        $this->markMessageAsRead($messageId);
                    } else {
                        Log::error("❌ Usuário não encontrado (Job) - REGISTRO MANTIDO PARA INVESTIGAÇÃO", [
                            'user_id_procurado' => $authRequest->user_id,
                            'tenant_id' => $authRequest->tenant_id,
                            'auth_request_id' => $authRequest->id,
                            'status' => $authRequest->status,
                            'wa_id' => $authRequest->wa_id,
                            'verification_code' => substr($uuid, 0, 20) . '...',
                        ]);
                        $this->sendTextMessage($from, "❌ Erro ao vincular seu WhatsApp. Entre em contato com o suporte do Dominus.");

                        // NÃO DELETAR: Manter registro para investigação
                        // Isso pode indicar problema no banco de dados do tenant
                        Log::info("⚠️  Usuário não encontrado - registro mantido para investigação pelo suporte");
                    }
                } catch (\Exception $e) {
                    Log::error("Erro ao vincular usuário: " . $e->getMessage());
                    $this->sendTextMessage($from, "❌ Erro ao vincular seu WhatsApp. Entre em contato com o suporte do Dominus.");
                }
            }
        } else {
            // Mensagem normal de usuário já vinculado (não é UUID, não é comando conhecido)
            Log::info("Mensagem de texto recebida de {$from}: {$text}");

            // 3. Se não for comando e não for UUID: Enviar mensagem genérica
            $this->sendTextMessage($from, "Desculpe, eu sou treinado apenas para ler documentos fiscais. Por favor, envie uma foto ou PDF! 📷");
        }
    }

    /**
     * Processar mensagem de mídia
     */
    private function handleMediaMessage($from, $message, $type)
    {
        $messageId = $message['id'];
        $mediaId = null;
        $mimeType = null;
        $caption = null;
        $filename = null;

        // Extrair informações da mídia baseado no tipo
        switch ($type) {
            case 'image':
                $mediaId = $message['image']['id'] ?? null;
                $mimeType = $message['image']['mime_type'] ?? null;
                $caption = $message['image']['caption'] ?? null;
                break;
            case 'document':
                $mediaId = $message['document']['id'] ?? null;
                $mimeType = $message['document']['mime_type'] ?? null;
                $caption = $message['document']['caption'] ?? null;
                $filename = $message['document']['filename'] ?? null;
                break;
            case 'audio':
                $mediaId = $message['audio']['id'] ?? null;
                $mimeType = $message['audio']['mime_type'] ?? null;
                break;
            case 'video':
                $mediaId = $message['video']['id'] ?? null;
                $mimeType = $message['video']['mime_type'] ?? null;
                $caption = $message['video']['caption'] ?? null;
                break;
        }

        if (!$mediaId) {
            Log::warning("Media ID não encontrado para mensagem {$messageId}");
            return;
        }

        Log::info("Mensagem de mídia recebida: tipo={$type}, media_id={$mediaId}, de={$from}");

        // Buscar access_token do whatsapp_auth_requests
        $phoneNumberId = $this->extractPhoneNumberId();
        $authRequest = WhatsappAuthRequest::byPhoneNumberId($phoneNumberId)->active()->first();

        $accessToken = $authRequest->access_token ?? config('services.meta.token');

        // Baixar mídia
        $this->downloadAndProcessMedia($mediaId, $type, $mimeType, $caption, $filename, $from, $accessToken);
    }

    /**
     * Extrair phone_number_id do payload
     * Retorna phone_number_id do payload ou do config como fallback
     *
     * @return string
     * @throws \RuntimeException Se não encontrar phone_number_id
     */
    private function extractPhoneNumberId()
    {
        foreach ($this->payload['entry'] as $entry) {
            if (isset($entry['changes']) && is_array($entry['changes'])) {
                foreach ($entry['changes'] as $change) {
                    if (isset($change['value']['metadata']['phone_number_id'])) {
                        return $change['value']['metadata']['phone_number_id'];
                    }
                }
            }
        }

        // Fallback: usar phone_number_id do config
        $phoneNumberId = config('services.meta.phone_id');
        if ($phoneNumberId) {
            Log::warning("phone_number_id não encontrado no payload, usando fallback do config: {$phoneNumberId}");
            return $phoneNumberId;
        }

        // Se não encontrou nem no payload nem no config, lançar exceção
        throw new \RuntimeException('phone_number_id não encontrado no payload do webhook e não configurado em services.meta.phone_id');
    }

    /**
     * Baixar e processar mídia
     */
    private function downloadAndProcessMedia($mediaId, $type, $mimeType, $caption, $filename, $from, $accessToken)
    {
        try {
            // 0. Validar tipo de arquivo suportado
            $allowedMimeTypes = [
                'image/jpeg',
                'image/jpg',
                'image/png',
                'image/gif',
                'image/webp',
                'application/pdf',
            ];

            $allowedTypes = ['image', 'document'];

            // Verificar se o tipo é suportado
            if (!in_array($type, $allowedTypes)) {
                Log::warning("Tipo de mídia não suportado: {$type}", [
                    'from' => $from,
                    'media_id' => $mediaId,
                    'mime_type' => $mimeType,
                ]);
                $this->sendTextMessage($from, "⚠️ Eita, no momento, só estou aceitando arquivos no formato PDF e imagem.");
                return;
            }

            // Verificar se o mimeType é suportado (se disponível)
            if ($mimeType && !in_array($mimeType, $allowedMimeTypes)) {
                Log::warning("MIME type não suportado: {$mimeType}", [
                    'from' => $from,
                    'media_id' => $mediaId,
                    'type' => $type,
                ]);
                $this->sendTextMessage($from, "⚠️ Eita, no momento, só estou aceitando arquivos no formato PDF e imagem.");
                return;
            }

            // 1. Obter URL temporária da mídia
            $mediaUrl = $this->getMediaUrl($mediaId, $accessToken);

            if (!$mediaUrl) {
                Log::error("Não foi possível obter URL da mídia {$mediaId}");
                return;
            }

            // 2. Baixar mídia
            $response = Http::withToken($accessToken)->get($mediaUrl);

            if (!$response->successful()) {
                Log::error("Erro ao baixar mídia: " . $response->status());
                return;
            }

            // 3. Determinar extensão do arquivo
            $extension = $this->getFileExtension($mimeType, $type);

            // Usar nome original do arquivo se disponível, senão gerar um nome único
            $originalFilename = $filename ?: 'documento_' . time();
            $filenameWithoutExt = pathinfo($originalFilename, PATHINFO_FILENAME);
            $storageFilename = \Illuminate\Support\Str::slug($filenameWithoutExt) . '_' . time() . '_' . substr($mediaId, 0, 10) . '.' . $extension;

            // 4. Salvar no storage do tenant usando o mesmo diretório do upload via PC
            // O stancl/tenancy automaticamente isola o storage por tenant
            $directory = 'domus_documentos';
            $fullPath = "{$directory}/{$storageFilename}";

            // Garantir que o diretório existe
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
                Log::info("Diretório de storage criado: {$directory}");
            }

            // Salvar arquivo e obter o caminho correto
            $saved = Storage::disk('public')->put($fullPath, $response->body());

            // Se put() retornar true, usar o caminho que passamos
            // Se retornar string (caminho), usar o retornado
            $path = is_string($saved) ? $saved : $fullPath;

            Log::info("Mídia salva: {$path}", [
                'storage_path' => $path,
                'saved_return' => $saved,
                'full_path' => $fullPath,
                'tenant_id' => $this->tenantId
            ]);

            // 5. Criar registro no banco de dados (domus_documentos) - UNIFICAÇÃO COM UPLOAD VIA PC
            // Buscar informações do usuário e empresa ANTES de entrar no contexto do tenant
            // (WhatsappAuthRequest está no banco central)
            $userId = null;
            $userName = null;
            $companyId = null;

            // Buscar user_id através do WhatsappAuthRequest usando wa_id (banco central)
            $phoneNumberId = $this->extractPhoneNumberId();
            $authRequest = WhatsappAuthRequest::byPhoneNumberId($phoneNumberId)
                ->where('wa_id', $from)
                ->where('status', 'active')
                ->first();

            if ($authRequest && $authRequest->user_id) {
                $userId = $authRequest->user_id;
            }

            // Entrar no contexto do tenant para buscar dados do usuário e empresa
            $tenant = Tenant::find($this->tenantId);
            if ($tenant) {
                $tenant->run(function () use ($userId, &$userName, &$companyId) {
                    // Buscar usuário dentro do contexto do tenant
                    if ($userId) {
                        $user = User::find($userId);
                        if ($user) {
                            $userName = $user->name;
                            $companyId = $user->company_id;
                        }
                    }

                    // Se não encontrou company_id, pegar a primeira empresa
                    if (!$companyId) {
                        $company = Company::first();
                        if ($company) {
                            $companyId = $company->id;
                        }
                    }

                    // Se ainda não tem user_name, usar um padrão
                    if (!$userName) {
                        $userName = 'WhatsApp Usuário';
                    }
                });
            }

            // Preparar conteúdo base64 para salvar (limitado a 65KB para evitar truncamento)
            $fileContent = $response->body();
            $base64Content = base64_encode($fileContent);
            $base64Length = strlen($base64Content);

            // Se o base64 for muito grande (> 60KB), não salvar completo (evitar truncamento no banco)
            if ($base64Length > 60000) {
                Log::warning("Base64 muito grande ({$base64Length} bytes), truncando para evitar erro no banco");
                $base64Content = null; // Não salvar base64 para arquivos grandes
            }

            // Criar registro em domus_documentos dentro do contexto do tenant
            $documento = null;
            $fileSize = strlen($fileContent); // Tamanho do arquivo em bytes

            if ($tenant) {
                $tenant->run(function () use ($path, $storageFilename, $mimeType, $extension, $base64Content, $fileSize, $companyId, $userId, $userName, $from, &$documento) {
                    $documento = DomusDocumento::create([
                        'nome_arquivo' => $storageFilename,
                        'caminho_arquivo' => $path,
                        'tipo_arquivo' => $extension,
                        'mime_type' => $mimeType,
                        'tamanho_arquivo' => $fileSize,
                        'base64_content' => $base64Content, // Pode ser null para arquivos grandes
                        'status' => 'pendente', // Status inicial: pendente (será processado pela IA)
                        'company_id' => $companyId,
                        'user_id' => $userId,
                        'user_name' => $userName,
                        'canal_origem' => 'whatsapp',
                        'remetente' => $from,
                    ]);

                    Log::info("Registro criado em domus_documentos", [
                        'documento_id' => $documento->id,
                        'nome_arquivo' => $documento->nome_arquivo,
                        'caminho_arquivo' => $documento->caminho_arquivo,
                        'canal_origem' => $documento->canal_origem,
                        'user_id' => $documento->user_id,
                        'tamanho_arquivo' => $documento->tamanho_arquivo,
                    ]);
                });
            }

            // 6. GATILHO PARA A IA: Despachar Job que vai analisar o documento com GPT-4o
            \App\Jobs\AnalyzeDocumentWithAi::dispatch($this->tenantId, $path, $from);

            // 7. Enviar mensagem informando que está processando (em vez de "sucesso final")
            $this->sendTextMessage($from, "✅ Arquivo recebido! A Inteligência Artificial está analisando seu documento. Aguarde um instante...");
        } catch (\Exception $e) {
            Log::error("Erro ao processar mídia: " . $e->getMessage());
            $this->sendTextMessage($from, "❌ Erro ao processar o documento. Tente novamente.");
        }
    }

    /**
     * Obter URL temporária da mídia
     */
    private function getMediaUrl($mediaId, $accessToken)
    {
        $response = Http::withToken($accessToken)
            ->get("https://graph.facebook.com/v21.0/{$mediaId}");

        if ($response->successful()) {
            return $response->json('url');
        }

        return null;
    }

    /**
     * Determinar extensão do arquivo
     */
    private function getFileExtension($mimeType, $type)
    {
        if ($mimeType) {
            $mimeMap = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/gif' => 'gif',
                'application/pdf' => 'pdf',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
                'application/msword' => 'doc',
            ];

            return $mimeMap[$mimeType] ?? 'bin';
        }

        // Fallback baseado no tipo
        $typeMap = [
            'image' => 'jpg',
            'document' => 'pdf',
            'audio' => 'ogg',
            'video' => 'mp4',
        ];

        return $typeMap[$type] ?? 'bin';
    }

    /**
     * Enviar mensagem de texto
     */
    private function sendTextMessage($to, $text)
    {
        try {
            $phoneNumberId = $this->extractPhoneNumberId();
        } catch (\RuntimeException $e) {
            Log::error("Não foi possível enviar mensagem: " . $e->getMessage());
            return;
        }

        $authRequest = WhatsappAuthRequest::byPhoneNumberId($phoneNumberId)->active()->first();
        $accessToken = $authRequest->access_token ?? config('services.meta.token');

        if (!$accessToken) {
            Log::error("Não foi possível enviar mensagem: access_token não encontrado");
            return;
        }

        try {
            $response = Http::withToken($accessToken)
                ->post("https://graph.facebook.com/v21.0/{$phoneNumberId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'to' => $to,
                    'type' => 'text',
                    'text' => ['body' => $text]
                ]);

            if ($response->successful()) {
                Log::info("Mensagem enviada para {$to}");
            } else {
                Log::error("Erro ao enviar mensagem: " . $response->body());
            }
        } catch (\Exception $e) {
            Log::error("Erro ao enviar mensagem: " . $e->getMessage());
        }
    }

    /**
     * Marcar mensagem como lida
     */
    private function markMessageAsRead($messageId)
    {
        try {
            $phoneNumberId = $this->extractPhoneNumberId();
        } catch (\RuntimeException $e) {
            Log::warning("Não foi possível marcar mensagem como lida: " . $e->getMessage());
            return;
        }

        $authRequest = WhatsappAuthRequest::byPhoneNumberId($phoneNumberId)->active()->first();
        $accessToken = $authRequest->access_token ?? config('services.meta.token');

        if (!$accessToken) {
            Log::warning("Não foi possível marcar mensagem como lida: access_token não encontrado");
            return;
        }

        try {
            Http::withToken($accessToken)
                ->post("https://graph.facebook.com/v21.0/{$phoneNumberId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'status' => 'read',
                    'message_id' => $messageId
                ]);
        } catch (\Exception $e) {
            Log::error("Erro ao marcar mensagem como lida: " . $e->getMessage());
        }
    }

    /**
     * Processar status de mensagem
     */
    private function processMessageStatus($status)
    {
        $messageId = $status['id'];
        $statusType = $status['status']; // sent, delivered, read, failed

        Log::info("Status da mensagem {$messageId}: {$statusType}");
        // TODO: Atualizar status no banco de dados se necessário
    }

    /**
     * Verificar se mensagem já foi processada
     * IMPORTANTE: Usa conexão central pois a tabela está no banco central
     * (não no banco do tenant, mesmo estando dentro de $tenant->run())
     */
    private function isMessageProcessed($wamid)
    {
        // Forçar conexão central: a tabela whatsapp_messages_processed está no banco central
        // Dentro de $tenant->run(), o contexto muda para o banco do tenant, mas essa tabela é compartilhada
        return DB::connection('mysql')
            ->table('whatsapp_messages_processed')
            ->where('wamid', $wamid)
            ->exists();
    }

    /**
     * Marcar mensagem como processada
     * IMPORTANTE: Usa conexão central pois a tabela está no banco central
     * (não no banco do tenant, mesmo estando dentro de $tenant->run())
     */
    private function markMessageAsProcessed($wamid)
    {
        // Forçar conexão central: a tabela whatsapp_messages_processed está no banco central
        // Dentro de $tenant->run(), o contexto muda para o banco do tenant, mas essa tabela é compartilhada
        DB::connection('mysql')
            ->table('whatsapp_messages_processed')
            ->insertOrIgnore([
                'wamid' => $wamid,
                'processed_at' => now(),
            ]);
    }

    /**
     * Salvar ou atualizar integração WhatsApp
     */
    private function saveOrUpdateIntegracao($userId, $remetente)
    {
        try {
            $tenantId = $this->tenantId;

            // IMPORTANTE: Atualizar whatsapp_auth_requests para garantir status 'active'
            // Este código foi adicionado para prevenir deleção indevida de registros
            $authRequest = WhatsappAuthRequest::where('tenant_id', $tenantId)
                ->where('user_id', $userId)
                ->first();

            if ($authRequest) {
                $authRequest->status = 'active';
                // Garantir que phone_number_id está salvo
                if (!$authRequest->phone_number_id) {
                    try {
                        $phoneNumberId = $this->extractPhoneNumberId();
                        $authRequest->phone_number_id = $phoneNumberId;
                    } catch (\RuntimeException $e) {
                        // Se não conseguir extrair, usar do config
                        $authRequest->phone_number_id = config('services.meta.phone_id');
                    }
                }
                // Garantir que access_token está salvo
                if (!$authRequest->access_token) {
                    $authRequest->access_token = config('services.meta.token');
                }
                $authRequest->save();
                Log::info("✅ whatsapp_auth_requests atualizado para status 'active' (Job)", [
                    'auth_request_id' => $authRequest->id,
                    'user_id' => $userId,
                    'status' => 'active',
                ]);
            } else {
                Log::warning("⚠️ whatsapp_auth_requests não encontrado ao salvar integração (Job)", [
                    'tenant_id' => $tenantId,
                    'user_id' => $userId,
                ]);
            }

            // Extrair destinatário
            try {
                $phoneNumberId = $this->extractPhoneNumberId();
                $destinatario = $phoneNumberId;
            } catch (\RuntimeException $e) {
                // Se não conseguir extrair phone_number_id, usar fallback do config
                $destinatario = config('services.meta.whatsapp_number', '558183797797');
                Log::warning("Não foi possível extrair phone_number_id ao salvar integração, usando fallback: " . $e->getMessage());
            }

            $integracao = Integracao::where('user_id', $userId)
                ->where('tipo', 'whatsapp')
                ->first();

            if ($integracao) {
                $integracao->status = 'configurado';
                $integracao->remetente = $remetente;
                $integracao->destinatario = $destinatario;
                $integracao->save();
            } else {
                Integracao::create([
                    'tipo' => 'whatsapp',
                    'status' => 'configurado',
                    'remetente' => $remetente,
                    'destinatario' => $destinatario,
                    'user_id' => $userId,
                ]);
            }

            Log::info("Integração WhatsApp salva/atualizada para usuário {$userId}");
        } catch (\Exception $e) {
            Log::error("Erro ao salvar integração: " . $e->getMessage());
        }
    }
}
