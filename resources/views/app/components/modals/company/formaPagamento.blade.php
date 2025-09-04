         <!--begin::Modal - Add Payment-->
         <div class="modal fade" id="kt_modal_add_payment" tabindex="-1" aria-hidden="true">
             <!--begin::Modal dialog-->
             <div class="modal-dialog mw-650px">
                 <!--begin::Modal content-->
                 <div class="modal-content">
                     <!--begin::Modal header-->
                     <div class="modal-header">
                         <!--begin::Modal title-->
                         <h2 class="fw-bold">Forma de Pagamento</h2>
                         <!--end::Modal title-->
                         <!--begin::Close-->
                         <div id="kt_modal_add_payment_close" class="btn btn-icon btn-sm btn-active-icon-primary">
                             <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
                             <span class="svg-icon svg-icon-1">
                                 <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                     <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                         transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                     <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                         transform="rotate(45 7.41422 6)" fill="currentColor" />
                                 </svg>
                             </span>
                             <!--end::Svg Icon-->
                         </div>
                         <!--end::Close-->
                     </div>
                     <!--end::Modal header-->
                     <!--begin::Modal body-->
                     <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                         <!--begin::Form-->
                         <form id="kt_modal_add_form" method="POST" action="{{ route('formas-pagamento.store') }}">
                            @csrf
                             <!--begin::Row-->
                             <div class="row">
                                 <!--begin::Col-->
                                 <div class="col-md-7">
                                     <!--begin::Input group-->
                                     <div class="fv-row mb-7">
                                         <!--begin::Label-->
                                         <label class="fs-6 fw-semibold form-label mb-2">
                                             <span class="required">Nome</span>
                                             <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                                 title="Nome da forma de pagamento."></i>
                                         </label>
                                         <!--end::Label-->
                                         <!--begin::Input-->
                                         <input type="text" class="form-control form-control-solid" name="nome"
                                             id="nome" value="" />
                                         <!--end::Input-->
                                     </div>
                                                             <!--begin::Row-->
                                 </div>
                                <!--begin::Col-->
                                <div class="col-md-5">
                                     <!--end::Input group-->
                                     <div class="fv-row mb-7">
                                         <!--begin::Label-->
                                         <label class="fs-6 fw-semibold form-label mb-2">
                                             <span class="">Código</span>
                                             <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                                 title="Ele precisar ser uma sigla ou código descritivo (ex: 'PIX', 'BOL', 'CC')."></i>
                                         </label>
                                         <!--end::Label-->
                                         <!--begin::Input-->
                                         <input type="text" class="form-control form-control-solid"
                                             placeholder="Ex: Pix, BOL" name="codigo" id="codigo" value="" />
                                         <!--end::Input-->
                                     </div>
                                     <!--end::Input group-->
                                 </div>
                             </div>

                             <!--begin::Input group-->
                             <div class="fv-row mb-10">
                                 <!--begin::Label-->
                                 <label class="fs-6 fw-semibold mb-2">Tipo de Taxa
                                     <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                                         title="Representa um valor em reais (R$) ou uma porcentagem (%) depende do contexto."></i>
                                 </label>
                                 <!--End::Label-->
                                 <!--begin::Row-->
                                 <div class="row g-9" data-kt-buttons="true"
                                     data-kt-buttons-target="[data-kt-button='true']">
                                     <!--begin::Col-->
                                     <div class="col">
                                         <!--begin::Option-->
                                         <label
                                             class="btn btn-outline btn-outline-dashed btn-active-light-primary active d-flex text-start p-6"
                                             data-kt-button="true">
                                             <!--begin::Radio-->
                                             <span
                                                 class="form-check form-check-custom form-check-solid form-check-sm align-items-start mt-1">
                                                 <input class="form-check-input" type="radio" name="tipo_taxa"
                                                     value="valor_fixo" checked="checked" />
                                             </span>
                                             <!--end::Radio-->
                                             <!--begin::Info-->
                                             <span class="ms-5">
                                                 <span class="fs-4 fw-bold text-gray-800 d-block">Valor Fixo R$</span>
                                             </span>
                                             <!--end::Info-->
                                         </label>
                                         <!--end::Option-->
                                     </div>
                                     <!--end::Col-->
                                     <!--begin::Col-->
                                     <div class="col">
                                         <!--begin::Option-->
                                         <label
                                             class="btn btn-outline btn-outline-dashed btn-active-light-primary d-flex text-start p-6"
                                             data-kt-button="true">
                                             <!--begin::Radio-->
                                             <span
                                                 class="form-check form-check-custom form-check-solid form-check-sm align-items-start mt-1">
                                                 <input class="form-check-input" type="radio" id="tipo_taxa"
                                                     name="tipo_taxa" value="porcentagem" />
                                             </span>
                                             <!--end::Radio-->
                                             <!--begin::Info-->
                                             <span class="ms-5">
                                                 <span class="fs-4 fw-bold text-gray-800 d-block">Porcentagem %</span>
                                             </span>
                                             <!--end::Info-->
                                         </label>
                                         <!--end::Option-->
                                     </div>
                                     <!--end::Col-->
                                 </div>
                                 <!--end::Row-->
                             </div>
                             <!--end::Input group-->
                             <!--begin::Row-->
                             <div class="row">
                                 <!--begin::Col-->
                                 <div class="col-md-6">
                                     <!--begin::Input group-->
                                     <div class="fv-row mb-7">
                                         <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                             <span class="required">Valor</span>
                                             <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                                                 title="Informe o valor da despesa"></i>
                                         </label>
                                         <div class="input-group mb-3">
                                             <!-- Símbolo dinâmico (R$ ou %) -->
                                             <span class="input-group-text" id="simbolo">R$</span>
                                             <!-- Campo de entrada -->
                                             <input type="text" class="form-control" name="taxa"
                                                 id="valor2" placeholder="0,00" aria-label="Valor"
                                                 aria-describedby="simbolo">
                                         </div>
                                     </div>
                                     <!--end::Input group-->
                                 </div>
                                 <!--end::Col-->
                                 <!--begin::Col-->
                                 <div class="col-md-6">
                                     <!--begin::Input group-->
                                     <div class="fv-row mb-7">
                                         <!--begin::Label-->
                                         <label
                                             class="required fs-6 fw-semibold form-label mb-2">Ativado/Desativado</label>
                                         <!--end::Label-->
                                         <!--begin::Input-->
                                         <select class="form-select form-select-solid fw-bold"
                                             data-control="select2" data-placeholder="Selecione uma opção"
                                             data-hide-search="true" id="ativo" name="ativo">
                                             <option></option>
                                             <option value="1">Ativado</option>
                                             <option value="0">Desativado</option>
                                         </select>
                                         <!--end::Input-->
                                     </div>
                                     <!--end::Input group-->
                                 </div>
                                 <!--end::Col-->
                             </div>
                             <!--end::Row-->
                             
                             <!--begin::Row-->
                             <div class="row">
                                 <!--begin::Col-->
                                 <div class="col-md-6">
                                     <!--begin::Input group-->
                                     <div class="fv-row mb-7">
                                         <!--begin::Label-->
                                         <label class="fs-6 fw-semibold form-label mb-2">
                                             <span class="">Prazo de Liberação (dias)</span>
                                             <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                                 title="Prazo em dias para liberação do pagamento."></i>
                                         </label>
                                         <!--end::Label-->
                                         <!--begin::Input-->
                                         <input type="number" class="form-control form-control-solid"
                                             placeholder="0" name="prazo_liberacao" id="prazo_liberacao" value="0" min="0" />
                                         <!--end::Input-->
                                     </div>
                                     <!--end::Input group-->
                                 </div>
                                 <!--end::Col-->
                                 <!--begin::Col-->
                                 <div class="col-md-6">
                                     <!--begin::Input group-->
                                     <div class="fv-row mb-7">
                                         <!--begin::Label-->
                                         <label class="fs-6 fw-semibold form-label mb-2">
                                             <span class="">Método de Integração</span>
                                             <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                                 title="Método de integração com sistemas externos."></i>
                                         </label>
                                         <!--end::Label-->
                                         <!--begin::Input-->
                                         <input type="text" class="form-control form-control-solid"
                                             placeholder="Ex: API, Webhook" name="metodo_integracao" id="metodo_integracao" />
                                         <!--end::Input-->
                                     </div>
                                     <!--end::Input group-->
                                 </div>
                                 <!--end::Col-->
                             </div>
                             <!--end::Row-->
                             
                             <!--begin::Input group-->
                             <div class="fv-row mb-7">
                                 <!--begin::Label-->
                                 <label class="fs-6 fw-semibold form-label mb-2">
                                     <span class="">Observações</span>
                                     <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                         title="Observações adicionais sobre a forma de pagamento."></i>
                                 </label>
                                 <!--end::Label-->
                                 <!--begin::Input-->
                                 <textarea class="form-control form-control-solid" rows="3" 
                                     placeholder="Digite observações adicionais..." name="observacao" id="observacao"></textarea>
                                 <!--end::Input-->
                             </div>
                             <!--end::Input group-->
                             
                             <!--begin::Actions-->
                             <div class="text-center">
                                 <button type="reset" id="kt_modal_add_payment_cancel"
                                     class="btn btn-light me-3">Sair</button>
                                 <button type="submit" id="kt_modal_add_payment_submit" class="btn btn-primary">
                                     <span class="indicator-label">Enviar</span>
                                     <span class="indicator-progress">Espere...
                                         <span
                                             class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                 </button>
                             </div>
                             <!--end::Actions-->
                         </form>
                         <!--end::Form-->
                     </div>
                     <!--end::Modal body-->
                 </div>
                 <!--end::Modal content-->
             </div>
             <!--end::Modal dialog-->
         </div>
         <!--end::Modal - New Card-->

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Função para atualizar o símbolo da taxa baseado no tipo selecionado
    function updateTaxaSymbol() {
        const tipoTaxa = document.querySelector('input[name="tipo_taxa"]:checked').value;
        const simboloElement = document.getElementById('simbolo');
        const taxaInput = document.getElementById('valor2');
        
        if (tipoTaxa === 'valor_fixo') {
            simboloElement.textContent = 'R$';
            taxaInput.placeholder = '0,00';
        } else {
            simboloElement.textContent = '%';
            taxaInput.placeholder = '0,00';
        }
    }

    // Adicionar event listeners para os radio buttons
    const radioButtons = document.querySelectorAll('input[name="tipo_taxa"]');
    radioButtons.forEach(radio => {
        radio.addEventListener('change', updateTaxaSymbol);
    });

    // Formatação de moeda para o campo taxa
    const taxaInput = document.getElementById('valor2');
    taxaInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        value = (parseFloat(value) / 100).toFixed(2);
        e.target.value = value.replace('.', ',');
    });

    // Validação do formulário
    const form = document.getElementById('kt_modal_add_form');
    form.addEventListener('submit', function(e) {
        const nome = document.getElementById('nome').value.trim();
        const codigo = document.getElementById('codigo').value.trim();
        const taxa = document.getElementById('valor2').value.trim();
        const ativo = document.getElementById('ativo').value;

        if (!nome) {
            e.preventDefault();
            alert('Por favor, preencha o nome da forma de pagamento.');
            return false;
        }

        if (!codigo) {
            e.preventDefault();
            alert('Por favor, preencha o código da forma de pagamento.');
            return false;
        }

        if (!taxa || parseFloat(taxa.replace(',', '.')) <= 0) {
            e.preventDefault();
            alert('Por favor, preencha um valor válido para a taxa.');
            return false;
        }

        if (!ativo) {
            e.preventDefault();
            alert('Por favor, selecione se a forma de pagamento está ativa ou não.');
            return false;
        }
    });

    // Inicializar o símbolo
    updateTaxaSymbol();
});
</script>
