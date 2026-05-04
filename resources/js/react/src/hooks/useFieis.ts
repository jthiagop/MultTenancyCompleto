import { useCallback, useEffect, useRef, useState } from 'react';

// ── Tipos ────────────────────────────────────────────────────────────────────

export type FielStatus = 'Ativo' | 'Inativo' | 'Falecido' | 'Mudou-se';

export interface IFiel {
  id: number;
  nome_completo: string;
  avatar_url: string | null;
  sexo: 'M' | 'F' | 'Outro' | null;
  cpf: string | null;
  rg: string | null;
  data_nascimento: string | null;
  data_nascimento_formatted: string | null;
  idade: number | null;
  telefone: string | null;
  telefone_is_whatsapp: boolean;
  email: string | null;
  cidade_uf: string | null;
  dizimista: boolean;
  codigo_dizimista: string | null;
  status: FielStatus;
  created_at_formatted: string | null;
}

export interface FieisPagination {
  total: number;
  per_page: number;
  current_page: number;
  last_page: number;
}

export interface FieisStats {
  total: number;
  masculino: number;
  feminino: number;
  dizimista: number;
  ativos: number;
}

// ── Filtros avançados ────────────────────────────────────────────────────────

export interface FieisAdvancedFiltersState {
  /** Faixa de idade em anos. */
  idade?: { min?: number; max?: number };
  /** Cidade (parcial, case-insensitive). */
  cidade?: string;
  /** Estado civil (múltipla escolha). */
  estado_civil?: string[];
  /** Situação / status (múltipla escolha). */
  situacao?: string[];
  /** Intervalo de data de nascimento (ISO date string). */
  nascimento?: { from?: string; to?: string };
  /** Sexo (múltipla escolha — sobrepõe o filtro da aba quando informado). */
  sexo?: ('M' | 'F' | 'Outro')[];
}

export interface UseFieisParams {
  search?: string;
  page?: number;
  perPage?: number;
  sortBy?: string;
  sortDir?: 'asc' | 'desc';
  status?: FielStatus | '';
  sexo?: 'M' | 'F' | 'Outro' | '';
  dizimista?: boolean | null;
  advancedFilters?: FieisAdvancedFiltersState;
}

export interface UseFieisReturn {
  data: IFiel[];
  pagination: FieisPagination;
  stats: FieisStats;
  loading: boolean;
  error: Error | null;
  refetch: () => void;
}

const DEFAULT_STATS: FieisStats = { total: 0, masculino: 0, feminino: 0, dizimista: 0, ativos: 0 };

// ── Helper ───────────────────────────────────────────────────────────────────

export class HttpError extends Error {
  constructor(public readonly status: number) {
    super(`HTTP ${status}`);
  }
}

async function fetchJson<T>(url: string, signal: AbortSignal): Promise<T> {
  const res = await fetch(url, {
    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
    credentials: 'same-origin',
    signal,
  });
  if (!res.ok) throw new HttpError(res.status);
  return res.json() as Promise<T>;
}

// ── Hook ─────────────────────────────────────────────────────────────────────

export function useFieis(params: UseFieisParams = {}): UseFieisReturn {
  const [data, setData] = useState<IFiel[]>([]);
  const [pagination, setPagination] = useState<FieisPagination>({
    total: 0,
    per_page: 20,
    current_page: 1,
    last_page: 1,
  });
  const [stats, setStats] = useState<FieisStats>(DEFAULT_STATS);
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
    qp.set('sort_by', params.sortBy ?? 'nome_completo');
    qp.set('sort_dir', params.sortDir ?? 'asc');
    if (params.search) qp.set('search', params.search);
    if (params.status) qp.set('status', params.status);
    if (params.sexo) qp.set('sexo', params.sexo);
    if (typeof params.dizimista === 'boolean') {
      qp.set('dizimista', params.dizimista ? '1' : '0');
    }

    // Filtros avançados
    const af = params.advancedFilters;
    if (af) {
      if (af.idade?.min != null) qp.set('idade_min', String(af.idade.min));
      if (af.idade?.max != null) qp.set('idade_max', String(af.idade.max));
      if (af.cidade?.trim()) qp.set('cidade', af.cidade.trim());
      if (af.estado_civil?.length) qp.set('estado_civil', af.estado_civil.join(','));
      if (af.situacao?.length) qp.set('situacao', af.situacao.join(','));
      if (af.nascimento?.from) qp.set('nascimento_de', af.nascimento.from);
      if (af.nascimento?.to) qp.set('nascimento_ate', af.nascimento.to);
      if (af.sexo?.length) qp.set('sexo', af.sexo.join(','));
    }

    setLoading(true);
    setError(null);

    fetchJson<{
      data: IFiel[];
      total: number;
      per_page: number;
      current_page: number;
      last_page: number;
      stats?: FieisStats;
    }>(`/api/cadastros/fieis?${qp.toString()}`, ctrl.signal)
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
        setError(err instanceof Error ? err : new Error('Erro ao carregar fiéis.'));
      })
      .finally(() => {
        if (!ctrl.signal.aborted) setLoading(false);
      });
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [
    params.page,
    params.perPage,
    params.search,
    params.sortBy,
    params.sortDir,
    params.status,
    params.sexo,
    params.dizimista,
    // eslint-disable-next-line react-hooks/exhaustive-deps
    JSON.stringify(params.advancedFilters),
  ]);

  useEffect(() => {
    fetch$();
    return () => { abortRef.current?.abort(); };
  }, [fetch$]);

  return { data, pagination, stats, loading, error, refetch: fetch$ };
}
