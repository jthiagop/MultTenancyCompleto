/**
 * AppToast - Sistema de Toasts Global
 * Baseado no Bootstrap Toast do Metronic
 * 
 * Uso:
 *   AppToast.show('success', 'Título', 'Mensagem');
 *   AppToast.loading('Processando...');
 *   AppToast.success('Concluído!', 'Mensagem opcional');
 *   AppToast.error('Erro!', 'Detalhes do erro');
 */
window.AppToast = (function() {
    'use strict';

    // Configurações de tipos
    const types = {
        success: { 
            iconClass: 'ki-duotone ki-check-circle text-success',
            iconHtml: '<span class="path1"></span><span class="path2"></span>'
        },
        info: { 
            iconClass: 'ki-duotone ki-information-2 text-primary',
            iconHtml: '<span class="path1"></span><span class="path2"></span><span class="path3"></span>'
        },
        warning: { 
            iconClass: 'ki-duotone ki-information-5 text-warning',
            iconHtml: '<span class="path1"></span><span class="path2"></span><span class="path3"></span>'
        },
        error: { 
            iconClass: 'ki-duotone ki-cross-circle text-danger',
            iconHtml: '<span class="path1"></span><span class="path2"></span>'
        },
        loading: { 
            iconClass: 'spinner-border spinner-border-sm text-primary',
            iconHtml: ''
        }
    };

    /**
     * Obtém ou cria o container de toasts
     */
    function getContainer() {
        let container = document.getElementById('kt_toast_stack_container');
        
        // Se não existir, criar dinamicamente
        if (!container) {
            container = document.createElement('div');
            container.id = 'kt_toast_stack_container';
            container.className = 'toast-container position-fixed top-0 end-0 p-3';
            container.style.zIndex = '1090';
            document.body.appendChild(container);
        }
        
        return container;
    }

    /**
     * Obtém o template do toast
     */
    function getTemplate() {
        const wrapper = document.getElementById('kt_toast_template_wrapper');
        if (wrapper) {
            return wrapper.querySelector('.toast');
        }
        
        // Se não existir, criar template básico
        const template = document.createElement('div');
        template.className = 'toast';
        template.setAttribute('role', 'alert');
        template.setAttribute('aria-live', 'assertive');
        template.setAttribute('aria-atomic', 'true');
        template.innerHTML = `
            <div class="toast-header">
                <i class="fs-2 me-3" data-toast-icon></i>
                <strong class="me-auto" data-toast-title>Sistema</strong>
                <small class="text-muted" data-toast-time>Agora</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Fechar"></button>
            </div>
            <div class="toast-body" data-toast-message></div>
        `;
        return template;
    }

    /**
     * Mostra um toast
     * @param {string} type - 'success', 'info', 'warning', 'error', 'loading'
     * @param {string} title - Título do toast
     * @param {string} message - Mensagem (pode conter HTML)
     * @param {object} options - Opções adicionais
     * @returns {HTMLElement} - Elemento do toast criado
     */
    function show(type, title, message, options = {}) {
        const container = getContainer();
        const template = getTemplate();
        
        if (!container || !template) {
            console.error('[AppToast] Container ou template não encontrado');
            return null;
        }

        // Configurações padrão
        const config = {
            autohide: options.autohide !== undefined ? options.autohide : (type !== 'loading'),
            delay: options.delay || (type === 'loading' ? 60000 : 5000),
            persistent: options.persistent || false
        };

        // Clona o template
        const toastEl = template.cloneNode(true);
        
        // Configura autohide
        toastEl.setAttribute('data-bs-autohide', config.autohide);
        toastEl.setAttribute('data-bs-delay', config.delay);
        
        // Configura ícone
        const typeConfig = types[type] || types.info;
        const iconEl = toastEl.querySelector('[data-toast-icon]');
        if (iconEl) {
            iconEl.className = `fs-2 me-3 ${typeConfig.iconClass}`;
            iconEl.innerHTML = typeConfig.iconHtml;
        }
        
        // Configura textos
        const titleEl = toastEl.querySelector('[data-toast-title]');
        if (titleEl) titleEl.textContent = title;
        
        const messageEl = toastEl.querySelector('[data-toast-message]');
        if (messageEl) messageEl.innerHTML = message || '';
        
        const timeEl = toastEl.querySelector('[data-toast-time]');
        if (timeEl) timeEl.textContent = 'Agora';
        
        // Se for persistent, esconde o botão de fechar
        if (config.persistent) {
            const closeBtn = toastEl.querySelector('.btn-close');
            if (closeBtn) closeBtn.style.display = 'none';
        }
        
        // Adiciona ao container
        container.appendChild(toastEl);
        
        // Inicializa e mostra o toast (Metronic já inicializa via .toast class)
        const toast = new bootstrap.Toast(toastEl);
        toast.show();
        
        // Guarda referência para poder fechar programaticamente
        toastEl._bsToast = toast;
        
        // Remove do DOM quando escondido (cleanup)
        toastEl.addEventListener('hidden.bs.toast', function() {
            toastEl.remove();
        });
        
        return toastEl;
    }

    /**
     * Fecha um toast programaticamente
     * @param {HTMLElement} toastEl - Elemento do toast
     */
    function close(toastEl) {
        if (toastEl && toastEl._bsToast) {
            toastEl._bsToast.hide();
        } else if (toastEl) {
            toastEl.remove();
        }
    }

    /**
     * Fecha todos os toasts
     */
    function closeAll() {
        const container = getContainer();
        if (container) {
            const toasts = container.querySelectorAll('.toast');
            toasts.forEach(toast => close(toast));
        }
    }

    // Atalhos convenientes
    function success(title, message, options) {
        return show('success', title, message, options);
    }

    function info(title, message, options) {
        return show('info', title, message, options);
    }

    function warning(title, message, options) {
        return show('warning', title, message, options);
    }

    function error(title, message, options) {
        return show('error', title, message, options);
    }

    function loading(title, message, options) {
        return show('loading', title, message || 'Aguarde...', { 
            autohide: false, 
            persistent: true,
            ...options 
        });
    }

    // API pública
    return {
        show,
        close,
        closeAll,
        success,
        info,
        warning,
        error,
        loading
    };
})();

console.log('[AppToast] Sistema de Toasts carregado');
