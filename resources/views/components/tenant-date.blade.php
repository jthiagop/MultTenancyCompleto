@props([
    'name',
    'id' => null,
    'label' => null,
    'placeholder' => 'Informe a data',
    'required' => false,
    'value' => null,
    'class' => ''
])

<div class="col-md-2 fv-row {{ $class }}">
    @if($label)
        <label class="{{ $required ? 'required' : '' }} fs-6 fw-semibold mb-2">
            {{ $label }}
        </label>
    @endif
    <div class="position-relative d-flex align-items-center">
        <!--begin::Icon-->
        <span class="svg-icon svg-icon-2 position-absolute mx-4">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <path opacity="0.3"
                    d="M21 22H3C2.4 22 2 21.6 2 21V5C2 4.4 2.4 4 3 4H21C21.6 4 22 4.4 22 5V21C22 21.6 21.6 22 21 22Z"
                    fill="currentColor" />
                <path
                    d="M6 6C5.4 6 5 5.6 5 5V3C5 2.4 5.4 2 6 2C6.6 2 7 2.4 7 3V5C7 5.6 6.6 6 6 6ZM11 5V3C11 2.4 10.6 2 10 2C9.4 2 9 2.4 9 3V5C9 5.6 9.4 6 10 6C10.6 6 11 5.6 11 5ZM15 5V3C15 2.4 14.6 2 14 2C13.4 2 13 2.4 13 3V5C13 5.6 13.4 6 14 6C14.6 6 15 5.6 15 5ZM19 5V3C19 2.4 18.6 2 18 2C17.4 2 17 2.4 17 3V5C17 5.6 17.4 6 18 6C18.6 6 19 5.6 19 5Z"
                    fill="currentColor" />
                <path
                    d="M8.8 13.1C9.2 13.1 9.5 13 9.7 12.8C9.9 12.6 10.1 12.3 10.1 11.9C10.1 11.6 10 11.3 9.8 11.1C9.6 10.9 9.3 10.8 9 10.8C8.8 10.8 8.6 10.8 8.4 10.9C8.2 11 8.1 11.1 8 11.2C7.9 11.3 7.8 11.4 7.7 11.6C7.6 11.8 7.5 11.9 7.5 12.1C7.5 12.2 7.4 12.2 7.3 12.3C7.2 12.4 7.1 12.4 6.9 12.4C6.7 12.4 6.6 12.3 6.5 12.2C6.4 12.1 6.3 11.9 6.3 11.7C6.3 11.5 6.4 11.3 6.5 11.1C6.6 10.9 6.8 10.7 7 10.5C7.2 10.3 7.5 10.1 7.9 10C8.3 9.9 8.6 9.8 9.1 9.8C9.5 9.8 9.8 9.9 10.1 10C10.4 10.1 10.7 10.3 10.9 10.4C11.1 10.5 11.3 10.8 11.4 11.1C11.5 11.4 11.6 11.6 11.6 11.9C11.6 12.3 11.5 12.6 11.3 12.9C11.1 13.2 10.9 13.5 10.6 13.7C10.9 13.9 11.2 14.1 11.4 14.3C11.6 14.5 11.8 14.7 11.9 15C12 15.3 12.1 15.5 12.1 15.8C12.1 16.2 12 16.5 11.9 16.8C11.8 17.1 11.5 17.4 11.3 17.7C11.1 18 10.7 18.2 10.3 18.3C9.9 18.4 9.5 18.5 9 18.5C8.5 18.5 8.1 18.4 7.7 18.2C7.3 18 7 17.8 6.8 17.6C6.6 17.4 6.4 17.1 6.3 16.8C6.2 16.5 6.1 16.3 6.1 16.1C6.1 15.9 6.2 15.7 6.3 15.6C6.4 15.5 6.6 15.4 6.8 15.4C6.9 15.4 7 15.4 7.1 15.5C7.2 15.6 7.3 15.6 7.3 15.7C7.5 16.2 7.7 16.6 8 16.9C8.3 17.2 8.6 17.3 9 17.3C9.2 17.3 9.5 17.2 9.7 17.1C9.9 17 10.1 16.8 10.3 16.6C10.5 16.4 10.5 16.1 10.5 15.8C10.5 15.3 10.4 15 10.1 14.7C9.8 14.4 9.5 14.3 9.1 14.3C9 14.3 8.9 14.3 8.7 14.3C8.5 14.3 8.4 14.3 8.4 14.3C8.2 14.3 8 14.2 7.9 14.1C7.8 14 7.7 13.8 7.7 13.7C7.7 13.5 7.8 13.4 7.9 13.2C8 13 8.2 13 8.5 13H8.8V13.1ZM15.3 17.5V12.2C14.3 13 13.6 13.3 13.3 13.3C13.1 13.3 13 13.2 12.9 13.1C12.8 13 12.7 12.8 12.7 12.6C12.7 12.4 12.8 12.3 12.9 12.2C13 12.1 13.2 12 13.6 11.8C14.1 11.6 14.5 11.3 14.7 11.1C14.9 10.9 15.2 10.6 15.5 10.3C15.8 10 15.9 9.8 15.9 9.7C15.9 9.6 16.1 9.6 16.3 9.6C16.5 9.6 16.7 9.7 16.8 9.8C16.9 9.9 17 10.2 17 10.5V17.2C17 18 16.7 18.4 16.2 18.4C16 18.4 15.8 18.3 15.6 18.2C15.4 18.1 15.3 17.8 15.3 17.5Z"
                    fill="currentColor" />
            </svg>
        </span>
        <!--end::Icon-->
        <input
            type="text"
            name="{{ $name }}"
            id="{{ $id ?? $name }}"
            placeholder="{{ $placeholder }}"
            value="{{ old($name, $value ?? now()->format('d/m/Y')) }}"
            {{ $required ? 'required' : '' }}
            {!! $attributes->merge(['class' => 'form-control ps-12']) !!} />
    </div>
</div>

@push('scripts')
<script>
    // Inicializar flatpickr para campos de data. For more info, please visit the official plugin site: https://flatpickr.js.org/
    document.addEventListener('DOMContentLoaded', function() {
        // Função para verificar se o locale pt está registrado
        function isLocaleRegistered() {
            if (typeof flatpickr === 'undefined') return false;
            return flatpickr.l10ns && (flatpickr.l10ns.pt || flatpickr.l10ns.pt_BR);
        }

        // Função para inicializar os datepickers
        function initDatepickers() {
        var dateInputs = document.querySelectorAll('[name="{{ $name }}"]');

        dateInputs.forEach(function(dateInput) {
            // Verificar se já foi inicializado
            if (dateInput._flatpickr) {
                return;
            }

                // Verificar se flatpickr está disponível
                if (typeof flatpickr === 'undefined') {
                    console.warn('[TenantDate] Flatpickr não está disponível');
                    return;
                }

                // Verificar se o locale pt está registrado
                if (!isLocaleRegistered()) {
                    console.warn('[TenantDate] Locale pt não está registrado. Aguardando...');
                    // Tenta novamente após um delay
                    setTimeout(function() {
                        if (isLocaleRegistered()) {
                            initSingleDatepicker(dateInput);
                        } else {
                            console.error('[TenantDate] Locale pt não está disponível após espera');
                            // Inicializa sem locale como fallback
                            initSingleDatepicker(dateInput, false);
                        }
                    }, 200);
                    return;
                }

                initSingleDatepicker(dateInput);
            });
        }

        // Função para inicializar um único datepicker
        function initSingleDatepicker(dateInput, useLocale = true) {
            try {
                var config = {
                enableTime: false,
                dateFormat: "d/m/Y", // Formato pt-BR para exibição
                allowInput: true, // Permite digitação manual
                clickOpens: true, // Abre o calendário ao clicar
                onChange: function(selectedDates, dateStr, instance) {
                    // Formata para ISO 8601 e define o valor real
                    if (selectedDates.length > 0) {
                        const isoDate = selectedDates[0].toISOString().split('T')[0]; // YYYY-MM-DD
                        instance.input.setAttribute('data-iso', isoDate);
                    }
                }
                };

                // Só adiciona locale se estiver disponível
                if (useLocale && isLocaleRegistered()) {
                    config.locale = "pt";
                }

                flatpickr(dateInput, config);
            } catch (error) {
                console.error('[TenantDate] Erro ao inicializar flatpickr:', error);
            }
        }

        // Aguarda um pouco para garantir que o locale seja registrado
        setTimeout(initDatepickers, 100);
    });
</script>
@endpush

