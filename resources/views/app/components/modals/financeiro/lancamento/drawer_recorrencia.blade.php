<!--begin::Drawer - Configuração de Recorrência-->
<style>
    #kt_drawer_recorrencia {
        z-index: 1070 !important;
    }

    #kt_drawer_recorrencia .drawer-overlay {
        z-index: 1065 !important;
    }

    /* Ensure inputs in drawer are clickable */
    #kt_drawer_recorrencia input,
    #kt_drawer_recorrencia button,
    #kt_drawer_recorrencia select,
    #kt_drawer_recorrencia textarea {
        pointer-events: auto !important;
    }
</style>
<div id="kt_drawer_recorrencia" class="bg-white" data-kt-drawer="true" data-kt-drawer-activate="true"
    data-kt-drawer-toggle="#kt_drawer_recorrencia_button" data-kt-drawer-close="#kt_drawer_recorrencia_close"
    data-kt-drawer-width="500px" data-kt-drawer-permanent="true" data-kt-drawer-direction="end" tabindex="0">
    <!--begin::Card-->
    <div class="card shadow-none rounded-0 w-100">
        <!--begin::Header-->
        <div class="card-header pe-5">
            <!--begin::Title-->
            <div class="card-title">
                <h3 class="fw-bold m-0">Recorrência</h3>
            </div>
            <!--end::Title-->
            <!--begin::Toolbar-->
            <div class="card-toolbar">
                <button type="button" class="btn btn-sm btn-icon btn-active-color-primary"
                    id="kt_drawer_recorrencia_close">
                    <i class="bi bi-x fs-1">
                    </i>
                </button>
            </div>
            <!--end::Toolbar-->
        </div>
        <!--end::Header-->

        <!--begin::Body-->
        <div class="card-body">
            <form id="kt_drawer_recorrencia_form">
                @csrf
                <meta name="csrf-token" content="{{ csrf_token() }}">

                <!--begin::Input group - Frequência da recorrência-->
                <div class="mb-10">
                    <div class="row g-9 mb-5">
                        <!--begin::Col-->
                        <div class="col-md-6">
                            <x-tenant-label for="intervalo_repeticao" required>Repetir a cada</x-tenant-label>
                            <x-tenant-input name="intervalo_repeticao" id="intervalo_repeticao" type="number"
                                placeholder="1" min="1" required class="" />
                        </div>
                        <!--end::Col-->

                        <!--begin::Col-->
                        <div class="col-md-6">
                            <x-tenant-select name="frequencia" id="frequencia_recorrencia" label="Frequência"
                                placeholder="Selecione a frequência" required :hideSearch="true"
                                dropdown-parent="#kt_drawer_recorrencia" class="">
                                <option value="diario">Dia(s)</option>
                                <option value="semanal">Semana(s)</option>
                                <option value="mensal">Mês(es)</option>
                                <option value="anual">Ano(s)</option>
                            </x-tenant-select>
                        </div>
                        <!--end::Col-->
                    </div>
                </div>
                <!--end::Input group-->
                <div class="separator my-5"></div>

                <!--begin::Input group - Término da recorrência-->
                <div class="mb-10">
                    <h3 class="fs-5 fw-semibold mb-5">Término da recorrência</h3>

                    <div class="d-flex align-items-center gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tipo_termino"
                                id="tipo_termino_ocorrencias" value="ocorrencias" checked>
                            <label class="form-check-label" for="tipo_termino_ocorrencias">
                                Após
                            </label>
                        </div>

                        <div class="flex-grow-1">
                            <x-tenant-input name="apos_ocorrencias" id="apos_ocorrencias" type="number"
                                placeholder="12" min="1" max="366" required class="" />
                        </div>

                        <span class="text-muted">Ocorrências</span>
                    </div>
                </div>
                <!--end::Input group-->
            </form>
        </div>
        <!--end::Body-->
        <!--begin::Actions-->
        <div class="card-footer d-flex justify-content-end gap-3">
            <x-tenant-button
                type="button"
                variant="light"
                size="sm"
                icon="bi bi-x-lg"
                iconPosition="left"
                id="kt_drawer_recorrencia_close">
                Cancelar
            </x-tenant-button>

            <x-tenant-button
                type="button"
                variant="primary"
                size="sm"
                icon="bi bi-save"
                iconPosition="left"
                id="kt_drawer_recorrencia_submit"
                :loading="true"
                loadingText="Aguarde..."
                form="kt_drawer_recorrencia_form">
                Salvar
            </x-tenant-button>
        </div>
        <!--end::Actions-->
    </div>
    <!--end::Card-->
</div>
<!--end::Drawer - Configuração de Recorrência-->
