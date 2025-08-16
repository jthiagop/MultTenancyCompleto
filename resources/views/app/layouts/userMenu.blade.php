<div class="app-navbar-item ms-1 ms-lg-3" id="kt_header_user_menu_toggle">
    <div class="cursor-pointer symbol-group symbol-hover"
        data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-attach="parent"
        data-kt-menu-placement="bottom-end">
        
        {{-- INÍCIO DO NOVO ÍCONE AGRUPADO --}}

        <div class="symbol symbol-circle symbol-35px symbol-md-40px">
            @if($currentUser && $currentUser->avatar)
                <img src="{{ route('file', ['path' => $currentUser->avatar]) }}" alt="Usuário"/>
            @else
                {{-- Fallback para as iniciais do usuário se não houver avatar --}}
                <div class="symbol-label fs-2 fw-semibold bg-primary text-inverse-primary">{{ strtoupper(substr($currentUser->name ?? 'U', 0, 1)) }}</div>
            @endif
        </div>

        <div class="symbol symbol-circle symbol-35px symbol-md-40px">
            @if($activeCompany && $activeCompany->avatar)
                <img src="{{ route('file', ['path' => $activeCompany->avatar]) }}" alt="Empresa"/>
            @else
                {{-- Fallback para as iniciais da empresa se não houver logo --}}
                <div class="symbol-label fs-2 fw-semibold bg-success text-inverse-success">{{ strtoupper(substr($activeCompany->name ?? 'C', 0, 1)) }}</div>
            @endif
        </div>
        
        {{-- FIM DO NOVO ÍCONE AGRUPADO --}}

    </div>

    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-275px"
        data-kt-menu="true">
        <div class="menu-item px-3">
            <div class="menu-content d-flex align-items-center px-3">
                <div class="symbol symbol-50px me-5">
                    <img src="{{ route('file', ['path' => $currentUser->avatar]) }}"
                        alt="user" class="rounded-circle" />
                </div>
                <div class="d-flex flex-column">
                    <div class="fw-bold d-flex align-items-center fs-5">
                        {{ Str::limit($currentUser->name, 20, '...') }}
                    </div>
                    <a href="#"
                        class="fw-semibold text-muted text-hover-primary fs-7">{{ $currentUser->email }}</a>
                </div>
                </div>
        </div>
        <div class="separator my-2"></div>
        {{-- INÍCIO DO SELETOR DE EMPRESAS --}}
        <div class="menu-item px-5" data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="left-start" data-kt-menu-offset="-15px, 0">
            <a href="#" class="menu-link px-5">
                <span class="menu-title">Minhas Empresas</span>
                <span class="menu-arrow"></span>
            </a>
            <div class="menu-sub menu-sub-dropdown w-275px py-4">
                @foreach ($allCompanies as $company)
                <div class="menu-item px-3">
                    <a href="{{ route('session.switch-company', $company->id) }}" 
                       class="menu-link d-flex px-5 @if($activeCompany && $activeCompany->id == $company->id) active @endif">
                        <span class="symbol symbol-20px me-4">
                            @if($company->avatar)
                                <img class="rounded-1" src="{{ route('file', ['path' => $company->avatar]) }}" alt="{{ $company->name }}" />
                            @else
                                <img class="rounded-1" src="/assets/media/png/building.svg" alt="Ícone de empresa" />
                            @endif
                        </span>
                        {{ $company->name }}
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        {{-- FIM DO SELETOR DE EMPRESAS --}}

        <div class="menu-item px-5">
            <a href="{{ route('profile.edit') }}" class="menu-link px-5"> Meu Perfil</a>
        </div>
        <div class="separator my-2"></div>
        <div class="menu-item px-5">
            <a href="{{ route('logout') }}" 
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
               class="menu-link px-5">
               Sair
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>
        </div>
    </div>