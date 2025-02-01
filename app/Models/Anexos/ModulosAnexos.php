<?php

namespace App\Models\Anexos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModulosAnexos extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Os campos que podem ser atribuídos em massa.
     */
    protected $fillable = [
        'anexavel_id',
        'anexavel_type',
        'nome_arquivo',
        'caminho_arquivo',
        'tipo_arquivo',
        'extensao_arquivo',
        'mime_type',
        'tamanho_arquivo',
        'descricao',
        'status',
        'data_upload',
        'excluido_em',
        'comentarios',
        'tags',
        'updated_by',
        'updated_by_name',
        'created_by',
        'created_by_name',
    ];

    /**
     * O tipo de dados do campo `tags`.
     */
    protected $casts = [
        'tags' => 'array',
        'data_upload' => 'datetime',
        'excluido_em' => 'datetime',
    ];

    /**
     * Relacionamento polimórfico.
     *
     * Indica que este modelo pode pertencer a diferentes tipos de modelos.
     */
    public function anexavel()
    {
        return $this->morphTo();
    }

    /**
     * Verifica se o anexo está ativo.
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->status === 'ativo';
    }

    /**
     * Escopo para anexos ativos.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAtivos($query)
    {
        return $query->where('status', 'ativo');
    }

    /**
     * Escopo para anexos excluídos.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeExcluidos($query)
    {
        return $query->whereNotNull('excluido_em');
    }
}
