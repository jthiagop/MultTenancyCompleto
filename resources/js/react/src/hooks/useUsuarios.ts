import { useCallback, useEffect, useRef, useState } from 'react';

// ── Tipos ────────────────────────────────────────────────────────────────────

export interface IUsuarioRole {
  name: string;
}

export interface IUsuarioCompany {
  id: number;
  name: string;
  avatar_url: string | null;
}

export interface IUsuario {
  id: number;
  name: string;
  email: string;
  avatar_url: string | null;
  active: boolean;
  last_login_formatted: string;
  created_at_formatted: string;
  roles: IUsuarioRole[];
  companies: IUsuarioCompany[];
}

export interface UsuariosPagination {
  total: number;
  per_page: number;
  current_page: number;
  last_page: number;
}

export interface UseUsuariosParams {
  search?: string;
  page?: number;
  perPage?: number;
  sortBy?: string;
  sortDir?: 'asc' | 'desc';
}

export interface UseUsuariosReturn {
  data: IUsuario[];
  pagination: UsuariosPagination;
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

// ── Hook ─────────────────────────────────────────────────────────────────────

export function useUsuarios(params: UseUsuariosParams = {}): UseUsuariosReturn {
  const [data, setData] = useState<IUsuario[]>([]);
  const [pagination, setPagination] = useState<UsuariosPagination>({
    total: 0,
    per_page: 20,
    current_page: 1,
    last_page: 1,
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
    qp.set('per_page', String(params.perPage ?? 20));
    qp.set('sort_by', params.sortBy ?? 'name');
    qp.set('sort_dir', params.sortDir ?? 'asc');
    if (params.search) qp.set('search', params.search);

    setLoading(true);
    setError(null);

    fetchJson<{
      data: IUsuario[];
      total: number;
      per_page: number;
      current_page: number;
      last_page: number;
    }>(`/api/cadastros/usuarios?${qp.toString()}`)
      .then((res) => {
        if (ctrl.signal.aborted) return;
        setData(res.data);
        setPagination({
          total: res.total,
          per_page: res.per_page,
          current_page: res.current_page,
          last_page: res.last_page,
        });
      })
      .catch((err: unknown) => {
        if (ctrl.signal.aborted) return;
        setError(err instanceof Error ? err : new Error('Erro ao carregar usuários'));
      })
      .finally(() => {
        if (!ctrl.signal.aborted) setLoading(false);
      });
  }, [params.page, params.perPage, params.search, params.sortBy, params.sortDir]);

  useEffect(() => {
    fetch$();
    return () => { abortRef.current?.abort(); };
  }, [fetch$]);

  return { data, pagination, loading, error, refetch: fetch$ };
}
