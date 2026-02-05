<?php

namespace App\Models;

use App\Models\Financeiro\BankStatement;
use App\Models\Financeiro\TransacaoFinanceira;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Vinkla\Hashids\Facades\Hashids;


class EntidadeFinanceira extends Model
{
    use HasFactory;

    protected $table = 'entidades_financeiras';

    protected $fillable = [
        'nome',
        'tipo',
        'banco_id',
        'agencia',
        'conta',
        'account_type',
        'saldo_inicial',
        'saldo_atual',
        'descricao',
        'conta_contabil_id',
        'company_id',
        'created_by_name',
        'created_by',
        'updated_by_name',
        'updated_by',
    ];
    public function podeConciliarCom(BankStatement $outraTransacao, $tolerancia = 5.00)
    {
        return abs($this->amount + $outraTransacao->amount) <= $tolerancia;
    }


    // Relacionamento com movimentações
    public function movimentacoes()
    {
        return $this->hasMany(Movimentacao::class, 'entidade_id');
    }

    public function transacoesFinanceiras()
    {
        return $this->hasMany(TransacaoFinanceira::class, 'entidade_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id'); // Cada EntidadeFinanceira pertence a uma Company
    }

    public function bankStatements()
    {
        return $this->hasMany(BankStatement::class, 'entidade_financeira_id');
    }

    // Método para atualizar o saldo atual
    public function atualizarSaldo()
    {
        // Soma todas as entradas e subtrai todas as saídas
        $totalMovimentacoes = $this->movimentacoes()->where('tipo', 'entrada')->sum('valor') - $this->movimentacoes()->where('tipo', 'saida')->sum('valor');

        // O saldo atual é o saldo inicial (que não muda) mais o total de movimentações
        $this->saldo_atual = $this->saldo_inicial + $totalMovimentacoes;
        $this->save(); // Salva o novo saldo no banco
    }

    /**
     * ✅ Calcula o saldo dinamicamente baseado em MOVIMENTAÇÕES
     * 
     * Fórmula: saldo_inicial + (Σ entrada) - (Σ saida)
     * 
     * REGRA DE NEGÓCIO:
     * - Movimentações SÓ existem para transações EFETIVADAS (pago/recebido)
     * - A tabela movimentacoes é a fonte de verdade para o saldo
     * - Transações em_aberto são previsões e NÃO têm movimentação
     * 
     * @return float
     */
    public function calculateBalance()
    {
        // ✅ FONTE DE VERDADE: Tabela movimentacoes
        // Movimentações só existem para transações efetivadas (pago/recebido)
        // Portanto, não precisa filtrar por situação - se existe movimentação, impacta o saldo
        $saldoMovimentacoes = DB::table('movimentacoes')
            ->where('entidade_id', $this->id)
            ->whereNull('deleted_at')
            ->selectRaw("SUM(CASE WHEN tipo = 'entrada' THEN valor ELSE -valor END) as saldo")
            ->value('saldo') ?? 0;

        return $this->saldo_inicial + $saldoMovimentacoes;
    }

    /**
     * ✅ Accessor para obter saldo calculado dinamicamente
     * Uso em views: {{ $entidade->saldo_dinamico }}
     * 
     * @return float
     */
    public function getSaldoDinamicoAttribute()
    {
        return $this->calculateBalance();
    }

    /**
     * ✅ NOVO: Recalcula o saldo dinamicamente e sincroniza com a coluna estática (cache)
     * Usar em caso de desincronização ou como "botão de cura"
     * 
     * @return bool
     */
    public function recalcularSaldo(): bool
    {
        // 1. Calcula a verdade absoluta
        $saldoCalculado = $this->calculateBalance();
        
        // 2. Sincroniza com cache (coluna estática)
        $saldoAnterior = $this->saldo_atual;
        $this->saldo_atual = $saldoCalculado;
        
        // 3. Log da mudança
        if ($saldoAnterior !== $saldoCalculado) {
            \Log::warning("Saldo recalculado - Desincronização detectada", [
                'entidade_id' => $this->id,
                'saldo_anterior' => $saldoAnterior,
                'saldo_novo' => $saldoCalculado,
                'diferenca' => $saldoCalculado - $saldoAnterior,
                'company_id' => $this->company_id,
            ]);
        }
        
        return $this->save();
    }

    /**
     * ✅ NOVO: Função estática para recalcular TODOS os saldos da empresa
     * Roda via Command ou Admin Dashboard
     * 
     * @param int|null $companyId ID da empresa (se null, usa sessão)
     * @return int Número de entidades corrigidas
     */
    public static function recalcularTodosSaldos($companyId = null): int
    {
        $activeCompanyId = $companyId ?? session('active_company_id');
        
        if (!$activeCompanyId) {
            \Log::error("recalcularTodosSaldos: Nenhuma empresa ativa");
            return 0;
        }
        
        $entidades = self::where('company_id', $activeCompanyId)->get();
        $corrigidas = 0;
        
        foreach ($entidades as $entidade) {
            $saldoAnterior = $entidade->saldo_atual;
            $entidade->recalcularSaldo();
            
            if ($saldoAnterior !== $entidade->saldo_atual) {
                $corrigidas++;
            }
        }
        
        \Log::info("Recálculo de saldos concluído", [
            'company_id' => $activeCompanyId,
            'total_entidades' => $entidades->count(),
            'entidades_corrigidas' => $corrigidas,
        ]);
        
        return $corrigidas;
    }

    static public function getValorTotalEntidade()
    {
        $companyId = session('active_company_id'); // Recupere a empresa do usuário logado

        // Soma os saldos atuais das entidades financeiras do tipo 'caixa'
        return EntidadeFinanceira::where('tipo', 'caixa') // Filtra pelo tipo desejado
            ->where('company_id', $companyId) // Filtra pela empresa do usuário
            ->sum('saldo_atual'); // Soma a coluna 'saldo_atual'
    }

    static public function getValorTotalEntidadeBC()
    {
        $companyId = session('active_company_id'); // Recupere a empresa do usuário logado

        // Soma os saldos atuais das entidades financeiras do tipo 'caixa'
        return EntidadeFinanceira::where('tipo', 'banco') // Filtra pelo tipo desejado
            ->where('company_id', $companyId) // Filtra pela empresa do usuário
            ->sum('saldo_atual'); // Soma a coluna 'saldo_atual'
    }

    static public function getEntidadeFinanceira()
    {
        $companyId = session('active_company_id'); // Recupere a empresa do usuário logado

        // Soma os saldos atuais das entidades financeiras do tipo 'caixa'
        return EntidadeFinanceira::where('company_id', $companyId)->get();
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

        // Se nenhuma empresa estiver ativa, não retorna nada para proteger os dados.
        return $query->whereRaw('1 = 0');
    }

        /**
     * Uma entidade financeira (conta) pertence a um banco (instituição).
     */
    public function bank()
    {
        return $this->belongsTo(Bank::class, 'banco_id');
    }

    /**
     * Accessor: Formata o saldo atual como moeda brasileira
     */
    public function getSaldoFormatadoAttribute(): string
    {
        $saldo = $this->saldo_atual ?? 0;
        return number_format($saldo, 2, ',', '.');
    }

    /**
     * Accessor: Retorna o saldo atual ou 0 se null
     */
    public function getSaldoAtualSeguroAttribute(): float
    {
        return $this->saldo_atual ?? 0;
    }

    /**
     * Relacionamento com conta contábil (Plano de Contas).
     */
    public function contaContabil()
    {
        return $this->belongsTo(\App\Models\Contabilide\ChartOfAccount::class, 'conta_contabil_id');
    }

    /**
     * 1. O Laravel usa isso para gerar a URL (route('banco.show', $banco))
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
