									<!--begin::Modal - Customers - Add-->
									<div class="modal fade" id="DM_modal_add_sepultado" tabindex="-1" aria-hidden="true">
										<!--begin::Modal dialog-->
										<div class="modal-dialog modal-dialog-centered mw-650px">
											<!--begin::Modal content-->
											<div class="modal-content">
												<!--begin::Form-->
												<form class="form" action="#" id="DM_modal_add_sepultado_form" enctype="multipart/form-data">
                                                    @csrf <!-- Token CSRF para segurança -->
													<!--begin::Modal header-->
													<div class="modal-header" id="DM_modal_add_sepultado_header">
														<!--begin::Modal title-->
														<h2 class="fw-bold">Adicionar Sepultado</h2>
														<!--end::Modal title-->
														<!--begin::Close-->
														<div id="DM_modal_add_sepultado_close" class="btn btn-icon btn-sm btn-active-icon-primary">
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
													<div class="modal-body py-10 px-lg-17">
														<!--begin::Scroll-->
														<div class="scroll-y me-n7 pe-7" id="DM_modal_add_sepultado_scroll" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#DM_modal_add_sepultado_header" data-kt-scroll-wrappers="#DM_modal_add_sepultado_scroll" data-kt-scroll-offset="300px">                                                    <!-- Avatar -->
                                                            <div class="fv-row mb-7 text-center">
                                                                <div class="d-flex justify-content-center my-3">
                                                                    <div class="image-input image-input-outline image-input-placeholder" data-kt-image-input="true">
                                                                        <!-- Wrapper para a imagem -->
                                                                        <div class="image-input-wrapper w-150px h-150px rounded-circle"
                                                                            style="background-image: url('assets/media/avatars/blank.png');"></div>

                                                                        <!-- Botão para alterar o avatar -->
                                                                        <label class="btn btn-icon btn-circle btn-active-color-primary w-30px h-30px bg-body shadow position-absolute translate-middle bottom-0 start-100"
                                                                            data-kt-image-input-action="change" title="Alterar avatar">
                                                                            <i class="bi bi-pencil-fill fs-7"></i>
                                                                            <input type="file" name="avatar" accept=".png, .jpg, .jpeg" />
                                                                            <input type="hidden" name="avatar_remove" />
                                                                        </label>

                                                                        <!-- Botão para remover o avatar -->
                                                                        <span class="btn btn-icon btn-circle btn-active-color-primary w-30px h-30px bg-body shadow position-absolute translate-middle bottom-0 start-0"
                                                                            data-kt-image-input-action="remove" title="Remover avatar">
                                                                            <i class="bi bi-x fs-2"></i>
                                                                        </span>
                                                                    </div>
                                                                </div>

                                                                <div class="form-text mt-3">Tipos de arquivo permitidos: png, jpg, jpeg.</div>
                                                            </div>
															<!--begin::Input group-->
															<div class="fv-row mb-7">
																<!--begin::Label-->
																<label class="required fs-6 fw-semibold mb-2">Nome do Falecido</label>
																<!--end::Label-->
																<!--begin::Input-->
																<input type="text" class="form-control form-control-solid" placeholder="" name="nome" value="Sean Bean" />
																<!--end::Input-->
															</div>
															<!--end::Input group-->
															<!--begin::Input group-->
                                                            <div class="row g-9 mb-7">
                                                                <!--begin::Col-->
                                                                <div class="col-md-4 fv-row">
																<!--begin::Label-->
																<label class="fs-6 fw-semibold mb-2">
																	<span class="required">Data Nascimento</span>
																	<i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip" title="Data de nascimento"></i>
																</label>
																<!--end::Label-->
																<!--begin::Input-->
                                                                <input type="date" class="form-control form-control-solid" name="data_nascimento" value="{{ now()->format('Y-m-d') }}" />
																<!--end::Input-->
															    </div>
                                                                <!--End::Col -->
                                                                <!--begin::Col-->
                                                                <div class="col-md-4 fv-row">
																<!--begin::Label-->
																<label class="fs-6 fw-semibold mb-2">
																	<span class="required">Data Falecimento</span>
																	<i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip" title="Data de falecimento"></i>
																</label>
																<!--end::Label-->
																<!--begin::Input-->
                                                                <input type="date" class="form-control form-control-solid" name="data_falecimento" value="{{ now()->format('Y-m-d') }}" />
																<!--end::Input-->
															    </div>
                                                                <!--End::Col -->
                                                                <!--begin::Col-->
                                                                <div class="col-md-4 fv-row">
																<!--begin::Label-->
																<label class="fs-6 fw-semibold mb-2">
																	<span class="required">Email</span>
																	<i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip" title="Data do sepultamento"></i>
																</label>
																<!--end::Label-->
																<!--begin::Input-->
                                                                <input type="date" class="form-control form-control-solid" name="data_sepultamento" value="{{ now()->format('Y-m-d') }}" />
																<!--end::Input-->
															    </div>
                                                            </div>
															<!--end::Input group-->
															<!--begin::Input group-->
															<div class="fv-row mb-15">
																<!--begin::Label-->
																<label class="fs-6 fw-semibold mb-2">Description</label>
																<!--end::Label-->
																<!--begin::Input-->
																<input type="text" class="form-control form-control-solid" placeholder="" name="description" />
																<!--end::Input-->
															</div>
															<!--end::Input group-->
															<!--begin::Billing toggle-->
															<div class="fw-bold fs-3 rotate collapsible mb-7" data-bs-toggle="collapse" href="#DM_modal_add_sepultado_billing_info" role="button" aria-expanded="false" aria-controls="kt_customer_view_details">Shipping Information
															<span class="ms-2 rotate-180">
																<!--begin::Svg Icon | path: icons/duotune/arrows/arr072.svg-->
																<span class="svg-icon svg-icon-3">
																	<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																		<path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="currentColor" />
																	</svg>
																</span>
																<!--end::Svg Icon-->
															</span></div>
															<!--end::Billing toggle-->
															<!--begin::Billing form-->
															<div id="DM_modal_add_sepultado_billing_info" class="collapse show">
																<!--begin::Input group-->
																<div class="d-flex flex-column mb-7 fv-row">
																	<!--begin::Label-->
																	<label class="required fs-6 fw-semibold mb-2">Address Line 1</label>
																	<!--end::Label-->
																	<!--begin::Input-->
																	<input class="form-control form-control-solid" placeholder="" name="address1" value="101, Collins Street" />
																	<!--end::Input-->
																</div>
																<!--end::Input group-->
																<!--begin::Input group-->
																<div class="d-flex flex-column mb-7 fv-row">
																	<!--begin::Label-->
																	<label class="fs-6 fw-semibold mb-2">Address Line 2</label>
																	<!--end::Label-->
																	<!--begin::Input-->
																	<input class="form-control form-control-solid" placeholder="" name="address2" value="" />
																	<!--end::Input-->
																</div>
																<!--end::Input group-->
																<!--begin::Input group-->
																<div class="d-flex flex-column mb-7 fv-row">
																	<!--begin::Label-->
																	<label class="required fs-6 fw-semibold mb-2">Town</label>
																	<!--end::Label-->
																	<!--begin::Input-->
																	<input class="form-control form-control-solid" placeholder="" name="city" value="Melbourne" />
																	<!--end::Input-->
																</div>
																<!--end::Input group-->
																<!--begin::Input group-->
																<div class="row g-9 mb-7">
																	<!--begin::Col-->
																	<div class="col-md-6 fv-row">
																		<!--begin::Label-->
																		<label class="required fs-6 fw-semibold mb-2">State / Province</label>
																		<!--end::Label-->
																		<!--begin::Input-->
																		<input class="form-control form-control-solid" placeholder="" name="state" value="Victoria" />
																		<!--end::Input-->
																	</div>
																	<!--end::Col-->
																	<!--begin::Col-->
																	<div class="col-md-6 fv-row">
																		<!--begin::Label-->
																		<label class="required fs-6 fw-semibold mb-2">Post Code</label>
																		<!--end::Label-->
																		<!--begin::Input-->
																		<input class="form-control form-control-solid" placeholder="" name="postcode" value="3000" />
																		<!--end::Input-->
																	</div>
																	<!--end::Col-->
																</div>
																<!--end::Input group-->
																<!--begin::Input group-->
																<div class="d-flex flex-column mb-7 fv-row">
																	<!--begin::Label-->
																	<label class="fs-6 fw-semibold mb-2">
																		<span class="required">Country</span>
																		<i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip" title="Country of origination"></i>
																	</label>
																	<!--end::Label-->
																	<!--begin::Input-->
																	<select name="country" aria-label="Select a Country" data-control="select2" data-placeholder="Select a Country..." data-dropdown-parent="#DM_modal_add_sepultado" class="form-select form-select-solid fw-bold">
																		<option value="">Select a Country...</option>
																		<option value="AF">Afghanistan</option>
																		<option value="AX">Aland Islands</option>
																		<option value="AL">Albania</option>
																		<option value="DZ">Algeria</option>
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
																			<div class="fs-7 fw-semibold text-muted">If you need more info, please check budget planning</div>
																			<!--end::Input-->
																		</div>
																		<!--end::Label-->
																		<!--begin::Switch-->
																		<label class="form-check form-switch form-check-custom form-check-solid">
																			<!--begin::Input-->
																			<input class="form-check-input" name="billing" type="checkbox" value="1" id="DM_modal_add_sepultado_billing" checked="checked" />
																			<!--end::Input-->
																			<!--begin::Label-->
																			<span class="form-check-label fw-semibold text-muted" for="DM_modal_add_sepultado_billing">Yes</span>
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
														<!--end::Scroll-->
													</div>
													<!--end::Modal body-->
													<!--begin::Modal footer-->
													<div class="modal-footer flex-center">
														<!--begin::Button-->
														<button type="reset" id="DM_modal_add_sepultado_cancel" class="btn btn-light me-3">Discard</button>
														<!--end::Button-->
														<!--begin::Button-->
														<button type="submit" id="DM_modal_add_sepultado_submit" class="btn btn-primary">
															<span class="indicator-label">Submit</span>
															<span class="indicator-progress">Por favor, aguarde...
															<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
														</button>
														<!--end::Button-->
													</div>
													<!--end::Modal footer-->
												</form>
												<!--end::Form-->
											</div>
										</div>
									</div>
									<!--end::Modal - Customers - Add-->

                                    <script>
                                        $(document).ready(function() {
                                            $('#DM_modal_add_sepultado_form').submit(function(e) {
                                                e.preventDefault();  // Impede o envio tradicional do formulário

                                                var formData = new FormData(this);  // Coleta todos os dados do formulário

                                                // Envia os dados via AJAX
                                                $.ajax({
                                                    url: '{{ route('cemiterio.store') }}',  // Rota para processar os dados no backend
                                                    method: 'POST',
                                                    data: formData,
                                                    contentType: false,  // Necessário para enviar FormData
                                                    processData: false,  // Necessário para enviar FormData
                                                    success: function(response) {
                                                        // Se a autenticação for bem-sucedida, feche o modal e atualize o conteúdo
                                                        if (response.success) {
                                                            $('#DM_modal_add_sepultado').modal('hide');  // Fecha o modal
                                                            window.location.reload();  // Recarrega a página (opcional)
                                                        } else {
                                                            // Exibe mensagens de erro, caso haja
                                                            alert('Erro ao processar o formulário.');
                                                        }
                                                    },
                                                    error: function(xhr, status, error) {
                                                        // Exibe erro caso o envio falhe
                                                        alert('Erro no envio do formulário');
                                                    }
                                                });
                                            });
                                        });
                                    </script>
