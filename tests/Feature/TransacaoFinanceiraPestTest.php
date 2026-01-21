<?php

/**
 * Testes Pest para a rota de criação de lançamentos financeiros
 * 
 * INSTALAÇÃO DO PEST:
 * composer require pestphp/pest --dev --with-all-dependencies
 * composer require pestphp/pest-plugin-laravel --dev
 * ./vendor/bin/pest --init
 * 
 * EXECUTAR TESTES:
 * ./vendor/bin/pest tests/Feature/TransacaoFinanceiraPestTest.php
 */

use App\Models\Company;
use App\Models\Financeiro\EntidadeFinanceira;
use App\Models\Financeiro\TransacaoFinanceira;
use App\Models\LancamentoPadrao;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Cria empresa de teste
    $this->company = Company::factory()->create();
    
    // Cria usuário autenticado
    $this->user = User::factory()->create([
        'company_id' => $this->company->id,
    ]);
    
    // Cria entidade financeira (conta bancária)
    $this->entidade = EntidadeFinanceira::factory()->create([
        'company_id' => $this->company->id,
        'tipo' => 'banco',
        'nome' => 'Conta Teste',
    ]);
    
    // Cria categoria
    $this->categoria = LancamentoPadrao::factory()->create([
        'company_id' => $this->company->id,
        'tipo' => 'entrada',
    ]);
});

test('pode criar lançamento com dados válidos', function () {
    $response = $this->actingAs($this->user)
        ->withSession(['active_company_id' => $this->company->id])
        ->post(route('banco.store'), [
            'data_competencia' => '17/01/2026',
            'tipo' => 'entrada',
            'descricao' => 'Lançamento de Teste',
            'valor' => '1.500,00',
            'entidade_id' => $this->entidade->id,
            'lancamento_padrao_id' => $this->categoria->id,
            'tipo_documento' => 'pix',
            'situacao' => 'em_aberto',
        ]);

    $response->assertStatus(200)
        ->assertJson(['success' => true]);
    
    $this->assertDatabaseHas('transacoes_financeiras', [
        'descricao' => 'Lançamento de Teste',
        'tipo' => 'entrada',
        'company_id' => $this->company->id,
    ]);
});

test('descrição é obrigatória', function () {
    $response = $this->actingAs($this->user)
        ->withSession(['active_company_id' => $this->company->id])
        ->post(route('banco.store'), [
            'data_competencia' => '17/01/2026',
            'tipo' => 'entrada',
            'descricao' => '',
            'valor' => '1.500,00',
            'entidade_id' => $this->entidade->id,
            'lancamento_padrao_id' => $this->categoria->id,
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['descricao']);
});

test('valor é obrigatório e deve ser maior que zero', function () {
    $response = $this->actingAs($this->user)
        ->withSession(['active_company_id' => $this->company->id])
        ->post(route('banco.store'), [
            'data_competencia' => '17/01/2026',
            'tipo' => 'entrada',
            'descricao' => 'Teste',
            'valor' => '0,00',
            'entidade_id' => $this->entidade->id,
            'lancamento_padrao_id' => $this->categoria->id,
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['valor']);
});

test('entidade financeira é obrigatória', function () {
    $response = $this->actingAs($this->user)
        ->withSession(['active_company_id' => $this->company->id])
        ->post(route('banco.store'), [
            'data_competencia' => '17/01/2026',
            'tipo' => 'entrada',
            'descricao' => 'Teste',
            'valor' => '1.500,00',
            'entidade_id' => null,
            'lancamento_padrao_id' => $this->categoria->id,
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['entidade_id']);
});

test('pode criar lançamento de saída (despesa)', function () {
    $categoriaSaida = LancamentoPadrao::factory()->create([
        'company_id' => $this->company->id,
        'tipo' => 'saida',
    ]);

    $response = $this->actingAs($this->user)
        ->withSession(['active_company_id' => $this->company->id])
        ->post(route('banco.store'), [
            'data_competencia' => '17/01/2026',
            'tipo' => 'saida',
            'descricao' => 'Despesa de Teste',
            'valor' => '500,00',
            'entidade_id' => $this->entidade->id,
            'lancamento_padrao_id' => $categoriaSaida->id,
            'tipo_documento' => 'boleto',
            'situacao' => 'em_aberto',
        ]);

    $response->assertStatus(200)
        ->assertJson(['success' => true]);
    
    $this->assertDatabaseHas('transacoes_financeiras', [
        'descricao' => 'Despesa de Teste',
        'tipo' => 'saida',
    ]);
});

test('pode criar lançamento com status recebido', function () {
    $response = $this->actingAs($this->user)
        ->withSession(['active_company_id' => $this->company->id])
        ->post(route('banco.store'), [
            'data_competencia' => '17/01/2026',
            'tipo' => 'entrada',
            'descricao' => 'Lançamento Recebido',
            'valor' => '1.000,00',
            'entidade_id' => $this->entidade->id,
            'lancamento_padrao_id' => $this->categoria->id,
            'tipo_documento' => 'dinheiro',
            'situacao' => 'recebido',
            'data_pagamento' => '17/01/2026',
            'valor_pago' => '1.000,00',
        ]);

    $response->assertStatus(200);
    
    $this->assertDatabaseHas('transacoes_financeiras', [
        'descricao' => 'Lançamento Recebido',
        'situacao' => 'recebido',
    ]);
});

test('visitante não pode criar lançamento', function () {
    $response = $this->post(route('banco.store'), [
        'data_competencia' => '17/01/2026',
        'tipo' => 'entrada',
        'descricao' => 'Teste',
        'valor' => '1.000,00',
    ]);

    $response->assertRedirect(route('login'));
});

test('endpoint summary retorna dados válidos', function () {
    TransacaoFinanceira::factory()->count(3)->create([
        'company_id' => $this->company->id,
        'entidade_id' => $this->entidade->id,
        'tipo' => 'entrada',
        'situacao' => 'em_aberto',
    ]);

    $response = $this->actingAs($this->user)
        ->withSession(['active_company_id' => $this->company->id])
        ->get(route('banco.summary', [
            'start_date' => '2026-01-01',
            'end_date' => '2026-01-31',
        ]));

    $response->assertStatus(200)
        ->assertJsonStructure([
            'tabs' => [['key', 'value']],
            'sideCard' => ['total_receitas', 'total_despesas', 'saldo'],
            'meta' => ['start_date', 'end_date', 'updated_at'],
        ]);
});
