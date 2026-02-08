<!--begin::Notifications-->
<div class="app-navbar-item ms-1 ms-lg-3" id="notifications-wrapper">
    <!--begin::Menu- wrapper-->
    <div class="btn btn-icon btn-custom btn-icon-muted btn-active-light btn-active-color-primary w-35px h-35px w-md-40px h-md-40px position-relative"
        data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-attach="parent"
        data-kt-menu-placement="bottom-end" id="notifications-trigger">
        <!--begin::Svg Icon | path: icons/duotune/general/gen022.svg-->
        <i class="fa-solid fa-bell fs-3"></i>
        <!--end::Svg Icon-->
        <!--begin::Badge-->
        <span class="badge badge-circle badge-danger position-absolute top-0 end-0 d-none" id="notifications-badge" style="font-size: 10px; min-width: 16px; height: 16px;">0</span>
        <!--end::Badge-->
    </div>
    <!--begin::Menu-->
    <div class="menu menu-sub menu-sub-dropdown menu-column w-350px w-lg-375px" data-kt-menu="true" id="notifications-menu">
        <!--begin::Heading-->
        <div class="d-flex flex-column bgi-no-repeat border-bottom">
            <!--begin::Title-->
            <div class="d-flex justify-content-between align-items-center px-9 mt-7 mb-5">
                <h3 class="fw-semibold mb-0">Notificações
                    <span class="fs-8 opacity-75 ps-3" id="notifications-count-text">0 notificações</span>
                </h3>
                <button type="button" class="btn btn-sm btn-light-primary d-none" id="mark-all-read-btn">
                    <i class="fa-solid fa-check-double fs-7 me-1"></i> Marcar todas como lidas
                </button>
            </div>
            <!--end::Title-->
        </div>
        <!--end::Heading-->

        <!--begin::Items-->
        <div class="scroll-y mh-325px my-5 px-8" id="notifications-list">
            <!--begin::Loading-->
            <div class="d-flex justify-content-center py-5" id="notifications-loading">
                <span class="spinner-border spinner-border-sm text-primary" role="status"></span>
                <span class="ms-2 text-muted">Carregando...</span>
            </div>
            <!--end::Loading-->
            <!--begin::Empty-->
            <div class="text-center py-5 d-none" id="notifications-empty">
                <img src="{{ global_asset('assets/media/illustration/search_list.png') }}" alt="Sem notificações" class="mb-3" style="max-width: 120px;">
                <p class="text-muted mb-0">Nenhuma notificação</p>
            </div>
            <!--end::Empty-->
        </div>
        <!--end::Items-->
        <!--begin::View more-->
        <div class="py-1 text-center border-top">
            <a href="{{ route('notifications.page') }}" class="btn btn-color-gray-600 btn-active-color-primary" id="view-all-notifications">
                Ver Todas
                <!--begin::Svg Icon | path: icons/duotune/arrows/arr064.svg-->
                <span class="svg-icon svg-icon-5">
                    <i class="fa-solid fa-arrow-right"></i>
                </span>
                <!--end::Svg Icon-->
            </a>
        </div>
        <!--end::View more-->
    </div>
    <!--end::Menu-->
    <!--end::Menu wrapper-->
</div>
<!--end::Notifications-->

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const NotificationsManager = {
        // Elementos do DOM
        badge: document.getElementById('notifications-badge'),
        countText: document.getElementById('notifications-count-text'),
        list: document.getElementById('notifications-list'),
        loading: document.getElementById('notifications-loading'),
        empty: document.getElementById('notifications-empty'),
        markAllBtn: document.getElementById('mark-all-read-btn'),
        trigger: document.getElementById('notifications-trigger'),
        
        // Estado
        notifications: [],
        unreadCount: 0,
        isLoading: false,
        
        // Inicialização
        init() {
            this.loadNotifications();
            this.bindEvents();
            // Atualizar a cada 30 segundos
            setInterval(() => this.loadUnreadCount(), 30000);
        },
        
        // Eventos
        bindEvents() {
            // Marcar todas como lidas
            if (this.markAllBtn) {
                this.markAllBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    this.markAllAsRead();
                });
            }
            
            // Recarregar ao abrir o menu
            if (this.trigger) {
                this.trigger.addEventListener('click', () => {
                    this.loadNotifications();
                });
            }
        },
        
        // Carregar notificações
        async loadNotifications() {
            if (this.isLoading) return;
            this.isLoading = true;
            
            this.showLoading(true);
            
            try {
                const response = await fetch('/notifications', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (!response.ok) throw new Error('Erro ao carregar notificações');
                
                const data = await response.json();
                this.notifications = data.notifications || [];
                this.unreadCount = data.unread_count || 0;
                
                this.render();
                this.updateBadge();
            } catch (error) {
                console.error('Erro ao carregar notificações:', error);
                this.showEmpty();
            } finally {
                this.isLoading = false;
                this.showLoading(false);
            }
        },
        
        // Carregar apenas contagem de não lidas
        async loadUnreadCount() {
            try {
                const response = await fetch('/notifications/unread-count', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (!response.ok) return;
                
                const data = await response.json();
                this.unreadCount = data.count || 0;
                this.updateBadge();
            } catch (error) {
                console.error('Erro ao carregar contagem:', error);
            }
        },
        
        // Marcar como lida
        async markAsRead(id) {
            try {
                const response = await fetch(`/notifications/${id}/read`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    }
                });
                
                if (response.ok) {
                    // Atualizar estado local
                    const notification = this.notifications.find(n => n.id === id);
                    if (notification && !notification.read_at) {
                        notification.read_at = new Date().toISOString();
                        this.unreadCount = Math.max(0, this.unreadCount - 1);
                        this.updateBadge();
                        this.updateNotificationUI(id);
                    }
                }
            } catch (error) {
                console.error('Erro ao marcar como lida:', error);
            }
        },
        
        // Marcar todas como lidas
        async markAllAsRead() {
            try {
                const response = await fetch('/notifications/mark-all-read', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    }
                });
                
                if (response.ok) {
                    this.notifications.forEach(n => n.read_at = new Date().toISOString());
                    this.unreadCount = 0;
                    this.updateBadge();
                    this.render();
                }
            } catch (error) {
                console.error('Erro ao marcar todas como lidas:', error);
            }
        },
        
        // Renderizar notificações
        render() {
            if (this.notifications.length === 0) {
                this.showEmpty();
                return;
            }
            
            // Esconder empty e loading
            if (this.empty) this.empty.classList.add('d-none');
            if (this.loading) this.loading.classList.add('d-none');
            
            // Mostrar/ocultar botão marcar todas
            if (this.markAllBtn) {
                this.markAllBtn.classList.toggle('d-none', this.unreadCount === 0);
            }
            
            // Atualizar texto de contagem
            this.updateCountText();
            
            // Limpar lista (mantendo loading e empty)
            const items = this.list.querySelectorAll('.notification-item');
            items.forEach(item => item.remove());
            
            // Renderizar cada notificação
            this.notifications.forEach(notification => {
                const html = this.renderNotification(notification);
                this.list.insertAdjacentHTML('beforeend', html);
            });
            
            // Bind eventos de clique
            this.list.querySelectorAll('.notification-item').forEach(item => {
                item.addEventListener('click', (e) => {
                    const id = item.dataset.id;
                    const url = item.dataset.url;
                    
                    // Marcar como lida
                    this.markAsRead(id);
                    
                    // Navegar se houver URL
                    if (url && url !== '#' && url !== '') {
                        window.location.href = url;
                    }
                });
            });
        },
        
        // Renderizar uma notificação
        renderNotification(notification) {
            const data = notification.data || {};
            const icon = data.icon || 'ki-notification';
            const color = data.color || 'primary';
            const title = data.title || 'Notificação';
            const message = data.message || '';
            const url = data.action_url || '#';
            const isUnread = !notification.read_at;
            const timeAgo = this.timeAgo(notification.created_at);
            
            return `
                <div class="d-flex flex-stack py-4 notification-item ${isUnread ? 'bg-light-primary rounded px-3' : ''}" 
                     data-id="${notification.id}" 
                     data-url="${url}"
                     style="cursor: pointer;">
                    <!--begin::Section-->
                    <div class="d-flex align-items-center">
                        <!--begin::Symbol-->
                        <div class="symbol symbol-35px me-4">
                            <span class="symbol-label bg-light-${color}">
                                <i class="${icon} fs-2 text-${color}"></i>
                            </span>
                        </div>
                        <!--end::Symbol-->
                        <!--begin::Title-->
                        <div class="mb-0 me-2">
                            <span class="fs-6 text-gray-800 fw-bold ${isUnread ? 'text-primary' : ''}">${this.escapeHtml(title)}</span>
                            <div class="text-gray-400 fs-7">${this.escapeHtml(message)}</div>
                        </div>
                        <!--end::Title-->
                    </div>
                    <!--end::Section-->
                    <!--begin::Label-->
                    <div class="d-flex flex-column align-items-end">
                        <span class="badge badge-light fs-8">${timeAgo}</span>
                        ${isUnread ? '<span class="badge badge-circle badge-primary mt-1" style="width: 8px; height: 8px;"></span>' : ''}
                    </div>
                    <!--end::Label-->
                </div>
            `;
        },
        
        // Atualizar UI de uma notificação específica
        updateNotificationUI(id) {
            const item = this.list.querySelector(`[data-id="${id}"]`);
            if (item) {
                item.classList.remove('bg-light-primary');
                const badge = item.querySelector('.badge-primary.badge-circle');
                if (badge) badge.remove();
                const title = item.querySelector('.text-primary');
                if (title) title.classList.remove('text-primary');
            }
        },
        
        // Atualizar badge
        updateBadge() {
            if (this.badge) {
                if (this.unreadCount > 0) {
                    this.badge.textContent = this.unreadCount > 99 ? '99+' : this.unreadCount;
                    this.badge.classList.remove('d-none');
                } else {
                    this.badge.classList.add('d-none');
                }
            }
        },
        
        // Atualizar texto de contagem
        updateCountText() {
            if (this.countText) {
                const total = this.notifications.length;
                this.countText.textContent = `${total} notificação${total !== 1 ? 'es' : ''}`;
            }
        },
        
        // Mostrar loading
        showLoading(show) {
            if (this.loading) {
                this.loading.classList.toggle('d-none', !show);
            }
        },
        
        // Mostrar empty
        showEmpty() {
            if (this.empty) {
                this.empty.classList.remove('d-none');
            }
            if (this.loading) {
                this.loading.classList.add('d-none');
            }
            if (this.markAllBtn) {
                this.markAllBtn.classList.add('d-none');
            }
            this.updateCountText();
        },
        
        // Calcular tempo relativo
        timeAgo(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const seconds = Math.floor((now - date) / 1000);
            
            if (seconds < 60) return 'Agora';
            if (seconds < 3600) return `${Math.floor(seconds / 60)} min`;
            if (seconds < 86400) return `${Math.floor(seconds / 3600)}h`;
            if (seconds < 604800) return `${Math.floor(seconds / 86400)}d`;
            
            return date.toLocaleDateString('pt-BR', { day: '2-digit', month: 'short' });
        },
        
        // Escapar HTML
        escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    };
    
    // Inicializar
    NotificationsManager.init();
    
    // Expor globalmente para uso externo
    window.NotificationsManager = NotificationsManager;
});
</script>
@endpush
