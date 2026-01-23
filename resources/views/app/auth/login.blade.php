@extends('app.auth.layouts.auth-layout')

@section('title', config('app.name', 'Dominus'))
@section('meta_description', 'No contexto da gestão eclesial, Dominus é um sistema que permite gerenciar de forma
    eficiente os campos de pastorais, patrimônio e financeiro. Com Dominus, a administração de sua paróquia se torna mais
    organizada e produtiva, facilitando a gestão de recursos e atividades eclesiais.')
@section('canonical_url', 'https://dominusbr.com/login')

@section('aside_content')
    <!--begin::Text-->
    <div class="d-none d-lg-block text-white fs-base text-center">
        @if (isset($randomImage) && $randomImage)
            <!--begin::Image Info - Discreto-->
            <div class="d-none d-lg-block position-absolute bottom-0 start-50 translate-middle-x mb-10 px-5 text-center"
                style="z-index: 2;">
                <div class="text-white fs-6 fw-bold mb-1" style="text-shadow: 0 2px 4px rgba(0,0,0,0.8);">
                    {{ $randomImage->descricao }}
                </div>
                <div class="text-white fs-7 fw-bold" style="text-shadow: 0 2px 4px rgba(0,0,0,0.8);">
                    <i class="fas fa-map-marker-alt text-white "></i> {{ $randomImage->localidade }}
                </div>
            </div>
            <!--end::Image Info-->
        @endif
    </div>
    <!--end::Text-->
@endsection

@section('form_content')
    <!--begin::Form-->
    <form class="form w-100" method="POST" action="{{ route('login') }}" id="kt_sign_in_form">
        @csrf
        <!--begin::Heading-->
        <div class="text-center mb-10">
            <!--begin::Title-->
            <h1 class="text-dark fw-bolder mb-3">Entrar</h1>
            <!--end::Title-->
            <!--begin::Subtitle-->
            <div class="text-gray-500 fw-semibold fs-6">Faça seu login e comece a usar o Dominus
            </div>
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
            <a href="{{ route('password.request.admin') }}" class="link-primary">Esqueceu sua
                senha?</a>
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
                <span class="indicator-progress">Por favor, aguarde...
                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                <!--end::Indicator progress-->
            </button>
        </div>
        <!--end::Submit button-->

    </form>
    <!--end::Form-->
@endsection

@push('scripts')
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
                    form, {
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
                                eleInvalidClass: '', // comment to enable invalid state icons
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
                form.addEventListener('submit', function(e) {
                    // Prevent button default action
                    e.preventDefault();

                    // Hide previous alerts
                    hideAlert();

                    // Validate form
                    validator.validate().then(function(status) {
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
                                        let errorMessage =
                                            "Desculpe, ocorreu um erro. Por favor, tente novamente.";

                                        if (result.data) {
                                            if (result.data.message) {
                                                errorMessage = result.data.message;
                                            } else if (result.data.errors) {
                                                // Validation errors
                                                const errors = result.data.errors;
                                                const firstError = Object.values(errors)[0];
                                                errorMessage = Array.isArray(firstError) ?
                                                    firstError[0] : firstError;

                                                // Show field errors
                                                if (errors.email) {
                                                    validator.updateFieldStatus('email',
                                                        'Invalid', {
                                                            message: errors.email[0]
                                                        });
                                                }
                                                if (errors.password) {
                                                    validator.updateFieldStatus('password',
                                                        'Invalid', {
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
                                    showAlert(
                                        "Desculpe, ocorreu um erro de conexão. Por favor, verifique sua internet e tente novamente.",
                                        'danger');
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
@endpush
