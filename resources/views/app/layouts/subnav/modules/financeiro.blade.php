<!--begin::Page title-->
<div class="page-title d-flex flex-column flex-wrap me-3">
    <!--begin::Title-->
    <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column my-0">
        {{ $pageTitle ?? 'Financeiro' }}
    </h1>
    <!--end::Title-->
    <!--begin::Breadcrumb-->
    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
        <!--begin::Item-->
        <li class="breadcrumb-item text-muted">
            <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
        </li>
        <!--end::Item-->
        <!--begin::Item-->
        <li class="breadcrumb-item">
            <span class="bullet bg-gray-400 w-5px h-2px"></span>
        </li>
        <!--end::Item-->
        
        @if(!request()->routeIs('banco.list') && !request()->routeIs('caixa.list'))
        <!--begin::Item-->
        <li class="breadcrumb-item">
            <span class="bullet bg-gray-400 w-5px h-2px"></span>
        </li>
        <!--end::Item-->
        <!--begin::Item-->
        <li class="breadcrumb-item text-muted">{{ $pageTitle ?? 'Financeiro' }}</li>
        <!--end::Item-->
        @endif
    </ul>
    <!--end::Breadcrumb-->
</div>
<!--end::Page title-->

{{-- Include Modals --}}
@include('app.components.modals.financeiro.conciliacao.modal_conciliacao_bancaria')
@include('app.components.modals.financeiro.prestacao_contas.modal_prestacao_contas')
@include('app.components.modals.financeiro.boletim.modal_boletim_financeiro')
