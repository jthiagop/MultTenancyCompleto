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
use App\Models\LancamentoPadrao;
use App\Models\Movimentacao;
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
        ));
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


        // Entidades do tipo 'Caixa' da empresa ativa
        $entidades = EntidadeFinanceira::where('company_id', $activeCompanyId)->where('tipo', 'caixa')->get();

        // Entidades do tipo 'Banco' da empresa ativa (parece repetido, mas corrigindo a lógica)
        $entidadesBanco = EntidadeFinanceira::where('company_id', $activeCompanyId)->where('tipo', 'banco')->get();
        // Somando entradas e saídas do Caixa da empresa ativa
        $somaEntradas = TransacaoFinanceira::where('company_id', $activeCompanyId)->where('origem', 'Caixa')->where('tipo', 'entrada')->sum('valor');
        $somaSaidas = TransacaoFinanceira::where('company_id', $activeCompanyId)->where('origem', 'Caixa')->where('tipo', 'saida')->sum('valor');



        // Total das Entidades Financeiras da empresa ativa
        $total = EntidadeFinanceira::where('company_id', $activeCompanyId)
            ->where('tipo', 'caixa') // Mantendo o filtro por 'caixa' que você tinha
            ->sum('saldo_atual');

        // Transações de Caixa da empresa ativa (sua consulta já estava quase correta, só precisava usar a variável certa)
        $transacoes = TransacaoFinanceira::where('origem', 'Caixa')
            ->where('company_id', $activeCompanyId) // Usando a variável correta
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
            'company' => $company,
            'entidades' => $entidades,
            'entidadesBanco' => $entidadesBanco,
            'activeTab' => $activeTab,
            'centrosAtivos' => $centrosAtivos,
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
        $validatedData['data_competencia'] = Carbon::createFromFormat('d/m/Y', $validatedData['data_competencia'])->format('Y-m-d');
        $validatedData['valor'] = str_replace(',', '.', str_replace('.', '', $validatedData['valor']));

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
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                // Nome original do arquivo (sem caminho completo)
                $nomeOriginal = $file->getClientOriginalName();
                $anexoName = time() . '_' . $file->getClientOriginalName();
                $anexoPath = $file->storeAs('anexos', $anexoName, 'public');

                ModulosAnexo::create([
                    'anexavel_id'   => $caixa->id,                   // ID da transacao_financeira
                    'anexavel_type' => TransacaoFinanceira::class,   // caminho da classe do Model
                    'nome_arquivo'  => $nomeOriginal,
                    'caminho_arquivo' => $anexoPath,
                    'tamanho_arquivo' => $file->getSize(),
                    'tipo_arquivo'  => $file->getMimeType() ?? '',  // se quiser
                    'created_by'    => Auth::id(),
                    'created_by_name' => Auth::user()->name,
                    // etc., se tiver mais campos
                ]);
            }
        }
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

            // Validação dos dados
            $validator = Validator::make($request->all(), [
                'data_competencia' => 'required|date',
                'descricao' => 'required|string|max:255',
                'valor' => 'required|regex:/^\d+(\.\d{1,2})?$/',
                'tipo' => 'required|in:entrada,saida',
                'lancamento_padrao_id' => 'required|exists:lancamento_padraos,id',
                'cost_center_id' => 'required|exists:cost_centers,id',
                'tipo_documento' => 'required|string|max:255',
                'numero_documento' => 'nullable|string|max:50',
                'historico_complementar' => 'nullable|string|max:500',
                'comprovacao_fiscal' => 'required|boolean',
                'anexos.*' => 'nullable|file|mimes:jpeg,png,pdf|max:2048',
                'entidade_id' => 'required|exists:entidades_financeiras,id', // Valida entidade
            ], [
                'valor.regex' => 'O valor deve estar no formato correto (exemplo: 1234.56).',
                'tipo.in' => 'O tipo deve ser "entrada" ou "saída".',
            ]);

            // Se a validação falhar
            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    \Flasher\Laravel\Facade\Flasher::addError($error);
                }
                return redirect()->back()->withInput();
            }

            // Validação bem-sucedida
            $validatedData = $validator->validated();

            // Busca o registro no banco de dados
            $banco = TransacaoFinanceira::findOrFail($id);
            $movimentacao = Movimentacao::findOrFail($banco->movimentacao_id);

            // Ajusta o saldo da entidade antes de atualizar os valores
            // 1) Entidade antiga vinculada à movimentação
            $oldEntidade = EntidadeFinanceira::findOrFail($movimentacao->entidade_id);

            // Reverte o impacto do lançamento antigo no saldo da entidade
            // 2) Reverter saldo antigo
            if ($movimentacao->tipo === 'entrada') {
                $oldEntidade->saldo_atual -= $movimentacao->valor;
            } else {
                $oldEntidade->saldo_atual += $movimentacao->valor;
            }
            $oldEntidade->save();

            // 3) Atualiza a movimentação (agora ela aponta para a nova entidade e novo valor)
            $movimentacao->update([
                'entidade_id' => $validatedData['entidade_id'],
                'tipo'        => $validatedData['tipo'],
                'valor'       => $validatedData['valor'],
                'descricao'   => $validatedData['descricao'],
                'updated_by'  => Auth::user()->id,
            ]);

            // 4) Entidade nova escolhida no form
            $newEntidade = EntidadeFinanceira::findOrFail($validatedData['entidade_id']);

            // 5) Aplicar o valor atualizado na nova entidade
            if ($validatedData['tipo'] === 'entrada') {
                $newEntidade->saldo_atual += $validatedData['valor'];
            } else {
                $newEntidade->saldo_atual -= $validatedData['valor'];
            }
            $newEntidade->save();

            // Atualiza os dados do banco
            $validatedData['movimentacao_id'] = $movimentacao->id; // Mantém o vínculo com a movimentação
            $transacao->update($validatedData);

            // Verifica se há anexos enviados
            if ($request->hasFile('anexos')) {
                foreach ($request->file('anexos') as $anexo) {
                    // Gera um nome único para o anexo
                    $anexoName = Str::uuid() . '_' . $anexo->getClientOriginalName();

                    // Salva o arquivo no diretório público
                    $anexoPath = $anexo->storeAs('anexos', $anexoName, 'public');

                    // Cria o registro do anexo no banco de dados
                    Anexo::create([
                        'banco_id' => $banco->id,
                        'nome_arquivo' => $anexoName,
                        'caminho_arquivo' => $anexoPath,
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                    ]);
                }
            }

            // Adiciona mensagem de sucesso
            Flasher::addSuccess('Lançamento atualiazado com sucesso!');
            return redirect()->back()->with('message', 'Atualizado com sucesso!');
        } catch (\Exception $e) {
            // Log de erro e mensagem de retorno
            Log::error('Erro ao atualizar movimentação: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Erro ao atualizar: ' . $e->getMessage());
        }
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
        
        $fornecedores = \App\Models\Fornecedor::orderBy('nome')->get(['id', 'nome']);
        $contas = EntidadeFinanceira::where('company_id', $activeCompanyId)
            ->orderBy('nome')
            ->get(['id', 'nome']);

        return response()->json([
            'fornecedores' => $fornecedores,
            'contas' => $contas
        ]);
    }
}
