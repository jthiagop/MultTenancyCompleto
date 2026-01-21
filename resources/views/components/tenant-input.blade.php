@props([
    'name',
    'id' => null,
    'label' => null,
    'placeholder' => null,
    'required' => false,
    'value' => null,
    'type' => 'text',
    'class' => 'col-md-5',
    'mask' => null, // 'currency' para máscara de moeda brasileira
    'currency' => 'R$', // Símbolo da moeda (usado quando mask='currency')
])

<div class="{{ $class }} fv-row">
    @if($label)
        <label class="d-flex align-items-center fs-6 fw-semibold mb-2 {{ $required ? 'required' : '' }}">
            <span>{{ $label }}</span>
        </label>
    @endif
    @if($mask === 'currency')
        <div class="input-group mb-3">
            <span class="input-group-text" id="basic-addon-{{ $id ?? $name }}">{{ $currency }}</span>
            <input
                type="text"
                class="form-control"
                placeholder="{{ $placeholder ?? '0,00' }}"
                name="{{ $name }}"
                id="{{ $id ?? $name }}"
                value="{{ $value ?? old($name) }}"
                {{ $required ? 'required' : '' }}
                aria-label="{{ $label ?? $name }}"
                aria-describedby="basic-addon-{{ $id ?? $name }}"
                {!! $attributes->merge([]) !!} />
        </div>
    @else
        <input
            type="{{ $type }}"
            class="form-control"
            placeholder="{{ $placeholder }}"
            name="{{ $name }}"
            id="{{ $id ?? $name }}"
            value="{{ $value ?? old($name) }}"
            {{ $required ? 'required' : '' }}
            {!! $attributes->merge([]) !!} />
    @endif
    <!-- Exibindo a mensagem de erro -->
    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

@if($mask === 'currency')
<script>
    (function() {
        var inputId = '{{ $id ?? $name }}';
        var inputElement = document.getElementById(inputId);

        if (!inputElement) {
            return;
        }

        // Função para inicializar a máscara de moeda melhorada
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

            // Aplica a máscara de moeda brasileira melhorada
            // Os números serão empurrados da direita para a esquerda conforme o usuário digita
            try {
                var inputmaskInstance = Inputmask({
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
                    clearMaskOnLostFocus: false,
                    // Configurações para melhorar o comportamento de digitação
                    showMaskOnHover: false,
                    showMaskOnFocus: true,
                    // Força o cursor a sempre ficar no final (empurra números da direita para esquerda)
                    positionCaretOnClick: "none",
                    // Remove zeros à esquerda automaticamente
                    autoUnmask: false,
                    // Configuração para melhorar a experiência de digitação
                    onBeforeMask: function(value, opts) {
                        // Remove caracteres não numéricos exceto vírgula e ponto
                        return value.replace(/[^\d,.-]/g, '');
                    },
                    onKeyDown: function(e, buffer, caretPos, opts) {
                        // Permite navegação com setas e delete/backspace
                        if ([37, 38, 39, 40, 46, 8].indexOf(e.keyCode) !== -1) {
                            return true;
                        }
                        // Permite Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
                        if (e.ctrlKey && [65, 67, 86, 88].indexOf(e.keyCode) !== -1) {
                            return true;
                        }
                        // Permite apenas números
                        if (e.keyCode >= 48 && e.keyCode <= 57) {
                            return true;
                        }
                        // Permite números do teclado numérico
                        if (e.keyCode >= 96 && e.keyCode <= 105) {
                            return true;
                        }
                        return false;
                    },
                    onKeyPress: function(e, buffer, caretPos, opts) {
                        // Quando um número é digitado, força o cursor para o final
                        // Isso garante que os números sejam empurrados da direita para a esquerda
                        setTimeout(function() {
                            var len = inputElement.value.length;
                            inputElement.setSelectionRange(len, len);
                        }, 0);
                    }
                }).mask(inputElement);

                // Marca como inicializado
                inputElement.setAttribute('data-mask-initialized', '1');

                // Adiciona eventos para melhorar a experiência de digitação
                // Garante que os números sejam sempre empurrados da direita para a esquerda
                inputElement.addEventListener('input', function(e) {
                    // Sempre move o cursor para o final após qualquer entrada
                    setTimeout(function() {
                        var len = e.target.value.length;
                        e.target.setSelectionRange(len, len);
                    }, 0);
                });

                inputElement.addEventListener('keydown', function(e) {
                    // Quando um número é digitado, força o cursor para o final
                    if ((e.keyCode >= 48 && e.keyCode <= 57) || (e.keyCode >= 96 && e.keyCode <= 105)) {
                        setTimeout(function() {
                            var len = e.target.value.length;
                            e.target.setSelectionRange(len, len);
                        }, 0);
                    }
                });

                // Previne que o usuário clique no meio do campo e digite
                // Sempre força o cursor para o final
                inputElement.addEventListener('click', function(e) {
                    setTimeout(function() {
                        var len = e.target.value.length;
                        e.target.setSelectionRange(len, len);
                    }, 0);
                });

                // Previne que o usuário selecione parte do texto e digite
                // Sempre força o cursor para o final após digitação
                inputElement.addEventListener('keyup', function(e) {
                    // Se não for uma tecla de navegação, move o cursor para o final
                    if ([37, 38, 39, 40, 46, 8, 9, 16, 17, 18, 91, 93].indexOf(e.keyCode) === -1) {
                        setTimeout(function() {
                            var len = e.target.value.length;
                            e.target.setSelectionRange(len, len);
                        }, 0);
                    }
                });

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
            var element = document.getElementById(inputId);
            if (element && !element.hasAttribute('data-mask-initialized')) {
                inputElement = element;
                setTimeout(initCurrencyMask, 100);
            }
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    })();
</script>
@endif

