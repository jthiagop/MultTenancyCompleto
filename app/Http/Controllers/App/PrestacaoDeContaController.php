<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Financeiro\CostCenter;
use App\Models\Financeiro\TransacaoFinanceira;
use Auth;
use Illuminate\Http\Request;
use PDF;

class PrestacaoDeContaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Se você quiser passar lista de centros de custo para o select:
        // $centrosDeCusto = CostCenter::all();
        // return view('app.relatorios.financeiro.index', compact('centrosDeCusto'));
        $centrosAtivos = CostCenter::getCadastroCentroCusto();
        // Recupera a empresa do usuário logado
        $company = Auth::user()->companies()->first(); // Ou qualquer método que obtenha a empresa

        return view('app.relatorios.financeiro.index', [
            'centrosAtivos' => $centrosAtivos,
            'company' => $company,
        ]);
    }

    public function gerarPdf(Request $request)
    {
        // 1) Capturar filtros
        $dataInicial = $request->input('data_inicial');
        $dataFinal   = $request->input('data_final');
        $costCenter  = $request->input('cost_center_id');

        // 2) Montar query de transações
        $query = TransacaoFinanceira::query();

        // Filtro por data inicial e final
        if ($dataInicial) {
            $query->where('data_competencia', '>=', $dataInicial);
        }
        if ($dataFinal) {
            $query->where('data_competencia', '<=', $dataFinal);
        }

        // Filtro por centro de custo
        if ($costCenter) {
            $query->where('cost_center_id', $costCenter);
        }

        // 3) Obter os registros e agrupar por 'origem'
        $transacoes = $query->orderBy('data_competencia', 'asc')->get();
        $agrupadoPorOrigem = $transacoes->groupBy('origem');
        // Ex.: dois grupos: "Banco" e "Caixa"

        // 4) Calcular as somas por cada origem
        //    e também podemos calcular um total geral de entradas/saídas
        $dadosParaView = [];
        $totalGeralEntrada = 0;
        $totalGeralSaida   = 0;

        foreach ($agrupadoPorOrigem as $origem => $items) {
            // Somar entradas (tipo = 'E')
            $entradaTotal = $items->where('tipo', 'entrada')->sum('valor');

            // Somar saídas (tipo = 'S')
            $saidaTotal   = $items->where('tipo', 'saida')->sum('valor');

            // Atualiza total geral
            $totalGeralEntrada += $entradaTotal;
            $totalGeralSaida   += $saidaTotal;

            // Guardar no array de resultados por origem
            $dadosParaView[] = [
                // 'origem' => 'Banco' ou 'Caixa'
                'origem'         => $origem,
                'movimentacoes'  => $items,          // todos os registros deste grupo
                'total_entrada'  => $entradaTotal,
                'total_saida'    => $saidaTotal,
            ];
        }

        // 5) Montar o PDF
        $pdf = PDF::loadView('app.relatorios.financeiro.prestacao_pdf', [
            'dados'            => $dadosParaView,
            'dataInicial'      => $dataInicial,
            'dataFinal'        => $dataFinal,
            'costCenter'       => $costCenter,
            // Passamos também os totais gerais
            'totalGeralEntrada' => $totalGeralEntrada,
            'totalGeralSaida'  => $totalGeralSaida,
        ]);

        return $pdf->stream('relatorio-prestacao-de-contas.pdf');
    }

    public function print(Request $request, $id)
    {
        // Obter o ID da empresa do usuário autenticado
        $companyId = Auth::user()->company_id;

        // Buscar o banco com o ID e verificar se pertence à empresa do usuário
        $caixa = TransacaoFinanceira::with('modulos_anexos')
            ->where('company_id', $companyId) // Filtrar pelo company_id do usuário
            ->findOrFail($id);

            dd($caixa);
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
        //
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
