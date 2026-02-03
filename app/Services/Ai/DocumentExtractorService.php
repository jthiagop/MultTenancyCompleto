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
    private const MAX_TOKENS = 2000;

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
                                    'text' => 'Analise este documento fiscal brasileiro e extraia todos os dados relevantes conforme a estrutura JSON especificada. Procure por campos como juros, multa, parcelamento, impostos retidos. Retorne APENAS o JSON, sem explicações adicionais.',
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
                    'enum' => ['NF-e', 'NFC-e', 'BOLETO', 'RECIBO', 'FATURA_CARTAO', 'CUPOM', 'OUTRO'],
                    'description' => 'Tipo do documento fiscal',
                ],
                'estabelecimento' => [
                    'type' => 'object',
                    'properties' => [
                        'nome' => [
                            'type' => ['string', 'null'],
                            'description' => 'Nome do estabelecimento',
                        ],
                        'cnpj' => [
                            'type' => ['string', 'null'],
                            'description' => 'CNPJ apenas com números',
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
                            'description' => 'Chave de acesso da NF-e/NFC-e (44 dígitos)',
                        ],
                        'numero_nf' => [
                            'type' => ['string', 'null'],
                            'description' => 'Número da nota fiscal',
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
                                    'description' => 'Nome do emitente',
                                ],
                                'cnpj' => [
                                    'type' => ['string', 'null'],
                                    'description' => 'CNPJ do emitente',
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
                                    'description' => 'CNPJ ou CPF do destinatário',
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
                        'valor_total' => [
                            'type' => 'number',
                            'description' => 'Valor total do documento',
                        ],
                        'valor_principal' => [
                            'type' => 'number',
                            'description' => 'Valor principal sem encargos',
                        ],
                        'forma_pagamento' => [
                            'type' => ['string', 'null'],
                            'description' => 'Forma de pagamento utilizada',
                        ],
                        'numero_documento' => [
                            'type' => ['string', 'null'],
                            'description' => 'Número do documento',
                        ],
                        'juros' => [
                            'type' => 'number',
                            'description' => 'Valor de juros',
                        ],
                        'multa' => [
                            'type' => 'number',
                            'description' => 'Valor de multa',
                        ],
                        'desconto' => [
                            'type' => 'number',
                            'description' => 'Valor de desconto',
                        ],
                        'impostos_retidos' => [
                            'type' => 'number',
                            'description' => 'Valor de impostos retidos',
                        ],
                        'observacoes_financeiras' => [
                            'type' => ['string', 'null'],
                            'description' => 'Observações específicas sobre cobrança, vencimento, juros, multa ou parcelamento',
                        ],
                    ],
                    'required' => [
                        'data_emissao',
                        'valor_total',
                        'valor_principal',
                        'forma_pagamento',
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
                            'description' => 'Número da parcela atual',
                        ],
                        'total_parcelas' => [
                            'type' => 'integer',
                            'description' => 'Total de parcelas',
                        ],
                        'frequencia' => [
                            'type' => 'string',
                            'description' => 'Frequência do parcelamento',
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
                            'description' => 'Descrição detalhada do item ou serviço',
                        ],
                        'categoria_sugerida' => [
                            'type' => ['string', 'null'],
                            'description' => 'Categoria sugerida para classificação',
                        ],
                        'codigo_referencia' => [
                            'type' => ['string', 'null'],
                            'description' => 'Código de referência do documento',
                        ],
                    ],
                    'required' => ['descricao_detalhada', 'categoria_sugerida', 'codigo_referencia'],
                    'additionalProperties' => false,
                ],
                'observacoes' => [
                    'type' => ['string', 'null'],
                    'description' => 'Observações gerais sobre o documento',
                ],
                'itens' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'descricao' => [
                                'type' => ['string', 'null'],
                                'description' => 'Descrição do item',
                            ],
                            'quantidade' => [
                                'type' => 'number',
                                'description' => 'Quantidade do item',
                            ],
                            'valor_unitario' => [
                                'type' => 'number',
                                'description' => 'Valor unitário do item',
                            ],
                            'categoria_sugerida' => [
                                'type' => ['string', 'null'],
                                'description' => 'Categoria sugerida para o item',
                            ],
                        ],
                        'required' => ['descricao', 'quantidade', 'valor_unitario', 'categoria_sugerida'],
                        'additionalProperties' => false,
                    ],
                    'description' => 'Lista de itens do documento',
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
     * Retorna o prompt do sistema para a IA
     *
     * @return string
     */
    private function getSystemPrompt(): string
    {
        return <<<'PROMPT'
Você é um especialista contábil brasileiro com vasta experiência em análise de documentos fiscais, e Especialista em Contabilidade Eclesial e Gestão de Conventos e Paroquial.

Sua tarefa é analisar documentos fiscais brasileiros (NF-e, NFC-e, Boletos, Recibos de Maquininha, etc.) e extrair os dados de forma estruturada e precisa, sempre considerando a natureza legal e financeira dos documentos.

REGRAS DE EXTRAÇÃO:
1. Valores monetários devem ser números decimais (float), SEMPRE retorne 0.00 se não existir (não null)
2. Se o ano não estiver explícito na data, assuma o ano atual
3. Datas devem estar no formato YYYY-MM-DD
4. CNPJ deve ser retornado apenas com números (sem pontos, barras ou hífens)
5. Juros/Multa: Procure por campos explícitos ou textos como "Encargos", "Mora", "Juros"
6. Parcelamento: Procure padrões como "01/12", "Parc 1 de 3", "10x de..."
7. Se um campo numérico não existir (ex: juros), retorne 0.00. Não retorne null para valores monetários
8. Categorias sugeridas devem ser genéricas e relevantes para o contexto brasileiro

EXTRAÇÃO DE NF-e/NFC-e (IMPORTANTE):
- **chave_acesso**: Procure por "Chave de Acesso", código de 44 dígitos (geralmente em blocos de 4)
- **numero_nf**: Número da nota fiscal (ex: "Nº 000.123.456")
- **serie**: Série da NF (ex: "Série 1", "Série 001")
- **emitente**: Empresa que emitiu a nota (nome e CNPJ)
- **destinatario**: Quem recebeu a nota (nome e CNPJ/CPF)
- Se não for NF-e/NFC-e, preencha todos os campos de nfe_info com null

OBSERVAÇÕES (use dois campos distintos):
- **financeiro.observacoes_financeiras**: Notas sobre cobrança, vencimento, juros/multa, parcelamento (ex: "Vencimento em 5 dias", "Juros de 2% ao mês", "Parcela 3 de 12")
- **observacoes** (root): Alertas gerais e contextuais (ex: "Documento vencido", "Contém bebida alcoólica", "Compra próxima à Páscoa", "Juros altos detectados")

REGRAS ESPECÍFICAS DE CONTEXTO CATÓLICO:
1. MATERIAIS LITÚRGICOS: Se encontrar itens como 'Vinho Canônico', 'Partículas', 'Hóstias', 'Círio', 'Velas', 'Incenso', 'Carvão', 'Paramentos' (túnicas, estolas), classifique como LITURGIA ou CULTO.
2. FESTAS: Se for compra de grande quantidade de descartáveis, refrigerantes ou carnes, verifique se parece ser para uma Quermesse ou Cantina, e não consumo próprio.
3. MANUTENÇÃO: Igrejas são prédios antigos. Dê atenção a materiais de construção e reparos.
4. DATA LITÚRGICA: Se a data do documento for próxima de grandes festas (Páscoa, Natal, Padroeiro), mencione isso nas observações se relevante (ex: 'Compra de flores próxima à Páscoa').

TIPOS DE DOCUMENTO:
- NF-e: Nota Fiscal Eletrônica
- NFC-e: Nota Fiscal de Consumidor Eletrônica
- BOLETO: Boleto bancário
- RECIBO: Recibo simples
- FATURA_CARTAO: Fatura de cartão de crédito/débito
- CUPOM: Cupom fiscal
- OUTRO: Outros tipos de documentos

IMPORTANTE: Se o documento não for um documento fiscal brasileiro reconhecível, retorne tipo_documento como "OUTRO" e preencha os campos disponíveis com as informações que conseguir extrair.
PROMPT;
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
                'valor_total' => $this->parseMoney($data['financeiro']['valor_total'] ?? 0),
                'valor_principal' => $this->parseMoney(
                    $data['financeiro']['valor_principal'] ?? $data['financeiro']['valor_total'] ?? 0
                ),
                'forma_pagamento' => $data['financeiro']['forma_pagamento'] ?? null,
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
            ],
            'observacoes' => $data['observacoes'] ?? null,
            'itens' => [],
        ];

        // Normalizar itens
        if (isset($data['itens']) && is_array($data['itens'])) {
            foreach ($data['itens'] as $item) {
                $normalized['itens'][] = [
                    'descricao' => $item['descricao'] ?? null,
                    'quantidade' => $this->parseMoney($item['quantidade'] ?? 0),
                    'valor_unitario' => $this->parseMoney($item['valor_unitario'] ?? 0),
                    'categoria_sugerida' => $item['categoria_sugerida'] ?? null,
                ];
            }
        }

        return $normalized;
    }
}

