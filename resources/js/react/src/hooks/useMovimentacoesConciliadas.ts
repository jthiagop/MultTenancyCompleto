import { useCallback, useEffect, useState } from 'react';
import type { PeriodValue } from '@/components/ui/period-picker';

export type ConciliacaoLinhaTipo = 'manual' | 'automatico';

export interface LinhaMovimentacaoApi {
  id: string;
  descricao_sistema: string;
  subcategoria?: string | null;
  valor_sistema_cents: number;
  descricao_banco: string;
  valor_banco_cents: number;
  conciliacao: ConciliacaoLinhaTipo;
  bank_statement_id?: number;
  transacao_financeira_id?: number;
}

export interface DiaMovimentacaoApi {
  id: string;
  data_label: string;
  dia_semana: string;
  diferenca_cents: number;
  saldo_sistema_cents: number;
  saldo_banco_cents: number;
  tem_pendencia: boolean;
  linhas: LinhaMovimentacaoApi[];
}

interface ApiSuccess {
  success: true;
  data: { dias: DiaMovimentacaoApi[] };
}

interface ApiError {
  success: false;
  message?: string;
}

export function useMovimentacoesConciliadas(entidadeId: string | undefined, period: PeriodValue) {
  const [dias, setDias] = useState<DiaMovimentacaoApi[]>([]);
  const [loading, setLoading] = useState(Boolean(entidadeId));
  const [error, setError] = useState<string | null>(null);

  const fetchDias = useCallback(async () => {
    if (!entidadeId) {
      setDias([]);
      return;
    }

    const params = new URLSearchParams({
      start_date: period.startDate,
      end_date: period.endDate,
    });
    const url = `/app/financeiro/banco/entidade/${entidadeId}/movimentacoes-conciliadas?${params.toString()}`;

    const res = await fetch(url, {
      headers: {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      },
      credentials: 'same-origin',
    });

    const body = (await res.json()) as ApiSuccess | ApiError;
    if (!res.ok || !body.success) {
      throw new Error(!body.success ? body.message ?? `Erro ${res.status}` : `Erro ${res.status}`);
    }

    setDias(body.data.dias ?? []);
  }, [entidadeId, period.startDate, period.endDate]);

  useEffect(() => {
    if (!entidadeId) {
      setDias([]);
      setLoading(false);
      setError(null);
      return;
    }

    setLoading(true);
    setError(null);
    fetchDias()
      .catch((e) => setError(e instanceof Error ? e.message : 'Erro ao carregar movimentações'))
      .finally(() => setLoading(false));
  }, [entidadeId, fetchDias]);

  return {
    dias,
    loading,
    error,
    refresh: fetchDias,
  };
}
