/**
 * DataTable Pane Bundle
 * 
 * Este arquivo deve ser incluído nas views que usam o DataTable Pane.
 * Ele carrega todos os módulos na ordem correta:
 * 
 * 1. Core (interface base + motor)
 * 2. Adapters (financeiro, extrato, secretary)
 * 3. Entry Point (orquestrador)
 * 
 * Uso no Blade:
 * <script src="{{ asset('assets/js/custom/tenant/datatable-pane-bundle.js') }}"></script>
 * 
 * Ou via @vite:
 * @vite(['resources/js/datatable-pane-bundle.js'])
 */

(function() {
    'use strict';

    const BASE_PATH = '/tenancy/assets/js/custom/tenant/';
    
    const scripts = [
        'core/datatable-adapter.js',
        'core/datatable-core.js',
        'adapters/financeiro-adapter.js',
        'adapters/extrato-adapter.js',
        'adapters/secretary-adapter.js',
        'tenant-datatable-pane-v2.js'
    ];

    let loadedCount = 0;

    function loadScript(src) {
        return new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = BASE_PATH + src;
            script.async = false; // Manter ordem de carregamento
            script.onload = () => {
                loadedCount++;
                console.log(`[DataTableBundle] Carregado (${loadedCount}/${scripts.length}): ${src}`);
                resolve();
            };
            script.onerror = () => {
                console.error(`[DataTableBundle] Erro ao carregar: ${src}`);
                reject(new Error(`Failed to load: ${src}`));
            };
            document.head.appendChild(script);
        });
    }

    async function loadAllScripts() {
        console.log('[DataTableBundle] Iniciando carregamento dos módulos...');
        
        for (const script of scripts) {
            try {
                await loadScript(script);
            } catch (error) {
                console.error('[DataTableBundle] Falha no carregamento:', error);
                return;
            }
        }
        
        console.log('[DataTableBundle] Todos os módulos carregados com sucesso!');
    }

    // Iniciar carregamento
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', loadAllScripts);
    } else {
        loadAllScripts();
    }

})();
