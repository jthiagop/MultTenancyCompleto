<?php

namespace App\Http\Controllers\App;

use App\Helpers\BrowsershotHelper;
use App\Helpers\TransacaoFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Financer\StoreTransacaoFinanceiraRequest;
use App\Models\Anexo;
use App\Models\Banco;
use App\Models\EntidadeFinanceira;
use App\Models\Financeiro\BankStatement;
use App\Models\Financeiro\CostCenter;
use App\Models\Financeiro\ModulosAnexo;
use App\Models\Financeiro\Recorrencia;
use App\Models\Financeiro\TransacaoFinanceira;
use App\Models\Financeiro\TransacaoFracionamento;
use App\Models\FormasPagamento;
use App\Models\Parceiro;
use App\Models\HorarioMissa;
use App\Models\LancamentoPadrao;
use App\Models\Movimentacao;
use App\Models\User;
use App\Services\RecurrenceService;
use App\Services\TransacaoFinanceiraService;
use App\Services\EntidadeFinanceiraService;
use App\Services\ConciliacaoSuggestionService;
use App\Support\Money;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Str;
use Flasher\Laravel\Facade\Flasher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Spatie\Browsershot\Browsershot;


class BancoController extends Controller
{
    protected TransacaoFinanceiraService $transacaoService;
    protected TransacaoFormatter $formatter;
    protected RecurrenceService $recurrenceService;
    protected EntidadeFinanceiraService $entidadeFinanceiraService;
    protected ConciliacaoSuggestionService $suggestionService;

    public function __construct(
        TransacaoFinanceiraService $transacaoService,
        TransacaoFormatter $formatter,
        RecurrenceService $recurrenceService,
        EntidadeFinanceiraService $entidadeFinanceiraService,
        ConciliacaoSuggestionService $suggestionService
    ) {
        $this->transacaoService = $transacaoService;
        $this->formatter = $formatter;
        $this->recurrenceService = $recurrenceService;
        $this->entidadeFinanceiraService = $entidadeFinanceiraService;
        $this->suggestionService = $suggestionService;
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
        $formasPagamento = FormasPagamento::where('ativo', true)->orderBy('nome')->get();
        $parceiros = Parceiro::forActiveCompany()->orderBy('nome')->get();


        return view('app.financeiro.index', [
            'valorEntradaBanco' => $valorEntradaBanco,
            'ValorSaidasBanco' => $ValorSaidasBanco,
            'lps' => $lps,
            'entidadesBanco' => $entidadesBanco,
            'formasPagamento' => $formasPagamento,
            'parceiros' => $parceiros,
            'fornecedores' => $parceiros,
        ]);
    }


    public function getSugestao(Request $request)
    {
        try {
            $companyId = session('active_company_id');
            $parceiroId = $request->get('parceiro_id');
            $descricao = $request->get('descricao');
            $valor = $request->get('valor');

            // Converte valor monetário se vier formatado
            if ($valor) {
                $money = Money::fromHumanInput($valor);
                $valor = $money->getAmount();
            }

            $sugestao = $this->suggestionService->sugerirPorDados(
                (int) $companyId,
                $descricao,
                $parceiroId ? (int) $parceiroId : null,
                $valor ? (float) $valor : null
            );

            return response()->json($sugestao);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar sugestão: ' . $e->getMessage());
            return response()->json(['error' => 'Falha ao processar sugestão'], 500);
        }
    }

    public function list(Request $request)
    {
        // Obter o valor da aba ativa da URL, se presente
        $activeTab = $request->input('tab', 'contas_receber'); // 'contas_receber' é o padrão

        // Tabs válidas (removidas: 'overview', 'bancos', 'relatorios' e 'registros')
        $validTabs = ['contas_receber', 'contas_pagar', 'extrato', 'conciliacao', 'lancamento'];

        // Se a tab não for válida, redirecionar para a tab padrão
        if (!in_array($activeTab, $validTabs)) {
            return redirect()->route('banco.list', ['tab' => 'contas_receber']);
        }

        // Suponha que você já tenha o ID da empresa disponível
        $companyId = session('active_company_id'); // ou $companyId = 1; se o ID for fixo

        if (!$companyId) {
            return redirect()->route('dashboard')->with('error', 'Por favor, selecione uma empresa para visualizar os dados.');
        }


        $perPage = (int) $request->input('per_page', 50); // Aumentado de 25 para 50
        $perPage = max(5, min($perPage, 200)); // limites úteis

        $lps = LancamentoPadrao::all();
        $formasPagamento = FormasPagamento::where('ativo', true)->orderBy('nome')->get();
        $parceiros = Parceiro::forActiveCompany()->orderBy('nome')->get();

        // Filtrar as entradas e saídas pelos bancos relacionados à empresa
        list($somaEntradas, $somaSaida) = Banco::getBanco();

        // 🟢 Obtém a data do mês selecionado ou usa o mês atual
        $mesSelecionado = $request->input('mes', Carbon::now()->month);
        $anoSelecionado = $request->input('ano', Carbon::now()->year);
        // 🟢 Obtém os dados do gráfico usando o Service
        $dadosGrafico = $this->transacaoService->getDadosGrafico($mesSelecionado, $anoSelecionado);

        // 🟢 Obtém os dados do fluxo de caixa anual (entradas e saídas por mês)
        $dadosFluxoCaixaAnual = $this->transacaoService->getDadosFluxoCaixaAnual($anoSelecionado);

        $total  = EntidadeFinanceira::getValorTotalEntidadeBC();

        // Buscar saldo total das entidades do tipo 'caixa'
        $totalCaixa = EntidadeFinanceira::forActiveCompany()
            ->where('tipo', 'caixa')
            ->sum('saldo_atual');

        // Buscar entidades financeiras (banco e caixa) para o filtro de conta
        $entidadesFinanceiras = EntidadeFinanceira::forActiveCompany()
            ->whereIn('tipo', ['banco', 'caixa'])
            // REMOVIDO: ->with('bankStatements')  // Causa timeout com muitos registros
            ->get();

        // Manter $entidadesBanco separado para lógica específica de banco
        $entidadesBanco = $entidadesFinanceiras->where('tipo', 'banco')->values();

        // Buscar entidades do tipo 'caixa'
        $entidadesCaixa = EntidadeFinanceira::forActiveCompany()
            ->where('tipo', 'caixa')
            ->get();

        // Preparar entidades para o side-card (merge, cálculos de variação, etc)
        $todasEntidades = $this->entidadeFinanceiraService->prepararEntidadesParaSideCard($entidadesBanco, $entidadesCaixa);

        // Entidades para o relatório de prestação de contas
        $entidades = EntidadeFinanceira::forActiveCompany() // 1. Usa o scope para filtrar pela empresa
            ->where('tipo', 'banco')  // 2. Adiciona o filtro específico para bancos
            // REMOVIDO: ->with('bankStatements')  // Causa timeout com muitos registros
            ->get();

        // Filtrar as transações de banco através do relacionamento com entidades_financeiras
        // Transações com anexos relacionados
        $transacoes = TransacaoFinanceira::with('modulos_anexos')
            ->whereHas('entidadeFinanceira', function ($query) {
                $query->where('tipo', 'banco');
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
            // Usa query direto para evitar carregar todos os registros na memória
            $statusConciliação = \App\Models\Financeiro\BankStatement::where('entidade_financeira_id', $entidade->id)
                ->distinct()
                ->pluck('status_conciliacao')
                ->toArray();

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

        // Verifica se existem horários de missa cadastrados para a empresa ativa
        $hasHorariosMissas = HorarioMissa::where('company_id', $companyId)->exists();

        // Preparar accountOptions para as tabs (incluindo banco e caixa)
        $accountOptions = $entidadesFinanceiras->map(function($entidade) {
            return [
                'id' => $entidade->id, 
                'nome' => $entidade->nome,
                'tipo' => $entidade->tipo
            ];
        })->toArray();

        // 🟢 Configurações específicas para cada tab
        $tabConfigs = [
            'overview' => [
                'title' => 'Visão Geral',
                'showFilters' => true,
                'showStats' => true,
            ],
            'contas_receber' => [
                'title' => 'Contas a Receber',
                'tipo' => 'entrada',
                'showFilters' => true,
                'showStats' => true,
                'accountOptions' => $accountOptions,
            ],
            'contas_pagar' => [
                'title' => 'Contas a Pagar',
                'tipo' => 'saida',
                'showFilters' => true,
                'showStats' => true,
                'accountOptions' => $accountOptions,
            ],
            'extrato' => [
                'title' => 'Extrato',
                'showFilters' => true,
                'showStats' => true,
                'accountOptions' => $accountOptions,
            ],
            'conciliacao' => [
                'title' => 'Conciliação',
                'showFilters' => true,
                'showStats' => false,
                'accountOptions' => $accountOptions,
            ],
            'lancamento' => [
                'title' => 'Lançamento',
                'showFilters' => false,
                'showStats' => false,
            ],
        ];

        // 🟢 Retorna a View com todos os dados
        return view('app.financeiro.banco.list', array_merge([
            'valorEntrada' => $valorEntrada,
            'ValorSaidas' => $ValorSaidas,
            'total' => $total,
            'totalCaixa' => $totalCaixa,
            'lps' => $lps,
            'formasPagamento' => $formasPagamento,
            'parceiros' => $parceiros,
            'entidadesBanco' => $entidadesBanco,
            'entidadesCaixa' => $entidadesCaixa,
            'todasEntidades' => $todasEntidades ?? collect(), // Entidades preparadas para side-card
            'activeTab' => $activeTab,
            'transacoes' => $transacoes,
            'centrosAtivos' => $centrosAtivos,
            'mesSelecionado' => $mesSelecionado,
            'anoSelecionado' => $anoSelecionado,
            'perPage' => $perPage,
            'entidades' => $entidades,
            'hasHorariosMissas' => $hasHorariosMissas,
            'tabConfigs' => $tabConfigs,
            'dadosFluxoCaixaAnual' => $dadosFluxoCaixaAnual,
            'accountOptions' => $accountOptions,
            'fornecedores' => $parceiros,
        ], $dadosGrafico ));
    }

    /**
     * Retorna dados de resumo para atualização via AJAX
     * Usado pelo EventBus para atualizar tabs e cards sem recarregar a página
     */
    public function getSummary(Request $request)
    {
        $companyId = session('active_company_id');
        
        if (!$companyId) {
            return response()->json(['error' => 'Empresa não selecionada'], 400);
        }
        
        // Parse das datas do request
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $tab = $request->input('tab', 'contas_receber'); // Detecta qual tab está ativa
        
        // Determina se é extrato
        $isExtrato = $tab === 'extrato';
        
        // Busca transações do período
        $query = TransacaoFinanceira::forActiveCompany()
            ->whereHas('entidadeFinanceira', function ($q) {
                $q->whereIn('tipo', ['banco', 'caixa']); // Inclui banco e caixa
            });
        
        // Aplica filtro de data baseado na tab
        if ($isExtrato) {
            // Para extrato, filtra por data_competencia
            $query->whereBetween('data_competencia', [$startDate, $endDate]);
        } else {
            // Para outras tabs, filtra por data_competencia
            $query->whereBetween('data_competencia', [$startDate, $endDate]);
        }
        
        $transacoes = $query->get();
        
        // Formata valores
        $formatMoney = fn($value) => 'R$ ' . number_format($value, 2, ',', '.');
        
        if ($isExtrato) {
            // Stats específicas para extrato - usa método centralizado
            $stats = $this->calculateExtratoStats($startDate, $endDate);
            
            if (!$stats) {
                return response()->json(['error' => 'Erro ao calcular estatísticas'], 500);
            }
            
            return response()->json([
                'tabs' => [
                    ['key' => 'receitas_aberto', 'value' => $formatMoney($stats['receitas_aberto'])],
                    ['key' => 'receitas_realizadas', 'value' => $formatMoney($stats['receitas_realizadas'])],
                    ['key' => 'despesas_aberto', 'value' => $formatMoney($stats['despesas_aberto'])],
                    ['key' => 'despesas_realizadas', 'value' => $formatMoney($stats['despesas_realizadas'])],
                    ['key' => 'total', 'value' => $formatMoney($stats['total'])],
                ],
                'meta' => [
                    'tab' => 'extrato',
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'updated_at' => now()->format('H:i:s'),
                ]
            ]);
        }
        
        // Stats para contas a receber/pagar (comportamento original)
        $totalEmAberto = $transacoes->where('situacao', 'em_aberto')->sum('valor');
        $totalAtrasado = $transacoes->where('situacao', 'atrasado')->sum('valor');
        $totalPago = $transacoes->where('situacao', 'pago')->sum('valor');
        $totalRecebido = $transacoes->where('situacao', 'recebido')->sum('valor');
        $totalPrevisto = $transacoes->where('situacao', 'previsto')->sum('valor');
        
        // Totais por tipo
        $totalReceitas = $transacoes->where('tipo', 'entrada')->sum('valor');
        $totalDespesas = $transacoes->where('tipo', 'saida')->sum('valor');
        $saldo = $totalReceitas - $totalDespesas;
        
        return response()->json([
            'tabs' => [
                ['key' => 'em_aberto', 'value' => $formatMoney($totalEmAberto)],
                ['key' => 'atrasado', 'value' => $formatMoney($totalAtrasado)],
                ['key' => 'pago', 'value' => $formatMoney($totalPago)],
                ['key' => 'recebido', 'value' => $formatMoney($totalRecebido)],
                ['key' => 'previsto', 'value' => $formatMoney($totalPrevisto)],
            ],
            'sideCard' => [
                'total_receitas' => $formatMoney($totalReceitas),
                'total_despesas' => $formatMoney($totalDespesas),
                'saldo' => $formatMoney($saldo),
            ],
            'meta' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'updated_at' => now()->format('H:i:s'),
            ]
        ]);
    }

    /**
     * Retorna dados para os gráficos de transações bancárias
     */
    public function getChartData(Request $request)
    {
        Log::info('getChartData chamado - INÍCIO - TENANT CONTEXT');

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

        // Construir query base - filtrar por entidades do tipo 'banco'
        $query = TransacaoFinanceira::where('company_id', $companyId)
            ->whereHas('entidadeFinanceira', function ($q) {
                $q->where('tipo', 'banco');
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
     * Retorna dados para o gráfico de fluxo de banco por intervalo de datas
     */
    public function getFluxoBancoChartData(Request $request)
    {
        Log::info('getFluxoBancoChartData chamado', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
        ]);

        $companyId = session('active_company_id');

        // Fallback: Tentar obter a empresa do usuário se não estiver na sessão
        if (!$companyId) {
            $userCompany = User::getCompany();
            if ($userCompany) {
                $companyId = $userCompany->company_id;
                Log::info('getFluxoBancoChartData - Usando fallback User::getCompany()', ['company_id' => $companyId]);
            }
        }

        Log::info('getFluxoBancoChartData - companyId final', ['company_id' => $companyId]);

        if (!$companyId) {
            Log::error('getFluxoBancoChartData - Empresa não encontrada na sessão ou no usuário');
            return response()->json(['error' => 'Empresa não encontrada'], 400);
        }

        // Parâmetros de filtro por intervalo de datas
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $groupBy = $request->input('group_by', 'day'); // day, week, month
        $limit = (int) $request->input('limit', 30); // Limite de períodos a retornar
        $offset = (int) $request->input('offset', 0); // Offset para paginação

        // Se não fornecido, usa o período padrão (últimos 30 dias)
        if (!$startDate || !$endDate) {
            $endDate = Carbon::now()->format('Y-m-d');
            $startDate = Carbon::now()->subDays(29)->format('Y-m-d');
        }

        // Converter strings para Carbon
        $start = Carbon::createFromFormat('Y-m-d', $startDate)->startOfDay();
        $end = Carbon::createFromFormat('Y-m-d', $endDate)->endOfDay();

        // Determinar granularidade automática baseada no período
        $diasDiferenca = $start->diffInDays($end);
        if ($groupBy === 'auto') {
            if ($diasDiferenca <= 31) {
                $groupBy = 'day';
            } elseif ($diasDiferenca <= 90) {
                $groupBy = 'week';
            } else {
                $groupBy = 'month';
            }
        }

        // Agregação no banco de dados usando groupBy
        switch ($groupBy) {
            case 'week':
                $dateFormat = DB::raw("DATE_FORMAT(COALESCE(data_competencia, data), '%Y-%u') as periodo");
                $dateGroup = DB::raw("DATE_FORMAT(COALESCE(data_competencia, data), '%Y-%u')");
                $dateLabel = DB::raw("CONCAT('Sem ', DATE_FORMAT(COALESCE(data_competencia, data), '%u/%Y')) as label");
                break;
            case 'month':
                $dateFormat = DB::raw("DATE_FORMAT(COALESCE(data_competencia, data), '%Y-%m') as periodo");
                $dateGroup = DB::raw("DATE_FORMAT(COALESCE(data_competencia, data), '%Y-%m')");
                $dateLabel = DB::raw("DATE_FORMAT(COALESCE(data_competencia, data), '%m/%Y') as label");
                break;
            case 'day':
            default:
                $dateFormat = DB::raw("DATE(COALESCE(data_competencia, data)) as periodo");
                $dateGroup = DB::raw("DATE(COALESCE(data_competencia, data))");
                $dateLabel = DB::raw("DATE_FORMAT(COALESCE(data_competencia, data), '%d/%m') as label");
                break;
        }

        // Contar total de períodos disponíveis
        $totalPeriodos = Movimentacao::where('company_id', $companyId)
            ->whereHas('entidade', function ($q) {
                $q->where('tipo', 'banco');
            })
            ->where(function($q) use ($start, $end) {
                $q->whereBetween('data_competencia', [$start, $end])
                  ->orWhere(function($subQ) use ($start, $end) {
                      $subQ->whereNull('data_competencia')
                           ->whereBetween('data', [$start, $end]);
                  });
            })
            ->select($dateFormat)
            ->groupBy($dateGroup)
            ->get()
            ->count();

        // Agregar dados no banco de dados com limite e offset
        $dadosAgregados = Movimentacao::where('company_id', $companyId)
            ->whereHas('entidade', function ($q) {
                $q->where('tipo', 'banco');
            })
            ->where(function($q) use ($start, $end) {
                $q->whereBetween('data_competencia', [$start, $end])
                  ->orWhere(function($subQ) use ($start, $end) {
                      $subQ->whereNull('data_competencia')
                           ->whereBetween('data', [$start, $end]);
                  });
            })
            ->select(
                $dateFormat,
                $dateLabel,
                DB::raw('SUM(CASE WHEN tipo = "entrada" THEN valor ELSE 0 END) as entradas'),
                DB::raw('SUM(CASE WHEN tipo = "saida" THEN valor ELSE 0 END) as saidas')
            )
            ->groupBy($dateGroup)
            ->orderByRaw('periodo DESC') // Ordenar do mais recente para o mais antigo
            ->limit($limit)
            ->offset($offset)
            ->get()
            ->reverse(); // Reverter para exibir do mais antigo para o mais recente no gráfico

        // Preparar dados para o gráfico
        $dadosPorPeriodo = [];
        foreach ($dadosAgregados as $item) {
            $dadosPorPeriodo[] = [
                'data' => $item->label,
                'data_completa' => $item->periodo,
                'entradas' => (float) $item->entradas,
                'saidas' => (float) $item->saidas
            ];
        }

        // Calcular totais do período (agregação direta no banco)
        $totais = Movimentacao::where('company_id', $companyId)
            ->whereHas('entidade', function ($q) {
                $q->where('tipo', 'banco');
            })
            ->where(function($q) use ($start, $end) {
                $q->whereBetween('data_competencia', [$start, $end])
                  ->orWhere(function($subQ) use ($start, $end) {
                      $subQ->whereNull('data_competencia')
                           ->whereBetween('data', [$start, $end]);
                  });
            })
            ->select(
                DB::raw('SUM(CASE WHEN tipo = "entrada" THEN valor ELSE 0 END) as total_entradas'),
                DB::raw('SUM(CASE WHEN tipo = "saida" THEN valor ELSE 0 END) as total_saidas')
            )
            ->first();

        $totalEntradas = (float) ($totais->total_entradas ?? 0);
        $totalSaidas = (float) ($totais->total_saidas ?? 0);
        $saldoTotal = $totalEntradas - $totalSaidas;

        // Preparar dados para o gráfico (arrays separados)
        $categorias = array_column($dadosPorPeriodo, 'data');
        $dadosEntradas = array_column($dadosPorPeriodo, 'entradas');
        $dadosSaidas = array_column($dadosPorPeriodo, 'saidas');

        $response = [
            'categorias' => $categorias,
            'entradas' => $dadosEntradas,
            'saidas' => $dadosSaidas,
            'totais' => [
                'entradas' => (float) $totalEntradas,
                'saidas' => (float) $totalSaidas,
                'saldo' => (float) $saldoTotal
            ],
            'periodo' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'group_by' => $groupBy,
                'dias_diferenca' => $diasDiferenca
            ],
            'paginacao' => [
                'total' => $totalPeriodos,
                'limit' => $limit,
                'offset' => $offset,
                'has_more' => ($offset + $limit) < $totalPeriodos,
                'next_offset' => $offset + $limit
            ]
        ];

        return response()->json($response);
    }

    /**
     * Calcula estatísticas para as tabs (vencidos, hoje, a_vencer, recebidos/pagos, total)
     */
    public function getStatsData(Request $request)
    {
        $companyId = session('active_company_id');

        if (!$companyId) {
            return response()->json([
                'vencidos' => '0,00',
                'hoje' => '0,00',
                'a_vencer' => '0,00',
                'recebidos' => '0,00',
                'total' => '0,00'
            ]);
        }

        $tipo = $request->input('tipo', 'entrada'); // entrada ou saida
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $entidadeId = $request->input('entidade_id'); // Filtro de conta

        // Se não fornecido, usa o mês atual
        if (!$startDate || !$endDate) {
            $startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
            $endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
        }

        $start = Carbon::createFromFormat('Y-m-d', $startDate)->startOfDay();
        $end = Carbon::createFromFormat('Y-m-d', $endDate)->endOfDay();
        $hoje = Carbon::now()->startOfDay();

        // Query base
        $query = TransacaoFinanceira::whereHas('entidadeFinanceira', function ($q) {
                $q->whereIn('tipo', ['banco', 'caixa']);
            })
            ->where('company_id', $companyId)
            ->where('tipo', $tipo)
            // Excluir transações PAI (parceladas) - elas são apenas containers
            ->where('situacao', '!=', 'parcelado');

        // Aplicar filtro de conta se fornecido
        if ($entidadeId) {
            if (is_array($entidadeId)) {
                // Múltiplas contas selecionadas
                $query->whereIn('entidade_id', $entidadeId);
            } else {
                // Uma conta selecionada
                $query->where('entidade_id', $entidadeId);
            }
        }

        // Vencidos: usar método reutilizável
        $vencidos = $this->aplicarFiltroVencidos(clone $query, $hoje, $start, $end)->sum('valor');

        // Vencem hoje: usar método reutilizável
        $hojeCount = 0;
        if ($hoje->between($start, $end)) {
            $hojeCount = $this->aplicarFiltroHoje(clone $query, $hoje, $start, $end)->sum('valor');
        }

        // A vencer: usar método reutilizável
        $aVencer = $this->aplicarFiltroAVencer(clone $query, $hoje, $start, $end)->sum('valor');

        // Recebidos/Pagos: situacao = 'pago' ou valor_pago >= valor
        // IMPORTANTE: Considerar fracionamentos quando existirem
        // Recebidos/Pagos: situacao = 'pago' ou 'recebido' + data_vencimento no período
        $situacaoPaga = $tipo === 'entrada' ? 'recebido' : 'pago';
        
        $recebidos = (clone $query)
            ->where('situacao', $situacaoPaga)
            ->where(function($q) use ($start, $end) {
                $q->whereBetween('data_vencimento', [$start, $end])
                  ->orWhere(function($subQ) use ($start, $end) {
                      $subQ->whereNull('data_vencimento')
                           ->whereBetween('data_competencia', [$start, $end]);
                  });
            })
            ->sum('valor');

        // Verificar se é extrato (filtra por data_pagamento)
        $isExtrato = $request->input('tab') === 'extrato' || $request->input('is_extrato') === 'true';

        if ($isExtrato) {
            // =====================================================
            // ESTATÍSTICAS ESPECÍFICAS PARA EXTRATO
            // Usa método centralizado
            // =====================================================
            
            $stats = $this->calculateExtratoStats($startDate, $endDate, $entidadeId);
            
            if (!$stats) {
                return response()->json([
                    'receitas_aberto' => '0,00',
                    'receitas_realizadas' => '0,00',
                    'despesas_aberto' => '0,00',
                    'despesas_realizadas' => '0,00',
                    'total' => '0,00'
                ]);
            }

            $response = [
                'receitas_aberto' => number_format((float) $stats['receitas_aberto'], 2, ',', '.'),
                'receitas_realizadas' => number_format((float) $stats['receitas_realizadas'], 2, ',', '.'),
                'despesas_aberto' => number_format((float) $stats['despesas_aberto'], 2, ',', '.'),
                'despesas_realizadas' => number_format((float) $stats['despesas_realizadas'], 2, ',', '.'),
                'total' => number_format((float) $stats['total'], 2, ',', '.'),
                'saldo_anterior' => number_format((float) ($stats['saldo_anterior'] ?? 0), 2, ',', '.'),
                'saldo_anterior_raw' => (float) ($stats['saldo_anterior'] ?? 0),
            ];
        } else {
            // Total do período: todas as transações com data_vencimento OU data_competencia dentro do período
            $total = (clone $query)
                ->where(function($q) use ($start, $end) {
                    $q->whereBetween('data_vencimento', [$start, $end])
                      ->orWhere(function($subQ) use ($start, $end) {
                          $subQ->whereNull('data_vencimento')
                               ->whereBetween('data_competencia', [$start, $end]);
                      });
                })
                ->sum('valor');

            $response = [
                'vencidos' => number_format((float) $vencidos, 2, ',', '.'), // Valores já estão em DECIMAL
                'hoje' => number_format((float) $hojeCount, 2, ',', '.'),
                'a_vencer' => number_format((float) $aVencer, 2, ',', '.'),
                'total' => number_format((float) $total, 2, ',', '.')
            ];

            // Debug log
            \Log::info('[STATS] Calculando stats para contas a receber/pagar', [
                'tipo' => $tipo,
                'vencidos_raw' => $vencidos,
                'hoje_raw' => $hojeCount,
                'a_vencer_raw' => $aVencer,
                'recebidos_raw' => $recebidos,
                'total_raw' => $total,
                'hoje_between' => $hoje->between($start, $end),
                'hoje_date' => $hoje->format('Y-m-d'),
                'start_date' => $start->format('Y-m-d'),
                'end_date' => $end->format('Y-m-d'),
            ]);

            // Para entrada usa 'recebidos', para saida usa 'pagos'
            if ($tipo === 'entrada') {
                $response['recebidos'] = number_format((float) $recebidos, 2, ',', '.'); // Valores já estão em DECIMAL
            } else {
                $response['pagos'] = number_format((float) $recebidos, 2, ',', '.');
            }
        }

        return response()->json($response);
    }

    /**
     * Fornece os dados para a DataTable com processamento do lado do servidor (server-side)
     */
    public function getTransacoesData(Request $request)
    {
        \Log::info('getTransacoesData - Início', [
            'draw' => $request->input('draw'),
            'start' => $request->input('start'),
            'length' => $request->input('length'),
            'search' => $request->input('search'),
            'tipo' => $request->input('tipo'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date')
        ]);

        $companyId = session('active_company_id');

        if (!$companyId) {
            \Log::warning('getTransacoesData - Company ID não encontrado');
            return response()->json([
                'draw' => intval($request->input('draw', 1)),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => []
            ]);
        }

        // Query base - filtrar transações de banco e caixa
        $query = TransacaoFinanceira::with(['modulos_anexos', 'lancamentoPadrao', 'fracionamentos', 'recorrencia', 'parceiro', 'entidadeFinanceira'])
            ->whereHas('entidadeFinanceira', function ($q) {
                $q->whereIn('tipo', ['banco', 'caixa']);
            })
            ->where('company_id', $companyId)
            // Excluir transações PAI (parceladas) - elas são apenas containers
            // Apenas as transações FILHAS (parcelas individuais) devem aparecer na listagem
            ->where('situacao', '!=', 'parcelado');

        // Contagem total de registros antes de qualquer filtro
        $recordsTotal = $query->count();

        // Aplicar busca geral (do campo de pesquisa do DataTables)
        if ($request->filled('search') && !empty($request->search['value'])) {
            $search = $request->search['value'];
            
            // Verificar se a busca é um valor monetário (ex: "150,00" ou "150.00" ou "1.500,00")
            $searchNumeric = null;
            $cleanSearch = preg_replace('/[^\d,.]/', '', $search);
            if (!empty($cleanSearch)) {
                // Converter formato brasileiro (1.500,00) para decimal
                $cleanSearch = str_replace('.', '', $cleanSearch); // Remove separador de milhar
                $cleanSearch = str_replace(',', '.', $cleanSearch); // Converte vírgula decimal
                if (is_numeric($cleanSearch)) {
                    $searchNumeric = (float) $cleanSearch;
                }
            }
            
            $query->where(function($q) use ($search, $searchNumeric) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('descricao', 'like', "%{$search}%")
                  ->orWhere('tipo_documento', 'like', "%{$search}%")
                  ->orWhere('numero_documento', 'like', "%{$search}%")
                  ->orWhere('origem', 'like', "%{$search}%")
                  ->orWhere('historico_complementar', 'like', "%{$search}%")
                  ->orWhereHas('lancamentoPadrao', function($subQ) use ($search) {
                      $subQ->where('description', 'like', "%{$search}%");
                  })
                  ->orWhereHas('parceiro', function($subQ) use ($search) {
                      $subQ->where('nome', 'like', "%{$search}%");
                  })
                  ->orWhereHas('entidadeFinanceira', function($subQ) use ($search) {
                      $subQ->where('nome', 'like', "%{$search}%");
                  });
                
                // Busca por valor (com tolerância para arredondamento)
                if ($searchNumeric !== null) {
                    $tolerance = 0.01; // Tolerância de 1 centavo
                    $q->orWhereBetween('valor', [$searchNumeric - $tolerance, $searchNumeric + $tolerance]);
                }
            });
        }

        // Aplicar filtro de tipo (entrada/saida)
        if ($request->filled('tipo') && $request->tipo !== 'all' && $request->tipo !== '') {
            $query->where('tipo', $request->tipo);
        }

        // Aplicar filtro de situação (em_aberto, atrasado, previsto, pago_parcial, pago, desconsiderado)
        if ($request->filled('situacao') && $request->situacao !== 'all' && $request->situacao !== '') {
            $query->where('situacao', $request->situacao);
        }

        // Aplicar filtro de entidade_id (conta) - pode ser array ou valor único
        if ($request->filled('entidade_id')) {
            $entidadeIds = $request->input('entidade_id');
            if (is_array($entidadeIds) && count($entidadeIds) > 0) {
                // Remove valores vazios
                $entidadeIds = array_filter($entidadeIds, function($value) {
                    return !empty($value);
                });
                if (count($entidadeIds) > 0) {
                    $query->whereIn('entidade_id', $entidadeIds);
                }
            } elseif (!is_array($entidadeIds) && !empty($entidadeIds)) {
                $query->where('entidade_id', $entidadeIds);
            }
        }

        // Filtro por status da tab (vencidos, hoje, a_vencer, recebidos, total)
        $status = $request->input('status');
        $hoje = Carbon::now()->startOfDay();

        // Preparar variáveis de período para uso nos filtros
        $startDate = null;
        $endDate = null;
        $isContasReceberPagar = false;
        $isExtrato = false;

        if ($request->filled('start_date') && $request->filled('end_date')) {
            try {
                $startDate = Carbon::createFromFormat('Y-m-d', $request->start_date)->startOfDay();
                $endDate = Carbon::createFromFormat('Y-m-d', $request->end_date)->endOfDay();
                $tipo = $request->input('tipo');
                $isContasReceberPagar = in_array($tipo, ['entrada', 'saida']);
                // Detectar se estamos na aba "extrato" via parâmetro da request
                $isExtrato = $request->input('tab') === 'extrato' || $request->input('is_extrato') === 'true';
            } catch (\Exception $e) {
                Log::warning('Erro ao processar filtro de data no DataTables', ['error' => $e->getMessage()]);
            }
        }

        // Aplicar filtro de status da tab
        if ($status && $status !== 'total') {
            switch ($status) {
                // =====================================================
                // FILTROS ESPECÍFICOS PARA EXTRATO
                // Usam data_competencia + excluem desconsiderado/parcelado/agendado
                // para manter consistência com ExtratoController (PDF)
                // =====================================================
                case 'receitas_aberto':
                    // Receitas em Aberto: entrada + não recebido + competência no período
                    if ($startDate && $endDate && $isExtrato) {
                        $query->whereNotIn('situacao', [\App\Enums\SituacaoTransacao::DESCONSIDERADO, \App\Enums\SituacaoTransacao::PARCELADO])
                              ->where('agendado', false)
                              ->where('tipo', 'entrada')
                              ->whereBetween('data_competencia', [$startDate, $endDate])
                              ->where('situacao', '!=', 'recebido');
                    }
                    break;

                case 'receitas_realizadas':
                    // Receitas Realizadas: entrada + recebido + competência no período
                    if ($startDate && $endDate && $isExtrato) {
                        $query->whereNotIn('situacao', [\App\Enums\SituacaoTransacao::DESCONSIDERADO, \App\Enums\SituacaoTransacao::PARCELADO])
                              ->where('agendado', false)
                              ->where('tipo', 'entrada')
                              ->whereBetween('data_competencia', [$startDate, $endDate])
                              ->where('situacao', 'recebido');
                    }
                    break;

                case 'despesas_aberto':
                    // Despesas em Aberto: saída + não pago + competência no período
                    if ($startDate && $endDate && $isExtrato) {
                        $query->whereNotIn('situacao', [\App\Enums\SituacaoTransacao::DESCONSIDERADO, \App\Enums\SituacaoTransacao::PARCELADO])
                              ->where('agendado', false)
                              ->where('tipo', 'saida')
                              ->whereBetween('data_competencia', [$startDate, $endDate])
                              ->whereNotIn('situacao', ['pago']);
                    }
                    break;

                case 'despesas_realizadas':
                    // Despesas Realizadas: saída + pago + competência no período
                    if ($startDate && $endDate && $isExtrato) {
                        $query->whereNotIn('situacao', [\App\Enums\SituacaoTransacao::DESCONSIDERADO, \App\Enums\SituacaoTransacao::PARCELADO])
                              ->where('agendado', false)
                              ->where('tipo', 'saida')
                              ->whereBetween('data_competencia', [$startDate, $endDate])
                              ->where('situacao', 'pago');
                    }
                    break;

                // =====================================================
                // FILTROS PARA CONTAS A RECEBER/PAGAR
                // =====================================================
                case 'vencidos':
                    \Log::info('[FILTRO] Aplicando filtro VENCIDOS', [
                        'hoje' => $hoje->format('Y-m-d'),
                        'startDate' => $startDate?->format('Y-m-d'),
                        'endDate' => $endDate?->format('Y-m-d'),
                        'isContasReceberPagar' => $isContasReceberPagar
                    ]);
                    // Usar método reutilizável
                    $this->aplicarFiltroVencidos($query, $hoje, $startDate, $endDate, $isContasReceberPagar);
                    break;

                case 'hoje':
                    \Log::info('[FILTRO] Aplicando filtro HOJE', [
                        'hoje' => $hoje->format('Y-m-d'),
                        'startDate' => $startDate?->format('Y-m-d'),
                        'endDate' => $endDate?->format('Y-m-d')
                    ]);
                    // Usar método reutilizável
                    $this->aplicarFiltroHoje($query, $hoje, $startDate, $endDate);
                    break;

                case 'a_vencer':
                    \Log::info('[FILTRO] Aplicando filtro A_VENCER', [
                        'hoje' => $hoje->format('Y-m-d'),
                        'startDate' => $startDate?->format('Y-m-d'),
                        'endDate' => $endDate?->format('Y-m-d'),
                        'isContasReceberPagar' => $isContasReceberPagar
                    ]);
                    // Usar método reutilizável
                    $this->aplicarFiltroAVencer($query, $hoje, $startDate, $endDate, $isContasReceberPagar);
                    break;

                case 'recebidos':
                    \Log::info('[FILTRO] Aplicando filtro RECEBIDOS', [
                        'tipo' => 'entrada',
                        'startDate' => $startDate?->format('Y-m-d'),
                        'endDate' => $endDate?->format('Y-m-d')
                    ]);
                    // Recebidos: tipo='entrada' + situacao='recebido' + data_vencimento/competencia no período
                    $query->where('tipo', 'entrada')
                          ->where('situacao', 'recebido');
                    
                    // Aplicar filtro de período
                    if ($startDate && $endDate) {
                        $query->where(function($q) use ($startDate, $endDate) {
                            $q->whereBetween('data_vencimento', [$startDate, $endDate])
                              ->orWhere(function($subQ) use ($startDate, $endDate) {
                                  $subQ->whereNull('data_vencimento')
                                       ->whereBetween('data_competencia', [$startDate, $endDate]);
                              });
                        });
                    }
                    break;

                case 'pagos':
                    // Pagos: tipo='saida' + situacao='pago' + data_vencimento/competencia no período
                    $query->where('tipo', 'saida')
                          ->where('situacao', 'pago');
                    
                    // Aplicar filtro de período
                    if ($startDate && $endDate) {
                        $query->where(function($q) use ($startDate, $endDate) {
                            $q->whereBetween('data_vencimento', [$startDate, $endDate])
                              ->orWhere(function($subQ) use ($startDate, $endDate) {
                                  $subQ->whereNull('data_vencimento')
                                       ->whereBetween('data_competencia', [$startDate, $endDate]);
                              });
                        });
                    }
                    break;

            }
        } else {
            // Para contas a receber (entrada) e contas a pagar (saida), quando status = 'total' ou não especificado,
            // mostrar TODAS as transações (incluindo pagas) dentro do período
            // Deve incluir transações que têm data_vencimento dentro do período
            if ($startDate && $endDate) {
                if ($isExtrato) {
                    // Para Extrato: mesmos filtros do ExtratoController (PDF)
                    // Usa data_competencia, exclui desconsiderado/parcelado/agendado
                    $query->whereNotIn('situacao', [\App\Enums\SituacaoTransacao::DESCONSIDERADO, \App\Enums\SituacaoTransacao::PARCELADO])
                          ->where('agendado', false)
                          ->whereBetween('data_competencia', [$startDate, $endDate]);
                } elseif ($isContasReceberPagar) {
                    // Para contas a receber/pagar, filtrar por data_vencimento OU data_competencia (se vencimento for null)
                    $query->where(function($q) use ($startDate, $endDate) {
                        $q->whereBetween('data_vencimento', [$startDate, $endDate])
                          ->orWhere(function($subQ) use ($startDate, $endDate) {
                              $subQ->whereNull('data_vencimento')
                                   ->whereBetween('data_competencia', [$startDate, $endDate]);
                          });
                    });
                } else {
                    // Para outras tabs, filtrar por data_competencia
                    $query->whereBetween('data_competencia', [$startDate, $endDate]);
                }
            }
        }

        // Contagem de registros após aplicar os filtros
        $recordsFiltered = $query->count();
        
        // Log do SQL gerado para debug
        \Log::info('[QUERY] SQL gerado após filtros', [
            'sql' => $query->toSql(),
            'bindings' => $query->getBindings(),
            'recordsFiltered' => $recordsFiltered
        ]);

        // Aplicar ordenação
        $orderColumn = 'id'; // ID por padrão
        $orderDir = 'desc';

        if ($request->has('order') && count($request->order)) {
            $order = $request->order[0];
            $columnIndex = (int) $order['column'];
            $orderDir = $order['dir'];

            // Mapear índice da coluna para campo do banco
            // Verifica se é contas a receber/pagar ou registros normais
            $isContasReceberPagar = $request->filled('tipo') && ($request->tipo === 'entrada' || $request->tipo === 'saida');

            if ($isContasReceberPagar) {
                // Mapeamento para contas a receber/pagar (com vencimento e situação)
                $columnMap = [
                    0 => 'checkbox', // Checkbox não é ordenável
                    1 => 'data_vencimento',
                    2 => 'descricao',
                    3 => 'valor',
                    4 => 'valor_pago',
                    5 => 'situacao',
                    6 => 'origem',
                    7 => 'actions'
                ];
            } else {
                // Mapeamento padrão (para registros)
            $columnMap = [
                0 => 'checkbox', // Checkbox não é ordenável
                1 => 'data_competencia',
                2 => 'comprovacao_fiscal',
                3 => 'descricao',
                4 => 'tipo',
                5 => 'valor',
                6 => 'origem',
                7 => 'actions'
            ];
            }

            $orderColumn = $columnMap[$columnIndex] ?? 'id';

            // Campos que não devem ser ordenados (HTML)
            $nonOrderableColumns = ['checkbox', 'comprovacao_fiscal', 'descricao', 'anexos', 'actions', 'situacao', 'origem'];
            if (in_array($orderColumn, $nonOrderableColumns)) {
                $orderColumn = 'id'; // Fallback para ID
            }

            // Se data_vencimento não existir, usar data_competencia como fallback
            if ($orderColumn === 'data_vencimento' && !$isContasReceberPagar) {
                $orderColumn = 'data_competencia';
            }
        }

        $query->orderBy($orderColumn, $orderDir);

        // Aplicar paginação
        $start = $request->input('start', 0);
        $length = $request->input('length', 50);
        $transacoes = $query->skip($start)->take($length)->get();

        // Verifica se é contas a receber/pagar ou registros normais (antes do map)
        $isContasReceberPagar = $request->filled('tipo') && ($request->tipo === 'entrada' || $request->tipo === 'saida');
        $isExtrato = $request->input('tab') === 'extrato' || $request->input('is_extrato') === 'true';

        // Função auxiliar para formatar a origem com conta (se for banco)
        $formatarOrigem = function($transacao) {
            $origem = $transacao->origem ?? '-';
            if (strtolower($origem) === 'banco' && $transacao->entidadeFinanceira && $transacao->entidadeFinanceira->conta) {
                return $origem . ' - ' . $transacao->entidadeFinanceira->conta;
            }
            return $origem;
        };

        // Formatar os dados para a resposta JSON
        $data = $transacoes->map(function($transacao) use ($request, $isContasReceberPagar, $isExtrato, $formatarOrigem) {
            // Formatar descrição com informação de recorrência ou parcelamento (se houver)
            $descricaoTexto = e($transacao->descricao);

            // Verifica se a transação faz parte de uma recorrência
            if ($transacao->recorrencia->isNotEmpty()) {
                $recorrencia = $transacao->recorrencia->first();
                $pivot = $recorrencia->pivot;
                $numeroOcorrencia = $pivot->numero_ocorrencia ?? 1;
                $totalOcorrencias = $recorrencia->total_ocorrencias ?? 1;

                // Formato: "1/12 - Nana Banana" com ícone
                $descricaoTexto = '<i class="bi bi-repeat text-primary me-2" title="Lançamento recorrente"></i>' . $numeroOcorrencia . '/' . $totalOcorrencias . ' - ' . $descricaoTexto;
            }
            // Verifica se a transação é uma parcela (tem parent_id)
            elseif ($transacao->parent_id) {
                // Busca os dados do parcelamento para exibir número da parcela
                $parcelamento = \App\Models\Financeiro\Parcelamento::where('transacao_parcela_id', $transacao->id)->first();
                if ($parcelamento) {
                    $numeroParcela = $parcelamento->numero_parcela;
                    $totalParcelas = $parcelamento->total_parcelas;
                    // Formato: "1/3 - Descrição" com ícone de parcelamento
                    $descricaoTexto = '<i class="bi bi-signpost-split text-primary me-2" title="Lançamento parcelado"></i>' . $numeroParcela . '/' . $totalParcelas . ' - ' . $descricaoTexto;
                } else {
                    // Se não encontrou parcelamento, apenas mostra o ícone
                    $descricaoTexto = '<i class="bi bi-signpost-split text-primary me-2" title="Lançamento parcelado"></i>' . $descricaoTexto;
                }
            }

            $descricaoHtml = '<div class="fw-bold"><a href="#" onclick="abrirDrawerTransacao(' . $transacao->id . '); return false;" class="text-gray-800 text-hover-primary">' . $descricaoTexto . '</a></div>';
            
            // Linha secundária: Badge do Lançamento Padrão + Nome do Fornecedor
            $subInfo = [];
            
            // Lançamento Padrão com Badge colorido (verde para entrada, vermelho para saída)
            if ($transacao->lancamentoPadrao) {
                $badgeClass = $transacao->tipo === 'entrada' ? 'badge-light-success' : 'badge-light-danger';
                $subInfo[] = '<span class="badge py-1 px-2 ' . $badgeClass . ' fs-8">' . e($transacao->lancamentoPadrao->description) . '</span>';
            }
            
            // Nome do fornecedor/parceiro
            if ($transacao->parceiro) {
                $subInfo[] = '<span class="text-muted"><i class="bi bi-person me-1"></i>' . e($transacao->parceiro->nome) . '</span>';
            }
            
            // Junta as informações com separador
            if (!empty($subInfo)) {
                $descricaoHtml .= '<div class="d-flex align-items-center gap-2 mt-1">' . implode(' <span class="text-muted">/</span> ', $subInfo) . '</div>';
            }

            // Formatar ações usando classe Helper
            // Mostrar "Informar pagamento" apenas se a transação não estiver completamente paga
            // Priorizar a situação da transação sobre o valor_pago
            $situacaoValue = $transacao->situacao instanceof \App\Enums\SituacaoTransacao 
                ? $transacao->situacao->value 
                : $transacao->situacao;
            
            $isPago = in_array($situacaoValue, ['pago', 'recebido']);

            $actionsHtml = $this->formatter->formatActions($transacao, [
                'showInformarPagamento' => !$isPago
            ]);


            if ($isExtrato) {
                // Formatação para Extrato
                // Formatar situação
                $situacaoBadge = '';
                if ($transacao->situacao) {
                    $badgeClasses = [
                        'em_aberto' => 'badge-light-warning',
                        'atrasado' => 'badge-light-danger',
                        'previsto' => 'badge-light-info',
                        'pago_parcial' => 'badge-light-primary',
                        'pago' => 'badge-light-success',
                        'recebido' => 'badge-light-success',
                        'desconsiderado' => 'badge-light-secondary'
                    ];
                    // Converter enum para string antes de usar como chave
                    $situacaoValue = $transacao->situacao instanceof \App\Enums\SituacaoTransacao 
                        ? $transacao->situacao->value 
                        : $transacao->situacao;
                    $badgeClass = $badgeClasses[$situacaoValue] ?? 'badge-light-secondary';
                    $situacaoLabel = ucfirst(str_replace('_', ' ', $situacaoValue));
                    $situacaoBadge = '<div class="badge fw-bold py-2 px-3 ' . $badgeClass . '">' . $situacaoLabel . '</div>';
                } else {
                    $situacaoBadge = '<div class="badge fw-bold py-2 px-3 badge-light-secondary">Em Aberto</div>';
                }


                // Data de competência
                $dataExibicao = '-';
                if ($transacao->data_competencia) {
                    try {
                        $dataExibicao = Carbon::parse($transacao->data_competencia)->format('d/m/Y');
                    } catch (\Exception $e) {
                        $dataExibicao = '-';
                    }
                }

                // Checkbox para seleção (adicionar classe row-check para identificação)
                $checkboxHtml = '<div class="form-check form-check-sm form-check-custom form-check-solid justify-content-center">
                    <input class="form-check-input row-check" type="checkbox" value="' . $transacao->id . '" />
                </div>';

                // Valor (já está em DECIMAL, não precisa dividir por 100)
                $valorFormatado = 'R$ ' . number_format((float) $transacao->valor, 2, ',', '.');

                // Saldo (calculado com base no saldo da entidade ou valor pago)
                // Para extrato, podemos usar o valor_pago como saldo
                $saldo = $transacao->valor_pago ?? $transacao->valor;
                $saldoFormatado = 'R$ ' . number_format((float) $saldo, 2, ',', '.');

                return [
                    $checkboxHtml,
                    $dataExibicao,
                    $descricaoHtml,
                    $situacaoBadge,
                    $valorFormatado,
                    $saldoFormatado,
                    $actionsHtml
                ];
            } elseif ($isContasReceberPagar) {
                // Formatar situação
                $situacaoBadge = '';
                if ($transacao->situacao) {
                    $badgeClasses = [
                        'em_aberto' => 'badge-light-warning',
                        'atrasado' => 'badge-light-danger',
                        'previsto' => 'badge-light-info',
                        'pago_parcial' => 'badge-light-primary',
                        'pago' => 'badge-light-success',
                        'recebido' => 'badge-light-success',
                        'desconsiderado' => 'badge-light-secondary'
                    ];
                    // Converter Enum para string
                    $situacaoValue = $transacao->situacao instanceof \App\Enums\SituacaoTransacao 
                        ? $transacao->situacao->value 
                        : $transacao->situacao;
                    
                    $badgeClass = $badgeClasses[$situacaoValue] ?? 'badge-light-secondary';
                    $situacaoLabel = ucfirst(str_replace('_', ' ', $situacaoValue));
                    $situacaoBadge = '<div class="badge fw-bold py-2 px-3 ' . $badgeClass . '">' . $situacaoLabel . '</div>';
                } else {
                    $situacaoBadge = '<div class="badge fw-bold py-2 px-3 badge-light-secondary">Em Aberto</div>';
                }

                // Determinar qual data usar (vencimento ou competência)
                // Formata com Y maiúsculo para mostrar 4 dígitos do ano
                // Usa createFromFormat para garantir interpretação correta (Y-m-d do banco)
                $dataExibicao = '-';
                if ($transacao->data_vencimento) {
                    try {
                        // Se já está em formato Y-m-d do banco, usa createFromFormat
                        if (is_string($transacao->data_vencimento) && strpos($transacao->data_vencimento, '-') !== false) {
                            $dataExibicao = Carbon::createFromFormat('Y-m-d', $transacao->data_vencimento)->format('d/m/Y');
                        } else {
                            $dataExibicao = Carbon::parse($transacao->data_vencimento)->format('d/m/Y');
                        }
                    } catch (\Exception $e) {
                        $dataExibicao = '-';
                    }
                } elseif ($transacao->data_competencia) {
                    try {
                        // Se já está em formato Y-m-d do banco, usa createFromFormat
                        if (is_string($transacao->data_competencia) && strpos($transacao->data_competencia, '-') !== false) {
                            $dataExibicao = Carbon::createFromFormat('Y-m-d', $transacao->data_competencia)->format('d/m/Y');
                        } else {
                            $dataExibicao = Carbon::parse($transacao->data_competencia)->format('d/m/Y');
                        }
                    } catch (\Exception $e) {
                        $dataExibicao = '-';
                    }
                }

                // Checkbox para seleção (adicionar classe row-check para identificação)
                $checkboxHtml = '<div class="form-check form-check-sm form-check-custom form-check-solid justify-content-center">
                    <input class="form-check-input row-check" type="checkbox" value="' . $transacao->id . '" />
                </div>';

                // Calcular valor "A pagar"
                // Se houver fracionamento do tipo "em_aberto", usar o valor desse fracionamento
                // Caso contrário, calcular: valor - valor_pago
                $valorAPagar = 0;
                $fracionamentoEmAberto = $transacao->fracionamentos->where('tipo', 'em_aberto')->first();
                if ($fracionamentoEmAberto) {
                    $valorAPagar = $fracionamentoEmAberto->valor;
                } else {
                    // Se não há fracionamento, calcular o que falta pagar
                    $valorAPagar = max(0, $transacao->valor - ($transacao->valor_pago ?? 0));
                }

                return [
                    $checkboxHtml,
                    $dataExibicao,
                    $descricaoHtml,
                    'R$ ' . number_format((float) $transacao->valor, 2, ',', '.'),
                    'R$ ' . number_format((float) $valorAPagar, 2, ',', '.'),
                    $situacaoBadge,
                    $formatarOrigem($transacao),
                    $actionsHtml
                ];
            } else {
                // Retorno padrão para registros
                // Checkbox para seleção (adicionar classe row-check para identificação)
                $checkboxHtml = '<div class="form-check form-check-sm form-check-custom form-check-solid justify-content-center">
                    <input class="form-check-input row-check" type="checkbox" value="' . $transacao->id . '" />
                </div>';

                return [
                    $checkboxHtml,
                    $transacao->data_competencia
                        ? Carbon::parse($transacao->data_competencia)->format('d/m/y')
                        : '-',
                    // Verifica se tem anexos ativos diretamente do relacionamento
                    $transacao->modulos_anexos->where('status', 'ativo')->isNotEmpty()
                        ? '<i class="fas fa-check-circle text-success" title="Comprovação Fiscal"></i>'
                        : '<i class="bi bi-x-circle-fill text-danger" title="Sem Comprovação Fiscal"></i>',
                    $descricaoHtml,
                    '<div class="badge fw-bold py-2 px-3 ' . ($transacao->tipo == 'entrada' ? 'badge-success' : 'badge-danger') . '">' . ucfirst($transacao->tipo) . '</div>',
                    'R$ ' . number_format((float) $transacao->valor, 2, ',', '.'),
                    $formatarOrigem($transacao),
                    $actionsHtml
                ];
            }
        });

        // Retorna a resposta no formato que a DataTable espera
        $response = [
            'draw' => intval($request->input('draw', 1)),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data->toArray()
        ];

        \Log::info('getTransacoesData - Resposta', [
            'draw' => $response['draw'],
            'recordsTotal' => $response['recordsTotal'],
            'recordsFiltered' => $response['recordsFiltered'],
            'data_count' => count($response['data'])
        ]);

        return response()->json($response);
    }

    /**
     * Retorna os detalhes de uma transação financeira para o drawer
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
                'recibo.address', // Carregar recibo com endereço
                'parceiro.address', // Carregar parceiro com endereço para recibo
                'parcelas.entidadeFinanceira', // Carregar parcelas com entidade
            ])
            ->where('company_id', $companyId)
            ->findOrFail($id);

        return response()->json([
            'id' => $transacao->id,
            'descricao' => $transacao->descricao,
            'tipo' => $transacao->tipo,
            'valor' => (float) $transacao->valor, // Valor já está em DECIMAL
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
            'parceiro' => $transacao->parceiro ? [
                'id' => $transacao->parceiro->id,
                'nome' => $transacao->parceiro->nome,
                'nome_fantasia' => $transacao->parceiro->nome_fantasia,
                'cpf_cnpj' => $transacao->parceiro->documento,
                'telefone' => $transacao->parceiro->telefone,
                'email' => $transacao->parceiro->email,
                'address' => $transacao->parceiro->address ? [
                    'cep' => $transacao->parceiro->address->cep,
                    'rua' => $transacao->parceiro->address->rua,
                    'numero' => $transacao->parceiro->address->numero,
                    'bairro' => $transacao->parceiro->address->bairro,
                    'complemento' => $transacao->parceiro->address->complemento,
                    'cidade' => $transacao->parceiro->address->cidade,
                    'uf' => $transacao->parceiro->address->uf,
                ] : null
            ] : null,
            'anexos' => $transacao->modulos_anexos->map(function($anexo) {
                return [
                    'nome' => $anexo->nome_arquivo,
                    'url' => $anexo->caminho_arquivo ? route('file', ['path' => $anexo->caminho_arquivo]) : ($anexo->link ?? '#')
                ];
            }),
            // Dados de parcelamento (para transação filha)
            'parcela_info' => $transacao->parent_id ? (function() use ($transacao) {
                $parcelamento = \App\Models\Financeiro\Parcelamento::where('transacao_parcela_id', $transacao->id)->first();
                if ($parcelamento) {
                    return [
                        'numero_parcela' => $parcelamento->numero_parcela,
                        'total_parcelas' => $parcelamento->total_parcelas,
                        'parent_id' => $transacao->parent_id,
                        'parent_descricao' => $transacao->parent?->descricao ?? null,
                    ];
                }
                return null;
            })() : null,
            // Dados de parcelamento (para transação pai)
            'is_parcelado' => $transacao->parcelas->isNotEmpty(),
            'parent_id' => $transacao->parent_id,
            'parcelas' => $transacao->parcelas->map(function ($parcela) {
                return [
                    'numero_parcela' => $parcela->numero_parcela,
                    'total_parcelas' => $parcela->total_parcelas,
                    'data_vencimento' => $parcela->data_vencimento ? $parcela->data_vencimento->format('d/m/Y') : '-',
                    'valor' => (float) $parcela->valor,
                    'situacao' => $parcela->situacao,
                    'valor_pago' => (float) ($parcela->valor_pago ?? 0),
                    'descricao' => $parcela->descricao,
                    'entidade_nome' => $parcela->entidadeFinanceira?->nome ?? '-',
                ];
            })->values()->toArray(),
        ]);
    }


    /**
     * Retorna o total de conciliações pendentes para todas as entidades bancárias
     */
    public function getConciliacoesPendentes(Request $request)
    {
        $activeCompanyId = session('active_company_id');

        if (!$activeCompanyId) {
            Log::warning('getConciliacoesPendentes: Nenhuma empresa selecionada na sessão');
            return response()->json([
                'success' => false,
                'message' => 'Nenhuma empresa selecionada.',
                'total' => 0
            ], 403);
        }

        try {
            // Busca todas as entidades financeiras do tipo banco
            $entidadesBanco = EntidadeFinanceira::forActiveCompany()
                ->where('tipo', 'banco')
                ->pluck('id');

            Log::info('getConciliacoesPendentes', [
                'company_id' => $activeCompanyId,
                'entidades_count' => $entidadesBanco->count(),
                'entidades_ids' => $entidadesBanco->toArray()
            ]);

            // Se não houver entidades bancárias, retorna 0
            if ($entidadesBanco->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'total' => 0
                ]);
            }

            // Conta os bank statements pendentes de conciliação
            // IMPORTANTE: Busca TODAS as conciliações pendentes de TODO o período (sem filtro de data)
            // Filtra por company_id para garantir segurança em multitenancy
            $totalPendentes = BankStatement::where('company_id', $activeCompanyId)
                ->whereIn('entidade_financeira_id', $entidadesBanco)
                ->whereNotIn('status_conciliacao', ['ok', 'ignorado'])
                ->whereDoesntHave('transacoes')
                // Não aplica filtro de data - busca de todo o período histórico
                ->count();

            Log::info('getConciliacoesPendentes: Total encontrado (todo o período histórico)', [
                'total' => $totalPendentes,
                'observacao' => 'Contagem inclui todas as conciliações pendentes de todo o período, sem filtro de data'
            ]);

            return response()->json([
                'success' => true,
                'total' => $totalPendentes
            ]);
        } catch (\Exception $e) {
            Log::error('Erro em getConciliacoesPendentes', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar conciliações pendentes.',
                'total' => 0
            ], 500);
        }
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
     * Refatorado para usar TransacaoFinanceiraService
     */
    public function store(StoreTransacaoFinanceiraRequest $request)
    {
        try {
            // Recupera a companhia associada ao usuário autenticado
            $subsidiary = User::getCompany();

            if (!$subsidiary) {
                return redirect()->back()->with('error', 'Companhia não encontrada.');
            }

            // Adiciona company_id aos dados validados
            $validatedData = $request->validated();
            $validatedData['company_id'] = $subsidiary->company_id;

            // Delega a criação para o Service (com DB::transaction automático)
            $transacao = $this->transacaoService->criarLancamento($validatedData, $request);

            // Processa fracionamentos se houver pagamento parcial
            if ($this->temPagamentoParcial($request, $validatedData)) {
                $movimentacao = $transacao->movimentacao;
                $this->criarLancamentosFracionados($transacao, $movimentacao, $validatedData, $request);
            }

            // Processa parcelas se houver
            if ($this->temParcelas($request)) {
                $this->criarParcelas($transacao, $validatedData, $request);
                // A transação principal é mantida com o valor total
                // As parcelas são criadas na tabela 'parcelamentos'
            }

            // Resposta de sucesso - AJAX ou redirect
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Lançamento criado com sucesso!',
                    'data' => [
                        'id' => $transacao->id,
                        'descricao' => $transacao->descricao,
                        'valor' => $transacao->valor,
                    ]
                ]);
            }

            // Mensagem de sucesso
            Flasher::addSuccess('Lançamento criado com sucesso!');
            return redirect()->back()->with('message', 'Lançamento criado com sucesso!');

        } catch (\Exception $e) {
            Log::error('Erro ao criar lançamento', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Resposta de erro - AJAX ou redirect
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao criar lançamento: ' . $e->getMessage()
                ], 500);
            }
            
            Flasher::addError('Erro ao criar lançamento: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Verifica se tem pagamento parcial
     */
    protected function temPagamentoParcial(Request $request, array $data): bool
    {
        if (!$request->has('valor_pago') || !isset($data['valor_pago']) || $data['valor_pago'] <= 0) {
            return false;
        }

        $jurosPago = (float) ($request->input('juros_pagamento', 0));
        $multaPago = (float) ($request->input('multa_pagamento', 0));
        $valorParaComparacao = $data['valor_pago'] + $jurosPago + $multaPago;

        // É parcial se o valor pago for menor que o valor total
        return $valorParaComparacao < $data['valor'] && abs($valorParaComparacao - $data['valor']) >= 0.01;
    }

    /**
     * Verifica se tem recorrência
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
     * Verifica se tem parcelas
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
     * Processa movimentacao.
     * Agora vincula a Movimentacao à TransacaoFinanceira via relacionamento polimórfico usando Eloquent
     */
    private function movimentacao(TransacaoFinanceira $transacao, array $validatedData)
    {
        // Busca o lançamento padrão para obter conta_debito_id e conta_credito_id se não foram enviados
        $contaDebitoId = null;
        $contaCreditoId = null;
        $lancamentoPadraoId = null;

        if (isset($validatedData['lancamento_padrao_id']) && $validatedData['lancamento_padrao_id']) {
            $lancamentoPadraoId = $validatedData['lancamento_padrao_id'];
            $lancamentoPadrao = LancamentoPadrao::find($lancamentoPadraoId);

            if ($lancamentoPadrao) {
                $lancamentoPadrao->refresh();

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

        // 🔗 Usa Eloquent para criar a movimentação via relacionamento polimórfico
        // Eloquent automatically sets origem_type e origem_id
        $movimentacao = $transacao->movimentacao()->create([
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

        return $movimentacao;
    }

    /**
     * Processa lançamentos padrão.
     * Agora vincula via polimorfismo em vez de usar movimentacao_id
     */
    private function processarLancamentoPadrao(TransacaoFinanceira $transacao, array $validatedData)
    {
        $lancamentoPadrao = LancamentoPadrao::find($validatedData['lancamento_padrao_id']);
        if ($lancamentoPadrao && $lancamentoPadrao->description === 'Deposito Bancário') {
            $validatedData['origem'] = 'Banco';
            $validatedData['tipo'] = 'entrada';

            // Recarrega o lançamento padrão para garantir que temos os campos contábeis atualizados
            $lancamentoPadrao->refresh();

            // Cria outra movimentação para "Deposito Bancário" com polimorfismo
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
                // 🔗 POLIMORFISMO: Vincula a movimentação à transação
                'origem_type' => TransacaoFinanceira::class,
                'origem_id' => $transacao->id,
            ]);

            // Cria o lançamento no banco (SEM usar movimentacao_id)
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
        $allFiles = $request->allFiles();

        foreach ($anexos as $index => $anexoData) {
            $formaAnexo = $anexoData['forma_anexo'] ?? 'arquivo';
            $tipoAnexo = $anexoData['tipo_anexo'] ?? null;
            $descricao = $anexoData['descricao'] ?? null;

            if ($formaAnexo === 'arquivo') {
                // Tenta encontrar o arquivo usando diferentes chaves
                $file = null;

                // Tenta com notação de ponto
                $fileKey = "anexos.{$index}.arquivo";
                if ($request->hasFile($fileKey)) {
                    $file = $request->file($fileKey);
                }

                // Se não encontrou, tenta buscar em allFiles
                if (!$file && isset($allFiles['anexos'][$index]['arquivo'])) {
                    $file = $allFiles['anexos'][$index]['arquivo'];
                }

                if ($file && $file->isValid()) {
                    try {
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
                    } catch (\Exception $e) {
                        Log::error('Erro ao salvar anexo', ['error' => $e->getMessage()]);
                    }
                }
            } elseif ($formaAnexo === 'link') {
                // Processa link
                $link = $anexoData['link'] ?? null;

                if ($link) {
                    try {
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
                    } catch (\Exception $e) {
                        Log::error('Erro ao salvar link', ['error' => $e->getMessage()]);
                    }
                }
            }
        }

        // Atualiza automaticamente o campo comprovacao_fiscal
        if (isset($caixa)) {
            $caixa->updateComprovacaoFiscal();
        }
    }

    /**
     * Cria uma recorrência para o lançamento
     * Agora obtém a movimentação via relacionamento polimórfico
     */
    private function criarRecorrencia(TransacaoFinanceira $transacao, array $validatedData, Request $request)
    {
        \Log::info('criarRecorrencia - Início', [
            'transacao_id' => $transacao->id,
            'configuracao_recorrencia' => $request->input('configuracao_recorrencia'),
            'intervalo_repeticao' => $request->input('intervalo_repeticao'),
            'frequencia' => $request->input('frequencia'),
            'apos_ocorrencias' => $request->input('apos_ocorrencias')
        ]);

        $configuracaoRecorrenciaId = $request->input('configuracao_recorrencia');
        $intervaloRepeticao = $request->input('intervalo_repeticao');
        $frequencia = $request->input('frequencia');
        $aposOcorrencias = $request->input('apos_ocorrencias');

        $recorrencia = null;
        
        // Parse data_competencia (pode vir em formato brasileiro d/m/Y ou Y-m-d)
        $dataCompetencia = $validatedData['data_competencia'];
        if (str_contains($dataCompetencia, '/')) {
            $dataInicio = Carbon::createFromFormat('d/m/Y', $dataCompetencia);
        } elseif (str_contains($dataCompetencia, '-') && strlen($dataCompetencia) === 10) {
            // Verifica se é Y-m-d ou d-m-Y
            $parts = explode('-', $dataCompetencia);
            if (strlen($parts[0]) === 4) {
                $dataInicio = Carbon::createFromFormat('Y-m-d', $dataCompetencia);
            } else {
                $dataInicio = Carbon::createFromFormat('d-m-Y', $dataCompetencia);
            }
        } else {
            $dataInicio = Carbon::parse($dataCompetencia);
        }

        // Se foi enviado um ID de configuração existente (numérico), usa ela
        if ($configuracaoRecorrenciaId && (is_numeric($configuracaoRecorrenciaId) || (is_string($configuracaoRecorrenciaId) && ctype_digit($configuracaoRecorrenciaId)))) {
            $id = (int) $configuracaoRecorrenciaId;
            $recorrencia = Recorrencia::forActiveCompany()->find($id);

            if (!$recorrencia) {
                \Log::warning('Configuração de recorrência não encontrada', ['id' => $id]);
                return;
            }

            // Usa os valores da configuração existente
            $intervaloRepeticao = $recorrencia->intervalo_repeticao;
            $frequencia = $recorrencia->frequencia;
            $aposOcorrencias = $recorrencia->total_ocorrencias;
        }
        // Se não foi enviado ID mas tem os parâmetros, verifica se já existe ou cria nova configuração
        elseif ($intervaloRepeticao && $frequencia && $aposOcorrencias) {
            // Verifica se já existe uma configuração idêntica
            $recorrenciaExistente = Recorrencia::where('company_id', $validatedData['company_id'])
                ->where('intervalo_repeticao', $intervaloRepeticao)
                ->where('frequencia', $frequencia)
                ->where('total_ocorrencias', $aposOcorrencias)
                ->where('ativo', true)
                ->first();

            if ($recorrenciaExistente) {
                // Usa a configuração existente
                $recorrencia = $recorrenciaExistente;
            } else {
                // Gera nome automático
                $frequenciaText = [
                    'diario' => 'Dia(s)',
                    'semanal' => 'Semana(s)',
                    'mensal' => 'Mês(es)',
                    'anual' => 'Ano(s)'
                ];
                $nome = "A cada {$intervaloRepeticao} " . ($frequenciaText[$frequencia] ?? $frequencia) . " - Após {$aposOcorrencias} ocorrências";

                // Cria nova configuração de recorrência
                $recorrencia = Recorrencia::create([
                    'company_id' => $validatedData['company_id'],
                    'nome' => $nome,
                    'intervalo_repeticao' => $intervaloRepeticao,
                    'frequencia' => $frequencia,
                    'total_ocorrencias' => $aposOcorrencias,
                    'ocorrencias_geradas' => 0,
                    'data_proxima_geracao' => $dataInicio,
                    'data_inicio' => $dataInicio,
                    'data_fim' => null, // Será calculado pelo RecurrenceService
                    'ativo' => true,
                    'created_by' => Auth::id(),
                    'created_by_name' => Auth::user()->name,
                    'updated_by' => Auth::id(),
                    'updated_by_name' => Auth::user()->name,
                ]);
            }
        } else {
            // Não há dados suficientes para criar recorrência
            return;
        }

        // Garante que temos uma recorrência válida
        if (!$recorrencia) {
            \Log::warning('Não foi possível criar ou encontrar recorrência');
            return;
        }

        // Vincula a configuração de recorrência na transação original
        $transacao->recorrencia_id = $recorrencia->id;
        $transacao->save();

        // Usa o RecurrenceService para gerar todos os lançamentos recorrentes
        try {
            $this->recurrenceService->generateRecurringTransactions(
                $recorrencia,
                $transacao,
                $validatedData
            );

            \Log::info('Recorrência criada e lançamentos gerados com sucesso', [
                'recorrencia_id' => $recorrencia->id,
                'transacao_id' => $transacao->id,
                'total_ocorrencias' => $recorrencia->total_ocorrencias
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro ao gerar lançamentos recorrentes', [
                'recorrencia_id' => $recorrencia->id,
                'transacao_id' => $transacao->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'frequencia' => $recorrencia->frequencia,
                'validatedData' => $validatedData
            ]);
            // Re-lança a exceção para que o usuário saiba que algo deu errado
            throw new \Exception('Erro ao gerar lançamentos recorrentes: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Calcula a próxima data de geração baseada na frequência
     */
    private function calcularProximaDataGeracao(Carbon $dataInicio, int $intervalo, string $frequencia): Carbon
    {
        $data = clone $dataInicio;

        switch ($frequencia) {
            case 'diario':
                $data->addDays($intervalo);
                break;
            case 'semanal':
                $data->addWeeks($intervalo);
                break;
            case 'mensal':
                $data->addMonths($intervalo);
                break;
            case 'anual':
                $data->addYears($intervalo);
                break;
        }

        return $data;
    }

    /**
     * Calcula a data de término baseada no total de ocorrências
     */
    private function calcularDataFim(Carbon $dataInicio, int $intervalo, string $frequencia, int $totalOcorrencias): Carbon
    {
        $data = clone $dataInicio;
        $totalIntervalos = ($totalOcorrencias - 1) * $intervalo; // -1 porque a primeira já é a data de início

        switch ($frequencia) {
            case 'diario':
                $data->addDays($totalIntervalos);
                break;
            case 'semanal':
                $data->addWeeks($totalIntervalos);
                break;
            case 'mensal':
                $data->addMonths($totalIntervalos);
                break;
            case 'anual':
                $data->addYears($totalIntervalos);
                break;
        }

        return $data;
    }

    /**
     * Cria registros de fracionamento quando há pagamento parcial
     * Não cria lançamentos filhos, apenas registra os fracionamentos na tabela transacao_fracionamentos
     */
    private function criarLancamentosFracionados(
        TransacaoFinanceira $transacaoPrincipal,
        Movimentacao $movimentacaoPrincipal,
        array $validatedData,
        Request $request
    ) {
        $valorTotal = (float) $validatedData['valor'];
        $valorPago = (float) $validatedData['valor_pago'];

        // Obtém valores de juros, multa e desconto do pagamento
        $jurosPago = (float) ($request->input('juros_pagamento', 0));
        $multaPago = (float) ($request->input('multa_pagamento', 0));
        $descontoPago = (float) ($request->input('desconto_pagamento', 0));

        // IMPORTANTE: Para calcular o valor em aberto, considera apenas valor_pago + juros + multa (SEM desconto)
        // O desconto não reduz o valor em aberto, é apenas um ajuste no valor final pago
        $valorParaComparacao = $valorPago + $jurosPago + $multaPago;

        // Calcula o valor em aberto (valor total do lançamento - valor_pago - juros - multa)
        $valorAberto = $valorTotal - $valorParaComparacao;

        // Garante que o valor em aberto não seja negativo
        if ($valorAberto < 0) {
            $valorAberto = 0;
        }

        // Valor total pago (com desconto aplicado) - usado apenas no registro de fracionamento
        $valorTotalPago = $valorParaComparacao - $descontoPago;

        // Obtém data do pagamento
        $dataPagamento = null;
        if ($request->has('data_pagamento') && $request->input('data_pagamento')) {
            try {
                $dataPagamento = Carbon::createFromFormat('d/m/Y', $request->input('data_pagamento'))->format('Y-m-d');
            } catch (\Exception $e) {
                $dataPagamento = $validatedData['data_vencimento'] ?? $validatedData['data_competencia'];
            }
        } else {
            $dataPagamento = $validatedData['data_vencimento'] ?? $validatedData['data_competencia'];
        }

        // Obtém forma de pagamento e conta
        $formaPagamento = '';
        $contaPagamento = '';

        if ($request->has('entidade_id')) {
            $entidadeId = $request->input('entidade_id');
            $entidade = \App\Models\EntidadeFinanceira::find($entidadeId);
            if ($entidade) {
                $formaPagamento = $entidade->agencia . ' - ' . $entidade->conta;
            }
        }

        // Tenta obter conta de pagamento (pode não existir)
        if ($request->has('conta_pagamento_id') || $request->has('conta_financeira_id')) {
            $contaId = $request->input('conta_pagamento_id') ?? $request->input('conta_financeira_id');
            // Aqui você pode buscar o nome da conta se necessário
        }

        // 1. Registra o fracionamento PAGO
        TransacaoFracionamento::create([
            'transacao_principal_id' => $transacaoPrincipal->id,
            'tipo' => 'pago',
            'valor' => $valorPago,
            'data_pagamento' => $dataPagamento,
            'juros' => $jurosPago,
            'multa' => $multaPago,
            'desconto' => $descontoPago,
            'valor_total' => $valorTotalPago,
            'forma_pagamento' => $formaPagamento,
            'conta_pagamento' => $contaPagamento,
        ]);

        // 2. Registra o fracionamento EM ABERTO (se houver saldo)
        if ($valorAberto > 0.01) {
            TransacaoFracionamento::create([
                'transacao_principal_id' => $transacaoPrincipal->id,
                'tipo' => 'em_aberto',
                'valor' => $valorAberto,
                'data_pagamento' => null,
                'juros' => 0,
                'multa' => 0,
                'desconto' => 0,
                'valor_total' => $valorAberto,
                'forma_pagamento' => null,
                'conta_pagamento' => null,
            ]);
        }

        // 3. Atualiza o lançamento principal para "Pago Parcial"
        $transacaoPrincipal->situacao = 'pago_parcial';
        $transacaoPrincipal->valor_pago = $valorPago;
        $transacaoPrincipal->save();
    }

    /**
     * Cria parcelas para uma transação financeira parcelada
     * As parcelas são armazenadas na tabela 'parcelamentos'
     * A transação principal é mantida com o valor TOTAL
     */
    private function criarParcelas(
        TransacaoFinanceira $transacaoPrincipal,
        array $validatedData,
        Request $request
    ) {
        $parcelas = $request->input('parcelas', []);

        if (empty($parcelas) || !is_array($parcelas)) {
            return;
        }

        ksort($parcelas);
        $totalParcelas = count($parcelas);

        foreach ($parcelas as $index => $parcela) {
            $numeroParcela = (int) $index; // O índice já é o número da parcela (1, 2, 3...)
            
            // Se o índice começar em 0, ajusta para começar em 1
            if ($numeroParcela === 0) {
                $numeroParcela = 1;
            }

            // Entidade/Forma de pagamento da parcela
            $entidadeIdParcela = $validatedData['entidade_id'];
            if (isset($parcela['forma_pagamento_id']) && $parcela['forma_pagamento_id']) {
                $entidadeIdParcela = $parcela['forma_pagamento_id'];
            }

            // Conta de pagamento (pode ser diferente para cada parcela)
            $contaPagamentoId = null;
            if (isset($parcela['conta_pagamento_id']) && $parcela['conta_pagamento_id']) {
                $contaPagamentoId = $parcela['conta_pagamento_id'];
            }

            // Data de vencimento da parcela
            $dataVencimentoParcela = $validatedData['data_competencia'];
            if (isset($parcela['vencimento']) && $parcela['vencimento']) {
                $dataVencimentoParcela = $this->converterDataVencimentoParcela(
                    $parcela['vencimento'], 
                    $validatedData['data_competencia'],
                    $index
                );
            }

            // Valor da parcela
            $valorParcela = isset($parcela['valor']) ? $this->converterValorParaDecimal($parcela['valor']) : 0;

            // Percentual da parcela (calculado automaticamente se não informado)
            $valorTotal = (float) $transacaoPrincipal->valor;
            $percentualParcela = isset($parcela['percentual']) && $parcela['percentual'] > 0 
                ? (float) $parcela['percentual'] 
                : ($valorTotal > 0 ? round(($valorParcela / $valorTotal) * 100, 2) : 0);

            // Descrição da parcela (gerada automaticamente se não informada)
            $descricaoParcela = isset($parcela['descricao']) && $parcela['descricao'] 
                ? $parcela['descricao'] 
                : $validatedData['descricao'] . " {$numeroParcela}/{$totalParcelas}";

            // 1. Cria a TransacaoFinanceira para a parcela (aparece nas listagens)
            $transacaoParcela = TransacaoFinanceira::create([
                'company_id' => $validatedData['company_id'],
                'parent_id' => $transacaoPrincipal->id, // Vincula à transação PAI
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

            // 2. Cria o registro na tabela 'parcelamentos' vinculando à transação
            $transacaoPrincipal->parcelas()->create([
                'transacao_parcela_id' => $transacaoParcela->id, // Vincula à TransacaoFinanceira da parcela
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

            \Log::info('✅ Parcela criada', [
                'transacao_principal_id' => $transacaoPrincipal->id,
                'transacao_parcela_id' => $transacaoParcela->id,
                'numero_parcela' => $numeroParcela,
                'total_parcelas' => $totalParcelas,
                'valor' => $valorParcela,
                'vencimento' => $dataVencimentoParcela,
                'descricao' => $descricaoParcela
            ]);
        }

        // Atualiza a situação da transação principal para indicar que é parcelada
        $transacaoPrincipal->update([
            'situacao' => \App\Enums\SituacaoTransacao::PARCELADO,
        ]);

        \Log::info('✅ Parcelamento criado com sucesso', [
            'transacao_id' => $transacaoPrincipal->id,
            'total_parcelas' => $totalParcelas,
            'valor_total' => $transacaoPrincipal->valor
        ]);
    }

    /**
     * Converte data de vencimento da parcela para formato Y-m-d
     */
    private function converterDataVencimentoParcela(string $vencimentoStr, string $dataFallback, $parcelaIndex): string
    {
        $vencimentoStr = trim($vencimentoStr);
        $vencimentoStr = preg_replace('/\s+/', '', $vencimentoStr);

        if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $vencimentoStr, $matches)) {
            $dia = (int)trim($matches[1]);
            $mes = (int)trim($matches[2]);
            $ano = (int)trim($matches[3]);

            if ($dia >= 1 && $dia <= 31 && $mes >= 1 && $mes <= 12 && $ano >= 1900 && $ano <= 2100) {
                try {
                    return Carbon::create($ano, $mes, $dia, 0, 0, 0)->format('Y-m-d');
                } catch (\Exception $e) {
                    \Log::warning('Erro ao criar data de vencimento da parcela', [
                        'vencimento' => $vencimentoStr,
                        'erro' => $e->getMessage(),
                        'parcela_index' => $parcelaIndex
                    ]);
                }
            }
        }

        // Tenta formato alternativo
        try {
            return Carbon::createFromFormat('d/m/Y', $vencimentoStr)->format('Y-m-d');
        } catch (\Exception $e) {
            \Log::warning('Erro ao converter data de vencimento da parcela', [
                'vencimento' => $vencimentoStr,
                'erro' => $e->getMessage(),
                'parcela_index' => $parcelaIndex
            ]);
        }

        return $dataFallback;
    }

    /**
     * Converte valor de string formatada para decimal
     */
    private function converterValorParaDecimal($valor): float
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

    public function update(Request $request, $id)
    {
        try {
            // Obtenha a empresa do usuário autenticado
            $companyId = session('active_company_id');

            // Busca o registro no banco de dados
            $transacao = TransacaoFinanceira::where('company_id', $companyId)->findOrFail($id);

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
            Log::error('Erro ao atualizar transação: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao atualizar transação: ' . $e->getMessage()
                ], 500);
            }

            Flasher::addError('Erro ao atualizar transação: ' . $e->getMessage());
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
                    Flasher::addError($error);
                }
                return redirect()->back()->withInput();
            }
        }

        // Se nenhum campo foi enviado para atualizar
        if (empty($dataToUpdate)) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhum campo foi enviado para atualizar'
                ], 422);
            }
            Flasher::addError('Nenhum campo foi enviado para atualizar');
            return redirect()->back();
        }

        // Atualiza apenas os campos que foram enviados
        $transacao->update($dataToUpdate);

        // Resposta de sucesso
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Campo atualizado com sucesso!'
            ]);
        }

        Flasher::addSuccess('Campo atualizado com sucesso!');
        return redirect()->back();
    }
    
    /**
     * Atualiza a transação completa (edição via drawer)
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
                Flasher::addError($error);
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
        $movimentacaoAntiga = $transacao->movimentacao;
        $valorAntigo = $movimentacaoAntiga ? $movimentacaoAntiga->valor : 0;
        $tipoAntigo = $movimentacaoAntiga ? $movimentacaoAntiga->tipo : null;
        
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
                
                Log::info('Removendo movimentação de transação não efetivada', [
                    'transacao_id' => $transacao->id,
                    'situacao' => $situacaoAtual
                ]);
                $transacao->movimentacao->delete();
                
                // Precisa recalcular o saldo da entidade que teve movimentação removida
                $entidadesParaRecalcular[] = $entidadeMovimentacaoRemovida;
            }
        }
        
        // Saldo atualizado automaticamente pelo MovimentacaoObserver (increment/decrement O(1))
        
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
        
        Flasher::addSuccess('Lançamento atualizado com sucesso!');
        return redirect()->back();
    }

    /**
     * Retorna os dados da transação para edição no drawer (AJAX)
     * 
     * @param int $id ID da transação
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDadosEdicao($id)
    {
        try {
            $companyId = session('active_company_id');
            
            $transacao = TransacaoFinanceira::with([
                'movimentacao',
                'lancamentoPadrao',
                'costCenter',
                'parceiro',
                'entidadeFinanceira',
                'recorrenciaConfig',
                'modulos_anexos',
                'parcelas.transacaoParcela',
            ])
            ->where('company_id', $companyId)
            ->findOrFail($id);
            
            // Formatar os dados para o formulário
            $dados = [
                'id' => $transacao->id,
                'tipo' => $transacao->tipo, // entrada ou saida
                'tipo_financeiro' => $transacao->tipo === 'entrada' ? 'receita' : 'despesa',
                'descricao' => $transacao->descricao,
                'valor' => number_format($transacao->valor, 2, ',', '.'),
                'data_competencia' => $transacao->data_competencia ? $transacao->data_competencia->format('Y-m-d') : null,
                'data_vencimento' => $transacao->data_vencimento ? $transacao->data_vencimento->format('Y-m-d') : null,
                'data_pagamento' => $transacao->data_pagamento ? $transacao->data_pagamento->format('Y-m-d') : null,
                'entidade_id' => $transacao->entidade_id,
                'fornecedor_id' => $transacao->parceiro_id,
                'parceiro_id' => $transacao->parceiro_id,
                'parceiro_nome' => $transacao->parceiro?->nome,
                'lancamento_padrao_id' => $transacao->lancamento_padrao_id,
                'cost_center_id' => $transacao->cost_center_id,
                'tipo_documento' => $transacao->tipo_documento,
                'numero_documento' => $transacao->numero_documento,
                'origem' => $transacao->origem,
                'historico_complementar' => $transacao->historico_complementar,
                'comprovacao_fiscal' => (bool) $transacao->comprovacao_fiscal,
                'situacao' => $transacao->situacao?->value ?? $transacao->situacao,
                'agendado' => (bool) $transacao->agendado,
                'valor_pago' => $transacao->valor_pago ? number_format($transacao->valor_pago, 2, ',', '.') : null,
                'juros' => $transacao->juros ? number_format($transacao->juros, 2, ',', '.') : null,
                'multa' => $transacao->multa ? number_format($transacao->multa, 2, ',', '.') : null,
                'desconto' => $transacao->desconto ? number_format($transacao->desconto, 2, ',', '.') : null,
                'recorrencia_id' => $transacao->recorrencia_id,
                'parent_id' => $transacao->parent_id,
                // Dados de parcela filha (quando esta transação é uma parcela individual)
                'parcela_info' => $transacao->parent_id ? (function() use ($transacao) {
                    $parcelamento = \App\Models\Financeiro\Parcelamento::where('transacao_parcela_id', $transacao->id)->first();
                    if ($parcelamento) {
                        return [
                            'numero_parcela' => $parcelamento->numero_parcela,
                            'total_parcelas' => $parcelamento->total_parcelas,
                            'parent_id' => $transacao->parent_id,
                            'parent_descricao' => $transacao->parent?->descricao ?? null,
                        ];
                    }
                    return null;
                })() : null,
                // Dados de parcelamento (se for transação PAI)
                'is_parcelado' => $transacao->parcelas->isNotEmpty(),
                'parcelas' => $transacao->parcelas->map(function ($parcela) {
                    return [
                        'id' => $parcela->id,
                        'numero_parcela' => $parcela->numero_parcela,
                        'total_parcelas' => $parcela->total_parcelas,
                        'data_vencimento' => $parcela->data_vencimento ? $parcela->data_vencimento->format('d/m/Y') : null,
                        'valor' => number_format((float) $parcela->valor, 2, ',', '.'),
                        'percentual' => number_format((float) $parcela->percentual, 2, ',', '.'),
                        'situacao' => $parcela->situacao,
                        'valor_pago' => $parcela->valor_pago ? number_format((float) $parcela->valor_pago, 2, ',', '.') : '0,00',
                        'entidade_id' => $parcela->entidade_id,
                        'conta_pagamento_id' => $parcela->conta_pagamento_id,
                        'descricao' => $parcela->descricao,
                        'agendado' => (bool) $parcela->agendado,
                        'transacao_parcela_id' => $parcela->transacao_parcela_id,
                    ];
                })->values()->toArray(),
            ];
            
            return response()->json([
                'success' => true,
                'data' => $dados,
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Erro ao buscar dados para edição: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar dados da transação: ' . $e->getMessage(),
            ], 500);
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
        $companyId = session('active_company_id');

        // Buscar o banco com o ID e verificar se pertence à empresa do usuário
        $banco = TransacaoFinanceira::with([
                'modulos_anexos',
                'recorrenciaConfig',
                'recorrencia' => function($query) {
                    $query->withPivot('numero_ocorrencia', 'data_geracao');
                }
            ])
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
    public function destroy(Request $request, $id)
    {
        try {
            // Get delete scope from request (current|all)
            $deleteScope = $request->input('delete_scope', 'current');
            
            // 1) Localiza a transação financeira pelo ID
            $transacao = TransacaoFinanceira::findOrFail($id);
            
            // Check if this is a recurring transaction
            if ($transacao->recorrencia_id && $deleteScope === 'all') {
                // Delete entire recurrence series
                return $this->destroyRecurrenceSeries($transacao);
            } else {
                // Delete only current transaction
                return $this->destroySingleTransaction($transacao);
            }
            
        } catch (\Exception $e) {
            // Em caso de erro, registra log e retorna com mensagem de erro
            \Log::error('Erro ao excluir transação: ' . $e->getMessage());
            
            // Return JSON response para AJAX
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao excluir transação: ' . $e->getMessage()
                ], 500);
            }
            
            Flasher::addError('Erro ao excluir transação: ' . $e->getMessage());
            return redirect()->back();
        }
    }
    
    /**
     * Delete only a single transaction
     * Agora usa relacionamento polimórfico para localizar movimentação
     */
    protected function destroySingleTransaction(TransacaoFinanceira $transacao)
    {
        // 1) Localiza a movimentação associada via relacionamento polimórfico
        $movimentacao = $transacao->movimentacao;

        // 2) Excluir anexos associados (se houver)
        $anexos = ModulosAnexo::where('anexavel_id', $transacao->id)
            ->where('anexavel_type', TransacaoFinanceira::class)
            ->get();

        foreach ($anexos as $anexo) {
            if (Storage::disk('public')->exists($anexo->caminho_arquivo)) {
                Storage::disk('public')->delete($anexo->caminho_arquivo);
            }
            $anexo->delete();
        }

        // 3) Remove from pivot table if it's part of a recurrence
        if ($transacao->recorrencia_id) {
            \DB::table('recorrencia_transacoes')
                ->where('transacao_id', $transacao->id)
                ->delete();
        }

        // 4) Exclui a movimentação associada (Observer reverte saldo automaticamente)
        if ($movimentacao) {
            $movimentacao->delete();
        }

        // 5) Exclui a transação financeira
        $transacao->delete();

        // 8) Return success response
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Lançamento excluído com sucesso!'
            ]);
        }
        
        Flasher::addSuccess('Transação excluída com sucesso!');
        return redirect()->route('banco.list');
    }
    
    /**
     * Delete entire recurrence series
     */
    protected function destroyRecurrenceSeries(TransacaoFinanceira $transacao)
    {
        $recorrenciaId = $transacao->recorrencia_id;
        
        // Find all transactions in this recurrence
        $transacoes = TransacaoFinanceira::where('recorrencia_id', $recorrenciaId)->get();
        
        $deletedCount = 0;
        foreach ($transacoes as $trans) {
            // Delete attachments
            $anexos = ModulosAnexo::where('anexavel_id', $trans->id)
                ->where('anexavel_type', TransacaoFinanceira::class)
                ->get();
                
            foreach ($anexos as $anexo) {
                if (Storage::disk('public')->exists($anexo->caminho_arquivo)) {
                    Storage::disk('public')->delete($anexo->caminho_arquivo);
                }
                $anexo->delete();
            }
            
            // Exclui movimentação (Observer reverte saldo automaticamente)
            $movimentacao = $trans->movimentacao;
            if ($movimentacao) {
                $movimentacao->delete();
            }
            
            $trans->delete();
            $deletedCount++;
        }
        
        // Delete all pivot records
        \DB::table('recorrencia_transacoes')
            ->where('recorrencia_id', $recorrenciaId)
            ->delete();
        
        // Delete recurrence configuration
        \DB::table('recorrencias')
            ->where('id', $recorrenciaId)
            ->delete();
        
        // Return success response
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "$deletedCount lançamentos da série excluídos com sucesso!"
            ]);
        }
        
        Flasher::addSuccess("$deletedCount transações da série excluídas com sucesso!");
        return redirect()->route('banco.list');
    }

    /**
     * Gera relatório PDF de transações bancárias
     */
    public function gerarRelatorio(Request $request)
    {
        // Validação
        $validated = $request->validate([
            'data_inicial' => 'required|date',
            'data_final' => 'required|date|after_or_equal:data_inicial',
            'entidade_id' => 'required|array|min:1',
            'entidade_id.*' => 'required|string',
            'cost_center_id' => 'nullable|exists:cost_centers,id',
            'tipo' => 'nullable|in:entrada,saida,ambos',
            'orientacao' => 'nullable|in:horizontal,vertical',
            'lancamentos_padrao' => 'nullable|array',
            'lancamentos_padrao.*' => 'nullable|string',
        ]);

        $companyId = session('active_company_id');
        if (!$companyId) {
            return redirect()->route('dashboard')->with('error', 'Por favor, selecione uma empresa.');
        }

        // Processar entidades selecionadas
        $entidadesIds = array_filter($validated['entidade_id'], function($value) {
            return $value !== 'todos' && !empty($value);
        });

        // Se "Todos" foi selecionado ou nenhum ID específico, buscar todas as entidades do tipo 'banco'
        if (empty($entidadesIds) || in_array('todos', $validated['entidade_id'])) {
            $entidadesIds = EntidadeFinanceira::forActiveCompany()
                ->where('tipo', 'banco')
                ->pluck('id')
                ->toArray();
        } else {
            // Verificar se todas as entidades selecionadas pertencem à empresa e são do tipo 'banco'
            $entidadesValidas = EntidadeFinanceira::forActiveCompany()
                ->where('tipo', 'banco')
                ->whereIn('id', $entidadesIds)
                ->pluck('id')
                ->toArray();

            if (count($entidadesValidas) !== count($entidadesIds)) {
                return redirect()->back()->with('error', 'Uma ou mais entidades financeiras selecionadas são inválidas.');
            }
        }

        // Buscar informações das entidades para exibir no relatório
        $entidades = EntidadeFinanceira::whereIn('id', $entidadesIds)->get();

        // Converter datas
        $dataInicial = Carbon::createFromFormat('Y-m-d', $validated['data_inicial'])->startOfDay();
        $dataFinal = Carbon::createFromFormat('Y-m-d', $validated['data_final'])->endOfDay();
        $tipo = $validated['tipo'] ?? 'ambos';
        $orientacao = $validated['orientacao'] ?? 'horizontal';

        // Buscar transações - as entidades já foram validadas como tipo 'banco' acima
        // Não precisa filtrar novamente por origem, pois já filtramos por entidade_id que são todas do tipo 'banco'
        $query = TransacaoFinanceira::with(['entidadeFinanceira', 'lancamentoPadrao', 'costCenter'])
            ->where('company_id', $companyId)
            ->whereIn('entidade_id', $entidadesIds)
            ->whereBetween('data_competencia', [$dataInicial, $dataFinal]);

        // Filtro por centro de custo
        if (!empty($validated['cost_center_id'])) {
            $query->where('cost_center_id', $validated['cost_center_id']);
        }

        // Filtro por tipo
        if ($tipo !== 'ambos') {
            $query->where('tipo', $tipo);
        }

        // Filtro por lançamentos padrão
        if (!empty($validated['lancamentos_padrao']) && is_array($validated['lancamentos_padrao'])) {
            $lancamentosIds = array_filter($validated['lancamentos_padrao'], function($value) {
                return $value !== 'todos' && !empty($value);
            });

            if (!empty($lancamentosIds)) {
                $query->whereIn('lancamento_padrao_id', $lancamentosIds);
            }
            // Se tiver apenas "todos" ou estiver vazio, não aplica filtro (mostra todos)
        }

        $transacoes = $query->orderBy('data_competencia')->get();

        // Calcular saldos anteriores para cada origem
        $origens = $transacoes->pluck('origem')->unique();
        $saldosAnteriores = [];

        foreach ($origens as $origem) {
            // Buscar todas as transações anteriores ao período para esta origem
            $transacoesAnteriores = TransacaoFinanceira::where('company_id', $companyId)
                ->where('origem', $origem)
                ->where('data_competencia', '<', $dataInicial)
                ->get();

            $saldoAnterior = 0;
            foreach ($transacoesAnteriores as $trans) {
                if ($trans->tipo === 'entrada') {
                    $saldoAnterior += $trans->valor;
                } else {
                    $saldoAnterior -= $trans->valor;
                }
            }

            $saldosAnteriores[$origem] = $saldoAnterior;
        }

        // Agrupar por origem
        $dados = [];
        $totalEntradas = 0;
        $totalSaidas = 0;

        foreach ($transacoes->groupBy('origem') as $origem => $items) {
            $totEntrada = $items->where('tipo', 'entrada')->sum('valor');
            $totSaida = $items->where('tipo', 'saida')->sum('valor');
            $totalEntradas += $totEntrada;
            $totalSaidas += $totSaida;

            $dados[] = [
                'origem' => $origem,
                'items' => $items,
                'totEntrada' => $totEntrada,
                'totSaida' => $totSaida,
                'saldoAnterior' => $saldosAnteriores[$origem] ?? 0,
            ];
        }

        // Buscar empresa
        $company = \App\Models\Company::with('addresses')->find($companyId);

        // Renderizar HTML
        $html = view('app.financeiro.banco.tabs.relatorio_pdf', [
            'dados' => $dados,
            'dataInicial' => $dataInicial->format('d/m/Y'),
            'dataFinal' => $dataFinal->format('d/m/Y'),
            'entidade' => count($entidades) === 1 ? $entidades->first() : null, // Para compatibilidade
            'entidades' => $entidades, // Todas as entidades selecionadas
            'costCenter' => !empty($validated['cost_center_id'])
                ? CostCenter::find($validated['cost_center_id'])
                : null,
            'tipo' => $tipo,
            'orientacao' => $orientacao,
            'totalEntradas' => $totalEntradas,
            'totalSaidas' => $totalSaidas,
            'company' => $company,
        ])->render();

        // Gerar PDF
        $browsershot = BrowsershotHelper::configureChromePath(
            Browsershot::html($html)
                ->format('A4')
                ->showBackground()
                ->margins(8, 8, 25, 8) // Margem inferior maior para o rodapé
        );

        if ($orientacao === 'horizontal') {
            $browsershot->landscape();
        } else {
            $browsershot->portrait();
        }

        $pdf = $browsershot->pdf();

        // Gerar nome do arquivo
        if (count($entidades) === 1) {
            $entidadeNome = $entidades->first()->nome;
        } else {
            $entidadeNome = count($entidades) . '-entidades';
        }
        $filename = 'relatorio-banco-' . $entidadeNome . '-' . $dataInicial->format('Y-m-d') . '-' . $dataFinal->format('Y-m-d') . '.pdf';
        $filename = preg_replace('/[^a-zA-Z0-9\-_\.]/', '_', $filename);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    /**
     * Marca uma transação financeira como paga
     * Utiliza o TransacaoFinanceiraService para criar a movimentação (tabela da verdade)
     */
    public function markAsPaid(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:transacoes_financeiras,id',
            'data_pagamento' => 'nullable|date'
        ]);

        try {
            $companyId = session('active_company_id');

            if (!$companyId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhuma empresa selecionada.'
                ], 403);
            }

            $transacao = TransacaoFinanceira::where('company_id', $companyId)
                ->findOrFail($request->id);

            // Converter situação para string (pode ser Enum)
            $situacaoValue = $transacao->situacao instanceof \App\Enums\SituacaoTransacao 
                ? $transacao->situacao->value 
                : $transacao->situacao;

            // Verifica se já está pago/recebido
            if (in_array($situacaoValue, ['pago', 'recebido'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transação já está marcada como ' . ($transacao->tipo === 'entrada' ? 'recebida' : 'paga') . '.'
                ], 400);
            }

            $dataPagamento = $request->input('data_pagamento', Carbon::today()->format('Y-m-d'));

            // ✅ Usa o Service para registrar a baixa (cria movimentação + atualiza saldo)
            $this->transacaoService->registrarBaixa($transacao, [
                'valor_pago' => $transacao->valor,
                'data_pagamento' => $dataPagamento
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Transação marcada como ' . ($transacao->tipo === 'entrada' ? 'recebida' : 'paga') . ' com sucesso.'
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao marcar transação como paga: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao marcar como pago: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Marca múltiplas transações financeiras como pagas (ação em lote)
     */
    public function batchMarkAsPaid(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|exists:transacoes_financeiras,id',
            'data_pagamento' => 'nullable|date'
        ]);

        try {
            $companyId = session('active_company_id');

            if (!$companyId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhuma empresa selecionada.'
                ], 403);
            }

            $ids = $request->input('ids');
            $dataPagamento = $request->input('data_pagamento', Carbon::today()->format('Y-m-d'));

            // Buscar todas as transações
            $transacoes = TransacaoFinanceira::where('company_id', $companyId)
                ->whereIn('id', $ids)
                ->get();

            if ($transacoes->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhuma transação encontrada.'
                ], 404);
            }

            // Atualizar todas as transações usando o Service
            $count = 0;
            foreach ($transacoes as $transacao) {
                // Validação: Skip se já estava pago/recebido (idempotência)
                $situacaoPaga = $transacao->tipo === 'entrada' ? 'recebido' : 'pago';
                if ($transacao->situacao === $situacaoPaga) {
                    continue;
                }

                // ✅ Usa o Service para registrar a baixa (cria movimentação + atualiza saldo)
                $this->transacaoService->registrarBaixa($transacao, [
                    'valor_pago' => $transacao->valor,
                    'data_pagamento' => $dataPagamento
                ]);

                $count++;
            }

            return response()->json([
                'success' => true,
                'message' => "{$count} transação(ões) marcada(s) como " . ($transacoes->first()?->tipo === 'entrada' ? 'recebida(s)' : 'paga(s)') . " com sucesso."
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao marcar transações como pagas em lote: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao marcar como pagas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Marca múltiplas transações financeiras como em aberto (ação em lote)
     * Reverte os pagamentos: exclui movimentações e atualiza saldos
     */
    public function batchMarkAsOpen(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|exists:transacoes_financeiras,id'
        ]);

        try {
            $companyId = session('active_company_id');

            if (!$companyId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhuma empresa selecionada.'
                ], 403);
            }

            $ids = $request->input('ids');

            // Buscar todas as transações
            $transacoes = TransacaoFinanceira::where('company_id', $companyId)
                ->whereIn('id', $ids)
                ->get();

            if ($transacoes->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhuma transação encontrada.'
                ], 404);
            }

            // ✅ Usar o Service para reverter cada transação
            $count = 0;
            foreach ($transacoes as $transacao) {
                $this->transacaoService->reverterBaixa($transacao);
                $count++;
            }

            return response()->json([
                'success' => true,
                'message' => "{$count} transação(ões) marcada(s) como em aberto com sucesso."
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao marcar transações como em aberto em lote: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao marcar como em aberto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Marca uma transação financeira individual como em aberto
     * Reverte o pagamento: exclui movimentação e atualiza saldo
     */
    public function markAsOpen(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:transacoes_financeiras,id'
        ]);

        try {
            $companyId = session('active_company_id');

            if (!$companyId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhuma empresa selecionada.'
                ], 403);
            }

            $transacao = TransacaoFinanceira::where('company_id', $companyId)
                ->findOrFail($request->id);

            // Converter situação para string (pode ser Enum)
            $situacaoValue = $transacao->situacao instanceof \App\Enums\SituacaoTransacao 
                ? $transacao->situacao->value 
                : $transacao->situacao;

            // Verifica se já está em aberto
            if ($situacaoValue === 'em_aberto') {
                return response()->json([
                    'success' => false,
                    'message' => 'Transação já está marcada como em aberto.'
                ], 400);
            }

            // ✅ Usa o Service para reverter a baixa (exclui movimentação + reverte saldo)
            $this->transacaoService->reverterBaixa($transacao);

            return response()->json([
                'success' => true,
                'message' => 'Transação marcada como em aberto com sucesso.'
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao marcar transação como em aberto: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao marcar como em aberto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exclui múltiplas transações financeiras (ação em lote)
     */
    public function batchDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|exists:transacoes_financeiras,id'
        ]);

        try {
            $companyId = session('active_company_id');

            if (!$companyId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhuma empresa selecionada.'
                ], 403);
            }

            $ids = $request->input('ids');

            // Buscar todas as transações
            $transacoes = TransacaoFinanceira::where('company_id', $companyId)
                ->whereIn('id', $ids)
                ->get();

            if ($transacoes->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhuma transação encontrada.'
                ], 404);
            }

            // Deletar todas as transações
            $count = 0;
            foreach ($transacoes as $transacao) {
                $transacao->delete();
                $count++;
            }

            return response()->json([
                'success' => true,
                'message' => "{$count} transação(ões) excluída(s) com sucesso."
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao excluir transações em lote: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir transações: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Inverte o tipo de uma transação individual (receita ↔ despesa)
     */
    public function reverseType(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:transacoes_financeiras,id'
        ]);

        try {
            $companyId = session('active_company_id');

            if (!$companyId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhuma empresa selecionada.'
                ], 403);
            }

            $transacao = TransacaoFinanceira::where('company_id', $companyId)
                ->findOrFail($request->id);

            $this->transacaoService->inverterTipo($transacao);

            $novoTipo = $transacao->tipo === 'entrada' ? 'Receita' : 'Despesa';

            return response()->json([
                'success' => true,
                'message' => "Tipo invertido para {$novoTipo} com sucesso."
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao inverter tipo da transação: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao inverter tipo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Inverte o tipo de múltiplas transações (ação em lote)
     */
    public function batchReverseType(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|exists:transacoes_financeiras,id'
        ]);

        try {
            $companyId = session('active_company_id');

            if (!$companyId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhuma empresa selecionada.'
                ], 403);
            }

            $ids = $request->input('ids');

            $transacoes = TransacaoFinanceira::where('company_id', $companyId)
                ->whereIn('id', $ids)
                ->get();

            if ($transacoes->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhuma transação encontrada.'
                ], 404);
            }

            $count = 0;
            foreach ($transacoes as $transacao) {
                $this->transacaoService->inverterTipo($transacao);
                $count++;
            }

            return response()->json([
                'success' => true,
                'message' => "{$count} transação(ões) invertida(s) com sucesso."
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao inverter tipo em lote: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erro ao inverter tipo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Aplica filtro para excluir transações pagas/recebidas
     * Método reutilizável para getStatsData e getTransacoesData
     */
    private function aplicarFiltroNaoPagos($query)
    {
        // Se a transação está marcada como 'pago' ou 'recebido', ela deve ser excluída das abas de "Aberto"
        // Independentemente do valor pago registrado no banco (que pode estar inconsistente)
        return $query->where(function($q) {
            $q->whereNull('situacao')
              ->orWhereNotIn('situacao', ['pago', 'recebido']);
        });
    }

    /**
     * Método privado centralizado para calcular estatísticas
     * Usado por getSummary e getStatsData para evitar duplicação de código
     */
    private function calculateExtratoStats($startDate, $endDate, $entidadeId = null)
    {
        $companyId = session('active_company_id');
        
        if (!$companyId) {
            return null;
        }
        
        $start = Carbon::createFromFormat('Y-m-d', $startDate)->startOfDay();
        $end = Carbon::createFromFormat('Y-m-d', $endDate)->endOfDay();
        
        // Query base para extrato — mesmos filtros do ExtratoController (PDF)
        // para garantir consistência entre a aba extrato e o relatório PDF
        $query = TransacaoFinanceira::whereHas('entidadeFinanceira', function ($q) {
                $q->whereIn('tipo', ['banco', 'caixa']);
            })
            ->where('company_id', $companyId)
            ->whereNotIn('situacao', [\App\Enums\SituacaoTransacao::DESCONSIDERADO, \App\Enums\SituacaoTransacao::PARCELADO])
            ->where('agendado', false)
            ->whereBetween('data_competencia', [$start, $end]);
        
        // Aplicar filtro de conta se fornecido
        if ($entidadeId) {
            if (is_array($entidadeId)) {
                $query->whereIn('entidade_id', $entidadeId);
            } else {
                $query->where('entidade_id', $entidadeId);
            }
        }
        
        
        // Receitas em Aberto (entrada + não recebido)
        $receitasAberto = (clone $query)
            ->where('tipo', 'entrada')
            ->whereNotIn('situacao', ['recebido'])
            ->sum('valor');
        
        // Receitas Realizadas (entrada + recebido)
        $receitasRealizadas = (clone $query)
            ->where('tipo', 'entrada')
            ->where('situacao', 'recebido')
            ->sum('valor');
        
        // Despesas em Aberto (saída + não pago)
        $despesasAberto = (clone $query)
            ->where('tipo', 'saida')
            ->whereNotIn('situacao', ['pago'])
            ->sum('valor');
        
        // Despesas Realizadas (saída + pago)
        $despesasRealizadas = (clone $query)
            ->where('tipo', 'saida')
            ->where('situacao', 'pago')
            ->sum('valor');
        
        
        // Total do período = TODAS as Receitas - TODAS as Despesas (independente da situação)
        $totalReceitas = (clone $query)
            ->where('tipo', 'entrada')
            ->sum('valor');
        
        $totalDespesas = (clone $query)
            ->where('tipo', 'saida')
            ->sum('valor');
        
        $total = $totalReceitas - $totalDespesas;

        // Saldo anterior ao período:
        // Soma de todas as transações efetivadas (pago/recebido) ANTES do início do período
        $queryAnterior = TransacaoFinanceira::whereHas('entidadeFinanceira', function ($q) {
                $q->whereIn('tipo', ['banco', 'caixa']);
            })
            ->where('company_id', $companyId)
            ->where('data_competencia', '<', $start)
            ->whereNotIn('situacao', [\App\Enums\SituacaoTransacao::DESCONSIDERADO, \App\Enums\SituacaoTransacao::PARCELADO])
            ->where('agendado', false);

        if ($entidadeId) {
            if (is_array($entidadeId)) {
                $queryAnterior->whereIn('entidade_id', $entidadeId);
            } else {
                $queryAnterior->where('entidade_id', $entidadeId);
            }
        }

        $entradasAntes = (clone $queryAnterior)->where('tipo', 'entrada')->sum('valor');
        $saidasAntes = (clone $queryAnterior)->where('tipo', 'saida')->sum('valor');
        $saldoAnterior = $entradasAntes - $saidasAntes;
        
        return [
            'receitas_aberto' => $receitasAberto,
            'receitas_realizadas' => $receitasRealizadas,
            'despesas_aberto' => $despesasAberto,
            'despesas_realizadas' => $despesasRealizadas,
            'total' => $total,
            'saldo_anterior' => $saldoAnterior,
        ];
    }

    /**
     * Aplica filtro de data de vencimento para transações vencidas
     * Método reutilizável para getStatsData e getTransacoesData
     */
    private function aplicarFiltroVencidos($query, $hoje, $startDate, $endDate, $isContasReceberPagar = true)
    {
        if ($startDate && $endDate && $isContasReceberPagar) {
            // Se hoje é antes do início do período, não há nada vencido ainda no período
            if ($hoje->lt($startDate)) {
                $query->whereRaw('1 = 0');
            } else {
                // O limite superior para "vencidos" é ontem (hoje - 1 dia) ou o fim do período, o que for menor
                $ontem = $hoje->copy()->subDay();
                $limiteFim = $ontem->lt($endDate) ? $ontem : $endDate;

                $query->where(function($q) use ($startDate, $limiteFim) {
                    $q->whereBetween('data_vencimento', [$startDate, $limiteFim])
                      ->orWhere(function($subQ) use ($startDate, $limiteFim) {
                          $subQ->whereNull('data_vencimento')
                               ->whereBetween('data_competencia', [$startDate, $limiteFim]);
                      });
                });
            }
        } else {
            $query->where(function($q) use ($hoje) {
                $q->where('data_vencimento', '<', $hoje)
                  ->orWhere(function($subQ) use ($hoje) {
                      $subQ->whereNull('data_vencimento')
                           ->where('data_competencia', '<', $hoje);
                  });
            });
        }

        return $this->aplicarFiltroNaoPagos($query);
    }

    /**
     * Aplica filtro para transações que vencem hoje
     * Método reutilizável para getStatsData e getTransacoesData
     */
    private function aplicarFiltroHoje($query, $hoje, $startDate = null, $endDate = null)
    {
        if ($startDate && $endDate) {
            if (!$hoje->between($startDate, $endDate)) {
                return $query->whereRaw('1 = 0');
            }
        }

        $query->where(function($q) use ($hoje) {
            $q->where('data_vencimento', $hoje)
              ->orWhere(function($subQ) use ($hoje) {
                  $subQ->whereNull('data_vencimento')
                       ->where('data_competencia', $hoje);
              });
        });

        return $this->aplicarFiltroNaoPagos($query);
    }

    /**
     * Aplica filtro para transações a vencer (excluindo hoje)
     * Método reutilizável para getStatsData e getTransacoesData
     */
    private function aplicarFiltroAVencer($query, $hoje, $startDate, $endDate, $isContasReceberPagar = true)
    {
        if ($startDate && $endDate && $isContasReceberPagar) {
            // Se hoje é depois do fim do período, não há nada "A Vencer" (tudo já venceu ou vence hoje)
            if ($hoje->gte($endDate)) {
                $query->whereRaw('1 = 0');
            } else {
                // O limite inferior para "A Vencer" é amanhã (hoje + 1 dia) ou o início do período, o que for maior
                $amanha = $hoje->copy()->addDay();
                $limiteInicio = $amanha->gt($startDate) ? $amanha : $startDate;

                $query->where(function($q) use ($limiteInicio, $endDate) {
                    $q->whereBetween('data_vencimento', [$limiteInicio, $endDate])
                      ->orWhere(function($subQ) use ($limiteInicio, $endDate) {
                          $subQ->whereNull('data_vencimento')
                               ->whereBetween('data_competencia', [$limiteInicio, $endDate]);
                      });
                });
            }
        } else {
            $query->where(function($q) use ($hoje) {
                $q->where('data_vencimento', '>', $hoje)
                  ->orWhere(function($subQ) use ($hoje) {
                      $subQ->whereNull('data_vencimento')
                           ->where('data_competencia', '>', $hoje);
                  });
            });
        }

        return $this->aplicarFiltroNaoPagos($query);
    }
}
