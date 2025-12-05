@php
    // Recupera a última imagem de fundo ativa ou define uma padrão
    $backgroundImage = \App\Models\TelaDeLogin::where('status', 'ativo')->latest()->value('imagem_caminho');
@endphp

<html lang="pt_BR">
<!--begin::Head-->

<head>
    <base href="../../../" />
    <title>{{ config('app.name', 'Dominus') }}</title>
    <meta charset="utf-8" />
    <meta name="description"
        content="No contexto da gestão eclesial, Dominus é um sistema que permite gerenciar de forma eficiente os campos de pastorais, patrimônio e financeiro. Com Dominus,
    a administração de sua paróquia se torna mais organizada e produtiva, facilitando a gestão de recursos e atividades eclesiais." />
    <meta name="keywords"
        content="gestão eclesial, sistema Dominus, campos de pastorais, gestão de patrimônio, gestão financeira eclesial, administração paroquial, gestão de recursos eclesiais, atividades eclesiais, eficiência na gestão eclesial, produtividade paroquial" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta property="og:locale" content="pt_BR" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="https://keenthemes.com/keen" />
    <meta property="og:title" content="Dominus - Sistema Eclesiais" />
    <meta property="og:url" content="https://dominusbr.com/" />
    <meta property="og:site_name" content="Dominus | Dominus Sistema Eclesial" />
    <link rel="canonical" href="https://dominusbr.com/login" />
    <link rel="shortcut icon" href="assets/media/logos/favicon.ico" />
    <!--begin::Fonts(mandatory for all pages)-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <!--end::Fonts-->
    <!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
    <link href="../assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    <link href="../assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
    <!--end::Global Stylesheets Bundle-->
</head>
<!--end::Head-->
<!--begin::Body-->

<body id="kt_body" class="app-blank app-blank">
    <!--begin::Theme mode setup on page load-->
    <script>
        var defaultThemeMode = "light";
        var themeMode;
        if (document.documentElement) {
            if (document.documentElement.hasAttribute("data-bs-theme-mode")) {
                themeMode = document.documentElement.getAttribute("data-bs-theme-mode");
            } else {
                if (localStorage.getItem("data-bs-theme") !== null) {
                    themeMode = localStorage.getItem("data-bs-theme");
                } else {
                    themeMode = defaultThemeMode;
                }
            }
            if (themeMode === "system") {
                themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
            }
            document.documentElement.setAttribute("data-bs-theme", themeMode);
        }
    </script>
    <!--end::Theme mode setup on page load-->
    <!--begin::Root-->
    <div class="d-flex flex-column flex-root" id="kt_app_root">
        <!--begin::Authentication - Sign-in -->
        <div class="d-flex flex-column flex-lg-row flex-column-fluid">
            <!--begin::Aside-->
            <div class="d-flex flex-lg-row-fluid w-lg-50 bgi-size-cover bgi-position-center"
                style="background-image: url('{{ $backgroundImage ? route('file', ['path' => $backgroundImage]) : asset('/assets/media/misc/penha.png') }}');">

                <!--begin::Content-->
                <div class="d-flex flex-column flex-center p-7 p-lg-10 w-100">
                    <!--begin::Logo-->
                    <a href="{{ route('dashboard') }}" class="mb-0 mb-lg-20">
                        <img alt="Logo" src="/assets/media/logos/default.svg" class="h-40px h-lg-50px" />
                    </a>
                    <!--end::Logo-->

                    <!--begin::Image-->
                    <img class="d-none d-lg-block mx-auto w-300px w-lg-75 w-xl-500px mb-10 mb-lg-20"
                        src="assets/media/misc/auth-screens.png" alt="" />
                    <!--end::Image-->
                    <div class="glass-effect">
                        <!--begin::Title-->
                        <h1 class="d-none d-lg-block text-white fs-2qx fw-bold text-center mb-7">
                            Dominus: Rápido, Eficiente e Produtivo
                        </h1>
                        <!--end::Title-->
                        <!--begin::Text-->
                        <div class="d-none d-lg-block text-white fs-base text-center px-5"
                            style="line-height: 1.8; max-width: 600px; margin: 0 auto;">
                            <p class="mb-3" style="text-align: justify; text-align-last: center;">
                                No contexto da gestão eclesial, <a href="#"
                                    class="opacity-75-hover text-warning fw-semibold me-1">Dominus</a> é um sistema
                                que permite gerenciar de forma eficiente os campos de pastorais, patrimônio e
                                financeiro.
                            </p>
                            <p class="mb-0" style="text-align: justify; text-align-last: center;">
                                Com <a href="#"
                                    class="opacity-75-hover text-warning fw-semibold me-1">Dominus</a>,
                                a administração de sua paróquia se torna mais organizada e produtiva, facilitando a
                                gestão de recursos e atividades eclesiais.
                            </p>
                        </div>
                        <!--end::Text-->
                    </div>
                </div>
                <!--end::Content-->
            </div>
            <!--begin::Aside-->
            <!--begin::Body-->
            <div class="d-flex flex-column flex-lg-row-fluid w-lg-50 p-10">
                <!--begin::Form-->
                <div class="d-flex flex-center flex-column flex-lg-row-fluid">
                    <!--begin::Logo-->
                    <a href="../../demo1/dist/index.html" class="mb-0 mb-lg-50">
                        <img alt="Logo" src="assets/media/logos/apple-touch-icon.svg" class="h-100px h-lg-100px" />
                    </a>
                    <!--begin::Wrapper-->
                    <div class="w-lg-500px p-10">
                        <!--begin::Form-->
                        <form class="form w-100" method="POST" action="{{ route('first-access') }}" id="kt_first_access_form">
                            @csrf
                            <!--begin::Heading-->
                            <div class="text-center mb-10">
                                <!--begin::Title-->
                                <h1 class="text-dark fw-bolder mb-3">Defina sua senha</h1>
                                <!--end::Title-->
                                <!--begin::Link-->
                                <div class="text-gray-500 fw-semibold fs-6">
                                    Por favor, defina uma nova senha para continuar
                                </div>
                                <!--end::Link-->
                            </div>
                            <!--begin::Heading-->

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if (session('status'))
                                <div class="alert alert-success">
                                    {{ session('status') }}
                                </div>
                            @endif

                            <!--begin::Input group - Senha Atual-->
                            <div class="fv-row mb-8">
                                <label class="form-label fw-bolder text-dark fs-6">Senha Atual</label>
                                <div class="position-relative">
                                    <input class="form-control bg-transparent" type="password"
                                        placeholder="Digite a senha informada pelo administrador"
                                        name="current_password" autocomplete="current-password" required />
                                    <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2"
                                        data-kt-password-meter-control="visibility">
                                        <i class="bi bi-eye-slash fs-2"></i>
                                        <i class="bi bi-eye fs-2 d-none"></i>
                                    </span>
                                </div>
                                @error('current_password')
                                    <div class="text-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                            <!--end::Input group-->

                            <!--begin::Input group - Nova Senha-->
                            <div class="fv-row mb-8" data-kt-password-meter="true">
                                <label class="form-label fw-bolder text-dark fs-6">Nova Senha</label>
                                <div class="mb-1">
                                    <div class="position-relative mb-3">
                                        <input class="form-control bg-transparent" type="password"
                                            placeholder="Digite sua nova senha" name="password" autocomplete="new-password" required />
                                        <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2"
                                            data-kt-password-meter-control="visibility">
                                            <i class="bi bi-eye-slash fs-2"></i>
                                            <i class="bi bi-eye fs-2 d-none"></i>
                                        </span>
                                    </div>
                                    <!--begin::Meter-->
                                    <div class="d-flex align-items-center mb-3"
                                        data-kt-password-meter-control="highlight">
                                        <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2">
                                        </div>
                                        <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2">
                                        </div>
                                        <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2">
                                        </div>
                                        <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px"></div>
                                    </div>
                                    <!--end::Meter-->
                                </div>
                                <!--begin::Hint-->
                                <div class="text-muted">Use 8 ou mais caracteres com uma combinação de letras, números e símbolos.</div>
                                <!--end::Hint-->
                                @error('password')
                                    <div class="text-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                            <!--end::Input group-->

                            <!--begin::Input group - Confirmar Senha-->
                            <div class="fv-row mb-8">
                                <label class="form-label fw-bolder text-dark fs-6">Confirmar Nova Senha</label>
                                <div class="position-relative">
                                    <input class="form-control bg-transparent" type="password"
                                        placeholder="Confirme sua nova senha" name="password_confirmation"
                                        autocomplete="new-password" required />
                                    <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2"
                                        data-kt-password-meter-control="visibility">
                                        <i class="bi bi-eye-slash fs-2"></i>
                                        <i class="bi bi-eye fs-2 d-none"></i>
                                    </span>
                                </div>
                            </div>
                            <!--end::Input group-->

                            <!--begin::Action-->
                            <div class="d-grid mb-10">
                                <button type="submit" class="btn btn-primary">
                                    <!--begin::Indicator label-->
                                    <span class="indicator-label">Salvar Senha</span>
                                    <!--end::Indicator label-->
                                    <!--begin::Indicator progress-->
                                    <span class="indicator-progress">Aguarde...
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                    <!--end::Indicator progress-->
                                </button>
                            </div>
                            <!--end::Action-->
                        </form>
                        <!--end::Form-->
                    </div>
                    <!--end::Wrapper-->
                </div>
                <!--end::Form-->
                <!--begin::Footer-->
                <div class="d-flex flex-center flex-wrap px-5">
                    <!--begin::Links-->
                    <div class="d-flex fw-semibold text-primary fs-base">
                        <a href="#" class="px-5" target="_blank">Termos</a>
                        <a href="#" class="px-5" target="_blank">Plans</a>
                        <a href="#" class="px-5" target="_blank">Contato</a>
                    </div>
                    <!--end::Links-->
                </div>
                <!--end::Footer-->
            </div>
            <!--end::Body-->
        </div>
        <!--end::Authentication - Sign-in-->
    </div>
    <!--end::Root-->
    <!--begin::Javascript-->
    <script>
        var hostUrl = "assets/";
    </script>
    <!--begin::Global Javascript Bundle(mandatory for all pages)-->
    <script src="/assets/plugins/global/plugins.bundle.js"></script>
    <script src="/assets/js/scripts.bundle.js"></script>
    <!--end::Global Javascript Bundle-->
    <!--begin::Custom Javascript(used for this page only)-->
    <!--end::Custom Javascript-->
    <!--end::Javascript-->
</body>
<!--end::Body-->

</html>
