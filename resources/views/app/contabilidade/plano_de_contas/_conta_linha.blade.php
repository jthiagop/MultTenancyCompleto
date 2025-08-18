<!-- resources/views/app/contabilidade/plano_de_contas/_conta_linha.blade.php -->

<tr>
    <!--begin::Name-->
    <td>
        <div class="d-flex align-items-center" style="padding-left: {{ $level * 20 }}px;">
            @if (isset($allGroupedAccounts[$conta->id]))
                <i class="fa-solid fa-folder-open text-warning me-3"></i>
            @else
                <i class="fa-solid fa-file-lines text-gray-400 me-3"></i>
            @endif
            <span class="text-gray-800">{{ $conta->name }}</span>
        </div>
    </td>
    <!--end::Name-->

    <!--begin::Code-->
    <td>{{ $conta->code }}</td>
    <!--end::Code-->

    <!--begin::Type-->
    <td>
        <span class="badge {{ $conta->badge_class }}">{{ $conta->formatted_type }}</span>
    </td>
    <!--end::Type-->

    <!--begin::Actions-->
    <td class="text-end">
        {{-- O botão agora é um dropdown --}}
        <a href="#" class="btn btn-sm btn-light btn-active-light-primary" data-kt-menu-trigger="click"
            data-kt-menu-placement="bottom-end">
            Ações
            <span class="svg-icon svg-icon-5 m-0">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13583 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z"
                        fill="currentColor" />
                </svg>
            </span>
        </a>
        <!--begin::Menu-->
        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4"
            data-kt-menu="true">
            <!--begin::Menu item-->
            <div class="menu-item px-3">
                <a href="#" class="menu-link px-3 edit-btn" data-id="{{ $conta->id }}">
                    Editar
                </a>
            </div>
            <!--end::Menu item-->
            <!--begin::Menu item-->
            <div class="menu-item px-3">
                <a href="#" class="menu-link px-3 text-danger delete-btn" data-id="{{ $conta->id }}"
                    data-name="{{ $conta->name }}">
                    Excluir
                </a>
            </div>
            <!--end::Menu item-->
        </div>
        <!--end::Menu-->
    </td>
    <!--end::Actions-->
</tr>

{{-- A recursão continua a mesma --}}
@if (isset($allGroupedAccounts[$conta->id]))
    @foreach ($allGroupedAccounts[$conta->id] as $childAccount)
        @include('app.contabilidade.plano_de_contas._conta_linha', [
            'conta' => $childAccount,
            'allGroupedAccounts' => $allGroupedAccounts,
            'level' => $level + 1,
        ])
    @endforeach
@endif
