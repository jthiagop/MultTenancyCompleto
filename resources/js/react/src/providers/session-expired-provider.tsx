import {
  createContext,
  useCallback,
  useContext,
  useEffect,
  useMemo,
  useRef,
  useState,
} from 'react';
import { LogIn, RefreshCcw, ShieldAlert } from 'lucide-react';
import { toAbsoluteUrl } from '@/lib/helpers';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';

// ── Tipos públicos ────────────────────────────────────────────────────────────

/**
 * Motivo pelo qual a sessão foi marcada como expirada. Usado para mostrar
 * mensagem contextual e ajudar em telemetria futura.
 *
 * - `idle`            : `CheckSessionExpiration` no backend (401 com body
 *                       `{ error: 'SESSION_EXPIRED' }`).
 * - `csrf`            : `HandleSessionExpiration` no backend (419 — token
 *                       CSRF expirou; comum em abas deixadas abertas por horas).
 * - `manual`          : Algum lugar do app chamou `markExpired('manual')`
 *                       (ex.: logout falhou e queremos forçar a tela).
 * - `logout-other-tab`: O usuário fez logout em outra aba; sincronizamos via
 *                       `storage` event para encerrar nesta aba também.
 */
export type SessionExpiredReason = 'idle' | 'csrf' | 'manual' | 'logout-other-tab';

interface SessionExpiredContextValue {
  expired: boolean;
  reason: SessionExpiredReason;
  markExpired: (reason?: SessionExpiredReason) => void;
  /** Limpa o estado — útil para fluxos como "trocar de empresa". */
  clear: () => void;
}

const SessionExpiredContext = createContext<SessionExpiredContextValue | null>(null);

export function useSessionExpired(): SessionExpiredContextValue {
  const ctx = useContext(SessionExpiredContext);
  if (!ctx) {
    throw new Error('useSessionExpired() precisa estar dentro de <SessionExpiredProvider>');
  }
  return ctx;
}

// ── Constantes ────────────────────────────────────────────────────────────────

/** Conta regressiva (s) antes de redirecionar automaticamente para `/login`. */
const REDIRECT_SECONDS = 10;

/** Chave usada para preservar o deep link e propagar logout entre abas. */
const RETURN_TO_KEY = 'app:return-to';
const LOGOUT_BROADCAST_KEY = 'app:logout';

/**
 * Flag global no `window` para garantir que o monkey-patch de `fetch` seja
 * idempotente. Em dev (HMR), o provider é remontado várias vezes; sem essa
 * proteção cada remontagem capturaria o `fetch` já patcheado, criando uma
 * cascata de patches aninhados.
 */
declare global {
  interface Window {
    __sessionExpiredPatched?: boolean;
  }
}

// ── Helpers ───────────────────────────────────────────────────────────────────

/**
 * Decide se uma resposta HTTP indica sessão expirada.
 *
 * Regras (alinhadas ao backend `CheckSessionExpiration` e `HandleSessionExpiration`):
 * - 419 sempre conta (CSRF mismatch é sinônimo de sessão).
 * - 401 só conta quando o body JSON tem `error === 'SESSION_EXPIRED'`,
 *   evitando falso positivo para 401 emitidos por políticas Spatie ou
 *   tokens de API inválidos (que não devem disparar a tela de "sessão
 *   expirada").
 */
async function classifyResponse(res: Response): Promise<SessionExpiredReason | null> {
  if (res.status !== 401 && res.status !== 419) return null;
  if (res.status === 419) return 'csrf';

  const ctype = res.headers.get('content-type') ?? '';
  if (!ctype.includes('application/json')) return null;

  try {
    const body = (await res.clone().json()) as { error?: string };
    return body?.error === 'SESSION_EXPIRED' ? 'idle' : null;
  } catch {
    // Body não-JSON ou parse falhou — não trata como expiração para evitar
    // falso positivo em respostas opacas.
    return null;
  }
}

/** Lê com segurança um valor do `sessionStorage` (storage pode estar bloqueado). */
function safeGetSession(key: string): string | null {
  try {
    return window.sessionStorage.getItem(key);
  } catch {
    return null;
  }
}

function safeSetSession(key: string, value: string): void {
  try {
    window.sessionStorage.setItem(key, value);
  } catch {
    /* noop */
  }
}

function safeBroadcastLogout(): void {
  try {
    // Escrever no localStorage dispara `storage` event nas outras abas.
    window.localStorage.setItem(LOGOUT_BROADCAST_KEY, String(Date.now()));
    // Limpa imediatamente para não poluir o storage de longa data.
    window.localStorage.removeItem(LOGOUT_BROADCAST_KEY);
  } catch {
    /* noop */
  }
}

/**
 * Resolve a URL final do `/login`, considerando dois cenários:
 * - Em produção (e em dev com proxy do Laravel) `/login` é relativo ao host
 *   atual e funciona direto.
 * - Em dev rodando o Vite isolado em `localhost:5174`, precisamos prefixar
 *   com `VITE_LARAVEL_URL` para cair na rota Laravel correta.
 *
 * O `next` preserva onde o usuário estava (deep link) para o backend
 * redirecionar de volta após o login bem-sucedido.
 */
function buildLoginUrl(): string {
  const base = (import.meta.env.VITE_LARAVEL_URL as string | undefined) ?? '';
  const next = safeGetSession(RETURN_TO_KEY) ?? '';
  const qs = next ? `?next=${encodeURIComponent(next)}` : '';
  return `${base}/login${qs}`;
}

function goToLogin(): void {
  safeBroadcastLogout();
  window.location.href = buildLoginUrl();
}

// ── Provider ──────────────────────────────────────────────────────────────────

interface SessionExpiredProviderProps {
  children: React.ReactNode;
  /**
   * Quando `false`, não instala o interceptador de fetch nem renderiza o
   * dialog. Use `false` no shell de autenticação (a tela de login não tem
   * sessão para expirar e renderizar o dialog ali seria contraproducente).
   */
  enabled?: boolean;
}

export function SessionExpiredProvider({
  children,
  enabled = true,
}: SessionExpiredProviderProps) {
  const [expired, setExpired] = useState(false);
  const [reason, setReason] = useState<SessionExpiredReason>('idle');

  // Ref espelhando o estado para uso dentro do interceptor de fetch (que
  // não re-renderiza). Evita marcar `expired` múltiplas vezes durante
  // requests em rajada.
  const expiredRef = useRef(false);

  const markExpired = useCallback((nextReason: SessionExpiredReason = 'idle') => {
    if (expiredRef.current) return;
    expiredRef.current = true;
    // Telemetria leve no console — útil para calibrar `session.lifetime`
    // em produção e detectar padrões anormais (ex.: muitos `csrf` em
    // sequência indica que o cookie de sessão está sendo invalidado).
    if (typeof console !== 'undefined' && typeof console.warn === 'function') {
      console.warn('[session-expired]', {
        reason: nextReason,
        at: new Date().toISOString(),
        path: window.location.pathname,
      });
    }
    setReason(nextReason);
    setExpired(true);
    // Salva onde o usuário estava para retornar após login.
    try {
      safeSetSession(RETURN_TO_KEY, window.location.pathname + window.location.search);
    } catch {
      /* noop */
    }
  }, []);

  const clear = useCallback(() => {
    expiredRef.current = false;
    setExpired(false);
    setReason('idle');
  }, []);

  // ── Interceptador de fetch (idempotente entre HMRs) ─────────────────────────
  useEffect(() => {
    if (!enabled) return;
    if (window.__sessionExpiredPatched) return;
    window.__sessionExpiredPatched = true;

    const originalFetch = window.fetch.bind(window);
    window.fetch = async (...args: Parameters<typeof fetch>) => {
      const response = await originalFetch(...args);
      const detected = await classifyResponse(response);
      if (detected) markExpired(detected);
      return response;
    };
    // Sem cleanup: o patch é singleton intencional (o provider vive enquanto
    // o app vive). Tentar restaurar no unmount restauraria um `fetch` que
    // outras libs talvez tenham re-patcheado depois.
  }, [enabled, markExpired]);

  // ── Sincronização entre abas: logout em uma aba expira nas outras ───────────
  useEffect(() => {
    if (!enabled) return;
    const onStorage = (e: StorageEvent) => {
      if (e.key === LOGOUT_BROADCAST_KEY && e.newValue) {
        markExpired('logout-other-tab');
      }
    };
    window.addEventListener('storage', onStorage);
    return () => window.removeEventListener('storage', onStorage);
  }, [enabled, markExpired]);

  const value = useMemo<SessionExpiredContextValue>(
    () => ({ expired, reason, markExpired, clear }),
    [expired, reason, markExpired, clear],
  );

  return (
    <SessionExpiredContext.Provider value={value}>
      {children}
      {enabled && <SessionExpiredDialog open={expired} reason={reason} />}
    </SessionExpiredContext.Provider>
  );
}

// ── Dialog ────────────────────────────────────────────────────────────────────

interface SessionExpiredDialogProps {
  open: boolean;
  reason: SessionExpiredReason;
}

interface DialogCopy {
  title: string;
  description: string;
}

function copyForReason(reason: SessionExpiredReason): DialogCopy {
  switch (reason) {
    case 'csrf':
      return {
        title: 'Token de segurança expirou',
        description:
          'O token de proteção desta página expirou. Faça login novamente para continuar.',
      };
    case 'logout-other-tab':
      return {
        title: 'Você saiu em outra aba',
        description:
          'Detectamos que sua sessão foi encerrada em outra janela. Faça login novamente para continuar usando o sistema aqui.',
      };
    case 'manual':
      return {
        title: 'Sessão encerrada',
        description: 'Sua sessão foi encerrada. Faça login novamente para continuar.',
      };
    case 'idle':
    default:
      return {
        title: 'Sessão expirada',
        description: 'Sua sessão expirou por inatividade. Faça login novamente para continuar.',
      };
  }
}

function SessionExpiredDialog({ open, reason }: SessionExpiredDialogProps) {
  const { title, description } = copyForReason(reason);
  const [seconds, setSeconds] = useState(REDIRECT_SECONDS);

  // Conta regressiva: quando o dialog abre, dispara o intervalo. Ao chegar
  // em 0 redireciona automaticamente para `/login`. Garante que requests
  // pendentes não fiquem batendo no servidor por minutos.
  useEffect(() => {
    if (!open) return;
    setSeconds(REDIRECT_SECONDS);

    const id = window.setInterval(() => {
      setSeconds((current) => {
        if (current <= 1) {
          window.clearInterval(id);
          goToLogin();
          return 0;
        }
        return current - 1;
      });
    }, 1000);

    return () => window.clearInterval(id);
  }, [open]);

  return (
    <Dialog open={open}>
      <DialogContent
        className="w-full max-w-[440px] max-h-[95%]"
        showCloseButton={false}
        onPointerDownOutside={(e) => e.preventDefault()}
        onEscapeKeyDown={(e) => e.preventDefault()}
      >
        {/* Title/Description visualmente escondidos, presentes para acessibilidade
            (Radix exige ambos para anunciar a modal corretamente em leitores de tela). */}
        <DialogHeader className="sr-only">
          <DialogTitle>{title}</DialogTitle>
          <DialogDescription>{description}</DialogDescription>
        </DialogHeader>

        <div className="flex flex-col items-center gap-5 py-6 text-center">
          <img
            src={toAbsoluteUrl('/media/illustrations/23.svg')}
            className="block dark:hidden max-h-[140px]"
            alt=""
            aria-hidden="true"
          />
          <img
            src={toAbsoluteUrl('/media/illustrations/23-dark.svg')}
            className="hidden dark:block max-h-[140px]"
            alt=""
            aria-hidden="true"
          />

          <div className="flex items-center gap-2">
            <ShieldAlert className="size-5 text-destructive" aria-hidden="true" />
            <h3 className="text-lg font-semibold text-foreground">{title}</h3>
          </div>

          <p className="text-sm text-muted-foreground max-w-xs whitespace-pre-line">
            {description}
          </p>

          <div
            className="flex flex-col gap-2 w-full max-w-[260px] pt-1"
            aria-live="polite"
          >
            <Button
              variant="primary"
              size="lg"
              className="gap-2"
              onClick={goToLogin}
              autoFocus
            >
              <LogIn className="size-4" aria-hidden="true" />
              <span>
                Ir para login
                {seconds > 0 && (
                  <span className="ms-1 opacity-70 tabular-nums">({seconds}s)</span>
                )}
              </span>
            </Button>
            <Button
              variant="outline"
              size="sm"
              className="gap-2"
              onClick={() => window.location.reload()}
            >
              <RefreshCcw className="size-4" aria-hidden="true" />
              Tentar novamente
            </Button>
          </div>
        </div>
      </DialogContent>
    </Dialog>
  );
}
