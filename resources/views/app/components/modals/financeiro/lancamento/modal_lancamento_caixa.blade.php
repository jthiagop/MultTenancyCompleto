<!--begin::Modal - New Target Caixa-->
<div class="modal fade" id="Dm_modal_caixa" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-fullscreen">
        <!--begin::Modal content-->
        <div class="modal-content rounded">
            <!--begin::Modal header-->
            <div class="modal-header btn btn-sm  ">
                <h3 class="modal-title" id="modal_caixa_title">Novo Lançamento</h3>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!--end::Modal header-->

            <!--begin::Modal body-->
            <div class="modal-body scroll-y px-10 px-lg-15 pb-15  bg-light pt-5">
                <!-- Begin::Form -->
                <form id="Dm_modal_caixa_form" class="form" action="{{ route('caixa.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <meta name="csrf-token" content="{{ csrf_token() }}">

                    <!-- Campo oculto para identificar o tipo (receita ou despesa) -->
                    <input type="hidden" name="tipo_financeiro" id="tipo_financeiro_caixa" value="">
                    <input type="hidden" name="status_pagamento" id="status_pagamento_caixa" value="em aberto">
                    <input type="hidden" name="origem" id="origem_caixa" value="Caixa">
                    <!-- Campo hidden para garantir que o tipo seja sempre enviado -->
                    <input type="hidden" name="tipo_hidden" id="tipo_hidden_caixa" value="">

                    <script>
                        // Configura o modal dinamicamente baseado no tipo (receita/despesa)
                        $(document).ready(function() {
                            var tipoLancamento = null; // Variável para armazenar o tipo do lançamento

                            $('#Dm_modal_caixa').on('show.bs.modal', function(event) {
                                var button = $(event.relatedTarget);
                                tipoLancamento = button.data('tipo'); // Receita ou Despesa
                                var modal = $(this);
                                var form = modal.find('#Dm_modal_caixa_form');
                                var modalTitle = modal.find('#modal_caixa_title');
                                var tipoFinanceiroInput = modal.find('#tipo_financeiro_caixa');
                                var entidadeSelect = modal.find('#entidade_id_caixa');

                                // Atualiza o título do modal baseado no tipo
                                if (tipoLancamento === 'receita') {
                                    modalTitle.text('Nova Receita');
                                    tipoFinanceiroInput.val('receita');
                                } else if (tipoLancamento === 'despesa') {
                                    modalTitle.text('Nova Despesa');
                                    tipoFinanceiroInput.val('despesa');
                                } else {
                                    modalTitle.text('Novo Lançamento');
                                    tipoFinanceiroInput.val('');
                                }

                                // Limpa a seleção
                                entidadeSelect.val(null);

                                // Reinicializa o Select2 se necessário
                                setTimeout(function() {
                                    if (entidadeSelect.hasClass('select2-hidden-accessible')) {
                                        entidadeSelect.select2('destroy');
                                    }
                                    // Reinicializa o Select2
                                    if (typeof KTSelect2 !== 'undefined') {
                                        new KTSelect2(entidadeSelect[0]);
                                    }
                                }, 100);
                            });

                            // Aguarda o modal ser completamente exibido para manipular o select de tipo e lançamento padrão
                            $('#Dm_modal_caixa').on('shown.bs.modal', function() {
                                var modal = $(this);
                                var tipoSelect = modal.find('#tipo_select_caixa');
                                var lancamentoPadraoSelect = modal.find('#lancamento_padraos_id_caixa');
                                var tipoHidden = modal.find('#tipo_hidden_caixa');

                                // Aguarda um pequeno delay para garantir que o select2 foi inicializado
                                setTimeout(function() {
                                    if (tipoLancamento === 'receita') {
                                        // Seleciona "entrada" no select
                                        tipoSelect.val('entrada');
                                        // Atualiza o campo hidden
                                        if (tipoHidden.length > 0) {
                                            tipoHidden.val('entrada');
                                        }
                                        // Desabilita o select
                                        tipoSelect.prop('disabled', true);

                                        // Atualiza o Select2
                                        if (tipoSelect.hasClass('select2-hidden-accessible')) {
                                            tipoSelect.select2('destroy');
                                        }
                                        // Reinicializa o Select2 desabilitado
                                        if (typeof KTSelect2 !== 'undefined') {
                                            new KTSelect2(tipoSelect[0]);
                                        } else if (typeof tipoSelect.select2 !== 'undefined') {
                                            tipoSelect.select2();
                                        }
                                        tipoSelect.trigger('change');

                                        // Filtra os lançamentos padrão para entrada
                                        filtrarLancamentosPadrao('entrada', lancamentoPadraoSelect);
                                    } else if (tipoLancamento === 'despesa') {
                                        // Seleciona "saida" no select
                                        tipoSelect.val('saida');
                                        // Atualiza o campo hidden
                                        if (tipoHidden.length > 0) {
                                            tipoHidden.val('saida');
                                        }
                                        // Desabilita o select
                                        tipoSelect.prop('disabled', true);

                                        // Atualiza o Select2
                                        if (tipoSelect.hasClass('select2-hidden-accessible')) {
                                            tipoSelect.select2('destroy');
                                        }
                                        // Reinicializa o Select2 desabilitado
                                        if (typeof KTSelect2 !== 'undefined') {
                                            new KTSelect2(tipoSelect[0]);
                                        } else if (typeof tipoSelect.select2 !== 'undefined') {
                                            tipoSelect.select2();
                                        }
                                        tipoSelect.trigger('change');

                                        // Filtra os lançamentos padrão para saída
                                        filtrarLancamentosPadrao('saida', lancamentoPadraoSelect);
                                    } else {
                                        // Se não houver tipo definido, mantém habilitado
                                        tipoSelect.prop('disabled', false);
                                        if (tipoSelect.hasClass('select2-hidden-accessible')) {
                                            tipoSelect.select2('destroy');
                                        }
                                        // Reinicializa o Select2 habilitado
                                        if (typeof KTSelect2 !== 'undefined') {
                                            new KTSelect2(tipoSelect[0]);
                                        } else if (typeof tipoSelect.select2 !== 'undefined') {
                                            tipoSelect.select2();
                                        }
                                        // Limpa o campo hidden
                                        if (tipoHidden.length > 0) {
                                            tipoHidden.val('');
                                        }
                                    }
                                }, 150);
                            });

                            // Função para filtrar lançamentos padrão baseado no tipo
                            function filtrarLancamentosPadrao(tipo, $select) {
                                // Filtra usando as opções existentes no DOM
                                $select.find('option').each(function() {
                                    var $option = $(this);
                                    var optionType = $option.data('type');

                                    // Se for a opção vazia, mantém visível
                                    if ($option.val() === '' || !optionType) {
                                        $option.prop('disabled', false).show();
                                    } else if (optionType === tipo) {
                                        // Mostra opções do tipo correto
                                        $option.prop('disabled', false).show();
                                    } else {
                                        // Esconde opções de outro tipo
                                        $option.prop('disabled', true).hide();
                                    }
                                });

                                // Limpa a seleção atual
                                $select.val(null);

                                // Atualiza o Select2
                                setTimeout(function() {
                                    if ($select.hasClass('select2-hidden-accessible')) {
                                        $select.select2('destroy');
                                    }
                                    // Reinicializa o Select2
                                    if (typeof KTSelect2 !== 'undefined') {
                                        new KTSelect2($select[0]);
                                    } else if (typeof $select.select2 !== 'undefined') {
                                        $select.select2();
                                    }
                                }, 50);
                            }
                        });
                    </script>

                    <div class="card mb-xl-10 ">
                        <div class="card-body px-10">
                            <!--begin::Form-->
                            <!--begin::Input group - Assign & Due Date-->
                            <div class="row g-9 mb-8">
                                <div class="col-md-2 fv-row">
                                    <label class="required fs-6 fw-semibold mb-2">
                                        Data de competência
                                    </label>
                                    <div class="position-relative d-flex align-items-center">
                                        <!--begin::Icon-->
                                        <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path opacity="0.3"
                                                    d="M21 22H3C2.4 22 2 21.6 2 21V5C2 4.4 2.4 4 3 4H21C21.6 4 22 4.4 22 5V21C22 21.6 21.6 22 21 22Z"
                                                    fill="currentColor" />
                                                <path
                                                    d="M6 6C5.4 6 5 5.6 5 5V3C5 2.4 5.4 2 6 2C6.6 2 7 2.4 7 3V5C7 5.6 6.6 6 6 6ZM11 5V3C11 2.4 10.6 2 10 2C9.4 2 9 2.4 9 3V5C9 5.6 9.4 6 10 6C10.6 6 11 5.6 11 5ZM15 5V3C15 2.4 14.6 2 14 2C13.4 2 13 2.4 13 3V5C13 5.6 13.4 6 14 6C14.6 6 15 5.6 15 5ZM19 5V3C19 2.4 18.6 2 18 2C17.4 2 17 2.4 17 3V5C17 5.6 17.4 6 18 6C18.6 6 19 5.6 19 5Z"
                                                    fill="currentColor" />
                                                <path
                                                    d="M8.8 13.1C9.2 13.1 9.5 13 9.7 12.8C9.9 12.6 10.1 12.3 10.1 11.9C10.1 11.6 10 11.3 9.8 11.1C9.6 10.9 9.3 10.8 9 10.8C8.8 10.8 8.6 10.8 8.4 10.9C8.2 11 8.1 11.1 8 11.2C7.9 11.3 7.8 11.4 7.7 11.6C7.6 11.8 7.5 11.9 7.5 12.1C7.5 12.2 7.4 12.2 7.3 12.3C7.2 12.4 7.1 12.4 6.9 12.4C6.7 12.4 6.6 12.3 6.5 12.2C6.4 12.1 6.3 11.9 6.3 11.7C6.3 11.5 6.4 11.3 6.5 11.1C6.6 10.9 6.8 10.7 7 10.5C7.2 10.3 7.5 10.1 7.9 10C8.3 9.9 8.6 9.8 9.1 9.8C9.5 9.8 9.8 9.9 10.1 10C10.4 10.1 10.7 10.3 10.9 10.4C11.1 10.5 11.3 10.8 11.4 11.1C11.5 11.4 11.6 11.6 11.6 11.9C11.6 12.3 11.5 12.6 11.3 12.9C11.1 13.2 10.9 13.5 10.6 13.7C10.9 13.9 11.2 14.1 11.4 14.3C11.6 14.5 11.8 14.7 11.9 15C12 15.3 12.1 15.5 12.1 15.8C12.1 16.2 12 16.5 11.9 16.8C11.8 17.1 11.5 17.4 11.3 17.7C11.1 18 10.7 18.2 10.3 18.3C9.9 18.4 9.5 18.5 9 18.5C8.5 18.5 8.1 18.4 7.7 18.2C7.3 18 7 17.8 6.8 17.6C6.6 17.4 6.4 17.1 6.3 16.8C6.2 16.5 6.1 16.3 6.1 16.1C6.1 15.9 6.2 15.7 6.3 15.6C6.4 15.5 6.6 15.4 6.8 15.4C6.9 15.4 7 15.4 7.1 15.5C7.2 15.6 7.3 15.6 7.3 15.7C7.5 16.2 7.7 16.6 8 16.9C8.3 17.2 8.6 17.3 9 17.3C9.2 17.3 9.5 17.2 9.7 17.1C9.9 17 10.1 16.8 10.3 16.6C10.5 16.4 10.5 16.1 10.5 15.8C10.5 15.3 10.4 15 10.1 14.7C9.8 14.4 9.5 14.3 9.1 14.3C9 14.3 8.9 14.3 8.7 14.3C8.5 14.3 8.4 14.3 8.4 14.3C8.2 14.3 8 14.2 7.9 14.1C7.8 14 7.7 13.8 7.7 13.7C7.7 13.5 7.8 13.4 7.9 13.2C8 13 8.2 13 8.5 13H8.8V13.1ZM15.3 17.5V12.2C14.3 13 13.6 13.3 13.3 13.3C13.1 13.3 13 13.2 12.9 13.1C12.8 13 12.7 12.8 12.7 12.6C12.7 12.4 12.8 12.3 12.9 12.2C13 12.1 13.2 12 13.6 11.8C14.1 11.6 14.5 11.3 14.7 11.1C14.9 10.9 15.2 10.6 15.5 10.3C15.8 10 15.9 9.8 15.9 9.7C15.9 9.6 16.1 9.6 16.3 9.6C16.5 9.6 16.7 9.7 16.8 9.8C16.9 9.9 17 10.2 17 10.5V17.2C17 18 16.7 18.4 16.2 18.4C16 18.4 15.8 18.3 15.6 18.2C15.4 18.1 15.3 17.8 15.3 17.5Z"
                                                    fill="currentColor" />
                                            </svg>
                                        </span>
                                        <!--end::Icon-->
                                        <input class="form-control ps-12" placeholder="Informe a data"
                                            name="data_competencia" id="data_caixa" />
                                    </div>
                                </div>
                                <!--end::Col-->
                                <!--begin::Col-->
                                <div class="col-md-3 fv-row">
                                    <label class="required d-flex align-items-center fs-5 fw-semibold mb-2"
                                        id="label_entidade_caixa">Entidade</label>
                                    <div class="input-group">
                                        <select class="form-select" data-control="select2"
                                            data-dropdown-parent="#Dm_modal_caixa" data-placeholder="Selecione a Entidade"
                                            name="entidade_id" data-hide-search="true" id="entidade_id_caixa">
                                            <option value="" disabled selected>Selecione</option>
                                            @if (isset($entidades))
                                                @foreach ($entidades as $entidade)
                                                    @if($entidade->tipo === 'caixa')
                                                        <option value="{{ $entidade->id }}">
                                                            {{ $entidade->nome }} ({{ ucfirst($entidade->tipo) }})
                                                        </option>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <!-- Exibindo a mensagem de erro -->
                                    @error('entidade_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <!--end::Col-->

                                <!--begin::Input group - Target Title-->
                                <div class="col-md-5 fv-row">
                                    <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                        <span class="required">Descrição</span>
                                    </label>
                                    <input type="text" class="form-control" placeholder="Informe a descrição"
                                        name="descricao" id="descricao_caixa" />
                                </div>

                                <!--end::Input group - Target Title-->
                                <!--begin::Input group - Valor-->
                                <div class="col-md-2 fv-row">
                                    <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                        <span class="required">Valor</span>
                                        <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                                            title="Informe o valor da transação"></i>
                                    </label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text" id="basic-addon1-caixa">R$</span>
                                        <input type="text" class="form-control" name="valor" id="valor_caixa"
                                            placeholder="0,00" aria-label="Valor" aria-describedby="basic-addon1-caixa">
                                    </div>

                                </div>
                                <!--end::Input group - Valor-->
                            </div>
                            <!--begin::Input group - Assign & Due Date-->
                            <div class="row g-9 mb-8">
                                <div class="col-md-2 fv-row">
                                    <label class="d-flex align-items-center fs-5 fw-semibold mb-2">
                                        <span class="required">Tipo</span>
                                        <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                                            title="As categorias são utilizadas para formar um Plano de Contas. Muitas destas categorias são demonstradas em Relatórios e também alimentam o DRE Gerencial."></i>
                                    </label>
                                    <select class="form-select" data-control="select2"
                                        data-placeholder="Selecione o tipo" data-hide-search="true" name="tipo"
                                        id="tipo_select_caixa">
                                        <option value="" disabled selected>Defina o tipo</option>
                                        <option value="entrada" {{ old('tipo') == 'entrada' ? 'selected' : '' }}>
                                            Entrada</option>
                                        <option value="saida" {{ old('tipo') == 'saida' ? 'selected' : '' }}>
                                            Saída</option>
                                    </select>
                                    @error('tipo')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 fv-row">
                                    <label class="required fs-6 fw-semibold mb-2">Lançamento Padrão</label>
                                    <div class="input-group">
                                        <select class="form-select" data-control="select2"
                                            data-dropdown-parent="#Dm_modal_caixa" name="lancamento_padrao_id"
                                            id="lancamento_padraos_id_caixa" data-placeholder="Escolha um Lançamento..."
                                            data-allow-clear="true" data-minimum-results-for-search="0">
                                            <option value=""></option> <!-- Opção vazia para o placeholder -->
                                            @foreach ($lps as $lp)
                                                <option value="{{ $lp->id }}"
                                                    data-description="{{ $lp->description }}"
                                                    data-type="{{ $lp->type }}"> {{ $lp->id }} -
                                                    {{ $lp->description }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('lancamento_padrao_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 fv-row">
                                    <label class="fs-5 fw-semibold mb-2">Centro de Custo</label>
                                    <div class="input-group">
                                        <select name="cost_center_id" id="cost_center_id_caixa"
                                            class="form-select @error('cost_center_id') is-invalid @enderror"
                                            data-control="select2" data-dropdown-parent="#Dm_modal_caixa"
                                            data-dropdown-css-class="auto"
                                            data-placeholder="Selecione o Centro de Custo" data-allow-clear="true"
                                            data-minimum-results-for-search="0">
                                            <!-- Placeholder configurado aqui -->
                                            @foreach ($centrosAtivos as $centrosAtivos)
                                                <option value="{{ $centrosAtivos->id }}">{{ $centrosAtivos->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('centro')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>
                            <!--end::Input group - Assign & Due Date-->
                            <!--begin::Input group-->
                            <div class="row g-9 mb-5">
                                <!--begin::Col-->
                                <div class="col-md-4 fv-row">
                                    <label class="required fs-6 fw-semibold mb-2">Tipo de Documento</label><i
                                        class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                                        title="As categorias são utilizadas para formar um Plano de Contas. Muitas destas categorias são demonstradas em Relatórios e também alimentam o DRE Gerencial."></i>
                                    <select class="form-select" data-control="select2"
                                        data-dropdown-parent="#Dm_modal_caixa" data-hide-search="true"
                                        data-placeholder="Selecione o tipo de documento" name="tipo_documento"
                                        id="tipo_documento_caixa">
                                        <option value="Pix" {{ old('tipo_documento') == 'Pix' ? 'selected' : '' }}>
                                            Pix
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
                                </div>
                                <!--end::Col-->
                                <div class="col-md-4 fv-row ">
                                    <label class="fs-5 fw-semibold mb-2">Número do Documento</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="1234567890"
                                            name="numero_documento" value="{{ old('numero_documento') }}" />
                                    </div>
                                    @error('numero_documento')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <!-- Novo campo de entrada para o banco de depósito -->
                                <div class="col-md-4 fv-row" id="banco-deposito-caixa" style="display:none;">
                                    <label class="fs-5 fw-semibold mb-2">Banco de Depósito</label>
                                    <select id="bancoSelect_caixa" name="entidade_banco_id" aria-label="Select a Banco"
                                        data-control="select2" data-placeholder="Escolha um banco..."
                                        class="form-select form-select-solid">
                                        <option value=""></option>
                                        @if (isset($entidadesBanco))
                                            @foreach ($entidadesBanco as $entidade)
                                                <option value="{{ $entidade->id }}">{{ $entidade->nome }}
                                                    ({{ ucfirst($entidade->tipo) }})
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <!--end::Input group-->
                        </div>
                    </div>

                    <!--begin::Card-->
                    <div class="card mb-xl-10 ">
                        <div class="card-body px-10">

                            <!--begin::Input group-->
                            <div class="d-flex flex-stack w-lg-50 g-9 mb-5">
                                <!--begin::Label-->
                                <div class="me-5">
                                    <label class="fs-6 fw-semibold form-label">Existe comprovação fiscal?</label>
                                    <div class="fs-7 fw-semibold text-muted">Documentos que comprovam transações
                                        financeiras</div>
                                </div>
                                <!--end::Label-->
                                <!-- Input Hidden para garantir o envio de "0" quando desmarcado -->
                                <input type="hidden" name="comprovacao_fiscal" value="0">
                                <!--begin::Switch-->
                                <label class="form-check form-switch form-check-custom form-check-solid">
                                    <!-- Checkbox para enviar 1 quando marcado -->
                                    <input class="form-check-input" type="checkbox" name="comprovacao_fiscal"
                                        value="1" id="comprovacao_fiscal_checkbox_caixa" />
                                    <span class="form-check-label fw-semibold text-muted">Possui Nota</span>
                                </label>
                                <!--end::Switch-->
                                <script>
                                    // Controla a exibição da tab de Anexos baseado no checkbox
                                    $(document).ready(function() {
                                        $('#comprovacao_fiscal_checkbox_caixa').on('change', function() {
                                            var isChecked = $(this).is(':checked');
                                            var tabAnexosItem = $('#tab_anexos_item_caixa');

                                            if (isChecked) {
                                                // Mostra a tab de Anexos
                                                tabAnexosItem.show();
                                            } else {
                                                // Esconde a tab de Anexos
                                                tabAnexosItem.hide();

                                                // Se a tab de Anexos estiver ativa, volta para a tab de Histórico
                                                var tabAnexosLink = tabAnexosItem.find('a');
                                                if (tabAnexosLink.hasClass('active')) {
                                                    tabAnexosLink.removeClass('active');
                                                    $('#kt_tab_pane_2_caixa').removeClass('show active');
                                                    $('#kt_tab_pane_1_caixa').addClass('show active');
                                                    $('a[href="#kt_tab_pane_1_caixa"]').addClass('active');
                                                }
                                            }
                                        });
                                    });
                                </script>
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="d-flex flex-column mb-8">
                                <div class="d-flex flex-column mb-5 fv-row">
                                    <ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-bs-toggle="tab"
                                                href="#kt_tab_pane_1_caixa">Histórico complementar</a>
                                        </li>
                                        <li class="nav-item" id="tab_anexos_item_caixa" style="display: none;">
                                            <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_pane_2_caixa">Anexos</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content" id="myTabContent_caixa">
                                        <div class="tab-pane fade show active" id="kt_tab_pane_1_caixa" role="tabpanel">
                                            <textarea class="form-control" name="historico_complementar" id="complemento_caixa" maxlength="250" rows="3"
                                                placeholder="Mais detalhes sobre o lançamento"></textarea>
                                            <span class="fs-6 text-muted">Insira no máximo 250
                                                caracteres</span>
                                        </div>
                                        <div class="tab-pane fade" id="kt_tab_pane_2_caixa" role="tabpanel">
                                            <x-anexos-input name="anexos" :anexosExistentes="[]" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end::Input group-->

                        </div>
                    </div>
                    <!--end::Card-->
                    <!-- Script para exibir/esconder campos de recorrência -->
                </form>
                <!--end::Form-->
            </div>
            <!--end::Modal body-->

            <!--begin::Modal footer-->
            <div class="modal-footer btn btn-sm">
                <div class="text-center">
                    <button type="reset" id="Dm_modal_caixa_cancel"
                        class="btn btn-sm btn-light me-3">Cancelar</button>
                    <!-- Split dropup button -->
                    <div class="btn-group dropup">
                        <!-- Botão principal -->
                        <button type="submit" id="Dm_modal_caixa_submit" class="btn btn-sm btn-primary">
                            <span class="indicator-label">Enviar</span>
                            <span class="indicator-progress">Aguarde...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>

                        </button>
                        <!-- Botão de dropup -->
                        <button type="button" class="btn btn-sm btn-primary dropdown-toggle dropdown-toggle-split"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <!-- Opções do dropup -->
                        <div class="dropdown-menu">
                            <a class="dropdown-item btn-sm" href="#" id="Dm_modal_caixa_clone">Salvar e
                                Clonar</a>
                            <a class="dropdown-item btn-sm" href="#" id="Dm_modal_caixa_novo">Salvar e Limpar</a>
                        </div>
                    </div>
                </div>
                <!--end::Actions-->
            </div>
            <!--end::Modal footer-->
        </div>
        <!--end::Modal content-->
    </div>
    <!--end::Modal dialog-->

</div>
<!--end::Modal - New Target Caixa-->

