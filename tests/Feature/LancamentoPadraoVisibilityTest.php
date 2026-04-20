<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\LancamentoPadrao;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Garante as regras de visibilidade descritas em
 * `app/Models/Concerns/BelongsToCompanyHierarchy.php`:
 *
 *  - pivot vazio   => categoria global do tenant
 *  - pivot com a company ativa      => visível
 *  - pivot com a matriz da company  => visível (herança)
 *  - caso contrário                 => invisível
 */
class LancamentoPadraoVisibilityTest extends TestCase
{
    use RefreshDatabase;

    protected Company $matriz;
    protected Company $filial;
    protected Company $outraMatriz;
    protected Company $filialDeOutraMatriz;

    protected function setUp(): void
    {
        parent::setUp();

        $this->matriz = Company::factory()->create(['type' => 'matriz']);
        $this->filial = Company::factory()->create([
            'type' => 'filial',
            'parent_id' => $this->matriz->id,
        ]);
        $this->outraMatriz = Company::factory()->create(['type' => 'matriz']);
        $this->filialDeOutraMatriz = Company::factory()->create([
            'type' => 'filial',
            'parent_id' => $this->outraMatriz->id,
        ]);
    }

    public function test_categoria_global_visivel_em_matriz_e_filial(): void
    {
        $global = LancamentoPadrao::factory()->create();

        $this->assertTrue(
            LancamentoPadrao::forActiveCompany($this->matriz->id)
                ->whereKey($global->id)->exists(),
            'categoria global deve ser visível na matriz'
        );

        $this->assertTrue(
            LancamentoPadrao::forActiveCompany($this->filial->id)
                ->whereKey($global->id)->exists(),
            'categoria global deve ser visível na filial'
        );
    }

    public function test_categoria_da_matriz_visivel_na_matriz_e_suas_filiais(): void
    {
        $lp = LancamentoPadrao::factory()->forCompany($this->matriz)->create();

        $this->assertTrue(
            LancamentoPadrao::forActiveCompany($this->matriz->id)
                ->whereKey($lp->id)->exists(),
            'visível na própria matriz'
        );

        $this->assertTrue(
            LancamentoPadrao::forActiveCompany($this->filial->id)
                ->whereKey($lp->id)->exists(),
            'visível na filial por herança'
        );

        $this->assertFalse(
            LancamentoPadrao::forActiveCompany($this->outraMatriz->id)
                ->whereKey($lp->id)->exists(),
            'não deve aparecer em outra matriz'
        );

        $this->assertFalse(
            LancamentoPadrao::forActiveCompany($this->filialDeOutraMatriz->id)
                ->whereKey($lp->id)->exists(),
            'não deve aparecer em filial de outra matriz'
        );
    }

    public function test_categoria_da_filial_visivel_somente_nela(): void
    {
        $lp = LancamentoPadrao::factory()->forCompany($this->filial)->create();

        $this->assertTrue(
            LancamentoPadrao::forActiveCompany($this->filial->id)
                ->whereKey($lp->id)->exists(),
            'visível na própria filial'
        );

        $this->assertFalse(
            LancamentoPadrao::forActiveCompany($this->matriz->id)
                ->whereKey($lp->id)->exists(),
            'não é visível na matriz (matriz não herda da filial)'
        );

        $this->assertFalse(
            LancamentoPadrao::forActiveCompany($this->filialDeOutraMatriz->id)
                ->whereKey($lp->id)->exists(),
            'não aparece em filial de outra hierarquia'
        );
    }

    public function test_classificacao_para_company_respeita_pivot(): void
    {
        $global = LancamentoPadrao::factory()->create();
        $daMatriz = LancamentoPadrao::factory()->forCompany($this->matriz)->create();
        $daFilial = LancamentoPadrao::factory()->forCompany($this->filial)->create();

        $this->assertSame('global',    $global->classificacaoParaCompany($this->filial->id));
        $this->assertSame('inherited', $daMatriz->classificacaoParaCompany($this->filial->id));
        $this->assertSame('own',       $daFilial->classificacaoParaCompany($this->filial->id));
        $this->assertSame('own',       $daMatriz->classificacaoParaCompany($this->matriz->id));
    }

    public function test_sync_company_hierarchy_aceita_ids_validos(): void
    {
        $lp = LancamentoPadrao::factory()->create();

        $lp->syncCompanyHierarchy([$this->matriz->id, $this->filial->id]);

        $this->assertEqualsCanonicalizing(
            [$this->matriz->id, $this->filial->id],
            $lp->companies()->pluck('companies.id')->map(fn ($v) => (int) $v)->all(),
        );

        // Remove uma das companies e garante que só a outra permanece.
        $lp->syncCompanyHierarchy([$this->filial->id]);
        $this->assertSame(
            [$this->filial->id],
            $lp->companies()->pluck('companies.id')->map(fn ($v) => (int) $v)->all(),
        );

        // Array vazio => categoria volta a ser global (pivot vazio).
        $lp->syncCompanyHierarchy([]);
        $this->assertSame(0, $lp->companies()->count());
    }

    public function test_scope_global_filtra_apenas_categorias_sem_pivot(): void
    {
        $global = LancamentoPadrao::factory()->create();
        LancamentoPadrao::factory()->forCompany($this->matriz)->create();

        $globais = LancamentoPadrao::global()->pluck('id')->all();

        $this->assertContains($global->id, $globais);
        $this->assertCount(1, $globais);
    }

    public function test_matriz_pode_criar_categoria_visivel_na_filial_sem_criar_duplicata(): void
    {
        $user = User::factory()->create(['company_id' => $this->matriz->id]);

        // Simula controller: cria LP e vincula ao pivot da matriz
        $lp = LancamentoPadrao::create([
            'type' => 'saida',
            'description' => 'Energia Elétrica',
            'user_id' => $user->id,
        ]);
        $lp->syncCompanyHierarchy([$this->matriz->id]);

        // Para a filial, aparece como herdada (sem duplicação)
        $visiveisNaFilial = LancamentoPadrao::forActiveCompany($this->filial->id)->count();
        $this->assertSame(1, $visiveisNaFilial);
    }
}
