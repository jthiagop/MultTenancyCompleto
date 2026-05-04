import { useEffect, useRef, useState } from 'react';

/** Resultado mínimo de fiéis usado pelo SearchSelect do drawer de Dízimo. */
export interface FielSearchResult {
  id: number;
  nome_completo: string;
  avatar_url: string | null;
  codigo_dizimista: string | null;
  cidade_uf: string | null;
}

interface UseFielSearchReturn {
  options: FielSearchResult[];
  loading: boolean;
}

/**
 * Busca assíncrona de fiéis por nome/CPF (reutiliza /api/cadastros/fieis).
 * Debounce 300ms; cancelamento via AbortController.
 */
export function useFielSearch(term: string): UseFielSearchReturn {
  const [options, setOptions] = useState<FielSearchResult[]>([]);
  const [loading, setLoading] = useState(false);
  const abortRef = useRef<AbortController | null>(null);

  useEffect(() => {
    const cleanTerm = term.trim();
    if (cleanTerm.length < 2) {
      setOptions([]);
      setLoading(false);
      return;
    }

    const handle = window.setTimeout(() => {
      abortRef.current?.abort();
      const ctrl = new AbortController();
      abortRef.current = ctrl;

      setLoading(true);
      const qp = new URLSearchParams({
        search: cleanTerm,
        per_page: '20',
        page: '1',
        sort_by: 'nome_completo',
        sort_dir: 'asc',
      });

      fetch(`/api/cadastros/fieis?${qp.toString()}`, {
        headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin',
        signal: ctrl.signal,
      })
        .then((r) => (r.ok ? r.json() : Promise.reject(new Error(`HTTP ${r.status}`))))
        .then((res: { data: Array<Record<string, unknown>> }) => {
          if (ctrl.signal.aborted) return;
          const list: FielSearchResult[] = (res.data ?? []).map((f) => ({
            id: Number(f.id),
            nome_completo: String(f.nome_completo ?? ''),
            avatar_url: (f.avatar_url as string | null) ?? null,
            codigo_dizimista: (f.codigo_dizimista as string | null) ?? null,
            cidade_uf: (f.cidade_uf as string | null) ?? null,
          }));
          setOptions(list);
        })
        .catch((err: unknown) => {
          if (err instanceof DOMException && err.name === 'AbortError') return;
          setOptions([]);
        })
        .finally(() => {
          if (!ctrl.signal.aborted) setLoading(false);
        });
    }, 300);

    return () => {
      window.clearTimeout(handle);
      abortRef.current?.abort();
    };
  }, [term]);

  return { options, loading };
}
