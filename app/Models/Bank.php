<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use HasFactory;

    protected $table = 'banks';
    protected $fillable = ['name', 'logo_path', 'compe_code'];

    /**
     * Diretório base dos logos de bancos (relativo à raiz pública).
     */
    protected const LOGO_BASE_PATH = '/tenancy/assets/media/svg/bancos/';

    /**
     * Accessor: Retorna a URL completa do logo do banco.
     * 
     * Aceita tanto o slug (brasil.svg) quanto o caminho completo legado.
     */
    public function getLogoUrlAttribute(): ?string
    {
        if (empty($this->logo_path)) {
            return null;
        }

        // Se já é um caminho completo (legado ou URL), retorna diretamente
        if (str_starts_with($this->logo_path, '/') || str_starts_with($this->logo_path, 'http')) {
            return $this->logo_path;
        }

        // Caso contrário, é um slug - gera o caminho completo
        return self::LOGO_BASE_PATH . $this->logo_path;
    }

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