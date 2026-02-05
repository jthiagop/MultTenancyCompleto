<?php

namespace App\Services;

use App\Enums\SituacaoTransacao;
use App\Models\EntidadeFinanceira;
use App\Models\Financeiro\TransacaoFinanceira;
use App\Models\Movimentacao;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class TransacaoDeleteService
{
    /**
     * Excluir uma única transação
     * 
     * REGRA IMPORTANTE:
     * - Se situação for 'pago' ou 'recebido': Reverter saldo + Excluir movimentações + Excluir transação
     * - Se situação for 'em_aberto': Apenas excluir a transação (sem afetar saldo ou movimentações)
     */
    public function delete(TransacaoFinanceira $transacao, array $options = []): bool
    {
        $softDelete = $options['soft_delete'] ?? true;

        DB::beginTransaction();
        
        try {
            // Validar se pode excluir
            if (!$this->canDelete($transacao)) {
                throw new Exception('Não é possível excluir esta transação.');
            }

            // Verificar se houve movimentação de dinheiro (pago ou recebido)
            // $transacao->situacao é um enum SituacaoTransacao, usamos comparação direta
            $houveMovimentacaoDinheiro = in_array($transacao->situacao, [
                SituacaoTransacao::PAGO,
                SituacaoTransacao::RECEBIDO,
            ], true);

            if ($houveMovimentacaoDinheiro) {
                // SITUAÇÃO PAGO/RECEBIDO: Reverter saldo + Limpar movimentações
                
                // Reverter saldo da entidade
                $entidadeId = $transacao->entidade_id ?? $transacao->entidade_financeira_id;
                if ($entidadeId) {
                    $this->revertBalance($transacao);
                }

                // Limpar movimentações relacionadas
                $this->cleanupMovimentacoes($transacao);

                // Desfazer conciliação bancária se existir
                $this->undoBankReconciliation($transacao);

                Log::info('Transação com movimentação de dinheiro - saldo revertido e movimentações limpas', [
                    'transacao_id' => $transacao->id,
                    'situacao' => $transacao->situacao,
                    'tenant_id' => tenancy()->tenant->id ?? null
                ]);
            } else {
                // SITUAÇÃO EM_ABERTO: Apenas excluir a transação
                Log::info('Transação em aberto - apenas exclusão do registro, sem afetar saldo ou movimentações', [
                    'transacao_id' => $transacao->id,
                    'situacao' => $transacao->situacao,
                    'tenant_id' => tenancy()->tenant->id ?? null
                ]);
            }

            // Excluir a transação
            if ($softDelete) {
                $transacao->delete(); // Soft delete
            } else {
                $transacao->forceDelete(); // Hard delete
            }

            // Log da operação
            Log::info('Transação excluída', [
                'transacao_id' => $transacao->id,
                'tipo' => $transacao->tipo,
                'valor' => $transacao->valor,
                'situacao' => $transacao->situacao,
                'houve_movimentacao_dinheiro' => $houveMovimentacaoDinheiro,
                'soft_delete' => $softDelete,
                'tenant_id' => tenancy()->tenant->id ?? null
            ]);

            DB::commit();
            return true;

        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Erro ao excluir transação', [
                'transacao_id' => $transacao->id,
                'error' => $e->getMessage(),
                'tenant_id' => tenancy()->tenant->id ?? null
            ]);

            throw $e;
        }
    }

    /**
     * Excluir múltiplas transações em lote
     * 
     * REGRA IMPORTANTE:
     * - Se situação for 'pago' ou 'recebido': Reverter saldo + Excluir movimentações + Excluir transação
     * - Se situação for 'em_aberto': Apenas excluir a transação (sem afetar saldo ou movimentações)
     */
    public function deleteBatch(array $transacaoIds, array $options = []): array
    {
        $softDelete = $options['soft_delete'] ?? true;
        $results = ['success' => [], 'failed' => []];

        DB::beginTransaction();
        
        try {
            $transacoes = TransacaoFinanceira::whereIn('id', $transacaoIds)->get();
            
            foreach ($transacoes as $transacao) {
                try {
                    // Validar se pode excluir
                    if (!$this->canDelete($transacao)) {
                        $results['failed'][] = [
                            'id' => $transacao->id,
                            'error' => 'Não é possível excluir esta transação.'
                        ];
                        continue;
                    }

                    // Verificar se houve movimentação de dinheiro (pago ou recebido)
                    $houveMovimentacaoDinheiro = in_array($transacao->situacao, [
                        SituacaoTransacao::PAGO,
                        SituacaoTransacao::RECEBIDO,
                    ], true);

                    if ($houveMovimentacaoDinheiro) {
                        // SITUAÇÃO PAGO/RECEBIDO: Reverter saldo + Limpar movimentações
                        
                        // Reverter saldo da entidade
                        $entidadeId = $transacao->entidade_id ?? $transacao->entidade_financeira_id;
                        if ($entidadeId) {
                            $this->revertBalance($transacao);
                        }

                        // Limpar movimentações relacionadas
                        $this->cleanupMovimentacoes($transacao);

                        // Desfazer conciliação bancária se existir
                        $this->undoBankReconciliation($transacao);
                    }

                    // Excluir a transação
                    if ($softDelete) {
                        $transacao->delete(); // Soft delete
                    } else {
                        $transacao->forceDelete(); // Hard delete
                    }

                    $results['success'][] = $transacao->id;

                } catch (Exception $e) {
                    $results['failed'][] = [
                        'id' => $transacao->id,
                        'error' => $e->getMessage()
                    ];
                }
            }

            // Log da operação em lote
            Log::info('Exclusão em lote de transações', [
                'total_solicitadas' => count($transacaoIds),
                'sucessos' => count($results['success']),
                'falhas' => count($results['failed']),
                'soft_delete' => $softDelete,
                'tenant_id' => tenancy()->tenant->id ?? null
            ]);

            DB::commit();
            return $results;

        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Erro na exclusão em lote de transações', [
                'transacao_ids' => $transacaoIds,
                'error' => $e->getMessage(),
                'tenant_id' => tenancy()->tenant->id ?? null
            ]);

            throw $e;
        }
    }

    /**
     * Verificar se uma transação pode ser excluída
     */
    public function canDelete(TransacaoFinanceira $transacao): bool
    {
        // Verificar se não está conciliada (se houver esse campo)
        if (isset($transacao->conciliada) && $transacao->conciliada) {
            return false;
        }

        // Verificar se não tem movimentações críticas (usando relação polimórfica)
        $hasImmutableMovements = Movimentacao::where('origem_type', TransacaoFinanceira::class)
            ->where('origem_id', $transacao->id)
            ->where('status', 'processada')
            ->exists();

        if ($hasImmutableMovements) {
            return false;
        }

        // Adicionar outras validações conforme necessário
        // Por exemplo: verificar se não está em processo de auditoria, etc.

        return true;
    }

    /**
     * Reverter o saldo da entidade financeira
     */
    private function revertBalance(TransacaoFinanceira $transacao): void
    {
        // Usar entidade_id ao invés de entidade_financeira_id
        $entidadeId = $transacao->entidade_id ?? $transacao->entidade_financeira_id;
        
        if (!$entidadeId) {
            return;
        }

        $entidade = EntidadeFinanceira::find($entidadeId);
        if (!$entidade) {
            return;
        }

        // Lógica de reversão do saldo baseada no tipo de transação
        // Usar saldo_atual ao invés de saldo
        $saldoAnterior = $entidade->saldo_atual ?? $entidade->saldo;
        
        if ($transacao->tipo === 'entrada') {
            // Se era entrada, subtrair do saldo atual
            if (isset($entidade->saldo_atual)) {
                $entidade->saldo_atual -= $transacao->valor;
            } else {
                $entidade->saldo -= $transacao->valor;
            }
        } elseif ($transacao->tipo === 'saida') {
            // Se era saída, somar ao saldo atual
            if (isset($entidade->saldo_atual)) {
                $entidade->saldo_atual += $transacao->valor;
            } else {
                $entidade->saldo += $transacao->valor;
            }
        }

        $entidade->save();

        $novoSaldo = $entidade->saldo_atual ?? $entidade->saldo;

        Log::info('Saldo da entidade revertido', [
            'entidade_id' => $entidade->id,
            'transacao_id' => $transacao->id,
            'tipo_transacao' => $transacao->tipo,
            'valor_revertido' => $transacao->valor,
            'saldo_anterior' => $saldoAnterior,
            'novo_saldo' => $novoSaldo,
            'tenant_id' => tenancy()->tenant->id ?? null
        ]);
    }

    /**
     * Limpar movimentações relacionadas à transação
     */
    private function cleanupMovimentacoes(TransacaoFinanceira $transacao): void
    {
        $totalMovimentacoes = 0;
        
        // 1. Buscar movimentações pela relação polimórfica (origem_type, origem_id)
        $movimentacoesByPolymorphic = Movimentacao::where('origem_type', TransacaoFinanceira::class)
            ->where('origem_id', $transacao->id)
            ->get();
        
        foreach ($movimentacoesByPolymorphic as $movimentacao) {
            // Soft delete das movimentações relacionadas
            $movimentacao->delete();
            $totalMovimentacoes++;
        }

        // 2. Se a transação tem movimentacao_id, lidar com a movimentação específica
        if ($transacao->movimentacao_id) {
            $movimentacao = Movimentacao::find($transacao->movimentacao_id);
            
            if ($movimentacao) {
                // Se existir campo valor_conciliado, resetar para null
                if (\Schema::hasColumn('movimentacoes', 'valor_conciliado')) {
                    $movimentacao->valor_conciliado = null;
                    $movimentacao->save();
                }
                
                // Deletar a movimentação
                $movimentacao->delete();
                $totalMovimentacoes++;
                
                Log::info('Movimentação específica deletada', [
                    'movimentacao_id' => $transacao->movimentacao_id,
                    'transacao_id' => $transacao->id,
                    'tenant_id' => tenancy()->tenant->id ?? null
                ]);
            }
        }
        
        if ($totalMovimentacoes > 0) {
            Log::info('Movimentações limpas', [
                'transacao_id' => $transacao->id,
                'movimentacoes_removidas' => $totalMovimentacoes,
                'tenant_id' => tenancy()->tenant->id ?? null
            ]);
        }
    }

    /**
     * Desfazer conciliação bancária se houver
     */
    private function undoBankReconciliation(TransacaoFinanceira $transacao): void
    {
        // Implementar lógica específica de desfazer conciliação bancária
        
        // 1. Desfazer vínculo com bank_statements
        if (method_exists($transacao, 'bankStatements') && $transacao->bankStatements()->exists()) {
            $bankStatements = $transacao->bankStatements;
            
            foreach ($bankStatements as $bankStatement) {
                $bankStatement->update([
                    'reconciled' => false,
                    'status_conciliacao' => 'pendente'
                ]);
                
                Log::info('Bank statement resetado', [
                    'bank_statement_id' => $bankStatement->id,
                    'status' => 'pendente',
                    'tenant_id' => tenancy()->tenant->id ?? null
                ]);
            }
            
            // Remover vínculo na tabela pivot
            $transacao->bankStatements()->detach();
        }
        
        // 2. Resetar campos de conciliação se existirem
        if (isset($transacao->conciliada) && $transacao->conciliada) {
            $transacao->conciliada = false;
            $transacao->data_conciliacao = null;
            $transacao->save();

            Log::info('Conciliação bancária desfeita', [
                'transacao_id' => $transacao->id,
                'tenant_id' => tenancy()->tenant->id ?? null
            ]);
        }
    }

    /**
     * Obter estatísticas de exclusões
     */
    public function getDeleteStats(array $transacaoIds): array
    {
        $transacoes = TransacaoFinanceira::whereIn('id', $transacaoIds)->get();
        
        $stats = [
            'total' => $transacoes->count(),
            'por_tipo' => [],
            'valor_total' => 0,
            'can_delete' => 0,
            'cannot_delete' => 0
        ];

        foreach ($transacoes as $transacao) {
            $stats['valor_total'] += $transacao->valor;
            
            // Contar por tipo
            $tipo = $transacao->tipo;
            if (!isset($stats['por_tipo'][$tipo])) {
                $stats['por_tipo'][$tipo] = ['count' => 0, 'valor' => 0];
            }
            $stats['por_tipo'][$tipo]['count']++;
            $stats['por_tipo'][$tipo]['valor'] += $transacao->valor;

            // Verificar se pode excluir
            if ($this->canDelete($transacao)) {
                $stats['can_delete']++;
            } else {
                $stats['cannot_delete']++;
            }
        }

        return $stats;
    }
}