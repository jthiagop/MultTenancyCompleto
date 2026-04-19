import { useCallback, useEffect, useMemo, useState } from 'react';
import {
  ColumnDef,
  getCoreRowModel,
  getPaginationRowModel,
  getSortedRowModel,
  PaginationState,
  SortingState,
  useReactTable,
} from '@tanstack/react-table';
import { format, parseISO } from 'date-fns';
import { ptBR } from 'date-fns/locale';
import {
  Baby,
  CalendarX2,
  Copy,
  CreditCard,
  MapPin,
  MoreHorizontal,
  Pencil,
  RefreshCw,
  Search,
  Settings2,
  UserCheck,
  UserRound,
} from 'lucide-react';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { CardFooter, CardHeader, CardTable, CardToolbar } from '@/components/ui/card';
import { DataGrid } from '@/components/ui/data-grid';
import { DataGridColumnHeader } from '@/components/ui/data-grid-column-header';
import { DataGridColumnVisibility } from '@/components/ui/data-grid-column-visibility';
import { DataGridPagination } from '@/components/ui/data-grid-pagination';
import { DataGridTable } from '@/components/ui/data-grid-table';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import { ScrollArea, ScrollBar } from '@/components/ui/scroll-area';

export interface IDifunto {
  id: number;
  nome_completo: string;
  avatar: string | null;
  data_nascimento: string | null;
  data_falecimento: string;
  cpf: string | null;
  nome_responsavel: string | null;
  tumulo_atual: string | null;
}

function fmtDate(iso: string | null) {
  if (!iso) return '—';
  try {
    return format(parseISO(iso), 'dd/MM/yyyy', { locale: ptBR });
  } catch {
    return iso;
  }
}

export function DifuntosTable({
  refreshKey,
  onSaved: _onSaved,
  onEdit,
  onClone,
}: {
  refreshKey?: number;
  onSaved?: () => void;
  onEdit?: (id: number) => void;
  onClone?: (id: number) => void;
}) {
  const [data, setData] = useState<IDifunto[]>([]);
  const [total, setTotal] = useState(0);
  const [loading, setLoading] = useState(false);
  const [search, setSearch] = useState('');
  const [pagination, setPagination] = useState<PaginationState>({ pageIndex: 0, pageSize: 20 });
  const [sorting, setSorting] = useState<SortingState>([{ id: 'nome_completo', desc: false }]);

  const sortCol = sorting[0];

  const loadData = useCallback(async () => {
    setLoading(true);
    try {
      const params = new URLSearchParams({
        page: String(pagination.pageIndex + 1),
        per_page: String(pagination.pageSize),
        ...(search ? { search } : {}),
        ...(sortCol ? { sort_by: sortCol.id, sort_dir: sortCol.desc ? 'desc' : 'asc' } : {}),
      });
      const res = await fetch(`/cemiterio/difuntos?${params}`, {
        headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      });
      if (!res.ok || !res.headers.get('content-type')?.includes('application/json')) return;
      const json = await res.json();
      setData(json.data ?? []);
      setTotal(json.total ?? 0);
    } finally {
      setLoading(false);
    }
  }, [pagination.pageIndex, pagination.pageSize, search, sortCol]);

  useEffect(() => {
    loadData();
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [loadData, refreshKey]);

  const columns = useMemo<ColumnDef<IDifunto>[]>(
    () => [
      {
        id: 'nome_completo',
        accessorKey: 'nome_completo',
        header: ({ column }) => (
          <DataGridColumnHeader
            column={column}
            title="Nome"
            icon={<UserRound aria-hidden className="size-3.5 shrink-0 opacity-60" />}
          />
        ),
        cell: ({ row }) => {
          const initials = row.original.nome_completo
            .split(' ')
            .filter(Boolean)
            .slice(0, 2)
            .map((n) => n[0].toUpperCase())
            .join('');
          return (
            <div className="flex items-center gap-2.5 min-w-0">
              <Avatar className="size-8 shrink-0">
                <AvatarImage src={row.original.avatar ?? undefined} alt={row.original.nome_completo} />
                <AvatarFallback className="text-xs">{initials}</AvatarFallback>
              </Avatar>
              <span className="font-medium text-sm truncate">{row.original.nome_completo}</span>
            </div>
          );
        },
        enableSorting: true,
        size: 260,
      },
      {
        id: 'cpf',
        accessorKey: 'cpf',
        header: ({ column }) => (
          <DataGridColumnHeader
            column={column}
            title="CPF"
            icon={<CreditCard aria-hidden className="size-3.5 shrink-0 opacity-60" />}
          />
        ),
        cell: ({ row }) => (
          <span className="text-sm text-muted-foreground tabular-nums">{row.original.cpf ?? '—'}</span>
        ),
        enableSorting: false,
        size: 140,
      },
      {
        id: 'data_falecimento',
        accessorKey: 'data_falecimento',
        header: ({ column }) => (
          <DataGridColumnHeader
            column={column}
            title="Falecimento"
            icon={<CalendarX2 aria-hidden className="size-3.5 shrink-0 opacity-60" />}
          />
        ),
        cell: ({ row }) => (
          <span className="text-sm tabular-nums">{fmtDate(row.original.data_falecimento)}</span>
        ),
        enableSorting: true,
        size: 140,
      },
      {
        id: 'data_nascimento',
        accessorKey: 'data_nascimento',
        header: ({ column }) => (
          <DataGridColumnHeader
            column={column}
            title="Nascimento"
            icon={<Baby aria-hidden className="size-3.5 shrink-0 opacity-60" />}
          />
        ),
        cell: ({ row }) => (
          <span className="text-sm text-muted-foreground tabular-nums">
            {fmtDate(row.original.data_nascimento)}
          </span>
        ),
        enableSorting: false,
        size: 140,
      },
      {
        id: 'nome_responsavel',
        accessorKey: 'nome_responsavel',
        header: ({ column }) => (
          <DataGridColumnHeader
            column={column}
            title="Responsável"
            icon={<UserCheck aria-hidden className="size-3.5 shrink-0 opacity-60" />}
          />
        ),
        cell: ({ row }) => (
          <span className="text-sm text-muted-foreground">{row.original.nome_responsavel ?? '—'}</span>
        ),
        enableSorting: false,
        size: 200,
      },
      {
        id: 'tumulo_atual',
        accessorKey: 'tumulo_atual',
        header: ({ column }) => (
          <DataGridColumnHeader
            column={column}
            title="Túmulo"
            icon={<MapPin aria-hidden className="size-3.5 shrink-0 opacity-60" />}
          />
        ),
        cell: ({ row }) => (
          <span className="text-sm text-muted-foreground">{row.original.tumulo_atual ?? '—'}</span>
        ),
        enableSorting: false,
        size: 130,
      },
      {
        id: 'acoes',
        header: () => null,
        cell: ({ row }) => (
          <DropdownMenu>
            <DropdownMenuTrigger asChild>
              <Button variant="ghost" size="sm" className="size-8 p-0">
                <MoreHorizontal className="size-4" />
                <span className="sr-only">Ações</span>
              </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end">
              <DropdownMenuItem onClick={() => onEdit?.(row.original.id)}>
                <Pencil className="size-3.5 mr-2" />
                Editar
              </DropdownMenuItem>
              <DropdownMenuSeparator />
              <DropdownMenuItem onClick={() => onClone?.(row.original.id)}>
                <Copy className="size-3.5 mr-2" />
                Clonar
              </DropdownMenuItem>
            </DropdownMenuContent>
          </DropdownMenu>
        ),
        enableSorting: false,
        enableHiding: false,
        size: 50,
      },
    ],
    [onEdit, onClone],
  );

  const table = useReactTable({
    columns,
    data,
    pageCount: Math.ceil(total / pagination.pageSize),
    getRowId: (row) => String(row.id),
    state: { pagination, sorting },
    columnResizeMode: 'onChange',
    onPaginationChange: setPagination,
    onSortingChange: setSorting,
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
                placeholder="Buscar nome ou CPF..."
                value={search}
                onChange={(e) => {
                  setSearch(e.target.value);
                  setPagination((p) => ({ ...p, pageIndex: 0 }));
                }}
                className="pl-8 h-10 w-72"
              />
            </div>

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
