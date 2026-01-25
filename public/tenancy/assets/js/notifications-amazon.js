/**
 * Amazon-style Notifications System
 * Inspired by Amazon's notification design
 */

(function() {
    'use strict';

    // SVG Icons
    const icons = {
        success: `<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
        </svg>`,

        error: `<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
        </svg>`,

        warning: `<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/>
        </svg>`,

        info: `<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
        </svg>`
    };

    const closeIcon = `<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
    </svg>`;

    // Notification titles
    const titles = {
        success: 'Sucesso',
        error: 'Erro',
        warning: 'Atenção',
        info: 'Informação'
    };

    // Create container if it doesn't exist
    function getContainer() {
        let container = document.getElementById('fl-notifications-container');

        if (!container) {
            container = document.createElement('div');
            container.id = 'fl-notifications-container';
            container.setAttribute('aria-live', 'polite');
            container.setAttribute('aria-atomic', 'true');
            document.body.appendChild(container);
        }

        return container;
    }

    // Create notification element
    function createNotification(type, message, title = null) {
        const notification = document.createElement('div');
        notification.className = `fl-amazon fl-${type}`;
        notification.setAttribute('role', type === 'error' ? 'alert' : 'status');
        notification.setAttribute('aria-live', type === 'error' ? 'assertive' : 'polite');
        notification.setAttribute('aria-atomic', 'true');

        const displayTitle = title || titles[type] || type.charAt(0).toUpperCase() + type.slice(1);

        notification.innerHTML = `
            <div class="fl-amazon-alert">
                <div class="fl-alert-content">
                    <div class="fl-icon-container">
                        ${icons[type] || icons.info}
                    </div>
                    <div class="fl-text-content">
                        <div class="fl-alert-title">${displayTitle}</div>
                        <div class="fl-alert-message">${message}</div>
                    </div>
                </div>
                <div class="fl-alert-actions">
                    <button class="fl-close" aria-label="Fechar notificação">
                        ${closeIcon}
                    </button>
                </div>
            </div>
        `;

        return notification;
    }

    // Show notification
    function show(type, message, title = null, duration = 5000) {
        const container = getContainer();
        const notification = createNotification(type, message, title);

        // Add to container
        container.appendChild(notification);

        // Close button handler
        const closeBtn = notification.querySelector('.fl-close');
        closeBtn.addEventListener('click', () => {
            removeNotification(notification);
        });

        // Auto remove after duration
        if (duration > 0) {
            setTimeout(() => {
                removeNotification(notification);
            }, duration);
        }

        return notification;
    }

    // Remove notification
    function removeNotification(notification) {
        if (!notification || !notification.parentNode) return;

        notification.classList.add('fl-removing');

        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }

    // Public API
    window.AmazonNotifications = {
        success: (message, title = null, duration = 5000) => show('success', message, title, duration),
        error: (message, title = null, duration = 5000) => show('error', message, title, duration),
        warning: (message, title = null, duration = 5000) => show('warning', message, title, duration),
        info: (message, title = null, duration = 5000) => show('info', message, title, duration)
    };

    // Alias simples
    window.notify = window.AmazonNotifications;

})();
