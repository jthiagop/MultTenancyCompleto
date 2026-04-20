<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lancamento_padrao_company', function (Blueprint $t) {
            $t->id();
            $t->foreignId('lancamento_padrao_id')
                ->constrained('lancamento_padraos')
                ->cascadeOnDelete();
            $t->foreignId('company_id')
                ->constrained('companies')
                ->cascadeOnDelete();
            $t->timestamps();

            $t->unique(['lancamento_padrao_id', 'company_id'], 'lp_company_unique');
            $t->index('company_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lancamento_padrao_company');
    }
};
