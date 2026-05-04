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
  Activity,
  CalendarDays,
  CalendarPlus,
  EllipsisVertical,
  Eye,
  Fingerprint,
  HeartHandshake,
  IdCard,
  MapPin,
  MessageCircle,
  Pencil,
  Phone,
  Printer,
  PrinterCheck,
  RefreshCw,
  Search,
  Trash2,
  UserRound,
  Users,
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
import { DataGrid } from '@/components/ui/data-grid';
import { DataGridColumnHeader } from '@/components/ui/data-grid-column-header';
import { DataGridPagination } from '@/components/ui/data-grid-pagination';
import {
  DataGridTable,
  DataGridTableRowSelect,
  DataGridTableRowSelectAll,
} from '@/components/ui/data-grid-table';
import { ScrollArea, ScrollBar } from '@/components/ui/scroll-area';
import { useFieis, HttpError, type FielStatus, type IFiel, type FieisAdvancedFiltersState } from '@/hooks/useFieis';
import { financeiroToolbarSoftBlueClass, financeiroToolbarSoftBlueInputClass } from '@/lib/financeiro-toolbar-accent';
import { cn } from '@/lib/utils';
import { FieisStatsBar, type FieisStatKey } from '@/pages/fieis/components/fieis-stats-bar';
import {
  FieisAdvancedFiltersScope,
  FieisAdvancedFiltersTrigger,
  FieisAdvancedFiltersChipsSection,
} from '@/pages/fieis/components/fieis-advanced-filters-bar';
import { CarteirinhaDialog } from '@/pages/fieis/components/carteirinha-dialog';

// ── Mapeamentos visuais ─────────────────────────────────────────────────────

const STATUS_VARIANT: Record<FielStatus, 'success' | 'secondary' | 'warning' | 'info'> = {
  Ativo: 'success',
  Inativo: 'secondary',
  Falecido: 'warning',
  'Mudou-se': 'info',
};

const SEXO_LABEL: Record<string, string> = {
  M: 'Masculino',
  F: 'Feminino',
  Outro: 'Outro',
};

// ── Célula: avatar + nome + idade/email ─────────────────────────────────────

function FielCell({ fiel }: { fiel: IFiel }) {
  const initials = fiel.nome_completo
    .trim()
    .split(/\s+/)
    .slice(0, 2)
    .map((p) => p.charAt(0).toUpperCase())
    .join('');

  const subtitle =
    fiel.email
      ?? (fiel.idade != null ? `${fiel.idade} anos` : null)
      ?? fiel.data_nascimento_formatted
      ?? null;

  return (
    <div className="flex items-center gap-3 min-w-0">
      <div className="relative size-9 shrink-0 overflow-hidden rounded-full bg-muted">
        {fiel.avatar_url ? (
          <img
            src={fiel.avatar_url}
            alt={fiel.nome_completo}
            className="size-full object-cover"
            loading="lazy"
          />
        ) : (
          <span className="flex size-full items-center justify-center text-xs font-semibold uppercase text-muted-foreground">
            {initials || '?'}
          </span>
        )}
      </div>
      <div className="min-w-0">
        <p className="truncate text-sm font-medium text-foreground">
          {fiel.nome_completo}
        </p>
        {subtitle && (
          <p className="truncate text-xs text-muted-foreground">{subtitle}</p>
        )}
      </div>
    </div>
  );
}

// ── Componente principal ────────────────────────────────────────────────────

export interface FieisTableProps {
  refreshKey?: number;
  onEdit?: (id: number) => void;
  onView?: (id: number) => void;
  onDelete?: (id: number, nome: string) => void;
}

export function FieisTable({ refreshKey, onEdit, onView, onDelete }: FieisTableProps) {
  const navigate = useNavigate();
  const [pagination, setPagination] = useState<PaginationState>({ pageIndex: 0, pageSize: 20 });
  const [sorting, setSorting] = useState<SortingState>([{ id: 'nome_completo', desc: false }]);
  const [rowSelection, setRowSelection] = useState<RowSelectionState>({});
  const [searchQuery, setSearchQuery] = useState('');
  const [activeTab, setActiveTab] = useState<FieisStatKey>('todos');
  const [advancedFilters, setAdvancedFilters] = useState<FieisAdvancedFiltersState>({});
  const [carteirinhaFielId, setCarteirinhaFielId] = useState<number | null>(null);

  const sortCol = sorting[0];

  // Converte a aba ativa em parâmetros de filtro para o hook
  const tabFilters = useMemo(() => {
    switch (activeTab) {
      case 'masculino':  return { sexo: 'M' as const };
      case 'feminino':   return { sexo: 'F' as const };
      case 'dizimista':  return { dizimista: true };
      case 'ativos':     return { status: 'Ativo' as FielStatus };
      default:           return {};
    }
  }, [activeTab]);

  const onAdvancedFiltersChange = useCallback((next: FieisAdvancedFiltersState) => {
    setAdvancedFilters(next);
    setPagination((p) => ({ ...p, pageIndex: 0 }));
  }, []);

  const { data, stats, pagination: meta, loading, error, refetch } = useFieis({
    search: searchQuery || undefined,
    page: pagination.pageIndex + 1,
    perPage: pagination.pageSize,
    sortBy: sortCol?.id,
    sortDir: sortCol?.desc ? 'desc' : 'asc',
    ...tabFilters,
    advancedFilters,
  });

  function handleTabClick(key: FieisStatKey) {
    setActiveTab(key);
    setPagination((p) => ({ ...p, pageIndex: 0 }));
  }

  useEffect(() => {
    if (refreshKey) refetch();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [refreshKey]);

  useEffect(() => {
    if (error instanceof HttpError && error.status === 403) {
      navigate('/dashboard', { replace: true });
    }
  }, [error, navigate]);

  const columns = useMemo<ColumnDef<IFiel>[]>(
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
        id: 'nome_completo',
        accessorKey: 'nome_completo',
        header: ({ column }) => (
          <DataGridColumnHeader
            title="Fiel"
            column={column}
            icon={<UserRound className="size-3.5 shrink-0 opacity-60" aria-hidden />}
          />
        ),
        cell: ({ row }) => <FielCell fiel={row.original} />,
        enableSorting: true,
        size: 280,
      },
      {
        id: 'sexo',
        accessorKey: 'sexo',
        header: ({ column }) => (
          <DataGridColumnHeader
            title="Sexo"
            column={column}
            icon={<Users className="size-3.5 shrink-0 opacity-60" aria-hidden />}
          />
        ),
        cell: ({ row }) => {
          const sexo = row.original.sexo;
          if (!sexo) return <span className="text-muted-foreground text-sm">—</span>;
          return <span className="text-sm">{SEXO_LABEL[sexo] ?? sexo}</span>;
        },
        enableSorting: true,
        size: 100,
      },
      {
        id: 'cpf',
        accessorKey: 'cpf',
        header: ({ column }) => (
          <DataGridColumnHeader
            title="CPF"
            column={column}
            icon={<Fingerprint className="size-3.5 shrink-0 opacity-60" aria-hidden />}
          />
        ),
        cell: ({ row }) => (
          <span className="text-sm tabular-nums">{row.original.cpf ?? '—'}</span>
        ),
        enableSorting: true,
        size: 140,
      },
      {
        id: 'telefone',
        accessorKey: 'telefone',
        header: ({ column }) => (
          <DataGridColumnHeader
            title="Telefone"
            column={column}
            icon={<Phone className="size-3.5 shrink-0 opacity-60" aria-hidden />}
          />
        ),
        cell: ({ row }) => {
          const tel = row.original.telefone;
          if (!tel) return <span className="text-muted-foreground text-sm">—</span>;
          return (
            <span className="inline-flex items-center gap-1.5 text-sm tabular-nums">
              {tel}
              {row.original.telefone_is_whatsapp && (
                <MessageCircle
                  className="size-3.5 text-green-600 shrink-0"
                  aria-label="WhatsApp"
                />
              )}
            </span>
          );
        },
        enableSorting: false,
        size: 160,
      },
      {
        id: 'cidade_uf',
        accessorKey: 'cidade_uf',
        header: ({ column }) => (
          <DataGridColumnHeader
            title="Cidade / UF"
            column={column}
            icon={<MapPin className="size-3.5 shrink-0 opacity-60" aria-hidden />}
          />
        ),
        cell: ({ row }) => (
          <span className="text-sm truncate">{row.original.cidade_uf ?? '—'}</span>
        ),
        enableSorting: false,
        size: 180,
      },
      {
        id: 'dizimista',
        accessorKey: 'dizimista',
        header: ({ column }) => (
          <DataGridColumnHeader
            title="Dizimista"
            column={column}
            icon={<HeartHandshake className="size-3.5 shrink-0 opacity-60" aria-hidden />}
          />
        ),
        cell: ({ row }) => {
          const { dizimista, codigo_dizimista } = row.original;
          if (!dizimista) {
            return (
              <Badge variant="secondary" appearance="outline" size="sm">
                Não
              </Badge>
            );
          }
          return (
            <span className="inline-flex items-center gap-1.5">
              <Badge variant="success" appearance="outline" size="sm">
                Sim
              </Badge>
              {codigo_dizimista && (
                <span className="inline-flex items-center gap-1 text-xs text-muted-foreground tabular-nums">
                  <IdCard className="size-3" />
                  {codigo_dizimista}
                </span>
              )}
            </span>
          );
        },
        enableSorting: false,
        size: 150,
      },
      {
        id: 'status',
        accessorKey: 'status',
        header: ({ column }) => (
          <DataGridColumnHeader
            title="Situação"
            column={column}
            icon={<Activity className="size-3.5 shrink-0 opacity-60" aria-hidden />}
          />
        ),
        cell: ({ row }) => {
          const status = row.original.status;
          return (
            <Badge variant={STATUS_VARIANT[status] ?? 'secondary'} appearance="outline" size="sm">
              {status}
            </Badge>
          );
        },
        enableSorting: true,
        size: 120,
      },
      {
        id: 'rg',
        accessorKey: 'rg',
        header: ({ column }) => (
          <DataGridColumnHeader
            title="RG"
            column={column}
            icon={<IdCard className="size-3.5 shrink-0 opacity-60" aria-hidden />}
          />
        ),
        cell: ({ row }) => (
          <span className="text-sm tabular-nums">{row.original.rg ?? '—'}</span>
        ),
        enableSorting: false,
        size: 130,
      },
      {
        id: 'data_nascimento',
        accessorKey: 'data_nascimento',
        header: ({ column }) => (
          <DataGridColumnHeader
            title="Nascimento"
            column={column}
            icon={<CalendarDays className="size-3.5 shrink-0 opacity-60" aria-hidden />}
          />
        ),
        cell: ({ row }) => (
          <span className="text-sm tabular-nums">
            {row.original.data_nascimento_formatted ?? '—'}
          </span>
        ),
        enableSorting: true,
        size: 130,
      },
      {
        id: 'created_at',
        accessorKey: 'created_at_formatted',
        header: ({ column }) => (
          <DataGridColumnHeader
            title="Cadastrado em"
            column={column}
            icon={<CalendarPlus className="size-3.5 shrink-0 opacity-60" aria-hidden />}
          />
        ),
        cell: ({ row }) => (
          <span className="text-sm tabular-nums">{row.original.created_at_formatted ?? '—'}</span>
        ),
        enableSorting: true,
        size: 140,
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
            <DropdownMenuContent align="end" className="w-48">
              <DropdownMenuItem onClick={() => onView?.(row.original.id)}>
                <Eye className="size-3.5" />
                Visualizar
              </DropdownMenuItem>
              <DropdownMenuItem onClick={() => onEdit?.(row.original.id)}>
                <Pencil className="size-3.5" />
                Editar
              </DropdownMenuItem>
              {row.original.dizimista && (
                <DropdownMenuItem onClick={() => setCarteirinhaFielId(row.original.id)}>
                  <Printer className="size-3.5" />
                  Ver carteirinha
                </DropdownMenuItem>
              )}
              <DropdownMenuSeparator />
              <DropdownMenuItem
                onClick={() => onDelete?.(row.original.id, row.original.nome_completo)}
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
    [onEdit, onView, onDelete],
  );

  // Algumas colunas iniciam ocultas (poderão ser reabilitadas pelo menu de colunas).
  const [columnVisibility, setColumnVisibility] = useState<VisibilityState>({
    rg: false,
    data_nascimento: false,
    created_at: false,
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

  // ── Seleção em massa para imprimir carteirinhas ──────────────────────────
  // O `rowSelection` guarda IDs (string) de TODAS as linhas selecionadas,
  // inclusive de outras páginas (já que usamos `getRowId: String(row.id)`).
  const selectedIdsAll = useMemo(
    () => Object.keys(rowSelection).filter((k) => rowSelection[k]),
    [rowSelection],
  );

  // Para detectar se há não-dizimistas, contamos só com base no que está
  // visível na página atual (não temos dados das outras páginas em memória).
  const visibleSelectedRows = useMemo(
    () => table.getSelectedRowModel().rows,
    // eslint-disable-next-line react-hooks/exhaustive-deps
    [rowSelection, data],
  );
  const visibleNaoDizimistas = useMemo(
    () => visibleSelectedRows.filter((r) => !r.original.dizimista).length,
    [visibleSelectedRows],
  );

  const totalSelecionados = selectedIdsAll.length;
  const showBulkBar = totalSelecionados > 0;
  const LOTE_MAX = 60;

  const handleImprimirSelecionados = useCallback(() => {
    if (selectedIdsAll.length === 0) return;
    if (selectedIdsAll.length > LOTE_MAX) {
      // simples aviso visual via alerta nativo — evita dependência extra
      window.alert(`Selecione no máximo ${LOTE_MAX} fiéis por lote.`);
      return;
    }
    const ids = selectedIdsAll.join(',');
    window.open(
      `/relatorios/fieis/carteirinhas/pdf?ids=${ids}`,
      '_blank',
      'noopener',
    );
  }, [selectedIdsAll]);

  const handleClearSelection = useCallback(() => {
    setRowSelection({});
  }, []);

  return (
    <DataGrid
      table={table}
      recordCount={meta.total}
      tableLayout={{
        columnsPinnable: true,
        columnsMovable: false,
        columnsVisibility: true,
        cellBorder: true,
        width: 'auto',
      }}
    >
      <Card>
        <FieisStatsBar
          stats={stats}
          activeKey={activeTab}
          onTabClick={handleTabClick}
        />

        <FieisAdvancedFiltersScope value={advancedFilters} onChange={onAdvancedFiltersChange}>
          <CardHeader className="flex flex-col items-stretch justify-start gap-3 py-4 min-h-0">
            <div className="overflow-x-auto -mx-4 px-4">
              <div className="flex min-w-fit w-full flex-nowrap items-center gap-2">
                {/* Busca */}
                <div className="relative shrink-0 w-52 min-w-[200px]">
                  <Search className="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-blue-700/50 dark:text-blue-200/45 pointer-events-none" />
                  <Input
                    className={cn(financeiroToolbarSoftBlueInputClass, 'h-10 rounded-md ps-9 pe-9')}
                    placeholder="Buscar fiel por nome, CPF ou contato..."
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

                {/* Mais filtros */}
                <FieisAdvancedFiltersTrigger />

                <div className="ms-auto flex shrink-0 items-center justify-end gap-2">
                  <CardToolbar>
                    <Button
                      variant="outline"
                      size="sm"
                      type="button"
                      className={cn(financeiroToolbarSoftBlueClass, 'h-10 min-w-10 px-3')}
                      onClick={refetch}
                      disabled={loading}
                      aria-label="Atualizar"
                    >
                      <RefreshCw className={cn('size-4', loading && 'animate-spin')} />
                    </Button>
                  </CardToolbar>
                </div>
              </div>
            </div>

            {/* Chips dos filtros avançados ativos */}
            <FieisAdvancedFiltersChipsSection />
          </CardHeader>
        </FieisAdvancedFiltersScope>

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

        {showBulkBar && (
          <div className="border-t border-border bg-blue-50/70 dark:bg-blue-900/15">
            <div className="flex flex-wrap items-center justify-between gap-3 px-4 py-2.5">
              <div className="flex flex-col gap-0.5 text-sm">
                <span className="font-medium text-foreground">
                  {totalSelecionados} {totalSelecionados === 1 ? 'fiel selecionado' : 'fiéis selecionados'}
                </span>
                {visibleNaoDizimistas > 0 && (
                  <span className="text-xs text-muted-foreground">
                    {visibleNaoDizimistas}{' '}
                    {visibleNaoDizimistas === 1
                      ? 'selecionado nesta página não é dizimista e será ignorado.'
                      : 'selecionados nesta página não são dizimistas e serão ignorados.'}
                  </span>
                )}
                {totalSelecionados > LOTE_MAX && (
                  <span className="text-xs text-destructive">
                    Limite por lote: {LOTE_MAX} fiéis. Reduza a seleção para imprimir.
                  </span>
                )}
              </div>
              <div className="flex items-center gap-2">
                <Button
                  type="button"
                  size="sm"
                  variant="outline"
                  onClick={handleClearSelection}
                  className="gap-1.5"
                >
                  <X className="size-3.5" />
                  Limpar seleção
                </Button>
                <Button
                  type="button"
                  size="sm"
                  onClick={handleImprimirSelecionados}
                  disabled={totalSelecionados === 0 || totalSelecionados > LOTE_MAX}
                  className="gap-1.5 bg-blue-600 hover:bg-blue-700 text-white border-0"
                >
                  <PrinterCheck className="size-3.5" />
                  Imprimir todas as carteirinhas
                </Button>
              </div>
            </div>
          </div>
        )}

        <CardFooter>
          <DataGridPagination />
        </CardFooter>
      </Card>

      <CarteirinhaDialog
        open={carteirinhaFielId !== null}
        onOpenChange={(open) => {
          if (!open) setCarteirinhaFielId(null);
        }}
        fielId={carteirinhaFielId}
      />
    </DataGrid>
  );
}
