@props([
    'entidade',
    'showPercentage' => false,
])

<!--begin::Items-->
<div class="d-flex d-grid gap-5">
    <!--begin::Item-->
    <div class="d-flex flex-column flex-shrink-0 me-4">
        <!--begin::Info-->
        <div class="d-flex align-items-center">
            <!--begin::Currency-->
            <span class="fs-4 fw-semibold text-gray-400 me-1 align-self-start">R$</span>
            <!--end::Currency-->
            <!--begin::Amount-->
            <span class="fs-2hx fw-bold text-dark me-2 lh-1 ls-n2">
                {{ number_format($entidade->saldo_atual, 2, ',', '.') }}
            </span>
            <!--end::Amount-->
            @if($showPercentage)
                <!--begin::Badge-->
                <span class="badge badge-light-success fs-base">
                    <i class="bi bi-arrow-up"></i>
                    2.2%
                </span>
                <!--end::Badge-->
            @endif
        </div>
        <!--end::Info-->
    </div>
    <!--end::Item-->
</div>
<!--end::Items-->

