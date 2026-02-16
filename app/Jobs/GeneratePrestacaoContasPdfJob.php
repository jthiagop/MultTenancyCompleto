<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Spatie\Browsershot\Browsershot;
use App\Helpers\BrowsershotHelper;
use App\Enums\SituacaoTransacao;
use App\Models\Financeiro\TransacaoFinanceira;
use App\Models\PdfGeneration;
use App\Models\EntidadeFinanceira;
use App\Models\User;
use App\Notifications\RelatorioGeradoNotification;
use App\Notifications\RelatorioErroNotification;
use Carbon\Carbon;

class GeneratePrestacaoContasPdfJob implements ShouldQueue
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
    protected $entidadeId;
    protected $modelo;
    protected $tipoData;
    protected $situacoes;
    protected $categorias;
    protected $parceiroId;
    protected $comprovacaoFiscal;
    protected $tipoValor;

    public function __construct(
        $dataInicial,
        $dataFinal,
        $companyId,
        $userId,
        $tenantId,
        $pdfGenerationId,
        $entidadeId = null,
        $modelo = 'horizontal',
        $tipoData = 'competencia',
        $situacoes = [],
        $categorias = [],
        $parceiroId = null,
        $comprovacaoFiscal = false,
        $tipoValor = 'previsto'
    ) {
        $this->dataInicial = $dataInicial;
        $this->dataFinal = $dataFinal;
        $this->companyId = $companyId;
        $this->userId = $userId;
        $this->tenantId = $tenantId;
        $this->pdfGenerationId = $pdfGenerationId;
        $this->entidadeId = $entidadeId;
        $this->modelo = $modelo;
        $this->tipoData = $tipoData;
        $this->situacoes = $situacoes ?? [];
        $this->categorias = $categorias ?? [];
        $this->parceiroId = $parceiroId;
        $this->comprovacaoFiscal = $comprovacaoFiscal;
        $this->tipoValor = $tipoValor;
    }

    public function handle()
    {
        try {
            Log::info('[GeneratePrestacaoContasPdfJob] Job iniciado', [
                'tenant_id' => $this->tenantId,
                'company_id' => $this->companyId,
                'pdf_generation_id' => $this->pdfGenerationId,
            ]);

            // Inicializar tenant
            if ($this->tenantId) {
                $tenant = \App\Models\Tenant::find($this->tenantId);
                if ($tenant) {
                    tenancy()->initialize($tenant);
                    Log::info('[GeneratePrestacaoContasPdfJob] Tenant inicializado');
                }
            }

            // Atualizar status para processing
            $pdfGen = PdfGeneration::find($this->pdfGenerationId);
            if ($pdfGen) {
                $pdfGen->update([
                    'status' => 'processing',
                    'started_at' => now(),
                ]);
            }

            $company = \App\Models\Company::with('addresses')->find($this->companyId);

            if (!$company) {
                throw new \Exception("Company não encontrada com ID: {$this->companyId}");
            }

            // Parse das datas — aceita tanto d/m/Y quanto Y-m-d
            $dataInicio = $this->parseFlexibleDate($this->dataInicial)->startOfDay();
            $dataFim = $this->parseFlexibleDate($this->dataFinal)->endOfDay();

            // Coluna de data a filtrar
            $colunaData = $this->tipoData === 'pagamento' ? 'data_pagamento' : 'data_competencia';

            // Situações a excluir (sempre exclui parcelado + desconsiderado)
            $situacoesExcluidas = [
                SituacaoTransacao::DESCONSIDERADO->value,
                SituacaoTransacao::PARCELADO->value,
            ];

            // Query otimizada - com segurança tenant
            $query = TransacaoFinanceira::with(['entidadeFinanceira', 'lancamentoPadrao', 'parceiro'])
                ->where('company_id', $this->companyId)
                ->whereNotIn('situacao', $situacoesExcluidas)
                ->where('agendado', false)
                ->when($dataInicio, fn($q) => $q->whereDate($colunaData, '>=', $dataInicio))
                ->when($dataFim, fn($q) => $q->whereDate($colunaData, '<=', $dataFim))
                ->when($this->entidadeId, fn($q) => $q->where('entidade_id', $this->entidadeId))
                ->when(!empty($this->situacoes), fn($q) => $q->whereIn('situacao', $this->situacoes))
                ->when(!empty($this->categorias), fn($q) => $q->whereIn('lancamento_padrao_id', $this->categorias))
                ->when($this->parceiroId, fn($q) => $q->where('parceiro_id', $this->parceiroId))
                ->when($this->comprovacaoFiscal, fn($q) => $q->where('comprovacao_fiscal', true))
                ->orderBy($colunaData);

            $transacoes = $query->get()->groupBy('origem');

            // Totais por origem + totais gerais
            $dados = [];
            $totEntradaAll = $totSaidaAll = 0;
            $campoValor = $this->tipoValor === 'pago' ? 'valor_pago' : 'valor';

            foreach ($transacoes as $origem => $items) {
                $totEntrada = $items->where('tipo', 'entrada')->sum($campoValor);
                $totSaida = $items->where('tipo', 'saida')->sum($campoValor);

                $totEntradaAll += $totEntrada;
                $totSaidaAll += $totSaida;

                $dados[] = compact('origem', 'items', 'totEntrada', 'totSaida');
            }

            // Dados dos filtros para exibir no cabeçalho do PDF
            $entidadeNome = $this->entidadeId
                ? optional(EntidadeFinanceira::find($this->entidadeId))->nome
                : null;

            $parceiroNome = $this->parceiroId
                ? optional(\App\Models\Parceiro::find($this->parceiroId))->nome
                : null;

            Log::info('[GeneratePrestacaoContasPdfJob] Dados processados', [
                'total_grupos' => count($dados),
                'total_entradas' => $totEntradaAll,
                'total_saidas' => $totSaidaAll,
            ]);

            $viewData = [
                'empresaRelatorio' => $company,
                'nomeEmpresa' => $company->name,
                'razaoSocial' => $company->razao_social,
                'cnpjEmpresa' => $company->cnpj,
                'avatarEmpresa' => $company->avatar,
                'enderecoEmpresa' => $company->addresses,
                'dados' => $dados,
                'dataInicial' => $dataInicio->format('d/m/Y'),
                'dataFinal' => $dataFim->format('d/m/Y'),
                'entidadeNome' => $entidadeNome,
                'totalEntradas' => $totEntradaAll,
                'totalSaidas' => $totSaidaAll,
                'company' => $company,
                'tipoValor' => $this->tipoValor,
                'parceiroNome' => $parceiroNome,
                'comprovacaoFiscal' => $this->comprovacaoFiscal,
            ];

            $html = View::make('app.relatorios.financeiro.prestacao_pdf', $viewData)->render();

            // Gerar PDF - respeita o modelo escolhido
            $isLandscape = $this->modelo === 'horizontal';

            $pdf = BrowsershotHelper::configureChromePath(
                Browsershot::html($html)
                    ->format('A4')
                    ->landscape($isLandscape)
                    ->showBackground()
                    ->margins(8, 8, 15, 8)
                    ->waitUntilNetworkIdle()
            )->pdf();

            // Salvar PDF no storage central
            $filePrefix = $dataInicio->format('Ymd') . '_' . $dataFim->format('Ymd');
            $filename = "pdfs/prestacao-contas/prestacao_{$filePrefix}_{$this->companyId}_" . time() . ".pdf";
            $centralStoragePath = base_path('storage/app/public/' . $filename);

            // Garantir que o diretório existe
            $directory = dirname($centralStoragePath);
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            file_put_contents($centralStoragePath, $pdf);

            // Gerar nome amigável do arquivo
            $friendlyName = "Prestação de Contas - {$dataInicio->format('d/m/Y')} a {$dataFim->format('d/m/Y')}";

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

            Log::info('[GeneratePrestacaoContasPdfJob] PDF gerado com sucesso', [
                'pdf_id' => $this->pdfGenerationId,
                'filename' => $filename,
            ]);

            // Notificar usuário que o PDF está pronto
            $user = User::find($this->userId);
            if ($user && $pdfGen) {
                $pdfGen->refresh();
                $downloadUrl = $pdfGen->download_url ?? '#';
                $user->notify(new RelatorioGeradoNotification(
                    $downloadUrl,
                    $friendlyName,
                    $this->companyId,
                    $this->userId,
                    $pdfGen
                ));
                Log::info("[GeneratePrestacaoContasPdfJob] Notificação enviada ao usuário #{$this->userId}");
            }

            return $filename;

        } catch (\Exception $e) {
            if (isset($pdfGen)) {
                $pdfGen->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                    'completed_at' => now(),
                ]);
            }

            Log::error('[GeneratePrestacaoContasPdfJob] Erro ao gerar PDF', [
                'pdf_id' => $this->pdfGenerationId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Notificar usuário sobre o erro
            $user = User::find($this->userId);
            if ($user && isset($dataInicio, $dataFim)) {
                $periodoNome = $dataInicio->format('d/m/Y') . ' a ' . $dataFim->format('d/m/Y');
                $user->notify(new RelatorioErroNotification(
                    "Prestação de Contas - {$periodoNome}",
                    $e->getMessage(),
                    $this->companyId
                ));
            }

            throw $e; // Re-throw para retry
        }
    }

    /**
     * Parse data flexível — aceita d/m/Y ou Y-m-d
     */
    protected function parseFlexibleDate(string $dateStr): Carbon
    {
        // Tenta d/m/Y primeiro (formato do Flatpickr)
        if (preg_match('#^\d{2}/\d{2}/\d{4}$#', $dateStr)) {
            return Carbon::createFromFormat('d/m/Y', $dateStr);
        }

        // Tenta Y-m-d (formato ISO)
        if (preg_match('#^\d{4}-\d{2}-\d{2}$#', $dateStr)) {
            return Carbon::createFromFormat('Y-m-d', $dateStr);
        }

        // Fallback — Carbon::parse genérico
        return Carbon::parse($dateStr);
    }
}
