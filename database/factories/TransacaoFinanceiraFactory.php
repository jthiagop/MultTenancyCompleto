<?php

namespace Database\Factories;

use App\Models\Financeiro\TransacaoFinanceira;
use App\Models\Company;
use App\Models\EntidadeFinanceira;
use App\Models\LancamentoPadrao;
use App\Enums\SituacaoTransacao;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Financeiro\TransacaoFinanceira>
 */
class TransacaoFinanceiraFactory extends Factory
{
    protected $model = TransacaoFinanceira::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tipo = fake()->randomElement(['entrada', 'saida']);
        $valor = fake()->randomFloat(2, 50, 5000);

        return [
            'company_id' => Company::factory(),
            'data_competencia' => fake()->dateTimeBetween('-1 month', '+1 month'),
            'data_vencimento' => fake()->dateTimeBetween('-1 month', '+1 month'),
            'tipo' => $tipo,
            'valor' => $valor,
            'descricao' => fake()->sentence(4),
            'entidade_id' => EntidadeFinanceira::factory(),
            'lancamento_padrao_id' => LancamentoPadrao::factory()->state(['type' => $tipo]),
            'tipo_documento' => fake()->randomElement(['pix', 'boleto', 'dinheiro', 'cartao_debito', 'cartao_credito', 'transferencia']),
            'situacao' => SituacaoTransacao::EM_ABERTO,
            'origem' => 'manual',
        ];
    }

    /**
     * Transação do tipo entrada (receita).
     */
    public function entrada(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo' => 'entrada',
        ]);
    }

    /**
     * Transação do tipo saída (despesa).
     */
    public function saida(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo' => 'saida',
        ]);
    }

    /**
     * Transação em aberto.
     */
    public function emAberto(): static
    {
        return $this->state(fn (array $attributes) => [
            'situacao' => SituacaoTransacao::EM_ABERTO,
            'valor_pago' => null,
            'data_pagamento' => null,
        ]);
    }

    /**
     * Transação paga/recebida.
     */
    public function paga(): static
    {
        return $this->state(fn (array $attributes) => [
            'situacao' => SituacaoTransacao::PAGO,
            'valor_pago' => $attributes['valor'] ?? fake()->randomFloat(2, 50, 5000),
            'data_pagamento' => now(),
        ]);
    }

    /**
     * Transação recebida (para entradas).
     */
    public function recebida(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo' => 'entrada',
            'situacao' => SituacaoTransacao::RECEBIDO,
            'valor_pago' => $attributes['valor'] ?? fake()->randomFloat(2, 50, 5000),
            'data_pagamento' => now(),
        ]);
    }

    /**
     * Transação atrasada.
     */
    public function atrasada(): static
    {
        return $this->state(fn (array $attributes) => [
            'situacao' => SituacaoTransacao::ATRASADO,
            'data_vencimento' => fake()->dateTimeBetween('-2 months', '-1 day'),
            'valor_pago' => null,
            'data_pagamento' => null,
        ]);
    }
}
