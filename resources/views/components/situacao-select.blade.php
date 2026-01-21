{{-- Select de Situação Dinâmico --}}
@props([
    'id' => 'situacao',
    'name' => 'situacao',
    'value' => null,
    'tipo' => 'entrada', // entrada ou saida
    'required' => false
])

<select 
    class="form-select form-select-solid" 
    id="{{ $id }}" 
    name="{{ $name }}"
    data-control="select2"
    data-placeholder="Selecione a situação"
    data-tipo-atual="{{ $tipo }}"
    {{ $required ? 'required' : '' }}
>
    <option value="">Selecione a situação</option>
    
    {{-- Opções para ENTRADA --}}
    <optgroup label="Situações para Entrada" data-tipo="entrada" style="{{ $tipo === 'entrada' ? '' : 'display:none;' }}">
        @foreach(\App\Enums\SituacaoTransacao::forEntrada() as $situacao)
            <option 
                value="{{ $situacao->value }}" 
                {{ $value == $situacao->value ? 'selected' : '' }}
                data-tipo="entrada"
            >
                {{ $situacao->label() }}
            </option>
        @endforeach
    </optgroup>
    
    {{-- Opções para SAÍDA --}}
    <optgroup label="Situações para Saída" data-tipo="saida" style="{{ $tipo === 'saida' ? '' : 'display:none;' }}">
        @foreach(\App\Enums\SituacaoTransacao::forSaida() as $situacao)
            <option 
                value="{{ $situacao->value }}" 
                {{ $value == $situacao->value ? 'selected' : '' }}
                data-tipo="saida"
            >
                {{ $situacao->label() }}
            </option>
        @endforeach
    </optgroup>
</select>

<script>
// Script para atualizar opções do select de situação baseado no tipo
document.addEventListener('DOMContentLoaded', function() {
    const situacaoSelect = document.getElementById('{{ $id }}');
    const tipoSelect = document.getElementById('tipo');
    
    if (!situacaoSelect || !tipoSelect) return;
    
    function atualizarOpcoesSituacao() {
        const tipoAtual = tipoSelect.value; // 'entrada' ou 'saida'
        const optgroups = situacaoSelect.querySelectorAll('optgroup');
        const options = situacaoSelect.querySelectorAll('option[data-tipo]');
        
        // Mostra/esconde optgroups
        optgroups.forEach(optgroup => {
            const optgroupTipo = optgroup.getAttribute('data-tipo');
            if (optgroupTipo === tipoAtual) {
                optgroup.style.display = '';
            } else {
                optgroup.style.display = 'none';
            }
        });
        
        // Habilita/desabilita opções
        options.forEach(option => {
            const optionTipo = option.getAttribute('data-tipo');
            if (optionTipo === tipoAtual) {
                option.style.display = '';
                option.disabled = false;
            } else {
                option.style.display = 'none';
                option.disabled = true;
            }
        });
        
        // Se a opção atualmente selecionada não é válida para o novo tipo, limpa
        const selectedOption = situacaoSelect.querySelector('option:checked');
        if (selectedOption && selectedOption.getAttribute('data-tipo') !== tipoAtual) {
            situacaoSelect.value = '';
            if (typeof $(situacaoSelect).select2 === 'function') {
                $(situacaoSelect).val('').trigger('change');
            }
        }
        
        // Atualiza Select2 se estiver ativo
        if (typeof $(situacaoSelect).select2 === 'function') {
            $(situacaoSelect).select2('destroy');
            $(situacaoSelect).select2({
                placeholder: 'Selecione a situação',
                allowClear: true
            });
        }
    }
    
    // Event listener para mudança no tipo
    tipoSelect.addEventListener('change', atualizarOpcoesSituacao);
    
    // Atualiza na inicialização
    atualizarOpcoesSituacao();
});
</script>
