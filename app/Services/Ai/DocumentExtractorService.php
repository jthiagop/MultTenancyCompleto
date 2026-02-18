<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Exceptions\Ai\DocumentExtractionException;
use App\Models\FormasPagamento;
use App\Models\LancamentoPadrao;
use Imagick;

class DocumentExtractorService
{
    private const OPENAI_API_URL = 'https://api.openai.com/v1/chat/completions';
    private const MODEL = 'gpt-4o-2024-08-06'; // Snapshot com suporte garantido a Structured Outputs
    private const MAX_TOKENS = 3000;

    // Limites de tamanho de arquivo (em bytes)
    private const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB - limite seguro para produção

    // Configurações de conversão de PDF para imagem
    private const PDF_CONVERSION_DPI = 200; // DPI otimizado para OCR (balanço entre qualidade e tamanho)
    private const MAX_PDF_PAGES = 5; // Limite de páginas para evitar custo excessivo de tokens
    private const PDF_IMAGE_FORMAT = 'png'; // PNG mantém melhor qualidade de texto

    /**
     * ID da empresa ativa (para filtrar lançamentos padrão)
     * @var int|null
     */
    private ?int $companyId;

    /**
     * Construtor do serviço
     * @param int|null $companyId ID da empresa (default: sessão ativa)
     */
    public function __construct(?int $companyId = null)
    {
        $this->companyId = $companyId ?? session('active_company_id');
    }

    /**
     * Extrai dados estruturados de um documento fiscal brasileiro
     *
     * @param string $base64Content Conteúdo do documento em Base64
     * @param string $mimeType Tipo MIME do documento (ex: image/png, application/pdf)
     * @return array Dados extraídos do documento
     * @throws DocumentExtractionException
     */
    public function extractData(string $base64Content, string $mimeType): array
    {
        try {
            // Validar se a chave da API está configurada
            $apiKey = config('services.openai.key');
            if (empty($apiKey)) {
                Log::error('OPENAI_API_KEY não configurada no ambiente');
                throw new DocumentExtractionException('Chave da API OpenAI não configurada');
            }

            // Validar mimeType
            if (!$this->isValidMimeType($mimeType)) {
                Log::error('Tipo MIME não suportado', ['mime_type' => $mimeType]);
                throw new DocumentExtractionException('Tipo de arquivo não suportado. Use imagens (PNG, JPG) ou PDF.');
            }

            // Para PDFs, converter para imagens (estratégia 100% documentada pela OpenAI)
            $fileContents = [];

            if ($mimeType === 'application/pdf') {
                Log::info('Convertendo PDF para imagens antes do processamento');
                $startTime = microtime(true);

                $imagePages = $this->convertPdfToImages($base64Content);

                $conversionTime = round((microtime(true) - $startTime) * 1000, 2);
                Log::info('PDF convertido com sucesso', [
                    'page_count' => count($imagePages),
                    'conversion_time_ms' => $conversionTime,
                ]);

                // Construir array de conteúdo com todas as páginas
                foreach ($imagePages as $index => $imageBase64) {
                    $fileContents[] = [
                        'type' => 'image_url',
                        'image_url' => [
                            'url' => "data:image/png;base64,{$imageBase64}",
                        ],
                    ];
                }
            } else {
                // Para imagens, usar diretamente
                $fileContents[] = $this->buildFileContent($base64Content, $mimeType);
            }

            // Montar prompt do sistema
            $systemPrompt = $this->getSystemPrompt();

            // Fazer requisição para OpenAI usando chat/completions
            // Com retry para erros transitórios (429, 5xx)
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])
                ->timeout(120)
                ->retry(3, 100, function ($exception, $request) {
                    // Retry apenas para erros transitórios
                    if ($exception instanceof \Illuminate\Http\Client\RequestException) {
                        // Defensive: verificar se response existe antes de acessar
                        if (isset($exception->response)) {
                            $status = $exception->response->status();
                            // 429: Rate limit, 500-504: Server errors
                            return in_array($status, [429, 500, 502, 503, 504]);
                        }
                    }
                    return false;
                }, throw: false)
                ->post(self::OPENAI_API_URL, [
                    'model' => self::MODEL,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => $systemPrompt,
                        ],
                        [
                            'role' => 'user',
                            'content' => [
                                [
                                    'type' => 'text',
                                    'text' => 'Analise este documento fiscal brasileiro e extraia todos os dados conforme o schema JSON. '
                                        . 'ATENÇÃO: (1) valor_total = valor FINAL a pagar (se houver Troco, calcule: Pago − Troco). '
                                        . '(2) Preencha data_vencimento para boletos/faturas. '
                                        . '(3) Se a lista de formas de pagamento foi fornecida, preencha forma_pagamento_id com o ID correspondente. '
                                        . '(4) Se a lista de lançamentos padrão foi fornecida, preencha lancamento_padrao_id com o ID mais adequado. '
                                        . '(5) Para cada item, calcule valor_total_item = quantidade × valor_unitario.',
                                ],
                                ...$fileContents, // Spread operator: suporta múltiplas imagens (páginas de PDF)
                            ],
                        ],
                    ],
                    'response_format' => [
                        'type' => 'json_schema',
                        'json_schema' => [
                            'name' => 'document_extraction',
                            'strict' => true,
                            'schema' => $this->getResponseSchema(),
                        ],
                    ],
                    'temperature' => 0.0, // Zero criatividade, máxima precisão
                    'max_tokens' => self::MAX_TOKENS,
                ]);

            // Verificar se a resposta existe após retries (throw: false pode retornar null)
            if (!$response) {
                Log::error('Resposta nula da API OpenAI após retries');
                throw new DocumentExtractionException('Falha na comunicação com a API OpenAI após múltiplas tentativas');
            }

            // Verificar se a requisição foi bem-sucedida
            if (!$response->successful()) {
                $errorData = $response->json();
                $errorMessage = $errorData['error']['message'] ?? 'Erro desconhecido da API OpenAI';
                $errorType = $errorData['error']['type'] ?? null;
                $errorCode = $errorData['error']['code'] ?? null;

                // LGPD: Não loga response body que pode conter dados sensíveis
                Log::error('Erro na API OpenAI', [
                    'status' => $response->status(),
                    'error_type' => $errorType,
                    'error_code' => $errorCode,
                    'error_message' => $errorMessage,
                ]);

                // Mensagens amigáveis para erros comuns
                $friendlyMessage = $this->getFriendlyErrorMessage($errorMessage, $errorType, $errorCode);
                throw new DocumentExtractionException($friendlyMessage);
            }

            // Extrair e validar resposta
            $responseData = $response->json();
            $message = $responseData['choices'][0]['message'] ?? null;

            if (!$message) {
                Log::error('Resposta sem mensagem da API OpenAI');
                throw new DocumentExtractionException('Resposta inválida da API OpenAI');
            }

            // Verificar se houve recusa (refusal) do Structured Outputs
            $refusal = $message['refusal'] ?? null;
            if ($refusal !== null) {
                Log::warning('OpenAI recusou processar o documento', [
                    'refusal_reason' => $refusal,
                ]);
                throw new DocumentExtractionException(
                    'Não foi possível analisar este documento. O conteúdo pode não ser compatível com o formato esperado ou pode conter informações que não podem ser processadas.'
                );
            }

            $content = $message['content'] ?? null;
            if (empty($content)) {
                Log::error('Resposta vazia da API OpenAI (sem refusal explícito)');
                throw new DocumentExtractionException('Resposta vazia da API OpenAI');
            }

            // Decodificar JSON
            $extractedData = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Erro ao decodificar JSON da resposta', [
                    'json_error' => json_last_error_msg(),
                    // LGPD: Não loga o conteúdo completo que pode ter dados sensíveis
                ]);
                throw new DocumentExtractionException('Resposta inválida da API OpenAI');
            }

            // Validar estrutura básica do retorno
            $extractedData = $this->validateAndNormalizeData($extractedData);

            return $extractedData;

        } catch (DocumentExtractionException $e) {
            // Re-lançar exceções personalizadas
            throw $e;
        } catch (\Exception $e) {
            Log::error('Erro inesperado ao extrair dados do documento', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw new DocumentExtractionException('Erro inesperado ao processar documento: ' . $e->getMessage());
        }
    }

    /**
     * Sugere um nome descritivo para o documento usando IA
     *
     * @param string $base64Content Conteúdo do documento em Base64
     * @param string $mimeType Tipo MIME do documento
     * @return string Nome sugerido para o documento
     */
    public function suggestDocumentName(string $base64Content, string $mimeType): string
    {
        try {
            // Para PDFs, a API não aceita no formato image_url, então vamos usar nome baseado no tipo
            if ($mimeType === 'application/pdf') {
                return 'Documento PDF ' . date('d-m-Y H\hi');
            }

            // Validar se a chave da API está configurada
            $apiKey = config('services.openai.key');
            if (empty($apiKey)) {
                // Se não tiver API key, retornar nome genérico
                return $this->generateGenericName($mimeType);
            }

            // Validar mimeType (apenas imagens funcionam com a API atual)
            if (!str_starts_with($mimeType, 'image/')) {
                return $this->generateGenericName($mimeType);
            }

            // Construir data URI para o arquivo (apenas imagens)
            $fileContent = $this->buildFileContent($base64Content, $mimeType);

            // Fazer requisição para OpenAI com prompt descritivo
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post(self::OPENAI_API_URL, [
                        'model' => self::MODEL,
                        'messages' => [
                            [
                                'role' => 'system',
                                'content' => 'Você é um assistente que cria nomes descritivos para arquivos. Sempre retorne um nome descritivo curto baseado no conteúdo da imagem, sem extensão de arquivo, sem aspas, sem pontuação final. Se não conseguir identificar, use descrições genéricas factuais.',
                            ],
                            [
                                'role' => 'user',
                                'content' => [
                                    [
                                        'type' => 'text',
                                        'text' => 'Analise esta imagem e sugira um nome descritivo em português (máximo 80 caracteres). Se for documento fiscal, inclua: tipo, empresa e data. Se for outro tipo de imagem, descreva o conteúdo principal de forma objetiva. Exemplos: "Nota fiscal Ambev dezembro 2024", "Boleto energia elétrica venc 20-01", "Foto produto em estoque"',
                                    ],
                                    $fileContent,
                                ],
                            ],
                        ],
                        'max_tokens' => 100,
                    ]);

            if (!$response->successful()) {
                $errorData = $response->json();
                Log::warning('Erro ao sugerir nome do documento', [
                    'status' => $response->status(),
                    'error' => $errorData,
                    'error_message' => $errorData['error']['message'] ?? 'Mensagem não disponível',
                ]);
                return $this->generateGenericName($mimeType);
            }

            $responseData = $response->json();
            $suggestedName = trim($responseData['choices'][0]['message']['content'] ?? '');

            // Validar resposta - detectar recusas ou respostas inválidas
            $invalidPhrases = [
                'desculpe',
                'não posso',
                'não consigo',
                'i cannot',
                'i can\'t',
                'sorry',
                'unable to',
            ];

            foreach ($invalidPhrases as $phrase) {
                if (stripos($suggestedName, $phrase) !== false) {
                    Log::info('IA retornou recusa, usando nome genérico', [
                        'response' => $suggestedName,
                    ]);
                    return 'Imagem ' . date('d-m-Y H\hi');
                }
            }

            // Validar e limpar o nome sugerido
            if (empty($suggestedName) || strlen($suggestedName) < 3) {
                return $this->generateGenericName($mimeType);
            }

            // Limpar caracteres inválidos para nome de arquivo, mas preservar espaços e hífens
            $suggestedName = preg_replace('/[^\w\s\-]/', '', $suggestedName);
            $suggestedName = preg_replace('/\s+/', ' ', $suggestedName); // Normalizar espaços
            $suggestedName = trim($suggestedName);
            $suggestedName = substr($suggestedName, 0, 100); // Limitar tamanho

            return $suggestedName;

        } catch (\Exception $e) {
            Log::warning('Erro ao sugerir nome do documento', [
                'error' => $e->getMessage(),
            ]);
            return $this->generateGenericName($mimeType);
        }
    }

    /**
     * Gera um nome genérico quando a IA não está disponível
     *
     * @param string $mimeType
     * @return string
     */
    private function generateGenericName(string $mimeType): string
    {
        $type = 'documento';
        if ($mimeType === 'application/pdf') {
            $type = 'documento_pdf';
        } elseif (str_starts_with($mimeType, 'image/')) {
            $type = 'documento_imagem';
        }

        return $type . '_' . date('YmdHis');
    }

    /**
     * Gera um nome descritivo a partir dos dados extraídos do documento
     *
     * @param array $extractedData
     * @return string
     */
    public function generateNameFromExtractedData(array $extractedData): string
    {
        try {
            $parts = [];

            // Tipo do documento
            $tipoDoc = $extractedData['tipo_documento'] ?? null;
            if ($tipoDoc && $tipoDoc !== 'OUTRO') {
                $parts[] = $tipoDoc;
            }

            // Nome do estabelecimento
            $estabelecimento = $extractedData['estabelecimento']['nome'] ?? null;
            if ($estabelecimento) {
                // Simplificar nome da empresa (remover S.A., LTDA, etc)
                $estabelecimento = preg_replace('/\b(S\.?A\.?|LTDA\.?|ME|EPP)\b/i', '', $estabelecimento);
                $estabelecimento = trim($estabelecimento);
                $parts[] = 'da ' . $estabelecimento;
            }

            // Data de emissão
            $dataEmissao = $extractedData['financeiro']['data_emissao'] ?? null;
            if ($dataEmissao) {
                try {
                    $date = new \DateTime($dataEmissao);
                    $parts[] = 'ref ' . $date->format('m-Y');
                } catch (\Exception $e) {
                    // Ignorar se data inválida
                }
            }

            // Valor total
            $valorTotal = $extractedData['financeiro']['valor_total'] ?? null;
            if ($valorTotal) {
                $parts[] = 'R$ ' . number_format($valorTotal, 2, ',', '.');
            }

            // Montar nome final
            if (empty($parts)) {
                return 'Documento fiscal ' . date('d-m-Y');
            }

            $nome = implode(' ', $parts);

            // Limpar e limitar tamanho
            $nome = preg_replace('/[^\w\s\-,.]/', '', $nome);
            $nome = preg_replace('/\s+/', ' ', $nome);
            $nome = trim($nome);
            $nome = substr($nome, 0, 100);

            return $nome;

        } catch (\Exception $e) {
            Log::warning('Erro ao gerar nome a partir dos dados extraídos', [
                'error' => $e->getMessage(),
            ]);
            return 'Documento fiscal ' . date('d-m-Y');
        }
    }

    /**
     * Valida se o tipo MIME é suportado
     *
     * @param string $mimeType
     * @return bool
     */
    private function isValidMimeType(string $mimeType): bool
    {
        $supportedTypes = [
            'image/png',
            'image/jpeg',
            'image/jpg',
            'image/webp',
            'application/pdf',
        ];

        return in_array(strtolower($mimeType), $supportedTypes);
    }

    /**
     * Faz upload de um arquivo para a OpenAI Files API
     *
     * @param string $base64Content
     * @param string $mimeType
     * @return string file_id
     * @throws DocumentExtractionException
     */
    private function uploadFileToOpenAI(string $base64Content, string $mimeType): string
    {
        try {
            $apiKey = config('services.openai.key');

            // Decodificar base64 com validação estrita
            $fileContent = base64_decode($base64Content, true);
            if ($fileContent === false) {
                Log::error('Base64 inválido fornecido para upload');
                throw new DocumentExtractionException('Conteúdo base64 inválido');
            }

            // Validar tamanho do arquivo
            $fileSize = strlen($fileContent);
            if ($fileSize > self::MAX_FILE_SIZE) {
                $fileSizeMB = round($fileSize / (1024 * 1024), 2);
                $maxSizeMB = round(self::MAX_FILE_SIZE / (1024 * 1024), 2);

                Log::warning('Arquivo muito grande para upload', [
                    'file_size_mb' => $fileSizeMB,
                    'max_size_mb' => $maxSizeMB,
                ]);

                throw new DocumentExtractionException(
                    "O arquivo é muito grande ({$fileSizeMB}MB). Por favor, envie um arquivo menor que {$maxSizeMB}MB. "
                    . "Dica: Se for um PDF com várias páginas, extraia apenas as páginas necessárias para reduzir o tamanho."
                );
            }

            // Determinar extensão
            $extension = 'pdf';
            if (str_starts_with($mimeType, 'image/')) {
                $extension = str_replace('image/', '', $mimeType);
            }

            $filename = 'document_' . time() . '.' . $extension;

            // Fazer upload usando multipart/form-data com retry
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
            ])
                ->retry(3, 100, function ($exception, $request) {
                    // Retry apenas para erros transitórios
                    if ($exception instanceof \Illuminate\Http\Client\RequestException) {
                        // Defensive: verificar se response existe antes de acessar
                        if (isset($exception->response)) {
                            $status = $exception->response->status();
                            return in_array($status, [429, 500, 502, 503, 504]);
                        }
                    }
                    return false;
                }, throw: false)
                ->attach('file', $fileContent, $filename)
                ->post('https://api.openai.com/v1/files', [
                    'purpose' => 'user_data',
                ]);

            // Verificar se a resposta existe após retries
            if (!$response) {
                Log::error('Resposta nula do upload após retries');
                throw new DocumentExtractionException('Falha no upload do arquivo após múltiplas tentativas');
            }

            if (!$response->successful()) {
                $errorData = $response->json();
                // LGPD: Não loga errorData completo que pode conter dados sensíveis
                Log::error('Erro ao fazer upload do arquivo para OpenAI', [
                    'status' => $response->status(),
                    'error_type' => $errorData['error']['type'] ?? null,
                    'error_code' => $errorData['error']['code'] ?? null,
                    'error_message' => $errorData['error']['message'] ?? 'Erro desconhecido',
                ]);
                throw new DocumentExtractionException('Erro ao fazer upload do arquivo: ' . ($errorData['error']['message'] ?? 'Erro desconhecido'));
            }

            $responseData = $response->json();
            $fileId = $responseData['id'] ?? null;

            if (empty($fileId)) {
                throw new DocumentExtractionException('Upload bem-sucedido mas file_id não retornado');
            }

            Log::info('Arquivo enviado para OpenAI', [
                'file_id' => $fileId,
                'filename' => $filename,
            ]);

            return $fileId;

        } catch (DocumentExtractionException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Erro inesperado ao fazer upload do arquivo', [
                'error' => $e->getMessage(),
            ]);
            throw new DocumentExtractionException('Erro ao fazer upload: ' . $e->getMessage());
        }
    }

    /**
     * Converte PDF para array de imagens PNG em base64
     * Usa Imagick para renderizar cada página do PDF como imagem de alta qualidade
     *
     * @param string $base64Content Conteúdo do PDF em base64
     * @return array Array de strings base64 (uma por página)
     * @throws DocumentExtractionException
     */
    private function convertPdfToImages(string $base64Content): array
    {
        try {
            // Verificar se Imagick está disponível
            if (!extension_loaded('imagick')) {
                Log::error('Extensão Imagick não está instalada');
                throw new DocumentExtractionException(
                    'Processamento de PDF indisponível. Entre em contato com o suporte técnico. (Erro: Imagick extension not found)'
                );
            }

            // Decodificar base64 com validação estrita
            $pdfBinary = base64_decode($base64Content, true);
            if ($pdfBinary === false) {
                Log::error('Base64 inválido fornecido para conversão de PDF');
                throw new DocumentExtractionException('Conteúdo base64 do PDF inválido');
            }

            // Validar tamanho do arquivo
            $fileSize = strlen($pdfBinary);
            if ($fileSize > self::MAX_FILE_SIZE) {
                $fileSizeMB = round($fileSize / (1024 * 1024), 2);
                $maxSizeMB = round(self::MAX_FILE_SIZE / (1024 * 1024), 2);

                Log::warning('PDF muito grande para conversão', [
                    'file_size_mb' => $fileSizeMB,
                    'max_size_mb' => $maxSizeMB,
                ]);

                throw new DocumentExtractionException(
                    "O PDF é muito grande ({$fileSizeMB}MB). Por favor, envie um arquivo menor que {$maxSizeMB}MB."
                );
            }

            // Criar instância do Imagick
            $imagick = new \Imagick();

            // Configurar densidade (DPI) antes de ler o PDF
            // DPI mais alto = melhor qualidade de OCR, mas arquivo maior
            $imagick->setResolution(self::PDF_CONVERSION_DPI, self::PDF_CONVERSION_DPI);

            // Ler PDF da memória
            $imagick->readImageBlob($pdfBinary);

            // Obter número de páginas
            $pageCount = $imagick->getNumberImages();

            Log::info('PDF carregado para conversão', [
                'page_count' => $pageCount,
                'file_size_mb' => round($fileSize / (1024 * 1024), 2),
            ]);

            // Validar limite de páginas
            if ($pageCount > self::MAX_PDF_PAGES) {
                Log::warning('PDF excede limite de páginas, processando apenas as primeiras', [
                    'total_pages' => $pageCount,
                    'max_pages' => self::MAX_PDF_PAGES,
                ]);
            }

            $images = [];
            $processedPages = 0;

            // Iterar sobre cada página
            foreach ($imagick as $pageIndex => $page) {
                // Respeitar limite de páginas
                if ($processedPages >= self::MAX_PDF_PAGES) {
                    Log::info('Limite de páginas atingido, parando conversão', [
                        'processed' => $processedPages,
                    ]);
                    break;
                }

                try {
                    // Configurar formato de saída
                    $page->setImageFormat(self::PDF_IMAGE_FORMAT);

                    // Qualidade de compressão (85 é um bom balanço)
                    $page->setImageCompressionQuality(85);

                    // Converter para RGB (necessário para PNG)
                    $page->setImageColorspace(Imagick::COLORSPACE_SRGB);

                    // Obter imagem como blob e converter para base64
                    $imageBlob = $page->getImageBlob();
                    $imageBase64 = base64_encode($imageBlob);

                    $images[] = $imageBase64;
                    $processedPages++;

                    Log::debug('Página de PDF convertida', [
                        'page_number' => $pageIndex + 1,
                        'image_size_kb' => round(strlen($imageBlob) / 1024, 2),
                    ]);

                } catch (\Exception $e) {
                    Log::error('Erro ao converter página específica do PDF', [
                        'page_number' => $pageIndex + 1,
                        'error' => $e->getMessage(),
                    ]);
                    // Continuar com próxima página ao invés de falhar completamente
                    continue;
                }
            }

            // Limpar recursos do Imagick
            $imagick->clear();
            $imagick->destroy();

            // Verificar se conseguimos converter pelo menos uma página
            if (empty($images)) {
                Log::error('Nenhuma página do PDF pôde ser convertida');
                throw new DocumentExtractionException(
                    'Não foi possível converter o PDF. O arquivo pode estar corrompido ou protegido por senha.'
                );
            }

            return $images;

        } catch (DocumentExtractionException $e) {
            // Re-lançar exceções personalizadas
            throw $e;
        } catch (\ImagickException $e) {
            Log::error('Erro do Imagick ao converter PDF', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Mensagens amigáveis para erros comuns do Imagick
            $errorMessage = $e->getMessage();
            if (stripos($errorMessage, 'password') !== false || stripos($errorMessage, 'encrypted') !== false) {
                throw new DocumentExtractionException('O PDF está protegido por senha. Por favor, remova a proteção antes de enviar.');
            } elseif (stripos($errorMessage, 'corrupt') !== false) {
                throw new DocumentExtractionException('O PDF está corrompido. Por favor, verifique o arquivo e tente novamente.');
            } else {
                throw new DocumentExtractionException('Erro ao processar PDF: ' . $errorMessage);
            }
        } catch (\Exception $e) {
            Log::error('Erro inesperado ao converter PDF para imagens', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw new DocumentExtractionException('Erro inesperado ao processar PDF: ' . $e->getMessage());
        }
    }


    /**
     * Constrói o conteúdo do arquivo para envio à API
     * Todas as imagens (incluindo PDFs convertidos) usam image_url com base64
     *
     * @param string $base64Content
     * @param string $mimeType
     * @return array
     */
    private function buildFileContent(string $base64Content, string $mimeType): array
    {
        // Todas as imagens usam image_url (PDFs já foram convertidos antes de chegar aqui)
        return [
            'type' => 'image_url',
            'image_url' => [
                'url' => "data:{$mimeType};base64,{$base64Content}",
            ],
        ];
    }

    /**
     * Carrega lista de formas de pagamento ativas do banco
     * Retorna array compacto para injeção no prompt
     * 
     * @return array ['id' => 'nome', ...]
     */
    private function getFormasPagamentoList(): array
    {
        try {
            return FormasPagamento::where('ativo', true)
                ->orderBy('nome')
                ->pluck('nome', 'id')
                ->toArray();
        } catch (\Exception $e) {
            Log::error('Erro ao carregar formas de pagamento', [
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Carrega top 30 lançamentos padrão mais usados da empresa
     * Filtra por tipo se especificado (receita/despesa)
     * 
     * @param string|null $tipo 'receita' ou 'despesa'
     * @return array ['id' => 'description (category)', ...]
     */
    private function getLancamentosPadraoList(?string $tipo = null): array
    {
        try {
            if (!$this->companyId) {
                Log::warning('Company ID não definido, retornando lista vazia de lançamentos padrão');
                return [];
            }

            $query = LancamentoPadrao::where('company_id', $this->companyId);

            if ($tipo) {
                $query->where('type', $tipo);
            }

            // Top 30 mais usados (ou todos se não tiver relação com transações)
            // Por enquanto, apenas ordenar por categoria e descrição
            return $query->orderBy('category')
                ->orderBy('description')
                ->limit(30)
                ->get()
                ->mapWithKeys(fn($lp) => [
                    $lp->id => "{$lp->description} ({$lp->category})"
                ])
                ->toArray();

        } catch (\Exception $e) {
            Log::error('Erro ao carregar lançamentos padrão', [
                'error' => $e->getMessage(),
                'company_id' => $this->companyId,
            ]);
            return [];
        }
    }

    /**

     * Retorna o JSON Schema para Structured Outputs
     *
     * @return array
     */
    private function getResponseSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'tipo_documento' => [
                    'type' => 'string',
                    'enum' => ['NF-e', 'NFC-e', 'BOLETO', 'RECIBO', 'FATURA_CARTAO', 'COMPROVANTE', 'CUPOM', 'OUTRO'],
                    'description' => 'Tipo do documento fiscal',
                ],
                'estabelecimento' => [
                    'type' => 'object',
                    'properties' => [
                        'nome' => [
                            'type' => ['string', 'null'],
                            'description' => 'Nome do estabelecimento/fornecedor (EMITENTE)',
                        ],
                        'cnpj' => [
                            'type' => ['string', 'null'],
                            'description' => 'CNPJ apenas com números (14 dígitos, sem formatação)',
                        ],
                    ],
                    'required' => ['nome', 'cnpj'],
                    'additionalProperties' => false,
                ],
                'nfe_info' => [
                    'type' => 'object',
                    'properties' => [
                        'chave_acesso' => [
                            'type' => ['string', 'null'],
                            'description' => 'Chave de acesso da NF-e/NFC-e (44 dígitos numéricos, sem espaços)',
                        ],
                        'numero_nf' => [
                            'type' => ['string', 'null'],
                            'description' => 'Número da nota fiscal (apenas dígitos, sem zeros à esquerda)',
                        ],
                        'serie' => [
                            'type' => ['string', 'null'],
                            'description' => 'Série da nota fiscal',
                        ],
                        'emitente' => [
                            'type' => 'object',
                            'properties' => [
                                'nome' => [
                                    'type' => ['string', 'null'],
                                    'description' => 'Nome/Razão Social do emitente',
                                ],
                                'cnpj' => [
                                    'type' => ['string', 'null'],
                                    'description' => 'CNPJ do emitente (apenas dígitos)',
                                ],
                            ],
                            'required' => ['nome', 'cnpj'],
                            'additionalProperties' => false,
                        ],
                        'destinatario' => [
                            'type' => 'object',
                            'properties' => [
                                'nome' => [
                                    'type' => ['string', 'null'],
                                    'description' => 'Nome do destinatário',
                                ],
                                'cnpj_cpf' => [
                                    'type' => ['string', 'null'],
                                    'description' => 'CNPJ ou CPF do destinatário (apenas dígitos)',
                                ],
                            ],
                            'required' => ['nome', 'cnpj_cpf'],
                            'additionalProperties' => false,
                        ],
                    ],
                    'required' => ['chave_acesso', 'numero_nf', 'serie', 'emitente', 'destinatario'],
                    'additionalProperties' => false,
                ],
                'financeiro' => [
                    'type' => 'object',
                    'properties' => [
                        'data_emissao' => [
                            'type' => ['string', 'null'],
                            'description' => 'Data de emissão no formato YYYY-MM-DD',
                        ],
                        'data_vencimento' => [
                            'type' => ['string', 'null'],
                            'description' => 'Data de vencimento no formato YYYY-MM-DD (boletos, faturas). Null se não aplicável.',
                        ],
                        'valor_total' => [
                            'type' => 'number',
                            'description' => 'Valor FINAL a pagar (após juros, multa, desconto). REGRA: Valor Pago − Troco, ou Subtotal ± ajustes.',
                        ],
                        'valor_principal' => [
                            'type' => 'number',
                            'description' => 'Valor original/subtotal ANTES de juros, multa e desconto',
                        ],
                        'forma_pagamento' => [
                            'type' => ['string', 'null'],
                            'description' => 'Nome descritivo da forma de pagamento (ex: "PIX", "Cartão de Crédito", "Dinheiro")',
                        ],
                        'forma_pagamento_id' => [
                            'type' => ['integer', 'null'],
                            'description' => 'ID da forma de pagamento correspondente na lista fornecida. Null se não encontrar correspondência.',
                        ],
                        'numero_documento' => [
                            'type' => ['string', 'null'],
                            'description' => 'Número do documento (boleto, NF, recibo)',
                        ],
                        'juros' => [
                            'type' => 'number',
                            'description' => 'Valor de juros/mora/encargos. 0.00 se não houver.',
                        ],
                        'multa' => [
                            'type' => 'number',
                            'description' => 'Valor de multa por atraso. 0.00 se não houver.',
                        ],
                        'desconto' => [
                            'type' => 'number',
                            'description' => 'Valor de desconto/abatimento. 0.00 se não houver.',
                        ],
                        'impostos_retidos' => [
                            'type' => 'number',
                            'description' => 'Total de impostos retidos (ISS, IRRF, PIS, COFINS, CSLL). 0.00 se não houver.',
                        ],
                        'observacoes_financeiras' => [
                            'type' => ['string', 'null'],
                            'description' => 'Notas sobre cobrança, vencimento, juros/multa, parcelamento',
                        ],
                    ],
                    'required' => [
                        'data_emissao',
                        'data_vencimento',
                        'valor_total',
                        'valor_principal',
                        'forma_pagamento',
                        'forma_pagamento_id',
                        'numero_documento',
                        'juros',
                        'multa',
                        'desconto',
                        'impostos_retidos',
                        'observacoes_financeiras',
                    ],
                    'additionalProperties' => false,
                ],
                'parcelamento' => [
                    'type' => 'object',
                    'properties' => [
                        'is_parcelado' => [
                            'type' => 'boolean',
                            'description' => 'Indica se é parcelado',
                        ],
                        'parcela_atual' => [
                            'type' => 'integer',
                            'description' => 'Número da parcela atual (1 se não parcelado)',
                        ],
                        'total_parcelas' => [
                            'type' => 'integer',
                            'description' => 'Total de parcelas (1 se não parcelado)',
                        ],
                        'frequencia' => [
                            'type' => 'string',
                            'description' => 'Frequência: MENSAL, SEMANAL, QUINZENAL, ANUAL ou UNICA',
                        ],
                    ],
                    'required' => ['is_parcelado', 'parcela_atual', 'total_parcelas', 'frequencia'],
                    'additionalProperties' => false,
                ],
                'classificacao' => [
                    'type' => 'object',
                    'properties' => [
                        'descricao_detalhada' => [
                            'type' => ['string', 'null'],
                            'description' => 'Descrição detalhada do item ou serviço principal',
                        ],
                        'categoria_sugerida' => [
                            'type' => ['string', 'null'],
                            'description' => 'Categoria sugerida para classificação contábil',
                        ],
                        'lancamento_padrao_id' => [
                            'type' => ['integer', 'null'],
                            'description' => 'ID do lançamento padrão correspondente na lista fornecida. Null se não encontrar correspondência.',
                        ],
                        'codigo_referencia' => [
                            'type' => ['string', 'null'],
                            'description' => 'Código de referência do documento',
                        ],
                    ],
                    'required' => ['descricao_detalhada', 'categoria_sugerida', 'lancamento_padrao_id', 'codigo_referencia'],
                    'additionalProperties' => false,
                ],
                'observacoes' => [
                    'type' => ['string', 'null'],
                    'description' => 'Alertas gerais e contextuais sobre o documento',
                ],
                'itens' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'descricao' => [
                                'type' => ['string', 'null'],
                                'description' => 'Descrição limpa do item (sem códigos EAN/GTIN)',
                            ],
                            'quantidade' => [
                                'type' => 'number',
                                'description' => 'Quantidade do item',
                            ],
                            'valor_unitario' => [
                                'type' => 'number',
                                'description' => 'Preço de UMA unidade (não o subtotal da linha)',
                            ],
                            'valor_total_item' => [
                                'type' => 'number',
                                'description' => 'Valor total do item (quantidade × valor_unitario)',
                            ],
                            'categoria_sugerida' => [
                                'type' => ['string', 'null'],
                                'description' => 'Categoria sugerida para o item',
                            ],
                        ],
                        'required' => ['descricao', 'quantidade', 'valor_unitario', 'valor_total_item', 'categoria_sugerida'],
                        'additionalProperties' => false,
                    ],
                    'description' => 'Lista de itens/produtos do documento',
                ],
            ],
            'required' => [
                'tipo_documento',
                'estabelecimento',
                'nfe_info',
                'financeiro',
                'parcelamento',
                'classificacao',
                'observacoes',
                'itens',
            ],
            'additionalProperties' => false,
        ];
    }

    /**
     * Retorna o prompt do sistema para a IA com REGRAS DE NEGÓCIO BRASILEIRAS
     * Dinâmico: injeta formas de pagamento e lançamentos padrão do banco
     *
     * @return string
     */
    private function getSystemPrompt(): string
    {
        $basePrompt = <<<'PROMPT'
Você é um especialista contábil brasileiro, com experiência em Contabilidade Eclesial e Gestão de Paróquias e Conventos.

Sua tarefa é extrair dados estruturados de documentos fiscais brasileiros com máxima precisão.

═══════════════════════════════════════════════════
1. REGRAS GERAIS
═══════════════════════════════════════════════════

- Datas no formato YYYY-MM-DD. Se o ano for omitido, assuma o ano atual.
- CNPJ/CPF: apenas dígitos, sem formatação.
- NUNCA invente dados — se não encontrar, retorne null (strings) ou 0.00 (números).

═══════════════════════════════════════════════════
2. TIPOS DE DOCUMENTO
═══════════════════════════════════════════════════

- **NF-e**: Nota Fiscal Eletrônica (DANFE A4).
- **NFC-e**: Nota Fiscal de Consumidor Eletrônica (tira de papel, cupom eletrônico).
- **CUPOM**: Cupom fiscal antigo (não eletrônico) ou SAT/CF-e.
- **BOLETO**: Boleto bancário (com código de barras, linha digitável).
- **RECIBO**: Recibo simples de serviço, doação, pagamento manual.
- **FATURA_CARTAO**: Fatura ou comprovante de cartão de crédito/débito (TEF/POS, resumo de fatura).
- **COMPROVANTE**: Comprovante de transferência bancária, PIX, TED, DOC, depósito.
- **OUTRO**: Documento não classificável nas categorias acima.

═══════════════════════════════════════════════════
3. VALOR TOTAL — REGRA DE OURO (CRÍTICO)
═══════════════════════════════════════════════════

O campo valor_total deve conter o VALOR FINAL A PAGAR.

⚠️ ARMADILHAS — NÃO confunda com:
- "VALOR PAGO R$" = dinheiro entregue ao caixa (pode incluir troco).
- "SUBTOTAL" = total parcial antes de descontos/acréscimos.

REGRAS DE CÁLCULO:
- Se houver "VALOR PAGO" e "TROCO": valor_total = Valor Pago − Troco.
- Se houver "SUBTOTAL" e "DESCONTO": valor_total = Subtotal − Desconto.
- Se houver "SUBTOTAL" e "ACRÉSCIMO" (gorjeta, taxa): valor_total = Subtotal + Acréscimo.
- valor_principal = subtotal original (ANTES de ajustes).

Exemplo:
  TOTAL R$: 82,85 ← valor_total
  Valor Pago: R$ 100,00 ← NÃO usar
  Troco: R$ 17,15 ← Confirma: 100,00 − 17,15 = 82,85 ✓

═══════════════════════════════════════════════════
4. REGRAS POR TIPO DE DOCUMENTO
═══════════════════════════════════════════════════

**NF-e / DANFE:**
- valor_total = "VALOR TOTAL DA NOTA" (bloco Cálculo do Imposto), NÃO "Valor dos Produtos".
- Prefira "DATA DE EMISSÃO" (dhEmi). Fallback: "DATA DE SAÍDA" (dhSaiEnt).
- Chave de acesso = 44 dígitos (sem espaços).
- estabelecimento = EMITENTE (fornecedor). Destinatário = quem comprou.
- Se NF-e de serviço, busque impostos retidos (ISS, IRRF, PIS, COFINS, CSLL).

**NFC-e / CUPOM:**
- Itens: ignore códigos EAN/GTIN. "1,000 UN" → 1.0. "0,500 KG" → 0.5.
- valor_unitario = preço de UMA unidade, NÃO subtotal da linha.
- valor_total_item = quantidade × valor_unitario.
- Taxa de serviço/gorjeta: some ao valor_total e registre em observacoes_financeiras.

**BOLETO:**
- valor_principal = "Valor do Documento" (original).
- valor_total = "Valor Cobrado" (com juros/multa/desconto).
- data_emissao = data de emissão do boleto.
- data_vencimento = "Data de Vencimento" (campo dedicado, obrigatório para boletos).
- BENEFICIÁRIO (Cedente) → estabelecimento.nome. PAGADOR (Sacado) → nfe_info.destinatario.

**RECIBO:**
- Pode não ter CNPJ (retorne null).
- Doações/dízimo: categoria_sugerida = "DOAÇÃO/DÍZIMO".

**COMPROVANTE (PIX/TED/DOC/Depósito):**
- Identifique o tipo de transferência.
- Extraia remetente, destinatário, valor, data/hora.
- Em comprovantes PIX, busque a chave PIX e o ID da transação.

**FATURA_CARTAO:**
- Identifique bandeira, crédito vs débito, parcelas.
- Em comprovantes TEF/POS: extraia NSU, código de autorização.

═══════════════════════════════════════════════════
5. DATAS
═══════════════════════════════════════════════════

- data_emissao: Data de emissão/realização do documento. SEMPRE YYYY-MM-DD.
- data_vencimento: Apenas para boletos, faturas ou documentos com vencimento explícito. Null se não aplicável.
- Para boletos: AMBAS as datas devem ser preenchidas quando disponíveis.

═══════════════════════════════════════════════════
6. JUROS, MULTA, DESCONTO E PARCELAMENTO
═══════════════════════════════════════════════════

- Juros: "Juros", "Encargos", "Mora". Se não encontrar → 0.00.
- Multa: "Multa", "Multa por Atraso". Se não encontrar → 0.00.
- Desconto: "Desconto", "Abatimento". Se não encontrar → 0.00.
- Impostos retidos: Some ISS + IRRF + PIS + COFINS + CSLL. Se não encontrar → 0.00.
- Parcelamento: "Parcela 1/6", "3x de R$ 50", "01/12" → is_parcelado=true.

═══════════════════════════════════════════════════
7. CONTEXTO ECLESIAL
═══════════════════════════════════════════════════

- Itens litúrgicos (velas, hóstias, vinho canônico, incenso, paramentos) → categoria "LITURGIA".
- Grandes compras de descartáveis/alimentos → verificar se é Quermesse/Festa → "FESTAS/EVENTOS".
- Materiais de construção/elétrica/hidráulica → "MANUTENÇÃO".
- Doações e dízimo → "DOAÇÃO/DÍZIMO".
- Se data próxima de Páscoa, Natal, Corpus Christi → mencionar nas observacoes.

═══════════════════════════════════════════════════
8. NF-e/NFC-e — CAMPOS ESPECÍFICOS
═══════════════════════════════════════════════════

- Se o documento NÃO for NF-e/NFC-e, preencha TODOS os campos de nfe_info com null.
- numero_nf: apenas dígitos, sem zeros à esquerda ("000.123.456" → "123456").
- serie: apenas o número ("Série 1" → "1").

═══════════════════════════════════════════════════
9. OBSERVAÇÕES — DOIS CAMPOS DISTINTOS
═══════════════════════════════════════════════════

- financeiro.observacoes_financeiras: APENAS dados de cobrança (vencimento, juros, parcelas).
- observacoes: Alertas contextuais ("Documento vencido", "Possível vinho canônico", "Valor alto - conferir").

═══════════════════════════════════════════════════
10. ITENS DO DOCUMENTO
═══════════════════════════════════════════════════

- Extraia todos os itens com descrição limpa (sem EAN), quantidade, valor_unitario.
- Calcule valor_total_item = quantidade × valor_unitario para cada item.
- A soma dos valor_total_item deve ser ≈ valor_principal (use como validação interna).
PROMPT;

        // ═══ SEÇÃO DINÂMICA: Formas de Pagamento do Banco ═══
        $formasPagamento = $this->getFormasPagamentoList();
        $formasPagamentoSection = '';

        if (!empty($formasPagamento)) {
            $listaFormas = collect($formasPagamento)
                ->map(fn($nome, $id) => "  - ID {$id}: {$nome}")
                ->implode("\n");

            $formasPagamentoSection = <<<FORMAS

═══════════════════════════════════════════════════
11. FORMAS DE PAGAMENTO CADASTRADAS
═══════════════════════════════════════════════════

Ao identificar a forma de pagamento do documento, preencha:
- forma_pagamento: texto descritivo identificado no documento.
- forma_pagamento_id: o ID correspondente da lista abaixo. Se não houver correspondência clara, retorne null.

Lista de formas de pagamento:
{$listaFormas}

Dicas de mapeamento:
- "Dinheiro" → procure por DINHEIRO
- "PIX", "Chave PIX" → procure por PIX
- "Cartão de Crédito" → procure por opção de crédito (CC_OUTROS ou similar)
- "Cartão de Débito" → procure por opção de débito (CD_OUTROS ou similar)
- "Boleto" → procure por BOLETO
- "Transferência", "TED", "DOC" → procure por TRANSFERENCIA
- "Depósito" → procure por DEPOSITO
- "Cheque" → procure por CHEQUE
FORMAS;
        }

        // ═══ SEÇÃO DINÂMICA: Lançamentos Padrão da Empresa ═══
        $lancamentosPadrao = $this->getLancamentosPadraoList();
        $lancamentosPadraoSection = '';

        if (!empty($lancamentosPadrao)) {
            $listaLancamentos = collect($lancamentosPadrao)
                ->map(fn($descricao, $id) => "  - ID {$id}: {$descricao}")
                ->implode("\n");

            $lancamentosPadraoSection = <<<LANCAMENTOS

═══════════════════════════════════════════════════
12. LANÇAMENTOS PADRÃO DA EMPRESA
═══════════════════════════════════════════════════

Ao analisar o documento, tente identificar qual lançamento padrão melhor corresponde ao conteúdo.
Preencha classificacao.lancamento_padrao_id com o ID mais adequado. Se nenhum corresponder, retorne null.

{$listaLancamentos}
LANCAMENTOS;
        }

        return $basePrompt . $formasPagamentoSection . $lancamentosPadraoSection;
    }



    /**
     * Retorna mensagem de erro amigável baseada no tipo de erro da API
     *
     * @param string $errorMessage
     * @param string|null $errorType
     * @param string|null $errorCode
     * @return string
     */
    private function getFriendlyErrorMessage(string $errorMessage, ?string $errorType, ?string $errorCode): string
    {
        // Erro de quota/billing
        if (
            stripos($errorMessage, 'quota') !== false ||
            stripos($errorMessage, 'billing') !== false ||
            stripos($errorMessage, 'exceeded') !== false ||
            $errorCode === 'insufficient_quota'
        ) {
            return 'A cota da API OpenAI foi excedida. Por favor, verifique seu plano e detalhes de cobrança na plataforma OpenAI. Acesse: https://platform.openai.com/account/billing';
        }

        // Erro de autenticação
        if (
            stripos($errorMessage, 'invalid') !== false &&
            (stripos($errorMessage, 'api key') !== false || stripos($errorMessage, 'authentication') !== false) ||
            $errorCode === 'invalid_api_key'
        ) {
            return 'Chave da API OpenAI inválida. Verifique a configuração da variável OPENAI_API_KEY no arquivo .env';
        }

        // Erro de rate limit
        if (stripos($errorMessage, 'rate limit') !== false || $errorCode === 'rate_limit_exceeded') {
            return 'Limite de requisições excedido. Aguarde alguns instantes e tente novamente.';
        }

        // Erro de modelo não disponível
        if (
            stripos($errorMessage, 'model') !== false &&
            (stripos($errorMessage, 'not found') !== false || stripos($errorMessage, 'not available') !== false)
        ) {
            return 'Modelo de IA não disponível. Verifique se o modelo gpt-4o está disponível na sua conta OpenAI.';
        }

        // Erro de contexto muito longo
        if (
            stripos($errorMessage, 'context length') !== false ||
            stripos($errorMessage, 'maximum context') !== false
        ) {
            return 'O documento é muito grande para processar. Tente com um arquivo menor ou de melhor qualidade.';
        }

        // Retorna mensagem original se não for um erro conhecido
        return "Erro ao processar documento: {$errorMessage}";
    }

    /**
     * Normaliza datas para o formato YYYY-MM-DD, lidando com formatos brasileiros
     *
     * @param mixed $date
     * @return string|null
     */
    private function normalizeDate($date): ?string
    {
        if ($date === null || $date === '') {
            return null;
        }

        $dateStr = trim((string) $date);

        // Se já está no formato YYYY-MM-DD válido, retorna
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateStr)) {
            try {
                $dt = new \DateTime($dateStr);
                // Valida se a data é real (ex: 2024-02-30 é inválida)
                if ($dt->format('Y-m-d') === $dateStr) {
                    return $dateStr;
                }
            } catch (\Exception $e) {
                // Continua para tentar outros formatos
            }
        }

        // Remove caracteres extras, mantém apenas números e separadores
        $dateStr = preg_replace('/[^\d\/\-.]/', '', $dateStr);

        // Tenta parsear formatos brasileiros comuns
        $patterns = [
            // dd/mm/yyyy ou dd-mm-yyyy
            '/^(\d{1,2})[\/\-\.](\d{1,2})[\/\-\.](\d{4})$/' => function ($matches) {
                return [$matches[3], $matches[2], $matches[1]]; // [ano, mês, dia]
            },
            // dd/mm ou dd-mm (sem ano - assume ano atual)
            '/^(\d{1,2})[\/\-\.](\d{1,2})$/' => function ($matches) {
                $currentYear = date('Y');
                return [$currentYear, $matches[2], $matches[1]]; // [ano, mês, dia]
            },
            // yyyy-mm-dd (já tentado acima, mas pode ter espaços)
            '/^(\d{4})[\/\-\.](\d{1,2})[\/\-\.](\d{1,2})$/' => function ($matches) {
                return [$matches[1], $matches[2], $matches[3]]; // [ano, mês, dia]
            },
        ];

        foreach ($patterns as $pattern => $extractor) {
            if (preg_match($pattern, $dateStr, $matches)) {
                list($year, $month, $day) = $extractor($matches);

                // Normalizar com zeros à esquerda
                $year = str_pad($year, 4, '0', STR_PAD_LEFT);
                $month = str_pad($month, 2, '0', STR_PAD_LEFT);
                $day = str_pad($day, 2, '0', STR_PAD_LEFT);

                // Validar se a data é real
                try {
                    $normalized = "$year-$month-$day";
                    $dt = new \DateTime($normalized);

                    // Verifica se a data criada corresponde aos valores fornecidos
                    // (previne datas como 2024-02-30 que são convertidas para 2024-03-02)
                    if ($dt->format('Y-m-d') === $normalized) {
                        return $normalized;
                    }
                } catch (\Exception $e) {
                    // Data inválida, continua tentando outros padrões
                    continue;
                }
            }
        }

        // Se nenhum padrão funcionou, loga e retorna null
        Log::warning('Formato de data não reconhecido', [
            'date_input' => $dateStr,
        ]);

        return null;
    }

    /**
     * Sanitiza CNPJ removendo caracteres não numéricos
     *
     * @param mixed $cnpj
     * @return string|null
     */
    private function sanitizeCNPJ($cnpj): ?string
    {
        if ($cnpj === null || $cnpj === '') {
            return null;
        }

        // Remove tudo que não for dígito
        $sanitized = preg_replace('/\D+/', '', (string) $cnpj);

        // Se ficou vazio após sanitização, retorna null
        return $sanitized !== '' ? $sanitized : null;
    }

    /**
     * Faz parse de valores monetários, suportando formato brasileiro (1.234,56) e internacional (1,234.56)
     *
     * @param mixed $value
     * @return float
     */
    private function parseMoney($value): float
    {
        if ($value === null) {
            return 0.0;
        }

        if (is_int($value) || is_float($value)) {
            return (float) $value;
        }

        $v = trim((string) $value);
        // Remove símbolos de moeda e espaços
        $v = preg_replace('/[^\d,.\-]/', '', $v);

        // Se está vazio após limpeza, retorna 0
        if ($v === '' || $v === '-') {
            return 0.0;
        }

        // Formato brasileiro: "1.234,56" => "1234.56"
        if (str_contains($v, '.') && str_contains($v, ',')) {
            // Assume que ponto é separador de milhar e vírgula é decimal
            $v = str_replace('.', '', $v);
            $v = str_replace(',', '.', $v);
        } elseif (str_contains($v, ',')) {
            // Apenas vírgula, assume que é decimal brasileiro
            $v = str_replace(',', '.', $v);
        }
        // Se apenas ponto, já está no formato correto (internacional)

        return (float) $v;
    }

    /**
     * Valida e normaliza os dados extraídos
     *
     * @param array $data
     * @return array
     */
    private function validateAndNormalizeData(array $data): array
    {
        // Garantir estrutura básica
        $normalized = [
            'tipo_documento' => $data['tipo_documento'] ?? 'OUTRO',
            'estabelecimento' => [
                'nome' => $data['estabelecimento']['nome'] ?? null,
                'cnpj' => $this->sanitizeCNPJ($data['estabelecimento']['cnpj'] ?? null),
            ],
            'nfe_info' => [
                'chave_acesso' => $this->sanitizeCNPJ($data['nfe_info']['chave_acesso'] ?? null), // Remove formatação
                'numero_nf' => $data['nfe_info']['numero_nf'] ?? null,
                'serie' => $data['nfe_info']['serie'] ?? null,
                'emitente' => [
                    'nome' => $data['nfe_info']['emitente']['nome'] ?? null,
                    'cnpj' => $this->sanitizeCNPJ($data['nfe_info']['emitente']['cnpj'] ?? null),
                ],
                'destinatario' => [
                    'nome' => $data['nfe_info']['destinatario']['nome'] ?? null,
                    'cnpj_cpf' => $this->sanitizeCNPJ($data['nfe_info']['destinatario']['cnpj_cpf'] ?? null),
                ],
            ],
            'financeiro' => [
                'data_emissao' => $this->normalizeDate($data['financeiro']['data_emissao'] ?? null),
                'data_vencimento' => $this->normalizeDate($data['financeiro']['data_vencimento'] ?? null),
                'valor_total' => $this->parseMoney($data['financeiro']['valor_total'] ?? 0),
                'valor_principal' => $this->parseMoney(
                    $data['financeiro']['valor_principal'] ?? $data['financeiro']['valor_total'] ?? 0
                ),
                'forma_pagamento' => $data['financeiro']['forma_pagamento'] ?? null,
                'forma_pagamento_id' => isset($data['financeiro']['forma_pagamento_id']) ? (int) $data['financeiro']['forma_pagamento_id'] : null,
                'numero_documento' => $data['financeiro']['numero_documento'] ?? null,
                'juros' => $this->parseMoney($data['financeiro']['juros'] ?? 0),
                'multa' => $this->parseMoney($data['financeiro']['multa'] ?? 0),
                'desconto' => $this->parseMoney($data['financeiro']['desconto'] ?? 0),
                'impostos_retidos' => $this->parseMoney($data['financeiro']['impostos_retidos'] ?? 0),
                'observacoes_financeiras' => $data['financeiro']['observacoes_financeiras'] ?? null,
            ],
            'parcelamento' => [
                'is_parcelado' => $data['parcelamento']['is_parcelado'] ?? false,
                'parcela_atual' => isset($data['parcelamento']['parcela_atual']) ? (int) $data['parcelamento']['parcela_atual'] : 1,
                'total_parcelas' => isset($data['parcelamento']['total_parcelas']) ? (int) $data['parcelamento']['total_parcelas'] : 1,
                'frequencia' => $data['parcelamento']['frequencia'] ?? 'UNICA',
            ],
            'classificacao' => [
                'descricao_detalhada' => $data['classificacao']['descricao_detalhada'] ?? null,
                'categoria_sugerida' => $data['classificacao']['categoria_sugerida'] ?? null,
                'codigo_referencia' => $data['classificacao']['codigo_referencia'] ?? null,
                'lancamento_padrao_id' => isset($data['classificacao']['lancamento_padrao_id']) ? (int) $data['classificacao']['lancamento_padrao_id'] : null,
            ],
            'observacoes' => $data['observacoes'] ?? null,
            'itens' => [],
        ];

        // Normalizar itens
        if (isset($data['itens']) && is_array($data['itens'])) {
            foreach ($data['itens'] as $item) {
                $quantidade = $this->parseMoney($item['quantidade'] ?? 0);
                $valorUnitario = $this->parseMoney($item['valor_unitario'] ?? 0);
                $valorTotalItem = isset($item['valor_total_item'])
                    ? $this->parseMoney($item['valor_total_item'])
                    : round($quantidade * $valorUnitario, 2);

                $normalized['itens'][] = [
                    'descricao' => $item['descricao'] ?? null,
                    'quantidade' => $quantidade,
                    'valor_unitario' => $valorUnitario,
                    'valor_total_item' => $valorTotalItem,
                    'categoria_sugerida' => $item['categoria_sugerida'] ?? null,
                ];
            }
        }

        return $normalized;
    }
}

