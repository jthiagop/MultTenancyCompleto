<?php

namespace App\Services;

use App\Models\Financeiro\BankStatement;
use App\Models\Financeiro\TransacaoFinanceira;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Serviço de Matching Inteligente para Conciliação Bancária
 * 
 * Utiliza um sistema de pontuação (score) para ranquear as sugestões
 * de conciliação, considerando múltiplos critérios com pesos diferentes.
 */
class ConciliacaoMatchingService
{
    /**
     * Pesos para cada critério de matching
     */
    private const PESOS = [
        'valor_exato' => 40,           // Valor idêntico
        'valor_proximo' => 25,         // Valor com tolerância de 2%
        'data_mesmo_dia' => 25,        // Mesma data
        'data_proxima' => 15,          // Data próxima (até 7 dias)
        'data_mesmo_mes' => 8,         // Mesmo mês
        'numero_documento' => 20,      // Número do documento igual
        'descricao_similar' => 15,     // Descrição similar
    ];

    /**
     * Tolerância de valor (percentual)
     */
    private const TOLERANCIA_VALOR_PERCENTUAL = 0.02; // 2%
    
    /**
     * Tolerância de valor mínima (em reais)
     */
    private const TOLERANCIA_VALOR_MINIMA = 0.10; // R$ 0,10

    /**
     * Busca possíveis transações para conciliação com score de confiança
     *
     * @param BankStatement $bankStatement
     * @param int $entidadeId
     * @param int $limite
     * @return Collection
     */
    public function buscarPossiveisTransacoes(BankStatement $bankStatement, int $entidadeId, int $limite = 5): Collection
    {
        $valorExtrato = abs($bankStatement->amount);
        $tipo = $bankStatement->amount < 0 ? 'saida' : 'entrada';
        $dataExtrato = Carbon::parse($bankStatement->dtposted);
        $numeroDocumento = $bankStatement->checknum;
        $memoExtrato = Str::upper($bankStatement->memo ?? '');

        // Calcula tolerância de valor (maior entre % e mínimo absoluto)
        $toleranciaPercentual = $valorExtrato * self::TOLERANCIA_VALOR_PERCENTUAL;
        $tolerancia = max($toleranciaPercentual, self::TOLERANCIA_VALOR_MINIMA);
        
        $valorMin = $valorExtrato - $tolerancia;
        $valorMax = $valorExtrato + $tolerancia;

        // Janela de busca: -30 dias a +15 dias (mais realista que ±2 meses)
        $dataInicio = $dataExtrato->copy()->subDays(30)->startOfDay();
        $dataFim = $dataExtrato->copy()->addDays(15)->endOfDay();

        // Busca candidatos com critérios mais flexíveis
        $candidatos = TransacaoFinanceira::forActiveCompany()
            ->where('entidade_id', $entidadeId)
            ->where('tipo', $tipo)
            ->whereBetween('valor', [$valorMin, $valorMax])
            ->whereBetween('data_competencia', [$dataInicio, $dataFim])
            ->whereNull('bank_statement_id') // Não conciliadas
            ->get();

        // Calcula score para cada candidato
        $candidatosComScore = $candidatos->map(function ($transacao) use ($valorExtrato, $dataExtrato, $numeroDocumento, $memoExtrato) {
            $score = $this->calcularScore($transacao, $valorExtrato, $dataExtrato, $numeroDocumento, $memoExtrato);
            $transacao->match_score = $score['total'];
            $transacao->match_detalhes = $score['detalhes'];
            return $transacao;
        });

        // Ordena por score (maior primeiro) e limita resultados
        return $candidatosComScore
            ->sortByDesc('match_score')
            ->take($limite)
            ->values();
    }

    /**
     * Calcula o score de matching para uma transação
     *
     * @param TransacaoFinanceira $transacao
     * @param float $valorExtrato
     * @param Carbon $dataExtrato
     * @param string|null $numeroDocumento
     * @param string $memoExtrato
     * @return array
     */
    private function calcularScore(
        $transacao,
        float $valorExtrato,
        Carbon $dataExtrato,
        ?string $numeroDocumento,
        string $memoExtrato
    ): array {
        $score = 0;
        $detalhes = [];

        // 1. VALOR
        $diferencaValor = abs($transacao->valor - $valorExtrato);
        $tolerancia = max($valorExtrato * self::TOLERANCIA_VALOR_PERCENTUAL, self::TOLERANCIA_VALOR_MINIMA);
        
        if ($diferencaValor == 0) {
            $score += self::PESOS['valor_exato'];
            $detalhes['valor'] = ['pontos' => self::PESOS['valor_exato'], 'motivo' => 'Valor exato'];
        } elseif ($diferencaValor <= $tolerancia) {
            // Pontuação proporcional à proximidade
            $proporcao = 1 - ($diferencaValor / $tolerancia);
            $pontosValor = (int) round(self::PESOS['valor_proximo'] * $proporcao);
            $score += $pontosValor;
            $detalhes['valor'] = ['pontos' => $pontosValor, 'motivo' => 'Valor próximo (diferença: R$ ' . number_format($diferencaValor, 2, ',', '.') . ')'];
        }

        // 2. DATA
        $dataTransacao = Carbon::parse($transacao->data_competencia);
        $diferencaDias = abs($dataTransacao->diffInDays($dataExtrato));
        
        if ($diferencaDias == 0) {
            $score += self::PESOS['data_mesmo_dia'];
            $detalhes['data'] = ['pontos' => self::PESOS['data_mesmo_dia'], 'motivo' => 'Mesma data'];
        } elseif ($diferencaDias <= 7) {
            // Pontuação decrescente por dia de diferença
            $pontosData = (int) round(self::PESOS['data_proxima'] * (1 - ($diferencaDias / 7)));
            $score += $pontosData;
            $detalhes['data'] = ['pontos' => $pontosData, 'motivo' => "Data próxima ({$diferencaDias} dias)"];
        } elseif ($dataTransacao->format('Y-m') === $dataExtrato->format('Y-m')) {
            $score += self::PESOS['data_mesmo_mes'];
            $detalhes['data'] = ['pontos' => self::PESOS['data_mesmo_mes'], 'motivo' => 'Mesmo mês'];
        }

        // 3. NÚMERO DO DOCUMENTO
        if ($numeroDocumento && $transacao->numero_documento) {
            $numDocTransacao = Str::upper(trim($transacao->numero_documento));
            $numDocExtrato = Str::upper(trim($numeroDocumento));
            
            if ($numDocTransacao === $numDocExtrato) {
                $score += self::PESOS['numero_documento'];
                $detalhes['documento'] = ['pontos' => self::PESOS['numero_documento'], 'motivo' => 'Número do documento idêntico'];
            } elseif (Str::contains($numDocTransacao, $numDocExtrato) || Str::contains($numDocExtrato, $numDocTransacao)) {
                $pontosDoc = (int) round(self::PESOS['numero_documento'] * 0.5);
                $score += $pontosDoc;
                $detalhes['documento'] = ['pontos' => $pontosDoc, 'motivo' => 'Número do documento parcialmente igual'];
            }
        }

        // 4. SIMILARIDADE DE DESCRIÇÃO
        if ($memoExtrato && $transacao->descricao) {
            $descricaoTransacao = Str::upper($transacao->descricao);
            $similaridade = $this->calcularSimilaridade($memoExtrato, $descricaoTransacao);
            
            if ($similaridade > 0.3) {
                $pontosDescricao = (int) round(self::PESOS['descricao_similar'] * $similaridade);
                $score += $pontosDescricao;
                $detalhes['descricao'] = ['pontos' => $pontosDescricao, 'motivo' => 'Descrição similar (' . round($similaridade * 100) . '%)'];
            }
        }

        // Normaliza score para 0-100
        $maxPossivel = self::PESOS['valor_exato'] + self::PESOS['data_mesmo_dia'] + self::PESOS['numero_documento'] + self::PESOS['descricao_similar'];
        $scoreNormalizado = min(100, (int) round(($score / $maxPossivel) * 100));

        return [
            'total' => $scoreNormalizado,
            'bruto' => $score,
            'detalhes' => $detalhes,
        ];
    }

    /**
     * Calcula similaridade entre duas strings usando palavras em comum
     *
     * @param string $str1
     * @param string $str2
     * @return float (0 a 1)
     */
    private function calcularSimilaridade(string $str1, string $str2): float
    {
        // Remove caracteres especiais e divide em palavras
        $palavras1 = collect(preg_split('/\s+/', preg_replace('/[^A-Z0-9\s]/', '', $str1)))
            ->filter(fn($p) => strlen($p) >= 3)
            ->unique();
        
        $palavras2 = collect(preg_split('/\s+/', preg_replace('/[^A-Z0-9\s]/', '', $str2)))
            ->filter(fn($p) => strlen($p) >= 3)
            ->unique();

        if ($palavras1->isEmpty() || $palavras2->isEmpty()) {
            return 0;
        }

        // Conta palavras em comum
        $emComum = $palavras1->intersect($palavras2)->count();
        $totalPalavras = max($palavras1->count(), $palavras2->count());

        return $emComum / $totalPalavras;
    }

    /**
     * Retorna a classificação textual do score
     *
     * @param int $score
     * @return array
     */
    public static function classificarScore(int $score): array
    {
        if ($score >= 80) {
            return ['nivel' => 'alto', 'cor' => 'success', 'texto' => 'Alta confiança'];
        }
        
        if ($score >= 50) {
            return ['nivel' => 'medio', 'cor' => 'warning', 'texto' => 'Média confiança'];
        }
        
        if ($score >= 25) {
            return ['nivel' => 'baixo', 'cor' => 'info', 'texto' => 'Baixa confiança'];
        }

        return ['nivel' => 'muito_baixo', 'cor' => 'secondary', 'texto' => 'Verificar manualmente'];
    }
}
