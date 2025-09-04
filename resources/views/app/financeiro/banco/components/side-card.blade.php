<div class="col-12 col-sm-12 col-md-4"> <!--begin::Row-->
    <div class="card mb-6 mb-xl-9">
        <!--begin::Col-->
        <div class="col-xl-12 mb-xl-6">
            <!--begin::Slider Widget 2-->
            <div id="kt_sliders_widget_2_slider"
                class="card card-flush carousel carousel-custom carousel-stretch slide h-xl-100"
                data-bs-ride="carousel" data-bs-interval="6000">
                <!--begin::Header-->
                <div class="card-header pt-5">
                    <!--begin::Title-->
                    <h4 class="card-title d-flex align-items-start flex-column">
                        <span class="card-label fw-bold text-gray-800">Lista de Bancos</span>
                        <span class="text-gray-400 mt-1 fw-bold fs-7">
                            Exibindo {{ count($entidadesBanco) }}
                            @if (count($entidadesBanco) == 1)
                                banco
                            @else
                                bancos
                            @endif
                        </span>
                    </h4>
                    <!--end::Title-->
                    <!--begin::Toolbar-->
                    <div class="card-toolbar">
                        <!--begin::Carousel Indicators-->
                        <ol
                            class="p-0 m-0 carousel-indicators carousel-indicators-bullet carousel-indicators-active-success">
                            @foreach ($entidadesBanco as $key => $entidade)
                                <li data-bs-target="#kt_sliders_widget_2_slider"
                                    data-bs-slide-to="{{ $key }}"
                                    class="@if ($key == 0) active @endif ms-1">
                                </li>
                            @endforeach
                        </ol>
                        <!--end::Carousel Indicators-->
                    </div>
                    <!--end::Toolbar-->
                </div>
                <!--end::Header-->

                <!--begin::Body-->
                <div class="card-body py-6">
                    <!--begin::Carousel-->
                    <div class="carousel-inner">
                        <!--begin::Itens do Carrossel-->
                        @foreach ($entidadesBanco as $key => $entidade)
                            <div
                                class="carousel-item @if ($key == 0) active show @endif">
                                <!--begin::Wrapper-->
                                <div class="d-flex align-items-center mb-9">
                                    <!--begin::Symbol-->
                                    <div class="symbol symbol-70px symbol-circle me-5">
                                        {{-- 
                                                Verifica se a entidade tem um banco relacionado 
                                                e se esse banco tem um caminho de logo definido.
                                            --}}
                                        @if ($entidade->bank && $entidade->bank->logo_path)
                                            {{-- Usa o caminho do logo salvo no banco de dados --}}
                                            <img src="{{ $entidade->bank->logo_path}}"
                                                alt="{{ $entidade->bank->name }}"
                                                class="p-3" />
                                        @else
                                            {{-- Fallback: Mostra um ícone genérico se não houver logo --}}
                                            <span class="symbol-label bg-light-primary">
                                                <span
                                                    class="svg-icon svg-icon-3x svg-icon-primary">
                                                    <svg width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M20 14H18V10H20V14ZM10 14H8V10H10V14ZM15 14H13V10H15V14Z"
                                                            fill="currentColor" />
                                                        <path opacity="0.3"
                                                            d="M22 18V6C22 5.4 21.6 5 21 5H3C2.4 5 2 5.4 2 6V18C2 18.6 2.4 19 3 19H21C21.6 19 22 18.6 22 18ZM5 14H7V10H5V14ZM12 14H10V10H12V14ZM17 14H15V10H17V14Z"
                                                            fill="currentColor" />
                                                    </svg>
                                                </span>
                                            </span>
                                        @endif
                                    </div>
                                    <!--end::Symbol-->

                                    <!--begin::Info-->
                                    <div class="m-0">
                                        <!--begin::Subtitle-->
                                        <h4 class="fw-bold text-gray-800 mb-3">
                                            {{ $entidade->nome }} <span
                                                class="badge badge-info fs-base">{{ $entidade->conta }}</span>
                                        </h4>
                                        <!--end::Subtitle-->

                                        <!--begin::Items-->
                                        <div class="d-flex d-grid gap-5">
                                            <!--begin::Item-->
                                            <div class="d-flex flex-column flex-shrink-0 me-4">
                                                <!--begin::Info-->
                                                <div class="d-flex align-items-center">
                                                    <!--begin::Currency-->
                                                    <span
                                                        class="fs-4 fw-semibold text-gray-400 me-1 align-self-start">R$</span>
                                                    <!--end::Currency-->
                                                    <!--begin::Amount-->
                                                    <span
                                                        class="fs-2hx fw-bold text-dark me-2 lh-1 ls-n2">{{ number_format($entidade->saldo_atual, 2, ',', '.') }}</span>
                                                    <!--end::Amount-->
                                                    <!--begin::Badge-->
                                                    <span
                                                        class="badge badge-light-success fs-base">
                                                        <!--begin::Svg Icon | path: icons/duotune/arrows/arr066.svg-->
                                                        <span
                                                            class="svg-icon svg-icon-5 svg-icon-success ms-n1">
                                                            <svg width="24" height="24"
                                                                viewBox="0 0 24 24"
                                                                fill="none"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <rect opacity="0.5" x="13"
                                                                    y="6" width="13"
                                                                    height="2"
                                                                    rx="1"
                                                                    transform="rotate(90 13 6)"
                                                                    fill="currentColor" />
                                                                <path
                                                                    d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z"
                                                                    fill="currentColor" />
                                                            </svg>
                                                        </span>
                                                        <!--end::Svg Icon-->2.2%</span>
                                                    <!--end::Badge-->
                                                </div>
                                                <!--end::Info-->
                                            </div>
                                            <!--end::Item-->
                                        </div>
                                        <!--end::Items-->
                                    </div>
                                    <!--end::Info-->
                                </div>
                                <!--end::Wrapper-->

                                <!--begin::Action-->
                                <div class="m-0">
                                    <a href="#"
                                        class="btn btn-sm btn-light me-2 mb-2">Detalhes</a>
                                    <a href="{{ route('entidades.show', $entidade->id) }}"
                                        class="btn btn-sm btn-success mb-2">Entrar no Banco</a>
                                </div>
                                <!--end::Action-->
                            </div>
                        @endforeach
                    </div>
                </div>
                <!--end::Body-->
            </div>
            <!--end::Slider Widget 2-->

        </div>
        <!--end::Col-->
    </div>
    <!--end::Row-->
</div>
