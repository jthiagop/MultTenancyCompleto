import { useState, useMemo } from 'react';
import {
  CheckCircle,
  Calendar,
  Bot,
  Plus,
  FileQuestion,
  Package,
} from 'lucide-react';
import { Card } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Switch } from '@/components/ui/switch';
import { Skeleton } from '@/components/ui/skeleton';
import { cn } from '@/lib/utils';

// Estrutura real retornada pela IA (via dados_extraidos JSON do backend)
export interface DadosExtraidos {
  tipo_documento?: string;
  estabelecimento?: {
    nome?: string;
    cnpj?: string;
  };
  observacoes?: string;
  financeiro?: {
    data_emissao?: string;
    forma_pagamento?: string;
    valor_total?: number;
    juros?: number;
    multa?: number;
    desconto?: number;
    numero_documento?: string;
    observacoes_financeiras?: string;
  };
  classificacao?: {
    categoria_sugerida?: string;
    descricao_detalhada?: string;
  };
  parcelamento?: {
    is_parcelado?: boolean;
    parcela_atual?: number;
    total_parcelas?: number;
  };
  nfe_info?: {
    numero_nf?: string;
  };
  itens?: Array<{
    descricao?: string;
    quantidade?: number;
    valor_unitario?: number;
    valor?: number;
    categoria_sugerida?: string;
  }>;
}

const SINGLE_TRANSACTION_TYPES = [
  'NF-e', 'NFC-e', 'CUPOM', 'CUPOM_FISCAL', 'NOTA_FISCAL',
  'FATURA_CARTAO', 'BOLETO', 'RECIBO', 'COMPROVANTE',
];

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
  /** Histórico complementar (itens da nota + observações), alinhado ao drawer Domus Blade */
  historico?: string;
  /** CNPJ/CPF só dígitos — match ou cadastro automático de parceiro */
  parceiroDocumento?: string;
  parceiroNomeIa?: string;
}

interface ExtractedDataProps {
  data: DadosExtraidos;
  loading?: boolean;
  onCreateLancamento?: (payload: CreateLancamentoPayload) => void;
}

interface ConsolidatedEntry {
  fornecedor: string;
  cnpj: string | null;
  data: string;
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
  isSingleTransaction: boolean;
  totalItens: number;
}

function consolidateEntries(data: DadosExtraidos): ConsolidatedEntry[] {
  const itens = data.itens ?? [];
  if (itens.length === 0) return [];

  const fornecedor = data.estabelecimento?.nome ?? 'Fornecedor não informado';
  const cnpj = data.estabelecimento?.cnpj ?? null;
  const dataEmissao = data.financeiro?.data_emissao ?? '-';
  const formaPagamento = data.financeiro?.forma_pagamento ?? '';
  const tipoDocumento = data.tipo_documento ?? '';
  const numeroNf = data.nfe_info?.numero_nf ?? null;
  const numeroDocumento = data.financeiro?.numero_documento ?? null;
  const descricao = data.classificacao?.descricao_detalhada ?? null;
  const isParcelado = data.parcelamento?.is_parcelado ?? false;
  const parcelaAtual = data.parcelamento?.parcela_atual ?? 1;
  const totalParcelas = data.parcelamento?.total_parcelas ?? 1;
  const juros = data.financeiro?.juros ?? 0;
  const multa = data.financeiro?.multa ?? 0;
  const desconto = data.financeiro?.desconto ?? 0;

  const isSingle = SINGLE_TRANSACTION_TYPES.includes(tipoDocumento);

  if (isSingle) {
    let valorTotal = data.financeiro?.valor_total ?? 0;
    if (valorTotal <= 0) {
      valorTotal = itens.reduce((sum, item) => {
        const qty = item.quantidade ?? 1;
        const val = item.valor_unitario ?? item.valor ?? 0;
        return sum + qty * val;
      }, 0);
    }

    return [{
      fornecedor,
      cnpj,
      data: formatDate(dataEmissao),
      formaPagamento,
      categoria: data.classificacao?.categoria_sugerida ?? 'Sem categoria',
      valor: valorTotal,
      tipoDocumento,
      numeroNf,
      numeroDocumento,
      descricao,
      isParcelado,
      parcelaAtual,
      totalParcelas,
      juros,
      multa,
      desconto,
      isSingleTransaction: true,
      totalItens: itens.length,
    }];
  }

  return itens.map((item) => ({
    fornecedor,
    cnpj,
    data: formatDate(dataEmissao),
    formaPagamento,
    categoria: item.categoria_sugerida ?? data.classificacao?.categoria_sugerida ?? 'Sem categoria',
    valor: item.valor_unitario ?? item.valor ?? 0,
    tipoDocumento,
    numeroNf,
    numeroDocumento,
    descricao: item.descricao ?? descricao,
    isParcelado,
    parcelaAtual,
    totalParcelas,
    juros,
    multa,
    desconto,
    isSingleTransaction: false,
    totalItens: 0,
  }));
}

function formatDate(raw: string): string {
  if (!raw || raw === '-') return '-';
  try {
    const d = new Date(raw);
    if (isNaN(d.getTime())) return raw;
    return d.toLocaleDateString('pt-BR');
  } catch {
    return raw;
  }
}

function formatDateToIso(displayDate: string): string {
  if (!displayDate || displayDate === '-') return '';
  const parts = displayDate.split('/');
  if (parts.length === 3) {
    return `${parts[2]}-${parts[1].padStart(2, '0')}-${parts[0].padStart(2, '0')}`;
  }
  try {
    const d = new Date(displayDate);
    if (isNaN(d.getTime())) return displayDate;
    return d.toISOString().split('T')[0];
  } catch {
    return displayDate;
  }
}

const fmtCurrency = (v: number) =>
  new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(v);

const fmtCnpj = (cnpj: string) =>
  cnpj.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5');

/** Espelha `prefillForm` → histórico complementar no drawer Domus Blade (`drawer_domusia_despesa.blade.php`). */
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

function onlyDigits(s: string | null | undefined): string {
  return (s ?? '').replace(/\D/g, '');
}

export function ExtractedData({ data, loading, onCreateLancamento }: ExtractedDataProps) {
  const entries = useMemo(() => consolidateEntries(data), [data]);

  if (loading) {
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
        <div className="p-4 flex flex-col gap-3">
          {Array.from({ length: 3 }).map((_, i) => (
            <div key={i} className="flex items-center rounded-lg border border-dashed border-border p-0">
              <div className="pl-5 pr-3 py-3">
                <Skeleton className="w-10 h-5 rounded" />
              </div>
              <div className="flex-1 py-3 px-3 flex flex-col gap-1.5">
                <div className="flex gap-2">
                  <Skeleton className="h-4 w-44" />
                  <Skeleton className="h-4 w-16" />
                </div>
                <div className="flex gap-2">
                  <Skeleton className="h-3.5 w-20" />
                  <Skeleton className="h-3.5 w-16" />
                  <Skeleton className="h-3.5 w-18" />
                </div>
              </div>
              <div className="flex gap-2 pr-4 py-3">
                <Skeleton className="h-8 w-24 rounded" />
                <Skeleton className="h-8 w-28 rounded" />
              </div>
            </div>
          ))}
        </div>
      </Card>
    );
  }

  if (entries.length === 0) {
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
    <Card className="overflow-hidden">
      <div className="flex items-center justify-between px-4 py-3 border-b border-border bg-muted/30">
        <h3 className="text-sm font-semibold flex items-center gap-2">
          <CheckCircle className="size-4 text-green-500" />
          Lançamentos extraídos
        </h3>
        <Badge variant="outline" className="text-xs text-green-600 border-green-200">
          {entries.length} {entries.length === 1 ? 'item' : 'itens'}
        </Badge>
      </div>
      <div className="p-4 flex flex-col gap-3">
        {entries.map((entry, idx) => (
          <ExtractedEntryItem
            key={idx}
            entry={entry}
            dadosCompletos={data}
            onCreateLancamento={onCreateLancamento}
          />
        ))}
      </div>
    </Card>
  );
}

function ExtractedEntryItem({
  entry,
  dadosCompletos,
  onCreateLancamento,
}: {
  entry: ConsolidatedEntry;
  dadosCompletos: DadosExtraidos;
  onCreateLancamento?: (payload: CreateLancamentoPayload) => void;
}) {
  const [isReceita, setIsReceita] = useState(false);
  const tipoLabel = isReceita ? 'Entrada' : 'Saída';
  const tipoColor = isReceita ? 'text-green-600' : 'text-destructive';
  const barColor = isReceita ? 'bg-green-500' : 'bg-destructive';
  const badgeColor = isReceita
    ? 'bg-green-500/10 text-green-600 border-green-500/20'
    : 'bg-destructive/10 text-destructive border-destructive/20';

  return (
    <div className="extracted-entry-card relative flex items-center rounded-lg border border-dashed border-border hover:shadow-md transition-all hover:-translate-y-0.5">
      {/* Barra lateral colorida */}
      <div className={cn('absolute left-0 top-0 bottom-0 w-1 rounded-l-lg transition-colors', barColor)} />

      {/* Toggle entrada/saída */}
      <div className="pl-5 pr-3 py-3 shrink-0 flex items-center gap-2">
        <Switch
          checked={isReceita}
          onCheckedChange={setIsReceita}
          className="data-[state=checked]:bg-green-500 data-[state=unchecked]:bg-destructive"
        />
        <span className={cn('text-[11px] font-semibold whitespace-nowrap transition-colors', tipoColor)}>
          {tipoLabel}
        </span>
      </div>

      <div className="w-px self-stretch bg-border/25 my-2" />

      {/* Conteúdo principal */}
      <div className="flex-1 py-3 px-3 min-w-0 flex flex-col gap-1">
        {/* Linha 1: Fornecedor + badges */}
        <div className="flex items-center gap-2 flex-wrap">
          <span className="font-bold text-sm truncate max-w-[280px]">{entry.fornecedor}</span>
          {entry.cnpj && (
            <span className="text-[10px] text-muted-foreground">{fmtCnpj(entry.cnpj)}</span>
          )}
          {entry.tipoDocumento && (
            <Badge variant="secondary" className="text-[10px] py-0.5">
              {entry.tipoDocumento}
              {entry.numeroNf && ` Nº ${entry.numeroNf}`}
              {!entry.numeroNf && entry.numeroDocumento && ` ${entry.numeroDocumento}`}
            </Badge>
          )}
          {entry.isSingleTransaction && entry.totalItens > 1 && (
            <Badge variant="secondary" className="text-[10px] py-0.5 bg-blue-500/10 text-blue-600 border-blue-500/20">
              <Package className="size-2.5 mr-0.5" />
              {entry.totalItens} itens
            </Badge>
          )}
          {entry.isParcelado && entry.totalParcelas > 1 && (
            <Badge variant="secondary" className="text-[10px] py-0.5 bg-yellow-500/10 text-yellow-600 border-yellow-500/20">
              {entry.parcelaAtual}/{entry.totalParcelas}x
            </Badge>
          )}
        </div>

        {/* Linha 2: Data + Pagamento + Categoria + Encargos */}
        <div className="flex items-center gap-2 flex-wrap text-xs text-muted-foreground">
          <span className="flex items-center gap-1">
            <Calendar className="size-3" />
            {entry.data}
          </span>
          {entry.formaPagamento && (
            <Badge variant="secondary" className="text-[10px] py-0.5">{entry.formaPagamento}</Badge>
          )}
          <Badge variant="secondary" className="text-[10px] py-0.5">{entry.categoria}</Badge>
          {entry.desconto > 0 && (
            <span className="text-green-600 font-semibold">-{fmtCurrency(entry.desconto)}</span>
          )}
          {entry.juros > 0 && (
            <span className="text-yellow-600 font-semibold">+{fmtCurrency(entry.juros)} juros</span>
          )}
          {entry.multa > 0 && (
            <span className="text-destructive font-semibold">+{fmtCurrency(entry.multa)} multa</span>
          )}
        </div>

        {/* Linha 3: Descrição IA */}
        {entry.descricao && entry.descricao !== entry.fornecedor && (
          <span className="text-[11px] text-muted-foreground/70 truncate max-w-[500px] flex items-center gap-1">
            <Bot className="size-3 text-primary shrink-0" />
            {entry.descricao}
          </span>
        )}
      </div>

      {/* Valor + Botão */}
      <div className="flex items-center gap-2 pr-4 py-3 shrink-0">
        <span className={cn(
          'text-sm font-bold px-2.5 py-1.5 rounded-md border transition-colors',
          badgeColor,
        )}>
          <span>{isReceita ? '+' : '-'}</span>
          {fmtCurrency(entry.valor)}
        </span>
        <Button
          size="sm"
          className={cn(
            'gap-1.5 text-xs whitespace-nowrap font-bold transition-colors',
            isReceita
              ? 'bg-green-600 hover:bg-green-700 text-white'
              : 'bg-destructive hover:bg-destructive/90 text-destructive-foreground',
          )}
            onClick={() => {
            const fmtBRL = (v: number) =>
              v > 0 ? v.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : '';

            const historico = buildHistoricoComplementar(dadosCompletos);
            const docEstab = onlyDigits(dadosCompletos.estabelecimento?.cnpj);
            const nomeEstab = (dadosCompletos.estabelecimento?.nome ?? '').trim();

            onCreateLancamento?.({
              tipo: isReceita ? 'receita' : 'despesa',
              descricao: entry.descricao ?? `${entry.tipoDocumento} - ${entry.fornecedor}`,
              valor: fmtBRL(entry.valor),
              dataCompetencia: formatDateToIso(entry.data),
              vencimento: formatDateToIso(entry.data),
              formaPagamento: entry.formaPagamento,
              numeroDocumento: entry.numeroNf ?? entry.numeroDocumento ?? '',
              juros: fmtBRL(entry.juros),
              multa: fmtBRL(entry.multa),
              desconto: fmtBRL(entry.desconto),
              historico: historico || undefined,
              parceiroDocumento: docEstab || undefined,
              parceiroNomeIa: nomeEstab || undefined,
            });
          }}
        >
          <Plus className="size-3" />
          Criar {isReceita ? 'Receita' : 'Despesa'}
        </Button>
      </div>
    </div>
  );
}
