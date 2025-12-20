<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TelaDeLogin extends Model
{
    use HasFactory;

    protected $table = 'tela_de_login'; // Certifique-se de que o nome da tabela está correto

    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array
     */
    protected $fillable = [
        'imagem_caminho',
        'descricao',
        'localidade',
        'data_upload',
        'upload_usuario_id',
        'status',
        'tags',
        'updated_by',
    ];

    /**
     * Os atributos que devem ser convertidos para tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'data_upload' => 'date',
        'tags' => 'array',
    ];

    /**
     * Relacionamento com o usuário que fez o upload.
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'upload_usuario_id');
    }

    /**
     * Relacionamento com o usuário que atualizou o registro.
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope para buscar apenas imagens ativas
     */
    public function scopeAtivas($query)
    {
        return $query->where('status', 'ativo');
    }

    /**
     * Accessor para URL completa da imagem
     */
    public function getImagemUrlAttribute()
    {
        return asset('storage/' . $this->imagem_caminho);
    }
}
