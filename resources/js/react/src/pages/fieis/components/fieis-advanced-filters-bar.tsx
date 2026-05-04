import {
  createContext,
  useCallback,
  useContext,
  useMemo,
  useState,
  type ReactNode,
} from 'react';
import {
  CalendarDays,
  Check,
  ChevronDown,
  Funnel,
  Heart,
  MapPin,
  Trash2,
  UserCog,
  UserRound,
  X,
} from 'lucide-react';
import { Popover as PopoverPrimitive } from 'radix-ui';
import { Button } from '@/components/ui/button';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import {
  financeiroToolbarSoftBlueChipClass,
  financeiroToolbarSoftBlueClass,
  financeiroToolbarSoftBlueInputClass,
  financeiroToolbarSoftBluePopoverTitleClass,
} from '@/lib/financeiro-toolbar-accent';
import { cn } from '@/lib/utils';
import type { FieisAdvancedFiltersState } from '@/hooks/useFieis';

// ── Tipos dos filtros disponíveis ──────────────────────────────────────────────

type FilterKind = 'idade' | 'cidade' | 'estado_civil' | 'situacao' | 'nascimento' | 'sexo';

const ALL_KINDS: FilterKind[] = [
  'idade',
  'cidade',
  'estado_civil',
  'situacao',
  'nascimento',
  'sexo',
];

const META: Record<FilterKind, { title: string; menu: string; icon: React.ReactNode }> = {
  idade: {
    title: 'Idade',
    menu: 'Idade (mín. / máx.)',
    icon: <UserRound className="size-3.5 shrink-0 text-muted-foreground" />,
  },
  cidade: {
    title: 'Cidade',
    menu: 'Cidade',
    icon: <MapPin className="size-3.5 shrink-0 text-muted-foreground" />,
  },
  estado_civil: {
    title: 'Estado civil',
    menu: 'Estado civil',
    icon: <Heart className="size-3.5 shrink-0 text-muted-foreground" />,
  },
  situacao: {
    title: 'Situação',
    menu: 'Situação',
    icon: <UserCog className="size-3.5 shrink-0 text-muted-foreground" />,
  },
  nascimento: {
    title: 'Data de nascimento',
    menu: 'Data de nascimento',
    icon: <CalendarDays className="size-3.5 shrink-0 text-muted-foreground" />,
  },
  sexo: {
    title: 'Sexo',
    menu: 'Sexo',
    icon: <UserRound className="size-3.5 shrink-0 text-muted-foreground" />,
  },
};

const ESTADO_CIVIL_OPTIONS = [
  'Solteiro(a)',
  'Casado(a)',
  'União Estável',
  'Separado(a)',
  'Divorciado(a)',
  'Viúvo(a)',
  'Outro',
];

const SITUACAO_OPTIONS = [
  { value: 'Ativo', label: 'Ativo' },
  { value: 'Inativo', label: 'Inativo' },
  { value: 'Falecido', label: 'Falecido' },
  { value: 'Mudou-se', label: 'Mudou-se' },
];

const SEXO_OPTIONS = [
  { value: 'M', label: 'Masculino' },
  { value: 'F', label: 'Feminino' },
  { value: 'Outro', label: 'Outro' },
];

// ── Helpers ────────────────────────────────────────────────────────────────────

function isActive(f: FieisAdvancedFiltersState, k: FilterKind): boolean {
  // Considera "ativo" assim que a chave existe (mesmo array vazio),
  // para que o chip apareça e o usuário consiga editar/remover via UI.
  // O envio de query string já lida com arrays vazios (não envia o param).
  switch (k) {
    case 'idade':       return f.idade !== undefined;
    case 'cidade':      return f.cidade !== undefined;
    case 'estado_civil': return f.estado_civil !== undefined;
    case 'situacao':    return f.situacao !== undefined;
    case 'nascimento':  return f.nascimento !== undefined;
    case 'sexo':        return f.sexo !== undefined;
    default:            return false;
  }
}

function activeKindsList(f: FieisAdvancedFiltersState): FilterKind[] {
  return ALL_KINDS.filter((k) => isActive(f, k));
}

function chipText(f: FieisAdvancedFiltersState, k: FilterKind): string {
  const title = META[k].title;
  switch (k) {
    case 'idade': {
      const { min, max } = f.idade ?? {};
      const parts: string[] = [];
      if (min != null) parts.push(`≥ ${min}`);
      if (max != null) parts.push(`≤ ${max}`);
      return `${title}: ${parts.length ? parts.join(' e ') + ' anos' : 'definir'}`;
    }
    case 'cidade':
      return `${title}: ${f.cidade?.trim() || 'definir'}`;
    case 'estado_civil': {
      const v = f.estado_civil ?? [];
      return `${title}: ${v.length > 2 ? `${v.length} selecionados` : v.join(', ') || 'definir'}`;
    }
    case 'situacao': {
      const v = f.situacao ?? [];
      return `${title}: ${v.join(', ') || 'definir'}`;
    }
    case 'nascimento': {
      const { from, to } = f.nascimento ?? {};
      if (!from && !to) return `${title}: definir`;
      return `${title}: ${from ?? '…'} → ${to ?? '…'}`;
    }
    case 'sexo': {
      const map: Record<string, string> = { M: 'Masculino', F: 'Feminino', Outro: 'Outro' };
      const v = (f.sexo ?? []).map((s) => map[s] ?? s);
      return `${title}: ${v.join(', ') || 'definir'}`;
    }
    default:
      return title;
  }
}

// ── Context ────────────────────────────────────────────────────────────────────

type FiltersCtx = {
  value: FieisAdvancedFiltersState;
  onChange: (next: FieisAdvancedFiltersState) => void;
  editing: FilterKind | null;
  setEditing: (k: FilterKind | null) => void;
  addKind: (k: FilterKind) => void;
  removeKind: (k: FilterKind) => void;
  clearAll: () => void;
  active: FilterKind[];
  available: FilterKind[];
};

const FiltersContext = createContext<FiltersCtx | null>(null);

function useFiltersContext(): FiltersCtx {
  const ctx = useContext(FiltersContext);
  if (!ctx) throw new Error('Use inside FieisAdvancedFiltersScope');
  return ctx;
}

// ── Scope ──────────────────────────────────────────────────────────────────────

export function FieisAdvancedFiltersScope({
  value,
  onChange,
  children,
}: {
  value: FieisAdvancedFiltersState;
  onChange: (next: FieisAdvancedFiltersState) => void;
  children: ReactNode;
}) {
  const [editing, setEditing] = useState<FilterKind | null>(null);

  const addKind = useCallback(
    (k: FilterKind) => {
      switch (k) {
        case 'idade':       onChange({ ...value, idade: {} }); break;
        case 'cidade':      onChange({ ...value, cidade: '' }); break;
        case 'estado_civil': onChange({ ...value, estado_civil: [] }); break;
        case 'situacao':    onChange({ ...value, situacao: [] }); break;
        case 'nascimento':  onChange({ ...value, nascimento: {} }); break;
        case 'sexo':        onChange({ ...value, sexo: [] }); break;
      }
      // Defere para que o chip seja renderizado antes de abrir o popover.
      requestAnimationFrame(() => setEditing(k));
    },
    [onChange, value],
  );

  const removeKind = useCallback(
    (k: FilterKind) => {
      const next = { ...value };
      switch (k) {
        case 'idade':       delete next.idade; break;
        case 'cidade':      delete next.cidade; break;
        case 'estado_civil': delete next.estado_civil; break;
        case 'situacao':    delete next.situacao; break;
        case 'nascimento':  delete next.nascimento; break;
        case 'sexo':        delete next.sexo; break;
      }
      onChange(next);
      setEditing((cur) => (cur === k ? null : cur));
    },
    [onChange, value],
  );

  const clearAll = useCallback(() => {
    onChange({});
    setEditing(null);
  }, [onChange]);

  const active = activeKindsList(value);
  const available = ALL_KINDS.filter((k) => !isActive(value, k));

  const ctxValue = useMemo(
    () => ({ value, onChange, editing, setEditing, addKind, removeKind, clearAll, active, available }),
    [value, onChange, editing, addKind, removeKind, clearAll, active, available],
  );

  return <FiltersContext.Provider value={ctxValue}>{children}</FiltersContext.Provider>;
}

// ── Trigger "Mais filtros" ─────────────────────────────────────────────────────

export function FieisAdvancedFiltersTrigger() {
  const { available, addKind } = useFiltersContext();

  return (
    <DropdownMenu>
      <DropdownMenuTrigger asChild>
        <Button
          type="button"
          variant="outline"
          size="sm"
          className={cn(financeiroToolbarSoftBlueClass, 'h-10 min-w-44 shrink-0 gap-1.5 px-4 font-normal')}
        >
          <Funnel className="size-4" />
          Mais filtros
          <ChevronDown className="size-3.5 opacity-60" />
        </Button>
      </DropdownMenuTrigger>
      <DropdownMenuContent align="start" className="w-56">
        {available.length === 0 ? (
          <div className="px-2 py-1.5 text-xs text-muted-foreground">
            Todos os filtros ativos
          </div>
        ) : (
          available.map((k) => (
            <DropdownMenuItem key={k} onSelect={() => addKind(k)} className="gap-2">
              {META[k].icon}
              {META[k].menu}
            </DropdownMenuItem>
          ))
        )}
      </DropdownMenuContent>
    </DropdownMenu>
  );
}

// ── Editor de cada filtro (renderizado dentro do PopoverContent) ──────────────

function FilterEditor({
  kind,
  value,
  onChange,
}: {
  kind: FilterKind;
  value: FieisAdvancedFiltersState;
  onChange: (next: FieisAdvancedFiltersState) => void;
}) {
  if (kind === 'idade') {
    return (
      <div className="p-4 space-y-3">
        <div className="flex flex-col gap-1">
          <span className="text-sm font-semibold text-blue-900 dark:text-blue-100">Idade</span>
          <span className="text-xs text-muted-foreground">Defina a faixa etária em anos.</span>
        </div>
        <div className="grid grid-cols-2 gap-3">
          <label className="flex flex-col gap-1.5 text-xs font-medium text-muted-foreground">
            Mínima
            <Input
              type="number"
              min={0}
              max={150}
              placeholder="Ex.: 18"
              autoFocus
              className={cn(financeiroToolbarSoftBlueInputClass, 'h-9 rounded-md')}
              value={value.idade?.min != null ? String(value.idade.min) : ''}
              onChange={(e) => {
                const n = e.target.value === '' ? undefined : Number(e.target.value);
                onChange({ ...value, idade: { ...value.idade, min: n } });
              }}
            />
          </label>
          <label className="flex flex-col gap-1.5 text-xs font-medium text-muted-foreground">
            Máxima
            <Input
              type="number"
              min={0}
              max={150}
              placeholder="Ex.: 65"
              className={cn(financeiroToolbarSoftBlueInputClass, 'h-9 rounded-md')}
              value={value.idade?.max != null ? String(value.idade.max) : ''}
              onChange={(e) => {
                const n = e.target.value === '' ? undefined : Number(e.target.value);
                onChange({ ...value, idade: { ...value.idade, max: n } });
              }}
            />
          </label>
        </div>
      </div>
    );
  }

  if (kind === 'cidade') {
    return (
      <div className="p-4 space-y-3">
        <div className="flex flex-col gap-1">
          <span className="text-sm font-semibold text-blue-900 dark:text-blue-100">Cidade</span>
          <span className="text-xs text-muted-foreground">Busca parcial pelo nome da cidade.</span>
        </div>
        <Input
          type="text"
          placeholder="Ex.: São Paulo"
          autoFocus
          className={cn(financeiroToolbarSoftBlueInputClass, 'h-9 rounded-md')}
          value={value.cidade ?? ''}
          onChange={(e) => onChange({ ...value, cidade: e.target.value || undefined })}
        />
      </div>
    );
  }

  if (kind === 'estado_civil') {
    const selected = value.estado_civil ?? [];
    const toggle = (opt: string) => {
      const next = selected.includes(opt)
        ? selected.filter((v) => v !== opt)
        : [...selected, opt];
      onChange({ ...value, estado_civil: next });
    };
    return (
      <div className="flex flex-col">
        <div className={cn('px-4 py-3 border-b', financeiroToolbarSoftBluePopoverTitleClass)}>
          <span className="text-sm font-semibold text-blue-900 dark:text-blue-100">Estado civil</span>
        </div>
        <div className="max-h-72 overflow-y-auto py-1">
          {ESTADO_CIVIL_OPTIONS.map((opt) => {
            const isSelected = selected.includes(opt);
            return (
              <button
                key={opt}
                type="button"
                onClick={() => toggle(opt)}
                className={cn(
                  'flex w-full items-center gap-2.5 px-4 py-2 text-sm text-left transition-colors',
                  isSelected ? 'bg-blue-50 dark:bg-blue-950/40' : 'hover:bg-muted/60',
                )}
              >
                <span className={cn(
                  'flex size-4 shrink-0 items-center justify-center rounded border',
                  isSelected ? 'border-blue-500 bg-blue-500 text-white' : 'border-border',
                )}>
                  {isSelected && <Check className="size-3" />}
                </span>
                {opt}
              </button>
            );
          })}
        </div>
      </div>
    );
  }

  if (kind === 'situacao') {
    const selected = value.situacao ?? [];
    const toggle = (opt: string) => {
      const next = selected.includes(opt)
        ? selected.filter((v) => v !== opt)
        : [...selected, opt];
      onChange({ ...value, situacao: next });
    };
    return (
      <div className="flex flex-col">
        <div className={cn('px-4 py-3 border-b', financeiroToolbarSoftBluePopoverTitleClass)}>
          <span className="text-sm font-semibold text-blue-900 dark:text-blue-100">Situação</span>
        </div>
        <div className="py-1">
          {SITUACAO_OPTIONS.map(({ value: val, label }) => {
            const isSelected = selected.includes(val);
            return (
              <button
                key={val}
                type="button"
                onClick={() => toggle(val)}
                className={cn(
                  'flex w-full items-center gap-2.5 px-4 py-2 text-sm text-left transition-colors',
                  isSelected ? 'bg-blue-50 dark:bg-blue-950/40' : 'hover:bg-muted/60',
                )}
              >
                <span className={cn(
                  'flex size-4 shrink-0 items-center justify-center rounded border',
                  isSelected ? 'border-blue-500 bg-blue-500 text-white' : 'border-border',
                )}>
                  {isSelected && <Check className="size-3" />}
                </span>
                {label}
              </button>
            );
          })}
        </div>
      </div>
    );
  }

  if (kind === 'nascimento') {
    return (
      <div className="p-4 space-y-3">
        <div className="flex flex-col gap-1">
          <span className="text-sm font-semibold text-blue-900 dark:text-blue-100">Data de nascimento</span>
          <span className="text-xs text-muted-foreground">Defina o intervalo de datas.</span>
        </div>
        <div className="grid grid-cols-2 gap-3">
          <label className="flex flex-col gap-1.5 text-xs font-medium text-muted-foreground">
            De
            <Input
              type="date"
              autoFocus
              className={cn(financeiroToolbarSoftBlueInputClass, 'h-9 rounded-md')}
              value={value.nascimento?.from ?? ''}
              onChange={(e) =>
                onChange({
                  ...value,
                  nascimento: { ...value.nascimento, from: e.target.value || undefined },
                })
              }
            />
          </label>
          <label className="flex flex-col gap-1.5 text-xs font-medium text-muted-foreground">
            Até
            <Input
              type="date"
              className={cn(financeiroToolbarSoftBlueInputClass, 'h-9 rounded-md')}
              value={value.nascimento?.to ?? ''}
              onChange={(e) =>
                onChange({
                  ...value,
                  nascimento: { ...value.nascimento, to: e.target.value || undefined },
                })
              }
            />
          </label>
        </div>
      </div>
    );
  }

  if (kind === 'sexo') {
    const selected = value.sexo ?? [];
    const toggle = (opt: 'M' | 'F' | 'Outro') => {
      const next = selected.includes(opt)
        ? selected.filter((v) => v !== opt)
        : [...selected, opt];
      onChange({ ...value, sexo: next });
    };
    return (
      <div className="flex flex-col">
        <div className={cn('px-4 py-3 border-b', financeiroToolbarSoftBluePopoverTitleClass)}>
          <span className="text-sm font-semibold text-blue-900 dark:text-blue-100">Sexo</span>
        </div>
        <div className="py-1">
          {SEXO_OPTIONS.map(({ value: val, label }) => {
            const isSelected = selected.includes(val as 'M' | 'F' | 'Outro');
            return (
              <button
                key={val}
                type="button"
                onClick={() => toggle(val as 'M' | 'F' | 'Outro')}
                className={cn(
                  'flex w-full items-center gap-2.5 px-4 py-2 text-sm text-left transition-colors',
                  isSelected ? 'bg-blue-50 dark:bg-blue-950/40' : 'hover:bg-muted/60',
                )}
              >
                <span className={cn(
                  'flex size-4 shrink-0 items-center justify-center rounded border',
                  isSelected ? 'border-blue-500 bg-blue-500 text-white' : 'border-border',
                )}>
                  {isSelected && <Check className="size-3" />}
                </span>
                {label}
              </button>
            );
          })}
        </div>
      </div>
    );
  }

  return null;
}

// ── ChipsSection ──────────────────────────────────────────────────────────────

const POPOVER_CONTENT_CLASS =
  'z-60 flex max-h-[min(480px,85vh)] w-[min(100vw-2rem,360px)] flex-col overflow-hidden rounded-md border border-blue-100/80 bg-popover text-popover-foreground shadow-lg shadow-black/5 outline-hidden ' +
  'dark:border-blue-900/50 ' +
  'data-[state=open]:animate-in data-[state=closed]:animate-out ' +
  'data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 ' +
  'data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 ' +
  'data-[side=bottom]:slide-in-from-top-2 data-[side=top]:slide-in-from-bottom-2';

export function FieisAdvancedFiltersChipsSection() {
  const { value, onChange, editing, setEditing, removeKind, clearAll, active } =
    useFiltersContext();

  if (active.length === 0) return null;

  return (
    <div className="flex flex-col gap-1.5 min-w-0 w-full">
      <span className="text-xs font-medium text-muted-foreground">Mais filtros selecionados</span>
      <div className="-mx-4 px-4 overflow-x-auto">
        <div className="flex min-w-fit flex-nowrap items-center gap-2">
          {active.map((k) => (
            <PopoverPrimitive.Root
              key={k}
              open={editing === k}
              onOpenChange={(o) => setEditing(o ? k : null)}
            >
              <div
                className={cn(
                  'inline-flex shrink-0 max-w-[280px] items-center gap-1 rounded-md border py-1 ps-1 pe-1 text-xs',
                  financeiroToolbarSoftBlueChipClass,
                )}
              >
                <span className="flex size-6 shrink-0 items-center justify-center text-blue-700/70 dark:text-blue-200/80">
                  {META[k].icon}
                </span>
                <PopoverPrimitive.Trigger asChild>
                  <button
                    type="button"
                    className="min-w-0 flex-1 truncate text-left font-medium text-blue-900 hover:underline dark:text-blue-50 px-1"
                  >
                    {chipText(value, k)}
                  </button>
                </PopoverPrimitive.Trigger>
                <Button
                  type="button"
                  mode="icon"
                  variant="ghost"
                  className="size-6 shrink-0 text-blue-700/70 hover:text-destructive dark:text-blue-200/80"
                  onClick={(e) => { e.stopPropagation(); removeKind(k); }}
                  aria-label={`Remover filtro ${META[k].title}`}
                >
                  <X className="size-3.5" />
                </Button>
              </div>

              <PopoverPrimitive.Portal>
                <PopoverPrimitive.Content
                  className={POPOVER_CONTENT_CLASS}
                  align="start"
                  sideOffset={6}
                  collisionPadding={16}
                >
                  <FilterEditor kind={k} value={value} onChange={onChange} />
                </PopoverPrimitive.Content>
              </PopoverPrimitive.Portal>
            </PopoverPrimitive.Root>
          ))}

          <Button
            type="button"
            variant="outline"
            size="sm"
            className={cn(
              financeiroToolbarSoftBlueClass,
              'h-9 shrink-0 gap-1.5 px-3 text-blue-700 hover:text-destructive dark:text-blue-200 dark:hover:text-destructive',
            )}
            onClick={clearAll}
          >
            <Trash2 className="size-3.5" />
            Limpar filtros
          </Button>
        </div>
      </div>
    </div>
  );
}
