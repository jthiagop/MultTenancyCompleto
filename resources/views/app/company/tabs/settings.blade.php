<div class="card">
    <!--begin::Card header-->
    <div class="card-header">
        <!--begin::Card title-->
        <div class="card-title fs-3 fw-bold">Configurações de Organismos</div>
        <!--end::Card title-->
    </div>
    <!--end::Card header-->
    <!--begin::Form-->
    <form method="POST" action="{{ route('company.update', $companyShow->id) }}" enctype="multipart/form-data"
        id="kt_account_profile_details_form" class="form">
        @csrf
        @method('PUT')
        <!--begin::Card body-->
        <div class="card-body p-9">
            <!--begin::Row-->
            <div class="row mb-5">
                <!--begin::Col-->
                <div class="col-xl-3">
                    <div class="fs-6 fw-semibold mt-2 mb-3">Brasão/logo</div>
                </div>
                <!--end::Col-->
                <!--begin::Col-->
                <div class="col-lg-8">
                    <!--begin::Image input-->
                    <div class="image-input image-input-outline" data-kt-image-input="true"
                        style="background-image: url('/assets/media/svg/avatars/blank.svg')">
                        <!--begin::Preview existing avatar-->
                        <div class="image-input-wrapper w-125px h-125px bgi-position-center"
                            style="background-size: cover; background-position: center; background-repeat: no-repeat;
                               background-image: url('{{ $companyShow->avatar ? route('file', ['path' => $companyShow->avatar]) : '/public/assets/media/avatars/blank.png' }}')">
                        </div>
                        <!--end::Preview existing avatar-->
                        <!--begin::Label-->
                        <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-white shadow"
                            data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change avatar">
                            <i class="bi bi-pencil-fill fs-7"></i>
                            <!--begin::Inputs-->
                            <input type="file" name="avatar" accept=".png, .jpg, .jpeg" />
                            <input type="hidden" name="avatar_remove" />
                            <!--end::Inputs-->
                        </label>
                        <!--end::Label-->
                        <!--begin::Cancel-->
                        <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-white shadow"
                            data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel avatar">
                            <i class="bi bi-x fs-2"></i>
                        </span>
                        <!--end::Cancel-->
                        <!--begin::Remove-->
                        <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-white shadow"
                            data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove avatar">
                            <i class="bi bi-x fs-2"></i>
                        </span>
                        <!--end::Remove-->
                    </div>
                    <!--end::Image input-->
                    <!--begin::Hint-->
                    <div class="form-text">Tipos de arquivos permitidos: png, jpg, jpeg.</div>
                    <!--end::Hint-->
                </div>
                <!--end::Col-->
            </div>
            <!--end::Row-->
            <!--begin::Row-->
            <div class="row mb-8">
                <!--begin::Col-->
                <div class="col-xl-3">
                    <div class="fs-6 fw-semibold mt-2 mb-3">Nome Organismo</div>
                </div>
                <!--end::Col-->
                <!--begin::Col-->
                <div class="col-xl-9 fv-row">
                    <input type="text" class="form-control form-control-solid" name="name"
                        value="{{ $companyShow->name }}" />
                </div>
            </div>
            <!--end::Row-->
            <!--begin::Row-->
            <div class="row mb-8">
                <!--begin::Col-->
                <div class="col-xl-3">
                    <div class="fs-6 fw-semibold mt-2 mb-3">CNPJ</div>
                </div>
                <!--end::Col-->
                <!--begin::Col-->
                <div class="col-xl-9 fv-row">
                    <input type="text" class="form-control form-control-solid" name="cnpj" id="cnpj"
                        value="{{ $companyShow->cnpj }}" />
                </div>
            </div>
            <!--end::Row-->
            <!--begin::Row-->
            <div class="row mb-8">
                <!--begin::Col-->
                <div class="col-xl-3">
                    <div class="fs-6 fw-semibold mt-2 mb-3">E-mail</div>
                </div>
                <!--end::Col-->
                <!--begin::Col-->
                <div class="col-xl-9 fv-row">
                    <input type="text" class="form-control form-control-solid" name="email" id="email"
                        value="{{ $companyShow->email }}" />
                </div>
            </div>
            <!--end::Row-->
            <!--begin::Row-->
            <div class="row mb-8">
                <!--begin::Col-->
                <div class="col-xl-3">
                    <div class="fs-6 fw-semibold mt-2 mb-3">Detalhes do Organismo</div>
                </div>
                <!--end::Col-->
                <!--begin::Col-->
                <div class="col-xl-9 fv-row">
                    <textarea name="details" class="form-control form-control-solid h-100px">{{ $companyShow->details }}</textarea>
                </div>
                <!--begin::Col-->
            </div>
            <!--end::Row-->
            <!--begin::Row-->
            <div class="row mb-8">
                <!--begin::Col-->
                <div class="col-xl-3">
                    <div class="fs-6 fw-semibold mt-2 mb-3">Data de Fundação / Data do CNPJ</div>
                </div>
                <!--end::Col-->
                <!--begin::Col-->
                <div class="col-xl-9 fv-row">
                    <div class="row">
                        <!--begin::Start Date-->
                        <div class="col-md-6">
                            <div class="position-relative d-flex align-items-center">
                                <span class="svg-icon position-absolute ms-4 mb-1 svg-icon-2">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path opacity="0.3"
                                            d="M21 22H3C2.4 22 2 21.6 2 21V5C2 4.4 2.4 4 3 4H21C21.6 4 22 4.4 22 5V21C22 21.6 21.6 22 21 22Z"
                                            fill="currentColor" />
                                        <path
                                            d="M6 6C5.4 6 5 5.6 5 5V3C5 2.4 5.4 2 6 2C6.6 2 7 2.4 7 3V5C7 5.6 6.6 6 6 6ZM11 5V3C11 2.4 10.6 2 10 2C9.4 2 9 2.4 9 3V5C9 5.6 9.4 6 10 6C10.6 6 11 5.6 11 5ZM15 5V3C15 2.4 14.6 2 14 2C13.4 2 13 2.4 13 3V5C13 5.6 13.4 6 14 6C14.6 6 15 5.6 15 5ZM19 5V3C19 2.4 18.6 2 18 2C17.4 2 17 2.4 17 3V5C17 5.6 17.4 6 18 6C18.6 6 19 5.6 19 5Z"
                                            fill="currentColor" />
                                    </svg>
                                </span>
                                <input class="form-control form-control-solid ps-12" name="data_fundacao"
                                    placeholder="Data de Fundação"
                                    value="{{ \Carbon\Carbon::parse($companyShow->data_fundacao)->format('d/m/Y') }}"
                                    id="DM_datepicker_1" />
                            </div>
                        </div>
                        <!--end::Start Date-->

                        <!--begin::End Date-->
                        <div class="col-md-6">
                            <div class="position-relative d-flex align-items-center">
                                <span class="svg-icon position-absolute ms-4 mb-1 svg-icon-2">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path opacity="0.3"
                                            d="M21 22H3C2.4 22 2 21.6 2 21V5C2 4.4 2.4 4 3 4H21C21.6 4 22 4.4 22 5V21C22 21.6 21.6 22 21 22Z"
                                            fill="currentColor" />
                                        <path
                                            d="M6 6C5.4 6 5 5.6 5 5V3C5 2.4 5.4 2 6 2C6.6 2 7 2.4 7 3V5C7 5.6 6.6 6 6 6ZM11 5V3C11 2.4 10.6 2 10 2C9.4 2 9 2.4 9 3V5C9 5.6 9.4 6 10 6C10.6 6 11 5.6 11 5ZM15 5V3C15 2.4 14.6 2 14 2C13.4 2 13 2.4 13 3V5C13 5.6 13.4 6 14 6C14.6 6 15 5.6 15 5ZM19 5V3C19 2.4 18.6 2 18 2C17.4 2 17 2.4 17 3V5C17 5.6 17.4 6 18 6C18.6 6 19 5.6 19 5Z"
                                            fill="currentColor" />
                                    </svg>
                                </span>
                                <input class="form-control form-control-solid ps-12" name="data_cnpj"
                                    placeholder="Data do CNPJ"
                                    value="{{ \Carbon\Carbon::parse($companyShow->data_cnpj)->format('d/m/Y') }}"
                                    id="DM_datepicker_2" />
                            </div>
                        </div>
                        <!--end::End Date-->
                    </div>
                </div>
                <!--end::Col-->
            </div>
            <!--end::Row-->
            <!--begin::Row-->
            <div class="row mb-8">
                <!--begin::Col-->
                <div class="col-xl-3">
                    <div class="fs-6 fw-semibold mt-2 mb-3">CEP / Rua / Bairro / Numero</div>
                </div>
                <!--end::Col-->
                <!--begin::Col-->
                <div class="col-xl-9 fv-row">
                    <div class="row">
                        <!--begin::Start Date-->
                        <div class="col-md-2">
                            <input type="text" class="form-control form-control-solid" placeholder="00000-000"
                                name="cep" id="cep" value="{{ $companyShow->addresses->cep }}" />
                        </div>
                        <!--end::Start Date-->
                        <!--begin::End Date-->
                        <div class="col-md-6">
                            <input type="text" class="form-control form-control-solid"
                                placeholder="Rua: Exemplo de Francisco" name="logradouro" id="logradouro"
                                value="{{ $companyShow->addresses->rua }}" />
                        </div>
                        <!--end::End Date-->
                        <!--begin::End Date-->
                        <div class="col-md-3">
                            <input type="text" class="form-control form-control-solid"
                                placeholder="Bairro: São Sebastião" name="bairro" id="bairro"
                                value="{{ $companyShow->addresses->bairro }}" />
                        </div>
                        <!--end::End Date-->
                        <!--begin::End Date-->
                        <div class="col-md-1">
                            <input type="text" class="form-control form-control-solid" placeholder="118"
                                name="numero" id="numero" value="{{ $companyShow->addresses->numero }}" />
                        </div>
                        <!--end::End Date-->
                    </div>
                </div>
                <!--end::Col-->
            </div>
            <!--end::Row-->
            <!--begin::Row-->
            <div class="row mb-8">
                <!--begin::Col-->
                <div class="col-xl-3">
                    <div class="fs-6 fw-semibold mt-2 mb-3">Estado</div>
                </div>
                <!--end::Col-->
                <!--begin::Col-->
                <div class="col-xl-9 fv-row">
                    <select name="uf" id="uf" data-control="select2"
                        class="form-select form-select-solid form-select-lg">
                        <option value="">Selecione o estado...</option>
                        <option value="AC" {{ ($companyShow->addresses->uf ?? '') == 'AC' ? 'selected' : '' }}>Acre
                        </option>
                        <option value="AL" {{ ($companyShow->addresses->uf ?? '') == 'AL' ? 'selected' : '' }}>
                            Alagoas</option>
                        <option value="AP" {{ ($companyShow->addresses->uf ?? '') == 'AP' ? 'selected' : '' }}>
                            Amapá</option>
                        <option value="AM" {{ ($companyShow->addresses->uf ?? '') == 'AM' ? 'selected' : '' }}>
                            Amazonas</option>
                        <option value="BA" {{ ($companyShow->addresses->uf ?? '') == 'BA' ? 'selected' : '' }}>
                            Bahia</option>
                        <option value="CE" {{ ($companyShow->addresses->uf ?? '') == 'CE' ? 'selected' : '' }}>
                            Ceará</option>
                        <option value="DF" {{ ($companyShow->addresses->uf ?? '') == 'DF' ? 'selected' : '' }}>
                            Distrito Federal</option>
                        <option value="ES" {{ ($companyShow->addresses->uf ?? '') == 'ES' ? 'selected' : '' }}>
                            Espírito Santo</option>
                        <option value="GO" {{ ($companyShow->addresses->uf ?? '') == 'GO' ? 'selected' : '' }}>
                            Goiás</option>
                        <option value="MA" {{ ($companyShow->addresses->uf ?? '') == 'MA' ? 'selected' : '' }}>
                            Maranhão</option>
                        <option value="MT" {{ ($companyShow->addresses->uf ?? '') == 'MT' ? 'selected' : '' }}>
                            Mato Grosso</option>
                        <option value="MS" {{ ($companyShow->addresses->uf ?? '') == 'MS' ? 'selected' : '' }}>
                            Mato Grosso do Sul</option>
                        <option value="MG" {{ ($companyShow->addresses->uf ?? '') == 'MG' ? 'selected' : '' }}>
                            Minas Gerais</option>
                        <option value="PA" {{ ($companyShow->addresses->uf ?? '') == 'PA' ? 'selected' : '' }}>
                            Pará</option>
                        <option value="PB" {{ ($companyShow->addresses->uf ?? '') == 'PB' ? 'selected' : '' }}>
                            Paraíba</option>
                        <option value="PR" {{ ($companyShow->addresses->uf ?? '') == 'PR' ? 'selected' : '' }}>
                            Paraná</option>
                        <option value="PE" {{ ($companyShow->addresses->uf ?? '') == 'PE' ? 'selected' : '' }}>
                            Pernambuco</option>
                        <option value="PI" {{ ($companyShow->addresses->uf ?? '') == 'PI' ? 'selected' : '' }}>
                            Piauí</option>
                        <option value="RJ" {{ ($companyShow->addresses->uf ?? '') == 'RJ' ? 'selected' : '' }}>Rio
                            de Janeiro</option>
                        <option value="RN" {{ ($companyShow->addresses->uf ?? '') == 'RN' ? 'selected' : '' }}>Rio
                            Grande do Norte</option>
                        <option value="RS" {{ ($companyShow->addresses->uf ?? '') == 'RS' ? 'selected' : '' }}>Rio
                            Grande do Sul</option>
                        <option value="RO" {{ ($companyShow->addresses->uf ?? '') == 'RO' ? 'selected' : '' }}>
                            Rondônia</option>
                        <option value="RR" {{ ($companyShow->addresses->uf ?? '') == 'RR' ? 'selected' : '' }}>
                            Roraima</option>
                        <option value="SC" {{ ($companyShow->addresses->uf ?? '') == 'SC' ? 'selected' : '' }}>
                            Santa Catarina</option>
                        <option value="SP" {{ ($companyShow->addresses->uf ?? '') == 'SP' ? 'selected' : '' }}>São
                            Paulo</option>
                        <option value="SE" {{ ($companyShow->addresses->uf ?? '') == 'SE' ? 'selected' : '' }}>
                            Sergipe</option>
                        <option value="TO" {{ ($companyShow->addresses->uf ?? '') == 'TO' ? 'selected' : '' }}>
                            Tocantins</option>
                        <option value="EX" {{ ($companyShow->addresses->uf ?? '') == 'EX' ? 'selected' : '' }}>
                            Estrangeiro</option>
                    </select>
                </div>
                <!--begin::Col-->
            </div>
            <!--end::Row-->
            <!--begin::Row-->
            <div class="row mb-8">
                <!--begin::Col-->
                <div class="col-xl-3">
                    <div class="fs-6 fw-semibold mt-2 mb-3">Notifications</div>
                </div>
                <!--end::Col-->
                <!--begin::Col-->
                <div class="col-xl-9">
                    <div class="d-flex fw-semibold h-100">
                        <div class="form-check form-check-custom form-check-solid me-9">
                            <input class="form-check-input" type="checkbox" value="" id="email" />
                            <label class="form-check-label ms-3" for="email">Email</label>
                        </div>
                        <div class="form-check form-check-custom form-check-solid">
                            <input class="form-check-input" type="checkbox" value="" id="phone"
                                checked="checked" />
                            <label class="form-check-label ms-3" for="phone">Phone</label>
                        </div>
                    </div>
                </div>
                <!--end::Col-->
            </div>
            <!--end::Row-->

            <!--begin::Row-->
            <div class="row">
                <!--begin::Col-->
                <div class="col-xl-3">
                    <div class="fs-6 fw-semibold mt-2 mb-3">Status</div>
                </div>
                <!--end::Col-->
                <!--begin::Col-->
                <div class="col-xl-9">
                    <div class="form-check form-switch form-check-custom form-check-solid">
                        <input class="form-check-input" type="checkbox" id="status" name="status"
                            value="active" {{ $companyShow->status === 'active' ? 'checked' : '' }} />
                        <label class="form-check-label fw-semibold text-gray-400 ms-3" for="status">
                            {{ ucfirst($companyShow->status) }}
                        </label>
                    </div>
                </div>
                <!--end::Col-->
            </div>
            <!--end::Row-->
        </div>
        <!--end::Card body-->
        <!--begin::Card footer-->
        <div class="card-footer d-flex justify-content-end py-6 px-9">
            <button type="submit" class="btn btn-primary" id="kt_project_settings_submit">Salvar Alterações</button>
        </div>
        <!--end::Card footer-->
    </form>
    <!--end:Form-->
</div>


