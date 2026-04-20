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

    /**
     * Vincula a categoria a companies específicas via pivot (após criação).
     *
     * @param  array<int|\App\Models\Company>  $companies
     */
    public function forCompanies(array $companies): static
    {
        return $this->afterCreating(function (LancamentoPadrao $lp) use ($companies) {
            $ids = array_map(
                fn ($c) => $c instanceof Company ? $c->id : (int) $c,
                $companies,
            );
            $lp->companies()->sync($ids);
        });
    }

    /**
     * Atalho: categoria restrita a uma única company.
     */
    public function forCompany(Company|int $company): static
    {
        return $this->forCompanies([$company]);
    }
}
