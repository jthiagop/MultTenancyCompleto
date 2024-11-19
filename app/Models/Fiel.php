<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fiel extends Model
{
    use HasFactory;

    protected $table = 'fieis';

    protected $fillable = [
        'company_id',
        'nome_completo',
        'data_nascimento',
        'sexo',
        'estado_civil',
        'profissao',
        'cpf',
        'rg',
        'telefone',
        'telefone_secundario',
        'email',
        'endereco',
        'bairro',
        'notifications',
        'cidade',
        'estado',
        'cep',
        'data_batismo',
        'local_batismo',
        'data_casamento',
        'local_casamento',
        'data_ingresso',
        'responsavel_ingresso',
        'grupo_participante',
        'ministerio',
        'dizimista',
        'valor_dizimo',
        'frequencia_dizimo',
        'ultima_contribuicao',
        'observacoes',
        'status',
        'created_by',
        'updated_by',
        'created_by_name',
        'updated_by_name',
        'avatar'
    ];
}
