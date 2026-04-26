import { useCallback, useEffect, useRef, useState } from 'react';

// ── Tipos ────────────────────────────────────────────────────────────────────

export interface IMembro {
  id: number;
  name: string;
  avatar_url: string | null;
  province: string | null;
  role: { name: string; slug: string } | null;
  current_stage: { id: number; name: string } | null;
  current_location: string | null;
  data_chave: string | null;
  is_active: boolean;
}

export interface MembrosStats {
  todos: number;
  presbiteros: number;
  diaconos: number;
  irmaos: number;
  votos_simples: number;
}

export interface MembrosPagination {
  total: number;
  per_page: number;
  current_page: number;
  last_page: number;
}

export type MembrosTab = 'todos' | 'presbiteros' | 'diaconos' | 'irmaos' | 'votos_simples';

export interface UseMembrosParams {
  tab?: MembrosTab;
  search?: string;
  page?: number;
  perPage?: number;
  sortBy?: string;
  sortDir?: 'asc' | 'desc';
}

export interface UseMembrosReturn {
  data: IMembro[];
  stats: MembrosStats;
  pagination: MembrosPagination;
  loading: boolean;
  error: Error | null;
  refetch: () => void;
}

// ── Helper ───────────────────────────────────────────────────────────────────

export class HttpError extends Error {
  constructor(public readonly status: number) {
    super(`HTTP ${status}`);
  }
}

async function fetchJson<T>(url: string): Promise<T> {
  const res = await fetch(url, {
    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
    credentials: 'same-origin',
  });
  if (!res.ok) throw new HttpError(res.status);
  return res.json() as Promise<T>;
}

// Mapeia a aba para os query params enviados ao backend
function tabToParams(tab: MembrosTab): Record<string, string> {
  switch (tab) {
    case 'presbiteros':  return { role_slug: 'presbitero' };
    case 'diaconos':     return { role_slug: 'diacono' };
    case 'irmaos':       return { role_slug: 'irmao' };
    case 'votos_simples':return { profession: 'temporaria' };
    default:             return {};
  }
}

const DEFAULT_STATS: MembrosStats = {
  todos: 0, presbiteros: 0, diaconos: 0, irmaos: 0, votos_simples: 0,
};

// ── Hook ─────────────────────────────────────────────────────────────────────

export function useMembros(params: UseMembrosParams = {}): UseMembrosReturn {
  const [data, setData] = useState<IMembro[]>([]);
  const [stats, setStats] = useState<MembrosStats>(DEFAULT_STATS);
  const [pagination, setPagination] = useState<MembrosPagination>({
    total: 0, per_page: 25, current_page: 1, last_page: 1,
  });
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<Error | null>(null);
  const abortRef = useRef<AbortController | null>(null);

  const fetch$ = useCallback(() => {
    abortRef.current?.abort();
    const ctrl = new AbortController();
    abortRef.current = ctrl;

    const qp = new URLSearchParams();
    qp.set('page', String(params.page ?? 1));
    qp.set('per_page', String(params.perPage ?? 25));
    qp.set('sort_by', params.sortBy ?? 'name');
    qp.set('sort_dir', params.sortDir ?? 'asc');
    if (params.search) qp.set('search', params.search);

    // Filtros de aba
    const tabParams = tabToParams(params.tab ?? 'todos');
    Object.entries(tabParams).forEach(([k, v]) => qp.set(k, v));

    setLoading(true);
    setError(null);

    fetchJson<{
      data: IMembro[];
      total: number;
      per_page: number;
      current_page: number;
      last_page: number;
      stats: MembrosStats;
    }>(`/api/secretary/membros?${qp.toString()}`)
      .then((res) => {
        if (ctrl.signal.aborted) return;
        setData(res.data);
        setStats(res.stats ?? DEFAULT_STATS);
        setPagination({
          total: res.total,
          per_page: res.per_page,
          current_page: res.current_page,
          last_page: res.last_page,
        });
      })
      .catch((err: unknown) => {
        if (ctrl.signal.aborted) return;
        setError(err instanceof Error ? err : new Error('Erro ao carregar membros'));
      })
      .finally(() => {
        if (!ctrl.signal.aborted) setLoading(false);
      });
  }, [params.page, params.perPage, params.search, params.sortBy, params.sortDir, params.tab]);

  useEffect(() => {
    fetch$();
    return () => { abortRef.current?.abort(); };
  }, [fetch$]);

  return { data, stats, pagination, loading, error, refetch: fetch$ };
}
