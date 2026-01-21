<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class FinancialTransactionValidationService
{
    protected FinancialDataFormatterService $formatter;
    protected ParcelaService $parcelaService;

    public function __construct(
        FinancialDataFormatterService $formatter,
        ParcelaService $parcelaService
    ) {
        $this->formatter = $formatter;
        $this->parcelaService = $parcelaService;
    }

    /**
     * Valida dados básicos de uma transação financeira
     *
     * @param array $dados
     * @param bool $validarValorMaiorQueZero
     * @return array ['valid' => bool, 'errors' => array]
     */
    public function validarDadosBasicos(array $dados, bool $validarValorMaiorQueZero = true): array
    {
        $errors = [];

        // Valida valor
        if (!isset($dados['valor']) || $dados['valor'] === null || $dados['valor'] === '') {
            $errors['valor'] = 'O campo valor é obrigatório.';
        } else {
            $valorNumero = is_numeric($dados['valor']) ? (float) $dados['valor'] : $this->formatter->parseValorBrasileiro($dados['valor']);

            if ($validarValorMaiorQueZero && $valorNumero <= 0) {
                $errors['valor'] = 'O valor deve ser maior que zero.';
            }
        }

        // Valida descrição
        if (!isset($dados['descricao']) || trim($dados['descricao']) === '') {
            $errors['descricao'] = 'O campo descrição é obrigatório.';
        }

        // Valida data de competência
        if (!isset($dados['data_competencia']) || trim($dados['data_competencia']) === '') {
            $errors['data_competencia'] = 'O campo data de competência é obrigatório.';
        }

        // Valida entidade
        if (!isset($dados['entidade_id']) || !$dados['entidade_id']) {
            $errors['entidade_id'] = 'O campo entidade financeira é obrigatório.';
        }

        // Valida tipo
        if (!isset($dados['tipo']) || !in_array($dados['tipo'], ['entrada', 'saida'])) {
            $errors['tipo'] = 'O campo tipo deve ser "entrada" ou "saida".';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Valida valor pago não excede valor principal
     *
     * @param float|string $valorPago
     * @param float|string $valorPrincipal
     * @return array ['valid' => bool, 'erro' => string|null]
     */
    public function validarValorPago($valorPago, $valorPrincipal): array
    {
        $valorPagoNumero = is_numeric($valorPago) ? (float) $valorPago : $this->formatter->parseValorBrasileiro((string) $valorPago);
        $valorPrincipalNumero = is_numeric($valorPrincipal) ? (float) $valorPrincipal : $this->formatter->parseValorBrasileiro((string) $valorPrincipal);

        if ($valorPagoNumero > $valorPrincipalNumero) {
            return [
                'valid' => false,
                'erro' => "O valor pago (R$ {$this->formatter->formatarValorBrasileiro($valorPagoNumero)}) não pode ser maior que o valor principal (R$ {$this->formatter->formatarValorBrasileiro($valorPrincipalNumero)}).",
            ];
        }

        return [
            'valid' => true,
            'erro' => null,
        ];
    }

    /**
     * Valida dados de recorrência
     *
     * @param array $dados
     * @return array ['valid' => bool, 'errors' => array]
     */
    public function validarRecorrencia(array $dados): array
    {
        $errors = [];

        // Se não está marcado como recorrente, não valida
        if (!isset($dados['repetir_lancamento']) || $dados['repetir_lancamento'] != '1') {
            return [
                'valid' => true,
                'errors' => [],
            ];
        }

        // Valida configuração de recorrência
        if (!isset($dados['configuracao_recorrencia']) || !$dados['configuracao_recorrencia']) {
            $errors['configuracao_recorrencia'] = 'O campo Configuração de Recorrência é obrigatório quando "Repetir lançamento" está marcado.';
        }

        // Valida dia de cobrança
        if (!isset($dados['dia_cobranca']) || !$dados['dia_cobranca']) {
            $errors['dia_cobranca'] = 'O campo Dia de Cobrança é obrigatório quando "Repetir lançamento" está marcado.';
        }

        // Valida vencimento (que vira 1º Vencimento)
        if (!isset($dados['vencimento']) && !isset($dados['data_vencimento'])) {
            $errors['vencimento'] = 'O campo 1º Vencimento é obrigatório quando "Repetir lançamento" está marcado.';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Valida parcelas
     *
     * @param array $parcelas
     * @param float $valorTotal
     * @return array ['valid' => bool, 'errors' => array]
     */
    public function validarParcelas(array $parcelas, float $valorTotal): array
    {
        $errors = [];

        if (empty($parcelas) || !is_array($parcelas)) {
            return [
                'valid' => true,
                'errors' => [],
            ];
        }

        // Valida soma das parcelas
        $validacaoSoma = $this->parcelaService->validarSomaParcelas($parcelas, $valorTotal);
        if (!$validacaoSoma['valid']) {
            $errors['parcelas'] = $validacaoSoma['erro'];
        }

        // Valida percentuais
        $validacaoPercentuais = $this->parcelaService->validarPercentuaisParcelas($parcelas);
        if (!$validacaoPercentuais['valid']) {
            $errors['parcelas_percentuais'] = $validacaoPercentuais['erro'];
        }

        // Valida cada parcela individualmente
        foreach ($parcelas as $index => $parcela) {
            if (!is_array($parcela)) {
                continue;
            }

            $numeroParcela = $index + 1;

            // Valida valor da parcela
            if (isset($parcela['valor'])) {
                $valorParcela = is_numeric($parcela['valor']) ? (float) $parcela['valor'] : $this->formatter->parseValorBrasileiro((string) $parcela['valor']);
                if ($valorParcela <= 0) {
                    $errors["parcelas.{$index}.valor"] = "O valor da parcela {$numeroParcela} não pode ser zero.";
                }
            }

            // Valida percentual da parcela
            if (isset($parcela['percentual'])) {
                $percentualParcela = is_numeric($parcela['percentual']) ? (float) $parcela['percentual'] : (float) $this->formatter->parseValorBrasileiro((string) $parcela['percentual']);
                if ($percentualParcela <= 0) {
                    $errors["parcelas.{$index}.percentual"] = "O percentual da parcela {$numeroParcela} não pode ser zero.";
                }
            }

            // Valida data de vencimento da parcela
            if (isset($parcela['vencimento']) || isset($parcela['data_vencimento'])) {
                $dataVencimento = $parcela['vencimento'] ?? $parcela['data_vencimento'] ?? null;
                if (!$dataVencimento || trim($dataVencimento) === '') {
                    $errors["parcelas.{$index}.vencimento"] = "A data de vencimento da parcela {$numeroParcela} é obrigatória.";
                }
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Validação completa de uma transação financeira
     *
     * @param array $dados
     * @return array ['valid' => bool, 'errors' => array]
     */
    public function validarTransacaoCompleta(array $dados): array
    {
        $errors = [];

        // Valida dados básicos
        $validacaoBasicos = $this->validarDadosBasicos($dados);
        if (!$validacaoBasicos['valid']) {
            $errors = array_merge($errors, $validacaoBasicos['errors']);
        }

        // Valida recorrência se aplicável
        $validacaoRecorrencia = $this->validarRecorrencia($dados);
        if (!$validacaoRecorrencia['valid']) {
            $errors = array_merge($errors, $validacaoRecorrencia['errors']);
        }

        // Valida valor pago se existir
        if (isset($dados['valor_pago']) && isset($dados['valor'])) {
            $validacaoValorPago = $this->validarValorPago($dados['valor_pago'], $dados['valor']);
            if (!$validacaoValorPago['valid']) {
                $errors['valor_pago'] = $validacaoValorPago['erro'];
            }
        }

        // Valida parcelas se existirem
        if (isset($dados['parcelas']) && is_array($dados['parcelas']) && !empty($dados['parcelas'])) {
            $valorTotal = is_numeric($dados['valor']) ? (float) $dados['valor'] : $this->formatter->parseValorBrasileiro((string) $dados['valor']);
            $validacaoParcelas = $this->validarParcelas($dados['parcelas'], $valorTotal);
            if (!$validacaoParcelas['valid']) {
                $errors = array_merge($errors, $validacaoParcelas['errors']);
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Valida dados usando Validator do Laravel
     *
     * @param array $dados
     * @param array $rules
     * @param array $messages
     * @return \Illuminate\Validation\Validator
     */
    public function validarComLaravel(array $dados, array $rules, array $messages = [])
    {
        return Validator::make($dados, $rules, $messages);
    }
}

