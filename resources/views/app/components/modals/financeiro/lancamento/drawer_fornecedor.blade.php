<!--begin::Drawer - Novo Fornecedor-->
<style>
    #kt_drawer_fornecedor {
        z-index: 1070 !important;
    }

    #kt_drawer_fornecedor .drawer-overlay {
        z-index: 1065 !important;
    }

    /* Ensure inputs in drawer are clickable */
    #kt_drawer_fornecedor input,
    #kt_drawer_fornecedor button,
    #kt_drawer_fornecedor select,
    #kt_drawer_fornecedor textarea {
        pointer-events: auto !important;
    }
</style>
<div class="rounded-4 " id="kt_drawer_fornecedor" data-kt-drawer="true" data-kt-drawer-activate="true"
    data-kt-drawer-toggle="#kt_drawer_fornecedor_button" data-kt-drawer-close="#kt_drawer_fornecedor_close"
    data-kt-drawer-width="750px" tabindex="0">


    <div class="card shadow-sm w-100">
        <!--begin::Header-->
        <div class="card-header pe-5">
            <!--begin::Title-->
            <div class="card-title">
                <h3 class="fw-bold m-0" id="fornecedor_drawer_title">Novo Pessoa</h3>
            </div>
            <!--end::Title-->

            <!--begin::Toolbar-->
            <div class="card-toolbar">
                <button type="button" class="btn btn-sm btn-icon btn-active-color-primary"
                    id="kt_drawer_fornecedor_close">
                    <i class="bi bi-x fs-2">
                    </i>
                </button>
            </div>
            <!--end::Toolbar-->
        </div>
        <!--end::Header-->
        <div class="card-body drawer-body py-10 px-lg-17">

            <form id="kt_drawer_fornecedor_form">
                @csrf
                <meta name="csrf-token" content="{{ csrf_token() }}">
                
                {{-- Hidden field to store tipo (fornecedor/cliente) --}}
                <input type="hidden" id="parceiro_tipo_hidden" name="tipo" value="fornecedor">

                <!--begin::Scroll-->
                <!--begin::Notice-->
                <!--end::Notice-->
                <!--begin::Input group-->
                <div class="row mb-5">
                    <!--begin::Col-->
                    <div class="col-md-4 fv-row">
                        <!--begin::Label-->
                        <!--begin::Label-->
                        <label class="d-flex align-items-center fs-5 fw-semibold mb-2">
                            <span class="required">Tipo de Fornecedor</span>
                            <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                                title="Your payment statements may very based on selected country"></i>
                        </label>
                        <!--end::Label-->
                        <!--begin::Select-->
                        <select id="fornecedor_tipo" name="fornecedor_tipo" data-control="select2"
                            data-dropdown-parent="#kt_drawer_fornecedor"
                            data-placeholder="Escolha um Tipo de Fornecedor..." class="form-select">
                            <option value="">Escolha um Tipo de Fornecedor...</option>
                            <option value="1">Pessoa Física</option>
                            <option value="2">Pessoa Jurídica</option>

                        </select>
                        <!--end::Select-->
                    </div>
                    <!--end::Col-->
                    <!--begin::Col - CPF (Pessoa Física)-->
                    <div class="col-md-8 fv-row" id="cpf_container" style="display: none;">
                        <!--begin::Label-->
                        <label class="required fs-5 mb-2">CPF</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <input class="form-control" placeholder="000.000.000-00" name="cpf" id="cpf" />
                        <!--end::Input-->
                    </div>
                    <!--end::Col-->
                    <!--begin::Col - CNPJ (Pessoa Jurídica)-->
                    <div class="col-md-8 fv-row" id="cnpj_container" style="display: none;">
                        <!--begin::Label-->
                        <label class="required fs-5 mb-2">CNPJ</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <div class="input-group">
                            <input type="text" class="form-control " name="cnpj" id="cnpj"
                                placeholder="00.000.000/0000-00" />
                            <button type="button" class="btn btn-light-primary" id="btn-consultar-cnpj">
                                <i class="bi bi-search"></i> Consultar
                            </button>
                        </div>
                        <!--end::Input-->
                    </div>
                    <!--end::Col-->
                </div>
                <!--end::Input group-->
                <!--begin::Input group-->
                <div class="d-flex flex-column mb-5 fv-row " style="display: none;" id="nome_fantasia_container">
                    <!--begin::Label-->
                    <label class="required fs-5 fw-semibold mb-2">Nome Fantasia</label>
                    <!--end::Label-->
                    <!--begin::Input-->
                    <input class="form-control" placeholder="Digite o nome fantasia do fornecedor" name="nome_fantasia"
                        id="nome_fantasia" />
                    <!--end::Input-->
                </div>
                <!--end::Input group-->
                <!--begin::Input group-->
                <div class="d-flex flex-column mb-5 fv-row" style="display: none;" id="nome_completo_container">
                    <!--begin::Label-->
                    <label class="required fs-5 fw-semibold mb-2">Nome Completo </label>
                    <!--end::Label-->
                    <!--begin::Input-->
                    <input class="form-control" placeholder="Digite o nome completo do fornecedor" name="nome_completo"
                        id="nome_completo" />
                    <!--end::Input-->
                </div>
                <!--end::Input group-->
                <!--begin::Input group-->
                <div class="row">
                    <!--begin::Col-->
                    <div class="col-md-4 fv-row">
                        <!--begin::Label-->
                        <!--begin::Input group-->
                        <div class="mb-10">
                            <x-tenant-label for="fornecedor_telefone">Telefone</x-tenant-label>
                            <x-tenant-input name="telefone" id="fornecedor_telefone" placeholder="(00) 00000-0000"
                                class="" />
                        </div>
                        <!--end::Input group-->
                        <!--end::Col-->
                    </div>
                    <!--begin::Col-->
                    <div class="col-md-8 fv-row">
                        <!--begin::Label-->
                        <div class="mb-10">
                            <x-tenant-label for="fornecedor_email">E-mail</x-tenant-label>
                            <x-tenant-input name="email" id="fornecedor_email" type="email"
                                placeholder="exemplo@email.com" class="" />
                        </div>
                    </div>
                    <!--end::Col-->
                </div>

                <div class="separator separator-dotted border-2 my-10"></div>


                <!--begin::Billing toggle-->
                <div class="fw-bold fs-3 rotate collapsible mb-7" data-bs-toggle="collapse"
                    href="#kt_modal_add_customer_billing_info" role="button" aria-expanded="false"
                    aria-controls="kt_customer_view_details">Endereço
                    <span class="ms-2 rotate-180">
                        <!--begin::Svg Icon | path: icons/duotune/arrows/arr072.svg-->
                        <span class="svg-icon svg-icon-3">
                            <i class="bi bi-chevron-down rotate-180"></i>
                        </span>
                        <!--end::Svg Icon-->
                    </span>
                </div>
                <!--end::Billing toggle-->
                <!--begin::Billing form-->
                <div id="kt_modal_add_customer_billing_info" class="collapse show">
                    <!--begin::Input group-->
                    <div class="d-flex flex-column mb-7 fv-row">
                        <!--begin::Label-->
                        <label class="required fs-6 fw-semibold mb-2">Endereço</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <input class="form-control" placeholder="" name="address1" value="101, Collins Street" />
                        <!--end::Input-->
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group-->
                    <div class="row g-9 mb-7">
                        <!--begin::Col-->
                        <div class="col-md-4 fv-row">
                            <!--begin::Label-->
                            <label class="required fs-6 fw-semibold mb-2">CEP</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input class="form-control" placeholder="" name="cep" id="cep"
                                value="" />
                            <!--end::Input-->
                        </div>
                        <!--end::Col-->
                        <!--begin::Col-->
                        <div class="col-md-8 fv-row">
                            <!--begin::Label-->
                            <label class="required fs-6 fw-semibold mb-2">Rua</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input class="form-control" placeholder="" name="city" value="Melbourne" />
                            <!--end::Input-->
                        </div>
                        <!--end::Col-->

                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group-->
                    <div class="row g-9 mb-7">
                        <!--begin::Col-->
                        <div class="col-md-8 fv-row">
                            <!--begin::Label-->
                            <label class="required fs-6 fw-semibold mb-2">Bairro</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input class="form-control" placeholder="" name="bairro" id="bairro"
                                value="" />
                            <!--end::Input-->
                        </div>
                        <!--end::Col-->
                        <!--begin::Col-->
                        <div class="col-md-4 fv-row">
                            <!--begin::Label-->
                            <label class="required fs-6 fw-semibold mb-2">Número</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input class="form-control" placeholder="" name="numero" id="numero"
                                value="" />
                            <!--end::Input-->
                        </div>
                        <!--end::Col-->

                    </div>
                    <!--end::Input group-->

                    <!--begin::Input group-->
                    <div class="d-flex flex-column mb-7 fv-row">
                        <!--begin::Label-->
                        <label class="fs-6 fw-semibold mb-2">
                            <span class="required">Estado</span>
                            <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                title="Country of origination"></i>
                        </label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <select name="country" aria-label="Select a Country" data-control="select2"
                            data-placeholder="Select a Country..." data-dropdown-parent="#kt_drawer_fornecedor"
                            class="form-select">
                            <option value="">Selecione um estado...</option>
                            <option value="AC">Acre</option>
                            <option value="AL">Alagoas</option>
                            <option value="AP">Amapá</option>
                            <option value="AM">Amazonas</option>
                            <option value="BA">Bahia</option>
                            <option value="CE">Ceará</option>
                            <option value="DF">Distrito Federal</option>
                            <option value="ES">Espírito Santo</option>
                            <option value="GO">Goiás</option>
                            <option value="MA">Maranhão</option>
                            <option value="MT">Mato Grosso</option>
                            <option value="MS">Mato Grosso do Sul</option>
                            <option value="MG">Minas Gerais</option>
                            <option value="PA">Pará</option>
                            <option value="PB">Paraíba</option>
                            <option value="PR">Paraná</option>
                            <option value="PE">Pernambuco</option>
                            <option value="PI">Piauí</option>
                            <option value="RJ">Rio de Janeiro</option>
                            <option value="RN">Rio Grande do Norte</option>
                            <option value="RS">Rio Grande do Sul</option>
                            <option value="RO">Rondônia</option>
                            <option value="RR">Roraima</option>
                            <option value="SC">Santa Catarina</option>
                            <option value="SP">São Paulo</option>
                            <option value="SE">Sergipe</option>
                            <option value="TO">Tocantins</option>
                        </select>
                        <!--end::Input-->
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group-->
                    <div class="fv-row mb-7">
                        <!--begin::Wrapper-->
                        <div class="d-flex flex-stack">
                            <!--begin::Label-->
                            <div class="me-5">
                                <!--begin::Label-->
                                <label class="fs-6 fw-semibold">Use as a billing adderess?</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <div class="fs-7 fw-semibold text-muted">If you need more info, please check budget
                                    planning</div>
                                <!--end::Input-->
                            </div>
                            <!--end::Label-->
                            <!--begin::Switch-->
                            <label class="form-check form-switch form-check-custom form-check-solid">
                                <!--begin::Input-->
                                <input class="form-check-input" name="billing" type="checkbox" value="1"
                                    id="kt_modal_add_customer_billing" checked="checked" />
                                <!--end::Input-->
                                <!--begin::Label-->
                                <span class="form-check-label fw-semibold text-muted"
                                    for="kt_modal_add_customer_billing">Yes</span>
                                <!--end::Label-->
                            </label>
                            <!--end::Switch-->
                        </div>
                        <!--begin::Wrapper-->
                    </div>
                    <!--end::Input group-->
                </div>
                <!--end::Billing form-->


        </div>
        <!--end::Body-->
        </form>
        <!--begin::Actions-->
        <div class="card-footer">
            <button type="button" class="btn btn-sm btn-light" id="kt_drawer_fornecedor_cancel">
                
                Cancelar
            </button>
            <button type="submit" class="btn btn-sm btn-primary" id="kt_drawer_fornecedor_submit" form="kt_drawer_fornecedor_form">
                <span class="indicator-label">
                    Salvar
                </span>
                <span class="indicator-progress">
                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                    Aguarde...
                </span>
            </button>
        </div>
        <!--end::Actions-->


    </div>
</div>
<!--end::Drawer - Novo Fornecedor-->



@push('scripts')
<script>
    (function() {
        // Função de toggle definida fora para manter referência estável
        var toggleDocumentFieldsHandler = null;

        function initToggleDocumentFields() {
            const tipoSelect = document.getElementById('fornecedor_tipo');
            const cpfContainer = document.getElementById('cpf_container');
            const cnpjContainer = document.getElementById('cnpj_container');
            const cpfInput = document.getElementById('cpf');
            const cnpjInput = document.getElementById('cnpj');
            const nomeFantasiaContainer = document.getElementById('nome_fantasia_container');
            const nomeCompletoContainer = document.getElementById('nome_completo_container');
            const nomeFantasiaInput = document.getElementById('nome_fantasia');
            const nomeCompletoInput = document.getElementById('nome_completo');

            if (!tipoSelect) {
                console.warn('Select fornecedor_tipo não encontrado');
                return;
            }

            // Verifica se os containers foram encontrados
            if (!cpfContainer) console.warn('Container CPF não encontrado');
            if (!cnpjContainer) console.warn('Container CNPJ não encontrado');

            // Define a função de toggle uma única vez (mantém referência estável)
            // A função precisa acessar os elementos do escopo atual, então usa closure
            if (!toggleDocumentFieldsHandler) {
                toggleDocumentFieldsHandler = function() {
                    // Obtém os elementos novamente para garantir que estão atualizados
                    var tipoSelectEl = document.getElementById('fornecedor_tipo');
                    var cpfContainerEl = document.getElementById('cpf_container');
                    var cnpjContainerEl = document.getElementById('cnpj_container');
                    var cpfInputEl = document.getElementById('cpf');
                    var cnpjInputEl = document.getElementById('cnpj');
                    var nomeFantasiaContainerEl = document.getElementById('nome_fantasia_container');
                    var nomeCompletoContainerEl = document.getElementById('nome_completo_container');
                    var nomeFantasiaInputEl = document.getElementById('nome_fantasia');
                    var nomeCompletoInputEl = document.getElementById('nome_completo');

                    if (!tipoSelectEl) return;

                    // Obtém o valor do select (pode ser Select2 ou nativo)
                    let selectedValue;
                    if (typeof $ !== 'undefined' && $.fn.select2 && $(tipoSelectEl).hasClass(
                            'select2-hidden-accessible')) {
                        selectedValue = $(tipoSelectEl).val();
                    } else {
                        selectedValue = tipoSelectEl.value;
                    }

                    // Converte para string para comparação consistente
                    selectedValue = String(selectedValue);

                    // Usa jQuery se disponível para garantir que os elementos sejam encontrados
                    var $cpfContainer = cpfContainerEl ? (typeof $ !== 'undefined' ? $(cpfContainerEl) : null) :
                        null;
                    var $cnpjContainer = cnpjContainerEl ? (typeof $ !== 'undefined' ? $(cnpjContainerEl) :
                        null) : null;
                    var $nomeFantasiaContainer = nomeFantasiaContainerEl ? (typeof $ !== 'undefined' ? $(
                        nomeFantasiaContainerEl) : null) : null;
                    var $nomeCompletoContainer = nomeCompletoContainerEl ? (typeof $ !== 'undefined' ? $(
                        nomeCompletoContainerEl) : null) : null;

                    if (selectedValue === '1') {
                        // Pessoa Física - Mostra CPF, oculta CNPJ
                        if ($cpfContainer && $cpfContainer.length) {
                            $cpfContainer.show().removeClass('d-none');
                        } else if (cpfContainerEl) {
                            cpfContainerEl.style.display = 'block';
                            cpfContainerEl.classList.remove('d-none');
                        }

                        if ($cnpjContainer && $cnpjContainer.length) {
                            $cnpjContainer.hide().addClass('d-none');
                        } else if (cnpjContainerEl) {
                            cnpjContainerEl.style.display = 'none';
                            cnpjContainerEl.classList.add('d-none');
                        }

                        if (cpfInputEl) {
                            cpfInputEl.required = true;
                            cpfInputEl.removeAttribute('disabled');
                        }
                        if (cnpjInputEl) {
                            cnpjInputEl.required = false;
                            cnpjInputEl.removeAttribute('required');
                            cnpjInputEl.setAttribute('disabled', 'disabled');
                            cnpjInputEl.value = ''; // Limpa valor quando oculto
                        }

                        // Nome Completo (Exibe)
                        if ($nomeCompletoContainer && $nomeCompletoContainer.length) {
                            $nomeCompletoContainer.show().removeClass('d-none');
                        } else if (nomeCompletoContainerEl) {
                            nomeCompletoContainerEl.style.display = 'block';
                            nomeCompletoContainerEl.classList.remove('d-none');
                        }
                        if (nomeCompletoInputEl) {
                            nomeCompletoInputEl.required = true;
                            nomeCompletoInputEl.removeAttribute('disabled');
                        }

                        // Nome Fantasia (Oculta)
                        if ($nomeFantasiaContainer && $nomeFantasiaContainer.length) {
                            $nomeFantasiaContainer.hide().addClass('d-none');
                        } else if (nomeFantasiaContainerEl) {
                            nomeFantasiaContainerEl.style.display = 'none';
                            nomeFantasiaContainerEl.classList.add('d-none');
                        }
                        if (nomeFantasiaInputEl) {
                            nomeFantasiaInputEl.required = false;
                            nomeFantasiaInputEl.removeAttribute('required');
                            nomeFantasiaInputEl.setAttribute('disabled', 'disabled');
                            nomeFantasiaInputEl.value = '';
                        }
                    } else if (selectedValue === '2') {
                        // Pessoa Jurídica - Mostra CNPJ, oculta CPF
                        if ($cpfContainer && $cpfContainer.length) {
                            $cpfContainer.hide().addClass('d-none');
                        } else if (cpfContainerEl) {
                            cpfContainerEl.style.display = 'none';
                            cpfContainerEl.classList.add('d-none');
                        }

                        if ($cnpjContainer && $cnpjContainer.length) {
                            $cnpjContainer.show().removeClass('d-none');
                        } else if (cnpjContainerEl) {
                            cnpjContainerEl.style.display = 'block';
                            cnpjContainerEl.classList.remove('d-none');
                        }

                        if (cpfInputEl) {
                            cpfInputEl.required = false;
                            cpfInputEl.removeAttribute('required');
                            cpfInputEl.setAttribute('disabled', 'disabled');
                            cpfInputEl.value = ''; // Limpa valor quando oculto
                        }
                        if (cnpjInputEl) {
                            cnpjInputEl.required = true;
                            cnpjInputEl.removeAttribute('disabled');
                        }

                        // Nome Completo (Oculta)
                        if ($nomeCompletoContainer && $nomeCompletoContainer.length) {
                            $nomeCompletoContainer.hide().addClass('d-none');
                        } else if (nomeCompletoContainerEl) {
                            nomeCompletoContainerEl.style.display = 'none';
                            nomeCompletoContainerEl.classList.add('d-none');
                        }
                        if (nomeCompletoInputEl) {
                            nomeCompletoInputEl.required = false;
                            nomeCompletoInputEl.removeAttribute('required');
                            nomeCompletoInputEl.setAttribute('disabled', 'disabled');
                            nomeCompletoInputEl.value = '';
                        }

                        // Nome Fantasia (Exibe)
                        if ($nomeFantasiaContainer && $nomeFantasiaContainer.length) {
                            $nomeFantasiaContainer.show().removeClass('d-none');
                        } else if (nomeFantasiaContainerEl) {
                            nomeFantasiaContainerEl.style.display = 'block';
                            nomeFantasiaContainerEl.classList.remove('d-none');
                        }
                        if (nomeFantasiaInputEl) {
                            nomeFantasiaInputEl.required = true;
                            nomeFantasiaInputEl.removeAttribute('disabled');
                        }
                    } else {
                        // Nenhum selecionado - Oculta ambos
                        if ($cpfContainer && $cpfContainer.length) {
                            $cpfContainer.hide().addClass('d-none');
                        } else if (cpfContainerEl) {
                            cpfContainerEl.style.display = 'none';
                            cpfContainerEl.classList.add('d-none');
                        }

                        if ($cnpjContainer && $cnpjContainer.length) {
                            $cnpjContainer.hide().addClass('d-none');
                        } else if (cnpjContainerEl) {
                            cnpjContainerEl.style.display = 'none';
                            cnpjContainerEl.classList.add('d-none');
                        }

                        if (cpfInputEl) {
                            cpfInputEl.required = false;
                            cpfInputEl.removeAttribute('required');
                            cpfInputEl.setAttribute('disabled', 'disabled');
                            cpfInputEl.value = ''; // Limpa valor quando oculto
                        }
                        if (cnpjInputEl) {
                            cnpjInputEl.required = false;
                            cnpjInputEl.removeAttribute('required');
                            cnpjInputEl.setAttribute('disabled', 'disabled');
                            cnpjInputEl.value = ''; // Limpa valor quando oculto
                        }

                        // Oculta Nome Completo
                        if ($nomeCompletoContainer && $nomeCompletoContainer.length) {
                            $nomeCompletoContainer.hide().addClass('d-none');
                        } else if (nomeCompletoContainerEl) {
                            nomeCompletoContainerEl.style.display = 'none';
                            nomeCompletoContainerEl.classList.add('d-none');
                        }
                        if (nomeCompletoInputEl) {
                            nomeCompletoInputEl.required = false;
                            nomeCompletoInputEl.removeAttribute('required');
                            nomeCompletoInputEl.setAttribute('disabled', 'disabled');
                            nomeCompletoInputEl.value = '';
                        }

                        // Oculta Nome Fantasia
                        if ($nomeFantasiaContainer && $nomeFantasiaContainer.length) {
                            $nomeFantasiaContainer.hide().addClass('d-none');
                        } else if (nomeFantasiaContainerEl) {
                            nomeFantasiaContainerEl.style.display = 'none';
                            nomeFantasiaContainerEl.classList.add('d-none');
                        }
                        if (nomeFantasiaInputEl) {
                            nomeFantasiaInputEl.required = false;
                            nomeFantasiaInputEl.removeAttribute('required');
                            nomeFantasiaInputEl.setAttribute('disabled', 'disabled');
                            nomeFantasiaInputEl.value = '';
                        }
                    }
                };
            }

            // Usa a função de toggle estável (mantém referência para poder remover listeners)
            var toggleDocumentFields = toggleDocumentFieldsHandler;

            // Função para inicializar Select2 do tipo de fornecedor
            function initTipoSelect2() {
                if (typeof $ === 'undefined' || !$.fn.select2) {
                    console.warn('jQuery ou Select2 não disponível');
                    return;
                }

                var $tipoSelect = $(tipoSelect);

                // Se já foi inicializado, não reinicializa
                if ($tipoSelect.hasClass('select2-hidden-accessible')) {
                    return;
                }

                // Inicializa Select2
                $tipoSelect.select2({
                    dropdownParent: $('#kt_drawer_fornecedor'),
                    placeholder: 'Escolha um Tipo de Fornecedor...',
                    allowClear: false,
                    minimumResultsForSearch: 0,
                    width: '100%',
                    theme: 'bootstrap5'
                });

                // Dispara toggleDocumentFields após inicialização para garantir estado inicial correto
                setTimeout(function() {
                    toggleDocumentFields();
                }, 100);
            }

            // Função para definir valor padrão baseado no título do drawer
            // Só aplica se o select ainda não tiver valor selecionado
            function defaultFromTitleOnOpen() {
                // Verifica se já há um valor selecionado (não sobrescreve escolha do usuário)
                var currentValue = null;
                if (typeof $ !== 'undefined' && $.fn.select2 && $(tipoSelect).hasClass(
                        'select2-hidden-accessible')) {
                    currentValue = $(tipoSelect).val();
                } else {
                    currentValue = tipoSelect.value;
                }

                // Se já tem valor, não faz nada
                if (currentValue && currentValue !== '') {
                    return;
                }

                // Obtém o título do drawer para determinar o valor padrão
                var drawerTitleElement = document.getElementById('fornecedor_drawer_title');
                var drawerTitle = drawerTitleElement ? drawerTitleElement.textContent.trim() : '';

                // Define valor padrão baseado no título:
                // - "Fornecedor" → Pessoa Jurídica (valor '2') - CNPJ
                // - "Cliente" → Pessoa Física (valor '1') - CPF
                var valorPadrao = null;
                if (drawerTitle.includes('Fornecedor') || drawerTitle.toLowerCase().includes('fornecedor')) {
                    valorPadrao = '2'; // Pessoa Jurídica (CNPJ)
                } else if (drawerTitle.includes('Cliente') || drawerTitle.toLowerCase().includes('cliente')) {
                    valorPadrao = '1'; // Pessoa Física (CPF)
                }

                // Se não conseguiu determinar pelo título, não faz nada
                if (!valorPadrao) {
                    return;
                }

                // Aplica o valor padrão
                if (typeof $ !== 'undefined' && $.fn.select2) {
                    var $tipoSelect = $(tipoSelect);
                    if ($tipoSelect.hasClass('select2-hidden-accessible')) {
                        // Dispara 'change' simples (sem namespace) para garantir que todos os listeners sejam acionados
                        $tipoSelect.val(valorPadrao).trigger('change');
                    } else {
                        tipoSelect.value = valorPadrao;
                        if (toggleDocumentFieldsHandler) {
                            toggleDocumentFieldsHandler();
                        }
                    }
                } else {
                    tipoSelect.value = valorPadrao;
                    if (toggleDocumentFieldsHandler) {
                        toggleDocumentFieldsHandler();
                    }
                }
            }

            // Configura os event listeners PRIMEIRO (antes de inicializar Select2)
            // Usa namespace para facilitar remoção posterior
            if (typeof $ !== 'undefined' && $.fn.select2) {
                var $tipoSelect = $(tipoSelect);

                // Remove TODOS os listeners anteriores usando namespace
                $tipoSelect.off('.fornecedorTipo');

                // Adiciona listener para mudanças no Select2 com namespace
                $tipoSelect.on('change.fornecedorTipo', function() {
                    toggleDocumentFields();
                });

                // Também escuta o evento select2:select com namespace
                $tipoSelect.on('select2:select.fornecedorTipo', function() {
                    setTimeout(toggleDocumentFields, 50);
                });
            } else {
                // Event listener nativo - usa a função estável para poder remover depois
                if (tipoSelect._toggleHandler) {
                    tipoSelect.removeEventListener('change', tipoSelect._toggleHandler);
                }
                tipoSelect._toggleHandler = toggleDocumentFields;
                tipoSelect.addEventListener('change', tipoSelect._toggleHandler);
            }

            // Inicializa Select2 DEPOIS de configurar os listeners
            initTipoSelect2();

            // Aguarda um pouco para garantir que o Select2 foi inicializado
            setTimeout(function() {
                // Aplica valor padrão baseado no título (só se não tiver valor já selecionado)
                defaultFromTitleOnOpen();
                // Verifica estado atual e atualiza campos
                toggleDocumentFields();
            }, 400);
        }

        // Inicializa quando o DOM estiver pronto
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initToggleDocumentFields);
        } else {
            initToggleDocumentFields();
        }

        // Inicializa quando o drawer for aberto (caso seja carregado dinamicamente)
        const drawerElement = document.getElementById('kt_drawer_fornecedor');
        if (drawerElement) {
            // Função para aplicar valor padrão quando o drawer é aberto
            function applyDefaultOnDrawerOpen() {
                // Aguarda para garantir que o título foi atualizado pelo drawer-init.blade.php
                setTimeout(function() {
                    // Só aplica default se o select ainda não tiver valor
                    if (toggleDocumentFieldsHandler) {
                        // Busca elementos novamente
                        var tipoSelectEl = document.getElementById('fornecedor_tipo');
                        if (tipoSelectEl) {
                            var currentValue = null;
                            if (typeof $ !== 'undefined' && $.fn.select2 && $(tipoSelectEl).hasClass(
                                    'select2-hidden-accessible')) {
                                currentValue = $(tipoSelectEl).val();
                            } else {
                                currentValue = tipoSelectEl.value;
                            }

                            // Se não tem valor, aplica default baseado no título
                            if (!currentValue || currentValue === '') {
                                var drawerTitleEl = document.getElementById('fornecedor_drawer_title');
                                var drawerTitle = drawerTitleEl ? drawerTitleEl.textContent.trim() : '';

                                var valorPadrao = null;
                                if (drawerTitle.includes('Fornecedor') || drawerTitle.toLowerCase()
                                    .includes('fornecedor')) {
                                    valorPadrao = '2'; // Pessoa Jurídica (CNPJ)
                                } else if (drawerTitle.includes('Cliente') || drawerTitle.toLowerCase()
                                    .includes('cliente')) {
                                    valorPadrao = '1'; // Pessoa Física (CPF)
                                }

                                if (valorPadrao) {
                                    if (typeof $ !== 'undefined' && $.fn.select2 && $(tipoSelectEl)
                                        .hasClass('select2-hidden-accessible')) {
                                        $(tipoSelectEl).val(valorPadrao).trigger('change');
                                    } else {
                                        tipoSelectEl.value = valorPadrao;
                                        if (toggleDocumentFieldsHandler) {
                                            toggleDocumentFieldsHandler();
                                        }
                                    }
                                }
                            }
                        }
                    }
                }, 400);
            }

            // Usa evento do KTDrawer (Metronic) se disponível
            if (typeof KTDrawer !== 'undefined') {
                drawerElement.addEventListener('kt.drawer.shown', applyDefaultOnDrawerOpen);
            }

            // Observa mudanças no atributo data-kt-drawer-shown (quando drawer é aberto)
            var observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'attributes' && mutation.attributeName ===
                        'data-kt-drawer-shown') {
                        if (drawerElement.getAttribute('data-kt-drawer-shown') === 'true') {
                            applyDefaultOnDrawerOpen();
                        }
                    }
                });
            });

            observer.observe(drawerElement, {
                attributes: true,
                attributeFilter: ['data-kt-drawer-shown']
            });

            // Observa mudanças no título (quando atualizado pelo drawer-init)
            var titleElement = document.getElementById('fornecedor_drawer_title');
            if (titleElement) {
                var titleObserver = new MutationObserver(function() {
                    // Quando o título muda, aplica default se necessário
                    setTimeout(function() {
                        if (toggleDocumentFieldsHandler) {
                            var tipoSelectEl = document.getElementById('fornecedor_tipo');
                            if (tipoSelectEl) {
                                var currentValue = null;
                                if (typeof $ !== 'undefined' && $.fn.select2 && $(tipoSelectEl)
                                    .hasClass('select2-hidden-accessible')) {
                                    currentValue = $(tipoSelectEl).val();
                                } else {
                                    currentValue = tipoSelectEl.value;
                                }

                                // Só aplica se não tiver valor já selecionado
                                if (!currentValue || currentValue === '') {
                                    var drawerTitle = titleElement.textContent.trim();
                                    var valorPadrao = null;
                                    if (drawerTitle.includes('Fornecedor') || drawerTitle
                                        .toLowerCase().includes('fornecedor')) {
                                        valorPadrao = '2';
                                    } else if (drawerTitle.includes('Cliente') || drawerTitle
                                        .toLowerCase().includes('cliente')) {
                                        valorPadrao = '1';
                                    }

                                    if (valorPadrao) {
                                        if (typeof $ !== 'undefined' && $.fn.select2 && $(
                                                tipoSelectEl).hasClass(
                                                'select2-hidden-accessible')) {
                                            // Dispara 'change' simples para garantir que todos os listeners sejam acionados
                                            $(tipoSelectEl).val(valorPadrao).trigger('change');
                                        } else {
                                            tipoSelectEl.value = valorPadrao;
                                            if (toggleDocumentFieldsHandler) {
                                                toggleDocumentFieldsHandler();
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }, 200);
                });
                titleObserver.observe(titleElement, {
                    childList: true,
                    characterData: true,
                    subtree: true
                });
            }
        }
    })();

    document.addEventListener('DOMContentLoaded', function() {
        const btnConsultar = document.getElementById('btn-consultar-cnpj');
        const inputCnpj = document.getElementById('cnpj');

        if (btnConsultar) {
            btnConsultar.addEventListener('click', function() {
                const cnpj = inputCnpj.value.replace(/\D/g, '');

                if (cnpj.length !== 14) {
                    toastr.warning('Por favor, digite um CNPJ válido com 14 dígitos.');
                    return;
                }

                // Feedback visual de carregamento
                const originalText = btnConsultar.innerHTML;
                btnConsultar.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Consultando...';
                btnConsultar.disabled = true;

                fetch('{{ route("company.consultar-cnpj") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ cnpj: cnpj })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erro na consulta');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.error) {
                        toastr.error(data.error);
                        return;
                    }

                    // Preencher campos
                    // Razão Social (#razao_social) -> Nome Completo (#nome_completo)
                    if (document.getElementById('nome_completo')) {
                        document.getElementById('nome_completo').value = data.razao_social || '';
                    } else if (document.querySelector('input[name="razao_social"]')) {
                        document.querySelector('input[name="razao_social"]').value = data.razao_social || '';
                    }

                    // Nome Fantasia (#name) -> Nome Fantasia (#nome_fantasia)
                    if (document.getElementById('nome_fantasia')) {
                        document.getElementById('nome_fantasia').value = data.nome_fantasia || data.razao_social || '';
                    } else if (document.querySelector('input[name="name"]')) {
                        document.querySelector('input[name="name"]').value = data.nome_fantasia || data.razao_social || '';
                    }
                    
                    // Preenche o nome geral também se vazio
                    if (document.getElementById('fornecedor_nome')) {
                        if (!document.getElementById('fornecedor_nome').value) {
                             document.getElementById('fornecedor_nome').value = data.nome_fantasia || data.razao_social || '';
                        }
                    }

                    // E-mail (#email) -> #fornecedor_email
                    if (data.email) {
                        if (document.getElementById('fornecedor_email')) {
                            document.getElementById('fornecedor_email').value = data.email;
                        } else if (document.getElementById('email')) {
                            document.getElementById('email').value = data.email;
                        }
                    }

                    // Telefone
                    if (data.telefone) {
                         if (document.getElementById('fornecedor_telefone')) {
                            document.getElementById('fornecedor_telefone').value = data.telefone;
                        }
                    }

                    // Endereço
                    if (document.getElementById('cep')) document.getElementById('cep').value = data.cep || '';
                    // logradouro -> address1
                    if (document.querySelector('input[name="address1"]')) {
                        document.querySelector('input[name="address1"]').value = data.logradouro || '';
                    } else if (document.getElementById('logradouro')) {
                        document.getElementById('logradouro').value = data.logradouro || '';
                    }
                    
                    if (document.getElementById('numero')) document.getElementById('numero').value = data.numero || '';
                    if (document.getElementById('bairro')) document.getElementById('bairro').value = data.bairro || '';
                    
                    // localidade -> city
                    if (document.querySelector('input[name="city"]')) {
                        document.querySelector('input[name="city"]').value = data.municipio || '';
                    } else if (document.getElementById('localidade')) {
                        document.getElementById('localidade').value = data.municipio || '';
                    }

                    // Estado (Select2)
                    if (data.uf) {
                        // Tenta encontrar select por name=country ou name=uf
                        const selectCountry = document.querySelector('select[name="country"]');
                        const selectUf = document.querySelector('select[name="uf"]');
                        
                        const targetSelect = selectCountry || selectUf;
                        
                        if (targetSelect) {
                            if (typeof $ !== 'undefined' && $(targetSelect).hasClass('select2-hidden-accessible')) {
                                $(targetSelect).val(data.uf).trigger('change');
                            } else {
                                targetSelect.value = data.uf;
                            }
                        }
                    }

                    // Datas
                    if (data.data_inicio_atividade) { // Vem no formato YYYY-MM-DD
                        const dateParts = data.data_inicio_atividade.split('-');
                        const formattedDate = `${dateParts[2]}/${dateParts[1]}/${dateParts[0]}`;
                        
                        const dateInput = document.querySelector('input[name="data_fundacao"]');
                         if (dateInput) {
                            // Se for flatpickr/datepicker, pode precisar de tratamento especial
                             dateInput.value = formattedDate;
                             // Tenta atualizar se for um componente de data visível
                             dateInput.dispatchEvent(new Event('input'));
                         }
                    }

                    toastr.success('Dados preenchidos com sucesso!');
                })
                .catch(error => {
                    console.error('Erro:', error);
                    toastr.error('Erro ao consultar CNPJ. Verifique se o número está correto.');
                })
                .finally(() => {
                    btnConsultar.innerHTML = originalText;
                    btnConsultar.disabled = false;
                });
            });
        }

        // CEP Search Logic
        const inputCep = document.getElementById('cep');
        if (inputCep) {
            inputCep.addEventListener('blur', function() {
                const cep = this.value.replace(/\D/g, '');

                if (cep !== "") {
                    const validacep = /^[0-9]{8}$/;

                    if(validacep.test(cep)) {
                        // Feedback visual? Maybe toast info
                        
                        fetch(`https://viacep.com.br/ws/${cep}/json/`)
                        .then(response => response.json())
                        .then(data => {
                            if (!("erro" in data)) {
                                // Update fields
                                if (document.querySelector('input[name="address1"]')) {
                                    document.querySelector('input[name="address1"]').value = data.logradouro;
                                }
                                if (document.getElementById('bairro')) {
                                    document.getElementById('bairro').value = data.bairro;
                                }
                                if (document.querySelector('input[name="city"]')) {
                                    document.querySelector('input[name="city"]').value = data.localidade;
                                }
                                
                                // Update State
                        
                        // Update State
                                const selectCountry = document.querySelector('select[name="country"]');
                                if (selectCountry) {
                                    if (typeof $ !== 'undefined' && $(selectCountry).hasClass('select2-hidden-accessible')) {
                                        $(selectCountry).val(data.uf).trigger('change');
                                    } else {
                                        selectCountry.value = data.uf;
                                    }
                                }
                                
                                toastr.success('Endereço encontrado!');
                            } else {
                                toastr.error("CEP não encontrado.");
                            }
                        })
                        .catch(error => {
                            console.error('Erro CEP:', error);
                            toastr.error("Erro ao buscar CEP.");
                        });
                    } else {
                        toastr.warning("Formato de CEP inválido.");
                    }
                }
            });
        }

        // Form Submission Logic
        const form = document.querySelector('#kt_drawer_fornecedor_form');
        const submitButton = document.querySelector('#kt_drawer_fornecedor_submit');
        const cancelButton = document.querySelector('#kt_drawer_fornecedor_cancel');

        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                // Validation
                // You might want to add client-side validation here
                
                // Prepare Data
                const formData = new FormData(form);
                const data = Object.fromEntries(formData.entries());

                // Button State
                const originalText = submitButton.querySelector('.indicator-label').textContent;
                submitButton.setAttribute('data-kt-indicator', 'on');
                submitButton.disabled = true;

                fetch('{{ route("financeiro.fornecedores.store") }}', {
                     method: 'POST',
                     headers: {
                         'Content-Type': 'application/json',
                         'Accept': 'application/json',
                         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                     },
                     body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        toastr.success(result.message);
                        
                        // ===== NOVO: Atualizar Select2 do Lançamento e selecionar o novo item =====
                        const novoId = result.data?.id;
                        const novoNome = result.data?.nome;
                        const novoTipo = result.data?.type || 'fornecedor';
                        
                        // Determina qual select deve ser atualizado
                        // Usa o tipo retornado pelo backend ou infere pelo título do drawer
                        let targetSelectId = '#fornecedor_id';
                        if (novoTipo === 'cliente') {
                            targetSelectId = '#cliente_id';
                        }
                        
                        // Fallback: usa variável global se disponível (set pelo drawer-init)
                        if (window.__drawerTargetSelect) {
                            targetSelectId = window.__drawerTargetSelect;
                        }
                        
                        if (novoId && novoNome && typeof $ !== 'undefined') {
                            const $target = $(targetSelectId);
                            
                            if ($target.length) {
                                // Remove option existente se houver (evita duplicação)
                                $target.find(`option[value="${novoId}"]`).remove();
                                
                                // Cria nova option já selecionada
                                const opt = new Option(novoNome, novoId, true, true);
                                
                                // Adiciona e dispara change para atualizar o Select2
                                $target.append(opt).trigger('change');
                                
                                console.log('[DrawerFornecedor] Select atualizado:', targetSelectId, 'com:', novoNome, '(ID:', novoId, ')');
                            } else {
                                console.warn('[DrawerFornecedor] Select alvo não encontrado:', targetSelectId);
                                
                                // Tenta fallback para #fornecedor_id se cliente_id não foi encontrado
                                if (targetSelectId === '#cliente_id') {
                                    const $fallback = $('#fornecedor_id');
                                    if ($fallback.length) {
                                        $fallback.find(`option[value="${novoId}"]`).remove();
                                        const optFallback = new Option(novoNome, novoId, true, true);
                                        $fallback.append(optFallback).trigger('change');
                                        console.log('[DrawerFornecedor] Fallback para #fornecedor_id com:', novoNome);
                                    }
                                }
                            }
                        } else {
                            console.warn('[DrawerFornecedor] Dados incompletos para atualizar select:', { novoId, novoNome });
                        }
                        
                        // Limpa variável global
                        window.__drawerTargetSelect = null;
                        // ===== FIM NOVO =====
                        
                        // Close Drawer
                         const drawerElement = document.getElementById('kt_drawer_fornecedor');
                         if (drawerElement) {
                             // Use Metronic Drawer instance if available
                             const drawer = KTDrawer.getInstance(drawerElement);
                             if (drawer) {
                                 drawer.hide();
                             } else {
                                 // Fallback: try getOrCreateInstance or click close button
                                 if (typeof KTDrawer.getOrCreateInstance === 'function') {
                                     const inst = KTDrawer.getOrCreateInstance(drawerElement);
                                     if (inst) inst.hide();
                                 } else {
                                     const closeBtn = document.querySelector('#kt_drawer_fornecedor_close');
                                     if(closeBtn) closeBtn.click();
                                 }
                             }
                         }

                         // Reset form
                         form.reset();
                         // Reset Select2 dentro do drawer de fornecedor
                          $('#fornecedor_tipo').val(null).trigger('change');
                          $('select[name="country"]').val(null).trigger('change');
                          // Reset hidden tipo field
                          $('#parceiro_tipo_hidden').val('fornecedor');

                         // Emit event for other listeners
                         document.dispatchEvent(new CustomEvent('parceiro-created', { 
                             detail: { 
                                 id: novoId, 
                                 nome: novoNome, 
                                 type: novoTipo 
                             } 
                         }));
                         
                    } else {
                        toastr.error(result.message || 'Erro ao salvar.');
                        if (result.errors) {
                            // Show validation errors
                            Object.values(result.errors).forEach(errors => {
                                errors.forEach(err => toastr.error(err));
                            });
                        }
                    }
                })
                .catch(error => {
                    console.error('Erro na requisição:', error);
                    toastr.error('Ocorreu um erro inesperado.');
                })
                .finally(() => {
                    submitButton.removeAttribute('data-kt-indicator');
                    submitButton.disabled = false;
                });
            });
        }

        if (cancelButton) {
            cancelButton.addEventListener('click', function() {
                  // Close Drawer logic similar to success
                 const drawerElement = document.getElementById('kt_drawer_fornecedor');
                 // ... close logic
                 const closeBtn = document.querySelector('#kt_drawer_fornecedor_close');
                 if(closeBtn) closeBtn.click();
            });
        }
    });
</script>
@endpush
