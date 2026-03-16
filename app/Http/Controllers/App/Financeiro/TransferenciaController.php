<?php

namespace App\Http\Controllers\App\Financeiro;

use App\Http\Controllers\Controller;
use App\Http\Requests\Financer\StoreTransferenciaRequest;
use App\Models\EntidadeFinanceira;
use App\Models\Financeiro\Transferencia;
use App\Models\Financeiro\TransacaoFinanceira;
use App\Models\LancamentoPadrao;
use App\Models\Movimentacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransferenciaController extends Controller
{
    /**
     * Processa a transferência entre contas (banco/caixa).
     *
     * Cria atomicamente:
     * 1. Registro em transferencias
     * 2. Movimentação + TransacaoFinanceira de SAÍDA (pago) na origem
     * 3. Movimentação + TransacaoFinanceira de ENTRADA (recebido) no destino
     */
    public function store(StoreTransferenciaRequest $request)
    {
        $companyId = session('active_company_id');

        if (!$companyId) {
            return response()->json([
                'success' => false,
                'message' => 'Empresa ativa não encontrada na sessão.',
            ], 422);
        }

        $validated = $request->validated();

        // Buscar entidades e validar que pertencem à empresa ativa
        $entidadeOrigem = EntidadeFinanceira::where('company_id', $companyId)
            ->findOrFail($validated['entidade_origem_id']);
        $entidadeDestino = EntidadeFinanceira::where('company_id', $companyId)
            ->findOrFail($validated['entidade_destino_id']);

        // Buscar ou criar LPs de transferência automaticamente
        $lpSaida = LancamentoPadrao::firstOrCreate(
            ['company_id' => $companyId, 'description' => 'Transferência de Saída'],
            ['type' => 'saida', 'user_id' => Auth::id()]
        );
        $lpEntrada = LancamentoPadrao::firstOrCreate(
            ['company_id' => $companyId, 'description' => 'Transferência de Entrada'],
            ['type' => 'entrada', 'user_id' => Auth::id()]
        );

        try {
            $result = DB::transaction(function () use ($validated, $companyId, $entidadeOrigem, $entidadeDestino, $lpSaida, $lpEntrada) {
                $user = Auth::user();
                $auditFields = [
                    'created_by' => $user->id,
                    'created_by_name' => $user->name,
                    'updated_by' => $user->id,
                    'updated_by_name' => $user->name,
                ];

                // ─── 1. Criar registro na tabela transferencias ───
                $transferencia = Transferencia::create([
                    'company_id' => $companyId,
                    'entidade_origem_id' => $entidadeOrigem->id,
                    'entidade_destino_id' => $entidadeDestino->id,
                    'valor' => $validated['valor'],
                    'data' => $validated['data'],
                    'descricao' => $validated['descricao'],
                    'user_id' => $user->id,
                ]);

                // ─── 2. Criar Movimentação de SAÍDA (origem) ───
                $movSaida = Movimentacao::create([
                    'entidade_id' => $entidadeOrigem->id,
                    'tipo' => 'saida',
                    'valor' => $validated['valor'],
                    'data' => $validated['data'],
                    'descricao' => $validated['descricao'],
                    'company_id' => $companyId,
                    'lancamento_padrao_id' => $lpSaida?->id,
                    'conta_debito_id' => $lpSaida?->conta_debito_id,
                    'conta_credito_id' => $lpSaida?->conta_credito_id,
                    'data_competencia' => $validated['data'],
                    'origem_type' => TransacaoFinanceira::class,
                    ...$auditFields,
                ]);

                // ─── 3. Criar TransacaoFinanceira de SAÍDA (pago) ───
                $txSaida = TransacaoFinanceira::create([
                    'company_id' => $companyId,
                    'data_competencia' => $validated['data'],
                    'data_vencimento' => $validated['data'],
                    'data_pagamento' => $validated['data'],
                    'entidade_id' => $entidadeOrigem->id,
                    'tipo' => 'saida',
                    'valor' => $validated['valor'],
                    'valor_pago' => $validated['valor'],
                    'descricao' => $validated['descricao'],
                    'situacao' => 'pago',
                    'lancamento_padrao_id' => $lpSaida?->id,
                    'movimentacao_id' => $movSaida->id,
                    'transferencia_id' => $transferencia->id,
                    'origem' => 'transferencia',
                    'historico_complementar' => 'Transferência para ' . $entidadeDestino->nome,
                    ...$auditFields,
                ]);

                // Vincular a movimentação à transação via morph
                $movSaida->update([
                    'origem_id' => $txSaida->id,
                    'origem_type' => TransacaoFinanceira::class,
                ]);

                // ─── 4. Criar Movimentação de ENTRADA (destino) ───
                $movEntrada = Movimentacao::create([
                    'entidade_id' => $entidadeDestino->id,
                    'tipo' => 'entrada',
                    'valor' => $validated['valor'],
                    'data' => $validated['data'],
                    'descricao' => $validated['descricao'],
                    'company_id' => $companyId,
                    'lancamento_padrao_id' => $lpEntrada?->id,
                    'conta_debito_id' => $lpEntrada?->conta_debito_id,
                    'conta_credito_id' => $lpEntrada?->conta_credito_id,
                    'data_competencia' => $validated['data'],
                    'origem_type' => TransacaoFinanceira::class,
                    ...$auditFields,
                ]);

                // ─── 5. Criar TransacaoFinanceira de ENTRADA (recebido) ───
                $txEntrada = TransacaoFinanceira::create([
                    'company_id' => $companyId,
                    'data_competencia' => $validated['data'],
                    'data_vencimento' => $validated['data'],
                    'data_pagamento' => $validated['data'],
                    'entidade_id' => $entidadeDestino->id,
                    'tipo' => 'entrada',
                    'valor' => $validated['valor'],
                    'valor_pago' => $validated['valor'],
                    'descricao' => $validated['descricao'],
                    'situacao' => 'recebido',
                    'lancamento_padrao_id' => $lpEntrada?->id,
                    'movimentacao_id' => $movEntrada->id,
                    'transferencia_id' => $transferencia->id,
                    'origem' => 'transferencia',
                    'historico_complementar' => 'Transferência de ' . $entidadeOrigem->nome,
                    ...$auditFields,
                ]);

                // Vincular a movimentação à transação via morph
                $movEntrada->update([
                    'origem_id' => $txEntrada->id,
                    'origem_type' => TransacaoFinanceira::class,
                ]);

                return $transferencia;
            });

            Log::info('Transferência criada com sucesso', [
                'transferencia_id' => $result->id,
                'origem' => $entidadeOrigem->nome,
                'destino' => $entidadeDestino->nome,
                'valor' => $validated['valor'],
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Transferência realizada com sucesso!',
                'transferencia_id' => $result->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao processar transferência', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $validated,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar transferência: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Retorna os dados de uma transferência para edição.
     */
    public function show(int $id)
    {
        $companyId = session('active_company_id');

        $transferencia = Transferencia::where('company_id', $companyId)
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'transferencia' => [
                'id' => $transferencia->id,
                'entidade_origem_id' => $transferencia->entidade_origem_id,
                'entidade_destino_id' => $transferencia->entidade_destino_id,
                'descricao' => $transferencia->descricao,
                'data' => $transferencia->data ? $transferencia->data->format('d/m/Y') : null,
                'valor' => number_format($transferencia->valor, 2, ',', '.'),
            ],
        ]);
    }

    /**
     * Atualiza uma transferência existente.
     *
     * Reverte as movimentações/transações antigas e recria com os novos dados.
     */
    public function update(StoreTransferenciaRequest $request, int $id)
    {
        $companyId = session('active_company_id');

        if (!$companyId) {
            return response()->json([
                'success' => false,
                'message' => 'Empresa ativa não encontrada na sessão.',
            ], 422);
        }

        $validated = $request->validated();

        $transferencia = Transferencia::where('company_id', $companyId)
            ->findOrFail($id);

        $entidadeOrigem = EntidadeFinanceira::where('company_id', $companyId)
            ->findOrFail($validated['entidade_origem_id']);
        $entidadeDestino = EntidadeFinanceira::where('company_id', $companyId)
            ->findOrFail($validated['entidade_destino_id']);

        $lpSaida = LancamentoPadrao::firstOrCreate(
            ['company_id' => $companyId, 'description' => 'Transferência de Saída'],
            ['type' => 'saida', 'user_id' => Auth::id()]
        );
        $lpEntrada = LancamentoPadrao::firstOrCreate(
            ['company_id' => $companyId, 'description' => 'Transferência de Entrada'],
            ['type' => 'entrada', 'user_id' => Auth::id()]
        );

        try {
            $result = DB::transaction(function () use ($validated, $companyId, $transferencia, $entidadeOrigem, $entidadeDestino, $lpSaida, $lpEntrada) {
                $user = Auth::user();
                $auditFields = [
                    'updated_by' => $user->id,
                    'updated_by_name' => $user->name,
                ];

                // ─── 1. Remover transações e movimentações antigas ───
                $transacoesAntigas = TransacaoFinanceira::where('transferencia_id', $transferencia->id)->get();
                foreach ($transacoesAntigas as $tx) {
                    // Deletar a movimentação associada (o Observer reverterá o saldo)
                    if ($tx->movimentacao_id) {
                        Movimentacao::where('id', $tx->movimentacao_id)->delete();
                    }
                    $tx->delete();
                }

                // ─── 2. Atualizar registro da transferência ───
                $transferencia->update([
                    'entidade_origem_id' => $entidadeOrigem->id,
                    'entidade_destino_id' => $entidadeDestino->id,
                    'valor' => $validated['valor'],
                    'data' => $validated['data'],
                    'descricao' => $validated['descricao'],
                ]);

                // ─── 3. Recriar Movimentação de SAÍDA (origem) ───
                $movSaida = Movimentacao::create([
                    'entidade_id' => $entidadeOrigem->id,
                    'tipo' => 'saida',
                    'valor' => $validated['valor'],
                    'data' => $validated['data'],
                    'descricao' => $validated['descricao'],
                    'company_id' => $companyId,
                    'lancamento_padrao_id' => $lpSaida?->id,
                    'conta_debito_id' => $lpSaida?->conta_debito_id,
                    'conta_credito_id' => $lpSaida?->conta_credito_id,
                    'data_competencia' => $validated['data'],
                    'origem_type' => TransacaoFinanceira::class,
                    'created_by' => $user->id,
                    'created_by_name' => $user->name,
                    ...$auditFields,
                ]);

                // ─── 4. Recriar TransacaoFinanceira de SAÍDA (pago) ───
                $txSaida = TransacaoFinanceira::create([
                    'company_id' => $companyId,
                    'data_competencia' => $validated['data'],
                    'data_vencimento' => $validated['data'],
                    'data_pagamento' => $validated['data'],
                    'entidade_id' => $entidadeOrigem->id,
                    'tipo' => 'saida',
                    'valor' => $validated['valor'],
                    'valor_pago' => $validated['valor'],
                    'descricao' => $validated['descricao'],
                    'situacao' => 'pago',
                    'lancamento_padrao_id' => $lpSaida?->id,
                    'movimentacao_id' => $movSaida->id,
                    'transferencia_id' => $transferencia->id,
                    'origem' => 'transferencia',
                    'historico_complementar' => 'Transferência para ' . $entidadeDestino->nome,
                    'created_by' => $user->id,
                    'created_by_name' => $user->name,
                    ...$auditFields,
                ]);

                $movSaida->update([
                    'origem_id' => $txSaida->id,
                    'origem_type' => TransacaoFinanceira::class,
                ]);

                // ─── 5. Recriar Movimentação de ENTRADA (destino) ───
                $movEntrada = Movimentacao::create([
                    'entidade_id' => $entidadeDestino->id,
                    'tipo' => 'entrada',
                    'valor' => $validated['valor'],
                    'data' => $validated['data'],
                    'descricao' => $validated['descricao'],
                    'company_id' => $companyId,
                    'lancamento_padrao_id' => $lpEntrada?->id,
                    'conta_debito_id' => $lpEntrada?->conta_debito_id,
                    'conta_credito_id' => $lpEntrada?->conta_credito_id,
                    'data_competencia' => $validated['data'],
                    'origem_type' => TransacaoFinanceira::class,
                    'created_by' => $user->id,
                    'created_by_name' => $user->name,
                    ...$auditFields,
                ]);

                // ─── 6. Recriar TransacaoFinanceira de ENTRADA (recebido) ───
                $txEntrada = TransacaoFinanceira::create([
                    'company_id' => $companyId,
                    'data_competencia' => $validated['data'],
                    'data_vencimento' => $validated['data'],
                    'data_pagamento' => $validated['data'],
                    'entidade_id' => $entidadeDestino->id,
                    'tipo' => 'entrada',
                    'valor' => $validated['valor'],
                    'valor_pago' => $validated['valor'],
                    'descricao' => $validated['descricao'],
                    'situacao' => 'recebido',
                    'lancamento_padrao_id' => $lpEntrada?->id,
                    'movimentacao_id' => $movEntrada->id,
                    'transferencia_id' => $transferencia->id,
                    'origem' => 'transferencia',
                    'historico_complementar' => 'Transferência de ' . $entidadeOrigem->nome,
                    'created_by' => $user->id,
                    'created_by_name' => $user->name,
                    ...$auditFields,
                ]);

                $movEntrada->update([
                    'origem_id' => $txEntrada->id,
                    'origem_type' => TransacaoFinanceira::class,
                ]);

                return $transferencia;
            });

            Log::info('Transferência atualizada com sucesso', [
                'transferencia_id' => $result->id,
                'origem' => $entidadeOrigem->nome,
                'destino' => $entidadeDestino->nome,
                'valor' => $validated['valor'],
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Transferência atualizada com sucesso!',
                'transferencia_id' => $result->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar transferência', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'transferencia_id' => $id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar transferência: ' . $e->getMessage(),
            ], 500);
        }
    }
}
