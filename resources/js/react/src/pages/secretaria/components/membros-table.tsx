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
import { EllipsisVertical, Loader2, RefreshCw, Search, X } from 'lucide-react';
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
import { DataGrid } from '@/components/ui/data-grid';
import { DataGridColumnHeader } from '@/components/ui/data-grid-column-header';
import { DataGridPagination } from '@/components/ui/data-grid-pagination';
import {
  DataGridTable,
  DataGridTableRowSelect,
  DataGridTableRowSelectAll,
} from '@/components/ui/data-grid-table';
import { ScrollArea, ScrollBar } from '@/components/ui/scroll-area';
import { Tabs, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { HttpError, type IMembro, type MembrosTab, useMembros } from '@/hooks/useMembros';

// ── Variantes de função → badge ───────────────────────────────────────────────

const ROLE_VARIANT: Record<string, 'success' | 'warning' | 'primary' | 'secondary'> = {
  presbitero: 'success',
  diacono:    'warning',
  irmao:      'primary',
};

// ── Célula de nome + avatar ───────────────────────────────────────────────────

function MembroCell({ row }: { row: { original: IMembro } }) {
  const { name, avatar_url, role, current_stage } = row.original;
  return (
    <div className="flex items-center gap-3">
      <div className="relative size-9 shrink-0 overflow-hidden rounded-full bg-muted">
        {avatar_url ? (
          <img src={avatar_url} alt={name} className="size-full object-cover" />
        ) : (
          <span className="flex size-full items-center justify-center text-xs font-semibold uppercase text-muted-foreground">
            {name.charAt(0)}
          </span>
        )}
      </div>
      <div className="min-w-0">
        <p className="truncate text-sm font-medium text-foreground">{name}</p>
        <div className="flex flex-wrap items-center gap-1 mt-0.5">
          {role && (
            <Badge variant={ROLE_VARIANT[role.slug] ?? 'secondary'} appearance="light" size="sm">
              {role.name}
            </Badge>
          )}
          {current_stage && (
            <Badge variant="info" appearance="light" size="sm">
              {current_stage.name}
            </Badge>
          )}
        </div>
      </div>
    </div>
  );
}

// ── Abas ─────────────────────────────────────────────────────────────────────

const TABS: Array<{ key: MembrosTab; label: string; statsKey: keyof ReturnType<typeof useMembros>['stats'] }> = [
  { key: 'todos',         label: 'Todos',         statsKey: 'todos' },
  { key: 'presbiteros',   label: 'Presbíteros',   statsKey: 'presbiteros' },
  { key: 'diaconos',      label: 'Diáconos',      statsKey: 'diaconos' },
  { key: 'irmaos',        label: 'Irmãos',        statsKey: 'irmaos' },
  { key: 'votos_simples', label: 'Votos Simples', statsKey: 'votos_simples' },
];

// ── Componente principal ──────────────────────────────────────────────────────

export function MembrosTable({
  refreshKey,
  onEdit,
}: {
  refreshKey?: number;
  onEdit?: (id: number) => void;
}) {
  const navigate = useNavigate();
  const [activeTab, setActiveTab] = useState<MembrosTab>('todos');
  const [pagination, setPagination] = useState<PaginationState>({ pageIndex: 0, pageSize: 25 });
  const [sorting, setSorting] = useState<SortingState>([{ id: 'name', desc: false }]);
  const [rowSelection, setRowSelection] = useState<RowSelectionState>({});
  const [searchQuery, setSearchQuery] = useState('');

  const sortCol = sorting[0];

  const { data, stats, pagination: meta, loading, error, refetch } = useMembros({
    tab:     activeTab,
    search:  searchQuery || undefined,
    page:    pagination.pageIndex + 1,
    perPage: pagination.pageSize,
    sortBy:  sortCol?.id,
    sortDir: sortCol?.desc ? 'desc' : 'asc',
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

  const handleTabChange = useCallback((tab: MembrosTab) => {
    setActiveTab(tab);
    setPagination((p) => ({ ...p, pageIndex: 0 }));
  }, []);

  const handleClearSearch = useCallback(() => {
    setSearchQuery('');
    setPagination((p) => ({ ...p, pageIndex: 0 }));
  }, []);

  const columns = useMemo<ColumnDef<IMembro>[]>(
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
        id: 'name',
        accessorKey: 'name',
        header: ({ column }) => <DataGridColumnHeader title="Membro" column={column} />,
        cell: ({ row }) => <MembroCell row={row} />,
        enableSorting: true,
        size: 280,
      },
      {
        id: 'province',
        accessorKey: 'province',
        header: ({ column }) => <DataGridColumnHeader title="Província" column={column} />,
        cell: ({ row }) => (
          <span className="text-sm text-foreground">
            {row.original.province ?? <span className="text-muted-foreground">—</span>}
          </span>
        ),
        enableSorting: false,
        size: 140,
      },
      {
        id: 'current_location',
        accessorKey: 'current_location',
        header: ({ column }) => <DataGridColumnHeader title="Localização Atual" column={column} />,
        cell: ({ row }) => (
          <span className="text-sm text-foreground truncate">
            {row.original.current_location ?? <span className="text-muted-foreground">—</span>}
          </span>
        ),
        enableSorting: false,
        size: 200,
      },
      {
        id: 'data_chave',
        accessorKey: 'data_chave',
        header: ({ column }) => <DataGridColumnHeader title="Ordenação / Profissão" column={column} />,
        cell: ({ row }) => (
          <span className="tabular-nums text-sm">
            {row.original.data_chave ?? <span className="text-muted-foreground">—</span>}
          </span>
        ),
        enableSorting: false,
        size: 160,
      },
      {
        id: 'actions',
        header: () => <span className="sr-only">Ações</span>,
        cell: ({ row }) => (
          <div className="flex justify-end">
            <DropdownMenu>
              <DropdownMenuTrigger asChild>
                <Button variant="ghost" size="sm" className="size-8 p-0">
                  <EllipsisVertical className="size-4" />
                  <span className="sr-only">Ações</span>
                </Button>
              </DropdownMenuTrigger>
              <DropdownMenuContent align="end">
                <DropdownMenuItem onClick={() => onEdit?.(row.original.id)}>
                  Editar
                </DropdownMenuItem>
                <DropdownMenuSeparator />
                <DropdownMenuItem className="text-destructive focus:text-destructive">
                  Excluir
                </DropdownMenuItem>
              </DropdownMenuContent>
            </DropdownMenu>
          </div>
        ),
        enableSorting: false,
        size: 60,
      },
    ],
    [onEdit],
  );

  const table = useReactTable({
    data,
    columns,
    pageCount: meta.last_page,
    state: { pagination, sorting, rowSelection },
    onPaginationChange: setPagination,
    onSortingChange: setSorting,
    onRowSelectionChange: setRowSelection,
    getCoreRowModel: getCoreRowModel(),
    getSortedRowModel: getSortedRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
    manualPagination: true,
    manualSorting: true,
    enableRowSelection: true,
    getRowId: (row) => String(row.id),
  });

  return (
    <DataGrid
      table={table}
      recordCount={meta.total}
      tableLayout={{ columnsPinnable: true, columnsMovable: false, columnsVisibility: false, cellBorder: true, width: 'auto' }}
    >
      <Card>
        <Tabs value={activeTab} onValueChange={(v) => handleTabChange(v as MembrosTab)}>
          {/* Abas dentro do card — mesmo padrão de contabilidade/page.tsx */}
          <CardHeader className="flex-col items-stretch justify-start gap-0 border-b border-border pt-4 pb-0 min-h-0 px-0 bg-accent/50">
            <TabsList variant="line" className="w-full justify-start gap-6 px-5">
              {TABS.map((tab) => (
                <TabsTrigger key={tab.key} value={tab.key} className="gap-1.5">
                  {tab.label}
                  <Badge variant="secondary" appearance="light" size="sm" className="tabular-nums">
                    {stats[tab.statsKey]}
                  </Badge>
                </TabsTrigger>
              ))}
            </TabsList>
          </CardHeader>

          {/* Barra de busca + atualizar */}
          <div className="flex items-center gap-3 px-4 py-3 border-b border-border/50">
            <div className="relative flex-1 max-w-xs">
              <Search className="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground pointer-events-none" />
              <Input
                className="ps-9 pe-9 h-9"
                placeholder="Buscar membro..."
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
            <CardToolbar>
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

          {error && (
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
        </Tabs>
      </Card>
    </DataGrid>
  );
}
