{{-- Em resources/views/app/contabilidade/mapeamento/_table.blade.php --}}

<!--begin::Card-->
<div class="card card-flush">
    <!--begin::Card header-->
    <div class="card-header pt-8">
        <div class="card-title">
            <!--begin::Search-->
            <div class="d-flex align-items-center position-relative my-1">
                <span class="svg-icon svg-icon-1 position-absolute ms-6">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="currentColor" />
                        <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="currentColor" />
                    </svg>
                </span>
                <input type="text" data-kt-filter="search" class="form-control form-control-solid w-250px ps-15" placeholder="Pesquisar Mapeamentos..." />
            </div>
            <!--end::Search-->
        </div>
        <!--begin::Card toolbar-->
        <div class="card-toolbar">
            <!--begin::Toolbar-->
            <div class="d-flex justify-content-end">
                <!--begin::Add button-->
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_mapeamento">
                    Novo Mapeamento
                </button>
                <!--end::Add button-->
            </div>
            <!--end::Toolbar-->
        </div>
        <!--end::Card toolbar-->
    </div>
    <!--end::Card header-->
    <!--begin::Card body-->
    <div class="card-body">
        <!--begin::Table-->
        <table class="table align-middle table-row-dashed fs-6 gy-5">
            <thead>
              <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                <th class="min-w-250px">DE: Lançamento Padrão</th>
                <th class="min-w-250px">PARA: Conta Débito</th>
                <th class="min-w-250px">PARA: Conta Crédito</th>
                <th class="text-end min-w-100px">Ações</th>
              </tr>
            </thead>
            <tbody class="fw-semibold text-gray-600">
                @forelse ($mapeamentos as $mapeamento)
                    <tr>
                        <td>{{ $mapeamento->lancamentoPadrao->description ?? 'N/A' }}</td>
                        <td>
                            <span class="text-gray-800">{{ $mapeamento->contaDebito->name ?? 'N/A' }}</span>
                            <br>
                            <small class="text-muted">{{ $mapeamento->contaDebito->code ?? '' }}</small>
                        </td>
                        <td>
                            <span class="text-gray-800">{{ $mapeamento->contaCredito->name ?? 'N/A' }}</span>
                            <br>
                            <small class="text-muted">{{ $mapeamento->contaCredito->code ?? '' }}</small>
                        </td>
                        <td class="text-end">
                            <form action="{{ route('contabilidade.mapeamento.destroy', $mapeamento->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este mapeamento?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-icon btn-light-danger btn-sm">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-10">
                            Nenhum mapeamento contábil cadastrado.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <!--end::Table-->
    </div>
    <!--end::Card body-->
</div>
<!--end::Card-->
