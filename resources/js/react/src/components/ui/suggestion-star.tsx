import { Tooltip, TooltipContent, TooltipTrigger } from '@/components/ui/tooltip';
import { cn } from '@/lib/utils';
import { Lightbulb } from 'lucide-react';

export type OrigemSugestao = 'regra' | 'historico_parceiro' | 'historico_texto' | 'padrao' | string;

export function suggestionOrigemLabel(origem: OrigemSugestao | null | undefined): string {
  if (origem === 'regra') return 'Sugestão por regra aprendida';
  if (origem && origem.startsWith('historico')) return 'Sugestão por histórico';
  return 'Sugestão automática';
}

function tooltipLabel(origem: OrigemSugestao | null | undefined, confianca: number): string {
  if (origem === 'regra') {
    return `Sugestão baseada em regra aprendida (${confianca}%)`;
  }
  if (origem && origem.startsWith('historico')) {
    return `Sugestão baseada em transações anteriores (${confianca}%)`;
  }
  return `Sugestão automática (${confianca}%)`;
}

function normValue(v: string | null | undefined): string {
  return (v ?? '').replace(/\s+/g, '').toLowerCase();
}

interface SuggestionStarProps {
  currentValue: string | null | undefined;
  suggestedValue: string | null | undefined;
  origem?: OrigemSugestao | null;
  confianca?: number;
  className?: string;
  /** "absolute" para dentro de inputs, "inline" para dentro de botoes/triggers */
  placement?: 'inline' | 'absolute';
}

/**
 * Badge + estrela dourada indicando que o valor atual do campo é uma sugestão da IA.
 * Visível quando `currentValue === suggestedValue`; fade out quando difere.
 */
export function SuggestionStar({
  currentValue,
  suggestedValue,
  origem,
  confianca = 0,
  className,
  placement = 'inline',
}: SuggestionStarProps) {
  if (!suggestedValue || confianca <= 0) return null;

  const isMatch = normValue(currentValue) === normValue(suggestedValue);
  const label = tooltipLabel(origem, confianca);

  return (
    <Tooltip>
      <TooltipTrigger asChild>
        <span
          role="img"
          tabIndex={-1}
          onClick={(e) => { e.preventDefault(); e.stopPropagation(); }}
          className={cn(
            'inline-flex shrink-0 items-center justify-center size-6 rounded-md border border-blue-400/50 bg-blue-400 text-white shadow-sm transition-opacity duration-200 hover:bg-blue-700 dark:border-blue-400/40 dark:bg-blue-700 dark:hover:bg-blue-600 cursor-default',
            isMatch ? 'opacity-100' : 'pointer-events-none opacity-0',
            placement === 'absolute' && 'absolute right-2.5 top-1/2 z-10 -translate-y-1/2',
            className,
          )}
          aria-label={label}
          aria-hidden={!isMatch}
        >
          <Lightbulb className="size-3.5" />
        </span>
      </TooltipTrigger>
      <TooltipContent side="top" className="max-w-xs text-xs">
        {label}
      </TooltipContent>
    </Tooltip>
  );
}
