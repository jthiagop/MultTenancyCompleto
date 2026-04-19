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
import { RefreshCw, Search, Settings2 } from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { CardFooter, CardHeader, CardTable, CardToolbar } from '@/components/ui/card';
import { DataGrid } from '@/components/ui/data-grid';
import { DataGridColumnHeader } from '@/components/ui/data-grid-column-header';
import { DataGridColumnVisibility } from '@/components/ui/data-grid-column-visibility';
import { DataGridPagination } from '@/components/ui/data-grid-pagination';
import { DataGridTable } from '@/components/ui/data-grid-table';
import { Input } from '@/components/ui/input';
import { ScrollArea, ScrollBar } from '@/components/ui/scroll-area';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';

export interface IOcupacao {
  id: number;
  codigo_tumulo: string;
  difunto_nome: string;
  data_entrada: string;
  data_saida: string | null;
  tipo_ocupacao: 'inumacao' | 'exumacao' | 'translado';
  numero_contrato: string | null;
}

const TIPO_LABEL: Record<IOcupacao['tipo_ocupacao'], string> = {
  inumacao: 'Inumação',
  exumacao: 'Exumação',
  translado: 'Translado',
};

function fmtDate(iso: string | null) {
  if (!iso) return '—';
  try {
    return format(parseISO(iso), 'dd/MM/yyyy', { locale: ptBR });
  } catch {
    return iso;
  }
}

export function OcupacoesTable({ refreshKey }: { refreshKey?: number }) {
  const [data, setData] = useState<IOcupacao[]>([]);
  const [total, setTotal] = useState(0);
  const [loading, setLoading] = useState(false);
  const [search, setSearch] = useState('');
  const [situacaoFilter, setSituacaoFilter] = useState<string>('all');
  const [pagination, setPagination] = useState<PaginationState>({ pageIndex: 0, pageSize: 20 });
  const [sorting, setSorting] = useState<SortingState>([{ id: 'data_entrada', desc: true }]);

  const sortCol = sorting[0];

  const loadData = useCallback(async () => {
    setLoading(true);
    try {
      const params = new URLSearchParams({
        page: String(pagination.pageIndex + 1),
        per_page: String(pagination.pageSize),
        ...(search ? { search } : {}),
        ...(situacaoFilter !== 'all' ? { situacao: situacaoFilter } : {}),
        ...(sortCol ? { sort_by: sortCol.id, sort_dir: sortCol.desc ? 'desc' : 'asc' } : {}),
      });
      const res = await fetch(`/cemiterio/ocupacoes?${params}`, {
        headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      });
      if (!res.ok || !res.headers.get('content-type')?.includes('application/json')) return;
      const json = await res.json();
      setData(json.data ?? []);
      setTotal(json.total ?? 0);
    } finally {
      setLoading(false);
    }
  }, [pagination.pageIndex, pagination.pageSize, search, situacaoFilter, sortCol]);

  useEffect(() => {
    loadData();
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [loadData, refreshKey]);

  const columns = useMemo<ColumnDef<IOcupacao>[]>(
    () => [
      {
        id: 'codigo_tumulo',
        accessorKey: 'codigo_tumulo',
        header: ({ column }) => <DataGridColumnHeader column={column} title="Túmulo" />,
        cell: ({ row }) => <span className="font-medium text-sm">{row.original.codigo_tumulo}</span>,
        enableSorting: true,
        size: 120,
      },
      {
        id: 'difunto_nome',
        accessorKey: 'difunto_nome',
        header: ({ column }) => <DataGridColumnHeader column={column} title="Difunto" />,
        cell: ({ row }) => <span className="text-sm">{row.original.difunto_nome}</span>,
        enableSorting: true,
        size: 220,
      },
      {
        id: 'tipo_ocupacao',
        accessorKey: 'tipo_ocupacao',
        header: ({ column }) => <DataGridColumnHeader column={column} title="Tipo" />,
        cell: ({ row }) => (
          <span className="text-sm text-muted-foreground">{TIPO_LABEL[row.original.tipo_ocupacao]}</span>
        ),
        enableSorting: false,
        size: 120,
      },
      {
        id: 'data_entrada',
        accessorKey: 'data_entrada',
        header: ({ column }) => <DataGridColumnHeader column={column} title="Entrada" />,
        cell: ({ row }) => (
          <span className="text-sm tabular-nums">{fmtDate(row.original.data_entrada)}</span>
        ),
        enableSorting: true,
        size: 120,
      },
      {
        id: 'data_saida',
        accessorKey: 'data_saida',
        header: ({ column }) => <DataGridColumnHeader column={column} title="Saída" />,
        cell: ({ row }) => (
          <span className="text-sm text-muted-foreground tabular-nums">
            {fmtDate(row.original.data_saida)}
          </span>
        ),
        enableSorting: false,
        size: 120,
      },
      {
        id: 'numero_contrato',
        accessorKey: 'numero_contrato',
        header: ({ column }) => <DataGridColumnHeader column={column} title="Contrato" />,
        cell: ({ row }) => (
          <span className="text-sm text-muted-foreground">{row.original.numero_contrato ?? '—'}</span>
        ),
        enableSorting: false,
        size: 140,
      },
      {
        id: 'situacao',
        header: ({ column }) => <DataGridColumnHeader column={column} title="Situação" />,
        cell: ({ row }) =>
          row.original.data_saida ? (
            <Badge variant="secondary" appearance="outline" size="sm">Encerrada</Badge>
          ) : (
            <Badge variant="success" appearance="outline" size="sm">Ativa</Badge>
          ),
        enableSorting: false,
        size: 110,
      },
    ],
    [],
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
                placeholder="Buscar túmulo ou difunto..."
                value={search}
                onChange={(e) => {
                  setSearch(e.target.value);
                  setPagination((p) => ({ ...p, pageIndex: 0 }));
                }}
                className="pl-8 h-10 w-64"
              />
            </div>

            <Select
              value={situacaoFilter}
              onValueChange={(v) => {
                setSituacaoFilter(v);
                setPagination((p) => ({ ...p, pageIndex: 0 }));
              }}
            >
              <SelectTrigger className="h-10 w-40">
                <SelectValue placeholder="Situação" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">Todas</SelectItem>
                <SelectItem value="ativa">Ativa</SelectItem>
                <SelectItem value="encerrada">Encerrada</SelectItem>
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
