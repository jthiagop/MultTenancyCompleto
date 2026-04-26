/**
 * Sheet "Buscar Lançamento" do Dominus IA.
 *
 * Apresenta a tabela completa de transações (mesmo padrão de
 * extrato-table/despesas-table/receitas-table) com filtros e seleção múltipla
 * para o usuário vincular o documento extraído pela IA a lançamentos
 * pré-existentes.
 *
 * Modos de anexação (POST /financeiro/domusia/{id}/anexar-lancamento):
 *  - anexar             → adiciona o arquivo como comprovante em N lançamentos
 *                          (não toca em valor/situação).
 *  - baixar_total       → 1 lançamento: anexo + dá baixa total via
 *                          `registrarPagamento` (cria Movimentacao).
 *  - pagamento_parcial  → 1 lançamento: anexo + registra pagamento parcial via
 *                          `transacao_fracionamentos`.
 */
import { useCallback, useEffect, useMemo, useRef, useState } from 'react';
import {
  ColumnDef,
  PaginationState,
  RowSelectionState,
  SortingState,
  VisibilityState,
  getCoreRowModel,
  getPaginationRowModel,
  getSortedRowModel,
  useReactTable,
} from '@tanstack/react-table';
import {
  AlertTriangle,
  ArrowDownCircle,
  ArrowUpCircle,
  CheckCircle2,
  ChevronDown,
  FileText,
  Inbox,
  Info,
  LayoutList,
  Loader2,
  Paperclip,
  Search,
  Wallet,
} from 'lucide-react';

import { cn } from '@/lib/utils';
import { notify } from '@/lib/notify';
import { useFormSelectData } from '@/hooks/useFormSelectData';
import {
  useTransacoes,
  type ITransacao,
  type StatsContas,
  type StatsExtrato,
} from '@/hooks/useTransacoes';

import {
  Sheet,
  SheetContent,
  SheetDescription,
  SheetHeader,
  SheetTitle,
} from '@/components/ui/sheet';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardFooter, CardTable } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { ScrollArea, ScrollBar } from '@/components/ui/scroll-area';
import { ButtonGroup } from '@/components/ui/button-group';
import { DataGrid } from '@/components/ui/data-grid';
import { DataGridColumnHeader } from '@/components/ui/data-grid-column-header';
import { DataGridPagination } from '@/components/ui/data-grid-pagination';
import {
  DataGridTable,
  DataGridTableRowSelect,
  DataGridTableRowSelectAll,
} from '@/components/ui/data-grid-table';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuGroup,
  DropdownMenuItem,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { CurrencyInput } from '@/components/common/masked-input';
import { TransacaoFiltersRow } from '@/pages/financeiro/components/transacao-filters-row';
import { SummaryStatsBar } from '@/pages/financeiro/components/summary-stats-bar';
import {
  TransacaoAdvancedFiltersScope,
  TransacaoAdvancedFiltersTrigger,
  TransacaoAdvancedFiltersChipsSection,
  type TransacaoAdvancedTipoFormData,
} from '@/pages/financeiro/components/transacao-advanced-filters-bar';
import { financeiroToolbarSoftBlueClass } from '@/lib/financeiro-toolbar-accent';
import { type PeriodValue, defaultPeriod } from '@/components/ui/period-picker';
import { type TransacaoAdvancedFiltersState } from '@/hooks/useTransacoes';

import {
  DescricaoCell,
  OrigemCell,
  SITUACAO_VARIANT,
  fmtCurrency,
  type SituacaoColor,
} from '@/pages/financeiro/components/transacao-table-shared';

// ─────────────────────────────────────────────────────────────────────────────
// Tipos
// ─────────────────────────────────────────────────────────────────────────────

type TipoFilter = 'all' | 'entrada' | 'saida';
type AttachMode = 'anexar' | 'baixar_total' | 'pagamento_parcial';

interface LancamentoSearchSheetProps {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  documentoId: number | null;
  documentoValor: number;
  documentoDescricao?: string | null;
  tipoSugerido?: 'entrada' | 'saida';
  onAttached?: (transacaoIds: string[]) => void;
}

// ─────────────────────────────────────────────────────────────────────────────
// Helpers
// ─────────────────────────────────────────────────────────────────────────────

function isoToday(): string {
  const d = new Date();
  return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
}

function parseCurrencyBr(v: string): number {
  if (!v) return 0;
  return parseFloat(v.replace(/\./g, '').replace(',', '.')) || 0;
}

function brCurrencyDigits(n: number): string {
  return n.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

// ─────────────────────────────────────────────────────────────────────────────
// Componente principal
// ─────────────────────────────────────────────────────────────────────────────

export function LancamentoSearchSheet({
  open,
  onOpenChange,
  documentoId,
  documentoValor,
  documentoDescricao,
  tipoSugerido,
  onAttached,
}: LancamentoSearchSheetProps) {
  // ── Estado dos filtros / tabela ────────────────────────────────────────────
  const [pagination, setPagination] = useState<PaginationState>({ pageIndex: 0, pageSize: 20 });
  const [sorting, setSorting] = useState<SortingState>([{ id: 'vencimento', desc: true }]);
  const [rowSelection, setRowSelection] = useState<RowSelectionState>({});

  const [searchQuery, setSearchQuery] = useState('');
  const [tipo, setTipo] = useState<TipoFilter>(tipoSugerido ?? 'all');
  const [period, setPeriod] = useState<PeriodValue>(defaultPeriod);
  const [advancedFilters, setAdvancedFilters] = useState<TransacaoAdvancedFiltersState>({});
  const [activeStatKey, setActiveStatKey] = useState<string>('total');

  // Cache de transações selecionadas em todas as páginas (id → ITransacao).
  // Mantém soma e ações coerentes mesmo quando o usuário pagina ou aplica filtros
  // depois de selecionar linhas.
  const selectedRowsMapRef = useRef<Map<string, ITransacao>>(new Map());

  // ── Estado do dialog de pagamento ──────────────────────────────────────────
  const [pagamentoOpen, setPagamentoOpen] = useState(false);
  const [pagamentoMode, setPagamentoMode] = useState<Exclude<AttachMode, 'anexar'>>('baixar_total');

  // ── Estado do dialog de baixa em massa (múltiplos lançamentos) ─────────────
  const [baixarMassaOpen, setBaixarMassaOpen] = useState(false);

  // ── Estado de envio ────────────────────────────────────────────────────────
  const [saving, setSaving] = useState(false);

  const sortCol = sorting[0];

  const onAdvancedFiltersChange = useCallback((next: TransacaoAdvancedFiltersState) => {
    setAdvancedFilters(next);
    setPagination((p) => ({ ...p, pageIndex: 0 }));
  }, []);

  // Reset quando o sheet abre.
  useEffect(() => {
    if (!open) return;
    setRowSelection({});
    selectedRowsMapRef.current = new Map();
    setPagination({ pageIndex: 0, pageSize: 20 });
    setSearchQuery('');
    setTipo(tipoSugerido ?? 'all');
    setPeriod(defaultPeriod);
    setAdvancedFilters({});
    setActiveStatKey('total');
  }, [open, tipoSugerido]);

  // Reset paginação ao mudar filtros.
  useEffect(() => {
    setPagination((p) => ({ ...p, pageIndex: 0 }));
  }, [tipo, period, advancedFilters, activeStatKey]);

  // Quando troca o tipo, o conjunto de stat keys muda — volta para "total".
  useEffect(() => {
    setActiveStatKey('total');
  }, [tipo]);

  // ── Fetch via hook compartilhado ──────────────────────────────────────────
  const tab: 'contas_receber' | 'contas_pagar' | 'extrato' =
    tipo === 'entrada' ? 'contas_receber' : tipo === 'saida' ? 'contas_pagar' : 'extrato';

  const tipoFormData: TransacaoAdvancedTipoFormData =
    tipo === 'entrada' ? 'receita' : tipo === 'saida' ? 'despesa' : 'all';

  const { data, stats, pagination: meta, loading, error, refetch } = useTransacoes({
    tipo,
    tab,
    startDate: period.startDate,
    endDate: period.endDate,
    search: searchQuery || undefined,
    status: activeStatKey !== 'total' ? activeStatKey : undefined,
    page: pagination.pageIndex + 1,
    perPage: pagination.pageSize,
    sortBy: sortCol?.id,
    sortDir: sortCol?.desc ? 'desc' : 'asc',
    advancedFilters,
  });

  const statsContas = (tipo !== 'all' ? (stats as StatsContas | null) : null);
  const statsExtrato = (tipo === 'all' ? (stats as StatsExtrato | null) : null);

  // Itens para a SummaryStatsBar — variam conforme o tipo selecionado.
  const statsItems = useMemo(() => {
    if (tipo === 'entrada') {
      return [
        { statKey: 'vencidos', label: 'Vencidos (R$)',         value: statsContas?.vencidos  ?? 0, colorClass: 'text-rose-500',    accentColor: '#f43f5e' },
        { statKey: 'hoje',     label: 'Vencem hoje (R$)',      value: statsContas?.hoje      ?? 0, colorClass: 'text-pink-500',    accentColor: '#ec4899' },
        { statKey: 'a_vencer', label: 'A vencer (R$)',         value: statsContas?.a_vencer  ?? 0, colorClass: 'text-blue-500',    accentColor: '#3b82f6' },
        { statKey: 'pagos',    label: 'Recebidos (R$)',        value: statsContas?.recebidos ?? statsContas?.pagos ?? 0, colorClass: 'text-emerald-500', accentColor: '#10b981' },
        { statKey: 'total',    label: 'Total do período (R$)', value: statsContas?.total     ?? 0, colorClass: 'text-blue-600',    accentColor: '#2563eb' },
      ];
    }
    if (tipo === 'saida') {
      return [
        { statKey: 'vencidos', label: 'Vencidos (R$)',         value: statsContas?.vencidos ?? 0, colorClass: 'text-rose-500',    accentColor: '#f43f5e' },
        { statKey: 'hoje',     label: 'Vencem hoje (R$)',      value: statsContas?.hoje     ?? 0, colorClass: 'text-pink-500',    accentColor: '#ec4899' },
        { statKey: 'a_vencer', label: 'A vencer (R$)',         value: statsContas?.a_vencer ?? 0, colorClass: 'text-blue-500',    accentColor: '#3b82f6' },
        { statKey: 'pagos',    label: 'Pagos (R$)',            value: statsContas?.pagos    ?? 0, colorClass: 'text-emerald-500', accentColor: '#10b981' },
        { statKey: 'total',    label: 'Total do período (R$)', value: statsContas?.total    ?? 0, colorClass: 'text-blue-600',    accentColor: '#2563eb' },
      ];
    }
    return [
      { statKey: 'receitas_aberto',     label: 'Receitas em aberto (R$)', value: statsExtrato?.receitas_aberto     ?? 0, colorClass: 'text-blue-500',    accentColor: '#3b82f6' },
      { statKey: 'receitas_realizadas', label: 'Receitas recebidas (R$)', value: statsExtrato?.receitas_realizadas ?? 0, colorClass: 'text-emerald-500', accentColor: '#10b981' },
      { statKey: 'despesas_aberto',    label: 'Despesas em aberto (R$)', value: statsExtrato?.despesas_aberto     ?? 0, colorClass: 'text-pink-500',    accentColor: '#ec4899' },
      { statKey: 'despesas_realizadas', label: 'Despesas pagas (R$)',     value: statsExtrato?.despesas_realizadas ?? 0, colorClass: 'text-rose-500',    accentColor: '#f43f5e' },
      { statKey: 'total',               label: 'Total do período (R$)',   value: statsExtrato?.total               ?? 0, colorClass: 'text-blue-600',    accentColor: '#2563eb' },
    ];
  }, [tipo, statsContas, statsExtrato]);

  // Mantém o cache de selecionados sincronizado: a cada nova página de dados,
  // reaplica os objetos atualizados no Map. Linhas removidas da seleção saem.
  useEffect(() => {
    const map = selectedRowsMapRef.current;
    data.forEach((row) => {
      if (rowSelection[row.id]) map.set(row.id, row);
    });
    Array.from(map.keys()).forEach((id) => {
      if (!rowSelection[id]) map.delete(id);
    });
  }, [data, rowSelection]);

  // ── Definição de colunas (DataGrid completo) ──────────────────────────────
  const columns = useMemo<ColumnDef<ITransacao>[]>(
    () => [
      {
        accessorKey: 'id',
        header: () => <DataGridTableRowSelectAll />,
        cell: ({ row }) => <DataGridTableRowSelect row={row} />,
        enableSorting: false,
        enableHiding: false,
        size: 48,
      },
      {
        id: 'vencimento',
        accessorKey: 'vencimento',
        header: ({ column }) => <DataGridColumnHeader title="Vencimento" column={column} />,
        cell: ({ row }) => (
          <span className="text-foreground font-normal tabular-nums">
            {row.original.vencimento ?? row.original.data ?? '—'}
          </span>
        ),
        enableSorting: true,
        size: 110,
      },
      {
        id: 'descricao',
        accessorKey: 'descricao',
        header: ({ column }) => <DataGridColumnHeader title="Descrição" column={column} />,
        cell: ({ row }) => <DescricaoCell row={row} />,
        enableSorting: true,
        size: 280,
      },
      {
        id: 'parceiro',
        accessorKey: 'parceiro',
        header: ({ column }) => (
          <DataGridColumnHeader title="Fornecedor / Cliente" column={column} />
        ),
        cell: ({ row }) => <span className="truncate">{row.original.parceiro ?? '—'}</span>,
        enableSorting: false,
        size: 180,
      },
      {
        id: 'origem',
        accessorKey: 'origem',
        header: ({ column }) => <DataGridColumnHeader title="Origem" column={column} />,
        cell: ({ row }) => (
          <OrigemCell
            origem={row.original.origem}
            origemNome={row.original.origem_nome}
            origemAgencia={row.original.origem_agencia}
            origemConta={row.original.origem_conta}
          />
        ),
        enableSorting: false,
        size: 160,
      },
      {
        id: 'situacao',
        accessorKey: 'situacao',
        header: ({ column }) => <DataGridColumnHeader title="Situação" column={column} />,
        cell: ({ row }) => {
          const variant = SITUACAO_VARIANT[row.original.situacao] ?? 'secondary';
          return (
            <Badge variant={variant as SituacaoColor} appearance="outline" size="sm">
              {row.original.situacao_label}
            </Badge>
          );
        },
        enableSorting: false,
        size: 120,
      },
      {
        id: 'valor',
        accessorKey: 'valor',
        header: ({ column }) => <DataGridColumnHeader title="Total (R$)" column={column} />,
        cell: ({ row }) => {
          const isEntrada = row.original.tipo === 'entrada';
          return (
            <span
              className={cn(
                'font-semibold tabular-nums',
                isEntrada ? 'text-success' : 'text-destructive',
              )}
            >
              {isEntrada ? '+' : '-'}
              {fmtCurrency(row.original.valor)}
            </span>
          );
        },
        enableSorting: true,
        size: 130,
      },
      {
        id: 'valor_restante',
        accessorKey: 'valor_restante',
        header: ({ column }) => <DataGridColumnHeader title="Em aberto (R$)" column={column} />,
        cell: ({ row }) => {
          const v = row.original.valor_restante;
          return (
            <span className={cn('font-semibold tabular-nums', v <= 0 ? 'text-success' : '')}>
              {fmtCurrency(v)}
            </span>
          );
        },
        enableSorting: true,
        size: 130,
      },
      {
        id: 'numero_documento',
        accessorKey: 'numero_documento',
        header: ({ column }) => <DataGridColumnHeader title="Nº documento" column={column} />,
        cell: ({ row }) => <span className="truncate">{row.original.numero_documento ?? '—'}</span>,
        enableSorting: false,
        size: 130,
      },
      {
        id: 'categoria',
        accessorKey: 'categoria',
        header: ({ column }) => <DataGridColumnHeader title="Categoria" column={column} />,
        cell: ({ row }) => <span className="truncate">{row.original.categoria ?? '—'}</span>,
        enableSorting: false,
        size: 160,
      },
    ],
    [],
  );

  const [columnVisibility, setColumnVisibility] = useState<VisibilityState>({
    parceiro: false,
    numero_documento: false,
    categoria: false,
  });

  const table = useReactTable({
    columns,
    data,
    pageCount: meta.last_page,
    getRowId: (row) => row.id,
    state: { pagination, sorting, rowSelection, columnVisibility },
    columnResizeMode: 'onChange',
    onPaginationChange: setPagination,
    onSortingChange: setSorting,
    onColumnVisibilityChange: setColumnVisibility,
    enableRowSelection: true,
    onRowSelectionChange: setRowSelection,
    getCoreRowModel: getCoreRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
    getSortedRowModel: getSortedRowModel(),
    manualPagination: true,
    manualSorting: true,
  });

  // ── Linhas selecionadas (em todas as páginas) ─────────────────────────────
  const selectedIds = useMemo(
    () => Object.keys(rowSelection).filter((k) => rowSelection[k]),
    [rowSelection],
  );

  // Resolve os objetos selecionados unindo o cache (linhas vistas em páginas
  // anteriores) com a página atual. Garante que soma/diferença/ações sejam
  // coerentes mesmo após paginar ou aplicar filtros.
  const selectedRows = useMemo<ITransacao[]>(() => {
    const result: ITransacao[] = [];
    const seen = new Set<string>();
    data.forEach((row) => {
      if (rowSelection[row.id]) {
        result.push(row);
        seen.add(row.id);
      }
    });
    selectedRowsMapRef.current.forEach((row, id) => {
      if (rowSelection[id] && !seen.has(id)) result.push(row);
    });
    return result;
  }, [data, rowSelection]);

  const totalSelecionado = useMemo(
    () => selectedRows.reduce((sum, r) => sum + (r.valor_restante ?? r.valor ?? 0), 0),
    [selectedRows],
  );
  const diferenca = documentoValor - totalSelecionado;
  const podeBaixarTotal =
    selectedIds.length === 1 &&
    selectedRows.length === 1 &&
    Math.abs((selectedRows[0]?.valor_restante ?? 0) - documentoValor) < 0.01;

  // Quando há 2+ selecionados e a soma dos saldos restantes bate com o documento,
  // oferecer "Dar baixa em todos" (cada lançamento quita pelo seu próprio
  // valor restante — rateio implícito).
  const podeBaixarEmTodos =
    selectedIds.length > 1 &&
    selectedRows.length === selectedIds.length &&
    Math.abs(diferenca) < 0.01;

  // ── Anexar (apenas comprovante) ────────────────────────────────────────────
  const submit = useCallback(
    async (mode: AttachMode, extra: Record<string, unknown> = {}) => {
      if (!documentoId || selectedIds.length === 0 || saving) return;
      setSaving(true);
      try {
        const csrfEl = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]');
        const res = await fetch(
          `/financeiro/domusia/${documentoId}/anexar-lancamento`,
          {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': csrfEl?.content ?? '',
              Accept: 'application/json',
              'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
              transacao_ids: selectedIds.map((id) => Number(id)),
              modo: mode,
              ...extra,
            }),
          },
        );
        const json = await res.json().catch(() => ({}));
        if (!res.ok || json?.success === false) {
          throw new Error(json?.message ?? `HTTP ${res.status}`);
        }
        notify.success(json?.message ?? 'Documento anexado com sucesso');
        onAttached?.(selectedIds);
        onOpenChange(false);
      } catch (e) {
        notify.error('Erro ao anexar', (e as Error).message ?? '');
      } finally {
        setSaving(false);
      }
    },
    [documentoId, selectedIds, saving, onAttached, onOpenChange],
  );

  // ── Render ────────────────────────────────────────────────────────────────
  return (
    <>
      <Sheet open={open} onOpenChange={onOpenChange}>
        <SheetContent
          side="right"
          className="!max-w-none w-full sm:!max-w-[1800px] flex flex-col gap-0 p-0"
        >
          {/* Header ────────────────────────────────────────────────────────── */}
          <SheetHeader className="px-6 pt-6 pb-4 border-b border-border">
            <SheetTitle className="flex items-center gap-2 text-base">
              <Search className="size-4 text-primary" />
              Buscar lançamento
            </SheetTitle>
            <SheetDescription>
              Selecione um ou mais lançamentos para anexar este documento como
              comprovante. Quando você seleciona apenas um lançamento, é
              possível também registrar baixa total ou pagamento parcial.
            </SheetDescription>
          </SheetHeader>

          {/* Contexto do documento ──────────────────────────────────────────── */}
          <div className="px-6 py-3 bg-primary/5 border-b border-border flex items-center gap-3">
            <span className="flex size-8 items-center justify-center rounded-md bg-primary/10 text-primary shrink-0">
              <FileText className="size-4" />
            </span>
            <div className="min-w-0 flex-1">
              <div className="text-[11px] uppercase tracking-wide text-muted-foreground">
                Documento importado
              </div>
              <div className="text-sm font-semibold flex items-center gap-2 flex-wrap">
                <span className="text-primary">{fmtCurrency(documentoValor)}</span>
                {documentoDescricao && (
                  <>
                    <span className="text-muted-foreground/60">·</span>
                    <span className="text-foreground/90 truncate" title={documentoDescricao}>
                      {documentoDescricao}
                    </span>
                  </>
                )}
              </div>
            </div>
          </div>

          {/* Stats do filtro ─────────────────────────────────────────────────
              Mostra o impacto da filtragem (vencidos / a vencer / pagos no modo
              contas, ou receitas/despesas em aberto e realizadas no modo geral).
              Clicando em uma coluna filtra a tabela por aquele status. */}
          <SummaryStatsBar
            activeKey={activeStatKey}
            onTabClick={(key) => setActiveStatKey(key)}
            stats={statsItems}
          />

          {/* Filtros ───────────────────────────────────────────────────────── */}
          <TransacaoAdvancedFiltersScope
            value={advancedFilters}
            onChange={onAdvancedFiltersChange}
            tipoFormData={tipoFormData}
          >
            <div className="px-6 py-3 border-b border-border flex flex-wrap items-center gap-2">
              {/* ButtonGroup: Todo / Receitas / Despesas */}
              <ButtonGroup>
                <Button
                  variant="outline"
                  size="sm"
                  className={cn(
                    'gap-1.5',
                    tipo === 'all' && 'bg-primary text-primary-foreground hover:bg-primary/90 border-primary z-10',
                  )}
                  onClick={() => setTipo('all')}
                >
                  <LayoutList className="size-3.5" />
                  Todos
                </Button>
                <Button
                  variant="outline"
                  size="sm"
                  className={cn(
                    'gap-1.5',
                    tipo === 'entrada' && 'bg-primary text-primary-foreground hover:bg-primary/90 border-primary z-10',
                  )}
                  onClick={() => setTipo('entrada')}
                >
                  <ArrowUpCircle className="size-3.5 text-success" />
                  Receitas
                </Button>
                <Button
                  variant="outline"
                  size="sm"
                  className={cn(
                    'gap-1.5',
                    tipo === 'saida' && 'bg-primary text-primary-foreground hover:bg-primary/90 border-primary z-10',
                  )}
                  onClick={() => setTipo('saida')}
                >
                  <ArrowDownCircle className="size-3.5 text-destructive" />
                  Despesas
                </Button>
              </ButtonGroup>

              {/* Filtros de período + busca + filtros avançados */}
              <TransacaoFiltersRow
                period={period}
                onPeriodChange={(v) => {
                  setPeriod(v);
                  setPagination((p) => ({ ...p, pageIndex: 0 }));
                }}
                searchQuery={searchQuery}
                onSearchChange={(q) => {
                  setSearchQuery(q);
                  setPagination((p) => ({ ...p, pageIndex: 0 }));
                }}
                searchPlaceholder="Buscar lançamentos..."
                loading={loading}
                afterSearch={<TransacaoAdvancedFiltersTrigger />}
                extraBelow={<TransacaoAdvancedFiltersChipsSection />}
              />

              {/* Botão de atualizar */}
              <Button
                variant="outline"
                size="sm"
                className={cn(financeiroToolbarSoftBlueClass, 'h-10 px-3 ms-auto shrink-0')}
                onClick={refetch}
                disabled={loading}
                aria-label="Atualizar"
              >
                {loading ? (
                  <Loader2 className="size-4 animate-spin" />
                ) : (
                  <Search className="size-4" />
                )}
              </Button>
            </div>
          </TransacaoAdvancedFiltersScope>

          {/* Tabela ─────────────────────────────────────────────────────────── */}
          <div className="flex-1 overflow-hidden flex flex-col">
            {error && (
              <div className="px-6 py-2 text-sm text-destructive bg-destructive/10 border-b border-destructive/30">
                {error}
              </div>
            )}

            <DataGrid
              table={table}
              recordCount={meta.total}
              tableLayout={{
                columnsPinnable: true,
                columnsMovable: true,
                columnsVisibility: true,
                cellBorder: true,
                width: 'auto',
              }}
              isLoading={loading}
            >
              <Card className="border-0 rounded-none flex-1 flex flex-col">
                <CardTable className="flex-1 overflow-hidden">
                  <ScrollArea className="h-full">
                    {data.length === 0 && !loading ? (
                      <EmptyState />
                    ) : (
                      <DataGridTable />
                    )}
                    <ScrollBar orientation="horizontal" />
                  </ScrollArea>
                </CardTable>
                <CardFooter className="border-t border-border">
                  <DataGridPagination />
                </CardFooter>
              </Card>
            </DataGrid>
          </div>

          {/* Footer de ações ───────────────────────────────────────────────── */}
          <div className="border-t border-border bg-muted/20">
            {/* Resumo */}
            <div className="px-6 py-4 flex items-center justify-between flex-wrap gap-4 text-base">
              <div className="flex items-center gap-4 flex-wrap">
                <SummaryCell label="Documento" value={fmtCurrency(documentoValor)} />
                <SummaryCell
                  label={`Selecionados (${selectedIds.length})`}
                  value={fmtCurrency(totalSelecionado)}
                  muted={selectedIds.length === 0}
                />
                <SummaryCell
                  label="Diferença"
                  value={fmtCurrency(diferenca)}
                  tone={
                    selectedIds.length === 0
                      ? 'muted'
                      : Math.abs(diferenca) < 0.01
                        ? 'positive'
                        : 'warning'
                  }
                />
                {selectedIds.length === 1 && diferenca > 0.01 && (
                  <span className="inline-flex items-center gap-2 text-sm sm:text-base text-yellow-800 dark:text-yellow-400 max-w-xl leading-snug">
                    <Info className="size-4 shrink-0" />
                    Doc maior que o lançamento — anexe ou ajuste a seleção.
                  </span>
                )}
                {selectedIds.length === 1 && diferenca < -0.01 && (
                  <span className="inline-flex items-center gap-2 text-sm sm:text-base text-blue-800 dark:text-blue-300 max-w-xl leading-snug">
                    <Info className="size-4 shrink-0" />
                    Doc menor que o lançamento — você pode registrar pagamento parcial.
                  </span>
                )}
                {podeBaixarEmTodos && (
                  <span className="inline-flex items-center gap-2 text-sm sm:text-base text-emerald-700 dark:text-emerald-400 max-w-xl leading-snug">
                    <CheckCircle2 className="size-4 shrink-0" />
                    A soma dos selecionados bate com o documento — você pode dar baixa em todos.
                  </span>
                )}
              </div>
            </div>

            {/* Botões de ação */}
            <div className="px-6 py-4 flex flex-wrap items-center justify-end gap-3 border-t border-border/40">
              <Button variant="outline" size="md" className="text-base h-11 px-5" onClick={() => onOpenChange(false)} disabled={saving}>
                Cancelar
              </Button>

              {selectedIds.length === 0 && (
                <span className="text-sm sm:text-base text-muted-foreground italic">
                  Selecione ao menos um lançamento.
                </span>
              )}

              {selectedIds.length === 1 && (
                <div className="flex items-center">
                  {/* Botão principal: ação primária */}
                  <Button
                    variant="primary"
                    size="md"
                    className="gap-2 text-base h-11 px-5 font-semibold rounded-r-none border-r border-primary-foreground/20"
                    disabled={saving}
                    onClick={() => {
                      setPagamentoMode('pagamento_parcial');
                      setPagamentoOpen(true);
                    }}
                  >
                    {saving ? (
                      <Loader2 className="size-4 animate-spin" />
                    ) : (
                      <Wallet className="size-4" />
                    )}
                    Registrar pagamento
                  </Button>

                  {/* Seta do split button */}
                  <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                      <Button
                        variant="primary"
                        size="md"
                        mode="icon"
                        className="h-11 w-10 rounded-l-none border-l border-primary-foreground/20 shrink-0"
                        disabled={saving}
                        aria-label="Mais opções de ação"
                      >
                        <ChevronDown className="size-4" />
                      </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end" className="w-64">
                      <DropdownMenuGroup>
                        <DropdownMenuItem
                          onSelect={() => {
                            setPagamentoMode('pagamento_parcial');
                            setPagamentoOpen(true);
                          }}
                        >
                          <Wallet className="size-4" />
                          <div className="flex flex-col gap-0.5 min-w-0">
                            <span className="font-medium">Registrar pagamento parcial</span>
                            <span className="text-xs text-muted-foreground truncate">
                              Cria fracionamento via transacao_fracionamentos
                            </span>
                          </div>
                        </DropdownMenuItem>
                        <DropdownMenuItem
                          disabled={!podeBaixarTotal}
                          onSelect={() => {
                            setPagamentoMode('baixar_total');
                            setPagamentoOpen(true);
                          }}
                        >
                          <CheckCircle2 className="size-4 text-emerald-600" />
                          <div className="flex flex-col gap-0.5 min-w-0">
                            <span className="font-medium">Dar baixa total</span>
                            <span className="text-xs text-muted-foreground truncate">
                              {podeBaixarTotal
                                ? 'Quita o lançamento e cria movimentação'
                                : 'Disponível quando valores são iguais'}
                            </span>
                          </div>
                        </DropdownMenuItem>
                      </DropdownMenuGroup>
                      <DropdownMenuSeparator />
                      <DropdownMenuGroup>
                        <DropdownMenuItem onSelect={() => submit('anexar')}>
                          <Paperclip className="size-4" />
                          <div className="flex flex-col gap-0.5 min-w-0">
                            <span className="font-medium">Apenas anexar comprovante</span>
                            <span className="text-xs text-muted-foreground truncate">
                              Não altera valor nem situação do lançamento
                            </span>
                          </div>
                        </DropdownMenuItem>
                      </DropdownMenuGroup>
                    </DropdownMenuContent>
                  </DropdownMenu>
                </div>
              )}

              {selectedIds.length > 1 && !podeBaixarEmTodos && (
                <Button
                  variant="primary"
                  size="md"
                  className="gap-2 text-base h-11 px-5 font-semibold"
                  disabled={saving}
                  onClick={() => submit('anexar')}
                >
                  {saving ? <Loader2 className="size-4 animate-spin" /> : <Paperclip className="size-4" />}
                  Anexar em {selectedIds.length} lançamentos
                </Button>
              )}

              {/* Quando a soma dos selecionados bate exatamente com o documento,
                  oferecer baixa em massa (split button) — cada lançamento quita
                  pelo seu próprio valor restante (rateio implícito). */}
              {podeBaixarEmTodos && (
                <div className="flex items-center">
                  <Button
                    variant="primary"
                    size="md"
                    className="gap-2 text-base h-11 px-5 font-semibold rounded-r-none border-r border-primary-foreground/20"
                    disabled={saving}
                    onClick={() => setBaixarMassaOpen(true)}
                  >
                    {saving ? (
                      <Loader2 className="size-4 animate-spin" />
                    ) : (
                      <CheckCircle2 className="size-4" />
                    )}
                    Dar baixa em todos ({selectedIds.length})
                  </Button>
                  <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                      <Button
                        variant="primary"
                        size="md"
                        mode="icon"
                        className="h-11 w-10 rounded-l-none border-l border-primary-foreground/20 shrink-0"
                        disabled={saving}
                        aria-label="Mais opções de ação"
                      >
                        <ChevronDown className="size-4" />
                      </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end" className="w-72">
                      <DropdownMenuGroup>
                        <DropdownMenuItem onSelect={() => setBaixarMassaOpen(true)}>
                          <CheckCircle2 className="size-4 text-emerald-600" />
                          <div className="flex flex-col gap-0.5 min-w-0">
                            <span className="font-medium">Dar baixa em todos</span>
                            <span className="text-xs text-muted-foreground truncate">
                              Quita os {selectedIds.length} lançamentos selecionados
                            </span>
                          </div>
                        </DropdownMenuItem>
                        <DropdownMenuItem onSelect={() => submit('anexar')}>
                          <Paperclip className="size-4" />
                          <div className="flex flex-col gap-0.5 min-w-0">
                            <span className="font-medium">Apenas anexar comprovante</span>
                            <span className="text-xs text-muted-foreground truncate">
                              Não altera valor nem situação dos lançamentos
                            </span>
                          </div>
                        </DropdownMenuItem>
                      </DropdownMenuGroup>
                    </DropdownMenuContent>
                  </DropdownMenu>
                </div>
              )}
            </div>
          </div>
        </SheetContent>
      </Sheet>

      <RegistrarPagamentoDialog
        open={pagamentoOpen}
        onOpenChange={(o) => {
          if (!saving) setPagamentoOpen(o);
        }}
        mode={pagamentoMode}
        transacao={selectedRows[0] ?? null}
        documentoValor={documentoValor}
        saving={saving}
        onConfirm={async (payload) => {
          await submit(pagamentoMode, payload);
          setPagamentoOpen(false);
        }}
      />

      <BaixarEmMassaDialog
        open={baixarMassaOpen}
        onOpenChange={(o) => {
          if (!saving) setBaixarMassaOpen(o);
        }}
        transacoes={selectedRows}
        documentoValor={documentoValor}
        saving={saving}
        onConfirm={async (payload) => {
          await submit('baixar_total', payload);
          setBaixarMassaOpen(false);
        }}
      />
    </>
  );
}

// ─────────────────────────────────────────────────────────────────────────────
// Diálogo: Registrar pagamento (baixa total ou parcial)
// ─────────────────────────────────────────────────────────────────────────────

function RegistrarPagamentoDialog({
  open,
  onOpenChange,
  mode,
  transacao,
  documentoValor,
  saving,
  onConfirm,
}: {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  mode: Exclude<AttachMode, 'anexar'>;
  transacao: ITransacao | null;
  documentoValor: number;
  saving: boolean;
  onConfirm: (payload: Record<string, unknown>) => Promise<void> | void;
}) {
  const isParcial = mode === 'pagamento_parcial';
  const tipo: 'receita' | 'despesa' = transacao?.tipo === 'entrada' ? 'receita' : 'despesa';
  const { data: selectData } = useFormSelectData(tipo);

  const valorRestante = transacao?.valor_restante ?? transacao?.valor ?? 0;
  const valorInicial = isParcial
    ? Math.min(documentoValor, valorRestante)
    : valorRestante;

  const [valorPago, setValorPago] = useState(brCurrencyDigits(valorInicial));
  const [dataPagamento, setDataPagamento] = useState(isoToday());
  const [formaPagamento, setFormaPagamento] = useState('');
  const [contaPagamentoId, setContaPagamentoId] = useState<string>('');
  const [juros, setJuros] = useState('0,00');
  const [multa, setMulta] = useState('0,00');
  const [desconto, setDesconto] = useState('0,00');

  // Reset campos ao abrir/trocar transação.
  useEffect(() => {
    if (!open) return;
    setValorPago(brCurrencyDigits(valorInicial));
    setDataPagamento(isoToday());
    setFormaPagamento('');
    setContaPagamentoId('');
    setJuros('0,00');
    setMulta('0,00');
    setDesconto('0,00');
  }, [open, valorInicial]);

  const valorPagoNum = parseCurrencyBr(valorPago);
  const jurosNum = parseCurrencyBr(juros);
  const multaNum = parseCurrencyBr(multa);
  const descontoNum = parseCurrencyBr(desconto);

  const totalParaComparacao = valorPagoNum + jurosNum + multaNum;
  const valorEmAbertoApos = Math.max(0, valorRestante - totalParaComparacao);
  const valorExcedido = totalParaComparacao > valorRestante + 0.01;
  const valorMenorQueZero = valorPagoNum <= 0;

  const podeConfirmar = !valorExcedido && !valorMenorQueZero && !saving;

  const handleConfirm = useCallback(() => {
    if (!podeConfirmar) return;
    void onConfirm({
      valor_pago: valorPagoNum,
      data_pagamento: dataPagamento,
      forma_pagamento: formaPagamento || undefined,
      conta_pagamento_id: contaPagamentoId ? Number(contaPagamentoId) : undefined,
      juros: jurosNum || undefined,
      multa: multaNum || undefined,
      desconto: descontoNum || undefined,
    });
  }, [
    podeConfirmar,
    onConfirm,
    valorPagoNum,
    dataPagamento,
    formaPagamento,
    contaPagamentoId,
    jurosNum,
    multaNum,
    descontoNum,
  ]);

  if (!transacao) return null;

  return (
    <Dialog open={open} onOpenChange={onOpenChange}>
      <DialogContent className="max-w-xl">
        <DialogHeader>
          <DialogTitle className="flex items-center gap-2">
            {isParcial ? (
              <>
                <Wallet className="size-4 text-primary" />
                Registrar pagamento parcial
              </>
            ) : (
              <>
                <CheckCircle2 className="size-4 text-emerald-600" />
                Dar baixa total
              </>
            )}
          </DialogTitle>
          <DialogDescription>
            {isParcial
              ? 'Será criado um fracionamento do tipo "pago" e um saldo em aberto na transação.'
              : 'Será registrada a baixa total e criada a movimentação no saldo da conta.'}
          </DialogDescription>
        </DialogHeader>

        {/* Resumo do lançamento */}
        <div className="rounded-md border border-border bg-muted/30 p-3 space-y-1">
          <div className="text-[11px] uppercase tracking-wide text-muted-foreground">
            Lançamento selecionado
          </div>
          <div className="text-sm font-medium truncate">{transacao.descricao}</div>
          <div className="flex items-center justify-between text-xs">
            <span className="text-muted-foreground">Saldo em aberto</span>
            <span className="font-bold tabular-nums">{fmtCurrency(valorRestante)}</span>
          </div>
          <div className="flex items-center justify-between text-xs">
            <span className="text-muted-foreground">Valor do documento</span>
            <span className="font-bold tabular-nums text-primary">
              {fmtCurrency(documentoValor)}
            </span>
          </div>
        </div>

        {/* Campos */}
        <div className="grid grid-cols-2 gap-3">
          <div>
            <Label className="text-xs">Valor a registrar (R$)</Label>
            <CurrencyInput
              value={valorPago}
              onMaskedChange={setValorPago}
              className="h-9 mt-1"
              disabled={!isParcial}
            />
            {valorExcedido && (
              <div className="text-[11px] text-destructive mt-1 flex items-center gap-1">
                <AlertTriangle className="size-3" />
                Valor excede o saldo em aberto.
              </div>
            )}
          </div>

          <div>
            <Label className="text-xs">Data do pagamento</Label>
            <Input
              type="date"
              value={dataPagamento}
              onChange={(e) => setDataPagamento(e.target.value)}
              className="h-9 mt-1"
              max={isoToday()}
            />
          </div>

          <div>
            <Label className="text-xs">Forma de pagamento</Label>
            <Select value={formaPagamento} onValueChange={setFormaPagamento}>
              <SelectTrigger className="h-9 mt-1" size="sm">
                <SelectValue placeholder="Selecione..." />
              </SelectTrigger>
              <SelectContent>
                {selectData.formasPagamento.map((f) => (
                  <SelectItem key={f.id} value={f.nome}>
                    {f.nome}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>

          <div>
            <Label className="text-xs">Conta</Label>
            <Select value={contaPagamentoId} onValueChange={setContaPagamentoId}>
              <SelectTrigger className="h-9 mt-1" size="sm">
                <SelectValue placeholder="Selecione..." />
              </SelectTrigger>
              <SelectContent>
                {selectData.entidades.map((e) => (
                  <SelectItem key={e.id} value={String(e.id)}>
                    {e.label}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>

          <div>
            <Label className="text-xs">Juros (R$)</Label>
            <CurrencyInput value={juros} onMaskedChange={setJuros} className="h-9 mt-1" />
          </div>
          <div>
            <Label className="text-xs">Multa (R$)</Label>
            <CurrencyInput value={multa} onMaskedChange={setMulta} className="h-9 mt-1" />
          </div>
          <div className="col-span-2">
            <Label className="text-xs">Desconto (R$)</Label>
            <CurrencyInput value={desconto} onMaskedChange={setDesconto} className="h-9 mt-1" />
          </div>
        </div>

        {/* Resumo do impacto */}
        <div className="rounded-md border border-border bg-muted/20 p-3 text-xs space-y-1">
          <ResumoLinha label="Valor pago" value={fmtCurrency(valorPagoNum)} />
          {(jurosNum > 0 || multaNum > 0 || descontoNum > 0) && (
            <>
              <ResumoLinha label="(+) Juros" value={fmtCurrency(jurosNum)} muted />
              <ResumoLinha label="(+) Multa" value={fmtCurrency(multaNum)} muted />
              <ResumoLinha label="(−) Desconto" value={fmtCurrency(descontoNum)} muted />
            </>
          )}
          <div className="border-t border-border my-1" />
          <ResumoLinha
            label="Saldo em aberto após"
            value={fmtCurrency(valorEmAbertoApos)}
            tone={valorEmAbertoApos < 0.01 ? 'positive' : 'warning'}
            bold
          />
        </div>

        <DialogFooter>
          <Button variant="outline" onClick={() => onOpenChange(false)} disabled={saving}>
            Cancelar
          </Button>
          <Button
            variant={isParcial ? 'primary' : 'mono'}
            disabled={!podeConfirmar}
            onClick={handleConfirm}
            className="gap-1.5"
          >
            {saving ? (
              <Loader2 className="size-4 animate-spin" />
            ) : isParcial ? (
              <Wallet className="size-4" />
            ) : (
              <CheckCircle2 className="size-4" />
            )}
            {isParcial ? 'Confirmar pagamento parcial' : 'Confirmar baixa total'}
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  );
}

// ─────────────────────────────────────────────────────────────────────────────
// Diálogo: Dar baixa em todos (rateio implícito por valor restante)
// ─────────────────────────────────────────────────────────────────────────────

function BaixarEmMassaDialog({
  open,
  onOpenChange,
  transacoes,
  documentoValor,
  saving,
  onConfirm,
}: {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  transacoes: ITransacao[];
  documentoValor: number;
  saving: boolean;
  onConfirm: (payload: Record<string, unknown>) => Promise<void> | void;
}) {
  // Detecta o tipo predominante para carregar formas/contas adequadas.
  // Se houver mistura, prioriza "despesa" (caso comum: arquivos de boletos).
  const tipoPredominante: 'receita' | 'despesa' = useMemo(() => {
    if (!transacoes.length) return 'despesa';
    const entradas = transacoes.filter((t) => t.tipo === 'entrada').length;
    return entradas > transacoes.length / 2 ? 'receita' : 'despesa';
  }, [transacoes]);

  const { data: selectData } = useFormSelectData(tipoPredominante);

  const [dataPagamento, setDataPagamento] = useState(isoToday());
  const [formaPagamento, setFormaPagamento] = useState('');
  const [contaPagamentoId, setContaPagamentoId] = useState<string>('');

  useEffect(() => {
    if (!open) return;
    setDataPagamento(isoToday());
    setFormaPagamento('');
    setContaPagamentoId('');
  }, [open]);

  const totalBaixar = useMemo(
    () => transacoes.reduce((s, t) => s + (t.valor_restante ?? t.valor ?? 0), 0),
    [transacoes],
  );

  const possuiMistura = useMemo(() => {
    const tipos = new Set(transacoes.map((t) => t.tipo));
    return tipos.size > 1;
  }, [transacoes]);

  const handleConfirm = useCallback(() => {
    if (saving) return;
    void onConfirm({
      data_pagamento: dataPagamento,
      forma_pagamento: formaPagamento || undefined,
      conta_pagamento_id: contaPagamentoId ? Number(contaPagamentoId) : undefined,
    });
  }, [saving, onConfirm, dataPagamento, formaPagamento, contaPagamentoId]);

  if (!transacoes.length) return null;

  return (
    <Dialog open={open} onOpenChange={onOpenChange}>
      <DialogContent className="max-w-xl">
        <DialogHeader>
          <DialogTitle className="flex items-center gap-2">
            <CheckCircle2 className="size-4 text-emerald-600" />
            Dar baixa em todos os lançamentos
          </DialogTitle>
          <DialogDescription>
            Cada um dos {transacoes.length} lançamentos selecionados será quitado
            pelo seu próprio saldo em aberto. A movimentação será criada
            individualmente para cada baixa.
          </DialogDescription>
        </DialogHeader>

        {/* Resumo */}
        <div className="rounded-md border border-border bg-muted/30 p-3 space-y-2">
          <div className="flex items-center justify-between text-xs">
            <span className="text-muted-foreground">Total a baixar</span>
            <span className="font-bold tabular-nums">{fmtCurrency(totalBaixar)}</span>
          </div>
          <div className="flex items-center justify-between text-xs">
            <span className="text-muted-foreground">Valor do documento</span>
            <span className="font-bold tabular-nums text-primary">
              {fmtCurrency(documentoValor)}
            </span>
          </div>
          <div className="flex items-center justify-between text-xs">
            <span className="text-muted-foreground">Lançamentos selecionados</span>
            <Badge variant="secondary" appearance="outline" size="sm">
              {transacoes.length}
            </Badge>
          </div>
        </div>

        {/* Lista compacta dos lançamentos */}
        <div className="rounded-md border border-border max-h-48 overflow-y-auto divide-y divide-border">
          {transacoes.map((t) => (
            <div key={t.id} className="px-3 py-2 flex items-center justify-between gap-3 text-xs">
              <div className="min-w-0 flex-1">
                <div className="font-medium truncate">{t.descricao}</div>
                <div className="text-muted-foreground truncate">
                  Vence {t.vencimento ?? '—'} {t.parceiro ? `· ${t.parceiro}` : ''}
                </div>
              </div>
              <span
                className={cn(
                  'font-semibold tabular-nums whitespace-nowrap',
                  t.tipo === 'entrada' ? 'text-success' : 'text-destructive',
                )}
              >
                {fmtCurrency(t.valor_restante ?? t.valor ?? 0)}
              </span>
            </div>
          ))}
        </div>

        {possuiMistura && (
          <div className="flex items-start gap-2 rounded-md border border-yellow-500/30 bg-yellow-500/10 px-3 py-2 text-xs text-yellow-800 dark:text-yellow-300">
            <AlertTriangle className="size-4 mt-0.5 shrink-0" />
            <span>
              A seleção mistura receitas e despesas. As opções abaixo serão
              aplicadas a todas as baixas — revise antes de confirmar.
            </span>
          </div>
        )}

        {/* Campos comuns */}
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div>
            <Label className="text-xs">Data do pagamento</Label>
            <Input
              type="date"
              value={dataPagamento}
              onChange={(e) => setDataPagamento(e.target.value)}
              className="h-9 mt-1"
              max={isoToday()}
            />
          </div>
          <div>
            <Label className="text-xs">Forma de pagamento</Label>
            <Select value={formaPagamento} onValueChange={setFormaPagamento}>
              <SelectTrigger className="h-9 mt-1" size="sm">
                <SelectValue placeholder="Selecione..." />
              </SelectTrigger>
              <SelectContent>
                {selectData.formasPagamento.map((f) => (
                  <SelectItem key={f.id} value={f.nome}>
                    {f.nome}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>
          <div className="sm:col-span-2">
            <Label className="text-xs">Conta</Label>
            <Select value={contaPagamentoId} onValueChange={setContaPagamentoId}>
              <SelectTrigger className="h-9 mt-1" size="sm">
                <SelectValue placeholder="Selecione..." />
              </SelectTrigger>
              <SelectContent>
                {selectData.entidades.map((e) => (
                  <SelectItem key={e.id} value={String(e.id)}>
                    {e.label}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>
        </div>

        <DialogFooter>
          <Button variant="outline" onClick={() => onOpenChange(false)} disabled={saving}>
            Cancelar
          </Button>
          <Button
            variant="mono"
            disabled={saving}
            onClick={handleConfirm}
            className="gap-1.5"
          >
            {saving ? <Loader2 className="size-4 animate-spin" /> : <CheckCircle2 className="size-4" />}
            Confirmar baixa em {transacoes.length} lançamentos
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  );
}

// ─────────────────────────────────────────────────────────────────────────────
// Auxiliares de UI
// ─────────────────────────────────────────────────────────────────────────────

function SummaryCell({
  label,
  value,
  muted,
  tone,
}: {
  label: string;
  value: string;
  muted?: boolean;
  tone?: 'positive' | 'warning' | 'muted';
}) {
  return (
    <div className="flex flex-col gap-0.5 sm:flex-row sm:items-baseline sm:gap-2">
      <span className="text-sm sm:text-base font-medium text-muted-foreground">{label}:</span>
      <span
        className={cn(
          'text-lg sm:text-xl font-bold tabular-nums tracking-tight',
          muted && 'text-muted-foreground font-semibold',
          tone === 'positive' && 'text-success',
          tone === 'warning' && 'text-yellow-700 dark:text-yellow-400',
          tone === 'muted' && 'text-muted-foreground',
        )}
      >
        {value}
      </span>
    </div>
  );
}

function ResumoLinha({
  label,
  value,
  bold,
  muted,
  tone,
}: {
  label: string;
  value: string;
  bold?: boolean;
  muted?: boolean;
  tone?: 'positive' | 'warning';
}) {
  return (
    <div className="flex items-center justify-between">
      <span className={cn('text-muted-foreground', muted && 'opacity-70')}>{label}</span>
      <span
        className={cn(
          'tabular-nums',
          bold && 'font-bold',
          tone === 'positive' && 'text-success',
          tone === 'warning' && 'text-yellow-700 dark:text-yellow-500',
        )}
      >
        {value}
      </span>
    </div>
  );
}

function EmptyState() {
  return (
    <div className="flex flex-col items-center justify-center text-center py-16 gap-3">
      <Inbox className="size-10 text-muted-foreground/30" />
      <div className="text-sm font-medium text-muted-foreground">
        Nenhum lançamento encontrado
      </div>
      <p className="text-xs text-muted-foreground/70 max-w-sm">
        Ajuste os filtros (período, tipo, situação ou texto de busca) ou crie um
        novo lançamento a partir do documento.
      </p>
    </div>
  );
}
