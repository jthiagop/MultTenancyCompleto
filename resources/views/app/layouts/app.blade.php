<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<!--begin::Head-->

<head>
    <base href="" />
    <title>{{ config('app.name', 'Dominus') }}</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description"
        content="No contexto da gestão eclesial, Dominus é um sistema que permite gerenciar de forma eficiente os campos de pastorais, patrimônio e financeiro. Com Dominus,
    a administração de sua paróquia se torna mais organizada e produtiva, facilitando a gestão de recursos e atividades eclesiais." />
    <meta name="keywords"
        content="gestão eclesial, sistema Dominus, campos de pastorais, gestão de patrimônio, gestão financeira eclesial, administração paroquial, gestão de recursos eclesiais, atividades eclesiais, eficiência na gestão eclesial, produtividade paroquial" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta property="og:locale" content="pt_BR" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="Dominus - Sistema Eclesiais" />
    <meta property="og:url" content="https://dominusbr.com/" />
    <meta property="og:site_name" content="Dominus | Dominus Sistema Eclesial" />
    <link rel="canonical" href="https://dominusbr.com/login" />
    <link rel="shortcut icon" href="{{ url('tenancy/assets/media/app/mini-logo.svg') }}" />
    <!--begin::Fonts(mandatory for all pages)-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <!--end::Fonts-->
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <!--end::Global Stylesheets Bundle-->
    <link href="{{ url('tenancy/assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet">
    <link href="{{ url('tenancy/assets/css/style.bundle.css') }}" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!--end::Vite CSS Bundle-->

    <!--begin::Sidebar State Restore (antes da renderização)-->
    <script>
        (function () {
            // Função para ler cookie antes do DOM estar pronto
            function getCookie(name) {
                var value = "; " + document.cookie;
                var parts = value.split("; " + name + "=");
                if (parts.length === 2) return parts.pop().split(";").shift();
                return null;
            }

            // Restaurar estado do sidebar ANTES de qualquer renderização
            var sidebarState = getCookie('sidebar_minimize_state');

            // Função para aplicar o estado
            function applySidebarState() {
                if (sidebarState === 'on' && document.body) {
                    document.body.setAttribute('data-kt-app-sidebar-minimize', 'on');
                    // Aguardar um pouco para o toggle estar disponível
                    setTimeout(function () {
                        var toggle = document.getElementById('kt_app_sidebar_toggle');
                        if (toggle) {
                            toggle.classList.add('active');
                        }
                    }, 10);
                }
            }

            // Tentar aplicar imediatamente se o body já existir
            if (document.body) {
                applySidebarState();
            } else {
                // Aguardar o body estar disponível
                var checkBody = setInterval(function () {
                    if (document.body) {
                        applySidebarState();
                        clearInterval(checkBody);
                    }
                }, 10);

                // Fallback com DOMContentLoaded
                document.addEventListener('DOMContentLoaded', applySidebarState, false);
            }
        })();
    </script>
    <!--end::Sidebar State Restore-->
</head>
<!--end::Head-->
<!--begin::Body-->

<!--begin::Body-->

@php
    $sidebarState = request()->cookie('sidebar_minimize_state');
@endphp

<body id="kt_app_body" data-kt-app-layout="dark-sidebar" data-kt-app-header-fixed="true"
    data-kt-app-sidebar-enabled="true" data-kt-app-sidebar-fixed="true" data-kt-app-sidebar-hoverable="true"
    data-kt-app-sidebar-push-header="true" data-kt-app-sidebar-push-toolbar="true"
    data-kt-app-sidebar-push-footer="true" data-kt-app-toolbar-enabled="true" class="app-default" {{ $sidebarState === 'on' ? ' data-kt-app-sidebar-minimize="on"' : '' }}>
    <!--begin::Theme mode setup on page load-->
    <script>var defaultThemeMode = "light"; var themeMode; if (document.documentElement) { if (document.documentElement.hasAttribute("data-bs-theme-mode")) { themeMode = document.documentElement.getAttribute("data-bs-theme-mode"); } else { if (localStorage.getItem("data-bs-theme") !== null) { themeMode = localStorage.getItem("data-bs-theme"); } else { themeMode = defaultThemeMode; } } if (themeMode === "system") { themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light"; } document.documentElement.setAttribute("data-bs-theme", themeMode); }</script>
    <!--end::Theme mode setup on page load-->

    <style>
        /* Flash Messages */
        .flash-messages {
            position: fixed;
            z-index: 999;
            /* Garante que esteja acima da navbar */
            top: 80px;
            /* Coloca abaixo da navbar */
            right: 20px;
        }
    </style>
    <!--end::Theme mode setup on page load-->
    <!--begin::App-->
    <div class="d-flex flex-column flex-root app-root" id="kt_app_root">
        <!--begin::Page-->
        <div class="app-page flex-column flex-column-fluid" id="kt_app_page">
            <!--begin::Header-->
            @include('app.layouts.navigation')
            <!--end::Wrapper-->
        </div>
        <!--end::Page-->
    </div>
    <!--end::App-->
    <!--begin::Javascript-->
    <script>
        var hostUrl = "{{ url('') }}/tenancy/assets/";
    </script>
    <!--begin::Global Javascript Bundle (Metronic plugins + App)-->
    <script src="{{ url('tenancy/assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ url('tenancy/assets/js/scripts.bundle.js') }}"></script>
    @vite(['resources/js/app.js'])
    <!--end::Global Javascript Bundle-->
    <!--begin::Vendors Javascript(used for this page only)-->
    <script src="{{ url('tenancy/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <!--end::Vendors Javascript-->
    <link href="{{ url('tenancy/assets/css/custom.css') }}" rel="stylesheet" type="text/css" />
    <!--begin::Toast Script (converte mensagens de sessão em toasts)-->
    <script src="{{ url('tenancy/assets/js/toasts.js') }}"></script>
    <!--end::Toast Script-->
    <!--begin::Session Handler Script (trata expiração de sessão)-->
    <script src="{{ url('tenancy/assets/js/session-handler.js') }}"></script>
    <!--end::Session Handler Script-->
    <!--begin::Favorites Script-->
    <script src="{{ url('tenancy/assets/js/custom/apps/favorites.js') }}"></script>
    <!--end::Favorites Script-->
    <!--begin::Sidebar Menu Active State Script-->
    <script src="{{ url('tenancy/assets/js/sidebar-menu-active.js') }}"></script>
    <!--end::Sidebar Menu Active State Script-->
    <!--begin::Sidebar State Script-->
    <script src="{{ url('tenancy/assets/js/sidebar-state.js') }}"></script>
    <!--end::Sidebar State Script-->
    @stack('scripts')

    {{-- Tenant DataTable Pane Module - Agora carregado via Vite bundle (app.js) --}}

    {{-- Amazon-style Notifications System --}}
    <link href="{{ url('tenancy/assets/css/notifications-amazon.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ url('tenancy/assets/js/notifications-amazon.js') }}"></script>

    {{-- Exibir notificações da sessão Laravel --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Exibir notificações da sessão
            @if(session()->has('success'))
                notify.success("{{ session('success') }}");
            @endif

            @if(session()->has('error'))
                notify.error("{{ session('error') }}");
            @endif

            @if(session()->has('warning'))
                notify.warning("{{ session('warning') }}");
            @endif

            @if(session()->has('info'))
                notify.info("{{ session('info') }}");
            @endif

        });
    </script>
</body>
<!--end::Body-->

</html>