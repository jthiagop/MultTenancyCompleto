<?php

namespace App\Support;

/**
 * Classe mestra para tratamento de valores monetários
 * 
 * Centraliza toda a lógica de conversão e formatação de valores monetários,
 * substituindo conversões manuais espalhadas pelo código.
 * 
 * @package App\Support
 */
class Money
{
    /**
     * Valor monetário armazenado (sempre positivo e absoluto)
     * 
     * @var float
     */
    private float $amount;

    /**
     * Flag para indicar se o valor original era negativo (usado em fromOfx)
     * 
     * @var bool
     */
    private bool $isNegative = false;

    /**
     * Construtor privado - use os métodos estáticos de factory
     * 
     * @param float $amount Valor monetário (sempre positivo)
     * @param bool $isNegative Se o valor original era negativo
     */
    private function __construct(float $amount, bool $isNegative = false)
    {
        $this->amount = abs($amount);
        $this->isNegative = $isNegative;
    }

    /**
     * Factory: Cria instância a partir de input humano (formato brasileiro)
     * 
     * Aceita formatos como:
     * - '1.991,44' (formato brasileiro com separadores)
     * - '1000' (número inteiro)
     * - 'R$ 1.500,00' (com símbolo de moeda)
     * - '1.500,00' (sem símbolo)
     * 
     * @param string $value Valor em formato brasileiro
     * @return self
     */
    public static function fromHumanInput(string $value): self
    {
        // Remove símbolos de moeda e espaços
        $cleanValue = preg_replace('/[R$\s]/', '', trim($value));
        
        // Se está vazio, retorna zero
        if (empty($cleanValue)) {
            return new self(0.0);
        }

        // Se contém vírgula, é formato brasileiro (1.500,00 ou 25,00)
        if (strpos($cleanValue, ',') !== false) {
            // Remove pontos (milhares) e substitui vírgula por ponto
            $cleanValue = str_replace('.', '', $cleanValue);
            $cleanValue = str_replace(',', '.', $cleanValue);
            return new self((float) $cleanValue);
        }

        // Se contém ponto mas não vírgula, pode ser formato americano (1234.56)
        if (strpos($cleanValue, '.') !== false && strpos($cleanValue, ',') === false) {
            $pontos = substr_count($cleanValue, '.');
            // Se tem apenas 1 ponto, é separador decimal
            if ($pontos === 1) {
                return new self((float) $cleanValue);
            }
            // Múltiplos pontos = separadores de milhar, remove todos
            $cleanValue = str_replace('.', '', $cleanValue);
            return new self((float) $cleanValue);
        }

        // Se não tem vírgula nem ponto, trata como número inteiro em reais
        // Exemplo: "1991" → 1991.00
        $apenasNumeros = preg_replace('/[^0-9]/', '', $cleanValue);
        return new self((float) $apenasNumeros);
    }

    /**
     * Factory: Cria instância a partir de valor OFX (pode ser negativo)
     * 
     * Valores vindos de extratos bancários/OFX podem ser negativos.
     * Armazena o valor absoluto mas preserva a informação de sinal.
     * 
     * @param float $value Valor do OFX (pode ser negativo)
     * @return self
     */
    public static function fromOfx(float $value): self
    {
        $isNegative = $value < 0;
        return new self($value, $isNegative);
    }

    /**
     * Factory: Cria instância a partir de valor do banco de dados
     * 
     * Para valores já salvos no banco (ex: coluna DECIMAL)
     * Exemplo: 1991.44 (já está em formato numérico)
     * 
     * @param float $value Valor do banco de dados
     * @return self
     */
    public static function fromDatabase(float $value): self
    {
        return new self($value);
    }

    /**
     * Factory: Cria instância a partir de centavos (legado)
     * 
     * Para casos onde o sistema antigo salvou em centavos (integer)
     * Exemplo: 199144 (centavos) → 1991.44 (reais)
     * 
     * @param int $cents Valor em centavos
     * @return self
     */
    public static function fromCents(int $cents): self
    {
        return new self($cents / 100.0);
    }

    /**
     * Converte para formato de banco de dados (DECIMAL)
     * 
     * Retorna float formatado com 2 casas decimais para salvar em colunas DECIMAL
     * Exemplo: 1991.44
     * 
     * @return float
     */
    public function toDatabase(): float
    {
        return round($this->amount, 2);
    }

    /**
     * Converte para centavos (integer)
     * 
     * Retorna integer para casos de legado ou APIs externas
     * Exemplo: 199144 (centavos)
     * 
     * @return int
     */
    public function toCents(): int
    {
        return (int) round($this->amount * 100);
    }

    /**
     * Formata para exibição brasileira
     * 
     * Retorna string formatada no padrão brasileiro
     * Exemplo: 'R$ 1.991,44'
     * 
     * @return string
     */
    public function toBrl(): string
    {
        return 'R$ ' . number_format($this->amount, 2, ',', '.');
    }

    /**
     * Verifica se o valor original era negativo
     * 
     * Útil para valores vindos de OFX onde o sinal indica débito/crédito
     * 
     * @return bool True se o valor original era negativo
     */
    public function isNegative(): bool
    {
        return $this->isNegative;
    }

    /**
     * Retorna o valor absoluto (float)
     * 
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * Retorna o valor com sinal (float)
     * 
     * Se isNegative() for true, retorna negativo, senão positivo
     * 
     * @return float
     */
    public function getSignedAmount(): float
    {
        return $this->isNegative ? -$this->amount : $this->amount;
    }

    /**
     * Soma dois valores Money
     * 
     * @param Money $other Outro valor Money
     * @return self Nova instância com o resultado
     */
    public function add(Money $other): self
    {
        $result = $this->amount + $other->amount;
        return new self($result);
    }

    /**
     * Subtrai dois valores Money
     * 
     * @param Money $other Outro valor Money
     * @return self Nova instância com o resultado
     */
    public function subtract(Money $other): self
    {
        $result = $this->amount - $other->amount;
        return new self($result);
    }

    /**
     * Multiplica por um número
     * 
     * @param float $multiplier Multiplicador
     * @return self Nova instância com o resultado
     */
    public function multiply(float $multiplier): self
    {
        $result = $this->amount * $multiplier;
        return new self($result);
    }

    /**
     * Divide por um número
     * 
     * @param float $divisor Divisor
     * @return self Nova instância com o resultado
     * @throws \InvalidArgumentException Se o divisor for zero
     */
    public function divide(float $divisor): self
    {
        if ($divisor == 0) {
            throw new \InvalidArgumentException('Divisão por zero não permitida');
        }
        $result = $this->amount / $divisor;
        return new self($result);
    }

    /**
     * Compara dois valores Money
     * 
     * @param Money $other Outro valor Money
     * @return int -1 se menor, 0 se igual, 1 se maior
     */
    public function compare(Money $other): int
    {
        if ($this->amount < $other->amount) {
            return -1;
        }
        if ($this->amount > $other->amount) {
            return 1;
        }
        return 0;
    }

    /**
     * Verifica se é igual a outro valor Money
     * 
     * @param Money $other Outro valor Money
     * @return bool
     */
    public function equals(Money $other): bool
    {
        return $this->compare($other) === 0;
    }

    /**
     * Verifica se é maior que outro valor Money
     * 
     * @param Money $other Outro valor Money
     * @return bool
     */
    public function greaterThan(Money $other): bool
    {
        return $this->compare($other) > 0;
    }

    /**
     * Verifica se é menor que outro valor Money
     * 
     * @param Money $other Outro valor Money
     * @return bool
     */
    public function lessThan(Money $other): bool
    {
        return $this->compare($other) < 0;
    }

    /**
     * Retorna representação string (formato brasileiro)
     * 
     * @return string
     */
    public function __toString(): string
    {
        return $this->toBrl();
    }
}
