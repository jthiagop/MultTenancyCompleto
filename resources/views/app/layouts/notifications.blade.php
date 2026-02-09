{{-- ============================================================= --}}
{{-- SISTEMA DE NOTIFICAÇÕES — Alpine.js + Drawer com dados reais --}}
{{-- ============================================================= --}}
<style>[x-cloak] { display: none !important; }</style>

{{-- ======================== DROPDOWN ======================== --}}
<div class="app-navbar-item ms-1 ms-lg-3"
     x-data="notificationsDropdown()"
     @notifications-updated.window="loadNotifications()">

    {{-- Trigger --}}
    <div class="btn btn-icon btn-custom btn-icon-muted btn-active-light btn-active-color-primary w-35px h-35px w-md-40px h-md-40px position-relative"
         data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
         data-kt-menu-attach="parent"
         data-kt-menu-placement="bottom-end"
         x-ref="trigger"
         @click="loadNotifications()">
        <i class="fa-solid fa-bell fs-3"></i>
        {{-- Badge --}}
        <span class="position-absolute top-20 start-100 translate-middle badge badge-circle badge-danger badge-sm"
              :class="{ 'd-none': !(unreadCount > 0) }"
              x-text="unreadCount > 99 ? '99+' : unreadCount"
              x-transition></span>
    </div>

    {{-- Menu Dropdown --}}
    <div class="menu menu-sub menu-sub-dropdown menu-column w-350px w-lg-375px"
         data-kt-menu="true"
         id="notifications-menu">

        {{-- Header --}}
        <div class="d-flex flex-column bgi-no-repeat border-bottom">
            <div class="d-flex justify-content-between align-items-center px-9 mt-5 mb-5">
                <h4 class="fw-semibold mb-0">Notificações
                    <span class="fs-8 opacity-75 ps-3"
                          x-text="notifications.length + ' notificação' + (notifications.length !== 1 ? 'es' : '')"></span>
                </h4>
                {{-- Options menu (⋯) --}}
                <div class="position-relative" :class="{ 'd-none': !(unreadCount > 0) }">
                    <button type="button"
                            class="btn btn-sm btn-icon btn-active-light-primary"
                            data-kt-menu-trigger="click"
                            data-kt-menu-placement="bottom-end">
                        <i class="fa-solid fa-ellipsis fs-4 text-gray-500"></i>
                    </button>
                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px py-3"
                         data-kt-menu="true">
                        <div class="menu-item px-3">
                            <a href="javascript:void(0)" class="menu-link px-3" @click.prevent.stop="markAllAsRead()">
                                <span class="menu-icon"><i class="fa-solid fa-check-double text-primary"></i></span>
                                <span class="menu-title">Marcar todas como lidas</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Lista de notificações --}}
        <div class="scroll-y my-5 px-4" style="max-height: 350px;">
            {{-- Loading --}}
            <div class="d-flex justify-content-center my-5 px-8"
                 :class="{ 'd-none': !isLoading }">
                <span class="spinner-border spinner-border-sm text-primary" role="status"></span>
                <span class="ms-2 text-muted">Carregando...</span>
            </div>

            {{-- Empty --}}
            <div class="text-center py-5"
                 :class="{ 'd-none': !(!isLoading && notifications.length === 0) }">
                <img src="{{ global_asset('tenancy/assets/media/illustration/search_list.png') }}"
                     alt="Sem notificações" class="mb-3" style="max-width: 120px;">
                <p class="text-muted mb-0">Nenhuma notificação</p>
            </div>

            {{-- Items --}}
            <template x-for="n in notifications" :key="n.id">
                <div class="d-flex align-items-start py-3 px-2 rounded notification-item"
                     :class="{ 'opacity-65': n.read_at }"
                     style="cursor: pointer; transition: background 0.2s;"
                     @mouseover="$el.style.background='var(--bs-gray-100)'"
                     @mouseout="$el.style.background='transparent'"
                     @click="handleClick(n)">

                    {{-- Icon --}}
                    <div class="position-relative me-3 flex-shrink-0">
                        <div class="d-flex align-items-center justify-content-center"
                             style="width: 40px; height: 40px;">
                            <i :class="getIcon(n).cls + ' fs-1 text-' + getIcon(n).color"></i>
                        </div>
                        <div class="position-absolute" style="top: -2px; right: -2px;"
                             :class="{ 'd-none': !getStatusBadge(n) }" x-html="getStatusBadge(n)"></div>
                    </div>

                    {{-- Content --}}
                    <div class="flex-grow-1 me-2 overflow-hidden">
                        <div class="fw-bold text-gray-900 text-truncate" style="font-size: 13px;"
                             x-text="n.title"></div>

                        {{-- Meta info --}}
                        <div class="text-muted mt-1" style="font-size: 11px;"
                             :class="{ 'd-none': !getMetaInfo(n) }"
                             x-text="getMetaInfo(n)"></div>

                        <div class="text-muted mt-1" style="font-size: 11px;"
                             x-text="n.created_at"></div>

                        {{-- Barra de expiração --}}
                        <template x-if="n.expires_percent !== null && n.expires_percent !== undefined">
                            <div class="progress mt-2" style="height: 3px; border-radius: 2px;">
                                <div class="progress-bar"
                                     :class="n.expires_percent > 60 ? 'bg-success' : n.expires_percent > 30 ? 'bg-warning' : 'bg-danger'"
                                     :style="'width:' + n.expires_percent + '%; transition: width 0.3s;'"></div>
                            </div>
                        </template>
                    </div>

                    {{-- Unread dot --}}
                    <div class="flex-shrink-0 align-self-center ms-1" :class="{ 'd-none': n.read_at }">
                        <span class="d-inline-block rounded-circle bg-primary"
                              style="width: 10px; height: 10px;"></span>
                    </div>
                </div>
            </template>
        </div>

        {{-- Footer --}}
        <div class="py-1 text-center border-top">
            <a href="javascript:void(0)" class="btn btn-active-color-primary"
               data-kt-menu-dismiss="true"
               @click="openDrawer()">
                Ver Todas
                <span class="svg-icon svg-icon-5"><i class="fa-solid fa-arrow-right"></i></span>
            </a>
        </div>
    </div>
</div>

{{-- ======================== DRAWER ======================== --}}
<div id="kt_notifications_drawer"
     class="bg-body"
     data-kt-drawer="true"
     data-kt-drawer-name="notifications-drawer"
     data-kt-drawer-activate="true"
     data-kt-drawer-overlay="true"
     data-kt-drawer-width="{default:'300px', 'lg': '500px'}"
     data-kt-drawer-direction="end"
     data-kt-drawer-toggle="#kt_notifications_drawer_toggle"
     data-kt-drawer-close="#kt_notifications_drawer_close"
     x-data="notificationsDrawer()"
     @open-notifications-drawer.window="open()"
     x-effect="console.log('[Notif:Drawer] x-effect — isLoading=' + isLoading + ', isLoadingMore=' + isLoadingMore + ', hasMore=' + hasMore + ', count=' + notifications.length)">

    <div class="card w-100 rounded-0">
        {{-- Header --}}
        <div class="card-header " id="kt_notifications_drawer_header">
            <h3 class="card-title fw-bold">
                Notificações
            </h3>
            <div class="card-toolbar">
                <button type="button"
                        class="btn btn-sm btn-icon btn-active-light-primary me-n5"
                        id="kt_notifications_drawer_close">
                    <i class="fa-solid fa-xmark fs-2"></i>
                </button>
            </div>
        </div>

        {{-- Filtros --}}
        <div class="border-bottom px-7 py-4">
            <div class="d-flex align-items-center gap-2">
                <template x-for="f in filters" :key="f.key">
                    <button class="btn btn-sm"
                            :class="activeFilter === f.key ? 'btn-primary' : 'btn-light'"
                            @click="setFilter(f.key)"
                            x-text="f.label + (f.key === 'unread' ? ' (' + unreadCount + ')' : '')">
                    </button>
                </template>

                {{-- Limpar lidas --}}
                <button class="btn btn-sm btn-light-danger ms-auto"
                        :class="{ 'd-none': !(activeFilter === 'read' && notifications.length > 0) }"
                        @click="clearRead()">
                    <i class="fa-solid fa-trash-can me-1"></i> Limpar lidas
                </button>
            </div>
        </div>

        {{-- Lista com scroll infinito --}}
        <div class="card-body position-relative flex-grow-1 overflow-hidden p-0">
            <div class="scroll-y h-100 px-7 py-5"
                 x-ref="scrollContainer"
                 @scroll="handleScroll($event)">

                {{-- Loading inicial --}}
                <div class="d-flex justify-content-center my-10"
                     :class="{ 'd-none': !(isLoading && notifications.length === 0) }">
                    <span class="spinner-border text-primary" role="status"></span>
                </div>

                {{-- Empty --}}
                <div class="d-flex flex-column align-items-center justify-content-center py-10"
                     :class="{ 'd-none': !(!isLoading && notifications.length === 0) }">
                    <img src="{{ global_asset('tenancy/assets/media/illustration/search_list.png') }}"
                         alt="Sem notificações" class="mb-4" style="max-width: 160px; opacity: 0.7;">
                    <p class="text-muted mb-0 fs-6" x-text="emptyMessage()"></p>
                </div>

                {{-- Items --}}
                <template x-for="n in notifications" :key="n.id">
                    <div class="d-flex align-items-start py-4 px-3 rounded mb-2 notification-drawer-item"
                         :class="{ 'bg-light-primary': !n.read_at }"
                         style="cursor: pointer; transition: all 0.2s;"
                         @mouseover="$el.style.background = n.read_at ? 'var(--bs-gray-100)' : ''"
                         @mouseout="$el.style.background = n.read_at ? 'transparent' : ''"
                         @click="handleClick(n)">

                        {{-- Icon --}}
                        <div class="position-relative me-4 flex-shrink-0">
                            <div class="d-flex align-items-center justify-content-center rounded-circle"
                                 :class="'bg-light-' + getIcon(n).color"
                                 style="width: 48px; height: 48px;">
                                <i :class="getIcon(n).cls + ' fs-2 text-' + getIcon(n).color"></i>
                            </div>
                            <div class="position-absolute" style="top: -2px; right: -2px;"
                                 :class="{ 'd-none': !getStatusBadge(n) }" x-html="getStatusBadge(n)"></div>
                        </div>

                        {{-- Content --}}
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <div class="fw-bold text-gray-900 text-truncate me-3" style="font-size: 13px;"
                                     x-text="n.title"></div>
                                <span class="text-muted flex-shrink-0" style="font-size: 11px;"
                                      x-text="n.created_at"></span>
                            </div>
                            <div class="text-muted" style="font-size: 12px;"
                                 x-text="n.message" :class="{ 'd-none': !n.message }"></div>

                            {{-- Meta info --}}
                            <div class="d-flex align-items-center gap-3 mt-2"
                                 :class="{ 'd-none': !getMetaInfo(n) }">
                                <span class="text-muted" style="font-size: 11px;"
                                      x-text="getMetaInfo(n)"></span>
                            </div>

                            {{-- Barra de expiração --}}
                            <template x-if="n.expires_percent !== null && n.expires_percent !== undefined">
                                <div class="progress mt-2" style="height: 3px; border-radius: 2px;">
                                    <div class="progress-bar"
                                         :class="n.expires_percent > 60 ? 'bg-success' : n.expires_percent > 30 ? 'bg-warning' : 'bg-danger'"
                                         :style="'width:' + n.expires_percent + '%; transition: width 0.3s;'"></div>
                                </div>
                            </template>
                        </div>

                        {{-- Unread dot --}}
                        <div class="flex-shrink-0 align-self-center ms-3" :class="{ 'd-none': n.read_at }">
                            <span class="d-inline-block rounded-circle bg-primary"
                                  style="width: 10px; height: 10px;"></span>
                        </div>
                    </div>
                </template>

                {{-- Loading more (scroll infinito) --}}
                <div class="d-flex justify-content-center py-4"
                     :class="{ 'd-none': !isLoadingMore }">
                    <span class="spinner-border spinner-border-sm text-primary" role="status"></span>
                    <span class="ms-2 text-muted">Carregando mais...</span>
                </div>

                {{-- Fim da lista --}}
                <div class="text-center py-3"
                     :class="{ 'd-none': !(!hasMore && notifications.length > 0) }">
                    <span class="text-muted" style="font-size: 12px;">Todas as notificações foram carregadas</span>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="card-footer border-top py-4 px-7">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <button class="btn btn-sm btn-light fw-semibold flex-grow-1 me-2"
                        :class="{ 'disabled opacity-50': notifications.length === 0 }"
                        @click="archiveAll()">
                    <i class="fa-solid fa-box-archive me-1 text-gray-600"></i> Arquivar tudo
                </button>
                <button class="btn btn-sm btn-light fw-semibold flex-grow-1 ms-2"
                        :class="{ 'disabled opacity-50': unreadCount === 0 }"
                        @click="markAllAsRead()">
                    <i class="fa-solid fa-check-double me-1 text-gray-600"></i> Marque tudo como lido
                </button>
            </div>
        </div>
    </div>
</div>


{{-- Script INLINE (não @push) — precisa ser definido ANTES do Alpine.start() --}}
{{-- Como o @push('scripts') só roda DEPOIS do @vite (que chama Alpine.start()), --}}
{{-- definimos as funções aqui inline para que estejam disponíveis quando Alpine --}}
{{-- inicializar os componentes x-data no DOM acima. --}}
<script>
/**
 * Helper compartilhado — ícone e badge por tipo
 */
function notificationHelpers() {
    return {
        getIcon(n) {
            const map = {
                relatorio_gerado: { cls: 'fa-solid fa-file-pdf', color: 'danger' },
                relatorio_erro:   { cls: 'fa-solid fa-file-pdf', color: 'danger' },
                conta_vencendo:   { cls: 'fa-solid fa-calendar-days', color: 'warning' },
                aviso:            { cls: 'fa-solid fa-circle-info', color: 'info' },
                aviso_sistema:    { cls: 'fa-solid fa-circle-info', color: 'info' },
            };
            return map[n.tipo] || { cls: n.icon || 'fa-solid fa-bell', color: n.color || 'primary' };
        },

        getStatusBadge(n) {
            const map = {
                relatorio_gerado: '<i class="fa-solid fa-circle-check text-success" style="font-size: 10px;"></i>',
                relatorio_erro:   '<i class="fa-solid fa-circle-xmark text-danger" style="font-size: 10px;"></i>',
                conta_vencendo:   '<i class="fa-solid fa-clock text-warning" style="font-size: 10px;"></i>',
            };
            return map[n.tipo] || '';
        },

        getMetaInfo(n) {
            const parts = [];
            if (n.file_type) parts.push(n.file_type);
            if (n.file_size) parts.push(n.file_size);
            if (n.expires_in) parts.push(n.expires_in);
            return parts.length > 0 ? parts.join(' · ') : '';
        },

        csrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.content || '';
        },

        async apiFetch(url, options = {}) {
            const headers = {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            };
            // Só adicionar CSRF e Content-Type em requests que modificam dados
            if (options.method && options.method !== 'GET') {
                headers['X-CSRF-TOKEN'] = this.csrfToken();
                headers['Content-Type'] = 'application/json';
            }
            const response = await fetch(url, { ...options, headers: { ...headers, ...(options.headers || {}) } });
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            return response.json();
        },

        handleClick(n) {
            this.markAsRead(n);
            if (n.action_url && n.action_url !== '#') {
                if (n.target === '_blank') {
                    window.open(n.action_url, '_blank');
                } else {
                    window.location.href = n.action_url;
                }
            }
        },

        async markAsRead(n) {
            if (n.read_at) return;
            try {
                await this.apiFetch(`/notifications/${n.id}/read`, { method: 'POST' });
                n.read_at = new Date().toISOString();
                this.unreadCount = Math.max(0, this.unreadCount - 1);
            } catch (e) {
                console.error('Erro ao marcar como lida:', e);
            }
        },
    };
}

/**
 * DROPDOWN — componente Alpine.js
 */
function notificationsDropdown() {
    return {
        ...notificationHelpers(),

        notifications: [],
        unreadCount: 0,
        isLoading: false,
        _pollInterval: null,
        _autoCloseTimer: null,
        _initialized: false,

        init() {
            // Guard contra init() duplicado (Alpine 3.x chama automaticamente)
            if (this._initialized) return;
            this._initialized = true;

            console.log('[Notif:Dropdown] init() chamado');

            this.$nextTick(() => {
                this.moveDrawerToBody();
                this.loadNotifications();
            });

            // Polling a cada 30s, pausa quando a aba está oculta
            this._pollInterval = setInterval(() => {
                if (!document.hidden) this.loadUnreadCount();
            }, 30000);
        },

        moveDrawerToBody() {
            const drawer = document.getElementById('kt_notifications_drawer');
            if (drawer && drawer.parentElement !== document.body) {
                document.body.appendChild(drawer);
                console.log('[Notif:Dropdown] Drawer movido para body (sem initTree)');
            }
        },

        async loadNotifications() {
            if (this.isLoading) return;
            this.isLoading = true;
            console.log('[Notif:Dropdown] loadNotifications() chamado');
            try {
                const data = await this.apiFetch('/notifications');
                console.log('[Notif:Dropdown] Resposta:', JSON.stringify(data).substring(0, 200));
                this.notifications = data.notifications || [];
                this.unreadCount = data.unread_count || 0;
                console.log(`[Notif:Dropdown] ${this.notifications.length} notificações, ${this.unreadCount} não lidas`);
            } catch (e) {
                console.error('[Notif:Dropdown] Erro ao carregar notificações:', e);
                this.notifications = [];
            } finally {
                this.isLoading = false;
            }
        },

        async loadUnreadCount() {
            try {
                const data = await this.apiFetch('/notifications/unread-count');
                const newCount = data.unread_count || 0;

                if (newCount > this.unreadCount) {
                    this.unreadCount = newCount;
                    await this.loadNotifications();
                    this.openDropdown();
                } else {
                    this.unreadCount = newCount;
                }
            } catch (e) {
                console.error('Erro ao carregar contagem:', e);
            }
        },

        openDropdown() {
            const menu = document.getElementById('notifications-menu');
            if (!menu || menu.classList.contains('show')) return;

            const inst = KTMenu.getInstance(menu);
            if (inst) {
                inst.show(this.$refs.trigger);
            } else {
                menu.classList.add('show');
                menu.style.position = 'absolute';
            }

            // Auto-close 5s
            this._autoCloseTimer = setTimeout(() => this.closeDropdown(), 5000);
            menu.addEventListener('mouseenter', () => {
                if (this._autoCloseTimer) clearTimeout(this._autoCloseTimer);
            }, { once: true });
        },

        closeDropdown() {
            const menu = document.getElementById('notifications-menu');
            if (!menu || !menu.classList.contains('show')) return;
            const inst = KTMenu.getInstance(menu);
            inst ? inst.hide(this.$refs.trigger) : menu.classList.remove('show');
        },

        async markAllAsRead() {
            try {
                await this.apiFetch('/notifications/mark-all-read', { method: 'POST' });
                this.notifications.forEach(n => n.read_at = new Date().toISOString());
                this.unreadCount = 0;
            } catch (e) {
                console.error('Erro ao marcar todas como lidas:', e);
            }
        },

        openDrawer() {
            this.closeDropdown();
            this.$dispatch('open-notifications-drawer');
        },
    };
}

/**
 * DRAWER — componente Alpine.js com scroll infinito
 */
function notificationsDrawer() {
    return {
        ...notificationHelpers(),

        notifications: [],
        unreadCount: 0,
        isLoading: false,
        isLoadingMore: false,
        hasMore: true,
        activeFilter: 'all',
        pagination: { current_page: 0, last_page: 1, total: 0, has_more: true },
        filters: [
            { key: 'all',    label: 'Todas' },
            { key: 'unread', label: 'Não lidas' },
            { key: 'read',   label: 'Lidas' },
        ],
        _drawerInstance: null,
        _opening: false,

        open() {
            // Guard contra open() duplicado (Alpine.initTree criava instância dupla)
            if (this._opening) {
                console.log('[Notif:Drawer] open() ignorado — já em execução');
                return;
            }
            this._opening = true;
            console.log('[Notif:Drawer] open() chamado');

            this.notifications = [];
            this.pagination.current_page = 0;
            this.hasMore = true;
            this.isLoading = false;
            this.isLoadingMore = false;
            this.activeFilter = 'all';
            this.loadPage(1);

            // Abrir o drawer via KTDrawer
            this.$nextTick(() => {
                const el = document.getElementById('kt_notifications_drawer');
                if (el) {
                    this._drawerInstance = KTDrawer.getInstance(el);
                    if (this._drawerInstance) {
                        this._drawerInstance.show();
                        console.log('[Notif:Drawer] KTDrawer.show() OK');
                    } else {
                        console.warn('[Notif:Drawer] KTDrawer.getInstance() retornou null');
                    }
                }
                // Liberar guard após 500ms
                setTimeout(() => { this._opening = false; }, 500);
            });
        },

        async loadPage(page) {
            console.log(`[Notif:Drawer] loadPage(${page}) — filter=${this.activeFilter}`);

            if (page === 1) {
                this.isLoading = true;
                this.isLoadingMore = false;
            } else {
                this.isLoadingMore = true;
            }

            try {
                const url = `/notifications/all?filter=${this.activeFilter}&page=${page}`;
                console.log(`[Notif:Drawer] Fetching: ${url}`);
                const data = await this.apiFetch(url);
                console.log('[Notif:Drawer] Resposta da API:', JSON.stringify(data).substring(0, 300));

                const items = data.notifications || [];
                console.log(`[Notif:Drawer] ${items.length} items recebidos`);

                if (page === 1) {
                    this.notifications = items;
                } else {
                    this.notifications = [...this.notifications, ...items];
                }

                this.unreadCount = data.unread_count || 0;
                this.pagination = data.pagination || { current_page: page, last_page: 1, total: items.length, has_more: false };
                this.hasMore = this.pagination.has_more || false;

                console.log(`[Notif:Drawer] Estado final — isLoading=${this.isLoading}, isLoadingMore=${this.isLoadingMore}, hasMore=${this.hasMore}, total=${this.notifications.length}`);
            } catch (e) {
                console.error('[Notif:Drawer] Erro ao carregar notificações:', e);
                if (page === 1) this.notifications = [];
                this.hasMore = false;
            } finally {
                this.isLoading = false;
                this.isLoadingMore = false;
                console.log(`[Notif:Drawer] Finally — isLoading=${this.isLoading}, isLoadingMore=${this.isLoadingMore}`);
            }
        },

        setFilter(filter) {
            if (this.activeFilter === filter) return;
            this.activeFilter = filter;
            this.notifications = [];
            this.pagination.current_page = 0;
            this.hasMore = true;
            this.loadPage(1);

            // Reset scroll
            if (this.$refs.scrollContainer) {
                this.$refs.scrollContainer.scrollTop = 0;
            }
        },

        handleScroll(event) {
            const el = event.target;
            const threshold = 100;
            const atBottom = el.scrollHeight - el.scrollTop - el.clientHeight < threshold;

            if (atBottom && this.hasMore && !this.isLoadingMore && !this.isLoading) {
                this.loadPage(this.pagination.current_page + 1);
            }
        },

        async clearRead() {
            if (!confirm('Remover todas as notificações lidas?')) return;
            try {
                await this.apiFetch('/notifications/clear/read', { method: 'DELETE' });
                this.notifications = this.notifications.filter(n => !n.read_at);
                this.pagination.total = this.notifications.length;
                window.dispatchEvent(new CustomEvent('notifications-updated'));
            } catch (e) {
                console.error('Erro ao limpar lidas:', e);
            }
        },

        async markAllAsRead() {
            if (this.unreadCount === 0) return;
            try {
                await this.apiFetch('/notifications/mark-all-read', { method: 'POST' });
                this.notifications.forEach(n => n.read_at = n.read_at || new Date().toISOString());
                this.unreadCount = 0;
                window.dispatchEvent(new CustomEvent('notifications-updated'));
            } catch (e) {
                console.error('Erro ao marcar todas como lidas:', e);
            }
        },

        async archiveAll() {
            if (this.notifications.length === 0) return;
            if (!confirm('Arquivar todas as notificações? Isso marcará todas como lidas e removerá as lidas.')) return;
            try {
                // 1. Marcar todas como lidas
                await this.apiFetch('/notifications/mark-all-read', { method: 'POST' });
                // 2. Remover todas as lidas
                await this.apiFetch('/notifications/clear/read', { method: 'DELETE' });
                // 3. Limpar estado local
                this.notifications = [];
                this.unreadCount = 0;
                this.pagination.total = 0;
                this.hasMore = false;
                window.dispatchEvent(new CustomEvent('notifications-updated'));
            } catch (e) {
                console.error('Erro ao arquivar notificações:', e);
            }
        },

        emptyMessage() {
            const map = {
                all: 'Nenhuma notificação encontrada',
                unread: 'Nenhuma notificação não lida',
                read: 'Nenhuma notificação lida',
            };
            return map[this.activeFilter] || 'Nenhuma notificação';
        },
    };
}
</script>
