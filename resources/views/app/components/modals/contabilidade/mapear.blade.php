<!--begin::Modal - Mapeamento Contábil-->
<div class="modal fade" id="kt_modal_mapeamento" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <!--begin::Modal content-->
        <div class="modal-content rounded">
            <!--begin::Modal header-->
            <div class="modal-header pb-0 border-0 justify-content-end">
                <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    <span class="svg-icon svg-icon-1">...</span>
                </div>
            </div>
            <!--end::Modal header-->

            <!--begin::Modal body-->
            <div class="modal-body scroll-y px-10 px-lg-15 pt-0 pb-15">
                <!--begin:Form-->
                {{-- Certifique-se de criar a rota 'mapeamento.store' --}}
                <form id="kt_modal_mapeamento_form" class="form" method="POST" action="{{ route('contabilidade.mapeamento.store') }}">
                    @csrf
                    <!--begin::Heading-->
                    <div class="mb-13 text-center">
                        <h1 class="mb-3">Criar Mapeamento Contábil (DE/PARA)</h1>
                        <div class="text-muted fw-semibold fs-5">Traduza um lançamento financeiro para a contabilidade.</div>
                    </div>
                    <!--end::Heading-->

                    <!--begin::Input group - DE (Origem) -->
                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                            <span class="required">DE: Lançamento Padrão</span>
                            <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="Selecione a operação do dia a dia que você quer mapear."></i>
                        </label>
                        <select class="form-select form-select-solid" data-control="select2" data-dropdown-parent="#kt_modal_mapeamento" data-placeholder="Selecione o lançamento de origem..." name="lancamento_padrao_id">
                            <option></option>
                            {{-- 
                                Você precisará passar a variável '$lancamentosPadrao' do seu controller.
                                Ex: $lancamentosPadrao = LancamentoPadrao::forActiveCompany()->get();
                            --}}
                            @isset($lancamentosPadrao)
                                @foreach($lancamentosPadrao as $lp)
                                    <option value="{{ $lp->id }}">{{ $lp->description }}</option>
                                @endforeach
                            @endisset
                        </select>
                    </div>
                    <!--end::Input group-->

                    <div class="separator separator-dashed mb-8"></div>

                    <h3 class="mb-5">PARA: Contas Contábeis</h3>

                    <!--begin::Input group - PARA (Débito) -->
                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="required fs-6 fw-semibold mb-2">Conta Débito</label>
                        <select class="form-select form-select-solid" data-control="select2" data-dropdown-parent="#kt_modal_mapeamento" data-placeholder="Selecione a conta de débito..." name="conta_debito_id">
                            <option></option>
                            {{-- 
                                A variável '$contas' é a mesma usada no modal do Plano de Contas.
                            --}}
                            @isset($contas)
                                @foreach($contas as $conta)
                                    <option value="{{ $conta->id }}">{{ $conta->code }} - {{ $conta->name }}</option>
                                @endforeach
                            @endisset
                        </select>
                        <div class="text-muted fs-7 mt-2">Geralmente uma conta de Ativo (Caixa, Bancos) ou Despesa.</div>
                    </div>
                    <!--end::Input group-->

                    <!--begin::Input group - PARA (Crédito) -->
                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="required fs-6 fw-semibold mb-2">Conta Crédito</label>
                        <select class="form-select form-select-solid" data-control="select2" data-dropdown-parent="#kt_modal_mapeamento" data-placeholder="Selecione a conta de crédito..." name="conta_credito_id">
                            <option></option>
                            @isset($contas)
                                @foreach($contas as $conta)
                                    <option value="{{ $conta->id }}">{{ $conta->code }} - {{ $conta->name }}</option>
                                @endforeach
                            @endisset
                        </select>
                        <div class="text-muted fs-7 mt-2">Geralmente uma conta de Passivo (Fornecedores) ou Receita.</div>
                    </div>
                    <!--end::Input group-->

                    <!--begin::Actions-->
                    <div class="text-center">
                        <button type="reset" data-bs-dismiss="modal" class="btn btn-light me-3">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <span class="indicator-label">Salvar Mapeamento</span>
                        </button>
                    </div>
                    <!--end::Actions-->
                </form>
                <!--end:Form-->
            </div>
            <!--end::Modal body-->
        </div>
        <!--end::Modal content-->
    </div>
    <!--end::Modal dialog-->
</div>
<!--end::Modal - Mapeamento Contábil-->
