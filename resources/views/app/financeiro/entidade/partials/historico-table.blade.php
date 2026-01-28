{{-- resources/views/app/financeiro/entidade/partials/historico-table.blade.php --}}
{{-- Tabela de histórico de conciliações reutilizável para diferentes status --}}

@forelse($dados as $item)
    <tr>
        <td class="ps-0">
            <div class="d-flex flex-column">
                <span class="text-gray-800 fw-bold">{{ $item['data_extrato_formatada'] ?? '-' }}</span>
                <span class="text-muted fs-7" title="Data da Conciliação">
                    <i class="bi bi-clock-history fs-8 me-1"></i>{{ $item['data_conciliacao_formatada'] ?? '-' }}
                </span>
            </div>
        </td>
        <td>
            <!-- Histórico do Banco (Memo) -->
            <div class="text-gray-800 fw-bold fs-6 mb-1">
                {{ $item['descricao'] }}
            </div>
            
            <!-- Detalhes da Transação Interna -->
            <div class="d-flex align-items-center flex-wrap gap-1">
                @if($item['parceiro_nome'] && $item['parceiro_nome'] !== '-')
                    <span class="badge badge-light-secondary fw-bold fs-8" title="Parceiro">
                        {{ $item['parceiro_nome'] }}
                    </span>
                @endif
                
                @if($item['transacao_descricao'] && $item['transacao_descricao'] !== '-' && $item['transacao_descricao'] !== $item['descricao'])
                    <span class="text-gray-600 fs-7 italic">
                        "{{ $item['transacao_descricao'] }}"
                    </span>
                @endif
            </div>

            @if($item['lancamento_padrao'] && $item['lancamento_padrao'] !== '-')
                <div class="mt-1">
                    <span class="text-primary fs-8 fw-semibold border border-primary border-dashed px-2 py-0 rounded">
                        {{ $item['lancamento_padrao'] }}
                    </span>
                </div>
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
            <a href="#" class="btn btn-sm btn-light btn-active-light-primary" 
               data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                Ações <i class="ki-duotone ki-down fs-5 ms-1"></i>
            </a>
            <!--begin::Menu-->
            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-150px py-4" data-kt-menu="true">
                <!--begin::Menu item-->
                <div class="menu-item px-3">
                    <a href="#" class="menu-link px-3" data-action="ver-detalhes" data-id="{{ $item['id'] }}">
                        <i class="ki-duotone ki-eye fs-4 me-2">
                            <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                        </i> Ver Detalhes
                    </a>
                </div>
                <!--end::Menu item-->
                <!--begin::Menu item-->
                <div class="menu-item px-3">
                    <a href="#" class="menu-link px-3" data-action="desfazer" data-id="{{ $item['id'] }}">
                        <i class="ki-duotone ki-arrow-circle-left fs-4 me-2">
                            <span class="path1"></span><span class="path2"></span>
                        </i> Desfazer
                    </a>
                </div>
                <!--end::Menu item-->
            </div>
            <!--end::Menu-->
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
