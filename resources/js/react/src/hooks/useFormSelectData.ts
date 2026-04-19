import { useMemo } from 'react';
import { useAppData } from './useAppData';

// ── Tipos espelhando a resposta do FinanceiroFormDataController ──────────────

export interface ParceiroOption {
  id: string;
  nome: string;
  natureza: string;
  /** Somente dígitos (match automático Domus IA / drawer Blade) */
  cnpj?: string | null;
  cpf?: string | null;
}

export interface EntidadeOption {
  id: string;
  nome?: string;
  label: string;
  tipo: string;
  account_type?: string | null;
  logo: string | null;
  /** Saldo efetivo (entradas − saídas em `movimentacoes`), igual ao backend `calculateBalance` */
  saldo_atual?: number;
}

export interface CategoriaOption {
  id: string;
  description: string;
  type: string;
}

export interface CentroCustoOption {
  id: string;
  code: string;
  name: string;
}

export interface FormaPagamentoOption {
  id: string;
  codigo: string;
  nome: string;
}

export interface FilialOption {
  id: string;
  name: string;
  type: string;
  avatar_url: string | null;
}

export interface FormSelectData {
  parceiros: ParceiroOption[];
  entidades: EntidadeOption[];
  categorias: CategoriaOption[];
  centrosCusto: CentroCustoOption[];
  formasPagamento: FormaPagamentoOption[];
  filiais: FilialOption[];
}

// ── Hook ──────────────────────────────────────────────────────────────────────

export function useFormSelectData(tipo: 'receita' | 'despesa' | null) {
  const { formSelectData } = useAppData();

  const tipoQuery = tipo;

  const data = useMemo<FormSelectData>(() => {
    const raw = formSelectData ?? {
      parceiros: [],
      entidades: [],
      categorias: [],
      centrosCusto: [],
      formasPagamento: [],
      filiais: [],
    };

    const filiais = raw.filiais ?? [];

    if (!tipoQuery) {
      return { parceiros: [], entidades: raw.entidades, categorias: [], centrosCusto: raw.centrosCusto, formasPagamento: raw.formasPagamento, filiais };
    }

    const natureza = tipoQuery === 'receita' ? 'cliente' : 'fornecedor';
    const tipoLp   = tipoQuery === 'receita' ? 'entrada'  : 'saida';

    return {
      parceiros:       raw.parceiros.filter(p => p.natureza === natureza || p.natureza === 'ambos'),
      entidades:       raw.entidades,
      categorias:      raw.categorias.filter(c => c.type === tipoLp),
      centrosCusto:    raw.centrosCusto,
      formasPagamento: raw.formasPagamento,
      filiais,
    };
  }, [formSelectData, tipoQuery]);

  return { data, loading: false, error: null };
}
