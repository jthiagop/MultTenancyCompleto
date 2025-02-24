<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('recibos', function (Blueprint $table) {
            // Remove colunas desnecessárias
            $table->dropColumn([
                'numero_recibo',
                'data_emissao',
                'tipo_pessoa',
                'endereco',
                'cidade',
                'estado',
            ]);

            // Adiciona os novos relacionamentos
            $table->unsignedBigInteger('address_id')->nullable()->after('id');
            $table->foreign('address_id')->references('id')->on('adresses')->onDelete('set null');

            $table->unsignedBigInteger('transacao_id')->nullable()->after('address_id');
            $table->foreign('transacao_id')->references('id')->on('transacoes_financeiras')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('recibos', function (Blueprint $table) {
            // Adiciona de volta as colunas removidas
            $table->string('numero_recibo')->nullable();
            $table->date('data_emissao')->nullable();
            $table->string('tipo_pessoa')->nullable();
            $table->string('endereco')->nullable();
            $table->string('cidade')->nullable();
            $table->string('estado')->nullable();

            // Remove as chaves estrangeiras e as colunas adicionadas
            $table->dropForeign(['address_id']);
            $table->dropColumn('address_id');

            $table->dropForeign(['transacao_id']);
            $table->dropColumn('transacao_id');
        });
    }
};

