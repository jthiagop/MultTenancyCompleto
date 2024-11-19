                                <div class="modal fade" id="kt_modal_new_ticket" tabindex="-1" aria-hidden="true">
                                    <!--begin::Modal dialog-->
                                    <div class="modal-dialog modal-dialog-centered mw-750px">
                                        <!--begin::Modal content-->
                                        <div class="modal-content rounded">
                                            <!--begin::Modal header-->
                                            <div class="modal-header pb-0 border-0 justify-content-end">
                                                <!--begin::Close-->
                                                <div class="btn btn-sm btn-icon btn-active-color-primary"
                                                    data-bs-dismiss="modal">
                                                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
                                                    <span class="svg-icon svg-icon-1">
                                                        <svg width="24" height="24" viewBox="0 0 24 24"
                                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <rect opacity="0.5" x="6" y="17.3137" width="16"
                                                                height="2" rx="1"
                                                                transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                                            <rect x="7.41422" y="6" width="16" height="2"
                                                                rx="1" transform="rotate(45 7.41422 6)"
                                                                fill="currentColor" />
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
                                                <form id="kt_modal_new_ticket_form" class="form" action="{{ route('fieis.store') }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <!--begin::Heading-->
                                                    <div class="mb-13 text-center">
                                                        <!--begin::Title-->
                                                        <!--begin::Heading-->
                                                        <h1 class="mb-3">Cadastro de Fiéis</h1>
                                                        <div class="text-gray-400 fw-semibold fs-5">Preencha as
                                                            informações abaixo para cadastrar um novo fiel.
                                                        </div>
                                                        <!--end::Title-->
                                                        <!--begin::Description-->
                                                        <!--end::Description-->
                                                    </div>
                                                    <!--end::Heading-->
                                                    <!-- Avatar -->
                                                    <div class="fv-row mb-7 text-center">
                                                        <div class="d-flex justify-content-center">
                                                            <div class="image-input image-input-outline image-input-placeholder" data-kt-image-input="true">
                                                                <!-- Wrapper para a imagem -->
                                                                <div class="image-input-wrapper w-150px h-150px rounded-circle"
                                                                    style="background-image: url('/assets/media/avatars/blank.png');"></div>

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

                                                    <div class="row g-9 mb-8">
                                                        <!--begin::Col-->
                                                        <div class="col-md-8 fv-row">
                                                            <!--begin::Label-->
                                                            <label
                                                                class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                                                <span class="required">Nome Completo</span>
                                                                <i class="fas fa-exclamation-circle ms-2 fs-7"
                                                                    data-bs-toggle="tooltip"
                                                                    title="Specify a subject for your issue"></i>
                                                            </label>
                                                            <!--end::Label-->
                                                            <input type="text"
                                                                class="form-control form-control-solid"
                                                                placeholder="Nome Completo" name="nome_completo" />
                                                        </div>
                                                        <!--end::Col-->
                                                        <!--begin::Col-->
                                                        <div class="col-md-4 fv-row">
                                                            <label class="required fw-semibold fs-6 mb-2">Data de
                                                                Nascimento</label>
                                                            <!--begin::Input-->
                                                            <div class="position-relative d-flex align-items-center">
                                                                <!--begin::Icon-->
                                                                <div
                                                                    class="symbol symbol-20px me-4 position-absolute ms-4">
                                                                    <span class="symbol-label bg-secondary">
                                                                        <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
                                                                        <span class="svg-icon">
                                                                            <svg width="24" height="24"
                                                                                viewBox="0 0 24 24" fill="none"
                                                                                xmlns="http://www.w3.org/2000/svg">
                                                                                <rect x="2" y="2" width="9"
                                                                                    height="9" rx="2"
                                                                                    fill="currentColor" />
                                                                                <rect opacity="0.3" x="13" y="2"
                                                                                    width="9" height="9"
                                                                                    rx="2"
                                                                                    fill="currentColor" />
                                                                                <rect opacity="0.3" x="13" y="13"
                                                                                    width="9" height="9"
                                                                                    rx="2"
                                                                                    fill="currentColor" />
                                                                                <rect opacity="0.3" x="2" y="13"
                                                                                    width="9" height="9"
                                                                                    rx="2"
                                                                                    fill="currentColor" />
                                                                            </svg>
                                                                        </span>
                                                                        <!--end::Svg Icon-->
                                                                    </span>
                                                                </div>
                                                                <!--end::Icon-->
                                                                <!--begin::Datepicker-->
                                                                <input class="form-control form-control-solid ps-12"
                                                                    placeholder="Data nascimento" name="data_nascimento" />
                                                                <!--end::Datepicker-->
                                                            </div>
                                                            <!--end::Input-->
                                                        </div>
                                                        <!--end::Col-->
                                                    </div>
                                                    <!--end::Input group-->
                                                    <!--end::Input group-->
                                                    <!--begin::Input group-->
                                                    <div class="row g-9 mb-8">
                                                        <!--begin::Col-->
                                                        <div class="col-md-4 fv-row">
                                                            <label class="required fw-semibold fs-6 mb-2">Sexo</label>
                                                            <select name="sexo" class="form-select form-select-solid"
                                                                required>
                                                                <option value="">Selecione...</option>
                                                                <option value="M">Masculino</option>
                                                                <option value="F">Feminino</option>
                                                            </select>
                                                        </div>
                                                        <!--end::Col-->
                                                        <!--begin::Col-->
                                                        <div class="col-md-8 fv-row">
                                                            <!-- CPF e RG -->
                                                            <div class="row mb-7">
                                                                <div class="col-md-6">
                                                                    <label class="fw-semibold fs-6 mb-2">CPF</label>
                                                                    <input type="text" name="cpf"
                                                                        id="cpf"
                                                                        class="form-control form-control-solid"
                                                                        placeholder="000.000.000-00" required />
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="fw-semibold fs-6 mb-2">RG</label>
                                                                    <input type="text" name="rg"
                                                                        id="rg"
                                                                        class="form-control form-control-solid"
                                                                        placeholder="XX.XXX.XXX-X" />
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!--end::Col-->
                                                    </div>
                                                    <!--end::Input group-->
                                                    <!--begin::Input group-->
                                                    <div class="row g-9 mb-8">
                                                        <!--begin::Col - Profissão-->
                                                        <div class="col-md-6 fv-row">
                                                            <div class="fv-row mb-7">
                                                                <label class="fw-semibold fs-6 mb-2">Profissão</label>
                                                                <input type="text" name="profissao"
                                                                    class="form-control form-control-solid"
                                                                    placeholder="Profissão" />
                                                            </div>
                                                        </div>
                                                        <!--end::Col-->

                                                        <!--begin::Col - Estado Civil-->
                                                        <div class="col-md-6 fv-row">
                                                            <div class="fv-row mb-7">
                                                                <label class="fw-semibold fs-6 mb-2">Estado
                                                                    Civil</label>
                                                                <select name="estado_civil"
                                                                    class="form-select form-select-solid">
                                                                    <option value="">Selecione...</option>
                                                                    <option value="Amasiado(a)">Amasiado(a)</option>
                                                                    <option value="Solteiro(a)">Solteiro(a)</option>
                                                                    <option value="Casado(a)">Casado(a)</option>
                                                                    <option value="Divorciado(a)">Divorciado(a)</option>
                                                                    <option value="Viúvo(a)">Viúvo(a)</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <!--end::Col-->
                                                    </div>

                                                    <!--begin::Input group - Telefones e Email-->
                                                    <div class="row g-9 mb-8">
                                                        <!--begin::Col - Telefones-->
                                                        <div class="col-md-6 fv-row">
                                                            <div class="row mb-7">
                                                                <!-- Telefone -->
                                                                <div class="col-md-6">
                                                                    <label
                                                                        class="fw-semibold fs-6 mb-2">Telefone</label>
                                                                    <input type="text" id="telefone"
                                                                        name="telefone"
                                                                        class="form-control form-control-solid"
                                                                        placeholder="(99) 9.9999-9999" />
                                                                </div>
                                                                <!-- Telefone Secundário -->
                                                                <div class="col-md-6">
                                                                    <label class="fw-semibold fs-6 mb-2">Telefone
                                                                        Secundário</label>
                                                                    <input type="text" id="telefone"
                                                                        name="telefone_secundario"
                                                                        class="form-control form-control-solid"
                                                                        placeholder="(99) 9.9999-9999" />
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!--end::Col-->

                                                        <!--begin::Col - Email-->
                                                        <div class="col-md-6 fv-row">
                                                            <div class="fv-row mb-7">
                                                                <label class="fw-semibold fs-6 mb-2">Email</label>
                                                                <input type="email" name="email"
                                                                    class="form-control form-control-solid"
                                                                    placeholder="example@domain.com" />
                                                            </div>
                                                        </div>
                                                        <!--end::Col-->
                                                    </div>
                                                    <!--end::Input group-->

                                                    <!--begin::Input group-->

                                                    <!--begin::Grupo de Entrada - Endereço-->
                                                    <!-- CEP -->
                                                    <div class="row mb-7">
                                                        <div class="col-md-3">
                                                        <label class="fw-semibold fs-6 mb-2">CEP</label>
                                                        <input type="text" name="cep" id="cep"
                                                            class="form-control form-control-solid"
                                                            placeholder="00000-000" />
                                                        </div>
                                                            <div class="col-md-5">
                                                                <label class="fw-semibold fs-6 mb-2">Bairro</label>
                                                                <input type="text" name="bairro" id="bairro"
                                                                    class="form-control form-control-solid"
                                                                    placeholder="Bairro" />
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label class="fw-semibold fs-6 mb-2">Cidade</label>
                                                                <input type="text" name="cidade" id="localidade"
                                                                    class="form-control form-control-solid"
                                                                    placeholder="Cidade" />
                                                            </div>
                                                    </div>

                                                    <!-- Endereço Completo -->
                                                    <div class="row mb-7">
                                                        <div class="col-md-8">
                                                        <label class="fw-semibold fs-6 mb-2">Endereço</label>
                                                        <input type="text" name="endereco" id="logradouro"
                                                            class="form-control form-control-solid"
                                                            placeholder="Rua, número" />
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="fw-semibold fs-6 mb-2">Estado</label>
                                                            <select id="uf" name="estado"
                                                                class="form-select form-select-solid"
                                                                data-control="select2" data-hide-search="true"
                                                                data-placeholder="Selecione o Estado">
                                                                <option value="">Selecione o Estado</option>
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
                                                    </div>
                                                    <!--end::Input group-->
                                                    <!--begin::Input group-->
                                                    <!--begin::Input group - Notifications-->
                                                    <div class="mb-15 fv-row">
                                                        <!--begin::Wrapper-->
                                                        <div class="d-flex flex-stack">
                                                            <!--begin::Label-->
                                                            <div class="fw-semibold me-5">
                                                                <label for="notifications"
                                                                    class="fs-6">Notificações</label>
                                                                <div class="fs-7 text-gray-400">Permitir notificações
                                                                    por telefone ou email</div>
                                                            </div>
                                                            <!--end::Label-->

                                                            <!--begin::Checkboxes-->
                                                            <div class="d-flex align-items-center">
                                                                <!--begin::Checkbox - Email-->
                                                                <label
                                                                    class="form-check form-check-custom form-check-solid me-10">
                                                                    <input class="form-check-input h-20px w-20px"
                                                                        type="checkbox" name="notifications[]"
                                                                        value="email" id="notification_email"
                                                                        {{ in_array('email', old('notifications', $fiel->notifications ?? [])) ? 'checked' : '' }} />
                                                                    <span
                                                                        class="form-check-label fw-semibold">Email</span>
                                                                </label>
                                                                <!--end::Checkbox-->

                                                                <!--begin::Checkbox - Phone-->
                                                                <label
                                                                    class="form-check form-check-custom form-check-solid">
                                                                    <input class="form-check-input h-20px w-20px"
                                                                        type="checkbox" name="notifications[]"
                                                                        value="phone" id="notification_phone"
                                                                        {{ in_array('phone', old('notifications', $fiel->notifications ?? [])) ? 'checked' : '' }} />
                                                                    <span
                                                                        class="form-check-label fw-semibold">Telefone</span>
                                                                </label>
                                                                <!--end::Checkbox-->
                                                            </div>
                                                            <!--end::Checkboxes-->
                                                        </div>
                                                        <!--end::Wrapper-->
                                                    </div>
                                                    <!--end::Input group-->

                                                    <!--begin::Actions-->
                                                    <div class="text-center">
                                                        <button type="reset" id="kt_modal_new_ticket_cancel"
                                                            class="btn btn-light me-3">Cancel</button>
                                                        <button type="submit" id="kt_modal_new_ticket_submit"
                                                            class="btn btn-primary">
                                                            <span class="indicator-label">Submit</span>
                                                            <span class="indicator-progress">Please wait...
                                                                <span
                                                                    class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
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
