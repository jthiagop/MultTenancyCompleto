<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Migração Opção A: saldo_inicial = 0 na tabela, movimentação como fonte única de verdade.
 *
 * Para cada entidade financeira com saldo_inicial != 0:
 * 1. Se NÃO existe movimentação de saldo_inicial → cria uma
 * 2. Se JÁ existe movimentação de saldo_inicial → não duplica
 * 3. Zera o campo saldo_inicial na tabela entidades_financeiras
 * 4. Recalcula saldo_atual baseado apenas nas movimentações
 */
return new class extends Migration
{
    public function up(): void
    {
        $entidades = DB::table('entidades_financeiras')
            ->where('saldo_inicial', '!=', 0)
            ->get();

        $corrigidas = 0;
        $jaExistia = 0;

        foreach ($entidades as $entidade) {
            // Verifica se já existe uma movimentação de saldo_inicial para essa entidade
            $movSaldoInicial = DB::table('movimentacoes')
                ->where('entidade_id', $entidade->id)
                ->where('categoria', 'saldo_inicial')
                ->whereNull('deleted_at')
                ->first();

            if (!$movSaldoInicial) {
                // Também verifica pela descrição (entidades criadas ANTES desta migração)
                $movPorDescricao = DB::table('movimentacoes')
                    ->where('entidade_id', $entidade->id)
                    ->where('descricao', 'Saldo inicial da entidade financeira')
                    ->whereNull('deleted_at')
                    ->first();

                if ($movPorDescricao) {
                    // Atualiza a categoria da movimentação existente
                    DB::table('movimentacoes')
                        ->where('id', $movPorDescricao->id)
                        ->update([
                            'categoria' => 'saldo_inicial',
                            'data' => $movPorDescricao->data ?? $entidade->created_at,
                            'status' => 'concluida',
                        ]);
                    $jaExistia++;
                } else {
                    // Cria a movimentação de saldo inicial que estava faltando
                    DB::table('movimentacoes')->insert([
                        'entidade_id' => $entidade->id,
                        'tipo' => $entidade->saldo_inicial >= 0 ? 'entrada' : 'saida',
                        'valor' => abs($entidade->saldo_inicial),
                        'descricao' => 'Saldo inicial da entidade financeira',
                        'data' => $entidade->created_at,
                        'categoria' => 'saldo_inicial',
                        'status' => 'concluida',
                        'company_id' => $entidade->company_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $corrigidas++;
                }
            } else {
                $jaExistia++;
            }

            // Recalcula saldo_atual baseado APENAS nas movimentações
            $saldoCalculado = DB::table('movimentacoes')
                ->where('entidade_id', $entidade->id)
                ->whereNull('deleted_at')
                ->selectRaw("COALESCE(SUM(CASE WHEN tipo = 'entrada' THEN valor ELSE -valor END), 0) as saldo")
                ->value('saldo');

            // Zera saldo_inicial e atualiza saldo_atual
            DB::table('entidades_financeiras')
                ->where('id', $entidade->id)
                ->update([
                    'saldo_inicial' => 0,
                    'saldo_atual' => $saldoCalculado,
                ]);
        }

        Log::info("Migração Opção A concluída", [
            'entidades_processadas' => $entidades->count(),
            'movimentacoes_criadas' => $corrigidas,
            'ja_existiam' => $jaExistia,
        ]);
    }

    public function down(): void
    {
        // Reversão: restaura saldo_inicial a partir da movimentação de categoria 'saldo_inicial'
        $movimentacoes = DB::table('movimentacoes')
            ->where('categoria', 'saldo_inicial')
            ->whereNull('deleted_at')
            ->get();

        foreach ($movimentacoes as $mov) {
            $valor = $mov->tipo === 'entrada' ? $mov->valor : -$mov->valor;

            DB::table('entidades_financeiras')
                ->where('id', $mov->entidade_id)
                ->update(['saldo_inicial' => $valor]);
        }

        Log::info("Rollback Opção A: saldo_inicial restaurado para {$movimentacoes->count()} entidades");
    }
};
