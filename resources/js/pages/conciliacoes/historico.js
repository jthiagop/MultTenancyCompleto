/**
 * Módulo de Histórico de Conciliações
 * Padrão "Laravel Way" com paginação no servidor + debounce + event delegation
 */


const el = document.getElementById('conciliacoes-historico');
if (!el) {
} else {
    
    // Configs do container via data-*
    const entidadeId = JSON.parse(el.dataset.entidadeId || 'null');
    const urlHistorico = el.dataset.urlHistorico;
    const urlDetalhesTpl = el.dataset.urlDetalhes;
    const urlDesfazerTpl = el.dataset.urlDesfazer;

    // Elementos DOM
    let tbody = document.getElementById('historico-conciliacoes-body');
    const buscaInput = document.getElementById('busca-historico');
    const perPageSelect = document.getElementById('items-per-page');
    const paginationContainer = document.getElementById('historico-pagination');
    
    if (!tbody) {
        console.error('❌ ERRO CRÍTICO: tbody não encontrado!');
        console.error('   ID procurado: historico-conciliacoes-body');
    }

    // Formatadores
    const money = new Intl.NumberFormat('pt-BR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });

    // Mapa de status
    const STATUS = {
        ok: { text: 'Conciliado', klass: 'badge-light-success' },
        ignorado: { text: 'Ignorado', klass: 'badge-light-warning' },
        pendente: { text: 'Pendente', klass: 'badge-light-primary' },
        divergente: { text: 'Divergente', klass: 'badge-light-danger' },
    };

    // Escape HTML (prevenção XSS)
    const escapeHtml = (s) =>
        String(s ?? '').replace(/[&<>"']/g, (m) => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        }[m]));

    // Estado da paginação/busca
    let state = { page: 1, per_page: 10, q: '' };
    let aborter = null;

    /**
     * Debounce helper
     */
    const debounce = (fn, delay = 350) => {
        let t;
        return (...args) => {
            clearTimeout(t);
            t = setTimeout(() => fn(...args), delay);
        };
    };

    /**
     * Renderiza badge de status
     */
    function statusBadge(status) {
        const info = STATUS[status] || { text: status || '-', klass: 'badge-light-primary' };
        return `<span class="badge ${info.klass}">${escapeHtml(info.text)}</span>`;
    }

    /**
     * Trunca string com limite de caracteres
     */
    const truncate = (str, max = 25) => {
        if (!str) return '-';
        const s = String(str);
        return s.length > max ? s.substring(0, max - 3) + '...' : s;
    };

    /**
     * Renderiza linhas da tabela
     */
    function renderRows(items) {
        
        if (!items?.length) {
            console.warn('⚠️ Array vazio ou undefined, mostrando mensagem de vazio');
            console.warn('   items:', items);
            console.warn('   typeof items:', typeof items);
            console.warn('   Array.isArray(items):', Array.isArray(items));
            
            tbody.innerHTML = `
                <tr><td colspan="7" class="text-center py-10">
                    <div class="d-flex flex-column align-items-center">
                        <i class="ki-duotone ki-document fs-3x text-muted mb-3">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <span class="text-muted fs-6">Nenhuma conciliação realizada ainda</span>
                    </div>
                </td></tr>`;
            return;
        }

        
        tbody.innerHTML = items.map((item, idx) => {
            
            const tipoIsEntrada = item.tipo === 'entrada';
            const tipoClasse = tipoIsEntrada ? 'badge-light-success' : 'badge-light-danger';
            const valorClasse = tipoIsEntrada ? 'text-success' : 'text-danger';
            const detalhesId = Number(item.id);
            const lancamentoPadrao = item.lancamento_padrao && item.lancamento_padrao !== '-' 
                ? `<div class="text-muted fs-6">
                     <i class="bi bi-tag fs-6 me-1">
                     </i>
                     ${escapeHtml(item.lancamento_padrao)}
                   </div>` 
                : '';

            return `
                <tr data-id="${detalhesId}" style="cursor:pointer;">
                    <td>${escapeHtml(item.data_conciliacao_formatada || '-')}</td>
                    <td class="descricao-conciliacao">
                        <a href="#" class="text-gray-800 fw-bold text-hover-primary d-block" 
                           data-action="ver-detalhes" data-id="${detalhesId}">
                            ${escapeHtml(item.descricao || '-')}
                        </a>
                        ${lancamentoPadrao}
                    </td>
                    <td><span class="badge ${tipoClasse}">${tipoIsEntrada ? 'Entrada' : 'Saída'}</span></td>
                    <td class="text-end"><span class="fw-bold ${valorClasse}">R$ ${money.format(item.valor || 0)}</span></td>
                    <td>${statusBadge(item.status)}</td>
                    <td><span class="text-muted fs-7" title="${escapeHtml(item.usuario || '-')}">${escapeHtml(truncate(item.usuario, 25))}</span></td>
                    <td class="text-end">
                        <a href="#" class="btn btn-sm btn-light btn-active-light-primary" 
                           data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                            Ações <i class="ki-duotone ki-down fs-5 ms-1"></i>
                        </a>
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 
                                    menu-state-bg-light-primary fw-semibold fs-7 w-150px py-4" 
                             data-kt-menu="true">
                            <div class="menu-item px-3">
                                <a href="#" class="menu-link px-3" data-action="ver-detalhes" data-id="${detalhesId}">
                                    <i class="ki-duotone ki-eye fs-4 me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    Ver Detalhes
                                </a>
                            </div>
                            <div class="menu-item px-3">
                                <a href="#" class="menu-link px-3" data-action="desfazer" data-id="${detalhesId}">
                                    <i class="ki-duotone ki-arrow-circle-left fs-4 me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Desfazer
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>`;
        }).join('');

        // Reinicializa menus dropdown do Metronic
        if (typeof KTMenu !== 'undefined') {
            KTMenu.createInstances();
        }
    }

    /**
     * Renderiza paginação
     */
    function renderPagination(meta) {
        const totalPages = meta?.last_page || 1;
        const current = meta?.current_page || 1;

        if (totalPages <= 1) {
            paginationContainer.innerHTML = '';
            return;
        }

        const mk = (p, label, disabled = false, active = false) => `
            <li class="page-item ${disabled ? 'disabled' : ''} ${active ? 'active' : ''}">
                <a class="page-link" href="#" data-page="${p}">${label}</a>
            </li>`;

        let html = '';
        html += mk(current - 1, '<i class="previous"></i>', current === 1);

        // Janela de páginas (max 5 visíveis)
        const max = 5;
        let start = Math.max(1, current - Math.floor(max / 2));
        let end = Math.min(totalPages, start + max - 1);
        start = Math.max(1, end - max + 1);

        for (let p = start; p <= end; p++) {
            html += mk(p, p, false, p === current);
        }

        html += mk(current + 1, '<i class="next"></i>', current === totalPages);

        paginationContainer.innerHTML = html;
    }

    /**
     * Carrega dados do servidor
     */
    async function load() {
        if (!entidadeId || !urlHistorico) {
            console.warn('❌ entidadeId ou urlHistorico não definidos');
            console.warn('   entidadeId:', entidadeId);
            console.warn('   urlHistorico:', urlHistorico);
            return;
        }

        // Detecta qual tab está ativa para passar o status correto
        let activePane = document.querySelector('[data-status].show.active');
        if (!activePane) activePane = document.querySelector('[data-status].show');
        if (!activePane) activePane = document.querySelector('[data-status="all"]');
        
        const activeStatus = activePane ? activePane.getAttribute('data-status') : 'all';


        // Cancela requisição anterior se houver
        if (aborter) aborter.abort();
        aborter = new AbortController();

        // Loading state
        tbody.innerHTML = `
                <tr><td colspan="7" class="text-center py-10">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                    <div class="text-muted mt-3">Carregando histórico...</div>
                </td></tr>`;

        try {
            const qs = new URLSearchParams({
                page: String(state.page),
                per_page: String(state.per_page),
                q: state.q || '',
                status: activeStatus, // ✅ NOVO: Passa o status da aba ativa
            });

            const fullUrl = `${urlHistorico}?${qs.toString()}`;

            const res = await fetch(fullUrl, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                signal: aborter.signal,
            });

            if (!res.ok) {
                const errText = await res.text();
                throw new Error(`Falha ao carregar histórico (${res.status})`);
            }

            const json = await res.json();
            
            // Novo padrão: controller retorna HTML renderizado
            if (json.html) {

                // Renderiza HTML diretamente
                tbody.innerHTML = json.html;
                renderPagination(json?.meta);
                
                // Reinicializa menus
                if (typeof KTMenu !== 'undefined') {
                    KTMenu.createInstances();
                }
                
                return;
            }
            
            // Padrão antigo: controller retorna array de dados
            const items = json?.data ?? [];
            
            if (!items || items.length === 0) {
                console.warn('⚠️ Nenhum item retornado do servidor');
                console.warn('   Success:', json?.success);
                console.warn('   Data:', json?.data);
            }
            
            renderRows(items);
            renderPagination(json?.meta);

        } catch (error) {
            if (error.name === 'AbortError') {
                return;
            }
            
            console.error('❌ Erro ao carregar histórico:', error);
            console.error('   Message:', error.message);
            console.error('   Stack:', error.stack);
            
            tbody.innerHTML = `
                <tr><td colspan="7" class="text-center py-10">
                    <div class="d-flex flex-column align-items-center">
                        <i class="ki-duotone ki-cross-circle fs-3x text-danger mb-3">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <span class="text-danger">Erro ao carregar histórico de conciliações</span>
                        <small class="text-muted mt-2">${escapeHtml(error.message)}</small>
                    </div>
                </td></tr>`;
        }
    }

    /**
     * Event Listeners
     */

    // Busca com debounce
    const debouncedSearch = debounce(() => {
        state.page = 1;
        state.q = buscaInput.value.trim();
        load().catch(console.error);
    });

    buscaInput?.addEventListener('input', debouncedSearch);

    // Mudança de itens por página
    perPageSelect?.addEventListener('change', () => {
        state.per_page = Number(perPageSelect.value || 10);
        state.page = 1;
        load().catch(console.error);
    });

    // Event delegation: paginação
    paginationContainer?.addEventListener('click', (e) => {
        const a = e.target.closest('.page-link');
        if (!a) return;

        e.preventDefault();
        const p = Number(a.dataset.page);
        if (!p || p < 1) return;

        state.page = p;
        load().catch(console.error);
    });

    // Event delegation: ações da tabela
    tbody?.addEventListener('click', (e) => {
        const a = e.target.closest('[data-action]');
        if (!a) return;

        e.preventDefault();
        const action = a.dataset.action;
        const id = Number(a.dataset.id);

        if (action === 'ver-detalhes') {
            abrirDrawerConciliacao(id);
        }

        if (action === 'desfazer') {
            desfazerConciliacao(id);
        }
    });

    /**
     * Abre o drawer com detalhes da conciliação
     */
    async function abrirDrawerConciliacao(bankStatementId) {
        if (!bankStatementId || bankStatementId === 0) {
            Swal.fire({
                text: "Conciliação não encontrada.",
                icon: "warning",
                buttonsStyling: false,
                confirmButtonText: "Ok",
                customClass: {
                    confirmButton: "btn btn-primary"
                }
            });
            return;
        }

        try {
            const url = urlDetalhesTpl.replace(':id', bankStatementId);
            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) throw new Error('Erro ao carregar detalhes');

            const data = await response.json();

            // Preencher dados no drawer
            document.getElementById('drawer_conciliacao_id_hidden').value = data.id || 0;
            document.getElementById('drawer_conciliacao_id').textContent = `#${data.id}`;
            document.getElementById('drawer_conciliacao_descricao').textContent = data.descricao || 'Sem descrição';

            // Status badge
            const statusBadgeEl = document.getElementById('drawer_conciliacao_status_badge');
            const statusMap = {
                'ok': { text: 'Conciliado', class: 'badge-success' },
                'ignorado': { text: 'Ignorado', class: 'badge-warning' },
                'pendente': { text: 'Pendente', class: 'badge-primary' },
                'divergente': { text: 'Divergente', class: 'badge-danger' }
            };
            const statusInfo = statusMap[data.status_conciliacao] || { text: data.status_conciliacao || '-', class: 'badge-primary' };
            statusBadgeEl.innerHTML = `<span class="badge ${statusInfo.class}">${statusInfo.text}</span>`;

            // Tipo e badge
            const tipoBadge = document.getElementById('drawer_conciliacao_tipo_badge');
            if (data.tipo === 'entrada') {
                tipoBadge.textContent = 'ENTRADA';
                tipoBadge.className = 'badge badge-light-success fs-7 fw-bold';
            } else {
                tipoBadge.textContent = 'SAÍDA';
                tipoBadge.className = 'badge badge-light-danger fs-7 fw-bold';
            }

            // Valor
            document.getElementById('drawer_conciliacao_valor').textContent = `R$ ${money.format(data.valor || 0)}`;

            // Datas
            document.getElementById('drawer_conciliacao_data_extrato').textContent = data.data_extrato_formatada || '-';
            document.getElementById('drawer_conciliacao_data_conciliacao').textContent = data.data_conciliacao_formatada || '-';

            // Lançamento Padrão
            document.getElementById('drawer_conciliacao_lancamento').textContent = data.lancamento_padrao || '-';

            // Arquivo OFX
            document.getElementById('drawer_conciliacao_arquivo_ofx').textContent = data.arquivo_ofx || '-';
            document.getElementById('drawer_conciliacao_data_importacao').textContent = data.data_importacao_ofx_formatada || '-';
            document.getElementById('drawer_conciliacao_memo').textContent = data.memo || data.descricao || '-';

            // Transação Vinculada
            document.getElementById('drawer_conciliacao_transacao_id').textContent = data.transacao_id ? `#${data.transacao_id}` : '-';
            document.getElementById('drawer_conciliacao_entidade').textContent = data.entidade_financeira || '-';
            document.getElementById('drawer_conciliacao_centro_custo').textContent = data.centro_custo || '-';

            // Histórico
            const historicoEl = document.getElementById('drawer_conciliacao_historico');
            if (data.historico_complementar) {
                historicoEl.innerHTML = `<p class="mb-0">${escapeHtml(data.historico_complementar)}</p>`;
            } else {
                historicoEl.innerHTML = '<span class="text-muted">Nenhum histórico complementar</span>';
            }

            // Anexos
            document.getElementById('drawer_conciliacao_anexos').innerHTML = '<span class="text-muted">Nenhum anexo</span>';

            // Auditoria
            document.getElementById('drawer_conciliacao_criado_por').textContent = data.created_by_name || '-';
            document.getElementById('drawer_conciliacao_criado_em').textContent = data.created_at_formatado || '-';
            document.getElementById('drawer_conciliacao_atualizado_por').textContent = data.updated_by_name || '-';
            document.getElementById('drawer_conciliacao_atualizado_em').textContent = data.updated_at_formatado || '-';

            // Abrir o drawer
            const drawer = KTDrawer.getInstance(document.getElementById('kt_drawer_conciliacao_detalhes'));
            if (drawer) {
                drawer.show();
            }

        } catch (error) {
            console.error('Erro ao carregar detalhes da conciliação:', error);
            Swal.fire({
                text: "Erro ao carregar os detalhes da conciliação.",
                icon: "error",
                buttonsStyling: false,
                confirmButtonText: "Ok",
                customClass: {
                    confirmButton: "btn btn-primary"
                }
            });
        }
    }

    /**
     * Desfaz uma conciliação
     */
    async function desfazerConciliacao(bankStatementId) {
        if (!bankStatementId || bankStatementId === 0) {
            Swal.fire({
                text: "Conciliação não encontrada.",
                icon: "warning",
                buttonsStyling: false,
                confirmButtonText: "Ok",
                customClass: {
                    confirmButton: "btn btn-primary"
                }
            });
            return;
        }

        const result = await Swal.fire({
            title: "Tem certeza que deseja desfazer esta conciliação?",
            html: '<p class="text-danger fw-bold mt-3">Esta ação não pode ser desfeita!</p>',
            icon: "warning",
            showCancelButton: true,
            buttonsStyling: false,
            confirmButtonText: "Sim, desfazer",
            cancelButtonText: "Cancelar",
            customClass: {
                confirmButton: "btn btn-danger",
                cancelButton: "btn btn-secondary"
            }
        });

        if (!result.isConfirmed) return;

        // Mostrar loading
        Swal.fire({
            title: 'Processando...',
            text: 'Desfazendo conciliação',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        try {
            const url = urlDesfazerTpl.replace(':id', bankStatementId);
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) throw new Error('Erro ao desfazer conciliação');

            const result = await response.json();

            if (result.success) {
                await Swal.fire({
                    title: 'Sucesso!',
                    text: result.message || 'Conciliação desfeita com sucesso!',
                    icon: 'success',
                    buttonsStyling: false,
                    confirmButtonText: 'Ok',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    }
                });

                // Fechar drawer se estiver aberto
                const drawer = KTDrawer.getInstance(document.getElementById('kt_drawer_conciliacao_detalhes'));
                if (drawer) {
                    drawer.hide();
                }

                // Recarregar lista
                load().catch(console.error);

                // ✅ Atualizar contadores das abas após desfazer
                if (typeof window.atualizarContagemStatusTabs === 'function') {
                    // Se o controller retornou contadores, usar eles
                    if (result.counts) {
                        window.atualizarContagemStatusTabs(result.counts);
                    } else {
                        // Caso contrário, buscar contadores atualizados
                        buscarContagemAtualizada();
                    }
                }

                // Atualizar contador de pendentes se existir
                if (typeof window.atualizarTotalPendentes === 'function') {
                    window.atualizarTotalPendentes();
                }
            } else {
                Swal.fire({
                    title: 'Erro!',
                    text: result.message || 'Erro ao desfazer conciliação.',
                    icon: 'error',
                    buttonsStyling: false,
                    confirmButtonText: 'Ok',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    }
                });
            }
        } catch (error) {
            console.error('Erro ao desfazer conciliação:', error);
            Swal.fire({
                title: 'Erro!',
                text: 'Erro ao desfazer conciliação. Tente novamente.',
                icon: 'error',
                buttonsStyling: false,
                confirmButtonText: 'Ok',
                customClass: {
                    confirmButton: 'btn btn-primary'
                }
            });
        }
    }

    /**
     * Busca contadores atualizados do servidor
     */
    async function buscarContagemAtualizada() {
        try {
            const url = new URL(urlHistorico, window.location.origin);
            url.searchParams.append('only_counts', 'true');
            
            const response = await fetch(url.toString(), {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                return;
            }

            const data = await response.json();
            if (data.counts && typeof window.atualizarContagemStatusTabs === 'function') {
                window.atualizarContagemStatusTabs(data.counts);
            }
        } catch (error) {
        }
    }

    // Carrega dados inicialmente
    load().catch(console.error);

    // Expõe funções globalmente para compatibilidade
    window.recarregarHistoricoConciliacoes = function() {
        load().catch(console.error);
    };
    
    window.abrirDrawerConciliacao = abrirDrawerConciliacao;
    window.desfazerConciliacao = desfazerConciliacao;

    // --- REFORMA PARA TABS MÚLTIPLAS ---

    // 1. Função global para atualizar contadores das tabs
    if (typeof window.atualizarContagemStatusTabs !== 'function') {
        window.atualizarContagemStatusTabs = function(newCounts) {
            if (!newCounts) return;

            const allCount = (newCounts.ok || 0) + (newCounts.pendente || 0) + (newCounts.ignorado || 0) + (newCounts.divergente || 0);
            const countsToUpdate = {
                ...newCounts,
                all: newCounts.all ?? allCount,
            };

            ['all', 'ok', 'pendente', 'ignorado', 'divergente'].forEach(status => {
                const tabButton = document.querySelector(`#conciliacao-status-tab-${status}`);
                if (!tabButton) return;

                const countElement = tabButton.querySelector('.segmented-tab-count');
                if (countElement && countsToUpdate[status] !== undefined) {
                    countElement.textContent = countsToUpdate[status];
                }
            });
        };
    }

    // 2. Atualiza contadores iniciais se disponíveis
    if (window.initialConciliacaoCounts) {
        window.atualizarContagemStatusTabs(window.initialConciliacaoCounts);
    }

    // 3. Listener para mudança de abas
    const tabContainer = document.getElementById('conciliacao-status-tabs') || document.getElementById('conciliacao-status');
    if (tabContainer) {
        // Usa delegação ou busca todos os botões de tab
        const tabButtons = tabContainer.querySelectorAll('[data-bs-toggle="pill"], [data-bs-toggle="tab"]');
        
        tabButtons.forEach(btn => {
            btn.addEventListener('shown.bs.tab', (e) => {
                const targetId = e.target.getAttribute('aria-controls') || e.target.getAttribute('data-bs-target')?.replace('#', '');
                const targetPane = document.getElementById(targetId);
                const status = targetPane?.getAttribute('data-status') || 'all';
                
                
                // Atualiza referência do tbody
                const newItemBodyId = status === 'all' 
                    ? 'historico-conciliacoes-body' 
                    : `historico-conciliacoes-body-${status}`;
                
                const newTbody = document.getElementById(newItemBodyId);
                
                if (newTbody) {
                    tbody = newTbody; // Atualiza a variável global do escopo
                    
                    // Reseta paginação ao trocar de aba (opcional, mas recomendado)
                    state.page = 1;
                    
                    // Recarrega dados para a nova aba
                    load().catch(console.error);
                } else {
                }
            });
        });
    }
}
