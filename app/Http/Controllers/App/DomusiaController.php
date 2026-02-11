<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Services\Ai\DocumentExtractorService;
use App\Services\DocumentViewerService;
use App\Exceptions\Ai\DocumentExtractionException;
use App\Models\DomusDocumento;
use Illuminate\Http\Request;
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
            $status = $extractedData ? 'processado' : 'pendente';

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
                        'status' => 'erro',
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

