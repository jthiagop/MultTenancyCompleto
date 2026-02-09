{{-- Modal de Sessão Expirada --}}
<div class="modal fade" id="session-expired-modal" tabindex="-1"
     data-bs-backdrop="static" data-bs-keyboard="false"
     aria-labelledby="sessionExpiredModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
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
                    <img src="/tenancy/assets/media/auth/23.svg" alt="Sessão Expirada"
                         class="mw-300px mx-auto" style="max-width: 300px; height: auto;">
                </div>
                <!--end::Ilustração SVG-->

                <!--begin::Título-->
                <h2 class="fw-bold mb-3" id="sessionExpiredModalLabel">Sessão Expirada</h2>
                <!--end::Título-->

                <!--begin::Mensagem-->
                <p class="text-gray-600 fs-5 mb-5" id="session-expired-message">
                    Sua sessão expirou por inatividade. Por favor, faça login novamente para continuar.
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
