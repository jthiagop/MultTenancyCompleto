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

    public function showLoginForm()
    {
        // Recupere a última imagem de fundo ativa, ou a imagem padrão se não houver nenhuma.
        $backgroundImage = TelaDeLogin::where('status', 'ativo')->latest()->value('imagem_caminho');


        return view('auth.login', compact('backgroundImage'));
    }
}
