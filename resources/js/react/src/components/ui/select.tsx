'use client';

import * as React from 'react';
import { isValidElement, ReactNode } from 'react';
import { cn } from '@/lib/utils';
import { cva, VariantProps } from 'class-variance-authority';
import { Check, ChevronDown, ChevronUp, Search } from 'lucide-react';
import { Select as SelectPrimitive } from 'radix-ui';

// Create a Context for `indicatorPosition` and `indicator` control
const SelectContext = React.createContext<{
  indicatorPosition: 'left' | 'right';
  indicatorVisibility: boolean;
  indicator: ReactNode;
}>({ indicatorPosition: 'left', indicator: null, indicatorVisibility: true });

// Contexto opt-in para o modo `searchable`. Mantido SEPARADO do contexto
// existente para que componentes que não habilitam busca nem cheguem a ler
// `searchTerm` (zero impacto nos usos atuais do Select pelo app inteiro).
interface SelectSearchContextValue {
  enabled: boolean;
  term: string;
  setTerm: (v: string) => void;
  placeholder: string;
}
const SelectSearchContext = React.createContext<SelectSearchContextValue>({
  enabled: false,
  term: '',
  setTerm: () => {},
  placeholder: 'Buscar...',
});

// Helper: extrai texto puro de qualquer ReactNode para permitir filtrar
// itens cujo `children` não é uma string (ex.: <SelectItem><Icon/> Texto</SelectItem>).
function extractText(node: ReactNode): string {
  if (node == null || typeof node === 'boolean') return '';
  if (typeof node === 'string' || typeof node === 'number') return String(node);
  if (Array.isArray(node)) return node.map(extractText).join(' ');
  if (isValidElement(node)) {
    const children = (node.props as { children?: ReactNode })?.children;
    return extractText(children);
  }
  return '';
}

// Root Component
const Select = ({
  indicatorPosition = 'left',
  indicatorVisibility = true,
  indicator,
  searchable = false,
  searchPlaceholder = 'Buscar...',
  onOpenChange,
  ...props
}: {
  indicatorPosition?: 'left' | 'right';
  indicatorVisibility?: boolean;
  indicator?: ReactNode;
  /** Quando true, o dropdown exibe um campo de busca no topo que filtra os SelectItem. */
  searchable?: boolean;
  /** Placeholder do input de busca (somente quando `searchable`). */
  searchPlaceholder?: string;
} & React.ComponentProps<typeof SelectPrimitive.Root>) => {
  const [term, setTerm] = React.useState('');

  // Reset da busca toda vez que o dropdown fecha — evita "lembrar" filtro
  // antigo na próxima abertura. Mantém o callback do consumidor intacto.
  const handleOpenChange = React.useCallback(
    (open: boolean) => {
      if (!open) setTerm('');
      onOpenChange?.(open);
    },
    [onOpenChange],
  );

  return (
    <SelectContext.Provider value={{ indicatorPosition, indicatorVisibility, indicator }}>
      <SelectSearchContext.Provider
        value={{ enabled: searchable, term, setTerm, placeholder: searchPlaceholder }}
      >
        <SelectPrimitive.Root onOpenChange={handleOpenChange} {...props} />
      </SelectSearchContext.Provider>
    </SelectContext.Provider>
  );
};

function SelectGroup({ ...props }: React.ComponentProps<typeof SelectPrimitive.Group>) {
  return <SelectPrimitive.Group data-slot="select-group" {...props} />;
}

function SelectValue({ ...props }: React.ComponentProps<typeof SelectPrimitive.Value>) {
  return <SelectPrimitive.Value data-slot="select-value" {...props} />;
}

// Define size variants for SelectTrigger
const selectTriggerVariants = cva(
  `
    flex bg-background w-full items-center justify-between outline-none border border-input shadow-xs shadow-black/5 transition-shadow 
    text-foreground data-placeholder:text-muted-foreground focus-visible:border-ring focus-visible:outline-none focus-visible:ring-[3px] 
    focus-visible:ring-ring/30 disabled:cursor-not-allowed disabled:opacity-50 [&>span]:line-clamp-1 
    aria-invalid:border-destructive/60 aria-invalid:ring-destructive/10 dark:aria-invalid:border-destructive dark:aria-invalid:ring-destructive/20
    [[data-invalid=true]_&]:border-destructive/60 [[data-invalid=true]_&]:ring-destructive/10  dark:[[data-invalid=true]_&]:border-destructive dark:[[data-invalid=true]_&]:ring-destructive/20
  `,
  {
    variants: {
      size: {
        sm: 'h-7 px-2.5 text-xs gap-1 rounded-md',
        md: 'h-8.5 px-3 text-[0.8125rem] leading-(--text-sm--line-height) gap-1 rounded-md',
        lg: 'h-10 px-4 text-sm gap-1.5 rounded-md',
      },
    },
    defaultVariants: {
      size: 'md',
    },
  },
);

export interface SelectTriggerProps
  extends React.ComponentProps<typeof SelectPrimitive.Trigger>,
    VariantProps<typeof selectTriggerVariants> {}

function SelectTrigger({ className, children, size, ...props }: SelectTriggerProps) {
  return (
    <SelectPrimitive.Trigger
      data-slot="select-trigger"
      className={cn(selectTriggerVariants({ size }), className)}
      {...props}
    >
      {children}
      <SelectPrimitive.Icon asChild>
        <ChevronDown className="h-4 w-4 opacity-60 -me-0.5" />
      </SelectPrimitive.Icon>
    </SelectPrimitive.Trigger>
  );
}

function SelectScrollUpButton({ className, ...props }: React.ComponentProps<typeof SelectPrimitive.ScrollUpButton>) {
  return (
    <SelectPrimitive.ScrollUpButton
      data-slot="select-scroll-up-button"
      className={cn('flex cursor-default items-center justify-center py-1', className)}
      {...props}
    >
      <ChevronUp className="h-4 w-4" />
    </SelectPrimitive.ScrollUpButton>
  );
}

function SelectScrollDownButton({
  className,
  ...props
}: React.ComponentProps<typeof SelectPrimitive.ScrollDownButton>) {
  return (
    <SelectPrimitive.ScrollDownButton
      data-slot="select-scroll-down-button"
      className={cn('flex cursor-default items-center justify-center py-1', className)}
      {...props}
    >
      <ChevronDown className="h-4 w-4" />
    </SelectPrimitive.ScrollDownButton>
  );
}

function SelectContent({
  className,
  children,
  position = 'popper',
  onOpenAutoFocus,
  ...props
}: React.ComponentProps<typeof SelectPrimitive.Content>) {
  const search = React.useContext(SelectSearchContext);
  const inputRef = React.useRef<HTMLInputElement | null>(null);

  // Quando o modo `searchable` está ligado, queremos focar o campo de busca
  // ao abrir e impedir que o Radix Select faça o auto-focus em algum item
  // (que tiraria o foco do input antes do usuário começar a digitar).
  const handleOpenAutoFocus = React.useCallback(
    (e: Event) => {
      onOpenAutoFocus?.(e as unknown as Parameters<NonNullable<typeof onOpenAutoFocus>>[0]);
      if (search.enabled) {
        e.preventDefault();
        // Foco no input acontece via autoFocus do <input/>, mas garantimos via ref
        // como fallback caso o autoFocus seja perdido por algum portal.
        requestAnimationFrame(() => inputRef.current?.focus());
      }
    },
    [onOpenAutoFocus, search.enabled],
  );

  return (
    <SelectPrimitive.Portal>
      <SelectPrimitive.Content
        data-slot="select-content"
        className={cn(
          'relative z-50 max-h-96 min-w-[8rem] overflow-hidden rounded-md border border-border bg-popover shadow-md shadow-black/5 text-secondary-foreground data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2',
          position === 'popper' &&
            'data-[side=bottom]:translate-y-1.5 data-[side=left]:-translate-x-1.5 data-[side=right]:translate-x-1.5 data-[side=top]:-translate-y-1.5',
          className,
        )}
        position={position}
        onOpenAutoFocus={handleOpenAutoFocus}
        {...props}
      >
        {search.enabled && (
          <div
            className="flex items-center gap-2 border-b border-border px-2.5 py-1.5"
            // Evita que cliques no input dispatchem seleção de itens do Radix.
            onPointerDown={(e) => e.stopPropagation()}
            onMouseDown={(e) => e.stopPropagation()}
          >
            <Search className="size-3.5 text-muted-foreground shrink-0" />
            <input
              ref={inputRef}
              type="text"
              autoFocus
              autoComplete="off"
              spellCheck={false}
              value={search.term}
              placeholder={search.placeholder}
              onChange={(e) => search.setTerm(e.target.value)}
              // Bloqueia o typeahead nativo do Radix (que captura teclas para
              // navegar entre itens) e evita que setas/Enter fechem o popover
              // enquanto o foco está no input — o usuário fecha clicando num
              // item ou usando Esc.
              onKeyDown={(e) => {
                if (e.key === 'Escape') return; // deixa o Radix fechar
                e.stopPropagation();
              }}
              className="h-7 w-full bg-transparent text-sm outline-none placeholder:text-muted-foreground"
            />
          </div>
        )}

        <SelectScrollUpButton />
        <SelectPrimitive.Viewport
          className={cn(
            'p-1.5',
            position === 'popper' &&
              'h-[var(--radix-select-trigger-height)] w-full min-w-[var(--radix-select-trigger-width)]',
          )}
        >
          {children}
        </SelectPrimitive.Viewport>
        <SelectScrollDownButton />
      </SelectPrimitive.Content>
    </SelectPrimitive.Portal>
  );
}

function SelectLabel({ className, ...props }: React.ComponentProps<typeof SelectPrimitive.Label>) {
  return (
    <SelectPrimitive.Label
      data-slot="select-label"
      className={cn('py-1.5 ps-8 pe-2 text-xs text-muted-foreground font-medium', className)}
      {...props}
    />
  );
}

interface SelectItemProps extends React.ComponentProps<typeof SelectPrimitive.Item> {
  /**
   * Texto opcional usado pelo modo `searchable` para filtrar quando
   * `children` não é uma string simples (ex.: contém ícones ou badges).
   * Se omitido, o filtro extrai o texto recursivamente de `children`.
   */
  searchValue?: string;
}

function SelectItem({ className, children, searchValue, ...props }: SelectItemProps) {
  const { indicatorPosition, indicatorVisibility, indicator } = React.useContext(SelectContext);
  const search = React.useContext(SelectSearchContext);

  if (search.enabled && search.term) {
    const haystack = (searchValue ?? extractText(children)).toLowerCase();
    if (haystack && !haystack.includes(search.term.toLowerCase())) {
      return null;
    }
  }

  return (
    <SelectPrimitive.Item
      data-slot="select-item"
      className={cn(
        'relative flex w-full cursor-default select-none items-center rounded-sm py-1.5 text-sm outline-hidden text-foreground hover:bg-accent focus:bg-accent data-disabled:pointer-events-none data-disabled:opacity-50',
        indicatorPosition === 'left' ? 'ps-8 pe-2' : 'pe-8 ps-2',
        className,
      )}
      {...props}
    >
      {indicatorVisibility &&
        (indicator && isValidElement(indicator) ? (
          indicator
        ) : (
          <span
            className={cn(
              'absolute flex h-3.5 w-3.5 items-center justify-center',
              indicatorPosition === 'left' ? 'start-2' : 'end-2',
            )}
          >
            <SelectPrimitive.ItemIndicator>
              <Check className="h-4 w-4 text-primary" />
            </SelectPrimitive.ItemIndicator>
          </span>
        ))}
      <SelectPrimitive.ItemText>{children}</SelectPrimitive.ItemText>
    </SelectPrimitive.Item>
  );
}

function SelectIndicator({
  children,
  className,
  ...props
}: React.ComponentProps<typeof SelectPrimitive.ItemIndicator>) {
  const { indicatorPosition } = React.useContext(SelectContext);

  return (
    <span
      data-slot="select-indicator"
      className={cn(
        'absolute flex top-1/2 -translate-y-1/2 items-center justify-center',
        indicatorPosition === 'left' ? 'start-2' : 'end-2',
        className,
      )}
      {...props}
    >
      <SelectPrimitive.ItemIndicator>{children}</SelectPrimitive.ItemIndicator>
    </span>
  );
}

function SelectSeparator({ className, ...props }: React.ComponentProps<typeof SelectPrimitive.Separator>) {
  return (
    <SelectPrimitive.Separator
      data-slot="select-separator"
      className={cn('-mx-1.5 my-1.5 h-px bg-border', className)}
      {...props}
    />
  );
}

export {
  Select,
  SelectContent,
  SelectGroup,
  SelectIndicator,
  SelectItem,
  SelectLabel,
  SelectScrollDownButton,
  SelectScrollUpButton,
  SelectSeparator,
  SelectTrigger,
  SelectValue,
};
