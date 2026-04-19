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
        'company_id',
        'sepultura_id',
        'nome',
        'cpf',
        'avatar',
        'data_nascimento',
        'data_falecimento',
        'data_sepultamento',
        'causa_mortis',
        'tumulo_codigo',
        'observacoes',
        'documento_identificacao',
        'informacoes_atestado_obito',
        'familia_responsavel',
        'relacionamento',
        'livro_sepultamento',
        'folha_sepultamento',
        'numero_sepultamento',
        'imagens',
    ];

    protected $casts = [
        'imagens'           => 'array',
        'data_nascimento'   => 'datetime',
        'data_falecimento'  => 'datetime',
        'data_sepultamento' => 'datetime',
    ];

    /**
     * Relacionamento com a tabela Sepultura.
     */
    public function sepultura()
    {
        return $this->belongsTo(Sepultura::class, 'sepultura_id');
    }

    public function responsaveis()
    {
        return $this->hasMany(SepultadoResponsavel::class);
    }
}
