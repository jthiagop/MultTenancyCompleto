<?php

namespace App\Casts;

use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * Cast personalizado para conversão automática de datas brasileiras
 * 
 * Aceita:
 * - Formato brasileiro: d/m/Y (16/01/2026)
 * - Formato ISO: Y-m-d (2026-01-16)
 * 
 * Armazena sempre em formato ISO (Y-m-d) no banco de dados
 */
class BrazilianDateCast implements CastsAttributes
{
    /**
     * Cast the given value (do banco para o model)
     *
     * @param  array<string, mixed>  $attributes
     * @return \Carbon\Carbon|null
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?Carbon
    {
        if ($value === null) {
            return null;
        }

        // Se já é uma instância de Carbon, retorna
        if ($value instanceof Carbon) {
            return $value;
        }

        try {
            // Tenta fazer parse da data
            return Carbon::parse($value);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Prepare the given value for storage (do model para o banco)
     *
     * @param  array<string, mixed>  $attributes
     * @return string|null
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        // Se já é uma instância de Carbon, converte para Y-m-d
        if ($value instanceof Carbon) {
            return $value->format('Y-m-d');
        }

        // Se é string, verifica o formato
        if (is_string($value)) {
            // Formato brasileiro (d/m/Y) - tem barra
            if (strpos($value, '/') !== false) {
                try {
                    return Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
                } catch (\Exception $e) {
                    // Se falhar, tenta parse genérico
                    try {
                        return Carbon::parse($value)->format('Y-m-d');
                    } catch (\Exception $e2) {
                        return null;
                    }
                }
            }
            
            // Formato ISO (Y-m-d) - tem hífen
            if (strpos($value, '-') !== false) {
                try {
                    return Carbon::parse($value)->format('Y-m-d');
                } catch (\Exception $e) {
                    return null;
                }
            }
        }

        // Tenta parse genérico como último recurso
        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}
