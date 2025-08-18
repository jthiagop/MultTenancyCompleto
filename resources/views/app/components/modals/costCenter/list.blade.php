<div class="modal fade" id="kt_modal_select_centro_custo" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog mw-700px">
        <!--begin::Modal content-->
        <div class="modal-content">
            <!--begin::Modal header-->
            <div class="modal-header pb-0 border-0 d-flex justify-content-end">
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
            <div class="modal-body scroll-y mx-5 mx-xl-10 pt-0 pb-15">
                <!--begin::Heading-->
                <div class="text-center mb-13">
                    <!--begin::Title-->
                    <h1 class="d-flex justify-content-center align-items-center mb-3">
                        Centro de Custo Cadastrados
                        <span class="badge badge-circle badge-secondary ms-3">
                            {{ $centroCustos->count() }}
                        </span>
                    </h1>
                    <!--end::Title-->

                    <!--begin::Subtitle (ou nova descrição)-->
                    <div class="text-muted fw-semibold fs-6 mb-3">
                        Gerencie todos os Centros de Custo da sua empresa em um só lugar.
                        <br>
                    </div>
                    <!--end::Subtitle-->
                </div>

                <!--end::Heading-->
                <!--begin::Users-->
                <div class="mh-475px scroll-y me-n7 pe-7">
                    @foreach ($centroCustos as $centroCusto)
                        <!--begin::User-->
                        <div class="border border-hover-primary p-7 rounded mb-7">
                            <!--begin::Info-->
                            <div class="d-flex flex-stack pb-3">
                                <!--begin::Info-->
                                <div class="d-flex">
                                    <!--begin::Details-->
                                    <div class="ms-5">
                                        <!--begin::Name-->
                                        <div class="d-flex align-items-center">
                                            <a href="../../demo1/dist/pages/user-profile/overview.html"
                                                class="text-dark fw-bold text-hover-primary fs-5 me-4">{{ $centroCusto->name }}</a>
                                            <!--begin::Label-->
                                            @if ($centroCusto->status == 1)
                                                {{-- Badge verde com estrela preenchida e texto "Ativo" --}}
                                                <span
                                                    class="badge badge-light-success d-flex align-items-center fs-8 fw-semibold">
                                                    <span class="svg-icon svg-icon-8 svg-icon-success me-1">
                                                        <svg width="24" height="24" viewBox="0 0 24 24"
                                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M11.1359 4.48359C11.5216 3.82132 12.4784 3.82132 12.8641 4.48359L15.011 8.16962C15.1523 8.41222 15.3891 8.58425 15.6635 8.64367L19.8326 9.54646C20.5816 9.70867 20.8773 10.6186 20.3666 11.1901L17.5244 14.371C17.3374 14.5803 17.2469 14.8587 17.2752 15.138L17.7049 19.382C17.7821 20.1445 17.0081 20.7069 16.3067 20.3978L12.4032 18.6777C12.1463 18.5645 11.8537 18.5645 11.5968 18.6777L7.69326 20.3978C6.99192 20.7069 6.21789 20.1445 6.2951 19.382L6.7248 15.138C6.75308 14.8587 6.66264 14.5803 6.47558 14.371L3.63339 11.1901C3.12273 10.6186 3.41838 9.70867 4.16744 9.54646L8.3365 8.64367C8.61089 8.58425 8.84767 8.41222 8.98897 8.16962L11.1359 4.48359Z"
                                                                fill="currentColor" />
                                                        </svg>
                                                    </span>
                                                    Ativo
                                                </span>
                                            @else
                                                {{-- Badge vermelho com estrela vazada e texto "Desativado" --}}
                                                <span
                                                    class="badge badge-light-danger d-flex align-items-center fs-8 fw-semibold">
                                                    <span class="svg-icon svg-icon-8 svg-icon-danger me-1">
                                                        <svg width="24" height="24" viewBox="0 0 24 24"
                                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M11.1359 4.48359C11.5216 3.82132 12.4784 3.82132 12.8641 4.48359L15.011 8.16962C15.1523 8.41222 15.3891 8.58425 15.6635 8.64367L19.8326 9.54646C20.5816 9.70867 20.8773 10.6186 20.3666 11.1901L17.5244 14.371C17.3374 14.5803 17.2469 14.8587 17.2752 15.138L17.7049 19.382C17.7821 20.1445 17.0081 20.7069 16.3067 20.3978L12.4032 18.6777C12.1463 18.5645 11.8537 18.5645 11.5968 18.6777L7.69326 20.3978C6.99192 20.7069 6.21789 20.1445 6.2951 19.382L6.7248 15.138C6.75308 14.8587 6.66264 14.5803 6.47558 14.371L3.63339 11.1901C3.12273 10.6186 3.41838 9.70867 4.16744 9.54646L8.3365 8.64367C8.61089 8.58425 8.84767 8.41222 8.98897 8.16962L11.1359 4.48359Z"
                                                                stroke="currentColor" stroke-width="1.5"
                                                                fill="none" />
                                                        </svg>
                                                    </span>
                                                    Desativado
                                                </span>
                                            @endif

                                            <!--end::Label-->
                                        </div>
                                        <!--end::Name-->
                                        <!--begin::Desc-->
                                        <span class="text-muted fw-semibold mb-3">Criado:
                                            {{ $centroCusto->created_by_name }}</span>
                                        <!--end::Desc-->
                                    </div>
                                    <!--end::Details-->
                                </div>
                                <!--end::Info-->
                                <!--begin::Stats-->
                                <div clas="d-flex">
                                    <!--begin::Price-->
                                    <div class="text-end pb-3">
                                        <span class="text-muted fs-9">Orçamento</span><br>
                                        <span class="text-dark fw-bold fs-5">R${{ number_format($centroCusto->budget, 2, ',', '.') }}</span>
                                    </div>
                                    <!--end::Price-->
                                </div>
                                <!--end::Stats-->
                            </div>
                            <!--end::Info-->
                            <!--begin::Wrapper-->
                            <div class="p-0">
                                <!--begin::Section-->
                                <div class="d-flex flex-column">
                                    <!--begin::Text-->
                                    <p class="text-gray-700 fw-semibold fs-6 mb-4">
                                        {{ $centroCusto->observations }}</p>
                                    <!--end::Text-->
                                    @php
                                        // Decodifica o JSON em um array associativo
                                        $categories = json_decode($centroCusto->category, true) ?? [];
                                    @endphp

                                    <!--begin::Tags-->
                                    <div class="d-flex text-gray-700 fw-semibold fs-7">
                                        @foreach ($categories as $cat)
                                            <span class="border border-2 rounded me-3 p-1 px-2">
                                                {{ $cat['value'] }}
                                            </span>
                                        @endforeach
                                    </div>
                                    <!--end::Tags-->

                                </div>
                                <!--end::Section-->
                                <!--begin::Footer-->
                                <div class="d-flex flex-column">
                                    <!--begin::Separator-->
                                    <div class="separator separator-dashed border-muted my-5"></div>
                                    <!--end::Separator-->
                                    <!--begin::Action-->
                                    <div class="d-flex flex-stack align-items-center">
                                        <!--begin::Progress-->
                                        <div class="d-flex flex-column mw-200px me-5">
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="text-gray-700 fs-6 fw-semibold me-2">{{ $progresso = $centroCusto->progresso;}}%</span>
                                                <span class="text-muted fs-8">Concluído</span>
                                            </div>
                                            <div class="progress h-6px w-200px">
                                                <div class="progress-bar bg-success" role="progressbar"
                                                    style="width: {{ $progresso = $centroCusto->progresso;}}%;" aria-valuenow="4" aria-valuemin="10"
                                                    aria-valuemax="10"></div>
                                            </div>
                                        </div>
                                        <!--end::Progress-->

                                        <!--begin::Buttons-->
                                        <div class="d-flex gap-3">
                                            <a href="{{ route('costCenter.show', $centroCusto->id) }}" class="btn btn-sm btn-primary">Exibir</a>
                                            <a href="{{ route('costCenter.edit', $centroCusto->id) }}" class="btn btn-sm btn-secondary">Editar</a>
                                        </div>
                                        <!--end::Buttons-->
                                    </div>
                                    <!--end::Action-->

                                </div>
                                <!--end::Footer-->
                            </div>
                            <!--end::Wrapper-->
                        </div>
                        <!--end::User-->
                    @endforeach

                </div>
                <!--end::Users-->
            </div>
            <!--end::Modal Body-->
        </div>
        <!--end::Modal content-->
    </div>
    <!--end::Modal dialog-->
</div>
