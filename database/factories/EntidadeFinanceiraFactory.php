<?php

namespace Database\Factories;

use App\Models\EntidadeFinanceira;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EntidadeFinanceira>
 */
class EntidadeFinanceiraFactory extends Factory
{
    protected $model = EntidadeFinanceira::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome' => fake()->randomElement(['Conta Corrente', 'Conta Poupança', 'Caixa Interno', 'Banco Bradesco', 'Banco Itaú']),
            'tipo' => 'banco',
            'agencia' => fake()->numerify('####'),
            'conta' => fake()->numerify('######-#'),
            'account_type' => 'corrente',
            'saldo_inicial' => fake()->randomFloat(2, 1000, 50000),
            'saldo_atual' => fake()->randomFloat(2, 1000, 50000),
            'descricao' => fake()->optional()->sentence(),
            'company_id' => Company::factory(),
        ];
    }

    /**
     * Indica que a entidade é do tipo caixa.
     */
    public function caixa(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo' => 'caixa',
            'agencia' => null,
            'conta' => null,
        ]);
    }

    /**
     * Indica que a entidade é do tipo banco.
     */
    public function banco(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo' => 'banco',
        ]);
    }
}
