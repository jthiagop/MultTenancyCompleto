import type { MaskedNumberOptions } from 'imask';

/** Padrões de máscara pré-definidos */
export const MASK_PATTERNS = {
  cpf:      '000.000.000-00',
  cnpj:     '00.000.000/0000-00',
  cep:      '00000-000',
  telefone: [
    { mask: '(00) 00000-0000' }, // celular
    { mask: '(00) 0000-0000'  }, // fixo
  ],
  data:     '00/00/0000',
} as const;

/** Opções de moeda BRL para IMask */
export const CURRENCY_OPTS: MaskedNumberOptions = {
  mask: Number,
  scale: 2,
  thousandsSeparator: '.',
  padFractionalZeros: true,
  normalizeZeros: true,
  radix: ',',
  mapToRadix: ['.'],
  min: 0,
};

/** Classes CSS idênticas ao Input md do projeto */
export const INPUT_CLASS =
  'flex w-full bg-background border border-input shadow-xs shadow-black/5 transition-[color,box-shadow] ' +
  'text-foreground placeholder:text-muted-foreground/80 ' +
  'focus-visible:ring-ring/30 focus-visible:border-ring focus-visible:outline-none focus-visible:ring-[3px] ' +
  'disabled:cursor-not-allowed disabled:opacity-60 ' +
  'h-8.5 px-3 text-[0.8125rem] leading-(--text-sm--line-height) rounded-md';
