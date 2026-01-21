/**
 * FormSheet - Componente reutilizável de Sheet para formulários
 *
 * Componente que encapsula o Sheet do Shadcn UI com header e footer padronizados
 * para uso em formulários do sistema.
 */

import * as React from "react"
import {
    Sheet,
    SheetContent,
    SheetHeader,
    SheetTitle,
    SheetFooter,
} from '@/Components/ui/sheet'
import { Button } from '@/Components/ui/button'
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/Components/ui/dropdown-menu'
import { X, ChevronDown, VolumeOffIcon, CheckIcon, AlertTriangleIcon, UserRoundXIcon, ShareIcon, CopyIcon, TrashIcon, BookCopy } from 'lucide-react'
import { ButtonGroup } from "./ui/button-group"
import { DropdownMenuSeparator } from "./ui/dropdown-menu"

interface FormSheetProps {
    /** Controla se o Sheet está aberto */
    open: boolean
    /** Função chamada quando o Sheet deve ser fechado */
    onOpenChange: (open: boolean) => void
    /** Título exibido no header */
    title: string
    /** Conteúdo do formulário */
    children: React.ReactNode
    /** Função chamada ao clicar em Voltar */
    onBack?: () => void
    /** Função chamada ao clicar em Salvar */
    onSave?: () => void
    /** Função chamada ao clicar em "Salvar e Clonar" */
    onSaveAndClone?: () => void
    /** Função chamada ao clicar em "Salvar e Limpar" */
    onSaveAndClear?: () => void
    /** Se o botão Salvar está desabilitado */
    saveDisabled?: boolean
    /** Largura do Sheet (padrão: w-full) */
    className?: string
}

export function FormSheet({
    open,
    onOpenChange,
    title,
    children,
    onBack,
    onSave,
    onSaveAndClone,
    onSaveAndClear,
    saveDisabled = false,
    className,
}: FormSheetProps) {
    const handleBack = () => {
        if (onBack) {
            onBack()
        } else {
            onOpenChange(false)
        }
    }

    const hasSaveOptions = onSaveAndClone || onSaveAndClear

    return (
        <Sheet open={open} onOpenChange={onOpenChange}>
            <SheetContent
                side="bottom"
                className={`w-full h-[100vh] flex flex-col p-0 [&>button]:hidden ${className || ''}`}
                style={{ backgroundColor: '#f0f3f7' }}
            >
                {/* Header */}
                <SheetHeader className="px-6 py-4 border-b bg-white">
                    <div className="flex items-center justify-between">
                        <SheetTitle className="text-xl font-semibold">
                            {title}
                        </SheetTitle>
                        <Button
                            variant="ghost"
                            size="icon"
                            onClick={() => onOpenChange(false)}
                            className="h-8 w-8"
                        >
                            <X className="h-4 w-4" />
                            <span className="sr-only">Fechar</span>
                        </Button>
                    </div>
                </SheetHeader>

                {/* Content - Scrollable */}
                <div className="flex-1 overflow-y-auto px-6 py-4">
                    {children}
                </div>
                {/* Footer */}
                <SheetFooter>
                    <Button
                        variant="outline"
                        onClick={handleBack}
                    >
                        Voltar
                    </Button>

                    <ButtonGroup>
      <Button variant="success">Salvar</Button>
      <DropdownMenu>
        <DropdownMenuTrigger asChild>
          <Button variant="success" className="!pl-2">
            <ChevronDown />
          </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end" className="[--radius:1rem]">
          <DropdownMenuGroup>
            <DropdownMenuItem>
              Salvar e Clonar
            </DropdownMenuItem>
            <DropdownMenuItem>
              Salvar e Limpar
            </DropdownMenuItem>
            </DropdownMenuGroup>
        </DropdownMenuContent>
      </DropdownMenu>
    </ButtonGroup>
                </SheetFooter>
            </SheetContent>
        </Sheet>
    )
}

