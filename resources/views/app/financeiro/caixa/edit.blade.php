<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Material+Icons+Outlined">
<link rel="stylesheet" href="/assets/js/fileUpload/fileUpload.css">

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
                                            <div class="col-md-4 fv-row">
                                                <label class="required fs-5 fw-semibold mb-2">Lançamento Padrão</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" placeholder=""
                                                        name="lancamento_padrao"
                                                        value="{{ old('lancamento_padrao', $caixa->lancamento_padrao) }}" />
                                                </div>
                                                @error('lancamento_padrao')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 fv-row">
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
                                                        <div id="fileUpload"></div>
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
                                    <span class="indicator-progress">Please wait...
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
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

</x-tenant-app-layout>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="/assets/js/fileUpload/fileUpload.js"></script>

<script>
    $(document).ready(function () {
        $("#fileUpload").fileUpload();

    });
</script>
