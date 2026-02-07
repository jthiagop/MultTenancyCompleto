<?php

namespace App\Models\Contabilide;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChartOfAccount extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'chart_of_accounts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'code',
        'name',
        'type',
        'is_analytical',
        'is_deductible',
        'external_code',
        'parent_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_analytical' => 'boolean',
        'is_deductible' => 'boolean',
    ];

    /**
     * Relação: A conta pai desta conta (se houver).
     */
    public function parent()
    {
        return $this->belongsTo(ChartOfAccount::class, 'parent_id');
    }

    /**
     * Relação: As contas filhas desta conta.
     */
    public function children()
    {
        return $this->hasMany(ChartOfAccount::class, 'parent_id');
    }

    /**
     * Scope: Filtra a busca para incluir apenas os registros da empresa ativa na sessão.
     */
    public function scopeForActiveCompany($query)
    {
        $activeCompanyId = session('active_company_id');

        if ($activeCompanyId) {
            return $query->where('company_id', $activeCompanyId);
        }

        // Não retorna nada se nenhuma empresa estiver ativa para proteger os dados.
        return $query->whereRaw('1 = 0');
    }

        /**
     * Accessor: Retorna a classe CSS do badge com base no tipo da conta.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function badgeClass(): Attribute
    {
        return Attribute::make(
            get: fn () => [
                'receita' => 'badge-light-success',
                'despesa' => 'badge-light-danger',
                'ativo' => 'badge-light-primary',
                'passivo' => 'badge-light-warning',
                'patrimonio_liquido' => 'badge-light-info',
            ][$this->type] ?? 'badge-light-secondary', // Retorna uma cor padrão se o tipo não for encontrado
        );
    }

    /**
     * Accessor: Retorna o nome do tipo formatado para exibição.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function formattedType(): Attribute
    {
        return Attribute::make(
            get: fn () => ucfirst(str_replace('_', ' ', $this->type)),
        );
    }

    /**
     * Accessor: Retorna a classificação (Sintética ou Analítica) formatada.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function classification(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->is_analytical ? 'Analítica' : 'Sintética',
        );
    }

    /**
     * Accessor: Retorna a classe CSS do badge de classificação.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function classificationBadgeClass(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->is_analytical ? 'badge-light-success' : 'badge-light-warning',
        );
    }

    /**
     * Scope: Filtra apenas contas analíticas (que aceitam lançamentos).
     */
    public function scopeAnalytical($query)
    {
        return $query->where('is_analytical', true);
    }

    /**
     * Scope: Filtra apenas contas sintéticas (grupos).
     */
    public function scopeSynthetic($query)
    {
        return $query->where('is_analytical', false);
    }

    /**
     * Verifica se a conta pode receber lançamentos.
     */
    public function allowsPosting(): bool
    {
        return $this->is_analytical;
    }
}
