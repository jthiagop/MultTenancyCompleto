@props([
    'name' => 'avatar',
    'label' => 'Fotografia',
    'size' => '125px',
    'currentImage' => null,
    'hint' => 'Tipos: png, jpg, jpeg.',
    'accept' => '.png, .jpg, .jpeg',
    'inputId' => null,
])

@php
    $uniqueId = $inputId ?? 'photo_input_' . uniqid();
    $hasImage = !empty($currentImage);
@endphp

<div class="fv-row">
    <!--begin::Label-->
    @if($label)
        <label class="d-block fw-semibold fs-6 mb-5">{{ $label }}</label>
    @endif
    <!--end::Label-->

    <!--begin::Image placeholder style for dark mode-->
    <style>
        .image-input-placeholder-{{ $uniqueId }} {
            background-image: url('/tenancy/assets/media/svg/files/blank-image.svg');
        }
        [data-bs-theme="dark"] .image-input-placeholder-{{ $uniqueId }} {
            background-image: url('/tenancy/assets/media/svg/files/blank-image-dark.svg');
        }
    </style>
    <!--end::Image placeholder style-->

    <!--begin::Image input-->
    <div id="{{ $uniqueId }}" 
         class="image-input image-input-outline image-input-placeholder-{{ $uniqueId }} {{ $hasImage ? 'image-input-changed' : 'image-input-empty' }}"
         data-kt-image-input="true">
        
        <!--begin::Preview existing avatar-->
        <div class="image-input-wrapper w-125px h-125px" 
             style="{{ $hasImage ? 'background-image: url(' . $currentImage . ');' : '' }}">
        </div>
        <!--end::Preview existing avatar-->

        <!--begin::Edit button-->
        <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
               data-kt-image-input-action="change" 
               data-bs-toggle="tooltip" 
               title="Alterar foto">
            <i class="bi bi-pencil-fill fs-7"></i>
            <!--begin::Inputs-->
            <input type="file" name="{{ $name }}" accept="{{ $accept }}" />
            <input type="hidden" name="{{ $name }}_remove" />
            <!--end::Inputs-->
        </label>
        <!--end::Edit button-->

        <!--begin::Cancel button-->
        <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
              data-kt-image-input-action="cancel" 
              data-bs-toggle="tooltip" 
              title="Cancelar">
            <i class="bi bi-x fs-2"></i>
        </span>
        <!--end::Cancel button-->

        <!--begin::Remove button-->
        <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
              data-kt-image-input-action="remove" 
              data-bs-toggle="tooltip" 
              title="Remover foto">
            <i class="bi bi-x fs-2"></i>
        </span>
        <!--end::Remove button-->
    </div>
    <!--end::Image input-->

    <!--begin::Hint-->
    @if($hint)
        <div class="form-text">{{ $hint }}</div>
    @endif
    <!--end::Hint-->
</div>
