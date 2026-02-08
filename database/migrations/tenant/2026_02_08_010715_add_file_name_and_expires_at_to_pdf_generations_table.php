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
        Schema::table('pdf_generations', function (Blueprint $table) {
            $table->string('file_name')->nullable()->after('filename')->comment('Nome amigável do arquivo para exibição');
            $table->timestamp('expires_at')->nullable()->after('completed_at')->comment('Data de expiração do arquivo (5 dias após geração)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pdf_generations', function (Blueprint $table) {
            $table->dropColumn(['file_name', 'expires_at']);
        });
    }
};
