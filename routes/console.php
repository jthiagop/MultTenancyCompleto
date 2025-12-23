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
