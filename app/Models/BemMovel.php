<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BemMovel extends Model
{
    use HasFactory;

    protected $table = 'bens_moveis';

    protected $fillable = [
        'bem_id',
        'marca_modelo',
        'chapa_plaqueta',
        'garantia',
        'dados_adicionais',
    ];

    protected $casts = [
        'garantia' => 'date',
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
