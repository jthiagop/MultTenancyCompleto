'use client';

import * as React from 'react';
import { Command as CommandPrimitive } from 'cmdk';
import { Search } from 'lucide-react';
import { cn } from '@/lib/utils';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { Command, CommandEmpty, CommandItem, CommandList } from '@/components/ui/command';

type ComboboxContextValue<T> = {
  items: T[];
  selectedItem: T | null;
  setSelectedItem: (item: T | null) => void;
  open: boolean;
  setOpen: (open: boolean) => void;
  getItemKey: (item: T) => string;
  itemToString: (item: T) => string;
  getItemFilterText: (item: T) => string;
  placeholder?: string;
};

const ComboboxContext = React.createContext<ComboboxContextValue<unknown> | null>(null);

function useComboboxContext<T>() {
  const ctx = React.useContext(ComboboxContext);
  if (!ctx) throw new Error('Combobox components must be used within <Combobox>');
  return ctx as ComboboxContextValue<T>;
}

export interface ComboboxProps<T> {
  items: T[];
  getItemKey: (item: T) => string;
  itemToString: (item: T) => string;
  getItemFilterText?: (item: T) => string;
  /** Controle por chave (recomendado para formulários) */
  valueKey?: string | null;
  onValueChangeKey?: (key: string | null) => void;
  /** Controle pelo objeto inteiro */
  value?: T | null;
  onValueChange?: (item: T | null) => void;
  defaultValueKey?: string | null;
  defaultValue?: T | null;
  placeholder?: string;
  children: React.ReactNode;
}

function Combobox<T>({
  items,
  getItemKey,
  itemToString,
  getItemFilterText,
  valueKey,
  onValueChangeKey,
  value,
  onValueChange,
  defaultValueKey,
  defaultValue,
  placeholder = 'Selecione…',
  children,
}: ComboboxProps<T>) {
  const filterText = getItemFilterText ?? itemToString;

  const isControlledKey = valueKey !== undefined;
  const isControlledObj = value !== undefined;

  const [uncontrolledKey, setUncontrolledKey] = React.useState<string | null>(() => {
    if (defaultValue != null) return getItemKey(defaultValue);
    if (defaultValueKey != null) return defaultValueKey;
    return null;
  });

  const [open, setOpen] = React.useState(false);

  const resolvedKey = React.useMemo(() => {
    if (isControlledKey) return valueKey ?? null;
    if (isControlledObj && value != null) return getItemKey(value);
    if (isControlledObj && value == null) return null;
    return uncontrolledKey;
  }, [isControlledKey, isControlledObj, valueKey, value, uncontrolledKey, getItemKey]);

  const selectedItem = React.useMemo(() => {
    if (resolvedKey == null || resolvedKey === '') return null;
    return items.find((i) => getItemKey(i) === resolvedKey) ?? null;
  }, [items, resolvedKey, getItemKey]);

  const setSelectedItem = React.useCallback(
    (item: T | null) => {
      const k = item == null ? null : getItemKey(item);
      if (!isControlledKey && !isControlledObj) {
        setUncontrolledKey(k);
      }
      onValueChangeKey?.(k);
      onValueChange?.(item);
    },
    [getItemKey, isControlledKey, isControlledObj, onValueChangeKey, onValueChange],
  );

  const ctx: ComboboxContextValue<T> = {
    items,
    selectedItem,
    setSelectedItem,
    open,
    setOpen,
    getItemKey,
    itemToString,
    getItemFilterText: filterText,
    placeholder,
  };

  return (
    <ComboboxContext.Provider value={ctx as ComboboxContextValue<unknown>}>
      <Popover open={open} onOpenChange={setOpen}>
        {children}
      </Popover>
    </ComboboxContext.Provider>
  );
}

function ComboboxTrigger({ render }: { render: React.ReactElement }) {
  return <PopoverTrigger asChild>{render}</PopoverTrigger>;
}

function ComboboxValue({ className }: { className?: string }) {
  const { selectedItem, itemToString, placeholder } = useComboboxContext();
  return (
    <span className={cn('truncate text-left', className)}>
      {selectedItem ? itemToString(selectedItem) : <span className="text-muted-foreground">{placeholder}</span>}
    </span>
  );
}

function ComboboxContent({
  className,
  align = 'start',
  sideOffset = 4,
  children,
  ...props
}: React.ComponentProps<typeof PopoverContent>) {
  return (
    <PopoverContent
      align={align}
      sideOffset={sideOffset}
      className={cn(
        'max-h-[min(320px,var(--radix-popover-content-available-height))] w-[var(--radix-popover-trigger-width)] min-w-[min(100%,var(--radix-popover-trigger-width))] p-0',
        className,
      )}
      {...props}
    >
      <Command className="rounded-md border-0 bg-transparent shadow-none">{children}</Command>
    </PopoverContent>
  );
}

/** `showTrigger={false}` oculta o ícone de busca (compatível com o snippet de exemplo). */
function ComboboxInput({
  className,
  showTrigger = true,
  ...props
}: React.ComponentProps<typeof CommandPrimitive.Input> & { showTrigger?: boolean }) {
  const inner = (
    <CommandPrimitive.Input
      className={cn(
        'flex h-10 w-full rounded-md bg-transparent py-2.5 text-sm outline-hidden text-foreground placeholder:text-muted-foreground disabled:cursor-not-allowed disabled:opacity-50',
        className,
      )}
      {...props}
    />
  );
  return (
    <div className="flex items-center border-border border-b px-3" cmdk-input-wrapper="" data-slot="combobox-input">
      {showTrigger ? <Search className="me-2 h-4 w-4 shrink-0 opacity-50" aria-hidden /> : null}
      {inner}
    </div>
  );
}

function ComboboxEmpty({ children, className, ...props }: React.ComponentProps<typeof CommandEmpty>) {
  return (
    <CommandEmpty className={cn('py-6 text-center text-sm text-muted-foreground', className)} {...props}>
      {children ?? 'Nenhum resultado.'}
    </CommandEmpty>
  );
}

function ComboboxList<T>({
  itemsType,
  children,
}: {
  /** Mesma fonte de dados que `items` do `<Combobox>` — só para o TypeScript inferir `T`. */
  itemsType?: readonly T[];
  children: (item: T) => React.ReactNode;
}) {
  void itemsType;
  const ctx = React.useContext(ComboboxContext);
  if (!ctx) throw new Error('Combobox components must be used within <Combobox>');
  const items = ctx.items as T[];
  const getItemKey = ctx.getItemKey as (item: T) => string;
  return (
    <CommandList className="max-h-[260px]">
      {items.map((item) => (
        <React.Fragment key={getItemKey(item)}>{children(item)}</React.Fragment>
      ))}
    </CommandList>
  );
}

function ComboboxItem<T>({
  value,
  children,
  className,
  ...props
}: {
  value: T;
  children: React.ReactNode;
} & Omit<React.ComponentProps<typeof CommandItem>, 'value' | 'onSelect'>) {
  const ctx = React.useContext(ComboboxContext);
  if (!ctx) throw new Error('Combobox components must be used within <Combobox>');
  const { setSelectedItem, setOpen, getItemFilterText, getItemKey } = ctx as ComboboxContextValue<T>;
  const filterValue = getItemFilterText(value);
  const key = getItemKey(value);
  return (
    <CommandItem
      value={`${key} ${filterValue}`}
      keywords={[key, filterValue]}
      onSelect={() => {
        setSelectedItem(value);
        setOpen(false);
      }}
      className={cn('cursor-pointer', className)}
      {...props}
    >
      {children}
    </CommandItem>
  );
}

export {
  Combobox,
  ComboboxContent,
  ComboboxEmpty,
  ComboboxInput,
  ComboboxItem,
  ComboboxList,
  ComboboxTrigger,
  ComboboxValue,
};
