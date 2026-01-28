@props([
    'tableId' => '',
    'columns' => []
])

<!--begin::Skeleton Loading-->
<div id="skeleton-{{ $tableId }}" class="py-5 placeholder-glow">
    <div class="d-flex flex-stack mb-5">
        <div class="d-flex align-items-center position-relative my-1">
            <span class="svg-icon svg-icon-1 position-absolute ms-6"></span>
            <div class="placeholder rounded w-250px h-40px bg-light"></div>
        </div>
        <div class="d-flex justify-content-end gap-2">
            <div class="placeholder rounded w-100px h-40px bg-light"></div>
            <div class="placeholder rounded w-100px h-40px bg-light"></div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table align-middle table-row-dashed fs-6 gy-5">
            <thead>
                <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                    @foreach($columns as $column)
                        <th class="{{ $column['width'] ?? '' }}">
                            <div class="placeholder rounded w-100 h-20px bg-light"></div>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @for($i = 0; $i < 5; $i++)
                    <tr>
                        @foreach($columns as $column)
                            <td>
                                <div class="placeholder rounded w-100 h-20px bg-light"></div>
                            </td>
                        @endforeach
                    </tr>
                @endfor
            </tbody>
        </table>
    </div>
</div>
<!--end::Skeleton Loading-->
