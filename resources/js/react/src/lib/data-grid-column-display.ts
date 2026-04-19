import type { LucideIcon } from 'lucide-react';
import {
  AlignLeft,
  AlertCircle,
  BadgePercent,
  Banknote,
  Building2,
  Calendar,
  CalendarCheck,
  CalendarClock,
  CalendarDays,
  CircleDollarSign,
  Columns3,
  FileType,
  GitBranch,
  Hash,
  Info,
  Landmark,
  Percent,
  Tag,
  UserRound,
} from 'lucide-react';

/** Rótulos amigáveis para IDs de coluna comuns no financeiro (pt-BR). */
const COLUMN_LABEL_PT: Record<string, string> = {
  id: 'ID',
  vencimento: 'Vencimento',
  descricao: 'Descrição',
  valor: 'Valor',
  valor_restante: 'Valor restante',
  valor_pago: 'Valor pago',
  situacao: 'Situação',
  origem: 'Origem',
  parceiro: 'Parceiro',
  categoria: 'Categoria',
  centro_custo: 'Centro de Custo',
  conta: 'Conta',
  data: 'Data',
  data_competencia: 'Data de Competência',
  data_pagamento: 'Data de Pagamento',
  juros: 'Juros',
  multa: 'Multa',
  desconto: 'Desconto',
  numero_documento: 'Número do Documento',
  tipo_documento: 'Tipo de Documento',
};

/** Ajuste de grafia (minúsculas) para palavras comuns em IDs snake_case. */
const WORD_PT: Record<string, string> = {
  competencia: 'competência',
  pagamento: 'pagamento',
  descricao: 'descrição',
  situacao: 'situação',
  documento: 'documento',
  numero: 'número',
  restante: 'restante',
  custo: 'custo',
};

const CONNECTORS = new Set(['de', 'da', 'do', 'das', 'dos', 'e', 'em', 'na', 'no', 'nas', 'nos']);

function capitalizeWord(raw: string, forceTitle: boolean): string {
  const w = raw.toLowerCase();
  if (!forceTitle && CONNECTORS.has(w)) return w;
  const base = WORD_PT[w] ?? w;
  return base.charAt(0).toLocaleUpperCase('pt-BR') + base.slice(1);
}

function fallbackLabelFromId(columnId: string): string {
  const parts = columnId.split('_').filter(Boolean);
  if (parts.length === 0) return columnId;

  const first = parts[0].toLowerCase();
  if (first === 'data' && parts.length >= 2) {
    const tail = parts
      .slice(1)
      .map((p) => capitalizeWord(p, true))
      .join(' ');
    return `Data de ${tail}`;
  }

  return parts.map((p, i) => capitalizeWord(p, i === 0)).join(' ');
}

/** Rótulo para o menu de colunas: usa meta.headerTitle se definido; senão mapa + fallback. */
export function getDataGridColumnVisibilityLabel(columnId: string, headerTitle?: string): string {
  if (headerTitle && headerTitle.trim() !== '' && headerTitle !== columnId) {
    return headerTitle;
  }
  return COLUMN_LABEL_PT[columnId] ?? fallbackLabelFromId(columnId);
}

const COLUMN_ICON: Record<string, LucideIcon> = {
  id: Hash,
  vencimento: CalendarDays,
  descricao: AlignLeft,
  valor: Banknote,
  valor_restante: CircleDollarSign,
  valor_pago: Banknote,
  situacao: Info,
  origem: GitBranch,
  parceiro: UserRound,
  categoria: Tag,
  centro_custo: Building2,
  conta: Landmark,
  data: Calendar,
  data_competencia: CalendarClock,
  data_pagamento: CalendarCheck,
  juros: Percent,
  multa: AlertCircle,
  desconto: BadgePercent,
  numero_documento: Hash,
  tipo_documento: FileType,
};

export function getDataGridColumnVisibilityIcon(columnId: string): LucideIcon {
  return COLUMN_ICON[columnId] ?? Columns3;
}
