"use client"

import * as React from "react"
import * as SelectPrimitive from "@radix-ui/react-select"
import { Check, ChevronDown, ChevronUp, Plus, Search } from "lucide-react"

import { cn } from "@/lib/utils"
import { Input } from "@/Components/ui/input"

const Select = SelectPrimitive.Root

const SelectGroup = SelectPrimitive.Group

const SelectValue = SelectPrimitive.Value

const SelectTrigger = React.forwardRef<
  React.ElementRef<typeof SelectPrimitive.Trigger>,
  React.ComponentPropsWithoutRef<typeof SelectPrimitive.Trigger>
>(({ className, children, ...props }, ref) => (
  <SelectPrimitive.Trigger
    ref={ref}
    className={cn(
      "flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background data-[placeholder]:text-muted-foreground focus:outline-none focus:ring-0 focus:ring-offset-0 focus-visible:ring-0 focus-visible:ring-offset-0 disabled:cursor-not-allowed disabled:opacity-50 [&>span]:line-clamp-1",
      className
    )}
    {...props}
  >
    {children}
    <SelectPrimitive.Icon asChild>
      <ChevronDown className="h-4 w-4 opacity-50" />
    </SelectPrimitive.Icon>
  </SelectPrimitive.Trigger>
))
SelectTrigger.displayName = SelectPrimitive.Trigger.displayName

const SelectScrollUpButton = React.forwardRef<
  React.ElementRef<typeof SelectPrimitive.ScrollUpButton>,
  React.ComponentPropsWithoutRef<typeof SelectPrimitive.ScrollUpButton>
>(({ className, ...props }, ref) => (
  <SelectPrimitive.ScrollUpButton
    ref={ref}
    className={cn(
      "flex cursor-default items-center justify-center py-1",
      className
    )}
    {...props}
  >
    <ChevronUp className="h-4 w-4" />
  </SelectPrimitive.ScrollUpButton>
))
SelectScrollUpButton.displayName = SelectPrimitive.ScrollUpButton.displayName

const SelectScrollDownButton = React.forwardRef<
  React.ElementRef<typeof SelectPrimitive.ScrollDownButton>,
  React.ComponentPropsWithoutRef<typeof SelectPrimitive.ScrollDownButton>
>(({ className, ...props }, ref) => (
  <SelectPrimitive.ScrollDownButton
    ref={ref}
    className={cn(
      "flex cursor-default items-center justify-center py-1",
      className
    )}
    {...props}
  >
    <ChevronDown className="h-4 w-4" />
  </SelectPrimitive.ScrollDownButton>
))
SelectScrollDownButton.displayName =
  SelectPrimitive.ScrollDownButton.displayName

const SelectContent = React.forwardRef<
  React.ElementRef<typeof SelectPrimitive.Content>,
  React.ComponentPropsWithoutRef<typeof SelectPrimitive.Content>
>(({ className, children, position = "popper", ...props }, ref) => (
  <SelectPrimitive.Portal>
    <SelectPrimitive.Content
      ref={ref}
      className={cn(
        "relative z-50 max-h-[--radix-select-content-available-height] min-w-[8rem] overflow-y-auto overflow-x-hidden rounded-md border bg-popover text-popover-foreground shadow-md data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2 origin-[--radix-select-content-transform-origin]",
        position === "popper" &&
          "data-[side=bottom]:translate-y-1 data-[side=left]:-translate-x-1 data-[side=right]:translate-x-1 data-[side=top]:-translate-y-1",
        className
      )}
      position={position}
      {...props}
    >
      <SelectScrollUpButton />
      <SelectPrimitive.Viewport
        className={cn(
          "p-1",
          position === "popper" &&
            "h-[var(--radix-select-trigger-height)] w-full min-w-[var(--radix-select-trigger-width)]"
        )}
      >
        {children}
      </SelectPrimitive.Viewport>
      <SelectScrollDownButton />
    </SelectPrimitive.Content>
  </SelectPrimitive.Portal>
))
SelectContent.displayName = SelectPrimitive.Content.displayName

const SelectLabel = React.forwardRef<
  React.ElementRef<typeof SelectPrimitive.Label>,
  React.ComponentPropsWithoutRef<typeof SelectPrimitive.Label>
>(({ className, ...props }, ref) => (
  <SelectPrimitive.Label
    ref={ref}
    className={cn("py-1.5 pl-8 pr-2 text-sm font-semibold", className)}
    {...props}
  />
))
SelectLabel.displayName = SelectPrimitive.Label.displayName

interface SelectHeaderProps extends React.HTMLAttributes<HTMLDivElement> {
  /** Valor do input de busca */
  searchValue?: string
  /** Função chamada quando o valor de busca muda */
  onSearchChange?: (value: string) => void
  /** Placeholder do input de busca */
  searchPlaceholder?: string
  /** Se deve mostrar o campo de busca */
  showSearch?: boolean
}

const SelectHeader = React.forwardRef<HTMLDivElement, SelectHeaderProps>(
  ({ 
    className, 
    searchValue = "",
    onSearchChange,
    searchPlaceholder = "Pesquisar...",
    showSearch = true,
    ...props 
  }, ref) => {
    const inputRef = React.useRef<HTMLInputElement>(null)

    const handleSearchChange = (e: React.ChangeEvent<HTMLInputElement>) => {
      const value = e.target.value
      onSearchChange?.(value)
    }

    const handleMouseDown = (e: React.MouseEvent) => {
      // Apenas previne que o Select feche ao clicar no container
      e.stopPropagation()
    }

    const handleInputMouseDown = (e: React.MouseEvent<HTMLInputElement>) => {
      // Permite que o input receba o foco normalmente, apenas previne fechamento do Select
      e.stopPropagation()
      // Não previne o default para permitir foco
    }

    const handleInputPointerDown = (e: React.PointerEvent<HTMLInputElement>) => {
      // Garante que eventos de pointer também não fechem o Select
      e.stopPropagation()
    }

    const handleKeyDown = (e: React.KeyboardEvent<HTMLInputElement>) => {
      // Previne que o Select feche, mas permite digitação
      e.stopPropagation()
      // Não previne o default para permitir digitação normal
    }

    if (!showSearch) return null

    return (
      <div
        ref={ref}
        className={cn("border-b border-border p-2 sticky top-0 z-10 bg-popover", className)}
        {...props}
        onMouseDown={handleMouseDown}
      >
        <div className="relative">
          <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground pointer-events-none z-10" />
          <Input
            ref={inputRef}
            type="text"
            value={searchValue || ""}
            onChange={handleSearchChange}
            placeholder={searchPlaceholder}
            className="pl-9 h-9 text-sm focus-visible:ring-0 focus-visible:ring-offset-0"
            onMouseDown={handleInputMouseDown}
            onPointerDown={handleInputPointerDown}
            onKeyDown={handleKeyDown}
            onClick={(e) => {
              e.stopPropagation()
            }}
            autoComplete="off"
            data-lpignore="true"
          />
        </div>
      </div>
    )
  }
)
SelectHeader.displayName = "SelectHeader"

const SelectItem = React.forwardRef<
  React.ElementRef<typeof SelectPrimitive.Item>,
  React.ComponentPropsWithoutRef<typeof SelectPrimitive.Item>
>(({ className, children, ...props }, ref) => (
  <SelectPrimitive.Item
    ref={ref}
    className={cn(
      "relative flex w-full cursor-default select-none items-center rounded-sm py-1.5 pl-8 pr-2 text-sm outline-none focus:bg-accent focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50",
      className
    )}
    {...props}
  >
    <span className="absolute left-2 flex h-3.5 w-3.5 items-center justify-center">
      <SelectPrimitive.ItemIndicator>
        <Check className="h-4 w-4" />
      </SelectPrimitive.ItemIndicator>
    </span>

    <SelectPrimitive.ItemText>{children}</SelectPrimitive.ItemText>
  </SelectPrimitive.Item>
))
SelectItem.displayName = SelectPrimitive.Item.displayName

const SelectSeparator = React.forwardRef<
  React.ElementRef<typeof SelectPrimitive.Separator>,
  React.ComponentPropsWithoutRef<typeof SelectPrimitive.Separator>
>(({ className, ...props }, ref) => (
  <SelectPrimitive.Separator
    ref={ref}
    className={cn("-mx-1 my-1 h-px bg-muted", className)}
    {...props}
  />
))
SelectSeparator.displayName = SelectPrimitive.Separator.displayName

interface SelectFooterProps extends React.HTMLAttributes<HTMLDivElement> {
  /** Texto do botão de adicionar */
  addLabel?: string
  /** Função chamada ao clicar no botão */
  onAdd?: () => void
  /** Ícone customizado (padrão: Plus) */
  icon?: React.ReactNode
}

const SelectFooter = React.forwardRef<HTMLDivElement, SelectFooterProps>(
  ({ className, addLabel = "Adicionar novo item", onAdd, icon, ...props }, ref) => (
    <div
      ref={ref}
      className={cn("border-t border-border mt-1", className)}
      {...props}
    >
      <button
        type="button"
        onClick={(e) => {
          e.preventDefault()
          e.stopPropagation()
          onAdd?.()
        }}
        className={cn(
          "flex w-full items-center gap-2 px-2 py-2 text-sm text-blue-600 hover:bg-accent hover:text-accent-foreground rounded-sm transition-colors",
          "dark:text-blue-400"
        )}
      >
        {icon || <Plus className="h-4 w-4" />}
        <span>{addLabel}</span>
      </button>
    </div>
  )
)
SelectFooter.displayName = "SelectFooter"

export {
  Select,
  SelectGroup,
  SelectValue,
  SelectTrigger,
  SelectContent,
  SelectLabel,
  SelectItem,
  SelectSeparator,
  SelectFooter,
  SelectHeader,
  SelectScrollUpButton,
  SelectScrollDownButton,
}
