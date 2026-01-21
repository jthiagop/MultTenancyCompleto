/**
 * Welcome Page
 * 
 * Página de boas-vindas para testar o Inertia.js + React + Shadcn
 */

import AppLayout from '@/Layouts/AppLayout';
import { Link } from '@inertiajs/react';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';

export default function Welcome() {
    return (
        <AppLayout title="Bem-vindo">
            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <Card>
                        <CardHeader>
                            <CardTitle className="text-3xl">
                                Bem-vindo ao Inertia.js + React + Shadcn
                            </CardTitle>
                            <CardDescription>
                                Você está vendo uma página React renderizada através do Inertia.js com componentes Shadcn UI!
                            </CardDescription>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            <div className="space-y-2">
                                <label htmlFor="test-input" className="text-sm font-medium">
                                    Teste de Input Shadcn:
                                </label>
                                <Input
                                    id="test-input"
                                    type="text"
                                    placeholder="Digite algo aqui..."
                                />
                            </div>
                            <div className="flex gap-2">
                                <Button asChild>
                                    <Link href="/dashboard">
                                        Ir para Dashboard
                                    </Link>
                                </Button>
                                <Button variant="outline">
                                    Botão Outline
                                </Button>
                                <Button variant="secondary">
                                    Botão Secondary
                                </Button>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AppLayout>
    );
}

