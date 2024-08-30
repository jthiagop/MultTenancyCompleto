<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patrimonio extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'descricao',
        'patrimonio',
        'data',
        'livro',
        'folha',
        'registro',
        'tags',
        'cep',
        'bairro',
        'logradouro',
        'localidade',
        'uf',
        'complemento',
        'latitude',
        'longitude',
    ];

    public function escrituras()
    {
        return $this->hasMany(Escritura::class, 'patrimonio_id');
    }

}
