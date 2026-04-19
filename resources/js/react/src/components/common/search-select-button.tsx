import { useEffect, useState, type ReactNode } from 'react';
import { ChevronsUpDown } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { cn } from '@/lib/utils';
import {
  Command,
  CommandCheck,
  CommandEmpty,
  CommandGroup,
  CommandInput,
  CommandItem,
  CommandList,
} from '@/components/ui/command';
import type { SearchSelectOption } from '@/components/common/search-select';

export type { SearchSelectOption };

/**
 * Select com busca + botão no rodapé do painel.
 * - Padrão: rascunho na lista + **Aplicar** confirma (fluxo tenant-select-button Blade).
 * - Com `replaceApplyButton`: o botão azul vira outra ação (ex. **+ Adicionar Cliente**); aí `commitOnSelect`
 *   deve ser true (padrão automático) para escolher item na lista aplicar e fechar.
 */
export function SearchSelectButton({
  options,
  value,
  onValueChange,
  placeholder = 'Selecione',
  searchPlaceholder = 'Buscar...',
  applyLabel = 'Aplicar',
  /** Classes extras no botão do rodapé (mescla com o estilo azul padrão) */
  applyButtonClassName,
  /** Substitui o botão Aplicar por uma única ação (ex.: navegar para cadastro). */
  replaceApplyButton,
  /** true = clique no item chama onValueChange e fecha. Com `replaceApplyButton`, default é true. */
  commitOnSelect,
  /** Conteúdo abaixo do botão principal (só quando não há `replaceApplyButton`) */
  footerAddon,
  disabled = false,
  id,
  popoverModal = true,
  onApply,
  /** Mensagem quando não há opções (lista vazia). O cmdk não mostra CommandEmpty nesse caso. */
  emptyListMessage = 'Nenhum resultado encontrado.',
  /** Mensagem quando a busca não encontra itens (há opções, mas o filtro zerou). */
  noSearchResultsMessage = 'Nenhum resultado encontrado.',
  suggestionStar,
}: {
  options: SearchSelectOption[];
  value: string;
  onValueChange: (v: string) => void;
  placeholder?: string;
  searchPlaceholder?: string;
  applyLabel?: string;
  applyButtonClassName?: string;
  emptyListMessage?: string;
  noSearchResultsMessage?: string;
  replaceApplyButton?: {
    label: string;
    onClick: (ctx: { close: () => void }) => void;
  };
  commitOnSelect?: boolean;
  footerAddon?: (ctx: { close: () => void }) => ReactNode;
  disabled?: boolean;
  id?: string;
  popoverModal?: boolean;
  onApply?: (value: string) => void;
  /** ReactNode (ex: SuggestionStar) exibido ao lado da seta do trigger */
  suggestionStar?: ReactNode;
}) {
  const [open, setOpen] = useState(false);
  const [draft, setDraft] = useState(value);

  const effectiveCommitOnSelect = commitOnSelect ?? Boolean(replaceApplyButton);

  useEffect(() => {
    if (open) {
      setDraft(value);
    }
  }, [open, value]);

  const display = options.find((o) => o.value === value);

  function close() {
    setOpen(false);
  }

  function handleApply() {
    onValueChange(draft);
    onApply?.(draft);
    setOpen(false);
  }

  function handleSelectOption(optValue: string) {
    if (effectiveCommitOnSelect) {
      onValueChange(optValue);
      setDraft(optValue);
      setOpen(false);
    } else {
      setDraft(optValue);
    }
  }

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
          <span className="flex flex-col items-start gap-0.5 min-w-0 text-left flex-1">
            <span className={display ? 'text-foreground truncate w-full' : 'text-muted-foreground truncate w-full'}>
              {display ? display.label : placeholder}
            </span>
            {display?.hint ? (
              <span className="text-[0.6875rem] text-muted-foreground font-normal truncate w-full leading-tight">
                {display.hint}
              </span>
            ) : null}
          </span>
          <span className="inline-flex shrink-0 items-center gap-1.5">
            {suggestionStar}
            <ChevronsUpDown className="size-3.5 text-muted-foreground shrink-0" />
          </span>
        </Button>
      </PopoverTrigger>
      <PopoverContent className="w-[var(--radix-popover-trigger-width)] p-0 flex flex-col overflow-hidden" align="start">
        <Command className="[&_[cmdk-group-heading]]:px-2" shouldFilter={options.length > 0}>
          <CommandInput placeholder={searchPlaceholder} disabled={options.length === 0} />
          <CommandList className="max-h-[min(260px,40vh)]">
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
                      onSelect={() => handleSelectOption(opt.value)}
                      className="items-start gap-2"
                    >
                      <div className="flex flex-col gap-0.5 min-w-0 flex-1">
                        <span className="truncate text-[0.8125rem] leading-tight">{opt.label}</span>
                        {opt.hint ? (
                          <span className="text-[0.6875rem] text-muted-foreground truncate leading-tight">
                            {opt.hint}
                          </span>
                        ) : null}
                      </div>
                      {(effectiveCommitOnSelect ? value === opt.value : draft === opt.value) ? (
                        <CommandCheck className="shrink-0 size-4 mt-0.5" />
                      ) : null}
                    </CommandItem>
                  ))}
                </CommandGroup>
              </>
            )}
          </CommandList>
        </Command>
        <div className="border-t border-border bg-background p-2 shrink-0 space-y-2">
          {replaceApplyButton ? (
            <Button
              type="button"
              variant="foreground"
              size="sm"
              className={cn(
                'w-full rounded-md border-0 bg-blue-600 text-white shadow-none hover:bg-blue-700 hover:text-white',
                applyButtonClassName,
              )}
              onClick={() => replaceApplyButton.onClick({ close })}
            >
              {replaceApplyButton.label}
            </Button>
          ) : (
            <>
              <Button
                type="button"
                variant="foreground"
                size="sm"
                className={cn(
                  'w-full rounded-md border-0 bg-blue-600 text-white shadow-none hover:bg-blue-700 hover:text-white',
                  applyButtonClassName,
                )}
                onClick={handleApply}
              >
                {applyLabel}
              </Button>
              {footerAddon ? footerAddon({ close }) : null}
            </>
          )}
        </div>
      </PopoverContent>
    </Popover>
  );
}
