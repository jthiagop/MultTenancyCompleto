import type { ReactNode } from 'react';
import { Loader2, Search, X } from 'lucide-react';
import { PeriodPicker, type PeriodValue } from '@/components/ui/period-picker';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
  financeiroToolbarSoftBlueClass,
  financeiroToolbarSoftBlueInputClass,
} from '@/lib/financeiro-toolbar-accent';
import { cn } from '@/lib/utils';

export interface TransacaoFiltersRowProps {
  period: PeriodValue;
  onPeriodChange: (value: PeriodValue) => void;
  searchQuery: string;
  onSearchChange: (value: string) => void;
  searchPlaceholder?: string;
  /** Indica carregamento — exibe spinner no ícone de busca. */
  loading?: boolean;
  /** Se fornecido, o ícone de lupa vira botão clicável e Enter também o aciona. */
  onSearch?: () => void;
  /** Inserido entre o período e o campo de busca. */
  betweenPeriodAndSearch?: ReactNode;
  /** Inserido após o campo de busca (ex.: botão "Mais filtros"). */
  afterSearch?: ReactNode;
  /** Renderizado abaixo da linha de filtros (ex.: chips de filtros avançados). */
  extraBelow?: ReactNode;
  /** Classe aplicada ao wrapper externo. */
  className?: string;
  /** Classe aplicada à linha de filtros. */
  rowClassName?: string;
}

/**
 * Linha de filtros reutilizável: período + busca + slot afterSearch + chips abaixo.
 * Usada dentro de `FinanceiroTransacaoTableCardHeader` (tabelas) e no `SheetBuscarLancamento`.
 */
export function TransacaoFiltersRow({
  period,
  onPeriodChange,
  searchQuery,
  onSearchChange,
  searchPlaceholder = 'Buscar...',
  loading = false,
  onSearch,
  betweenPeriodAndSearch,
  afterSearch,
  extraBelow,
  className,
  rowClassName,
}: TransacaoFiltersRowProps) {
  return (
    <div className={cn('flex min-w-0 flex-1 flex-col gap-2', className)}>
      <div className={cn('flex flex-wrap items-center gap-2', rowClassName)}>
        <PeriodPicker
          value={period}
          onChange={onPeriodChange}
          buttonClassName={financeiroToolbarSoftBlueClass}
        />

        {betweenPeriodAndSearch}

        <div className="relative shrink-0">
          {onSearch ? (
            <button
              type="button"
              onClick={onSearch}
              className="pointer-events-auto absolute start-3 top-1/2 -translate-y-1/2 text-blue-600/55 hover:text-blue-600 dark:text-blue-300/60 dark:hover:text-blue-300"
              aria-label="Pesquisar"
            >
              {loading
                ? <Loader2 className="size-4 animate-spin" />
                : <Search className="size-4" />}
            </button>
          ) : (
            <Search className="pointer-events-none absolute start-3 top-1/2 size-4 -translate-y-1/2 text-blue-600/55 dark:text-blue-300/60" />
          )}
          <Input
            placeholder={searchPlaceholder}
            value={searchQuery}
            onChange={(e) => onSearchChange(e.target.value)}
            onKeyDown={onSearch ? (e) => e.key === 'Enter' && onSearch() : undefined}
            className={cn(financeiroToolbarSoftBlueInputClass, 'h-10 w-52 rounded-md ps-9')}
          />
          {searchQuery.length > 0 && (
            <Button
              mode="icon"
              variant="ghost"
              className="absolute end-1.5 top-1/2 h-6 w-6 -translate-y-1/2 text-blue-700/80 hover:bg-blue-100/80 dark:text-blue-200 dark:hover:bg-blue-950/50"
              type="button"
              onClick={() => onSearchChange('')}
            >
              <X />
            </Button>
          )}
        </div>

        {afterSearch}
      </div>

      {extraBelow}
    </div>
  );
}
