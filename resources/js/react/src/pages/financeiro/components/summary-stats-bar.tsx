import { cn } from '@/lib/utils';

export interface StatItem {
  statKey: string;
  label: string;
  value: number;
  colorClass: string;
  accentColor: string;
}

interface SummaryStatsBarProps {
  stats: StatItem[];
  activeKey: string;
  onTabClick: (key: string) => void;
}

const formatCurrency = (value: number) =>
  new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' })
    .format(value)
    .replace('R$\u00a0', '');

export function SummaryStatsBar({ stats, activeKey, onTabClick }: SummaryStatsBarProps) {
  return (
    // bg-muted/40: mesma tonalidade do <thead> das tabelas (data-grid-table.tsx)
    // — mantém continuidade visual com o header das tabelas logo abaixo.
    <div className="flex w-full border-b border-border bg-muted/40">
      {stats.map((stat, index) => {
        const isActive = activeKey === stat.statKey;
        return (
          <button
            key={stat.statKey}
            type="button"
            onClick={() => onTabClick(stat.statKey)}
            className={cn(
              'flex-1 flex flex-col items-center justify-center py-4 px-3 relative transition-colors focus-visible:outline-none',
              index < stats.length - 1 && 'border-r border-border',
              // Ativo: clareia para o tom do conteúdo (efeito "aba elevada");
              // hover (inativo): escurece um pouco em relação ao fundo.
              isActive ? 'bg-background' : 'hover:bg-muted/70 cursor-pointer',
            )}
          >
            {isActive && (
              <span
                className="absolute bottom-0 left-0 right-0 h-0.5"
                style={{ backgroundColor: stat.accentColor }}
              />
            )}
            <span className="text-xs font-medium text-muted-foreground mb-1 whitespace-nowrap">
              {stat.label}
            </span>
            <span className={cn('text-xl font-bold tabular-nums', stat.colorClass)}>
              {formatCurrency(stat.value)}
            </span>
          </button>
        );
      })}
    </div>
  );
}
