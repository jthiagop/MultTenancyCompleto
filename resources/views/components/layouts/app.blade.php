<!DOCTYPE html>
<html class="h-full" data-kt-theme="true" data-kt-theme-mode="light" dir="ltr" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <base href="{{ url('/') }}">
    <title>{{ config('app.name', 'Dominus') }} V2 - Dashboard</title>
    <meta charset="utf-8"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta content="width=device-width, initial-scale=1, shrink-to-fit=no" name="viewport"/>
    <meta content="Dominus Sistema Eclesial - Interface V2" name="description"/>
    
    <!-- Favicon -->
    <link href="{{ asset('assets/media/app/favicon.ico') }}" rel="shortcut icon"/>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    
    <!-- Vendors CSS (mantidos em public por serem assets externos - usando global_asset para evitar tenancy) -->
    <link href="{{ global_asset('metronic/assets/vendors/apexcharts/apexcharts.css') }}" rel="stylesheet"/>
    <link href="{{ global_asset('metronic/assets/vendors/keenicons/styles.bundle.css') }}" rel="stylesheet"/>
    
    <!-- Metronic Tailwind CSS via Vite -->
    @vite(['resources/css/metronic-v2.css'])
    
    <!-- Livewire Styles -->
    @livewireStyles

    <!-- Stack de estilos adicionais -->
    @stack('styles')
</head>

<body class="antialiased flex h-full text-base text-foreground bg-background demo1 kt-sidebar-fixed kt-header-fixed">
    
    <!-- Theme Mode Script -->
    <script>
        const defaultThemeMode = 'light';
        let themeMode;

        if (document.documentElement) {
            if (localStorage.getItem('kt-theme')) {
                themeMode = localStorage.getItem('kt-theme');
            } else if (document.documentElement.hasAttribute('data-kt-theme-mode')) {
                themeMode = document.documentElement.getAttribute('data-kt-theme-mode');
            } else {
                themeMode = defaultThemeMode;
            }

            if (themeMode === 'system') {
                themeMode = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }

            document.documentElement.classList.add(themeMode);
        }
    </script>
    <!-- End of Theme Mode Script -->
    
    <!-- Page -->
    <div class="flex grow">
        
        <!-- Sidebar -->
        @include('v2.layouts.partials.sidebar')
        <!-- End Sidebar -->
        
        <!-- Wrapper -->
        <div class="kt-wrapper flex grow flex-col">
            
            <!-- Header -->
            @include('v2.layouts.partials.header')
            <!-- End Header -->
            
            <!-- Content -->
            <main class="kt-content grow" id="kt_content">
                
                <!-- Container -->
                <div class="container-fluid px-5 lg:px-7.5 lg:pt-7.5 pt-5 pb-5 lg:pb-7.5" id="content_container">
                    
                    <!-- Badge V2 Beta -->
                    <div class="flex items-center gap-3 bg-primary/10 border border-primary/20 rounded-lg p-4 mb-5">
                        <div class="flex items-center justify-center size-10 rounded-full bg-primary text-white">
                            <i class="ki-filled ki-rocket text-lg"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-sm font-semibold text-foreground mb-0.5">🚀 Nova Interface V2</h4>
                            <p class="text-xs text-secondary-foreground mb-0">Você está testando a nova versão moderna do Dominus com Metronic v9.3.2</p>
                        </div>
                        <a href="{{ route('dashboard') }}" class="kt-btn kt-btn-sm kt-btn-outline kt-btn-mono shrink-0">
                            <i class="ki-filled ki-left text-xs me-1"></i>
                            Voltar para V1
                        </a>
                    </div>
                    
                    <!-- Page Content (Livewire Slot) -->
                    {{ $slot }}
                    
                </div>
                <!-- End Container -->
                
            </main>
            <!-- End Content -->
            
            <!-- Footer -->
            @include('v2.layouts.partials.footer')
            <!-- End Footer -->
            
        </div>
        <!-- End Wrapper -->
        
    </div>
    <!-- End Page -->
    
    <!-- Drawers & Modals -->
    @include('v2.layouts.partials.drawers')
    <!-- End Drawers & Modals -->
    
    <!-- Scripts do Metronic (mantidos em public por serem bundles compilados - usando global_asset para evitar tenancy) -->
    <script src="{{ global_asset('metronic/assets/js/core.bundle.js') }}"></script>
    <script src="{{ global_asset('metronic/assets/vendors/ktui/ktui.min.js') }}"></script>
    <script src="{{ global_asset('metronic/assets/vendors/apexcharts/apexcharts.min.js') }}"></script>
    
    <!-- Metronic V2 JS via Vite -->
    @vite(['resources/js/metronic-v2.js'])
    
    <!-- Livewire Scripts -->
    @livewireScripts

    <!-- Stack de scripts adicionais -->
    @stack('scripts')
    
</body>
</html>

