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
  Building2,
  Eye,
  EllipsisVertical,
  Loader2,
  Pencil,
  RefreshCw,
  Search,
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
import {
  Avatar,
  AvatarFallback,
  AvatarGroup,
  AvatarImage,
} from '@/components/ui/avatar';
import { useOrganismos, HttpError, type IOrganismo } from '@/hooks/useOrganismos';

// ── Variantes de status ────────────────────────────────────────────────────────

const STATUS_VARIANT: Record<string, 'success' | 'destructive' | 'warning' | 'secondary'> = {
  active:   'success',
  ativo:    'success',
  inactive: 'destructive',
  inativo:  'destructive',
  pendente: 'warning',
};

const STATUS_LABEL: Record<string, string> = {
  active:   'Ativo',
  ativo:    'Ativo',
  inactive: 'Inativo',
  inativo:  'Inativo',
  pendente: 'Pendente',
};

// ── Variantes de tipo (matriz/filial) ─────────────────────────────────────────

const TYPE_VARIANT: Record<string, 'primary' | 'secondary' | 'info'> = {
  matriz:    'primary',
  filial:    'info',
  secundaria:'secondary',
};

const TYPE_LABEL: Record<string, string> = {
  matriz:     'Matriz',
  filial:     'Filial',
  secundaria: 'Secundária',
};

// ── Célula: nome + email + endereço resumido ───────────────────────────────────

function OrganismoCell({ row }: { row: { original: IOrganismo } }) {
  const { name, email, avatar_url, address_line } = row.original;
  return (
    <div className="flex items-center gap-3">
      <div className="relative size-9 shrink-0 overflow-hidden rounded-full bg-muted border border-border">
        {avatar_url ? (
          <img src={avatar_url} alt={name} className="size-full object-cover" />
        ) : (
          <span className="flex size-full items-center justify-center text-muted-foreground">
            <Building2 className="size-4" />
          </span>
        )}
      </div>
      <div className="min-w-0">
        <p className="truncate text-sm font-medium text-foreground">{name}</p>
        {email && (
          <p className="truncate text-xs text-muted-foreground">{email}</p>
        )}
        {address_line && (
          <p className="truncate text-[11px] text-muted-foreground/80">{address_line}</p>
        )}
      </div>
    </div>
  );
}

// ── Componente principal ──────────────────────────────────────────────────────

export function OrganismosTable({
  refreshKey,
  onView,
  onEdit,
}: {
  refreshKey?: number;
  onView?: (id: number) => void;
  onEdit?: (id: number) => void;
}) {
  const navigate = useNavigate();
  const [pagination, setPagination] = useState<PaginationState>({ pageIndex: 0, pageSize: 20 });
  const [sorting, setSorting] = useState<SortingState>([{ id: 'name', desc: false }]);
  const [rowSelection, setRowSelection] = useState<RowSelectionState>({});
  const [searchQuery, setSearchQuery] = useState('');

  const sortCol = sorting[0];

  const { data, pagination: meta, loading, error, refetch } = useOrganismos({
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

  const columns = useMemo<ColumnDef<IOrganismo>[]>(
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
        header: ({ column }) => <DataGridColumnHeader title="Organismo" column={column} />,
        cell: ({ row }) => <OrganismoCell row={row} />,
        enableSorting: true,
        size: 340,
      },
      {
        id: 'cnpj',
        accessorKey: 'cnpj',
        header: ({ column }) => <DataGridColumnHeader title="CNPJ" column={column} />,
        cell: ({ row }) => (
          <span className="font-mono text-xs text-muted-foreground">
            {row.original.cnpj ?? '—'}
          </span>
        ),
        enableSorting: false,
        size: 150,
      },
      {
        id: 'type',
        accessorKey: 'type',
        header: ({ column }) => <DataGridColumnHeader title="Tipo" column={column} />,
        cell: ({ row }) => {
          const t = (row.original.type ?? '').toLowerCase();
          if (!t) return <span className="text-muted-foreground text-sm">—</span>;
          return (
            <Badge variant={TYPE_VARIANT[t] ?? 'secondary'} appearance="outline" size="sm">
              {TYPE_LABEL[t] ?? t}
            </Badge>
          );
        },
        enableSorting: true,
        size: 110,
      },
      {
        id: 'users_count',
        accessorKey: 'users_count',
        header: ({ column }) => <DataGridColumnHeader title="Membros" column={column} />,
        cell: ({ row }) => {
          const preview = row.original.users_preview ?? [];
          const extra = row.original.users_count - preview.length;
          if (row.original.users_count === 0) {
            return <span className="text-xs text-muted-foreground">Nenhum</span>;
          }
          return (
            <div className="flex items-center gap-2">
              <AvatarGroup>
                {preview.map((u) => (
                  <Avatar key={u.id} className="size-7" title={u.name}>
                    {u.avatar_url ? <AvatarImage src={u.avatar_url} alt={u.name} /> : null}
                    <AvatarFallback className="text-[10px]">
                      {u.name.slice(0, 2).toUpperCase()}
                    </AvatarFallback>
                  </Avatar>
                ))}
                {extra > 0 && (
                  <div className="flex size-7 items-center justify-center rounded-full bg-muted text-[10px] font-medium text-muted-foreground ring-2 ring-background">
                    +{extra}
                  </div>
                )}
              </AvatarGroup>
              <span className="text-xs text-muted-foreground tabular-nums">
                {row.original.users_count}
              </span>
            </div>
          );
        },
        enableSorting: true,
        size: 200,
      },
      {
        id: 'status',
        accessorKey: 'status',
        header: ({ column }) => <DataGridColumnHeader title="Status" column={column} />,
        cell: ({ row }) => {
          const s = (row.original.status ?? '').toLowerCase();
          if (!s) return <span className="text-muted-foreground text-sm">—</span>;
          return (
            <Badge variant={STATUS_VARIANT[s] ?? 'secondary'} appearance="outline" size="sm">
              {STATUS_LABEL[s] ?? s}
            </Badge>
          );
        },
        enableSorting: true,
        size: 120,
      },
      {
        id: 'created_at',
        accessorKey: 'created_at_formatted',
        header: ({ column }) => <DataGridColumnHeader title="Cadastro" column={column} />,
        cell: ({ row }) => (
          <span className="tabular-nums text-sm text-muted-foreground">
            {row.original.created_at_formatted}
          </span>
        ),
        enableSorting: true,
        size: 130,
      },
      // ── Colunas opcionais (ocultas por padrão) ───────────────────────────────
      {
        id: 'razao_social',
        accessorKey: 'razao_social',
        header: ({ column }) => <DataGridColumnHeader title="Razão Social" column={column} />,
        cell: ({ row }) => (
          <span className="truncate text-sm">{row.original.razao_social ?? '—'}</span>
        ),
        enableSorting: false,
        size: 220,
      },
      {
        id: 'email',
        accessorKey: 'email',
        header: ({ column }) => <DataGridColumnHeader title="E-mail" column={column} />,
        cell: ({ row }) => (
          <span className="truncate text-sm text-muted-foreground">
            {row.original.email ?? '—'}
          </span>
        ),
        enableSorting: true,
        size: 200,
      },
      {
        id: 'cidade_uf',
        header: ({ column }) => <DataGridColumnHeader title="Cidade / UF" column={column} />,
        cell: ({ row }) => {
          const { cidade, uf } = row.original;
          if (!cidade && !uf) return <span className="text-muted-foreground text-sm">—</span>;
          return (
            <span className="truncate text-sm">
              {[cidade, uf].filter(Boolean).join(' / ')}
            </span>
          );
        },
        enableSorting: false,
        size: 160,
      },
      {
        id: 'actions',
        header: () => <span className="text-xs text-muted-foreground">Ações</span>,
        cell: ({ row }) => (
          <DropdownMenu>
            <DropdownMenuTrigger asChild>
              <Button
                variant="ghost"
                size="sm"
                className="size-8 p-0 data-[state=open]:bg-accent"
                onClick={(e) => e.stopPropagation()}
              >
                <EllipsisVertical className="size-4" />
                <span className="sr-only">Ações</span>
              </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end" className="w-44">
              <DropdownMenuItem onClick={() => onEdit?.(row.original.id)}>
                <Pencil className="size-3.5" />
                Editar
              </DropdownMenuItem>
              <DropdownMenuItem onClick={() => onView?.(row.original.id)}>
                <Eye className="size-3.5" />
                Visualizar
              </DropdownMenuItem>
            </DropdownMenuContent>
          </DropdownMenu>
        ),
        enableSorting: false,
        enableHiding: false,
        size: 60,
      },
    ],
    [onView, onEdit],
  );

  const [columnVisibility, setColumnVisibility] = useState<VisibilityState>({
    razao_social: false,
    email:        false,
    cidade_uf:    false,
  });

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

  const handleClearSearch = useCallback(() => {
    setSearchQuery('');
    setPagination((p) => ({ ...p, pageIndex: 0 }));
  }, []);

  return (
    <DataGrid
      table={table}
      recordCount={meta.total}
      onRowClick={onEdit ? (row) => onEdit(Number(row.id)) : undefined}
      tableLayout={{
        columnsPinnable: true,
        columnsMovable: true,
        columnsVisibility: true,
        cellBorder: true,
        width: 'auto',
      }}
    >
      <Card>
        <CardHeader className="flex flex-row items-center gap-3 py-4 min-h-0">
          <div className="relative flex-1 max-w-xs">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground pointer-events-none" />
            <Input
              className="ps-9 pe-9 h-9"
              placeholder="Buscar por nome, razão social, CNPJ ou e-mail..."
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
        </CardHeader>

        {error && (
          <div className="px-4 py-2 text-sm text-destructive bg-destructive/10">
            {error instanceof HttpError && error.status === 403
              ? 'Você não tem permissão para visualizar os organismos.'
              : error.message}
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
