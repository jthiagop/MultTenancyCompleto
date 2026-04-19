<?php

namespace App\Models\Financeiro;

use App\Models\Company;
use App\Models\LancamentoPadrao;
use Illuminate\Database\Eloquent\Model;

class TransacaoRateio extends Model
{
    protected $table = 'transacoes_rateios';

    protected $fillable = [
        'transacao_financeira_id',
        'filial_id',
        'centro_custo_id',
        'lancamento_padrao_id',
        'valor',
        'percentual',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'percentual' => 'decimal:2',
    ];

    public function transacaoFinanceira()
    {
        return $this->belongsTo(TransacaoFinanceira::class, 'transacao_financeira_id');
    }

    public function filial()
    {
        return $this->belongsTo(Company::class, 'filial_id');
    }

    public function centroCusto()
    {
        return $this->belongsTo(CostCenter::class, 'centro_custo_id');
    }

    public function lancamentoPadrao()
    {
        return $this->belongsTo(LancamentoPadrao::class, 'lancamento_padrao_id');
    }
}
