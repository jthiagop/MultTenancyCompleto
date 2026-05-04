<?php

declare(strict_types=1);

namespace App\Services\Dizimo;

use App\Models\Dizimo;
use App\Models\Fiel;
use App\Models\Movimentacao;
use App\Models\Financeiro\TransacaoFinanceira;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Service responsável pela criação/atualização/exclusão de Dizimos com
 * integração ao módulo Financeiro (movimentações + transações).
 *
 * Toda gravação é envolvida em DB::transaction (atômica). O saldo da
 * EntidadeFinanceira é atualizado automaticamente pelo MovimentacaoObserver.
 *
 * Estratégia de "período" (carnê): N registros — um Dizimo por mês entre
 * (mes_inicio/ano_inicio) e (mes_fim/ano_fim), com o mesmo valor informado.
 * Cada Dizimo gera 1 Movimentação + 1 Transação.
 *
 * Oferta adicional: quando o usuário marcar "oferta_adicional", criamos um
 * Dizimo extra do tipo "Oferta" para o mesmo fiel/data/forma/entidade — não
 * contamina o relatório de Dízimos.
 */
class DizimoFinanceiroService
{
    /**
     * Tipos válidos para criação. Espelha a validação do controller e do front.
     */
    public const TIPOS_VALIDOS = ['Dízimo', 'Doação', 'Oferta', 'Outro'];

    /**
     * Formas de pagamento aceitas (sincronizadas com o select do drawer).
     */
    public const FORMAS_PAGAMENTO = [
        'Dinheiro', 'PIX', 'Cartão de Débito', 'Cartão de Crédito',
        'Transferência', 'Cheque', 'Outro',
    ];

    /**
     * Cria um (ou N) registros de Dizimo conforme o payload.
     *
     * Payload esperado (já validado):
     *  - tipo: Dízimo|Doação|Oferta|Outro
     *  - fiel_id: int
     *  - data_pagamento: Y-m-d
     *  - valor: float (>0)
     *  - forma_pagamento: string
     *  - entidade_financeira_id: int|null
     *  - observacoes: string|null
     *  - integrar_financeiro: bool
     *  - periodo: bool
     *  - mes_inicio: 'mm/aaaa' (se periodo)
     *  - mes_fim:    'mm/aaaa' (se periodo)
     *  - oferta_adicional: bool
     *  - oferta_adicional_valor: float|null  (quando oferta_adicional=true)
     *  - oferta_adicional_ref:   string|null (quando oferta_adicional=true)
     *
     * @return Collection<int, Dizimo>
     */
    public function criar(array $dados, ?int $userId = null, ?int $companyId = null): Collection
    {
        $userId    ??= Auth::id();
        $companyId ??= session('active_company_id');

        $userName = optional(Auth::user())->name;

        /** @var Collection<int, Dizimo> $resultado */
        $resultado = DB::transaction(function () use ($dados, $userId, $userName, $companyId) {
            $criados = collect();

            $datas = $this->gerarDatasPorPeriodo($dados);

            foreach ($datas as $dataPagamento) {
                $dizimo = $this->criarDizimoUnico(
                    array_merge($dados, ['data_pagamento' => $dataPagamento]),
                    $userId,
                    $userName,
                    $companyId,
                );
                $criados->push($dizimo);
            }

            // Oferta adicional → 1 registro extra do tipo "Oferta" na mesma data informada (data_pagamento original).
            if (! empty($dados['oferta_adicional'])) {
                $valorOferta = (float) ($dados['oferta_adicional_valor'] ?? 0);
                if ($valorOferta > 0) {
                    $obs = trim('Oferta adicional' . (! empty($dados['oferta_adicional_ref']) ? ': ' . $dados['oferta_adicional_ref'] : ''));
                    $oferta = $this->criarDizimoUnico(
                        array_merge($dados, [
                            'tipo'           => 'Oferta',
                            'valor'          => $valorOferta,
                            'data_pagamento' => $dados['data_pagamento'],
                            'observacoes'    => $obs,
                            'periodo'        => false,
                        ]),
                        $userId,
                        $userName,
                        $companyId,
                    );
                    $criados->push($oferta);
                }
            }

            // Atualiza última contribuição do fiel (apenas para "Dízimo")
            $this->atualizarUltimaContribuicaoFiel((int) $dados['fiel_id'], $criados);

            return $criados;
        });

        return $resultado;
    }

    /**
     * Atualiza um Dizimo existente, recriando/sincronizando movimentação e transação.
     */
    public function atualizar(Dizimo $dizimo, array $dados, ?int $userId = null): Dizimo
    {
        $userId   ??= Auth::id();
        $userName = optional(Auth::user())->name;

        /** @var Dizimo $resultado */
        $resultado = DB::transaction(function () use ($dizimo, $dados, $userId, $userName) {
            $dizimo->update([
                'fiel_id'                => $dados['fiel_id'],
                'tipo'                   => $dados['tipo'],
                'valor'                  => $dados['valor'],
                'data_pagamento'         => $dados['data_pagamento'],
                'forma_pagamento'        => $dados['forma_pagamento'],
                'entidade_financeira_id' => $dados['entidade_financeira_id'] ?? null,
                'observacoes'            => $dados['observacoes'] ?? null,
                'updated_by'             => $userId,
                'updated_by_name'        => $userName,
            ]);

            $integrar = ! empty($dados['integrar_financeiro']) && ! empty($dados['entidade_financeira_id']);

            if ($integrar) {
                if (! $dizimo->relationLoaded('fiel')) {
                    $dizimo->load('fiel');
                }
                $dizimo->refresh();
                $this->sincronizarFinanceiro($dizimo, (int) $dados['entidade_financeira_id'], $userId, $userName);
            } else {
                $this->removerFinanceiro($dizimo);
            }

            return $dizimo->fresh(['fiel', 'entidadeFinanceira', 'movimentacao']);
        });

        return $resultado;
    }

    /**
     * Exclui (soft delete) um Dizimo, removendo cascata de transação e movimentação.
     */
    public function excluir(Dizimo $dizimo): void
    {
        DB::transaction(function () use ($dizimo) {
            $this->removerFinanceiro($dizimo);
            $dizimo->delete();
        });
    }

    // ──────────────────────────────────────────────────────────────────────
    // Helpers internos
    // ──────────────────────────────────────────────────────────────────────

    /**
     * Cria um único Dizimo + movimentação + transação (quando integrado).
     */
    private function criarDizimoUnico(
        array $dados,
        ?int $userId,
        ?string $userName,
        ?int $companyId,
    ): Dizimo {
        $dizimo = Dizimo::create([
            'company_id'             => $companyId,
            'fiel_id'                => $dados['fiel_id'],
            'tipo'                   => $dados['tipo'],
            'valor'                  => $dados['valor'],
            'data_pagamento'         => $dados['data_pagamento'],
            'forma_pagamento'        => $dados['forma_pagamento'],
            'entidade_financeira_id' => $dados['entidade_financeira_id'] ?? null,
            'observacoes'            => $dados['observacoes'] ?? null,
            'created_by'             => $userId,
            'created_by_name'        => $userName,
            'updated_by'             => $userId,
            'updated_by_name'        => $userName,
        ]);

        $integrar = ! empty($dados['integrar_financeiro']) && ! empty($dados['entidade_financeira_id']);

        if ($integrar) {
            $dizimo->load('fiel');
            $this->sincronizarFinanceiro(
                $dizimo,
                (int) $dados['entidade_financeira_id'],
                $userId,
                $userName,
            );
        } else {
            $dizimo->update(['integrado_financeiro' => false]);
        }

        return $dizimo;
    }

    /**
     * Cria/atualiza Movimentação e Transação Financeira a partir do Dizimo.
     * Marca `integrado_financeiro = true` ao final.
     */
    private function sincronizarFinanceiro(
        Dizimo $dizimo,
        int $entidadeId,
        ?int $userId,
        ?string $userName,
    ): void {
        $companyId = $dizimo->company_id;

        $nomeFiel       = $dizimo->fiel?->nome_completo ?? 'Fiel Desconhecido';
        $dataFormatada  = Carbon::parse($dizimo->data_pagamento)->format('m/Y');
        $tipoRecebimento = $this->labelRecebimento($dizimo->tipo);

        $descricao = sprintf(
            '%d - %s %s %s',
            $dizimo->id,
            $tipoRecebimento,
            $dataFormatada,
            mb_strtoupper($nomeFiel),
        );

        $historicoComplementar = $descricao . ' ' . $tipoRecebimento;
        if (! empty($dizimo->observacoes)) {
            $historicoComplementar .= ' - ' . $dizimo->observacoes;
        }

        // ── Movimentação (criar ou atualizar) ──────────────────────────────
        $movimentacao = $dizimo->movimentacao_id ? Movimentacao::find($dizimo->movimentacao_id) : null;

        if ($movimentacao) {
            $movimentacao->update([
                'entidade_id'      => $entidadeId,
                'tipo'             => 'entrada',
                'valor'            => $dizimo->valor,
                'data'             => $dizimo->data_pagamento,
                'descricao'        => $descricao,
                'origem_id'        => $dizimo->id,
                'origem_type'      => Dizimo::class,
                'updated_by'       => $userId,
                'updated_by_name'  => $userName,
            ]);
        } else {
            $movimentacao = Movimentacao::create([
                'company_id'      => $companyId,
                'entidade_id'     => $entidadeId,
                'tipo'            => 'entrada',
                'valor'           => $dizimo->valor,
                'data'            => $dizimo->data_pagamento,
                'descricao'       => $descricao,
                'origem_id'       => $dizimo->id,
                'origem_type'     => Dizimo::class,
                'created_by'      => $userId,
                'created_by_name' => $userName,
                'updated_by'      => $userId,
                'updated_by_name' => $userName,
            ]);
        }

        // ── Transação Financeira (criar ou atualizar) ──────────────────────
        $transacao = TransacaoFinanceira::where('movimentacao_id', $movimentacao->id)->first();

        $payloadTransacao = [
            'data_competencia'       => $dizimo->data_pagamento,
            // Recebimento à vista: vencimento, pagamento e valor_pago são iguais ao valor.
            // Isso aciona o cálculo automático de `situacao = recebido` no boot do model.
            'data_vencimento'        => $dizimo->data_pagamento,
            'data_pagamento'         => $dizimo->data_pagamento,
            'valor_pago'             => $dizimo->valor,
            'situacao'               => \App\Enums\SituacaoTransacao::RECEBIDO,
            'entidade_id'            => $entidadeId,
            'valor'                  => $dizimo->valor,
            'descricao'              => $descricao,
            'tipo_documento'         => $dizimo->tipo,
            'numero_documento'       => 'DZ-' . $dizimo->id,
            'origem'                 => 'Dízimo/Doação',
            'historico_complementar' => $historicoComplementar,
            'updated_by'             => $userId,
            'updated_by_name'        => $userName,
        ];

        if ($transacao) {
            $transacao->update($payloadTransacao);
        } else {
            TransacaoFinanceira::create(array_merge($payloadTransacao, [
                'company_id'      => $companyId,
                'tipo'            => 'entrada',
                'movimentacao_id' => $movimentacao->id,
                'created_by'      => $userId,
                'created_by_name' => $userName,
            ]));
        }

        $dizimo->update([
            'movimentacao_id'      => $movimentacao->id,
            'integrado_financeiro' => true,
        ]);
    }

    /**
     * Remove a movimentação (e transação vinculada) do Dizimo, marcando-o
     * como não integrado. Idempotente.
     */
    private function removerFinanceiro(Dizimo $dizimo): void
    {
        if ($dizimo->movimentacao_id) {
            $movimentacao = Movimentacao::find($dizimo->movimentacao_id);
            if ($movimentacao) {
                TransacaoFinanceira::where('movimentacao_id', $movimentacao->id)->delete();
                $movimentacao->delete();
            }
            $dizimo->update([
                'movimentacao_id'      => null,
                'integrado_financeiro' => false,
            ]);
        } else {
            $dizimo->update(['integrado_financeiro' => false]);
        }
    }

    /**
     * Gera a sequência de datas (1 ou N — uma por mês) conforme `periodo`.
     *
     * Quando `periodo=true`, usamos o dia da `data_pagamento` original como
     * "dia de referência" para cada mês. Caso o dia não exista no mês, usa
     * o último dia válido (Carbon faz isso automaticamente com endOfMonth).
     *
     * @return string[] Lista de datas Y-m-d
     */
    private function gerarDatasPorPeriodo(array $dados): array
    {
        if (empty($dados['periodo'])) {
            return [Carbon::parse($dados['data_pagamento'])->format('Y-m-d')];
        }

        $inicio = $this->parseMesAno($dados['mes_inicio']);
        $fim    = $this->parseMesAno($dados['mes_fim']);

        if (! $inicio || ! $fim || $inicio->greaterThan($fim)) {
            // Fallback defensivo: período inválido → cria apenas um registro com a data informada.
            return [Carbon::parse($dados['data_pagamento'])->format('Y-m-d')];
        }

        $diaRef = (int) Carbon::parse($dados['data_pagamento'])->format('d');

        $datas  = [];
        $cursor = $inicio->copy();

        while ($cursor->lessThanOrEqualTo($fim)) {
            $ano    = (int) $cursor->format('Y');
            $mes    = (int) $cursor->format('m');
            $diaMax = (int) Carbon::create($ano, $mes, 1)->endOfMonth()->format('d');
            $dia    = min($diaRef, $diaMax);
            $datas[] = sprintf('%04d-%02d-%02d', $ano, $mes, $dia);
            $cursor->addMonthNoOverflow();
        }

        return $datas;
    }

    /**
     * "mm/aaaa" → Carbon (primeiro dia do mês). Retorna null para input inválido.
     */
    private function parseMesAno(?string $mmAaaa): ?Carbon
    {
        if (! $mmAaaa || ! preg_match('/^(\d{2})\/(\d{4})$/', $mmAaaa, $m)) {
            return null;
        }
        $mes = (int) $m[1];
        $ano = (int) $m[2];
        if ($mes < 1 || $mes > 12) {
            return null;
        }
        return Carbon::create($ano, $mes, 1);
    }

    private function labelRecebimento(string $tipo): string
    {
        return match ($tipo) {
            'Dízimo' => 'RECEBIMENTO DÍZIMO',
            'Doação' => 'RECEBIMENTO DOAÇÃO',
            'Oferta' => 'RECEBIMENTO OFERTA',
            default  => 'RECEBIMENTO ' . mb_strtoupper($tipo),
        };
    }

    /**
     * Atualiza `fiel_tithe.ultima_contribuicao` para a data mais recente
     * dentre os registros criados (apenas considerando "Dízimo").
     */
    private function atualizarUltimaContribuicaoFiel(int $fielId, Collection $criados): void
    {
        $maisRecente = $criados
            ->where('tipo', 'Dízimo')
            ->max('data_pagamento');

        if (! $maisRecente) {
            return;
        }

        $fiel = Fiel::with('tithe')->find($fielId);
        if ($fiel?->tithe) {
            $fiel->tithe->update(['ultima_contribuicao' => $maisRecente]);
        }
    }
}
