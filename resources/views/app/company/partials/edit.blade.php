<!--begin::Basic info-->
<div class="card mb-5 mb-xl-10">
    <!--begin::Card header-->
    <div class="card-header border-0 cursor-pointer" role="button" data-bs-toggle="collapse"
        data-bs-target="#kt_account_profile_details" aria-expanded="true" aria-controls="kt_account_profile_details">
        <!--begin::Card title-->
        <div class="card-title m-0">
            <h3 class="fw-bold m-0">Editar Organismo</h3>
        </div>
        <!--end::Card title-->
    </div>
    <!--begin::Card header-->
    <!--begin::Content-->
    <div id="kt_account_settings_profile_details" class="collapse show">
        <!--begin::Form-->
        <form method="POST" action="{{ route('company.update', $company->id) }}" enctype="multipart/form-data"
            id="kt_account_profile_details_form" class="form">
            @csrf
            @method('PUT')
            <!-- Mensagens de Erro -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
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
                                   background-image: url('{{ $company->avatar ? route('file', ['path' => $company->avatar]) : '/publicassets/media/avatars/blank.png' }}')">
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
                        <div class="fs-6 fw-semibold mt-2 mb-3">Razão Social</div>
                    </div>
                    <!--end::Col-->
                    <!--begin::Col-->
                    <div class="col-xl-9 fv-row">
                        <input type="text" class="form-control form-control-solid" name="razao_social"
                            placeholder="Razão Social do Organismo" value="{{ $company->razao_social ?? $company->name }}" />
                    </div>
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
                            value="{{ $company->name }}" />
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
                        <div class="input-group">
                            <input type="text" class="form-control form-control-solid" name="cnpj" id="cnpj"
                                value="{{ $company->cnpj }}" placeholder="CNPJ" />
                            <button type="button" class="btn btn-secondary" id="btn-consultar-cnpj">
                                <i class="bi bi-search"></i> Consultar
                            </button>
                        </div>
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
                            value="{{ $company->email }}" />
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
                        <textarea name="details" class="form-control form-control-solid h-100px">{{ $company->details }}</textarea>
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
                                        value="{{ $company->data_fundacao ? \Carbon\Carbon::parse($company->data_fundacao)->format('d/m/Y') : '' }}"
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
                                        value="{{ $company->data_cnpj ? \Carbon\Carbon::parse($company->data_cnpj)->format('d/m/Y') : '' }}"
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
                            <!--begin::CEP-->
                            <div class="col-md-2">
                                <input type="text" class="form-control form-control-solid" placeholder="00000-000"
                                    name="cep" id="cep" value="{{ $company->addresses->cep ?? '' }}" />
                            </div>
                            <!--end::CEP-->
                            <!--begin::Rua-->
                            <div class="col-md-6">
                                <input type="text" class="form-control form-control-solid"
                                    placeholder="Rua: Exemplo de Francisco" name="logradouro" id="logradouro"
                                    value="{{ $company->addresses->rua ?? '' }}" />
                            </div>
                            <!--end::Rua-->
                            <!--begin::Bairro-->
                            <div class="col-md-3">
                                <input type="text" class="form-control form-control-solid"
                                    placeholder="Bairro: São Sebastião" name="bairro" id="bairro"
                            value="{{ $company->addresses->bairro ?? '' }}" />
                            </div>
                            <!--end::Bairro-->
                            <!--begin::Numero-->
                            <div class="col-md-1">
                                <input type="text" class="form-control form-control-solid" placeholder="118"
                                    name="numero" id="numero" value="{{ $company->addresses->numero ?? '' }}" />
                            </div>
                            <!--end::Numero-->
                        </div>
                    </div>
                    <!--end::Col-->
                </div>
                <!--end::Row-->
                <!--begin::Row-->
                <div class="row mb-8">
                    <!--begin::Col-->
                    <div class="col-xl-3">
                        <div class="fs-6 fw-semibold mt-2 mb-3">Cidade / Estado</div>
                    </div>
                    <!--end::Col-->
                    <!--begin::Col-->
                    <div class="col-xl-9 fv-row">
                        <div class="row">
                            <!--begin::Cidade-->
                            <div class="col-md-6">
                                <input type="text" class="form-control form-control-solid" name="localidade" id="localidade"
                                    placeholder="Informe a Cidade" value="{{ $company->addresses->cidade ?? '' }}" />
                </div>
                            <!--end::Cidade-->
                            <!--begin::Estado-->
                            <div class="col-md-6">
                        <select name="uf" id="uf" data-control="select2"
                            class="form-select form-select-solid form-select-lg">
                            <option value="">Selecione o estado...</option>
                                    <option value="AC" {{ ($company->addresses->uf ?? '') == 'AC' ? 'selected' : '' }}>Acre
                            </option>
                            <option value="AL" {{ ($company->addresses->uf ?? '') == 'AL' ? 'selected' : '' }}>
                                Alagoas</option>
                            <option value="AP" {{ ($company->addresses->uf ?? '') == 'AP' ? 'selected' : '' }}>
                                Amapá</option>
                            <option value="AM" {{ ($company->addresses->uf ?? '') == 'AM' ? 'selected' : '' }}>
                                Amazonas</option>
                            <option value="BA" {{ ($company->addresses->uf ?? '') == 'BA' ? 'selected' : '' }}>
                                Bahia</option>
                            <option value="CE" {{ ($company->addresses->uf ?? '') == 'CE' ? 'selected' : '' }}>
                                Ceará</option>
                            <option value="DF" {{ ($company->addresses->uf ?? '') == 'DF' ? 'selected' : '' }}>
                                Distrito Federal</option>
                            <option value="ES" {{ ($company->addresses->uf ?? '') == 'ES' ? 'selected' : '' }}>
                                Espírito Santo</option>
                            <option value="GO" {{ ($company->addresses->uf ?? '') == 'GO' ? 'selected' : '' }}>
                                Goiás</option>
                            <option value="MA" {{ ($company->addresses->uf ?? '') == 'MA' ? 'selected' : '' }}>
                                Maranhão</option>
                            <option value="MT" {{ ($company->addresses->uf ?? '') == 'MT' ? 'selected' : '' }}>
                                Mato Grosso</option>
                            <option value="MS" {{ ($company->addresses->uf ?? '') == 'MS' ? 'selected' : '' }}>
                                Mato Grosso do Sul</option>
                            <option value="MG" {{ ($company->addresses->uf ?? '') == 'MG' ? 'selected' : '' }}>
                                Minas Gerais</option>
                            <option value="PA" {{ ($company->addresses->uf ?? '') == 'PA' ? 'selected' : '' }}>
                                Pará</option>
                            <option value="PB" {{ ($company->addresses->uf ?? '') == 'PB' ? 'selected' : '' }}>
                                Paraíba</option>
                            <option value="PR" {{ ($company->addresses->uf ?? '') == 'PR' ? 'selected' : '' }}>
                                Paraná</option>
                            <option value="PE" {{ ($company->addresses->uf ?? '') == 'PE' ? 'selected' : '' }}>
                                Pernambuco</option>
                            <option value="PI" {{ ($company->addresses->uf ?? '') == 'PI' ? 'selected' : '' }}>
                                Piauí</option>
                            <option value="RJ" {{ ($company->addresses->uf ?? '') == 'RJ' ? 'selected' : '' }}>Rio
                                de Janeiro</option>
                            <option value="RN" {{ ($company->addresses->uf ?? '') == 'RN' ? 'selected' : '' }}>Rio
                                Grande do Norte</option>
                            <option value="RS" {{ ($company->addresses->uf ?? '') == 'RS' ? 'selected' : '' }}>Rio
                                Grande do Sul</option>
                            <option value="RO" {{ ($company->addresses->uf ?? '') == 'RO' ? 'selected' : '' }}>
                                Rondônia</option>
                            <option value="RR" {{ ($company->addresses->uf ?? '') == 'RR' ? 'selected' : '' }}>
                                Roraima</option>
                            <option value="SC" {{ ($company->addresses->uf ?? '') == 'SC' ? 'selected' : '' }}>
                                Santa Catarina</option>
                            <option value="SP" {{ ($company->addresses->uf ?? '') == 'SP' ? 'selected' : '' }}>São
                                Paulo</option>
                            <option value="SE" {{ ($company->addresses->uf ?? '') == 'SE' ? 'selected' : '' }}>
                                Sergipe</option>
                            <option value="TO" {{ ($company->addresses->uf ?? '') == 'TO' ? 'selected' : '' }}>
                                Tocantins</option>
                            <option value="EX" {{ ($company->addresses->uf ?? '') == 'EX' ? 'selected' : '' }}>
                                Estrangeiro</option>
                        </select>
                            </div>
                            <!--end::Estado-->
                        </div>
                    </div>
                    <!--end::Col-->
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
                        <div class="fs-6 fw-semibold mt-2 mb-3">Status </div>
                    </div>
                    <!--end::Col-->
                    <!--begin::Col-->
                    <div class="col-xl-9">
                        <div class="form-check form-switch form-check-custom form-check-solid">
                            <input class="form-check-input" type="checkbox" id="status" name="status"
                                value="active" {{ $company->status === 'active' ? 'checked' : '' }} />
                            <label class="form-check-label fw-semibold text-gray-400 ms-3" for="status">
                                {{ ucfirst($company->status) }}
                            </label>
                        </div>
                    </div>
                    <!--end::Col-->
                </div>
                <!--end::Row-->

            </div>

            </div>
            <!--end::Card body-->
            <!--begin::Card footer-->
            <div class="card-footer d-flex justify-content-end py-6 px-9">
                <button type="submit" class="btn btn-primary" id="kt_project_settings_submit">Salvar Alterações</button>
            </div>
            <!--end::Card footer-->
        </form>

        <!--end::Form-->
    </div>
    <!--end::Content-->
</div>
<!--end::Basic info-->

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnConsultar = document.getElementById('btn-consultar-cnpj');
        const inputCnpj = document.getElementById('cnpj');

        if (btnConsultar) {
            btnConsultar.addEventListener('click', function() {
                const cnpj = inputCnpj.value.replace(/\D/g, '');

                if (cnpj.length !== 14) {
                    toastr.warning('Por favor, digite um CNPJ válido com 14 dígitos.');
                    return;
                }

                // Feedback visual de carregamento
                const originalText = btnConsultar.innerHTML;
                btnConsultar.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Consultando...';
                btnConsultar.disabled = true;

                fetch('{{ route("company.consultar-cnpj") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ cnpj: cnpj })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erro na consulta');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.error) {
                        toastr.error(data.error);
                        return;
                    }

                    // Preencher campos
                    // Razão Social (#razao_social) e Nome Fantasia (#name)
                    if (document.querySelector('input[name="razao_social"]')) {
                        document.querySelector('input[name="razao_social"]').value = data.razao_social || '';
                    }
                    if (document.querySelector('input[name="name"]')) {
                        document.querySelector('input[name="name"]').value = data.nome_fantasia || data.razao_social || '';
                    }

                    // E-mail (#email) - a API BrasilAPI nem sempre retorna email no objeto raiz, mas tenta
                    if (document.getElementById('email') && data.email) {
                        document.getElementById('email').value = data.email;
                    }

                    // Endereço
                    if (document.getElementById('cep')) document.getElementById('cep').value = data.cep || '';
                    if (document.getElementById('logradouro')) document.getElementById('logradouro').value = data.logradouro || '';
                    if (document.getElementById('numero')) document.getElementById('numero').value = data.numero || '';
                    if (document.getElementById('bairro')) document.getElementById('bairro').value = data.bairro || '';
                    if (document.getElementById('localidade')) document.getElementById('localidade').value = data.municipio || '';

                    // Estado (Select2)
                    if (data.uf) {
                        const selectUf = document.querySelector('select[name="uf"]');
                        if (selectUf) {
                            $(selectUf).val(data.uf).trigger('change');
                        }
                    }

                    // Datas
                    if (data.data_inicio_atividade) { // Vem no formato YYYY-MM-DD
                        const dateParts = data.data_inicio_atividade.split('-');
                        const formattedDate = `${dateParts[2]}/${dateParts[1]}/${dateParts[0]}`;
                        
                        const dateInput = document.querySelector('input[name="data_fundacao"]');
                         if (dateInput) {
                            // Se for flatpickr/datepicker, pode precisar de tratamento especial
                             dateInput.value = formattedDate;
                             // Tenta atualizar se for um componente de data visível
                             dateInput.dispatchEvent(new Event('input'));
                         }
                    }

                    toastr.success('Dados preenchidos com sucesso!');
                })
                .catch(error => {
                    console.error('Erro:', error);
                    toastr.error('Erro ao consultar CNPJ. Verifique se o número está correto.');
                })
                .finally(() => {
                    btnConsultar.innerHTML = originalText;
                    btnConsultar.disabled = false;
                });
            });
        }
    });
</script>
@endpush
