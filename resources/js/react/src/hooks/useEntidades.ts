import { useEffect, useState } from 'react';

export interface IEntidade {
  id: number;
  nome: string;
  tipo: 'banco' | 'caixa';
  account_type: string | null;
  account_label: string;
  agencia: string | null;
  conta: string | null;
  saldo_inicial: number;
  saldo_atual: number;
  saldo_negativo: boolean;
  logo_url: string | null;
  banco_nome: string | null;
  status_conciliacao: string;
  pendencias_conciliacao: number;
}

interface UseEntidadesResult {
  entidades: IEntidade[];
  totalSaldo: number;
  loading: boolean;
  error: string | null;
  refetch: () => void;
}

export function useEntidades(externalRefreshKey?: number): UseEntidadesResult {
  const [entidades, setEntidades] = useState<IEntidade[]>([]);
  const [totalSaldo, setTotalSaldo] = useState(0);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [tick, setTick] = useState(0);

  // Escuta o evento global emitido pelo interceptor de fetch.
  // Qualquer mutação financeira bem-sucedida (POST/PUT/PATCH/DELETE para
  // /app/financeiro/*) aciona o refetch automaticamente, sem que nenhum
  // componente ou hook de mutação precise disparar nada manualmente.
  useEffect(() => {
    const handler = () => setTick((t) => t + 1);
    window.addEventListener('financeiro:saldo-updated', handler);
    return () => window.removeEventListener('financeiro:saldo-updated', handler);
  }, []);

  useEffect(() => {
    let cancelled = false;
    setLoading(true);
    setError(null);

    fetch('/app/financeiro/banco/entidades', {
      headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin',
    })
      .then((res) => {
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        return res.json() as Promise<{ success: boolean; data: IEntidade[]; total_saldo: number }>;
      })
      .then((json) => {
        if (cancelled) return;
        if (json.success) {
          setEntidades(json.data);
          setTotalSaldo(json.total_saldo ?? 0);
        } else {
          setError('Não foi possível carregar as entidades financeiras.');
        }
      })
      .catch(() => {
        if (!cancelled) setError('Erro de conexão ao carregar entidades.');
      })
      .finally(() => {
        if (!cancelled) setLoading(false);
      });

    return () => {
      cancelled = true;
    };
  }, [tick, externalRefreshKey]);

  return { entidades, totalSaldo, loading, error, refetch: () => setTick((t) => t + 1) };
}
