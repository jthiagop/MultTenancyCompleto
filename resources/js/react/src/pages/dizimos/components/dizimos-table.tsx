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
} from '@tanstack/react-table';
import {
  CalendarDays,
  Coins,
  CreditCard,
  EllipsisVertical,
  HeartHandshake,
  Loader2,
  Pencil,
  RefreshCw,
  Search,
  Trash2,
  UserRound,
  Wallet,
  X,
} from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
  Card,
  CardFooter,
  CardHeader,
  CardTable,
  CardToolbar,
} from '@/components/ui/card';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import { DataGrid } from '@/components/ui/data-grid';
import { DataGridColumnHeader } from '@/components/ui/data-grid-column-header';
import { DataGridPagination } from '@/components/ui/data-grid-pagination';
import {
  DataGridTable,
  DataGridTableRowSelect,
  DataGridTableRowSelectAll,
} from '@/components/ui/data-grid-table';
import { ScrollArea, ScrollBar } from '@/components/ui/scroll-area';
import { DatePicker } from '@/components/ui/date-picker';
import { useDizimos, HttpError, type DizimoTipo, type IDizimo } from '@/hooks/useDizimos';
import { cn } from '@/lib/utils';

// ── Mapeamentos visuais ─────────────────────────────────────────────────────

const TIPO_VARIANT: Record<DizimoTipo, 'success' | 'info' | 'warning' | 'secondary'> = {
  Dízimo: 'success',
  Doação: 'info',
  Oferta: 'warning',
  Outro: 'secondary',
};

const TIPO_FILTER_OPTIONS: Array<{ value: 'todos' | DizimoTipo; label: string }> = [
  { value: 'todos', label: 'Todos os tipos' },
  { value: 'Dízimo', label: 'Dízimo' },
  { value: 'Doação', label: 'Doação' },
  { value: 'Oferta', label: 'Oferta' },
  { value: 'Outro', label: 'Outro' },
];

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
  const [searchQuery, setSearchQuery] = useState('');
  const [tipoFilter, setTipoFilter] = useState<'todos' | DizimoTipo>('todos');
  const [dataInicio, setDataInicio] = useState<string>('');
  const [dataFim, setDataFim] = useState<string>('');

  const sortCol = sorting[0];

  const { data, pagination: meta, stats, loading, error, refetch } = useDizimos({
    search: searchQuery || undefined,
    page: pagination.pageIndex + 1,
    perPage: pagination.pageSize,
    sortBy: sortCol?.id,
    sortDir: sortCol?.desc ? 'desc' : 'asc',
    tipo: tipoFilter === 'todos' ? undefined : [tipoFilter],
    dataInicio: dataInicio || undefined,
    dataFim: dataFim || undefined,
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
        cell: ({ row }) => (
          <span className="text-sm">{row.original.forma_pagamento ?? '—'}</span>
        ),
        enableSorting: true,
        size: 150,
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
    state: { pagination, sorting, rowSelection },
    columnResizeMode: 'onChange',
    onPaginationChange: setPagination,
    onSortingChange: setSorting,
    enableRowSelection: true,
    onRowSelectionChange: setRowSelection,
    getCoreRowModel: getCoreRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
    getSortedRowModel: getSortedRowModel(),
    manualPagination: true,
    manualSorting: true,
  });

  const handleClearSearch = useCallback(() => {
    setSearchQuery('');
    setPagination((p) => ({ ...p, pageIndex: 0 }));
  }, []);

  const handleClearDates = useCallback(() => {
    setDataInicio('');
    setDataFim('');
    setPagination((p) => ({ ...p, pageIndex: 0 }));
  }, []);

  const fmt = (n: number) =>
    n.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });

  return (
    <DataGrid
      table={table}
      recordCount={meta.total}
      tableLayout={{ columnsPinnable: true, columnsMovable: false, columnsVisibility: false, cellBorder: true, width: 'auto' }}
    >
      <Card>
        {/* Resumo de totais (do período filtrado) */}
        <div className="grid grid-cols-2 sm:grid-cols-4 gap-px bg-border border-b">
          <StatCell label="Total" value={fmt(stats.total)} accent="text-foreground" />
          <StatCell label="Dízimo" value={fmt(stats.dizimo)} accent="text-emerald-600" />
          <StatCell label="Doação" value={fmt(stats.doacao)} accent="text-sky-600" />
          <StatCell label="Oferta" value={fmt(stats.oferta)} accent="text-amber-600" />
        </div>

        <CardHeader className="flex flex-col gap-3 py-4 min-h-0">
          <div className="flex flex-wrap items-center gap-2">
            {/* Busca */}
            <div className="relative shrink-0 w-60 min-w-[200px]">
              <Search className="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground pointer-events-none" />
              <Input
                className="ps-9 pe-9 h-9"
                placeholder="Buscar por fiel ou observação..."
                value={searchQuery}
                onChange={(e) => {
                  setSearchQuery(e.target.value);
                  setPagination((p) => ({ ...p, pageIndex: 0 }));
                }}
              />
              {searchQuery && (
                <button
                  type="button"
                  onClick={handleClearSearch}
                  className="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                  aria-label="Limpar busca"
                >
                  <X className="size-3.5" />
                </button>
              )}
            </div>

            {/* Tipo */}
            <Select
              value={tipoFilter}
              onValueChange={(v) => {
                setTipoFilter(v as 'todos' | DizimoTipo);
                setPagination((p) => ({ ...p, pageIndex: 0 }));
              }}
            >
              <SelectTrigger className="h-9 w-44">
                <SelectValue />
              </SelectTrigger>
              <SelectContent>
                {TIPO_FILTER_OPTIONS.map((opt) => (
                  <SelectItem key={opt.value} value={opt.value}>
                    {opt.label}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>

            {/* Datas */}
            <div className="flex items-center gap-2">
              <DatePicker
                value={dataInicio}
                onChange={(iso) => {
                  setDataInicio(iso);
                  setPagination((p) => ({ ...p, pageIndex: 0 }));
                }}
                placeholder="Data inicial"
                size="sm"
              />
              <span className="text-xs text-muted-foreground">até</span>
              <DatePicker
                value={dataFim}
                onChange={(iso) => {
                  setDataFim(iso);
                  setPagination((p) => ({ ...p, pageIndex: 0 }));
                }}
                placeholder="Data final"
                size="sm"
              />
              {(dataInicio || dataFim) && (
                <Button
                  variant="ghost"
                  size="sm"
                  type="button"
                  onClick={handleClearDates}
                  className="h-9 px-2"
                  aria-label="Limpar datas"
                >
                  <X className="size-3.5" />
                </Button>
              )}
            </div>

            <CardToolbar className="ms-auto">
              <Button
                variant="outline"
                size="sm"
                className="h-9 min-w-9 px-3"
                type="button"
                onClick={refetch}
                disabled={loading}
                aria-label="Atualizar"
              >
                {loading ? (
                  <Loader2 className="size-4 animate-spin" />
                ) : (
                  <RefreshCw className="size-4" />
                )}
              </Button>
            </CardToolbar>
          </div>
        </CardHeader>

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

// ── Subcomponentes ──────────────────────────────────────────────────────────

function StatCell({
  label,
  value,
  accent,
}: {
  label: string;
  value: string;
  accent?: string;
}) {
  return (
    <div className="bg-card px-4 py-3 flex flex-col gap-0.5">
      <span className="text-xs uppercase tracking-wide text-muted-foreground">{label}</span>
      <span className={cn('text-base font-semibold tabular-nums', accent)}>{value}</span>
    </div>
  );
}
