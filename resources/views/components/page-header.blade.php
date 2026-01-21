@props(['title', 'breadcrumbs' => []])

<div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
    <!--begin::Title-->
    <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
        {{ $title }}
    </h1>
    <!--end::Title-->

    @if(!empty($breadcrumbs))
        <!--begin::Breadcrumb-->
        <ol class="breadcrumb breadcrumb-dot text-muted fs-8 fw-semibold my-0 pt-2 pt-lg-2">
            <!--begin::Item - Home-->
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Home</a>
            </li>
            <!--end::Item-->

            @foreach($breadcrumbs as $index => $breadcrumb)
                @if(is_array($breadcrumb))
                    <!--begin::Item-->
                    <li class="breadcrumb-item">
                        @if(isset($breadcrumb['url']))
                            <a href="{{ $breadcrumb['url'] }}" class="text-muted text-hover-primary">
                                {{ $breadcrumb['label'] }}
                            </a>
                        @else
                            <span class="text-muted">{{ $breadcrumb['label'] }}</span>
                        @endif
                    </li>
                    <!--end::Item-->
                @else
                    <!--begin::Item-->
                    <li class="breadcrumb-item ">
                        <span class="text-muted">{{ $breadcrumb }}</span>
                    </li>
                    <!--end::Item-->
                @endif
            @endforeach
        </ol>
        <!--end::Breadcrumb-->
    @endif
</div>
