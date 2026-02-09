<x-tenant-app-layout>
    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid py-3 py-lg-6">
        <!--begin::Content container-->
        <div id="kt_app_content_container" class="app-container container-fluid">
            <!--begin::Card-->
            <div class="card card-flush">
                <!--begin::Card header-->
                <div class="card-header mt-6">
                    <!--begin::Card title-->
                    <div class="card-title">
                        <!--begin::Search-->
                        <div class="d-flex align-items-center position-relative my-1 me-5">
                            <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                            <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </span>
                            <!--end::Svg Icon-->
                            <input type="text" data-kt-modules-table-filter="search"
                                class="form-control form-control-solid w-250px ps-15" placeholder="Search Modulos" />
                        </div>
                        <!--end::Search-->
                    </div>
                    <!--end::Card title-->
                    <!--begin::Card toolbar-->
                    <div class="card-toolbar">
                        <!--begin::Button-->
                        <button type="button" class="btn btn-light-primary" data-bs-toggle="modal"
                            data-bs-target="#kt_modal_add_module">
                            <!--begin::Svg Icon | path: icons/duotune/general/gen035.svg-->
                            <span class="svg-icon svg-icon-3">
                                <i class="bi bi-plus-square-fill"></i>
                            </span>
                            <!--end::Svg Icon-->Add Módulo</button>
                        <!--end::Button-->
                    </div>
                    <!--end::Card toolbar-->
                </div>
                <!--end::Card header-->
                <!--begin::Card body-->
                <div class="card-body pt-0">
                    <!--begin::Table-->
                    <table class="table align-middle table-row-dashed fs-6 gy-5 mb-0" id="kt_modules_table"
                        data-url="{{ route('modules.data') }}">
                        <!--begin::Table head-->
                        <thead>
                            <!--begin::Table row-->
                            <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                <th class="min-w-125px">Nome</th>
                                <th class="min-w-100px">Chave</th>
                                <th class="min-w-150px">Rota</th>
                                <th class="min-w-100px">Status</th>
                                <th class="min-w-100px">Dashboard</th>
                                <th class="min-w-125px">Criado em</th>
                                <th class="text-end min-w-100px">Ações</th>
                            </tr>
                            <!--end::Table row-->
                        </thead>
                        <!--end::Table head-->
                        <!--begin::Table body-->
                        <tbody class="fw-semibold text-gray-600">
                            <!-- DataTables will populate this -->
                        </tbody>
                        <!--end::Table body-->
                    </table>
                    <!--end::Table-->
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Card-->
            <!--begin::Modals-->
            <!--begin::Modal - Add module-->
            <div class="modal fade" id="kt_modal_add_module" tabindex="-1" aria-hidden="true">
                <!--begin::Modal dialog-->
                <div class="modal-dialog modal-dialog-centered mw-650px">
                    <!--begin::Modal content-->
                    <div class="modal-content">
                        <!--begin::Modal header-->
                        <div class="modal-header">
                            <!--begin::Modal title-->
                            <h2 class="fw-bold">Adicionar Módulo</h2>
                            <!--end::Modal title-->
                            <!--begin::Close-->
                            <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal"
                                aria-label="Close">
                                <span class="svg-icon svg-icon-1">
                                    <i class="fa fa-times"></i>
                                </span>
                            </div>
                            <!--end::Close-->
                        </div>
                        <!--end::Modal header-->
                        <!--begin::Modal body-->
                        <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                            <!--begin::Form-->
                            <form id="kt_modal_add_module_form" class="form" action="#" enctype="multipart/form-data">
                                <!--begin::Image input-->
                                <div class="image-input image-input-outline image-input-placeholder"
                                    data-kt-image-input="true">
                                    <!--begin::Preview existing avatar-->
                                    <div class="image-input-wrapper w-125px h-125px"
                                        style="background-image: url(/tenancy/assets/media/avatars/icon.png);"></div>
                                    <!--end::Preview existing avatar-->
                                    <!--begin::Label-->
                                    <label
                                        class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                        data-kt-image-input-action="change" data-bs-toggle="tooltip"
                                        title="Alterar ícone">
                                        <i class="bi bi-pencil-fill fs-7"></i>
                                        <!--begin::Inputs-->
                                        <input type="file" name="icon" accept=".png, .jpg, .jpeg" />
                                        <input type="hidden" name="icon_remove" />
                                        <!--end::Inputs-->
                                    </label>
                                    <!--end::Label-->
                                    <!--begin::Cancel-->
                                    <span
                                        class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                        data-kt-image-input-action="cancel" data-bs-toggle="tooltip"
                                        title="Cancelar ícone">
                                        <i class="bi bi-x fs-2"></i>
                                    </span>
                                    <!--end::Cancel-->
                                    <!--begin::Remove-->
                                    <span
                                        class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                        data-kt-image-input-action="remove" data-bs-toggle="tooltip"
                                        title="Remover ícone">
                                        <i class="bi bi-x fs-2"></i>
                                    </span>
                                    <!--end::Remove-->
                                </div>
                                <!--end::Image input-->
                                <!--begin::Hint-->
                                <div class="form-text">Tipos de arquivo permitidos: png, jpg, jpeg.</div>
                                <!--end::Hint-->

                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <label class="fs-6 fw-semibold form-label mb-2">
                                        <span class="required">Nome do Módulo</span>
                                    </label>
                                    <input class="form-control form-control-solid" placeholder="Ex: Financeiro"
                                        name="module_name" required />
                                </div>
                                <!--end::Input group-->
                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <label class="fs-6 fw-semibold form-label mb-2">
                                        <span class="required">Chave</span>
                                    </label>
                                    <input class="form-control form-control-solid" placeholder="Ex: financeiro"
                                        name="module_key" required />
                                </div>
                                <!--end::Input group-->
                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <label class="fs-6 fw-semibold form-label mb-2">
                                        <span class="required">Nome da Rota</span>
                                    </label>
                                    <input class="form-control form-control-solid" placeholder="Ex: financeiro.index"
                                        name="module_route" required />
                                </div>
                                <!--end::Input group-->
                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <label class="fs-6 fw-semibold form-label mb-2">
                                        <span>Permissão</span>
                                    </label>
                                    <input class="form-control form-control-solid" placeholder="Ex: financeiro.index"
                                        name="module_permission" />
                                </div>
                                <!--end::Input group-->
                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <label class="fs-6 fw-semibold form-label mb-2">
                                        <span>Descrição</span>
                                    </label>
                                    <textarea class="form-control form-control-solid" rows="3" placeholder="Descrição do módulo"
                                        name="module_description"></textarea>
                                </div>
                                <!--end::Input group-->
                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <label class="form-check form-check-custom form-check-solid me-9">
                                        <input class="form-check-input" type="checkbox" value="1"
                                            name="module_active" id="kt_module_active" checked />
                                        <span class="form-check-label" for="kt_module_active">Módulo Ativo</span>
                                    </label>
                                </div>
                                <!--end::Input group-->
                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <label class="form-check form-check-custom form-check-solid me-9">
                                        <input class="form-check-input" type="checkbox" value="1"
                                            name="module_dashboard" id="kt_module_dashboard" checked />
                                        <span class="form-check-label" for="kt_module_dashboard">Exibir no
                                            Dashboard</span>
                                    </label>
                                </div>
                                <!--end::Input group-->
                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <label class="fs-6 fw-semibold form-label mb-2">
                                        <span>Ordem</span>
                                    </label>
                                    <input type="number" class="form-control form-control-solid" placeholder="0"
                                        name="module_order" value="0" />
                                </div>
                                <!--end::Input group-->
                                <!--begin::Actions-->
                                <div class="text-center pt-15">
                                    <button type="reset" class="btn btn-light me-3"
                                        data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-primary">
                                        <span class="indicator-label">Salvar</span>
                                        <span class="indicator-progress">Aguarde...
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
            <!--end::Modal - Add module-->
            <!--begin::Modal - Update module-->
            <div class="modal fade" id="kt_modal_update_module" tabindex="-1" aria-hidden="true">
                <!--begin::Modal dialog-->
                <div class="modal-dialog modal-dialog-centered mw-650px">
                    <!--begin::Modal content-->
                    <div class="modal-content">
                        <!--begin::Modal header-->
                        <div class="modal-header">
                            <!--begin::Modal title-->
                            <h2 class="fw-bold">Editar Módulo</h2>
                            <!--end::Modal title-->
                            <!--begin::Close-->
                            <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal"
                                aria-label="Close">
                                <span class="svg-icon svg-icon-1">
                                    <i class="fa fa-times"></i>
                                </span>
                            </div>
                            <!--end::Close-->
                        </div>
                        <!--end::Modal header-->
                        <!--begin::Modal body-->
                        <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                            <!--begin::Form-->
                            <form id="kt_modal_update_module_form" class="form" action="#" enctype="multipart/form-data">
                                <input type="hidden" name="module_id" />
                                <!--begin::Image input-->
                                <div class="fv-row mb-7">
                                    <label class="fs-6 fw-semibold form-label mb-2">
                                        <span>Ícone do Módulo</span>
                                    </label>
                                    <div class="image-input image-input-outline image-input-placeholder"
                                        data-kt-image-input="true">
                                        <!--begin::Preview existing avatar-->
                                        <div class="image-input-wrapper w-125px h-125px"
                                            style="background-image: url({{ asset('tenancy/assets/media/avatars/blank.png') }});"></div>
                                        <!--end::Preview existing avatar-->
                                        <!--begin::Label-->
                                        <label
                                            class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                            data-kt-image-input-action="change" data-bs-toggle="tooltip"
                                            title="Alterar ícone">
                                            <i class="bi bi-pencil-fill fs-7"></i>
                                            <!--begin::Inputs-->
                                            <input type="file" name="icon" accept=".png, .jpg, .jpeg" />
                                            <input type="hidden" name="icon_remove" />
                                            <!--end::Inputs-->
                                        </label>
                                        <!--end::Label-->
                                        <!--begin::Cancel-->
                                        <span
                                            class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                            data-kt-image-input-action="cancel" data-bs-toggle="tooltip"
                                            title="Cancelar ícone">
                                            <i class="bi bi-x fs-2"></i>
                                        </span>
                                        <!--end::Cancel-->
                                        <!--begin::Remove-->
                                        <span
                                            class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                            data-kt-image-input-action="remove" data-bs-toggle="tooltip"
                                            title="Remover ícone">
                                            <i class="bi bi-x fs-2"></i>
                                        </span>
                                        <!--end::Remove-->
                                    </div>
                                    <!--begin::Hint-->
                                    <div class="form-text">Tipos de arquivo permitidos: png, jpg, jpeg.</div>
                                    <!--end::Hint-->
                                </div>
                                <!--end::Image input-->
                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <label class="fs-6 fw-semibold form-label mb-2">
                                        <span class="required">Nome do Módulo</span>
                                    </label>
                                    <input class="form-control form-control-solid" placeholder="Ex: Financeiro"
                                        name="module_name" required />
                                </div>
                                <!--end::Input group-->
                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <label class="fs-6 fw-semibold form-label mb-2">
                                        <span class="required">Chave</span>
                                    </label>
                                    <input class="form-control form-control-solid" placeholder="Ex: financeiro"
                                        name="module_key" required />
                                </div>
                                <!--end::Input group-->
                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <label class="fs-6 fw-semibold form-label mb-2">
                                        <span class="required">Nome da Rota</span>
                                    </label>
                                    <input class="form-control form-control-solid" placeholder="Ex: financeiro.index"
                                        name="module_route" required />
                                </div>
                                <!--end::Input group-->
                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <label class="fs-6 fw-semibold form-label mb-2">
                                        <span>Permissão</span>
                                    </label>
                                    <input class="form-control form-control-solid" placeholder="Ex: financeiro.index"
                                        name="module_permission" />
                                </div>
                                <!--end::Input group-->
                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <label class="fs-6 fw-semibold form-label mb-2">
                                        <span>Descrição</span>
                                    </label>
                                    <textarea class="form-control form-control-solid" rows="3" placeholder="Descrição do módulo"
                                        name="module_description"></textarea>
                                </div>
                                <!--end::Input group-->
                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <label class="form-check form-check-custom form-check-solid me-9">
                                        <input class="form-check-input" type="checkbox" value="1"
                                            name="module_active" id="kt_module_active_update" />
                                        <span class="form-check-label" for="kt_module_active_update">Módulo
                                            Ativo</span>
                                    </label>
                                </div>
                                <!--end::Input group-->
                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <label class="form-check form-check-custom form-check-solid me-9">
                                        <input class="form-check-input" type="checkbox" value="1"
                                            name="module_dashboard" id="kt_module_dashboard_update" />
                                        <span class="form-check-label" for="kt_module_dashboard_update">Exibir no
                                            Dashboard</span>
                                    </label>
                                </div>
                                <!--end::Input group-->
                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <label class="fs-6 fw-semibold form-label mb-2">
                                        <span>Ordem</span>
                                    </label>
                                    <input type="number" class="form-control form-control-solid" placeholder="0"
                                        name="module_order" value="0" />
                                </div>
                                <!--end::Input group-->
                                <!--begin::Actions-->
                                <div class="text-center pt-15">
                                    <button type="reset" class="btn btn-light me-3"
                                        data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-primary">
                                        <span class="indicator-label">Atualizar</span>
                                        <span class="indicator-progress">Aguarde...
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
            <!--end::Modal - Update module-->
            <!--end::Modals-->
        </div>
        <!--end::Content container-->
    </div>
    <!--end::Content-->

</x-tenant-app-layout>

<!--begin::Custom Javascript(used for this page only)-->
<script src="/tenancy/assets/js/custom/apps/modules/list.js"></script>
<script src="/tenancy/assets/js/custom/apps/modules/add-module.js"></script>
<script src="/tenancy/assets/js/custom/apps/modules/update-module.js"></script>
<!--end::Custom Javascript-->
