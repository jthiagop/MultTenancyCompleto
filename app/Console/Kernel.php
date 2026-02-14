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
        \App\Console\Commands\CleanExpiredNotifications::class,
    ];

    /**
     * Define o agendamento dos comandos do aplicativo.
     * 
     * NOTA: No Laravel 11+, schedules são definidos em routes/console.php
     * Este método está vazio para evitar duplicação.
     */
    protected function schedule(Schedule $schedule)
    {
        // Schedules movidos para routes/console.php
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
