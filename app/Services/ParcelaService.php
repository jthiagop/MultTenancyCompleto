<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ParcelaService
{
    protected FinancialDataFormatterService $formatter;

    public function __construct(FinancialDataFormatterService $formatter)
    {
        $this->formatter = $formatter;
    }

    /**
     * Calcula valores e percentuais das parcelas
     *
     * @param float $valorTotal
     * @param int $numeroParcelas
     * @return array Array com valores e percentuais de cada parcela
     */
    public function calcularParcelas(float $valorTotal, int $numeroParcelas): array
    {
        if ($numeroParcelas < 1 || $valorTotal <= 0) {
            return [];
        }

        $parcelas = [];
        $valorPorParcela = $valorTotal / $numeroParcelas;
        $percentualPorParcela = 100 / $numeroParcelas;

        for ($i = 1; $i <= $numeroParcelas; $i++) {
            // Última parcela recebe o resto para compensar arredondamentos
            if ($i === $numeroParcelas) {
                $valorParcela = $valorTotal - ($valorPorParcela * ($numeroParcelas - 1));
                $percentualParcela = 100 - ($percentualPorParcela * ($numeroParcelas - 1));
            } else {
                $valorParcela = $valorPorParcela;
                $percentualParcela = $percentualPorParcela;
            }

            $parcelas[] = [
                'numero' => $i,
                'valor' => round($valorParcela, 2),
                'percentual' => round($percentualParcela, 2),
            ];
        }

        return $parcelas;
    }

    /**
     * Calcula data de vencimento de uma parcela baseada na data base e número da parcela
     *
     * @param string|Carbon $dataBase Data base para cálculo (Y-m-d ou Carbon)
     * @param int $numeroParcela Número da parcela (1, 2, 3...)
     * @param int $intervaloMeses Intervalo em meses entre parcelas (padrão: 1)
     * @return string Data formatada Y-m-d
     */
    public function calcularDataVencimento($dataBase, int $numeroParcela, int $intervaloMeses = 1): string
    {
        if (!$dataBase) {
            $dataBase = Carbon::now();
        }

        if (!$dataBase instanceof Carbon) {
            try {
                $dataBase = Carbon::parse($dataBase);
            } catch (\Exception $e) {
                $dataBase = Carbon::now();
            }
        }

        // Calcula a data de vencimento: data base + (número da parcela - 1) * intervalo em meses
        $dataVencimento = $dataBase->copy()->addMonths(($numeroParcela - 1) * $intervaloMeses);

        return $dataVencimento->format('Y-m-d');
    }

    /**
     * Valida se os valores das parcelas somam o valor total
     *
     * @param array $parcelas Array de parcelas com 'valor'
     * @param float $valorTotal Valor total esperado
     * @param float $tolerancia Tolerância para diferença (padrão: 0.01)
     * @return array ['valid' => bool, 'erro' => string|null, 'soma' => float]
     */
    public function validarSomaParcelas(array $parcelas, float $valorTotal, float $tolerancia = 0.01): array
    {
        $soma = 0.0;

        foreach ($parcelas as $parcela) {
            $valor = is_array($parcela) ? ($parcela['valor'] ?? 0) : (float) $parcela;
            $soma += (float) $valor;
        }

        $diferenca = abs($soma - $valorTotal);

        return [
            'valid' => $diferenca <= $tolerancia,
            'erro' => $diferenca > $tolerancia ? "Soma das parcelas (R$ {$this->formatter->formatarValorBrasileiro($soma)}) não confere com o valor total (R$ {$this->formatter->formatarValorBrasileiro($valorTotal)}). Diferença: R$ {$this->formatter->formatarValorBrasileiro($diferenca)}" : null,
            'soma' => $soma,
            'diferenca' => $diferenca,
        ];
    }

    /**
     * Valida se os percentuais das parcelas somam 100%
     *
     * @param array $parcelas Array de parcelas com 'percentual'
     * @param float $tolerancia Tolerância para diferença (padrão: 0.01)
     * @return array ['valid' => bool, 'erro' => string|null, 'soma' => float]
     */
    public function validarPercentuaisParcelas(array $parcelas, float $tolerancia = 0.01): array
    {
        $soma = 0.0;

        foreach ($parcelas as $parcela) {
            $percentual = is_array($parcela) ? ($parcela['percentual'] ?? 0) : (float) $parcela;
            $soma += (float) $percentual;
        }

        $diferenca = abs($soma - 100);

        return [
            'valid' => $diferenca <= $tolerancia,
            'erro' => $diferenca > $tolerancia ? "Soma dos percentuais ({$this->formatter->formatarValorBrasileiro($soma, 2)}%) não confere com 100%. Diferença: {$this->formatter->formatarValorBrasileiro($diferenca, 2)}%" : null,
            'soma' => $soma,
            'diferenca' => $diferenca,
        ];
    }

    /**
     * Recalcula valores das parcelas baseado nos percentuais
     *
     * @param array $parcelas Array de parcelas com 'percentual'
     * @param float $valorTotal Valor total para cálculo
     * @return array Array de parcelas com valores recalculados
     */
    public function recalcularValoresPorPercentuais(array $parcelas, float $valorTotal): array
    {
        $parcelasRecalculadas = [];

        foreach ($parcelas as $index => $parcela) {
            $percentual = is_array($parcela) ? ($parcela['percentual'] ?? 0) : 0;
            $valor = ($valorTotal * (float) $percentual) / 100;

            if (is_array($parcela)) {
                $parcela['valor'] = round($valor, 2);
                $parcelasRecalculadas[] = $parcela;
            } else {
                $parcelasRecalculadas[] = round($valor, 2);
            }
        }

        return $parcelasRecalculadas;
    }

    /**
     * Recalcula percentuais das parcelas baseado nos valores
     *
     * @param array $parcelas Array de parcelas com 'valor'
     * @param float $valorTotal Valor total para cálculo
     * @return array Array de parcelas com percentuais recalculados
     */
    public function recalcularPercentuaisPorValores(array $parcelas, float $valorTotal): array
    {
        if ($valorTotal <= 0) {
            return $parcelas;
        }

        $parcelasRecalculadas = [];

        foreach ($parcelas as $index => $parcela) {
            $valor = is_array($parcela) ? ($parcela['valor'] ?? 0) : (float) $parcela;
            $percentual = ($valor / $valorTotal) * 100;

            if (is_array($parcela)) {
                $parcela['percentual'] = round($percentual, 2);
                $parcelasRecalculadas[] = $parcela;
            } else {
                $parcelasRecalculadas[] = round($percentual, 2);
            }
        }

        return $parcelasRecalculadas;
    }

    /**
     * Prepara dados das parcelas para salvar no banco
     * Converte valores e datas do formato brasileiro para formato do banco
     *
     * @param array $parcelas Array de parcelas do formulário
     * @param float $valorTotal Valor total (para validação e recálculo se necessário)
     * @return array Array de parcelas preparadas para banco
     */
    public function prepararParcelasParaBanco(array $parcelas, float $valorTotal): array
    {
        $parcelasPreparadas = [];
        $formatter = $this->formatter;

        foreach ($parcelas as $index => $parcela) {
            if (!is_array($parcela)) {
                continue;
            }

            $parcelaPreparada = $parcela;

            // Converte valor
            if (isset($parcela['valor'])) {
                $parcelaPreparada['valor'] = $formatter->parseValorBrasileiro($parcela['valor']);
            }

            // Converte percentual (pode estar como string)
            if (isset($parcela['percentual'])) {
                $parcelaPreparada['percentual'] = (float) $formatter->parseValorBrasileiro((string) $parcela['percentual']);
            }

            // Converte data de vencimento
            if (isset($parcela['vencimento'])) {
                $dataConvertida = $formatter->parseDataBrasileira($parcela['vencimento']);
                if ($dataConvertida) {
                    $parcelaPreparada['data_vencimento'] = $dataConvertida;
                    unset($parcelaPreparada['vencimento']);
                }
            }

            // Converte data_vencimento se já existir
            if (isset($parcela['data_vencimento'])) {
                $dataConvertida = $formatter->parseDataBrasileira($parcela['data_vencimento']);
                if ($dataConvertida) {
                    $parcelaPreparada['data_vencimento'] = $dataConvertida;
                }
            }

            $parcelasPreparadas[] = $parcelaPreparada;
        }

        // Valida soma das parcelas
        $validacao = $this->validarSomaParcelas($parcelasPreparadas, $valorTotal);
        if (!$validacao['valid']) {
            Log::warning('Soma das parcelas não confere', [
                'soma_parcelas' => $validacao['soma'],
                'valor_total' => $valorTotal,
                'diferenca' => $validacao['diferenca'],
            ]);
        }

        return $parcelasPreparadas;
    }

    /**
     * Gera descrição automática para parcela
     *
     * @param string $descricaoBase
     * @param int $numeroParcela
     * @param int $totalParcelas
     * @return string
     */
    public function gerarDescricaoParcela(string $descricaoBase, int $numeroParcela, int $totalParcelas): string
    {
        return trim($descricaoBase) . ' ' . $numeroParcela . '/' . $totalParcelas;
    }
}

