  <!--begin::Modal - New Target-->
  <div class="modal fade" id="Dm_modal_financeiro" tabindex="-1" aria-hidden="true">
      <!--begin::Modal dialog-->
      <div class="modal-dialog modal-fullscreen">
          <!--begin::Modal content-->
          <div class="modal-content rounded">
              <!--begin::Modal header-->
              <div class="modal-header">
                  <h2 class="modal-title" id="modal_financeiro_title">Novo Lançamento</h2>

                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <!--end::Modal header-->

              <!--begin::Modal body-->
              <div class="modal-body scroll-y px-10 px-lg-15 pb-15  bg-light pt-5">
                <!-- Begin::Form -->
                  <form id="kt_modal_new_target_form" class="form" action="#">
                      @csrf
                      <meta name="csrf-token" content="{{ csrf_token() }}">

                      <!-- Campo oculto para identificar o tipo (receita ou despesa) -->
                      <input type="hidden" name="tipo_financeiro" id="tipo_financeiro" value="">
                      <input type="hidden" name="status_pagamento" id="status_pagamento" value="em aberto">
                      <div class="card mb-xl-10 ">
                          <div class="card-body px-10">
                              <!--begin::Form-->
                              <!--begin::Input group - Assign & Due Date-->
                              <div class="row g-9 mb-8">
                                  <div class="col-md-2 fv-row">
                                      <label class="required fs-6 fw-semibold mb-2">
                                          Data de competência
                                      </label>
                                      <div class="position-relative d-flex align-items-center">
                                          <!--begin::Icon-->
                                          <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                              <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                  xmlns="http://www.w3.org/2000/svg">
                                                  <path opacity="0.3"
                                                      d="M21 22H3C2.4 22 2 21.6 2 21V5C2 4.4 2.4 4 3 4H21C21.6 4 22 4.4 22 5V21C22 21.6 21.6 22 21 22Z"
                                                      fill="currentColor" />
                                                  <path
                                                      d="M6 6C5.4 6 5 5.6 5 5V3C5 2.4 5.4 2 6 2C6.6 2 7 2.4 7 3V5C7 5.6 6.6 6 6 6ZM11 5V3C11 2.4 10.6 2 10 2C9.4 2 9 2.4 9 3V5C9 5.6 9.4 6 10 6C10.6 6 11 5.6 11 5ZM15 5V3C15 2.4 14.6 2 14 2C13.4 2 13 2.4 13 3V5C13 5.6 13.4 6 14 6C14.6 6 15 5.6 15 5ZM19 5V3C19 2.4 18.6 2 18 2C17.4 2 17 2.4 17 3V5C17 5.6 17.4 6 18 6C18.6 6 19 5.6 19 5Z"
                                                      fill="currentColor" />
                                                  <path
                                                      d="M8.8 13.1C9.2 13.1 9.5 13 9.7 12.8C9.9 12.6 10.1 12.3 10.1 11.9C10.1 11.6 10 11.3 9.8 11.1C9.6 10.9 9.3 10.8 9 10.8C8.8 10.8 8.6 10.8 8.4 10.9C8.2 11 8.1 11.1 8 11.2C7.9 11.3 7.8 11.4 7.7 11.6C7.6 11.8 7.5 11.9 7.5 12.1C7.5 12.2 7.4 12.2 7.3 12.3C7.2 12.4 7.1 12.4 6.9 12.4C6.7 12.4 6.6 12.3 6.5 12.2C6.4 12.1 6.3 11.9 6.3 11.7C6.3 11.5 6.4 11.3 6.5 11.1C6.6 10.9 6.8 10.7 7 10.5C7.2 10.3 7.5 10.1 7.9 10C8.3 9.9 8.6 9.8 9.1 9.8C9.5 9.8 9.8 9.9 10.1 10C10.4 10.1 10.7 10.3 10.9 10.4C11.1 10.5 11.3 10.8 11.4 11.1C11.5 11.4 11.6 11.6 11.6 11.9C11.6 12.3 11.5 12.6 11.3 12.9C11.1 13.2 10.9 13.5 10.6 13.7C10.9 13.9 11.2 14.1 11.4 14.3C11.6 14.5 11.8 14.7 11.9 15C12 15.3 12.1 15.5 12.1 15.8C12.1 16.2 12 16.5 11.9 16.8C11.8 17.1 11.5 17.4 11.3 17.7C11.1 18 10.7 18.2 10.3 18.3C9.9 18.4 9.5 18.5 9 18.5C8.5 18.5 8.1 18.4 7.7 18.2C7.3 18 7 17.8 6.8 17.6C6.6 17.4 6.4 17.1 6.3 16.8C6.2 16.5 6.1 16.3 6.1 16.1C6.1 15.9 6.2 15.7 6.3 15.6C6.4 15.5 6.6 15.4 6.8 15.4C6.9 15.4 7 15.4 7.1 15.5C7.2 15.6 7.3 15.6 7.3 15.7C7.5 16.2 7.7 16.6 8 16.9C8.3 17.2 8.6 17.3 9 17.3C9.2 17.3 9.5 17.2 9.7 17.1C9.9 17 10.1 16.8 10.3 16.6C10.5 16.4 10.5 16.1 10.5 15.8C10.5 15.3 10.4 15 10.1 14.7C9.8 14.4 9.5 14.3 9.1 14.3C9 14.3 8.9 14.3 8.7 14.3C8.5 14.3 8.4 14.3 8.4 14.3C8.2 14.3 8 14.2 7.9 14.1C7.8 14 7.7 13.8 7.7 13.7C7.7 13.5 7.8 13.4 7.9 13.2C8 13 8.2 13 8.5 13H8.8V13.1ZM15.3 17.5V12.2C14.3 13 13.6 13.3 13.3 13.3C13.1 13.3 13 13.2 12.9 13.1C12.8 13 12.7 12.8 12.7 12.6C12.7 12.4 12.8 12.3 12.9 12.2C13 12.1 13.2 12 13.6 11.8C14.1 11.6 14.5 11.3 14.7 11.1C14.9 10.9 15.2 10.6 15.5 10.3C15.8 10 15.9 9.8 15.9 9.7C15.9 9.6 16.1 9.6 16.3 9.6C16.5 9.6 16.7 9.7 16.8 9.8C16.9 9.9 17 10.2 17 10.5V17.2C17 18 16.7 18.4 16.2 18.4C16 18.4 15.8 18.3 15.6 18.2C15.4 18.1 15.3 17.8 15.3 17.5Z"
                                                      fill="currentColor" />
                                              </svg>
                                          </span>
                                          <!--end::Icon-->
                                          <input class="form-control ps-12" placeholder="Informe a data" name="data_competencia"
                                              id="data" />
                                      </div>
                                  </div>
                                  <!--begin::Input group - Target Title-->
                                  <div class="col-md-8 fv-row">
                                      <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                          <span class="required">Descrição</span>
                                      </label>
                                      <input type="text" class="form-control" placeholder="Informe a descricão"
                                          name="descricao" id="descricao" />
                                  </div>

                                  <!--end::Input group - Target Title-->
                                  <!--begin::Input group - Valor-->
                                  <div class="col-md-2 fv-row">
                                      <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                          <span class="required">Valor</span>
                                          <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                                              title="Informe o valor da despesa"></i>
                                      </label>
                                      <div class="input-group mb-3">
                                          <span class="input-group-text" id="basic-addon1">R$</span>
                                          <input type="text" class="form-control" name="valor" id="valor2"
                                              placeholder="0,00" aria-label="Username" aria-describedby="basic-addon1">
                                      </div>

                                  </div>
                                  <!--end::Input group - Valor-->
                              </div>
                              <!--begin::Input group - Assign & Due Date-->
                              <div class="row g-9 mb-8">
                                <div class="col-md-6 fv-row">
                                    <label class="required fs-6 fw-semibold mb-2">Lançamento Padrão</label>
                                    <div class="input-group">
                                        <select name="lancamento_padraos_id" id="lancamento_padraos_id"
                                            data-control="select2" data-dropdown-css-class="auto"
                                            class="form-select"
                                            data-placeholder="Escolha um Lançamento...">
                                            <option value=""></option> <!-- Opção vazia para o placeholder -->
                                            @foreach ($lps as $lp)
                                                <option value="{{ $lp->id }}" data-description="{{ $lp->description }}">
                                                    {{ $lp->description }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('lancamento_padraos_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                  <div class="col-md-4 fv-row">
                                      <label class="fs-5 fw-semibold mb-2">Centro de Custo</label>
                                      <div class="input-group">
                                          <select name="cost_centers_i selectpicker" data-live-search="true"id="banco_id"
                                              class="form-select @error('cost_center_id') is-invalid @enderror"
                                              data-control="select2" data-dropdown-css-class="auto"
                                              data-placeholder="Selecione o Centro de Custo">
                                              <!-- Placeholder configurado aqui -->
                                              @foreach ($centrosAtivos as $centrosAtivos)
                                                  <option value="{{ $centrosAtivos->id }}">{{ $centrosAtivos->name }}
                                                  </option>
                                              @endforeach
                                          </select>
                                      </div>
                                      @error('centro')
                                          <div class="text-danger">{{ $message }}</div>
                                      @enderror
                                  </div>

                              </div>
                              <!--end::Input group - Assign & Due Date-->

                          </div>
                      </div>

                      <!--begin::Card-->
                      <div class="card mb-xl-10 pt-5">
                          <div class="card-body px-10">

                              <!-- 1) Opção de "Repetir o Lançamento?" (se desejar manter) -->
                              <!-- Seção de "Repetir o Lançamento?" -->
                              {{-- <div class="d-flex flex-stack mb-8">
                                  <div class="row g-9 w-100">
                                      <!-- Checkbox -->
                                      <div class="col-md-2 fv-row d-flex align-items-center">
                                          <label class="form-check form-switch form-check-custom form-check-solid">
                                              <span class="form-check-label fw-semibold text-muted me-2">
                                                  Repetir o Lançamento?
                                              </span>
                                               <input class="form-check-input h-30px w-50px" name="repetir"
                                                  type="checkbox" id="repetir-lancamento" />
                                          </label>
                                      </div>

                                      <!-- Campos de recorrência (por padrão, escondidos) -->
                                      <div id="campos-recorrencia" class="col-md-10 row" style="display: none;">
                                          <!-- Repetir a cada -->
                                          <div class="fv-row col-md-3 ">
                                              <label class="fs-6 fw-semibold mb-2">Repetir a cada</label>
                                              <input type="number" class="form-control" placeholder="Ex: 1"
                                                  name="repetir_a_cada" min="1" />
                                          </div>

                                          <!-- Frequência -->
                                          <div class="fv-row col-md-3 ">
                                              <label class="fs-6 fw-semibold mb-2">Frequência</label>
                                              <select class="form-select" name="frequencia">
                                                  <option value="">Selecione</option>
                                                  <option value="diario">Dias</option>
                                                  <option value="semanal">Semanas</option>
                                                  <option value="mensal">Meses</option>
                                                  <option value="anual">Anos</option>
                                              </select>
                                          </div>

                                          <!-- Término da recorrência -->
                                          <div class="fv-row col-md-3 ">
                                              <label class="fs-6 fw-semibold mb-2">Após quantas ocorrências</label>
                                              <input type="number" class="form-control" placeholder="Ex: 1"
                                                  name="apos_ocorrencias" min="1" />
                                          </div>
                                      </div>
                                  </div>
                              </div> --}}
                              <!--end::Opção de "Repetir o Lançamento?" -->
                              <!--end::Opção de "Repetir o Lançamento?" -->

                              <!-- 2) Título da seção -->
                              <h3><span class="mb-3">Condição de pagamento</span></h3>
                              <div class="separator my-5"></div>

                              <!-- 3) Linha com Parcelamento, Vencimento, Forma de pagamento, Conta de pagamento, Pago, Agendado -->
                              <div class="row g-9 mb-8">
                                  <!-- Parcelamento -->
                                  <div class="col-md-2 fv-row">
                                      <label class="required fs-6 fw-semibold mb-2">Parcelamento</label>
                                      <select id="parcelamento" name="parcelamento" class="form-select">
                                          <option value="">Nº de parcelas</option>
                                          <option value="1">À vista</option>
                                          @for ($i = 2; $i <= 100; $i++)
                                              <option value="{{ $i }}">{{ $i }}x</option>
                                          @endfor
                                      </select>
                                  </div>


                                  <!-- Vencimento -->
                                  <div class="col-md-2 fv-row">
                                      <label class="required fs-6 fw-semibold mb-2">
                                          1º vencimento
                                      </label>
                                      <div class="position-relative d-flex align-items-center">
                                          <!--begin::Icon-->
                                          <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                              <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                  xmlns="http://www.w3.org/2000/svg">
                                                  <path opacity="0.3"
                                                      d="M21 22H3C2.4 22 2 21.6 2 21V5C2 4.4 2.4 4 3 4H21C21.6 4 22 4.4 22 5V21C22 21.6 21.6 22 21 22Z"
                                                      fill="currentColor" />
                                                  <path
                                                      d="M6 6C5.4 6 5 5.6 5 5V3C5 2.4 5.4 2 6 2C6.6 2 7 2.4 7 3V5C7 5.6 6.6 6 6 6ZM11 5V3C11 2.4 10.6 2 10 2C9.4 2 9 2.4 9 3V5C9 5.6 9.4 6 10 6C10.6 6 11 5.6 11 5ZM15 5V3C15 2.4 14.6 2 14 2C13.4 2 13 2.4 13 3V5C13 5.6 13.4 6 14 6C14.6 6 15 5.6 15 5ZM19 5V3C19 2.4 18.6 2 18 2C17.4 2 17 2.4 17 3V5C17 5.6 17.4 6 18 6C18.6 6 19 5.6 19 5Z"
                                                      fill="currentColor" />
                                                  <path
                                                      d="M8.8 13.1C9.2 13.1 9.5 13 9.7 12.8C9.9 12.6 10.1 12.3 10.1 11.9C10.1 11.6 10 11.3 9.8 11.1C9.6 10.9 9.3 10.8 9 10.8C8.8 10.8 8.6 10.8 8.4 10.9C8.2 11 8.1 11.1 8 11.2C7.9 11.3 7.8 11.4 7.7 11.6C7.6 11.8 7.5 11.9 7.5 12.1C7.5 12.2 7.4 12.2 7.3 12.3C7.2 12.4 7.1 12.4 6.9 12.4C6.7 12.4 6.6 12.3 6.5 12.2C6.4 12.1 6.3 11.9 6.3 11.7C6.3 11.5 6.4 11.3 6.5 11.1C6.6 10.9 6.8 10.7 7 10.5C7.2 10.3 7.5 10.1 7.9 10C8.3 9.9 8.6 9.8 9.1 9.8C9.5 9.8 9.8 9.9 10.1 10C10.4 10.1 10.7 10.3 10.9 10.4C11.1 10.5 11.3 10.8 11.4 11.1C11.5 11.4 11.6 11.6 11.6 11.9C11.6 12.3 11.5 12.6 11.3 12.9C11.1 13.2 10.9 13.5 10.6 13.7C10.9 13.9 11.2 14.1 11.4 14.3C11.6 14.5 11.8 14.7 11.9 15C12 15.3 12.1 15.5 12.1 15.8C12.1 16.2 12 16.5 11.9 16.8C11.8 17.1 11.5 17.4 11.3 17.7C11.1 18 10.7 18.2 10.3 18.3C9.9 18.4 9.5 18.5 9 18.5C8.5 18.5 8.1 18.4 7.7 18.2C7.3 18 7 17.8 6.8 17.6C6.6 17.4 6.4 17.1 6.3 16.8C6.2 16.5 6.1 16.3 6.1 16.1C6.1 15.9 6.2 15.7 6.3 15.6C6.4 15.5 6.6 15.4 6.8 15.4C6.9 15.4 7 15.4 7.1 15.5C7.2 15.6 7.3 15.6 7.3 15.7C7.5 16.2 7.7 16.6 8 16.9C8.3 17.2 8.6 17.3 9 17.3C9.2 17.3 9.5 17.2 9.7 17.1C9.9 17 10.1 16.8 10.3 16.6C10.5 16.4 10.5 16.1 10.5 15.8C10.5 15.3 10.4 15 10.1 14.7C9.8 14.4 9.5 14.3 9.1 14.3C9 14.3 8.9 14.3 8.7 14.3C8.5 14.3 8.4 14.3 8.4 14.3C8.2 14.3 8 14.2 7.9 14.1C7.8 14 7.7 13.8 7.7 13.7C7.7 13.5 7.8 13.4 7.9 13.2C8 13 8.2 13 8.5 13H8.8V13.1ZM15.3 17.5V12.2C14.3 13 13.6 13.3 13.3 13.3C13.1 13.3 13 13.2 12.9 13.1C12.8 13 12.7 12.8 12.7 12.6C12.7 12.4 12.8 12.3 12.9 12.2C13 12.1 13.2 12 13.6 11.8C14.1 11.6 14.5 11.3 14.7 11.1C14.9 10.9 15.2 10.6 15.5 10.3C15.8 10 15.9 9.8 15.9 9.7C15.9 9.6 16.1 9.6 16.3 9.6C16.5 9.6 16.7 9.7 16.8 9.8C16.9 9.9 17 10.2 17 10.5V17.2C17 18 16.7 18.4 16.2 18.4C16 18.4 15.8 18.3 15.6 18.2C15.4 18.1 15.3 17.8 15.3 17.5Z"
                                                      fill="currentColor" />
                                              </svg>
                                          </span>
                                          <!--end::Icon-->
                                          <input class="form-control ps-12" placeholder="Informe a data"
                                              name="vencimento" id="vencimento" />
                                      </div>
                                  </div>

                                  <!-- Forma de pagamento -->
                                  <div class="col-md-3 fv-row">
                                    <label class="fs-6 fw-semibold mb-2">Forma de pagamento</label>
                                    <select name="forma_pagamento" class="form-select" data-control="select2" data-placeholder="Selecione a forma de pagamento">
                                        <option value="">Selecione</option>
                                        @foreach ($formasPagamento as $forma)
                                            <option value="{{ $forma->id }}">{{ $forma->nome }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                  <!-- Conta de pagamento -->
                                  <div class="col-md-3 fv-row">
                                      <label class="fs-6 fw-semibold mb-2">Centro de Custo</label>
                                      <select name="conta_pagamento" class="form-select">
                                          @foreach ($todasEntidades as $entidade)
                                              <option value="{{ $entidade->id }}">{{ $entidade->nome }}
                                              </option>
                                          @endforeach
                                      </select>
                                  </div>

                              </div>
                              <!--end::Linha com inputs principais-->

                              <!-- 4) Abas: Observações e Anexos -->
                              <ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
                                  <li class="nav-item">
                                      <a class="nav-link active" data-bs-toggle="tab"
                                          href="#kt_tab_pane_observacoes">Observações</a>
                                  </li>
                                  <li class="nav-item">
                                      <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_pane_anexos">Anexos</a>
                                  </li>
                              </ul>

                              <div class="tab-content" id="myTabContent">
                                  <!-- Observações -->
                                  <div class="tab-pane fade show active" id="kt_tab_pane_observacoes"
                                      role="tabpanel">
                                      <div class="mb-5">
                                          <label class="fs-6 fw-semibold mb-2">Observações</label>
                                          <textarea class="form-control" name="observacoes" rows="3" maxlength="250"
                                              placeholder="Descreva observações relevantes..."></textarea>
                                          <span class="fs-6 text-muted">Insira no máximo 250 caracteres</span>
                                      </div>
                                  </div>

                                  <!-- Anexos -->
                                  <div class="tab-pane fade" id="kt_tab_pane_anexos" role="tabpanel">
                                      <label class="fs-6 fw-semibold mb-2">Anexos</label>
                                      <input type="file" name="files[]" multiple />
                                      <!-- Se estiver usando Kendo Upload, substitua por seu script:
                                        <script>
                                            $("#photos2").kendoUpload({
                                                ...
                                            });
                                        </script>
                                        -->
                                  </div>
                              </div>
                              <!--end::Aba de Observações e Anexos-->

                          </div>
                      </div>
                      <!--end::Card-->

                      <!-- Script para exibir/esconder campos de recorrência -->
                  </form>
                  <!--end::Form-->
              </div>
              <!--end::Modal body-->

              <!--begin::Modal footer-->
              <div class="modal-footer me-10">


                  <div class="text-center">
                      <button type="reset" id="kt_modal_new_target_cancel"
                          class="btn btn-light me-3">Cancel</button>
                      <!-- Split dropup button -->
                      <div class="btn-group dropup">
                          <!-- Botão principal -->
                          <button type="submit" id="kt_modal_new_target_submit" class="btn btn-primary">
                              <span class="indicator-label">Enviar</span>
                              <span class="indicator-progress">Please wait...
                                  <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>

                          </button>
                          <!-- Botão de dropup -->
                          <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split"
                              data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                              <span class="sr-only">Toggle Dropdown</span>
                          </button>
                          <!-- Opções do dropup -->
                          <div class="dropdown-menu">
                              <a class="dropdown-item" href="#" id="kt_modal_new_target_clone">Salvar e
                                  Clonar</a>
                              <a class="dropdown-item" href="#" id="kt_modal_new_target_novo">Salvar e em
                                  Branco</a>
                          </div>
                      </div>
                  </div>
                  <!--end::Actions-->
              </div>
              <!--end::Modal footer-->
          </div>
          <!--end::Modal content-->
      </div>
      <!--end::Modal dialog-->

  </div>
  <!--end::Modal - New Target-->
