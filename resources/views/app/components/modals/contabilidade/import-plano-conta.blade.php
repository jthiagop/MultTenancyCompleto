<!--begin::Modal - Importar Plano de Contas-->
<div class="modal fade" id="kt_modal_import_plano_contas" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <!--begin::Modal content-->
        <div class="modal-content">
            <!--begin::Modal header-->
            <div class="modal-header">
                <!--begin::Modal title-->
                <h2 class="fw-bold">Importar Plano de Contas</h2>
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
                <form id="kt_modal_import_plano_contas_form" class="form" action="{{ route('contabilidade.plano-contas.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <!--begin::Input group-->
                    <div class="fv-row mb-10">
                        <!--begin::Label-->
                        <label class="fs-5 fw-semibold form-label mb-5">Selecione o arquivo (CSV ou Excel)</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <input type="file" name="file" class="form-control form-control-solid" accept=".csv,.xlsx,.xls" required />
                        <!--end::Input-->
                        <!--begin::Hint-->
                        <div class="form-text">
                            <strong>Formato esperado:</strong> O arquivo deve conter as colunas: <code>Código</code>, <code>Tipo</code> (S ou A), <code>Descrição</code>
                            <br>
                            <strong>Exemplo:</strong>
                            <pre class="mt-2 p-3 bg-light rounded">
                                        Código,Tipo,Descrição
                                        1,S,ATIVO
                                        1.01,S,ATIVO CIRCULANTE
                                        1.01.01,S,DISPONIBILIDADES
                                        1.01.01.0001,A,CAIXA GERAL
                            </pre>
                        </div>
                        <!--end::Hint-->
                    </div>
                    <!--end::Input group-->
                    <!--begin::Actions-->
                    <div class="text-center">
                        <button type="reset" class="btn btn-sm btn-light me-3" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" id="kt_modal_import_plano_contas_submit" class="btn btn-sm btn-primary">
                            <span class="indicator-label">Importar</span>
                            <span class="indicator-progress">Importando...
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
<!--end::Modal - Importar Plano de Contas-->

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('kt_modal_import_plano_contas_form');
    const submitButton = document.getElementById('kt_modal_import_plano_contas_submit');

    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(form);
            const fileInput = form.querySelector('input[type="file"]');

            if (!fileInput.files.length) {
                Swal.fire({
                    text: "Por favor, selecione um arquivo para importar.",
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

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                submitButton.disabled = false;
                submitButton.removeAttribute('data-kt-indicator');

                if (data.success) {
                    Swal.fire({
                        text: data.message || "Plano de contas importado com sucesso!",
                        icon: "success",
                        buttonsStyling: false,
                        confirmButtonText: "Ok!",
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    }).then(() => {
                        // Fecha o modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('kt_modal_import_plano_contas'));
                        if (modal) {
                            modal.hide();
                        }
                        // Recarrega a página para mostrar as novas contas
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        text: data.message || "Erro ao importar plano de contas.",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok!",
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    });
                }
            })
            .catch(error => {
                submitButton.disabled = false;
                submitButton.removeAttribute('data-kt-indicator');

                console.error('Erro:', error);
                Swal.fire({
                    text: "Erro ao importar plano de contas. Por favor, tente novamente.",
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

