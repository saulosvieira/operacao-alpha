<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Sincronização de contratos com Clicksign
// Executa a cada 15 minutos para verificar status dos contratos
Schedule::command('contracts:sync')->everyFifteenMinutes();
