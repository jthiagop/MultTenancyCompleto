<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;


use Illuminate\Database\Eloquent\Model;

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
        'movimentacao_id',
        'company_id',
        'created_by',
        'updated_by',
        'created_by_name',
        'updated_by_name',
    ];

    // Relacionamento com a entidade financeira
    public function entidade()
    {
        return $this->belongsTo(EntidadeFinanceira::class, 'entidade_id' , 'id');
    }

    public static function boot()
    {
        parent::boot();

        // Atualiza saldo após criar uma movimentação
        static::created(function ($movimentacao) {
            $entidade = EntidadeFinanceira::find($movimentacao->entidade_id);
            if ($movimentacao->tipo === 'entrada') {
                $entidade->saldo_atual += $movimentacao->valor;
            } else {
                $entidade->saldo_atual -= $movimentacao->valor;
            }
            $entidade->save();
        });

        // // Reverte saldo ao excluir uma movimentação
        // static::deleting(function ($movimentacao) {
        //     $entidade = EntidadeFinanceira::find($movimentacao->entidade_id);
        //     if ($movimentacao->tipo === 'entrada') {
        //         $entidade->saldo_atual -= $movimentacao->valor;
        //     } else {
        //         $entidade->saldo_atual += $movimentacao->valor;
        //     }
        //     $entidade->save();
        // });
    }

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
