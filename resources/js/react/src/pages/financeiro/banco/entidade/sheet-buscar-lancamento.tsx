import { useCallback, useEffect, useMemo, useRef, useState } from 'react';
import {
  Loader2,
  Search,
  Link2,
  Landmark,
} from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Checkbox } from '@/components/ui/checkbox';
import {
  Sheet,
  SheetBody,
  SheetContent,
  SheetHeader,
  SheetTitle,
} from '@/components/ui/sheet';
import { cn } from '@/lib/utils';
import { Tooltip, TooltipContent, TooltipTrigger } from '@/components/ui/tooltip';
import { notify } from '@/lib/notify';
import { defaultPeriod, type PeriodValue } from '@/components/ui/period-picker';
import { type TransacaoAdvancedFiltersState } from '@/hooks/useTransacoes';
import {
  TransacaoAdvancedFiltersChipsSection,
  TransacaoAdvancedFiltersScope,
  TransacaoAdvancedFiltersTrigger,
} from '@/pages/financeiro/components/transacao-advanced-filters-bar';
import { TransacaoFiltersRow } from '@/pages/financeiro/components/transacao-filters-row';

function formatBRLFromCents(amountCents: number) {
  return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(amountCents / 100);
}

function formatBRL(value: number) {
  return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(Math.abs(value));
}

function formatDataBR(iso: string | null | undefined) {
  if (!iso) return '—';
  const d = new Date(iso + 'T00:00:00');
  return d.toLocaleDateString('pt-BR');
}

const SITUACAO_MAP: Record<string, { label: string; class: string }> = {
  em_aberto:  { label: 'Em aberto',    class: 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-400' },
  pago:       { label: 'Pago',         class: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400' },
  recebido:   { label: 'Recebido',     class: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400' },
  atrasado:   { label: 'Atrasado',     class: 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400' },
  pago_parcial: { label: 'Pago parcial', class: 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400' },
  previsto:   { label: 'Previsto',     class: 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400' },
  desconsiderado: { label: 'Desconsiderado', class: 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-500' },
};

export interface BuscarLancamentoStatement {
  id: number;
  dtposted: string | null;
  amount_cents: number;
  amount?: number;
  memo: string | null;
  checknum?: string | null;
}

export interface BuscarLancamentoResult {
  id: number;
  data_competencia: string | null;
  data_vencimento: string | null;
  data_pagamento: string | null;
  tipo: string;
  valor: number;
  valor_pago: number;
  descricao: string | null;
  parceiro_nome?: string | null;
  conta_nome?: string | null;
  categoria?: string | null;
  situacao?: string | null;
  tipo_documento?: string | null;
  numero_documento?: string | null;
  conciliado?: boolean;
}

interface SheetBuscarLancamentoProps {
  open: boolean;
  onOpenChange: (v: boolean) => void;
  statement: BuscarLancamentoStatement;
  entidadeId: number;
  entidadeLogoUrl?: string | null;
  entidadeNome?: string | null;
  csrfToken: string;
  onSuccess: () => Promise<void>;
}

export function SheetBuscarLancamento({
  open, onOpenChange, statement: s, entidadeId, entidadeLogoUrl, entidadeNome, csrfToken, onSuccess,
}: SheetBuscarLancamentoProps) {
  const [query, setQuery] = useState('');
  const [period, setPeriod] = useState<PeriodValue>(() => defaultPeriod());
  const [results, setResults] = useState<BuscarLancamentoResult[]>([]);
  const [loading, setLoading] = useState(false);
  const [searched, setSearched] = useState(false);
  const [selectedIds, setSelectedIds] = useState<Set<number>>(new Set());
  const [vinculando, setVinculando] = useState(false);
  const [advancedFilters, setAdvancedFilters] = useState<TransacaoAdvancedFiltersState>({});

  const onAdvancedFiltersChange = useCallback((next: TransacaoAdvancedFiltersState) => {
    setAdvancedFilters(next);
  }, []);

  const bankValue = Math.abs(s.amount_cents) / 100;
  const neg = s.amount_cents < 0;

  const handleSearch = useCallback(async () => {
    setLoading(true);
    setSearched(true);
    setSelectedIds(new Set());
    try {
      const params = new URLSearchParams();
      params.set('entidade_id', String(entidadeId));
      params.set('q', query.trim());
      params.set('amount_cents', String(s.amount_cents));
      params.set('start_date', period.startDate);
      params.set('end_date', period.endDate);
      advancedFilters.categoria?.forEach((id) => params.append('lancamento_padrao_id', id));
      advancedFilters.centro_custo?.forEach((id) => params.append('cost_center_id', id));
      advancedFilters.parceiro?.forEach((id) => params.append('parceiro_id', id));
      advancedFilters.origem?.forEach((o) => params.append('origem', o));
      advancedFilters.recorrencia?.forEach((r) => params.append('recorrencia', r));
      if (advancedFilters.valor?.min != null && Number.isFinite(advancedFilters.valor.min)) params.set('valor_min', String(advancedFilters.valor.min));
      if (advancedFilters.valor?.max != null && Number.isFinite(advancedFilters.valor.max)) params.set('valor_max', String(advancedFilters.valor.max));
      if (advancedFilters.data_registro?.from) params.set('created_from', advancedFilters.data_registro.from);
      if (advancedFilters.data_registro?.to) params.set('created_to', advancedFilters.data_registro.to);

      const res = await fetch(`/conciliacao/buscar-lancamento?${params}`, {
        headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin',
      });

      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      const json = await res.json();
      setResults(json.data ?? []);
    } catch {
      notify.networkError();
    } finally {
      setLoading(false);
    }
  }, [query, s.amount_cents, entidadeId, csrfToken, period, advancedFilters]);

  const openRef = useRef(false);
  const periodRef = useRef(period);

  useEffect(() => {
    if (open && !openRef.current) {
      openRef.current = true;
      periodRef.current = period;
      handleSearch();
    }
    if (!open && openRef.current) {
      openRef.current = false;
      setResults([]);
      setSelectedIds(new Set());
      setSearched(false);
      setQuery('');
      setAdvancedFilters({});
      const fresh = defaultPeriod();
      setPeriod(fresh);
      periodRef.current = fresh;
    }
  }, [open]); // eslint-disable-line react-hooks/exhaustive-deps

  useEffect(() => {
    if (!openRef.current) return;
    if (period.startDate === periodRef.current.startDate && period.endDate === periodRef.current.endDate) return;
    periodRef.current = period;
    handleSearch();
  }, [period]); // eslint-disable-line react-hooks/exhaustive-deps

  useEffect(() => {
    if (!openRef.current) return;
    handleSearch();
  }, [advancedFilters]); // eslint-disable-line react-hooks/exhaustive-deps

  function toggleSelect(id: number) {
    setSelectedIds((prev) => {
      const next = new Set(prev);
      if (next.has(id)) next.delete(id);
      else next.add(id);
      return next;
    });
  }

  function toggleAll() {
    if (selectedIds.size === filteredResults.length) setSelectedIds(new Set());
    else setSelectedIds(new Set(filteredResults.map((r) => r.id)));
  }

  const filteredResults = useMemo(
    () => results.filter((r) => neg ? r.tipo === 'saida' : r.tipo === 'entrada'),
    [results, neg],
  );

  const selectedTotal = useMemo(
    () => filteredResults.filter((r) => selectedIds.has(r.id)).reduce((sum, r) => sum + Math.abs(r.valor), 0),
    [filteredResults, selectedIds],
  );
  const diferenca = bankValue - selectedTotal;

  async function handleConciliar() {
    if (selectedIds.size === 0 || vinculando) return;
    setVinculando(true);
    try {
      const itens = [...selectedIds].map((tid) => ({
        bank_statement_id: s.id,
        transacao_id: tid,
        mode: 'match',
      }));
      const res = await fetch('/conciliacao/conciliar-lote', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ itens }),
        credentials: 'same-origin',
      });
      const json = await res.json();
      if (!res.ok || !json.success) {
        notify.error('Erro ao conciliar', json.message ?? '');
        return;
      }
      notify.success('Conciliado!', json.message ?? '');
      onOpenChange(false);
      await onSuccess();
    } catch {
      notify.networkError();
    } finally {
      setVinculando(false);
    }
  }

  const allSelected = filteredResults.length > 0 && selectedIds.size === filteredResults.length;
  const someSelected = selectedIds.size > 0 && selectedIds.size < filteredResults.length;

  return (
    <Sheet open={open} onOpenChange={onOpenChange}>
      <SheetContent side="right" className="flex flex-col w-full sm:max-w-7xl p-0 gap-0" aria-describedby={undefined}>
        {/* ── Header fixo: dados do extrato ──────────── */}
        <div className="shrink-0 border-b bg-muted/30 px-6 py-4">
          <div className="flex items-start gap-4">
            <div className="size-10 shrink-0 rounded-lg overflow-hidden bg-muted flex items-center justify-center border border-border">
              {entidadeLogoUrl
                ? <img src={entidadeLogoUrl} alt={entidadeNome ?? ''} className="size-full object-contain p-1" />
                : <Landmark className="size-5 text-muted-foreground" aria-hidden />}
            </div>
            <div className="min-w-0 flex-1">
              <SheetHeader className="p-0">
                <SheetTitle className="text-base">
                  {neg ? 'Pagamento' : 'Recebimento'} importado de {formatBRLFromCents(Math.abs(s.amount_cents))}
                </SheetTitle>
              </SheetHeader>
              <div className="mt-1 flex flex-wrap gap-x-4 gap-y-0.5 text-xs text-muted-foreground">
                <span>Data do lançamento no extrato: <strong className="text-foreground">{formatDataBR(s.dtposted)}</strong></span>
                {s.memo && <span>Descrição: <strong className="text-foreground">{s.memo}</strong></span>}
                {s.checknum && <span>Nº doc: <strong className="text-foreground">{s.checknum}</strong></span>}
              </div>
            </div>
          </div>
        </div>

        {/* ── Barra de filtros ────────────────────────── */}
        <TransacaoAdvancedFiltersScope
          value={advancedFilters}
          onChange={onAdvancedFiltersChange}
          tipoFormData={neg ? 'despesa' : 'receita'}
        >
          <div className="shrink-0 border-b px-6 py-3">
            <TransacaoFiltersRow
              period={period}
              onPeriodChange={setPeriod}
              searchQuery={query}
              onSearchChange={setQuery}
              searchPlaceholder="Cliente, descrição, categoria..."
              loading={loading}
              onSearch={handleSearch}
              afterSearch={<TransacaoAdvancedFiltersTrigger />}
              extraBelow={
                <>
                  <TransacaoAdvancedFiltersChipsSection />
                  {searched && (
                    <p className="text-xs text-muted-foreground">
                      {filteredResults.length} registro(s) encontrado(s)
                    </p>
                  )}
                </>
              }
            />
          </div>
        </TransacaoAdvancedFiltersScope>

        {/* ── Tabela de resultados ────────────────────── */}
        <SheetBody className="flex-1 overflow-y-auto px-0 py-0">
          {!searched && (
            <div className="flex flex-col items-center justify-center h-full text-center px-6 py-12 text-muted-foreground">
              <Search className="size-8 mb-3 opacity-30" />
              <p className="text-sm font-medium">Pesquise para encontrar lançamentos</p>
              <p className="text-xs mt-1">Use a barra acima para buscar por descrição, parceiro ou valor.</p>
            </div>
          )}

          {searched && loading && (
            <div className="flex items-center justify-center py-12">
              <Loader2 className="size-6 animate-spin text-muted-foreground" />
            </div>
          )}

          {searched && !loading && filteredResults.length === 0 && (
            <div className="flex flex-col items-center justify-center py-12 text-center px-6 text-muted-foreground">
              <p className="text-sm font-medium">Nenhum lançamento encontrado</p>
              <p className="text-xs mt-1">Tente alterar os filtros de busca.</p>
            </div>
          )}

          {filteredResults.length > 0 && !loading && (
            <table className="w-full text-sm">
              <thead className="bg-muted/30 sticky top-0 z-10">
                <tr className="border-b">
                  <th className="w-10 px-4 py-2 text-center">
                    <Checkbox
                      checked={allSelected ? true : someSelected ? 'indeterminate' : false}
                      onCheckedChange={toggleAll}
                    />
                  </th>
                  <th className="px-3 py-2 text-left text-xs font-medium text-muted-foreground">Descrição</th>
                  <th className="w-36 px-3 py-2 text-left text-xs font-medium text-muted-foreground">Categoria</th>
                  <th className="w-24 px-3 py-2 text-left text-xs font-medium text-muted-foreground">Situação</th>
                  <th className="w-24 px-3 py-2 text-left text-xs font-medium text-muted-foreground">Vencimento</th>
                  <th className="w-24 px-3 py-2 text-left text-xs font-medium text-muted-foreground">Pagamento</th>
                  <th className="w-28 px-3 py-2 text-right text-xs font-medium text-muted-foreground">Valor (R$)</th>
                </tr>
              </thead>
              <tbody className="divide-y">
                {filteredResults.map((r) => {
                  const isSelected = selectedIds.has(r.id);
                  const sit = SITUACAO_MAP[r.situacao ?? ''];
                  return (
                    <tr
                      key={r.id}
                      className={cn(
                        'cursor-pointer transition-colors hover:bg-muted/30',
                        isSelected && 'bg-primary/5',
                        r.conciliado && 'opacity-60',
                      )}
                      onClick={() => toggleSelect(r.id)}
                    >
                      <td className="w-10 px-4 py-2.5 text-center" onClick={(e) => e.stopPropagation()}>
                        <Checkbox checked={isSelected} onCheckedChange={() => toggleSelect(r.id)} />
                      </td>

                      {/* Descrição + parceiro + tipo doc */}
                      <td className="px-3 py-2.5 min-w-0">
                        <div className="flex items-center gap-1.5">
                          <p className="font-medium truncate">{r.descricao ?? '—'}</p>
                          {r.conciliado && (
                            <Badge variant="outline" className="text-[10px] px-1.5 py-0 shrink-0 border-emerald-300 text-emerald-600">
                              <Link2 className="size-2.5 mr-0.5" />Conciliado
                            </Badge>
                          )}
                        </div>
                        <div className="flex items-center gap-1.5 mt-0.5">
                          {r.parceiro_nome && <span className="text-xs text-muted-foreground truncate">{r.parceiro_nome}</span>}
                          {r.parceiro_nome && r.tipo_documento && <span className="text-muted-foreground/40">·</span>}
                          {r.tipo_documento && <span className="text-[10px] text-muted-foreground uppercase">{r.tipo_documento}</span>}
                          {r.numero_documento && (
                            <span className="text-[10px] text-muted-foreground/60">nº {r.numero_documento}</span>
                          )}
                        </div>
                      </td>

                      {/* Categoria */}
                      <td className="w-36 px-3 py-2.5">
                        <span className="text-xs text-muted-foreground truncate">{r.categoria ?? '—'}</span>
                      </td>

                      {/* Situação */}
                      <td className="w-24 px-3 py-2.5">
                        {sit ? (
                          <span className={cn('inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium', sit.class)}>
                            {sit.label}
                          </span>
                        ) : (
                          <span className="text-xs text-muted-foreground">—</span>
                        )}
                      </td>

                      {/* Vencimento */}
                      <td className="w-24 px-3 py-2.5">
                        <span className="text-xs tabular-nums">{formatDataBR(r.data_vencimento ?? r.data_competencia)}</span>
                      </td>

                      {/* Pagamento */}
                      <td className="w-24 px-3 py-2.5">
                        <span className="text-xs tabular-nums">{formatDataBR(r.data_pagamento)}</span>
                      </td>

                      {/* Valor */}
                      <td className="w-28 px-3 py-2.5 text-right">
                        <span className={cn(
                          'font-semibold tabular-nums',
                          r.tipo === 'entrada' ? 'text-emerald-600' : 'text-red-600',
                        )}>
                          {formatBRL(r.valor)}
                        </span>
                      </td>
                    </tr>
                  );
                })}
              </tbody>
            </table>
          )}
        </SheetBody>

        {/* ── Footer fixo: totais e ações ────────────── */}
        <div className="shrink-0 border-t bg-background px-6 py-3">
          <div className="flex flex-wrap items-center justify-between gap-3">
            <div className="flex flex-wrap items-center gap-x-6 gap-y-1 text-xs">
              <div>
                <span className="text-muted-foreground">Valor do lançamento bancário (R$)</span>
                <p className="font-bold text-sm tabular-nums">{formatBRL(bankValue)}</p>
              </div>
              <div>
                <span className="text-muted-foreground">Valor selecionado (R$)</span>
                <p className={cn('font-bold text-sm tabular-nums', selectedTotal > 0 ? 'text-emerald-600' : 'text-muted-foreground')}>
                  {formatBRL(selectedTotal)}
                </p>
              </div>
              <div>
                <span className="text-muted-foreground">Diferença (R$)</span>
                <p className={cn('font-bold text-sm tabular-nums', Math.abs(diferenca) < 0.01 ? 'text-emerald-600' : 'text-amber-600')}>
                  {formatBRL(diferenca)}
                </p>
              </div>
            </div>
            <div className="flex items-center gap-2">
              <Button variant="outline" size="sm" onClick={() => onOpenChange(false)}>
                Cancelar
              </Button>
              <Tooltip>
                <TooltipTrigger asChild>
                  <span tabIndex={selectedIds.size > 0 && Math.abs(diferenca) >= 0.01 ? 0 : undefined}>
                    <Button
                      variant="primary"
                      size="sm"
                      onClick={handleConciliar}
                      disabled={selectedIds.size === 0 || vinculando || Math.abs(diferenca) >= 0.01}
                    >
                      {vinculando ? <Loader2 className="size-4 mr-1.5 animate-spin" /> : <Link2 className="size-4 mr-1.5" />}
                      Conciliar
                    </Button>
                  </span>
                </TooltipTrigger>
                {selectedIds.size > 0 && Math.abs(diferenca) >= 0.01 && (
                  <TooltipContent>
                    <p>Diferença de {formatBRL(diferenca)} ainda pendente. O valor selecionado precisa ser igual ao valor do lançamento bancário.</p>
                  </TooltipContent>
                )}
              </Tooltip>
            </div>
          </div>
        </div>
      </SheetContent>
    </Sheet>
  );
}
