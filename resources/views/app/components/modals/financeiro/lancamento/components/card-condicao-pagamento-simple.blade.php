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
            <div class="col-md-3 fv-row d-flex align-items-end pb-2" id="{{ $idPrefix }}agendado_wrapper">
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

        {{-- Accordion de Parcelas (exibido quando parcelamento >= 2x) --}}
        <div class="mt-4" id="{{ $idPrefix }}parcelas_accordion" style="display: none;">
            <div class="separator separator-dashed my-4"></div>
            <div class="d-flex align-items-center gap-2 mb-3">
                <i class="fa-solid fa-layer-group text-primary fs-5"></i>
                <span class="fw-bold text-gray-700 fs-6">Parcelas</span>
                <span class="badge badge-light-primary fs-8" id="{{ $idPrefix }}parcelas_count_badge"></span>
            </div>
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-7 gy-3" id="{{ $idPrefix }}parcelas_table">
                    <thead>
                        <tr class="text-start text-muted fw-bold fs-8 text-uppercase gs-0">
                            <th class="min-w-30px">#</th>
                            <th class="min-w-110px">Vencimento</th>
                            <th class="min-w-100px">Valor (R$)</th>
                            <th class="min-w-80px">% </th>
                            <th class="min-w-150px">Descrição</th>
                            <th class="min-w-80px">Agendado</th>
                        </tr>
                    </thead>
                    <tbody id="{{ $idPrefix }}parcelas_table_body">
                        {{-- Linhas geradas dinamicamente via JS --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Template de linha de parcela para o Domusia drawer --}}
<template id="{{ $idPrefix }}parcela_row_template">
    <tr data-parcela="">
        <td class="parcela-numero fw-bold text-gray-600"></td>
        <td>
            <input type="text"
                class="form-control form-control-sm parcela-vencimento"
                name="parcelas[][vencimento]"
                placeholder="dd/mm/yyyy"
                data-parcela-input="vencimento"
                data-parcela-num=""
                required>
        </td>
        <td>
            <div class="input-group input-group-sm">
                <span class="input-group-text py-1 px-2">R$</span>
                <input type="text"
                    class="form-control parcela-valor"
                    name="parcelas[][valor]"
                    placeholder="0,00"
                    data-parcela-input="valor"
                    data-parcela-num=""
                    required>
            </div>
        </td>
        <td>
            <input type="text"
                class="form-control form-control-sm parcela-percentual"
                name="parcelas[][percentual]"
                placeholder="0.00"
                data-parcela-input="percentual"
                data-parcela-num="">
        </td>

        <td>
            <input type="text"
                class="form-control form-control-sm parcela-descricao"
                name="parcelas[][descricao]"
                placeholder="Descrição"
                data-parcela-input="descricao"
                data-parcela-num=""
                data-descricao-base="">
        </td>
        <td>
            <div class="form-check form-check-custom form-check-solid form-check-sm">
                <input class="form-check-input parcela-agendado"
                    type="checkbox"
                    name="parcelas[][agendado]"
                    value="1"
                    data-parcela-input="agendado"
                    data-parcela-num="">
            </div>
        </td>
    </tr>
</template>
