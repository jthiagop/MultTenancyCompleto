@extends('app.auth.layouts.auth-layout')

@section('title', 'Redefinir Senha - ' . config('app.name', 'Dominus'))
@section('meta_description', 'Redefina sua senha de acesso ao sistema Dominus.')
@section('canonical_url', 'https://dominusbr.com/reset-password')

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
    <form class="form w-100" method="POST" action="{{ route('password.request.admin.store') }}"
        id="kt_request_password_form">
        @csrf
        <!--begin::Heading-->
        <div class="text-center mb-10">
            <!--begin::Title-->
            <h1 class="text-dark fw-bolder mb-3">Solicitar Nova Senha</h1>
            <!--end::Title-->
            <!--begin::Subtitle-->
            <div class="text-gray-500 fw-semibold fs-6">Informe seu email para solicitar uma nova senha ao administrador
            </div>
            <!--end::Subtitle=-->
        </div>
        <!--begin::Heading-->
        <!--begin::Alert para erros-->
        <div id="kt_request_password_alert" class="alert alert-dismissible d-none" role="alert">
            <span id="kt_request_password_alert_message"></span>
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
        <!--begin::Wrapper-->
        <div class="d-flex flex-stack flex-wrap gap-3 fs-base fw-semibold mb-8">
            <div></div>
            <!--begin::Link-->
            <a href="{{ route('login') }}" class="link-primary">Voltar para o login</a>
            <!--end::Link-->
        </div>
        <!--end::Wrapper-->
        <!--begin::Submit button-->
        <div class="d-grid mb-10">
            <button type="submit" id="kt_request_password_submit" class="btn btn-sm btn-primary">
                <!--begin::Indicator label-->
                <span class="indicator-label"><i class="fas fa-paper-plane"></i> Solicitar</span>
                <!--end::Indicator label-->
                <!--begin::Indicator progress-->
                <span class="indicator-progress">Enviando...
                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                <!--end::Indicator progress-->
            </button>
        </div>
        <!--end::Submit button-->

    </form>
    <!--end::Form-->
@endsection

		<!--begin::Custom Javascript(used for this page only)-->
		<script src="{{ url('assets/js/custom/authentication/reset-password/reset-password.js') }}"></script>
		<!--end::Custom Javascript-->
@endpush
