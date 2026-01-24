// For more details see: https://getbootstrap.com/docs/5.0/components/toasts/#usage

/**
 * Função global para exibir toasts dinamicamente
 * @param {Object} options - Opções do toast
 * @param {string} options.title - Título do toast
 * @param {string} options.message - Mensagem do toast
 * @param {string} options.icon - Classe do ícone (opcional, padrão: ki-abstract-39)
 * @param {string} options.iconColor - Cor do ícone (opcional, padrão: text-primary)
 * @param {string} options.time - Tempo exibido (opcional, padrão: 'Agora')
 * @param {number} options.delay - Delay em ms antes de fechar (opcional, padrão: 5000)
 * @param {boolean} options.autohide - Se deve fechar automaticamente (opcional, padrão: true)
 */
window.showToast = function(options) {
    // Validação de opções obrigatórias
    if (!options || !options.message) {
        console.error('showToast: message é obrigatório');
        return;
    }

    // Valores padrão
    const config = {
        title: options.title || 'Notificação',
        message: options.message,
        icon: options.icon || 'ki-duotone ki-abstract-39',
        iconColor: options.iconColor || 'text-primary',
        time: options.time || 'Agora',
        delay: options.delay || 5000,
        autohide: options.autohide !== false
    };

    // Cria ou obtém o container de toasts
    let toastContainer = document.getElementById('kt_toast_container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'kt_toast_container';
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '9999';
        document.body.appendChild(toastContainer);
    }

    // Cria o elemento do toast
    const toastId = 'toast_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    const toastEl = document.createElement('div');
    toastEl.id = toastId;
    toastEl.className = 'toast';
    toastEl.setAttribute('role', 'alert');
    toastEl.setAttribute('aria-live', 'assertive');
    toastEl.setAttribute('aria-atomic', 'true');

    // Configura autohide
    if (!config.autohide) {
        toastEl.setAttribute('data-bs-autohide', 'false');
    } else {
        toastEl.setAttribute('data-bs-delay', config.delay);
    }

    // HTML do toast com o visual especificado
    toastEl.innerHTML = `
        <div class="toast-header">
            <i class="${config.icon} fs-2 ${config.iconColor} me-3">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
            <strong class="me-auto">${config.title}</strong>
            <small>${config.time}</small>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            ${config.message}
        </div>
    `;

    // Adiciona ao container
    toastContainer.appendChild(toastEl);

    // Inicializa e exibe o toast
    const toast = new bootstrap.Toast(toastEl, {
        autohide: config.autohide,
        delay: config.delay
    });

    toast.show();

    // Remove o elemento do DOM após o toast ser escondido
    toastEl.addEventListener('hidden.bs.toast', function() {
        if (toastEl.parentNode) {
            toastEl.parentNode.removeChild(toastEl);
        }
    });

    return toast;
};

/**
 * Função específica para exibir toast de aviso sobre horários de missa não cadastrados
 * @param {Object} options - Opções do toast (opcional)
 * @param {string} options.cadastrarUrl - URL da rota para cadastrar horários (obrigatório se não usar data-attribute)
 * @param {number} options.delay - Delay em ms antes de fechar (opcional, padrão: 8000)
 * @param {string} options.icon - Classe do ícone (opcional, padrão: ki-information-5)
 */
window.showHorariosMissasToast = function(options) {
    // Valores padrão
    const config = {
        cadastrarUrl: options?.cadastrarUrl || null,
        delay: options?.delay || 8000,
        icon: options?.icon || 'bi bi-exclamation-triangle'
    };

    // Tenta obter a URL da rota de um data-attribute no body ou usa a passada como parâmetro
    let cadastrarUrl = config.cadastrarUrl;
    if (!cadastrarUrl) {
        const routeData = document.body.getAttribute('data-route-company-edit-horarios');
        if (routeData) {
            cadastrarUrl = routeData;
        } else {
            // Fallback: tenta construir a URL baseada no padrão Laravel
            const baseUrl = window.location.origin;
            cadastrarUrl = baseUrl + '/company/edit?tab=horario-missas';
        }
    }

    // Mensagem com link
    const message = `Não existem horários de missa cadastrados. <a href="${cadastrarUrl}" class="text-primary fw-bold">Cadastrar Horários de Missa?</a>`;

    // Exibe o toast usando a função principal
    if (typeof window.showToast === 'function') {
        return window.showToast({
            title: 'Atenção',
            message: message,
            icon: config.icon,
            iconColor: 'text-warning',
            time: 'Agora',
            delay: config.delay,
            autohide: true
        });
    } else {
        console.warn('showToast não está disponível. Certifique-se de que toasts.js está carregado.');
        return null;
    }
};

// Função para converter mensagens de sessão em toasts
window.convertSessionMessagesToToast = function() {
    // Verifica se há mensagens de sessão no DOM (via Blade)
    const successMessage = document.querySelector('[data-session-success]');
    const errorMessage = document.querySelector('[data-session-error]');
    const warningMessage = document.querySelector('[data-session-warning]');
    const infoMessage = document.querySelector('[data-session-info]');

    if (successMessage && typeof window.showToast === 'function') {
        const message = successMessage.getAttribute('data-session-success');
        window.showToast({
            title: 'Sucesso',
            message: message,
            icon: 'ki-duotone ki-check-circle',
            iconColor: 'text-success',
            time: 'Agora',
            delay: 5000,
            autohide: true
        });
        successMessage.remove();
    }

    if (errorMessage && typeof window.showToast === 'function') {
        const message = errorMessage.getAttribute('data-session-error');
        window.showToast({
            title: 'Erro',
            message: message,
            icon: 'bi bi-x-circle',
            iconColor: 'text-danger',
            time: 'Agora',
            delay: 7000,
            autohide: true
        });
        errorMessage.remove();
    }

    if (warningMessage && typeof window.showToast === 'function') {
        const message = warningMessage.getAttribute('data-session-warning');
        window.showToast({
            title: 'Atenção',
            message: message,
            icon: 'bi bi-exclamation-triangle',
            iconColor: 'text-warning',
            time: 'Agora',
            delay: 6000,
            autohide: true
        });
        warningMessage.remove();
    }

    if (infoMessage && typeof window.showToast === 'function') {
        const message = infoMessage.getAttribute('data-session-info');
        window.showToast({
            title: 'Informação',
            message: message,
            icon: 'bi bi-info-circle',
            iconColor: 'text-info',
            time: 'Agora',
            delay: 5000,
            autohide: true
        });
        infoMessage.remove();
    }
};

// Inicialização de toasts existentes no DOM (mantém compatibilidade)
window.addEventListener('DOMContentLoaded', event => {
    const toastBasicEl = document.getElementById('toastBasic');
    const toastNoAutohideEl = document.getElementById('toastNoAutohide');

    if (toastBasicEl) {
        const toastBasic = new bootstrap.Toast(toastBasicEl);
        const toastBasicTrigger = document.getElementById('toastBasicTrigger');
        if (toastBasicTrigger) {
            toastBasicTrigger.addEventListener('click', event => {
                toastBasic.show();
            });
        }
    }

    if (toastNoAutohideEl) {
        const toastNoAutohide = new bootstrap.Toast(toastNoAutohideEl);
        const toastNoAutohideTrigger = document.getElementById('toastNoAutohideTrigger');
        if (toastNoAutohideTrigger) {
            toastNoAutohideTrigger.addEventListener('click', event => {
                toastNoAutohide.show();
            });
        }
    }

    // Converte mensagens de sessão em toasts
    window.convertSessionMessagesToToast();
});
