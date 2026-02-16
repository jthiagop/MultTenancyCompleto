<?php

namespace App\Services;

use App\Models\EntidadeFinanceira;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EntidadeFinanceiraService
{
    /**
     * Calcula a variação percentual do saldo comparando com o período anterior
     *
     * @param EntidadeFinanceira $entidade
     * @param int $mesesAtras Quantos meses atrás comparar (padrão: 1 mês)
     * @return float
     */
    public function calcularVariacaoPercentual(EntidadeFinanceira $entidade, int $mesesAtras = 1): float
    {
        $saldoAtual = $entidade->saldo_atual ?? 0;
        
        // Busca o saldo do período anterior através das transações
        $dataComparacao = Carbon::now()->subMonths($mesesAtras);
        
        // Calcula o saldo anterior somando saldo inicial + todas as transações até a data de comparação
        $saldoAnterior = $this->getSaldoAnterior($entidade, $dataComparacao);
        
        // Se não há saldo anterior, retorna 0 ou 100% se há saldo atual
        if ($saldoAnterior == 0) {
            return $saldoAtual > 0 ? 100.0 : 0.0;
        }
        
        // Calcula a variação percentual
        $variacao = (($saldoAtual - $saldoAnterior) / abs($saldoAnterior)) * 100;
        
        return round($variacao, 1);
    }

    /**
     * Obtém o saldo de uma entidade em uma data específica
     *
     * @param EntidadeFinanceira $entidade
     * @param Carbon|null $dataComparacao
     * @return float
     */
    protected function getSaldoAnterior(EntidadeFinanceira $entidade, ?Carbon $dataComparacao = null): float
    {
        if (!$dataComparacao) {
            $dataComparacao = Carbon::now()->subMonth();
        }

        // Opção A: saldo_inicial = 0 na tabela, a movimentação de saldo_inicial
        // já é contabilizada via transações financeiras efetivadas

        // Soma todas as entradas até a data de comparação
        $entradas = $entidade->transacoesFinanceiras()
            ->where('tipo', 'entrada')
            ->whereDate('data_competencia', '<=', $dataComparacao->endOfDay())
            ->whereNull('deleted_at')
            ->sum('valor');

        // Subtrai todas as saídas até a data de comparação
        $saidas = $entidade->transacoesFinanceiras()
            ->where('tipo', 'saida')
            ->whereDate('data_competencia', '<=', $dataComparacao->endOfDay())
            ->whereNull('deleted_at')
            ->sum('valor');

        return $entradas - $saidas;
    }

    /**
     * Prepara as entidades para exibição no side-card
     *
     * @param \Illuminate\Support\Collection $entidadesBanco
     * @param \Illuminate\Support\Collection $entidadesCaixa
     * @return \Illuminate\Support\Collection
     */
    public function prepararEntidadesParaSideCard($entidadesBanco, $entidadesCaixa)
    {
        // Merge das coleções
        $todasEntidades = $entidadesBanco->merge($entidadesCaixa)->values();

        // Adiciona cálculos e formatações
        return $todasEntidades->map(function ($entidade) {
            // Calcula variação percentual
            $entidade->variacao_percentual = $this->calcularVariacaoPercentual($entidade);
            
            // Calcula variação absoluta em R$
            $saldoAtual = $entidade->saldo_atual ?? 0;
            $saldoAnterior = $this->getSaldoAnterior($entidade);
            $entidade->variacao_valor = $saldoAtual - $saldoAnterior;

            // Determina se a variação é positiva ou negativa
            $entidade->variacao_positiva = $entidade->variacao_percentual >= 0;
            
            // Garante que saldo_atual tenha valor padrão
            if ($entidade->saldo_atual === null) {
                $entidade->saldo_atual = 0;
            }

            return $entidade;
        });
    }
}
