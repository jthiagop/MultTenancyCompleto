<!--begin::Modal - Excluir Conta Contábil-->
<div class="modal fade" id="kt_modal_delete_plano_conta" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <!-- Cabeçalho -->
            <div class="modal-header">
                <h5 class="modal-title  fw-bold">
                    Confirmar Exclusão
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Corpo -->
            <div class="modal-body text-center">
                <i class="bi bi-exclamation-circle-fill text-danger icon-size-9 mb-4"></i>
                <p class="mb-0 fs-5 fw-semibold text-center">
                    Tem certeza que deseja excluir a conta contábil <span class="text-nowrap fs-3 fw-bold" id="conta-name"></span> ?
                </p>
                <small class="text-muted d-block mt-3">
                    <i class="fas fa-info-circle me-1 icon-size-9"></i>
                    Esta ação não pode ser desfeita e pode afetar registros relacionados.
                </small>
            </div>

            <!-- Rodapé -->
            <div class="modal-footer justify-content-center">
                <form id="delete-plano-conta-form" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-sm btn-danger">
                        <i class="fas fa-trash-alt"></i> Confirmar Exclusão
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
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>Excluindo...';
        submitBtn.disabled = true;

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ _method: 'DELETE' })
        })
        .then(response => {
            return response.json().then(data => ({
                ok: response.ok,
                status: response.status,
                data: data
            }));
        })
        .then(({ ok, status, data }) => {
            if (ok && (data.success || data.message)) {
                // Fecha o modal de exclusão
                const modalEl = document.getElementById('kt_modal_delete_plano_conta');
                const modalInstance = bootstrap.Modal.getInstance(modalEl);
                if (modalInstance) {
                    modalInstance.hide();
                }

                // Recarrega apenas a tabela
                if (typeof window.reloadPlanoContasTable === 'function') {
                    window.reloadPlanoContasTable();
                }

                // Toast de sucesso
                toastr.options = {
                    closeButton: true,
                    progressBar: true,
                    positionClass: 'toastr-top-right',
                    timeOut: 4000,
                };
                toastr.success(data.message || 'Conta excluída com sucesso!');
            } else {
                // Erro de validação ou servidor
                toastr.error(data.message || 'Ocorreu um erro ao excluir a conta.');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            toastr.error('Erro de comunicação com o servidor. Tente novamente.');
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
