<?php

namespace App\Services;

use App\Models\EntidadeFinanceira;
use App\Models\Financeiro\BankStatement;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

/**
 * Carrega conciliações pendentes (extrato OFX) para uma entidade, com sugestões e matching.
 * Usado por EntidadeFinanceiraController::conciliacoesTab (HTML) e ReactBancoController (JSON).
 */
final class ConciliacoesPendentesTabData
{
    /**
     * @return array{entidade: EntidadeFinanceira, bank_statements: LengthAwarePaginator, counts: array{all:int, received:int, paid:int}}
     */
    public static function fetch(int $activeCompanyId, int $entidadeId, string $tab, int $page, int $perPage = 5): array
    {
        $entidade = EntidadeFinanceira::forActiveCompany()->findOrFail($entidadeId);

        $query = self::baseQuery($activeCompanyId, $entidadeId);

        if ($tab === 'received') {
            $query->where('amount_cents', '>', 0);
        } elseif ($tab === 'paid') {
            $query->where('amount_cents', '<', 0);
        }

        $bankStatements = $query->orderBy('dtposted', 'desc')->paginate($perPage, ['*'], 'page', $page);

        try {
            $suggestionService = new ConciliacaoSuggestionService();
            foreach ($bankStatements as $lancamento) {
                try {
                    $lancamento->sugestao = $suggestionService->gerarSugestao($lancamento);
                } catch (\Exception $e) {
                    \Log::warning('Erro ao gerar sugestão para lançamento', [
                        'lancamento_id' => $lancamento->id,
                        'error'         => $e->getMessage(),
                    ]);
                    $lancamento->sugestao = null;
                }
            }
        } catch (\Exception $e) {
            \Log::error('Erro ao inicializar serviço de sugestões', ['error' => $e->getMessage()]);
        }

        $matchingService = new ConciliacaoMatchingService();
        foreach ($bankStatements as $lancamento) {
            $lancamento->possiveisTransacoes = $matchingService->buscarPossiveisTransacoes($lancamento, $entidadeId, 5);
            $lancamento->movimentacao_interna = MovimentacaoInternaDetector::detectar(
                $lancamento->memo ?? '',
                $lancamento->amount
            );
        }

        $counts = self::counts($activeCompanyId, $entidadeId);

        return [
            'entidade'        => $entidade,
            'bank_statements' => $bankStatements,
            'counts'          => $counts,
        ];
    }

    public static function baseQuery(int $activeCompanyId, int $entidadeId): Builder
    {
        return BankStatement::where('company_id', $activeCompanyId)
            ->where('entidade_financeira_id', $entidadeId)
            ->whereNotIn('status_conciliacao', ['ok', 'ignorado'])
            ->whereDoesntHave('transacoes')
            ->where(function ($q) {
                $q->where('conciliado_com_missa', false)
                    ->orWhereNull('conciliado_com_missa');
            });
    }

    /**
     * @return array{all: int, received: int, paid: int}
     */
    public static function counts(int $activeCompanyId, int $entidadeId): array
    {
        $baseQuery = self::baseQuery($activeCompanyId, $entidadeId);

        return [
            'all'      => (clone $baseQuery)->count(),
            'received' => (clone $baseQuery)->where('amount_cents', '>', 0)->count(),
            'paid'     => (clone $baseQuery)->where('amount_cents', '<', 0)->count(),
        ];
    }
}
