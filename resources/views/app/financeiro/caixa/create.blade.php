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
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('caixa.index') }}" class="text-muted text-hover-primary">Financeiro</a>
                            </li>
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
                                            <div class="col-md-2 fv-row">
                                                <label class="required fs-5 fw-semibold mb-2">Data</label>
                                                <div class="input-group" id="kt_td_picker_date_only"
                                                    data-td-target-input="nearest" data-td-target-toggle="nearest">
                                                    <input class="form-control" name="data_competencia" type="date"
                                                        placeholder="Pick a date" id="kt_datepicker_1"
                                                        value="{{ old('data_competencia', now()->format('Y-m-d')) }}" />
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
                                            <div class="col-md-4 fv-row">
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
                                                    <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                                                        title="As categorias são utilizadas para formar um Plano de Contas. Muitas destas categorias são demonstradas em Relatórios e também alimentam o DRE Gerencial."></i>
                                                </label>
                                                <select class="form-select" data-control="select" data-dropdown-css-class="w-200px"
                                                        data-placeholder="Selecione o tipo" name="tipo" required data-hide-search="true" id="tipo_select">
                                                        <option value="" disabled selected>Selecione o tipo</option>
                                                    <option value="entrada" {{ old('tipo') == 'entrada' ? 'selected' : '' }}>Entrada</option>
                                                    <option value="saida" {{ old('tipo') == 'saida' ? 'selected' : '' }}>Saída</option>
                                                </select>
                                                @error('tipo')
                                                <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6 fv-row">
                                                <label class="required fs-5 fw-semibold mb-2">Lançamento Padrão</label>
                                                <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                                                    title="As categorias são utilizadas para formar um Plano de Contas. Muitas destas categorias são demonstradas em Relatórios e também alimentam o DRE Gerencial."></i>
                                                <div class="input-group">
                                                    <select name="lancamento_padrao" aria-label="Select a Country" data-control="select2"
                                                            data-placeholder="Escolha um Lançamento..." class="form-select fw-bold" id="lancamento_padrao">
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
                                                        placeholder="" value="{{ $company->name  }}"  />
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
                                                    <option value="CF - Cupom Fiscal"
                                                        {{ old('tipo_documento') == 'CF - Cupom Fiscal' ? 'selected' : '' }}>
                                                        CF - Cupom Fiscal</option>
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
                                            <div class="col-md-4 fv-row">
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
                                            <!-- Novo campo de entrada para o banco de depósito -->
                                            <div class="col-md-4 fv-row" id="banco-deposito" style="display:none;">
                                                <label class=" fs-5 fw-semibold mb-2">Selecione o Banco de Depósito</label>
                                                <select id="bancoSelect" name="banco_id" aria-label="Select a Banco" data-control="select2" data-placeholder="Escolha um banco..." class="form-select fw-bold">
                                                    <option value=""></option>
                                                    @foreach ($bancos as $banco)
                                                    <option data-banco-code="{{ $banco->banco }}" value="{{ $banco->id }}"><span class="banco-name"></span>{{ $banco->banco }} - {{ $banco->name }}/{{ $banco->conta }}</option>
                                                    @endforeach
                                                </select>
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
                                                                allowedExtensions: ["jpg", "jpeg", "png", "pdf", "page"], // Extensões permitidas
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
                                <a href="{{ route('caixa.list') }}" class="btn btn-warning me-2 mb-2">
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

<script>
$(document).ready(function() {
    $('#lancamento_padrao').on('change', function() {
        var selectedValue = $(this).val();
        if (selectedValue === 'Deposito Bancário') {
            $('#banco-deposito').show(); // Mostra o campo do banco de depósito
        } else {
            $('#banco-deposito').hide(); // Esconde o campo do banco de depósito
        }
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tipoSelect = document.getElementById('tipo_select');
    const lancamentoPadraoSelect = document.getElementById('lancamento_padrao');

    tipoSelect.addEventListener('change', function() {
        const selectedTipo = tipoSelect.value;

        // Limpa todas as opções do select de Lançamento Padrão
        lancamentoPadraoSelect.innerHTML = '';

        // Adiciona a opção vazia
        const emptyOption = document.createElement('option');
        emptyOption.value = '';
        emptyOption.text = 'Escolha um Lançamento...';
        lancamentoPadraoSelect.appendChild(emptyOption);

        // Filtra e adiciona as opções de acordo com o tipo selecionado
        @foreach ($lps as $lp)
            if ('{{ $lp->type }}' === selectedTipo) {
                const option = document.createElement('option');
                option.value = '{{ $lp->description }}';
                option.text = '{{ $lp->description }}';
                lancamentoPadraoSelect.appendChild(option);
            }
        @endforeach
    });
});
</script>
