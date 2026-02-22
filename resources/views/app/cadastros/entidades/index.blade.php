<x-tenant-app-layout pageTitle="Cadastro de Entidade Financeira" :breadcrumbs="[['label' => 'Financeiro', 'url' => route('banco.list')], ['label' => 'Entidades Financeiras']]">

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid mt-5" >
        <!--begin::Content container-->
        <div id="kt_app_content_container" class="app-container container-fluid">
            <!--begin::Modal - Cadastro de Entidade Financeira-->
            @include('app.components.modals.financeiro.entidade')
            <!--end::Modal-->

            <!--begin::Row-->
            <div class="row gy-5 g-xl-10">
                <!--begin::Col-->
                <div class="col-xl-12 mb-5 mb-xl-10">
                    <!--begin::Engage widget 1-->
                    <div class="card h-md-100" dir="ltr">
                        <div class="card">
                            <!--begin::Products-->
                            <div class="card card-flush">
                                <!--begin::Card header-->
                                <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                                    <!--begin::Card title-->
                                    <div class="card-title">
                                        <!--begin::Search-->
                                        <div class="d-flex align-items-center position-relative my-1">
                                            <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <input type="text" id="kt_entidades_search"
                                                class="form-control form-control-solid w-250px ps-12"
                                                placeholder="Buscar entidade..." />
                                        </div>
                                    </div>
                                    <!--end::Card title-->
                                    <!--begin::Actions-->
                                    <div class="d-flex align-items-center gap-2 gap-lg-3">
                                        <div class="m-0">
                                            <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#kt_modal_entidade_financeira">
                                                <i class="bi bi-plus-lg fs-2"></i>
                                                Cadastrar Nova
                                            </a>
                                        </div>
                                    </div>
                                    <!--end::Actions-->
                                </div>
                                <!--end::Card header-->
                                <!--begin::Card body-->
                                <div class="card-body pt-0">
                                    <!--begin::Table-->
                                    <table class="table align-middle table-row-dashed fs-6 gy-5"
                                        id="kt_entidades_table">
                                        <!--begin::Table head-->
                                        <thead>
                                            <!--begin::Table row-->
                                            <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                                <th class="min-w-150px">Nome</th>
                                                <th class="text-end min-w-175px">Saldo Inicial</th>
                                                <th class="text-end min-w-150px">Ultima Atualização</th>
                                                <th class="text-end min-w-150px">Saldo Atual</th>
                                                <th class="text-end min-w-100px">Tipo</th>
                                                <th class="text-end min-w-150px">Conta Contábil</th>
                                                <th class="text-end min-w-150px">Descrição</th>
                                                @if (auth()->user()->hasRole(['admin', 'global']))
                                                    <th class="text-end min-w-100px">Ações</th>
                                                @endif
                                            </tr>
                                            <!--end::Table row-->
                                        </thead>
                                        <!--end::Table head-->
                                        <!--begin::Table body-->
                                        <tbody class="fw-semibold text-gray-600">
                                            @forelse($entidades as $entidade)
                                                <tr>
                                                    <!-- Nome -->
                                                    <td>{{ $entidade->nome }}</td>
                                                    <!-- Saldo Inicial -->
                                                    <td class="text-end pe-0">R$
                                                        {{ number_format($entidade->saldo_inicial_real, 2, ',', '.') }}
                                                    </td>
                                                    <!-- Saldo Inicial -->
                                                    <td class="text-end pe-0">
                                                        {{ $entidade->updated_at->format('d/m/Y H:i') }}
                                                    </td>
                                                    <!-- Saldo Atual -->
                                                    <td
                                                        class="text-end pe-0 {{ $entidade->saldo_atual >= 0 ? 'text-success' : 'text-danger' }}">
                                                        R$
                                                        {{ number_format($entidade->saldo_atual, 2, ',', '.') }}
                                                    </td>
                                                    <!-- Tipo -->
                                                    <td class="text-end pe-0">{{ ucfirst($entidade->tipo) }}
                                                    </td>
                                                    <!-- Conta Contábil -->
                                                    <td class="text-end pe-0">
                                                        @if ($entidade->contaContabil)
                                                            <span
                                                                class="text-gray-800">{{ $entidade->contaContabil->code }}
                                                                - {{ $entidade->contaContabil->name }}</span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <!-- Descrição -->
                                                    <td class="text-end">{{ $entidade->descricao ?? '-' }}
                                                    </td>
                                                    @if (auth()->user()->hasRole(['admin', 'global', 'admin_user']))
                                                        <!-- Ações -->
                                                        <td class="text-end">
                                                            <button type="button"
                                                                class="btn btn-sm btn-icon btn-light-primary btn-edit-entidade"
                                                                data-entidade-id="{{ $entidade->id }}"
                                                                data-entidade-tipo="{{ $entidade->tipo }}"
                                                                data-entidade-nome="{{ htmlspecialchars($entidade->nome, ENT_QUOTES, 'UTF-8') }}"
                                                                data-entidade-banco-id="{{ $entidade->banco_id ?? '' }}"
                                                                data-entidade-agencia="{{ $entidade->agencia ?? '' }}"
                                                                data-entidade-conta="{{ $entidade->conta ?? '' }}"
                                                                data-entidade-account-type="{{ $entidade->account_type ?? '' }}"
                                                                data-entidade-descricao="{{ htmlspecialchars($entidade->descricao ?? '', ENT_QUOTES, 'UTF-8') }}"
                                                                data-entidade-conta-contabil-id="{{ $entidade->conta_contabil_id ?? '' }}"
                                                                data-bs-toggle="tooltip" title="Editar">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                        </td>
                                                    @endif
                                                </tr>
                                            @empty
                                            @endforelse
                                        </tbody>
                                        <!--end::Table body-->
                                    </table>
                                    <!--end::Table-->
                                </div>
                                <!--end::Card body-->

                            </div>
                            <!--end::Products-->
                        </div>
                        <!--end::Card-->
                    </div>
                    <!--end::Engage widget 1-->
                </div>
                <!--end::Col-->
            </div>
            <!--end::Row-->

            <!--begin::Drawer de Edição-->
            @if (auth()->user()->hasRole(['admin', 'global']))
                <div id="kt_drawer_edit_entidade" class="bg-white" data-kt-drawer="true"
                    data-kt-drawer-name="edit_entidade" data-kt-drawer-activate="true"
                    data-kt-drawer-toggle="#kt_drawer_edit_entidade_toggle"
                    data-kt-drawer-close="#kt_drawer_edit_entidade_close" data-kt-drawer-overlay="true"
                    data-kt-drawer-width="{default:'300px', 'md': '500px', 'lg': '600px'}"
                    data-kt-drawer-direction="end">
                    <!--begin::Card-->
                    <div class="card rounded-0 w-100">
                        <!--begin::Card header-->
                        <div class="card-header pe-5">
                            <!--begin::Title-->
                            <div class="card-title">
                                <div class="d-flex align-items-center">
                                    <i class="ki-duotone ki-pencil fs-2 text-primary me-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <h2 class="fw-bold mb-0">Editar Entidade Financeira</h2>
                                </div>
                            </div>
                            <!--end::Title-->
                            <!--begin::Card toolbar-->
                            <div class="card-toolbar">
                                <div class="btn btn-sm btn-icon btn-active-light-primary"
                                    id="kt_drawer_edit_entidade_close">
                                </div>
                            </div>
                            <!--end::Card toolbar-->
                        </div>
                        <!--end::Card header-->

                        <!--begin::Card body-->
                        <div class="card-body hover-scroll-overlay-y py-10 px-7">
                            <form id="kt_drawer_edit_entidade_form" method="POST" action=""
                                data-base-url="{{ route('entidades.index') }}" novalidate>
                                @csrf
                                <input type="hidden" name="entidade_id" id="edit_entidade_id">
                                <input type="hidden" id="edit_tipo_hidden" value="">

                                <!--begin::Tipo badge-->
                                <div class="mb-7">
                                    <label class="fs-6 fw-semibold mb-2 text-gray-500">Tipo</label>
                                    <div class="d-flex align-items-center">
                                        <input type="text" class="form-control form-control-solid bg-light-primary text-primary fw-bold"
                                            id="edit_tipo" readonly disabled style="max-width: 180px;">
                                    </div>
                                </div>
                                <!--end::Tipo badge-->

                                <!--begin::Separator-->
                                <div class="separator separator-dashed my-6"></div>

                                <!-- Nome (para tipo Caixa) -->
                                <div class="mb-5" id="edit_nome-group">
                                    <label class="required fs-5 fw-semibold mb-2">Nome da Entidade</label>
                                    <input type="text" class="form-control form-control-solid" name="nome"
                                        id="edit_nome" placeholder="Ex: Caixa Central" />
                                </div>

                                <!-- Banco (para tipo Banco) -->
                                <div class="d-none" id="edit_banco-group">
                                    <x-tenant-select
                                        name="bank_id"
                                        id="edit_banco-select"
                                        label="Banco"
                                        :required="true"
                                        class="mb-5 w-100"
                                        placeholder="Selecione um banco"
                                        dropdownParent="#kt_drawer_edit_entidade"
                                        :allowClear="true">
                                        @isset($banks)
                                            @foreach ($banks as $bank)
                                                <option value="{{ $bank->id }}" data-icon="{{ $bank->logo_url }}">
                                                    {{ $bank->name }}
                                                </option>
                                            @endforeach
                                        @endisset
                                    </x-tenant-select>
                                </div>

                                <!-- Agência, Conta e Natureza (para tipo Banco) -->
                                <div class="row mb-5 d-none" id="edit_banco-details-group">
                                    <div class="col-md-4">
                                        <label class="required fs-5 fw-semibold mb-2">Agência</label>
                                        <input type="text" class="form-control form-control-solid" name="agencia"
                                            id="edit_agencia" placeholder="Agência" />
                                    </div>
                                    <div class="col-md-4">
                                        <label class="required fs-5 fw-semibold mb-2">Conta</label>
                                        <input type="text" class="form-control form-control-solid" name="conta"
                                            id="edit_conta" placeholder="Conta" />
                                    </div>
                                    <x-tenant-select
                                        name="account_type"
                                        id="edit_account_type"
                                        label="Natureza"
                                        :required="true"
                                        class="col-md-4"
                                        placeholder="Selecione"
                                        dropdownParent="#kt_drawer_edit_entidade"
                                        :hideSearch="true">
                                        <option value="corrente">Conta Corrente</option>
                                        <option value="poupanca">Poupança</option>
                                        <option value="aplicacao">Aplicação</option>
                                        <option value="renda_fixa">Renda Fixa</option>
                                        <option value="tesouro_direto">Tesouro Direto</option>
                                    </x-tenant-select>
                                </div>

                                <!--begin::Separator-->
                                <div class="separator separator-dashed my-6"></div>

                                <!-- Descrição -->
                                <div class="mb-5">
                                    <label class="fs-5 fw-semibold mb-2">Descrição</label>
                                    <textarea class="form-control form-control-solid" rows="3" name="descricao" id="edit_descricao"
                                        placeholder="Insira uma descrição (opcional)"></textarea>
                                </div>

                                <!-- Conta Contábil -->
                                <x-tenant-select
                                    name="conta_contabil_id"
                                    id="edit_conta_contabil_id"
                                    label="Conta Contábil (Plano de Contas)"
                                    class="mb-5 w-100"
                                    placeholder="Selecione a conta contábil..."
                                    dropdownParent="#kt_drawer_edit_entidade"
                                    :allowClear="true"
                                    tooltip="Vínculo contábil para exportação (De/Para)">
                                    @isset($contas)
                                        @foreach ($contas as $conta)
                                            <option value="{{ $conta->id }}">
                                                {{ $conta->code }} - {{ $conta->name }}
                                            </option>
                                        @endforeach
                                    @endisset
                                </x-tenant-select>

                                <!--begin::Separator-->
                                <div class="separator separator-dashed my-6"></div>

                                <!-- Saldo Inicial e Atual (readonly) -->
                                <div class="row mb-7">
                                    <div class="col-md-6">
                                        <label class="fs-6 fw-semibold mb-2 text-gray-500">Saldo Inicial</label>
                                        <input type="text" class="form-control form-control-solid bg-light"
                                            id="edit_saldo_inicial" readonly disabled>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="fs-6 fw-semibold mb-2 text-gray-500">Saldo Atual</label>
                                        <input type="text" class="form-control form-control-solid bg-light"
                                            id="edit_saldo_atual" readonly disabled>
                                    </div>
                                </div>

                                <!--begin::Actions-->
                                <div class="d-flex justify-content-end pt-5">
                                    <div class="d-flex gap-3">
                                        <button type="button" class="btn btn-light"
                                            id="kt_drawer_edit_entidade_close_btn">Cancelar</button>
                                        <button type="button" class="btn btn-primary"
                                            id="kt_drawer_edit_entidade_submit">
                                            <span class="indicator-label">
                                                <i class="bi bi-x-lg fs-4 me-1"></i>
                                                Salvar Alterações
                                            </span>
                                            <span class="indicator-progress">Salvando...
                                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                            </span>
                                        </button>
                                    </div>
                                </div>
                                <!--end::Actions-->
                            </form>
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Card-->
                </div>
            @endif
            <!--end::Drawer de Edição-->
        </div>
        <!--end::Content container-->
    </div>
    <!--end::Content-->

</x-tenant-app-layout>

<!--begin::Vendors Javascript(used for this page only)-->
<script src="/tenancy/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<!--end::Vendors Javascript-->
<!--begin::Custom Javascript(used for this page only)-->
<script src="/tenancy/assets/js/custom/apps/entidades/entidade-manager.js"></script>

