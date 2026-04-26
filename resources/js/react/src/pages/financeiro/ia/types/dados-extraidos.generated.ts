/**
 * ARQUIVO GERADO AUTOMATICAMENTE — NÃO EDITE MANUALMENTE.
 *
 * Para regenerar:
 *   php artisan domus:dump-extraction-schema
 *
 * Fonte canônica:
 *   app/Services/Ai/DocumentExtractorService.php :: getResponseSchema()
 *
 * Este arquivo garante paridade entre o JSON Schema enviado à OpenAI
 * (Structured Outputs) e o tipo consumido no front-end React.
 */

export interface DadosExtraidos {
  /** Tipo do documento fiscal */
  tipo_documento: 'NF-e' | 'NFC-e' | 'BOLETO' | 'RECIBO' | 'FATURA_CARTAO' | 'COMPROVANTE' | 'CUPOM' | 'OUTRO';
  estabelecimento: {
    /** Nome do estabelecimento/fornecedor (EMITENTE) */
    nome: string | null;
    /** CNPJ apenas com números (14 dígitos, sem formatação) */
    cnpj: string | null;
  };
  nfe_info: {
    /** Chave de acesso da NF-e/NFC-e (44 dígitos numéricos, sem espaços) */
    chave_acesso: string | null;
    /** Número da nota fiscal (apenas dígitos, sem zeros à esquerda) */
    numero_nf: string | null;
    /** Série da nota fiscal */
    serie: string | null;
    emitente: {
      /** Nome/Razão Social do emitente */
      nome: string | null;
      /** CNPJ do emitente (apenas dígitos) */
      cnpj: string | null;
    };
    destinatario: {
      /** Nome do destinatário */
      nome: string | null;
      /** CNPJ ou CPF do destinatário (apenas dígitos) */
      cnpj_cpf: string | null;
    };
  };
  financeiro: {
    /** Data de emissão no formato YYYY-MM-DD */
    data_emissao: string | null;
    /** Data de vencimento no formato YYYY-MM-DD (boletos, faturas). Null se não aplicável. */
    data_vencimento: string | null;
    /** Valor FINAL a pagar. Prioridade: (1) VALOR A PAGAR, (2) Pago − Troco, (3) Subtotal − Desconto + Acréscimo. */
    valor_total: number;
    /** Valor original/subtotal ANTES de juros, multa e desconto */
    valor_principal: number;
    /** Nome descritivo da forma de pagamento (ex: "PIX", "Cartão de Crédito", "Dinheiro") */
    forma_pagamento: string | null;
    /** ID da forma de pagamento correspondente na lista fornecida. Null se não encontrar correspondência. */
    forma_pagamento_id: number | null;
    /** Número do documento (boleto, NF, recibo) */
    numero_documento: string | null;
    /** Valor de juros/mora/encargos. 0.00 se não houver. */
    juros: number;
    /** Valor de multa por atraso. 0.00 se não houver. */
    multa: number;
    /** Valor de desconto/abatimento. 0.00 se não houver. */
    desconto: number;
    /** Total de impostos retidos (ISS, IRRF, PIS, COFINS, CSLL). 0.00 se não houver. */
    impostos_retidos: number;
    /** Notas sobre cobrança, vencimento, juros/multa, parcelamento */
    observacoes_financeiras: string | null;
  };
  parcelamento: {
    /** Indica se é parcelado */
    is_parcelado: boolean;
    /** Número da parcela atual (1 se não parcelado) */
    parcela_atual: number;
    /** Total de parcelas (1 se não parcelado) */
    total_parcelas: number;
    /** Frequência: MENSAL, SEMANAL, QUINZENAL, ANUAL ou UNICA */
    frequencia: string;
  };
  classificacao: {
    /** Descrição detalhada do item ou serviço principal */
    descricao_detalhada: string | null;
    /** Categoria sugerida para classificação contábil */
    categoria_sugerida: string | null;
    /** ID do lançamento padrão correspondente na lista fornecida. Null se não encontrar correspondência. */
    lancamento_padrao_id: number | null;
    /** Código de referência do documento */
    codigo_referencia: string | null;
  };
  /** Alertas gerais e contextuais sobre o documento */
  observacoes: string | null;
  /** Lista de itens/produtos do documento */
  itens: Array<{
    /** Descrição limpa do item (sem códigos EAN/GTIN) */
    descricao: string | null;
    /** Quantidade do item */
    quantidade: number;
    /** Preço de UMA unidade (não o subtotal da linha) */
    valor_unitario: number;
    /** Valor total do item (quantidade × valor_unitario) */
    valor_total_item: number;
    /** Categoria sugerida para o item */
    categoria_sugerida: string | null;
  }>;
}
