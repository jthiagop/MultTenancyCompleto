<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FielSpouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'fiel_id',
        'fiel_conjuge_id',
        'nome_conjuge',
        'data_nascimento',
        'ocultar_ano',
        'profissao',
        'dizimista',
        'codigo_dizimista',
        'cartao_magnetico',
        'percentual_salario',
        'criar_ficha',
    ];

    protected $casts = [
        'data_nascimento' => 'date',
        'ocultar_ano' => 'boolean',
        'dizimista' => 'boolean',
        'criar_ficha' => 'boolean',
        'percentual_salario' => 'decimal:2',
    ];

    public function fiel()
    {
        return $this->belongsTo(Fiel::class);
    }

    public function conjuge()
    {
        return $this->belongsTo(Fiel::class, 'fiel_conjuge_id');
    }
}
