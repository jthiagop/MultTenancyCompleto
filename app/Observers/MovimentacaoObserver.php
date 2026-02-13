<?php

namespace App\Observers;

use App\Models\EntidadeFinanceira;
use App\Models\Movimentacao;
use Illuminate\Support\Facades\Log;

/**
 * Observer centralizado para gerenciar o saldo_atual da EntidadeFinanceira.
 *
 * ESTRATÉGIA: Incremental O(1)
 * - Cada criação/exclusão de Movimentacao faz um único INCREMENT ou DECREMENT
 *   atômico no saldo_atual da entidade vinculada.
 * - Isso é eficiente mesmo com milhões de registros.
 * - O recalcularSaldo() continua disponível como ferramenta de MANUTENÇÃO/REPAIR.
 *
 * REGRA:
 *   tipo = 'entrada' → INCREMENT saldo_atual
 *   tipo = 'saida'   → DECREMENT saldo_atual
 */
class MovimentacaoObserver
{
    /**
     * Após criar uma movimentação, ajusta o saldo da entidade.
     */
    public function created(Movimentacao $movimentacao): void
    {
        $this->ajustarSaldo($movimentacao, 'created');
    }

    /**
     * Após atualizar uma movimentação (ex: inversão de tipo ou alteração de valor).
     * Reverte o saldo antigo e aplica o novo.
     */
    public function updated(Movimentacao $movimentacao): void
    {
        // Só precisa agir se tipo ou valor mudaram
        $tipoMudou = $movimentacao->wasChanged('tipo');
        $valorMudou = $movimentacao->wasChanged('valor');
        $entidadeMudou = $movimentacao->wasChanged('entidade_id');

        if (!$tipoMudou && !$valorMudou && !$entidadeMudou) {
            return;
        }

        // 1. Reverter o impacto antigo na entidade anterior
        $entidadeAntigaId = $entidadeMudou
            ? $movimentacao->getOriginal('entidade_id')
            : $movimentacao->entidade_id;

        $tipoAntigo = $movimentacao->getOriginal('tipo');
        $valorAntigo = (float) $movimentacao->getOriginal('valor');

        $entidadeAntiga = EntidadeFinanceira::find($entidadeAntigaId);
        if ($entidadeAntiga) {
            if ($tipoAntigo === 'entrada') {
                $entidadeAntiga->decrement('saldo_atual', $valorAntigo);
            } else {
                $entidadeAntiga->increment('saldo_atual', $valorAntigo);
            }

            Log::info('[MovimentacaoObserver::updated] Saldo revertido na entidade anterior', [
                'entidade_id' => $entidadeAntigaId,
                'tipo_antigo' => $tipoAntigo,
                'valor_antigo' => $valorAntigo,
                'saldo_atual' => $entidadeAntiga->fresh()->saldo_atual,
            ]);
        }

        // 2. Aplicar o impacto novo na entidade atual
        $this->ajustarSaldo($movimentacao, 'updated');
    }

    /**
     * Antes de deletar (soft delete) uma movimentação, reverte o saldo.
     */
    public function deleted(Movimentacao $movimentacao): void
    {
        $entidade = EntidadeFinanceira::find($movimentacao->entidade_id);
        if (!$entidade) {
            return;
        }

        // Inverso do created: desfaz o impacto
        if ($movimentacao->tipo === 'entrada') {
            $entidade->decrement('saldo_atual', (float) $movimentacao->valor);
        } else {
            $entidade->increment('saldo_atual', (float) $movimentacao->valor);
        }

        Log::info('[MovimentacaoObserver::deleted] Saldo revertido após exclusão de movimentação', [
            'movimentacao_id' => $movimentacao->id,
            'entidade_id' => $entidade->id,
            'tipo' => $movimentacao->tipo,
            'valor' => $movimentacao->valor,
            'saldo_atual' => $entidade->fresh()->saldo_atual,
        ]);
    }

    /**
     * Aplica o impacto de uma movimentação no saldo da entidade.
     * Usado por created() e pela parte "novo" do updated().
     */
    private function ajustarSaldo(Movimentacao $movimentacao, string $evento): void
    {
        $entidade = EntidadeFinanceira::find($movimentacao->entidade_id);
        if (!$entidade) {
            Log::warning("[MovimentacaoObserver::{$evento}] Entidade não encontrada", [
                'entidade_id' => $movimentacao->entidade_id,
                'movimentacao_id' => $movimentacao->id,
            ]);
            return;
        }

        $valor = (float) $movimentacao->valor;

        if ($movimentacao->tipo === 'entrada') {
            $entidade->increment('saldo_atual', $valor);
        } else {
            $entidade->decrement('saldo_atual', $valor);
        }

        Log::info("[MovimentacaoObserver::{$evento}] Saldo ajustado", [
            'movimentacao_id' => $movimentacao->id,
            'entidade_id' => $entidade->id,
            'tipo' => $movimentacao->tipo,
            'valor' => $valor,
            'saldo_atual' => $entidade->fresh()->saldo_atual,
        ]);
    }
}
