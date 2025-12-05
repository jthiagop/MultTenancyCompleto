<!-- CSS do Kendo (tema) -->
<link href="https://kendo.cdn.telerik.com/themes/8.0.1/default/default-main.css" rel="stylesheet" />

<!-- jQuery (obrigatório) -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<!-- Kendo UI (JS principal) -->
<script src="https://kendo.cdn.telerik.com/2024.2.514/js/kendo.all.min.js"></script>



<x-tenant-app-layout>

    <!-- Modal -->
        @include('app.financeiro.banco.components.modal')

    <!-- Estilos -->
    <style>
        #drop-area {
            border: 8px dashed #007bff;
            padding: 20px;
            cursor: pointer;
        }
    </style>

    <!-- Script -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let dropArea = document.getElementById("drop-area");
            let fileInput = document.getElementById("fileInput");
            let fileNameDisplay = document.getElementById("fileName");
            let importButton = document.getElementById("importButton");

            // Evento ao selecionar um arquivo
            fileInput.addEventListener("change", function() {
                if (fileInput.files.length > 0) {
                    fileNameDisplay.textContent = "📂 " + fileInput.files[0].name;
                    importButton.removeAttribute("disabled");
                }
            });

            // Eventos de arrastar e soltar
            dropArea.addEventListener("dragover", function(event) {
                event.preventDefault();
                dropArea.style.backgroundColor = "#f8f9fa";
            });

            dropArea.addEventListener("dragleave", function() {
                dropArea.style.backgroundColor = "white";
            });

            dropArea.addEventListener("drop", function(event) {
                event.preventDefault();
                dropArea.style.backgroundColor = "white";
                let files = event.dataTransfer.files;
                if (files.length > 0 && files[0].type === "application/x-ofx") {
                    fileInput.files = files;
                    fileNameDisplay.textContent = "📂 " + files[0].name;
                    importButton.removeAttribute("disabled");
                } else {
                    alert("Por favor, selecione um arquivo OFX válido.");
                }
            });
        });
    </script>
    <!--begin::Main-->
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <!--begin::Content wrapper-->
        <div class="d-flex flex-column flex-column-fluid">
            <!--begin::Toolbar-->
            @include('app.financeiro.banco.components.header')
            <!--end::Toolbar-->
            <!--begin::Content-->
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <!--begin::Content container-->
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <!-- Mensagem de sucesso -->
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Fechar"></button>
                        </div>
                    @endif

                    <!-- Mensagem de erro geral (não relacionada à validação) -->
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Fechar"></button>
                        </div>
                    @endif
                    <!-- Mensagens de erro de validação (caso existam) -->
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul>
                                @foreach ($errors->all() as $erro)
                                    <li>{{ $erro }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Fechar"></button>
                        </div>
                    @endif
                    <!--begin::Navbar-->
                        @include('app.financeiro.banco.components.main-card')
                    <!--end::Navbar-->
                    @includeIf("app.financeiro.banco.tabs.{$activeTab}")
                </div>
                <!--end::Content container-->
            </div>
            <!--end::Content-->
        </div>
        <!--end::Content wrapper-->
    </div>
    @include('app.components.modals.lancar-banco')
    <!--end::Modal - Upgrade plan-->
    <script>
        var lpsData = @json($lps);
        // Gerar apenas o caminho da rota (sem domínio)
        var bancoFluxoChartDataUrl = '{{ route("banco.fluxo.chart.data", [], false) }}';
        console.log('[Blade] URL da rota banco.fluxo.chart.data:', bancoFluxoChartDataUrl);
    </script>
</x-tenant-app-layout>

<script src="/assets/js/custom_script.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!--begin::Vendors Javascript(used for this page only)-->
<script src="/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<!--end::Vendors Javascript-->
<script src="/assets/js/custom/utilities/modals/financeiro/moduloAnexos.js"></script>
<script src="/assets/js/custom/utilities/modals/financeiro/new-banco.js"></script>

<script src="/assets/plugins/custom/formrepeater/formrepeater.bundle.js"></script>
<script src="/assets/js/custom/apps/bancos/form-dropzone.js"></script>

<!--begin::Custom Javascript(used for this page only)-->

<!--end::Custom Javascript chats-->
<script src="/assets/js/custom/apps/bancos/shipping.js"></script>
<!--end::Custom Javascript chats bancos-->

<script src="/assets/js/custom/apps/bancos/widgets.bundle.js"></script>

@if($activeTab === 'overview')
<script src="/assets/js/custom/apps/bancos/banco-fluxo-widget-36.js"></script>
@endif

<script src="/assets/js/custom/apps/bancos/fluxo-banco-chart.js"></script>
<!--end::Custom Javascript-->

<script src="/assets/js/custom/utilities/modals/upgrade-plan.js"></script>
<script src="/assets/js/custom/utilities/modals/create-campaign.js"></script>
<script src="/assets/js/custom/utilities/modals/users-search.js"></script>

<script src="/assets/js/custom/utilities/modals/company/prestacaoConta.js"></script>



<!--end::Custom Javascript-->
<!--end::Javascript-->

<!-- jQuery -->

<!-- Custom Script -->
<script src="{{ asset('js/custom_script.js') }}"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deleteLinks = document.querySelectorAll('.delete-link');

        deleteLinks.forEach(link => {
            link.addEventListener('click', function(event) {
                event.preventDefault();
                const id = this.getAttribute('data-id');
                const form = document.getElementById(`delete-form-${id}`);
                Swal.fire({
                    title: 'Você tem certeza?',
                    text: 'Esta ação não pode ser desfeita!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sim, exclua!',
                    cancelButtonText: 'Não, cancele',
                    customClass: {
                        confirmButton: 'btn btn-danger',
                        cancelButton: 'btn btn-secondary'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
