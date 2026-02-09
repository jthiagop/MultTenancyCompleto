<!--begin::Card Etapas de Formação-->
<div class="card mb-5 mb-xl-8">
    <div class="card-header border-0">
        <h3 class="card-title fw-bold text-gray-800">
            <i class="fa-solid fa-graduation-cap me-2 text-primary"></i>
            Histórico de Vida Religiosa
        </h3>
    </div>
    <div class="card-body pt-0">
        @if ($timeline->count() > 0)
            <!--begin::Timeline-->
            <div class="timeline">
                @foreach ($timeline as $event)
                    <!--begin::Timeline item-->
                    <div class="timeline-item">
                        <!--begin::Timeline line-->
                        <div class="timeline-line w-40px"></div>
                        <!--end::Timeline line-->

                        <!--begin::Timeline icon-->
                        <div class="timeline-icon symbol symbol-circle symbol-40px">
                            <div class="symbol-label bg-light-{{ $event->color }}">
                                <i class="{{ $event->icon }} text-{{ $event->color }}"></i>
                            </div>
                        </div>
                        <!--end::Timeline icon-->

                        <!--begin::Timeline content-->
                        <div class="timeline-content mb-10 mt-n1">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <span class="fs-5 fw-bold text-gray-800">
                                        @if($event->emoji)
                                            <span class="me-1">{{ $event->emoji }}</span>
                                        @endif
                                        {{ $event->title }}
                                    </span>
                                    @foreach($event->badges as $badge)
                                        <span class="badge badge-light-{{ $badge['color'] }} ms-2">{{ $badge['label'] }}</span>
                                    @endforeach
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-4 fs-7 text-gray-600 mb-2">
                                <div>
                                    <i class="fa-regular fa-calendar me-1"></i>
                                    {{ $event->formattedDate() }}
                                </div>
                                <div class="text-muted">
                                    {{ $event->relativeTime() }}
                                </div>
                            </div>

                            @if($event->description)
                                <div class="fs-7 text-gray-600 mb-3">
                                    {{ $event->description }}
                                </div>
                            @endif

                            @php
                                $companyId = $event->metadata['company_id'] ?? null;
                                $company = $companyId ? ($member->formationPeriods->firstWhere('company_id', $companyId)?->company) : null;
                            @endphp

                            @if ($company && in_array($event->type, ['formation_start']))
                                <!--begin::Record-->
                                <div class="d-flex align-items-center border border-dashed border-gray-300 rounded min-w-750px px-7 py-3 ">
                                    <!--begin::User-->
                                    <div class="symbol symbol-circle symbol-25px me-2">
                                        <img src="{{ route('file', ['path' => $company->avatar]) }}" alt="img" />
                                    </div>
                                    <!--end::User-->
                                    <!--begin::Title-->
                                    <a href=""
                                        class="fs-5 text-dark text-hover-primary fw-semibold w-375px min-w-200px">{{ $company->name }}</a>
                                    <!--end::Title-->
                                    <!--begin::Label-->
                                    <div class="min-w-175px pe-2">
                                    </div>
                                    <!--end::Label-->
                                    <!--begin::Users-->
                                    <div class="symbol-group symbol-hover flex-nowrap flex-grow-1 min-w-100px pe-2">
                                        <!--begin::User-->
                                        <div class="symbol symbol-circle symbol-25px">
                                        </div>
                                        <!--end::User-->
                                        <!--begin::User-->
                                        <div class="symbol symbol-circle symbol-25px">
                                        </div>
                                        <!--end::User-->
                                        <!--begin::User-->
                                        <div class="symbol symbol-circle symbol-25px">
                                            <div class="symbol-label fs-8 fw-semibold bg-primary text-inverse-primary">A
                                            </div>
                                        </div>
                                        <!--end::User-->
                                    </div>
                                    <!--end::Users-->
                                    <!--begin::Progress-->
                                    <div class="min-w-125px pe-2">
                                        <span class="badge badge-light-{{ $company->status == 'active' ? 'success' : 'danger' }}">
                                            {{ $company->status == 'active' ? 'Ativo' : 'Desativado' }}
                                        </span>
                                    </div>
                                    <!--end::Progress-->
                                    <!--begin::Action-->
                                    <a href="" class="btn btn-sm btn-light btn-active-light-primary">Ver</a>
                                    <!--end::Action-->
                                </div>
                                <!--end::Record-->
                            @endif
                        </div>
                        <!--end::Timeline content-->
                    </div>
                    <!--end::Timeline item-->
                @endforeach
            </div>
            <!--end::Timeline-->
        @else
            <div class="text-center py-10">
                <img src="{{ asset('tenancy/assets/media/illustrations/sketchy-1/5.png') }}" alt="Sem registros" class="mw-200px mb-5">
                <p class="text-gray-500 fs-6">Nenhum evento registrado na timeline</p>
            </div>
        @endif
    </div>
</div>
<!--end::Card Etapas de Formação-->
