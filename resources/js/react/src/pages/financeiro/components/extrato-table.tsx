import { useCallback, useEffect, useMemo, useState } from 'react';
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
import { Wallet } from 'lucide-react';
import { financeiroToolbarSoftBlueChipClass } from '@/lib/financeiro-toolbar-accent';
import { cn } from '@/lib/utils';
import { defaultPeriod, type PeriodValue } from '@/components/ui/period-picker';
import { Badge } from '@/components/ui/badge';
import {
  Card,
  CardFooter,
  CardTable,
} from '@/components/ui/card';
import { DataGrid } from '@/components/ui/data-grid';
import { DataGridColumnHeader } from '@/components/ui/data-grid-column-header';
import { DataGridPagination } from '@/components/ui/data-grid-pagination';
import {
  DataGridTable,
  DataGridTableRowSelect,
  DataGridTableRowSelectAll,
} from '@/components/ui/data-grid-table';
import { ScrollArea, ScrollBar } from '@/components/ui/scroll-area';
import { FinanceiroTransacaoTableCardHeader } from '@/pages/financeiro/components/financeiro-transacao-table-card-header';
import {
  TransacaoAdvancedFiltersChipsSection,
  TransacaoAdvancedFiltersScope,
  TransacaoAdvancedFiltersTrigger,
} from '@/pages/financeiro/components/transacao-advanced-filters-bar';
import { SummaryStatsBar } from '@/pages/financeiro/components/summary-stats-bar';
import {
  useTransacoes,
  type ITransacao,
  type StatsExtrato,
  type TransacaoAdvancedFiltersState,
} from '@/hooks/useTransacoes';
import {
  fmtCurrency,
  SITUACAO_VARIANT,
  DescricaoCell,
  TransacaoActionsCell,
  type SituacaoColor,
  type OnEditTransacao,
  type OnInformarPagamento,
  type OnOpenTransacaoDetalhes,
  type OnOpenRecibo,
} from './transacao-table-shared';

export function ExtratoTable({
  refreshKey,
  onEdit,
  onInformarPagamento,
  onOpenDetalhes,
  onOpenRecibo,
}: {
  refreshKey?: number;
  onEdit?: OnEditTransacao;
  onInformarPagamento?: OnInformarPagamento;
  onOpenDetalhes?: OnOpenTransacaoDetalhes;
  onOpenRecibo?: OnOpenRecibo;
}) {
  const [pagination, setPagination] = useState<PaginationState>({ pageIndex: 0, pageSize: 20 });
  const [sorting, setSorting] = useState<SortingState>([{ id: 'data', desc: true }]);
  const [rowSelection, setRowSelection] = useState<RowSelectionState>({});
  const [searchQuery, setSearchQuery] = useState('');
  const [activeStatKey, setActiveStatKey] = useState<string>('total');
  const [period, setPeriod] = useState<PeriodValue>(defaultPeriod);
  const [advancedFilters, setAdvancedFilters] = useState<TransacaoAdvancedFiltersState>({});

  const sortCol = sorting[0];

  const onAdvancedFiltersChange = useCallback((next: TransacaoAdvancedFiltersState) => {
    setAdvancedFilters(next);
    setPagination((p) => ({ ...p, pageIndex: 0 }));
  }, []);

  const { data, stats, pagination: meta, saldoAnterior, loading, error, refetch } = useTransacoes({
    tipo:      'all',
    tab:       'extrato',
    startDate: period.startDate,
    endDate:   period.endDate,
    search:    searchQuery || undefined,
    status:    activeStatKey !== 'total' ? activeStatKey : undefined,
    page:      pagination.pageIndex + 1,
    perPage:   pagination.pageSize,
    sortBy:    sortCol?.id === 'data' ? 'data' : sortCol?.id,
    sortDir:   sortCol?.desc ? 'desc' : 'asc',
    advancedFilters,
  });

  const statsExtrato = stats as StatsExtrato | null;

  useEffect(() => {
    if (refreshKey) refetch();
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [refreshKey]);

  const columns = useMemo<ColumnDef<ITransacao>[]>(
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
        id: 'data',
        accessorKey: 'data',
        header: ({ column }) => <DataGridColumnHeader title="Data" column={column} />,
        cell: ({ row }) => (
          <span className="text-foreground font-normal tabular-nums">
            {row.original.data ?? '—'}
          </span>
        ),
        enableSorting: true,
        size: 110,
      },
      {
        id: 'descricao',
        accessorKey: 'descricao',
        header: ({ column }) => <DataGridColumnHeader title="Descrição" column={column} />,
        cell: ({ row }) => <DescricaoCell row={row} />,
        enableSorting: true,
        size: 280,
      },
      {
        id: 'situacao',
        accessorKey: 'situacao',
        header: ({ column }) => <DataGridColumnHeader title="Situação" column={column} />,
        cell: ({ row }) => {
          const { situacao_label } = row.original;
          const variant = SITUACAO_VARIANT[row.original.situacao] ?? 'secondary';
          return (
            <Badge variant={variant as SituacaoColor} appearance="outline" size="sm">
              {situacao_label}
            </Badge>
          );
        },
        enableSorting: false,
        size: 130,
      },
      {
        id: 'valor',
        accessorKey: 'valor',
        header: ({ column }) => <DataGridColumnHeader title="Valor (R$)" column={column} />,
        cell: ({ row }) => {
          const isEntrada = row.original.tipo === 'entrada';
          return (
            <span className={`font-semibold tabular-nums ${isEntrada ? 'text-success' : 'text-destructive'}`}>
              {isEntrada ? '+' : '-'}{fmtCurrency(row.original.valor)}
            </span>
          );
        },
        enableSorting: true,
        size: 140,
      },
      {
        id: 'origem',
        accessorKey: 'origem',
        header: ({ column }) => <DataGridColumnHeader title="Conta" column={column} />,
        cell: ({ row }) => (
          <span className="text-foreground font-normal truncate text-sm">{row.original.origem}</span>
        ),
        enableSorting: false,
        size: 160,
      },
      {
        id: 'actions',
        header: '',
        cell: ({ row }) => (
          <TransacaoActionsCell
            row={row}
            onEdit={onEdit ?? (() => {})}
            onInformarPagamento={onInformarPagamento}
            onOpenDetalhes={onOpenDetalhes}
            onOpenRecibo={onOpenRecibo}
            onDeleted={refetch}
          />
        ),
        enableSorting: false,
        size: 56,
      },
    ],
    [onEdit, onInformarPagamento, onOpenDetalhes, onOpenRecibo, refetch],
  );

  const table = useReactTable({
    columns,
    data,
    pageCount: meta.last_page,
    getRowId: (row) => row.id,
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

  return (
    <DataGrid
      table={table}
      recordCount={meta.total}
      onRowClick={onOpenDetalhes ? (row) => onOpenDetalhes(String(row.id)) : undefined}
      tableLayout={{ columnsPinnable: true, columnsMovable: true, columnsVisibility: true, cellBorder: true }}
    >
      <Card>
        <SummaryStatsBar
          activeKey={activeStatKey}
          onTabClick={(key) => {
            setActiveStatKey(key);
            setPagination((p) => ({ ...p, pageIndex: 0 }));
          }}
          stats={[
            { statKey: 'receitas_aberto',      label: 'Receitas em Aberto',   value: statsExtrato?.receitas_aberto      ?? 0, colorClass: 'text-blue-500',    accentColor: '#3b82f6' },
            { statKey: 'receitas_realizadas',  label: 'Receitas Realizadas',  value: statsExtrato?.receitas_realizadas  ?? 0, colorClass: 'text-emerald-500', accentColor: '#10b981' },
            { statKey: 'despesas_aberto',      label: 'Despesas em Aberto',   value: statsExtrato?.despesas_aberto      ?? 0, colorClass: 'text-orange-500',  accentColor: '#f97316' },
            { statKey: 'despesas_realizadas',  label: 'Despesas Realizadas',  value: statsExtrato?.despesas_realizadas  ?? 0, colorClass: 'text-rose-500',    accentColor: '#f43f5e' },
            {
              statKey: 'total',
              label: 'Saldo do Período',
              value: statsExtrato?.total ?? 0,
              colorClass: (statsExtrato?.total ?? 0) >= 0 ? 'text-emerald-600' : 'text-rose-600',
              accentColor: (statsExtrato?.total ?? 0) >= 0 ? '#059669' : '#e11d48',
            },
          ]}
        />

        <TransacaoAdvancedFiltersScope
          value={advancedFilters}
          onChange={onAdvancedFiltersChange}
          tipoFormData="all"
        >
          <FinanceiroTransacaoTableCardHeader
            period={period}
            onPeriodChange={(v) => {
              setPeriod(v);
              setPagination((p) => ({ ...p, pageIndex: 0 }));
              setActiveStatKey('total');
            }}
            searchQuery={searchQuery}
            onSearchChange={(q) => {
              setSearchQuery(q);
              setPagination((p) => ({ ...p, pageIndex: 0 }));
            }}
            searchPlaceholder="Buscar no extrato..."
            loading={loading}
            refetch={refetch}
            headingRowClassName="flex-wrap"

            afterSearch={<TransacaoAdvancedFiltersTrigger />}
            extraBeforeToolbar={<TransacaoAdvancedFiltersChipsSection />}
          />
        </TransacaoAdvancedFiltersScope>

        {error && (
          <div className="px-4 py-2 text-sm text-destructive bg-destructive/10">{error}</div>
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
