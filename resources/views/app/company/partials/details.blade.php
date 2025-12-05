<!--begin::details View-->
<div class="card mb-5 mb-xl-10" id="kt_profile_details_view">
    <!--begin::Card header-->
    <div class="card-header cursor-pointer">
        <!--begin::Card title-->
        <div class="card-title d-flex justify-content-between align-items-center">
            <h3 class="fw-bold m-0">Detalhes do Organismo</h3>
        </div>
        <!--end::Card title-->
        <!--begin::Action-->
        <button type="button" id="btn-editar-organismo"
            class="btn btn-sm btn-primary align-self-center">Editar Organismo</button>
        <!--end::Action-->
    </div>
    <!--begin::Card header-->
    <!--begin::Card body-->
    <div class="card-body p-9">
        <!--begin::Row-->
        <div class="row mb-7">
            <!--begin::Label-->
            <label class="col-lg-4 fw-semibold text-muted">Nome</label>
            <!--end::Label-->
            <!--begin::Col-->
            <div class="col-lg-8">
                <span class="fw-bold fs-6 text-gray-800">{{ $company->name }}</span>
            </div>
            <!--end::Col-->
        </div>
        <!--end::Row-->
        <!--begin::Input group-->
        <div class="row mb-7">
            <!--begin::Label-->
            <label class="col-lg-4 fw-semibold text-muted">Razão Social</label>
            <!--end::Label-->
            <!--begin::Col-->
            <div class="col-lg-8 fv-row">
                <span class="fw-semibold text-gray-800 fs-6">{{ $company->name }}</span>
            </div>
            <!--end::Col-->
        </div>
        <!--end::Input group-->
        <!--begin::Input group-->
        <div class="row mb-7">
            <!--begin::Label-->
            <label class="col-lg-4 fw-semibold text-muted">Telefone
                <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                    title="Phone number must be active"></i></label>
            <!--end::Label-->
            <!--begin::Col-->
            <div class="col-lg-8 d-flex align-items-center">
                <span class="fw-bold fs-6 text-gray-800 me-2">{{ $company->phone }}</span>
                <span class="badge badge-success">Verified</span>
            </div>
            <!--end::Col-->
        </div>
        <!--end::Input group-->
        <!--begin::Input group-->
        <div class="row mb-7">
            <!--begin::Label-->
            <label class="col-lg-4 fw-semibold text-muted">Site</label>
            <!--end::Label-->
            <!--begin::Col-->
            <div class="col-lg-8">
                <a href="#"
                    class="fw-semibold fs-6 text-gray-800 text-hover-primary">{{ $company->site }}</a>
            </div>
            <!--end::Col-->
        </div>
        <!--end::Input group-->
        <!--begin::Input group-->
        <div class="row mb-7">
            <!--begin::Label-->
            <label class="col-lg-4 fw-semibold text-muted">País
                <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                    title="Country of origination"></i></label>
            <!--end::Label-->
            <!--begin::Col-->
            <div class="col-lg-8">
                <span class="fw-bold fs-6 text-gray-800">{{ $company->country }}</span>
            </div>
            <!--end::Col-->
        </div>
        <!--end::Input group-->
        <!--begin::Input group-->
        <div class="row mb-7">
            <!--begin::Label-->
            <label class="col-lg-4 fw-semibold text-muted">Comunicação</label>
            <!--end::Label-->
            <!--begin::Col-->
            <div class="col-lg-8">
                <span class="fw-bold fs-6 text-gray-800">Email, Phone</span>
            </div>
            <!--end::Col-->
        </div>
        <!--end::Input group-->
        <!--begin::Input group-->
        <div class="row mb-10">
            <!--begin::Label-->
            <label class="col-lg-4 fw-semibold text-muted">Allow Changes</label>
            <!--begin::Label-->
            <!--begin::Label-->
            <div class="col-lg-8">
                <span class="fw-semibold fs-6 text-gray-800">Yes</span>
            </div>
            <!--begin::Label-->
        </div>
        <!--end::Input group-->
        <!--begin::Notice-->
        <div
            class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-6">
            <!--begin::Icon-->
            <!--begin::Svg Icon | path: icons/duotune/general/gen044.svg-->
            <i class="bi bi-exclamation-circle-fill text-warning fs-2"></i>
            <!--end::Svg Icon-->
            <!--end::Icon-->
            <!--begin::Wrapper-->
            <div class="d-flex flex-stack flex-grow-1">
                <!--begin::Content-->
                <div class="fw-semibold">
                    <h4 class="text-gray-900 fw-bold">Precisamos de sua atenção!</h4>
                    <div class="fs-6 text-gray-700">O seu pagamento foi recusado. Para começar a
                        usar as ferramentas, por favor
                        <a class="fw-bold" href="../../demo1/dist/account/billing.html">Adicione
                            um método de pagamento</a>.
                    </div>
                </div>
                <!--end::Content-->
            </div>
            <!--end::Wrapper-->
        </div>
        <!--end::Notice-->
    </div>
    <!--end::Card body-->
</div>
<!--end::details View-->

