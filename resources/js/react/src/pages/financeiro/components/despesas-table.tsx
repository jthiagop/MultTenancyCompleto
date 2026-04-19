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
  VisibilityState,
} from '@tanstack/react-table';
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
  type StatsContas,
  type TransacaoAdvancedFiltersState,
} from '@/hooks/useTransacoes';
import {
  fmtCurrency,
  SITUACAO_VARIANT,
  DescricaoCell,
  OrigemCell,
  TransacaoActionsCell,
  type SituacaoColor,
  type OnEditTransacao,
  type OnInformarPagamento,
  type OnOpenTransacaoDetalhes,
  type OnOpenRecibo,
} from './transacao-table-shared';

export function DespesasTable({
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
  const [sorting, setSorting] = useState<SortingState>([{ id: 'vencimento', desc: true }]);
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

  const { data, stats, pagination: meta, loading, error, refetch } = useTransacoes({
    tipo:      'saida',
    tab:       'contas_pagar',
    startDate: period.startDate,
    endDate:   period.endDate,
    search:    searchQuery || undefined,
    status:    activeStatKey !== 'total' ? activeStatKey : undefined,
    page:      pagination.pageIndex + 1,
    perPage:   pagination.pageSize,
    sortBy:    sortCol?.id,
    sortDir:   sortCol?.desc ? 'desc' : 'asc',
    advancedFilters,
  });

  const statsContas = stats as StatsContas | null;

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
        id: 'vencimento',
        accessorKey: 'vencimento',
        header: ({ column }) => <DataGridColumnHeader title="Vencimento" column={column} />,
        cell: ({ row }) => (
          <span className="text-foreground font-normal tabular-nums">
            {row.original.vencimento ?? '—'}
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
        size: 260,
      },
      {
        id: 'valor',
        accessorKey: 'valor',
        header: ({ column }) => <DataGridColumnHeader title="Total (R$)" column={column} />,
        cell: ({ row }) => (
          <span className="font-semibold tabular-nums text-destructive">
            {fmtCurrency(row.original.valor)}
          </span>
        ),
        enableSorting: true,
        size: 130,
      },
      {
        id: 'valor_restante',
        accessorKey: 'valor_restante',
        header: ({ column }) => <DataGridColumnHeader title="A Pagar (R$)" column={column} />,
        cell: ({ row }) => {
          const v = row.original.valor_restante;
          return (
            <span className={`font-semibold tabular-nums ${v <= 0 ? 'text-success' : 'text-destructive'}`}>
              {fmtCurrency(v)}
            </span>
          );
        },
        enableSorting: true,
        size: 130,
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
        id: 'origem',
        accessorKey: 'origem',
        header: ({ column }) => <DataGridColumnHeader title="Origem" column={column} />,
        cell: ({ row }) => (
          <OrigemCell
            origem={row.original.origem}
            origemNome={row.original.origem_nome}
            origemAgencia={row.original.origem_agencia}
            origemConta={row.original.origem_conta}
          />
        ),
        enableSorting: false,
        size: 160,
      },
      // ── Colunas extras (ocultas por padrão) ──────────────────────────────────
      {
        id: 'parceiro',
        accessorKey: 'parceiro',
        header: ({ column }) => <DataGridColumnHeader title="Fornecedor / Cliente" column={column} />,
        cell: ({ row }) => <span className="truncate">{row.original.parceiro ?? '—'}</span>,
        enableSorting: false,
        size: 180,
      },
      {
        id: 'categoria',
        accessorKey: 'categoria',
        header: ({ column }) => <DataGridColumnHeader title="Categoria" column={column} />,
        cell: ({ row }) => <span className="truncate">{row.original.categoria ?? '—'}</span>,
        enableSorting: false,
        size: 180,
      },
      {
        id: 'centro_custo',
        accessorKey: 'centro_custo',
        header: ({ column }) => <DataGridColumnHeader title="Centro de custo" column={column} />,
        cell: ({ row }) => <span className="truncate">{row.original.centro_custo ?? '—'}</span>,
        enableSorting: false,
        size: 160,
      },
      {
        id: 'conta',
        accessorKey: 'conta',
        header: ({ column }) => <DataGridColumnHeader title="Conta" column={column} />,
        cell: ({ row }) => <span className="truncate">{row.original.conta ?? '—'}</span>,
        enableSorting: false,
        size: 160,
      },
      {
        id: 'data_competencia',
        accessorKey: 'data_competencia',
        header: ({ column }) => <DataGridColumnHeader title="Competência" column={column} />,
        cell: ({ row }) => <span className="tabular-nums">{row.original.data_competencia ?? '—'}</span>,
        enableSorting: false,
        size: 120,
      },
      {
        id: 'data_pagamento',
        accessorKey: 'data_pagamento',
        header: ({ column }) => <DataGridColumnHeader title="Pagamento" column={column} />,
        cell: ({ row }) => <span className="tabular-nums">{row.original.data_pagamento ?? '—'}</span>,
        enableSorting: false,
        size: 120,
      },
      {
        id: 'valor_pago',
        accessorKey: 'valor_pago',
        header: ({ column }) => <DataGridColumnHeader title="Valor pago (R$)" column={column} />,
        cell: ({ row }) => <span className="tabular-nums font-semibold">{fmtCurrency(row.original.valor_pago ?? 0)}</span>,
        enableSorting: false,
        size: 140,
      },
      {
        id: 'juros',
        accessorKey: 'juros',
        header: ({ column }) => <DataGridColumnHeader title="Juros (R$)" column={column} />,
        cell: ({ row }) => <span className="tabular-nums">{fmtCurrency(row.original.juros ?? 0)}</span>,
        enableSorting: false,
        size: 120,
      },
      {
        id: 'multa',
        accessorKey: 'multa',
        header: ({ column }) => <DataGridColumnHeader title="Multa (R$)" column={column} />,
        cell: ({ row }) => <span className="tabular-nums">{fmtCurrency(row.original.multa ?? 0)}</span>,
        enableSorting: false,
        size: 120,
      },
      {
        id: 'desconto',
        accessorKey: 'desconto',
        header: ({ column }) => <DataGridColumnHeader title="Desconto (R$)" column={column} />,
        cell: ({ row }) => <span className="tabular-nums">{fmtCurrency(row.original.desconto ?? 0)}</span>,
        enableSorting: false,
        size: 120,
      },
      {
        id: 'numero_documento',
        accessorKey: 'numero_documento',
        header: ({ column }) => <DataGridColumnHeader title="Nº documento" column={column} />,
        cell: ({ row }) => <span className="truncate">{row.original.numero_documento ?? '—'}</span>,
        enableSorting: false,
        size: 140,
      },
      {
        id: 'tipo_documento',
        accessorKey: 'tipo_documento',
        header: ({ column }) => <DataGridColumnHeader title="Tipo documento" column={column} />,
        cell: ({ row }) => <span className="truncate">{row.original.tipo_documento ?? '—'}</span>,
        enableSorting: false,
        size: 140,
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
        enableHiding: false,
        size: 56,
      },
    ],
    [onEdit, onInformarPagamento, onOpenDetalhes, onOpenRecibo, refetch],
  );

  const [columnVisibility, setColumnVisibility] = useState<VisibilityState>({
    parceiro: false,
    categoria: false,
    centro_custo: false,
    conta: false,
    data_competencia: false,
    data_pagamento: false,
    valor_pago: false,
    juros: false,
    multa: false,
    desconto: false,
    numero_documento: false,
    tipo_documento: false,
  });

  const table = useReactTable({
    columns,
    data,
    pageCount: meta.last_page,
    getRowId: (row) => row.id,
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

  return (
    <DataGrid
      table={table}
      recordCount={meta.total}
      onRowClick={onOpenDetalhes ? (row) => onOpenDetalhes(String(row.id)) : undefined}
      tableLayout={{ columnsPinnable: true, columnsMovable: true, columnsVisibility: true, cellBorder: true, width: 'auto' }}
    >
      <Card>
        <SummaryStatsBar
          activeKey={activeStatKey}
          onTabClick={(key) => {
            setActiveStatKey(key);
            setPagination((p) => ({ ...p, pageIndex: 0 }));
          }}
          stats={[
            { statKey: 'vencidos',  label: 'Vencidos (R$)',        value: statsContas?.vencidos  ?? 0, colorClass: 'text-rose-500',    accentColor: '#f43f5e' },
            { statKey: 'hoje',      label: 'Vencem hoje (R$)',      value: statsContas?.hoje      ?? 0, colorClass: 'text-pink-500',    accentColor: '#ec4899' },
            { statKey: 'a_vencer',  label: 'A vencer (R$)',         value: statsContas?.a_vencer  ?? 0, colorClass: 'text-blue-500',    accentColor: '#3b82f6' },
            { statKey: 'pagos',     label: 'Pagos (R$)',            value: statsContas?.pagos     ?? 0, colorClass: 'text-emerald-500', accentColor: '#10b981' },
            { statKey: 'total',     label: 'Total do período (R$)', value: statsContas?.total     ?? 0, colorClass: 'text-blue-600',    accentColor: '#2563eb' },
          ]}
        />
        <TransacaoAdvancedFiltersScope
          value={advancedFilters}
          onChange={onAdvancedFiltersChange}
          tipoFormData="despesa"
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
            searchPlaceholder="Buscar despesas..."
            loading={loading}
            refetch={refetch}
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
