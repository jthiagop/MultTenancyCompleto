import { useCallback, useEffect, useRef, useState } from 'react';

// ── Tipos compartilhados ─────────────────────────────────────────────────────

export type SituacaoTransacao =
  | 'em_aberto'
  | 'atrasado'
  | 'previsto'
  | 'pago_parcial'
  | 'pago'
  | 'recebido'
  | 'desconsiderado';

export interface ITransacao {
  id: string;
  descricao: string;
  parceiro: string | null;
  valor: number;
  valor_restante: number;
  situacao: SituacaoTransacao;
  situacao_label: string;
  situacao_color: 'warning' | 'destructive' | 'secondary' | 'success';
  origem: string;
  is_parcelado: boolean;
  /** "2/6" se é uma parcela, null caso contrário */
  parcela_info: string | null;
  is_recorrente: boolean;
  /** "3/12" se é recorrente, null caso contrário */
  recorrencia_info: string | null;
  is_transferencia: boolean;
  is_rateio_origem: boolean;
  is_rateio_filho: boolean;
  origem_nome: string | null;
  origem_agencia: string | null;
  origem_conta: string | null;
  tipo: 'entrada' | 'saida';
  /** presente para contas a receber/pagar */
  vencimento?: string | null;
  /** presente para extrato */
  data?: string | null;
  // Campos extras configuráveis
  categoria?: string | null;
  centro_custo?: string | null;
  conta?: string | null;
  numero_documento?: string | null;
  tipo_documento?: string | null;
  data_competencia?: string | null;
  data_pagamento?: string | null;
  valor_pago?: number;
  juros?: number;
  multa?: number;
  desconto?: number;
}

export interface TransacoesPagination {
  total: number;
  per_page: number;
  current_page: number;
  last_page: number;
}

export interface StatsContas {
  vencidos: number;
  hoje: number;
  a_vencer: number;
  recebidos?: number;
  pagos?: number;
  total: number;
}

export interface StatsExtrato {
  receitas_aberto: number;
  receitas_realizadas: number;
  despesas_aberto: number;
  despesas_realizadas: number;
  total: number;
  saldo_anterior: number;
}

export type Stats = StatsContas | StatsExtrato;

/** Filtros avançados (chips) — só envie chaves que o usuário adicionou. */
export interface TransacaoAdvancedFiltersState {
  categoria?: string[];
  centro_custo?: string[];
  parceiro?: string[];
  origem?: string[];
  recorrencia?: ('com' | 'sem')[];
  valor?: { min?: number; max?: number };
  data_registro?: { from?: string; to?: string };
}

// ── Parâmetros do hook ───────────────────────────────────────────────────────

export interface UseTransacoesParams {
  tipo: 'entrada' | 'saida' | 'all';
  tab: 'contas_receber' | 'contas_pagar' | 'extrato';
  startDate?: string;
  endDate?: string;
  entidadeId?: string | number | null;
  situacao?: string;
  search?: string;
  status?: string;
  page?: number;
  perPage?: number;
  sortBy?: string;
  sortDir?: 'asc' | 'desc';
  advancedFilters?: TransacaoAdvancedFiltersState;
}

// ── Retorno do hook ──────────────────────────────────────────────────────────

export interface UseTransacoesReturn {
  data: ITransacao[];
  stats: Stats | null;
  pagination: TransacoesPagination;
  saldoAnterior: number;
  loading: boolean;
  loadingStats: boolean;
  error: string | null;
  refetch: () => void;
}

// ── Helpers ──────────────────────────────────────────────────────────────────

function buildParams(p: UseTransacoesParams, extra?: Record<string, string>): URLSearchParams {
  const now = new Date();
  const firstDay = new Date(now.getFullYear(), now.getMonth(), 1).toISOString().slice(0, 10);
  const lastDay  = new Date(now.getFullYear(), now.getMonth() + 1, 0).toISOString().slice(0, 10);

  const qp = new URLSearchParams();
  qp.set('tipo', p.tipo);
  qp.set('tab', p.tab);
  qp.set('start_date', p.startDate ?? firstDay);
  qp.set('end_date', p.endDate ?? lastDay);
  qp.set('page', String(p.page ?? 1));
  qp.set('per_page', String(p.perPage ?? 20));
  qp.set('sort_by', p.sortBy ?? 'vencimento');
  qp.set('sort_dir', p.sortDir ?? 'desc');

  if (extra) {
    Object.entries(extra).forEach(([k, v]) => qp.set(k, v));
  }

  if (p.entidadeId) qp.set('entidade_id', String(p.entidadeId));
  if (p.situacao) qp.set('situacao', p.situacao);
  if (p.search) qp.set('search', p.search);
  if (p.status) qp.set('status', p.status);

  const af = p.advancedFilters;
  if (af) {
    af.categoria?.forEach((id) => qp.append('lancamento_padrao_id', id));
    af.centro_custo?.forEach((id) => qp.append('cost_center_id', id));
    af.parceiro?.forEach((id) => qp.append('parceiro_id', id));
    af.origem?.forEach((o) => qp.append('origem', o));
    af.recorrencia?.forEach((r) => qp.append('recorrencia', r));
    if (af.valor?.min != null && Number.isFinite(af.valor.min)) qp.set('valor_min', String(af.valor.min));
    if (af.valor?.max != null && Number.isFinite(af.valor.max)) qp.set('valor_max', String(af.valor.max));
    if (af.data_registro?.from) qp.set('created_from', af.data_registro.from);
    if (af.data_registro?.to) qp.set('created_to', af.data_registro.to);
  }

  return qp;
}

async function fetchJson<T>(url: string): Promise<T> {
  const res = await fetch(url, {
    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
    credentials: 'same-origin',
  });
  if (!res.ok) throw new Error(`HTTP ${res.status}`);
  return res.json() as Promise<T>;
}

// ── Hook principal ────────────────────────────────────────────────────────────

export function useTransacoes(params: UseTransacoesParams): UseTransacoesReturn {
  const [data,         setData]         = useState<ITransacao[]>([]);
  const [stats,        setStats]        = useState<Stats | null>(null);
  const [saldoAnterior, setSaldoAnterior] = useState(0);
  const [pagination,   setPagination]   = useState<TransacoesPagination>({ total: 0, per_page: 20, current_page: 1, last_page: 1 });
  const [loading,      setLoading]      = useState(false);
  const [loadingStats, setLoadingStats] = useState(false);
  const [error,        setError]        = useState<string | null>(null);
  const abortRef = useRef<AbortController | null>(null);

  const fetch$ = useCallback(() => {
    abortRef.current?.abort();
    const ctrl = new AbortController();
    abortRef.current = ctrl;

    const qp = buildParams(params);
    const statsQp = buildParams(params, { status: '' });

    setLoading(true);
    setLoadingStats(true);
    setError(null);

    // Transações
    fetchJson<{
      data: ITransacao[];
      total: number;
      per_page: number;
      current_page: number;
      last_page: number;
      saldo_anterior: number;
    }>(`/app/financeiro/banco/transacoes?${qp.toString()}`)
      .then((res) => {
        setData(res.data);
        setSaldoAnterior(res.saldo_anterior ?? 0);
        setPagination({
          total:        res.total,
          per_page:     res.per_page,
          current_page: res.current_page,
          last_page:    res.last_page,
        });
      })
      .catch((e) => {
        if ((e as Error).name !== 'AbortError') setError((e as Error).message);
      })
      .finally(() => setLoading(false));

    // Stats
    fetchJson<Stats>(`/app/financeiro/banco/stats?${statsQp.toString()}`)
      .then(setStats)
      .catch((e) => {
        if ((e as Error).name !== 'AbortError') console.warn('stats error:', e);
      })
      .finally(() => setLoadingStats(false));
  }, [
    params.tipo, params.tab, params.startDate, params.endDate,
    params.entidadeId, params.situacao, params.search,
    params.status, params.page, params.perPage, params.sortBy, params.sortDir,
    params.advancedFilters,
  ]);

  useEffect(() => { fetch$(); }, [fetch$]);

  return { data, stats, pagination, saldoAnterior, loading, loadingStats, error, refetch: fetch$ };
}
