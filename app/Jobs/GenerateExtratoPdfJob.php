<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
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

class GenerateExtratoPdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300;
    public $tries = 3;

    protected $dataInicial;
    protected $dataFinal;
    protected $entidadeId;
    protected $companyId;
    protected $userId;
    protected $tenantId;
    protected $pdfGenerationId;

    public function __construct($dataInicial, $dataFinal, $entidadeId, $companyId, $userId, $tenantId, $pdfGenerationId)
    {
        $this->dataInicial = $dataInicial;
        $this->dataFinal = $dataFinal;
        $this->entidadeId = $entidadeId;
        $this->companyId = $companyId;
        $this->userId = $userId;
        $this->tenantId = $tenantId;
        $this->pdfGenerationId = $pdfGenerationId;
    }

    public function handle()
    {
        try {
            Log::info('[GenerateExtratoPdfJob] Job iniciado', [
                'tenant_id' => $this->tenantId,
                'company_id' => $this->companyId,
                'entidade_id' => $this->entidadeId,
                'pdf_generation_id' => $this->pdfGenerationId,
            ]);

            // Inicializar tenant
            if ($this->tenantId) {
                $tenant = \App\Models\Tenant::find($this->tenantId);
                if ($tenant) {
                    tenancy()->initialize($tenant);
                    Log::info('[GenerateExtratoPdfJob] Tenant inicializado');
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

            // Buscar entidade financeira
            $entidade = EntidadeFinanceira::where('company_id', $this->companyId)
                ->findOrFail($this->entidadeId);

            $dataInicio = Carbon::createFromFormat('d/m/Y', $this->dataInicial)->startOfDay();
            $dataFim = Carbon::createFromFormat('d/m/Y', $this->dataFinal)->endOfDay();

            // Opção A: saldo_inicial = 0, movimentações são a fonte de verdade

            // Saldo anterior ao período
            $entradasAntes = TransacaoFinanceira::where('entidade_id', $this->entidadeId)
                ->where('company_id', $this->companyId)
                ->whereNotIn('situacao', [SituacaoTransacao::DESCONSIDERADO, SituacaoTransacao::PARCELADO])
                ->where('agendado', false)
                ->where('tipo', 'entrada')
                ->where('data_competencia', '<', $dataInicio)
                ->sum('valor');

            $saidasAntes = TransacaoFinanceira::where('entidade_id', $this->entidadeId)
                ->where('company_id', $this->companyId)
                ->whereNotIn('situacao', [SituacaoTransacao::DESCONSIDERADO, SituacaoTransacao::PARCELADO])
                ->where('agendado', false)
                ->where('tipo', 'saida')
                ->where('data_competencia', '<', $dataInicio)
                ->sum('valor');

            $saldoInicial = (float) ($entidade->saldo_inicial ?? 0);
            $entradasAntes = (float) $entradasAntes;
            $saidasAntes = (float) $saidasAntes;
            $saldoAnterior = round($saldoInicial + $entradasAntes - $saidasAntes, 2);

            Log::info('[GenerateExtratoPdfJob] Saldo anterior calculado', [
                'saldo_inicial' => $saldoInicial,
                'entradas_antes' => $entradasAntes,
                'saidas_antes' => $saidasAntes,
                'saldo_anterior' => $saldoAnterior,
            ]);

            // Transações do período
            $transacoes = TransacaoFinanceira::where('entidade_id', $this->entidadeId)
                ->where('company_id', $this->companyId)
                ->whereNotIn('situacao', [SituacaoTransacao::DESCONSIDERADO, SituacaoTransacao::PARCELADO])
                ->where('agendado', false)
                ->whereBetween('data_competencia', [$dataInicio, $dataFim])
                ->with(['lancamentoPadrao', 'parceiro'])
                ->orderBy('data_competencia', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            // Calcular saldo progressivo
            $saldoCorrente = $saldoAnterior;
            $movimentacoes = [];
            $totalEntradas = 0;
            $totalSaidas   = 0;

            foreach ($transacoes as $transacao) {
                $valor = round((float) $transacao->valor, 2);

                if ($transacao->tipo === 'entrada') {
                    $saldoCorrente = round($saldoCorrente + $valor, 2);
                    $totalEntradas = round($totalEntradas + $valor, 2);
                } else {
                    $saldoCorrente = round($saldoCorrente - $valor, 2);
                    $totalSaidas   = round($totalSaidas + $valor, 2);
                }

                $movimentacoes[] = [
                    'data'      => Carbon::parse($transacao->data_competencia)->format('d/m/Y'),
                    'descricao' => $transacao->descricao ?? '-',
                    'categoria' => $transacao->lancamentoPadrao?->description ?? '-',
                    'parceiro'  => $transacao->parceiro?->nome ?? $transacao->parceiro?->nome_fantasia ?? '-',
                    'tipo'      => $transacao->tipo,
                    'entrada'   => $transacao->tipo === 'entrada' ? $valor : null,
                    'saida'     => $transacao->tipo === 'saida' ? $valor : null,
                    'saldo'     => $saldoCorrente,
                    'situacao'  => $transacao->situacao,
                ];
            }

            $saldoFinal = $saldoCorrente;

            Log::info('[GenerateExtratoPdfJob] Totais calculados', [
                'entidade' => $entidade->nome,
                'entidade_id' => $this->entidadeId,
                'total_transacoes' => count($movimentacoes),
                'total_entradas' => $totalEntradas,
                'total_saidas' => $totalSaidas,
                'saldo_anterior' => $saldoAnterior,
                'saldo_final' => $saldoFinal,
                'periodo' => $dataInicio->format('Y-m-d') . ' a ' . $dataFim->format('Y-m-d'),
            ]);

            $viewData = [
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
                'dataInicial'       => $dataInicio->format('d/m/Y'),
                'dataFinal'         => $dataFim->format('d/m/Y'),
            ];

            $html = \Illuminate\Support\Facades\View::make('app.relatorios.financeiro.extrato_pdf', $viewData)->render();

            // Gerar PDF — paisagem para caber mais colunas
            $pdf = BrowsershotHelper::configureChromePath(
                Browsershot::html($html)
                    ->format('A4')
                    ->landscape()
                    ->showBackground()
                    ->margins(8, 8, 15, 8)
                    ->waitUntilNetworkIdle()
            )->pdf();

            // Salvar PDF no storage central
            $filePrefix = $dataInicio->format('Ymd') . '_' . $dataFim->format('Ymd');
            $filename = "pdfs/extratos/extrato_{$filePrefix}_{$this->companyId}_{$this->entidadeId}_" . time() . ".pdf";
            $centralStoragePath = base_path('storage/app/public/' . $filename);

            $directory = dirname($centralStoragePath);
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            file_put_contents($centralStoragePath, $pdf);

            $friendlyName = "Extrato - {$entidade->nome} - {$dataInicio->format('d/m/Y')} a {$dataFim->format('d/m/Y')}";

            if ($pdfGen) {
                $pdfGen->update([
                    'status' => 'completed',
                    'filename' => $filename,
                    'file_name' => $friendlyName,
                    'completed_at' => now(),
                    'expires_at' => now()->addDays(PdfGeneration::EXPIRATION_DAYS),
                ]);
            }

            Log::info('[GenerateExtratoPdfJob] PDF gerado com sucesso', [
                'pdf_id' => $this->pdfGenerationId,
                'filename' => $filename,
            ]);

            // Notificar usuário
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
                Log::info("[GenerateExtratoPdfJob] Notificação enviada ao usuário #{$this->userId}");
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

            Log::error('[GenerateExtratoPdfJob] Erro ao gerar PDF', [
                'pdf_id' => $this->pdfGenerationId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $user = User::find($this->userId);
            if ($user && isset($dataInicio, $dataFim)) {
                $periodoNome = $dataInicio->format('d/m/Y') . ' a ' . $dataFim->format('d/m/Y');
                $user->notify(new RelatorioErroNotification(
                    "Extrato - {$periodoNome}",
                    $e->getMessage(),
                    $this->companyId
                ));
            }

            throw $e;
        }
    }
}
