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
                            Criação de Entidade Financeira</h1>
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
                            <li class="breadcrumb-item text-muted">Cadastros</li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-400 w-5px h-2px"></span>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">Criação de Entidades</li>
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
                    <!--begin::Row-->
                    <div class="row gy-5 g-xl-10">
                        <!--begin::Col-->
                        <div class="col-xl-6 mb-xl-10">
                            <!--begin::Chart widget 5-->
                            <div class="card card-flush h-lg-100">
                                <div class="card-body">
                                    <!--begin:Form-->
                                    <form method="POST" action="{{ route('entidades.store') }}" class="form mb-15">
                                        @csrf <!-- Token CSRF obrigatório para proteção -->

                                        <!--begin::Heading-->
                                        <div class="mb-13 text-center">
                                            <!--begin::Title-->
                                            <h1 class="mb-3">Cadastrar Nova Entidade</h1>
                                            <!--end::Title-->
                                            <!--begin::Description-->
                                            <div class="text-muted fw-semibold fs-5">
                                                Preencha os detalhes da nova
                                                <a href="#" class="fw-bold link-primary">Entidade Financeira</a>.
                                            </div>
                                            <!--end::Description-->
                                        </div>
                                        <!--end::Heading-->
                                        <div class="row mb-5">
                                            <!--begin::Col-->
                                            <div class="col-md-4 fv-row">
                                                <label class="fs-5 fw-semibold mb-2">Tipo</label>
                                                <select name="tipo" id="tipo"
                                                    class="form-select form-select-solid" required>
                                                    <option value="" disabled selected>Selecione o tipo</option>
                                                    <option value="caixa"
                                                        {{ old('tipo') == 'caixa' ? 'selected' : '' }}>Caixa</option>
                                                    <option value="banco"
                                                        {{ old('tipo') == 'banco' ? 'selected' : '' }}>Banco</option>
                                                </select>
                                                @error('tipo')
                                                    <div class="text-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <!--end::Col-->

                                            <!--begin::Col-->
                                            <div class="col-md-8 fv-row" id="nome-entidade-group">
                                                <label class="fs-5 fw-semibold mb-2">Nome da Entidade</label>
                                                <input type="text" class="form-control form-control-solid"
                                                    placeholder="Ex: Caixa Central" name="nome"
                                                    value="{{ old('nome') }}" />
                                                @error('nome')
                                                    <div class="text-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <!--end::Col-->
                                            <!-- Campos para Banco (inicialmente ocultos) -->
                                            <!-- Grupo de Banco (Oculto por padrão) -->
                                            <div class="col-md-8 fv-row d-none" id="banco-group">
                                                <label class="fs-5 fw-semibold mb-2">Banco</label>
                                                <select id="banco-select" name="bank_id"
                                                    class="form-select form-select-solid" data-control="select2"
                                                    data-placeholder="Selecione um banco">
                                                    <option></option> <!-- para placeholder vazio -->

                                                    {{-- A lista de bancos agora vem do controller --}}
                                                    @isset($banks)
                                                        @foreach ($banks as $bank)
                                                            {{--
                                                                A CORREÇÃO ESTÁ AQUI:
                                                                Removemos a função asset() e passamos o $bank->logo_path diretamente.
                                                            --}}
                                                            <option value="{{ $bank->id }}"
                                                                data-icon="{{ $bank->logo_path }}">
                                                                {{ $bank->name }}
                                                            </option>
                                                        @endforeach
                                                    @endisset
                                                </select>

                                                @error('bank_id')
                                                    <div class="text-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <!--end::Input group-->
                                        </div>
                                        <!--end::Input group-->
                                        <!--begin::Input group-->
                                        <div class="row mb-5">
                                            <div class="col-md-4 fv-row d-none" id="agencia-group">
                                                <label class="fs-5 fw-semibold mb-2">Agência</label>
                                                <input type="text" class="form-control form-control-solid"
                                                    placeholder="Número da agência" name="agencia"
                                                    value="{{ old('agencia') }}" />
                                                @error('agencia')
                                                    <div class="text-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-4 fv-row d-none" id="conta-group">
                                                <label class="fs-5 fw-semibold mb-2">Conta</label>
                                                <input type="text" class="form-control form-control-solid"
                                                    placeholder="Número da conta" name="conta"
                                                    value="{{ old('conta') }}" />
                                                @error('conta')
                                                    <div class="text-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-4 fv-row d-none" id="account-type-group">
                                                <label class="fs-5 fw-semibold mb-2">Natureza da Conta</label>
                                                <select name="account_type" id="account_type"
                                                    class="form-select form-select-solid">
                                                    <option value="" disabled selected>Selecione a natureza</option>
                                                    <option value="corrente" {{ old('account_type') == 'corrente' ? 'selected' : '' }}>Conta Corrente</option>
                                                    <option value="poupanca" {{ old('account_type') == 'poupanca' ? 'selected' : '' }}>Poupança</option>
                                                    <option value="aplicacao" {{ old('account_type') == 'aplicacao' ? 'selected' : '' }}>Aplicação</option>
                                                    <option value="renda_fixa" {{ old('account_type') == 'renda_fixa' ? 'selected' : '' }}>Renda Fixa</option>
                                                    <option value="tesouro_direto" {{ old('account_type') == 'tesouro_direto' ? 'selected' : '' }}>Tesouro Direto</option>
                                                </select>
                                                @error('account_type')
                                                    <div class="text-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!--begin::Linha Saldo Inicial / Saldo Atual-->
                                        <div class="row mb-5">
                                            <!--begin::Col-->
                                            <div class="col-md-6 fv-row">
                                                <!--begin::Label-->
                                                <label class="fs-5 fw-semibold mb-2">Saldo Inicial</label>
                                                <!--end::Label-->
                                                <div class="position-relative d-flex align-items-center">
                                                    <!--begin::Icon-->
                                                    <!--begin::Svg Icon | path: icons/duotune/general/gen014.svg-->
                                                    <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                                        <svg class="icon icon-tabler icon-tabler-currency-real"
                                                            fill="none" height="24" stroke="currentColor"
                                                            stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" viewBox="0 0 24 24" width="24"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <!-- O preenchimento inicial não está definido -->
                                                            <path d="M0 0h24v24H0z" fill="none" stroke="none">
                                                            </path>
                                                            <!-- Desenha a primeira linha que representa o símbolo da moeda -->
                                                            <path d="M21 6h-4a3 3 0 0 0 0 6h1a3 3 0 0 1 0 6h-4"></path>
                                                            <!-- Traça a segunda linha da moeda -->
                                                            <path d="M4 18v-12h3a3 3 0 1 1 0 6h-3c5.5 0 5 4 6 6"></path>
                                                            <!-- Traça duas linhas verticais curtas -->
                                                            <path d="M18 6v-2"></path>
                                                            <path d="M17 20v-2"></path>
                                                        </svg>
                                                    </span>
                                                    <!--end::Svg Icon-->
                                                    <!--end::Icon-->
                                                    <!--begin::Input-->
                                                    <input type="text"
                                                        class="form-control form-control-solid ps-12 money"
                                                        placeholder="Ex: 1.000,00" id="valor2"
                                                        name="saldo_inicial" required />
                                                    <!--end::Input-->
                                                    @error('saldo_inicial')
                                                        <div class="text-danger mt-2">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <!--end::Col-->

                                            <!--begin::Col-->
                                            <div class="col-md-6 fv-row">
                                                <!--begin::Label-->
                                                <label class="fs-5 fw-semibold mb-2">Saldo Atual</label>
                                                <!--end::Label-->
                                                <div class="position-relative d-flex align-items-center">
                                                    <!--begin::Icon-->
                                                    <!--begin::Svg Icon | path: icons/duotune/general/gen014.svg-->
                                                    <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                                        <svg class="icon icon-tabler icon-tabler-currency-real"
                                                            fill="none" height="24" stroke="currentColor"
                                                            stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" viewBox="0 0 24 24" width="24"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <!-- O preenchimento inicial não está definido -->
                                                            <path d="M0 0h24v24H0z" fill="none" stroke="none">
                                                            </path>
                                                            <!-- Desenha a primeira linha que representa o símbolo da moeda -->
                                                            <path d="M21 6h-4a3 3 0 0 0 0 6h1a3 3 0 0 1 0 6h-4"></path>
                                                            <!-- Traça a segunda linha da moeda -->
                                                            <path d="M4 18v-12h3a3 3 0 1 1 0 6h-3c5.5 0 5 4 6 6"></path>
                                                            <!-- Traça duas linhas verticais curtas -->
                                                            <path d="M18 6v-2"></path>
                                                            <path d="M17 20v-2"></path>
                                                        </svg>
                                                    </span>
                                                    <!--end::Svg Icon-->
                                                    <!--end::Icon-->
                                                    <!--begin::Input-->
                                                    <input type="text"
                                                        class="form-control form-control-solid ps-12 money"
                                                        placeholder="Ex: 1.000,00" id="valor2" name="saldo_atual"
                                                        disabled />
                                                    <!--end::Input-->
                                                    @error('saldo_atual')
                                                        <div class="text-danger mt-2">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <!--end::Col-->
                                        </div>
                                        <!--end::Input group-->
                                        <!--end::Linha Saldo-->

                                        <!--begin::Descrição-->
                                        <div class="d-flex flex-column mb-5 fv-row">
                                            <label class="fs-5 fw-semibold mb-2">Descrição</label>
                                            <textarea class="form-control form-control-solid" rows="4" name="descricao"
                                                placeholder="Insira uma descrição (opcional)"></textarea>
                                            @error('descricao')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <!--end::Descrição-->

                                        <!--begin::Notice (Opcional)-->
                                        <div
                                            class="notice d-flex bg-light-primary rounded border-primary border border-dashed mb-9 p-6">
                                            <span class="svg-icon svg-icon-2tx svg-icon-primary me-4">
                                                <!-- Ícone ilustrativo -->
                                                <svg width="24" height="24" viewBox="0 0 24 24"
                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path opacity="0.3" d="M3.20001 5.91897L16.9 3.01895C17.4 2.91895
                                                        18 3.219 18.1 3.819L19.2 9.01895L3.20001 5.91897Z"
                                                        fill="currentColor" />
                                                    <path opacity="0.3" d="M13 13.9189C13 12.2189 14.3 10.9189
                                                        16 10.9189H21C21.6 10.9189 22 11.3189 22
                                                        11.9189V15.9189C22 16.5189 21.6 16.9189
                                                        21 16.9189H16C14.3 16.9189 13 15.6189
                                                        13 13.9189ZM16 12.4189C15.2 12.4189 14.5
                                                        13.1189 14.5 13.9189C14.5 14.7189 15.2
                                                        15.4189 16 15.4189C16.8 15.4189 17.5
                                                        14.7189 17.5 13.9189C17.5 13.1189 16.8
                                                        12.4189 16 12.4189Z" fill="currentColor" />
                                                    <path d="M13 13.9189C13 12.2189 14.3 10.9189
                                                    16 10.9189H21V7.91895C21 6.81895 20.1
                                                    5.91895 19 5.91895H3C2.4 5.91895
                                                    2 6.31895 2 6.91895V20.9189C2
                                                    21.5189 2.4 21.9189 3
                                                    21.9189H19C20.1 21.9189 21
                                                    21.0189 21 19.9189V16.9189H16C14.3
                                                    16.9189 13 15.6189 13 13.9189Z" fill="currentColor" />
                                                </svg>
                                            </span>
                                            <div class="d-flex flex-stack flex-grow-1">
                                                <div class="fw-semibold">
                                                    <h4 class="text-gray-900 fw-bold">Dica</h4>
                                                    <div class="fs-6 text-gray-700">
                                                        Certifique-se de preencher corretamente todos os campos
                                                        obrigatórios.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end::Notice-->

                                        <!--begin::Actions-->
                                        <div class="text-center">
                                            <button type="reset" class="btn btn-light me-3"
                                                data-kt-modal-action-type="cancel">
                                                Cancelar
                                            </button>
                                            <button type="submit" class="btn btn-primary"
                                                id="kt_modal_submit_button">
                                                <span class="indicator-label">Enviar</span>
                                                <span class="indicator-progress">
                                                    Aguarde...
                                                    <span
                                                        class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                                </span>
                                            </button>
                                        </div>
                                        <!--end::Actions-->
                                    </form>
                                    <!--end:Form-->
                                </div>
                            </div>
                            <!--end::Chart widget 5-->
                        </div>
                        <!--end::Col-->
                        <!--begin::Col-->
                        <div class="col-xl-6 mb-5 mb-xl-10">
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
                                                    Entidades Financeiras
                                                </div>
                                            </div>
                                            <!--end::Card title-->
                                        </div>
                                        <!--end::Card header-->
                                        <!--begin::Card body-->
                                        <div class="card-body pt-0">
                                            <!--begin::Table-->
                                            <table class="table align-middle table-row-dashed fs-6 gy-5"
                                                id="kt_ecommerce_report_sales_table">
                                                <!--begin::Table head-->
                                                <thead>
                                                    <!--begin::Table row-->
                                                    <tr
                                                        class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                                        <th class="min-w-250px">Nome</th>
                                                        <th class="text-end min-w-175px">Saldo Inicial</th>
                                                        <th class="text-end min-w-150px">Ultima Atualização</th>
                                                        <th class="text-end min-w-150px">Saldo Atual</th>
                                                        <th class="text-end min-w-120px">Tipo</th>
                                                        <th class="text-end min-w-300px">Descrição</th>
                                                        @if(auth()->user()->hasRole(['admin', 'global']))
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
                                                                {{ number_format($entidade->saldo_inicial, 2, ',', '.') }}
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
                                                            <!-- Descrição -->
                                                            <td class="text-end">{{ $entidade->descricao ?? '-' }}
                                                            </td>
                                                            @if(auth()->user()->hasRole(['admin', 'global', 'admin_user']))
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
                                                                            data-bs-toggle="tooltip"
                                                                            title="Editar">
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
                    @if(auth()->user()->hasRole(['admin', 'global']))
                    <div id="kt_drawer_edit_entidade"
                         class="bg-white"
                         data-kt-drawer="true"
                         data-kt-drawer-name="edit_entidade"
                         data-kt-drawer-activate="true"
                         data-kt-drawer-toggle="#kt_drawer_edit_entidade_toggle"
                         data-kt-drawer-close="#kt_drawer_edit_entidade_close"
                         data-kt-drawer-overlay="true"
                         data-kt-drawer-width="{default:'300px', 'md': '500px', 'lg': '600px'}"
                         data-kt-drawer-direction="end">
                        <!--begin::Card-->
                        <div class="card rounded-0 w-100">
                            <!--begin::Card header-->
                            <div class="card-header pe-5">
                                <!--begin::Title-->
                                <div class="card-title">
                                    <h2 class="fw-bold">Editar Entidade Financeira</h2>
                                </div>
                                <!--end::Title-->
                                <!--begin::Card toolbar-->
                                <div class="card-toolbar">
                                    <!--begin::Close-->
                                    <div class="btn btn-sm btn-icon btn-active-light-primary" id="kt_drawer_edit_entidade_close">
                                        <i class="fas fa-times fs-1"></i>
                                    </div>
                                    <!--end::Close-->
                                </div>
                                <!--end::Card toolbar-->
                            </div>
                            <!--end::Card header-->

                            <!--begin::Card body-->
                            <div class="card-body hover-scroll-overlay-y py-10 px-7">
                                <form id="kt_drawer_edit_entidade_form" method="POST" action="">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="entidade_id" id="edit_entidade_id">

                                    <!-- Tipo (readonly) -->
                                    <div class="mb-5">
                                        <label class="fs-5 fw-semibold mb-2">Tipo</label>
                                        <input type="text" class="form-control form-control-solid"
                                               id="edit_tipo" readonly disabled>
                                    </div>

                                    <!-- Nome (para tipo Caixa) -->
                                    <div class="mb-5" id="edit_nome-group">
                                        <label class="fs-5 fw-semibold mb-2">Nome da Entidade</label>
                                        <input type="text" class="form-control form-control-solid"
                                               name="nome" id="edit_nome"
                                               placeholder="Ex: Caixa Central" />
                                        @error('nome')
                                            <div class="text-danger mt-2">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Banco (para tipo Banco) -->
                                    <div class="mb-5 d-none" id="edit_banco-group">
                                        <label class="fs-5 fw-semibold mb-2">Banco</label>
                                        <select id="edit_banco-select" name="bank_id"
                                            class="form-select form-select-solid" data-control="select2"
                                            data-placeholder="Selecione um banco">
                                            <option></option>
                                            @isset($banks)
                                                @foreach ($banks as $bank)
                                                    <option value="{{ $bank->id }}"
                                                        data-icon="{{ $bank->logo_path }}">
                                                        {{ $bank->name }}
                                                    </option>
                                                @endforeach
                                            @endisset
                                        </select>
                                        @error('bank_id')
                                            <div class="text-danger mt-2">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Agência, Conta e Natureza (para tipo Banco) -->
                                    <div class="row mb-5 d-none" id="edit_banco-details-group">
                                        <div class="col-md-4">
                                            <label class="fs-5 fw-semibold mb-2">Agência</label>
                                            <input type="text" class="form-control form-control-solid"
                                                   name="agencia" id="edit_agencia"
                                                   placeholder="Número da agência" />
                                            @error('agencia')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4">
                                            <label class="fs-5 fw-semibold mb-2">Conta</label>
                                            <input type="text" class="form-control form-control-solid"
                                                   name="conta" id="edit_conta"
                                                   placeholder="Número da conta" />
                                            @error('conta')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4">
                                            <label class="fs-5 fw-semibold mb-2">Natureza da Conta</label>
                                            <select name="account_type" id="edit_account_type"
                                                class="form-select form-select-solid">
                                                <option value="" disabled>Selecione a natureza</option>
                                                <option value="corrente">Conta Corrente</option>
                                                <option value="poupanca">Poupança</option>
                                                <option value="aplicacao">Aplicação</option>
                                                <option value="renda_fixa">Renda Fixa</option>
                                                <option value="tesouro_direto">Tesouro Direto</option>
                                            </select>
                                            @error('account_type')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Descrição -->
                                    <div class="mb-5">
                                        <label class="fs-5 fw-semibold mb-2">Descrição</label>
                                        <textarea class="form-control form-control-solid"
                                                  rows="4" name="descricao" id="edit_descricao"
                                                  placeholder="Insira uma descrição (opcional)"></textarea>
                                        @error('descricao')
                                            <div class="text-danger mt-2">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Saldo Inicial e Atual (readonly) -->
                                    <div class="row mb-5">
                                        <div class="col-md-6">
                                            <label class="fs-5 fw-semibold mb-2">Saldo Inicial</label>
                                            <input type="text" class="form-control form-control-solid"
                                                   id="edit_saldo_inicial" readonly disabled>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="fs-5 fw-semibold mb-2">Saldo Atual</label>
                                            <input type="text" class="form-control form-control-solid"
                                                   id="edit_saldo_atual" readonly disabled>
                                        </div>
                                    </div>

                                    <!--begin::Card footer-->
                                    <div class="card-footer">
                                        <div class="d-flex justify-content-end gap-2">
                                            <button type="button" class="btn btn-light" id="kt_drawer_edit_entidade_close_btn">Cancelar</button>
                                            <button type="submit" class="btn btn-primary">
                                                <span class="indicator-label">Salvar Alterações</span>
                                                <span class="indicator-progress">Aguarde...
                                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                    <!--end::Card footer-->
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
        </div>
        <!--end::Content wrapper-->
    </div>
    <!--end:::Main-->
</x-tenant-app-layout>

<!--begin::Vendors Javascript(used for this page only)-->
<script src="/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<!--end::Vendors Javascript-->
<!--begin::Custom Javascript(used for this page only)-->
<script src="/assets/js/custom/apps/ecommerce/reports/sales/sales.js"></script>
<script src="/assets/js/custom/utilities/modals/bidding.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tipoSelect = document.getElementById('tipo');
        const nomeEntidadeGroup = document.getElementById('nome-entidade-group');
        const bancoGroup = document.getElementById('banco-group');
        const agenciaGroup = document.getElementById('agencia-group');
        const contaGroup = document.getElementById('conta-group');
        const accountTypeGroup = document.getElementById('account-type-group');

        // Função para exibir/esconder campos
        function toggleFields() {
            const selected = tipoSelect.value;
            if (selected === 'banco') {
                nomeEntidadeGroup.classList.add('d-none'); // Esconde Nome da Entidade
                bancoGroup.classList.remove('d-none'); // Mostra o select de Banco
                agenciaGroup.classList.remove('d-none'); // Mostra Agência
                contaGroup.classList.remove('d-none'); // Mostra Conta
                accountTypeGroup.classList.remove('d-none'); // Mostra Natureza da Conta
            } else {
                nomeEntidadeGroup.classList.remove('d-none');
                bancoGroup.classList.add('d-none');
                agenciaGroup.classList.add('d-none');
                contaGroup.classList.add('d-none');
                accountTypeGroup.classList.add('d-none');
            }
        }

        // Evento de mudança no select "tipo"
        tipoSelect.addEventListener('change', toggleFields);

        // Ao carregar a página, se "tipo=banco" já estiver selecionado (ex.: old value),
        // podemos chamar toggleFields() para exibir/esconder adequadamente.
        toggleFields();
    });
</script>
<script>
    $(document).ready(function() {
        $('#banco-select').select2({
            placeholder: "Selecione um banco",
            allowClear: true,

            // Exibir ícone no menu suspenso
            templateResult: function(state) {
                // Se for placeholder ou sem valor, retornar o texto normal
                if (!state.id) {
                    return state.text;
                }

                // Recupera o caminho do ícone do atributo data-icon
                let iconUrl = $(state.element).attr('data-icon');
                if (!iconUrl) {
                    return state.text;
                }

                // Monta um elemento com img + texto
                let $state = $(`
                    <span class="d-flex align-items-center">
                        <img src="${iconUrl}" class="me-2" style="width:24px; height:24px;" />
                        <span>${state.text}</span>
                    </span>
                `);

                return $state;
            },

            // Exibir ícone na opção selecionada
            templateSelection: function(state) {
                if (!state.id) {
                    return state.text;
                }

                let iconUrl = $(state.element).attr('data-icon');
                if (!iconUrl) {
                    return state.text;
                }

                let $state = $(`
                    <span class="d-flex align-items-center">
                        <img src="${iconUrl}" class="me-2" style="width:24px; height:24px;" />
                        <span>${state.text}</span>
                    </span>
                `);
                return $state;
            },
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const submitButton = document.getElementById('kt_modal_submit_button');
        const form = document.querySelector('form'); // Seleciona o formulário

        form.addEventListener('submit', function(e) {
            // Impede o envio do formulário até que o JavaScript seja executado
            e.preventDefault();

            // Mostra o indicador de carregamento
            submitButton.setAttribute('data-kt-indicator', 'on');
            submitButton.disabled = true;

            // Simula um atraso (substitua isso pelo envio real do formulário)
            setTimeout(function() {
                // Oculta o indicador de carregamento
                submitButton.removeAttribute('data-kt-indicator');
                submitButton.disabled = false;

                // Envia o formulário (substitua isso pelo envio real do formulário)
                form.submit();
            }, 2000); // 2 segundos de atraso (apenas para simulação)
        });
    });

    // Função para abrir drawer de edição
    function openEditDrawer(id, tipo, nome, bancoId, agencia, conta, accountType, descricao) {
        console.log('=== Iniciando abertura do drawer ===');
        console.log('Parâmetros recebidos:', { id, tipo, nome, bancoId, agencia, conta, accountType, descricao });

        // Verifica se o drawer existe
        const drawerElement = document.getElementById('kt_drawer_edit_entidade');
        if (!drawerElement) {
            console.error('❌ Drawer não encontrado no DOM!');
            console.log('Tentando encontrar elemento com ID: kt_drawer_edit_entidade');
            alert('Erro: Drawer de edição não encontrado. Verifique se você tem permissão de administrador.');
            return;
        }
        console.log('✅ Drawer encontrado no DOM:', drawerElement);

        // Obtém ou cria instância do Drawer
        let drawer;
        if (typeof KTDrawer !== 'undefined') {
            // Tenta obter instância existente
            drawer = KTDrawer.getInstance(drawerElement);

            // Se não existe, cria uma nova instância
            if (!drawer) {
                console.log('Criando nova instância do Drawer...');
                drawer = new KTDrawer(drawerElement);
            } else {
                console.log('Usando instância existente do Drawer');
            }

            // Garante que o drawer foi criado
            if (!drawer) {
                console.error('Falha ao criar instância do Drawer!');
                alert('Erro: Não foi possível criar o Drawer. Verifique o console para mais detalhes.');
                return;
            }
        } else {
            console.error('KTDrawer não encontrado!');
            alert('Erro: Biblioteca KTDrawer não encontrada. Verifique se os scripts foram carregados corretamente.');
            return;
        }

        const form = document.getElementById('kt_drawer_edit_entidade_form');

        if (!form) {
            console.error('Formulário não encontrado!');
            return;
        }

        // Define a action do form
        const baseUrl = '{{ route('entidades.index') }}';
        form.action = `${baseUrl}/${id}`;
        const entidadeIdInput = document.getElementById('edit_entidade_id');
        if (entidadeIdInput) {
            entidadeIdInput.value = id;
        }

        // Trata valores nulos ou undefined
        nome = nome || '';
        // Converte string 'null' para null
        bancoId = (bancoId === 'null' || bancoId === null || bancoId === undefined) ? null : parseInt(bancoId);
        agencia = agencia || '';
        conta = conta || '';
        accountType = accountType || '';
        descricao = descricao || '';

        try {
            // Preenche os campos
            const tipoInput = document.getElementById('edit_tipo');
            if (tipoInput) {
                tipoInput.value = tipo === 'banco' ? 'Banco' : 'Caixa';
            }

            if (tipo === 'caixa') {
                // Mostra campo de nome, esconde campos de banco
                const nomeGroup = document.getElementById('edit_nome-group');
                const bancoGroup = document.getElementById('edit_banco-group');
                const bancoDetailsGroup = document.getElementById('edit_banco-details-group');
                const nomeInput = document.getElementById('edit_nome');

                if (nomeGroup) nomeGroup.classList.remove('d-none');
                if (bancoGroup) bancoGroup.classList.add('d-none');
                if (bancoDetailsGroup) bancoDetailsGroup.classList.add('d-none');
                if (nomeInput) nomeInput.value = nome;
            } else {
                // Esconde campo de nome, mostra campos de banco
                const nomeGroup = document.getElementById('edit_nome-group');
                const bancoGroup = document.getElementById('edit_banco-group');
                const bancoDetailsGroup = document.getElementById('edit_banco-details-group');
                const agenciaInput = document.getElementById('edit_agencia');
                const contaInput = document.getElementById('edit_conta');
                const accountTypeInput = document.getElementById('edit_account_type');

                if (nomeGroup) nomeGroup.classList.add('d-none');
                if (bancoGroup) bancoGroup.classList.remove('d-none');
                if (bancoDetailsGroup) bancoDetailsGroup.classList.remove('d-none');
                if (agenciaInput) agenciaInput.value = agencia;
                if (contaInput) contaInput.value = conta;
                if (accountTypeInput) accountTypeInput.value = accountType;
            }

            const descricaoInput = document.getElementById('edit_descricao');
            if (descricaoInput) {
                descricaoInput.value = descricao;
            }
        } catch (error) {
            console.error('Erro ao preencher campos:', error);
        }

        // Busca os saldos via AJAX
        const jsonUrl = '{{ route('entidades.index') }}';
        fetch(`${jsonUrl}/${id}/json`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data.entidade) {
                    const entidade = data.data.entidade;
                    const saldoInicialInput = document.getElementById('edit_saldo_inicial');
                    const saldoAtualInput = document.getElementById('edit_saldo_atual');

                    if (saldoInicialInput) {
                        saldoInicialInput.value = 'R$ ' + parseFloat(entidade.saldo_inicial || 0).toLocaleString('pt-BR', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
                    }
                    if (saldoAtualInput) {
                        saldoAtualInput.value = 'R$ ' + parseFloat(entidade.saldo_atual || 0).toLocaleString('pt-BR', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
                    }
                }
            })
            .catch(error => {
                console.error('Erro ao buscar dados:', error);
            });

        // Abre o drawer primeiro
        try {
            console.log('Tentando abrir o drawer...');
            if (drawer && typeof drawer.show === 'function') {
                drawer.show();
                console.log('Drawer.show() chamado com sucesso');
            } else {
                console.error('drawer.show não é uma função!', drawer);
                alert('Erro: Método show() não disponível no Drawer.');
                return;
            }

            // Função para inicializar Select2
            const initSelect2 = () => {
                if (tipo === 'banco') {
                    setTimeout(() => {
                        const bancoSelect = $('#edit_banco-select');
                        if (bancoSelect.length === 0) {
                            console.warn('Select de banco não encontrado');
                            return;
                        }

                        if (bancoSelect.hasClass('select2-hidden-accessible')) {
                            bancoSelect.select2('destroy');
                        }

                        // Define o valor do select antes de inicializar
                        if (bancoId) {
                            bancoSelect.val(bancoId);
                        }

                        if (typeof KTSelect2 !== 'undefined') {
                            new KTSelect2(bancoSelect[0]);
                        } else {
                            bancoSelect.select2({
                                placeholder: "Selecione um banco",
                                allowClear: true,
                                templateResult: function(state) {
                                    if (!state.id) return state.text;
                                    let iconUrl = $(state.element).attr('data-icon');
                                    if (!iconUrl) return state.text;
                                    let html = '<span class="d-flex align-items-center">';
                                    html += '<img src="' + iconUrl + '" class="me-2" style="width:24px; height:24px;" />';
                                    html += '<span>' + state.text + '</span>';
                                    html += '</span>';
                                    return $(html);
                                },
                                templateSelection: function(state) {
                                    if (!state.id) return state.text;
                                    let iconUrl = $(state.element).attr('data-icon');
                                    if (!iconUrl) return state.text;
                                    let html = '<span class="d-flex align-items-center">';
                                    html += '<img src="' + iconUrl + '" class="me-2" style="width:24px; height:24px;" />';
                                    html += '<span>' + state.text + '</span>';
                                    html += '</span>';
                                    return $(html);
                                }
                            });
                        }
                    }, 100);
                }
            };

            // Aguarda o drawer ser exibido antes de inicializar Select2
            drawer.on('kt.drawer.shown', function() {
                initSelect2();
            });

            // Fallback: se o evento não for disparado, tenta inicializar após 500ms
            setTimeout(() => {
                if (tipo === 'banco' && $('#edit_banco-select').length > 0 && !$('#edit_banco-select').hasClass('select2-hidden-accessible')) {
                    initSelect2();
                }
            }, 500);

            // Adiciona evento ao botão de cancelar
            const closeBtn = document.getElementById('kt_drawer_edit_entidade_close_btn');
            if (closeBtn) {
                closeBtn.addEventListener('click', function() {
                    drawer.hide();
                });
            }
        } catch (error) {
            console.error('Erro ao abrir drawer:', error);
            alert('Erro ao abrir drawer de edição. Verifique o console para mais detalhes.');
        }
    }

    // Adiciona event listeners aos botões de editar
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializa o Drawer
        const drawerElement = document.getElementById('kt_drawer_edit_entidade');
        if (drawerElement && typeof KTDrawer !== 'undefined') {
            // Verifica se já foi inicializado
            let drawer = KTDrawer.getInstance(drawerElement);
            if (!drawer) {
                // Inicializa o Drawer se ainda não foi inicializado
                console.log('Inicializando Drawer de edição...');
                drawer = new KTDrawer(drawerElement);
            }
        }

        // Adiciona event listeners aos botões de editar
        const editButtons = document.querySelectorAll('.btn-edit-entidade');
        editButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-entidade-id');
                const tipo = this.getAttribute('data-entidade-tipo');
                const nome = this.getAttribute('data-entidade-nome');
                const bancoId = this.getAttribute('data-entidade-banco-id') || null;
                const agencia = this.getAttribute('data-entidade-agencia') || '';
                const conta = this.getAttribute('data-entidade-conta') || '';
                const accountType = this.getAttribute('data-entidade-account-type') || '';
                const descricao = this.getAttribute('data-entidade-descricao') || '';

                openEditDrawer(id, tipo, nome, bancoId, agencia, conta, accountType, descricao);
            });
        });
    });
</script>
