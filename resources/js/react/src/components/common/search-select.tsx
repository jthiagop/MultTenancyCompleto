import { useState } from 'react';
import type { ReactNode } from 'react';
import { ChevronsUpDown } from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import {
  Command,
  CommandCheck,
  CommandEmpty,
  CommandGroup,
  CommandInput,
  CommandItem,
  CommandList,
} from '@/components/ui/command';

export interface SearchSelectOption {
  value: string;
  label: string;
  /** Ex.: natureza do parceiro (como data-natureza no x-tenant-select) */
  hint?: string;
  /** URL da imagem exibida à esquerda (ex.: logo do banco) */
  icon?: string | null;
  /** Badges exibidos abaixo do label (ex.: tipo de conta) */
  badges?: Array<{ label: string; variant?: 'default' | 'secondary' | 'destructive' | 'outline'; className?: string }>;
}

export function SearchSelect({
  options,
  value,
  onValueChange,
  placeholder = 'Selecione',
  searchPlaceholder = 'Buscar...',
  disabled = false,
  id,
  /** false dentro de Sheet/Dialog — evita conflito de foco/camada (equivalente a dropdown-parent no Select2) */
  popoverModal = true,
  emptyListMessage = 'Nenhum resultado encontrado.',
  noSearchResultsMessage = 'Nenhum resultado encontrado.',
  suggestionStar,
}: {
  options: SearchSelectOption[];
  value: string;
  onValueChange: (v: string) => void;
  placeholder?: string;
  searchPlaceholder?: string;
  disabled?: boolean;
  id?: string;
  popoverModal?: boolean;
  emptyListMessage?: string;
  noSearchResultsMessage?: string;
  /** ReactNode (ex: SuggestionStar) exibido ao lado da seta do trigger */
  suggestionStar?: ReactNode;
}) {
  const [open, setOpen] = useState(false);
  const selected = options.find((o) => o.value === value);

  return (
    <Popover open={open} onOpenChange={setOpen} modal={popoverModal}>
      <PopoverTrigger asChild>
        <Button
          id={id}
          variant="outline"
          role="combobox"
          aria-expanded={open}
          disabled={disabled}
          className="w-full justify-between font-normal min-h-8.5 h-auto py-1.5 px-3 text-[0.8125rem]"
        >
          <span className="flex items-center gap-2 min-w-0 flex-1">
            {selected?.icon ? (
              <img
                src={selected.icon}
                alt=""
                className="size-5 rounded object-contain shrink-0"
                onError={(e) => { (e.currentTarget as HTMLImageElement).style.display = 'none'; }}
              />
            ) : null}
            <span className="flex flex-col items-start gap-0.5 min-w-0 text-left flex-1">
              <span className={selected ? 'text-foreground truncate w-full' : 'text-muted-foreground truncate w-full'}>
                {selected ? selected.label : placeholder}
              </span>
              {selected?.badges?.length ? (
                <span className="flex flex-wrap gap-1 mt-0.5">
                  {selected.badges.map((b) =>
                    b.className ? (
                      <span key={b.label} className={cn('inline-flex items-center gap-1 text-[0.625rem] font-medium rounded-full px-1.5 py-px leading-none', b.className)}>
                        {b.label}
                      </span>
                    ) : (
                      <Badge key={b.label} variant={b.variant ?? 'secondary'} className="text-[0.625rem] px-1.5 py-0 h-4 font-normal leading-none">
                        {b.label}
                      </Badge>
                    )
                  )}
                </span>
              ) : null}
              {selected?.hint ? (
                <span className="text-[0.6875rem] text-muted-foreground font-normal truncate w-full leading-tight">
                  {selected.hint}
                </span>
              ) : null}
            </span>
          </span>
          <span className="inline-flex shrink-0 items-center gap-1.5">
            {suggestionStar}
            <ChevronsUpDown className="size-3.5 text-muted-foreground shrink-0" />
          </span>
        </Button>
      </PopoverTrigger>
      <PopoverContent className="w-[var(--radix-popover-trigger-width)] p-0" align="start">
        <Command shouldFilter={options.length > 0}>
          <CommandInput placeholder={searchPlaceholder} disabled={options.length === 0} />
          <CommandList>
            {options.length === 0 ? (
              <div
                className="px-3 py-8 text-center text-sm text-muted-foreground"
                role="status"
                aria-live="polite"
              >
                {emptyListMessage}
              </div>
            ) : (
              <>
                <CommandEmpty className="py-6 text-sm">{noSearchResultsMessage}</CommandEmpty>
                <CommandGroup>
                  {options.map((opt) => (
                    <CommandItem
                      key={opt.value}
                      value={`${opt.label} ${opt.hint ?? ''}`.trim()}
                      onSelect={() => {
                        onValueChange(opt.value);
                        setOpen(false);
                      }}
                      className="items-center gap-2"
                    >
                      {opt.icon ? (
                        <img
                          src={opt.icon}
                          alt=""
                          className="size-5 rounded object-contain shrink-0"
                          onError={(e) => { (e.currentTarget as HTMLImageElement).style.display = 'none'; }}
                        />
                      ) : null}
                      <div className="flex flex-col gap-0.5 min-w-0 flex-1">
                        <span className="truncate text-[0.8125rem] leading-tight">{opt.label}</span>
                        {opt.badges?.length ? (
                          <span className="flex flex-wrap gap-1 mt-0.5">
                            {opt.badges.map((b) =>
                              b.className ? (
                                <span key={b.label} className={cn('inline-flex items-center gap-1 text-[0.625rem] font-medium rounded-full px-1.5 py-px leading-none', b.className)}>
                                  {b.label}
                                </span>
                              ) : (
                                <Badge key={b.label} variant={b.variant ?? 'secondary'} className="text-[0.625rem] px-1.5 py-0 h-4 font-normal leading-none">
                                  {b.label}
                                </Badge>
                              )
                            )}
                          </span>
                        ) : null}
                        {opt.hint ? (
                          <span className="text-[0.6875rem] text-muted-foreground truncate leading-tight">
                            {opt.hint}
                          </span>
                        ) : null}
                      </div>
                      {value === opt.value ? <CommandCheck className="shrink-0 size-4 mt-0.5" /> : null}
                    </CommandItem>
                  ))}
                </CommandGroup>
              </>
            )}
          </CommandList>
        </Command>
      </PopoverContent>
    </Popover>
  );
}
