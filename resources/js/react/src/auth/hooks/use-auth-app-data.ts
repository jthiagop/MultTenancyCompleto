export interface AuthAppUrls {
  login: string;
  register: string;
  passwordEmail: string;
  passwordStore: string;
}

export interface AuthAppData {
  csrfToken: string;
  appName: string;
  urls: AuthAppUrls;
  flashStatus: string | null;
  validationErrors: Record<string, string[]>;
  /** Mesma lógica do auth-layout Blade: TelaDeLogin ativa ou penha.png */
  loginBackgroundUrl: string;
  /** tenancy/assets/media/app/mini-logo.svg */
  loginMiniLogoUrl: string;
}

declare global {
  interface Window {
    __AUTH_APP_DATA__?: AuthAppData;
  }
}

export function useAuthAppData(): AuthAppData {
  const data = window.__AUTH_APP_DATA__;
  const loginDefaults = {
    loginBackgroundUrl: '/tenancy/assets/media/misc/penha.png',
    loginMiniLogoUrl: '/tenancy/assets/media/app/mini-logo.svg',
  };

  if (!data) {
    /**
     * Fallback para desenvolvimento via Vite direto (sem Blade do Laravel).
     * Com o proxy Vite (vite.config.ts), /login, /register etc. são repassados
     * ao Laravel transparentemente — sem problema de CORS ou cross-origin.
     */
    return {
      csrfToken: '',
      appName: (import.meta.env.VITE_APP_NAME as string | undefined) ?? 'Dominus',
      urls: {
        login: '/login',
        register: '/register',
        passwordEmail: '/forgot-password',
        passwordStore: '/reset-password',
      },
      flashStatus: null,
      validationErrors: {},
      ...loginDefaults,
    };
  }
  return {
    ...data,
    loginBackgroundUrl: data.loginBackgroundUrl || loginDefaults.loginBackgroundUrl,
    loginMiniLogoUrl: data.loginMiniLogoUrl || loginDefaults.loginMiniLogoUrl,
  };
}
