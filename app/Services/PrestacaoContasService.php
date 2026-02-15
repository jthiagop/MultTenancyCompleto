<?php 

namespace App\Services;

use App\Models\Financeiro\TransacaoFinanceira;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PrestacaoContasService
{
    public function coletar(array $filtros): array
    {
        [$dataInicial, $dataFinal, $entidadeId] = [
            $filtros['data_inicial'] ?? null,
            $filtros['data_final']   ?? null,
            $filtros['entidade_id'] ?? null,
        ];

        $tipoData   = $filtros['tipo_data'] ?? 'competencia';
        $situacoes  = $filtros['situacoes'] ?? [];
        $categorias = $filtros['categorias'] ?? [];
        $parceiroId        = $filtros['parceiro_id'] ?? null;
        $comprovacaoFiscal = $filtros['comprovacao_fiscal'] ?? false;
        $tipoValor         = $filtros['tipo_valor'] ?? 'previsto';

        // Coluna de data a filtrar
        $colunaData = $tipoData === 'pagamento' ? 'data_pagamento' : 'data_competencia';

        // Situacoes sempre excluidas
        $situacoesExcluidas = [
            \App\Enums\SituacaoTransacao::DESCONSIDERADO->value,
            \App\Enums\SituacaoTransacao::PARCELADO->value,
        ];

        $query = TransacaoFinanceira::with(['entidadeFinanceira', 'lancamentoPadrao', 'parceiro'])
            ->forActiveCompany()
            ->whereNotIn('situacao', $situacoesExcluidas)
            ->where('agendado', false)
            ->when($dataInicial, fn($q) => $q->whereDate($colunaData, '>=', $dataInicial))
            ->when($dataFinal,   fn($q) => $q->whereDate($colunaData, '<=', $dataFinal))
            ->when($entidadeId,  fn($q) => $q->where('entidade_id', $entidadeId))
            ->when(!empty($situacoes),  fn($q) => $q->whereIn('situacao', $situacoes))
            ->when(!empty($categorias), fn($q) => $q->whereIn('lancamento_padrao_id', $categorias))
            ->when($parceiroId,        fn($q) => $q->where('parceiro_id', $parceiroId))
            ->when($comprovacaoFiscal, fn($q) => $q->where('comprovacao_fiscal', true))
            ->orderBy($colunaData);

        $colecao = $query->get();

        $campoValor = $tipoValor === 'pago' ? 'valor_pago' : 'valor';

        // Agrupa pela origem (Banco, Caixa…)
        $grupos = $colecao->groupBy('origem')->map(function (Collection $items, string $origem) use ($campoValor) {
            $entrada = $items->where('tipo', 'entrada')->sum($campoValor);
            $saida   = $items->where('tipo', 'saida')->sum($campoValor);

            return [
                'origem'          => $origem,
                'movimentacoes'   => $items,
                'total_entrada'   => $entrada,
                'total_saida'     => $saida,
            ];
        })->values(); // devolvo como array plano p/ Blade

        return [
            'dados'             => $grupos,
            'totalGeralEntrada' => $grupos->sum('total_entrada'),
            'totalGeralSaida'   => $grupos->sum('total_saida'),
            'dataInicial'       => $dataInicial,
            'dataFinal'         => $dataFinal,
        ];
    }
}
