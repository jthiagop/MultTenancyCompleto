<style>
    /* Estilos para a "Tela de Computador" */
    .computer-frame {
        width: 80%;
        max-width: 1024px;
        margin: 40px auto;
        padding: 20px;
        background-color: #333;
        border-radius: 15px;
        box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.3);
        position: relative;
        overflow: hidden;
    }

    .computer-screen {
        background-color: #f8f9fa;
        border-radius: 10px;
        overflow: hidden;
    }

    .computer-frame:before {
        content: '';
        position: absolute;
        top: -20px;
        left: 50%;
        transform: translateX(-50%);
        width: 60px;
        height: 8px;
        background-color: #333;
        border-radius: 5px;
    }

    .computer-frame:after {
        content: '';
        position: absolute;
        bottom: -30px;
        left: 50%;
        transform: translateX(-50%);
        width: 120px;
        height: 15px;
        background-color: #333;
        border-radius: 10px;
    }
</style>

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
                            Getting Started</h1>
                        <!--end::Title-->
                        <!--begin::Breadcrumb-->
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">
                                <a href="../../demo1/dist/index.html" class="text-muted text-hover-primary">Home</a>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-400 w-5px h-2px"></span>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">Customers</li>
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
                    <!--begin::Card body-->
                    <div class="card-body p-0">
                        <form id="TelaDeLogin" method="POST" action="{{ route('telaLogin.store') }}"
                            enctype="multipart/form-data">
                            @csrf
                            <!--begin::Wrapper-->
                            <div class="card-px text-center py-2 my-2">
                                <!--begin::Title-->
                                <h2 class="fs-2x fw-bold">Personalizar Tela de Login</h2>
                                <!--end::Title-->
                                <!--begin::Description-->
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <div id="kt_body" class="app-blank app-blank">
                                    <div class="computer-frame">
                                        <div class="computer-screen">
                                            <!--begin::Root-->
                                            <div class="d-flex flex-column flex-root" id="kt_app_root">
                                                <!--begin::Authentication - Sign-in-->
                                                <div class="d-flex flex-column flex-lg-row flex-column-fluid">

                                                    <!-- Input de Upload de Imagem -->
                                                    <!--begin::Aside (Left Section with Background Image)-->
                                                    <div class="d-flex flex-lg-row-fluid w-lg-50 bgi-size-cover bgi-position-center"
                                                        style="background-image: url('assets/media/misc/penha.png');">
                                                        <!--begin::Content-->
                                                        <div class="d-flex flex-column flex-center p-7 p-lg-10 w-100">
                                                            <!--begin::Logo-->
                                                            <a href="{{ route('dashboard') }}" class="mb-0 mb-lg-20">
                                                                <img alt="Logo" src="assets/media/logos/default.svg"
                                                                    class="h-40px h-lg-50px">
                                                            </a>
                                                            <!--end::Logo-->

                                                            <!--begin::Image and Title-->
                                                            <div class="glass-effect text-center text-white">
                                                                <h1 class="d-none d-lg-block fs-2qx fw-bold mb-7">
                                                                    Dominus: Rápido, Eficiente e Produtivo
                                                                </h1>
                                                                <div class="d-none d-lg-block fs-base">
                                                                    No contexto da gestão eclesial, <a href="#"
                                                                        class="opacity-75-hover text-warning fw-semibold me-1">Dominus</a>
                                                                    é um sistema
                                                                    que permite gerenciar de forma eficiente os
                                                                    campos
                                                                    de pastorais, patrimônio e financeiro.
                                                                </div>
                                                            </div>
                                                            <!--end::Image and Title-->

                                                            <!-- Upload Button -->
                                                            <input type="file" id="backgroundImageUpload"
                                                                accept="image/*" name="backgroundImage"
                                                                style="display: none;">
                                                        </div>
                                                        <!--end::Content-->
                                                    </div>
                                                    <!--end::Aside-->

                                                    <!--begin::Body (Login Form Section)-->
                                                    <div class="d-flex flex-column flex-lg-row-fluid w-lg-50 p-10">
                                                        <!--begin::Form-->
                                                        <div class="d-flex flex-center flex-column flex-lg-row-fluid">
                                                            <!--begin::Logo-->
                                                            <a href="#" class="mb-0 mb-lg-10 disabled">
                                                                <img alt="Logo"
                                                                    src="assets/media/logos/apple-touch-icon.svg"
                                                                    class="h-140px h-lg-150px">
                                                            </a>
                                                            <!-- Form Inputs for Customization -->
                                                            <div class="w-100 p-5">
                                                                <div class="fv-row mb-5">
                                                                    <label class="form-label required">Nome do Convento</label>
                                                                    <input type="text" name="descricao" class="form-control" 
                                                                           placeholder="Ex: Convento São Francisco" required
                                                                           id="input-descricao">
                                                                </div>
                                                                <div class="fv-row mb-5">
                                                                    <label class="form-label required">Localidade</label>
                                                                    <input type="text" name="localidade" class="form-control" 
                                                                           placeholder="Ex: Recife - PE" required
                                                                           id="input-localidade">
                                                                </div>

                                                                <!-- Login Form Preview (Disabled) -->
                                                                <div class="opacity-50 mt-10">
                                                                    <div class="text-center mb-5">
                                                                        <h3 class="text-dark fw-bolder">Visualização do Login</h3>
                                                                    </div>
                                                                    <div class="fv-row mb-5">
                                                                        <input type="email" class="form-control bg-transparent" disabled placeholder="Email">
                                                                    </div>
                                                                    <div class="fv-row mb-5">
                                                                        <input type="password" class="form-control bg-transparent" disabled placeholder="Senha">
                                                                    </div>
                                                                    <div class="d-grid mb-7">
                                                                        <button type="button" disabled class="btn btn-primary">Entrar</button>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!--end::Wrapper-->
                                                        </div>
                                                        <!--end::Form-->
                                                    </div>
                                                    <!--end::Body-->
                                                </div>
                                                <!--end::Authentication - Sign-in-->
                                            </div>
                                            <!--end::Root-->
                                        </div>
                                    </div>
                                    <!--end::Description-->
                                    <div class="tns tns-default mb-3" style="direction: ltr">
                                        <!--begin::Slider-->
                                        <div data-tns="true" data-tns-loop="true" data-tns-swipe-angle="false"
                                            data-tns-speed="2000" data-tns-autoplay="true"
                                            data-tns-autoplay-timeout="8000" data-tns-items="3" data-tns-center="true"
                                            data-tns-slide-by="true" data-tns-nav-container="#kt_slider_thumbnails"
                                            data-tns-nav-as-thumbnails="true" data-tns-prev-button="#kt_slider_prev"
                                            data-tns-next-button="#kt_slider_next">
                                            @forelse($existingImages as $image)
                                            <!--begin::Item-->
                                            <div class="text-center px-5 py-5">
                                                <div class="card shadow-sm h-100">
                                                    <img src="{{ asset('storage/' . $image->imagem_caminho) }}"
                                                        class="card-img-top card-rounded mw-100" style="height: 200px; object-fit: cover;" alt="{{ $image->descricao }}" />
                                                    <div class="card-body p-3">
                                                        <div class="fw-bold text-gray-800">{{Str::limit($image->descricao, 20)}}</div>
                                                        <div class="fs-7 text-muted mt-1">
                                                            <i class="fas fa-map-marker-alt fs-9 me-1"></i> {{Str::limit($image->localidade, 20)}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--end::Item-->
                                            @empty
                                            <div class="text-center px-5 py-5">
                                                <img src="assets/media/misc/penha.png" class="card-rounded mw-100" alt="Padrão" />
                                                <p class="text-muted mt-2">Nenhuma imagem personalizada.</p>
                                            </div>
                                            @endforelse
                                        </div>
                                        <!--end::Slider-->

                                        <!--begin::Slider button-->
                                        <button class="btn btn-icon btn-active-color-primary" id="kt_slider_prev">
                                            <i class="fas fa-chevron-left"></i>
                                        </button>
                                        <!--end::Slider button-->

                                        <!--begin::Slider button-->
                                        <button class="btn btn-icon btn-active-color-primary" id="kt_slider_next">
                                            <i class="fas fa-chevron-right"></i>
                                        </button>
                                        <!--end::Slider button-->
                                    </div>

                                    <div class="d-flex flex-center">
                                        <ul class="d-flex align-items-center list-unstyled gap-5 cursor-pointer" id="kt_slider_thumbnails">
                                            @foreach($existingImages as $image)
                                            <li class="d-flex gap-3">
                                                <img src="{{ asset('storage/' . $image->imagem_caminho) }}" class="w-50px h-50px rounded object-fit-cover"
                                                    alt="" />
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <!--begin::Action-->
                                    <!--begin::Ações (Centralizados)-->
                                    <div class="d-flex justify-content-center mt-5">
                                        <div class="d-flex gap-3 align-items-center">
                                            <!-- Botão de Upload de Imagem -->
                                            <label for="backgroundImageUpload"
                                                class="btn btn-light-success d-flex align-items-center gap-2">
                                                <i class="fa-solid fa-upload"></i> Upload de Imagem
                                            </label>
                                            <input type="file" id="backgroundImageUpload" accept="image/*"
                                                style="display: none;">

                                            <!-- Botão de Salvar Tela (Submit) -->
                                            <button type="submit"
                                                class="btn btn-light-primary d-flex align-items-center gap-2">
                                                <i class="fa-solid fa-floppy-disk"></i> Salvar Tela
                                            </button>
                                        </div>
                                    </div>
                                    <!--end::Ações-->
                                </div>

                            </div>
                        </form>

                        <!--end::Action-->
                    </div>
                    <!--end::Wrapper-->
                </div>
                <!--end::Card-->
            </div>
            <!--end::Content container-->
        </div>
        <!--end::Content-->
    </div>
    <!--end::Content wrapper-->
    </div>
    <!--end:::Main-->

    <!--begin::Javascript-->
    <script>
        document.getElementById('backgroundImageUpload').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.querySelector(
                            '.d-flex.flex-lg-row-fluid.w-lg-50.bgi-size-cover.bgi-position-center')
                        .style.backgroundImage = `url('${e.target.result}')`;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
    <!--end::Javascript-->
    </body>


</x-tenant-app-layout>
