// Utilitário compartilhado entre os consumidores de categorias
// (lancamento-drawer e conciliacao-novo-lancamento-form) para renderizar
// badges uniformes indicando ID, código e escopo de visibilidade.

import type { CategoriaOption } from '@/hooks/useFormSelectData';

type CategoriaBadge = {
  label: string;
  variant?: 'secondary' | 'outline';
  className?: string;
  title?: string;
};

/** Badge que identifica a quem uma categoria pertence (herança/global/própria). */
function scopeBadge(scope: CategoriaOption['scope']): CategoriaBadge | null {
  switch (scope) {
    case 'global':
      return {
        label: 'Global',
        variant: 'outline',
        className: 'bg-slate-50 text-slate-600 dark:bg-slate-900/40 dark:text-slate-300',
        title: 'Categoria disponível em todas as empresas do tenant.',
      };
    case 'inherited':
      return {
        label: 'Matriz',
        variant: 'outline',
        className: 'bg-blue-50 text-blue-700 dark:bg-blue-950/40 dark:text-blue-300',
        title: 'Categoria herdada da matriz.',
      };
    case 'own':
      // Categoria explicitamente ligada a esta company: default (sem badge
      // extra) para não poluir a listagem no caso comum.
      return null;
    default:
      return null;
  }
}

/**
 * Monta a lista de badges para uma categoria a ser exibida num SearchSelect.
 * Sempre inclui um identificador: `CODIGO` (quando existir) ou `#id`.
 * Adiciona um segundo badge descrevendo a origem (escopo) quando não-própria.
 */
export function categoriaBadges(c: CategoriaOption): CategoriaBadge[] {
  const idLabel = c.codigo && c.codigo.trim() !== '' ? c.codigo.trim() : `#${c.id}`;
  const idBadge: CategoriaBadge = {
    label: idLabel,
    variant: 'outline',
  };

  const scope = scopeBadge(c.scope);
  return scope ? [idBadge, scope] : [idBadge];
}
