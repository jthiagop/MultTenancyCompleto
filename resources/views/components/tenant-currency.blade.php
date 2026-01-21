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
    <div class="input-group mb-3">
        <span class="input-group-text" id="basic-addon-{{ $id ?? $name }}">{{ $currency }}</span>
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
                    removeMaskOnSubmit: true,
                    allowMinus: false,
                    clearMaskOnLostFocus: false
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

