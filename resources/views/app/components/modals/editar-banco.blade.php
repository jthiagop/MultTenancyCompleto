<div class="modal fade" id="kt_modal_new_card" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered mw-800px">
        <!--begin::Modal content-->
        <div class="modal-content">
            <!--begin::Modal header-->
            <div class="modal-header">
                <!--begin::Modal title-->
                <h2>Editar Lançamento: #{{ $banco->id }}</h2>
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
                <!--begin::Form-->
                <form method="POST" action="{{ route('banco.update', $banco->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <!-- Campo oculto para entidade_id -->
                    <input type="hidden" name="entidade_id" value="{{ $banco->movimentacao->entidade->id ?? '' }}">

                    <!--end::Input group-->
                    <div class="row g-9 mb-5">
                        <!--begin::Col-->
                        <div class="col-md-7 fv-row">
                            <!--begin::Label-->
                            <label class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">
                                <span class="required">Descrição</span>
                                <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                                    title="Specify a card holder's name"></i>
                            </label>
                            <!--end::Label-->
                            <input type="text" class="form-control form-control-solid" placeholder=""
                                name="descricao" value="{{ old('descricao', $banco->descricao) }}" />
                        </div>
                        <div class="col-md-5 fv-row">
                            <label class="d-flex align-items-center fs-5 fw-semibold mb-2">
                                <span class="required">Entidade</span>
                                <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                                    title="Entidades financeiras representam pontos de controle financeiro, como caixas, bancos, dízimos, coletas ou doações. Elas são utilizadas para organizar e monitorar as entradas e saídas de recursos financeiros, ajudando na gestão eficiente e na geração de relatórios gerenciais.">
                                </i>
                            </label>

                            <!-- Aqui você percorre TODAS as entidades e marca como selecionada aquela que o usuário já tiver -->
                            <select name="entidade_id" id="entidade_id" data-dropdown-css-class="w-200px"
                                class="form-select form-select-solid" data-control="select" required>
                                @foreach ($entidadesBanco as $entidade)
                                    <option value="{{ $entidade->id }}" {{-- Usamos old() para recuperar valor em caso de erro de validação
                                             e $banco->entidade_id para trazer o valor do banco --}}
                                        {{ old('entidade_id', $banco->entidade_id) == $entidade->id ? 'selected' : '' }}>
                                        {{ $entidade->nome }} ({{ ucfirst($entidade->tipo) }})
                                    </option>
                                @endforeach
                            </select>

                            @error('entidade_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                    <!--end::Input group-->
                    <div class="row g-9 mb-5">
                        <!--begin::Col-->
                        <div class="col-md-4 fv-row">
                            <label class="required fs-5 fw-semibold mb-2">Valor</label>
                            <div class="position-relative d-flex align-items-center">
                                <!--begin::Icon-->
                                <!--begin::Svg Icon | path: icons/duotune/general/gen014.svg-->
                                <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                    <svg class="icon icon-tabler icon-tabler-currency-real" fill="none"
                                        height="24" stroke="currentColor" stroke-linecap="round"
                                        stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <!-- O preenchimento inicial não está definido -->
                                        <path d="M0 0h24v24H0z" fill="none" stroke="none"></path>
                                        <!-- Desenha a primeira linha que representa o símbolo da moeda -->
                                        <path d="M21 6h-4a3 3 0 0 0 0 6h1a3 3 0 0 1 0 6h-4"></path>
                                        <!-- Traça a segunda linha da moeda -->
                                        <path d="M4 18v-12h3a3 3 0 1 1 0 6h-3c5.5 0 5 4 6 6"></path>
                                        <!-- Traça duas linhas verticais curtas -->
                                        <path d="M18 6v-2"></path>
                                        <path d="M17 20v-2"></path>
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                                <!--end::Icon-->
                                <input class="form-control form-control-solid ps-12 money" id="valor2" name="valor"
                                    required value="{{ old('valor', number_format($banco->valor, 2, ',', '.')) }}" />
                            </div>
                            @error('valor')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <!--end::Col-->
                        <!--begin::Col-->
                        <div class="col-md-4 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Data</label>
                            <!--begin::Input-->
                            <div class="position-relative d-flex align-items-center">
                                <!--begin::Icon-->
                                <!--begin::Svg Icon | path: icons/duotune/general/gen014.svg-->
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
                                            d="M8.8 13.1C9.2 13.1 9.5 13 9.7 12.8C9.9 12.6 10.1 12.3 10.1 11.9C10.1 11.6 10 11.3 9.8 11.1C9.6 10.9 9.3 10.8 9 10.8C8.8 10.8 8.59999 10.8 8.39999 10.9C8.19999 11 8.1 11.1 8 11.2C7.9 11.3 7.8 11.4 7.7 11.6C7.6 11.8 7.5 11.9 7.5 12.1C7.5 12.2 7.4 12.2 7.3 12.3C7.2 12.4 7.09999 12.4 6.89999 12.4C6.69999 12.4 6.6 12.3 6.5 12.2C6.4 12.1 6.3 11.9 6.3 11.7C6.3 11.5 6.4 11.3 6.5 11.1C6.6 10.9 6.8 10.7 7 10.5C7.2 10.3 7.49999 10.1 7.89999 10C8.29999 9.90003 8.60001 9.80003 9.10001 9.80003C9.50001 9.80003 9.80001 9.90003 10.1 10C10.4 10.1 10.7 10.3 10.9 10.4C11.1 10.5 11.3 10.8 11.4 11.1C11.5 11.4 11.6 11.6 11.6 11.9C11.6 12.3 11.5 12.6 11.3 12.9C11.1 13.2 10.9 13.5 10.6 13.7C10.9 13.9 11.2 14.1 11.4 14.3C11.6 14.5 11.8 14.7 11.9 15C12 15.3 12.1 15.5 12.1 15.8C12.1 16.2 12 16.5 11.9 16.8C11.8 17.1 11.5 17.4 11.3 17.7C11.1 18 10.7 18.2 10.3 18.3C9.9 18.4 9.5 18.5 9 18.5C8.5 18.5 8.1 18.4 7.7 18.2C7.3 18 7 17.8 6.8 17.6C6.6 17.4 6.4 17.1 6.3 16.8C6.2 16.5 6.10001 16.3 6.10001 16.1C6.10001 15.9 6.2 15.7 6.3 15.6C6.4 15.5 6.6 15.4 6.8 15.4C6.9 15.4 7.00001 15.4 7.10001 15.5C7.20001 15.6 7.3 15.6 7.3 15.7C7.5 16.2 7.7 16.6 8 16.9C8.3 17.2 8.6 17.3 9 17.3C9.2 17.3 9.5 17.2 9.7 17.1C9.9 17 10.1 16.8 10.3 16.6C10.5 16.4 10.5 16.1 10.5 15.8C10.5 15.3 10.4 15 10.1 14.7C9.80001 14.4 9.50001 14.3 9.10001 14.3C9.00001 14.3 8.9 14.3 8.7 14.3C8.5 14.3 8.39999 14.3 8.39999 14.3C8.19999 14.3 7.99999 14.2 7.89999 14.1C7.79999 14 7.7 13.8 7.7 13.7C7.7 13.5 7.79999 13.4 7.89999 13.2C7.99999 13 8.2 13 8.5 13H8.8V13.1ZM15.3 17.5V12.2C14.3 13 13.6 13.3 13.3 13.3C13.1 13.3 13 13.2 12.9 13.1C12.8 13 12.7 12.8 12.7 12.6C12.7 12.4 12.8 12.3 12.9 12.2C13 12.1 13.2 12 13.6 11.8C14.1 11.6 14.5 11.3 14.7 11.1C14.9 10.9 15.2 10.6 15.5 10.3C15.8 10 15.9 9.80003 15.9 9.70003C15.9 9.60003 16.1 9.60004 16.3 9.60004C16.5 9.60004 16.7 9.70003 16.8 9.80003C16.9 9.90003 17 10.2 17 10.5V17.2C17 18 16.7 18.4 16.2 18.4C16 18.4 15.8 18.3 15.6 18.2C15.4 18.1 15.3 17.8 15.3 17.5Z"
                                            fill="currentColor" />
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                                <!--end::Icon-->
                                <!--begin::Datepicker-->
                                <input class="form-control form-control-solid ps-12" name="data_competencia" required
                                    type="text" placeholder="Informe a data" id="kt_datepicker_1"
                                    value="{{ old('data_competencia', \Carbon\Carbon::parse($banco->data_competencia)->format('d/m/Y')) }}" />

                                <!--end::Datepicker-->
                            </div>
                            <!--end::Input-->
                        </div>
                        <!--end::Col-->
                        <!-- Entrada/Saída -->
                        <div class="col-md-4 fv-row">
                            <label class="d-flex align-items-center fs-5 fw-semibold mb-2">
                                <span class="required">Entrada/Saída</span>
                                <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                                    title="As categorias são utilizadas para formar um Plano de Contas. Muitas destas categorias são demonstradas em Relatórios e também alimentam o DRE Gerencial."></i>
                            </label>
                            <select class="form-select form-select-solid" data-control="select2"
                                data-dropdown-css-class="w-200px" data-placeholder="Selecione o tipo" name="tipo"
                                required data-hide-search="true" id="tipo_select_banco">
                                <option value="" disabled selected>Defina o tipo</option>
                                <option value="entrada"
                                    {{ old('tipo', $banco->tipo) == 'entrada' ? 'selected' : '' }}>
                                    Entrada</option>
                                <option value="saida" {{ old('tipo', $banco->tipo) == 'saida' ? 'selected' : '' }}>
                                    Saída</option>
                            </select>
                        </div>

                        <!-- Lançamento Padrão -->
                        <div class="d-flex flex-column mb-7 fv-row">
                            <label class="required fs-6 fw-semibold form-label mb-2">Lançamento Padrão</label>
                            <select name="lancamento_padrao_id" aria-label="Escolha um Lançamento"
                                data-control="select2" data-placeholder="Escolha um Lançamento..."
                                class="form-select form-select-solid fw-bold" id="lancamento_padrao_banco">
                                <option value="">Escolha um Lançamento...</option>
                                @foreach ($lps as $lp)
                                    <option value="{{ $lp->id }}" data-type="{{ $lp->type }}"
                                        data-description="{{ $lp->description }}"
                                        data-tipo-label="{{ $lp->type === 'entrada' ? 'Receita' : 'Despesa' }}"
                                        data-tipo-color="{{ $lp->type === 'entrada' ? 'success' : 'danger' }}"
                                        {{ $banco->lancamento_padrao_id == $lp->id ? 'selected' : '' }}>
                                        {{ $lp->id }} - {{ $lp->description }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="row mb-10">
                            <div class="col-md-6 fv-row">
                                <label class="d-flex align-items-center fs-5 fw-semibold mb-2">
                                    <span class="required">Tipo de Documento</span>
                                    <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                                        title="As categorias são utilizadas para formar um Plano de Contas. Muitas destas categorias são demonstradas em Relatórios e também alimentam o DRE Gerencial."></i>
                                </label>
                                <select class="form-control form-control-solid" data-control="select"
                                    name="tipo_documento" id="tipo_documento">
                                    <option value="Pix"
                                        {{ old('tipo_documento', $banco->tipo_documento) == 'Pix' ? 'selected' : '' }}>
                                        Pix</option>
                                    <option value="OUTR - Dafe"
                                        {{ old('tipo_documento', $banco->tipo_documento) == 'OUTR - Dafe' ? 'selected' : '' }}>
                                        OUTR - Dafe</option>
                                    <option value="NF - Nota Fiscal"
                                        {{ old('tipo_documento', $banco->tipo_documento) == 'NF - Nota Fiscal' ? 'selected' : '' }}>
                                        NF - Nota Fiscal</option>
                                    <option value="CF - Cupom Fiscal"
                                        {{ old('tipo_documento', $banco->tipo_documento) == 'CF - Cupom Fiscal' ? 'selected' : '' }}>
                                        CF - Cupom Fiscal</option>
                                    <option value="DANF - Danfe"
                                        {{ old('tipo_documento', $banco->tipo_documento) == 'DANF - Danfe' ? 'selected' : '' }}>
                                        DANF - Danfe</option>
                                    <option value="BOL - Boleto"
                                        {{ old('tipo_documento', $banco->tipo_documento) == 'BOL - Boleto' ? 'selected' : '' }}>
                                        BOL - Boleto</option>
                                    <option value="REP - Repasse"
                                        {{ old('tipo_documento', $banco->tipo_documento) == 'REP - Repasse' ? 'selected' : '' }}>
                                        REP - Repasse</option>
                                    <option value="CCRD - Cartão de Credito"
                                        {{ old('tipo_documento', $banco->tipo_documento) == 'CCRD - Cartão de Credito' ? 'selected' : '' }}>
                                        CCRD - Cartão de Credito</option>
                                    <option value="CTRB - Cartão de Débito"
                                        {{ old('tipo_documento', $banco->tipo_documento) == 'CTRB - Cartão de Débito' ? 'selected' : '' }}>
                                        CTRB - Cartão de Débito</option>
                                    <option value="REC - Recibo"
                                        {{ old('tipo_documento', $banco->tipo_documento) == 'REC - Recibo' ? 'selected' : '' }}>
                                        REC - Recibo</option>
                                    <option value="CARN - Carnê"
                                        {{ old('tipo_documento', $banco->tipo_documento) == 'CARN - Carnê' ? 'selected' : '' }}>
                                        CARN - Carnê</option>
                                    <option value="FAT - Fatura"
                                        {{ old('tipo_documento', $banco->tipo_documento) == 'FAT - Fatura' ? 'selected' : '' }}>
                                        FAT - Fatura</option>
                                    <option value="APOL - Apólice"
                                        {{ old('tipo_documento', $banco->tipo_documento) == 'APOL - Apólice' ? 'selected' : '' }}>
                                        APOL - Apólice</option>
                                    <option value="DUPL - Duplicata"
                                        {{ old('tipo_documento', $banco->tipo_documento) == 'DUPL - Duplicata' ? 'selected' : '' }}>
                                        DUPL - Duplicata</option>
                                    <option value="TRIB - Tribunal"
                                        {{ old('tipo_documento', $banco->tipo_documento) == 'TRIB - Tribunal' ? 'selected' : '' }}>
                                        TRIB - Tribunal</option>
                                    <option value="Outros"
                                        {{ old('tipo_documento', $banco->tipo_documento) == 'Outros' ? 'selected' : '' }}>
                                        Outros</option>
                                    <option value="T Banc - Transferência Bancaria"
                                        {{ old('tipo_documento', $banco->tipo_documento) == 'T Banc - Transferência Bancaria' ? 'selected' : '' }}>
                                        T Banc - Transferência Bancaria</option>
                                </select>
                                @error('tipo_documento')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 fv-row">
                                <label class="fs-5 fw-semibold mb-2">Número do Documento</label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-solid ps-12"
                                        placeholder="" name="numero_documento"
                                        value="{{ old('numero_documento', $banco->numero_documento) }}" />
                                </div>
                                @error('numero_documento')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>

                        <div class="row mb-10">
                            <div class="col-md-12 fv-row">
                                <label class="required fs-5 fw-semibold mb-2">Centro de Custo</label>
                                <div class="input-group">
                                    <select name="cost_center_id" id="cost_center_id" class="form-select form-select-solid @error('cost_center_id') is-invalid @enderror"
                                    data-control="select" data-dropdown-css-class="auto" data-placeholder="Selecione o Centro de Custo">
                                    <!-- Placeholder -->
                                    <option value="" disabled>Selecione o Centro de Custo</option>
                                    @foreach ($centrosAtivos as $centro)
                                        <option value="{{ $centro->id }}"
                                            {{ old('cost_center_id', $centroCusto->cost_center_id ?? '') == $centro->id ? 'selected' : '' }}>
                                            {{ $centro->name }}
                                        </option>
                                    @endforeach
                                </select>

                                </div>
                                @error('cost_center_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-0">
                            <!--begin::Title-->
                            <h5 class="mb-4">Historico Homplementar:</h5>
                            <!--end::Title-->
                            <!--begin::Product table-->
                            <div class="table-responsive">
                                <tr>
                                    <td class="text-gray-800">
                                        <textarea class="form-control" name="historico_complementar" id="complemento" cols="20" rows="3">{{ old('historico_complementar', $banco->historico_complementar) }}</textarea>
                                        <p class="text-gray-400">Descreva observações relevantes sobre
                                            esse lançamento financeiro</p>
                                        @error('historico_complementar')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </td>
                                </tr>
                            </div>
                            <!--end::Product table-->
                        </div>
                        <!--end::Section-->

                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="d-flex flex-stack">
                            <!--begin::Label-->
                            <div class="me-5">
                                <label class="fs-6 fw-semibold form-label">Possui Nota Fiscal?</label>
                                <div class="fs-7 fw-semibold text-muted">Marque esta opção se o lançamento tiver uma
                                    nota
                                    fiscal como comprovante.</div>
                            </div>
                            <!--end::Label-->
                            <!--begin::Switch-->
                            <label class="form-check form-switch form-check-custom form-check-solid">
                                <!-- Campo hidden para garantir que 0 seja enviado caso o checkbox esteja desmarcado -->
                                <input type="hidden" name="comprovacao_fiscal" value="0">
                                <!-- Checkbox para enviar 1 quando marcado -->
                                <input class="form-check-input" type="checkbox" name="comprovacao_fiscal"
                                    value="1"
                                    {{ old('comprovacao_fiscal', $banco->comprovacao_fiscal) ? 'checked' : '' }} />
                                <span class="form-check-label fw-semibold text-muted">Possui Nota</span>
                            </label>
                            <!--end::Switch-->
                        </div>
                        <!--end::Input group-->
                        <!--begin::Actions-->
                        <div class="text-center pt-15">
                            <!-- Botão de Cancelar -->
                            <button type="reset" id="kt_modal_new_card_cancel" class="btn btn-light me-3">
                                <i class="fas fa-times-circle me-2"></i> Cancelar
                            </button>

                            <!-- Botão de Enviar -->
                            <button type="submit" id="kt_modal_new_card_submit" class="btn btn-primary">
                                <i class="fas fa-sync-alt me-2"></i>
                                <span class="indicator-label">Atualizar</span>
                                <span class="indicator-progress">
                                    Enviando...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>
                        </div>
                        <!--end::Actions-->
                </form>
                <!--end::Form-->
            </div>
            <!--end::Modal body-->
        </div>
        <!--end::Modal content-->
    </div>
    <!--end::Modal dialog-->
</div>
<!--end::Modal - New Card-->
