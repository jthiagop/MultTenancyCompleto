<?php

namespace App\Models\Financeiro;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class CostCenter extends Model
{
    use HasFactory, SoftDeletes;

    // Nome da tabela (caso necessário especificar)
    // protected $table = 'cost_centers';

    // Caso a chave primária seja diferente de 'id', você pode especificar aqui
    // protected $primaryKey = 'id';

    /**
     * Define quais campos podem ser preenchidos em massa (Mass Assignment).
     */
    protected $fillable = [
        'code',
        'company_id',
        'name',
        'department_id',
        'manager_id',
        'status',
        'start_date',
        'end_date',
        'budget',
        'observations',
        'parent_id',
        'category',
        'created_by',
        'created_by_name',
        'updated_by',
        'updated_by_name',
    ];

    public function transacoesFinanceiras()
    {
        return $this->hasMany(TransacaoFinanceira::class, 'cost_center_id');
    }

    /**
     * Scope: Filtra a busca para incluir apenas os registros da empresa ativa na sessão.
     * Este é o método correto para lidar com o contexto da sessão.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForActiveCompany($query)
    {
        $activeCompanyId = session('active_company_id');

        // Se houver uma empresa ativa, aplica o filtro.
        if ($activeCompanyId) {
            return $query->where('company_id', $activeCompanyId);
        }

        // Se não houver, retorna uma consulta que não trará resultados.
        // Isso previne vazamento de dados caso a sessão se perca.
        return $query->whereRaw('1 = 0');
    }

    public static function getCadastroCentroCusto()
    {
        $userId = Auth::id(); // Recupere o ID do usuário logado

        // Exemplo de associação via "company_user"
        // Ajuste conforme a estrutura das suas tabelas e colunas
        $centrosAtivos = self::join('company_user', 'cost_centers.company_id', '=', 'company_user.company_id')
            ->where('company_user.user_id', $userId)
            ->where('cost_centers.status', 1) // 1 = Ativo
            ->select('cost_centers.*')       // Selecione as colunas de cost_centers
            ->get();

        return $centrosAtivos;
    }
}
