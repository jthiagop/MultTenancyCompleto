<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Centraliza o envio de notificações financeiras para uma empresa.
 *
 * Regras de negócio:
 *  - Notificações são enviadas a TODOS os usuários da company que possuem
 *    a permissão "financeiro.index" — não apenas a quem criou o registro.
 *  - Cada usuário recebe sua própria cópia na tabela `notifications`,
 *    permitindo leitura e arquivamento independentes.
 *  - O campo `company_id` no payload é usado pelo NotificationController
 *    para filtrar as notificações por empresa ativa na sessão.
 */
class NotificacaoFinanceiraService
{
    /**
     * Retorna todos os usuários da company com permissão financeiro.index.
     *
     * @param  int|null  $companyId  Null → retorna array vazio (sem envio)
     * @return Collection<int, User>
     */
    public function usuariosDaEmpresa(?int $companyId): Collection
    {
        if (! $companyId) {
            return collect();
        }

        return User::permission('financeiro.index')
            ->whereHas('companies', fn ($q) => $q->where('company_id', $companyId))
            ->get();
    }

    /**
     * Envia uma notificação para todos os usuários financeiros da empresa.
     *
     * @param  int|null      $companyId   ID da empresa
     * @param  Notification  $notification  Instância da notificação a enviar
     * @return int  Número de usuários notificados
     */
    public function notificarEmpresa(?int $companyId, Notification $notification): int
    {
        $usuarios = $this->usuariosDaEmpresa($companyId);

        if ($usuarios->isEmpty()) {
            Log::debug('[NotificacaoFinanceira] Nenhum usuário elegível', ['company_id' => $companyId]);
            return 0;
        }

        $total = 0;

        foreach ($usuarios as $user) {
            try {
                $user->notify(clone $notification);
                $total++;
            } catch (\Exception $e) {
                Log::warning('[NotificacaoFinanceira] Erro ao notificar usuário', [
                    'user_id'    => $user->id,
                    'company_id' => $companyId,
                    'erro'       => $e->getMessage(),
                ]);
            }
        }

        return $total;
    }
}
