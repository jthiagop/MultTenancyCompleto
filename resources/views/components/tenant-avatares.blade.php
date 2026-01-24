@props([
    'currentUser' => null,
    'activeCompany' => null,
    'size' => '35px',
    'borderColor' => 'success',
    'userBgColor' => 'primary',
    'companyBgColor' => 'success',
])

<div class="cursor-pointer symbol-group symbol-hover" 
     data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
     data-kt-menu-attach="parent" 
     data-kt-menu-placement="bottom-end"
     style="position: relative; display: inline-flex; align-items: center;">

    {{-- Avatar do Usuário com corte --}}
    <div class="symbol symbol-circle symbol-{{ $size }} symbol-md-{{ $size }} border border-2 border-{{ $borderColor }}" 
         style="position: relative; z-index: 1;">
        @if($currentUser && $currentUser->avatar)
            <img src="{{ route('file', ['path' => $currentUser->avatar]) }}" 
                 alt="Usuário" 
                 class="rounded-circle" 
                 style="clip-path: circle(50% at 50% 50%);" />
        @else
            {{-- Fallback para as iniciais do usuário se não houver avatar --}}
            <div class="symbol-label fs-2 fw-semibold bg-{{ $userBgColor }} text-inverse-{{ $userBgColor }}">
                {{ strtoupper(substr($currentUser->name ?? 'U', 0, 1)) }}
            </div>
        @endif
        {{-- Círculo de corte usando pseudo-elemento --}}
        <div style="position: absolute; 
                    right: -5px; 
                    top: 50%; 
                    transform: translateY(-50%);
                    width: 20px; 
                    height: 40px; 
                    background: var(--bs-app-bg-color, #fff);
                    border-radius: 50px 0 0 50px;
                    z-index: 2;"></div>
    </div>

    {{-- Avatar da Empresa (sobreposto) --}}
    <div class="symbol symbol-circle symbol-{{ $size }} symbol-md-{{ $size }} border border-2 border-{{ $borderColor }}" 
         style="margin-left: -15px; position: relative; z-index: 3;">
        @if($activeCompany && $activeCompany->avatar)
            <img src="{{ route('file', ['path' => $activeCompany->avatar]) }}" 
                 alt="Empresa" 
                 class="rounded-circle" />
        @else
            {{-- Fallback para as iniciais da empresa se não houver logo --}}
            <div class="symbol-label fs-2 fw-semibold bg-{{ $companyBgColor }} text-inverse-{{ $companyBgColor }}">
                {{ strtoupper(substr($activeCompany->name ?? 'C', 0, 1)) }}
            </div>
        @endif
    </div>
</div>
