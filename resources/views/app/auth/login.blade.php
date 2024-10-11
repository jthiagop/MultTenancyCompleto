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
                style="background-image: url(assets/media/misc/penha.png)">
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
                    <div class="d-none d-lg-block text-white fs-base text-center">
                        No contexto da gestão eclesial, <a href="#" class="opacity-75-hover text-warning fw-semibold me-1">Dominus</a> é um sistema
                        que permite gerenciar de forma eficiente os campos de <br />pastorais, patrimônio e financeiro. Com <a href="#" class="opacity-75-hover text-warning fw-semibold me-1">Dominus</a>,
                        a administração de sua paróquia se torna mais organizada e <br />produtiva, facilitando a gestão de recursos e atividades eclesiais.
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
                        <a href="../../demo1/dist/index.html" class="mb-0 mb-lg-10">
                            <img alt="Logo" src="assets/media/logos/apple-touch-icon.svg" class="h-140px h-lg-150px" />
                        </a>
                    <!--begin::Wrapper-->
                    <div class="w-lg-500px p-10">
                        <!--begin::Form-->
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <!--begin::Heading-->
                            <div class="text-center mb-11">
                                <!--begin::Title-->
                                <h1 class="text-dark fw-bolder mb-3">Entre no Dominus</h1>
                                <!--end::Title-->
                                <!--begin::Subtitle-->
                                <div class="text-gray-500 fw-semibold fs-6">Faça seu login</div>
                                <!--end::Subtitle-->
                            </div>
                            <!--end::Heading-->

                            <!--begin::Input group-->
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="fv-row mb-8">
                                <!--begin::Email-->
                                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="form-control bg-transparent" placeholder="Email" />
                                <!--end::Email-->
                            </div>
                            <!--end::Input group-->

                            <div class="fv-row mb-3">
                                <!--begin::Password-->
                                <input id="password" type="password" name="password" required autocomplete="current-password" class="form-control bg-transparent" placeholder="Senha" />
                                <!--end::Password-->
                            </div>
                            <!--end::Input group-->

                            <!--begin::Wrapper-->
                            <div class="d-flex flex-stack flex-wrap gap-3 fs-base fw-semibold mb-8">
                                <div></div>
                                <!--begin::Link-->
                                <label for="remember_me" class="inline-flex items-center">
                                    <input id="remember_me" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" name="remember">
                                    <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Lembre-me') }}</span>
                                </label>
                                <!--end::Link-->
                            </div>
                            <!--begin::Separator-->
                            <div class="separator separator-content my-14">
                                <span class="w-125px text-gray-500 fw-semibold fs-7">Bem-vindo</span>
                            </div>
                            <!--end::Separator-->

                            <!--begin::Submit button-->
                            <div class="d-grid mb-10">
                                <button type="submit" id="kt_sign_in_submit" class="btn btn-primary">
                                    <!--begin::Indicator label-->
                                    <span class="indicator-label">Entrar</span>
                                    <!--end::Indicator label-->
                                    <!--begin::Indicator progress-->
                                    <span class="indicator-progress">Por favor, espere...
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                    </span>
                                    <!--end::Indicator progress-->
                                </button>
                            </div>
                            <!--end::Submit button-->
                        </form>
                        <!--end::Form-->

                    </div>
                    <!--end::Wrapper-->
                </div>
                @if (session('status'))
    <div class="alert alert-warning">
        {{ session('status') }}
    </div>
@endif
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
