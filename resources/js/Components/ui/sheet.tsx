"use client"

import * as React from "react"
import * as SheetPrimitive from "@radix-ui/react-dialog"
import { cva, type VariantProps } from "class-variance-authority"
import { X } from "lucide-react"

import { cn } from "@/lib/utils"
import { SelectItem } from "@/Components/ui/select"

const Sheet = SheetPrimitive.Root

const SheetTrigger = SheetPrimitive.Trigger

const SheetClose = SheetPrimitive.Close

const SheetPortal = SheetPrimitive.Portal

const SheetOverlay = React.forwardRef<
  React.ElementRef<typeof SheetPrimitive.Overlay>,
  React.ComponentPropsWithoutRef<typeof SheetPrimitive.Overlay>
>(({ className, ...props }, ref) => (
  <SheetPrimitive.Overlay
    className={cn(
      "fixed inset-0 z-50 bg-black/80  data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0",
      className
    )}
    {...props}
    ref={ref}
  />
))
SheetOverlay.displayName = SheetPrimitive.Overlay.displayName

const sheetVariants = cva(
  "fixed z-50 gap-4 bg-background p-6 shadow-lg transition ease-in-out data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:duration-300 data-[state=open]:duration-500",
  {
    variants: {
      side: {
        top: "inset-x-0 top-0 border-b data-[state=closed]:slide-out-to-top data-[state=open]:slide-in-from-top",
        bottom:
          "inset-x-0 bottom-0 border-t data-[state=closed]:slide-out-to-bottom data-[state=open]:slide-in-from-bottom",
        left: "inset-y-0 left-0 h-full w-3/4 border-r data-[state=closed]:slide-out-to-left data-[state=open]:slide-in-from-left sm:max-w-sm",
        right:
          "inset-y-0 right-0 h-full w-3/4  border-l data-[state=closed]:slide-out-to-right data-[state=open]:slide-in-from-right sm:max-w-sm",
      },
    },
    defaultVariants: {
      side: "right",
    },
  }
)

interface SheetContentProps
  extends React.ComponentPropsWithoutRef<typeof SheetPrimitive.Content>,
    VariantProps<typeof sheetVariants> {}

const SheetContent = React.forwardRef<
  React.ElementRef<typeof SheetPrimitive.Content>,
  SheetContentProps
>(({ side = "right", className, children, ...props }, ref) => (
  <SheetPortal>
    <SheetOverlay />
    <SheetPrimitive.Content
      ref={ref}
      className={cn(sheetVariants({ side }), className)}
      {...props}
    >
        {children}
      <SheetPrimitive.Close className="absolute right-4 top-4 rounded-sm opacity-70 ring-offset-background transition-opacity hover:opacity-100 focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:pointer-events-none data-[state=open]:bg-secondary">
        <X className="h-4 w-4" />
        <span className="sr-only">Close</span>
      </SheetPrimitive.Close>
    </SheetPrimitive.Content>
  </SheetPortal>
))
SheetContent.displayName = SheetPrimitive.Content.displayName

const SheetHeader = ({
  className,
  ...props
}: React.HTMLAttributes<HTMLDivElement>) => (
  <div
    className={cn(
      "flex flex-col space-y-2 text-center sm:text-left",
      className
    )}
    {...props}
  />
)
SheetHeader.displayName = "SheetHeader"

const SheetFooter = ({
  className,
  ...props
}: React.HTMLAttributes<HTMLDivElement>) => (
  <div
    className={cn(
      "px-6 py-4 border-t bg-white dark:bg-gray-800 flex items-center justify-between",
      className
    )}
    {...props}
  />
)
SheetFooter.displayName = "SheetFooter"

const SheetTitle = React.forwardRef<
  React.ElementRef<typeof SheetPrimitive.Title>,
  React.ComponentPropsWithoutRef<typeof SheetPrimitive.Title>
>(({ className, ...props }, ref) => (
  <SheetPrimitive.Title
    ref={ref}
    className={cn("text-lg font-semibold text-foreground", className)}
    {...props}
  />
))
SheetTitle.displayName = SheetPrimitive.Title.displayName

const SheetDescription = React.forwardRef<
  React.ElementRef<typeof SheetPrimitive.Description>,
  React.ComponentPropsWithoutRef<typeof SheetPrimitive.Description>
>(({ className, ...props }, ref) => (
  <SheetPrimitive.Description
    ref={ref}
    className={cn("text-sm text-muted-foreground", className)}
    {...props}
  />
))
SheetDescription.displayName = SheetPrimitive.Description.displayName

/**
 * Hook utilitário para filtrar e renderizar itens de Select
 * 
 * @template T - Tipo do item a ser filtrado
 * @param items - Array de itens a serem filtrados
 * @param searchValue - Valor da pesquisa atual
 * @param options - Configurações opcionais
 * @returns JSX.Element com os itens filtrados ou mensagem de vazio
 * 
 * @example
 * ```tsx
 * import { renderFilteredSelectItems } from '@/Components/ui/sheet'
 * 
 * const items = renderFilteredSelectItems(
 *   entidadesFinanceiras,
 *   searchEntidadeFinanceira,
 *   {
 *     getLabel: (item) => item.nome,
 *     getValue: (item) => item.id.toString(),
 *     getKey: (item) => item.id,
 *     emptyMessage: 'Nenhuma entidade encontrada',
 *     noItemsMessage: 'Nenhuma entidade disponível'
 *   }
 * )
 * ```
 */
export function renderFilteredSelectItems<T>(
  items: T[],
  searchValue: string,
  options: {
    /** Função para obter o label do item */
    getLabel: (item: T) => string
    /** Função para obter o value do item */
    getValue: (item: T) => string
    /** Função para obter a key do item */
    getKey: (item: T) => string | number
    /** Mensagem quando não há itens após filtrar */
    emptyMessage?: string
    /** Mensagem quando não há itens disponíveis */
    noItemsMessage?: string
    /** Função customizada para filtrar (opcional) */
    filterFn?: (item: T, search: string) => boolean
  }
): JSX.Element {
  const {
    getLabel,
    getValue,
    getKey,
    emptyMessage = 'Nenhum item encontrado',
    noItemsMessage = 'Nenhum item disponível',
    filterFn
  } = options

  // Aplica o filtro
  const filteredItems = searchValue
    ? items.filter((item) => {
        if (filterFn) {
          return filterFn(item, searchValue)
        }
        return getLabel(item).toLowerCase().includes(searchValue.toLowerCase())
      })
    : items

  // Retorna os itens filtrados ou mensagem de vazio
  if (filteredItems.length > 0) {
    return (
      <>
        {filteredItems.map((item) => (
          <SelectItem key={getKey(item)} value={getValue(item)}>
            {getLabel(item)}
          </SelectItem>
        ))}
      </>
    )
  }

  // Mensagem quando não há resultados
  return (
    <SelectItem value="" disabled>
      {searchValue ? emptyMessage : noItemsMessage}
    </SelectItem>
  )
}


export {
  Sheet,
  SheetPortal,
  SheetOverlay,
  SheetTrigger,
  SheetClose,
  SheetContent,
  SheetHeader,
  SheetFooter,
  SheetTitle,
  SheetDescription,
}
