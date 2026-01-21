/**
 * Financeiro - Módulo Financeiro
 *
 * Página inicial do módulo financeiro com visão geral e acesso rápido
 */

import { Head, Link } from '@inertiajs/react';
import { useState } from 'react';
import { AppSidebar } from '@/Components/app-sidebar';
import { SiteHeader } from '@/Components/site-header';
import {
    SidebarInset,
    SidebarProvider,
} from '@/Components/ui/sidebar';
import { MetronicCard } from '@/Components/ui/card';
import {
    Wallet,
    TrendingUp,
    TrendingDown,
    DollarSign,
    ArrowUpRight,
    ArrowDownRight,
    FileText,
    CreditCard,
    PieChart,
    Calendar,
    Plus,
    ChevronDown
} from 'lucide-react';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/Components/ui/dropdown-menu';
import { Button } from '@/Components/ui/button';
import { FormSheet } from '@/Components/form-sheet';
import { FormLancamento } from './Partials';

interface EntidadeFinanceira {
    id: number;
    nome: string;
    tipo: string;
}

interface FinanceiroIndexProps {
    auth: {
        user: {
            name: string;
            email: string;
        };
    };
    stats?: {
        saldoTotal: number;
        receitasMes: number;
        despesasMes: number;
        transacoesRecentes: number;
    };
    entidadesFinanceiras?: EntidadeFinanceira[];
}

export default function FinanceiroIndex({ auth, stats, entidadesFinanceiras = [] }: FinanceiroIndexProps) {
    const [sheetOpen, setSheetOpen] = useState(false);
    const [sheetType, setSheetType] = useState<'receita' | 'despesa' | null>(null);

    // Valores padrão para demonstração
    const financialStats = stats || {
        saldoTotal: 125450.75,
        receitasMes: 45230.00,
        despesasMes: 28540.50,
        transacoesRecentes: 156
    };

    const handleOpenSheet = (type: 'receita' | 'despesa') => {
        setSheetType(type);
        setSheetOpen(true);
    };

    const handleSave = () => {
        console.log('Salvar', sheetType);
        // Implementar lógica de salvamento
    };

    const handleSaveAndClone = () => {
        console.log('Salvar e Clonar', sheetType);
        // Implementar lógica de salvar e clonar
    };

    const handleSaveAndClear = () => {
        console.log('Salvar e Limpar', sheetType);
        // Implementar lógica de salvar e limpar
        setSheetOpen(false);
    };

    const quickAccessItems = [
        {
            title: 'Contas',
            description: 'Gerenciar contas bancárias e caixas',
            icon: Wallet,
            color: 'blue',
            href: '/financeiro/contas'
        },
        {
            title: 'Movimentações',
            description: 'Lançar receitas e despesas',
            icon: CreditCard,
            color: 'green',
            href: '/financeiro/movimentacoes'
        },
        {
            title: 'Relatórios',
            description: 'Visualizar relatórios financeiros',
            icon: FileText,
            color: 'purple',
            href: '/financeiro/relatorios'
        },
        {
            title: 'Categorias',
            description: 'Gerenciar plano de contas',
            icon: PieChart,
            color: 'orange',
            href: '/financeiro/categorias'
        }
    ];

    const formatCurrency = (value: number) => {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        }).format(value);
    };

    return (
        <>
            <Head title="Financeiro" />
            <SidebarProvider>
                <AppSidebar />
                <SidebarInset>
                    <SiteHeader />
                    <div className="flex flex-1 flex-col gap-4 p-4 pt-0">
                        {/* Header */}
                        <div className="mt-4 flex flex-col gap-2">
                            <div className="flex items-center justify-between">
                                <div className="flex flex-col gap-2">
                                    <h1 className="text-2xl font-semibold tracking-tight">
                                        Módulo Financeiro
                                    </h1>
                                    <p className="text-sm text-muted-foreground">
                                        Gerencie suas receitas, despesas e acompanhe a saúde financeira.
                                    </p>
                                </div>
                                <DropdownMenu >
                                    <DropdownMenuTrigger asChild>
                                        <Button size="sm" variant="success">
                                            <Plus className="h-4 w-4" />
                                            Lançamentos
                                            <ChevronDown className="ml-2 h-4 w-4" />
                                        </Button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent align="end">
                                        <DropdownMenuItem onClick={() => handleOpenSheet('receita')}>
                                            Receita
                                        </DropdownMenuItem>
                                        <DropdownMenuItem onClick={() => handleOpenSheet('despesa')}>
                                            Despesa
                                        </DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </div>
                        </div>

                        {/* Cards de Estatísticas */}
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            {/* Saldo Total */}
                            <MetronicCard>
                                <div className="p-6 flex flex-col gap-4">
                                    <div className="flex items-center justify-between">
                                        <div className="p-2 bg-blue-100 dark:bg-blue-950 rounded-lg">
                                            <DollarSign className="w-6 h-6 text-blue-600 dark:text-blue-400" />
                                        </div>
                                        <span className="text-xs text-muted-foreground">Atualizado agora</span>
                                    </div>
                                    <div>
                                        <p className="text-sm font-medium text-muted-foreground mb-1">Saldo Total</p>
                                        <p className="text-2xl font-bold">{formatCurrency(financialStats.saldoTotal)}</p>
                                    </div>
                                </div>
                            </MetronicCard>

                            {/* Receitas do Mês */}
                            <MetronicCard>
                                <div className="p-6 flex flex-col gap-4">
                                    <div className="flex items-center justify-between">
                                        <div className="p-2 bg-green-100 dark:bg-green-950 rounded-lg">
                                            <TrendingUp className="w-6 h-6 text-green-600 dark:text-green-400" />
                                        </div>
                                        <div className="flex items-center gap-1 text-green-600 dark:text-green-400">
                                            <ArrowUpRight className="w-4 h-4" />
                                            <span className="text-xs font-medium">+12.5%</span>
                                        </div>
                                    </div>
                                    <div>
                                        <p className="text-sm font-medium text-muted-foreground mb-1">Receitas do Mês</p>
                                        <p className="text-2xl font-bold">{formatCurrency(financialStats.receitasMes)}</p>
                                    </div>
                                </div>
                            </MetronicCard>

                            {/* Despesas do Mês */}
                            <MetronicCard>
                                <div className="p-6 flex flex-col gap-4">
                                    <div className="flex items-center justify-between">
                                        <div className="p-2 bg-red-100 dark:bg-red-950 rounded-lg">
                                            <TrendingDown className="w-6 h-6 text-red-600 dark:text-red-400" />
                                        </div>
                                        <div className="flex items-center gap-1 text-red-600 dark:text-red-400">
                                            <ArrowDownRight className="w-4 h-4" />
                                            <span className="text-xs font-medium">-8.3%</span>
                                        </div>
                                    </div>
                                    <div>
                                        <p className="text-sm font-medium text-muted-foreground mb-1">Despesas do Mês</p>
                                        <p className="text-2xl font-bold">{formatCurrency(financialStats.despesasMes)}</p>
                                    </div>
                                </div>
                            </MetronicCard>

                            {/* Transações Recentes */}
                            <MetronicCard>
                                <div className="p-6 flex flex-col gap-4">
                                    <div className="flex items-center justify-between">
                                        <div className="p-2 bg-purple-100 dark:bg-purple-950 rounded-lg">
                                            <Calendar className="w-6 h-6 text-purple-600 dark:text-purple-400" />
                                        </div>
                                        <span className="text-xs text-muted-foreground">Últimos 30 dias</span>
                                    </div>
                                    <div>
                                        <p className="text-sm font-medium text-muted-foreground mb-1">Transações</p>
                                        <p className="text-2xl font-bold">{financialStats.transacoesRecentes}</p>
                                    </div>
                                </div>
                            </MetronicCard>
                        </div>

                        {/* Acesso Rápido */}
                        <div className="mt-4">
                            <h2 className="text-lg font-semibold mb-4">Acesso Rápido</h2>
                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                {quickAccessItems.map((item) => {
                                    const Icon = item.icon;
                                    const bgColorClass = `bg-${item.color}-100 dark:bg-${item.color}-950`;
                                    const textColorClass = `text-${item.color}-600 dark:text-${item.color}-400`;

                                    return (
                                        <Link key={item.title} href={item.href}>
                                            <MetronicCard className="h-full hover:shadow-sm transition-all cursor-pointer group">
                                                <div className="p-6 flex flex-col gap-3">
                                                    <div className={`p-3 ${bgColorClass} rounded-lg w-fit group-hover:scale-110 transition-transform`}>
                                                        <Icon className={`w-6 h-6 ${textColorClass}`} />
                                                    </div>
                                                    <div>
                                                        <h3 className="font-semibold mb-1">{item.title}</h3>
                                                        <p className="text-sm text-muted-foreground">{item.description}</p>
                                                    </div>
                                                </div>
                                            </MetronicCard>
                                        </Link>
                                    );
                                })}
                            </div>
                        </div>

                        {/* Área de Conteúdo Expandido */}
                        <div className="mt-4">
                            <MetronicCard>
                                <div className="p-6">
                                    <h2 className="text-lg font-semibold mb-4">Resumo Mensal</h2>
                                    <div className="space-y-4">
                                        <div className="flex items-center justify-between p-4 bg-muted/50 rounded-lg">
                                            <div className="flex items-center gap-3">
                                                <div className="p-2 bg-background rounded-lg">
                                                    <TrendingUp className="w-5 h-5 text-green-600" />
                                                </div>
                                                <div>
                                                    <p className="font-medium">Resultado do Mês</p>
                                                    <p className="text-sm text-muted-foreground">Receitas - Despesas</p>
                                                </div>
                                            </div>
                                            <p className="text-xl font-bold text-green-600">
                                                {formatCurrency(financialStats.receitasMes - financialStats.despesasMes)}
                                            </p>
                                        </div>

                                        <div className="grid grid-cols-2 gap-4">
                                            <div className="p-4 bg-muted/50 rounded-lg">
                                                <p className="text-sm text-muted-foreground mb-1">Taxa de Crescimento</p>
                                                <p className="text-lg font-semibold text-green-600">+12.5%</p>
                                            </div>
                                            <div className="p-4 bg-muted/50 rounded-lg">
                                                <p className="text-sm text-muted-foreground mb-1">Economia Mensal</p>
                                                <p className="text-lg font-semibold text-blue-600">37%</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </MetronicCard>
                        </div>
                    </div>
                </SidebarInset>
            </SidebarProvider>

            {/* FormSheet para Receita/Despesa */}
            {sheetType && (
                <FormSheet
                    open={sheetOpen}
                    onOpenChange={setSheetOpen}
                    title={sheetType === 'receita' ? 'Nova Receita' : 'Nova Despesa'}
                    onSave={handleSave}
                    onSaveAndClone={handleSaveAndClone}
                    onSaveAndClear={handleSaveAndClear}
                >
                    <FormLancamento tipo={sheetType} entidadesFinanceiras={entidadesFinanceiras} />
                </FormSheet>
            )}
        </>
    );
}
