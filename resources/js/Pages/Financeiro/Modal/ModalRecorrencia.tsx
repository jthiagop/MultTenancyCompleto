/**
 * ModalRecorrencia - Modal para configurar recorrência de lançamentos
 *
 * Permite configurar a frequência e término de recorrências para lançamentos financeiros
 */

import * as React from "react"
import { useState } from "react"
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogFooter,
} from '@/Components/ui/dialog'
import { Label } from '@/Components/ui/label'
import { Input } from '@/Components/ui/input'
import { Button } from '@/Components/ui/button'
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/Components/ui/select'

interface ModalRecorrenciaProps {
    /** Controla se o modal está aberto */
    open: boolean
    /** Função chamada quando o modal deve ser fechado */
    onOpenChange: (open: boolean) => void
    /** Função chamada ao confirmar a recorrência */
    onConfirm?: (data: RecorrenciaData) => void
}

export interface RecorrenciaData {
    repetirACada: number
    frequencia: string
    termino: 'apos' | 'ate'
    ocorrencias?: number
    dataFim?: Date
}

export function ModalRecorrencia({ open, onOpenChange, onConfirm }: ModalRecorrenciaProps) {
    const [repetirACada, setRepetirACada] = useState<number>(1)
    const [frequencia, setFrequencia] = useState<string>("mes")
    const [termino, setTermino] = useState<'apos' | 'ate'>('apos')
    const [ocorrencias, setOcorrencias] = useState<number>(1)

    const handleConfirm = () => {
        const data: RecorrenciaData = {
            repetirACada,
            frequencia,
            termino,
            ocorrencias: termino === 'apos' ? ocorrencias : undefined,
        }
        onConfirm?.(data)
        onOpenChange(false)
    }

    const handleCancel = () => {
        onOpenChange(false)
    }

    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogContent className="sm:max-w-[500px]">
                <DialogHeader>
                    <DialogTitle className="text-lg font-semibold">
                        Recorrência
                    </DialogTitle>
                </DialogHeader>

                <div className="space-y-6 py-4">
                    {/* Seção: Frequência da recorrência */}
                    <div className="space-y-4">
                        <h3 className="text-sm font-medium text-gray-900">
                            Frequência da recorrência
                        </h3>

                        <div className="grid grid-cols-12 gap-4">
                            {/* Repetir a cada */}
                            <div className="flex flex-col gap-2 col-span-12 sm:col-span-5">
                                <Label htmlFor="repetir-a-cada" required>
                                    Repetir a cada
                                </Label>
                                <Input
                                    type="number"
                                    id="repetir-a-cada"
                                    value={repetirACada}
                                    onChange={(e) => setRepetirACada(Number(e.target.value))}
                                    min="1"
                                    className="focus-visible:ring-0 focus-visible:ring-offset-0"
                                />
                            </div>

                            {/* Frequência */}
                            <div className="flex flex-col gap-2 col-span-12 sm:col-span-7">
                                <Label htmlFor="frequencia" required>
                                    Frequência
                                </Label>
                                <Select value={frequencia} onValueChange={setFrequencia}>
                                    <SelectTrigger id="frequencia" className="focus:ring-0 focus:ring-offset-0">
                                        <SelectValue placeholder="Selecione" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="dia">Dia(s)</SelectItem>
                                        <SelectItem value="semana">Semana(s)</SelectItem>
                                        <SelectItem value="mes">Mês(es)</SelectItem>
                                        <SelectItem value="ano">Ano(s)</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                        </div>
                    </div>

                    {/* Seção: Término da recorrência */}
                    <div className="space-y-4">
                        <h3 className="text-sm font-medium text-gray-900">
                            Término da recorrência
                        </h3>

                        <div className="space-y-3">
                            {/* Radio: Após */}
                            <div className="flex items-center space-x-3">
                                <input
                                    type="radio"
                                    id="termino-apos"
                                    name="termino"
                                    value="apos"
                                    checked={termino === 'apos'}
                                    onChange={(e) => setTermino(e.target.value as 'apos')}
                                    className="h-4 w-4 text-blue-600 focus:ring-blue-500"
                                />
                                <Label htmlFor="termino-apos" className="font-normal cursor-pointer">
                                    Após
                                </Label>
                                {termino === 'apos' && (
                                    <div className="flex items-center space-x-2">
                                        <Input
                                            type="number"
                                            value={ocorrencias}
                                            onChange={(e) => setOcorrencias(Number(e.target.value))}
                                            min="1"
                                            className="w-20 h-8 focus-visible:ring-0 focus-visible:ring-offset-0"
                                        />
                                        <span className="text-sm text-gray-600">Ocorrências</span>
                                    </div>
                                )}
                            </div>

                            {/* Radio: Até (opcional, para implementação futura) */}
                            <div className="flex items-center space-x-3">
                                <input
                                    type="radio"
                                    id="termino-ate"
                                    name="termino"
                                    value="ate"
                                    checked={termino === 'ate'}
                                    onChange={(e) => setTermino(e.target.value as 'ate')}
                                    className="h-4 w-4 text-blue-600 focus:ring-blue-500"
                                />
                                <Label htmlFor="termino-ate" className="font-normal cursor-pointer">
                                    Até
                                </Label>
                                {termino === 'ate' && (
                                    <span className="text-sm text-gray-500">
                                        (Funcionalidade em desenvolvimento)
                                    </span>
                                )}
                            </div>
                        </div>
                    </div>
                </div>

                <DialogFooter className="flex-row justify-end gap-2 sm:gap-0">
                    <Button
                        variant="outline"
                        onClick={handleCancel}
                        className="sm:mr-2"
                    >
                        Cancelar
                    </Button>
                    <Button
                        variant="success"
                        onClick={handleConfirm}
                    >
                        Confirmar
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    )
}

