import { useCallback, useEffect, useMemo, useState } from 'react';
import {
  ColumnDef,
  getCoreRowModel,
  getPaginationRowModel,
  getSortedRowModel,
  PaginationState,
  SortingState,
  VisibilityState,
  useReactTable,
} from '@tanstack/react-table';
import { MoreHorizontal, Pencil, RefreshCw, Search, Settings2 } from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { CardFooter, CardHeader, CardTable, CardToolbar } from '@/components/ui/card';
import { DataGrid } from '@/components/ui/data-grid';
import { DataGridColumnHeader } from '@/components/ui/data-grid-column-header';
import { DataGridColumnVisibility } from '@/components/ui/data-grid-column-visibility';
import { DataGridPagination } from '@/components/ui/data-grid-pagination';
import { DataGridTable } from '@/components/ui/data-grid-table';
import { Input } from '@/components/ui/input';
import { ScrollArea, ScrollBar } from '@/components/ui/scroll-area';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';

export interface ITumulo {
  id: number;
  codigo_tumulo: string;
  localizacao: string;
  tipo: string;
  tamanho: string | null;
  status: 'Disponível' | 'Ocupada' | 'Reservada' | 'Manutenção';
  ocupante_atual: string | null;
}

const STATUS_VARIANT: Record<
  ITumulo['status'],
  'success' | 'destructive' | 'warning' | 'secondary'
> = {
  'Disponível': 'success',
  'Ocupada':    'destructive',
  'Reservada':  'warning',
  'Manutenção': 'secondary',
};

export function TumulosTable({
  refreshKey,
  onSaved: _onSaved,
  onEdit,
}: {
  refreshKey?: number;
  onSaved?: () => void;
  onEdit?: (id: number) => void;
}) {
  const [data, setData] = useState<ITumulo[]>([]);
  const [total, setTotal] = useState(0);
  const [loading, setLoading] = useState(false);
  const [search, setSearch] = useState('');
  const [statusFilter, setStatusFilter] = useState<string>('all');
  const [pagination, setPagination] = useState<PaginationState>({ pageIndex: 0, pageSize: 20 });
  const [sorting, setSorting] = useState<SortingState>([]);
  const [columnVisibility, setColumnVisibility] = useState<VisibilityState>({
    tamanho: false,
  });

  const sortCol = sorting[0];

  const loadData = useCallback(async () => {
    setLoading(true);
    try {
      const params = new URLSearchParams({
        page: String(pagination.pageIndex + 1),
        per_page: String(pagination.pageSize),
        ...(search ? { search } : {}),
        ...(statusFilter !== 'all' ? { status: statusFilter } : {}),
        ...(sortCol ? { sort_by: sortCol.id, sort_dir: sortCol.desc ? 'desc' : 'asc' } : {}),
      });
      const res = await fetch(`/cemiterio/tumulos?${params}`, {
        headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      });
      if (!res.ok || !res.headers.get('content-type')?.includes('application/json')) return;
      const json = await res.json();
      setData(json.data ?? []);
      setTotal(json.total ?? 0);
    } finally {
      setLoading(false);
    }
  }, [pagination.pageIndex, pagination.pageSize, search, statusFilter, sortCol]);

  useEffect(() => {
    loadData();
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [loadData, refreshKey]);

  const columns = useMemo<ColumnDef<ITumulo>[]>(
    () => [
      {
        id: 'codigo_tumulo',
        accessorKey: 'codigo_tumulo',
        header: ({ column }) => <DataGridColumnHeader column={column} title="Código" />,
        cell: ({ row }) => <span className="font-medium text-sm">{row.original.codigo_tumulo}</span>,
        enableSorting: true,
        size: 120,
      },
      {
        id: 'localizacao',
        accessorKey: 'localizacao',
        header: ({ column }) => <DataGridColumnHeader column={column} title="Localização" />,
        cell: ({ row }) => <span className="text-sm text-muted-foreground">{row.original.localizacao}</span>,
        enableSorting: true,
        size: 200,
      },
      {
        id: 'tipo',
        accessorKey: 'tipo',
        header: ({ column }) => <DataGridColumnHeader column={column} title="Tipo" />,
        cell: ({ row }) => <span className="text-sm">{row.original.tipo || '—'}</span>,
        enableSorting: true,
        size: 120,
      },
      {
        id: 'tamanho',
        accessorKey: 'tamanho',
        header: ({ column }) => <DataGridColumnHeader column={column} title="Tamanho" />,
        cell: ({ row }) => <span className="text-sm text-muted-foreground">{row.original.tamanho ?? '—'}</span>,
        enableSorting: false,
        size: 120,
      },
      {
        id: 'status',
        accessorKey: 'status',
        header: ({ column }) => <DataGridColumnHeader column={column} title="Status" />,
        cell: ({ row }) => {
          const s = row.original.status;
          return (
            <Badge variant={STATUS_VARIANT[s]} appearance="outline" size="sm">
              {s}
            </Badge>
          );
        },
        enableSorting: false,
        size: 130,
      },
      {
        id: 'ocupante_atual',
        accessorKey: 'ocupante_atual',
        header: ({ column }) => <DataGridColumnHeader column={column} title="Ocupante Atual" />,
        cell: ({ row }) => (
          <span className="text-sm text-muted-foreground">{row.original.ocupante_atual ?? '—'}</span>
        ),
        enableSorting: false,
        size: 200,
      },
      {
        id: 'actions',
        header: () => <span className="text-xs text-muted-foreground">Ações</span>,
        cell: ({ row }) => (
          <DropdownMenu>
            <DropdownMenuTrigger asChild>
              <Button variant="ghost" size="sm" className="size-8 p-0">
                <MoreHorizontal className="size-4" />
              </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end">
              <DropdownMenuItem onClick={() => onEdit?.(row.original.id)}>
                <Pencil className="size-3.5" />
                Editar
              </DropdownMenuItem>
            </DropdownMenuContent>
          </DropdownMenu>
        ),
        enableSorting: false,
        enableHiding: false,
        size: 60,
      },
    ],
    [onEdit],
  );

  const table = useReactTable({
    columns,
    data,
    pageCount: Math.ceil(total / pagination.pageSize),
    getRowId: (row) => String(row.id),
    state: { pagination, sorting, columnVisibility },
    columnResizeMode: 'onChange',
    onPaginationChange: setPagination,
    onSortingChange: setSorting,
    onColumnVisibilityChange: setColumnVisibility,
    getCoreRowModel: getCoreRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
    getSortedRowModel: getSortedRowModel(),
    manualPagination: true,
    manualSorting: true,
  });

  return (
    <DataGrid
      table={table}
      recordCount={total}
      isLoading={loading}
      tableLayout={{ columnsPinnable: true, columnsMovable: true, columnsVisibility: true, cellBorder: true }}
    >
      <CardHeader className="flex flex-col items-stretch justify-start gap-3 py-4 min-h-0">
          <div className="flex min-w-0 w-full flex-wrap items-center gap-2">
            <div className="relative">
              <Search className="absolute left-2.5 top-2.5 size-4 text-muted-foreground" />
              <Input
                placeholder="Buscar código ou localização..."
                value={search}
                onChange={(e) => {
                  setSearch(e.target.value);
                  setPagination((p) => ({ ...p, pageIndex: 0 }));
                }}
                className="pl-8 h-10 w-64"
              />
            </div>

            <Select
              value={statusFilter}
              onValueChange={(v) => {
                setStatusFilter(v);
                setPagination((p) => ({ ...p, pageIndex: 0 }));
              }}
            >
              <SelectTrigger className="h-10 w-44">
                <SelectValue placeholder="Filtrar status" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">Todos os status</SelectItem>
                <SelectItem value="Disponível">Disponível</SelectItem>
                <SelectItem value="Ocupada">Ocupada</SelectItem>
                <SelectItem value="Reservada">Reservada</SelectItem>
                <SelectItem value="Manutenção">Manutenção</SelectItem>
              </SelectContent>
            </Select>

            <div className="ms-auto flex items-center gap-2">
              <CardToolbar>
                <Button
                  variant="outline"
                  size="sm"
                  className="h-10 min-w-10 px-3"
                  onClick={loadData}
                  disabled={loading}
                  aria-label="Atualizar"
                >
                  <RefreshCw className={`size-4 ${loading ? 'animate-spin' : ''}`} />
                </Button>
                <DataGridColumnVisibility
                  table={table}
                  trigger={
                    <Button variant="outline" size="sm" className="h-10 min-w-32 gap-2 px-3">
                      <Settings2 className="size-4" />
                      Colunas
                    </Button>
                  }
                />
              </CardToolbar>
            </div>
          </div>
      </CardHeader>

    <CardTable>
        <ScrollArea>
          <DataGridTable />
          <ScrollBar orientation="horizontal" />
        </ScrollArea>
      </CardTable>

      <CardFooter>
        <DataGridPagination />
      </CardFooter>
    </DataGrid>
  );
}
