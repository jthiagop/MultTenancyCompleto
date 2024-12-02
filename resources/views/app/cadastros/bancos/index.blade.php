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
                            Adicionar Bancos</h1>
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
                            <li class="breadcrumb-item text-muted">Cadastros</li>
                            <!--end::Item-->
                        </ul>
                        <!--end::Breadcrumb-->
                    </div>
                    <!--end::Page title-->
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
                                        placeholder="Buscar Bancos" />
                                </div>
                                <!--end::Search-->
                            </div>
                            <!--begin::Card title-->
                            <!--begin::Card toolbar-->
                            <div class="card-toolbar">
                                <!--begin::Toolbar-->
                                <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                                    <!--begin::Filter-->
                                    <button type="button" class="btn btn-light-primary me-3"
                                        data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                        <!--begin::Svg Icon | path: icons/duotune/general/gen031.svg-->
                                        <span class="svg-icon svg-icon-2">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z"
                                                    fill="currentColor" />
                                            </svg>
                                        </span>
                                        <!--end::Svg Icon-->Filter</button>
                                    <!--begin::Menu 1-->
                                    <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px" data-kt-menu="true"
                                        id="kt-toolbar-filter">
                                        <!--begin::Header-->
                                        <div class="px-7 py-5">
                                            <div class="fs-4 text-dark fw-bold">Filter Options</div>
                                        </div>
                                        <!--end::Header-->
                                        <!--begin::Separator-->
                                        <div class="separator border-gray-200"></div>
                                        <!--end::Separator-->
                                        <!--begin::Content-->
                                        <div class="px-7 py-5">
                                            <!--begin::Input group-->
                                            <div class="mb-10">
                                                <!--begin::Label-->
                                                <label class="form-label fs-5 fw-semibold mb-3">Month:</label>
                                                <!--end::Label-->
                                                <!--begin::Input-->
                                                <select class="form-select form-select-solid fw-bold"
                                                    data-kt-select2="true" data-placeholder="Select option"
                                                    data-allow-clear="true" data-kt-customer-table-filter="month"
                                                    data-dropdown-parent="#kt-toolbar-filter">
                                                    <option></option>
                                                    <option value="aug">August</option>
                                                    <option value="sep">September</option>
                                                    <option value="oct">October</option>
                                                    <option value="nov">November</option>
                                                    <option value="dec">December</option>
                                                </select>
                                                <!--end::Input-->
                                            </div>
                                            <!--end::Input group-->
                                            <!--begin::Input group-->
                                            <div class="mb-10">
                                                <!--begin::Label-->
                                                <label class="form-label fs-5 fw-semibold mb-3">Payment Type:</label>
                                                <!--end::Label-->
                                                <!--begin::Options-->
                                                <div class="d-flex flex-column flex-wrap fw-semibold"
                                                    data-kt-customer-table-filter="payment_type">
                                                    <!--begin::Option-->
                                                    <label
                                                        class="form-check form-check-sm form-check-custom form-check-solid mb-3 me-5">
                                                        <input class="form-check-input" type="radio"
                                                            name="payment_type" value="all" checked="checked" />
                                                        <span class="form-check-label text-gray-600">All</span>
                                                    </label>
                                                    <!--end::Option-->
                                                    <!--begin::Option-->
                                                    <label
                                                        class="form-check form-check-sm form-check-custom form-check-solid mb-3 me-5">
                                                        <input class="form-check-input" type="radio"
                                                            name="payment_type" value="visa" />
                                                        <span class="form-check-label text-gray-600">Visa</span>
                                                    </label>
                                                    <!--end::Option-->
                                                    <!--begin::Option-->
                                                    <label
                                                        class="form-check form-check-sm form-check-custom form-check-solid mb-3">
                                                        <input class="form-check-input" type="radio"
                                                            name="payment_type" value="mastercard" />
                                                        <span class="form-check-label text-gray-600">Mastercard</span>
                                                    </label>
                                                    <!--end::Option-->
                                                    <!--begin::Option-->
                                                    <label
                                                        class="form-check form-check-sm form-check-custom form-check-solid">
                                                        <input class="form-check-input" type="radio"
                                                            name="payment_type" value="american_express" />
                                                        <span class="form-check-label text-gray-600">American
                                                            Express</span>
                                                    </label>
                                                    <!--end::Option-->
                                                </div>
                                                <!--end::Options-->
                                            </div>
                                            <!--end::Input group-->
                                            <!--begin::Actions-->
                                            <div class="d-flex justify-content-end">
                                                <button type="reset"
                                                    class="btn btn-light btn-active-light-primary me-2"
                                                    data-kt-menu-dismiss="true"
                                                    data-kt-customer-table-filter="reset">Reset</button>
                                                <button type="submit" class="btn btn-primary"
                                                    data-kt-menu-dismiss="true"
                                                    data-kt-customer-table-filter="filter">Apply</button>
                                            </div>
                                            <!--end::Actions-->
                                        </div>
                                        <!--end::Content-->
                                    </div>
                                    <!--end::Menu 1-->
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
                                        <!--end::Svg Icon-->Export</button>
                                    <!--end::Export-->
                                    <!--begin::Add customer-->
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#kt_modal_add_customer">Add Banco</button>
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
                                        data-kt-customer-table-select="delete_selected">Delete Seleção</button>
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
                                        <th class="min-w-200px">Nome do banco</th>
                                        <th class="min-w-75px">Agência</th>
                                        <th class="min-w-75px">Conta</th>
                                        <th class="min-w-25px">Digito</th>
                                        <th class="min-w-100px">criado pro</th>
                                        <th class="min-w-200px">Descrição</th>
                                        <th class="text-end min-w-70px">Ações</th>
                                    </tr>
                                    <!--end::Table row-->
                                </thead>
                                <!--end::Table head-->
                                <!--begin::Table body-->
                                <tbody class="fw-semibold text-gray-600">
                                    @foreach ($bancos as $banco)
                                        <tr>
                                            <!--begin::Checkbox-->
                                            <td>
                                                <div
                                                    class="form-check form-check-sm form-check-custom form-check-solid">
                                                    <input class="form-check-input" type="checkbox" value="1" />
                                                </div>
                                            </td>
                                            <!--end::Checkbox-->
                                            <!--begin::Name=-->
                                            <td data-banco-code="{{ $banco->banco }}">
                                                <!-- SVG será inserido dinamicamente pelo script -->
                                                <img src="" class="w-25px me-3" alt="Banco Logo" />
                                                <span class="">{{ $banco->banco }}</span>
                                            </td>
                                            <!--end::Name=-->
                                            <!--begin::Email=-->
                                            <td>
                                                <a href="#"
                                                    class="text-gray-600 text-hover-primary mb-1">{{ $banco->agencia }}</a>
                                            </td>
                                            <!--end::Email=-->
                                            <!--begin::Company=-->
                                            <td>{{ $banco->banco }}</td>
                                            <!--end::Company=-->
                                            <!--begin::Payment method=-->
                                            <td>{{ $banco->digito }} </td>
                                            <!--end::Payment method=-->
                                            <!--begin::Date=-->
                                            <td>{{ $banco->user->name }}</td>
                                            <!--end::Date=-->
                                            <!--end::Payment method=-->
                                            <!--begin::Date=-->
                                            <td>{{ $banco->description }}</td>
                                            <!--end::Date=-->
                                            <!--begin::Action=-->
                                            <td class="text-end">
                                                <a href="#"
                                                    class="btn btn-sm btn-light btn-active-light-primary"
                                                    data-kt-menu-trigger="click"
                                                    data-kt-menu-placement="bottom-end">Ações
                                                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr072.svg-->
                                                    <span class="svg-icon svg-icon-5 m-0">
                                                        <svg width="24" height="24" viewBox="0 0 24 24"
                                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z"
                                                                fill="currentColor" />
                                                        </svg>
                                                    </span>
                                                    <!--end::Svg Icon--></a>
                                                <!--begin::Menu-->
                                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4"
                                                    data-kt-menu="true">
                                                    <!--begin::Menu item-->
                                                    <div class="menu-item px-3">
                                                        <a href="#"
                                                            onclick="openEditModal({{ $banco->toJson() }})"
                                                            data-kt-menu-trigger="click"
                                                            data-kt-menu-placement="bottom-end"
                                                            class="menu-link px-3">Editar</a>
                                                    </div>
                                                    <!--end::Menu item-->
                                                    <!--begin::Menu item-->
                                                    <div class="menu-item px-3">
                                                        <a href="#" class="menu-link px-3"
                                                            data-kt-customer-table-filter="delete_row"
                                                            data-id="{{ $banco->id }}">Excluir</a>
                                                    </div>
                                                    <!--end::Menu item-->

                                                </div>
                                                <!--end::Menu-->
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
                    <!--begin::Modal - Customers - Add/Update-->
                    <div class="modal fade" id="kt_modal_add_customer" tabindex="-1" aria-hidden="true">
                        <!--begin::Modal dialog-->
                        <div class="modal-dialog modal-dialog-centered mw-650px">
                            <!--begin::Modal content-->
                            <div class="modal-content">
                                <!--begin::Form-->
                                <form class="form" method="POST" id="kt_modal_form"
                                    action="{{ route('cadastroBancos.store') }}">
                                    @csrf
                                    @method('POST') <!-- Ou PUT, DELETE para edição e exclusão -->

                                    <input type="hidden" name="_method" id="method_field" value="POST">
                                    <input type="hidden" name="banco_id" id="banco_id" value="">

                                    <!--begin::Modal header-->
                                    <div class="modal-header" id="kt_modal_add_customer_header">
                                        <!--begin::Modal title-->
                                        <h2 class="fw-bold" id="modal-title">Add Banco</h2>
                                        <!--end::Modal title-->
                                        <!--begin::Close-->
                                        <div data-bs-dismiss="modal"
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
                                        <!-- Exibir Erros de Validação -->
                                        @if ($errors->any())
                                            <div class="alert alert-danger">
                                                <ul>
                                                    @foreach ($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                        <!--begin::Scroll-->
                                        <div class="scroll-y me-n7 pe-7" id="kt_modal_add_customer_scroll"
                                            data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}"
                                            data-kt-scroll-max-height="auto"
                                            data-kt-scroll-dependencies="#kt_modal_add_customer_header"
                                            data-kt-scroll-wrappers="#kt_modal_add_customer_scroll"
                                            data-kt-scroll-offset="300px">
                                            <!--begin::Input group-->
                                            <div class="d-flex flex-column mb-7 fv-row">
                                                <!--begin::Label-->
                                                <label class="fs-6 fw-semibold mb-2">
                                                    <span class="required">Qual o Banco?</span>
                                                    <i class="fas fa-exclamation-circle ms-1 fs-7"
                                                        data-bs-toggle="tooltip" title="Country of origination"></i>
                                                </label>
                                                <!--end::Label-->
                                                <!--begin::Input-->
                                                <select id="bank_select" name="banco"
                                                    aria-label="Selecione seu banco" data-control="select2"
                                                    data-placeholder="Selecione seu banco..."
                                                    data-dropdown-parent="#kt_modal_add_customer"
                                                    class="form-select fw-bold">
                                                    <option value=""></option>
                                                    <option value="Banco do Brasil S.A" data-logo="brasil.svg"
                                                        data-name="Banco do Brasil S.A">Banco do Brasil S.A</option>
                                                    <option value="Banco Santander Brasil S.A"
                                                        data-logo="santander.svg"
                                                        data-name="Banco Santander Brasil S.A">Banco Santander Brasil
                                                        S.A</option>
                                                    <option value="Bradesco S.A" data-logo="bradesco.svg"
                                                        data-name="Bradesco S.A">Bradesco S.A</option>
                                                    <option value="Caixa Econômica Federal" data-logo="caixa.svg"
                                                        data-name="Caixa Econômica Federal">Caixa Econômica Federal
                                                    </option>
                                                    <option value="Itaú Unibanco S.A" data-logo="itau.svg"
                                                        data-name="Itaú Unibanco S.A">Itaú Unibanco S.A</option>
                                                    <option value="Lets Bank S.A" data-logo="lets-bank.svg"
                                                        data-name="Lets Bank S.A">Lets Bank S.A</option>
                                                    <option value="Mercado Pago" data-logo="mercadopago.svg"
                                                        data-name="Mercado Pago">Mercado Pago</option>
                                                    <option value="Nu Pagamentos S.A (Nubank)" data-logo="nubank.svg"
                                                        data-name="Nu Pagamentos S.A (Nubank)">Nu Pagamentos S.A
                                                        (Nubank)</option>
                                                    <option value="Unicred" data-logo="unicred.svg"
                                                        data-name="Unicred">Unicred</option>
                                                    <option value="PagSeguro Internet S.A" data-logo="pagseguro.svg"
                                                        data-name="PagSeguro Internet S.A">PagSeguro Internet S.A
                                                    </option>
                                                    <option value="Sicredi" data-logo="sicredi.svg"
                                                        data-name="Sicredi">Sicredi</option>
                                                    <option value="Stone Pagamentos S.A" data-logo="stone.svg"
                                                        data-name="Stone Pagamentos S.A">Stone Pagamentos S.A</option>
                                                    <option value="Ailos" data-logo="ailos.svg" data-name="Ailos">
                                                        Ailos</option>
                                                    <option value="Sicoob" data-logo="sicoob.svg" data-name="Sicoob">
                                                        Sicoob</option>
                                                    <option value="Quality Digital Bank - temporária"
                                                        data-logo="quality-digital-bank.svg"
                                                        data-name="Quality Digital Bank - temporária">Quality Digital
                                                        Bank - temporária</option>
                                                    <option value="Asaas IP S.A" data-logo="asaas.svg"
                                                        data-name="Asaas IP S.A">Asaas IP S.A</option>
                                                    <option value="BRB - Banco de Brasília" data-logo="brb.svg"
                                                        data-name="BRB - Banco de Brasília">BRB - Banco de Brasília
                                                    </option>
                                                    <option value="Banco BS2 S.A" data-logo="banco-bs2.svg"
                                                        data-name="Banco BS2 S.A">Banco BS2 S.A</option>
                                                    <option value="Banco BTG Pactual" data-logo="btg-pactual.svg"
                                                        data-name="Banco BTG Pactual">Banco BTG Pactual</option>
                                                    <option value="Banco C6 S.A" data-logo="banco-c6.svg"
                                                        data-name="Banco C6 S.A">Banco C6 S.A</option>
                                                    <option value="Banco Daycoval" data-logo="banco-daycoval.svg"
                                                        data-name="Banco Daycoval">Banco Daycoval</option>
                                                    <option value="Banco Industrial do Brasil S.A"
                                                        data-logo="banco-industrial-do-brasil.svg"
                                                        data-name="Banco Industrial do Brasil S.A">Banco Industrial do
                                                        Brasil S.A</option>
                                                    <option value="Banco Inter S.A" data-logo="banco-inter.svg"
                                                        data-name="Banco Inter S.A">Banco Inter S.A</option>
                                                    <option value="Banco Mercantil do Brasil S.A"
                                                        data-logo="banco-mercantil.svg"
                                                        data-name="Banco Mercantil do Brasil S.A">Banco Mercantil do
                                                        Brasil S.A</option>
                                                    <option value="Banco Original S.A" data-logo="banco-original.svg"
                                                        data-name="Banco Original S.A">Banco Original S.A</option>
                                                    <option value="Banco Pine" data-logo="banco-pine.svg"
                                                        data-name="Banco Pine">Banco Pine</option>
                                                    <option value="Banco Rendimento" data-logo="banco-rendimento.svg"
                                                        data-name="Banco Rendimento">Banco Rendimento</option>
                                                    <option value="Banco Safra S.A" data-logo="banco-safra.svg"
                                                        data-name="Banco Safra S.A">Banco Safra S.A</option>
                                                    <option value="Banco Sofisa" data-logo="banco-sofisa.svg"
                                                        data-name="Banco Sofisa">Banco Sofisa</option>
                                                    <option value="Banco Topazio" data-logo="banco-topazio.svg"
                                                        data-name="Banco Topazio">Banco Topazio</option>
                                                    <option value="Banco Triângulo - Tribanco"
                                                        data-logo="banco-triangulo.svg"
                                                        data-name="Banco Triângulo - Tribanco">Banco Triângulo -
                                                        Tribanco</option>
                                                    <option value="ABC Brasil" data-logo="abc-brasil.svg"
                                                        data-name="ABC Brasil">ABC Brasil</option>
                                                    <option value="Banco da Amazônia S.A"
                                                        data-logo="banco-da-amazonia.svg"
                                                        data-name="Banco da Amazônia S.A">Banco da Amazônia S.A
                                                    </option>
                                                    <option value="Banco do Estado do Espírito Santo"
                                                        data-logo="banco-estado-espirito-santo.svg"
                                                        data-name="Banco do Estado do Espírito Santo">Banco do Estado
                                                        do Espírito Santo</option>
                                                    <option value="Banco do Estado do Pará"
                                                        data-logo="banco-estado-para.svg"
                                                        data-name="Banco do Estado do Pará">Banco do Estado do Pará
                                                    </option>
                                                    <option value="Banco do Estado do Sergipe"
                                                        data-logo="banco-estado-sergipe.svg"
                                                        data-name="Banco do Estado do Sergipe">Banco do Estado do
                                                        Sergipe</option>
                                                    <option value="Banco do Nordeste do Brasil S.A"
                                                        data-logo="banco-nordeste.svg"
                                                        data-name="Banco do Nordeste do Brasil S.A">Banco do Nordeste
                                                        do Brasil S.A</option>
                                                    <option value="Bancos Escuros" data-logo="bancos-escuros.svg"
                                                        data-name="Bancos Escuros">Bancos Escuros</option>
                                                    <option value="Bank of America" data-logo="bank-of-america.svg"
                                                        data-name="Bank of America">Bank of America</option>
                                                    <option value="Banrisul" data-logo="banrisul.svg"
                                                        data-name="Banrisul">Banrisul</option>
                                                    <option value="Capitual" data-logo="capitual.svg"
                                                        data-name="Capitual">Capitual</option>
                                                    <option value="Conta Simples Soluções em Pagamentos"
                                                        data-logo="conta-simples.svg"
                                                        data-name="Conta Simples Soluções em Pagamentos">Conta Simples
                                                        Soluções em Pagamentos</option>
                                                    <option value="Cora Sociedade Crédito Direto S.A"
                                                        data-logo="cora-credito.svg"
                                                        data-name="Cora Sociedade Crédito Direto S.A">Cora Sociedade
                                                        Crédito Direto S.A</option>
                                                    <option value="Credisis" data-logo="credisis.svg"
                                                        data-name="Credisis">Credisis</option>
                                                    <option value="Cresol" data-logo="cresol.svg" data-name="Cresol">
                                                        Cresol</option>
                                                    <option value="Efí - Gerencianet" data-logo="efi-gerencianet.svg"
                                                        data-name="Efí - Gerencianet">Efí - Gerencianet</option>
                                                    <option value="Grafeno" data-logo="grafeno.svg"
                                                        data-name="Grafeno">Grafeno</option>
                                                    <option value="Omie.Cash" data-logo="omie-cash.svg"
                                                        data-name="Omie.Cash">Omie.Cash</option>
                                                    <option value="Uniprime" data-logo="uniprime.svg"
                                                        data-name="Uniprime">Uniprime</option>
                                                </select>
                                                <!--end::Input-->


                                            </div>
                                            <!-- Campo oculto para armazenar o nome do banco -->
                                            <input type="hidden" id="banco_nome" name="banco_nome" value="">
                                            <!--end::Input group-->
                                            <!--begin::Input group-->
                                            <div class="row g-9 mb-7">
                                                <!--begin::Col-->
                                                <div class="col-md-6 fv-row">
                                                    <!--begin::Label-->
                                                    <label class="required fs-6 fw-semibold mb-2">Número da
                                                        Conta</label>
                                                    <!--end::Label-->
                                                    <!--begin::Input-->
                                                    <input class="form-control" placeholder="02548-4"
                                                        name="conta" />
                                                    <!--end::Input-->
                                                </div>
                                                <!--end::Col-->
                                                <!--begin::Col-->
                                                <div class="col-md-6 fv-row">
                                                    <!--begin::Label-->
                                                    <label class="required fs-6 fw-semibold mb-2">Número da
                                                        Agencia</label>
                                                    <!--end::Label-->
                                                    <!--begin::Input-->
                                                    <input class="form-control" placeholder="24525-0"
                                                        name="agencia" />
                                                    <!--end::Input-->
                                                </div>
                                                <!--end::Col-->
                                            </div>
                                            <!--end::Input group-->
                                            <!--begin::Input group-->
                                            <div class="row g-9 mb-7">
                                                <!--begin::Col-->
                                                <div class="col-md-4 fv-row">
                                                    <!--begin::Label-->
                                                    <label class="fs-6 fw-semibold mb-2">
                                                        <span class="">Digito agência</span>
                                                        <i class="fas fa-exclamation-circle ms-1 fs-7"
                                                            data-bs-toggle="tooltip" title="Se tiver digito"></i>
                                                    </label>
                                                    <!--end::Label-->
                                                    <!--begin::Input-->
                                                    <input class="form-control" placeholder="001" name="digito" />
                                                    <!--end::Input-->
                                                </div>
                                                <!--end::Col-->
                                                <!--begin::Col-->
                                                <div class="col-md-8 fv-row">
                                                    <!--begin::Label-->
                                                    <label class="required fs-6 fw-semibold mb-2">Tipo de Conta</label>
                                                    <!--end::Label-->
                                                    <!--begin::Input-->
                                                    <select class="form-select fw-bold" name="account_type" required>
                                                        <option value="corrente">Corrente</option>
                                                        <option value="poupanca">Poupança</option>
                                                        <option value="aplicacao">Aplicação</option>
                                                    </select>
                                                    <!--end::Input-->
                                                </div>
                                                <!--end::Col-->
                                            </div>
                                            <!--end::Input group-->
                                            <!--begin::Input group-->
                                            <div class="fv-row mb-15">
                                                <!--begin::Label-->
                                                <label class="fs-6 fw-semibold mb-2">Descrição da Conta</label>
                                                <!--end::Label-->
                                                <!--begin::Input-->
                                                <textarea type="text" class="form-control" placeholder="Breve descrição da conta" name="description"></textarea>
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
                                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">
                                            <img src="/assets/media/icons/duotune/arrows/arr092.svg" />
                                            Sair
                                        </button>
                                        <!--end::Button-->
                                        <!--begin::Button-->
                                        <button type="submit" class="btn btn-primary">
                                            <img src="/assets/media/icons/duotune/general/gen035.svg" />
                                            <span class="indicator-label">Salvar</span>
                                        </button>
                                        <!--end::Button-->
                                    </div>
                                    <!--end::Modal footer-->
                                </form>
                                <!--end::Form-->
                            </div>
                        </div>
                    </div>
                    <!--end::Modal - Customers - Add/Update-->

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
                                            <label class="fs-5 fw-semibold form-label mb-5">Select Export
                                                Format:</label>
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
                <!--end::Content container-->
            </div>
            <!--end::Content-->
        </div>
        <!--end::Content wrapper-->
    </div>
    <!--end:::Main-->

</x-tenant-app-layout>

<script>
    function openAddModal() {
        document.getElementById('kt_modal_add_customer_header').querySelector('h2').innerText = 'Adicionar Banco';
        document.getElementById('method_field').value = 'POST';
        document.getElementById('banco_id').value = '';
        document.getElementById('kt_modal_add_customer').querySelector('form').action =
            "{{ route('cadastroBancos.store') }}";
        $('#kt_modal_add_customer').modal('show');
    }

    function openEditModal(banco) {
        document.getElementById('kt_modal_add_customer_header').querySelector('h2').innerText = 'Atualizar Banco';
        document.getElementById('method_field').value = 'PUT';
        document.getElementById('banco_id').value = banco.id;
        document.getElementById('kt_modal_add_customer').querySelector('form').action =
            "{{ route('cadastroBancos.update', ':id') }}".replace(':id', banco.id);

        document.querySelector('#bank_select').value = banco.banco;
        document.querySelector('input[name="conta"]').value = banco.conta;
        document.querySelector('input[name="agencia"]').value = banco.agencia;
        document.querySelector('input[name="digito"]').value = banco.digito || '';
        document.querySelector('select[name="account_type"]').value = banco.account_type;
        document.querySelector('textarea[name="description"]').value = banco.description;

        // Atualizar o Select2
        $('#bank_select').select2();

        $('#kt_modal_add_customer').modal('show');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const bankSelect = document.getElementById('bank_select');
        const bancoNomeInput = document.getElementById('banco_nome');

        if (bankSelect && bancoNomeInput) {
            // Atualiza o campo oculto sempre que o banco é selecionado
            bankSelect.addEventListener('change', function() {
                const selectedOption = bankSelect.options[bankSelect.selectedIndex];
                const bancoNome = selectedOption.getAttribute('data-name'); // Obtém o nome do banco
                bancoNomeInput.value = bancoNome || ''; // Atualiza o valor do campo oculto
            });
        }
    });
</script>



<script>
    var hostUrl = "/assets/";
</script>
<!--begin::Global Javascript Bundle(mandatory for all pages)-->
<script src="/assets/plugins/global/plugins.bundle.js"></script>
<script src="/assets/js/scripts.bundle.js"></script>
<!--end::Global Javascript Bundle-->
<!--begin::Vendors Javascript(used for this page only)-->
<script src="/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<!--end::Vendors Javascript-->
<!--begin::Custom Javascript(used for this page only)-->
<script src="/assets/js/custom/apps/customers/list/export.js"></script>
<script src="/assets/js/custom/apps/bancos/bancos.js"></script>
<script src="/assets/js/custom/apps/customers/add.js"></script>
<script src="/assets/js/widgets.bundle.js"></script>
<script src="/assets/js/custom/apps/chat/chat.js"></script>
<script src="/assets/js/custom/utilities/modals/upgrade-plan.js"></script>
<script src="/assets/js/custom/utilities/modals/create-campaign.js"></script>
<script src="/assets/js/custom/utilities/modals/users-search.js"></script>
<!--end::Custom Javascript-->
<!--end::Javascript-->
