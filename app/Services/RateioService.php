<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Financeiro\TransacaoFinanceira;
use App\Models\Financeiro\TransacaoRateio;
use App\Models\LancamentoPadrao;
use App\Notifications\RateioRecebidoNotification;
use App\Services\NotificacaoFinanceiraService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RateioService
{
    /**
     * Processa o rateio de uma transação financeira, salvando as linhas
     * e gerando acertos intercompany quando a filial difere da empresa dona do boleto.
     */
    public function processarRateio(TransacaoFinanceira $transacao, array $rateios): void
    {
        DB::transaction(function () use ($transacao, $rateios) {
            $user = Auth::user();
            $companyDonaId = (int) $transacao->company_id;
            $nomeMatriz = Company::find($companyDonaId)?->name ?? 'Matriz';

            $auditFields = [
                'created_by' => $user?->id,
                'created_by_name' => $user?->name,
                'updated_by' => $user?->id,
                'updated_by_name' => $user?->name,
            ];

            foreach ($rateios as $linha) {
                $filialId = (int) $linha['filial_id'];
                $valor = (float) $linha['valor'];
                $percentual = (float) $linha['percentual'];

                TransacaoRateio::create([
                    'transacao_financeira_id' => $transacao->id,
                    'filial_id' => $filialId,
                    'centro_custo_id' => $linha['centro_custo_id'] ?: null,
                    'lancamento_padrao_id' => $linha['lancamento_padrao_id'] ?: null,
                    'valor' => $valor,
                    'percentual' => $percentual,
                ]);

                if ($filialId === $companyDonaId) {
                    continue;
                }

                $this->criarAcertoIntercompany(
                    $transacao,
                    $filialId,
                    $valor,
                    $companyDonaId,
                    $nomeMatriz,
                    $auditFields,
                );
            }
        });
    }

    /**
     * Cria os registros de acerto intercompany:
     *
     * 1. Saída na Filial (custo de competência, sem impacto bancário imediato)
     *    - origem = 'rateio', situacao = 'em_aberto'
     *    - rateio_origem_id → despesa original da Matriz
     *    - reembolso_par_id → entrada pendente criada na Matriz (passo 2)
     *
     * 2. Entrada pendente na Matriz (reembolso esperado)
     *    - origem = 'reembolso_rateio', situacao = 'em_aberto'
     *    - rateio_origem_id → despesa original da Matriz
     *    - reembolso_par_id → saída criada na Filial (passo 1)
     *
     * Quando a filial paga o boleto (saída → pago) e a Matriz recebe (entrada → recebido),
     * o ciclo fecha e o saldo líquido da Matriz retorna a zero.
     */
    private function criarAcertoIntercompany(
        TransacaoFinanceira $transacaoOriginal,
        int $filialId,
        float $valor,
        int $companyDonaId,
        string $nomeMatriz,
        array $auditFields,
    ): void {
        $user = Auth::user();
        $nomeFilial = Company::find($filialId)?->name ?? "Filial #{$filialId}";

        // ── Passo 1: Saída na Filial ─────────────────────────────────────────
        $lpPagar = LancamentoPadrao::firstOrCreateForCompany(
            (int) $filialId,
            ['description' => 'Ressarcimento para a Matriz'],
            ['type' => 'saida', 'category' => 'Rateio', 'user_id' => $user?->id]
        );

        $entidadeFilial = \App\Models\EntidadeFinanceira::where('company_id', $filialId)->first();

        $transacaoFilial = TransacaoFinanceira::create([
            'company_id'              => $filialId,
            'data_competencia'        => $transacaoOriginal->data_competencia,
            'data_vencimento'         => $transacaoOriginal->data_vencimento ?? $transacaoOriginal->data_competencia,
            'entidade_id'             => $entidadeFilial?->id ?? $transacaoOriginal->entidade_id,
            'parceiro_id'             => $transacaoOriginal->parceiro_id,
            'tipo'                    => 'saida',
            'valor'                   => $valor,
            'descricao'               => "Ressarcimento para {$nomeMatriz} - Rateio",
            'situacao'                => 'em_aberto',
            'lancamento_padrao_id'    => $lpPagar->id,
            'tipo_documento'          => $transacaoOriginal->tipo_documento,
            'origem'                  => 'rateio',
            'rateio_origem_id'        => $transacaoOriginal->id,
            'historico_complementar'  => "Rateio ref. {$transacaoOriginal->descricao}",
            ...$auditFields,
        ]);

        // ── Passo 2: Entrada pendente na Matriz (reembolso esperado) ─────────
        $lpReceber = LancamentoPadrao::firstOrCreateForCompany(
            (int) $companyDonaId,
            ['description' => 'Reembolso de Rateio de Filiais'],
            ['type' => 'entrada', 'category' => 'Rateio', 'user_id' => $user?->id]
        );

        $matrizEntrada = TransacaoFinanceira::create([
            'company_id'              => $companyDonaId,
            'data_competencia'        => $transacaoOriginal->data_competencia,
            'data_vencimento'         => $transacaoOriginal->data_vencimento ?? $transacaoOriginal->data_competencia,
            'entidade_id'             => $transacaoOriginal->entidade_id,
            'parceiro_id'             => $transacaoOriginal->parceiro_id,
            'tipo'                    => 'entrada',
            'valor'                   => $valor,
            'descricao'               => "Reembolso de Rateio - {$nomeFilial}",
            'situacao'                => 'em_aberto',
            'lancamento_padrao_id'    => $lpReceber->id,
            'tipo_documento'          => $transacaoOriginal->tipo_documento,
            'origem'                  => 'reembolso_rateio',
            'rateio_origem_id'        => $transacaoOriginal->id,
            'reembolso_par_id'        => $transacaoFilial->id,
            'historico_complementar'  => "Reembolso ref. {$transacaoOriginal->descricao} — {$nomeFilial}",
            ...$auditFields,
        ]);

        // ── Passo 3: Fechar o elo bidirecional ───────────────────────────────
        $transacaoFilial->update(['reembolso_par_id' => $matrizEntrada->id]);

        Log::info("Acerto intercompany criado", [
            'matriz_id'         => $companyDonaId,
            'filial_id'         => $filialId,
            'valor'             => $valor,
            'filial_saida_id'   => $transacaoFilial->id,
            'matriz_entrada_id' => $matrizEntrada->id,
        ]);

        // ── Notifica os usuários financeiros da filial sobre a cobrança ───────
        try {
            app(NotificacaoFinanceiraService::class)->notificarEmpresa(
                $filialId,
                new RateioRecebidoNotification(
                    descricao:   $transacaoOriginal->descricao ?? 'Rateio',
                    valor:       $valor,
                    nomeMatriz:  $nomeMatriz,
                    transacaoId: $transacaoFilial->id,
                    companyId:   $filialId,
                    triggeredBy: $user?->id,
                )
            );
        } catch (\Throwable $e) {
            Log::warning('Falha ao notificar filial sobre rateio intercompany', [
                'filial_id'    => $filialId,
                'transacao_id' => $transacaoFilial->id,
                'error'        => $e->getMessage(),
            ]);
        }
    }
}
