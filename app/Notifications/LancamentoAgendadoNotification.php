<?php

namespace App\Notifications;

use App\Channels\WhatsappChannel;
use App\Channels\WhatsappNotifiable;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Notificação enviada no dia do vencimento de um lançamento marcado como "Agendado".
 *
 * Disparada pelo LancamentoAgendadoNotificacaoJob, que é agendado no momento
 * do save quando o campo `agendado = true`.
 */
class LancamentoAgendadoNotification extends Notification implements WhatsappNotifiable
{
    use Queueable;

    public function __construct(
        protected string  $descricao,
        protected float   $valor,
        protected string  $tipo,          // 'entrada' | 'saida'
        protected string  $vencimento,    // Y-m-d
        protected ?int    $transacaoId  = null,
        protected ?int    $companyId    = null,
        protected ?int    $triggeredBy  = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', WhatsappChannel::class];
    }

    public function toWhatsapp(object $notifiable): string
    {
        $isEntrada  = $this->tipo === 'entrada';
        $tipoLabel  = $isEntrada ? 'Recebimento' : 'Pagamento';
        $emoji      = $isEntrada ? '💰' : '💸';
        $valorFmt   = 'R$ ' . number_format($this->valor, 2, ',', '.');
        $vencFmt    = \Carbon\Carbon::parse($this->vencimento)->format('d/m/Y');

        return implode("\n", [
            "{$emoji} *Lançamento Agendado — Vence Hoje*",
            "",
            "*{$tipoLabel}:* {$valorFmt}",
            "📋 _{$this->descricao}_",
            "📅 Vencimento: {$vencFmt}",
            "",
            "Acesse o sistema para confirmar a baixa do lançamento.",
        ]);
    }

    public function toArray(object $notifiable): array
    {
        $isEntrada = $this->tipo === 'entrada';
        $valorFmt  = 'R$ ' . number_format($this->valor, 2, ',', '.');
        $tipoLabel = $isEntrada ? 'recebimento' : 'pagamento';

        return [
            'icon'         => 'ki-calendar-tick',
            'color'        => $isEntrada ? 'success' : 'warning',
            'title'        => 'Lançamento Agendado — Vence Hoje',
            'message'      => "O {$tipoLabel} agendado de {$valorFmt} '{$this->descricao}' vence hoje.",
            'action_url'   => null,
            'target'       => '_self',
            'tipo'         => 'lancamento_agendado',
            'categoria'    => 'financeiro',
            'sub_tipo'     => $isEntrada ? 'receita' : 'despesa',
            'acao'         => 'vencimento',
            'transacao_id' => $this->transacaoId,
            'company_id'   => $this->companyId,
            'triggered_by' => $this->triggeredBy,
        ];
    }
}
