<link href="https://kendo.cdn.telerik.com/themes/8.0.1/default/default-main.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<script src="https://kendo.cdn.telerik.com/2024.2.514/js/kendo.all.min.js"></script>
<x-tenant-app-layout>

    <!--begin::Main-->
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <!--begin::Content wrapper-->
        <div class="d-flex flex-column flex-column-fluid">
            <!--begin::Toolbar-->
            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                <!--begin::Toolbar container-->
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <!--begin::Page title-->
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <!--begin::Title-->
                        <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                            Lançamento de Caixa</h1>
                        <!--end::Title-->
                        <!--begin::Breadcrumb-->
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Ínicio</a>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-400 w-5px h-2px"></span>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">Financeiro</li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-400 w-5px h-2px"></span>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">Lançamento Caixa</li>
                            <!--end::Item-->
                        </ul>
                        <!--end::Breadcrumb-->
                    </div>
                    <!--end::Page title-->
                    <!--begin::Actions-->
                    <div class="d-flex align-items-center gap-2 gap-lg-3">
                        <!--begin::Filter menu-->
                        <div class="d-flex">
                            <select name="campaign-type" data-control="select2" data-hide-search="true"
                                class="form-select form-select-sm bg-body border-body w-175px">
                                <option value="Twitter" selected="selected">Select Campaign</option>
                                <option value="Twitter">Twitter Campaign</option>
                                <option value="Twitter">Facebook Campaign</option>
                                <option value="Twitter">Adword Campaign</option>
                                <option value="Twitter">Carbon Campaign</option>
                            </select>
                            <a href="#" class="btn btn-icon btn-sm btn-success flex-shrink-0 ms-4"
                                data-bs-toggle="modal" data-bs-target="#kt_modal_create_campaign">
                                <!--begin::Svg Icon | path: icons/duotune/arrows/arr075.svg-->
                                <span class="svg-icon svg-icon-2">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2"
                                            rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor" />
                                        <rect x="4.36396" y="11.364" width="16" height="2" rx="1"
                                            fill="currentColor" />
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                            </a>
                        </div>
                        <!--end::Filter menu-->
                        <!--begin::Secondary button-->
                        <!--end::Secondary button-->
                        <!--begin::Primary button-->
                        <!--end::Primary button-->
                    </div>
                    <!--end::Actions-->
                </div>
                <!--end::Toolbar container-->
            </div>
            <!--end::Toolbar-->
            <!--begin::Content-->
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <!--begin::Content container-->
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <form method="POST" action="{{ route('caixa.update', $caixa->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
                            <div class="card card-flush py-4">
                                <div class="card-header">
                                    <div class="card-title">
                                        <h2>Atualização do lançamento</h2>
                                    </div>
                                </div>
                                <div class="modal-body py-10 px-lg-17">
                                    <!-- Exibir erros de validação por campo -->
                                    @foreach ($errors->all() as $error)
                                        <div class="alert alert-danger mt-2">
                                            {{ $error }}
                                        </div>
                                    @endforeach
                                    <div class="scroll-y me-n7 pe-7" id="kt_modal_new_address_scroll"
                                        data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}"
                                        data-kt-scroll-max-height="auto"
                                        data-kt-scroll-dependencies="#kt_modal_new_address_header"
                                        data-kt-scroll-wrappers="#kt_modal_new_address_scroll"
                                        data-kt-scroll-offset="300px">
                                        <div class="row mb-5">
                                            <div class="col-md-3 fv-row">
                                                <label class="required fs-5 fw-semibold mb-2">Data de
                                                    Competência</label>
                                                <div class="input-group" id="kt_td_picker_date_only"
                                                    data-td-target-input="nearest" data-td-target-toggle="nearest">
                                                    <input class="form-control" name="data_competencia" required
                                                        type="date" placeholder="Pick a date" id="kt_datepicker_1"
                                                        value="{{ old('data_competencia', $caixa->data_competencia) }}" />
                                                    <span class="input-group-text"
                                                        data-td-target="#kt_td_picker_date_only"
                                                        data-td-toggle="datetimepicker">
                                                        <i class="ki-duotone ki-calendar fs-2"><span
                                                                class="path1"></span><span class="path2"></span></i>
                                                    </span>
                                                </div>
                                                @error('data_competencia')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 fv-row">
                                                <label class="required fs-5 fw-semibold mb-2">Descrição</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" placeholder=""
                                                        name="descricao"
                                                        value="{{ old('descricao', $caixa->descricao) }}" />
                                                </div>
                                                @error('descricao')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-3 fv-row">
                                                <label class="required fs-5 fw-semibold mb-2">Valor</label>
                                                <div class="input-group">
                                                    <span class="input-group-text" id="basic-addon1">R$</span>
                                                    <input class="form-control money" placeholder="Valor"
                                                        aria-label="Valor" aria-describedby="basic-addon1"
                                                        id="valor" name="valor" required
                                                        value="{{ old('valor', number_format($caixa->valor, 2, ',', '.')) }}" />
                                                </div>
                                                @error('valor')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row mb-5">
                                            <div class="col-md-2 fv-row">
                                                <label class="d-flex align-items-center fs-5 fw-semibold mb-2">
                                                    <span class="required">Entrada/Saída</span>
                                                    <i class="fas fa-exclamation-circle ms-2 fs-7"
                                                        data-bs-toggle="tooltip"
                                                        title="As categorias são utilizadas para formar um Plano de Contas. Muitas destas categorias são demonstradas em Relatórios e também alimentam o DRE Gerencial."></i>
                                                </label>
                                                <select class="form-select" data-control="select2"
                                                    data-dropdown-css-class="w-200px"
                                                    data-placeholder="Select an option" name="tipo" required
                                                    data-hide-search="true">
                                                    <option></option>
                                                    <option value="entrada"
                                                        {{ old('tipo', $caixa->tipo) == 'entrada' ? 'selected' : '' }}>
                                                        Entrada</option>
                                                    <option value="saida"
                                                        {{ old('tipo', $caixa->tipo) == 'saida' ? 'selected' : '' }}>
                                                        Saída</option>
                                                </select>
                                                @error('tipo')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 fv-row">
                                                <label class="required fs-5 fw-semibold mb-2">Lançamento Padrão</label>
                                                <div class="input-group">
                                                    <select name="lancamento_padrao" aria-label="Select a Country"
                                                        data-control="select2"
                                                        data-placeholder="Escolha um Lançamento..."
                                                        class="form-select  fw-bold">
                                                        <option value=""></option>
                                                        @foreach ($lps as $lp)
                                                            <option value="{{ $lp->description }}">
                                                                {{ $lp->description }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                @error('lancamento_padrao')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-4 fv-row">
                                                <label class="fs-5 fw-semibold mb-2">Centro de Custo</label>
                                                <div class="input-group">
                                                    <input type="text" name="centro" class="form-control"
                                                        placeholder="" value="{{ old('centro', $caixa->centro) }}" />
                                                </div>
                                                @error('centro')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row mb-5">
                                            <div class="col-md-4 fv-row">
                                                <label class="d-flex align-items-center fs-5 fw-semibold mb-2">
                                                    <span class="required">Tipo de Documento</span>
                                                    <i class="fas fa-exclamation-circle ms-2 fs-7"
                                                        data-bs-toggle="tooltip"
                                                        title="As categorias são utilizadas para formar um Plano de Contas. Muitas destas categorias são demonstradas em Relatórios e também alimentam o DRE Gerencial."></i>
                                                </label>
                                                <select class="form-control" name="tipo_documento"
                                                    id="tipo_documento">
                                                    <option value="Pix"
                                                        {{ old('tipo_documento', $caixa->tipo_documento) == 'Pix' ? 'selected' : '' }}>
                                                        Pix</option>
                                                    <option value="OUTR - Dafe"
                                                        {{ old('tipo_documento', $caixa->tipo_documento) == 'OUTR - Dafe' ? 'selected' : '' }}>
                                                        OUTR - Dafe</option>
                                                    <option value="NF - Nota Fiscal"
                                                        {{ old('tipo_documento', $caixa->tipo_documento) == 'NF - Nota Fiscal' ? 'selected' : '' }}>
                                                        NF - Nota Fiscal</option>
                                                    <option value="DANF - Danfe"
                                                        {{ old('tipo_documento', $caixa->tipo_documento) == 'DANF - Danfe' ? 'selected' : '' }}>
                                                        DANF - Danfe</option>
                                                    <option value="BOL - Boleto"
                                                        {{ old('tipo_documento', $caixa->tipo_documento) == 'BOL - Boleto' ? 'selected' : '' }}>
                                                        BOL - Boleto</option>
                                                    <option value="REP - Repasse"
                                                        {{ old('tipo_documento', $caixa->tipo_documento) == 'REP - Repasse' ? 'selected' : '' }}>
                                                        REP - Repasse</option>
                                                    <option value="CCRD - Cartão de Credito"
                                                        {{ old('tipo_documento', $caixa->tipo_documento) == 'CCRD - Cartão de Credito' ? 'selected' : '' }}>
                                                        CCRD - Cartão de Credito</option>
                                                    <option value="CTRB - Cartão de Débito"
                                                        {{ old('tipo_documento', $caixa->tipo_documento) == 'CTRB - Cartão de Débito' ? 'selected' : '' }}>
                                                        CTRB - Cartão de Débito</option>
                                                    <option value="REC - Recibo"
                                                        {{ old('tipo_documento', $caixa->tipo_documento) == 'REC - Recibo' ? 'selected' : '' }}>
                                                        REC - Recibo</option>
                                                    <option value="CARN - Carnê"
                                                        {{ old('tipo_documento', $caixa->tipo_documento) == 'CARN - Carnê' ? 'selected' : '' }}>
                                                        CARN - Carnê</option>
                                                    <option value="FAT - Fatura"
                                                        {{ old('tipo_documento', $caixa->tipo_documento) == 'FAT - Fatura' ? 'selected' : '' }}>
                                                        FAT - Fatura</option>
                                                    <option value="APOL - Apólice"
                                                        {{ old('tipo_documento', $caixa->tipo_documento) == 'APOL - Apólice' ? 'selected' : '' }}>
                                                        APOL - Apólice</option>
                                                    <option value="DUPL - Duplicata"
                                                        {{ old('tipo_documento', $caixa->tipo_documento) == 'DUPL - Duplicata' ? 'selected' : '' }}>
                                                        DUPL - Duplicata</option>
                                                    <option value="TRIB - Tribunal"
                                                        {{ old('tipo_documento', $caixa->tipo_documento) == 'TRIB - Tribunal' ? 'selected' : '' }}>
                                                        TRIB - Tribunal</option>
                                                    <option value="Outros"
                                                        {{ old('tipo_documento', $caixa->tipo_documento) == 'Outros' ? 'selected' : '' }}>
                                                        Outros</option>
                                                    <option value="T Banc - Transferência Bancaria"
                                                        {{ old('tipo_documento', $caixa->tipo_documento) == 'T Banc - Transferência Bancaria' ? 'selected' : '' }}>
                                                        T Banc - Transferência Bancaria</option>
                                                </select>
                                                @error('tipo_documento')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 fv-row">
                                                <label class="fs-5 fw-semibold mb-2">Número do Documento</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" placeholder=""
                                                        name="numero_documento"
                                                        value="{{ old('numero_documento', $caixa->numero_documento) }}" />
                                                </div>
                                                @error('numero_documento')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column mb-5 fv-row">
                                            <ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
                                                <li class="nav-item">
                                                    <a class="nav-link active" data-bs-toggle="tab"
                                                        href="#kt_tab_pane_1">Histórico complementar</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" data-bs-toggle="tab"
                                                        href="#kt_tab_pane_2">Anexos</a>
                                                </li>
                                            </ul>
                                            <div class="tab-content" id="myTabContent">
                                                <div class="tab-pane fade show active" id="kt_tab_pane_1"
                                                    role="tabpanel">
                                                    <textarea class="form-control" name="historico_complementar" id="complemento" cols="20" rows="3">{{ old('historico_complementar', $caixa->historico_complementar) }}</textarea>
                                                    <p class="mensagem-vermelha">Descreva observações relevantes sobre
                                                        esse lançamento financeiro</p>
                                                    @error('historico_complementar')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="tab-pane fade" id="kt_tab_pane_2" role="tabpanel">
                                                    <div class="parent-div">
                                                        <!--begin::Card-->
                                                        <div class="card card-flush">
                                                            <!--begin::Card header-->
                                                            <div class="card-header pt-8">
                                                                <div class="card-title">
                                                                    <!--begin::Search-->
                                                                    <div
                                                                        class="d-flex align-items-center position-relative my-1">
                                                                        <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                                                                        <span
                                                                            class="svg-icon svg-icon-1 position-absolute ms-6">
                                                                            <svg width="24" height="24"
                                                                                viewBox="0 0 24 24" fill="none"
                                                                                xmlns="http://www.w3.org/2000/svg">
                                                                                <rect opacity="0.5" x="17.0365"
                                                                                    y="15.1223" width="8.15546"
                                                                                    height="2" rx="1"
                                                                                    transform="rotate(45 17.0365 15.1223)"
                                                                                    fill="currentColor" />
                                                                                <path
                                                                                    d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z"
                                                                                    fill="currentColor" />
                                                                            </svg>
                                                                        </span>
                                                                        <!--end::Svg Icon-->
                                                                        <input type="text"
                                                                            data-kt-filemanager-table-filter="search"
                                                                            class="form-control form-control-solid w-250px ps-15"
                                                                            placeholder="Pesquisar arquivos" />
                                                                    </div>
                                                                    <!--end::Search-->
                                                                </div>
                                                                <!--begin::Card toolbar-->
                                                                <div class="card-toolbar">
                                                                    <!--begin::Toolbar-->
                                                                    <div class="d-flex justify-content-end"
                                                                        data-kt-filemanager-table-toolbar="base">
                                                                        <!--begin::Add customer-->
                                                                        <button type="button" class="btn btn-primary"
                                                                            data-bs-toggle="modal"
                                                                            data-bs-target="#kt_modal_upload">
                                                                            <!--begin::Svg Icon | path: icons/duotune/files/fil018.svg-->
                                                                            <span class="svg-icon svg-icon-2">
                                                                                <svg width="24" height="24"
                                                                                    viewBox="0 0 24 24" fill="none"
                                                                                    xmlns="http://www.w3.org/2000/svg">
                                                                                    <path opacity="0.3"
                                                                                        d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z"
                                                                                        fill="currentColor" />
                                                                                    <path
                                                                                        d="M10.4 3.60001L12 6H21C21.6 6 22 6.4 22 7V19C22 19.6 21.6 20 21 20H3C2.4 20 2 19.6 2 19V4C2 3.4 2.4 3 3 3H9.20001C9.70001 3 10.2 3.20001 10.4 3.60001ZM16 11.6L12.7 8.29999C12.3 7.89999 11.7 7.89999 11.3 8.29999L8 11.6H11V17C11 17.6 11.4 18 12 18C12.6 18 13 17.6 13 17V11.6H16Z"
                                                                                        fill="currentColor" />
                                                                                    <path opacity="0.3"
                                                                                        d="M11 11.6V17C11 17.6 11.4 18 12 18C12.6 18 13 17.6 13 17V11.6H11Z"
                                                                                        fill="currentColor" />
                                                                                </svg>
                                                                            </span>
                                                                            <!--end::Svg Icon-->Adicionar Mais</button>
                                                                        <!--end::Add customer-->
                                                                    </div>
                                                                    <!--end::Toolbar-->
                                                                    <!--begin::Group actions-->
                                                                    <div class="d-flex justify-content-end align-items-center d-none"
                                                                        data-kt-filemanager-table-toolbar="selected">
                                                                        <div class="fw-bold me-5">
                                                                            <span class="me-2"
                                                                                data-kt-filemanager-table-select="selected_count"></span>Selecionado
                                                                        </div>
                                                                        <button type="button" class="btn btn-danger"
                                                                            data-kt-filemanager-table-select="delete_selected">Excluir
                                                                            Selecionado</button>
                                                                    </div>
                                                                    <!--end::Group actions-->
                                                                </div>
                                                                <!--end::Card toolbar-->
                                                            </div>
                                                            <!--end::Card header-->
                                                            <!--begin::Card body-->
                                                            <div class="card-body">
                                                                <!--begin::Table-->
                                                                <table id="kt_file_manager_list"
                                                                    data-kt-filemanager-table="files"
                                                                    class="table align-middle table-row-dashed fs-6 gy-5">
                                                                    <!--begin::Table head-->
                                                                    <thead>
                                                                        <!--begin::Table row-->
                                                                        <tr
                                                                            class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                                                            <th class="w-10px pe-2">
                                                                                <div
                                                                                    class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                                                                    <input class="form-check-input"
                                                                                        type="checkbox"
                                                                                        data-kt-check="true"
                                                                                        data-kt-check-target="#kt_file_manager_list .form-check-input"
                                                                                        value="1" />
                                                                                </div>
                                                                            </th>
                                                                            <th class="min-w-250px">Nome</th>
                                                                            <th class="min-w-10px">Tamanho</th>
                                                                            <th class="min-w-125px">ÚLTIMA MODIFICAÇÃO
                                                                            </th>
                                                                            <th class="w-125px"></th>
                                                                        </tr>
                                                                        <!--end::Table row-->
                                                                    </thead>
                                                                    <!--end::Table head-->
                                                                    <!--begin::Table body-->
                                                                    <tbody class="fw-semibold text-gray-600">
                                                                        @foreach ($caixa->anexos as $file)
                                                                            <tr data-file-id="{{ $file->id }}">
                                                                                <!--begin::Checkbox-->
                                                                                <td>
                                                                                    <div
                                                                                        class="form-check form-check-sm form-check-custom form-check-solid">
                                                                                        <input class="form-check-input"
                                                                                            type="checkbox"
                                                                                            value="1" />
                                                                                    </div>
                                                                                </td>
                                                                                <!--end::Checkbox-->
                                                                                <!--begin::Name-->
                                                                                <td>
                                                                                    <div
                                                                                        class="d-flex align-items-center">
                                                                                        <!--begin::Svg Icon-->
                                                                                        <span
                                                                                            class="svg-icon svg-icon-2x svg-icon-primary me-4">
                                                                                            <svg width="24"
                                                                                                height="24"
                                                                                                viewBox="0 0 24 24"
                                                                                                fill="none"
                                                                                                xmlns="http://www.w3.org/2000/svg">
                                                                                                <path opacity="0.3"
                                                                                                    d="M19 22H5C4.4 22 4 21.6 4 21V3C4 2.4 4.4 2 5 2H14L20 8V21C20 21.6 19.6 22 19 22Z"
                                                                                                    fill="currentColor" />
                                                                                                <path
                                                                                                    d="M15 8H20L14 2V7C14 7.6 14.4 8 15 8Z"
                                                                                                    fill="currentColor" />
                                                                                            </svg>
                                                                                        </span>
                                                                                        <!--end::Svg Icon-->
                                                                                        <a href="#"
                                                                                            class="text-gray-800 text-hover-primary">{{ $file->nome_arquivo }}</a>
                                                                                    </div>
                                                                                </td>
                                                                                <!--end::Name-->
                                                                                <!--begin::Size-->
                                                                                <td>{{ $file->size ?? 'N/A' }}</td>
                                                                                <!--end::Size-->
                                                                                <!--begin::Last modified-->
                                                                                <td>{{ $file->updated_at->format('d M Y, g:i a') }}
                                                                                </td>
                                                                                <!--end::Last modified-->
                                                                                <!--begin::Actions-->
                                                                                <td class="text-end"
                                                                                    data-kt-filemanager-table="action_dropdown">
                                                                                    <div
                                                                                        class="d-flex justify-content-end">
                                                                                        <!--begin::Share link-->
                                                                                        <div class="ms-2"
                                                                                            data-kt-filemanager-table="copy_link">
                                                                                            <a type="button"
                                                                                                href="{{ route('file', ['path' => $file->caminho_arquivo]) }}"
                                                                                                target="_blank"
                                                                                                class="btn btn-sm btn-icon btn-light btn-active-light-primary"
                                                                                                data-kt-menu-trigger="click"
                                                                                                data-kt-menu-placement="bottom-end">
                                                                                                <!--begin::Svg Icon-->
                                                                                                <span
                                                                                                    class="svg-icon svg-icon-5 m-0">
                                                                                                    <svg width="24"
                                                                                                        height="24"
                                                                                                        viewBox="0 0 24 24"
                                                                                                        fill="none"
                                                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                                                        <path
                                                                                                            opacity="0.3"
                                                                                                            d="M18.4 5.59998C18.7766 5.9772 18.9881 6.48846 18.9881 7.02148C18.9881 7.55451 18.7766 8.06577 18.4 8.44299L14.843 12C14.466 12.377 13.9547 12.5887 13.4215 12.5887C12.8883 12.5887 12.377 12.377 12 12C11.623 11.623 11.4112 11.1117 11.4112 10.5785C11.4112 10.0453 11.623 9.53399 12 9.15698L15.553 5.604C15.9302 5.22741 16.4415 5.01587 16.9745 5.01587C17.5075 5.01587 18.0188 5.22741 18.396 5.604L18.4 5.59998ZM20.528 3.47205C20.0614 3.00535 19.5074 2.63503 18.8977 2.38245C18.288 2.12987 17.6344 1.99988 16.9745 1.99988C16.3145 1.99988 15.661 2.12987 15.0513 2.38245C14.4416 2.63503 13.8876 3.00535 13.421 3.47205L9.86801 7.02502C9.40136 7.49168 9.03118 8.04568 8.77863 8.6554C8.52608 9.26511 8.39609 9.91855 8.39609 10.5785C8.39609 11.2384 8.52608 11.8919 8.77863 12.5016C9.03118 13.1113 9.40136 13.6653 9.86801 14.132C10.3347 14.5986 10.8886 14.9688 11.4984 15.2213C12.1081 15.4739 12.7616 15.6039 13.4215 15.6039C14.0815 15.6039 14.7349 15.4739 15.3446 15.2213C15.9543 14.9688 16.5084 14.5986 16.975 14.132L20.528 10.579C20.9947 10.1124 21.3649 9.55844 21.6175 8.94873C21.8701 8.33902 22.0001 7.68547 22.0001 7.02551C22.0001 6.36555 21.8701 5.71201 21.6175 5.10229C21.3649 4.49258 20.9947 3.93867 20.528 3.47205Z"
                                                                                                            fill="currentColor" />
                                                                                                        <path
                                                                                                            d="M14.132 9.86804C13.6421 9.37931 13.0561 8.99749 12.411 8.74695L12 9.15698C11.6234 9.53421 11.4119 10.0455 11.4119 10.5785C11.4119 11.1115 11.6234 11.6228 12 12C12.3766 12.3772 12.5881 12.8885 12.5881 13.4215C12.5881 13.9545 12.3766 14.4658 12 14.843L8.44699 18.396C8.06999 18.773 7.55868 18.9849 7.02551 18.9849C6.49235 18.9849 5.98101 18.773 5.604 18.396C5.227 18.019 5.0152 17.5077 5.0152 16.9745C5.0152 16.4413 5.227 15.93 5.604 15.553L8.74701 12.411C8.28705 11.233 8.28705 9.92498 8.74701 8.74695C8.10159 8.99532 7.50441 9.37386 6.99841 9.86804L3.47198 13.421C2.99954 13.932 2.61101 14.5061 2.314 15.1297C2.01699 15.7534 1.81673 16.4178 1.7259 17.1034C1.63508 17.7891 1.65439 18.4851 1.78249 19.1621C1.91059 19.8391 2.14555 20.4914 2.47702 21.1047C2.80849 21.718 3.24346 22.2832 3.73675 22.7765C4.23005 23.2698 4.79526 23.7048 5.40855 24.0363C6.02184 24.3678 6.67411 24.6028 7.35108 24.7309C8.02805 24.859 8.72407 24.8783 9.40973 24.7875C10.0954 24.6967 10.7598 24.4964 11.3834 24.1994C12.007 23.9024 12.5811 23.5138 13.092 23.0414L14.132 21.867L14.384 21.383Z"
                                                                                                            fill="currentColor" />
                                                                                                    </svg>
                                                                                                </span>
                                                                                                <!--end::Svg Icon-->
                                                                                            </a>
                                                                                        </div>
                                                                                        <!--end::Share link-->
                                                                                        <!--begin::More-->
                                                                                        <div class="ms-2">
                                                                                            <button type="button" class="btn btn-sm btn-icon btn-light btn-active-light-primary delete-file-button" data-file-id="{{ $file->id }}" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">
                                                                                                <i class="fa-duotone fa-solid fa-trash-can text-danger"></i>
                                                                                            </button>
                                                                                        </div>

                                                                                        <!--end::More-->
                                                                                    </div>
                                                                                </td>
                                                                                <!--end::Actions-->
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                    <!--end::Table body-->
                                                                </table>
                                                                <!--end::Table-->
                                                            </div>
                                                            <!--end::Card body-->

                                                        </div>
                                                        <!--end::Card-->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <a href="{{ route('caixa.index') }}" id="kt_ecommerce_add_product_cancel"
                                    class="btn btn-light me-5">Voltar</a>
                                <button type="submit" class="btn btn-primary">
                                    <span class="indicator-label">Atualizar</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <!--end::Content container-->
            </div>
            <!--end::Content-->
        </div>
        <!--end::Content wrapper-->
    </div>
    <!--end:::Main-->

    <!-- Modal Excluir -->
<!--begin::Modal - Confirm Delete-->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar Exclusão</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Tem certeza de que deseja excluir este arquivo?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteButton">Excluir</button>
            </div>
        </div>
    </div>
</div>
<!--end::Modal - Confirm Delete-->

    <!--begin::Modal - Upload File-->
    <div class="modal fade" id="kt_modal_upload" tabindex="-1" aria-hidden="true">
        <!--begin::Modal dialog-->
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <!--begin::Modal content-->
            <div class="modal-content">
                <!--begin::Form-->
                <form id="uploadForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <!--begin::Modal header-->
                    <div class="modal-header">
                        <!--begin::Modal title-->
                        <h2 class="fw-bold">Upload files</h2>
                        <!--end::Modal title-->
                        <!--begin::Close-->
                        <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                            <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
                            <span class="svg-icon svg-icon-1">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <rect opacity="0.5" x="6" y="17.3137" width="16" height="2"
                                        rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
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
                    <div class="modal-body pt-10 pb-15 px-lg-17">
                        <!--begin::Input group-->
                        <div class="form-group">
                            <!--begin::Dropzone-->
                            <div class="dropzone dropzone-queue mb-2" id="kt_dropzonejs_example_2">
                                <input type="file" name="files[]" id="photos" />
                                <script>
                                    $("#photos").kendoUpload({
                                        async: {
                                            removeUrl: "{{ url('/remove') }}",
                                            removeField: "path",
                                            withCredentials: false
                                        },
                                        multiple: true, // Permite a seleção de múltiplos arquivos
                                        validation: {
                                            allowedExtensions: ["jpg", "jpeg", "png", "pdf"], // Extensões permitidas
                                            maxFileSize: 5242880, // Tamanho máximo do arquivo (5 MB)
                                            minFileSize: 1024 // Tamanho mínimo do arquivo (1 KB)
                                        },
                                        localization: {
                                            uploadSuccess: "Upload bem-sucedido!",
                                            uploadFail: "Falha no upload",
                                            invalidFileExtension: "Tipo de arquivo não permitido",
                                            invalidMaxFileSize: "O arquivo é muito grande",
                                            invalidMinFileSize: "O arquivo é muito pequeno",
                                            select: "Anexar Arquivos"

                                        }
                                    });
                                </script>
                            </div>
                            <!--end::Dropzone-->
                            <!--begin::Hint-->
                            <span class="form-text fs-6 text-muted">O tamanho máximo do arquivo é 5 MB por
                                arquivo.</span>
                            <!--end::Hint-->

                        </div>
                        <!--end::Input group-->
                        <button type="submit" class="btn btn-primary">
                            <span class="indicator-label">Atualizar</span>
                        </button>
                    </div>
                    <!--end::Modal body-->
                </form>
                <!--end::Form-->
            </div>
        </div>
    </div>
    <!--end::Modal - Upload File-->

</x-tenant-app-layout>


<!--begin::Vendors Javascript(used for this page only)-->
<script src="/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<!--end::Vendors Javascript-->
<!--begin::Custom Javascript(used for this page only)-->
<script src="/assets/js/custom/apps/file-manager/list.js"></script>
<script src="/assets/js/widgets.bundle.js"></script>
<script src="/assets/js/custom/apps/chat/chat.js"></script>
<script src="/assets/js/custom/utilities/modals/upgrade-plan.js"></script>
<script src="/assets/js/custom/utilities/modals/create-campaign.js"></script>
<script src="/assets/js/custom/utilities/modals/users-search.js"></script>

<!--end::Custom Javascript-->
<script>
    $(document).ready(function() {
        $('#uploadForm').on('submit', function(e) {
            e.preventDefault();

            let formData = new FormData(this);
            $.ajax({
                url: "{{ route('anexos.update', $caixa->id) }}",
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    // Fechar o modal
                    $('#kt_modal_upload').modal('hide');
                    // Exibir mensagem de sucesso (você pode personalizar isso)
                    alert('Arquivos enviados com sucesso!');
                    // Atualizar a lista de anexos ou fazer qualquer outra ação necessária
                    location.reload();
                },
                error: function(xhr) {
                    // Exibir mensagens de erro
                    alert('Erro ao enviar os arquivos.');
                }
            });
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
    var deleteFileId;

    // Capture the delete button click event
    document.querySelectorAll('.delete-file-button').forEach(button => {
        button.addEventListener('click', function () {
            deleteFileId = this.getAttribute('data-file-id');
        });
    });

    // Handle the confirm delete button click event
    document.getElementById('confirmDeleteButton').addEventListener('click', function () {
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = `/anexos/${deleteFileId}`;

        var csrfField = document.createElement('input');
        csrfField.type = 'hidden';
        csrfField.name = '_token';
        csrfField.value = '{{ csrf_token() }}';

        var methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';

        form.appendChild(csrfField);
        form.appendChild(methodField);

        document.body.appendChild(form);
        form.submit();
    });
});

</script>
