import { useCallback, useEffect, useState } from 'react';

export type ConciliacoesTabKey = 'all' | 'received' | 'paid';

export interface MatchClassificacao {
  nivel: string;
  cor: string;
  texto: string;
}

export interface PossivelTransacao {
  id: number;
  data_competencia: string | null;
  tipo: string;
  valor: number;
  descricao: string | null;
  match_score: number;
  match_classificacao: MatchClassificacao;
}

export interface StatementBlock {
  id: number;
  dtposted: string | null;
  amount_cents: number;
  amount: number;
  memo: string | null;
  checknum: string | null;
  status_conciliacao: string | null;
  movimentacao_interna: Record<string, unknown> | false | null;
}

export interface ConciliacaoItem {
  statement: StatementBlock;
  sugestao: unknown;
  possiveis_transacoes: PossivelTransacao[];
}

export interface EntidadeResumo {
  id: number;
  nome: string;
  tipo: string;
  logo_url: string | null;
  banco_nome: string | null;
}

export interface ConciliacoesCounts {
  all: number;
  received: number;
  paid: number;
}

export interface ConciliacoesPagination {
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
  has_more: boolean;
}

export interface ConciliacaoFormCentro {
  id: number;
  name: string;
}

export interface ConciliacaoFormLP {
  id: number;
  /** Código opcional configurado pelo admin; quando presente substitui `#id` no UI. */
  codigo?: string | null;
  description: string;
  type: string;
  /** Visibilidade sob a ótica da company ativa (pivot N:N). */
  scope?: 'global' | 'own' | 'inherited' | 'other';
  /** Ids das companies ligadas à categoria — útil para tooltip/debug. */
  company_ids?: number[];
}

export interface ConciliacaoFormFormaPagamento {
  id: number;
  nome: string;
  codigo: string;
}

export interface ConciliacaoFormParceiro {
  id: number;
  nome: string;
  natureza: string;
}

export interface ConciliacaoFormEntidadeBanco {
  id: number;
  nome: string;
  tipo: string;
  /** Logo do banco (URL relativa ou absoluta). */
  logo_url?: string | null;
  banco_nome?: string | null;
  agencia?: string | null;
  conta?: string | null;
  /** Valor interno: corrente, poupanca, aplicacao, etc. */
  account_type?: string | null;
  /** Rótulo amigável para exibir no UI (ex.: Poupança vs Conta Corrente no mesmo nº). */
  account_type_label?: string | null;
}

export interface ConciliacaoFormOptions {
  centros: ConciliacaoFormCentro[];
  lancamentos_padrao: ConciliacaoFormLP[];
  formas_pagamento: ConciliacaoFormFormaPagamento[];
  parceiros: ConciliacaoFormParceiro[];
  entidades_banco: ConciliacaoFormEntidadeBanco[];
  deposito_lancamento_padrao_id: number | null;
}

interface FetchResult {
  success: boolean;
  items?: ConciliacaoItem[];
  entidade?: EntidadeResumo;
  counts?: ConciliacoesCounts;
  pagination?: ConciliacoesPagination;
  form_options?: ConciliacaoFormOptions;
  tab?: string;
  message?: string;
}

export function useConciliacoesPendentes(entidadeId: string | undefined) {
  const [tab, setTab] = useState<ConciliacoesTabKey>('all');
  const [page, setPage] = useState(1);
  const [items, setItems] = useState<ConciliacaoItem[]>([]);
  const [entidade, setEntidade] = useState<EntidadeResumo | null>(null);
  const [counts, setCounts] = useState<ConciliacoesCounts | null>(null);
  const [pagination, setPagination] = useState<ConciliacoesPagination | null>(null);
  const [formOptions, setFormOptions] = useState<ConciliacaoFormOptions | null>(null);
  const [loading, setLoading] = useState(true);
  const [loadingMore, setLoadingMore] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const fetchPage = useCallback(
    async (nextPage: number, append: boolean) => {
      if (!entidadeId) return;

      const url = `/app/financeiro/banco/entidade/${entidadeId}/conciliacoes-pendentes?tab=${tab}&page=${nextPage}`;
      const res = await fetch(url, {
        headers: {
          Accept: 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
      });

      const data = (await res.json()) as FetchResult;
      if (!res.ok || !data.success) {
        throw new Error(data.message ?? `Erro ${res.status}`);
      }

      const newItems = data.items ?? [];
      if (append) {
        setItems((prev) => {
          const ids = new Set(prev.map((i) => i.statement.id));
          const merged = [...prev];
          for (const it of newItems) {
            if (!ids.has(it.statement.id)) merged.push(it);
          }
          return merged;
        });
      } else {
        setItems(newItems);
      }

      setEntidade(data.entidade ?? null);
      setCounts(data.counts ?? null);
      setPagination(data.pagination ?? null);
      if (data.form_options) {
        setFormOptions(data.form_options);
      }
      setPage(nextPage);
    },
    [entidadeId, tab],
  );

  const refresh = useCallback(async () => {
    if (!entidadeId) return;
    setLoading(true);
    setError(null);
    try {
      await fetchPage(1, false);
    } catch (e) {
      setError(e instanceof Error ? e.message : 'Erro ao carregar');
    } finally {
      setLoading(false);
    }
  }, [entidadeId, fetchPage]);

  useEffect(() => {
    if (!entidadeId) {
      setLoading(false);
      setItems([]);
      setFormOptions(null);
      return;
    }
    setItems([]);
    setLoading(true);
    setError(null);
    setPage(1);
    fetchPage(1, false)
      .catch((e) => setError(e instanceof Error ? e.message : 'Erro ao carregar'))
      .finally(() => setLoading(false));
  }, [entidadeId, tab, fetchPage]);

  const loadMore = useCallback(async () => {
    if (!pagination?.has_more || loadingMore || !entidadeId) return;
    setLoadingMore(true);
    setError(null);
    try {
      await fetchPage(page + 1, true);
    } catch (e) {
      setError(e instanceof Error ? e.message : 'Erro ao carregar');
    } finally {
      setLoadingMore(false);
    }
  }, [entidadeId, fetchPage, page, pagination?.has_more, loadingMore]);

  return {
    tab,
    setTab: (t: ConciliacoesTabKey) => {
      setTab(t);
      setPage(1);
    },
    items,
    entidade,
    counts,
    pagination,
    formOptions,
    loading,
    loadingMore,
    error,
    refresh,
    loadMore,
  };
}
