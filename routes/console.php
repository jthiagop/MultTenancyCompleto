<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

use Illuminate\Support\Facades\Schedule;
use App\Jobs\ConsultarNotasEntradaJob;

// Rodar a cada hora (Padrão Sefaz: não rode a cada minuto para não ser bloqueado)
Schedule::job(new ConsultarNotasEntradaJob)->hourly();

// Gerar recorrências diariamente
Schedule::command('recorrencias:gerar')->daily();

// Limpar registros antigos da tabela whatsapp_messages_processed diariamente (mantém últimos 30 dias)
Schedule::command('whatsapp:clean-old-messages --days=30')
    ->daily()
    ->withoutOverlapping(); // Evita rodar duas vezes se o anterior travar;
