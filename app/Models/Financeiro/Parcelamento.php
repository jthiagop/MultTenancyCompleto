<?php

namespace App\Models\Financeiro;

use App\Models\EntidadeFinanceira;
use App\Models\Movimentacao;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Parcelamento extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'parcelamentos';

    protected $fillable = [
        'transacao_financeira_id',
        'transacao_parcela_id', // A transação filha (parcela individual)
        'numero_parcela',
        'total_parcelas',
        'data_vencimento',
        'data_pagamento',
        'valor',
        'percentual',
        'valor_pago',
        'juros',
        'multa',
        'desconto',
        'entidade_id',
        'conta_pagamento_id',
        'descricao',
        'situacao',
        'agendado',
        'movimentacao_id',
        'created_by',
        'created_by_name',
        'updated_by',
        'updated_by_name',
    ];

    protected $casts = [
        'data_vencimento' => \App\Casts\BrazilianDateCast::class,
        'data_pagamento' => \App\Casts\BrazilianDateCast::class,
        'valor' => 'decimal:2',
        'percentual' => 'decimal:2',
        'valor_pago' => 'decimal:2',
        'juros' => 'decimal:2',
        'multa' => 'decimal:2',
        'desconto' => 'decimal:2',
        'agendado' => 'boolean',
    ];

    /**
     * Relacionamento com a transação financeira pai
     */
    public function transacaoFinanceira(): BelongsTo
    {
        return $this->belongsTo(TransacaoFinanceira::class, 'transacao_financeira_id');
    }

    /**
     * Relacionamento com a transação financeira filha (a parcela como TransacaoFinanceira)
     */
    public function transacaoParcela(): BelongsTo
    {
        return $this->belongsTo(TransacaoFinanceira::class, 'transacao_parcela_id');
    }

    /**
     * Relacionamento com a entidade financeira (forma de pagamento)
     */
    public function entidadeFinanceira(): BelongsTo
    {
        return $this->belongsTo(EntidadeFinanceira::class, 'entidade_id');
    }

    /**
     * Relacionamento com a conta de pagamento
     */
    public function contaPagamento(): BelongsTo
    {
        return $this->belongsTo(EntidadeFinanceira::class, 'conta_pagamento_id');
    }

    /**
     * Relacionamento com a movimentação (quando paga)
     */
    public function movimentacao(): BelongsTo
    {
        return $this->belongsTo(Movimentacao::class, 'movimentacao_id');
    }

    /**
     * Relacionamento com o usuário que criou
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relacionamento com o usuário que atualizou
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Retorna o rótulo da parcela (ex: "1/3", "2/3")
     */
    public function getLabelParcelaAttribute(): string
    {
        return "{$this->numero_parcela}/{$this->total_parcelas}";
    }

    /**
     * Verifica se a parcela está atrasada
     */
    public function getEstaAtrasadaAttribute(): bool
    {
        if (in_array($this->situacao, ['pago', 'recebido'])) {
            return false;
        }

        $vencimento = Carbon::parse($this->data_vencimento);
        return $vencimento->isPast();
    }

    /**
     * Calcula o valor total a pagar (valor + juros + multa - desconto)
     */
    public function getValorTotalAPagarAttribute(): float
    {
        return (float) $this->valor + (float) $this->juros + (float) $this->multa - (float) $this->desconto;
    }

    /**
     * Calcula o valor em aberto (valor total - valor pago)
     */
    public function getValorEmAbertoAttribute(): float
    {
        $totalAPagar = $this->valor_total_a_pagar;
        return max(0, $totalAPagar - (float) $this->valor_pago);
    }

    /**
     * Verifica se está totalmente paga
     */
    public function getEstaPagaAttribute(): bool
    {
        return in_array($this->situacao, ['pago', 'recebido']);
    }

    /**
     * Scope para parcelas em aberto
     */
    public function scopeEmAberto($query)
    {
        return $query->where('situacao', 'em_aberto');
    }

    /**
     * Scope para parcelas atrasadas
     */
    public function scopeAtrasadas($query)
    {
        return $query->where('situacao', 'em_aberto')
            ->where('data_vencimento', '<', now()->format('Y-m-d'));
    }

    /**
     * Scope para parcelas pagas
     */
    public function scopePagas($query)
    {
        return $query->whereIn('situacao', ['pago', 'recebido']);
    }

    /**
     * Scope para parcelas a vencer em X dias
     */
    public function scopeAVencerEm($query, int $dias)
    {
        return $query->where('situacao', 'em_aberto')
            ->whereBetween('data_vencimento', [
                now()->format('Y-m-d'),
                now()->addDays($dias)->format('Y-m-d')
            ]);
    }

    /**
     * Scope para parcelas de uma transação específica
     */
    public function scopeDeTransacao($query, int $transacaoId)
    {
        return $query->where('transacao_financeira_id', $transacaoId);
    }
}
