<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormasPagamento extends Model
{
    use HasFactory, SoftDeletes;

    // Nome da tabela (opcional, se seguir o padrão do Laravel)
    protected $table = 'formas_pagamento';

    // Campos que podem ser preenchidos em massa
    protected $fillable = [
        'nome',
        'codigo',
        'ativo',
        'taxa',
        'tipo_taxa',
        'prazo_liberacao',
        'metodo_integracao',
        'icone',
        'observacao',
    ];

    // Campos que devem ser ocultos em respostas JSON (opcional)
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
