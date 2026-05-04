import { type RefObject, useCallback, useEffect, useMemo, useRef, useState } from 'react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import { ChevronUp, Eraser, Loader2, Plus, Save, UserPlus } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { ButtonGroup } from '@/components/ui/button-group';
import { DatePicker } from '@/components/ui/date-picker';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Form, FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { ScrollArea } from '@/components/ui/scroll-area';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import {
  Sheet,
  SheetBody,
  SheetContent,
  SheetFooter,
  SheetHeader,
  SheetTitle,
} from '@/components/ui/sheet';
import { Switch } from '@/components/ui/switch';
import { Textarea } from '@/components/ui/textarea';
import { CurrencyInput } from '@/components/common/masked-input';
import { SearchSelect, type SearchSelectOption } from '@/components/common/search-select';
import { useAppData } from '@/hooks/useAppData';
import { useFormSelectData } from '@/hooks/useFormSelectData';
import { notify } from '@/lib/notify';
import { CadastroFielSheet } from '@/pages/fieis/components/cadastro-fiel-sheet';
import { useFielSearch, type FielSearchResult } from '@/pages/dizimos/components/use-fiel-search';
import { useLookupCarne } from '@/pages/dizimos/components/use-lookup-carne';

// ── Constantes ─────────────────────────────────────────────────────────────

const TIPOS = ['Dízimo', 'Doação', 'Oferta', 'Outro'] as const;
type Tipo = (typeof TIPOS)[number];

const FORMAS_PAGAMENTO = [
  'Dinheiro',
  'PIX',
  'Cartão de Débito',
  'Cartão de Crédito',
  'Transferência',
  'Cheque',
  'Outro',
] as const;
type FormaPagamento = (typeof FORMAS_PAGAMENTO)[number];

/** Forma de pagamento → tipos compatíveis de entidade financeira. */
const FORMA_TO_ENTIDADE_TIPOS: Record<FormaPagamento, Array<'caixa' | 'banco'> | null> = {
  Dinheiro: ['caixa'],
  PIX: ['banco'],
  'Cartão de Débito': ['banco'],
  'Cartão de Crédito': ['banco'],
  Transferência: ['banco'],
  Cheque: ['banco', 'caixa'],
  Outro: null,
};

// ── Schema ─────────────────────────────────────────────────────────────────

const mesAnoRegex = /^(\d{2})\/(\d{4})$/;

const schema = z
  .object({
    tipo: z.enum(['Dízimo', 'Doação', 'Oferta', 'Outro']),
    fiel_id: z.number().int().positive('Selecione um fiel.'),
    comunidade: z.string().nullable().optional(),

    periodo: z.boolean(),
    mes_inicio: z.string().optional().or(z.literal('')),
    mes_fim: z.string().optional().or(z.literal('')),

    data_pagamento: z
      .string()
      .min(1, 'Informe a data.')
      .regex(/^\d{4}-\d{2}-\d{2}$/, 'Data inválida.'),
    valor: z.number().positive('Informe um valor maior que zero.'),

    oferta_adicional: z.boolean(),
    oferta_adicional_valor: z.number().nullable().optional(),
    oferta_adicional_ref: z.string().max(255).nullable().optional(),

    forma_pagamento: z.enum([
      'Dinheiro',
      'PIX',
      'Cartão de Débito',
      'Cartão de Crédito',
      'Transferência',
      'Cheque',
      'Outro',
    ]),
    entidade_financeira_id: z.number().int().positive().nullable(),
    integrar_financeiro: z.boolean(),

    observacoes: z.string().max(1000).nullable().optional(),
  })
  .superRefine((d, ctx) => {
    if (d.periodo) {
      if (!d.mes_inicio || !mesAnoRegex.test(d.mes_inicio)) {
        ctx.addIssue({
          code: z.ZodIssueCode.custom,
          path: ['mes_inicio'],
          message: 'Formato MM/AAAA.',
        });
      }
      if (!d.mes_fim || !mesAnoRegex.test(d.mes_fim)) {
        ctx.addIssue({
          code: z.ZodIssueCode.custom,
          path: ['mes_fim'],
          message: 'Formato MM/AAAA.',
        });
      }
      if (d.mes_inicio && d.mes_fim && mesAnoRegex.test(d.mes_inicio) && mesAnoRegex.test(d.mes_fim)) {
        const ini = parseMesAno(d.mes_inicio);
        const fim = parseMesAno(d.mes_fim);
        if (ini && fim && ini > fim) {
          ctx.addIssue({
            code: z.ZodIssueCode.custom,
            path: ['mes_fim'],
            message: 'Mês final deve ser ≥ inicial.',
          });
        }
      }
    }

    if (d.oferta_adicional) {
      if (!d.oferta_adicional_valor || d.oferta_adicional_valor <= 0) {
        ctx.addIssue({
          code: z.ZodIssueCode.custom,
          path: ['oferta_adicional_valor'],
          message: 'Informe o valor da oferta adicional.',
        });
      }
    }

    if (d.integrar_financeiro && !d.entidade_financeira_id) {
      ctx.addIssue({
        code: z.ZodIssueCode.custom,
        path: ['entidade_financeira_id'],
        message: 'Selecione a Conta/Caixa para integrar ao financeiro.',
      });
    }
  });

type DizimoFormValues = z.infer<typeof schema>;

function parseMesAno(s: string): number | null {
  const m = mesAnoRegex.exec(s);
  if (!m) return null;
  const mes = Number(m[1]);
  const ano = Number(m[2]);
  if (mes < 1 || mes > 12) return null;
  return ano * 100 + mes;
}

function todayISO(): string {
  const d = new Date();
  const y = d.getFullYear();
  const m = String(d.getMonth() + 1).padStart(2, '0');
  const day = String(d.getDate()).padStart(2, '0');
  return `${y}-${m}-${day}`;
}

const FORM_DEFAULTS: DizimoFormValues = {
  tipo: 'Dízimo',
  fiel_id: 0,
  comunidade: null,
  periodo: false,
  mes_inicio: '',
  mes_fim: '',
  data_pagamento: todayISO(),
  valor: 0,
  oferta_adicional: false,
  oferta_adicional_valor: null,
  oferta_adicional_ref: null,
  forma_pagamento: 'Dinheiro',
  entidade_financeira_id: null,
  integrar_financeiro: true,
  observacoes: null,
};

// ── Props ──────────────────────────────────────────────────────────────────

export interface LancamentoDizimoDrawerProps {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  /** Quando definido, abre em modo edição (carrega dados via apiShow). */
  editingId?: number | null;
  onSaved?: () => void;
}

// ── Componente principal ───────────────────────────────────────────────────

export function LancamentoDizimoDrawer({
  open,
  onOpenChange,
  editingId,
  onSaved,
}: LancamentoDizimoDrawerProps) {
  const isEditing = !!editingId;
  const { csrfToken } = useAppData();
  const { data: formSelectData } = useFormSelectData('receita');

  const form = useForm<DizimoFormValues>({
    resolver: zodResolver(schema),
    defaultValues: FORM_DEFAULTS,
    mode: 'onSubmit',
  });

  const watchTipo = form.watch('tipo');
  const watchPeriodo = form.watch('periodo');
  const watchOferta = form.watch('oferta_adicional');
  const watchIntegrar = form.watch('integrar_financeiro');
  const watchForma = form.watch('forma_pagamento');
  const watchFielId = form.watch('fiel_id');

  const [submitting, setSubmitting] = useState(false);
  const [saveMode, setSaveMode] = useState<'close' | 'new' | 'clear'>('close');
  const formRef = useRef<HTMLFormElement>(null);
  const [cadastroFielOpen, setCadastroFielOpen] = useState(false);

  // ── Identificação do fiel — busca unificada ──────────────────────────────
  // Campo único que aceita: código D-XXXX (lookup por carnê) ou nome/CPF
  // (busca por texto). A detecção é automática: se o valor começa com uma
  // letra ou com dígito único (padrão D-XXXX), tentamos lookup; caso
  // contrário fazemos busca de texto.

  const [fielQuery, setFielQuery] = useState('');
  const [fielSelecionado, setFielSelecionado] = useState<{
    id: number;
    nome: string;
    avatar_url: string | null;
    comunidade: string | null;
  } | null>(null);

  // Detecta se o input parece um código de carnê (D-XXXX ou só dígitos <= 6 chars)
  const isCodigoPattern = (v: string) => /^[A-Za-z][-\s]?\d/i.test(v.trim()) || /^\d{1,6}$/.test(v.trim());

  // Busca por nome/CPF apenas quando não parece código
  const searchTerm = isCodigoPattern(fielQuery) ? '' : fielQuery;
  const { options: fielMatches, loading: searchingFieis } = useFielSearch(searchTerm);
  const { lookup: lookupCarne, loading: lookingUp, error: lookupError } = useLookupCarne();

  const fielOptions = useMemo<SearchSelectOption[]>(() => {
    const merged = new Map<string, FielSearchResult>();
    fielMatches.forEach((f) => merged.set(String(f.id), f));
    if (fielSelecionado && !merged.has(String(fielSelecionado.id))) {
      merged.set(String(fielSelecionado.id), {
        id: fielSelecionado.id,
        nome_completo: fielSelecionado.nome,
        avatar_url: fielSelecionado.avatar_url,
        codigo_dizimista: null,
        cidade_uf: fielSelecionado.comunidade,
      });
    }
    return Array.from(merged.values()).map((f) => ({
      value: String(f.id),
      label: f.nome_completo,
      icon: f.avatar_url,
      hint: [f.codigo_dizimista, f.cidade_uf].filter(Boolean).join(' · ') || undefined,
    }));
  }, [fielMatches, fielSelecionado]);

  /** Dispara lookup de carnê ou notifica que o termo é muito curto. */
  async function handleFielSearch() {
    const q = fielQuery.trim();
    if (!q) return;

    if (isCodigoPattern(q)) {
      // Normaliza: se digitou só números, prefixar com D-
      const codigo = /^\d+$/.test(q) ? `D-${q.padStart(4, '0')}` : q.toUpperCase();
      const result = await lookupCarne(codigo);
      if (!result) {
        notify.error('Carnê não encontrado', `Nenhum fiel com código "${codigo}".`);
        return;
      }
      selecionarFiel(result.fiel.id, result.fiel.nome_completo, result.fiel.avatar_url, result.comunidade ?? null);
      notify.success('Fiel encontrado', result.fiel.nome_completo);
    }
    // Se não é código, o SearchSelect abaixo já mostra os resultados em tempo real
  }

  function selecionarFiel(id: number, nome: string, avatar_url: string | null, comunidade: string | null) {
    form.setValue('fiel_id', id, { shouldValidate: true });
    form.setValue('comunidade', comunidade);
    setFielSelecionado({ id, nome, avatar_url, comunidade });
  }

  function handleSelectFielFromSearch(value: string) {
    const fiel = fielMatches.find((f) => String(f.id) === value);
    if (!fiel) return;
    selecionarFiel(fiel.id, fiel.nome_completo, fiel.avatar_url, fiel.cidade_uf ?? null);
  }

  // ── Entidades financeiras filtradas por forma de pagamento ───────────────

  const entidadeOptions = useMemo<SearchSelectOption[]>(() => {
    const tiposCompat = FORMA_TO_ENTIDADE_TIPOS[watchForma];
    const base = formSelectData.entidades ?? [];
    const filtered = tiposCompat
      ? base.filter((e) => tiposCompat.includes(e.tipo as 'caixa' | 'banco'))
      : base;
    return filtered.map((e) => ({
      value: String(e.id),
      label: e.label,
      icon: e.logo,
      hint: e.tipo === 'banco' ? 'Banco' : e.tipo === 'caixa' ? 'Caixa' : e.tipo,
    }));
  }, [formSelectData.entidades, watchForma]);

  // Quando muda a forma de pagamento, limpa entidade incompatível.
  useEffect(() => {
    const current = form.getValues('entidade_financeira_id');
    if (!current) return;
    const stillCompatible = entidadeOptions.some((o) => Number(o.value) === current);
    if (!stillCompatible) {
      form.setValue('entidade_financeira_id', null, { shouldValidate: false });
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [watchForma]);

  // ── Carrega registro em modo edição ──────────────────────────────────────

  useEffect(() => {
    if (!open) return;

    if (!isEditing) {
      form.reset(FORM_DEFAULTS);
      setFielSelecionado(null);
      setFielQuery('');
      return;
    }

    let cancelled = false;
    fetch(`/api/cadastros/dizimos/${editingId}`, {
      headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin',
    })
      .then((r) => (r.ok ? r.json() : Promise.reject(new Error(`HTTP ${r.status}`))))
      .then((res: { success: boolean; data: Record<string, unknown> }) => {
        if (cancelled || !res.success) return;
        const d = res.data;
        const fiel = d.fiel as
          | { id: number; nome_completo: string; avatar_url: string | null }
          | null;
        form.reset({
          tipo: (d.tipo as Tipo) ?? 'Dízimo',
          fiel_id: fiel?.id ?? 0,
          comunidade: null,
          periodo: false,
          mes_inicio: '',
          mes_fim: '',
          data_pagamento: (d.data_pagamento as string) ?? todayISO(),
          valor: Number(d.valor ?? 0),
          oferta_adicional: false,
          oferta_adicional_valor: null,
          oferta_adicional_ref: null,
          forma_pagamento: (d.forma_pagamento as FormaPagamento) ?? 'Dinheiro',
          entidade_financeira_id: (d.entidade_financeira_id as number | null) ?? null,
          integrar_financeiro: Boolean(d.integrado_financeiro),
          observacoes: (d.observacoes as string | null) ?? null,
        });
        if (fiel) {
          setFielSelecionado({
            id: fiel.id,
            nome: fiel.nome_completo,
            avatar_url: fiel.avatar_url,
            comunidade: null,
          });
          setFielQuery(fiel.nome_completo);
        }
      })
      .catch(() => {
        notify.error('Erro ao carregar lançamento', 'Tente novamente.');
      });
    return () => {
      cancelled = true;
    };
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [open, editingId]);

  // ── Submit ───────────────────────────────────────────────────────────────

  const handleSubmit = form.handleSubmit(async (values) => {
    if (!csrfToken) {
      notify.reload();
      return;
    }

    if (!values.fiel_id || values.fiel_id <= 0) {
      form.setError('fiel_id', { message: 'Selecione um fiel.' });
      return;
    }

    setSubmitting(true);
    try {
      const fd = new FormData();
      fd.append('_token', csrfToken);
      if (isEditing) fd.append('_method', 'PUT');

      fd.append('tipo', values.tipo);
      fd.append('fiel_id', String(values.fiel_id));
      fd.append('data_pagamento', values.data_pagamento);
      fd.append('valor', values.valor.toFixed(2));
      fd.append('forma_pagamento', values.forma_pagamento);
      if (values.entidade_financeira_id) {
        fd.append('entidade_financeira_id', String(values.entidade_financeira_id));
      }
      if (values.observacoes?.trim()) fd.append('observacoes', values.observacoes.trim());
      fd.append('integrar_financeiro', values.integrar_financeiro ? '1' : '0');

      if (!isEditing) {
        fd.append('periodo', values.periodo ? '1' : '0');
        if (values.periodo) {
          if (values.mes_inicio) fd.append('mes_inicio', values.mes_inicio);
          if (values.mes_fim) fd.append('mes_fim', values.mes_fim);
        }
        fd.append('oferta_adicional', values.oferta_adicional ? '1' : '0');
        if (values.oferta_adicional) {
          if (values.oferta_adicional_valor) {
            fd.append('oferta_adicional_valor', values.oferta_adicional_valor.toFixed(2));
          }
          if (values.oferta_adicional_ref?.trim()) {
            fd.append('oferta_adicional_ref', values.oferta_adicional_ref.trim());
          }
        }
      }

      const url = isEditing
        ? `/api/cadastros/dizimos/${editingId}`
        : '/api/cadastros/dizimos';

      const res = await fetch(url, {
        method: 'POST',
        headers: {
          Accept: 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
        body: fd,
      });

      const result = (await res.json()) as {
        success?: boolean;
        message?: string;
        errors?: Record<string, string[]>;
        count?: number;
      };

      if (res.ok && result.success !== false) {
        const msg = isEditing
          ? 'Lançamento atualizado.'
          : result.count && result.count > 1
            ? `${result.count} lançamentos registrados.`
            : 'Lançamento registrado.';
        notify.success(isEditing ? 'Atualizado!' : 'Registrado!', msg);
        onSaved?.();
        if (saveMode === 'new' && !isEditing) {
          setSaveMode('close');
          // Reset preservando tipo/forma/entidade para agilizar lançamentos em sequência.
          const keep = {
            tipo: values.tipo,
            forma_pagamento: values.forma_pagamento,
            entidade_financeira_id: values.entidade_financeira_id,
            integrar_financeiro: values.integrar_financeiro,
          };
          form.reset({ ...FORM_DEFAULTS, ...keep });
          setFielSelecionado(null);
          setFielQuery('');
        } else if (saveMode === 'clear') {
          setSaveMode('close');
          form.reset(FORM_DEFAULTS);
          setFielSelecionado(null);
          setFielQuery('');
        } else {
          onOpenChange(false);
        }
        return;
      }

      if (result.errors) {
        Object.entries(result.errors).forEach(([field, messages]) => {
          if (messages[0]) {
            form.setError(field as keyof DizimoFormValues, { message: messages[0] });
          }
        });
      }
      notify.error('Não foi possível salvar', result.message ?? 'Verifique os campos e tente novamente.');
    } catch {
      notify.networkError();
    } finally {
      setSubmitting(false);
    }
  });

  // ── Helpers de UI ────────────────────────────────────────────────────────

  const renderTipoButtons = useCallback(() => {
    return (
      <ButtonGroup className="w-full grid grid-cols-2 sm:grid-cols-4">
        {TIPOS.map((t) => (
          <Button
            key={t}
            type="button"
            variant={watchTipo === t ? 'primary' : 'outline'}
            size="sm"
            onClick={() => form.setValue('tipo', t)}
            className="justify-center"
          >
            {t}
          </Button>
        ))}
      </ButtonGroup>
    );
  }, [watchTipo, form]);

  return (
    <Sheet open={open} onOpenChange={onOpenChange}>
      <SheetContent side="right" className="w-full sm:max-w-xl flex flex-col p-0 gap-0">
        <SheetHeader className="border-b px-6 py-4">
          <SheetTitle>{isEditing ? 'Editar lançamento' : 'Novo lançamento'}</SheetTitle>
          <p className="text-xs text-muted-foreground">
            {isEditing
              ? 'Altere os dados do lançamento de Dízimo/Doação.'
              : 'Registre dízimo, doação ou oferta — opcionalmente em série mensal (carnê).'}
          </p>
        </SheetHeader>

        <Form {...form}>
          <form ref={formRef} onSubmit={handleSubmit} className="flex flex-col flex-1 min-h-0">
            <SheetBody className="flex-1 min-h-0 p-0">
              <ScrollArea className="h-full px-6 py-4">
                <div className="space-y-6">
                  {/* 1. Tipo do lançamento */}
                  <section className="space-y-2">
                    <Label>Tipo do lançamento</Label>
                    {renderTipoButtons()}
                  </section>

                  {/* 2. Identificação do fiel */}
                  <section className="space-y-3">
                    <div className="flex items-center justify-between">
                      <Label>Fiel</Label>
                      <Button
                        type="button"
                        size="sm"
                        variant="ghost"
                        className="h-7 px-2 text-xs text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50 dark:hover:bg-emerald-950/30"
                        onClick={() => setCadastroFielOpen(true)}
                      >
                        <UserPlus className="size-3.5 mr-1" />
                        Novo fiel
                      </Button>
                    </div>

                    {/* Busca unificada: aceita código D-XXXX/barras ou nome/CPF */}
                    <div className="flex gap-2">
                      <div className="relative flex-1">
                        <Input
                          value={fielQuery}
                          onChange={(e) => setFielQuery(e.target.value)}
                          placeholder="Nome, CPF ou código do carnê (D-0001)…"
                          onKeyDown={(e) => {
                            if (e.key === 'Enter') {
                              e.preventDefault();
                              handleFielSearch();
                            }
                          }}
                          className="pr-8"
                        />
                        {(searchingFieis || lookingUp) && (
                          <Loader2 className="absolute right-2.5 top-1/2 -translate-y-1/2 size-3.5 animate-spin text-muted-foreground" />
                        )}
                      </div>
                      {isCodigoPattern(fielQuery) && (
                        <Button
                          type="button"
                          variant="outline"
                          onClick={handleFielSearch}
                          disabled={lookingUp || !fielQuery.trim()}
                        >
                          {lookingUp ? <Loader2 className="size-4 animate-spin" /> : <Plus className="size-4" />}
                          Buscar
                        </Button>
                      )}
                    </div>

                    {lookupError && !lookingUp && (
                      <p className="text-xs text-destructive">{lookupError}</p>
                    )}

                    {/* Lista de resultados por nome — só aparece quando não é código */}
                    {!isCodigoPattern(fielQuery) && fielQuery.trim().length >= 2 && (
                      <SearchSelect
                        options={fielOptions}
                        value={watchFielId ? String(watchFielId) : ''}
                        onValueChange={handleSelectFielFromSearch}
                        placeholder={searchingFieis ? 'Buscando...' : 'Selecione o fiel'}
                        searchPlaceholder="Filtrar resultados..."
                        emptyListMessage={
                          searchingFieis ? 'Buscando...' : 'Nenhum fiel encontrado.'
                        }
                        popoverModal={false}
                      />
                    )}

                    {fielSelecionado && (
                      <div className="flex items-center gap-3 rounded-md border bg-muted/40 p-2">
                        <div className="size-9 rounded-full bg-muted overflow-hidden shrink-0">
                          {fielSelecionado.avatar_url ? (
                            <img
                              src={fielSelecionado.avatar_url}
                              alt={fielSelecionado.nome}
                              className="size-full object-cover"
                            />
                          ) : (
                            <span className="flex size-full items-center justify-center text-xs font-semibold uppercase text-muted-foreground">
                              {fielSelecionado.nome.charAt(0)}
                            </span>
                          )}
                        </div>
                        <div className="min-w-0">
                          <p className="truncate text-sm font-medium">{fielSelecionado.nome}</p>
                          {fielSelecionado.comunidade && (
                            <p className="truncate text-xs text-muted-foreground">
                              {fielSelecionado.comunidade}
                            </p>
                          )}
                        </div>
                      </div>
                    )}

                    {form.formState.errors.fiel_id && (
                      <p className="text-xs text-destructive">{form.formState.errors.fiel_id.message}</p>
                    )}
                  </section>

                  {/* 3. Período (apenas no cadastro) */}
                  {!isEditing && (
                    <section className="space-y-3">
                      <FormField
                        control={form.control}
                        name="periodo"
                        render={({ field }) => (
                          <FormItem className="flex items-center justify-between rounded-md border bg-accent/20 px-3 py-2">
                            <div>
                              <Label className="text-sm font-medium">Período (carnê)</Label>
                              <p className="text-xs text-muted-foreground mt-0.5">
                                Gera um lançamento por mês entre o início e o fim informados.
                              </p>
                            </div>
                            <FormControl>
                              <Switch checked={field.value} onCheckedChange={field.onChange} />
                            </FormControl>
                          </FormItem>
                        )}
                      />

                      {watchPeriodo && (
                        <div className="grid grid-cols-2 gap-3">
                          <FormField
                            control={form.control}
                            name="mes_inicio"
                            render={({ field }) => (
                              <FormItem>
                                <FormLabel>Primeiro mês/ano</FormLabel>
                                <FormControl>
                                  <Input
                                    placeholder="MM/AAAA"
                                    maxLength={7}
                                    value={field.value ?? ''}
                                    onChange={(e) => field.onChange(formatMesAno(e.target.value))}
                                  />
                                </FormControl>
                                <FormMessage />
                              </FormItem>
                            )}
                          />
                          <FormField
                            control={form.control}
                            name="mes_fim"
                            render={({ field }) => (
                              <FormItem>
                                <FormLabel>Último mês/ano</FormLabel>
                                <FormControl>
                                  <Input
                                    placeholder="MM/AAAA"
                                    maxLength={7}
                                    value={field.value ?? ''}
                                    onChange={(e) => field.onChange(formatMesAno(e.target.value))}
                                  />
                                </FormControl>
                                <FormMessage />
                              </FormItem>
                            )}
                          />
                        </div>
                      )}
                    </section>
                  )}

                  {/* 4. Pagamento */}
                  <section className="space-y-3">
                    <div className="grid grid-cols-2 gap-3">
                      <FormField
                        control={form.control}
                        name="data_pagamento"
                        render={({ field }) => (
                          <FormItem>
                            <FormLabel>Data da Oferta</FormLabel>
                            <FormControl>
                              <DatePicker
                                value={field.value}
                                onChange={field.onChange}
                                onBlur={field.onBlur}
                              />
                            </FormControl>
                            <FormMessage />
                          </FormItem>
                        )}
                      />
                      <FormField
                        control={form.control}
                        name="valor"
                        render={({ field }) => (
                          <FormItem>
                            <FormLabel>Valor</FormLabel>
                            <FormControl>
                              <CurrencyInput
                                value={
                                  field.value > 0
                                    ? (field.value * 100)
                                        .toString()
                                        .padStart(3, '0')
                                        .replace(/(\d+)(\d{2})$/, '$1,$2')
                                    : ''
                                }
                                onUnmaskedChange={(cents) =>
                                  field.onChange(Number(cents) / 100)
                                }
                                placeholder="0,00"
                              />
                            </FormControl>
                            <FormMessage />
                          </FormItem>
                        )}
                      />
                    </div>

                    {!isEditing && (
                      <>
                        <FormField
                          control={form.control}
                          name="oferta_adicional"
                          render={({ field }) => (
                            <FormItem className="flex items-center justify-between rounded-md border bg-accent/20 px-3 py-2">
                              <div>
                                <Label className="text-sm font-medium">Oferta adicional</Label>
                                <p className="text-xs text-muted-foreground mt-0.5">
                                  Cria um registro extra do tipo "Oferta" para o mesmo fiel/data.
                                </p>
                              </div>
                              <FormControl>
                                <Switch checked={field.value} onCheckedChange={field.onChange} />
                              </FormControl>
                            </FormItem>
                          )}
                        />

                        {watchOferta && (
                          <div className="grid grid-cols-2 gap-3">
                            <FormField
                              control={form.control}
                              name="oferta_adicional_valor"
                              render={({ field }) => (
                                <FormItem>
                                  <FormLabel>Valor da oferta</FormLabel>
                                  <FormControl>
                                    <CurrencyInput
                                      value={
                                        field.value && field.value > 0
                                          ? (field.value * 100)
                                              .toString()
                                              .padStart(3, '0')
                                              .replace(/(\d+)(\d{2})$/, '$1,$2')
                                          : ''
                                      }
                                      onUnmaskedChange={(cents) =>
                                        field.onChange(cents ? Number(cents) / 100 : null)
                                      }
                                      placeholder="0,00"
                                    />
                                  </FormControl>
                                  <FormMessage />
                                </FormItem>
                              )}
                            />
                            <FormField
                              control={form.control}
                              name="oferta_adicional_ref"
                              render={({ field }) => (
                                <FormItem>
                                  <FormLabel>Ref. oferta adicional</FormLabel>
                                  <FormControl>
                                    <Input
                                      value={field.value ?? ''}
                                      onChange={(e) => field.onChange(e.target.value || null)}
                                      placeholder="Ex.: Construção do salão"
                                    />
                                  </FormControl>
                                  <FormMessage />
                                </FormItem>
                              )}
                            />
                          </div>
                        )}
                      </>
                    )}
                  </section>

                  {/* 5. Financeiro */}
                  <section className="space-y-3">
                    <div className="grid grid-cols-1 gap-3">
                      <FormField
                        control={form.control}
                        name="forma_pagamento"
                        render={({ field }) => (
                          <FormItem>
                            <FormLabel>Forma de recebimento</FormLabel>
                            <Select value={field.value} onValueChange={field.onChange}>
                              <FormControl>
                                <SelectTrigger>
                                  <SelectValue />
                                </SelectTrigger>
                              </FormControl>
                              <SelectContent>
                                {FORMAS_PAGAMENTO.map((fp) => (
                                  <SelectItem key={fp} value={fp}>
                                    {fp}
                                  </SelectItem>
                                ))}
                              </SelectContent>
                            </Select>
                            <FormMessage />
                          </FormItem>
                        )}
                      />

                      <FormField
                        control={form.control}
                        name="entidade_financeira_id"
                        render={({ field }) => (
                          <FormItem>
                            <FormLabel>
                              Conta / Caixa{watchIntegrar ? ' *' : ''}
                            </FormLabel>
                            <SearchSelect
                              options={entidadeOptions}
                              value={field.value ? String(field.value) : ''}
                              onValueChange={(v) =>
                                field.onChange(v ? Number(v) : null)
                              }
                              placeholder={
                                entidadeOptions.length === 0
                                  ? 'Nenhuma conta compatível'
                                  : 'Selecione conta/caixa'
                              }
                              searchPlaceholder="Buscar conta..."
                              popoverModal={false}
                              emptyListMessage="Nenhuma conta cadastrada."
                            />
                            <FormMessage />
                          </FormItem>
                        )}
                      />
                    </div>

                    <FormField
                      control={form.control}
                      name="integrar_financeiro"
                      render={({ field }) => (
                        <FormItem className="flex items-center justify-between rounded-md border bg-accent/20 px-3 py-2">
                          <div>
                            <Label className="text-sm font-medium">Integrar ao financeiro</Label>
                            <p className="text-xs text-muted-foreground mt-0.5">
                              Quando marcado, cria a movimentação na conta selecionada e atualiza o saldo.
                            </p>
                          </div>
                          <FormControl>
                            <Switch checked={field.value} onCheckedChange={field.onChange} />
                          </FormControl>
                        </FormItem>
                      )}
                    />

                    <FormField
                      control={form.control}
                      name="observacoes"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Observações</FormLabel>
                          <FormControl>
                            <Textarea
                              rows={3}
                              value={field.value ?? ''}
                              onChange={(e) => field.onChange(e.target.value || null)}
                              placeholder="Opcional"
                            />
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                  </section>
                </div>
              </ScrollArea>
            </SheetBody>

            <SheetFooter className="border-t px-6 py-3 flex-row items-center justify-end gap-2 bg-background">
              <Button
                type="button"
                variant="outline"
                onClick={() => onOpenChange(false)}
                disabled={submitting}
              >
                Cancelar
              </Button>
              <SplitSaveButton
                saving={submitting}
                isEditing={isEditing}
                formRef={formRef}
                onSave={() => { setSaveMode('close'); }}
                onSaveAndNew={() => { setSaveMode('new'); }}
                onSaveAndClear={() => { setSaveMode('clear'); }}
              />
            </SheetFooter>
          </form>
        </Form>
      </SheetContent>

      {/*
        Quick-create de fiel — abre o `CadastroFielSheet` exigindo CPF
        (validação extra do schema + flag `require_cpf=1` enviada ao backend
        para garantir unicidade na company ativa). Ao salvar, o fiel é
        auto-selecionado no drawer e o usuário continua o lançamento.
      */}
      <CadastroFielSheet
        open={cadastroFielOpen}
        onOpenChange={setCadastroFielOpen}
        requireCpf
        onCreated={(fiel) => {
          form.setValue('fiel_id', fiel.id, { shouldValidate: true });
          form.setValue('comunidade', null);
          setFielSelecionado({
            id: fiel.id,
            nome: fiel.nome_completo,
            avatar_url: fiel.avatar_url,
            comunidade: null,
          });
          setFielQuery(fiel.nome_completo);
          notify.success('Fiel cadastrado!', `${fiel.nome_completo} foi selecionado para o lançamento.`);
        }}
      />
    </Sheet>
  );
}

// ── SplitSaveButton ────────────────────────────────────────────────────────

function SplitSaveButton({
  saving,
  isEditing,
  formRef,
  onSave,
  onSaveAndNew,
  onSaveAndClear,
}: {
  saving: boolean;
  isEditing: boolean;
  formRef: RefObject<HTMLFormElement | null>;
  onSave: () => void;
  onSaveAndNew: () => void;
  onSaveAndClear: () => void;
}) {
  function submit(modeFn: () => void) {
    modeFn();
    // Aguarda o próximo tick para o state ser atualizado antes do submit
    setTimeout(() => formRef.current?.requestSubmit(), 0);
  }

  return (
    <ButtonGroup>
      <Button
        type="submit"
        className="bg-blue-600 hover:bg-blue-700 text-white border-0 rounded-e-none"
        onClick={onSave}
        disabled={saving}
      >
        {saving ? <Loader2 className="size-4 animate-spin" /> : <Save className="size-4" />}
        {saving ? 'Salvando…' : 'Salvar'}
      </Button>
      <DropdownMenu>
        <DropdownMenuTrigger asChild>
          <Button
            type="button"
            className="bg-blue-600 hover:bg-blue-700 text-white border-0 border-l border-l-blue-500 px-2 rounded-s-none"
            disabled={saving}
          >
            <ChevronUp className="size-4" />
          </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end" side="top" className="min-w-[180px]">
          {!isEditing && (
            <DropdownMenuItem onSelect={() => submit(onSaveAndNew)}>
              <Plus className="size-4" />
              Salvar e novo
            </DropdownMenuItem>
          )}
          <DropdownMenuItem onSelect={() => submit(onSaveAndClear)}>
            <Eraser className="size-4" />
            Salvar e limpar
          </DropdownMenuItem>
        </DropdownMenuContent>
      </DropdownMenu>
    </ButtonGroup>
  );
}

// ── Helpers ────────────────────────────────────────────────────────────────

/** Aplica máscara progressiva MM/AAAA enquanto o usuário digita. */
function formatMesAno(raw: string): string {
  const digits = raw.replace(/\D/g, '').slice(0, 6);
  if (digits.length <= 2) return digits;
  return `${digits.slice(0, 2)}/${digits.slice(2)}`;
}
