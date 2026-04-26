import { useCallback, useEffect, useRef, useState } from 'react';
import { notify } from '@/lib/notify';

export interface INotification {
  id: string;
  icon: string;
  color: string;
  title: string;
  message: string;
  action_url: string | null;
  target: string;
  tipo: string;
  categoria: string; // 'financeiro' | 'sistema' | 'geral' | ...
  read_at: string | null;
  created_at: string;
  created_at_iso: string;
  triggered_by?: { name: string; avatar: string | null } | null;
  // campos de arquivo/relatório
  file_type?: string | null;
  file_size?: string | null;
  expires_at?: string | null;
  expires_in?: string | null;
  expires_percent?: number | null;
  // campos financeiros (conta vencendo / lançamento)
  urgencia?: 'atrasado' | 'hoje' | 'amanha' | 'semana' | null;
  sub_tipo?: 'receita' | 'despesa' | null;
  acao?: 'criado' | 'atualizado' | 'pago' | 'recebido' | null;
  transacao_id?: number | null;
  data_vencimento?: string | null;      // "DD/MM/YYYY"
  data_vencimento_iso?: string | null;  // "YYYY-MM-DD"
  valor?: number | null;
  nome_matriz?: string | null;
}

interface ApiResponse {
  success: boolean;
  notifications: INotification[];
  unread_count: number;
  pagination?: {
    current_page: number;
    last_page: number;
    total: number;
    has_more: boolean;
  };
}

async function fetchJson<T>(url: string, signal?: AbortSignal): Promise<T> {
  const res = await fetch(url, {
    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
    credentials: 'same-origin',
    signal,
  });
  if (!res.ok) throw new Error(`HTTP ${res.status}`);
  return res.json() as Promise<T>;
}

async function postJson(url: string): Promise<void> {
  await fetch(url, {
    method: 'POST',
    headers: {
      Accept: 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
      'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? '',
    },
    credentials: 'same-origin',
  });
}

async function deleteJson(url: string): Promise<void> {
  await fetch(url, {
    method: 'DELETE',
    headers: {
      Accept: 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
      'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? '',
    },
    credentials: 'same-origin',
  });
}

const LAST_SEEN_KEY = 'dominus.notifications.last_seen_at';

function getLastSeenAt(): number {
  try {
    const raw = localStorage.getItem(LAST_SEEN_KEY);
    return raw ? Number(raw) || 0 : 0;
  } catch {
    return 0;
  }
}

function setLastSeenAt(ts: number): void {
  try {
    localStorage.setItem(LAST_SEEN_KEY, String(ts));
  } catch {
    /* localStorage indisponível — ignora */
  }
}

// ---------------------------------------------------------------------------
// Echo (broadcast) — opcional. Quando window.Echo estiver presente (Reverb /
// Pusher), assinamos `private-tenant.{tenantId}.user.{userId}.notifications`
// e ao receber `notification.count.changed` fazemos um reload imediato.
// Sem Echo, o polling continua funcionando como fallback.
// ---------------------------------------------------------------------------
interface EchoLike {
  private(channel: string): {
    listen(event: string, cb: (e: unknown) => void): unknown;
    stopListening(event: string): unknown;
  };
  leave(channel: string): unknown;
}

declare global {
  interface Window {
    Echo?: EchoLike;
  }
}

function getMeta(name: string): string | null {
  if (typeof document === 'undefined') return null;
  return document.querySelector<HTMLMetaElement>(`meta[name="${name}"]`)?.content ?? null;
}

export function useNotifications(pollIntervalMs = 30_000) {
  const [notifications, setNotifications] = useState<INotification[]>([]);
  const [unreadCount, setUnreadCount]     = useState(0);
  const [loading, setLoading]             = useState(false);
  const abortRef    = useRef<AbortController | null>(null);
  const knownIds    = useRef<Set<string>>(new Set());
  const isFirstLoad = useRef(true);

  const load = useCallback(async () => {
    abortRef.current?.abort();
    const ctrl = new AbortController();
    abortRef.current = ctrl;
    setLoading(true);
    try {
      const res = await fetchJson<ApiResponse>('/notifications/all?filter=all&page=1', ctrl.signal);
      const incoming: INotification[] = Array.isArray(res.notifications) ? res.notifications : [];

      if (isFirstLoad.current) {
        // Primeiro carregamento (também após reload da página): apenas registra
        // os IDs e usa a marca de tempo persistida para decidir se algo é novo.
        // Sem essa proteção todo F5 disparava 5–10 toasts de novo.
        isFirstLoad.current = false;
        const lastSeen = getLastSeenAt();
        const novasDesdeLastSeen: INotification[] = [];

        for (const n of incoming) {
          knownIds.current.add(n.id);
          const created = new Date(n.created_at_iso).getTime();
          if (created > lastSeen) {
            novasDesdeLastSeen.push(n);
          }
        }

        // Se houver notificações criadas após o último timestamp visto e a página
        // está visível, mostra um toast resumido — mas apenas um, agrupado.
        if (novasDesdeLastSeen.length > 0 && !document.hidden) {
          const total = novasDesdeLastSeen.length;
          if (total === 1) {
            const n = novasDesdeLastSeen[0];
            notify.info(n.title, n.message || undefined, { duration: 6000 });
          } else {
            notify.info(`${total} novas notificações`, undefined, { duration: 6000 });
          }
        }
      } else {
        // Polls seguintes: detecta IDs ainda não vistos nesta sessão.
        const novas = incoming.filter((n) => !knownIds.current.has(n.id));
        novas.forEach((n) => knownIds.current.add(n.id));

        const relatorios = novas.filter((n) => n.tipo === 'relatorio_gerado');
        const outras     = novas.filter((n) => n.tipo !== 'relatorio_gerado');

        if (relatorios.length > 0) {
          notify.dismissPdfLoading();
          for (const r of relatorios) {
            notify.pdfReady({
              title: r.title,
              fileSize: r.file_size,
              fileType: r.file_type,
              expiresIn: r.expires_in,
              downloadUrl: r.action_url,
            });
          }
        }

        if (outras.length === 1) {
          const n = outras[0];
          notify.info(n.title, n.message || undefined, { duration: 8000 });
        } else if (outras.length > 1) {
          notify.info(
            `${outras.length} novas notificações`,
            outras.map((n) => n.title).join(' · '),
            { duration: 8000 },
          );
        }
      }

      setNotifications(incoming);
      setUnreadCount(res.unread_count ?? 0);

      // Atualiza o marker apenas com a notificação MAIS NOVA recebida no payload —
      // assim, mesmo após reload, só haverá toast para algo de fato novo.
      if (incoming.length > 0) {
        const newest = incoming.reduce((acc, n) => {
          const t = new Date(n.created_at_iso).getTime();
          return t > acc ? t : acc;
        }, 0);
        if (newest > 0) setLastSeenAt(newest);
      }
    } catch (e: unknown) {
      if (e instanceof Error && e.name !== 'AbortError') console.error('[useNotifications]', e);
    } finally {
      setLoading(false);
    }
  }, []);

  // Polling: pausa quando a aba não está visível e retoma na visibilidade.
  // Quando broadcast (Echo) está disponível o polling roda mais espaçado
  // — ele vira só um "safety net", já que o real-time avisa as mudanças.
  useEffect(() => {
    let intervalId: number | null = null;
    let channelName: string | null = null;
    const echo = typeof window !== 'undefined' ? window.Echo : undefined;
    const tenantId = getMeta('tenant-id');
    const userId   = getMeta('user-id');
    const hasRealtime = Boolean(echo && tenantId && userId);
    const effectivePollMs = hasRealtime ? Math.max(pollIntervalMs, 120_000) : pollIntervalMs;

    const start = () => {
      if (intervalId !== null) return;
      intervalId = window.setInterval(() => {
        if (!document.hidden) void load();
      }, effectivePollMs);
    };

    const stop = () => {
      if (intervalId !== null) {
        clearInterval(intervalId);
        intervalId = null;
      }
    };

    const onVisibility = () => {
      if (document.hidden) {
        stop();
      } else {
        // Ao voltar a aba, faz um load imediato e religa o polling
        void load();
        start();
      }
    };

    void load();
    if (!document.hidden) start();
    document.addEventListener('visibilitychange', onVisibility);

    if (hasRealtime && echo && tenantId && userId) {
      channelName = `tenant.${tenantId}.user.${userId}.notifications`;
      try {
        echo.private(channelName).listen('.notification.count.changed', () => {
          // Em vez de mexer no estado com base no payload (que pode estar
          // dessincronizado com a paginação local), recarregamos a lista
          // — barato e mantém a UI 100% consistente.
          if (!document.hidden) void load();
        });
      } catch {
        // Sem broadcast disponível em runtime — segue só com polling.
        channelName = null;
      }
    }

    return () => {
      stop();
      document.removeEventListener('visibilitychange', onVisibility);
      abortRef.current?.abort();
      if (channelName && echo) {
        try {
          echo.private(channelName).stopListening('.notification.count.changed');
          echo.leave(channelName);
        } catch {
          /* ignore */
        }
      }
    };
  }, [load, pollIntervalMs]);

  const markAsRead = useCallback(async (id: string) => {
    setNotifications((prev) => {
      const target = prev.find((n) => n.id === id);
      if (target && !target.read_at) {
        setUnreadCount((c) => Math.max(0, c - 1));
      }
      return prev.map((n) => (n.id === id ? { ...n, read_at: new Date().toISOString() } : n));
    });
    await postJson(`/notifications/${id}/read`);
  }, []);

  const markAllAsRead = useCallback(async () => {
    const now = new Date().toISOString();
    setNotifications((prev) => prev.map((n) => ({ ...n, read_at: n.read_at ?? now })));
    setUnreadCount(0);
    await postJson('/notifications/mark-all-read');
    void load();
  }, [load]);

  const remove = useCallback(async (id: string) => {
    setNotifications((prev) => {
      const target = prev.find((n) => n.id === id);
      // Decrementa contador apenas se a notificação removida estava NÃO LIDA.
      // Antes, deletar uma notificação já lida zerava o badge incorretamente.
      if (target && !target.read_at) {
        setUnreadCount((c) => Math.max(0, c - 1));
      }
      return prev.filter((n) => n.id !== id);
    });
    await deleteJson(`/notifications/${id}`);
  }, []);

  const archiveRead = useCallback(async () => {
    await deleteJson('/notifications/clear/read');
    void load();
  }, [load]);

  return { notifications, unreadCount, loading, load, markAsRead, markAllAsRead, remove, archiveRead };
}
