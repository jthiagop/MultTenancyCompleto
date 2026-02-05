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
                
                // 4. ✅ REGRA DE NEGÓCIO: Só cria movimentação se a situação for EFETIVADA (pago/recebido)
                // Transações em_aberto são apenas previsões e não devem impactar saldo
                $situacoesEfetivadas = ['pago', 'recebido'];
                $movimentacao = null;
                
                if (in_array($data['situacao'], $situacoesEfetivadas)) {
                    $movimentacao = $transacao->movimentacao()->create($this->prepararDadosMovimentacao($data));
                }
                
                // 5. Processa pagamento (se houver)
                if ($this->temPagamento($request, $data) && $movimentacao) {
                    $this->processarPagamento($transacao, $movimentacao, $data, $request);
                }
                
                // 6. Processa lançamento padrão especial (Depósito Bancário)
                $this->processarLancamentoPadrao($transacao, $data);
                
                // 7. Processa recorrência (se houver)
                if ($this->temRecorrencia($request)) {
                    $this->processarRecorrencia($transacao, $data, $request);
                }
            } else {
                // Transação com parcelas (cria múltiplas transações)
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
            
            // ✅ IMPORTANTE: O valor da transação está em DECIMAL (ex: 1991.44)
            // Não precisa converter, já está em reais
            $valorEmReais = (string) abs((float) $transacao->valor);
            
            // Calcula incremento com base no tipo de transação usando bcmath
            if ($transacao->tipo === 'entrada') {
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
            
            Log::info('✅ Saldo da entidade atualizado após marcar como recebido/pago', [
                'entidade_id' => $entidade->id,
                'transacao_id' => $transacao->id,
                'tipo_transacao' => $transacao->tipo,
                'situacao' => $transacao->situacao->value,
                'valor_transacao_centavos' => $transacao->valor,
                'valor_em_reais' => $valorEmReais,
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
            
            // Cria movimentação para a parcela
            $transacaoParcela->movimentacao()->create([
                'entidade_id' => $entidadeIdParcela,
                'tipo' => $data['tipo'],
                'valor' => $dadosParcela['valor'],
                'data' => $data['data_competencia'],
                'descricao' => $dadosParcela['descricao'],
                'company_id' => $data['company_id'],
                'created_by' => $data['created_by'],
                'created_by_name' => $data['created_by_name'],
                'updated_by' => $data['updated_by'],
                'updated_by_name' => $data['updated_by_name'],
                'lancamento_padrao_id' => $dadosParcela['lancamento_padrao_id'],
                'data_competencia' => $data['data_competencia'],
            ]);
            
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
            $totalEntradas = TransacaoFinanceira::whereYear('data_competencia', $ano)
                ->whereMonth('data_competencia', $mes)
                ->where('tipo', 'entrada')
                ->sum('valor');

            // Busca o total de saídas (despesas) do mês
            $totalSaidas = TransacaoFinanceira::whereYear('data_competencia', $ano)
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
}
