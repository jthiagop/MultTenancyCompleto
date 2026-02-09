  <!--begin::Modal - New Target-->
  <div class="modal fade" id="Dm_modal_Avaliador" tabindex="-1" aria-hidden="true">
      <!--begin::Modal dialog-->
      <div class="modal-dialog modal-dialog-centered mw-650px">
          <!--begin::Modal content-->
          <div class="modal-content rounded">
              <!--begin::Modal header-->
              <div class="modal-header pb-0 border-0 justify-content-end">
                  <!--begin::Close-->
                  <div class="btn btn-sm btn-icon btn-active-color-primary" data-kt-modal-action-type="close">
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
                  <!--begin:Form-->
                  <form id="kt_modal_avaliador_form" class="form" action="{{ route('avaliador.store') }}" method="POST"
                        enctype="multipart/form-data">
                      @csrf
                      <!--begin::Heading-->
                      <div class="mb-13 text-center">
                          <h1 class="mb-3">Cadastrar Avaliador</h1>
                          <div class="text-muted fw-semibold fs-5">
                              Se precisar de mais informações, consulte o
                              <a href="#" class="fw-bold link-primary">manual de cadastro</a>.
                          </div>
                      </div>
                      <!--end::Heading-->

                      <!-- Avatar (sem floating label, pois é um componente de imagem personalizado) -->
                      <div class="fv-row mb-7 text-center">
                          <div class="d-flex justify-content-center">
                              <div class="image-input image-input-outline image-input-placeholder"
                                  data-kt-image-input="true">
                                  <!-- Imagem -->
                                  <div class="image-input-wrapper w-150px h-150px rounded-circle"
                                      style="background-image: url('tenancy/assets/media/avatars/blank.png');"></div>
                                  <!-- Botão alterar -->
                                  <label
                                      class="btn btn-icon btn-circle btn-active-color-primary w-30px h-30px bg-body shadow position-absolute translate-middle bottom-0 start-100"
                                      data-kt-image-input-action="change" title="Alterar avatar">
                                      <i class="bi bi-pencil-fill fs-7"></i>
                                      <input type="file" name="avatar" accept=".png, .jpg, .jpeg" />
                                      <input type="hidden" name="avatar_remove" />
                                  </label>
                                  <!-- Botão remover -->
                                  <span
                                      class="btn btn-icon btn-circle btn-active-color-primary w-30px h-30px bg-body shadow position-absolute translate-middle bottom-0 start-0"
                                      data-kt-image-input-action="remove" title="Remover avatar">
                                      <i class="bi bi-x fs-2"></i>
                                  </span>
                              </div>
                          </div>
                          <div class="form-text mt-3">Tipos de arquivo permitidos: png, jpg, jpeg.</div>
                      </div>
                      <!--end::Avatar-->

                      <!--begin::Floating group - Nome-->
                      <div class="form-floating mb-7">
                          <input type="text" class="form-control" id="nomeAvaliador" name="nome" placeholder=" "
                              required />
                          <label for="nomeAvaliador">Nome do Avaliador</label>
                      </div>
                      <!--end::Floating group - Nome-->
                      <div class="row g-3">

                          <!--begin::Floating group - Tipo de Profissional-->
                          <!-- Para floating labels com <select>, precisa incluir placeholder no <option> vazio -->
                          <div class="col-md-6 form-floating mb-7">
                              <select class="form-select" id="tipoProfissional" name="tipo_profissional"
                                  aria-label="Selecione o tipo" required>
                                  <option value="" disabled selected>Selecione...</option>
                                  <option value="engenheiro_civil">Engenheiro Civil</option>
                                  <option value="engenheiro_agronomo">Engenheiro Agrônomo</option>
                                  <option value="arquiteto">Arquiteto</option>
                                  <option value="corretor_imoveis">Corretor de Imóveis</option>
                                  <option value="outro">Outro</option>
                              </select>
                              <label for="tipoProfissional">Tipo de Profissional</label>
                          </div>
                          <!--end::Floating group - Tipo de Profissional-->

                          <!--begin::Floating group - Registro Profissional-->
                          <div class="col-md-6 form-floating mb-7">
                              <input type="text" class="form-control" id="registroProfissional"
                                  name="registro_profissional" placeholder=" " required />
                              <label for="registroProfissional">Registro Profissional (Ex: CREA, CAU, CRECI)</label>
                          </div>
                          <!--end::Floating group - Registro Profissional-->
                      </div>
                      <!--begin::Floating group - Telefone-->
                      <div class="form-floating mb-7">
                          <input type="text" class="form-control" id="telefoneAvaliador" name="telefone"
                              placeholder=" " required />
                          <label for="telefoneAvaliador">Telefone</label>
                      </div>
                      <!--end::Floating group - Telefone-->

                      <!--begin::Floating group - Email-->
                      <div class="form-floating mb-7">
                          <input type="email" class="form-control" id="emailAvaliador" name="email" placeholder=" "
                              required />
                          <label for="emailAvaliador">E-mail</label>
                      </div>
                      <!--end::Floating group - Email-->
                      <div class="row g-3">

                          <div class="col-md-3 form-floating">
                              <input type="text" class="form-control" id="cep" name="cep"
                                  placeholder=" " />
                              <label for="cepAvaliador">CEP</label>
                          </div>

                          <!--begin::Floating group - Endereço-->
                          <div class="col-md-9 form-floating mb-7">
                              <input type="text" class="form-control" id="logradouro" name="endereco"
                                  placeholder=" " />
                              <label for="logradouro">Endereço</label>
                          </div>
                          <!--end::Floating group - Endereço-->
                      </div>
                      <!--begin::Row para Cidade / UF / País com floating labels-->
                      <div class="row g-3">
                          <!-- País -->
                          <div class="col-md-4 form-floating">
                              <input type="text" class="form-control" id="bairro" name="bairro"
                                  placeholder=" " />
                              <label for="bairro">Bairro</label>
                          </div>
                          <!-- Cidade -->
                          <div class="col-md-4 form-floating">
                              <input type="text" class="form-control" id="localidade" name="cidade"
                                  placeholder=" " />
                              <label for="localidade">Cidade</label>
                          </div>
                          <!-- UF -->
                          <div class="col-md-4 form-floating">
                              <input type="text" class="form-control" id="uf" name="uf"
                                  placeholder=" " />
                              <label for="uf">UF</label>
                          </div>
                      </div>
                      <!--end::Row Cidade / UF / País-->

                      <!--begin::Actions-->
                      <div class="text-center mt-8">
                        <button type="reset" class="btn btn-light me-3" data-kt-modal-action-type="cancel">Cancelar</button>
                        <button type="submit" class="btn btn-primary" data-kt-modal-action-type="submit">
                            <span class="indicator-label">Cadastrar</span>
                            <span class="indicator-progress">Aguarde...
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
  <!--end::Modal - New Target-->
