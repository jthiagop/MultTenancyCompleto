@php
    // Recupera a última imagem de fundo ativa ou define uma padrão
    if (!isset($backgroundImage)) {
        $backgroundImage = \App\Models\TelaDeLogin::where('status', 'ativo')->latest()->value('imagem_caminho');
    }
@endphp

<html lang="pt_BR">
<!--begin::Head-->

<head>
    <base href="../../../" />
    <title>@yield('title', config('app.name', 'Dominus'))</title>
    <meta charset="utf-8" />
    <meta name="description" content="@yield('meta_description', 'No contexto da gestão eclesial, Dominus é um sistema que permite gerenciar de forma eficiente os campos de pastorais, patrimônio e financeiro. Com Dominus, a administração de sua paróquia se torna mais organizada e produtiva, facilitando a gestão de recursos e atividades eclesiais.')" />
    <meta name="keywords" content="@yield('meta_keywords', 'gestão eclesial, sistema Dominus, campos de pastorais, gestão de patrimônio, gestão financeira eclesial, administração paroquial, gestão de recursos eclesiais, atividades eclesiais, eficiência na gestão eclesial, produtividade paroquial')" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta property="og:locale" content="pt_BR" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="https://dominus.eco.br" />
    <meta property="og:title" content="Dominus - Sistema Eclesiais" />
    <meta property="og:url" content="https://dominusbr.com/" />
    <meta property="og:site_name" content="Dominus | Dominus Sistema Eclesial" />
    <link rel="canonical" href="@yield('canonical_url', 'https://dominusbr.com')" />
    <link rel="shortcut icon" href="{{ url('tenancy/assets/media/app/mini-logo.svg') }}" />
    <!--begin::Fonts(mandatory for all pages)-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <!--end::Fonts-->
    <!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
    <link href="/tenancy/assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    <link href="/tenancy/assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
    <!--end::Global Stylesheets Bundle-->
    @stack('styles')
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
        <!--begin::Authentication -->
        <div class="d-flex flex-column flex-lg-row flex-column-fluid">
            <!--begin::Aside-->
            <div class="d-flex flex-lg-row-fluid w-lg-50 bgi-size-cover bgi-position-center position-relative"
                style="background-image: url('{{ isset($backgroundImage) && $backgroundImage ? route('file', ['path' => $backgroundImage]) : url('/tenancy/assets/media/misc/penha.png') }}');">

                <!--begin::Gradient Overlay - Preto para Transparente na parte inferior (apenas telas >= 992px)-->
                <div class="d-none d-lg-block position-absolute bottom-0 start-0 end-0"
                    style="height: 200px; background: linear-gradient(to top, rgba(0, 0, 0, 0.8) 0%, rgba(0, 0, 0, 0.4) 50%, transparent 100%); pointer-events: none; z-index: 1;">
                </div>
                <!--end::Gradient Overlay-->

                <!--begin::Content-->
                <div class="d-flex flex-column flex-center p-7 p-lg-10 w-100 position-relative">
                    <!--begin::Logo-->
                    <a href="{{ route('dashboard') }}" class="mb-0 mb-lg-20 position-relative">
                        <img alt="Logo" src="{{ url('tenancy/assets/media/app/default-logo-dark.svg') }}"
                            class="h-30px h-lg-30px position-relative"
                            style="z-index: 1; filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 15));" />
                    </a>
                    <!--end::Logo-->

                    <!--begin::Image-->
                    <img class="d-none d-lg-block mx-auto w-300px w-lg-75 w-xl-500px mb-10 mb-lg-10"
                        src="/tenancy/assets/media/misc/auth-screens.png" alt="" />
                    <!--end::Image-->
                    <div class="glass-effect">
                        <!--begin::Aside Content-->
                        @yield('aside_content')
                        <!--end::Aside Content-->
                    </div>
                </div>
                <!--end::Content-->
            </div>
            <!--end::Aside-->
            <!--begin::Body-->
            <div class="d-flex flex-column flex-lg-row-fluid w-lg-50 p-10">
                <!--begin::Form-->
                <div class="d-flex flex-center flex-column flex-lg-row-fluid">
                    <!--begin::Logo-->
                    <a href="{{ route('dashboard') }}" class="mb-0 mb-lg-10">
                        <img alt="Logo" src="{{ url('tenancy/assets/media/app/mini-logo.svg') }}" class="h-100px h-lg-100px" />
                    </a>
                    <!--begin::Wrapper-->
                    <div class="w-lg-500px p-10">
                        <!--begin::Form Content-->
                        @yield('form_content')
                        <!--end::Form Content-->
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
                <div class="d-flex flex-column flex-center flex-wrap px-5">
                    <!--begin::Links-->
                    <div class="d-flex fw-semibold text-primary fs-base mb-4">
                        <a href="{{ route('termos') }}" class="px-5">Termos</a>
                        <a href="#" class="px-5" target="_blank">Plans</a>
                        <a href="#" class="px-5" target="_blank">Contato</a>
                    </div>
                    <!--end::Links-->
                    
                    <!--begin::Legal Info-->
                    <div class="text-center text-muted fs-7">
                        <p class="mb-1 fw-bold">Dominus Tecnologia</p>
                        <p class="mb-1">
                            CNPJ: 60.571.888/0001-44 <span class="mx-1">-</span> Razão Social: Jose Thiago Pereira de Oliveira
                        </p>
                        <p class="mt-2 mb-0">
                            © {{ date('Y') }} Dominus. Todos os direitos reservados.
                        </p>
                    </div>
                    <!--end::Legal Info-->
                </div>
                <!--end::Footer-->
            </div>
            <!--end::Body-->
        </div>
        <!--end::Authentication-->
    </div>
    <!--end::Root-->
    <!--begin::Javascript-->
    <script>
        var hostUrl = "/tenancy/assets/";
    </script>
    <!--begin::Global Javascript Bundle(mandatory for all pages)-->
    <script src="/tenancy/assets/plugins/global/plugins.bundle.js"></script>
    <script src="/tenancy/assets/js/scripts.bundle.js"></script>
    <!--end::Global Javascript Bundle-->
    <!--begin::Custom Javascript(used for this page only)-->
    @stack('scripts')
    <!--end::Custom Javascript-->
    <!--end::Javascript-->
</body>
<!--end::Body-->

</html>

