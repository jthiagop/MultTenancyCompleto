<div class="modal fade" id="DM_modal_store_escritura" tabindex="-1" aria-hidden="true">
    			<!--begin::Modal dialog-->
			<div class="modal-dialog mw-800px">
				<!--begin::Modal content-->
				<div class="modal-content">
					<!--begin::Modal header-->
					<div class="modal-header pb-0 border-0">
						<!--begin::Close-->
						<div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
							<!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
							<span class="svg-icon svg-icon-1">
								<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
									<rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
									<rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
								</svg>
							</span>
							<!--end::Svg Icon-->
						</div>
						<!--end::Close-->
					</div>
					<!--end::Modal header-->
					<!--begin::Modal body-->
					<div class="modal-body scroll-y mx-5 mx-xl-10 pt-0 pb-15">
						<!--begin::Heading-->
						<div class="text-center mb-13">
							<!--begin::Title-->
							<h1 class="d-flex justify-content-center align-items-center mb-3">Cadastrar Outorgante e Outorgado</h1>
							<!--end::Title-->
							<!--begin::Description-->
							<div class="text-muted fw-semibold fs-5">Código RID do Patrimônio:
							<a href="" class="link-primary fw-bold"><strong>{{ $patrimonio->codigo_rid }}</strong></a>.</div>
							<!--end::Description-->
						</div>
						<!--end::Heading-->
						<!--begin::Users-->
						<div class="mh-475px scroll-y me-n7 pe-7">
                            <form id="kt_modal_new_address_form" class="form fv-plugins-bootstrap5 fv-plugins-framework" method="POST" action="{{ route('escritura.store') }}">
                                @csrf
                                <!-- Dados do Outorgante -->
                                <input type="hidden" name="patrimonio_id" value="{{ $patrimonio->id }}">

                                <div class="mb-8">
                                  <div class="form-floating">
                                    <input type="text" class="form-control form-control-solid" id="outorgante" name="outorgante" placeholder="Nome do Outorgante" value="{{ old('outorgante', optional($patrimonio->escrituras->last())->outorgante ?? '') }}">
                                    <label for="outorgante">
                                      Nome do Outorgante
                                      <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip" title="Informe os dados do Outorgante (quem concede ou transfere)"></i>
                                    </label>
                                  </div>
                                </div>
                                <div class="row g-9 mb-7">
                                  <div class="col-md-7">
                                    <div class="form-floating">
                                      <input type="email" class="form-control form-control-solid" id="outorgante_email" name="outorgante_email" placeholder="nome@exemplo.com.br" value="{{ old('outorgante_email', optional($patrimonio->escrituras->last())->outorgante_email ?? '') }}">
                                      <label for="outorgante_email">nome@exemplo.com.br</label>
                                    </div>
                                  </div>
                                  <div class="col-md-5">
                                    <div class="form-floating">
                                      <input type="text" class="form-control form-control-solid" id="outorgante_telefone" name="outorgante_telefone" placeholder="(00) 0.0000-0000" value="{{ old('outorgante_telefone', optional($patrimonio->escrituras->last())->outorgante_telefone ?? '') }}">
                                      <label for="outorgante_telefone">(00) 0.0000-0000</label>
                                    </div>
                                  </div>
                                </div>
                                <div class="row g-9 mb-7">
                                  <div class="col-md-8">
                                    <div class="form-floating">
                                      <input type="text" class="form-control form-control-solid" id="matricula" name="matricula" placeholder="Número da Matrícula" value="{{ old('matricula', optional($patrimonio->escrituras->last())->matricula ?? '') }}">
                                      <label for="matricula">Número da Matrícula</label>
                                    </div>
                                  </div>
                                  <div class="col-md-4">
                                    <div class="form-floating">
                                      <input type="date" class="form-control form-control-solid" id="aquisicao" name="aquisicao" placeholder="Selecione a data" value="{{ old('aquisicao', optional($patrimonio->escrituras->last())->aquisicao ?? '') }}">
                                      <label for="aquisicao">Selecione a data</label>
                                    </div>
                                  </div>
                                </div>
                                <hr class="separator separator-dashed border-secondary my-10" />
                                <!-- Dados do Outorgado -->
                                <div class="mb-8">
                                  <div class="form-floating">
                                    <input type="text" class="form-control form-control-solid" id="outorgado" name="outorgado" placeholder="Nome do Outorgado" value="{{ old('outorgado', optional($patrimonio->escrituras->last())->outorgado ?? '') }}">
                                    <label for="outorgado">
                                      Nome do Outorgado
                                      <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip" title="Informe os dados do Outorgado (quem recebe ou compra)"></i>
                                    </label>
                                  </div>
                                </div>
                                <div class="row g-9 mb-7">
                                  <div class="col-md-7">
                                    <div class="form-floating">
                                      <input type="email" class="form-control form-control-solid" id="outorgado_email" name="outorgado_email" placeholder="nome@exemplo.com.br" value="{{ old('outorgado_email', optional($patrimonio->escrituras->last())->outorgado_email ?? '') }}">
                                      <label for="outorgado_email">nome@exemplo.com.br</label>
                                    </div>
                                  </div>
                                  <div class="col-md-5">
                                    <div class="form-floating">
                                      <input type="text" class="form-control form-control-solid" id="outorgado_telefone" name="outorgado_telefone" placeholder="(00) 0.0000-0000" value="{{ old('outorgado_telefone', optional($patrimonio->escrituras->last())->outorgado_telefone ?? '') }}">
                                      <label for="outorgado_telefone">(00) 0.0000-0000</label>
                                    </div>
                                  </div>
                                </div>
                                <div class="row g-9 mb-7">
                                  <div class="col-md-4">
                                    <div class="form-floating position-relative">
                                        <input type="text" class="form-control form-control-solid" id="valor2" name="valor"
                                        placeholder="0,00" value="{{ old('valor2', optional($patrimonio->escrituras->last())->valor ?? '') }}">
                                      <label for="valor">Valor de Aquisição</label>
                                    </div>
                                  </div>
                                  <div class="col-md-4">
                                    <div class="form-floating">
                                      <input type="text" class="form-control form-control-solid" id="area_total" name="area_total" placeholder="Área Total" value="{{ old('area_total', optional($patrimonio->escrituras->last())->area_total ?? '') }}">
                                      <label for="area_total">Área Total</label>
                                    </div>
                                  </div>
                                  <div class="col-md-4">
                                    <div class="form-floating">
                                      <input type="text" class="form-control form-control-solid" id="area_privativa" name="area_privativa" placeholder="Área Privativa" value="{{ old('area_privativa', optional($patrimonio->escrituras->last())->area_privativa ?? '') }}">
                                      <label for="area_privativa">Área Privativa</label>
                                    </div>
                                  </div>
                                </div>
                                <div class="mb-8">
                                  <div class="form-floating">
                                    <textarea class="form-control form-control-solid" id="informacoes" name="informacoes" placeholder="Mais detalhes sobre o cadastro (máx. 250 caracteres)" style="height: 100px;">{{ old('informacoes', optional($patrimonio->informacoes)->informacoes ?? 'Sem escritura') }}</textarea>
                                    <label for="informacoes">Mais detalhes sobre o cadastro (máx. 250 caracteres)</label>
                                  </div>
                                  <small class="text-muted">Máximo 250 caracteres</small>
                                </div>
                                <!-- Modal Footer -->
                                <div class="modal-footer flex-center">
                                  <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">
                                    <i class="bi bi-box-arrow-right"></i> Sair
                                  </button>
                                  <button type="submit" id="kt_modal_new_address_submit" class="btn btn-primary">
                                    <i class="bi bi-arrow-repeat"></i> Atualizar
                                  </button>
                                </div>
                              </form>

						</div>
						<!--end::Users-->
					</div>
					<!--end::Modal Body-->
				</div>
				<!--end::Modal content-->
			</div>
			<!--end::Modal dialog-->
</div>
