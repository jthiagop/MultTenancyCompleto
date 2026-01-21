/**
 * Components
 * 
 * Exporta todos os componentes Shadcn
 * Exemplo: Botões, Inputs, Selects, Modals, etc.
 */

// Exemplo de importação de componentes
// export { Button } from './Button';
// export { Input } from './Input';
// export { Select } from './Select';

// Inicializar componentes quando necessário
export const initComponents = () => {
    // Inicialização de componentes
    console.log('Components initialized');
};

// Auto-inicializar quando o DOM estiver pronto
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initComponents);
} else {
    initComponents();
}
