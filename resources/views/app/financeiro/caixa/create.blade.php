
<link href="/assets/fileinput/css/fileinput.css" media="all" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" crossorigin="anonymous">
<link href="/assets/fileinput/themes/explorer-fa5/theme.css" media="all" rel="stylesheet" type="text/css"/>
<script src="https://code.jquery.com/jquery-3.6.0.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="/assets/fileinput/js/plugins/buffer.min.js" type="text/javascript"></script>
<script src="/assets/fileinput/js/plugins/filetype.min.js" type="text/javascript"></script>
<script src="/assets/fileinput/js/plugins/piexif.js" type="text/javascript"></script>
<script src="/assets/fileinput/js/plugins/sortable.js" type="text/javascript"></script>
<script src="/assets/fileinput/js/fileinput.js" type="text/javascript"></script>
<script src="/assets/fileinput/js/locales/fr.js" type="text/javascript"></script>
<script src="/assets/fileinput/js/locales/es.js" type="text/javascript"></script>
<script src="/assets/fileinput/themes/fa5/theme.js" type="text/javascript"></script>
<script src="/assets/fileinput/themes/explorer-fa5/theme.js" type="text/javascript"></script>

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
                    <form method="POST" action="{{ route('caixa.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
                            <div class="card card-flush py-4">
                                <div class="card-header">
                                    <div class="card-title">
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
                                            <div class="col-md-3 fv-row">
                                                <label class="required fs-5 fw-semibold mb-2">Data de
                                                    Competência</label>
                                                <div class="input-group" id="kt_td_picker_date_only"
                                                    data-td-target-input="nearest" data-td-target-toggle="nearest">
                                                    <input class="form-control" name="data_competencia" type="date"
                                                        placeholder="Pick a date" id="kt_datepicker_1"
                                                        value="{{ old('data_competencia') }}" />
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
                                                        name="descricao" value="{{ old('descricao') }}" />
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
                                                        {{ old('tipo') == 'entrada' ? 'selected' : '' }}>Entrada
                                                    </option>
                                                    <option value="saida"
                                                        {{ old('tipo') == 'saida' ? 'selected' : '' }}>Saída</option>
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
                                                        value="{{ old('lancamento_padrao') }}" />
                                                </div>
                                                @error('lancamento_padrao')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 fv-row">
                                                <label class="fs-5 fw-semibold mb-2">Centro de Custo</label>
                                                <div class="input-group">
                                                    <input type="text" name="centro" class="form-control"
                                                        placeholder="" value="{{ old('centro') }}" />
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
                                                    <div class="file-loading">
                                                        <input id="file-4" multiple name="anexos[]" type="file" class="file" data-upload-url="#">
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
                                    <span class="indicator-label">Salvar</span>
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

    <script>
        $('#file-fr').fileinput({
            theme: 'fa5',
            language: 'fr',
            uploadUrl: '#',
            allowedFileExtensions: ['jpg', 'png', 'jpeg', 'pdf']
        });
        $('#file-es').fileinput({
            theme: 'fa5',
            language: 'es',
            uploadUrl: '#',
            allowedFileExtensions: ['jpg', 'png', 'gif', 'pdf']
        });
        $("#file-0").fileinput({
            theme: 'fa5',
            uploadUrl: '#'
        }).on('filepreupload', function(event, data, previewId, index) {
            alert('The description entered is:\n\n' + ($('#description').val() || ' NULL'));
        });
        $("#file-1").fileinput({
            theme: 'fa5',
            uploadUrl: '#', // you must set a valid URL here else you will get an error
            allowedFileExtensions: ['jpg', 'png', 'gif', 'pdf'],
            overwriteInitial: false,
            maxFileSize: 1000,
            maxFilesNum: 10,
            //allowedFileTypes: ['image', 'video', 'flash'],
            slugCallback: function (filename) {
                return filename.replace('(', '_').replace(']', '_');

            }
        });
        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            $("#file-input").fileinput({
                theme: "fas",
                uploadUrl: "/upload",
                uploadAsync: true,
                showUpload: true,
                showRemove: true,
                maxFileCount: 5,
                browseOnZoneClick: true,
                allowedFileExtensions: ['jpg', 'png', 'gif', 'pdf'],
                elErrorContainer: '#kartik-file-errors',
                uploadExtraData: function() {
                    return {
                        _token: csrfToken
                    };
                }
            }).on('filebatchselected', function(event, files) {
                // Files selected, now you can upload them
                $("#file-input").fileinput("upload");
            }).on('fileuploaded', function(event, previewId, index, fileId) {
                console.log('File uploaded successfully.');
            }).on('filedeleted', function(event, key, jqXHR, data) {
                console.log('File deleted successfully.');
            });
        });
        /*
         $(".file").on('fileselect', function(event, n, l) {
         alert('File Selected. Name: ' + l + ', Num: ' + n);
         });
         */
        $("#file-3").fileinput({
            theme: 'fa5',
            showUpload: false,
            showCaption: false,
            browseClass: "btn btn-primary btn-lg",
            fileType: "any",
            previewFileIcon: "<i class='glyphicon glyphicon-king'></i>",
            overwriteInitial: false,
            initialPreviewAsData: true,
            initialPreview: [
                "http://lorempixel.com/1920/1080/transport/1",
                "http://lorempixel.com/1920/1080/transport/2",
                "http://lorempixel.com/1920/1080/transport/3"
            ],
            initialPreviewConfig: [
                {caption: "transport-1.jpg", size: 329892, width: "120px", url: "{$url}", key: 1},
                {caption: "transport-2.jpg", size: 872378, width: "120px", url: "{$url}", key: 2},
                {caption: "transport-3.jpg", size: 632762, width: "120px", url: "{$url}", key: 3}
            ]
        });
        $("#file-4").fileinput({
            theme: 'fa5',
            uploadExtraData: {kvId: '10'}
        });
        $(".btn-warning").on('click', function () {
            var $el = $("#file-4");
            if ($el.attr('disabled')) {
                $el.fileinput('enable');
            } else {
                $el.fileinput('disable');
            }
        });
        $(".btn-info").on('click', function () {
            $("#file-4").fileinput('refresh', {previewClass: 'bg-info'});
        });
        /*
         $('#file-4').on('fileselectnone', function() {
         alert('Huh! You selected no files.');
         });
         $('#file-4').on('filebrowse', function() {
         alert('File browse clicked for #file-4');
         });
         */
        $(document).ready(function () {
            $("#test-upload").fileinput({
                'theme': 'fa5',
                'showPreview': false,
                'allowedFileExtensions': ['jpg', 'png', 'gif', 'pdf'],
                'elErrorContainer': '#errorBlock'
            });
            $("#kv-explorer").fileinput({
                'theme': 'explorer-fa5',
                'uploadUrl': '#',
                overwriteInitial: false,
                initialPreviewAsData: true,
                initialPreview: [
                    "http://lorempixel.com/1920/1080/nature/1",
                    "http://lorempixel.com/1920/1080/nature/2",
                    "http://lorempixel.com/1920/1080/nature/3"
                ],
                initialPreviewConfig: [
                    {caption: "nature-1.jpg", size: 329892, width: "120px", url: "{$url}", key: 1},
                    {caption: "nature-2.jpg", size: 872378, width: "120px", url: "{$url}", key: 2},
                    {caption: "nature-3.jpg", size: 632762, width: "120px", url: "{$url}", key: 3}
                ]
            });
            /*
             $("#test-upload").on('fileloaded', function(event, file, previewId, index) {
             alert('i = ' + index + ', id = ' + previewId + ', file = ' + file.name);
             });
             */
            $('#inp-add-1').on('change', function() {
                var $plugin = $('#inp-add-2').data('fileinput');
                $plugin.addToStack($(this)[0].files[0])
            });
            $('#inp-add-2').fileinput({
                uploadUrl: '#',
                //uploadUrl: 'http://localhost/plugins/test-upload',
                initialPreviewAsData: true,
                initialPreview: [
                    "https://dummyimage.com/640x360/a0f.png&text=Transport+1",
                    "https://dummyimage.com/640x360/3a8.png&text=Transport+2",
                    "https://dummyimage.com/640x360/6ff.png&text=Transport+3"
                ],
                initialPreviewConfig: [
                    {caption: "transport-1.jpg", size: 329892, width: "120px", url: "{$url}", key: 1, zoomData: 'https://dummyimage.com/1920x1080/a0f.png&text=Transport+1', description: '<h5>NUMBER 1</h5> The first choice for transport. This is the future.'},
                    {caption: "transport-2.jpg", size: 872378, width: "120px", url: "{$url}", key: 2, zoomData: 'https://dummyimage.com/1920x1080/3a8.png&text=Transport+2', description: '<h5>NUMBER 2</h5> The second choice for transport. This is the future.'},
                    {caption: "transport-3.jpg", size: 632762, width: "120px", url: "{$url}", key: 3, zoomData: 'https://dummyimage.com/1920x1080/6ff.png&text=Transport+3', description: '<h5>NUMBER 3</h5> The third choice for transport. This is the future.'}
                ]
            });
        });
    </script>
</x-tenant-app-layout>
