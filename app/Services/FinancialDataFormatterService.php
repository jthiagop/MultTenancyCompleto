<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class FinancialDataFormatterService
{
    /**
     * Converte valor do formato brasileiro (1.234,56) para numérico (1234.56)
     *
     * @param string|null $valorBrasileiro
     * @return float
     */
    public function parseValorBrasileiro(?string $valorBrasileiro): float
    {
        if (!$valorBrasileiro || $valorBrasileiro === '' || trim($valorBrasileiro) === '') {
            return 0.0;
        }

        // Remove espaços
        $valorBrasileiro = trim($valorBrasileiro);

        // Se já é um número válido (contém apenas números e um ponto decimal), retorna direto
        if (preg_match('/^\d+\.?\d*$/', $valorBrasileiro)) {
            return (float) $valorBrasileiro;
        }

        // Se contém vírgula, é formato brasileiro (1.500,00 ou 25,00)
        if (strpos($valorBrasileiro, ',') !== false) {
            // Remove pontos (milhares) e substitui vírgula por ponto
            $valorLimpo = str_replace('.', '', $valorBrasileiro);
            $valorLimpo = str_replace(',', '.', $valorLimpo);
            return (float) $valorLimpo;
        }

        // Se não tem vírgula nem ponto, ou tem múltiplos pontos, tenta parse direto
        return (float) str_replace('.', '', $valorBrasileiro);
    }

    /**
     * Formata valor numérico para formato brasileiro (1234.56 -> 1.234,56)
     *
     * @param float|int|null $valor
     * @param int $decimais
     * @return string
     */
    public function formatarValorBrasileiro($valor, int $decimais = 2): string
    {
        if ($valor === null || $valor === '') {
            return '0,00';
        }

        $valorFloat = (float) $valor;

        // Formata com vírgula como separador decimal e ponto como separador de milhares
        return number_format($valorFloat, $decimais, ',', '.');
    }

    /**
     * Converte data do formato brasileiro (d/m/Y) para formato do banco (Y-m-d)
     *
     * @param string|null $dataBrasileira
     * @param string $formatoOrigem
     * @return string|null
     */
    public function parseDataBrasileira(?string $dataBrasileira, string $formatoOrigem = 'd/m/Y'): ?string
    {
        if (!$dataBrasileira || trim($dataBrasileira) === '') {
            return null;
        }

        $dataBrasileira = trim($dataBrasileira);
        $dataBrasileira = preg_replace('/\s+/', '', $dataBrasileira); // Remove espaços

        try {
            // Tenta parsear no formato especificado
            if ($formatoOrigem === 'd/m/Y') {
                // Valida formato antes de converter
                if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $dataBrasileira, $matches)) {
                    $dia = (int) $matches[1];
                    $mes = (int) $matches[2];
                    $ano = (int) $matches[3];

                    // Valida valores
                    if ($dia >= 1 && $dia <= 31 && $mes >= 1 && $mes <= 12 && $ano >= 1900 && $ano <= 2100) {
                        $carbon = Carbon::create($ano, $mes, $dia, 0, 0, 0);
                        return $carbon->format('Y-m-d');
                    }
                }

                // Fallback: tenta criar diretamente
                $carbon = Carbon::createFromFormat('d/m/Y', $dataBrasileira);
                return $carbon->format('Y-m-d');
            } else {
                $carbon = Carbon::createFromFormat($formatoOrigem, $dataBrasileira);
                return $carbon->format('Y-m-d');
            }
        } catch (\Exception $e) {
            Log::warning('Erro ao converter data brasileira', [
                'data_original' => $dataBrasileira,
                'formato_origem' => $formatoOrigem,
                'erro' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Converte data do formato do banco (Y-m-d) para formato brasileiro (d/m/Y)
     *
     * @param string|null $dataBanco
     * @param string $formatoSaida
     * @return string|null
     */
    public function formatarDataBrasileira(?string $dataBanco, string $formatoSaida = 'd/m/Y'): ?string
    {
        if (!$dataBanco || trim($dataBanco) === '') {
            return null;
        }

        try {
            $carbon = Carbon::parse($dataBanco);
            return $carbon->format($formatoSaida);
        } catch (\Exception $e) {
            Log::warning('Erro ao formatar data para brasileiro', [
                'data_original' => $dataBanco,
                'formato_saida' => $formatoSaida,
                'erro' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Formata valor para moeda brasileira (R$ 1.234,56)
     *
     * @param float|int|null $valor
     * @return string
     */
    public function formatarMoeda($valor): string
    {
        $valorFormatado = $this->formatarValorBrasileiro($valor);
        return 'R$ ' . $valorFormatado;
    }

    /**
     * Prepara dados do formulário para salvar no banco
     * Converte valores e datas do formato brasileiro para formato do banco
     *
     * @param array $dadosFormulario
     * @return array
     */
    public function prepararDadosParaBanco(array $dadosFormulario): array
    {
        $dadosPreparados = $dadosFormulario;

        // Converte valor se existir
        if (isset($dadosPreparados['valor'])) {
            $dadosPreparados['valor'] = $this->parseValorBrasileiro($dadosPreparados['valor']);
        }

        // Converte data_competencia
        if (isset($dadosPreparados['data_competencia'])) {
            $dataConvertida = $this->parseDataBrasileira($dadosPreparados['data_competencia']);
            if ($dataConvertida) {
                $dadosPreparados['data_competencia'] = $dataConvertida;
            }
        }

        // Converte data_vencimento ou vencimento
        if (isset($dadosPreparados['data_vencimento'])) {
            $dataConvertida = $this->parseDataBrasileira($dadosPreparados['data_vencimento']);
            if ($dataConvertida) {
                $dadosPreparados['data_vencimento'] = $dataConvertida;
            }
        }

        if (isset($dadosPreparados['vencimento'])) {
            $dataConvertida = $this->parseDataBrasileira($dadosPreparados['vencimento']);
            if ($dataConvertida) {
                $dadosPreparados['data_vencimento'] = $dataConvertida;
                unset($dadosPreparados['vencimento']);
            }
        }

        // Converte data_pagamento
        if (isset($dadosPreparados['data_pagamento'])) {
            $dataConvertida = $this->parseDataBrasileira($dadosPreparados['data_pagamento']);
            if ($dataConvertida) {
                $dadosPreparados['data_pagamento'] = $dataConvertida;
            }
        }

        // Converte campos de valores monetários adicionais
        $camposValor = ['valor_pago', 'juros', 'multa', 'desconto', 'juros_pagamento', 'multa_pagamento', 'desconto_pagamento', 'valor_a_pagar'];
        foreach ($camposValor as $campo) {
            if (isset($dadosPreparados[$campo])) {
                $dadosPreparados[$campo] = $this->parseValorBrasileiro($dadosPreparados[$campo]);
            }
        }

        return $dadosPreparados;
    }
}

