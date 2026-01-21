/**
 * FormLancamento - Formulário unificado para criação de receitas e despesas
 *
 * Componente reutilizável que se adapta ao tipo de lançamento (receita/despesa)
 * evitando duplicação de código e facilitando manutenção
 */

import * as React from "react"
import { useState } from "react"
import {
    Card,
    CardHeaderFlex,
    CardTitle,
    CardContent,
} from '@/Components/ui/card'
import { Label } from '@/Components/ui/label'
import { Input } from '@/Components/ui/input'
import { Button } from '@/Components/ui/button'
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/Components/ui/popover'
import { Calendar } from '@/Components/ui/calendar'
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
    SelectFooter,
    SelectHeader,
} from '@/Components/ui/select'
import { CalendarDays, CirclePlus, X, Zap, HelpCircle } from 'lucide-react'
import { format } from 'date-fns'
import { InputGroup, InputGroupAddon, InputGroupInput } from "@/Components/ui/input-group"
import { Switch } from "@/Components/ui/switch"
import { Checkbox } from "@/Components/ui/checkbox"
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/Components/ui/tooltip"
import { Tabs, TabsList, TabsTrigger, TabsContent } from "@/Components/ui/tabs"
import { Textarea } from "@/Components/ui/textarea"
import { ModalRecorrencia, RecorrenciaData } from '../Modal/ModalRecorrencia'
import { renderFilteredSelectItems } from "@/Components/ui/sheet"

interface EntidadeFinanceira {
    id: number
    nome: string
    tipo: string
}

interface FormLancamentoProps {
    /** Tipo do lançamento: 'receita' ou 'despesa' */
    tipo: 'receita' | 'despesa'
    /** Dados iniciais do formulário */
    initialData?: Record<string, any>
    /** Função chamada quando os dados mudam */
    onChange?: (data: Record<string, any>) => void
    /** Lista de entidades financeiras */
    entidadesFinanceiras?: EntidadeFinanceira[]
}

export function FormLancamento({ tipo, initialData, onChange, entidadesFinanceiras = [] }: FormLancamentoProps) {
    const [date, setDate] = useState<Date | undefined>(new Date())
    const [open, setOpen] = useState(false)
    const [openVencimento, setOpenVencimento] = useState(false)
    const [searchCentroCusto, setSearchCentroCusto] = useState("")
    const [searchLancamentoPadrao, setSearchLancamentoPadrao] = useState("")
    const [searchEntidadeFinanceira, setSearchEntidadeFinanceira] = useState("")
    const [repetirLancamento, setRepetirLancamento] = useState(false)
    const [modalRecorrenciaOpen, setModalRecorrenciaOpen] = useState(false)
    const [vencimento, setVencimento] = useState<Date | undefined>(new Date())
    const [parcelamento, setParcelamento] = useState<string>("avista")
    const [contaRecebimento, setContaRecebimento] = useState<string>("caixinha")
    const [recebido, setRecebido] = useState(false)
    const [entidadeFinanceiraSelecionada, setEntidadeFinanceiraSelecionada] = useState<string>("")

    const onSubmit = (e: React.FormEvent) => {
        e.preventDefault()
        console.log('Form submitted')
    }

    return (
        <div className="space-y-6">
            <form onSubmit={onSubmit} className="">
                <Card>
                    <CardHeaderFlex className="ml-2">
                        <CardTitle className="text-lg font-semibold text-primary font-sans">
                            Informações do lançamento
                        </CardTitle>
                    </CardHeaderFlex>
                    <CardContent>
                        {/* Primeira linha com 4 campos */}
                        <div className="grid grid-cols-12 gap-4 mt-6">
                            {/* 1. Data de Competência */}
                            <div className="flex flex-col gap-1 col-span-12 sm:col-span-2">
                                <Label htmlFor="date" className="px-1 text-sm" required>
                                    Data de Competência
                                </Label>
                                <Popover open={open} onOpenChange={setOpen}>
                                    <PopoverTrigger asChild>
                                        <Button
                                            variant="outline"
                                            id="date"
                                            className="w-full justify-between font-normal"
                                            type="button"
                                        >
                                            {date ? format(date, "dd/MM/yyyy") : "Selecione a data"}
                                            <CalendarDays className="h-4 w-4 opacity-50" />
                                        </Button>
                                    </PopoverTrigger>
                                    <PopoverContent className="w-auto overflow-hidden p-0" align="start">
                                        <Calendar
                                            mode="single"
                                            selected={date}
                                            captionLayout="dropdown"
                                            onSelect={(selectedDate) => {
                                                setDate(selectedDate)
                                                setOpen(false)
                                            }}
                                        />
                                    </PopoverContent>
                                </Popover>
                            </div>

                            {/* 2. Select Entidade Financeira */}
                            <div className="flex flex-col gap-2 col-span-12 sm:col-span-3">
                                <Label htmlFor="entidade-financeira">
                                    Entidade Financeira
                                </Label>
                                <Select value={entidadeFinanceiraSelecionada} onValueChange={setEntidadeFinanceiraSelecionada}>
                                    <SelectTrigger id="entidade-financeira" className="focus:ring-0 focus:ring-offset-0">
                                        <SelectValue placeholder="Selecione" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectHeader
                                            searchValue={searchEntidadeFinanceira}
                                            onSearchChange={setSearchEntidadeFinanceira}
                                            searchPlaceholder="Pesquisar entidade financeira..."
                                        />
                                        {renderFilteredSelectItems(
                                            entidadesFinanceiras,
                                            searchEntidadeFinanceira,
                                            {
                                                getLabel: (entidade) => entidade.nome,
                                                getValue: (entidade) => entidade.id.toString(),
                                                getKey: (entidade) => entidade.id,
                                                emptyMessage: 'Nenhuma entidade encontrada',
                                                noItemsMessage: 'Nenhuma entidade disponível'
                                            }
                                        )}
                                    </SelectContent>
                                </Select>
                            </div>

                            {/* 3. Input Descrição */}
                            <div className="flex flex-col gap-2 col-span-12 sm:col-span-5">
                                <Label htmlFor="descricao">
                                    Descrição
                                </Label>
                                <Input className="text-sm focus-visible:ring-0 focus-visible:ring-offset-0" id="descricao" placeholder="Descrição" name="descricao" />
                            </div>

                            {/* 4. Input Valor com prefixo R$ */}
                            <div className="flex flex-col gap-2 col-span-12 sm:col-span-2">
                                <Label htmlFor="valor">
                                    Valor
                                </Label>
                                <InputGroup>
                                    <InputGroupInput placeholder="0,00" className="text-sm focus-visible:ring-0 focus-visible:ring-offset-0" />
                                    <InputGroupAddon>
                                        R$
                                    </InputGroupAddon>
                                </InputGroup>
                            </div>
                        </div>
                        <div className="grid grid-cols-12 gap-4 mt-10 mb-10">
                            {/* 1. Select Lançamento Padrão */}
                            <div className="flex flex-col gap-2 col-span-12 sm:col-span-3">
                                <Label htmlFor="lancamento-padrao" required>
                                    Lançamento Padrão
                                </Label>
                                <Select>
                                    <SelectTrigger id="lancamento-padrao" className="focus:ring-0 focus:ring-offset-0">
                                        <SelectValue placeholder="Selecione" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectHeader
                                            searchValue={searchLancamentoPadrao}
                                            onSearchChange={setSearchLancamentoPadrao}
                                            searchPlaceholder="Pesquisar lançamento padrão..."
                                        />
                                        <SelectItem value="1">Lançamento 1</SelectItem>
                                        <SelectItem value="2">Lançamento 2</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>

                            {/* 2. Select Centro de Custo */}
                            <div className="flex flex-col gap-2 col-span-12 sm:col-span-3">
                                <Label htmlFor="centro-custo" required>
                                    Centro de Custo
                                </Label>
                                <Select>
                                    <SelectTrigger id="centro-custo" className="focus:ring-0 focus:ring-offset-0">
                                        <SelectValue placeholder="Selecione" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectHeader
                                            searchValue={searchCentroCusto}
                                            onSearchChange={setSearchCentroCusto}
                                            searchPlaceholder="Pesquisar centro de custo..."
                                        />
                                        <SelectItem value="1">Centro 1</SelectItem>
                                        <SelectItem value="2">Centro 2</SelectItem>
                                        <SelectFooter>
                                            <Button variant="outline" size="sm">
                                                <CirclePlus className="h-4 w-4" />
                                                Adicionar novo centro de custo
                                            </Button>
                                        </SelectFooter>
                                    </SelectContent>
                                </Select>
                            </div>

                            {/* 3. Input Número do Documento */}
                            <div className="flex flex-col gap-2 col-span-12 sm:col-span-2">
                                <Label htmlFor="numero-documento">
                                    Número do Documento
                                </Label>
                                <Input
                                    className="text-sm focus-visible:ring-0 focus-visible:ring-offset-0"
                                    id="numero-documento"
                                    placeholder="NF - 00/2025"
                                    name="numero-documento"
                                />
                            </div>

                            <div className="flex flex-col gap-2 col-span-12 sm:col-span-3">
                                <Label htmlFor="centro-custo" required>
                                    Cliente
                                </Label>
                                <Select>
                                    <SelectTrigger id="cliente" className="focus:ring-0 focus:ring-offset-0">
                                        <SelectValue placeholder="Selecione" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="1">Cliente 1</SelectItem>
                                        <SelectItem value="2">Cliente 2</SelectItem>
                                        <SelectFooter>
                                            <Button variant="outline" size="sm">
                                                <CirclePlus className="h-4 w-4" />
                                                Adicionar novo cliente
                                            </Button>
                                        </SelectFooter>
                                    </SelectContent>
                                </Select>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card className="mt-4">
                    <CardHeaderFlex className="flex-col items-start gap-3 ml-2">
                        <div className="flex items-center justify-between w-full">
                            <CardTitle className="text-lg font-semibold text-primary font-sans ">
                                Condições de pagamento
                            </CardTitle>
                            <div className="flex items-center space-x-2 mt-2 mb-3">
                                <Switch
                                    id="repetir-lancamento"
                                    checked={repetirLancamento}
                                    onCheckedChange={setRepetirLancamento}
                                />
                                <Label htmlFor="repetir-lancamento">Repetir lançamento?</Label>
                            </div>
                        </div>
                        {repetirLancamento && (
                            <div className="flex flex-col gap-2 w-full">
                                <Label htmlFor="frequencia-repeticao">
                                    Frequência de repetição
                                </Label>
                                <Select>
                                    <SelectTrigger id="frequencia-repeticao" className="focus:ring-0 focus:ring-offset-0">
                                        <SelectValue placeholder="Selecione" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectHeader
                                            searchPlaceholder="Pesquisar frequência..."
                                        />
                                        <SelectItem value="diario">Diário</SelectItem>
                                        <SelectItem value="semanal">Semanal</SelectItem>
                                        <SelectItem value="mensal">Mensal</SelectItem>
                                        <SelectItem value="anual">Anual</SelectItem>
                                        <SelectFooter
                                            addLabel="Cadastrar Recorrência"
                                            onAdd={() => setModalRecorrenciaOpen(true)}
                                            icon={<CirclePlus className="h-4 w-4" />}
                                        />
                                    </SelectContent>
                                </Select>
                            </div>
                        )}
                    </CardHeaderFlex>
                    <CardContent>
                        <div className="grid grid-cols-12 gap-4 mt-4">
                            {/* 1. Parcelamento */}
                            <div className="flex flex-col gap-2 col-span-12 sm:col-span-1">
                                <Label htmlFor="parcelamento" required>
                                    Parcelamento
                                </Label>
                                <Select value={parcelamento} onValueChange={setParcelamento}>
                                    <SelectTrigger id="parcelamento" className="focus:ring-0 focus:ring-offset-0">
                                        <SelectValue placeholder="Selecione" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="avista">À vista</SelectItem>
                                        <SelectItem value="2x">2x</SelectItem>
                                        <SelectItem value="3x">3x</SelectItem>
                                        <SelectItem value="4x">4x</SelectItem>
                                        <SelectItem value="5x">5x</SelectItem>
                                        <SelectItem value="6x">6x</SelectItem>
                                        <SelectItem value="10x">10x</SelectItem>
                                        <SelectItem value="12x">12x</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>

                            {/* 2. Vencimento */}
                            <div className="flex flex-col gap-1 col-span-12 sm:col-span-2">
                                <Label htmlFor="vencimento" className="px-1 text-sm" required>
                                    Vencimento
                                </Label>
                                <Popover open={openVencimento} onOpenChange={setOpenVencimento}>
                                    <PopoverTrigger asChild>
                                        <Button
                                            variant="outline"
                                            id="vencimento"
                                            className="w-full justify-between font-normal"
                                            type="button"
                                        >
                                            {vencimento ? format(vencimento, "dd/MM/yyyy") : "Selecione a data"}
                                            <CalendarDays className="h-4 w-4 opacity-50" />
                                        </Button>
                                    </PopoverTrigger>
                                    <PopoverContent className="w-auto overflow-hidden p-0" align="start">
                                        <Calendar
                                            mode="single"
                                            selected={vencimento}
                                            captionLayout="dropdown"
                                            onSelect={(selectedDate) => {
                                                setVencimento(selectedDate)
                                                setOpenVencimento(false)
                                            }}
                                        />
                                    </PopoverContent>
                                </Popover>
                            </div>

                            {/* 3. Forma de pagamento */}
                            <div className="flex flex-col gap-2 col-span-12 sm:col-span-3">
                                <Label htmlFor="forma-pagamento">
                                    Forma de pagamento
                                </Label>
                                <Select>
                                    <SelectTrigger id="forma-pagamento" className="focus:ring-0 focus:ring-offset-0">
                                        <SelectValue placeholder="Selecione" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="dinheiro">Dinheiro</SelectItem>
                                        <SelectItem value="pix">PIX</SelectItem>
                                        <SelectItem value="credito">Cartão de Crédito</SelectItem>
                                        <SelectItem value="debito">Cartão de Débito</SelectItem>
                                        <SelectItem value="boleto">Boleto</SelectItem>
                                        <SelectItem value="transferencia">Transferência</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>

                            {/* 4. Conta de recebimento */}
                            <div className="flex flex-col gap-2 col-span-12 sm:col-span-3">
                                <Label htmlFor="conta-recebimento">
                                    Conta de recebimento
                                </Label>
                                <div className="relative">
                                    <Select value={contaRecebimento} onValueChange={setContaRecebimento}>
                                        <SelectTrigger
                                            id="conta-recebimento"
                                            className="focus:ring-0 focus:ring-offset-0 pl-20"
                                        >
                                            <div className="absolute left-2 flex items-center gap-1">
                                                <button
                                                    type="button"
                                                    onClick={(e) => {
                                                        e.preventDefault()
                                                        e.stopPropagation()
                                                        setContaRecebimento("")
                                                    }}
                                                    className="text-blue-600 hover:text-blue-800 p-0.5 z-10"
                                                    onMouseDown={(e) => {
                                                        e.preventDefault()
                                                        e.stopPropagation()
                                                    }}
                                                >
                                                    <X className="h-3.5 w-3.5" />
                                                </button>
                                                <button
                                                    type="button"
                                                    onClick={(e) => {
                                                        e.preventDefault()
                                                        e.stopPropagation()
                                                        // Ação rápida - pode abrir modal ou fazer ação
                                                        console.log('Ação rápida para conta:', contaRecebimento)
                                                    }}
                                                    className="text-blue-600 hover:text-blue-800 p-0.5 z-10"
                                                    onMouseDown={(e) => {
                                                        e.preventDefault()
                                                        e.stopPropagation()
                                                    }}
                                                >
                                                    <Zap className="h-3.5 w-3.5" />
                                                </button>
                                            </div>
                                            <SelectValue placeholder="Selecione" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="caixinha">Caixinha</SelectItem>
                                            <SelectItem value="banco1">Banco 1</SelectItem>
                                            <SelectItem value="banco2">Banco 2</SelectItem>
                                            <SelectItem value="caixa">Caixa</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>
                            </div>
                            {/* 5. Recebido (Checkbox) */}
                            <div className="flex items-center space-x-2 mt-6">
                                <Checkbox
                                    id="recebido"
                                    checked={recebido}
                                    onCheckedChange={(checked) => setRecebido(checked === true)}
                                />
                                <Label htmlFor="recebido" className="font-normal cursor-pointer">
                                    Recebido
                                </Label>
                                <TooltipProvider>
                                    <Tooltip>
                                        <TooltipTrigger asChild>
                                            <HelpCircle className="h-4 w-4 text-blue-600 cursor-help" />
                                        </TooltipTrigger>
                                        <TooltipContent>
                                            <p>Marque esta opção se o pagamento já foi recebido</p>
                                        </TooltipContent>
                                    </Tooltip>
                                </TooltipProvider>
                            </div>
                        </div>
                        <div className="mt-8">
                        <Tabs defaultValue="observacoes" className="w-full">
                            <TabsList className="mb-4 h-auto p-0 bg-transparent border-b border-gray-200 rounded-none">
                                <TabsTrigger
                                    value="observacoes"
                                    className="data-[state=active]:text-blue-600 data-[state=active]:border-b-2 data-[state=active]:border-blue-600 data-[state=active]:bg-transparent data-[state=active]:shadow-none rounded-none border-b-2 border-transparent text-gray-600 hover:text-gray-900"
                                >
                                    Observações
                                </TabsTrigger>
                                <TabsTrigger
                                    value="anexo"
                                    className="data-[state=active]:text-blue-600 data-[state=active]:border-b-2 data-[state=active]:border-blue-600 data-[state=active]:bg-transparent data-[state=active]:shadow-none rounded-none border-b-2 border-transparent text-gray-600 hover:text-gray-900"
                                >
                                    Anexo
                                </TabsTrigger>
                            </TabsList>

                            <TabsContent value="observacoes" className="space-y-4">
                                <Textarea
                                    id="observacoes"
                                    placeholder="Descreva observações relevantes sobre esse lançamento financeiro"
                                    className="min-h-[120px] focus-visible:ring-0 focus-visible:ring-offset-0"
                                    rows={6}
                                />
                            </TabsContent>

                            <TabsContent value="anexo" className="space-y-4 mt-4">
                                <h3 className="text-sm font-medium text-gray-700">
                                    Anexo
                                </h3>
                                <div className="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
                                    <p className="text-sm text-gray-500">
                                        Área para upload de anexos (em desenvolvimento)
                                    </p>
                                </div>
                            </TabsContent>
                        </Tabs>
                        </div>
                    </CardContent>
                </Card>
            </form>

            {/* Modal de Recorrência */}
            <ModalRecorrencia
                open={modalRecorrenciaOpen}
                onOpenChange={setModalRecorrenciaOpen}
                onConfirm={(data: RecorrenciaData) => {
                    console.log('Recorrência configurada:', data)
                    // Aqui você pode salvar os dados da recorrência
                }}
            />
        </div>
    )
}

