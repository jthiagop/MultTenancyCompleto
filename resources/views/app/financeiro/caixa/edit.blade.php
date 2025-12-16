<link href="https://kendo.cdn.telerik.com/themes/8.0.1/default/default-main.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<script src="https://kendo.cdn.telerik.com/2024.2.514/js/kendo.all.min.js"></script>
<x-tenant-app-layout>

    {{-- *** Modal *** --}}
    @include('app.components.modals.financeiro.recibo.recibo')

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
                            Lançamento de Caixa</h1>
                        <!--end::Title-->
                        <!--begin::Breadcrumb-->
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
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
                            <li class="breadcrumb-item text-muted">Lançamento Caixa</li>
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
            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">

                <!--begin::Content container-->
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
                                <button type="button" class="btn btn-secondary"
                                    data-bs-dismiss="modal">Cancelar</button>
                                <button type="button" class="btn btn-danger" id="confirmDeleteButton">Excluir</button>
                            </div>
                        </div>
                    </div>
                </div>

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
                                    Tem certeza que deseja excluir o registro <strong>#{{ $caixa->id }}</strong>?
                                </p>
                                <small class="text-muted d-block mt-3">
                                    Esta ação não pode ser desfeita.
                                </small>
                            </div>

                            <!-- Rodapé -->
                            <div class="modal-footer justify-content-center">
                                <form id="delete-form" method="POST"
                                    action="{{ route('caixa.destroy', $caixa->id) }}">
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
                            <div class="flex-lg-row-fluid mb-10 mb-lg-0 me-lg-7 me-xl-10">
                                <!--begin::Card-->
                                <div class="card card-flush pt-3 mb-5 mb-xl-10">
                                    <!--begin::Card header-->
                                    <div class="card-header">
                                        <!--begin::Card title-->
                                        <div class="card-title">
                                            <div class="d-flex align-items-center mb-2">
                                                <a href="#"
                                                    class="text-gray-800 text-hover-primary fs-2 fw-bolder me-1">Dados
                                                    da Cobrança:</a>

                                                @if ($caixa->tipo === 'entrada')
                                                    <span
                                                        class=" fs-2 fw-bolder me-1 text-success">#{{ $caixa->id }}</span>
                                                @elseif($caixa->tipo === 'saida')
                                                    <span class="text-danger">#{{ $caixa->id }}</span>
                                                @else
                                                    <span class="text-secondary">#{{ $caixa->id }}</span>
                                                @endif

                                                @if ($caixa->comprovacao_fiscal == 1)
                                                    <!-- Ícone em amarelo -->
                                                    <a href="#" class="" data-bs-toggle="tooltip"
                                                        data-bs-placement="right"
                                                        alt="Lançamento com Comprovação Fiscal"
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
                                                        data-bs-placement="right"
                                                        alt="Lançamento Sem Comprovação Fiscal"
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

                                        </div>
                                        <!--end::Card toolbar-->
                                        <!--begin::Actions-->
                                        <div class="d-flex mb-4 align-items-center">
                                            <div class="me-0">
                                                <!-- Botão do Menu -->
                                                <button
                                                    class="btn btn-sm btn-icon btn-bg-warning btn-active-color-light"
                                                    data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end"
                                                    aria-label="Opções">
                                                    <i class="bi bi-three-dots fs-3"></i>
                                                </button>
                                                <!--begin::Menu Dropdown-->
                                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px py-3"
                                                    data-kt-menu="true">
                                                    <!--begin::Título do Menu-->
                                                    <div class="menu-item px-3">
                                                        <div
                                                            class="menu-content text-muted pb-2 px-3 fs-7 text-uppercase">
                                                            Gerenciamento
                                                        </div>
                                                    </div>
                                                    <!--end::Título do Menu-->
                                                    <div class="menu-item px-3">
                                                        <a href="#" class="menu-link px-3 icon-hover-blue"data-bs-toggle="modal" data-bs-target="#kt_modal_new_card">
                                                            <i class="fas fa-edit me-2"></i>Editar Lançamento</a>
                                                    </div>
                                                    <!--end::Item: Editar-->
                                                    <!--begin::Item: Criar Fatura-->
                                                    <!-- HTML -->
                                                    <div class="menu-item px-3">
                                                        <a href="#" data-bs-toggle="modal" data-bs-target="#kt_modal_new_ticket" class="menu-link px-3 icon-hover-blue">
                                                            <i class="bi bi-receipt me-2"></i>
                                                            Gerar Recibo
                                                        </a>
                                                    </div>
                                                    <div class="menu-item px-3">
                                                        <form action="{{ route('bill.print', $caixa->id) }}" method="POST" class="d-inline">
                                                            @csrf <!-- Token CSRF para segurança -->
                                                            <button type="submit" class="menu-link px-3 icon-hover-blue bg-transparent border-0 w-100 text-start">
                                                                <i class="bi bi-printer me-2"></i> <!-- Ícone de impressão -->
                                                                Imprimir
                                                            </button>
                                                        </form>
                                                    </div>
                                                    <!-- CSS -->
                                                    <style>
                                                        /* Quando pairar o mouse sobre o link .icon-hover-blue, o ícone dentro dele (i) ficará azul */
                                                        .icon-hover-blue:hover i {
                                                            color: #0d6efd;
                                                            /* Azul padrão do Bootstrap ou cor de sua preferência */
                                                        }
                                                    </style>
                                                    <!--end::Item: Criar Fatura-->
                                                    <!--begin::Item: Criar Pagamento (exemplo com ícone de alerta)-->
                                                    <!--end::Item: Criar Pagamento-->
                                                    <!--begin::Item: Gerar Boleto-->
                                                    <div class="menu-item px-3">
                                                        <a href="#" class="menu-link px-3">Gerar Boleto</a>
                                                    </div>
                                                    <!--end::Item: Gerar Boleto-->
                                                    <!--begin::Item: Assinatura (submenu)-->
                                                    <div class="menu-item px-3" data-kt-menu-trigger="hover"
                                                        data-kt-menu-placement="right-end">
                                                        <a href="#" class="menu-link px-3">
                                                            <span class="menu-title">Assinatura</span>
                                                            <span class="menu-arrow"></span>
                                                        </a>
                                                        <!--begin::Submenu-->
                                                        <div class="menu-sub menu-sub-dropdown w-175px py-4">
                                                            <!--begin::Item: Planos-->
                                                            <div class="menu-item px-3">
                                                                <a href="#" class="menu-link px-3">Planos</a>
                                                            </div>
                                                            <!--end::Item: Planos-->

                                                            <!--begin::Item: Cobrança-->
                                                            <div class="menu-item px-3">
                                                                <a href="#" class="menu-link px-3">Cobrança</a>
                                                            </div>
                                                            <!--end::Item: Cobrança-->

                                                            <!--begin::Item: Extratos-->
                                                            <div class="menu-item px-3">
                                                                <a href="#" class="menu-link px-3">Extratos</a>
                                                            </div>
                                                            <!--end::Item: Extratos-->

                                                            <!--begin::Separador-->
                                                            <div class="separator my-2"></div>
                                                            <!--end::Separador-->

                                                            <!--begin::Item: Recorrência (switch)-->
                                                            <div class="menu-item px-3">
                                                                <div class="menu-content px-3">
                                                                    <label
                                                                        class="form-check form-switch form-check-custom form-check-solid">
                                                                        <input class="form-check-input w-30px h-20px"
                                                                            type="checkbox" value="1"
                                                                            checked="checked" name="notifications" />
                                                                        <span
                                                                            class="form-check-label text-muted fs-6">Recorrente</span>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <!--end::Item: Recorrência (switch)-->
                                                        </div>
                                                        <!--end::Submenu-->
                                                    </div>
                                                    <!--end::Item: Assinatura (submenu)-->

                                                    <!--begin::Item: Excluir-->
                                                    <div class="menu-item px-3 icon-hover-danger">
                                                        <a href="#" class="menu-link px-3 text-danger"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#kt_modal_delete_card">
                                                            <i class="bi bi-trash me-2 text-danger"></i>
                                                            Excluir
                                                        </a>

                                                    </div>
                                                    <!--end::Item: Excluir-->
                                                </div>
                                                <!--end::Menu Dropdown-->
                                            </div>
                                            <!--end::Menu-->

                                        </div>
                                        <!--end::Actions-->
                                    </div>
                                    <!--end::Card header-->
                                    <!--begin::Card body-->
                                    <div class="card-body pt-3">
                                        <!--begin::Section-->
                                        <div class="mb-10">
                                            <!--begin::Title-->


                                            <!--end::Title-->
                                            <!--begin::Details-->
                                            <div class="d-flex flex-wrap py-5">
                                                <!--begin::Row-->
                                                <div class="flex-equal me-5">
                                                    <!--begin::Details-->
                                                    <table class="table fs-6 fw-semibold gs-0 gy-2 gx-2 m-0">
                                                        <!--begin::Row-->
                                                        <tr>
                                                            <td class="text-gray-400 min-w-175px w-175px">Data:</td>
                                                            <td class="text-gray-800 min-w-200px">
                                                                <a href="../../demo1/dist/pages/apps/customers/view.html"
                                                                    class="text-gray-800 text-hover-primary">{{ \Carbon\Carbon::parse($caixa->data_competencia)->format('d/m/Y') }}</a>
                                                            </td>
                                                        </tr>
                                                        <!--end::Row-->
                                                        <!--begin::Row-->
                                                        <tr>
                                                            <td class="text-gray-400">Centro de Custo:</td>
                                                            <td class="text-gray-800">{{ $caixa->costCenter->name ?? '-' }}
                                                            </td>
                                                        </tr>
                                                        <!--end::Row-->
                                                        <!--begin::Row-->
                                                        <tr>
                                                            <td class="text-gray-400">Tipo: </td>
                                                            <td>
                                                                @if ($caixa->tipo === 'entrada')
                                                                    <span
                                                                        class="badge badge-light-success">{{ $caixa->tipo }}</span>
                                                                @elseif($caixa->tipo === 'saida')
                                                                    <span
                                                                        class="badge badge-light-danger">{{ $caixa->tipo }}</span>
                                                                @else
                                                                    <span
                                                                        class="badge badge-light-secondary">{{ $caixa->tipo }}</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-gray-400">Lançamento Padão</td>
                                                            <td class="text-gray-800">
                                                                {{ $caixa->lancamentoPadrao->description ?? '-' }}
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
                                                            <td class="text-gray-800">{{ $caixa->descricao }}</td>
                                                        </tr>
                                                        <!--end::Row-->
                                                        <!--begin::Row-->
                                                        <tr>
                                                            <td class="text-gray-400">Valor:</td>
                                                            <td class="text-gray-800">R$
                                                                {{ number_format($caixa->valor, 2, ',', '.') }}</td>
                                                        </tr>
                                                        <!--end::Row-->
                                                        <!--begin::Row-->
                                                        <tr>
                                                            <td class="text-gray-400">Tipo de Documento:</td>
                                                            <td class="text-gray-800">{{ $caixa->tipo_documento }}
                                                            </td>
                                                        </tr>
                                                        <!--end::Row-->
                                                        <!--begin::Row-->
                                                        <tr>
                                                            <td class="text-gray-400">N. do documento:</td>
                                                            <td class="text-gray-800">{{ $caixa->numero_documento }}
                                                            </td>
                                                        </tr>
                                                        <!--end::Row-->
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
                                                            rows="3">{{ old('historico_complementar', $caixa->historico_complementar) }}</textarea>
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
                                                            <rect opacity="0.5" x="17.0365" y="15.1223"
                                                                width="8.15546" height="2" rx="1"
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
                                                    @include('app.components.modals.modal-upload')


                                                    <!--begin::Add customer-->
                                                    <button type="button" class="btn btn-primary"
                                                        data-bs-toggle="modal" data-bs-target="#kt_modal_upload_arquivo">
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
                                                    <tr
                                                        class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
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
                                                        <th class="text-end">Excluir</th>
                                                    </tr>
                                                    <!--end::Table row-->
                                                </thead>
                                                <!--end::Table head-->
                                                <!--begin::Table body-->
                                                <tbody class="fw-semibold text-gray-600">
                                                    @forelse ($caixa->modulos_anexos as $file)
                                                        <tr>
                                                            <!-- ID -->
                                                            <td>
                                                                {{ $file->id }}</td>
                                                            <!-- Nome -->
                                                            <td>
                                                                <x-file-icon :anexo="$file" />
                                                            </td>
                                                            <!-- Tamanho -->
                                                            <td>
                                                                {{ formatSizeUnits($file->tamanho_arquivo) }}</td>
                                                                <!-- Última Modificação -->
                                                            <td>
                                                                {{ \Carbon\Carbon::parse($file->updated_at)->format('d M Y, g:i a') }}
                                                            </td>
                                                                <!-- Ações -->
                                                            <td class="text-end">
                                                                <a href="#"
                                                                    class="btn btn-icon btn-active-light-danger w-30px h-30px"
                                                                    title="Excluir" data-bs-toggle="modal"
                                                                    data-bs-target="#kt_modal_delete_file">
                                                                    <span class="svg-icon svg-icon-3">
                                                                        <svg width="24" height="24"
                                                                            viewBox="0 0 24 24" fill="none"
                                                                            xmlns="http://www.w3.org/2000/svg">
                                                                            <path
                                                                                d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z"
                                                                                fill="currentColor" />
                                                                            <path opacity="0.5"
                                                                                d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z"
                                                                                fill="currentColor" />
                                                                            <path opacity="0.5"
                                                                                d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z"
                                                                                fill="currentColor" />
                                                                        </svg>
                                                                    </span>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        <!--begin::Modal - Confirmar Exclusão-->
                                                        <div class="modal fade" id="kt_modal_delete_file"
                                                            tabindex="-1" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered">
                                                                <div class="modal-content">
                                                                    <!-- Cabeçalho -->
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title text-danger fw-bold">
                                                                            Confirmar Exclusão</h5>
                                                                        <button type="button" class="btn-close"
                                                                            data-bs-dismiss="modal"
                                                                            aria-label="Close"></button>
                                                                    </div>

                                                                    <!-- Corpo -->
                                                                    <div class="modal-body text-center">
                                                                        <i
                                                                            class="bi bi-exclamation-circle-fill text-danger fs-2 mb-4"></i>
                                                                        <p class="mb-0 fs-5 fw-semibold text-center">
                                                                            Tem certeza que deseja excluir o documento
                                                                            <strong>#{{ $file->nome_arquivo }}</strong>?
                                                                        </p>
                                                                        <small class="text-muted d-block mt-3">
                                                                            Esta ação não pode ser desfeita.
                                                                        </small>
                                                                    </div>

                                                                    <!-- Rodapé -->
                                                                    <div class="modal-footer justify-content-center">
                                                                        <form method="POST"
                                                                            action="{{ route('modulosAnexos.destroy', $file->id) }}"
                                                                            class="d-inline">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="button"
                                                                                class="btn btn-secondary px-4"
                                                                                data-bs-dismiss="modal">Cancelar</button>
                                                                            <button type="submit"
                                                                                class="btn btn-danger px-4">
                                                                                <i class="fas fa-trash-alt me-2"></i>
                                                                                Confirmar Exclusão
                                                                            </button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>
                                                        <!--end::Modal - Confirmar Exclusão-->
                                                    @empty
                                                        <tr>
                                                            <td colspan="5" class="text-center text-muted">Nenhum
                                                                arquivo encontrado.</td>
                                                        </tr>
                                                    @endforelse
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
                            </div>
                            <!--end::Content-->
                        </div>
                        <!--end::Layout-->
                    </div>
                    <!--end::Content container-->
                </div>
                <!--end::Content-->
                <!--end::Content container-->
            </div>
            <!--end::Content-->
        </div>
        <!--end::Content wrapper-->
    </div>
    <!--end:::Main-->

    <!-- Modal Excluir -->
    <!--begin::Modal - Confirm Delete-->



    <!--end::Modal - Confirm Delete-->
    @include('app.components.modals.editar-caixa')




</x-tenant-app-layout>
<script src="/assets/js/custom/utilities/modals/financeiro/update-caixa.js"></script>
<!--begin::Vendors Javascript(used for this page only)-->
<script src="/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<!--end::Vendors Javascript-->
<!--begin::Custom Javascript(used for this page only)-->
<script src="/assets/js/custom/apps/file-manager/upload_arquivos.js"></script>
<script src="/assets/js/custom/apps/invoices/create.js"></script>

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
                url: "{{ route('anexos.update', $caixa->id) }}",
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
    $(document).ready(function() {
        $('#lancamento_padrao_caixa').on('change', function() {
            var selectedValue = $(this).val();
            if (selectedValue === '4') {
                $('#banco-deposito').show(); // Mostra o campo do banco de depósito
            } else {
                $('#banco-deposito').hide(); // Esconde o campo do banco de depósito
            }
        });
    });


    document.addEventListener('DOMContentLoaded', function() {
        const tipoSelect = document.getElementById('tipo_select_caixa');
        const lancamentoPadraoCaixa = document.getElementById('lancamento_padrao_caixa');

        tipoSelect.addEventListener('change', function() {
            const selectedTipo = tipoSelect.value;

            // Função para atualizar opções do select com base no tipo
            const updateOptions = (selectElement) => {
                // Limpa todas as opções do select de Lançamento Padrão
                selectElement.innerHTML = '';

                // Adiciona a opção vazia
                const emptyOption = document.createElement('option');
                emptyOption.value = '';
                emptyOption.text = 'Escolha um Lançamento...';
                selectElement.appendChild(emptyOption);

                // Filtra e adiciona as opções de acordo com o tipo selecionado
                lpsData.forEach(function(lp) {
                    if (lp.type === selectedTipo) {
                        const option = document.createElement('option');
                        option.value = lp.id;
                        option.text = lp.description;
                        selectElement.appendChild(option);
                    }
                });
            };

            // Atualizar ambos os selects, se eles existirem na página
            if (lancamentoPadraoBanco) updateOptions(lancamentoPadraoBanco);
            if (lancamentoPadraoCaixa) updateOptions(lancamentoPadraoCaixa);
        });
    });


    $(document).ready(function() {
        $('#kt_modal_new_card').on('shown.bs.modal', function() {
            $('#lancamento_padrao_caixa').select2({
                placeholder: "Escolha um Lançamento...",
                width: '100%',
                closeOnSelect: true,
                dropdownParent: $('#kt_modal_new_card'),
                language: 'pt' // Define o idioma para português personalizado
            });

        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar os tooltips e menus do Bootstrap
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        var menuElements = document.querySelectorAll('[data-kt-menu]');
        menuElements.forEach(function(menuEl) {
            new KTMenu(menuEl);
        });
    });
</script>
