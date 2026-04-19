import { useEffect, useState, useRef } from 'react';
import {
  Dialog,
  DialogBody,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogDescription,
  DialogFooter,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Loader2, Check, X, Building2, User, Users, Trash2 } from 'lucide-react';
import { notify } from '@/lib/notify';
import { useAppData } from '@/hooks/useAppData';
import { CurrencyInput } from '@/components/common/masked-input';

type SearchMode = 'tumulo' | 'difunto' | 'parente';

interface TumuloResult {
  id: number;
  codigo: string;
  localizacao?: string;
  tipo?: string;
  status?: string;
}

interface DifuntoResult {
  difunto_id: number;
  nome: string;
  cpf?: string;
  sepultura_id: number | null;
  sepultura_codigo: string;
  sepultura_local: string;
}

interface ParenteResult {
  responsavel_id: number;
  responsavel_nome: string;
  responsavel_tel?: string;
  difunto_id: number;
  difunto_nome: string;
  cpf?: string;
  sepultura_id: number | null;
  sepultura_codigo: string;
}

// Item adicionado ao carrinho de seleção
interface SelectedItem {
  key: string;        // único: "sepultura:1" | "sepultado:5"
  type: 'sepultura' | 'sepultado';
  id: number;
  label: string;      // código do túmulo ou nome do difunto
  sublabel?: string;  // localização ou vínculo exibido abaixo
}

interface Props {
  open: boolean;
  onOpenChange: (v: boolean) => void;
  onSaved?: () => void;
}

const EMPTY_FORM = {
  descricao: '',
  data_vencimento: '',
  valor: '',
  valor_cents: 0,
};

const MODE_LABELS: Record<SearchMode, string> = {
  tumulo:  'Por Túmulo',
  difunto: 'Por Difunto',
  parente: 'Por Parente',
};

const MODE_ICONS: Record<SearchMode, React.ReactNode> = {
  tumulo:  <Building2 className="size-3.5" />,
  difunto: <User className="size-3.5" />,
  parente: <Users className="size-3.5" />,
};

export function LancamentoCemiterioSheet({ open, onOpenChange, onSaved }: Props) {
  const { csrfToken } = useAppData();
  const [form, setForm] = useState({ ...EMPTY_FORM });
  const [submitting, setSubmitting] = useState(false);

  // Busca
  const [searchMode, setSearchMode] = useState<SearchMode>('tumulo');
  const [searchQuery, setSearchQuery] = useState('');
  const [results, setResults] = useState<(TumuloResult | DifuntoResult | ParenteResult)[]>([]);
  const [searching, setSearching] = useState(false);
  const debounceRef = useRef<ReturnType<typeof setTimeout> | null>(null);

  // Carrinho de itens selecionados
  const [selected, setSelected] = useState<SelectedItem[]>([]);

  // Reset ao fechar
  useEffect(() => {
    if (!open) {
      setForm({ ...EMPTY_FORM });
      setSearchQuery('');
      setResults([]);
      setSelected([]);
      setSearchMode('tumulo');
    }
  }, [open]);

  function handleModeChange(mode: SearchMode) {
    setSearchMode(mode);
    setSearchQuery('');
    setResults([]);
  }

  function handleSearchChange(q: string) {
    setSearchQuery(q);
    if (debounceRef.current) clearTimeout(debounceRef.current);
    if (q.length < 1) { setResults([]); return; }

    debounceRef.current = setTimeout(async () => {
      setSearching(true);
      try {
        const urlMap: Record<SearchMode, string> = {
          tumulo:  `/cemiterio/sepulturas/search?q=${encodeURIComponent(q)}`,
          difunto: `/cemiterio/difuntos/search?q=${encodeURIComponent(q)}`,
          parente: `/cemiterio/parentes/search?q=${encodeURIComponent(q)}`,
        };
        const res = await fetch(urlMap[searchMode], {
          headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
          credentials: 'same-origin',
        });
        if (!res.ok) return;
        const json = await res.json();

        if (searchMode === 'tumulo') {
          setResults((json.data ?? []).map((s: any) => ({
            id: s.id,
            codigo: s.codigo_sepultura ?? s.codigo ?? '',
            localizacao: s.localizacao ?? '',
            tipo: s.tipo ?? '',
            status: s.status ?? '',
          })));
        } else {
          setResults(json.data ?? []);
        }
      } catch { /* ignore */ } finally {
        setSearching(false);
      }
    }, 300);
  }

  // Adiciona ao carrinho (evita duplicatas)
  function addToCart(item: SelectedItem) {
    if (selected.find((s) => s.key === item.key)) return;
    setSelected((prev) => [...prev, item]);
    setSearchQuery('');
    setResults([]);
  }

  function removeFromCart(key: string) {
    setSelected((prev) => prev.filter((s) => s.key !== key));
  }

  function selectTumulo(t: TumuloResult) {
    addToCart({
      key: `sepultura:${t.id}`,
      type: 'sepultura',
      id: t.id,
      label: t.codigo,
      sublabel: t.localizacao || t.tipo || undefined,
    });
  }

  function selectDifunto(d: DifuntoResult) {
    addToCart({
      key: `sepultado:${d.difunto_id}`,
      type: 'sepultado',
      id: d.difunto_id,
      label: d.nome,
      sublabel: d.sepultura_codigo ? `Túmulo: ${d.sepultura_codigo}` : 'Sem túmulo cadastrado',
    });
  }

  // Parente → adiciona o difunto ao carrinho
  function selectParente(p: ParenteResult) {
    addToCart({
      key: `sepultado:${p.difunto_id}`,
      type: 'sepultado',
      id: p.difunto_id,
      label: p.difunto_nome,
      sublabel: `Parente: ${p.responsavel_nome}${p.sepultura_codigo ? ` · Túmulo: ${p.sepultura_codigo}` : ''}`,
    });
  }

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    if (selected.length === 0) { notify.warning('Nenhum item', 'Adicione ao menos um túmulo ou difunto.'); return; }
    if (!form.data_vencimento) { notify.warning('Campo obrigatório', 'Informe a data de vencimento.'); return; }
    if (form.valor_cents <= 0) { notify.warning('Campo obrigatório', 'Informe o valor da cobrança.'); return; }
    if (!csrfToken) { notify.reload(); return; }

    setSubmitting(true);
    try {
      const payload = {
        items:           selected.map((s) => ({ type: s.type, id: s.id })),
        data_vencimento: form.data_vencimento,
        valor:           (form.valor_cents / 100).toFixed(2),
        descricao:       form.descricao.trim() || undefined,
      };

      const res = await fetch('/cemiterio/cobrancas', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
        body: JSON.stringify(payload),
      });

      if (!res.headers.get('content-type')?.includes('application/json')) {
        notify.error('Erro inesperado', 'Resposta inválida do servidor.');
        return;
      }

      const result = await res.json();

      if (res.ok && result.success) {
        notify.success('Cobranças lançadas!', result.message ?? 'Registradas com sucesso.');
        onOpenChange(false);
        onSaved?.();
      } else {
        notify.error('Não foi possível salvar', result.message ?? 'Verifique os dados e tente novamente.');
        if (result.errors) notify.validationErrors?.(result.errors);
      }
    } catch {
      notify.error('Erro', 'Falha na comunicação com o servidor.');
    } finally {
      setSubmitting(false);
    }
  }

  // ─── Dropdown de resultados ──────────────────────────────────────────────────

  function renderResults() {
    if (results.length === 0) return null;

    return (
      <div className="rounded-md border bg-popover shadow-md z-[999] max-h-52 overflow-y-auto">
        {searchMode === 'tumulo' && (results as TumuloResult[]).map((t) => {
          const already = !!selected.find((s) => s.key === `sepultura:${t.id}`);
          return (
            <button
              key={t.id}
              type="button"
              disabled={already}
              className={`w-full px-3 py-2 text-left text-sm flex items-start justify-between gap-2 ${already ? 'opacity-40 cursor-not-allowed' : 'hover:bg-accent'}`}
              onClick={() => selectTumulo(t)}
            >
              <span>
                <span className="font-medium">{t.codigo}</span>
                {t.localizacao && <span className="text-muted-foreground ml-2">— {t.localizacao}</span>}
                {t.tipo && <span className="text-muted-foreground ml-1 text-xs">({t.tipo})</span>}
              </span>
              {t.status && (
                <span className={`text-xs shrink-0 ${t.status === 'Disponível' ? 'text-green-600' : t.status === 'Ocupada' ? 'text-blue-600' : 'text-muted-foreground'}`}>
                  {t.status}
                </span>
              )}
            </button>
          );
        })}

        {searchMode === 'difunto' && (results as DifuntoResult[]).map((d) => {
          const already = !!selected.find((s) => s.key === `sepultado:${d.difunto_id}`);
          return (
            <button
              key={d.difunto_id}
              type="button"
              disabled={already}
              className={`w-full px-3 py-2 text-left text-sm flex items-start justify-between gap-2 ${already ? 'opacity-40 cursor-not-allowed' : 'hover:bg-accent'}`}
              onClick={() => selectDifunto(d)}
            >
              <span>
                <span className="font-medium">{d.nome}</span>
                {d.cpf && <span className="text-muted-foreground ml-2 text-xs">{d.cpf}</span>}
              </span>
              {d.sepultura_codigo
                ? <span className="text-xs shrink-0 text-blue-600 flex items-center gap-1"><Building2 className="size-3" />{d.sepultura_codigo}</span>
                : <span className="text-xs shrink-0 text-amber-500">sem túmulo</span>
              }
            </button>
          );
        })}

        {searchMode === 'parente' && (results as ParenteResult[]).map((p) => {
          const already = !!selected.find((s) => s.key === `sepultado:${p.difunto_id}`);
          return (
            <button
              key={`${p.responsavel_id}-${p.difunto_id}`}
              type="button"
              disabled={already}
              className={`w-full px-3 py-2 text-left text-sm flex flex-col gap-0.5 ${already ? 'opacity-40 cursor-not-allowed' : 'hover:bg-accent'}`}
              onClick={() => selectParente(p)}
            >
              <span className="flex items-center justify-between">
                <span className="font-medium">{p.difunto_nome}</span>
                {p.sepultura_codigo
                  ? <span className="text-xs text-blue-600 flex items-center gap-1"><Building2 className="size-3" />{p.sepultura_codigo}</span>
                  : <span className="text-xs text-amber-500">sem túmulo</span>
                }
              </span>
              <span className="text-xs text-muted-foreground flex items-center gap-1">
                <Users className="size-3" />
                Parente: {p.responsavel_nome}
                {p.responsavel_tel && <span className="ml-1">&middot; {p.responsavel_tel}</span>}
              </span>
            </button>
          );
        })}
      </div>
    );
  }

  const submitLabel = selected.length > 1
    ? `Lançar ${selected.length} Cobranças`
    : 'Lançar Cobrança';

  return (
    <Dialog open={open} onOpenChange={onOpenChange}>
      <DialogContent className="sm:max-w-lg">
        <DialogHeader className="-mx-6 -mt-6 px-6 py-4 mb-0 border-b border-border bg-muted/50 dark:bg-muted/20 rounded-t-lg">
          <DialogTitle>Lançar Cobrança</DialogTitle>
          <DialogDescription>
            Selecione um ou mais túmulos / difuntos e informe o valor e vencimento.
          </DialogDescription>
        </DialogHeader>

        <form onSubmit={handleSubmit} className="flex flex-col">
          <DialogBody className="flex flex-col gap-4 px-1 py-4 overflow-y-auto max-h-[55vh]">

          {/* ── Toggle de modo ── */}
          <div className="flex gap-1 rounded-lg bg-muted p-1">
            {(['tumulo', 'difunto', 'parente'] as SearchMode[]).map((mode) => (
              <button
                key={mode}
                type="button"
                onClick={() => handleModeChange(mode)}
                className={`flex flex-1 items-center justify-center gap-1.5 rounded-md px-2 py-1.5 text-xs font-medium transition-colors ${
                  searchMode === mode
                    ? 'bg-background text-foreground shadow-sm'
                    : 'text-muted-foreground hover:text-foreground'
                }`}
              >
                {MODE_ICONS[mode]}
                {MODE_LABELS[mode]}
              </button>
            ))}
          </div>

          {/* ── Campo de busca ── */}
          <div className="flex flex-col gap-1.5">
            <Label>
              {searchMode === 'tumulo'  ? 'Buscar túmulo' :
               searchMode === 'difunto' ? 'Buscar difunto' :
                                          'Buscar por parente / responsável'}
            </Label>
            <div className="relative">
              <Input
                placeholder={
                  searchMode === 'tumulo'  ? 'Código ou localização...' :
                  searchMode === 'difunto' ? 'Nome ou CPF...' :
                                            'Nome do parente ou responsável...'
                }
                value={searchQuery}
                onChange={(e) => handleSearchChange(e.target.value)}
                autoComplete="off"
              />
              {searching && (
                <div className="absolute right-2 top-1/2 -translate-y-1/2">
                  <Loader2 className="size-4 animate-spin text-muted-foreground" />
                </div>
              )}
            </div>
            {renderResults()}
          </div>

          {/* ── Carrinho de selecionados ── */}
          {selected.length > 0 && (
            <div className="flex flex-col gap-1.5">
              <Label className="text-xs text-muted-foreground">
                {selected.length} {selected.length === 1 ? 'item selecionado' : 'itens selecionados'}
              </Label>
              <div className="rounded-md border divide-y">
                {selected.map((s) => (
                  <div key={s.key} className="flex items-center gap-2 px-3 py-2">
                    <Badge
                      variant={s.type === 'sepultura' ? 'secondary' : 'outline'}
                      className="shrink-0 gap-1"
                    >
                      {s.type === 'sepultura'
                        ? <Building2 className="size-3" />
                        : <User className="size-3" />
                      }
                      {s.type === 'sepultura' ? 'Túmulo' : 'Difunto'}
                    </Badge>
                    <div className="flex-1 min-w-0">
                      <p className="text-sm font-medium truncate">{s.label}</p>
                      {s.sublabel && <p className="text-xs text-muted-foreground truncate">{s.sublabel}</p>}
                    </div>
                    <button
                      type="button"
                      onClick={() => removeFromCart(s.key)}
                      className="shrink-0 text-muted-foreground hover:text-destructive transition-colors"
                      aria-label="Remover"
                    >
                      <Trash2 className="size-3.5" />
                    </button>
                  </div>
                ))}
              </div>
            </div>
          )}

          {/* ── Campos comuns ── */}
          <div className="flex flex-col gap-1.5">
            <Label htmlFor="descricao">Descrição</Label>
            <Textarea
              id="descricao"
              placeholder="Ex: Taxa anual de manutenção 2026"
              value={form.descricao}
              onChange={(e) => setForm((f) => ({ ...f, descricao: e.target.value }))}
              rows={2}
            />
          </div>

          <div className="grid grid-cols-2 gap-3">
            <div className="flex flex-col gap-1.5">
              <Label htmlFor="data_vencimento">Vencimento *</Label>
              <Input
                id="data_vencimento"
                type="date"
                value={form.data_vencimento}
                onChange={(e) => setForm((f) => ({ ...f, data_vencimento: e.target.value }))}
              />
            </div>
            <div className="flex flex-col gap-1.5">
              <Label htmlFor="valor">
                Valor *{' '}
                {selected.length > 1 && (
                  <span className="text-muted-foreground font-normal">(por item)</span>
                )}
              </Label>
              <CurrencyInput
                id="valor"
                placeholder="R$ 0,00"
                value={form.valor}
                onMaskedChange={(masked) => setForm((f) => ({ ...f, valor: masked }))}
                onUnmaskedChange={(unmasked) => setForm((f) => ({ ...f, valor_cents: Number(unmasked) }))}
              />
            </div>
          </div>

          </DialogBody>
          <DialogFooter>
            <Button type="button" variant="outline" onClick={() => onOpenChange(false)} disabled={submitting}>
              <X className="size-4" />
              Cancelar
            </Button>
            <Button type="submit" disabled={submitting || selected.length === 0}>
              {submitting ? <Loader2 className="size-4 animate-spin" /> : <Check className="size-4" />}
              {submitLabel}
            </Button>
          </DialogFooter>
        </form>
      </DialogContent>
    </Dialog>
  );
}
