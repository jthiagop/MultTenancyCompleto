<!-- begin::Tenant Currency Input Component - version 1.0.0 -->
@props([
    'name',
    'id' => null,
    'label' => null,
    'placeholder' => '0,00',
    'required' => false,
    'value' => null,
    'currency' => 'R$',
    'tooltip' => null,
    'class' => 'col-md-2',
    'readonly' => false,
    'showSuggestionStar' => false,
    'suggestionTooltip' => '',
    'suggestedValue' => null,
])

<div class="{{ $class }} fv-row">
    @if($label)
        <label class="d-flex align-items-center fs-6 fw-semibold mb-2 {{ $required ? 'required' : '' }}">
            <span>{{ $label }}</span>
            @if($tooltip)
                <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="{{ $tooltip }}"></i>
            @endif
        </label>
    @endif
    <div class="input-group mb-3" style="position: relative;">
        <span class="input-group-text" id="basic-addon-{{ $id ?? $name }}">{{ $currency }}</span>
        @if($showSuggestionStar)
            <span class="suggestion-star-wrapper suggestion-star-{{ $id ?? $name }}" 
                  style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); z-index: 9999; pointer-events: auto; cursor: pointer; width: 20px; height: 20px; display: none; align-items: center; justify-content: center;"
                  data-bs-toggle="tooltip" 
                  data-bs-placement="top" 
                  data-bs-html="true"
                  data-suggested-value="{{ $suggestedValue }}"
                  title="{{ $suggestionTooltip }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 32 32" style="pointer-events: none;"><g fill="none"><path fill="#fdd835" d="m30.398 15.208l-3.483-1.123c-2.227-.735-3.532-2.538-4.145-4.803l-1.532-7.465c-.04-.147-.138-.345-.438-.345a.46.46 0 0 0-.437.345L18.83 9.285c-.615 2.265-1.917 4.067-4.145 4.803l-3.482 1.122c-.493.16-.5.855-.01 1.023L14.7 17.44c2.22.737 3.515 2.537 4.13 4.793l1.535 7.382c.04.148.123.413.438.413c.332 0 .397-.265.437-.413l1.535-7.382c.615-2.258 1.91-4.058 4.13-4.793l3.508-1.207c.485-.17.477-.866-.015-1.026"/><path fill="#ffee58" d="M30.728 15.52a.53.53 0 0 0-.33-.312l-3.483-1.123c-2.227-.735-3.532-2.538-4.145-4.803l-1.532-7.465c-.023-.085-.103-.24-.195-.285l.495 7.493c.367 3.42.682 5.03 3.412 5.5c2.345.405 5.058.87 5.778.995"/><path fill="#f4b400" d="m30.735 15.91l-6.04 1.385c-2.128.54-3.3 1.772-3.3 4.997l-.593 7.735c.203-.02.368-.13.438-.412l1.535-7.382c.615-2.258 1.91-4.058 4.13-4.793l3.508-1.207a.53.53 0 0 0 .322-.323"/><path fill="#fdd835" d="M10.453 21.703c-2.083-.688-2.273-1.463-2.623-2.77l-.873-3.06c-.052-.198-.567-.198-.622 0L5.742 18.7c-.352 1.303-1.102 2.338-2.382 2.76l-2.04.885c-.283.093-.288.492-.005.588l2.055.727c1.275.422 2.02 1.457 2.375 2.755l.592 2.705c.055.197.568.197.62 0l.695-2.693c.353-1.305.893-2.342 2.625-2.767l1.93-.727a.31.31 0 0 0-.005-.59z"/><path fill="#ffee58" d="M7.123 18.888c.212 1.964.32 2.51 1.912 2.917l3.317.648a.3.3 0 0 0-.152-.108l-1.75-.642c-1.827-.625-2.332-1.42-2.675-3.01s-.707-2.628-.707-2.628c-.128-.343-.31-.325-.31-.325z"/><path fill="#f4b400" d="M7.182 25.748c0-1.853 1.013-2.77 2.623-2.77l2.505-.103s-.145.192-.398.253l-1.635.532c-1.387.558-2.02.837-2.45 2.735c0 0-.555 2.207-.66 2.44c-.145.325-.317.392-.317.392z"/><path fill="#f4b400" stroke="#f4b400" stroke-miterlimit="10" stroke-width="0.25" d="M14.935 7.035c.14-.048.135-.248-.008-.288l-1.93-.52a1.19 1.19 0 0 1-.835-.825l-.76-3.137c-.037-.153-.254-.153-.292.002l-.715 3.126a1.18 1.18 0 0 1-.85.842l-1.918.497c-.142.038-.152.238-.012.288l2.022.7c.363.125.643.42.753.788l.723 2.897c.037.15.252.152.29 0l.747-2.907a1.2 1.2 0 0 1 .76-.783z"/></g></svg>
        </span>
    @endif
        <input
            type="text"
            class="form-control {{ $readonly ? 'bg-light' : '' }}"
            name="{{ $name }}"
            id="{{ $id ?? $name }}"
            placeholder="{{ $placeholder }}"
            value="{{ $value ?? old($name) }}"
            {{ $required ? 'required' : '' }}
            {{ $readonly ? 'readonly style="cursor: not-allowed;"' : '' }}
            aria-label="{{ $label ?? $name }}"
            aria-describedby="basic-addon-{{ $id ?? $name }}"
            {!! $attributes->merge([]) !!} />
    </div>
    <!-- Exibindo a mensagem de erro -->
    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<script>
    (function() {
        var inputId = '{{ $id ?? $name }}';
        var inputElement = document.getElementById(inputId);

        if (!inputElement) {
            return;
        }

        // Função para inicializar a máscara de moeda
        function initCurrencyMask() {
            // Verifica se já foi inicializado
            if (inputElement.hasAttribute('data-mask-initialized')) {
                return;
            }

            // Verifica se Inputmask está disponível
            if (typeof Inputmask === 'undefined') {
                // Tenta novamente após um delay se Inputmask ainda não estiver carregado
                setTimeout(initCurrencyMask, 100);
                return;
            }

            // Aplica a máscara de moeda brasileira
            try {
                Inputmask({
                    alias: "currency",
                    groupSeparator: ".",
                    radixPoint: ",",
                    autoGroup: true,
                    digits: 2,
                    digitsOptional: false,
                    placeholder: "0,00",
                    rightAlign: false,
                    removeMaskOnSubmit: false,
                    allowMinus: false,
                    clearMaskOnLostFocus: false,
                    numericInput: true
                }).mask(inputElement);

                // Marca como inicializado
                inputElement.setAttribute('data-mask-initialized', '1');
            } catch (error) {
                console.error('Erro ao inicializar máscara de moeda para o campo ' + inputId + ':', error);
            }
        }

        // Aguarda o DOM estar pronto
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initCurrencyMask);
        } else {
            // DOM já está pronto, mas pode ser que o elemento ainda não exista
            setTimeout(initCurrencyMask, 50);
        }

        // Também inicializar quando o elemento for inserido dinamicamente (ex: modais)
        var observer = new MutationObserver(function(mutations) {
            if (document.getElementById(inputId) && !document.getElementById(inputId).hasAttribute('data-mask-initialized')) {
                setTimeout(initCurrencyMask, 100);
            }
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    })();
</script>

