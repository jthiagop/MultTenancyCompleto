import { useCallback, useState } from 'react';

export interface CarneLookupResult {
  fiel: { id: number; nome_completo: string; avatar_url: string | null };
  comunidade: string | null;
  codigo: string;
  dizimista: boolean;
}

interface UseLookupCarneReturn {
  lookup: (codigo: string) => Promise<CarneLookupResult | null>;
  loading: boolean;
  error: string | null;
}

/**
 * Lookup do código do carnê (D-XXXX) na company ativa.
 * Retorna o fiel + comunidade quando encontrado; null caso contrário.
 */
export function useLookupCarne(): UseLookupCarneReturn {
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const lookup = useCallback(async (codigo: string): Promise<CarneLookupResult | null> => {
    const code = codigo.trim();
    if (!code) {
      setError('Informe o código do carnê.');
      return null;
    }

    setLoading(true);
    setError(null);

    try {
      const qp = new URLSearchParams({ codigo: code });
      const res = await fetch(`/api/cadastros/dizimos/lookup-codigo?${qp.toString()}`, {
        headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin',
      });

      const data = (await res.json()) as
        | (CarneLookupResult & { success: true })
        | { success: false; message?: string };

      if (!res.ok || data.success === false) {
        setError(('message' in data ? data.message : null) ?? 'Carnê não encontrado.');
        return null;
      }
      return data;
    } catch {
      setError('Erro de conexão ao buscar carnê.');
      return null;
    } finally {
      setLoading(false);
    }
  }, []);

  return { lookup, loading, error };
}
