<?php

namespace App\Http\Controllers\App;

use App\Helpers\DateHelper;
use App\Http\Controllers\Controller;
use App\Models\Financeiro\BankStatement;
use App\Models\Financeiro\TransacaoFinanceira;
use App\Models\TenantFilial;
use App\Models\User;
use Carbon\Carbon;
use Flasher\Laravel\Facade\Flasher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Pega a empresa ativa da sessão
        $activeCompanyId = session('active_company_id');
        $company = $user->companies()->find($activeCompanyId);

        // Fallback se nenhuma empresa estiver na sessão
        if (!$company && $user->companies()->exists()) {
            $company = $user->companies()->first();
            session(['active_company_id' => $company->id]);
            $activeCompanyId = $company->id;
        }

        if (!$company) {
            abort(403, 'Nenhuma empresa associada a este usuário.');
        }

        // Verifica se foi passado intervalo de datas ou apenas o ano
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $selectedYear = $request->input('year', now()->year);

        $query = TransacaoFinanceira::where('company_id', $activeCompanyId)
            ->whereIn('tipo', ['entrada', 'saida']); // Apenas entradas e saídas

        // Se tiver intervalo de datas, usa ele; senão, usa o ano
        if ($startDate && $endDate) {
            $start = Carbon::createFromFormat('Y-m-d', $startDate)->startOfDay();
            $end = Carbon::createFromFormat('Y-m-d', $endDate)->endOfDay();
            $query->whereBetween('data_competencia', [$start, $end]);
            
            // Para intervalo de datas, agrupa por dia
            $lancamentos = $query->select(
                    DB::raw('DATE(data_competencia) as data'),
                    'tipo',
                    DB::raw('SUM(valor) as total_valor')
                )
                ->groupBy('data', 'tipo')
                ->orderBy('data')
                ->get();

            // Prepara os dados agrupados por dia
            $dadosPorDia = [];
            $currentDate = $start->copy();
            
            while ($currentDate <= $end) {
                $dataFormatada = $currentDate->format('Y-m-d');
                $dadosPorDia[$dataFormatada] = [
                    'Entradas' => 0,
                    'Saídas' => 0
                ];
                $currentDate->addDay();
            }

            foreach ($lancamentos as $lancamento) {
                $dataFormatada = $lancamento->data;
                if ($lancamento->tipo === 'entrada') {
                    $dadosPorDia[$dataFormatada]['Entradas'] = (float) $lancamento->total_valor;
                } elseif ($lancamento->tipo === 'saida') {
                    $dadosPorDia[$dataFormatada]['Saídas'] = (float) $lancamento->total_valor;
                }
            }

            // Prepara arrays para o gráfico
            $entradasData = [];
            $saidasData = [];
            $categories = [];
            
            foreach ($dadosPorDia as $data => $valores) {
                $categories[] = Carbon::createFromFormat('Y-m-d', $data)->format('d/m');
                $entradasData[] = $valores['Entradas'];
                $saidasData[] = $valores['Saídas'];
            }

            $areaChartData = [
                'series' => [
                    ['name' => 'Entradas', 'data' => $entradasData],
                    ['name' => 'Saídas', 'data' => $saidasData],
                ],
                'categories' => $categories
            ];
        } else {
            // Mantém a lógica original por ano
            $query->whereYear('data_competencia', $selectedYear);
            
            $lancamentos = $query->select(
                    DB::raw('MONTH(data_competencia) as mes'),
                    'tipo',
                    DB::raw('SUM(valor) as total_valor')
                )
                ->groupBy('mes', 'tipo')
                ->get();

            // Prepara os dados para o gráfico
            $series = [
                'Entradas' => array_fill(0, 12, 0), // Inicializa um array com 12 zeros
                'Saídas'   => array_fill(0, 12, 0), // Inicializa um array com 12 zeros
            ];

            foreach ($lancamentos as $lancamento) {
                $mesIndex = $lancamento->mes - 1; // Ajusta o mês (1-12) para o índice do array (0-11)
                if ($lancamento->tipo === 'entrada') {
                    $series['Entradas'][$mesIndex] = (float) $lancamento->total_valor;
                } elseif ($lancamento->tipo === 'saida') {
                    $series['Saídas'][$mesIndex] = (float) $lancamento->total_valor;
                }
            }

            // Formata os dados no padrão que o ApexCharts espera
            $areaChartData = [
                'series' => [
                    ['name' => 'Entradas', 'data' => $series['Entradas']],
                    ['name' => 'Saídas', 'data' => $series['Saídas']],
                ],
                'categories' => ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez']
            ];
        }

        // Retorna para a view
        return view('app.dashboard', [
            'areaChartData' => $areaChartData,
            'selectedYear' => $selectedYear,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Obter o usuário pelo ID
        $user = User::findOrFail($id);

        // Carregar a empresa relacionada
        $company = $user->company;

        // Retornar a visão com os dados do usuário e da empresa
        return view('app.dashboard', compact('user', 'company'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Retorna dados do gráfico de ranking de missas por dia da semana
     */
    public function getMissasChartData(Request $request)
    {
        $activeCompanyId = session('active_company_id');
        
        if (!$activeCompanyId) {
            return response()->json(['error' => 'Nenhuma empresa selecionada'], 400);
        }

        // Parâmetros de data (opcionais)
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Query base: busca BankStatements conciliados com missas
        // Usar whereRaw para tratar tanto boolean true quanto string '1'
        $query = BankStatement::where('company_id', $activeCompanyId)
            ->where(function($q) {
                $q->where('conciliado_com_missa', true)
                  ->orWhere('conciliado_com_missa', 1)
                  ->orWhere('conciliado_com_missa', '1');
            })
            ->whereNotNull('horario_missa_id')
            ->with('horarioMissa');

        // Aplicar filtro de período se fornecido
        if ($startDate && $endDate) {
            $start = Carbon::createFromFormat('Y-m-d', $startDate)->startOfDay();
            $end = Carbon::createFromFormat('Y-m-d', $endDate)->endOfDay();
            
            // Usar transaction_datetime se disponível, senão dtposted
            $query->where(function($q) use ($start, $end) {
                $q->whereBetween('transaction_datetime', [$start, $end])
                  ->orWhere(function($q2) use ($start, $end) {
                      $q2->whereNull('transaction_datetime')
                         ->whereBetween('dtposted', [$start, $end]);
                  });
            });
        }

        $bankStatements = $query->get();
        
        // Log para debug
        \Log::info('DashboardController::getMissasChartData', [
            'company_id' => $activeCompanyId,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_statements' => $bankStatements->count(),
            'statements' => $bankStatements->map(function($s) {
                return [
                    'id' => $s->id,
                    'amount' => $s->amount,
                    'conciliado_com_missa' => $s->conciliado_com_missa,
                    'horario_missa_id' => $s->horario_missa_id,
                    'horario_missa' => $s->horarioMissa ? [
                        'id' => $s->horarioMissa->id,
                        'dia_semana' => $s->horarioMissa->dia_semana
                    ] : null,
                    'transaction_datetime' => $s->transaction_datetime,
                    'dtposted' => $s->dtposted
                ];
            })->toArray()
        ]);

        // Ordem dos dias da semana
        $ordemDias = [
            'Domingo' => 0,
            'Segunda' => 1,
            'Terça' => 2,
            'Quarta' => 3,
            'Quinta' => 4,
            'Sexta' => 5,
            'Sábado' => 6
        ];

        // Agrupar por dia da semana e somar valores
        $dadosPorDia = [];
        foreach ($bankStatements as $statement) {
            // Verificar se o relacionamento foi carregado
            if (!$statement->relationLoaded('horarioMissa') && $statement->horario_missa_id) {
                $statement->load('horarioMissa');
            }
            
            if ($statement->horarioMissa && $statement->horarioMissa->dia_semana) {
                // Normalizar dia_semana: primeira letra maiúscula (ex: "sexta" -> "Sexta")
                $diaSemana = ucfirst(mb_strtolower($statement->horarioMissa->dia_semana));
                
                if (!isset($dadosPorDia[$diaSemana])) {
                    $dadosPorDia[$diaSemana] = 0;
                }
                
                // Somar apenas valores positivos (entradas)
                if ($statement->amount > 0) {
                    $dadosPorDia[$diaSemana] += floatval($statement->amount);
                }
            } else {
                // Log para debug quando horarioMissa não está disponível
                \Log::warning('BankStatement sem horarioMissa válido', [
                    'bank_statement_id' => $statement->id,
                    'horario_missa_id' => $statement->horario_missa_id,
                    'horarioMissa_loaded' => $statement->relationLoaded('horarioMissa'),
                    'horarioMissa_exists' => $statement->horarioMissa ? true : false,
                    'dia_semana' => $statement->horarioMissa ? $statement->horarioMissa->dia_semana : null
                ]);
            }
        }

        // Ordenar por ordem dos dias da semana
        uksort($dadosPorDia, function($a, $b) use ($ordemDias) {
            $ordemA = $ordemDias[$a] ?? 999;
            $ordemB = $ordemDias[$b] ?? 999;
            return $ordemA <=> $ordemB;
        });

        // Preparar arrays para o gráfico
        $categories = array_keys($dadosPorDia);
        $data = array_values($dadosPorDia);

        // Se algum dia não tiver dados, adicionar com valor 0
        $diasCompletos = ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'];
        $categoriesFinal = [];
        $dataFinal = [];
        
        foreach ($diasCompletos as $dia) {
            $categoriesFinal[] = $dia;
            $dataFinal[] = $dadosPorDia[$dia] ?? 0;
        }

        // Log final para debug
        \Log::info('DashboardController::getMissasChartData - Resultado final', [
            'dadosPorDia' => $dadosPorDia,
            'categoriesFinal' => $categoriesFinal,
            'dataFinal' => $dataFinal
        ]);

        return response()->json([
            'success' => true,
            'data' => $dataFinal,
            'categories' => $categoriesFinal,
            'debug' => [
                'total_statements' => $bankStatements->count(),
                'statements_with_horario' => $bankStatements->filter(function($s) {
                    return $s->horarioMissa && $s->horarioMissa->dia_semana;
                })->count()
            ]
        ]);
    }
}
