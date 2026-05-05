import * as React from 'react';
import { CalendarIcon } from 'lucide-react';
import { IMaskInput } from 'react-imask';
import { Button } from '@/components/ui/button';
import { inputVariants, InputAddon, InputGroup } from '@/components/ui/input';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { cn } from '@/lib/utils';

// ── Constantes (alinhadas ao date-picker) ───────────────────────────────────

const MONTHS_PT = [
  'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
  'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro',
];

const CURRENT_YEAR = new Date().getFullYear();
const YEAR_RANGE = Array.from({ length: 201 }, (_, i) => CURRENT_YEAR - 100 + i);

const MES_ANO_REGEX = /^(\d{2})\/(\d{4})$/;

// ── Helpers ─────────────────────────────────────────────────────────────────

/** "MM/AAAA" válido → { monthIndex0, year } ou null. */
export function parseMesYyyyParts(value: string | undefined): { monthIndex0: number; year: number } | null {
  if (!value?.trim()) return null;
  const m = MES_ANO_REGEX.exec(value.trim());
  if (!m) return null;
  const month = Number(m[1]);
  const year = Number(m[2]);
  if (month < 1 || month > 12 || !Number.isFinite(year)) return null;
  return { monthIndex0: month - 1, year };
}

/** monthIndex0 (0–11) + ano → "MM/AAAA". */
export function formatMesYyyyFromParts(monthIndex0: number, year: number): string {
  return `${String(monthIndex0 + 1).padStart(2, '0')}/${year}`;
}

function isValidMesYyyy(v: string): boolean {
  return parseMesYyyyParts(v) !== null;
}

// ── Props ───────────────────────────────────────────────────────────────────

export interface MonthYearPickerProps {
  /** Valor em "MM/AAAA". */
  value?: string;
  onChange?: (mmYyyy: string) => void;
  onBlur?: () => void;
  placeholder?: string;
  disabled?: boolean;
  className?: string;
  align?: 'start' | 'center' | 'end';
  size?: 'sm' | 'md' | 'lg';
  invalid?: boolean;
  id?: string;
  name?: string;
  'aria-label'?: string;
  'aria-describedby'?: string;
  clearLabel?: string;
  /** Esconde o botão Limpar no rodapé do popover. */
  hideClear?: boolean;
}

// ── Componente ─────────────────────────────────────────────────────────────

/**
 * Seletor de mês/ano no formato MM/AAAA, com máscara e popover
 * (mês + ano em português), no mesmo espírito visual do {@link DatePicker}.
 */
export const MonthYearPicker = React.forwardRef<HTMLInputElement, MonthYearPickerProps>(
  function MonthYearPicker(
    {
      value = '',
      onChange,
      onBlur,
      placeholder = 'mm/aaaa',
      disabled = false,
      className,
      align = 'end',
      size,
      invalid = false,
      id,
      name,
      'aria-label': ariaLabel,
      'aria-describedby': ariaDescribedBy,
      clearLabel = 'Limpar',
      hideClear = false,
    },
    ref,
  ) {
    const [open, setOpen] = React.useState(false);
    const [draftMonth, setDraftMonth] = React.useState(0);
    const [draftYear, setDraftYear] = React.useState(CURRENT_YEAR);

    React.useEffect(() => {
      if (open) {
        const p = parseMesYyyyParts(value);
        if (p) {
          setDraftMonth(p.monthIndex0);
          setDraftYear(p.year);
        } else {
          const now = new Date();
          setDraftMonth(now.getMonth());
          setDraftYear(now.getFullYear());
        }
      }
    }, [open, value]);

    function applyDraft() {
      const next = formatMesYyyyFromParts(draftMonth, draftYear);
      onChange?.(next);
      setOpen(false);
    }

    function handleAccept(masked: string) {
      const t = masked.trim();
      if (!t) {
        onChange?.('');
        return;
      }
      if (isValidMesYyyy(t)) {
        onChange?.(t);
        const p = parseMesYyyyParts(t);
        if (p) {
          setDraftMonth(p.monthIndex0);
          setDraftYear(p.year);
        }
      } else {
        onChange?.(t);
      }
    }

    function handleClear() {
      onChange?.('');
      setOpen(false);
    }

    function handleInputKeyDown(e: React.KeyboardEvent<HTMLInputElement>) {
      if (disabled) return;
      if (e.key === 'ArrowDown' && e.altKey) {
        e.preventDefault();
        setOpen(true);
      } else if (e.key === 'Escape' && open) {
        setOpen(false);
      }
    }

    const displayValue = value ?? '';
    const digits = displayValue.replace(/\D/g, '');
    const isInternalInvalid =
      !!displayValue && digits.length >= 6 && !isValidMesYyyy(displayValue);
    const ariaInvalid = invalid || isInternalInvalid || undefined;

    return (
      <InputGroup className={cn('w-full', className)}>
        <IMaskInput
          inputRef={ref as React.Ref<HTMLInputElement> | undefined}
          id={id}
          name={name}
          mask="00/0000"
          value={displayValue}
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
                aria-label="Abrir seletor de mês e ano"
                tabIndex={-1}
              >
                <CalendarIcon className={cn(size === 'sm' ? 'size-3.5' : 'size-4')} />
              </Button>
            </PopoverTrigger>
            <PopoverContent
              className="w-auto min-w-[280px] p-3"
              align={align}
              alignOffset={-8}
              sideOffset={10}
            >
              <div className="grid gap-3">
                <div className="grid grid-cols-2 gap-2">
                  <label className="grid gap-1">
                    <span className="text-xs font-medium text-muted-foreground">Mês</span>
                    <select
                      value={draftMonth}
                      onChange={(e) => setDraftMonth(Number(e.target.value))}
                      aria-label="Mês"
                      className="h-9 rounded-md border border-input bg-background px-2 text-sm font-medium text-foreground hover:bg-accent focus:outline-none focus-visible:ring-2 focus-visible:ring-ring/50"
                    >
                      {MONTHS_PT.map((nome, i) => (
                        <option key={nome} value={i}>
                          {nome}
                        </option>
                      ))}
                    </select>
                  </label>
                  <label className="grid gap-1">
                    <span className="text-xs font-medium text-muted-foreground">Ano</span>
                    <select
                      value={draftYear}
                      onChange={(e) => setDraftYear(Number(e.target.value))}
                      aria-label="Ano"
                      className="h-9 rounded-md border border-input bg-background px-2 text-sm font-semibold tabular-nums text-foreground hover:bg-accent focus:outline-none focus-visible:ring-2 focus-visible:ring-ring/50"
                    >
                      {YEAR_RANGE.map((y) => (
                        <option key={y} value={y}>
                          {y}
                        </option>
                      ))}
                    </select>
                  </label>
                </div>
                <div className="flex items-center justify-end gap-2 border-t border-border pt-2">
                  {!hideClear && (
                    <Button
                      type="button"
                      variant="ghost"
                      size="sm"
                      className="h-8 text-xs text-muted-foreground hover:text-destructive"
                      onClick={handleClear}
                      disabled={!value?.trim()}
                    >
                      {clearLabel}
                    </Button>
                  )}
                  <Button type="button" size="sm" className="h-8 text-xs" onClick={applyDraft}>
                    Aplicar
                  </Button>
                </div>
              </div>
            </PopoverContent>
          </Popover>
        </InputAddon>
      </InputGroup>
    );
  },
);
