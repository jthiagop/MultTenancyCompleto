<?php

namespace App\Http\Controllers\Financeiro;

use App\Http\Controllers\Controller;
use App\Models\EntidadeFinanceira;
use Illuminate\Http\Request;


class FinanceiroController extends Controller
{
    /**
     * Display the financial module dashboard
     */
    public function index()
    {
        $activeCompanyId = session('active_company_id');
        
        // Buscar entidades financeiras da empresa ativa
        $entidadesFinanceiras = [];
        if ($activeCompanyId) {
            $entidadesFinanceiras = EntidadeFinanceira::where('company_id', $activeCompanyId)
                ->orderBy('nome')
                ->get()
                ->map(function ($entidade) {
                    return [
                        'id' => $entidade->id,
                        'nome' => $entidade->nome,
                        'tipo' => $entidade->tipo,
                    ];
                })
                ->toArray();
        }
        
        return view('app.financeiro.index', [
            'stats' => [
                'saldoTotal' => 125450.75,
                'receitasMes' => 45230.00,
                'despesasMes' => 28540.50,
                'transacoesRecentes' => 156
            ],
            'entidadesFinanceiras' => $entidadesFinanceiras
        ]);
    }
}

