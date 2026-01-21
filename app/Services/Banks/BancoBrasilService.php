<?php

namespace App\Services\Banks;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\BankConfig;
use Exception;

class BancoBrasilService
{
    protected $config;
    protected $authUrl;
    protected $baseUrl;

    // Recebe o Model BankConfig (já descriptografado pelo Eloquent se acessado via propriedade)
    public function __construct(BankConfig $config)
    {
        $this->config = $config;

        if ($config->ambiente === 'producao') {
            $this->authUrl = 'https://oauth.bb.com.br/oauth/token';
            $this->baseUrl = 'https://api.bb.com.br/cobrancas/v2';
        } else {
            $this->authUrl = 'https://oauth.hm.bb.com.br/oauth/token';
            $this->baseUrl = 'https://api.hm.bb.com.br/cobrancas/v2';
        }
    }

    // Método público para o botão "Testar Conexão"
    public function testarAutenticacao()
    {
        // Força a geração de um token novo sem cache para validar credenciais
        return $this->requestToken();
    }

    protected function requestToken()
    {
        // O Laravel já entrega descriptografado se você configurou o cast 'encrypted' no Model
        $clientId = $this->config->client_id;
        $clientSecret = $this->config->client_secret;

        \Log::info('BB OAuth - Solicitando Token', [
            'client_id_preview' => substr($clientId, 0, 10) . '...',
            'auth_url' => $this->authUrl,
            'ambiente' => $this->config->ambiente
        ]);

        // Basic Auth Base64
        $basicAuth = base64_encode("{$clientId}:{$clientSecret}");

        $headers = [
            'Authorization' => "Basic {$basicAuth}",
            'Content-Type'  => 'application/x-www-form-urlencoded',
        ];

        // Adiciona header MCI TESTE se estiver em homologação (conforme documentação BB)
        // Em homologação: x-br-com-bb-ipa-mciteste
        // Em produção: x-br-com-bb-ipa-mci
        if ($this->config->ambiente === 'homologacao' && !empty($this->config->mci_teste)) {
            $headers['x-br-com-bb-ipa-mciteste'] = $this->config->mci_teste;
        }

        $response = Http::withHeaders($headers)
          ->withoutVerifying()
          ->asForm()
          ->post($this->authUrl, [
            'grant_type' => 'client_credentials'
            // Escopo removido - o BB vai conceder todos os escopos autorizados para a aplicação
        ]);

        \Log::info('BB OAuth - Resposta', [
            'status' => $response->status(),
            'body_preview' => substr($response->body(), 0, 200)
        ]);

        if ($response->failed()) {
            throw new Exception("BB rejeitou credenciais: " . $response->body());
        }

        return $response->json()['access_token'];
    }

    // Método inteligente com Cache para usar no dia a dia
    public function getToken()
    {
        // Cache Key única por Tenant e AppKey
        $cacheKey = 'bb_token_' . md5($this->config->client_id);

        try {
            return Cache::remember($cacheKey, 540, function () {
                return $this->requestToken();
            });
        } catch (\Exception $e) {
            // Fallback: Se o cache falhar (ex: erro de tags no driver file), solicita token direto
            return $this->requestToken();
        }
    }

    /**
     * Testa a API de Extrato do BB
     * Faz uma chamada real à API para validar se as credenciais e configurações estão corretas
     *
     * @return array Retorna dados do teste incluindo sucesso/erro e informações da conta
     * @throws Exception
     */
    public function testarApiExtrato()
    {
        try {
            // Validar se agência e conta estão configuradas
            if (empty($this->config->agencia) || empty($this->config->conta_corrente)) {
                throw new Exception("Agência e Conta Corrente são obrigatórias para consultar extrato");
            }

            // Buscar extrato dos últimos 7 dias como teste
            // Formato DDMMAAAA (8 dígitos) conforme documentação BB
            $dataInicio = now()->subDays(7)->format('dmY');
            $dataFim = now()->format('dmY');

            $resultado = $this->getExtrato($dataInicio, $dataFim);

            return [
                'success' => true,
                'message' => 'API de Extrato funcionando corretamente!',
                'agencia' => $this->config->agencia,
                'conta' => $this->config->conta_corrente,
                'periodo' => "{$dataInicio} a {$dataFim}",
                'total_lancamentos' => count($resultado['lancamentos'] ?? []),
                'saldo_inicial' => $resultado['saldoInicial'] ?? null,
                'saldo_final' => $resultado['saldoFinal'] ?? null,
            ];

        } catch (Exception $e) {
            throw new Exception("Erro ao testar API de Extrato: " . $e->getMessage());
        }
    }

    /**
     * Busca extrato bancário do BB
     *
     * @param string $dataInicio Data inicial no formato dd.mm.yyyy
     * @param string $dataFim Data final no formato dd.mm.yyyy
     * @param int $indicadorLancamento 1=Débito, 2=Crédito, 3=Todos (default)
     * @return array Dados do extrato
     * @throws Exception
     */
    public function getExtrato(string $dataInicio, string $dataFim, int $indicadorLancamento = 3)
    {
        // Validações
        if (empty($this->config->agencia) || empty($this->config->conta_corrente)) {
            throw new Exception("Agência e Conta Corrente não configuradas");
        }

        // Obter token de autenticação
        $token = $this->getToken();

        \Log::info('BB Extrato - Token obtido', [
            'token_preview' => substr($token, 0, 20) . '...',
            'ambiente' => $this->config->ambiente
        ]);

        // Preparar headers
        $headers = [
            'Authorization' => "Bearer {$token}",
            'Content-Type' => 'application/json',
        ];

        // Developer Application Key removida do Header para a API de Extratos (agora vai na Query String)
        // if (!empty($this->config->developer_app_key)) {
        //    $headers['Developer-Application-Key'] = $this->config->developer_app_key;
        // }

        // Adiciona header MCI TESTE se estiver em homologação (conforme documentação BB)
        if ($this->config->ambiente === 'homologacao' && !empty($this->config->mci_teste)) {
            $headers['x-br-com-bb-ipa-mciteste'] = $this->config->mci_teste;
        }

        // Montar URL da API de Extrato
        $extratoUrl = $this->getExtratoUrl();

        // Parâmetros da requisição
        $params = [
            'gw-dev-app-key' => $this->config->developer_app_key, // App Key via Query Param
            'dataInicioSolicitacao' => $dataInicio,
            'dataFimSolicitacao' => $dataFim,
            'indicadorLancamento' => $indicadorLancamento,
        ];

        \Log::info('BB Extrato - Requisição', [
            'url' => $extratoUrl,
            'params' => $params,
            'headers' => array_merge($headers, ['Authorization' => 'Bearer ***']),
            'agencia_original' => $this->config->agencia,
            'conta_original' => $this->config->conta_corrente
        ]);

        // Fazer requisição com retry (ambiente de homologação do BB é instável)
        $maxRetries = 3;
        $retryDelay = 2; // segundos
        $lastException = null;

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                $response = Http::withHeaders($headers)
                    ->withoutVerifying()
                    ->timeout(30)
                    ->get($extratoUrl, $params);

                \Log::info('BB Extrato - Resposta', [
                    'attempt' => $attempt,
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'headers' => $response->headers()
                ]);

                // Se for sucesso, retorna
                if ($response->successful()) {
                    if ($attempt > 1) {
                        \Log::info('BB Extrato - Sucesso após retry', ['attempts' => $attempt]);
                    }
                    return $response->json();
                }

                // Se for erro 500 e ainda temos tentativas, tenta novamente
                if ($response->status() >= 500 && $attempt < $maxRetries) {
                    \Log::warning('BB Extrato - Erro 500, tentando novamente', [
                        'attempt' => $attempt,
                        'max_retries' => $maxRetries,
                        'retry_delay' => $retryDelay
                    ]);
                    sleep($retryDelay);
                    continue;
                }

                // Outros erros ou última tentativa
                $errorBody = $response->body();
                $statusCode = $response->status();
                throw new Exception("Erro ao buscar extrato (HTTP {$statusCode}): {$errorBody}");

            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                $lastException = $e;
                if ($attempt < $maxRetries) {
                    \Log::warning('BB Extrato - Erro de conexão, tentando novamente', [
                        'attempt' => $attempt,
                        'error' => $e->getMessage()
                    ]);
                    sleep($retryDelay);
                    continue;
                }
            }
        }

        // Se chegou aqui, todas as tentativas falharam
        throw $lastException ?? new Exception("Erro ao buscar extrato após {$maxRetries} tentativas");
    }

    /**
     * Retorna a URL base da API de Extrato conforme ambiente
     *
     * @return string URL completa para consulta de extrato
     */
    protected function getExtratoUrl()
    {
        $agencia = ltrim($this->config->agencia, '0');
        $conta = ltrim($this->config->conta_corrente, '0');

        \Log::info('BB Extrato - Montando URL', [
            'agencia_original' => $this->config->agencia,
            'agencia_trimmed' => $agencia,
            'conta_original' => $this->config->conta_corrente,
            'conta_trimmed' => $conta,
            'ambiente' => $this->config->ambiente
        ]);

        if ($this->config->ambiente === 'producao') {
            $url = "https://api.bb.com.br/extratos/v1/conta-corrente/agencia/{$agencia}/conta/{$conta}";
        } else {
            $url = "https://api.hm.bb.com.br/extratos/v1/conta-corrente/agencia/{$agencia}/conta/{$conta}";
        }

        \Log::info('BB Extrato - URL Final', ['url' => $url]);

        return $url;
    }

    /**
     * Busca saldo da conta corrente
     *
     * @return array Dados do saldo
     * @throws Exception
     */
    public function getSaldo()
    {
        // Validações
        if (empty($this->config->agencia) || empty($this->config->conta_corrente)) {
            throw new Exception("Agência e Conta Corrente não configuradas");
        }

        // Obter token de autenticação
        $token = $this->getToken();

        // Preparar headers
        $headers = [
            'Authorization' => "Bearer {$token}",
            'Content-Type' => 'application/json',
        ];

        // Adiciona Developer Application Key
        if (!empty($this->config->developer_app_key)) {
            $headers['Developer-Application-Key'] = $this->config->developer_app_key;
        }

        // Adiciona header MCI TESTE se estiver em homologação (conforme documentação BB)
        if ($this->config->ambiente === 'homologacao' && !empty($this->config->mci_teste)) {
            $headers['x-br-com-bb-ipa-mciteste'] = $this->config->mci_teste;
        }

        // Montar URL
        $agencia = $this->config->agencia;
        $conta = $this->config->conta_corrente;

        if ($this->config->ambiente === 'producao') {
            $saldoUrl = "https://api.bb.com.br/extratos/v1/conta-corrente/{$agencia}/{$conta}/saldo";
        } else {
            $saldoUrl = "https://api.hm.bb.com.br/extratos/v1/conta-corrente/{$agencia}/{$conta}/saldo";
        }

        // Fazer requisição
        $response = Http::withHeaders($headers)
            ->withoutVerifying()
            ->get($saldoUrl);

        if ($response->failed()) {
            $errorBody = $response->body();
            $statusCode = $response->status();
            throw new Exception("Erro ao buscar saldo (HTTP {$statusCode}): {$errorBody}");
        }

        return $response->json();
    }
}
