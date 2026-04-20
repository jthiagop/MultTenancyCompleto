<?php

namespace Tests\Feature;

use App\Enums\StatusDomusDocumento;
use App\Models\Company;
use App\Models\DomusDocumento;
use App\Models\Financeiro\EntidadeFinanceira;
use App\Models\Financeiro\ModulosAnexo;
use App\Models\Financeiro\TransacaoFinanceira;
use App\Models\LancamentoPadrao;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReactBancoLancamentoDomusTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Company $company;

    protected EntidadeFinanceira $entidade;

    protected LancamentoPadrao $categoriaSaida;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::factory()->create();
        $this->user = User::factory()->create([
            'company_id' => $this->company->id,
        ]);
        $this->entidade = EntidadeFinanceira::factory()->create([
            'company_id' => $this->company->id,
            'tipo' => 'banco',
        ]);
        $this->categoriaSaida = LancamentoPadrao::factory()
            ->saida()
            ->forCompany($this->company)
            ->create();
    }

    public function test_store_lancamento_com_domus_documento_id_marca_lancado_e_cria_anexo(): void
    {
        $domus = DomusDocumento::create([
            'nome_arquivo' => 'doc-teste.pdf',
            'caminho_arquivo' => 'domus_documentos/doc-teste.pdf',
            'tipo_arquivo' => 'PDF',
            'mime_type' => 'application/pdf',
            'tamanho_arquivo' => 1024,
            'company_id' => $this->company->id,
            'user_id' => $this->user->id,
            'user_name' => 'Tester',
            'status' => StatusDomusDocumento::PROCESSADO,
            'canal_origem' => 'upload',
        ]);

        $response = $this->actingAs($this->user)
            ->withSession(['active_company_id' => $this->company->id])
            ->postJson(route('react.banco.lancamento.store'), [
                'tipo' => 'despesa',
                'descricao' => 'Despesa via Domus teste',
                'valor' => 150.5,
                'data_competencia' => '2026-04-08',
                'entidade_id' => (string) $this->entidade->id,
                'lancamento_padrao_id' => (string) $this->categoriaSaida->id,
                'tipo_documento' => 'boleto',
                'domus_documento_id' => $domus->id,
            ]);

        $response->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('domus_documento_id', $domus->id);

        $domus->refresh();
        $this->assertSame(StatusDomusDocumento::LANCADO->value, $domus->status->value);

        $transacaoId = (int) $response->json('id');
        $this->assertGreaterThan(0, $transacaoId);

        $this->assertDatabaseHas('modulos_anexos', [
            'anexavel_id' => $transacaoId,
            'anexavel_type' => TransacaoFinanceira::class,
            'nome_arquivo' => 'doc-teste.pdf',
        ]);

        $count = ModulosAnexo::query()
            ->where('anexavel_id', $transacaoId)
            ->where('anexavel_type', TransacaoFinanceira::class)
            ->count();
        $this->assertSame(1, $count);
    }

    public function test_domus_documento_id_de_outra_company_e_rejeitado(): void
    {
        $outraCompany = Company::factory()->create();
        $domus = DomusDocumento::create([
            'nome_arquivo' => 'outro.pdf',
            'caminho_arquivo' => 'domus_documentos/outro.pdf',
            'tipo_arquivo' => 'PDF',
            'company_id' => $outraCompany->id,
            'user_id' => $this->user->id,
            'user_name' => 'Tester',
            'status' => StatusDomusDocumento::PROCESSADO,
            'canal_origem' => 'upload',
        ]);

        $response = $this->actingAs($this->user)
            ->withSession(['active_company_id' => $this->company->id])
            ->postJson(route('react.banco.lancamento.store'), [
                'tipo' => 'despesa',
                'descricao' => 'Teste validação Domus',
                'valor' => 10,
                'data_competencia' => '2026-04-08',
                'entidade_id' => (string) $this->entidade->id,
                'lancamento_padrao_id' => (string) $this->categoriaSaida->id,
                'tipo_documento' => 'pix',
                'domus_documento_id' => $domus->id,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['domus_documento_id']);
    }
}
