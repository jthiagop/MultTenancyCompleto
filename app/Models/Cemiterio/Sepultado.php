<?php

namespace App\Models\Cemiterio;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sepultado extends Model
{
    use HasFactory, SoftDeletes;

    // Atributo para a data de exclusão
    protected $dates = ['deleted_at'];

    // Campos que podem ser atribuídos em massa
    protected $fillable = [
        'nome',
        'avatar',
        'data_nascimento',
        'data_falecimento',
        'documento_identificacao',
        'informacoes_atestado_obito',
        'familia_responsavel',
        'relacionamento',
        'sepultura_id',
        'data_sepultamento',
    ];

    /**
     * Relacionamento com a tabela Sepultura.
     */
        // Relacionamento com a Sepultura (um para muitos)
        public function sepultura()
        {
            return $this->belongsTo(Sepultura::class, 'sepultura_id'); // 'sepultura_id' é a chave estrangeira
        }
}
