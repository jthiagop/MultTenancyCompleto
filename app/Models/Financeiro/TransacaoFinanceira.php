<?php

namespace App\Models\Financeiro;

use App\Models\Anexo;
use App\Models\EntidadeFinanceira;
use App\Models\LancamentoPadrao;
use App\Models\Movimentacao;
use App\Models\User;
use App\Models\Financeiro\TransacaoFracionamento;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class TransacaoFinanceira extends Model
{
    /** @use HasFactory<\Database\Factories\TransacaoFinanceiraFactory> */
    protected $table = 'transacoes_financeiras';

    use HasFactory, SoftDeletes;
    protected $fillable = [
        'company_id',
        'data_competencia',
        'data_vencimento',
        'data_pagamento',
        'entidade_id',
        'tipo',
        'valor',
        'valor_pago',
        'juros',
        'multa',
        'desconto',
        'valor_a_pagar',
        'descricao',
        'lancamento_padrao_id',
        // 'movimentacao_id', // ❌ REMOVIDO: Use $transacao->movimentacao()->create() ao invés
        'recorrencia_id',
        'cost_center_id',
        'tipo_documento',
        'numero_documento',
        'origem',
        'historico_complementar',
        'comprovacao_fiscal',
        'situacao',
        'agendado',
        'created_by',
        'updated_by',
        'created_by_name',
        'updated_by_name',
    ];

    protected $casts = [
        'situacao' => \App\Enums\SituacaoTransacao::class,
        'data_competencia' => \App\Casts\BrazilianDateCast::class,
        'data_vencimento' => \App\Casts\BrazilianDateCast::class,
        'data_pagamento' => \App\Casts\BrazilianDateCast::class,
        'valor' => 'integer',  // Em centavos
        'valor_pago' => 'integer',  // Em centavos
        'juros' => 'integer',  // Em centavos
        'multa' => 'integer',  // Em centavos
        'desconto' => 'integer',  // Em centavos
        'valor_a_pagar' => 'integer',  // Em centavos
        'agendado' => 'boolean',
        'comprovacao_fiscal' => 'boolean',
    ];

    /**
     * Accessors para converter centavos em reais para exibição
     */
    public function getValorEmReaisAttribute()
    {
        return $this->valor / 100;
    }

    public function getValorPagoEmReaisAttribute()
    {
        return $this->valor_pago ? $this->valor_pago / 100 : 0;
    }

    public function getJurosEmReaisAttribute()
    {
        return $this->juros ? $this->juros / 100 : 0;
    }

    public function getMultaEmReaisAttribute()
    {
        return $this->multa ? $this->multa / 100 : 0;
    }

    public function getDescontoEmReaisAttribute()
    {
        return $this->desconto ? $this->desconto / 100 : 0;
    }

    public function getValorAPagarEmReaisAttribute()
    {
        return $this->valor_a_pagar ? $this->valor_a_pagar / 100 : 0;
    }

    // Tabela Pivot
    public function bankStatements()
    {
        return $this->belongsToMany(
            BankStatement::class,
            'bank_statement_transacao',
            'transacao_financeira_id',
            'bank_statement_id'
        )
            ->withPivot('valor_conciliado', 'status_conciliacao')
            ->withTimestamps();
    }

    public function recibo()
    {
        return $this->hasOne(Recibo::class, 'transacao_id');
    }

    public function costCenter()
    {
        return $this->belongsTo(CostCenter::class, 'cost_center_id');
    }

    public function transacoesFinanceiras()
    {
        return $this->hasMany(TransacaoFinanceira::class, 'entidade_id');
    }

    public function lancamentoPadrao()
    {
        return $this->belongsTo(LancamentoPadrao::class, 'lancamento_padrao_id');
    }

    public function entidadeFinanceira()
    {
        return $this->belongsTo(EntidadeFinanceira::class, 'entidade_id');
    }

    public function movimentacao()
    {
        return $this->morphOne(Movimentacao::class, 'origem');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function modulos_anexos()
    {
        return $this->hasMany(ModulosAnexo::class, 'anexavel_id');
    }

    /**
     * Relacionamento: Configuração de recorrência usada por esta transação
     */
    public function recorrenciaConfig()
    {
        return $this->belongsTo(Recorrencia::class, 'recorrencia_id');
    }

    /**
     * Relacionamento: Recorrência que gerou esta transação (via pivot - para transações geradas)
     */
    public function recorrencia()
    {
        return $this->belongsToMany(
            Recorrencia::class,
            'recorrencia_transacoes',
            'transacao_financeira_id',
            'recorrencia_id'
        )
            ->withPivot('data_geracao', 'numero_ocorrencia', 'movimentacao_id')
            ->withTimestamps();
    }

    /**
     * Relacionamento: Lançamentos fracionados desta transação
     */
    public function fracionamentos()
    {
        return $this->hasMany(TransacaoFracionamento::class, 'transacao_principal_id');
    }

    /**
     * Scope: Fracionamentos do tipo "pago"
     */
    public function fracionamentosPagos()
    {
        return $this->hasMany(TransacaoFracionamento::class, 'transacao_principal_id')
            ->where('tipo', 'pago');
    }

    /**
     * Scope: Fracionamentos do tipo "em_aberto"
     */
    public function fracionamentosEmAberto()
    {
        return $this->hasMany(TransacaoFracionamento::class, 'transacao_principal_id')
            ->where('tipo', 'em_aberto');
    }

    static public function getChartSaida()
    {
        $userId = Auth::user()->id; // Recupere o ID do usuário logado
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        // Consulta principal na tabela transacoes_financeiras
        $saidas = DB::table('transacoes_financeiras')
            ->join('company_user', 'transacoes_financeiras.company_id', '=', 'company_user.company_id')
            ->where('company_user.user_id', $userId)
            ->where('transacoes_financeiras.tipo', 'S') // Filtra apenas as saídas (S para saída, E para entrada)
            ->whereYear('transacoes_financeiras.data_competencia', $currentYear) // Filtra pelo ano vigente
            ->whereMonth('transacoes_financeiras.data_competencia', $currentMonth) // Filtra pelo mês vigente
            ->whereNull('transacoes_financeiras.deleted_at') // Ignora registros excluídos (Soft Delete)
            ->select('transacoes_financeiras.*', 'transacoes_financeiras.origem')
            ->get();

        // Separar entre Banco e Caixa
        $bancoSaidas = $saidas->where('origem', 'Banco')->sum('valor');
        $caixaSaidas = $saidas->where('origem', 'Caixa')->sum('valor');

        // Retornar os valores em um array associativo
        return [
            'banco' => $bancoSaidas,
            'caixa' => $caixaSaidas,
            'total' => $bancoSaidas + $caixaSaidas
        ];
    }

    /**
     * Retorna informações da recorrência formatadas (ex: "1/12")
     * 
     * @return string|null
     */
    public function getRecorrenciaInfoAttribute(): ?string
    {
        $recorrencia = $this->recorrencia()->first();
        if (!$recorrencia) {
            return null;
        }

        $pivot = $recorrencia->pivot;
        $numeroOcorrencia = $pivot->numero_ocorrencia ?? 1;
        $totalOcorrencias = $recorrencia->total_ocorrencias ?? 1;

        return "{$numeroOcorrencia}/{$totalOcorrencias}";
    }

    /**
     * Scope: Filtra a busca para incluir apenas os registros da empresa ativa na sessão.
     * Este é o método que estava faltando.
     */
    public function scopeForActiveCompany($query)
    {
        // Pega o ID da empresa que está na sessão
        $activeCompanyId = session('active_company_id');

        // Se houver uma empresa ativa, aplica o filtro.
        if ($activeCompanyId) {
            return $query->where('company_id', $activeCompanyId);
        }

        // Se não houver, retorna uma consulta que não trará resultados para proteger os dados.
        return $query->whereRaw('1 = 0');
    }

    /**
     * Atualiza automaticamente o campo comprovacao_fiscal baseado na existência de anexos
     * 
     * @return bool
     */
    public function updateComprovacaoFiscal()
    {
        // Conta quantos anexos ativos existem para esta transação
        $hasAnexos = $this->modulos_anexos()
            ->where('status', 'ativo')
            ->exists();

        // Atualiza o campo comprovacao_fiscal
        $this->comprovacao_fiscal = $hasAnexos;

        return $this->save();
    }

    /**
     * Calcula automaticamente a situação do lançamento baseado nos dados atuais
     * 
     * @return string
     */
    public function calcularSituacao(): string
    {
        // Se foi desconsiderado manualmente, mantém
        if ($this->situacao === 'desconsiderado') {
            return 'desconsiderado';
        }

        // PRIORIDADE 1: Se há fracionamentos, a situação é "pago_parcial"
        // Verifica se existem fracionamentos (carregando o relacionamento se necessário)
        if ($this->relationLoaded('fracionamentos')) {
            if ($this->fracionamentos->isNotEmpty()) {
                return 'pago_parcial';
            }
        } else {
            // Se o relacionamento não está carregado, verifica diretamente no banco
            if ($this->exists && $this->fracionamentos()->exists()) {
                return 'pago_parcial';
            }
        }

        // Se está agendado e ainda não venceu
        if ($this->agendado && $this->data_vencimento && $this->data_vencimento > now()) {
            return 'previsto';
        }

        // Se venceu e não foi pago completamente
        if ($this->data_vencimento && $this->data_vencimento < now() && $this->valor_pago < $this->valor) {
            return 'atrasado';
        }

        // Se foi pago parcialmente (sem fracionamentos registrados)
        if ($this->valor_pago > 0 && $this->valor_pago < $this->valor) {
            return 'pago_parcial';
        }

        // Se foi totalmente pago/recebido
        if ($this->valor_pago >= $this->valor && $this->valor > 0) {
            // Entrada → recebido | Saída → pago
            return ($this->tipo === 'entrada')
                ? \App\Enums\SituacaoTransacao::RECEBIDO->value
                : \App\Enums\SituacaoTransacao::PAGO->value;
        }

        // Padrão: em aberto
        return 'em_aberto';
    }

    /**
     * Atualiza a situação automaticamente baseado nos dados atuais
     * 
     * @return bool
     */
    public function atualizarSituacao(): bool
    {
        // Se há fracionamentos, sempre deve ser "pago_parcial"
        if ($this->exists && $this->fracionamentos()->exists()) {
            $this->situacao = 'pago_parcial';
        } else {
            $this->situacao = $this->calcularSituacao();
        }
        return $this->save();
    }

    /**
     * Mutator para garantir que valor sempre seja absoluto (positivo)
     * Blindagem de segurança para impedir valores negativos no banco
     */
    protected function valor(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => abs((int) $value),
        );
    }

    /**
     * Mutator para garantir que valor_pago sempre seja absoluto (positivo)
     */
    protected function valorPago(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => $value !== null ? abs((int) $value) : null,
        );
    }

    /**
     * Mutator para garantir que juros sempre seja absoluto (positivo)
     */
    protected function juros(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => $value !== null ? abs((int) $value) : null,
        );
    }

    /**
     * Mutator para garantir que multa sempre seja absoluto (positivo)
     */
    protected function multa(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => $value !== null ? abs((int) $value) : null,
        );
    }

    /**
     * Mutator para garantir que desconto sempre seja absoluto (positivo)
     */
    protected function desconto(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => $value !== null ? abs((int) $value) : null,
        );
    }

    /**
     * Mutator para garantir que valor_a_pagar sempre seja absoluto (positivo)
     */
    protected function valorAPagar(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => $value !== null ? abs((int) $value) : null,
        );
    }

    /**
     * Boot do modelo - atualiza situação automaticamente quando necessário
     */
    protected static function boot()
    {
        parent::boot();

        // Atualiza situação antes de salvar
        static::saving(function ($transacao) {
            // PRIORIDADE 1: Se há fracionamentos, SEMPRE deve ser "pago_parcial"
            if ($transacao->exists) {
                if ($transacao->fracionamentos()->exists()) {
                    $transacao->situacao = \App\Enums\SituacaoTransacao::PAGO_PARCIAL;
                    return;
                }
            }

            // PRIORIDADE 2: Se foi definido manualmente como desconsiderado, mantém
            if ($transacao->situacao === \App\Enums\SituacaoTransacao::DESCONSIDERADO) {
                return;
            }

            // PRIORIDADE 3: Se foi definido manualmente como pago, recebido ou em_aberto, mantém
            $situacoesParaManter = [
                \App\Enums\SituacaoTransacao::PAGO->value,
                \App\Enums\SituacaoTransacao::RECEBIDO->value,
                \App\Enums\SituacaoTransacao::EM_ABERTO->value
            ];

            // Normaliza situacao para comparação (pode ser Enum ou string)
            $situacaoValue = $transacao->situacao instanceof \App\Enums\SituacaoTransacao
                ? $transacao->situacao->value
                : $transacao->situacao;

            if (in_array($situacaoValue, $situacoesParaManter)) {
                return;
            }

            // PRIORIDADE 4: Se foi definido manualmente como pago_parcial, mantém
            if ($situacaoValue === \App\Enums\SituacaoTransacao::PAGO_PARCIAL->value) {
                return;
            }

            // PRIORIDADE 5: Calcula automaticamente para outros casos
            $situacaoCalculada = $transacao->calcularSituacao();

            // Novo registro com data passada não nasce atrasado
            if (!$transacao->exists && $situacaoCalculada === 'atrasado') {
                $situacaoCalculada = 'em_aberto';
            }

            $transacao->situacao = $situacaoCalculada;
        });
    }
}
