import * as React from 'react';
import { CalendarIcon, ChevronLeft, ChevronRight } from 'lucide-react';
import { ptBR } from 'date-fns/locale';
import { IMaskInput } from 'react-imask';
import type { Matcher } from 'react-day-picker';
import { Calendar } from '@/components/ui/calendar';
import { inputVariants, InputAddon, InputGroup } from '@/components/ui/input';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';

// ── Helpers ───────────────────────────────────────────────────────────────────
// Mantidos com a mesma assinatura — outros módulos importam diretamente.

/** "YYYY-MM-DD" → Date (no fuso local, sem conversão UTC). */
function isoToDate(iso: string | undefined): Date | undefined {
  if (!iso) return undefined;
  const m = /^(\d{4})-(\d{2})-(\d{2})$/.exec(iso);
  if (!m) return undefined;
  // Constrói via componentes (y, m-1, d) para evitar parsing UTC do ISO
  // string em alguns runtimes — garante "00:00 local" sempre.
  const date = new Date(+m[1], +m[2] - 1, +m[3]);
  return isNaN(date.getTime()) ? undefined : date;
}

/** Date → "YYYY-MM-DD" (componentes locais, não UTC). */
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
  // Valida que a data realmente existe (evita 31/02/2024 virar 02/03/2024).
  const dt = new Date(+y, +m - 1, +d);
  if (
    dt.getFullYear() !== +y ||
    dt.getMonth() !== +m - 1 ||
    dt.getDate() !== +d
  ) {
    return '';
  }
  return `${y}-${m}-${d}`;
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

/** Normaliza min/max (string ISO ou Date) para Date local. */
function toDateLimit(v: string | Date | undefined): Date | undefined {
  if (!v) return undefined;
  if (v instanceof Date) return isNaN(v.getTime()) ? undefined : v;
  return isoToDate(v);
}

// ── Props ─────────────────────────────────────────────────────────────────────

export interface DatePickerProps {
  /** Valor controlado em formato ISO "YYYY-MM-DD". */
  value?: string;
  /** Chamado com "YYYY-MM-DD" ao selecionar, ou "" ao limpar. */
  onChange?: (iso: string) => void;
  /** Disparado quando o input perde o foco — útil para react-hook-form. */
  onBlur?: () => void;

  placeholder?: string;
  disabled?: boolean;
  className?: string;
  align?: 'start' | 'center' | 'end';
  /** Tamanho — propaga para input, addon e botão do calendário. */
  size?: 'sm' | 'md' | 'lg';

  /**
   * Restrição de dia de cobrança (recorrência). Quando definido (ex.: '15'
   * ou 'ultimo'), apenas o dia correspondente pode ser escolhido em cada mês;
   * o usuário pode mudar mês/ano livremente.
   */
  billingDayConstraint?: string;

  /** Limite mínimo aceito (ISO `YYYY-MM-DD` ou Date). */
  minDate?: string | Date;
  /** Limite máximo aceito (ISO `YYYY-MM-DD` ou Date). */
  maxDate?: string | Date;
  /**
   * Função adicional para desabilitar datas. Compõe com `minDate`,
   * `maxDate` e `billingDayConstraint`. Retorne `true` para desabilitar.
   */
  isDateDisabled?: (date: Date) => boolean;

  /** Marca o input como inválido (estilo destrutivo + aria-invalid). */
  invalid?: boolean;

  // ── Form integration ─────────────────────────────────────────────────────
  id?: string;
  name?: string;
  /** aria-label para o input (quando não há `<Label htmlFor>`). */
  'aria-label'?: string;
  /** aria-describedby — para associar mensagens de erro do FormControl. */
  'aria-describedby'?: string;

  // ── Footer (Hoje / Limpar) ───────────────────────────────────────────────
  /** Esconde a barra de ações Hoje / Limpar. */
  hideFooterActions?: boolean;
  /** Texto do botão "Hoje". */
  todayLabel?: string;
  /** Texto do botão "Limpar". */
  clearLabel?: string;
}

// ── Cabeçalho de navegação ────────────────────────────────────────────────────

const MONTHS_PT = [
  'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
  'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro',
];

const CURRENT_YEAR = new Date().getFullYear();
/** Range padrão do select de ano: ±100 anos do atual. Cobre datas de
 *  nascimento (idosos) e vencimentos futuros sem precisar de scroll mensal. */
const YEAR_RANGE = Array.from({ length: 201 }, (_, i) => CURRENT_YEAR - 100 + i);

/** Botão de navegação compacto reutilizável dentro do cabeçalho. */
function NavIconBtn({
  onClick,
  label,
  disabled,
  children,
}: {
  onClick: () => void;
  label: string;
  disabled?: boolean;
  children: React.ReactNode;
}) {
  return (
    <button
      type="button"
      onClick={onClick}
      disabled={disabled}
      aria-label={label}
      className="flex items-center justify-center size-7 rounded-md hover:bg-accent text-muted-foreground hover:text-foreground transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring/50 disabled:opacity-40 disabled:hover:bg-transparent disabled:cursor-not-allowed"
    >
      {children}
    </button>
  );
}

/**
 * Cabeçalho do calendário 100 % em português:
 *   ‹   [select de mês ▼]   [select de ano ▼]   ›
 *
 * Diferenças vs. anterior:
 *   - Ano também é select (vs. texto fixo) → usuário troca década sem
 *     navegar mês a mês.
 *   - Botões prev/next ficam desabilitados quando ultrapassam min/max.
 */
function CalendarHeader({
  month,
  onMonthChange,
  minDate,
  maxDate,
}: {
  month: Date | undefined;
  onMonthChange: (m: Date) => void;
  minDate?: Date;
  maxDate?: Date;
}) {
  const ref = month ?? new Date();
  const year = ref.getFullYear();
  const mi = ref.getMonth();

  const go = (y: number, m: number) => onMonthChange(new Date(y, m, 1));

  // Desabilita prev/next quando sairíamos do range permitido.
  const prevDisabled = (() => {
    if (!minDate) return false;
    const prev = new Date(year, mi - 1, 1);
    const minMonth = new Date(minDate.getFullYear(), minDate.getMonth(), 1);
    return prev < minMonth;
  })();
  const nextDisabled = (() => {
    if (!maxDate) return false;
    const next = new Date(year, mi + 1, 1);
    const maxMonth = new Date(maxDate.getFullYear(), maxDate.getMonth(), 1);
    return next > maxMonth;
  })();

  return (
    <div className="flex items-center gap-1.5 px-2 pt-2.5 pb-1 select-none">
      <NavIconBtn onClick={() => go(year, mi - 1)} label="Mês anterior" disabled={prevDisabled}>
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

      {/* Select de Ano */}
      <select
        value={year}
        onChange={(e) => go(Number(e.target.value), mi)}
        aria-label="Selecionar ano"
        className="rounded-md border border-input bg-background px-2 py-1 text-[0.8125rem] font-semibold leading-tight text-foreground hover:bg-accent focus:outline-none focus-visible:ring-2 focus-visible:ring-ring/50 cursor-pointer tabular-nums"
      >
        {YEAR_RANGE.map((y) => (
          <option key={y} value={y}>
            {y}
          </option>
        ))}
      </select>

      <NavIconBtn onClick={() => go(year, mi + 1)} label="Próximo mês" disabled={nextDisabled}>
        <ChevronRight className="size-4" />
      </NavIconBtn>
    </div>
  );
}

// ── Componente principal ──────────────────────────────────────────────────────

/**
 * Input de data com máscara DD/MM/AAAA e calendário popover.
 *
 * - Cabeçalho 100% em português com select de mês e ano.
 * - Calendário localizado em pt-BR via date-fns.
 * - Suporta `minDate`/`maxDate`/`isDateDisabled`/`billingDayConstraint`.
 * - Atalhos de teclado: `Alt+↓` abre, `Esc` fecha (padrão WAI-ARIA).
 * - Footer com ações "Hoje" e "Limpar" (configuráveis via
 *   `hideFooterActions`/`todayLabel`/`clearLabel`).
 * - Compatível com react-hook-form via `forwardRef` no input + `onBlur`.
 *
 * ```tsx
 * const [data, setData] = useState('');
 * <DatePicker value={data} onChange={setData} />
 * ```
 *
 * Com react-hook-form:
 * ```tsx
 * <Controller
 *   control={control}
 *   name="vencimento"
 *   render={({ field, fieldState }) => (
 *     <DatePicker {...field} invalid={!!fieldState.error} />
 *   )}
 * />
 * ```
 */
export const DatePicker = React.forwardRef<HTMLInputElement, DatePickerProps>(function DatePicker(
  {
    value,
    onChange,
    onBlur,
    placeholder = 'dd/mm/aaaa',
    disabled = false,
    className,
    align = 'end',
    size,
    billingDayConstraint,
    minDate,
    maxDate,
    isDateDisabled,
    invalid = false,
    id,
    name,
    'aria-label': ariaLabel,
    'aria-describedby': ariaDescribedBy,
    hideFooterActions = false,
    todayLabel = 'Hoje',
    clearLabel = 'Limpar',
  },
  ref,
) {
  const [open, setOpen] = React.useState(false);
  const [viewMonth, setViewMonth] = React.useState<Date | undefined>(undefined);

  // ── Normalização de limites ─────────────────────────────────────────────────
  const minDateObj = React.useMemo(() => toDateLimit(minDate), [minDate]);
  const maxDateObj = React.useMemo(() => toDateLimit(maxDate), [maxDate]);

  // ── Data selecionada (memoizada — evita recriação por render) ───────────────
  const valueIso = React.useMemo(() => vencimentoValueToIso(value), [value]);
  const selectedDate = React.useMemo(() => {
    const base = isoToDate(valueIso);
    if (!base || !billingDayConstraint) return base;
    const want = effectiveBillingDayInMonth(
      billingDayConstraint,
      base.getFullYear(),
      base.getMonth(),
    );
    return base.getDate() === want
      ? base
      : new Date(base.getFullYear(), base.getMonth(), want);
  }, [valueIso, billingDayConstraint]);

  // ── Sincroniza viewMonth ao abrir e quando o valor muda externamente ───────
  React.useEffect(() => {
    if (open) setViewMonth(selectedDate ?? new Date());
  }, [open, selectedDate]);

  // ── Clamp ao billingDayConstraint (em useEffect, não useLayoutEffect) ──────
  // Antes era useLayoutEffect, o que dispara durante a fase de commit do DOM
  // e pode gerar warning "Cannot update component while rendering" em React 18
  // strict mode. Como o clamp é puramente lógico e não afeta layout, useEffect
  // resolve o caso sem o risco.
  React.useEffect(() => {
    if (!billingDayConstraint || !onChange || !valueIso) return;
    const fixed = clampVencimentoToBillingDay(valueIso, billingDayConstraint);
    if (fixed !== valueIso) onChange(fixed);
  }, [billingDayConstraint, valueIso, onChange]);

  // ── Handlers ──────────────────────────────────────────────────────────────
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

  function handleSelectToday() {
    const today = new Date();
    handleCalendarSelect(today);
  }

  function handleClear() {
    onChange?.('');
    setOpen(false);
  }

  // Atalho WAI-ARIA: Alt+ArrowDown abre o calendário no input.
  function handleInputKeyDown(e: React.KeyboardEvent<HTMLInputElement>) {
    if (disabled) return;
    if (e.key === 'ArrowDown' && e.altKey) {
      e.preventDefault();
      setOpen(true);
    } else if (e.key === 'Escape' && open) {
      setOpen(false);
    }
  }

  // ── Composição dos matchers de "data desabilitada" ─────────────────────────
  const calendarDisabledMatchers = React.useMemo<Matcher[] | undefined>(() => {
    const matchers: Matcher[] = [];
    if (minDateObj) matchers.push({ before: minDateObj });
    if (maxDateObj) matchers.push({ after: maxDateObj });
    if (billingDayConstraint) {
      matchers.push((d: Date) => {
        const want = effectiveBillingDayInMonth(
          billingDayConstraint,
          d.getFullYear(),
          d.getMonth(),
        );
        return d.getDate() !== want;
      });
    }
    if (isDateDisabled) matchers.push(isDateDisabled);
    return matchers.length > 0 ? matchers : undefined;
  }, [minDateObj, maxDateObj, billingDayConstraint, isDateDisabled]);

  // ── Estado visual de inválido ──────────────────────────────────────────────
  // Se o usuário digitou uma data completa (10 chars) mas o parsing falhou,
  // marcamos como inválido. Combina com a prop externa `invalid`.
  const displayValue = isoToDisplay(valueIso);
  const rawInputValue = value && /^\d{2}\/\d{2}\/\d{4}$/.test(value) ? value : displayValue;
  const isInternalInvalid =
    !!rawInputValue &&
    rawInputValue.length === 10 &&
    !valueIso;
  const ariaInvalid = invalid || isInternalInvalid || undefined;

  // ── "Hoje" desabilitado quando fora do range permitido ────────────────────
  const todayDisabled = React.useMemo(() => {
    const today = new Date();
    const t = new Date(today.getFullYear(), today.getMonth(), today.getDate());
    if (minDateObj && t < new Date(minDateObj.getFullYear(), minDateObj.getMonth(), minDateObj.getDate())) {
      return true;
    }
    if (maxDateObj && t > new Date(maxDateObj.getFullYear(), maxDateObj.getMonth(), maxDateObj.getDate())) {
      return true;
    }
    if (billingDayConstraint) {
      const want = effectiveBillingDayInMonth(billingDayConstraint, t.getFullYear(), t.getMonth());
      if (t.getDate() !== want) return true;
    }
    if (isDateDisabled?.(t)) return true;
    return false;
  }, [minDateObj, maxDateObj, billingDayConstraint, isDateDisabled]);

  return (
    <InputGroup className={cn('w-full', className)}>
      <IMaskInput
        // ref via inputRef (IMaskInput expõe inputRef em vez de ref nativa)
        inputRef={ref as React.Ref<HTMLInputElement> | undefined}
        id={id}
        name={name}
        mask="00/00/0000"
        value={isoToDisplay(valueIso)}
        onAccept={handleAccept}
        onBlur={onBlur}
        onKeyDown={handleInputKeyDown}
        placeholder={placeholder}
        disabled={disabled}
        aria-label={ariaLabel}
        aria-describedby={ariaDescribedBy}
        aria-invalid={ariaInvalid}
        aria-haspopup="dialog"
        aria-expanded={open}
        onClick={() => !disabled && setOpen(true)}
        className={cn(
          inputVariants({ variant: size }),
          'rounded-e-none border-e-0',
        )}
      />
      <InputAddon variant={size} mode="icon" className="border-s-0 rounded-s-none px-0">
        <Popover open={open} onOpenChange={setOpen}>
          <PopoverTrigger asChild>
            <Button
              type="button"
              variant="ghost"
              mode="icon"
              disabled={disabled}
              className={cn(
                'rounded-s-none text-muted-foreground hover:text-foreground',
                size === 'sm' && 'size-7',
                size === 'lg' && 'size-10',
                (!size || size === 'md') && 'size-8.5',
              )}
              aria-label="Abrir calendário"
              tabIndex={-1}
            >
              <CalendarIcon className={cn(size === 'sm' ? 'size-3.5' : 'size-4')} />
            </Button>
          </PopoverTrigger>
          <PopoverContent
            className="w-auto overflow-hidden p-0"
            align={align}
            alignOffset={-8}
            sideOffset={10}
          >
            <CalendarHeader
              month={viewMonth}
              onMonthChange={setViewMonth}
              minDate={minDateObj}
              maxDate={maxDateObj}
            />

            <Calendar
              mode="single"
              locale={ptBR}
              selected={selectedDate}
              month={viewMonth}
              onMonthChange={setViewMonth}
              onSelect={handleCalendarSelect}
              disabled={calendarDisabledMatchers}
              autoFocus
              hideNavigation
              classNames={{ month_caption: 'hidden' }}
            />

            {!hideFooterActions && (
              <div className="flex items-center justify-between gap-2 border-t border-border px-3 py-2">
                <Button
                  type="button"
                  variant="ghost"
                  size="sm"
                  className="h-7 px-2 text-xs"
                  onClick={handleSelectToday}
                  disabled={todayDisabled}
                >
                  {todayLabel}
                </Button>
                <Button
                  type="button"
                  variant="ghost"
                  size="sm"
                  className="h-7 px-2 text-xs text-muted-foreground hover:text-destructive"
                  onClick={handleClear}
                  disabled={!valueIso}
                >
                  {clearLabel}
                </Button>
              </div>
            )}
          </PopoverContent>
        </Popover>
      </InputAddon>
    </InputGroup>
  );
});

