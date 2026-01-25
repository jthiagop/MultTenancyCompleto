<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parceiro extends Model
{
    protected $table = 'parceiros';
    
    protected $fillable = ['nome', 'cnpj', 'telefone', 'email', 'company_id'];

    /**
     * Scope: Filtra a busca para incluir apenas os registros da empresa ativa na sessão.
     */
    public function scopeForActiveCompany($query)
    {
        $activeCompanyId = session('active_company_id');

        if ($activeCompanyId) {
            return $query->where('company_id', $activeCompanyId);
        }

        return $query->whereRaw('1 = 0');
    }
}
