<?php

namespace App\Http\Controllers\App\Financeiro;

use App\Http\Controllers\Controller;
use App\Http\Requests\Financer\StoreRepasseRequest;
use App\Http\Requests\Financer\UpdateRepasseRequest;
use App\Models\Company;
use App\Models\Financeiro\Repasse;
use App\Services\RepasseService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RepasseController extends Controller
{
    public function __construct(
        private RepasseService $repasseService
    ) {}

    /**
     * Retorna estatísticas (stats) dos repasses para as tabs segmentadas.
     */
    public function statsData(Request $request): JsonResponse
    {
        $companyId = session('active_company_id');

        if (!$companyId) {
            return response()->json([
                'a_pagar' => '0,00',
                'pagos' => '0,00',
                'atrasados' => '0,00',
                'total' => '0,00',
            ]);
        }

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if (!$startDate || !$endDate) {
            $startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
            $endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
        }

        $start = Carbon::createFromFormat('Y-m-d', $startDate)->startOfDay();
        $end = Carbon::createFromFormat('Y-m-d', $endDate)->endOfDay();
        $hoje = Carbon::now()->startOfDay();

        // Filtro por tipo: saida (empresa é origem) ou entrada (empresa é destino)
        $tipo = $request->input('tipo');

        $baseQuery = Repasse::query();
        if ($tipo === 'entrada') {
            $baseQuery->whereHas('itens', fn($sub) => $sub->where('company_destino_id', $companyId));
        } elseif ($tipo === 'saida') {
            $baseQuery->where('company_origem_id', $companyId);
        } else {
            $baseQuery->where(function ($q) use ($companyId) {
                $q->where('company_origem_id', $companyId)
                    ->orWhereHas('itens', fn($sub) => $sub->where('company_destino_id', $companyId));
            });
        }

        // A Pagar: pendentes com vencimento no período ou sem vencimento com emissão no período
        $aPagar = (clone $baseQuery)
            ->where('status', 'pendente')
            ->where(function ($q) use ($hoje) {
                $q->where('data_vencimento', '>=', $hoje)
                    ->orWhereNull('data_vencimento');
            })
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('data_vencimento', [$start, $end])
                    ->orWhere(fn($sub) => $sub->whereNull('data_vencimento')->whereBetween('data_emissao', [$start, $end]));
            })
            ->sum('valor_total');

        // Pagos (executados) no período
        $pagos = (clone $baseQuery)
            ->where('status', 'executado')
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('data_vencimento', [$start, $end])
                    ->orWhere(fn($sub) => $sub->whereNull('data_vencimento')->whereBetween('data_emissao', [$start, $end]));
            })
            ->sum('valor_total');

        // Atrasados: pendentes com vencimento anterior a hoje E dentro do período
        $atrasados = (clone $baseQuery)
            ->where('status', 'pendente')
            ->whereNotNull('data_vencimento')
            ->where('data_vencimento', '<', $hoje)
            ->whereBetween('data_vencimento', [$start, $end])
            ->sum('valor_total');

        // Total do período
        $total = (clone $baseQuery)
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('data_vencimento', [$start, $end])
                    ->orWhere(fn($sub) => $sub->whereNull('data_vencimento')->whereBetween('data_emissao', [$start, $end]));
            })
            ->whereIn('status', ['pendente', 'executado'])
            ->sum('valor_total');

        return response()->json([
            'a_pagar' => number_format((float) $aPagar, 2, ',', '.'),
            'pagos' => number_format((float) $pagos, 2, ',', '.'),
            'atrasados' => number_format((float) $atrasados, 2, ',', '.'),
            'total' => number_format((float) $total, 2, ',', '.'),
        ]);
    }

    /**
     * Retorna dados dos repasses para DataTables (server-side).
     * Formato compatível com tenant-datatable-pane.js (arrays com índices numéricos).
     */
    public function data(Request $request): JsonResponse
    {
        $companyId = session('active_company_id');

        if (!$companyId) {
            return response()->json([
                'draw' => (int) $request->input('draw', 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
            ]);
        }

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $status = $request->input('status');

        $start = null;
        $end = null;
        $hoje = Carbon::now()->startOfDay();

        if ($startDate && $endDate) {
            $start = Carbon::createFromFormat('Y-m-d', $startDate)->startOfDay();
            $end = Carbon::createFromFormat('Y-m-d', $endDate)->endOfDay();
        }

        // Filtro por tipo: saida (empresa é origem) ou entrada (empresa é destino)
        $tipo = $request->input('tipo');

        $query = Repasse::with(['companyOrigem', 'entidadeOrigem', 'formaPagamento', 'itens.companyDestino']);
        if ($tipo === 'entrada') {
            $query->whereHas('itens', fn($sub) => $sub->where('company_destino_id', $companyId));
        } elseif ($tipo === 'saida') {
            $query->where('company_origem_id', $companyId);
        } else {
            $query->where(function ($q) use ($companyId) {
                $q->where('company_origem_id', $companyId)
                    ->orWhereHas('itens', fn($sub) => $sub->where('company_destino_id', $companyId));
            });
        }

        // Filtro por status da tab
        if ($status && $status !== 'total') {
            switch ($status) {
                case 'a_pagar':
                    $query->where('status', 'pendente')
                        ->where(function ($q) use ($hoje) {
                            $q->where('data_vencimento', '>=', $hoje)
                                ->orWhereNull('data_vencimento');
                        });
                    break;

                case 'pagos':
                    $query->where('status', 'executado');
                    break;

                case 'atrasados':
                    $query->where('status', 'pendente')
                        ->whereNotNull('data_vencimento')
                        ->where('data_vencimento', '<', $hoje);
                    break;
            }
        } else {
            // Total: excluir cancelados
            $query->whereIn('status', ['pendente', 'executado']);
        }

        // Filtro de período
        if ($start && $end) {
            $query->where(function ($q) use ($start, $end) {
                $q->whereBetween('data_vencimento', [$start, $end])
                    ->orWhere(fn($sub) => $sub->whereNull('data_vencimento')->whereBetween('data_emissao', [$start, $end]));
            });
        }

        $recordsTotal = $query->count();

        // Ordenação
        $orderColumn = 'data_vencimento';
        $orderDir = 'desc';

        if ($request->has('order') && count($request->order)) {
            $order = $request->order[0];
            $columnIndex = (int) $order['column'];
            $orderDir = $order['dir'];

            $columnMap = [
                0 => 'data_vencimento',
                1 => 'descricao',
                2 => null,          // filiais - não ordenável
                3 => 'tipo_documento',
                4 => null,          // nº documento - não ordenável
                5 => null,          // forma pgto - não ordenável
                6 => 'valor_total',
                7 => 'status',
                8 => null,          // ações
            ];

            $mapped = $columnMap[$columnIndex] ?? null;
            if ($mapped) {
                $orderColumn = $mapped;
            }
        }

        $query->orderBy($orderColumn, $orderDir);

        // Paginação
        $startOffset = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 50);
        $repasses = $query->skip($startOffset)->take($length)->get();

        $data = $repasses->map(function ($repasse) use ($companyId) {
            $filiais = $repasse->itens->map(fn($item) => $item->companyDestino?->name)->filter()->implode(', ');
            $isOrigem = $repasse->company_origem_id === (int) $companyId;

            // Data de vencimento formatada
            $dataVencimento = '-';
            if ($repasse->data_vencimento) {
                $dataVencimento = $repasse->data_vencimento->format('d/m/Y');
            } elseif ($repasse->data_emissao) {
                $dataVencimento = $repasse->data_emissao->format('d/m/Y');
            }

            // Descrição com ícone de direção
            $direcaoIcon = $isOrigem
                ? '<i class="bi bi-arrow-up-right text-danger me-1" title="Enviado"></i>'
                : '<i class="bi bi-arrow-down-left text-success me-1" title="Recebido"></i>';
            $descricaoTexto = e($repasse->descricao ?? 'Repasse');
            $descricaoHtml = '<div class="fw-bold"><a href="#" onclick="verRepasse(' . $repasse->id . '); return false;" class="text-gray-800 text-hover-primary">' . $direcaoIcon . $descricaoTexto . '</a></div>';

            // Sub-info: competência + origem/destino
            $subInfo = [];
            if ($repasse->competencia) {
                $subInfo[] = '<span class="badge badge-light-primary py-1 px-2 fs-8">' . e($repasse->competencia) . '</span>';
            }
            $origemDestinoLabel = $isOrigem
                ? 'Para: ' . ($filiais ?: '-')
                : 'De: ' . ($repasse->companyOrigem?->name ?? '-');
            $subInfo[] = '<span class="text-muted"><i class="bi bi-building me-1"></i>' . $origemDestinoLabel . '</span>';
            if (!empty($subInfo)) {
                $descricaoHtml .= '<div class="d-flex align-items-center gap-2 mt-1">' . implode(' ', $subInfo) . '</div>';
            }

            // Filiais coluna
            $filiaisHtml = $filiais ?: '-';

            // Status badge
            $statusBadges = [
                'pendente' => '<div class="badge fw-bold py-2 px-3 badge-light-warning">Pendente</div>',
                'executado' => '<div class="badge fw-bold py-2 px-3 badge-light-success">Executado</div>',
                'cancelado' => '<div class="badge fw-bold py-2 px-3 badge-light-danger">Cancelado</div>',
            ];
            $statusBadge = $statusBadges[$repasse->status] ?? '<div class="badge fw-bold py-2 px-3 badge-light-secondary">' . ucfirst($repasse->status) . '</div>';

            // Valor formatado
            $valorClass = $isOrigem ? 'text-danger' : 'text-success';
            $valorHtml = '<span class="fw-bold ' . $valorClass . '">R$ ' . number_format((float) $repasse->valor_total, 2, ',', '.') . '</span>';

            // Ações
            $actionsHtml = '<div class="d-flex justify-content-end gap-1">';
            $actionsHtml .= '<button class="btn btn-sm btn-icon btn-light-primary" title="Ver detalhes" onclick="verRepasse(' . $repasse->id . ')"><i class="bi bi-eye"></i></button>';
            if ($repasse->status === 'pendente' && $isOrigem) {
                $actionsHtml .= '<button class="btn btn-sm btn-icon btn-light-info" title="Editar" onclick="editarRepasse(' . $repasse->id . ')"><i class="bi bi-pencil"></i></button>';
                $actionsHtml .= '<button class="btn btn-sm btn-icon btn-light-success" title="Executar" onclick="executarRepasse(' . $repasse->id . ')"><i class="bi bi-check-circle"></i></button>';
                $actionsHtml .= '<button class="btn btn-sm btn-icon btn-light-danger" title="Cancelar" onclick="cancelarRepasse(' . $repasse->id . ')"><i class="bi bi-x-circle"></i></button>';
            }
            $actionsHtml .= '</div>';

            // Retorno como array indexado para tenant-datatable-pane.js
            return [
                $dataVencimento,                                    // 0 - Vencimento
                $descricaoHtml,                                     // 1 - Descrição
                $filiaisHtml,                                       // 2 - Filial(is)
                e($repasse->tipo_documento ?? '-'),                 // 3 - Tipo Doc.
                e($repasse->numero_documento ?? '-'),               // 4 - Nº Doc.
                $repasse->formaPagamento?->nome ?? '-',             // 5 - Forma Pgto.
                $valorHtml,                                         // 6 - Valor
                $statusBadge,                                       // 7 - Status
                $actionsHtml,                                       // 8 - Ações
            ];
        });

        return response()->json([
            'draw' => (int) $request->input('draw', 1),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'data' => $data,
        ]);
    }

    /**
     * Cria um novo repasse.
     */
    public function store(StoreRepasseRequest $request): JsonResponse
    {
        $companyId = session('active_company_id');

        if (!$companyId) {
            return response()->json([
                'success' => false,
                'message' => 'Empresa ativa não encontrada na sessão.',
            ], 422);
        }

        // Verificar se a empresa é matriz
        $company = Company::find($companyId);
        if ($company?->type !== 'matriz') {
            return response()->json([
                'success' => false,
                'message' => 'Apenas a matriz pode criar repasses.',
            ], 403);
        }

        $validated = $request->validated();
        $validated['company_origem_id'] = $companyId;

        $executarImediato = filter_var($validated['executar_imediato'] ?? false, FILTER_VALIDATE_BOOLEAN);

        try {
            $repasse = $this->repasseService->criar($validated, $executarImediato);

            return response()->json([
                'success' => true,
                'message' => $executarImediato
                    ? 'Repasse criado e executado com sucesso!'
                    : 'Repasse criado com sucesso! Status: Pendente.',
                'repasse_id' => $repasse->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao criar repasse', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar repasse: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Retorna dados de um repasse para visualização/edição.
     */
    public function show(int $id): JsonResponse
    {
        $companyId = session('active_company_id');

        $repasse = Repasse::with(['itens.companyDestino', 'itens.entidadeDestino', 'entidadeOrigem', 'formaPagamento'])
            ->where(function ($q) use ($companyId) {
                $q->where('company_origem_id', $companyId)
                    ->orWhereHas('itens', function ($sub) use ($companyId) {
                        $sub->where('company_destino_id', $companyId);
                    });
            })
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'repasse' => [
                'id' => $repasse->id,
                'entidade_origem_id' => $repasse->entidade_origem_id,
                'tipo' => $repasse->tipo,
                'criterio_rateio' => $repasse->criterio_rateio,
                'valor_total' => number_format((float) $repasse->valor_total, 2, ',', '.'),
                'data_emissao' => $repasse->data_emissao ? $repasse->data_emissao->format('d/m/Y') : null,
                'data_entrada' => $repasse->data_entrada ? $repasse->data_entrada->format('d/m/Y') : null,
                'data_vencimento' => $repasse->data_vencimento ? $repasse->data_vencimento->format('d/m/Y') : null,
                'competencia' => $repasse->competencia,
                'tipo_documento' => $repasse->tipo_documento,
                'numero_documento' => $repasse->numero_documento,
                'forma_pagamento_id' => $repasse->forma_pagamento_id,
                'forma_recebimento_id' => $repasse->forma_recebimento_id,
                'descricao' => $repasse->descricao,
                'status' => $repasse->status,
                'itens' => $repasse->itens->map(fn($item) => [
                    'id' => $item->id,
                    'company_destino_id' => $item->company_destino_id,
                    'company_destino_nome' => $item->companyDestino?->name,
                    'entidade_destino_id' => $item->entidade_destino_id,
                    'entidade_destino_nome' => $item->entidadeDestino?->nome,
                    'percentual' => $item->percentual,
                    'valor' => number_format($item->valor, 2, ',', '.'),
                ]),
            ],
        ]);
    }

    /**
     * Atualiza um repasse pendente.
     */
    public function update(UpdateRepasseRequest $request, int $id): JsonResponse
    {
        $companyId = session('active_company_id');

        if (!$companyId) {
            return response()->json([
                'success' => false,
                'message' => 'Empresa ativa não encontrada na sessão.',
            ], 422);
        }

        // Verificar se a empresa é matriz
        $company = Company::find($companyId);
        if ($company?->type !== 'matriz') {
            return response()->json([
                'success' => false,
                'message' => 'Apenas a matriz pode editar repasses.',
            ], 403);
        }

        $repasse = Repasse::where('company_origem_id', $companyId)
            ->findOrFail($id);

        if (!$repasse->isPendente()) {
            return response()->json([
                'success' => false,
                'message' => 'Apenas repasses pendentes podem ser editados.',
            ], 422);
        }

        $validated = $request->validated();

        try {
            $repasse = $this->repasseService->atualizar($repasse, $validated);

            return response()->json([
                'success' => true,
                'message' => 'Repasse atualizado com sucesso!',
                'repasse_id' => $repasse->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar repasse', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar repasse: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Executa um repasse pendente.
     */
    public function executar(int $id): JsonResponse
    {
        $companyId = session('active_company_id');

        $repasse = Repasse::where('company_origem_id', $companyId)
            ->findOrFail($id);

        try {
            $this->repasseService->executarRepasse($repasse);

            return response()->json([
                'success' => true,
                'message' => 'Repasse executado com sucesso!',
            ]);
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Cancela um repasse pendente.
     */
    public function cancelar(int $id): JsonResponse
    {
        $companyId = session('active_company_id');

        $repasse = Repasse::where('company_origem_id', $companyId)
            ->findOrFail($id);

        try {
            $this->repasseService->cancelar($repasse);

            return response()->json([
                'success' => true,
                'message' => 'Repasse cancelado com sucesso.',
            ]);
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Retorna as filiais da empresa ativa (para popular selects).
     */
    public function filiais(): JsonResponse
    {
        $companyId = session('active_company_id');
        $filiais = $this->repasseService->getFiliais($companyId);

        return response()->json([
            'success' => true,
            'filiais' => $filiais->map(fn($f) => [
                'id' => $f->id,
                'name' => $f->name,
            ]),
        ]);
    }

    /**
     * Retorna entidades financeiras de uma company (para popular selects dinâmicos).
     */
    public function entidadesPorCompany(int $companyId): JsonResponse
    {
        $activeCompanyId = session('active_company_id');

        // Apenas permite ver entidades de filiais da matrix ativa ou dela mesma
        $company = Company::find($companyId);
        if (!$company || ($company->parent_id !== $activeCompanyId && $company->id !== $activeCompanyId)) {
            return response()->json(['success' => false, 'message' => 'Acesso negado.'], 403);
        }

        $entidades = $this->repasseService->getEntidadesDeCompany($companyId);

        return response()->json([
            'success' => true,
            'entidades' => $entidades->map(fn($e) => [
                'id' => $e->id,
                'nome' => $e->nome,
                'tipo' => $e->tipo,
            ]),
        ]);
    }
}
