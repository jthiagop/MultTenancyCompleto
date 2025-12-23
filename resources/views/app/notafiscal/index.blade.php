<x-tenant-app-layout>
    <!--begin::Main-->
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <!--begin::Content wrapper-->
        <div class="d-flex flex-column flex-column-fluid">
            <!--begin::Content container-->
            <div id="kt_app_content_container" class="app-container container-fluid">
                <!--begin::Content-->
                <div id="kt_app_content" class="app-content flex-column-fluid pt-7">
                    <!--begin::Container-->
                    <div class="app-container container-fluid">
                        <!--begin::Card-->
                        <div class="card">
                            <!--begin::Card headers-->
                            <div class="card-header border-0 pt-6">
                                <div class="card-title">
                                    <h3 class="fw-bold m-0">Certificado A1</h3>
                                </div>
                                <div class="card-toolbar">
                                    <button type="button" class="btn btn-sm btn-light-primary me-3" data-bs-toggle="modal" data-bs-target="#kt_modal_adicionar_conta_notafiscal">
                                        <i class="bi bi-plus-lg fs-2"></i>
                                        Adicionar Conta
                                    </button>
                                </div>
                            </div>
                            <!--end::Card header-->

                            <!--begin::Card body-->
                            <div class="card-body pt-0">
                                @if(isset($conta) && $conta)
                                    <!-- Exibição do Certificado -->
                                    <div class="d-flex flex-column gap-5">
                                        <!-- Linha 1: Arquivo e Ações -->
                                        <div class="row mb-5">
                                            <div class="col-md-3">
                                                <label class="form-label fw-bold text-gray-700">Certificado A1<span class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-md-9">
                                                <div class="d-flex align-items-center flex-wrap gap-2">
                                                    <span class="text-gray-800 fw-bold fs-6" title="{{ basename($conta->certificado_path) }}">
                                                        {{ $conta->certificado_nome ?? basename($conta->certificado_path) }}
                                                    </span>
                                                    
                                                    <span class="mx-2 text-gray-400">|</span>
                                                    
                                                    <a href="#" class="text-primary fw-bold fs-7 me-2" data-bs-toggle="modal" data-bs-target="#kt_modal_adicionar_conta_notafiscal">Mudar</a>
                                                    
                                                    <a href="{{ route('file', ['path' => $conta->certificado_path]) }}" target="_blank" class="text-primary fw-bold fs-7 d-flex align-items-center">
                                                        <i class="bi bi-download fs-7 me-1"></i> Baixar
                                                    </a>
                                                </div>
                                                <div class="text-gray-500 fs-7 mt-1">
                                                    Dias restantes: {{ $diasRestantes ?? 'Desconhecido' }}
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Linha 2: Senha -->
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label class="form-label fw-bold text-gray-700">Senha A1<span class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="position-relative">
                                                    <input type="password" class="form-control form-control-solid" value="{{ $senhaDescriptografada }}" readonly id="senha-a1-display" />
                                                    <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2" onclick="togglePasswordVisibility('senha-a1-display', this)">
                                                        <i class="bi bi-eye-slash fs-2"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                @else
                                    <!-- Empty State (Sem certificado) -->
                                    <div class="text-center py-10">
                                        <i class="ki-duotone ki-file fs-3x text-primary mb-5">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                            <span class="path4"></span>
                                            <span class="path5"></span>
                                        </i>
                                        <h3 class="fs-2 fw-bold text-gray-800 mb-2">Módulo de Nota Fiscal</h3>
                                        <p class="text-gray-500 fs-4 mb-5">
                                            Receber os arquivos XML organizados é o paraíso.
                                        </p>
                                        <p class="text-gray-600 fs-6">
                                            Use o botão acima para adicionar um certificado digital A1.
                                        </p>
                                    </div>
                                @endif
                            </div>
                            <!--end::Card body-->

                            @push('scripts')
                            <script>
                                function togglePasswordVisibility(inputId, iconElement) {
                                    const input = document.getElementById(inputId);
                                    const icon = iconElement.querySelector('i');
                                    
                                    if (input.type === 'password') {
                                        input.type = 'text';
                                        icon.classList.remove('bi-eye-slash');
                                        icon.classList.add('bi-eye');
                                    } else {
                                        input.type = 'password';
                                        icon.classList.remove('bi-eye');
                                        icon.classList.add('bi-eye-slash');
                                    }
                                }
                            </script>
                            @endpush
                    </div>
                    <!--end::Container-->
                </div>
                <!--end::Content-->
            </div>
            <!--end::Content container-->
        </div>
        <!--end::Content wrapper-->
    </div>
    <!--end::Main-->

    <!--begin::Modal - Adicionar Conta da Nota Fiscal-->
    @include('app.components.modals.notafiscal.adicionar_conta', [
        'cnpjMatriz' => $cnpjMatriz ?? null,
        'cnpjMatrizRaw' => $cnpjMatrizRaw ?? null
    ])
    <!--end::Modal - Adicionar Conta da Nota Fiscal-->


    <!--begin::Scripts - Adicionar Conta-->
    <script src="/assets/js/custom/apps/notafiscal/adicionar-conta.js"></script>
    <!--end::Scripts - Adicionar Conta-->

</x-tenant-app-layout>

