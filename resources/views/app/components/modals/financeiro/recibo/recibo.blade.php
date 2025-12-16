       <!--begin::Modal - Support Center - Create Ticket-->

@if (!empty($caixa->recibo) && !empty($caixa->recibo->id))
<!-- Modal para Exclusão do Recibo -->
<div class="modal fade" id="modalDeleteRecibo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Excluir Recibo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir este recibo?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <form action="{{ route('recibos.destroy', $caixa->recibo->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Excluir</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

       <div class="modal fade {{ session('modal_open') ? 'show d-block' : '' }}" id="kt_modal_new_ticket" tabindex="-1"
           aria-hidden="true">
           <!--begin::Modal dialog-->
           <div class="modal-dialog modal-dialog-centered mw-850px">
               <!--begin::Modal content-->
               <div class="modal-content rounded">
                   <!--begin::Modal header-->
                   <div class="modal-header pb-0 border-0 justify-content-end">
                       <!--begin::Close-->
                       <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
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
                   <!--begin::Modal header-->
                   <!--begin::Modal body-->
                   <div class="modal-body scroll-y px-10 px-lg-15 pt-0 pb-15">
                       <!--begin::Heading-->
                       <div class="mb-13 text-center">
                           <!--begin::Title-->
                           <h1 class="mb-3">Gerar Recibo</h1>
                           <!--end::Title-->
                        <!--begin::Description-->
                        <div class="text-gray-400 fw-semibold fs-5">
                            Preencha os dados abaixo para emitir o recibo referente a esta transação. Após a emissão, você poderá imprimir ou excluir o recibo conforme necessário.
                        </div>       <!--end::Description-->
                       </div>
                       <!--end::Heading-->
                       <!--begin:Form-->
                       <form method="POST" action="{{ route('gerarRecibo', ['transacao' => $caixa->id]) }}">
                           @csrf
                           <!--begin::Wrapper-->
                           <input type="hidden" name="tipo_transacao"
                               value="{{ $caixa->tipo === 'entrada' ? 'Recebimento' : 'Pagamento' }}">

                           <!--begin::Separator-->
                           <div class="d-flex flex-column align-items-start flex-xxl-row">
                               <!--begin::Input group-->
                               <div class="d-flex align-items-center flex-equal fw-row me-4 order-2"
                                   data-bs-toggle="tooltip" data-bs-trigger="hover" title="Especificar data do recibo">
                                   <!--begin::Date-->
                                   <div class="fs-6 fw-bold text-gray-700 text-nowrap">Data:</div>
                                   <!--end::Date-->
                                   <!--begin::Input-->
                                   <div class="position-relative d-flex align-items-center w-150px">
                                       <!--begin::Datepicker-->
                                       <input class="form-control form-control-transparent fw-bold pe-5"
                                           placeholder="Select date" readonly name="data_emissao"
                                           value="{{ \Carbon\Carbon::parse($caixa->data_competencia)->format('d/m/Y') }}" />
                                       <!--end::Datepicker-->
                                   </div>
                                   <!--end::Input-->
                               </div>
                               <!--end::Input group-->
                               <!--begin::Input group-->
                               <div class="d-flex flex-center flex-equal fw-row text-nowrap order-1 order-xxl-2 me-4"
                                   data-bs-toggle="tooltip" data-bs-trigger="hover"  title="Número do recibo">
                                   <span class="fs-2x fw-bold readonly text-gray-800">Número #</span>
                                   <input type="text" class="form-control form-control-solid"
                                       value="{{ $caixa->recibo->id ?? 'Não Emitido' }}" readonly placeholder="...">

                               </div>
                               <!--end::Input group-->
                               <!--begin::Input group-->
                               <div class="d-flex align-items-center justify-content-end flex-equal order-3 fw-row"
                                data-bs-toggle="tooltip" data-bs-trigger="hover" title="Valor referente ao recibo">       <!--begin::Date-->
                                   <div class="fs-6 fw-bold text-gray-700 text-nowrap">Valor: R$
                                   </div>
                                   <!--end::Date-->
                                   <!--begin::Input-->
                                   <div class="position-relative d-flex align-items-center w-150px">
                                       <!--begin::Datepicker-->
                                       <input class="form-control form-control-transparent fw-bold pe-5"
                                           placeholder="Select date" name="valor" readonly
                                           value="{{ number_format($caixa->valor, 2, ',', '.') }}" />
                                       <!--end::Datepicker-->
                                   </div>
                                   <!--end::Input-->
                               </div>
                               <!--end::Input group-->
                           </div>
                           <!--end::Top-->
                           <div class="separator separator-dashed my-10"></div>
                           <!--end::Separator-->
                           <!--begin::Input group-->
                           <div class="row g-9 mb-8">
                               <!--begin::Col-->
                               <div class="col-md-8 fv-row">
                                   <label class="required fs-6 fw-semibold mb-2">Nome</label>
                                   <input type="text"
                                       class="form-control form-control-solid {{ $errors->has('nome') ? 'is-invalid' : '' }}"
                                       placeholder="Nome da Empresa ou da Pessoa" name="nome"
                                       value="{{ old('nome', $caixa->recibo->nome ?? '') }}" />

                                   <!-- Exibir Mensagem de Erro -->
                                   @if ($errors->has('nome'))
                                       <div class="invalid-feedback">
                                           {{ $errors->first('nome') }}
                                       </div>
                                   @endif

                               </div>
                               <!--end::Col-->
                               <!--begin::Col-->
                               <div class="col-md-4 fv-row">
                                   <!--begin::Label-->
                                   <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                       <span class="">CPF/CNPJ</span>
                                       <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                                           title="109.824.789.47"></i>
                                   </label>
                                   <!--end::Label-->
                                   <input type="text"
                                       class="form-control form-control-solid {{ $errors->has('cpf_cnpj') ? 'is-invalid' : '' }}"
                                       placeholder="108.834.864-40" id="cpf_cnpj" name="cpf_cnpj"
                                       value="{{ old('cpf_cnpj', $caixa->recibo->cpf_cnpj ?? '') }}" />
                                   <!-- Exibir Mensagem de Erro -->
                                   @if ($errors->has('cpf_cnpj'))
                                       <div class="invalid-feedback">
                                           {{ $errors->first('cpf_cnpj') }}
                                       </div>
                                   @endif
                               </div>
                               <!--end::Col-->
                           </div>
                           <!--end::Input group-->
                           <!--begin::Input group-->
                           <div class="row g-9 mb-8">
                               <!--begin::Col-->
                               <div class="col-md-3 fv-row">
                                   <!--begin::Label-->
                                   <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                       <span class="">CEP</span>
                                   </label>
                                   <!--end::Label-->
                                   <input class="form-control form-control-solid" placeholder="55385-000"
                                       id="cep" name="cep"
                                       value="{{ optional(optional($caixa->recibo)->address)->cep ?? '' }}" placeholder="Ex: 55385-000" />
                               </div>
                               <!--end::Begin-->
                               <!--begin::Col-->
                               <div class="col-md-7 fv-row">
                                   <!--begin::Label-->
                                   <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                       <span class="">Rua</span>
                                   </label>
                                   <!--end::Label-->
                                   <input class="form-control form-control-solid" placeholder="Ex: Rua São José"
                                       id="logradouro" name="logradouro"
                                       value="{{ optional(optional($caixa->recibo)->address)->rua ?? '' }}" />
                               </div>
                               <!--end::Begin-->
                               <!--begin::Col-->
                               <div class="col-md-2 fv-row">
                                   <!--begin::Label-->
                                   <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                       <span class="">Número</span>
                                   </label>
                                   <!--end::Label-->
                                   <input class="form-control form-control-solid" placeholder="N:108" id="logradouro"
                                       name="numero"
                                       value="{{ optional(optional($caixa->recibo)->address)->numero ?? '' }}" />
                               </div>
                               <!--end::Begin-->
                               <!--end::Input group-->
                           </div>
                           <!--begin::Input group-->
                           <div class="row g-9 mb-8">
                               <!--begin::Col-->
                               <div class="col-md-4 fv-row">
                                   <label class="fs-6 fw-semibold mb-2">Bairro</label>
                                   <!--begin::Input-->
                                   <div class="position-relative d-flex align-items-center">
                                       <!--begin::Datepicker-->
                                       <input type="text" class="form-control form-control-solid"
                                           placeholder="Ex: Rua Frei Caneca" id="bairro"
                                           value="{{ optional(optional($caixa->recibo)->address)->bairro ?? '' }}"
                                           name="bairro" />
                                       <!--end::Datepicker-->
                                   </div>
                                   <!--end::Input-->
                               </div>
                               <!--end::Col-->
                               <!--begin::Col-->
                               <div class="col-md-4 fv-row">
                                   <label class="fs-6 fw-semibold mb-2">Cidade</label>
                                   <!--begin::Input-->
                                   <div class="position-relative d-flex align-items-center">
                                       <!--begin::Datepicker-->
                                       <input type="text" class="form-control form-control-solid"
                                           placeholder="Ex: Recife" id="localidade"
                                           value="{{ optional(optional($caixa->recibo)->address)->cidade ?? '' }}"
                                           name="localidade" />
                                       <!--end::Datepicker-->
                                   </div>
                                   <!--end::Input-->
                               </div>
                               <!--end::Col-->
                               <!--begin::Col-->
                               <div class="col-md-4 fv-row">
                                   <label class="fs-6 fw-semibold mb-2">Estado</label>
                                   <select id="uf" name="uf"
                                       data-control="select2"class="form-select form-select-solid"
                                       data-control="select2" data-placeholder="Pernambuco"
                                       {{ optional(optional($caixa->recibo)->uf)->uf ?? ''}}
                                       data-hide-search="true">
                                       <option value=""></option>
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
                               </div>
                               <!--end::Col-->
                               <!--end::Col-->
                           </div>
                           <!--end::Input group-->

                           <!--begin::Input group-->
                           <div class="d-flex flex-column mb-8 fv-row">
                               <label class="fs-6 fw-semibold mb-2">Referente</label>

                               <!-- Textarea para descrição -->
                               <textarea class="form-control form-control-solid {{ $errors->has('referente') ? 'is-invalid' : '' }}"
                                rows="4" name="referente" placeholder="Descreva o serviço prestado">{{ old('referente', $caixa->recibo->referente ?? '') }}</textarea>


                               <!-- Exibir Mensagem de Erro -->
                               @if ($errors->has('referente'))
                                   <div class="invalid-feedback">
                                       {{ $errors->first('referente') }}
                                   </div>
                               @endif
                           </div>

                           <!--end::Input group-->

                           <!--begin::Actions-->
                           <div class="text-center">
                               <button type="reset" id="kt_modal_new_ticket_cancel"
                                   class="btn btn-light me-3">Cancel</button>

                               <!-- Exibe os botões somente se existir um Recibo gerado -->
                               @if (!empty($caixa->recibo) && !empty($caixa->recibo->id))
                                   <!-- Botão para Imprimir Recibo -->
                                   <a href="{{ route('recibo.imprimir', $caixa->recibo->id) }}" target="_blank"
                                       class="btn btn-success">
                                       <i class="fas fa-print"></i> Imprimir Recibo
                                   </a>
                                   <!-- Botão para Excluir Recibo -->
                                   <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                       data-bs-target="#modalDeleteRecibo">
                                       <i class="fas fa-trash"></i> Excluir Recibo
                                   </button>
                               @endif

                               <button type="submit" id="kt_modal_new_ticket_submit" class="btn btn-primary">
                                   <span class="indicator-label">Emitir</span>
                                   <span class="indicator-progress">Por favor, aguarde...
                                       <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                   </span>
                               </button>
                           </div>
                           <!--end::Actions-->
                       </form>
                       <!--end:Form-->
                   </div>
                   <!--end::Modal body-->
               </div>
               <!--end::Modal content-->
           </div>
           <!--end::Modal dialog-->
       </div>
       <!--end::Modal - Support Center - Create Ticket-->

       <script>
           $(document).ready(function() {
               // Quando o campo CEP perde o foco
               $('#cep').on('blur', function() {
                   var cep = $(this).val().replace(/\D/g, '');

                   if (cep !== "") {
                       // Verifica se o CEP tem 8 dígitos
                       var validacep = /^[0-9]{8}$/;

                       if (validacep.test(cep)) {
                           // Preenche os campos com "..." enquanto carrega
                           $('#logradouro').val('...');
                           $('#bairro').val('...');
                           $('#localidade').val('...');
                           $('#uf').val('...');
                           $('#ibge').val('...');
                           $('#complemento').val('...');

                           // Faz a requisição para a API ViaCEP
                           $.getJSON("https://viacep.com.br/ws/" + cep + "/json/?callback=?", function(dados) {

                               if (!("erro" in dados)) {
                                   // Atualiza os campos com os valores da consulta
                                   $('#logradouro').val(dados.logradouro);
                                   $('#bairro').val(dados.bairro);
                                   $('#localidade').val(dados.localidade);
                                   $('#uf').val(dados.uf).trigger('change'); // Atualiza o select2
                                   $('#ibge').val(dados.ibge);
                                   $('#complemento').val(dados.complemento);
                               } else {
                                   // CEP não encontrado
                                   alert("CEP não encontrado.");
                               }
                           });
                       } else {
                           alert("Formato de CEP inválido.");
                       }
                   } else {
                       // CEP sem valor, limpa o formulário
                       limpaFormularioCEP();
                   }
               });

               function limpaFormularioCEP() {
                   // Limpa valores do formulário de CEP
                   $('#logradouro').val('');
                   $('#bairro').val('');
                   $('#localidade').val('');
                   $('#uf').val('').trigger('change');
                   $('#ibge').val('');
                   $('#complemento').val('');
               }
           });
       </script>

       <script>
           document.addEventListener("DOMContentLoaded", function() {
               if ("{{ session('modal_open') }}" === "1") {
                   var modalElement = document.getElementById("kt_modal_new_ticket");
                   var myModal = new bootstrap.Modal(modalElement);
                   myModal.show();

                   // Criar manualmente o fundo escurecido (modal-backdrop)
                   if (!document.querySelector('.modal-backdrop')) {
                       let backdrop = document.createElement("div");
                       backdrop.className = "modal-backdrop fade show";
                       document.body.appendChild(backdrop);
                   }
               }
           });
       </script>
