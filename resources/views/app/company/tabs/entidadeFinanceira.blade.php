
                    <!--begin::Row-->
                    <div class="row gy-5 g-xl-10">
                        <!--begin::Col-->
                        <div class="col-xl-6 mb-xl-10">
                            <!--begin::Chart widget 5-->
                            <div class="card card-flush h-lg-100">
                                <div class="card-body">
                                    <!--begin::Form-->
                                    <form method="POST" action="{{ route('entidades.store') }}" class="form mb-15">
                                        @csrf <!-- Token CSRF obrigatório para proteção -->
                                        <!--begin::Title-->
                                        <!--end::CSRF Token-->
                                        <div class="d-flex flex-column mb-9 fv-row">
                                            <h1 class="fw-bold text-dark mb-7">Cadastrar Nova Entidade Financeira</h1>
                                            <span class="fs-4 fw-semibold text-gray-600 d-block">Preencha os detalhes da
                                                nova entidade financeira.</span>
                                        </div>
                                        <!--end::Title-->
                                        <input type="hidden" name="company_id" value="{{ $companyShow->id }}">

                                        <!--begin::Input group-->
                                        <div class="row mb-5">
                                            <!--begin::Col-->
                                            <div class="col-md-6 fv-row">
                                                <label class="fs-5 fw-semibold mb-2">Nome da Entidade</label>
                                                <input type="text" class="form-control form-control-solid"
                                                    placeholder="Ex: Caixa Central" name="nome"
                                                    value="{{ old('nome') }}" required />
                                                @error('nome')
                                                    <div class="text-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <!--end::Col-->

                                            <!--begin::Col-->
                                            <div class="col-md-6 fv-row">
                                                <label class="fs-5 fw-semibold mb-2">Tipo</label>
                                                <select name="tipo" class="form-select form-select-solid" required>
                                                    <option value="" disabled selected>Selecione o tipo</option>
                                                    <option value="caixa"
                                                        {{ old('tipo') == 'caixa' ? 'selected' : '' }}>Caixa</option>
                                                    <option value="banco"
                                                        {{ old('tipo') == 'banco' ? 'selected' : '' }}>Banco</option>
                                                    <option value="dizimo"
                                                        {{ old('tipo') == 'dizimo' ? 'selected' : '' }}>Dízimo</option>
                                                    <option value="coleta"
                                                        {{ old('tipo') == 'coleta' ? 'selected' : '' }}>Coleta</option>
                                                    <option value="doacao"
                                                        {{ old('tipo') == 'doacao' ? 'selected' : '' }}>Doação</option>
                                                </select>
                                                @error('tipo')
                                                    <div class="text-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <!--end::Col-->
                                        </div>
                                        <!--end::Input group-->

                                        <!--begin::Input group-->
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
                                                        placeholder="Ex: 1.000,00" id="valor2"
                                                        name="saldo_atual" />
                                                    <!--end::Input-->
                                                    @error('saldo_atual')
                                                        <div class="text-danger mt-2">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <!--end::Col-->
                                        </div>
                                        <!--end::Input group-->

                                        <!--begin::Input group-->
                                        <div class="d-flex flex-column mb-5 fv-row">
                                            <!--begin::Label-->
                                            <label class="fs-5 fw-semibold mb-2">Descrição</label>
                                            <!--end::Label-->
                                            <!--begin::Textarea-->
                                            <textarea class="form-control form-control-solid" rows="4" name="descricao"
                                                placeholder="Insira uma descrição para a entidade (opcional)"></textarea>
                                            <!--end::Textarea-->
                                            @error('descricao')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <!--end::Input group-->

                                        <!--begin::Submit-->
                                        <button type="submit" class="btn btn-primary">
                                            <!--begin::Indicator label-->
                                            <span class="indicator-label">Cadastrar Entidade</span>
                                            <!--end::Indicator label-->
                                            <!--begin::Indicator progress-->
                                            <span class="indicator-progress">Aguarde...
                                                <span
                                                    class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                            <!--end::Indicator progress-->
                                        </button>
                                        <!--end::Submit-->
                                    </form>
                                    <!--end::Form-->

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
                                                            <td class="text-end pe-0 {{ $entidade->saldo_atual >= 0 ? 'text-success' : 'text-danger' }}">
                                                                R$ {{ number_format($entidade->saldo_atual, 2, ',', '.') }}
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

<!--begin::Vendors Javascript(used for this page only)-->
<script src="/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<!--end::Vendors Javascript-->
<!--begin::Custom Javascript(used for this page only)-->
<script src="/assets/js/custom/apps/ecommerce/reports/sales/sales.js"></script>
