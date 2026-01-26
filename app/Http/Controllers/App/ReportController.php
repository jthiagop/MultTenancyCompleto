<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Caixa;
use App\Models\Financeiro\TransacaoFinanceira;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{

    public function shippingReport()
    {
        return view('report.shipping');
    }

    public function shippingReportData(Request $request)
    {
        $caixas = Caixa::select('id', 'data_competencia', 'tipo_documento', 'lancamento_padrao', 'tipo', 'valor', 'origem')->get();

        return datatables()->of($caixas)
            ->editColumn('data_competencia', function ($caixa) {
                return date('d-m-Y', strtotime($caixa->data_competencia));
            })
            ->editColumn('tipo', function ($caixa) {
                $badgeClass = $caixa->tipo == 'entrada' ? 'badge-success' : 'badge-danger';
                return '<div class="badge fw-bold ' . $badgeClass . '">' . $caixa->tipo . '</div>';
            })
            ->editColumn('valor', function ($caixa) {
                return 'R$ ' . number_format($caixa->valor / 100, 2, ',', '.');
            })
            ->addColumn('acoes', function ($caixa) {
                $editUrl = route('caixa.edit', $caixa->id);
                $deleteUrl = route('caixa.destroy', $caixa->id);

                return '<a href="' . $editUrl . '" class="menu-link px-3">Editar</a>
                        <a href="#" class="menu-link px-3 delete-link" data-id="' . $caixa->id . '">Excluir</a>
                        <form id="delete-form-' . $caixa->id . '" action="' . $deleteUrl . '" method="POST" style="display: none;">
                            ' . csrf_field() . method_field('DELETE') . '
                        </form>';
            })
            ->rawColumns(['tipo', 'acoes'])
            ->make(true);
    }

    public function getFinancialData(Request $request)
    {
        $activeCompanyId = session('active_company_id');
        if (!$activeCompanyId) {
            return response()->json(['data' => []]); // Retorna vazio se não houver empresa
        }

        $query = TransacaoFinanceira::with(['lancamentoPadrao', 'modulos_anexos'])
            ->where('company_id', $activeCompanyId);

        // Aplica o filtro de status (entrada/saída)
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('tipo', $request->status);
        }

        // Aplica o filtro de período
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::createFromFormat('Y-m-d', $request->start_date)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $request->end_date)->endOfDay();
            $query->whereBetween('data_competencia', [$startDate, $endDate]);
        }

        // Aplica a busca geral
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('descricao', 'like', "%{$search}%")
                    ->orWhere('tipo_documento', 'like', "%{$search}%")
                    ->orWhere('origem', 'like', "%{$search}%");
            });
        }

        $transacoes = $query->latest('data_competencia')->get();

        // Formata os dados para a view (pode ser feito com API Resources para projetos maiores)
        $data = $transacoes->map(function ($transacao) {
            // A lógica para formatar cada linha da tabela vai aqui
            return [
                'id' => $transacao->id,
                'data_competencia' => Carbon::parse($transacao->data_competencia)->format('d/m/Y'),
                'tipo_documento' => $transacao->tipo_documento,
                'comprovacao_fiscal' => $transacao->comprovacao_fiscal,
                'descricao' => $transacao->descricao,
                'lancamento_padrao' => optional($transacao->lancamentoPadrao)->description,
                'tipo' => $transacao->tipo,
                'valor' => number_format($transacao->valor / 100, 2, ',', '.'),
                'origem' => $transacao->origem,
                'anexos' => $transacao->modulos_anexos, // Passa a coleção de anexos
                'edit_url' => route('banco.edit', $transacao->id) // Exemplo de URL para o botão de editar
            ];
        });

        return response()->json(['data' => $data]);
    }

    /**
     * Busca os dados financeiros filtrados para serem usados na análise da IA.
     */
    public function getDataForGeminiAnalysis(Request $request)
    {
        $activeCompanyId = session('active_company_id');
        if (!$activeCompanyId) {
            return response()->json(['error' => 'Nenhuma empresa selecionada'], 403);
        }

        $query = TransacaoFinanceira::with('lancamentoPadrao')
                                    ->where('company_id', $activeCompanyId);

        // Aplica os mesmos filtros da DataTable
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('tipo', $request->status);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::createFromFormat('Y-m-d', $request->start_date)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $request->end_date)->endOfDay();
            $query->whereBetween('data_competencia', [$startDate, $endDate]);
        }
        
        if ($request->filled('search')) {
            $search = $request->search['value']; // A busca do datatable vem em search[value]
            if(!empty($search)){
                 $query->where(function($q) use ($search) {
                    $q->where('descricao', 'like', "%{$search}%")
                      ->orWhere('tipo_documento', 'like', "%{$search}%")
                      ->orWhere('origem', 'like', "%{$search}%");
                });
            }
        }

        $transacoes = $query->latest('data_competencia')->get();

        // Retorna os dados brutos. O frontend irá formatar o prompt.
        return response()->json($transacoes);
    }

    /**
     * Fornece os dados para a DataTable com processamento do lado do servidor.
     */
    public function getFinancialDataServerSide(Request $request)
    {
        $activeCompanyId = session('active_company_id');
        if (!$activeCompanyId) {
            return response()->json(['data' => []]); // Retorna vazio se não houver empresa
        }
        $query = TransacaoFinanceira::with(['lancamentoPadrao'])
                                    ->where('company_id', $activeCompanyId);

        // Contagem total de registros antes de qualquer filtro
        $recordsTotal = $query->count();

        // Aplicar filtros personalizados (status e data)
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('tipo', $request->status);
        }
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::createFromFormat('Y-m-d', $request->start_date)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $request->end_date)->endOfDay();
            $query->whereBetween('data_competencia', [$startDate, $endDate]);
        }
        
        // Aplicar busca geral (do campo de pesquisa)
        if ($request->filled('search') && !empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('descricao', 'like', "%{$search}%")
                  ->orWhere('tipo_documento', 'like', "%{$search}%")
                  ->orWhere('origem', 'like', "%{$search}%");
            });
        }

        // Contagem de registros após aplicar os filtros
        $recordsFiltered = $query->count();

        // Aplicar ordenação
        if ($request->has('order') && count($request->order)) {
            $order = $request->order[0];
            $columnIndex = $order['column'];
            $columnName = $request->columns[$columnIndex]['data'];
            $direction = $order['dir'];
            if($request->columns[$columnIndex]['orderable'] == 'true'){
                 $query->orderBy($columnName, $direction);
            }
        } else {
            $query->latest('data_competencia');
        }

        // Aplicar paginação
        $transacoes = $query->skip($request->start)->take($request->length)->get();

        // Formatar os dados para a resposta JSON
        $data = $transacoes->map(function($transacao) {
            return [
                'id' => $transacao->id,
                'data_competencia' => Carbon::parse($transacao->data_competencia)->format('d/m/Y'),
                'tipo_documento' => $transacao->tipo_documento,
                'comprovacao_fiscal' => $transacao->comprovacao_fiscal,
                'descricao' => $transacao->descricao,
                'lancamento_padrao' => optional($transacao->lancamentoPadrao)->description,
                'tipo' => $transacao->tipo,
                'valor' => number_format($transacao->valor / 100, 2, ',', '.'),
                'origem' => $transacao->origem,
                'anexos' => $transacao->modulos_anexos->count(),
                'actions' => route('banco.edit', $transacao->id)
            ];
        });

        // Retorna a resposta no formato que a DataTable espera
        return response()->json([
            "draw"            => intval($request->draw),
            "recordsTotal"    => intval($recordsTotal),
            "recordsFiltered" => intval($recordsFiltered),
            "data"            => $data
        ]);
    }
}
