@props(['bens', 'tipo', 'titulo' => 'Lista de Bens'])

<!--begin::Table-->
<div class="card card-flush mt-6 mt-xl-9">
    <!--begin::Card header-->
    <div class="card-header mt-5">
        <!--begin::Card title-->
        <div class="card-title flex-column">
            <h3 class="fw-bold mb-1">{{ $titulo }}</h3>
        </div>
        <!--begin::Card title-->
        <!--begin::Card toolbar-->
        <div class="card-toolbar my-1">
            <!--begin::Search-->
            <div class="d-flex align-items-center position-relative me-4">
                <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                <span class="svg-icon svg-icon-3 position-absolute ms-3">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546"
                            height="2" rx="1" transform="rotate(45 17.0365 15.1223)"
                            fill="currentColor" />
                        <path
                            d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z"
                            fill="currentColor" />
                    </svg>
                </span>
                <!--end::Svg Icon-->
                <input type="text" id="kt_filter_search_{{ $tipo }}"
                    class="form-control form-control-solid form-select-sm w-150px ps-9"
                    placeholder="Pesquisar..." />
            </div>
            <!--end::Search-->
        </div>
        <!--begin::Card toolbar-->
    </div>
    <!--end::Card header-->
    <!--begin::Card body-->
    <div class="card-body pt-0">
        <!--begin::Table container-->
        <div class="table-responsive">
            <!--begin::Table-->
            <table id="kt_table_bens_{{ $tipo }}"
                class="table table-row-bordered table-row-dashed gy-4 align-middle fw-bold">
                <!--begin::Head-->
                <thead class="fs-7 text-gray-400 text-uppercase">
                    <tr>
                        <th class="min-w-100px">Descrição</th>
                        @if($tipo == 'veiculo')
                            <th class="min-w-100px">Placa</th>
                            <th class="min-w-100px">Ano</th>
                            <th class="min-w-100px">Combustível</th>
                        @elseif($tipo == 'imovel')
                            <th class="min-w-100px">Endereço</th>
                            <th class="min-w-100px">Cidade</th>
                            <th class="min-w-100px">Área Total</th>
                        @elseif($tipo == 'movel')
                            <th class="min-w-100px">Marca/Modelo</th>
                            <th class="min-w-100px">Chapa/Plaqueta</th>
                        @endif
                        <th class="min-w-100px">Valor</th>
                        <th class="min-w-100px">Data Aquisição</th>
                        <th class="min-w-100px">Estado</th>
                        <th class="min-w-50px text-end">Ações</th>
                    </tr>
                </thead>
                <!--end::Head-->
                <!--begin::Body-->
                <tbody class="fs-6">
                    @forelse ($bens as $bem)
                        <tr id="bem-{{ $bem->id }}">
                            <td>
                                <!--begin::User-->
                                <div class="d-flex align-items-center">
                                    <!--begin::Info-->
                                    <div class="d-flex flex-column justify-content-center">
                                        <a href="{{ route('bem.show', $bem->id) }}"
                                            class="fs-6 text-gray-800 text-hover-primary">
                                            {{ $bem->descricao }}
                                        </a>
                                        <div class="fw-semibold text-gray-400">
                                            {{ $bem->centro_custo ?? 'N/A' }}
                                        </div>
                                    </div>
                                    <!--end::Info-->
                                </div>
                                <!--end::User-->
                            </td>
                            @if($tipo == 'veiculo')
                                <td>
                                    <span class="badge badge-light-primary">{{ $bem->veiculo?->placa ?? 'N/A' }}</span>
                                </td>
                                <td>{{ $bem->veiculo?->ano_modelo ?? 'N/A' }}</td>
                                <td>{{ $bem->veiculo?->combustivel ?? 'N/A' }}</td>
                            @elseif($tipo == 'imovel')
                                <td>{{ $bem->imovel?->endereco ?? 'N/A' }}</td>
                                <td>{{ $bem->imovel?->cidade ?? 'N/A' }}</td>
                                <td>{{ $bem->imovel?->area_total ? number_format($bem->imovel->area_total, 2, ',', '.') . ' m²' : 'N/A' }}</td>
                            @elseif($tipo == 'movel')
                                <td>{{ $bem->bemMovel?->marca_modelo ?? 'N/A' }}</td>
                                <td>{{ $bem->bemMovel?->chapa_plaqueta ?? 'N/A' }}</td>
                            @endif
                            <td>
                                <span class="fw-bold">R$ {{ number_format($bem->valor, 2, ',', '.') }}</span>
                            </td>
                            <td>
                                {{ $bem->data_aquisicao ? \Carbon\Carbon::parse($bem->data_aquisicao)->format('d/m/Y') : 'N/A' }}
                            </td>
                            <td>
                                @php
                                    $badgeClass = match($bem->estado_bem) {
                                        'Novo' => 'badge-light-success',
                                        'Bom' => 'badge-light-primary',
                                        'Ruim' => 'badge-light-danger',
                                        default => 'badge-light-secondary'
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }} fw-bold px-4 py-3">
                                    {{ $bem->estado_bem ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <!-- Botão de visualização -->
                                <a href="{{ route('bem.show', $bem->id) }}"
                                    class="btn btn-light btn-sm me-2">
                                    <i class="ki-solid ki-eye"></i>
                                </a>
                                <!-- Botão de edição -->
                                <a href="{{ route('bem.edit', $bem->id) }}"
                                    class="btn btn-light btn-sm me-2">
                                    <i class="ki-solid ki-pencil"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $tipo == 'veiculo' ? 8 : ($tipo == 'imovel' ? 8 : 7) }}" class="text-center py-10">
                                <div class="text-muted">Nenhum bem encontrado</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <!--end::Body-->
            </table>
            <!--end::Table-->
        </div>
        <!--end::Table container-->
        <!--begin::Pagination-->
        @if($bens->hasPages())
            <div class="d-flex justify-content-end mt-5">
                {{ $bens->links() }}
            </div>
        @endif
        <!--end::Pagination-->
    </div>
    <!--end::Card body-->
</div>
<!--end::Card-->

