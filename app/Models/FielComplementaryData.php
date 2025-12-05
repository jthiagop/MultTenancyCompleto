<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FielComplementaryData extends Model
{
    use HasFactory;

    protected $fillable = [
        'fiel_id',
        'data_cadastro',
        'profissao',
        'estado_civil',
        'nacionalidade',
        'natural',
        'uf_natural',
        'passaporte',
        'titulo_eleitor',
        'zona',
        'secao',
        'observacoes',
    ];

    protected $casts = [
        'data_cadastro' => 'date',
    ];

    public function fiel()
    {
        return $this->belongsTo(Fiel::class);
    }
}
