@props([
    'type' => 'button', // button, submit, reset
    'variant' => 'primary', // primary, light, danger, success, warning, info
    'size' => 'sm', // sm, md, lg
    'icon' => null,
    'iconPosition' => 'left', // left, right
    'id' => null,
    'loading' => false,
    'loadingText' => 'Aguarde...',
    'class' => '',
    'confirm' => false, // Se true, mostra confirmação antes de executar ação
    'confirmText' => 'Tem certeza de que deseja cancelar?',
    'confirmIcon' => 'warning',
    'confirmButtonText' => 'Sim, cancelar!',
    'cancelButtonText' => 'Não, voltar',
    'onConfirm' => null, // Função JavaScript a ser executada após confirmação (opcional)
    'resetForm' => false, // Se true, reseta o formulário após confirmação
    'closeModal' => false, // Se true, fecha o modal após confirmação
    'modalId' => null, // ID do modal para fechar (necessário se closeModal for true)
    'formId' => null, // ID do formulário para resetar (necessário se resetForm for true)
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

    $variantValue = $variant ?? 'primary';
    $sizeValue = $size ?? 'sm';
    $variantClass = isset($buttonClasses[$variantValue]) ? $buttonClasses[$variantValue] : 'btn-primary';
    $sizeClass = isset($sizeClasses[$sizeValue]) ? $sizeClasses[$sizeValue] : 'btn-sm';
    $buttonClass = 'btn ' . $variantClass . ' ' . $sizeClass . ' ' . $class;
    $hasLoading = filter_var($loading, FILTER_VALIDATE_BOOLEAN);
@endphp

<button
    type="{{ $type }}"
    class="{{ $buttonClass }}"
    @if($id) id="{{ $id }}" @endif
    {!! $attributes->merge([]) !!}>

    @if($hasLoading)
        <span class="indicator-label">
            @if($icon && $iconPosition === 'left')
                <i class="{{ $icon }} me-2"></i>
            @endif
            {{ $slot }}
            @if($icon && $iconPosition === 'right')
                <i class="{{ $icon }} ms-2"></i>
            @endif
        </span>
        <span class="indicator-progress">
            {{ $loadingText }}
            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
        </span>
    @else
        @if($icon && $iconPosition === 'left')
            <i class="{{ $icon }} me-2"></i>
        @endif
        {{ $slot }}
        @if($icon && $iconPosition === 'right')
            <i class="{{ $icon }} ms-2"></i>
        @endif
    @endif
</button>

@if($confirm)
<script>
    (function() {
        var buttonId = '{{ $id }}';
        var buttonElement = document.getElementById(buttonId);

        if (!buttonElement) {
            return;
        }

        // Aguarda o DOM estar pronto e SweetAlert estar disponível
        function initConfirmButton() {
            if (typeof Swal === 'undefined') {
                setTimeout(initConfirmButton, 100);
                return;
            }

            buttonElement.addEventListener('click', function(e) {
                e.preventDefault();

                Swal.fire({
                    text: '{{ $confirmText }}',
                    icon: '{{ $confirmIcon }}',
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: '{{ $confirmButtonText }}',
                    cancelButtonText: '{{ $cancelButtonText }}',
                    customClass: {
                        confirmButton: 'btn btn-sm btn-danger',
                        cancelButton: 'btn btn-sm btn-primary-light'
                    }
                }).then(function(result) {
                    if (result.value) {
                        @if($onConfirm)
                            // Executa função customizada se fornecida
                            try {
                                {!! $onConfirm !!}
                            } catch (e) {
                                console.error('Erro ao executar onConfirm:', e);
                            }
                        @endif

                        // Comportamento padrão (executa mesmo se onConfirm for fornecido)
                        @if($resetForm && $formId)
                            var form = document.getElementById('{{ $formId }}');
                            if (form) {
                                form.reset();

                                // Limpa campos Select2 se disponível
                                if (typeof $ !== 'undefined' && $.fn.select2) {
                                    $(form).find('select').each(function() {
                                        if ($(this).hasClass('select2-hidden-accessible')) {
                                            $(this).val(null).trigger('change');
                                        }
                                    });
                                }
                            }
                        @endif

                        @if($closeModal && $modalId)
                            var modalElement = document.getElementById('{{ $modalId }}');
                            if (modalElement) {
                                var bsModal = bootstrap.Modal.getInstance(modalElement);
                                if (bsModal) {
                                    bsModal.hide();
                                } else if (typeof $ !== 'undefined') {
                                    $(modalElement).modal('hide');
                                }
                            }
                        @endif
                    } else if (result.dismiss === 'cancel') {
                        Swal.fire({
                            text: 'Seu formulário não foi cancelado!',
                            icon: 'info',
                            buttonsStyling: false,
                            confirmButtonText: 'Ok, entendi!',
                            customClass: {
                                confirmButton: 'btn btn-sm btn-primary',
                            }
                        });
                    }
                });
            });
        }

        // Aguarda o DOM estar pronto
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initConfirmButton);
        } else {
            setTimeout(initConfirmButton, 50);
        }
    })();
</script>
@endif
