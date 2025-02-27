<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('bank_statements', function (Blueprint $table) {
            $table->string('file_name')->nullable()->after('deleted_at'); // Nome do arquivo
            $table->string('file_hash')->unique()->nullable()->after('file_name'); // Hash do arquivo para evitar duplicatas
            $table->decimal('total_value', 15, 2)->nullable()->after('file_hash'); // Valor total do OFX
            $table->integer('transaction_count')->nullable()->after('total_value'); // Número de transações no arquivo
            $table->timestamp('imported_at')->nullable()->after('transaction_count'); // Data da importação
            $table->unsignedBigInteger('imported_by')->nullable()->after('imported_at'); // Usuário que fez a importação

            // Índices para otimização
            $table->index('imported_at');
            $table->index('reconciled');

            // Chave estrangeira para o usuário que importou
            $table->foreign('imported_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('bank_statements', function (Blueprint $table) {
            $table->dropColumn(['file_name', 'file_hash', 'total_value', 'transaction_count', 'imported_at', 'imported_by']);
            $table->dropIndex(['imported_at', 'reconciled']);
            $table->dropForeign(['imported_by']);
        });
    }
};
