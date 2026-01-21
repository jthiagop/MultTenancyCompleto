/**
 * AppLayout
 * 
 * Layout principal da aplicação usando Inertia.js
 * Este layout será usado por todas as páginas React
 */

import { Head, Link } from '@inertiajs/react';
import { ReactNode } from 'react';

interface AppLayoutProps {
    children: ReactNode;
    title?: string;
}

export default function AppLayout({ children, title = 'Laravel' }: AppLayoutProps) {
    return (
        <>
            <Head title={title} />

            <div className="min-h-screen bg-gray-100">
                {/* Header */}
                <header className="bg-white shadow-sm">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                        <div className="flex items-center justify-between">
                            <div className="flex items-center">
                                <Link href="/" className="text-xl font-bold text-gray-900">
                                    {title}
                                </Link>
                            </div>
                            <nav className="flex space-x-4">
                                <Link
                                    href="/"
                                    className="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium"
                                >
                                    Home
                                </Link>
                                <Link
                                    href="/dashboard"
                                    className="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium"
                                >
                                    Dashboard
                                </Link>
                            </nav>
                        </div>
                    </div>
                </header>

                {/* Main Content */}
                <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    {children}
                </main>

                {/* Footer */}
                <footer className="bg-white border-t border-gray-200 mt-auto">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                        <p className="text-center text-sm text-gray-500">
                            © {new Date().getFullYear()} {title}. Todos os direitos reservados.
                        </p>
                    </div>
                </footer>
            </div>
        </>
    );
}

