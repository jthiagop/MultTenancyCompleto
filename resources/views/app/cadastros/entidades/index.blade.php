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
                                            <div class="col-md-6 fv-row d-none" id="agencia-group">
                                                <label class="fs-5 fw-semibold mb-2">Agência</label>
                                                <input type="text" class="form-control form-control-solid"
                                                    placeholder="Número da agência" name="agencia"
                                                    value="{{ old('agencia') }}" />
                                                @error('agencia')
                                                    <div class="text-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6 fv-row d-none" id="conta-group">
                                                <label class="fs-5 fw-semibold mb-2">Conta</label>
                                                <input type="text" class="form-control form-control-solid"
                                                    placeholder="Número da conta" name="conta"
                                                    value="{{ old('conta') }}" />
                                                @error('conta')
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
                                            <!--begin::Card toolbar-->
                                            <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                                                <!--begin::Export dropdown-->
                                                <button type="button" class="btn btn-light-primary"
                                                    data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr078.svg-->
                                                    <span class="svg-icon svg-icon-2">
                                                        <svg width="24" height="24" viewBox="0 0 24 24"
                                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <rect opacity="0.3" x="12.75" y="4.25" width="12"
                                                                height="2" rx="1"
                                                                transform="rotate(90 12.75 4.25)"
                                                                fill="currentColor" />
                                                            <path
                                                                d="M12.0573 6.11875L13.5203 7.87435C13.9121 8.34457 14.6232 8.37683 15.056 7.94401C15.4457 7.5543 15.4641 6.92836 15.0979 6.51643L12.4974 3.59084C12.0996 3.14332 11.4004 3.14332 11.0026 3.59084L8.40206 6.51643C8.0359 6.92836 8.0543 7.5543 8.44401 7.94401C8.87683 8.37683 9.58785 8.34458 9.9797 7.87435L11.4427 6.11875C11.6026 5.92684 11.8974 5.92684 12.0573 6.11875Z"
                                                                fill="currentColor" />
                                                            <path opacity="0.3"
                                                                d="M18.75 8.25H17.75C17.1977 8.25 16.75 8.69772 16.75 9.25C16.75 9.80228 17.1977 10.25 17.75 10.25C18.3023 10.25 18.75 10.6977 18.75 11.25V18.25C18.75 18.8023 18.3023 19.25 17.75 19.25H5.75C5.19772 19.25 4.75 18.8023 4.75 18.25V11.25C4.75 10.6977 5.19771 10.25 5.75 10.25C6.30229 10.25 6.75 9.80228 6.75 9.25C6.75 8.69772 6.30229 8.25 5.75 8.25H4.75C3.64543 8.25 2.75 9.14543 2.75 10.25V19.25C2.75 20.3546 3.64543 21.25 4.75 21.25H18.75C19.8546 21.25 20.75 20.3546 20.75 19.25V10.25C20.75 9.14543 19.8546 8.25 18.75 8.25Z"
                                                                fill="currentColor" />
                                                        </svg>
                                                    </span>
                                                    <!--end::Svg Icon-->Export Report</button>
                                                <!--begin::Menu-->
                                                <div id="kt_ecommerce_report_sales_export_menu"
                                                    class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-200px py-4"
                                                    data-kt-menu="true">
                                                    <!--begin::Menu item-->
                                                    <div class="menu-item px-3">
                                                        <a href="#" class="menu-link px-3"
                                                            data-kt-ecommerce-export="copy">Copy to clipboard</a>
                                                    </div>
                                                    <!--end::Menu item-->
                                                    <!--begin::Menu item-->
                                                    <div class="menu-item px-3">
                                                        <a href="#" class="menu-link px-3"
                                                            data-kt-ecommerce-export="excel">Export as Excel</a>
                                                    </div>
                                                    <!--end::Menu item-->
                                                    <!--begin::Menu item-->
                                                    <div class="menu-item px-3">
                                                        <a href="#" class="menu-link px-3"
                                                            data-kt-ecommerce-export="csv">Export as CSV</a>
                                                    </div>
                                                    <!--end::Menu item-->
                                                    <!--begin::Menu item-->
                                                    <div class="menu-item px-3">
                                                        <a href="#" class="menu-link px-3"
                                                            data-kt-ecommerce-export="pdf">Export as PDF</a>
                                                    </div>
                                                    <!--end::Menu item-->
                                                </div>
                                                <!--end::Menu-->
                                                <!--end::Export dropdown-->
                                            </div>
                                            <!--end::Card toolbar-->
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

        // Função para exibir/esconder campos
        function toggleFields() {
            const selected = tipoSelect.value;
            if (selected === 'banco') {
                nomeEntidadeGroup.classList.add('d-none'); // Esconde Nome da Entidade
                bancoGroup.classList.remove('d-none'); // Mostra o select de Banco
                agenciaGroup.classList.remove('d-none'); // Mostra Agência
                contaGroup.classList.remove('d-none'); // Mostra Conta
            } else {
                nomeEntidadeGroup.classList.remove('d-none');
                bancoGroup.classList.add('d-none');
                agenciaGroup.classList.add('d-none');
                contaGroup.classList.add('d-none');
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
</script>
