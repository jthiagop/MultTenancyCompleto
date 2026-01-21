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
                            <h3 class="fw-bold m-0">Detalhes da Importação</h3>
                        </div>
                        <!--end::Card title-->

                        <!--begin::Card toolbar-->
                        <div class="card-toolbar">
                            <a href="{{ route('bank-statements.index') }}" class="btn btn-sm btn-light">
                                <i class="bi bi-arrow-left"></i>
                                Voltar
                            </a>
                        </div>
                        <!--end::Card toolbar-->
                    </div>
                    <!--end::Card header-->

                    <!--begin::Card body-->
                    <div class="card-body pt-0">
                        <!--begin::Import Info-->
                        <div class="row g-6 mb-8">
                            <div class="col-md-3">
                                <label class="fs-6 fw-bold text-gray-700 mb-2">Data da Importação</label>
                                <div class="fs-5 text-gray-900">{{ $import->imported_at->format('d/m/Y H:i') }}</div>
                            </div>
                            <div class="col-md-3">
                                <label class="fs-6 fw-bold text-gray-700 mb-2">Conta Bancária</label>
                                <div class="fs-5 text-gray-900">{{ $import->bankConfig->nome_conta ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-3">
                                <label class="fs-6 fw-bold text-gray-700 mb-2">Período</label>
                                <div class="fs-5 text-gray-900">
                                    {{ $import->period_start?->format('d/m/Y') }} - {{ $import->period_end?->format('d/m/Y') }}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="fs-6 fw-bold text-gray-700 mb-2">Importado Por</label>
                                <div class="fs-5 text-gray-900">{{ $import->importedBy->name ?? 'Sistema' }}</div>
                            </div>
                        </div>
                        <!--end::Import Info-->

                        <!--begin::Separator-->
                        <div class="separator separator-dashed my-8"></div>
                        <!--end::Separator-->

                        <!--begin::Table-->
                        <div class="table-responsive">
                            <table class="table align-middle table-row-dashed fs-6 gy-5">
                                <!--begin::Table head-->
                                <thead>
                                    <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                        <th class="min-w-100px">Data</th>
                                        <th>Descrição</th>
                                        <th class="text-end">Valor</th>
                                        <th>Tipo</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <!--end::Table head-->

                                <!--begin::Table body-->
                                <tbody class="fw-semibold text-gray-600">
                                    @forelse ($import->entries as $entry)
                                        <tr>
                                            <td>{{ $entry->transaction_date?->format('d/m/Y') ?? '-' }}</td>
                                            <td>
                                                <div class="fw-bold">{{ $entry->description }}</div>
                                                @if($entry->additional_info)
                                                    <div class="text-muted fs-7">{{ $entry->additional_info }}</div>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <span class="fw-bold {{ $entry->type === 'credit' ? 'text-success' : 'text-danger' }}">
                                                    {{ $entry->type === 'credit' ? '+' : '-' }} R$ {{ number_format($entry->amount, 2, ',', '.') }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-light-{{ $entry->type === 'credit' ? 'success' : 'danger' }}">
                                                    {{ $entry->type === 'credit' ? 'Crédito' : 'Débito' }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                @if($entry->reconciled_at)
                                                    <span class="badge badge-light-success">Conciliado</span>
                                                @else
                                                    <span class="badge badge-light-warning">Pendente</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-10">
                                                <div class="d-flex flex-column align-items-center">
                                                    <i class="ki-duotone ki-file-deleted fs-5x text-gray-400 mb-4">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                    <span class="text-gray-600 fs-5">Nenhum lançamento encontrado</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <!--end::Table body-->
                            </table>
                        </div>
                        <!--end::Table-->
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
</x-tenant-app-layout>
