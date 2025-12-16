<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFavoriteRoute extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'company_id', 'route_name', 'display_name',
        'icon', 'module_key', 'order_index', 'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
        'order_index' => 'integer',
    ];

    // Scopes
    public function scopeForCurrentUser($query)
    {
        return $query->where('user_id', auth()->id())
            ->where('company_id', session('active_company_id'));
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order_index')->orderBy('display_name');
    }

    // Helper para verificar se usuário tem permissão
    public function userHasPermission(): bool
    {
        $permission = $this->module_key . '.index';
        return auth()->user()->can($permission);
    }
}
