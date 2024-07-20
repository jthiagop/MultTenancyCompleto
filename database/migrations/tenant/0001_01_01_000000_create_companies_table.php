<?php
// Migração para a tabela companies
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('cnpj')->nullable()->unique();
            $table->string('email')->nullable();
            $table->string('avatar')->nullable();
            $table->date('data_cnpj')->nullable();
            $table->date('data_fundacao')->nullable();
            $table->text('details')->nullable();
            $table->enum('type', ['matriz', 'filial']);
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('status')->default('active');
            $table->json('tags')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->foreign('parent_id')->references('id')->on('companies')->onDelete('cascade');

        });
    }

    public function down()
    {
        Schema::dropIfExists('companies');
    }
}
