<!--begin:::Tab content-->

<div class="tab-content" id="myTabContent">
    <!--begin:::Tab pane-->
    <div class="tab-pane fade show active" id="kt_ecommerce_customer_overview" role="tabpanel">

        <!--begin::Card-->
        <div class="card mb-5">
            <!--begin::Card header-->
            <div class="card-header">
                <!--begin::Card title-->
                <div class="card-title fs-3 fw-bold">🪦 Cadastro de Túmulo</div>
                <!--end::Card title-->
            </div>
            <!--end::Card header-->
            <!--begin::Form-->
            <form id="kt_project_settings_form" class="form" action="{{ route('sepultura.store') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <!--begin::Card body-->
                <div class="card-body p-9">
                    <!--begin::Input group-->
                    <div class="row g-9 mb-7">
                        <!--begin::Col-->
                        <div class="col-md-3 fv-row">
                            <!--begin::Label-->
                            <label class="required fs-6 fw-semibold mb-2">Código da Sepultura</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="text" class="form-control form-control-solid" placeholder="T-1234"
                                id="codigo_sepultura" value="{{ old('codigo_sepultura') }}" name="codigo_sepultura" />
                            <!--end::Input-->
                        </div>
                        <div class="col-md-3 fv-row">
                            <!--begin::Label-->
                            <label class="fs-6 fw-semibold mb-2">
                                <span class="required">Tipo</span>
                                <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                    title="Tipo de sepultura"></i>
                            </label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <select class="form-control form-control-solid select2" name="tipo">
                                <option value="terreno">Terreno</option>
                                <option value="mausoléu">Mausoléu</option>
                                <option value="jazigo">Jazigo</option>
                                <option value="urna columbário">Urna Columbário</option>
                                <option value="cripta">Cripta</option>
                                <option value="cova">Cova</option>
                                <option value="sepultura vertical">Sepultura Vertical</option>
                                <option value="cemiterio familiar">Cemitério Familiar</option>
                                <option value="ossário">Ossário</option>
                            </select>
                            <!--end::Input-->
                        </div>
                        <div class="col-md-2 fv-row">
                            <!--begin::Label-->
                            <label class="fs-6 fw-semibold mb-2">
                                <span class="required">Tamanho</span>
                                <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                    title="Tamanho da sepultura"></i>
                            </label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="text"
                                class="form-control form-control-solid @error('tamanho') is-invalid @enderror"
                                name="tamanho" id="area_total" name="tamanho" step="0.01" min="0"
                                value="{{ old('tamanho') }}" placeholder="Exemplo: 12.50" />
                            <!--end::Input-->
                            @error('tamanho')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-2 fv-row">
                            <!--begin::Label-->
                            <label class="fs-4å fw-semibold mb-2">
                                <span class="required">Data de Aquisição</span>
                                <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                    title="Data de aquisição"></i>
                            </label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="date"
                                class="form-control form-control-solid @error('data_aquisicao') is-invalid @enderror"
                                name="data_aquisicao" value="{{ old('data_aquisicao') }}" />
                            <!--end::Input-->
                            @error('data_aquisicao')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-2 fv-row">
                            <!--begin::Label-->
                            <label class="fs-6 fw-semibold mb-2">
                                <span class="required">Status</span>
                                <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                    title="Status da sepultura"></i>
                            </label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <select class="form-control form-control-solid @error('status') is-invalid @enderror"
                                name="status">
                                <option value="disponível" {{ old('status') == 'disponível' ? 'selected' : '' }}>
                                    Disponível
                                </option>
                                <option value="ocupado" {{ old('status') == 'ocupado' ? 'selected' : '' }}>
                                    Ocupado</option>
                                <option value="em manutenção" {{ old('status') == 'em manutenção' ? 'selected' : '' }}>
                                    Em
                                    Manutenção
                                </option>
                            </select>
                            <!--end::Input-->
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group-->
                    <div class="fv-row ">
                        <!--begin::Label-->
                        <label class="fs-6 fw-semibold mb-2">Localizacao</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <textarea class="form-control form-control-solid" rows="4" name="localizacao"
                            placeholder="Localização da sepultura (bloco, quadra, etc."></textarea>
                        <!--end::Input-->
                    </div>
                    <!--end::Input group-->
                </div>
                <!--end::Card body-->
                <!--begin::Card footer-->
                <div class="card-footer d-flex justify-content-end py-6 px-9">
                    <button type="reset" class="btn btn-light btn-active-light-primary me-2">Discard</button>
                    <button type="submit" class="btn btn-primary" id="kt_project_settings_submit">Save Changes</button>
                </div>
                <!--end::Card footer-->
            </form>
            <!--end:Form-->
        </div>
        <!--end::Card-->
        <!--begin::Card-->
        <div class="card pt-4 mb-6 mb-xl-9">
            <!--begin::Card header-->
            <div class="card-header border-0">
                <!--begin::Card title-->
                <div class="card-title">
                    <h2>Lista de Túmulos</h2>
                </div>
                <!--end::Card title-->
                <!--begin::Card toolbar-->
                <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                    <!--begin::Search-->
                    <div class="d-flex align-items-center position-relative my-1">
                        <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                        <span class="svg-icon svg-icon-1 position-absolute ms-4">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2"
                                    rx="1" transform="rotate(45 17.0365 15.1223)" fill="currentColor" />
                                <path
                                    d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z"
                                    fill="currentColor" />
                            </svg>
                        </span>
                        <!--end::Svg Icon-->
                        <input type="text" data-kt-ecommerce-order-filter="search"
                            class="form-control form-control-solid w-250px ps-14" placeholder="Search Report" />
                    </div>
                    <!--end::Search-->
                    <!--begin::Filter-->
                    <div class="w-150px">
                        <!--begin::Select2-->
                        <select class="form-select form-select-solid" data-control="select2" data-hide-search="true"
                            data-placeholder="Status" data-kt-ecommerce-order-filter="status">
                            <option></option>
                            <option value="all">All</option>
                            <option value="jazigo">Jazigo</option>
                            <option value="locked">Locked</option>
                            <option value="disabled">Disabled</option>
                            <option value="banned">Banned</option>
                        </select>
                        <!--end::Select2-->
                    </div>
                    <!--end::Filter-->
                    <!--begin::Export dropdown-->
                    <button type="button" class="btn btn-light-primary" data-kt-menu-trigger="click"
                        data-kt-menu-placement="bottom-end">
                        <!--begin::Svg Icon | path: icons/duotune/arrows/arr078.svg-->
                        <span class="svg-icon svg-icon-2">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.3" x="12.75" y="4.25" width="12" height="2" rx="1"
                                    transform="rotate(90 12.75 4.25)" fill="currentColor" />
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
                    <div id="kt_ecommerce_report_customer_orders_export_menu"
                        class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-200px py-4"
                        data-kt-menu="true">
                        <!--begin::Menu item-->
                        <div class="menu-item px-3">
                            <a href="#" class="menu-link px-3" data-kt-ecommerce-export="copy">Copy to
                                clipboard</a>
                        </div>
                        <!--end::Menu item-->
                        <!--begin::Menu item-->
                        <div class="menu-item px-3">
                            <a href="#" class="menu-link px-3" data-kt-ecommerce-export="excel">Export as
                                Excel</a>
                        </div>
                        <!--end::Menu item-->
                        <!--begin::Menu item-->
                        <div class="menu-item px-3">
                            <a href="#" class="menu-link px-3" data-kt-ecommerce-export="csv">Export as CSV</a>
                        </div>
                        <!--end::Menu item-->
                        <!--begin::Menu item-->
                        <div class="menu-item px-3">
                            <a href="#" class="menu-link px-3" data-kt-ecommerce-export="pdf">Export as PDF</a>
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
            <div class="card-body pt-0 pb-5">
                <!--begin::Table-->
                <table class="table align-middle table-row-dashed fs-6 gy-5"
                    id="kt_ecommerce_report_customer_orders_table">
                    <!--begin::Table head-->
                    <thead>
                        <!--begin::Table row-->
                        <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                            <th class="min-w-100px">Código</th>
                            <th class="min-w-100px">Localização</th>
                            <th class="min-w-100px">Status</th>
                            <th class="min-w-100px">Data</th>
                            <th class="text-end min-w-75px">Tipo</th>
                            <th class="text-end min-w-75px">Tamanho</th>
                            <th class="text-end min-w-100px">Ação</th>
                        </tr>
                        <!--end::Table row-->
                    </thead>
                    <!--end::Table head-->
                    <!--begin::Table body-->
                    <tbody class="fw-semibold text-gray-600">
                        <!--begin::Table row-->
                        @foreach ($sepulturas as $sepultura)
                            <tr>
                                <!--begin::Customer name=-->
                                <td>
                                    <a href="#"
                                        class="text-dark text-hover-primary">{{ $sepultura->codigo_sepultura }}</a>
                                </td>
                                <!--end::Customer name=-->
                                <!--begin::Email=-->
                                <td>
                                    {{ \Illuminate\Support\Str::limit($sepultura->localizacao, 20) }}
                                </td>
                                <!--end::Email=-->
                                <!--begin::Status=-->
                                <td>
                                    <div
                                        class="badge badge-light-{{ $sepultura->status == 'disponível' ? 'success' : ($sepultura->status == 'ocupado' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($sepultura->status) }}
                                    </div>
                                </td>
                                <!--begin::Status=-->
                                <!--begin::Status=-->
                                <td>{{ \Carbon\Carbon::parse($sepultura->data_aquisicao)->isoFormat('D/MM/Y') }}</td>
                                <!--begin::Status=-->
                                <!--begin::No orders=-->
                                <td class="text-end pe-0">
                                    <div
                                        class="badge badge-light-{{ $sepultura->tipo == 'cemiterio familiar'
                                            ? 'info'
                                            : ($sepultura->tipo == 'cova'
                                                ? 'secondary'
                                                : ($sepultura->tipo == 'cripta'
                                                    ? 'dark'
                                                    : ($sepultura->tipo == 'jazigo'
                                                        ? 'primary'
                                                        : ($sepultura->tipo == 'mausoléu'
                                                            ? 'danger'
                                                            : ($sepultura->tipo == 'ossário'
                                                                ? 'warning'
                                                                : ($sepultura->tipo == 'sepultura vertical'
                                                                    ? 'success'
                                                                    : ($sepultura->tipo == 'terreno'
                                                                        ? 'warning'
                                                                        : 'default'))))))) }}">
                                        {{ ucfirst($sepultura->tipo) }}
                                    </div>
                                </td>
                                <!--end::No orders=-->
                                <!--begin::No products=-->
                                <td class="text-end pe-0">
                                    <a href="#"
                                        class="text-dark text-hover-primary">{{ number_format($sepultura->tamanho, 2, ',', '.') }}
                                        m²</a>
                                </td>
                                <!--end::No products=-->
                                <!--begin::Total=-->
                                <td class="text-end">
                                    <div class="ms-5">
                                        <!--begin::Edit-->
                                        <a href="{{ route('cemiterio.edit', $sepultura->id) }}"
                                            class="btn btn-icon btn-active-light-primary w-30px h-30px me-3">
                                            <span data-bs-toggle="tooltip" data-bs-trigger="hover" title="Edit">
                                                <!--begin::Svg Icon | path: icons/duotune/art/art005.svg-->
                                                <span class="svg-icon svg-icon-3">
                                                    <svg width="24" height="24" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path opacity="0.3"
                                                            d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z"
                                                            fill="currentColor" />
                                                        <path
                                                            d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z"
                                                            fill="currentColor" />
                                                    </svg>
                                                </span>
                                                <!--end::Svg Icon-->
                                            </span>
                                        </a>
                                        <!--end::Edit-->
                                        <!-- Botão para abrir o modal -->
                                        <a href="#"
                                            class="btn btn-icon btn-active-light-danger w-30px h-30px me-3"
                                            data-bs-toggle="modal" data-bs-target="#deleteModal">
                                            <!-- Ícone de exclusão -->
                                            <span class="svg-icon svg-icon-3">
                                                <svg width="24" height="24" viewBox="0 0 24 24"
                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
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

                                    </div>
                                </td>
                                <!--end::Total=-->
                            </tr>
                        @endforeach
                        <!--end::Table row-->
                    </tbody>
                    <!--end::Table body-->
                </table>
                <!--end::Table-->
            </div>
            <!--end::Card body-->
        </div>
        <!--end::Card-->
    </div>
    <!--end:::Tab pane-->
</div>
<!--end:::Tab content-->
<!--begin::Modal de Exclusão-->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir esta sepultura? Esta ação não pode ser desfeita.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <!-- Formulário de exclusão -->
                <form action="{{ route('cemiterio.destroy', $sepultura->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE') <!-- Método DELETE -->
                    <button type="submit" class="btn btn-danger">
                        Excluir
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<!--end::Modal de Exclusão-->
