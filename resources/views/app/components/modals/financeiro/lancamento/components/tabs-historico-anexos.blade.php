<!--begin::Input group-->
<div class="d-flex flex-column mb-8">
    <div class="d-flex flex-column mb-5 fv-row">
        <ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab"
                    href="#kt_tab_pane_1">Histórico complementar</a>
            </li>
            <li class="nav-item" id="tab_anexos_item" style="display: none;">
                <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_pane_2">Anexos</a>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="kt_tab_pane_1" role="tabpanel">
                <textarea class="form-control" name="historico_complementar" id="complemento" cmaxlength="250" rows="3"
                    name="target_details" placeholder="Mais detalhes sobre o lançamento"></textarea>
                <span class="fs-6 text-muted">Insira no máximo 250
                    caracteres</span>
            </div>
            <div class="tab-pane fade" id="kt_tab_pane_2" role="tabpanel">
                <x-anexos-input name="anexos" :anexosExistentes="[]" />
            </div>
        </div>
    </div>
</div>
<!--end::Input group-->

