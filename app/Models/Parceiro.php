<?php

namespace App\Models;

use App\Enums\NaturezaParceiro;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Vinkla\Hashids\Facades\Hashids;

class Parceiro extends Model
{
    use SoftDeletes;

    protected $table = 'parceiros';
    
    protected $fillable = [
        'nome', 'nome_fantasia', 'tipo', 'natureza', 'cnpj', 'cpf', 
        'telefone', 'email', 'active', 'observacoes',
        'company_id', 'address_id', 
        'created_by', 'created_by_name',
        'updated_by', 'updated_by_name',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

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

    /**
     * Scope: Filtra por natureza (fornecedor, cliente, etc.)
     */
    public function scopeNatureza($query, string $natureza)
    {
        return $query->where('natureza', $natureza);
    }

    /**
     * Scope: Filtra por tipo de pessoa (pj, pf, ambos)
     */
    public function scopeTipo($query, string $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    /**
     * Scope: Apenas ativos
     */
    public function scopeAtivos($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope: Apenas inativos
     */
    public function scopeInativos($query)
    {
        return $query->where('active', false);
    }

    /**
     * Retorna o documento formatado (CNPJ ou CPF)
     */
    public function getDocumentoAttribute(): ?string
    {
        if ($this->cnpj && strlen($this->cnpj) > 11) {
            return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $this->cnpj);
        }
        if ($this->cnpj && strlen($this->cnpj) <= 11) {
            return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $this->cnpj);
        }
        if ($this->cpf) {
            return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $this->cpf);
        }
        return null;
    }

    /**
     * Retorna label legível do tipo de pessoa
     */
    public function getTipoLabelAttribute(): string
    {
        return match ($this->tipo) {
            'pj' => 'Pessoa Jurídica',
            'pf' => 'Pessoa Física',
            'ambos' => 'PJ e PF',
            default => ucfirst($this->tipo ?? 'Não definido'),
        };
    }

    /**
     * Retorna label legível da natureza (usa Enum com fallback)
     */
    public function getNaturezaLabelAttribute(): string
    {
        return NaturezaParceiro::labelFor($this->natureza);
    }

    /**
     * Retorna badge class da natureza
     */
    public function getNaturezaBadgeClassAttribute(): string
    {
        return NaturezaParceiro::badgeClassFor($this->natureza);
    }

    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id');
    }

    public function transacoes()
    {
        return $this->hasMany(\App\Models\Financeiro\TransacaoFinanceira::class, 'parceiro_id');
    }

    /**
     * 1. O Laravel usa isso para gerar a URL (route('parceiros.show', $parceiro))
     */
    public function getRouteKey()
    {
        return Hashids::encode($this->getKey());
    }

    /**
     * 2. O Laravel usa isso para encontrar o model vindo da URL
     */
    public function resolveRouteBinding($value, $field = null)
    {
        $decoded = Hashids::decode($value);
        return $this->where('id', $decoded[0] ?? null)->firstOrFail();
    }
}
