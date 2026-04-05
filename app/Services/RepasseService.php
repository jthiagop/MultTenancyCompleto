<?php

namespace App\Services;

use App\Models\Company;
use App\Models\EntidadeFinanceira;
use App\Models\Financeiro\Repasse;
use App\Models\Financeiro\RepasseItem;
use App\Models\Financeiro\TransacaoFinanceira;
use App\Models\LancamentoPadrao;
use App\Models\Movimentacao;
use App\Notifications\RepasseCriadoNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RepasseService
{
    /**
     * Cria um repasse e seus itens. Se já for para executar, gera as transações/movimentações.
     *
     * @param array $data Dados validados do repasse
     * @param bool  $executarImediato Se true, executa o repasse (gera transações) imediatamente
     * @return Repasse
     */
    public function criar(array $data, bool $executarImediato = false): Repasse
    {
        /** @var Repasse $repasse */
        $repasse = DB::transaction(function () use ($data, $executarImediato) {
            $user = Auth::user();

            // 1. Criar o repasse mestre
            $repasse = Repasse::create([
                'company_origem_id' => $data['company_origem_id'],
                'entidade_origem_id' => $data['entidade_origem_id'],
                'tipo' => $data['tipo'] ?? 'repasse_direto',
                'criterio_rateio' => $data['criterio_rateio'] ?? 'valor_fixo',
                'valor_total' => $data['valor_total'],
                'data_emissao' => $data['data_emissao'],
                'data_entrada' => $data['data_entrada'] ?? null,
                'data_vencimento' => $data['data_vencimento'] ?? null,
                'competencia' => $data['competencia'] ?? null,
                'tipo_documento' => $data['tipo_documento'] ?? null,
                'numero_documento' => $data['numero_documento'] ?? null,
                'forma_pagamento_id' => $data['forma_pagamento_id'] ?? null,
                'forma_recebimento_id' => $data['forma_recebimento_id'] ?? null,
                'descricao' => $data['descricao'] ?? null,
                'status' => 'pendente',
                'user_id' => $user->id,
            ]);

            // 2. Criar itens (filiais destino)
            foreach ($data['itens'] as $itemData) {
                RepasseItem::create([
                    'repasse_id' => $repasse->id,
                    'company_destino_id' => $itemData['company_destino_id'],
                    'entidade_destino_id' => $itemData['entidade_destino_id'] ?? null,
                    'percentual' => $itemData['percentual'] ?? null,
                    'valor' => $itemData['valor'],
                ]);
            }

            // 3. Se marcado para execução imediata, gera transações
            if ($executarImediato) {
                $this->executarRepasse($repasse);
            }

            return $repasse->load('itens.companyDestino');
        });

        // Notificar usuários das filiais destino
        $this->notificarFiliais($repasse);

        return $repasse;
    }

    /**
     * Atualiza um repasse pendente e seus itens.
     *
     * @param Repasse $repasse Repasse a ser atualizado (deve estar pendente)
     * @param array $data Dados validados
     * @return Repasse
     */
    public function atualizar(Repasse $repasse, array $data): Repasse
    {
        if (!$repasse->isPendente()) {
            throw new \RuntimeException('Apenas repasses pendentes podem ser editados.');
        }

        /** @var Repasse $result */
        $result = DB::transaction(function () use ($repasse, $data) {
            // 1. Atualizar o repasse mestre
            $repasse->update([
                'entidade_origem_id' => $data['entidade_origem_id'],
                'valor_total' => $data['valor_total'],
                'data_emissao' => $data['data_emissao'],
                'data_entrada' => $data['data_entrada'] ?? null,
                'data_vencimento' => $data['data_vencimento'] ?? null,
                'competencia' => $data['competencia'] ?? null,
                'tipo_documento' => $data['tipo_documento'] ?? null,
                'numero_documento' => $data['numero_documento'] ?? null,
                'forma_pagamento_id' => $data['forma_pagamento_id'] ?? null,
                'forma_recebimento_id' => $data['forma_recebimento_id'] ?? null,
                'descricao' => $data['descricao'] ?? null,
            ]);

            // 2. Recriar itens (remover antigos e criar novos)
            $repasse->itens()->delete();

            foreach ($data['itens'] as $itemData) {
                RepasseItem::create([
                    'repasse_id' => $repasse->id,
                    'company_destino_id' => $itemData['company_destino_id'],
                    'entidade_destino_id' => $itemData['entidade_destino_id'],
                    'percentual' => $itemData['percentual'] ?? null,
                    'valor' => $itemData['valor'],
                ]);
            }

            return $repasse->fresh('itens.companyDestino');
        });

        return $result;
    }

    /**
     * Executa um repasse pendente: gera transações financeiras e movimentações.
     */
    public function executarRepasse(Repasse $repasse): Repasse
    {
        if ($repasse->isExecutado()) {
            throw new \RuntimeException('Este repasse já foi executado.');
        }

        if ($repasse->isCancelado()) {
            throw new \RuntimeException('Não é possível executar um repasse cancelado.');
        }

        /** @var Repasse $result */
        $result = DB::transaction(function () use ($repasse) {
            $user = Auth::user();
            $auditFields = [
                'created_by' => $user->id,
                'created_by_name' => $user->name,
                'updated_by' => $user->id,
                'updated_by_name' => $user->name,
            ];

            $companyOrigemId = $repasse->company_origem_id;

            // Buscar/criar LPs de repasse
            $lpSaida = LancamentoPadrao::firstOrCreate(
                ['company_id' => $companyOrigemId, 'description' => 'Repasse Enviado'],
                ['type' => 'saida', 'category' => 'Repasse', 'user_id' => $user->id]
            );

            $repasse->load('itens');

            foreach ($repasse->itens as $item) {
                $lpEntrada = LancamentoPadrao::firstOrCreate(
                    ['company_id' => $item->company_destino_id, 'description' => 'Repasse Recebido'],
                    ['type' => 'entrada', 'category' => 'Repasse', 'user_id' => $user->id]
                );

                $nomeDestino = Company::find($item->company_destino_id)?->name ?? 'Filial';
                $nomeOrigem = Company::find($companyOrigemId)?->name ?? 'Matriz';

                // ── Movimentação de SAÍDA (na matriz) ──
                $movSaida = Movimentacao::create([
                    'entidade_id' => $repasse->entidade_origem_id,
                    'tipo' => 'saida',
                    'valor' => $item->valor,
                    'data' => $repasse->data_emissao,
                    'descricao' => "Repasse para {$nomeDestino}",
                    'company_id' => $companyOrigemId,
                    'lancamento_padrao_id' => $lpSaida->id,
                    'data_competencia' => $repasse->data_emissao,
                    'origem_type' => TransacaoFinanceira::class,
                    ...$auditFields,
                ]);

                // ── Transação Financeira de SAÍDA (na matriz, em_aberto) ──
                $txSaida = TransacaoFinanceira::create([
                    'company_id' => $companyOrigemId,
                    'data_competencia' => $repasse->data_emissao,
                    'data_vencimento' => $repasse->data_vencimento ?? $repasse->data_emissao,
                    'entidade_id' => $repasse->entidade_origem_id,
                    'tipo' => 'saida',
                    'valor' => $item->valor,
                    'descricao' => $repasse->descricao ?? "Repasse para {$nomeDestino}",
                    'situacao' => 'em_aberto',
                    'lancamento_padrao_id' => $lpSaida->id,
                    'movimentacao_id' => $movSaida->id,
                    'tipo_documento' => $repasse->tipo_documento,
                    'numero_documento' => $repasse->numero_documento,
                    'origem' => 'repasse',
                    'historico_complementar' => "Repasse para {$nomeDestino} - Competência: {$repasse->competencia}",
                    ...$auditFields,
                ]);

                $movSaida->update([
                    'origem_id' => $txSaida->id,
                    'origem_type' => TransacaoFinanceira::class,
                ]);

                // ── Transação Financeira de ENTRADA (na filial, em_aberto - aguarda confirmação) ──
                // A transação de entrada só é criada se a filial já tiver uma conta destino definida.
                // Caso contrário, será criada quando a filial confirmar o recebimento e informar a conta.
                $txEntrada = null;
                if ($item->entidade_destino_id) {
                    $txEntrada = TransacaoFinanceira::create([
                        'company_id' => $item->company_destino_id,
                        'data_competencia' => $repasse->data_entrada ?? $repasse->data_emissao,
                        'data_vencimento' => $repasse->data_vencimento ?? $repasse->data_emissao,
                        'entidade_id' => $item->entidade_destino_id,
                        'tipo' => 'entrada',
                        'valor' => $item->valor,
                        'descricao' => $repasse->descricao ?? "Repasse recebido de {$nomeOrigem}",
                        'situacao' => 'em_aberto',
                        'lancamento_padrao_id' => $lpEntrada->id,
                        'tipo_documento' => $repasse->tipo_documento,
                        'numero_documento' => $repasse->numero_documento,
                        'origem' => 'repasse',
                        'historico_complementar' => "Repasse de {$nomeOrigem} - Competência: {$repasse->competencia}",
                        ...$auditFields,
                    ]);
                }

                // ── Atualizar item com IDs das transações/movimentações ──
                // Mov/tx entrada será criada quando a filial confirmar o recebimento (se ainda não existir)
                $item->update([
                    'transacao_saida_id' => $txSaida->id,
                    'transacao_entrada_id' => $txEntrada?->id,
                    'movimentacao_saida_id' => $movSaida->id,
                ]);
            }

            $repasse->update(['status' => 'executado']);

            Log::info('Repasse executado com sucesso', [
                'repasse_id' => $repasse->id,
                'valor_total' => $repasse->valor_total,
                'itens' => $repasse->itens->count(),
                'user_id' => $user->id,
            ]);

            return $repasse->fresh('itens');
        });

        return $result;
    }

    /**
     * Cancela um repasse pendente.
     */
    public function cancelar(Repasse $repasse): Repasse
    {
        if ($repasse->isExecutado()) {
            throw new \RuntimeException('Não é possível cancelar um repasse já executado. Reverta primeiro.');
        }

        $repasse->update(['status' => 'cancelado']);

        return $repasse;
    }

    /**
     * Notifica os usuários das filiais destino sobre o repasse criado.
     */
    protected function notificarFiliais(Repasse $repasse): void
    {
        try {
            $nomeMatriz = $repasse->companyOrigem->name ?? 'Matriz';
            $valorFormatado = number_format((float) $repasse->valor_total, 2, ',', '.');

            foreach ($repasse->itens as $item) {
                $usuarios = $item->companyDestino?->users ?? collect();

                foreach ($usuarios as $usuario) {
                    $usuario->notify(new RepasseCriadoNotification($repasse, $nomeMatriz, $valorFormatado));
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Falha ao notificar filiais sobre repasse', [
                'repasse_id' => $repasse->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Retorna as filiais de uma empresa matriz.
     */
    public function getFiliais(int $companyOrigemId): \Illuminate\Database\Eloquent\Collection
    {
        return Company::where('parent_id', $companyOrigemId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
    }

    /**
     * Retorna entidades financeiras de uma company para uso em selects.
     */
    public function getEntidadesDeCompany(int $companyId): \Illuminate\Database\Eloquent\Collection
    {
        return EntidadeFinanceira::where('company_id', $companyId)
            ->whereIn('tipo', ['banco', 'caixa'])
            ->orderBy('nome')
            ->get();
    }

    /**
     * Calcula valores de rateio por percentual.
     */
    public function calcularRateio(float $valorTotal, array $percentuais): array
    {
        $valores = [];
        $somaCalculada = 0;

        foreach ($percentuais as $i => $pct) {
            $valor = round($valorTotal * ($pct / 100), 2);
            $valores[] = $valor;
            $somaCalculada += $valor;
        }

        // Ajuste de centavos no último item (arredondamento)
        $diff = round($valorTotal - $somaCalculada, 2);
        if ($diff != 0 && count($valores) > 0) {
            $valores[count($valores) - 1] += $diff;
        }

        return $valores;
    }

    /**
     * Lista repasses para DataTables (server-side).
     */
    public function listarParaDataTable(int $companyId, array $filters = [])
    {
        $query = Repasse::with(['companyOrigem', 'entidadeOrigem', 'formaPagamento', 'itens.companyDestino'])
            ->where(function ($q) use ($companyId) {
                // Mostrar repasses onde a company é origem OU é destino em algum item
                $q->where('company_origem_id', $companyId)
                    ->orWhereHas('itens', function ($sub) use ($companyId) {
                        $sub->where('company_destino_id', $companyId);
                    });
            });

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['competencia'])) {
            $query->where('competencia', $filters['competencia']);
        }

        return $query->orderBy('data_emissao', 'desc');
    }
}
