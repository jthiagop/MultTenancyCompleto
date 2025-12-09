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
                    <a href="{{ route('dashboard') }}" class="mb-0 mb-lg-20 position-relative" style="display: inline-block; padding: 15px 20px; border-radius: 12px; background: radial-gradient(ellipse at center, rgba(0, 0, 0, 0.35) 0%, rgba(0, 0, 0, 0.25) 40%, rgba(0, 0, 0, 0.15) 70%, rgba(0, 0, 0, 0.05) 90%, transparent 100%); backdrop-filter: blur(3px);">
                        <img alt="Logo" src="/assets/media/logos/default-white.svg" class="h-40px h-lg-50px position-relative" style="z-index: 1; filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));" />
                    </a>
                    <!--end::Logo-->

                    <!--begin::Image-->
                    <img class="d-none d-lg-block mx-auto w-300px w-lg-75 w-xl-500px mb-10 mb-lg-10"
                        src="assets/media/misc/auth-screens.png" alt="" />
                    <!--end::Image-->
                    <div class="glass-effect">
                        <!--begin::Title-->
                        <h1 class="d-none d-lg-block text-white fs-2qx fw-bold text-center mb-7">
                            Dominus: Rápido, Eficiente e Produtivo
                        </h1>
                        <!--end::Title-->
                        <!--begin::Text-->
                        <div class="d-none d-lg-block text-white fs-base text-center px-10"
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
                    <a href="../../demo1/dist/index.html" class="mb-0 mb-lg-10">
                        <img alt="Logo" src="assets/media/logos/apple-touch-icon.svg" class="h-100px h-lg-100px" />
                    </a>
                    <!--begin::Wrapper-->
                    <div class="w-lg-500px p-10">
                        <!--begin::Form-->
                        <form class="form w-100" method="POST" action="{{ route('login') }}" id="kt_sign_in_form">
                            @csrf
                            <!--begin::Heading-->
                            <div class="text-center mb-10">
                                <!--begin::Title-->
                                <h1 class="text-dark fw-bolder mb-3">Entrar</h1>
                                <!--end::Title-->
                                <!--begin::Subtitle-->
                                <div class="text-gray-500 fw-semibold fs-6">Dominus: Rápido, Eficiente e Produtivo</div>
                                <!--end::Subtitle=-->
                            </div>
                            <!--begin::Heading-->
                            <!--begin::Alert para erros-->
                            <div id="kt_sign_in_alert" class="alert alert-dismissible d-none" role="alert">
                                <span id="kt_sign_in_alert_message"></span>
                            </div>
                            <!--end::Alert para erros-->
                            <!--begin::Input group=-->
                            <div class="fv-row mb-8">
                                <!--begin::Email-->
                                <input type="text" placeholder="Email" name="email" autocomplete="off"
                                    class="form-control form-control-sm bg-transparent" />
                                <div class="fv-plugins-message-container">
                                    <div class="fv-help-block">
                                        <span role="alert"></span>
                                    </div>
                                </div>
                                <!--end::Email-->
                            </div>
                            <!--end::Input group=-->
                            <div class="fv-row mb-3">
                                <!--begin::Password-->
                                <input type="password" placeholder="Password" name="password" autocomplete="off"
                                    class="form-control form-control-sm bg-transparent" />
                                <div class="fv-plugins-message-container">
                                    <div class="fv-help-block">
                                        <span role="alert"></span>
                                    </div>
                                </div>
                                <!--end::Password-->
                            </div>
                            <!--end::Input group=-->
                            <!--begin::Wrapper-->
                            <div class="d-flex flex-stack flex-wrap gap-3 fs-base fw-semibold mb-8">
                                <div></div>
                                <!--begin::Link-->
                                <a href="{{ route('password.request.admin') }}"
                                    class="link-primary">Esqueceu sua senha?</a>
                                <!--end::Link-->
                            </div>
                            <!--end::Wrapper-->
                            <!--begin::Submit button-->
                            <div class="d-grid mb-10">
                                <button type="submit" id="kt_sign_in_submit" class="btn btn-sm btn-primary">
                                    <!--begin::Indicator label-->
                                    <span class="indicator-label"><i class="fas fa-sign-in-alt"></i> Entrar</span>
                                    <!--end::Indicator label-->
                                    <!--begin::Indicator progress-->
                                    <span class="indicator-progress">Please wait...
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
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
    <script>
        "use strict";

        // Class definition
        var KTSigninGeneral = function() {
            // Elements
            var form;
            var submitButton;
            var validator;
            var alertElement;
            var alertMessageElement;

            // Handle form
            var handleValidation = function(e) {
                // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
                validator = FormValidation.formValidation(
                    form,
                    {
                        fields: {
                            'email': {
                                validators: {
                                    regexp: {
                                        regexp: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
                                        message: 'O valor não é um endereço de email válido',
                                    },
                                    notEmpty: {
                                        message: 'O endereço de email é obrigatório'
                                    }
                                }
                            },
                            'password': {
                                validators: {
                                    notEmpty: {
                                        message: 'A senha é obrigatória'
                                    }
                                }
                            }
                        },
                        plugins: {
                            trigger: new FormValidation.plugins.Trigger(),
                            bootstrap: new FormValidation.plugins.Bootstrap5({
                                rowSelector: '.fv-row',
                                eleInvalidClass: '',  // comment to enable invalid state icons
                                eleValidClass: '' // comment to enable valid state icons
                            })
                        }
                    }
                );
            }

            var showAlert = function(message, type) {
                alertElement.className = 'alert alert-dismissible alert-' + (type || 'danger');
                alertMessageElement.textContent = message;
                alertElement.classList.remove('d-none');

                // Auto hide after 5 seconds
                setTimeout(function() {
                    alertElement.classList.add('d-none');
                }, 5000);
            }

            var hideAlert = function() {
                alertElement.classList.add('d-none');
            }

            var handleSubmitAjax = function(e) {
                // Handle form submit
                form.addEventListener('submit', function (e) {
                    // Prevent button default action
                    e.preventDefault();

                    // Hide previous alerts
                    hideAlert();

                    // Validate form
                    validator.validate().then(function (status) {
                        if (status == 'Valid') {
                            // Show loading indication
                            submitButton.setAttribute('data-kt-indicator', 'on');

                            // Disable button to avoid multiple click
                            submitButton.disabled = true;

                            // Get form data
                            const email = form.querySelector('[name="email"]').value;
                            const password = form.querySelector('[name="password"]').value;
                            const token = form.querySelector('[name="_token"]').value;
                            const action = form.getAttribute('action');

                            // Use fetch API for AJAX request
                            fetch(action, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': token,
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    email: email,
                                    password: password,
                                    _token: token
                                })
                            })
                            .then(function(response) {
                                return response.json().then(function(data) {
                                    return {
                                        ok: response.ok,
                                        status: response.status,
                                        data: data
                                    };
                                });
                            })
                            .then(function(result) {
                                // Hide loading indication
                                submitButton.removeAttribute('data-kt-indicator');

                                // Enable button
                                submitButton.disabled = false;

                                if (result.ok && result.data) {
                                            // Clear form
                                            form.querySelector('[name="email"]').value = "";
                                            form.querySelector('[name="password"]').value = "";

                                            // Redirect
                                            if (result.data.redirect) {
                                                location.href = result.data.redirect;
                                            } else {
                                                location.href = "{{ route('dashboard') }}";
                                            }
                                } else {
                                    // Handle error response
                                    let errorMessage = "Desculpe, ocorreu um erro. Por favor, tente novamente.";

                                    if (result.data) {
                                        if (result.data.message) {
                                            errorMessage = result.data.message;
                                        } else if (result.data.errors) {
                                            // Validation errors
                                            const errors = result.data.errors;
                                            const firstError = Object.values(errors)[0];
                                            errorMessage = Array.isArray(firstError) ? firstError[0] : firstError;

                                            // Show field errors
                                            if (errors.email) {
                                                validator.updateFieldStatus('email', 'Invalid', {
                                                    message: errors.email[0]
                                                });
                                            }
                                            if (errors.password) {
                                                validator.updateFieldStatus('password', 'Invalid', {
                                                    message: errors.password[0]
                                                });
                                            }
                                        }
                                    }

                                    // Show error alert
                                    showAlert(errorMessage, 'danger');
                                }
                            })
                            .catch(function(error) {
                                // Hide loading indication
                                submitButton.removeAttribute('data-kt-indicator');

                                // Enable button
                                submitButton.disabled = false;

                                // Network error or other
                                showAlert("Desculpe, ocorreu um erro de conexão. Por favor, verifique sua internet e tente novamente.", 'danger');
                                console.error('Erro na requisição:', error);
                            });
                        } else {
                            // Show error popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                            Swal.fire({
                                text: "Por favor, corrija os erros no formulário antes de continuar.",
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, entendi!",
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                }
                            });
                        }
                    });
                });
            }

            // Public functions
            return {
                // Initialization
                init: function() {
                    form = document.querySelector('#kt_sign_in_form');
                    submitButton = document.querySelector('#kt_sign_in_submit');
                    alertElement = document.querySelector('#kt_sign_in_alert');
                    alertMessageElement = document.querySelector('#kt_sign_in_alert_message');

                    if (!form || !submitButton) {
                        return;
                    }

                    handleValidation();
                    handleSubmitAjax();
                }
            };
        }();

        // On document ready
        KTUtil.onDOMContentLoaded(function() {
            KTSigninGeneral.init();
        });
    </script>
    <!--end::Custom Javascript-->
    <!--end::Javascript-->
</body>
<!--end::Body-->

</html>
