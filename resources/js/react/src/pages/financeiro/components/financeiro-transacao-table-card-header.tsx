import type { ReactNode } from 'react';
import { RefreshCw } from 'lucide-react';
import { type PeriodValue } from '@/components/ui/period-picker';
import { Button } from '@/components/ui/button';
import { CardHeader, CardToolbar } from '@/components/ui/card';
import { financeiroToolbarSoftBlueClass } from '@/lib/financeiro-toolbar-accent';
import { cn } from '@/lib/utils';
import { TableToolbarBase } from '@/pages/financeiro/components/transacao-table-shared';
import { TransacaoFiltersRow } from '@/pages/financeiro/components/transacao-filters-row';

export interface FinanceiroTransacaoTableCardHeaderProps {
  period: PeriodValue;
  onPeriodChange: (value: PeriodValue) => void;
  searchQuery: string;
  onSearchChange: (value: string) => void;
  searchPlaceholder: string;
  loading: boolean;
  refetch: () => void;
  /** Inserido entre o período e o campo de busca (ex.: saldo anterior no extrato). */
  betweenPeriodAndSearch?: ReactNode;
  /** Ex.: `flex-wrap` no extrato — aplicado à linha de período/busca/filtros. */
  headingRowClassName?: string;
  /** Após o campo de busca, mesma linha (ex.: botão "Mais filtros"). */
  afterSearch?: ReactNode;
  /** Linha de chips de filtros avançados (abaixo da primeira linha). */
  extraBeforeToolbar?: ReactNode;
  /**
   * `data-grid` — atualizar + colunas (exige DataGridProvider).
   * `refresh-only` — só botão atualizar (telas sem DataGrid, ex.: movimentações conciliadas).
   */
  toolbarMode?: 'data-grid' | 'refresh-only';
}

/**
 * Cabeçalho comum das tabelas de receitas, despesas e extrato:
 * período, busca, filtros opcionais e ações da grid (atualizar / colunas) na mesma linha superior.
 */
export function FinanceiroTransacaoTableCardHeader({
  period,
  onPeriodChange,
  searchQuery,
  onSearchChange,
  searchPlaceholder,
  loading,
  refetch,
  betweenPeriodAndSearch,
  headingRowClassName,
  afterSearch,
  extraBeforeToolbar,
  toolbarMode = 'data-grid',
}: FinanceiroTransacaoTableCardHeaderProps) {
  return (
    <CardHeader className="flex flex-col items-stretch justify-start gap-3 py-4 min-h-0">
      <div
        className={cn(
          'flex min-w-0 w-full flex-wrap items-center gap-2',
          headingRowClassName,
        )}
      >
        <TransacaoFiltersRow
          period={period}
          onPeriodChange={onPeriodChange}
          searchQuery={searchQuery}
          onSearchChange={onSearchChange}
          searchPlaceholder={searchPlaceholder}
          betweenPeriodAndSearch={betweenPeriodAndSearch}
          afterSearch={afterSearch}
        />
        <div className="ms-auto flex min-w-0 shrink-0 flex-wrap items-center justify-end gap-2">
          {toolbarMode === 'refresh-only' ? (
            <CardToolbar>
              <Button
                variant="outline"
                size="sm"
                className={cn(financeiroToolbarSoftBlueClass, 'h-10 min-w-10 px-3')}
                type="button"
                onClick={refetch}
                disabled={loading}
                aria-label="Atualizar"
              >
                <RefreshCw className={`size-4 ${loading ? 'animate-spin' : ''}`} />
              </Button>
            </CardToolbar>
          ) : (
            <TableToolbarBase loading={loading} refetch={refetch} />
          )}
        </div>
      </div>

      {extraBeforeToolbar}
    </CardHeader>
  );
}
