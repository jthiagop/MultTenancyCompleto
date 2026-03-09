<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\WhatsappAuthRequest;
use App\Models\User;
use App\Models\Integracao;
use Stancl\Tenancy\Facades\Tenancy;

class WhatsAppIntegrationController extends Controller
{
    // Configurações da Meta WhatsApp Business API
    private $metaApiUrl = 'https://graph.facebook.com/v21.0';
    private $phoneNumberId; // ID do número de telefone na Meta
    private $accessToken; // Token de acesso permanente (META_WHATSAPP_TOKEN)
    private $webhookVerifyToken; // Token para verificação do webhook (META_VERIFY_TOKEN)
    private $systemNumber; // Número oficial do WhatsApp no formato internacional
    private $appSecret; // App Secret para validação de assinatura

    public function __construct()
    {
        // Carregar configurações do config/services.php (suporta config:cache)
        $this->phoneNumberId = config('services.meta.phone_id');
        $this->accessToken = config('services.meta.token');
        $this->webhookVerifyToken = config('services.meta.verify_token');
        $this->systemNumber = config('services.meta.whatsapp_number');
        $this->appSecret = config('services.meta.app_secret');
    }

    /**
     * Gerar QR Code para autenticação/vinculação
     * Gera um link wa.me com UUID e exibe o QR Code desse link
     */
    public function getQRCode(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'error' => 'Usuário não autenticado'
                ], 401);
            }

            $tenantId = tenancy()->tenant->id;

            // 1. Gerar UUID único para vinculação
            $verificationCode = Str::uuid()->toString();

            // 2. Salvar na tabela central (banco central)
            // Buscar phone_number_id do .env (META_PHONE_ID)
            $phoneNumberId = $this->phoneNumberId;
            $accessToken = $this->accessToken;

            // Verificar se já existe registro para este tenant/user
            $authRequest = WhatsappAuthRequest::where('tenant_id', $tenantId)
                ->where('user_id', $user->id)
                ->first();

            // Capturar company_id ativo na sessão do usuário no momento da vinculação
            $activeCompanyId = session('active_company_id') ?? $user->company_id;

            if ($authRequest) {
                // Atualizar registro existente
                $authRequest->verification_code = $verificationCode;
                $authRequest->phone_number_id = $phoneNumberId;
                $authRequest->access_token = $accessToken;
                $authRequest->company_id = $activeCompanyId;
                $authRequest->status = 'pending';
                $authRequest->save();
            } else {
                // Criar novo registro
                $authRequest = new WhatsappAuthRequest();
                $authRequest->verification_code = $verificationCode;
                $authRequest->tenant_id = $tenantId;
                $authRequest->user_id = $user->id;
                $authRequest->company_id = $activeCompanyId;
                $authRequest->phone_number_id = $phoneNumberId;
                $authRequest->access_token = $accessToken;
                $authRequest->status = 'pending';
                $authRequest->save();
            }

            // 3. Criar ou atualizar integração pendente
            $this->createOrUpdatePendenteIntegracao($user->id);

            // 4. Criar o Link Deep Link (wa.me) com mensagem formatada
            // Mensagem: "Olá Dominus, meu código de vinculação é: [UUID]"
            $mensagem = "Olá Dominus, meu código de vinculação é: {$verificationCode}";
            $linkWhatsApp = "https://wa.me/{$this->systemNumber}?text=" . urlencode($mensagem);

            // 5. Gerar o QR Code do link
            $qrCodeSvg = QrCode::size(250)->generate($linkWhatsApp);
            $qrCodeBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrCodeSvg);

            return response()->json([
                'success' => true,
                'base64' => $qrCodeBase64,
                'code' => $verificationCode,
                'link' => $linkWhatsApp,
                'message' => 'Escaneie o QR Code ou clique no link para iniciar a conversa no WhatsApp'
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao gerar Auth QR Code: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verificar status da integração
     */
    public function checkStatus($code)
    {
        try {
            if (!$code) {
                return response()->json(['success' => false, 'status' => 'unknown']);
            }

            $authRequest = WhatsappAuthRequest::where('verification_code', $code)->first();

            if ($authRequest && $authRequest->status === 'active') {
                return response()->json([
                    'success' => true, 
                    'status' => 'active',
                    'message' => 'Integração realizada com sucesso!'
                ]);
            }
            
            if (!$authRequest) {
                 return response()->json(['success' => false, 'status' => 'not_found']);
            }
            
            if ($authRequest->isExpired()) {
                 return response()->json(['success' => false, 'status' => 'expired']);
            }

            return response()->json(['success' => true, 'status' => 'pending']);
        } catch (\Exception $e) {
            Log::error('Erro ao verificar status: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Webhook da Meta - Verificação e Recebimento de Mensagens
     * GET: Verificação de segurança (handshake)
     * POST: Processamento de mensagens (roteamento por phone_number_id)
     */
    public function webhook(Request $request)
    {
        // 1. Verificação do Webhook (GET request da Meta)
        if ($request->isMethod('get')) {
            return $this->verifyWebhook($request);
        }

        // 2. Processar mensagens recebidas (POST request)
        try {
            // Validar assinatura do webhook (opcional mas recomendado)
            // Em desenvolvimento, pode ser desabilitado se META_APP_SECRET não estiver configurado
            $skipValidation = config('services.meta.skip_signature_validation', false);

            if (!$skipValidation && !$this->validateWebhookSignature($request)) {
                Log::error('Assinatura do webhook inválida. Rejeitando requisição.');
                return response()->json(['success' => false, 'error' => 'Invalid signature'], 403);
            }

            $data = $request->all();
            Log::info('Meta Webhook POST recebido', [
                'payload_completo' => json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            ]);

            // Verificar estrutura da Meta: entry[0].changes[0].value.metadata.phone_number_id
            if (!isset($data['entry']) || !is_array($data['entry']) || empty($data['entry'])) {
                Log::warning('Estrutura de webhook inválida: entry não encontrado ou vazio', [
                    'payload_keys' => array_keys($data),
                    'payload_sample' => json_encode($data, JSON_PRETTY_PRINT)
                ]);
                return response()->json(['success' => false, 'error' => 'Invalid structure'], 400);
            }

            // Verificar se é apenas um webhook de status (sem mensagens para processar)
            // Webhooks de status (sent, delivered, read) não precisam processamento de tenant
            if ($this->isStatusOnlyWebhook($data)) {
                Log::debug('Webhook de status recebido (não requer processamento)', [
                    'tipo' => 'status_only'
                ]);
                return response()->json(['success' => true], 200);
            }

            // Verificar deduplicação: se mensagem já foi processada, retornar imediatamente
            // Isso evita processamento desnecessário antes mesmo de resolver o tenant
            $wamid = $this->extractWamidFromPayload($data);
            if ($wamid && $this->isMessageProcessed($wamid)) {
                Log::info("Mensagem wamid {$wamid} já foi processada. Retornando 200 OK (deduplicação).");
                return response()->json(['success' => true], 200);
            }

            // PRIORIDADE 1: Tentar resolver tenant pelo verification_code (código de vinculação)
            // Isso permite processar mensagens de vinculação sem precisar do phone_number_id cadastrado
            $verificationCode = $this->extractVerificationCodeFromPayload($data);
            $tenant = null;

            if ($verificationCode) {
                Log::info("Verification code encontrado no payload: {$verificationCode}");
                $tenant = $this->resolveTenantByVerificationCode($verificationCode);

                if ($tenant) {
                    Log::info("Tenant resolvido pelo verification_code: {$tenant->id} ({$tenant->name})");
                } else {
                    Log::warning("Não foi possível resolver tenant pelo verification_code: {$verificationCode}");
                }
            }

            // PRIORIDADE 2: Se não encontrou pelo verification_code, tentar pelo wa_id (número do remetente)
            // Esta é a forma correta para mensagens normais (após vinculação) em SaaS multi-tenant
            if (!$tenant) {
                $waId = $this->extractWaIdFromPayload($data);

                if ($waId) {
                    Log::info("wa_id extraído do payload: {$waId}");
                    $tenant = $this->resolveTenantByWaId($waId);

                    if ($tenant) {
                        Log::info("Tenant resolvido pelo wa_id: {$tenant->id} ({$tenant->name})");
                    } else {
                        Log::warning("Não foi possível resolver tenant pelo wa_id: {$waId}");
                    }
                }
            }

            // PRIORIDADE 3: Se não encontrou pelo wa_id, tentar pelo phone_number_id (fallback)
            // Nota: Em SaaS multi-tenant, todos compartilham o mesmo phone_number_id, então esta resolução é menos confiável
            if (!$tenant) {
            // Extrair phone_number_id do primeiro entry
            $phoneNumberId = null;
            $extractionPath = null;
            foreach ($data['entry'] as $entryIndex => $entry) {
                if (isset($entry['changes']) && is_array($entry['changes'])) {
                    foreach ($entry['changes'] as $changeIndex => $change) {
                        // Tentar múltiplos caminhos possíveis para phone_number_id
                        $possiblePaths = [
                            'value.metadata.phone_number_id',
                            'value.phone_number_id',
                            'metadata.phone_number_id',
                            'phone_number_id'
                        ];

                        foreach ($possiblePaths as $path) {
                            $pathParts = explode('.', $path);
                            $value = $change;
                            $found = true;
                            
                            foreach ($pathParts as $part) {
                                if (isset($value[$part])) {
                                    $value = $value[$part];
                                } else {
                                    $found = false;
                                    break;
                                }
                            }
                            
                            if ($found && $value) {
                                $phoneNumberId = $value;
                                $extractionPath = "entry[{$entryIndex}].changes[{$changeIndex}].{$path}";
                                break 3; // Sair dos três loops
                            }
                        }
                    }
                }
            }

                if ($phoneNumberId) {
            Log::info("phone_number_id extraído: {$phoneNumberId}", [
                'caminho_extracao' => $extractionPath,
                'tipo_valor' => gettype($phoneNumberId),
                'valor_bruto' => $phoneNumberId
            ]);

            // Resolver tenant pelo phone_number_id
            $tenant = $this->resolveTenantByPhoneNumberId($phoneNumberId);

            if (!$tenant) {
                        Log::warning("Não foi possível resolver tenant para phone_number_id: {$phoneNumberId}");
                    }
                } else {
                    Log::warning('phone_number_id não encontrado no payload do webhook', [
                        'estrutura_entry' => isset($data['entry'][0]) ? json_encode($data['entry'][0], JSON_PRETTY_PRINT) : 'entry[0] não existe',
                        'estrutura_changes' => isset($data['entry'][0]['changes'][0]) ? json_encode($data['entry'][0]['changes'][0], JSON_PRETTY_PRINT) : 'changes[0] não existe',
                        'estrutura_value' => isset($data['entry'][0]['changes'][0]['value']) ? json_encode($data['entry'][0]['changes'][0]['value'], JSON_PRETTY_PRINT) : 'value não existe'
                    ]);
                }
            }

            // Se não encontrou tenant por nenhum método, retornar erro
            if (!$tenant) {
                Log::error("Não foi possível resolver tenant. Verification code: " . ($verificationCode ?? 'não encontrado'));
                return response()->json(['success' => false, 'error' => 'Tenant not found'], 404);
            }

            // Inicializar tenant programaticamente
            tenancy()->initialize($tenant);
            Log::info("Tenant inicializado: {$tenant->id} ({$tenant->name})");

            // Despachar Job assíncrono para processamento
            \App\Jobs\ProcessWhatsAppWebhook::dispatch($tenant->id, $data);

            // Retornar 200 OK imediatamente (resposta rápida)
            return response()->json(['success' => true], 200);

        } catch (\Exception $e) {
            Log::error('Erro no webhook Meta: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            // Finalizar tenancy se foi inicializada
            if (tenancy()->initialized) {
                tenancy()->end();
            }

            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Verificar webhook (handshake da Meta)
     * A Meta envia um GET com hub_verify_token e hub_challenge
     * Devemos retornar o hub_challenge em plain text se o token estiver correto
     */
    private function verifyWebhook(Request $request)
    {
        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        Log::info('Tentativa de verificação de webhook', [
            'mode' => $mode,
            'token_recebido' => $token,
            'token_esperado' => $this->webhookVerifyToken,
            'challenge' => $challenge
        ]);

        // Verificar se é uma requisição de verificação e se o token está correto
        if ($mode === 'subscribe' && $token === $this->webhookVerifyToken) {
            Log::info('Webhook verificado com sucesso. Retornando challenge: ' . $challenge);

            // Retornar o hub_challenge em plain text (sem JSON, sem HTML)
            return response($challenge, 200)
                ->header('Content-Type', 'text/plain');
        }

        Log::warning('Falha na verificação do webhook', [
            'mode' => $mode,
            'token_match' => $token === $this->webhookVerifyToken
        ]);

        return response('Forbidden', 403);
    }

    /**
     * Processar mensagem recebida
     */
    private function processIncomingMessage($message, $value)
    {
        $from = $message['from']; // Número do remetente
        $messageId = $message['id'];
        $timestamp = $message['timestamp'];
        $type = $message['type']; // text, image, document, audio, video, etc.

        Log::info("Mensagem recebida de {$from}, tipo: {$type}");

        // Processar diferentes tipos de mensagem
        switch ($type) {
            case 'text':
                $text = $message['text']['body'];
                $this->handleTextMessage($from, $text, $messageId);
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
     * Verifica se a mensagem contém um UUID (código de vinculação)
     * Aceita UUID puro ou mensagens formatadas como "Olá Dominus, meu código é: UUID"
     */
    private function handleTextMessage($from, $text, $messageId)
    {
        // Limpar espaços e quebras de linha
        $text = trim($text);

        // Verificar se a mensagem é um UUID válido ou contém um UUID
        // Aceita:
        // - UUID puro: 5682ec9d-3712-4823-aa35-27b4109426e8
        // - Mensagem formatada: "Olá Dominus, meu código é: 5682ec9d-3712-4823-aa35-27b4109426e8"
        // - Mensagem formatada: "meu código para cadastro é 5682ec9d-3712-4823-aa35-27b4109426e8"
        // UUIDs têm formato: xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx (36 caracteres)
        $uuidPattern = '/[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}/i';

        if (preg_match($uuidPattern, $text, $matches)) {
            $uuid = $matches[0];

            Log::info("UUID detectado na mensagem: {$uuid} de {$from}");

            // Buscar solicitação no banco central
            $authRequest = WhatsappAuthRequest::where('verification_code', $uuid)->first();

            Log::info("🔍 Busca por código de vinculação (Controller)", [
                'verification_code' => substr($uuid, 0, 20) . '...',
                'encontrado' => $authRequest ? 'sim' : 'não',
                'auth_request_id' => $authRequest->id ?? null,
                'status' => $authRequest->status ?? null,
                'wa_id' => $authRequest->wa_id ?? null,
                'tenant_id' => $authRequest->tenant_id ?? null,
            ]);

            if ($authRequest) {
                // Verificar se o código expirou
                if ($authRequest->isExpired()) {
                    Log::warning("⏰ Código de vinculação expirado (Controller)", [
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

                // Verificar se este número já está vinculado a outro usuário/tenant
                // Ignorar registros inativos (integração excluída) para permitir re-vinculação
                $existingBinding = WhatsappAuthRequest::where('wa_id', $from)
                    ->whereNotNull('wa_id')
                    ->where('id', '!=', $authRequest->id)
                    ->where('status', 'active')
                    ->first();

                if ($existingBinding) {
                    Log::warning("⚠️ Número já vinculado a outro usuário/tenant (Controller)", [
                        'from' => $from,
                        'existing_auth_id' => $existingBinding->id,
                        'existing_tenant_id' => $existingBinding->tenant_id,
                        'existing_user_id' => $existingBinding->user_id,
                        'new_auth_id' => $authRequest->id,
                        'new_tenant_id' => $authRequest->tenant_id,
                        'new_user_id' => $authRequest->user_id,
                    ]);

                    $this->sendTextMessage($from,
                        "⚠️ Este número de WhatsApp já está vinculado a outro usuário no Sistema Dominus.\n\n" .
                        "Cada número só pode ser vinculado a um único usuário.\n\n" .
                        "Se você deseja trocar a vinculação, peça ao administrador para desvincular o número anterior no painel do sistema."
                    );
                    $this->markMessageAsRead($messageId);
                    return;
                }

                Log::info("Vinculando usuário {$authRequest->user_id} do tenant {$authRequest->tenant_id} ao número {$from}");

                try {
                    // Inicializar o contexto do tenant
                    tenancy()->initialize($authRequest->tenant_id);

                    Log::info("👤 Buscando usuário para vinculação (Controller)", [
                        'user_id' => $authRequest->user_id,
                        'tenant_id' => $authRequest->tenant_id,
                        'from' => $from,
                    ]);

                    $user = User::find($authRequest->user_id);
                    if ($user) {
                        Log::info("✅ Usuário encontrado, iniciando vinculação (Controller)", [
                            'user_id' => $user->id,
                            'user_name' => $user->name,
                        ]);

                        $user->whatsapp_number = $from;
                        $user->save();

                        Log::info("✅ Número WhatsApp salvo no usuário (Controller)", [
                            'user_id' => $user->id,
                            'whatsapp_number' => $from,
                        ]);

                        // Salvar ou atualizar integração WhatsApp
                        $this->saveOrUpdateIntegracao($user->id, $from);

                        // Atualizar registro na tabela whatsapp_auth_requests (não deletar!)
                        // Isso permite resolver tenant pelo wa_id em mensagens futuras
                        $statusAntigo = $authRequest->status;
                        $waIdAntigo = $authRequest->wa_id;

                        Log::info("📝 Preparando atualização do registro whatsapp_auth_requests (Controller)", [
                            'auth_request_id' => $authRequest->id,
                            'status_antes' => $statusAntigo,
                            'wa_id_antes' => $waIdAntigo,
                            'wa_id_novo' => $from,
                        ]);

                        $authRequest->wa_id = $from;
                        $authRequest->status = 'active';

                        Log::info("💾 Salvando registro whatsapp_auth_requests (Controller)", [
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
                        Log::error("❌ Usuário não encontrado (Controller) - REGISTRO MANTIDO PARA INVESTIGAÇÃO", [
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
                    Log::error("Erro ao vincular tenant/user: " . $e->getMessage(), [
                        'trace' => $e->getTraceAsString()
                    ]);
                    $this->sendTextMessage($from, "❌ Erro ao processar sua solicitação. Tente novamente ou entre em contato com o suporte do Dominus.");
                } finally {
                    // Garantir que o contexto do tenant seja limpo
                    tenancy()->end();
                }
            } else {
                Log::warning("UUID não encontrado no banco: {$uuid}");
                $this->sendTextMessage($from, "❌ Código de vinculação inválido ou expirado. Por favor, gere um novo código no sistema Dominus.");
            }
        } else {
            // Mensagem comum de usuário vinculado
            $this->handleIncomingMessage($from, $text);
        }
    }

    /**
     * Processar mensagem de mídia (imagem, documento, etc)
     * Baixa a mídia usando o token da Meta e prepara para processamento
     */
    private function handleMediaMessage($from, $message, $type)
    {
        if (!isset($message[$type])) {
            Log::warning("Estrutura de mídia inválida para tipo: {$type}");
            return;
        }

        $mediaData = $message[$type];
        $mediaId = $mediaData['id'] ?? null;
        $mimeType = $mediaData['mime_type'] ?? null;
        $caption = $mediaData['caption'] ?? null;
        $filename = $mediaData['filename'] ?? null;

        if (!$mediaId) {
            Log::warning("Media ID não encontrado na mensagem");
            return;
        }

        Log::info("Mídia recebida de {$from}: tipo={$type}, id={$mediaId}, mime={$mimeType}");

        try {
            // Obter URL da mídia usando o token da Meta
            $mediaUrl = $this->getMediaUrl($mediaId);

            if ($mediaUrl) {
                // Baixar e processar a mídia
                $this->downloadAndProcessMedia($mediaUrl, $from, $type, $mimeType, $filename, $caption);
            } else {
                Log::error("Não foi possível obter URL da mídia {$mediaId}");
                $this->sendTextMessage($from, "❌ Erro ao processar o arquivo enviado. Tente novamente.");
            }
        } catch (\Exception $e) {
            Log::error("Erro ao processar mídia: " . $e->getMessage());
            $this->sendTextMessage($from, "❌ Erro ao processar o arquivo enviado.");
        }
    }

    /**
     * Obter URL da mídia usando o token da Meta
     * A Meta retorna uma URL temporária para download
     */
    private function getMediaUrl($mediaId)
    {
        try {
            $response = Http::withToken($this->accessToken)
                ->get("{$this->metaApiUrl}/{$mediaId}");

            if ($response->successful()) {
                $data = $response->json();
                return $data['url'] ?? null;
            } else {
                Log::error("Erro ao obter URL da mídia. Status: {$response->status()}, Body: {$response->body()}");
            }
        } catch (\Exception $e) {
            Log::error("Erro ao obter URL da mídia: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Baixar e processar mídia recebida
     */
    private function downloadAndProcessMedia($mediaUrl, $from, $type, $mimeType, $filename, $caption)
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
                    'mime_type' => $mimeType,
                    'filename' => $filename,
                ]);
                $this->sendTextMessage($from, "⚠️ Eita, no momento, só estou aceitando arquivos no formato PDF e imagem.");
                return;
            }
            
            // Verificar se o mimeType é suportado (se disponível)
            if ($mimeType && !in_array($mimeType, $allowedMimeTypes)) {
                Log::warning("MIME type não suportado: {$mimeType}", [
                    'from' => $from,
                    'type' => $type,
                    'filename' => $filename,
                ]);
                $this->sendTextMessage($from, "⚠️ Eita, no momento, só estou aceitando arquivos no formato PDF e imagem.");
                return;
            }

            // Baixar o arquivo usando o token da Meta
            $response = Http::withToken($this->accessToken)
                ->get($mediaUrl);

            if ($response->successful()) {
                $fileContent = $response->body();

                // Determinar extensão do arquivo
                $extension = $this->getFileExtension($mimeType, $type, $filename);

                // Gerar nome único para o arquivo
                $uniqueFilename = Str::uuid() . '.' . $extension;

                // Salvar arquivo temporariamente (ou processar diretamente)
                $storagePath = storage_path('app/temp/whatsapp/' . $uniqueFilename);

                // Criar diretório se não existir
                if (!is_dir(dirname($storagePath))) {
                    mkdir(dirname($storagePath), 0755, true);
                }

                file_put_contents($storagePath, $fileContent);

                Log::info("Mídia baixada e salva: {$storagePath}", [
                    'from' => $from,
                    'type' => $type,
                    'size' => strlen($fileContent),
                    'mime' => $mimeType
                ]);

                // TODO: Processar o arquivo (extrair dados de PDF, OCR de imagens, etc)
                // Aqui você pode chamar serviços de processamento de documentos
                // Exemplo: processarDocumentoFinanceiro($storagePath, $from, $type);

                // Enviar confirmação
                $this->sendTextMessage($from, "✅ Arquivo recebido! Estamos processando...");

            } else {
                Log::error("Erro ao baixar mídia. Status: {$response->status()}");
            }
        } catch (\Exception $e) {
            Log::error("Erro ao baixar e processar mídia: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Determinar extensão do arquivo baseado no MIME type ou tipo
     */
    private function getFileExtension($mimeType, $type, $filename)
    {
        // Se tiver filename, extrair extensão
        if ($filename) {
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            if ($ext) {
                return $ext;
            }
        }

        // Mapear MIME types comuns
        $mimeMap = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            'application/pdf' => 'pdf',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/vnd.ms-excel' => 'xls',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
        ];

        if ($mimeType && isset($mimeMap[$mimeType])) {
            return $mimeMap[$mimeType];
        }

        // Fallback baseado no tipo
        $typeMap = [
            'image' => 'jpg',
            'document' => 'pdf',
            'audio' => 'mp3',
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
            $response = Http::withToken($this->accessToken)
                ->post("{$this->metaApiUrl}/{$this->phoneNumberId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'recipient_type' => 'individual',
                    'to' => $to,
                    'type' => 'text',
                    'text' => [
                        'preview_url' => false,
                        'body' => $text
                    ]
                ]);

            if ($response->successful()) {
                Log::info("Mensagem enviada para {$to}");
                return $response->json();
            } else {
                Log::error("Erro ao enviar mensagem: " . $response->body());
            }
        } catch (\Exception $e) {
            Log::error("Erro ao enviar mensagem: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Marcar mensagem como lida
     * Usa a API da Meta para marcar mensagens como lidas
     */
    private function markMessageAsRead($messageId)
    {
        try {
            $response = Http::withToken($this->accessToken)
                ->post("{$this->metaApiUrl}/{$this->phoneNumberId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'status' => 'read',
                    'message_id' => $messageId
                ]);

            if ($response->successful()) {
                Log::info("Mensagem {$messageId} marcada como lida");
            } else {
                Log::warning("Erro ao marcar mensagem como lida. Status: {$response->status()}, Body: {$response->body()}");
            }
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
     * Handler genérico para mensagens de usuários vinculados
     */
    private function handleIncomingMessage($fromNumber, $text)
    {
        Log::info("Mensagem de texto recebida de {$fromNumber}: {$text}");
        // TODO: Implementar lógica de comandos, IA, etc.
    }

    /**
     * Criar ou atualizar integração pendente quando gera QR Code
     */
    private function createOrUpdatePendenteIntegracao($userId)
    {
        try {
            $destinatario = $this->systemNumber;

            // Verificar se já existe integração WhatsApp para este usuário
            $integracao = Integracao::where('user_id', $userId)
                ->where('tipo', 'whatsapp')
                ->first();

            if ($integracao) {
                // Se já existe e está configurado, não fazer nada
                if ($integracao->status === 'configurado') {
                    return;
                }
                // Se está pendente, atualizar destinatário
                $integracao->destinatario = $destinatario;
                $integracao->save();
            } else {
                // Criar nova integração pendente
                Integracao::create([
                    'tipo' => 'whatsapp',
                    'status' => 'pendente',
                    'remetente' => null,
                    'destinatario' => $destinatario,
                    'user_id' => $userId,
                ]);
            }

            Log::info("Integração WhatsApp pendente criada/atualizada para usuário {$userId}");
        } catch (\Exception $e) {
            Log::error("Erro ao criar integração pendente: " . $e->getMessage());
        }
    }

    /**
     * Salvar ou atualizar integração WhatsApp quando vinculado
     */
    private function saveOrUpdateIntegracao($userId, $remetente)
    {
        try {
            $tenantId = tenancy()->tenant->id;
            $destinatario = $this->phoneNumberId;

            // Atualizar whatsapp_auth_requests para marcar como active
            $authRequest = WhatsappAuthRequest::where('tenant_id', $tenantId)
                ->where('user_id', $userId)
                ->first();

            if ($authRequest) {
                $authRequest->status = 'active';
                // Garantir que phone_number_id está salvo
                if (!$authRequest->phone_number_id) {
                    $authRequest->phone_number_id = $this->phoneNumberId;
                }
                // Garantir que access_token está salvo
                if (!$authRequest->access_token) {
                    $authRequest->access_token = $this->accessToken;
                }
                $authRequest->save();
                Log::info("whatsapp_auth_requests atualizado para status 'active' para usuário {$userId}");
            }

            // Verificar se já existe integração WhatsApp para este usuário
            $integracao = Integracao::where('user_id', $userId)
                ->where('tipo', 'whatsapp')
                ->first();

            if ($integracao) {
                // Atualizar integração existente
                $integracao->status = 'configurado';
                $integracao->remetente = $remetente;
                $integracao->destinatario = $destinatario;
                $integracao->save();
            } else {
                // Criar nova integração
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

    /**
     * Listar integrações do usuário atual
     */
    public function listarIntegracoes()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'error' => 'Usuário não autenticado'
                ], 401);
            }

            $integracoes = Integracao::where('user_id', $user->id)
                ->orderBy('tipo')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $integracoes
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao listar integrações: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Excluir integração
     */
    public function excluirIntegracao($id)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'error' => 'Usuário não autenticado'
                ], 401);
            }

            $integracao = Integracao::where('id', $id)
                ->where('user_id', $user->id)
                ->first();

            if (!$integracao) {
                return response()->json([
                    'success' => false,
                    'error' => 'Integração não encontrada'
                ], 404);
            }

            // Se for WhatsApp configurado, remover número do usuário E marcar auth request como inactive
            if ($integracao->tipo === 'whatsapp' && $integracao->status === 'configurado') {
                $user->whatsapp_number = null;
                $user->save();

                // Marcar registro whatsapp_auth_requests como inactive (não deletar para manter histórico)
                $this->markAuthRequestAsInactive($user->id);
            }

            $integracao->delete();

            Log::info("Integração {$integracao->tipo} excluída pelo usuário {$user->id}");

            return response()->json([
                'success' => true,
                'message' => 'Integração excluída com sucesso'
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao excluir integração: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Extrair wamid (WhatsApp Message ID) do payload
     * Busca o campo 'id' nas mensagens do payload
     *
     * @param array $data
     * @return string|null
     */
    private function extractWamidFromPayload($data)
    {
        if (!isset($data['entry']) || !is_array($data['entry'])) {
            return null;
        }

        foreach ($data['entry'] as $entry) {
            if (!isset($entry['changes']) || !is_array($entry['changes'])) {
                continue;
            }

            foreach ($entry['changes'] as $change) {
                if (!isset($change['value']['messages']) || !is_array($change['value']['messages'])) {
                    continue;
                }

                foreach ($change['value']['messages'] as $message) {
                    if (isset($message['id'])) {
                        return $message['id'];
                    }
                }
            }
        }

        return null;
    }

    /**
     * Verificar se mensagem já foi processada (deduplicação)
     * Verifica na tabela whatsapp_messages_processed (banco central)
     *
     * @param string $wamid
     * @return bool
     */
    private function isMessageProcessed($wamid)
    {
        if (!$wamid) {
            return false;
        }

        // Usar conexão central (mysql) pois a tabela está no banco central
        return DB::connection('mysql')
            ->table('whatsapp_messages_processed')
            ->where('wamid', $wamid)
            ->exists();
    }

    /**
     * Verificar se o webhook contém apenas status updates (sem mensagens para processar)
     * Webhooks de status (sent, delivered, read) não requerem processamento de tenant
     *
     * @param array $data
     * @return bool
     */
    private function isStatusOnlyWebhook($data): bool
    {
        if (!isset($data['entry']) || !is_array($data['entry'])) {
            return false;
        }

        foreach ($data['entry'] as $entry) {
            if (!isset($entry['changes']) || !is_array($entry['changes'])) {
                continue;
            }

            foreach ($entry['changes'] as $change) {
                $value = $change['value'] ?? [];

                // Se tem statuses MAS não tem messages, é apenas status update
                if (isset($value['statuses']) &&
                    is_array($value['statuses']) &&
                    !empty($value['statuses']) &&
                    (!isset($value['messages']) || empty($value['messages']))) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Extrair wa_id (número do remetente) do payload
     * Busca o campo 'from' nas mensagens do payload
     *
     * @param array $data
     * @return string|null
     */
    private function extractWaIdFromPayload($data)
    {
        if (!isset($data['entry']) || !is_array($data['entry'])) {
            return null;
        }

        foreach ($data['entry'] as $entry) {
            if (!isset($entry['changes']) || !is_array($entry['changes'])) {
                continue;
            }

            foreach ($entry['changes'] as $change) {
                if (!isset($change['value']['messages']) || !is_array($change['value']['messages'])) {
                    continue;
                }

                foreach ($change['value']['messages'] as $message) {
                    if (isset($message['from'])) {
                        $waId = $message['from'];
                        Log::debug("wa_id extraído do payload: {$waId}");
                        return $waId;
                    }
                }
            }
        }

        return null;
    }

    /**
     * Extrair verification_code (UUID) das mensagens de texto do payload
     * Busca por UUID no formato xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx nas mensagens de texto
     *
     * @param array $data
     * @return string|null
     */
    private function extractVerificationCodeFromPayload($data)
    {
        if (!isset($data['entry']) || !is_array($data['entry'])) {
            return null;
        }

        // Padrão UUID: xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx
        $uuidPattern = '/[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}/i';

        foreach ($data['entry'] as $entry) {
            if (!isset($entry['changes']) || !is_array($entry['changes'])) {
                continue;
            }

            foreach ($entry['changes'] as $change) {
                if (!isset($change['value']['messages']) || !is_array($change['value']['messages'])) {
                    continue;
                }

                foreach ($change['value']['messages'] as $message) {
                    // Verificar mensagens de texto
                    if (isset($message['type']) && $message['type'] === 'text' && isset($message['text']['body'])) {
                        $text = $message['text']['body'];

                        if (preg_match($uuidPattern, $text, $matches)) {
                            $uuid = $matches[0];
                            Log::info("UUID encontrado na mensagem de texto: {$uuid}");
                            return $uuid;
                        }
                    }
                }
            }
        }

        return null;
    }

    /**
     * Resolver tenant pelo verification_code
     * Busca na tabela whatsapp_auth_requests (banco central) pelo verification_code
     *
     * @param string $verificationCode
     * @return \App\Models\Tenant|null
     */
    private function resolveTenantByVerificationCode($verificationCode)
    {
        if (!$verificationCode) {
            Log::warning('verification_code não fornecido para resolução de tenant');
            return null;
        }

        Log::info("Resolvendo tenant pelo verification_code: {$verificationCode}");

        // Buscar registro na tabela whatsapp_auth_requests (banco central)
        $authRequest = WhatsappAuthRequest::where('verification_code', $verificationCode)->first();

        if (!$authRequest) {
            Log::warning("Nenhum registro encontrado para verification_code: {$verificationCode}");
            return null;
        }

        // Verificar se o código expirou
        if ($authRequest->isExpired()) {
            Log::warning("Código de vinculação expirado: {$verificationCode}");
            return null;
        }

        // Buscar o tenant
        $tenant = \App\Models\Tenant::find($authRequest->tenant_id);

        if (!$tenant) {
            Log::error("Tenant não encontrado para tenant_id: {$authRequest->tenant_id}", [
                'auth_request_id' => $authRequest->id,
                'verification_code' => $verificationCode
            ]);
            return null;
        }

        Log::info("Tenant resolvido pelo verification_code: {$tenant->id} ({$tenant->name})");
        return $tenant;
    }

    /**
     * Resolver tenant pelo wa_id (número do WhatsApp do remetente)
     * Busca na tabela whatsapp_auth_requests (banco central) um registro ativo com o wa_id
     * Esta é a forma correta de resolver tenant para mensagens normais (após vinculação)
     *
     * @param string $waId
     * @return \App\Models\Tenant|null
     */
    private function resolveTenantByWaId($waId)
    {
        if (!$waId) {
            Log::warning('wa_id não fornecido para resolução de tenant');
            return null;
        }

        Log::info("Resolvendo tenant pelo wa_id: {$waId}");

        // Buscar registro ativo na tabela whatsapp_auth_requests (banco central)
        $authRequest = WhatsappAuthRequest::byWaId($waId)
            ->active()
            ->first();

        if (!$authRequest) {
            Log::warning("Nenhum registro ativo encontrado para wa_id: {$waId}");
            return null;
        }

        // Buscar o tenant
        $tenant = \App\Models\Tenant::find($authRequest->tenant_id);

        if (!$tenant) {
            Log::error("Tenant não encontrado para tenant_id: {$authRequest->tenant_id}", [
                'auth_request_id' => $authRequest->id,
                'wa_id' => $waId
            ]);
            return null;
        }

        Log::info("Tenant resolvido pelo wa_id: {$tenant->id} ({$tenant->name}) para wa_id: {$waId}");
        return $tenant;
    }

    /**
     * Resolver tenant pelo phone_number_id
     * Busca na tabela whatsapp_auth_requests um registro ativo com o phone_number_id
     * Nota: Em SaaS multi-tenant, todos compartilham o mesmo phone_number_id, então esta resolução é menos confiável
     *
     * @param string $phoneNumberId
     * @return \App\Models\Tenant|null
     */
    private function resolveTenantByPhoneNumberId($phoneNumberId)
    {
        if (!$phoneNumberId) {
            Log::warning('phone_number_id não fornecido para resolução de tenant');
            return null;
        }

        Log::info("Resolvendo tenant pelo phone_number_id: {$phoneNumberId}");

        // Buscar registro ativo na tabela whatsapp_auth_requests
        $authRequest = WhatsappAuthRequest::byPhoneNumberId($phoneNumberId)
            ->active()
            ->first();

        if (!$authRequest) {
            // Log detalhado para debug: verificar se existe algum registro (mesmo inativo)
            $anyRequest = WhatsappAuthRequest::byPhoneNumberId($phoneNumberId)->first();
            $allRequests = WhatsappAuthRequest::whereNotNull('phone_number_id')
                ->select('id', 'phone_number_id', 'status', 'tenant_id')
                ->get()
                ->map(function($req) {
                    return [
                        'id' => $req->id,
                        'phone_number_id' => $req->phone_number_id,
                        'status' => $req->status,
                        'tenant_id' => $req->tenant_id
                    ];
                })
                ->toArray();

            Log::warning("Nenhum registro ativo encontrado para phone_number_id: {$phoneNumberId}", [
                'phone_number_id_procurado' => $phoneNumberId,
                'existe_registro_inativo' => $anyRequest ? 'Sim' : 'Não',
                'registro_inativo_detalhes' => $anyRequest ? [
                    'id' => $anyRequest->id,
                    'status' => $anyRequest->status,
                    'tenant_id' => $anyRequest->tenant_id
                ] : null,
                'todos_phone_number_ids_cadastrados' => $allRequests,
                'total_registros' => count($allRequests)
            ]);
            return null;
        }

        // Buscar o tenant
        $tenant = \App\Models\Tenant::find($authRequest->tenant_id);

        if (!$tenant) {
            Log::error("Tenant não encontrado para tenant_id: {$authRequest->tenant_id}", [
                'auth_request_id' => $authRequest->id,
                'phone_number_id' => $phoneNumberId
            ]);
            return null;
        }

        Log::info("Tenant resolvido: {$tenant->id} ({$tenant->name}) para phone_number_id: {$phoneNumberId}");
        return $tenant;
    }

    /**
     * Validar assinatura do webhook usando HMAC SHA256
     * Valida o header X-Hub-Signature-256 usando o META_APP_SECRET
     *
     * IMPORTANTE: Usa $request->getContent() para obter o payload RAW original,
     * pois qualquer modificação (ordem de chaves, espaçamento, etc.) invalidaria o hash.
     *
     * @param Request $request
     * @return bool
     */
    private function validateWebhookSignature(Request $request)
    {
        // Se não tiver APP_SECRET configurado, pular validação (não recomendado em produção)
        if (!$this->appSecret) {
            Log::warning('META_APP_SECRET não configurado. Validação de assinatura desabilitada.');
            return true; // Permitir continuar, mas logar aviso
        }

        $signature = $request->header('X-Hub-Signature-256');

        if (!$signature) {
            Log::warning('Header X-Hub-Signature-256 não encontrado na requisição');
            return false;
        }

        // Remover prefixo "sha256=" se presente (case-insensitive)
        $signature = preg_replace('/^sha256=/i', '', $signature);
        $signature = trim($signature);

        // IMPORTANTE: Usar getContent() para obter o payload RAW original
        // Não usar json_encode() ou outras transformações, pois mudanças na ordem
        // das chaves ou espaçamento invalidariam o hash
        $payload = $request->getContent();

        if (empty($payload)) {
            Log::error('Payload vazio - não foi possível obter o conteúdo RAW para validação da assinatura');
            return false;
        }

        // Calcular hash do payload usando o App Secret
        $expectedSignature = hash_hmac('sha256', $payload, $this->appSecret);

        // Comparar assinaturas de forma segura
        $isValid = hash_equals($expectedSignature, $signature);

        if (!$isValid) {
            Log::error('Assinatura do webhook inválida', [
                'expected' => $expectedSignature,
                'received' => $signature,
                'payload_length' => strlen($payload),
            ]);
        } else {
            Log::info('Assinatura do webhook validada com sucesso');
        }

        return $isValid;
    }

    /**
     * Marcar registro whatsapp_auth_requests como inactive
     * Usado quando o usuário exclui a integração WhatsApp
     * O registro é mantido para histórico, mas marcado como inactive para bloquear novas mensagens
     *
     * @param int $userId
     * @return void
     */
    private function markAuthRequestAsInactive($userId)
    {
        try {
            $tenantId = tenancy()->tenant->id;

            $authRequest = WhatsappAuthRequest::where('tenant_id', $tenantId)
                ->where('user_id', $userId)
                ->first();

            if ($authRequest) {
                $waIdAntes = $authRequest->wa_id;
                $statusAntes = $authRequest->status;

                // Marcar como inativo e limpar dados sensíveis
                $authRequest->status = 'inactive';
                $authRequest->wa_id = null; // Liberar número para re-vinculação em outro tenant
                $authRequest->access_token = null; // Limpar token sensível por segurança
                $authRequest->save();

                Log::info("📝 Registro whatsapp_auth_requests marcado como inactive (integração excluída)", [
                    'auth_request_id' => $authRequest->id,
                    'tenant_id' => $tenantId,
                    'user_id' => $userId,
                    'wa_id' => $waIdAntes,
                    'status_antes' => $statusAntes,
                    'status_depois' => 'inactive',
                    'access_token_limpo' => true,
                ]);
            } else {
                Log::warning("Registro whatsapp_auth_requests não encontrado ao marcar como inactive", [
                    'tenant_id' => $tenantId,
                    'user_id' => $userId,
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Erro ao marcar whatsapp_auth_requests como inactive: " . $e->getMessage(), [
                'user_id' => $userId,
            ]);
        }
    }
}
