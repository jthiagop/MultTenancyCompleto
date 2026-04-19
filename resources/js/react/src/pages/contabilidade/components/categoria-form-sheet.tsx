import { useEffect, useMemo, useState } from 'react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import {
  ArrowLeftRight,
  BookOpen,
  Check,
  ChevronsUpDown,
  CircleDollarSign,
  Loader2,
  Tag,
  TrendingDown,
  TrendingUp,
  X,
} from 'lucide-react';
import {
  Sheet,
  SheetBody,
  SheetContent,
  SheetFooter,
  SheetHeader,
  SheetTitle,
} from '@/components/ui/sheet';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Form, FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import {
  Command,
  CommandEmpty,
  CommandGroup,
  CommandInput,
  CommandItem,
  CommandList,
} from '@/components/ui/command';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { useAppData } from '@/hooks/useAppData';
import { cn } from '@/lib/utils';
import { notify } from '@/lib/notify';

// ── Tipos ──────────────────────────────────────────────────────────────────────

const TIPO_VALUES = ['entrada', 'saida', 'ambos', 'transferencia', 'somente_contabil'] as const;
type TipoMovimento = (typeof TIPO_VALUES)[number];

export interface PlanoContaOption {
  id: number;
  code: string;
  name: string;
  type: string;
  is_analytical: boolean;
}

// ── Schema ─────────────────────────────────────────────────────────────────────

const categoriaSchema = z.object({
  description:      z.string().min(1, 'O nome é obrigatório.').max(255, 'Nome muito longo.'),
  category:         z.string().max(255).optional().or(z.literal('')),
  type:             z.enum(TIPO_VALUES, { error: 'Selecione o tipo de movimento.' }),
  is_active:        z.boolean(),
  conta_debito_id:  z.number().nullable(),
  conta_credito_id: z.number().nullable(),
});

type CategoriaFormValues = z.infer<typeof categoriaSchema>;

const DEFAULTS: CategoriaFormValues = {
  description:      '',
  category:         '',
  type:             'entrada',
  is_active:        true,
  conta_debito_id:  null,
  conta_credito_id: null,
};

// ── Config visual dos tipos ────────────────────────────────────────────────────

const TIPO_CONFIG: Record<TipoMovimento, {
  label: string;
  hint: string;
  Icon: React.ElementType;
  color: string;
  debitoLabel: string;
  creditoLabel: string;
  debitoHint: string;
  creditoHint: string;
}> = {
  entrada: {
    label: 'Entrada',
    hint: 'Receitas e ingressos',
    Icon: TrendingUp,
    color: 'border-emerald-300 bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:border-emerald-800 dark:text-emerald-300',
    debitoLabel: 'Conta a Debitar',
    creditoLabel: 'Conta a Creditar',
    debitoHint: 'Ex: Caixa, Banco — onde o valor entra',
    creditoHint: 'Ex: Receita, Oferta — origem contábil',
  },
  saida: {
    label: 'Saída',
    hint: 'Despesas e pagamentos',
    Icon: TrendingDown,
    color: 'border-red-300 bg-red-50 text-red-700 dark:bg-red-950 dark:border-red-800 dark:text-red-300',
    debitoLabel: 'Conta a Debitar',
    creditoLabel: 'Conta a Creditar',
    debitoHint: 'Ex: Despesa, Custo — a conta que aumenta',
    creditoHint: 'Ex: Banco, Fornecedor — de onde o valor sai',
  },
  ambos: {
    label: 'Ambos',
    hint: 'Entrada e saída',
    Icon: CircleDollarSign,
    color: 'border-blue-300 bg-blue-50 text-blue-700 dark:bg-blue-950 dark:border-blue-800 dark:text-blue-300',
    debitoLabel: 'Conta Débito',
    creditoLabel: 'Conta Crédito',
    debitoHint: 'Conta debitada no lançamento',
    creditoHint: 'Conta creditada no lançamento',
  },
  transferencia: {
    label: 'Transferência',
    hint: 'Entre contas financeiras',
    Icon: ArrowLeftRight,
    color: 'border-violet-300 bg-violet-50 text-violet-700 dark:bg-violet-950 dark:border-violet-800 dark:text-violet-300',
    debitoLabel: 'Conta Destino',
    creditoLabel: 'Conta Origem',
    debitoHint: 'Conta que recebe o valor (débito)',
    creditoHint: 'Conta que cede o valor (crédito)',
  },
  somente_contabil: {
    label: 'Somente Contábil',
    hint: 'Sem movimento financeiro',
    Icon: BookOpen,
    color: 'border-amber-300 bg-amber-50 text-amber-700 dark:bg-amber-950 dark:border-amber-800 dark:text-amber-300',
    debitoLabel: 'Conta Débito',
    creditoLabel: 'Conta Crédito',
    debitoHint: 'Conta debitada no ajuste contábil',
    creditoHint: 'Conta creditada no ajuste contábil',
  },
};

// ── Subcomponente: Combobox de conta contábil ──────────────────────────────────

function ContaCombobox({
  value,
  onChange,
  placeholder,
  contas,
  hint,
}: {
  value: number | null;
  onChange: (v: number | null) => void;
  placeholder: string;
  contas: PlanoContaOption[];
  hint?: string;
}) {
  const [open, setOpen] = useState(false);
  const [search, setSearch] = useState('');

  const filtered = useMemo(() => {
    const q = search.trim().toLowerCase();
    if (!q) return contas;
    return contas.filter(
      (c) => c.code.toLowerCase().includes(q) || c.name.toLowerCase().includes(q),
    );
  }, [contas, search]);

  const selected = useMemo(() => contas.find((c) => c.id === value) ?? null, [contas, value]);

  return (
    <div className="space-y-1">
      <Popover open={open} onOpenChange={setOpen}>
        <PopoverTrigger asChild>
          <Button
            type="button"
            variant="outline"
            role="combobox"
            className={cn(
              'w-full justify-between h-9 text-sm font-normal',
              !selected && 'text-muted-foreground',
            )}
          >
            <span className="truncate">
              {selected
                ? `${selected.code} — ${selected.name}`
                : placeholder}
            </span>
            <ChevronsUpDown className="ml-2 size-3.5 shrink-0 opacity-50" />
          </Button>
        </PopoverTrigger>
        <PopoverContent className="w-[460px] p-0 z-[70]" align="start">
          <Command shouldFilter={false}>
            <CommandInput
              placeholder="Buscar conta..."
              value={search}
              onValueChange={setSearch}
            />
            <CommandList>
              <CommandEmpty>Nenhuma conta encontrada.</CommandEmpty>
              <CommandGroup>
                {value !== null && (
                  <CommandItem
                    value="__clear__"
                    onSelect={() => { onChange(null); setOpen(false); setSearch(''); }}
                    className="text-muted-foreground italic text-xs"
                  >
                    <X className="size-3 mr-2 shrink-0" />
                    Limpar seleção
                  </CommandItem>
                )}
                {filtered.map((conta) => (
                  <CommandItem
                    key={conta.id}
                    value={String(conta.id)}
                    onSelect={() => { onChange(conta.id); setOpen(false); setSearch(''); }}
                    className="grid grid-cols-[16px_minmax(0,7.5rem)_1fr] gap-2 items-center"
                    title={`${conta.code} — ${conta.name}`}
                  >
                    <Check
                      className={cn(
                        'size-3.5 shrink-0',
                        conta.id === value ? 'opacity-100' : 'opacity-0',
                      )}
                    />
                    <span className="font-mono text-xs text-muted-foreground truncate">
                      {conta.code}
                    </span>
                    <span className="truncate text-sm">{conta.name}</span>
                  </CommandItem>
                ))}
              </CommandGroup>
            </CommandList>
          </Command>
        </PopoverContent>
      </Popover>
      {hint && <p className="text-xs text-muted-foreground">{hint}</p>}
    </div>
  );
}

// ── Props ──────────────────────────────────────────────────────────────────────

export interface CategoriaFormSheetProps {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  editingId?: number | null;
  planoContasList: PlanoContaOption[];
  onSuccess: () => void;
}

// ── Componente principal ───────────────────────────────────────────────────────

export function CategoriaFormSheet({
  open,
  onOpenChange,
  editingId,
  planoContasList,
  onSuccess,
}: CategoriaFormSheetProps) {
  const { csrfToken } = useAppData();
  const isEditing = !!editingId;
  const [loadingData, setLoadingData] = useState(false);

  const form = useForm<CategoriaFormValues>({
    resolver: zodResolver(categoriaSchema),
    defaultValues: DEFAULTS,
  });

  const {
    formState: { isSubmitting },
    watch,
  } = form;

  const tipoAtual = watch('type');
  const contaDebitoId = watch('conta_debito_id');
  const contaCreditoId = watch('conta_credito_id');

  const tipoConfig = TIPO_CONFIG[tipoAtual] ?? TIPO_CONFIG.entrada;

  const contaDebitoSelecionada = useMemo(
    () => planoContasList.find((c) => c.id === contaDebitoId) ?? null,
    [planoContasList, contaDebitoId],
  );

  const contaCreditoSelecionada = useMemo(
    () => planoContasList.find((c) => c.id === contaCreditoId) ?? null,
    [planoContasList, contaCreditoId],
  );

  // ── Carrega dados ao editar ──────────────────────────────────────────────────

  useEffect(() => {
    if (!open) return;

    if (editingId) {
      setLoadingData(true);
      fetch(`/contabilidade/categorias/${editingId}/edit`, {
        headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin',
      })
        .then((r) => r.json())
        .then((json) => {
          const d = json.categoria;
          if (!d?.id) throw new Error('Dados inválidos.');
          form.reset({
            description:      d.description ?? '',
            category:         d.category ?? '',
            type:             TIPO_VALUES.includes(d.type) ? d.type : 'entrada',
            is_active:        d.is_active ?? true,
            conta_debito_id:  d.conta_debito_id ?? null,
            conta_credito_id: d.conta_credito_id ?? null,
          });
        })
        .catch(() => notify.error('Erro', 'Não foi possível carregar os dados da categoria.'))
        .finally(() => setLoadingData(false));
    } else {
      form.reset(DEFAULTS);
    }
  }, [open, editingId]);

  // ── Submissão ────────────────────────────────────────────────────────────────

  const handleSubmit = form.handleSubmit(async (data) => {
    if (!csrfToken) { notify.reload(); return; }

    try {
      const payload = {
        description:      data.description.trim(),
        category:         data.category?.trim() || null,
        type:             data.type,
        is_active:        data.is_active,
        conta_debito_id:  data.conta_debito_id,
        conta_credito_id: data.conta_credito_id,
      };

      const url    = isEditing ? `/contabilidade/categorias/${editingId}` : '/contabilidade/categorias';
      const method = isEditing ? 'PUT' : 'POST';

      const res = await fetch(url, {
        method,
        headers: {
          'Content-Type':       'application/json',
          Accept:               'application/json',
          'X-CSRF-TOKEN':       csrfToken,
          'X-Requested-With':   'XMLHttpRequest',
        },
        credentials: 'same-origin',
        body: JSON.stringify(payload),
      });

      const result = (await res.json()) as {
        success?: boolean;
        message?: string;
        errors?: Record<string, string[]>;
      };

      if (res.ok && result.success !== false) {
        notify.success(
          isEditing ? 'Categoria atualizada!' : 'Categoria criada!',
          result.message ?? `"${data.description.trim()}" foi ${isEditing ? 'atualizada' : 'criada'} com sucesso.`,
        );
        onOpenChange(false);
        onSuccess();
      } else {
        if (result.errors) {
          Object.entries(result.errors).forEach(([field, messages]) => {
            const key = field as keyof CategoriaFormValues;
            if (messages[0]) form.setError(key, { message: messages[0] });
          });
        } else {
          notify.error('Não foi possível salvar', result.message ?? 'Verifique os dados e tente novamente.');
        }
      }
    } catch {
      notify.networkError();
    }
  });

  // ── Render ───────────────────────────────────────────────────────────────────

  return (
    <Sheet open={open} onOpenChange={onOpenChange}>
      <SheetContent
        side="right"
        overlayClassName="z-[55]"
        className={cn(
          'z-60 gap-0 lg:w-[560px] sm:max-w-none inset-5 start-auto h-auto rounded-lg border p-0 flex flex-col',
          '**:data-[slot=sheet-close]:top-4.5 **:data-[slot=sheet-close]:end-5',
        )}
      >
        <SheetHeader className="border-b py-3.5 px-5 border-border space-y-0">
          <SheetTitle className="font-medium flex items-center gap-2">
            <Tag className="size-4 text-muted-foreground" />
            {isEditing ? 'Editar Categoria' : 'Nova Categoria'}
          </SheetTitle>
        </SheetHeader>

        <Form {...form}>
          <form onSubmit={handleSubmit} className="flex flex-1 min-h-0 flex-col">
            <SheetBody className="grow p-0 flex flex-col min-h-0">
              {loadingData ? (
                <div className="flex flex-1 items-center justify-center py-20">
                  <Loader2 className="size-6 animate-spin text-muted-foreground" />
                </div>
              ) : (
                <ScrollArea className="flex-1 px-5 py-5">
                  <div className="space-y-5">

                    {/* ── Seção 1: Identificação ────────────────────────────── */}
                    <Card className="rounded-md">
                      <CardHeader className="min-h-9.5 bg-accent/50 py-2">
                        <CardTitle className="text-2sm flex items-center gap-1.5">
                          <Tag className="size-3.5 text-muted-foreground" />
                          Identificação
                        </CardTitle>
                      </CardHeader>
                      <CardContent className="pt-4 space-y-4">
                        <FormField
                          control={form.control}
                          name="description"
                          render={({ field }) => (
                            <FormItem>
                              <FormLabel className="text-xs">
                                Nome da Categoria <span className="text-destructive">*</span>
                              </FormLabel>
                              <FormControl>
                                <Input
                                  {...field}
                                  placeholder="Ex: Energia Elétrica, Oferta Dominical…"
                                  autoFocus
                                />
                              </FormControl>
                              <FormMessage />
                            </FormItem>
                          )}
                        />

                        <FormField
                          control={form.control}
                          name="category"
                          render={({ field }) => (
                            <FormItem>
                              <FormLabel className="text-xs">
                                Agrupador{' '}
                                <span className="text-muted-foreground font-normal">(opcional)</span>
                              </FormLabel>
                              <FormControl>
                                <Input
                                  {...field}
                                  placeholder="Ex: Administrativo, Utilidades, Coletas…"
                                />
                              </FormControl>
                              <p className="text-xs text-muted-foreground">
                                Usado para agrupar categorias semelhantes nos relatórios.
                              </p>
                              <FormMessage />
                            </FormItem>
                          )}
                        />
                      </CardContent>
                    </Card>

                    {/* ── Seção 2: Tipo de Movimento ────────────────────────── */}
                    <Card className="rounded-md">
                      <CardHeader className="min-h-9.5 bg-accent/50 py-2">
                        <CardTitle className="text-2sm flex items-center gap-1.5">
                          <CircleDollarSign className="size-3.5 text-muted-foreground" />
                          Tipo de Movimento{' '}
                          <span className="text-destructive">*</span>
                        </CardTitle>
                      </CardHeader>
                      <CardContent className="pt-4">
                        <FormField
                          control={form.control}
                          name="type"
                          render={({ field }) => (
                            <FormItem>
                              <div className="grid grid-cols-2 gap-2 sm:grid-cols-3">
                                {TIPO_VALUES.map((t) => {
                                  const cfg = TIPO_CONFIG[t];
                                  const isSelected = field.value === t;
                                  return (
                                    <button
                                      key={t}
                                      type="button"
                                      onClick={() => field.onChange(t)}
                                      className={cn(
                                        'flex flex-col items-start gap-1 rounded-lg border p-3 text-left transition-all cursor-pointer',
                                        isSelected
                                          ? cn(cfg.color, 'ring-2 ring-offset-1 ring-current/30')
                                          : 'border-border bg-background hover:bg-accent/50 text-foreground',
                                      )}
                                    >
                                      <cfg.Icon className={cn('size-4 shrink-0', isSelected ? 'text-current' : 'text-muted-foreground')} />
                                      <span className="text-xs font-semibold leading-tight">
                                        {cfg.label}
                                      </span>
                                      <span className={cn('text-[10px] leading-tight', isSelected ? 'text-current/70' : 'text-muted-foreground')}>
                                        {cfg.hint}
                                      </span>
                                    </button>
                                  );
                                })}
                              </div>
                              <FormMessage className="mt-2" />
                            </FormItem>
                          )}
                        />
                      </CardContent>
                    </Card>

                    {/* ── Seção 3: Vínculo Contábil ─────────────────────────── */}
                    <Card className="rounded-md">
                      <CardHeader className="min-h-9.5 bg-accent/50 py-2">
                        <CardTitle className="text-2sm flex items-center gap-1.5">
                          <BookOpen className="size-3.5 text-muted-foreground" />
                          Vínculo Contábil
                        </CardTitle>
                      </CardHeader>
                      <CardContent className="pt-4 space-y-4">
                        {/* Conta Débito */}
                        <FormField
                          control={form.control}
                          name="conta_debito_id"
                          render={({ field }) => (
                            <FormItem>
                              <FormLabel className="text-xs">{tipoConfig.debitoLabel}</FormLabel>
                              <FormControl>
                                <ContaCombobox
                                  value={field.value}
                                  onChange={field.onChange}
                                  placeholder={`Selecionar ${tipoConfig.debitoLabel.toLowerCase()}…`}
                                  contas={planoContasList}
                                  hint={tipoConfig.debitoHint}
                                />
                              </FormControl>
                              <FormMessage />
                            </FormItem>
                          )}
                        />

                        {/* Conta Crédito */}
                        <FormField
                          control={form.control}
                          name="conta_credito_id"
                          render={({ field }) => (
                            <FormItem>
                              <FormLabel className="text-xs">{tipoConfig.creditoLabel}</FormLabel>
                              <FormControl>
                                <ContaCombobox
                                  value={field.value}
                                  onChange={field.onChange}
                                  placeholder={`Selecionar ${tipoConfig.creditoLabel.toLowerCase()}…`}
                                  contas={planoContasList}
                                  hint={tipoConfig.creditoHint}
                                />
                              </FormControl>
                              <FormMessage />
                            </FormItem>
                          )}
                        />

                        {/* Preview da Regra Contábil */}
                        {(contaDebitoSelecionada || contaCreditoSelecionada) && (
                          <div className="rounded-lg border border-dashed border-border bg-muted/30 p-3 space-y-1.5">
                            <p className="text-[10px] font-semibold uppercase tracking-wider text-muted-foreground mb-2">
                              Partida Contábil
                            </p>
                            <div className="flex items-start gap-2.5">
                              <span className="text-[11px] font-bold text-blue-600 dark:text-blue-400 w-4 shrink-0 mt-0.5">D</span>
                              {contaDebitoSelecionada ? (
                                <div>
                                  <p className="text-xs font-medium text-foreground leading-tight">
                                    {contaDebitoSelecionada.name}
                                  </p>
                                  <p className="text-[10px] font-mono text-muted-foreground">
                                    {contaDebitoSelecionada.code}
                                  </p>
                                </div>
                              ) : (
                                <p className="text-xs text-muted-foreground italic">Não definida</p>
                              )}
                            </div>
                            <div className="flex items-start gap-2.5">
                              <span className="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 w-4 shrink-0 mt-0.5">C</span>
                              {contaCreditoSelecionada ? (
                                <div>
                                  <p className="text-xs font-medium text-foreground leading-tight">
                                    {contaCreditoSelecionada.name}
                                  </p>
                                  <p className="text-[10px] font-mono text-muted-foreground">
                                    {contaCreditoSelecionada.code}
                                  </p>
                                </div>
                              ) : (
                                <p className="text-xs text-muted-foreground italic">Não definida</p>
                              )}
                            </div>
                          </div>
                        )}
                      </CardContent>
                    </Card>

                  </div>
                </ScrollArea>
              )}
            </SheetBody>

            <SheetFooter className="border-t border-border px-5 py-3">
              <div className="flex w-full items-center justify-between gap-2">
                {/* Status */}
                <FormField
                  control={form.control}
                  name="is_active"
                  render={({ field }) => (
                    <Select
                      value={field.value ? 'active' : 'inactive'}
                      onValueChange={(v) => field.onChange(v === 'active')}
                    >
                      <SelectTrigger className="w-36 h-8 text-xs">
                        <SelectValue />
                      </SelectTrigger>
                      <SelectContent className="z-[70]">
                        <SelectItem value="active">
                          <span className="flex items-center gap-1.5">
                            <span className="size-1.5 rounded-full bg-green-500 shrink-0" />
                            Ativa
                          </span>
                        </SelectItem>
                        <SelectItem value="inactive">
                          <span className="flex items-center gap-1.5">
                            <span className="size-1.5 rounded-full bg-muted-foreground shrink-0" />
                            Inativa
                          </span>
                        </SelectItem>
                      </SelectContent>
                    </Select>
                  )}
                />

                <div className="flex items-center gap-2">
                  <Button
                    type="button"
                    variant="outline"
                    onClick={() => onOpenChange(false)}
                    disabled={isSubmitting}
                  >
                    <X className="size-4" />
                    Cancelar
                  </Button>
                  <Button
                    type="submit"
                    className="bg-blue-600 hover:bg-blue-700 text-white border-0"
                    disabled={isSubmitting || loadingData}
                  >
                    {isSubmitting ? (
                      <>
                        <Loader2 className="size-4 animate-spin" />
                        Salvando…
                      </>
                    ) : (
                      <>
                        <Check className="size-4" />
                        {isEditing ? 'Salvar Alterações' : 'Salvar Categoria'}
                      </>
                    )}
                  </Button>
                </div>
              </div>
            </SheetFooter>
          </form>
        </Form>
      </SheetContent>
    </Sheet>
  );
}
