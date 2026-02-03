<x-tenant-modal id="kt_modal_ministry" title="Registrar Ministério" footerAlign="center">
    <x-slot name="header">
        <h2 class="fw-bold" id="kt_modal_ministry_title">Registrar Ministério</h2>
    </x-slot>

    <!--begin::Form-->
    <form id="kt_modal_ministry_form" class="form" action="#">
        <input type="hidden" name="ministry_id" id="ministry_id">
        <input type="hidden" name="ministry_type_id" id="ministry_type_id">
        <input type="hidden" name="member_id" value="{{ $member->id }}">

        <!--begin::Row Data e Ministrante-->
        <div class="row g-5 mb-4">
            <!--begin::Col-->
            <div class="col-md-4 fv-row">
                <x-tenant-date name="ministry_date" label="Data" placeholder="Informe a data" required="true" />
            </div>
            <!--end::Col-->
            <!--begin::Col-->
            <div class="col-md-8 fv-row">
                <div class="d-flex flex-column mb-5 fv-row">
                    <x-tenant-input name="minister_name" label="Ministrante" placeholder="Digite o nome do Ministrante"
                        required="true" class="" />
                </div>
                <!--end::Col-->
            </div>
            <!--end::Row-->
        </div>
        <!--end::Col Dados-->

        <!--begin::Input group-->
        <div class="fv-row mb-7">
            <!--begin::Label-->
            <x-tenant-input name="diocese_name" label="Paroquia"
                placeholder="Ex: Paroquia da Imaculada Conceição - Lajedo/PE" required="true" class="" />
            <!--end::Input-->
        </div>
        <!--end::Input group-->

        <!--begin::Input group-->
        <div class="fv-row mb-7">
            <x-tenant-textarea name="ministry_notes" label="Informações Adicionais"
                placeholder="Observações sobre a cerimônia, local, etc." rows="3" />
        </div>
        <!--end::Input group-->
    </form>
    <!--end::Form-->

    <x-slot name="footer">
        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="kt_modal_ministry_submit">
            <span class="indicator-label">Salvar</span>
            <span class="indicator-progress">Por favor, aguarde...
                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
            </span>
        </button>
    </x-slot>
</x-tenant-modal>
