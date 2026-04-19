import { useCallback, useEffect, useMemo, useState } from 'react';
import {
  ArrowLeft,
  Loader2,
  ChevronRight,
  ChevronDown,
  FolderTree,
  FileText,
  Sparkles,
  Shield,
  Building2,
  Settings2,
  Info,
  Check,
  ChevronsUpDown,
  X,
} from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import {
  Combobox,
  ComboboxContent,
  ComboboxEmpty,
  ComboboxInput,
  ComboboxItem,
  ComboboxList,
  ComboboxTrigger,
} from '@/components/ui/combobox';
import {
  Sheet,
  SheetBody,
  SheetContent,
  SheetFooter,
  SheetHeader,
  SheetTitle,
} from '@/components/ui/sheet';
import {
  Collapsible,
  CollapsibleContent,
  CollapsibleTrigger,
} from '@/components/ui/collapsible';
import {
  Tooltip,
  TooltipContent,
  TooltipTrigger,
} from '@/components/ui/tooltip';
import { Badge } from '@/components/ui/badge';
import { cn } from '@/lib/utils';
import { notify } from '@/lib/notify';

// ─────────────────────────────────────────────────────────────────────────────
// Types & Interfaces
// ─────────────────────────────────────────────────────────────────────────────

interface PlanoContaOption {
  id: number;
  code: string;
  name: string;
  type?: string;
}

interface PlanoContaFormSheetProps {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  planoContas: PlanoContaOption[];
  editingItem?: { id: number; code: string; name: string; type: string; is_analytical?: boolean } | null;
  onSuccess: () => void;
}

type PlanoContaType = '' | 'ativo' | 'passivo' | 'patrimonio_liquido' | 'receita' | 'despesa';
type ClassificacaoType = 'sintetica' | 'analitica';
type NaturezaType = '' | 'receita' | 'despesa' | 'transferencia' | 'patrimonial' | 'contabil';

interface PlanoContaFormState {
  code: string;
  name: string;
  type: PlanoContaType;
  parent_id: string;
  classificacao: ClassificacaoType;
  natureza: NaturezaType;
  centro_custo: string;
  external_code: string;
}

// ─────────────────────────────────────────────────────────────────────────────
// Constants
// ─────────────────────────────────────────────────────────────────────────────

const initialFormState: PlanoContaFormState = {
  code: '',
  name: '',
  type: '',
  parent_id: '',
  classificacao: 'analitica',
  natureza: '',
  centro_custo: '',
  external_code: '',
};

const TIPO_LABELS: Record<PlanoContaType, string> = {
  '': 'Selecione',
  ativo: 'Ativo',
  passivo: 'Passivo',
  patrimonio_liquido: 'Patrimônio Líquido',
  receita: 'Receita',
  despesa: 'Despesa',
};

const TIPO_COLORS: Record<PlanoContaType, string> = {
  '': '',
  ativo: 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/30 dark:text-blue-400 dark:border-blue-800',
  passivo: 'bg-red-50 text-red-700 border-red-200 dark:bg-red-950/30 dark:text-red-400 dark:border-red-800',
  patrimonio_liquido: 'bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-950/30 dark:text-purple-400 dark:border-purple-800',
  receita: 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/30 dark:text-emerald-400 dark:border-emerald-800',
  despesa: 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/30 dark:text-amber-400 dark:border-amber-800',
};

const NATUREZA_OPTIONS = [
  { value: 'receita', label: 'Receita', desc: 'Entradas financeiras' },
  { value: 'despesa', label: 'Despesa', desc: 'Saídas financeiras' },
  { value: 'transferencia', label: 'Transferência', desc: 'Movimentações internas' },
  { value: 'patrimonial', label: 'Patrimonial', desc: 'Bens e direitos' },
  { value: 'contabil', label: 'Somente Contábil', desc: 'Sem impacto financeiro' },
];

const CENTRO_CUSTO_OPTIONS = [
  { value: '', label: 'Nenhum (padrão)' },
  { value: 'matriz', label: 'Matriz' },
  { value: 'filial', label: 'Filial' },
  { value: 'provincial', label: 'Provincial' },
  { value: 'paroquia', label: 'Paróquia' },
];

// ─────────────────────────────────────────────────────────────────────────────
// Component
// ─────────────────────────────────────────────────────────────────────────────

export function PlanoContaFormSheet({
  open,
  onOpenChange,
  planoContas,
  editingItem = null,
  onSuccess,
}: PlanoContaFormSheetProps) {
  const [form, setForm] = useState<PlanoContaFormState>(initialFormState);
  const [errors, setErrors] = useState<Record<string, string[]>>({});
  const [saving, setSaving] = useState(false);
  const [showAdvanced, setShowAdvanced] = useState(false);
  const [codeLoading, setCodeLoading] = useState(false);
  const [loadingEdit, setLoadingEdit] = useState(false);

  const isEditing = editingItem != null;

  // ─────────────────────────────────────────────────────────────────────────
  // Derived State
  // ─────────────────────────────────────────────────────────────────────────

  const selectedParent = useMemo(() => {
    if (!form.parent_id || form.parent_id === '_none') return null;
    return planoContas.find((c) => String(c.id) === form.parent_id) ?? null;
  }, [form.parent_id, planoContas]);

  // Monta a hierarquia visual (breadcrumb da conta)
  const hierarchy = useMemo(() => {
    if (!selectedParent) return [];

    const parts: { code: string; name: string }[] = [];
    const codeParts = selectedParent.code.split('.');

    // Reconstrói a hierarquia baseada no código
    let currentCode = '';
    for (let i = 0; i < codeParts.length; i++) {
      currentCode = currentCode ? `${currentCode}.${codeParts[i]}` : codeParts[i];
      const conta = planoContas.find((c) => c.code === currentCode);
      if (conta) {
        parts.push({ code: conta.code, name: conta.name });
      }
    }

    return parts;
  }, [selectedParent, planoContas]);

  // Sugere o tipo automaticamente baseado na conta pai
  const suggestedType = useMemo((): PlanoContaType => {
    if (!selectedParent?.type) return '';
    const parentType = selectedParent.type.toLowerCase();
    if (parentType in TIPO_LABELS) return parentType as PlanoContaType;
    return '';
  }, [selectedParent]);

  // ─────────────────────────────────────────────────────────────────────────
  // Effects
  // ─────────────────────────────────────────────────────────────────────────

  // Auto-preenche o tipo quando a conta pai muda
  useEffect(() => {
    if (suggestedType && !form.type) {
      setForm((prev) => ({ ...prev, type: suggestedType }));
    }
  }, [suggestedType, form.type]);

  // Reset / preenche ao abrir-fechar
  useEffect(() => {
    if (!open) {
      setForm(initialFormState);
      setErrors({});
      setShowAdvanced(false);
      return;
    }

    if (editingItem) {
      // Pré-preenche com os dados conhecidos (incluindo classificacao para evitar envio errado)
      setForm({
        ...initialFormState,
        code: editingItem.code,
        name: editingItem.name,
        type: editingItem.type as PlanoContaType,
        classificacao: editingItem.is_analytical != null
          ? (editingItem.is_analytical ? 'analitica' : 'sintetica')
          : 'analitica',
      });

      // Busca dados completos da conta
      setLoadingEdit(true);
      void (async () => {
        try {
          const res = await fetch(`/contabilidade/plano-contas/${editingItem.id}/edit`, {
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
          });
          if (res.ok) {
            const json = await res.json();
            const account = json.conta ?? json.account ?? json;
            setForm((prev) => ({
              ...prev,
              parent_id: account.parent_id ? String(account.parent_id) : '_none',
              // O modelo retorna is_analytical (boolean), não allows_posting
              classificacao: account.is_analytical ? 'analitica' : 'sintetica',
              external_code: account.external_code ?? '',
            }));
          }
        } catch {
          // Mantém os dados pré-preenchidos
        } finally {
          setLoadingEdit(false);
        }
      })();
    }
  }, [open, editingItem]);

  // ─────────────────────────────────────────────────────────────────────────
  // Handlers
  // ─────────────────────────────────────────────────────────────────────────

  const handleOpenChange = useCallback(
    (newOpen: boolean) => {
      onOpenChange(newOpen);
    },
    [onOpenChange],
  );

  const loadNextCode = useCallback(async (parentId?: string) => {
    setCodeLoading(true);
    try {
      const params = parentId ? `?parent_id=${parentId}` : '';
      const res = await fetch(`/contabilidade/plano-contas/next-code${params}`, {
        headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin',
      });
      if (res.ok) {
        const json = await res.json();
        // API retorna { success: true, next_code: "1.01.001" }
        if (json.success && json.next_code) {
          setForm((prev) => ({ ...prev, code: json.next_code }));
        }
      }
    } catch {
      // Silently ignore
    } finally {
      setCodeLoading(false);
    }
  }, []);

  const handleParentChange = useCallback(
    (value: string) => {
      const parentConta = planoContas.find((c) => String(c.id) === value);

      setForm((prev) => ({
        ...prev,
        parent_id: value,
        // Auto-preenche o tipo se a conta pai tiver tipo definido
        type: parentConta?.type ? (parentConta.type.toLowerCase() as PlanoContaType) : prev.type,
      }));

      void loadNextCode(value === '_none' || value === '' ? undefined : value);
    },
    [loadNextCode, planoContas],
  );

  const handleSave = useCallback(async () => {
    setSaving(true);
    setErrors({});

    try {
      const url = isEditing
        ? `/contabilidade/plano-contas/${editingItem!.id}`
        : '/contabilidade/plano-contas';
      const method = isEditing ? 'PUT' : 'POST';

      const res = await fetch(url, {
        method,
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN':
            (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? '',
        },
        credentials: 'same-origin',
        body: JSON.stringify({
          code: form.code,
          name: form.name,
          type: form.type,
          parent_id: form.parent_id && form.parent_id !== '_none' ? form.parent_id : null,
          allows_posting: form.classificacao === 'analitica',
          external_code: form.external_code || null,
        }),
      });

      if (!res.ok) {
        const json = await res.json();
        if (json.errors) {
          setErrors(json.errors);
        } else {
          notify.error('Erro', json.message || 'Erro ao salvar conta.');
        }
        return;
      }

      notify.success(
        isEditing ? 'Conta Atualizada' : 'Conta Criada',
        isEditing
          ? 'A conta contábil foi atualizada com sucesso.'
          : 'A conta contábil foi cadastrada com sucesso.',
      );
      handleOpenChange(false);
      onSuccess();
    } catch {
      notify.error('Erro', 'Não foi possível salvar a conta contábil.');
    } finally {
      setSaving(false);
    }
  }, [form, handleOpenChange, isEditing, editingItem, onSuccess]);

  // ─────────────────────────────────────────────────────────────────────────
  // Render
  // ─────────────────────────────────────────────────────────────────────────

  return (
    <Sheet open={open} onOpenChange={handleOpenChange}>
      <SheetContent
        side="right"
        className="w-full sm:max-w-lg flex flex-col gap-0 p-0"
        aria-describedby={undefined}
      >
        {/* ─────────────── Header ─────────────── */}
        <SheetHeader className="px-6 py-5 border-b border-border/50 bg-muted/30">
          <div className="flex items-center gap-3">
            <Button
              type="button"
              variant="ghost"
              size="icon"
              className="size-9 shrink-0 rounded-full hover:bg-background"
              onClick={() => handleOpenChange(false)}
              aria-label="Voltar"
            >
              <ArrowLeft className="size-5" />
            </Button>
            <div className="flex-1 min-w-0">
              <SheetTitle className="text-lg font-semibold text-foreground">
                {isEditing ? 'Editar Conta Contábil' : 'Nova Conta Contábil'}
              </SheetTitle>
              <p className="text-xs text-muted-foreground mt-0.5">
                {isEditing
                  ? 'Edite as informações da conta contábil'
                  : 'Cadastre uma nova conta no plano de contas'}
              </p>
            </div>
          </div>
        </SheetHeader>

        {/* ─────────────── Body ─────────────── */}
        <SheetBody className="flex-1 overflow-y-auto">
          <div className="px-6 py-6 space-y-6">
            {/* ─────── Seção: Identificação ─────── */}
            <section className="space-y-4">
              <div className="flex items-center gap-2 text-sm font-medium text-foreground">
                <FileText className="size-4 text-primary" />
                Identificação
              </div>

              {/* Nome da Conta */}
              <div className="space-y-2">
                <Label className="text-sm font-medium">
                  Nome da Conta <span className="text-destructive">*</span>
                </Label>
                <Input
                  value={form.name}
                  onChange={(e) => setForm((prev) => ({ ...prev, name: e.target.value }))}
                  placeholder="Ex: Bancos Conta Movimento"
                  className={cn(
                    'h-11 bg-background',
                    errors.name && 'border-destructive focus-visible:ring-destructive',
                  )}
                />
                {errors.name && (
                  <p className="text-xs text-destructive">{errors.name[0]}</p>
                )}
              </div>

              {/* Conta Pai */}
              <div className="space-y-2">
                <Label className="text-sm font-medium">Conta Pai</Label>
                <Combobox
                  items={planoContas}
                  getItemKey={(c) => String(c.id)}
                  itemToString={(c) => `${c.code} - ${c.name}`}
                  getItemFilterText={(c) => `${c.code} ${c.name}`}
                  valueKey={form.parent_id && form.parent_id !== '_none' ? form.parent_id : null}
                  onValueChangeKey={(key) => handleParentChange(key ?? '_none')}
                  placeholder="Buscar conta pai..."
                >
                  <div className="relative">
                    <ComboboxTrigger
                      render={
                        <Button
                          variant="outline"
                          role="combobox"
                          className={cn(
                            'h-11 w-full justify-between bg-background font-normal',
                            !form.parent_id || form.parent_id === '_none'
                              ? 'text-muted-foreground'
                              : '',
                          )}
                        >
                          <span className="truncate">
                            {form.parent_id && form.parent_id !== '_none'
                              ? (() => {
                                  const conta = planoContas.find(
                                    (c) => String(c.id) === form.parent_id,
                                  );
                                  return conta ? (
                                    <span className="flex items-center gap-2">
                                      <span className="font-mono text-xs text-muted-foreground">
                                        {conta.code}
                                      </span>
                                      <span>{conta.name}</span>
                                    </span>
                                  ) : (
                                    'Buscar conta pai...'
                                  );
                                })()
                              : 'Nenhuma — conta raiz'}
                          </span>
                          <ChevronsUpDown className="ml-2 size-4 shrink-0 opacity-50" />
                        </Button>
                      }
                    />
                    {form.parent_id && form.parent_id !== '_none' && (
                      <Button
                        type="button"
                        variant="ghost"
                        size="icon"
                        className="absolute right-8 top-1/2 -translate-y-1/2 size-6 hover:bg-muted"
                        onClick={(e) => {
                          e.stopPropagation();
                          handleParentChange('_none');
                        }}
                      >
                        <X className="size-3.5" />
                      </Button>
                    )}
                  </div>
                  <ComboboxContent className="p-0">
                    <ComboboxInput placeholder="Digite para buscar..." />
                    <ComboboxEmpty>Nenhuma conta encontrada.</ComboboxEmpty>
                    <ComboboxList itemsType={planoContas}>
                      {(conta) => (
                        <ComboboxItem value={conta} className="flex items-center gap-2">
                          <Check
                            className={cn(
                              'size-4 shrink-0',
                              form.parent_id === String(conta.id)
                                ? 'opacity-100'
                                : 'opacity-0',
                            )}
                          />
                          <span className="font-mono text-xs text-muted-foreground">
                            {conta.code}
                          </span>
                          <span className="truncate">{conta.name}</span>
                        </ComboboxItem>
                      )}
                    </ComboboxList>
                  </ComboboxContent>
                </Combobox>
                {errors.parent_id && (
                  <p className="text-xs text-destructive">{errors.parent_id[0]}</p>
                )}
              </div>

              {/* Preview da Hierarquia */}
              {hierarchy.length > 0 && (
                <div className="rounded-lg border border-border/50 bg-muted/30 p-3">
                  <div className="flex items-center gap-1.5 text-xs text-muted-foreground mb-2">
                    <FolderTree className="size-3.5" />
                    <span className="font-medium">Hierarquia</span>
                  </div>
                  <div className="flex flex-wrap items-center gap-1">
                    {hierarchy.map((item, idx) => (
                      <span key={item.code} className="flex items-center gap-1">
                        {idx > 0 && <ChevronRight className="size-3 text-muted-foreground/50" />}
                        <span className="text-xs px-2 py-1 rounded bg-background border border-border/50 text-foreground">
                          {item.name}
                        </span>
                      </span>
                    ))}
                    <ChevronRight className="size-3 text-muted-foreground/50" />
                    <span className="text-xs px-2 py-1 rounded bg-primary/10 border border-primary/20 text-primary font-medium">
                      {form.name || 'Nova Conta'}
                    </span>
                  </div>
                </div>
              )}

              {/* Código da Conta */}
              <div className="space-y-2">
                <div className="flex items-center justify-between">
                  <Label className="text-sm font-medium">
                    Código <span className="text-destructive">*</span>
                  </Label>
                  {codeLoading && (
                    <span className="flex items-center gap-1.5 text-xs text-muted-foreground">
                      <Loader2 className="size-3 animate-spin" />
                      Gerando...
                    </span>
                  )}
                </div>
                <div className="relative">
                  <Input
                    value={form.code}
                    onChange={(e) => setForm((prev) => ({ ...prev, code: e.target.value }))}
                    placeholder="Ex: 1.1.01.001"
                    className={cn(
                      'h-11 bg-background font-mono pr-24',
                      errors.code && 'border-destructive focus-visible:ring-destructive',
                    )}
                  />
                  {form.code && !codeLoading && (
                    <div className="absolute right-3 top-1/2 -translate-y-1/2 flex items-center gap-1.5">
                      <Sparkles className="size-3.5 text-amber-500" />
                      <span className="text-xs text-muted-foreground">Sugerido</span>
                    </div>
                  )}
                </div>
                {errors.code && (
                  <p className="text-xs text-destructive">{errors.code[0]}</p>
                )}
              </div>
            </section>

            <div className="border-t border-border/50" />

            {/* ─────── Seção: Classificação ─────── */}
            <section className="space-y-4">
              <div className="flex items-center gap-2 text-sm font-medium text-foreground">
                <FolderTree className="size-4 text-primary" />
                Classificação
              </div>

              {/* Tipo */}
              <div className="space-y-2">
                <div className="flex items-center gap-2">
                  <Label className="text-sm font-medium">
                    Tipo <span className="text-destructive">*</span>
                  </Label>
                  {suggestedType && form.type === suggestedType && (
                    <Badge variant="secondary" className="text-[10px] h-5 gap-1">
                      <Sparkles className="size-3" />
                      Auto
                    </Badge>
                  )}
                </div>
                <Select
                  value={form.type}
                  onValueChange={(v) => setForm((prev) => ({ ...prev, type: v as PlanoContaType }))}
                >
                  <SelectTrigger
                    className={cn(
                      'h-11 bg-background',
                      errors.type && 'border-destructive focus-visible:ring-destructive',
                    )}
                  >
                    <SelectValue placeholder="Selecione o tipo" />
                  </SelectTrigger>
                  <SelectContent>
                    {(Object.keys(TIPO_LABELS) as PlanoContaType[])
                      .filter((k) => k !== '')
                      .map((key) => (
                        <SelectItem key={key} value={key}>
                          <span className="flex items-center gap-2">
                            <span
                              className={cn(
                                'size-2 rounded-full',
                                key === 'ativo' && 'bg-blue-500',
                                key === 'passivo' && 'bg-red-500',
                                key === 'patrimonio_liquido' && 'bg-purple-500',
                                key === 'receita' && 'bg-emerald-500',
                                key === 'despesa' && 'bg-amber-500',
                              )}
                            />
                            {TIPO_LABELS[key]}
                          </span>
                        </SelectItem>
                      ))}
                  </SelectContent>
                </Select>
                {form.type && (
                  <div
                    className={cn(
                      'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium border',
                      TIPO_COLORS[form.type],
                    )}
                  >
                    {TIPO_LABELS[form.type]}
                  </div>
                )}
                {errors.type && (
                  <p className="text-xs text-destructive">{errors.type[0]}</p>
                )}
              </div>

              {/* Classificação: Sintética x Analítica */}
              <div className="space-y-3">
                <Label className="text-sm font-medium">Classificação</Label>
                <div className="grid grid-cols-2 gap-3">
                  {/* Card Sintética */}
                  <button
                    type="button"
                    onClick={() => setForm((prev) => ({ ...prev, classificacao: 'sintetica' }))}
                    className={cn(
                      'relative flex flex-col items-start p-4 rounded-xl border-2 transition-all text-left',
                      'hover:border-primary/50 hover:bg-primary/5',
                      form.classificacao === 'sintetica'
                        ? 'border-primary bg-primary/5 ring-2 ring-primary/20'
                        : 'border-border/50 bg-background',
                    )}
                  >
                    <div
                      className={cn(
                        'size-10 rounded-lg flex items-center justify-center mb-3',
                        form.classificacao === 'sintetica'
                          ? 'bg-primary/10 text-primary'
                          : 'bg-muted text-muted-foreground',
                      )}
                    >
                      <FolderTree className="size-5" />
                    </div>
                    <span className="text-sm font-semibold text-foreground">Sintética</span>
                    <span className="text-xs text-muted-foreground mt-1 leading-relaxed">
                      Agrupa subcontas. Não recebe lançamentos.
                    </span>
                    {form.classificacao === 'sintetica' && (
                      <div className="absolute top-3 right-3 size-5 rounded-full bg-primary flex items-center justify-center">
                        <svg className="size-3 text-primary-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={3} d="M5 13l4 4L19 7" />
                        </svg>
                      </div>
                    )}
                  </button>

                  {/* Card Analítica */}
                  <button
                    type="button"
                    onClick={() => setForm((prev) => ({ ...prev, classificacao: 'analitica' }))}
                    className={cn(
                      'relative flex flex-col items-start p-4 rounded-xl border-2 transition-all text-left',
                      'hover:border-primary/50 hover:bg-primary/5',
                      form.classificacao === 'analitica'
                        ? 'border-primary bg-primary/5 ring-2 ring-primary/20'
                        : 'border-border/50 bg-background',
                    )}
                  >
                    <div
                      className={cn(
                        'size-10 rounded-lg flex items-center justify-center mb-3',
                        form.classificacao === 'analitica'
                          ? 'bg-primary/10 text-primary'
                          : 'bg-muted text-muted-foreground',
                      )}
                    >
                      <FileText className="size-5" />
                    </div>
                    <span className="text-sm font-semibold text-foreground">Analítica</span>
                    <span className="text-xs text-muted-foreground mt-1 leading-relaxed">
                      Recebe lançamentos financeiros diretos.
                    </span>
                    {form.classificacao === 'analitica' && (
                      <div className="absolute top-3 right-3 size-5 rounded-full bg-primary flex items-center justify-center">
                        <svg className="size-3 text-primary-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={3} d="M5 13l4 4L19 7" />
                        </svg>
                      </div>
                    )}
                  </button>
                </div>
                {(errors.allows_posting ?? errors.classificacao) && (
                  <p className="text-xs text-destructive mt-1">
                    {(errors.allows_posting ?? errors.classificacao)?.[0]}
                  </p>
                )}
              </div>

              {/* Natureza Financeira (apenas para Analítica) */}
              {form.classificacao === 'analitica' && (
                <div className="space-y-3">
                  <div className="flex items-center gap-2">
                    <Label className="text-sm font-medium">Natureza Financeira</Label>
                    <Tooltip>
                      <TooltipTrigger asChild>
                        <Info className="size-3.5 text-muted-foreground cursor-help" />
                      </TooltipTrigger>
                      <TooltipContent side="top" className="max-w-xs">
                        Define o comportamento da conta em relatórios e fluxo de caixa.
                      </TooltipContent>
                    </Tooltip>
                  </div>
                  <div className="grid grid-cols-2 gap-2">
                    {NATUREZA_OPTIONS.map((opt) => (
                      <button
                        key={opt.value}
                        type="button"
                        onClick={() => setForm((prev) => ({ ...prev, natureza: opt.value as NaturezaType }))}
                        className={cn(
                          'flex flex-col items-start p-3 rounded-lg border transition-all text-left',
                          'hover:border-primary/50',
                          form.natureza === opt.value
                            ? 'border-primary bg-primary/5'
                            : 'border-border/50 bg-background',
                        )}
                      >
                        <span className="text-sm font-medium text-foreground">{opt.label}</span>
                        <span className="text-[11px] text-muted-foreground">{opt.desc}</span>
                      </button>
                    ))}
                  </div>
                </div>
              )}
            </section>

            <div className="border-t border-border/50" />

            {/* ─────── Seção: Configurações Avançadas (Colapsável) ─────── */}
            <Collapsible open={showAdvanced} onOpenChange={setShowAdvanced}>
              <CollapsibleTrigger asChild>
                <button
                  type="button"
                  className="flex items-center gap-2 text-sm font-medium text-muted-foreground hover:text-foreground transition-colors w-full"
                >
                  <Settings2 className="size-4" />
                  <span>Configurações Avançadas</span>
                  <ChevronDown
                    className={cn(
                      'size-4 ml-auto transition-transform',
                      showAdvanced && 'rotate-180',
                    )}
                  />
                </button>
              </CollapsibleTrigger>
              <CollapsibleContent className="pt-4 space-y-4">
                {/* Código de Integração */}
                <div className="space-y-2">
                  <Label className="text-sm font-medium">Código de Integração ERP</Label>
                  <Input
                    value={form.external_code}
                    onChange={(e) => setForm((prev) => ({ ...prev, external_code: e.target.value }))}
                    placeholder="Código para integração com sistema externo"
                    className="h-11 bg-background font-mono text-sm"
                  />
                  <p className="text-xs text-muted-foreground">
                    Utilizado para integração com sistemas contábeis externos.
                  </p>
                </div>

                {/* Centro de Custo */}
                <div className="space-y-2">
                  <div className="flex items-center gap-2">
                    <Building2 className="size-4 text-muted-foreground" />
                    <Label className="text-sm font-medium">Unidade / Centro de Custo</Label>
                  </div>
                  <Select
                    value={form.centro_custo}
                    onValueChange={(v) => setForm((prev) => ({ ...prev, centro_custo: v }))}
                  >
                    <SelectTrigger className="h-11 bg-background">
                      <SelectValue placeholder="Selecione uma unidade (opcional)" />
                    </SelectTrigger>
                    <SelectContent>
                      {CENTRO_CUSTO_OPTIONS.map((opt) => (
                        <SelectItem key={opt.value || '_empty'} value={opt.value || '_empty'}>
                          {opt.label}
                        </SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                </div>
              </CollapsibleContent>
            </Collapsible>
          </div>
        </SheetBody>

        {/* ─────────────── Footer ─────────────── */}
        <SheetFooter className="px-6 py-4 border-t border-border/50 bg-muted/30">
          <div className="w-full space-y-4">
            {/* Observação de Segurança */}
            <div className="flex items-start gap-2 text-xs text-muted-foreground">
              <Shield className="size-3.5 shrink-0 mt-0.5" />
              <span>Toda alteração será registrada no histórico do sistema para auditoria.</span>
            </div>

            {/* Botões */}
            <div className="flex items-center justify-between gap-3">
              <Button
                type="button"
                variant="ghost"
                onClick={() => handleOpenChange(false)}
                disabled={saving}
                className="text-muted-foreground"
              >
                Cancelar
              </Button>
              <Button
                type="button"
                onClick={() => void handleSave()}
                disabled={saving || loadingEdit || !form.name || !form.code || !form.type}
                className="min-w-[140px] bg-primary hover:bg-primary/90"
              >
                {saving ? (
                  <>
                    <Loader2 className="size-4 animate-spin mr-2" />
                    Salvando...
                  </>
                ) : loadingEdit ? (
                  <>
                    <Loader2 className="size-4 animate-spin mr-2" />
                    Carregando...
                  </>
                ) : isEditing ? (
                  'Salvar Alterações'
                ) : (
                  'Salvar Conta'
                )}
              </Button>
            </div>
          </div>
        </SheetFooter>
      </SheetContent>
    </Sheet>
  );
}
