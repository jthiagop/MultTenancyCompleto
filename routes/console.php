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

// Notificar usuários sobre contas atrasadas, que vencem hoje ou amanhã
// Roda a cada hora — o comando internamente verifica o horário configurado por empresa (padrão 08:00)
Schedule::command('financeiro:notificar-contas-vencendo')
    ->hourly()
    ->withoutOverlapping();

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

// Auditoria de saldos financeiros: detecta e corrige divergências entre saldo_atual (cache)
// e a soma real das movimentações. Roda diariamente às 02:00 e registra no laravel.log.
// Para auditar sem corrigir, rode manualmente: php artisan financeiro:recalcular-saldos --dry-run
Schedule::command('financeiro:recalcular-saldos')
    ->dailyAt('02:00')
    ->withoutOverlapping()
    ->runInBackground();
