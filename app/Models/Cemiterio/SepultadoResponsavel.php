<?php

namespace App\Models\Cemiterio;

use Illuminate\Database\Eloquent\Model;

class SepultadoResponsavel extends Model
{
    protected $table = 'sepultado_responsaveis';

    protected $fillable = [
        'sepultado_id',
        'nome',
        'telefone',
        'cep',
        'logradouro',
        'numero',
        'bairro',
        'cidade',
        'uf',
    ];

    public function sepultado()
    {
        return $this->belongsTo(Sepultado::class);
    }
}
