<?php

namespace App\Models\Financeiro;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransacaoFracionamento extends Model
{
    use HasFactory;

    protected $table = 'transacao_fracionamentos';

    protected $fillable = [
        'transacao_principal_id',
        'tipo',
        'valor',
        'data_pagamento',
        'juros',
        'multa',
        'desconto',
        'valor_total',
        'forma_pagamento',
        'conta_pagamento',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'juros' => 'decimal:2',
        'multa' => 'decimal:2',
        'desconto' => 'decimal:2',
        'valor_total' => 'decimal:2',
        'data_pagamento' => 'date',
    ];

    /**
     * Relacionamento: Transação principal
     */
    public function transacaoPrincipal()
    {
        return $this->belongsTo(TransacaoFinanceira::class, 'transacao_principal_id');
    }
}
