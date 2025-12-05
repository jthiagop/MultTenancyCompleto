<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Imovel extends Model
{
    use HasFactory;

    protected $fillable = [
        'bem_id',
        'inscricao_municipal',
        'cep',
        'endereco',
        'bairro',
        'cidade',
        'uf',
        'matricula',
        'cartorio',
        'livro',
        'folha',
        'area_total',
        'area_privativa',
        'dados_adicionais',
    ];

    protected $casts = [
        'area_total' => 'decimal:2',
        'area_privativa' => 'decimal:2',
        'dados_adicionais' => 'array',
    ];

    /**
     * Relacionamento com Bem (N:1)
     */
    public function bem()
    {
        return $this->belongsTo(Bem::class, 'bem_id');
    }
}
