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
                    // Clonar a resposta para poder ler sem consumir
                    const clonedResponse = response.clone();
                    
                    // Tentar parsear como JSON primeiro
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        try {
                            const data = await response.json();
                            if (data.error === 'SESSION_EXPIRED' || 
                                data.message?.includes('sessão expirou') || 
                                data.message?.includes('Page Expired') ||
                                data.message?.includes('token expirado')) {
                        this.handleSessionExpired(data.message || 'Sua sessão expirou por inatividade.');
                        return;
                            }
                        } catch (e) {
                            // Se falhar ao parsear JSON, tentar como texto
                        }
                    }
                    
                    // Se for HTML ou não conseguir parsear JSON, verificar conteúdo
                    try {
                        const text = await clonedResponse.text();
                        if (text.includes('Page Expired') || 
                            text.includes('419') || 
                            text.includes('sessão expirou') || 
                            text.includes('token expirado') ||
                            text.includes('CSRF token')) {
                            this.handleSessionExpired('Sua sessão ou token CSRF expirou por inatividade.');
                            return;
                        }
                    } catch (e) {
                        // Se falhar ao ler texto, ainda assim mostrar modal para erro 419
                        if (response.status === 419) {
                            this.handleSessionExpired('Sua sessão ou token CSRF expirou por inatividade.');
                            return;
                        }
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
                        if (xhr.responseText.includes('sessão expirou') || 
                            xhr.responseText.includes('Page Expired') || 
                            xhr.responseText.includes('419') ||
                            xhr.responseText.includes('token expirado') ||
                            xhr.responseText.includes('CSRF token')) {
                            this.handleSessionExpired('Sua sessão ou token CSRF expirou por inatividade.');
                        }
                    }
                }
            });
        }
    }

    checkSessionExpiredMessage() {
        // Verificar se há mensagem de sessão expirada na página atual
        const errorMessage = document.querySelector('.alert-danger, .alert-warning');
        if (errorMessage && (errorMessage.textContent.includes('sessão expirou') || 
                             errorMessage.textContent.includes('Page Expired') ||
                             errorMessage.textContent.includes('419'))) {
            this.showSessionExpiredModal(errorMessage.textContent);
        }
        
        // Verificar se a página contém erro 419 no título ou conteúdo
        if (document.title.includes('419') || document.title.includes('Page Expired')) {
            this.showSessionExpiredModal('Sua sessão expirou por inatividade.');
        }
        
        // Verificar se há texto "419" ou "Page Expired" no body
        const bodyText = document.body.textContent || document.body.innerText;
        if (bodyText.includes('419') && bodyText.includes('Page Expired')) {
            this.showSessionExpiredModal('Sua sessão expirou por inatividade.');
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
        // Evitar múltiplos modais
        const existingModal = document.getElementById('session-expired-modal');
        if (existingModal) {
            return;
        }

        // Criar modal de sessão expirada estilizado
        const modalHtml = `
            <div class="modal fade" id="session-expired-modal" tabindex="-1" 
                 data-bs-backdrop="static" data-bs-keyboard="false" 
                 aria-labelledby="sessionExpiredModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <!--begin::Header-->
                        <div class="modal-header border-0 pb-0 pt-10">
                            <button type="button" class="btn-close" data-bs-dismiss="modal" 
                                    aria-label="Close" style="display: none;"></button>
                        </div>
                        <!--end::Header-->
                        
                        <!--begin::Body-->
                        <div class="modal-body text-center py-10 px-10">
                            <!--begin::Ilustração SVG-->
                            <div class="mb-5">
                                <img src="/assets/media/auth/23.svg" alt="Sessão Expirada" 
                                     class="mw-300px mx-auto" style="max-width: 300px; height: auto;">
                            </div>
                            <!--end::Ilustração SVG-->
                            
                            <!--begin::Título-->
                            <h2 class="fw-bold mb-3" id="sessionExpiredModalLabel">Sessão Expirada</h2>
                            <!--end::Título-->
                            
                            <!--begin::Mensagem-->
                            <p class="text-gray-600 fs-5 mb-5">
                                ${message || 'Sua sessão expirou por inatividade. Por favor, faça login novamente para continuar.'}
                            </p>
                            <!--end::Mensagem-->
                        </div>
                        <!--end::Body-->
                        
                        <!--begin::Footer-->
                        <div class="modal-footer border-0 justify-content-center pb-10">
                            <button type="button" class="btn btn-primary btn-lg px-8" 
                                    id="session-expired-login-btn">
                                <i class="fa-solid fa-right-to-bracket me-2"></i>
                                Ir para Login
                            </button>
                        </div>
                        <!--end::Footer-->
                    </div>
                </div>
            </div>
        `;

        // Adicionar modal ao body
        document.body.insertAdjacentHTML('beforeend', modalHtml);

        // Configurar evento do botão
        const loginBtn = document.getElementById('session-expired-login-btn');
        if (loginBtn) {
            loginBtn.addEventListener('click', () => {
                window.location.href = this.getLoginUrl();
            });
        }

        // Mostrar modal usando Bootstrap
        const modalElement = document.getElementById('session-expired-modal');
        if (modalElement && typeof bootstrap !== 'undefined') {
            const modal = new bootstrap.Modal(modalElement, {
                backdrop: 'static',
                keyboard: false
            });
        modal.show();

            // Limpar modal quando for fechado
            modalElement.addEventListener('hidden.bs.modal', () => {
                modalElement.remove();
            });
        }
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
