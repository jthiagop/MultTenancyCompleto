<!--begin::Modal - Exportar Plano de Contas-->
<div class="modal fade" id="kt_subscriptions_export_modal" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <!--begin::Modal content-->
        <div class="modal-content">
            <!--begin::Modal header-->
            <div class="modal-header">
                <!--begin::Modal title-->
                <h2 class="fw-bold">Exportar Plano de Contas</h2>
                <!--end::Modal title-->
                <!--begin::Close-->
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
                    <span class="svg-icon svg-icon-1">
                        <i class="bi bi-x-lg fs-3"></i>
                    </span>
                    <!--end::Svg Icon-->
                </div>
                <!--end::Close-->
            </div>
            <!--end::Modal header-->
            <!--begin::Modal body-->
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <!--begin::Form-->
                <form id="kt_plano_contas_export_form" class="form" action="{{ route('contabilidade.plano-contas.export') }}" method="POST">
                    @csrf
                    <!--begin::Input group-->
                    <div class="fv-row mb-10">
                        <!--begin::Label-->
                        <label class="fs-5 fw-semibold form-label mb-5">Selecione o formato de exportação:</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <select data-control="select2" data-placeholder="Selecione um formato"
                            data-hide-search="true" name="format" class="form-select form-select-solid" required>
                            <option value="">Selecione...</option>
                            <option value="excel">Excel (.xlsx)</option>
                            <option value="csv">CSV</option>
                            <option value="pdf">PDF</option>
                        </select>
                        <!--end::Input-->
                    </div>
                    <!--end::Input group-->
                    
                    <!--begin::Input group-->
                    <div class="fv-row mb-10">
                        <!--begin::Label-->
                        <label class="fs-5 fw-semibold form-label mb-5">Filtrar por tipo:</label>
                        <!--end::Label-->
                        <!--begin::Radio group-->
                        <div class="d-flex flex-column">
                            <!--begin::Radio button-->
                            <label class="form-check form-check-custom form-check-sm form-check-solid mb-3">
                                <input class="form-check-input" type="radio" value="all"
                                    checked="checked" name="account_type" />
                                <span class="form-check-label text-gray-600 fw-semibold">Todas as contas</span>
                            </label>
                            <!--end::Radio button-->
                            <!--begin::Radio button-->
                            <label class="form-check form-check-custom form-check-sm form-check-solid mb-3">
                                <input class="form-check-input" type="radio" value="ativo" name="account_type" />
                                <span class="form-check-label text-gray-600 fw-semibold">Apenas Ativo</span>
                            </label>
                            <!--end::Radio button-->
                            <!--begin::Radio button-->
                            <label class="form-check form-check-custom form-check-sm form-check-solid mb-3">
                                <input class="form-check-input" type="radio" value="passivo" name="account_type" />
                                <span class="form-check-label text-gray-600 fw-semibold">Apenas Passivo</span>
                            </label>
                            <!--end::Radio button-->
                            <!--begin::Radio button-->
                            <label class="form-check form-check-custom form-check-sm form-check-solid mb-3">
                                <input class="form-check-input" type="radio" value="receita" name="account_type" />
                                <span class="form-check-label text-gray-600 fw-semibold">Apenas Receita</span>
                            </label>
                            <!--end::Radio button-->
                            <!--begin::Radio button-->
                            <label class="form-check form-check-custom form-check-sm form-check-solid mb-3">
                                <input class="form-check-input" type="radio" value="despesa" name="account_type" />
                                <span class="form-check-label text-gray-600 fw-semibold">Apenas Despesa</span>
                            </label>
                            <!--end::Radio button-->
                            <!--begin::Radio button-->
                            <label class="form-check form-check-custom form-check-sm form-check-solid">
                                <input class="form-check-input" type="radio" value="patrimonio_liquido" name="account_type" />
                                <span class="form-check-label text-gray-600 fw-semibold">Apenas Patrimônio Líquido</span>
                            </label>
                            <!--end::Radio button-->
                        </div>
                        <!--end::Radio group-->
                    </div>
                    <!--end::Input group-->
                    
                    <!--begin::Actions-->
                    <div class="text-center">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" id="kt_plano_contas_export_submit" class="btn btn-primary">
                            <span class="indicator-label">Exportar</span>
                            <span class="indicator-progress">Exportando...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                    <!--end::Actions-->
                </form>
                <!--end::Form-->
            </div>
            <!--end::Modal body-->
        </div>
        <!--end::Modal content-->
    </div>
    <!--end::Modal dialog-->
</div>
<!--end::Modal - Exportar Plano de Contas-->

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('kt_plano_contas_export_form');
    const submitButton = document.getElementById('kt_plano_contas_export_submit');

    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(form);
            const format = formData.get('format');

            if (!format) {
                Swal.fire({
                    text: "Por favor, selecione um formato de exportação.",
                    icon: "warning",
                    buttonsStyling: false,
                    confirmButtonText: "Ok!",
                    customClass: {
                        confirmButton: "btn btn-primary"
                    }
                });
                return;
            }

            // Desabilita o botão e mostra loading
            submitButton.disabled = true;
            submitButton.setAttribute('data-kt-indicator', 'on');

            // Submete o formulário para download
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erro ao exportar');
                }
                return response.blob();
            })
            .then(blob => {
                submitButton.disabled = false;
                submitButton.removeAttribute('data-kt-indicator');

                // Cria um link para download
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                
                // Define o nome do arquivo baseado no formato
                let filename = 'plano_de_contas';
                if (format === 'excel') {
                    filename += '.xlsx';
                } else if (format === 'csv') {
                    filename += '.csv';
                } else if (format === 'pdf') {
                    filename += '.pdf';
                }
                
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                a.remove();

                // Fecha o modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('kt_subscriptions_export_modal'));
                if (modal) {
                    modal.hide();
                }

                Swal.fire({
                    text: "Plano de contas exportado com sucesso!",
                    icon: "success",
                    buttonsStyling: false,
                    confirmButtonText: "Ok!",
                    customClass: {
                        confirmButton: "btn btn-primary"
                    }
                });
            })
            .catch(error => {
                submitButton.disabled = false;
                submitButton.removeAttribute('data-kt-indicator');

                console.error('Erro:', error);
                Swal.fire({
                    text: "Erro ao exportar plano de contas. Por favor, tente novamente.",
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "Ok!",
                    customClass: {
                        confirmButton: "btn btn-primary"
                    }
                });
            });
        });
    }
});
</script>
