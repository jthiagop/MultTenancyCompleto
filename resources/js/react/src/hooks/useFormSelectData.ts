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

/**
 * Categoria (lancamento padrão) com metadados do pivot N:N.
 *
 * - `scope` resume a visibilidade da categoria sob a ótica da company ativa:
 *     - 'global'     : pivot vazio → visível em todo o tenant
 *     - 'own'        : a company ativa está no pivot
 *     - 'inherited'  : a matriz da company ativa está no pivot (herança)
 *     - 'other'      : fallback (não deveria aparecer quando filtrado)
 * - `company_ids` contém os ids ligados à categoria (útil para tooltip/debug).
 * - `codigo` é o prefixo opcional exibido antes do label.
 */
export interface CategoriaOption {
  id: string;
  codigo?: string | null;
  description: string;
  type: string;
  scope?: 'global' | 'own' | 'inherited' | 'other';
  company_ids?: string[];
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
      // O backend já filtra por tipo (entrada|saida|ambos). Mantemos apenas um
      // filtro defensivo para incluir 'ambos' caso a resposta venha completa.
      categorias:      raw.categorias.filter(c => c.type === tipoLp || c.type === 'ambos'),
      centrosCusto:    raw.centrosCusto,
      formasPagamento: raw.formasPagamento,
      filiais,
    };
  }, [formSelectData, tipoQuery]);

  return { data, loading: false, error: null };
}
