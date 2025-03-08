<?php

namespace App\Models\Patrimonio;

use Illuminate\Database\Eloquent\Model;

class Avaliador extends Model
{
    protected $table = 'avaliadores'; // Define o nome da tabela

    protected $fillable = [
        'user_id',
        'nome',
        'tipo_profissional',
        'registro_profissional',
        'telefone',
        'email',
        'created_by',
        'created_by_name',
        'updated_by',
        'updated_by_name',
        'created_at',
        'updated_at'
    ];
}
