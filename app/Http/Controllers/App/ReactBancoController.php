<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Http\Requests\Financer\StoreTransacaoFinanceiraRequest;
use App\Models\Bank;
use App\Models\Contabilide\ChartOfAccount;
use App\Models\EntidadeFinanceira;
use App\Models\Financeiro\BankStatement;
use App\Models\Movimentacao;
use Illuminate\Support\Facades\DB;
use App\Models\Financeiro\CostCenter;
use App\Models\Financeiro\ModulosAnexo;
use App\Models\Financeiro\TransacaoFinanceira;
use App\Models\FormasPagamento;
use App\Models\LancamentoPadrao;
use App\Models\Parceiro;
use App\Jobs\LancamentoAgendadoNotificacaoJob;
use App\Services\ConciliacoesPendentesTabData;
use App\Services\ConciliacaoMatchingService;
use App\Services\DomusDocumentoLancamentoService;
use App\Services\TransacaoFinanceiraService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

/**
 * Endpoints REST para as tabelas React do módulo Banco.
 * Retornam JSON limpo (sem HTML) — diferente do BancoController
 * que usa DataTables com arrays posicionais e HTML nas células.
 */
class ReactBancoController extends Controller
{
    // ── Mapa de situações → label + cor ────────────────────────────────────────
    private const SITUACAO_CONFIG = [
        'em_aberto'    => ['label' => 'Em Aberto',    'color' => 'warning'],
        'atrasado'     => ['label' => 'Atrasado',     'color' => 'destructive'],
        'previsto'     => ['label' => 'Previsto',     'color' => 'secondary'],
        'pago_parcial' => ['label' => 'Pago Parcial', 'color' => 'secondary'],
        'pago'         => ['label' => 'Pago',         'color' => 'success'],
        'recebido'     => ['label' => 'Recebido',     'color' => 'success'],
        'desconsiderado' => ['label' => 'Desconsiderado', 'color' => 'secondary'],
    ];

    // ─────────────────────────────────────────────────────────────────────────
    // POST /app/financeiro/banco/lancamento
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Cria um novo lançamento financeiro a partir do drawer React.
     *
     * Body JSON esperado:
     *   tipo               receita | despesa
     *   descricao          string (obrigatório)
     *   valor              numeric  — ex: 1234.56 (float, sem formatação)
     *   data_competencia   Y-m-d ou d/m/Y
     *   data_vencimento    Y-m-d ou d/m/Y (opcional, usa data_competencia se omitido)
     *   entidade_id        int
     *   parceiro_id        int|null
     *   lancamento_padrao_id int|null  (categoria)
     *   cost_center_id     int|null
     *   numero_documento   string|null
     *   historico_complementar string|null
     *   recebido_pago      bool  — marca a situação como pago/recebido ao salvar
     *
     * Recorrência, rateio e demais regras seguem o mesmo fluxo do BancoController (TransacaoFinanceiraService).
     */
    public function store(
        StoreTransacaoFinanceiraRequest $request,
        TransacaoFinanceiraService $transacaoService,
        DomusDocumentoLancamentoService $domusLancamentoService
    ): \Illuminate\Http\JsonResponse {
        $companyId = session('active_company_id');

        if (!$companyId) {
            return response()->json(['success' => false, 'message' => 'Sessão inválida.'], 401);
        }

        try {
            $validatedData = $request->validated();
            $validatedData['company_id'] = $companyId;

            $transacao = $transacaoService->criarLancamento($validatedData, $request);

            if ($transacaoService->deveCriarParcelamentos($request)) {
                $transacaoService->criarParcelamentosParaLancamentoPrincipal($transacao, $validatedData, $request);
            }

            // Domus: anexo e status na transação principal (pai com valor total). Parcelas filhas ficam em parent_id.
            $domusDocumentoId = null;
            if ($request->filled('domus_documento_id')) {
                try {
                    $domusDoc = $domusLancamentoService->findForActiveCompany((int) $request->input('domus_documento_id'));
                    if ($domusDoc) {
                        $domusLancamentoService->markLancadoAndAttachAnexo($domusDoc, $transacao);
                        $domusDocumentoId = $domusDoc->id;
                    }
                } catch (\Exception $e) {
                    Log::warning('Erro ao processar DomusDocumento: '.$e->getMessage());
                }
            }

            // Agenda notificação WhatsApp/banco para o dia do vencimento
            if ($request->boolean('agendado')) {
                $vencimento = Carbon::parse(
                    $transacao->getRawOriginal('data_vencimento') ?? $transacao->getRawOriginal('data_competencia')
                )->startOfDay();

                if ($vencimento->isFuture() || $vencimento->isToday()) {
                    $delay = $vencimento->isFuture() ? $vencimento : now()->addSeconds(5);
                    LancamentoAgendadoNotificacaoJob::dispatch(
                        transacaoId: $transacao->id,
                        companyId:   (int) $companyId,
                        tenantId:    rescue(static fn () => tenancy()->tenant?->id, null, report: false),
                        triggeredBy: Auth::id(),
                    )->delay($delay);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Lançamento criado com sucesso!',
                'id'      => $transacao->id,
                'domus_documento_id' => $domusDocumentoId,
            ], 201);
        } catch (\Throwable $e) {
            Log::error('ReactBancoController::store', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar lançamento: '.$e->getMessage(),
            ], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET /app/financeiro/banco/lancamento/{id}
    // ─────────────────────────────────────────────────────────────────────────

    /** Retorna os dados completos de uma transação para pré-preencher o drawer de edição. */
    public function show(int $id): \Illuminate\Http\JsonResponse
    {
        $companyId = session('active_company_id');
        if (!$companyId) {
            return response()->json(['success' => false, 'message' => 'Sessão inválida.'], 401);
        }

        $t = TransacaoFinanceira::with(['parceiro:id,nome', 'entidadeFinanceira:id,nome', 'lancamentoPadrao:id,description', 'costCenter:id,name', 'fracionamentos'])
            ->where('company_id', $companyId)->findOrFail($id);

        $tipoLabel   = $t->tipo instanceof \BackedEnum ? $t->tipo->value : (string) $t->tipo;
        $situacaoStr = $t->situacao instanceof \BackedEnum ? $t->situacao->value : (string) ($t->situacao ?? 'em_aberto');

        // ── Parcelas ─────────────────────────────────────────────────────────
        // 1. Se esta transação É a principal → suas parcelas estão em $t->parcelas()
        // 2. Se esta transação É uma filha  → usamos parent_id para localizar o pai
        //    e carregar TODAS as parcelas dele
        $parcelamentos   = $t->parcelas()->orderBy('numero_parcela')->get();
        $transacaoPai    = null;

        if ($parcelamentos->isEmpty()) {
            // Via parent_id (mais confiável)
            if ($t->parent_id) {
                $transacaoPai = TransacaoFinanceira::find($t->parent_id);
            }
            // Fallback: via tabela parcelamentos
            if (!$transacaoPai) {
                $parcelamentoRef = \App\Models\Financeiro\Parcelamento::where('transacao_parcela_id', $t->id)->first();
                if ($parcelamentoRef && $parcelamentoRef->transacao_financeira_id) {
                    $transacaoPai = TransacaoFinanceira::find($parcelamentoRef->transacao_financeira_id);
                }
            }
            // Carrega todas as parcelas do pai
            if ($transacaoPai) {
                $parcelamentos = $transacaoPai->parcelas()->orderBy('numero_parcela')->get();
            }
        }

        $parcelamentoStr = null;
        $parcelasData    = [];

        if ($parcelamentos->isNotEmpty()) {
            $totalParcelas   = (int) $parcelamentos->first()->total_parcelas;
            $parcelamentoStr = $totalParcelas . 'x';

            foreach ($parcelamentos as $p) {
                $venc = $p->getRawOriginal('data_vencimento');
                $parcelasData[] = [
                    'vencimento'         => $venc ? Carbon::parse($venc)->format('Y-m-d') : null,
                    'valor'              => round((float) $p->valor, 2),
                    'percentual'         => round((float) $p->percentual, 2),
                    'conta_pagamento_id' => $p->conta_pagamento_id ? (string) $p->conta_pagamento_id : null,
                    'descricao'          => $p->descricao ?? '',
                    'agendado'           => (bool) $p->agendado,
                ];
            }
        }

        // Quando $t é uma parcela filha, os campos do formulário (valor, descricao, etc.)
        // devem vir do PAI — do contrário o "valor" seria só o de uma parcela,
        // causando divergência na validação "soma das parcelas ≠ valor total".
        $fonte = $transacaoPai ?? $t;

        $fonteVencimento = $fonte->getRawOriginal('data_vencimento');
        $fonteCompetencia = $fonte->getRawOriginal('data_competencia');
        $fonteSituacao = $fonte->situacao instanceof \BackedEnum ? $fonte->situacao->value : (string) ($fonte->situacao ?? 'em_aberto');

        // ── Recorrência ──────────────────────────────────────────────────────
        $recorrenciaData = null;
        $recorrenciaConfig = $t->recorrenciaConfig;
        if (!$recorrenciaConfig && $t->recorrencia_id) {
            $recorrenciaConfig = \App\Models\Financeiro\Recorrencia::find($t->recorrencia_id);
        }

        if ($recorrenciaConfig) {
            $recorrenciaData = [
                'id'                   => (string) $recorrenciaConfig->id,
                'nome'                 => $recorrenciaConfig->nome ?? '',
                'intervalo_repeticao'  => (int) $recorrenciaConfig->intervalo_repeticao,
                'frequencia'           => $recorrenciaConfig->frequencia ?? 'mensal',
                'total_ocorrencias'    => (int) $recorrenciaConfig->total_ocorrencias,
                'ocorrencias_geradas'  => (int) $recorrenciaConfig->ocorrencias_geradas,
                'ativo'                => (bool) $recorrenciaConfig->ativo,
            ];
        }

        // Dados extras para o sheet de pagamento
        $parceiroNome = $fonte->parceiro?->nome ?? null;
        $categoriaNome = $fonte->lancamentoPadrao?->description ?? null;
        $centroCustoNome = $fonte->costCenter?->name ?? null;
        $entidadeNome = $fonte->entidadeFinanceira?->nome ?? null;

        $valorRestante = (float) $fonte->valor;
        if ($t->fracionamentos && $t->fracionamentos->isNotEmpty()) {
            $emAberto = $t->fracionamentos->firstWhere('tipo', 'em_aberto');
            if ($emAberto) {
                $valorRestante = (float) $emAberto->valor;
            }
        } else {
            $valorRestante = max(0, (float) $fonte->valor - (float) ($fonte->valor_pago ?? 0));
        }

        // Histórico de pagamentos (fracionamentos tipo 'pago')
        $pagamentosHist = [];
        if ($t->fracionamentos && $t->fracionamentos->isNotEmpty()) {
            $pagamentosHist = $t->fracionamentos
                ->where('tipo', 'pago')
                ->sortByDesc('created_at')
                ->values()
                ->map(fn($f) => [
                    'id'              => $f->id,
                    'valor'           => round((float) $f->valor, 2),
                    'juros'           => round((float) ($f->juros ?? 0), 2),
                    'multa'           => round((float) ($f->multa ?? 0), 2),
                    'desconto'        => round((float) ($f->desconto ?? 0), 2),
                    'valor_total'     => round((float) ($f->valor_total ?? 0), 2),
                    'data_pagamento'  => $f->data_pagamento ? Carbon::parse($f->data_pagamento)->format('Y-m-d') : null,
                    'forma_pagamento' => $f->forma_pagamento ?? '',
                    'conta_pagamento' => $f->conta_pagamento ?? '',
                ])
                ->toArray();
        }

        // Anexos do lançamento principal (mesma transação usada para o formulário — $fonte)
        $fonte->loadMissing('modulos_anexos');
        $anexosPayload = $fonte->modulos_anexos
            ->where('status', 'ativo')
            ->values()
            ->map(function ($anexo) {
                $url = $anexo->caminho_arquivo
                    ? route('file', ['path' => $anexo->caminho_arquivo])
                    : ($anexo->link ?? '#');

                return [
                    'id'          => $anexo->id,
                    'nome'        => $anexo->nome_arquivo ?: ($anexo->link ?: 'Link'),
                    'url'         => $url,
                    'forma_anexo' => $anexo->forma_anexo ?? 'arquivo',
                    'tipo_anexo'  => $anexo->tipo_anexo,
                    'descricao'   => $anexo->descricao,
                ];
            })
            ->values()
            ->all();

        return response()->json([
            'id'                     => $t->id,
            'tipo'                   => $tipoLabel === 'entrada' ? 'receita' : 'despesa',
            'descricao'              => $fonte->descricao ?? '',
            'valor'                  => round((float) $fonte->valor, 2),
            'valor_restante'         => round($valorRestante, 2),
            'data_competencia'       => $fonteCompetencia ? Carbon::parse($fonteCompetencia)->format('Y-m-d') : null,
            'data_vencimento'        => $fonteVencimento  ? Carbon::parse($fonteVencimento)->format('Y-m-d')  : null,
            'entidade_id'            => (string) ($fonte->entidade_id ?? ''),
            'entidade_nome'          => $entidadeNome,
            'parceiro_id'            => $fonte->parceiro_id  ? (string) $fonte->parceiro_id  : null,
            'parceiro_nome'          => $parceiroNome,
            'lancamento_padrao_id'   => $fonte->lancamento_padrao_id ? (string) $fonte->lancamento_padrao_id : null,
            'categoria_nome'         => $categoriaNome,
            'cost_center_id'         => $fonte->cost_center_id ? (string) $fonte->cost_center_id : null,
            'centro_custo_nome'      => $centroCustoNome,
            'tipo_documento'         => $fonte->tipo_documento ?? '',
            'numero_documento'       => $fonte->numero_documento ?? '',
            'historico_complementar' => $fonte->historico_complementar ?? '',
            'situacao'               => $fonteSituacao,
            'recebido_pago'          => in_array($fonteSituacao, ['pago', 'recebido']),
            'juros'                  => round((float) ($t->juros ?? 0), 2),
            'multa'                  => round((float) ($t->multa ?? 0), 2),
            'desconto'               => round((float) ($t->desconto ?? 0), 2),
            // Parcelamento
            'parcelamento'           => $parcelamentoStr,
            'parcelas'               => $parcelasData,
            // Recorrência
            'recorrencia_id'         => $t->recorrencia_id ? (string) $t->recorrencia_id : null,
            'recorrencia'            => $recorrenciaData,
            // Histórico de pagamentos parciais
            'pagamentos'             => $pagamentosHist,
            'anexos'                 => $anexosPayload,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // DELETE /app/financeiro/banco/lancamento/anexo/{anexoId}
    // ─────────────────────────────────────────────────────────────────────────

    /** Remove um anexo vinculado a uma transação financeira do tenant (drawer React). */
    public function destroyAnexo(int $anexoId): \Illuminate\Http\JsonResponse
    {
        $companyId = session('active_company_id');
        if (!$companyId) {
            return response()->json(['success' => false, 'message' => 'Sessão inválida.'], 401);
        }

        $anexo = ModulosAnexo::with('anexavel')->find($anexoId);
        if (!$anexo) {
            return response()->json(['success' => false, 'message' => 'Anexo não encontrado.'], 404);
        }

        $anexavel = $anexo->anexavel;
        if (!$anexavel instanceof TransacaoFinanceira) {
            return response()->json(['success' => false, 'message' => 'Anexo inválido.'], 403);
        }

        if ((int) $anexavel->company_id !== (int) $companyId) {
            return response()->json(['success' => false, 'message' => 'Não autorizado.'], 403);
        }

        try {
            if ($anexo->caminho_arquivo && Storage::disk('public')->exists($anexo->caminho_arquivo)) {
                Storage::disk('public')->delete($anexo->caminho_arquivo);
            }
            $anexo->delete();
            $anexavel->refresh();
            $anexavel->updateComprovacaoFiscal();

            return response()->json(['success' => true, 'message' => 'Anexo excluído com sucesso.']);
        } catch (\Throwable $e) {
            Log::error('ReactBancoController::destroyAnexo', [
                'anexo_id' => $anexoId,
                'message'  => $e->getMessage(),
            ]);

            return response()->json(['success' => false, 'message' => 'Erro ao excluir o anexo.'], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PUT /app/financeiro/banco/lancamento/{id}
    // ─────────────────────────────────────────────────────────────────────────

    /** Atualiza uma transação existente a partir do drawer React. */
    public function update(Request $request, int $id, TransacaoFinanceiraService $transacaoService): \Illuminate\Http\JsonResponse
    {
        $companyId = session('active_company_id');
        if (!$companyId) {
            return response()->json(['success' => false, 'message' => 'Sessão inválida.'], 401);
        }

        $transacao = TransacaoFinanceira::where('company_id', $companyId)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'tipo'                   => 'required|in:receita,despesa',
            'descricao'              => 'required|string|max:255',
            'valor'                  => 'required|numeric|min:0.01',
            'data_competencia'       => 'required|date',
            'data_vencimento'        => 'nullable|date',
            'entidade_id'            => 'required|integer',
            'parceiro_id'            => 'nullable|integer',
            'lancamento_padrao_id'   => 'nullable|integer',
            'cost_center_id'         => 'nullable|integer',
            'tipo_documento'         => 'nullable|string|max:50',
            'numero_documento'       => 'nullable|string|max:100',
            'historico_complementar' => 'nullable|string|max:250',
            'recebido_pago'          => 'nullable|boolean',
            'agendado'               => 'nullable|boolean',
            // Parcelamento (opcional — só presente ao editar todas as parcelas)
            'parcelamento'           => 'nullable|string',
            'parcelas'               => 'nullable|array',
            'parcelas.*.vencimento'  => 'required_with:parcelas|date_format:Y-m-d',
            'parcelas.*.valor'       => 'required_with:parcelas|numeric|gt:0',
            'parcelas.*.percentual'  => 'nullable|numeric',
            'parcelas.*.conta_pagamento_id' => 'nullable|integer',
            'parcelas.*.descricao'   => 'nullable|string|max:255',
            'parcelas.*.agendado'    => 'nullable|boolean',
            // Recorrência (opcional — só presente ao editar todas as recorrências)
            'repetir_lancamento'     => 'nullable|boolean',
            'configuracao_recorrencia' => 'nullable|string',
            'intervalo_repeticao'    => 'nullable|integer|min:1',
            'frequencia'             => 'nullable|string|in:diario,semanal,mensal,anual',
            'apos_ocorrencias'       => 'nullable|integer|min:1',
            'dia_cobranca'           => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Dados inválidos.', 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        $tipoDb       = $data['tipo'] === 'receita' ? 'entrada' : 'saida';
        $recebidoPago = (bool) ($data['recebido_pago'] ?? false);

        /** @var \Illuminate\Http\JsonResponse $response */
        $response = DB::transaction(function () use ($request, $transacao, $transacaoService, $companyId, $data, $tipoDb, $recebidoPago): \Illuminate\Http\JsonResponse {

        // ── Determinar a transação PRINCIPAL (pai) para parcelamento ──────────
        // Se a transação editada é uma parcela filha (tem parent_id), usamos o pai
        // para excluir e recriar todos os parcelamentos.
        $transacaoPrincipal = null;
        $temParcelasNoRequest = $transacaoService->deveCriarParcelamentos($request);
        $temParcelasExistentes = false;

        if ($request->has('parcelamento') && $request->input('parcelamento') !== 'avista' && $request->input('parcelamento') !== '1x') {
            if ($transacao->parent_id) {
                $transacaoPrincipal = TransacaoFinanceira::where('company_id', $companyId)->find($transacao->parent_id);
            }
            if (!$transacaoPrincipal) {
                $transacaoPrincipal = $transacao;
            }
            $temParcelasExistentes = $transacaoPrincipal->parcelas()->count() > 0;
        }

        // ── Situação: parcelado se houver parcelas novas; pago/recebido se marcado ──
        $situacao = 'em_aberto';
        if ($temParcelasNoRequest) {
            $situacao = 'parcelado'; // será definido pelo service, mas init segura aqui
        } elseif ($recebidoPago) {
            $situacao = $tipoDb === 'entrada' ? 'recebido' : 'pago';
        }

        // ── Atualiza campos principais da transação ───────────────────────────
        $updateData = [
            'tipo'                   => $tipoDb,
            'descricao'              => $data['descricao'],
            'valor'                  => (float) $data['valor'],
            'valor_pago'             => $recebidoPago ? (float) $data['valor'] : $transacao->valor_pago,
            'data_competencia'       => $data['data_competencia'],
            'data_vencimento'        => $data['data_vencimento'] ?? $data['data_competencia'],
            'entidade_id'            => (int) $data['entidade_id'],
            'parceiro_id'            => isset($data['parceiro_id']) ? (int) $data['parceiro_id'] : null,
            'lancamento_padrao_id'   => isset($data['lancamento_padrao_id']) ? (int) $data['lancamento_padrao_id'] : null,
            'cost_center_id'         => isset($data['cost_center_id']) ? (int) $data['cost_center_id'] : null,
            'tipo_documento'         => $data['tipo_documento'] ?? null,
            'numero_documento'       => $data['numero_documento'] ?? null,
            'historico_complementar' => $data['historico_complementar'] ?? null,
            'agendado'               => (bool) ($data['agendado'] ?? false),
            'updated_by'             => Auth::id(),
            'updated_by_name'        => Auth::user()?->name ?? 'Sistema',
        ];

        // Só atualiza situação aqui se NÃO houver parcelamento (o service define a situação nesses casos)
        if (!$temParcelasNoRequest && !$temParcelasExistentes) {
            $updateData['situacao'] = $situacao;
        }

        $transacao->update($updateData);

        // ── Sincroniza Movimentação via service centralizado ─────────────────
        $transacaoService->sincronizarMovimentacao($transacao, $tipoDb, (float) $data['valor'], (int) $data['entidade_id'], $recebidoPago);

        // Se editou a principal (diferente da $transacao), sincroniza campos dela também
        if ($transacaoPrincipal && $transacaoPrincipal->id !== $transacao->id) {
            $transacaoPrincipal->update(array_merge($updateData, [
                'valor' => (float) $data['valor'],
            ]));
        }

        // ── Recria parcelamentos se necessário ────────────────────────────────
        if ($transacaoPrincipal && ($temParcelasNoRequest || $temParcelasExistentes)) {
            $validatedData = array_merge($data, [
                'company_id' => $companyId,
                'tipo'       => $tipoDb,
                'origem'     => 'Banco',
            ]);
            $transacaoService->excluirERecriarParcelamentos($transacaoPrincipal, $validatedData, $request);
        }

        // ── Atualiza recorrência se necessário ─────────────────────────────────
        $repetirLancamento = $request->boolean('repetir_lancamento');
        if ($repetirLancamento && $request->has('intervalo_repeticao')) {
            $recorrenciaId = $transacao->recorrencia_id;
            if ($recorrenciaId) {
                $recorrencia = \App\Models\Financeiro\Recorrencia::find($recorrenciaId);
                if ($recorrencia) {
                    $validatedRecData = array_merge($data, [
                        'company_id'      => $companyId,
                        'tipo'            => $tipoDb,
                        'origem'          => 'Banco',
                        'data_competencia' => $data['data_competencia'],
                        'data_vencimento'  => $data['data_vencimento'] ?? $data['data_competencia'],
                    ]);
                    $transacaoService->atualizarRecorrencia($transacao, $recorrencia, $validatedRecData, $request);
                }
            }
        }

        if ($request->has('anexos') && is_array($request->input('anexos'))) {
            try {
                $transacaoService->processarAnexosPublic($request, $transacao);
            } catch (\Exception $e) {
                Log::warning('Erro ao processar anexos na atualização (React)', [
                    'transacao_id' => $transacao->id,
                    'erro'         => $e->getMessage(),
                ]);
            }
        }

        // Re-agenda notificação WhatsApp/banco se agendado = true
        if ($request->boolean('agendado')) {
            $transacao->refresh();
            $vencimento = Carbon::parse(
                $transacao->getRawOriginal('data_vencimento') ?? $transacao->getRawOriginal('data_competencia')
            )->startOfDay();

            if ($vencimento->isFuture() || $vencimento->isToday()) {
                $delay = $vencimento->isFuture() ? $vencimento : now()->addSeconds(5);
                LancamentoAgendadoNotificacaoJob::dispatch(
                    transacaoId: $transacao->id,
                    companyId:   (int) $companyId,
                    tenantId:    rescue(static fn () => tenancy()->tenant?->id, null, report: false),
                    triggeredBy: Auth::id(),
                )->delay($delay);
            }
        }

        return response()->json(['success' => true, 'message' => 'Lançamento atualizado com sucesso!']);

        }); // fim DB::transaction

        return $response;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // POST /app/financeiro/banco/lancamento/{id}/pagamento
    // ─────────────────────────────────────────────────────────────────────────

    /** Registra pagamento (total ou parcial) de uma transação. */
    public function registrarPagamento(Request $request, int $id, TransacaoFinanceiraService $transacaoService): \Illuminate\Http\JsonResponse
    {
        $companyId = session('active_company_id');
        if (!$companyId) {
            return response()->json(['success' => false, 'message' => 'Sessão inválida.'], 401);
        }

        $transacao = TransacaoFinanceira::where('company_id', $companyId)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'valor_pago'         => 'required|numeric|gt:0',
            'data_pagamento'     => 'required|date',
            'juros'              => 'nullable|numeric|min:0',
            'multa'              => 'nullable|numeric|min:0',
            'desconto'           => 'nullable|numeric|min:0',
            'forma_pagamento'    => 'nullable|string|max:100',
            'conta_pagamento'    => 'nullable|string|max:100',
            'conta_pagamento_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Dados inválidos.', 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        // Calcula valor restante considerando fracionamentos
        $transacao->load('fracionamentos');
        $valorRestante = (float) $transacao->valor;
        if ($transacao->fracionamentos && $transacao->fracionamentos->isNotEmpty()) {
            $emAberto = $transacao->fracionamentos->firstWhere('tipo', 'em_aberto');
            if ($emAberto) {
                $valorRestante = (float) $emAberto->valor;
            }
        } else {
            $valorRestante = max(0, (float) $transacao->valor - (float) ($transacao->valor_pago ?? 0));
        }

        if ((float) $data['valor_pago'] > $valorRestante + 0.01) {
            return response()->json([
                'success' => false,
                'message' => 'O valor pago (R$ ' . number_format($data['valor_pago'], 2, ',', '.') . ') excede o valor restante (R$ ' . number_format($valorRestante, 2, ',', '.') . ').',
            ], 422);
        }

        // Resolve nome da conta se conta_pagamento_id foi enviado
        if (!empty($data['conta_pagamento_id'])) {
            $entidade = \App\Models\EntidadeFinanceira::find($data['conta_pagamento_id']);
            if ($entidade) {
                $data['conta_pagamento'] = $entidade->nome ?? ($entidade->agencia . ' - ' . $entidade->conta);
            }
        }

        try {
            $transacaoService->registrarPagamento($transacao, $data);
            return response()->json(['success' => true, 'message' => 'Pagamento registrado com sucesso!']);
        } catch (\Throwable $e) {
            \Log::error('ReactBancoController::registrarPagamento', [
                'transacao_id' => $id,
                'message' => $e->getMessage(),
            ]);
            return response()->json(['success' => false, 'message' => 'Erro ao registrar pagamento: ' . $e->getMessage()], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PATCH /app/financeiro/banco/lancamento/{id}/quick-update
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Atualização parcial usada pelos diálogos de edição rápida no
     * PagamentoSheet (Pencil ao lado de cada campo do "Informações do
     * lançamento"). Aceita um subconjunto de campos seguros — cada um é
     * validado individualmente; envie só o que precisa alterar.
     *
     *   descricao             string, max 255
     *   lancamento_padrao_id  int|null  — null limpa categoria
     *   cost_center_id        int|null  — null limpa centro de custo
     *   parceiro_id           int|null  — null limpa fornecedor/cliente
     *   data_competencia      Y-m-d
     *   data_vencimento       Y-m-d|null
     *   numero_documento      string|null, max 100
     *
     * O endpoint atualiza diretamente a transação alvo (sem propagar para
     * irmãs do parcelamento) — é responsabilidade da UI deixar isso claro
     * para o usuário se necessário.
     */
    public function quickUpdate(Request $request, int $id): \Illuminate\Http\JsonResponse
    {
        $companyId = session('active_company_id');
        if (! $companyId) {
            return response()->json(['success' => false, 'message' => 'Sessão inválida.'], 401);
        }

        $transacao = TransacaoFinanceira::where('company_id', $companyId)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'descricao'            => 'sometimes|nullable|string|max:255',
            'lancamento_padrao_id' => 'sometimes|nullable|integer',
            'cost_center_id'       => 'sometimes|nullable|integer',
            'parceiro_id'          => 'sometimes|nullable|integer',
            'data_competencia'     => 'sometimes|required|date_format:Y-m-d',
            'data_vencimento'      => 'sometimes|nullable|date_format:Y-m-d',
            'numero_documento'     => 'sometimes|nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        if (empty($data)) {
            return response()->json([
                'success' => false,
                'message' => 'Nenhum campo enviado para atualização.',
            ], 422);
        }

        $transacao->fill($data);
        $transacao->save();

        return response()->json([
            'success' => true,
            'message' => 'Lançamento atualizado.',
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET /app/financeiro/banco/transacoes
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Lista transações paginadas com JSON limpo para as tabelas React.
     *
     * Query params:
     *   tipo        entrada | saida | all
     *   tab         contas_receber | contas_pagar | extrato
     *   start_date  Y-m-d
     *   end_date    Y-m-d
     *   entidade_id int (opcional)
     *   situacao    string (opcional)
     *   search      string (opcional)
     *   page        int (default 1)
     *   per_page    int (default 20)
     *   sort_by     vencimento|data|descricao|valor|situacao (default vencimento)
     *   sort_dir    asc|desc (default desc)
     *   lancamento_padrao_id[]  ids de categoria (LP); use "__blank__" para sem categoria
     *   cost_center_id[]        ids de centro de custo; "__blank__" para sem centro
     *   parceiro_id[]           ids de parceiro; "__blank__" para sem parceiro
     *   origem[]                valores do campo origem (string)
     *   recorrencia[]           "com" | "sem" (ambos = sem filtro de recorrência)
     *   valor_min, valor_max    faixa de valor (decimal)
     *   created_from, created_to Y-m-d (filtro em created_at)
     */
    public function transacoes(Request $request)
    {
        $companyId = session('active_company_id');

        if (!$companyId) {
            return response()->json(['data' => [], 'total' => 0, 'per_page' => 20, 'current_page' => 1, 'saldo_anterior' => 0]);
        }

        $tipo      = $request->input('tipo', 'entrada');
        $tab       = $request->input('tab', 'contas_receber');
        $isExtrato = $tab === 'extrato';

        [$start, $end] = $this->resolveperiod($request);

        // ── Query base ────────────────────────────────────────────────────────
        $query = TransacaoFinanceira::with(['parceiro:id,nome', 'entidadeFinanceira:id,nome,tipo,conta,agencia', 'fracionamentos', 'recorrencia', 'lancamentoPadrao:id,description', 'costCenter:id,name'])
            ->withCount('rateiosGerados')
            ->whereHas('entidadeFinanceira', fn($q) => $q->whereIn('tipo', ['banco', 'caixa']))
            ->where('company_id', $companyId)
            ->where('situacao', '!=', 'parcelado');

        // Filtro de tipo
        if ($tipo !== 'all') {
            $query->where('tipo', $tipo);
        }

        // Filtro de entidade
        if ($request->filled('entidade_id')) {
            $ids = (array) $request->input('entidade_id');
            $ids = array_filter($ids);
            if (count($ids) === 1) {
                $query->where('entidade_id', reset($ids));
            } elseif (count($ids) > 1) {
                $query->whereIn('entidade_id', $ids);
            }
        }

        // Filtro de situação
        if ($request->filled('situacao') && $request->situacao !== 'all') {
            $query->where('situacao', $request->situacao);
        }

        // Busca geral
        if ($request->filled('search')) {
            $q = $request->input('search');
            $query->where(function ($qb) use ($q) {
                $qb->where('descricao', 'like', "%{$q}%")
                   ->orWhere('origem', 'like', "%{$q}%")
                   ->orWhereHas('parceiro', fn($p) => $p->where('nome', 'like', "%{$q}%"));
            });
        }

        // Filtro de período + filtro de status da aba
        $status = $request->input('status', 'total');
        $this->aplicarFiltroPeriodo($query, $start, $end, $isExtrato, $status);

        $this->applyTransacoesReactFilters($request, $query);

        $total = $query->count();

        // Ordenação
        $sortBy  = $request->input('sort_by', $isExtrato ? 'data_competencia' : 'data_vencimento');
        $sortDir = $request->input('sort_dir', 'desc');
        $colMap  = [
            'vencimento' => 'data_vencimento',
            'data'       => 'data_competencia',
            'descricao'  => 'descricao',
            'valor'      => 'valor',
            'situacao'   => 'situacao',
        ];
        $dbCol = $colMap[$sortBy] ?? ($isExtrato ? 'data_competencia' : 'data_vencimento');
        $query->orderBy($dbCol, $sortDir);

        // Paginação
        $perPage = (int) $request->input('per_page', 20);
        $page    = (int) $request->input('page', 1);
        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        // Saldo anterior (só extrato)
        $saldoAnterior = 0;
        if ($isExtrato && $start) {
            $saldoAnterior = $this->calcularSaldoAnterior($companyId, $start, $request->input('entidade_id'));
        }

        $data = $paginator->getCollection()->map(fn($t) => $this->formatTransacao($t, $isExtrato));

        return response()->json([
            'data'          => $data->values(),
            'total'         => $paginator->total(),
            'per_page'      => $paginator->perPage(),
            'current_page'  => $paginator->currentPage(),
            'last_page'     => $paginator->lastPage(),
            'saldo_anterior' => round((float) $saldoAnterior, 2),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET /app/financeiro/banco/stats
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Retorna estatísticas do período para a barra de stats React.
     *
     * Query params: mesmos de transacoes() (tipo, tab, start_date, end_date, entidade_id)
     */
    public function stats(Request $request)
    {
        $companyId = session('active_company_id');

        if (!$companyId) {
            return response()->json($this->emptyStats($request->input('tab', 'contas_receber')));
        }

        $tipo      = $request->input('tipo', 'entrada');
        $tab       = $request->input('tab', 'contas_receber');
        $isExtrato = $tab === 'extrato';

        [$start, $end] = $this->resolveperiod($request);
        $hoje = Carbon::now()->startOfDay();

        $entidadeId = $request->input('entidade_id');

        if ($isExtrato) {
            return response()->json($this->statsExtrato($request, $companyId, $start, $end, $entidadeId));
        }

        return response()->json($this->statsContas($request, $companyId, $tipo, $start, $end, $hoje, $entidadeId));
    }

    /**
     * Valores distintos de origem (campo na transação) para multiselect de filtros.
     */
    public function transacoesOpcoesOrigem(Request $request)
    {
        $companyId = session('active_company_id');

        if (!$companyId) {
            return response()->json(['origens' => []]);
        }

        $origens = TransacaoFinanceira::query()
            ->whereHas('entidadeFinanceira', fn ($q) => $q->whereIn('tipo', ['banco', 'caixa']))
            ->where('company_id', $companyId)
            ->where('situacao', '!=', 'parcelado')
            ->whereNotNull('origem')
            ->where('origem', '!=', '')
            ->distinct()
            ->orderBy('origem')
            ->limit(200)
            ->pluck('origem')
            ->values()
            ->all();

        return response()->json(['origens' => $origens]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helpers privados
    // ─────────────────────────────────────────────────────────────────────────

    private function resolveperiod(Request $request): array
    {
        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');

        if (!$startDate || !$endDate) {
            return [
                Carbon::now()->startOfMonth()->startOfDay(),
                Carbon::now()->endOfMonth()->endOfDay(),
            ];
        }

        return [
            Carbon::createFromFormat('Y-m-d', $startDate)->startOfDay(),
            Carbon::createFromFormat('Y-m-d', $endDate)->endOfDay(),
        ];
    }

    private function aplicarFiltroPeriodo($query, $start, $end, bool $isExtrato, string $status): void
    {
        $hoje = Carbon::now()->startOfDay();

        if ($status !== 'total' && $status !== '') {
            switch ($status) {
                case 'receitas_aberto':
                    $query->where('tipo', 'entrada')
                          ->whereNotIn('situacao', ['recebido', 'desconsiderado', 'parcelado'])
                          ->where('agendado', false)
                          ->whereBetween('data_competencia', [$start, $end]);
                    break;
                case 'receitas_realizadas':
                    $query->where('tipo', 'entrada')
                          ->where('situacao', 'recebido')
                          ->where('agendado', false)
                          ->whereBetween('data_competencia', [$start, $end]);
                    break;
                case 'despesas_aberto':
                    $query->where('tipo', 'saida')
                          ->whereNotIn('situacao', ['pago', 'desconsiderado', 'parcelado'])
                          ->where('agendado', false)
                          ->whereBetween('data_competencia', [$start, $end]);
                    break;
                case 'despesas_realizadas':
                    $query->where('tipo', 'saida')
                          ->where('situacao', 'pago')
                          ->where('agendado', false)
                          ->whereBetween('data_competencia', [$start, $end]);
                    break;
                case 'vencidos':
                    $ontem = $hoje->copy()->subDay();
                    $limite = $ontem->lt($end) ? $ontem : $end;
                    $query->where(fn($q) => $q->whereBetween('data_vencimento', [$start, $limite])
                                              ->orWhere(fn($s) => $s->whereNull('data_vencimento')->whereBetween('data_competencia', [$start, $limite])))
                          ->whereNotIn('situacao', ['pago', 'recebido', 'desconsiderado']);
                    break;
                case 'hoje':
                    $query->where(fn($q) => $q->whereDate('data_vencimento', $hoje)
                                              ->orWhere(fn($s) => $s->whereNull('data_vencimento')->whereDate('data_competencia', $hoje)))
                          ->whereNotIn('situacao', ['pago', 'recebido', 'desconsiderado']);
                    break;
                case 'a_vencer':
                    $amanha = $hoje->copy()->addDay();
                    $inicio = $amanha->gt($start) ? $amanha : $start;
                    $query->where(fn($q) => $q->whereBetween('data_vencimento', [$inicio, $end])
                                              ->orWhere(fn($s) => $s->whereNull('data_vencimento')->whereBetween('data_competencia', [$inicio, $end])))
                          ->whereNotIn('situacao', ['pago', 'recebido', 'desconsiderado']);
                    break;
                case 'recebidos':
                    $query->where('situacao', 'recebido')
                          ->where(fn($q) => $q->whereBetween('data_vencimento', [$start, $end])
                                              ->orWhere(fn($s) => $s->whereNull('data_vencimento')->whereBetween('data_competencia', [$start, $end])));
                    break;
                case 'pagos':
                    $query->where('situacao', 'pago')
                          ->where(fn($q) => $q->whereBetween('data_vencimento', [$start, $end])
                                              ->orWhere(fn($s) => $s->whereNull('data_vencimento')->whereBetween('data_competencia', [$start, $end])));
                    break;
            }
            return;
        }

        // status === 'total' — mostrar tudo dentro do período
        if ($isExtrato) {
            $query->whereNotIn('situacao', ['desconsiderado', 'parcelado'])
                  ->where('agendado', false)
                  ->whereBetween('data_competencia', [$start, $end]);
        } else {
            $query->where(fn($q) => $q->whereBetween('data_vencimento', [$start, $end])
                                      ->orWhere(fn($s) => $s->whereNull('data_vencimento')->whereBetween('data_competencia', [$start, $end])));
        }
    }

    /**
     * Filtros avançados compartilhados entre transacoes() e stats.
     */
    private function applyTransacoesReactFilters(Request $request, $query): void
    {
        $this->applyNullableIdInFilter($request, $query, 'lancamento_padrao_id', 'lancamento_padrao_id');
        $this->applyNullableIdInFilter($request, $query, 'cost_center_id', 'cost_center_id');
        $this->applyNullableIdInFilter($request, $query, 'parceiro_id', 'parceiro_id');

        $origens = $this->normalizeRequestStringArray($request->input('origem'));
        if ($origens !== null && $origens !== []) {
            $origens = array_values(array_filter($origens, fn ($v) => $v !== '' && $v !== null));
            if ($origens !== []) {
                $query->whereIn('origem', $origens);
            }
        }

        $rec = $this->normalizeRequestStringArray($request->input('recorrencia'));
        if ($rec !== null && $rec !== []) {
            $hasCom = in_array('com', $rec, true);
            $hasSem = in_array('sem', $rec, true);
            if ($hasCom xor $hasSem) {
                if ($hasCom) {
                    $query->whereNotNull('recorrencia_id');
                } else {
                    $query->whereNull('recorrencia_id');
                }
            }
        }

        if ($request->filled('valor_min')) {
            $query->where('valor', '>=', (float) $request->input('valor_min'));
        }
        if ($request->filled('valor_max')) {
            $query->where('valor', '<=', (float) $request->input('valor_max'));
        }

        if ($request->filled('created_from')) {
            try {
                $from = Carbon::createFromFormat('Y-m-d', $request->input('created_from'))->startOfDay();
                $query->where('created_at', '>=', $from);
            } catch (\Throwable) {
            }
        }
        if ($request->filled('created_to')) {
            try {
                $to = Carbon::createFromFormat('Y-m-d', $request->input('created_to'))->endOfDay();
                $query->where('created_at', '<=', $to);
            } catch (\Throwable) {
            }
        }
    }

    /**
     * @param  mixed  $raw  entrada bruta do request
     */
    private function applyNullableIdInFilter(Request $request, $query, string $paramKey, string $column): void
    {
        if (! $request->has($paramKey)) {
            return;
        }

        $raw = $request->input($paramKey);
        $items = is_array($raw) ? $raw : ($raw === null || $raw === '' ? [] : [$raw]);
        $includeBlank = false;
        $numericIds = [];

        foreach ($items as $item) {
            if ($item === '__blank__' || $item === '') {
                $includeBlank = true;

                continue;
            }
            if ($item === null) {
                continue;
            }
            $numericIds[] = (int) $item;
        }

        $numericIds = array_values(array_unique(array_filter($numericIds, fn ($id) => $id > 0)));

        if ($numericIds === [] && ! $includeBlank) {
            return;
        }

        $query->where(function ($q) use ($numericIds, $includeBlank, $column) {
            if ($numericIds !== [] && $includeBlank) {
                $q->whereIn($column, $numericIds)->orWhereNull($column);
            } elseif ($numericIds !== []) {
                $q->whereIn($column, $numericIds);
            } else {
                $q->whereNull($column);
            }
        });
    }

    /** @return array|null null se parâmetro ausente; [] se vazio explícito */
    private function normalizeRequestStringArray(mixed $raw): ?array
    {
        if ($raw === null) {
            return null;
        }
        if (is_string($raw)) {
            return $raw === '' ? [] : [$raw];
        }
        if (! is_array($raw)) {
            return [];
        }

        return array_values(array_map(static fn ($v) => is_string($v) ? $v : (string) $v, $raw));
    }

    private function formatTransacao(TransacaoFinanceira $t, bool $isExtrato): array
    {
        $parceiro = $t->parceiro?->nome;
        $entidade = $t->entidadeFinanceira;

        // Valor a pagar/receber (considera fracionamentos)
        $valorRestante = (float) $t->valor;
        if ($t->fracionamentos && $t->fracionamentos->isNotEmpty()) {
            $emAberto = $t->fracionamentos->firstWhere('tipo', 'em_aberto');
            if ($emAberto) {
                $valorRestante = (float) $emAberto->valor;
            }
        } else {
            $valorRestante = max(0, (float) $t->valor - (float) ($t->valor_pago ?? 0));
        }

        // Origem formatada — o campo é sempre 'Banco' no DB; corrige pelo tipo da entidade
        if ($entidade?->tipo === 'caixa') {
            $origem = 'Caixa';
        } else {
            $origem = $t->origem ?? '-';
            if (strtolower($origem) === 'banco' && $entidade?->conta) {
                $origem = 'Banco - ' . $entidade->conta;
            }
        }

        // Converter enums para string (situacao e tipo podem ser BackedEnum)
        $situacaoStr = $t->situacao instanceof \BackedEnum ? $t->situacao->value : (string) ($t->situacao ?? 'em_aberto');
        $tipoStr     = $t->tipo     instanceof \BackedEnum ? $t->tipo->value     : (string) ($t->tipo     ?? '');

        // ── Parcelamento ───────────────────────────────────────────────────────
        $isParcelado  = $t->parent_id !== null;
        $parcelaInfo  = null;
        if ($isParcelado) {
            $parcelamento = \App\Models\Financeiro\Parcelamento::where('transacao_parcela_id', $t->id)->first();
            if ($parcelamento) {
                $parcelaInfo = $parcelamento->numero_parcela . '/' . $parcelamento->total_parcelas;
            }
        }

        // ── Recorrência ────────────────────────────────────────────────────────
        $isRecorrente    = $t->recorrencia instanceof \Illuminate\Support\Collection
            ? $t->recorrencia->isNotEmpty()
            : ($t->recorrencia_id !== null);
        $recorrenciaInfo = null;
        if ($isRecorrente && $t->recorrencia instanceof \Illuminate\Support\Collection && $t->recorrencia->isNotEmpty()) {
            $rec   = $t->recorrencia->first();
            $pivot = $rec->pivot;
            $num   = $pivot?->numero_ocorrencia ?? null;
            $total = $rec->total_ocorrencias ?? null;
            if ($num && $total) {
                $recorrenciaInfo = $num . '/' . $total;
            }
        }

        $base = [
            'id'               => (string) $t->id,
            'descricao'        => $t->descricao ?? '',
            'parceiro'         => $parceiro,
            'valor'            => round((float) $t->valor, 2),
            'valor_restante'   => round($valorRestante, 2),
            'situacao'         => $situacaoStr,
            'situacao_label'   => self::SITUACAO_CONFIG[$situacaoStr]['label'] ?? ucfirst($situacaoStr),
            'situacao_color'   => self::SITUACAO_CONFIG[$situacaoStr]['color'] ?? 'secondary',
            'origem'           => $origem,
            'is_parcelado'     => $isParcelado,
            'parcela_info'     => $parcelaInfo,
            'is_recorrente'    => $isRecorrente,
            'recorrencia_info' => $recorrenciaInfo,
            'is_transferencia'  => $t->transferencia_id !== null,
            'is_rateio_origem'  => ($t->rateios_gerados_count ?? 0) > 0,
            'is_rateio_filho'   => $t->rateio_origem_id !== null,
            'tipo'              => $tipoStr,
            'origem_nome'       => $entidade?->nome ?? null,
            'origem_agencia'    => $entidade?->agencia ?? null,
            'origem_conta'      => $entidade?->conta ?? null,
            // Campos extras (ocultos por padrão, configuráveis pelo usuário)
            'categoria'        => $t->lancamentoPadrao?->description ?? null,
            'centro_custo'     => $t->costCenter?->name ?? null,
            'conta'            => $entidade?->nome ?? null,
            'numero_documento' => $t->numero_documento ?? null,
            'tipo_documento'   => $t->tipo_documento ?? null,
            'data_competencia' => $t->data_competencia ? Carbon::parse($t->data_competencia)->format('d/m/Y') : null,
            'data_pagamento'   => $t->data_pagamento   ? Carbon::parse($t->data_pagamento)->format('d/m/Y')   : null,
            'valor_pago'       => round((float) ($t->valor_pago ?? 0), 2),
            'juros'            => round((float) ($t->juros ?? 0), 2),
            'multa'            => round((float) ($t->multa ?? 0), 2),
            'desconto'         => round((float) ($t->desconto ?? 0), 2),
        ];

        if ($isExtrato) {
            $base['data'] = $t->data_competencia ? Carbon::parse($t->data_competencia)->format('d/m/Y') : null;
        } else {
            $base['vencimento'] = $t->data_vencimento
                ? Carbon::parse($t->data_vencimento)->format('d/m/Y')
                : ($t->data_competencia ? Carbon::parse($t->data_competencia)->format('d/m/Y') : null);
        }

        return $base;
    }

    private function statsContas(Request $request, int $companyId, string $tipo, $start, $end, $hoje, $entidadeId): array
    {
        $base = TransacaoFinanceira::whereHas('entidadeFinanceira', fn($q) => $q->whereIn('tipo', ['banco', 'caixa']))
            ->where('company_id', $companyId)
            ->where('tipo', $tipo)
            ->where('situacao', '!=', 'parcelado');

        if ($entidadeId) {
            $ids = (array) $entidadeId;
            count($ids) === 1 ? $base->where('entidade_id', reset($ids)) : $base->whereIn('entidade_id', $ids);
        }

        $this->applyTransacoesReactFilters($request, $base);

        $ontem = $hoje->copy()->subDay();
        $limiteVenc = $ontem->lt($end) ? $ontem : $end;

        $vencidos = (clone $base)
            ->whereNotIn('situacao', ['pago', 'recebido', 'desconsiderado'])
            ->where(fn($q) => $q->whereBetween('data_vencimento', [$start, $limiteVenc])
                                ->orWhere(fn($s) => $s->whereNull('data_vencimento')->whereBetween('data_competencia', [$start, $limiteVenc])))
            ->sum('valor');

        $hojeVal = 0;
        if ($hoje->between($start, $end)) {
            $hojeVal = (clone $base)
                ->whereNotIn('situacao', ['pago', 'recebido', 'desconsiderado'])
                ->where(fn($q) => $q->whereDate('data_vencimento', $hoje)
                                    ->orWhere(fn($s) => $s->whereNull('data_vencimento')->whereDate('data_competencia', $hoje)))
                ->sum('valor');
        }

        $amanha = $hoje->copy()->addDay();
        $inicioAVencer = $amanha->gt($start) ? $amanha : $start;
        $aVencer = (clone $base)
            ->whereNotIn('situacao', ['pago', 'recebido', 'desconsiderado'])
            ->where(fn($q) => $q->whereBetween('data_vencimento', [$inicioAVencer, $end])
                                ->orWhere(fn($s) => $s->whereNull('data_vencimento')->whereBetween('data_competencia', [$inicioAVencer, $end])))
            ->sum('valor');

        $situacaoPaga = $tipo === 'entrada' ? 'recebido' : 'pago';
        $liquidados = (clone $base)
            ->where('situacao', $situacaoPaga)
            ->where(fn($q) => $q->whereBetween('data_vencimento', [$start, $end])
                                ->orWhere(fn($s) => $s->whereNull('data_vencimento')->whereBetween('data_competencia', [$start, $end])))
            ->sum('valor');

        $total = (clone $base)
            ->where(fn($q) => $q->whereBetween('data_vencimento', [$start, $end])
                                ->orWhere(fn($s) => $s->whereNull('data_vencimento')->whereBetween('data_competencia', [$start, $end])))
            ->sum('valor');

        $resp = [
            'vencidos' => round((float) $vencidos, 2),
            'hoje'     => round((float) $hojeVal, 2),
            'a_vencer' => round((float) $aVencer, 2),
            'total'    => round((float) $total, 2),
        ];

        $resp[$tipo === 'entrada' ? 'recebidos' : 'pagos'] = round((float) $liquidados, 2);

        return $resp;
    }

    private function statsExtrato(Request $request, int $companyId, $start, $end, $entidadeId): array
    {
        $base = TransacaoFinanceira::whereHas('entidadeFinanceira', fn($q) => $q->whereIn('tipo', ['banco', 'caixa']))
            ->where('company_id', $companyId)
            ->whereNotIn('situacao', ['desconsiderado', 'parcelado'])
            ->where('agendado', false)
            ->whereBetween('data_competencia', [$start, $end]);

        if ($entidadeId) {
            $ids = (array) $entidadeId;
            count($ids) === 1 ? $base->where('entidade_id', reset($ids)) : $base->whereIn('entidade_id', $ids);
        }

        $this->applyTransacoesReactFilters($request, $base);

        $receitasAberto    = (clone $base)->where('tipo', 'entrada')->whereNotIn('situacao', ['recebido'])->sum('valor');
        $receitasRealizadas = (clone $base)->where('tipo', 'entrada')->where('situacao', 'recebido')->sum('valor');
        $despesasAberto    = (clone $base)->where('tipo', 'saida')->whereNotIn('situacao', ['pago'])->sum('valor');
        $despesasRealizadas = (clone $base)->where('tipo', 'saida')->where('situacao', 'pago')->sum('valor');
        $totalReceitas     = (clone $base)->where('tipo', 'entrada')->sum('valor');
        $totalDespesas     = (clone $base)->where('tipo', 'saida')->sum('valor');

        $saldoAnterior = $this->calcularSaldoAnterior($companyId, $start, $entidadeId);

        return [
            'receitas_aberto'     => round((float) $receitasAberto, 2),
            'receitas_realizadas' => round((float) $receitasRealizadas, 2),
            'despesas_aberto'     => round((float) $despesasAberto, 2),
            'despesas_realizadas' => round((float) $despesasRealizadas, 2),
            'total'               => round((float) ($totalReceitas - $totalDespesas), 2),
            'saldo_anterior'      => round((float) $saldoAnterior, 2),
        ];
    }

    private function calcularSaldoAnterior(int $companyId, $start, $entidadeId): float
    {
        $q = TransacaoFinanceira::whereHas('entidadeFinanceira', fn($qb) => $qb->whereIn('tipo', ['banco', 'caixa']))
            ->where('company_id', $companyId)
            ->where('data_competencia', '<', $start)
            ->whereNotIn('situacao', ['desconsiderado', 'parcelado'])
            ->where('agendado', false);

        if ($entidadeId) {
            $ids = (array) $entidadeId;
            count($ids) === 1 ? $q->where('entidade_id', reset($ids)) : $q->whereIn('entidade_id', $ids);
        }

        $entradas = (clone $q)->where('tipo', 'entrada')->sum('valor');
        $saidas   = (clone $q)->where('tipo', 'saida')->sum('valor');

        return (float) $entradas - (float) $saidas;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET /app/financeiro/banco/entidades
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Retorna todas as entidades financeiras (bancos + caixas) com saldo atual
     * para exibição no carrossel da página de gestão financeira.
     */
    public function entidades(): \Illuminate\Http\JsonResponse
    {
        $companyId = session('active_company_id');

        if (!$companyId) {
            return response()->json(['success' => false, 'message' => 'Sessão inválida.'], 401);
        }

        $entidades = EntidadeFinanceira::with('bank')
            ->where('company_id', $companyId)
            ->orderByRaw("CASE WHEN tipo = 'banco' THEN 0 ELSE 1 END")
            ->orderBy('nome')
            ->get();

        $accountTypeLabels = [
            'corrente'       => 'Conta Corrente',
            'poupanca'       => 'Poupança',
            'aplicacao'      => 'Aplicação',
            'renda_fixa'     => 'Renda Fixa',
            'tesouro_direto' => 'Tesouro Direto',
        ];

        $prioridadeStatus = ['divergente', 'em análise', 'parcial', 'pendente', 'ajustado', 'ignorado', 'ok'];

        $data = $entidades->map(function (EntidadeFinanceira $e) use ($accountTypeLabels, $companyId, $prioridadeStatus) {
            $saldo = (float) ($e->saldo_atual ?? 0);

            $pendenciasCount = BankStatement::where('company_id', $companyId)
                ->where('entidade_financeira_id', $e->id)
                ->whereNotIn('status_conciliacao', ['ok', 'ignorado'])
                ->whereDoesntHave('transacoes')
                ->count();

            $statusList = BankStatement::where('entidade_financeira_id', $e->id)
                ->distinct()
                ->pluck('status_conciliacao')
                ->toArray();

            $statusFinal = 'ok';
            foreach ($prioridadeStatus as $status) {
                if (in_array($status, $statusList)) {
                    $statusFinal = $status;
                    break;
                }
            }

            return [
                'id'               => $e->id,
                'nome'             => $e->nome,
                'tipo'             => $e->tipo,
                'account_type'     => $e->account_type,
                'account_label'    => $e->tipo === 'banco'
                    ? ($accountTypeLabels[$e->account_type] ?? 'Banco')
                    : 'Caixa',
                'agencia'          => $e->agencia,
                'conta'            => $e->conta,
                'saldo_inicial'    => (float) ($e->saldo_inicial ?? 0),
                'saldo_atual'      => $saldo,
                'saldo_negativo'   => $saldo < 0,
                'logo_url'         => $e->tipo === 'banco'
                    ? ($e->bank?->logo_url ?? null)
                    : '/tenancy/assets/media/svg/bancos/fraternidadecaixa.svg',
                'banco_nome'       => $e->bank?->name ?? null,
                'status_conciliacao' => $statusFinal,
                'pendencias_conciliacao' => $pendenciasCount,
            ];
        });

        return response()->json([
            'success'     => true,
            'data'        => $data,
            'total_saldo' => $entidades->sum('saldo_atual'),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET /app/financeiro/banco/contas-contabeis
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Lista contas contábeis (plano de contas) para o select de criação de entidade.
     */
    public function contasContabeis(): \Illuminate\Http\JsonResponse
    {
        $contas = ChartOfAccount::orderBy('code')
            ->get(['id', 'code', 'name'])
            ->map(fn(ChartOfAccount $c) => [
                'id'   => $c->id,
                'code' => $c->code,
                'name' => $c->name,
            ]);

        return response()->json(['data' => $contas]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET /app/financeiro/banco/banks
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Lista todos os bancos disponíveis para o select de criação de entidade.
     */
    public function banks(): \Illuminate\Http\JsonResponse
    {
        $banks = Bank::orderBy('name')
            ->get(['id', 'name', 'compe_code', 'logo_path'])
            ->map(fn(Bank $b) => [
                'id'       => $b->id,
                'name'     => $b->name,
                'code'     => $b->compe_code,
                'logo_url' => $b->logo_url,
            ]);

        return response()->json(['data' => $banks]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // POST /app/financeiro/banco/entidades/store
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Cria uma nova entidade financeira via React (retorna JSON).
     */
    public function storeEntidade(Request $request): \Illuminate\Http\JsonResponse
    {
        $companyId = session('active_company_id');
        if (!$companyId) {
            return response()->json(['success' => false, 'message' => 'Sessão inválida.'], 401);
        }

        $validated = $request->validate([
            'tipo'          => 'required|in:caixa,banco',
            'nome'          => 'required_if:tipo,caixa|nullable|string|max:100',
            'nome_banco'    => 'nullable|string|max:100',
            'bank_id'       => 'required_if:tipo,banco|nullable|integer|exists:banks,id',
            'agencia'       => 'nullable|string|max:20',
            'conta'         => 'nullable|string|max:20',
            'account_type'  => 'required_if:tipo,banco|nullable|in:corrente,poupanca,aplicacao,renda_fixa,tesouro_direto',
            'saldo_inicial'     => 'required|numeric',
            'descricao'         => 'nullable|string|max:255',
            'conta_contabil_id' => 'nullable|integer|exists:chart_of_accounts,id',
        ]);

        $accountTypeNames = [
            'corrente'       => 'Conta Corrente',
            'poupanca'       => 'Poupança',
            'aplicacao'      => 'Aplicação',
            'renda_fixa'     => 'Renda Fixa',
            'tesouro_direto' => 'Tesouro Direto',
        ];

        if ($validated['tipo'] === 'banco') {
            $bank = Bank::findOrFail($validated['bank_id']);
            if (!empty($validated['nome_banco'])) {
                $validated['nome'] = $validated['nome_banco'];
            } else {
                $typeName = $accountTypeNames[$validated['account_type']] ?? 'Conta';
                $validated['nome'] = "{$bank->name} - {$typeName} - Ag. {$validated['agencia']} C/C {$validated['conta']}";
            }
            $validated['banco_id'] = $validated['bank_id'];
        } else {
            $validated['banco_id'] = null;
        }

        try {
            $entidade = EntidadeFinanceira::create(array_merge($validated, [
                'company_id'      => $companyId,
                'created_by'      => Auth::id(),
                'created_by_name' => Auth::user()->name,
                'updated_by'      => Auth::id(),
                'updated_by_name' => Auth::user()->name,
            ]));

            \App\Models\Movimentacao::create([
                'entidade_id' => $entidade->id,
                'tipo'        => 'entrada',
                'valor'       => abs($validated['saldo_inicial']),
                'descricao'   => 'Saldo inicial da entidade financeira',
                'categoria'   => 'saldo_inicial',
                'company_id'  => $companyId,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Entidade financeira criada com sucesso!',
                'id'      => $entidade->id,
            ]);
        } catch (\Exception $e) {
            Log::error('storeEntidade error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erro ao criar entidade financeira.'], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET /app/financeiro/banco/entidade/{id}/resumo
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Saldo, valor pendente de conciliação e datas — para o cabeçalho da página React (paridade com tabs.blade).
     */
    public function entidadeResumo(int $id): \Illuminate\Http\JsonResponse
    {
        $companyId = session('active_company_id');

        if (! $companyId) {
            return response()->json(['success' => false, 'message' => 'Sessão inválida.'], 401);
        }

        try {
            $entidade = EntidadeFinanceira::forActiveCompany()->findOrFail($id);

            $baseQuery = ConciliacoesPendentesTabData::baseQuery($companyId, $id);
            $valorPendente = (float) (clone $baseQuery)->sum('amount');

            $ultimoImport = BankStatement::query()
                ->where('company_id', $companyId)
                ->where('entidade_financeira_id', $id)
                ->orderByDesc('dtposted')
                ->first();

            return response()->json([
                'success' => true,
                'data'    => [
                    'saldo_atual'                     => (float) ($entidade->saldo_atual ?? 0),
                    'valor_pendente_conciliacao'      => abs($valorPendente),
                    'data_ultima_atualizacao'         => $entidade->updated_at?->toIso8601String(),
                    'data_ultimo_lancamento_importado' => $ultimoImport && $ultimoImport->dtposted
                        ? Carbon::parse($ultimoImport->dtposted)->toIso8601String()
                        : null,
                ],
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Entidade não encontrada.'], 404);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET /app/financeiro/banco/entidade/{id}/conciliacoes-pendentes
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Lista extratos bancários pendentes de conciliação (JSON para React), mesma lógica que conciliacoesTab.
     */
    public function conciliacoesPendentes(Request $request, int $id): \Illuminate\Http\JsonResponse
    {
        $companyId = session('active_company_id');

        if (!$companyId) {
            return response()->json(['success' => false, 'message' => 'Sessão inválida.'], 401);
        }

        $tab = $request->input('tab', 'all');
        if (! in_array($tab, ['all', 'received', 'paid'], true)) {
            $tab = 'all';
        }

        $page = max(1, (int) $request->input('page', 1));
        $perPage = min(max((int) $request->input('per_page', 5), 1), 30);

        try {
            $loaded = ConciliacoesPendentesTabData::fetch($companyId, $id, $tab, $page, $perPage);
            $entidade = $loaded['entidade'];
            $entidade->loadMissing('bank');

            $bankStatements = $loaded['bank_statements'];
            $items = [];

            $formOptions = self::buildConciliacaoFormOptions();

            foreach ($bankStatements as $stmt) {
                $sugestao = $stmt->sugestao;
                if (is_object($sugestao) && method_exists($sugestao, 'toArray')) {
                    $sugestao = $sugestao->toArray();
                }

                $possiveis = [];
                foreach ($stmt->possiveisTransacoes ?? [] as $t) {
                    $score = (int) ($t->match_score ?? 0);
                    $possiveis[] = [
                        'id'               => $t->id,
                        'data_competencia' => $t->data_competencia
                            ? \Illuminate\Support\Carbon::parse($t->data_competencia)->format('Y-m-d')
                            : null,
                        'tipo'             => $t->tipo,
                        'valor'            => (float) $t->valor,
                        'descricao'        => $t->descricao,
                        'match_score'      => $score,
                        'match_classificacao' => ConciliacaoMatchingService::classificarScore($score),
                    ];
                }

                $items[] = [
                    'statement' => [
                        'id'                   => $stmt->id,
                        'dtposted'             => $stmt->dtposted
                            ? \Illuminate\Support\Carbon::parse($stmt->dtposted)->format('Y-m-d')
                            : null,
                        'amount_cents'         => (int) ($stmt->amount_cents ?? 0),
                        'amount'               => (float) ($stmt->amount ?? 0),
                        'memo'                 => $stmt->memo,
                        'checknum'             => $stmt->checknum,
                        'status_conciliacao'   => $stmt->status_conciliacao,
                        'movimentacao_interna' => $stmt->movimentacao_interna,
                    ],
                    'sugestao'              => $sugestao,
                    'possiveis_transacoes'  => $possiveis,
                ];
            }

            return response()->json([
                'success'    => true,
                'tab'        => $tab,
                'entidade'   => [
                    'id'       => $entidade->id,
                    'nome'     => $entidade->nome,
                    'tipo'     => $entidade->tipo,
                    'logo_url' => $entidade->tipo === 'banco'
                        ? ($entidade->bank?->logo_url ?? null)
                        : '/tenancy/assets/media/svg/bancos/fraternidadecaixa.svg',
                    'banco_nome' => $entidade->bank?->name ?? null,
                ],
                'counts'       => $loaded['counts'],
                'items'        => $items,
                'form_options' => $formOptions,
                'pagination' => [
                    'current_page' => $bankStatements->currentPage(),
                    'last_page'    => $bankStatements->lastPage(),
                    'per_page'     => $bankStatements->perPage(),
                    'total'        => $bankStatements->total(),
                    'has_more'     => $bankStatements->hasMorePages(),
                ],
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Entidade não encontrada.'], 404);
        } catch (\Exception $e) {
            \Log::error('ReactBancoController::conciliacoesPendentes', ['e' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar conciliações.',
            ], 500);
        }
    }

    /**
     * GET /app/financeiro/banco/entidade/{id}/movimentacoes-conciliadas
     *
     * Extratos já vinculados a lançamentos financeiros (conciliados), agrupados por dia do extrato.
     *
     * Query:
     *   - start_date, end_date (Y-m-d) — intervalo inclusivo no campo dtposted (preferencial).
     *   - days (1–365, default 90) — usado só se start/end ausentes: janela a partir de hoje para trás.
     */
    public function movimentacoesConciliadas(Request $request, int $id): \Illuminate\Http\JsonResponse
    {
        $companyId = session('active_company_id');

        if (! $companyId) {
            return response()->json(['success' => false, 'message' => 'Sessão inválida.'], 401);
        }

        $startIn = $request->input('start_date');
        $endIn = $request->input('end_date');

        try {
            EntidadeFinanceira::forActiveCompany()->findOrFail($id);

            if ($startIn && $endIn) {
                $since = Carbon::parse($startIn)->startOfDay();
                $until = Carbon::parse($endIn)->endOfDay();
                if ($since->gt($until)) {
                    return response()->json(['success' => false, 'message' => 'A data inicial deve ser anterior ou igual à final.'], 422);
                }
                if ($since->diffInDays($until) > 730) {
                    return response()->json(['success' => false, 'message' => 'Período máximo: 2 anos.'], 422);
                }
            } else {
                $days = min(max((int) $request->input('days', 90), 1), 365);
                $since = Carbon::now()->subDays($days)->startOfDay();
                $until = Carbon::now()->endOfDay();
            }

            $statements = BankStatement::query()
                ->where('company_id', $companyId)
                ->where('entidade_financeira_id', $id)
                ->whereNotNull('dtposted')
                ->where('dtposted', '>=', $since)
                ->where('dtposted', '<=', $until)
                ->whereHas('transacoes')
                ->whereNotIn('status_conciliacao', ['ignorado'])
                ->with([
                    'transacoes' => static function ($q) {
                        $q->with('lancamentoPadrao:id,description');
                    },
                ])
                ->orderByDesc('dtposted')
                ->limit(2000)
                ->get();

            /** @var array<string, array{linhas: list<array<string, mixed>>, tem_pendencia: bool}> $byDay */
            $byDay = [];

            foreach ($statements as $stmt) {
                $dayKey = Carbon::parse($stmt->dtposted)->format('Y-m-d');

                if (! isset($byDay[$dayKey])) {
                    $byDay[$dayKey] = [
                        'linhas'        => [],
                        'tem_pendencia' => false,
                    ];
                }

                $transacoes = $stmt->transacoes;
                if ($transacoes->isEmpty()) {
                    continue;
                }

                $sumConc = (float) $transacoes->sum(static function ($t) {
                    return (float) ($t->pivot->valor_conciliado ?? 0);
                });
                $amountBancoCents = (int) ($stmt->amount_cents ?? round((float) $stmt->amount * 100));
                $n = $transacoes->count();

                $stmtStatus = (string) ($stmt->status_conciliacao ?? '');
                if (in_array($stmtStatus, ['parcial', 'divergente'], true)) {
                    $byDay[$dayKey]['tem_pendencia'] = true;
                }

                $remainingBanco = $amountBancoCents;
                $i = 0;

                foreach ($transacoes as $transacao) {
                    $i++;
                    $valorConc = (float) ($transacao->pivot->valor_conciliado ?? 0);
                    $valorSistemaCents = (int) round($valorConc * 100);

                    $pivotStatus = (string) ($transacao->pivot->status_conciliacao ?? '');
                    if (in_array($pivotStatus, ['parcial', 'divergente'], true)) {
                        $byDay[$dayKey]['tem_pendencia'] = true;
                    }

                    if ($n === 1) {
                        $valorBancoCents = $amountBancoCents;
                    } elseif ($sumConc > 0) {
                        if ($i === $n) {
                            $valorBancoCents = $remainingBanco;
                        } else {
                            $part = (int) round($amountBancoCents * ($valorConc / $sumConc));
                            $valorBancoCents = $part;
                            $remainingBanco -= $part;
                        }
                    } else {
                        $valorBancoCents = $i === $n ? $remainingBanco : 0;
                    }

                    $tipoConc = $stmt->conciliado_com_missa ? 'automatico' : 'manual';

                    $byDay[$dayKey]['linhas'][] = [
                        'id'                      => $stmt->id.'-'.$transacao->id,
                        'descricao_sistema'       => (string) ($transacao->descricao ?? ''),
                        'subcategoria'            => $transacao->lancamentoPadrao?->description,
                        'valor_sistema_cents'     => $valorSistemaCents,
                        'descricao_banco'         => (string) ($stmt->memo ?? ''),
                        'valor_banco_cents'       => $valorBancoCents,
                        'conciliacao'             => $tipoConc,
                        'bank_statement_id'       => (int) $stmt->id,
                        'transacao_financeira_id' => (int) $transacao->id,
                    ];
                }
            }

            krsort($byDay);

            $dias = [];
            foreach ($byDay as $dayKey => $day) {
                $c = Carbon::parse($dayKey)->locale('pt_BR');

                $totSistema = 0;
                $totBanco = 0;
                foreach ($day['linhas'] as $ln) {
                    $totSistema += (int) ($ln['valor_sistema_cents'] ?? 0);
                    $totBanco += (int) ($ln['valor_banco_cents'] ?? 0);
                }

                $dias[] = [
                    'id'                  => $dayKey,
                    'data_label'          => $c->format('d/m/Y'),
                    'dia_semana'          => ucfirst((string) $c->isoFormat('dddd')),
                    'diferenca_cents'     => $totSistema - $totBanco,
                    'saldo_sistema_cents' => $totSistema,
                    'saldo_banco_cents'   => $totBanco,
                    'tem_pendencia'       => $day['tem_pendencia'],
                    'linhas'              => $day['linhas'],
                ];
            }

            return response()->json([
                'success' => true,
                'data'    => [
                    'dias' => $dias,
                ],
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Entidade não encontrada.'], 404);
        } catch (\Exception $e) {
            \Log::error('ReactBancoController::movimentacoesConciliadas', ['e' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar movimentações conciliadas.',
            ], 500);
        }
    }

    private function emptyStats(string $tab): array
    {
        if ($tab === 'extrato') {
            return ['receitas_aberto' => 0, 'receitas_realizadas' => 0, 'despesas_aberto' => 0, 'despesas_realizadas' => 0, 'total' => 0, 'saldo_anterior' => 0];
        }
        return ['vencidos' => 0, 'hoje' => 0, 'a_vencer' => 0, 'recebidos' => 0, 'total' => 0];
    }

    /**
     * Listas para o formulário "Novo lançamento" na conciliação (espelha conciliacoesTab / Blade).
     *
     * @return array{
     *   centros: list<array{id:int, name:string}>,
     *   lancamentos_padrao: list<array{id:int, description:string, type:string}>,
     *   formas_pagamento: list<array{id:int, nome:string, codigo:string}>,
     *   parceiros: list<array{id:int, nome:string, natureza:string}>,
     *   entidades_banco: list<array{id:int, nome:string, tipo:string, logo_url:?string, banco_nome:?string, agencia:?string, conta:?string, account_type:?string, account_type_label:?string}>,
     *   deposito_lancamento_padrao_id: int|null
     * }
     */
    private static function buildConciliacaoFormOptions(): array
    {
        $scalarEnum = static function ($v): string {
            if ($v instanceof \BackedEnum) {
                return (string) $v->value;
            }

            return (string) $v;
        };

        $centros = CostCenter::forActiveCompany()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn ($c) => ['id' => (int) $c->id, 'name' => (string) $c->name])
            ->values()
            ->all();

        $lps = LancamentoPadrao::forActiveCompany()
            ->with('companies:id')
            ->orderBy('description')
            ->get(['id', 'codigo', 'description', 'type'])
            ->map(fn ($lp) => [
                'id'          => (int) $lp->id,
                'codigo'      => $lp->codigo,
                'description' => (string) $lp->description,
                'type'        => $scalarEnum($lp->type),
                'scope'       => $lp->classificacaoParaCompany(),
                'company_ids' => $lp->companies->pluck('id')->map(fn ($v) => (int) $v)->values()->all(),
            ])
            ->values()
            ->all();

        $formas = FormasPagamento::query()
            ->where('ativo', true)
            ->orderBy('nome')
            ->get(['id', 'nome', 'codigo'])
            ->map(fn ($f) => [
                'id'     => (int) $f->id,
                'nome'   => (string) $f->nome,
                'codigo' => (string) $f->codigo,
            ])
            ->values()
            ->all();

        $parceiros = Parceiro::forActiveCompany()
            ->orderBy('nome')
            ->get(['id', 'nome', 'natureza'])
            ->map(fn ($p) => [
                'id'        => (int) $p->id,
                'nome'      => (string) $p->nome,
                'natureza'  => $scalarEnum($p->natureza),
            ])
            ->values()
            ->all();

        $accountTypeLabels = [
            'corrente'       => 'Conta Corrente',
            'poupanca'       => 'Poupança',
            'aplicacao'      => 'Aplicação',
            'renda_fixa'     => 'Renda Fixa',
            'tesouro_direto' => 'Tesouro Direto',
        ];

        $entidadesBanco = EntidadeFinanceira::forActiveCompany()
            ->where('tipo', 'banco')
            ->with('bank:id,name,logo_path')
            ->orderBy('nome')
            ->get(['id', 'nome', 'tipo', 'banco_id', 'agencia', 'conta', 'account_type'])
            ->map(static function (EntidadeFinanceira $e) use ($accountTypeLabels) {
                $at = $e->account_type;

                return [
                    'id'                  => (int) $e->id,
                    'nome'                => (string) $e->nome,
                    'tipo'                => (string) $e->tipo,
                    'logo_url'            => $e->bank?->logo_url ?? null,
                    'banco_nome'          => $e->bank?->name ?? null,
                    'agencia'             => $e->agencia !== null ? (string) $e->agencia : null,
                    'conta'               => $e->conta !== null ? (string) $e->conta : null,
                    'account_type'        => $at !== null ? (string) $at : null,
                    'account_type_label'  => $at !== null
                        ? ($accountTypeLabels[$at] ?? ucfirst((string) $at))
                        : null,
                ];
            })
            ->values()
            ->all();

        $depositoId = LancamentoPadrao::forActiveCompany()
            ->where('description', 'Deposito Bancário')
            ->value('id');

        return [
            'centros'                       => $centros,
            'lancamentos_padrao'            => $lps,
            'formas_pagamento'              => $formas,
            'parceiros'                     => $parceiros,
            'entidades_banco'               => $entidadesBanco,
            'deposito_lancamento_padrao_id' => $depositoId !== null ? (int) $depositoId : null,
        ];
    }
}
