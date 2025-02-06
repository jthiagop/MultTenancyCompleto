<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Financeiro\CostCenter;
use App\Models\Financeiro\TransacaoFinanceira;
use Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

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
        $costCenter  = $request->input('cost_center_id', 'name');

        // 2) Montar query de transações
        $query = TransacaoFinanceira::query();

        // Filtro por data (data_competencia)
        if ($dataInicial && $dataFinal) {
            $query->whereBetween('data_competencia', [$dataInicial, $dataFinal]);
        }

        // Filtro por centro de custo
        if ($costCenter) {
            $query->where('cost_center_id', $costCenter);
        }

        // Você pode incluir outros filtros que julgar necessário

        // 3) Obter os registros
        $transacoes = $query->orderBy('data_competencia', 'asc')->get();

        // 4) Processar/Organizar os dados (exemplo de agrupamento por conta/categoria)
        //    Aqui você adaptaria conforme a sua necessidade.
        $agrupadoPorConta = $transacoes->groupBy('tipo_documento'); // Exemplo: agrupar por tipo_documento

        // 5) Calcular saldos e outras informações
        //    Supondo que você vá somar entradas e saídas, você pode filtrar no collection
        //    Este é só um exemplo simples. Você pode criar estruturas mais elaboradas.

        $dadosParaView = [];
        foreach ($agrupadoPorConta as $contaDocumento => $items) {
            // Pega entradas
            $entradaTotal = $items->where('tipo', 'E')->sum('valor');

            // Pega saídas
            $saidaTotal = $items->where('tipo', 'S')->sum('valor');

            // Monta um array que contenha:
            // - nome da categoria
            // - saldo anterior (você pode ter que buscar isso antes do período?)
            // - lista de movimentações
            // - total de entradas, total de saídas

            $dadosParaView[] = [
                'tipo_documento' => $contaDocumento,
                'movimentacoes'  => $items,
                'total_entrada'  => $entradaTotal,
                'total_saida'    => $saidaTotal
            ];
        }

        // 6) Montar o PDF
        //    Ao final, retornamos a visualização para gerar o PDF
        //    Passamos $dadosParaView (ou outro nome) para a blade

        $pdf = Pdf::loadView('app.relatorios.financeiro.prestacao_pdf', [
            'dados'         => $dadosParaView,
            'dataInicial'   => $dataInicial,
            'dataFinal'     => $dataFinal,
            'costCenter'    => $costCenter,
        ]);

        // Retorna para visualização no navegador:
        // return $pdf->stream('relatorio-prestacao-de-contas.pdf');

        // Ou força o download:
        return $pdf->stream('relatorio-prestacao-de-contas.pdf');

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
