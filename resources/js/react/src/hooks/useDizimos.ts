import { useCallback, useEffect, useRef, useState } from 'react';

// ── Tipos ────────────────────────────────────────────────────────────────────

export type DizimoTipo = 'Dízimo' | 'Doação' | 'Oferta' | 'Outro';

export interface DizimoFiel {
  id: number;
  nome_completo: string;
  avatar_url: string | null;
}

export interface DizimoEntidade {
  id: number;
  nome: string;
  tipo: string;
}

export interface IDizimo {
  id: number;
  tipo: DizimoTipo;
  valor: number;
  valor_formatado: string;
  data_pagamento: string | null;
  data_pagamento_formatada: string | null;
  forma_pagamento: string | null;
  numero_documento: string | null;
  observacoes: string | null;
  integrado_financeiro: boolean;
  movimentacao_id: number | null;
  fiel: DizimoFiel | null;
  entidade_financeira: DizimoEntidade | null;
  entidade_financeira_id: number | null;
}

export interface DizimosPagination {
  total: number;
  per_page: number;
  current_page: number;
  last_page: number;
}

export interface DizimosStats {
  total: number;
  dizimo: number;
  doacao: number;
  oferta: number;
}

export interface DizimosFilters {
  search?: string;
  fielId?: number;
  tipo?: DizimoTipo[];
  dataInicio?: string;
  dataFim?: string;
  valorMin?: number;
  valorMax?: number;
  formaPagamento?: string;
  integrado?: boolean | null;
}

export interface UseDizimosParams extends DizimosFilters {
  page?: number;
  perPage?: number;
  sortBy?: string;
  sortDir?: 'asc' | 'desc';
}

export class HttpError extends Error {
  constructor(public readonly status: number) {
    super(`HTTP ${status}`);
  }
}

const DEFAULT_STATS: DizimosStats = { total: 0, dizimo: 0, doacao: 0, oferta: 0 };

// ── Hook ─────────────────────────────────────────────────────────────────────

export function useDizimos(params: UseDizimosParams = {}) {
  const [data, setData] = useState<IDizimo[]>([]);
  const [pagination, setPagination] = useState<DizimosPagination>({
    total: 0,
    per_page: 20,
    current_page: 1,
    last_page: 1,
  });
  const [stats, setStats] = useState<DizimosStats>(DEFAULT_STATS);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<Error | null>(null);
  const abortRef = useRef<AbortController | null>(null);

  const fetch$ = useCallback(() => {
    abortRef.current?.abort();
    const ctrl = new AbortController();
    abortRef.current = ctrl;

    const qp = new URLSearchParams();
    qp.set('page', String(params.page ?? 1));
    qp.set('per_page', String(params.perPage ?? 20));
    qp.set('sort_by', params.sortBy ?? 'data_pagamento');
    qp.set('sort_dir', params.sortDir ?? 'desc');
    if (params.search?.trim()) qp.set('search', params.search.trim());
    if (params.fielId) qp.set('fiel_id', String(params.fielId));
    if (params.tipo?.length) qp.set('tipo', params.tipo.join(','));
    if (params.dataInicio) qp.set('data_inicio', params.dataInicio);
    if (params.dataFim) qp.set('data_fim', params.dataFim);
    if (params.valorMin !== undefined) qp.set('valor_min', String(params.valorMin));
    if (params.valorMax !== undefined) qp.set('valor_max', String(params.valorMax));
    if (params.formaPagamento) qp.set('forma_pagamento', params.formaPagamento);
    if (typeof params.integrado === 'boolean') {
      qp.set('integrado', params.integrado ? '1' : '0');
    }

    setLoading(true);
    setError(null);

    fetch(`/api/cadastros/dizimos?${qp.toString()}`, {
      headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin',
      signal: ctrl.signal,
    })
      .then(async (r) => {
        if (!r.ok) throw new HttpError(r.status);
        return r.json() as Promise<{
          data: IDizimo[];
          total: number;
          per_page: number;
          current_page: number;
          last_page: number;
          stats?: DizimosStats;
        }>;
      })
      .then((res) => {
        if (ctrl.signal.aborted) return;
        setData(res.data);
        setPagination({
          total: res.total,
          per_page: res.per_page,
          current_page: res.current_page,
          last_page: res.last_page,
        });
        if (res.stats) setStats(res.stats);
      })
      .catch((err: unknown) => {
        if (ctrl.signal.aborted) return;
        if (err instanceof DOMException && err.name === 'AbortError') return;
        setError(err instanceof Error ? err : new Error('Erro ao carregar lançamentos.'));
      })
      .finally(() => {
        if (!ctrl.signal.aborted) setLoading(false);
      });
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [
    params.page,
    params.perPage,
    params.sortBy,
    params.sortDir,
    params.search,
    params.fielId,
    // eslint-disable-next-line react-hooks/exhaustive-deps
    params.tipo?.join(','),
    params.dataInicio,
    params.dataFim,
    params.valorMin,
    params.valorMax,
    params.formaPagamento,
    params.integrado,
  ]);

  useEffect(() => {
    fetch$();
    return () => {
      abortRef.current?.abort();
    };
  }, [fetch$]);

  return { data, pagination, stats, loading, error, refetch: fetch$ };
}
