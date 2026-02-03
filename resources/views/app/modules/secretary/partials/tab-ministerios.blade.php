<!--begin:::Tab pane-->
<div class="tab-pane fade" id="kt_user_view_overview_security" role="tabpanel">
    <!--begin::Card-->
    <div class="card pt-4 mb-6 mb-xl-9">
        <!--begin::Card header-->
        <div class="card-header  border-1">
            <!--begin::Card title-->
            <div class="card-title">
                <h2>Sacramentos da Ordem</h2>
            </div>
            <!--end::Card title-->
        </div>
        <!--end::Card header-->
        <!--begin::Card body-->
        <div class="card-body pt-0 pb-5">
            <!--begin::Table wrapper-->
            <div class="table-responsive">
                <!--begin::Table-->
                <table class="table align-middle table-row-dashed gy-5" id="kt_table_member_ministries">
                    <!--begin::Table head-->
                    <thead class="border-bottom border-gray-200 fs-7 fw-bold">
                        <tr class="text-start text-muted text-uppercase gs-0">
                            <th class="min-w-150px">Ministério</th>
                            <th class="min-w-100px">Data</th>
                            <th class="min-w-150px">Ministrante</th>
                            <th class="min-w-150px">Diocese</th>
                            <th class="text-end min-w-70px">Ações</th>
                        </tr>
                    </thead>
                    <!--end::Table head-->
                    <!--begin::Table body-->
                    <tbody class="fs-6 fw-semibold text-gray-600">
                        @foreach($ministryTypes as $type)
                            @php
                                $ministry = $member->ministries->firstWhere('ministry_type_id', $type->id);
                            @endphp
                            <tr data-ministry-type-id="{{ $type->id }}" data-ministry-type-name="{{ $type->name }}">
                                <td>
                                    <span class="fw-bold text-gray-800">{{ $type->name }}</span>
                                </td>
                                <td>
                                    @if($ministry)
                                        <span class="text-gray-800">{{ $ministry->date->format('d/m/Y') }}</span>
                                    @else
                                        <span class="badge badge-light-warning">Não informado</span>
                                    @endif
                                </td>
                                <td>
                                    @if($ministry && $ministry->minister_name)
                                        <span class="text-gray-600">{{ $ministry->minister_name }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($ministry && $ministry->diocese_name)
                                        <span class="text-gray-600">{{ $ministry->diocese_name }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <button type="button"
                                        class="btn btn-icon btn-active-light-primary w-30px h-30px ms-auto btn-edit-ministry"
                                        data-ministry-id="{{ $ministry?->id }}"
                                        data-ministry-type-id="{{ $type->id }}"
                                        data-ministry-type-name="{{ $type->name }}"
                                        data-date="{{ $ministry?->date?->format('Y-m-d') }}"
                                        data-minister-name="{{ $ministry?->minister_name }}"
                                        data-diocese-name="{{ $ministry?->diocese_name }}"
                                        data-notes="{{ $ministry?->notes }}"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#kt_modal_ministry">
                                        <span class="svg-icon svg-icon-3">
                                            <i class="fa-solid fa-pencil fs-5"></i>
                                        </span>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <!--end::Table body-->
                </table>
                <!--end::Table-->
            </div>
            <!--end::Table wrapper-->
        </div>
        <!--end::Card body-->
    </div>
    <!--end::Card-->
</div>
<!--end:::Tab pane-->

<!--begin::Modal - Ministry-->
    @include('app.modules.secretary.partials.modal-ministry')
<!--end::Modal - Ministry-->
