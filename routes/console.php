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

// Atualizar status de contas diariamente
Schedule::command('contas:atualizar-status')
    ->daily();

// Limpar códigos de vinculação WhatsApp expirados a cada hora
Schedule::command('whatsapp:clean-expired-codes')
    ->hourly();

// Limpar PDFs expirados diariamente (remove arquivos físicos e registros após 5 dias)
Schedule::command('pdf:clean-expired --days=5')
    ->daily()
    ->withoutOverlapping();

// Limpar notificações expiradas diariamente (remove registros do banco de dados)
Schedule::command('notifications:clean-expired')
    ->daily()
    ->withoutOverlapping();
