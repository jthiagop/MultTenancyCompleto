/**
 * Bootstrap Configuration
 * Configurações iniciais da aplicação
 */

// Expor window global para bibliotecas externas
window._ = require('lodash');

/**
 * CSRF Token Setup
 */
const token = document.querySelector('meta[name="csrf-token"]');
if (token) {
    window.csrfToken = token.content;
    
    // Configurar headers padrão para fetch
    if (typeof window.fetchHeaders === 'undefined') {
        window.fetchHeaders = {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.csrfToken,
        };
    }
}

/**
 * Global Error Handler
 */
window.addEventListener('error', (event) => {
    console.error('Global error:', event.error);
});

window.addEventListener('unhandledrejection', (event) => {
    console.error('Unhandled promise rejection:', event.reason);
});

/**
 * Axios Configuration (se usar)
 */
if (typeof window.axios !== 'undefined') {
    window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = window.csrfToken;
}

/**
 * Configurações do Metronic
 */
window.hostUrl = window.location.protocol + '//' + window.location.hostname + (window.location.port ? ':' + window.location.port : '') + '/assets/';

// Expor variáveis globais úteis
window.appConfig = {
    apiUrl: window.location.origin + '/api',
    baseUrl: window.location.origin,
    csrfToken: window.csrfToken,
};

console.log('✅ Bootstrap configuration loaded');
