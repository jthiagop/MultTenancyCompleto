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
    protected $rateioService;

    public function __construct(RecurrenceService $recurrenceService, RateioService $rateioService)
    {
        $this->recurrenceService = $recurrenceService;
        $this->rateioService = $rateioService;
    }

    /**
     * Sincroniza a Movimentacao de uma transação financeira de forma centralizada e à prova de erros.
     *
     * Este é o método canônico para garantir que o registro em `movimentacoes` — e portanto o
     * `saldo_atual` em `entidades_financeiras` — esteja sempre consistente com o estado atual
     * da transação. Deve ser chamado em qualquer operação que altere:
     *   - entidade_id (troca de conta)
     *   - tipo        (entrada ↔ saída)
     *   - valor       (correção de montante)
     *   - situacao    (efetivação ou reversão para em_aberto)
     *
     * Cenários cobertos:
     *   A) Transação efetivada (pago/recebido) + movimentação existe
     *      → atualiza entidade_id / tipo / valor → MovimentacaoObserver::updated reverte o
     *        impacto antigo e aplica o novo de forma atômica.
     *   B) Transação efetivada mas sem movimentação (inconsistência herdada ou bug anterior)
     *      → cria nova movimentação → MovimentacaoObserver::created incrementa o saldo.
     *   C) Transação revertida para em_aberto
     *      → deleta movimentação existente → MovimentacaoObserver::deleted decrementa o saldo.
     *   D) Transação em_aberto sem movimentação
     *      → nenhuma ação necessária (estado correto, saldo não foi impactado).
     *
     * IMPORTANTE: deve ser chamado **dentro** de um DB::transaction para garantir atomicidade.
     *
     * @param TransacaoFinanceira $transacao Transação cujos campos já foram atualizados.
     * @param string  $tipoDb     Valor do banco: 'entrada' ou 'saida'.
     * @param float   $valorNovo  Valor a ser usado na movimentação.
     * @param int     $entidadeId ID da entidade financeira destino.
     * @param bool    $efetivado  true = pago/recebido; false = em_aberto/previsto.
     */
    public function sincronizarMovimentacao(
        TransacaoFinanceira $transacao,
        string $tipoDb,
        float  $valorNovo,
        int    $entidadeId,
        bool   $efetivado
    ): void {
        // Busca por ambas as vias: movimentacao_id direto (Path A / conciliação)
        // ou relação polimórfica morphOne (Path B / registrarBaixa).
        $movimentacao = null;
        if ($transacao->movimentacao_id) {
            $movimentacao = Movimentacao::find($transacao->movimentacao_id);
        }
        if (!$movimentacao) {
            $movimentacao = $transacao->movimentacao()->first();
        }

        // ── Cenário C / D: não efetivado ─────────────────────────────────────
        if (!$efetivado) {
            if ($movimentacao) {
                // Cenário C: reverteu para em_aberto — desfaz o impacto no saldo.
                $movimentacao->delete(); // MovimentacaoObserver::deleted → decrementa/incrementa saldo
                DB::table('transacoes_financeiras')->where('id', $transacao->id)->update(['movimentacao_id' => null]);
                $transacao->movimentacao_id = null;

                Log::info('[sincronizarMovimentacao] Movimentação removida — transação revertida para em_aberto.', [
                    'transacao_id'   => $transacao->id,
                    'movimentacao_id' => $movimentacao->id,
                ]);
            }
            // Cenário D: já estava em_aberto e sem movimentação — nada a fazer.
            return;
        }

        // ── Cenário A: movimentação já existe — sincroniza o que mudou ────────
        if ($movimentacao) {
            $movimentacao->entidade_id = $entidadeId;
            $movimentacao->tipo        = $tipoDb;
            $movimentacao->valor       = $valorNovo;
            $movimentacao->save(); // MovimentacaoObserver::updated → ajusta saldos das entidades

            Log::info('[sincronizarMovimentacao] Movimentação sincronizada.', [
                'transacao_id'    => $transacao->id,
                'movimentacao_id' => $movimentacao->id,
                'entidade_id'     => $entidadeId,
                'tipo'            => $tipoDb,
                'valor'           => $valorNovo,
            ]);
            return;
        }

        // ── Cenário B: efetivado mas sem movimentação (inconsistência) ─────────
        Log::warning('[sincronizarMovimentacao] Transação efetivada sem movimentação vinculada — recriando.', [
            'transacao_id' => $transacao->id,
        ]);

        $lancamentoPadrao = $transacao->lancamento_padrao_id
            ? LancamentoPadrao::find($transacao->lancamento_padrao_id)
            : null;

        $nova = Movimentacao::create([
            'entidade_id'       => $entidadeId,
            'tipo'              => $tipoDb,
            'valor'             => $valorNovo,
            'data'              => $transacao->getRawOriginal('data_competencia') ?? now()->toDateString(),
            'descricao'         => $transacao->descricao,
            'company_id'        => $transacao->company_id,
            'lancamento_padrao_id' => $transacao->lancamento_padrao_id,
            'conta_debito_id'   => $lancamentoPadrao?->debit_account_id ?? null,
            'conta_credito_id'  => $lancamentoPadrao?->credit_account_id ?? null,
            'data_competencia'  => $transacao->getRawOriginal('data_competencia'),
            'created_by'        => Auth::id(),
            'created_by_name'   => Auth::user()?->name ?? 'Sistema',
            'updated_by'        => Auth::id(),
            'updated_by_name'   => Auth::user()?->name ?? 'Sistema',
        ]); // MovimentacaoObserver::created → incrementa saldo

        DB::table('transacoes_financeiras')->where('id', $transacao->id)->update(['movimentacao_id' => $nova->id]);
        $transacao->movimentacao_id = $nova->id;

        Log::info('[sincronizarMovimentacao] Movimentação recriada (Cenário B).', [
            'transacao_id'    => $transacao->id,
            'movimentacao_id' => $nova->id,
        ]);
    }

    /**
     * Cria um novo lançamento financeiro com todas as suas dependências
     * 
     * Padrão profissional:
     * - Transação DB envolvendo apenas operações em banco
     * - Anexos processados DEPOIS do commit (DB::afterCommit)
     * - Para parcelamentos: cria UMA transação principal, as parcelas são 
     *   processadas pelo BancoController::criarParcelas() na tabela 'parcelamentos'
     * - Retorna sempre um model válido
     * 
     * @param array $validatedData Dados validados do request
     * @param Request $request Request original para acessar dados não validados
     * @return TransacaoFinanceira Transação principal criada
     * @throws \Exception
     */
    public function criarLancamento(array $validatedData, Request $request): TransacaoFinanceira
    {
        $transacao = DB::transaction(function () use ($validatedData, $request) {
            // 1. Prepara os dados
            $data = $this->prepararDados($validatedData, $request);
            
            // 2. Calcula a situação baseada no checkbox "Pago"
            $data['situacao'] = $this->calcularSituacao($request);
            
            // 3. Cria a transação principal (mesmo se houver parcelas)
            // NOTA: As parcelas são processadas pelo BancoController::criarParcelas()
            // que cria registros na tabela 'parcelamentos' vinculados a esta transação
            $transacao = TransacaoFinanceira::create($data);
            
            // 4. ✅ REGRA DE NEGÓCIO: Só cria movimentação se a situação for EFETIVADA (pago/recebido)
            // Transações em_aberto são apenas previsões e não devem impactar saldo
            // Para transações parceladas, a movimentação será criada quando cada parcela for paga
            $temParcelas = $this->temParcelas($request);
            $situacoesEfetivadas = ['pago', 'recebido'];
            $movimentacao = null;
            
            if (!$temParcelas && in_array($data['situacao'], $situacoesEfetivadas)) {
                $movimentacao = $transacao->movimentacao()->create($this->prepararDadosMovimentacao($data));
            }
            
            // 5. Processa pagamento (se houver e não for parcelado)
            if (!$temParcelas && $this->temPagamento($request, $data) && $movimentacao) {
                $this->processarPagamento($transacao, $movimentacao, $data, $request);
            }
            
            // 6. Processa lançamento padrão especial (Depósito Bancário)
            if (!$temParcelas) {
                $this->processarLancamentoPadrao($transacao, $data);
            }
            
            // 7. Processa recorrência (se houver e não for parcelado)
            if (!$temParcelas && $this->temRecorrencia($request)) {
                $this->processarRecorrencia($transacao, $data, $request);
            }

            // 8. Processa rateio (se houver)
            $rateios = $request->input('rateios', []);
            if (!empty($rateios) && is_array($rateios)) {
                $this->rateioService->processarRateio($transacao, $rateios);
            }
            
            return $transacao;
        });
        
        /** @var \App\Models\Financeiro\TransacaoFinanceira $transacao */
        
        // 8. Processa anexos APÓS commit
        DB::afterCommit(function () use ($request, $transacao) {
            try {
                $this->processarAnexos($request, $transacao);
            } catch (\Exception $e) {
                Log::warning('Erro ao processar anexos após commit', [
                    'transacao_id' => $transacao->id,
                    'erro' => $e->getMessage()
                ]);
            }
        });
        
        return $transacao;
    }

    /**
     * Registra a baixa de uma transação financeira (marca como pago/recebido)
     * 
     * Este método:
     * - Atualiza a situação da transação para pago/recebido
     * - Cria a movimentação correspondente (impacta saldo)
     * - Registra valor pago e data de pagamento
     * 
     * @param TransacaoFinanceira $transacao Transação a ser baixada
     * @param array $dados Dados da baixa (valor_pago, data_pagamento, juros, multa, desconto)
     * @return TransacaoFinanceira Transação atualizada
     * @throws \Exception
     */
    public function registrarBaixa(TransacaoFinanceira $transacao, array $dados): TransacaoFinanceira
    {
        /** @var TransacaoFinanceira $result */
        $result = DB::transaction(function () use ($transacao, $dados) {
            // 1. Define valores padrão
            $valorPago = $dados['valor_pago'] ?? $transacao->valor;
            $dataPagamento = $dados['data_pagamento'] ?? Carbon::today()->format('Y-m-d');
            $juros = $dados['juros'] ?? 0;
            $multa = $dados['multa'] ?? 0;
            $desconto = $dados['desconto'] ?? 0;

            // 2. Atualiza a transação
            $transacao->situacao = ($transacao->tipo === 'entrada') 
                ? \App\Enums\SituacaoTransacao::RECEBIDO
                : \App\Enums\SituacaoTransacao::PAGO;
            $transacao->valor_pago = $valorPago;
            $transacao->data_pagamento = $dataPagamento;
            $transacao->juros = $juros;
            $transacao->multa = $multa;
            $transacao->desconto = $desconto;
            $transacao->updated_by = Auth::id();
            $transacao->updated_by_name = Auth::user()->name ?? 'Sistema';
            $transacao->save();

            // 3. Cria a movimentação (impacta saldo)
            // Só cria se ainda não existir uma movimentação para esta transação
            if (!$transacao->movimentacao) {
                $lancamentoPadrao = LancamentoPadrao::find($transacao->lancamento_padrao_id);
                
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
                    'conta_debito_id' => $lancamentoPadrao->debit_account_id ?? null,
                    'conta_credito_id' => $lancamentoPadrao->credit_account_id ?? null,
                    'data_competencia' => $transacao->data_competencia,
                ];

                $transacao->movimentacao()->create($dadosMovimentacao);
                // Saldo atualizado automaticamente pelo MovimentacaoObserver (increment/decrement O(1))
            }

            // 4. Baixa automática da contraparte intercompany (rateio)
            if ($transacao->rateio_origem_id) {
                $this->baixarContraparteIntercompany($transacao);
            }

            Log::info('[registrarBaixa] Transação baixada com sucesso', [
                'transacao_id' => $transacao->id,
                'valor_pago' => $valorPago,
                'situacao' => $transacao->situacao,
            ]);

            return $transacao;
        });

        return $result;
    }

    /**
     * Reverte uma baixa de transação financeira
     * 
     * Operação inversa do registrarBaixa():
     * - Altera situação para "em_aberto"
     * - Limpa campos de pagamento (valor_pago, data_pagamento, juros, multa, desconto)
     * - Exclui a movimentação associada (reverte impacto no saldo)
     * 
     * @param TransacaoFinanceira $transacao Transação a ser reaberta
     * @return TransacaoFinanceira Transação atualizada
     * @throws \Exception
     */
    public function reverterBaixa(TransacaoFinanceira $transacao): TransacaoFinanceira
    {
        /** @var TransacaoFinanceira $result */
        $result = DB::transaction(function () use ($transacao) {
            // Guarda referência da entidade para recalcular saldo depois
            $entidadeId = $transacao->entidade_id;
            
            // 1. Exclui a movimentação associada (reverte impacto no saldo)
            if ($transacao->movimentacao) {
                $movimentacaoId = $transacao->movimentacao->id;
                $transacao->movimentacao->delete();
                
                Log::info('[reverterBaixa] Movimentação excluída', [
                    'transacao_id' => $transacao->id,
                    'movimentacao_id' => $movimentacaoId,
                ]);
            }

            // 2. Atualiza a transação para "em_aberto"
            $transacao->situacao = \App\Enums\SituacaoTransacao::EM_ABERTO;
            $transacao->valor_pago = null;
            $transacao->data_pagamento = null;
            $transacao->juros = null;
            $transacao->multa = null;
            $transacao->desconto = null;
            $transacao->updated_by = Auth::id();
            $transacao->updated_by_name = Auth::user()->name ?? 'Sistema';
            $transacao->save();
            // Saldo atualizado automaticamente pelo MovimentacaoObserver (increment/decrement O(1))

            Log::info('[reverterBaixa] Transação reaberta com sucesso', [
                'transacao_id' => $transacao->id,
                'situacao' => $transacao->situacao,
            ]);

            return $transacao;
        });

        return $result;
    }

    /**
     * Inverte o tipo de uma transação (entrada ↔ saída) e de todas as filhas
     *
     * - Inverte tipo: entrada → saída, saída → entrada
     * - Atualiza situação (pago ↔ recebido, mantém em_aberto)
     * - Atualiza movimentação associada (se existir)
     * - Recalcula saldo da entidade financeira
     * - Inverte todas as transações filhas (parcelas)
     *
     * @param TransacaoFinanceira $transacao
     * @return TransacaoFinanceira
     * @throws \Exception
     */
    public function inverterTipo(TransacaoFinanceira $transacao): TransacaoFinanceira
    {
        /** @var TransacaoFinanceira $result */
        $result = DB::transaction(function () use ($transacao) {
            // 1. Inverte a transação principal
            $this->inverterTipoUnico($transacao);

            // 2. Inverte todas as filhas (parcelas)
            /** @var \Illuminate\Database\Eloquent\Collection<int, TransacaoFinanceira> $filhas */
            $filhas = TransacaoFinanceira::where('parent_id', $transacao->id)->get();
            foreach ($filhas as $filha) {
                $this->inverterTipoUnico($filha);
            }

            Log::info('[inverterTipo] Tipo invertido com sucesso', [
                'transacao_id' => $transacao->id,
                'novo_tipo' => $transacao->tipo,
                'filhas_invertidas' => $filhas->count(),
            ]);

            return $transacao;
        });

        return $result;
    }

    /**
     * Inverte o tipo de uma única transação (sem propagar para filhas)
     */
    protected function inverterTipoUnico(TransacaoFinanceira $transacao): void
    {
        $novoTipo = $transacao->tipo === 'entrada' ? 'saida' : 'entrada';
        $transacao->tipo = $novoTipo;

        // Atualiza situação se for pago/recebido
        $situacaoValue = $transacao->situacao instanceof \App\Enums\SituacaoTransacao
            ? $transacao->situacao->value
            : $transacao->situacao;

        if ($situacaoValue === 'pago') {
            $transacao->situacao = \App\Enums\SituacaoTransacao::RECEBIDO;
        } elseif ($situacaoValue === 'recebido') {
            $transacao->situacao = \App\Enums\SituacaoTransacao::PAGO;
        }
        // em_aberto, parcelado, etc. permanecem inalterados

        $transacao->updated_by = Auth::id();
        $transacao->updated_by_name = Auth::user()->name ?? 'Sistema';
        $transacao->save();

        // Atualiza movimentação associada (se existir)
        // Busca por relacionamento polimórfico OU por movimentacao_id (legado/conciliação)
        $movimentacao = $transacao->movimentacao;
        
        // Fallback: busca por movimentacao_id se não encontrou pelo morphOne
        if (!$movimentacao && $transacao->movimentacao_id) {
            $movimentacao = \App\Models\Movimentacao::find($transacao->movimentacao_id);
            
            // Corrige o relacionamento polimórfico para futuras operações
            if ($movimentacao) {
                $movimentacao->origem_id = $transacao->id;
                $movimentacao->origem_type = TransacaoFinanceira::class;
            }
        }
        
        if ($movimentacao) {
            $movimentacao->tipo = $novoTipo;
            $movimentacao->save();
            
            Log::info('[inverterTipoUnico] Movimentação atualizada', [
                'movimentacao_id' => $movimentacao->id,
                'novo_tipo' => $novoTipo,
            ]);
        } else {
            Log::warning('[inverterTipoUnico] Movimentação não encontrada para transação', [
                'transacao_id' => $transacao->id,
                'movimentacao_id_attr' => $transacao->movimentacao_id ?? 'null',
            ]);
        }
        // Saldo atualizado automaticamente pelo MovimentacaoObserver (increment/decrement O(1))
    }

    /**
     * Ao dar baixa numa transação de rateio intercompany, localiza
     * e baixa automaticamente a contraparte (Receber ↔ Pagar).
     */
    protected function baixarContraparteIntercompany(TransacaoFinanceira $transacao): void
    {
        try {
            $tipoContraparte = $transacao->tipo === 'entrada' ? 'saida' : 'entrada';

            $contraparte = TransacaoFinanceira::where('rateio_origem_id', $transacao->rateio_origem_id)
                ->where('tipo', $tipoContraparte)
                ->where('id', '!=', $transacao->id)
                ->whereIn('situacao', ['em_aberto'])
                ->where('valor', $transacao->valor)
                ->first();

            if (!$contraparte) {
                Log::info('[baixarContraparteIntercompany] Contraparte não encontrada ou já baixada', [
                    'transacao_id' => $transacao->id,
                    'rateio_origem_id' => $transacao->rateio_origem_id,
                ]);
                return;
            }

            $contraparte->situacao = ($contraparte->tipo === 'entrada')
                ? \App\Enums\SituacaoTransacao::RECEBIDO
                : \App\Enums\SituacaoTransacao::PAGO;
            $contraparte->valor_pago = $transacao->valor_pago ?? $contraparte->valor;
            $contraparte->data_pagamento = $transacao->data_pagamento ?? now()->format('Y-m-d');
            $contraparte->updated_by = Auth::id();
            $contraparte->updated_by_name = Auth::user()->name ?? 'Sistema';
            $contraparte->save();

            if (!$contraparte->movimentacao) {
                $lancamentoPadrao = LancamentoPadrao::find($contraparte->lancamento_padrao_id);

                $contraparte->movimentacao()->create([
                    'entidade_id' => $contraparte->entidade_id,
                    'tipo' => $contraparte->tipo,
                    'valor' => $contraparte->valor_pago,
                    'data' => $contraparte->data_pagamento,
                    'descricao' => $contraparte->descricao,
                    'company_id' => $contraparte->company_id,
                    'created_by' => Auth::id(),
                    'created_by_name' => Auth::user()->name ?? 'Sistema',
                    'updated_by' => Auth::id(),
                    'updated_by_name' => Auth::user()->name ?? 'Sistema',
                    'lancamento_padrao_id' => $contraparte->lancamento_padrao_id,
                    'conta_debito_id' => $lancamentoPadrao->debit_account_id ?? null,
                    'conta_credito_id' => $lancamentoPadrao->credit_account_id ?? null,
                    'data_competencia' => $contraparte->data_competencia,
                ]);
            }

            Log::info('[baixarContraparteIntercompany] Contraparte baixada automaticamente', [
                'transacao_id' => $transacao->id,
                'contraparte_id' => $contraparte->id,
            ]);
        } catch (\Throwable $e) {
            Log::warning('[baixarContraparteIntercompany] Erro ao baixar contraparte: ' . $e->getMessage(), [
                'transacao_id' => $transacao->id,
                'rateio_origem_id' => $transacao->rateio_origem_id,
            ]);
        }
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
        // Compara em centavos (inteiros) para evitar erros de ponto flutuante
        $comparacaoCents = (int) round($valorParaComparacao * 100);
        $valorCents = (int) round((float) $data['valor'] * 100);

        if ($comparacaoCents >= $valorCents) {
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
            // Saldo atualizado automaticamente pelo MovimentacaoObserver (increment/decrement O(1))
        }
        // Pagamento parcial será tratado por método específico do controller
    }
    
    /**
     * Atualiza o saldo da entidade financeira quando recebido/pago está marcado
     * 
     * @param TransacaoFinanceira $transacao
     * @param array $data
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
            $entidade->recalcularSaldo();
            
            Log::info('✅ Saldo recalculado após marcar como recebido/pago', [
                'entidade_id' => $entidade->id,
                'transacao_id' => $transacao->id,
                'tipo_transacao' => $transacao->tipo,
                'situacao' => $transacao->situacao->value,
                'saldo_antes' => $saldoAntes,
                'saldo_depois' => $entidade->saldo_atual,
            ]);
        } catch (\Exception $e) {
            Log::error('❌ Erro ao atualizar saldo da entidade', [
                'entidade_id' => $transacao->entidade_id,
                'transacao_id' => $transacao->id,
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
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
     * Processa os anexos enviados (acesso público para uso no controller de update)
     */
    public function processarAnexosPublic(Request $request, TransacaoFinanceira $transacao): void
    {
        $this->processarAnexos($request, $transacao);
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
            $intervaloRepeticao = (int) $request->input('intervalo_repeticao', 1);
            $frequencia = $request->input('frequencia', 'mensal');
            $aposOcorrencias = (int) $request->input('apos_ocorrencias', 12);

            // Configuração existente (template): copia parâmetros; cada lançamento ganha um novo registro em recorrencias
            $configId = $request->input('configuracao_recorrencia');
            if ($configId !== null && $configId !== '' && is_numeric($configId)) {
                $tpl = Recorrencia::forActiveCompany()->find((int) $configId);
                if ($tpl) {
                    $intervaloRepeticao = (int) $tpl->intervalo_repeticao;
                    $frequencia = $tpl->frequencia;
                    $aposOcorrencias = (int) $tpl->total_ocorrencias;
                }
            }
            
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
     * Processa parcelas
     * Cria múltiplas transações (uma por parcela) ao invés de deletar a transação principal
     * 
     * @param ?TransacaoFinanceira $transacaoPrincipal Não será usado (compatibilidade)
     * @return TransacaoFinanceira Retorna a primeira parcela como referência
     */
    protected function processarParcelas(
        ?TransacaoFinanceira $transacaoPrincipal,
        array $data,
        Request $request
    ): TransacaoFinanceira {
        $parcelas = $request->input('parcelas', []);
        $primeiraTransacao = null;
        
        if (empty($parcelas) || !is_array($parcelas)) {
            throw new \Exception('Nenhuma parcela fornecida para processar');
        }
        
        ksort($parcelas);
        
        foreach ($parcelas as $index => $parcela) {
            $entidadeIdParcela = $data['entidade_id'];
            if (isset($parcela['conta_pagamento_id']) && $parcela['conta_pagamento_id']) {
                $entidadeIdParcela = $parcela['conta_pagamento_id'];
            }
            
            // Converte data de vencimento da parcela
            $dataVencimentoParcela = $data['data_competencia'];
            if (isset($parcela['vencimento']) && $parcela['vencimento']) {
                $dataVencimentoParcela = $this->converterDataVencimentoParcela($parcela['vencimento'], $data['data_competencia'], $index);
            }
            
            $dadosParcela = [
                'company_id' => $data['company_id'],
                'data_competencia' => $data['data_competencia'],
                'data_vencimento' => $dataVencimentoParcela,
                'entidade_id' => $entidadeIdParcela,
                'tipo' => $data['tipo'],
                'valor' => isset($parcela['valor']) ? (float) $parcela['valor'] : 0,
                'descricao' => isset($parcela['descricao']) ? $parcela['descricao'] : $data['descricao'] . ' - Parcela ' . ($index + 1),
                'lancamento_padrao_id' => $data['lancamento_padrao_id'] ?? null,
                'cost_center_id' => $data['cost_center_id'] ?? null,
                'tipo_documento' => $data['tipo_documento'] ?? null,
                'numero_documento' => ($data['numero_documento'] ?? '') . '-' . ($index + 1),
                'origem' => $data['origem'] ?? null,
                'historico_complementar' => $data['historico_complementar'] ?? null,
                'comprovacao_fiscal' => $data['comprovacao_fiscal'] ?? false,
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
            ];
            
            // Cria transação para a parcela
            $transacaoParcela = TransacaoFinanceira::create($dadosParcela);
            
            // NÃO cria movimentação — parcelas nascem como em_aberto e não devem impactar saldo.
            // A movimentação será criada quando o usuário marcar a parcela como pago/recebido
            // via registrarBaixa(), que chamará movimentacao()->create() e o Observer cuidará do saldo.
            
            // Guarda referência da primeira parcela
            if ($index === 0) {
                $primeiraTransacao = $transacaoParcela;
            }
        }
        
        // Retorna primeira parcela como referência
        return $primeiraTransacao ?? throw new \Exception('Erro ao processar parcelas');
    }
    
    /**
     * Converte data de vencimento da parcela para formato padrão Y-m-d
     */
    protected function converterDataVencimentoParcela(string $vencimentoStr, string $fallback, int $index): string
    {
        try {
            $vencimentoStr = trim(preg_replace('/\s+/', '', $vencimentoStr));

            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $vencimentoStr)) {
                return $vencimentoStr;
            }

            if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $vencimentoStr, $matches)) {
                $dia = (int) trim($matches[1]);
                $mes = (int) trim($matches[2]);
                $ano = (int) trim($matches[3]);

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
     * Indica se o request traz parcelamento 2x+ com linhas de parcelas (fluxo Blade/React).
     */
    public function deveCriarParcelamentos(Request $request): bool
    {
        return $request->has('parcelamento')
            && $request->input('parcelamento') !== 'avista'
            && $request->input('parcelamento') !== '1x'
            && $request->has('parcelas')
            && is_array($request->input('parcelas'))
            && count($request->input('parcelas')) > 0;
    }

    /**
     * Exclui todos os parcelamentos e transações filhas existentes da transação principal
     * e, se o request trouxer novos dados de parcelas, recria tudo do zero.
     *
     * Usado ao editar "todas as parcelas" quando o usuário pode ter mudado
     * a quantidade de parcelas, valores ou datas.
     */
    /**
     * Atualiza recorrência de forma inteligente:
     *  - Reduziu quantidade → exclui excedentes da última para a primeira
     *  - Aumentou quantidade → cria os lançamentos adicionais
     *  - Mudou intervalo/frequência → exclui em aberto e recria tudo
     * Lançamentos pagos/recebidos são sempre preservados.
     */
    public function atualizarRecorrencia(
        TransacaoFinanceira $transacaoOriginal,
        Recorrencia $recorrencia,
        array $validatedData,
        Request $request
    ): void {
        DB::transaction(function () use ($transacaoOriginal, $recorrencia, $validatedData, $request) {
            // Resolve valores do template se configuracao_recorrencia foi enviado
            $novoIntervalo     = (int) $request->input('intervalo_repeticao', $recorrencia->intervalo_repeticao);
            $novaFrequencia    = $request->input('frequencia', $recorrencia->frequencia);
            $novasOcorrencias  = (int) $request->input('apos_ocorrencias', $recorrencia->total_ocorrencias);

            $configId = $request->input('configuracao_recorrencia');
            if ($configId !== null && $configId !== '' && is_numeric($configId)) {
                $tpl = Recorrencia::forActiveCompany()->find((int) $configId);
                if ($tpl) {
                    $novoIntervalo    = (int) $tpl->intervalo_repeticao;
                    $novaFrequencia   = $tpl->frequencia;
                    $novasOcorrencias = (int) $tpl->total_ocorrencias;
                }
            }

            $configMudou = $novoIntervalo !== (int) $recorrencia->intervalo_repeticao
                        || $novaFrequencia !== $recorrencia->frequencia;

            if ($configMudou) {
                $this->recorrenciaRecriarTudo($transacaoOriginal, $recorrencia, $validatedData, $novoIntervalo, $novaFrequencia, $novasOcorrencias);
            } elseif ($novasOcorrencias < (int) $recorrencia->total_ocorrencias) {
                $this->recorrenciaReduzir($recorrencia, $novasOcorrencias);
            } elseif ($novasOcorrencias > (int) $recorrencia->total_ocorrencias) {
                $this->recorrenciaExpandir($transacaoOriginal, $recorrencia, $validatedData, $novasOcorrencias);
            }

            $this->atualizarMetaRecorrencia($recorrencia, $novoIntervalo, $novaFrequencia, $novasOcorrencias);
        });
    }

    /**
     * Reduz: exclui excedentes da última ocorrência para a primeira.
     * Pagos/recebidos são desvinculados e preservados.
     */
    private function recorrenciaReduzir(Recorrencia $recorrencia, int $novasOcorrencias): void
    {
        $situacoesProtegidas = [
            \App\Enums\SituacaoTransacao::PAGO->value,
            \App\Enums\SituacaoTransacao::RECEBIDO->value,
        ];

        // Busca transações ordenadas por numero_ocorrencia DESC (última → primeira)
        $transacoes = $recorrencia->transacoesGeradas()
            ->orderByPivot('numero_ocorrencia', 'desc')
            ->get();

        $excluidas = 0;
        $preservadas = 0;
        $totalAtual = $transacoes->count();
        $quantidadeExcluir = $totalAtual - $novasOcorrencias;

        if ($quantidadeExcluir <= 0) return;

        foreach ($transacoes as $t) {
            if ($excluidas + $preservadas >= $quantidadeExcluir) break;

            $situacaoValue = $t->situacao instanceof \BackedEnum ? $t->situacao->value : (string) $t->situacao;

            $recorrencia->transacoesGeradas()->detach($t->id);

            if (in_array($situacaoValue, $situacoesProtegidas)) {
                $t->update(['recorrencia_id' => null]);
                $preservadas++;
                continue;
            }

            if ($t->movimentacao_id) {
                Movimentacao::where('id', $t->movimentacao_id)->delete();
            }
            $t->delete();
            $excluidas++;
        }

        $recorrencia->decrement('ocorrencias_geradas', $excluidas);

        Log::info('📉 Recorrência reduzida', [
            'recorrencia_id' => $recorrencia->id,
            'excluidas' => $excluidas,
            'preservadas' => $preservadas,
            'novas_ocorrencias' => $novasOcorrencias,
        ]);
    }

    /**
     * Expande: gera lançamentos adicionais a partir da última ocorrência existente.
     */
    private function recorrenciaExpandir(
        TransacaoFinanceira $transacaoOriginal,
        Recorrencia $recorrencia,
        array $validatedData,
        int $novasOcorrencias
    ): void {
        $ultimaTransacao = $recorrencia->transacoesGeradas()
            ->orderByPivot('numero_ocorrencia', 'desc')
            ->first();

        $ocorrenciasAtuais = (int) $recorrencia->ocorrencias_geradas;
        $adicionar = $novasOcorrencias - $ocorrenciasAtuais;

        if ($adicionar <= 0 || !$ultimaTransacao) return;

        $ultimaData = Carbon::parse($ultimaTransacao->data_competencia);

        // Gera datas adicionais continuando de onde parou
        $datesAdicionais = [];
        $currentDate = $ultimaData->copy();
        for ($i = 0; $i < $adicionar; $i++) {
            switch ($recorrencia->frequencia) {
                case 'diario':  $currentDate->addDays($recorrencia->intervalo_repeticao); break;
                case 'semanal': $currentDate->addWeeks($recorrencia->intervalo_repeticao); break;
                case 'mensal':  $currentDate->addMonthsNoOverflow($recorrencia->intervalo_repeticao); break;
                case 'anual':   $currentDate->addYearsNoOverflow($recorrencia->intervalo_repeticao); break;
            }
            $datesAdicionais[] = $currentDate->copy();
        }

        $dueDateDiff = null;
        if (isset($validatedData['data_vencimento'])) {
            $dueDateDiff = Carbon::parse($validatedData['data_competencia'])
                ->diffInDays(Carbon::parse($validatedData['data_vencimento']), false);
        }

        foreach ($datesAdicionais as $idx => $occurrenceDate) {
            $occurrenceNumber = $ocorrenciasAtuais + $idx + 1;

            $novaTransacaoData = $validatedData;
            $novaTransacaoData['data_competencia'] = $occurrenceDate->format('Y-m-d');
            $novaTransacaoData['data_vencimento'] = $dueDateDiff !== null
                ? $occurrenceDate->copy()->addDays($dueDateDiff)->format('Y-m-d')
                : $occurrenceDate->format('Y-m-d');
            $novaTransacaoData['situacao'] = 'em_aberto';
            $novaTransacaoData['agendado'] = true;
            $novaTransacaoData['descricao'] = ($validatedData['descricao'] ?? '') . " ({$occurrenceNumber}/{$novasOcorrencias})";

            $novaMovimentacao = Movimentacao::create([
                'entidade_id' => $novaTransacaoData['entidade_id'],
                'tipo'        => $novaTransacaoData['tipo'],
                'valor'       => $novaTransacaoData['valor'],
                'data'        => $novaTransacaoData['data_competencia'],
                'descricao'   => $novaTransacaoData['descricao'],
                'company_id'  => $novaTransacaoData['company_id'],
                'created_by'  => Auth::id(),
                'created_by_name' => Auth::user()?->name ?? 'Sistema',
                'updated_by'  => Auth::id(),
                'updated_by_name' => Auth::user()?->name ?? 'Sistema',
            ]);

            $novaTransacaoData['movimentacao_id'] = $novaMovimentacao->id;
            $novaTransacaoData['recorrencia_id'] = $recorrencia->id;
            $novaTransacao = TransacaoFinanceira::create($novaTransacaoData);

            $recorrencia->transacoesGeradas()->attach($novaTransacao->id, [
                'data_geracao'      => $occurrenceDate->format('Y-m-d'),
                'numero_ocorrencia' => $occurrenceNumber,
                'movimentacao_id'   => $novaMovimentacao->id,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);

            $recorrencia->increment('ocorrencias_geradas');
        }

        Log::info('📈 Recorrência expandida', [
            'recorrencia_id' => $recorrencia->id,
            'adicionadas' => $adicionar,
            'novas_ocorrencias' => $novasOcorrencias,
        ]);
    }

    /**
     * Recria tudo: mudou intervalo ou frequência, então as datas mudam.
     * Exclui em aberto, preserva pagos/recebidos, e gera novamente.
     */
    private function recorrenciaRecriarTudo(
        TransacaoFinanceira $transacaoOriginal,
        Recorrencia $recorrencia,
        array $validatedData,
        int $novoIntervalo,
        string $novaFrequencia,
        int $novasOcorrencias
    ): void {
        $situacoesProtegidas = [
            \App\Enums\SituacaoTransacao::PAGO->value,
            \App\Enums\SituacaoTransacao::RECEBIDO->value,
        ];

        /** @var \Illuminate\Database\Eloquent\Collection<int, TransacaoFinanceira> $transacoes */
        $transacoes = TransacaoFinanceira::where('recorrencia_id', $recorrencia->id)->get();
        $excluidas = 0;
        $preservadas = 0;

        foreach ($transacoes as $t) {
            $situacaoValue = $t->situacao instanceof \BackedEnum ? $t->situacao->value : (string) $t->situacao;
            $recorrencia->transacoesGeradas()->detach($t->id);

            if (in_array($situacaoValue, $situacoesProtegidas)) {
                $t->update(['recorrencia_id' => null]);
                $preservadas++;
                continue;
            }

            if ($t->movimentacao_id) {
                Movimentacao::where('id', $t->movimentacao_id)->delete();
            }
            $t->delete();
            $excluidas++;
        }

        $recorrencia->update(['ocorrencias_geradas' => 0]);

        $this->recurrenceService->generateRecurringTransactions(
            $recorrencia,
            $transacaoOriginal,
            $validatedData
        );

        Log::info('🔄 Recorrência recriada (config alterada)', [
            'recorrencia_id' => $recorrencia->id,
            'excluidas' => $excluidas,
            'preservadas' => $preservadas,
        ]);
    }

    /** Atualiza metadados da recorrência. */
    private function atualizarMetaRecorrencia(
        Recorrencia $recorrencia,
        int $intervalo,
        string $frequencia,
        int $totalOcorrencias
    ): void {
        $frequenciaMap = [
            'diario' => 'Dia(s)', 'semanal' => 'Semana(s)',
            'mensal' => 'Mês(es)', 'anual' => 'Ano(s)',
        ];
        $texto = $frequenciaMap[$frequencia] ?? $frequencia;

        $recorrencia->update([
            'intervalo_repeticao' => $intervalo,
            'frequencia'          => $frequencia,
            'total_ocorrencias'   => $totalOcorrencias,
            'nome'                => "A cada {$intervalo} {$texto} - Após {$totalOcorrencias} ocorrência(s)",
            'updated_by'          => Auth::id(),
            'updated_by_name'     => Auth::user()?->name ?? 'Sistema',
        ]);
    }

    public function excluirERecriarParcelamentos(
        TransacaoFinanceira $transacaoPrincipal,
        array $validatedData,
        Request $request
    ): void {
        // Soft-delete dos registros na tabela parcelamentos
        $transacaoPrincipal->parcelas()->delete();

        // Soft-delete das transações filhas (parcelas como TransacaoFinanceira)
        TransacaoFinanceira::where('parent_id', $transacaoPrincipal->id)->delete();

        // Reverte situação do pai para em_aberto (será reclassificado logo abaixo se houver novas parcelas)
        $transacaoPrincipal->update(['situacao' => \App\Enums\SituacaoTransacao::EM_ABERTO]);

        // Recria parcelas se o request as incluir
        if ($this->deveCriarParcelamentos($request)) {
            $this->criarParcelamentosParaLancamentoPrincipal($transacaoPrincipal, $validatedData, $request);
        }

        Log::info('🔄 Parcelamentos excluídos e recriados', [
            'transacao_id'  => $transacaoPrincipal->id,
            'novas_parcelas' => $this->deveCriarParcelamentos($request) ? count((array) $request->input('parcelas', [])) : 0,
        ]);
    }

    /**
     * Cria transações filhas e registros em parcelamentos (tabela parcelamentos), mantendo o pai com valor total.
     * Usa ordem sequencial (1..N) independentemente das chaves do array (0-based JSON ou 1-based form).
     */
    public function criarParcelamentosParaLancamentoPrincipal(
        TransacaoFinanceira $transacaoPrincipal,
        array $validatedData,
        Request $request
    ): void {
        $parcelas = $request->input('parcelas', []);

        if (empty($parcelas) || !is_array($parcelas)) {
            return;
        }

        $parcelasLista = array_values($parcelas);
        $totalParcelas = count($parcelasLista);

        foreach ($parcelasLista as $position => $parcela) {
            $numeroParcela = $position + 1;

            $entidadeIdParcela = $validatedData['entidade_id'];
            if (isset($parcela['forma_pagamento_id']) && $parcela['forma_pagamento_id']) {
                $entidadeIdParcela = $parcela['forma_pagamento_id'];
            }

            $contaPagamentoId = null;
            if (isset($parcela['conta_pagamento_id']) && $parcela['conta_pagamento_id']) {
                $contaPagamentoId = $parcela['conta_pagamento_id'];
            }

            $dataVencimentoParcela = $validatedData['data_competencia'];
            if (isset($parcela['vencimento']) && $parcela['vencimento']) {
                $dataVencimentoParcela = $this->converterDataVencimentoParcela(
                    (string) $parcela['vencimento'],
                    $validatedData['data_competencia'],
                    $numeroParcela
                );
            }

            $valorParcela = isset($parcela['valor']) ? $this->converterValorInformadoParcela($parcela['valor']) : 0.0;

            $valorTotal = (float) $transacaoPrincipal->valor;
            $percentualParcela = isset($parcela['percentual']) && (float) $parcela['percentual'] > 0
                ? (float) $parcela['percentual']
                : ($valorTotal > 0 ? round(($valorParcela / $valorTotal) * 100, 2) : 0.0);

            $descricaoParcela = isset($parcela['descricao']) && $parcela['descricao']
                ? $parcela['descricao']
                : ($validatedData['descricao'] . " {$numeroParcela}/{$totalParcelas}");

            $transacaoParcela = TransacaoFinanceira::create([
                'company_id' => $validatedData['company_id'],
                'parent_id' => $transacaoPrincipal->id,
                'data_competencia' => $validatedData['data_competencia'],
                'data_vencimento' => $dataVencimentoParcela,
                'entidade_id' => $entidadeIdParcela,
                'parceiro_id' => $validatedData['parceiro_id'] ?? $validatedData['fornecedor_id'] ?? null,
                'tipo' => $validatedData['tipo'],
                'valor' => $valorParcela,
                'descricao' => $descricaoParcela,
                'lancamento_padrao_id' => $validatedData['lancamento_padrao_id'] ?? null,
                'cost_center_id' => $validatedData['cost_center_id'] ?? null,
                'tipo_documento' => $validatedData['tipo_documento'] ?? null,
                'numero_documento' => isset($validatedData['numero_documento'])
                    ? $validatedData['numero_documento'] . '-' . $numeroParcela
                    : null,
                'origem' => $validatedData['origem'] ?? 'Banco',
                'historico_complementar' => $validatedData['historico_complementar'] ?? null,
                'comprovacao_fiscal' => $validatedData['comprovacao_fiscal'] ?? false,
                'situacao' => 'em_aberto',
                'agendado' => isset($parcela['agendado']) ? (bool) $parcela['agendado'] : false,
                'valor_pago' => 0,
                'juros' => 0,
                'multa' => 0,
                'desconto' => 0,
                'created_by' => Auth::id(),
                'created_by_name' => Auth::user()->name ?? 'Sistema',
                'updated_by' => Auth::id(),
                'updated_by_name' => Auth::user()->name ?? 'Sistema',
            ]);

            $transacaoPrincipal->parcelas()->create([
                'transacao_parcela_id' => $transacaoParcela->id,
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
                'created_by' => Auth::id(),
                'created_by_name' => Auth::user()->name ?? 'Sistema',
                'updated_by' => Auth::id(),
                'updated_by_name' => Auth::user()->name ?? 'Sistema',
            ]);

            Log::info('✅ Parcela criada', [
                'transacao_principal_id' => $transacaoPrincipal->id,
                'transacao_parcela_id' => $transacaoParcela->id,
                'numero_parcela' => $numeroParcela,
                'total_parcelas' => $totalParcelas,
                'valor' => $valorParcela,
                'vencimento' => $dataVencimentoParcela,
                'descricao' => $descricaoParcela,
            ]);
        }

        $transacaoPrincipal->update([
            'situacao' => \App\Enums\SituacaoTransacao::PARCELADO,
        ]);

        Log::info('✅ Parcelamento criado com sucesso', [
            'transacao_id' => $transacaoPrincipal->id,
            'total_parcelas' => $totalParcelas,
            'valor_total' => $transacaoPrincipal->valor,
        ]);
    }

    protected function converterValorInformadoParcela(mixed $valor): float
    {
        if (is_numeric($valor)) {
            return (float) $valor;
        }
        if (is_string($valor)) {
            $v = str_replace('.', '', $valor);
            $v = str_replace(',', '.', $v);

            return (float) $v;
        }

        return 0.0;
    }

    /**
     * Obtém dados do gráfico de transações por dia
     */
    public function getDadosGrafico($mes, $ano, $companyId = null)
    {
        // Obtém a quantidade de dias no mês selecionado
        $diasNoMes = Carbon::create($ano, $mes, 1)->daysInMonth;

        // Se não informado, tenta obter da sessão
        if (!$companyId) {
            $companyId = session('active_company_id');
        }

        // Inicializa arrays para armazenar os dados do gráfico
        $dias = [];
        $recebimentos = [];
        $pagamentos = [];
        $transfEntrada = [];
        $transfSaida = [];
        $saldo = [];

        // Busca todas as transações do mês selecionado (filtradas por empresa)
        $transacoes = TransacaoFinanceira::when($companyId, fn($q) => $q->where('company_id', $companyId))
        ->whereYear('data_competencia', $ano)
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
    public function getDadosFluxoCaixaAnual($ano, $companyId = null)
    {
        $entradas = [];
        $saidas = [];

        // Se não informado, tenta obter da sessão
        if (!$companyId) {
            $companyId = session('active_company_id');
        }

        // Loop para cada mês do ano
        for ($mes = 1; $mes <= 12; $mes++) {
            // Busca o total de entradas (receitas) do mês (filtradas por empresa)
            $totalEntradas = TransacaoFinanceira::when($companyId, fn($q) => $q->where('company_id', $companyId))
                ->whereYear('data_competencia', $ano)
                ->whereMonth('data_competencia', $mes)
                ->where('tipo', 'entrada')
                ->sum('valor');

            // Busca o total de saídas (despesas) do mês (filtradas por empresa)
            $totalSaidas = TransacaoFinanceira::when($companyId, fn($q) => $q->where('company_id', $companyId))
                ->whereYear('data_competencia', $ano)
                ->whereMonth('data_competencia', $mes)
                ->where('tipo', 'saida')
                ->sum('valor');

            $entradas[] = (float) $totalEntradas;
            $saidas[] = (float) $totalSaidas;
        }

        return [
            'entradas' => $entradas,
            'saidas' => $saidas
        ];
    }

    /**
     * Registra pagamento (total ou parcial) de uma transação.
     * Se parcial, cria fracionamentos na tabela transacao_fracionamentos.
     * A Movimentacao (que impacta saldo da entidade financeira) só é criada
     * quando o valor total é quitado — pagamentos parciais apenas registram
     * fracionamentos e atualizam valor_pago na transação.
     */
    public function registrarPagamento(TransacaoFinanceira $transacao, array $dados): TransacaoFinanceira
    {
        /** @var TransacaoFinanceira $result */
        $result = DB::transaction(function () use ($transacao, $dados) {
            $valorOriginal = (float) $transacao->valor;
            $valorPago     = (float) $dados['valor_pago'];
            $juros         = (float) ($dados['juros'] ?? 0);
            $multa         = (float) ($dados['multa'] ?? 0);
            $desconto      = (float) ($dados['desconto'] ?? 0);
            $dataPagamento = $dados['data_pagamento'] ?? Carbon::today()->format('Y-m-d');
            $formaPagamento = $dados['forma_pagamento'] ?? '';
            $contaPagamento = $dados['conta_pagamento'] ?? '';

            // Valor restante real (considerando fracionamentos anteriores)
            $transacao->load('fracionamentos');
            $valorRestante = $valorOriginal;
            if ($transacao->fracionamentos && $transacao->fracionamentos->isNotEmpty()) {
                $emAberto = $transacao->fracionamentos->firstWhere('tipo', 'em_aberto');
                if ($emAberto) {
                    $valorRestante = (float) $emAberto->valor;
                }
            } else {
                $valorRestante = max(0, $valorOriginal - (float) ($transacao->valor_pago ?? 0));
            }

            $valorParaComparacao = $valorPago + $juros + $multa;
            $valorAbertoApos = max(0, $valorRestante - $valorParaComparacao);
            $valorTotalPago  = $valorParaComparacao - $desconto;
            $quitaTotal = $valorAbertoApos < 0.01;

            // Remove fracionamento em_aberto anterior
            $transacao->fracionamentos()
                ->where('tipo', 'em_aberto')
                ->delete();

            // Registra o fracionamento deste pagamento
            \App\Models\Financeiro\TransacaoFracionamento::create([
                'transacao_principal_id' => $transacao->id,
                'tipo'                   => 'pago',
                'valor'                  => $valorPago,
                'data_pagamento'         => $dataPagamento,
                'juros'                  => $juros,
                'multa'                  => $multa,
                'desconto'               => $desconto,
                'valor_total'            => $valorTotalPago,
                'forma_pagamento'        => $formaPagamento,
                'conta_pagamento'        => $contaPagamento,
            ]);

            if ($quitaTotal) {
                // Tudo pago — registra baixa completa (cria Movimentacao + atualiza saldo)
                $totalJaPago = $transacao->fracionamentos()->where('tipo', 'pago')->sum('valor');

                return $this->registrarBaixa($transacao, [
                    'valor_pago'     => $totalJaPago,
                    'data_pagamento' => $dataPagamento,
                    'juros'          => (float) $transacao->fracionamentos()->where('tipo', 'pago')->sum('juros'),
                    'multa'          => (float) $transacao->fracionamentos()->where('tipo', 'pago')->sum('multa'),
                    'desconto'       => (float) $transacao->fracionamentos()->where('tipo', 'pago')->sum('desconto'),
                ]);
            }

            // Ainda há saldo em aberto — registra fracionamento pendente
            \App\Models\Financeiro\TransacaoFracionamento::create([
                'transacao_principal_id' => $transacao->id,
                'tipo'                   => 'em_aberto',
                'valor'                  => $valorAbertoApos,
                'data_pagamento'         => null,
                'juros'                  => 0,
                'multa'                  => 0,
                'desconto'               => 0,
                'valor_total'            => $valorAbertoApos,
                'forma_pagamento'        => null,
                'conta_pagamento'        => null,
            ]);

            // Atualiza transação — situação parcial, sem criar Movimentacao
            $transacao->situacao = \App\Enums\SituacaoTransacao::PAGO_PARCIAL;
            $transacao->valor_pago = $transacao->fracionamentos()->where('tipo', 'pago')->sum('valor');
            $transacao->data_pagamento = $dataPagamento;
            $transacao->updated_by = Auth::id();
            $transacao->updated_by_name = Auth::user()?->name ?? 'Sistema';
            $transacao->save();

            return $transacao->fresh();
        });

        return $result;
    }
}
