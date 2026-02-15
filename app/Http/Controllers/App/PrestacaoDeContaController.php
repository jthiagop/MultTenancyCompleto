<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Financeiro\CostCenter;
use App\Models\Financeiro\TransacaoFinanceira;
use App\Services\PrestacaoContasService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use PDF;
use Spatie\Browsershot\Browsershot;
use App\Helpers\BrowsershotHelper;

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
        // 1) Filtros
        $dataInicial = $request->date('data_inicial');
        $dataFinal   = $request->date('data_final');
        $entidadeId  = $request->input('entidade_id');
        $modelo      = $request->input('modelo', 'horizontal');
        $tipoData    = $request->input('tipo_data', 'competencia'); // competencia ou pagamento
        $situacoes   = $request->input('situacoes', []);            // array de situacoes
        $categorias  = $request->input('categorias', []);           // array de lancamento_padrao_id
        $parceiroId        = $request->input('parceiro_id');              // filtro por parceiro
        $comprovacaoFiscal = $request->boolean('comprovacao_fiscal');     // somente com comprovacao fiscal
        $tipoValor         = $request->input('tipo_valor', 'previsto');  // previsto ou pago

        // Converter string separada por virgula em array (quando vem via query string)
        if (is_string($situacoes)) {
            $situacoes = array_filter(explode(',', $situacoes));
        }
        if (is_string($categorias)) {
            $categorias = array_filter(explode(',', $categorias));
        }

        // 2) Coluna de data a filtrar
        $colunaData = $tipoData === 'pagamento' ? 'data_pagamento' : 'data_competencia';

        // 3) Situacoes a excluir (sempre exclui parcelado + desconsiderado)
        $situacoesExcluidas = [
            \App\Enums\SituacaoTransacao::DESCONSIDERADO->value,
            \App\Enums\SituacaoTransacao::PARCELADO->value,
        ];

        // 4) Query otimizada - com seguranca tenant
        $query = TransacaoFinanceira::with(['entidadeFinanceira', 'lancamentoPadrao', 'parceiro'])
            ->forActiveCompany()
            ->whereNotIn('situacao', $situacoesExcluidas)
            ->where('agendado', false)
            ->when($dataInicial, fn($q) => $q->whereDate($colunaData, '>=', $dataInicial))
            ->when($dataFinal,   fn($q) => $q->whereDate($colunaData, '<=', $dataFinal))
            ->when($entidadeId,  fn($q) => $q->where('entidade_id', $entidadeId))
            ->when(!empty($situacoes),  fn($q) => $q->whereIn('situacao', $situacoes))
            ->when(!empty($categorias), fn($q) => $q->whereIn('lancamento_padrao_id', $categorias))
            ->when($parceiroId,        fn($q) => $q->where('parceiro_id', $parceiroId))
            ->when($comprovacaoFiscal, fn($q) => $q->where('comprovacao_fiscal', true))
            ->orderBy($colunaData);

        $transacoes = $query->get()
            ->groupBy('origem');

        // 3) Totais por origem + totais gerais
        $dados         = [];
        $totEntradaAll = $totSaidaAll = 0;
        $campoValor    = $tipoValor === 'pago' ? 'valor_pago' : 'valor';

        foreach ($transacoes as $origem => $items) {
            $totEntrada  = $items->where('tipo', 'entrada')->sum($campoValor);
            $totSaida    = $items->where('tipo', 'saida')->sum($campoValor);

            $totEntradaAll += $totEntrada;
            $totSaidaAll   += $totSaida;

            $dados[] = compact('origem', 'items', 'totEntrada', 'totSaida');
        }

        // 4) Dados do filtro para exibir no cabecalho do PDF
        $entidadeNome = $entidadeId
            ? optional(\App\Models\EntidadeFinanceira::find($entidadeId))->nome
            : null;

        // 5) HTML da view - respeita o modelo escolhido (horizontal/vertical)
        $isLandscape = $modelo === 'horizontal';

        $parceiroNome = $parceiroId
            ? optional(\App\Models\Parceiro::find($parceiroId))->nome
            : null;

        $html = view('app.relatorios.financeiro.prestacao_pdf', [
            'dados'              => $dados,
            'dataInicial'        => $dataInicial?->format('d/m/Y'),
            'dataFinal'          => $dataFinal?->format('d/m/Y'),
            'entidadeNome'       => $entidadeNome,
            'totalEntradas'      => $totEntradaAll,
            'totalSaidas'        => $totSaidaAll,
            'company'            => Auth::user()->companies()->first(),
            'tipoValor'          => $tipoValor,
            'parceiroNome'       => $parceiroNome,
            'comprovacaoFiscal'  => $comprovacaoFiscal,
        ])->render();

        // 6) PDF - respeita o modelo escolhido (horizontal/vertical)
        $pdf = BrowsershotHelper::configureChromePath(
            Browsershot::html($html)
                ->format('A4')
                ->landscape($isLandscape)
                ->showBackground()
                ->margins(8, 8, 8, 8)
        )->pdf();

        return response($pdf, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename=prestacao-de-contas.pdf',
        ]);
    }


    protected function logoToBase64($company): ?string
    {
        $path = $company->avatar
            ? storage_path('app/public/'.$company->avatar)
            : public_path('tenancy/assets/media/png/perfil.svg');

        return 'data:image/'.pathinfo($path, PATHINFO_EXTENSION).';base64,'.base64_encode(file_get_contents($path));
    }

    public function print(Request $request, $id)
    {
        // Obter o ID da empresa do usuário autenticado
        $companyId = session('active_company_id');

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
