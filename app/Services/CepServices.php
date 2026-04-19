<?php

namespace App\Services; // Corrigido o namespace

use Illuminate\Support\Facades\Http; // Importa o Http

class CepServices
{
    public function consultar(string $cep)
    {
        $appKey    = config('services.webmaniabr.app_key');
        $appSecret = config('services.webmaniabr.app_secret');

        $response = Http::get(
            "https://webmaniabr.com/api/1/cep/{$cep}/?app_key={$appKey}&app_secret={$appSecret}"
        );

        return $response->json();
    }
}
