@props([
    'tabs' => [],
    'active' => '',
    'param' => 'tab',
    'preserveQuery' => true
])

@php
    // Função para gerar URL com querystring
    $generateUrl = function($key) use ($param, $preserveQuery) {
        $currentUrl = url()->current();
        
        if ($preserveQuery) {
            // Preserva todos os parâmetros atuais e substitui apenas o param
            $queryParams = request()->query();
            $queryParams[$param] = $key;
            return $currentUrl . '?' . http_build_query($queryParams);
        } else {
            // Gera URL apenas com param=key
            return $currentUrl . '?' . http_build_query([$param => $key]);
        }
    };
    
    // Mapeamento de variants para cores do Bootstrap
    $variantColors = [
        'danger' => '#f1416c',
        'success' => '#50cd89',
        'primary' => '#009ef7',
        'secondary' => '#e4e6ef',
        'warning' => '#ffc700',
    ];
@endphp

<div class="card mb-5 mb-xl-10">
    <div class="card-body p-0">
        <div class="row g-0" role="tablist">
            @forelse($tabs as $tab)
                @php
                    $isActive = $active === $tab['key'];
                    $variant = $tab['variant'] ?? 'primary';
                    $borderColor = $variantColors[$variant] ?? $variantColors['primary'];
                    $textColor = $variantColors[$variant] ?? $variantColors['primary'];
                @endphp
                
                <div class="col-md {{ !$loop->last ? 'border-end border-gray-300' : '' }}">
                    <a href="{{ $generateUrl($tab['key']) }}" 
                       class="d-block p-6 text-decoration-none hover-bg-light-{{ $variant }} transition-all"
                       role="tab"
                       aria-selected="{{ $isActive ? 'true' : 'false' }}"
                       {{ $isActive ? 'aria-current=page' : '' }}
                       style="border-top: 3px solid {{ $isActive ? $borderColor : 'transparent' }}; transition: all 0.2s ease;">
                        
                        <div class="d-flex flex-column align-items-center text-center">
                            {{-- Label com ícone opcional --}}
                            <div class="d-flex align-items-center gap-2 mb-2">
                                @if(isset($tab['icon']))
                                    <i class="{{ $tab['icon'] }} fs-6 text-muted"></i>
                                @endif
                                <span class="fs-6 fw-semibold text-gray-600">{{ $tab['label'] }}</span>
                            </div>
                            
                            {{-- Valor --}}
                            <div class="fs-2 fw-bold {{ $isActive ? '' : 'text-gray-800' }}" 
                                 style="{{ $isActive ? 'color: ' . $textColor . ';' : '' }}">
                                {{ $tab['value'] }}
                            </div>
                            
                            {{-- Hint opcional --}}
                            @if(isset($tab['hint']))
                                <div class="fs-7 text-muted mt-1">{{ $tab['hint'] }}</div>
                            @endif
                        </div>
                    </a>
                </div>
            @empty
                <div class="col-12 p-6 text-center text-muted">
                    Nenhuma aba disponível
                </div>
            @endforelse
        </div>
    </div>
</div>
