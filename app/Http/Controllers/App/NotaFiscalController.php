<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\NotaFiscalConta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use NFePHP\Common\Certificate\Pkcs12;
use NFePHP\Common\Certificate\Asn;
use Carbon\Carbon;

class NotaFiscalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Buscar o primeiro registro da tabela companies (matriz)
        $matriz = Company::orderBy('id', 'asc')->first();
        $cnpjMatriz = $matriz && $matriz->cnpj ? $this->formatarCNPJ($matriz->cnpj) : null;
        $cnpjMatrizRaw = $matriz && $matriz->cnpj ? preg_replace('/\D/', '', $matriz->cnpj) : null;

        // Buscar conta da Nota Fiscal ativa da empresa na sessão
        $companyId = session('active_company_id') ?? Auth::user()->company_id;
        $conta = NotaFiscalConta::where('company_id', $companyId)->first();

        $diasRestantes = null;
        $validade = null;
        $senhaDescriptografada = null;

        if ($conta) {
            if ($conta->certificado_validade) {
                $validade = Carbon::parse($conta->certificado_validade);
                $agora = Carbon::now();

                if ($validade->isFuture()) {
                    $now = Carbon::now();

                    // Total de dias restantes (cast para int para garantir número inteiro)
                    $days = (int) $now->diffInDays($validade);

                    // Horas restantes (módulo 24)
                    $hours = (int) $now->copy()->addDays($days)->diffInHours($validade);

                    // Minutos restantes (módulo 60)
                    $minutes = (int) $now->copy()->addDays($days)->addHours($hours)->diffInMinutes($validade);

                    $diasRestantes = "{$days}d {$hours}h {$minutes}m";
                } else {
                    $diasRestantes = "Expirado";
                }
            }

            try {
                $senhaDescriptografada = Crypt::decryptString($conta->certificado_senha);
            } catch (\Exception $e) {
                $senhaDescriptografada = 'Erro ao descriptografar';
            }
        }

        return view('app.notafiscal.index', [
            'cnpjMatriz' => $cnpjMatriz,
            'cnpjMatrizRaw' => $cnpjMatrizRaw,
            'conta' => $conta,
            'diasRestantes' => $diasRestantes,
            'senhaDescriptografada' => $senhaDescriptografada
        ]);
    }

    /**
     * Store a new NF-e account (CNPJ + Certificate A1)
     */
    public function storeConta(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cnpj' => 'required|string|size:14|regex:/^\d{14}$/',
            'cnpj_raw' => 'required|string|size:14|regex:/^\d{14}$/',
            'certificado_a1' => [
                'required',
                'file',
                'max:5120', // 5MB max
                function ($attribute, $value, $fail) {
                    if (!$value) {
                        return;
                    }

                    $extension = strtolower($value->getClientOriginalExtension());
                    $mimeType = $value->getMimeType();

                    // Verificar extensão
                    $allowedExtensions = ['pfx', 'p12'];
                    if (!in_array($extension, $allowedExtensions)) {
                        $fail('O certificado deve ser um arquivo .pfx ou .p12.');
                        return;
                    }

                    // Verificar MIME type (alguns navegadores podem enviar tipos diferentes)
                    $allowedMimeTypes = [
                        'application/x-pkcs12',
                        'application/pkcs12',
                        'application/pkcs-12',
                        'application/x-pkcs-12',
                        'application/octet-stream', // Alguns sistemas podem enviar como octet-stream
                    ];

                    // Se o MIME type não estiver na lista permitida, ainda aceitar se a extensão estiver correta
                    // (pois alguns sistemas podem não detectar corretamente o MIME type de arquivos .p12)
                    if (!in_array($mimeType, $allowedMimeTypes) && $mimeType !== 'application/octet-stream') {
                        // Log para debug, mas não falhar se a extensão estiver correta
                        \Log::info('MIME type não reconhecido para certificado', [
                            'extension' => $extension,
                            'mime_type' => $mimeType,
                            'filename' => $value->getClientOriginalName()
                        ]);
                    }
                }
            ],
            'senha_a1' => 'required|string|min:1',
        ], [
            'cnpj.required' => 'O CNPJ é obrigatório.',
            'cnpj.size' => 'O CNPJ deve conter 14 dígitos.',
            'cnpj.regex' => 'O CNPJ deve conter apenas números.',
            'cnpj_raw.required' => 'O CNPJ é obrigatório.',
            'cnpj_raw.size' => 'O CNPJ deve conter 14 dígitos.',
            'cnpj_raw.regex' => 'O CNPJ deve conter apenas números.',
            'certificado_a1.required' => 'O certificado A1 é obrigatório.',
            'certificado_a1.file' => 'O certificado deve ser um arquivo válido.',
            'certificado_a1.mimes' => 'O certificado deve ser um arquivo .pfx ou .p12.',
            'certificado_a1.max' => 'O certificado não pode ter mais de 5MB.',
            'senha_a1.required' => 'A senha do certificado é obrigatória.',
            'senha_a1.min' => 'A senha não pode estar vazia.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erro de validação.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            $companyId = session('active_company_id') ?? $user->company_id;

            if (!$companyId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhuma empresa selecionada.'
                ], 400);
            }

            $cnpj = $request->input('cnpj_raw') ?: $request->input('cnpj');
            $senhaA1 = $request->input('senha_a1');
            $certificadoFile = $request->file('certificado_a1');

            // 1. Validar CNPJ (sua função existente)
            if (!$this->validarCNPJ($cnpj)) {
                return response()->json(['success' => false, 'message' => 'CNPJ inválido.'], 422);
            }

            // Ler conteúdo do certificado antes de salvar
            $certificadoPath = $certificadoFile->getRealPath();
            $pfxContent = file_get_contents($certificadoPath);

            // Validar se o arquivo foi lido corretamente
            if (empty($pfxContent)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao ler o arquivo do certificado. Verifique se o arquivo está íntegro.'
                ], 422);
            }

            // Validar certificado - Suporte para OpenSSL 3.x e algoritmos legacy
            try {
                // Limpar espaços em branco da senha (pode haver espaços no início/fim)
                $senhaA1 = trim($senhaA1);

                // Limpar erros anteriores do OpenSSL
                while (openssl_error_string() !== false) {
                    // Limpar buffer de erros
                }

                // Primeiro, tentar validar com OpenSSL diretamente
                $cert = null;
                $pkey = null;
                $certData = [];
                $readResult = false;

                // TENTATIVA 1: Ler certificado PKCS12 com OpenSSL diretamente da memória
                Log::info('Tentativa 1: OpenSSL direto da memória');
                $readResult = @openssl_pkcs12_read($pfxContent, $certData, $senhaA1);

                // Capturar todos os erros do OpenSSL
                $opensslErrors = [];
                while (($error = openssl_error_string()) !== false) {
                    $opensslErrors[] = $error;
                }

                // TENTATIVA 2: Se falhar, tentar salvando em arquivo temporário
                // (alguns sistemas têm problemas ao ler da memória)
                if (!$readResult) {
                    Log::info('Tentativa 2: OpenSSL com arquivo temporário');

                    $tempFile = tempnam(sys_get_temp_dir(), 'cert_');
                    file_put_contents($tempFile, $pfxContent);

                    try {
                        $readResult = @openssl_pkcs12_read(file_get_contents($tempFile), $certData, $senhaA1);

                        // Capturar erros adicionais
                        while (($error = openssl_error_string()) !== false) {
                            $opensslErrors[] = $error;
                        }
                    } finally {
                        @unlink($tempFile);
                    }
                }

                if (!$readResult) {
                    // Se OpenSSL falhar, tentar com NFePHP
                    Log::info('Tentativa 3: NFePHP', [
                        'openssl_errors' => $opensslErrors,
                        'senha_length' => strlen($senhaA1),
                        'file_size' => strlen($pfxContent)
                    ]);

                    // Tentar com NFePHP
                    try {
                        $pkcs12 = new Pkcs12();
                        $pkcs12->loadPfx($pfxContent, $senhaA1, false, true, true);

                        if (empty($pkcs12->pubKey)) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Erro ao extrair a chave pública do certificado. Verifique se a senha está correta e se o arquivo não está corrompido.'
                            ], 422);
                        }

                        $certInfo = openssl_x509_parse($pkcs12->pubKey);
                        $pubKey = $pkcs12->pubKey;
                    } catch (\Exception $e) {
                        Log::error('Erro ao usar NFePHP', [
                            'error' => $e->getMessage(),
                            'error_class' => get_class($e),
                            'openssl_errors' => $opensslErrors
                        ]);

                        // Verificar se é erro de OpenSSL 3.x (algoritmos não suportados)
                        $isOpenSSL3Error = false;
                        $isPasswordError = false;

                        foreach ($opensslErrors as $error) {
                            // Erro específico do OpenSSL 3.x com algoritmos antigos
                            if (stripos($error, 'unsupported') !== false ||
                                stripos($error, 'digital envelope routines') !== false) {
                                $isOpenSSL3Error = true;
                                break;
                            }

                            // Erro de senha incorreta (apenas do OpenSSL, não da mensagem genérica do NFePHP)
                            if (stripos($error, 'mac verify failure') !== false ||
                                stripos($error, 'bad decrypt') !== false ||
                                stripos($error, 'wrong password') !== false) {
                                $isPasswordError = true;
                            }
                        }

                        // IMPORTANTE: Não verificar a mensagem do NFePHP pois ela diz "Senha errada ou arquivo corrompido"
                        // de forma genérica, mesmo quando é problema de algoritmo legacy
                        // Apenas confiar nos erros específicos do OpenSSL para detectar senha incorreta

                        // Se for erro de OpenSSL 3.x, tentar converter automaticamente
                        if ($isOpenSSL3Error && !$isPasswordError) {
                            Log::warning('Certificado Legacy detectado. Tentando converter automaticamente...', [
                                'cnpj' => $cnpj,
                                'openssl_errors' => $opensslErrors
                            ]);

                            // Tentar converter o certificado
                            $certificadoConvertido = $this->tentarConverterCertificadoLegacy($certificadoPath, $senhaA1);

                            if ($certificadoConvertido && file_exists($certificadoConvertido)) {
                                // Conversão bem-sucedida! Tentar ler o certificado convertido
                                try {
                                    $pfxContentConvertido = file_get_contents($certificadoConvertido);

                                    // Tentar com OpenSSL no certificado convertido
                                    $certDataConvertido = [];
                                    $readResultConvertido = @openssl_pkcs12_read($pfxContentConvertido, $certDataConvertido, $senhaA1);

                                    if ($readResultConvertido) {
                                        // Sucesso com OpenSSL!
                                        $cert = $certDataConvertido['cert'];
                                        $pkey = $certDataConvertido['pkey'];
                                        $certInfo = openssl_x509_parse($cert);
                                        $pubKey = $cert;

                                        // Atualizar variáveis para usar o certificado convertido
                                        $pfxContent = $pfxContentConvertido;
                                        $certificadoPath = $certificadoConvertido;

                                        Log::info('Certificado legacy convertido e validado com sucesso!', [
                                            'cnpj' => $cnpj
                                        ]);
                                    } else {
                                        // Tentar com NFePHP no certificado convertido
                                        $pkcs12Convertido = new Pkcs12();
                                        $pkcs12Convertido->loadPfx($pfxContentConvertido, $senhaA1, false, true, true);

                                        if (empty($pkcs12Convertido->pubKey)) {
                                            throw new \Exception('Falha ao ler certificado convertido');
                                        }

                                        $certInfo = openssl_x509_parse($pkcs12Convertido->pubKey);
                                        $pubKey = $pkcs12Convertido->pubKey;

                                        // Atualizar variáveis
                                        $pfxContent = $pfxContentConvertido;
                                        $certificadoPath = $certificadoConvertido;

                                        Log::info('Certificado legacy convertido e validado com NFePHP!', [
                                            'cnpj' => $cnpj
                                        ]);
                                    }
                                } catch (\Exception $e2) {
                                    Log::error('Falha ao ler certificado mesmo após conversão', [
                                        'error' => $e2->getMessage()
                                    ]);

                                    // Limpar arquivo convertido
                                    if (file_exists($certificadoConvertido)) {
                                        @unlink($certificadoConvertido);
                                    }

                                    return response()->json([
                                        'success' => false,
                                        'message' => 'O certificado foi convertido mas ainda não pôde ser validado. Por favor, solicite um certificado mais recente à Autoridade Certificadora.'
                                    ], 422);
                                }
                            } else {
                                // Conversão falhou
                                Log::error('Falha na conversão automática do certificado legacy');

                                return response()->json([
                                    'success' => false,
                                    'message' => 'O certificado utiliza algoritmos antigos não suportados e a conversão automática falhou. Por favor, solicite um certificado mais recente à Autoridade Certificadora ou entre em contato com o suporte técnico.'
                                ], 422);
                            }
                        } else {
                            // Não é erro de legacy ou é erro de senha
                            $errorMessage = 'Não foi possível validar o certificado. ';

                            if ($isPasswordError) {
                                $errorMessage .= 'A senha informada está incorreta. Verifique se não há espaços extras ou caracteres especiais.';
                            } else {
                                $errorMessage .= 'Verifique se o arquivo está íntegro e se a senha está correta.';
                            }

                            return response()->json([
                                'success' => false,
                                'message' => $errorMessage
                            ], 422);
                        }
                    }
                } else {
                    // OpenSSL conseguiu ler, usar os dados dele
                    $cert = $certData['cert'];
                    $pkey = $certData['pkey'];

                    // Parsear o certificado
                    $certInfo = openssl_x509_parse($cert);
                    $pubKey = $cert;
                }

                if (!$certInfo) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Erro ao processar informações do certificado.'
                    ], 422);
                }

                // Extrair CNPJ do certificado usando NFePHP Asn
                $cnpjCertificado = Asn::getCNPJCert($pubKey);

                if (empty($cnpjCertificado)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Não foi possível extrair o CNPJ do certificado. Verifique se o certificado é válido.'
                    ], 422);
                }

                // Limpar CNPJ do certificado (remover caracteres não numéricos)
                $cnpjCertificado = preg_replace('/\D/', '', $cnpjCertificado);

                // Extrair validade do certificado
                $validadeTimestamp = $certInfo['validTo_time_t'] ?? null;
                $validade = $validadeTimestamp ? date('Y-m-d', $validadeTimestamp) : null;

                // Verificar se o CNPJ do certificado corresponde ao CNPJ informado (primeiros 8 dígitos)
                if (strlen($cnpjCertificado) >= 8 && substr($cnpj, 0, 8) !== substr($cnpjCertificado, 0, 8)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'O CNPJ do certificado não corresponde ao CNPJ informado. Verifique se está usando o certificado correto.'
                    ], 422);
                }

                // Log de sucesso para debug
                Log::info('Certificado A1 validado com sucesso', [
                    'cnpj' => $cnpj,
                    'cnpj_certificado' => $cnpjCertificado,
                    'validade' => $validade,
                    'method' => $readResult ? 'OpenSSL' : 'NFePHP'
                ]);

            } catch (\NFePHP\Common\Exception\RuntimeException $e) {
                // Senha incorreta ou certificado corrompido
                Log::warning('Erro ao validar certificado A1', [
                    'error' => $e->getMessage(),
                    'error_class' => get_class($e),
                    'cnpj' => $cnpj,
                    'file_size' => strlen($pfxContent),
                    'file_path' => $certificadoPath,
                    'openssl_error' => openssl_error_string()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'A senha do certificado está incorreta ou o arquivo está corrompido. Verifique os dados informados.'
                ], 422);
            } catch (\NFePHP\Common\Exception\InvalidArgumentException $e) {
                // Erro de argumento inválido
                Log::warning('Erro ao processar certificado A1', [
                    'error' => $e->getMessage(),
                    'error_class' => get_class($e),
                    'cnpj' => $cnpj,
                    'openssl_error' => openssl_error_string()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao processar o certificado: ' . $e->getMessage()
                ], 422);
            } catch (\Exception $e) {
                // Qualquer outro erro
                Log::error('Erro inesperado ao validar certificado A1', [
                    'error' => $e->getMessage(),
                    'error_class' => get_class($e),
                    'trace' => $e->getTraceAsString(),
                    'cnpj' => $cnpj,
                    'openssl_error' => openssl_error_string()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Erro inesperado ao processar o certificado. Tente novamente ou entre em contato com o suporte.'
                ], 422);
            } catch (\Exception $e) {
                // Qualquer outro erro relacionado ao certificado
                Log::error('Erro inesperado ao validar certificado A1', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'cnpj' => $cnpj
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao validar o certificado: ' . $e->getMessage()
                ], 422);
            }

            // Se chegou aqui, o certificado é válido. Agora salvar no disco
            // Se foi convertido, usar o nome do arquivo original mas com conteúdo do convertido
            $fileName = time() . '_' . $cnpj . '_' . $certificadoFile->getClientOriginalName();

            // Se temos um certificado convertido, salvar ele ao invés do original
            if (isset($certificadoConvertido) && file_exists($certificadoConvertido)) {
                // Salvar o certificado convertido
                $pfxContentToSave = file_get_contents($certificadoConvertido);
                Storage::disk('public')->put('notafiscal/certificados/' . $fileName, $pfxContentToSave);
                $path = 'notafiscal/certificados/' . $fileName;

                // Limpar arquivo temporário convertido e original
                @unlink($certificadoConvertido);
                if (file_exists($certificadoPath) && $certificadoPath !== $certificadoConvertido) {
                    @unlink($certificadoPath);
                }

                Log::info('Certificado convertido salvo com sucesso', [
                    'arquivo' => $fileName,
                    'path' => $path
                ]);
            } else {
                // Salvar certificado original (não foi convertido)
                $path = $certificadoFile->storeAs('notafiscal/certificados', $fileName, 'public');
            }

            // Extrair validade e nome do certificado
            $validTo = null;
            $certName = 'Certificado Digital';

            // Re-ler o certificado para obter informações detalhadas
            $tempCertData = [];
            if (openssl_pkcs12_read($pfxContent, $tempCertData, $senhaA1)) {
                $certInfo = openssl_x509_parse($tempCertData['cert']);

                if ($certInfo && isset($certInfo['validTo_time_t'])) {
                    $validTo = date('Y-m-d H:i:s', $certInfo['validTo_time_t']);
                }

                // Tentar extrair o nome (CN ou O)
                if ($certInfo && isset($certInfo['subject'])) {
                    if (!empty($certInfo['subject']['CN'])) {
                        $certName = $certInfo['subject']['CN'];
                    } elseif (!empty($certInfo['subject']['O'])) {
                        $certName = $certInfo['subject']['O'];
                    }
                }
            } else {
                // Se falhar ao reler com OpenSSL, usar as informações já extraídas (validade e cnpj)
                // e manter o nome padrão ou tentar extrair de $certInfo se ainda disponível
                if (isset($certInfo) && $certInfo && isset($certInfo['validTo_time_t'])) {
                    $validTo = date('Y-m-d H:i:s', $certInfo['validTo_time_t']);
                }
                if (isset($certInfo) && $certInfo && isset($certInfo['subject'])) {
                    if (isset($certInfo['subject']['CN'])) {
                        $certName = $certInfo['subject']['CN'];
                    } elseif (isset($certInfo['subject']['O'])) {
                        $certName = $certInfo['subject']['O'];
                    }
                }
            }

            // Criptografar senha antes de salvar
            $senhaCriptografada = Crypt::encryptString($senhaA1);

            // Salvar informações no banco de dados
            $notaFiscalConta = NotaFiscalConta::updateOrCreate(
                ['company_id' => $companyId],
                [
                    'cnpj' => $cnpj,
                    'certificado_path' => $path, // Usar $path que já contém o caminho salvo
                    'certificado_senha' => $senhaCriptografada,
                    'certificado_validade' => $validTo,
                    'certificado_cnpj' => $cnpjCertificado,
                    'certificado_nome' => $certName,
                    'created_by' => $user->id,
                    'created_by_name' => $user->name,
                    'ativo' => true,
                ]
            );

            Log::info('Conta de Nota Fiscal salva com sucesso', ['company_id' => $companyId]);

            return response()->json([
                'success' => true,
                'message' => 'Conta da Nota Fiscal adicionada com sucesso!',
                'data' => [
                    'id' => $notaFiscalConta->id,
                    'cnpj' => $this->formatarCNPJ($cnpj),
                    'certificado_validade' => $validTo ? date('d/m/Y', strtotime($validTo)) : null,
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro ao adicionar conta NF-e', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar requisição: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tenta converter um certificado PKCS12 Legacy para formato moderno
     * usando OpenSSL CLI com provedor legacy
     *
     * @param string $caminhoArquivo Caminho completo do arquivo PFX/P12
     * @param string $senha Senha do certificado
     * @return string|false Retorna o caminho do arquivo convertido ou false em caso de falha
     */
    private function tentarConverterCertificadoLegacy($caminhoArquivo, $senha)
    {
        Log::info('Tentando converter certificado legacy', [
            'arquivo' => basename($caminhoArquivo),
            'tamanho' => file_exists($caminhoArquivo) ? filesize($caminhoArquivo) : 0
        ]);

        // Caminhos dos arquivos temporários
        // IMPORTANTE: Arquivos temporários de upload podem não ter extensão (.pfx ou .p12)
        // Por isso, não podemos apenas usar str_replace, devemos concatenar ou garantir o sufixo
        $pemTemporario = $caminhoArquivo . '.pem';
        $pfxModerno = $caminhoArquivo . '_moderno.pfx';

        try {
            // PASSO 1: Converter PFX Legacy → PEM
            // Usando flags -legacy e -provider para forçar leitura de algoritmos antigos
            Log::info('Convertendo Legacy PFX para PEM...');

            $cmdToPem = [
                'openssl', 'pkcs12',
                '-in', $caminhoArquivo,
                '-nodes', // Não criptografar a chave privada no PEM temporário
                '-passin', "pass:{$senha}",
                '-legacy', // Flag para OpenSSL 3.x ler arquivos com algoritmos antigos
                '-provider', 'default',
                '-provider', 'legacy',
                '-out', $pemTemporario
            ];

            $resultPem = Process::run($cmdToPem);

            if ($resultPem->failed()) {
                Log::error('Falha ao converter Legacy para PEM', [
                    'erro' => $resultPem->errorOutput(),
                    'output' => $resultPem->output()
                ]);
                return false;
            }

            if (!file_exists($pemTemporario) || filesize($pemTemporario) === 0) {
                Log::error('Arquivo PEM temporário foi criado mas está vazio ou não existe');
                return false;
            }

            Log::info('PEM temporário criado com sucesso', [
                'tamanho' => filesize($pemTemporario)
            ]);

            // PASSO 2: Converter PEM → PFX Moderno com AES-256
            Log::info('Convertendo PEM para PFX moderno...');

            // O arquivo PEM contém tanto o certificado quanto a chave privada
            // Precisamos especificar que estamos usando o mesmo arquivo para ambos
            $cmdToPfx = [
                'openssl', 'pkcs12',
                '-export',
                '-in', $pemTemporario,
                '-inkey', $pemTemporario,  // A chave privada também está no PEM
                '-out', $pfxModerno,
                '-passout', "pass:{$senha}",
                '-keypbe', 'AES-256-CBC', // Algoritmo moderno para a chave privada
                '-certpbe', 'AES-256-CBC', // Algoritmo moderno para o certificado
                '-macalg', 'SHA256' // Algoritmo de MAC moderno
            ];

            // Debug do comando
            Log::info('Executando comando PFX:', [
                'comando' => implode(' ', array_map(function($arg) use ($senha) {
                    return str_replace($senha, '***', $arg);
                }, $cmdToPfx))
            ]);

            $resultPfx = Process::run($cmdToPfx);

            Log::info('Resultado OpenSSL Export:', [
                'exit_code' => $resultPfx->exitCode(),
                'output' => $resultPfx->output(),
                'error_output' => $resultPfx->errorOutput()
            ]);

            if ($resultPfx->failed()) {
                Log::error('Falha ao gerar PFX Moderno (Exit Code != 0)', [
                    'erro' => $resultPfx->errorOutput(),
                    'output' => $resultPfx->output()
                ]);

                // Limpar o PEM mesmo em caso de falha
                if (file_exists($pemTemporario)) {
                    @unlink($pemTemporario);
                }

                return false;
            }

            // Limpar o PEM temporário (CRÍTICO: contém chave privada não criptografada!)
            if (file_exists($pemTemporario)) {
                @unlink($pemTemporario);
                Log::info('Arquivo PEM temporário removido');
            }

            // Verificar se o arquivo convertido foi criado com sucesso
            if (!file_exists($pfxModerno) || filesize($pfxModerno) === 0) {
                Log::error('Arquivo PFX moderno não foi criado ou está vazio');
                return false;
            }

            Log::info('Certificado convertido com sucesso', [
                'arquivo_original' => basename($caminhoArquivo),
                'arquivo_convertido' => basename($pfxModerno),
                'tamanho_convertido' => filesize($pfxModerno)
            ]);

            return $pfxModerno;

        } catch (\Exception $e) {
            Log::error('Erro crítico na conversão do certificado', [
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Limpar arquivos temporários em caso de erro
            if (file_exists($pemTemporario)) {
                @unlink($pemTemporario);
            }
            if (file_exists($pfxModerno)) {
                @unlink($pfxModerno);
            }

            return false;
        }
    }

    /**
     * Valida CNPJ
     */
    private function validarCNPJ($cnpj)
    {
        $cnpj = preg_replace('/\D/', '', $cnpj);

        if (strlen($cnpj) != 14) {
            return false;
        }

        // Elimina CNPJs conhecidos como inválidos
        if (preg_match('/(\d)\1{13}/', $cnpj)) {
            return false;
        }

        // Validação dos dígitos verificadores
        $length = strlen($cnpj) - 2;
        $numbers = substr($cnpj, 0, $length);
        $digits = substr($cnpj, $length);
        $sum = 0;
        $pos = $length - 7;

        for ($i = $length; $i >= 1; $i--) {
            $sum += $numbers[$length - $i] * $pos--;
            if ($pos < 2) {
                $pos = 9;
            }
        }

        $result = $sum % 11 < 2 ? 0 : 11 - $sum % 11;
        if ($result != $digits[0]) {
            return false;
        }

        $length = $length + 1;
        $numbers = substr($cnpj, 0, $length);
        $sum = 0;
        $pos = $length - 7;

        for ($i = $length; $i >= 1; $i--) {
            $sum += $numbers[$length - $i] * $pos--;
            if ($pos < 2) {
                $pos = 9;
            }
        }

        $result = $sum % 11 < 2 ? 0 : 11 - $sum % 11;
        if ($result != $digits[1]) {
            return false;
        }

        return true;
    }

    /**
     * Formata CNPJ
     */
    private function formatarCNPJ($cnpj)
    {
        $cnpj = preg_replace('/\D/', '', $cnpj);
        return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $cnpj);
    }
}
