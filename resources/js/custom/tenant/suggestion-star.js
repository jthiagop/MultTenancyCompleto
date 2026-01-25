/**
 * Gerenciador de Estrelas de Sugestão Inteligente
 * Controla a exibição e comportamento das estrelas que indicam sugestões da IA
 */
class SuggestionStarManager {
    constructor() {
        console.log('🌟 [SuggestionStarManager] Construtor chamado');
        this.stars = new Map();
        this.init();
    }

    init() {
        console.log('🌟 [SuggestionStarManager] Init chamado. Document.readyState:', document.readyState);
        // Aguarda o DOM estar pronto
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.initializeStars());
        } else {
            this.initializeStars();
        }
    }

    initializeStars() {
        console.log('🌟 [SuggestionStarManager] Inicializando estrelas...');
        // Busca todas as estrelas de sugestão na página
        const starWrappers = document.querySelectorAll('[class*="suggestion-star-"]');
        console.log('🌟 [SuggestionStarManager] Estrelas encontradas:', starWrappers.length);
        
        starWrappers.forEach(starWrapper => {
            console.log('🌟 [SuggestionStarManager] Processando estrela:', starWrapper);
            const classList = Array.from(starWrapper.classList);
            // Pega a classe mais específica (não a genérica "suggestion-star-wrapper")
            const starClass = classList.find(cls => cls.startsWith('suggestion-star-') && cls !== 'suggestion-star-wrapper');
            console.log('🌟 [SuggestionStarManager] Classes:', classList, 'Star class:', starClass);
            
            if (!starClass) {
                console.warn('⚠️ [SuggestionStarManager] Star class não encontrada');
                return;
            }
            
            // Extrai o ID do select da classe
            const selectId = starClass.replace('suggestion-star-', '');
            const selectElement = document.getElementById(selectId);
            const suggestedValue = starWrapper.getAttribute('data-suggested-value');
            
            console.log('🌟 [SuggestionStarManager] Select ID:', selectId);
            console.log('🌟 [SuggestionStarManager] Select Element:', selectElement);
            console.log('🌟 [SuggestionStarManager] Suggested Value:', suggestedValue);
            console.log('🌟 [SuggestionStarManager] Current Value:', selectElement?.value);
            
            if (!selectElement) {
                console.error('❌ [SuggestionStarManager] Select element não encontrado para ID:', selectId);
                return;
            }
            
            if (!suggestedValue) {
                console.warn('⚠️ [SuggestionStarManager] Suggested value não definido');
                return;
            }
            
            // Inicializa o tooltip Bootstrap
            let tooltip = null;
            if (typeof bootstrap !== 'undefined') {
                console.log('✅ [SuggestionStarManager] Bootstrap disponível, criando tooltip');
                tooltip = new bootstrap.Tooltip(starWrapper, {
                    trigger: 'hover',
                    html: true
                });
            } else {
                console.warn('⚠️ [SuggestionStarManager] Bootstrap não está disponível');
            }
            
            // Armazena referências
            this.stars.set(selectId, {
                starWrapper,
                selectElement,
                suggestedValue,
                tooltip
            });
            console.log('✅ [SuggestionStarManager] Estrela registrada para select:', selectId);
            
            // Configura eventos
            this.setupEvents(selectId);
            
            // Verifica estado inicial após um pequeno delay (aguarda Select2 inicializar)
            setTimeout(() => {
                const initialValue = selectElement.value;
                console.log('🔍 [SuggestionStarManager] Verificando valor inicial após delay:', selectId, '=', initialValue);
                if (!initialValue || initialValue === '') {
                    console.log('🚫 [SuggestionStarManager] Escondendo estrela inicial (sem valor):', selectId);
                    this.hideStar(selectId);
                } else if (initialValue == suggestedValue) {
                    console.log('✨ [SuggestionStarManager] Mostrando estrela inicial (valor corresponde):', selectId);
                    this.showStar(selectId);
                } else {
                    console.log('🚫 [SuggestionStarManager] Escondendo estrela inicial (valor diferente):', selectId);
                    this.hideStar(selectId);
                }
            }, 300);
        });
        
        console.log('🌟 [SuggestionStarManager] Total de estrelas registradas:', this.stars.size);
    }

    setupEvents(selectId) {
        console.log('🎯 [SuggestionStarManager] Configurando eventos para:', selectId);
        const star = this.stars.get(selectId);
        if (!star) {
            console.error('❌ [SuggestionStarManager] Star não encontrada ao configurar eventos:', selectId);
            return;
        }

        const { starWrapper, selectElement, suggestedValue } = star;

        // Monitora mudanças no select (suporta Select2 e select nativo)
        const hasSelect2 = typeof jQuery !== 'undefined' && jQuery(selectElement).data('select2');
        console.log('🎯 [SuggestionStarManager] Select2 detectado?', hasSelect2);
        
        if (hasSelect2) {
            // Select2
            console.log('🎯 [SuggestionStarManager] Registrando evento change (Select2)');
            jQuery(selectElement).on('change', () => {
                console.log('🔄 [SuggestionStarManager] Evento change disparado (Select2) para:', selectId);
                this.handleSelectChange(selectId);
            });
        } else {
            // Select nativo
            console.log('🎯 [SuggestionStarManager] Registrando evento change (nativo)');
            selectElement.addEventListener('change', () => {
                console.log('🔄 [SuggestionStarManager] Evento change disparado (nativo) para:', selectId);
                this.handleSelectChange(selectId);
            });
        }

        // Previne que cliques na estrela abram o select
        starWrapper.addEventListener('click', (e) => {
            console.log('👆 [SuggestionStarManager] Click na estrela:', selectId);
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            return false;
        });

        starWrapper.addEventListener('mousedown', (e) => {
            console.log('👆 [SuggestionStarManager] Mousedown na estrela:', selectId);
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            return false;
        });
        
        starWrapper.addEventListener('mouseenter', () => {
            console.log('🖱️ [SuggestionStarManager] Mouse entrou na estrela:', selectId);
        });
    }

    handleSelectChange(selectId) {
        console.log('🔄 [SuggestionStarManager] Handling select change para:', selectId);
        const star = this.stars.get(selectId);
        if (!star) {
            console.error('❌ [SuggestionStarManager] Star não encontrada:', selectId);
            return;
        }

        const { starWrapper, selectElement, suggestedValue, tooltip } = star;
        const currentValue = selectElement.value;

        console.log('� [SuggestionStarManager] Comparando valores:');
        console.log('   - Valor atual:', currentValue, '(tipo:', typeof currentValue, ')');
        console.log('   - Valor sugerido:', suggestedValue, '(tipo:', typeof suggestedValue, ')');
        console.log('   - São iguais?', currentValue == suggestedValue);
        console.log('   - Valor vazio?', !currentValue || currentValue === '');

        // Se não tem valor selecionado (placeholder), esconde a estrela
        if (!currentValue || currentValue === '') {
            console.log('🚫 [SuggestionStarManager] Sem valor selecionado, escondendo estrela');
            this.hideStar(selectId);
        }
        // Se o valor mudou e é diferente da sugestão, esconde a estrela
        else if (currentValue != suggestedValue) {
            console.log('❌ [SuggestionStarManager] Valores diferentes, escondendo estrela');
            this.hideStar(selectId);
        } 
        // Se o valor é igual à sugestão, mostra a estrela
        else if (currentValue == suggestedValue) {
            console.log('✅ [SuggestionStarManager] Valores iguais, mostrando estrela');
            this.showStar(selectId);
        }
    }

    hideStar(selectId) {
        console.log('👻 [SuggestionStarManager] Escondendo estrela:', selectId);
        const star = this.stars.get(selectId);
        if (!star) return;

        const { starWrapper, tooltip } = star;
        
        starWrapper.style.display = 'none';
        console.log('👻 [SuggestionStarManager] Estrela escondida');
        
        // Esconde o tooltip se estiver visível
        if (tooltip) {
            tooltip.hide();
            console.log('👻 [SuggestionStarManager] Tooltip escondido');
        }
    }

    showStar(selectId) {
        console.log('✨ [SuggestionStarManager] Mostrando estrela:', selectId);
        const star = this.stars.get(selectId);
        if (!star) return;

        const { starWrapper } = star;
        starWrapper.style.display = 'flex';
        console.log('✨ [SuggestionStarManager] Estrela mostrada');
    }

    // Método público para reinicializar (útil para conteúdo dinâmico)
    reinitialize() {
        this.stars.clear();
        this.initializeStars();
    }

    // Método público para adicionar uma estrela dinamicamente
    addStar(selectId, suggestedValue, tooltipText) {
        const selectElement = document.getElementById(selectId);
        if (!selectElement) return;

        const starWrapper = document.querySelector(`.suggestion-star-${selectId}`);
        if (!starWrapper) return;

        starWrapper.setAttribute('data-suggested-value', suggestedValue);
        starWrapper.setAttribute('title', tooltipText);

        // Inicializa tooltip
        let tooltip = null;
        if (typeof bootstrap !== 'undefined') {
            tooltip = new bootstrap.Tooltip(starWrapper, {
                trigger: 'hover',
                html: true
            });
        }

        this.stars.set(selectId, {
            starWrapper,
            selectElement,
            suggestedValue,
            tooltip
        });

        this.setupEvents(selectId);
    }
}

// Instância global
console.log('🚀 [SuggestionStarManager] Criando instância global...');
window.suggestionStarManager = new SuggestionStarManager();
console.log('🚀 [SuggestionStarManager] Instância global criada e disponível em window.suggestionStarManager');
