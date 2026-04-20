import { Outlet } from 'react-router';
import { useAuthAppData } from '@/auth/hooks/use-auth-app-data';
import { Card, CardContent } from '@/components/ui/card';

export function AuthBrandedLayout() {
    const {
        appName,
        loginBackgroundUrl,
        loginBackgroundDescricao,
        loginBackgroundLocalidade,
        loginMiniLogoUrl,
    } = useAuthAppData();

    const hasLegend = Boolean(loginBackgroundDescricao || loginBackgroundLocalidade);

    return (
        <div className="grid lg:grid-cols-2 grow min-h-screen">
            {/* Coluna do formulário — logo acima do card */}
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

            {/* Painel lateral — imagem única (sorteada no backend a cada reload) */}
            <div
                className="relative order-1 lg:order-2 overflow-hidden rounded-b-xl lg:rounded-xl lg:border lg:border-border lg:m-5 bg-cover bg-center bg-no-repeat bg-muted min-h-[320px] lg:min-h-screen"
                style={{ backgroundImage: `url(${JSON.stringify(loginBackgroundUrl)})` }}
                role="img"
                aria-label={
                    hasLegend
                        ? `${loginBackgroundDescricao ?? ''}${loginBackgroundLocalidade ? ' — ' + loginBackgroundLocalidade : ''}`.trim()
                        : 'Imagem de fundo da tela de login'
                }
            >
                {/* Gradiente topo — legibilidade do logo/appName */}
                <div
                    className="pointer-events-none absolute inset-x-0 top-0 z-1 h-[200px]"
                    style={{
                        background:
                            'linear-gradient(to bottom, rgba(0, 0, 0, 0.8) 0%, rgba(0, 0, 0, 0.4) 50%, transparent 100%)',
                    }}
                />

                {/* Gradiente base — só aparece se houver legenda (evita sombra inútil) */}
                {hasLegend && (
                    <div
                        className="pointer-events-none absolute inset-x-0 bottom-0 z-1 h-[260px]"
                        style={{
                            background:
                                'linear-gradient(to top, rgba(0, 0, 0, 0.85) 0%, rgba(0, 0, 0, 0.5) 50%, transparent 100%)',
                        }}
                    />
                )}

                {/* Cabeçalho — logo + appName */}
                <div className="relative z-2 flex flex-col gap-3 p-8 lg:p-12 text-white drop-shadow-sm">
                    <div className="flex flex-wrap items-center gap-3">
                        <a
                            href="/dashboard"
                            className="shrink-0 rounded-sm ring-offset-2 ring-offset-transparent focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/60"
                        >
                            <img
                                src={loginMiniLogoUrl}
                                alt=""
                                className="h-[50px] w-auto max-w-[400px] object-contain object-left drop-shadow-md"
                            />
                        </a>
                        <h3 className="text-2xl font-semibold text-mono min-w-0">
                            {appName}
                        </h3>
                    </div>
                    <p className="text-base font-medium text-white/90 max-w-[520px]">
                        Acesse o painel com segurança. Gestão financeira e pastoral em um só lugar.
                    </p>
                </div>

                {/* Legenda — descricao (convento) + localidade */}
                {hasLegend && (
                    <div className="absolute inset-x-0 bottom-0 z-2 p-8 lg:p-12 text-white drop-shadow-sm">
                        {loginBackgroundDescricao && (
                            <h4 className="text-xl lg:text-2xl font-semibold leading-tight">
                                {loginBackgroundDescricao}
                            </h4>
                        )}
                        {loginBackgroundLocalidade && (
                            <p className="mt-1 text-sm lg:text-base text-white/85">
                                {loginBackgroundLocalidade}
                            </p>
                        )}
                    </div>
                )}
            </div>
        </div>
    );
}
