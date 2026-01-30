<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Spatie\Browsershot\Browsershot;
use App\Helpers\BrowsershotHelper;
use App\Models\Financeiro\TransacaoFinanceira;
use App\Models\PdfGeneration;
use App\Models\EntidadeFinanceira;
use App\Models\Tenant;
use Carbon\Carbon;

class GenerateBoletimPdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300;
    public $tries = 3;

    protected $mes;
    protected $ano;
    protected $companyId;
    protected $userId;
    protected $tenantId;
    protected $pdfGenerationId;

    public function __construct($mes, $ano, $companyId, $userId, $tenantId, $pdfGenerationId)
    {
        $this->mes = $mes;
        $this->ano = $ano;
        $this->companyId = $companyId;
        $this->userId = $userId;
        $this->tenantId = $tenantId;
        $this->pdfGenerationId = $pdfGenerationId;
    }

    public function handle()
    {
        try {
            Log::info('[GenerateBoletimPdfJob] Job iniciado', [
                'tenant_id' => $this->tenantId,
                'company_id' => $this->companyId,
                'pdf_generation_id' => $this->pdfGenerationId,
            ]);

            // Inicializar tenant
            if ($this->tenantId) {
                $tenant = \App\Models\Tenant::find($this->tenantId);
                Log::info('[GenerateBoletimPdfJob] Tenant encontrado', [
                    'tenant' => $tenant ? $tenant->id : 'NULL',
                ]);
                if ($tenant) {
                    tenancy()->initialize($tenant);
                    Log::info('[GenerateBoletimPdfJob] Tenant inicializado');
                }
            }

            // Atualizar status para processing
            $pdfGen = PdfGeneration::find($this->pdfGenerationId);
            Log::info('[GenerateBoletimPdfJob] PdfGeneration', [
                'found' => $pdfGen ? true : false,
            ]);
            if ($pdfGen) {
                $pdfGen->update([
                    'status' => 'processing',
                    'started_at' => now(),
                ]);
            }

            $company = \App\Models\Company::with('addresses')->find($this->companyId);
            
            Log::info('[GenerateBoletimPdfJob] Company busca', [
                'company_id' => $this->companyId,
                'found' => $company ? true : false,
                'connection' => \DB::getDefaultConnection(),
            ]);
            
            // Validar se company existe
            if (!$company) {
                throw new \Exception("Company não encontrada com ID: {$this->companyId}");
            }
            
            Log::info('[GenerateBoletimPdfJob] Iniciando geração', [
                'company_id' => $this->companyId,
                'company_name' => $company->name,
                'mes' => $this->mes,
                'ano' => $this->ano,
            ]);
            
            // Calcular período
            $dataInicio = Carbon::create($this->ano, $this->mes, 1)->startOfMonth();
            $dataFim = Carbon::create($this->ano, $this->mes, 1)->endOfMonth();

            // Buscar transações
            $transacoes = TransacaoFinanceira::where('company_id', $this->companyId)
                ->whereBetween('data_competencia', [$dataInicio, $dataFim])
                ->with(['lancamentoPadrao', 'entidadeFinanceira', 'costCenter'])
                ->get();

            // Calcular totais
            $totalEntradas = $transacoes->where('tipo', 'entrada')->sum('valor');
            $totalSaidas = $transacoes->where('tipo', 'saida')->sum('valor');
            $saldo = $totalEntradas - $totalSaidas;

            // Agrupar por lançamento padrão
            $entradasPorLancamento = $transacoes->where('tipo', 'entrada')
                ->groupBy('lancamento_padrao_id')
                ->map(function ($grupo) {
                    $lp = $grupo->first()->lancamentoPadrao;
                    return [
                        'codigo' => $lp->code ?? '---',
                        'descricao' => $lp->description ?? 'Sem lançamento',
                        'valor' => $grupo->sum('valor')
                    ];
                });

            $saidasPorLancamento = $transacoes->where('tipo', 'saida')
                ->groupBy('lancamento_padrao_id')
                ->map(function ($grupo) {
                    $lp = $grupo->first()->lancamentoPadrao;
                    return [
                        'codigo' => $lp->code ?? '---',
                        'descricao' => $lp->description ?? 'Sem lançamento',
                        'valor' => $grupo->sum('valor')
                    ];
                });

            // 2. RESULTADO DAS CONTAS DE MOVIMENTO FINANCEIRO
            $entidades = EntidadeFinanceira::where('company_id', $this->companyId)->get();
            $contasMovimento = [];
            $saldoAnteriorTotal = 0;
            $saldoAtualTotal = 0;

            foreach ($entidades as $entidade) {
                // Saldo inicial da conta
                $saldoInicial = $entidade->saldo_inicial ?? 0;

                // Entradas e Saídas ANTES do período (para o saldo anterior)
                $entradasAntes = TransacaoFinanceira::where('entidade_id', $entidade->id)
                    ->where('tipo', 'entrada')
                    ->where('data_competencia', '<', $dataInicio)
                    ->sum('valor');

                $saidasAntes = TransacaoFinanceira::where('entidade_id', $entidade->id)
                    ->where('tipo', 'saida')
                    ->where('data_competencia', '<', $dataInicio)
                    ->sum('valor');

                $saldoAnterior = $saldoInicial + $entradasAntes - $saidasAntes;

                // Entradas e Saídas NO período
                $entradasPeriodo = $transacoes->where('entidade_id', $entidade->id)
                    ->where('tipo', 'entrada')
                    ->sum('valor');

                $saidasPeriodo = $transacoes->where('entidade_id', $entidade->id)
                    ->where('tipo', 'saida')
                    ->sum('valor');

                $saldoAtual = $saldoAnterior + $entradasPeriodo - $saidasPeriodo;

                $contasMovimento[] = [
                    'conta' => $entidade->description,
                    'saldo_anterior' => $saldoAnterior,
                    'entrada' => $entradasPeriodo,
                    'saida' => $saidasPeriodo,
                    'saldo_atual' => $saldoAtual
                ];

                $saldoAnteriorTotal += $saldoAnterior;
                $saldoAtualTotal += $saldoAtual;
            }

            $deficit = $totalEntradas - $totalSaidas;

            Log::info("Job Boletim: HTML sendo gerado para " . $company->razao_social);
            
            // Debug: verificar dados da company
            Log::info('[GenerateBoletimPdfJob] Dados company para view', [
                'name' => $company->name,
                'razao_social' => $company->razao_social,
                'cnpj' => $company->cnpj,
                'avatar' => $company->avatar,
                'has_address' => $company->addresses ? 'Sim' : 'Não',
                'address_rua' => $company->addresses?->rua,
            ]);

            // Preparar dados para a view - garantir que sejam passados corretamente
            $viewData = [
                'empresaRelatorio' => $company,
                'nomeEmpresa' => $company->name,
                'razaoSocial' => $company->razao_social,
                'cnpjEmpresa' => $company->cnpj,
                'avatarEmpresa' => $company->avatar,
                'enderecoEmpresa' => $company->addresses,
                'mes' => $this->mes,
                'ano' => $this->ano,
                'dataInicio' => $dataInicio,
                'dataFim' => $dataFim,
                'dataInicial' => $dataInicio->format('d/m/Y'),
                'dataFinal' => $dataFim->format('d/m/Y'),
                'totalEntradas' => $totalEntradas,
                'totalSaidas' => $totalSaidas,
                'saldo' => $totalEntradas - $totalSaidas,
                'lancamentosEntradas' => $entradasPorLancamento,
                'lancamentosSaidas' => $saidasPorLancamento,
                'transacoes' => $transacoes,
                'contasMovimento' => $contasMovimento,
                'saldoAnteriorTotal' => $saldoAnteriorTotal,
                'saldoAtualTotal' => $saldoAtualTotal,
                'deficit' => $deficit,
            ];

            // Gerar HTML usando View::make para garantir escopo correto
            $html = \Illuminate\Support\Facades\View::make('app.relatorios.financeiro.boletim_pdf', $viewData)->render();
            
            // Debug: verificar se company aparece no HTML
            Log::info('[GenerateBoletimPdfJob] HTML contém company?', [
                'contains_nome' => str_contains(strtoupper($html), strtoupper($company->name ?? '---')),
                'contains_razao' => str_contains(strtoupper($html), strtoupper($company->razao_social ?? '---')),
                'html_excerpt' => substr($html, strpos($html, '<body>'), 1000),
            ]);

            // Gerar PDF
            $pdf = BrowsershotHelper::configureChromePath(
                Browsershot::html($html)
                    ->format('A4')
                    ->showBackground()
                    ->margins(8, 8, 8, 8)
                    ->waitUntilNetworkIdle()
            )->pdf();

            // Salvar PDF
            $filename = "pdfs/boletins/boletim_{$this->mes}_{$this->ano}_{$this->companyId}_" . time() . ".pdf";
            Storage::disk('public')->put($filename, $pdf);

            // Atualizar status para completed
            if ($pdfGen) {
                $pdfGen->update([
                    'status' => 'completed',
                    'filename' => $filename,
                    'completed_at' => now(),
                ]);
            }

            Log::info("PDF Boletim gerado com sucesso", [
                'pdf_id' => $this->pdfGenerationId,
                'filename' => $filename,
                'company_id' => $this->companyId,
                'mes' => $this->mes,
                'ano' => $this->ano,
            ]);

            return $filename;

        } catch (\Exception $e) {
            // Atualizar status para failed
            if (isset($pdfGen)) {
                $pdfGen->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                    'completed_at' => now(),
                ]);
            }

            Log::error("Erro ao gerar PDF Boletim", [
                'pdf_id' => $this->pdfGenerationId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e; // Re-throw para retry
        }
    }
}
