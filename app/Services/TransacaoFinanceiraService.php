<?php

namespace App\Services;

use App\Models\Financeiro\TransacaoFinanceira;
use App\Models\Movimentacao;
use App\Models\LancamentoPadrao;
use App\Models\Financeiro\Recorrencia;
use App\Models\Banco;
use App\Models\ModulosAnexo;
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
     * @param array $validatedData Dados validados do request
     * @param Request $request Request original para acessar dados não validados
     * @return TransacaoFinanceira
     * @throws \Exception
     */
    public function criarLancamento(array $validatedData, Request $request): TransacaoFinanceira
    {
        return DB::transaction(function () use ($validatedData, $request) {
            // 1. Prepara os dados
            $data = $this->prepararDados($validatedData, $request);
            
            // 2. Calcula a situação baseada no checkbox "Pago"
            $data['situacao'] = $this->calcularSituacao($request);
            
            // 3. Cria a movimentação
            $movimentacao = $this->criarMovimentacao($data);
            $data['movimentacao_id'] = $movimentacao->id;
            
            // 4. Cria a transação financeira
            $transacao = TransacaoFinanceira::create($data);
            
            // 5. Processa pagamento (se houver)
            if ($this->temPagamento($request, $data)) {
                $this->processarPagamento($transacao, $movimentacao, $data, $request);
            }
            
            // 6. Processa lançamento padrão especial (Depósito Bancário)
            $this->processarLancamentoPadrao($data);
            
            // 7. Processa anexos
            $this->processarAnexos($request, $transacao);
            
            // 8. Processa recorrência (se houver)
            if ($this->temRecorrencia($request)) {
                $this->processarRecorrencia($transacao, $movimentacao, $data, $request);
            }
            
            // 9. Processa parcelas (se houver)
            if ($this->temParcelas($request)) {
                $this->processarParcelas($transacao, $movimentacao, $data, $request);
                // Remove a transação principal, pois as parcelas são as transações reais
                $transacao->delete();
            }
            
            return $transacao;
        });
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
     * Cria a movimentação associada à transação
     */
    protected function criarMovimentacao(array $data): Movimentacao
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

        return Movimentacao::create([
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
        ]);
    }

    /**
     * Verifica se há pagamento a ser processado
     * IMPORTANTE: Só processa pagamento se o checkbox "pago" ou "recebido" estiver marcado
     */
    protected function temPagamento(Request $request, array $data): bool
    {
        $tipo = $request->input('tipo');
        
        // Para ENTRADA: só processar pagamento se checkbox "recebido" estiver marcado
        if ($tipo === 'entrada') {
            return $request->has('recebido') && $request->input('recebido') && $request->input('recebido') !== '0';
        }
        
        // Para SAÍDA: só processar pagamento se checkbox "pago" estiver marcado
        if ($tipo === 'saida') {
            return $request->has('pago') && $request->input('pago') && $request->input('pago') !== '0';
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
                ? \App\Enums\SituacaoTransacao::RECEBIDO->value 
                : \App\Enums\SituacaoTransacao::PAGO->value;
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
        }
        // Pagamento parcial será tratado por método específico do controller
    }

    /**
     * Processa lançamento padrão especial (Depósito Bancário)
     */
    protected function processarLancamentoPadrao(array $data): void
    {
        if (!isset($data['lancamento_padrao_id'])) {
            return;
        }

        $lancamentoPadrao = LancamentoPadrao::find($data['lancamento_padrao_id']);
        
        if ($lancamentoPadrao && $lancamentoPadrao->description === 'Deposito Bancário') {
            $data['origem'] = 'Banco';
            $data['tipo'] = 'entrada';
            $lancamentoPadrao->refresh();

            $movimentacaoBanco = Movimentacao::create([
                'entidade_id' => $data['entidade_banco_id'],
                'tipo' => $data['tipo'],
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
            ]);

            $data['movimentacao_id'] = $movimentacaoBanco->id;
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
     */
    protected function temRecorrencia(Request $request): bool
    {
        return $request->has('configuracao_recorrencia') || 
            ($request->has('intervalo_repeticao') && 
             $request->has('frequencia') && 
             $request->has('apos_ocorrencias'));
    }

    /**
     * Processa recorrência
     * Método será implementado posteriormente ou delegado ao controller
     */
    protected function processarRecorrencia(
        TransacaoFinanceira $transacao,
        Movimentacao $movimentacao,
        array $data,
        Request $request
    ): void {
        // Implementação futura
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
     * Método será implementado posteriormente ou delegado ao controller
     */
    protected function processarParcelas(
        TransacaoFinanceira $transacao,
        Movimentacao $movimentacao,
        array $data,
        Request $request
    ): void {
        // Implementação futura
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
