<x-tenant-app-layout pageTitle="{{ $member->name }}" :breadcrumbs="[['label' => 'Secretaria', 'url' => route('secretary.index')], ['label' => $member->name]]">

    <!--begin::Content wrapper-->
    <div class="d-flex flex-column flex-column-fluid py-3 py-lg-6">

        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid">

            <!--begin::Content container-->
            <div id="kt_app_content_container" class="app-container container-fluid">

                <!--begin::Toolbar-->
                <div class="d-flex justify-content-between align-items-center mb-6">

                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-light-warning" data-action="edit"
                            data-id="{{ $member->id }}">
                            <i class="fa-regular fa-pen-to-square me-2"></i>Editar
                    </div>
                </div>
                <!--end::Toolbar-->
            </div>
            <!--end::Content container-->
                <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <!--begin::Content container-->
        <div id="kt_app_content_container" class="app-container container-fluid">
            <!--begin::Layout-->
            <div class="d-flex flex-column flex-lg-row">
                <!--begin::Sidebar-->
                <div class="flex-column flex-lg-row-auto w-lg-250px w-xl-400px mb-10">
                    <!--begin::Card-->
                    <div class="card mb-5 mb-xl-8">
                        <!--begin::Card body-->
                        <div class="card-body">
                            <!--begin::Summary-->
                            <!--begin::Summary-->
                            <div class="d-flex flex-center flex-column mb-5">
                                <!--begin::Avatar-->
                                <div class="symbol symbol-150px symbol-circle mb-7">
                                    @if ($member->avatar)
                                        <img src="{{ route('file', ['path' => $member->avatar]) }}"
                                            alt="{{ $member->name }}" />
                                    @else
                                        <div class="symbol-label fs-1 bg-light-primary text-primary fw-bold">
                                            {{ strtoupper(substr($member->name, 0, 2)) }}
                                        </div>
                                    @endif
                                </div>
                                <!--end::Avatar-->

                                <!--begin::Name-->
                                <h3 class="fs-3 text-gray-800 text-hover-primary fw-bold mb-3">{{ $member->name }}
                                </h3>
                                <!--end::Name-->

                                <!--begin::Badges-->
                                <div class="d-flex flex-wrap justify-content-center gap-2 mb-3">
                                    @if ($member->role)
                                        @php
                                            $roleVariants = [
                                                'presbitero' => 'success',
                                                'diacono' => 'warning',
                                                'irmao' => 'primary',
                                            ];
                                            $variant = $roleVariants[$member->role->slug] ?? 'secondary';
                                        @endphp
                                        <span
                                            class="badge badge-lg badge-{{ $variant }}">{{ $member->role->name }}</span>
                                    @endif

                                    @if ($member->currentStage)
                                        <span
                                            class="badge badge-lg badge-light-primary d-inline">{{ $member->currentStage->name }}</span>
                                    @endif

                                    @if ($member->is_active)
                                        <span class="badge badge-lg badge-light-success">Ativo</span>
                                    @else
                                        <span class="badge badge-lg badge-light-danger">Inativo</span>
                                    @endif
                                </div>
                                <!--end::Badges-->
                            </div>
                            <!--end::Summary-->

                            <!--begin::Datas Comemorativas-->
                            @php
                                // Cálculos de idade e anos
                                $idade = $member->birth_date?->age;

                                // Anos de votos/profissão
                                $anosVotos = null;
                                $tipoVotos = null;
                                $dataVotos = null;
                                if ($member->perpetual_profession_date) {
                                    $anosVotos = (int) $member->perpetual_profession_date->diffInYears(now());
                                    $tipoVotos = 'Prof. Perp.';
                                    $dataVotos = $member->perpetual_profession_date;
                                } elseif ($member->temporary_profession_date) {
                                    $anosVotos = (int) $member->temporary_profession_date->diffInYears(now());
                                    $tipoVotos = 'Votos temporários';
                                    $dataVotos = $member->temporary_profession_date;
                                }

                                // Anos de ordenação
                                $anosOrdenacao = null;
                                $tipoOrdenacao = null;
                                $dataOrdenacao = null;
                                if ($member->priestly_ordination_date) {
                                    $anosOrdenacao = (int) $member->priestly_ordination_date->diffInYears(now());
                                    $tipoOrdenacao = 'Ord. presbiteral';
                                    $dataOrdenacao = $member->priestly_ordination_date;
                                } elseif ($member->diaconal_ordination_date) {
                                    $anosOrdenacao = (int) $member->diaconal_ordination_date->diffInYears(now());
                                    $tipoOrdenacao = 'Ord. diaconal';
                                    $dataOrdenacao = $member->diaconal_ordination_date;
                                }

                                // Próximo jubileu (25, 50, 60, 75 anos)
                                $proximoJubileu = null;
                                $jubileus = [25, 50, 60, 75];
                                $eventos = [];

                                // Verificar jubileu de ordenação
                                if ($dataOrdenacao) {
                                    foreach ($jubileus as $anos) {
                                        $dataJubileu = $dataOrdenacao->copy()->addYears($anos);
                                        if ($dataJubileu->isFuture()) {
                                            $eventos[] = [
                                                'tipo' => $anos . ' anos de ' . strtolower($tipoOrdenacao),
                                                'data' => $dataJubileu,
                                                'anos' => $anos,
                                            ];
                                            break;
                                        }
                                    }
                                }

                                // Verificar jubileu de profissão
                                if ($dataVotos) {
                                    foreach ($jubileus as $anos) {
                                        $dataJubileu = $dataVotos->copy()->addYears($anos);
                                        if ($dataJubileu->isFuture()) {
                                            $eventos[] = [
                                                'tipo' => $anos . ' anos de ' . strtolower($tipoVotos),
                                                'data' => $dataJubileu,
                                                'anos' => $anos,
                                            ];
                                            break;
                                        }
                                    }
                                }

                                // Próximo aniversário
                                if ($member->birth_date) {
                                    $proxAniversario = $member->birth_date->copy()->year(now()->year);
                                    if ($proxAniversario->isPast()) {
                                        $proxAniversario->addYear();
                                    }
                                    $idadeProx = $proxAniversario->year - $member->birth_date->year;
                                    $eventos[] = [
                                        'tipo' => $idadeProx . ' anos de vida',
                                        'data' => $proxAniversario,
                                        'anos' => $idadeProx,
                                        'isAniversario' => true,
                                    ];
                                }

                                // Ordenar por data mais próxima
                                usort($eventos, fn($a, $b) => $a['data'] <=> $b['data']);
                                $proximoEvento = $eventos[0] ?? null;
                            @endphp

                            <div class="d-flex flex-wrap flex-center">
                                <!--begin::Stats - Idade-->
                                @if ($idade)
                                    <div class="border border-gray-300 border-dashed rounded py-3 px-3 mb-3 text-center"
                                        style="min-width: 90px;">
                                        <div class="fs-2 fw-bold text-gray-700">
                                            {{ $idade }}
                                            <span class="svg-icon svg-icon-3 svg-icon-primary ms-1">
                                                <i class="fa-solid fa-cake-candles text-primary"></i>
                                            </span>
                                        </div>
                                        <div class="fw-semibold text-muted fs-7">Idade</div>
                                        <div class="text-gray-500 fs-8">{{ $member->birth_date->format('d/m/Y') }}
                                        </div>
                                    </div>
                                @endif
                                <!--end::Stats-->

                                <!--begin::Stats - Votos-->
                                @if ($anosVotos !== null)
                                    <div class="border border-gray-300 border-dashed rounded py-3 px-3 mx-2 mb-3 text-center"
                                        style="min-width: 90px;">
                                        <div class="fs-2 fw-bold text-gray-700">
                                            {{ $anosVotos }}
                                            <span class="svg-icon svg-icon-3 svg-icon-success ms-1">
                                                <i class="fa-solid fa-cross text-success"></i>
                                            </span>
                                        </div>
                                        <div class="fw-semibold text-muted fs-7">{{ $tipoVotos }}</div>
                                        <div class="text-gray-500 fs-8">{{ $dataVotos->format('d/m/Y') }}</div>
                                    </div>
                                @endif
                                <!--end::Stats-->

                                <!--begin::Stats - Ordenação ou Etapa-->
                                @if ($anosOrdenacao !== null)
                                    <div class="border border-gray-300 border-dashed rounded py-3 px-3 mb-3 text-center"
                                        style="min-width: 90px;">
                                        <div class="fs-2 fw-bold text-gray-700">
                                            {{ $anosOrdenacao }}
                                            <span class="svg-icon svg-icon-3 svg-icon-warning ms-1">
                                                <i class="fa-solid fa-hands-praying text-warning"></i>
                                            </span>
                                        </div>
                                        <div class="fw-semibold text-muted fs-7">{{ $tipoOrdenacao }}</div>
                                        <div class="text-gray-500 fs-8">{{ $dataOrdenacao->format('d/m/Y') }}</div>
                                    </div>
                                @elseif($member->currentStage && $member->role?->slug === 'irmao')
                                    <div class="border border-gray-300 border-dashed rounded py-3 px-3 mb-3 text-center"
                                        style="min-width: 90px;">
                                        <div class="fs-6 fw-bold text-gray-700">
                                            <i class="fa-solid fa-graduation-cap text-info me-1"></i>
                                        </div>
                                        <div class="fw-semibold text-muted fs-7">Etapa atual</div>
                                        <div class="text-gray-600 fs-8">{{ $member->currentStage->name }}</div>
                                    </div>
                                @endif
                                <!--end::Stats-->
                            </div>

                            <!--begin::Próximo Evento Comemorativo-->
                            @if ($proximoEvento)
                                <div class="d-flex justify-content-center mt-2 mb-3">
                                    <div
                                        class="badge badge-light-{{ isset($proximoEvento['isAniversario']) ? 'primary' : 'warning' }} px-4 py-2">
                                        <i
                                            class="fa-solid fa-{{ isset($proximoEvento['isAniversario']) ? 'gift' : 'star' }} me-2"></i>
                                        <span class="fw-semibold">
                                            Próximo: {{ $proximoEvento['tipo'] }}
                                            <span
                                                class="text-muted ms-1">({{ $proximoEvento['data']->format('d/m/Y') }})</span>
                                        </span>
                                    </div>
                                </div>
                            @endif
                            <!--end::Próximo Evento Comemorativo-->
                            <!--end::Datas Comemorativas-->
                            <!--begin::Details toggle-->
                            <div class="d-flex flex-stack fs-4 py-3">
                                <div class="fw-bold rotate collapsible" data-bs-toggle="collapse"
                                    href="#kt_user_view_details" role="button" aria-expanded="false"
                                    aria-controls="kt_user_view_details">Detalhes
                                    <span class="ms-2 rotate-180">
                                        <!--begin::Svg Icon | path: icons/duotune/arrows/arr072.svg-->
                                        <i class="fa-solid fa-chevron-down"></i>
                                        <!--end::Svg Icon-->
                                    </span>
                                </div>
                                <span data-bs-toggle="tooltip" data-bs-trigger="hover" title="Editar dados do membro">
                                    <a class="btn btn-sm btn-light-primary" id="btn_edit_member">
                                        <i class="fa-solid fa-pen-to-square me-1"></i>Editar
                                    </a>
                                </span>
                            </div>
                            <!--end::Details toggle-->
                            <div class="separator"></div>
                            <!--begin::Details content-->
                            <div id="kt_user_view_details" class="collapse show">
                                <div class="pb-5 fs-6">
                                    <!--begin::Details item-->
                                    <div class="fw-bold mt-5">ID da Província</div>
                                    <div class="text-gray-600">{{ $member->order_registration_number }}</div>
                                    <!--begin::Details item-->
                                    <!--begin::Details item-->
                                    <div class="fw-bold mt-5">ID da Ordem</div>
                                    <div class="text-gray-600">{{ $member->order_registration_number }}</div>
                                    <!--begin::Details item-->
                                    <!--begin::Details item-->
                                    <div class="fw-bold mt-5">Email</div>
                                    <div class="text-gray-600">
                                        <a href="#"
                                            class="text-gray-600 text-hover-primary">info@keenthemes.com</a>
                                    </div>
                                    <!--begin::Details item-->
                                    <!--begin::Details item-->
                                    <div class="fw-bold mt-5">Endereço de Origem</div>

                                    <div class="fs-6">
                                        @if ($originAddress && ($originAddress->rua || $originAddress->cidade || $originAddress->uf || $originAddress->cep))
                                            @if ($originAddress->rua)
                                                    <span class="text-gray-600 fw-semibold">{{ $originAddress->rua }}
                                                        @if ($originAddress->numero)
                                                            , {{ $originAddress->numero }} -                                         
                                                            @if ($originAddress->bairro)
                                                                {{ $originAddress->bairro }}
                                                            @endif
                                                        @endif
                                                    </span>
                                            @endif

                                            @if ($originAddress->cidade || $originAddress->uf)
                                                <div class=" text-gray-600">
                                                    {{ $originAddress->cidade }}@if ($originAddress->cidade && $originAddress->uf)
                                                        -
                                                    @endif{{ $originAddress->uf }}
                                                </div>
                                            @endif
                                            @if ($originAddress->cep)
                                                <div class="text-gray-500">CEP: {{ $originAddress->cep }}</div>
                                            @endif
                                        @else
                                            <span class="text-muted fst-italic">Não informado</span>
                                        @endif
                                    </div>
                                    <!--begin::Details item-->
                                    <!--begin::Details item-->
                                    <div class="fw-bold mt-5">Language</div>
                                    <div class="text-gray-600">English</div>
                                    <!--begin::Details item-->
                                    <!--begin::Details item-->
                                    <div class="fw-bold mt-5">Last Login</div>
                                    <div class="text-gray-600">25 Oct 2023, 10:10 pm</div>
                                    <!--begin::Details item-->
                                </div>
                            </div>
                            <!--end::Details content-->
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Card-->
                    <!--begin::Connected Accounts-->
                    {{-- @include('app.modules.secretary.partials.card-connected-accounts', ['member' => $member]) --}}
                    <!--end::Connected Accounts-->
                </div>
                <!--end::Sidebar-->
                <!--begin::Content-->
                <div class="flex-lg-row-fluid ms-lg-15">
                    <!--begin:::Tabs-->
                    <ul class="nav nav-custom nav-tabs nav-line-tabs nav-line-tabs-2x border-0 fs-4 fw-semibold mb-8">
                        <!--begin:::Tab item-->
                        <li class="nav-item">
                            <a class="nav-link text-active-primary pb-4 active" data-bs-toggle="tab"
                                href="#kt_user_view_overview_tab">Time Line</a>
                        </li>
                        <!--end:::Tab item-->
                        @if($member->role && $member->role->slug !== 'irmao')
                        <!--begin:::Tab item-->
                        <li class="nav-item">
                            <a class="nav-link text-active-primary pb-4" data-kt-countup-tabs="true"
                                data-bs-toggle="tab" href="#kt_user_view_overview_security">Ministérios</a>
                        </li>
                        <!--end:::Tab item-->
                        @endif
                        <!--begin:::Tab item-->
                        <li class="nav-item">
                            <a class="nav-link text-active-primary pb-4" data-bs-toggle="tab"
                                href="#kt_user_view_overview_events_and_logs_tab">Events & Logs</a>
                        </li>
                        <!--end:::Tab item-->
                        <!--begin:::Tab item-->
                        <li class="nav-item ms-auto">
                            <!--begin::Action menu-->
                            <a href="#" class="btn btn-sm btn-primary ps-7" data-kt-menu-trigger="click"
                                data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">Ações
                                <!--begin::Svg Icon | path: icons/duotune/arrows/arr072.svg-->
                                <span class="svg-icon svg-icon-2 me-0">
                                    <i class="fa-solid fa-chevron-down"></i>
                                </span>
                                <!--end::Svg Icon--></a>
                            <!--begin::Menu-->
                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold py-4 w-250px fs-6"
                                data-kt-menu="true">
                                <!--begin::Menu item-->
                                <div class="menu-item px-5">
                                    <div class="menu-content text-muted pb-2 px-5 fs-7 text-uppercase">Payments</div>
                                </div>
                                <!--end::Menu item-->
                                <!--begin::Menu item-->
                                <div class="menu-item px-5">
                                    <a href="#" class="menu-link px-5">Create invoice</a>
                                </div>
                                <!--end::Menu item-->
                                <!--begin::Menu item-->
                                <div class="menu-item px-5">
                                    <a href="#" class="menu-link flex-stack px-5">Create payments
                                        <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                                            title="Specify a target name for future usage and reference"></i></a>
                                </div>
                                <!--end::Menu item-->
                                <!--begin::Menu item-->
                                <div class="menu-item px-5" data-kt-menu-trigger="hover"
                                    data-kt-menu-placement="left-start">
                                    <a href="#" class="menu-link px-5">
                                        <span class="menu-title">Subscription</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <!--begin::Menu sub-->
                                    <div class="menu-sub menu-sub-dropdown w-175px py-4">
                                        <!--begin::Menu item-->
                                        <div class="menu-item px-3">
                                            <a href="#" class="menu-link px-5">Apps</a>
                                        </div>
                                        <!--end::Menu item-->
                                        <!--begin::Menu item-->
                                        <div class="menu-item px-3">
                                            <a href="#" class="menu-link px-5">Billing</a>
                                        </div>
                                        <!--end::Menu item-->
                                        <!--begin::Menu item-->
                                        <div class="menu-item px-3">
                                            <a href="#" class="menu-link px-5">Statements</a>
                                        </div>
                                        <!--end::Menu item-->
                                        <!--begin::Menu separator-->
                                        <div class="separator my-2"></div>
                                        <!--end::Menu separator-->
                                        <!--begin::Menu item-->
                                        <div class="menu-item px-3">
                                            <div class="menu-content px-3">
                                                <label
                                                    class="form-check form-switch form-check-custom form-check-solid">
                                                    <input class="form-check-input w-30px h-20px" type="checkbox"
                                                        value="" name="notifications" checked="checked"
                                                        id="kt_user_menu_notifications" />
                                                    <span class="form-check-label text-muted fs-6"
                                                        for="kt_user_menu_notifications">Notifications</span>
                                                </label>
                                            </div>
                                        </div>
                                        <!--end::Menu item-->
                                    </div>
                                    <!--end::Menu sub-->
                                </div>
                                <!--end::Menu item-->
                                <!--begin::Menu separator-->
                                <div class="separator my-3"></div>
                                <!--end::Menu separator-->
                                <!--begin::Menu item-->
                                <div class="menu-item px-5">
                                    <div class="menu-content text-muted pb-2 px-5 fs-7 text-uppercase">Account</div>
                                </div>
                                <!--end::Menu item-->
                                <!--begin::Menu item-->
                                <div class="menu-item px-5">
                                    <a href="#" class="menu-link px-5">Reports</a>
                                </div>
                                <!--end::Menu item-->
                                <!--begin::Menu item-->
                                <div class="menu-item px-5 my-1">
                                    <a href="#" class="menu-link px-5">Account Settings</a>
                                </div>
                                <!--end::Menu item-->
                                <!--begin::Menu item-->
                                <div class="menu-item px-5">
                                    <a class="menu-link text-danger px-5" data-action="delete"
                            data-id="{{ $member->id }}">Deletar Registro</a>

                                </div>
                                <!--end::Menu item-->
                            </div>
                            <!--end::Menu-->
                            <!--end::Menu-->
                        </li>
                        <!--end:::Tab item-->
                    </ul>
                    <!--end:::Tabs-->
                    <!--begin:::Tab content-->
                    <div class="tab-content" id="myTabContent">
                        <!--begin:::Tab pane-->
                        <div class="tab-pane fade show active" id="kt_user_view_overview_tab" role="tabpanel">
                            <!--begin::Card-->
                        <!--begin::Card Etapas de Formação-->
                        @include('app.modules.secretary.partials.card-etapa-formacao' )
                        <!--end::Card Etapas de Formação-->
                            <!--end::Card-->
                            <!--begin::Tasks-->
                            <div class="card card-flush mb-6 mb-xl-9">
                                <!--begin::Card header-->
                                <div class="card-header mt-6">
                                    <!--begin::Card title-->
                                    <div class="card-title flex-column">
                                        <h2 class="mb-1">User's Tasks</h2>
                                        <div class="fs-6 fw-semibold text-muted">Total 25 tasks in backlog</div>
                                    </div>
                                    <!--end::Card title-->
                                    <!--begin::Card toolbar-->
                                    <div class="card-toolbar">
                                        <button type="button" class="btn btn-light-primary btn-sm"
                                            data-bs-toggle="modal" data-bs-target="#kt_modal_add_task">
                                            <!--begin::Svg Icon | path: icons/duotune/files/fil005.svg-->
                                            <span class="svg-icon svg-icon-3">
                                                <svg width="24" height="24" viewBox="0 0 24 24"
                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path opacity="0.3"
                                                        d="M19 22H5C4.4 22 4 21.6 4 21V3C4 2.4 4.4 2 5 2H14L20 8V21C20 21.6 19.6 22 19 22ZM16 13.5L12.5 13V10C12.5 9.4 12.6 9.5 12 9.5C11.4 9.5 11.5 9.4 11.5 10L11 13L8 13.5C7.4 13.5 7 13.4 7 14C7 14.6 7.4 14.5 8 14.5H11V18C11 18.6 11.4 19 12 19C12.6 19 12.5 18.6 12.5 18V14.5L16 14C16.6 14 17 14.6 17 14C17 13.4 16.6 13.5 16 13.5Z"
                                                        fill="currentColor" />
                                                    <rect x="11" y="19" width="10" height="2" rx="1"
                                                        transform="rotate(-90 11 19)" fill="currentColor" />
                                                    <rect x="7" y="13" width="10" height="2" rx="1"
                                                        fill="currentColor" />
                                                    <path d="M15 8H20L14 2V7C14 7.6 14.4 8 15 8Z"
                                                        fill="currentColor" />
                                                </svg>
                                            </span>
                                            <!--end::Svg Icon-->Add Task</button>
                                    </div>
                                    <!--end::Card toolbar-->
                                </div>
                                <!--end::Card header-->
                                <!--begin::Card body-->
                                <div class="card-body d-flex flex-column">
                                    <!--begin::Item-->
                                    <div class="d-flex align-items-center position-relative mb-7">
                                        <!--begin::Label-->
                                        <div class="position-absolute top-0 start-0 rounded h-100 bg-secondary w-4px">
                                        </div>
                                        <!--end::Label-->
                                        <!--begin::Details-->
                                        <div class="fw-semibold ms-5">
                                            <a href="#" class="fs-5 fw-bold text-dark text-hover-primary">Create
                                                FureStibe branding logo</a>
                                            <!--begin::Info-->
                                            <div class="fs-7 text-muted">Due in 1 day
                                                <a href="#">Karina Clark</a>
                                            </div>
                                            <!--end::Info-->
                                        </div>
                                        <!--end::Details-->
                                        <!--begin::Menu-->
                                        <button type="button"
                                            class="btn btn-icon btn-active-light-primary w-30px h-30px ms-auto"
                                            data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                            <!--begin::Svg Icon | path: icons/duotune/general/gen019.svg-->
                                            <span class="svg-icon svg-icon-3">
                                                <svg width="24" height="24" viewBox="0 0 24 24"
                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M17.5 11H6.5C4 11 2 9 2 6.5C2 4 4 2 6.5 2H17.5C20 2 22 4 22 6.5C22 9 20 11 17.5 11ZM15 6.5C15 7.9 16.1 9 17.5 9C18.9 9 20 7.9 20 6.5C20 5.1 18.9 4 17.5 4C16.1 4 15 5.1 15 6.5Z"
                                                        fill="currentColor" />
                                                    <path opacity="0.3"
                                                        d="M17.5 22H6.5C4 22 2 20 2 17.5C2 15 4 13 6.5 13H17.5C20 13 22 15 22 17.5C22 20 20 22 17.5 22ZM4 17.5C4 18.9 5.1 20 6.5 20C7.9 20 9 18.9 9 17.5C9 16.1 7.9 15 6.5 15C5.1 15 4 16.1 4 17.5Z"
                                                        fill="currentColor" />
                                                </svg>
                                            </span>
                                            <!--end::Svg Icon-->
                                        </button>
                                        <!--begin::Task menu-->
                                        <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px"
                                            data-kt-menu="true" data-kt-menu-id="kt-users-tasks">
                                            <!--begin::Header-->
                                            <div class="px-7 py-5">
                                                <div class="fs-5 text-dark fw-bold">Update Status</div>
                                            </div>
                                            <!--end::Header-->
                                            <!--begin::Menu separator-->
                                            <div class="separator border-gray-200"></div>
                                            <!--end::Menu separator-->
                                            <!--begin::Form-->
                                            <form class="form px-7 py-5" data-kt-menu-id="kt-users-tasks-form">
                                                <!--begin::Input group-->
                                                <div class="fv-row mb-10">
                                                    <!--begin::Label-->
                                                    <label class="form-label fs-6 fw-semibold">Status:</label>
                                                    <!--end::Label-->
                                                    <!--begin::Input-->
                                                    <select class="form-select form-select-solid" name="task_status"
                                                        data-kt-select2="true" data-placeholder="Select option"
                                                        data-allow-clear="true" data-hide-search="true">
                                                        <option></option>
                                                        <option value="1">Approved</option>
                                                        <option value="2">Pending</option>
                                                        <option value="3">In Process</option>
                                                        <option value="4">Rejected</option>
                                                    </select>
                                                    <!--end::Input-->
                                                </div>
                                                <!--end::Input group-->
                                                <!--begin::Actions-->
                                                <div class="d-flex justify-content-end">
                                                    <button type="button"
                                                        class="btn btn-sm btn-light btn-active-light-primary me-2"
                                                        data-kt-users-update-task-status="reset">Reset</button>
                                                    <button type="submit" class="btn btn-sm btn-primary"
                                                        data-kt-users-update-task-status="submit">
                                                        <span class="indicator-label">Apply</span>
                                                        <span class="indicator-progress">Please wait...
                                                            <span
                                                                class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                                    </button>
                                                </div>
                                                <!--end::Actions-->
                                            </form>
                                            <!--end::Form-->
                                        </div>
                                        <!--end::Task menu-->
                                        <!--end::Menu-->
                                    </div>
                                    <!--end::Item-->
                                    <!--begin::Item-->
                                    <div class="d-flex align-items-center position-relative mb-7">
                                        <!--begin::Label-->
                                        <div class="position-absolute top-0 start-0 rounded h-100 bg-secondary w-4px">
                                        </div>
                                        <!--end::Label-->
                                        <!--begin::Details-->
                                        <div class="fw-semibold ms-5">
                                            <a href="#"
                                                class="fs-5 fw-bold text-dark text-hover-primary">Schedule a meeting
                                                with FireBear CTO John</a>
                                            <!--begin::Info-->
                                            <div class="fs-7 text-muted">Due in 3 days
                                                <a href="#">Rober Doe</a>
                                            </div>
                                            <!--end::Info-->
                                        </div>
                                        <!--end::Details-->
                                        <!--begin::Menu-->
                                        <button type="button"
                                            class="btn btn-icon btn-active-light-primary w-30px h-30px ms-auto"
                                            data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                            <!--begin::Svg Icon | path: icons/duotune/general/gen019.svg-->
                                            <span class="svg-icon svg-icon-3">
                                                <svg width="24" height="24" viewBox="0 0 24 24"
                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M17.5 11H6.5C4 11 2 9 2 6.5C2 4 4 2 6.5 2H17.5C20 2 22 4 22 6.5C22 9 20 11 17.5 11ZM15 6.5C15 7.9 16.1 9 17.5 9C18.9 9 20 7.9 20 6.5C20 5.1 18.9 4 17.5 4C16.1 4 15 5.1 15 6.5Z"
                                                        fill="currentColor" />
                                                    <path opacity="0.3"
                                                        d="M17.5 22H6.5C4 22 2 20 2 17.5C2 15 4 13 6.5 13H17.5C20 13 22 15 22 17.5C22 20 20 22 17.5 22ZM4 17.5C4 18.9 5.1 20 6.5 20C7.9 20 9 18.9 9 17.5C9 16.1 7.9 15 6.5 15C5.1 15 4 16.1 4 17.5Z"
                                                        fill="currentColor" />
                                                </svg>
                                            </span>
                                            <!--end::Svg Icon-->
                                        </button>
                                        <!--begin::Task menu-->
                                        <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px"
                                            data-kt-menu="true" data-kt-menu-id="kt-users-tasks">
                                            <!--begin::Header-->
                                            <div class="px-7 py-5">
                                                <div class="fs-5 text-dark fw-bold">Update Status</div>
                                            </div>
                                            <!--end::Header-->
                                            <!--begin::Menu separator-->
                                            <div class="separator border-gray-200"></div>
                                            <!--end::Menu separator-->
                                            <!--begin::Form-->
                                            <form class="form px-7 py-5" data-kt-menu-id="kt-users-tasks-form">
                                                <!--begin::Input group-->
                                                <div class="fv-row mb-10">
                                                    <!--begin::Label-->
                                                    <label class="form-label fs-6 fw-semibold">Status:</label>
                                                    <!--end::Label-->
                                                    <!--begin::Input-->
                                                    <select class="form-select form-select-solid" name="task_status"
                                                        data-kt-select2="true" data-placeholder="Select option"
                                                        data-allow-clear="true" data-hide-search="true">
                                                        <option></option>
                                                        <option value="1">Approved</option>
                                                        <option value="2">Pending</option>
                                                        <option value="3">In Process</option>
                                                        <option value="4">Rejected</option>
                                                    </select>
                                                    <!--end::Input-->
                                                </div>
                                                <!--end::Input group-->
                                                <!--begin::Actions-->
                                                <div class="d-flex justify-content-end">
                                                    <button type="button"
                                                        class="btn btn-sm btn-light btn-active-light-primary me-2"
                                                        data-kt-users-update-task-status="reset">Reset</button>
                                                    <button type="submit" class="btn btn-sm btn-primary"
                                                        data-kt-users-update-task-status="submit">
                                                        <span class="indicator-label">Apply</span>
                                                        <span class="indicator-progress">Please wait...
                                                            <span
                                                                class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                                    </button>
                                                </div>
                                                <!--end::Actions-->
                                            </form>
                                            <!--end::Form-->
                                        </div>
                                        <!--end::Task menu-->
                                        <!--end::Menu-->
                                    </div>
                                    <!--end::Item-->
                                    <!--begin::Item-->
                                    <div class="d-flex align-items-center position-relative mb-7">
                                        <!--begin::Label-->
                                        <div class="position-absolute top-0 start-0 rounded h-100 bg-secondary w-4px">
                                        </div>
                                        <!--end::Label-->
                                        <!--begin::Details-->
                                        <div class="fw-semibold ms-5">
                                            <a href="#" class="fs-5 fw-bold text-dark text-hover-primary">9
                                                Degree Project Estimation</a>
                                            <!--begin::Info-->
                                            <div class="fs-7 text-muted">Due in 1 week
                                                <a href="#">Neil Owen</a>
                                            </div>
                                            <!--end::Info-->
                                        </div>
                                        <!--end::Details-->
                                        <!--begin::Menu-->
                                        <button type="button"
                                            class="btn btn-icon btn-active-light-primary w-30px h-30px ms-auto"
                                            data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                            <!--begin::Svg Icon | path: icons/duotune/general/gen019.svg-->
                                            <span class="svg-icon svg-icon-3">
                                                <svg width="24" height="24" viewBox="0 0 24 24"
                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M17.5 11H6.5C4 11 2 9 2 6.5C2 4 4 2 6.5 2H17.5C20 2 22 4 22 6.5C22 9 20 11 17.5 11ZM15 6.5C15 7.9 16.1 9 17.5 9C18.9 9 20 7.9 20 6.5C20 5.1 18.9 4 17.5 4C16.1 4 15 5.1 15 6.5Z"
                                                        fill="currentColor" />
                                                    <path opacity="0.3"
                                                        d="M17.5 22H6.5C4 22 2 20 2 17.5C2 15 4 13 6.5 13H17.5C20 13 22 15 22 17.5C22 20 20 22 17.5 22ZM4 17.5C4 18.9 5.1 20 6.5 20C7.9 20 9 18.9 9 17.5C9 16.1 7.9 15 6.5 15C5.1 15 4 16.1 4 17.5Z"
                                                        fill="currentColor" />
                                                </svg>
                                            </span>
                                            <!--end::Svg Icon-->
                                        </button>
                                        <!--begin::Task menu-->
                                        <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px"
                                            data-kt-menu="true" data-kt-menu-id="kt-users-tasks">
                                            <!--begin::Header-->
                                            <div class="px-7 py-5">
                                                <div class="fs-5 text-dark fw-bold">Update Status</div>
                                            </div>
                                            <!--end::Header-->
                                            <!--begin::Menu separator-->
                                            <div class="separator border-gray-200"></div>
                                            <!--end::Menu separator-->
                                            <!--begin::Form-->
                                            <form class="form px-7 py-5" data-kt-menu-id="kt-users-tasks-form">
                                                <!--begin::Input group-->
                                                <div class="fv-row mb-10">
                                                    <!--begin::Label-->
                                                    <label class="form-label fs-6 fw-semibold">Status:</label>
                                                    <!--end::Label-->
                                                    <!--begin::Input-->
                                                    <select class="form-select form-select-solid" name="task_status"
                                                        data-kt-select2="true" data-placeholder="Select option"
                                                        data-allow-clear="true" data-hide-search="true">
                                                        <option></option>
                                                        <option value="1">Approved</option>
                                                        <option value="2">Pending</option>
                                                        <option value="3">In Process</option>
                                                        <option value="4">Rejected</option>
                                                    </select>
                                                    <!--end::Input-->
                                                </div>
                                                <!--end::Input group-->
                                                <!--begin::Actions-->
                                                <div class="d-flex justify-content-end">
                                                    <button type="button"
                                                        class="btn btn-sm btn-light btn-active-light-primary me-2"
                                                        data-kt-users-update-task-status="reset">Reset</button>
                                                    <button type="submit" class="btn btn-sm btn-primary"
                                                        data-kt-users-update-task-status="submit">
                                                        <span class="indicator-label">Apply</span>
                                                        <span class="indicator-progress">Please wait...
                                                            <span
                                                                class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                                    </button>
                                                </div>
                                                <!--end::Actions-->
                                            </form>
                                            <!--end::Form-->
                                        </div>
                                        <!--end::Task menu-->
                                        <!--end::Menu-->
                                    </div>
                                    <!--end::Item-->
                                    <!--begin::Item-->
                                    <div class="d-flex align-items-center position-relative mb-7">
                                        <!--begin::Label-->
                                        <div class="position-absolute top-0 start-0 rounded h-100 bg-secondary w-4px">
                                        </div>
                                        <!--end::Label-->
                                        <!--begin::Details-->
                                        <div class="fw-semibold ms-5">
                                            <a href="#"
                                                class="fs-5 fw-bold text-dark text-hover-primary">Dashboard UI & UX
                                                for Leafr CRM</a>
                                            <!--begin::Info-->
                                            <div class="fs-7 text-muted">Due in 1 week
                                                <a href="#">Olivia Wild</a>
                                            </div>
                                            <!--end::Info-->
                                        </div>
                                        <!--end::Details-->
                                        <!--begin::Menu-->
                                        <button type="button"
                                            class="btn btn-icon btn-active-light-primary w-30px h-30px ms-auto"
                                            data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                            <!--begin::Svg Icon | path: icons/duotune/general/gen019.svg-->
                                            <span class="svg-icon svg-icon-3">
                                                <svg width="24" height="24" viewBox="0 0 24 24"
                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M17.5 11H6.5C4 11 2 9 2 6.5C2 4 4 2 6.5 2H17.5C20 2 22 4 22 6.5C22 9 20 11 17.5 11ZM15 6.5C15 7.9 16.1 9 17.5 9C18.9 9 20 7.9 20 6.5C20 5.1 18.9 4 17.5 4C16.1 4 15 5.1 15 6.5Z"
                                                        fill="currentColor" />
                                                    <path opacity="0.3"
                                                        d="M17.5 22H6.5C4 22 2 20 2 17.5C2 15 4 13 6.5 13H17.5C20 13 22 15 22 17.5C22 20 20 22 17.5 22ZM4 17.5C4 18.9 5.1 20 6.5 20C7.9 20 9 18.9 9 17.5C9 16.1 7.9 15 6.5 15C5.1 15 4 16.1 4 17.5Z"
                                                        fill="currentColor" />
                                                </svg>
                                            </span>
                                            <!--end::Svg Icon-->
                                        </button>
                                        <!--begin::Task menu-->
                                        <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px"
                                            data-kt-menu="true" data-kt-menu-id="kt-users-tasks">
                                            <!--begin::Header-->
                                            <div class="px-7 py-5">
                                                <div class="fs-5 text-dark fw-bold">Update Status</div>
                                            </div>
                                            <!--end::Header-->
                                            <!--begin::Menu separator-->
                                            <div class="separator border-gray-200"></div>
                                            <!--end::Menu separator-->
                                            <!--begin::Form-->
                                            <form class="form px-7 py-5" data-kt-menu-id="kt-users-tasks-form">
                                                <!--begin::Input group-->
                                                <div class="fv-row mb-10">
                                                    <!--begin::Label-->
                                                    <label class="form-label fs-6 fw-semibold">Status:</label>
                                                    <!--end::Label-->
                                                    <!--begin::Input-->
                                                    <select class="form-select form-select-solid" name="task_status"
                                                        data-kt-select2="true" data-placeholder="Select option"
                                                        data-allow-clear="true" data-hide-search="true">
                                                        <option></option>
                                                        <option value="1">Approved</option>
                                                        <option value="2">Pending</option>
                                                        <option value="3">In Process</option>
                                                        <option value="4">Rejected</option>
                                                    </select>
                                                    <!--end::Input-->
                                                </div>
                                                <!--end::Input group-->
                                                <!--begin::Actions-->
                                                <div class="d-flex justify-content-end">
                                                    <button type="button"
                                                        class="btn btn-sm btn-light btn-active-light-primary me-2"
                                                        data-kt-users-update-task-status="reset">Reset</button>
                                                    <button type="submit" class="btn btn-sm btn-primary"
                                                        data-kt-users-update-task-status="submit">
                                                        <span class="indicator-label">Apply</span>
                                                        <span class="indicator-progress">Please wait...
                                                            <span
                                                                class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                                    </button>
                                                </div>
                                                <!--end::Actions-->
                                            </form>
                                            <!--end::Form-->
                                        </div>
                                        <!--end::Task menu-->
                                        <!--end::Menu-->
                                    </div>
                                    <!--end::Item-->
                                    <!--begin::Item-->
                                    <div class="d-flex align-items-center position-relative">
                                        <!--begin::Label-->
                                        <div class="position-absolute top-0 start-0 rounded h-100 bg-secondary w-4px">
                                        </div>
                                        <!--end::Label-->
                                        <!--begin::Details-->
                                        <div class="fw-semibold ms-5">
                                            <a href="#" class="fs-5 fw-bold text-dark text-hover-primary">Mivy
                                                App R&D, Meeting with clients</a>
                                            <!--begin::Info-->
                                            <div class="fs-7 text-muted">Due in 2 weeks
                                                <a href="#">Sean Bean</a>
                                            </div>
                                            <!--end::Info-->
                                        </div>
                                        <!--end::Details-->
                                        <!--begin::Menu-->
                                        <button type="button"
                                            class="btn btn-icon btn-active-light-primary w-30px h-30px ms-auto"
                                            data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                            <!--begin::Svg Icon | path: icons/duotune/general/gen019.svg-->
                                            <span class="svg-icon svg-icon-3">
                                                <svg width="24" height="24" viewBox="0 0 24 24"
                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M17.5 11H6.5C4 11 2 9 2 6.5C2 4 4 2 6.5 2H17.5C20 2 22 4 22 6.5C22 9 20 11 17.5 11ZM15 6.5C15 7.9 16.1 9 17.5 9C18.9 9 20 7.9 20 6.5C20 5.1 18.9 4 17.5 4C16.1 4 15 5.1 15 6.5Z"
                                                        fill="currentColor" />
                                                    <path opacity="0.3"
                                                        d="M17.5 22H6.5C4 22 2 20 2 17.5C2 15 4 13 6.5 13H17.5C20 13 22 15 22 17.5C22 20 20 22 17.5 22ZM4 17.5C4 18.9 5.1 20 6.5 20C7.9 20 9 18.9 9 17.5C9 16.1 7.9 15 6.5 15C5.1 15 4 16.1 4 17.5Z"
                                                        fill="currentColor" />
                                                </svg>
                                            </span>
                                            <!--end::Svg Icon-->
                                        </button>
                                        <!--begin::Task menu-->
                                        <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px"
                                            data-kt-menu="true" data-kt-menu-id="kt-users-tasks">
                                            <!--begin::Header-->
                                            <div class="px-7 py-5">
                                                <div class="fs-5 text-dark fw-bold">Update Status</div>
                                            </div>
                                            <!--end::Header-->
                                            <!--begin::Menu separator-->
                                            <div class="separator border-gray-200"></div>
                                            <!--end::Menu separator-->
                                            <!--begin::Form-->
                                            <form class="form px-7 py-5" data-kt-menu-id="kt-users-tasks-form">
                                                <!--begin::Input group-->
                                                <div class="fv-row mb-10">
                                                    <!--begin::Label-->
                                                    <label class="form-label fs-6 fw-semibold">Status:</label>
                                                    <!--end::Label-->
                                                    <!--begin::Input-->
                                                    <select class="form-select form-select-solid" name="task_status"
                                                        data-kt-select2="true" data-placeholder="Select option"
                                                        data-allow-clear="true" data-hide-search="true">
                                                        <option></option>
                                                        <option value="1">Approved</option>
                                                        <option value="2">Pending</option>
                                                        <option value="3">In Process</option>
                                                        <option value="4">Rejected</option>
                                                    </select>
                                                    <!--end::Input-->
                                                </div>
                                                <!--end::Input group-->
                                                <!--begin::Actions-->
                                                <div class="d-flex justify-content-end">
                                                    <button type="button"
                                                        class="btn btn-sm btn-light btn-active-light-primary me-2"
                                                        data-kt-users-update-task-status="reset">Reset</button>
                                                    <button type="submit" class="btn btn-sm btn-primary"
                                                        data-kt-users-update-task-status="submit">
                                                        <span class="indicator-label">Apply</span>
                                                        <span class="indicator-progress">Please wait...
                                                            <span
                                                                class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                                    </button>
                                                </div>
                                                <!--end::Actions-->
                                            </form>
                                            <!--end::Form-->
                                        </div>
                                        <!--end::Task menu-->
                                        <!--end::Menu-->
                                    </div>
                                    <!--end::Item-->
                                </div>
                                <!--end::Card body-->
                            </div>
                            <!--end::Tasks-->
                        </div>
                        <!--end:::Tab pane-->
                        @if($member->role && $member->role->slug !== 'irmao')
                        <!--begin:::Tab pane-->
                        @include('app.modules.secretary.partials.tab-ministerios')
                        <!--end:::Tab pane-->
                        @endif
                        <!--begin:::Tab pane-->
                        <div class="tab-pane fade" id="kt_user_view_overview_events_and_logs_tab" role="tabpanel">
                            <!--begin::Card-->
                            <div class="card pt-4 mb-6 mb-xl-9">
                                <!--begin::Card header-->
                                <div class="card-header border-0">
                                    <!--begin::Card title-->
                                    <div class="card-title">
                                        <h2>Login Sessions</h2>
                                    </div>
                                    <!--end::Card title-->
                                    <!--begin::Card toolbar-->
                                    <div class="card-toolbar">
                                        <!--begin::Filter-->
                                        <button type="button" class="btn btn-sm btn-flex btn-light-primary"
                                            id="kt_modal_sign_out_sesions">
                                            <!--begin::Svg Icon | path: icons/duotune/arrows/arr077.svg-->
                                            <span class="svg-icon svg-icon-3">
                                                <svg width="24" height="24" viewBox="0 0 24 24"
                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <rect opacity="0.3" x="4" y="11" width="12"
                                                        height="2" rx="1" fill="currentColor" />
                                                    <path
                                                        d="M5.86875 11.6927L7.62435 10.2297C8.09457 9.83785 8.12683 9.12683 7.69401 8.69401C7.3043 8.3043 6.67836 8.28591 6.26643 8.65206L3.34084 11.2526C2.89332 11.6504 2.89332 12.3496 3.34084 12.7474L6.26643 15.3479C6.67836 15.7141 7.3043 15.6957 7.69401 15.306C8.12683 14.8732 8.09458 14.1621 7.62435 13.7703L5.86875 12.3073C5.67684 12.1474 5.67684 11.8526 5.86875 11.6927Z"
                                                        fill="currentColor" />
                                                    <path
                                                        d="M8 5V6C8 6.55228 8.44772 7 9 7C9.55228 7 10 6.55228 10 6C10 5.44772 10.4477 5 11 5H18C18.5523 5 19 5.44772 19 6V18C19 18.5523 18.5523 19 18 19H11C10.4477 19 10 18.5523 10 18C10 17.4477 9.55228 17 9 17C8.44772 17 8 17.4477 8 18V19C8 20.1046 8.89543 21 10 21H19C20.1046 21 21 20.1046 21 19V5C21 3.89543 20.1046 3 19 3H10C8.89543 3 8 3.89543 8 5Z"
                                                        fill="currentColor" />
                                                </svg>
                                            </span>
                                            <!--end::Svg Icon-->Sign out all sessions</button>
                                        <!--end::Filter-->
                                    </div>
                                    <!--end::Card toolbar-->
                                </div>
                                <!--end::Card header-->
                                <!--begin::Card body-->
                                <div class="card-body pt-0 pb-5">
                                    <!--begin::Table wrapper-->
                                    <div class="table-responsive">
                                        <!--begin::Table-->
                                        <table class="table align-middle table-row-dashed gy-5"
                                            id="kt_table_users_login_session">
                                            <!--begin::Table head-->
                                            <thead class="border-bottom border-gray-200 fs-7 fw-bold">
                                                <!--begin::Table row-->
                                                <tr class="text-start text-muted text-uppercase gs-0">
                                                    <th class="min-w-100px">Location</th>
                                                    <th>Device</th>
                                                    <th>IP Address</th>
                                                    <th class="min-w-125px">Time</th>
                                                    <th class="min-w-70px">Actions</th>
                                                </tr>
                                                <!--end::Table row-->
                                            </thead>
                                            <!--end::Table head-->
                                            <!--begin::Table body-->
                                            <tbody class="fs-6 fw-semibold text-gray-600">
                                                <tr>
                                                    <!--begin::Invoice=-->
                                                    <td>Australia</td>
                                                    <!--end::Invoice=-->
                                                    <!--begin::Status=-->
                                                    <td>Chome - Windows</td>
                                                    <!--end::Status=-->
                                                    <!--begin::Amount=-->
                                                    <td>207.46.48.64</td>
                                                    <!--end::Amount=-->
                                                    <!--begin::Date=-->
                                                    <td>23 seconds ago</td>
                                                    <!--end::Date=-->
                                                    <!--begin::Action=-->
                                                    <td>Current session</td>
                                                    <!--end::Action=-->
                                                </tr>
                                                <tr>
                                                    <!--begin::Invoice=-->
                                                    <td>Australia</td>
                                                    <!--end::Invoice=-->
                                                    <!--begin::Status=-->
                                                    <td>Safari - iOS</td>
                                                    <!--end::Status=-->
                                                    <!--begin::Amount=-->
                                                    <td>207.45.45.303</td>
                                                    <!--end::Amount=-->
                                                    <!--begin::Date=-->
                                                    <td>3 days ago</td>
                                                    <!--end::Date=-->
                                                    <!--begin::Action=-->
                                                    <td>
                                                        <a href="#" data-kt-users-sign-out="single_user">Sign
                                                            out</a>
                                                    </td>
                                                    <!--end::Action=-->
                                                </tr>
                                                <tr>
                                                    <!--begin::Invoice=-->
                                                    <td>Australia</td>
                                                    <!--end::Invoice=-->
                                                    <!--begin::Status=-->
                                                    <td>Chrome - Windows</td>
                                                    <!--end::Status=-->
                                                    <!--begin::Amount=-->
                                                    <td>207.17.37.106</td>
                                                    <!--end::Amount=-->
                                                    <!--begin::Date=-->
                                                    <td>last week</td>
                                                    <!--end::Date=-->
                                                    <!--begin::Action=-->
                                                    <td>Expired</td>
                                                    <!--end::Action=-->
                                                </tr>
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
                            <!--begin::Card-->
                            <div class="card pt-4 mb-6 mb-xl-9">
                                <!--begin::Card header-->
                                <div class="card-header border-0">
                                    <!--begin::Card title-->
                                    <div class="card-title">
                                        <h2>Logs</h2>
                                    </div>
                                    <!--end::Card title-->
                                    <!--begin::Card toolbar-->
                                    <div class="card-toolbar">
                                        <!--begin::Button-->
                                        <button type="button" class="btn btn-sm btn-light-primary">
                                            <!--begin::Svg Icon | path: icons/duotune/files/fil021.svg-->
                                            <span class="svg-icon svg-icon-3">
                                                <svg width="24" height="24" viewBox="0 0 24 24"
                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path opacity="0.3"
                                                        d="M19 15C20.7 15 22 13.7 22 12C22 10.3 20.7 9 19 9C18.9 9 18.9 9 18.8 9C18.9 8.7 19 8.3 19 8C19 6.3 17.7 5 16 5C15.4 5 14.8 5.2 14.3 5.5C13.4 4 11.8 3 10 3C7.2 3 5 5.2 5 8C5 8.3 5 8.7 5.1 9H5C3.3 9 2 10.3 2 12C2 13.7 3.3 15 5 15H19Z"
                                                        fill="currentColor" />
                                                    <path
                                                        d="M13 17.4V12C13 11.4 12.6 11 12 11C11.4 11 11 11.4 11 12V17.4H13Z"
                                                        fill="currentColor" />
                                                    <path opacity="0.3"
                                                        d="M8 17.4H16L12.7 20.7C12.3 21.1 11.7 21.1 11.3 20.7L8 17.4Z"
                                                        fill="currentColor" />
                                                </svg>
                                            </span>
                                            <!--end::Svg Icon-->Download Report</button>
                                        <!--end::Button-->
                                    </div>
                                    <!--end::Card toolbar-->
                                </div>
                                <!--end::Card header-->
                                <!--begin::Card body-->
                                <div class="card-body py-0">
                                    <!--begin::Table wrapper-->
                                    <div class="table-responsive">
                                        <!--begin::Table-->
                                        <table
                                            class="table align-middle table-row-dashed fw-semibold text-gray-600 fs-6 gy-5"
                                            id="kt_table_users_logs">
                                            <!--begin::Table body-->
                                            <tbody>
                                                <!--begin::Table row-->
                                                <tr>
                                                    <!--begin::Badge=-->
                                                    <td class="min-w-70px">
                                                        <div class="badge badge-light-success">200 OK</div>
                                                    </td>
                                                    <!--end::Badge=-->
                                                    <!--begin::Status=-->
                                                    <td>POST /v1/invoices/in_4172_5158/payment</td>
                                                    <!--end::Status=-->
                                                    <!--begin::Timestamp=-->
                                                    <td class="pe-0 text-end min-w-200px">20 Dec 2023, 11:05 am</td>
                                                    <!--end::Timestamp=-->
                                                </tr>
                                                <!--end::Table row-->
                                                <!--begin::Table row-->
                                                <tr>
                                                    <!--begin::Badge=-->
                                                    <td class="min-w-70px">
                                                        <div class="badge badge-light-success">200 OK</div>
                                                    </td>
                                                    <!--end::Badge=-->
                                                    <!--begin::Status=-->
                                                    <td>POST /v1/invoices/in_4172_5158/payment</td>
                                                    <!--end::Status=-->
                                                    <!--begin::Timestamp=-->
                                                    <td class="pe-0 text-end min-w-200px">15 Apr 2023, 10:30 am</td>
                                                    <!--end::Timestamp=-->
                                                </tr>
                                                <!--end::Table row-->
                                                <!--begin::Table row-->
                                                <tr>
                                                    <!--begin::Badge=-->
                                                    <td class="min-w-70px">
                                                        <div class="badge badge-light-success">200 OK</div>
                                                    </td>
                                                    <!--end::Badge=-->
                                                    <!--begin::Status=-->
                                                    <td>POST /v1/invoices/in_3436_8609/payment</td>
                                                    <!--end::Status=-->
                                                    <!--begin::Timestamp=-->
                                                    <td class="pe-0 text-end min-w-200px">10 Mar 2023, 11:30 am</td>
                                                    <!--end::Timestamp=-->
                                                </tr>
                                                <!--end::Table row-->
                                                <!--begin::Table row-->
                                                <tr>
                                                    <!--begin::Badge=-->
                                                    <td class="min-w-70px">
                                                        <div class="badge badge-light-warning">404 WRN</div>
                                                    </td>
                                                    <!--end::Badge=-->
                                                    <!--begin::Status=-->
                                                    <td>POST /v1/customer/c_63d9246d4b75d/not_found</td>
                                                    <!--end::Status=-->
                                                    <!--begin::Timestamp=-->
                                                    <td class="pe-0 text-end min-w-200px">20 Jun 2023, 11:30 am</td>
                                                    <!--end::Timestamp=-->
                                                </tr>
                                                <!--end::Table row-->
                                                <!--begin::Table row-->
                                                <tr>
                                                    <!--begin::Badge=-->
                                                    <td class="min-w-70px">
                                                        <div class="badge badge-light-success">200 OK</div>
                                                    </td>
                                                    <!--end::Badge=-->
                                                    <!--begin::Status=-->
                                                    <td>POST /v1/invoices/in_4750_9706/payment</td>
                                                    <!--end::Status=-->
                                                    <!--begin::Timestamp=-->
                                                    <td class="pe-0 text-end min-w-200px">25 Jul 2023, 5:30 pm</td>
                                                    <!--end::Timestamp=-->
                                                </tr>
                                                <!--end::Table row-->
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
                            <!--begin::Card-->
                            <div class="card pt-4 mb-6 mb-xl-9">
                                <!--begin::Card header-->
                                <div class="card-header border-0">
                                    <!--begin::Card title-->
                                    <div class="card-title">
                                        <h2>Events</h2>
                                    </div>
                                    <!--end::Card title-->
                                    <!--begin::Card toolbar-->
                                    <div class="card-toolbar">
                                        <!--begin::Button-->
                                        <button type="button" class="btn btn-sm btn-light-primary">
                                            <!--begin::Svg Icon | path: icons/duotune/files/fil021.svg-->
                                            <span class="svg-icon svg-icon-3">
                                                <svg width="24" height="24" viewBox="0 0 24 24"
                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path opacity="0.3"
                                                        d="M19 15C20.7 15 22 13.7 22 12C22 10.3 20.7 9 19 9C18.9 9 18.9 9 18.8 9C18.9 8.7 19 8.3 19 8C19 6.3 17.7 5 16 5C15.4 5 14.8 5.2 14.3 5.5C13.4 4 11.8 3 10 3C7.2 3 5 5.2 5 8C5 8.3 5 8.7 5.1 9H5C3.3 9 2 10.3 2 12C2 13.7 3.3 15 5 15H19Z"
                                                        fill="currentColor" />
                                                    <path
                                                        d="M13 17.4V12C13 11.4 12.6 11 12 11C11.4 11 11 11.4 11 12V17.4H13Z"
                                                        fill="currentColor" />
                                                    <path opacity="0.3"
                                                        d="M8 17.4H16L12.7 20.7C12.3 21.1 11.7 21.1 11.3 20.7L8 17.4Z"
                                                        fill="currentColor" />
                                                </svg>
                                            </span>
                                            <!--end::Svg Icon-->Download Report</button>
                                        <!--end::Button-->
                                    </div>
                                    <!--end::Card toolbar-->
                                </div>
                                <!--end::Card header-->
                                <!--begin::Card body-->
                                <div class="card-body py-0">
                                    <!--begin::Table-->
                                    <table
                                        class="table align-middle table-row-dashed fs-6 text-gray-600 fw-semibold gy-5"
                                        id="kt_table_customers_events">
                                        <!--begin::Table body-->
                                        <tbody>
                                            <!--begin::Table row-->
                                            <tr>
                                                <!--begin::Event=-->
                                                <td class="min-w-400px">
                                                    <a href="#"
                                                        class="text-gray-600 text-hover-primary me-1">Emma
                                                        Smith</a>has made payment to
                                                    <a href="#"
                                                        class="fw-bold text-gray-900 text-hover-primary">#XRS-45670</a>
                                                </td>
                                                <!--end::Event=-->
                                                <!--begin::Timestamp=-->
                                                <td class="pe-0 text-gray-600 text-end min-w-200px">24 Jun 2023, 5:20
                                                    pm</td>
                                                <!--end::Timestamp=-->
                                            </tr>
                                            <!--end::Table row-->
                                            <!--begin::Table row-->
                                            <tr>
                                                <!--begin::Event=-->
                                                <td class="min-w-400px">
                                                    <a href="#"
                                                        class="text-gray-600 text-hover-primary me-1">Emma
                                                        Smith</a>has made payment to
                                                    <a href="#"
                                                        class="fw-bold text-gray-900 text-hover-primary">#XRS-45670</a>
                                                </td>
                                                <!--end::Event=-->
                                                <!--begin::Timestamp=-->
                                                <td class="pe-0 text-gray-600 text-end min-w-200px">15 Apr 2023, 5:20
                                                    pm</td>
                                                <!--end::Timestamp=-->
                                            </tr>
                                            <!--end::Table row-->
                                            <!--begin::Table row-->
                                            <tr>
                                                <!--begin::Event=-->
                                                <td class="min-w-400px">Invoice
                                                    <a href="#"
                                                        class="fw-bold text-gray-900 text-hover-primary me-1">#WER-45670</a>is
                                                    <span class="badge badge-light-info">In Progress</span>
                                                </td>
                                                <!--end::Event=-->
                                                <!--begin::Timestamp=-->
                                                <td class="pe-0 text-gray-600 text-end min-w-200px">22 Sep 2023, 9:23
                                                    pm</td>
                                                <!--end::Timestamp=-->
                                            </tr>
                                            <!--end::Table row-->
                                            <!--begin::Table row-->
                                            <tr>
                                                <!--begin::Event=-->
                                                <td class="min-w-400px">Invoice
                                                    <a href="#"
                                                        class="fw-bold text-gray-900 text-hover-primary me-1">#WER-45670</a>is
                                                    <span class="badge badge-light-info">In Progress</span>
                                                </td>
                                                <!--end::Event=-->
                                                <!--begin::Timestamp=-->
                                                <td class="pe-0 text-gray-600 text-end min-w-200px">25 Jul 2023, 5:20
                                                    pm</td>
                                                <!--end::Timestamp=-->
                                            </tr>
                                            <!--end::Table row-->
                                            <!--begin::Table row-->
                                            <tr>
                                                <!--begin::Event=-->
                                                <td class="min-w-400px">
                                                    <a href="#"
                                                        class="text-gray-600 text-hover-primary me-1">Brian Cox</a>has
                                                    made payment to
                                                    <a href="#"
                                                        class="fw-bold text-gray-900 text-hover-primary">#OLP-45690</a>
                                                </td>
                                                <!--end::Event=-->
                                                <!--begin::Timestamp=-->
                                                <td class="pe-0 text-gray-600 text-end min-w-200px">25 Jul 2023, 10:30
                                                    am</td>
                                                <!--end::Timestamp=-->
                                            </tr>
                                            <!--end::Table row-->
                                            <!--begin::Table row-->
                                            <tr>
                                                <!--begin::Event=-->
                                                <td class="min-w-400px">Invoice
                                                    <a href="#"
                                                        class="fw-bold text-gray-900 text-hover-primary me-1">#KIO-45656</a>status
                                                    has changed from
                                                    <span class="badge badge-light-succees me-1">In Transit</span>to
                                                    <span class="badge badge-light-success">Approved</span>
                                                </td>
                                                <!--end::Event=-->
                                                <!--begin::Timestamp=-->
                                                <td class="pe-0 text-gray-600 text-end min-w-200px">20 Jun 2023, 8:43
                                                    pm</td>
                                                <!--end::Timestamp=-->
                                            </tr>
                                            <!--end::Table row-->
                                            <!--begin::Table row-->
                                            <tr>
                                                <!--begin::Event=-->
                                                <td class="min-w-400px">Invoice
                                                    <a href="#"
                                                        class="fw-bold text-gray-900 text-hover-primary me-1">#SEP-45656</a>status
                                                    has changed from
                                                    <span class="badge badge-light-warning me-1">Pending</span>to
                                                    <span class="badge badge-light-info">In Progress</span>
                                                </td>
                                                <!--end::Event=-->
                                                <!--begin::Timestamp=-->
                                                <td class="pe-0 text-gray-600 text-end min-w-200px">21 Feb 2023, 8:43
                                                    pm</td>
                                                <!--end::Timestamp=-->
                                            </tr>
                                            <!--end::Table row-->
                                            <!--begin::Table row-->
                                            <tr>
                                                <!--begin::Event=-->
                                                <td class="min-w-400px">Invoice
                                                    <a href="#"
                                                        class="fw-bold text-gray-900 text-hover-primary me-1">#SEP-45656</a>status
                                                    has changed from
                                                    <span class="badge badge-light-warning me-1">Pending</span>to
                                                    <span class="badge badge-light-info">In Progress</span>
                                                </td>
                                                <!--end::Event=-->
                                                <!--begin::Timestamp=-->
                                                <td class="pe-0 text-gray-600 text-end min-w-200px">05 May 2023, 9:23
                                                    pm</td>
                                                <!--end::Timestamp=-->
                                            </tr>
                                            <!--end::Table row-->
                                            <!--begin::Table row-->
                                            <tr>
                                                <!--begin::Event=-->
                                                <td class="min-w-400px">
                                                    <a href="#"
                                                        class="text-gray-600 text-hover-primary me-1">Brian Cox</a>has
                                                    made payment to
                                                    <a href="#"
                                                        class="fw-bold text-gray-900 text-hover-primary">#OLP-45690</a>
                                                </td>
                                                <!--end::Event=-->
                                                <!--begin::Timestamp=-->
                                                <td class="pe-0 text-gray-600 text-end min-w-200px">15 Apr 2023, 6:05
                                                    pm</td>
                                                <!--end::Timestamp=-->
                                            </tr>
                                            <!--end::Table row-->
                                            <!--begin::Table row-->
                                            <tr>
                                                <!--begin::Event=-->
                                                <td class="min-w-400px">
                                                    <a href="#"
                                                        class="text-gray-600 text-hover-primary me-1">Sean Bean</a>has
                                                    made payment to
                                                    <a href="#"
                                                        class="fw-bold text-gray-900 text-hover-primary">#XRS-45670</a>
                                                </td>
                                                <!--end::Event=-->
                                                <!--begin::Timestamp=-->
                                                <td class="pe-0 text-gray-600 text-end min-w-200px">25 Jul 2023, 11:05
                                                    am</td>
                                                <!--end::Timestamp=-->
                                            </tr>
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
                </div>
                <!--end::Content-->
            </div>
            <!--end::Layout-->
        </div>
        <!--end::Content container-->
    </div>
    <!--end::Content-->

    </div>
    <!--end::Content wrapper-->

    <!--begin::Modal Editar Membro-->
    @include('app.modules.secretary.partials.modal-member-form', [
        'formationStages' => $formationStages,
        'companies' => $companies
    ])
    <!--end::Modal Editar Membro-->

    @push('scripts')
        <script src="{{ url('/js/domusia/secretary.js') }}"></script>
        <script src="{{ url('/js/domusia/ministry.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Inicializar classe DomusiaSecretary para a página show
                const secretaryConfig = {
                    storeUrl: '{{ route('secretary.store') }}',
                    editUrl: '{{ route('secretary.edit', '__ID__') }}',
                    updateUrl: '{{ route('secretary.update', '__ID__') }}',
                    deleteUrl: '{{ route('secretary.destroy', '__ID__') }}',
                    showUrl: '{{ route('secretary.show', '__ID__') }}',
                    statsUrl: '{{ route('secretary.stats') }}',
                    indexUrl: '{{ route('secretary.index') }}',
                    csrfToken: '{{ csrf_token() }}',
                    isShowPage: true,
                    memberId: {{ $member->id }},
                    stageOrders: {
                        @foreach ($formationStages as $stage)
                        '{{ $stage->id }}': {{ $stage->sort_order }},
                        @endforeach
                    }
                };
                
                // Instância do secretary (sem DataTable na página show)
                window.secretaryInstance = new DomusiaSecretary(secretaryConfig);
                
                // Inicializar gerenciador de ministérios (se o membro não for Irmão)
                @if($member->role && $member->role->slug !== 'irmao')
                window.ministryManager = new MemberMinistryManager(
                    {{ $member->id }},
                    '{{ route('secretary.ministries.store', $member->id) }}',
                    '{{ route('secretary.ministries.update', [$member->id, '__MINISTRY_ID__']) }}'
                );
                @endif
                
                // Botão de editar no card
                document.getElementById('btn_edit_member')?.addEventListener('click', function(e) {
                    e.preventDefault();
                    window.secretaryInstance.openEditModal({{ $member->id }});
                });
                
                // Ações de editar e excluir (outros botões)
                document.querySelectorAll('[data-action]').forEach(element => {
                    element.addEventListener('click', function(e) {
                        e.preventDefault();
                        const action = this.dataset.action;
                        const memberId = this.dataset.id;

                        if (action === 'edit') {
                            window.secretaryInstance.openEditModal(memberId);
                        } else if (action === 'delete') {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    title: 'Confirmar exclusão',
                                    text: 'Deseja realmente excluir este membro?',
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#d33',
                                    cancelButtonColor: '#3085d6',
                                    confirmButtonText: 'Sim, excluir',
                                    cancelButtonText: 'Cancelar'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        fetch('{{ route('secretary.destroy', $member->id) }}', {
                                                method: 'DELETE',
                                                headers: {
                                                    'Accept': 'application/json',
                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                }
                                            })
                                            .then(response => response.json())
                                            .then(data => {
                                                if (data.success) {
                                                    Swal.fire('Excluído!', data.message,
                                                        'success').then(() => {
                                                        window.location.href =
                                                            '{{ route('secretary.index') }}';
                                                    });
                                                } else {
                                                    Swal.fire('Erro!', data.message,
                                                        'error');
                                                }
                                            });
                                    }
                                });
                            } else if (confirm('Deseja realmente excluir este membro?')) {
                                fetch('{{ route('secretary.destroy', $member->id) }}', {
                                        method: 'DELETE',
                                        headers: {
                                            'Accept': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        }
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            window.location.href =
                                                '{{ route('secretary.index') }}';
                                        }
                                    });
                            }
                        }
                    });
                });
            });
        </script>
    @endpush

</x-tenant-app-layout>
