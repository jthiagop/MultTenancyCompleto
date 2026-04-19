/**
 * Interceptor global de fetch para o módulo financeiro.
 *
 * Ao ser instalado (uma única vez no bootstrap da aplicação), substitui
 * window.fetch por uma versão que monitora requisições mutantes
 * (POST, PUT, PATCH, DELETE) para rotas do financeiro.
 *
 * Quando a resposta é bem-sucedida (2xx), dispara o evento customizado
 * "financeiro:saldo-updated" no window. Qualquer hook ou componente pode
 * ouvir esse evento e se atualizar automaticamente — sem que nenhum
 * método de mutação precise ser modificado individualmente.
 *
 * Rotas monitoradas (prefixos):
 *   /app/financeiro/
 *   /financeiro/transacao/
 *   /financeiro/transferencia
 *
 * Uso:
 *   import { installFinanceiroFetchInterceptor } from '@/lib/financeiro-fetch-interceptor';
 *   installFinanceiroFetchInterceptor(); // chame uma vez no main.tsx
 */

const MUTATING_METHODS = new Set(['POST', 'PUT', 'PATCH', 'DELETE']);

const WATCHED_PREFIXES = [
  '/app/financeiro/',
  '/financeiro/transacao/',
  '/financeiro/transferencia',
  '/banco/reverse-type',
  '/banco/batch-reverse-type',
  '/banco/mark-as-open',
];

function isWatchedUrl(input: RequestInfo | URL): boolean {
  let url: string;
  if (typeof input === 'string') {
    url = input;
  } else if (input instanceof URL) {
    url = input.pathname;
  } else {
    // Request object
    url = input.url;
  }
  // Normaliza para pathname relativo caso venha como URL absoluta
  try {
    url = new URL(url, window.location.origin).pathname;
  } catch {
    // se falhar, usa como está
  }
  return WATCHED_PREFIXES.some((prefix) => url.startsWith(prefix));
}

let installed = false;

export function installFinanceiroFetchInterceptor(): void {
  if (installed) return;
  installed = true;

  const originalFetch = window.fetch.bind(window);

  window.fetch = async function interceptedFetch(
    input: RequestInfo | URL,
    init?: RequestInit,
  ): Promise<Response> {
    const response = await originalFetch(input, init);

    const method = (init?.method ?? 'GET').toUpperCase();

    if (
      MUTATING_METHODS.has(method) &&
      isWatchedUrl(input) &&
      response.ok
    ) {
      window.dispatchEvent(new CustomEvent('financeiro:saldo-updated'));
    }

    return response;
  };
}
