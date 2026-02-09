<!--begin::Financial Modules-->
    <div class="card-body ">

        <div class="row">
            <!--begin::Caixa Card-->
            <div class="col-12 col-md-6 hover-elevate-up">
                <a href="{{ route('caixa.list', ['tab' => 'lancamento']) }}"
                    class="text-decoration-none" aria-label="Acessar lançamentos de caixa">
                    <div
                        class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6">
                        <span class="svg-icon svg-icon-5x me-5">
                            <img width="50" height="50"
                                src="/tenancy/assets/media/png/Cash_Register-transformed.webp"
                                alt="Ícone de lançamento de caixa" />
                        </span>
                        <div class="d-flex flex-stack flex-grow-1 flex-wrap flex-md-nowrap">
                            <div class="mb-3 mb-md-0 fw-semibold">
                                <h4 class="text-gray-900 fw-bold">Lançamento de Caixa</h4>
                                <div class="text-muted fw-semibold fs-6">Gerencie entradas e saídas
                                    em dinheiro com controle total.</div>

                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <!--end::Caixa Card-->
            <!--begin::Banco Card-->
            <div class="col-12 col-md-6 hover-elevate-up">
                <a href="{{ route('banco.list', ['tab' => 'registros']) }}"
                    class="text-decoration-none" aria-label="Acessar lançamentos bancários">
                    <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6">
                        <span class="svg-icon svg-icon-5x me-5">
                            <img width="50" height="50" src="/tenancy/assets/media/png/banco3.png"
                                alt="Ícone de lançamentos bancários" />

                        </span>
                        <div class="d-flex flex-stack flex-grow-1 flex-wrap flex-md-nowrap">
                            <div class="mb-3 mb-md-0 fw-semibold">
                                <h4 class="text-gray-900 fw-bold">Lançamentos Bancários</h4>
                                <div class="text-muted fw-semibold fs-6">Controle transações de
                                    contas bancárias com relatórios detalhados.</div>

                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <!--end::Banco Card-->
        </div>
    </div>
    
<!--end::Financial Modules-->

