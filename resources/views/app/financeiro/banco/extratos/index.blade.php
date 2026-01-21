<x-tenant-app-layout>
    <!--begin::Content wrapper-->
    <div class="d-flex flex-column flex-column-fluid pt-7">
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
                            <h3 class="fw-bold m-0">Extratos Bancários</h3>
                        </div>
                        <!--end::Card title-->

                        <!--begin::Card toolbar-->
                        <div class="card-toolbar">
                            <!--begin::Toolbar-->
                            <div class="d-flex justify-content-end gap-2" data-kt-customer-table-toolbar="base">
                                <!--begin::Sincronizar Button-->
                                <form action="{{ route('bank-statements.sync') }}" method="POST" class="d-inline" id="form-sync-extrato">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success" id="btn-sync-extrato">
                                        <i class="bi bi-arrow-clockwise me-2"></i>
                                        Sincronizar Agora
                                    </button>
                                </form>
                                <!--end::Sincronizar Button-->

                                <!--begin::Button-->
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#kt_modal_fetch_extrato">
                                    <i class="bi bi-search"></i>
                                    Buscar Extrato BB
                                </button>
                                <!--end::Button-->
                            </div>
                            <!--end::Toolbar-->
                        </div>
                        <!--end::Card toolbar-->
                    </div>
                    <!--end::Card header-->

                    <!--begin::Card body-->
                    <div class="card-body pt-0">
                        <!--begin::Stats-->
                        <div class="row g-6 g-xl-9 mb-6">
                            <div class="col-md-6">
                                <div class="card card-flush h-md-100">
                                    <div class="card-body d-flex flex-column justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-60px me-5">
                                                <span class="symbol-label bg-light-primary">
                                                    <i class="bi bi-credit-card fs-2x text-primary"></i>
                                                </span>
                                            </div>
                                            <div class="d-flex flex-column flex-grow-1">
                                                <span class="text-gray-500 fw-semibold fs-6">Total de Lançamentos</span>
                                                <span class="text-gray-800 fw-bold fs-2">{{ number_format($totalEntries) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card card-flush h-md-100">
                                    <div class="card-body d-flex flex-column justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-60px me-5">
                                                <span class="symbol-label bg-light-warning">
                                                    <i class="bi bi-clock-history fs-2x text-warning"></i>
                                                </span>
                                            </div>
                                            <div class="d-flex flex-column flex-grow-1">
                                                <span class="text-gray-500 fw-semibold fs-6">Pendente Conciliação</span>
                                                <span class="text-gray-800 fw-bold fs-2">{{ number_format($pendingReconciliation) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Stats-->

                        <!--begin::Table-->
                        <div class="table-responsive">
                            <table class="table align-middle table-row-dashed fs-6 gy-5">
                                <!--begin::Table head-->
                                <thead>
                                    <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                        <th class="min-w-125px">Data Importação</th>
                                        <th class="min-w-125px">Conta Bancária</th>
                                        <th>Período</th>
                                        <th>Origem</th>
                                        <th>Lançamentos</th>
                                        <th>Importado Por</th>
                                        <th class="text-end min-w-70px">Ações</th>
                                    </tr>
                                </thead>
                                <!--end::Table head-->

                                <!--begin::Table body-->
                                <tbody class="fw-semibold text-gray-600">
                                    @forelse ($imports as $import)
                                        <tr>
                                            <td>{{ $import->imported_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <span class="badge badge-light-primary">
                                                    {{ $import->bankConfig->nome_conta ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td>
                                                {{ $import->period_start?->format('d/m/Y') }} -
                                                {{ $import->period_end?->format('d/m/Y') }}
                                            </td>
                                            <td>
                                                <span class="badge badge-light-info">{{ $import->source }}</span>
                                            </td>
                                            <td>{{ $import->entries_count ?? $import->entries->count() }}</td>
                                            <td>{{ $import->importedBy->name ?? 'Sistema' }}</td>
                                            <td class="text-end">
                                                <a href="{{ route('bank-statements.show', $import->id) }}"
                                                    class="btn btn-sm btn-light btn-active-light-primary">
                                                    Ver Detalhes
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-10">
                                                <div class="d-flex flex-column align-items-center">
                                                    <i class="ki-duotone ki-file-deleted fs-5x text-gray-400 mb-4">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                    <span class="text-gray-600 fs-5">Nenhum extrato importado ainda</span>
                                                    <span class="text-gray-500 fs-6">Clique em "Buscar Extrato BB" para começar</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <!--end::Table body-->
                            </table>
                        </div>
                        <!--end::Table-->

                        <!--begin::Pagination-->
                        <div class="d-flex justify-content-end mt-6">
                            {{ $imports->links() }}
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

    <!--begin::Modal - Fetch Extrato-->
    <div class="modal fade" id="kt_modal_fetch_extrato" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <form id="kt_modal_fetch_extrato_form" action="{{ route('bank-statements.fetch') }}" method="POST">
                    @csrf
                    <!--begin::Modal header-->
                    <div class="modal-header">
                        <h2 class="fw-bold">Buscar Extrato do Banco do Brasil</h2>
                        <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                            <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                        </div>
                    </div>
                    <!--end::Modal header-->

                    <!--begin::Modal body-->
                    <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                        <!--begin::Input group-->
                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-6 mb-2">Conta Bancária</label>
                            <select name="bank_account_id" class="form-select form-select-solid" required>
                                <option value="">Selecione uma conta</option>
                                {{-- TODO: Popular com contas que tem config BB --}}
                            </select>
                        </div>
                        <!--end::Input group-->

                        <!--begin::Input group-->
                        <div class="row g-9 mb-7">
                            <div class="col-md-6 fv-row">
                                <label class="required fw-semibold fs-6 mb-2">Data Inicial</label>
                                <input type="date" name="start_date" class="form-control form-control-solid"
                                    value="{{ now()->subDays(7)->format('Y-m-d') }}" required />
                            </div>

                            <div class="col-md-6 fv-row">
                                <label class="required fw-semibold fs-6 mb-2">Data Final</label>
                                <input type="date" name="end_date" class="form-control form-control-solid"
                                    value="{{ now()->format('Y-m-d') }}" required />
                            </div>
                        </div>
                        <!--end::Input group-->

                        <!--begin::Alert-->
                        <div class="alert alert-warning d-flex align-items-center p-5 mb-10">
                            <i class="ki-duotone ki-information-5 fs-2x text-warning me-4"><span class="path1"></span><span
                                    class="path2"></span><span class="path3"></span></i>
                            <div class="d-flex flex-column">
                                <h5 class="mb-1">Período máximo: 31 dias</h5>
                                <span class="fs-7">Conforme limitação da API do Banco do Brasil</span>
                            </div>
                        </div>
                        <!--end::Alert-->
                    </div>
                    <!--end::Modal body-->

                    <!--begin::Modal footer-->
                    <div class="modal-footer flex-center">
                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" id="kt_modal_fetch_extrato_submit" class="btn btn-primary">
                            <span class="indicator-label">Buscar Extrato</span>
                            <span class="indicator-progress">Aguarde...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                    <!--end::Modal footer-->
                </form>
            </div>
        </div>
    </div>
    <!--end::Modal-->

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('kt_modal_fetch_extrato_form');
                const submitButton = document.getElementById('kt_modal_fetch_extrato_submit');

                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    submitButton.setAttribute('data-kt-indicator', 'on');
                    submitButton.disabled = true;

                    const formData = new FormData(form);

                    fetch(form.action, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            submitButton.removeAttribute('data-kt-indicator');
                            submitButton.disabled = false;

                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Sucesso!',
                                    text: data.message,
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Erro',
                                    text: data.message,
                                    confirmButtonText: 'OK'
                                });
                            }
                        })
                        .catch(error => {
                            submitButton.removeAttribute('data-kt-indicator');
                            submitButton.disabled = false;

                            Swal.fire({
                                icon: 'error',
                                title: 'Erro',
                                text: 'Erro ao buscar extrato: ' + error.message,
                                confirmButtonText: 'OK'
                            });
                        });
                });

                // Botão Sincronizar
                const formSync = document.getElementById('form-sync-extrato');
                const btnSync = document.getElementById('btn-sync-extrato');

                if (formSync && btnSync) {
                    formSync.addEventListener('submit', function(e) {
                        e.preventDefault();
                        
                        const btn = btnSync;
                        const originalHtml = btn.innerHTML;
                        
                        btn.disabled = true;
                        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sincronizando...';

                        fetch(formSync.action, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            btn.disabled = false;
                            btn.innerHTML = originalHtml;

                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Sincronização Concluída!',
                                    text: data.message,
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Erro na Sincronização',
                                    text: data.message,
                                    confirmButtonText: 'OK'
                                });
                            }
                        })
                        .catch(error => {
                            btn.disabled = false;
                            btn.innerHTML = originalHtml;
                            
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro',
                                text: 'Erro ao sincronizar: ' + error.message,
                                confirmButtonText: 'OK'
                            });
                        });
                    });
                }
            });
        </script>
    @endpush
</x-tenant-app-layout>
