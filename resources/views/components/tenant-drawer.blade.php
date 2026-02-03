@props([
    'drawerId', // ID único do drawer (obrigatório)
    'title' => 'Drawer', // Título do drawer
    'width' => "{default:'300px', 'md': '500px'}", // Largura responsiva do drawer
    'toggleButtonId' => null, // ID do botão que abre o drawer (opcional)
    'closeButtonId' => null, // ID do botão que fecha o drawer (opcional)
    'showCloseButton' => true, // Se deve mostrar o botão de fechar
    'headerClass' => '', // Classes CSS adicionais para o header
    'bodyClass' => '', // Classes CSS adicionais para o body
    'footerClass' => '', // Classes CSS adicionais para o footer
    'cardClass' => ' card-bordered shadow-none rounded-0 w-100', // Classes CSS para o card
])

@php
    // Gera IDs derivados do drawerId
    $toggleButtonId = $toggleButtonId ?? $drawerId . '_button';
    $closeButtonId = $closeButtonId ?? $drawerId . '_close';
    $headerId = $drawerId . '_header';
    $bodyId = $drawerId . '_body';
    $footerId = $drawerId . '_footer';
    $scrollId = $drawerId . '_scroll';
@endphp

<!--begin::Drawer-->
<div id="{{ $drawerId }}" class="bg-body" data-kt-drawer="true" data-kt-drawer-activate="true"
    data-kt-drawer-toggle="#{{ $toggleButtonId }}" data-kt-drawer-close="#{{ $closeButtonId }}"
    data-kt-drawer-overlay="true" data-kt-drawer-width="{{ $width }}">

    <!--begin::Card-->
    <div class="card {{ $cardClass }}">
        <!--begin::Header-->
        <div class="card-header  {{ $headerClass }}" id="{{ $headerId }}">
            <h3 class="card-title fw-bold text-gray-800">{{ $title }}</h3>
            <div class="card-toolbar">
                @isset($toolbar)
                    {{ $toolbar }}
                @endisset
                @if($showCloseButton)
                    <button type="button" class="btn btn-sm btn-icon btn-active-color-primary"
                        id="{{ $closeButtonId }}">
                        <i class="bi bi-x fs-2"></i>
                    </button>
                @endif
            </div>
        </div>
        <!--end::Header-->

        <!--begin::Body-->
        <div class="card-body position-relative drawer-body {{ $bodyClass }}" id="{{ $bodyId }}">
            <!--begin::Content-->
            <div id="{{ $scrollId }}" class="position-relative scroll-y me-n5 pe-5" data-kt-scroll="true"
                data-kt-scroll-height="auto" data-kt-scroll-wrappers="#{{ $bodyId }}"
                data-kt-scroll-dependencies="#{{ $headerId }}, #{{ $footerId }}"
                data-kt-scroll-offset="5px">

                @isset($body)
                    {{ $body }}
                @else
                {{ $slot }}
                @endisset

            </div>
            <!--end::Content-->
        </div>
        <!--end::Body-->

        @isset($footer)
            <!--begin::Footer-->
            <div class="card-footer text-center {{ $footerClass }}" id="{{ $footerId }}">
                {{ $footer }}
            </div>
            <!--end::Footer-->
        @endisset

    </div>
    <!--end::Card-->
</div>
<!--end::Drawer-->

@push('styles')
<style>
    /* Cor de fundo padrão para o body do drawer (modo light) */
    .drawer-body-default {
        background-color: #f2f5fa !important;
    }

    /* Cor de fundo para o body do drawer no modo dark */
    [data-bs-theme="dark"] .drawer-body-default {
        background-color: #1e1e2d !important;
    }
</style>
@endpush

@push('scripts')
<script>
    (function() {
        function updateDrawerBodyColor() {
            const drawerBodies = document.querySelectorAll('.drawer-body-default');
            const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';

            drawerBodies.forEach(function(body) {
                if (isDark) {
                    body.style.backgroundColor = '#1e1e2d';
                } else {
                    body.style.backgroundColor = '#f2f5fa';
                }
            });
        }

        // Atualizar na inicialização
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', updateDrawerBodyColor);
        } else {
            updateDrawerBodyColor();
        }

        // Observar mudanças no tema
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'data-bs-theme') {
                    updateDrawerBodyColor();
                }
            });
        });

        observer.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['data-bs-theme']
        });
    })();
</script>
@endpush

