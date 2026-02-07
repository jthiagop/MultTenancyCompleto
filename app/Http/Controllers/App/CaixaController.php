<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Http\Requests\Financer\StoreTransacaoFinanceiraRequest;
use App\Models\Anexo;
use App\Models\Banco;
use App\Models\Caixa;
use App\Models\Company;
use App\Models\ContasFinanceiras;
use App\Models\EntidadeFinanceira;
use App\Models\Financeiro\CostCenter;
use App\Models\Financeiro\ModulosAnexo;
use App\Models\Financeiro\TransacaoFinanceira;
use App\Models\FormasPagamento;
use App\Models\Parceiro;
use App\Models\LancamentoPadrao;
use App\Models\Movimentacao;
use App\Support\Money;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Flasher\Laravel\Facade\Flasher;
use Illuminate\Support\Facades\Log;

class CaixaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $activeCompanyId = session('active_company_id');
        if (!$activeCompanyId) {
            return redirect()->route('dashboard')->with('error', 'Selecione uma empresa.');
        }

        // Todas as buscas agora são filtradas
        $transacoesFinanceiras = TransacaoFinanceira::with('entidadeFinanceira') // Eager Load
            ->where('company_id', $activeCompanyId) // Filtro pelo company_id
            ->orderBy('id', 'DESC')
            ->paginate(10);

        $valorEntradaBanco = Banco::getBancoEntrada();
        $ValorSaidasBanco = Banco::getBancoSaida();

        $valorEntrada = caixa::getCaixaEntrada();
        $ValorSaidas = caixa::getCaixaSaida();

        $caixas = Caixa::getCaixaList();

        // Busca todos os dados da tabela formas_pagamento
        $formasPagamento = FormasPagamento::where('ativo', true)->orderBy('nome')->get();
        $entidades = Caixa::getEntidadesCaixa();
        $entidadesBanco = Caixa::getEntidadesBanco();
        $lps = LancamentoPadrao::all();
        $parceiros = Parceiro::forActiveCompany()->orderBy('nome')->get();

        list($somaEntradas, $somaSaida) = caixa::getCaixa();
        $total = $somaEntradas - $somaSaida;

        $centrosAtivos = CostCenter::getCadastroCentroCusto();

        $todasEntidades = EntidadeFinanceira::getEntidadeFinanceira();

        // filtragem de despesas
        $despesasEmAberto = ContasFinanceiras::where('tipo_financeiro', 'despesa')
            ->where('status_pagamento', 'em aberto')
            ->whereDate('data_primeiro_vencimento', '<=', Carbon::today())
            ->get();
        $valorDespesaTotal = $despesasEmAberto->sum('valor');


        // filtragem de receitas
        $receitasEmAberto = ContasFinanceiras::where('tipo_financeiro', 'receita')
            ->where('status_pagamento', 'em aberto')
            ->whereDate('data_primeiro_vencimento', '<=', Carbon::today())
            ->get();

        $receitasAVencer = ContasFinanceiras::where('tipo_financeiro', 'receita')
            ->whereIn('status_pagamento', ['em aberto', 'pendente', 'vencida'])
            ->whereDate('data_primeiro_vencimento', '>', Carbon::today())
            ->get();


        $valorTotal = $receitasEmAberto->sum('valor');
        $TotalreceitasAVencer = $receitasAVencer->sum('valor');

        return view('app.financeiro.index', compact(
            'caixas',
            'valorEntrada',
            'ValorSaidas',
            'valorEntradaBanco',
            'ValorSaidasBanco',
            'lps',
            'centrosAtivos',
            'todasEntidades',
            'total',
            'entidades',
            'entidadesBanco',
            'transacoesFinanceiras',
            'formasPagamento',
            'receitasEmAberto',
            'receitasAVencer',
            'valorTotal',
            'TotalreceitasAVencer',
            'despesasEmAberto',
            'valorDespesaTotal',
            'parceiros',
        ))->with('fornecedores', $parceiros);
    }

    public function list(Request $request)
    {
        // 1. CONFIGURAÇÃO INICIAL E SEGURANÇA (A fonte da verdade é a SESSÃO)
        $activeTab = $request->input('tab', 'overview');
        $activeCompanyId = session('active_company_id');

        if (!$activeCompanyId) {
            return redirect()->route('dashboard')->with('error', 'Por favor, selecione uma empresa para visualizar os dados.');
        }

        // 2. BUSCA DE DADOS, AGORA TODOS FILTRADOS PELA EMPRESA ATIVA

        // Lançamentos Padrão da empresa ativa
        $lps = LancamentoPadrao::all();
        $formasPagamento = FormasPagamento::where('ativo', true)->orderBy('nome')->get();
        $parceiros = Parceiro::forActiveCompany()->orderBy('nome')->get();

        // Entidades do tipo 'Caixa' da empresa ativa
        $entidades = EntidadeFinanceira::where('company_id', $activeCompanyId)->where('tipo', 'caixa')->get();

        // Entidades do tipo 'Banco' da empresa ativa (parece repetido, mas corrigindo a lógica)
        $entidadesBanco = EntidadeFinanceira::where('company_id', $activeCompanyId)->where('tipo', 'banco')->get();
        // Somando entradas e saídas do Caixa da empresa ativa - Filtrando por entidade financeira do tipo 'caixa'
        $somaEntradas = TransacaoFinanceira::join('entidades_financeiras', 'transacoes_financeiras.entidade_id', '=', 'entidades_financeiras.id')
            ->where('transacoes_financeiras.company_id', $activeCompanyId)
            ->where('entidades_financeiras.tipo', 'caixa')
            ->where('transacoes_financeiras.tipo', 'entrada')
            ->whereNull('transacoes_financeiras.deleted_at')
            ->sum('transacoes_financeiras.valor');

        $somaSaidas = TransacaoFinanceira::join('entidades_financeiras', 'transacoes_financeiras.entidade_id', '=', 'entidades_financeiras.id')
            ->where('transacoes_financeiras.company_id', $activeCompanyId)
            ->where('entidades_financeiras.tipo', 'caixa')
            ->where('transacoes_financeiras.tipo', 'saida')
            ->whereNull('transacoes_financeiras.deleted_at')
            ->sum('transacoes_financeiras.valor');



        // Total das Entidades Financeiras da empresa ativa
        $total = EntidadeFinanceira::where('company_id', $activeCompanyId)
            ->where('tipo', 'caixa') // Mantendo o filtro por 'caixa' que você tinha
            ->sum('saldo_atual');

        // Transações de Caixa da empresa ativa - Filtrando por entidade financeira do tipo 'caixa'
        $transacoes = TransacaoFinanceira::with(['entidadeFinanceira', 'lancamentoPadrao', 'modulos_anexos'])
            ->join('entidades_financeiras', 'transacoes_financeiras.entidade_id', '=', 'entidades_financeiras.id')
            ->where('transacoes_financeiras.company_id', $activeCompanyId)
            ->where('entidades_financeiras.tipo', 'caixa')
            ->whereNull('transacoes_financeiras.deleted_at')
            ->select('transacoes_financeiras.*')
            ->orderBy('transacoes_financeiras.data_competencia', 'DESC')
            ->orderBy('transacoes_financeiras.id', 'DESC')
            ->get();


        // Busca todos os centros de custo ativos DA EMPRESA ATIVA NA SESSÃO
        $centrosAtivos = CostCenter::forActiveCompany()->get();


        // A variável $company agora é a empresa ativa na sessão
        $company = Auth::user()->companies()->find($activeCompanyId);

        // 3. RETORNO PARA A VIEW com os dados corretos e filtrados
        return view('app.financeiro.caixa.list', [
            'transacoes' => $transacoes,
            'valorEntrada' => $somaEntradas, // Usando a variável já calculada
            'valorSaidas' => $somaSaidas,   // Usando a variável já calculada
            'total' => $total,
            'lps' => $lps,
            'formasPagamento' => $formasPagamento,
            'parceiros' => $parceiros,
            'company' => $company,
            'entidades' => $entidades,
            'entidadesBanco' => $entidadesBanco,
            'activeTab' => $activeTab,
            'centrosAtivos' => $centrosAtivos,
            'fornecedores' => $parceiros,
        ]);
    }

    /**
     * Buscar saldos mensais de caixa para o relatório
     */
    public function getSaldosMensais(Request $request)
    {
        $activeCompanyId = session('active_company_id');

        if (!$activeCompanyId) {
            return response()->json([
                'success' => false,
                'message' => 'Nenhuma empresa selecionada.'
            ], 400);
        }

        $ano = $request->input('ano', date('Y'));
        $codigo = $request->input('codigo', '');
        $centroCustoId = $request->input('centro_custo_id', '');

        // Buscar movimentações do ano
        $queryAno = Movimentacao::join('entidades_financeiras', 'movimentacoes.entidade_id', '=', 'entidades_financeiras.id')
            ->where('entidades_financeiras.company_id', $activeCompanyId)
            ->where('entidades_financeiras.tipo', 'caixa')
            ->whereNull('movimentacoes.deleted_at')
            ->whereYear('movimentacoes.data', $ano);

        // Filtro por código (ID da entidade financeira)
        if ($codigo) {
            $queryAno->where('entidades_financeiras.id', $codigo);
        }

        // Filtro por centro de custo - usar subquery para evitar duplicatas
        if ($centroCustoId) {
            $queryAno->whereExists(function($query) use ($centroCustoId) {
                $query->select(DB::raw(1))
                    ->from('transacoes_financeiras')
                    ->whereColumn('transacoes_financeiras.movimentacao_id', 'movimentacoes.id')
                    ->where('transacoes_financeiras.cost_center_id', $centroCustoId)
                    ->whereNull('transacoes_financeiras.deleted_at');
            });
        }

        $movimentacoesAno = $queryAno->select(
                DB::raw('MONTH(movimentacoes.data) as mes'),
                DB::raw('SUM(CASE WHEN movimentacoes.tipo = "entrada" THEN movimentacoes.valor ELSE 0 END) as entradas'),
                DB::raw('SUM(CASE WHEN movimentacoes.tipo = "saida" THEN movimentacoes.valor ELSE 0 END) as saidas')
            )
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        // Buscar saldo anterior (saldo acumulado até o início do ano)
        $saldoAnteriorQuery = Movimentacao::join('entidades_financeiras', 'movimentacoes.entidade_id', '=', 'entidades_financeiras.id')
            ->where('entidades_financeiras.company_id', $activeCompanyId)
            ->where('entidades_financeiras.tipo', 'caixa')
            ->whereNull('movimentacoes.deleted_at')
            ->whereRaw('YEAR(movimentacoes.data) < ?', [$ano]);

        // Filtro por código
        if ($codigo) {
            $saldoAnteriorQuery->where('entidades_financeiras.id', $codigo);
        }

        // Filtro por centro de custo - usar subquery para evitar duplicatas
        if ($centroCustoId) {
            $saldoAnteriorQuery->whereExists(function($query) use ($centroCustoId) {
                $query->select(DB::raw(1))
                    ->from('transacoes_financeiras')
                    ->whereColumn('transacoes_financeiras.movimentacao_id', 'movimentacoes.id')
                    ->where('transacoes_financeiras.cost_center_id', $centroCustoId)
                    ->whereNull('transacoes_financeiras.deleted_at');
            });
        }

        $saldoAnterior = $saldoAnteriorQuery->select(
            DB::raw('COALESCE(SUM(CASE
                WHEN movimentacoes.tipo = "entrada" THEN movimentacoes.valor
                WHEN movimentacoes.tipo = "saida" THEN -movimentacoes.valor
                ELSE 0
            END), 0) as saldo')
        )->value('saldo') ?? 0;

        // Organizar dados por mês
        $dados = [];
        $saldoAnteriorMes = (float) $saldoAnterior; // Saldo anterior do primeiro mês

        for ($mes = 1; $mes <= 12; $mes++) {
            $movimentacao = $movimentacoesAno->firstWhere('mes', $mes);

            $entradas = $movimentacao ? (float) $movimentacao->entradas : 0;
            $saidas = $movimentacao ? (float) $movimentacao->saidas : 0;

            // Saldo atual é o saldo anterior + entradas - saídas
            $saldoAtual = $saldoAnteriorMes + $entradas - $saidas;

            $dados[$mes] = [
                'saldo_anterior' => round($saldoAnteriorMes, 2),
                'entradas' => round($entradas, 2),
                'saidas' => round($saidas, 2),
                'saldo_atual' => round($saldoAtual, 2)
            ];

            // O saldo anterior do próximo mês é o saldo atual deste mês
            $saldoAnteriorMes = $saldoAtual;
        }

        return response()->json([
            'success' => true,
            'data' => $dados
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $activeCompanyId = session('active_company_id');
        if (!$activeCompanyId) {
            return redirect()->back()->with('error', 'Selecione uma empresa.');
        }

        $lps = LancamentoPadrao::forActiveCompany()->get(); // Usando scope
        $totalCaixa = EntidadeFinanceira::forActiveCompany()->where('tipo', 'caixa')->sum('saldo_atual');

        return view('app.financeiro.caixa.create', compact('lps', 'totalCaixa'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTransacaoFinanceiraRequest $request)
    {
        // Recupera a companhia associada ao usuário autenticado
        $activeCompanyId = session('active_company_id');

        // 2. Verificação de segurança para garantir que uma empresa está ativa.
        if (!$activeCompanyId) {
            // É melhor usar um Flasher ou uma mensagem de erro mais específica
            flash()->error('Nenhuma empresa selecionada. Por favor, escolha uma empresa antes de criar uma transação.');
            return redirect()->back();
        }

        // Validação automática com StoreTransacaoFinanceiraRequest
        $validatedData = $request->validated();

        // Formata valores
        // Tenta múltiplos formatos de data (d/m/Y ou d-m-Y)
        $dataCompetencia = $validatedData['data_competencia'];
        try {
            // Tenta primeiro com formato d/m/Y (barra)
            $validatedData['data_competencia'] = Carbon::createFromFormat('d/m/Y', $dataCompetencia)->format('Y-m-d');
        } catch (\Exception $e) {
            try {
                // Se falhar, tenta com formato d-m-Y (hífen)
                $validatedData['data_competencia'] = Carbon::createFromFormat('d-m-Y', $dataCompetencia)->format('Y-m-d');
            } catch (\Exception $e2) {
                // Se ambos falharem, tenta parse automático
                $validatedData['data_competencia'] = Carbon::parse($dataCompetencia)->format('Y-m-d');
            }
        }
        // O valor já foi convertido no prepareForValidation do Request
        
        // Processa data_vencimento (já convertido no prepareForValidation, mas garante formato correto)
        if (isset($validatedData['vencimento']) && $validatedData['vencimento']) {
            // Remove o campo 'vencimento' se ainda existir (já foi convertido para 'data_vencimento')
            unset($validatedData['vencimento']);
        }
        
        // Se data_vencimento não foi fornecida, usa a data de competência como padrão
        if (!isset($validatedData['data_vencimento']) || !$validatedData['data_vencimento']) {
            $validatedData['data_vencimento'] = $validatedData['data_competencia'];
        } else {
            // Garante que está no formato Y-m-d (já deve estar, mas verifica)
            if (strpos($validatedData['data_vencimento'], '/') !== false) {
                $validatedData['data_vencimento'] = Carbon::createFromFormat('d/m/Y', $validatedData['data_vencimento'])->format('Y-m-d');
            }
        }
        
        // Processa valor_pago se fornecido
        if (isset($validatedData['valor_pago']) && $validatedData['valor_pago']) {
            // Já foi convertido no prepareForValidation, mas garante que seja float
            $validatedData['valor_pago'] = (float) $validatedData['valor_pago'];
        } else {
            // Se não fornecido, verifica se o checkbox "pago" está marcado
            if ($request->has('pago') && $request->input('pago')) {
                $validatedData['valor_pago'] = (float) $validatedData['valor'];
            } else {
                $validatedData['valor_pago'] = 0;
            }
        }

        // Processa juros se fornecido (já convertido no prepareForValidation)
        if (isset($validatedData['juros'])) {
            $validatedData['juros'] = (float) ($validatedData['juros'] ?? 0);
        } else {
            $validatedData['juros'] = 0;
        }

        // Processa multa se fornecido (já convertido no prepareForValidation)
        if (isset($validatedData['multa'])) {
            $validatedData['multa'] = (float) ($validatedData['multa'] ?? 0);
        } else {
            $validatedData['multa'] = 0;
        }

        // Processa desconto se fornecido (já convertido no prepareForValidation)
        if (isset($validatedData['desconto'])) {
            $validatedData['desconto'] = (float) ($validatedData['desconto'] ?? 0);
        } else {
            $validatedData['desconto'] = 0;
        }

        // Processa valor_a_pagar se fornecido, senão calcula
        if (isset($validatedData['valor_a_pagar']) && $validatedData['valor_a_pagar']) {
            $validatedData['valor_a_pagar'] = (float) $validatedData['valor_a_pagar'];
        } else {
            // Calcula: valor_a_pagar = valor + juros + multa - desconto
            $valor = (float) $validatedData['valor'];
            $juros = (float) ($validatedData['juros'] ?? 0);
            $multa = (float) ($validatedData['multa'] ?? 0);
            $desconto = (float) ($validatedData['desconto'] ?? 0);
            $valorAPagar = $valor + $juros + $multa - $desconto;
            $validatedData['valor_a_pagar'] = max(0, $valorAPagar); // Garante que não seja negativo
        }
        
        // Processa agendado (checkbox)
        $validatedData['agendado'] = $request->has('agendado') && $request->input('agendado') ? true : false;
        
        // Situação será calculada automaticamente pelo modelo, mas se fornecida manualmente, mantém
        if (!isset($validatedData['situacao']) || !$validatedData['situacao']) {
            $validatedData['situacao'] = 'em_aberto'; // Será recalculado pelo modelo se necessário
        }

        // Adiciona informações padrão
        $validatedData['company_id'] = $activeCompanyId;
        $validatedData['created_by'] = Auth::id();
        $validatedData['created_by_name'] = Auth::user()->name;
        $validatedData['updated_by'] = Auth::id();
        $validatedData['updated_by_name'] = Auth::user()->name;

        // 1) Criar movimentação no Caixa
        $movimentacao = $this->movimentacao($validatedData);
        $validatedData['movimentacao_id'] = $movimentacao->id;

        // 2) Criar lançamento no Caixa
        $caixa = TransacaoFinanceira::create($validatedData);

        // Busca o ID do Lançamento Padrão "Depósito Bancário"
        $lancamentoPadraoDepositoId = LancamentoPadrao::where('description', 'Deposito Bancário')->value('id');

        // 3) Se o Lançamento Padrão for "Depósito Bancário", cria o lançamento no Banco
        if (isset($validatedData['lancamento_padrao_id']) && (int) $validatedData['lancamento_padrao_id'] === (int) $lancamentoPadraoDepositoId) {

            // Ajusta os dados para o Banco
            $validatedData['origem'] = 'Banco';
            $validatedData['tipo'] = ($validatedData['tipo'] === 'saida') ? 'entrada' : 'saida';

            // Se houver um banco associado, adiciona ao campo entidade_id
            if (!empty($validatedData['entidade_banco_id'])) {
                $validatedData['entidade_id'] = $validatedData['entidade_banco_id'];
            }

            // Criar movimentação no Banco
            $movimentacaoBanco = $this->movimentacao($validatedData);
            $validatedData['movimentacao_id'] = $movimentacaoBanco->id;

            // Criar lançamento no Banco
            $banco = TransacaoFinanceira::create($validatedData);

            // Processar anexos
            $this->processarAnexos($request, $banco);
        }

        // Processar anexos do caixa
        $this->processarAnexos($request, $caixa);

        // Mensagem de sucesso
        Flasher::addSuccess('Lançamento criado com sucesso!');

        return redirect()->back()->with('message', 'Lançamento criado com sucesso!');
    }


    /**
     * Processa movimentacao.
     */
    private function movimentacao(array $validatedData)
    {
        // Busca o lançamento padrão para obter conta_debito_id e conta_credito_id se não foram enviados
        $contaDebitoId = null;
        $contaCreditoId = null;
        $lancamentoPadraoId = null;

        if (isset($validatedData['lancamento_padrao_id']) && $validatedData['lancamento_padrao_id']) {
            $lancamentoPadraoId = $validatedData['lancamento_padrao_id'];
            $lancamentoPadrao = LancamentoPadrao::find($lancamentoPadraoId);

            if ($lancamentoPadrao) {
                // Recarrega o lançamento padrão para garantir que temos os campos contábeis atualizados
                $lancamentoPadrao->refresh();

                // Se não foram enviados no request, busca do lançamento padrão
                if (!isset($validatedData['conta_debito_id']) && $lancamentoPadrao->conta_debito_id) {
                    $contaDebitoId = $lancamentoPadrao->conta_debito_id;
                } elseif (isset($validatedData['conta_debito_id'])) {
                    $contaDebitoId = $validatedData['conta_debito_id'];
                }

                if (!isset($validatedData['conta_credito_id']) && $lancamentoPadrao->conta_credito_id) {
                    $contaCreditoId = $lancamentoPadrao->conta_credito_id;
                } elseif (isset($validatedData['conta_credito_id'])) {
                    $contaCreditoId = $validatedData['conta_credito_id'];
                }
            }
        }

        // Cria o lançamento na tabela 'movimentacoes'
        $movimentacao = Movimentacao::create([
            'entidade_id' => $validatedData['entidade_id'],
            'tipo'        => $validatedData['tipo'],
            'valor'       => $validatedData['valor'],
            'data'        => $validatedData['data_competencia'],
            'descricao'   => $validatedData['descricao'],
            'company_id'  => $validatedData['company_id'],
            'created_by'  => $validatedData['created_by'],
            'created_by_name' => $validatedData['created_by_name'],
            'updated_by'      => $validatedData['updated_by'],
            'updated_by_name' => $validatedData['updated_by_name'],
            'lancamento_padrao_id' => $lancamentoPadraoId,
            'conta_debito_id' => $contaDebitoId,
            'conta_credito_id' => $contaCreditoId,
            'data_competencia' => $validatedData['data_competencia'],
        ]);

        // Retorna o objeto Movimentacao recém-criado, de onde poderemos pegar o ID
        return $movimentacao;
    }

    /**
     * Processa lançamentos padrão.
     */
    private function processarLancamentoPadrao(array $validatedData)
    {
        $lancamentoPadrao = LancamentoPadrao::find($validatedData['lancamento_padrao_id']);
        if ($lancamentoPadrao && $lancamentoPadrao->description === 'Deposito Bancário') {
            $validatedData['origem'] = 'Caixa';
            $validatedData['tipo'] = 'entrada';

            // Recarrega o lançamento padrão para garantir que temos os campos contábeis atualizados
            $lancamentoPadrao->refresh();

            // Cria outra movimentação para "Deposito Bancário"
            $movimentacaoBanco = Movimentacao::create([
                'entidade_id' => $validatedData['entidade_banco_id'],
                'tipo' => $validatedData['tipo'],
                'valor' => $validatedData['valor'],
                'descricao' => $validatedData['descricao'],
                'company_id' => $validatedData['company_id'],
                'created_by' => $validatedData['created_by'],
                'created_by_name' => $validatedData['created_by_name'],
                'updated_by' => $validatedData['updated_by'],
                'updated_by_name' => $validatedData['updated_by_name'],
                'lancamento_padrao_id' => $lancamentoPadrao->id,
                'conta_debito_id' => $lancamentoPadrao->conta_debito_id ?? null,
                'conta_credito_id' => $lancamentoPadrao->conta_credito_id ?? null,
                'data_competencia' => $validatedData['data_competencia'],
            ]);

            // Cria o lançamento no banco
            $validatedData['movimentacao_id'] = $movimentacaoBanco->id;
            Banco::create($validatedData);
        }
    }

    /**
     * Processa os anexos enviados.
     */
    private function processarAnexos(Request $request, TransacaoFinanceira $caixa)
    {
        // Verifica se há anexos no formato anexos[index][arquivo] ou anexos[index][link]
        if (!$request->has('anexos') || !is_array($request->input('anexos'))) {
            return;
        }

        $anexos = $request->input('anexos');

        foreach ($anexos as $index => $anexoData) {
            $formaAnexo = $anexoData['forma_anexo'] ?? 'arquivo';
            $tipoAnexo = $anexoData['tipo_anexo'] ?? null;
            $descricao = $anexoData['descricao'] ?? null;

            if ($formaAnexo === 'arquivo') {
                // Processa arquivo
                $fileKey = "anexos.{$index}.arquivo";

                if ($request->hasFile($fileKey)) {
                    $file = $request->file($fileKey);
                    $nomeOriginal = $file->getClientOriginalName();
                    $anexoName = time() . '_' . $nomeOriginal;
                    $anexoPath = $file->storeAs('anexos', $anexoName, 'public');

                    ModulosAnexo::create([
                        'anexavel_id'     => $caixa->id,
                        'anexavel_type'   => TransacaoFinanceira::class,
                        'forma_anexo'     => 'arquivo',
                        'nome_arquivo'    => $nomeOriginal,
                        'caminho_arquivo' => $anexoPath,
                        'tipo_arquivo'    => $file->getMimeType() ?? '',
                        'extensao_arquivo' => $file->getClientOriginalExtension(),
                        'mime_type'       => $file->getMimeType() ?? '',
                        'tamanho_arquivo' => $file->getSize(),
                        'tipo_anexo'      => $tipoAnexo,
                        'descricao'       => $descricao,
                        'status'          => 'ativo',
                        'data_upload'     => now(),
                        'created_by'     => Auth::id(),
                        'created_by_name' => Auth::user()->name,
                    ]);
                }
            } elseif ($formaAnexo === 'link') {
                // Processa link
                $link = $anexoData['link'] ?? null;

                if ($link) {
                    ModulosAnexo::create([
                        'anexavel_id'     => $caixa->id,
                        'anexavel_type'   => TransacaoFinanceira::class,
                        'forma_anexo'     => 'link',
                        'link'            => $link,
                        'tipo_anexo'      => $tipoAnexo,
                        'descricao'       => $descricao,
                        'status'          => 'ativo',
                        'data_upload'     => now(),
                        'created_by'     => Auth::id(),
                        'created_by_name' => Auth::user()->name,
                    ]);
                }
            }
        }

        // Atualiza automaticamente o campo comprovacao_fiscal
        $caixa->updateComprovacaoFiscal();
    }


    /**
     * Display the specified resource.
     */
    public function show(Caixa $caixa)
    {
        return view('app.financeiro.caixa.show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    // O edit fica mais seguro
    public function edit($id)
    {
        // SEGURANÇA: Garante que a transação pertence à empresa ativa
        $caixa = TransacaoFinanceira::forActiveCompany()
            ->with('modulos_anexos', 'recibo.address')
            ->findOrFail($id);

        // SEGURANÇA: Garante que os dados de apoio também são da empresa ativa
        $lps = LancamentoPadrao::all();
        $entidades = EntidadeFinanceira::forActiveCompany()->get();
        $centrosAtivos = CostCenter::forActiveCompany()->get();

        return view('app.financeiro.caixa.edit', compact('caixa', 'lps', 'entidades', 'centrosAtivos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            // Obtenha a empresa do usuário autenticado
            $transacao = TransacaoFinanceira::forActiveCompany()->findOrFail($id);

            // Verifica se é edição inline (com field_type) ou edição completa (drawer)
            $fieldType = $request->input('field_type');
            
            if ($fieldType) {
                // ===== MODO: Edição inline de campo único =====
                return $this->updateInlineField($request, $transacao, $fieldType);
            } else {
                // ===== MODO: Edição completa via drawer =====
                return $this->updateFullTransaction($request, $transacao);
            }
            
        } catch (\Exception $e) {
            // Log de erro e mensagem de retorno
            \Log::error('Erro ao atualizar transação de caixa: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao atualizar transação: ' . $e->getMessage()
                ], 500);
            }

            \Flasher\Laravel\Facade\Flasher::addError('Erro ao atualizar transação: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }
    
    /**
     * Atualiza um campo específico (edição inline)
     */
    protected function updateInlineField(Request $request, TransacaoFinanceira $transacao, string $fieldType)
    {
        $dataToValidate = $request->all();
        $dataToUpdate = [];
        $rules = [];

        // Determina qual campo está sendo editado baseado no field_type
        if ($fieldType === 'descricao' && $request->has('descricao')) {
            $rules['descricao'] = 'required|string|max:255';
            $dataToUpdate['descricao'] = $request->descricao;
        } elseif ($fieldType === 'lancamento_padrao_id' && $request->has('lancamento_padrao_id')) {
            $rules['lancamento_padrao_id'] = 'required|exists:lancamento_padraos,id';
            $dataToUpdate['lancamento_padrao_id'] = $request->lancamento_padrao_id;
        } elseif ($fieldType === 'cost_center_id' && $request->has('cost_center_id')) {
            $rules['cost_center_id'] = 'nullable|exists:cost_centers,id';
            $dataToUpdate['cost_center_id'] = $request->cost_center_id ? $request->cost_center_id : null;
        } elseif ($fieldType === 'valor' && $request->has('valor')) {
            // Tratar o valor do campo "valor" usando a classe de suporte Money
            $money = Money::fromHumanInput($request->input('valor'));
            $valor = $money->toDatabase();
            
            $dataToValidate['valor'] = $valor;
            $rules['valor'] = 'required|numeric|min:0';
            $dataToUpdate['valor'] = $valor;
        }

        // Valida apenas os campos que foram enviados
        if (!empty($rules)) {
            $validator = Validator::make($dataToValidate, $rules);

            // Se a validação falhar
            if ($validator->fails()) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Erro de validação',
                        'errors' => $validator->errors()
                    ], 422);
                }

                foreach ($validator->errors()->all() as $error) {
                    \Flasher\Laravel\Facade\Flasher::addError($error);
                }
                return redirect()->back()->withInput();
            }
        }

        // Atualiza o registro
        $transacao->update($dataToUpdate);

        // Atualiza também a movimentação associada
        if ($transacao->movimentacao && !empty($dataToUpdate)) {
            $movimentacaoData = [];
            if (isset($dataToUpdate['descricao'])) {
                $movimentacaoData['descricao'] = $dataToUpdate['descricao'];
            }
            if (isset($dataToUpdate['lancamento_padrao_id'])) {
                $movimentacaoData['lancamento_padrao_id'] = $dataToUpdate['lancamento_padrao_id'];
            }
            if (isset($dataToUpdate['valor'])) {
                $movimentacaoData['valor'] = $dataToUpdate['valor'];
            }
            if (!empty($movimentacaoData)) {
                $transacao->movimentacao->update($movimentacaoData);
            }
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Transação atualizada com sucesso!',
                'data' => [
                    'id' => $transacao->id,
                    'descricao' => $transacao->descricao,
                    'valor' => $transacao->valor,
                ]
            ]);
        }

        \Flasher\Laravel\Facade\Flasher::addSuccess('Transação atualizada com sucesso!');
        return redirect()->back();
    }
    
    /**
     * Atualiza a transação completa (via drawer)
     */
    protected function updateFullTransaction(Request $request, TransacaoFinanceira $transacao)
    {
        // Converte datas do formato brasileiro (dd/mm/yyyy) para Y-m-d antes da validação
        $camposData = ['data_competencia', 'data_vencimento', 'data_pagamento'];
        foreach ($camposData as $campoData) {
            $valor = $request->input($campoData);
            if ($valor && strpos($valor, '/') !== false) {
                try {
                    $request->merge([
                        $campoData => \Carbon\Carbon::createFromFormat('d/m/Y', trim($valor))->format('Y-m-d'),
                    ]);
                } catch (\Exception $e) {
                    // Mantém o valor original — a validação 'date' tratará o erro
                }
            }
        }

        // Validação dos campos
        $rules = [
            'data_competencia' => 'required|date',
            'descricao' => 'required|string|max:255',
            'valor' => 'required',
            'tipo' => 'required|in:entrada,saida',
            'lancamento_padrao_id' => 'required|exists:lancamento_padraos,id',
            'cost_center_id' => 'nullable|exists:cost_centers,id',
            'tipo_documento' => 'required|string',
            'entidade_id' => 'required|exists:entidades_financeiras,id',
            'fornecedor_id' => 'nullable|exists:parceiros,id',
            'numero_documento' => 'nullable|string',
            'historico_complementar' => 'nullable|string|max:500',
            'comprovacao_fiscal' => 'nullable|boolean',
        ];
        
        $validator = Validator::make($request->all(), $rules);
        
        if ($validator->fails()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro de validação',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            foreach ($validator->errors()->all() as $error) {
                \Flasher\Laravel\Facade\Flasher::addError($error);
            }
            return redirect()->back()->withInput();
        }
        
        // Prepara dados para atualização
        $dataToUpdate = [
            'data_competencia' => $request->input('data_competencia'),
            'descricao' => $request->input('descricao'),
            'valor' => Money::fromHumanInput($request->input('valor'))->toDatabase(),
            'tipo' => $request->input('tipo'),
            'lancamento_padrao_id' => $request->input('lancamento_padrao_id'),
            'cost_center_id' => $request->input('cost_center_id'),
            'tipo_documento' => $request->input('tipo_documento'),
            'entidade_id' => $request->input('entidade_id'),
            'parceiro_id' => $request->input('fornecedor_id'),
            'numero_documento' => $request->input('numero_documento'),
            'historico_complementar' => $request->input('historico_complementar'),
            'comprovacao_fiscal' => $request->boolean('comprovacao_fiscal'),
            'updated_by' => Auth::id(),
            'updated_by_name' => Auth::user()?->name,
        ];
        
        // Atualiza data de vencimento se fornecida
        if ($request->has('data_vencimento') || $request->has('vencimento')) {
            $dataToUpdate['data_vencimento'] = $request->input('data_vencimento') ?? $request->input('vencimento');
        }
        
        // Guarda a entidade antiga ANTES de atualizar (para recalcular saldo se mudou)
        $entidadeAntigaId = $transacao->entidade_id;
        
        // Atualiza a transação
        $transacao->update($dataToUpdate);
        
        // Recarrega a transação para obter a situação atualizada
        $transacao->refresh();
        
        // ✅ REGRA DE NEGÓCIO: Só atualiza movimentação se a situação for EFETIVADA (pago/recebido)
        // Transações em_aberto são apenas previsões e não devem ter movimentação
        $situacoesEfetivadas = ['pago', 'recebido'];
        // Extrai o valor string do enum (se for enum) ou usa direto se já for string
        $situacaoAtual = $transacao->situacao instanceof \App\Enums\SituacaoTransacao 
            ? $transacao->situacao->value 
            : $transacao->situacao;
        $entidadeNovaId = $dataToUpdate['entidade_id'];
        
        // Flag para saber se precisamos recalcular saldos
        $entidadesParaRecalcular = [];
        
        if (in_array($situacaoAtual, $situacoesEfetivadas)) {
            // ✅ Atualiza o valor_pago quando a transação está efetivada
            $transacao->update(['valor_pago' => $dataToUpdate['valor']]);
            
            // Se tem movimentação, atualiza. Se não tem, cria.
            if ($transacao->movimentacao) {
                // Verifica se a entidade mudou
                $entidadeMovimentacaoAntiga = $transacao->movimentacao->entidade_id;
                
                $transacao->movimentacao->update([
                    'entidade_id' => $dataToUpdate['entidade_id'],
                    'tipo' => $dataToUpdate['tipo'],
                    'valor' => $dataToUpdate['valor'],
                    'descricao' => $dataToUpdate['descricao'],
                    'data' => $dataToUpdate['data_competencia'],
                    'data_competencia' => $dataToUpdate['data_competencia'],
                    'lancamento_padrao_id' => $dataToUpdate['lancamento_padrao_id'],
                    'updated_by' => $dataToUpdate['updated_by'],
                    'updated_by_name' => $dataToUpdate['updated_by_name'],
                ]);
                
                // Se a entidade mudou, precisamos recalcular ambas
                if ($entidadeMovimentacaoAntiga != $entidadeNovaId) {
                    $entidadesParaRecalcular[] = $entidadeMovimentacaoAntiga;
                }
                $entidadesParaRecalcular[] = $entidadeNovaId;
            } else {
                // Não tem movimentação mas deveria ter (situação é pago/recebido)
                // Cria a movimentação
                $transacao->movimentacao()->create([
                    'entidade_id' => $dataToUpdate['entidade_id'],
                    'tipo' => $dataToUpdate['tipo'],
                    'valor' => $dataToUpdate['valor'],
                    'descricao' => $dataToUpdate['descricao'],
                    'data' => $dataToUpdate['data_competencia'],
                    'data_competencia' => $dataToUpdate['data_competencia'],
                    'company_id' => $transacao->company_id,
                    'lancamento_padrao_id' => $dataToUpdate['lancamento_padrao_id'],
                    'created_by' => $dataToUpdate['updated_by'],
                    'created_by_name' => $dataToUpdate['updated_by_name'],
                    'updated_by' => $dataToUpdate['updated_by'],
                    'updated_by_name' => $dataToUpdate['updated_by_name'],
                ]);
                $entidadesParaRecalcular[] = $entidadeNovaId;
            }
        } else {
            // Se a situação não é efetivada (ex: em_aberto) e existe movimentação, deve remover
            // Isso acontece quando uma transação paga é revertida para em_aberto
            if ($transacao->movimentacao) {
                $entidadeMovimentacaoRemovida = $transacao->movimentacao->entidade_id;
                
                Log::info('Removendo movimentação de transação não efetivada (Caixa)', [
                    'transacao_id' => $transacao->id,
                    'situacao' => $situacaoAtual
                ]);
                $transacao->movimentacao->delete();
                
                // Precisa recalcular o saldo da entidade que teve movimentação removida
                $entidadesParaRecalcular[] = $entidadeMovimentacaoRemovida;
            }
        }
        
        // ✅ ATUALIZA O SALDO_ATUAL DAS ENTIDADES AFETADAS
        if (!empty($entidadesParaRecalcular)) {
            $entidadesUnicas = array_unique($entidadesParaRecalcular);
            foreach ($entidadesUnicas as $entidadeId) {
                $entidade = EntidadeFinanceira::find($entidadeId);
                if ($entidade) {
                    $saldoAnterior = $entidade->saldo_atual;
                    $entidade->recalcularSaldo();
                    
                    Log::info('Saldo recalculado após atualização de transação (Caixa)', [
                        'entidade_id' => $entidadeId,
                        'transacao_id' => $transacao->id,
                        'saldo_anterior' => $saldoAnterior,
                        'saldo_novo' => $entidade->saldo_atual,
                    ]);
                }
            }
        }
        
        // Resposta de sucesso
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Lançamento atualizado com sucesso!',
                'data' => [
                    'id' => $transacao->id,
                    'descricao' => $transacao->descricao,
                    'valor' => $transacao->valor,
                ]
            ]);
        }
        
        \Flasher\Laravel\Facade\Flasher::addSuccess('Lançamento atualizado com sucesso!');
        return redirect()->back();
    }

    /**
     * Formata os dados do request antes de processá-los.
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    private function formatarDadosRequest($request)
    {
        // Tratar o valor do campo "valor"
        if ($request->has('valor')) {
            $request->merge([
                'valor' => str_replace(',', '.', str_replace('.', '', $request->input('valor')))
            ]);
        }

        // Converter data_competencia para o formato correto
        if ($request->has('data_competencia')) {
            $request->merge([
                'data_competencia' => Carbon::createFromFormat('d/m/Y', $request->input('data_competencia'))->format('Y-m-d')
            ]);
        }
    }

    /**
     * Reverte o saldo da entidade para antes da atualização.
     *
     * @param \App\Models\Movimentacao $movimentacao
     * @return void
     */
    private function reverterSaldoEntidade($movimentacao)
    {
        $entidade = EntidadeFinanceira::findOrFail($movimentacao->entidade_id);

        if ($movimentacao->tipo === 'entrada') {
            $entidade->saldo_atual -= $movimentacao->valor;
        } else {
            $entidade->saldo_atual += $movimentacao->valor;
        }

        $entidade->save();
    }

    /**
     * Aplica o novo saldo na entidade financeira.
     *
     * @param int $entidadeId
     * @param string $tipo
     * @param float $valor
     * @return void
     */
    private function aplicarSaldoEntidade($entidadeId, $tipo, $valor)
    {
        $entidade = EntidadeFinanceira::findOrFail($entidadeId);

        if ($tipo === 'entrada') {
            $entidade->saldo_atual += $valor;
        } else {
            $entidade->saldo_atual -= $valor;
        }

        $entidade->save();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            // 1) Localiza a transação financeira pelo ID
            $transacao = TransacaoFinanceira::findOrFail($id);

            // 2) Localiza a movimentação associada
            $movimentacao = Movimentacao::findOrFail($transacao->movimentacao_id);

            // 3) Localiza a entidade financeira associada
            $entidade = EntidadeFinanceira::findOrFail($movimentacao->entidade_id);

            // 4) Ajusta o saldo da entidade financeira
            // Obs.: aqui deve subtrair ou somar usando $movimentacao->valor (não $entidade->valor)
            if ($movimentacao->tipo === 'entrada') {
                // Se a movimentação era uma entrada, subtrai o valor do saldo atual
                $entidade->saldo_atual -= $transacao->valor;
            } else {
                // Se a movimentação era uma saída, adiciona o valor ao saldo atual
                $entidade->saldo_atual += $transacao->valor;
            }
            $entidade->save();

            // 5) Excluir anexos associados (se houver)
            $anexos = ModulosAnexo::where('anexavel_id', $transacao->id)
                ->where('anexavel_type', TransacaoFinanceira::class)
                ->get();

            foreach ($anexos as $anexo) {
                // Remove o arquivo do disco, se existir
                if (Storage::disk('public')->exists($anexo->caminho_arquivo)) {
                    Storage::disk('public')->delete($anexo->caminho_arquivo);
                }
                // Exclui o registro no banco
                $anexo->delete();
            }

            // 6) Exclui a movimentação associada
            $movimentacao->delete();

            // 7) Exclui a transação financeira
            $transacao->delete();

            // 8) Mensagem de sucesso e redirecionamento
            Flasher::addSuccess('Transação excluída com sucesso!');
            return redirect()->route('caixa.list');
        } catch (\Exception $e) {
            // 9) Em caso de erro, registra log e retorna com mensagem de erro
            Log::error('Erro ao excluir transação: ' . $e->getMessage());
            Flasher::addError('Erro ao excluir transação: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function getDespesasChartData(Request $request)
    {
        // 1. Validação da requisição (opcional, mas recomendado)
        $request->validate([
            'range' => 'nullable|integer',
            'start_date' => 'nullable|date_format:Y-m-d',
            'end_date' => 'nullable|date_format:Y-m-d',
        ]);

        // 2. Determina o período
        $activeCompanyId = session('active_company_id');
        $query = TransacaoFinanceira::forActiveCompany()->where('tipo', 'saida');

        if ($request->has('start_date') && $request->has('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->whereBetween('data_competencia', [$startDate, $endDate]);
        } else {
            $days = $request->input('range', 30); // Padrão de 30 dias
            $query->where('data_competencia', '>=', now()->subDays($days)->startOfDay());
        }

        // 3. Agrupa os dados por dia e soma os valores
        $despesas = $query->select(
            DB::raw('DATE(data_competencia) as data'),
            DB::raw('SUM(valor) as total')
        )
            ->groupBy('data')
            ->orderBy('data', 'asc')
            ->get();

        // 4. Formata os dados para o ApexCharts
        $categories = $despesas->pluck('data')->map(function ($date) {
            return Carbon::parse($date)->format('d/m');
        });

        $seriesData = $despesas->pluck('total');
        $totalGeral = $despesas->sum('total');

        // 5. Retorna a resposta em JSON
        return response()->json([
            'categories' => $categories,
            'series' => [
                ['name' => 'Despesas', 'data' => $seriesData]
            ],
            'total' => 'R$ ' . number_format($totalGeral, 2, ',', '.')
        ]);
    }

    /**
     * Get financial data for AJAX requests
     */
    public function getFinancialData(Request $request)
    {
        $activeCompanyId = session('active_company_id');
        if (!$activeCompanyId) {
            return response()->json(['error' => 'Empresa não selecionada'], 400);
        }

        $tipo = $request->input('tipo', 'receita'); // receita ou despesa
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);
        $status = $request->input('status', '');
        $fornecedor = $request->input('fornecedor', '');
        $conta = $request->input('conta', '');

        $query = ContasFinanceiras::with(['fornecedor', 'lancamentoPadrao'])
            ->where('tipo_financeiro', $tipo)
            ->whereMonth('data_primeiro_vencimento', $month)
            ->whereYear('data_primeiro_vencimento', $year);

        // Aplicar filtros
        if ($status) {
            $query->where('status_pagamento', $status);
        }

        if ($fornecedor) {
            $query->where('fornecedor_id', $fornecedor);
        }

        if ($conta) {
            $query->where('entidade_financeira_id', $conta);
        }

        $data = $query->get();

        // Calcular totais para cards
        $vencidos = $data->where('status_pagamento', 'vencido')->sum('valor');
        $vencemHoje = $data->where('status_pagamento', 'em aberto')
            ->where('data_primeiro_vencimento', Carbon::today()->format('Y-m-d'))
            ->sum('valor');
        $aVencer = $data->whereIn('status_pagamento', ['em aberto', 'pendente'])
            ->where('data_primeiro_vencimento', '>', Carbon::today())
            ->sum('valor');
        $pagos = $data->where('status_pagamento', 'pago')->sum('valor');
        $total = $data->sum('valor');

        return response()->json([
            'data' => $data,
            'cards' => [
                'vencidos' => $vencidos,
                'vencemHoje' => $vencemHoje,
                'aVencer' => $aVencer,
                'pagos' => $pagos,
                'total' => $total
            ]
        ]);
    }

    /**
     * Mark financial entries as paid
     */
    public function markAsPaid(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:contas_financeiras,id'
        ]);

        try {
            $ids = $request->input('ids');
            $dataPagamento = $request->input('data_pagamento', Carbon::today()->format('Y-m-d'));

            ContasFinanceiras::whereIn('id', $ids)->update([
                'status_pagamento' => 'pago',
                'data_pagamento' => $dataPagamento,
                'updated_by' => Auth::id(),
                'updated_by_name' => Auth::user()->name
            ]);

            return response()->json([
                'success' => true,
                'message' => count($ids) . ' registro(s) marcado(s) como pago(s)'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao marcar como pago: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export financial data
     */
    public function export(Request $request)
    {
        $request->validate([
            'tipo' => 'required|in:receita,despesa',
            'format' => 'required|in:pdf,excel,csv'
        ]);

        $tipo = $request->input('tipo');
        $format = $request->input('format');
        $ids = $request->input('ids', []);

        $query = ContasFinanceiras::with(['fornecedor', 'lancamentoPadrao'])
            ->where('tipo_financeiro', $tipo);

        if (!empty($ids)) {
            $query->whereIn('id', $ids);
        }

        $data = $query->get();

        // Aqui você implementaria a lógica de exportação
        // Por enquanto, retornamos os dados em JSON
        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => 'Exportação preparada para ' . strtoupper($format)
        ]);
    }

    /**
     * Delete financial entries
     */
    public function deleteEntries(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:contas_financeiras,id'
        ]);

        try {
            $ids = $request->input('ids');

            ContasFinanceiras::whereIn('id', $ids)->delete();

            return response()->json([
                'success' => true,
                'message' => count($ids) . ' registro(s) excluído(s) com sucesso'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get filter options for dropdowns
     */
    public function getFilterOptions(Request $request)
    {
        $activeCompanyId = session('active_company_id');

        $parceiros = \App\Models\Parceiro::orderBy('nome')->get(['id', 'nome']);
        $contas = EntidadeFinanceira::where('company_id', $activeCompanyId)
            ->orderBy('nome')
            ->get(['id', 'nome']);

        return response()->json([
            'parceiros' => $parceiros,
            'contas' => $contas
        ]);
    }

    /**
     * Retorna os detalhes de uma transação financeira do caixa para o drawer
     */
    public function getDetalhes($id)
    {
        $companyId = session('active_company_id');

        $transacao = TransacaoFinanceira::with([
                'lancamentoPadrao',
                'entidadeFinanceira',
                'costCenter',
                'modulos_anexos',
                'createdBy',
                'updatedBy',
                'recibo.address' // Carregar recibo com endereço
            ])
            ->where('company_id', $companyId)
            ->findOrFail($id);

        return response()->json([
            'id' => $transacao->id,
            'descricao' => $transacao->descricao,
            'tipo' => $transacao->tipo,
            'valor' => $transacao->valor,
            'data_competencia_formatada' => $transacao->data_competencia ? Carbon::parse($transacao->data_competencia)->format('d/m/Y') : null,
            'lancamento_padrao' => $transacao->lancamentoPadrao->description ?? null,
            'tipo_documento' => $transacao->tipo_documento,
            'numero_documento' => $transacao->numero_documento,
            'comprovacao_fiscal' => $transacao->comprovacao_fiscal ? 'Sim' : 'Não',
            'origem' => $transacao->origem,
            'entidade_financeira' => $transacao->entidadeFinanceira->nome ?? null,
            'centro_custo' => $transacao->costCenter->descricao ?? null,
            'historico_complementar' => $transacao->historico_complementar,
            'created_by_name' => $transacao->created_by_name ?? ($transacao->createdBy->name ?? null),
            'updated_by_name' => $transacao->updated_by_name ?? ($transacao->updatedBy->name ?? null),
            'created_at_formatado' => $transacao->created_at->format('d/m/Y H:i'),
            'updated_at_formatado' => $transacao->updated_at->format('d/m/Y H:i'),
            'recibo' => $transacao->recibo ? [
                'id' => $transacao->recibo->id,
                'nome' => $transacao->recibo->nome,
                'cpf_cnpj' => $transacao->recibo->cpf_cnpj,
                'referente' => $transacao->recibo->referente,
                'address' => $transacao->recibo->address ? [
                    'cep' => $transacao->recibo->address->cep,
                    'rua' => $transacao->recibo->address->rua,
                    'numero' => $transacao->recibo->address->numero,
                    'bairro' => $transacao->recibo->address->bairro,
                    'complemento' => $transacao->recibo->address->complemento,
                    'cidade' => $transacao->recibo->address->cidade,
                    'uf' => $transacao->recibo->address->uf,
                ] : null
            ] : null,
            'anexos' => $transacao->modulos_anexos->map(function($anexo) {
                return [
                    'nome' => $anexo->nome_arquivo,
                    'url' => $anexo->caminho_arquivo ? route('file', ['path' => $anexo->caminho_arquivo]) : ($anexo->link ?? '#')
                ];
            })
        ]);
    }
}
