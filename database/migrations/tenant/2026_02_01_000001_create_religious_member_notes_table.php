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
        Schema::create('religious_member_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('religious_member_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title')->nullable();
            $table->text('content');
            $table->enum('type', ['general', 'formation', 'health', 'administrative', 'spiritual'])->default('general');
            $table->boolean('is_private')->default(false);
            $table->timestamps();
            
            $table->index(['religious_member_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('religious_member_notes');
    }
};
