<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\ContaVencendoNotification;
use App\Notifications\LancamentoCriadoNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

/**
 * Envia notificações de teste para o primeiro usuário do tenant.
 * Útil para validar o layout e o fluxo de notificações sem precisar de dados reais.
 *
 * IMPORTANTE: Este command deve ser executado dentro do contexto de tenant:
 *   php artisan tenants:run "financeiro:notificar-teste"
 */
class EnviarNotificacaoTeste extends Command
{
    protected $signature   = 'financeiro:notificar-teste {--user= : ID do usuário (padrão: primeiro usuário)}';
    protected $description = 'Envia notificações de teste financeiras para um usuário (execute via tenants:run)';

    public function handle(): void
    {
        $userId = $this->option('user');

        // Se --user informado, envia só para aquele; senão envia para TODOS
        $users = $userId
            ? User::where('id', $userId)->get()
            : User::all();

        if ($users->isEmpty()) {
            $this->error('Nenhum usuário encontrado.');
            return;
        }

        foreach ($users as $user) {
            $this->info("Enviando notificações de teste para: {$user->name} ({$user->email})");

        // Pega o "outro" usuário para simular quem criou a transação
        $criador = User::where('id', '!=', $user->id)->first() ?? $user;

        // 1. Conta ATRASADA (despesa) — quem criou = $criador
        $user->notify(new ContaVencendoNotification(
            descricao:         'Aluguel Sede',
            valor:             3500.00,
            dataVencimento:    Carbon::now()->subDays(3)->format('d/m/Y'),
            dataVencimentoIso: Carbon::now()->subDays(3)->format('Y-m-d'),
            subTipo:           'despesa',
            transacaoId:       null,
            companyId:         null,
            urgencia:          'atrasado',
            triggeredBy:       $criador->id,
        ));
        $this->line('  ✓ Conta atrasada (despesa) enviada');

        // 2. Conta que VENCE HOJE (receita)
        $user->notify(new ContaVencendoNotification(
            descricao:         'Mensalidade Cliente ABC',
            valor:             1200.00,
            dataVencimento:    Carbon::today()->format('d/m/Y'),
            dataVencimentoIso: Carbon::today()->format('Y-m-d'),
            subTipo:           'receita',
            transacaoId:       null,
            companyId:         null,
            urgencia:          'hoje',
            triggeredBy:       $criador->id,
        ));
        $this->line('  ✓ Conta que vence hoje (receita) enviada');

        // 3. Conta que VENCE AMANHÃ (despesa)
        $user->notify(new ContaVencendoNotification(
            descricao:         'Fornecedor XYZ — Parcela 3/6',
            valor:             850.00,
            dataVencimento:    Carbon::tomorrow()->format('d/m/Y'),
            dataVencimentoIso: Carbon::tomorrow()->format('Y-m-d'),
            subTipo:           'despesa',
            transacaoId:       null,
            companyId:         null,
            urgencia:          'amanha',
            triggeredBy:       $criador->id,
        ));
        $this->line('  ✓ Conta que vence amanhã (despesa) enviada');

        // 4. Lançamento CRIADO (receita) — criado pelo $criador
        $user->notify(new LancamentoCriadoNotification(
            descricao:   'Contrato de Serviço — Janeiro',
            valor:       5000.00,
            tipo:        'receita',
            acao:        'criado',
            transacaoId: null,
            companyId:   null,
            triggeredBy: $criador->id,
        ));
        $this->line('  ✓ Lançamento criado (receita) enviado');

        // 5. Pagamento CONFIRMADO (despesa) — pago pelo $criador
        $user->notify(new LancamentoCriadoNotification(
            descricao:   'Internet — Fevereiro',
            valor:       299.90,
            tipo:        'despesa',
            acao:        'pago',
            transacaoId: null,
            companyId:   null,
            triggeredBy: $criador->id,
        ));
        $this->line('  ✓ Pagamento confirmado (despesa) enviado');

            $this->newLine();
        }

        $this->info(count($users) * 5 . ' notificações de teste enviadas para ' . count($users) . ' usuário(s).');
    }
}
