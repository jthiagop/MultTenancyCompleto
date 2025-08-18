<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CadastroBanco extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'created_by',
        'banco',
        'name',
        'agencia',
        'conta',
        'digito',
        'account_type',
        'description',
    ];

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
