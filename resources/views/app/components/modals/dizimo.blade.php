<!--begin::Modal - Dízimo/Doação-->
<div class="modal fade" id="kt_modal_dizimo" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <!--begin::Modal content-->
        <div class="modal-content">
            <!--begin::Form-->
            <form class="form" action="{{ route('dizimos.store') }}" id="kt_modal_dizimo_form" method="POST">
                @csrf
                <input type="hidden" name="_method" id="kt_modal_dizimo_method" value="POST">
                <input type="hidden" name="dizimo_id" id="kt_modal_dizimo_id">

                <!--begin::Modal header-->
                <div class="modal-header" id="kt_modal_dizimo_header">
                    <!--begin::Modal title-->
                    <h2 id="kt_modal_dizimo_title">Novo Lançamento de Dízimo/Doação</h2>
                    <!--end::Modal title-->
                    <!--begin::Close-->
                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                        <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
                        <span class="svg-icon svg-icon-1">
                            <i class="bi bi-x-lg"></i>
                        </span>
                        <!--end::Svg Icon-->
                    </div>
                    <!--end::Close-->
                </div>
                <!--end::Modal header-->

                <!--begin::Modal body-->
                <div class="modal-body py-10 px-lg-17">
                    <!--begin::Scroll-->
                    <div class="scroll-y me-n7 pe-7" id="kt_modal_dizimo_scroll" data-kt-scroll="true"
                        data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto"
                        data-kt-scroll-dependencies="#kt_modal_dizimo_header"
                        data-kt-scroll-wrappers="#kt_modal_dizimo_scroll" data-kt-scroll-offset="300px">

                        <!--begin::Input group - Fiel-->
                        <div class="d-flex flex-column mb-5 fv-row">
                            <!--begin::Label-->
                            <label class="required fs-5 fw-semibold mb-2">Fiel</label>
                            <!--end::Label-->
                            <!--begin::Select-->
                            <select name="fiel_id" id="kt_modal_dizimo_fiel" data-control="select2"
                                data-dropdown-parent="#kt_modal_dizimo" data-placeholder="Selecione um fiel..."
                                class="form-select">
                                <option value="">Selecione um fiel...</option>
                                @foreach ($fieis as $fiel)
                                    <option value="{{ $fiel->id }}">{{ $fiel->id }} - {{ $fiel->nome_completo }}
                                    </option>
                                @endforeach
                            </select>
                            <!--end::Select-->
                            <div class="fv-plugins-message-container">
                                <div class="fv-help-block">
                                    <span role="alert" class="invalid-feedback" id="fielid-error"></span>
                                </div>
                            </div>
                        </div>
                        <!--end::Input group-->

                        <!--begin::Input group - Tipo-->
                        <div class="d-flex flex-column mb-5 fv-row">
                            <!--begin::Label-->
                            <label class="required fs-5 fw-semibold mb-2">Tipo</label>
                            <!--end::Label-->
                            <!--begin::Select-->
                            <select name="tipo" id="kt_modal_dizimo_tipo" data-control="select2"
                                data-dropdown-parent="#kt_modal_dizimo" class="form-select">
                                <option value="Dízimo">Dízimo</option>
                                <option value="Doação">Doação</option>
                                <option value="Oferta">Oferta</option>
                                <option value="Outro">Outro</option>
                            </select>
                            <!--end::Select-->
                            <div class="fv-plugins-message-container">
                                <div class="fv-help-block">
                                    <span role="alert" class="invalid-feedback" id="tipo-error"></span>
                                </div>
                            </div>
                        </div>
                        <!--end::Input group-->

                        <!--begin::Input group - Valor e Data-->
                        <div class="row g-9 mb-5">
                            <!--begin::Col - Valor-->
                            <div class="col-md-6 fv-row">
                                <!--begin::Label-->
                                <label class="required fs-5 fw-semibold mb-2">Valor</label>
                                <!--end::Label-->
                                <div class="input-group mb-5">
                                    <span class="input-group-text" id="basic-addon1">R$</span>
                                    <input type="text" name="valor" id="kt_modal_dizimo_valor"
                                        class="form-control"  aria-label="Valor"
                                        aria-describedby="valor-addon" >
                                </div>
                                <!--end::Input group-->
                                <!--end::Input group-->
                                <div class="fv-plugins-message-container">
                                    <div class="fv-help-block">
                                        <span role="alert" class="invalid-feedback" id="valor-error"></span>
                                    </div>
                                </div>
                                <!--begin::Input group-->
                            </div>
                            <!--end::Col-->
                            <!--begin::Col - Data-->
                            <div class="col-md-6 fv-row">
                                <!--begin::Label-->
                                <label class="required fs-5 fw-semibold mb-2">Data de Pagamento</label>
                                <!--end::Label-->
                                <!--begin::Input group-->
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-calendar-event"></i>
                                    </span>
                                    <input type="text" name="data_pagamento" id="kt_modal_dizimo_data"
                                        class="form-control" placeholder="Selecione a data"
                                        aria-label="Data de Pagamento" aria-describedby="data-addon" >
                                </div>
                                <!--end::Input group-->
                                <div class="fv-plugins-message-container">
                                    <div class="fv-help-block">
                                        <span role="alert" class="invalid-feedback"
                                            id="data_pagamento-error"></span>
                                    </div>
                                </div>
                            </div>
                            <!--end::Col-->
                        </div>
                        <!--end::Input group-->

                        <!--begin::Input group - Forma de Pagamento-->
                        <div class="d-flex flex-column mb-5 fv-row">
                            <!--begin::Label-->
                            <label class="required fs-5 fw-semibold mb-2">Forma de Pagamento</label>
                            <!--end::Label-->
                            <!--begin::Select-->
                            <select name="forma_pagamento" id="kt_modal_dizimo_forma" data-control="select2"
                                data-dropdown-parent="#kt_modal_dizimo" class="form-select">
                                <option value="Dinheiro">Dinheiro</option>
                                <option value="PIX">PIX</option>
                                <option value="Cartão de Débito">Cartão de Débito</option>
                                <option value="Cartão de Crédito">Cartão de Crédito</option>
                                <option value="Transferência">Transferência</option>
                                <option value="Cheque">Cheque</option>
                                <option value="Outro">Outro</option>
                            </select>
                            <!--end::Select-->
                            <div class="fv-plugins-message-container">
                                <div class="fv-help-block">
                                    <span role="alert" class="invalid-feedback" id="forma_pagamento-error"></span>
                                </div>
                            </div>
                        </div>
                        <!--end::Input group-->

                        <!--begin::Input group - Entidade Financeira-->
                        <div class="d-flex flex-column mb-5 fv-row">
                            <!--begin::Label-->
                            <label class="d-flex align-items-center fs-5 fw-semibold mb-2">
                                <span>Forma de Pagamento</span>
                                <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                                    title="Selecione a forma de pagamento"></i>
                            </label>
                            <!--end::Label-->
                            <!--begin::Select-->
                            <select name="entidade_financeira_id" id="kt_modal_dizimo_entidade"
                                data-control="select2" data-dropdown-parent="#kt_modal_dizimo"
                                data-placeholder="Selecione uma entidade..." class="form-select">
                                <option value="">Selecione uma entidade...</option>
                                @php
                                    $currentTipo = null;
                                @endphp
                                @foreach ($entidades as $entidade)
                                    @if ($currentTipo !== $entidade->tipo)
                                        @if ($currentTipo !== null)
                                            </optgroup>
                                        @endif
                                        <optgroup label="{{ ucfirst($entidade->tipo) }}s">
                                        @php
                                            $currentTipo = $entidade->tipo;
                                        @endphp
                                    @endif
                                    <option value="{{ $entidade->id }}">
                                        {{ $entidade->nome }}
                                        @if($entidade->tipo === 'banco' && $entidade->banco)
                                            - {{ $entidade->banco->nome ?? '' }}
                                        @endif
                                    </option>
                                @endforeach
                                @if ($currentTipo !== null)
                                    </optgroup>
                                @endif
                            </select>
                            <!--end::Select-->
                            <div class="fv-plugins-message-container">
                                <div class="fv-help-block">
                                    <span role="alert" class="invalid-feedback"
                                        id="entidadefinanceiraid-error"></span>
                                </div>
                            </div>
                        </div>
                        <!--end::Input group-->

                        <!--begin::Input group - Integrar com Financeiro-->
                        <div class="fv-row mb-5">
                            <!--begin::Wrapper-->
                            <div class="d-flex flex-stack">
                                <!--begin::Label-->
                                <div class="me-5">
                                    <!--begin::Label-->
                                    <label class="fs-5 fw-semibold">Integrar com Financeiro?</label>
                                    <!--end::Label-->
                                    <!--begin::Description-->
                                    <div class="fs-7 fw-semibold text-muted">
                                        Cria automaticamente um lançamento no módulo financeiro
                                    </div>
                                    <!--end::Description-->
                                </div>
                                <!--end::Label-->
                                <!--begin::Switch-->
                                <label class="form-check form-switch form-check-custom form-check-solid">
                                    <!--begin::Input-->
                                    <input class="form-check-input" name="integrar_financeiro"
                                        id="kt_modal_dizimo_integrar" type="checkbox" value="1" />
                                    <!--end::Input-->
                                    <!--begin::Label-->
                                    <span class="form-check-label fw-semibold text-muted">Sim</span>
                                    <!--end::Label-->
                                </label>
                                <!--end::Switch-->
                            </div>
                            <!--end::Wrapper-->
                        </div>
                        <!--end::Input group-->

                        <!--begin::Input group - Observações-->
                        <div class="d-flex flex-column mb-5 fv-row">
                            <!--begin::Label-->
                            <label class="fs-5 fw-semibold mb-2">Observações</label>
                            <!--end::Label-->
                            <!--begin::Textarea-->
                            <textarea name="observacoes" id="kt_modal_dizimo_observacoes" class="form-control" rows="3"
                                placeholder="Observações sobre o lançamento..."></textarea>
                            <!--end::Textarea-->
                            <div class="fv-plugins-message-container">
                                <div class="fv-help-block">
                                    <span role="alert" class="invalid-feedback" id="observacoes-error"></span>
                                </div>
                            </div>
                        </div>
                        <!--end::Input group-->

                    </div>
                    <!--end::Scroll-->
                </div>
                <!--end::Modal body-->

                <!--begin::Modal footer-->
                <div class="modal-footer flex-center">
                    <!--begin::Button-->
                    <button type="reset" id="kt_modal_dizimo_cancel" class="btn btn-light me-3"
                        data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <!--end::Button-->
                    <!--begin::Button-->
                    <button type="submit" id="kt_modal_dizimo_submit" class="btn btn-primary">
                        <span class="indicator-label">Salvar</span>
                        <span class="indicator-progress">Aguarde...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                    <!--end::Button-->
                </div>
                <!--end::Modal footer-->
            </form>
            <!--end::Form-->
        </div>
        <!--end::Modal content-->
    </div>
    <!--end::Modal dialog-->
</div>
<!--end::Modal - Dízimo/Doação-->
