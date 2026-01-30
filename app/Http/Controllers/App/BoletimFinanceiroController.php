<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
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
     * Gera PDF do Boletim Financeiro
     */
    public function gerarPdf(Request $request)
    {
        // 1) Filtros
        $dataInicial = $request->input('data_inicial');
        $dataFinal   = $request->input('data_final');
        
        // Converter datas do formato brasileiro para Y-m-d
        $dataInicialFormatted = Carbon::createFromFormat('d/m/Y', $dataInicial);
        $dataFinalFormatted = Carbon::createFromFormat('d/m/Y', $dataFinal);

        // 2) PRESTAÇÃO DE CONTAS - Lançamentos Agrupados por Código
        $lancamentosEntradas = TransacaoFinanceira::with('lancamentoPadrao')
            ->where('company_id', session('active_company_id'))
            ->whereBetween('data_competencia', [$dataInicialFormatted, $dataFinalFormatted])
            ->where('tipo', 'entrada') // Entrada
            ->get()
            ->groupBy('lancamento_padrao_id')
            ->map(function ($group) {
                $lancamento = $group->first()->lancamentoPadrao;
                return [
                    'codigo' => $lancamento->id ?? '-',
                    'descricao' => $lancamento->description ?? 'Sem descrição',
                    'valor' => $group->sum('valor')
                ];
            })
            ->sortBy('codigo')
            ->values();

        $lancamentosSaidas = TransacaoFinanceira::with('lancamentoPadrao')
            ->where('company_id', session('active_company_id'))
            ->whereBetween('data_competencia', [$dataInicialFormatted, $dataFinalFormatted])
            ->where('tipo', 'saida') // Saída
            ->get()
            ->groupBy('lancamento_padrao_id')
            ->map(function ($group) {
                $lancamento = $group->first()->lancamentoPadrao;
                return [
                    'codigo' => $lancamento->id ?? '-',
                    'descricao' => $lancamento->description ?? 'Sem descrição',
                    'valor' => $group->sum('valor')
                ];
            })
            ->sortBy('codigo')
            ->values();

        $totalEntradas = $lancamentosEntradas->sum('valor');
        $totalSaidas = $lancamentosSaidas->sum('valor');

        // 3) RESULTADO DAS CONTAS DE MOVIMENTO FINANCEIRO
        $entidades = EntidadeFinanceira::where('company_id', session('active_company_id'))->get();
        
        $contasMovimento = $entidades->map(function ($entidade) use ($dataInicialFormatted, $dataFinalFormatted) {
            // Buscar movimentações do período
            $entradas = TransacaoFinanceira::where('entidade_id', $entidade->id)
                ->whereBetween('data_competencia', [$dataInicialFormatted, $dataFinalFormatted])
                ->where('tipo', 'entrada')
                ->sum('valor');
            
            $saidas = TransacaoFinanceira::where('entidade_id', $entidade->id)
                ->whereBetween('data_competencia', [$dataInicialFormatted, $dataFinalFormatted])
                ->where('tipo', 'saida')
                ->sum('valor');
            
            // Calcular saldo anterior
            $saldoAnterior = $entidade->saldo_atual - ($entradas - $saidas);
            
            return [
                'conta' => $entidade->nome,
                'saldo_anterior' => $saldoAnterior,
                'entrada' => $entradas,
                'saida' => $saidas,
                'saldo_atual' => $entidade->saldo_atual
            ];
        });

        // 4) TOTAIS GERAIS
        $saldoAnteriorTotal = $contasMovimento->sum('saldo_anterior');
        $saldoAtualTotal = $contasMovimento->sum('saldo_atual');
        $deficit = $totalEntradas - $totalSaidas;

        // 5) Buscar company
        $company = Auth::user()->companies()->first();

        // 6) HTML da view
        $html = view('app.relatorios.financeiro.boletim_pdf', [
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
            'company'             => $company,
        ])->render();

        // 7) PDF
        $pdf = BrowsershotHelper::configureChromePath(
            Browsershot::html($html)
                ->format('A4')
                ->showBackground()
                ->margins(8, 8, 8, 8)
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
            // Extrair mês e ano das datas
            $dataInicial = $request->input('data_inicial');
            $dataInicialFormatted = Carbon::createFromFormat('d/m/Y', $dataInicial);
            
            $mes = $dataInicialFormatted->month;
            $ano = $dataInicialFormatted->year;
            $companyId = session('active_company_id');
            $tenantId = tenant('id');

            // Validar company_id
            if (!$companyId) {
                Log::warning('[BoletimFinanceiro] Company ID não encontrado na sessão');
                return response()->json([
                    'success' => false,
                    'message' => 'Empresa não selecionada. Por favor, selecione uma empresa.'
                ], 400);
            }

            Log::info('[BoletimFinanceiro] Iniciando geração async', [
                'company_id' => $companyId,
                'tenant_id' => $tenantId,
                'user_id' => Auth::id(),
                'mes' => $mes,
                'ano' => $ano,
            ]);

            // Criar registro de rastreamento
            $pdfGen = PdfGeneration::create([
                'type' => 'boletim',
                'user_id' => Auth::id(),
                'company_id' => $companyId,
                'status' => 'pending',
                'parameters' => [
                    'mes' => $mes,
                    'ano' => $ano,
                    'data_inicial' => $dataInicial,
                    'data_final' => $request->input('data_final'),
                ],
            ]);

            // Despachar job
            GenerateBoletimPdfJob::dispatch(
                $mes,
                $ano,
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
