/**
 * Módulo principal do Drawer de Lançamento Financeiro
 * 
 * Este arquivo é o ponto de entrada que exporta todos os módulos
 * relacionados ao drawer de lançamento e inicializa os componentes.
 * 
 * @module financeiro/drawer
 * @version 1.0.0
 */

// Utilitários de moeda
export * from '../utils/currency.js';

// Classes principais
export { DrawerParcelasController, getParcelasController } from './DrawerParcelasController.js';
export { DrawerFormManager, getFormManager } from './DrawerFormManager.js';
export { DrawerInitializer, getDrawerInitializer } from './DrawerInitializer.js';

// Importa para inicialização
import { getParcelasController } from './DrawerParcelasController.js';
import { getFormManager } from './DrawerFormManager.js';
import { getDrawerInitializer } from './DrawerInitializer.js';

/**
 * Inicializa todos os módulos do drawer
 * @param {Object} config - Configurações globais
 */
export function initDrawerLancamento(config = {}) {
    console.log('[DrawerLancamento] Inicializando módulos...');
    
    // Aguarda jQuery estar disponível
    if (typeof window.jQuery === 'undefined') {
        console.warn('[DrawerLancamento] jQuery não disponível. Aguardando...');
        setTimeout(() => initDrawerLancamento(config), 100);
        return;
    }
    
    // Aguarda DOM estar pronto
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => initDrawerLancamento(config));
        return;
    }
    
    try {
        // Inicializa controlador de parcelas
        const parcelasController = getParcelasController(config.parcelas || {});
        parcelasController.init();
        
        // Inicializa gerenciador de formulário
        const formManager = getFormManager(config.form || {});
        formManager.init();
        
        // Inicializa o drawer
        const drawerInitializer = getDrawerInitializer(config.drawer || {});
        drawerInitializer.init();
        
        // Expõe instâncias globalmente
        window.drawerParcelasController = parcelasController;
        window.drawerFormManager = formManager;
        window.drawerInitializer = drawerInitializer;
        
        // Função global para abrir drawer (compatibilidade)
        window.abrirDrawerLancamento = function(tipo, origem) {
            drawerInitializer.abrirDrawer(tipo, origem);
        };
        
        console.log('[DrawerLancamento] Módulos inicializados com sucesso');
    } catch (error) {
        console.error('[DrawerLancamento] Erro na inicialização:', error);
    }
}

// Auto-inicialização quando importado
if (typeof window !== 'undefined') {
    // Expõe função de inicialização
    window.initDrawerLancamento = initDrawerLancamento;
    
    // Inicializa automaticamente quando DOM estiver pronto
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => initDrawerLancamento());
    } else {
        // Pequeno delay para garantir que jQuery está carregado
        setTimeout(() => initDrawerLancamento(), 100);
    }
}
