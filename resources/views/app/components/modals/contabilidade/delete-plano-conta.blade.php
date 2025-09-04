<!--begin::Modal - Excluir Conta Contábil-->
<div class="modal fade" id="kt_modal_delete_plano_conta" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <!-- Cabeçalho -->
            <div class="modal-header">
                <h5 class="modal-title text-danger fw-bold">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Confirmar Exclusão
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Corpo -->
            <div class="modal-body text-center">
                <i class="bi bi-exclamation-circle-fill text-danger fs-1 mb-4"></i>
                <p class="mb-0 fs-5 fw-semibold text-center">
                    Tem certeza que deseja excluir a conta contábil <strong id="conta-name"></strong>?
                </p>
                <small class="text-muted d-block mt-3">
                    <i class="fas fa-info-circle me-1"></i>
                    Esta ação não pode ser desfeita e pode afetar registros relacionados.
                </small>
            </div>

            <!-- Rodapé -->
            <div class="modal-footer justify-content-center">
                <form id="delete-plano-conta-form" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-danger px-4">
                        <i class="fas fa-trash-alt me-2"></i> Confirmar Exclusão
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<!--end::Modal-->

<!--begin::Scripts-->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Manipula o envio do formulário de exclusão
    document.getElementById('delete-plano-conta-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const form = this;
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        // Mostra loading
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Excluindo...';
        submitBtn.disabled = true;
        
        fetch(form.action, {
            method: form.method,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.message) {
                // Sucesso
                Swal.fire({
                    text: data.message,
                    icon: "success",
                    buttonsStyling: false,
                    confirmButtonText: "Ok!",
                    customClass: {
                        confirmButton: "btn btn-primary"
                    }
                }).then(() => {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        window.location.reload();
                    }
                });
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            Swal.fire({
                text: "Ocorreu um erro inesperado. Tente novamente.",
                icon: "error",
                buttonsStyling: false,
                confirmButtonText: "Ok!",
                customClass: {
                    confirmButton: "btn btn-primary"
                }
            });
        })
        .finally(() => {
            // Restaura o botão
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });
});
</script>
<!--end::Scripts-->
