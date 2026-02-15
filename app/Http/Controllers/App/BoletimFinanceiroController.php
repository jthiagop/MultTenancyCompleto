<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Enums\SituacaoTransacao;
use App\Models\EntidadeFinanceira;
use App\Models\Financeiro\TransacaoFinanceira;
use App\Models\LancamentoPadrao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Browsershot\Browsershot;
use App\Helpers\BrowsershotHelper;
use Carbon\Carbon;
use App\Jobs\GenerateBoletimPdfJob;
use App\Models\PdfGeneration;
use Illuminate\Support\Facades\Log;

class BoletimFinanceiroController extends Controller
{
    /**
     * Situacoes que devem ser ignoradas no boletim.
     */
    private const SITUACOES_EXCLUIDAS = [
        SituacaoTransacao::DESCONSIDERADO,
        SituacaoTransacao::PARCELADO,
    ];

    /**
     * Retorna a query-base filtrada por empresa ativa, excluindo
     * transacoes desconsideradas, parceladas e agendadas.
     */
    private function baseQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return TransacaoFinanceira::forActiveCompany()
            ->whereNotIn('situacao', self::SITUACOES_EXCLUIDAS)
            ->where('agendado', false);
    }

    /**
     * Gera PDF do Boletim Financeiro
     */
    public function gerarPdf(Request $request)
    {
        // 1) Filtros — validar formato de data
        $dataInicial = $request->input('data_inicial');
        $dataFinal   = $request->input('data_final');

        try {
            $dataInicialFormatted = Carbon::createFromFormat('d/m/Y', $dataInicial)->startOfDay();
            $dataFinalFormatted   = Carbon::createFromFormat('d/m/Y', $dataFinal)->endOfDay();
        } catch (\Exception $e) {
            abort(422, 'Formato de data invalido. Use dd/mm/aaaa.');
        }

        // 2) Buscar todas as transacoes do periodo em uma unica query
        $transacoes = $this->baseQuery()
            ->whereBetween('data_competencia', [$dataInicialFormatted, $dataFinalFormatted])
            ->with('lancamentoPadrao')
            ->get();

        // Agrupar por lancamento padrao
        $lancamentosEntradas = $transacoes->where('tipo', 'entrada')
            ->groupBy('lancamento_padrao_id')
            ->map(function ($group) {
                $lp = $group->first()->lancamentoPadrao;
                return [
                    'codigo'    => $lp?->id ?? '-',
                    'descricao' => $lp?->description ?? 'Sem descricao',
                    'valor'     => $group->sum('valor'),
                ];
            })
            ->sortBy('codigo')
            ->values();

        $lancamentosSaidas = $transacoes->where('tipo', 'saida')
            ->groupBy('lancamento_padrao_id')
            ->map(function ($group) {
                $lp = $group->first()->lancamentoPadrao;
                return [
                    'codigo'    => $lp?->id ?? '-',
                    'descricao' => $lp?->description ?? 'Sem descricao',
                    'valor'     => $group->sum('valor'),
                ];
            })
            ->sortBy('codigo')
            ->values();

        $totalEntradas = $lancamentosEntradas->sum('valor');
        $totalSaidas   = $lancamentosSaidas->sum('valor');

        // 3) RESULTADO DAS CONTAS DE MOVIMENTO FINANCEIRO
        //    Uma unica query agrupada substitui o N+1
        $entidades = EntidadeFinanceira::forActiveCompany()->get();
        $entidadeIds = $entidades->pluck('id');

        // Movimentacoes ANTES do periodo (para saldo anterior)
        $movAntes = TransacaoFinanceira::forActiveCompany()
            ->whereNotIn('situacao', self::SITUACOES_EXCLUIDAS)
            ->where('agendado', false)
            ->whereIn('entidade_id', $entidadeIds)
            ->where('data_competencia', '<', $dataInicialFormatted)
            ->groupBy('entidade_id', 'tipo')
            ->select('entidade_id', 'tipo', DB::raw('SUM(valor) as total'))
            ->get()
            ->groupBy('entidade_id');

        // Movimentacoes NO periodo (usar colecao ja carregada)
        $movPeriodo = $transacoes->groupBy('entidade_id');

        $contasMovimento = $entidades->map(function ($entidade) use ($movAntes, $movPeriodo) {
            $saldoInicial = $entidade->saldo_inicial ?? 0;

            // Saldo anterior = saldo_inicial + entradas_antes - saidas_antes
            $antes = $movAntes->get($entidade->id, collect());
            $entradasAntes = $antes->where('tipo', 'entrada')->sum('total');
            $saidasAntes   = $antes->where('tipo', 'saida')->sum('total');
            $saldoAnterior = $saldoInicial + $entradasAntes - $saidasAntes;

            // Entradas/saidas no periodo
            $periodo         = $movPeriodo->get($entidade->id, collect());
            $entradasPeriodo = $periodo->where('tipo', 'entrada')->sum('valor');
            $saidasPeriodo   = $periodo->where('tipo', 'saida')->sum('valor');
            $saldoAtual      = $saldoAnterior + $entradasPeriodo - $saidasPeriodo;

            return [
                'conta'          => $entidade->nome,
                'saldo_anterior' => $saldoAnterior,
                'entrada'        => $entradasPeriodo,
                'saida'          => $saidasPeriodo,
                'saldo_atual'    => $saldoAtual,
            ];
        });

        // 4) TOTAIS GERAIS
        $saldoAnteriorTotal = $contasMovimento->sum('saldo_anterior');
        $saldoAtualTotal    = $contasMovimento->sum('saldo_atual');
        $deficit             = $totalEntradas - $totalSaidas;

        // 5) Buscar company da sessao ativa
        $company = Auth::user()
            ->companies()
            ->with('addresses')
            ->where('companies.id', session('active_company_id'))
            ->first();

        // 6) HTML da view
        $html = view('app.relatorios.financeiro.boletim_pdf', [
            'empresaRelatorio'    => $company,
            'nomeEmpresa'         => $company->name,
            'razaoSocial'         => $company->razao_social,
            'cnpjEmpresa'         => $company->cnpj,
            'avatarEmpresa'       => $company->avatar,
            'enderecoEmpresa'     => $company->addresses,
            'lancamentosEntradas' => $lancamentosEntradas,
            'lancamentosSaidas'   => $lancamentosSaidas,
            'totalEntradas'       => $totalEntradas,
            'totalSaidas'         => $totalSaidas,
            'contasMovimento'     => $contasMovimento,
            'saldoAnteriorTotal'  => $saldoAnteriorTotal,
            'saldoAtualTotal'     => $saldoAtualTotal,
            'deficit'             => $deficit,
            'dataInicial'         => $dataInicial,
            'dataFinal'           => $dataFinal,
        ])->render();

        // 7) PDF
        $pdf = BrowsershotHelper::configureChromePath(
            Browsershot::html($html)
                ->format('A4')
                ->showBackground()
                ->margins(8, 8, 15, 8)
                ->waitUntilNetworkIdle()
        )->pdf();

        return response($pdf, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename=boletim-financeiro.pdf',
        ]);
    }

    /**
     * Gera PDF de forma assíncrona (não trava o servidor)
     */
    public function gerarPdfAsync(Request $request)
    {
        try {
            $dataInicial = $request->input('data_inicial');
            $dataFinal = $request->input('data_final');
            $companyId = session('active_company_id');
            $tenantId = tenant('id');

            // Validar company_id
            if (!$companyId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Empresa não selecionada. Por favor, selecione uma empresa.'
                ], 400);
            }

            // Criar registro de rastreamento
            $pdfGen = PdfGeneration::create([
                'type' => 'boletim',
                'user_id' => Auth::id(),
                'company_id' => $companyId,
                'status' => 'pending',
                'parameters' => [
                    'data_inicial' => $dataInicial,
                    'data_final' => $dataFinal,
                ],
            ]);

            // Despachar job
            GenerateBoletimPdfJob::dispatch(
                $dataInicial,
                $dataFinal,
                $companyId,
                Auth::id(),
                $tenantId,
                $pdfGen->id
            );

            return response()->json([
                'success' => true,
                'pdf_id' => $pdfGen->id,
                'message' => 'PDF sendo gerado em background. Aguarde...'
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao despachar job de PDF Boletim', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao iniciar geração de PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verifica status de geração de PDF
     */
    public function checkPdfStatus($id)
    {
        try {
            $pdfGen = PdfGeneration::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'status' => $pdfGen->status,
                'download_url' => $pdfGen->download_url,
                'error_message' => $pdfGen->error_message,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'PDF não encontrado'
            ], 404);
        }
    }
}
