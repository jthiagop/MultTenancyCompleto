<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use HasFactory;

    protected $table = 'banks';
    protected $fillable = ['name', 'logo_path'];

    /**
     * Um banco (instituição) pode ter muitas contas (entidades financeiras).
     */
    public function contas()
    {
        return $this->hasMany(EntidadeFinanceira::class, 'banco_id');
    }

    // Defina a relação com o modelo User
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
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

        // Não retorna nada se nenhuma empresa estiver ativa
        return $query->whereRaw('1 = 0');
    }
}