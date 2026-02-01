<x-tenant-app-layout pageTitle="{{ $member->name }}" :breadcrumbs="[['label' => 'Secretaria', 'url' => route('secretary.index')], ['label' => $member->name]]">

    <!--begin::Content wrapper-->
    <div class="d-flex flex-column flex-column-fluid py-3 py-lg-6">

        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid">

            <!--begin::Content container-->
            <div id="kt_app_content_container" class="app-container container-fluid">
                
                <!--begin::Toolbar-->
                <div class="d-flex justify-content-between align-items-center mb-6">
                    <div>
                        <a href="{{ route('secretary.index') }}" class="btn btn-sm btn-light-primary">
                            <i class="fa-solid fa-arrow-left me-2"></i>Voltar
                        </a>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-light-warning" data-action="edit" data-id="{{ $member->id }}">
                            <i class="fa-regular fa-pen-to-square me-2"></i>Editar
                        </button>
                        <button type="button" class="btn btn-sm btn-light-danger" data-action="delete" data-id="{{ $member->id }}">
                            <i class="fa-regular fa-trash-can me-2"></i>Excluir
                        </button>
                    </div>
                </div>
                <!--end::Toolbar-->

                <div class="row g-5 g-xl-8">
                    
                    <!--begin::Col - Perfil-->
                    <div class="col-xl-4">
                        <!--begin::Card Perfil-->
                        <div class="card mb-5 mb-xl-8">
                            <div class="card-body pt-15">
                                <!--begin::Summary-->
                                <div class="d-flex flex-center flex-column mb-5">
                                    <!--begin::Avatar-->
                                    <div class="symbol symbol-150px symbol-circle mb-7">
                                        @if($member->avatar)
                                            <img src="{{ route('file', ['path' => $member->avatar]) }}" alt="{{ $member->name }}" />
                                        @else
                                            <div class="symbol-label fs-1 bg-light-primary text-primary fw-bold">
                                                {{ strtoupper(substr($member->name, 0, 2)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <!--end::Avatar-->
                                    
                                    <!--begin::Name-->
                                    <h3 class="fs-2 text-gray-800 fw-bold mb-1">{{ $member->name }}</h3>
                                    <!--end::Name-->
                                    
                                    <!--begin::Badges-->
                                    <div class="d-flex flex-wrap justify-content-center gap-2 mb-3">
                                        @if($member->role)
                                            @php
                                                $roleVariants = [
                                                    'presbitero' => 'success',
                                                    'diacono' => 'warning', 
                                                    'irmao' => 'primary'
                                                ];
                                                $variant = $roleVariants[$member->role->slug] ?? 'secondary';
                                            @endphp
                                            <span class="badge badge-lg badge-light-{{ $variant }}">{{ $member->role->name }}</span>
                                        @endif
                                        
                                        @if($member->currentStage)
                                            <span class="badge badge-lg badge-light-info">{{ $member->currentStage->name }}</span>
                                        @endif
                                        
                                        @if($member->is_active)
                                            <span class="badge badge-lg badge-light-success">Ativo</span>
                                        @else
                                            <span class="badge badge-lg badge-light-danger">Inativo</span>
                                        @endif
                                    </div>
                                    <!--end::Badges-->
                                </div>
                                <!--end::Summary-->
                                
                                <!--begin::Details-->
                                <div class="d-flex flex-stack fs-4 py-3">
                                    <div class="fw-bold">Informações</div>
                                </div>
                                
                                <div class="separator separator-dashed my-3"></div>
                                
                                <!--begin::Details item-->
                                <div class="d-flex flex-stack py-2">
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-30px me-3">
                                            <span class="symbol-label bg-light-primary">
                                                <i class="fa-solid fa-calendar text-primary"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500 fs-7">Data de Nascimento</span>
                                            <div class="text-gray-800 fw-semibold">
                                                {{ $member->birth_date?->format('d/m/Y') ?? '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Details item-->
                                
                                @if($member->cpf)
                                <div class="d-flex flex-stack py-2">
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-30px me-3">
                                            <span class="symbol-label bg-light-info">
                                                <i class="fa-solid fa-id-card text-info"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500 fs-7">CPF</span>
                                            <div class="text-gray-800 fw-semibold">{{ $member->cpf }}</div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                
                                @if($member->province)
                                <div class="d-flex flex-stack py-2">
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-30px me-3">
                                            <span class="symbol-label bg-light-warning">
                                                <i class="fa-solid fa-map-marker-alt text-warning"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500 fs-7">Província</span>
                                            <div class="text-gray-800 fw-semibold">{{ $member->province->name }}</div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                
                                @if($member->order_registration_number)
                                <div class="d-flex flex-stack py-2">
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-30px me-3">
                                            <span class="symbol-label bg-light-success">
                                                <i class="fa-solid fa-hashtag text-success"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500 fs-7">Registro na Ordem</span>
                                            <div class="text-gray-800 fw-semibold">{{ $member->order_registration_number }}</div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                <!--end::Details-->
                            </div>
                        </div>
                        <!--end::Card Perfil-->
                        
                        <!--begin::Card Endereço de Origem-->
                        @if($originAddress)
                        <div class="card mb-5 mb-xl-8">
                            <div class="card-header border-0">
                                <h3 class="card-title fw-bold text-gray-800">
                                    <i class="fa-solid fa-home me-2 text-primary"></i>
                                    Endereço de Origem
                                </h3>
                            </div>
                            <div class="card-body pt-0">
                                <div class="fs-6">
                                    @if($originAddress->rua)
                                        <div class="mb-2">
                                            <span class="text-gray-800 fw-semibold">{{ $originAddress->rua }}@if($originAddress->numero), {{ $originAddress->numero }}@endif</span>
                                        </div>
                                    @endif
                                    @if($originAddress->bairro)
                                        <div class="mb-2 text-gray-600">{{ $originAddress->bairro }}</div>
                                    @endif
                                    @if($originAddress->cidade || $originAddress->uf)
                                        <div class="mb-2 text-gray-600">
                                            {{ $originAddress->cidade }}@if($originAddress->cidade && $originAddress->uf) - @endif{{ $originAddress->uf }}
                                        </div>
                                    @endif
                                    @if($originAddress->cep)
                                        <div class="text-gray-500">CEP: {{ $originAddress->cep }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif
                        <!--end::Card Endereço de Origem-->
                    </div>
                    <!--end::Col - Perfil-->
                    
                    <!--begin::Col - Detalhes-->
                    <div class="col-xl-8">
                        
                        <!--begin::Card Etapas de Formação-->
                        <div class="card mb-5 mb-xl-8">
                            <div class="card-header border-0">
                                <h3 class="card-title fw-bold text-gray-800">
                                    <i class="fa-solid fa-graduation-cap me-2 text-primary"></i>
                                    Histórico de Formação
                                </h3>
                            </div>
                            <div class="card-body pt-0">
                                @if($member->formationPeriods->count() > 0)
                                    <!--begin::Timeline-->
                                    <div class="timeline">
                                        @foreach($member->formationPeriods as $period)
                                            <!--begin::Timeline item-->
                                            <div class="timeline-item">
                                                <!--begin::Timeline line-->
                                                <div class="timeline-line w-40px"></div>
                                                <!--end::Timeline line-->
                                                
                                                <!--begin::Timeline icon-->
                                                <div class="timeline-icon symbol symbol-circle symbol-40px">
                                                    <div class="symbol-label bg-light-{{ $period->is_current ? 'success' : 'primary' }}">
                                                        <i class="fa-solid fa-{{ $period->is_current ? 'star' : 'check' }} text-{{ $period->is_current ? 'success' : 'primary' }}"></i>
                                                    </div>
                                                </div>
                                                <!--end::Timeline icon-->
                                                
                                                <!--begin::Timeline content-->
                                                <div class="timeline-content mb-10 mt-n1">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <div>
                                                            <span class="fs-5 fw-bold text-gray-800">
                                                                {{ $period->formationStage?->name ?? 'Etapa não definida' }}
                                                            </span>
                                                            @if($period->is_current)
                                                                <span class="badge badge-light-success ms-2">Atual</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="d-flex flex-wrap gap-4 fs-7 text-gray-600">
                                                        <div>
                                                            <i class="fa-regular fa-calendar me-1"></i>
                                                            {{ $period->start_date?->format('d/m/Y') ?? '-' }}
                                                            @if($period->end_date)
                                                                até {{ $period->end_date->format('d/m/Y') }}
                                                            @elseif($period->is_current)
                                                                - presente
                                                            @endif
                                                        </div>
                                                        
                                                        @if($period->company)
                                                            <div>
                                                                <i class="fa-solid fa-building me-1"></i>
                                                                {{ $period->company->name }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <!--end::Timeline content-->
                                            </div>
                                            <!--end::Timeline item-->
                                        @endforeach
                                    </div>
                                    <!--end::Timeline-->
                                @else
                                    <div class="text-center text-gray-500 py-10">
                                        <i class="fa-solid fa-folder-open fs-2x mb-3 d-block"></i>
                                        Nenhum período de formação registrado
                                    </div>
                                @endif
                            </div>
                        </div>
                        <!--end::Card Etapas de Formação-->
                        
                        <!--begin::Card Observações-->
                        @if($member->observacoes || $member->notes)
                        <div class="card mb-5 mb-xl-8">
                            <div class="card-header border-0">
                                <h3 class="card-title fw-bold text-gray-800">
                                    <i class="fa-solid fa-sticky-note me-2 text-primary"></i>
                                    Observações
                                </h3>
                            </div>
                            <div class="card-body pt-0">
                                <p class="text-gray-700 mb-0">{{ $member->observacoes ?? $member->notes }}</p>
                            </div>
                        </div>
                        @endif
                        <!--end::Card Observações-->
                        
                    </div>
                    <!--end::Col - Detalhes-->
                    
                </div>

            </div>
            <!--end::Content container-->
            
        </div>
        <!--end::Content-->
    </div>
    <!--end::Content wrapper-->
    
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Ações de editar e excluir
            document.querySelectorAll('[data-action]').forEach(element => {
                element.addEventListener('click', function(e) {
                    e.preventDefault();
                    const action = this.dataset.action;
                    const memberId = this.dataset.id;
                    
                    if (action === 'edit') {
                        window.location.href = '{{ route("secretary.index") }}?edit=' + memberId;
                    } else if (action === 'delete') {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'Confirmar exclusão',
                                text: 'Deseja realmente excluir este membro?',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#d33',
                                cancelButtonColor: '#3085d6',
                                confirmButtonText: 'Sim, excluir',
                                cancelButtonText: 'Cancelar'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    fetch('{{ route("secretary.destroy", $member->id) }}', {
                                        method: 'DELETE',
                                        headers: {
                                            'Accept': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        }
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            Swal.fire('Excluído!', data.message, 'success').then(() => {
                                                window.location.href = '{{ route("secretary.index") }}';
                                            });
                                        } else {
                                            Swal.fire('Erro!', data.message, 'error');
                                        }
                                    });
                                }
                            });
                        } else if (confirm('Deseja realmente excluir este membro?')) {
                            fetch('{{ route("secretary.destroy", $member->id) }}', {
                                method: 'DELETE',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    window.location.href = '{{ route("secretary.index") }}';
                                }
                            });
                        }
                    }
                });
            });
        });
    </script>
    @endpush

</x-tenant-app-layout>
