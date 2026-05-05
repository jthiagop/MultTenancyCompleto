import { useCallback, useEffect, useMemo, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import {
  ColumnDef,
  getCoreRowModel,
  getPaginationRowModel,
  getSortedRowModel,
  PaginationState,
  RowSelectionState,
  SortingState,
  useReactTable,
  VisibilityState,
} from '@tanstack/react-table';
import {
  CalendarDays,
  ChevronDown,
  Coins,
  CreditCard,
  EllipsisVertical,
  HeartHandshake,
  Loader2,
  Pencil,
  Trash2,
  UserRound,
  Users,
  Wallet,
  X,
} from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
  Card,
  CardFooter,
  CardTable,
} from '@/components/ui/card';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { ScrollArea, ScrollBar } from '@/components/ui/scroll-area';
import { DataGrid } from '@/components/ui/data-grid';
import { DataGridColumnHeader } from '@/components/ui/data-grid-column-header';
import { DataGridPagination } from '@/components/ui/data-grid-pagination';
import {
  DataGridTable,
  DataGridTableRowSelect,
  DataGridTableRowSelectAll,
} from '@/components/ui/data-grid-table';
import { defaultPeriod, type PeriodValue } from '@/components/ui/period-picker';
import { SummaryStatsBar } from '@/pages/financeiro/components/summary-stats-bar';
import { FinanceiroTransacaoTableCardHeader } from '@/pages/financeiro/components/financeiro-transacao-table-card-header';
import {
  financeiroToolbarSoftBlueClass,
  financeiroToolbarSoftBlueInputClass,
} from '@/lib/financeiro-toolbar-accent';
import { useDizimos, HttpError, type DizimoTipo, type IDizimo } from '@/hooks/useDizimos';
import { useFielSearch } from '@/pages/dizimos/components/use-fiel-search';
import { cn } from '@/lib/utils';

// ── Mapeamentos visuais ─────────────────────────────────────────────────────

const TIPO_VARIANT: Record<DizimoTipo, 'success' | 'info' | 'warning' | 'secondary'> = {
  Dízimo: 'success',
  Doação: 'info',
  Oferta: 'warning',
  Outro: 'secondary',
};

// ── Célula: avatar + nome ────────────────────────────────────────────────────

function FielCell({ fiel }: { fiel: IDizimo['fiel'] }) {
  if (!fiel) return <span className="text-muted-foreground text-sm">—</span>;
  const initials = fiel.nome_completo
    .trim()
    .split(/\s+/)
    .slice(0, 2)
    .map((p) => p.charAt(0).toUpperCase())
    .join('');
  return (
    <div className="flex items-center gap-3 min-w-0">
      <div className="relative size-8 shrink-0 overflow-hidden rounded-full bg-muted">
        {fiel.avatar_url ? (
          <img src={fiel.avatar_url} alt={fiel.nome_completo} className="size-full object-cover" loading="lazy" />
        ) : (
          <span className="flex size-full items-center justify-center text-xs font-semibold uppercase text-muted-foreground">
            {initials || '?'}
          </span>
        )}
      </div>
      <span className="truncate text-sm font-medium">{fiel.nome_completo}</span>
    </div>
  );
}

// ── FielFilterPopover ────────────────────────────────────────────────────────

function FielFilterPopover({
  fielId,
  fielNome,
  onSelect,
  onClear,
}: {
  fielId: number | null;
  fielNome: string;
  onSelect: (id: number, nome: string) => void;
  onClear: () => void;
}) {
  const [open, setOpen] = useState(false);
  const [query, setQuery] = useState('');
  const { options, loading } = useFielSearch(query);

  const handleSelect = (id: number, nome: string) => {
    onSelect(id, nome);
    setOpen(false);
    setQuery('');
  };

  return (
    <Popover open={open} onOpenChange={(v) => { setOpen(v); if (!v) setQuery(''); }}>
      <PopoverTrigger asChild>
        <Button
          type="button"
          variant="outline"
          size="sm"
          className={cn('h-10 shrink-0 gap-1.5 rounded-md px-3 text-sm', financeiroToolbarSoftBlueClass)}
        >
          <Users className="size-3.5 shrink-0" />
          {fielId ? (
            <span className="max-w-[120px] truncate">{fielNome}</span>
          ) : (
            'Fiel'
          )}
          {fielId ? (
            <span
              role="button"
              className="ml-0.5 opacity-60 hover:opacity-100"
              onClick={(e) => { e.stopPropagation(); onClear(); }}
              aria-label="Limpar filtro de fiel"
            >
              <X className="size-3" />
            </span>
          ) : (
            <ChevronDown className="size-3 opacity-50" />
          )}
        </Button>
      </PopoverTrigger>
      <PopoverContent className="w-72 p-2" align="start">
        <div className="relative mb-2">
          <Input
            placeholder="Buscar fiel..."
            value={query}
            onChange={(e) => setQuery(e.target.value)}
            className={cn(financeiroToolbarSoftBlueInputClass, 'h-9 ps-3')}
            autoFocus
          />
        </div>
        {loading && (
          <div className="flex items-center gap-2 px-2 py-4 text-sm text-muted-foreground">
            <Loader2 className="size-4 animate-spin" /> Buscando...
          </div>
        )}
        {!loading && query.length >= 2 && options.length === 0 && (
          <p className="px-2 py-4 text-sm text-muted-foreground text-center">Nenhum fiel encontrado.</p>
        )}
        {!loading && query.length < 2 && (
          <p className="px-2 py-2 text-xs text-muted-foreground">
            Digite ao menos 2 caracteres para buscar.
          </p>
        )}
        <div className="max-h-52 overflow-y-auto">
          {options.map((f) => (
            <button
              key={f.id}
              type="button"
              className="flex w-full items-center gap-2 rounded px-2 py-1.5 text-sm hover:bg-accent text-left"
              onClick={() => handleSelect(f.id, f.nome_completo)}
            >
              <div className="size-7 shrink-0 rounded-full bg-muted overflow-hidden flex items-center justify-center">
                {f.avatar_url ? (
                  <img src={f.avatar_url} alt={f.nome_completo} className="size-full object-cover" />
                ) : (
                  <span className="text-[10px] font-semibold uppercase text-muted-foreground">
                    {f.nome_completo.charAt(0)}
                  </span>
                )}
              </div>
              <span className="truncate">{f.nome_completo}</span>
            </button>
          ))}
        </div>
      </PopoverContent>
    </Popover>
  );
}

// ── ValorRangePopover ────────────────────────────────────────────────────────

function ValorRangePopover({
  valorMin,
  valorMax,
  onApply,
  onClear,
}: {
  valorMin: string;
  valorMax: string;
  onApply: (min: string, max: string) => void;
  onClear: () => void;
}) {
  const [open, setOpen] = useState(false);
  const [localMin, setLocalMin] = useState(valorMin);
  const [localMax, setLocalMax] = useState(valorMax);
  const hasFilter = !!valorMin || !!valorMax;

  useEffect(() => {
    if (open) {
      setLocalMin(valorMin);
      setLocalMax(valorMax);
    }
  }, [open, valorMin, valorMax]);

  const label = hasFilter
    ? valorMin && valorMax
      ? `R$ ${valorMin} – ${valorMax}`
      : valorMin
        ? `≥ R$ ${valorMin}`
        : `≤ R$ ${valorMax}`
    : 'Valor';

  return (
    <Popover open={open} onOpenChange={setOpen}>
      <PopoverTrigger asChild>
        <Button
          type="button"
          variant="outline"
          size="sm"
          className={cn('h-10 shrink-0 gap-1.5 rounded-md px-3 text-sm', financeiroToolbarSoftBlueClass)}
        >
          <Coins className="size-3.5 shrink-0" />
          <span className={cn('truncate', hasFilter ? 'max-w-[120px]' : '')}>{label}</span>
          {hasFilter ? (
            <span
              role="button"
              className="ml-0.5 opacity-60 hover:opacity-100"
              onClick={(e) => { e.stopPropagation(); onClear(); }}
              aria-label="Limpar filtro de valor"
            >
              <X className="size-3" />
            </span>
          ) : (
            <ChevronDown className="size-3 opacity-50" />
          )}
        </Button>
      </PopoverTrigger>
      <PopoverContent className="w-56 p-3" align="start">
        <p className="text-xs font-medium text-muted-foreground mb-3">Filtrar por valor (R$)</p>
        <div className="space-y-2">
          <div>
            <label className="text-xs text-muted-foreground mb-1 block">Mínimo</label>
            <Input
              type="number"
              min="0"
              step="0.01"
              placeholder="0,00"
              value={localMin}
              onChange={(e) => setLocalMin(e.target.value)}
              className={cn(financeiroToolbarSoftBlueInputClass, 'h-9')}
            />
          </div>
          <div>
            <label className="text-xs text-muted-foreground mb-1 block">Máximo</label>
            <Input
              type="number"
              min="0"
              step="0.01"
              placeholder="0,00"
              value={localMax}
              onChange={(e) => setLocalMax(e.target.value)}
              className={cn(financeiroToolbarSoftBlueInputClass, 'h-9')}
            />
          </div>
          <div className="flex gap-2 pt-1">
            <Button
              type="button"
              size="sm"
              className="flex-1 h-8"
              onClick={() => { onApply(localMin, localMax); setOpen(false); }}
            >
              Aplicar
            </Button>
            <Button
              type="button"
              variant="ghost"
              size="sm"
              className="h-8"
              onClick={() => { onClear(); setOpen(false); }}
            >
              Limpar
            </Button>
          </div>
        </div>
      </PopoverContent>
    </Popover>
  );
}

// ── Componente principal ────────────────────────────────────────────────────

export interface DizimosTableProps {
  refreshKey?: number;
  onEdit?: (id: number) => void;
  onDelete?: (dizimo: IDizimo) => void;
  canEdit?: boolean;
  canDelete?: boolean;
}

export function DizimosTable({
  refreshKey,
  onEdit,
  onDelete,
  canEdit = true,
  canDelete = true,
}: DizimosTableProps) {
  const navigate = useNavigate();

  const [pagination, setPagination] = useState<PaginationState>({ pageIndex: 0, pageSize: 20 });
  const [sorting, setSorting] = useState<SortingState>([{ id: 'data_pagamento', desc: true }]);
  const [rowSelection, setRowSelection] = useState<RowSelectionState>({});
  const [columnVisibility, setColumnVisibility] = useState<VisibilityState>({});

  const [searchQuery, setSearchQuery] = useState('');
  const [activeStatKey, setActiveStatKey] = useState<string>('total');
  const [period, setPeriod] = useState<PeriodValue>(defaultPeriod);

  // Filtros extras
  const [fielId, setFielId] = useState<number | null>(null);
  const [fielNome, setFielNome] = useState('');
  const [valorMin, setValorMin] = useState('');
  const [valorMax, setValorMax] = useState('');

  const sortCol = sorting[0];

  const { data, pagination: meta, stats, loading, error, refetch } = useDizimos({
    search: searchQuery || undefined,
    page: pagination.pageIndex + 1,
    perPage: pagination.pageSize,
    sortBy: sortCol?.id,
    sortDir: sortCol?.desc ? 'desc' : 'asc',
    tipo: activeStatKey !== 'total' ? [activeStatKey as DizimoTipo] : undefined,
    dataInicio: period.startDate,
    dataFim: period.endDate,
    fielId: fielId ?? undefined,
    valorMin: valorMin ? parseFloat(valorMin) : undefined,
    valorMax: valorMax ? parseFloat(valorMax) : undefined,
  });

  useEffect(() => {
    if (refreshKey) refetch();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [refreshKey]);

  useEffect(() => {
    if (error instanceof HttpError && error.status === 403) {
      navigate('/dashboard', { replace: true });
    }
  }, [error, navigate]);

  // ── Handlers ──────────────────────────────────────────────────────────────

  const handleFielSelect = useCallback((id: number, nome: string) => {
    setFielId(id);
    setFielNome(nome);
    setPagination((p) => ({ ...p, pageIndex: 0 }));
  }, []);

  const handleFielClear = useCallback(() => {
    setFielId(null);
    setFielNome('');
    setPagination((p) => ({ ...p, pageIndex: 0 }));
  }, []);

  const handleValorApply = useCallback((min: string, max: string) => {
    setValorMin(min);
    setValorMax(max);
    setPagination((p) => ({ ...p, pageIndex: 0 }));
  }, []);

  const handleValorClear = useCallback(() => {
    setValorMin('');
    setValorMax('');
    setPagination((p) => ({ ...p, pageIndex: 0 }));
  }, []);

  // ── Colunas ────────────────────────────────────────────────────────────────

  const columns = useMemo<ColumnDef<IDizimo>[]>(
    () => [
      {
        accessorKey: 'id',
        header: () => <DataGridTableRowSelectAll />,
        cell: ({ row }) => <DataGridTableRowSelect row={row} />,
        enableSorting: false,
        enableHiding: false,
        size: 51,
      },
      {
        id: 'data_pagamento',
        accessorKey: 'data_pagamento',
        header: ({ column }) => (
          <DataGridColumnHeader
            title="Data"
            column={column}
            icon={<CalendarDays className="size-3.5 shrink-0 opacity-60" aria-hidden />}
          />
        ),
        cell: ({ row }) => (
          <span className="text-sm tabular-nums">{row.original.data_pagamento_formatada ?? '—'}</span>
        ),
        enableSorting: true,
        size: 110,
      },
      {
        id: 'fiel',
        header: ({ column }) => (
          <DataGridColumnHeader
            title="Fiel"
            column={column}
            icon={<UserRound className="size-3.5 shrink-0 opacity-60" aria-hidden />}
          />
        ),
        cell: ({ row }) => <FielCell fiel={row.original.fiel} />,
        enableSorting: false,
        size: 260,
      },
      {
        id: 'tipo',
        accessorKey: 'tipo',
        header: ({ column }) => (
          <DataGridColumnHeader
            title="Tipo"
            column={column}
            icon={<HeartHandshake className="size-3.5 shrink-0 opacity-60" aria-hidden />}
          />
        ),
        cell: ({ row }) => (
          <Badge variant={TIPO_VARIANT[row.original.tipo] ?? 'secondary'} appearance="outline" size="sm">
            {row.original.tipo}
          </Badge>
        ),
        enableSorting: true,
        size: 110,
      },
      {
        id: 'valor',
        accessorKey: 'valor',
        header: ({ column }) => (
          <DataGridColumnHeader
            title="Valor"
            column={column}
            icon={<Coins className="size-3.5 shrink-0 opacity-60" aria-hidden />}
          />
        ),
        cell: ({ row }) => (
          <span className="text-sm tabular-nums font-medium">{row.original.valor_formatado}</span>
        ),
        enableSorting: true,
        size: 130,
      },
      {
        id: 'forma_pagamento',
        accessorKey: 'forma_pagamento',
        header: ({ column }) => (
          <DataGridColumnHeader
            title="Forma"
            column={column}
            icon={<CreditCard className="size-3.5 shrink-0 opacity-60" aria-hidden />}
          />
        ),
        cell: ({ row }) => {
          const doc = row.original.numero_documento;
          return (
            <div className="flex flex-col gap-0.5 min-w-0">
              <span className="truncate text-sm">{row.original.forma_pagamento ?? '—'}</span>
              {doc && (
                <span
                  className="truncate text-[10px] uppercase tracking-wide text-muted-foreground"
                  title={`Documento: ${doc}`}
                >
                  Doc.&nbsp;{doc}
                </span>
              )}
            </div>
          );
        },
        enableSorting: true,
        size: 170,
      },
      {
        id: 'entidade',
        header: ({ column }) => (
          <DataGridColumnHeader
            title="Conta / Caixa"
            column={column}
            icon={<Wallet className="size-3.5 shrink-0 opacity-60" aria-hidden />}
          />
        ),
        cell: ({ row }) => {
          const ent = row.original.entidade_financeira;
          if (!ent) return <span className="text-muted-foreground text-sm">—</span>;
          return (
            <div className="flex flex-col gap-0.5 min-w-0">
              <span className="truncate text-sm">{ent.nome}</span>
              <span className="truncate text-[10px] uppercase tracking-wide text-muted-foreground">
                {ent.tipo}
              </span>
            </div>
          );
        },
        enableSorting: false,
        size: 180,
      },
      {
        id: 'integrado',
        accessorKey: 'integrado_financeiro',
        header: ({ column }) => <DataGridColumnHeader title="Integrado" column={column} />,
        cell: ({ row }) =>
          row.original.integrado_financeiro ? (
            <Badge variant="success" appearance="outline" size="sm">Sim</Badge>
          ) : (
            <Badge variant="secondary" appearance="outline" size="sm">Não</Badge>
          ),
        enableSorting: false,
        size: 110,
      },
      {
        id: 'actions',
        header: () => (
          <span className="inline-flex h-full items-center gap-1.5 text-[0.8125rem] font-normal leading-[calc(1.125/0.8125)] text-muted-foreground">
            <EllipsisVertical className="size-3.5 shrink-0 opacity-60" aria-hidden />
            Ações
          </span>
        ),
        cell: ({ row }) => (
          <DropdownMenu>
            <DropdownMenuTrigger asChild>
              <Button variant="ghost" size="sm" className="size-8 p-0 data-[state=open]:bg-accent">
                <EllipsisVertical className="size-4" />
                <span className="sr-only">Ações</span>
              </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end" className="w-44">
              <DropdownMenuItem
                onClick={() => onEdit?.(row.original.id)}
                disabled={!canEdit}
              >
                <Pencil className="size-3.5" />
                Editar
              </DropdownMenuItem>
              <DropdownMenuSeparator />
              <DropdownMenuItem
                onClick={() => onDelete?.(row.original)}
                disabled={!canDelete}
                className="text-destructive focus:text-destructive"
              >
                <Trash2 className="size-3.5" />
                Excluir
              </DropdownMenuItem>
            </DropdownMenuContent>
          </DropdownMenu>
        ),
        enableSorting: false,
        enableHiding: false,
        size: 60,
      },
    ],
    [onEdit, onDelete, canEdit, canDelete],
  );

  const table = useReactTable({
    columns,
    data,
    pageCount: meta.last_page,
    getRowId: (row) => String(row.id),
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

  return (
    <DataGrid
      table={table}
      recordCount={meta.total}
      tableLayout={{ columnsPinnable: true, columnsMovable: true, columnsVisibility: true, cellBorder: true, width: 'auto' }}
    >
      <Card>
        {/* ── Tabs de totais por tipo ─────────────────────────────────────── */}
        <SummaryStatsBar
          activeKey={activeStatKey}
          onTabClick={(key) => {
            setActiveStatKey(key);
            setPagination((p) => ({ ...p, pageIndex: 0 }));
          }}
          stats={[
            { statKey: 'Dízimo', label: 'Dízimo (R$)',  value: stats.dizimo,  colorClass: 'text-emerald-600', accentColor: '#059669' },
            { statKey: 'Doação', label: 'Doação (R$)',  value: stats.doacao,  colorClass: 'text-sky-600',     accentColor: '#0284c7' },
            { statKey: 'Oferta', label: 'Oferta (R$)',  value: stats.oferta,  colorClass: 'text-amber-600',   accentColor: '#d97706' },
            { statKey: 'total',  label: 'Total (R$)',   value: stats.total,   colorClass: 'text-blue-600',    accentColor: '#2563eb' },
          ]}
        />

        {/* ── Cabeçalho: período + busca + filtros extras + toolbar ───────── */}
        <FinanceiroTransacaoTableCardHeader
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
          searchPlaceholder="Buscar por fiel ou observação..."
          loading={loading}
          refetch={refetch}
          afterSearch={
            <>
              <FielFilterPopover
                fielId={fielId}
                fielNome={fielNome}
                onSelect={handleFielSelect}
                onClear={handleFielClear}
              />
              <ValorRangePopover
                valorMin={valorMin}
                valorMax={valorMax}
                onApply={handleValorApply}
                onClear={handleValorClear}
              />
            </>
          }
        />

        {error && !(error instanceof HttpError && error.status === 403) && (
          <div className="px-4 py-2 text-sm text-destructive bg-destructive/10">
            {error.message}
          </div>
        )}

        <CardTable>
          <ScrollArea>
            <DataGridTable />
            <ScrollBar orientation="horizontal" />
          </ScrollArea>
        </CardTable>
        <CardFooter>
          <DataGridPagination />
        </CardFooter>
      </Card>
    </DataGrid>
  );
}
