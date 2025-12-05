<!--begin::Modal - New Veículos-->
<div class="modal fade" id="kt_modal_new_veiculos" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-fullscreen">
        <!--begin::Modal content-->
        <div class="modal-content rounded">
            <!--begin::Modal header-->
            <div class="modal-header btn btn-sm">
                <h3 class="modal-title" id="modal_veiculos_title">Cadastro de Veículo</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!--end::Modal header-->

            <!--begin::Modal body-->
            <div class="modal-body scroll-y px-10 px-lg-15 pb-15 bg-light pt-5">
                <!-- Begin::Form -->
                <form id="kt_modal_veiculos_form" class="form" action="{{ route('bem.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <meta name="csrf-token" content="{{ csrf_token() }}">

                    <!-- Campo oculto para identificar o tipo -->
                    <input type="hidden" name="tipo" value="veiculo">

                    <!--begin::Card-->
                    <div class="card mb-xl-10">
                        <div class="card-body px-10">
                            <!--begin::Form-->
                            <!-- Conteúdo do formulário será adicionado aqui -->
                            <div class="row g-9 mb-8">
                                <div class="col-md-12 fv-row">
                                    <div class="alert alert-info">
                                        <i class="bi bi-info-circle me-2"></i>
                                        Formulário de cadastro de veículo será implementado em breve.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Card-->
                </form>
                <!--end::Form-->
            </div>
            <!--end::Modal body-->

            <!--begin::Modal footer-->
            <div class="modal-footer btn btn-sm">
                <div class="text-center">
                    <button type="reset" id="kt_modal_veiculos_cancel"
                        class="btn btn-sm btn-light me-3">Cancelar</button>
                    <!-- Split dropup button -->
                    <div class="btn-group dropup">
                        <!-- Botão principal -->
                        <button type="submit" id="kt_modal_veiculos_submit" form="kt_modal_veiculos_form" class="btn btn-sm btn-primary">
                            <span class="indicator-label">Salvar</span>
                            <span class="indicator-progress">Aguarde...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                        <!-- Botão de dropup -->
                        <button type="button" class="btn btn-sm btn-primary dropdown-toggle dropdown-toggle-split"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <!-- Opções do dropup -->
                        <div class="dropdown-menu">
                            <a class="dropdown-item btn-sm" href="#" id="kt_modal_veiculos_clone">Salvar e Clonar</a>
                            <a class="dropdown-item btn-sm" href="#" id="kt_modal_veiculos_novo">Salvar e em Branco</a>
                        </div>
                    </div>
                </div>
                <!--end::Actions-->
            </div>
            <!--end::Modal footer-->
        </div>
        <!--end::Modal content-->
    </div>
    <!--end::Modal dialog-->
</div>
<!--end::Modal - New Veículos-->

