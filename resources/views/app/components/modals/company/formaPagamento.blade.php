         <!--begin::Modal - Add Payment-->
         <div class="modal fade" id="kt_modal_add_payment" tabindex="-1" aria-hidden="true">
             <!--begin::Modal dialog-->
             <div class="modal-dialog mw-650px">
                 <!--begin::Modal content-->
                 <div class="modal-content">
                     <!--begin::Modal header-->
                     <div class="modal-header">
                         <!--begin::Modal title-->
                         <h2 class="fw-bold">Add Forma de Pagamento</h2>
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
                             <!--begin::Input group-->
                             <div class="mb-7">
                                 <!--begin::Label-->
                                 <label class="fs-6 fw-semibold mb-3">
                                     <span>Add Icone</span>
                                     <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                         title="Allowed file types: png, jpg, jpeg."></i>
                                 </label>
                                 <!--end::Label-->
                                 <!--begin::Image input wrapper-->
                                 <div class="mt-1">
                                     <!--begin::Image placeholder-->
                                     <style>
                                         .image-input-placeholder {
                                             background-image: url('assets/media/svg/files/blank-image.svg');
                                         }

                                         [data-bs-theme="dark"] .image-input-placeholder {
                                             background-image: url('assets/media/svg/files/blank-image-dark.svg');
                                         }
                                     </style>
                                     <!--end::Image placeholder-->
                                     <!--begin::Image input-->
                                     <div class="image-input image-input-outline image-input-placeholder image-input-empty image-input-empty"
                                         data-kt-image-input="true">
                                         <!--begin::Preview existing avatar-->
                                         <div class="image-input-wrapper w-100px h-100px"
                                             style="background-image: url('')"></div>
                                         <!--end::Preview existing avatar-->
                                         <!--begin::Edit-->
                                         <label
                                             class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                             data-kt-image-input-action="change" data-bs-toggle="tooltip"
                                             title="Change avatar">
                                             <i class="bi bi-pencil-fill fs-7"></i>
                                             <!--begin::Inputs-->
                                             <input type="file" name="avatar" accept=".png, .jpg, .jpeg" />
                                             <input type="hidden" name="avatar_remove" />
                                             <!--end::Inputs-->
                                         </label>
                                         <!--end::Edit-->
                                         <!--begin::Cancel-->
                                         <span
                                             class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                             data-kt-image-input-action="cancel" data-bs-toggle="tooltip"
                                             title="Cancel avatar">
                                             <i class="bi bi-x fs-2"></i>
                                         </span>
                                         <!--end::Cancel-->
                                         <!--begin::Remove-->
                                         <span
                                             class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                             data-kt-image-input-action="remove" data-bs-toggle="tooltip"
                                             title="Remove avatar">
                                             <i class="bi bi-x fs-2"></i>
                                         </span>
                                         <!--end::Remove-->
                                     </div>
                                     <!--end::Image input-->
                                 </div>
                                 <!--end::Image input wrapper-->
                             </div>
                             <!--end::Input group-->
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

                             <!--begin::Input group-->
                             <div class="fv-row mb-15">
                                 <!--begin::Label-->
                                 <label class="fs-6 fw-semibold form-label mb-2">
                                     <span class="required">Informações</span>
                                     <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                         title="Information such as description of invoice or product purchased."></i>
                                 </label>
                                 <!--end::Label-->
                                 <!--begin::Input-->
                                 <textarea class="form-control form-control-solid rounded-3" name="observacao"></textarea>
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
