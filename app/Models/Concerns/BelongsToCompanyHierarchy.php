<?php

namespace App\Models\Concerns;

use App\Models\Company;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Trait reutilizável para recursos destinados a "uma ou mais companies"
 * dentro de um mesmo tenant, com regras de herança matriz -> filiais.
 *
 * Convenção:
 *  - pivot vazio  => recurso GLOBAL (visível em todas as companies do tenant)
 *  - pivot com a company ativa      => visível
 *  - pivot com a matriz da company ativa (companies.parent_id) => visível
 *  - caso contrário                 => invisível
 *
 * O nome da tabela-pivot é deduzido do model (`{table_singular}_company`) e
 * pode ser sobrescrito declarando `protected string $companyPivotTable` no
 * model que usa o trait.
 */
trait BelongsToCompanyHierarchy
{
    /**
     * Relacionamento N:N com companies via tabela pivot.
     */
    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(
            Company::class,
            $this->resolveCompanyPivotTable(),
        )->withTimestamps();
    }

    /**
     * Filtra registros visíveis para a company ativa (ou para `$companyId`,
     * se fornecido). Aplica a herança matriz -> filiais automaticamente.
     */
    public function scopeForActiveCompany(Builder $query, ?int $companyId = null): Builder
    {
        $companyId = $companyId ?? (int) session('active_company_id');

        // Sem company ativa, só retorna os globais (pivot vazio).
        if (! $companyId) {
            return $query->whereDoesntHave('companies');
        }

        $parentId = Company::whereKey($companyId)->value('parent_id');

        return $query->where(function (Builder $q) use ($companyId, $parentId) {
            // Globais do tenant
            $q->whereDoesntHave('companies')
                // Explicitamente ligadas à company ativa
                ->orWhereHas('companies', fn (Builder $c) => $c->where('companies.id', $companyId));

            if ($parentId) {
                // Herança: a matriz compartilha com todas as filiais
                $q->orWhereHas(
                    'companies',
                    fn (Builder $c) => $c->where('companies.id', $parentId),
                );
            }
        });
    }

    /**
     * Retorna apenas registros globais (pivot vazio).
     */
    public function scopeGlobal(Builder $query): Builder
    {
        return $query->whereDoesntHave('companies');
    }

    /**
     * Restringe a registros ligados à company informada via pivot
     * (não inclui globais nem herança).
     */
    public function scopeOwnedBy(Builder $query, int $companyId): Builder
    {
        return $query->whereHas(
            'companies',
            fn (Builder $c) => $c->where('companies.id', $companyId),
        );
    }

    /**
     * Classifica a visibilidade deste registro sob a ótica de uma company.
     *
     *  - 'global'    : pivot vazio
     *  - 'own'       : company ativa está no pivot
     *  - 'inherited' : matriz da company ativa está no pivot
     *  - 'other'     : nenhum dos casos acima (não deveria aparecer quando
     *                  filtrado por forActiveCompany)
     */
    public function scopeClassifyFor(Builder $query, ?int $companyId = null): Builder
    {
        return $query;
    }

    public function scopeClassificacao(?int $companyId = null): string
    {
        return $this->classificacaoParaCompany($companyId);
    }

    /**
     * Classifica a visibilidade do registro carregado.
     * Usa `companies` eager-loaded se disponível (evita N+1).
     */
    public function classificacaoParaCompany(?int $companyId = null): string
    {
        $companyId = $companyId ?? (int) session('active_company_id');

        $ids = $this->relationLoaded('companies')
            ? $this->companies->pluck('id')->map(fn ($v) => (int) $v)->all()
            : $this->companies()->pluck('companies.id')->map(fn ($v) => (int) $v)->all();

        if (empty($ids)) {
            return 'global';
        }

        if ($companyId && in_array($companyId, $ids, true)) {
            return 'own';
        }

        $parentId = $companyId
            ? (int) (Company::whereKey($companyId)->value('parent_id') ?? 0)
            : 0;

        if ($parentId && in_array($parentId, $ids, true)) {
            return 'inherited';
        }

        return 'other';
    }

    /**
     * Atalho "firstOrCreate" ciente do pivot: tenta localizar um registro
     * ligado à company informada que case com `$attributes`; se não houver,
     * cria o registro (sem company_id na coluna) e vincula ao pivot.
     *
     * @param  array<string, mixed>  $attributes  Condições para a busca e valores da criação
     * @param  array<string, mixed>  $values      Valores adicionais usados apenas na criação
     */
    public static function firstOrCreateForCompany(
        int $companyId,
        array $attributes,
        array $values = [],
    ): static {
        $existing = static::query()
            ->ownedBy($companyId)
            ->where($attributes)
            ->first();

        if ($existing) {
            return $existing;
        }

        $record = static::create($attributes + $values);
        $record->syncCompanyHierarchy([$companyId]);

        return $record;
    }

    /**
     * Sincroniza o pivot validando que todos os ids pertencem ao tenant atual.
     *
     * @param  array<int|string>  $companyIds
     * @return array{attached: array<int>, detached: array<int>, updated: array<int>}
     */
    public function syncCompanyHierarchy(array $companyIds): array
    {
        $ids = collect($companyIds)
            ->map(fn ($v) => (int) $v)
            ->filter()
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return $this->companies()->sync([]);
        }

        $valid = Company::whereIn('id', $ids)->pluck('id')->all();

        return $this->companies()->sync($valid);
    }

    /**
     * Nome da tabela pivot para o model que usa este trait.
     * Pode ser sobrescrito declarando a propriedade `$companyPivotTable`.
     */
    protected function resolveCompanyPivotTable(): string
    {
        if (property_exists($this, 'companyPivotTable') && is_string($this->companyPivotTable)) {
            return $this->companyPivotTable;
        }

        // Deriva de `lancamento_padraos` -> `lancamento_padrao_company`
        $base = $this->getTable();
        $singular = \Illuminate\Support\Str::singular($base);

        return $singular . '_company';
    }
}
