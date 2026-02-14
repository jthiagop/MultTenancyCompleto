@props([
    'todasEntidades' => null,
    'entidadesBanco' => collect(),
    'entidadesCaixa' => collect(),
    'carouselId' => null,
    'showVariacao' => true,
])

@php
    // Compatibilidade: se todasEntidades não foi passada, faz o merge aqui
    // (para não quebrar código existente)
    if (!$todasEntidades) {
        $todasEntidades = $entidadesBanco->merge($entidadesCaixa)->values();
    }

    // Gera ID único para o carrossel se não foi fornecido
    $carouselId = $carouselId ?? 'kt_sliders_widget_2_slider_' . uniqid();

    // Garante que todasEntidades seja uma coleção
    $todasEntidades = $todasEntidades instanceof \Illuminate\Support\Collection
        ? $todasEntidades
        : collect($todasEntidades);
@endphp

<div class="col-12 col-sm-12 col-md-4">
    @if($todasEntidades->isEmpty())
        {{-- Estado vazio --}}
        <div class="card card-flush h-xl-100">
            <div class="card-body d-flex align-items-center justify-content-center">
                <div class="text-center">
                    <i class="bi bi-inbox fs-3x text-gray-400 mb-3 d-block" aria-hidden="true"></i>
                    <p class="text-gray-600 mb-0">Nenhuma entidade financeira disponível.</p>
                </div>
            </div>
        </div>
    @else
        <div id="{{ $carouselId }}"
             class="carousel carousel-custom slide h-xl-100 border-start border-2 border-primary ps-6 ms-6 d-flex flex-column justify-content-center"
             data-bs-ride="carousel"
             data-bs-interval="9000"
             role="region"
             aria-label="Carrossel de entidades financeiras">

            <div class="position-relative">
                <div class="carousel-inner" role="listbox" aria-label="Lista de entidades financeiras">
                    @foreach ($todasEntidades as $key => $entidade)
                        @include('app.financeiro.banco.components.side-card-item', [
                            'entidade' => $entidade,
                            'isActive' => $key === 0,
                            'index' => $key,
                        ])
                    @endforeach
                </div>
            </div>

            {{-- Indicators --}}
            @if ($todasEntidades->count() > 1)
                <ol class="p-0 m-0 carousel-indicators carousel-indicators-bullet carousel-indicators-active-primary"
                    role="tablist"
                    aria-label="Indicadores de slide">
                    @foreach ($todasEntidades as $key => $entidade)
                        <li data-bs-target="#{{ $carouselId }}"
                            data-bs-slide-to="{{ $key }}"
                            class="bullet bullet-dot bg-gray-400 me-5 {{ $key === 0 ? 'active' : '' }}"
                            role="tab"
                            aria-label="Ir para slide {{ $key + 1 }}"
                            @if($key === 0) aria-selected="true" @endif>
                        </li>
                    @endforeach
                </ol>
            @endif
        </div>
    @endif
</div>

{{-- Modal para renomear entidade --}}
<div class="modal fade" id="modal_renomear_entidade" tabindex="-1" aria-labelledby="modal_renomear_label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered ">
        <div class="modal-content">
            <div class="modal-header py-4">
                <h5 class="modal-title fs-6" id="modal_renomear_label">
                   Renomear Entidade
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="renomear_entidade_id">
                <div class="mb-0">
                    <label for="renomear_entidade_nome" class="form-label fw-semibold fs-7">Nome</label>
                    <input type="text"
                           class="form-control form-control-sm"
                           id="renomear_entidade_nome"
                           maxlength="150"
                           placeholder="Digite o novo nome"
                           autocomplete="off">
                    <div class="invalid-feedback" id="renomear_nome_error"></div>
                </div>
            </div>
            <div class="modal-footer py-3">
                <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-sm btn-primary" id="btn_salvar_renomear">
                    <i class="bi bi-check2 me-1"></i>Salvar
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
@once
<script>
    // Inicializar tooltips quando o DOM estiver pronto
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializa tooltips do Bootstrap
        var tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        var tooltipList = Array.from(tooltipTriggerList).map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Reinicializa tooltips quando novos slides são carregados (para conteúdo dinâmico)
        var carouselElement = document.getElementById('{{ $carouselId }}');
        if (carouselElement) {
            carouselElement.addEventListener('slid.bs.carousel', function () {
                tooltipList.forEach(function(tooltip) { tooltip.dispose(); });
                tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
                tooltipList = Array.from(tooltipTriggerList).map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            });
        }

        // === Renomear Entidade ===
        const modal = document.getElementById('modal_renomear_entidade');
        const bsModal = new bootstrap.Modal(modal);
        const inputId = document.getElementById('renomear_entidade_id');
        const inputNome = document.getElementById('renomear_entidade_nome');
        const inputError = document.getElementById('renomear_nome_error');
        const btnSalvar = document.getElementById('btn_salvar_renomear');

        // Abrir modal ao clicar no lápis (capture phase para pegar antes do link)
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.btn-rename-entidade');
            if (!btn) return;
            e.preventDefault();
            e.stopImmediatePropagation();

            // Destrói tooltip antes de abrir o modal
            const tooltipInstance = bootstrap.Tooltip.getInstance(btn);
            if (tooltipInstance) tooltipInstance.hide();

            inputId.value = btn.dataset.entidadeId;
            inputNome.value = btn.dataset.entidadeNome;
            inputNome.classList.remove('is-invalid');
            inputError.textContent = '';
            bsModal.show();

            // Focus no input após animação do modal
            modal.addEventListener('shown.bs.modal', function handler() {
                inputNome.focus();
                inputNome.select();
                modal.removeEventListener('shown.bs.modal', handler);
            });
        }, true); // true = capture phase, intercepta ANTES do <a> receber o click

        // Salvar ao pressionar Enter
        inputNome.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                btnSalvar.click();
            }
        });

        // Salvar nome
        btnSalvar.addEventListener('click', async function() {
            const id = inputId.value;
            const nome = inputNome.value.trim();

            if (!nome) {
                inputNome.classList.add('is-invalid');
                inputError.textContent = 'O nome é obrigatório.';
                return;
            }

            btnSalvar.disabled = true;
            btnSalvar.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Salvando...';

            try {
                const response = await fetch(`{{ route('entidades.renomear', ['id' => '__ID__']) }}`.replace('__ID__', id), {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ nome }),
                });

                const data = await response.json();

                if (data.success) {
                    // Atualiza o nome no card (pode haver múltiplos spans com data-entidade-nome)
                    document.querySelectorAll(`[data-entidade-nome="${id}"]`).forEach(el => {
                        if (el.tagName === 'SPAN') el.textContent = data.nome;
                    });

                    // Atualiza o data attribute do botão lápis
                    document.querySelectorAll(`.btn-rename-entidade[data-entidade-id="${id}"]`).forEach(btn => {
                        btn.dataset.entidadeNome = data.nome;
                    });

                    bsModal.hide();

                    if (typeof toastr !== 'undefined') {
                        toastr.success('Nome atualizado com sucesso!');
                    }
                } else {
                    inputNome.classList.add('is-invalid');
                    inputError.textContent = data.message || 'Erro ao salvar.';
                }
            } catch (err) {
                inputNome.classList.add('is-invalid');
                inputError.textContent = 'Erro de conexão. Tente novamente.';
                if (typeof toastr !== 'undefined') {
                    toastr.error('Erro de conexão. Tente novamente.');
                }
            } finally {
                btnSalvar.disabled = false;
                btnSalvar.innerHTML = '<i class="bi bi-check2 me-1"></i>Salvar';
            }
        });
    });
</script>
@endonce
@endpush
