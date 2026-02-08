<x-tenant-app-layout pageTitle="Notificações">
    <!--begin::Main-->
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <!--begin::Content wrapper-->
        <div class="d-flex flex-column flex-column-fluid">
            <!--begin::Toolbar-->
            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                            Notificações
                        </h1>
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-400 w-5px h-2px"></span>
                            </li>
                            <li class="breadcrumb-item text-muted">Notificações</li>
                        </ul>
                    </div>
                    <div class="d-flex align-items-center gap-2 gap-lg-3">
                        <button type="button" class="btn btn-sm btn-light-primary" id="mark-all-read-page-btn">
                            <i class="fa-solid fa-check-double me-1"></i> Marcar todas como lidas
                        </button>
                        <button type="button" class="btn btn-sm btn-light-danger" id="clear-read-btn">
                            <i class="fa-solid fa-trash me-1"></i> Limpar lidas
                        </button>
                    </div>
                </div>
            </div>
            <!--end::Toolbar-->

            <!--begin::Content-->
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-fluid">
                    <!--begin::Card-->
                    <div class="card">
                        <!--begin::Card body-->
                        <div class="card-body p-0">
                            @forelse($notifications as $notification)
                                @php
                                    $data = $notification->data;
                                    $icon = $data['icon'] ?? 'fa-solid fa-bell';
                                    $color = $data['color'] ?? 'primary';
                                    $title = $data['title'] ?? 'Notificação';
                                    $message = $data['message'] ?? '';
                                    $url = $data['action_url'] ?? '#';
                                    $isUnread = !$notification->read_at;
                                @endphp
                                <div class="d-flex flex-stack px-8 py-6 border-bottom notification-row {{ $isUnread ? 'bg-light-primary' : '' }}"
                                     data-id="{{ $notification->id }}"
                                     data-url="{{ $url }}">
                                    <!--begin::Section-->
                                    <div class="d-flex align-items-center">
                                        <!--begin::Symbol-->
                                        <div class="symbol symbol-45px me-5">
                                            <span class="symbol-label bg-light-{{ $color }}">
                                                <i class="{{ $icon }} fs-2 text-{{ $color }}"></i>
                                            </span>
                                        </div>
                                        <!--end::Symbol-->
                                        <!--begin::Content-->
                                        <div class="d-flex flex-column">
                                            <a href="{{ $url }}" class="fs-5 text-gray-800 text-hover-primary fw-bold {{ $isUnread ? 'text-primary' : '' }}">
                                                {{ $title }}
                                            </a>
                                            <span class="fs-6 text-gray-600 mt-1">{{ $message }}</span>
                                            <span class="fs-7 text-muted mt-2">
                                                <i class="fa-regular fa-clock me-1"></i>
                                                {{ $notification->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                        <!--end::Content-->
                                    </div>
                                    <!--end::Section-->
                                    <!--begin::Actions-->
                                    <div class="d-flex align-items-center gap-2">
                                        @if($isUnread)
                                            <span class="badge badge-circle badge-primary" style="width: 10px; height: 10px;"></span>
                                        @endif
                                        <button type="button" class="btn btn-icon btn-sm btn-light-danger btn-delete-notification" 
                                                data-id="{{ $notification->id }}" title="Excluir">
                                            <i class="fa-solid fa-trash fs-7"></i>
                                        </button>
                                    </div>
                                    <!--end::Actions-->
                                </div>
                            @empty
                                <div class="text-center py-15">
                                    <i class="fa-regular fa-bell-slash fs-3x text-muted mb-5"></i>
                                    <p class="text-muted fs-5 mb-0">Nenhuma notificação encontrada</p>
                                </div>
                            @endforelse
                        </div>
                        <!--end::Card body-->
                        
                        @if($notifications->hasPages())
                            <!--begin::Card footer-->
                            <div class="card-footer d-flex justify-content-center py-6">
                                {{ $notifications->links() }}
                            </div>
                            <!--end::Card footer-->
                        @endif
                    </div>
                    <!--end::Card-->
                </div>
            </div>
            <!--end::Content-->
        </div>
        <!--end::Content wrapper-->
    </div>
    <!--end::Main-->

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            
            // Marcar todas como lidas
            document.getElementById('mark-all-read-page-btn')?.addEventListener('click', async function() {
                try {
                    const response = await fetch('/notifications/mark-all-read', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });
                    
                    if (response.ok) {
                        // Remover destaque de todas as notificações
                        document.querySelectorAll('.notification-row.bg-light-primary').forEach(row => {
                            row.classList.remove('bg-light-primary');
                            row.querySelector('.badge-primary.badge-circle')?.remove();
                            row.querySelector('.text-primary')?.classList.remove('text-primary');
                        });
                        
                        // Atualizar badge no navbar
                        if (window.NotificationsManager) {
                            window.NotificationsManager.loadUnreadCount();
                        }
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Sucesso!',
                            text: 'Todas as notificações foram marcadas como lidas.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                } catch (error) {
                    console.error('Erro:', error);
                }
            });
            
            // Limpar notificações lidas
            document.getElementById('clear-read-btn')?.addEventListener('click', async function() {
                const result = await Swal.fire({
                    title: 'Limpar notificações lidas?',
                    text: 'Esta ação não pode ser desfeita.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sim, limpar!',
                    cancelButtonText: 'Cancelar'
                });
                
                if (result.isConfirmed) {
                    try {
                        const response = await fetch('/notifications/clear/read', {
                            method: 'DELETE',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': csrfToken
                            }
                        });
                        
                        if (response.ok) {
                            // Remover linhas que não têm o destaque (já foram lidas)
                            document.querySelectorAll('.notification-row:not(.bg-light-primary)').forEach(row => {
                                row.remove();
                            });
                            
                            Swal.fire({
                                icon: 'success',
                                title: 'Sucesso!',
                                text: 'Notificações lidas foram removidas.',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    } catch (error) {
                        console.error('Erro:', error);
                    }
                }
            });
            
            // Excluir notificação individual
            document.querySelectorAll('.btn-delete-notification').forEach(btn => {
                btn.addEventListener('click', async function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const id = this.dataset.id;
                    const row = this.closest('.notification-row');
                    
                    try {
                        const response = await fetch(`/notifications/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': csrfToken
                            }
                        });
                        
                        if (response.ok) {
                            row.remove();
                            
                            // Atualizar contagem no navbar
                            if (window.NotificationsManager) {
                                window.NotificationsManager.loadUnreadCount();
                            }
                        }
                    } catch (error) {
                        console.error('Erro:', error);
                    }
                });
            });
            
            // Marcar como lida ao clicar na linha
            document.querySelectorAll('.notification-row').forEach(row => {
                row.addEventListener('click', async function(e) {
                    if (e.target.closest('.btn-delete-notification')) return;
                    
                    const id = this.dataset.id;
                    const url = this.dataset.url;
                    
                    // Marcar como lida
                    try {
                        await fetch(`/notifications/${id}/read`, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': csrfToken
                            }
                        });
                        
                        // Atualizar contagem no navbar
                        if (window.NotificationsManager) {
                            window.NotificationsManager.loadUnreadCount();
                        }
                    } catch (error) {
                        console.error('Erro:', error);
                    }
                    
                    // Navegar se houver URL válida
                    if (url && url !== '#' && url !== '') {
                        window.location.href = url;
                    }
                });
            });
        });
    </script>
    @endpush
</x-tenant-app-layout>
