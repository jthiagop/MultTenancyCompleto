<?php

namespace App\Models\Financeiro;

use App\Models\Company;
use App\Models\EntidadeFinanceira;
use App\Models\Movimentacao;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RepasseItem extends Model
{
    protected $table = 'repasse_itens';

    protected $fillable = [
        'repasse_id',
        'company_destino_id',
        'entidade_destino_id',
        'percentual',
        'valor',
        'transacao_saida_id',
        'transacao_entrada_id',
        'movimentacao_saida_id',
        'movimentacao_entrada_id',
    ];

    protected $casts = [
        'percentual' => 'decimal:2',
        'valor' => 'decimal:2',
    ];

    // ─── Relacionamentos ───

    public function repasse(): BelongsTo
    {
        return $this->belongsTo(Repasse::class, 'repasse_id');
    }

    public function companyDestino(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_destino_id');
    }

    public function entidadeDestino(): BelongsTo
    {
        return $this->belongsTo(EntidadeFinanceira::class, 'entidade_destino_id');
    }

    public function transacaoSaida(): BelongsTo
    {
        return $this->belongsTo(TransacaoFinanceira::class, 'transacao_saida_id');
    }

    public function transacaoEntrada(): BelongsTo
    {
        return $this->belongsTo(TransacaoFinanceira::class, 'transacao_entrada_id');
    }

    public function movimentacaoSaida(): BelongsTo
    {
        return $this->belongsTo(Movimentacao::class, 'movimentacao_saida_id');
    }

    public function movimentacaoEntrada(): BelongsTo
    {
        return $this->belongsTo(Movimentacao::class, 'movimentacao_entrada_id');
    }
}
