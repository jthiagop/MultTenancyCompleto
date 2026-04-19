import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';
import path from 'path';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');
    const backendUrl = env.APP_URL || 'http://localhost:8000';

    return {
    plugins: [
        laravel({
            input: [
                'resources/js/app.js',
                'resources/css/app.css',
                'resources/tenancy/assets/plugins/global/plugins.bundle.css',
                'resources/tenancy/assets/css/style.bundle.css',
                'resources/js/pages/conciliacoes/historico.js',
                // Módulo do Drawer de Lançamento Financeiro
                'resources/js/financeiro/drawer/index.js',
                // React Bridge — monta componentes React dentro de views Blade
                'resources/js/react-bridge.tsx',
            ],
            refresh: true,
            // Build directory padrão (assets será acessado via symlink tenancy/assets/build -> build)
            buildDirectory: 'build',
            ssr: false,
        }),
        react(),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/js'),
            '@react': path.resolve(__dirname, 'resources/js/react/src'),
        },
    },
    build: {
        // Garantir que os assets sejam gerados corretamente
        outDir: 'public/build',
        manifest: 'manifest.json',
        rollupOptions: {
            output: {
                manualChunks: undefined,
            },
        },
    },
    server: {
        // Proxy: encaminha rotas do Laravel ao servidor backend durante o desenvolvimento.
        // Em produção as URLs relativas resolvem diretamente para o mesmo host (sem proxy).
        proxy: {
            '/company': { target: backendUrl, changeOrigin: false },
            '/api': { target: backendUrl, changeOrigin: false },
            '/app': { target: backendUrl, changeOrigin: false },
            '/session': { target: backendUrl, changeOrigin: false },
            '/financeiro': { target: backendUrl, changeOrigin: false },
            '/banco': { target: backendUrl, changeOrigin: false },
            '/logout': { target: backendUrl, changeOrigin: false },
            '/sanctum': { target: backendUrl, changeOrigin: false },
        },
    },
    };
});
