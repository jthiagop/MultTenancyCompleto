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
import { EllipsisVertical, KeyRound, Loader2, Pencil, RefreshCw, Search, X } from 'lucide-react';
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
import { useUsuarios, HttpError, type IUsuario } from '@/hooks/useUsuarios';
import {
  Avatar,
  AvatarFallback,
  AvatarGroup,
  AvatarImage,
} from '@/components/ui/avatar';

// ── Variantes de role → badge ─────────────────────────────────────────────────

const ROLE_VARIANT: Record<string, 'primary' | 'destructive' | 'warning' | 'secondary' | 'success'> = {
  global:    'destructive',
  admin:     'primary',
  admin_user:'warning',
  user:      'secondary',
};

const ROLE_LABEL: Record<string, string> = {
  global:     'Global',
  admin:      'Admin',
  admin_user: 'Admin Usuário',
  user:       'Usuário',
  sub_user:   'Sub-usuário',
};

// ── Célula de avatar + nome + email ──────────────────────────────────────────

function UsuarioCell({ row }: { row: { original: IUsuario } }) {
  const { name, email, avatar_url } = row.original;
  return (
    <div className="flex items-center gap-3">
      <div className="relative size-8 shrink-0 overflow-hidden rounded-full bg-muted">
        {avatar_url ? (
          <img
            src={avatar_url}
            alt={name}
            className="size-full object-cover"
          />
        ) : (
          <span className="flex size-full items-center justify-center text-xs font-semibold uppercase text-muted-foreground">
            {name.charAt(0)}
          </span>
        )}
      </div>
      <div className="min-w-0">
        <p className="truncate text-sm font-medium text-foreground">{name}</p>
        <p className="truncate text-xs text-muted-foreground">{email}</p>
      </div>
    </div>
  );
}

// ── Componente principal ──────────────────────────────────────────────────────

export function UsuariosTable({ refreshKey, onEdit, onResetPassword }: { refreshKey?: number; onEdit?: (id: number) => void; onResetPassword?: (id: number, name: string, email: string) => void }) {
  const navigate = useNavigate();
  const [pagination, setPagination] = useState<PaginationState>({ pageIndex: 0, pageSize: 20 });
  const [sorting, setSorting] = useState<SortingState>([{ id: 'name', desc: false }]);
  const [rowSelection, setRowSelection] = useState<RowSelectionState>({});
  const [searchQuery, setSearchQuery] = useState('');

  const sortCol = sorting[0];

  const { data, pagination: meta, loading, error, refetch } = useUsuarios({
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

  const columns = useMemo<ColumnDef<IUsuario>[]>(
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
        header: ({ column }) => <DataGridColumnHeader title="Usuário" column={column} />,
        cell: ({ row }) => <UsuarioCell row={row} />,
        enableSorting: true,
        size: 260,
      },
      {
        id: 'roles',
        accessorKey: 'roles',
        header: ({ column }) => <DataGridColumnHeader title="Permissão" column={column} />,
        cell: ({ row }) => {
          const roles = row.original.roles;
          if (!roles.length) return <span className="text-muted-foreground text-sm">—</span>;
          return (
            <div className="flex flex-wrap gap-1">
              {roles.map((r) => (
                <Badge
                  key={r.name}
                  variant={ROLE_VARIANT[r.name] ?? 'secondary'}
                  appearance="outline"
                  size="sm"
                >
                  {ROLE_LABEL[r.name] ?? r.name}
                </Badge>
              ))}
            </div>
          );
        },
        enableSorting: false,
        size: 180,
      },
      {
        id: 'companies',
        accessorKey: 'companies',
        header: ({ column }) => <DataGridColumnHeader title="Organismos" column={column} />,
        cell: ({ row }) => {
          const companies = row.original.companies ?? [];
          if (!companies.length) return <span className="text-muted-foreground text-sm">—</span>;
          const visible = companies.slice(0, 4);
          const extra = companies.length - visible.length;
          return (
            <AvatarGroup>
              {visible.map((c) => (
                <Avatar key={c.id} className="size-7" title={c.name}>
                  {c.avatar_url ? (
                    <AvatarImage src={c.avatar_url} alt={c.name} />
                  ) : null}
                  <AvatarFallback className="text-[10px]">
                    {c.name.slice(0, 2).toUpperCase()}
                  </AvatarFallback>
                </Avatar>
              ))}
              {extra > 0 && (
                <div className="flex size-7 items-center justify-center rounded-full bg-muted text-[10px] font-medium text-muted-foreground ring-2 ring-background">
                  +{extra}
                </div>
              )}
            </AvatarGroup>
          );
        },
        enableSorting: false,
        size: 160,
      },
      {
        id: 'last_login_formatted',
        accessorKey: 'last_login_formatted',
        header: ({ column }) => <DataGridColumnHeader title="Último Login" column={column} />,
        cell: ({ row }) => (
          <span className="text-sm text-muted-foreground">{row.original.last_login_formatted}</span>
        ),
        enableSorting: false,
        size: 150,
      },
      {
        id: 'active',
        accessorKey: 'active',
        header: ({ column }) => <DataGridColumnHeader title="Status" column={column} />,
        cell: ({ row }) =>
          row.original.active ? (
            <Badge variant="success" appearance="outline" size="sm">Ativado</Badge>
          ) : (
            <Badge variant="destructive" appearance="outline" size="sm">Desativado</Badge>
          ),
        enableSorting: true,
        size: 110,
      },
      {
        id: 'created_at',
        accessorKey: 'created_at_formatted',
        header: ({ column }) => <DataGridColumnHeader title="Data de Ingresso" column={column} />,
        cell: ({ row }) => (
          <span className="tabular-nums text-sm">{row.original.created_at_formatted}</span>
        ),
        enableSorting: true,
        size: 140,
      },
      {
        id: 'actions',
        header: () => <span className="text-xs text-muted-foreground">Ações</span>,
        cell: ({ row }) => (
          <DropdownMenu>
            <DropdownMenuTrigger asChild>
              <Button variant="ghost" size="sm" className="size-8 p-0 data-[state=open]:bg-accent">
                <EllipsisVertical className="size-4" />
                <span className="sr-only">Ações</span>
              </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end" className="w-44">
              <DropdownMenuItem onClick={() => onEdit?.(row.original.id)}>
                <Pencil className="size-3.5" />
                Editar
              </DropdownMenuItem>
              <DropdownMenuSeparator />
              <DropdownMenuItem
                onClick={() => onResetPassword?.(row.original.id, row.original.name, row.original.email)}
                className="text-amber-600 focus:text-amber-600 dark:text-amber-400"
              >
                <KeyRound className="size-3.5" />
                Redefinir Senha
              </DropdownMenuItem>
            </DropdownMenuContent>
          </DropdownMenu>
        ),
        enableSorting: false,
        enableHiding: false,
        size: 60,
      },
    ],
    [],
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

  return (
    <DataGrid
      table={table}
      recordCount={meta.total}
      tableLayout={{ columnsPinnable: true, columnsMovable: false, columnsVisibility: false, cellBorder: true, width: 'auto' }}
    >
      <Card>
        <CardHeader className="flex flex-row items-center gap-3 py-4 min-h-0">
          <div className="relative flex-1 max-w-xs">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground pointer-events-none" />
            <Input
              className="ps-9 pe-9 h-9"
              placeholder="Buscar usuário..."
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
          <div className="px-4 py-2 text-sm text-destructive bg-destructive/10">{error.message}</div>
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
