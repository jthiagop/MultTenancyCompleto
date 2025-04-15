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
                    <!--begin::Card-->
                    <div class="card">
                        <!-- Mensagens de Erro -->

                        <!--begin::Card body-->
                        <div class="card-body p-0">
                            <form id="TelaDeLogin" method="POST" action="{{ route('telaLogin.store') }}"
                                enctype="multipart/form-data">
                                @csrf
                                <!--begin::Wrapper-->
                                <div class="card-px text-center py-20 my-10">
                                    <!--begin::Title-->
                                    <h2 class="fs-2x fw-bold mb-10">Personalizar Tela de Login</h2>
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
                                    <body id="kt_body" class="app-blank app-blank">
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
                                                            <div
                                                                class="d-flex flex-column flex-center p-7 p-lg-10 w-100">
                                                                <!--begin::Logo-->
                                                                <a href="{{ route('dashboard') }}"
                                                                    class="mb-0 mb-lg-20">
                                                                    <img alt="Logo"
                                                                        src="assets/media/logos/default.svg"
                                                                        class="h-40px h-lg-50px">
                                                                </a>
                                                                <!--end::Logo-->

                                                                <!--begin::Image and Title-->
                                                                <div class="glass-effect text-center text-white">
                                                                    <h1 class="d-none d-lg-block fs-2qx fw-bold mb-7">
                                                                        Dominus: Rápido, Eficiente e Produtivo
                                                                    </h1>
                                                                    <div class="d-none d-lg-block fs-base">
                                                                        No contexto da gestão eclesial, <a
                                                                            href="#"
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
                                                                    accept="image/*" name="backgroundImage" style="display: none;">
                                                            </div>
                                                            <!--end::Content-->
                                                        </div>
                                                        <!--end::Aside-->

                                                        <!--begin::Body (Login Form Section)-->
                                                        <div class="d-flex flex-column flex-lg-row-fluid w-lg-50 p-10">
                                                            <!--begin::Form-->
                                                            <div
                                                                class="d-flex flex-center flex-column flex-lg-row-fluid">
                                                                <!--begin::Logo-->
                                                                <a href="#" class="mb-0 mb-lg-10 disabled">
                                                                    <img alt="Logo"
                                                                        src="assets/media/logos/apple-touch-icon.svg"
                                                                        class="h-140px h-lg-150px">
                                                                </a>
                                                                <!--begin::Wrapper-->
                                                                <div class="w-lg-350px p-5">
                                                                    <form method="POST" action="{{ route('login') }}">
                                                                        @csrf
                                                                        <!--begin::Heading-->
                                                                        <div class="text-center mb-8">
                                                                            <h1 class="text-dark fw-bolder mb-3">Entre
                                                                                no Dominus</h1>
                                                                            <div class="text-gray-500 fw-semibold fs-6">
                                                                                Faça seu login</div>
                                                                        </div>
                                                                        <!--end::Heading-->

                                                                        <!--begin::Input group-->
                                                                        <div class="fv-row mb-5">
                                                                            <input id="email" type="email"
                                                                                name="email" required autofocus
                                                                                autocomplete="username"
                                                                                class="form-control bg-transparent"
                                                                                disabled placeholder="Email">
                                                                        </div>
                                                                        <div class="fv-row mb-5">
                                                                            <input id="password" type="password"
                                                                                name="password" required
                                                                                autocomplete="current-password"
                                                                                class="form-control bg-transparent"
                                                                                disabled placeholder="Senha">
                                                                        </div>
                                                                        <!--end::Input group-->

                                                                        <div class="d-grid mb-7">
                                                                            <button type="submit"
                                                                                id="kt_sign_in_submit" disabled
                                                                                class="btn btn-primary">
                                                                                <span
                                                                                    class="indicator-label">Entrar</span>
                                                                                <span class="indicator-progress">Por
                                                                                    favor, espere...
                                                                                    <span
                                                                                        class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                                                                </span>
                                                                            </button>
                                                                        </div>
                                                                    </form>
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
                            </form>

                            <!--end::Action-->
                        </div>
                        <!--end::Wrapper-->
                    </div>
                    <!--end::Card body-->
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
