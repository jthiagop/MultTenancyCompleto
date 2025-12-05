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
                            Patrimônio</h1>
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
                                <a href="{{ route('patrimonio.filtrar') }}" class="text-muted text-hover-primary">
                                    Patrimônio</a>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-400 w-5px h-2px"></span>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">
                                Filtrar Patrimônios
                            </li>
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
                    <!--begin::Navbar-->
                    @include('app.components.card-body-patrimonio', ['active' => true])
                    <!--end::Navbar-->

                    <div class="card mb-7">
                        <div class="card-body">
                            <h3 class="card-title mb-5">Filtrar Patrimônios</h3>

                            <form action="{{ route('patrimonio.filtrar') }}" method="GET"
                                class="row g-3 align-items-end">

                                <div class="col-md-4">
                                    <label for="filter_field" class="form-label">Campo</label>
                                    <select name="filter_field" id="filter_field" class="form-select form-select-solid">
                                        <option value="descricao"
                                            {{ request('filter_field') == 'descricao' ? 'selected' : '' }}>Descrição
                                        </option>
                                        <option value="codigo_rid"
                                            {{ request('filter_field') == 'codigo_rid' ? 'selected' : '' }}>Código RID
                                        </option>
                                        <option value="patrimonio"
                                            {{ request('filter_field') == 'patrimonio' ? 'selected' : '' }}>Patrimônio
                                        </option>
                                        <option value="localidade"
                                            {{ request('filter_field') == 'localidade' ? 'selected' : '' }}>Cidade
                                        </option>
                                        <option value="bairro"
                                            {{ request('filter_field') == 'bairro' ? 'selected' : '' }}>Bairro</option>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label for="filter_condition" class="form-label">Condição</label>
                                    <select name="filter_condition" id="filter_condition"
                                        class="form-select form-select-solid">
                                        <option value="contains"
                                            {{ request('filter_condition') == 'contains' ? 'selected' : '' }}>Contém
                                        </option>
                                        <option value="equals"
                                            {{ request('filter_condition') == 'equals' ? 'selected' : '' }}>Igual a
                                        </option>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label for="filter_value" class="form-label">Busca</label>
                                    <input type="text" name="filter_value" id="filter_value"
                                        class="form-control form-control-solid" value="{{ request('filter_value') }}"
                                        placeholder="Digite aqui...">
                                </div>

                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <span class="svg-icon svg-icon-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-search me-2" viewBox="0 0 16 16">
                                                <path
                                                    d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
                                            </svg>
                                        </span>Pesquisar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!--begin::Table-->
                    <div class="card card-flush mt-6 mt-xl-9">
                        <!--begin::Card header-->
                        <div class="card-header mt-5">
                            <!--begin::Card title-->
                            <div class="card-title flex-column">
                                <h3 class="fw-bold mb-1">Lista Patrimônio Foreiro</h3>
                            </div>
                            <!--begin::Card title-->
                            <!--begin::Card toolbar-->
                            <div class="card-toolbar my-1">
                                <div class=" my-1">
                                    <a href="{{ route('patrimonio.imprimir', request()->query()) }}" target="_blank"
                                        class="btn btn-sm btn-primary me-3">
                                        <i class="bi bi-printer"></i> Imprimir PDF
                                    </a>
                                </div>
                            </div>
                            <!--begin::Card toolbar-->
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            <!--begin::Table container-->
                            <div class="table-responsive">
                                <!--begin::Table-->
                                <table id="kt_profile_overview_table"
                                    class="table table-row-bordered table-row-dashed gy-4 align-middle fw-bold">
                                    <!--begin::Head-->
                                    <thead class="fs-7 text-gray-400 text-uppercase">
                                        <tr>
                                            <th class="min-w-50px">RID</th>
                                            <th class="min-w-150px">Manager</th>
                                            <th class="min-w-90px">Cidade</th>
                                            <th class="min-w-90px">Bairro</th>
                                            <th class="min-w-150px">Date</th>
                                            <th class="min-w-50px text-end">Details</th>
                                        </tr>
                                    </thead>
                                    <!--end::Head-->
                                    <!--begin::Body-->
                                    <tbody class="fs-6">
                                        @foreach ($patrimonios as $foreiro)
                                            <tr id="patrimonio-{{ $foreiro->id }}">
                                                <td>{{ $foreiro->codigo_rid }}</td>
                                                <td>
                                                    <!--begin::User-->
                                                    <div class="d-flex align-items-center">
                                                        <!--begin::Info-->
                                                        <div class="d-flex flex-column justify-content-center">
                                                            <a href="#"
                                                                class="fs-6 text-gray-800 text-hover-primary"
                                                                onclick="showToast('{{ $foreiro->id }}')">
                                                                {{ $foreiro->descricao }}
                                                            </a>
                                                            <div class="fw-semibold text-gray-400">
                                                                {{ $foreiro->patrimonio }}</div>
                                                        </div>
                                                        <!--end::Info-->
                                                    </div>
                                                    <!--end::User-->
                                                </td>
                                                <td>{{ $foreiro->localidade }}</td>
                                                <td>{{ $foreiro->bairro }}</td>
                                                <td>
                                                    <span
                                                        class="badge badge-light-success fw-bold px-4 py-3">Approved</span>
                                                </td>
                                                <td class="text-end">
                                                    <!-- Botão de visualização -->
                                                    <a href="{{ route('patrimonio.show', $foreiro->id) }}"
                                                        class="btn btn-light btn-sm"><span><i
                                                                class="ki-solid ki-eye"></i>Ver</span></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <!--end::Body-->
                                </table>
                                <!--end::Table-->
                            </div>
                            <!--end::Table container-->
                            <!--begin::Pagination-->
                            <div class="d-flex justify-content-end mt-5">
                                {{ $patrimonios->links() }}
                            </div>
                            <!--end::Pagination-->
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Card-->
                </div>
                <!--end::Content container-->
            </div>
            <!--end::Content-->
        </div>
        <!--end::Content wrapper-->
    </div>
    <!--end:::Main-->

    <script>
        function showToast(patrimonioId) {
            const toastMessage = `Você clicou no patrimônio com ID: ${patrimonioId}`;

            // Configura o toast
            const toastEl = document.createElement('div');
            toastEl.className = 'toast';
            toastEl.role = 'alert';
            toastEl.ariaLive = 'assertive';
            toastEl.ariaAtomic = 'true';
            toastEl.innerHTML = `
            <div class="toast-header">
                <strong class="me-auto">Notificação</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">${toastMessage}</div>
        `;

            document.body.appendChild(toastEl);
            const toast = new bootstrap.Toast(toastEl);
            toast.show();

            // Remove o toast após um tempo
            setTimeout(() => {
                document.body.removeChild(toastEl);
            }, 3000); // 3000 ms = 3 segundos
        }
    </script>
</x-tenant-app-layout>

