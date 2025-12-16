<!-- /resources/views/app/components/toolbar.blade.php -->

<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <!--begin::Toolbar container-->
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
        <!--begin::Page title-->
        <div class="page-title d-flex align-items-center flex-wrap me-3">
            <!--begin::Logo-->
            <div class="symbol symbol-50px me-3 border border-secondary border-opacity-25 rounded-circle">
                @if (!empty($company->avatar))
                    <img class="rounded-circle" alt="Logo"
                        src="{{ route('file', ['path' => $company->avatar]) }}" />
                @else
                    <div class="symbol-label fs-2 fw-semibold text-primary bg-light-primary">
                        {{ substr($company->name, 0, 1) }}
                    </div>
                @endif
            </div>
            <!--end::Logo-->

            <!--begin::Text Group-->
            <div class="d-flex flex-column justify-content-center">
                <!--begin::Title-->
                <h1 class="page-heading text-dark fw-bold fs-4 my-0 lh-1">
                    {{ $company->name }}
                </h1>
                <!--end::Title-->

                <!--begin::Subtitle & Info-->
                <div class="d-flex align-items-center flex-wrap fw-semibold fs-7 my-0 pt-1">
                    <!-- Razão Social -->
                    <span class="text-muted me-3">{{ $company->razao_social }}</span>

                    <!-- Separator -->
                    <span class="bullet bg-gray-400 w-5px h-2px me-3"></span>

                    <!-- CNPJ Badge -->
                    @if (!empty($company->cnpj))
                        <a href="{{ route('company.edit', ['company' => $company->id]) }}"
                           class="badge badge-light-primary text-hover-primary text-decoration-none"
                           data-bs-toggle="tooltip" title="Editar dados da empresa">
                           <i class="bi bi-pencil-square me-1 text-primary fs-8"></i>
                           CNPJ: {{ $company->cnpj }}
                        </a>
                    @else
                        <a href="{{ route('company.edit', ['company' => $company->id]) }}"
                           class="badge badge-light-warning text-hover-warning text-decoration-none">
                           <i class="bi bi-exclamation-triangle me-1 text-warning fs-8"></i>
                           Completar Cadastro
                        </a>
                    @endif
                </div>
                <!--end::Subtitle & Info-->
            </div>
            <!--end::Text Group-->
        </div>
        <!--end::Page title-->

        <!--begin::Actions-->
        <div class="d-flex align-items-center gap-1 gap-lg-2">
            <!-- Data e Hora -->
            <div class="d-flex align-items-center bg-light rounded px-1 py-2">
                <i class="bi bi-clock text-gray-500 fs-6 me-2"></i>
                <div id="datetime" class="text-gray-700 fs-7"></div>
            </div>

            {{-- <!-- Botão para Criar Campanha -->
            <a href="#" class="btn btn-sm btn-flex btn-success fw-bold"
                data-bs-toggle="modal" data-bs-target="#kt_modal_create_campaign">
                <i class="bi bi-plus-lg fs-2"></i>
                <span class="d-none d-md-inline ms-1">Nova Campanha</span>
            </a> --}}
        </div>
        <!--end::Actions-->
    </div>
    <!--end::Toolbar container-->
</div>

<!-- Script simples para atualizar hora se não existir globalmente -->
<script>
    (function() {
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
            const dateString = now.toLocaleDateString('pt-BR', { weekday: 'short', day: '2-digit', month: 'short' });
            const el = document.getElementById('datetime');
            if(el) el.innerHTML = `<span class="text-muted fs-8 me-1">${dateString}</span> ${timeString}`;
        }
        updateTime();
        setInterval(updateTime, 60000);
    })();
</script>
