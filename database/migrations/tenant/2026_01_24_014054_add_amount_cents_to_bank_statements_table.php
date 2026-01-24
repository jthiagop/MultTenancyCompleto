<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * ✅ Adiciona suporte a centavos (integer) para evitar problemas de rounding
     * amount_cents > 0 = Recebimento (entrada/credit)
     * amount_cents < 0 = Pagamento (saída/debit)
     * 
     * Utiliza chunkById para processar em lotes (1000 por vez) evitando memory overflow
     */
    public function up(): void
    {
        Schema::table('bank_statements', function (Blueprint $table) {
            $table->bigInteger('amount_cents')
                ->nullable()
                ->after('amount')
                ->comment('Valor em centavos (positivo=entrada, negativo=saída)');
        });

        // ✅ Preenche amount_cents a partir do amount (DECIMAL), processando em chunks
        // Conversão segura: amount (DECIMAL) → float → cents (INTEGER)
        DB::table('bank_statements')
            ->select('id', 'amount')
            ->whereNotNull('amount')
            ->orderBy('id')
            ->chunkById(1000, function ($rows) {
                foreach ($rows as $row) {
                    // amount vem como string/decimal -> converte para centavos com segurança
                    $cents = (int) round(((float) $row->amount) * 100);

                    DB::table('bank_statements')
                        ->where('id', $row->id)
                        ->update(['amount_cents' => $cents]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bank_statements', function (Blueprint $table) {
            $table->dropColumn('amount_cents');
        });
    }
};
