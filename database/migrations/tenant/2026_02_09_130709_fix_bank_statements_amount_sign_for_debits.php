<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Correção: Pagamentos (DEBIT) estavam sendo salvos com amount positivo,
     * fazendo com que fossem classificados como "entrada" ao invés de "saída".
     * 
     * Esta migration corrige o sinal de amount e amount_cents para registros
     * do tipo DEBIT que ainda estão com valores positivos.
     */
    public function up(): void
    {
        // Corrige bank_statements onde trntype = 'DEBIT' mas amount é positivo
        // Débitos devem ter amount NEGATIVO
        $affected = DB::table('bank_statements')
            ->where('trntype', 'DEBIT')
            ->where('amount', '>', 0)
            ->update([
                'amount' => DB::raw('amount * -1'),
                'amount_cents' => DB::raw('amount_cents * -1'),
            ]);

        Log::info("✅ Migration fix_bank_statements_amount_sign: {$affected} registros DEBIT corrigidos (amount → negativo).");

        // Segurança: Garante que CREDIT tem amount positivo
        $affectedCredit = DB::table('bank_statements')
            ->where('trntype', 'CREDIT')
            ->where('amount', '<', 0)
            ->update([
                'amount' => DB::raw('ABS(amount)'),
                'amount_cents' => DB::raw('ABS(amount_cents)'),
            ]);

        if ($affectedCredit > 0) {
            Log::info("✅ Migration fix_bank_statements_amount_sign: {$affectedCredit} registros CREDIT corrigidos (amount → positivo).");
        }
    }

    /**
     * Reverter: Torna todos os amounts positivos novamente (estado anterior).
     */
    public function down(): void
    {
        DB::table('bank_statements')
            ->where('amount', '<', 0)
            ->update([
                'amount' => DB::raw('ABS(amount)'),
                'amount_cents' => DB::raw('ABS(amount_cents)'),
            ]);
    }
};
