import { IMaskInput } from 'react-imask';
import type { FactoryOpts } from 'imask';
import { forwardRef } from 'react';
import { cn } from '@/lib/utils';
import { MASK_PATTERNS, INPUT_CLASS } from './mask-config';

// ── Helpers de moeda ─────────────────────────────────────────────────────────

function parseCentsFromFormatted(formatted: string): number {
  if (!formatted) return 0;
  const normalized = formatted.replace(/\./g, '').replace(',', '.');
  const val = parseFloat(normalized);
  return isNaN(val) ? 0 : Math.round(val * 100);
}

function formatBRL(cents: number): string {
  return (cents / 100).toLocaleString('pt-BR', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  });
}

// ── CurrencyInput ─────────────────────────────────────────────────────────────

export interface CurrencyInputProps
  extends Omit<React.InputHTMLAttributes<HTMLInputElement>, 'value' | 'onChange'> {
  value?: string;
  /** Valor formatado: "1.234,56" */
  onMaskedChange?: (masked: string) => void;
  /** Centavos como string: "123456" */
  onUnmaskedChange?: (unmasked: string) => void;
}

/**
 * Input de moeda (BRL) estilo calculadora:
 * - Digitar `1` → `0,01`; `12` → `0,12`; `123` → `1,23`
 * - Backspace remove o último dígito da direita
 */
export const CurrencyInput = forwardRef<HTMLInputElement, CurrencyInputProps>(
  function CurrencyInput({ value, onMaskedChange, onUnmaskedChange, className, placeholder, ...rest }, ref) {
    const currentCents = parseCentsFromFormatted(String(value ?? ''));
    const displayValue = currentCents > 0 ? formatBRL(currentCents) : '';

    function emit(newCents: number) {
      onMaskedChange?.(newCents > 0 ? formatBRL(newCents) : '');
      onUnmaskedChange?.(String(newCents));
    }

    function handleKeyDown(e: React.KeyboardEvent<HTMLInputElement>) {
      if (['Tab', 'Enter', 'Escape', 'ArrowLeft', 'ArrowRight', 'Home', 'End'].includes(e.key)) return;
      if (e.ctrlKey || e.metaKey) return;
      e.preventDefault();
      if (e.key === 'Backspace' || e.key === 'Delete') {
        emit(Math.floor(currentCents / 10));
      } else if (/^\d$/.test(e.key)) {
        const newCents = currentCents * 10 + parseInt(e.key, 10);
        if (newCents <= 9_999_999_999) emit(newCents);
      }
    }

    function handlePaste(e: React.ClipboardEvent<HTMLInputElement>) {
      e.preventDefault();
      const text = e.clipboardData.getData('text').trim();
      const normalized = text.replace(/\./g, '').replace(',', '.');
      const parsed = parseFloat(normalized);
      if (!isNaN(parsed) && parsed >= 0) {
        const newCents = Math.round(parsed * 100);
        if (newCents <= 9_999_999_999) emit(newCents);
      }
    }

    return (
      <input
        ref={ref}
        type="text"
        inputMode="numeric"
        value={displayValue}
        onChange={() => {}}
        onKeyDown={handleKeyDown}
        onPaste={handlePaste}
        placeholder={placeholder ?? '0,00'}
        className={cn(INPUT_CLASS, className)}
        {...rest}
      />
    );
  },
);

// ── MaskedInput ───────────────────────────────────────────────────────────────

type MaskPattern = keyof typeof MASK_PATTERNS;

interface MaskedInputProps extends React.InputHTMLAttributes<HTMLInputElement> {
  maskType?: MaskPattern;
  maskOpts?: FactoryOpts;
  onUnmaskedChange?: (unmasked: string) => void;
  onMaskedChange?: (masked: string) => void;
}

export const MaskedInput = forwardRef<HTMLInputElement, MaskedInputProps>(
  function MaskedInput(
    { maskType, maskOpts, onUnmaskedChange, onMaskedChange, className, value, onChange, ...rest },
    ref,
  ) {
    const opts: FactoryOpts = maskOpts
      ? (maskOpts as FactoryOpts)
      : maskType
        ? ({ mask: MASK_PATTERNS[maskType] } as FactoryOpts)
        : ({ mask: /.*/ } as FactoryOpts);

    return (
      <IMaskInput
        {...(opts as object)}
        inputRef={ref as React.Ref<HTMLInputElement>}
        className={cn(INPUT_CLASS, className)}
        value={String(value ?? '')}
        onAccept={(val, maskRef) => {
          onMaskedChange?.(val as string);
          onUnmaskedChange?.(maskRef.unmaskedValue);
          if (onChange) {
            const nativeEvent = { target: { value: val as string } } as React.ChangeEvent<HTMLInputElement>;
            onChange(nativeEvent);
          }
        }}
        {...rest}
      />
    );
  },
);
