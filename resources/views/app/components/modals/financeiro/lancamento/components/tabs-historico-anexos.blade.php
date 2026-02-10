<!--begin::Card-->
<div class="card mb-xl-10 border border-gray-300 border-active active ">
    <div class="card-header ">
        <div id="kt_app_toolbar" class="app-toolbar py-lg-6 d-flex justify-content-between align-items-center w-100">
            <ul class="nav nav-tabs nav-line-tabs">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#kt_tab_pane_1">Histórico complementar</a>
                </li>
                <li class="nav-item" id="tab_anexos_item">
                    <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_pane_2">Anexos</a>
                </li>
            </ul>
        </div>
    </div>
    <div class="card-body px-10">

        <!--begin::Input group-->
        <div class="d-flex flex-column mb-8">
            <div class="d-flex flex-column mb-5 fv-row">
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="kt_tab_pane_1" role="tabpanel">
                        <textarea class="form-control" name="historico_complementar" id="complemento" cmaxlength="250" rows="3"
                            name="target_details" placeholder="Mais detalhes sobre o lançamento"></textarea>
                        <span class="fs-6 text-muted">Insira no máximo 250 caracteres</span>
                    </div>
                    <div class="tab-pane fade" id="kt_tab_pane_2" role="tabpanel">
                        <x-anexos-input name="anexos" :anexosExistentes="[]" />
                    </div>
                </div>
            </div>
        </div>
        <!--end::Input group-->
    </div>
</div>
