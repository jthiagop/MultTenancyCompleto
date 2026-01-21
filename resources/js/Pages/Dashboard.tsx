/**
 * Dashboard Page
 * 
 * Página de dashboard usando Inertia.js + React com Shadcn Sidebar
 */

import { Head } from '@inertiajs/react';
import { AppSidebar } from '@/Components/app-sidebar';
import { SiteHeader } from '@/Components/site-header';
import {
    SidebarInset,
    SidebarProvider,
} from '@/Components/ui/sidebar';
import { Card, CardContent, CardDescription, CardHeader, CardTitle, MetronicCard } from '@/Components/ui/card';

interface DashboardProps {
    auth: {
        user: {
            name: string;
            email: string;
        };
    };
}

export default function Dashboard({ auth }: DashboardProps) {
    console.log('Dashboard component mounted', { auth });
    return (
        <>
            <Head title="Dashboard" />
            <SidebarProvider>
                <AppSidebar />
                <SidebarInset>
                    <SiteHeader />
                    <div className="flex flex-1 flex-col gap-4 p-4 pt-0">
                        <div className="mt-4 flex flex-col gap-2">
                            <h1 className="text-xl font-medium tracking-tight">
                                Bem-vindo, {auth.user.name.split(' ')[0]} <span className="inline-block hover:animate-spin">👋</span>
                            </h1>
                            <p className="text-sm font-normal">
                                Aqui você pode gerenciar seu convento.
                            </p>
                        </div>
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6">
                            {/* Financeiro */}
                            <MetronicCard className="min-h-[150px]">
                                <div className="mt-4 ms-5">
                                    {/* Icon placeholder - using SVG directly or img if available */}
                                    <div className="p-2 bg-blue-100 rounded-lg w-fit">
                                        <img src="/assets/media/icons/duotune/finance/fin001.svg" className="w-8 h-8 text-blue-600" alt="Financeiro" />
                                    </div>
                                </div>
                                <div className="flex flex-col gap-1 pb-4 px-5">
                                    <span className="text-xl font-bold">Financeiro</span>
                                    <span className="text-sm text-muted-foreground">Cadastros financeiros, movimentações</span>
                                </div>
                            </MetronicCard>

                            {/* Patrimônio */}
                            <MetronicCard className="min-h-[150px]">
                                <div className="mt-4 ms-5">
                                    <div className="p-2 bg-orange-100 rounded-lg w-fit">
                                        <img src="/assets/media/icons/duotune/maps/map001.svg" className="w-8 h-8 text-orange-600" alt="Patrimônio" />
                                    </div>
                                </div>
                                <div className="flex flex-col gap-1 pb-4 px-5">
                                    <span className="text-xl font-bold">Patrimônio</span>
                                    <span className="text-sm text-muted-foreground">Gestão patrimonial, foro e laudêmio</span>
                                </div>
                            </MetronicCard>

                            {/* Contabilidade */}
                            <MetronicCard className="min-h-[150px]">
                                <div className="mt-4 ms-5">
                                    <div className="p-2 bg-green-100 rounded-lg w-fit">
                                        <img src="/assets/media/icons/duotune/finance/fin008.svg" className="w-8 h-8 text-green-600" alt="Contabilidade" />
                                    </div>
                                </div>
                                <div className="flex flex-col gap-1 pb-4 px-5">
                                    <span className="text-xl font-bold">Contabilidade</span>
                                    <span className="text-sm text-muted-foreground">Gerenciar plano de contas e DE/PARA</span>
                                </div>
                            </MetronicCard>

                            {/* Dízimo e Doações */}
                            <MetronicCard className="min-h-[150px]">
                                <div className="mt-4 ms-5">
                                    <div className="p-2 bg-red-100 rounded-lg w-fit">
                                        <img src="/assets/media/icons/duotune/medicine/med005.svg" className="w-8 h-8 text-red-600" alt="Dízimo" />
                                    </div>
                                </div>
                                <div className="flex flex-col gap-1 pb-4 px-5">
                                    <span className="text-xl font-bold">Dízimo e Doações</span>
                                    <span className="text-sm text-muted-foreground">Gerenciamento de dízimo e doações</span>
                                </div>
                            </MetronicCard>

                            {/* Cadastro de Fiéis */}
                            <MetronicCard className="min-h-[150px]">
                                <div className="mt-4 ms-5">
                                    <div className="p-2 bg-cyan-100 rounded-lg w-fit">
                                        <img src="/assets/media/icons/duotune/communication/com013.svg" className="w-8 h-8 text-cyan-600" alt="Fiéis" />
                                    </div>
                                </div>
                                <div className="flex flex-col gap-1 pb-4 px-5">
                                    <span className="text-xl font-bold">Cadastro de Fiéis</span>
                                    <span className="text-sm text-muted-foreground">Gerenciamento de membros e contribuições</span>
                                </div>
                            </MetronicCard>

                            {/* Cadastro de Sepulturas */}
                            <MetronicCard className="min-h-[150px]">
                                <div className="mt-4 ms-5">
                                    <div className="p-2 bg-stone-100 rounded-lg w-fit">
                                        <img src="/assets/media/icons/duotune/general/gen002.svg" className="w-8 h-8 text-stone-600" alt="Sepulturas" />
                                    </div>
                                </div>
                                <div className="flex flex-col gap-1 pb-4 px-5">
                                    <span className="text-xl font-bold">Cadastro de Sepulturas</span>
                                    <span className="text-sm text-muted-foreground">Gerenciamento de sepultamentos, manutenção...</span>
                                </div>
                            </MetronicCard>

                            {/* Nota Fiscal */}
                            <MetronicCard className="min-h-[150px]">
                                <div className="mt-4 ms-5">
                                    <div className="p-2 bg-indigo-100 rounded-lg w-fit">
                                        <img src="/assets/media/icons/duotune/files/fil003.svg" className="w-8 h-8 text-indigo-600" alt="Nota Fiscal" />
                                    </div>
                                </div>
                                <div className="flex flex-col gap-1 pb-4 px-5">
                                    <span className="text-xl font-bold">Nota Fiscal</span>
                                    <span className="text-sm text-muted-foreground">Receber os arquivos XML organizados é o paraíso.</span>
                                </div>
                            </MetronicCard>
                        </div>
                        <div className="min-h-[100vh] flex-1 rounded-xl bg-muted/50 md:min-h-min p-6">
                            <h2 className="text-2xl font-bold mb-4">Conteúdo Principal</h2>
                            <p className="text-muted-foreground">
                                Este é o conteúdo principal da dashboard. Aqui você pode adicionar gráficos, tabelas e outras informações importantes.
                            </p>
                        </div>
                    </div>
                </SidebarInset>
            </SidebarProvider>
        </>
    );
}
