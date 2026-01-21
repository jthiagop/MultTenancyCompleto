/**
 * Máscaras de formatação para inputs
 * Os números são processados da direita para a esquerda
 */

/**
 * Remove todos os caracteres não numéricos de uma string
 */
export function onlyNumbers(value: string): string {
    return value.replace(/\D/g, '');
}

/**
 * Máscara de CPF: 000.000.000-00
 */
export function maskCPF(value: string): string {
    const numbers = onlyNumbers(value);

    if (numbers.length <= 3) {
        return numbers;
    } else if (numbers.length <= 6) {
        return `${numbers.slice(0, 3)}.${numbers.slice(3)}`;
    } else if (numbers.length <= 9) {
        return `${numbers.slice(0, 3)}.${numbers.slice(3, 6)}.${numbers.slice(6)}`;
    } else {
        return `${numbers.slice(0, 3)}.${numbers.slice(3, 6)}.${numbers.slice(6, 9)}-${numbers.slice(9, 11)}`;
    }
}

/**
 * Máscara de CNPJ: 00.000.000/0000-00
 */
export function maskCNPJ(value: string): string {
    const numbers = onlyNumbers(value);

    if (numbers.length <= 2) {
        return numbers;
    } else if (numbers.length <= 5) {
        return `${numbers.slice(0, 2)}.${numbers.slice(2)}`;
    } else if (numbers.length <= 8) {
        return `${numbers.slice(0, 2)}.${numbers.slice(2, 5)}.${numbers.slice(5)}`;
    } else if (numbers.length <= 12) {
        return `${numbers.slice(0, 2)}.${numbers.slice(2, 5)}.${numbers.slice(5, 8)}/${numbers.slice(8)}`;
    } else {
        return `${numbers.slice(0, 2)}.${numbers.slice(2, 5)}.${numbers.slice(5, 8)}/${numbers.slice(8, 12)}-${numbers.slice(12, 14)}`;
    }
}

/**
 * Máscara que detecta automaticamente CPF ou CNPJ baseado no tamanho
 */
export function maskCPFCNPJ(value: string): string {
    const numbers = onlyNumbers(value);

    if (numbers.length <= 11) {
        return maskCPF(value);
    } else {
        return maskCNPJ(value);
    }
}

/**
 * Máscara de valor monetário: 0,00
 * Processa da direita para a esquerda (como calculadora)
 * Exemplo: Digitar "1" = "0,01", "12" = "0,12", "123" = "1,23", "1234" = "12,34"
 */
export function maskCurrency(value: string): string {
    // Remove tudo exceto números e pontos/vírgulas (para limpar formatação)
    const numbers = onlyNumbers(value);

    // Se não há números ou é apenas zero
    if (!numbers || numbers === '0' || numbers === '') {
        return '0,00';
    }

    // Remove zeros à esquerda, mas mantém pelo menos um zero
    const cleanNumbers = numbers.replace(/^0+/, '') || '0';

    // Se o número tem menos de 3 dígitos, adiciona zeros à esquerda para centavos
    const padded = cleanNumbers.padStart(3, '0');

    // Separa reais (tudo exceto os últimos 2 dígitos) e centavos (últimos 2 dígitos)
    const reais = padded.slice(0, -2);
    const centavos = padded.slice(-2);

    // Adiciona separador de milhares nos reais (ponto a cada 3 dígitos)
    const reaisFormatted = reais.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

    return `${reaisFormatted},${centavos}`;
}

/**
 * Máscara de CEP: 00000-000
 */
export function maskCEP(value: string): string {
    const numbers = onlyNumbers(value);

    if (numbers.length <= 5) {
        return numbers;
    } else {
        return `${numbers.slice(0, 5)}-${numbers.slice(5, 8)}`;
    }
}

/**
 * Máscara de telefone: (00) 0000-0000 ou (00) 00000-0000
 */
export function maskPhone(value: string): string {
    const numbers = onlyNumbers(value);

    if (numbers.length <= 2) {
        return numbers.length > 0 ? `(${numbers}` : '';
    } else if (numbers.length <= 6) {
        return `(${numbers.slice(0, 2)}) ${numbers.slice(2)}`;
    } else if (numbers.length <= 10) {
        return `(${numbers.slice(0, 2)}) ${numbers.slice(2, 6)}-${numbers.slice(6)}`;
    } else {
        // Telefone celular com 11 dígitos
        return `(${numbers.slice(0, 2)}) ${numbers.slice(2, 7)}-${numbers.slice(7, 11)}`;
    }
}

/**
 * Handler para inputs com máscara
 * Retorna o valor formatado e o valor numérico
 */
export function handleMaskedInput(
    value: string,
    mask: 'cpf' | 'cnpj' | 'cpfcnpj' | 'currency' | 'cep' | 'phone'
): { formatted: string; numeric: string } {
    let formatted = '';

    switch (mask) {
        case 'cpf':
            formatted = maskCPF(value);
            break;
        case 'cnpj':
            formatted = maskCNPJ(value);
            break;
        case 'cpfcnpj':
            formatted = maskCPFCNPJ(value);
            break;
        case 'currency':
            formatted = maskCurrency(value);
            break;
        case 'cep':
            formatted = maskCEP(value);
            break;
        case 'phone':
            formatted = maskPhone(value);
            break;
        default:
            formatted = value;
    }

    return {
        formatted,
        numeric: onlyNumbers(value),
    };
}

/**
 * Hook helper para usar máscaras em inputs
 */
export function useMask(mask: 'cpf' | 'cnpj' | 'cpfcnpj' | 'currency' | 'cep' | 'phone') {
    return (e: React.ChangeEvent<HTMLInputElement>) => {
        const { formatted } = handleMaskedInput(e.target.value, mask);
        e.target.value = formatted;
        return formatted;
    };
}

