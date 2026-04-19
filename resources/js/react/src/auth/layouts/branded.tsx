import { Outlet } from 'react-router';
import { useAuthAppData } from '@/auth/hooks/use-auth-app-data';
import { Card, CardContent } from '@/components/ui/card';

export function AuthBrandedLayout() {
    const { appName, loginBackgroundUrl, loginMiniLogoUrl } = useAuthAppData();

    return (
        <div className="grid lg:grid-cols-2 grow min-h-screen">
            {/* Coluna do formulário — logo acima do card (como auth-layout Blade, linhas 101–105) */}
            <div className="flex flex-col justify-center items-center p-8 lg:p-10 order-2 lg:order-1 gap-6 lg:gap-10">
                <a href="/dashboard" className="shrink-0">
                    <img
                        src={loginMiniLogoUrl}
                        alt=""
                        className="h-[100px] w-auto max-w-[280px] object-contain object-left lg:object-center"
                    />
                </a>
                <Card className="w-full max-w-[400px]">
                    <CardContent className="p-6">
                        <Outlet />
                    </CardContent>
                </Card>
            </div>

            {/* Painel lateral — imagem de fundo (como aside Blade, linhas 65–66) */}
            <div
                className="relative order-1 lg:order-2 overflow-hidden rounded-b-xl lg:rounded-xl lg:border lg:border-border lg:m-5 bg-cover bg-center bg-no-repeat from-primary/15 via-background to-muted dark:from-primary/10 dark:to-background flex flex-col p-8 lg:p-16 gap-4"
                style={{ backgroundImage: `url(${JSON.stringify(loginBackgroundUrl)})` }}
            >
                <div
                    className="pointer-events-none absolute inset-x-0 top-0 z-1 h-[200px]"
                    style={{
                        background:
                            'linear-gradient(to bottom, rgba(0, 0, 0, 0.8) 0%, rgba(0, 0, 0, 0.4) 50%, transparent 100%)',
                    }}
                />
                <div
                    className="pointer-events-none absolute inset-x-0 bottom-0 z-1 h-[200px]"
                    style={{
                        background:
                            'linear-gradient(to top, rgba(0, 0, 0, 0.8) 0%, rgba(0, 0, 0, 0.4) 50%, transparent 100%)',
                    }}
                />

                <div className="relative z-2 flex flex-col gap-3 text-white drop-shadow-sm">
                    <div className="flex flex-wrap items-center gap-3">
                        <a href="/dashboard" className="shrink-0 rounded-sm ring-offset-2 ring-offset-transparent focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/60">
                            <img
                                src={loginMiniLogoUrl}
                                alt=""
                                className="h-[50px] w-auto max-w-[400px] object-contain object-left drop-shadow-md"
                            />
                        </a>
                        <h3 className="text-2xl font-semibold text-mono min-w-0">{appName}</h3>
                        <p className="text-base font-medium text-white/90">
                        Acesse o painel com segurança. Gestão financeira e pastoral em um só lugar.
                    </p>
                    </div>

                </div>
            </div>
        </div>
    );
}
