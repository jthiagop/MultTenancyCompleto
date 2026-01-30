{{-- Toast Stack Container - Posicionado no canto superior direito --}}
<div id="kt_toast_stack_container" class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1090;"></div>

{{-- Toast Template (hidden) - Será clonado pelo JavaScript --}}
<div class="d-none" id="kt_toast_template_wrapper">
    <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="5000">
        <div class="toast-header">
            <i class="fs-2 me-3" data-toast-icon></i>
            <strong class="me-auto" data-toast-title>Sistema</strong>
            <small class="text-muted" data-toast-time>Agora</small>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Fechar"></button>
        </div>
        <div class="toast-body" data-toast-message></div>
    </div>
</div>
