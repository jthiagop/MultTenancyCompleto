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
        \App\Console\Commands\CleanExpiredWhatsappCodes::class,
        \App\Console\Commands\CleanExpiredPdfGenerations::class,
    ];

    /**
     * Define o agendamento dos comandos do aplicativo.
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('contas:atualizar-status')->daily();

        // Limpar códigos de vinculação WhatsApp expirados a cada hora
        $schedule->command('whatsapp:clean-expired-codes')->hourly();

        // Limpar PDFs gerados expirados (após 5 dias) - roda diariamente às 3h
        $schedule->command('pdf:clean-expired')->dailyAt('03:00');
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
