import { useMemo, useState } from 'react';
import {
  CheckCircle,
  Calendar,
  Bot,
  Plus,
  Search,
  FileQuestion,
  Package,
  AlertTriangle,
  Receipt,
  CalendarClock,
  Tag,
  Hash,
  Building2,
} from 'lucide-react';
import { Card } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Switch } from '@/components/ui/switch';
import { Skeleton } from '@/components/ui/skeleton';
import { Alert, AlertContent, AlertDescription, AlertIcon, AlertTitle } from '@/components/ui/alert';
import {
  Tooltip,
  TooltipContent,
  TooltipProvider,
  TooltipTrigger,
} from '@/components/ui/tooltip';
import { cn } from '@/lib/utils';

// ─────────────────────────────────────────────────────────────────────────────
// Tipos — derivados automaticamente do JSON Schema do back-end.
//
// Single source of truth: o schema canônico vive em
//   app/Services/Ai/DocumentExtractorService.php :: getResponseSchema()
// e é convertido em TS via:
//   php artisan domus:dump-extraction-schema
//
// Aqui aplicamos `DeepPartial` porque, embora a IA seja obrigada (via
// Structured Outputs) a sempre retornar todos os campos, documentos
// antigos persistidos podem ter dados parciais. Também mantemos um
// campo legado em `itens[].valor` para compat com extrações antigas.
// ─────────────────────────────────────────────────────────────────────────────

import type { DadosExtraidos as DadosExtraidosStrict } from '../types/dados-extraidos.generated';

type DeepPartial<T> = T extends Array<infer U>
  ? Array<DeepPartial<U>>
  : T extends object
    ? { [K in keyof T]?: DeepPartial<T[K]> }
    : T;

type ItemStrict = NonNullable<DadosExtraidosStrict['itens']>[number];

export type TipoDocumentoIA = DadosExtraidosStrict['tipo_documento'];

export type DadosExtraidos = Omit<DeepPartial<DadosExtraidosStrict>, 'tipo_documento' | 'itens'> & {
  /** Aceita string genérica para tolerar versões futuras do enum. */
  tipo_documento?: TipoDocumentoIA | string;
  itens?: Array<DeepPartial<ItemStrict> & { /** legado — mantido por compat */ valor?: number }>;
};

/**
 * Tipos de documento que representam UMA única transação (todos os itens
 * pertencem à mesma compra → 1 lançamento). Lista alinhada ao enum do backend.
 */
const SINGLE_TRANSACTION_TYPES: ReadonlyArray<TipoDocumentoIA> = [
  'NF-e',
  'NFC-e',
  'CUPOM',
  'FATURA_CARTAO',
  'BOLETO',
  'RECIBO',
  'COMPROVANTE',
];

/**
 * Tipos que tipicamente representam uma entrada de dinheiro (receita).
 * Os demais são tratados como despesa por padrão, mas o usuário pode
 * inverter via toggle.
 */
const RECEITA_TYPES: ReadonlyArray<TipoDocumentoIA> = ['RECIBO'];

function inferTipoLancamento(tipo: string | undefined): boolean {
  if (!tipo) return false;
  return RECEITA_TYPES.includes(tipo as TipoDocumentoIA);
}

export interface CreateLancamentoPayload {
  tipo: 'receita' | 'despesa';
  descricao: string;
  valor: string;
  dataCompetencia: string;
  vencimento: string;
  formaPagamento: string;
  numeroDocumento: string;
  juros: string;
  multa: string;
  desconto: string;
  /** Histórico complementar (itens da nota + observações). */
  historico?: string;
  /** CNPJ/CPF só dígitos — match ou cadastro automático de parceiro */
  parceiroDocumento?: string;
  parceiroNomeIa?: string;
}

export interface SearchLancamentoPayload {
  /** Tipo provável (receita = entrada, despesa = saida) — usado como filtro inicial. */
  tipo: 'entrada' | 'saida';
  /** Valor numérico do documento (R$). */
  valor: number;
  /** Descrição/resumo do documento (header do sheet). */
  descricao: string;
}

interface ExtractedDataProps {
  data: DadosExtraidos;
  loading?: boolean;
  onCreateLancamento?: (payload: CreateLancamentoPayload) => void;
  onSearchLancamento?: (payload: SearchLancamentoPayload) => void;
}

interface ConsolidatedEntry {
  fornecedor: string;
  cnpj: string | null;
  /** Data formatada para exibição (dd/mm/yyyy) */
  dataDisplay: string;
  /** Data ISO original (YYYY-MM-DD) — usada na criação do lançamento */
  dataIso: string;
  vencimentoIso: string;
  formaPagamento: string;
  categoria: string;
  valor: number;
  tipoDocumento: string;
  numeroNf: string | null;
  numeroDocumento: string | null;
  descricao: string | null;
  isParcelado: boolean;
  parcelaAtual: number;
  totalParcelas: number;
  juros: number;
  multa: number;
  desconto: number;
  impostosRetidos: number;
  isSingleTransaction: boolean;
  totalItens: number;
}

// ─────────────────────────────────────────────────────────────────────────────
// Helpers
// ─────────────────────────────────────────────────────────────────────────────

function formatDateBR(iso: string | null | undefined): string {
  if (!iso) return '-';
  const m = /^(\d{4})-(\d{2})-(\d{2})/.exec(iso);
  if (m) return `${m[3]}/${m[2]}/${m[1]}`;
  const d = new Date(iso);
  if (!isNaN(d.getTime())) return d.toLocaleDateString('pt-BR');
  return iso;
}

const fmtCurrency = (v: number) =>
  new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(v);

const fmtCnpjCpf = (doc: string) => {
  const digits = doc.replace(/\D/g, '');
  if (digits.length === 14) {
    return digits.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5');
  }
  if (digits.length === 11) {
    return digits.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
  }
  return doc;
};

function onlyDigits(s: string | null | undefined): string {
  return (s ?? '').replace(/\D/g, '');
}

/**
 * Formata um número como string monetária brasileira (sem símbolo) para
 * preencher campos de formulário do drawer (que esperam "1.234,56").
 */
const fmtBRLString = (v: number): string =>
  v > 0
    ? v.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
    : '';

/**
 * Constrói o histórico complementar (itens da nota + observações), espelhando
 * o comportamento do drawer Domus Blade.
 */
export function buildHistoricoComplementar(data: DadosExtraidos): string {
  const historicoParts: string[] = [];
  const itens = data.itens ?? [];
  const fin = data.financeiro ?? {};

  if (itens.length > 0) {
    historicoParts.push('ITENS:');
    itens.forEach((it, idx) => {
      const desc = (it.descricao ?? `Item ${idx + 1}`).trim();
      const qtd = it.quantidade ?? 1;
      const vlrUnit = parseFloat(String(it.valor_unitario ?? it.valor ?? 0));
      const subtotal = qtd * vlrUnit;
      let linha = `${qtd}x ${desc}`;
      if (vlrUnit > 0) {
        linha += ` (R$ ${vlrUnit.toFixed(2).replace('.', ',')}`;
        if (qtd > 1) {
          linha += ` = R$ ${subtotal.toFixed(2).replace('.', ',')}`;
        }
        linha += ')';
      }
      historicoParts.push(linha);
    });
  }

  if (data.observacoes) historicoParts.push(data.observacoes);
  if (fin.observacoes_financeiras) historicoParts.push(fin.observacoes_financeiras);

  const text = historicoParts.join('\n');
  return text.length > 500 ? text.substring(0, 500) : text;
}

function getEstabelecimentoNome(data: DadosExtraidos): string {
  return (
    data.estabelecimento?.nome?.trim()
    || data.nfe_info?.emitente?.nome?.trim()
    || 'Fornecedor não informado'
  );
}

function getEstabelecimentoCnpj(data: DadosExtraidos): string | null {
  const cnpj = data.estabelecimento?.cnpj || data.nfe_info?.emitente?.cnpj;
  return cnpj ? onlyDigits(cnpj) || null : null;
}

/**
 * Calcula o valor total. Para documentos sem itens, usa `valor_total`
 * direto; quando há itens e `valor_total` está zerado, soma os itens.
 */
function resolveValorTotal(data: DadosExtraidos): number {
  const declarado = data.financeiro?.valor_total ?? 0;
  if (declarado > 0) return declarado;

  const itens = data.itens ?? [];
  return itens.reduce((sum, item) => {
    const qty = item.quantidade ?? 1;
    const val = item.valor_total_item ?? item.valor_unitario ?? item.valor ?? 0;
    return sum + (item.valor_total_item ? val : qty * val);
  }, 0);
}

/**
 * Consolida as entradas extraídas em uma lista de "lançamentos sugeridos".
 *
 * - Documentos de transação única (NF-e, BOLETO, RECIBO, etc.) geram 1 entrada
 *   mesmo quando há vários itens.
 * - Documentos sem itens (boleto, comprovante PIX, recibo de doação) recebem
 *   uma entrada sintética baseada nos dados financeiros principais.
 * - Documentos genéricos (OUTRO) com múltiplos itens podem gerar várias entradas.
 */
function consolidateEntries(data: DadosExtraidos): ConsolidatedEntry[] {
  const itens = data.itens ?? [];
  const fornecedor = getEstabelecimentoNome(data);
  const cnpj = getEstabelecimentoCnpj(data);
  const dataEmissao = data.financeiro?.data_emissao ?? '';
  const dataVencimento = data.financeiro?.data_vencimento ?? '';
  const formaPagamento = data.financeiro?.forma_pagamento ?? '';
  const tipoDocumento = (data.tipo_documento ?? '') as string;
  const numeroNf = data.nfe_info?.numero_nf ?? null;
  const numeroDocumento = data.financeiro?.numero_documento ?? null;
  const descricao = data.classificacao?.descricao_detalhada ?? null;
  const isParcelado = data.parcelamento?.is_parcelado ?? false;
  const parcelaAtual = data.parcelamento?.parcela_atual ?? 1;
  const totalParcelas = data.parcelamento?.total_parcelas ?? 1;
  const juros = data.financeiro?.juros ?? 0;
  const multa = data.financeiro?.multa ?? 0;
  const desconto = data.financeiro?.desconto ?? 0;
  const impostosRetidos = data.financeiro?.impostos_retidos ?? 0;

  const isSingle = SINGLE_TRANSACTION_TYPES.includes(tipoDocumento as TipoDocumentoIA);
  const valorPrincipal = resolveValorTotal(data);

  const baseEntry: Omit<ConsolidatedEntry, 'valor' | 'categoria' | 'descricao' | 'totalItens' | 'isSingleTransaction'> = {
    fornecedor,
    cnpj,
    dataDisplay: formatDateBR(dataEmissao),
    dataIso: dataEmissao || '',
    vencimentoIso: dataVencimento || dataEmissao || '',
    formaPagamento,
    tipoDocumento,
    numeroNf,
    numeroDocumento,
    isParcelado,
    parcelaAtual,
    totalParcelas,
    juros,
    multa,
    desconto,
    impostosRetidos,
  };

  // Caso 1 — transação única OU documento sem itens: gera UMA entrada consolidada
  if (isSingle || itens.length === 0) {
    return [{
      ...baseEntry,
      valor: valorPrincipal,
      categoria: data.classificacao?.categoria_sugerida ?? 'Sem categoria',
      descricao: descricao,
      isSingleTransaction: true,
      totalItens: itens.length,
    }];
  }

  // Caso 2 — documento "OUTRO" com múltiplos itens: uma entrada por item
  return itens.map((item) => ({
    ...baseEntry,
    valor: item.valor_total_item ?? (item.quantidade ?? 1) * (item.valor_unitario ?? item.valor ?? 0),
    categoria: item.categoria_sugerida ?? data.classificacao?.categoria_sugerida ?? 'Sem categoria',
    descricao: item.descricao ?? descricao,
    isSingleTransaction: false,
    totalItens: 0,
  }));
}

// ─────────────────────────────────────────────────────────────────────────────
// Componente — Visão geral do documento (cabeçalho)
// ─────────────────────────────────────────────────────────────────────────────

function DocumentSummary({ data }: { data: DadosExtraidos }) {
  const fornecedor = getEstabelecimentoNome(data);
  const cnpj = getEstabelecimentoCnpj(data);
  const fin = data.financeiro ?? {};
  const tipo = data.tipo_documento ?? 'OUTRO';

  const valorTotal = fin.valor_total ?? 0;
  const valorPrincipal = fin.valor_principal ?? 0;
  const desconto = fin.desconto ?? 0;
  const juros = fin.juros ?? 0;
  const multa = fin.multa ?? 0;
  const impostosRetidos = fin.impostos_retidos ?? 0;
  const valorLiquido = valorTotal - impostosRetidos;

  const hasRetencao = impostosRetidos > 0;
  const hasAjustes = desconto > 0 || juros > 0 || multa > 0;
  const hasVencimento = !!fin.data_vencimento && fin.data_vencimento !== fin.data_emissao;

  return (
    <Card className="overflow-hidden">
      <div className="px-4 py-3 border-b border-border bg-muted/30 flex items-center justify-between">
        <h3 className="text-sm font-semibold flex items-center gap-2">
          <Receipt className="size-4 text-primary" />
          Visão geral do documento
        </h3>
        <Badge variant="outline" className="text-xs">{tipo}</Badge>
      </div>

      <div className="p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
        {/* Fornecedor */}
        <div className="flex items-start gap-2 min-w-0">
          <Building2 className="size-4 text-muted-foreground mt-0.5 shrink-0" />
          <div className="min-w-0">
            <div className="text-[11px] uppercase tracking-wide text-muted-foreground">Fornecedor</div>
            <div className="text-sm font-semibold truncate" title={fornecedor}>{fornecedor}</div>
            {cnpj && (
              <div className="text-[11px] text-muted-foreground">{fmtCnpjCpf(cnpj)}</div>
            )}
          </div>
        </div>

        {/* Datas */}
        <div className="flex items-start gap-2">
          <Calendar className="size-4 text-muted-foreground mt-0.5 shrink-0" />
          <div className="min-w-0">
            <div className="text-[11px] uppercase tracking-wide text-muted-foreground">
              {hasVencimento ? 'Emissão / Vencimento' : 'Data de emissão'}
            </div>
            <div className="text-sm font-medium">
              {formatDateBR(fin.data_emissao)}
              {hasVencimento && (
                <>
                  <span className="text-muted-foreground mx-1.5">•</span>
                  <span className="inline-flex items-center gap-1">
                    <CalendarClock className="size-3 text-yellow-600" />
                    {formatDateBR(fin.data_vencimento)}
                  </span>
                </>
              )}
            </div>
          </div>
        </div>

        {/* Número documento */}
        {(data.nfe_info?.numero_nf || fin.numero_documento) && (
          <div className="flex items-start gap-2">
            <Hash className="size-4 text-muted-foreground mt-0.5 shrink-0" />
            <div className="min-w-0">
              <div className="text-[11px] uppercase tracking-wide text-muted-foreground">Número</div>
              <div className="text-sm font-medium">
                {data.nfe_info?.numero_nf
                  ? `NF ${data.nfe_info.numero_nf}${data.nfe_info.serie ? ` / Série ${data.nfe_info.serie}` : ''}`
                  : fin.numero_documento}
              </div>
            </div>
          </div>
        )}

        {/* Forma de pagamento + categoria */}
        {(fin.forma_pagamento || data.classificacao?.categoria_sugerida) && (
          <div className="flex items-start gap-2">
            <Tag className="size-4 text-muted-foreground mt-0.5 shrink-0" />
            <div className="min-w-0 flex flex-wrap gap-1.5">
              {fin.forma_pagamento && (
                <Badge variant="secondary" className="text-[10px]">{fin.forma_pagamento}</Badge>
              )}
              {data.classificacao?.categoria_sugerida && (
                <Badge variant="secondary" className="text-[10px]">{data.classificacao.categoria_sugerida}</Badge>
              )}
            </div>
          </div>
        )}
      </div>

      {/* Linha de valores */}
      <div className="px-4 pb-4 grid grid-cols-2 md:grid-cols-4 gap-3">
        {valorPrincipal > 0 && hasAjustes && (
          <ValueCell label="Valor principal" value={fmtCurrency(valorPrincipal)} muted />
        )}
        {desconto > 0 && (
          <ValueCell label="Desconto" value={`- ${fmtCurrency(desconto)}`} className="text-green-600" />
        )}
        {(juros > 0 || multa > 0) && (
          <ValueCell
            label="Juros + multa"
            value={`+ ${fmtCurrency(juros + multa)}`}
            className="text-yellow-600"
          />
        )}
        <ValueCell
          label={hasRetencao ? 'Valor bruto' : 'Valor total'}
          value={fmtCurrency(valorTotal)}
          highlight
        />
      </div>

      {/* ALERTA DE RETENÇÃO — crítico para tesouraria */}
      {hasRetencao && (
        <div className="px-4 pb-4">
          <Alert variant="warning" appearance="light" size="md">
            <AlertIcon>
              <AlertTriangle />
            </AlertIcon>
            <AlertContent>
              <AlertTitle>Atenção: documento com impostos retidos</AlertTitle>
              <AlertDescription>
                <div className="space-y-1 text-sm">
                  <div>
                    Impostos retidos:{' '}
                    <span className="font-semibold">{fmtCurrency(impostosRetidos)}</span>
                  </div>
                  <div>
                    Valor LÍQUIDO a pagar ao fornecedor:{' '}
                    <span className="font-bold text-base">{fmtCurrency(valorLiquido)}</span>
                  </div>
                  <div className="text-xs opacity-80">
                    Não pague o valor bruto — confira a guia de retenção antes de finalizar.
                  </div>
                </div>
              </AlertDescription>
            </AlertContent>
          </Alert>
        </div>
      )}

      {data.observacoes && (
        <div className="px-4 pb-4">
          <div className="text-[11px] uppercase tracking-wide text-muted-foreground mb-1">Observações da IA</div>
          <p className="text-xs text-muted-foreground/90 leading-relaxed">{data.observacoes}</p>
        </div>
      )}
    </Card>
  );
}

function ValueCell({
  label,
  value,
  className,
  muted,
  highlight,
}: {
  label: string;
  value: string;
  className?: string;
  muted?: boolean;
  highlight?: boolean;
}) {
  return (
    <div
      className={cn(
        'rounded-md px-3 py-2 border',
        highlight ? 'bg-primary/5 border-primary/20' : 'bg-muted/40 border-border',
      )}
    >
      <div className="text-[10px] uppercase tracking-wide text-muted-foreground">{label}</div>
      <div
        className={cn(
          'text-sm font-semibold',
          muted && 'text-muted-foreground',
          className,
        )}
      >
        {value}
      </div>
    </div>
  );
}

// ─────────────────────────────────────────────────────────────────────────────
// Componente principal
// ─────────────────────────────────────────────────────────────────────────────

export function ExtractedData({
  data,
  loading,
  onCreateLancamento,
  onSearchLancamento,
}: ExtractedDataProps) {
  const entries = useMemo(() => consolidateEntries(data), [data]);
  const hasAnyData = !loading && (
    !!data.tipo_documento
    || !!data.estabelecimento?.nome
    || (data.financeiro?.valor_total ?? 0) > 0
    || (data.itens?.length ?? 0) > 0
  );

  if (loading) {
    return <ExtractedDataSkeleton />;
  }

  if (!hasAnyData) {
    return (
      <Card className="flex flex-col items-center justify-center py-10 gap-3">
        <FileQuestion className="size-10 text-muted-foreground/30" />
        <h4 className="font-semibold text-muted-foreground">Sem dados extraídos</h4>
        <p className="text-xs text-muted-foreground/70 text-center max-w-xs">
          Não foi possível extrair dados deste documento.<br />
          Verifique se o arquivo está legível e tente novamente.
        </p>
      </Card>
    );
  }

  return (
    <TooltipProvider delayDuration={200}>
      <div className="flex flex-col gap-4">
        <DocumentSummary data={data} />

        <Card className="overflow-hidden">
          <div className="flex items-center justify-between px-4 py-3 border-b border-border bg-muted/30">
            <div className="flex items-center gap-2 min-w-0">
              <CheckCircle className="size-4 text-green-500 shrink-0" />
              <h3 className="text-sm font-semibold">Lançamentos sugeridos</h3>
              <span className="hidden md:inline text-[10px] text-muted-foreground/70">
                · alterne <span className="font-semibold">Entrada/Saída</span> se a IA inverteu
              </span>
            </div>
            <Badge variant="outline" className="text-xs text-green-600 border-green-200 shrink-0">
              {entries.length} {entries.length === 1 ? 'item' : 'itens'}
            </Badge>
          </div>
          <div className="p-4 flex flex-col gap-2.5">
            {entries.map((entry, idx) => (
              <ExtractedEntryItem
                key={idx}
                entry={entry}
                dadosCompletos={data}
                onCreateLancamento={onCreateLancamento}
                onSearchLancamento={onSearchLancamento}
              />
            ))}
          </div>
        </Card>
      </div>
    </TooltipProvider>
  );
}

function ExtractedDataSkeleton() {
  return (
    <Card className="overflow-hidden">
      <div className="flex items-center justify-between px-4 py-3 border-b border-border bg-muted/30">
        <h3 className="text-sm font-semibold flex items-center gap-2">
          <CheckCircle className="size-4 text-green-500" />
          Lançamentos extraídos
        </h3>
        <Badge variant="outline" className="text-xs text-muted-foreground">
          carregando…
        </Badge>
      </div>
      <div className="p-4 flex flex-col gap-2.5">
        {Array.from({ length: 3 }).map((_, i) => (
          <div key={i} className="flex items-stretch rounded-lg border border-border overflow-hidden">
            <div className="w-1 bg-muted shrink-0" />
            <div className="flex flex-col items-center justify-center gap-1.5 px-3 py-3 shrink-0 bg-muted/20">
              <Skeleton className="h-4 w-8 rounded-full" />
              <Skeleton className="h-2.5 w-10" />
            </div>
            <div className="flex-1 py-3 px-4 flex flex-col gap-1.5 border-l border-border/60">
              <div className="flex gap-2">
                <Skeleton className="h-4 w-44" />
                <Skeleton className="h-4 w-16" />
              </div>
              <div className="flex gap-1.5">
                <Skeleton className="h-3.5 w-20" />
                <Skeleton className="h-3.5 w-24" />
              </div>
              <div className="flex gap-1.5">
                <Skeleton className="h-3.5 w-20" />
                <Skeleton className="h-3.5 w-16" />
                <Skeleton className="h-3.5 w-20" />
              </div>
            </div>
            <div className="flex items-center gap-2 px-4 py-3 shrink-0 border-l border-border/60 bg-muted/10">
              <Skeleton className="h-7 w-24 rounded-md" />
              <Skeleton className="h-7 w-32 rounded-md" />
              <Skeleton className="h-7 w-32 rounded-md" />
            </div>
          </div>
        ))}
      </div>
    </Card>
  );
}

function ExtractedEntryItem({
  entry,
  dadosCompletos,
  onCreateLancamento,
  onSearchLancamento,
}: {
  entry: ConsolidatedEntry;
  dadosCompletos: DadosExtraidos;
  onCreateLancamento?: (payload: CreateLancamentoPayload) => void;
  onSearchLancamento?: (payload: SearchLancamentoPayload) => void;
}) {
  const [isReceita, setIsReceita] = useState(() => inferTipoLancamento(entry.tipoDocumento));

  const tipoLabel = isReceita ? 'Entrada' : 'Saída';
  const tipoColor = isReceita ? 'text-green-600' : 'text-destructive';
  const barColor = isReceita ? 'bg-green-500' : 'bg-destructive';
  const valorWrapperColor = isReceita
    ? 'bg-green-500/10 text-green-600 border-green-500/20'
    : 'bg-destructive/10 text-destructive border-destructive/20';

  const showDescricao =
    !!entry.descricao && entry.descricao !== entry.fornecedor;

  const handleCreate = () => {
    const historico = buildHistoricoComplementar(dadosCompletos);
    const docEstab = onlyDigits(dadosCompletos.estabelecimento?.cnpj ?? dadosCompletos.nfe_info?.emitente?.cnpj);
    const nomeEstab = (dadosCompletos.estabelecimento?.nome ?? dadosCompletos.nfe_info?.emitente?.nome ?? '').trim();

    onCreateLancamento?.({
      tipo: isReceita ? 'receita' : 'despesa',
      descricao: entry.descricao ?? `${entry.tipoDocumento} - ${entry.fornecedor}`,
      valor: fmtBRLString(entry.valor),
      dataCompetencia: entry.dataIso,
      vencimento: entry.vencimentoIso,
      formaPagamento: entry.formaPagamento,
      numeroDocumento: entry.numeroNf ?? entry.numeroDocumento ?? '',
      juros: fmtBRLString(entry.juros),
      multa: fmtBRLString(entry.multa),
      desconto: fmtBRLString(entry.desconto),
      historico: historico || undefined,
      parceiroDocumento: docEstab || undefined,
      parceiroNomeIa: nomeEstab || undefined,
    });
  };

  const handleSearch = () => {
    onSearchLancamento?.({
      tipo: isReceita ? 'entrada' : 'saida',
      valor: entry.valor,
      descricao: entry.descricao ?? `${entry.tipoDocumento} - ${entry.fornecedor}`,
    });
  };

  return (
    <div
      className={cn(
        'extracted-entry-card relative flex items-stretch overflow-hidden rounded-lg border border-border bg-card',
        'transition-all hover:border-primary/40 hover:shadow-sm',
      )}
    >
      {/* Barra lateral colorida — sinaliza visualmente o tipo (entrada/saída) */}
      <div className={cn('w-1 shrink-0 transition-colors', barColor)} />

      {/* Coluna 1 — Toggle Entrada/Saída */}
      <div className="flex flex-col items-center justify-center gap-1.5 px-3 py-3 shrink-0 bg-muted/20">
        <Switch
          checked={isReceita}
          onCheckedChange={setIsReceita}
          aria-label="Alternar entre Entrada e Saída"
          className="data-[state=checked]:bg-green-500 data-[state=unchecked]:bg-destructive"
        />
        <span
          className={cn(
            'text-[10px] font-bold uppercase tracking-wide whitespace-nowrap transition-colors',
            tipoColor,
          )}
        >
          {tipoLabel}
        </span>
      </div>

      {/* Coluna 2 — Conteúdo (fornecedor + meta) */}
      <div className="flex-1 min-w-0 flex flex-col justify-center gap-1.5 px-4 py-3 border-l border-border/60">
        {/* Linha 1 — Fornecedor + alerta crítico (retenção) */}
        <div className="flex items-center gap-2 flex-wrap">
          <span
            className="font-bold text-sm text-foreground truncate max-w-[340px]"
            title={entry.fornecedor}
          >
            {entry.fornecedor}
          </span>
          {entry.impostosRetidos > 0 && (
            <Tooltip>
              <TooltipTrigger asChild>
                <Badge
                  variant="secondary"
                  className="text-[10px] py-0.5 bg-yellow-500/15 text-yellow-700 border-yellow-500/30 cursor-help"
                >
                  <AlertTriangle className="size-2.5 mr-0.5" />
                  Retenção
                </Badge>
              </TooltipTrigger>
              <TooltipContent variant="light">
                Impostos retidos: {fmtCurrency(entry.impostosRetidos)}
                <br />
                Líquido: {fmtCurrency(entry.valor - entry.impostosRetidos)}
              </TooltipContent>
            </Tooltip>
          )}
        </div>

        {/* Linha 2 — Identificação do documento (chips) */}
        {(entry.tipoDocumento || entry.cnpj || (entry.isSingleTransaction && entry.totalItens > 1) || entry.isParcelado) && (
          <div className="flex items-center gap-1.5 flex-wrap">
            {entry.tipoDocumento && (
              <Badge variant="secondary" className="text-[10px] py-0.5 font-medium">
                {entry.tipoDocumento}
                {entry.numeroNf && ` Nº ${entry.numeroNf}`}
                {!entry.numeroNf && entry.numeroDocumento && ` ${entry.numeroDocumento}`}
              </Badge>
            )}
            {entry.cnpj && (
              <span className="text-[10px] text-muted-foreground tabular-nums">
                {fmtCnpjCpf(entry.cnpj)}
              </span>
            )}
            {entry.isSingleTransaction && entry.totalItens > 1 && (
              <Badge
                variant="secondary"
                className="text-[10px] py-0.5 bg-blue-500/10 text-blue-600 border-blue-500/20"
              >
                <Package className="size-2.5 mr-0.5" />
                {entry.totalItens} itens
              </Badge>
            )}
            {entry.isParcelado && entry.totalParcelas > 1 && (
              <Badge
                variant="secondary"
                className="text-[10px] py-0.5 bg-yellow-500/10 text-yellow-600 border-yellow-500/20"
              >
                {entry.parcelaAtual}/{entry.totalParcelas}x
              </Badge>
            )}
          </div>
        )}

        {/* Linha 3 — Datas, categoria, pagamento, ajustes */}
        <div className="flex items-center gap-2 flex-wrap text-xs text-muted-foreground">
          <span className="flex items-center gap-1">
            <Calendar className="size-3" />
            {entry.dataDisplay}
          </span>
          {entry.vencimentoIso && entry.vencimentoIso !== entry.dataIso && (
            <span className="flex items-center gap-1 text-yellow-600">
              <CalendarClock className="size-3" />
              vence {formatDateBR(entry.vencimentoIso)}
            </span>
          )}
          {entry.formaPagamento && (
            <Badge variant="secondary" className="text-[10px] py-0.5">
              {entry.formaPagamento}
            </Badge>
          )}
          <Badge variant="secondary" className="text-[10px] py-0.5">
            {entry.categoria}
          </Badge>
          {entry.desconto > 0 && (
            <span className="text-green-600 font-semibold">
              -{fmtCurrency(entry.desconto)}
            </span>
          )}
          {entry.juros > 0 && (
            <span className="text-yellow-600 font-semibold">
              +{fmtCurrency(entry.juros)} juros
            </span>
          )}
          {entry.multa > 0 && (
            <span className="text-destructive font-semibold">
              +{fmtCurrency(entry.multa)} multa
            </span>
          )}
        </div>

        {/* Linha 4 — Descrição da IA */}
        {showDescricao && (
          <Tooltip>
            <TooltipTrigger asChild>
              <span className="text-[11px] text-muted-foreground/80 truncate max-w-[520px] flex items-center gap-1 cursor-help">
                <Bot className="size-3 text-primary shrink-0" />
                {entry.descricao}
              </span>
            </TooltipTrigger>
            <TooltipContent variant="light" className="max-w-md">
              {entry.descricao}
            </TooltipContent>
          </Tooltip>
        )}
      </div>

      {/* Coluna 3 — Valor + Ações (em linha, CTA azul padrão) */}
      <div className="flex items-center gap-2 px-4 py-3 shrink-0 border-l border-border/60 bg-muted/10">
        <div
          className={cn(
            'text-base font-bold tabular-nums px-2.5 py-1.5 rounded-md border whitespace-nowrap transition-colors',
            valorWrapperColor,
          )}
        >
          <span>{isReceita ? '+' : '-'}</span>
          {fmtCurrency(entry.valor)}
        </div>
        <Button
          variant="primary"
          size="sm"
          className="gap-1.5 text-xs font-semibold whitespace-nowrap"
          onClick={handleCreate}
        >
          <Plus className="size-3" />
          Criar {isReceita ? 'Receita' : 'Despesa'}
        </Button>
        <Button
          variant="outline"
          size="sm"
          className="gap-1.5 text-xs font-semibold whitespace-nowrap"
          onClick={handleSearch}
          disabled={!onSearchLancamento}
          title="Vincular a um lançamento já existente"
        >
          <Search className="size-3" />
          Buscar Lançamento
        </Button>
      </div>
    </div>
  );
}
