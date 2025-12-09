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
        if (!Schema::hasTable('fiel_spouse')) {
        Schema::create('fiel_spouse', function (Blueprint $table) {
            $table->id();
                $table->foreignId('fiel_id')->constrained('fieis')->onDelete('cascade');
                $table->foreignId('fiel_conjuge_id')->nullable()->constrained('fieis')->onDelete('set null');
                $table->string('nome_conjuge')->nullable();
                $table->date('data_nascimento')->nullable();
                $table->boolean('ocultar_ano')->default(false);
                $table->string('profissao')->nullable();
                $table->boolean('dizimista')->default(false);
                $table->string('codigo_dizimista')->nullable();
                $table->string('cartao_magnetico')->nullable();
                $table->decimal('percentual_salario', 5, 2)->nullable();
                $table->boolean('criar_ficha')->default(false);
            $table->timestamps();
                
                $table->unique('fiel_id');
            });
        } else {
            // Se a tabela já existe, verificar e adicionar colunas que faltam
            Schema::table('fiel_spouse', function (Blueprint $table) {
                if (!Schema::hasColumn('fiel_spouse', 'fiel_conjuge_id')) {
                    $table->foreignId('fiel_conjuge_id')->nullable()->constrained('fieis')->onDelete('set null')->after('fiel_id');
                }
                if (!Schema::hasColumn('fiel_spouse', 'nome_conjuge')) {
                    $table->string('nome_conjuge')->nullable()->after('fiel_conjuge_id');
                }
                if (!Schema::hasColumn('fiel_spouse', 'data_nascimento')) {
                    $table->date('data_nascimento')->nullable()->after('nome_conjuge');
                }
                if (!Schema::hasColumn('fiel_spouse', 'ocultar_ano')) {
                    $table->boolean('ocultar_ano')->default(false)->after('data_nascimento');
                }
                if (!Schema::hasColumn('fiel_spouse', 'profissao')) {
                    $table->string('profissao')->nullable()->after('ocultar_ano');
                }
                if (!Schema::hasColumn('fiel_spouse', 'dizimista')) {
                    $table->boolean('dizimista')->default(false)->after('profissao');
                }
                if (!Schema::hasColumn('fiel_spouse', 'codigo_dizimista')) {
                    $table->string('codigo_dizimista')->nullable()->after('dizimista');
                }
                if (!Schema::hasColumn('fiel_spouse', 'cartao_magnetico')) {
                    $table->string('cartao_magnetico')->nullable()->after('codigo_dizimista');
                }
                if (!Schema::hasColumn('fiel_spouse', 'percentual_salario')) {
                    $table->decimal('percentual_salario', 5, 2)->nullable()->after('cartao_magnetico');
                }
                if (!Schema::hasColumn('fiel_spouse', 'criar_ficha')) {
                    $table->boolean('criar_ficha')->default(false)->after('percentual_salario');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fiel_spouse');
    }
};
