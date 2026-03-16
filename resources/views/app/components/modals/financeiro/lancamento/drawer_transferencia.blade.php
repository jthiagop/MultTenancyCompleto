<!--begin::Drawer - Transferência entre Contas-->
<x-tenant-drawer drawerId="kt_drawer_transferencia" title="Transferência entre Contas"
    width="{default:'300px', 'md': '500px'}">

    <form id="kt_drawer_transferencia_form">
        @csrf
        <input type="hidden" id="transferencia_id" name="transferencia_id" value="">

        <!--begin::Input group - Conta de Origem-->
        <div class="mb-8">
            <x-tenant-select name="conta_origem_id" id="conta_origem_id" label="Conta de Origem"
                placeholder="De onde o valor sairá..." required :hideSearch="false"
                dropdown-parent="#kt_drawer_transferencia" class="">
                @isset($todasEntidades)
                    @foreach ($todasEntidades as $entidade)
                        <option value="{{ $entidade->id }}" data-tipo="{{ $entidade->tipo }}"
                            data-saldo="{{ $entidade->saldo_atual ?? 0 }}">
                            {{ $entidade->nome }}
                        </option>
                    @endforeach
                @endisset
            </x-tenant-select>
            <div class="fv-plugins-message-container">
                <div class="fv-help-block">
                    <span class="error-message text-danger fs-7" id="error-conta_origem_id"
                        style="display: none;"></span>
                </div>
            </div>
        </div>
        <!--end::Input group-->

        <!--begin::Input group - Conta de Destino-->
        <div class="mb-8">
            <x-tenant-select name="conta_destino_id" id="conta_destino_id" label="Conta de Destino"
                placeholder="Para onde o valor será enviado..." required :hideSearch="false"
                dropdown-parent="#kt_drawer_transferencia" class="">
                @isset($todasEntidades)
                    @foreach ($todasEntidades as $entidade)
                        <option value="{{ $entidade->id }}" data-tipo="{{ $entidade->tipo }}"
                            data-saldo="{{ $entidade->saldo_atual ?? 0 }}">
                            {{ $entidade->nome }}
                        </option>
                    @endforeach
                @endisset
            </x-tenant-select>
            <div class="fv-plugins-message-container">
                <div class="fv-help-block">
                    <span class="error-message text-danger fs-7" id="error-conta_destino_id"
                        style="display: none;"></span>
                </div>
            </div>
        </div>
        <!--end::Input group-->

        <!--begin::Input group - Descrição-->
        <div class="mb-8">
            <x-tenant-input name="descricao_transferencia" id="descricao_transferencia" type="text"
                label="Descrição" placeholder="Ex: Transferência entre contas correntes" required class="" />
            <div class="fv-plugins-message-container">
                <div class="fv-help-block">
                    <span class="error-message text-danger fs-7" id="error-descricao_transferencia"
                        style="display: none;"></span>
                </div>
            </div>
        </div>
        <!--end::Input group-->

        <!--begin::Input group - Data da Transferência-->
        <div class="mb-8">
            <x-tenant-date name="data_transferencia" id="data_transferencia" label="Data da Transferência"
                placeholder="dd/mm/aaaa" required class="" />
            <div class="text-muted fs-7 mt-2">Não é possível selecionar uma data futura.</div>
            <div class="fv-plugins-message-container">
                <div class="fv-help-block">
                    <span class="error-message text-danger fs-7" id="error-data_transferencia"
                        style="display: none;"></span>
                </div>
            </div>
        </div>
        <!--end::Input group-->

        <!--begin::Input group - Valor-->
        <div class="mb-8">
            <x-tenant-input name="valor_transferencia" id="valor2" type="text" label="Valor"
                placeholder="0,00" required mask="currency" class="" />
            <div class="fv-plugins-message-container">
                <div class="fv-help-block">
                    <span class="error-message text-danger fs-7" id="error-valor_transferencia"
                        style="display: none;"></span>
                </div>
            </div>
        </div>
        <!--end::Input group-->
    </form>

    <x-slot name="footer">
        <div class="d-flex justify-content-end gap-3">
            <x-tenant-button type="button" variant="light" size="sm" icon="bi bi-x-lg" iconPosition="left"
                id="kt_drawer_transferencia_close">
                Cancelar
            </x-tenant-button>

            <x-tenant-button type="button" variant="primary" size="sm" icon="bi bi-arrow-left-right"
                iconPosition="left" id="kt_drawer_transferencia_submit" :loading="true" loadingText="Aguarde..."
                form="kt_drawer_transferencia_form">
                Transferir
            </x-tenant-button>
        </div>
    </x-slot>
</x-tenant-drawer>

<!--begin::Script - Drawer Transferência-->
<script>
    (function() {
        'use strict';

        const SELECTORS = {
            form: 'kt_drawer_transferencia_form',
            drawer: 'kt_drawer_transferencia',
            submitBtn: 'kt_drawer_transferencia_submit',
            closeBtn: 'kt_drawer_transferencia_close',
            contaOrigem: 'conta_origem_id',
            contaDestino: 'conta_destino_id',
            descricao: 'descricao_transferencia',
            data: 'data_transferencia',
            valor: 'valor2',
            transferenciaId: 'transferencia_id',
        };

        const URLS = {
            store: "{{ route('transferencia.store') }}",
            show: "{{ route('transferencia.show', ['id' => '__ID__']) }}",
            update: "{{ route('transferencia.update', ['id' => '__ID__']) }}",
        };

        const ERROR_MAP = {
            'entidade_origem_id': 'error-conta_origem_id',
            'entidade_destino_id': 'error-conta_destino_id',
            'descricao': 'error-descricao_transferencia',
            'data': 'error-data_transferencia',
            'valor': 'error-valor_transferencia',
        };

        // Estado do drawer: 'criar' ou 'editar'
        let modoEdicao = false;

        function getEl(id) { return document.getElementById(id); }

        function limparErros() {
            Object.values(ERROR_MAP).forEach(function(spanId) {
                const el = getEl(spanId);
                if (el) { el.style.display = 'none'; el.textContent = ''; }
            });
        }

        function exibirErros(errors) {
            Object.entries(errors).forEach(function([campo, msgs]) {
                const spanId = ERROR_MAP[campo];
                if (spanId) {
                    const el = getEl(spanId);
                    if (el) {
                        el.textContent = Array.isArray(msgs) ? msgs[0] : msgs;
                        el.style.display = 'block';
                    }
                }
            });
        }

        function setModoEdicao(editando) {
            modoEdicao = editando;
            const submitBtn = getEl(SELECTORS.submitBtn);
            const drawerEl = getEl(SELECTORS.drawer);
            if (submitBtn) {
                const btnText = submitBtn.querySelector('.indicator-label');
                if (btnText) {
                    btnText.innerHTML = editando
                        ? '<i class="bi bi-pencil-square me-1"></i> Atualizar'
                        : '<i class="bi bi-arrow-left-right me-1"></i> Transferir';
                }
            }
            if (drawerEl) {
                const titleEl = drawerEl.querySelector('.card-title, [data-kt-drawer-title]');
                if (titleEl) {
                    titleEl.textContent = editando
                        ? 'Editar Transferência'
                        : 'Transferência entre Contas';
                }
            }
        }

        function resetForm() {
            const form = getEl(SELECTORS.form);
            if (!form) return;
            form.reset();

            // Limpar id da transferência
            const idInput = getEl(SELECTORS.transferenciaId);
            if (idInput) idInput.value = '';

            // Resetar Select2
            if (typeof $ !== 'undefined' && $.fn.select2) {
                $('#' + SELECTORS.contaOrigem).val('').trigger('change');
                $('#' + SELECTORS.contaDestino).val('').trigger('change');
            }

            // Resetar data para hoje (formato brasileiro)
            const dataInput = getEl(SELECTORS.data);
            if (dataInput) {
                const now = new Date();
                const hoje = String(now.getDate()).padStart(2, '0') + '/' + String(now.getMonth() + 1).padStart(2, '0') + '/' + now.getFullYear();
                dataInput.value = hoje;
                if (dataInput._flatpickr) {
                    dataInput._flatpickr.setDate(now, true);
                }
            }

            setModoEdicao(false);
            limparErros();
        }

        function getSelectedText(selectId) {
            const el = getEl(selectId);
            if (!el || !el.value) return '';
            const opt = el.options[el.selectedIndex];
            return opt ? opt.textContent.trim() : '';
        }

        function atualizarDescricao() {
            const nomeOrigem = getSelectedText(SELECTORS.contaOrigem);
            const nomeDestino = getSelectedText(SELECTORS.contaDestino);
            const descInput = getEl(SELECTORS.descricao);
            if (!descInput) return;

            if (nomeOrigem && nomeDestino) {
                descInput.value = 'Origem: ' + nomeOrigem + ' / Destino: ' + nomeDestino;
            } else if (nomeOrigem) {
                descInput.value = 'Origem: ' + nomeOrigem;
            } else if (nomeDestino) {
                descInput.value = 'Destino: ' + nomeDestino;
            }
        }

        function validarContasDiferentes() {
            const origem = getEl(SELECTORS.contaOrigem);
            const destino = getEl(SELECTORS.contaDestino);

            if (origem && destino && origem.value && destino.value && origem.value === destino.value) {
                const el = getEl(ERROR_MAP['entidade_destino_id']);
                if (el) {
                    el.textContent = 'A conta de destino deve ser diferente da conta de origem.';
                    el.style.display = 'block';
                }
                return false;
            }

            const el = getEl(ERROR_MAP['entidade_destino_id']);
            if (el) { el.style.display = 'none'; el.textContent = ''; }
            return true;
        }

        function fecharDrawer() {
            const drawerEl = getEl(SELECTORS.drawer);
            if (!drawerEl) return;
            const instance = KTDrawer.getInstance(drawerEl);
            if (instance) instance.hide();
        }

        function setLoading(loading) {
            const btn = getEl(SELECTORS.submitBtn);
            if (!btn) return;

            if (loading) {
                btn.setAttribute('data-kt-indicator', 'on');
                btn.disabled = true;
            } else {
                btn.removeAttribute('data-kt-indicator');
                btn.disabled = false;
            }
        }

        function preencherFormulario(dados) {
            // Conta de Origem (Select2)
            if (typeof $ !== 'undefined' && $.fn.select2) {
                $('#' + SELECTORS.contaOrigem).val(dados.entidade_origem_id).trigger('change');
                $('#' + SELECTORS.contaDestino).val(dados.entidade_destino_id).trigger('change');
            }

            // Descrição
            const descInput = getEl(SELECTORS.descricao);
            if (descInput) descInput.value = dados.descricao || '';

            // Data (formato brasileiro dd/mm/yyyy)
            const dataInput = getEl(SELECTORS.data);
            if (dataInput && dados.data) {
                dataInput.value = dados.data;
                if (dataInput._flatpickr) {
                    dataInput._flatpickr.setDate(dados.data, true, 'd/m/Y');
                }
            }

            // Valor
            const form = getEl(SELECTORS.form);
            const valorInput = form ? form.querySelector('#' + SELECTORS.valor) : getEl(SELECTORS.valor);
            if (valorInput) valorInput.value = dados.valor || '';
        }

        function submitTransferencia() {
            limparErros();

            if (!validarContasDiferentes()) return;

            const form = getEl(SELECTORS.form);
            const origem = getEl(SELECTORS.contaOrigem);
            const destino = getEl(SELECTORS.contaDestino);
            const descricao = getEl(SELECTORS.descricao);
            const data = getEl(SELECTORS.data);
            const valor = form ? form.querySelector('#' + SELECTORS.valor) : getEl(SELECTORS.valor);
            const transferenciaId = getEl(SELECTORS.transferenciaId)?.value;

            // Validação básica client-side
            if (!origem?.value || !destino?.value || !descricao?.value || !data?.value || !valor?.value) {
                if (!origem?.value) exibirErros({ entidade_origem_id: 'Selecione a conta de origem.' });
                if (!destino?.value) exibirErros({ entidade_destino_id: 'Selecione a conta de destino.' });
                if (!descricao?.value) exibirErros({ descricao: 'Informe a descrição.' });
                if (!data?.value) exibirErros({ data: 'Informe a data.' });
                if (!valor?.value) exibirErros({ valor: 'Informe o valor.' });
                return;
            }

            setLoading(true);

            const csrfToken = document.querySelector('#' + SELECTORS.form + ' input[name="_token"]')?.value
                || document.querySelector('meta[name="csrf-token"]')?.content;

            // Determinar URL e método baseado no modo
            const isEdit = modoEdicao && transferenciaId;
            const url = isEdit
                ? URLS.update.replace('__ID__', transferenciaId)
                : URLS.store;
            const method = isEdit ? 'PUT' : 'POST';

            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    entidade_origem_id: origem.value,
                    entidade_destino_id: destino.value,
                    descricao: descricao.value,
                    data: data.value,
                    valor: valor.value,
                }),
            })
            .then(function(response) {
                return response.json().then(function(data) {
                    return { ok: response.ok, status: response.status, data: data };
                });
            })
            .then(function(result) {
                setLoading(false);

                if (result.ok && result.data.success) {
                    fecharDrawer();
                    resetForm();

                    Swal.fire({
                        icon: 'success',
                        title: 'Sucesso!',
                        text: result.data.message || (isEdit ? 'Transferência atualizada!' : 'Transferência realizada!'),
                        timer: 2500,
                        showConfirmButton: false,
                    });

                    // Recarregar DataTables se existirem
                    if (typeof window.LaravelDataTables !== 'undefined') {
                        Object.values(window.LaravelDataTables).forEach(function(dt) {
                            if (dt && typeof dt.ajax === 'object' && typeof dt.ajax.reload === 'function') {
                                dt.ajax.reload(null, false);
                            }
                        });
                    }

                    document.dispatchEvent(new CustomEvent('transferencia:created'));

                } else if (result.status === 422 && result.data.errors) {
                    exibirErros(result.data.errors);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: result.data.message || 'Erro ao processar transferência.',
                    });
                }
            })
            .catch(function(error) {
                setLoading(false);
                console.error('Erro na transferência:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Erro',
                    text: 'Erro de conexão. Tente novamente.',
                });
            });
        }

        function initDrawerTransferencia() {
            const form = getEl(SELECTORS.form);
            if (!form) return;

            // Limitar data máxima para hoje via Flatpickr
            const dataInput = getEl(SELECTORS.data);
            if (dataInput && dataInput._flatpickr) {
                dataInput._flatpickr.set('maxDate', 'today');
            }

            // Validação de contas diferentes e auto-descrição nos selects
            if (typeof $ !== 'undefined' && $.fn.select2) {
                $('#' + SELECTORS.contaOrigem).on('change', function() {
                    validarContasDiferentes();
                    atualizarDescricao();
                });
                $('#' + SELECTORS.contaDestino).on('change', function() {
                    validarContasDiferentes();
                    atualizarDescricao();
                });
            }

            // Submit
            const submitBtn = getEl(SELECTORS.submitBtn);
            if (submitBtn) {
                submitBtn.addEventListener('click', submitTransferencia);
            }

            // Cancelar
            const closeBtn = getEl(SELECTORS.closeBtn);
            if (closeBtn) {
                closeBtn.addEventListener('click', function() {
                    fecharDrawer();
                    resetForm();
                });
            }
        }

        // Abrir drawer para CRIAR (sem parâmetro)
        window.abrirDrawerTransferencia = function(transferenciaId) {
            resetForm();

            const drawerEl = getEl(SELECTORS.drawer);
            if (!drawerEl) return;
            const drawerInstance = KTDrawer.getInstance(drawerEl);

            if (transferenciaId) {
                // Modo EDIÇÃO: carregar dados via AJAX
                setModoEdicao(true);
                const idInput = getEl(SELECTORS.transferenciaId);
                if (idInput) idInput.value = transferenciaId;

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                const showUrl = URLS.show.replace('__ID__', transferenciaId);

                fetch(showUrl, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                })
                .then(function(response) { return response.json(); })
                .then(function(result) {
                    if (result.success && result.transferencia) {
                        preencherFormulario(result.transferencia);
                        if (drawerInstance) drawerInstance.toggle();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: 'Não foi possível carregar os dados da transferência.',
                        });
                    }
                })
                .catch(function(error) {
                    console.error('Erro ao carregar transferência:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: 'Erro de conexão ao carregar transferência.',
                    });
                });
            } else {
                // Modo CRIAÇÃO
                if (drawerInstance) drawerInstance.toggle();
            }
        };

        // Inicializa quando o DOM estiver pronto
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initDrawerTransferencia);
        } else {
            initDrawerTransferencia();
        }
    })();
</script>
<!--end::Script - Drawer Transferência-->
