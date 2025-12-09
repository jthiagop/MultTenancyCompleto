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
                <span class="fw-semibold text-gray-800 fs-6">{{ $company->razao_social ?? $company->name }}</span>
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
                <span class="fw-bold fs-6 text-gray-800 me-2">{{ $company->addresses->phone ?? 'Não cadastrado' }}</span>
                <span class="badge badge-success">{{ $company->status == 'active' ? 'Ativo' : 'Inativo' }}</span>
            </div>
            <!--end::Col-->
        </div>
        <!--end::Input group-->
        <!--begin::Input group-->
        <div class="row mb-7">
            <!--begin::Label-->
            <label class="col-lg-4 fw-semibold text-muted">Rua</label>
            <!--end::Label-->
            <!--begin::Col-->
            <div class="col-lg-8">
                <a href="#"
                    class="fw-semibold fs-6 text-gray-800 text-hover-primary">{{ $company->addresses->rua ?? 'Não cadastrado' }}</a>
                    {{ $company->addresses->numero ?? 'Não cadastrado' }} -
                    {{ $company->addresses->bairro ?? 'Não cadastrado' }} -
                    {{ $company->addresses->cidade ?? 'Não cadastrado' }} -
                    {{ $company->addresses->uf ?? 'Não cadastrado' }}
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
                <span class="fw-bold fs-6 text-gray-800">{{ $company->addresses->country ?? 'Não cadastrado' }}</span>
            </div>
            <!--end::Col-->
        </div>
        <!--end::Input group-->
        <!--begin::Input group-->
        <div class="row mb-7">
            <!--begin::Label-->
            <label class="col-lg-4 fw-semibold text-muted">E-mail e Telefone</label>
            <!--end::Label-->
            <!--begin::Col-->
            <div class="col-lg-8">
                <span class="fw-bold fs-6 text-gray-800">{{ $company->addresses->email ?? 'Não cadastrado' }}, {{ $company->addresses->phone ?? 'Não cadastrado' }}</span>
            </div>
            <!--end::Col-->
        </div>
        <!--end::Input group-->
        <!--begin::Input group-->
        <div class="row mb-10">
            <!--begin::Label-->
            <label class="col-lg-4 fw-semibold text-muted">Cadastro ativo?</label>
            <!--begin::Label-->
            <!--begin::Label-->
            <div class="col-lg-8">
                <span class="fw-semibold fs-6 text-gray-800">{{ $company->status == 'ativo' ? 'Sim' : 'Não' }}</span>
            </div>
            <!--begin::Label-->
        </div>
        <!--end::Input group-->
    </div>
    <!--end::Card body-->
</div>
<!--end::details View-->

