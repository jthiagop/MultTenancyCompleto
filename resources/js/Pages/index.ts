/**
 * Pages
 * 
 * Exporta todas as páginas/views que correspondem às rotas
 * Exemplo: Dashboard, Caixa, Configurações, etc.
 */

// Exemplo de importação de páginas
// export { Dashboard } from './Dashboard';
// export { Caixa } from './Caixa';
// export { Configuracoes } from './Configuracoes';

// Inicializar páginas baseado na rota atual
export const initPages = () => {
    // Obter a rota atual
    const currentPath = window.location.pathname;
    
    // Inicializar página específica baseado na rota
    // Exemplo:
    // if (currentPath.includes('/dashboard')) {
    //     Dashboard.init();
    // } else if (currentPath.includes('/caixa')) {
    //     Caixa.init();
    // }
    
    console.log('Pages initialized for:', currentPath);
};

// Auto-inicializar quando o DOM estiver pronto
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initPages);
} else {
    initPages();
}
