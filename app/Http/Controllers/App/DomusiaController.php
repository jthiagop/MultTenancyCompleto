<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Services\Ai\DocumentExtractorService;
use App\Services\DocumentViewerService;
use App\Services\DomusDocumentoLancamentoService;
use App\Services\TransacaoFinanceiraService;
use App\Exceptions\Ai\DocumentExtractionException;
use App\Models\DomusDocumento;
use App\Models\Financeiro\TransacaoFinanceira;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;

class DomusiaController extends Controller
{
    protected DocumentViewerService $documentViewerService;

    public function __construct(DocumentViewerService $documentViewerService)
    {
        $this->documentViewerService = $documentViewerService;
    }

    /**
     * Extrai dados de um documento usando IA
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function extract(Request $request)
    {
        try {
            $request->validate([
                'base64_content' => 'required|string',
                'mime_type' => 'required|string|in:application/pdf,image/png,image/jpeg,image/jpg,image/webp',
                'filename' => 'nullable|string|max:255',
                'canal_origem' => 'nullable|string|in:upload,whatsapp,email',
            ]);

            // Inicializar serviço de extração
            $extractorService = new DocumentExtractorService();

            // Sugerir nome descritivo
            $suggestedName = null;
            $extractedData = null;

            // Para PDFs, processar com extração de dados primeiro e depois gerar nome
            if ($request->mime_type === 'application/pdf') {
                try {
                    Log::info('Processando PDF com extração de dados');

                    // Extrair dados do PDF
                    $extractedData = $extractorService->extractData(
                        $request->base64_content,
                        $request->mime_type
                    );

                    Log::info('Dados extraídos do PDF', ['data' => $extractedData]);

                    // Gerar nome descritivo a partir dos dados extraídos
                    $suggestedName = $extractorService->generateNameFromExtractedData($extractedData);

                    Log::info('Nome gerado a partir dos dados extraídos', [
                        'suggested' => $suggestedName,
                    ]);

                } catch (\Exception $e) {
                    Log::warning('Erro ao extrair dados do PDF', [
                        'error' => $e->getMessage(),
                    ]);
                    // Usar nome genérico se falhar
                    $suggestedName = 'Documento PDF ' . date('d-m-Y H\hi');
                    $extractedData = null;
                }
            } else {
                // Para imagens, usar sugestão de nome por IA
                try {
                    $suggestedName = $extractorService->suggestDocumentName(
                        $request->base64_content,
                        $request->mime_type
                    );
                    Log::info('Nome sugerido pela IA para imagem', [
                        'original' => $request->filename,
                        'suggested' => $suggestedName,
                    ]);
                } catch (\Exception $e) {
                    Log::warning('Erro ao sugerir nome, usando nome original', [
                        'error' => $e->getMessage(),
                    ]);
                    $suggestedName = pathinfo($request->filename ?? 'documento', PATHINFO_FILENAME);
                }
            }

            // Salvar arquivo no storage
            $fileName = $request->filename ?? 'documento_' . time() . '.' . $this->documentViewerService->getExtensionFromMimeType($request->mime_type);
            $filePath = $this->saveFile($request->base64_content, $fileName, $request->mime_type);

            // Calcular tamanho do arquivo
            $fileSize = strlen(base64_decode($request->base64_content));

            // Preparar dados extraídos para salvar
            $dadosExtraidos = $extractedData ? json_encode($extractedData) : null;
            $status = $extractedData ? \App\Enums\StatusDomusDocumento::PROCESSADO : \App\Enums\StatusDomusDocumento::PENDENTE;

            // Extrair campos principais se houver dados extraídos
            $estabelecimentoNome = $extractedData['estabelecimento']['nome'] ?? null;
            $estabelecimentoCnpj = $extractedData['estabelecimento']['cnpj'] ?? null;
            $dataEmissao = $extractedData['financeiro']['data_emissao'] ?? null;
            $valorTotal = $extractedData['financeiro']['valor_total'] ?? null;
            $formaPagamento = $extractedData['financeiro']['forma_pagamento'] ?? null;
            $tipoDocumento = $extractedData['tipo_documento'] ?? null;

            // Salvar documento no banco de dados
            $documento = DomusDocumento::create([
                'nome_arquivo' => $suggestedName,
                'caminho_arquivo' => $filePath,
                'tipo_arquivo' => $this->documentViewerService->getExtensionFromMimeType($request->mime_type),
                'mime_type' => $request->mime_type,
                'tamanho_arquivo' => $fileSize,
                'base64_content' => $request->base64_content,
                'status' => $status,
                'dados_extraidos' => $dadosExtraidos,
                'tipo_documento' => $tipoDocumento,
                'estabelecimento_nome' => $estabelecimentoNome,
                'estabelecimento_cnpj' => $estabelecimentoCnpj,
                'data_emissao' => $dataEmissao,
                'valor_total' => $valorTotal,
                'forma_pagamento' => $formaPagamento,
                'processado_em' => $extractedData ? now() : null,
                'company_id' => session('active_company_id') ?? Auth::user()->company_id ?? null,
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name ?? null,
                'canal_origem' => $request->canal_origem ?? 'upload',
                'remetente' => $request->remetente ?? null,
            ]);

            return response()->json([
                'success' => true,
                'documento_id' => $documento->id,
                'nome_sugerido' => $suggestedName,
                'message' => 'Documento salvo com sucesso',
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            Log::error('Erro inesperado ao processar documento', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'filename' => $request->filename ?? 'N/A',
            ]);

            // Tentar salvar documento com status de erro
            try {
                if (isset($request->base64_content) && isset($request->mime_type)) {
                    DomusDocumento::create([
                        'nome_arquivo' => $request->filename ?? 'documento_erro_' . time(),
                        'tipo_arquivo' => $this->documentViewerService->getExtensionFromMimeType($request->mime_type),
                        'mime_type' => $request->mime_type,
                        'status' => \App\Enums\StatusDomusDocumento::ERRO,
                        'erro_processamento' => $e->getMessage(),
                        'company_id' => session('active_company_id') ?? Auth::user()->company_id ?? null,
                        'user_id' => Auth::id(),
                        'user_name' => Auth::user()->name ?? null,
                        'canal_origem' => $request->canal_origem ?? 'upload',
                    ]);
                }
            } catch (\Exception $saveError) {
                Log::error('Erro ao salvar documento com erro', [
                    'error' => $saveError->getMessage(),
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Erro inesperado ao processar documento. Tente novamente.',
            ], 500);
        }
    }

    /**
     * Salva o arquivo no storage
     *
     * @param string $base64Content
     * @param string $fileName
     * @param string $mimeType
     * @return string
     */
    private function saveFile(string $base64Content, string $fileName, string $mimeType): string
    {
        $decodedContent = base64_decode($base64Content);
        $extension = $this->documentViewerService->getExtensionFromMimeType($mimeType);
        $uniqueFileName = Str::slug(pathinfo($fileName, PATHINFO_FILENAME)) . '_' . time() . '.' . $extension;

        $path = 'domus_documentos/' . $uniqueFileName;

        Log::info('Salvando arquivo', [
            'nome_original' => $fileName,
            'nome_unico' => $uniqueFileName,
            'caminho' => $path,
            'tamanho' => strlen($decodedContent),
            'mime_type' => $mimeType,
        ]);

        $saved = Storage::disk('public')->put($path, $decodedContent);

        if (!$saved) {
            Log::error('Falha ao salvar arquivo', [
                'caminho' => $path,
            ]);
            throw new \Exception('Falha ao salvar arquivo no storage');
        }

        // Verificar se o arquivo foi realmente salvo
        $exists = Storage::disk('public')->exists($path);
        Log::info('Arquivo salvo', [
            'caminho' => $path,
            'salvo' => $saved,
            'existe' => $exists,
        ]);

        return $path;
    }

    /**
     * Lista documentos pendentes
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Request $request)
    {
        try {
            $filters = [
                'status' => $request->input('status'), // Opcional: pode filtrar por status específico
                'tipo_documento' => $request->input('tipo_documento'),
                'canal_origem' => $request->input('canal_origem'),
                'order_by' => $request->input('order_by', 'created_at'),
                'order_direction' => $request->input('order_direction', 'desc'),
            ];

            // Remover filtros nulos
            $filters = array_filter($filters, fn($value) => $value !== null);

            $documentos = $this->documentViewerService->listDocuments(null, $filters);

            // Renderizar HTML via Blade
            $html = View::make('app.financeiro.domusia.partials.components.pending-document-list-items', [
                'documentos' => $documentos
            ])->render();

            return response()->json([
                'success' => true,
                'data' => $documentos,
                'html' => $html
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao listar documentos', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao listar documentos',
            ], 500);
        }
    }

    /**
     * Remove um documento
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $deleted = $this->documentViewerService->deleteDocument($id);

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Documento não encontrado',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Documento excluído com sucesso',
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao excluir documento', [
                'error' => $e->getMessage(),
                'document_id' => $id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir documento',
            ], 500);
        }
    }

    /**
     * Busca um documento específico
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $documento = $this->documentViewerService->getDocument($id, null);

            if (!$documento) {
                return response()->json([
                    'success' => false,
                    'message' => 'Documento não encontrado',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $documento,
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao buscar documento', [
                'error' => $e->getMessage(),
                'documento_id' => $id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Documento não encontrado',
            ], 404);
        }
    }


    /**
     * Serve o arquivo diretamente (para contornar problemas com link simbólico)
     *
     * @param int $id
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function serveFile($id)
    {
        try {
            $fileData = $this->documentViewerService->getDocumentFile($id);

            if (!$fileData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Arquivo não encontrado',
                ], 404);
            }

            return response()->file($fileData['path'], [
                'Content-Type' => $fileData['mime_type'],
                'Content-Disposition' => 'inline; filename="' . $fileData['filename'] . '"',
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao servir arquivo', [
                'error' => $e->getMessage(),
                'document_id' => $id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao servir arquivo',
            ], 500);
        }
    }

    /**
     * Anexa um DomusDocumento a um ou mais lançamentos financeiros já existentes.
     *
     * Endpoint usado pelo botão "Buscar Lançamento" no Dominus IA. Suporta três
     * modos de operação que cobrem os cenários de divergência entre o valor do
     * documento e o(s) lançamento(s):
     *
     *  - modo=anexar             → apenas anexa o arquivo como comprovante em N
     *                               lançamentos (não toca em valor/situação).
     *  - modo=baixar_total       → anexa em 1 lançamento e dá baixa total via
     *                               registrarPagamento (cria Movimentacao).
     *  - modo=pagamento_parcial  → anexa em 1 lançamento e registra pagamento
     *                               parcial via transacao_fracionamentos
     *                               (situação: PAGO_PARCIAL).
     *
     * Em todos os modos:
     *  - O documento muda de status para LANCADO.
     *  - O arquivo é registrado como anexo (modulos_anexos) da(s) transação(ões),
     *    herdando classificação por tipo (NF-e → nota_fiscal, BOLETO → boleto, …).
     *  - Escopo de tenant/empresa garantido pelos services para evitar IDOR.
     *
     * POST /financeiro/domusia/{id}/anexar-lancamento
     * Body:
     *  {
     *    transacao_ids: int[]                       (preferencial)
     *    transacao_id: int                          (compatibilidade — 1 item)
     *    modo: "anexar"|"baixar_total"|"pagamento_parcial"  (default: "anexar")
     *    valor_pago?: number                        (obrig. em pagamento_parcial)
     *    data_pagamento?: Y-m-d                     (default: hoje)
     *    forma_pagamento?: string
     *    conta_pagamento?: string
     *    conta_pagamento_id?: int                   (resolve nome de Entidade)
     *    juros?: number, multa?: number, desconto?: number
     *  }
     */
    public function attachToLancamento(
        int $id,
        Request $request,
        DomusDocumentoLancamentoService $service,
        TransacaoFinanceiraService $transacaoService
    ) {
        try {
            $validated = $request->validate([
                'transacao_ids'      => 'sometimes|array|min:1',
                'transacao_ids.*'    => 'integer|min:1',
                'transacao_id'       => 'sometimes|integer|min:1',
                'modo'               => 'sometimes|string|in:anexar,baixar_total,pagamento_parcial',
                'valor_pago'         => 'nullable|numeric|gt:0',
                'data_pagamento'     => 'nullable|date',
                'forma_pagamento'    => 'nullable|string|max:100',
                'conta_pagamento'    => 'nullable|string|max:100',
                'conta_pagamento_id' => 'nullable|integer',
                'juros'              => 'nullable|numeric|min:0',
                'multa'              => 'nullable|numeric|min:0',
                'desconto'           => 'nullable|numeric|min:0',
            ]);

            $modo = $validated['modo'] ?? 'anexar';

            // Normaliza IDs: aceita transacao_ids[] ou transacao_id avulso (legado).
            $ids = (array) ($validated['transacao_ids'] ?? []);
            if (empty($ids) && !empty($validated['transacao_id'])) {
                $ids = [(int) $validated['transacao_id']];
            }
            $ids = array_values(array_unique(array_map('intval', $ids)));

            if (empty($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Informe ao menos um lançamento.',
                ], 422);
            }

            // Pagamento parcial exige exatamente 1 lançamento (precisa de valor_pago).
            // Baixa total aceita N lançamentos: cada um quita pelo seu próprio
            // valor restante (rateio implícito quando a soma bate com o documento).
            if ($modo === 'pagamento_parcial' && count($ids) !== 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pagamento parcial só pode ser registrado em um lançamento por vez.',
                ], 422);
            }

            $companyId = session('active_company_id');
            if (!$companyId) {
                return response()->json(['success' => false, 'message' => 'Sessão inválida.'], 401);
            }

            $domusDoc = $service->findForActiveCompany($id);
            if (!$domusDoc) {
                return response()->json([
                    'success' => false,
                    'message' => 'Documento não encontrado.',
                ], 404);
            }

            $transacoes = TransacaoFinanceira::where('company_id', $companyId)
                ->whereIn('id', $ids)
                ->get();

            if ($transacoes->count() !== count($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Um ou mais lançamentos não foram encontrados nesta empresa.',
                ], 404);
            }

            // Resolve nome da conta a partir do id da entidade financeira (se enviado).
            $contaNomeResolvida = $validated['conta_pagamento'] ?? null;
            if (!$contaNomeResolvida && !empty($validated['conta_pagamento_id'])) {
                $entidade = \App\Models\EntidadeFinanceira::find((int) $validated['conta_pagamento_id']);
                if ($entidade) {
                    $contaNomeResolvida = $entidade->nome
                        ?? trim(($entidade->agencia ?? '') . ' - ' . ($entidade->conta ?? ''));
                }
            }

            // Validação prévia de valor_pago para modo parcial (evita iniciar
            // a transação de DB e ter rollback caro).
            if ($modo === 'pagamento_parcial') {
                if (empty($validated['valor_pago'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Informe o valor a registrar como pagamento parcial.',
                    ], 422);
                }
                /** @var TransacaoFinanceira $transacao */
                $transacao = $transacoes->first();
                $valorRestante = $this->calcularValorRestante($transacao);
                if ((float) $validated['valor_pago'] > $valorRestante + 0.01) {
                    return response()->json([
                        'success' => false,
                        'message' => 'O valor informado (R$ '
                            . number_format((float) $validated['valor_pago'], 2, ',', '.')
                            . ') excede o saldo em aberto (R$ '
                            . number_format($valorRestante, 2, ',', '.')
                            . ').',
                    ], 422);
                }
            }

            DB::transaction(function () use ($transacoes, $domusDoc, $service, $transacaoService, $modo, $validated, $contaNomeResolvida) {
                /** @var TransacaoFinanceira $transacao */
                foreach ($transacoes as $transacao) {
                    if (in_array($modo, ['baixar_total', 'pagamento_parcial'], true)) {
                        $valorPago = $modo === 'baixar_total'
                            ? $this->calcularValorRestante($transacao)
                            : (float) $validated['valor_pago'];

                        $transacaoService->registrarPagamento($transacao, [
                            'valor_pago'      => $valorPago,
                            'data_pagamento'  => $validated['data_pagamento'] ?? Carbon::today()->format('Y-m-d'),
                            'juros'           => (float) ($validated['juros']    ?? 0),
                            'multa'           => (float) ($validated['multa']    ?? 0),
                            'desconto'        => (float) ($validated['desconto'] ?? 0),
                            'forma_pagamento' => $validated['forma_pagamento']  ?? '',
                            'conta_pagamento' => $contaNomeResolvida           ?? '',
                        ]);

                        $transacao->refresh();
                    }

                    $service->markLancadoAndAttachAnexo($domusDoc, $transacao);
                }
            });

            return response()->json([
                'success'            => true,
                'message'            => $this->mensagemSucessoAnexo($modo, count($transacoes)),
                'modo'               => $modo,
                'domus_documento_id' => $domusDoc->id,
                'transacao_ids'      => $ids,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos.',
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erro ao anexar documento a lançamento', [
                'documento_id' => $id,
                'request'      => $request->only([
                    'transacao_id', 'transacao_ids', 'modo',
                    'valor_pago', 'data_pagamento',
                ]),
                'error'        => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao anexar documento ao lançamento.',
            ], 500);
        }
    }

    /**
     * Saldo em aberto da transação considerando fracionamentos (em_aberto)
     * ou, se não houver, a diferença entre valor e valor_pago.
     */
    private function calcularValorRestante(TransacaoFinanceira $transacao): float
    {
        $transacao->load('fracionamentos');
        if ($transacao->fracionamentos && $transacao->fracionamentos->isNotEmpty()) {
            $emAberto = $transacao->fracionamentos->firstWhere('tipo', 'em_aberto');
            if ($emAberto) {
                return (float) $emAberto->valor;
            }
        }

        return max(0, (float) $transacao->valor - (float) ($transacao->valor_pago ?? 0));
    }

    /**
     * Mensagem amigável de retorno por modo + quantidade de lançamentos.
     */
    private function mensagemSucessoAnexo(string $modo, int $qtd): string
    {
        return match ($modo) {
            'baixar_total'      => $qtd > 1
                ? "Documento anexado e {$qtd} lançamentos baixados com sucesso."
                : 'Documento anexado e lançamento baixado com sucesso.',
            'pagamento_parcial' => 'Documento anexado e pagamento parcial registrado com sucesso.',
            default             => $qtd > 1
                ? "Documento anexado a {$qtd} lançamentos com sucesso."
                : 'Documento anexado ao lançamento com sucesso.',
        };
    }

    /**
     * Renderiza HTML dos itens extraídos usando Blade
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * Tipos de documento que representam uma transação única (todos os itens
     * pertencem à mesma compra e devem gerar apenas UM lançamento).
     */
    private const SINGLE_TRANSACTION_TYPES = [
        'NF-e', 'NFC-e', 'CUPOM', 'CUPOM_FISCAL', 'NOTA_FISCAL',
        'FATURA_CARTAO', 'BOLETO', 'RECIBO', 'COMPROVANTE',
    ];

    public function renderExtractedEntries(Request $request)
    {
        try {
            $request->validate([
                'extracted_data' => 'required|array',
            ]);

            $extractedData = $request->extracted_data;

            // Verificar se há dados extraídos
            if (!isset($extractedData['itens']) || empty($extractedData['itens'])) {
                $html = View::make('app.financeiro.domusia.partials.components.extracted-data-empty')->render();
                return response()->json([
                    'success' => true,
                    'html' => $html,
                ]);
            }

            $fornecedor = $extractedData['estabelecimento']['nome'] ?? 'Fornecedor não informado';
            $dataEmissao = $extractedData['financeiro']['data_emissao'] ?? '';
            $formaPagamento = $extractedData['financeiro']['forma_pagamento'] ?? '';
            $tipoDocumento = $extractedData['tipo_documento'] ?? '';

            // Formatar data
            $dataFormatada = '-';
            if ($dataEmissao) {
                try {
                    $data = Carbon::parse($dataEmissao);
                    $dataFormatada = $data->format('d/m/Y');
                } catch (\Exception $e) {
                    $dataFormatada = $dataEmissao;
                }
            }

            // Função auxiliar para determinar se é receita ou despesa
            $isReceita = function($item, $extractedData) {
                return false;
            };

            // Determinar se é um documento de transação única (cupom fiscal, NF-e, etc.)
            // Nesses casos, todos os itens pertencem à mesma compra = 1 lançamento
            $isSingleTransaction = in_array($tipoDocumento, self::SINGLE_TRANSACTION_TYPES, true);

            $html = '';

            if ($isSingleTransaction) {
                // ── Transação Única: consolidar todos os itens em UM card ──
                $valorTotal = floatval($extractedData['financeiro']['valor_total'] ?? 0);

                // Se valor_total não veio, somar os itens
                if ($valorTotal <= 0) {
                    foreach ($extractedData['itens'] as $item) {
                        $qtd = floatval($item['quantidade'] ?? 1);
                        $vlr = floatval($item['valor_unitario'] ?? $item['valor'] ?? 0);
                        $valorTotal += $qtd * $vlr;
                    }
                }

                $categoria = $extractedData['classificacao']['categoria_sugerida'] ?? 'Sem categoria';

                // Montar um item consolidado para o template
                $itemConsolidado = [
                    'descricao' => ($extractedData['classificacao']['descricao_detalhada'] ?? null)
                        ?: $tipoDocumento . ' - ' . $fornecedor,
                    'quantidade' => 1,
                    'valor_unitario' => $valorTotal,
                    'itens_consolidados' => count($extractedData['itens']),
                ];

                $html .= View::make('app.financeiro.domusia.partials.components.extracted-entry-item', [
                    'index' => 0,
                    'item' => $itemConsolidado,
                    'extractedData' => $extractedData,
                    'fornecedor' => $fornecedor,
                    'dataFormatada' => $dataFormatada,
                    'formaPagamento' => $formaPagamento,
                    'categoria' => $categoria,
                    'valorItem' => $valorTotal,
                    'isReceitaItem' => false,
                    'isSingleTransaction' => true,
                    'totalItens' => count($extractedData['itens']),
                    'tipoDocumento' => $tipoDocumento,
                ])->render();
            } else {
                // ── Múltiplas transações: um card por item ──
                foreach ($extractedData['itens'] as $index => $item) {
                    $isReceitaItem = $isReceita($item, $extractedData);
                    $valorItem = $item['valor_unitario'] ?? $item['valor'] ?? 0;
                    $categoria = $item['categoria_sugerida'] ?? $extractedData['classificacao']['categoria_sugerida'] ?? 'Sem categoria';

                    $html .= View::make('app.financeiro.domusia.partials.components.extracted-entry-item', [
                        'index' => $index,
                        'item' => $item,
                        'extractedData' => $extractedData,
                        'fornecedor' => $fornecedor,
                        'dataFormatada' => $dataFormatada,
                        'formaPagamento' => $formaPagamento,
                        'categoria' => $categoria,
                        'valorItem' => $valorItem,
                        'isReceitaItem' => $isReceitaItem,
                        'isSingleTransaction' => false,
                        'totalItens' => 0,
                        'tipoDocumento' => $tipoDocumento,
                    ])->render();
                }
            }

            return response()->json([
                'success' => true,
                'html' => $html,
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao renderizar itens extraídos', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao renderizar itens extraídos',
            ], 500);
        }
    }
}

