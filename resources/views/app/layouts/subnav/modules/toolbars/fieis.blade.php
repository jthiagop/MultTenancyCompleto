<!--begin::Page title-->
<div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
    <!--begin::Title-->
    <h1 class="page-heading d-flex text-gray-800 fw-bold fs-3 flex-column justify-content-center my-0 dark:text-white">
        Cadastro de Fiéis</h1>
    <!--end::Title-->
    <!--begin::Breadcrumb-->
    <ul class="breadcrumb breadcrumb-separatorless fw-bold fs-7 my-0 pt-1">
        <!--begin::Item-->
        <li class="breadcrumb-item text-muted dark:text-gray-400">
            <a href="{{ route('dashboard') }}"
                class="text-muted text-hover-primary dark:text-gray-400 dark:text-hover-white">Dashboard</a>
        </li>
        <!--end::Item-->
        <!--begin::Item-->
        <li class="breadcrumb-item">
            <span class="bullet bg-gray-400 w-5px h-2px dark:bg-gray-600"></span>
        </li>
        <!--end::Item-->
        <!--begin::Item-->
        <li class="breadcrumb-item text-muted dark:text-gray-400">Cadastro de Fiéis</li>
        <!--end::Item-->
    </ul>
    <!--end::Breadcrumb-->
</div>
<!--end::Page title-->
<!--begin::Actions-->
<div class="d-flex align-items-center gap-2 gap-lg-3">
    <!--begin::Relatório-->
    <button type="button" class="btn btn-sm btn-light-primary me-3" data-bs-toggle="modal"
        data-bs-target="#kt_fieis_export_modal">
        <!--begin::Svg Icon | path: icons/duotune/arrows/arr078.svg-->
        <span class="svg-icon svg-icon-2">
            <i class="fa-solid fa-file-export"></i>
        </span>
        <!--end::Svg Icon-->Relatório
    </button>
    <!--end::Relatório-->
    <!--begin::Add user-->
    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_new_ticket">
        <!--begin::Svg Icon | path: icons/duotune/arrows/arr075.svg-->
        <span class="svg-icon svg-icon-2">
            <i class="fa-solid fa-user-plus"></i> Novo Fiél
        </span>
        <!--end::Svg Icon-->
    </button>
    <!--end::Add user-->
</div>
<!--end::Actions-->
