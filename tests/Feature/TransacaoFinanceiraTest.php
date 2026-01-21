<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Financeiro\EntidadeFinanceira;
use App\Models\Financeiro\TransacaoFinanceira;
use App\Models\LancamentoPadrao;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Testes para a rota de criação de lançamentos financeiros
 * 
 * Para converter para Pest (caso instale):
 * composer require pestphp/pest --dev --with-all-dependencies
 * composer require pestphp/pest-plugin-laravel --dev
 * ./vendor/bin/pest --init
 */
class TransacaoFinanceiraTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Company $company;
    protected EntidadeFinanceira $entidade;
    protected LancamentoPadrao $categoria;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Cria empresa de teste
        $this->company = Company::factory()->create();
        
        // Cria usuário autenticado
        $this->user = User::factory()->create([
            'company_id' => $this->company->id,
        ]);
        
        // Seta empresa ativa na sessão
        session(['active_company_id' => $this->company->id]);
        
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
    }

    /**
     * Testa criação de lançamento com dados válidos
     */
    public function test_can_create_lancamento_with_valid_data(): void
    {
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
            ->assertJson([
                'success' => true,
            ]);
        
        // Verifica se foi criado no banco
        $this->assertDatabaseHas('transacoes_financeiras', [
            'descricao' => 'Lançamento de Teste',
            'tipo' => 'entrada',
            'company_id' => $this->company->id,
        ]);
    }

    /**
     * Testa que descrição é obrigatória
     */
    public function test_requires_descricao(): void
    {
        $response = $this->actingAs($this->user)
            ->withSession(['active_company_id' => $this->company->id])
            ->post(route('banco.store'), [
                'data_competencia' => '17/01/2026',
                'tipo' => 'entrada',
                'descricao' => '', // Campo vazio
                'valor' => '1.500,00',
                'entidade_id' => $this->entidade->id,
                'lancamento_padrao_id' => $this->categoria->id,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['descricao']);
    }

    /**
     * Testa que valor é obrigatório e deve ser maior que zero
     */
    public function test_requires_valid_valor(): void
    {
        $response = $this->actingAs($this->user)
            ->withSession(['active_company_id' => $this->company->id])
            ->post(route('banco.store'), [
                'data_competencia' => '17/01/2026',
                'tipo' => 'entrada',
                'descricao' => 'Teste',
                'valor' => '0,00', // Valor zero
                'entidade_id' => $this->entidade->id,
                'lancamento_padrao_id' => $this->categoria->id,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['valor']);
    }

    /**
     * Testa que entidade financeira é obrigatória
     */
    public function test_requires_entidade_id(): void
    {
        $response = $this->actingAs($this->user)
            ->withSession(['active_company_id' => $this->company->id])
            ->post(route('banco.store'), [
                'data_competencia' => '17/01/2026',
                'tipo' => 'entrada',
                'descricao' => 'Teste',
                'valor' => '1.500,00',
                'entidade_id' => null, // Campo vazio
                'lancamento_padrao_id' => $this->categoria->id,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['entidade_id']);
    }

    /**
     * Testa criação de lançamento de saída (despesa)
     */
    public function test_can_create_lancamento_saida(): void
    {
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
            ->assertJson([
                'success' => true,
            ]);
        
        $this->assertDatabaseHas('transacoes_financeiras', [
            'descricao' => 'Despesa de Teste',
            'tipo' => 'saida',
        ]);
    }

    /**
     * Testa criação de lançamento com status "pago"
     */
    public function test_can_create_lancamento_pago(): void
    {
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
    }

    /**
     * Testa que usuário não autenticado não pode criar lançamento
     */
    public function test_guest_cannot_create_lancamento(): void
    {
        $response = $this->post(route('banco.store'), [
            'data_competencia' => '17/01/2026',
            'tipo' => 'entrada',
            'descricao' => 'Teste',
            'valor' => '1.000,00',
        ]);

        $response->assertRedirect(route('login'));
    }

    /**
     * Testa endpoint de summary para atualização das tabs
     */
    public function test_summary_endpoint_returns_valid_data(): void
    {
        // Cria alguns lançamentos para ter dados
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
                'tabs' => [
                    '*' => ['key', 'value']
                ],
                'sideCard' => [
                    'total_receitas',
                    'total_despesas',
                    'saldo',
                ],
                'meta' => [
                    'start_date',
                    'end_date',
                    'updated_at',
                ],
            ]);
    }
}
