<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

/**
 * Notificação para contas que vencem hoje ou estão próximas do vencimento.
 */
class ContaVencendoNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $descricao;
    protected float $valor;
    protected string $dataVencimento;
    protected ?int $transacaoId;
    protected ?int $companyId;
    protected string $urgencia; // 'hoje', 'amanha', 'semana'

    /**
     * Create a new notification instance.
     *
     * @param string $descricao Descrição da conta
     * @param float $valor Valor da conta
     * @param string $dataVencimento Data de vencimento formatada
     * @param int|null $transacaoId ID da transação para link direto
     * @param int|null $companyId ID da empresa
     * @param string $urgencia Nível de urgência (hoje, amanha, semana)
     */
    public function __construct(
        string $descricao,
        float $valor,
        string $dataVencimento,
        ?int $transacaoId = null,
        ?int $companyId = null,
        string $urgencia = 'hoje'
    ) {
        $this->descricao = $descricao;
        $this->valor = $valor;
        $this->dataVencimento = $dataVencimento;
        $this->transacaoId = $transacaoId;
        $this->companyId = $companyId;
        $this->urgencia = $urgencia;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        $valorFormatado = 'R$ ' . number_format($this->valor, 2, ',', '.');

        $titulos = [
            'hoje' => 'Conta Vence Hoje!',
            'amanha' => 'Conta Vence Amanhã',
            'semana' => 'Conta Vence Esta Semana',
        ];

        $cores = [
            'hoje' => 'danger',
            'amanha' => 'warning',
            'semana' => 'info',
        ];

        return [
            'icon' => 'ki-calendar-tick',
            'color' => $cores[$this->urgencia] ?? 'warning',
            'title' => $titulos[$this->urgencia] ?? 'Conta a Vencer',
            'message' => "'{$this->descricao}' no valor de {$valorFormatado} vence em {$this->dataVencimento}.",
            'action_url' => $this->transacaoId ? route('banco.show', $this->transacaoId) : null,
            'target' => '_self',
            'company_id' => $this->companyId,
            'tipo' => 'conta_vencendo',
            'transacao_id' => $this->transacaoId,
        ];
    }
}
