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
      {/*
        Linha 1 (período + busca + filtros + atualizar/colunas):
        - flex-nowrap + overflow-x-auto: em telas pequenas mantém todos os
          controles em uma única linha e ativa scroll horizontal nativo,
          igual à tabela logo abaixo. Antes usávamos flex-wrap, que fazia
          os botões "deformarem" pulando para múltiplas linhas em mobile.
        - min-w-fit no inner garante que o flex container nunca fique
          menor que o conteúdo natural (caso contrário o flex tentaria
          comprimir e o overflow nunca ativaria).
      */}
      <div className="overflow-x-auto -mx-4 px-4">
        <div
          className={cn(
            'flex min-w-fit w-full flex-nowrap items-center gap-2',
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
            rowClassName="flex-nowrap"
          />
          <div className="ms-auto flex shrink-0 items-center justify-end gap-2">
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
      </div>

      {extraBeforeToolbar}
    </CardHeader>
  );
}
