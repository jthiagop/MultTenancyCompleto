@props([
    'prefix' => '', // Prefixo para os IDs e names (ex: 'recibo_' => recibo_cep, recibo_logradouro...)
    'dropdownParent' => null, // Parent do Select2 para UF
    'showLabels' => true, // Mostrar labels dos campos
    'required' => false, // Se endereço é obrigatório
    'class' => '', // Classes adicionais para o container
    'cepValue' => null,
    'logradouroValue' => null,
    'numeroValue' => null,
    'bairroValue' => null,
    'cidadeValue' => null,
    'ufValue' => null,
    'complementoValue' => null,
    'showComplemento' => false, // Mostrar campo de complemento
    'colCep' => 'col-md-3',
    'colLogradouro' => 'col-md-7',
    'colNumero' => 'col-md-2',
    'colBairro' => 'col-md-4',
    'colCidade' => 'col-md-4',
    'colUf' => 'col-md-4',
    'colComplemento' => 'col-md-12',
])

@php
    $p = $prefix ? $prefix : '';
    $cepId = $p . 'cep';
    $logradouroId = $p . 'logradouro';
    $numeroId = $p . 'numero';
    $bairroId = $p . 'bairro';
    $cidadeId = $p . 'localidade';
    $ufId = $p . 'uf';
    $complementoId = $p . 'complemento';
@endphp

<div class="tenant-endereco-wrapper {{ $class }}" data-prefix="{{ $p }}">
    <!--begin::Linha 1: CEP, Logradouro, Número-->
    <div class="row g-5 mb-6">
        <div class="{{ $colCep }} fv-row">
            @if($showLabels)
                <label class="fs-6 fw-semibold mb-2 {{ $required ? 'required' : '' }}">CEP</label>
            @endif
            <div class="position-relative">
                <input 
                    type="text" 
                    class="form-control tenant-endereco-cep" 
                    placeholder="00000-000" 
                    id="{{ $cepId }}" 
                    name="cep"
                    value="{{ $cepValue }}"
                    maxlength="9"
                    {{ $required ? 'required' : '' }} />
                <span class="tenant-endereco-cep-spinner position-absolute top-50 end-0 translate-middle-y me-3" style="display: none;">
                    <span class="spinner-border spinner-border-sm text-primary"></span>
                </span>
            </div>
            <div class="invalid-feedback"></div>
        </div>
        <div class="{{ $colLogradouro }} fv-row">
            @if($showLabels)
                <label class="fs-6 fw-semibold mb-2">Rua</label>
            @endif
            <input 
                type="text" 
                class="form-control tenant-endereco-logradouro" 
                placeholder="Logradouro" 
                id="{{ $logradouroId }}" 
                name="logradouro"
                value="{{ $logradouroValue }}" />
            <div class="invalid-feedback"></div>
        </div>
        <div class="{{ $colNumero }} fv-row">
            @if($showLabels)
                <label class="fs-6 fw-semibold mb-2">Nº</label>
            @endif
            <input 
                type="text" 
                class="form-control tenant-endereco-numero" 
                placeholder="—" 
                id="{{ $numeroId }}" 
                name="numero"
                value="{{ $numeroValue }}" />
            <div class="invalid-feedback"></div>
        </div>
    </div>
    <!--end::Linha 1-->

    <!--begin::Linha 2: Bairro, Cidade, UF-->
    <div class="row g-5 mb-6">
        <div class="{{ $colBairro }} fv-row">
            @if($showLabels)
                <label class="fs-6 fw-semibold mb-2">Bairro</label>
            @endif
            <input 
                type="text" 
                class="form-control tenant-endereco-bairro" 
                placeholder="Bairro" 
                id="{{ $bairroId }}" 
                name="bairro"
                value="{{ $bairroValue }}" />
            <div class="invalid-feedback"></div>
        </div>
        <div class="{{ $colCidade }} fv-row">
            @if($showLabels)
                <label class="fs-6 fw-semibold mb-2">Cidade</label>
            @endif
            <input 
                type="text" 
                class="form-control tenant-endereco-cidade" 
                placeholder="Cidade" 
                id="{{ $cidadeId }}" 
                name="localidade"
                value="{{ $cidadeValue }}" />
            <div class="invalid-feedback"></div>
        </div>
        <div class="{{ $colUf }} fv-row">
            @if($showLabels)
                <label class="fs-6 fw-semibold mb-2">Estado</label>
            @endif
            <select 
                id="{{ $ufId }}" 
                name="uf" 
                class="form-select tenant-endereco-uf"
                data-control="select2" 
                @if($dropdownParent) data-dropdown-parent="{{ $dropdownParent }}" @endif
                data-placeholder="UF"
                data-allow-clear="true">
                <option value=""></option>
                <option value="AC" {{ $ufValue == 'AC' ? 'selected' : '' }}>Acre</option>
                <option value="AL" {{ $ufValue == 'AL' ? 'selected' : '' }}>Alagoas</option>
                <option value="AP" {{ $ufValue == 'AP' ? 'selected' : '' }}>Amapá</option>
                <option value="AM" {{ $ufValue == 'AM' ? 'selected' : '' }}>Amazonas</option>
                <option value="BA" {{ $ufValue == 'BA' ? 'selected' : '' }}>Bahia</option>
                <option value="CE" {{ $ufValue == 'CE' ? 'selected' : '' }}>Ceará</option>
                <option value="DF" {{ $ufValue == 'DF' ? 'selected' : '' }}>Distrito Federal</option>
                <option value="ES" {{ $ufValue == 'ES' ? 'selected' : '' }}>Espírito Santo</option>
                <option value="GO" {{ $ufValue == 'GO' ? 'selected' : '' }}>Goiás</option>
                <option value="MA" {{ $ufValue == 'MA' ? 'selected' : '' }}>Maranhão</option>
                <option value="MT" {{ $ufValue == 'MT' ? 'selected' : '' }}>Mato Grosso</option>
                <option value="MS" {{ $ufValue == 'MS' ? 'selected' : '' }}>Mato Grosso do Sul</option>
                <option value="MG" {{ $ufValue == 'MG' ? 'selected' : '' }}>Minas Gerais</option>
                <option value="PA" {{ $ufValue == 'PA' ? 'selected' : '' }}>Pará</option>
                <option value="PB" {{ $ufValue == 'PB' ? 'selected' : '' }}>Paraíba</option>
                <option value="PR" {{ $ufValue == 'PR' ? 'selected' : '' }}>Paraná</option>
                <option value="PE" {{ $ufValue == 'PE' ? 'selected' : '' }}>Pernambuco</option>
                <option value="PI" {{ $ufValue == 'PI' ? 'selected' : '' }}>Piauí</option>
                <option value="RJ" {{ $ufValue == 'RJ' ? 'selected' : '' }}>Rio de Janeiro</option>
                <option value="RN" {{ $ufValue == 'RN' ? 'selected' : '' }}>Rio Grande do Norte</option>
                <option value="RS" {{ $ufValue == 'RS' ? 'selected' : '' }}>Rio Grande do Sul</option>
                <option value="RO" {{ $ufValue == 'RO' ? 'selected' : '' }}>Rondônia</option>
                <option value="RR" {{ $ufValue == 'RR' ? 'selected' : '' }}>Roraima</option>
                <option value="SC" {{ $ufValue == 'SC' ? 'selected' : '' }}>Santa Catarina</option>
                <option value="SP" {{ $ufValue == 'SP' ? 'selected' : '' }}>São Paulo</option>
                <option value="SE" {{ $ufValue == 'SE' ? 'selected' : '' }}>Sergipe</option>
                <option value="TO" {{ $ufValue == 'TO' ? 'selected' : '' }}>Tocantins</option>
            </select>
            <div class="invalid-feedback"></div>
        </div>
    </div>
    <!--end::Linha 2-->

    @if($showComplemento)
    <!--begin::Complemento-->
    <div class="row g-5 mb-6">
        <div class="{{ $colComplemento }} fv-row">
            @if($showLabels)
                <label class="fs-6 fw-semibold mb-2">Complemento</label>
            @endif
            <input 
                type="text" 
                class="form-control tenant-endereco-complemento" 
                placeholder="Apto, Bloco, Sala..." 
                id="{{ $complementoId }}" 
                name="complemento"
                value="{{ $complementoValue }}" />
            <div class="invalid-feedback"></div>
        </div>
    </div>
    <!--end::Complemento-->
    @endif
</div>

@once
@push('scripts')
<script>
/**
 * Inicializa a busca de CEP para todos os componentes tenant-endereco na página.
 * Usa classes CSS para identificar os campos, permitindo múltiplos componentes.
 */
$(document).ready(function() {
    $('.tenant-endereco-wrapper').each(function() {
        const $wrapper = $(this);
        const $cep = $wrapper.find('.tenant-endereco-cep');
        const $spinner = $wrapper.find('.tenant-endereco-cep-spinner');
        const $logradouro = $wrapper.find('.tenant-endereco-logradouro');
        const $bairro = $wrapper.find('.tenant-endereco-bairro');
        const $cidade = $wrapper.find('.tenant-endereco-cidade');
        const $uf = $wrapper.find('.tenant-endereco-uf');
        const $numero = $wrapper.find('.tenant-endereco-numero');
        const camposEndereco = [$logradouro, $bairro, $cidade, $uf];

        function setEnderecoLoading(loading) {
            $spinner.toggle(loading);
            camposEndereco.forEach($el => $el.prop('disabled', loading));
        }

        // Máscara de CEP
        $cep.on('input', function() {
            let value = $(this).val().replace(/\D/g, '');
            if (value.length > 5) {
                value = value.substring(0, 5) + '-' + value.substring(5, 8);
            }
            $(this).val(value);
        });

        // Busca CEP no blur
        $cep.on('blur', function() {
            const cep = $(this).val().replace(/\D/g, '');

            if (cep.length === 0) return;

            if (!/^[0-9]{8}$/.test(cep)) {
                Swal.fire({
                    text: 'CEP inválido. Informe 8 dígitos.',
                    icon: 'warning',
                    buttonsStyling: false,
                    confirmButtonText: 'Ok',
                    customClass: { confirmButton: 'btn btn-primary' }
                });
                return;
            }

            setEnderecoLoading(true);

            $.getJSON("https://viacep.com.br/ws/" + cep + "/json/?callback=?")
                .done(function(dados) {
                    if (dados.erro) {
                        Swal.fire({
                            text: 'CEP não encontrado.',
                            icon: 'warning',
                            buttonsStyling: false,
                            confirmButtonText: 'Ok',
                            customClass: { confirmButton: 'btn btn-primary' }
                        });
                        return;
                    }

                    // Preenche só o que vier (não trata vazio como erro)
                    if (dados.logradouro) $logradouro.val(dados.logradouro);
                    if (dados.bairro) $bairro.val(dados.bairro);
                    if (dados.localidade) $cidade.val(dados.localidade);
                    if (dados.uf) $uf.val(dados.uf).trigger('change');

                    // Foco no número após preencher
                    $numero.focus();
                })
                .fail(function() {
                    Swal.fire({
                        text: 'Erro ao buscar CEP. Tente novamente.',
                        icon: 'error',
                        buttonsStyling: false,
                        confirmButtonText: 'Ok',
                        customClass: { confirmButton: 'btn btn-primary' }
                    });
                })
                .always(function() {
                    setEnderecoLoading(false);
                });
        });
    });
});

/**
 * Helper global para preencher endereço via JS
 * @param {string} prefix - Prefixo dos IDs (ex: 'recibo_')
 * @param {object} address - Objeto com cep, rua, numero, bairro, cidade, uf
 */
function preencherEndereco(prefix, address) {
    const p = prefix || '';
    document.getElementById(p + 'cep').value = address.cep || '';
    document.getElementById(p + 'logradouro').value = address.rua || address.logradouro || '';
    document.getElementById(p + 'numero').value = address.numero || '';
    document.getElementById(p + 'bairro').value = address.bairro || '';
    document.getElementById(p + 'localidade').value = address.cidade || address.localidade || '';
    $('#' + p + 'uf').val(address.uf || '').trigger('change');
    if (document.getElementById(p + 'complemento')) {
        document.getElementById(p + 'complemento').value = address.complemento || '';
    }
}

/**
 * Helper global para limpar endereço via JS
 * @param {string} prefix - Prefixo dos IDs (ex: 'recibo_')
 */
function limparEndereco(prefix) {
    const p = prefix || '';
    document.getElementById(p + 'cep').value = '';
    document.getElementById(p + 'logradouro').value = '';
    document.getElementById(p + 'numero').value = '';
    document.getElementById(p + 'bairro').value = '';
    document.getElementById(p + 'localidade').value = '';
    $('#' + p + 'uf').val('').trigger('change');
    if (document.getElementById(p + 'complemento')) {
        document.getElementById(p + 'complemento').value = '';
    }
}
</script>
@endpush
@endonce
