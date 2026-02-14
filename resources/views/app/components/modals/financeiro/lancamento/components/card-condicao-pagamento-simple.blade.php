{{-- Card Condição de Pagamento - Versão Simplificada --}}
{{-- 
    Parâmetros:
    - $idPrefix: Prefixo para IDs (ex: 'domusia_') - default: ''
    - $maxParcelas: Número máximo de parcelas - default: 24
    - $showValoresExtras: Mostrar juros/multa/desconto - default: true
    - $compact: Layout compacto - default: false
    - $cardClass: Classes adicionais do card - default: ''
    - $dropdownParent: Elemento pai para dropdowns Select2 (ex: '#drawer_id') - default: ''
--}}

@php
    $idPrefix = $idPrefix ?? '';
    $maxParcelas = $maxParcelas ?? 24;
    $showValoresExtras = $showValoresExtras ?? true;
    $compact = $compact ?? false;
    $cardClass = $cardClass ?? '';
    $dropdownParent = $dropdownParent ?? '';
    
    // IDs dos campos
    $parcelamentoId = $idPrefix . 'parcelamento';
    $vencimentoId = $idPrefix . 'vencimento';
    $pagoCheckboxId = $idPrefix . 'pago_checkbox';
    $pagoLabelId = $idPrefix . 'pago_label';
    $pagoWrapperId = $idPrefix . 'pago_wrapper';
    $agendadoCheckboxId = $idPrefix . 'agendado_checkbox';
    $jurosId = $idPrefix . 'juros';
    $multaId = $idPrefix . 'multa';
    $descontoId = $idPrefix . 'desconto';
    $valorPagoId = $idPrefix . 'valor_pago';
    $valoresExtrasId = $idPrefix . 'valores_extras';
@endphp

<div class="card border border-gray-300 mb-5 {{ $cardClass }}">
    <div class="card-header min-h-45px">
        <h3 class="card-title fs-6 fw-bold">Condição de Pagamento</h3>
    </div>
    <div class="card-body px-6 py-5">
        {{-- Linha 1: Parcelamento, Vencimento, Pago/Recebido, Agendado --}}
        <div class="row g-4 {{ $showValoresExtras ? 'mb-4' : '' }}">
            {{-- Parcelamento --}}
            <x-tenant-select 
                name="parcelamento" 
                id="{{ $parcelamentoId }}" 
                label="Parcelamento"
                placeholder="Selecione"
                :allowClear="false"
                :hideSearch="true"
                dropdown-parent="{{ $dropdownParent ?? '' }}"
                labelSize="fs-7"
                class="col-md-3">
                <option value="avista" selected>À Vista</option>
                @for ($i = 2; $i <= $maxParcelas; $i++)
                    <option value="{{ $i }}x">{{ $i }}x</option>
                @endfor
            </x-tenant-select>

            {{-- Vencimento --}}
            <x-tenant-date 
                name="vencimento" 
                id="{{ $vencimentoId }}" 
                label="Vencimento"
                placeholder="Data de vencimento" 
                class="col-md-3" />

            {{-- Checkbox Pago/Recebido --}}
            <div class="col-md-3 fv-row d-flex align-items-end pb-2" id="{{ $pagoWrapperId }}">
                <div class="form-check form-switch form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" name="pago"
                        value="1" id="{{ $pagoCheckboxId }}" />
                    <label class="form-check-label fw-semibold text-muted fs-7"
                        for="{{ $pagoCheckboxId }}" id="{{ $pagoLabelId }}">
                        Pago
                    </label>
                </div>
            </div>

            {{-- Checkbox Agendado --}}
            <div class="col-md-3 fv-row d-flex align-items-end pb-2">
                <div class="form-check form-switch form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" name="agendado"
                        value="1" id="{{ $agendadoCheckboxId }}" />
                    <label class="form-check-label fw-semibold text-muted fs-7"
                        for="{{ $agendadoCheckboxId }}">
                        Agendado
                    </label>
                </div>
            </div>
        </div>

        {{-- Linha 2: Juros, Multa, Desconto, Valor Pago (escondido por padrão, mostra quando pago/recebido) --}}
        @if($showValoresExtras)
        <div class="row g-4" id="{{ $valoresExtrasId }}" style="display: none;">
            <x-tenant-currency 
                name="juros" 
                id="{{ $jurosId }}" 
                label="Juros"
                placeholder="0,00" 
                class="col-md-3" />
            
            <x-tenant-currency 
                name="multa" 
                id="{{ $multaId }}" 
                label="Multa"
                placeholder="0,00" 
                class="col-md-3" />
            
            <x-tenant-currency 
                name="desconto" 
                id="{{ $descontoId }}" 
                label="Desconto"
                placeholder="0,00" 
                class="col-md-3" />
            
            <x-tenant-currency 
                name="valor_pago" 
                id="{{ $valorPagoId }}"
                label="Valor Pago" 
                placeholder="0,00" 
                class="col-md-3"
                :readonly="true" />
        </div>
        @endif
    </div>
</div>
