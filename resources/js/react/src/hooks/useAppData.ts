interface AppUser {
  id: number;
  name: string;
  email: string;
  avatar_url: string | null;
}

interface AppModule {
  name: string;
  description: string;
  key: string;
  url: string | null;
  icon: string | null;
  icon_class: string | null;
}

/** Espelha Company do Laravel: id, name, razao_social, cnpj, avatar (gerado pelo ReactAppController). */
export interface AppCompany {
  id: number;
  name: string;
  razao_social: string | null;
  cnpj: string | null;
  email: string | null;
  /**
   * Tipo (matriz | filial). Usado pelo picker de disponibilidade de categoria
   * e por UI que aplica tratamento diferente para matriz vs. filial.
   */
  type?: string | null;
  /** ID da matriz desta company (null quando a própria é matriz). */
  parent_id?: number | null;
  /** URL resolvida para o avatar (/file/…) ou null. */
  avatar_url: string | null;
  /** Endereço principal (hasOne addresses no Company). */
  address: {
    cep: string | null;
    rua: string | null;
    bairro: string | null;
    numero: string | null;
    cidade: string | null;
    uf: string | null;
  } | null;
}

interface FormSelectDataRaw {
  parceiros: Array<{ id: string; nome: string; natureza: string }>;
  entidades: Array<{
    id: string;
    label: string;
    tipo: string;
    logo: string | null;
    /** Saldo efetivo via movimentações (mesma regra que `EntidadeFinanceira::calculateBalance`) */
    saldo_atual?: number;
  }>;
  categorias: Array<{
    id: string;
    codigo?: string | null;
    description: string;
    type: string;
    scope?: 'global' | 'own' | 'inherited' | 'other';
    company_ids?: string[];
  }>;
  centrosCusto: Array<{ id: string; code: string; name: string }>;
  formasPagamento: Array<{ id: string; codigo: string; nome: string }>;
  filiais?: Array<{ id: string; name: string; type: string; avatar_url: string | null }>;
}

interface AppData {
  user: AppUser;
  /** ID da empresa ativa na sessão (session('active_company_id')). */
  companyId: number | null;
  /** Todas as empresas às quais o usuário tem acesso (espelha $allCompanies do userMenu.blade.php). */
  companies: AppCompany[];
  csrfToken: string;
  /** POST para encerrar sessão (igual ao form logout do Blade) */
  logoutUrl: string;
  baseUrl: string;
  modules: AppModule[];
  formSelectData?: FormSelectDataRaw;
  hasHorariosMissa: boolean;
  /** Espelha permissões do Blade (Spatie): menu “Recalcular saldos”, etc. */
  hasAdminRole?: boolean;
  /** Espelha @role('global'): ex. Formas de Recebimento. */
  hasGlobalRole?: boolean;  /** Spatie can('users.index') — exibe menu Cadastros > Usuários. */
  canUsersIndex?: boolean;
  /** Spatie can('company.index') — exibe menu Cadastros > Módulos e Permissões. */
  canCompanyIndex?: boolean;
  /** Spatie can('contabilidade.index') — acesso à página de Contabilidade no React. */
  canContabilidadeIndex?: boolean;
  /** Spatie can('financeiro.index') — exibe menu Financeiro na sidebar. */
  canFinanceiroIndex?: boolean;
  /** Spatie can('notafiscal.index') — exibe item Nota Fiscal no menu Financeiro. */
  canNotafiscalIndex?: boolean;
  /** Spatie can('secretary.index') — acesso à página de Secretaria no React. */
  canSecretaryIndex?: boolean;
  /** Spatie can('fieis.index') — acesso à página de Cadastro de Fiéis no React. */
  canFieisIndex?: boolean;
}

declare global {
  interface Window {
    __APP_DATA__?: AppData;
  }
}

export function useAppData(): AppData {
  const data = window.__APP_DATA__;

  if (!data) {
    // Fallback para desenvolvimento local sem o Blade
    return {
      user: { id: 0, name: 'Usuário', email: '', avatar_url: null },
      companyId: null,
      companies: [],
      csrfToken: '',
      logoutUrl: '/logout',
      baseUrl: '/app/',
      modules: [],
      hasHorariosMissa: false,
      hasAdminRole: false,
      hasGlobalRole: false,
      canUsersIndex: false,
      canCompanyIndex: false,
      canContabilidadeIndex: false,
      canFinanceiroIndex: false,
      canNotafiscalIndex: false,
      canSecretaryIndex: false,
      canFieisIndex: false,
    };
  }

  return data;
}
