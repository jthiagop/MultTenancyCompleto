<?php

namespace App\Events\Ia;

use App\Models\DomusDocumento;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Disparado quando o Job de análise da Domus IA finaliza um documento
 * (sucesso ou erro). Implementa ShouldBroadcast para que o front-end
 * possa reagir em tempo real via Laravel Echo (Reverb / Pusher).
 *
 * Para ativar broadcast em tempo real:
 *   1. composer require laravel/reverb (ou pusher/pusher-php-server)
 *   2. php artisan install:broadcasting
 *   3. Configurar BROADCAST_CONNECTION=reverb no .env
 *   4. Front-end: instalar laravel-echo + pusher-js e iniciar window.Echo
 *
 * Sem broadcast configurado, o evento ainda é disparado localmente —
 * o front-end recorre a polling enquanto o canal não estiver disponível.
 */
class DocumentoProcessado implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Identificador do tenant — usado na composição do canal privado.
     */
    public string $tenantId;

    /**
     * ID do DomusDocumento processado.
     */
    public int $documentoId;

    /**
     * Status final ('processado', 'erro', 'lancado').
     */
    public string $status;

    /**
     * Mensagem opcional (resumo da IA ou descrição do erro).
     */
    public ?string $mensagem;

    /**
     * Hash/ID do canal de WhatsApp ou usuário-alvo (quando aplicável).
     */
    public ?string $userPhone;

    public function __construct(
        string $tenantId,
        int $documentoId,
        string $status,
        ?string $mensagem = null,
        ?string $userPhone = null,
    ) {
        $this->tenantId    = $tenantId;
        $this->documentoId = $documentoId;
        $this->status      = $status;
        $this->mensagem    = $mensagem;
        $this->userPhone   = $userPhone;
    }

    /**
     * Canal privado por tenant — todos os clientes do tenant recebem
     * notificações de processamento. O front-end filtra pelo documentoId
     * que estiver visualizando.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("tenant.{$this->tenantId}.domus-ia"),
        ];
    }

    /**
     * Nome do evento no canal — fixo para que o front-end possa escutar
     * por uma string estável independentemente do FQCN.
     */
    public function broadcastAs(): string
    {
        return 'documento.processado';
    }

    /**
     * Payload mínimo enviado ao cliente — não inclua dados sensíveis aqui.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'documento_id' => $this->documentoId,
            'status'       => $this->status,
            'mensagem'     => $this->mensagem,
        ];
    }
}
