<!--begin:::Tab pane - Pendentes-->
<div class="tab-pane fade {{ ($activeTab ?? 'pendentes') === 'pendentes' ? 'active show' : '' }}"
    id="kt_customer_view_overview_events_and_logs_tab" role="tabpanel">
    <!--begin::Layout-->
    <div class="d-flex flex-column flex-lg-row">

        <!--begin::Sidebar-->
        <div class="flex-column flex-lg-row-auto w-100 w-xl-350px mb-10">
            <!--begin::Card-->
            <div class="card mb-5 mb-xl-8">
                <!--begin::Card body-->
                <div class="card-body">
                    <!--begin::Summary-->
                    <div class="d-flex flex-center flex-column ">
                        <!--begin::Upload zone-->
                        @include('app.financeiro.domusia.partials.components.upload-zone')
                        <!--end::Upload zone-->
                        <!--begin::Info-->
                        <div class="d-flex flex-wrap flex-center mb-3">
                            <!--begin::Filter-->
                            @include('app.financeiro.domusia.partials.components.document-filter')
                            <!--end::Filter-->
                        </div>
                        <!--end::Info-->
                    </div>
                    <!--end::Summary-->
                    <!--begin::Details toggle-->
                    <div class="d-flex flex-stack fs-4 py-3">
                        <div class="fw-bold rotate collapsible" data-bs-toggle="collapse"
                            href="#kt_customer_view_details" role="button" aria-expanded="false"
                            aria-controls="kt_customer_view_details"> <span class="pulse pulse--warning pulse--sm me-2" id="pulsePendingDocuments" style="display: none;"></span> Pendentes
                            <span class="ms-2 rotate-180">
                                <!--begin::Svg Icon | path: icons/duotune/arrows/arr072.svg-->
                                <span class="svg-icon svg-icon-3">
                                    <i class="bi bi-chevron-up"></i>
                                </span>
                                <!--end::Svg Icon-->
                            </span>
                        </div>
                        <span class="badge badge-light-primary" id="documentosCountBadge">0</span>
                    </div>
                    <!--end::Details toggle-->
                    <div class="separator separator-dashed my-3"></div>
                    <!--begin::Details content-->
                    <div id="kt_customer_view_details" class="collapse show">
                        <div class="py-5 fs-6">
                            <!--begin::Document list-->
                            @include('app.financeiro.domusia.partials.components.document-list')
                            <!--end::Document list-->
                        </div>
                    </div>
                    <!--end::Details content-->
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Card-->
        </div>
        <!--end::Sidebar-->

        <!--begin::Content-->
        <div class="flex-lg-row-fluid ms-lg-5 d-flex flex-column gap-5">

            <!--begin::Document Viewer Section-->
            @include('app.financeiro.domusia.partials.components.document-viewer')
            <!--end::Document Viewer Section-->

            <!--begin::Extracted Data Section-->
            @include('app.financeiro.domusia.partials.components.extracted-data')
            <!--end::Extracted Data Section-->

        </div>
        <!--end::Content-->

        <!--begin::Sidebar-->
        <div class="card" id="sidebarContainer"
            style="display: none;">
        </div>
        <!--end::Sidebar-->
    </div>
    <!--end::Layout-->
</div>
<!--end:::Tab pane - Pendentes-->

@push('scripts')

    {{-- Configurações passadas do Laravel para o JavaScript --}}
    @php
        $domusiaConfig = [
            'routes' => [
                'list' => route('domusia.list'),
                'extract' => route('domusia.extract'),
                'show' => route('domusia.show', ':id'),
                'destroy' => route('domusia.destroy', ':id'),
                'login' => route('login'),
            ],
            'csrfToken' => csrf_token(),
            'maxFileSize' => 10 * 1024 * 1024, // 10 MB
            'maxFileSizeMB' => 10,
            'allowedTypes' => [
                'application/pdf',
                'image/png',
                'image/jpeg',
                'image/jpg',
                'image/webp'
            ],
        ];
    @endphp
    <script>
        // Configurações do Domusia Pendentes
        window.domusiaPendentesConfig = @json($domusiaConfig);

        // Variáveis globais para compatibilidade com componentes existentes
        window.currentDocument = null;
        window.selectedFiles = [];
        window.documentosCarregados = [];
        window.currentDocumentIndex = 0;
        window.zoomLevel = 100;
        window.documentList = [];
    </script>

    {{-- Carregar o módulo JavaScript do Domusia Pendentes (método tradicional) --}}
    <script src="{{ url('/js/domusia/pendentes.js') }}"></script>

    <script>
        // Inicializar o módulo quando o DOM estiver pronto
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof DomusiaPendentes !== 'undefined' && window.domusiaPendentesConfig) {
                window.domusiaPendentesInstance = new DomusiaPendentes(window.domusiaPendentesConfig);

                // Sincronizar variáveis globais com a instância (para compatibilidade)
                Object.defineProperty(window, 'currentDocument', {
                    get: () => window.domusiaPendentesInstance?.currentDocument || null,
                    set: (value) => {
                        if (window.domusiaPendentesInstance) {
                            window.domusiaPendentesInstance.currentDocument = value;
                        }
                    }
                });

                Object.defineProperty(window, 'documentList', {
                    get: () => window.domusiaPendentesInstance?.documentList || [],
                    set: (value) => {
                        if (window.domusiaPendentesInstance) {
                            window.domusiaPendentesInstance.documentList = value;
                        }
                    }
                });

                Object.defineProperty(window, 'currentDocumentIndex', {
                    get: () => window.domusiaPendentesInstance?.currentDocumentIndex || 0,
                    set: (value) => {
                        if (window.domusiaPendentesInstance) {
                            window.domusiaPendentesInstance.currentDocumentIndex = value;
                        }
                    }
                });

                Object.defineProperty(window, 'documentosCarregados', {
                    get: () => window.domusiaPendentesInstance?.documentosCarregados || [],
                    set: (value) => {
                        if (window.domusiaPendentesInstance) {
                            window.domusiaPendentesInstance.documentosCarregados = value;
                        }
                    }
                });

                // Função global de navegação (para compatibilidade)
                window.navigateDocument = function(direction) {
                    if (window.domusiaPendentesInstance) {
                        window.domusiaPendentesInstance.navigateDocument(direction);
                    }
                };
            }
        });
    </script>
@endpush
