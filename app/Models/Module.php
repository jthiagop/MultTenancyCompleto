<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Module extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'key',
        'name',
        'route_name',
        'icon_path',
        'icon_class',
        'permission',
        'description',
        'is_active',
        'order_index',
        'show_on_dashboard',
        'metadata',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'show_on_dashboard' => 'boolean',
        'metadata' => 'array',
        'order_index' => 'integer',
    ];

    /**
     * Scope para módulos ativos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para módulos que devem aparecer no dashboard
     */
    public function scopeForDashboard($query)
    {
        return $query->where('show_on_dashboard', true);
    }

    /**
     * Scope para ordenação
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order_index')->orderBy('name');
    }

    /**
     * Scope para filtrar módulos disponíveis para uma company.
     *
     * Lógica opt-out: por padrão, todos os módulos ativos estão disponíveis.
     * Exclui apenas módulos que tenham registro na pivot com is_active = false.
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->where(function ($q) use ($companyId) {
            $q->whereDoesntHave('companies', function ($pivot) use ($companyId) {
                $pivot->where('company_id', $companyId)
                      ->where('company_module.is_active', false);
            });
        });
    }

    /**
     * Verifica se o usuário tem permissão para acessar o módulo
     */
    public function userHasPermission($user): bool
    {
        // Super usuários com role 'global' têm acesso a todos os módulos
        if ($user->hasRole('global')) {
            return true;
        }

        // Se o módulo não tem permissão específica, permite acesso
        if (!$this->permission) {
            return true;
        }

        // Verifica se o usuário tem a permissão específica
        return $user->can($this->permission);
    }

    /**
     * Verifica se o módulo está ativo para uma company específica.
     * Se não houver registro na pivot, o módulo está ativo (opt-out).
     */
    public function isActiveForCompany(int $companyId): bool
    {
        $pivot = $this->companies()->where('company_id', $companyId)->first();

        // Sem registro na pivot = ativo (padrão)
        if (!$pivot) {
            return $this->is_active;
        }

        return (bool) $pivot->pivot->is_active;
    }

    /**
     * Relacionamento many-to-many com Company (pivot de configuração).
     * A pivot controla desativações por company (opt-out).
     */
    public function companies()
    {
        return $this->belongsToMany(Company::class, 'company_module')
            ->withPivot('is_active', 'settings')
            ->withTimestamps();
    }

    /**
     * Retorna as permissões relacionadas a este módulo.
     * Baseado na convenção de nome (ex: módulo 'financeiro' busca 'financeiro.%')
     */
    public function getRelatedPermissionsAttribute()
    {
        $prefix = $this->key;

        return \Spatie\Permission\Models\Permission::where('name', 'LIKE', "{$prefix}.%")->get();
    }
}
