<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FielTithe extends Model
{
    use HasFactory;

    protected $table = 'fiel_tithe';

    protected $fillable = [
        'fiel_id',
        'dizimista',
        'codigo',
        'percentual_salario',
        'cartao_magnetico',
        'missionario_dizimo',
        'valor_dizimo',
        'frequencia_dizimo',
        'ultima_contribuicao',
    ];

    protected $casts = [
        'dizimista' => 'boolean',
        'valor_dizimo' => 'decimal:2',
        'percentual_salario' => 'decimal:2',
        'ultima_contribuicao' => 'date',
    ];

    public function fiel()
    {
        return $this->belongsTo(Fiel::class);
    }
}
