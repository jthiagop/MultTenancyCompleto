<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Http\Requests\Financer\StoreTransacaoFinanceiraRequest;
use App\Models\Anexo;
use App\Models\Banco;
use App\Models\EntidadeFinanceira;
use App\Models\Financeiro\CostCenter;
use App\Models\Financeiro\ModulosAnexo;
use App\Models\Financeiro\TransacaoFinanceira;
use App\Models\LancamentoPadrao;
use App\Models\Movimentacao;
use App\Models\User;
use App\Services\TransacaoFinanceiraService;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Str;
use Flasher\Laravel\Facade\Flasher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class BancoController extends Controller
{
    protected $transacaoService;

    public function __construct(TransacaoFinanceiraService $transacaoService)
    {
        $this->transacaoService = $transacaoService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $valorEntradaBanco = Banco::getBancoEntrada();
        $ValorSaidasBanco = Banco::getBancoSaida();

        $entidadesBanco = Banco::getEntidadesBanco();


        $lps = LancamentoPadrao::all();


        return view('app.financeiro.index', [
            'valorEntradaBanco' => $valorEntradaBanco,
            'ValorSaidasBanco' => $ValorSaidasBanco,
            'lps' => $lps,
            'entidadesBanco' => $entidadesBanco,
        ]);
    }


    public function list(Request $request)
    {
        // Obter o valor da aba ativa da URL, se presente
        $activeTab = $request->input('tab', 'overview'); // 'overview' é o padrão caso não haja o parâmetro 'tab'

        // Suponha que você já tenha o ID da empresa disponível
        $companyId = session('active_company_id'); // ou $companyId = 1; se o ID for fixo

        if (!$companyId) {
            return redirect()->route('dashboard')->with('error', 'Por favor, selecione uma empresa para visualizar os dados.');
        }


        $perPage = (int) $request->input('per_page', 25);
        $perPage = max(5, min($perPage, 200)); // limites úteis

        $lps = LancamentoPadrao::all();

        // Filtrar as entradas e saídas pelos bancos relacionados à empresa
        list($somaEntradas, $somaSaida) = Banco::getBanco();

        // 🟢 Obtém a data do mês selecionado ou usa o mês atual
        $mesSelecionado = $request->input('mes', Carbon::now()->month);
        $anoSelecionado = $request->input('ano', Carbon::now()->year);
        // 🟢 Obtém os dados do gráfico usando o Service
        $dadosGrafico = $this->transacaoService->getDadosGrafico($mesSelecionado, $anoSelecionado);

        $total  = EntidadeFinanceira::getValorTotalEntidadeBC();

        $entidadesBanco = EntidadeFinanceira::forActiveCompany() // 1. Usa o scope para filtrar pela empresa
            ->where('tipo', 'banco')  // 2. Adiciona o filtro específico para bancos
            ->with('bankStatements')  // 3. (Opcional, mas recomendado) Otimiza a consulta
            ->get();

        // Filtrar as transações com origem "Banco"
        // Transações com anexos relacionados
        $transacoes = TransacaoFinanceira::with('modulos_anexos')
            ->where(function ($query) {
                $query->where('origem', 'Conciliação Bancária')
                    ->orWhere('origem', 'Banco');
            })
            ->where('company_id', $companyId)
            ->paginate($perPage);


        $valorEntrada = Banco::getBancoEntrada();
        $ValorSaidas = Banco::getBancoSaida();
        $centrosAtivos = CostCenter::forActiveCompany()->get();


        // Lista de prioridades para os status de conciliação
        $prioridadeStatus = ['divergente', 'em análise', 'parcial', 'pendente', 'ajustado', 'ignorado', 'ok'];

        // Calcula o status final de conciliação para cada entidade bancária
        foreach ($entidadesBanco as $entidade) {
            // Obtém os status de conciliação de todos os extratos bancários da entidade
            $statusConciliação = $entidade->bankStatements->pluck('status_conciliacao')->toArray();

            // Define o status final com base na prioridade
            $statusFinal = 'ok'; // Assume "OK" por padrão
            foreach ($prioridadeStatus as $status) {
                if (in_array($status, $statusConciliação)) {
                    $statusFinal = $status;
                    break; // Para no primeiro status encontrado seguindo a prioridade
                }
            }
            // Armazena o status final na entidade para uso na View
            $entidade->status_conciliacao = ucfirst($statusFinal);
        }

        // Mapeia classes CSS para os status
        $statusClasses = [
            'ok' => 'badge-light-success',
            'pendente' => 'badge-light-warning',
            'parcial' => 'badge-light-primary',
            'divergente' => 'badge-light-danger',
            'ignorado' => 'badge-light-secondary',
            'ajustado' => 'badge-light-info',
            'em análise' => 'badge-light-dark',
        ];

        // Adiciona a classe CSS correspondente a cada entidade
        foreach ($entidadesBanco as $entidade) {
            $entidade->badge_class = $statusClasses[strtolower($entidade->status_conciliacao)] ?? 'badge-light-secondary';
        }


        // 🟢 Retorna a View com todos os dados
        return view('app.financeiro.banco.list', array_merge([
            'valorEntrada' => $valorEntrada,
            'ValorSaidas' => $ValorSaidas,
            'total' => $total,
            'lps' => $lps,
            'entidadesBanco' => $entidadesBanco,
            'activeTab' => $activeTab,
            'transacoes' => $transacoes,
            'centrosAtivos' => $centrosAtivos,
            'mesSelecionado' => $mesSelecionado,
            'anoSelecionado' => $anoSelecionado,
            'perPage' => $perPage,
        ], $dadosGrafico));
    }

    /**
     * Retorna dados para os gráficos de transações bancárias
     */
    public function getChartData(Request $request)
    {
        Log::info('getChartData chamado - INÍCIO');
        
        $companyId = session('active_company_id');
        
        Log::info('getChartData chamado', [
            'company_id' => $companyId,
            'mes' => $request->input('mes'),
            'ano' => $request->input('ano'),
            'entidade_id' => $request->input('entidade_id')
        ]);
        
        if (!$companyId) {
            Log::error('Empresa não encontrada na sessão');
            return response()->json(['error' => 'Empresa não encontrada'], 400);
        }

        // Parâmetros de filtro
        $mes = $request->input('mes', Carbon::now()->month);
        $ano = $request->input('ano', Carbon::now()->year);
        $entidadeId = $request->input('entidade_id'); // Filtro opcional por banco específico

        // Construir query base
        $query = TransacaoFinanceira::where('company_id', $companyId)
            ->where(function ($q) {
                $q->where('origem', 'Conciliação Bancária')
                  ->orWhere('origem', 'Banco');
            })
            ->whereYear('data_competencia', $ano)
            ->whereMonth('data_competencia', $mes);

        // Filtrar por entidade específica se fornecida
        if ($entidadeId) {
            $query->where('entidade_id', $entidadeId);
        }

        $transacoes = $query->orderBy('data_competencia')->get();
        
        Log::info('Transações encontradas', [
            'total' => $transacoes->count(),
            'primeiras_5' => $transacoes->take(5)->toArray()
        ]);

        // Agrupar por dia do mês
        $diasNoMes = Carbon::create($ano, $mes, 1)->daysInMonth;
        $dados = [];

        for ($dia = 1; $dia <= $diasNoMes; $dia++) {
            $dataAtual = Carbon::create($ano, $mes, $dia);
            $dataFormatada = $dataAtual->format('Y-m-d');
            
            $transacoesDia = $transacoes->filter(function ($transacao) use ($dataFormatada) {
                return Carbon::parse($transacao->data_competencia)->format('Y-m-d') === $dataFormatada;
            });

            $entradas = $transacoesDia->where('tipo', 'entrada')->sum('valor');
            $saidas = $transacoesDia->where('tipo', 'saida')->sum('valor');

            $dados[] = [
                'dia' => $dia,
                'data' => $dataAtual->format('d/m'),
                'entradas' => (float) $entradas,
                'saidas' => (float) $saidas,
                'saldo_dia' => (float) ($entradas - $saidas)
            ];
        }

        // Calcular totais do período
        $totalEntradas = $transacoes->where('tipo', 'entrada')->sum('valor');
        $totalSaidas = $transacoes->where('tipo', 'saida')->sum('valor');
        $saldoTotal = $totalEntradas - $totalSaidas;

        $response = [
            'dados' => $dados,
            'totais' => [
                'entradas' => (float) $totalEntradas,
                'saidas' => (float) $totalSaidas,
                'saldo' => (float) $saldoTotal
            ],
            'periodo' => [
                'mes' => $mes,
                'ano' => $ano,
                'mes_nome' => Carbon::create($ano, $mes, 1)->locale('pt_BR')->monthName
            ]
        ];
        
        Log::info('Dados do gráfico preparados', [
            'total_dados' => count($dados),
            'totais' => $response['totais'],
            'primeiros_3_dados' => array_slice($dados, 0, 3)
        ]);

        return response()->json($response);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $company = User::getCompanyName();
        $lps = LancamentoPadrao::all();


        return view('app.financeiro.banco.create', [
            'lps' => $lps,
            'company' => $company,

        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTransacaoFinanceiraRequest $request)
    {
        // Recupera a companhia associada ao usuário autenticado
        $subsidiary = User::getCompany();

        if (!$subsidiary) {
            return redirect()->back()->with('error', 'Companhia não encontrada.');
        }

        // Validação dos dados é automática com StoreTransacaoFinanceiraRequest, não é necessário duplicar validações aqui

        // Processa os dados validados
        $validatedData = $request->validated();

        // Converte o valor e a data para os formatos adequados
        $validatedData['data_competencia'] = Carbon::createFromFormat('d-m-Y', $validatedData['data_competencia'])->format('Y-m-d');
        $validatedData['valor'] = str_replace(',', '.', str_replace('.', '', $validatedData['valor']));

        // Adiciona informações padrão
        $validatedData['company_id'] = $subsidiary->company_id;
        $validatedData['created_by'] = Auth::id();
        $validatedData['created_by_name'] = Auth::user()->name;
        $validatedData['updated_by'] = Auth::id();
        $validatedData['updated_by_name'] = Auth::user()->name;

        // 1) Chama o método movimentacao() e guarda o retorno
        $movimentacao = $this->movimentacao($validatedData);

        // 2) Atribui o ID retornado à chave movimentacao_id de $validatedData
        $validatedData['movimentacao_id'] = $movimentacao->id;

        // 3) Cria o registro na tabela transacoes_financeiras usando o ID que acabamos de obter
        $caixa = TransacaoFinanceira::create($validatedData);

        // Verifica e processa lançamentos padrão
        $this->processarLancamentoPadrao($validatedData);

        // Processa anexos, se existirem
        $this->processarAnexos($request, $caixa);

        // Adiciona mensagem de sucesso
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
            $validatedData['origem'] = 'Banco';
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




    public function update(Request $request, $id)
    {
        try {
            // Obtenha a empresa do usuário autenticado
            $subsidiaryId = User::getCompany();

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
                    Flasher::addError($error);
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
            $banco->update($validatedData);

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
     * Display the specified resource.
     */
    public function show(Banco $banco)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // Obter o ID da empresa do usuário autenticado
        $companyId = Auth::user()->company_id;

        // Buscar o banco com o ID e verificar se pertence à empresa do usuário
        $banco = TransacaoFinanceira::with('modulos_anexos')
            ->where('company_id', $companyId) // Filtrar pelo company_id do usuário
            ->findOrFail($id);

        // Garantir que apenas dados da mesma empresa sejam carregados
        $lps = LancamentoPadrao::all();
        $entidadesBanco = Banco::getEntidadesBanco();
        $centrosAtivos = CostCenter::where('company_id', $companyId)->get();

        // Retornar a view com os dados filtrados
        return view(
            'app.financeiro.banco.edit',
            [
                'banco' => $banco,
                'lps' => $lps,
                'entidadesBanco' => $entidadesBanco,
                'centrosAtivos' => $centrosAtivos,
            ]
        );
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
                $entidade->saldo_atual -= $movimentacao->valor;
            } else {
                // Se a movimentação era uma saída, adiciona o valor ao saldo atual
                $entidade->saldo_atual += $movimentacao->valor;
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
            return redirect()->route('banco.list');
        } catch (\Exception $e) {
            // 9) Em caso de erro, registra log e retorna com mensagem de erro
            Log::error('Erro ao excluir transação: ' . $e->getMessage());
            Flasher::addError('Erro ao excluir transação: ' . $e->getMessage());
            return redirect()->back();
        }
    }
}
