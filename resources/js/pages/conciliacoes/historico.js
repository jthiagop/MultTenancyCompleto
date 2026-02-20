/**
 * Módulo de Histórico de Conciliações
 * Padrão "Laravel Way" com paginação no servidor + debounce + event delegation
 */


// Busca o container inicial (tab 'all' por padrão)
const el = document.getElementById('historico-container-all') || document.getElementById('conciliacoes-historico');
if (!el) {
    console.warn('[Historico] Container não encontrado');
} else {

    // Configs do container via data-*
    const entidadeId = JSON.parse(el.dataset.entidadeId || 'null');
    const urlHistorico = el.dataset.urlHistorico;
    const urlDetalhesTpl = el.dataset.urlDetalhes;
    const urlDesfazerTpl = el.dataset.urlDesfazer;

    // Elementos DOM - busca inicial para tab 'all'
    let tbody = document.getElementById('historico-conciliacoes-body');

    // Busca dinâmica por tab
    function getBuscaInput(status) {
        status = status || getActiveStatus();
        return document.getElementById(`busca-historico-${status}`);
    }

    // Paginação dinâmica por tab (funções helper)
    function getActiveStatus() {
        const activeBtn = document.querySelector('#historico-status-tabs-tabs .nav-link.active');
        if (activeBtn) {
            const key = activeBtn.getAttribute('data-tab-key') || activeBtn.getAttribute('data-status-tab');
            if (key) return key;
            const targetId = activeBtn.getAttribute('data-bs-target')?.replace('#', '');
            if (targetId) {
                const pane = document.getElementById(targetId);
                if (pane) return pane.getAttribute('data-status') || 'all';
            }
        }
        const activePane = document.querySelector('[data-status].show.active');
        return activePane ? activePane.getAttribute('data-status') || 'all' : 'all';
    }

    function getPerPageSelect(status) {
        status = status || getActiveStatus();
        return document.getElementById(`items-per-page-${status}`);
    }

    function getPaginationContainer(status) {
        status = status || getActiveStatus();
        return document.getElementById(`historico-pagination-${status}`);
    }

    // Referências iniciais (compatibilidade)
    let perPageSelect = getPerPageSelect('all');
    let paginationContainer = getPaginationContainer('all');

    // Função auxiliar para obter tbody baseado no status
    function getTbodyByStatus(status) {
        const tbodyId = status === 'all'
            ? 'historico-conciliacoes-body'
            : `historico-conciliacoes-body-${status}`;
        const foundTbody = document.getElementById(tbodyId);
        if (!foundTbody) {
            console.warn(`[Historico] Tbody não encontrado para status: ${status} (ID: ${tbodyId})`);
        }
        return foundTbody;
    }

    if (!tbody) {
        console.warn('[Historico] Tbody inicial não encontrado, será buscado na primeira troca de aba');
        // Tenta buscar o tbody da tab ativa
        const activePane = document.querySelector('[data-status].show.active');
        if (activePane) {
            const activeStatus = activePane.getAttribute('data-status') || 'all';
            tbody = getTbodyByStatus(activeStatus);
        }
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

    // Estado da paginação/busca/período
    let state = { page: 1, per_page: 10, q: '', start_date: null, end_date: null };
    let aborter = null;

    // Período: inicializa com mês atual
    if (typeof moment !== 'undefined') {
        state.start_date = moment().startOf('month').format('YYYY-MM-DD');
        state.end_date = moment().endOf('month').format('YYYY-MM-DD');
    }

    /**
     * Atualiza tfoot com o total da página
     */
    function updateTotal(json, status) {
        status = status || getActiveStatus();
        const totalEl = document.getElementById(`historico-total-${status}`);
        if (!totalEl) return;

        const entradas = json?.total_entradas || 0;
        const saidas = json?.total_saidas || 0;
        const saldo = entradas - saidas;

        if (saldo >= 0) {
            totalEl.textContent = `R$ ${money.format(saldo)}`;
            totalEl.classList.remove('text-danger');
            totalEl.classList.add('text-success');
        } else {
            totalEl.textContent = `-R$ ${money.format(Math.abs(saldo))}`;
            totalEl.classList.remove('text-success');
            totalEl.classList.add('text-danger');
        }
    }

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
        // Verifica se tbody está definido
        if (!tbody) {
            console.error('[Historico] Tbody não definido ao renderizar linhas');
            return;
        }

        if (!items?.length) {
            console.warn('[Historico] Array vazio ou undefined, mostrando mensagem de vazio');
            console.warn('   items:', items);
            console.warn('   tbody.id:', tbody.id);

            tbody.innerHTML = `
                <tr><td colspan="7" class="text-center py-10">
                    <div class="d-flex flex-column align-items-center">
                        <i class="ki-duotone ki-document fs-3x text-muted mb-3">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <span class="text-muted fs-6">Nenhuma conciliação encontrada para este filtro</span>
                    </div>
                </td></tr>`;
            return;
        }

        console.log(`[Historico] Renderizando ${items.length} itens no tbody:`, tbody.id);


        tbody.innerHTML = items.map((item, idx) => {

        const tipoNormalizado = window.normalizeTipo ? window.normalizeTipo(item.tipo) : item.tipo;
        const tipoIsEntrada = tipoNormalizado === 'receita' || item.tipo === 'entrada';
        const tipoClasse = tipoIsEntrada ? 'badge-light-success' : 'badge-light-danger';
        const valorClasse = tipoIsEntrada ? 'text-success' : 'text-danger';
            const detalhesId = Number(item.id);

            // Renderiza Lancamento Padrao badge
            const lancamentoPadrao = item.lancamento_padrao && item.lancamento_padrao !== '-'
                ? `<div class="mt-1">
                     <span class="text-primary fs-8 fw-semibold border border-primary border-dashed px-2 py-0 rounded">
                        ${escapeHtml(item.lancamento_padrao)}
                     </span>
                   </div>`
                : '';

            // Renderiza Parceiro e Descrição Interna
            let detalhesInternos = '';
            if ((item.parceiro_nome && item.parceiro_nome !== '-') ||
                (item.transacao_descricao && item.transacao_descricao !== '-' && item.transacao_descricao !== item.descricao)) {

                detalhesInternos = `<div class="d-flex align-items-center flex-wrap gap-1">`;

                if (item.parceiro_nome && item.parceiro_nome !== '-') {
                    detalhesInternos += `<span class="badge badge-light-secondary fw-bold fs-8" title="Parceiro">${escapeHtml(item.parceiro_nome)}</span>`;
                }

                if (item.transacao_descricao && item.transacao_descricao !== '-' && item.transacao_descricao !== item.descricao) {
                    detalhesInternos += `<span class="text-gray-600 fs-7 italic">"${escapeHtml(item.transacao_descricao)}"</span>`;
                }

                detalhesInternos += `</div>`;
            }

            return `
                <tr data-id="${detalhesId}" style="cursor:pointer;">
                    <td class="ps-0">
                        <div class="d-flex flex-column">
                            <span class="text-gray-800 fw-bold">${escapeHtml(item.data_extrato_formatada || '-')}</span>
                            <span class="text-muted fs-7" title="Data da Conciliação">
                                <i class="bi bi-clock-history fs-8 me-1"></i>${escapeHtml(item.data_conciliacao_formatada || '-')}
                            </span>
                        </div>
                    </td>
                    <td class="descricao-conciliacao">
                        <!-- Histórico do Banco (Memo) -->
                        <a href="#" class="text-gray-800 fw-bold text-hover-primary d-block fs-6 mb-1"
                           data-action="ver-detalhes" data-id="${detalhesId}">
                            ${escapeHtml(item.descricao || '-')}
                        </a>

                        ${detalhesInternos}
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
    function renderPagination(meta, targetStatus) {
        const container = targetStatus ? getPaginationContainer(targetStatus) : (getPaginationContainer() || paginationContainer);
        if (!container) return;

        const totalPages = meta?.last_page || 1;
        const current = meta?.current_page || 1;

        if (totalPages <= 1) {
            container.innerHTML = '';
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

        container.innerHTML = html;
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

        // Detecta qual tab está ativa para passar o status correto de forma robusta
        // Busca pelo botão de tab ativo do Bootstrap
        const activeTabButton = document.querySelector('#historico-status-tabs-tabs .nav-link.active, #historico-status-tabs .nav-link.active');
        let activeStatus = 'all';

        if (activeTabButton) {
            // Tenta obter do data-tab-key ou data-status-tab
            activeStatus = activeTabButton.getAttribute('data-tab-key') ||
                          activeTabButton.getAttribute('data-status-tab') ||
                          'all';

            // Se não encontrou, tenta extrair do data-bs-target
            if (activeStatus === 'all' || !activeStatus) {
                const targetId = activeTabButton.getAttribute('data-bs-target')?.replace('#', '');
                if (targetId) {
                    const targetPane = document.getElementById(targetId);
                    if (targetPane) {
                        activeStatus = targetPane.getAttribute('data-status') || 'all';
                    }
                }
            }
        } else {
            // Fallback: busca pelo pane ativo
            const activePane = document.querySelector('[data-status].show.active, [data-status].show');
            if (activePane) {
                activeStatus = activePane.getAttribute('data-status') || 'all';
            }
        }

        console.log('[Historico] Status detectado para carregamento:', activeStatus);


        // Cancela requisição anterior se houver
        if (aborter) aborter.abort();
        aborter = new AbortController();

        // Atualiza tbody baseado no status antes de mostrar loading
        const tbodyForStatus = getTbodyByStatus(activeStatus);
        if (tbodyForStatus) {
            tbody = tbodyForStatus;
        }

        // Loading state
        if (tbody) {
            tbody.innerHTML = `
                    <tr><td colspan="7" class="text-center py-10">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Carregando...</span>
                        </div>
                        <div class="text-muted mt-3">Carregando histórico (${activeStatus})...</div>
                    </td></tr>`;
        }

        try {
            const qs = new URLSearchParams({
                page: String(state.page),
                per_page: String(state.per_page),
                q: state.q || '',
                status: activeStatus,
            });

            // Adiciona período se definido
            if (state.start_date) qs.set('start_date', state.start_date);
            if (state.end_date) qs.set('end_date', state.end_date);

            const fullUrl = `${urlHistorico}?${qs.toString()}`;
            console.log('[Historico] Carregando dados:', {
                status: activeStatus,
                page: state.page,
                per_page: state.per_page,
                q: state.q,
                start_date: state.start_date,
                end_date: state.end_date,
                url: fullUrl
            });

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

            // Atualiza contadores das tabs se retornados
            if (json.counts && typeof window.atualizarContagemStatusTabs === 'function') {
                window.atualizarContagemStatusTabs(json.counts);
            }

            // Novo padrão: controller retorna HTML renderizado
            if (json.html) {
                // Atualiza contadores das tabs se retornados
                if (json.counts && typeof window.atualizarContagemStatusTabs === 'function') {
                    window.atualizarContagemStatusTabs(json.counts);
                }

                // IMPORTANTE: Garante que está usando o tbody correto para o status atual
                // Isso previne que dados de uma tab sejam renderizados em outra
                const currentTbody = getTbodyByStatus(activeStatus);
                if (currentTbody) {
                    tbody = currentTbody;
                    console.log('[Historico] Renderizando HTML no tbody:', currentTbody.id, 'para status:', activeStatus);

                    // Renderiza HTML diretamente no tbody correto
                    tbody.innerHTML = json.html;
                    renderPagination(json?.meta, activeStatus);
                    updateTotal(json, activeStatus);

                    // Reinicializa menus apenas no tbody atual
                    if (typeof KTMenu !== 'undefined') {
                        // Busca menus apenas dentro do tbody atual para evitar conflitos
                        const menuTriggers = tbody.closest('.card')?.querySelectorAll('[data-kt-menu-trigger]');
                        if (menuTriggers && menuTriggers.length > 0) {
                            KTMenu.createInstances();
                        }
                    }
                } else {
                    console.error('[Historico] Tbody não encontrado para status:', activeStatus);
                }

                return;
            }

            // Padrão antigo: controller retorna array de dados
            // IMPORTANTE: Garante que está usando o tbody correto para o status atual
            const currentTbody = getTbodyByStatus(activeStatus);
            if (currentTbody) {
                tbody = currentTbody;
                console.log('[Historico] Renderizando dados no tbody:', currentTbody.id, 'para status:', activeStatus);
            } else {
                console.error('[Historico] Tbody não encontrado para status:', activeStatus);
                return;
            }

            const items = json?.data ?? [];

            if (!items || items.length === 0) {
                console.warn('[Historico] Nenhum item retornado do servidor para status:', activeStatus);
                console.warn('   Success:', json?.success);
                console.warn('   Data:', json?.data);
            }

            renderRows(items);
            renderPagination(json?.meta, activeStatus);
            updateTotal(json, activeStatus);

        } catch (error) {
            if (error.name === 'AbortError') {
                return;
            }

            console.error('❌ Erro ao carregar histórico:', error);
            console.error('   Message:', error.message);
            console.error('   Stack:', error.stack);

            if (tbody) {
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
    }

    /**
     * Event Listeners
     */

    // Busca com debounce - event delegation para todos os inputs de busca
    const debouncedSearch = debounce(() => {
        const buscaInput = getBuscaInput();
        state.page = 1;
        state.q = buscaInput ? buscaInput.value.trim() : '';
        load().catch(console.error);
    });

    document.addEventListener('input', (e) => {
        if (e.target.closest('.busca-historico-input')) {
            debouncedSearch();
        }
    });

    // Mudança de itens por página - event delegation para todos os selects
    document.addEventListener('change', (e) => {
        const sel = e.target.closest('.items-per-page-select');
        if (!sel) return;
        state.per_page = Number(sel.value || 10);
        state.page = 1;
        // Sincroniza o valor em todos os selects
        document.querySelectorAll('.items-per-page-select').forEach(s => { s.value = sel.value; });
        load().catch(console.error);
    });

    // Event delegation: paginação em todas as tabs
    document.addEventListener('click', (e) => {
        const a = e.target.closest('.historico-pagination-list .page-link');
        if (!a) return;

        e.preventDefault();
        const p = Number(a.dataset.page);
        if (!p || p < 1) return;

        state.page = p;
        load().catch(console.error);
    });

    // Event delegation: ações da tabela (usando document para funcionar em todas as tabs)
    document.addEventListener('click', (e) => {
        // Verifica se o clique foi dentro de um container de histórico
        const container = e.target.closest('[id^="historico-container-"]');
        if (!container) return;

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

    // ========== SELETOR DE PERÍODO (via tenant-datatable-filters / periodChanged event) ==========
    document.addEventListener('periodChanged', (e) => {
        if (e.detail.tableId !== 'historico-conciliacoes') return;
        state.start_date = e.detail.start ? e.detail.start.format('YYYY-MM-DD') : null;
        state.end_date = e.detail.end ? e.detail.end.format('YYYY-MM-DD') : null;
        state.page = 1;
        load().catch(console.error);
    });
    // ========== FIM SELETOR DE PERÍODO ==========

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

    // Carrega dados inicialmente apenas para a tab ativa
    // Aguarda um pouco para garantir que o DOM está pronto e as tabs estão inicializadas
    setTimeout(() => {
        // Verifica qual tab está ativa inicialmente
        const initialActiveTab = document.querySelector('#historico-status-tabs-tabs .nav-link.active, #historico-status-tabs .nav-link.active');
        const initialActivePane = document.querySelector('[data-status].show.active');

        if (initialActiveTab || initialActivePane) {
            console.log('[Historico] Carregando dados iniciais para tab ativa');
            load().catch(console.error);
        } else {
            // Se não encontrou tab ativa, tenta carregar para 'all' (padrão)
            console.log('[Historico] Nenhuma tab ativa encontrada, carregando para "all"');
            // Força o tbody para 'all' e carrega
            const allTbody = getTbodyByStatus('all');
            if (allTbody) {
                tbody = allTbody;
                load().catch(console.error);
            }
        }
    }, 300);

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

            // Calcula total sem incluir pendentes (que não são exibidos)
            const allCount = (newCounts.ok || 0) + (newCounts.ignorado || 0) + (newCounts.divergente || 0);
            const countsToUpdate = {
                ...newCounts,
                all: newCounts.all ?? allCount,
            };

            // Apenas os status que são exibidos nas tabs
            ['all', 'ok', 'ignorado', 'divergente'].forEach(status => {
                const tabButton = document.querySelector(`#historico-status-tabs-tab-${status}`) ||
                                 document.querySelector(`#conciliacao-status-tab-${status}`);
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

    // 3. Listener para mudança de abas - usando event delegation no document
    // O Bootstrap dispara o evento 'shown.bs.tab' no elemento que foi clicado
    // Usamos event delegation no document para capturar todos os eventos de tab

    function handleTabChange(e) {
        // O evento pode vir do botão ou do elemento relacionado
        const targetButton = e.target.closest('[data-bs-toggle="tab"]') || e.target;

        // Verifica se é uma tab do histórico
        if (!targetButton || !targetButton.hasAttribute('data-bs-toggle')) {
            return;
        }

        // Verifica se pertence ao container de histórico
        const tabContainer = document.getElementById('historico-status-tabs-tabs');
        if (!tabContainer || !tabContainer.contains(targetButton)) {
            return;
        }

        const targetId = targetButton.getAttribute('data-bs-target')?.replace('#', '') ||
                       targetButton.getAttribute('aria-controls');

        if (!targetId) {
            console.warn('[Historico] Target ID não encontrado no botão da tab');
            return;
        }

        const targetPane = document.getElementById(targetId);
        if (!targetPane) {
            console.warn('[Historico] Pane não encontrado:', targetId);
            console.warn('[Historico] Tentando encontrar pane alternativo...');
            // Tenta encontrar pelo data-status se o ID não funcionar
            const statusFromButton = targetButton.getAttribute('data-tab-key') || 
                                    targetButton.getAttribute('data-status-tab');
            if (statusFromButton) {
                const altPane = document.querySelector(`[data-status="${statusFromButton}"]`);
                if (altPane) {
                    console.log('[Historico] Pane encontrado pelo data-status:', statusFromButton);
                    // Continua com o pane alternativo
                    const status = statusFromButton;
                    const newItemBodyId = status === 'all' 
                        ? 'historico-conciliacoes-body' 
                        : `historico-conciliacoes-body-${status}`;
                    const newTbody = document.getElementById(newItemBodyId);
                    if (newTbody) {
                        tbody = newTbody;
                        perPageSelect = getPerPageSelect(status);
                        paginationContainer = getPaginationContainer(status);
                        state.page = 1;
                        state.q = '';
                        const buscaEl = getBuscaInput(status);
                        if (buscaEl) buscaEl.value = '';
                        newTbody.innerHTML = `
                            <tr><td colspan="7" class="text-center py-10">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Carregando...</span>
                                </div>
                                <div class="text-muted mt-3">Carregando histórico (${status})...</div>
                            </td></tr>`;
                        load().catch(console.error);
                    }
                    return;
                }
            }
            return;
        }

        const status = targetPane.getAttribute('data-status') ||
                      targetButton.getAttribute('data-tab-key') ||
                      targetButton.getAttribute('data-status-tab') ||
                      'all';

        console.log('[Historico] Tab mudou para:', status, 'Pane:', targetId);

        // Atualiza referência do tbody baseado no status
        const newItemBodyId = status === 'all'
            ? 'historico-conciliacoes-body'
            : `historico-conciliacoes-body-${status}`;

        const newTbody = document.getElementById(newItemBodyId);

        if (newTbody) {
            tbody = newTbody; // Atualiza a variável global do escopo
            perPageSelect = getPerPageSelect(status);
            paginationContainer = getPaginationContainer(status);
            console.log('[Historico] Tbody atualizado para:', newItemBodyId);

            // Reseta paginação ao trocar de aba
            state.page = 1;
            state.q = ''; // Limpa busca ao trocar de tab

            // Limpa o campo de busca da tab nova
            const buscaEl = getBuscaInput(status);
            if (buscaEl) buscaEl.value = '';

            // Mostra loading no tbody da nova tab
            newTbody.innerHTML = `
                <tr><td colspan="7" class="text-center py-10">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                    <div class="text-muted mt-3">Carregando histórico (${status})...</div>
                </td></tr>`;

            // Recarrega dados para a nova aba com o status correto
            load().catch(console.error);
        } else {
            console.warn('[Historico] Tbody não encontrado:', newItemBodyId);
        }
    }

    // Registra o listener no document para capturar todos os eventos de tab
    document.addEventListener('shown.bs.tab', handleTabChange);

    console.log('[Historico] Listener de tabs registrado no document');
}
