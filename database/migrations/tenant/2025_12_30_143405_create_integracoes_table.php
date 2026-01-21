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
        Schema::create('integracoes', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['whatsapp', 'dda', 'email'])->default('whatsapp');
            $table->enum('status', ['configurado', 'pendente'])->default('pendente');
            $table->string('remetente')->nullable(); // Número do usuário (whatsapp_number)
            $table->string('destinatario')->nullable(); // Número do sistema Dominus
            $table->unsignedBigInteger('user_id')->nullable(); // Usuário que configurou
            $table->timestamps();
            
            // Índices
            $table->index('user_id');
            $table->index('tipo');
            $table->index('status');
            
            // Foreign key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('integracoes');
    }
};
