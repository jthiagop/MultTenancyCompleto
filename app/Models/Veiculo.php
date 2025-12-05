<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Veiculo extends Model
{
    use HasFactory;

    protected $fillable = [
        'bem_id',
        'placa',
        'renavam',
        'chassi',
        'combustivel',
        'ano_modelo',
        'ano_fabricacao',
        'cor',
        'crlv',
        'dados_adicionais',
    ];

    protected $casts = [
        'ano_modelo' => 'integer',
        'ano_fabricacao' => 'integer',
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
