<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Module extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
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
     * Relacionamento com Company
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Retorna as permissões relacionadas a este módulo.
     * Baseado na convenção de nome (ex: módulo 'financeiro' busca 'financeiro.%')
     */
    public function getRelatedPermissionsAttribute()
    {
        // Se a chave do módulo for 'financeiro', buscamos permissões que começam com 'financeiro.'
        $prefix = $this->key;

        return \Spatie\Permission\Models\Permission::where('name', 'LIKE', "{$prefix}.%")->get();
    }

    /**
     * Scope para filtrar por company_id
     * Inclui módulos globais (sem company_id) e módulos específicos da empresa
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->where(function ($q) use ($companyId) {
            $q->where('company_id', $companyId)
              ->orWhereNull('company_id'); // Módulos globais (sem company_id) são visíveis para todos
        });
    }
}
