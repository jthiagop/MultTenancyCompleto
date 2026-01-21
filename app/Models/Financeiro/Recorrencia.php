<?php

namespace App\Models\Financeiro;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Recorrencia extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'recorrencias';

    protected $fillable = [
        'company_id',
        'nome',
        'intervalo_repeticao',
        'frequencia',
        'total_ocorrencias',
        'ocorrencias_geradas',
        'data_proxima_geracao',
        'data_inicio',
        'data_fim',
        'ativo',
        'ultima_execucao',
        'created_by',
        'created_by_name',
        'updated_by',
        'updated_by_name',
    ];

    protected $casts = [
        'data_proxima_geracao' => 'date',
        'data_inicio' => 'date',
        'data_fim' => 'date',
        'ultima_execucao' => 'datetime',
        'ativo' => 'boolean',
        'intervalo_repeticao' => 'integer',
        'total_ocorrencias' => 'integer',
        'ocorrencias_geradas' => 'integer',
    ];

    /**
     * Relacionamento com todas as transações que usam esta configuração
     */
    public function transacoes()
    {
        return $this->hasMany(TransacaoFinanceira::class, 'recorrencia_id');
    }

    /**
     * Relacionamento com TransacoesFinanceiras geradas (via pivot)
     */
    public function transacoesGeradas()
    {
        return $this->belongsToMany(
            TransacaoFinanceira::class,
            'recorrencia_transacoes',
            'recorrencia_id',
            'transacao_financeira_id'
        )
            ->withPivot('data_geracao', 'numero_ocorrencia', 'movimentacao_id')
            ->withTimestamps();
    }

    /**
     * Relacionamento com usuário que criou
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relacionamento com usuário que atualizou
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope para recorrências ativas
     */
    public function scopeAtivas($query)
    {
        return $query->where('ativo', true);
    }

    /**
     * Scope para recorrências que devem ser geradas hoje ou antes
     */
    public function scopeParaGerar($query)
    {
        return $query->where('ativo', true)
            ->where('data_proxima_geracao', '<=', Carbon::today());
    }

    /**
     * Scope para filtrar por company_id
     */
    public function scopeForActiveCompany($query)
    {
        $companyId = session('active_company_id');
        if ($companyId) {
            return $query->where('company_id', $companyId);
        }
        return $query;
    }

    /**
     * Scope para buscar configurações reutilizáveis (ativas e disponíveis)
     */
    public function scopeReutilizaveis($query)
    {
        return $query->where('ativo', true)
            ->forActiveCompany()
            ->orderBy('nome', 'asc')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Verifica se a recorrência já completou todas as ocorrências
     */
    public function isCompleta(): bool
    {
        return $this->ocorrencias_geradas >= $this->total_ocorrencias;
    }

    /**
     * Calcula a próxima data de geração baseada na frequência
     */
    public function calcularProximaDataGeracao(Carbon $dataAtual): Carbon
    {
        $novaData = clone $dataAtual;

        switch ($this->frequencia) {
            case 'diario':
                $novaData->addDays($this->intervalo_repeticao);
                break;
            case 'semanal':
                $novaData->addWeeks($this->intervalo_repeticao);
                break;
            case 'mensal':
                $novaData->addMonths($this->intervalo_repeticao);
                break;
            case 'anual':
                $novaData->addYears($this->intervalo_repeticao);
                break;
        }

        return $novaData;
    }
}
