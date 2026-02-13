<?php

namespace App\Models;

use App\Observers\MovimentacaoObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\DB;

/**
 * Movimentação financeira — representa o impacto real no saldo.
 *
 * O saldo_atual da EntidadeFinanceira é atualizado automaticamente
 * pelo MovimentacaoObserver (increment/decrement atômico O(1)).
 */
#[ObservedBy(MovimentacaoObserver::class)]
class Movimentacao extends Model
{
    use HasFactory;
    use SoftDeletes;


    protected $table = 'movimentacoes';

    protected $fillable = [
        'entidade_id',
        'tipo',
        'valor',
        'data',
        'categoria',
        'status',
        'descricao',
        // 'movimentacao_id', // ❌ REMOVIDO: Auto-referência não deve estar aqui
        'company_id',
        'created_by',
        'updated_by',
        'created_by_name',
        'updated_by_name',
        'lancamento_padrao_id',
        'conta_debito_id',
        'conta_credito_id',
        'data_competencia',
        'origem_id',  
        'origem_type',
    ];

    /**
     * Retorna QUEM criou essa movimentação (Transacao, Dizimo, Patrimonio).
     */
    public function origem(): MorphTo
    {
        return $this->morphTo();
    }

    // Relacionamento com a entidade financeira
    public function entidade()
    {
        return $this->belongsTo(EntidadeFinanceira::class, 'entidade_id' , 'id');
    }

    // Relacionamento com lançamento padrão
    public function lancamentoPadrao()
    {
        return $this->belongsTo(LancamentoPadrao::class, 'lancamento_padrao_id');
    }

    // Relacionamento com conta de débito (contabilidade)
    public function contaDebito()
    {
        return $this->belongsTo(\App\Models\Contabilide\ChartOfAccount::class, 'conta_debito_id');
    }

    // Relacionamento com conta de crédito (contabilidade)
    public function contaCredito()
    {
        return $this->belongsTo(\App\Models\Contabilide\ChartOfAccount::class, 'conta_credito_id');
    }

    /**
     * Mutator para garantir que valor sempre seja absoluto (positivo)
     * Blindagem de segurança para impedir valores negativos no banco
     */
    protected function valor(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => abs((float) $value),
        );
    }

    // Observer MovimentacaoObserver gerencia o saldo automaticamente
    // via increment/decrement atômico O(1) — veja app/Observers/MovimentacaoObserver.php

    public function calcularSaldoAtual()
    {
        $entradas = $this->movimentacoes()->where('tipo', 'entrada')->sum('valor');
        $saidas = $this->movimentacoes()->where('tipo', 'saida')->sum('valor');

        return $this->saldo_inicial + $entradas - $saidas;
    }

    public static function getReceitaMes($companyId)
    {
        // Obtém o mês e ano atual
        $currentMonth = now()->format('m');
        $currentYear = now()->format('Y');

        // Soma todas as entradas (tipo = 'entrada') do mês atual e pertencentes à empresa
        return self::join('entidades_financeiras', 'movimentacoes.entidade_id', '=', 'entidades_financeiras.id')
            ->where('entidades_financeiras.company_id', $companyId) // Filtra pela empresa fornecida
            ->where('movimentacoes.tipo', 'entrada') // Filtra apenas movimentações de entrada
            ->whereMonth('movimentacoes.data', $currentMonth) // Filtra pelo mês atual
            ->whereYear('movimentacoes.data', $currentYear)   // Filtra pelo ano atual
            ->sum('movimentacoes.valor'); // Soma a coluna 'valor'
    }

    public static function getDespesasMes($companyId)
    {
        // Obtém o mês e ano atual
        $currentMonth = now()->format('m');
        $currentYear = now()->format('Y');

        // Soma todas as saídas (tipo = 'saida') do mês atual e pertencentes à empresa
        return self::join('entidades_financeiras', 'movimentacoes.entidade_id', '=', 'entidades_financeiras.id')
            ->where('entidades_financeiras.company_id', $companyId) // Filtra pela empresa fornecida
            ->where('movimentacoes.tipo', 'saida') // Filtra apenas movimentações de saída
            ->whereMonth('movimentacoes.data', $currentMonth) // Filtra pelo mês atual
            ->whereYear('movimentacoes.data', $currentYear)   // Filtra pelo ano atual
            ->sum('movimentacoes.valor'); // Soma a coluna 'valor'
    }

    public static function getSaldoBanco($companyId)
    {
        // Obtém o mês e ano atual
        $currentMonth = now()->format('m');
        $currentYear = now()->format('Y');

        // Calcula o saldo para banco
        return self::join('entidades_financeiras', 'movimentacoes.entidade_id', '=', 'entidades_financeiras.id')
            ->where('entidades_financeiras.company_id', $companyId) // Filtra pela empresa fornecida
            ->where('entidades_financeiras.tipo', 'banco') // Filtra apenas entidades do tipo banco
            ->whereMonth('movimentacoes.data', $currentMonth) // Filtra pelo mês atual
            ->whereYear('movimentacoes.data', $currentYear)   // Filtra pelo ano atual
            ->select(
                DB::raw('SUM(CASE WHEN movimentacoes.tipo = "entrada" THEN movimentacoes.valor
                                WHEN movimentacoes.tipo = "saida" THEN -movimentacoes.valor
                                ELSE 0 END) as saldo_banco') // Soma ou subtrai conforme o tipo
            )
            ->value('saldo_banco'); // Retorna o saldo final
    }

    public static function getSaldoCaixa($companyId)
    {
        // Obtém o mês e ano atual
        $currentMonth = now()->format('m');
        $currentYear = now()->format('Y');

        // Calcula o saldo para caixa
        return self::join('entidades_financeiras', 'movimentacoes.entidade_id', '=', 'entidades_financeiras.id')
            ->where('entidades_financeiras.company_id', $companyId) // Filtra pela empresa fornecida
            ->where('entidades_financeiras.tipo', 'caixa') // Filtra apenas entidades do tipo caixa
            ->whereMonth('movimentacoes.data', $currentMonth) // Filtra pelo mês atual
            ->whereYear('movimentacoes.data', $currentYear)   // Filtra pelo ano atual
            ->select(
                DB::raw('SUM(CASE WHEN movimentacoes.tipo = "entrada" THEN movimentacoes.valor
                                WHEN movimentacoes.tipo = "saida" THEN -movimentacoes.valor
                                ELSE 0 END) as saldo_caixa') // Soma ou subtrai conforme o tipo
            )
            ->value('saldo_caixa'); // Retorna o saldo final
    }
    public static function getSaldoBancoPorMesAno($companyId)
    {
        return self::join('entidades_financeiras', 'movimentacoes.entidade_id', '=', 'entidades_financeiras.id')
            ->where('entidades_financeiras.company_id', $companyId) // Filtra pela empresa fornecida
            ->where('entidades_financeiras.tipo', 'banco') // Filtra apenas entidades do tipo banco
            ->select(
                DB::raw('YEAR(movimentacoes.data) as ano'),
                DB::raw('MONTH(movimentacoes.data) as mes'),
                DB::raw('SUM(CASE WHEN movimentacoes.tipo = "entrada" THEN movimentacoes.valor
                                WHEN movimentacoes.tipo = "saida" THEN -movimentacoes.valor
                                ELSE 0 END) as saldo_banco') // Soma ou subtrai conforme o tipo
            )
            ->groupBy('ano', 'mes') // Agrupa por ano e mês
            ->orderBy('ano')
            ->orderBy('mes')
            ->get(); // Retorna os dados agrupados por ano e mês
    }


    public static function getSaldoCaixaPorMesAno($companyId)
    {
        return self::join('entidades_financeiras', 'movimentacoes.entidade_id', '=', 'entidades_financeiras.id')
            ->where('entidades_financeiras.company_id', $companyId) // Filtra pela empresa fornecida
            ->where('entidades_financeiras.tipo', 'caixa') // Filtra apenas entidades do tipo caixa
            ->select(
                DB::raw('YEAR(movimentacoes.data) as ano'),
                DB::raw('MONTH(movimentacoes.data) as mes'),
                DB::raw('SUM(CASE WHEN movimentacoes.tipo = "entrada" THEN movimentacoes.valor
                                WHEN movimentacoes.tipo = "saida" THEN -movimentacoes.valor
                                ELSE 0 END) as saldo_caixa') // Soma ou subtrai conforme o tipo
            )
            ->groupBy('ano', 'mes') // Agrupa por ano e mês
            ->orderBy('ano')
            ->orderBy('mes')
            ->get(); // Retorna os dados agrupados por ano e mês
    }

}
