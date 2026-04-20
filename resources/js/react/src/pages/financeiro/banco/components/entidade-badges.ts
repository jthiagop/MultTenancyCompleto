// Utilitário compartilhado entre os formulários do módulo financeiro para
// renderizar o tipo (Caixa / Conta Corrente / Poupança etc.) e os
// identificadores (agência/conta) de uma entidade financeira dentro do
// `SearchSelect` como badges consistentes.

export const ACCOUNT_TYPE_LABELS: Record<string, string> = {
  corrente:       'Conta Corrente',
  poupanca:       'Poupança',
  aplicacao:      'Aplicação',
  renda_fixa:     'Renda Fixa',
  tesouro_direto: 'Tesouro Direto',
};

export const ACCOUNT_BADGE_CLASS: Record<string, string> = {
  corrente:       'bg-blue-50  text-blue-700  dark:bg-blue-950/40  dark:text-blue-300',
  poupanca:       'bg-green-50 text-green-700 dark:bg-green-950/40 dark:text-green-300',
  aplicacao:      'bg-sky-50   text-sky-700   dark:bg-sky-950/40   dark:text-sky-300',
  renda_fixa:     'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300',
  tesouro_direto: 'bg-gray-100 text-gray-600  dark:bg-gray-800     dark:text-gray-300',
  caixa:          'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300',
};

export type EntidadeBadge = {
  label: string;
  variant?: 'secondary' | 'outline';
  className?: string;
};

export function entidadeBadges(
  tipo: string,
  accountType?: string | null,
  agenciaConta?: string,
): EntidadeBadge[] {
  const typeKey = tipo === 'caixa' ? 'caixa' : (accountType ?? '');
  const colorClass = ACCOUNT_BADGE_CLASS[typeKey] ?? 'bg-muted text-muted-foreground';
  const typeLabel = tipo === 'caixa'
    ? 'Caixa'
    : (ACCOUNT_TYPE_LABELS[accountType ?? ''] ?? 'Banco');

  const result: EntidadeBadge[] = [{ label: typeLabel, className: colorClass }];
  if (agenciaConta) {
    result.push({ label: agenciaConta, variant: 'outline' });
  }
  return result;
}
