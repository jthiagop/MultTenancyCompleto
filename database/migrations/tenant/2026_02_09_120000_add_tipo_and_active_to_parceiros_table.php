<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('parceiros', function (Blueprint $table) {
            // Tipo de pessoa: PJ (Pessoa Jurídica), PF (Pessoa Física), ambos
            if (!Schema::hasColumn('parceiros', 'tipo')) {
                $table->string('tipo', 20)->default('pj')->after('nome_fantasia');
            }
            // Natureza: fornecedor, cliente, etc. (string livre, validado via Enum PHP)
            if (!Schema::hasColumn('parceiros', 'natureza')) {
                $table->string('natureza', 50)->default('fornecedor')->after('tipo');
            }
            if (!Schema::hasColumn('parceiros', 'cpf')) {
                $table->string('cpf', 14)->nullable()->after('cnpj');
            }
            if (!Schema::hasColumn('parceiros', 'active')) {
                $table->boolean('active')->default(true)->after('email');
            }
            if (!Schema::hasColumn('parceiros', 'observacoes')) {
                $table->text('observacoes')->nullable()->after('active');
            }
        });

        // Classificar registros existentes que tenham CNPJ como PJ / fornecedor
        DB::table('parceiros')
            ->whereNull('natureza')
            ->orWhere('natureza', '')
            ->update(['natureza' => 'fornecedor']);

        DB::table('parceiros')
            ->whereNull('tipo')
            ->orWhere('tipo', '')
            ->update(['tipo' => 'pj']);
    }

    public function down(): void
    {
        Schema::table('parceiros', function (Blueprint $table) {
            $cols = ['tipo', 'natureza', 'cpf', 'active', 'observacoes'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('parceiros', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
