import * as React from 'react';
import { CalendarIcon, ChevronLeft, ChevronRight } from 'lucide-react';
// Dias da semana em pt-BR para sobrescrever o padrão inglês do DayPicker
const WEEKDAYS_PT = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
import { IMaskInput } from 'react-imask';
import { Calendar } from '@/components/ui/calendar';
import { inputVariants, InputAddon, InputGroup } from '@/components/ui/input';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';

// ── Helpers ───────────────────────────────────────────────────────────────────

/** "YYYY-MM-DD" → Date */
function isoToDate(iso: string | undefined): Date | undefined {
  if (!iso) return undefined;
  const d = new Date(iso + 'T00:00:00');
  return isNaN(d.getTime()) ? undefined : d;
}

/** Date → "YYYY-MM-DD" */
function dateToIso(date: Date | undefined): string {
  if (!date) return '';
  const y = date.getFullYear();
  const m = String(date.getMonth() + 1).padStart(2, '0');
  const d = String(date.getDate()).padStart(2, '0');
  return `${y}-${m}-${d}`;
}

/** "YYYY-MM-DD" → "DD/MM/AAAA" */
function isoToDisplay(iso: string | undefined): string {
  if (!iso || iso.length < 10) return '';
  const [y, m, d] = iso.split('-');
  return `${d}/${m}/${y}`;
}

/** "DD/MM/AAAA" → "YYYY-MM-DD" (retorna '' se inválido) */
function displayToIso(display: string): string {
  const match = display.match(/^(\d{2})\/(\d{2})\/(\d{4})$/);
  if (!match) return '';
  const [, d, m, y] = match;
  const date = new Date(`${y}-${m}-${d}T00:00:00`);
  return isNaN(date.getTime()) ? '' : `${y}-${m}-${d}`;
}

/** Aceita ISO ou DD/MM/AAAA (valor do formulário legado) */
export function vencimentoValueToIso(value: string | undefined): string {
  if (!value || !value.trim()) return '';
  const t = value.trim();
  if (/^\d{4}-\d{2}-\d{2}$/.test(t)) return t;
  return displayToIso(t);
}

function daysInMonth(year: number, monthIndex0: number): number {
  return new Date(year, monthIndex0 + 1, 0).getDate();
}

/** Dia efetivo no mês (1–31): respeita último dia em fevereiro etc. */
export function effectiveBillingDayInMonth(diaCobranca: string, year: number, monthIndex0: number): number {
  const last = daysInMonth(year, monthIndex0);
  if (diaCobranca === 'ultimo') return last;
  const n = parseInt(diaCobranca, 10);
  if (!Number.isFinite(n) || n < 1) return 1;
  return Math.min(n, last);
}

/** Ajusta uma data ISO ao dia de cobrança (mantém ano/mês). */
export function clampVencimentoToBillingDay(isoYmd: string, diaCobranca: string): string {
  const match = isoYmd.match(/^(\d{4})-(\d{2})-(\d{2})$/);
  if (!match) return isoYmd;
  const y = +match[1];
  const m0 = +match[2] - 1;
  const d = effectiveBillingDayInMonth(diaCobranca, y, m0);
  const mm = String(m0 + 1).padStart(2, '0');
  const dd = String(d).padStart(2, '0');
  return `${y}-${mm}-${dd}`;
}

// ── Props ─────────────────────────────────────────────────────────────────────

export interface DatePickerProps {
  /** Valor controlado em formato ISO "YYYY-MM-DD" */
  value?: string;
  /** Chamado com "YYYY-MM-DD" ao selecionar, ou "" ao limpar */
  onChange?: (iso: string) => void;
  placeholder?: string;
  disabled?: boolean;
  className?: string;
  align?: 'start' | 'center' | 'end';
  /** Tamanho do input — mapeia para os variants do componente Input */
  size?: 'sm' | 'md' | 'lg';
  /**
   * Quando definido (ex.: recorrência), só o dia correspondente pode ser escolhido em cada mês
   * ('1'–'30' ou 'ultimo'). O usuário pode mudar mês/ano; o dia segue o select de cobrança.
   */
  billingDayConstraint?: string;
}

// ── Cabeçalho de navegação ────────────────────────────────────────────────────

const MONTHS_PT = [
  'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
  'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro',
];

/** Botão de navegação compacto reutilizável dentro do cabeçalho. */
function NavIconBtn({
  onClick,
  label,
  children,
}: {
  onClick: () => void;
  label: string;
  children: React.ReactNode;
}) {
  return (
    <button
      type="button"
      onClick={onClick}
      aria-label={label}
      className="flex items-center justify-center size-7 rounded-md hover:bg-accent text-muted-foreground hover:text-foreground transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring/50"
    >
      {children}
    </button>
  );
}

/**
 * Cabeçalho do calendário 100 % em português:
 *   ‹   [select de mês ▼]   [ano]   ›
 */
function CalendarHeader({
  month,
  onMonthChange,
}: {
  month: Date | undefined;
  onMonthChange: (m: Date) => void;
}) {
  const ref = month ?? new Date();
  const year = ref.getFullYear();
  const mi = ref.getMonth();

  const go = (y: number, m: number) => onMonthChange(new Date(y, m, 1));

  return (
    <div className="flex items-center gap-1.5 px-2 pt-2.5 pb-1 select-none">
      {/* ← Mês anterior */}
      <NavIconBtn onClick={() => go(year, mi - 1)} label="Mês anterior">
        <ChevronLeft className="size-4" />
      </NavIconBtn>

      {/* Select de Mês — ocupa o espaço disponível */}
      <select
        value={mi}
        onChange={(e) => go(year, Number(e.target.value))}
        aria-label="Selecionar mês"
        className="flex-1 min-w-0 rounded-md border border-input bg-background px-2 py-1 text-[0.8125rem] font-medium leading-tight text-foreground hover:bg-accent focus:outline-none focus-visible:ring-2 focus-visible:ring-ring/50 cursor-pointer"
      >
        {MONTHS_PT.map((name, i) => (
          <option key={i} value={i}>
            {name}
          </option>
        ))}
      </select>

      {/* Ano — apenas texto, sem setas */}
      <span className="w-12 text-center text-[0.8125rem] font-semibold text-foreground shrink-0">
        {year}
      </span>

      {/* → Próximo mês */}
      <NavIconBtn onClick={() => go(year, mi + 1)} label="Próximo mês">
        <ChevronRight className="size-4" />
      </NavIconBtn>
    </div>
  );
}

// ── Componente principal ──────────────────────────────────────────────────────

/**
 * Input de data com máscara DD/MM/AAAA e calendário popover.
 * Cabeçalho 100 % em português: select de mês + navegação de ano.
 *
 * ```tsx
 * const [data, setData] = useState('');
 * <DatePicker value={data} onChange={setData} />
 * ```
 */
export function DatePicker({
  value,
  onChange,
  placeholder = 'dd/mm/aaaa',
  disabled = false,
  className,
  align = 'end',
  size,
  billingDayConstraint,
}: DatePickerProps) {
  const [open, setOpen] = React.useState(false);

  // Mês/ano exibido no calendário — independente da data selecionada.
  const [viewMonth, setViewMonth] = React.useState<Date | undefined>(undefined);

  const valueIso = vencimentoValueToIso(value);
  let selectedDate = isoToDate(valueIso);
  if (billingDayConstraint && selectedDate) {
    const y = selectedDate.getFullYear();
    const m = selectedDate.getMonth();
    const want = effectiveBillingDayInMonth(billingDayConstraint, y, m);
    if (selectedDate.getDate() !== want) {
      selectedDate = new Date(y, m, want);
    }
  }

  // Ao abrir sincroniza o viewMonth com a data selecionada (ou hoje).
  React.useEffect(() => {
    if (open) setViewMonth(selectedDate ?? new Date());
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [open]);

  React.useLayoutEffect(() => {
    if (!billingDayConstraint || !onChange) return;
    const iso = vencimentoValueToIso(value);
    if (!iso) return;
    const fixed = clampVencimentoToBillingDay(iso, billingDayConstraint);
    if (fixed !== iso) onChange(fixed);
  }, [billingDayConstraint, value, onChange]);

  function handleAccept(maskedValue: string) {
    let iso = displayToIso(maskedValue);
    if (iso && billingDayConstraint) {
      iso = clampVencimentoToBillingDay(iso, billingDayConstraint);
    }
    onChange?.(iso);
    if (iso) setViewMonth(isoToDate(iso));
  }

  function handleCalendarSelect(date: Date | undefined) {
    if (!date) {
      onChange?.('');
      setOpen(false);
      return;
    }
    let iso = dateToIso(date);
    if (billingDayConstraint) {
      const y = date.getFullYear();
      const m = date.getMonth();
      const d = effectiveBillingDayInMonth(billingDayConstraint, y, m);
      iso = `${y}-${String(m + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
    }
    onChange?.(iso);
    setOpen(false);
  }

  const calendarDisabled = billingDayConstraint
    ? (d: Date) => {
        const y = d.getFullYear();
        const m = d.getMonth();
        const want = effectiveBillingDayInMonth(billingDayConstraint, y, m);
        return d.getDate() !== want;
      }
    : undefined;

  return (
    <InputGroup className={cn('w-full', className)}>
      {/* Input com máscara — onClick abre o popover sem usar asChild (que quebra o IMaskInput) */}
      <IMaskInput
        mask="00/00/0000"
        value={isoToDisplay(valueIso)}
        onAccept={handleAccept}
        placeholder={placeholder}
        disabled={disabled}
        onClick={() => !disabled && setOpen(true)}
        className={cn(
          inputVariants({ variant: size }),
          'rounded-e-none border-e-0',
        )}
      />
      <InputAddon mode="icon" className="border-s-0 rounded-s-none px-0">
        <Popover open={open} onOpenChange={setOpen}>
          <PopoverTrigger asChild>
            <Button
              type="button"
              variant="ghost"
              mode="icon"
              disabled={disabled}
              className="size-8.5 rounded-s-none text-muted-foreground hover:text-foreground"
              aria-label="Abrir calendário"
            >
              <CalendarIcon className="size-4" />
            </Button>
          </PopoverTrigger>
          <PopoverContent
            className="w-auto overflow-hidden p-0"
            align={align}
            alignOffset={-8}
            sideOffset={10}
          >
            {/* Cabeçalho customizado 100 % em português */}
            <CalendarHeader month={viewMonth} onMonthChange={setViewMonth} />

            {/* Calendário sem nav nativa (evita duplicata de cabeçalho) */}
            <Calendar
              mode="single"
              selected={selectedDate}
              month={viewMonth}
              onMonthChange={setViewMonth}
              onSelect={handleCalendarSelect}
              disabled={calendarDisabled}
              initialFocus
              hideNavigation
              classNames={{ month_caption: 'hidden' }}
              formatters={{
                formatWeekdayName: (date) => WEEKDAYS_PT[date.getDay()],
              }}
            />
          </PopoverContent>
        </Popover>
      </InputAddon>
    </InputGroup>
  );
}
