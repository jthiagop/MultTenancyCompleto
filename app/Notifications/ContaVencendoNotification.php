<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

/**
 * Notificação para contas que vencem hoje, amanhã, esta semana ou estão atrasadas.
 *
 * Urgências disponíveis: 'atrasado' | 'hoje' | 'amanha' | 'semana'
 */
class ContaVencendoNotification extends Notification
{

    public function __construct(
        protected string  $descricao,
        protected float   $valor,
        protected string  $dataVencimento,     // formato "DD/MM/YYYY"
        protected string  $dataVencimentoIso,  // formato "YYYY-MM-DD"
        protected string  $subTipo,            // 'receita' | 'despesa'
        protected ?int    $transacaoId = null,
        protected ?int    $companyId   = null,
        protected string  $urgencia    = 'hoje', // 'atrasado'|'hoje'|'amanha'|'semana'
        protected ?int    $triggeredBy = null,  // ID do usuário que criou a transação
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $valorFmt = 'R$ ' . number_format($this->valor, 2, ',', '.');

        $titulos = [
            'atrasado' => 'Conta em atraso!',
            'hoje'     => 'Conta vence hoje',
            'amanha'   => 'Conta vence amanhã',
            'semana'   => 'Conta vence esta semana',
        ];

        $cores = [
            'atrasado' => 'danger',
            'hoje'     => 'warning',
            'amanha'   => 'info',
            'semana'   => 'info',
        ];

        $mensagens = [
            'atrasado' => "'{$this->descricao}' ({$valorFmt}) estava prevista para {$this->dataVencimento} e ainda está em aberto.",
            'hoje'     => "'{$this->descricao}' ({$valorFmt}) vence hoje ({$this->dataVencimento}).",
            'amanha'   => "'{$this->descricao}' ({$valorFmt}) vence amanhã ({$this->dataVencimento}).",
            'semana'   => "'{$this->descricao}' ({$valorFmt}) vence em {$this->dataVencimento}.",
        ];

        return [
            'icon'                 => 'ki-calendar-tick',
            'color'                => $cores[$this->urgencia]    ?? 'warning',
            'title'                => $titulos[$this->urgencia]  ?? 'Conta a Vencer',
            'message'              => $mensagens[$this->urgencia] ?? $mensagens['semana'],
            'action_url'           => null,
            'target'               => '_self',
            'tipo'                 => 'conta_vencendo',
            'categoria'            => 'financeiro',
            'sub_tipo'             => $this->subTipo,
            'urgencia'             => $this->urgencia,
            'transacao_id'         => $this->transacaoId,
            'data_vencimento'      => $this->dataVencimento,
            'data_vencimento_iso'  => $this->dataVencimentoIso,
            'valor'                => $this->valor,
            'company_id'           => $this->companyId,
            'triggered_by'         => $this->triggeredBy,
        ];
    }
}
