import { AppRouting } from '@/routing/app-routing';
import { AuthAppRouting } from '@/routing/auth-app-routing';
import { ThemeProvider } from 'next-themes';
import { HelmetProvider } from 'react-helmet-async';
import { BrowserRouter } from 'react-router-dom';
import { LoadingBarContainer } from 'react-top-loading-bar';
import { Toaster } from '@/components/ui/sonner';
import { SessionExpiredProvider } from '@/providers/session-expired-provider';

/** Blade autenticado injeta sempre __APP_DATA__; Blade guest (react-auth) injeta __AUTH_APP_DATA__. */
function hasLaravelAppShell(): boolean {
  return typeof window !== 'undefined' && window.__APP_DATA__ !== undefined;
}

function hasLaravelAuthShell(): boolean {
  return typeof window !== 'undefined' && window.__AUTH_APP_DATA__ !== undefined;
}

/**
 * Vite direto (ex.: http://localhost:5174/) não recebe Blade → sem __APP_DATA__.
 * Nesse caso usamos só o fluxo de login React (AuthAppRouting), com basename vazio
 * para que o router case a URL "/" sem exigir o prefixo "/app".
 * Com `npm run dev` atrás do Laravel (REACT_VITE_DEV), __APP_DATA__ existe e o
 * painel segue normal com basename="/app".
 */
const isAuthShell =
  hasLaravelAuthShell() || (import.meta.env.DEV && !hasLaravelAppShell());

const { BASE_URL } = import.meta.env;
const appRouterBase = (import.meta.env.VITE_ROUTER_BASE as string) || BASE_URL;
const appBasename = appRouterBase.endsWith('/') ? appRouterBase.slice(0, -1) : appRouterBase;

/** Auth shell não tem prefixo /app — usa basename="" para casar qualquer URL. */
const basename = isAuthShell ? '' : appBasename;

export function App() {
  return (
    <ThemeProvider
      attribute="class"
      defaultTheme="light"
      storageKey="vite-theme"
      enableSystem
      disableTransitionOnChange
      enableColorScheme
    >
      <HelmetProvider>
        <LoadingBarContainer>
          <BrowserRouter basename={basename}>
            <Toaster />
            <SessionExpiredProvider enabled={!isAuthShell}>
              {isAuthShell ? <AuthAppRouting /> : <AppRouting />}
            </SessionExpiredProvider>
          </BrowserRouter>
        </LoadingBarContainer>
      </HelmetProvider>
    </ThemeProvider>
  );
}
