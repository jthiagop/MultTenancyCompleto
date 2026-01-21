{{-- Modal para exclusão de lançamentos recorrentes --}}
<div class="modal fade" id="kt_modal_delete_recurrence" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-top mw-550px">
        <div class="modal-content">
            {{-- Header --}}
            <!-- Cabeçalho -->
            <div class="modal-header">
                <h1 class="modal-title fw-bold">Excluir lançamento</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{-- Body --}}
            <div class="modal-body text-center">
                <div class="text-center mb-13">
                    {{-- Description --}}
                    <div class="text-muted fw-semibold fs-5 mb-10">
                        Ao confirmar esta ação, quais lançamentos você deseja excluir?
                    </div>

                    {{-- Radio Options --}}
                    <div class="d-flex flex-column gap-5">
                        {{-- Option 1: Current only --}}
                        <label class="btn btn-outline btn-outline-dashed btn-active-light-primary d-flex text-start p-6"
                            for="delete_scope_current">
                            <span
                                class="form-check form-check-custom form-check-solid form-check-sm align-items-start mt-1">
                                <input class="form-check-input" type="radio" name="delete_scope" value="current"
                                    id="delete_scope_current" checked>
                            </span>
                            <span class="ms-5">
                                <span class="fs-4 fw-bold text-gray-800 d-block">Somente o lançamento atual</span>
                                <span class="fw-semibold fs-7 text-gray-600">
                                    Apenas este lançamento será excluído. Os demais da série permanecerão.
                                </span>
                            </span>
                        </label>

                        {{-- Option 2: All in series --}}
                        <label class="btn btn-outline btn-outline-dashed btn-active-light-danger d-flex text-start p-6"
                            for="delete_scope_all">
                            <span
                                class="form-check form-check-custom form-check-solid form-check-sm align-items-start mt-1">
                                <input class="form-check-input" type="radio" name="delete_scope" value="all"
                                    id="delete_scope_all">
                            </span>
                            <span class="ms-5">
                                <span class="fs-4 fw-bold text-gray-800 d-block">Todos os lançamentos</span>
                                <span class="fw-semibold fs-7 text-gray-600">
                                    Todos os lançamentos desta recorrência serão excluídos permanentemente.
                                </span>
                            </span>
                        </label>
                    </div>
                </div>

                {{-- Hidden fields --}}
                <input type="hidden" id="delete_recurrence_transaction_id" value="">

                {{-- Actions --}}
                <div class="text-center">
                    <button type="button" class="btn btn-sm btn-light me-3" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-2"></i>
                        Cancelar</button>
                    <button type="button" class="btn btn-sm btn-danger" id="kt_modal_delete_recurrence_submit">
                        <i class="bi bi-trash-fill me-2"></i>
                        Confirmar Exclusão
                        <span class="indicator-progress">
                            Aguarde... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('kt_modal_delete_recurrence');
            const submitButton = document.getElementById('kt_modal_delete_recurrence_submit');
            const transactionIdInput = document.getElementById('delete_recurrence_transaction_id');

            // Function to open modal
            window.openDeleteRecurrenceModal = function(transactionId) {
                transactionIdInput.value = transactionId;
                const modalInstance = new bootstrap.Modal(modal);
                modalInstance.show();
            };

            // Submit handler
            if (submitButton) {
                submitButton.addEventListener('click', function() {
                    const transactionId = transactionIdInput.value;
                    const deleteScope = document.querySelector('input[name="delete_scope"]:checked').value;

                    if (!transactionId) {
                        toastr.error('ID da transação não encontrado');
                        return;
                    }

                    // Show loading
                    submitButton.setAttribute('data-kt-indicator', 'on');
                    submitButton.disabled = true;

                    // Prepare form data
                    const formData = new FormData();
                    formData.append('_token', document.querySelector('meta[name="csrf-token"]')
                        .getAttribute('content'));
                    formData.append('_method', 'DELETE');
                    formData.append('delete_scope', deleteScope);

                    // Submit delete request
                    fetch(`/banco/${transactionId}`, {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            // Hide loading
                            submitButton.removeAttribute('data-kt-indicator');
                            submitButton.disabled = false;

                            if (data.success) {
                                // Close modal
                                const modalInstance = bootstrap.Modal.getInstance(modal);
                                modalInstance.hide();

                                // Show success message
                                toastr.success(data.message || 'Lançamento excluído com sucesso!');

                                // Emit event to reload datatable
                                if (window.DominusEvents) {
                                    window.DominusEvents.emit('transaction.deleted', {
                                        id: transactionId,
                                        scope: deleteScope
                                    });
                                }

                                // Reload page after delay
                                setTimeout(() => {
                                    window.location.reload();
                                }, 1500);
                            } else {
                                toastr.error(data.message || 'Erro ao excluir lançamento');
                            }
                        })
                        .catch(error => {
                            console.error('Erro:', error);
                            submitButton.removeAttribute('data-kt-indicator');
                            submitButton.disabled = false;
                            toastr.error('Erro ao excluir lançamento');
                        });
                });
            }
        });
    </script>
@endpush
