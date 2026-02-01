  <!--begin::Modal - Cadastro Membro-->
  <div class="modal fade" id="kt_modal_member" tabindex="-1" aria-hidden="true">
      <!--begin::Modal dialog-->
      <div class="modal-dialog modal-dialog-top mw-750px">
          <!--begin::Modal content-->
          <div class="modal-content rounded">
              <!--begin::Modal header-->
              <div class="modal-header border-4 pb-4">
                  <!--begin::Heading-->
                  <div>
                      <!--begin::Title-->
                      <h1 class="mb-1">Novo Membro</h1>
                      <!--end::Title-->
                  </div>
                  <!--end::Heading-->
                  <!--begin::Close-->
                  <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                      <i class="bi bi-x-lg fs-2"></i>
                  </div>
                  <!--end::Close-->
              </div>
              <!--end::Modal header-->
              <!--begin::Modal body-->
              <div class="modal-body scroll-y px-8 px-lg-8 pt-8 pb-15">
                  <!--begin:Form-->
                  <form id="kt_modal_member_form" class="form" action="#">
                      @include('app.modules.secretary.partials._form')
                  </form>
                  <!--end:Form-->
              </div>
              <!--end::Modal body-->
              <!--Begin::Modal footer-->
              <div class="modal-footer flex-center">
                  <!--begin::Actions-->
                  <button type="reset" id="kt_modal_new_target_cancel" class="btn btn-light me-3">Cancelar</button>
                  
                  <!--begin::Split Button Salvar-->
                  <div class="btn-group">
                      <button type="submit" id="kt_modal_new_target_submit" class="btn btn-primary" data-save-mode="default">
                          <span class="indicator-label">Salvar</span>
                          <span class="indicator-progress">Por favor, aguarde...
                              <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                          </span>
                      </button>
                      <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" 
                          data-bs-toggle="dropdown" aria-expanded="false">
                          <span class="visually-hidden">Mais opções</span>
                      </button>
                      <ul class="dropdown-menu dropdown-menu-end">
                          <li>
                              <a class="dropdown-item" href="#" data-save-mode="clone">
                                  <i class="fa-regular fa-copy me-2 text-primary"></i>Salvar e Clonar
                              </a>
                          </li>
                          <li>
                              <a class="dropdown-item" href="#" data-save-mode="clear">
                                  <i class="fa-regular fa-file me-2 text-success"></i>Salvar e Limpar
                              </a>
                          </li>
                      </ul>
                  </div>
                  <!--end::Split Button Salvar-->
                  <!--end::Actions-->
              </div>
              <!--End::Modal footer-->
          </div>
          <!--end::Modal content-->
      </div>
      <!--end::Modal dialog-->
  </div>
  <!--end::Modal - New Target-->
