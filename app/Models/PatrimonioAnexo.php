<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatrimonioAnexo extends Model
{
    use HasFactory;

    protected $fillable = [
        'patrimonio_id',
        'nome_arquivo',
        'caminho_arquivo',
        'tipo_arquivo',
        'tamanho_arquivo',
        'descricao',
        'uploaded_by',
    ];

    /**
     * Relacionamento com Patrimonio.
     * Cada anexo pertence a um único patrimônio.
     */
    public function patrimonio()
    {
        return $this->belongsTo(Patrimonio::class);
    }

    /**
     * Relacionamento com User.
     * Cada anexo foi feito por um usuário (opcional).
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
