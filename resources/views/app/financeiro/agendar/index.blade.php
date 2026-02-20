<x-tenant-app-layout pageTitle="Agendar Relatório" :breadcrumbs="[['label' => 'Financeiro', 'url' => route('banco.list')], ['label' => 'Agendar Relatório']]">

    <!--begin::Content wrapper-->
    <div class="d-flex flex-column flex-column-fluid py-3 py-lg-6">

        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <!--begin::Content container-->
            <div id="kt_app_content_container" class="app-container container-fluid">
                <!--begin::Card-->
                <div class="card">
                    <!--begin::Card body-->
                    <div class="card-body">
                        <!--begin::Heading-->
                        <div class="card-px text-center pt-15 pb-15">
                            <!--begin::Title-->
                            <h2 class="fs-2x fw-bold mb-0">Programe o envio automático de relatórios</h2>
                            <!--end::Title-->
                            <!--begin::Description-->
                            <p class="text-gray-400 fs-4 fw-semibold py-7">Escolha os relatórios que quer enviar por e-mail, defina uma frequência e quem deve recebê-los.
                            </p>
                            <!--end::Description-->
                            <!--begin::Action-->
                            <a href="#" class="btn btn-primary er fs-6 px-8 py-4" data-bs-toggle="modal"
                                data-bs-target="#kt_modal_create_api_key">Novo Relatório - <span class="text-warning">EM BREVE</span></a>
                            <!--end::Action-->
                        </div>
                        <!--end::Heading-->
                        <!--begin::Illustration-->
                        <div class="text-center pb-15 px-5">
                            <img src="{{ url('/tenancy/assets/media/illustrations/sketchy-1/16.png') }}" alt=""
                                class="mw-100 h-200px h-sm-325px" />
                        </div>
                        <!--end::Illustration-->
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Card-->
            </div>
            <!--end::Content container-->
        </div>
        <!--end::Content-->
    </div>
    <!--end::Content wrapper-->



</x-tenant-app-layout>
