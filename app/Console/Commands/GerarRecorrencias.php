<?php

namespace App\Console\Commands;

use App\Models\Financeiro\Recorrencia;
use App\Models\Financeiro\TransacaoFinanceira;
use App\Models\Movimentacao;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GerarRecorrencias extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recorrencias:gerar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gera lançamentos financeiros automaticamente para recorrências ativas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando geração de recorrências...');

        $totalGeradas = 0;
        $totalErros = 0;

        // Itera sobre todos os tenants
        Tenant::all()->each(function ($tenant) use (&$totalGeradas, &$totalErros) {
            $tenant->run(function () use ($tenant, &$totalGeradas, &$totalErros) {
                $this->info("Processando tenant: {$tenant->id}");

                // Busca transações que têm recorrência_id e precisam gerar próxima ocorrência
                // Busca pela pivot para encontrar a última ocorrência gerada de cada recorrência
                $transacoesComRecorrencia = TransacaoFinanceira::whereNotNull('recorrencia_id')
                    ->with(['recorrenciaConfig', 'movimentacao'])
                    ->get()
                    ->groupBy('recorrencia_id');

                if ($transacoesComRecorrencia->isEmpty()) {
                    $this->info("  Nenhuma recorrência para gerar.");
                    return;
                }

                $this->info("  Encontradas {$transacoesComRecorrencia->count()} configuração(ões) de recorrência para processar.");

                foreach ($transacoesComRecorrencia as $recorrenciaId => $transacoes) {
                    try {
                        DB::beginTransaction();

                        // Busca a configuração de recorrência
                        $recorrencia = Recorrencia::find($recorrenciaId);
                        if (!$recorrencia || !$recorrencia->ativo) {
                            DB::commit();
                            continue;
                        }

                        // Busca a última transação gerada desta recorrência (via pivot)
                        $ultimaOcorrencia = DB::table('recorrencia_transacoes')
                            ->where('recorrencia_id', $recorrenciaId)
                            ->orderBy('numero_ocorrencia', 'desc')
                            ->first();

                        if (!$ultimaOcorrencia) {
                            DB::commit();
                            continue;
                        }

                        // Busca a transação original (primeira da série)
                        $transacaoOriginal = TransacaoFinanceira::find($ultimaOcorrencia->transacao_financeira_id);
                        if (!$transacaoOriginal) {
                            $this->error("  Recorrência #{$recorrenciaId}: Transação original não encontrada.");
                            DB::rollBack();
                            $totalErros++;
                            continue;
                        }

                        // Verifica se já completou todas as ocorrências
                        $ocorrenciasGeradas = DB::table('recorrencia_transacoes')
                            ->where('recorrencia_id', $recorrenciaId)
                            ->count();

                        if ($ocorrenciasGeradas >= $recorrencia->total_ocorrencias) {
                            $recorrencia->update(['ativo' => false]);
                            $this->warn("  Recorrência #{$recorrenciaId} completada e desativada.");
                            DB::commit();
                            continue;
                        }

                        // Calcula próxima data baseada na última ocorrência
                        $dataUltimaOcorrencia = Carbon::parse($ultimaOcorrencia->data_geracao);
                        $dataGeracao = $recorrencia->calcularProximaDataGeracao($dataUltimaOcorrencia);

                        // Verifica se já passou da data de geração
                        if ($dataGeracao->isFuture()) {
                            DB::commit();
                            continue;
                        }

                        // Verifica se já existe lançamento para esta data (prevenção de duplicatas)
                        $existeLancamento = TransacaoFinanceira::where('company_id', $recorrencia->company_id)
                            ->where('recorrencia_id', $recorrenciaId)
                            ->where('data_competencia', $dataGeracao->format('Y-m-d'))
                            ->exists();

                        if ($existeLancamento) {
                            $this->warn("  Recorrência #{$recorrenciaId}: Lançamento já existe para {$dataGeracao->format('d/m/Y')}. Pulando...");
                            DB::commit();
                            continue;
                        }

                        // Cria nova transação financeira (nasce como em_aberto — previsão futura)
                        // A movimentação (impacto no saldo) só será criada quando o usuário
                        // marcar como pago/recebido via registrarBaixa()
                        $novaTransacao = TransacaoFinanceira::create([
                            'company_id' => $transacaoOriginal->company_id,
                            'data_competencia' => $dataGeracao->format('Y-m-d'),
                            'data_vencimento' => $dataGeracao->format('Y-m-d'),
                            'entidade_id' => $transacaoOriginal->entidade_id,
                            'tipo' => $transacaoOriginal->tipo,
                            'valor' => $transacaoOriginal->valor,
                            'descricao' => $transacaoOriginal->descricao,
                            'situacao' => 'em_aberto', // Recorrência nasce como em_aberto
                            'recorrencia_id' => $recorrenciaId,
                            'centro' => $transacaoOriginal->centro,
                            'tipo_documento' => $transacaoOriginal->tipo_documento,
                            'numero_documento' => $transacaoOriginal->numero_documento,
                            'origem' => $transacaoOriginal->origem,
                            'historico_complementar' => $transacaoOriginal->historico_complementar,
                            'comprovacao_fiscal' => $transacaoOriginal->comprovacao_fiscal,
                            'lancamento_padrao_id' => $transacaoOriginal->lancamento_padrao_id,
                            'cost_center_id' => $transacaoOriginal->cost_center_id,
                            'valor_pago' => 0,
                            'juros' => 0,
                            'multa' => 0,
                            'desconto' => 0,
                            'created_by' => $transacaoOriginal->created_by,
                            'created_by_name' => $transacaoOriginal->created_by_name ?? 'Sistema',
                            'updated_by' => $transacaoOriginal->updated_by,
                            'updated_by_name' => $transacaoOriginal->updated_by_name ?? 'Sistema',
                        ]);

                        // Registra na tabela pivot (sem movimentacao_id — será preenchido na baixa)
                        $numeroOcorrencia = $ocorrenciasGeradas + 1;
                        DB::table('recorrencia_transacoes')->insert([
                            'recorrencia_id' => $recorrenciaId,
                            'transacao_financeira_id' => $novaTransacao->id,
                            'data_geracao' => $dataGeracao->format('Y-m-d'),
                            'numero_ocorrencia' => $numeroOcorrencia,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        // Calcula próxima data de geração
                        $novaData = $recorrencia->calcularProximaDataGeracao($dataGeracao);

                        // Atualiza recorrência com dados da instância atual
                        $recorrencia->update([
                            'ocorrencias_geradas' => $numeroOcorrencia,
                            'data_proxima_geracao' => $novaData,
                            'ultima_execucao' => now(),
                            'ativo' => $numeroOcorrencia < $recorrencia->total_ocorrencias,
                        ]);

                        DB::commit();

                        $this->info("  ✓ Recorrência #{$recorrenciaId}: Lançamento gerado para {$dataGeracao->format('d/m/Y')} (Ocorrência {$numeroOcorrencia}/{$recorrencia->total_ocorrencias})");
                        $totalGeradas++;

                        if ($numeroOcorrencia >= $recorrencia->total_ocorrencias) {
                            $this->info("  → Recorrência #{$recorrenciaId} completada e desativada.");
                        }

                        Log::info("Recorrência gerada", [
                            'recorrencia_id' => $recorrenciaId,
                            'transacao_id' => $novaTransacao->id,
                            'data_geracao' => $dataGeracao->format('Y-m-d'),
                            'ocorrencia' => $numeroOcorrencia,
                        ]);

                    } catch (\Exception $e) {
                        DB::rollBack();
                        $totalErros++;
                        $this->error("  ✗ Erro ao gerar recorrência #{$recorrenciaId}: {$e->getMessage()}");
                        Log::error("Erro ao gerar recorrência", [
                            'recorrencia_id' => $recorrenciaId,
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString(),
                        ]);
                    }
                }
            });
        });

        $this->info("\nGeração concluída!");
        $this->info("Total de lançamentos gerados: {$totalGeradas}");
        if ($totalErros > 0) {
            $this->warn("Total de erros: {$totalErros}");
        }

        return Command::SUCCESS;
    }
}
