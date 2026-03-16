<?php

namespace App\Models\Financeiro;

use App\Models\EntidadeFinanceira;
use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transferencia extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'transferencias';

    protected $fillable = [
        'company_id',
        'entidade_origem_id',
        'entidade_destino_id',
        'valor',
        'data',
        'descricao',
        'user_id',
    ];

    protected $casts = [
        'data' => \App\Casts\BrazilianDateCast::class,
        'valor' => 'decimal:2',
    ];

    /**
     * Conta de origem (de onde o valor sai)
     */
    public function origem(): BelongsTo
    {
        return $this->belongsTo(EntidadeFinanceira::class, 'entidade_origem_id');
    }

    /**
     * Conta de destino (para onde o valor vai)
     */
    public function destino(): BelongsTo
    {
        return $this->belongsTo(EntidadeFinanceira::class, 'entidade_destino_id');
    }

    /**
     * Transações financeiras geradas por esta transferência (saída + entrada)
     */
    public function transacoes(): HasMany
    {
        return $this->hasMany(TransacaoFinanceira::class, 'transferencia_id');
    }

    /**
     * Usuário que realizou a transferência
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Mutator para garantir que valor sempre seja absoluto (positivo)
     */
    protected function valor(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => abs((float) $value),
        );
    }

    /**
     * Scope: Filtra pela empresa ativa na sessão
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
