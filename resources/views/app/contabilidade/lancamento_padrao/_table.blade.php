{{-- Em resources/views/app/contabilidade/lancamento_padrao/_table.blade.php --}}

<!--begin::Card-->
<div class="card card-flush">
    <!--begin::Card header-->
                <div class="card-header border-0 pt-6">
                    <!--begin::Card title-->
                    <div class="card-title">
                        <!--begin::Search-->
                        <div class="d-flex align-items-center position-relative my-1">
                            <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                            <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                <i class="bi bi-search"></i>
                            </span>
                            <!--end::Svg Icon-->
                            <input type="text" data-kt-lancamento-padrao-table-filter="search"
                                class="form-control form-control-solid w-250px ps-14"
                                placeholder="Pesquisar Lançamentos Padrão" />
                        </div>
                        <!--end::Search-->
                    </div>
                    <!--begin::Card title-->
                    <!--begin::Card toolbar-->
                    <div class="card-toolbar">
                        <!--begin::Toolbar-->
                        <div class="d-flex justify-content-end" data-kt-lancamento-padrao-table-toolbar="base">
                            <!--begin::Filter-->
                            <button type="button" class="btn btn-sm btn-light-primary me-3" data-kt-menu-trigger="click"
                                data-kt-menu-placement="bottom-end">
                                <!--begin::Svg Icon | path: icons/duotune/general/gen031.svg-->
                                <span class="svg-icon svg-icon-2">
                                    <i class="bi bi-funnel fs-3"></i>
                                </span>
                                <!--end::Svg Icon-->Filtro</button>
                            <!--begin::Menu 1-->
                            <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px" data-kt-menu="true">
                                <!--begin::Header-->
                                <div class="px-7 py-5">
                                    <div class="fs-5 text-dark fw-bold">Opções de Filtro</div>
                                </div>
                                <!--end::Header-->
                                <!--begin::Separator-->
                                <div class="separator border-gray-200"></div>
                                <!--end::Separator-->
                                <!--begin::Content-->
                                <div class="px-7 py-5" data-kt-lancamento-padrao-table-filter="form">
                                    <!--begin::Input group-->
                                    <div class="mb-10">
                                        <label class="form-label fs-6 fw-semibold">Tipo:</label>
                                        <select class="form-select form-select-solid fw-bold" data-kt-select2="true"
                                            data-placeholder="Selecione o tipo" data-allow-clear="true"
                                            data-kt-lancamento-padrao-table-filter="type" data-hide-search="true">
                                            <option></option>
                                            <option value="entrada">Entrada</option>
                                            <option value="saida">Saída</option>
                                            <option value="ambos">Ambos</option>
                                        </select>
                                    </div>
                                    <!--end::Input group-->
                                    <!--begin::Input group-->
                                    <div class="mb-10">
                                        <label class="form-label fs-6 fw-semibold">Categoria:</label>
                                        <select class="form-select form-select-solid fw-bold" data-kt-select2="true"
                                            data-placeholder="Selecione a categoria" data-allow-clear="true"
                                            data-kt-lancamento-padrao-table-filter="category" data-hide-search="true">
                                            <option></option>
                                            <option value="Administrativo">Administrativo</option>
                                            <option value="Alimentação">Alimentação</option>
                                            <option value="Cerimônias">Cerimônias</option>
                                            <option value="Comércio">Comércio</option>
                                            <option value="Coletas">Coletas</option>
                                            <option value="Comunicação">Comunicação</option>
                                            <option value="Contribuições">Contribuições</option>
                                            <option value="Doações">Doações</option>
                                            <option value="Educação">Educação</option>
                                            <option value="Equipamentos">Equipamentos</option>
                                            <option value="Eventos">Eventos</option>
                                            <option value="Intenções">Intenções</option>
                                            <option value="Liturgia">Liturgia</option>
                                            <option value="Manutenção">Manutenção</option>
                                            <option value="Material de escritório">Material de escritório</option>
                                            <option value="Pessoal">Pessoal</option>
                                            <option value="Rendimentos">Rendimentos</option>
                                            <option value="Saúde">Saúde</option>
                                            <option value="Serviços essenciais">Serviços essenciais</option>
                                            <option value="Suprimentos">Suprimentos</option>
                                            <option value="Financeiro">Financeiro</option>
                                            <option value="Transporte">Transporte</option>
                                            <option value="Telecomunicações">Telecomunicações</option>
                                        </select>
                                    </div>
                                    <!--end::Input group-->
                                    <!--begin::Actions-->
                                    <div class="d-flex justify-content-end">
                                        <button type="reset"
                                            class="btn btn-light btn-active-light-primary fw-semibold me-2 px-6"
                                            data-kt-menu-dismiss="true"
                                            data-kt-lancamento-padrao-table-filter="reset">Reset</button>
                                        <button type="submit" class="btn btn-primary fw-semibold px-6"
                                            data-kt-menu-dismiss="true"
                                            data-kt-lancamento-padrao-table-filter="filter">Aplicar</button>
                                    </div>
                                    <!--end::Actions-->
                                </div>
                                <!--end::Content-->
                            </div>
                            <!--end::Menu 1-->
                            <!--end::Filter-->
                            <!--begin::Export-->
                            <button type="button" class="btn btn-sm btn-light-primary me-3" data-bs-toggle="modal"
                                data-bs-target="#kt_subscriptions_export_modal">
                                <!--begin::Svg Icon | path: icons/duotune/arrows/arr078.svg-->
                                <span class="svg-icon svg-icon-2">
                                    <i class="bi bi-box-arrow-up fs-3"></i>
                                </span>
                                <!--end::Svg Icon-->Exportar</button>
                            <!--end::Export-->
                            <!--begin::Add lancamento padrao-->
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_lancamento_padrao">
                                <!--begin::Svg Icon | path: icons/duotune/arrows/arr075.svg-->
                                <span class="svg-icon svg-icon-2">
                                    <i class="bi bi-plus-circle fs-3"></i>
                                </span>
                                <!--end::Svg Icon-->Lançamento Padrão</a>
                            <!--end::Add lancamento padrao-->
                        </div>
                        <!--end::Toolbar-->
                        <!--begin::Group actions-->
                        <div class="d-flex justify-content-end align-items-center d-none"
                            data-kt-lancamento-padrao-table-toolbar="selected">
                            <div class="fw-bold me-5">
                                <span class="me-2"
                                    data-kt-lancamento-padrao-table-select="selected_count"></span>Selecionados
                            </div>
                            <button type="button" class="btn btn-danger"
                                data-kt-lancamento-padrao-table-select="delete_selected">Excluir Selecionados</button>
                        </div>
                        <!--end::Group actions-->
                    </div>
                    <!--end::Card toolbar-->
                </div>
                <!--end::Card header-->
                <!--begin::Card body-->
                <div class="card-body pt-0">
                    <!--begin::Table-->
                    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_lancamento_padrao_table">
                        <!--begin::Table head-->
                        <thead>
                            <!--begin::Table row-->
                            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                <th class="w-10px pe-2">
                                    <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                        <input class="form-check-input" type="checkbox" data-kt-check="true"
                                            data-kt-check-target="#kt_lancamento_padrao_table .form-check-input"
                                            value="1" />
                                    </div>
                                </th>
                                <th class="min-w-200px">Descrição</th>
                                <th class="min-w-100px">Tipo</th>
                                <th class="min-w-150px">Categoria</th>
                                <th class="min-w-200px">Conta Débito</th>
                                <th class="min-w-200px">Conta Crédito</th>
                                <th class="text-end min-w-70px">Ações</th>
                            </tr>
                            <!--end::Table row-->
                        </thead>
                        <!--end::Table head-->
                        <!--begin::Table body-->
                        <tbody class="text-gray-600 fw-semibold">
                            <!-- Dados serão carregados via AJAX -->
                        </tbody>
                        <!--end::Table body-->
                    </table>
                    <!--end::Table-->
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Card-->
            <!--begin::Modals-->
            <!--begin::Modal - Adjust Balance-->
            <div class="modal fade" id="kt_subscriptions_export_modal" tabindex="-1" aria-hidden="true">
                <!--begin::Modal dialog-->
                <div class="modal-dialog modal-dialog-centered mw-650px">
                    <!--begin::Modal content-->
                    <div class="modal-content">
                        <!--begin::Modal header-->
                        <div class="modal-header">
                            <!--begin::Modal title-->
                            <h2 class="fw-bold">Export Subscriptions</h2>
                            <!--end::Modal title-->
                            <!--begin::Close-->
                            <div id="kt_subscriptions_export_close"
                                class="btn btn-icon btn-sm btn-active-icon-primary">
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
                            <form id="kt_subscriptions_export_form" class="form" action="#">
                                <!--begin::Input group-->
                                <div class="fv-row mb-10">
                                    <!--begin::Label-->
                                    <label class="fs-5 fw-semibold form-label mb-5">Select Export Format:</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <select data-control="select2" data-placeholder="Select a format"
                                        data-hide-search="true" name="format" class="form-select form-select-solid">
                                        <option value="excell">Excel</option>
                                        <option value="pdf">PDF</option>
                                        <option value="cvs">CVS</option>
                                        <option value="zip">ZIP</option>
                                    </select>
                                    <!--end::Input-->
                                </div>
                                <!--end::Input group-->
                                <!--begin::Input group-->
                                <div class="fv-row mb-10">
                                    <!--begin::Label-->
                                    <label class="fs-5 fw-semibold form-label mb-5">Select Date Range:</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input class="form-control form-control-solid" placeholder="Pick a date"
                                        name="date" />
                                    <!--end::Input-->
                                </div>
                                <!--end::Input group-->
                                <!--begin::Row-->
                                <div class="row fv-row mb-15">
                                    <!--begin::Label-->
                                    <label class="fs-5 fw-semibold form-label mb-5">Payment Type:</label>
                                    <!--end::Label-->
                                    <!--begin::Radio group-->
                                    <div class="d-flex flex-column">
                                        <!--begin::Radio button-->
                                        <label
                                            class="form-check form-check-custom form-check-sm form-check-solid mb-3">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                checked="checked" name="payment_type" />
                                            <span class="form-check-label text-gray-600 fw-semibold">All</span>
                                        </label>
                                        <!--end::Radio button-->
                                        <!--begin::Radio button-->
                                        <label
                                            class="form-check form-check-custom form-check-sm form-check-solid mb-3">
                                            <input class="form-check-input" type="checkbox" value="2"
                                                checked="checked" name="payment_type" />
                                            <span class="form-check-label text-gray-600 fw-semibold">Visa</span>
                                        </label>
                                        <!--end::Radio button-->
                                        <!--begin::Radio button-->
                                        <label
                                            class="form-check form-check-custom form-check-sm form-check-solid mb-3">
                                            <input class="form-check-input" type="checkbox" value="3"
                                                name="payment_type" />
                                            <span class="form-check-label text-gray-600 fw-semibold">Mastercard</span>
                                        </label>
                                        <!--end::Radio button-->
                                        <!--begin::Radio button-->
                                        <label class="form-check form-check-custom form-check-sm form-check-solid">
                                            <input class="form-check-input" type="checkbox" value="4"
                                                name="payment_type" />
                                            <span class="form-check-label text-gray-600 fw-semibold">American
                                                Express</span>
                                        </label>
                                        <!--end::Radio button-->
                                    </div>
                                    <!--end::Input group-->
                                </div>
                                <!--end::Row-->
                                <!--begin::Actions-->
                                <div class="text-center">
                                    <button type="reset" id="kt_subscriptions_export_cancel"
                                        class="btn btn-light me-3">Discard</button>
                                    <button type="submit" id="kt_subscriptions_export_submit"
                                        class="btn btn-primary">
                                        <span class="indicator-label">Submit</span>
                                        <span class="indicator-progress">Please wait...
                                            <span
                                                class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
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
            <!--end::Modal - New Card-->
            <!--end::Modals-->
</div>
<!--end::Card-->

<script src="/assets/js/custom/apps/contabilidade/lancamento-padrao/list.js"></script>
