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
        $costCenter  = $request->input('cost_center_id');

        // 2) Query otimizada
        $query = TransacaoFinanceira::with(['entidadeFinanceira', 'lancamentoPadrao'])
            ->when($dataInicial, fn($q) => $q->whereDate('data_competencia', '>=', $dataInicial))
            ->when($dataFinal,   fn($q) => $q->whereDate('data_competencia', '<=', $dataFinal))
            ->when($costCenter,  fn($q) => $q->where('cost_center_id', $costCenter))
            ->orderBy('data_competencia');

        $transacoes = $query->get()
            ->groupBy('origem');               // “Banco”, “Caixa” …

        // 3) Totais por origem + totais gerais
        $dados         = [];
        $totEntradaAll = $totSaidaAll = 0;

        foreach ($transacoes as $origem => $items) {
            $totEntrada  = $items->where('tipo', 'entrada')->sum('valor');
            $totSaida    = $items->where('tipo', 'saida')->sum('valor');

            $totEntradaAll += $totEntrada;
            $totSaidaAll   += $totSaida;

            $dados[] = compact('origem', 'items', 'totEntrada', 'totSaida');
        }

        // 4) HTML da view
        $html = view('app.relatorios.financeiro.prestacao_pdf', [
            'dados'           => $dados,
            'dataInicial'     => $dataInicial?->format('d/m/Y'),
            'dataFinal'       => $dataFinal?->format('d/m/Y'),
            'costCenter'      => optional(CostCenter::find($costCenter))->descricao,
            'totalEntradas'   => $totEntradaAll,
            'totalSaidas'     => $totSaidaAll,
            'company'         => Auth::user()->companies()->first(),   // ajuste conforme seu tenant
        ])->render();

        // 5) PDF
        $pdf = BrowsershotHelper::configureChromePath(
            Browsershot::html($html)
                ->format('A4')
                ->landscape()
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
