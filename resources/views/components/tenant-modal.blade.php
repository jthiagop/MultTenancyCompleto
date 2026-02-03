@props([
    'id' => 'kt_modal',
    'title' => 'Modal',
    'size' => 'mw-650px',
    'footerAlign' => 'center',
    'static' => true
])

<!--begin::Modal-->
<div class="modal fade" @if($static) data-bs-backdrop="static" @endif id="{{ $id }}" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered {{ $size }}">
        <!--begin::Modal content-->
        <div class="modal-content">
            <!--begin::Modal header-->
            <div class="modal-header">
                <!--begin::Modal title-->
                @if(isset($header))
                    {{ $header }}
                @else
                    <h2 class="fw-bold">{{ $title }}</h2>
                @endif
                <!--end::Modal title-->
                <!--begin::Close-->
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <span class="svg-icon svg-icon-1">
                        <i class="fa-solid fa-xmark fs-3"></i>
                    </span>
                </div>
                <!--end::Close-->
            </div>
            <!--end::Modal header-->
            <!--begin::Modal body-->
            <div class="modal-body">
                {{ $slot }}
            </div>
            <!--end::Modal body-->
            <!--begin::Modal footer-->
            @if(isset($footer))
                <div class="modal-footer flex-{{ $footerAlign }}">
                    {{ $footer }}
                </div>
            @endif
            <!--end::Modal footer-->
        </div>
        <!--end::Modal content-->
    </div>
    <!--end::Modal dialog-->
</div>
<!--end::Modal-->
