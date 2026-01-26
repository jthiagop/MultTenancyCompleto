import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/app.js',
                'resources/css/app.css',
                'resources/tenancy/assets/plugins/global/plugins.bundle.css',
                'resources/tenancy/assets/css/style.bundle.css',
                'resources/js/pages/conciliacoes/historico.js',
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
});
