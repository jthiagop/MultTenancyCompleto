<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class BankConfig extends Model
{
    protected $table = 'bank_configs';

    protected $fillable = [
        'banco_codigo',
        'nome_conta',
        'agencia',
        'conta_corrente',
        'client_id',
        'client_secret',
        'developer_app_key',
        'mci_teste',
        'convenio',
        'carteira',
        'variacao',
        'ambiente',
        'ativo'
    ];

    // ISSO É MÁGICO: O Laravel gerencia a criptografia sozinho aqui.
    // Você não precisa usar Crypt::encrypt no controller.
    protected $casts = [
        'client_id' => 'encrypted',
        'client_secret' => 'encrypted',
        'developer_app_key' => 'encrypted',
        'ativo' => 'boolean',
    ];
}
