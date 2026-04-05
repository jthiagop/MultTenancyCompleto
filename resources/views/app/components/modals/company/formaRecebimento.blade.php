<!--begin::Modal - Add Forma Recebimento-->
<div class="modal fade" id="kt_modal_add_recebimento" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog mw-650px">
        <!--begin::Modal content-->
        <div class="modal-content">
            <!--begin::Modal header-->
            <div class="modal-header">
                <h2 class="fw-bold">Forma de Recebimento</h2>
                <div id="kt_modal_add_recebimento_close" class="btn btn-icon btn-sm btn-active-icon-primary"
                    data-bs-dismiss="modal">
                    <span class="svg-icon svg-icon-1">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                transform="rotate(-45 6 17.3137)" fill="currentColor" />
                            <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                transform="rotate(45 7.41422 6)" fill="currentColor" />
                        </svg>
                    </span>
                </div>
            </div>
            <!--end::Modal header-->
            <!--begin::Modal body-->
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <!--begin::Form-->
                <form id="kt_modal_add_recebimento_form" method="POST"
                    action="{{ route('formas-recebimento.store') }}">
                    @csrf
                    <!--begin::Row-->
                    <div class="row">
                        <!--begin::Col-->
                        <div class="col-md-7">
                            <div class="fv-row mb-7">
                                <label class="fs-6 fw-semibold form-label mb-2">
                                    <span class="required">Nome</span>
                                    <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                        title="Nome da forma de recebimento."></i>
                                </label>
                                <input type="text" class="form-control form-control-solid" name="nome"
                                    id="recebimento_nome" value="" />
                            </div>
                        </div>
                        <!--end::Col-->
                        <!--begin::Col-->
                        <div class="col-md-5">
                            <div class="fv-row mb-7">
                                <label class="fs-6 fw-semibold form-label mb-2">
                                    <span class="">Código</span>
                                    <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                        title="Sigla ou código descritivo (ex: 'PIX', 'BOL', 'TED')."></i>
                                </label>
                                <input type="text" class="form-control form-control-solid"
                                    placeholder="Ex: PIX, BOL" name="codigo" id="recebimento_codigo" value="" />
                            </div>
                        </div>
                        <!--end::Col-->
                    </div>
                    <!--end::Row-->

                    <!--begin::Row-->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="fv-row mb-7">
                                <label class="required fs-6 fw-semibold form-label mb-2">Ativado/Desativado</label>
                                <select class="form-select form-select-solid fw-bold" data-control="select2"
                                    data-placeholder="Selecione uma opção" data-hide-search="true"
                                    id="recebimento_ativo" name="ativo">
                                    <option></option>
                                    <option value="1">Ativado</option>
                                    <option value="0">Desativado</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <!--end::Row-->

                    <!--begin::Input group-->
                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold form-label mb-2">
                            <span>Observações</span>
                        </label>
                        <textarea class="form-control form-control-solid" rows="3"
                            placeholder="Digite observações adicionais..." name="observacao"
                            id="recebimento_observacao"></textarea>
                    </div>
                    <!--end::Input group-->

                    <!--begin::Actions-->
                    <div class="text-center">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Sair</button>
                        <button type="submit" id="kt_modal_add_recebimento_submit" class="btn btn-primary">
                            <span class="indicator-label">Enviar</span>
                            <span class="indicator-progress">Espere...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
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
<!--end::Modal - Add Forma Recebimento-->

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var form = document.getElementById('kt_modal_add_recebimento_form');
        if (!form) return;

        form.addEventListener('submit', function(e) {
            var nome = document.getElementById('recebimento_nome').value.trim();
            var codigo = document.getElementById('recebimento_codigo').value.trim();
            var ativo = document.getElementById('recebimento_ativo').value;

            if (!nome) {
                e.preventDefault();
                Swal.fire('Atenção', 'Preencha o nome da forma de recebimento.', 'warning');
                return false;
            }

            if (!codigo) {
                e.preventDefault();
                Swal.fire('Atenção', 'Preencha o código da forma de recebimento.', 'warning');
                return false;
            }

            if (!ativo) {
                e.preventDefault();
                Swal.fire('Atenção', 'Selecione se a forma de recebimento está ativa ou não.', 'warning');
                return false;
            }
        });
    });
</script>
