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
                            Lançamento Bancário</h1>
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
                            <li class="breadcrumb-item text-muted">Lançamento Bancário</li>
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
                    <form method="POST" action="{{ route('banco.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
                            <div class="card card-flush py-4">
                                <div class="card-header">
                                    <div class="card-title">
                                    <!--begin::Svg Icon | path: icons/duotune/finance/fin006.svg-->
                                    <span class="svg-icon svg-icon-3x me-5">
                                        <svg version="1.1" id="_x34_" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512.00 512.00" xml:space="preserve" width="256px" height="256px" fill="#000000" stroke="#000000" stroke-width="4.096"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="1.024"></g><g id="SVGRepo_iconCarrier"> <g> <polygon style="fill:#EFEEEF;" points="474.016,135.427 493.838,135.427 493.838,85.881 256.001,0 18.162,85.881 18.162,135.427 37.985,135.427 "></polygon> <polygon style="fill:#E3E1E1;" points="50.81,105.702 256.001,31.602 461.19,105.702 "></polygon> <polygon style="fill:#CBCBCB;" points="270.627,36.883 256.001,31.602 50.81,105.702 80.063,105.702 "></polygon> <polygon style="fill:#CBCBCB;" points="434.38,189.938 444.283,189.938 444.283,170.114 365.004,170.114 365.004,189.938 374.914,189.938 "></polygon> <polygon style="fill:#CBCBCB;" points="374.914,402.988 365.004,402.988 365.004,422.81 444.283,422.81 444.283,402.988 434.38,402.988 "></polygon> <rect x="374.914" y="189.938" style="fill:#D8D8D9;" width="59.465" height="213.05"></rect> <rect x="226.267" y="189.938" style="fill:#D8D8D9;" width="59.457" height="213.05"></rect> <rect x="77.62" y="189.938" style="fill:#D8D8D9;" width="59.465" height="213.05"></rect> <g> <rect x="102.397" y="189.938" style="fill:#CBCBCB;" width="9.912" height="213.05"></rect> <rect x="82.575" y="189.938" style="fill:#CBCBCB;" width="9.919" height="213.05"></rect> <rect x="122.219" y="189.938" style="fill:#CBCBCB;" width="9.912" height="213.05"></rect> </g> <g> <rect x="251.044" y="189.938" style="fill:#CBCBCB;" width="9.912" height="213.05"></rect> <rect x="231.231" y="189.938" style="fill:#CBCBCB;" width="9.903" height="213.05"></rect> <rect x="270.866" y="189.938" style="fill:#CBCBCB;" width="9.903" height="213.05"></rect> </g> <g> <rect x="399.693" y="189.938" style="fill:#CBCBCB;" width="9.91" height="213.05"></rect> <rect x="379.878" y="189.938" style="fill:#CBCBCB;" width="9.903" height="213.05"></rect> <rect x="419.515" y="189.938" style="fill:#CBCBCB;" width="9.901" height="213.05"></rect> </g> <polygon style="fill:#CBCBCB;" points="285.724,189.938 295.645,189.938 295.645,170.114 216.364,170.114 216.364,189.938 226.267,189.938 "></polygon> <polygon style="fill:#CBCBCB;" points="226.267,402.988 216.364,402.988 216.364,422.81 295.645,422.81 295.645,402.988 285.724,402.988 "></polygon> <polygon style="fill:#CBCBCB;" points="137.086,189.938 146.996,189.938 146.996,170.114 67.717,170.114 67.717,189.938 77.62,189.938 "></polygon> <polygon style="fill:#CBCBCB;" points="77.62,402.988 67.717,402.988 67.717,422.81 146.996,422.81 146.996,402.988 137.086,402.988 "></polygon> <g> <polygon style="fill:#EFEEEF;" points="37.985,462.446 18.162,462.446 18.162,512 493.838,512 493.838,462.446 474.016,462.446 "></polygon> <rect x="37.985" y="422.81" style="fill:#D8D8D9;" width="436.031" height="39.637"></rect> </g> <rect x="37.985" y="135.427" style="fill:#D8D8D9;" width="436.031" height="34.687"></rect> </g> </g></svg>
                                    </span>
                                    <!--end::Svg Icon-->
                                        <h2>Informações do lançamento</h2>
                                    </div>
                                </div>
                                <div class="modal-body py-10 px-lg-17">
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
                                            <div class="col-md-2 fv-row">
                                                <label class="required fs-5 fw-semibold mb-2">Data de
                                                    Competência</label>
                                                <div class="input-group" id="kt_td_picker_date_only"
                                                    data-td-target-input="nearest" data-td-target-toggle="nearest">
                                                    <input class="form-control" name="data_competencia" type="date"
                                                        placeholder="Pick a date" id="kt_datepicker_1"
                                                        value="{{ old('data_competencia', now()->format('Y-m-d')) }}" />
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
                                            <div class="col-md-3 fv-row">
                                                <label class="required fs-5 fw-semibold mb-2">Banco</label>
                                                <div class="input-group">
                                                    <select id="bancoSelect" name="banco_id" aria-label="Select a Banco" data-control="select2" data-placeholder="Escolha um banco..." class="form-select fw-bold" required>
                                                        <option value=""></option>
                                                        @foreach ($bancos as $banco)
                                                        <option data-banco-code="{{ $banco->banco }}" value="{{ $banco->id }}"><span class="banco-name"></span>{{ $banco->banco }} - {{ $banco->name }}/{{ $banco->conta }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                @error('banco_id')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-5 fv-row">
                                                <label class="required fs-5 fw-semibold mb-2">Descrição</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" placeholder=""
                                                        name="descricao" value="{{ old('descricao') }}" />
                                                </div>
                                                @error('descricao')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-2 fv-row">
                                                <label class="required fs-5 fw-semibold mb-2">Valor</label>
                                                <div class="input-group">
                                                    <span class="input-group-text" id="basic-addon1">R$</span>
                                                    <input class="form-control money" placeholder="Valor"
                                                        aria-label="Valor" aria-describedby="basic-addon1"
                                                        id="valor" name="valor" required
                                                        value="{{ old('valor') }}" />
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

                                                </label>
                                                <select class="form-select" data-control="select2"
                                                    data-dropdown-css-class="w-200px"
                                                    data-placeholder="Selecione o tipo" name="tipo" required
                                                    data-hide-search="true">
                                                    <option></option>
                                                    <option value="entrada"
                                                        {{ old('tipo') == 'entrada' ? 'selected' : '' }}>Entrada
                                                    </option>
                                                    <option value="saida"
                                                        {{ old('tipo') == 'saida' ? 'selected' : '' }}>Saída</option>
                                                </select>
                                                @error('tipo')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 fv-row">

                                                <label class="required fs-5 fw-semibold mb-2">Lançamento Padrão</label>
                                                <i class="fas fa-exclamation-circle ms-2 fs-7"
                                                        data-bs-toggle="tooltip"
                                                        title="As categorias são utilizadas para formar um Plano de Contas. Muitas destas categorias são demonstradas em Relatórios e também alimentam o DRE Gerencial."></i>
                                                <div class="input-group">
                                                    <select name="lancamento_padrao" aria-label="Select a Country" data-control="select2" data-placeholder="Escolha um Lançamento..." class="form-select  fw-bold" id="lancamento_padrao">
                                                        <option value=""></option>
                                                        @foreach ($lps as $lp)
                                                        <option value="{{ $lp->description }}" data-type="{{ $lp->type }}">{{ $lp->description }} </option>
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
                                                    <input type="text" name="centro" readonly class="form-control"
                                                        placeholder="" value="{{ $company->first()->companies_name }}"  />
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
                                                        {{ old('tipo_documento') == 'Pix' ? 'selected' : '' }}>Pix
                                                    </option>
                                                    <option value="OUTR - Dafe"
                                                        {{ old('tipo_documento') == 'OUTR - Dafe' ? 'selected' : '' }}>
                                                        OUTR - Dafe</option>
                                                    <option value="NF - Nota Fiscal"
                                                        {{ old('tipo_documento') == 'NF - Nota Fiscal' ? 'selected' : '' }}>
                                                        NF - Nota Fiscal</option>
                                                    <option value="DANF - Danfe"
                                                        {{ old('tipo_documento') == 'DANF - Danfe' ? 'selected' : '' }}>
                                                        DANF - Danfe</option>
                                                    <option value="BOL - Boleto"
                                                        {{ old('tipo_documento') == 'BOL - Boleto' ? 'selected' : '' }}>
                                                        BOL - Boleto</option>
                                                    <option value="REP - Repasse"
                                                        {{ old('tipo_documento') == 'REP - Repasse' ? 'selected' : '' }}>
                                                        REP - Repasse</option>
                                                    <option value="CCRD - Cartão de Credito"
                                                        {{ old('tipo_documento') == 'CCRD - Cartão de Credito' ? 'selected' : '' }}>
                                                        CCRD - Cartão de Credito</option>
                                                    <option value="CDBT - Cartão de Debito"
                                                        {{ old('tipo_documento') == 'CDBT - Cartão de Debito' ? 'selected' : '' }}>
                                                        CDBT - Cartão de Debito</option>
                                                    <option value="CH - Cheque"
                                                        {{ old('tipo_documento') == 'CH - Cheque' ? 'selected' : '' }}>
                                                        CH - Cheque</option>
                                                    <option value="REC - Recibo"
                                                        {{ old('tipo_documento') == 'REC - Recibo' ? 'selected' : '' }}>
                                                        REC - Recibo</option>
                                                    <option value="CARN - Carnê"
                                                        {{ old('tipo_documento') == 'CARN - Carnê' ? 'selected' : '' }}>
                                                        CARN - Carnê</option>
                                                    <option value="FAT - Fatura"
                                                        {{ old('tipo_documento') == 'FAT - Fatura' ? 'selected' : '' }}>
                                                        FAT - Fatura</option>
                                                    <option value="APOL - Apólice"
                                                        {{ old('tipo_documento') == 'APOL - Apólice' ? 'selected' : '' }}>
                                                        APOL - Apólice</option>
                                                    <option value="DUPL - Duplicata"
                                                        {{ old('tipo_documento') == 'DUPL - Duplicata' ? 'selected' : '' }}>
                                                        DUPL - Duplicata</option>
                                                    <option value="TRIB - Tribunal"
                                                        {{ old('tipo_documento') == 'TRIB - Tribunal' ? 'selected' : '' }}>
                                                        TRIB - Tribunal</option>
                                                    <option value="Outros"
                                                        {{ old('tipo_documento') == 'Outros' ? 'selected' : '' }}>
                                                        Outros</option>
                                                    <option value="T Banc - Transferência Bancaria"
                                                        {{ old('tipo_documento') == 'T Banc - Transferência Bancaria' ? 'selected' : '' }}>
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
                                                        value="{{ old('numero_documento') }}" />
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
                                                    <textarea class="form-control" name="historico_complementar" id="complemento" cols="20" rows="3">{{ old('historico_complementar') }}</textarea>
                                                    <p class="mensagem-vermelha">Descreva observações relevantes sobre
                                                        esse lançamento financeiro</p>
                                                    @error('historico_complementar')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="tab-pane fade" id="kt_tab_pane_2" role="tabpanel">
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
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <a href="{{ route('caixa.index') }}" id="kt_ecommerce_add_product_cancel"
                                    class="btn btn-secondary me-2 mb-2">Voltar</a>
                                <a href="{{ route('banco.list') }}" class="btn btn-warning me-2 mb-2">
                                    <i class="bi bi-search fs-1"></i>
                                    Pesquisar
                                </a>
                                <button type="submit" class="btn btn-primary me-2 mb-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="currentColor" class="bi bi-floppy2 fs-1" viewBox="0 0 16 16">
                                        <path
                                            d="M1.5 0h11.586a1.5 1.5 0 0 1 1.06.44l1.415 1.414A1.5 1.5 0 0 1 16 2.914V14.5a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 14.5v-13A1.5 1.5 0 0 1 1.5 0M1 1.5v13a.5.5 0 0 0 .5.5H2v-4.5A1.5 1.5 0 0 1 3.5 9h9a1.5 1.5 0 0 1 1.5 1.5V15h.5a.5.5 0 0 0 .5-.5V2.914a.5.5 0 0 0-.146-.353l-1.415-1.415A.5.5 0 0 0 13.086 1H13v3.5A1.5 1.5 0 0 1 11.5 6h-7A1.5 1.5 0 0 1 3 4.5V1H1.5a.5.5 0 0 0-.5.5m9.5-.5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5z" />
                                    </svg>
                                    <span class="indicator-label">Lançar</span>
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

<script src="assets/js/custom/apps/bancos/bancos.js"></script>


<script>
    const bancoList = {
        '001': { name: 'Banco do Brasil S.A', svg: 'assets/media/svg/bancos/brasil.svg' },
        '033': { name: 'Banco Santander Brasil S.A', svg: 'assets/media/svg/bancos/santander.svg' },
        '237': { name: 'Bradesco S.A', svg: 'assets/media/svg/bancos/bradesco.svg' },
        '104': { name: 'Caixa Econômica Federal', svg: 'assets/media/svg/bancos/caixa.svg' },
        '341': { name: 'Itaú Unibanco S.A', svg: 'assets/media/svg/bancos/itau.svg' },
        '143': { name: 'Lets Bank S.A', svg: 'assets/media/svg/bancos/lets.svg' },
        '403': { name: 'Mercado Pago', svg: 'assets/media/svg/bancos/mercadopago.svg' },
        '260': { name: 'Nu Pagamentos S.A (Nubank)', svg: 'assets/media/svg/bancos/nubank.svg' },
        '136': { name: 'Unicred', svg: 'assets/media/svg/bancos/unicred.svg' },
        '290': { name: 'PagSeguro Internet S.A', svg: 'assets/media/svg/bancos/pagseguro.svg' },
        '748': { name: 'Sicredi', svg: 'assets/media/svg/bancos/sicredi.svg' },
        '197': { name: 'Stone Pagamentos S.A', svg: 'assets/media/svg/bancos/stone.svg' },
        '065': { name: 'Ailos', svg: 'assets/media/svg/bancos/ailos.svg' },
        '756': { name: 'Sicoob', svg: 'assets/media/svg/bancos/sicoob.svg' },
        '000': { name: 'Quality Digital Bank - temporária', svg: 'assets/media/svg/bancos/qualidade.svg' },
        '364': { name: 'Asaas IP S.A', svg: 'assets/media/svg/bancos/assas.svg' },
        '070': { name: 'BRB - Banco de Brasília', svg: 'assets/media/svg/bancos/brasilia.svg' },
        '218': { name: 'Banco BS2 S.A', svg: 'assets/media/svg/bancos/bs2.svg' },
        '208': { name: 'Banco BTG Pactual', svg: 'assets/media/svg/bancos/btg.svg' },
        '336': { name: 'Banco C6 S.A', svg: 'assets/media/svg/bancos/c6.svg' },
        '707': { name: 'Banco Daycoval', svg: 'assets/media/svg/bancos/daycoval.svg' },
        '604': { name: 'Banco Industrial do Brasil S.A', svg: 'assets/media/svg/bancos/industrial.svg' },
        '077': { name: 'Banco Inter S.A', svg: 'assets/media/svg/bancos/inter.svg' },
        '389': { name: 'Banco Mercantil do Brasil S.A', svg: 'assets/media/svg/bancos/mercantil.svg' },
        '212': { name: 'Banco Original S.A', svg: 'assets/media/svg/bancos/original.svg' },
        '643': { name: 'Banco Pine', svg: 'assets/media/svg/bancos/pine.svg' },
        '633': { name: 'Banco Rendimento', svg: 'assets/media/svg/bancos/rendimento.svg' },
        '422': { name: 'Banco Safra S.A', svg: 'assets/media/svg/bancos/safra.svg' },
        '637': { name: 'Banco Sofisa', svg: 'assets/media/svg/bancos/sofisa.svg' },
        '082': { name: 'Banco Topazio', svg: 'assets/media/svg/bancos/topazio.svg' },
        '634': { name: 'Banco Triângulo - Tribanco', svg: 'assets/media/svg/bancos/triangulo.svg' },
        '003': { name: 'Banco da Amazônia S.A', svg: 'assets/media/svg/bancos/amazonia.svg' },
        '021': { name: 'Banco do Estado do Espírito Santo', svg: 'assets/media/svg/bancos/espirito-santo.svg' },
        '037': { name: 'Banco do Estado do Pará', svg: 'assets/media/svg/bancos/para.svg' },
        '047': { name: 'Banco do Estado do Sergipe', svg: 'assets/media/svg/bancos/sergipe.svg' },
        '004': { name: 'Banco do Nordeste do Brasil S.A', svg: 'assets/media/svg/bancos/nordeste.svg' },
        '011': { name: 'Bank of America', svg: 'assets/media/svg/bancos/america.svg' },
        '041': { name: 'Banrisul', svg: 'assets/media/svg/bancos/banrisul.svg' },
        '268': { name: 'Capitual', svg: 'assets/media/svg/bancos/capitual.svg' },
        '331': { name: 'Conta Simples Soluções em Pagamentos', svg: 'assets/media/svg/bancos/conta-simples.svg' },
        '323': { name: 'Cora Sociedade Crédito Direto S.A', svg: 'assets/media/svg/bancos/cora.svg' },
        '097': { name: 'Credisis', svg: 'assets/media/svg/bancos/credisis.svg' },
        '085': { name: 'Cresol', svg: 'assets/media/svg/bancos/cresol.svg' },
        '401': { name: 'Grafeno', svg: 'assets/media/svg/bancos/grafeno.svg' },
        '084': { name: 'Uniprime', svg: 'assets/media/svg/bancos/uniprime.svg' }
    };

    document.getElementById('bancoSelect').addEventListener('change', function() {
        var bancoId = this.value;
        var bancoNomeElement = document.getElementById('bancoNome');
        if (bancoId && bancoList[bancoId]) {
            bancoNomeElement.textContent = 'Nome do Banco: ' + bancoList[bancoId].name;
        } else {
            bancoNomeElement.textContent = '';
        }
    });
</script>

<script>
    $(document).ready(function() {
    $('#lancamento_padrao').select2({
        templateResult: formatOption,
        templateSelection: formatOption,
        escapeMarkup: function(markup) {
            return markup;
        }
    });
});

function formatOption(option) {
    if (!option.id) {
        return option.text;
    }

    var type = $(option.element).data('type');
    var badge = '';

    if (type === 'entrada') {
        badge = '<span class="badge badge-light-success fw-bold fs-8 opacity-75 ps-3 ">Entrada</span>';
    } else if (type === 'saida') {
        badge = '<span class="badge badge-light-danger fw-bold fs-8 opacity-75 ps-3">Saída</span>';
    }

    return badge + ' ' + option.text;
}
</script>
