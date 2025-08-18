<?php

namespace App\Models\Contabilide;

use App\Models\LancamentoPadrao;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountMapping extends Model
{
    use HasFactory;

    protected $table = 'account_mappings';

    protected $fillable = [
        'company_id',
        'lancamento_padrao_id',
        'conta_debito_id',
        'conta_credito_id',
    ];

    /**
     * Relação: O Lançamento Padrão que está sendo mapeado ("DE").
     */
    public function lancamentoPadrao()
    {
        return $this->belongsTo(LancamentoPadrao::class, 'lancamento_padrao_id');
    }

    /**
     * Relação: A conta contábil de Débito ("PARA").
     */
    public function contaDebito()
    {
        return $this->belongsTo(ChartOfAccount::class, 'conta_debito_id');
    }

    /**
     * Relação: A conta contábil de Crédito ("PARA").
     */
    public function contaCredito()
    {
        return $this->belongsTo(ChartOfAccount::class, 'conta_credito_id');
    }

    /**
     * Scope: Filtra a busca para incluir apenas os registros da empresa ativa na sessão.
     */
    public function scopeForActiveCompany($query)
    {
        $activeCompanyId = session('active_company_id');

        if ($activeCompanyId) {
            return $query->where('company_id', $activeCompanyId);
        }

        return $query->whereRaw('1 = 0');
    }
}
