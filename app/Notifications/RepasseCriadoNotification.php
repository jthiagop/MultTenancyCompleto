<?php

namespace App\Notifications;

use App\Models\Financeiro\Repasse;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class RepasseCriadoNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Repasse $repasse;
    protected string $nomeMatriz;
    protected string $valorFormatado;

    public function __construct(Repasse $repasse, string $nomeMatriz, string $valorFormatado)
    {
        $this->repasse = $repasse;
        $this->nomeMatriz = $nomeMatriz;
        $this->valorFormatado = $valorFormatado;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'icon' => 'bi bi-diagram-3',
            'color' => 'warning',
            'title' => 'Novo Repasse Recebido',
            'message' => "{$this->nomeMatriz} solicitou um repasse de R$ {$this->valorFormatado}.",
            'action_url' => route('banco.list', ['tab' => 'repasses', 'tipo_repasse' => 'a_receber']),
            'target' => '_self',
            'company_id' => $this->repasse->company_origem_id,
            'tipo' => 'repasse_criado',
            'repasse_id' => $this->repasse->id,
        ];
    }
}
