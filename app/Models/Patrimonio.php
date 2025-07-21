<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patrimonio extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'codigo_rid',
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

    /**
     * Relacionamento com Escritura.
     * Um patrimônio pode ter várias escrituras.
     */
    public function escrituras()
    {
        return $this->hasMany(Escritura::class, 'patrimonio_id');
    }

    /**
     * Relacionamento com PatrimonioAnexo.
     * Um patrimônio pode ter vários anexos.
     */
    public function anexos()
    {
        return $this->hasMany(PatrimonioAnexo::class, 'patrimonio_id');
    }
}