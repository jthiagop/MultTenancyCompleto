<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Notificação gerada quando um lançamento financeiro é criado ou atualizado.
 */
class LancamentoCriadoNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected string $descricao,
        protected float  $valor,
        protected string $tipo,       // 'receita' | 'despesa'
        protected string $acao,       // 'criado' | 'atualizado' | 'pago' | 'recebido'
        protected ?int   $transacaoId = null,
        protected ?int   $companyId   = null,
        protected ?int   $triggeredBy = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $valorFmt = 'R$ ' . number_format($this->valor, 2, ',', '.');

        $configs = [
            'receita' => [
                'criado'    => ['icon' => 'ki-dollar',        'color' => 'success', 'title' => 'Receita lançada'],
                'atualizado'=> ['icon' => 'ki-dollar',        'color' => 'info',    'title' => 'Receita atualizada'],
                'recebido'  => ['icon' => 'ki-check-circle',  'color' => 'success', 'title' => 'Receita recebida'],
                'pago'      => ['icon' => 'ki-check-circle',  'color' => 'success', 'title' => 'Receita recebida'],
            ],
            'despesa' => [
                'criado'    => ['icon' => 'ki-minus-circle',  'color' => 'danger',  'title' => 'Despesa lançada'],
                'atualizado'=> ['icon' => 'ki-minus-circle',  'color' => 'warning', 'title' => 'Despesa atualizada'],
                'pago'      => ['icon' => 'ki-check-circle',  'color' => 'primary', 'title' => 'Despesa paga'],
                'recebido'  => ['icon' => 'ki-check-circle',  'color' => 'primary', 'title' => 'Despesa paga'],
            ],
        ];

        $cfg = $configs[$this->tipo][$this->acao]
            ?? ['icon' => 'ki-finance-calc', 'color' => 'secondary', 'title' => 'Lançamento financeiro'];

        $acaoLabel = match ($this->acao) {
            'criado'    => 'foi lançado',
            'atualizado'=> 'foi atualizado',
            'pago'      => 'foi pago',
            'recebido'  => 'foi recebido',
            default     => 'foi registrado',
        };

        return [
            'icon'         => $cfg['icon'],
            'color'        => $cfg['color'],
            'title'        => $cfg['title'],
            'message'      => "'{$this->descricao}' ({$valorFmt}) {$acaoLabel}.",
            'action_url'   => null,
            'target'       => '_self',
            'tipo'         => 'lancamento_financeiro',
            'categoria'    => 'financeiro',
            'sub_tipo'     => $this->tipo,
            'acao'         => $this->acao,
            'transacao_id' => $this->transacaoId,
            'company_id'   => $this->companyId,
            'triggered_by' => $this->triggeredBy,
        ];
    }
}
