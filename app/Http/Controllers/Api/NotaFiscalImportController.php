<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EntidadeFinanceira;
use App\Models\Financeiro\TransacaoFinanceira;
use App\Models\Movimentacao;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;
use Carbon\Carbon;

class NotaFiscalImportController extends Controller
{
    /**
     * Importa nota fiscal a partir da URL do QR Code
     */
    public function import(Request $request)
    {
        try {
            // Validação
            $validated = $request->validate([
                'url' => 'required|string',
            ]);

            $url = $validated['url'];

            // Validar se é uma URL válida da SEFAZ (aceita caracteres especiais como pipe)
            if (!filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED) &&
                !preg_match('/^https?:\/\/.+/', $url)) {
                return response()->json([
                    'success' => false,
                    'message' => 'URL inválida. Certifique-se de que o QR Code contém uma URL válida da SEFAZ.'
                ], 400);
            }

            // Obter usuário autenticado e empresa
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não autenticado'
                ], 401);
            }

            // Obter company_id - tentar da sessão primeiro, depois da primeira empresa do usuário
            $companyId = session('active_company_id');
            if (!$companyId) {
                $firstCompany = $user->companies()->first();
                if (!$firstCompany) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Nenhuma empresa associada ao usuário'
                    ], 400);
                }
                $companyId = $firstCompany->id;
            }

            // Fazer scraping da SEFAZ
            $notaData = $this->scrapeSefaz($url);

            if (!$notaData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não foi possível extrair dados da nota fiscal. Verifique se a URL é válida.'
                ], 400);
            }

            // Buscar ou criar entidade financeira padrão (Caixa)
            $entidadeFinanceira = EntidadeFinanceira::where('company_id', $companyId)
                ->where('tipo', 'caixa')
                ->first();

            if (!$entidadeFinanceira) {
                // Criar entidade financeira padrão se não existir
                $entidadeFinanceira = EntidadeFinanceira::create([
                    'nome' => 'Caixa Principal',
                    'tipo' => 'caixa',
                    'company_id' => $companyId,
                    'saldo_inicial' => 0,
                    'saldo_atual' => 0,
                    'created_by' => $user->id,
                    'created_by_name' => $user->name,
                    'updated_by' => $user->id,
                    'updated_by_name' => $user->name,
                ]);
            }

            // Preparar dados para movimentação
            $dataEmissao = isset($notaData['data_emissao'])
                ? Carbon::createFromFormat('d/m/Y', $notaData['data_emissao'])->format('Y-m-d')
                : now()->format('Y-m-d');

            $valor = $this->parseValor($notaData['valor_total'] ?? '0');
            $tipo = 'entrada'; // Nota fiscal geralmente é entrada (compra)

            // Criar descrição da movimentação
            $descricao = sprintf(
                'Nota Fiscal %s - %s',
                $notaData['numero'] ?? 'N/A',
                $notaData['emitente_nome'] ?? 'Emitente não identificado'
            );

            // Criar movimentação
            $movimentacao = Movimentacao::create([
                'entidade_id' => $entidadeFinanceira->id,
                'tipo' => $tipo,
                'valor' => $valor,
                'data' => $dataEmissao,
                'data_competencia' => $dataEmissao,
                'descricao' => $descricao,
                'company_id' => $companyId,
                'created_by' => $user->id,
                'created_by_name' => $user->name,
                'updated_by' => $user->id,
                'updated_by_name' => $user->name,
            ]);

            // Criar transação financeira
            $transacao = TransacaoFinanceira::create([
                'company_id' => $companyId,
                'data_competencia' => $dataEmissao,
                'entidade_id' => $entidadeFinanceira->id,
                'tipo' => $tipo,
                'valor' => $valor,
                'descricao' => $descricao,
                'movimentacao_id' => $movimentacao->id,
                'tipo_documento' => 'Nota Fiscal',
                'numero_documento' => $notaData['chave_acesso'] ?? $notaData['numero'] ?? null,
                'origem' => 'Caixa',
                'comprovacao_fiscal' => true,
                'created_by' => $user->id,
                'created_by_name' => $user->name,
                'updated_by' => $user->id,
                'updated_by_name' => $user->name,
            ]);

            // Mensagem de sucesso
            $mensagem = sprintf(
                'Sucesso! Lançamento de R$ %s da %s registrado.',
                number_format($valor, 2, ',', '.'),
                $notaData['emitente_nome'] ?? 'Emitente não identificado'
            );

            return response()->json([
                'success' => true,
                'message' => $mensagem,
                'data' => [
                    'movimentacao_id' => $movimentacao->id,
                    'transacao_id' => $transacao->id,
                    'valor' => $valor,
                    'emitente' => $notaData['emitente_nome'] ?? null,
                    'numero' => $notaData['numero'] ?? null,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao importar nota fiscal', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'url' => $request->input('url')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar nota fiscal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Faz scraping da página da SEFAZ
     */
    private function scrapeSefaz(string $url): ?array
    {
        try {
            // Fazer requisição HTTP para a URL da SEFAZ
            $response = Http::timeout(30)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                    'Accept-Language' => 'pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
                ])
                ->get($url);

            if (!$response->successful()) {
                Log::warning('Erro ao acessar URL da SEFAZ', [
                    'url' => $url,
                    'status' => $response->status()
                ]);
                return null;
            }

            $html = $response->body();

            // Usar DomCrawler para extrair dados
            $crawler = new Crawler($html);

            $data = [];

            // Extrair dados principais da nota fiscal
            // Nota: Os seletores podem variar dependendo do estado e formato da página da SEFAZ
            // Você precisará ajustar os seletores CSS baseado na estrutura real da página

            // Exemplo de extração (ajuste conforme necessário):
            // Número da nota
            $data['numero'] = $this->extractText($crawler, '#numeroNFe, .numero-nfe, [id*="numero"]');

            // Chave de acesso
            $data['chave_acesso'] = $this->extractText($crawler, '#chaveAcesso, .chave-acesso, [id*="chave"]');

            // Data de emissão
            $data['data_emissao'] = $this->extractText($crawler, '#dataEmissao, .data-emissao, [id*="data"]');

            // Valor total
            $data['valor_total'] = $this->extractText($crawler, '#valorTotal, .valor-total, [id*="valor"], [class*="valor"]');

            // Nome do emitente
            $data['emitente_nome'] = $this->extractText($crawler, '#emitenteNome, .emitente-nome, [id*="emitente"], [class*="emitente"]');

            // CNPJ do emitente
            $data['emitente_cnpj'] = $this->extractText($crawler, '#emitenteCNPJ, .emitente-cnpj, [id*="cnpj"]');

            // Descrição dos produtos (opcional)
            $data['produtos'] = $this->extractProdutos($crawler);

            // Se não conseguiu extrair dados essenciais, tentar método alternativo
            if (empty($data['valor_total']) && empty($data['numero'])) {
                // Tentar extrair de tabelas ou outros formatos
                $data = $this->extractFromTables($crawler, $data);
            }

            return $data;

        } catch (\Exception $e) {
            Log::error('Erro no scraping da SEFAZ', [
                'url' => $url,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Extrai texto de um elemento usando múltiplos seletores
     */
    private function extractText(Crawler $crawler, string $selectors): ?string
    {
        $selectorList = explode(',', $selectors);

        foreach ($selectorList as $selector) {
            $selector = trim($selector);
            try {
                $element = $crawler->filter($selector)->first();
                if ($element->count() > 0) {
                    $text = $element->text();
                    if (!empty(trim($text))) {
                        return trim($text);
                    }
                }
            } catch (\Exception $e) {
                // Continuar tentando outros seletores
                continue;
            }
        }

        return null;
    }

    /**
     * Extrai produtos da nota fiscal
     */
    private function extractProdutos(Crawler $crawler): array
    {
        $produtos = [];

        try {
            // Tentar encontrar tabela de produtos
            $crawler->filter('table tr')->each(function (Crawler $row) use (&$produtos) {
                $cells = $row->filter('td');
                if ($cells->count() >= 3) {
                    $produtos[] = [
                        'descricao' => $cells->eq(0)->text(),
                        'quantidade' => $cells->eq(1)->text(),
                        'valor' => $cells->eq(2)->text(),
                    ];
                }
            });
        } catch (\Exception $e) {
            // Ignorar erros
        }

        return $produtos;
    }

    /**
     * Extrai dados de tabelas HTML (método alternativo)
     */
    private function extractFromTables(Crawler $crawler, array $data): array
    {
        try {
            // Procurar por tabelas e tentar extrair dados
            $crawler->filter('table')->each(function (Crawler $table) use (&$data) {
                $rows = $table->filter('tr');

                $rows->each(function (Crawler $row) use (&$data) {
                    $cells = $row->filter('td, th');
                    if ($cells->count() >= 2) {
                        $label = trim($cells->eq(0)->text());
                        $value = trim($cells->eq(1)->text());

                        // Mapear labels comuns
                        if (stripos($label, 'valor') !== false && stripos($label, 'total') !== false) {
                            $data['valor_total'] = $value;
                        }
                        if (stripos($label, 'número') !== false || stripos($label, 'nota') !== false) {
                            $data['numero'] = $value;
                        }
                        if (stripos($label, 'data') !== false && stripos($label, 'emissão') !== false) {
                            $data['data_emissao'] = $value;
                        }
                        if (stripos($label, 'emitente') !== false || stripos($label, 'razao') !== false) {
                            $data['emitente_nome'] = $value;
                        }
                        if (stripos($label, 'cnpj') !== false) {
                            $data['emitente_cnpj'] = $value;
                        }
                    }
                });
            });
        } catch (\Exception $e) {
            // Ignorar erros
        }

        return $data;
    }

    /**
     * Converte string de valor para float
     */
    private function parseValor(?string $valor): float
    {
        if (!$valor) {
            return 0.0;
        }

        // Remove espaços e caracteres não numéricos exceto vírgula e ponto
        $valor = preg_replace('/[^\d,.-]/', '', $valor);

        // Substitui vírgula por ponto se necessário
        $valor = str_replace(',', '.', str_replace('.', '', $valor));

        return (float) $valor;
    }
}

