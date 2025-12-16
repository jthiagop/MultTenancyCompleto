<x-tenant-app-layout>

    <!--begin::Main-->
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <!--begin::Content wrapper-->
        <div class="d-flex flex-column flex-column-fluid">
            <!--begin::Toolbar-->
            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                <!--begin::Toolbar container-->
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <!--begin::Page title-->
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <!--begin::Title-->
                        <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                            Cadastro de Lançamento Padrão</h1>
                        <!--end::Title-->
                        <!--begin::Breadcrumb-->
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">
                                <a href="../../demo1/dist/index.html" class="text-muted text-hover-primary">Home</a>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-400 w-5px h-2px"></span>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">Financeiro</li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-400 w-5px h-2px"></span>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">Cadastro</li>
                            <!--end::Item-->
                        </ul>
                        <!--end::Breadcrumb-->
                    </div>
                    <!--end::Page title-->
                    <!--begin::Actions-->

                    <!--end::Actions-->
                </div>
                <!--end::Toolbar container-->
            </div>
            <!--end::Toolbar-->
            <!--begin::Content-->
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <!--begin::Content container-->
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <!--begin::Card-->
                    <div class="card">
                        <!--begin::Card header-->
                        <div class="card-header border-0 pt-6">
                            <!--begin::Card title-->
                            <div class="card-title">
                                <!--begin::Search-->
                                <div class="d-flex align-items-center position-relative my-1">
                                    <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                                    <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2"
                                                rx="1" transform="rotate(45 17.0365 15.1223)"
                                                fill="currentColor" />
                                            <path
                                                d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z"
                                                fill="currentColor" />
                                        </svg>
                                    </span>
                                    <!--end::Svg Icon-->
                                    <input type="text" data-kt-customer-table-filter="search"
                                        class="form-control form-control-solid w-250px ps-15"
                                        placeholder="Buscar Lançamento" />
                                </div>
                                <!--end::Search-->
                            </div>
                            <!--begin::Card title-->
                            <!--begin::Card toolbar-->
                            <div class="card-toolbar">
                                <!--begin::Toolbar-->
                                <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                                    <!--begin::Filter-->
                                    <div class="w-150px me-3">
                                        <!--begin::Select2-->
                                        <select class="form-select form-select-solid" data-control="select2"
                                            data-hide-search="true" data-placeholder="Status"
                                            data-kt-ecommerce-order-filter="status">
                                            <option></option>
                                            <option value="all">Todos</option>
                                            <option value="entrada">Entrada</option>
                                            <option value="saida">Saída</option>
                                        </select>
                                        <!--end::Select2-->
                                    </div>
                                    <!--end::Filter-->
                                    <!--begin::Export-->
                                    <button type="button" class="btn btn-light-primary me-3" data-bs-toggle="modal"
                                        data-bs-target="#kt_customers_export_modal">
                                        <!--begin::Svg Icon | path: icons/duotune/arrows/arr078.svg-->
                                        <span class="svg-icon svg-icon-2">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <rect opacity="0.3" x="12.75" y="4.25" width="12" height="2"
                                                    rx="1" transform="rotate(90 12.75 4.25)"
                                                    fill="currentColor" />
                                                <path
                                                    d="M12.0573 6.11875L13.5203 7.87435C13.9121 8.34457 14.6232 8.37683 15.056 7.94401C15.4457 7.5543 15.4641 6.92836 15.0979 6.51643L12.4974 3.59084C12.0996 3.14332 11.4004 3.14332 11.0026 3.59084L8.40206 6.51643C8.0359 6.92836 8.0543 7.5543 8.44401 7.94401C8.87683 8.37683 9.58785 8.34458 9.9797 7.87435L11.4427 6.11875C11.6026 5.92684 11.8974 5.92684 12.0573 6.11875Z"
                                                    fill="currentColor" />
                                                <path opacity="0.3"
                                                    d="M18.75 8.25H17.75C17.1977 8.25 16.75 8.69772 16.75 9.25C16.75 9.80228 17.1977 10.25 17.75 10.25C18.3023 10.25 18.75 10.6977 18.75 11.25V18.25C18.75 18.8023 18.3023 19.25 17.75 19.25H5.75C5.19772 19.25 4.75 18.8023 4.75 18.25V11.25C4.75 10.6977 5.19771 10.25 5.75 10.25C6.30229 10.25 6.75 9.80228 6.75 9.25C6.75 8.69772 6.30229 8.25 5.75 8.25H4.75C3.64543 8.25 2.75 9.14543 2.75 10.25V19.25C2.75 20.3546 3.64543 21.25 4.75 21.25H18.75C19.8546 21.25 20.75 20.3546 20.75 19.25V10.25C20.75 9.14543 19.8546 8.25 18.75 8.25Z"
                                                    fill="currentColor" />
                                            </svg>
                                        </span>
                                        <!--end::Svg Icon-->Exportar</button>
                                    <!--end::Export-->
                                    <!--begin::Add customer-->
                                    <a href="{{ route('lancamentoPadrao.create') }}" class="btn btn-primary">Cadastro
                                        de Lançamento</a>
                                    <!--end::Add customer-->
                                </div>
                                <!--end::Toolbar-->
                                <!--begin::Group actions-->
                                <div class="d-flex justify-content-end align-items-center d-none"
                                    data-kt-customer-table-toolbar="selected">
                                    <div class="fw-bold me-5">
                                        <span class="me-2"
                                            data-kt-customer-table-select="selected_count"></span>Selected
                                    </div>
                                    <button type="button" class="btn btn-danger"
                                        data-kt-customer-table-select="delete_selected">Delete Selected</button>
                                </div>
                                <!--end::Group actions-->
                            </div>
                            <!--end::Card toolbar-->
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            <!--begin::Table-->
                            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_customers_table">
                                <!--begin::Table head-->
                                <thead>
                                    <!--begin::Table row-->
                                    <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                        <th class="w-10px pe-2">
                                            <div
                                                class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                                <input class="form-check-input" type="checkbox" data-kt-check="true"
                                                    data-kt-check-target="#kt_customers_table .form-check-input"
                                                    value="1" />
                                            </div>
                                        </th>
                                        <th class="min-w-20px">id</th>
                                        <th class="min-w-500px">Drescrição</th>
                                        <th class="min-w-155px">Categoria</th>
                                        <th class="min-w-125px">Tipo</th>
                                        <th class="min-w-225px">Usuário</th>
                                        <th class="min-w-125px">data</th>
                                        <th class="text-end min-w-70px">Ação</th>
                                    </tr>
                                    <!--end::Table row-->
                                </thead>
                                <!--end::Table head-->
                                <!--begin::Table body-->
                                <tbody class="fw-semibold text-gray-600">
                                    @foreach ($lps as $lp)
                                        <tr>
                                            <!--begin::Checkbox-->
                                            <td>
                                                <div
                                                    class="form-check form-check-sm form-check-custom form-check-solid">
                                                    <input class="form-check-input" type="checkbox" value="1" />
                                                </div>
                                            </td>
                                            <!--end::Checkbox-->
                                            <td>{{ $lp->id }}</td>
                                            <!--begin::Name=-->
                                            <td>
                                                <a href="{{ route('lancamentoPadrao.edit', $lp->id) }}"
                                                    class="text-gray-800 text-hover-primary mb-1">{{ $lp->description }}</a>
                                            </td>
                                            <!--end::Name=-->
                                            <!--begin::Email=-->
                                            <td>
                                                <div
                                                    class=" badge {{ $categoryColors[$lp->category] ?? 'text-muted' }}">
                                                    {{ $lp->getCategoryEmoji() }} {{ $lp->category }}
                                                </div>
                                            </td>
                                            <!--end::Email=-->
                                            <!--begin::Status=-->
                                            <td>
                                                <!--begin::Badges-->
                                                <div
                                                    class="badge {{ $lp->type === 'entrada' ? 'badge-light-success' : ($lp->type === 'ambos' ? 'badge-light-primary' : 'badge-light-danger') }}">
                                                    {{ $lp->type === 'ambos' ? 'Ambos' : $lp->type }}
                                                </div>
                                                <!--end::Badges-->
                                            </td>
                                            <!--end::Status=-->
                                            <!--begin::IP Address=-->
                                            <td>{{ Str::limit($lp->user->name, 20) }}</td>
                                            <!--end::IP Address=-->
                                            <!--begin::Date=-->
                                            <td>{{ date(' d/m/Y', strtotime($lp->date)) }}</td>
                                            <!--end::Date=-->
                                            <!--begin::Action=-->
                                                <!-- Botão de edição com ícone -->
                                                <td class="text-end">
                                                    <!-- Link de edição com ícone de lápis -->
                                                    <a href="{{ route('lancamentoPadrao.edit', $lp->id) }}" class="btn btn-sm btn-light btn-active-light-primary">
                                                        <!-- Ícone de lápis -->
                                                        <span class="svg-icon svg-icon-5 m-0">
                                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M21.2799 6.40005L11.7399 15.94C10.7899 16.89 7.96987 17.33 7.33987 16.7C6.70987 16.07 7.13987 13.25 8.08987 12.3L17.6399 2.75002C17.8754 2.49308 18.1605 2.28654 18.4781 2.14284C18.7956 1.99914 19.139 1.92124 19.4875 1.9139C19.8359 1.90657 20.1823 1.96991 20.5056 2.10012C20.8289 2.23033 21.1225 2.42473 21.3686 2.67153C21.6147 2.91833 21.8083 3.21243 21.9379 3.53609C22.0674 3.85976 22.1299 4.20626 22.1217 4.55471C22.1134 4.90316 22.0346 5.24635 21.8904 5.5635C21.7462 5.88065 21.5395 6.16524 21.2824 6.40005H21.2799ZM15.6399 4.50002L2.28987 17.85C2.19987 17.94 2.11987 18.12 2.09987 18.25L1.63987 21.8C1.54987 22.39 2.02987 22.87 2.61987 22.78L6.15987 22.32C6.28987 22.3 6.45987 22.22 6.55987 22.12L19.9099 8.77002C20.6599 8.02002 21.0799 7.05002 20.9899 6.04002C20.9099 5.02002 20.2699 4.06002 19.2299 3.50002C18.1999 2.93002 17.1999 2.88002 16.2299 3.34002L15.6399 4.50002Z" fill="currentColor"/>
                                                                <path d="M5.88989 21.85C5.69989 21.66 5.50989 21.47 5.32989 21.28C5.14989 21.1 4.95989 20.91 4.76989 20.72C4.67989 20.63 4.59989 20.54 4.51989 20.45C4.43989 20.36 4.32989 20.22 4.21989 20.09C4.16989 20.03 4.08989 19.94 4.01989 19.86C3.93989 19.77 3.83989 19.64 3.74989 19.53C3.70989 19.49 3.65989 19.42 3.60989 19.37C3.55989 19.32 3.49989 19.27 3.44989 19.22L8.08989 14.58C8.47989 14.19 8.83989 13.85 9.16989 13.53C10.1199 12.58 11.5599 12.58 12.5099 13.53C13.4599 14.48 13.4599 15.92 12.5099 16.87C12.1899 17.19 11.8499 17.56 11.4699 17.94L5.88989 21.85Z" fill="currentColor"/>
                                                            </svg>
                                                        </span>
                                                    </a>
                                            </td>
                                            <!--end::Action=-->
                                        </tr>
                                    @endforeach

                                </tbody>
                                <!--end::Table body-->
                            </table>
                            <!--end::Table-->
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Card-->
                    <!--begin::Modals-->
                    <!--begin::Modal - Customers - Add-->
                    <div class="modal fade" id="kt_modal_add_customer" tabindex="-1" aria-hidden="true">
                        <!--begin::Modal dialog-->
                        <div class="modal-dialog modal-dialog-centered mw-650px">
                            <!--begin::Modal content-->
                            <div class="modal-content">
                                <!--begin::Form-->
                                <form class="form" method="POST" action="{{ route('lancamentoPadrao.store') }}"
                                    id="kt_modal_add_customer_form">
                                    @csrf
                                    @method('POST') <!-- Ou PUT, DELETE para edição e exclusão -->
                                    <input type="hidden" name="id" id="lancamento_padrao_id">

                                    <!--begin::Modal header-->
                                    <div class="modal-header" id="kt_modal_add_customer_header">
                                        <!--begin::Modal title-->
                                        <h2 class="fw-bold">Add Lançamento Padrão</h2>
                                        <!--end::Modal title-->
                                        <!--begin::Close-->
                                        <div id="kt_modal_add_customer_close"
                                            class="btn btn-icon btn-sm btn-active-icon-primary">
                                            <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
                                            <span class="svg-icon svg-icon-1">
                                                <svg width="24" height="24" viewBox="0 0 24 24"
                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <rect opacity="0.5" x="6" y="17.3137" width="16"
                                                        height="2" rx="1"
                                                        transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                                    <rect x="7.41422" y="6" width="16" height="2"
                                                        rx="1" transform="rotate(45 7.41422 6)"
                                                        fill="currentColor" />
                                                </svg>
                                            </span>
                                            <!--end::Svg Icon-->
                                        </div>
                                        <!--end::Close-->
                                    </div>
                                    <!--end::Modal header-->
                                    <!--begin::Modal body-->
                                    <div class="modal-body py-10 px-lg-17">
                                        <!--begin::Scroll-->
                                        <div class="scroll-y me-n7 pe-7" id="kt_modal_add_customer_scroll"
                                            data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}"
                                            data-kt-scroll-max-height="auto"
                                            data-kt-scroll-dependencies="#kt_modal_add_customer_header"
                                            data-kt-scroll-wrappers="#kt_modal_add_customer_scroll"
                                            data-kt-scroll-offset="300px">

                                            <!--begin::Input group-->
                                            <div class="fv-row mb-7">
                                                <!--begin::Label-->
                                                <label class="required fs-6 fw-semibold mb-2">Tipo</label>
                                                <!--end::Label-->
                                                <!--begin::Input-->
                                                <select class="form-select form-select-solid" name="type"
                                                    data-control="select2" data-placeholder="Selecione o tipo">
                                                    <option></option>
                                                    <option value="entrada">Entrada</option>
                                                    <option value="saida">Saída</option>
                                                </select>
                                                <!--end::Input-->
                                            </div>
                                            <!--end::Input group-->

                                            <!--begin::Input group-->
                                            <div class="fv-row mb-15">
                                                <!--begin::Label-->
                                                <label class="required fs-6 fw-semibold mb-2">Descrição</label>
                                                <!--end::Label-->
                                                <!--begin::Input-->
                                                <div class="d-flex flex-column mb-8">
                                                    <textarea class="form-control form-control-solid" rows="3" name="description"
                                                        placeholder="Digite a descrição"></textarea>
                                                </div>
                                                <!--end::Input-->
                                            </div>
                                            <!--end::Input group-->

                                            <!--begin::Input group-->
                                            <div class="fv-row mb-7">
                                                <!--begin::Label-->
                                                <label class="required fs-6 fw-semibold mb-2">Data</label>
                                                <!--end::Label-->
                                                <!--begin::Input-->
                                                <input type="date" class="form-control form-control-solid"
                                                    name="date" value="" />
                                                <!--end::Input-->
                                            </div>
                                            <!--end::Input group-->

                                            <!--begin::Input group-->
                                            <div class="fv-row mb-7">
                                                <!--begin::Label-->
                                                <label class="required fs-6 fw-semibold mb-2">Categoria</label>
                                                <!--end::Label-->
                                                <!--begin::Input-->
                                                <select class="form-select form-select-solid" name="category"
                                                    data-control="select2" data-placeholder="Selecione uma categoria">
                                                    <option value="">Selecione uma categoria...</option>
                                                    <!-- Adicione as opções de categorias aqui -->
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
                                                    <option value="Material de escritório">Material de escritório
                                                    </option>
                                                    <option value="Pessoal">Pessoal</option>
                                                    <option value="Rendimentos">Rendimentos</option>
                                                    <option value="Saúde">Saúde</option>
                                                    <option value="Serviços essenciais">Serviços essenciais</option>
                                                    <option value="Suprimentos">Suprimentos</option>
                                                    <option value="Financeiro">Financeiro</option>
                                                    <option value="Transporte">Transporte</option>
                                                    <!-- ... -->
                                                </select>
                                                <!--end::Input-->
                                            </div>
                                            <!--end::Input group-->

                                        </div>
                                        <!--end::Scroll-->
                                    </div>
                                    <!--end::Modal body-->

                                    <!--begin::Modal footer-->
                                    <div class="modal-footer flex-center">
                                        <!--begin::Button-->
                                        <button type="reset" id="kt_modal_add_customer_cancel"
                                            class="btn btn-light me-3">Sair</button>
                                        <!--end::Button-->
                                        <!--begin::Button-->
                                        <button type="submit" id="kt_modal_add_customer_submit"
                                            class="btn btn-primary">
                                            <span class="indicator-label">Salvar</span>
                                            <span class="indicator-progress">Por favor, aguarde...
                                                <span
                                                    class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                        </button>
                                        <!--end::Button-->
                                    </div>
                                    <!--end::Modal footer-->
                                </form>
                                <!--end::Form-->
                            </div>
                        </div>
                    </div>
                    <!--end::Modal - Customers - Add-->

                    <!--begin::Modal - Customers - Edit-->
                    <!-- Modal de Edição -->
                    <div class="modal fade" id="kt_modal_edit_customer" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered mw-650px">
                            <div class="modal-content">
                                <form class="form" method="POST" id="kt_modal_edit_customer_form">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="id" id="lancamento_padrao_id">

                                    <div class="modal-header" id="kt_modal_edit_customer_header">
                                        <h2 class="fw-bold">Editar Lançamento Padrão</h2>
                                        <div id="kt_modal_edit_customer_close"
                                            class="btn btn-icon btn-sm btn-active-icon-primary">
                                            <span class="svg-icon svg-icon-1">
                                                <svg width="24" height="24" viewBox="0 0 24 24"
                                                    fill="none">
                                                    <rect opacity="0.5" x="6" y="17.3137" width="16"
                                                        height="2" rx="1"
                                                        transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                                    <rect x="7.41422" y="6" width="16" height="2"
                                                        rx="1" transform="rotate(45 7.41422 6)"
                                                        fill="currentColor" />
                                                </svg>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="modal-body py-10 px-lg-17">
                                        <div class="fv-row mb-7">
                                            <label class="required fs-6 fw-semibold mb-2">Tipo</label>
                                            <select class="form-select form-select-solid" name="type"
                                                data-control="select2">
                                                <option value="entrada">Entrada</option>
                                                <option value="saida">Saída</option>
                                            </select>
                                        </div>
                                        <div class="fv-row mb-15">
                                            <label class="required fs-6 fw-semibold mb-2">Descrição</label>
                                            <textarea class="form-control form-control-solid" rows="3" name="description"></textarea>
                                        </div>
                                        <div class="fv-row mb-7">
                                            <label class="required fs-6 fw-semibold mb-2">Data</label>
                                            <input type="date" class="form-control form-control-solid"
                                                name="date" />
                                        </div>
                                        <div class="fv-row mb-7">
                                            <label class="required fs-6 fw-semibold mb-2">Categoria</label>
                                            <select class="form-select form-select-solid" name="category"
                                                data-control="select2">
                                                <!-- Adicione as opções de categoria -->
                                            </select>
                                        </div>
                                    </div>

                                    <div class="modal-footer flex-center">
                                        <button type="reset" id="kt_modal_edit_customer_cancel"
                                            class="btn btn-light me-3">Cancelar</button>
                                        <button type="submit" id="kt_modal_edit_customer_submit"
                                            class="btn btn-primary">
                                            <span class="indicator-label">Atualizar</span>
                                            <span class="indicator-progress">Por favor, aguarde...
                                                <span
                                                    class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                            </span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <script>
                        $(document).ready(function() {
                            // Inicializar Select2
                            $('[data-control="select2"]').select2();

                            // Função para abrir o modal de edição
                            function openModalForEditing(lancamentoPadrao) {
                                // Preencher os campos do modal com os dados do lançamento existente
                                $('#lancamento_padrao_id').val(lancamentoPadrao.id);
                                $('[name="type"]').val(lancamentoPadrao.type).trigger('change');
                                $('[name="description"]').val(lancamentoPadrao.description);
                                $('[name="date"]').val(lancamentoPadrao.date);
                                $('[name="category"]').val(lancamentoPadrao.category).trigger('change');

                                // Atualizar a ação do formulário para usar o método PUT e a rota de update
                                $('#kt_modal_edit_customer_form').attr('action', `/lancamentoPadrao/${lancamentoPadrao.id}`);
                                $('#kt_modal_edit_customer_form').find('input[name="_method"]').val('PUT');

                                // Abrir o modal
                                $('#kt_modal_edit_customer').modal('show');
                            }

                            // Listener para abrir o modal de edição
                            $('.edit-button').on('click', function() {
                                var lancamentoPadrao = $(this).data(
                                'lancamentopadrao'); // Supondo que você passe os dados do lançamento via data-attributes
                                openModalForEditing(lancamentoPadrao);
                            });

                            // Função para abrir o modal de criação
                            function openModalForCreating() {
                                // Limpar os campos do modal
                                $('#kt_modal_edit_customer_form').trigger('reset');
                                $('#lancamento_padrao_id').val('');

                                // Atualizar a ação do formulário para usar o método POST e a rota de store
                                $('#kt_modal_edit_customer_form').attr('action', `{{ route('lancamentoPadrao.store') }}`);
                                $('#kt_modal_edit_customer_form').find('input[name="_method"]').val('POST');

                                // Abrir o modal
                                $('#kt_modal_edit_customer').modal('show');
                            }
                        });
                    </script>


                    <!--end::Modal - Customers - Edit-->

                    <!--begin::Modal - Adjust Balance-->
                    <div class="modal fade" id="kt_customers_export_modal" tabindex="-1" aria-hidden="true">
                        <!--begin::Modal dialog-->
                        <div class="modal-dialog modal-dialog-centered mw-650px">
                            <!--begin::Modal content-->
                            <div class="modal-content">
                                <!--begin::Modal header-->
                                <div class="modal-header">
                                    <!--begin::Modal title-->
                                    <h2 class="fw-bold">Export Customers</h2>
                                    <!--end::Modal title-->
                                    <!--begin::Close-->
                                    <div id="kt_customers_export_close"
                                        class="btn btn-icon btn-sm btn-active-icon-primary">
                                        <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
                                        <span class="svg-icon svg-icon-1">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2"
                                                    rx="1" transform="rotate(-45 6 17.3137)"
                                                    fill="currentColor" />
                                                <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                                    transform="rotate(45 7.41422 6)" fill="currentColor" />
                                            </svg>
                                        </span>
                                        <!--end::Svg Icon-->
                                    </div>
                                    <!--end::Close-->
                                </div>
                                <!--end::Modal header-->
                                <!--begin::Modal body-->
                                <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                                    <!--begin::Form-->
                                    <form id="kt_customers_export_form" class="form" action="#">
                                        <!--begin::Input group-->
                                        <div class="fv-row mb-10">
                                            <!--begin::Label-->
                                            <label class="fs-5 fw-semibold form-label mb-5">Selecione formato:</label>
                                            <!--end::Label-->
                                            <!--begin::Input-->
                                            <select data-control="select2" data-placeholder="Select a format"
                                                data-hide-search="true" name="format"
                                                class="form-select form-select-solid">
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
                                                    <span
                                                        class="form-check-label text-gray-600 fw-semibold">Visa</span>
                                                </label>
                                                <!--end::Radio button-->
                                                <!--begin::Radio button-->
                                                <label
                                                    class="form-check form-check-custom form-check-sm form-check-solid mb-3">
                                                    <input class="form-check-input" type="checkbox" value="3"
                                                        name="payment_type" />
                                                    <span
                                                        class="form-check-label text-gray-600 fw-semibold">Mastercard</span>
                                                </label>
                                                <!--end::Radio button-->
                                                <!--begin::Radio button-->
                                                <label
                                                    class="form-check form-check-custom form-check-sm form-check-solid">
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
                                            <button type="reset" id="kt_customers_export_cancel"
                                                class="btn btn-light me-3">Discard</button>
                                            <button type="submit" id="kt_customers_export_submit"
                                                class="btn btn-primary">
                                                <span class="indicator-label">Submit</span>
                                                <span class="indicator-progress">Por favor, aguarde...
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
                <!--end::Content container-->
            </div>
            <!--end::Content-->
        </div>
        <!--end::Content wrapper-->
    </div>
    <!--end:::Main-->


</x-tenant-app-layout>



<!--begin::Vendors Javascript(used for this page only)-->
<script src="assets/plugins/custom/datatables/datatables.bundle.js"></script>
<!--end::Vendors Javascript-->
<!--begin::Custom Javascript(used for this page only)-->
<script src="assets/js/custom/apps/ecommerce/customers/listing/listing.js"></script>
<script src="assets/js/custom/apps/cadastros/lancamentoPadrao/add.js"></script>
<script src="assets/js/custom/apps/cadastros/lancamentoPadrao/edit.js"></script>
<script src="assets/js/custom/apps/ecommerce/customers/listing/export.js"></script>
<script src="assets/js/widgets.bundle.js"></script>
<script src="assets/js/custom/apps/chat/chat.js"></script>
<script src="assets/js/custom/utilities/modals/upgrade-plan.js"></script>
<script src="assets/js/custom/utilities/modals/create-campaign.js"></script>
<script src="assets/js/custom/utilities/modals/users-search.js"></script>
<!--end::Custom Javascript-->
<!--end::Javascript-->
