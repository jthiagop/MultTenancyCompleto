<?php

namespace App\Console\Commands;

use App\Models\EntidadeFinanceira;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RecalcularSaldosFinanceiros extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'financeiro:recalcular-saldos
                            {--company_id= : ID da empresa (processa todas se omitido)}
                            {--dry-run     : Apenas audita divergências sem corrigir}
                            {--tolerance=0.01 : Diferença mínima (em R$) para considerar divergência}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Audita e corrige divergências entre saldo_atual (cache) e movimentações reais';

    /** Exit code usado quando há divergências (útil em pipelines de CI/monitoramento) */
    const EXIT_HAS_DIVERGENCES = 2;

    public function handle(): int
    {
        $dryRun    = (bool) $this->option('dry-run');
        $companyId = $this->option('company_id');
        $tolerance = (float) $this->option('tolerance');

        $this->info($dryRun
            ? '🔍 Modo auditoria (--dry-run): nenhum valor será alterado.'
            : '🔄 Iniciando recálculo de saldos financeiros...'
        );
        $this->newLine();

        try {
            $query = EntidadeFinanceira::query();

            if ($companyId) {
                $query->where('company_id', $companyId);
            }

            $entidades = $query->get();

            if ($entidades->isEmpty()) {
                $this->warn('Nenhuma entidade financeira encontrada para os critérios informados.');
                return Command::SUCCESS;
            }

            // Calcula os saldos reais de todas as entidades em uma única query (evita N+1)
            $ids         = $entidades->pluck('id')->toArray();
            $saldosReais = EntidadeFinanceira::saldosCalculadosPorEntidadeIds($ids);

            $divergentes = [];

            foreach ($entidades as $entidade) {
                $saldoCache = round((float) $entidade->saldo_atual, 2);
                $saldoReal  = round($saldosReais[$entidade->id] ?? 0.0, 2);
                $diferenca  = abs($saldoReal - $saldoCache);

                if ($diferenca < $tolerance) {
                    continue;
                }

                $divergentes[] = [
                    'id'           => $entidade->id,
                    'nome'         => $entidade->nome,
                    'company_id'   => $entidade->company_id,
                    'saldo_cache'  => $saldoCache,
                    'saldo_real'   => $saldoReal,
                    'diferenca'    => $saldoReal - $saldoCache,
                ];

                if (!$dryRun) {
                    DB::table('entidades_financeiras')
                        ->where('id', $entidade->id)
                        ->update(['saldo_atual' => $saldoReal]);
                }
            }

            // ── Relatório ─────────────────────────────────────────────────────
            $total      = $entidades->count();
            $qtdDiverg  = count($divergentes);
            $qtdOk      = $total - $qtdDiverg;

            $this->table(
                ['ID', 'Conta', 'Company', 'Cache (R$)', 'Real (R$)', 'Diferença (R$)'],
                array_map(fn($d) => [
                    $d['id'],
                    $d['nome'],
                    $d['company_id'],
                    number_format($d['saldo_cache'], 2, ',', '.'),
                    number_format($d['saldo_real'],  2, ',', '.'),
                    ($d['diferenca'] >= 0 ? '+' : '') . number_format($d['diferenca'], 2, ',', '.'),
                ], $divergentes)
            );

            $this->newLine();
            $this->info("Total processado : {$total}");
            $this->info("Sem divergência  : {$qtdOk}");

            if ($qtdDiverg > 0) {
                $this->warn("Divergentes      : {$qtdDiverg}");

                Log::warning('[RecalcularSaldosFinanceiros] Divergências detectadas', [
                    'dry_run'    => $dryRun,
                    'total'      => $total,
                    'divergentes'=> $qtdDiverg,
                    'detalhes'   => $divergentes,
                ]);

                if ($dryRun) {
                    $this->warn('Rode sem --dry-run para corrigir automaticamente.');
                } else {
                    $this->info("{$qtdDiverg} saldo(s) corrigido(s).");
                }

                // Retorna exit code diferente para facilitar alertas em monitoramento
                return self::EXIT_HAS_DIVERGENCES;
            }

            $this->info('✅ Todos os saldos estão consistentes.');
            return Command::SUCCESS;

        } catch (\Throwable $e) {
            $this->error('❌ Erro: ' . $e->getMessage());
            Log::error('[RecalcularSaldosFinanceiros] Falha ao executar', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);
            return Command::FAILURE;
        }
    }
}
