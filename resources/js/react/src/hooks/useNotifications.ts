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

export function useNotifications(pollIntervalMs = 30_000) {
  const [notifications, setNotifications] = useState<INotification[]>([]);
  const [unreadCount, setUnreadCount]     = useState(0);
  const [loading, setLoading]             = useState(false);
  const abortRef   = useRef<AbortController | null>(null);
  const knownIds   = useRef<Set<string>>(new Set());
  const isFirstLoad = useRef(true);

  const load = useCallback(async () => {
    abortRef.current?.abort();
    const ctrl = new AbortController();
    abortRef.current = ctrl;
    setLoading(true);
    try {
      const res = await fetchJson<ApiResponse>('/notifications/all?filter=all&page=1', ctrl.signal);
      const incoming: INotification[] = Array.isArray(res.notifications) ? res.notifications : [];

      // Na primeira carga apenas popula o conjunto de IDs conhecidos — sem toast
      if (isFirstLoad.current) {
        isFirstLoad.current = false;
        incoming.forEach((n) => knownIds.current.add(n.id));
      } else {
        // Polls seguintes: detecta IDs ainda não vistos e exibe toast por notificação
        const novas = incoming.filter((n) => !knownIds.current.has(n.id));
        novas.forEach((n) => knownIds.current.add(n.id));

        // Se chegou um relatório gerado, descarta os toasts de loading de PDF
        // e exibe toast rico com ícone de arquivo + botão "Abrir"
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
    } catch (e: unknown) {
      if (e instanceof Error && e.name !== 'AbortError') console.error('[useNotifications]', e);
    } finally {
      setLoading(false);
    }
  }, []);

  // Carrega ao montar e a cada intervalo
  useEffect(() => {
    void load();
    const id = setInterval(() => void load(), pollIntervalMs);
    return () => {
      clearInterval(id);
      abortRef.current?.abort();
    };
  }, [load, pollIntervalMs]);

  const markAsRead = useCallback(async (id: string) => {
    setNotifications((prev) =>
      prev.map((n) => (n.id === id ? { ...n, read_at: new Date().toISOString() } : n)),
    );
    setUnreadCount((c) => Math.max(0, c - 1));
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
    setNotifications((prev) => prev.filter((n) => n.id !== id));
    setUnreadCount((c) => Math.max(0, c - 1));
    await deleteJson(`/notifications/${id}`);
  }, []);

  const archiveRead = useCallback(async () => {
    await deleteJson('/notifications/clear/read');
    void load();
  }, [load]);

  return { notifications, unreadCount, loading, load, markAsRead, markAllAsRead, remove, archiveRead };
}
