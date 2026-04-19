import { useCallback, useEffect, useMemo, useState } from 'react';
import {
  ChevronLeft,
  ChevronRight,
  Eye,
  Loader2,
  MoreHorizontal,
  Undo2,
} from 'lucide-react';
import { Card, CardContent } from '@/components/ui/card';
import { defaultPeriod, type PeriodValue } from '@/components/ui/period-picker';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Tabs, TabsList, TabsTrigger } from '@/components/ui/tabs';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { ConciliacaoDetalhesSheet } from '@/pages/financeiro/banco/entidade/conciliacao-detalhes-sheet';
import { DesfazerConciliacaoDialog } from '@/pages/financeiro/banco/entidade/desfazer-conciliacao-dialog';
import { FinanceiroTransacaoTableCardHeader } from '@/pages/financeiro/components/financeiro-transacao-table-card-header';
import { cn } from '@/lib/utils';

function formatBRL(v: number) {
  return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(v);
}

export interface HistoricoConciliacoesTabProps {
  entidadeId: string | undefined;
}

type StatusFiltro = 'all' | 'ok' | 'ignorado' | 'divergente';

interface HistoricoRow {
  id: number;
  transacao_id?: number | null;
  descricao: string;
  transacao_descricao: string;
  parceiro_nome: string;
  tipo: string;
  valor: number;
  status: string;
  lancamento_padrao: string;
  usuario: string;
  data_extrato_formatada: string;
  data_conciliacao_formatada: string;
}

interface HistoricoCounts {
  all?: number;
  ok?: number;
  pendente?: number;
  ignorado?: number;
  divergente?: number;
}

interface HistoricoMeta {
  current_page: number;
  last_page: number;
  total: number;
  per_page: number;
}

const STATUS_BADGE: Record<string, { label: string; className: string }> = {
  ok: { label: 'Conciliado', className: 'bg-emerald-500/15 text-emerald-800 border-emerald-500/30' },
  pendente: { label: 'Pendente', className: 'bg-primary/15 text-primary border-primary/30' },
  ignorado: { label: 'Ignorado', className: 'bg-amber-500/15 text-amber-900 border-amber-500/30' },
  divergente: { label: 'Divergente', className: 'bg-destructive/15 text-destructive border-destructive/30' },
};

export function HistoricoConciliacoesTab({ entidadeId }: HistoricoConciliacoesTabProps) {
  const [statusTab, setStatusTab] = useState<StatusFiltro>('all');
  const [page, setPage] = useState(1);
  const [perPage] = useState(10);
  const [period, setPeriod] = useState<PeriodValue>(() => defaultPeriod());
  const [searchInput, setSearchInput] = useState('');
  const [debouncedQ, setDebouncedQ] = useState('');

  const [rows, setRows] = useState<HistoricoRow[]>([]);
  const [counts, setCounts] = useState<HistoricoCounts>({});
  const [meta, setMeta] = useState<HistoricoMeta | null>(null);
  const [totalEntradas, setTotalEntradas] = useState(0);
  const [totalSaidas, setTotalSaidas] = useState(0);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const [detalhesSheetOpen, setDetalhesSheetOpen] = useState(false);
  const [detalhesBankStatementId, setDetalhesBankStatementId] = useState<number | null>(null);

  const [desfazerInfo, setDesfazerInfo] = useState<{ id: number; descricao: string } | null>(null);

  useEffect(() => {
    const t = window.setTimeout(() => setDebouncedQ(searchInput.trim()), 400);
    return () => window.clearTimeout(t);
  }, [searchInput]);

  useEffect(() => {
    setPage(1);
  }, [statusTab, debouncedQ, period.startDate, period.endDate]);

  const fetchHistorico = useCallback(async () => {
    if (!entidadeId) {
      setRows([]);
      setMeta(null);
      return;
    }

    setLoading(true);
    setError(null);
    try {
      const params = new URLSearchParams({
        structured: '1',
        page: String(page),
        per_page: String(perPage),
        q: debouncedQ,
        status: statusTab,
        start_date: period.startDate,
        end_date: period.endDate,
      });

      const res = await fetch(`/relatorios/entidades/${entidadeId}/historico-conciliacoes?${params.toString()}`, {
        headers: {
          Accept: 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
      });

      const json = (await res.json()) as {
        success?: boolean;
        data?: HistoricoRow[];
        counts?: HistoricoCounts;
        meta?: HistoricoMeta;
        total_entradas?: number;
        total_saidas?: number;
        message?: string;
      };

      if (!res.ok || !json.success) {
        throw new Error(json.message ?? `Erro ${res.status}`);
      }

      setRows(Array.isArray(json.data) ? json.data : []);
      if (json.counts) setCounts(json.counts);
      if (json.meta) setMeta(json.meta);
      setTotalEntradas(Number(json.total_entradas ?? 0));
      setTotalSaidas(Number(json.total_saidas ?? 0));
    } catch (e) {
      setError(e instanceof Error ? e.message : 'Erro ao carregar histórico');
      setRows([]);
      setMeta(null);
    } finally {
      setLoading(false);
    }
  }, [entidadeId, page, perPage, debouncedQ, statusTab, period.startDate, period.endDate]);

  useEffect(() => {
    fetchHistorico().catch(() => {});
  }, [fetchHistorico]);

  function handleVerDetalhes(bankStatementId: number) {
    setDetalhesBankStatementId(bankStatementId);
    setDetalhesSheetOpen(true);
  }

  const saldoResumo = useMemo(() => {
    const s = totalEntradas - totalSaidas;
    return { valor: s, positivo: s >= 0 };
  }, [totalEntradas, totalSaidas]);

  const tabLabels: { key: StatusFiltro; label: string; countKey: keyof HistoricoCounts }[] = [
    { key: 'all', label: 'Todos', countKey: 'all' },
    { key: 'ok', label: 'Conciliados', countKey: 'ok' },
    { key: 'ignorado', label: 'Ignorados', countKey: 'ignorado' },
    { key: 'divergente', label: 'Divergentes', countKey: 'divergente' },
  ];

  return (
    <div className="space-y-4">
      <Card>
        <FinanceiroTransacaoTableCardHeader
          period={period}
          onPeriodChange={setPeriod}
          searchQuery={searchInput}
          onSearchChange={setSearchInput}
          searchPlaceholder="Buscar memo, parceiro, categoria…"
          loading={loading}
          refetch={fetchHistorico}
          toolbarMode="refresh-only"
          betweenPeriodAndSearch={
            <div
              className={cn(
                'flex items-center gap-2 rounded-md border border-border bg-muted/40 px-3 py-1.5 text-sm tabular-nums',
                saldoResumo.positivo ? 'text-emerald-700 dark:text-emerald-400' : 'text-destructive',
              )}
              title="Entradas − saídas no período filtrado (valores do extrato)"
            >
              Saldo período: {formatBRL(saldoResumo.valor)}
            </div>
          }
        />

        <CardContent className="space-y-4 pt-0">
          <Tabs
            value={statusTab}
            onValueChange={(v) => setStatusTab(v as StatusFiltro)}
            className="w-full"
          >
            <TabsList variant="line" size="sm" className="mb-0 w-full flex-wrap justify-start">
              {tabLabels.map(({ key, label, countKey }) => (
                <TabsTrigger key={key} value={key} className="gap-1.5">
                  {label}
                  <span className="text-muted-foreground tabular-nums">
                    ({counts[countKey] ?? '—'})
                  </span>
                </TabsTrigger>
              ))}
            </TabsList>
          </Tabs>

          <p className="text-xs text-muted-foreground">
            Histórico de conciliações já processadas (exceto pendentes). Conta{' '}
            <span className="font-mono text-foreground">{entidadeId ?? '—'}</span>.
          </p>

          {error && (
            <div className="rounded-lg border border-destructive/40 bg-destructive/10 px-4 py-3 text-sm text-destructive">
              {error}
            </div>
          )}

          {loading && (
            <div className="flex items-center justify-center gap-2 rounded-xl border border-dashed border-border py-16 text-muted-foreground">
              <Loader2 className="size-5 animate-spin" aria-hidden />
              <span>Carregando histórico…</span>
            </div>
          )}

          {!loading && !error && (
            <div className="rounded-md border border-border overflow-hidden">
              <Table>
                <TableHeader>
                  <TableRow className="hover:bg-transparent">
                    <TableHead className="min-w-[120px]">Data extrato</TableHead>
                    <TableHead className="min-w-[220px]">Histórico</TableHead>
                    <TableHead className="w-[90px]">Tipo</TableHead>
                    <TableHead className="text-end w-[110px]">Valor</TableHead>
                    <TableHead className="w-[120px]">Status</TableHead>
                    <TableHead className="min-w-[100px]">Usuário</TableHead>
                    <TableHead className="text-end w-[110px]">Ações</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  {rows.length === 0 ? (
                    <TableRow>
                      <TableCell colSpan={7} className="py-12 text-center text-muted-foreground">
                        Nenhuma conciliação encontrada para o filtro selecionado.
                      </TableCell>
                    </TableRow>
                  ) : (
                    rows.map((row) => (
                      <TableRow key={row.id}>
                        <TableCell>
                          <div className="flex flex-col gap-0.5">
                            <span className="font-semibold">{row.data_extrato_formatada ?? '—'}</span>
                            <span className="text-xs text-muted-foreground">
                              Conc.: {row.data_conciliacao_formatada ?? '—'}
                            </span>
                          </div>
                        </TableCell>
                        <TableCell>
                          <button
                            type="button"
                            className="text-left font-medium text-foreground hover:text-primary hover:underline"
                            onClick={() => handleVerDetalhes(row.id)}
                          >
                            {row.descricao}
                          </button>
                          <div className="mt-1 flex flex-wrap gap-1">
                            {row.parceiro_nome && row.parceiro_nome !== '-' && (
                              <Badge variant="secondary" className="text-[10px] font-normal">
                                {row.parceiro_nome}
                              </Badge>
                            )}
                            {row.transacao_descricao &&
                              row.transacao_descricao !== '-' &&
                              row.transacao_descricao !== row.descricao && (
                                <span className="text-xs text-muted-foreground italic">
                                  &quot;{row.transacao_descricao}&quot;
                                </span>
                              )}
                          </div>
                          {row.lancamento_padrao && row.lancamento_padrao !== '-' && (
                            <div className="mt-1">
                              <Badge variant="outline" className="text-[10px] font-normal border-dashed">
                                {row.lancamento_padrao}
                              </Badge>
                            </div>
                          )}
                        </TableCell>
                        <TableCell>
                          <Badge
                            variant="outline"
                            className={cn(
                              'text-xs font-normal',
                              row.tipo === 'entrada' || row.tipo === 'receita'
                                ? 'border-emerald-500/40 text-emerald-800 dark:text-emerald-300'
                                : 'border-red-500/40 text-red-800 dark:text-red-300',
                            )}
                          >
                            {row.tipo === 'entrada' || row.tipo === 'receita' ? 'Entrada' : 'Saída'}
                          </Badge>
                        </TableCell>
                        <TableCell className="text-end tabular-nums font-semibold">
                          {formatBRL(Number(row.valor ?? 0))}
                        </TableCell>
                        <TableCell>
                          {(() => {
                            const st = STATUS_BADGE[row.status] ?? {
                              label: row.status,
                              className: 'bg-muted text-muted-foreground',
                            };
                            return (
                              <Badge variant="outline" className={cn('text-xs font-normal', st.className)}>
                                {st.label}
                              </Badge>
                            );
                          })()}
                        </TableCell>
                        <TableCell className="text-sm text-muted-foreground max-w-[140px] truncate" title={row.usuario}>
                          {row.usuario}
                        </TableCell>
                        <TableCell className="text-end">
                          <DropdownMenu>
                            <DropdownMenuTrigger asChild>
                              <Button type="button" variant="outline" size="sm" className="h-8 gap-1">
                                <MoreHorizontal className="size-4 sm:hidden" aria-hidden />
                                <span className="hidden sm:inline">Ações</span>
                              </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end" className="w-48">
                              <DropdownMenuItem
                                className="gap-2"
                                onSelect={() => handleVerDetalhes(row.id)}
                              >
                                <Eye className="size-4" />
                                Ver detalhes
                              </DropdownMenuItem>
                              <DropdownMenuItem
                                className="gap-2 text-destructive focus:text-destructive"
                                onSelect={() => setDesfazerInfo({ id: row.id, descricao: row.descricao })}
                              >
                                <Undo2 className="size-4" />
                                Desfazer
                              </DropdownMenuItem>
                            </DropdownMenuContent>
                          </DropdownMenu>
                        </TableCell>
                      </TableRow>
                    ))
                  )}
                </TableBody>
              </Table>
            </div>
          )}

          {!loading && meta && meta.last_page > 1 && (
            <div className="flex flex-wrap items-center justify-between gap-2 border-t border-border pt-4">
              <p className="text-xs text-muted-foreground">
                Página {meta.current_page} de {meta.last_page} — {meta.total} registro(s)
              </p>
              <div className="flex items-center gap-2">
                <Button
                  type="button"
                  variant="outline"
                  size="sm"
                  disabled={meta.current_page <= 1}
                  onClick={() => setPage((p) => Math.max(1, p - 1))}
                >
                  <ChevronLeft className="size-4" />
                  Anterior
                </Button>
                <Button
                  type="button"
                  variant="outline"
                  size="sm"
                  disabled={meta.current_page >= meta.last_page}
                  onClick={() => setPage((p) => p + 1)}
                >
                  Próxima
                  <ChevronRight className="size-4" />
                </Button>
              </div>
            </div>
          )}
        </CardContent>
      </Card>

      <ConciliacaoDetalhesSheet
        open={detalhesSheetOpen}
        onOpenChange={(o) => {
          setDetalhesSheetOpen(o);
          if (!o) setDetalhesBankStatementId(null);
        }}
        bankStatementId={detalhesBankStatementId}
      />

      <DesfazerConciliacaoDialog
        open={desfazerInfo !== null}
        onOpenChange={(o) => { if (!o) setDesfazerInfo(null); }}
        bankStatementId={desfazerInfo?.id ?? null}
        descricaoBanco={desfazerInfo?.descricao ?? ''}
        onSuccess={async () => { await fetchHistorico(); }}
      />
    </div>
  );
}
