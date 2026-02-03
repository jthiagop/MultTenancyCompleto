<?php

namespace App\Services;

use App\Models\Financeiro\TransacaoFinanceira;
use App\Models\Movimentacao;
use App\Models\LancamentoPadrao;
use App\Models\Financeiro\Recorrencia;
use App\Models\Banco;
use App\Models\Financeiro\ModulosAnexo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

/**
 * Service para gerenciar operações de Transações Financeiras
 * Centraliza toda a lógica de negócio relacionada a lançamentos financeiros
 */
class TransacaoFinanceiraService
{
    protected $recurrenceService;

    public function __construct(RecurrenceService $recurrenceService)
    {
        $this->recurrenceService = $recurrenceService;
    }

    /**
     * Cria um novo lançamento financeiro com todas as suas dependências
     * 
     * Padrão profissional:
     * - Transação DB envolvendo apenas operações em banco
     * - Anexos processados DEPOIS do commit (DB::afterCommit)
     * - Não retorna model deletado
     * - Retorna sempre um model válido
     * 
     * @param array $validatedData Dados validados do request
     * @param Request $request Request original para acessar dados não validados
     * @return TransacaoFinanceira Transação criada ou primeira parcela se parcelado
     * @throws \Exception
     */
    public function criarLancamento(array $validatedData, Request $request): TransacaoFinanceira
    {
        $transacao = DB::transaction(function () use ($validatedData, $request) {
            // 1. Prepara os dados
            $data = $this->prepararDados($validatedData, $request);
            
            // 2. Calcula a situação baseada no checkbox "Pago"
            $data['situacao'] = $this->calcularSituacao($request);
            
            // 3. Verifica se será parcelado
            $temParcelas = $this->temParcelas($request);
            
            if (!$temParcelas) {
                // Transação comum (sem parcelas)
                $transacao = TransacaoFinanceira::create($data);
                
                // 4. Verifica se tem pagamento REAL (checkbox pago/recebido marcado)
                $temPagamentoReal = $this->temPagamento($request, $data);
                
                if ($temPagamentoReal) {
                    // ✅ Só cria movimentação se houver pagamento/recebimento REAL
                    // A tabela movimentacoes é a "tabela da verdade" - só registra fatos consumados
                    $movimentacao = $transacao->movimentacao()->create($this->prepararDadosMovimentacao($data));
                    
                    // Processa pagamento (atualiza saldo, etc)
                    $this->processarPagamento($transacao, $movimentacao, $data, $request);
                    
                    Log::info('✅ Movimentação criada - Pagamento/Recebimento real', [
                        'transacao_id' => $transacao->id,
                        'movimentacao_id' => $movimentacao->id,
                        'tipo' => $transacao->tipo,
                        'situacao' => $data['situacao']
                    ]);
                } else {
                    // ❌ NÃO cria movimentação para compromissos futuros
                    // Apenas a transação_financeira é criada (é o compromisso/planejamento)
                    Log::info('📋 Apenas transação criada (compromisso futuro) - Sem movimentação', [
                        'transacao_id' => $transacao->id,
                        'tipo' => $transacao->tipo,
                        'situacao' => $data['situacao'],
                        'data_vencimento' => $data['data_vencimento'] ?? null
                    ]);
                }
                
                // 5. Processa lançamento padrão especial (Depósito Bancário)
                $this->processarLancamentoPadrao($transacao, $data);
                
                // 6. Processa recorrência (se houver)
                if ($this->temRecorrencia($request)) {
                    $this->processarRecorrencia($transacao, $data, $request);
                }
            } else {
                // Transação com parcelas (cria múltiplas transações)
                // ❌ Parcelas são compromissos futuros - NÃO criam movimentação
                $transacao = $this->processarParcelas(null, $data, $request);
            }
            
            return $transacao;
        });
        
        /** @var \App\Models\Financeiro\TransacaoFinanceira $transacao */
        
        // 8. Processa anexos APÓS commit
        // Se falhar, não afeta a transação criada no banco
        DB::afterCommit(function () use ($request, $transacao) {
            try {
                $this->processarAnexos($request, $transacao);
            } catch (\Exception $e) {
                Log::warning('Erro ao processar anexos após commit', [
                    'transacao_id' => $transacao->id,
                    'erro' => $e->getMessage()
                ]);
                // Não relança - transação já foi commitada com sucesso
            }
        });
        
        return $transacao;
    }

    /**
     * Prepara os dados para criação da transação
     */
    protected function prepararDados(array $validatedData, Request $request): array
    {
        $data = $validatedData;
        
        
        // Converte data de competência (formato d/m/Y ou d-m-Y para Y-m-d)
        // NOTA: Se já vier no formato Y-m-d (convertido no Request), não faz nada
        if (isset($data['data_competencia'])) {
            $dateValue = $data['data_competencia'];
            
            // Se NÃO está no formato Y-m-d (não tem hífen na posição correta), converte
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateValue)) {
                if (str_contains($dateValue, '/')) {
                    $data['data_competencia'] = Carbon::createFromFormat('d/m/Y', $dateValue)->format('Y-m-d');
                } else {
                    $data['data_competencia'] = Carbon::createFromFormat('d-m-Y', $dateValue)->format('Y-m-d');
                }
            }
            // Se já está no formato Y-m-d, mantém como está
        }
        
        // Remove campo 'vencimento' se existir (já foi convertido para 'data_vencimento' no Request)
        if (isset($data['vencimento'])) {
            unset($data['vencimento']);
        }
        
        // Se data_vencimento não foi fornecida, usa a data de competência
        if (!isset($data['data_vencimento']) || !$data['data_vencimento']) {
            $data['data_vencimento'] = $data['data_competencia'];
        }
        
        // Processa valor_pago
        // Verifica se o checkbox 'pago' (saída) ou 'recebido' (entrada) está marcado
        if (!isset($data['valor_pago']) || !$data['valor_pago']) {
            $isPago = $request->has('pago') && $request->input('pago');
            $isRecebido = $request->has('recebido') && $request->input('recebido');
            
            $data['valor_pago'] = ($isPago || $isRecebido) 
                ? (float) $data['valor'] 
                : 0;
        }
        
        // Processa juros, multa, desconto
        $data['juros'] = (float) ($data['juros'] ?? 0);
        $data['multa'] = (float) ($data['multa'] ?? 0);
        $data['desconto'] = (float) ($data['desconto'] ?? 0);
        
        // Calcula valor_a_pagar
        if (!isset($data['valor_a_pagar']) || !$data['valor_a_pagar']) {
            $valor = (float) $data['valor'];
            $juros = $data['juros'];
            $multa = $data['multa'];
            $desconto = $data['desconto'];
            $data['valor_a_pagar'] = max(0, $valor + $juros + $multa - $desconto);
        }
        
        // Processa checkbox agendado
        $data['agendado'] = $request->has('agendado') && $request->input('agendado') ? true : false;

        // Mapeia fornecedor_id (vindo do request/blade) para parceiro_id (banco de dados)
        if (isset($data['fornecedor_id'])) {
            $data['parceiro_id'] = $data['fornecedor_id'];
        }
        
        // Adiciona informações de auditoria
        $data['created_by'] = Auth::id();
        $data['created_by_name'] = Auth::user()->name;
        $data['updated_by'] = Auth::id();
        $data['updated_by_name'] = Auth::user()->name;
        
        return $data;
    }

    /**
     * Calcula a situação baseada nos checkboxes "Pago" (saída) ou "Recebido" (entrada)
     * 
     * @param Request $request
     */
    protected function calcularSituacao(Request $request): string
    {
        $tipo = $request->input('tipo');
        
        // Log de debug
        \Log::info('[calcularSituacao] Calculando situação', [
            'tipo' => $tipo,
            'has_recebido' => $request->has('recebido'),
            'input_recebido' => $request->input('recebido'),
            'has_pago' => $request->has('pago'),
            'input_pago' => $request->input('pago'),
            'all_inputs' => $request->all()
        ]);
        
        // Para ENTRADA: verificar checkbox "recebido"
        if ($tipo === 'entrada') {
            if ($request->has('recebido') && $request->input('recebido')) {
                \Log::info('[calcularSituacao] Definindo como RECEBIDO');
                return \App\Enums\SituacaoTransacao::RECEBIDO->value;
            }
        }
        
        // Para SAÍDA: verificar checkbox "pago"
        if ($tipo === 'saida') {
            if ($request->has('pago') && $request->input('pago')) {
                \Log::info('[calcularSituacao] Definindo como PAGO');
                return \App\Enums\SituacaoTransacao::PAGO->value;
            }
        }
        
        \Log::info('[calcularSituacao] Definindo como EM_ABERTO (padrão)');
        return \App\Enums\SituacaoTransacao::EM_ABERTO->value;
    }

    /**
     * Prepara os dados para criar a movimentação via Eloquent
     * Extrai apenas os campos necessários para a tabela movimentacoes
     */
    protected function prepararDadosMovimentacao(array $data): array
    {
        // Busca informações do lançamento padrão
        $contaDebitoId = null;
        $contaCreditoId = null;
        $lancamentoPadraoId = null;

        if (isset($data['lancamento_padrao_id']) && $data['lancamento_padrao_id']) {
            $lancamentoPadraoId = $data['lancamento_padrao_id'];
            $lancamentoPadrao = LancamentoPadrao::find($lancamentoPadraoId);

            if ($lancamentoPadrao) {
                $lancamentoPadrao->refresh();
                
                $contaDebitoId = $data['conta_debito_id'] ?? $lancamentoPadrao->conta_debito_id;
                $contaCreditoId = $data['conta_credito_id'] ?? $lancamentoPadrao->conta_credito_id;
            }
        }

        // Retorna apenas os dados necessários para Movimentacao
        // O polimorfismo (origem_type e origem_id) é preenchido automaticamente pelo Eloquent
        return [
            'entidade_id' => $data['entidade_id'],
            'tipo' => $data['tipo'],
            'valor' => $data['valor'],
            'data' => $data['data_competencia'],
            'descricao' => $data['descricao'],
            'company_id' => $data['company_id'],
            'created_by' => $data['created_by'],
            'created_by_name' => $data['created_by_name'],
            'updated_by' => $data['updated_by'],
            'updated_by_name' => $data['updated_by_name'],
            'lancamento_padrao_id' => $lancamentoPadraoId,
            'conta_debito_id' => $contaDebitoId,
            'conta_credito_id' => $contaCreditoId,
            'data_competencia' => $data['data_competencia'],
        ];
    }
    
    /**
     * Remove o método antigo criarMovimentacao() - agora usamos Eloquent
     */

    /**
     * Verifica se há pagamento a ser processado
     * IMPORTANTE: Só processa pagamento se o checkbox "pago" ou "recebido" estiver marcado
     */
    protected function temPagamento(Request $request, array $data): bool
    {
        $tipo = $request->input('tipo');
        
        // Para ENTRADA: só processar pagamento se checkbox "recebido" estiver marcado
        if ($tipo === 'entrada') {
            $hasRecebido = $request->has('recebido') && $request->input('recebido') && $request->input('recebido') !== '0';
            
            Log::info('[temPagamento] Verificando checkbox recebido', [
                'tipo' => $tipo,
                'has_recebido' => $request->has('recebido'),
                'input_recebido' => $request->input('recebido'),
                'resultado' => $hasRecebido
            ]);
            
            return $hasRecebido;
        }
        
        // Para SAÍDA: só processar pagamento se checkbox "pago" estiver marcado
        if ($tipo === 'saida') {
            $hasPago = $request->has('pago') && $request->input('pago') && $request->input('pago') !== '0';
            
            Log::info('[temPagamento] Verificando checkbox pago', [
                'tipo' => $tipo,
                'has_pago' => $request->has('pago'),
                'input_pago' => $request->input('pago'),
                'resultado' => $hasPago
            ]);
            
            return $hasPago;
        }
        
        return false;
    }

    /**
     * Processa o pagamento (completo ou parcial)
     */
    protected function processarPagamento(
        TransacaoFinanceira $transacao,
        Movimentacao $movimentacao,
        array $data,
        Request $request
    ): void {
        $jurosPago = (float) ($request->input('juros_pagamento', 0));
        $multaPago = (float) ($request->input('multa_pagamento', 0));
        $descontoPago = (float) ($request->input('desconto_pagamento', 0));

        // Valor para comparação (sem desconto)
        $valorParaComparacao = $data['valor_pago'] + $jurosPago + $multaPago;

        // Verifica se é pagamento completo ou parcial
        if (abs($valorParaComparacao - $data['valor']) < 0.01 || $valorParaComparacao >= $data['valor']) {
            // Pagamento completo - define situação baseada no tipo
            // Entrada → recebido | Saída → pago
            $transacao->situacao = ($transacao->tipo === 'entrada') 
                ? \App\Enums\SituacaoTransacao::RECEBIDO
                : \App\Enums\SituacaoTransacao::PAGO;
            $transacao->valor_pago = $data['valor_pago'];

            if ($request->has('juros_pagamento')) {
                $transacao->juros = $jurosPago;
            }
            if ($request->has('multa_pagamento')) {
                $transacao->multa = $multaPago;
            }
            if ($request->has('desconto_pagamento')) {
                $transacao->desconto = $descontoPago;
            }

            // Atualiza data de vencimento se data de pagamento foi informada
            if ($request->has('data_pagamento') && $request->input('data_pagamento')) {
                $transacao->data_vencimento = $request->input('data_pagamento');
            }

            $transacao->save();
            
            // ✅ ATUALIZA SALDO DA ENTIDADE quando recebido/pago está marcado
            $this->atualizarSaldoEntidade($transacao, $data);
        }
        // Pagamento parcial será tratado por método específico do controller
    }
    
    /**
     * Atualiza o saldo da entidade financeira quando recebido/pago está marcado
     * 
     * @param TransacaoFinanceira $transacao
     * @param array $data
     */
    /**
     * Atualiza o saldo da entidade financeira
     * 
     * @param TransacaoFinanceira $transacao Transação associada
     * @param array $data Dados com 'valor' e 'tipo' (usa dados da transação se não fornecidos)
     *                    - valor: valor a ser aplicado no saldo
     *                    - tipo: 'entrada' (soma) ou 'saida' (subtrai)
     */
    protected function atualizarSaldoEntidade(TransacaoFinanceira $transacao, array $data): void
    {
        try {
            $entidade = \App\Models\EntidadeFinanceira::find($transacao->entidade_id);
            
            if (!$entidade) {
                Log::warning('Entidade financeira não encontrada ao atualizar saldo', [
                    'entidade_id' => $transacao->entidade_id,
                    'transacao_id' => $transacao->id
                ]);
                return;
            }
            
            $saldoAntes = $entidade->saldo_atual;
            
            // ✅ Usa valor do $data se fornecido, senão usa valor da transação
            $valor = $data['valor'] ?? $transacao->valor;
            $valorEmReais = (string) abs((float) $valor);
            
            // ✅ Usa tipo do $data se fornecido, senão usa tipo da transação
            // Isso permite inverter a operação no extorno
            $tipo = $data['tipo'] ?? $transacao->tipo;
            
            // Calcula incremento com base no tipo usando bcmath
            // - entrada (receita) → soma ao saldo (dinheiro entrando)
            // - saida (despesa) → subtrai do saldo (dinheiro saindo)
            if ($tipo === 'entrada') {
                // Entrada (recebido) → soma ao saldo
                $valorParaAdicionar = $valorEmReais;
            } else {
                // Saída (pago) → subtrai do saldo
                $valorParaAdicionar = bcmul($valorEmReais, '-1', 2);
            }
            
            // Atualiza saldo usando bcmath (DECIMAL precisão)
            $saldoAtualStr = (string) $entidade->saldo_atual;
            $entidade->saldo_atual = bcadd($saldoAtualStr, $valorParaAdicionar, 2);
            $entidade->save();
            
            Log::info('✅ Saldo da entidade atualizado', [
                'entidade_id' => $entidade->id,
                'transacao_id' => $transacao->id,
                'tipo_operacao' => $tipo,
                'tipo_transacao_original' => $transacao->tipo,
                'valor_aplicado' => $valorEmReais,
                'valor_adicionado' => $valorParaAdicionar,
                'saldo_antes' => $saldoAntes,
                'saldo_depois' => $entidade->saldo_atual,
                'calculo' => "{$saldoAntes} + ({$valorParaAdicionar}) = {$entidade->saldo_atual}"
            ]);
        } catch (\Exception $e) {
            Log::error('❌ Erro ao atualizar saldo da entidade', [
                'entidade_id' => $transacao->entidade_id,
                'transacao_id' => $transacao->id,
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            // Não relança a exceção para não interromper o fluxo
        }
    }

    /**
     * Registra a baixa de uma transação (pagamento/recebimento)
     * Este método é chamado quando uma transação que estava "em_aberto" é marcada como paga/recebida
     * 
     * IMPORTANTE: Este é o momento em que a MOVIMENTAÇÃO é criada
     * A movimentação é a "tabela da verdade" - só registra fatos consumados
     * 
     * @param TransacaoFinanceira $transacao Transação a ser baixada
     * @param array $dadosBaixa Dados da baixa (valor_pago, data_pagamento, juros, multa, desconto)
     * @return Movimentacao|null Movimentação criada ou null se já existia
     */
    public function registrarBaixa(TransacaoFinanceira $transacao, array $dadosBaixa = []): ?Movimentacao
    {
        /** @var Movimentacao|null $result */
        $result = DB::transaction(function () use ($transacao, $dadosBaixa) {
            // Determina o valor pago (usa valor da transação se não especificado)
            $valorPago = $dadosBaixa['valor_pago'] ?? $transacao->valor;
            $dataPagamento = $dadosBaixa['data_pagamento'] ?? now()->format('Y-m-d');
            
            // Atualiza a transação com os dados da baixa (sempre atualiza)
            $situacao = $transacao->tipo === 'entrada' 
                ? \App\Enums\SituacaoTransacao::RECEBIDO 
                : \App\Enums\SituacaoTransacao::PAGO;
            
            $transacao->update([
                'situacao' => $situacao,
                'valor_pago' => $valorPago,
                'data_pagamento' => $dataPagamento,
                'juros' => $dadosBaixa['juros'] ?? $transacao->juros ?? 0,
                'multa' => $dadosBaixa['multa'] ?? $transacao->multa ?? 0,
                'desconto' => $dadosBaixa['desconto'] ?? $transacao->desconto ?? 0,
                'updated_by' => Auth::id(),
                'updated_by_name' => Auth::user()->name ?? 'Sistema',
            ]);

            Log::info('✅ Transação atualizada para pago/recebido', [
                'transacao_id' => $transacao->id,
                'tipo' => $transacao->tipo,
                'situacao' => $situacao->value,
                'valor_pago' => $valorPago,
                'data_pagamento' => $dataPagamento
            ]);

            // Verifica se já existe movimentação (evita duplicação)
            if ($transacao->movimentacao()->exists()) {
                Log::info('📋 Movimentação já existe para esta transação', [
                    'transacao_id' => $transacao->id,
                    'movimentacao_id' => $transacao->movimentacao->id ?? null
                ]);
                return $transacao->movimentacao;
            }

            // ✅ Agora sim, cria a MOVIMENTAÇÃO (registro real na tabela da verdade)
            $dadosMovimentacao = [
                'entidade_id' => $transacao->entidade_id,
                'tipo' => $transacao->tipo,
                'valor' => $valorPago,
                'data' => $dataPagamento,
                'descricao' => $transacao->descricao,
                'company_id' => $transacao->company_id,
                'created_by' => Auth::id(),
                'created_by_name' => Auth::user()->name ?? 'Sistema',
                'updated_by' => Auth::id(),
                'updated_by_name' => Auth::user()->name ?? 'Sistema',
                'lancamento_padrao_id' => $transacao->lancamento_padrao_id,
                'data_competencia' => $transacao->data_competencia,
            ];

            $movimentacao = $transacao->movimentacao()->create($dadosMovimentacao);

            // Atualiza o saldo da entidade
            $this->atualizarSaldoEntidade($transacao, [
                'valor' => $valorPago,
                'tipo' => $transacao->tipo
            ]);

            Log::info('✅ Baixa registrada - Movimentação criada', [
                'transacao_id' => $transacao->id,
                'movimentacao_id' => $movimentacao->id,
                'tipo' => $transacao->tipo,
                'situacao' => $transacao->situacao->value ?? $transacao->situacao,
                'valor_pago' => $valorPago,
                'data_pagamento' => $dataPagamento
            ]);

            return $movimentacao;
        });
        
        return $result;
    }

    /**
     * Reverte a baixa de uma transação (marca como em_aberto)
     * - Exclui a movimentação associada
     * - Reverte o saldo da entidade financeira
     * - Zera o valor_pago e muda situação para em_aberto
     * 
     * @param TransacaoFinanceira $transacao Transação a ser revertida
     * @return bool True se reverteu com sucesso
     */
    public function reverterBaixa(TransacaoFinanceira $transacao): bool
    {
        /** @var bool $result */
        $result = DB::transaction(function () use ($transacao) {
            // Verifica se existe movimentação para excluir
            $movimentacao = $transacao->movimentacao;
            
            if ($movimentacao) {
                $valorMovimentacao = $movimentacao->valor;
                $tipoMovimentacao = $movimentacao->tipo;
                
                // Reverte o saldo da entidade (operação inversa)
                // Se era entrada, subtrai do saldo. Se era saída, soma ao saldo.
                $this->atualizarSaldoEntidade($transacao, [
                    'valor' => $valorMovimentacao,
                    'tipo' => $tipoMovimentacao === 'entrada' ? 'saida' : 'entrada' // Inverte o tipo
                ]);
                
                // Exclui a movimentação
                $movimentacao->delete();
                
                Log::info('🔄 Movimentação excluída e saldo revertido', [
                    'transacao_id' => $transacao->id,
                    'movimentacao_id' => $movimentacao->id,
                    'valor_revertido' => $valorMovimentacao,
                    'tipo_original' => $tipoMovimentacao
                ]);
            }
            
            // Atualiza a transação para em_aberto
            $transacao->update([
                'situacao' => \App\Enums\SituacaoTransacao::EM_ABERTO,
                'valor_pago' => 0,
                'data_pagamento' => null,
                'updated_by' => Auth::id(),
                'updated_by_name' => Auth::user()->name ?? 'Sistema',
            ]);
            
            Log::info('✅ Transação revertida para em_aberto', [
                'transacao_id' => $transacao->id,
                'tipo' => $transacao->tipo
            ]);
            
            return true;
        });
        
        return $result;
    }

    /**
     * Processa lançamento padrão especial (Depósito Bancário)
     * Agora usa Eloquent para criar a movimentação via polimorfismo
     */
    protected function processarLancamentoPadrao(TransacaoFinanceira $transacao, array $data): void
    {
        if (!isset($data['lancamento_padrao_id'])) {
            return;
        }

        $lancamentoPadrao = LancamentoPadrao::find($data['lancamento_padrao_id']);
        
        if ($lancamentoPadrao && $lancamentoPadrao->description === 'Deposito Bancário') {
            $lancamentoPadrao->refresh();

            // Prepara dados para a movimentação do depósito
            $dadosMovimentacao = [
                'entidade_id' => $data['entidade_banco_id'],
                'tipo' => 'entrada',
                'valor' => $data['valor'],
                'descricao' => $data['descricao'],
                'company_id' => $data['company_id'],
                'created_by' => $data['created_by'],
                'created_by_name' => $data['created_by_name'],
                'updated_by' => $data['updated_by'],
                'updated_by_name' => $data['updated_by_name'],
                'lancamento_padrao_id' => $lancamentoPadrao->id,
                'conta_debito_id' => $lancamentoPadrao->conta_debito_id ?? null,
                'conta_credito_id' => $lancamentoPadrao->conta_credito_id ?? null,
                'data_competencia' => $data['data_competencia'],
            ];

            // 🔗 Usa Eloquent para criar a movimentação via polimorfismo
            $transacao->movimentacao()->create($dadosMovimentacao);

            // Cria o lançamento no banco (SEM usar movimentacao_id)
            Banco::create($data);
        }
    }

    /**
     * Processa os anexos enviados
     */
    protected function processarAnexos(Request $request, TransacaoFinanceira $transacao): void
    {
        if (!$request->has('anexos') || !is_array($request->input('anexos'))) {
            return;
        }

        $anexos = $request->input('anexos');
        $allFiles = $request->allFiles();

        foreach ($anexos as $index => $anexoData) {
            $formaAnexo = $anexoData['forma_anexo'] ?? 'arquivo';
            $tipoAnexo = $anexoData['tipo_anexo'] ?? null;
            $descricao = $anexoData['descricao'] ?? null;

            if ($formaAnexo === 'arquivo') {
                $file = $this->buscarArquivo($request, $allFiles, $index);

                if ($file && $file->isValid()) {
                    $this->salvarArquivo($file, $transacao, $tipoAnexo, $descricao);
                }
            } elseif ($formaAnexo === 'link') {
                $link = $anexoData['link'] ?? null;
                
                if ($link) {
                    $this->salvarLink($link, $transacao, $tipoAnexo, $descricao);
                }
            }
        }

        // Atualiza comprovação fiscal
        $transacao->updateComprovacaoFiscal();
    }

    /**
     * Busca arquivo no request
     */
    protected function buscarArquivo(Request $request, array $allFiles, int $index)
    {
        $fileKey = "anexos.{$index}.arquivo";
        
        if ($request->hasFile($fileKey)) {
            return $request->file($fileKey);
        }
        
        if (isset($allFiles['anexos'][$index]['arquivo'])) {
            return $allFiles['anexos'][$index]['arquivo'];
        }
        
        return null;
    }

    /**
     * Salva arquivo anexo
     */
    protected function salvarArquivo($file, TransacaoFinanceira $transacao, $tipoAnexo, $descricao): void
    {
        try {
            $nomeOriginal = $file->getClientOriginalName();
            $anexoName = time() . '_' . $nomeOriginal;
            $anexoPath = $file->storeAs('anexos', $anexoName, 'public');

            ModulosAnexo::create([
                'anexavel_id' => $transacao->id,
                'anexavel_type' => TransacaoFinanceira::class,
                'forma_anexo' => 'arquivo',
                'nome_arquivo' => $nomeOriginal,
                'caminho_arquivo' => $anexoPath,
                'tipo_arquivo' => $file->getMimeType() ?? '',
                'extensao_arquivo' => $file->getClientOriginalExtension(),
                'mime_type' => $file->getMimeType() ?? '',
                'tamanho_arquivo' => $file->getSize(),
                'tipo_anexo' => $tipoAnexo,
                'descricao' => $descricao,
                'status' => 'ativo',
                'data_upload' => now(),
                'created_by' => Auth::id(),
                'created_by_name' => Auth::user()->name,
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao salvar anexo', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Salva link anexo
     */
    protected function salvarLink(string $link, TransacaoFinanceira $transacao, $tipoAnexo, $descricao): void
    {
        try {
            ModulosAnexo::create([
                'anexavel_id' => $transacao->id,
                'anexavel_type' => TransacaoFinanceira::class,
                'forma_anexo' => 'link',
                'link' => $link,
                'tipo_anexo' => $tipoAnexo,
                'descricao' => $descricao,
                'status' => 'ativo',
                'data_upload' => now(),
                'created_by' => Auth::id(),
                'created_by_name' => Auth::user()->name,
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao salvar link', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Verifica se há recorrência a ser processada
     * O checkbox 'repetir_lancamento' deve estar marcado para processar recorrência
     */
    protected function temRecorrencia(Request $request): bool
    {
        // Verifica se o checkbox de repetição está marcado
        $repetirMarcado = $request->has('repetir_lancamento') && $request->input('repetir_lancamento') == 1;
        
        if (!$repetirMarcado) {
            return false;
        }
        
        return $request->has('configuracao_recorrencia') || 
            ($request->has('intervalo_repeticao') && 
             $request->has('frequencia') && 
             $request->has('apos_ocorrencias'));
    }

    /**
     * Processa recorrência
     * Cria registro de recorrência e gera as transações futuras
     * 
     * @param TransacaoFinanceira $transacao A transação principal (primeira ocorrência)
     * @param array $data Dados validados da transação
     * @param Request $request Requisição com dados de recorrência
     * @return void
     */
    protected function processarRecorrencia(
        TransacaoFinanceira $transacao,
        array $data,
        Request $request
    ): void {
        try {
            // Obter dados da recorrência da requisição
            $intervaloRepeticao = (int) $request->input('intervalo_repeticao', 1);
            $frequencia = $request->input('frequencia', 'mensal');
            $aposOcorrencias = (int) $request->input('apos_ocorrencias', 12);
            
            // Mapear frequência para nome legível
            $frequenciaMap = [
                'diario' => 'Dia(s)',
                'semanal' => 'Semana(s)',
                'mensal' => 'Mês(es)',
                'anual' => 'Ano(s)',
            ];
            $frequenciaTexto = $frequenciaMap[$frequencia] ?? $frequencia;
            
            // Gerar nome descritivo da recorrência (ex: "A cada 2 Mês(es) - Após 4 ocorrências")
            $nomeRecorrencia = "A cada {$intervaloRepeticao} {$frequenciaTexto} - Após {$aposOcorrencias} ocorrência(s)";
            
            // Criar registro de recorrência
            $recorrencia = Recorrencia::create([
                'company_id' => $data['company_id'],
                'nome' => $nomeRecorrencia,
                'intervalo_repeticao' => $intervaloRepeticao,
                'frequencia' => $frequencia,
                'total_ocorrencias' => $aposOcorrencias,
                'ocorrencias_geradas' => 0,
                'data_inicio' => Carbon::parse($data['data_competencia']),
                'ativo' => true,
                'created_by' => Auth::id(),
                'created_by_name' => Auth::user()->name,
                'updated_by' => Auth::id(),
                'updated_by_name' => Auth::user()->name,
            ]);
            
            // Associar a transação inicial (primeira ocorrência) ao recorrência
            $transacao->update(['recorrencia_id' => $recorrencia->id]);
            
            // Gerar as demais transações recorrentes usando o RecurrenceService
            $this->recurrenceService->generateRecurringTransactions(
                $recorrencia,
                $transacao,
                $data
            );
            
            Log::info('Recorrência processada com sucesso', [
                'recorrencia_id' => $recorrencia->id,
                'transacao_id' => $transacao->id,
                'frequencia' => $frequencia,
                'intervalo' => $intervaloRepeticao,
                'total_ocorrencias' => $aposOcorrencias,
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao processar recorrência', [
                'transacao_id' => $transacao->id,
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Verifica se há parcelas a serem processadas
     */
    protected function temParcelas(Request $request): bool
    {
        return $request->has('parcelamento') && 
            $request->input('parcelamento') !== 'avista' && 
            $request->input('parcelamento') !== '1x' &&
            $request->has('parcelas') && 
            is_array($request->input('parcelas')) && 
            count($request->input('parcelas')) > 0;
    }

    /**
     * Processa parcelas (SOLUÇÃO HÍBRIDA)
     * 
     * Cria:
     * 1. Transação PAI com valor TOTAL e situação 'parcelado' (não aparece na listagem)
     * 2. Uma TransacaoFinanceira para cada parcela (FILHA) com parent_id apontando para o PAI
     * 3. Um registro em 'parcelamentos' com metadata (numero_parcela, percentual, etc.)
     * 
     * @param ?TransacaoFinanceira $transacaoPrincipal Não será usado (compatibilidade)
     * @return TransacaoFinanceira Retorna a primeira transação filha
     */
    protected function processarParcelas(
        ?TransacaoFinanceira $transacaoPrincipal,
        array $data,
        Request $request
    ): TransacaoFinanceira {
        $parcelas = $request->input('parcelas', []);
        
        if (empty($parcelas) || !is_array($parcelas)) {
            throw new \Exception('Nenhuma parcela fornecida para processar');
        }
        
        ksort($parcelas);
        $totalParcelas = count($parcelas);
        
        // 1. Cria a transação PAI com o valor TOTAL
        // Situação 'parcelado' indica que é apenas um container - não aparece na listagem
        $dadosPai = array_merge($data, [
            'situacao' => 'parcelado', // Situação especial para transação pai
        ]);
        
        $transacaoPai = TransacaoFinanceira::create($dadosPai);
        
        Log::info('📋 Transação PAI (parcelada) criada', [
            'transacao_id' => $transacaoPai->id,
            'valor_total' => $data['valor'],
            'total_parcelas' => $totalParcelas,
            'situacao' => 'parcelado'
        ]);
        
        $primeiraTransacaoFilha = null;
        
        // 2. Cria uma TransacaoFinanceira para cada parcela (FILHA)
        foreach ($parcelas as $index => $parcela) {
            $numeroParcela = (int) $index;
            if ($numeroParcela === 0) {
                $numeroParcela = 1;
            }
            
            // Entidade/Forma de pagamento da parcela
            $entidadeIdParcela = $data['entidade_id'];
            if (isset($parcela['forma_pagamento_id']) && $parcela['forma_pagamento_id']) {
                $entidadeIdParcela = $parcela['forma_pagamento_id'];
            }
            
            // Conta de pagamento (pode ser diferente para cada parcela)
            $contaPagamentoId = null;
            if (isset($parcela['conta_pagamento_id']) && $parcela['conta_pagamento_id']) {
                $contaPagamentoId = $parcela['conta_pagamento_id'];
            }
            
            // Converte data de vencimento da parcela
            $dataVencimentoParcela = $data['data_competencia'];
            if (isset($parcela['vencimento']) && $parcela['vencimento']) {
                $dataVencimentoParcela = $this->converterDataVencimentoParcela(
                    $parcela['vencimento'], 
                    $data['data_competencia'], 
                    $index
                );
            }
            
            // Valor da parcela
            $valorParcela = isset($parcela['valor']) ? $this->converterValorParaDecimal($parcela['valor']) : 0;
            
            // Percentual da parcela
            $percentualParcela = isset($parcela['percentual']) ? (float) $parcela['percentual'] : 0;
            
            // Descrição da parcela
            $descricaoParcela = isset($parcela['descricao']) && $parcela['descricao'] 
                ? $parcela['descricao'] 
                : $data['descricao'] . " {$numeroParcela}/{$totalParcelas}";
            
            // 2.1 Cria a TransacaoFinanceira FILHA (a parcela individual)
            $dadosFilha = [
                'company_id' => $data['company_id'],
                'parent_id' => $transacaoPai->id, // Vincula ao PAI
                'data_competencia' => $data['data_competencia'],
                'data_vencimento' => $dataVencimentoParcela,
                'entidade_id' => $contaPagamentoId ?? $entidadeIdParcela,
                'parceiro_id' => $data['parceiro_id'] ?? null,
                'tipo' => $data['tipo'],
                'valor' => $valorParcela,
                'descricao' => $descricaoParcela,
                'lancamento_padrao_id' => $data['lancamento_padrao_id'] ?? null,
                'cost_center_id' => $data['cost_center_id'] ?? null,
                'tipo_documento' => $data['tipo_documento'] ?? null,
                'numero_documento' => isset($data['numero_documento']) && $data['numero_documento'] 
                    ? $data['numero_documento'] . "-{$numeroParcela}" 
                    : null,
                'origem' => $data['origem'] ?? null,
                'historico_complementar' => $data['historico_complementar'] ?? null,
                'comprovacao_fiscal' => $data['comprovacao_fiscal'] ?? false,
                'situacao' => 'em_aberto', // Cada parcela começa em aberto
                'agendado' => isset($parcela['agendado']) ? (bool) $parcela['agendado'] : false,
                'valor_pago' => 0,
                'juros' => 0,
                'multa' => 0,
                'desconto' => 0,
                'created_by' => $data['created_by'],
                'created_by_name' => $data['created_by_name'],
                'updated_by' => $data['updated_by'],
                'updated_by_name' => $data['updated_by_name'],
            ];
            
            $transacaoFilha = TransacaoFinanceira::create($dadosFilha);
            
            // Guarda a primeira parcela para retornar
            if ($primeiraTransacaoFilha === null) {
                $primeiraTransacaoFilha = $transacaoFilha;
            }
            
            // 2.2 Cria o registro de metadata em 'parcelamentos'
            $transacaoPai->parcelas()->create([
                'transacao_parcela_id' => $transacaoFilha->id, // Vincula à transação filha
                'numero_parcela' => $numeroParcela,
                'total_parcelas' => $totalParcelas,
                'data_vencimento' => $dataVencimentoParcela,
                'valor' => $valorParcela,
                'percentual' => $percentualParcela,
                'entidade_id' => $entidadeIdParcela,
                'conta_pagamento_id' => $contaPagamentoId,
                'descricao' => $descricaoParcela,
                'situacao' => 'em_aberto',
                'agendado' => isset($parcela['agendado']) ? (bool) $parcela['agendado'] : false,
                'valor_pago' => 0,
                'juros' => 0,
                'multa' => 0,
                'desconto' => 0,
                'created_by' => $data['created_by'],
                'created_by_name' => $data['created_by_name'],
                'updated_by' => $data['updated_by'],
                'updated_by_name' => $data['updated_by_name'],
            ]);
            
            Log::info('✅ Parcela criada (TransacaoFinanceira + Parcelamento)', [
                'transacao_pai_id' => $transacaoPai->id,
                'transacao_filha_id' => $transacaoFilha->id,
                'numero_parcela' => $numeroParcela,
                'total_parcelas' => $totalParcelas,
                'valor' => $valorParcela,
                'vencimento' => $dataVencimentoParcela
            ]);
        }
        
        Log::info('✅ Parcelamento HÍBRIDO criado com sucesso', [
            'transacao_pai_id' => $transacaoPai->id,
            'total_parcelas' => $totalParcelas,
            'valor_total' => $transacaoPai->valor,
            'transacoes_filhas' => $transacaoPai->children()->count()
        ]);
        
        // Retorna a primeira transação filha (para compatibilidade com fluxo existente)
        return $primeiraTransacaoFilha ?? $transacaoPai;
    }
    
    /**
     * Converte valor de string formatada para decimal
     */
    protected function converterValorParaDecimal($valor): float
    {
        if (is_numeric($valor)) {
            return (float) $valor;
        }

        if (is_string($valor)) {
            // Remove pontos de milhar e substitui vírgula por ponto
            $valor = str_replace('.', '', $valor);
            $valor = str_replace(',', '.', $valor);
            return (float) $valor;
        }

        return 0;
    }
    
    /**
     * Converte data de vencimento da parcela para formato padrão Y-m-d
     */
    protected function converterDataVencimentoParcela(string $vencimentoStr, string $fallback, int $index): string
    {
        try {
            $vencimentoStr = trim(preg_replace('/\s+/', '', $vencimentoStr));
            
            if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $vencimentoStr, $matches)) {
                $dia = (int)trim($matches[1]);
                $mes = (int)trim($matches[2]);
                $ano = (int)trim($matches[3]);
                
                if ($dia >= 1 && $dia <= 31 && $mes >= 1 && $mes <= 12 && $ano >= 1900 && $ano <= 2100) {
                    return Carbon::create($ano, $mes, $dia, 0, 0, 0)->format('Y-m-d');
                }
            }
            
            return Carbon::createFromFormat('d/m/Y', $vencimentoStr)->format('Y-m-d');
        } catch (\Exception $e) {
            Log::warning('Erro ao converter data de vencimento da parcela', [
                'vencimento' => $vencimentoStr,
                'erro' => $e->getMessage(),
                'parcela_index' => $index
            ]);
            return $fallback;
        }
    }

    /**
     * Obtém dados do gráfico de transações por dia
     */
    public function getDadosGrafico($mes, $ano)
    {
        // Obtém a quantidade de dias no mês selecionado
        $diasNoMes = Carbon::create($ano, $mes, 1)->daysInMonth;

        // Inicializa arrays para armazenar os dados do gráfico
        $dias = [];
        $recebimentos = [];
        $pagamentos = [];
        $transfEntrada = [];
        $transfSaida = [];
        $saldo = [];

        // Busca todas as transações do mês selecionado
        $transacoes = TransacaoFinanceira::whereYear('data_competencia', $ano)
        ->whereMonth('data_competencia', $mes)
        ->orderBy('data_competencia')
        ->get()
        ->map(function ($transacao) {
            // Converte a string para um objeto Carbon
            $transacao->data_competencia = Carbon::parse($transacao->data_competencia);
            return $transacao;
        });


        // Variável para armazenar o saldo acumulado
        $saldoAcumulado = 0;

        // Loop para preencher os dados do gráfico para cada dia do mês
        for ($dia = 1; $dia <= $diasNoMes; $dia++) {
            $dataLoop = Carbon::create($ano, $mes, $dia)->format('Y-m-d');

            // Filtra as transações do dia
            $transacoesDia = $transacoes->filter(fn ($t) => $t->data_competencia->format('Y-m-d') === $dataLoop);

            // Calcula os totais de cada tipo de transação no dia
            $valorRecebimentos = $transacoesDia->where('tipo', 'entrada')->sum('valor');
            $valorPagamentos = $transacoesDia->where('tipo', 'saida')->sum('valor');
            $valorTransfEnt = $transacoesDia->where('tipo', 'transfer_in')->sum('valor');
            $valorTransfSai = $transacoesDia->where('tipo', 'transfer_out')->sum('valor');

            // Atualiza o saldo acumulado
            $saldoAcumulado += ($valorRecebimentos + $valorTransfEnt) - ($valorPagamentos + $valorTransfSai);

            // Adiciona os valores ao array
            $dias[] = $dia;
            $recebimentos[] = (float) $valorRecebimentos;
            $pagamentos[] = (float) $valorPagamentos;
            $transfEntrada[] = (float) $valorTransfEnt;
            $transfSaida[] = (float) $valorTransfSai;
            $saldo[] = (float) $saldoAcumulado;
        }

        return compact('dias', 'recebimentos', 'pagamentos', 'transfEntrada', 'transfSaida', 'saldo');
    }

    /**
     * Obtém os dados de fluxo de caixa anual (entradas e saídas por mês)
     */
    public function getDadosFluxoCaixaAnual($ano)
    {
        $entradas = [];
        $saidas = [];

        // Loop para cada mês do ano
        for ($mes = 1; $mes <= 12; $mes++) {
            // Busca o total de entradas (receitas) do mês
            // Exclui transações com situacao 'parcelado' (são transações pai, evita duplicação)
            $totalEntradas = TransacaoFinanceira::whereYear('data_competencia', $ano)
                ->whereMonth('data_competencia', $mes)
                ->where('tipo', 'entrada')
                ->where('situacao', '!=', 'parcelado')
                ->sum('valor');

            // Busca o total de saídas (despesas) do mês
            // Exclui transações com situacao 'parcelado' (são transações pai, evita duplicação)
            $totalSaidas = TransacaoFinanceira::whereYear('data_competencia', $ano)
                ->whereMonth('data_competencia', $mes)
                ->where('tipo', 'saida')
                ->where('situacao', '!=', 'parcelado')
                ->sum('valor');

            $entradas[] = (float) $totalEntradas;
            $saidas[] = (float) $totalSaidas;
        }

        return [
            'entradas' => $entradas,
            'saidas' => $saidas
        ];
    }
}
