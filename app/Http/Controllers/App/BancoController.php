<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Http\Requests\Financer\StoreTransacaoFinanceiraRequest;
use App\Models\Anexo;
use App\Models\Banco;
use App\Models\EntidadeFinanceira;
use App\Models\Financeiro\BankStatement;
use App\Models\Financeiro\CostCenter;
use App\Models\Financeiro\ModulosAnexo;
use App\Models\Financeiro\TransacaoFinanceira;
use App\Models\HorarioMissa;
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
use Spatie\Browsershot\Browsershot;


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


        $perPage = (int) $request->input('per_page', 50); // Aumentado de 25 para 50
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

        // Entidades para o relatório de prestação de contas
        $entidades = EntidadeFinanceira::forActiveCompany() // 1. Usa o scope para filtrar pela empresa
            ->where('tipo', 'banco')  // 2. Adiciona o filtro específico para bancos
            ->with('bankStatements')  // 3. (Opcional, mas recomendado) Otimiza a consulta
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

        // Se a tab ativa for 'relatorios', buscar entidades financeiras do tipo 'banco' para o select
        $entidadesRelatorio = [];
        if ($activeTab === 'relatorios') {
            $entidadesRelatorio = EntidadeFinanceira::forActiveCompany()
                ->where('tipo', 'banco')
                ->orderBy('nome')
                ->get();
        }

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

        // Verifica se existem horários de missa cadastrados para a empresa ativa
        $hasHorariosMissas = HorarioMissa::where('company_id', $companyId)->exists();

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
            'entidades' => $entidades,
            'entidadesRelatorio' => $entidadesRelatorio,
            'hasHorariosMissas' => $hasHorariosMissas,
        ], $dadosGrafico));
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

        // Query base - filtrar apenas transações de banco
        $query = TransacaoFinanceira::with(['modulos_anexos', 'lancamentoPadrao'])
            ->whereHas('entidadeFinanceira', function ($q) {
                $q->where('tipo', 'banco');
            })
            ->where('company_id', $companyId);

        // Contagem total de registros antes de qualquer filtro
        $recordsTotal = $query->count();

        // Aplicar busca geral (do campo de pesquisa do DataTables)
        if ($request->filled('search') && !empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('descricao', 'like', "%{$search}%")
                  ->orWhere('tipo_documento', 'like', "%{$search}%")
                  ->orWhere('numero_documento', 'like', "%{$search}%")
                  ->orWhere('origem', 'like', "%{$search}%")
                  ->orWhereHas('lancamentoPadrao', function($subQ) use ($search) {
                      $subQ->where('description', 'like', "%{$search}%");
                  });
            });
        }

        // Aplicar filtro de tipo (entrada/saida)
        if ($request->filled('tipo') && $request->tipo !== 'all' && $request->tipo !== '') {
            $query->where('tipo', $request->tipo);
        }

        // Aplicar filtro de data (daterangepicker)
        if ($request->filled('start_date') && $request->filled('end_date')) {
            try {
                $startDate = Carbon::createFromFormat('Y-m-d', $request->start_date)->startOfDay();
                $endDate = Carbon::createFromFormat('Y-m-d', $request->end_date)->endOfDay();
                $query->whereBetween('data_competencia', [$startDate, $endDate]);
            } catch (\Exception $e) {
                Log::warning('Erro ao processar filtro de data no DataTables', ['error' => $e->getMessage()]);
            }
        }

        // Contagem de registros após aplicar os filtros
        $recordsFiltered = $query->count();

        // Aplicar ordenação
        $orderColumn = 'id'; // ID por padrão
        $orderDir = 'desc';
        
        if ($request->has('order') && count($request->order)) {
            $order = $request->order[0];
            $columnIndex = (int) $order['column'];
            $orderDir = $order['dir'];
            
            // Mapear índice da coluna para campo do banco
            $columnMap = [
                0 => 'id',
                1 => 'data_competencia',
                2 => 'tipo_documento',
                3 => 'comprovacao_fiscal',
                4 => 'descricao',
                5 => 'tipo',
                6 => 'valor',
                7 => 'origem',
                8 => 'anexos',
                9 => 'actions'
            ];
            
            $orderColumn = $columnMap[$columnIndex] ?? 'id';
            
            // Campos que não devem ser ordenados (HTML)
            $nonOrderableColumns = ['comprovacao_fiscal', 'descricao', 'anexos', 'actions'];
            if (in_array($orderColumn, $nonOrderableColumns)) {
                $orderColumn = 'id'; // Fallback para ID
            }
        }
        
        $query->orderBy($orderColumn, $orderDir);

        // Aplicar paginação
        $start = $request->input('start', 0);
        $length = $request->input('length', 50);
        $transacoes = $query->skip($start)->take($length)->get();

        // Formatar os dados para a resposta JSON
        $data = $transacoes->map(function($transacao) {
            // Formatar descrição com lançamento padrão
            // Formatar descrição com lançamento padrão
            $descricaoHtml = '<div class="fw-bold"><a href="#" onclick="abrirDrawerTransacao(' . $transacao->id . '); return false;" class="text-gray-800 text-hover-primary">' . e($transacao->descricao) . '</a></div>';
            if ($transacao->lancamentoPadrao) {
                $descricaoHtml .= '<div class="text-muted small">' . e($transacao->lancamentoPadrao->description) . '</div>';
            }
            
            // Formatar anexos
            $anexosHtml = $this->formatAnexos($transacao);
            
            // Formatar ações
            $actionsHtml = '<div class="d-flex justify-content-end align-items-center">
                <a href="' . route('banco.edit', $transacao->id) . '" class="btn btn-icon btn-active-light-primary w-30px h-30px ms-auto me-5">
                    <span class="svg-icon svg-icon-3">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="currentColor" />
                            <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="currentColor" />
                        </svg>
                    </span>
                </a>
            </div>';
            
            return [
                $transacao->id,
                $transacao->data_competencia 
                    ? Carbon::parse($transacao->data_competencia)->format('d/m/y') 
                    : '-',
                $transacao->tipo_documento ?? '-',
                $transacao->comprovacao_fiscal 
                    ? '<i class="fas fa-check-circle text-success" title="Comprovação Fiscal"></i>'
                    : '<i class="bi bi-x-circle-fill text-danger" title="Sem Comprovação Fiscal"></i>',
                $descricaoHtml,
                '<div class="badge fw-bold ' . ($transacao->tipo == 'entrada' ? 'badge-success' : 'badge-danger') . '">' . $transacao->tipo . '</div>',
                'R$ ' . number_format($transacao->valor, 2, ',', '.'),
                $transacao->origem ?? '-',
                $anexosHtml,
                $actionsHtml
            ];
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
    
    /**
     * Formata os anexos para exibição na tabela
     */
    private function formatAnexos($transacao)
    {
        $anexos = $transacao->modulos_anexos->take(3);
        $remainingAnexos = $transacao->modulos_anexos->count() - 3;
        
        $icons = [
            'pdf' => ['icon' => 'bi-file-earmark-pdf-fill', 'color' => 'text-danger'],
            'jpg' => ['icon' => 'bi-file-earmark-image-fill', 'color' => 'text-warning'],
            'jpeg' => ['icon' => 'bi-file-earmark-image-fill', 'color' => 'text-primary'],
            'png' => ['icon' => 'bi-file-earmark-image-fill', 'color' => 'text-warning'],
            'doc' => ['icon' => 'bi-file-earmark-word-fill', 'color' => 'text-info'],
            'docx' => ['icon' => 'bi-file-earmark-word-fill', 'color' => 'text-info'],
            'xls' => ['icon' => 'bi-file-earmark-spreadsheet-fill', 'color' => 'text-warning'],
            'xlsx' => ['icon' => 'bi-file-earmark-spreadsheet-fill', 'color' => 'text-warning'],
            'txt' => ['icon' => 'bi-file-earmark-text-fill', 'color' => 'text-muted'],
        ];
        $defaultIcon = ['icon' => 'bi-file-earmark-fill', 'color' => 'text-secondary'];
        
        $html = '<div class="symbol-group symbol-hover fs-8">';
        
        foreach ($anexos as $anexo) {
            $formaAnexo = $anexo->forma_anexo ?? 'arquivo';
            $isLink = $formaAnexo === 'link';
            
            if ($isLink) {
                $href = $anexo->link ?? '#';
                $tooltip = $anexo->link ?? 'Link';
                $iconData = ['icon' => 'bi-link-45deg', 'color' => 'text-primary'];
            } else {
                $extension = pathinfo($anexo->nome_arquivo ?? '', PATHINFO_EXTENSION);
                $iconData = $icons[strtolower($extension)] ?? $defaultIcon;
                $tooltip = $anexo->nome_arquivo ?? 'Arquivo';
                
                if ($anexo->caminho_arquivo) {
                    $href = route('file', ['path' => $anexo->caminho_arquivo]);
                } else {
                    $href = '#';
                }
            }
            
            $html .= '<div class="symbol symbol-30px symbol-circle bg-light-primary text-primary d-flex justify-content-center align-items-center" data-bs-toggle="tooltip" title="' . e($tooltip) . '">';
            $html .= '<a href="' . e($href) . '" target="_blank" class="text-decoration-none">';
            $html .= '<i class="bi ' . $iconData['icon'] . ' ' . $iconData['color'] . ' fs-3"></i>';
            $html .= '</a></div>';
        }
        
        if ($remainingAnexos > 0) {
            $html .= '<div class="symbol symbol-25px symbol-circle" data-bs-toggle="tooltip" title="Mais ' . $remainingAnexos . ' anexos">';
            $html .= '<a href="' . route('banco.edit', $transacao->id) . '">';
            $html .= '<span class="symbol-label fs-8 fw-bold bg-light text-gray-800">+' . $remainingAnexos . '</span>';
            $html .= '</a></div>';
        }
        
        if ($transacao->modulos_anexos->isEmpty()) {
            $html .= '<div class="symbol symbol-25px symbol-circle text-center" data-bs-toggle="tooltip" title="Nenhum anexo disponível">';
            $html .= '<span class="symbol-label fs-8 fw-bold bg-light text-gray-800">0</span>';
            $html .= '</div>';
        }
        
        $html .= '</div>';
        
        return $html;
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
            $validatedData['origem'] = 'Banco';
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

            // Busca o lançamento padrão para obter conta_debito_id e conta_credito_id
            $contaDebitoId = null;
            $contaCreditoId = null;
            
            if (isset($validatedData['lancamento_padrao_id']) && $validatedData['lancamento_padrao_id']) {
                $lancamentoPadrao = LancamentoPadrao::find($validatedData['lancamento_padrao_id']);
                
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
            
            // 3) Atualiza a movimentação (agora ela aponta para a nova entidade e novo valor)
            $movimentacao->update([
                'entidade_id' => $validatedData['entidade_id'],
                'tipo'        => $validatedData['tipo'],
                'valor'       => $validatedData['valor'],
                'data'        => $validatedData['data_competencia'],
                'descricao'   => $validatedData['descricao'],
                'lancamento_padrao_id' => $validatedData['lancamento_padrao_id'] ?? null,
                'conta_debito_id' => $contaDebitoId,
                'conta_credito_id' => $contaCreditoId,
                'data_competencia' => $validatedData['data_competencia'],
                'updated_by'  => Auth::user()->id,
                'updated_by_name' => Auth::user()->name,
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
        $companyId = session('active_company_id');

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
        $browsershot = Browsershot::html($html)
            ->format('A4')
            ->showBackground()
            ->margins(8, 8, 25, 8); // Margem inferior maior para o rodapé

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
}
