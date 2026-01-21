/**
 * Layouts
 * 
 * Exporta todos os layouts da aplicação
 * Exemplo: Sidebar, Navbar, Footer, etc.
 */

// Exemplo de importação de layouts
// export { Sidebar } from './Sidebar';
// export { Navbar } from './Navbar';
// export { Footer } from './Footer';

// Inicializar layouts quando necessário
export const initLayouts = () => {
    // Inicialização de layouts
    console.log('Layouts initialized');
};

// Auto-inicializar quando o DOM estiver pronto
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initLayouts);
} else {
    initLayouts();
}
