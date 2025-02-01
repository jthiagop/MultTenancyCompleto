<?php

namespace App\Models\Financeiro;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModulosAnexo extends Model
{
    use SoftDeletes;

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
        'uploaded_by',
        'status',
        'data_upload',
        'excluido_em',
        'comentarios',
        'tags',
        'created_by',
        'created_by_name',
        'updated_by',
        'updated_by_name',
    ];

    // Relacionamento Polimórfico
    public function anexavel()
    {
        return $this->morphTo();
    }

    // Relacionamento com o usuário que fez o upload
    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
