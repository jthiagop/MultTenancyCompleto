<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bank_statement_imports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('bank_account_id')->nullable()->constrained('entidades_financeiras')->onDelete('set null'); // Referencia entidades_financeiras

            // Origem: 'OFX', 'CSV', 'API_BB', 'MANUAL'
            $table->string('source');

            // Para OFX/CSV
            $table->string('file_name')->nullable();
            $table->string('file_hash')->nullable(); // Hash do arquivo inteiro

            // Para API (Controle do que foi baixado)
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();

            // Auditoria
            $table->foreignId('imported_by')->nullable()->constrained('users');
            $table->timestamp('imported_at')->useCurrent();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_statement_imports');
    }
};
