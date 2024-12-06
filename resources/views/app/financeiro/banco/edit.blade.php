<link href="https://kendo.cdn.telerik.com/themes/8.0.1/default/default-main.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<script src="https://kendo.cdn.telerik.com/2024.2.514/js/kendo.all.min.js"></script>
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
                            Lançamento Bancário</h1>
                        <!--end::Title-->
                        <!--begin::Breadcrumb-->
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Ínicio</a>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-400 w-5px h-2px"></span>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('caixa.index') }}"
                                    class="text-muted text-hover-primary">Financeiro</a>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-400 w-5px h-2px"></span>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('banco.list') }}" class="text-muted text-hover-primary">Movimentação
                                    Bacária</a>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-400 w-5px h-2px"></span>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">Lançamento Bancário</li>
                            <!--end::Item-->
                        </ul>
                        <!--end::Breadcrumb-->
                    </div>
                    <!--end::Page title-->
                    <!--begin::Actions-->
                    <div class="d-flex align-items-center gap-2 gap-lg-3">
                        <!--begin::Filter menu-->
                        <div class="d-flex">
                            <select name="campaign-type" data-control="select2" data-hide-search="true"
                                class="form-select form-select-sm bg-body border-body w-175px">
                                <option value="Twitter" selected="selected">Select Campaign</option>
                                <option value="Twitter">Twitter Campaign</option>
                                <option value="Twitter">Facebook Campaign</option>
                                <option value="Twitter">Adword Campaign</option>
                                <option value="Twitter">Carbon Campaign</option>
                            </select>
                            <a href="#" class="btn btn-icon btn-sm btn-success flex-shrink-0 ms-4"
                                data-bs-toggle="modal" data-bs-target="#kt_modal_create_campaign">
                                <!--begin::Svg Icon | path: icons/duotune/arrows/arr075.svg-->
                                <span class="svg-icon svg-icon-2">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2"
                                            rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor" />
                                        <rect x="4.36396" y="11.364" width="16" height="2" rx="1"
                                            fill="currentColor" />
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                            </a>
                        </div>
                        <!--end::Filter menu-->
                        <!--begin::Secondary button-->
                        <!--end::Secondary button-->
                        <!--begin::Primary button-->
                        <!--end::Primary button-->
                    </div>
                    <!--end::Actions-->
                </div>
                <!--end::Toolbar container-->
            </div>
            <!--end::Toolbar-->
            <!--begin::Modal - Confirmar Exclusão-->

            <div class="modal fade" id="kt_modal_delete_card" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <!-- Cabeçalho -->
                        <div class="modal-header">
                            <h5 class="modal-title text-danger fw-bold">Confirmar Exclusão</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>

                        <!-- Corpo -->
                        <div class="modal-body text-center">
                            <i class="bi bi-exclamation-circle-fill text-danger fs-2 mb-4"></i>
                            <p class="mb-0 fs-5 fw-semibold text-center">
                                Tem certeza que deseja excluir o registro <strong>#{{ $banco->id }}</strong>?
                            </p>
                            <small class="text-muted d-block mt-3">
                                Esta ação não pode ser desfeita.
                            </small>
                        </div>

                        <!-- Rodapé -->
                        <div class="modal-footer justify-content-center">
                            <form id="delete-form" method="POST" action="{{ route('banco.destroy', $banco->id) }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-secondary px-4"
                                    data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-danger px-4">
                                    <i class="fas fa-trash-alt me-2"></i> Confirmar Exclusão
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
            <!--end::Modal - Confirmar Exclusão-->
            <!--begin::Content-->
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <!--begin::Content container-->
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <!--begin::Layout-->
                    <div class="d-flex flex-column flex-lg-row">
                        <!--begin::Content-->
                        <div class="flex-lg-row-fluid me-lg-15 order-2 order-lg-1 mb-10 mb-lg-0">
                            <!--begin::Card-->
                            <div class="card card-flush pt-3 mb-5 mb-xl-10">
                                <!--begin::Card header-->
                                <div class="card-header">
                                    <!--begin::Card title-->
                                    <div class="card-title">
                                        <div class="d-flex align-items-center mb-2">
                                            <a href="#"
                                                class="text-gray-800 text-hover-primary fs-2 fw-bolder me-1">Dados da
                                                Cobrança:</a>

                                            @if ($banco->tipo === 'entrada')
                                                <span
                                                    class=" fs-2 fw-bolder me-1 text-success">#{{ $banco->id }}</span>
                                            @elseif($banco->tipo === 'saida')
                                                <span class="text-danger">#{{ $banco->id }}</span>
                                            @else
                                                <span class="text-secondary">#{{ $banco->id }}</span>
                                            @endif

                                            @if ($banco->comprovacao_fiscal == 1)
                                                <!-- Ícone em amarelo -->
                                                <a href="#" class="" data-bs-toggle="tooltip"
                                                    data-bs-placement="right" alt="Lançamento com Comprovação Fiscal"
                                                    title="Lançamento com Comprovação Fiscal">
                                                    <!--begin::Svg Icon | path: icons/duotune/general/gen026.svg-->
                                                    <span class="svg-icon svg-icon-1 svg-icon-primary">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24px"
                                                            height="24px" viewBox="0 0 24 24">
                                                            <path
                                                                d="M10.0813 3.7242C10.8849 2.16438 13.1151 2.16438 13.9187 3.7242V3.7242C14.4016 4.66147 15.4909 5.1127 16.4951 4.79139V4.79139C18.1663 4.25668 19.7433 5.83365 19.2086 7.50485V7.50485C18.8873 8.50905 19.3385 9.59842 20.2758 10.0813V10.0813C21.8356 10.8849 21.8356 13.1151 20.2758 13.9187V13.9187C19.3385 14.4016 18.8873 15.491 19.2086 16.4951V16.4951C19.7433 18.1663 18.1663 19.7433 16.4951 19.2086V19.2086C15.491 18.8873 14.4016 19.3385 13.9187 20.2758V20.2758C13.1151 21.8356 10.8849 21.8356 10.0813 20.2758V20.2758C9.59842 19.3385 8.50905 18.8873 7.50485 19.2086V19.2086C5.83365 19.7433 4.25668 18.1663 4.79139 16.4951V16.4951C5.1127 15.491 4.66147 14.4016 3.7242 13.9187V13.9187C2.16438 13.1151 2.16438 10.8849 3.7242 10.0813V10.0813C4.66147 9.59842 5.1127 8.50905 4.79139 7.50485V7.50485C4.25668 5.83365 5.83365 4.25668 7.50485 4.79139V4.79139C8.50905 5.1127 9.59842 4.66147 10.0813 3.7242V3.7242Z"
                                                                fill="currentColor" />
                                                            <path
                                                                d="M14.8563 9.1903C15.0606 8.94984 15.3771 8.9385 15.6175 9.14289C15.858 9.34728 15.8229 9.66433 15.6185 9.9048L11.863 14.6558C11.6554 14.9001 11.2876 14.9258 11.048 14.7128L8.47656 12.4271C8.24068 12.2174 8.21944 11.8563 8.42911 11.6204C8.63877 11.3845 8.99996 11.3633 9.23583 11.5729L11.3706 13.4705L14.8563 9.1903Z"
                                                                fill="white" />
                                                        </svg>
                                                    </span>
                                                    <!--end::Svg Icon-->
                                                </a>
                                            @else
                                                <!-- Ícone em vermelho -->
                                                <a href="#" class="" data-bs-toggle="tooltip"
                                                    data-bs-placement="right" alt="Lançamento Sem Comprovação Fiscal"
                                                    title="Sem Comprovação Fiscal">
                                                    <span class="svg-icon svg-icon-1 svg-icon-danger">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24px"
                                                            height="24px" viewBox="0 0 24 24">
                                                            <path
                                                                d="M10.0813 3.7242C10.8849 2.16438 13.1151 2.16438 13.9187 3.7242V3.7242C14.4016 4.66147 15.4909 5.1127 16.4951 4.79139V4.79139C18.1663 4.25668 19.7433 5.83365 19.2086 7.50485V7.50485C18.8873 8.50905 19.3385 9.59842 20.2758 10.0813V10.0813C21.8356 10.8849 21.8356 13.1151 20.2758 13.9187V13.9187C19.3385 14.4016 18.8873 15.491 19.2086 16.4951V16.4951C19.7433 18.1663 18.1663 19.7433 16.4951 19.2086V19.2086C15.491 18.8873 14.4016 19.3385 13.9187 20.2758V20.2758C13.1151 21.8356 10.8849 21.8356 10.0813 20.2758V20.2758C9.59842 19.3385 8.50905 18.8873 7.50485 19.2086V19.2086C5.83365 19.7433 4.25668 18.1663 4.79139 16.4951V16.4951C5.1127 15.491 4.66147 14.4016 3.7242 13.9187V13.9187C2.16438 13.1151 2.16438 10.8849 3.7242 10.0813V10.0813C4.66147 9.59842 5.1127 8.50905 4.79139 7.50485V7.50485C4.25668 5.83365 5.83365 4.25668 7.50485 4.79139V4.79139C8.50905 5.1127 9.59842 4.66147 10.0813 3.7242V3.7242Z"
                                                                fill="currentColor" />
                                                            <path d="M14.5 9.5L9.5 14.5M9.5 9.5L14.5 14.5"
                                                                stroke="white" stroke-width="2"
                                                                stroke-linecap="round" stroke-linejoin="round" />
                                                        </svg>
                                                    </span>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                    <!--begin::Card title-->
                                    <!--begin::Card toolbar-->
                                    <div class="card-toolbar">
                                        <a href="#" class="btn btn-light-primary me-3" data-bs-toggle="modal"
                                            data-bs-target="#kt_modal_new_card"><i class="fas fa-edit"></i>
                                            Editar</a>
                                        <!-- Botão para Excluir -->
                                        <!-- Botão para Excluir -->
                                        <a href="#" class="btn btn-light-danger" data-bs-toggle="modal"
                                            data-bs-target="#kt_modal_delete_card">
                                            <i class="fas fa-trash-alt"></i> Excluir
                                        </a>
                                    </div>
                                    <!--end::Card toolbar-->
                                </div>
                                <!--end::Card header-->
                                <!--begin::Card body-->
                                <div class="card-body pt-3">
                                    <!--begin::Section-->
                                    <div class="mb-10">
                                        <!--begin::Details-->
                                        <div class="d-flex flex-wrap py-5">
                                            <!--begin::Row-->
                                            <div class="flex-equal me-5">
                                                <!--begin::Details-->
                                                <table class="table fs-6 fw-semibold gs-0 gy-2 gx-2 m-0">
                                                    <tr>
                                                        <td class="text-gray-400">Banco:</td>
                                                        <td class="text-gray-800">
                                                            {{ $banco->movimentacao->entidade->nome }}</td>
                                                    </tr>
                                                    <!--begin::Row-->
                                                    <tr>
                                                        <td class="text-gray-400 min-w-175px w-175px">Data:</td>
                                                        <td class="text-gray-800 min-w-200px">
                                                            <a href="../../demo1/dist/pages/apps/customers/view.html"
                                                                class="text-gray-800 text-hover-primary">{{ \Carbon\Carbon::parse($banco->data_competencia)->format('d/m/Y') }}</a>
                                                        </td>
                                                    </tr>
                                                    <!--end::Row-->
                                                    <!--begin::Row-->
                                                    <tr>
                                                        <td class="text-gray-400">Centro de Custo:</td>
                                                        <td class="text-gray-800">{{ $banco->centro }}</td>
                                                    </tr>
                                                    <!--end::Row-->
                                                    <!--begin::Row-->
                                                    <tr>
                                                        <td class="text-gray-400">Tipo: </td>
                                                        <td>
                                                            @if ($banco->tipo === 'entrada')
                                                                <span
                                                                    class="badge badge-light-success">{{ $banco->tipo }}</span>
                                                            @elseif($banco->tipo === 'saida')
                                                                <span
                                                                    class="badge badge-light-danger">{{ $banco->tipo }}</span>
                                                            @else
                                                                <span
                                                                    class="badge badge-light-secondary">{{ $banco->tipo }}</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-gray-400">Lançamento Padão</td>
                                                        <td class="text-gray-800">
                                                            {{ $banco->lancamentoPadrao->description }}
                                                        </td>
                                                    </tr>
                                                    <!--end::Row-->
                                                </table>
                                                <!--end::Details-->
                                            </div>
                                            <!--end::Row-->
                                            <!--begin::Row-->
                                            <div class="flex-equal">
                                                <!--begin::Details-->
                                                <table class="table fs-6 fw-semibold gs-0 gy-2 gx-2 m-0">
                                                    <!--begin::Row-->
                                                    <tr>
                                                        <td class="text-gray-400">Descriçao:</td>
                                                        <td class="text-gray-800">{{ $banco->descricao }}</td>
                                                    </tr>
                                                    <!--end::Row-->
                                                    <!--begin::Row-->
                                                    <tr>
                                                        <td class="text-gray-400">Tipo de Documento:</td>
                                                        <td class="text-gray-800">{{ $banco->tipo_documento }}
                                                        </td>
                                                    </tr>
                                                    <!--end::Row-->
                                                    <!--begin::Row-->
                                                    <tr>
                                                        <td class="text-gray-400">N. do documento:</td>
                                                        <td class="text-gray-800">{{ $banco->numero_documento }}
                                                        </td>
                                                    </tr>
                                                    <!--end::Row-->
                                                    <!-- Valor -->
                                                    <tr>
                                                        <td class="text-gray-400">Valor:</td>
                                                        <td>
                                                            <span
                                                                class="fs-4 fw-bold @if ($banco->tipo === 'entrada') text-success @else text-danger @endif">
                                                                R$ {{ number_format($banco->valor, 2, ',', '.') }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                </table>
                                                <!--end::Details-->
                                            </div>
                                            <!--end::Row-->
                                        </div>
                                        <!--end::Row-->
                                    </div>
                                    <!--end::Section-->
                                    <!--begin::Section-->
                                    <div class="mb-0">
                                        <!--begin::Title-->
                                        <h5 class="mb-4">Historico Homplementar:</h5>
                                        <!--end::Title-->
                                        <!--begin::Product table-->
                                        <div class="table-responsive">
                                            <tr>
                                                <td class="text-gray-800">
                                                    <textarea class="form-control" name="historico_complementar" id="complemento" disabled cols="20"
                                                        rows="3">{{ old('historico_complementar', $banco->historico_complementar) }}</textarea>
                                                    <p class="text-gray-400">Descreva observações relevantes sobre
                                                        esse lançamento financeiro</p>
                                                    @error('historico_complementar')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                            </tr>
                                        </div>
                                        <!--end::Product table-->
                                    </div>
                                    <!--end::Section-->
                                </div>
                                <!--end::Card body-->
                            </div>
                            <!--end::Card-->
                            <!--begin::Card-->
                            <div class="card card-flush pt-3 mb-5 mb-xl-10">
                                <!--begin::Card-->
                                <div class="card card-flush">
                                    <!--begin::Card header-->
                                    <div class="card-header pt-8">
                                        <div class="card-title">
                                            <!--begin::Search-->
                                            <div class="d-flex align-items-center position-relative my-1">
                                                <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                                                <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                                    <svg width="24" height="24" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546"
                                                            height="2" rx="1"
                                                            transform="rotate(45 17.0365 15.1223)"
                                                            fill="currentColor" />
                                                        <path
                                                            d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z"
                                                            fill="currentColor" />
                                                    </svg>
                                                </span>
                                                <!--end::Svg Icon-->
                                                <input type="text" data-kt-filemanager-table-filter="search"
                                                    class="form-control form-control-solid w-250px ps-15"
                                                    placeholder="Pesquisar Arquivos" />
                                            </div>
                                            <!--end::Search-->
                                        </div>
                                        <!--begin::Card toolbar-->
                                        <div class="card-toolbar">
                                            <!--begin::Toolbar-->
                                            <div class="d-flex justify-content-end"
                                                data-kt-filemanager-table-toolbar="base">

                                                <!--begin::Export-->
                                                <button disabled type="button" class="btn btn-light-primary me-3"
                                                    id="kt_file_manager_new_folder">
                                                    <!--begin::Svg Icon | path: icons/duotune/files/fil013.svg-->
                                                    <span class="svg-icon svg-icon-2">
                                                        <svg width="24" height="24" viewBox="0 0 24 24"
                                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path opacity="0.3"
                                                                d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z"
                                                                fill="currentColor" />
                                                            <path
                                                                d="M10.4 3.60001L12 6H21C21.6 6 22 6.4 22 7V19C22 19.6 21.6 20 21 20H3C2.4 20 2 19.6 2 19V4C2 3.4 2.4 3 3 3H9.2C9.7 3 10.2 3.20001 10.4 3.60001ZM16 12H13V9C13 8.4 12.6 8 12 8C11.4 8 11 8.4 11 9V12H8C7.4 12 7 12.4 7 13C7 13.6 7.4 14 8 14H11V17C11 17.6 11.4 18 12 18C12.6 18 13 17.6 13 17V14H16C16.6 14 17 13.6 17 13C17 12.4 16.6 12 16 12Z"
                                                                fill="currentColor" />
                                                            <path opacity="0.3"
                                                                d="M11 14H8C7.4 14 7 13.6 7 13C7 12.4 7.4 12 8 12H11V14ZM16 12H13V14H16C16.6 14 17 13.6 17 13C17 12.4 16.6 12 16 12Z"
                                                                fill="currentColor" />
                                                        </svg>
                                                    </span>
                                                    <!--end::Svg Icon-->Nova Pasta</button>
                                                <!--end::Export-->
                                                @include('app.components.modals.modal-upload-banco')


                                                <!--begin::Add customer-->
                                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                                    data-bs-target="#kt_modal_upload_arquivo">
                                                    <!--begin::Svg Icon | path: icons/duotune/files/fil018.svg-->
                                                    <span class="svg-icon svg-icon-2">
                                                        <svg width="24" height="24" viewBox="0 0 24 24"
                                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path opacity="0.3"
                                                                d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z"
                                                                fill="currentColor" />
                                                            <path
                                                                d="M10.4 3.6L12 6H21C21.6 6 22 6.4 22 7V19C22 19.6 21.6 20 21 20H3C2.4 20 2 19.6 2 19V4C2 3.4 2.4 3 3 3H9.2C9.7 3 10.2 3.2 10.4 3.6ZM16 11.6L12.7 8.3C12.3 7.9 11.7 7.9 11.3 8.3L8 11.6H11V17C11 17.6 11.4 18 12 18C12.6 18 13 17.6 13 17V11.6H16Z"
                                                                fill="currentColor" />
                                                        </svg>
                                                    </span>
                                                    Anexar Arquivo
                                                </button>
                                                <!--end::Add customer-->

                                                <!--end::Add customer-->
                                            </div>
                                            <!--end::Toolbar-->
                                            <!--begin::Group actions-->
                                            <div class="d-flex justify-content-end align-items-center d-none"
                                                data-kt-filemanager-table-toolbar="selected">
                                                <div class="fw-bold me-5">
                                                    <span class="me-2"
                                                        data-kt-filemanager-table-select="selected_count"></span>Selected
                                                </div>
                                                <button type="button" class="btn btn-danger"
                                                    data-kt-filemanager-table-select="delete_selected">Delete
                                                    Selected</button>
                                            </div>
                                            <!--end::Group actions-->
                                        </div>
                                        <!--end::Card toolbar-->
                                    </div>
                                    <!--end::Card header-->
                                    <!--begin::Card body-->
                                    <div class="card-body">
                                        <!--begin::Table-->
                                        <table id="kt_file_manager_list" data-kt-filemanager-table="files"
                                            class="table align-middle table-row-dashed fs-6 gy-5">
                                            <!--begin::Table head-->
                                            <thead>
                                                <!--begin::Table row-->
                                                <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                                    <th class="w-10px pe-2">
                                                        <div
                                                            class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                                            <input class="form-check-input" type="checkbox"
                                                                data-kt-check="true"
                                                                data-kt-check-target="#kt_file_manager_list .form-check-input"
                                                                value="1" />
                                                        </div>
                                                    </th>
                                                    <th class="min-w-250px">Nome</th>
                                                    <th class="min-w-10px">Tamanho</th>
                                                    <th class="min-w-125px">Data da Modificação</th>
                                                    <th class="w-125px"></th>
                                                </tr>
                                                <!--end::Table row-->
                                            </thead>
                                            <!--end::Table head-->
                                            <!--begin::Table body-->
                                            <tbody class="fw-semibold text-gray-600">
                                                @foreach ($banco->anexos as $file)
                                                    <tr>
                                                        <!--begin::Checkbox-->
                                                        <td>
                                                            <div
                                                                class="form-check form-check-sm form-check-custom form-check-solid">
                                                                <input class="form-check-input" type="checkbox"
                                                                    value="1" />
                                                            </div>
                                                        </td>
                                                        <!--end::Checkbox-->
                                                        <!--begin::Name=-->
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <!--begin::Svg Icon | path: icons/duotune/files/fil003.svg-->
                                                                <span
                                                                    class="svg-icon svg-icon-2x svg-icon-primary me-4">
                                                                    <svg width="24" height="24"
                                                                        viewBox="0 0 24 24" fill="none"
                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                        <path opacity="0.3"
                                                                            d="M19 22H5C4.4 22 4 21.6 4 21V3C4 2.4 4.4 2 5 2H14L20 8V21C20 21.6 19.6 22 19 22Z"
                                                                            fill="currentColor" />
                                                                        <path d="M15 8H20L14 2V7C14 7.6 14.4 8 15 8Z"
                                                                            fill="currentColor" />
                                                                    </svg>
                                                                </span>
                                                                <!--end::Svg Icon-->
                                                                <a href="{{ route('file', ['path' => $file->caminho_arquivo]) }}"
                                                                    target="_blank"
                                                                    class="text-gray-800 text-hover-primary">{{ $file->nome_arquivo }}</a>
                                                            </div>
                                                        </td>
                                                        <!--end::Name=-->
                                                        <!--begin::Size-->
                                                        <td>{{ formatSizeUnits($file->size) }}</td>
                                                        <!--end::Size-->
                                                        <!--begin::Last modified-->
                                                        <td>{{ $file->updated_at->format('d M Y, g:i a') }}</td>
                                                        <!--end::Last modified-->
                                                        <!--begin::Actions-->
                                                        <td class="text-end"
                                                            data-kt-filemanager-table="action_dropdown">
                                                            <div class="d-flex justify-content-end">
                                                                <!--begin::Share link-->
                                                                <div class="ms-2"
                                                                    data-kt-filemanger-table="copy_link">
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-icon btn-light btn-active-light-primary"
                                                                        data-kt-menu-trigger="click"
                                                                        data-kt-menu-placement="bottom-end">
                                                                        <!--begin::Svg Icon | path: icons/duotune/coding/cod007.svg-->
                                                                        <span class="svg-icon svg-icon-5 m-0">
                                                                            <svg width="24" height="24"
                                                                                viewBox="0 0 24 24" fill="none"
                                                                                xmlns="http://www.w3.org/2000/svg">
                                                                                <path opacity="0.3"
                                                                                    d="M18.4 5.59998C18.7766 5.9772 18.9881 6.48846 18.9881 7.02148C18.9881 7.55451 18.7766 8.06577 18.4 8.44299L14.843 12C14.466 12.377 13.9547 12.5887 13.4215 12.5887C12.8883 12.5887 12.377 12.377 12 12C11.623 11.623 11.4112 11.1117 11.4112 10.5785C11.4112 10.0453 11.623 9.53399 12 9.15698L15.553 5.604C15.9302 5.22741 16.4415 5.01587 16.9745 5.01587C17.5075 5.01587 18.0188 5.22741 18.396 5.604L18.4 5.59998ZM20.528 3.47205C20.0614 3.00535 19.5074 2.63503 18.8977 2.38245C18.288 2.12987 17.6344 1.99988 16.9745 1.99988C16.3145 1.99988 15.661 2.12987 15.0513 2.38245C14.4416 2.63503 13.8876 3.00535 13.421 3.47205L9.86801 7.02502C9.40136 7.49168 9.03118 8.04568 8.77863 8.6554C8.52608 9.26511 8.39609 9.91855 8.39609 10.5785C8.39609 11.2384 8.52608 11.8919 8.77863 12.5016C9.03118 13.1113 9.40136 13.6653 9.86801 14.132C10.3347 14.5986 10.8886 14.9688 11.4984 15.2213C12.1081 15.4739 12.7616 15.6039 13.4215 15.6039C14.0815 15.6039 14.7349 15.4739 15.3446 15.2213C15.9543 14.9688 16.5084 14.5986 16.975 14.132L20.528 10.579C20.9947 10.1124 21.3649 9.55844 21.6175 8.94873C21.8701 8.33902 22.0001 7.68547 22.0001 7.02551C22.0001 6.36555 21.8701 5.71201 21.6175 5.10229C21.3649 4.49258 20.9947 3.93867 20.528 3.47205Z"
                                                                                    fill="currentColor" />
                                                                                <path
                                                                                    d="M14.132 9.86804C13.6421 9.37931 13.0561 8.99749 12.411 8.74695L12 9.15698C11.6234 9.53421 11.4119 10.0455 11.4119 10.5785C11.4119 11.1115 11.6234 11.6228 12 12C12.3766 12.3772 12.5881 12.8885 12.5881 13.4215C12.5881 13.9545 12.3766 14.4658 12 14.843L8.44699 18.396C8.06999 18.773 7.55868 18.9849 7.02551 18.9849C6.49235 18.9849 5.98101 18.773 5.604 18.396C5.227 18.019 5.0152 17.5077 5.0152 16.9745C5.0152 16.4413 5.227 15.93 5.604 15.553L8.74701 12.411C8.28705 11.233 8.28705 9.92498 8.74701 8.74695C8.10159 8.99737 7.5152 9.37919 7.02499 9.86804L3.47198 13.421C2.52954 14.3635 2.00009 15.6417 2.00009 16.9745C2.00009 18.3073 2.52957 19.5855 3.47202 20.528C4.41446 21.4704 5.69269 21.9999 7.02551 21.9999C8.35833 21.9999 9.63656 21.4704 10.579 20.528L14.132 16.975C14.5987 16.5084 14.9689 15.9544 15.2215 15.3447C15.4741 14.735 15.6041 14.0815 15.6041 13.4215C15.6041 12.7615 15.4741 12.108 15.2215 11.4983C14.9689 10.8886 14.5987 10.3347 14.132 9.86804Z"
                                                                                    fill="currentColor" />
                                                                            </svg>
                                                                        </span>
                                                                        <!--end::Svg Icon-->
                                                                    </button>
                                                                    <!--begin::Menu-->
                                                                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-300px"
                                                                        data-kt-menu="true">
                                                                        <!--begin::Card-->
                                                                        <div class="card card-flush">
                                                                            <div class="card-body p-5">
                                                                                <!--begin::Loader-->
                                                                                <div class="d-flex"
                                                                                    data-kt-filemanger-table="copy_link_generator">
                                                                                    <!--begin::Spinner-->
                                                                                    <div class="me-5"
                                                                                        data-kt-indicator="on">
                                                                                        <span
                                                                                            class="indicator-progress">
                                                                                            <span
                                                                                                class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                                                                        </span>
                                                                                    </div>
                                                                                    <!--end::Spinner-->
                                                                                    <!--begin::Label-->
                                                                                    <div class="fs-6 text-dark">
                                                                                        Generating Share Link...</div>
                                                                                    <!--end::Label-->
                                                                                </div>
                                                                                <!--end::Loader-->
                                                                                <!--begin::Link-->
                                                                                <div class="d-flex flex-column text-start d-none"
                                                                                    data-kt-filemanger-table="copy_link_result">
                                                                                    <div class="d-flex mb-3">
                                                                                        <!--begin::Svg Icon | path: icons/duotune/arrows/arr085.svg-->
                                                                                        <span
                                                                                            class="svg-icon svg-icon-2 svg-icon-success me-3">
                                                                                            <svg width="24"
                                                                                                height="24"
                                                                                                viewBox="0 0 24 24"
                                                                                                fill="none"
                                                                                                xmlns="http://www.w3.org/2000/svg">
                                                                                                <path
                                                                                                    d="M9.89557 13.4982L7.79487 11.2651C7.26967 10.7068 6.38251 10.7068 5.85731 11.2651C5.37559 11.7772 5.37559 12.5757 5.85731 13.0878L9.74989 17.2257C10.1448 17.6455 10.8118 17.6455 11.2066 17.2257L18.1427 9.85252C18.6244 9.34044 18.6244 8.54191 18.1427 8.02984C17.6175 7.47154 16.7303 7.47154 16.2051 8.02984L11.061 13.4982C10.7451 13.834 10.2115 13.834 9.89557 13.4982Z"
                                                                                                    fill="currentColor" />
                                                                                            </svg>
                                                                                        </span>
                                                                                        <!--end::Svg Icon-->
                                                                                        <div class="fs-6 text-dark">
                                                                                            Share Link Generated</div>
                                                                                    </div>
                                                                                    <input type="text"
                                                                                        class="form-control form-control-sm"
                                                                                        value="https://path/to/file/or/folder/" />
                                                                                    <div
                                                                                        class="text-muted fw-normal mt-2 fs-8 px-3">
                                                                                        Read only.
                                                                                        <a href="../../demo1/dist/apps/file-manager/settings/.html"
                                                                                            class="ms-2">Change
                                                                                            permissions</a>
                                                                                    </div>
                                                                                </div>
                                                                                <!--end::Link-->
                                                                            </div>
                                                                        </div>
                                                                        <!--end::Card-->
                                                                    </div>
                                                                    <!--end::Menu-->
                                                                </div>
                                                                <!--end::Share link-->
                                                                <!--begin::More-->
                                                                <div class="ms-2">
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-icon btn-light btn-active-light-primary"
                                                                        data-kt-menu-trigger="click"
                                                                        data-kt-menu-placement="bottom-end">
                                                                        <!--begin::Svg Icon | path: icons/duotune/general/gen052.svg-->
                                                                        <span class="svg-icon svg-icon-5 m-0">
                                                                            <svg width="24" height="24"
                                                                                viewBox="0 0 24 24" fill="none"
                                                                                xmlns="http://www.w3.org/2000/svg">
                                                                                <rect x="10" y="10" width="4"
                                                                                    height="4" rx="2"
                                                                                    fill="currentColor" />
                                                                                <rect x="17" y="10" width="4"
                                                                                    height="4" rx="2"
                                                                                    fill="currentColor" />
                                                                                <rect x="3" y="10" width="4"
                                                                                    height="4" rx="2"
                                                                                    fill="currentColor" />
                                                                            </svg>
                                                                        </span>
                                                                        <!--end::Svg Icon-->
                                                                    </button>
                                                                    <!--begin::Menu-->
                                                                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-150px py-4"
                                                                        data-kt-menu="true">
                                                                        <!--begin::Menu item-->
                                                                        <div class="menu-item px-3">
                                                                            <a href="#"
                                                                                class="menu-link px-3">Download
                                                                                File</a>
                                                                        </div>
                                                                        <!--end::Menu item-->
                                                                        <!--begin::Menu item-->
                                                                        <div class="menu-item px-3">
                                                                            <a href="#" class="menu-link px-3"
                                                                                data-kt-filemanager-table="rename">Rename</a>
                                                                        </div>
                                                                        <!--end::Menu item-->
                                                                        <!--begin::Menu item-->
                                                                        <div class="menu-item px-3">
                                                                            <a href="#" class="menu-link px-3"
                                                                                data-kt-filemanager-table-filter="move_row"
                                                                                data-bs-toggle="modal"
                                                                                data-bs-target="#kt_modal_move_to_folder">Move
                                                                                to folder</a>
                                                                        </div>
                                                                        <!--end::Menu item-->
                                                                        <!--begin::Menu item-->
                                                                        <div class="menu-item px-3">
                                                                            <a href="#"
                                                                                class="menu-link text-danger px-3"
                                                                                data-kt-filemanager-table-filter="delete_row"
                                                                                data-file-id="{{ $file->id }}">Excluir</a>
                                                                        </div>
                                                                        <!--end::Menu item-->
                                                                    </div>
                                                                    <!--end::Menu-->
                                                                </div>
                                                                <!--end::More-->
                                                            </div>
                                                        </td>
                                                        <!--end::Actions-->
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
                            </div>
                            <!--end::Card-->
                            <!--begin::Card-->
                            <div class="card card-flush pt-3 mb-5 mb-xl-10">
                                <!--begin::Card header-->
                                <div class="card-header">
                                    <!--begin::Card title-->
                                    <div class="card-title">
                                        <h2>Modificações Recentes</h2>
                                    </div>
                                    <!--end::Card title-->
                                    <!--begin::Card toolbar-->
                                    <div class="card-toolbar">
                                        <a href="#" class="btn btn-light-primary">View All Events</a>
                                    </div>
                                    <!--end::Card toolbar-->
                                </div>
                                <!--end::Card header-->
                                <!--begin::Card body-->
                                <div class="card-body pt-0">
                                    <!--begin::Table wrapper-->
                                    <div class="table-responsive">
                                        <!--begin::Table-->
                                        <table
                                            class="table align-middle table-row-dashed fs-6 text-gray-600 fw-semibold gy-5"
                                            id="kt_table_customers_events">
                                            <!--begin::Table body-->
                                            <tbody>
                                                <!--begin::Table row-->
                                                <tr>
                                                    <!--begin::Event=-->
                                                    <td class="min-w-400px">
                                                        <a href="#"
                                                            class="fw-bold text-gray-800 text-hover-primary me-1">Brian
                                                            Cox</a>has made payment to
                                                        <a href="#"
                                                            class="fw-bold text-gray-800 text-hover-primary">5470-3581</a>
                                                    </td>
                                                    <!--end::Event=-->
                                                    <!--begin::Timestamp=-->
                                                    <td class="pe-0 text-gray-600 text-end min-w-200px">19 Aug
                                                        2023, 2:40 pm
                                                    </td>
                                                    <!--end::Timestamp=-->
                                                </tr>
                                                <!--end::Table row-->
                                                <!--begin::Table row-->
                                                <tr>
                                                    <!--begin::Event=-->
                                                    <td class="min-w-400px">Invoice
                                                        <a href="#"
                                                            class="fw-bold text-gray-800 text-hover-primary me-1">9730-7169</a>status
                                                        has changed from
                                                        <span class="badge badge-light-primary me-1">In
                                                            Transit</span>to
                                                        <span class="badge badge-light-success">Approved</span>
                                                    </td>
                                                    <!--end::Event=-->
                                                    <!--begin::Timestamp=-->
                                                    <td class="pe-0 text-gray-600 text-end min-w-200px">15 Apr
                                                        2023, 5:20 pm
                                                    </td>
                                                    <!--end::Timestamp=-->
                                                </tr>
                                                <!--end::Table row-->
                                                <!--begin::Table row-->
                                                <tr>
                                                    <!--begin::Event=-->
                                                    <td class="min-w-400px">Invoice
                                                        <a href="#"
                                                            class="fw-bold text-gray-800 text-hover-primary me-1">9730-7169</a>status
                                                        has changed from
                                                        <span class="badge badge-light-primary me-1">In
                                                            Transit</span>to
                                                        <span class="badge badge-light-success">Approved</span>
                                                    </td>
                                                    <!--end::Event=-->
                                                    <!--begin::Timestamp=-->
                                                    <td class="pe-0 text-gray-600 text-end min-w-200px">21 Feb
                                                        2023, 8:43 pm
                                                    </td>
                                                    <!--end::Timestamp=-->
                                                </tr>
                                                <!--end::Table row-->
                                                <!--begin::Table row-->
                                                <tr>
                                                    <!--begin::Event=-->
                                                    <td class="min-w-400px">Invoice
                                                        <a href="#"
                                                            class="fw-bold text-gray-800 text-hover-primary me-1">9511-2431</a>status
                                                        has changed from
                                                        <span class="badge badge-light-warning me-1">Pending</span>to
                                                        <span class="badge badge-light-info">In Progress</span>
                                                    </td>
                                                    <!--end::Event=-->
                                                    <!--begin::Timestamp=-->
                                                    <td class="pe-0 text-gray-600 text-end min-w-200px">10 Nov
                                                        2023, 9:23 pm
                                                    </td>
                                                    <!--end::Timestamp=-->
                                                </tr>
                                                <!--end::Table row-->
                                                <!--begin::Table row-->
                                                <tr>
                                                    <!--begin::Event=-->
                                                    <td class="min-w-400px">
                                                        <a href="#"
                                                            class="fw-bold text-gray-800 text-hover-primary me-1">Brian
                                                            Cox</a>has made payment to
                                                        <a href="#"
                                                            class="fw-bold text-gray-800 text-hover-primary">5470-3581</a>
                                                    </td>
                                                    <!--end::Event=-->
                                                    <!--begin::Timestamp=-->
                                                    <td class="pe-0 text-gray-600 text-end min-w-200px">21 Feb
                                                        2023, 11:05 am
                                                    </td>
                                                    <!--end::Timestamp=-->
                                                </tr>
                                                <!--end::Table row-->
                                            </tbody>
                                            <!--end::Table body-->
                                        </table>
                                        <!--end::Table-->
                                    </div>
                                    <!--end::Table wrapper-->
                                </div>
                                <!--end::Card body-->
                            </div>
                            <!--end::Card-->
                        </div>
                        <!--end::Content-->
                    </div>
                    <!--end::Layout-->
                </div>
                <!--end::Content container-->
            </div>
            <!--end::Content-->
        </div>
        <!--end::Content wrapper-->
    </div>
    <!--end:::Main-->

    <!-- Modal Excluir -->
    <!--begin::Modal - Confirm Delete-->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog"
        aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar Exclusão</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Tem certeza de que deseja excluir este arquivo?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteButton">Excluir</button>
                </div>
            </div>
        </div>
    </div>
    <!--end::Modal - Confirm Delete-->

    <!--begin::Modal - Upload File-->
    <div class="modal fade" id="kt_modal_upload" tabindex="-1" aria-hidden="true">
        <!--begin::Modal dialog-->
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <!--begin::Modal content-->
            <div class="modal-content">
                <!--begin::Form-->
                <form id="uploadForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <!--begin::Modal header-->
                    <div class="modal-header">
                        <!--begin::Modal title-->
                        <h2 class="fw-bold">Anexar Arquivos</h2>
                        <!--end::Modal title-->
                        <!--begin::Close-->
                        <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                            <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
                            <span class="svg-icon svg-icon-1">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <rect opacity="0.5" x="6" y="17.3137" width="16" height="2"
                                        rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
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
                    <div class="modal-body pt-10 pb-15 px-lg-17">
                        <!--begin::Input group-->
                        <div class="form-group">
                            <!--begin::Dropzone-->
                            <div class="dropzone dropzone-queue mb-2" id="kt_dropzonejs_example_2">
                                <input type="file" name="files[]" id="photos" />
                                <script>
                                    $("#photos").kendoUpload({
                                        async: {
                                            removeUrl: "{{ url('/remove') }}",
                                            removeField: "path",
                                            withCredentials: false
                                        },
                                        multiple: true, // Permite a seleção de múltiplos arquivos
                                        validation: {
                                            allowedExtensions: ["jpg", "jpeg", "png", "pdf", "page"], // Extensões permitidas
                                            maxFileSize: 5242880, // Tamanho máximo do arquivo (5 MB)
                                            minFileSize: 1024 // Tamanho mínimo do arquivo (1 KB)
                                        },
                                        localization: {
                                            uploadSuccess: "Upload bem-sucedido!",
                                            uploadFail: "Falha no upload",
                                            invalidFileExtension: "Tipo de arquivo não permitido",
                                            invalidMaxFileSize: "O arquivo é muito grande",
                                            invalidMinFileSize: "O arquivo é muito pequeno",
                                            select: "Anexar Arquivos"

                                        }
                                    });
                                </script>
                            </div>
                            <!--end::Dropzone-->
                            <!--begin::Hint-->
                            <span class="form-text fs-6 text-muted">O tamanho máximo do arquivo é 5 MB por
                                arquivo.</span>
                            <!--end::Hint-->

                        </div>
                        <!--end::Input group-->
                        <button type="submit" class="btn btn-primary">
                            <span class="indicator-label">Atualizar</span>
                        </button>
                    </div>
                    <!--end::Modal body-->
                </form>
                <!--end::Form-->
            </div>
        </div>
    </div>
    <!--end::Modal - Upload File-->
    @include('app.components.modals.editar-banco')

</x-tenant-app-layout>


<!--begin::Vendors Javascript(used for this page only)-->
<script src="/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<!--end::Vendors Javascript-->
<!--begin::Custom Javascript(used for this page only)-->
<script src="/assets/js/custom/apps/file-manager/list.js"></script>
<script src="/assets/js/widgets.bundle.js"></script>
<script src="/assets/js/custom/apps/chat/chat.js"></script>
<script src="/assets/js/custom/utilities/modals/upgrade-plan.js"></script>
<script src="/assets/js/custom/utilities/modals/create-campaign.js"></script>
<script src="/assets/js/custom/utilities/modals/users-search.js"></script>

<!--end::Custom Javascript-->
<script>
    $(document).ready(function() {
        $('#uploadForm').on('submit', function(e) {
            e.preventDefault();

            let formData = new FormData(this);
            $.ajax({
                url: "{{ route('anexos.update', $banco->id) }}",
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    // Fechar o modal
                    $('#kt_modal_upload').modal('hide');
                    // Exibir mensagem de sucesso (você pode personalizar isso)
                    alert('Arquivos enviados com sucesso!');
                    // Atualizar a lista de anexos ou fazer qualquer outra ação necessária
                    location.reload();
                },
                error: function(xhr) {
                    // Exibir mensagens de erro
                    alert('Erro ao enviar os arquivos.');
                }
            });
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var deleteFileId;

        // Capture the delete button click event
        document.querySelectorAll('.delete-file-button').forEach(button => {
            button.addEventListener('click', function() {
                deleteFileId = this.getAttribute('data-file-id');
            });
        });

        // Handle the confirm delete button click event
        document.getElementById('confirmDeleteButton').addEventListener('click', function() {
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = `/anexos/${deleteFileId}`;

            var csrfField = document.createElement('input');
            csrfField.type = 'hidden';
            csrfField.name = '_token';
            csrfField.value = '{{ csrf_token() }}';

            var methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';

            form.appendChild(csrfField);
            form.appendChild(methodField);

            document.body.appendChild(form);
            form.submit();
        });
    });
</script>


<script>
    $(document).ready(function() {
        $('#lancamento_padrao').select2({
            templateResult: formatOption,
            templateSelection: formatOption,
            escapeMarkup: function(markup) {
                return markup;
            }
        });
    });

    function formatOption(option) {
        if (!option.id) {
            return option.text;
        }

        var type = $(option.element).data('type');
        var badge = '';

        if (type === 'entrada') {
            badge = '<span class="badge badge-light-success fw-bold fs-8 opacity-75 ps-3 ">Entrada</span>';
        } else if (type === 'saida') {
            badge = '<span class="badge badge-light-danger fw-bold fs-8 opacity-75 ps-3">Saída</span>';
        }

        return badge + ' ' + option.text;
    }
</script>
