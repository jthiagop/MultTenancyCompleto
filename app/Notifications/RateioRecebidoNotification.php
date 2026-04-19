<?php

namespace App\Notifications;

use App\Channels\WhatsappChannel;
use App\Channels\WhatsappNotifiable;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Notificação enviada aos usuários financeiros de uma filial quando a empresa
 * matriz cria um rateio intercompany que gera uma cobrança (Contas a Pagar) para ela.
 */
class RateioRecebidoNotification extends Notification implements WhatsappNotifiable
{
    use Queueable;

    public function __construct(
        protected string $descricao,
        protected float  $valor,
        protected string $nomeMatriz,
        protected ?int   $transacaoId = null,
        protected ?int   $companyId   = null,
        protected ?int   $triggeredBy = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', WhatsappChannel::class];
    }

    public function toWhatsapp(object $notifiable): string
    {
        $valorFmt = 'R$ ' . number_format($this->valor, 2, ',', '.');

        return implode("\n", [
            "💸 *Cobrança de Rateio*",
            "",
            "*{$this->nomeMatriz}* lançou um rateio de *{$valorFmt}*",
            "📋 _{$this->descricao}_",
            "",
            "Acesse o sistema para verificar o lançamento em Contas a Pagar.",
        ]);
    }

    public function toArray(object $notifiable): array
    {
        $valorFmt = 'R$ ' . number_format($this->valor, 2, ',', '.');

        return [
            'icon'         => 'ki-abstract-26',
            'color'        => 'warning',
            'title'        => 'Cobrança de Rateio',
            'message'      => "{$this->nomeMatriz} lançou um rateio de {$valorFmt}: '{$this->descricao}'.",
            'action_url'   => null,
            'target'       => '_self',
            'tipo'         => 'rateio_recebido',
            'categoria'    => 'financeiro',
            'sub_tipo'     => 'despesa',
            'acao'         => 'criado',
            'transacao_id' => $this->transacaoId,
            'company_id'   => $this->companyId,
            'triggered_by' => $this->triggeredBy,
            'nome_matriz'  => $this->nomeMatriz,
        ];
    }
}
