<x-tenant-app-layout>
    <!--begin::Main-->
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <!--begin::Content wrapper-->
        <div class="d-flex flex-column flex-column-fluid">
            <!--begin::Toolbar-->
            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                <!--begin::Toolbar container-->
                <div id="kt_app_toolbar_container" class="app-containercontainer-xxl d-flex flex-stack">
                    <!--begin::Page title-->
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <!--begin::Title-->
                        <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                            Editar Organismo</h1>
                        <!--end::Title-->
                        <!--begin::Breadcrumb-->
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">
                                <a href="../../demo1/dist/index.html"
                                    class="text-muted text-hover-primary">Dashboard</a>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-400 w-5px h-2px"></span>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">
                                <a class="text-muted text-hover-primary">Configuração</a>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-400 w-5px h-2px"></span>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">Organismo</li>
                            <!--end::Item-->
                        </ul>
                        <!--end::Breadcrumb-->
                    </div>
                    <!--end::Page title-->
                </div>
                <!--end::Toolbar container-->
            </div>
            <!--begin::Content-->
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <!--begin::Content container-->
                <div id="kt_app_content_container" class="app-containercontainer-xxl">

                    <!--begin::Basic info-->
                    <div class="card mb-5 mb-xl-10">
                        <!--begin::Card header-->
                        <div class="card-header border-0 cursor-pointer" role="button" data-bs-toggle="collapse"
                            data-bs-target="#kt_account_profile_details" aria-expanded="true"
                            aria-controls="kt_account_profile_details">
                            <!--begin::Card title-->
                            <div class="card-title m-0">
                                <h3 class="fw-bold m-0">Detalhes</h3>
                            </div>
                            <!--end::Card title-->
                        </div>
                        <!--begin::Card header-->
                        <!--begin::Content-->
                        <div id="kt_account_settings_profile_details" class="collapse show">
                            <!--begin::Form-->
                            <!-- Formulário -->
                            <form method="POST" action="{{ route('company.update', $company->id) }}"
                                enctype="multipart/form-data" id="kt_account_profile_details_form" class="form">
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
                                <div class="card-body border-top p-9">
                                    <!-- Exemplo de input group para Brasão -->
                                    <div class="row mb-6">
                                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Brasão</label>
                                        <div class="col-lg-8">
                                            <div class="image-input image-input-outline" data-kt-image-input="true"
                                                style="background-image: url('/assets/media/svg/avatars/blank.svg')">
                                                <div class="image-input-wrapper w-125px h-125px"
                                                style="background-image: url('{{ $company->avatar ? route('file', ['path' => $company->avatar]) : '/assets/media/avatars/apple-touch-icon.svg' }}');">

                                                </div>
                                                <label
                                                    class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                                    data-kt-image-input-action="change" data-bs-toggle="tooltip"
                                                    title="Change avatar">
                                                    <i class="bi bi-pencil-fill fs-7"></i>
                                                    <input type="file" name="avatar" accept=".png, .jpg, .jpeg"
                                                        disabled /> <!-- Inicialmente desabilitado -->
                                                    <input type="hidden" name="avatar_remove"  />
                                                </label>

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
                                                    data-kt-image-input-action="remove" disabled data-bs-toggle="tooltip"
                                                    title="Remove avatar">
                                                    <i class="bi bi-x fs-2"></i>
                                                </span>
                                                <!--end::Remove-->
                                                <!--end::Image input-->
                                                <!--begin::Hint-->

                                                <!--end::Hint-->
                                            </div>
                                            <div class="form-text">Tipos de arquivos permitidos: png, jpg, jpeg.
                                            </div>

                                        </div>

                                    </div>

                                    <!-- Razão Social -->
                                    <div class="row mb-6">
                                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">Razão
                                            Social</label>
                                        <div class="col-lg-8 fv-row">
                                            <input type="text" name="name"
                                                class="form-control form-control-lg form-control-solid"
                                                placeholder="Nome do Organismo" value="{{ $company->name }}" disabled />
                                        </div>
                                    </div>

                                    <!-- CNPJ -->
                                    <div class="row mb-6">
                                        <label class="col-lg-4 col-form-label fw-semibold fs-6">CNPJ</label>
                                        <div class="col-lg-8 fv-row">
                                            <input type="tel" name="cnpj" id="cnpj"
                                                class="form-control form-control-lg form-control-solid"
                                                placeholder="000.000" value="{{ $company->cnpj }}" disabled />
                                        </div>
                                    </div>

                                    <!-- E-mail -->
                                    <div class="row mb-6">
                                        <label class="col-lg-4 col-form-label fw-semibold fs-6">E-mail</label>
                                        <div class="col-lg-8 fv-row">
                                            <input type="text" name="email"
                                                class="form-control form-control-lg form-control-solid"
                                                placeholder="E-mail da Empresa" value="{{ $company->email }}"
                                                disabled />
                                        </div>
                                    </div>

                                    <!-- CEP -->
                                    <div class="row mb-6">
                                        <label class="col-lg-4 col-form-label fw-semibold fs-6">CEP</label>
                                        <div class="col-lg-8 fv-row">
                                            <input type="text" name="cep" id="cep"
                                                class="form-control form-control-lg form-control-solid"
                                                placeholder="00000-000" value="{{ $company->addresses->cep ?? '' }}"
                                                disabled />
                                        </div>
                                    </div>

                                    <!-- Logradouro -->
                                    <div class="row mb-6">
                                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Logradouro</label>
                                        <div class="col-lg-8 fv-row">
                                            <input type="tel" name="logradouro" id="logradouro"
                                                class="form-control form-control-lg form-control-solid"
                                                placeholder="Informe a rua"
                                                value="{{ $company->addresses->rua ?? '' }}" disabled />
                                        </div>
                                    </div>

                                    <!-- Bairro -->
                                    <div class="row mb-6">
                                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Bairro</label>
                                        <div class="col-lg-8 fv-row">
                                            <input type="text" name="bairro" id="bairro"
                                                class="form-control form-control-lg form-control-solid"
                                                placeholder="Informe o bairro"
                                                value="{{ $company->addresses->bairro ?? '' }}" disabled />
                                        </div>
                                    </div>

                                    <!-- Cidade -->
                                    <div class="row mb-6">
                                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Cidade</label>
                                        <div class="col-lg-8 fv-row">
                                            <input type="text" name="localidade" id="localidade"
                                                class="form-control form-control-lg form-control-solid"
                                                placeholder="Informe a Cidade"
                                                value="{{ $company->addresses->cidade ?? '' }}" disabled />
                                        </div>
                                    </div>

                                    <!-- Estado -->
                                    <div class="row mb-6">
                                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Estado</label>
                                        <div class="col-lg-8 fv-row">
                                            <select name="uf" id="uf" data-control="select2"
                                                class="form-select form-select-solid form-select-lg" disabled>
                                                <option value="">Selecione o estado...</option>
                                                <option value="AC"
                                                    {{ ($company->addresses->uf ?? '') == 'AC' ? 'selected' : '' }}>Acre
                                                </option>
                                                <option value="AL"
                                                    {{ ($company->addresses->uf ?? '') == 'AL' ? 'selected' : '' }}>
                                                    Alagoas</option>
                                                <option value="AP"
                                                    {{ ($company->addresses->uf ?? '') == 'AP' ? 'selected' : '' }}>
                                                    Amapá</option>
                                                <option value="AM"
                                                    {{ ($company->addresses->uf ?? '') == 'AM' ? 'selected' : '' }}>
                                                    Amazonas</option>
                                                <option value="BA"
                                                    {{ ($company->addresses->uf ?? '') == 'BA' ? 'selected' : '' }}>
                                                    Bahia</option>
                                                <option value="CE"
                                                    {{ ($company->addresses->uf ?? '') == 'CE' ? 'selected' : '' }}>
                                                    Ceará</option>
                                                <option value="DF"
                                                    {{ ($company->addresses->uf ?? '') == 'DF' ? 'selected' : '' }}>
                                                    Distrito Federal</option>
                                                <option value="ES"
                                                    {{ ($company->addresses->uf ?? '') == 'ES' ? 'selected' : '' }}>
                                                    Espírito Santo</option>
                                                <option value="GO"
                                                    {{ ($company->addresses->uf ?? '') == 'GO' ? 'selected' : '' }}>
                                                    Goiás</option>
                                                <option value="MA"
                                                    {{ ($company->addresses->uf ?? '') == 'MA' ? 'selected' : '' }}>
                                                    Maranhão</option>
                                                <option value="MT"
                                                    {{ ($company->addresses->uf ?? '') == 'MT' ? 'selected' : '' }}>
                                                    Mato Grosso</option>
                                                <option value="MS"
                                                    {{ ($company->addresses->uf ?? '') == 'MS' ? 'selected' : '' }}>
                                                    Mato Grosso do Sul</option>
                                                <option value="MG"
                                                    {{ ($company->addresses->uf ?? '') == 'MG' ? 'selected' : '' }}>
                                                    Minas Gerais</option>
                                                <option value="PA"
                                                    {{ ($company->addresses->uf ?? '') == 'PA' ? 'selected' : '' }}>
                                                    Pará</option>
                                                <option value="PB"
                                                    {{ ($company->addresses->uf ?? '') == 'PB' ? 'selected' : '' }}>
                                                    Paraíba</option>
                                                <option value="PR"
                                                    {{ ($company->addresses->uf ?? '') == 'PR' ? 'selected' : '' }}>
                                                    Paraná</option>
                                                <option value="PE"
                                                    {{ ($company->addresses->uf ?? '') == 'PE' ? 'selected' : '' }}>
                                                    Pernambuco</option>
                                                <option value="PI"
                                                    {{ ($company->addresses->uf ?? '') == 'PI' ? 'selected' : '' }}>
                                                    Piauí</option>
                                                <option value="RJ"
                                                    {{ ($company->addresses->uf ?? '') == 'RJ' ? 'selected' : '' }}>Rio
                                                    de Janeiro</option>
                                                <option value="RN"
                                                    {{ ($company->addresses->uf ?? '') == 'RN' ? 'selected' : '' }}>Rio
                                                    Grande do Norte</option>
                                                <option value="RS"
                                                    {{ ($company->addresses->uf ?? '') == 'RS' ? 'selected' : '' }}>Rio
                                                    Grande do Sul</option>
                                                <option value="RO"
                                                    {{ ($company->addresses->uf ?? '') == 'RO' ? 'selected' : '' }}>
                                                    Rondônia</option>
                                                <option value="RR"
                                                    {{ ($company->addresses->uf ?? '') == 'RR' ? 'selected' : '' }}>
                                                    Roraima</option>
                                                <option value="SC"
                                                    {{ ($company->addresses->uf ?? '') == 'SC' ? 'selected' : '' }}>
                                                    Santa Catarina</option>
                                                <option value="SP"
                                                    {{ ($company->addresses->uf ?? '') == 'SP' ? 'selected' : '' }}>São
                                                    Paulo</option>
                                                <option value="SE"
                                                    {{ ($company->addresses->uf ?? '') == 'SE' ? 'selected' : '' }}>
                                                    Sergipe</option>
                                                <option value="TO"
                                                    {{ ($company->addresses->uf ?? '') == 'TO' ? 'selected' : '' }}>
                                                    Tocantins</option>
                                                <option value="EX"
                                                    {{ ($company->addresses->uf ?? '') == 'EX' ? 'selected' : '' }}>
                                                    Estrangeiro</option>
                                            </select>

                                        </div>
                                    </div>

                                    <!-- Opções de Comunicação -->
                                    <div class="row mb-6">
                                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Comunicação</label>
                                        <div class="col-lg-8 fv-row">
                                            <label
                                                class="form-check form-check-custom form-check-inline form-check-solid me-5">
                                                <input class="form-check-input" name="communication[]"
                                                    type="checkbox" value="1" disabled />
                                                <span class="fw-semibold ps-2 fs-6">Email</span>
                                            </label>
                                            <label
                                                class="form-check form-check-custom form-check-inline form-check-solid">
                                                <input class="form-check-input" name="communication[]"
                                                    type="checkbox" value="2" disabled />
                                                <span class="fw-semibold ps-2 fs-6">Telefone</span>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Cadastro ativo -->
                                    <div class="row mb-0">
                                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Cadastro ativo?</label>
                                        <div class="col-lg-8 d-flex align-items-center">
                                            <div
                                                class="form-check form-check-solid form-switch form-check-custom fv-row">
                                                <input class="form-check-input w-45px h-30px" type="checkbox"
                                                    id="allowmarketing" checked="checked" disabled />
                                                <label class="form-check-label" for="allowmarketing"></label>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="card-footer d-flex justify-content-end py-6 px-9">
                                    <button type="reset"
                                        class="btn btn-light btn-active-light-primary me-2">Descartar</button>
                                    <button type="button" id="editButton"
                                        class="btn btn-light btn-active-light-success me-2">
                                        <i class="fa-duotone fa-solid fa-pen-clip"></i> Editar
                                    </button>
                                    <button type="submit" class="btn btn-primary"
                                        id="kt_account_profile_details_submit" disabled>
                                        <i class="fa-solid fa-floppy-disk"></i> Salvar Alterações
                                    </button>
                                </div>
                            </form>

                            <script>
                                document.getElementById('editButton').addEventListener('click', function() {
                                    // Habilita todos os campos do formulário para edição
                                    document.querySelectorAll(
                                        '#kt_account_profile_details_form input, #kt_account_profile_details_form select').forEach(
                                        function(el) {
                                            el.disabled = false;
                                        });

                                    // Habilita o botão de salvar
                                    document.getElementById('kt_account_profile_details_submit').disabled = false;
                                });
                            </script>

                            <!--end::Form-->
                        </div>
                        <!--end::Content-->
                    </div>
                    <!--end::Basic info-->
                </div>
            </div>
        </div>
    </div>

</x-tenant-app-layout>

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
