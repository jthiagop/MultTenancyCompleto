/**
 * Módulo de utilitários para manipulação de valores monetários
 * 
 * Usa aritmética de inteiros (centavos) para evitar erros de precisão
 * de ponto flutuante em cálculos financeiros.
 * 
 * @module financeiro/utils/currency
 */

/**
 * Converte uma string de valor brasileiro para número float
 * Suporta formatos: "1.500,00", "25,00", "1234.56", "1991"
 * 
 * @param {string} valorStr - String do valor a ser convertido
 * @returns {number} Valor numérico em reais
 * 
 * @example
 * parseValorBrasileiro("1.500,00") // 1500.00
 * parseValorBrasileiro("25,50")    // 25.50
 * parseValorBrasileiro("1234.56")  // 1234.56
 */
export function parseValorBrasileiro(valorStr) {
    if (!valorStr || valorStr === '') return 0;
    
    valorStr = String(valorStr).trim();
    
    // Se contém vírgula, é formato brasileiro (1.500,00 ou 25,00)
    if (valorStr.indexOf(',') !== -1) {
        // Remove pontos (milhares) e substitui vírgula por ponto
        return parseFloat(valorStr.replace(/\./g, '').replace(',', '.')) || 0;
    }
    
    // Se contém ponto mas não vírgula, pode ser formato americano (1234.56)
    if (valorStr.indexOf('.') !== -1 && valorStr.indexOf(',') === -1) {
        const pontos = (valorStr.match(/\./g) || []).length;
        // Se tem apenas 1 ponto, é separador decimal
        if (pontos === 1) {
            return parseFloat(valorStr) || 0;
        }
        // Múltiplos pontos = separadores de milhar, remove todos
        return parseFloat(valorStr.replace(/\./g, '')) || 0;
    }
    
    // Se não tem vírgula nem ponto, trata como número inteiro em reais
    const apenasNumeros = valorStr.replace(/\D/g, '');
    return parseFloat(apenasNumeros) || 0;
}

/**
 * Formata um número para string no formato brasileiro
 * 
 * @param {number} valor - Valor numérico a ser formatado
 * @returns {string} Valor formatado (ex: "1.500,00")
 * 
 * @example
 * formatarValorBrasileiro(1500.00) // "1.500,00"
 * formatarValorBrasileiro(25.5)    // "25,50"
 */
export function formatarValorBrasileiro(valor) {
    if (isNaN(valor) || valor === null || valor === undefined) return '0,00';
    return valor.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

/**
 * Converte valor em reais para centavos (inteiro)
 * Usa Math.round para evitar erros de float
 * 
 * @param {number|string} valor - Valor em reais
 * @returns {number} Valor em centavos (inteiro)
 * 
 * @example
 * paraCentavos(15.99)    // 1599
 * paraCentavos("25,50")  // 2550
 */
export function paraCentavos(valor) {
    if (typeof valor === 'string') {
        valor = parseValorBrasileiro(valor);
    }
    return Math.round(valor * 100);
}

/**
 * Converte centavos para reais
 * 
 * @param {number} centavos - Valor em centavos
 * @returns {number} Valor em reais
 * 
 * @example
 * paraReais(1599) // 15.99
 */
export function paraReais(centavos) {
    return centavos / 100;
}

/**
 * Formata centavos diretamente para string brasileira
 * 
 * @param {number} centavos - Valor em centavos
 * @returns {string} Valor formatado (ex: "15,99")
 * 
 * @example
 * formatarCentavos(1599) // "15,99"
 */
export function formatarCentavos(centavos) {
    return formatarValorBrasileiro(paraReais(centavos));
}

/**
 * Divide um valor total em parcelas garantindo soma exata
 * Distribui o resto nas primeiras parcelas (1 centavo a mais cada)
 * 
 * @param {number} valorTotalCentavos - Valor total em centavos
 * @param {number} numParcelas - Número de parcelas
 * @returns {number[]} Array com o valor de cada parcela em centavos
 * 
 * @example
 * dividirEmParcelas(10000, 3) // [3334, 3333, 3333] = 10000
 * dividirEmParcelas(100, 3)   // [34, 33, 33] = 100
 */
export function dividirEmParcelas(valorTotalCentavos, numParcelas) {
    if (numParcelas <= 0) return [];
    if (numParcelas === 1) return [valorTotalCentavos];
    
    // Valor base de cada parcela (inteiro, sem arredondamento)
    const valorBaseParcela = Math.floor(valorTotalCentavos / numParcelas);
    
    // Resto que será distribuído nas primeiras parcelas
    const resto = valorTotalCentavos % numParcelas;
    
    const parcelas = [];
    for (let i = 0; i < numParcelas; i++) {
        // As primeiras 'resto' parcelas recebem 1 centavo a mais
        if (i < resto) {
            parcelas.push(valorBaseParcela + 1);
        } else {
            parcelas.push(valorBaseParcela);
        }
    }
    
    return parcelas;
}

/**
 * Formata valor para exibição com símbolo de moeda
 * 
 * @param {number} valor - Valor em reais
 * @param {boolean} incluirSimbolo - Se deve incluir "R$ "
 * @returns {string} Valor formatado
 * 
 * @example
 * formatarMoeda(1500.00)       // "R$ 1.500,00"
 * formatarMoeda(1500.00, false) // "1.500,00"
 */
export function formatarMoeda(valor, incluirSimbolo = true) {
    const valorFormatado = formatarValorBrasileiro(Math.abs(valor));
    const sinal = valor < 0 ? '-' : '';
    return incluirSimbolo ? `${sinal}R$ ${valorFormatado}` : `${sinal}${valorFormatado}`;
}

/**
 * Valida se uma string representa um valor monetário válido
 * 
 * @param {string} valorStr - String do valor
 * @returns {boolean} Se é um valor válido
 */
export function isValorValido(valorStr) {
    if (!valorStr || valorStr === '') return false;
    const valor = parseValorBrasileiro(valorStr);
    return !isNaN(valor) && isFinite(valor) && valor >= 0;
}

/**
 * Calcula percentual de um valor
 * 
 * @param {number} valorCentavos - Valor em centavos
 * @param {number} totalCentavos - Total em centavos
 * @returns {number} Percentual (0-100)
 */
export function calcularPercentual(valorCentavos, totalCentavos) {
    if (totalCentavos === 0) return 0;
    return (valorCentavos / totalCentavos) * 100;
}

/**
 * Aplica percentual a um valor
 * 
 * @param {number} totalCentavos - Total em centavos
 * @param {number} percentual - Percentual (0-100)
 * @returns {number} Valor calculado em centavos
 */
export function aplicarPercentual(totalCentavos, percentual) {
    return Math.round((totalCentavos * percentual) / 100);
}

// Expor globalmente para compatibilidade com código legado
if (typeof window !== 'undefined') {
    window.FinanceiroUtils = window.FinanceiroUtils || {};
    Object.assign(window.FinanceiroUtils, {
        parseValorBrasileiro,
        formatarValorBrasileiro,
        paraCentavos,
        paraReais,
        formatarCentavos,
        dividirEmParcelas,
        formatarMoeda,
        isValorValido,
        calcularPercentual,
        aplicarPercentual
    });
    
    // Aliases para compatibilidade
    window.parseValorBrasileiro = parseValorBrasileiro;
    window.formatarValorBrasileiro = formatarValorBrasileiro;
    window.parseValorBR = parseValorBrasileiro; // Alias para código que usa esse nome
}
