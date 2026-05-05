import { type RefObject, useCallback, useEffect, useMemo, useRef, useState } from 'react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import { ChevronUp, CalendarRange, Eraser, Landmark, Loader2, MessageSquareText, Plus, Save, Tags, UserPlus, Users, Wallet, X } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { ButtonGroup } from '@/components/ui/button-group';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { DatePicker } from '@/components/ui/date-picker';
import { MonthYearPicker } from '@/components/ui/month-year-picker';
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

function onlyDigitsFielQuery(s: string): string {
  return s.replace(/\D/g, '');
}

/**
 * Código de carteirinha/carnê (D-XXXX ou só o número, até 6 dígitos).
 * CPF (11 dígitos com ou sem máscara) não entra aqui — usa a busca textual na API.
 */
function isCodigoCarnetPattern(v: string): boolean {
  const t = v.trim();
  if (!t) return false;
  if (onlyDigitsFielQuery(t).length === 11) return false;
  if (/^D[-\s]?\d/i.test(t)) return true;
  if (/^\d+$/.test(t)) return t.length <= 6;
  return false;
}

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

    /**
     * Identificador externo do recebimento (cheque, TID PIX, comprovante,
     * etc.). Usado pela conciliação bancária — comparado com o `checknum`
     * do extrato OFX no `ConciliacaoMatchingService`.
     */
    numero_documento: z.string().max(50, 'Máx. 50 caracteres.').nullable().optional(),

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
  numero_documento: null,
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
  // Um campo: nome, CPF (com ou sem máscara), código da carteirinha/carnê (D-XXXX
  // ou só o número curto). CPF de 11 dígitos nunca é tratado como código de carnê.

  const [fielQuery, setFielQuery] = useState('');
  const [fielSelecionado, setFielSelecionado] = useState<{
    id: number;
    nome: string;
    avatar_url: string | null;
    comunidade: string | null;
  } | null>(null);

  // Detecta se o input parece código de carnê/carteirinha (não CPF).
  const searchTermForList = useMemo(() => {
    const t = fielQuery.trim();
    if (t.length < 2) return '';
    if (onlyDigitsFielQuery(t).length === 11) return fielQuery;
    if (/^D[-\s]?\d/i.test(t)) return fielQuery;
    if (!isCodigoCarnetPattern(fielQuery)) return fielQuery;
    return '';
  }, [fielQuery]);

  const { options: fielMatches, loading: searchingFieis } = useFielSearch(searchTermForList);
  const { lookup: lookupCarne, loading: lookingUp, error: lookupError } = useLookupCarne();


  /** Dispara lookup de carnê/código da carteirinha (variantes tratadas no backend). */
  async function handleFielSearch() {
    const q = fielQuery.trim();
    if (!q || !isCodigoCarnetPattern(q)) return;
    // Normaliza: se digitou só números, prefixar com D-
    const codigo = /^\d+$/.test(q) ? `D-${q.padStart(4, '0')}` : q.toUpperCase();
    const result = await lookupCarne(codigo);
    if (!result) {
      notify.error(
        'Código não encontrado',
        'Não encontramos esse código de carteirinha/carnê. Confira o número ou busque por nome ou CPF na lista.',
      );
      return;
    }
    selecionarFiel(result.fiel.id, result.fiel.nome_completo, result.fiel.avatar_url, result.comunidade ?? null);
    setFielQuery('');
    notify.success('Fiel encontrado', result.fiel.nome_completo);
  }

  function selecionarFiel(id: number, nome: string, avatar_url: string | null, comunidade: string | null) {
    form.setValue('fiel_id', id, { shouldValidate: true });
    form.setValue('comunidade', comunidade);
    setFielSelecionado({ id, nome, avatar_url, comunidade });
  }

  function limparFiel() {
    setFielSelecionado(null);
    form.setValue('fiel_id', 0, { shouldValidate: false });
    form.setValue('comunidade', null);
    setFielQuery('');
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
          numero_documento: (d.numero_documento as string | null) ?? null,
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
      if (values.numero_documento?.trim()) {
        fd.append('numero_documento', values.numero_documento.trim());
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
      <SheetContent side="right" className="gap-0 p-0 sm:max-w-4xl">
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
            <SheetBody className="flex-1 min-h-0 p-0 bg-muted">
              <ScrollArea className="h-full">
                <div className="p-5 space-y-4">
                  {/* ── Card 1: Tipo do lançamento ───────────────────────── */}
                  <Card className="rounded-xl">
                    <CardHeader className="min-h-12 bg-accent/50 py-2">
                      <CardTitle className="text-2sm flex items-center gap-1.5">
                        <Tags className="size-3.5 text-muted-foreground" />
                        Tipo do lançamento
                      </CardTitle>
                    </CardHeader>
                    <CardContent className="pt-4 space-y-3">
                      {renderTipoButtons()}
                    </CardContent>
                  </Card>

                  {/* ── Card 2: Fiel ─────────────────────────────────────── */}
                  <Card className="rounded-xl">
                    <CardHeader className="min-h-12 bg-accent/50 py-2">
                      <div className="flex w-full items-center justify-between gap-3">
                        <CardTitle className="text-2sm flex min-w-0 flex-1 items-center gap-1.5">
                          <Users className="size-3.5 shrink-0 text-muted-foreground" />
                          <span className="label-required">Fiel</span>
                        </CardTitle>
                        <Button
                          type="button"
                          size="sm"
                          className="h-7 shrink-0 border-0 bg-green-600 px-2.5 text-xs font-medium text-white shadow-none hover:bg-green-700 focus-visible:ring-green-600/40 dark:bg-green-700 dark:hover:bg-green-600"
                          onClick={() => setCadastroFielOpen(true)}
                        >
                          <UserPlus className="size-3.5 mr-1" />
                          Novo fiel
                        </Button>
                      </div>
                    </CardHeader>
                    <CardContent className="pt-4 space-y-3">
                      {fielSelecionado ? (
                        /* ── Fiel selecionado ── */
                        <div className="flex items-center gap-3 rounded-xl border bg-muted/40 p-2">
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
                          <div className="min-w-0 flex-1">
                            <p className="truncate text-sm font-medium">{fielSelecionado.nome}</p>
                            {fielSelecionado.comunidade && (
                              <p className="truncate text-xs text-muted-foreground">
                                {fielSelecionado.comunidade}
                              </p>
                            )}
                          </div>
                          <Button
                            type="button"
                            variant="ghost"
                            size="icon"
                            className="size-7 shrink-0 text-muted-foreground hover:text-destructive"
                            onClick={limparFiel}
                            aria-label="Trocar fiel"
                          >
                            <X className="size-3.5" />
                          </Button>
                        </div>
                      ) : (
                        /* ── Combobox de busca ── */
                        <div className="relative">
                          <Input
                            value={fielQuery}
                            onChange={(e) => setFielQuery(e.target.value)}
                            placeholder="Nome, CPF ou código da carteirinha (D-0001)…"
                            onKeyDown={(e) => {
                              if (e.key === 'Enter') {
                                e.preventDefault();
                                if (isCodigoCarnetPattern(fielQuery)) handleFielSearch();
                              }
                            }}
                            className="pr-8"
                            autoComplete="off"
                          />
                          {(searchingFieis || lookingUp) && (
                            <Loader2 className="absolute right-2.5 top-1/2 -translate-y-1/2 size-3.5 animate-spin text-muted-foreground" />
                          )}

                          {/* Dropdown de resultados por nome/CPF */}
                          {searchTermForList.trim().length >= 2 && (
                            <div className="absolute top-full left-0 right-0 z-50 mt-1 rounded-md border bg-popover shadow-md overflow-hidden">
                              {searchingFieis ? (
                                <div className="flex items-center gap-2 px-3 py-3 text-sm text-muted-foreground">
                                  <Loader2 className="size-3.5 animate-spin" /> Buscando...
                                </div>
                              ) : fielMatches.length === 0 ? (
                                <p className="px-3 py-3 text-sm text-muted-foreground text-center">Nenhum fiel encontrado.</p>
                              ) : (
                                <div className="max-h-52 overflow-y-auto">
                                  {fielMatches.map((f) => (
                                    <button
                                      key={f.id}
                                      type="button"
                                      className="flex w-full items-center gap-2.5 px-3 py-2 text-sm hover:bg-accent text-left"
                                      onMouseDown={(e) => {
                                        e.preventDefault();
                                        selecionarFiel(f.id, f.nome_completo, f.avatar_url, f.cidade_uf ?? null);
                                        setFielQuery('');
                                      }}
                                    >
                                      <div className="size-7 shrink-0 rounded-full bg-muted overflow-hidden flex items-center justify-center">
                                        {f.avatar_url ? (
                                          <img src={f.avatar_url} alt={f.nome_completo} className="size-full object-cover" />
                                        ) : (
                                          <span className="text-[10px] font-semibold uppercase text-muted-foreground">
                                            {f.nome_completo.charAt(0)}
                                          </span>
                                        )}
                                      </div>
                                      <div className="min-w-0">
                                        <p className="truncate font-medium">{f.nome_completo}</p>
                                        {(f.codigo_dizimista || f.cidade_uf) && (
                                          <p className="truncate text-xs text-muted-foreground">
                                            {[f.codigo_dizimista, f.cidade_uf].filter(Boolean).join(' · ')}
                                          </p>
                                        )}
                                      </div>
                                    </button>
                                  ))}
                                </div>
                              )}
                            </div>
                          )}

                          {/* Dica para lookup por código de carnê */}
                          {isCodigoCarnetPattern(fielQuery) && fielQuery.trim().length > 0 && (
                            <p className="mt-1.5 text-xs text-muted-foreground">
                              {/^D[-\s]?\d/i.test(fielQuery.trim()) ? (
                                <>
                                  Pressione <kbd className="rounded border px-1 text-[10px]">Enter</kbd> para
                                  buscar pelo código ou selecione o fiel na lista abaixo.
                                </>
                              ) : (
                                <>
                                  Pressione <kbd className="rounded border px-1 text-[10px]">Enter</kbd> ou{' '}
                                  <button
                                    type="button"
                                    className="underline underline-offset-2"
                                    onClick={handleFielSearch}
                                    disabled={lookingUp}
                                  >
                                    clique aqui
                                  </button>{' '}
                                  para buscar pelo número do carnê.
                                </>
                              )}
                            </p>
                          )}
                        </div>
                      )}

                      {lookupError && !lookingUp && (
                        <p className="text-xs text-destructive">{lookupError}</p>
                      )}

                      {form.formState.errors.fiel_id && (
                        <p className="text-xs text-destructive">{form.formState.errors.fiel_id.message}</p>
                      )}
                    </CardContent>
                  </Card>

                  {/* ── Card 3: Período (carnê) — apenas novo ───────────── */}
                  {!isEditing && (
                    <Card className="rounded-xl">
                      <CardHeader className="min-h-10.5 bg-accent/50 py-2">
                        <CardTitle className="text-2sm flex items-center gap-1.5">
                          <CalendarRange className="size-4.5 text-muted-foreground" />
                          Período (carnê)
                        </CardTitle>
                      </CardHeader>
                      <CardContent className="pt-4 space-y-3">
                      <FormField
                        control={form.control}
                        name="periodo"
                        render={({ field }) => (
                          <FormItem className="flex flex-row items-start gap-3 rounded-xl border bg-accent/20 px-3 py-2">
                            <FormControl className="shrink-0 pt-0.5">
                              <Switch checked={field.value} onCheckedChange={field.onChange} />
                            </FormControl>
                            <div className="min-w-0 flex-1 flex flex-wrap items-baseline gap-x-2 gap-y-0.5">
                              <Label className="text-sm font-medium leading-none">Período (carnê)</Label>
                              <span className="text-xs text-muted-foreground leading-snug">
                                Gera um lançamento por mês entre o início e o fim informados.
                              </span>
                            </div>
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
                                  <MonthYearPicker
                                    value={field.value ?? ''}
                                    onChange={field.onChange}
                                    onBlur={field.onBlur}
                                    invalid={!!form.formState.errors.mes_inicio}
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
                                  <MonthYearPicker
                                    value={field.value ?? ''}
                                    onChange={field.onChange}
                                    onBlur={field.onBlur}
                                    invalid={!!form.formState.errors.mes_fim}
                                  />
                                </FormControl>
                                <FormMessage />
                              </FormItem>
                            )}
                          />
                        </div>
                      )}
                      </CardContent>
                    </Card>
                  )}

                  {/* ── Card 4: Valores ──────────────────────────────────── */}
                  <Card className="rounded-xl">
                    <CardHeader className="min-h-12 bg-accent/50 py-2">
                      <CardTitle className="text-2sm flex items-center gap-1.5">
                        <Wallet className="size-3.5 text-muted-foreground" />
                        Valores e data
                      </CardTitle>
                    </CardHeader>
                    <CardContent className="pt-4 space-y-3">
                    <div className="grid grid-cols-2 gap-3">
                      <FormField
                        control={form.control}
                        name="data_pagamento"
                        render={({ field }) => (
                          <FormItem>
                            <FormLabel className="label-required">Data da Oferta</FormLabel>
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
                            <FormLabel className="label-required">Valor</FormLabel>
                            <FormControl>
                              <div className="relative">
                                <span className="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-muted-foreground font-medium pointer-events-none select-none">
                                  R$
                                </span>
                                <CurrencyInput
                                  className="pl-8"
                                  value={
                                    field.value > 0
                                      ? field.value.toLocaleString('pt-BR', {
                                          minimumFractionDigits: 2,
                                          maximumFractionDigits: 2,
                                        })
                                      : ''
                                  }
                                  onUnmaskedChange={(cents) =>
                                    field.onChange(Number(cents) / 100)
                                  }
                                />
                              </div>
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
                            <FormItem className="flex flex-row items-start gap-3 rounded-xl border bg-accent/20 px-3 py-2">
                              <FormControl className="shrink-0 pt-0.5">
                                <Switch checked={field.value} onCheckedChange={field.onChange} />
                              </FormControl>
                              <div className="min-w-0 flex-1 flex flex-wrap items-baseline gap-x-2 gap-y-0.5">
                                <Label className="text-sm font-medium leading-none">Oferta adicional</Label>
                                <span className="text-xs text-muted-foreground leading-snug">
                                  Cria um registro extra do tipo &quot;Oferta&quot; para o mesmo fiel/data.
                                </span>
                              </div>
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
                                    <div className="relative">
                                      <span className="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-muted-foreground font-medium pointer-events-none select-none">
                                        R$
                                      </span>
                                      <CurrencyInput
                                        className="pl-8"
                                        value={
                                          field.value && field.value > 0
                                            ? field.value.toLocaleString('pt-BR', {
                                                minimumFractionDigits: 2,
                                                maximumFractionDigits: 2,
                                              })
                                            : ''
                                        }
                                        onUnmaskedChange={(cents) =>
                                          field.onChange(cents ? Number(cents) / 100 : null)
                                        }
                                      />
                                    </div>
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
                    </CardContent>
                  </Card>

                  {/* ── Card 5: Recebimento e integração ───────────────── */}
                  <Card className="rounded-xl">
                    <CardHeader className="min-h-12 bg-accent/50 py-2">
                      <CardTitle className="text-2sm flex items-center gap-1.5">
                        <Landmark className="size-3.5 text-muted-foreground" />
                        Recebimento e integração
                      </CardTitle>
                    </CardHeader>
                    <CardContent className="pt-4 space-y-3">
                    <div className="grid grid-cols-1 gap-3 sm:grid-cols-2">
                      <FormField
                        control={form.control}
                        name="forma_pagamento"
                        render={({ field }) => (
                          <FormItem>
                            <FormLabel className="label-required">Forma de recebimento</FormLabel>
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
                        name="numero_documento"
                        render={({ field }) => (
                          <FormItem>
                            <FormLabel className="flex items-center gap-1.5">
                              Nº documento
                              <span className="text-[11px] font-normal text-muted-foreground">
                                (auxilia conciliação)
                              </span>
                            </FormLabel>
                            <FormControl>
                              <Input
                                value={field.value ?? ''}
                                onChange={(e) => field.onChange(e.target.value || null)}
                                onBlur={field.onBlur}
                                maxLength={50}
                                placeholder={numeroDocumentoPlaceholder(watchForma)}
                              />
                            </FormControl>
                            <FormMessage />
                          </FormItem>
                        )}
                      />
                    </div>

                    <FormField
                      control={form.control}
                      name="entidade_financeira_id"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel className={watchIntegrar ? 'label-required' : ''}>
                            Conta / Caixa
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

                    <FormField
                      control={form.control}
                      name="integrar_financeiro"
                      render={({ field }) => (
                        <FormItem className="flex flex-row items-start gap-3 rounded-xl border bg-accent/20 px-3 py-2">
                          <FormControl className="shrink-0 pt-0.5">
                            <Switch checked={field.value} onCheckedChange={field.onChange} />
                          </FormControl>
                          <div className="min-w-0 flex-1 flex flex-wrap items-baseline gap-x-2 gap-y-0.5">
                            <Label className="text-sm font-medium leading-none">Integrar ao financeiro</Label>
                            <span className="text-xs text-muted-foreground leading-snug">
                              Quando marcado, cria a movimentação na conta selecionada e atualiza o saldo.
                            </span>
                          </div>
                        </FormItem>
                      )}
                    />

                    </CardContent>
                  </Card>

                  {/* ── Card 6: Observações ──────────────────────────────── */}
                  <Card className="rounded-xl">
                    <CardHeader className="min-h-12 bg-accent/50 py-2">
                      <CardTitle className="text-2sm flex items-center gap-1.5">
                        <MessageSquareText className="size-3.5 text-muted-foreground" />
                        Observações
                      </CardTitle>
                    </CardHeader>
                    <CardContent className="pt-4">
                    <FormField
                      control={form.control}
                      name="observacoes"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel className="sr-only">Observações</FormLabel>
                          <FormControl>
                            <Textarea
                              rows={3}
                              value={field.value ?? ''}
                              onChange={(e) => field.onChange(e.target.value || null)}
                              placeholder="Opcional — notas internas sobre o lançamento"
                            />
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                    </CardContent>
                  </Card>
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

/**
 * Sugere um placeholder por forma de pagamento — guia o usuário a digitar
 * o identificador correto que costuma vir no extrato OFX (`checknum`).
 */
function numeroDocumentoPlaceholder(forma: FormaPagamento | undefined): string {
  switch (forma) {
    case 'Cheque':
      return 'Nº do cheque';
    case 'PIX':
      return 'TID / endToEndId';
    case 'Cartão de Débito':
    case 'Cartão de Crédito':
      return 'NSU / autorização';
    case 'Transferência':
      return 'Nº da transferência';
    case 'Dinheiro':
      return 'Nº do recibo (opcional)';
    default:
      return 'Identificador (opcional)';
  }
}

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
