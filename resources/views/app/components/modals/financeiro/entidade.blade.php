<!--begin::Modal - Cadastro de Entidade Financeira-->
<div class="modal fade" id="kt_modal_entidade_financeira" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered mw-750px">
        <!--begin::Modal content-->
        <div class="modal-content rounded">
            <!--begin:Form-->
            <form id="kt_modal_entidade_financeira_form" class="form" method="POST"
                action="{{ route('entidades.store') }}">
                @csrf
                <!--begin::Modal header-->
                <div class="modal-header justify-content-between mb-10">
                    <h3 class="modal-title fw-bold">Cadastrar Nova Entidade Financeira</h3>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <span class="svg-icon svg-icon-1">
                            <i class="bi bi-x-lg fs-3"></i>
                        </span>
                    </div>
                </div>
                <!--end::Modal header-->

                <!--begin::Modal body-->
                <div class="modal-body scroll-y px-10 px-lg-15 pt-0 pb-15">
                    <div class="row mb-5">
                        <!--begin::Col-->
                        <div class="col-md-4 fv-row">
                            <label class="fs-5 fw-semibold mb-2">Tipo</label>
                            <select name="tipo" id="tipo" class="form-select form-select-solid" required>
                                <option value="" disabled selected>Selecione o tipo</option>
                                <option value="caixa" {{ old('tipo') == 'caixa' ? 'selected' : '' }}>Caixa</option>
                                <option value="banco" {{ old('tipo') == 'banco' ? 'selected' : '' }}>Banco</option>
                            </select>
                            @error('tipo')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                        <!--end::Col-->

                        <!--begin::Col-->
                        <div class="col-md-8 fv-row" id="nome-entidade-group">
                            <label class="fs-5 fw-semibold mb-2">Nome da Entidade</label>
                            <input type="text" class="form-control form-control-solid"
                                placeholder="Ex: Caixa Central" name="nome"
                                value="{{ old('nome') }}" />
                            @error('nome')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                        <!--end::Col-->

                        <!-- Campos para Banco (inicialmente ocultos) -->
                        <div class="col-md-8 fv-row d-none" id="banco-group">
                            <label class="fs-5 fw-semibold mb-2">Banco</label>
                            <select id="banco-select" name="bank_id"
                                class="form-select form-select-solid" data-control="select2"
                                data-dropdown-parent="#kt_modal_entidade_financeira"
                                data-placeholder="Selecione um banco">
                                <option></option>
                                @isset($banks)
                                    @foreach ($banks as $bank)
                                        <option value="{{ $bank->id }}"
                                            data-icon="{{ $bank->logo_path }}">
                                            {{ $bank->name }}
                                        </option>
                                    @endforeach
                                @endisset
                            </select>
                            @error('bank_id')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!--begin::Input group-->
                    <div class="row mb-5">
                        <div class="col-md-4 fv-row d-none" id="agencia-group">
                            <label class="fs-5 fw-semibold mb-2">Agência</label>
                            <input type="text" class="form-control form-control-solid"
                                placeholder="Número da agência" name="agencia"
                                value="{{ old('agencia') }}" />
                            @error('agencia')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 fv-row d-none" id="conta-group">
                            <label class="fs-5 fw-semibold mb-2">Conta</label>
                            <input type="text" class="form-control form-control-solid"
                                placeholder="Número da conta" name="conta"
                                value="{{ old('conta') }}" />
                            @error('conta')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 fv-row d-none" id="account-type-group">
                            <label class="fs-5 fw-semibold mb-2">Natureza da Conta</label>
                            <select name="account_type" id="account_type"
                                class="form-select form-select-solid">
                                <option value="" disabled selected>Selecione a natureza</option>
                                <option value="corrente" {{ old('account_type') == 'corrente' ? 'selected' : '' }}>Conta Corrente</option>
                                <option value="poupanca" {{ old('account_type') == 'poupanca' ? 'selected' : '' }}>Poupança</option>
                                <option value="aplicacao" {{ old('account_type') == 'aplicacao' ? 'selected' : '' }}>Aplicação</option>
                                <option value="renda_fixa" {{ old('account_type') == 'renda_fixa' ? 'selected' : '' }}>Renda Fixa</option>
                                <option value="tesouro_direto" {{ old('account_type') == 'tesouro_direto' ? 'selected' : '' }}>Tesouro Direto</option>
                            </select>
                            @error('account_type')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!--begin::Linha Saldo Inicial / Saldo Atual-->
                    <div class="row mb-5">
                        <!--begin::Col-->
                        <div class="col-md-6 fv-row">
                            <!--begin::Label-->
                            <label class="fs-5 fw-semibold mb-2">Saldo Inicial</label>
                            <!--end::Label-->
                            <div class="position-relative d-flex align-items-center">
                                <!--begin::Icon-->
                                <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                    <svg class="icon icon-tabler icon-tabler-currency-real"
                                        fill="none" height="24" stroke="currentColor"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" viewBox="0 0 24 24" width="24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path d="M0 0h24v24H0z" fill="none" stroke="none"></path>
                                        <path d="M21 6h-4a3 3 0 0 0 0 6h1a3 3 0 0 1 0 6h-4"></path>
                                        <path d="M4 18v-12h3a3 3 0 1 1 0 6h-3c5.5 0 5 4 6 6"></path>
                                        <path d="M18 6v-2"></path>
                                        <path d="M17 20v-2"></path>
                                    </svg>
                                </span>
                                <!--end::Icon-->
                                <!--begin::Input-->
                                <input type="text"
                                    class="form-control form-control-solid ps-12 money"
                                    placeholder="Ex: 1.000,00" id="saldo_inicial_input"
                                    name="saldo_inicial" required />
                                <!--end::Input-->
                                @error('saldo_inicial')
                                    <div class="text-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!--end::Col-->

                        <!--begin::Col-->
                        <div class="col-md-6 fv-row">
                            <!--begin::Label-->
                            <label class="fs-5 fw-semibold mb-2">Saldo Atual</label>
                            <!--end::Label-->
                            <div class="position-relative d-flex align-items-center">
                                <!--begin::Icon-->
                                <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                    <svg class="icon icon-tabler icon-tabler-currency-real"
                                        fill="none" height="24" stroke="currentColor"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" viewBox="0 0 24 24" width="24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path d="M0 0h24v24H0z" fill="none" stroke="none"></path>
                                        <path d="M21 6h-4a3 3 0 0 0 0 6h1a3 3 0 0 1 0 6h-4"></path>
                                        <path d="M4 18v-12h3a3 3 0 1 1 0 6h-3c5.5 0 5 4 6 6"></path>
                                        <path d="M18 6v-2"></path>
                                        <path d="M17 20v-2"></path>
                                    </svg>
                                </span>
                                <!--end::Icon-->
                                <!--begin::Input-->
                                <input type="text"
                                    class="form-control form-control-solid ps-12 money"
                                    placeholder="Ex: 1.000,00" id="saldo_atual_input" name="saldo_atual"
                                    disabled />
                                <!--end::Input-->
                                @error('saldo_atual')
                                    <div class="text-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!--end::Col-->
                    </div>
                    <!--end::Linha Saldo-->

                    <!--begin::Descrição-->
                    <div class="d-flex flex-column mb-5 fv-row">
                        <label class="fs-5 fw-semibold mb-2">Descrição</label>
                        <textarea class="form-control form-control-solid" rows="4" name="descricao"
                            placeholder="Insira uma descrição (opcional)">{{ old('descricao') }}</textarea>
                        @error('descricao')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                    <!--end::Descrição-->

                    <!--begin::Conta Contábil-->
                    <div class="d-flex flex-column mb-5 fv-row">
                        <label class="fs-5 fw-semibold mb-2">Conta Contábil (Plano de Contas)</label>
                        <select class="form-select form-select-solid" data-control="select2"
                            data-dropdown-parent="#kt_modal_entidade_financeira"
                            data-placeholder="Selecione a conta contábil..." name="conta_contabil_id"
                            id="conta_contabil_id">
                            <option></option>
                            @isset($contas)
                                @foreach ($contas as $conta)
                                    <option value="{{ $conta->id }}" {{ old('conta_contabil_id') == $conta->id ? 'selected' : '' }}>
                                        {{ $conta->code }} - {{ $conta->name }}
                                    </option>
                                @endforeach
                            @endisset
                        </select>
                        <div class="text-muted fs-7 mt-2">Vínculo contábil para exportação (De/Para)</div>
                        @error('conta_contabil_id')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                    <!--end::Conta Contábil-->

                    <!--begin::Notice-->
                    <div
                        class="notice d-flex bg-light-primary rounded border-primary border border-dashed mb-9 p-6">
                        <span class="svg-icon svg-icon-2tx svg-icon-primary me-4">
                            <svg width="24" height="24" viewBox="0 0 24 24"
                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.3" d="M3.20001 5.91897L16.9 3.01895C17.4 2.91895
                                    18 3.219 18.1 3.819L19.2 9.01895L3.20001 5.91897Z"
                                    fill="currentColor" />
                                <path opacity="0.3" d="M13 13.9189C13 12.2189 14.3 10.9189
                                    16 10.9189H21C21.6 10.9189 22 11.3189 22
                                    11.9189V15.9189C22 16.5189 21.6 16.9189
                                    21 16.9189H16C14.3 16.9189 13 15.6189
                                    13 13.9189ZM16 12.4189C15.2 12.4189 14.5
                                    13.1189 14.5 13.9189C14.5 14.7189 15.2
                                    15.4189 16 15.4189C16.8 15.4189 17.5
                                    14.7189 17.5 13.9189C17.5 13.1189 16.8
                                    12.4189 16 12.4189Z" fill="currentColor" />
                                <path d="M13 13.9189C13 12.2189 14.3 10.9189
                                16 10.9189H21V7.91895C21 6.81895 20.1
                                5.91895 19 5.91895H3C2.4 5.91895
                                2 6.31895 2 6.91895V20.9189C2
                                21.5189 2.4 21.9189 3
                                21.9189H19C20.1 21.9189 21
                                21.0189 21 19.9189V16.9189H16C14.3
                                16.9189 13 15.6189 13 13.9189Z" fill="currentColor" />
                            </svg>
                        </span>
                        <div class="d-flex flex-stack flex-grow-1">
                            <div class="fw-semibold">
                                <h4 class="text-gray-900 fw-bold">Dica</h4>
                                <div class="fs-6 text-gray-700">
                                    Certifique-se de preencher corretamente todos os campos
                                    obrigatórios.
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Notice-->

                </div>
                <!--end::Modal body-->

                <!--begin::Actions-->
                <div class="modal-footer flex-center">
                    <button type="reset" data-bs-dismiss="modal" class="btn btn-sm btn-light me-3">
                        <i class="bi bi-x-lg me-2"></i> Cancelar
                    </button>
                    <button type="submit" id="kt_modal_entidade_financeira_submit" class="btn btn-sm btn-primary">
                        <span class="indicator-label"> <i class="bi bi-save me-2"></i> Salvar</span>
                        <span class="indicator-progress">Aguarde...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button>
                </div>
                <!--end::Actions-->
            </form>
            <!--end:Form-->
        </div>
        <!--end::Modal content-->
    </div>
    <!--end::Modal dialog-->
</div>
<!--end::Modal - Cadastro de Entidade Financeira-->

<!--begin::Script para lógica do formulário-->
<script>
    (function() {
        'use strict';

        // Função para inicializar campos dinâmicos
        function initDynamicFields() {
            const tipoSelect = document.getElementById('tipo');
            const nomeEntidadeGroup = document.getElementById('nome-entidade-group');
            const bancoGroup = document.getElementById('banco-group');
            const agenciaGroup = document.getElementById('agencia-group');
            const contaGroup = document.getElementById('conta-group');
            const accountTypeGroup = document.getElementById('account-type-group');

            if (!tipoSelect) return;

            // Função para exibir/esconder campos
            function toggleFields() {
                const selected = tipoSelect.value;
                if (selected === 'banco') {
                    nomeEntidadeGroup?.classList.add('d-none');
                    bancoGroup?.classList.remove('d-none');
                    agenciaGroup?.classList.remove('d-none');
                    contaGroup?.classList.remove('d-none');
                    accountTypeGroup?.classList.remove('d-none');
                } else {
                    nomeEntidadeGroup?.classList.remove('d-none');
                    bancoGroup?.classList.add('d-none');
                    agenciaGroup?.classList.add('d-none');
                    contaGroup?.classList.add('d-none');
                    accountTypeGroup?.classList.add('d-none');
                }
            }

            // Evento de mudança no select "tipo"
            tipoSelect.addEventListener('change', toggleFields);

            // Ao carregar, verificar se já tem valor selecionado
            toggleFields();
        }

        // Função para inicializar Select2 do banco
        function initBancoSelect2() {
            if (typeof $ === 'undefined' || !$.fn.select2) {
                return;
            }

            const bancoSelect = document.getElementById('banco-select');
            if (!bancoSelect) return;

            // Verificar se já está inicializado
            if ($(bancoSelect).hasClass('select2-hidden-accessible')) {
                return;
            }

            $(bancoSelect).select2({
                dropdownParent: $('#kt_modal_entidade_financeira'),
                placeholder: "Selecione um banco",
                allowClear: true,
                templateResult: function(state) {
                    if (!state.id) {
                        return state.text;
                    }
                    let iconUrl = $(state.element).attr('data-icon');
                    if (!iconUrl) {
                        return state.text;
                    }
                    let $state = $(`
                        <span class="d-flex align-items-center">
                            <img src="${iconUrl}" class="me-2" style="width:24px; height:24px;" />
                            <span>${state.text}</span>
                        </span>
                    `);
                    return $state;
                },
                templateSelection: function(state) {
                    if (!state.id) {
                        return state.text;
                    }
                    let iconUrl = $(state.element).attr('data-icon');
                    if (!iconUrl) {
                        return state.text;
                    }
                    let $state = $(`
                        <span class="d-flex align-items-center">
                            <img src="${iconUrl}" class="me-2" style="width:24px; height:24px;" />
                            <span>${state.text}</span>
                        </span>
                    `);
                    return $state;
                },
            });
        }

        // Função para inicializar máscara de dinheiro
        function initMoneyMask() {
            if (typeof Inputmask === 'undefined') {
                return;
            }

            const saldoInicialInput = document.getElementById('saldo_inicial_input');
            if (saldoInicialInput) {
                Inputmask('decimal', {
                    alias: 'numeric',
                    groupSeparator: '.',
                    radixPoint: ',',
                    digits: 2,
                    autoGroup: true,
                    rightAlign: false,
                    oncleared: function() {
                        this.value = '';
                    }
                }).mask(saldoInicialInput);
            }
        }

        // Função para resetar formulário
        function resetForm() {
            const form = document.getElementById('kt_modal_entidade_financeira_form');
            if (form) {
                form.reset();

                // Resetar Select2
                if (typeof $ !== 'undefined' && $.fn.select2) {
                    $('#banco-select').val(null).trigger('change');
                    $('#conta_contabil_id').val(null).trigger('change');
                }

                // Resetar campos dinâmicos
                initDynamicFields();
            }
        }

        // Inicializar quando o modal for aberto
        const modal = document.getElementById('kt_modal_entidade_financeira');
        if (modal) {
            modal.addEventListener('shown.bs.modal', function() {
                initDynamicFields();
                initBancoSelect2();
                initMoneyMask();
            });

            modal.addEventListener('hidden.bs.modal', function() {
                resetForm();
            });
        }

        // Inicializar na carga da página se o modal já estiver visível
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                initDynamicFields();
            });
        } else {
            initDynamicFields();
        }
    })();
</script>
<!--end::Script para lógica do formulário-->

