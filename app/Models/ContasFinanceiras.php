<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContasFinanceiras extends Model
{
    protected $fillable = [
        'company_id',
        'data_competencia',
        'descricao',
        'valor',
        'tipo_financeiro',
        'cost_centers_id',
        'lancamento_padraos_id',
        'repetir',
        'intervalo_repeticao',
        'frequencia',
        'parcelamento',
        'data_primeiro_vencimento',
        'forma_pagamento_id',
        'entidade_financeira_id',
        'observacoes',
        'valor_pago',
        'juros',
        'multa',
        'desconto',
        'status_pagamento',
        'created_by',
        'created_by_name',
        'updated_by',
        'updated_by_name',
    ];

}
