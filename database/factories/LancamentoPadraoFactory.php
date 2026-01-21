<?php

namespace Database\Factories;

use App\Models\LancamentoPadrao;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LancamentoPadrao>
 */
class LancamentoPadraoFactory extends Factory
{
    protected $model = LancamentoPadrao::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = [
            'Administrativo', 'Alimentação', 'Cerimônias', 'Comércio', 
            'Comunicação', 'Contribuições', 'Doações', 'Educação',
            'Equipamentos', 'Eventos', 'Liturgia', 'Manutenção',
            'Pessoal', 'Rendimentos', 'Saúde', 'Transporte',
        ];

        return [
            'type' => fake()->randomElement(['entrada', 'saida']),
            'description' => fake()->sentence(3),
            'category' => fake()->randomElement($categories),
            'company_id' => Company::factory(),
        ];
    }

    /**
     * Indica que é categoria de entrada (receita).
     */
    public function entrada(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'entrada',
        ]);
    }

    /**
     * Indica que é categoria de saída (despesa).
     */
    public function saida(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'saida',
        ]);
    }
}
