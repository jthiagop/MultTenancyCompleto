<script>
/**
 * DominusEvents - Sistema de eventos global para comunicação entre componentes
 * 
 * Uso:
 * - DominusEvents.emit('transaction.created', { tipo: 'entrada', valor: 500 });
 * - DominusEvents.on('transaction.created', (data) => console.log(data));
 * 
 * Eventos disponíveis:
 * - transaction.created: Lançamento criado
 * - transaction.updated: Lançamento atualizado
 * - transaction.deleted: Lançamento excluído
 * - datatable.reload: Força reload da DataTable
 * - summary.refresh: Atualiza resumos/totais
 * 
 * @version 1.0.0
 */
window.DominusEvents = (function() {
    const listeners = {};
    const debug = false; // Mudar para true para ver logs
    
    return {
        /**
         * Registra um listener para um evento
         * @param {string} event - Nome do evento
         * @param {Function} callback - Função a ser executada
         * @returns {Function} Função para remover o listener
         */
        on(event, callback) {
            if (!listeners[event]) listeners[event] = [];
            listeners[event].push(callback);
                        
            // Retorna função para remover listener
            return () => this.off(event, callback);
        },
        
        /**
         * Remove um listener específico
         */
        off(event, callback) {
            if (!listeners[event]) return;
            listeners[event] = listeners[event].filter(cb => cb !== callback);
        },
        
        /**
         * Emite um evento para todos os listeners
         * @param {string} event - Nome do evento
         * @param {Object} data - Dados do evento
         */
        emit(event, data = {}) {
            const timestamp = new Date().toLocaleTimeString();
            
            
            if (!listeners[event]) return;
            
            listeners[event].forEach(callback => {
                try {
                    callback(data);
                } catch (error) {
                    console.error(`[DominusEvents] Erro ao executar callback do evento "${event}":`, error);
                }
            });
        },
        
        /**
         * Registra listener que executa apenas uma vez
         */
        once(event, callback) {
            const wrapper = (data) => {
                callback(data);
                this.off(event, wrapper);
            };
            this.on(event, wrapper);
        },
        
        /**
         * Lista todos os eventos registrados (debug)
         */
        list() {
            return Object.keys(listeners).map(event => ({
                event,
                count: listeners[event].length
            }));
        }
    };
})();

// Atalhos convenientes
window.emitEvent = (event, data) => DominusEvents.emit(event, data);
window.onEvent = (event, callback) => DominusEvents.on(event, callback);
</script>
