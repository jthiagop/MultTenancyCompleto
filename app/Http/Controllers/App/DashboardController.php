<?php

namespace App\Http\Controllers\App;

use App\Helpers\DateHelper;
use App\Http\Controllers\Controller;
use App\Models\TenantFilial;
use App\Models\User;
use Auth;
use Flasher\Laravel\Facade\Flasher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $dateHelper = new DateHelper();

        // Recupere o usuário logado
        $user = Auth::user();


        // Define o ano selecionado, com o ano atual como padrão
        $selectedYear = $request->input('year', now()->year);

        // Recupera a empresa do usuário logado
        $company = $user->companies()->first(); // Ou qualquer método que obtenha a empresa

        // Define as categorias de lançamento que queremos filtrar
        $categoriasLancamento = ['Doações', 'Coletas', 'Intenções'];

        // Consulta para lançamentos na tabela caixas
        $lancamentosCaixas = DB::table('caixas')
            ->join('lancamento_padraos', 'caixas.lancamento_padrao_id', '=', 'lancamento_padraos.id')
            ->where('caixas.company_id', $company->company_id)
            ->whereIn('lancamento_padraos.category', $categoriasLancamento)
            ->whereYear('caixas.data_competencia', $selectedYear)
            ->select(
                DB::raw('MONTH(caixas.data_competencia) as mes'),
                'lancamento_padraos.category as lancamento_padrao_id',
                DB::raw('SUM(caixas.valor) as total_valor')
            )
            ->groupBy('mes', 'lancamento_padrao_id');

        // Consulta para lançamentos na tabela bancos
        $lancamentosBancos = DB::table('bancos')
            ->join('lancamento_padraos', 'bancos.lancamento_padrao_id', '=', 'lancamento_padraos.id')
            ->where('bancos.company_id', $company->company_id)
            ->whereIn('lancamento_padraos.category', $categoriasLancamento)
            ->whereYear('bancos.data_competencia', $selectedYear)
            ->select(
                DB::raw('MONTH(bancos.data_competencia) as mes'),
                'lancamento_padraos.category as lancamento_padrao_id',
                DB::raw('SUM(bancos.valor) as total_valor')
            )
            ->groupBy('mes', 'lancamento_padrao_id');

        // Combina os resultados das duas consultas com union
        $lancamentos = $lancamentosCaixas->union($lancamentosBancos)
            ->orderBy('mes')
            ->get();


        // Organiza os dados para o gráfico
        $areaChartData = [
            'series' => [
                ['name' => 'Doações', 'data' => []],
                ['name' => 'Coletas', 'data' => []],
                ['name' => 'Intenções', 'data' => []]
            ],
            'categories' => []
        ];
        // Definir meses do ano
        $meses = ['Jan', 'Feb', 'Mar', 'Apr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];

        foreach ($meses as $index => $mes) {
            $mesNumero = $index + 1;

            // Adiciona o nome do mês na categoria
            $areaChartData['categories'][] = $mes;

            // Filtra lançamentos por mês e tipo
            foreach (['Doações', 'Coletas', 'Intenções'] as $i => $tipo) {
                $valor = $lancamentos
                    ->where('mes', $mesNumero)
                    ->where('lancamento_padrao_id', $tipo)
                    ->sum('total_valor'); // Calcula o total para o mês e tipo
                $areaChartData['series'][$i]['data'][] = $valor ?: 0; // Insere 0 se não houver valor
            }
        }
        // Retorna para a view
        return view('app.dashboard', [
            'company' => $company,
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
