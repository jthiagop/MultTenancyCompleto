<?php

namespace App\Http\Controllers\App;

use App\Helpers\DateHelper;
use App\Http\Controllers\Controller;
use App\Models\Financeiro\TransacaoFinanceira;
use App\Models\TenantFilial;
use App\Models\User;
use Flasher\Laravel\Facade\Flasher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $selectedYear = $request->input('year', now()->year);

        // Pega a empresa ativa da sessão
        $activeCompanyId = session('active_company_id');
        $company = $user->companies()->find($activeCompanyId);

        // Fallback se nenhuma empresa estiver na sessão
        if (!$company && $user->companies()->exists()) {
            $company = $user->companies()->first();
            session(['active_company_id' => $company->id]);
            $activeCompanyId = $company->id;
        }

        if (!$company) {
            abort(403, 'Nenhuma empresa associada a este usuário.');
        }

        // Busca TODAS as transações financeiras (entradas e saídas) para o ano selecionado
        $lancamentos = TransacaoFinanceira::where('company_id', $activeCompanyId)
            ->whereIn('tipo', ['entrada', 'saida']) // Apenas entradas e saídas
            ->whereYear('data_competencia', $selectedYear)
            ->select(
                DB::raw('MONTH(data_competencia) as mes'),
                'tipo',
                DB::raw('SUM(valor) as total_valor')
            )
            ->groupBy('mes', 'tipo')
            ->get();

        // Prepara os dados para o gráfico
        $series = [
            'Entradas' => array_fill(0, 12, 0), // Inicializa um array com 12 zeros
            'Saídas'   => array_fill(0, 12, 0), // Inicializa um array com 12 zeros
        ];

        foreach ($lancamentos as $lancamento) {
            $mesIndex = $lancamento->mes - 1; // Ajusta o mês (1-12) para o índice do array (0-11)
            if ($lancamento->tipo === 'entrada') {
                $series['Entradas'][$mesIndex] = (float) $lancamento->total_valor;
            } elseif ($lancamento->tipo === 'saida') {
                $series['Saídas'][$mesIndex] = (float) $lancamento->total_valor;
            }
        }

        // Formata os dados no padrão que o ApexCharts espera
        $areaChartData = [
            'series' => [
                ['name' => 'Entradas', 'data' => $series['Entradas']],
                ['name' => 'Saídas', 'data' => $series['Saídas']],
            ],
            'categories' => ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez']
        ];

        // Retorna para a view
        return view('app.dashboard', [
            'areaChartData' => $areaChartData,
            'selectedYear' => $selectedYear
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Obter o usuário pelo ID
        $user = User::findOrFail($id);

        // Carregar a empresa relacionada
        $company = $user->company;

        // Retornar a visão com os dados do usuário e da empresa
        return view('app.dashboard', compact('user', 'company'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
