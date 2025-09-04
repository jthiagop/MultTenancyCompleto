<!--begin::Financial Modules-->
<div class="card mb-5 mb-xl-10">
    <div class="card-body py-10">
        <div class="card-title mb-10">
            <h2>Módulos de Movimentação Financeira</h2>
        </div>
        <div class="row">
            <!--begin::Caixa Card-->
            <div class="col-12 col-md-6 hover-elevate-up">
                <a href="{{ route('caixa.list', ['tab' => 'lancamento']) }}"
                    class="text-decoration-none" aria-label="Acessar lançamentos de caixa">
                    <div
                        class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6">
                        <span class="svg-icon svg-icon-5x me-5">
                            <img width="50" height="50"
                                src="/assets/media/png/Cash_Register-transformed.webp"
                                alt="Ícone de lançamento de caixa" />
                        </span>
                        <div class="d-flex flex-stack flex-grow-1 flex-wrap flex-md-nowrap">
                            <div class="mb-3 mb-md-0 fw-semibold">
                                <h4 class="text-gray-900 fw-bold">Lançamento de Caixa</h4>
                                <div class="text-muted fw-semibold fs-6">Gerencie entradas e saídas
                                    em dinheiro com controle total.</div>
                                <span class="badge badge-success mt-2">{{ $caixaPendentes ?? 0 }}
                                    lançamentos pendentes</span>
                            </div>
                            <span class="btn btn-primary px-6 align-self-center" role="button">
                                <svg width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M5 12H19M19 12L13 6M19 12L13 18" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                                Acessar
                            </span>
                        </div>
                    </div>
                </a>
            </div>
            <!--end::Caixa Card-->
            <!--begin::Banco Card-->
            <div class="col-12 col-md-6 hover-elevate-up">
                <a href="{{ route('banco.list', ['tab' => 'lancamento']) }}"
                    class="text-decoration-none" aria-label="Acessar lançamentos bancários">
                    <div
                        class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6">
                        <span class="svg-icon svg-icon-5x me-5">
                            <img width="50" height="50" src="/assets/media/png/banco3.png"
                                alt="Ícone de lançamentos bancários" />
                        </span>
                        <div class="d-flex flex-stack flex-grow-1 flex-wrap flex-md-nowrap">
                            <div class="mb-3 mb-md-0 fw-semibold">
                                <h4 class="text-gray-900 fw-bold">Lançamentos Bancários</h4>
                                <div class="text-muted fw-semibold fs-6">Controle transações de
                                    contas bancárias com relatórios detalhados.</div>
                                <span class="badge badge-success mt-2">{{ $bancoPendentes ?? 0 }}
                                    lançamentos pendentes</span>
                            </div>
                            <span class="btn btn-primary px-6 align-self-center" role="button">
                                <svg width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M5 12H19M19 12L13 6M19 12L13 18" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                                Acessar
                            </span>
                        </div>
                    </div>
                </a>
            </div>
            <!--end::Banco Card-->
        </div>
    </div>
</div>
<!--end::Financial Modules-->
