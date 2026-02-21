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
use App\Enums\SituacaoTransacao;
use App\Models\Financeiro\TransacaoFinanceira;
use App\Models\PdfGeneration;
use App\Models\EntidadeFinanceira;
use App\Models\Tenant;
use App\Models\User;
use App\Notifications\RelatorioGeradoNotification;
use App\Notifications\RelatorioErroNotification;
use Carbon\Carbon;

class GenerateBoletimPdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300;
    public $tries = 3;

    protected $dataInicial;
    protected $dataFinal;
    protected $companyId;
    protected $userId;
    protected $tenantId;
    protected $pdfGenerationId;

    public function __construct($dataInicial, $dataFinal, $companyId, $userId, $tenantId, $pdfGenerationId)
    {
        $this->dataInicial = $dataInicial;
        $this->dataFinal = $dataFinal;
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
                'data_inicial' => $this->dataInicial,
                'data_final' => $this->dataFinal,
            ]);
            
            // Usar as datas reais selecionadas pelo usuário
            $dataInicio = Carbon::createFromFormat('d/m/Y', $this->dataInicial)->startOfDay();
            $dataFim = Carbon::createFromFormat('d/m/Y', $this->dataFinal)->endOfDay();

            // Buscar transações — excluir desconsideradas, parceladas e agendadas
            $transacoes = TransacaoFinanceira::where('company_id', $this->companyId)
                ->whereNotIn('situacao', [SituacaoTransacao::DESCONSIDERADO, SituacaoTransacao::PARCELADO])
                ->where('agendado', false)
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
                        'codigo' => $lp->id ?? '---',
                        'descricao' => $lp->description ?? 'Sem lançamento',
                        'valor' => $grupo->sum('valor')
                    ];
                });

            $saidasPorLancamento = $transacoes->where('tipo', 'saida')
                ->groupBy('lancamento_padrao_id')
                ->map(function ($grupo) {
                    $lp = $grupo->first()->lancamentoPadrao;
                    return [
                        'codigo' => $lp->id ?? '---',
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
                    ->where('company_id', $this->companyId)
                    ->whereNotIn('situacao', [SituacaoTransacao::DESCONSIDERADO, SituacaoTransacao::PARCELADO])
                    ->where('agendado', false)
                    ->where('tipo', 'entrada')
                    ->where('data_competencia', '<', $dataInicio)
                    ->sum('valor');

                $saidasAntes = TransacaoFinanceira::where('entidade_id', $entidade->id)
                    ->where('company_id', $this->companyId)
                    ->whereNotIn('situacao', [SituacaoTransacao::DESCONSIDERADO, SituacaoTransacao::PARCELADO])
                    ->where('agendado', false)
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
                    'conta' => $entidade->nome,
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

            // Salvar PDF no storage CENTRAL (não no tenant)
            // Usar caminho absoluto para evitar que o FilesystemTenancyBootstrapper redirecione
            $filePrefix = $dataInicio->format('Ymd') . '_' . $dataFim->format('Ymd');
            $filename = "pdfs/boletins/boletim_{$filePrefix}_{$this->companyId}_" . time() . ".pdf";
            $centralStoragePath = base_path('storage/app/public/' . $filename);
            
            // Garantir que o diretório existe
            $directory = dirname($centralStoragePath);
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
            
            // Salvar arquivo diretamente no storage central
            file_put_contents($centralStoragePath, $pdf);

            // Gerar nome amigável do arquivo
            $friendlyName = "Boletim Financeiro - {$dataInicio->format('d/m/Y')} a {$dataFim->format('d/m/Y')}";

            // Atualizar status para completed
            if ($pdfGen) {
                $pdfGen->update([
                    'status' => 'completed',
                    'filename' => $filename,
                    'file_name' => $friendlyName,
                    'completed_at' => now(),
                    'expires_at' => now()->addDays(PdfGeneration::EXPIRATION_DAYS),
                ]);
            }

            Log::info("PDF Boletim gerado com sucesso", [
                'pdf_id' => $this->pdfGenerationId,
                'filename' => $filename,
                'file_name' => $friendlyName,
                'expires_at' => now()->addDays(PdfGeneration::EXPIRATION_DAYS)->toDateTimeString(),
                'company_id' => $this->companyId,
                'data_inicial' => $this->dataInicial,
                'data_final' => $this->dataFinal,
            ]);

            // Notificar usuário que o PDF está pronto
            $user = User::find($this->userId);
            if ($user && $pdfGen) {
                // Recarregar o model para obter o download_url atualizado
                $pdfGen->refresh();
                $downloadUrl = $pdfGen->download_url ?? route('relatorios.boletim.financeiro.pdf-async.status', ['id' => $this->pdfGenerationId]);
                $user->notify(new RelatorioGeradoNotification(
                    $downloadUrl,
                    $friendlyName,
                    $this->companyId,
                    $this->userId,
                    $pdfGen // Metadados: tamanho, expiração
                ));
                Log::info("Notificação enviada ao usuário #{$this->userId}");
            }

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

            // Notificar usuário sobre o erro
            $user = User::find($this->userId);
            if ($user) {
                $periodoNome = $dataInicio->format('d/m/Y') . ' a ' . $dataFim->format('d/m/Y');
                $user->notify(new RelatorioErroNotification(
                    "Boletim Financeiro - {$periodoNome}",
                    $e->getMessage(),
                    $this->companyId
                ));
            }

            throw $e; // Re-throw para retry
        }
    }
}
