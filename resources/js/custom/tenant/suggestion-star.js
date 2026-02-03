/**
 * Gerenciador de Estrelas de Sugestão Inteligente
 * Controla a exibição e comportamento das estrelas que indicam sugestões da IA
 */
class SuggestionStarManager {
    constructor() {
        this.stars = new Map();
        this.init();
    }

    init() {
        // Aguarda o DOM estar pronto
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.initializeStars());
        } else {
            this.initializeStars();
        }
    }

    initializeStars() {
        // Busca todas as estrelas de sugestão na página
        const starWrappers = document.querySelectorAll('[class*="suggestion-star-"]');
        
        starWrappers.forEach(starWrapper => {
            const classList = Array.from(starWrapper.classList);
            // Pega a classe mais específica (não a genérica "suggestion-star-wrapper")
            const starClass = classList.find(cls => cls.startsWith('suggestion-star-') && cls !== 'suggestion-star-wrapper');
            
            if (!starClass) {
                return;
            }
            
            // Extrai o ID do select da classe
            const selectId = starClass.replace('suggestion-star-', '');
            const selectElement = document.getElementById(selectId);
            const suggestedValue = starWrapper.getAttribute('data-suggested-value');
            
            if (!selectElement) {
                return;
            }
            
            if (!suggestedValue) {
                return;
            }
            
            // Inicializa o tooltip Bootstrap
            let tooltip = null;
            if (typeof bootstrap !== 'undefined') {
                tooltip = new bootstrap.Tooltip(starWrapper, {
                    trigger: 'hover',
                    html: true
                });
            } else {
            }
            
            // Armazena referências
            this.stars.set(selectId, {
                starWrapper,
                selectElement,
                suggestedValue,
                tooltip
            });
            
            // Configura eventos
            this.setupEvents(selectId);
            
            // Verifica estado inicial após um pequeno delay (aguarda Select2 inicializar)
            setTimeout(() => {
                const initialValue = selectElement.value;
                if (!initialValue || initialValue === '') {
                    this.hideStar(selectId);
                } else if (initialValue == suggestedValue) {
                    this.showStar(selectId);
                } else {
                    this.hideStar(selectId);
                }
            }, 300);
        });        
    }

    setupEvents(selectId) {
        const star = this.stars.get(selectId);
        if (!star) {
            return;
        }

        const { starWrapper, selectElement, suggestedValue } = star;

        // Monitora mudanças no select (suporta Select2 e select nativo)
        const hasSelect2 = typeof jQuery !== 'undefined' && jQuery(selectElement).data('select2');
        
        if (hasSelect2) {
            // Select2
            jQuery(selectElement).on('change', () => {
                this.handleSelectChange(selectId);
            });
            
            // Ajusta posicionamento quando Select2 abrir/fechar
            jQuery(selectElement).on('select2:open', () => {
                this.adjustStarPosition(selectId, true);
            });
            
            jQuery(selectElement).on('select2:close', () => {
                this.adjustStarPosition(selectId, false);
            });
            
            // Ajusta posição quando valor mudar (clear button pode aparecer/desaparecer)
            jQuery(selectElement).on('select2:select select2:unselect', () => {
                setTimeout(() => {
                    this.adjustStarPosition(selectId, false);
                }, 50);
            });
        } else {
            // Select nativo
            selectElement.addEventListener('change', () => {
                this.handleSelectChange(selectId);
            });
        }

        // Previne que cliques na estrela abram o select
        starWrapper.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            return false;
        });

        starWrapper.addEventListener('mousedown', (e) => {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            return false;
        });
        
        starWrapper.addEventListener('mouseenter', () => {
        });
        
        // Ajusta posicionamento inicial após Select2 ser inicializado
        setTimeout(() => {
            this.adjustStarPosition(selectId, false);
        }, 500);
        
        // Verificação adicional para garantir posicionamento correto
        setTimeout(() => {
            this.adjustStarPosition(selectId, false);
        }, 1000);
    }

    adjustStarPosition(selectId, isOpen) {
        const star = this.stars.get(selectId);
        if (!star) {
            return;
        }

        const { starWrapper, selectElement } = star;
        const select2Container = selectElement.parentElement?.querySelector('.select2-container');
        
        if (select2Container) {
            // Verifica se o Select2 tem clear button
            const hasClearButton = select2Container.querySelector('.select2-selection__clear');
            
            // Ajusta posição baseado na presença do clear button
            if (hasClearButton) {
                // Com clear button: Estrela + X + chevron
                starWrapper.style.right = '50px';
            } else {
                // Sem clear button: Estrela + chevron
                starWrapper.style.right = '30px';
            }
            
            // Ajusta z-index baseado no estado do dropdown
            if (isOpen) {
                starWrapper.style.zIndex = '1';
            } else {
                starWrapper.style.zIndex = '3';
            }
        }
    }

    handleSelectChange(selectId) {
        const star = this.stars.get(selectId);
        if (!star) {
            return;
        }

        const { starWrapper, selectElement, suggestedValue, tooltip } = star;
        let currentValue = selectElement.value;

        // Se não tem valor selecionado (placeholder), esconde a estrela
        if (!currentValue || currentValue === '') {
            this.hideStar(selectId);
            return;
        }

        // Normalização para comparação (especialmente para moedas)
        const normalize = (val) => {
            if (typeof val !== 'string') return val;
            // Remove R$, espaços e converte vírgula para ponto se parecer moeda
            return val.replace('R$', '').replace(/\s/g, '').replace(',', '.').trim();
        };

        const currentNorm = normalize(currentValue);
        const suggestedNorm = normalize(suggestedValue);

        // Se o valor é igual à sugestão, mostra a estrela
        if (currentNorm == suggestedNorm) {
            this.showStar(selectId);
        } else {
            this.hideStar(selectId);
        }
    }

    hideStar(selectId) {
        const star = this.stars.get(selectId);
        if (!star) return;

        const { starWrapper, tooltip } = star;
        
        starWrapper.style.display = 'none';
        
        // Esconde o tooltip se estiver visível
        if (tooltip) {
            tooltip.hide();
        }
    }

    showStar(selectId) {
        const star = this.stars.get(selectId);
        if (!star) return;

        const { starWrapper } = star;
        starWrapper.style.display = 'flex';
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

        // Verifica o estado inicial imediatamente
        this.handleSelectChange(selectId);
    }
}

// Instância global
window.suggestionStarManager = new SuggestionStarManager();
