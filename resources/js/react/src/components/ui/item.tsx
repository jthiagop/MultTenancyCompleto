'use client';

import * as React from 'react';
import { Slot } from 'radix-ui';
import { cn } from '@/lib/utils';
import { cva, type VariantProps } from 'class-variance-authority';

const itemVariants = cva(
  'flex items-center gap-3 rounded-lg text-sm transition-colors',
  {
    variants: {
      variant: {
        default: '',
        outline:
          'border border-border bg-background p-3 hover:bg-muted/50',
        ghost: 'p-3 hover:bg-muted/50',
      },
    },
    defaultVariants: {
      variant: 'default',
    },
  },
);

interface ItemProps
  extends React.HTMLAttributes<HTMLDivElement>,
    VariantProps<typeof itemVariants> {
  asChild?: boolean;
}

function Item({ className, variant, asChild, ...props }: ItemProps) {
  const Comp = asChild ? Slot.Root : 'div';
  return (
    <Comp
      data-slot="item"
      className={cn(itemVariants({ variant }), className)}
      {...props}
    />
  );
}

function ItemGroup({ className, ...props }: React.HTMLAttributes<HTMLDivElement>) {
  return (
    <div
      data-slot="item-group"
      role="list"
      className={cn('flex flex-col gap-2', className)}
      {...props}
    />
  );
}

const itemMediaVariants = cva(
  'shrink-0 flex items-center justify-center overflow-hidden',
  {
    variants: {
      variant: {
        default: 'size-10 rounded-lg bg-muted',
        image: 'size-10 rounded-lg',
        icon: 'size-9 rounded-md bg-muted',
      },
    },
    defaultVariants: {
      variant: 'default',
    },
  },
);

function ItemMedia({
  className,
  variant,
  ...props
}: React.HTMLAttributes<HTMLDivElement> &
  VariantProps<typeof itemMediaVariants>) {
  return (
    <div
      data-slot="item-media"
      className={cn(itemMediaVariants({ variant }), className)}
      {...props}
    />
  );
}

function ItemContent({ className, ...props }: React.HTMLAttributes<HTMLDivElement>) {
  return (
    <div
      data-slot="item-content"
      className={cn('flex flex-1 flex-col gap-0.5 min-w-0', className)}
      {...props}
    />
  );
}

function ItemTitle({ className, ...props }: React.HTMLAttributes<HTMLDivElement>) {
  return (
    <div
      data-slot="item-title"
      className={cn('text-sm font-medium leading-tight', className)}
      {...props}
    />
  );
}

function ItemDescription({ className, ...props }: React.HTMLAttributes<HTMLDivElement>) {
  return (
    <div
      data-slot="item-description"
      className={cn('text-xs text-muted-foreground', className)}
      {...props}
    />
  );
}

export { Item, ItemContent, ItemDescription, ItemGroup, ItemMedia, ItemTitle };
