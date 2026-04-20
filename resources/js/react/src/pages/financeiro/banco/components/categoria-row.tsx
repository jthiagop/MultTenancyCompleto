import { Badge } from '@/components/ui/badge';
import { cn } from '@/lib/utils';
import { categoriaBadges } from './categoria-badges';

export type CategoriaRowData = {
  id: number | string;
  codigo?: string | null;
  description: string;
  type?: string;
  scope?: 'global' | 'own' | 'inherited' | 'other';
  company_ids?: Array<number | string>;
};

/**
 * Render compartilhado para uma linha de categoria em combobox/select.
 * Usa os mesmos badges do `SearchSelect` do `lancamento-drawer.tsx`, garantindo
 * consistência visual entre o drawer principal e o form de conciliação.
 */
export function CategoriaRow({
  categoria,
  className,
}: {
  categoria: CategoriaRowData;
  className?: string;
}) {
  const badges = categoriaBadges({
    id: String(categoria.id),
    codigo: categoria.codigo,
    description: categoria.description,
    type: categoria.type ?? '',
    scope: categoria.scope,
    company_ids: categoria.company_ids?.map(String),
  });

  return (
    <div className={cn('flex min-w-0 items-center gap-2', className)}>
      <span className="truncate">{categoria.description}</span>
      <div className="ml-auto flex shrink-0 items-center gap-1">
        {badges.map((b, i) => (
          <Badge
            key={`${b.label}-${i}`}
            variant={b.variant ?? 'secondary'}
            className={cn('h-5 px-1.5 text-[10px] font-medium', b.className)}
            title={b.title}
          >
            {b.label}
          </Badge>
        ))}
      </div>
    </div>
  );
}
