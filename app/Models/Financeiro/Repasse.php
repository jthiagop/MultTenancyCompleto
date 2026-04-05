<?php

namespace App\Models\Financeiro;

use App\Models\Company;
use App\Models\EntidadeFinanceira;
use App\Models\FormasPagamento;
use App\Models\FormasRecebimento;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Repasse extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'repasses';

    protected $fillable = [
        'company_origem_id',
        'entidade_origem_id',
        'tipo',
        'criterio_rateio',
        'valor_total',
        'data_emissao',
        'data_entrada',
        'data_vencimento',
        'competencia',
        'tipo_documento',
        'numero_documento',
        'forma_pagamento_id',
        'forma_recebimento_id',
        'descricao',
        'status',
        'user_id',
    ];

    protected $casts = [
        'data_emissao' => \App\Casts\BrazilianDateCast::class,
        'data_entrada' => \App\Casts\BrazilianDateCast::class,
        'data_vencimento' => \App\Casts\BrazilianDateCast::class,
        'valor_total' => 'decimal:2',
    ];

    // ─── Relacionamentos ───

    public function companyOrigem(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_origem_id');
    }

    public function entidadeOrigem(): BelongsTo
    {
        return $this->belongsTo(EntidadeFinanceira::class, 'entidade_origem_id');
    }

    public function formaPagamento(): BelongsTo
    {
        return $this->belongsTo(FormasPagamento::class, 'forma_pagamento_id');
    }

    public function formaRecebimento(): BelongsTo
    {
        return $this->belongsTo(FormasRecebimento::class, 'forma_recebimento_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function itens(): HasMany
    {
        return $this->hasMany(RepasseItem::class, 'repasse_id');
    }

    // ─── Scopes ───

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_origem_id', $companyId);
    }

    public function scopePendentes($query)
    {
        return $query->where('status', 'pendente');
    }

    public function scopeExecutados($query)
    {
        return $query->where('status', 'executado');
    }

    // ─── Helpers ───

    public function isExecutado(): bool
    {
        return $this->status === 'executado';
    }

    public function isCancelado(): bool
    {
        return $this->status === 'cancelado';
    }

    public function isPendente(): bool
    {
        return $this->status === 'pendente';
    }

    /**
     * Retorna o label do status com badge class (Metronic).
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pendente' => 'badge-light-warning',
            'executado' => 'badge-light-success',
            'cancelado' => 'badge-light-danger',
            default => 'badge-light-secondary',
        };
    }
}
