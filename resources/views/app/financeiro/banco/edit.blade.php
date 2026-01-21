<link href="https://kendo.cdn.telerik.com/themes/8.0.1/default/default-main.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<script src="https://kendo.cdn.telerik.com/2024.2.514/js/kendo.all.min.js"></script>

<x-tenant-app-layout
pageTitle="Editar Lançamento Bancário"
:breadcrumbs="[['label' => 'Financeiro', 'url' => route('banco.list')], ['label' => 'Editar Lançamento Bancário']]">

    {{-- *** Modal *** --}}
    @include('app.components.modals.financeiro.recibo.reciboBanco')
    @include('app.components.modals.financeiro.banco.edit-field', ['banco' => $banco, 'lps' => $lps, 'centrosAtivos' => $centrosAtivos])

    <!--begin::Main-->
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <!--begin::Content wrapper-->
        <div class="d-flex flex-column flex-column-fluid">
            <!--begin::Content-->
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <!--begin::Content container-->
                <div id="kt_app_content_container" class="app-container container-fluid">
                    <!--begin::Hero card-->
                    <div class="card card-bordered mb-12 mt-7">
                        <!--begin::Hero header-->
                                <!--begin::Card header-->
                                <div class="card-header">
                                    <!--begin::Card title-->
                                    <div class="card-title">
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="text-gray-800 text-hover-primary fs-2 fw-bolder me-1">Dados da Cobrança:</span>
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
                                                <a class="" data-bs-toggle="tooltip"
                                                    data-bs-placement="right" alt="Lançamento com Comprovação Fiscal"
                                                    title="Lançamento com Comprovação Fiscal">
                                                    <!--begin::Svg Icon | path: icons/duotune/general/gen026.svg-->
                                                    <span class="svg-icon svg-icon-1 svg-icon-primary">
                                                        <i class="bi bi-check-circle-fill"></i>
                                                    </span>
                                                    <!--end::Svg Icon-->
                                                </a>
                                            @else
                                                <!-- Ícone em vermelho -->
                                                <a class="" data-bs-toggle="tooltip"
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

                                    <!--begin::Actions-->
                                    <div class="card-toolbar">
                                        <div class="me-0">
                                            <!-- Botão do Menu -->
                                            <button class="btn btn-sm btn-primary btn-active-color- border-0 shadow-none text-white d-flex align-items-center"
                                                data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end"
                                                aria-label="Opções">
                                               <span class="ms-2 fw-bold">Ações</span> <i class="bi bi-chevron-down ms-2"></i>
                                            </button>
                                            <!--begin::Menu Dropdown-->
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px py-3"
                                                data-kt-menu="true">
                                                <!--begin::Título do Menu-->
                                                <div class="menu-item px-3">
                                                    <div class="menu-content text-muted pb-2 px-3 fs-7 text-uppercase">
                                                        Gerenciamento
                                                    </div>
                                                </div>
                                                <!--end::Título do Menu-->
                                                <div class="menu-item px-3">
                                                    <a href="#"
                                                        class="menu-link px-3 icon-hover-blue"data-bs-toggle="modal"
                                                        data-bs-target="#kt_modal_new_card">
                                                        <i class="fas fa-edit me-2"></i>Editar Lançamento</a>
                                                </div>
                                                <!--end::Item: Editar-->
                                                <!--begin::Item: Criar Fatura-->
                                                <!-- HTML -->
                                                <div class="menu-item px-3">
                                                    <a href="#" data-bs-toggle="modal"
                                                        data-bs-target="#kt_modal_new_ticket"
                                                        class="menu-link px-3 icon-hover-blue">
                                                        <i class="bi bi-receipt me-2"></i>
                                                        Gerar Recibo
                                                    </a>
                                                </div>
                                                <div class="menu-item px-3">
                                                    <form action="{{ route('bill.print', $banco->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf <!-- Token CSRF para segurança -->
                                                        <button type="submit"
                                                            class="menu-link px-3 icon-hover-blue bg-transparent border-0 w-100 text-start">
                                                            <i class="bi bi-printer me-2"></i>
                                                            <!-- Ícone de impressão -->
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
                                                        data-bs-toggle="modal" data-bs-target="#kt_modal_delete_card">
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
                                        <!--begin::Details-->
                                        <x-tenant-info-grid>
                                            <x-tenant-info-item label="Entidade Financeira" :value="$banco->entidadeFinanceira->nome" />
                                            <x-tenant-info-item label="Data de competência" :value="\Carbon\Carbon::parse($banco->data_competencia)->format('d/m/Y')" />
                                            <x-tenant-info-item label="Centro de Custo" :value="$banco->costCenter ? $banco->costCenter->name : 'Centro de Custo não informado'" :editable="true" field="cost_center_id" :value-id="$banco->cost_center_id" />
                                            <x-tenant-info-item label="Categoria" :value="$banco->lancamentoPadrao->description" :editable="true" field="lancamento_padrao_id" :value-id="$banco->lancamento_padrao_id" />
                                            <x-tenant-info-item label="Descrição" :value="$banco->descricao" :editable="true" field="descricao" />
                                            <x-tenant-info-item label="Tipo Documento" :value="$banco->tipo_documento" />
                                            <x-tenant-info-item label="N. Documento" :value="$banco->numero_documento" />
                                            <x-tenant-info-item label="Valor Total" :value="$banco->valor" currency :currency-variant="$banco->tipo" />
                                        </x-tenant-info-grid>
                                        <!--end::Details-->

                                        @if($banco->recorrencia_id && $banco->recorrenciaConfig)
                                        <!--begin::Section - Recorrência-->
                                        <div class="mb-8 mt-8">
                                            <!--begin::Title-->
                                            <h5 class="mb-4">Informações de recorrência</h5>
                                            <!--end::Title-->
                                            <div class="separator my-4"></div>

                                            <!--begin::Recurrence Info-->
                                            <div class="d-flex flex-column gap-3">
                                                <!--begin::Lancamento recorrente-->
                                                <div class="d-flex align-items-start">
                                                    <span class="text-gray-600 fw-semibold" style="min-width: 200px;">Lançamento recorrente?</span>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <i class="bi bi-repeat text-primary fs-5"></i>
                                                        <span class="text-gray-800 fw-bold">
                                                            @if($banco->recorrencia->isNotEmpty())
                                                                {{ $banco->recorrencia->first()->pivot->numero_ocorrencia ?? 1 }}/{{ $banco->recorrenciaConfig->total_ocorrencias }}
                                                            @else
                                                                1/{{ $banco->recorrenciaConfig->total_ocorrencias }}
                                                            @endif
                                                        </span>
                                                    </div>
                                                </div>
                                                <!--end::Lancamento recorrente-->

                                                <!--begin::Configuração de recorrência-->
                                                <div class="d-flex align-items-start">
                                                    <span class="text-gray-600 fw-semibold" style="min-width: 200px;">Configuração de recorrência</span>
                                                    <span class="text-gray-800">
                                                        @php
                                                            $frequenciaMap = [
                                                                'diario' => 'dia(s)',
                                                                'semanal' => 'semana(s)',
                                                                'mensal' => 'mês(es)',
                                                                'anual' => 'ano(s)'
                                                            ];
                                                            $frequenciaTexto = $frequenciaMap[$banco->recorrenciaConfig->frequencia] ?? $banco->recorrenciaConfig->frequencia;
                                                        @endphp
                                                        {{ ucfirst($banco->recorrenciaConfig->frequencia) }}: A cada
                                                        <span class="fw-bold">{{ $banco->recorrenciaConfig->intervalo_repeticao }} {{ $frequenciaTexto }}</span>,
                                                        <span class="fw-bold">{{ $banco->recorrenciaConfig->total_ocorrencias }}</span> vez(es)
                                                    </span>
                                                </div>
                                                <!--end::Configuração de recorrência-->
                                            </div>
                                            <!--end::Recurrence Info-->
                                        </div>
                                        <!--end::Section - Recorrência-->
                                        @endif
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

                            <!--begin::Modal - Confirm Delete-->
                            <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog"
                                aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar
                                                Exclusão
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Tem certeza de que deseja excluir este arquivo?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Cancelar</button>
                                            <form id="deleteFileForm" method="POST" action="#">
                                                @csrf
                                                @method('DELETE')
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-danger">Excluir</button>

                                                </div>
                                            </form>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end::Modal - Confirm Delete-->
                        </div>
                        <!--end::Hero body-->
                    </div>
                    <!--end::Hero card-->
                    <!--begin::Row-->
                    <div class="row gy-0 mb-6 mb-xl-12">
                        <!--begin::Col-->
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
                                                <i class="bi bi-search fs-3"></i>
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
                                            <!--begin::Add customer-->
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#kt_modal_upload_arquivo">
                                                <!--begin::Svg Icon | path: icons/duotune/files/fil018.svg-->
                                                <span class="svg-icon svg-icon-2">
                                                    <i class="bi bi-upload fs-3"></i>
                                                </span>
                                                Anexar Arquivo
                                            </button>
                                            <!--end::Add customer-->
                                        </div>
                                        <!--end::Toolbar-->
                                    </div>
                                    <!--end::Card toolbar-->
                                </div>
                                <!--end::Card header-->
                                <!--begin::Card body-->
                                <div class="card-body">
                                    <!--begin::Table-->
                                    <table id="kt_file_manager_list" data-kt-filemanager-table="files"
                                        class="table align-middle table-row-dashed fs-6 gy-3">
                                        <!--begin::Table head-->
                                        <thead>
                                            <tr class="text-start text-gray-400 fw-bold fs-6 text-uppercase gs-0">
                                                <th class="min-w-10px">ID</th>
                                                <th class="min-w-250px">Nome</th>
                                                <th class="min-w-10px">Tamanho</th>
                                                <th class="min-w-125px">Última Modificação</th>
                                                <th class="w-125px text-end">Ações</th>
                                            </tr>
                                        </thead>
                                        <!--end::Table head-->
                                        <!--begin::Table body-->
                                        <tbody class="fw-semibold text-gray-600">
                                            @forelse ($banco->modulos_anexos as $file)
                                                <tr>
                                                    <!-- ID -->
                                                    <td>{{ $file->id }}</td>

                                                    <!-- Nome -->
                                                    <td>
                                                        <x-file-icon :anexo="$file" />
                                                    </td>

                                                    <!-- Tamanho -->
                                                    <td>{{ formatSizeUnits($file->tamanho_arquivo) }}</td>

                                                    <!-- Última Modificação -->
                                                    <td>{{ \Carbon\Carbon::parse($file->updated_at)->format('d M Y, g:i a') }}
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
                                                <div class="modal fade" id="kt_modal_delete_file" tabindex="-1"
                                                    aria-hidden="true">
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
                        <!--end::Col-->
                    </div>
                    <!--end::Row-->
                </div>
                <!--end::Content container-->
            </div>
            <!--end::Content-->
        </div>
        <!--end::Content wrapper-->
    </div>
    <!--end:::Main-->



    <!--begin::Modal - Upload File-->
    @include('app.components.modals.editar-banco')
    <!--end::Modal - Upload File-->

</x-tenant-app-layout>


<!--begin::Vendors Javascript(used for this page only)-->
<script src="/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<script src="/assets/js/custom/utilities/modals/financeiro/update-banco.js"></script>

<!--end::Vendors Javascript-->
<!--begin::Custom Javascript(used for this page only)-->
<script src="/assets/js/custom/apps/file-manager/list.js"></script>
<script src="/assets/js/widgets.bundle.js"></script>
<script src="/assets/js/custom/apps/chat/chat.js"></script>
<script src="/assets/js/custom/utilities/modals/upgrade-plan.js"></script>
<script src="/assets/js/custom/utilities/modals/create-campaign.js"></script>
<script src="/assets/js/custom/utilities/modals/users-search.js"></script>


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

    document.addEventListener('DOMContentLoaded', function() {
        // Atualizar o formulário do modal com o ID do arquivo
        const confirmDeleteModal = document.getElementById('confirmDeleteModal');
        const deleteFileForm = document.getElementById('deleteFileForm');

        confirmDeleteModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget; // Botão que acionou o modal
            const fileId = button.getAttribute('data-file-id'); // Obter o ID do arquivo

            // Atualizar a ação do formulário para usar a rota destroy padrão
            deleteFileForm.action = `/files/${fileId}`;
        });
    });
</script>
