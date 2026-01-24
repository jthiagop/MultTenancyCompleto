         <div class="row gx-5 gx-xl-10">
            <!--begin::Nome do Banco e do Dominus-->
            <div class="col-md-5">
                <!--begin::Payment address-->
                <!--begin::Info-->
                <div class="d-flex flex-stack pb-10">
                    <!--begin::Info-->
                    <div class="d-flex">
                        <!--begin::Avatar-->
                        <div class="symbol symbol-45px">
                            @if ($entidade->bank && $entidade->bank->logo_path)
                                {{-- Usa o caminho do logo salvo no banco de dados --}}
                                <img src="{{ $entidade->bank->logo_path }}" alt="{{ $entidade->bank->name }}" />
                            @else
                                {{-- Fallback: Mostra as iniciais do nome da entidade se não houver logo --}}
                                <span
                                    class="symbol-label bg-light-primary text-primary fs-6 fw-bold">{{ strtoupper(substr($entidade->nome, 0, 1)) }}</span>
                            @endif
                        </div>
                        <!--end::Avatar-->
                        <!--begin::Details-->
                        <div class="ms-5">
                            <!--begin::Desc-->
                            <span class="text-muted fw-semibold mb-3">Lançamentos Importantes</span>
                            <!--end::Desc-->
                            <!--begin::Name-->
                            <div class="d-flex align-items-center">
                                <a class="text-dark fw-bold text-hover-primary fs-5 me-4">{{ $entidade->nome }}</a>
                            </div>
                            <!--end::Name-->
                        </div>
                        <!--end::Details-->
                    </div>
                    <!--end::Info-->
                </div>
                <!--end::Info-->
                <!--end::Payment address-->
            </div>
            <!--end::Payment address-->

            <!--begin::Conciliar (Botão Central)-->
            <div class="col-md-1 d-flex align-items-center justify-content-center">
            </div>
            <!--end::Conciliar-->

            <!--begin::Shipping address-->
            <div class="col-md-6">
                <!--begin::Info-->
                <div class="d-flex flex-stack pb-10">
                    <!--begin::Info-->
                    <div class="d-flex">
                        <!--begin::Avatar-->
                        <div class="symbol symbol-circle symbol-45px">
                            <img src="{{ url('assets/media/app/mini-logo.svg') }}" alt="" />
                        </div>
                        <!--end::Avatar-->
                        <!--begin::Details-->
                        <div class="ms-5">
                            <!--begin::Desc-->
                            <span class="text-muted fw-semibold mb-3">
                                Lançamentos a cadastrar
                            </span>
                            <!--end::Desc-->
                            <!--begin::Name-->
                            <div class="d-flex align-items-center">
                                <a class="text-dark fw-bold text-hover-primary fs-5 me-4">Dominus Sistema</a>
                            </div>
                            <!--end::Name-->
                        </div>
                        <!--end::Details-->
                    </div>
                    <!--end::Info-->
                </div>
                <!--end::Info-->
            </div>
            <!--end::Nome do Banco e do Dominus-->
        </div>