<?php

namespace App\Services;

use App\Models\Financeiro\TransacaoFinanceira;
use Carbon\Carbon;

class TransacaoService
{
    public function getDadosGraficoBanco($startDate, $endDate, $companyId)
    {
        // Obter transações no período
        $transacoes = TransacaoFinanceira::where('company_id', $companyId)
            ->where(function ($query) {
                $query->where('origem', 'Conciliação Bancária')
                      ->orWhere('origem', 'Banco');
            })
            ->whereBetween('data_transacao', [$startDate, $endDate])
            ->get();

        // Agrupar por data
        $entradas = [];
        $saidas = [];
        $labels = [];

        // Definir o intervalo de dias
        $days = $startDate->diffInDays($endDate) + 1;
        $currentDate = $startDate->copy();

        for ($i = 0; $i < $days; $i++) {
            $date = $currentDate->format('Y-m-d');
            $labels[] = $currentDate->format('d/m/Y');

            $entradas[] = $transacoes->where('tipo', 'entrada')
                ->where('data_transacao', '>=', $currentDate->startOfDay())
                ->where('data_transacao', '<=', $currentDate->endOfDay())
                ->sum('valor');

            $saidas[] = $transacoes->where('tipo', 'saida')
                ->where('data_transacao', '>=', $currentDate->startOfDay())
                ->where('data_transacao', '<=', $currentDate->endOfDay())
                ->sum('valor');

            $currentDate->addDay();
        }

        return [
            'labels' => $labels,
            'entradas' => $entradas,
            'saidas' => $saidas,
            'total_entradas' => array_sum($entradas),
            'total_saidas' => array_sum($saidas),
        ];
    }
}