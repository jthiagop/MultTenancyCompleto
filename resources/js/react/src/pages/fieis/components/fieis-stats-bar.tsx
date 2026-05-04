import { HeartHandshake, UserCheck, Users } from 'lucide-react';
import { cn } from '@/lib/utils';
import { type FieisStats } from '@/hooks/useFieis';

// ── Tipos ────────────────────────────────────────────────────────────────────

export type FieisStatKey = 'todos' | 'masculino' | 'feminino' | 'dizimista' | 'ativos';

// ── Ícones de gênero ──────────────────────────────────────────────────────────

function IconMale({ className }: { className?: string }) {
  return (
    <svg
      viewBox="0 0 24 24"
      fill="none"
      strokeWidth={1.8}
      stroke="currentColor"
      className={className}
      aria-hidden
    >
      <circle cx="10" cy="14" r="5" />
      <path d="M19 5h-5m5 0v5m0-5-6.5 6.5" strokeLinecap="round" strokeLinejoin="round" />
    </svg>
  );
}

function IconFemale({ className }: { className?: string }) {
  return (
    <svg
      viewBox="0 0 24 24"
      fill="none"
      strokeWidth={1.8}
      stroke="currentColor"
      className={className}
      aria-hidden
    >
      <circle cx="12" cy="9" r="5" />
      <path d="M12 14v6m-3-3h6" strokeLinecap="round" />
    </svg>
  );
}

interface TabDef {
  key: FieisStatKey;
  label: string;
  icon: React.ReactNode;
  accentColor: string;
  /** Cor do valor principal (igual `colorClass` do SummaryStatsBar das receitas/despesas). */
  colorClass: string;
  showPercent?: boolean;
  getValue: (stats: FieisStats) => number;
}

const TABS: TabDef[] = [
  {
    key: 'todos',
    label: 'Todos',
    icon: <Users className="size-3.5 shrink-0" />,
    accentColor: '#2563eb',
    colorClass: 'text-blue-600',
    showPercent: false,
    getValue: (s) => s.total,
  },
  {
    key: 'masculino',
    label: 'Homem',
    icon: <IconMale className="size-3.5 shrink-0" />,
    accentColor: '#6366f1',
    colorClass: 'text-indigo-600 dark:text-indigo-400',
    showPercent: true,
    getValue: (s) => s.masculino,
  },
  {
    key: 'feminino',
    label: 'Mulher',
    icon: <IconFemale className="size-3.5 shrink-0" />,
    accentColor: '#ec4899',
    colorClass: 'text-pink-600 dark:text-pink-400',
    showPercent: true,
    getValue: (s) => s.feminino,
  },
  {
    key: 'dizimista',
    label: 'Dizimista',
    icon: <HeartHandshake className="size-3.5 shrink-0" />,
    accentColor: '#10b981',
    colorClass: 'text-emerald-600 dark:text-emerald-400',
    showPercent: true,
    getValue: (s) => s.dizimista,
  },
  {
    key: 'ativos',
    label: 'Fiéis',
    icon: <UserCheck className="size-3.5 shrink-0" />,
    accentColor: '#0ea5e9',
    colorClass: 'text-sky-600 dark:text-sky-400',
    showPercent: false,
    getValue: (s) => s.ativos,
  },
];

// ── Componente (mesmo padrão visual do SummaryStatsBar / receitas / despesas) ─

export interface FieisStatsBarProps {
  stats: FieisStats;
  activeKey: FieisStatKey;
  onTabClick: (key: FieisStatKey) => void;
}

export function FieisStatsBar({ stats, activeKey, onTabClick }: FieisStatsBarProps) {
  const total = stats.total || 1;

  return (
    <div className="w-full border-b border-border bg-muted/40 overflow-x-auto">
      <div className="flex min-w-fit">
        {TABS.map((tab, index) => {
          const isActive = activeKey === tab.key;
          const count = tab.getValue(stats);
          const pct = tab.showPercent
            ? ((count / total) * 100).toLocaleString('pt-BR', {
                minimumFractionDigits: 1,
                maximumFractionDigits: 2,
              })
            : null;

          return (
            <button
              key={tab.key}
              type="button"
              onClick={() => onTabClick(tab.key)}
              className={cn(
                'flex-1 min-w-[160px] flex flex-col items-center justify-center py-4 px-3 relative transition-colors focus-visible:outline-none',
                index < TABS.length - 1 && 'border-r border-border',
                isActive ? 'bg-background' : 'hover:bg-muted/70 cursor-pointer',
              )}
              aria-pressed={isActive}
            >
              {isActive && (
                <span
                  className="absolute bottom-0 left-0 right-0 h-0.5"
                  style={{ backgroundColor: tab.accentColor }}
                />
              )}
              <span className="text-xs font-medium text-muted-foreground mb-1 whitespace-nowrap flex items-center justify-center gap-1">
                {tab.icon}
                {tab.label}
              </span>
              <span className={cn('text-xl font-bold tabular-nums whitespace-nowrap', tab.colorClass)}>
                {count.toLocaleString('pt-BR')}
                {pct !== null && (
                  <span className="text-sm font-semibold text-muted-foreground tabular-nums ms-1">
                    {pct}%
                  </span>
                )}
              </span>
            </button>
          );
        })}
      </div>
    </div>
  );
}
