<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Dizimo;
use App\Models\Fiel;
use App\Models\EntidadeFinanceira;
use App\Models\Movimentacao;
use App\Models\Financeiro\TransacaoFinanceira;
use App\Services\Dizimo\DizimoFinanceiroService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Flasher;

class DizimoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $companyId = session('active_company_id');

        if ($request->ajax()) {
            $dizimos = Dizimo::with(['fiel', 'entidadeFinanceira', 'movimentacao'])
                ->where('company_id', $companyId)
                ->select('dizimos.*');

            // Filtros
            if ($request->has('fiel_id') && $request->fiel_id) {
                $dizimos->where('fiel_id', $request->fiel_id);
            }

            if ($request->has('tipo') && $request->tipo) {
                $dizimos->where('tipo', $request->tipo);
            }

            if ($request->has('data_inicio') && $request->data_inicio) {
                $dizimos->whereDate('data_pagamento', '>=', $request->data_inicio);
            }

            if ($request->has('data_fim') && $request->data_fim) {
                $dizimos->whereDate('data_pagamento', '<=', $request->data_fim);
            }

            return DataTables::of($dizimos)
                ->addColumn('checkbox', function ($row) {
                    return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" value="' . $row->id . '" />
                            </div>';
                })
                ->addColumn('fiel_nome', function ($row) {
                    return $row->fiel ? $row->fiel->nome_completo : '-';
                })
                ->addColumn('valor_formatado', function ($row) {
                    return 'R$ ' . number_format($row->valor, 2, ',', '.');
                })
                ->addColumn('data_formatada', function ($row) {
                    return $row->data_pagamento ? $row->data_pagamento->format('d/m/Y') : '-';
                })
                ->addColumn('status_integracao', function ($row) {
                    if ($row->integrado_financeiro) {
                        return '<span class="badge badge-success">Integrado</span>';
                    }
                    return '<span class="badge badge-warning">Não Integrado</span>';
                })
                ->addColumn('action', function ($row) {
                    $editBtn = '<button type="button" class="btn btn-sm btn-light btn-active-light-primary"
                                    onclick="editDizimo(' . $row->id . ')">
                                    <i class="bi bi-pencil"></i> Editar
                                </button>';

                    $deleteBtn = '<button type="button" class="btn btn-sm btn-light btn-active-light-danger"
                                    onclick="deleteDizimo(' . $row->id . ')">
                                    <i class="bi bi-trash"></i> Excluir
                                </button>';

                    return $editBtn . ' ' . $deleteBtn;
                })
                ->rawColumns(['checkbox', 'status_integracao', 'action'])
                ->make(true);
        }

        // Estatísticas
        $totalDizimos = Dizimo::where('company_id', $companyId)->sum('valor');
        $totalMes = Dizimo::where('company_id', $companyId)
            ->whereMonth('data_pagamento', Carbon::now()->month)
            ->whereYear('data_pagamento', Carbon::now()->year)
            ->sum('valor');

        $totalDizimistas = Dizimo::where('company_id', $companyId)
            ->where('tipo', 'Dízimo')
            ->distinct('fiel_id')
            ->count('fiel_id');

        $fieis = Fiel::where('company_id', $companyId)
            ->orderBy('nome_completo')
            ->get();

                $entidades = EntidadeFinanceira::where('company_id', $companyId)
                    ->whereIn('tipo', ['caixa', 'banco'])
                    ->orderBy('tipo')
                    ->orderBy('nome')
                    ->get();

        return view('app.dizimos.index', compact(
            'totalDizimos',
            'totalMes',
            'totalDizimistas',
            'fieis',
            'entidades'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $companyId = session('active_company_id');

        $fieis = Fiel::where('company_id', $companyId)
            ->orderBy('nome_completo')
            ->get();

                $entidades = EntidadeFinanceira::where('company_id', $companyId)
                    ->whereIn('tipo', ['caixa', 'banco'])
                    ->orderBy('tipo')
                    ->orderBy('nome')
                    ->get();

        return view('app.dizimos.create', compact('fieis', 'entidades'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fiel_id' => 'required|exists:fieis,id',
            'tipo' => 'required|in:Dízimo,Doação,Oferta,Outro',
            'valor' => 'required|numeric|min:0.01',
            'data_pagamento' => 'required|date',
            'forma_pagamento' => 'required|in:Dinheiro,PIX,Cartão de Débito,Cartão de Crédito,Transferência,Cheque,Outro',
            'entidade_financeira_id' => 'nullable|exists:entidades_financeiras,id',
            'observacoes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro de validação',
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $companyId = session('active_company_id');
            $user = Auth::user();

            $dizimo = Dizimo::create([
                'company_id' => $companyId,
                'fiel_id' => $request->fiel_id,
                'tipo' => $request->tipo,
                'valor' => $request->valor,
                'data_pagamento' => $request->data_pagamento,
                'forma_pagamento' => $request->forma_pagamento,
                'entidade_financeira_id' => $request->entidade_financeira_id,
                'observacoes' => $request->observacoes,
                'created_by' => $user->id,
                'created_by_name' => $user->name,
                'updated_by' => $user->id,
                'updated_by_name' => $user->name,
            ]);

            // Integrar com financeiro se solicitado
            if ($request->has('integrar_financeiro') && $request->boolean('integrar_financeiro') && $request->entidade_financeira_id) {
                $this->integrarComFinanceiro($dizimo, $request);
            } else {
                // Garantir que está marcado como não integrado
                $dizimo->update([
                    'integrado_financeiro' => false
                ]);
            }

            // Atualizar última contribuição do fiel
            $fiel = Fiel::find($request->fiel_id);
            if ($fiel && $fiel->tithe) {
                $fiel->tithe->update([
                    'ultima_contribuicao' => $request->data_pagamento,
                ]);
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Dízimo/Doação registrado com sucesso!'
                ]);
            }

            Flasher::addSuccess('Dízimo/Doação registrado com sucesso!');
            return redirect()->route('dizimos.index');

        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao registrar dízimo/doação: ' . $e->getMessage()
                ], 500);
            }
            Flasher::addError('Erro ao registrar dízimo/doação: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $dizimo = Dizimo::with(['fiel', 'entidadeFinanceira', 'movimentacao'])
            ->findOrFail($id);

        return redirect()->route('dizimos.edit', $dizimo->id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $dizimo = Dizimo::with(['fiel', 'entidadeFinanceira'])->findOrFail($id);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $dizimo->id,
                    'fiel_id' => $dizimo->fiel_id,
                    'tipo' => $dizimo->tipo,
                    'valor' => number_format((float) $dizimo->valor, 2, ',', '.'),
                    'data_pagamento' => $dizimo->data_pagamento->format('Y-m-d'),
                    'forma_pagamento' => $dizimo->forma_pagamento,
                    'entidade_financeira_id' => $dizimo->entidade_financeira_id,
                    'observacoes' => $dizimo->observacoes,
                    'integrado_financeiro' => $dizimo->integrado_financeiro,
                ]
            ]);
        }

        $companyId = session('active_company_id');

        $fieis = Fiel::where('company_id', $companyId)
            ->orderBy('nome_completo')
            ->get();

                $entidades = EntidadeFinanceira::where('company_id', $companyId)
                    ->whereIn('tipo', ['caixa', 'banco'])
                    ->orderBy('tipo')
                    ->orderBy('nome')
                    ->get();

        return view('app.dizimos.edit', compact('dizimo', 'fieis', 'entidades'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'fiel_id' => 'required|exists:fieis,id',
            'tipo' => 'required|in:Dízimo,Doação,Oferta,Outro',
            'valor' => 'required|numeric|min:0.01',
            'data_pagamento' => 'required|date',
            'forma_pagamento' => 'required|in:Dinheiro,PIX,Cartão de Débito,Cartão de Crédito,Transferência,Cheque,Outro',
            'entidade_financeira_id' => 'nullable|exists:entidades_financeiras,id',
            'observacoes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro de validação',
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $dizimo = Dizimo::findOrFail($id);
            $user = Auth::user();

            $dizimo->update([
                'fiel_id' => $request->fiel_id,
                'tipo' => $request->tipo,
                'valor' => $request->valor,
                'data_pagamento' => $request->data_pagamento,
                'forma_pagamento' => $request->forma_pagamento,
                'entidade_financeira_id' => $request->entidade_financeira_id,
                'observacoes' => $request->observacoes,
                'updated_by' => $user->id,
                'updated_by_name' => $user->name,
            ]);

            // Gerenciar integração com financeiro
            $integrar = $request->has('integrar_financeiro') && $request->boolean('integrar_financeiro') && $request->entidade_financeira_id;
            
            // Recarregar relacionamento fiel se necessário
            if (!$dizimo->relationLoaded('fiel')) {
                $dizimo->load('fiel');
            }
            
            if ($integrar) {
                // Se já está integrado, usar o método de integração para atualizar
                // Isso garante que a formatação correta seja aplicada
                if ($dizimo->movimentacao_id) {
                    // Recarregar relacionamento antes de atualizar
                    if (!$dizimo->relationLoaded('fiel')) {
                        $dizimo->load('fiel');
                    }
                    // Atualizar dados do dízimo primeiro para garantir que os valores estejam corretos
                    $dizimo->refresh();
                    // Usar o método de integração que já formata corretamente
                    $this->integrarComFinanceiro($dizimo, $request);
                } else {
                    // Se não está integrado, criar movimentação e transação
                    $this->integrarComFinanceiro($dizimo, $request);
                }
            } else {
                // Se desmarcou integração, remover movimentação e transação
                if ($dizimo->movimentacao_id) {
                    $movimentacao = Movimentacao::find($dizimo->movimentacao_id);
                    if ($movimentacao) {
                        // Remover transação financeira primeiro (por causa da foreign key)
                        TransacaoFinanceira::where('movimentacao_id', $movimentacao->id)->delete();
                        // Remover movimentação
                        $movimentacao->delete();
                    }
                    $dizimo->update([
                        'movimentacao_id' => null,
                        'integrado_financeiro' => false
                    ]);
                } else {
                    $dizimo->update(['integrado_financeiro' => false]);
                }
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Dízimo/Doação atualizado com sucesso!'
                ]);
            }

            Flasher::addSuccess('Dízimo/Doação atualizado com sucesso!');
            return redirect()->route('dizimos.index');

        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao atualizar dízimo/doação: ' . $e->getMessage()
                ], 500);
            }
            Flasher::addError('Erro ao atualizar dízimo/doação: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $dizimo = Dizimo::findOrFail($id);

            // Remover movimentação financeira e transação se existir
            if ($dizimo->movimentacao_id) {
                $movimentacao = Movimentacao::find($dizimo->movimentacao_id);
                if ($movimentacao) {
                    // Remover transação financeira primeiro (por causa da foreign key)
                    TransacaoFinanceira::where('movimentacao_id', $movimentacao->id)->delete();
                    // Remover movimentação
                    $movimentacao->delete();
                }
            }

            $dizimo->delete();

            DB::commit();

            Flasher::addSuccess('Dízimo/Doação excluído com sucesso!');
            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            DB::rollBack();
            Flasher::addError('Erro ao excluir dízimo/doação: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Integrar dízimo com o módulo financeiro
     */
    private function integrarComFinanceiro(Dizimo $dizimo, Request $request)
    {
        $user = Auth::user();
        $companyId = session('active_company_id');
        
        // Carregar relacionamento fiel se necessário
        if (!$dizimo->relationLoaded('fiel')) {
            $dizimo->load('fiel');
        }
        
        $nomeFiel = $dizimo->fiel ? $dizimo->fiel->nome_completo : 'Fiel Desconhecido';
        
        // Formatar data para mês/ano (MM/YYYY)
        $dataFormatada = Carbon::parse($dizimo->data_pagamento)->format('m/Y');
        
        // Determinar tipo de recebimento baseado no tipo do dízimo
        $tipoRecebimento = strtoupper($dizimo->tipo);
        if ($dizimo->tipo === 'Dízimo') {
            $tipoRecebimento = 'RECEBIMENTO DÍZIMO';
        } elseif ($dizimo->tipo === 'Doação') {
            $tipoRecebimento = 'RECEBIMENTO DOAÇÃO';
        } elseif ($dizimo->tipo === 'Oferta') {
            $tipoRecebimento = 'RECEBIMENTO OFERTA';
        } else {
            $tipoRecebimento = 'RECEBIMENTO ' . strtoupper($dizimo->tipo);
        }
        
        // Formatar descrição: número_registro - TIPO_RECEBIMENTO MM/YYYY NOME_DOADOR
        $descricao = sprintf(
            '%d - %s %s %s',
            $dizimo->id,
            $tipoRecebimento,
            $dataFormatada,
            strtoupper($nomeFiel)
        );
        
        // Formatar histórico complementar: descrição + tipo de recebimento + observações (se houver)
        $historicoComplementar = $descricao . ' ' . $tipoRecebimento;
        if (!empty($dizimo->observacoes)) {
            $historicoComplementar .= ' - ' . $dizimo->observacoes;
        }

        // Criar ou atualizar movimentação
        if ($dizimo->movimentacao_id) {
            $movimentacao = Movimentacao::find($dizimo->movimentacao_id);
            if ($movimentacao) {
                $movimentacao->update([
                    'entidade_id' => $request->entidade_financeira_id,
                    'valor' => $dizimo->valor,
                    'data' => $dizimo->data_pagamento,
                    'descricao' => $descricao,
                    'updated_by' => $user->id,
                    'updated_by_name' => $user->name,
                ]);
            } else {
                $movimentacao = Movimentacao::create([
                    'company_id' => $companyId,
                    'entidade_id' => $request->entidade_financeira_id,
                    'tipo' => 'entrada',
                    'valor' => $dizimo->valor,
                    'data' => $dizimo->data_pagamento,
                    'descricao' => $descricao,
                    'created_by' => $user->id,
                    'created_by_name' => $user->name,
                    'updated_by' => $user->id,
                    'updated_by_name' => $user->name,
                ]);
                $dizimo->update(['movimentacao_id' => $movimentacao->id]);
            }
        } else {
            // Criar movimentação
            $movimentacao = Movimentacao::create([
                'company_id' => $companyId,
                'entidade_id' => $request->entidade_financeira_id,
                'tipo' => 'entrada',
                'valor' => $dizimo->valor,
                'data' => $dizimo->data_pagamento,
                'descricao' => $descricao,
                'created_by' => $user->id,
                'created_by_name' => $user->name,
                'updated_by' => $user->id,
                'updated_by_name' => $user->name,
            ]);
        }

        // Criar ou atualizar transação financeira
        $transacao = TransacaoFinanceira::where('movimentacao_id', $movimentacao->id)->first();
        
        if ($transacao) {
            $transacao->update([
                'data_competencia' => $dizimo->data_pagamento,
                'entidade_id' => $request->entidade_financeira_id,
                'valor' => $dizimo->valor,
                'descricao' => $descricao,
                'tipo_documento' => $dizimo->tipo,
                'numero_documento' => 'DZ-' . $dizimo->id,
                'origem' => 'Dízimo/Doação',
                'historico_complementar' => $historicoComplementar,
                'updated_by' => $user->id,
                'updated_by_name' => $user->name,
            ]);
        } else {
            TransacaoFinanceira::create([
                'company_id' => $companyId,
                'data_competencia' => $dizimo->data_pagamento,
                'entidade_id' => $request->entidade_financeira_id,
                'tipo' => 'entrada',
                'valor' => $dizimo->valor,
                'descricao' => $descricao,
                'movimentacao_id' => $movimentacao->id,
                'tipo_documento' => $dizimo->tipo,
                'numero_documento' => 'DZ-' . $dizimo->id,
                'origem' => 'Dízimo/Doação',
                'historico_complementar' => $historicoComplementar,
                'created_by' => $user->id,
                'created_by_name' => $user->name,
                'updated_by' => $user->id,
                'updated_by_name' => $user->name,
            ]);
        }

        // Atualizar dízimo
        $dizimo->update([
            'movimentacao_id' => $movimentacao->id,
            'integrado_financeiro' => true,
        ]);
    }

    // =====================================================================
    // API JSON usada pelo SPA React (/app/dizimos)
    //
    // Endpoints registrados em routes/tenant.php sob o middleware
    // `can:dizimos.index` (bloco do React). Os métodos Blade (index/store/
    // update/destroy/edit/create/show) acima permanecem para retro-compat.
    // =====================================================================

    /**
     * Lista paginada para a DataGrid React.
     * Aceita: search, fiel_id, tipo, data_inicio, data_fim, sort_by, sort_dir, page, per_page.
     */
    public function apiList(Request $request)
    {
        $companyId = session('active_company_id');

        if (! $companyId) {
            return response()->json([
                'data' => [], 'total' => 0, 'per_page' => 20, 'current_page' => 1, 'last_page' => 1,
            ]);
        }

        $query = Dizimo::with(['fiel:id,nome_completo,avatar', 'entidadeFinanceira:id,nome,tipo'])
            ->where('company_id', $companyId);

        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('fiel', fn ($f) => $f->where('nome_completo', 'like', "%{$search}%"))
                  ->orWhere('observacoes', 'like', "%{$search}%");
            });
        }

        if ($request->filled('fiel_id')) {
            $query->where('fiel_id', (int) $request->input('fiel_id'));
        }

        if ($request->filled('tipo')) {
            $tipos = array_filter(array_map('trim', explode(',', (string) $request->input('tipo'))));
            if (! empty($tipos)) {
                $query->whereIn('tipo', $tipos);
            }
        }

        if ($dataInicio = $request->input('data_inicio')) {
            $query->whereDate('data_pagamento', '>=', $dataInicio);
        }
        if ($dataFim = $request->input('data_fim')) {
            $query->whereDate('data_pagamento', '<=', $dataFim);
        }

        if ($request->filled('valor_min')) {
            $query->where('valor', '>=', (float) $request->input('valor_min'));
        }
        if ($request->filled('valor_max')) {
            $query->where('valor', '<=', (float) $request->input('valor_max'));
        }

        if ($request->filled('forma_pagamento')) {
            $query->where('forma_pagamento', $request->input('forma_pagamento'));
        }

        if ($request->filled('integrado')) {
            $query->where('integrado_financeiro', filter_var($request->input('integrado'), FILTER_VALIDATE_BOOLEAN));
        }

        // Sort whitelisted
        $sortBy  = $request->input('sort_by', 'data_pagamento');
        $sortDir = strtolower((string) $request->input('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $allowed = ['data_pagamento', 'valor', 'tipo', 'forma_pagamento', 'created_at', 'id'];
        if (! in_array($sortBy, $allowed, true)) {
            $sortBy = 'data_pagamento';
        }
        $query->orderBy($sortBy, $sortDir);

        $perPage = max(1, min(100, (int) $request->input('per_page', 20)));
        $page    = max(1, (int) $request->input('page', 1));
        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        // Stats globais (independem dos filtros de busca/paginação) — só do period filtrado.
        $statsQuery = Dizimo::where('company_id', $companyId);
        if ($dataInicio = $request->input('data_inicio')) $statsQuery->whereDate('data_pagamento', '>=', $dataInicio);
        if ($dataFim    = $request->input('data_fim'))    $statsQuery->whereDate('data_pagamento', '<=', $dataFim);

        $totalGeral = (float) $statsQuery->clone()->sum('valor');
        $totalDizimo = (float) $statsQuery->clone()->where('tipo', 'Dízimo')->sum('valor');
        $totalDoacao = (float) $statsQuery->clone()->where('tipo', 'Doação')->sum('valor');
        $totalOferta = (float) $statsQuery->clone()->where('tipo', 'Oferta')->sum('valor');

        $items = $paginator->getCollection()->map(fn (Dizimo $d) => [
            'id'                    => $d->id,
            'tipo'                  => $d->tipo,
            'valor'                 => (float) $d->valor,
            'valor_formatado'       => 'R$ ' . number_format((float) $d->valor, 2, ',', '.'),
            'data_pagamento'        => $d->data_pagamento?->format('Y-m-d'),
            'data_pagamento_formatada' => $d->data_pagamento?->format('d/m/Y'),
            'forma_pagamento'       => $d->forma_pagamento,
            'numero_documento'      => $d->numero_documento,
            'observacoes'           => $d->observacoes,
            'integrado_financeiro'  => (bool) $d->integrado_financeiro,
            'movimentacao_id'       => $d->movimentacao_id,
            'fiel'                  => $d->fiel ? [
                'id'            => $d->fiel->id,
                'nome_completo' => $d->fiel->nome_completo,
                'avatar_url'    => $d->fiel->avatar
                    ? '/file/' . ltrim(preg_replace('#^public/#', '', $d->fiel->avatar), '/')
                    : null,
            ] : null,
            'entidade_financeira'   => $d->entidadeFinanceira ? [
                'id'   => $d->entidadeFinanceira->id,
                'nome' => $d->entidadeFinanceira->nome,
                'tipo' => $d->entidadeFinanceira->tipo,
            ] : null,
            'entidade_financeira_id' => $d->entidade_financeira_id,
        ])->values();

        return response()->json([
            'data'         => $items,
            'total'        => $paginator->total(),
            'per_page'     => $paginator->perPage(),
            'current_page' => $paginator->currentPage(),
            'last_page'    => $paginator->lastPage(),
            'stats' => [
                'total'    => $totalGeral,
                'dizimo'   => $totalDizimo,
                'doacao'   => $totalDoacao,
                'oferta'   => $totalOferta,
            ],
        ]);
    }

    /**
     * Cria um (ou N — quando período) Dizimos via Service centralizado.
     * Retorna a coleção criada.
     */
    public function apiStore(Request $request, DizimoFinanceiroService $service)
    {
        $companyId = session('active_company_id');
        if (! $companyId) {
            return response()->json(['success' => false, 'message' => 'Empresa ativa não encontrada.'], 422);
        }

        $integrar = filter_var($request->input('integrar_financeiro', false), FILTER_VALIDATE_BOOLEAN);

        $rules = [
            'tipo'                   => ['required', 'in:' . implode(',', DizimoFinanceiroService::TIPOS_VALIDOS)],
            'fiel_id'                => ['required', 'integer', 'exists:fieis,id'],
            'data_pagamento'         => ['required', 'date'],
            'valor'                  => ['required', 'numeric', 'min:0.01'],
            'forma_pagamento'        => ['required', 'in:' . implode(',', DizimoFinanceiroService::FORMAS_PAGAMENTO)],
            'numero_documento'       => ['nullable', 'string', 'max:50'],
            'entidade_financeira_id' => $integrar
                ? ['required', 'integer', 'exists:entidades_financeiras,id']
                : ['nullable', 'integer', 'exists:entidades_financeiras,id'],
            'observacoes'            => ['nullable', 'string', 'max:1000'],
            'integrar_financeiro'    => ['sometimes', 'boolean'],

            'periodo'                => ['sometimes', 'boolean'],
            'mes_inicio'             => ['nullable', 'string', 'regex:/^\d{2}\/\d{4}$/'],
            'mes_fim'                => ['nullable', 'string', 'regex:/^\d{2}\/\d{4}$/'],

            'oferta_adicional'        => ['sometimes', 'boolean'],
            'oferta_adicional_valor'  => ['nullable', 'numeric', 'min:0'],
            'oferta_adicional_ref'    => ['nullable', 'string', 'max:255'],
        ];

        $data = $request->validate($rules);
        $data['integrar_financeiro'] = (bool) ($data['integrar_financeiro'] ?? false);
        $data['periodo']             = (bool) ($data['periodo'] ?? false);
        $data['oferta_adicional']    = (bool) ($data['oferta_adicional'] ?? false);

        if ($data['periodo'] && (empty($data['mes_inicio']) || empty($data['mes_fim']))) {
            return response()->json([
                'success' => false,
                'message' => 'Mês inicial e final são obrigatórios quando o período está habilitado.',
                'errors'  => ['mes_inicio' => ['Informe o mês inicial.'], 'mes_fim' => ['Informe o mês final.']],
            ], 422);
        }

        try {
            $criados = $service->criar($data, Auth::id(), (int) $companyId);

            return response()->json([
                'success' => true,
                'message' => 'Lançamento(s) registrado(s) com sucesso.',
                'count'   => $criados->count(),
                'ids'     => $criados->pluck('id')->all(),
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao registrar lançamento: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Detalhes de um Dizimo para edição via React.
     */
    public function apiShow($id)
    {
        $companyId = session('active_company_id');
        $dizimo = Dizimo::with(['fiel:id,nome_completo,avatar', 'entidadeFinanceira:id,nome,tipo'])
            ->where('company_id', $companyId)
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id'                     => $dizimo->id,
                'tipo'                   => $dizimo->tipo,
                'valor'                  => (float) $dizimo->valor,
                'data_pagamento'         => $dizimo->data_pagamento?->format('Y-m-d'),
                'forma_pagamento'        => $dizimo->forma_pagamento,
                'numero_documento'       => $dizimo->numero_documento,
                'entidade_financeira_id' => $dizimo->entidade_financeira_id,
                'observacoes'            => $dizimo->observacoes,
                'integrado_financeiro'   => (bool) $dizimo->integrado_financeiro,
                'fiel' => $dizimo->fiel ? [
                    'id'            => $dizimo->fiel->id,
                    'nome_completo' => $dizimo->fiel->nome_completo,
                    'avatar_url'    => $dizimo->fiel->avatar
                        ? '/file/' . ltrim(preg_replace('#^public/#', '', $dizimo->fiel->avatar), '/')
                        : null,
                ] : null,
            ],
        ]);
    }

    /**
     * Atualiza um Dizimo (sem suporte a período aqui — período só na criação).
     * Aceita POST com `_method=PUT/PATCH` (igual ao Fieis) ou PUT direto.
     */
    public function apiUpdate(Request $request, $id, DizimoFinanceiroService $service)
    {
        $companyId = session('active_company_id');
        $dizimo = Dizimo::where('company_id', $companyId)->findOrFail($id);

        $integrar = filter_var($request->input('integrar_financeiro', false), FILTER_VALIDATE_BOOLEAN);

        $rules = [
            'tipo'                   => ['required', 'in:' . implode(',', DizimoFinanceiroService::TIPOS_VALIDOS)],
            'fiel_id'                => ['required', 'integer', 'exists:fieis,id'],
            'data_pagamento'         => ['required', 'date'],
            'valor'                  => ['required', 'numeric', 'min:0.01'],
            'forma_pagamento'        => ['required', 'in:' . implode(',', DizimoFinanceiroService::FORMAS_PAGAMENTO)],
            'numero_documento'       => ['nullable', 'string', 'max:50'],
            'entidade_financeira_id' => $integrar
                ? ['required', 'integer', 'exists:entidades_financeiras,id']
                : ['nullable', 'integer', 'exists:entidades_financeiras,id'],
            'observacoes'            => ['nullable', 'string', 'max:1000'],
            'integrar_financeiro'    => ['sometimes', 'boolean'],
        ];

        $data = $request->validate($rules);
        $data['integrar_financeiro'] = (bool) ($data['integrar_financeiro'] ?? false);

        try {
            $atualizado = $service->atualizar($dizimo, $data, Auth::id());

            return response()->json([
                'success' => true,
                'message' => 'Lançamento atualizado com sucesso.',
                'id'      => $atualizado->id,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar lançamento: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function apiDestroy($id, DizimoFinanceiroService $service)
    {
        $companyId = session('active_company_id');
        $dizimo = Dizimo::where('company_id', $companyId)->findOrFail($id);

        try {
            $service->excluir($dizimo);
            return response()->json(['success' => true, 'message' => 'Lançamento excluído.']);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Busca o fiel pelo código de dizimista (ex.: D-0123) na company ativa.
     * Retorna { fiel, comunidade, codigo, dizimista }.
     */
    public function apiLookupCodigo(Request $request)
    {
        $companyId = session('active_company_id');
        $codigo = trim((string) $request->input('codigo', ''));

        if ($codigo === '') {
            return response()->json(['success' => false, 'message' => 'Informe o código do carnê.'], 422);
        }

        $variants = $this->dizimoCodigoLookupVariants($codigo);

        $fiel = Fiel::with(['tithe', 'company:id,name'])
            ->where('company_id', $companyId)
            ->whereHas('tithe', fn ($q) => $q->whereIn('codigo', $variants))
            ->first();

        if (! $fiel) {
            return response()->json(['success' => false, 'message' => 'Carnê não encontrado.'], 404);
        }

        $codigoResolvido = $fiel->tithe?->codigo ?? $codigo;

        return response()->json([
            'success' => true,
            'fiel' => [
                'id'            => $fiel->id,
                'nome_completo' => $fiel->nome_completo,
                'avatar_url'    => $fiel->avatar
                    ? '/file/' . ltrim(preg_replace('#^public/#', '', $fiel->avatar), '/')
                    : null,
            ],
            'comunidade' => $fiel->company?->name,
            'codigo'     => $codigoResolvido,
            'dizimista'  => (bool) ($fiel->tithe?->dizimista ?? false),
        ]);
    }

    /**
     * Gera variantes do código de dizimista/carteirinha para tolerar zeros à esquerda,
     * falta do prefixo D- e registros legados no banco.
     *
     * @return list<string>
     */
    private function dizimoCodigoLookupVariants(string $raw): array
    {
        $s = strtoupper(trim($raw));
        $out = array_values(array_unique(array_filter([$raw, $s], fn ($v) => $v !== '')));

        if (preg_match('/^D-?\s*(\d+)$/i', $s, $m)) {
            $n = (int) $m[1];
            for ($pad = 1; $pad <= 6; $pad++) {
                $out[] = 'D-' . str_pad((string) $n, $pad, '0', STR_PAD_LEFT);
            }
            $out[] = (string) $n;
            $out[] = str_pad((string) $n, 4, '0', STR_PAD_LEFT);
        } elseif (preg_match('/^\d+$/', $s)) {
            $n = (int) $s;
            for ($pad = 1; $pad <= 6; $pad++) {
                $out[] = 'D-' . str_pad((string) $n, $pad, '0', STR_PAD_LEFT);
            }
            $out[] = (string) $n;
            $out[] = str_pad((string) $n, 4, '0', STR_PAD_LEFT);
        }

        return array_values(array_unique(array_filter($out, fn ($v) => $v !== '')));
    }
}

