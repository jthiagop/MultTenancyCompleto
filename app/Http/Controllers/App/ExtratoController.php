<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Enums\SituacaoTransacao;
use App\Models\EntidadeFinanceira;
use App\Models\Financeiro\TransacaoFinanceira;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Browsershot\Browsershot;
use App\Helpers\BrowsershotHelper;
use Carbon\Carbon;
use App\Models\PdfGeneration;
use Illuminate\Support\Facades\Log;

class ExtratoController extends Controller
{
    /**
     * Situações que devem ser ignoradas no extrato.
     */
    private const SITUACOES_EXCLUIDAS = [
        SituacaoTransacao::DESCONSIDERADO,
        SituacaoTransacao::PARCELADO,
    ];

    /**
     * Retorna a query-base filtrada por empresa ativa, excluindo
     * transações desconsideradas, parceladas e agendadas.
     */
    private function baseQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return TransacaoFinanceira::forActiveCompany()
            ->whereNotIn('situacao', self::SITUACOES_EXCLUIDAS)
            ->where('agendado', false);
    }

    /**
     * Gera PDF do Extrato Financeiro (síncrono)
     */
    public function gerarPdf(Request $request)
    {
        // 1) Filtros — validar formato de data
        $dataInicial = $request->input('data_inicial');
        $dataFinal   = $request->input('data_final');
        $entidadeId  = $request->input('entidade_id');

        try {
            $dataInicialFormatted = Carbon::createFromFormat('d/m/Y', $dataInicial)->startOfDay();
            $dataFinalFormatted   = Carbon::createFromFormat('d/m/Y', $dataFinal)->endOfDay();
        } catch (\Exception $e) {
            abort(422, 'Formato de data inválido. Use dd/mm/aaaa.');
        }

        if (!$entidadeId) {
            abort(422, 'Selecione uma conta financeira.');
        }

        // 2) Buscar a entidade financeira
        $entidade = EntidadeFinanceira::forActiveCompany()
            ->findOrFail($entidadeId);

        // 3) Calcular saldo anterior ao período
        //    saldo_inicial + entradas_antes - saidas_antes
        $saldoInicial = $entidade->saldo_inicial ?? 0;

        $movAntes = $this->baseQuery()
            ->where('entidade_id', $entidadeId)
            ->where('data_competencia', '<', $dataInicialFormatted)
            ->groupBy('tipo')
            ->select('tipo', DB::raw('SUM(valor) as total'))
            ->get()
            ->keyBy('tipo');

        $entradasAntes = $movAntes->get('entrada')?->total ?? 0;
        $saidasAntes   = $movAntes->get('saida')?->total ?? 0;
        $saldoAnterior = $saldoInicial + $entradasAntes - $saidasAntes;

        // 4) Buscar transações do período, ordenadas por data
        $transacoes = $this->baseQuery()
            ->where('entidade_id', $entidadeId)
            ->whereBetween('data_competencia', [$dataInicialFormatted, $dataFinalFormatted])
            ->with(['lancamentoPadrao', 'parceiro'])
            ->orderBy('data_competencia', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        // 5) Calcular saldo progressivo (running balance)
        $saldoCorrente = $saldoAnterior;
        $movimentacoes = [];
        $totalEntradas = 0;
        $totalSaidas   = 0;

        foreach ($transacoes as $transacao) {
            $valor = $transacao->valor;

            if ($transacao->tipo === 'entrada') {
                $saldoCorrente += $valor;
                $totalEntradas += $valor;
            } else {
                $saldoCorrente -= $valor;
                $totalSaidas   += $valor;
            }

            $movimentacoes[] = [
                'data'        => Carbon::parse($transacao->data_competencia)->format('d/m/Y'),
                'descricao'   => $transacao->descricao ?? '-',
                'categoria'   => $transacao->lancamentoPadrao?->description ?? '-',
                'parceiro'    => $transacao->parceiro?->nome ?? $transacao->parceiro?->nome_fantasia ?? '-',
                'tipo'        => $transacao->tipo,
                'entrada'     => $transacao->tipo === 'entrada' ? $valor : null,
                'saida'       => $transacao->tipo === 'saida' ? $valor : null,
                'saldo'       => $saldoCorrente,
                'situacao'    => $transacao->situacao,
            ];
        }

        $saldoFinal = $saldoCorrente;

        Log::info('[ExtratoController] Totais calculados', [
            'entidade' => $entidade->nome,
            'entidade_id' => $entidadeId,
            'saldo_inicial' => $saldoInicial,
            'entradas_antes' => $entradasAntes,
            'saidas_antes' => $saidasAntes,
            'saldo_anterior' => $saldoAnterior,
            'total_entradas' => $totalEntradas,
            'total_saidas' => $totalSaidas,
            'saldo_final' => $saldoFinal,
            'total_transacoes' => count($movimentacoes),
            'periodo' => $dataInicial . ' a ' . $dataFinal,
        ]);

        // 6) Buscar company da sessão ativa
        $company = Auth::user()
            ->companies()
            ->with('addresses')
            ->where('companies.id', session('active_company_id'))
            ->first();

        // 7) HTML da view
        $html = view('app.relatorios.financeiro.extrato_pdf', [
            'empresaRelatorio'  => $company,
            'nomeEmpresa'       => $company->name,
            'razaoSocial'       => $company->razao_social,
            'cnpjEmpresa'       => $company->cnpj,
            'avatarEmpresa'     => $company->avatar,
            'enderecoEmpresa'   => $company->addresses,
            'entidade'          => $entidade,
            'movimentacoes'     => $movimentacoes,
            'saldoAnterior'     => $saldoAnterior,
            'saldoFinal'        => $saldoFinal,
            'totalEntradas'     => $totalEntradas,
            'totalSaidas'       => $totalSaidas,
            'dataInicial'       => $dataInicial,
            'dataFinal'         => $dataFinal,
        ])->render();

        // 8) PDF
        $pdf = BrowsershotHelper::configureChromePath(
            Browsershot::html($html)
                ->format('A4')
                ->landscape()
                ->showBackground()
                ->margins(8, 8, 15, 8)
                ->waitUntilNetworkIdle()
        )->pdf();

        return response($pdf, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename=extrato-financeiro.pdf',
        ]);
    }

    /**
     * Gera PDF de forma assíncrona (não trava o servidor)
     */
    public function gerarPdfAsync(Request $request)
    {
        try {
            $dataInicial = $request->input('data_inicial');
            $dataFinal   = $request->input('data_final');
            $entidadeId  = $request->input('entidade_id');
            $companyId   = session('active_company_id');
            $tenantId    = tenant('id');

            // Validar company_id
            if (!$companyId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Empresa não selecionada. Por favor, selecione uma empresa.'
                ], 400);
            }

            if (!$entidadeId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selecione uma conta financeira.'
                ], 400);
            }

            // Criar registro de rastreamento
            $pdfGen = PdfGeneration::create([
                'type' => 'extrato',
                'user_id' => Auth::id(),
                'company_id' => $companyId,
                'status' => 'pending',
                'parameters' => [
                    'data_inicial' => $dataInicial,
                    'data_final'   => $dataFinal,
                    'entidade_id'  => $entidadeId,
                ],
            ]);

            // Despachar job
            \App\Jobs\GenerateExtratoPdfJob::dispatch(
                $dataInicial,
                $dataFinal,
                $entidadeId,
                $companyId,
                Auth::id(),
                $tenantId,
                $pdfGen->id
            );

            return response()->json([
                'success' => true,
                'pdf_id'  => $pdfGen->id,
                'message' => 'PDF sendo gerado em background. Aguarde...'
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao despachar job de PDF Extrato', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao iniciar geração de PDF: ' . $e->getMessage()
            ], 500);
        }
    }
}
