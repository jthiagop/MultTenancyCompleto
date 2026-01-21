<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use App\Models\EntidadeFinanceira;
use App\Models\FormasPagamento;
use App\Models\LancamentoPadrao;
use App\Models\Financeiro\CostCenter;
use App\Models\Financeiro\TransacaoFinanceira;
use Illuminate\Http\Request;

class FinanceiroController extends Controller
{
    /**
     * Buscar entidades financeiras (caixa e banco) da empresa ativa
     */
    public static function getEntidadesFinanceiras()
    {
        $activeCompanyId = session('active_company_id');
        
        if (!$activeCompanyId) {
            return collect([]);
        }
        
        return EntidadeFinanceira::whereIn('tipo', ['caixa', 'banco'])
            ->where('company_id', $activeCompanyId)
            ->orderBy('tipo')
            ->orderBy('nome')
            ->get();
    }
    
    /**
     * Buscar formas de pagamento ativas
     */
    public static function getFormasPagamento()
    {
        return FormasPagamento::where('ativo', true)
            ->orderBy('nome')
            ->get();
    }
    
    /**
     * Buscar lançamentos padrão da empresa ativa
     */
    public static function getLancamentosPadrao()
    {
        return LancamentoPadrao::forActiveCompany()
            ->orderBy('description')
            ->get();
    }
    
    /**
     * Buscar centros de custo ativos da empresa ativa
     */
    public static function getCentrosCusto()
    {
        return CostCenter::forActiveCompany()
            ->where('status', true)
            ->orderBy('name')
            ->get();
    }
    
    /**
     * Buscar transações financeiras da empresa ativa
     */
    public static function getTransacoesFinanceiras()
    {
        return TransacaoFinanceira::forActiveCompany()
            ->with(['entidadeFinanceira', 'lancamentoPadrao', 'costCenter'])
            ->orderBy('data_competencia', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }
}

