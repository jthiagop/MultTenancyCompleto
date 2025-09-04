<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Os comandos Artisan do seu aplicativo.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\AtualizarStatusContas::class,
        \App\Console\Commands\SeedFormasPagamento::class,
    ];

    /**
     * Define o agendamento dos comandos do aplicativo.
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('contas:atualizar-status')->daily();
    }

    /**
     * Registra os comandos do aplicativo.
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
