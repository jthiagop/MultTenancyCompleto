{{-- resources/views/app/financeiro/entidade/partials/historico-table.blade.php --}}
{{-- Tabela de histórico de conciliações reutilizável para diferentes status --}}

@forelse($dados as $item)
    <tr>
        <td class="ps-0">
            <span class="text-gray-600">{{ $item['data_conciliacao_formatada'] ?? '-' }}</span>
        </td>
        <td>
            <span class="text-gray-800 fw-bold">{{ $item['descricao'] }}</span>
            @if($item['lancamento_padrao'] && $item['lancamento_padrao'] !== '-')
                <br>
                <small class="text-muted">{{ $item['lancamento_padrao'] }}</small>
            @endif
        </td>
        <td>
            @php
                $tipoDisplay = [
                    'entrada' => ['text' => 'Entrada', 'badge' => 'badge-light-success'],
                    'saida' => ['text' => 'Saída', 'badge' => 'badge-light-danger'],
                ];
                $tipo = $tipoDisplay[$item['tipo']] ?? $tipoDisplay['entrada'];
            @endphp
            <span class="badge {{ $tipo['badge'] }}">{{ $tipo['text'] }}</span>
        </td>
        <td class="text-end">
            <span class="text-gray-800 fw-bold">
                {{ number_format($item['valor'], 2, ',', '.') }}
            </span>
        </td>
        <td>
            @php
                $statusDisplay = [
                    'ok' => ['text' => 'Conciliado', 'badge' => 'badge-light-success'],
                    'pendente' => ['text' => 'Pendente', 'badge' => 'badge-light-primary'],
                    'ignorado' => ['text' => 'Ignorado', 'badge' => 'badge-light-warning'],
                    'divergente' => ['text' => 'Divergente', 'badge' => 'badge-light-danger'],
                ];
                $statusInfo = $statusDisplay[$item['status']] ?? $statusDisplay['pendente'];
            @endphp
            <span class="badge {{ $statusInfo['badge'] }}">{{ $statusInfo['text'] }}</span>
        </td>
        <td>
            <span class="text-gray-600">{{ $item['usuario'] }}</span>
        </td>
        <td class="text-end">
            <button class="btn btn-icon btn-sm btn-light-danger"
                type="button"
                data-action="desfazer"
                data-id="{{ $item['id'] }}"
                title="Reverter conciliação">
                <i class="bi bi-arrow-counterclockwise fs-6"></i>
            </button>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center py-10">
            <div class="d-flex flex-column align-items-center gap-2">
                <i class="bi bi-inbox fs-1 text-muted"></i>
                <p class="text-muted mb-0">
                    Nenhuma conciliação encontrada para o status selecionado
                </p>
            </div>
        </td>
    </tr>
@endforelse
