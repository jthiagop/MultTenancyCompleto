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
import { PlusCircle, MoreHorizontal, RefreshCw, Search } from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { CardFooter, CardHeader, CardTable, CardToolbar } from '@/components/ui/card';
import { DataGrid } from '@/components/ui/data-grid';
import { DataGridColumnHeader } from '@/components/ui/data-grid-column-header';
import { DataGridPagination } from '@/components/ui/data-grid-pagination';
import { DataGridTable } from '@/components/ui/data-grid-table';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import { ScrollArea, ScrollBar } from '@/components/ui/scroll-area';
import { Tabs, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { LancamentoCemiterioSheet } from '@/pages/cemiterio/components/lancamento-cemiterio-sheet';

export interface ICobranca {
  id: number;
  sepultura_id: number | null;
  sepultado_id: number | null;
  sepultado_nome: string | null;
  tipo_cobranca: 'tumulo' | 'difunto';
  titulo: string;
  responsavel: string;
  descricao: string;
  data_vencimento: string | null;
  data_pagamento: string | null;
  valor: number;
  valor_pago: number;
  situacao: string;
  situacao_label: string;
}

const SITUACAO_VARIANT: Record<string, 'success' | 'destructive' | 'warning' | 'secondary'> = {
  recebido:      'success',
  pago:          'success',
  atrasado:      'destructive',
  em_aberto:     'warning',
  desconsiderado: 'secondary',
};

function fmtDate(iso: string | null) {
  if (!iso) return '—';
  try { return format(parseISO(iso), 'dd/MM/yyyy', { locale: ptBR }); }
  catch { return iso; }
}

function fmtBRL(v: number) {
  return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(v);
}

export function CobrancasTab({
  refreshKey = 0,
  onSaved,
}: {
  refreshKey?: number;
  onSaved?: () => void;
}) {
  const [data, setData] = useState<ICobranca[]>([]);
  const [total, setTotal] = useState(0);
  const [loading, setLoading] = useState(false);
  const [search, setSearch] = useState('');
  const [activeSubTab, setActiveSubTab] = useState('all');
  const [de, setDe] = useState('');
  const [ate, setAte] = useState('');
  const [sorting, setSorting] = useState<SortingState>([{ id: 'data_vencimento', desc: false }]);
  const [pagination, setPagination] = useState<PaginationState>({ pageIndex: 0, pageSize: 20 });
  const [lançarOpen, setLançarOpen] = useState(false);
  const [internalKey, setInternalKey] = useState(0);

  const effectiveRefreshKey = refreshKey + internalKey;

  const fetchData = useCallback(async () => {
    setLoading(true);
    try {
      const params = new URLSearchParams({
        page:     String(pagination.pageIndex + 1),
        per_page: String(pagination.pageSize),
        search,
        sort_by:  sorting[0]?.id ?? 'data_vencimento',
        sort_dir: sorting[0]?.desc ? 'desc' : 'asc',
      });

      if (activeSubTab !== 'all') params.set('situacao', activeSubTab);
      if (de)  params.set('de', de);
      if (ate) params.set('ate', ate);

      const res = await fetch(`/cemiterio/cobrancas?${params}`, {
        headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin',
      });
      if (!res.ok) return;
      const json = await res.json();
      if (json.success) {
        setData(json.data ?? []);
        setTotal(json.total ?? 0);
      }
    } catch { /* ignore */ } finally {
      setLoading(false);
    }
  }, [pagination, search, sorting, activeSubTab, de, ate, effectiveRefreshKey]);

  useEffect(() => { fetchData(); }, [fetchData]);

  function handleSaved() {
    setInternalKey((k) => k + 1);
    onSaved?.();
  }

  const columns = useMemo<ColumnDef<ICobranca>[]>(() => [
    {
      accessorKey: 'titulo',
      header: ({ column }) => <DataGridColumnHeader column={column} title="Referência" />,
      cell: ({ row }) => {
        const { titulo, tipo_cobranca } = row.original;
        return (
          <span className="flex items-center gap-1.5">
            <Badge
              variant={tipo_cobranca === 'tumulo' ? 'secondary' : 'outline'}
              size="sm"
            >
              {tipo_cobranca === 'tumulo' ? 'Túmulo' : 'Difunto'}
            </Badge>
            <span className="font-medium">{titulo || '—'}</span>
          </span>
        );
      },
    },
    {
      accessorKey: 'responsavel',
      header: ({ column }) => <DataGridColumnHeader column={column} title="Responsável" />,
      cell: ({ row }) => row.original.responsavel || <span className="text-muted-foreground text-xs">—</span>,
    },
    {
      accessorKey: 'descricao',
      header: ({ column }) => <DataGridColumnHeader column={column} title="Descrição" />,
      cell: ({ row }) => (
        <span className="max-w-[240px] truncate block text-sm">{row.original.descricao || '—'}</span>
      ),
    },
    {
      accessorKey: 'data_vencimento',
      header: ({ column }) => <DataGridColumnHeader column={column} title="Vencimento" />,
      cell: ({ row }) => fmtDate(row.original.data_vencimento),
    },
    {
      accessorKey: 'valor',
      header: ({ column }) => <DataGridColumnHeader column={column} title="Valor" />,
      cell: ({ row }) => fmtBRL(row.original.valor),
    },
    {
      accessorKey: 'situacao',
      header: ({ column }) => <DataGridColumnHeader column={column} title="Situação" />,
      cell: ({ row }) => {
        const s = row.original.situacao;
        return (
          <Badge variant={SITUACAO_VARIANT[s] ?? 'secondary'} appearance="outline" size="sm">
            {row.original.situacao_label || s}
          </Badge>
        );
      },
    },
    {
      id: 'actions',
      header: () => null,
      enableSorting: false,
      cell: ({ row }) => (
        <DropdownMenu>
          <DropdownMenuTrigger asChild>
            <Button variant="ghost" size="sm" className="size-7 p-0">
              <MoreHorizontal className="size-4" />
            </Button>
          </DropdownMenuTrigger>
          <DropdownMenuContent align="end" className="z-65">
            <DropdownMenuItem disabled className="text-xs text-muted-foreground">
              ID #{row.original.id}
            </DropdownMenuItem>
          </DropdownMenuContent>
        </DropdownMenu>
      ),
    },
  ], []);

  const table = useReactTable({
    data,
    columns,
    pageCount: Math.ceil(total / pagination.pageSize),
    state: { sorting, pagination },
    manualPagination: true,
    manualSorting: true,
    onSortingChange: setSorting,
    onPaginationChange: setPagination,
    getCoreRowModel: getCoreRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
    getSortedRowModel: getSortedRowModel(),
  });

  return (
    <DataGrid
      table={table}
      recordCount={total}
      isLoading={loading}
    >
      <CardHeader className="flex-col items-stretch gap-3 py-4 min-h-0">
        {/* Sub-tabs de situação */}
        <Tabs value={activeSubTab} onValueChange={(v) => { setActiveSubTab(v); setPagination((p) => ({ ...p, pageIndex: 0 })); }}>
          <TabsList variant="line" className="h-auto gap-4">
            <TabsTrigger value="all" className="text-xs">Todas</TabsTrigger>
            <TabsTrigger value="a_receber" className="text-xs">A Receber</TabsTrigger>
            <TabsTrigger value="em_atraso" className="text-xs">Em Atraso</TabsTrigger>
            <TabsTrigger value="recebido" className="text-xs">Recebidas</TabsTrigger>
            <TabsTrigger value="cancelado" className="text-xs">Canceladas</TabsTrigger>
          </TabsList>
        </Tabs>

        {/* Barra de filtros + ações */}
        <div className="flex flex-wrap items-center gap-2">
          <div className="relative flex items-center min-w-0 flex-1 max-w-xs">
            <Search className="absolute left-2.5 size-4 text-muted-foreground pointer-events-none" />
            <Input
              placeholder="Buscar sepultura, responsável..."
              className="pl-8 h-8 text-sm"
              value={search}
              onChange={(e) => { setSearch(e.target.value); setPagination((p) => ({ ...p, pageIndex: 0 })); }}
            />
          </div>
          <Input
            type="date"
            className="h-8 text-sm w-36"
            value={de}
            onChange={(e) => { setDe(e.target.value); setPagination((p) => ({ ...p, pageIndex: 0 })); }}
            title="De"
          />
          <Input
            type="date"
            className="h-8 text-sm w-36"
            value={ate}
            onChange={(e) => { setAte(e.target.value); setPagination((p) => ({ ...p, pageIndex: 0 })); }}
            title="Até"
          />
          <div className="ms-auto flex items-center gap-2">
            <CardToolbar>
              <Button variant="outline" size="sm" onClick={fetchData} disabled={loading}>
                <RefreshCw className={`size-4 ${loading ? 'animate-spin' : ''}`} />
                Atualizar
              </Button>
              <Button size="sm" onClick={() => setLançarOpen(true)}>
                <PlusCircle className="size-4" />
                Lançar Taxa
              </Button>
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

      <LancamentoCemiterioSheet
        open={lançarOpen}
        onOpenChange={setLançarOpen}
        onSaved={handleSaved}
      />
    </DataGrid>
  );
}
