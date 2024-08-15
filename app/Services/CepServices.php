<?php

namespace App\Services; // Corrigido o namespace

use Illuminate\Support\Facades\Http; // Importa o Http

class CepServices
{
    public function consultar(string $cep)
    {
        $response = Http::get(
            "https://webmaniabr.com/api/1/cep/{$cep}/?app_key=dBvRCaKVRxCxLWzNcEuh08B8PWpqVW8Z&app_secret=TgUIlutKWRhtkLT5NiIGiH99251XUjX53JwaWtVOg6bLqReg"
        );

        return $response->json(); // Retorna a resposta como JSON
    }
}
