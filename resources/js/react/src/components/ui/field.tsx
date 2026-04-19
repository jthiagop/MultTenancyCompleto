import * as React from 'react';
import { cn } from '@/lib/utils';

/**
 * Agrupa visualmente múltiplos campos de formulário com espaçamento uniforme.
 *
 * @example
 * <FieldGroup>
 *   <Field>
 *     <Label htmlFor="name">Nome</Label>
 *     <Input id="name" />
 *   </Field>
 * </FieldGroup>
 */
function FieldGroup({ className, ...props }: React.HTMLAttributes<HTMLDivElement>) {
  return (
    <div
      data-slot="field-group"
      className={cn('flex flex-col gap-4', className)}
      {...props}
    />
  );
}

/**
 * Contêiner de um campo único — empilha label + input/controle com espaçamento correto.
 * Ao lado de um `<Label>` e `<Input>` produz o layout padrão de formulário.
 */
function Field({ className, ...props }: React.HTMLAttributes<HTMLDivElement>) {
  return (
    <div
      data-slot="field"
      className={cn('flex flex-col gap-1.5', className)}
      {...props}
    />
  );
}

export { Field, FieldGroup };
