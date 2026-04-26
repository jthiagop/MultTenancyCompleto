import { useEffect, useRef } from 'react';

/**
 * Listener real-time para atualizações da Domus IA.
 *
 * Estratégia em camadas:
 *
 * 1. Se `window.Echo` estiver disponível (Laravel Echo + Reverb/Pusher
 *    configurados), assina o canal privado `tenant.{tenantId}.domus-ia` e
 *    chama `onUpdate` imediatamente quando o evento `documento.processado`
 *    é recebido.
 *
 * 2. Se Echo não estiver presente OU `tenantId` não puder ser resolvido,
 *    usa polling adaptativo: chama `onUpdate` a cada `pollIntervalMs`
 *    enquanto `shouldPoll` retornar `true`. Tipicamente o consumidor
 *    desabilita o polling quando não há documentos pendentes.
 *
 * O hook é seguro para SSR (não toca em `window` no escopo de módulo) e
 * limpa todos os timers/canais no unmount.
 */
export interface UseDomusIaRealtimeOptions {
  /**
   * Callback chamado quando há mudança de status (sucesso ou erro) ou
   * quando o polling dispara. Deve atualizar o estado/recarregar dados.
   */
  onUpdate: (payload?: DocumentoProcessadoPayload) => void;

  /**
   * Predicate que indica se ainda faz sentido fazer polling. Volte
   * `false` quando não houver documentos pendentes para parar o loop.
   */
  shouldPoll: () => boolean;

  /**
   * Intervalo do polling em ms. Default: 5000.
   */
  pollIntervalMs?: number;

  /**
   * ID do tenant para compor o canal privado de broadcast. Quando
   * indefinido, o hook opera apenas em modo polling.
   */
  tenantId?: string | null;
}

export interface DocumentoProcessadoPayload {
  documento_id: number;
  status: 'processado' | 'erro' | 'lancado' | string;
  mensagem?: string | null;
}

interface EchoLike {
  private(channel: string): {
    listen(event: string, cb: (e: DocumentoProcessadoPayload) => void): unknown;
    stopListening(event: string): unknown;
  };
  leave(channel: string): unknown;
}

declare global {
  interface Window {
    Echo?: EchoLike;
  }
}

export function useDomusIaRealtime({
  onUpdate,
  shouldPoll,
  pollIntervalMs = 5000,
  tenantId,
}: UseDomusIaRealtimeOptions): void {
  // Mantém a callback estável entre renders sem reassinar o canal.
  const onUpdateRef = useRef(onUpdate);
  const shouldPollRef = useRef(shouldPoll);

  useEffect(() => {
    onUpdateRef.current = onUpdate;
    shouldPollRef.current = shouldPoll;
  }, [onUpdate, shouldPoll]);

  useEffect(() => {
    let cancelled = false;
    let pollTimer: ReturnType<typeof setTimeout> | null = null;
    let channelName: string | null = null;
    const echo = typeof window !== 'undefined' ? window.Echo : undefined;

    if (echo && tenantId) {
      channelName = `tenant.${tenantId}.domus-ia`;
      try {
        echo.private(channelName).listen('.documento.processado', (payload) => {
          if (cancelled) return;
          onUpdateRef.current?.(payload);
        });
      } catch {
        // Echo configurado mas canal indisponível — segue para polling.
        channelName = null;
      }
    }

    const tick = () => {
      if (cancelled) return;
      if (shouldPollRef.current()) {
        onUpdateRef.current?.();
        pollTimer = setTimeout(tick, pollIntervalMs);
      } else {
        // Reagenda uma checagem leve para reativar o polling se
        // novos pendentes aparecerem (ex.: novo upload).
        pollTimer = setTimeout(tick, pollIntervalMs * 2);
      }
    };

    pollTimer = setTimeout(tick, pollIntervalMs);

    return () => {
      cancelled = true;
      if (pollTimer) clearTimeout(pollTimer);
      if (channelName && echo) {
        try {
          echo.private(channelName).stopListening('.documento.processado');
          echo.leave(channelName);
        } catch {
          // ignora
        }
      }
    };
  }, [tenantId, pollIntervalMs]);
}
