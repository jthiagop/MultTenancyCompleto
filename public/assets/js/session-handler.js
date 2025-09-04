/**
 * Session Handler - Trata expiração de sessão de forma elegante
 */
class SessionHandler {
    constructor() {
        this.init();
    }

    init() {
        // Interceptar todas as requisições AJAX
        this.setupAjaxInterceptors();
        
        // Verificar se há mensagem de sessão expirada na página
        this.checkSessionExpiredMessage();
    }

    setupAjaxInterceptors() {
        // Interceptar requisições fetch
        const originalFetch = window.fetch;
        window.fetch = async (...args) => {
            try {
                const response = await originalFetch(...args);
                
                // Verificar se a resposta indica sessão expirada
                if (response.status === 419 || response.status === 401) {
                    const data = await response.json().catch(() => ({}));
                    
                    if (data.error === 'SESSION_EXPIRED' || data.message?.includes('sessão expirou')) {
                        this.handleSessionExpired(data.message || 'Sua sessão expirou por inatividade.');
                        return;
                    }
                }
                
                return response;
            } catch (error) {
                console.error('Erro na requisição:', error);
                throw error;
            }
        };

        // Interceptar requisições jQuery AJAX
        if (typeof $ !== 'undefined') {
            $(document).ajaxError((event, xhr, settings, thrownError) => {
                if (xhr.status === 419 || xhr.status === 401) {
                    try {
                        const data = JSON.parse(xhr.responseText);
                        if (data.error === 'SESSION_EXPIRED' || data.message?.includes('sessão expirou')) {
                            this.handleSessionExpired(data.message || 'Sua sessão expirou por inatividade.');
                        }
                    } catch (e) {
                        // Se não conseguir parsear JSON, verificar se é uma resposta de sessão expirada
                        if (xhr.responseText.includes('sessão expirou') || xhr.responseText.includes('Page Expired')) {
                            this.handleSessionExpired('Sua sessão expirou por inatividade.');
                        }
                    }
                }
            });
        }
    }

    checkSessionExpiredMessage() {
        // Verificar se há mensagem de sessão expirada na página atual
        const errorMessage = document.querySelector('.alert-danger, .alert-warning');
        if (errorMessage && errorMessage.textContent.includes('sessão expirou')) {
            this.showSessionExpiredModal(errorMessage.textContent);
        }
    }

    handleSessionExpired(message) {
        // Evitar múltiplas chamadas
        if (document.querySelector('#session-expired-modal')) {
            return;
        }

        this.showSessionExpiredModal(message);
    }

    showSessionExpiredModal(message) {
        // Criar modal de sessão expirada
        const modalHtml = `
            <div class="modal fade" id="session-expired-modal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-warning">
                            <h5 class="modal-title text-dark">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Sessão Expirada
                            </h5>
                        </div>
                        <div class="modal-body text-center">
                            <div class="mb-4">
                                <i class="fas fa-clock fa-3x text-warning mb-3"></i>
                                <p class="fs-5">${message}</p>
                                <p class="text-muted">Você será redirecionado para a página de login.</p>
                            </div>
                        </div>
                        <div class="modal-footer justify-content-center">
                            <button type="button" class="btn btn-primary" onclick="window.location.href='${this.getLoginUrl()}'">
                                <i class="fas fa-sign-in-alt me-2"></i>
                                Fazer Login
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Adicionar modal ao body
        document.body.insertAdjacentHTML('beforeend', modalHtml);

        // Mostrar modal
        const modal = new bootstrap.Modal(document.getElementById('session-expired-modal'));
        modal.show();

        // Redirecionar após 3 segundos
        setTimeout(() => {
            window.location.href = this.getLoginUrl();
        }, 3000);
    }

    getLoginUrl() {
        // Determinar URL de login baseada no contexto
        const currentPath = window.location.pathname;
        
        if (currentPath.startsWith('/app/')) {
            return '/app/login';
        } else {
            return '/login';
        }
    }
}

// Inicializar quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', () => {
    new SessionHandler();
});

// Também inicializar se o DOM já estiver pronto
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        new SessionHandler();
    });
} else {
    new SessionHandler();
}
