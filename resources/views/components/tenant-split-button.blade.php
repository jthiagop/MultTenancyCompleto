@props([
    'submitId' => null,
    'submitText' => 'Enviar',
    'submitIcon' => null,
    'variant' => 'primary',
    'size' => 'sm',
    'direction' => 'dropup', // dropup ou dropdown
    'items' => [], // Array de itens: [['id' => 'id1', 'text' => 'Texto', 'icon' => 'fas fa-icon'], ...]
    'class' => '',
])

@php
    $buttonClasses = [
        'primary' => 'btn-primary',
        'light' => 'btn-light',
        'danger' => 'btn-danger',
        'success' => 'btn-success',
        'warning' => 'btn-warning',
        'info' => 'btn-info',
    ];
    
    $sizeClasses = [
        'sm' => 'btn-sm',
        'md' => '',
        'lg' => 'btn-lg',
    ];
    
    $buttonClass = 'btn ' . ($buttonClasses[$variant] ?? 'btn-primary') . ' ' . ($sizeClasses[$size] ?? 'btn-sm');
    $directionClass = $direction === 'dropup' ? 'dropup' : 'dropdown';
@endphp

<!--begin::Split {{ $direction }} button-->
<div class="btn-group {{ $directionClass }} {{ $class }}">
    <!--begin::Botão principal-->
    <button type="submit" 
            id="{{ $submitId }}" 
            class="{{ $buttonClass }}">
        @if($submitIcon)
            <i class="{{ $submitIcon }} me-2"></i>
        @endif
        <span class="indicator-label">{{ $submitText }}</span>
        <span class="indicator-progress">Aguarde...
            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
        </span>
    </button>
    <!--end::Botão principal-->
    
    <!--begin::Botão de {{ $direction }}-->
    <button type="button" 
            class="{{ $buttonClass }} dropdown-toggle dropdown-toggle-split"
            data-bs-toggle="dropdown" 
            aria-haspopup="true" 
            aria-expanded="false">
        <span class="sr-only">Toggle Dropdown</span>
    </button>
    <!--end::Botão de {{ $direction }}-->
    
    <!--begin::Opções do {{ $direction }}-->
    @if(!empty($items))
        <div class="dropdown-menu">
            @foreach($items as $item)
                <a class="dropdown-item {{ $sizeClasses[$size] }}" 
                   href="#" 
                   id="{{ $item['id'] ?? '' }}">
                    @if(isset($item['icon']))
                        <i class="{{ $item['icon'] }} me-2"></i>
                    @endif
                    {{ $item['text'] ?? '' }}
                </a>
            @endforeach
        </div>
    @else
        {{ $slot }}
    @endif
    <!--end::Opções do {{ $direction }}-->
</div>
<!--end::Split {{ $direction }} button-->

