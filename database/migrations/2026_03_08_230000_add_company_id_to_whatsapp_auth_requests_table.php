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
        Schema::table('whatsapp_auth_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->after('user_id')
                ->comment('ID da empresa ativa no momento da vinculação (para salvar documentos na empresa correta)');
            $table->index(['tenant_id', 'user_id', 'company_id'], 'idx_tenant_user_company');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('whatsapp_auth_requests', function (Blueprint $table) {
            $table->dropIndex('idx_tenant_user_company');
            $table->dropColumn('company_id');
        });
    }
};
