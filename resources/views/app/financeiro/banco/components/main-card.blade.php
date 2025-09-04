<!--begin::Navbar-->
<div class="row no-gutters">
    <div class="12 col-sm-12 col-md-8">
        <div class="card mb-6 mb-xl-9">
            <div class="card-body pt-9 pb-0">
                <!--begin::Details-->
                <div class="d-flex flex-wrap flex-sm-nowrap mb-6">
                    <!--begin::Image-->
                    <div
                        class="d-flex flex-center flex-shrink-0 bg-light rounded w-100px h-100px w-lg-150px h-lg-150px me-7 mb-4">
                        <img class="img-fluid w-100 h-100 rounded"
                            src="/assets/media/png/banco3.png" alt="image" />
                    </div>
                    <!--end::Image-->
                    <!--begin::Wrapper-->
                    <div class="flex-grow-1">
                        <!--begin::Head-->
                        <div
                            class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                            <!--begin::Details-->
                            <div class="d-flex flex-column">
                                <!--begin::Status-->
                                <div class="d-flex align-items-center mb-1">
                                    <a href="#"
                                        class="text-gray-800 text-hover-primary fs-2 fw-bold me-3">Movimentação
                                        Bancária</a>
                                    {{-- <span class="badge badge-light-success me-auto">Ativado</span> --}}
                                </div>
                                <!--end::Status-->
                                <!--begin::Description-->
                                <div class="d-flex flex-wrap fw-semibold mb-4 fs-5 text-gray-400">
                                    Todos os
                                    lançamentos relacionados ao Banco</div>
                                <!--end::Description-->
                            </div>
                            <!--end::Details-->
                            <!--begin::Actions-->
                            <div class="d-flex mb-4">
                                {{-- <!--begin::Financeiro Button-->
                                <a href="{{ route('caixa.index') }}"
                                    class="btn btn-sm btn-bg-light btn-active-color-primary me-3">
                                    Financeiro
                                </a>
                                <!--end::Financeiro Button--> --}}

                                <!--begin::Lançamento Button-->
                                <a href="{{ route('banco.list', ['tab' => 'lancamento']) }}"
                                    class="btn btn-sm btn-primary me-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                        height="16" fill="currentColor"
                                        class="bi bi-plus-circle" viewBox="0 0 16 16">
                                        <path
                                            d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                        <path
                                            d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4" />
                                    </svg>
                                    Lançamento
                                </a>
                                <!--end::Lançamento Button-->

                                <!--begin::Menu-->
                                <div class="me-0">
                                    <button
                                        class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary"
                                        data-kt-menu-trigger="click"
                                        data-kt-menu-placement="bottom-end">
                                        <i class="bi bi-three-dots fs-3"></i>
                                    </button>

                                    <!--begin::Menu Dropdown-->
                                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px py-3"
                                        data-kt-menu="true">
                                        <!--begin::Heading-->
                                        <div class="menu-item px-3">
                                            <div
                                                class="menu-content text-muted pb-2 px-3 fs-7 text-uppercase">
                                                Pagamentos</div>
                                        </div>
                                        <!--end::Heading-->

                                        <!--begin::Menu Item-->
                                        <div class="menu-item px-3">
                                            <a href="#" class="menu-link px-3">Criar
                                                Fatura</a>
                                        </div>
                                        <!--end::Menu Item-->

                                        <!--begin::Menu Item-->
                                        <div class="menu-item px-3">
                                            <a href="#" class="menu-link flex-stack px-3">
                                                Criar Pagamento
                                                <i class="fas fa-exclamation-circle ms-2 fs-7"
                                                    data-bs-toggle="tooltip"
                                                    title="Especifique um nome de destino para uso futuro e referência"></i>
                                            </a>
                                        </div>
                                        <!--end::Menu Item-->

                                        <!--begin::Menu Item-->
                                        <div class="menu-item px-3">
                                            <a href="#" class="menu-link px-3">Gerar
                                                Boleto</a>
                                        </div>
                                        <!--end::Menu Item-->

                                        <!--begin::Subscription Menu-->
                                        <div class="menu-item px-3" data-kt-menu-trigger="hover"
                                            data-kt-menu-placement="right-end">
                                            <a href="#" class="menu-link px-3">
                                                <span class="menu-title">Assinatura</span>
                                                <span class="menu-arrow"></span>
                                            </a>

                                            <!--begin::Menu Sub-->
                                            <div class="menu-sub menu-sub-dropdown w-175px py-4">
                                                <!--begin::Menu Items-->
                                                <div class="menu-item px-3">
                                                    <a href="#"
                                                        class="menu-link px-3">Planos</a>
                                                </div>
                                                <div class="menu-item px-3">
                                                    <a href="#"
                                                        class="menu-link px-3">Cobranças</a>
                                                </div>
                                                <div class="menu-item px-3">
                                                    <a href="#"
                                                        class="menu-link px-3">Extratos</a>
                                                </div>
                                                <!--end::Menu Items-->

                                                <!--begin::Menu Separator-->
                                                <div class="separator my-2"></div>
                                                <!--end::Menu Separator-->

                                                <!--begin::Recurring Switch-->
                                                <div class="menu-item px-3">
                                                    <div class="menu-content px-3">
                                                        <label
                                                            class="form-check form-switch form-check-custom form-check-solid">
                                                            <input
                                                                class="form-check-input w-30px h-20px"
                                                                type="checkbox" value="1"
                                                                checked="checked"
                                                                name="notifications" />
                                                            <span
                                                                class="form-check-label text-muted fs-6">Recorrente</span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <!--end::Recurring Switch-->
                                            </div>
                                            <!--end::Menu Sub-->
                                        </div>
                                        <!--end::Subscription Menu-->

                                        <!--begin::Nav item-->
                                        <!-- Link para abrir o modal -->
                                        <div class="menu-item px-3">
                                            <a class="menu-link px-3" href="#"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalConciliacao">
                                                Conciliação Bancária
                                            </a>
                                        </div>
                                        <!--end::Nav item-->
                                        <!--begin::Settings Item-->
                                        <div class="menu-item px-3 my-1">
                                            <a href="#"
                                                class="menu-link px-3">Configurações</a>
                                        </div>
                                        <!--end::Settings Item-->
                                    </div>
                                    <!--end::Menu Dropdown-->
                                </div>
                                <!--end::Menu-->
                            </div>
                            <!--end::Actions-->

                        </div>
                        <!--end::Head-->
                        <!--begin::Info-->
                        <div class="d-flex flex-wrap justify-content-start">
                            <!--begin::Stats-->
                            <div class="d-flex flex-wrap">
                                <!--begin::Stat-->
                                <div
                                    class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                    <!--begin::Number-->
                                    <div class="d-flex align-items-center">
                                        <div class="fs-4 fw-bold">R$
                                            {{ number_format($total, 2, ',', '.') }}</div>
                                    </div>
                                    <!--end::Number-->
                                    <!--begin::Label-->
                                    <div class="fw-semibold fs-6 text-gray-400">Saldo Total</div>
                                    <!--end::Label-->
                                </div>
                                <!--end::Stat-->
                                <!--begin::Stat-->
                                <div
                                    class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                    <!--begin::Number-->
                                    <div class="d-flex align-items-center">
                                        <!--begin::Svg Icon | path: icons/duotune/arrows/arr065.svg-->
                                        <span class="svg-icon svg-icon-3 svg-icon-danger me-2">
                                            <svg width="24" height="24"
                                                viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <rect opacity="0.5" x="11" y="18" width="13"
                                                    height="2" rx="1"
                                                    transform="rotate(-90 11 18)"
                                                    fill="currentColor" />
                                                <path
                                                    d="M11.4343 15.4343L7.25 11.25C6.83579 10.8358 6.16421 10.8358 5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75L11.2929 18.2929C11.6834 18.6834 12.3166 18.6834 12.7071 18.2929L18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25C17.8358 10.8358 17.1642 10.8358 16.75 11.25L12.5657 15.4343C12.2533 15.7467 11.7467 15.7467 11.4343 15.4343Z"
                                                    fill="currentColor" />
                                            </svg>
                                        </span>
                                        <!--end::Svg Icon-->
                                        <div class="fs-4 fw-bold">R$
                                            {{ number_format($ValorSaidas, 2, ',', '.') }}</div>
                                    </div>
                                    <!--end::Number-->
                                    <!--begin::Label-->
                                    <div class="fw-semibold fs-6 text-gray-400">Saída</div>
                                    <!--end::Label-->
                                </div>
                                <!--end::Stat-->
                                <!--begin::Stat-->
                                <div
                                    class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                    <!--begin::Number-->
                                    <div class="d-flex align-items-center">
                                        <!--begin::Svg Icon | path: icons/duotune/arrows/arr066.svg-->
                                        <span class="svg-icon svg-icon-3 svg-icon-success me-2">
                                            <svg width="24" height="24"
                                                viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <rect opacity="0.5" x="13" y="6" width="13"
                                                    height="2" rx="1"
                                                    transform="rotate(90 13 6)"
                                                    fill="currentColor" />
                                                <path
                                                    d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z"
                                                    fill="currentColor" />
                                            </svg>
                                        </span>
                                        <!--end::Svg Icon-->
                                        <div class="fs-4 fw-bold">R$
                                            {{ number_format($valorEntrada, 2, ',', '.') }}</div>
                                    </div>
                                    <!--end::Number-->
                                    <!--begin::Label-->
                                    <div class="fw-semibold fs-6 text-gray-400"
                                        data-kt-countup-prefix="R$ ">Entrada</div>
                                    <!--end::Label-->
                                </div>
                                <!--end::Stat-->
                            </div>
                            <!--end::Stats-->
                        </div>
                        <!--end::Info-->
                    </div>
                    <!--end::Wrapper-->
                </div>
                <!--end::Details-->
                <div class="separator"></div>
                <!--begin::Nav-->
                <ul
                    class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold">
                    <!--begin::Nav item-->
                    <li class="nav-item">
                        <a class="nav-link text-active-primary py-5 me-6 {{ $activeTab === 'overview' ? 'active' : '' }}"
                            href="{{ route('banco.list', ['tab' => 'overview']) }}">
                            Gestão
                        </a>
                    </li>
                    <!--end::Nav item-->
                    <!--begin::Nav item-->
                    <li class="nav-item">
                        <a class="nav-link text-active-primary py-5 me-6 {{ $activeTab === 'lancamento' ? 'active' : '' }}"
                            href="{{ route('banco.list', ['tab' => 'lancamento']) }}">
                            Lançamento
                        </a>
                    </li>
                    <!--end::Nav item-->

                    <!--begin::Nav item-->
                    <li class="nav-item">
                        <a class="nav-link text-active-primary py-5 me-6 {{ $activeTab === 'registros' ? 'active' : '' }}"
                            href="{{ route('banco.list', ['tab' => 'registros']) }}">
                            Registros
                        </a>
                    </li>
                    <!--end::Nav item-->

                    <!--begin::Nav item-->
                    <li class="nav-item">
                        <a class="nav-link text-active-primary py-5 me-6 {{ $activeTab === 'bancos' ? 'active' : '' }}"
                            href="{{ route('banco.list', ['tab' => 'bancos']) }}">
                            Bancos
                        </a>
                    </li>
                    <!--end::Nav item-->

                </ul>
                <!--end::Nav-->
            </div>
        </div>
    </div>
</div>
