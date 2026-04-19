import { type HTMLAttributes, forwardRef } from 'react';
import { cn } from '@/lib/utils';

/**
 * Agrupa botões side-by-side colapsando as bordas internas.
 * Basta envolver <Button> filhos com este componente.
 */
const ButtonGroup = forwardRef<HTMLDivElement, HTMLAttributes<HTMLDivElement>>(
  ({ className, ...props }, ref) => (
    <div
      ref={ref}
      className={cn(
        'inline-flex items-center',
        // Remove border-radius dos filhos intermediários e ajusta os extremos
        '[&>*]:rounded-none',
        '[&>*:first-child]:rounded-s-md',
        '[&>*:last-child]:rounded-e-md',
        // Colapsa bordas duplicadas
        '[&>*:not(:first-child)]:-ml-px',
        // Garante z-index correto no hover/focus para a borda ficar visível
        '[&>*:hover]:z-10 [&>*:focus-visible]:z-10',
        className,
      )}
      {...props}
    />
  ),
);
ButtonGroup.displayName = 'ButtonGroup';

export { ButtonGroup };
