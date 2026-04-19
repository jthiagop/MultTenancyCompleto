import {
    createContext,
    useCallback,
    useContext,
    useEffect,
    useMemo,
    useState,
    type ReactNode,
} from 'react';
import {
    Building2,
    ChevronDown,
    CircleOff,
    Coins,
    Funnel,
    GitBranch,
    GitFork,
    Landmark,
    Repeat2,
    ScanLine,
    Send,
    Tag,
    Trash2,
    UserRound,
    X,
    type LucideIcon,
} from 'lucide-react';
/** `MultiSelect` aqui = painel embutido (sem 2º Popover). Evita ReferenceError em bundles que ainda usam o nome curto. */
import { MultiSelectEmbedded as MultiSelect, type MultiSelectOption } from '@/components/multi-select';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import {
    financeiroToolbarSoftBlueChipClass,
    financeiroToolbarSoftBlueClass,
    financeiroToolbarSoftBlueFilterFormClass,
    financeiroToolbarSoftBlueInputClass,
    financeiroToolbarSoftBluePopoverTitleClass,
} from '@/lib/financeiro-toolbar-accent';
import { cn } from '@/lib/utils';
import type { TransacaoAdvancedFiltersState } from '@/hooks/useTransacoes';

const BLANK = '__blank__';

/** Rótulos e ícones alinhados às origens exibidas na tabela (OrigemCell). */
const ORIGEM_FILTER_META: Record<string, { label: string; icon: LucideIcon }> = {
    banco: { label: 'Banco', icon: Landmark },
    caixa: { label: 'Caixa', icon: Coins },
    repasse: { label: 'Repasse', icon: Send },
    rateio: { label: 'Rateio', icon: GitFork },
    conciliacao_bancaria: { label: 'Conciliação bancária', icon: ScanLine },
    transferencia: { label: 'Transferência', icon: GitBranch },
};

function origemFallbackLabel(raw: string): string {
    return raw
        .split('_')
        .filter(Boolean)
        .map((w) => w.charAt(0).toLocaleUpperCase('pt-BR') + w.slice(1).toLowerCase())
        .join(' ');
}

export type TransacaoAdvancedTipoFormData = 'receita' | 'despesa' | 'all';

type FilterKind =
    | 'categoria'
    | 'centro_custo'
    | 'parceiro'
    | 'origem'
    | 'recorrencia'
    | 'valor'
    | 'data_registro';

const ALL_KINDS: FilterKind[] = [
    'categoria',
    'centro_custo',
    'parceiro',
    'origem',
    'recorrencia',
    'valor',
    'data_registro',
];

const META: Record<FilterKind, { title: string; menu: string }> = {
    categoria: { title: 'Categoria', menu: 'Categoria' },
    centro_custo: { title: 'Centro de custo', menu: 'Centro de custo' },
    parceiro: { title: 'Cliente/Fornecedor', menu: 'Cliente/Fornecedor' },
    origem: { title: 'Origem do lançamento', menu: 'Origem do lançamento' },
    recorrencia: { title: 'Recorrência', menu: 'Recorrência' },
    valor: { title: 'Valor', menu: 'Valor (mín. / máx.)' },
    data_registro: { title: 'Data do Registro', menu: 'Data do Registro' },
};

interface FormDataJson {
    categorias?: { id: number; description: string }[];
    centrosCusto?: { id: number; code?: string; name: string }[];
    parceiros?: { id: number; nome: string }[];
}

export function useTransacaoAdvancedFilterOptions(tipoFormData: TransacaoAdvancedTipoFormData) {
    const [formData, setFormData] = useState<FormDataJson | null>(null);
    const [origens, setOrigens] = useState<string[]>([]);

    const tipoQuery =
        tipoFormData === 'receita' ? 'receita' : tipoFormData === 'despesa' ? 'despesa' : 'all';

    useEffect(() => {
        let cancel = false;
        const url = `/financeiro/api/form-data?tipo=${tipoQuery}`;
        fetch(url, {
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
        })
            .then(async (r) => {
                const raw = await r.text();
                if (!r.ok) {
                    let payload: { message?: string; debug?: unknown } | null = null;
                    try {
                        payload = JSON.parse(raw) as { message?: string; debug?: unknown };
                    } catch {
                        /* corpo não é JSON (ex.: página HTML de erro) */
                    }
                    console.error('[financeiro/api/form-data]', {
                        status: r.status,
                        url,
                        tipo: tipoQuery,
                        message: payload?.message,
                        debug: payload?.debug,
                        bodyPreview: raw.length > 2000 ? `${raw.slice(0, 2000)}…` : raw,
                    });
                    if (payload?.debug != null) {
                        console.error('[financeiro/api/form-data] debug (servidor, APP_DEBUG)', payload.debug);
                    }
                    throw new Error(`form-data HTTP ${r.status}`);
                }
                let d: FormDataJson;
                try {
                    d = JSON.parse(raw) as FormDataJson;
                } catch {
                    console.error('[financeiro/api/form-data] resposta OK mas não é JSON', raw.slice(0, 800));
                    throw new Error('form-data: JSON inválido');
                }
                if (!cancel) setFormData(d);
            })
            .catch(() => {
                if (!cancel) setFormData(null);
            });
        return () => {
            cancel = true;
        };
    }, [tipoQuery]);

    useEffect(() => {
        let cancel = false;
        fetch('/app/financeiro/banco/transacoes-opcoes-origem', {
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
        })
            .then((r) => r.json())
            .then((d: { origens?: string[] }) => {
                if (!cancel) setOrigens(Array.isArray(d.origens) ? d.origens : []);
            })
            .catch(() => {
                if (!cancel) setOrigens([]);
            });
        return () => {
            cancel = true;
        };
    }, []);

    return { formData, origens };
}

function isActive(f: TransacaoAdvancedFiltersState, k: FilterKind): boolean {
    switch (k) {
        case 'categoria':
            return f.categoria !== undefined;
        case 'centro_custo':
            return f.centro_custo !== undefined;
        case 'parceiro':
            return f.parceiro !== undefined;
        case 'origem':
            return f.origem !== undefined;
        case 'recorrencia':
            return f.recorrencia !== undefined;
        case 'valor':
            return f.valor !== undefined;
        case 'data_registro':
            return f.data_registro !== undefined;
        default:
            return false;
    }
}

function activeKindsList(f: TransacaoAdvancedFiltersState): FilterKind[] {
    return ALL_KINDS.filter((k) => isActive(f, k));
}

function summarizeList(
    values: string[],
    options: MultiSelectOption[],
    blankLabel: string,
): string {
    if (values.length === 0) return '…';
    const labels = values.map((v) => {
        if (v === BLANK) return blankLabel;
        return options.find((o) => o.value === v)?.label ?? v;
    });
    if (labels.length > 3) return `${labels.length} itens selecionados`;
    return labels.join(', ');
}

type FiltersCtx = {
    value: TransacaoAdvancedFiltersState;
    onChange: (next: TransacaoAdvancedFiltersState) => void;
    editing: FilterKind | null;
    setEditing: (k: FilterKind | null) => void;
    categoriaOptions: MultiSelectOption[];
    centroOptions: MultiSelectOption[];
    parceiroOptions: MultiSelectOption[];
    origemOptions: MultiSelectOption[];
    recorrenciaOptions: MultiSelectOption[];
    addKind: (k: FilterKind) => void;
    removeKind: (k: FilterKind) => void;
    clearAll: () => void;
    active: FilterKind[];
    available: FilterKind[];
    chipText: (k: FilterKind) => string;
};

const FiltersContext = createContext<FiltersCtx | null>(null);

function useFiltersContext(): FiltersCtx {
    const ctx = useContext(FiltersContext);
    if (!ctx) {
        throw new Error(
            'TransacaoAdvancedFiltersTrigger/ChipsSection must be used inside TransacaoAdvancedFiltersScope',
        );
    }
    return ctx;
}

/**
 * Envolve o cabeçalho da tabela (ou trecho) para compartilhar estado entre o botão "Mais filtros" e a linha de chips.
 */
export function TransacaoAdvancedFiltersScope({
    value,
    onChange,
    tipoFormData,
    children,
}: {
    value: TransacaoAdvancedFiltersState;
    onChange: (next: TransacaoAdvancedFiltersState) => void;
    tipoFormData: TransacaoAdvancedTipoFormData;
    children: ReactNode;
}) {
    const { formData, origens } = useTransacaoAdvancedFilterOptions(tipoFormData);
    const [editing, setEditing] = useState<FilterKind | null>(null);

    const categoriaOptions: MultiSelectOption[] = useMemo(() => {
        const blank: MultiSelectOption = { value: BLANK, label: '(Em branco)', icon: CircleOff };
        const rest =
            formData?.categorias?.map((c) => ({
                value: String(c.id),
                label: c.description,
                icon: Tag,
            })) ?? [];
        return [blank, ...rest];
    }, [formData?.categorias]);

    const centroOptions: MultiSelectOption[] = useMemo(() => {
        const blank: MultiSelectOption = { value: BLANK, label: '(Em branco)', icon: CircleOff };
        const rest =
            formData?.centrosCusto?.map((c) => ({
                value: String(c.id),
                label: [c.code, c.name].filter(Boolean).join(' — ') || c.name,
                icon: Building2,
            })) ?? [];
        return [blank, ...rest];
    }, [formData?.centrosCusto]);

    const parceiroOptions: MultiSelectOption[] = useMemo(() => {
        const blank: MultiSelectOption = { value: BLANK, label: '(Em branco)', icon: CircleOff };
        const rest =
            formData?.parceiros?.map((p) => ({
                value: String(p.id),
                label: p.nome,
                icon: UserRound,
            })) ?? [];
        return [blank, ...rest];
    }, [formData?.parceiros]);

    const origemOptions: MultiSelectOption[] = useMemo(
        () =>
            origens.map((o) => {
                const meta = ORIGEM_FILTER_META[o];
                return {
                    value: o,
                    label: meta?.label ?? origemFallbackLabel(o),
                    icon: meta?.icon ?? Tag,
                };
            }),
        [origens],
    );

    const recorrenciaOptions: MultiSelectOption[] = useMemo(
        () => [
            { value: 'com', label: 'Com recorrência', icon: Repeat2 },
            { value: 'sem', label: 'Sem recorrência', icon: CircleOff },
        ],
        [],
    );

    const addKind = useCallback(
        (k: FilterKind) => {
            switch (k) {
                case 'valor':
                    onChange({ ...value, valor: {} });
                    break;
                case 'data_registro':
                    onChange({ ...value, data_registro: {} });
                    break;
                case 'categoria':
                    onChange({ ...value, categoria: [] });
                    break;
                case 'centro_custo':
                    onChange({ ...value, centro_custo: [] });
                    break;
                case 'parceiro':
                    onChange({ ...value, parceiro: [] });
                    break;
                case 'origem':
                    onChange({ ...value, origem: [] });
                    break;
                case 'recorrencia':
                    onChange({ ...value, recorrencia: [] });
                    break;
                default:
                    break;
            }
            setEditing(k);
        },
        [onChange, value],
    );

    const removeKind = useCallback(
        (k: FilterKind) => {
            const next = { ...value };
            switch (k) {
                case 'categoria':
                    delete next.categoria;
                    break;
                case 'centro_custo':
                    delete next.centro_custo;
                    break;
                case 'parceiro':
                    delete next.parceiro;
                    break;
                case 'origem':
                    delete next.origem;
                    break;
                case 'recorrencia':
                    delete next.recorrencia;
                    break;
                case 'valor':
                    delete next.valor;
                    break;
                case 'data_registro':
                    delete next.data_registro;
                    break;
                default:
                    break;
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

    const chipText = useCallback(
        (k: FilterKind): string => {
            const title = META[k].title;
            switch (k) {
                case 'categoria':
                    return `${title}: ${summarizeList(value.categoria ?? [], categoriaOptions, '(Em branco)')}`;
                case 'centro_custo':
                    return `${title}: ${summarizeList(value.centro_custo ?? [], centroOptions, '(Em branco)')}`;
                case 'parceiro':
                    return `${title}: ${summarizeList(value.parceiro ?? [], parceiroOptions, '(Em branco)')}`;
                case 'origem':
                    return `${title}: ${summarizeList(value.origem ?? [], origemOptions, '(Em branco)')}`;
                case 'recorrencia': {
                    const v = value.recorrencia ?? [];
                    const labels = v.map((x) => (x === 'com' ? 'Com recorrência' : 'Sem recorrência'));
                    return `${title}: ${labels.join(', ') || '…'}`;
                }
                case 'valor': {
                    const { min, max } = value.valor ?? {};
                    const parts: string[] = [];
                    if (min != null && Number.isFinite(min)) parts.push(`≥ ${min}`);
                    if (max != null && Number.isFinite(max)) parts.push(`≤ ${max}`);
                    return `${title}: ${parts.join(' e ') || '…'}`;
                }
                case 'data_registro': {
                    const { from, to } = value.data_registro ?? {};
                    if (!from && !to) return `${title}: …`;
                    return `${title}: ${from ?? '…'} → ${to ?? '…'}`;
                }
                default:
                    return title;
            }
        },
        [value, categoriaOptions, centroOptions, parceiroOptions, origemOptions],
    );

    const ctxValue = useMemo(
        () => ({
            value,
            onChange,
            editing,
            setEditing,
            categoriaOptions,
            centroOptions,
            parceiroOptions,
            origemOptions,
            recorrenciaOptions,
            addKind,
            removeKind,
            clearAll,
            active,
            available,
            chipText,
        }),
        [
            value,
            onChange,
            editing,
            categoriaOptions,
            centroOptions,
            parceiroOptions,
            origemOptions,
            recorrenciaOptions,
            addKind,
            removeKind,
            clearAll,
            active,
            available,
            chipText,
        ],
    );

    return <FiltersContext.Provider value={ctxValue}>{children}</FiltersContext.Provider>;
}

/** Botão "Mais filtros" (use dentro do Scope). */
export function TransacaoAdvancedFiltersTrigger() {
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
                    <div className="px-2 py-1.5 text-xs text-muted-foreground">Todos os filtros ativos</div>
                ) : (
                    available.map((k) => (
                        <DropdownMenuItem key={k} onSelect={() => addKind(k)}>
                            {META[k].menu}
                        </DropdownMenuItem>
                    ))
                )}
            </DropdownMenuContent>
        </DropdownMenu>
    );
}

/** Chips e “Limpar filtros” (use dentro do Scope). */
export function TransacaoAdvancedFiltersChipsSection() {
    const {
        value,
        onChange,
        editing,
        setEditing,
        categoriaOptions,
        centroOptions,
        parceiroOptions,
        origemOptions,
        recorrenciaOptions,
        removeKind,
        clearAll,
        active,
        chipText,
    } = useFiltersContext();

    if (active.length === 0) return null;

    return (
        <div className="flex flex-col gap-1.5 min-w-0 w-full">
            <span className="text-xs font-medium text-muted-foreground">Mais filtros selecionados</span>
            <div className="flex flex-wrap items-center gap-2">
                {active.map((k) => (
                    <Popover key={k} open={editing === k} onOpenChange={(o) => setEditing(o ? k : null)}>
                        <div
                            className={cn(
                                'inline-flex max-w-full items-center gap-1 rounded-md border py-1 ps-2.5 pe-1 text-xs',
                                financeiroToolbarSoftBlueChipClass,
                            )}
                        >
                            <PopoverTrigger asChild>
                                <button
                                    type="button"
                                    className="min-w-0 flex-1 truncate text-left font-medium text-blue-900 hover:underline dark:text-blue-50"
                                >
                                    {chipText(k)}
                                </button>
                            </PopoverTrigger>
                            <Button
                                type="button"
                                mode="icon"
                                variant="ghost"
                                className="size-6 shrink-0 text-blue-700/70 hover:text-destructive dark:text-blue-200/80"
                                onClick={(e) => {
                                    e.stopPropagation();
                                    removeKind(k);
                                }}
                                aria-label={`Remover filtro ${META[k].title}`}
                            >
                                <X className="size-3.5" />
                            </Button>
                        </div>
                        <PopoverContent
                            className="flex max-h-[min(480px,85vh)] w-[min(100vw-2rem,380px)] max-w-[min(100vw-2rem,380px)] flex-col overflow-hidden border-blue-100/80 p-0 dark:border-blue-900/50"
                            align="start"
                        >
                            {k === 'categoria' && (
                                <>
                                    <div className={cn('px-3 py-2', financeiroToolbarSoftBluePopoverTitleClass)}>
                                        <p className="text-sm font-medium text-blue-900 dark:text-blue-100">{META.categoria.title}</p>
                                    </div>
                                    <MultiSelect
                                        options={categoriaOptions}
                                        value={value.categoria ?? []}
                                        onValueChange={(v) => onChange({ ...value, categoria: v })}
                                        searchPlaceholder="Buscar categoria…"
                                    />
                                </>
                            )}
                            {k === 'centro_custo' && (
                                <>
                                    <div className={cn('px-3 py-2', financeiroToolbarSoftBluePopoverTitleClass)}>
                                        <p className="text-sm font-medium text-blue-900 dark:text-blue-100">{META.centro_custo.title}</p>
                                    </div>
                                    <MultiSelect
                                        options={centroOptions}
                                        value={value.centro_custo ?? []}
                                        onValueChange={(v) => onChange({ ...value, centro_custo: v })}
                                        searchPlaceholder="Buscar centro de custo…"
                                    />
                                </>
                            )}
                            {k === 'parceiro' && (
                                <>
                                    <div className={cn('px-3 py-2', financeiroToolbarSoftBluePopoverTitleClass)}>
                                        <p className="text-sm font-medium text-blue-900 dark:text-blue-100">{META.parceiro.title}</p>
                                    </div>
                                    <MultiSelect
                                        options={parceiroOptions}
                                        value={value.parceiro ?? []}
                                        onValueChange={(v) => onChange({ ...value, parceiro: v })}
                                        searchPlaceholder="Buscar parceiro…"
                                    />
                                </>
                            )}
                            {k === 'origem' && (
                                <>
                                    <div className={cn('px-3 py-2', financeiroToolbarSoftBluePopoverTitleClass)}>
                                        <p className="text-sm font-medium text-blue-900 dark:text-blue-100">{META.origem.title}</p>
                                    </div>
                                    <MultiSelect
                                        options={origemOptions}
                                        value={value.origem ?? []}
                                        onValueChange={(v) => onChange({ ...value, origem: v })}
                                        searchPlaceholder="Buscar origem…"
                                    />
                                </>
                            )}
                            {k === 'recorrencia' && (
                                <>
                                    <div className={cn('px-3 py-2', financeiroToolbarSoftBluePopoverTitleClass)}>
                                        <p className="text-sm font-medium text-blue-900 dark:text-blue-100">{META.recorrencia.title}</p>
                                    </div>
                                    <MultiSelect
                                        options={recorrenciaOptions}
                                        value={(value.recorrencia ?? []) as string[]}
                                        onValueChange={(v) =>
                                            onChange({
                                                ...value,
                                                recorrencia: v as ('com' | 'sem')[],
                                            })
                                        }
                                        searchPlaceholder="Buscar…"
                                    />
                                </>
                            )}
                            {k === 'valor' && (
                                <div className={cn('space-y-2 p-3', financeiroToolbarSoftBluePopoverTitleClass, 'border-0 bg-blue-50/40 dark:bg-blue-950/20')}>
                                    <div className="text-sm font-medium text-blue-900 dark:text-blue-100">Valor (R$)</div>
                                    <div className="flex flex-col gap-2 sm:flex-row sm:items-center">
                                        <label className="flex flex-col gap-1 text-xs text-blue-800/80 dark:text-blue-200/90">
                                            Mín.
                                            <Input
                                                type="number"
                                                step="0.01"
                                                min={0}
                                                className={cn(financeiroToolbarSoftBlueInputClass, 'h-9 rounded-md')}
                                                value={
                                                    value.valor?.min != null && Number.isFinite(value.valor.min)
                                                        ? String(value.valor.min)
                                                        : ''
                                                }
                                                onChange={(e) => {
                                                    const raw = e.target.value;
                                                    const n = raw === '' ? undefined : Number.parseFloat(raw);
                                                    onChange({
                                                        ...value,
                                                        valor: {
                                                            ...value.valor,
                                                            min: n !== undefined && Number.isFinite(n) ? n : undefined,
                                                        },
                                                    });
                                                }}
                                            />
                                        </label>
                                        <label className="flex flex-col gap-1 text-xs text-blue-800/80 dark:text-blue-200/90">
                                            Máx.
                                            <Input
                                                type="number"
                                                step="0.01"
                                                min={0}
                                                className={cn(financeiroToolbarSoftBlueInputClass, 'h-9 rounded-md')}
                                                value={
                                                    value.valor?.max != null && Number.isFinite(value.valor.max)
                                                        ? String(value.valor.max)
                                                        : ''
                                                }
                                                onChange={(e) => {
                                                    const raw = e.target.value;
                                                    const n = raw === '' ? undefined : Number.parseFloat(raw);
                                                    onChange({
                                                        ...value,
                                                        valor: {
                                                            ...value.valor,
                                                            max: n !== undefined && Number.isFinite(n) ? n : undefined,
                                                        },
                                                    });
                                                }}
                                            />
                                        </label>
                                    </div>
                                </div>
                            )}
                            {k === 'data_registro' && (
                                <div className={cn('space-y-2 p-3', financeiroToolbarSoftBlueFilterFormClass)}>
                                    <div className="text-sm font-medium text-blue-900 dark:text-blue-100">{META.data_registro.title}</div>
                                    <div className="flex flex-col gap-2 sm:flex-row sm:items-center">
                                        <label className="flex flex-col gap-1 text-xs text-blue-800/80 dark:text-blue-200/90">
                                            De
                                            <Input
                                                type="date"
                                                className={cn(financeiroToolbarSoftBlueInputClass, 'h-9 rounded-md')}
                                                value={value.data_registro?.from ?? ''}
                                                onChange={(e) =>
                                                    onChange({
                                                        ...value,
                                                        data_registro: {
                                                            ...value.data_registro,
                                                            from: e.target.value || undefined,
                                                        },
                                                    })
                                                }
                                            />
                                        </label>
                                        <label className="flex flex-col gap-1 text-xs text-blue-800/80 dark:text-blue-200/90">
                                            Até
                                            <Input
                                                type="date"
                                                className={cn(financeiroToolbarSoftBlueInputClass, 'h-9 rounded-md')}
                                                value={value.data_registro?.to ?? ''}
                                                onChange={(e) =>
                                                    onChange({
                                                        ...value,
                                                        data_registro: {
                                                            ...value.data_registro,
                                                            to: e.target.value || undefined,
                                                        },
                                                    })
                                                }
                                            />
                                        </label>
                                    </div>
                                </div>
                            )}
                        </PopoverContent>
                    </Popover>
                ))}
                <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    className={cn(
                        financeiroToolbarSoftBlueClass,
                        'h-10 min-w-44 gap-2 px-4 text-blue-700 hover:text-destructive dark:text-blue-200 dark:hover:text-destructive',
                    )}
                    onClick={clearAll}
                >
                    <Trash2 className="size-3.5" />
                    Limpar filtros
                </Button>
            </div>
        </div>
    );
}

/** Composição completa (dropdown + chips) para uso fora do cabeçalho fragmentado. */
export function TransacaoAdvancedFiltersBar({
    value,
    onChange,
    tipoFormData,
}: {
    value: TransacaoAdvancedFiltersState;
    onChange: (next: TransacaoAdvancedFiltersState) => void;
    tipoFormData: TransacaoAdvancedTipoFormData;
}) {
    return (
        <TransacaoAdvancedFiltersScope value={value} onChange={onChange} tipoFormData={tipoFormData}>
            <div className="flex flex-col gap-2 w-full min-w-0">
                <div className="flex flex-wrap items-center gap-2">
                    <TransacaoAdvancedFiltersTrigger />
                </div>
                <TransacaoAdvancedFiltersChipsSection />
            </div>
        </TransacaoAdvancedFiltersScope>
    );
}
