import { useEffect, useState } from 'react';
import { Check, ChevronsUpDown, type LucideIcon } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { cn } from '@/lib/utils';
import {
  Command,
  CommandEmpty,
  CommandGroup,
  CommandInput,
  CommandItem,
  CommandList,
} from '@/components/ui/command';
import { ScrollArea } from '@/components/ui/scroll-area';
import { financeiroToolbarSoftBlueCommandClass } from '@/lib/financeiro-toolbar-accent';

export interface MultiSelectOption {
  value: string;
  label: string;
  /** Ícone opcional (ex.: Lucide) à esquerda do rótulo. */
  icon?: LucideIcon;
}

/**
 * Lista de multi-seleção com busca, para usar **dentro** de um Popover já aberto
 * (ex.: chip de filtro). Sem trigger aninhado — evita menu dentro de menu.
 */
export function MultiSelectEmbedded({
  options,
  value,
  onValueChange,
  searchPlaceholder = 'Buscar…',
  emptyText = 'Nenhum resultado encontrado.',
  selectAllLabel = 'Selecionar todos',
}: {
  options: MultiSelectOption[];
  value: string[];
  onValueChange: (v: string[]) => void;
  searchPlaceholder?: string;
  emptyText?: string;
  selectAllLabel?: string;
}) {
  const selectableValues = options.map((o) => o.value);
  const allSelected =
    selectableValues.length > 0 && selectableValues.every((v) => value.includes(v));

  function toggleItem(itemValue: string) {
    onValueChange(
      value.includes(itemValue) ? value.filter((x) => x !== itemValue) : [...value, itemValue],
    );
  }

  function toggleSelectAll() {
    if (allSelected) onValueChange([]);
    else onValueChange([...selectableValues]);
  }

  return (
    <Command
      shouldFilter={options.length > 0}
      className={cn(
        'h-auto max-h-[min(420px,72vh)] w-full rounded-none border-0 bg-transparent shadow-none [&_[cmdk-input-wrapper]]:shrink-0 [&_[cmdk-input-wrapper]]:border-b',
        financeiroToolbarSoftBlueCommandClass,
      )}
    >
      <CommandInput placeholder={searchPlaceholder} />
      <ScrollArea className="max-h-[min(280px,50vh)]" viewportClassName="max-h-[inherit]">
        <CommandList className="max-h-none overflow-visible">
          <CommandEmpty className="py-6 text-sm">{emptyText}</CommandEmpty>
          <CommandGroup className="overflow-visible p-1.5">
            {selectableValues.length > 0 && (
              <CommandItem
                value={`__all__ ${selectAllLabel}`}
                onSelect={toggleSelectAll}
                className="gap-2 font-medium"
              >
                <div
                  className={cn(
                    'flex size-4 shrink-0 items-center justify-center rounded-sm border',
                    allSelected
                      ? 'border-primary bg-primary text-primary-foreground'
                      : 'border-input',
                  )}
                >
                  {allSelected && <Check className="size-3" strokeWidth={3} />}
                </div>
                <span className="text-[0.8125rem]">{selectAllLabel}</span>
              </CommandItem>
            )}
            {options.map((opt) => {
              const selected = value.includes(opt.value);
              const Icon = opt.icon;
              return (
                <CommandItem
                  key={opt.value}
                  value={`${opt.label} ${opt.value}`}
                  onSelect={() => toggleItem(opt.value)}
                  className="gap-2"
                >
                  <div
                    className={cn(
                      'flex size-4 shrink-0 items-center justify-center rounded-sm border',
                      selected
                        ? 'border-primary bg-primary text-primary-foreground'
                        : 'border-input',
                    )}
                  >
                    {selected && <Check className="size-3" strokeWidth={3} />}
                  </div>
                  {Icon ? (
                    <Icon className="size-3.5 shrink-0 text-muted-foreground" aria-hidden />
                  ) : null}
                  <span className="truncate text-[0.8125rem]">{opt.label}</span>
                </CommandItem>
              );
            })}
          </CommandGroup>
        </CommandList>
      </ScrollArea>
    </Command>
  );
}

export function SearchSelectMulti({
  options,
  value,
  onValueChange,
  placeholder = 'Selecione',
  searchPlaceholder = 'Buscar...',
  disabled = false,
  popoverModal = true,
}: {
  options: MultiSelectOption[];
  value: string[];
  onValueChange: (v: string[]) => void;
  placeholder?: string;
  searchPlaceholder?: string;
  disabled?: boolean;
  popoverModal?: boolean;
}) {
  const [open, setOpen] = useState(false);
  const [draft, setDraft] = useState<string[]>(value);

  useEffect(() => {
    if (open) setDraft(value);
  }, [open, value]);

  function handleApply() {
    onValueChange(draft);
    setOpen(false);
  }

  const displayText =
    value.length === 0
      ? placeholder
      : value.length === 1
        ? options.find((o) => o.value === value[0])?.label ?? '1 selecionado'
        : `${value.length} selecionados`;

  return (
    <Popover open={open} onOpenChange={setOpen} modal={popoverModal}>
      <PopoverTrigger asChild>
        <Button
          variant="outline"
          role="combobox"
          aria-expanded={open}
          disabled={disabled}
          className="w-full justify-between font-normal min-h-8.5 h-auto py-1.5 px-3 text-[0.8125rem]"
        >
          <span className={cn('truncate', value.length === 0 && 'text-muted-foreground')}>
            {displayText}
          </span>
          <ChevronsUpDown className="size-3.5 text-muted-foreground shrink-0" />
        </Button>
      </PopoverTrigger>
      <PopoverContent
        className="w-[var(--radix-popover-trigger-width)] max-h-[min(480px,85vh)] p-0 flex flex-col overflow-hidden"
        align="start"
      >
        <MultiSelectEmbedded
          options={options}
          value={draft}
          onValueChange={setDraft}
          searchPlaceholder={searchPlaceholder}
        />
        <div className="border-t border-border bg-background p-2 shrink-0">
          <Button
            type="button"
            variant="foreground"
            size="sm"
            className="w-full rounded-md border-0 bg-blue-600 text-white shadow-none hover:bg-blue-700 hover:text-white"
            onClick={handleApply}
          >
            Aplicar
          </Button>
        </div>
      </PopoverContent>
    </Popover>
  );
}
