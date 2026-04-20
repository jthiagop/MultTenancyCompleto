import { fileURLToPath, URL } from 'node:url';
import fs from 'node:fs';
import path from 'node:path';
import tailwindcss from '@tailwindcss/vite';
import react from '@vitejs/plugin-react';
import { defineConfig, loadEnv, type Plugin } from 'vite';
import type { IncomingMessage, ServerResponse } from 'node:http';

function reactHotFile(hotFilePath: string, devServerUrl: string): Plugin {
  return {
    name: 'react-hot-file',
    apply: 'serve',
    configureServer(server) {
      const writeHotFile = () => {
        fs.mkdirSync(path.dirname(hotFilePath), { recursive: true });
        fs.writeFileSync(hotFilePath, `${devServerUrl}\n`, 'utf8');
      };

      const removeHotFile = () => {
        if (fs.existsSync(hotFilePath)) {
          fs.unlinkSync(hotFilePath);
        }
      };

      writeHotFile();
      server.httpServer?.once('close', removeHotFile);
    },
  };
}

/** Rotas do React Router (sob /app/*) precisam servir index.html em dev. */
function spaIndexFallback(proxiedPrefixes: string[], routerBase: string): Plugin {
  return {
    name: 'spa-index-fallback',
    // Retornar uma função faz o Vite registrar o middleware DEPOIS dos
    // middlewares internos (incluindo o proxy). Assim o proxy processa
    // /api/*, /login, etc. primeiro; só URLs que sobrarem caem aqui.
    configureServer(server) {
      return () => {
        server.middlewares.use((req, _res, next) => {
          if (req.method !== 'GET' && req.method !== 'HEAD') return next();
          const path = req.url?.split('?')[0] ?? '';
          // Deixa o proxy do Laravel lidar com as rotas de backend
          if (proxiedPrefixes.some((p) => path.startsWith(p))) return next();
          if (
            path.startsWith('/@') ||
            path.startsWith('/node_modules') ||
            path.startsWith('/src') ||
            path === '/' ||
            path === ''
          ) {
            return next();
          }
          if (path.includes('.')) return next();
          req.url = '/index.html';
          next();
        });
      };
    },
  };
}

/**
 * Intercepta respostas de redirect (302) do Laravel após /login
 * e reescreve a Location para /app/ — assim o browser cai no painel React,
 * não no dashboard Blade (que exige assets Metronic separados).
 */
function rewritePostLoginRedirect() {
  return function (
    proxyRes: IncomingMessage & { headers: Record<string, string | string[] | undefined> },
    _req: IncomingMessage,
    res: ServerResponse,
  ) {
    if (![301, 302, 303, 307, 308].includes(proxyRes.statusCode ?? 0)) return;
    const location = proxyRes.headers['location'];
    if (!location) return;
    const loc = Array.isArray(location) ? location[0] : location;
    // Redireciona /dashboard → /app/ (painel React)
    if (loc.endsWith('/dashboard') || loc === '/dashboard' || loc === '/') {
      proxyRes.headers['location'] = '/app/';
    }
  };
}

export default defineConfig(({ mode }) => {
  const env = loadEnv(mode, process.cwd(), '');
  // URL do Laravel no dev (ex.: http://recife.localhost:8000)
  const laravelUrl = (env.VITE_LARAVEL_URL ?? 'http://127.0.0.1:8000').replace(/\/$/, '');

  /**
   * Todas as rotas proxiadas ao Laravel (GET + POST + etc.).
   * O React SPA só é servido para rotas que NÃO estejam nesta lista.
   * As rotas de auth (/login, /logout…) vão ao Blade do Laravel;
   * após login bem-sucedido o rewritePostLoginRedirect redireciona para /app/.
   */
  const proxiedPaths = [
    '/login',
    '/logout',
    '/register',
    '/forgot-password',
    '/reset-password',
    '/sanctum',
    '/password',     // alteração de senha obrigatória (first-access)
    '/api',          // todas as rotas /api/* incluindo /api/auth/status
    '/app',          // ReactAppController + rotas do painel React (inclui /app/session/switch-company)
    '/financeiro',   // POST parceiros, GET form-data, etc.
    '/conciliacao',  // Conciliação bancária (buscar-lancamento, conciliar-lote, dashboard-ia, etc.)
    '/banco',        // Sugestões, chart-data, stats, transações
    '/recorrencias', // configurações de recorrência (GET/POST) — mesmo prefixo que tenant.php
    '/notifications', // Sistema de notificações do usuário
    '/users',        // CRUD de usuários (UserController)
    '/file',         // avatares e arquivos do tenant (User::avatar_url → /file/…)
    '/upload-ofx',   // importação de extrato OFX
    '/integracoes',  // integrações WhatsApp/DDA/E-mail (Domus IA)
    '/transferencia', // POST transferência entre contas (tenant)
    '/whatsapp',     // QR code e status de vinculação WhatsApp (tenant)
    '/relatorios',   // entidades financeiras (renomear, etc.) e relatórios
    '/cemiterio',    // módulo cemitério (difuntos, túmulos, ocupações)
    '/contabilidade', // módulo contabilidade (categorias, plano de contas, mapeamento)
    '/tenancy',      // assets e rotas do pacote stancl/tenancy
    '/media',        // favicon e assets em public/media (QR Dominus no sheet WhatsApp)
    '/vendor',       // assets PHP (flasher, debugbar, etc.) injetados pelo Laravel
    '/_debugbar',
    '/react-app',    // assets buildados do React shell em public/react-app
  ];

  const rewrite = rewritePostLoginRedirect();
  const devServerUrl = (env.REACT_VITE_DEV_URL ?? 'http://localhost:5174').replace(/\/$/, '');

  const baseProxyOptions = {
    changeOrigin: false, // preserva Host (ex.: recife.localhost) para o tenant ser detectado
    secure: false,
    configure: (proxy: { on: (event: string, fn: (...args: unknown[]) => void) => void }) => {
      proxy.on('proxyRes', rewrite as (...args: unknown[]) => void);
    },
  };

  const proxyConfig: Record<string, object> = {};

  for (const path of proxiedPaths) {
    proxyConfig[path] = {
      ...baseProxyOptions,
      target: laravelUrl,
    };
  }

  const routerBase = (env.VITE_ROUTER_BASE ?? '/app').replace(/\/$/, '');

  return {
    plugins: [
      react(),
      tailwindcss(),
      spaIndexFallback(proxiedPaths, routerBase),
      reactHotFile(path.resolve(__dirname, '../../../public/react-app/hot'), devServerUrl),
    ],
    appType: 'spa',
    // Em produção os assets buildados ficam em public/react-app/ e são servidos
    // em /react-app/*. Em dev o Vite serve tudo na raiz. Essa base afeta apenas
    // imports internos do bundle (ex: import foo from './img.png'); os assets
    // estáticos referenciados via toAbsoluteUrl('/media/…') continuam root-relative.
    base: mode === 'production' ? '/react-app/' : '/',
    server: {
      port: 5174,
      strictPort: true,
      host: true,
      proxy: proxyConfig,
    },
    resolve: {
      alias: {
        '@': fileURLToPath(new URL('./src', import.meta.url)),
      },
    },
    build: {
      outDir: '../../../public/react-app',
      emptyOutDir: true,
      manifest: true,
      chunkSizeWarningLimit: 3000,
    },
  };
});
