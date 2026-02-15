<?php 

namespace App\Services;

use App\Models\Financeiro\TransacaoFinanceira;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PrestacaoContasService
{
    public function coletar(array $filtros): array
    {
        [$dataInicial, $dataFinal, $costCenter, $entidadeId] = [
            $filtros['data_inicial'] ?? null,
            $filtros['data_final']   ?? null,
            $filtros['cost_center_id'] ?? null,
            $filtros['entidade_id'] ?? null,
        ];

        $query = TransacaoFinanceira::with(['entidadeFinanceira', 'lancamentoPadrao', 'parceiro'])
            ->forActiveCompany()
            ->whereNotIn('situacao', [
                \App\Enums\SituacaoTransacao::DESCONSIDERADO->value,
                \App\Enums\SituacaoTransacao::PREVISTO->value,
                \App\Enums\SituacaoTransacao::PARCELADO->value,
            ])
            ->where('agendado', false)
            ->when($dataInicial, fn($q) => $q->whereDate('data_competencia', '>=', $dataInicial))
            ->when($dataFinal,   fn($q) => $q->whereDate('data_competencia', '<=', $dataFinal))
            ->when($costCenter,  fn($q) => $q->where('cost_center_id', $costCenter))
            ->when($entidadeId,  fn($q) => $q->where('entidade_id', $entidadeId))
            ->orderBy('data_competencia');

        $colecao = $query->get();

        // Agrupa pela origem (Banco, Caixa…)
        $grupos = $colecao->groupBy('origem')->map(function (Collection $items, string $origem) {
            $entrada = $items->where('tipo', 'entrada')->sum('valor');
            $saida   = $items->where('tipo', 'saida')->sum('valor');

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
            'costCenter'        => $costCenter,
        ];
    }
}
