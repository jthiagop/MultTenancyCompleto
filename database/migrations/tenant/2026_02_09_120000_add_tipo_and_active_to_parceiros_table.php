<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('parceiros', function (Blueprint $table) {
            if (!Schema::hasColumn('parceiros', 'tipo')) {
                $table->enum('tipo', ['fornecedor', 'cliente', 'ambos'])->default('fornecedor')->after('nome_fantasia');
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

        // Classificar automaticamente registros existentes
        // CNPJ com 14 dígitos = fornecedor (PJ), CPF com 11 = cliente (PF)
        DB::table('parceiros')->whereNull('tipo')->orWhere('tipo', '')->update(['tipo' => 'fornecedor']);
    }

    public function down(): void
    {
        Schema::table('parceiros', function (Blueprint $table) {
            $cols = ['tipo', 'cpf', 'active', 'observacoes'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('parceiros', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
