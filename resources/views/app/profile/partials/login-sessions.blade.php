<!--begin::Card-->
<div class="card pt-4 mb-6 mb-xl-9">
    <!--begin::Card header-->
    <div class="card-header border-0">
        <div class="card-title">
            <h2>Login de Acessos</h2>
        </div>
        <div class="card-toolbar">
            <button type="button" class="btn btn-sm btn-flex btn-light-primary" id="kt_modal_sign_out_sessions">
                <!--begin::Svg Icon | path: icons/duotune/arrows/arr077.svg-->
                <span class="svg-icon svg-icon-3">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <rect opacity="0.3" x="4" y="11" width="12" height="2" rx="1"
                            fill="currentColor" />
                        <path
                            d="M5.86875 11.6927L7.62435 10.2297C8.09457 9.83785 8.12683 9.12683 7.69401 8.69401C7.3043 8.3043 6.67836 8.28591 6.26643 8.65206L3.34084 11.2526C2.89332 11.6504 2.89332 12.3496 3.34084 12.7474L6.26643 15.3479C6.67836 15.7141 7.3043 15.6957 7.69401 15.306C8.12683 14.8732 8.09458 14.1621 7.62435 13.7703L5.86875 12.3073C5.67684 12.1474 5.67684 11.8526 5.86875 11.6927Z"
                            fill="currentColor" />
                        <path
                            d="M8 5V6C8 6.55228 8.44772 7 9 7C9.55228 7 10 6.55228 10 6C10 5.44772 10.4477 5 11 5H18C18.5523 5 19 5.44772 19 6V18C19 18.5523 18.5523 19 18 19H11C10.4477 19 10 18.5523 10 18C10 17.4477 9.55228 17 9 17C8.44772 17 8 17.4477 8 18V19C8 20.1046 8.89543 21 10 21H19C20.1046 21 21 20.1046 21 19V5C21 3.89543 20.1046 3 19 3H10C8.89543 3 8 3.89543 8 5Z"
                            fill="currentColor" />
                    </svg>
                </span>
                <!--end::Svg Icon-->
                Sair de todas as sessões</button>
            </button>
        </div>
    </div>
    <!--end::Card header-->
    <!--begin::Card body-->
    <div class="card-body pt-0 pb-5">
        <!--begin::Table wrapper-->
        <div class="table-responsive">
            <!--begin::Table-->
            <table class="table align-middle table-row-dashed gy-5" id="kt_table_users_login_session">
                <!--begin::Table head-->
                <thead class="border-bottom border-gray-200 fs-7 fw-bold">
                    <tr class="text-start text-muted text-uppercase gs-0">
                        <th class="min-w-100px">Local</th>
                        <th>Dispositivo</th>
                        <th>Endereço IP</th>
                        <th class="min-w-125px">Última Atividade</th>
                        <th class="min-w-70px text-end">Ações</th>
                    </tr>
                </thead>
                <!--end::Table head-->
                <!--begin::Table body-->
                <tbody class="fs-6 fw-semibold text-gray-600">
                    {{-- O loop dinâmico começa aqui --}}
                    @forelse ($loginSessions as $session)
                        <tr>
                            <td>Brasil</td> {{-- A localização ainda é estática, podemos melhorar depois --}}
                            <td>
                                {{ $session->agent['browser'] }} - {{ $session->agent['platform'] }}
                            </td>
                            <td>{{ $session->ip_address }}</td>
                            <td>{{ $session->last_active }}</td>
                            <td class="text-end">
                                @if ($session->is_current_device)
                                    <span class="badge badge-light-success">Sessão Atual</span>
                                @else
                                    {{-- O link de "Sign out" agora é um formulário seguro --}}
                                    <form method="POST" action="{{ route('profile.sessions.destroy', $session->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-link text-danger p-0">Sair</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">Nenhuma outra sessão ativa encontrada.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <!--end::Table body-->
            </table>
            <!--end::Table-->
        </div>
        <!--end::Table wrapper-->
    </div>
    <!--end::Card body-->
</div>
<!--end::Card-->
