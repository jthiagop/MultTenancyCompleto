<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Corrige movimentações criadas pela conciliação bancária que não têm
     * o relacionamento polimórfico (origem_id/origem_type) preenchido.
     * 
     * O relacionamento é reconstruído através do movimentacao_id na transação.
     */
    public function up(): void
    {
        $transacaoClass = 'App\\Models\\Financeiro\\TransacaoFinanceira';
        
        // Busca transações que têm movimentacao_id mas a movimentação não tem origem_id
        $transacoesParaCorrigir = DB::table('transacoes_financeiras as t')
            ->join('movimentacoes as m', 't.movimentacao_id', '=', 'm.id')
            ->whereNotNull('t.movimentacao_id')
            ->where(function ($query) {
                $query->whereNull('m.origem_id')
                      ->orWhere('m.origem_id', 0);
            })
            ->select('t.id as transacao_id', 'm.id as movimentacao_id')
            ->get();

        $corrigidas = 0;
        
        foreach ($transacoesParaCorrigir as $registro) {
            DB::table('movimentacoes')
                ->where('id', $registro->movimentacao_id)
                ->update([
                    'origem_id' => $registro->transacao_id,
                    'origem_type' => $transacaoClass,
                ]);
            
            $corrigidas++;
        }

        Log::info('[Migration] Correção de relacionamento polimórfico em movimentações', [
            'total_encontradas' => $transacoesParaCorrigir->count(),
            'total_corrigidas' => $corrigidas,
        ]);
    }

    /**
     * Não há como reverter de forma segura pois não sabemos
     * quais registros tinham origem_id nulo originalmente.
     */
    public function down(): void
    {
        // Não reverte - seria destrutivo
        Log::warning('[Migration] Rollback de fix_movimentacoes_origem_polymorph ignorado - operação destrutiva');
    }
};
