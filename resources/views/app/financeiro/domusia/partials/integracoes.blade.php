<!--begin:::Tab pane - Integrações-->
<div class="tab-pane fade {{ ($activeTab ?? 'pendentes') === 'integracoes' ? 'active show' : '' }}"
     id="kt_customer_view_overview_statements"
     role="tabpanel">
    <!--begin::Statements-->
    <div class="card mb-6 mb-xl-9">
        <!--begin::Header-->
        <div class="card-header">
            <!--begin::Title-->
            <div class="card-title">
                <h2>Integrações</h2>
            </div>
            <!--end::Title-->
            <!--begin::Card toolbar-->
            <div class="card-toolbar">
                <!--begin::Toolbar-->
                <div class="d-flex justify-content-end">
                    <!--begin::Add integration-->
                    <button type="button"
                            id="kt_drawer_integracao_button"
                            class="btn btn-sm btn-primary">
                        <i class="fa-solid fa-plus me-2"></i>
                        Nova Integração
                    </button>
                    <!--end::Add integration-->
                </div>
                <!--end::Toolbar-->
            </div>
            <!--end::Card toolbar-->
        </div>
        <!--end::Header-->
        <!--begin::Card body-->
        <div class="card-body pb-5">
            <!--begin::Table-->
            <table id="kt_customer_view_statement_table_1"
                class="table align-middle table-row-dashed fs-6 text-gray-600 fw-semibold gy-4">
                <!--begin::Table head-->
                <thead class="border-bottom border-gray-200">
                    <!--begin::Table row-->
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                        <th class="w-125px">
                            <span class="text-muted">Integração</span>
                            <i class="fa-solid fa-arrows-up-down ms-1 fs-6"></i>
                        </th>
                        <th class="w-100px">
                            <span class="text-muted">Situação</span>
                            <i class="fa-solid fa-arrows-up-down ms-1 fs-6"></i>
                        </th>
                        <th class="w-300px">Remetente</th>
                        <th class="w-100px">Destinatário</th>
                        <th class="w-100px text-end pe-7">Ações</th>
                    </tr>
                    <!--end::Table row-->
                </thead>
                <!--end::Table head-->
                <!--begin::Table body-->
                <tbody>
                    @forelse($integracoes ?? [] as $integracao)
                        <tr>
                            <td>
                                <span class="text-gray-800 fw-semibold">
                                    @if($integracao->tipo === 'whatsapp')
                                        <i class="fa-brands fa-whatsapp text-success me-2"></i>WhatsApp
                                    @elseif($integracao->tipo === 'dda')
                                        <i class="fa-solid fa-file-invoice-dollar text-primary me-2"></i>DDA
                                    @elseif($integracao->tipo === 'email')
                                        <i class="fa-solid fa-envelope text-info me-2"></i>E-mail
                                    @else
                                        {{ ucfirst($integracao->tipo) }}
                                    @endif
                                </span>
                            </td>
                            <td>
                                @if($integracao->status === 'configurado')
                                    <span class="badge badge-light-success">Configurado</span>
                                @else
                                    <span class="badge badge-light-warning">Pendente</span>
                                @endif
                            </td>
                            <td>
                                <span class="text-gray-700">
                                    {{ $integracao->remetente ?: '-' }}
                                </span>
                            </td>
                            <td>
                                <span class="text-gray-700">
                                    {{ $integracao->destinatario ?: '-' }}
                                </span>
                            </td>
                            <td class="text-end">
                                @if($integracao->tipo === 'whatsapp' && $integracao->status === 'pendente')
                                    <button
                                        id="kt_drawer_integracao_button"
                                        class="btn btn-sm btn-light btn-active-light-primary">
                                        Configurar
                                    </button>
                                @elseif($integracao->status === 'configurado')
                                    <button
                                        class="btn btn-sm btn-light-danger excluir-integracao-btn"
                                        data-integracao-id="{{ $integracao->id }}"
                                        data-integracao-tipo="{{ $integracao->tipo }}">
                                        Excluir
                                    </button>
                                @else
                                    <button
                                        id="kt_drawer_integracao_button"
                                        class="btn btn-sm btn-light btn-active-light-primary">
                                        Configurar
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-10">
                                <div class="text-muted">
                                    <i class="fa-solid fa-inbox fs-3x mb-3"></i>
                                    <p class="fs-6">Nenhuma integração configurada ainda.</p>
                                    <p class="fs-7 text-muted">Configure uma integração para começar a receber documentos.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <!--end::Table body-->
            </table>
            <!--end::Table-->
        </div>
        <!--end::Card body-->
    </div>
    <!--end::Statements-->
</div>
<!--end:::Tab pane - Integrações-->
