<!--begin::Form-->
<form id="kt_ecommerce_add_product_form" class="form d-flex flex-column flex-lg-row"
    action="{{ route('cemiterio.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <!--begin::Aside column-->
    <div class="d-flex flex-column gap-7 gap-lg-10 w-100 w-lg-300px mb-7 me-lg-10">
        <!--begin::Thumbnail settings-->
        <div class="card card-flush py-4">
            <!--begin::Card header-->
            <div class="card-header">
                <!--begin::Card title-->
                <div class="card-title">
                    <h2>Foto do Falecido</h2>
                </div>
                <!--end::Card title-->
            </div>
            <!--end::Card header-->
            <!--begin::Card body-->
            <div class="card-body text-center pt-0">
                <!--begin::Image input-->
                <!--begin::Image input placeholder-->
                <style>
                    .image-input-placeholder {
                        background-image: url('tenancy/assets/media/svg/files/blank-image.svg');
                    }

                    [data-bs-theme="dark"] .image-input-placeholder {
                        background-image: url('tenancy/assets/media/svg/files/blank-image-dark.svg');
                    }
                </style>
                <!--end::Image input placeholder-->
                <div class="image-input image-input-empty image-input-outline image-input-placeholder mb-3"
                    data-kt-image-input="true">
                    <!--begin::Preview existing avatar-->
                    <div class="image-input-wrapper w-150px h-150px"></div>
                    <!--end::Preview existing avatar-->
                    <!--begin::Label-->
                    <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                        data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change avatar">
                        <i class="bi bi-pencil-fill fs-7"></i>
                        <!--begin::Inputs-->
                        <input type="file" name="avatar" accept=".png, .jpg, .jpeg" />
                        <input type="hidden" name="avatar_remove" />
                        <!--end::Inputs-->
                    </label>
                    <!--end::Label-->
                    <!--begin::Cancel-->
                    <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                        data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel avatar">
                        <i class="bi bi-x fs-2"></i>
                    </span>
                    <!--end::Cancel-->
                    <!--begin::Remove-->
                    <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                        data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove avatar">
                        <i class="bi bi-x fs-2"></i>
                    </span>
                    <!--end::Remove-->
                </div>
                <!--end::Image input-->
                <!--begin::Description-->
                <div class="text-muted fs-7">Set the product thumbnail image. Only *.png, *.jpg and *.jpeg image files
                    are accepted</div>
                <!--end::Description-->
            </div>
            <!--end::Card body-->
        </div>
        <!--end::Thumbnail settings-->
        <!--begin::Status-->
        <!--begin::Card-->
        <!--begin::Card-->
        <div class="card card-flush py-4">
            <!--begin::Card header-->
            <div class="card-header">
                <div class="card-title">
                    <h2>Selecione o Túmulo</h2>
                </div>
                <!-- Card toolbar com botão de pesquisa -->
                <div class="card-toolbar">
                    <!-- Lupa para abrir o modal -->
                    <button type="button" class="btn btn-icon btn-light" data-bs-toggle="modal" data-bs-target="#kt_tomb_selection_modal">
                        <i class="bi bi-search"></i> <!-- Ícone de lupa -->
                    </button>
                </div>
            </div>
            <!--end::Card header-->
            <!--begin::Card body-->
            <div class="card-body pt-0">
                <!--begin::Select2-->
                <select class="form-select mb-2 @error('sepultura_id') is-invalid @enderror" data-control="select2" data-placeholder="Selecione uma opção" name="sepultura_id" data-allow-clear="true">
                    <option value=""></option>
                    @foreach ($sepulturas as $sepultura)
                    <option value="{{ $sepultura->id }}" {{ old('sepultura_id') == $sepultura->id ? 'selected' : '' }}>
                        {{ $sepultura->codigo_sepultura }} - {{ $sepultura->status }}
                    </option>
                @endforeach

                </select>

                <!--end::Select2-->
                <div class="text-muted fs-7">Selecione o túmulo desejado.</div>
                @error('sepultura_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            </div>
        </div>
        <!--end::Card-->


        <!--end::Card-->
        <!--end::Status-->
    </div>
    <!--end::Aside column-->
    <!--begin::Main column-->
    <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
        <!--begin::Tab content-->
        <div class="tab-content">
            <!--begin::Tab pane-->
            <div class="d-flex flex-column gap-7 gap-lg-10">
                <!--begin::General options-->
                <div class="card card-flush py-4">
                    <!--begin::Card header-->
                    <div class="card-header">
                        <div class="card-title">
                            <h2>Dados do Falecido</h2>
                        </div>
                    </div>
                    <!--end::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body pt-0">
                        <!--begin::Input group: Nome Completo-->
                        <div class="mb-10 fv-row">
                            <!--begin::Label-->
                            <label class="required form-label">Nome Completo</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="text" name="nome"  class="form-control form-control @error('nome') is-invalid @enderror"
                                placeholder="Francisco das Santas Chagas" value="{{ old('nome') }}"/>
                            <!--end::Input-->
                            <!--begin::Description-->
                            <div class="text-muted fs-7 mb-5">
                                Informe o nome completo do falecido. Este campo é obrigatório e deve ser preenchido
                                corretamente para garantir a integridade do cadastro.
                            </div>
                            <!--end::Description-->
                            @error('nome')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        </div>
                        <!--end::Input group-->

                        <!--begin::Row: Data Nascimento, Data Falecimento, Data Sepultamento-->
                        <div class="row">
                            <!-- Data Nascimento -->
                            <div class="col-12 col-sm-4 mb-7">
                                <label class="fs-6 fw-semibold ">
                                    <span class="required">Data Nascimento</span>
                                </label>
                                <input type="date"
                                    class="form-control form-control @error('data_nascimento') is-invalid @enderror"
                                    name="data_nascimento"
                                    value="{{ old('data_nascimento', now()->format('Y-m-d')) }}" />
                                @error('data_nascimento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Data Falecimento -->
                            <div class="col-12 col-sm-4 mb-7">
                                <label class="fs-6 fw-semibold ">
                                    <span class="required">Data Falecimento</span>
                                </label>
                                <input type="date"
                                    class="form-control form-control @error('data_falecimento') is-invalid @enderror"
                                    name="data_falecimento"
                                    value="{{ old('data_falecimento', now()->format('Y-m-d')) }}" />
                                @error('data_falecimento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Data Sepultamento -->
                            <div class="col-12 col-sm-4 mb-7">
                                <label class="fs-6 fw-semibold ">
                                    <span class="required">Data Sepultamento</span>
                                </label>
                                <input type="date"
                                    class="form-control form-control @error('data_sepultamento') is-invalid @enderror"
                                    name="data_sepultamento"
                                    value="{{ old('data_sepultamento', now()->format('Y-m-d')) }}" />
                                @error('data_sepultamento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!--end::Row-->
                        <div class="row">
                            <!-- Causa Mortis -->
                            <div class="col-12 col-sm-6 mb-7">
                                <label class="fs-6 fw-semibold mb-2">Causa Mortis</label>
                                <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                    title="Se refere à causa do falecimento, ou seja, a condição ou doença que levou ao óbito."></i>
                                <input type="text"
                                    placeholder="Exemplo: Doença cardíaca, acidente de trânsito, câncer"
                                    class="form-control @error('causa_mortis') is-invalid @enderror"
                                    name="causa_mortis" value="{{ old('causa_mortis') }}" />
                                @error('causa_mortis')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Livro Sepultamento -->
                            <div class="col-12 col-sm-2 mb-7">
                                <label class="fs-6 fw-semibold mb-2">Livro</label>
                                <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                    title="Livro de Registro de Sepultamento."></i>
                                <input type="text"
                                    class="form-control @error('livro_sepultamento') is-invalid @enderror"
                                    placeholder="L-0001" name="livro_sepultamento"
                                    value="{{ old('livro_sepultamento') }}" />
                                @error('livro_sepultamento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Folha Sepultamento -->
                            <div class="col-12 col-sm-2 mb-7">
                                <label class="fs-6 fw-semibold mb-2">Folha</label>
                                <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                    title="Folha de Registro de Sepultamento."></i>
                                <input type="text"
                                    class="form-control @error('folha_sepultamento') is-invalid @enderror"
                                    placeholder="F-234" name="folha_sepultamento"
                                    value="{{ old('folha_sepultamento') }}" />
                                @error('folha_sepultamento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Numero Sepultamento -->
                            <div class="col-12 col-sm-2 mb-7">
                                <label class="fs-6 fw-semibold mb-2">Numero</label>
                                <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                    title="Número de Registro de Sepultamento."></i>
                                <input type="text"
                                    class="form-control @error('numero_sepultamento') is-invalid @enderror"
                                    placeholder="R-4563" name="numero_sepultamento"
                                    value="{{ old('numero_sepultamento') }}" />
                                @error('numero_sepultamento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <!-- Família Responsável -->
                            <div class="col-12 col-sm-6 mb-7">
                                <label class="fs-6 fw-semibold mb-2">Família Responsável</label>
                                <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                    title="Nome da família responsável pelo sepultado."></i>
                                <input type="text"
                                    class="form-control @error('familia_responsavel') is-invalid @enderror"
                                    name="familia_responsavel" value="{{ old('familia_responsavel') }}"
                                    placeholder="Exemplo: Silva, Oliveira" />
                                @error('familia_responsavel')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Relacionamento -->
                            <div class="col-12 col-sm-6 mb-7">
                                <label class="fs-6 fw-semibold mb-2">Relacionamento com a pessoa sepultada</label>
                                <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                    title="Relacionamento do responsável com o sepultado."></i>
                                <input type="text"
                                    class="form-control @error('relacionamento') is-invalid @enderror"
                                    name="relacionamento" value="{{ old('relacionamento') }}"
                                    placeholder="Exemplo: Filho, Irmão, Amigo" />
                                @error('relacionamento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>


                        <!--begin::Input group: Description-->
                        <div class="mb-10 fv-row">
                            <!--begin::Label-->
                            <label class="form-label">Descrição</label>
                            <!--end::Label-->
                            <!--begin::Editor-->
                            <textarea class="form-control form-control" rows="2" name="informacoes_atestado_obito"  placeholder="">{{ old('data_sepultamento') }}" </textarea>

                            <!--end::Editor-->
                            <!--begin::Description-->
                            <div class="text-muted fs-7">Adicione uma descrição para o falecido para
                                melhor visibilidade.</div>
                            <!--end::Description-->
                        </div>
                        <!--end::Input group-->
                    </div>
                    <!--end::Card body-->

                </div>
                <!--end::General options-->
                <!--begin::Media-->
                <div class="card card-flush py-4">
                    <!--begin::Card header-->
                    <div class="card-header">
                        <div class="card-title">
                            <h2>Documentos</h2>
                        </div>
                    </div>
                    <!--end::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body pt-0">
                        <!--begin::Input group-->
                        <div class="fv-row mb-2">
                            <!--begin::Dropzone-->
                            <div class="dropzone" id="kt_ecommerce_add_product_media">
                                <!--begin::Message-->
                                <div class="dz-message needsclick">
                                    <!--begin::Icon-->
                                    <i class="bi bi-file-earmark-arrow-up text-primary fs-3x"></i>
                                    <!--end::Icon-->
                                    <!--begin::Info-->
                                    <div class="ms-4">
                                        <h3 class="fs-5 fw-bold text-gray-900 mb-1">Arraste os arquivos aqui ou clique
                                            para fazer o upload.</h3>
                                        <span class="fs-7 fw-semibold text-gray-400">Faça o upload de até 10
                                            arquivos</span>
                                    </div>
                                    <!--end::Info-->
                                </div>
                            </div>
                            <!--end::Dropzone-->
                        </div>
                        <!--end::Input group-->
                        <!--begin::Description-->
                        <div class="text-muted fs-7">Documentos do felecido.</div>
                        <!--end::Description-->
                    </div>

                    <!--end::Card header-->
                </div>
                <!--end::Media-->
            </div>
            <!--end::Tab pane-->
        </div>
        <!--end::Tab content-->
        <div class="d-flex justify-content-end">

            <!--begin::Button-->
            <button type="submit" id="kt_ecommerce_add_product_submit" class="btn btn-primary">
                <span class="indicator-label">Save Changes</span>
                <span class="indicator-progress">Por favor, aguarde...
                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
            </button>
            <!--end::Button-->
        </div>
    </div>
    <!--end::Main column-->
</form>
<!--end::Form-->

<!--begin::Modal - New Card-->
<div class="modal fade" id="kt_tomb_selection_modal" tabindex="-1" aria-labelledby="kt_tomb_selection_modal_label"
    aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered mw-950px">
        <!--begin::Modal content-->
        <div class="modal-content">
            <!--begin::Modal header-->
            <div class="modal-header">
                <!--begin::Modal title-->
                <h2>Add New Card</h2>
                <!--end::Modal title-->
                <!--begin::Close-->
                <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
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
                <!--begin::Table-->
                <table class="table align-middle table-row-dashed fs-6 gy-5"
                    id="kt_ecommerce_report_customer_orders_table">
                    <!--begin::Table head-->
                    <thead>
                        <!--begin::Table row-->
                        <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                            <th class="min-w-100px">Código</th>
                            <th class="min-w-100px">Localização</th>
                            <th class="min-w-100px">Status</th>
                            <th class="min-w-100px">Data</th>
                            <th class="text-end min-w-75px">Tipo</th>
                            <th class="text-end min-w-75px">Tamanho</th>
                            <th class="text-end min-w-100px">Ação</th>
                        </tr>
                        <!--end::Table row-->
                    </thead>
                    <!--end::Table head-->
                    <!--begin::Table body-->
                    <tbody class="fw-semibold text-gray-600">
                        <!--begin::Table row-->
                        @foreach ($sepulturas as $sepultura)
                            <tr>
                                <!--begin::Customer name=-->
                                <td>
                                    <a href="#"
                                        class="text-dark text-hover-primary">{{ $sepultura->codigo_sepultura }}</a>
                                </td>
                                <!--end::Customer name=-->
                                <!--begin::Email=-->
                                <td>
                                    {{ \Illuminate\Support\Str::limit($sepultura->localizacao, 20) }}
                                </td>
                                <!--end::Email=-->
                                <!--begin::Status=-->
                                <td>
                                    <div
                                        class="badge badge-light-{{ $sepultura->status == 'disponível' ? 'success' : ($sepultura->status == 'ocupado' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($sepultura->status) }}
                                    </div>
                                </td>
                                <!--begin::Status=-->
                                <!--begin::Status=-->
                                <td>{{ \Carbon\Carbon::parse($sepultura->data_aquisicao)->isoFormat('D/MM/Y') }}</td>
                                <!--begin::Status=-->
                                <!--begin::No orders=-->
                                <td class="text-end pe-0">
                                    <div
                                        class="badge badge-light-{{ $sepultura->tipo == 'cemiterio familiar'
                                            ? 'info'
                                            : ($sepultura->tipo == 'cova'
                                                ? 'secondary'
                                                : ($sepultura->tipo == 'cripta'
                                                    ? 'dark'
                                                    : ($sepultura->tipo == 'jazigo'
                                                        ? 'primary'
                                                        : ($sepultura->tipo == 'mausoléu'
                                                            ? 'danger'
                                                            : ($sepultura->tipo == 'ossário'
                                                                ? 'warning'
                                                                : ($sepultura->tipo == 'sepultura vertical'
                                                                    ? 'success'
                                                                    : ($sepultura->tipo == 'terreno'
                                                                        ? 'warning'
                                                                        : 'default'))))))) }}">
                                        {{ ucfirst($sepultura->tipo) }}
                                    </div>
                                </td>
                                <!--end::No orders=-->
                                <!--begin::No products=-->
                                <td class="text-end pe-0">
                                    <a href="#"
                                        class="text-dark text-hover-primary">{{ number_format($sepultura->tamanho, 2, ',', '.') }}
                                        m²</a>
                                </td>
                                <!--end::No products=-->
                                <!--begin::Total=-->
                                <td class="text-end">
                                    <div class="ms-5">
                                        <!--begin::Edit-->
                                        <a href="{{ route('cemiterio.edit', $sepultura->id) }}"
                                            class="btn btn-icon btn-active-light-primary w-30px h-30px me-3">
                                            <span data-bs-toggle="tooltip" data-bs-trigger="hover" title="Edit">
                                                <!--begin::Svg Icon | path: icons/duotune/art/art005.svg-->
                                                <span class="svg-icon svg-icon-3">
                                                    <svg width="24" height="24" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path opacity="0.3"
                                                            d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z"
                                                            fill="currentColor" />
                                                        <path
                                                            d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z"
                                                            fill="currentColor" />
                                                    </svg>
                                                </span>
                                                <!--end::Svg Icon-->
                                            </span>
                                        </a>
                                        <!--end::Edit-->
                                        <!-- Botão para abrir o modal -->
                                        <a href="#"
                                            class="btn btn-icon btn-active-light-danger w-30px h-30px me-3"
                                            data-bs-toggle="modal" data-bs-target="#deleteModal">
                                            <!-- Ícone de exclusão -->
                                            <span class="svg-icon svg-icon-3">
                                                <svg width="24" height="24" viewBox="0 0 24 24"
                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z"
                                                        fill="currentColor" />
                                                    <path opacity="0.5"
                                                        d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z"
                                                        fill="currentColor" />
                                                    <path opacity="0.5"
                                                        d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z"
                                                        fill="currentColor" />
                                                </svg>
                                            </span>
                                        </a>

                                    </div>
                                </td>
                                <!--end::Total=-->
                            </tr>
                        @endforeach
                        <!--end::Table row-->
                    </tbody>
                    <!--end::Table body-->
                </table>
                <!--end::Table-->
            </div>
            <!--end::Modal body-->
        </div>
        <!--end::Modal content-->
    </div>
    <!--end::Modal dialog-->
</div>
<!--end::Modal - New Card-->
