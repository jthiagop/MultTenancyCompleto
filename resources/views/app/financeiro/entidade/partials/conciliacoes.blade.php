{{--
REFACTORED: Conciliações Blade Template com Abas Filtradas via AJAX

✅ ARQUITETURA:
1. Filtragem server-side com amount_cents (sem erros de rounding)
2. AJAX para carregar tabs dinamicamente (sem reload de página)
3. Componente conciliacao-pane.blade.php renderização
4. Contadores dinâmicos baseados em tipo de transação

Benefícios:
- ✅ Sem reload de página ao trocar tabs
- ✅ Carrega apenas tab ativa (performance)
- ✅ amount_cents > 0 = Recebimento (entrada/credit)
- ✅ amount_cents < 0=Pagamento (saída/debit) 
- ✅ SEM truncamento ou rounding errors --}} 

@php

// ✅ Usa contadores server-side calculados no Controller
$tabs = [
    ['key' => 'all', 'label' => 'Todos', 'count' => $counts['all'] ?? 0],
    ['key' => 'received', 'label' => 'Recebimentos', 'count' => $counts['received'] ?? 0], 
    ['key' => 'paid', 'label' => 'Pagamentos', 'count' => $counts['paid'] ?? 0],
];
@endphp <div class="card mt-5">
    <x-tenant.segmented-tabs-toolbar :tabs="$tabs" :active="$tab ?? 'all'" id="conciliacao">
        <x-slot:actionsLeft>
            {{-- <button class="btn btn-sm btn-light-primary">Conciliar</button>
            <button class="btn btn-sm btn-light-primary">Editar</button>
            <button class="btn btn-sm btn-light-danger">Ignorar</button> --}}
        </x-slot>

        <x-slot:actionsRight>
            {{-- <a href=""
                class="btn btn-sm btn-light-success {{ ($activeTab ?? '') === 'historico' ? 'active' : '' }}">
                <i class="bi bi-clock-history me-1"></i>
                Histórico
            </a> --}}
        </x-slot>

        <x-slot:panes>
            <!-- ABA: TODOS -->
            <div class="tab-pane fade {{ ($tab ?? 'all') === 'all' ? 'show active' : '' }}" id="conciliacao-pane-all"
                role="tabpanel" aria-labelledby="conciliacao-tab-all" data-tab-key="all">
                <div class="text-center p-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                </div>
            </div>

            <!-- ABA: RECEBIMENTOS -->
            <div class="tab-pane fade {{ ($tab ?? 'all') === 'received' ? 'show active' : '' }}"
                id="conciliacao-pane-received" role="tabpanel" aria-labelledby="conciliacao-tab-received"
                data-tab-key="received">
                <div class="text-center p-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                </div>
            </div>

            <!-- ABA: PAGAMENTOS -->
            <div class="tab-pane fade {{ ($tab ?? 'all') === 'paid' ? 'show active' : '' }}" id="conciliacao-pane-paid"
                role="tabpanel" aria-labelledby="conciliacao-tab-paid" data-tab-key="paid">
                <div class="text-center p-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                </div>
            </div>
        </x-slot:panes>
    </x-tenant.segmented-tabs-toolbar>

</div>

{{-- Drawer de Transferência (renderizado síncrono, fora do AJAX) --}}
@if(isset($entidade))
    <x-conciliacao.drawer-transferencia
        :lps="$lps"
        :centrosAtivos="$centrosAtivos"
        :entidade="$entidade" />
@endif

@push('scripts')
    {{-- Carregar o handler de formulários UMA VEZ só --}}
    <script src="{{ url('/app/financeiro/entidade/conciliacoes-form-handler.js') }}"></script>

    {{-- TransferenciaDrawer: Gerencia o drawer de transferência entre contas --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const DRAWER_ID = 'conciliacao_transferencia_drawer';
            const FORM_ID = 'conciliacao_transferencia_form';
            const CLOSE_ID = DRAWER_ID + '_close';
            const SUBMIT_ID = DRAWER_ID + '_submit';
            const ENTIDADE_ORIGEM_ID = {{ $entidade->id }};

            const TransferenciaDrawer = {
                drawerInstance: null,
                currentConciliacaoId: null,
                contasCarregadas: [], // Cache das contas para seleção automática

                init() {
                    this.bindEvents();
                    this.loadContasDestino();
                },

                getDrawerInstance() {
                    const el = document.getElementById(DRAWER_ID);
                    if (!el) return null;

                    if (!this.drawerInstance) {
                        if (typeof KTDrawer !== 'undefined') {
                            this.drawerInstance = KTDrawer.getInstance(el);
                            if (!this.drawerInstance) {
                                KTDrawer.createInstances();
                                this.drawerInstance = KTDrawer.getInstance(el);
                            }
                            if (!this.drawerInstance) {
                                try { this.drawerInstance = new KTDrawer(el); } catch(e) {}
                            }
                        }
                    }
                    return this.drawerInstance;
                },

                bindEvents() {
                    // Botões de abrir (event delegation para funcionar com conteúdo AJAX)
                    document.addEventListener('click', (e) => {
                        const btn = e.target.closest('.btn-open-transferencia');
                        if (!btn) return;

                        this.currentConciliacaoId = btn.dataset.conciliacaoId;
                        this.fillFromButton(btn);
                        this.show();
                    });

                    // Cancelar
                    const cancelBtn = document.getElementById(CLOSE_ID + '_cancel');
                    if (cancelBtn) {
                        cancelBtn.addEventListener('click', () => this.close());
                    }

                    // Submeter
                    const submitBtn = document.getElementById(SUBMIT_ID);
                    if (submitBtn) {
                        submitBtn.addEventListener('click', () => this.submit());
                    }
                },

                fillFromButton(btn) {
                    const valor = parseFloat(btn.dataset.valor || 0);
                    const data = btn.dataset.data || '';
                    const memo = btn.dataset.memo || '';
                    const checknum = btn.dataset.checknum || '';

                    // Dados de movimentação interna (se detectada)
                    const movInternaDestino = btn.dataset.movInternaDestino || '';
                    const movInternaAccountType = btn.dataset.movInternaAccountType || '';
                    const movInternaBanco = btn.dataset.movInternaBanco || '';

                    document.getElementById('transf_bank_statement_id').value = this.currentConciliacaoId;
                    document.getElementById('transf_checknum').value = checknum;
                    document.getElementById('transf_valor').value = valor;
                    document.getElementById('transf_data').value = data;

                    const dataFormatada = data
                        ? new Date(data + 'T12:00:00').toLocaleDateString('pt-BR')
                        : '-';
                    document.getElementById('transf_info_data').textContent = dataFormatada;
                    document.getElementById('transf_info_memo').textContent = memo || '-';
                    document.getElementById('transf_info_valor').textContent =
                        'R$ ' + valor.toLocaleString('pt-BR', { minimumFractionDigits: 2 });

                    const descEl = document.getElementById('transf_descricao');
                    if (descEl) {
                        // Se for movimentação interna, usar descrição mais clara
                        if (movInternaDestino) {
                            descEl.value = movInternaDestino + ': ' + memo;
                        } else {
                            descEl.value = memo ? 'Transferência: ' + memo : '';
                        }
                    }

                    // Pré-selecionar conta destino se for movimentação interna
                    if (movInternaAccountType && this.contasCarregadas.length > 0) {
                        this.preSelectContaDestino(movInternaAccountType, movInternaBanco);
                    }
                },

                /**
                 * Pré-seleciona a conta destino baseada no tipo de conta e banco
                 */
                preSelectContaDestino(accountType, bancoCode) {
                    const select = document.getElementById('transf_entidade_destino_id');
                    if (!select || !this.contasCarregadas.length) return;

                    // Prioridade 1: Mesmo banco + mesmo account_type
                    let contaSugerida = null;
                    if (bancoCode && accountType) {
                        contaSugerida = this.contasCarregadas.find(c => 
                            c.banco_code === bancoCode && c.account_type === accountType
                        );
                    }

                    // Prioridade 2: Qualquer banco + mesmo account_type
                    if (!contaSugerida && accountType) {
                        contaSugerida = this.contasCarregadas.find(c => c.account_type === accountType);
                    }

                    // Prioridade 3: Mesmo banco + tipos de aplicação
                    if (!contaSugerida && bancoCode) {
                        contaSugerida = this.contasCarregadas.find(c => 
                            c.banco_code === bancoCode && 
                            ['aplicacao', 'poupanca', 'renda_fixa'].includes(c.account_type)
                        );
                    }

                    if (contaSugerida) {
                        select.value = contaSugerida.id;
                        // Atualizar Select2 se disponível
                        if (typeof $ !== 'undefined' && $(select).data('select2')) {
                            $(select).val(contaSugerida.id).trigger('change');
                        }
                    }
                },

                async loadContasDestino() {
                    try {
                        const response = await fetch(
                            '{{ route("conciliacao.contas-disponiveis") }}?entidade_origem_id=' + ENTIDADE_ORIGEM_ID,
                            {
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                                    'Accept': 'application/json'
                                }
                            }
                        );
                        const result = await response.json();

                        if (result.success && result.contas) {
                            // Armazenar no cache para pré-seleção automática
                            this.contasCarregadas = result.contas;

                            const select = document.getElementById('transf_entidade_destino_id');
                            if (!select) return;

                            const firstOption = select.querySelector('option:first-child');
                            select.innerHTML = '';
                            if (firstOption) select.appendChild(firstOption);

                            result.contas.forEach(conta => {
                                const opt = document.createElement('option');
                                opt.value = conta.id;
                                opt.textContent = conta.nome + (conta.account_type_label ? ' (' + conta.account_type_label + ')' : '');
                                // Armazenar dados extras para seleção automática
                                opt.dataset.accountType = conta.account_type || '';
                                opt.dataset.bancoCode = conta.banco_code || '';
                                select.appendChild(opt);
                            });

                            if (typeof $ !== 'undefined' && $(select).data('select2')) {
                                $(select).trigger('change');
                            }
                        }
                    } catch (error) {
                        console.error('Erro ao carregar contas de destino:', error);
                    }
                },

                async submit() {
                    const form = document.getElementById(FORM_ID);
                    if (!form) return;

                    const destino = document.getElementById('transf_entidade_destino_id');

                    if (!destino?.value) {
                        Swal.fire('Atenção', 'Selecione a conta de destino.', 'warning');
                        return;
                    }

                    const submitBtn = document.getElementById(SUBMIT_ID);
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Processando...';
                    }

                    try {
                        const formData = new FormData(form);
                        const response = await fetch(form.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                                'Accept': 'application/json'
                            }
                        });

                        const result = await response.json();

                        if (result.success || response.ok) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Transferência realizada!',
                                text: result.message || 'A transferência foi registrada com sucesso.',
                                timer: 2500,
                                showConfirmButton: false
                            });
                            this.close();
                            this.resetForm();

                            const row = document.querySelector('[data-conciliacao-id="' + this.currentConciliacaoId + '"]');
                            if (row) {
                                row.style.transition = 'opacity 0.4s ease';
                                row.style.opacity = '0';
                                setTimeout(() => row.remove(), 400);
                            }
                        } else {
                            throw new Error(result.message || 'Erro ao processar transferência');
                        }
                    } catch (error) {
                        Swal.fire('Erro', error.message || 'Erro ao processar a transferência.', 'error');
                    } finally {
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = '<i class="fa-solid fa-arrow-right-arrow-left me-1"></i> Transferir';
                        }
                    }
                },

                show() {
                    const instance = this.getDrawerInstance();
                    if (instance) {
                        instance.show();
                    } else {
                        const el = document.getElementById(DRAWER_ID);
                        if (el) {
                            el.classList.add('drawer-on');
                            document.body.classList.add('drawer-on');
                            let overlay = document.querySelector('.drawer-overlay');
                            if (!overlay) {
                                overlay = document.createElement('div');
                                overlay.className = 'drawer-overlay';
                                overlay.style.cssText = 'position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.4);z-index:109;';
                                overlay.addEventListener('click', () => this.close());
                                document.body.appendChild(overlay);
                            }
                        }
                    }

                    // Inicializar Select2 dentro do drawer (após abrir)
                    this.initSelect2();
                },

                /**
                 * Inicializa o Select2 da conta destino dentro do drawer
                 */
                initSelect2() {
                    const select = document.getElementById('transf_entidade_destino_id');
                    if (!select) return;

                    // Inicializar Select2 se não estiver inicializado
                    if (typeof $ !== 'undefined' && typeof $.fn.select2 !== 'undefined') {
                        if (!$(select).data('select2')) {
                            $(select).select2({
                                dropdownParent: $('#' + DRAWER_ID),
                                placeholder: 'Selecione a conta de destino',
                                allowClear: true,
                                width: '100%'
                            });
                        }
                    }
                },

                close() {
                    const instance = this.getDrawerInstance();
                    if (instance) {
                        instance.hide();
                    } else {
                        const el = document.getElementById(DRAWER_ID);
                        if (el) el.classList.remove('drawer-on');
                        document.body.classList.remove('drawer-on');
                        const overlay = document.querySelector('.drawer-overlay');
                        if (overlay) overlay.remove();
                    }
                },

                resetForm() {
                    const form = document.getElementById(FORM_ID);
                    if (form) form.reset();

                    // Limpar Select2 da conta destino
                    const selectDestino = document.getElementById('transf_entidade_destino_id');
                    if (selectDestino && typeof $ !== 'undefined' && $(selectDestino).data('select2')) {
                        $(selectDestino).val('').trigger('change');
                    }

                    const infoData = document.getElementById('transf_info_data');
                    const infoMemo = document.getElementById('transf_info_memo');
                    const infoValor = document.getElementById('transf_info_valor');
                    if (infoData) infoData.textContent = '-';
                    if (infoMemo) infoMemo.textContent = '-';
                    if (infoValor) infoValor.textContent = 'R$ 0,00';
                }
            };

            TransferenciaDrawer.init();
            window.TransferenciaDrawer = TransferenciaDrawer;
        });
    </script>

    <script>
        /**
         * ✅ AJAX Tab Loading - TODAS as tabs via AJAX
         * Carrega tabs dinamicamente sem reload de página
         * Incluindo a tab inicial
         */
        document.addEventListener('DOMContentLoaded', function() {
            const entidadeId = {{ $entidade->id }};
            const tabButtons = document.querySelectorAll('[data-bs-toggle="tab"]');
            const loadedTabs = new Set();
            const activeTab = '{{ $tab ?? 'all' }}';

            /**
             * Inicializa o botão "Carregar Mais"
             */
            function initLoadMoreButton(tabKey, targetPane) {
                const loadMoreBtn = targetPane.querySelector('.btn-load-more');
                if (!loadMoreBtn) {
                    return;
                }

                console.log('🔘 Botão "Carregar Mais" encontrado para tab:', tabKey);

                // Remove listeners antigos (clone e replace)
                const newBtn = loadMoreBtn.cloneNode(true);
                loadMoreBtn.parentNode.replaceChild(newBtn, loadMoreBtn);

                newBtn.addEventListener('click', function() {
                    const nextPage = parseInt(this.dataset.nextPage);
                    console.log('📄 Carregando página:', nextPage);

                    // Mostrar loading no botão
                    const originalHtml = this.innerHTML;
                    this.disabled = true;
                    this.innerHTML =
                        '<span class="spinner-border spinner-border-sm me-2"></span>Carregando...';

                    // Fazer requisição para próxima página
                    const url =
                        `{{ route('entidades.conciliacoes-tab', ['id' => $entidade->id]) }}?tab=${tabKey}&page=${nextPage}`;

                    fetch(url, {
                            method: 'GET',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Processar HTML
                                const tempDiv = document.createElement('div');
                                tempDiv.innerHTML = data.html;

                                // Remover scripts e headers
                                tempDiv.querySelectorAll('script').forEach(s => s.remove());
                                const header = tempDiv.querySelector(
                                '.row.gx-5.gx-xl-10'); // Header duplicado
                                if (header && !header.hasAttribute('data-conciliacao-id')) {
                                    header.remove();
                                }

                                // Remover container do botão atual
                                const loadMoreContainer = targetPane.querySelector(
                                    '#load-more-container');
                                if (loadMoreContainer) loadMoreContainer.remove();

                                // Encontrar container e adicionar itens
                                const cardBody = targetPane.querySelector('.card-body') || targetPane;
                                const newItems = tempDiv.querySelectorAll(
                                    '.row.gx-5.gx-xl-10[data-conciliacao-id]');

                                console.log('➕ Adicionando', newItems.length, 'novos itens');
                                newItems.forEach(item => cardBody.appendChild(item));

                                // Reinicializa Select2 nos novos elementos
                                if (typeof $ !== 'undefined' && typeof $.fn.select2 !== 'undefined') {
                                    newItems.forEach(item => {
                                        const selects = item.querySelectorAll('select[data-control="select2"]');
                                        selects.forEach(select => {
                                            if (!$(select).data('select2')) {
                                                $(select).select2({
                                                    placeholder: $(select).attr('placeholder') || 'Selecione...',
                                                    allowClear: true,
                                                    width: '100%'
                                                });
                                            }
                                        });
                                    });
                                    console.log('✅ Select2 reinicializado nos novos itens');
                                }

                                // Reinicializa o suggestionStarManager para os novos elementos
                                if (typeof window.suggestionStarManager !== 'undefined') {
                                    window.suggestionStarManager.reinitialize();
                                    console.log('✅ Suggestion stars reinicializadas nos novos itens');
                                }

                                // Adicionar novo botão se houver
                                const newLoadMore = tempDiv.querySelector('#load-more-container');
                                if (newLoadMore) {
                                    cardBody.appendChild(newLoadMore);
                                    initLoadMoreButton(tabKey, targetPane);
                                }
                            }
                        })
                        .catch(error => {
                            console.error('❌ Erro ao carregar mais:', error);
                            this.disabled = false;
                            this.innerHTML = originalHtml;
                        });
                });
            }

            /**
             * Função para carregar uma tab via AJAX
             */
            function loadTab(tabKey) {
                // Pega o pane correto baseado no ID padrão
                const targetPane = document.getElementById(`conciliacao-pane-${tabKey}`);

                if (!targetPane) {
                    return;
                }

                // Se já carregou, ignora
                if (targetPane.getAttribute('data-loaded') === 'true') {
                    return;
                }

                // Marca visualmente como carregando se estiver vazio
                if (!targetPane.innerHTML.trim() || targetPane.querySelector('.spinner-border')) {
                    targetPane.innerHTML = `
                            <div class="text-center p-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Carregando...</span>
                                </div>
                            </div>
                        `;
                }

                // Fazer AJAX request
                fetch(`{{ route('entidades.conciliacoes-tab', ['id' => $entidade->id]) }}?tab=${tabKey}`, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Erro na requisição');
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Marca como carregado para evitar reload
                            targetPane.setAttribute('data-loaded', 'true');

                            // Processar HTML
                            const tempDiv = document.createElement('div');
                            tempDiv.innerHTML = data.html;
                            tempDiv.querySelectorAll('script').forEach(s => s.remove());

                            targetPane.innerHTML = '';
                            targetPane.insertAdjacentHTML('beforeend', tempDiv.innerHTML);

                            // ✅ Inicializar Select2 nos selects que foram injetados
                            if (typeof window.initializeSelect2 !== 'undefined') {
                                window.initializeSelect2(targetPane);
                            }

                            // ✅ Reinicializar gerenciador de estrelas de sugestão
                            if (typeof window.suggestionStarManager !== 'undefined') {
                                console.log('🌟 Reinicializando estrelas de sugestão após AJAX...');
                                window.suggestionStarManager.reinitialize();
                            }

                            initLoadMoreButton(tabKey, targetPane);
                        } else {
                            targetPane.innerHTML = `<div class="alert alert-danger m-5">${data.message}</div>`;
                        }
                    })
                    .catch(error => {
                        console.error('Erro:', error);
                        targetPane.innerHTML =
                            `<div class="alert alert-danger m-5">Erro ao carregar dados</div>`;
                    });
            }

            // 1. Carregar tab inicial
            loadTab(activeTab);

            // 2. Listener para cliques nas tabs
            tabButtons.forEach(btn => {
                btn.addEventListener('shown.bs.tab', function(e) {
                    // Extrai a chave da tab do ID do target (#conciliacao-pane-XXX -> XXX)
                    const targetId = this.getAttribute('data-bs-target');
                    const tabKey = targetId.replace('#conciliacao-pane-', '');

                    console.log('🖱️ Mudança de tab:', tabKey);
                    loadTab(tabKey);
                });
            });

            // 3. Handler para conciliação via AJAX (sem reload de página)
            document.addEventListener('submit', function(e) {
                const form = e.target;
                
                console.log('🔍 [Debug] Form submitted:', form.className);
                
                // Verifica se é um form de conciliação (novo lançamento OU editar sugestão)
                if (!form.classList.contains('conciliacao-form') && !form.classList.contains('edit-suggestion-form')) {
                    return;
                }

                e.preventDefault();

                const formData = new FormData(form);
                const conciliacaoId = form.getAttribute('data-conciliacao-id');
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalBtnText = submitBtn ? submitBtn.innerHTML : '';

                // Loading state
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Conciliando...';
                }

                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {

                    if (data.success) {
                        // 1. Remove o item visualmente com animação
                        // Para novo lançamento: remove o card mais externo
                        // Para editar sugestão: remove a row inteira
                        let elementToRemove;
                        
                        if (form.classList.contains('conciliacao-form')) {
                            // Novo lançamento: sobe até encontrar .row com data-conciliacao-id
                            elementToRemove = form.closest('.row[data-conciliacao-id]');
                            console.log('🗑️ [Debug] Removendo novo lançamento');
                        } else if (form.classList.contains('edit-suggestion-form')) {
                            // Editar sugestão: remove a row inteira da conciliação
                            elementToRemove = form.closest('.row[data-conciliacao-id]');
                            console.log('🗑️ [Debug] Removendo sugestão editada');
                        }
                        
                        if (elementToRemove) {
                            elementToRemove.style.transition = 'opacity 0.3s, transform 0.3s';
                            elementToRemove.style.opacity = '0';
                            elementToRemove.style.transform = 'scale(0.95)';
                            
                            setTimeout(() => {
                                elementToRemove.remove();

                                // Reinicializa estrelas após remoção
                                if (typeof window.suggestionStarManager !== 'undefined') {
                                    window.suggestionStarManager.reinitialize();
                                }
                            }, 300);
                        }

                        // 2. Atualiza contadores usando funções globais de tabs.blade.php
                        // Recarrega saldos e informações financeiras
                        if (typeof window.carregarInformacoes === 'function') {
                            window.carregarInformacoes();
                        }

                        // Recarrega total de pendentes (independente do filtro de data)
                        if (typeof window.carregarTotalPendentes === 'function') {
                            window.carregarTotalPendentes();
                        }

                        // Atualiza badges das tabs internas (all, received, paid) com animação
                        if (data.data && data.data.counts) {
                            ['all', 'received', 'paid'].forEach(tabKey => {
                                const tabButton = document.querySelector(`#conciliacao-tab-${tabKey}`);
                                if (!tabButton) return;
                                
                                const tabCount = tabButton.querySelector('.segmented-tab-count');
                                if (tabCount && data.data.counts[tabKey] !== undefined) {
                                    const count = data.data.counts[tabKey];
                                    
                                    // Anima a redução do contador
                                    tabCount.style.transition = 'all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1)';
                                    tabCount.style.transform = 'scale(1.15) rotate(5deg)';
                                    
                                    setTimeout(() => {
                                        tabCount.textContent = count;
                                        tabCount.style.transform = 'scale(1) rotate(0deg)';
                                    }, 150);
                                    
                                    console.log(`📋 Tab "${tabKey}" atualizada: ${count} pendentes`);
                                }
                            });
                        }

                        // 4. Toast de sucesso
                        if (typeof showSuccessToast === 'function') {
                            showSuccessToast(data.message || 'Lançamento conciliado com sucesso!');
                        } else {
                            alert(data.message || 'Lançamento conciliado com sucesso!');
                        }
                    } else {
                        throw new Error(data.message || 'Erro ao conciliar');
                    }
                })
                .catch(error => {
                    console.error('❌ [Conciliação AJAX] Erro:', error);
                    
                    // Restaura botão
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnText;
                    }

                    // Toast de erro
                    if (typeof showErrorToast === 'function') {
                        showErrorToast(error.message || 'Erro ao conciliar lançamento');
                    } else {
                        alert('Erro: ' + (error.message || 'Erro ao conciliar lançamento'));
                    }
                });
            }, true); // useCapture para pegar o evento antes dos handlers específicos
        });
    </script>
@endpush

{{-- Include Modal de Filtro de Conciliações --}}
@include('app.components.modals.financeiro.conciliacao.modal_conciliacao_bancaria')
