<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profissao extends Model
{
    protected $table = 'profissoes';
    
    protected $fillable = [
        'nome',
        'descricao',
        'popularidade',
        'ativo'
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'popularidade' => 'integer'
    ];

    /**
     * Relacionamento com Fieis
     */
    public function fieis()
    {
        return $this->hasMany(Fiel::class, 'profissao_id');
    }
}
