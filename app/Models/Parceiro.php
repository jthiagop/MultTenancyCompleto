<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parceiro extends Model
{
    protected $table = 'parceiros';
    
    protected $fillable = ['nome', 'nome_fantasia', 'cnpj', 'telefone', 'email', 'company_id', 'address_id', 'created_by', 'created_by_name'];

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

    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id');
    }
}
