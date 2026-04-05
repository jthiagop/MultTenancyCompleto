<!--begin::Drawer - Repasse para Filiais-->
<x-tenant-drawer drawerId="kt_drawer_repasse" title="Novo Repasse para Filiais" width="{default:'350px', 'md': '600px'}">

    <form id="kt_drawer_repasse_form">
        @csrf



        <!--begin::Conta de Origem + Valor Total (row)-->
        <div class="row mb-6">
            <div class="col-md-6">
                <x-tenant-select name="entidade_origem_id" id="repasse_entidade_origem" label="Conta de Origem"
                    placeholder="Selecione a conta de saída..." required :hideSearch="false"
                    dropdown-parent="#kt_drawer_repasse" class="">
                    @isset($todasEntidades)
                        @foreach ($todasEntidades as $entidade)
                            <option value="{{ $entidade->id }}">{{ $entidade->nome }}</option>
                        @endforeach
                    @endisset
                </x-tenant-select>
                <span class="error-message text-danger fs-7" id="error-entidade_origem_id"
                    style="display: none;"></span>
            </div>
            <div class="col-md-6">
                <x-tenant-input name="valor_total" id="repasse_valor_total" type="text" label="Valor Total (R$)"
                    placeholder="0,00" required mask="currency" class="" />
                <span class="error-message text-danger fs-7" id="error-valor_total" style="display: none;"></span>
            </div>
        </div>
        <!--end::Conta de Origem + Valor Total-->

        <!--begin::Datas (row)-->
        <div class="row mb-6">
            <div class="col-md-4">
                <x-tenant-date name="data_emissao" id="repasse_data_emissao" label="Data Emissão"
                    placeholder="dd/mm/aaaa" required />
                <span class="error-message text-danger fs-7" id="error-data_emissao" style="display: none;"></span>
            </div>
            <div class="col-md-4">
                <x-tenant-date name="data_vencimento" id="repasse_data_vencimento" label="Data Vencimento"
                    placeholder="dd/mm/aaaa" />
                <span class="error-message text-danger fs-7" id="error-data_vencimento" style="display: none;"></span>
            </div>
            <div class="col-md-4">
                <x-tenant-date name="data_entrada" id="repasse_data_entrada" label="Data Entrada"
                    placeholder="dd/mm/aaaa" />
                <span class="error-message text-danger fs-7" id="error-data_entrada" style="display: none;"></span>
            </div>
        </div>
        <!--end::Datas-->

        <!--begin::Competência + Forma de Recebimento-->
        <div class="row mb-6">
            <div class="col-md-6">
                <x-tenant-date name="competencia" id="repasse_competencia" label="Competência" placeholder="mm/aaaa" />
                <span class="error-message text-danger fs-7" id="error-competencia" style="display: none;"></span>
            </div>
            <div class="col-md-6">
                <x-tenant-select name="forma_recebimento_id" id="repasse_forma_recebimento" label="Forma de Recebimento"
                    placeholder="Selecione..." required :hideSearch="false" dropdown-parent="#kt_drawer_repasse"
                    class="">
                    <option value="">Selecione...</option>
                    @isset($formasRecebimento)
                        @foreach ($formasRecebimento as $fr)
                            <option value="{{ $fr->id }}">{{ $fr->nome }}</option>
                        @endforeach
                    @endisset
                </x-tenant-select>
                <span class="error-message text-danger fs-7" id="error-forma_recebimento_id"
                    style="display: none;"></span>
            </div>
        </div>
        <!--end::Competência + Forma de Recebimento-->

        <!--begin::Nº Documento + Filial Destino-->
        <div class="row mb-6">
            <div class="col-md-4">
                <x-tenant-input name="numero_documento" id="repasse_numero_documento" type="text"
                    label="Nº do Documento" placeholder="Ex: 001/2026" required class="" />
                <span class="error-message text-danger fs-7" id="error-numero_documento" style="display: none;"></span>
            </div>
            <div class="col-md-8">
                <x-tenant-select name="company_destino_id" id="repasse_filial_destino" label="Filial Destino"
                    placeholder="Selecione a filial..." required :hideSearch="false"
                    dropdown-parent="#kt_drawer_repasse" class="">
                </x-tenant-select>
                <span class="error-message text-danger fs-7" id="error-company_destino_id"
                    style="display: none;"></span>
            </div>
        </div>
        <!--end::Nº Documento + Filial Destino-->

        <!--begin::Descrição-->
        <div class="mb-6">
            <label class="form-label fw-bold required">Descrição / Justificativa</label>
            <textarea class="form-control form-control-sm" name="descricao" id="repasse_descricao" rows="3"
                placeholder="Ex: Repasse mensal de março/2026" maxlength="500" required></textarea>
            <span class="error-message text-danger fs-7" id="error-descricao" style="display: none;"></span>
        </div>
        <!--end::Descrição-->
        <!--begin::Separator-->
        <div class="separator separator-dashed my-6"></div>
        <!--end::Separator-->



        <!--begin::Executar Imediato-->
        <div class="mb-6">
            <label class="form-check form-check-custom form-check-sm">
                <input class="form-check-input" type="checkbox" name="executar_imediato"
                    id="repasse_executar_imediato" value="1">
                <span class="form-check-label fw-semibold">Executar imediatamente (gera transações financeiras)</span>
            </label>
            <div class="text-muted fs-8 mt-1">Se desmarcado, o repasse ficará como "Pendente" para execução posterior.
            </div>
        </div>
        <!--end::Executar Imediato-->
    </form>

    <x-slot name="footer">
        <button type="button" class="btn btn-sm btn-light me-3" data-kt-drawer-dismiss="true">Cancelar</button>
        <button type="button" class="btn btn-sm btn-primary" id="repasse_submit_btn" onclick="submitRepasse()">
            <span class="indicator-label" id="repasse_submit_label">Salvar Repasse</span>
            <span class="indicator-progress">Processando...
                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
            </span>
        </button>
    </x-slot>
</x-tenant-drawer>
<!--end::Drawer - Repasse para Filiais-->

@push('scripts')
    <script>
        (function() {
            'use strict';

            var URLS = {
                store: '{{ route('repasses.store') }}',
                update: '{{ url("repasses") }}',
                filiais: '{{ route('repasses.filiais') }}'
            };

            var filiaisDisponiveis = [];
            var modoEdicao = false;
            var repasseEditandoId = null;

            // Carregar filiais ao abrir o drawer
            window.abrirDrawerRepasse = function() {
                modoEdicao = false;
                repasseEditandoId = null;
                resetForm();
                atualizarTituloDrawer();
                carregarFiliais();

                var drawerEl = document.querySelector('#kt_drawer_repasse');
                if (drawerEl) {
                    var drawer = KTDrawer.getInstance(drawerEl);
                    if (drawer) drawer.show();
                }
            };

            // Abrir drawer em modo edição com dados do repasse
            window.abrirDrawerRepasseEdicao = function(repasse) {
                modoEdicao = true;
                repasseEditandoId = repasse.id;
                resetForm();
                atualizarTituloDrawer();

                // Carregar filiais e depois preencher o formulário
                carregarFiliais(function() {
                    preencherFormulario(repasse);
                });

                var drawerEl = document.querySelector('#kt_drawer_repasse');
                if (drawerEl) {
                    var drawer = KTDrawer.getInstance(drawerEl);
                    if (drawer) drawer.show();
                }
            };

            function atualizarTituloDrawer() {
                var tituloEl = document.querySelector('#kt_drawer_repasse .drawer-title, #kt_drawer_repasse [data-drawer-title]');
                // Tenta atualizar o heading do drawer
                var headingEl = document.querySelector('#kt_drawer_repasse .card-title');
                if (headingEl) {
                    headingEl.textContent = modoEdicao ? 'Editar Repasse #' + repasseEditandoId : 'Novo Repasse para Filiais';
                }
                var btnLabel = document.getElementById('repasse_submit_label');
                if (btnLabel) {
                    btnLabel.textContent = modoEdicao ? 'Atualizar Repasse' : 'Salvar Repasse';
                }
                // Ocultar checkbox "executar imediato" em modo edição
                var execImediato = document.getElementById('repasse_executar_imediato');
                if (execImediato) {
                    var wrapper = execImediato.closest('.mb-6');
                    if (wrapper) {
                        wrapper.style.display = modoEdicao ? 'none' : '';
                    }
                }
            }

            function preencherFormulario(repasse) {
                // Conta de origem
                var origemSelect = document.getElementById('repasse_entidade_origem');
                if (origemSelect) {
                    $(origemSelect).val(repasse.entidade_origem_id).trigger('change');
                }

                // Valor total (campo com Inputmask currency)
                var valorInput = document.getElementById('repasse_valor_total');
                if (valorInput && repasse.valor_total) {
                    // Remove o Inputmask existente (junto com seus handlers de cursor forçado)
                    if (valorInput.inputmask) {
                        valorInput.inputmask.remove();
                    }
                    // Clona o elemento para remover os event listeners externos (cursor forçado)
                    var clone = valorInput.cloneNode(true);
                    clone.removeAttribute('data-mask-initialized');
                    valorInput.parentNode.replaceChild(clone, valorInput);
                    valorInput = clone;

                    // Seta o valor bruto (formato BR: "1.500,00")
                    valorInput.value = repasse.valor_total;

                    // Re-aplica o Inputmask SEM os event listeners de cursor forçado
                    // Isso permite que o usuário edite o valor livremente
                    if (typeof Inputmask !== 'undefined') {
                        Inputmask({
                            alias: "currency",
                            groupSeparator: ".",
                            radixPoint: ",",
                            autoGroup: true,
                            digits: 2,
                            digitsOptional: false,
                            placeholder: "0,00",
                            rightAlign: false,
                            removeMaskOnSubmit: false,
                            allowMinus: false,
                            clearMaskOnLostFocus: false,
                            showMaskOnHover: false,
                            showMaskOnFocus: true,
                            autoUnmask: false,
                            onBeforeMask: function(value, opts) {
                                return value.replace(/[^\d,.-]/g, '');
                            }
                        }).mask(valorInput);
                        valorInput.setAttribute('data-mask-initialized', '1');
                    }
                }

                // Datas
                setDateField('repasse_data_emissao', repasse.data_emissao);
                setDateField('repasse_data_vencimento', repasse.data_vencimento);
                setDateField('repasse_data_entrada', repasse.data_entrada);

                // Competência
                var compInput = document.getElementById('repasse_competencia');
                if (compInput) {
                    compInput.value = repasse.competencia || '';
                    if (compInput._flatpickr) {
                        compInput._flatpickr.setDate(repasse.competencia || '', false);
                    }
                }

                // Forma de recebimento
                var frSelect = document.getElementById('repasse_forma_recebimento');
                if (frSelect) {
                    $(frSelect).val(repasse.forma_recebimento_id).trigger('change');
                }

                // Nº Documento
                var numDocInput = document.getElementById('repasse_numero_documento');
                if (numDocInput) {
                    numDocInput.value = repasse.numero_documento || '';
                }

                // Filial destino (primeiro item)
                if (repasse.itens && repasse.itens.length > 0) {
                    var filialSelect = document.getElementById('repasse_filial_destino');
                    if (filialSelect) {
                        $(filialSelect).val(repasse.itens[0].company_destino_id).trigger('change');
                    }
                }

                // Descrição
                var descArea = document.getElementById('repasse_descricao');
                if (descArea) {
                    descArea.value = repasse.descricao || '';
                }
            }

            function setDateField(inputId, value) {
                var input = document.getElementById(inputId);
                if (!input || !value) return;
                if (input._flatpickr) {
                    // valor vem como dd/mm/yyyy
                    input._flatpickr.setDate(value, false, 'd/m/Y');
                } else {
                    input.value = value;
                }
            }

            function resetForm() {
                var form = document.getElementById('kt_drawer_repasse_form');
                if (form) form.reset();

                // Resetar selects com Select2
                ['repasse_filial_destino', 'repasse_entidade_origem', 'repasse_forma_recebimento'].forEach(function(id) {
                    var el = document.getElementById(id);
                    if (el && $(el).data('select2')) {
                        $(el).val('').trigger('change');
                    }
                });

                // Resetar flatpickr date fields
                ['repasse_data_emissao', 'repasse_data_vencimento', 'repasse_data_entrada'].forEach(function(id) {
                    var el = document.getElementById(id);
                    if (el && el._flatpickr) {
                        el._flatpickr.clear();
                    }
                });

                limparErros();
                atualizarCompetencia();
            }

            // Auto-preencher competência a partir da Data Emissão (mm/yyyy)
            function atualizarCompetencia() {
                var dataEmissao = document.getElementById('repasse_data_emissao');
                var competencia = document.getElementById('repasse_competencia');
                if (!dataEmissao || !competencia) return;

                var valor = dataEmissao.value;
                if (valor && valor.length >= 10) {
                    var partes = valor.split('/');
                    if (partes.length === 3) {
                        competencia.value = partes[1] + '/' + partes[2];
                        return;
                    }
                }
                // Fallback: mês/ano atual
                var hoje = new Date();
                var mes = String(hoje.getMonth() + 1).padStart(2, '0');
                competencia.value = mes + '/' + hoje.getFullYear();
            }

            // Reconfigurar flatpickr do campo competência como seletor mês/ano
            function initCompetenciaDatepicker() {
                var compInput = document.getElementById('repasse_competencia');
                if (!compInput) return;

                // Destruir flatpickr padrão que o x-tenant-date criou
                if (compInput._flatpickr) {
                    compInput._flatpickr.destroy();
                }

                if (typeof flatpickr !== 'undefined') {
                    var config = {
                        dateFormat: 'm/Y',
                        allowInput: true,
                        clickOpens: true
                    };
                    if (flatpickr.l10ns && flatpickr.l10ns.pt) {
                        config.locale = 'pt';
                    }
                    flatpickr(compInput, config);
                }
            }

            // Listeners para data de emissão e inicialização do competência
            document.addEventListener('DOMContentLoaded', function() {
                var dataEmissao = document.getElementById('repasse_data_emissao');
                if (dataEmissao) {
                    dataEmissao.addEventListener('change', atualizarCompetencia);
                }

                // Aguardar o x-tenant-date inicializar e depois reconfigurar
                setTimeout(initCompetenciaDatepicker, 500);

            });

            function carregarFiliais(callback) {
                fetch(URLS.filiais, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(function(r) {
                        return r.json();
                    })
                    .then(function(data) {
                        var select = document.getElementById('repasse_filial_destino');
                        if (!select) return;
                        select.innerHTML = '<option value="">Selecione a filial...</option>';

                        if (data.success && data.filiais) {
                            filiaisDisponiveis = data.filiais;
                            data.filiais.forEach(function(f) {
                                var opt = document.createElement('option');
                                opt.value = f.id;
                                opt.textContent = f.name;
                                select.appendChild(opt);
                            });
                        }
                        if ($(select).data('select2')) {
                            $(select).trigger('change.select2');
                        }
                        if (typeof callback === 'function') callback();
                    })
                    .catch(function(err) {
                        console.error('Erro ao carregar filiais:', err);
                    });
            }

            // Submit
            window.submitRepasse = function() {
                limparErros();
                var btn = document.getElementById('repasse_submit_btn');
                setLoading(btn, true);

                var form = document.getElementById('kt_drawer_repasse_form');
                var formData = new FormData(form);

                // Montar JSON
                var payload = {
                    entidade_origem_id: formData.get('entidade_origem_id'),
                    valor_total: formData.get('valor_total'),
                    data_emissao: formData.get('data_emissao'),
                    data_entrada: formData.get('data_entrada') || null,
                    data_vencimento: formData.get('data_vencimento') || null,
                    competencia: formData.get('competencia') || null,
                    tipo_documento: formData.get('tipo_documento') || null,
                    numero_documento: formData.get('numero_documento') || null,
                    descricao: formData.get('descricao') || null,
                    forma_recebimento_id: formData.get('forma_recebimento_id') || null,
                    itens: []
                };

                // Item único da filial selecionada
                payload.itens = [{
                    company_destino_id: formData.get('company_destino_id') || '',
                    percentual: null,
                    valor: formData.get('valor_total')
                }];

                // Determinar URL e método conforme modo
                var url = URLS.store;
                var method = 'POST';
                if (modoEdicao && repasseEditandoId) {
                    url = URLS.update + '/' + repasseEditandoId;
                    method = 'PUT';
                } else {
                    payload.executar_imediato = formData.get('executar_imediato') === '1';
                }

                fetch(url, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                        },
                        body: JSON.stringify(payload)
                    })
                    .then(function(r) {
                        return r.json().then(function(body) {
                            return {
                                status: r.status,
                                body: body
                            };
                        });
                    })
                    .then(function(res) {
                        setLoading(btn, false);

                        if (res.status >= 200 && res.status < 300 && res.body.success) {
                            // Sucesso
                            if (typeof Swal !== 'undefined') {
                                Swal.fire('Sucesso!', res.body.message, 'success');
                            }

                            // Resetar modo edição
                            modoEdicao = false;
                            repasseEditandoId = null;

                            // Fechar drawer
                            var drawerEl = document.querySelector('#kt_drawer_repasse');
                            if (drawerEl) {
                                var drawer = KTDrawer.getInstance(drawerEl);
                                if (drawer) drawer.hide();
                            }

                            // Recarregar DataTable de repasses
                            var dt = $.fn.DataTable.isDataTable('#kt_repasses_table') ?
                                $('#kt_repasses_table').DataTable() : null;
                            if (dt) dt.ajax.reload();

                            // Recarregar stats
                            if (window.TenantDataTablePane) window.TenantDataTablePane.initAllPanes();

                        } else if (res.body.errors) {
                            // Erros de validação
                            Object.keys(res.body.errors).forEach(function(field) {
                                // Normalizar campos de itens: itens.0.company_destino_id → company_destino_id
                                var normalizado = field.replace(/^itens\.\d+\./, '');
                                mostrarErro(normalizado, res.body.errors[field][0]);
                                // Também tenta o campo original
                                mostrarErro(field, res.body.errors[field][0]);
                            });
                        } else {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire('Erro', res.body.message || 'Erro ao criar repasse.', 'error');
                            }
                        }
                    })
                    .catch(function(err) {
                        setLoading(btn, false);
                        console.error('Erro ao submeter repasse:', err);
                        if (typeof Swal !== 'undefined') {
                            Swal.fire('Erro', 'Falha na comunicação com o servidor.', 'error');
                        }
                    });
            };

            function setLoading(btn, loading) {
                if (!btn) return;
                if (loading) {
                    btn.setAttribute('data-kt-indicator', 'on');
                    btn.disabled = true;
                } else {
                    btn.removeAttribute('data-kt-indicator');
                    btn.disabled = false;
                }
            }

            function parseValorBR(str) {
                if (!str) return 0;
                return parseFloat(str.replace(/\./g, '').replace(',', '.')) || 0;
            }

            function formatarValorBR(num) {
                return parseFloat(num).toLocaleString('pt-BR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            function limparErros() {
                document.querySelectorAll('#kt_drawer_repasse_form .error-message').forEach(function(el) {
                    el.style.display = 'none';
                    el.textContent = '';
                });
            }

            function mostrarErro(field, msg) {
                var el = document.getElementById('error-' + field);
                if (el) {
                    el.textContent = msg;
                    el.style.display = 'block';
                }
            }
        })();
    </script>
@endpush
