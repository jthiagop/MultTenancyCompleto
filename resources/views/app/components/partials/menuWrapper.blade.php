            <!--begin::Menu wrapper-->
            <div id="kt_app_sidebar_menu_wrapper" class="app-sidebar-wrapper hover-scroll-overlay-y my-5"
                data-kt-scroll="true" data-kt-scroll-activate="true" data-kt-scroll-height="auto"
                data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer"
                data-kt-scroll-wrappers="#kt_app_sidebar_menu" data-kt-scroll-offset="5px">
                <!--begin::Menu-->
                <div class="menu menu-column menu-rounded menu-sub-indention fw-semibold px-3" id="kt_app_sidebar_menu"
                    data-kt-menu="true" data-kt-menu-accordion="true">

                    <!--begin:Menu item - Dashboard-->
                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                        <!--begin:Menu link-->
                        <span class="menu-link">
                            <span class="menu-icon">
                                <span class="svg-icon svg-icon-2">
                                    <i class="bi bi-speedometer fs-3"></i>
                                </span>
                            </span>
                            <span class="menu-title">Dashboard</span>
                            <span class="menu-arrow"></span>
                        </span>
                        <!--end:Menu link-->
                        <!--begin:Menu sub-->
                        <div class="menu-sub menu-sub-accordion">
                            <!--begin:Menu item-->
                            <div class="menu-item">
                                <a class="menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                                    href="{{ route('dashboard') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Ínicio</span>
                                </a>
                            </div>
                            <!--end:Menu item-->
                            @can('financeiro.index')
                                <div class="menu-item">
                                    <a class="menu-link {{ request()->routeIs('banco.list') ? 'active' : '' }}"
                                        href="{{ route('banco.list') }}">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                                        <span class="menu-title">Financeiro</span>
                                    </a>
                                </div>
                            @endcan
                            @can('contabilidade.index')
                                <div class="menu-item">
                                    <a class="menu-link {{ request()->routeIs('contabilidade.index') ? 'active' : '' }}"
                                        href="{{ route('contabilidade.index') }}">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                                        <span class="menu-title">Contabilidade</span>
                                    </a>
                                </div>
                            @endcan
                            @can('patrimonio.index')
                                <div class="menu-item">
                                    <a class="menu-link {{ request()->routeIs('patrimonio.index') ? 'active' : '' }}"
                                        href="{{ route('patrimonio.index') }}">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                                        <span class="menu-title">Patrimônio</span>
                                    </a>
                                </div>
                            @endcan
                            @can('dizimos.index')
                                <div class="menu-item">
                                    <a class="menu-link {{ request()->routeIs('dizimos.index') ? 'active' : '' }}"
                                        href="{{ route('dizimos.index') }}">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                                        <span class="menu-title">Dízimo e Doações</span>
                                    </a>
                                </div>
                            @endcan
                            @can('fieis.index')
                                <div class="menu-item">
                                    <a class="menu-link {{ request()->routeIs('fieis.index') ? 'active' : '' }}"
                                        href="{{ route('fieis.index') }}">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                                        <span class="menu-title">Fieis</span>
                                    </a>
                                </div>
                            @endcan
                            @can('cemiterio.index')
                                <div class="menu-item">
                                    <a class="menu-link {{ request()->routeIs('cemiterio.index') ? 'active' : '' }}"
                                        href="{{ route('cemiterio.index') }}">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                                        <span class="menu-title">Cemiterio</span>
                                    </a>
                                </div>
                            @endcan
                            @can('notafiscal.index')
                                <div class="menu-item">
                                    <a class="menu-link {{ request()->routeIs('notafiscal.index') ? 'active' : '' }}"
                                        href="{{ route('notafiscal.index') }}">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                                        <span class="menu-title">Notas Fiscais</span>
                                    </a>
                                </div>
                            @endcan
                            @can('company.index')
                                <div class="menu-item">
                                    <a class="menu-link {{ request()->routeIs('company.index') ? 'active' : '' }}"
                                        href="{{ route('company.index') }}">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                                        <span class="menu-title">Organismo</span>
                                    </a>
                                </div>
                            @endcan
                            @can('secretary.index')
                                <div class="menu-item">
                                    <a class="menu-link {{ request()->routeIs('secretary.index') ? 'active' : '' }}"
                                        href="{{ route('secretary.index') }}">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                                        <span class="menu-title">Secretaria</span>
                                    </a>
                                </div>
                            @endcan
                        </div>
                        <!--end:Menu sub-->
                    </div>
                    <!--end:Menu item - Dashboard-->

                    <!--begin:Menu separator-->
                    <div class="menu-item pt-5">
                        <div class="menu-content">
                            <span class="menu-heading fw-bold text-uppercase fs-7">Serviços</span>
                        </div>
                    </div>
                    <!--end:Menu separator-->

                    <!--begin:Menu item - Financeiro-->
                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <i class="fa-solid fa-cash-register fs-3"></i>
                            </span>
                            <span class="menu-title">Financeiro</span>
                            <span class="menu-arrow"></span>
                        </span>
                        <div class="menu-sub menu-sub-accordion menu-active-bg">
                            <div class="menu-item">
                                <a class="menu-link {{ request()->routeIs('banco.list') && request()->get('tab') === 'contas_pagar' ? 'active' : '' }}"
                                    href="{{ route('banco.list', ['tab' => 'contas_pagar']) }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Despesas</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ request()->routeIs('banco.list') && request()->get('tab') === 'contas_receber' ? 'active' : '' }}"
                                    href="{{ route('banco.list', ['tab' => 'contas_receber']) }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Receitas</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ request()->routeIs('banco.list') && request()->get('tab') === 'extrato' ? 'active' : '' }}"
                                    href="{{ route('banco.list', ['tab' => 'extrato']) }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Extrato</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ request()->routeIs('parceiros.*') ? 'active' : '' }}"
                                    href="{{ route('parceiros.index') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Fornecedores e Clientes</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ request()->routeIs('domusia.index') ? 'active' : '' }}"
                                    href="{{ route('domusia.index') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Domus IA <i class="bi bi-robot fs-6 ms-2"></i></span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ request()->routeIs('nfe_entrada.index') ? 'active' : '' }}"
                                    href="{{ route('nfe_entrada.index') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Nota Fiscal</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <!--end:Menu item - Financeiro-->

                    <!--begin:Menu item - patrimonio-->
                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <i class="fa-solid fa-house-chimney fs-3"></i>
                            </span>
                            <span class="menu-title">Patrimônio</span>
                            <span class="menu-arrow"></span>
                        </span>
                        <div class="menu-sub menu-sub-accordion menu-active-bg">
                            <div class="menu-item">
                                <a class="menu-link " href="{{ route('patrimonio.index') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Bens e Imóveis</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link " href="">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Veículos</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link " href="">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Imóveis</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link" href="">
                                    <span class="menu-bullet ">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Cadastro de Imóveis</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link" href="">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Nota Fiscal</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <!--end:Menu item - patrimonio-->

                    @can('company.index')
                        <!--begin:Menu item - Cadastros-->
                        <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                            <span class="menu-link">
                                <span class="menu-icon">
                                    <i class="fa-regular fa-id-card fs-3"></i>
                                </span>
                                <span class="menu-title">Cadastros</span>
                                <span class="menu-arrow"></span>
                            </span>
                            <div class="menu-sub menu-sub-accordion menu-active-bg">
                                <div class="menu-item">
                                    <a class="menu-link {{ request()->routeIs('users.index') ? 'active' : '' }}"
                                        href="{{ route('users.index') }}">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                                        <span class="menu-title">Usuários</span>
                                    </a>
                                </div>
                                <div class="menu-item">
                                    <a class="menu-link {{ request()->routeIs('modules.index') ? 'active' : '' }}"
                                        href="{{ route('modules.list') }}">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                                        <span class="menu-title">Modulos</span>
                                    </a>
                                </div>
                                <div class="menu-item">
                                    <a class="menu-link {{ request()->routeIs('permissions.list') ? 'active' : '' }}"
                                        href="{{ route('permissions.list') }}">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                                        <span class="menu-title">Permissões</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <!--end:Menu item - Cadastros-->
                    @endcan

                </div>
                <!--end::Menu-->
            </div>
            <!--end::Menu wrapper-->
