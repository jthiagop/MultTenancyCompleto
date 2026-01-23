{{-- 
    Componente para exibir status de conciliação
    
    Uso:
    <x-status-badge :status="$conciliacao->status_conciliacao" />
--}}

@props(['status'])

@switch($status)
    @case('ok')
        <span class="badge badge-success">
            ✅ Conciliado
        </span>
        @break
    
    @case('pendente')
        <span class="badge badge-warning">
            ⏳ <span class="text-black">Pendente</span>
        </span>
        @break
    
    @case('parcial')
        <span class="badge badge-info">
            🟡 Parcial
        </span>
        @break
    
    @case('divergente')
        <span class="badge badge-danger">
            ❌ Divergente
        </span>
        @break
    
    @case('ignorado')
        <span class="badge badge-secondary">
            🚫 Ignorado
        </span>
        @break
    
    @case('ajustado')
        <span class="badge badge-primary">
            🔧 Ajustado
        </span>
        @break
    
    @case('em análise')
        <span class="badge badge-dark">
            🔍 Em Análise
        </span>
        @break
    
    @default
        <span class="badge badge-secondary">
            ❓ Desconhecido
        </span>
@endswitch
