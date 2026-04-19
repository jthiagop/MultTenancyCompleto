import { useState } from 'react';
import {
  startOfDay,
  endOfDay,
  startOfMonth,
  endOfMonth,
  startOfYear,
  endOfYear,
  subMonths,
  addMonths,
  subDays,
  addDays,
  differenceInCalendarDays,
  format,
} from 'date-fns';
import { ptBR } from 'date-fns/locale';
import { type DateRange } from 'react-day-picker';
import { CalendarDays, ChevronDown, ChevronLeft, ChevronRight } from 'lucide-react';
import { Calendar } from '@/components/ui/calendar';
import { Button } from '@/components/ui/button';
import { ButtonGroup } from '@/components/ui/button-group';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { cn } from '@/lib/utils';

// ── Tipos ─────────────────────────────────────────────────────────────────────

export interface PeriodValue {
  startDate: string; // ISO yyyy-MM-dd
  endDate: string;   // ISO yyyy-MM-dd
}

interface PeriodPickerProps {
  value: PeriodValue;
  onChange: (v: PeriodValue) => void;
  className?: string;
  /** Classes extras nos botões da barra (ex.: azul claro da área financeira). */
  buttonClassName?: string;
}

// ── Helpers ───────────────────────────────────────────────────────────────────

const toIso = (d: Date) => format(d, 'yyyy-MM-dd');

function isoToDate(iso: string): Date {
  const [y, m, d] = iso.split('-').map(Number);
  return new Date(y, m - 1, d);
}

/** Retorna o período padrão: primeiro ao último dia do mês corrente. */
export function defaultPeriod(): PeriodValue {
  const now  = new Date();
  const y    = now.getFullYear();
  const m    = now.getMonth();
  const pad  = (n: number) => String(n).padStart(2, '0');
  const last = new Date(y, m + 1, 0).getDate();
  return { startDate: `${y}-${pad(m + 1)}-01`, endDate: `${y}-${pad(m + 1)}-${pad(last)}` };
}

// ── Atalhos de período ────────────────────────────────────────────────────────

interface Preset {
  label: string;
  get: () => PeriodValue;
}

const PRESETS: Preset[] = [
  {
    label: 'Hoje',
    get: () => {
      const d = new Date();
      return { startDate: toIso(startOfDay(d)), endDate: toIso(endOfDay(d)) };
    },
  },
  {
    label: '7 dias',
    get: () => ({
      startDate: toIso(subDays(new Date(), 6)),
      endDate:   toIso(new Date()),
    }),
  },
  {
    label: 'Este mês',
    get: () => {
      const d = new Date();
      return { startDate: toIso(startOfMonth(d)), endDate: toIso(endOfMonth(d)) };
    },
  },
  {
    label: 'Mês passado',
    get: () => {
      const d = subMonths(new Date(), 1);
      return { startDate: toIso(startOfMonth(d)), endDate: toIso(endOfMonth(d)) };
    },
  },
  {
    label: 'Este ano',
    get: () => {
      const d = new Date();
      return { startDate: toIso(startOfYear(d)), endDate: toIso(endOfYear(d)) };
    },
  },
];

// ── Navegação (setas) ─────────────────────────────────────────────────────────

/**
 * Desloca o período para frente (+1) ou para trás (-1).
 *
 * Lógica inteligente:
 *  - período de um mês completo → muda de mês
 *  - período de um dia         → muda de dia
 *  - qualquer outro intervalo  → desloca pelo número de dias do intervalo
 */
function shiftPeriod(v: PeriodValue, dir: 1 | -1): PeriodValue {
  const start = isoToDate(v.startDate);
  const end   = isoToDate(v.endDate);
  const span  = differenceInCalendarDays(end, start) + 1; // dias no intervalo

  const isFullMonth =
    start.getDate() === 1 &&
    end.getTime() === endOfMonth(start).setHours(0, 0, 0, 0);

  const isFullYear =
    start.getMonth() === 0 && start.getDate() === 1 &&
    end.getMonth() === 11 && end.getDate() === 31 &&
    start.getFullYear() === end.getFullYear();

  if (isFullYear) {
    const base = new Date(start.getFullYear() + dir, 0, 1);
    return { startDate: toIso(startOfYear(base)), endDate: toIso(endOfYear(base)) };
  }

  if (isFullMonth) {
    const base = dir === 1 ? addMonths(start, 1) : subMonths(start, 1);
    return { startDate: toIso(startOfMonth(base)), endDate: toIso(endOfMonth(base)) };
  }

  // Intervalo genérico: desloca pelo número de dias
  const shift = span * dir;
  return {
    startDate: toIso(addDays(start, shift)),
    endDate:   toIso(addDays(end, shift)),
  };
}

// ── Label do botão central ────────────────────────────────────────────────────

function matchPreset(v: PeriodValue): string | null {
  for (const p of PRESETS) {
    const pv = p.get();
    if (pv.startDate === v.startDate && pv.endDate === v.endDate) return p.label;
  }
  return null;
}

function displayLabel(v: PeriodValue): string {
  const match = matchPreset(v);
  if (match) return match;
  const start = format(isoToDate(v.startDate), 'dd/MM/yy', { locale: ptBR });
  const end   = format(isoToDate(v.endDate),   'dd/MM/yy', { locale: ptBR });
  return start === end ? start : `${start} – ${end}`;
}

// ── Componente ────────────────────────────────────────────────────────────────

export function PeriodPicker({ value, onChange, className, buttonClassName }: PeriodPickerProps) {
  const [open, setOpen] = useState(false);
  const [calRange, setCalRange] = useState<DateRange | undefined>({
    from: isoToDate(value.startDate),
    to:   isoToDate(value.endDate),
  });

  const activePreset = matchPreset(value);

  function applyPreset(p: Preset) {
    const pv = p.get();
    onChange(pv);
    setCalRange({ from: isoToDate(pv.startDate), to: isoToDate(pv.endDate) });
    setOpen(false);
  }

  function applyRange() {
    if (!calRange?.from || !calRange?.to) return;
    onChange({ startDate: toIso(calRange.from), endDate: toIso(calRange.to) });
    setOpen(false);
  }

  function navigate(dir: 1 | -1) {
    const next = shiftPeriod(value, dir);
    onChange(next);
    setCalRange({ from: isoToDate(next.startDate), to: isoToDate(next.endDate) });
  }

  return (
    <ButtonGroup className={cn('shrink-0', className)}>
      {/* ← Período anterior */}
      <Button
        variant="outline"
        size="icon"
        className={cn('size-10 shrink-0', buttonClassName)}
        aria-label="Período anterior"
        onClick={() => navigate(-1)}
      >
        <ChevronLeft className="size-4" />
      </Button>

      {/* Botão central com label + popover do calendário */}
      <Popover open={open} onOpenChange={setOpen}>
        <PopoverTrigger asChild>
          <Button
            variant="outline"
            size="sm"
            className={cn('h-10 min-w-44 justify-between gap-1.5 px-4 font-normal', buttonClassName)}
          >
            <CalendarDays
              className={cn(
                'size-3.5 shrink-0',
                buttonClassName ? 'text-blue-700/75 dark:text-blue-200/85' : 'text-muted-foreground',
              )}
            />
            <span className="truncate flex-1 text-center">{displayLabel(value)}</span>
            <ChevronDown
              className={cn(
                'size-3 shrink-0',
                buttonClassName ? 'text-blue-700/75 dark:text-blue-200/85' : 'text-muted-foreground',
              )}
            />
          </Button>
        </PopoverTrigger>

        <PopoverContent className="w-auto p-0" align="center" side="bottom" sideOffset={8}>
          {/* Atalhos rápidos */}
          <div className="flex flex-wrap gap-1 border-b border-border p-2">
            {PRESETS.map((p) => (
              <Button
                key={p.label}
                variant={activePreset === p.label ? 'primary' : 'outline'}
                size="sm"
                className="h-7 text-xs px-2.5"
                onClick={() => applyPreset(p)}
              >
                {p.label}
              </Button>
            ))}
          </div>

          {/* Calendário de intervalo */}
          <Calendar
            mode="range"
            defaultMonth={calRange?.from}
            selected={calRange}
            onSelect={setCalRange}
            numberOfMonths={2}
            locale={ptBR}
          />

          {/* Rodapé */}
          <div className="flex items-center justify-between border-t border-border px-3 py-2 gap-2">
            <span className="text-xs text-muted-foreground">
              {calRange?.from && calRange?.to
                ? `${format(calRange.from, 'dd/MM/yy')} – ${format(calRange.to, 'dd/MM/yy')}`
                : 'Selecione um intervalo'}
            </span>
            <Button
              size="sm"
              className="h-7 text-xs bg-blue-600 hover:bg-blue-700 text-white border-0"
              disabled={!calRange?.from || !calRange?.to}
              onClick={applyRange}
            >
              Aplicar
            </Button>
          </div>
        </PopoverContent>
      </Popover>

      {/* → Próximo período */}
      <Button
        variant="outline"
        size="icon"
        className={cn('size-10 shrink-0', buttonClassName)}
        aria-label="Próximo período"
        onClick={() => navigate(1)}
      >
        <ChevronRight className="size-4" />
      </Button>
    </ButtonGroup>
  );
}
