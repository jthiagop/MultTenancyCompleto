<?php

namespace App\Models\Contabilide;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChartOfAccount extends Model
{
    use HasFactory;

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
        'parent_id',
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
}
