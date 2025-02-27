<?php

namespace App\Models\Financeiro;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankStatementTransacao extends Model
{
    use HasFactory;

    protected $table = 'bank_statement_transacao';

    protected $fillable = [
        'bank_statement_id',
        'transacao_financeira_id',
        'valor_conciliado',
        'status_conciliacao',
    ];

    public $timestamps = true; // Mantém created_at e updated_at automáticos.

    // Relacionamento com BankStatement
    public function bankStatement()
    {
        return $this->belongsTo(BankStatement::class);
    }

    // Relacionamento com TransacaoFinanceira
    public function transacaoFinanceira()
    {
        return $this->belongsTo(TransacaoFinanceira::class, 'transacao_financeira_id');
    }
}
