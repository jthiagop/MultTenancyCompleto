import { useCallback, useEffect, useMemo, useState } from 'react';
import type { LucideIcon } from 'lucide-react';
import {
  Loader2,
  ArrowLeft,
  Building2,
  List,
  ArrowDownToLine,
  ArrowUpFromLine,
  Ban,
  ChevronsDown,
  GitMerge,
  CircleCheck,
  Clock,
  CheckCircle2,
  AlertTriangle,
  Inbox,
  Sparkles,
  FileInput,
  ArrowRightLeft,
  ChevronDown,
  Link2,
  FilePenLine,
  BarChart3,
  Search,
} from 'lucide-react';
import { Card, CardContent, CardFooter, CardHeader } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { ButtonGroup } from '@/components/ui/button-group';
import { Badge } from '@/components/ui/badge';
import { cn } from '@/lib/utils';
import { toAbsoluteUrl } from '@/lib/helpers';
import { useAppData } from '@/hooks/useAppData';
import {
  useConciliacoesPendentes,
  type ConciliacoesTabKey,
  type ConciliacaoItem,
  type PossivelTransacao,
  type ConciliacaoFormOptions,
  type ConciliacaoFormEntidadeBanco,
} from '@/hooks/useConciliacoesPendentes';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
  Sheet,
  SheetBody,
  SheetContent,
  SheetFooter,
  SheetHeader,
  SheetTitle,
} from '@/components/ui/sheet';
import { notify } from '@/lib/notify';
import { ConciliacaoNovoLancamentoForm } from './conciliacao-novo-lancamento-form';
import { SheetBuscarLancamento } from './sheet-buscar-lancamento';

const BTN_VERMELHO =
  '!bg-red-600 !text-white hover:!bg-red-700 border-0 shadow-sm dark:!bg-red-600 dark:hover:!bg-red-500';

function formatBRLFromCents(amountCents: number) {
  return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(amountCents / 100);
}

function formatBRL(value: number) {
  return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(Math.abs(value));
}

function formatDataBR(iso: string | null) {
  if (!iso) return '—';
  const [y, m, d] = iso.split('-');
  if (!y || !m || !d) return iso;
  return `${d}/${m}/${y}`;
}

function progressBarClass(cor: string) {
  const m: Record<string, string> = {
    success: 'bg-emerald-500', warning: 'bg-amber-500', info: 'bg-sky-500',
    secondary: 'bg-muted-foreground', danger: 'bg-destructive', destructive: 'bg-destructive',
  };
  return m[cor] ?? 'bg-primary';
}

function badgeVariantFromCor(cor: string): 'success' | 'warning' | 'destructive' | 'outline' {
  if (cor === 'success') return 'success';
  if (cor === 'warning') return 'warning';
  if (cor === 'destructive' || cor === 'danger') return 'destructive';
  return 'outline';
}

const STATUS_CONCILIACAO_LABEL: Record<string, string> = {
  pendente: 'Pendente', ok: 'OK', conciliado: 'Conciliado', ignorado: 'Ignorado', divergente: 'Divergente',
};

function StatusConciliacaoComIcone({ status }: { status: string | null | undefined }) {
  const raw = (status ?? 'pendente').toLowerCase();
  const label = STATUS_CONCILIACAO_LABEL[raw] ?? (status ? String(status) : 'Pendente');
  let Icon: LucideIcon = Clock;
  let iconClass = 'text-sky-600 dark:text-sky-400';

  if (raw === 'ok' || raw === 'conciliado') { Icon = CheckCircle2; iconClass = 'text-emerald-600 dark:text-emerald-400'; }
  else if (raw === 'ignorado') { Icon = Ban; iconClass = 'text-muted-foreground'; }
  else if (raw === 'divergente') { Icon = AlertTriangle; iconClass = 'text-amber-600 dark:text-amber-400'; }

  return (
    <Badge variant="outline" className="text-[10px] uppercase inline-flex items-center gap-1.5 pr-2">
      <Icon className={cn('size-3.5 shrink-0', iconClass)} aria-hidden />
      {label}
    </Badge>
  );
}

// ── Dashboard de acurácia ─────────────────────────────────────────────

interface DashboardData {
  insuficiente: boolean;
  total?: number;
  total_registros?: number;
  minimo?: number;
  taxa_geral?: number;
  por_campo?: Record<string, { total: number; aceitos: number; taxa: number }>;
  por_origem?: Record<string, { total: number; aceitos: number; taxa: number }>;
  mensal?: { mes: string; total: number; aceitos: number; taxa: number }[];
}

function SuggestionDashboard() {
  const [data, setData] = useState<DashboardData | null>(null);
  const [loading, setLoading] = useState(true);
  const [open, setOpen] = useState(false);

  useEffect(() => {
    let cancelled = false;
    fetch('/conciliacao/dashboard-ia', {
      headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin',
    })
      .then((r) => { if (!r.ok) throw new Error(`HTTP ${r.status}`); return r.json(); })
      .then((json) => { if (!cancelled && json.success) setData(json.data); })
      .catch(() => {})
      .finally(() => { if (!cancelled) setLoading(false); });
    return () => { cancelled = true; };
  }, []);

  if (loading || !data || data.insuficiente) return null;

  const taxaCor = (data.taxa_geral ?? 0) >= 80 ? 'text-emerald-600' : (data.taxa_geral ?? 0) >= 60 ? 'text-amber-600' : 'text-destructive';

  return (
    <Card className="border-border/60">
      <button
        type="button"
        className="flex w-full items-center justify-between gap-2 px-5 py-3 text-left text-sm transition-colors hover:bg-muted/20"
        onClick={() => setOpen(!open)}
      >
        <span className="inline-flex items-center gap-2 font-medium">
          <BarChart3 className="size-4 text-primary opacity-80" aria-hidden />
          Inteligência da IA
          <span className={cn('text-sm font-bold tabular-nums', taxaCor)}>{data.taxa_geral}%</span>
          <span className="text-xs text-muted-foreground">de acerto</span>
        </span>
        <ChevronDown className={cn('size-4 text-muted-foreground transition-transform', open && 'rotate-180')} aria-hidden />
      </button>
      {open && (
        <CardContent className="border-t px-5 pb-5 pt-4 space-y-4">
          <div className="grid gap-4 sm:grid-cols-3">
            <div className="rounded-lg border p-3 text-center">
              <p className="text-2xl font-bold tabular-nums">{data.total}</p>
              <p className="text-xs text-muted-foreground">feedbacks registrados</p>
            </div>
            <div className="rounded-lg border p-3 text-center">
              <p className={cn('text-2xl font-bold tabular-nums', taxaCor)}>{data.taxa_geral}%</p>
              <p className="text-xs text-muted-foreground">taxa de acerto geral</p>
            </div>
            <div className="rounded-lg border p-3 text-center">
              <p className="text-2xl font-bold tabular-nums">{data.mensal?.length ?? 0}</p>
              <p className="text-xs text-muted-foreground">meses com dados</p>
            </div>
          </div>

          {data.por_campo && Object.keys(data.por_campo).length > 0 && (
            <div>
              <p className="text-xs font-semibold text-muted-foreground uppercase mb-2">Acerto por campo</p>
              <div className="space-y-1.5">
                {Object.entries(data.por_campo).map(([campo, info]) => (
                  <div key={campo} className="flex items-center gap-2 text-sm">
                    <span className="w-40 truncate text-muted-foreground capitalize">{campo.replace(/_/g, ' ')}</span>
                    <div className="flex-1 h-1.5 rounded-full bg-muted overflow-hidden">
                      <div className={cn('h-full rounded-full', info.taxa >= 80 ? 'bg-emerald-500' : info.taxa >= 60 ? 'bg-amber-500' : 'bg-destructive')} style={{ width: `${info.taxa}%` }} />
                    </div>
                    <span className="w-12 text-right tabular-nums font-medium">{info.taxa}%</span>
                  </div>
                ))}
              </div>
            </div>
          )}

          {data.mensal && data.mensal.length > 0 && (
            <div>
              <p className="text-xs font-semibold text-muted-foreground uppercase mb-2">Evolução mensal</p>
              <div className="flex items-end gap-1 h-16">
                {data.mensal.map((m) => (
                  <div key={m.mes} className="flex-1 flex flex-col items-center gap-0.5" title={`${m.mes}: ${m.taxa}%`}>
                    <div className="w-full rounded-sm bg-primary/80" style={{ height: `${Math.max(4, (m.taxa / 100) * 56)}px` }} />
                    <span className="text-[9px] text-muted-foreground tabular-nums">{m.mes.slice(5)}</span>
                  </div>
                ))}
              </div>
            </div>
          )}
        </CardContent>
      )}
    </Card>
  );
}

// ── Tab principal ─────────────────────────────────────────────────────

interface ConciliacoesPendentesTabProps {
  entidadeId: string | undefined;
}

export function ConciliacoesPendentesTab({ entidadeId }: ConciliacoesPendentesTabProps) {
  const { csrfToken } = useAppData();
  const {
    tab, setTab, items, entidade, counts, pagination, formOptions,
    loading, loadingMore, error, refresh, loadMore,
  } = useConciliacoesPendentes(entidadeId);

  // ── Bulk selection ──────────────────────────────────────────────────
  const [selectedIds, setSelectedIds] = useState<Set<number>>(new Set());
  const [bulkLoading, setBulkLoading] = useState(false);

  useEffect(() => { setSelectedIds(new Set()); }, [tab, entidadeId]);

  const selectableItems = useMemo(() => {
    return items.filter((r) => {
      const sug = r.sugestao;
      const confianca = sug && typeof sug === 'object' && !Array.isArray(sug) && typeof (sug as Record<string, unknown>).confianca === 'number'
        ? ((sug as Record<string, unknown>).confianca as number) : 0;
      return r.possiveis_transacoes.length > 0 || confianca >= 50;
    });
  }, [items]);

  const allSelected = selectableItems.length > 0 && selectableItems.every((r) => selectedIds.has(r.statement.id));

  function toggleSelect(id: number) {
    setSelectedIds((prev) => {
      const next = new Set(prev);
      if (next.has(id)) next.delete(id); else next.add(id);
      return next;
    });
  }

  function toggleSelectAll() {
    if (allSelected) {
      setSelectedIds(new Set());
    } else {
      setSelectedIds(new Set(selectableItems.map((r) => r.statement.id)));
    }
  }

  const handleBulkConciliar = useCallback(async () => {
    if (!csrfToken || selectedIds.size === 0) return;
    setBulkLoading(true);
    const itens = Array.from(selectedIds).map((bsId) => {
      const row = items.find((r) => r.statement.id === bsId);
      if (row && row.possiveis_transacoes.length > 0) {
        return { bank_statement_id: bsId, mode: 'match' as const, transacao_id: row.possiveis_transacoes[0].id };
      }
      return { bank_statement_id: bsId, mode: 'sugestao' as const };
    });
    try {
      const res = await fetch('/conciliacao/conciliar-lote', {
        method: 'POST',
        headers: { Accept: 'application/json', 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ itens }),
        credentials: 'same-origin',
      });
      const json = await res.json();
      if (json.success) {
        notify.success('Conciliação em lote', json.message);
        setSelectedIds(new Set());
        await refresh();
      } else {
        notify.error('Erro no lote', json.message ?? 'Verifique os dados.');
      }
    } catch { notify.networkError(); }
    finally { setBulkLoading(false); }
  }, [csrfToken, selectedIds, items, refresh]);

  const handleBulkIgnorar = useCallback(async () => {
    if (!csrfToken || selectedIds.size === 0) return;
    setBulkLoading(true);
    try {
      const res = await fetch('/conciliacao/ignorar-lote', {
        method: 'POST',
        headers: { Accept: 'application/json', 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ ids: Array.from(selectedIds) }),
        credentials: 'same-origin',
      });
      const json = await res.json();
      if (json.success) {
        notify.success('Ignorados em lote', json.message);
        setSelectedIds(new Set());
        await refresh();
      } else {
        notify.error('Erro', json.message ?? 'Tente novamente.');
      }
    } catch { notify.networkError(); }
    finally { setBulkLoading(false); }
  }, [csrfToken, selectedIds, refresh]);

  // ── Handlers individuais ────────────────────────────────────────────

  async function handleIgnorar(statementId: number) {
    if (!csrfToken) { notify.reload(); return; }
    try {
      const res = await fetch(`/conciliacao/${statementId}/ignorar`, {
        method: 'PATCH',
        headers: { Accept: 'application/json', 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken },
        credentials: 'same-origin',
      });
      const json = (await res.json()) as { success?: boolean; message?: string };
      if (!res.ok || !json.success) { notify.error('Não foi possível ignorar', json.message ?? 'Tente novamente.'); return; }
      notify.success('Lançamento ignorado', json.message ?? 'O extrato foi marcado como ignorado.');
      await refresh();
    } catch { notify.networkError(() => handleIgnorar(statementId)); }
  }

  async function handleConciliar(statementId: number, t: PossivelTransacao) {
    if (!csrfToken) { notify.reload(); return; }
    try {
      const body = new FormData();
      body.append('bank_statement_id', String(statementId));
      body.append('transacao_financeira_id', String(t.id));
      body.append('valor_conciliado', String(t.valor));
      const res = await fetch('/conciliacao', {
        method: 'POST',
        headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken },
        body, credentials: 'same-origin',
      });
      const json = (await res.json()) as { success?: boolean; message?: string };
      if (!res.ok || !json.success) { notify.error('Conciliação não realizada', json.message ?? 'Verifique os dados.'); return; }
      notify.success('Conciliado!', json.message ?? 'Lançamento vinculado ao extrato.');
      await refresh();
    } catch { notify.networkError(() => handleConciliar(statementId, t)); }
  }

  const tabs: { key: ConciliacoesTabKey; label: string; countKey: keyof NonNullable<typeof counts>; Icon: LucideIcon }[] = [
    { key: 'all', label: 'Todos', countKey: 'all', Icon: List },
    { key: 'received', label: 'Recebimentos', countKey: 'received', Icon: ArrowDownToLine },
    { key: 'paid', label: 'Pagamentos', countKey: 'paid', Icon: ArrowUpFromLine },
  ];

  return (
    <div className="space-y-6">
      {/* Dashboard IA */}
      <SuggestionDashboard />

      {/* Cabeçalho */}
      {entidade && (
        <div className="grid grid-cols-1 gap-6 md:grid-cols-2 border border-border rounded-xl p-4 bg-card/50">
          <div className="flex items-start gap-4">
            <div className="size-11 rounded-lg overflow-hidden bg-muted flex items-center justify-center shrink-0 border border-border">
              {entidade.logo_url ? (<img src={entidade.logo_url} alt="" className="size-full object-contain p-1" />) : (<Building2 className="size-5 text-muted-foreground" />)}
            </div>
            <div>
              <p className="text-xs text-muted-foreground font-medium">Lançamentos importados</p>
              <p className="text-base font-semibold text-foreground">{entidade.nome}</p>
            </div>
          </div>
          <div className="flex items-start gap-4 md:justify-end">
            <div className="size-11 rounded-full overflow-hidden bg-muted shrink-0 border border-border hidden sm:block">
              <img src={toAbsoluteUrl('/tenancy/assets/media/app/mini-logo.svg')} alt="" className="size-full object-contain p-1" />
            </div>
            <div className="text-left md:text-right">
              <p className="text-xs text-muted-foreground font-medium">Lançamentos a cadastrar</p>
              <p className="text-base font-semibold text-foreground">Dominus Sistema</p>
            </div>
          </div>
        </div>
      )}

      {/* Sub-abas + selecionar tudo */}
      <div className="flex flex-wrap items-center gap-2">
        {tabs.map((t) => (
          <Button key={t.key} variant={tab === t.key ? 'primary' : 'outline'} size="sm" onClick={() => setTab(t.key)}>
            <t.Icon className="size-3.5 shrink-0 opacity-90" aria-hidden />
            {t.label}
            {counts != null && (
              <Badge variant="secondary" className="ml-1.5 rounded-sm px-1.5 py-0 text-[10px]">{counts[t.countKey] ?? 0}</Badge>
            )}
          </Button>
        ))}
        {selectableItems.length > 0 && (
          <label className="ml-auto flex items-center gap-2 text-sm text-muted-foreground cursor-pointer select-none">
            <input type="checkbox" checked={allSelected} onChange={toggleSelectAll} className="rounded border-input" />
            Selecionar todas
          </label>
        )}
      </div>

      {error && (<div className="rounded-lg border border-destructive/30 bg-destructive/5 px-4 py-3 text-sm text-destructive">{error}</div>)}

      {loading && (
        <div className="flex items-center justify-center gap-2 py-16 text-muted-foreground">
          <Loader2 className="size-5 animate-spin shrink-0" aria-hidden />
          Carregando conciliações…
        </div>
      )}

      {!loading && !error && items.length === 0 && (
        <Card className="border-sky-200 bg-sky-50/50 dark:bg-sky-950/20 dark:border-sky-900">
          <CardContent className="flex gap-4 p-6">
            <div className="mt-0.5 rounded-full bg-emerald-500/15 p-2.5 text-emerald-600 dark:text-emerald-400" aria-hidden>
              <CircleCheck className="size-6" />
            </div>
            <div>
              <p className="font-semibold text-foreground inline-flex items-center gap-2">
                <Inbox className="size-4 text-sky-600 dark:text-sky-400 shrink-0" aria-hidden /> Nenhuma conciliação pendente
              </p>
              <p className="text-sm text-muted-foreground mt-1">
                {tab === 'received' && 'Não há recebimentos pendentes de conciliação.'}
                {tab === 'paid' && 'Não há pagamentos pendentes de conciliação.'}
                {tab === 'all' && 'Todas as conciliações foram processadas.'}
              </p>
            </div>
          </CardContent>
        </Card>
      )}

      {!loading && items.map((row) => {
        const isSelectable = selectableItems.includes(row);
        return (
          <ConciliacaoRow
            key={row.statement.id}
            row={row}
            entidadeId={entidadeId}
            formOptions={formOptions}
            csrfToken={csrfToken ?? ''}
            onIgnorar={handleIgnorar}
            onConciliar={handleConciliar}
            onRefresh={refresh}
            selected={selectedIds.has(row.statement.id)}
            selectable={isSelectable}
            onToggleSelect={() => toggleSelect(row.statement.id)}
            entidadeLogoUrl={entidade?.logo_url}
            entidadeNome={entidade?.nome}
          />
        );
      })}

      {!loading && pagination?.has_more && (
        <div className="flex justify-center pt-2">
          <Button variant="outline" size="sm" onClick={() => loadMore()} disabled={loadingMore}>
            {loadingMore ? (<Loader2 className="size-4 animate-spin mr-1.5" aria-hidden />) : (<ChevronsDown className="size-4 mr-1.5 opacity-90" aria-hidden />)}
            Carregar mais
          </Button>
        </div>
      )}

      {/* Barra de ações em lote */}
      {selectedIds.size > 0 && (
        <div className="fixed bottom-4 left-1/2 z-50 -translate-x-1/2 rounded-xl border bg-card px-5 py-3 shadow-lg flex items-center gap-3">
          <span className="text-sm font-medium">{selectedIds.size} selecionada(s)</span>
          <Button variant="primary" size="sm" onClick={handleBulkConciliar} disabled={bulkLoading}>
            {bulkLoading ? <Loader2 className="size-3.5 animate-spin mr-1" aria-hidden /> : <GitMerge className="size-3.5 mr-1" aria-hidden />}
            Conciliar
          </Button>
          <Button variant="outline" size="sm" className={BTN_VERMELHO} onClick={handleBulkIgnorar} disabled={bulkLoading}>
            <Ban className="size-3.5 mr-1" aria-hidden />
            Ignorar
          </Button>
          <Button variant="outline" size="sm" onClick={() => setSelectedIds(new Set())}>Limpar</Button>
        </div>
      )}
    </div>
  );
}

// ── Barra de tabs reutilizável ────────────────────────────────────────

function ActionTabBar({
  rightMode, setRightMode, canNewLanc, hasMatches, onTransferencia, onBuscar,
}: {
  rightMode: 'match' | 'novo';
  setRightMode: (m: 'match' | 'novo') => void;
  canNewLanc: boolean;
  hasMatches: boolean;
  onTransferencia: () => void;
  onBuscar: () => void;
}) {
  return (
    <div className="flex items-center justify-between gap-3">
      <ButtonGroup>
        {canNewLanc && (
          <Button
            variant={rightMode === 'novo' ? 'primary' : 'outline'}
            size="sm"
            onClick={() => setRightMode('novo')}
            className="text-xs"
          >
            <FilePenLine className="size-3.5 mr-1" aria-hidden /> Novo lançamento
          </Button>
        )}
        <Button variant="outline" size="sm" onClick={onTransferencia} className="text-xs">
          <ArrowRightLeft className="size-3.5 mr-1" aria-hidden /> Nova transferência
        </Button>
        {hasMatches && (
          <Button
            variant={rightMode === 'match' ? 'primary' : 'outline'}
            size="sm"
            onClick={() => setRightMode('match')}
            className="text-xs"
          >
            <Link2 className="size-3.5 mr-1" aria-hidden /> Vincular
          </Button>
        )}
      </ButtonGroup>
      <button
        type="button"
        onClick={onBuscar}
        className="inline-flex items-center gap-1.5 text-xs font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors"
      >
        <Search className="size-3.5" aria-hidden /> Buscar lançamento
      </button>
    </div>
  );
}

// ── Linha individual ──────────────────────────────────────────────────

function ConciliacaoRow({
  row, entidadeId, formOptions, csrfToken,
  onIgnorar, onConciliar, onRefresh,
  selected, selectable, onToggleSelect,
  entidadeLogoUrl, entidadeNome,
}: {
  row: ConciliacaoItem;
  entidadeId: string | undefined;
  formOptions: ConciliacaoFormOptions | null;
  csrfToken: string;
  onIgnorar: (id: number) => void;
  onConciliar: (statementId: number, t: PossivelTransacao) => void;
  onRefresh: () => Promise<void>;
  selected: boolean;
  selectable: boolean;
  onToggleSelect: () => void;
  entidadeLogoUrl?: string | null;
  entidadeNome?: string | null;
}) {
  const s = row.statement;
  const primeira = row.possiveis_transacoes[0];
  const neg = s.amount_cents < 0;
  const [novoLancSubmitting, setNovoLancSubmitting] = useState(false);
  const formNovoId = `conciliar-novo-${s.id}`;
  const hasMatches = row.possiveis_transacoes.length > 0;

  const entidadeIdNum = entidadeId ? Number.parseInt(entidadeId, 10) : NaN;
  const canNewLanc = formOptions != null && !Number.isNaN(entidadeIdNum) && entidadeIdNum > 0;
  const [rightMode, setRightMode] = useState<'match' | 'novo'>(hasMatches ? 'match' : 'novo');

  const [sheetTransfOpen, setSheetTransfOpen] = useState(false);
  const [sheetBuscarOpen, setSheetBuscarOpen] = useState(false);

  const mov = s.movimentacao_interna && typeof s.movimentacao_interna === 'object'
    ? (s.movimentacao_interna as { cor?: string; acao_label?: string; icone?: string }) : null;

  const [showExtras, setShowExtras] = useState(false);
  const extraMatches = row.possiveis_transacoes.slice(1);

  return (
    <div className={cn('grid grid-cols-1 gap-4 xl:grid-cols-[minmax(0,1fr)_auto_minmax(0,1fr)] xl:items-stretch xl:gap-6', selected && 'ring-2 ring-primary/40 rounded-xl')}>
      {/* Statement card (banco) */}
      <div className="min-w-0">
        <Card className="h-full min-h-[200px] border-2 border-border overflow-hidden">
          <CardHeader className="pb-3 px-5 pt-5 bg-muted/30">
            <div className="flex min-w-0 flex-nowrap items-center gap-x-3">
              {selectable && (
                <input type="checkbox" checked={selected} onChange={onToggleSelect} className="rounded border-input shrink-0" />
              )}
              <p className={cn('text-lg font-semibold tabular-nums shrink-0', neg ? 'text-destructive' : 'text-emerald-600')}>
                {formatBRLFromCents(s.amount_cents)}
              </p>
              <time className="text-sm font-medium text-foreground tabular-nums whitespace-nowrap" dateTime={s.dtposted ?? undefined}>
                {formatDataBR(s.dtposted)}
              </time>
            </div>
          </CardHeader>
          <CardContent className="px-5 pb-5 pt-4 space-y-3">
            <p className="text-sm text-foreground">
              {s.memo ?? 'Sem descrição'}
              {s.checknum ? (<Badge variant="outline" className="ml-2 align-middle text-xs">{s.checknum}</Badge>) : null}
            </p>
            {mov?.acao_label && (<Badge variant="secondary" className="text-xs">{mov.acao_label}</Badge>)}
            <div><StatusConciliacaoComIcone status={s.status_conciliacao} /></div>
          </CardContent>
          <CardFooter className="flex flex-wrap items-center justify-between gap-2 border-t bg-muted/20 px-5 py-4 text-xs text-muted-foreground">
            <span className="flex items-center gap-1"><FileInput className="size-3.5 shrink-0 opacity-80" aria-hidden /> Importado via OFX</span>
            <Button type="button" variant="primary" size="sm" className={BTN_VERMELHO} onClick={() => onIgnorar(s.id)}>
              <Ban className="size-3.5 shrink-0 opacity-95" aria-hidden /> Ignorar
            </Button>
          </CardFooter>
        </Card>
      </div>

      {/* Centro: botão conciliar */}
      <div className="flex shrink-0 justify-center items-center py-2 xl:px-1 xl:w-auto min-h-[48px]">
        {rightMode === 'match' && hasMatches && primeira ? (
          <Button type="button" variant="primary" size="sm" className="w-full max-w-[150px]" onClick={() => onConciliar(s.id, primeira)}>
            <Link2 className="size-3.5 mr-1.5 shrink-0 opacity-95" aria-hidden /> Vincular
          </Button>
        ) : rightMode === 'novo' && canNewLanc ? (
          <Button type="submit" form={formNovoId} variant="primary" size="sm" className="w-full max-w-[150px]" disabled={novoLancSubmitting}>
            {novoLancSubmitting ? (<Loader2 className="size-3.5 mr-1.5 animate-spin shrink-0" aria-hidden />) : (<GitMerge className="size-3.5 mr-1.5 shrink-0 opacity-95" aria-hidden />)}
            Conciliar
          </Button>
        ) : (
          <Button type="button" size="sm" variant="primary" className="w-full max-w-[150px]" disabled>
            <GitMerge className="size-3.5 mr-1.5 shrink-0 opacity-70" aria-hidden /> Conciliar
          </Button>
        )}
      </div>

      {/* Direita: conteúdo com tabs no header */}
      <div className="min-w-0">
        {rightMode === 'match' && hasMatches && primeira ? (
          <Card className="h-full min-h-[200px] border-amber-200/80 dark:border-amber-900/50">
            <CardHeader className="px-3 pt-3 pb-0">
              <ActionTabBar
                rightMode={rightMode}
                setRightMode={setRightMode}
                canNewLanc={canNewLanc}
                hasMatches={hasMatches}
                onTransferencia={() => setSheetTransfOpen(true)}
                onBuscar={() => setSheetBuscarOpen(true)}
              />
            </CardHeader>
            <CardContent className="space-y-4 px-5 pb-5 pt-4">
              <div className="flex gap-2 rounded-md border-l-4 border-amber-500 bg-amber-50 dark:bg-amber-950/30 px-3 py-2 text-sm text-foreground">
                <Sparkles className="size-4 shrink-0 text-amber-600 dark:text-amber-400 mt-0.5" aria-hidden />
                <strong>Lançamento correspondente encontrado</strong>
              </div>

              <MatchCard t={primeira} />

              {extraMatches.length > 0 && (
                <div>
                  <button
                    type="button"
                    className="flex items-center gap-1 text-xs text-muted-foreground hover:text-foreground transition-colors"
                    onClick={() => setShowExtras(!showExtras)}
                  >
                    <ChevronDown className={cn('size-3 transition-transform', showExtras && 'rotate-180')} />
                    {showExtras ? 'Ocultar' : `Ver mais ${extraMatches.length} opção(ões)`}
                  </button>
                  {showExtras && (
                    <div className="mt-2 space-y-3 border-t pt-3">
                      {extraMatches.map((t) => (
                        <div key={t.id} className="rounded-md border p-3">
                          <MatchCard t={t} compact />
                        </div>
                      ))}
                    </div>
                  )}
                </div>
              )}
            </CardContent>
          </Card>
        ) : rightMode === 'novo' && canNewLanc && formOptions ? (
          <ConciliacaoNovoLancamentoForm
            row={row}
            entidadeId={entidadeIdNum}
            formOptions={formOptions}
            formId={formNovoId}
            csrfToken={csrfToken}
            onSuccess={onRefresh}
            setSubmitting={setNovoLancSubmitting}
            headerExtra={
              <ActionTabBar
                rightMode={rightMode}
                setRightMode={setRightMode}
                canNewLanc={canNewLanc}
                hasMatches={hasMatches}
                onTransferencia={() => setSheetTransfOpen(true)}
                onBuscar={() => setSheetBuscarOpen(true)}
              />
            }
          />
        ) : (
          <Card className="h-full min-h-[200px] border-dashed">
            <CardHeader className="px-3 pt-3 pb-0">
              <ActionTabBar
                rightMode={rightMode}
                setRightMode={setRightMode}
                canNewLanc={canNewLanc}
                hasMatches={hasMatches}
                onTransferencia={() => setSheetTransfOpen(true)}
                onBuscar={() => setSheetBuscarOpen(true)}
              />
            </CardHeader>
            <CardContent className="px-5 pb-5 pt-4">
              <p className="text-sm text-muted-foreground">
                Não há sugestão automática. Carregue as opções do formulário ou abra esta entidade no painel completo.
              </p>
              {mov && (
                <div className="mt-3 rounded-md border border-amber-500/40 bg-amber-500/5 p-3 text-sm text-foreground">
                  <p className="font-medium inline-flex items-center gap-2">
                    <ArrowRightLeft className="size-4 text-amber-600 dark:text-amber-400 shrink-0" aria-hidden /> Possível movimentação interna
                  </p>
                  <p className="text-muted-foreground mt-1 text-xs">
                    {mov.acao_label ? `${mov.acao_label} — se for transferência entre contas, use o fluxo no painel Blade.` : 'Se for transferência entre contas, use o fluxo de transferência no painel Blade.'}
                  </p>
                </div>
              )}
            </CardContent>
          </Card>
        )}
      </div>

      {/* ── Sheet: Nova Transferência ──────────────── */}
      <SheetNovaTransferencia
        open={sheetTransfOpen}
        onOpenChange={setSheetTransfOpen}
        statement={s}
        entidadeId={entidadeIdNum}
        entidadesBanco={formOptions?.entidades_banco ?? []}
        csrfToken={csrfToken}
        onSuccess={onRefresh}
      />

      {/* ── Sheet: Buscar Lançamento ──────────────── */}
      <SheetBuscarLancamento
        open={sheetBuscarOpen}
        onOpenChange={setSheetBuscarOpen}
        statement={s}
        entidadeId={entidadeIdNum}
        entidadeLogoUrl={entidadeLogoUrl}
        entidadeNome={entidadeNome}
        csrfToken={csrfToken}
        onSuccess={onRefresh}
      />
    </div>
  );
}

// ── Sheet: Nova Transferência ──────────────────────────────────────────

function EntidadeDestinoSelectRow({ e }: { e: ConciliacaoFormEntidadeBanco }) {
  const logoSrc = e.logo_url ? toAbsoluteUrl(e.logo_url) : null;
  return (
    <div className="flex w-full max-w-full items-center gap-2.5 py-0.5">
      <div className="flex size-8 shrink-0 items-center justify-center overflow-hidden rounded-md border border-border bg-muted/40">
        {logoSrc ? (
          <img src={logoSrc} alt="" className="max-h-full max-w-full object-contain p-0.5" />
        ) : (
          <Building2 className="size-4 text-muted-foreground" aria-hidden />
        )}
      </div>
      <div className="flex min-w-0 flex-1 flex-col gap-0.5 text-start">
        <span className="truncate font-medium leading-tight">{e.nome}</span>
        <div className="flex flex-wrap items-center gap-1.5">
          {e.conta ? (
            <span className="text-xs tabular-nums text-muted-foreground">Nº {e.conta}</span>
          ) : null}
          {e.account_type_label ? (
            <Badge variant="outline" className="h-5 px-1.5 text-[10px] font-normal">
              {e.account_type_label}
            </Badge>
          ) : null}
        </div>
      </div>
    </div>
  );
}

function SheetNovaTransferencia({
  open, onOpenChange, statement: s, entidadeId, entidadesBanco, csrfToken, onSuccess,
}: {
  open: boolean;
  onOpenChange: (v: boolean) => void;
  statement: { id: number; dtposted: string | null; amount_cents: number; memo: string | null; checknum: string | null };
  entidadeId: number;
  entidadesBanco: ConciliacaoFormEntidadeBanco[];
  csrfToken: string;
  onSuccess: () => Promise<void>;
}) {
  const [destinoId, setDestinoId] = useState('');
  const [descricao, setDescricao] = useState('');
  const [submitting, setSubmitting] = useState(false);

  const destinos = useMemo(
    () => entidadesBanco.filter((e) => e.id !== entidadeId),
    [entidadesBanco, entidadeId],
  );

  const destinoSelecionado = useMemo(
    () => destinos.find((d) => String(d.id) === destinoId),
    [destinos, destinoId],
  );

  async function handleSubmit() {
    if (!destinoId || submitting) return;
    setSubmitting(true);
    try {
      const res = await fetch('/conciliacao/transferir', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, Accept: 'application/json' },
        body: JSON.stringify({
          bank_statement_id: s.id,
          entidade_origem_id: entidadeId,
          entidade_destino_id: Number(destinoId),
          valor: Math.abs(s.amount_cents / 100),
          data_transferencia: s.dtposted,
          descricao: descricao || null,
          checknum: s.checknum || null,
        }),
      });
      const json = await res.json();
      if (!res.ok || !json.success) { notify.error('Erro ao transferir', json.message ?? 'Verifique os dados.'); return; }
      notify.success('Transferência registrada!', json.message ?? '');
      onOpenChange(false);
      await onSuccess();
    } catch {
      notify.networkError();
    } finally {
      setSubmitting(false);
    }
  }

  return (
    <Sheet open={open} onOpenChange={onOpenChange}>
      <SheetContent side="right" className="w-full sm:max-w-md flex flex-col gap-0 p-0" aria-describedby={undefined}>
        <SheetHeader className="px-5 py-4 border-b border-border space-y-0">
          <div className="flex items-center gap-2">
            <Button
              type="button"
              variant="ghost"
              size="icon"
              className="size-8 shrink-0 text-primary"
              onClick={() => onOpenChange(false)}
              aria-label="Voltar"
            >
              <ArrowLeft className="size-5" />
            </Button>
            <SheetTitle className="text-base font-semibold text-start leading-tight">
              Nova transferência entre contas
            </SheetTitle>
          </div>
        </SheetHeader>

        <SheetBody className="flex-1 overflow-y-auto px-5 py-4 space-y-5">
          <div className="rounded-lg border border-border bg-muted/30 px-4 py-3 text-sm">
            <div className="flex justify-between gap-3 items-start">
              <div>
                <p className="text-xs text-muted-foreground">Dados do extrato</p>
                <p className="font-medium text-foreground mt-0.5">{formatDataBR(s.dtposted)}</p>
                {s.memo && (
                  <p className="text-xs text-muted-foreground mt-1 truncate max-w-45">{s.memo}</p>
                )}
              </div>
              <div className="text-end shrink-0">
                <p className="text-xs text-muted-foreground">Valor</p>
                <p className={cn('text-sm font-semibold tabular-nums', s.amount_cents < 0 ? 'text-destructive' : 'text-emerald-600')}>
                  {formatBRLFromCents(s.amount_cents)}
                </p>
              </div>
            </div>
          </div>

          <div className="space-y-2">
            <Label className="text-xs">
              Conta de destino <span className="text-destructive">*</span>
            </Label>
            <Select
              value={destinoId || undefined}
              onValueChange={(v) => setDestinoId(v ?? '')}
            >
              <SelectTrigger size="lg" className="h-auto min-h-11 w-full py-2 text-left">
                {destinoSelecionado ? (
                  <span className="flex w-full min-w-0 flex-1">
                    <EntidadeDestinoSelectRow e={destinoSelecionado} />
                  </span>
                ) : (
                  <SelectValue placeholder="Selecione a conta de destino" />
                )}
              </SelectTrigger>
              <SelectContent position="popper" className="max-w-[min(100vw-2rem,28rem)]">
                {destinos.map((e) => (
                  <SelectItem key={e.id} value={String(e.id)} className="cursor-pointer py-2">
                    <EntidadeDestinoSelectRow e={e} />
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>

          <div className="space-y-2">
            <Label className="text-xs">
              Descrição <span className="text-muted-foreground">(opcional)</span>
            </Label>
            <Input
              value={descricao}
              onChange={(e) => setDescricao(e.target.value)}
              placeholder="Ex: Aplicação automática Rende Fácil"
              disabled={submitting}
            />
          </div>
        </SheetBody>

        <SheetFooter className="px-5 py-4 border-t border-border flex-row justify-between gap-2 sm:justify-between">
          <Button type="button" variant="outline" onClick={() => onOpenChange(false)} disabled={submitting}>
            Cancelar
          </Button>
          <Button
            type="button"
            className="inline-flex gap-2"
            onClick={() => void handleSubmit()}
            disabled={!destinoId || submitting}
          >
            {submitting ? (
              <>
                <Loader2 className="size-4 animate-spin shrink-0" />
                Transferindo…
              </>
            ) : (
              'Transferir'
            )}
          </Button>
        </SheetFooter>
      </SheetContent>
    </Sheet>
  );
}

function MatchCard({ t, compact }: { t: PossivelTransacao; compact?: boolean }) {
  return (
    <>
      <div className="flex flex-wrap items-start justify-between gap-2">
        <div>
          <p className={cn('font-medium tabular-nums', compact && 'text-sm')}>{formatDataBR(t.data_competencia)}</p>
          <p className={cn('text-sm', t.tipo === 'entrada' ? 'text-emerald-600' : 'text-destructive')}>
            {t.tipo === 'entrada' ? 'Receita' : t.tipo === 'saida' ? 'Despesa' : t.tipo}
          </p>
        </div>
        <p className={cn('font-bold tabular-nums', compact ? 'text-base' : 'text-lg', t.tipo === 'entrada' ? 'text-emerald-600' : 'text-destructive')}>
          {formatBRL(t.valor)}
        </p>
      </div>
      <p className="text-sm"><span className="font-semibold">Descrição:</span> {t.descricao ?? '—'}</p>
      <div className="space-y-2">
        <div className="flex items-center gap-2">
          <span className="text-sm font-medium tabular-nums">{t.match_score}%</span>
          <Badge variant={badgeVariantFromCor(t.match_classificacao.cor)} appearance="light" className="text-xs">
            {t.match_classificacao.texto}
          </Badge>
        </div>
        {!compact && (
          <div className="h-1.5 w-full max-w-full sm:max-w-[320px] rounded-full bg-muted overflow-hidden">
            <div className={cn('h-full rounded-full transition-all', progressBarClass(t.match_classificacao.cor))} style={{ width: `${Math.min(100, t.match_score)}%` }} />
          </div>
        )}
      </div>
    </>
  );
}
