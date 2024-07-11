<x-tenant-app-layout>

    <!--begin::Main-->
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <!--begin::Content wrapper-->
        <div class="d-flex flex-column flex-column-fluid">
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <!--begin::Content container-->
                <div id="kt_app_content_container" class="app-container container-xxl">

                    <!--begin::Toolbar-->
                    <div class="d-flex flex-wrap flex-stack my-5">
                        <!--begin::Heading-->
                        <h2 class="fs-2 fw-semibold my-2">Projects
                            <span class="fs-6 text-gray-400 ms-1">by Status</span>
                        </h2>
                        <!--end::Heading-->
                        <!--begin::Controls-->
                        <div class="d-flex flex-wrap my-1">
                            <!--begin::Select wrapper-->
                            <div class="m-0">
                                <!--begin::Select-->
                                <select name="status" data-control="select2" data-hide-search="true"
                                    class="form-select form-select-sm bg-body border-body fw-bold w-125px">
                                    <option value="Active" selected="selected">Active</option>
                                    <option value="Approved">In Progress</option>
                                    <option value="Declined">To Do</option>
                                    <option value="In Progress">Completed</option>
                                </select>
                                <!--end::Select-->
                            </div>
                            <!--end::Select wrapper-->
                        </div>
                        <!--end::Controls-->
                    </div>
                    <!--end::Toolbar-->
                    <!--begin::Row-->
                    <div class="row g-6 g-xl-9">
                        <!--begin::Col-->
                        <div class="col-md-6 col-xl-4">
                            <!--begin::Card-->
                            <a href="../../demo1/dist/apps/projects/project.html" class="card border-hover-primary">
                                <!--begin::Card header-->
                                <div class="card-header border-0 pt-9">
                                    <!--begin::Card Title-->
                                    <div class="card-title m-0">
                                        <!--begin::Avatar-->
                                        <div class="symbol symbol-50px w-50px bg-light">
                                            <img src="assets/media/svg/brand-logos/plurk.svg" alt="image"
                                                class="p-3" />
                                        </div>
                                        <!--end::Avatar-->
                                    </div>
                                    <!--end::Car Title-->
                                    <!--begin::Card toolbar-->
                                    <div class="card-toolbar">
                                        <span class="badge badge-light-success fw-bold me-auto px-4 py-3">Liberado</span>
                                    </div>
                                    <!--end::Card toolbar-->
                                </div>
                                <!--end:: Card header-->
                                <!--begin:: Card body-->
                                <div class="card-body p-9">
                                    <!--begin::Name-->
                                    <div class="fs-1 fw-bold text-dark">Financeiro</div>
                                    <!--end::Name-->
                                    <!--begin::Description-->
                                    <p class="text-gray-400 fw-semibold fs-5 mt-1 mb-7">Movimentações e Cadastros Financeiros </p>
                                    <!--end::Description-->

                                    <!--begin::Progress-->
                                    <div class="h-4px w-100 bg-light mb-5" data-bs-toggle="tooltip"
                                        title="This project 50% completed">
                                        <div class="bg-primary rounded h-4px" role="progressbar" style="width: 50%"
                                            aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <!--end::Progress-->
                                    <!--begin::Users-->
                                    <div class="symbol-group symbol-hover">
                                        <!--begin::User-->
                                        <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip"
                                            title="Emma Smith">
                                            <img alt="Pic" src="assets/media/avatars/300-6.jpg" />
                                        </div>
                                        <!--begin::User-->
                                        <!--begin::User-->
                                        <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip"
                                            title="Rudy Stone">
                                            <img alt="Pic" src="assets/media/avatars/300-1.jpg" />
                                        </div>
                                        <!--begin::User-->
                                        <!--begin::User-->
                                        <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip"
                                            title="Susan Redwood">
                                            <span class="symbol-label bg-primary text-inverse-primary fw-bold">S</span>
                                        </div>
                                        <!--begin::User-->
                                    </div>
                                    <!--end::Users-->
                                </div>
                                <!--end:: Card body-->
                            </a>
                            <!--end::Card-->
                        </div>
                        <!--end::Col-->
                        <!--begin::Col-->
                        <div class="col-md-6 col-xl-4">
                            <!--begin::Card-->
                            <a href="../../demo1/dist/apps/projects/project.html" class="card border-hover-primary">
                                <!--begin::Card header-->
                                <div class="card-header border-0 pt-9">
                                    <!--begin::Card Title-->
                                    <div class="card-title m-0">
                                        <!--begin::Avatar-->
                                        <div class="symbol symbol-50px w-50px bg-light">
                                            <img src="assets/media/svg/brand-logos/disqus.svg" alt="image"
                                                class="p-3" />
                                        </div>
                                        <!--end::Avatar-->
                                    </div>
                                    <!--end::Car Title-->
                                    <!--begin::Card toolbar-->
                                    <div class="card-toolbar">
                                        <span class="badge badge-light-danger fw-bold me-auto px-4 py-3">Bloqueado</span>
                                    </div>
                                    <!--end::Card toolbar-->
                                </div>
                                <!--end:: Card header-->
                                <!--begin:: Card body-->
                                <div class="card-body p-9">
                                    <!--begin::Name-->
                                    <div class="fs-3 fw-bold text-dark">Patrimônio</div>
                                    <!--end::Name-->
                                    <!--begin::Description-->
                                    <p class="text-gray-400 fw-semibold fs-5 mt-1 mb-7">Gestão, foro e laudêmio </p>
                                    <!--end::Description-->
                                    <!--begin::Progress-->
                                    <div class="h-4px w-100 bg-light mb-5" data-bs-toggle="tooltip"
                                        title="This project 30% completed">
                                        <div class="bg-info rounded h-4px" role="progressbar" style="width: 30%"
                                            aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <!--end::Progress-->
                                    <!--begin::Users-->
                                    <div class="symbol-group symbol-hover">
                                        <!--begin::User-->
                                        <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip"
                                            title="Alan Warden">
                                            <span class="symbol-label bg-warning text-inverse-warning fw-bold">A</span>
                                        </div>
                                        <!--begin::User-->
                                        <!--begin::User-->
                                        <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip"
                                            title="Brian Cox">
                                            <img alt="Pic" src="assets/media/avatars/300-5.jpg" />
                                        </div>
                                        <!--begin::User-->
                                    </div>
                                    <!--end::Users-->
                                </div>
                                <!--end:: Card body-->
                            </a>
                            <!--end::Card-->
                        </div>
                        <!--end::Col-->
                        <!--begin::Col-->
                        <div class="col-md-6 col-xl-4">
                            <!--begin::Card-->
                            <a href="../../demo1/dist/apps/projects/project.html" class="card border-hover-primary">
                                <!--begin::Card header-->
                                <div class="card-header border-0 pt-9">
                                    <!--begin::Card Title-->
                                    <div class="card-title m-0">
                                        <!--begin::Avatar-->
                                        <div class="symbol symbol-50px w-50px bg-light">
                                            <img src="assets/media/svg/brand-logos/figma-1.svg" alt="image"
                                                class="p-3" />
                                        </div>
                                        <!--end::Avatar-->
                                    </div>
                                    <!--end::Car Title-->
                                    <!--begin::Card toolbar-->
                                    <div class="card-toolbar">
                                        <span class="badge badge-light-danger fw-bold me-auto px-4 py-3">Bloqueado</span>
                                    </div>
                                    <!--end::Card toolbar-->
                                </div>
                                <!--end:: Card header-->
                                <!--begin:: Card body-->
                                <div class="card-body p-9">
                                    <!--begin::Name-->
                                    <div class="fs-3 fw-bold text-dark">Controle Veícular</div>
                                    <!--end::Name-->
                                    <!--begin::Description-->
                                    <p class="text-gray-400 fw-semibold fs-5 mt-1 mb-7">Gestão dos seguros, frota veícular</p>
                                    <!--end::Description-->
                                    <!--begin::Progress-->
                                    <div class="h-4px w-100 bg-light mb-5" data-bs-toggle="tooltip"
                                        title="This project 100% completed">
                                        <div class="bg-success rounded h-4px" role="progressbar" style="width: 100%"
                                            aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <!--end::Progress-->
                                    <!--begin::Users-->
                                    <div class="symbol-group symbol-hover">
                                        <!--begin::User-->
                                        <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip"
                                            title="Mad Macy">
                                            <img alt="Pic" src="assets/media/avatars/300-2.jpg" />
                                        </div>
                                        <!--begin::User-->
                                        <!--begin::User-->
                                        <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip"
                                            title="Cris Willson">
                                            <img alt="Pic" src="assets/media/avatars/300-9.jpg" />
                                        </div>
                                        <!--begin::User-->
                                        <!--begin::User-->
                                        <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip"
                                            title="Mike Garcie">
                                            <span class="symbol-label bg-info text-inverse-info fw-bold">M</span>
                                        </div>
                                        <!--begin::User-->
                                    </div>
                                    <!--end::Users-->
                                </div>
                                <!--end:: Card body-->
                            </a>
                            <!--end::Card-->
                        </div>
                        <!--end::Col-->

                    <!--begin::Modals-->
                    <!--begin::Modal - View Users-->
                    <div class="modal fade" id="kt_modal_view_users" tabindex="-1" aria-hidden="true">
                        <!--begin::Modal dialog-->
                        <div class="modal-dialog mw-650px">
                            <!--begin::Modal content-->
                            <div class="modal-content">
                                <!--begin::Modal header-->
                                <div class="modal-header pb-0 border-0 justify-content-end">
                                    <!--begin::Close-->
                                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                                        <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
                                        <span class="svg-icon svg-icon-1">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2"
                                                    rx="1" transform="rotate(-45 6 17.3137)"
                                                    fill="currentColor" />
                                                <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                                    transform="rotate(45 7.41422 6)" fill="currentColor" />
                                            </svg>
                                        </span>
                                        <!--end::Svg Icon-->
                                    </div>
                                    <!--end::Close-->
                                </div>
                                <!--begin::Modal header-->
                                <!--begin::Modal body-->
                                <div class="modal-body scroll-y mx-5 mx-xl-18 pt-0 pb-15">
                                    <!--begin::Heading-->
                                    <div class="text-center mb-13">
                                        <!--begin::Title-->
                                        <h1 class="mb-3">Browse Users</h1>
                                        <!--end::Title-->
                                        <!--begin::Description-->
                                        <div class="text-muted fw-semibold fs-5">If you need more info, please check
                                            out our
                                            <a href="#" class="link-primary fw-bold">Users Directory</a>.
                                        </div>
                                        <!--end::Description-->
                                    </div>
                                    <!--end::Heading-->
                                    <!--begin::Users-->
                                    <div class="mb-15">
                                        <!--begin::List-->
                                        <div class="mh-375px scroll-y me-n7 pe-7">
                                            <!--begin::User-->
                                            <div
                                                class="d-flex flex-stack py-5 border-bottom border-gray-300 border-bottom-dashed">
                                                <!--begin::Details-->
                                                <div class="d-flex align-items-center">
                                                    <!--begin::Avatar-->
                                                    <div class="symbol symbol-35px symbol-circle">
                                                        <img alt="Pic" src="assets/media/avatars/300-6.jpg" />
                                                    </div>
                                                    <!--end::Avatar-->
                                                    <!--begin::Details-->
                                                    <div class="ms-6">
                                                        <!--begin::Name-->
                                                        <a href="#"
                                                            class="d-flex align-items-center fs-5 fw-bold text-dark text-hover-primary">Emma
                                                            Smith
                                                            <span class="badge badge-light fs-8 fw-semibold ms-2">Art
                                                                Director</span></a>
                                                        <!--end::Name-->
                                                        <!--begin::Email-->
                                                        <div class="fw-semibold text-muted">smith@kpmg.com</div>
                                                        <!--end::Email-->
                                                    </div>
                                                    <!--end::Details-->
                                                </div>
                                                <!--end::Details-->
                                                <!--begin::Stats-->
                                                <div class="d-flex">
                                                    <!--begin::Sales-->
                                                    <div class="text-end">
                                                        <div class="fs-5 fw-bold text-dark">$23,000</div>
                                                        <div class="fs-7 text-muted">Sales</div>
                                                    </div>
                                                    <!--end::Sales-->
                                                </div>
                                                <!--end::Stats-->
                                            </div>
                                            <!--end::User-->
                                            <!--begin::User-->
                                            <div
                                                class="d-flex flex-stack py-5 border-bottom border-gray-300 border-bottom-dashed">
                                                <!--begin::Details-->
                                                <div class="d-flex align-items-center">
                                                    <!--begin::Avatar-->
                                                    <div class="symbol symbol-35px symbol-circle">
                                                        <span
                                                            class="symbol-label bg-light-danger text-danger fw-semibold">M</span>
                                                    </div>
                                                    <!--end::Avatar-->
                                                    <!--begin::Details-->
                                                    <div class="ms-6">
                                                        <!--begin::Name-->
                                                        <a href="#"
                                                            class="d-flex align-items-center fs-5 fw-bold text-dark text-hover-primary">Melody
                                                            Macy
                                                            <span
                                                                class="badge badge-light fs-8 fw-semibold ms-2">Marketing
                                                                Analytic</span></a>
                                                        <!--end::Name-->
                                                        <!--begin::Email-->
                                                        <div class="fw-semibold text-muted">melody@altbox.com</div>
                                                        <!--end::Email-->
                                                    </div>
                                                    <!--end::Details-->
                                                </div>
                                                <!--end::Details-->
                                                <!--begin::Stats-->
                                                <div class="d-flex">
                                                    <!--begin::Sales-->
                                                    <div class="text-end">
                                                        <div class="fs-5 fw-bold text-dark">$50,500</div>
                                                        <div class="fs-7 text-muted">Sales</div>
                                                    </div>
                                                    <!--end::Sales-->
                                                </div>
                                                <!--end::Stats-->
                                            </div>
                                            <!--end::User-->
                                            <!--begin::User-->
                                            <div
                                                class="d-flex flex-stack py-5 border-bottom border-gray-300 border-bottom-dashed">
                                                <!--begin::Details-->
                                                <div class="d-flex align-items-center">
                                                    <!--begin::Avatar-->
                                                    <div class="symbol symbol-35px symbol-circle">
                                                        <img alt="Pic" src="assets/media/avatars/300-1.jpg" />
                                                    </div>
                                                    <!--end::Avatar-->
                                                    <!--begin::Details-->
                                                    <div class="ms-6">
                                                        <!--begin::Name-->
                                                        <a href="#"
                                                            class="d-flex align-items-center fs-5 fw-bold text-dark text-hover-primary">Max
                                                            Smith
                                                            <span
                                                                class="badge badge-light fs-8 fw-semibold ms-2">Software
                                                                Enginer</span></a>
                                                        <!--end::Name-->
                                                        <!--begin::Email-->
                                                        <div class="fw-semibold text-muted">max@kt.com</div>
                                                        <!--end::Email-->
                                                    </div>
                                                    <!--end::Details-->
                                                </div>
                                                <!--end::Details-->
                                                <!--begin::Stats-->
                                                <div class="d-flex">
                                                    <!--begin::Sales-->
                                                    <div class="text-end">
                                                        <div class="fs-5 fw-bold text-dark">$75,900</div>
                                                        <div class="fs-7 text-muted">Sales</div>
                                                    </div>
                                                    <!--end::Sales-->
                                                </div>
                                                <!--end::Stats-->
                                            </div>
                                            <!--end::User-->
                                            <!--begin::User-->
                                            <div
                                                class="d-flex flex-stack py-5 border-bottom border-gray-300 border-bottom-dashed">
                                                <!--begin::Details-->
                                                <div class="d-flex align-items-center">
                                                    <!--begin::Avatar-->
                                                    <div class="symbol symbol-35px symbol-circle">
                                                        <img alt="Pic" src="assets/media/avatars/300-5.jpg" />
                                                    </div>
                                                    <!--end::Avatar-->
                                                    <!--begin::Details-->
                                                    <div class="ms-6">
                                                        <!--begin::Name-->
                                                        <a href="#"
                                                            class="d-flex align-items-center fs-5 fw-bold text-dark text-hover-primary">Sean
                                                            Bean
                                                            <span class="badge badge-light fs-8 fw-semibold ms-2">Web
                                                                Developer</span></a>
                                                        <!--end::Name-->
                                                        <!--begin::Email-->
                                                        <div class="fw-semibold text-muted">sean@dellito.com</div>
                                                        <!--end::Email-->
                                                    </div>
                                                    <!--end::Details-->
                                                </div>
                                                <!--end::Details-->
                                                <!--begin::Stats-->
                                                <div class="d-flex">
                                                    <!--begin::Sales-->
                                                    <div class="text-end">
                                                        <div class="fs-5 fw-bold text-dark">$10,500</div>
                                                        <div class="fs-7 text-muted">Sales</div>
                                                    </div>
                                                    <!--end::Sales-->
                                                </div>
                                                <!--end::Stats-->
                                            </div>
                                            <!--end::User-->
                                            <!--begin::User-->
                                            <div
                                                class="d-flex flex-stack py-5 border-bottom border-gray-300 border-bottom-dashed">
                                                <!--begin::Details-->
                                                <div class="d-flex align-items-center">
                                                    <!--begin::Avatar-->
                                                    <div class="symbol symbol-35px symbol-circle">
                                                        <img alt="Pic" src="assets/media/avatars/300-25.jpg" />
                                                    </div>
                                                    <!--end::Avatar-->
                                                    <!--begin::Details-->
                                                    <div class="ms-6">
                                                        <!--begin::Name-->
                                                        <a href="#"
                                                            class="d-flex align-items-center fs-5 fw-bold text-dark text-hover-primary">Brian
                                                            Cox
                                                            <span class="badge badge-light fs-8 fw-semibold ms-2">UI/UX
                                                                Designer</span></a>
                                                        <!--end::Name-->
                                                        <!--begin::Email-->
                                                        <div class="fw-semibold text-muted">brian@exchange.com</div>
                                                        <!--end::Email-->
                                                    </div>
                                                    <!--end::Details-->
                                                </div>
                                                <!--end::Details-->
                                                <!--begin::Stats-->
                                                <div class="d-flex">
                                                    <!--begin::Sales-->
                                                    <div class="text-end">
                                                        <div class="fs-5 fw-bold text-dark">$20,000</div>
                                                        <div class="fs-7 text-muted">Sales</div>
                                                    </div>
                                                    <!--end::Sales-->
                                                </div>
                                                <!--end::Stats-->
                                            </div>
                                            <!--end::User-->
                                            <!--begin::User-->
                                            <div
                                                class="d-flex flex-stack py-5 border-bottom border-gray-300 border-bottom-dashed">
                                                <!--begin::Details-->
                                                <div class="d-flex align-items-center">
                                                    <!--begin::Avatar-->
                                                    <div class="symbol symbol-35px symbol-circle">
                                                        <span
                                                            class="symbol-label bg-light-warning text-warning fw-semibold">C</span>
                                                    </div>
                                                    <!--end::Avatar-->
                                                    <!--begin::Details-->
                                                    <div class="ms-6">
                                                        <!--begin::Name-->
                                                        <a href="#"
                                                            class="d-flex align-items-center fs-5 fw-bold text-dark text-hover-primary">Mikaela
                                                            Collins
                                                            <span class="badge badge-light fs-8 fw-semibold ms-2">Head
                                                                Of Marketing</span></a>
                                                        <!--end::Name-->
                                                        <!--begin::Email-->
                                                        <div class="fw-semibold text-muted">mik@pex.com</div>
                                                        <!--end::Email-->
                                                    </div>
                                                    <!--end::Details-->
                                                </div>
                                                <!--end::Details-->
                                                <!--begin::Stats-->
                                                <div class="d-flex">
                                                    <!--begin::Sales-->
                                                    <div class="text-end">
                                                        <div class="fs-5 fw-bold text-dark">$9,300</div>
                                                        <div class="fs-7 text-muted">Sales</div>
                                                    </div>
                                                    <!--end::Sales-->
                                                </div>
                                                <!--end::Stats-->
                                            </div>
                                            <!--end::User-->
                                            <!--begin::User-->
                                            <div
                                                class="d-flex flex-stack py-5 border-bottom border-gray-300 border-bottom-dashed">
                                                <!--begin::Details-->
                                                <div class="d-flex align-items-center">
                                                    <!--begin::Avatar-->
                                                    <div class="symbol symbol-35px symbol-circle">
                                                        <img alt="Pic" src="assets/media/avatars/300-9.jpg" />
                                                    </div>
                                                    <!--end::Avatar-->
                                                    <!--begin::Details-->
                                                    <div class="ms-6">
                                                        <!--begin::Name-->
                                                        <a href="#"
                                                            class="d-flex align-items-center fs-5 fw-bold text-dark text-hover-primary">Francis
                                                            Mitcham
                                                            <span
                                                                class="badge badge-light fs-8 fw-semibold ms-2">Software
                                                                Arcitect</span></a>
                                                        <!--end::Name-->
                                                        <!--begin::Email-->
                                                        <div class="fw-semibold text-muted">f.mit@kpmg.com</div>
                                                        <!--end::Email-->
                                                    </div>
                                                    <!--end::Details-->
                                                </div>
                                                <!--end::Details-->
                                                <!--begin::Stats-->
                                                <div class="d-flex">
                                                    <!--begin::Sales-->
                                                    <div class="text-end">
                                                        <div class="fs-5 fw-bold text-dark">$15,000</div>
                                                        <div class="fs-7 text-muted">Sales</div>
                                                    </div>
                                                    <!--end::Sales-->
                                                </div>
                                                <!--end::Stats-->
                                            </div>
                                            <!--end::User-->
                                            <!--begin::User-->
                                            <div
                                                class="d-flex flex-stack py-5 border-bottom border-gray-300 border-bottom-dashed">
                                                <!--begin::Details-->
                                                <div class="d-flex align-items-center">
                                                    <!--begin::Avatar-->
                                                    <div class="symbol symbol-35px symbol-circle">
                                                        <span
                                                            class="symbol-label bg-light-danger text-danger fw-semibold">O</span>
                                                    </div>
                                                    <!--end::Avatar-->
                                                    <!--begin::Details-->
                                                    <div class="ms-6">
                                                        <!--begin::Name-->
                                                        <a href="#"
                                                            class="d-flex align-items-center fs-5 fw-bold text-dark text-hover-primary">Olivia
                                                            Wild
                                                            <span
                                                                class="badge badge-light fs-8 fw-semibold ms-2">System
                                                                Admin</span></a>
                                                        <!--end::Name-->
                                                        <!--begin::Email-->
                                                        <div class="fw-semibold text-muted">olivia@corpmail.com</div>
                                                        <!--end::Email-->
                                                    </div>
                                                    <!--end::Details-->
                                                </div>
                                                <!--end::Details-->
                                                <!--begin::Stats-->
                                                <div class="d-flex">
                                                    <!--begin::Sales-->
                                                    <div class="text-end">
                                                        <div class="fs-5 fw-bold text-dark">$23,000</div>
                                                        <div class="fs-7 text-muted">Sales</div>
                                                    </div>
                                                    <!--end::Sales-->
                                                </div>
                                                <!--end::Stats-->
                                            </div>
                                            <!--end::User-->
                                            <!--begin::User-->
                                            <div
                                                class="d-flex flex-stack py-5 border-bottom border-gray-300 border-bottom-dashed">
                                                <!--begin::Details-->
                                                <div class="d-flex align-items-center">
                                                    <!--begin::Avatar-->
                                                    <div class="symbol symbol-35px symbol-circle">
                                                        <span
                                                            class="symbol-label bg-light-primary text-primary fw-semibold">N</span>
                                                    </div>
                                                    <!--end::Avatar-->
                                                    <!--begin::Details-->
                                                    <div class="ms-6">
                                                        <!--begin::Name-->
                                                        <a href="#"
                                                            class="d-flex align-items-center fs-5 fw-bold text-dark text-hover-primary">Neil
                                                            Owen
                                                            <span
                                                                class="badge badge-light fs-8 fw-semibold ms-2">Account
                                                                Manager</span></a>
                                                        <!--end::Name-->
                                                        <!--begin::Email-->
                                                        <div class="fw-semibold text-muted">owen.neil@gmail.com</div>
                                                        <!--end::Email-->
                                                    </div>
                                                    <!--end::Details-->
                                                </div>
                                                <!--end::Details-->
                                                <!--begin::Stats-->
                                                <div class="d-flex">
                                                    <!--begin::Sales-->
                                                    <div class="text-end">
                                                        <div class="fs-5 fw-bold text-dark">$45,800</div>
                                                        <div class="fs-7 text-muted">Sales</div>
                                                    </div>
                                                    <!--end::Sales-->
                                                </div>
                                                <!--end::Stats-->
                                            </div>
                                            <!--end::User-->
                                            <!--begin::User-->
                                            <div
                                                class="d-flex flex-stack py-5 border-bottom border-gray-300 border-bottom-dashed">
                                                <!--begin::Details-->
                                                <div class="d-flex align-items-center">
                                                    <!--begin::Avatar-->
                                                    <div class="symbol symbol-35px symbol-circle">
                                                        <img alt="Pic" src="assets/media/avatars/300-23.jpg" />
                                                    </div>
                                                    <!--end::Avatar-->
                                                    <!--begin::Details-->
                                                    <div class="ms-6">
                                                        <!--begin::Name-->
                                                        <a href="#"
                                                            class="d-flex align-items-center fs-5 fw-bold text-dark text-hover-primary">Dan
                                                            Wilson
                                                            <span class="badge badge-light fs-8 fw-semibold ms-2">Web
                                                                Desinger</span></a>
                                                        <!--end::Name-->
                                                        <!--begin::Email-->
                                                        <div class="fw-semibold text-muted">dam@consilting.com</div>
                                                        <!--end::Email-->
                                                    </div>
                                                    <!--end::Details-->
                                                </div>
                                                <!--end::Details-->
                                                <!--begin::Stats-->
                                                <div class="d-flex">
                                                    <!--begin::Sales-->
                                                    <div class="text-end">
                                                        <div class="fs-5 fw-bold text-dark">$90,500</div>
                                                        <div class="fs-7 text-muted">Sales</div>
                                                    </div>
                                                    <!--end::Sales-->
                                                </div>
                                                <!--end::Stats-->
                                            </div>
                                            <!--end::User-->
                                            <!--begin::User-->
                                            <div
                                                class="d-flex flex-stack py-5 border-bottom border-gray-300 border-bottom-dashed">
                                                <!--begin::Details-->
                                                <div class="d-flex align-items-center">
                                                    <!--begin::Avatar-->
                                                    <div class="symbol symbol-35px symbol-circle">
                                                        <span
                                                            class="symbol-label bg-light-danger text-danger fw-semibold">E</span>
                                                    </div>
                                                    <!--end::Avatar-->
                                                    <!--begin::Details-->
                                                    <div class="ms-6">
                                                        <!--begin::Name-->
                                                        <a href="#"
                                                            class="d-flex align-items-center fs-5 fw-bold text-dark text-hover-primary">Emma
                                                            Bold
                                                            <span
                                                                class="badge badge-light fs-8 fw-semibold ms-2">Corporate
                                                                Finance</span></a>
                                                        <!--end::Name-->
                                                        <!--begin::Email-->
                                                        <div class="fw-semibold text-muted">emma@intenso.com</div>
                                                        <!--end::Email-->
                                                    </div>
                                                    <!--end::Details-->
                                                </div>
                                                <!--end::Details-->
                                                <!--begin::Stats-->
                                                <div class="d-flex">
                                                    <!--begin::Sales-->
                                                    <div class="text-end">
                                                        <div class="fs-5 fw-bold text-dark">$5,000</div>
                                                        <div class="fs-7 text-muted">Sales</div>
                                                    </div>
                                                    <!--end::Sales-->
                                                </div>
                                                <!--end::Stats-->
                                            </div>
                                            <!--end::User-->
                                            <!--begin::User-->
                                            <div
                                                class="d-flex flex-stack py-5 border-bottom border-gray-300 border-bottom-dashed">
                                                <!--begin::Details-->
                                                <div class="d-flex align-items-center">
                                                    <!--begin::Avatar-->
                                                    <div class="symbol symbol-35px symbol-circle">
                                                        <img alt="Pic" src="assets/media/avatars/300-12.jpg" />
                                                    </div>
                                                    <!--end::Avatar-->
                                                    <!--begin::Details-->
                                                    <div class="ms-6">
                                                        <!--begin::Name-->
                                                        <a href="#"
                                                            class="d-flex align-items-center fs-5 fw-bold text-dark text-hover-primary">Ana
                                                            Crown
                                                            <span
                                                                class="badge badge-light fs-8 fw-semibold ms-2">Customer
                                                                Relationship</span></a>
                                                        <!--end::Name-->
                                                        <!--begin::Email-->
                                                        <div class="fw-semibold text-muted">ana.cf@limtel.com</div>
                                                        <!--end::Email-->
                                                    </div>
                                                    <!--end::Details-->
                                                </div>
                                                <!--end::Details-->
                                                <!--begin::Stats-->
                                                <div class="d-flex">
                                                    <!--begin::Sales-->
                                                    <div class="text-end">
                                                        <div class="fs-5 fw-bold text-dark">$70,000</div>
                                                        <div class="fs-7 text-muted">Sales</div>
                                                    </div>
                                                    <!--end::Sales-->
                                                </div>
                                                <!--end::Stats-->
                                            </div>
                                            <!--end::User-->
                                            <!--begin::User-->
                                            <div class="d-flex flex-stack py-5">
                                                <!--begin::Details-->
                                                <div class="d-flex align-items-center">
                                                    <!--begin::Avatar-->
                                                    <div class="symbol symbol-35px symbol-circle">
                                                        <span
                                                            class="symbol-label bg-light-info text-info fw-semibold">A</span>
                                                    </div>
                                                    <!--end::Avatar-->
                                                    <!--begin::Details-->
                                                    <div class="ms-6">
                                                        <!--begin::Name-->
                                                        <a href="#"
                                                            class="d-flex align-items-center fs-5 fw-bold text-dark text-hover-primary">Robert
                                                            Doe
                                                            <span
                                                                class="badge badge-light fs-8 fw-semibold ms-2">Marketing
                                                                Executive</span></a>
                                                        <!--end::Name-->
                                                        <!--begin::Email-->
                                                        <div class="fw-semibold text-muted">robert@benko.com</div>
                                                        <!--end::Email-->
                                                    </div>
                                                    <!--end::Details-->
                                                </div>
                                                <!--end::Details-->
                                                <!--begin::Stats-->
                                                <div class="d-flex">
                                                    <!--begin::Sales-->
                                                    <div class="text-end">
                                                        <div class="fs-5 fw-bold text-dark">$45,500</div>
                                                        <div class="fs-7 text-muted">Sales</div>
                                                    </div>
                                                    <!--end::Sales-->
                                                </div>
                                                <!--end::Stats-->
                                            </div>
                                            <!--end::User-->
                                        </div>
                                        <!--end::List-->
                                    </div>
                                    <!--end::Users-->
                                    <!--begin::Notice-->
                                    <div class="d-flex justify-content-between">
                                        <!--begin::Label-->
                                        <div class="fw-semibold">
                                            <label class="fs-6">Adding Users by Team Members</label>
                                            <div class="fs-7 text-muted">If you need more info, please check budget
                                                planning</div>
                                        </div>
                                        <!--end::Label-->
                                        <!--begin::Switch-->
                                        <label class="form-check form-switch form-check-custom form-check-solid">
                                            <input class="form-check-input" type="checkbox" value=""
                                                checked="checked" />
                                            <span class="form-check-label fw-semibold text-muted">Allowed</span>
                                        </label>
                                        <!--end::Switch-->
                                    </div>
                                    <!--end::Notice-->
                                </div>
                                <!--end::Modal body-->
                            </div>
                            <!--end::Modal content-->
                        </div>
                        <!--end::Modal dialog-->
                    </div>
                    <!--end::Modal - View Users-->
                    <!--begin::Modal - Users Search-->
                    <div class="modal fade" id="kt_modal_users_search" tabindex="-1" aria-hidden="true">
                        <!--begin::Modal dialog-->
                        <div class="modal-dialog modal-dialog-centered mw-650px">
                            <!--begin::Modal content-->
                            <div class="modal-content">
                                <!--begin::Modal header-->
                                <div class="modal-header pb-0 border-0 justify-content-end">
                                    <!--begin::Close-->
                                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                                        <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
                                        <span class="svg-icon svg-icon-1">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2"
                                                    rx="1" transform="rotate(-45 6 17.3137)"
                                                    fill="currentColor" />
                                                <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                                    transform="rotate(45 7.41422 6)" fill="currentColor" />
                                            </svg>
                                        </span>
                                        <!--end::Svg Icon-->
                                    </div>
                                    <!--end::Close-->
                                </div>
                                <!--begin::Modal header-->
                                <!--begin::Modal body-->
                                <div class="modal-body scroll-y mx-5 mx-xl-18 pt-0 pb-15">
                                    <!--begin::Content-->
                                    <div class="text-center mb-13">
                                        <h1 class="mb-3">Search Users</h1>
                                        <div class="text-muted fw-semibold fs-5">Invite Collaborators To Your Project
                                        </div>
                                    </div>
                                    <!--end::Content-->
                                    <!--begin::Search-->
                                    <div id="kt_modal_users_search_handler" data-kt-search-keypress="true"
                                        data-kt-search-min-length="2" data-kt-search-enter="enter"
                                        data-kt-search-layout="inline">
                                        <!--begin::Form-->
                                        <form data-kt-search-element="form" class="w-100 position-relative mb-5"
                                            autocomplete="off">
                                            <!--begin::Hidden input(Added to disable form autocomplete)-->
                                            <input type="hidden" />
                                            <!--end::Hidden input-->
                                            <!--begin::Icon-->
                                            <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                                            <span
                                                class="svg-icon svg-icon-2 svg-icon-lg-1 svg-icon-gray-500 position-absolute top-50 ms-5 translate-middle-y">
                                                <svg width="24" height="24" viewBox="0 0 24 24"
                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546"
                                                        height="2" rx="1"
                                                        transform="rotate(45 17.0365 15.1223)" fill="currentColor" />
                                                    <path
                                                        d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z"
                                                        fill="currentColor" />
                                                </svg>
                                            </span>
                                            <!--end::Svg Icon-->
                                            <!--end::Icon-->
                                            <!--begin::Input-->
                                            <input type="text"
                                                class="form-control form-control-lg form-control-solid px-15"
                                                name="search" value=""
                                                placeholder="Search by username, full name or email..."
                                                data-kt-search-element="input" />
                                            <!--end::Input-->
                                            <!--begin::Spinner-->
                                            <span
                                                class="position-absolute top-50 end-0 translate-middle-y lh-0 d-none me-5"
                                                data-kt-search-element="spinner">
                                                <span
                                                    class="spinner-border h-15px w-15px align-middle text-muted"></span>
                                            </span>
                                            <!--end::Spinner-->
                                            <!--begin::Reset-->
                                            <span
                                                class="btn btn-flush btn-active-color-primary position-absolute top-50 end-0 translate-middle-y lh-0 me-5 d-none"
                                                data-kt-search-element="clear">
                                                <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
                                                <span class="svg-icon svg-icon-2 svg-icon-lg-1 me-0">
                                                    <svg width="24" height="24" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <rect opacity="0.5" x="6" y="17.3137" width="16"
                                                            height="2" rx="1"
                                                            transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                                        <rect x="7.41422" y="6" width="16" height="2"
                                                            rx="1" transform="rotate(45 7.41422 6)"
                                                            fill="currentColor" />
                                                    </svg>
                                                </span>
                                                <!--end::Svg Icon-->
                                            </span>
                                            <!--end::Reset-->
                                        </form>
                                        <!--end::Form-->
                                        <!--begin::Wrapper-->
                                        <div class="py-5">
                                            <!--begin::Suggestions-->
                                            <div data-kt-search-element="suggestions">
                                                <!--begin::Heading-->
                                                <h3 class="fw-semibold mb-5">Recently searched:</h3>
                                                <!--end::Heading-->
                                                <!--begin::Users-->
                                                <div class="mh-375px scroll-y me-n7 pe-7">
                                                    <!--begin::User-->
                                                    <a href="#"
                                                        class="d-flex align-items-center p-3 rounded bg-state-light bg-state-opacity-50 mb-1">
                                                        <!--begin::Avatar-->
                                                        <div class="symbol symbol-35px symbol-circle me-5">
                                                            <img alt="Pic"
                                                                src="assets/media/avatars/300-6.jpg" />
                                                        </div>
                                                        <!--end::Avatar-->
                                                        <!--begin::Info-->
                                                        <div class="fw-semibold">
                                                            <span class="fs-6 text-gray-800 me-2">Emma Smith</span>
                                                            <span class="badge badge-light">Art Director</span>
                                                        </div>
                                                        <!--end::Info-->
                                                    </a>
                                                    <!--end::User-->
                                                    <!--begin::User-->
                                                    <a href="#"
                                                        class="d-flex align-items-center p-3 rounded bg-state-light bg-state-opacity-50 mb-1">
                                                        <!--begin::Avatar-->
                                                        <div class="symbol symbol-35px symbol-circle me-5">
                                                            <span
                                                                class="symbol-label bg-light-danger text-danger fw-semibold">M</span>
                                                        </div>
                                                        <!--end::Avatar-->
                                                        <!--begin::Info-->
                                                        <div class="fw-semibold">
                                                            <span class="fs-6 text-gray-800 me-2">Melody Macy</span>
                                                            <span class="badge badge-light">Marketing Analytic</span>
                                                        </div>
                                                        <!--end::Info-->
                                                    </a>
                                                    <!--end::User-->
                                                    <!--begin::User-->
                                                    <a href="#"
                                                        class="d-flex align-items-center p-3 rounded bg-state-light bg-state-opacity-50 mb-1">
                                                        <!--begin::Avatar-->
                                                        <div class="symbol symbol-35px symbol-circle me-5">
                                                            <img alt="Pic"
                                                                src="assets/media/avatars/300-1.jpg" />
                                                        </div>
                                                        <!--end::Avatar-->
                                                        <!--begin::Info-->
                                                        <div class="fw-semibold">
                                                            <span class="fs-6 text-gray-800 me-2">Max Smith</span>
                                                            <span class="badge badge-light">Software Enginer</span>
                                                        </div>
                                                        <!--end::Info-->
                                                    </a>
                                                    <!--end::User-->
                                                    <!--begin::User-->
                                                    <a href="#"
                                                        class="d-flex align-items-center p-3 rounded bg-state-light bg-state-opacity-50 mb-1">
                                                        <!--begin::Avatar-->
                                                        <div class="symbol symbol-35px symbol-circle me-5">
                                                            <img alt="Pic"
                                                                src="assets/media/avatars/300-5.jpg" />
                                                        </div>
                                                        <!--end::Avatar-->
                                                        <!--begin::Info-->
                                                        <div class="fw-semibold">
                                                            <span class="fs-6 text-gray-800 me-2">Sean Bean</span>
                                                            <span class="badge badge-light">Web Developer</span>
                                                        </div>
                                                        <!--end::Info-->
                                                    </a>
                                                    <!--end::User-->
                                                    <!--begin::User-->
                                                    <a href="#"
                                                        class="d-flex align-items-center p-3 rounded bg-state-light bg-state-opacity-50 mb-1">
                                                        <!--begin::Avatar-->
                                                        <div class="symbol symbol-35px symbol-circle me-5">
                                                            <img alt="Pic"
                                                                src="assets/media/avatars/300-25.jpg" />
                                                        </div>
                                                        <!--end::Avatar-->
                                                        <!--begin::Info-->
                                                        <div class="fw-semibold">
                                                            <span class="fs-6 text-gray-800 me-2">Brian Cox</span>
                                                            <span class="badge badge-light">UI/UX Designer</span>
                                                        </div>
                                                        <!--end::Info-->
                                                    </a>
                                                    <!--end::User-->
                                                </div>
                                                <!--end::Users-->
                                            </div>
                                            <!--end::Suggestions-->
                                            <!--begin::Results(add d-none to below element to hide the users list by default)-->
                                            <div data-kt-search-element="results" class="d-none">
                                                <!--begin::Users-->
                                                <div class="mh-375px scroll-y me-n7 pe-7">
                                                    <!--begin::User-->
                                                    <div class="rounded d-flex flex-stack bg-active-lighten p-4"
                                                        data-user-id="0">
                                                        <!--begin::Details-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Checkbox-->
                                                            <label
                                                                class="form-check form-check-custom form-check-solid me-5">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="users" data-kt-check="true"
                                                                    data-kt-check-target="[data-user-id='0']"
                                                                    value="0" />
                                                            </label>
                                                            <!--end::Checkbox-->
                                                            <!--begin::Avatar-->
                                                            <div class="symbol symbol-35px symbol-circle">
                                                                <img alt="Pic"
                                                                    src="assets/media/avatars/300-6.jpg" />
                                                            </div>
                                                            <!--end::Avatar-->
                                                            <!--begin::Details-->
                                                            <div class="ms-5">
                                                                <a href="#"
                                                                    class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Emma
                                                                    Smith</a>
                                                                <div class="fw-semibold text-muted">smith@kpmg.com
                                                                </div>
                                                            </div>
                                                            <!--end::Details-->
                                                        </div>
                                                        <!--end::Details-->
                                                        <!--begin::Access menu-->
                                                        <div class="ms-2 w-100px">
                                                            <select
                                                                class="form-select form-select-solid form-select-sm"
                                                                data-control="select2" data-hide-search="true">
                                                                <option value="1">Guest</option>
                                                                <option value="2" selected="selected">Owner
                                                                </option>
                                                                <option value="3">Can Edit</option>
                                                            </select>
                                                        </div>
                                                        <!--end::Access menu-->
                                                    </div>
                                                    <!--end::User-->
                                                    <!--begin::Separator-->
                                                    <div class="border-bottom border-gray-300 border-bottom-dashed">
                                                    </div>
                                                    <!--end::Separator-->
                                                    <!--begin::User-->
                                                    <div class="rounded d-flex flex-stack bg-active-lighten p-4"
                                                        data-user-id="1">
                                                        <!--begin::Details-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Checkbox-->
                                                            <label
                                                                class="form-check form-check-custom form-check-solid me-5">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="users" data-kt-check="true"
                                                                    data-kt-check-target="[data-user-id='1']"
                                                                    value="1" />
                                                            </label>
                                                            <!--end::Checkbox-->
                                                            <!--begin::Avatar-->
                                                            <div class="symbol symbol-35px symbol-circle">
                                                                <span
                                                                    class="symbol-label bg-light-danger text-danger fw-semibold">M</span>
                                                            </div>
                                                            <!--end::Avatar-->
                                                            <!--begin::Details-->
                                                            <div class="ms-5">
                                                                <a href="#"
                                                                    class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Melody
                                                                    Macy</a>
                                                                <div class="fw-semibold text-muted">melody@altbox.com
                                                                </div>
                                                            </div>
                                                            <!--end::Details-->
                                                        </div>
                                                        <!--end::Details-->
                                                        <!--begin::Access menu-->
                                                        <div class="ms-2 w-100px">
                                                            <select
                                                                class="form-select form-select-solid form-select-sm"
                                                                data-control="select2" data-hide-search="true">
                                                                <option value="1" selected="selected">Guest
                                                                </option>
                                                                <option value="2">Owner</option>
                                                                <option value="3">Can Edit</option>
                                                            </select>
                                                        </div>
                                                        <!--end::Access menu-->
                                                    </div>
                                                    <!--end::User-->
                                                    <!--begin::Separator-->
                                                    <div class="border-bottom border-gray-300 border-bottom-dashed">
                                                    </div>
                                                    <!--end::Separator-->
                                                    <!--begin::User-->
                                                    <div class="rounded d-flex flex-stack bg-active-lighten p-4"
                                                        data-user-id="2">
                                                        <!--begin::Details-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Checkbox-->
                                                            <label
                                                                class="form-check form-check-custom form-check-solid me-5">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="users" data-kt-check="true"
                                                                    data-kt-check-target="[data-user-id='2']"
                                                                    value="2" />
                                                            </label>
                                                            <!--end::Checkbox-->
                                                            <!--begin::Avatar-->
                                                            <div class="symbol symbol-35px symbol-circle">
                                                                <img alt="Pic"
                                                                    src="assets/media/avatars/300-1.jpg" />
                                                            </div>
                                                            <!--end::Avatar-->
                                                            <!--begin::Details-->
                                                            <div class="ms-5">
                                                                <a href="#"
                                                                    class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Max
                                                                    Smith</a>
                                                                <div class="fw-semibold text-muted">max@kt.com</div>
                                                            </div>
                                                            <!--end::Details-->
                                                        </div>
                                                        <!--end::Details-->
                                                        <!--begin::Access menu-->
                                                        <div class="ms-2 w-100px">
                                                            <select
                                                                class="form-select form-select-solid form-select-sm"
                                                                data-control="select2" data-hide-search="true">
                                                                <option value="1">Guest</option>
                                                                <option value="2">Owner</option>
                                                                <option value="3" selected="selected">Can Edit
                                                                </option>
                                                            </select>
                                                        </div>
                                                        <!--end::Access menu-->
                                                    </div>
                                                    <!--end::User-->
                                                    <!--begin::Separator-->
                                                    <div class="border-bottom border-gray-300 border-bottom-dashed">
                                                    </div>
                                                    <!--end::Separator-->
                                                    <!--begin::User-->
                                                    <div class="rounded d-flex flex-stack bg-active-lighten p-4"
                                                        data-user-id="3">
                                                        <!--begin::Details-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Checkbox-->
                                                            <label
                                                                class="form-check form-check-custom form-check-solid me-5">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="users" data-kt-check="true"
                                                                    data-kt-check-target="[data-user-id='3']"
                                                                    value="3" />
                                                            </label>
                                                            <!--end::Checkbox-->
                                                            <!--begin::Avatar-->
                                                            <div class="symbol symbol-35px symbol-circle">
                                                                <img alt="Pic"
                                                                    src="assets/media/avatars/300-5.jpg" />
                                                            </div>
                                                            <!--end::Avatar-->
                                                            <!--begin::Details-->
                                                            <div class="ms-5">
                                                                <a href="#"
                                                                    class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Sean
                                                                    Bean</a>
                                                                <div class="fw-semibold text-muted">sean@dellito.com
                                                                </div>
                                                            </div>
                                                            <!--end::Details-->
                                                        </div>
                                                        <!--end::Details-->
                                                        <!--begin::Access menu-->
                                                        <div class="ms-2 w-100px">
                                                            <select
                                                                class="form-select form-select-solid form-select-sm"
                                                                data-control="select2" data-hide-search="true">
                                                                <option value="1">Guest</option>
                                                                <option value="2" selected="selected">Owner
                                                                </option>
                                                                <option value="3">Can Edit</option>
                                                            </select>
                                                        </div>
                                                        <!--end::Access menu-->
                                                    </div>
                                                    <!--end::User-->
                                                    <!--begin::Separator-->
                                                    <div class="border-bottom border-gray-300 border-bottom-dashed">
                                                    </div>
                                                    <!--end::Separator-->
                                                    <!--begin::User-->
                                                    <div class="rounded d-flex flex-stack bg-active-lighten p-4"
                                                        data-user-id="4">
                                                        <!--begin::Details-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Checkbox-->
                                                            <label
                                                                class="form-check form-check-custom form-check-solid me-5">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="users" data-kt-check="true"
                                                                    data-kt-check-target="[data-user-id='4']"
                                                                    value="4" />
                                                            </label>
                                                            <!--end::Checkbox-->
                                                            <!--begin::Avatar-->
                                                            <div class="symbol symbol-35px symbol-circle">
                                                                <img alt="Pic"
                                                                    src="assets/media/avatars/300-25.jpg" />
                                                            </div>
                                                            <!--end::Avatar-->
                                                            <!--begin::Details-->
                                                            <div class="ms-5">
                                                                <a href="#"
                                                                    class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Brian
                                                                    Cox</a>
                                                                <div class="fw-semibold text-muted">brian@exchange.com
                                                                </div>
                                                            </div>
                                                            <!--end::Details-->
                                                        </div>
                                                        <!--end::Details-->
                                                        <!--begin::Access menu-->
                                                        <div class="ms-2 w-100px">
                                                            <select
                                                                class="form-select form-select-solid form-select-sm"
                                                                data-control="select2" data-hide-search="true">
                                                                <option value="1">Guest</option>
                                                                <option value="2">Owner</option>
                                                                <option value="3" selected="selected">Can Edit
                                                                </option>
                                                            </select>
                                                        </div>
                                                        <!--end::Access menu-->
                                                    </div>
                                                    <!--end::User-->
                                                    <!--begin::Separator-->
                                                    <div class="border-bottom border-gray-300 border-bottom-dashed">
                                                    </div>
                                                    <!--end::Separator-->
                                                    <!--begin::User-->
                                                    <div class="rounded d-flex flex-stack bg-active-lighten p-4"
                                                        data-user-id="5">
                                                        <!--begin::Details-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Checkbox-->
                                                            <label
                                                                class="form-check form-check-custom form-check-solid me-5">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="users" data-kt-check="true"
                                                                    data-kt-check-target="[data-user-id='5']"
                                                                    value="5" />
                                                            </label>
                                                            <!--end::Checkbox-->
                                                            <!--begin::Avatar-->
                                                            <div class="symbol symbol-35px symbol-circle">
                                                                <span
                                                                    class="symbol-label bg-light-warning text-warning fw-semibold">C</span>
                                                            </div>
                                                            <!--end::Avatar-->
                                                            <!--begin::Details-->
                                                            <div class="ms-5">
                                                                <a href="#"
                                                                    class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Mikaela
                                                                    Collins</a>
                                                                <div class="fw-semibold text-muted">mik@pex.com</div>
                                                            </div>
                                                            <!--end::Details-->
                                                        </div>
                                                        <!--end::Details-->
                                                        <!--begin::Access menu-->
                                                        <div class="ms-2 w-100px">
                                                            <select
                                                                class="form-select form-select-solid form-select-sm"
                                                                data-control="select2" data-hide-search="true">
                                                                <option value="1">Guest</option>
                                                                <option value="2" selected="selected">Owner
                                                                </option>
                                                                <option value="3">Can Edit</option>
                                                            </select>
                                                        </div>
                                                        <!--end::Access menu-->
                                                    </div>
                                                    <!--end::User-->
                                                    <!--begin::Separator-->
                                                    <div class="border-bottom border-gray-300 border-bottom-dashed">
                                                    </div>
                                                    <!--end::Separator-->
                                                    <!--begin::User-->
                                                    <div class="rounded d-flex flex-stack bg-active-lighten p-4"
                                                        data-user-id="6">
                                                        <!--begin::Details-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Checkbox-->
                                                            <label
                                                                class="form-check form-check-custom form-check-solid me-5">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="users" data-kt-check="true"
                                                                    data-kt-check-target="[data-user-id='6']"
                                                                    value="6" />
                                                            </label>
                                                            <!--end::Checkbox-->
                                                            <!--begin::Avatar-->
                                                            <div class="symbol symbol-35px symbol-circle">
                                                                <img alt="Pic"
                                                                    src="assets/media/avatars/300-9.jpg" />
                                                            </div>
                                                            <!--end::Avatar-->
                                                            <!--begin::Details-->
                                                            <div class="ms-5">
                                                                <a href="#"
                                                                    class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Francis
                                                                    Mitcham</a>
                                                                <div class="fw-semibold text-muted">f.mit@kpmg.com
                                                                </div>
                                                            </div>
                                                            <!--end::Details-->
                                                        </div>
                                                        <!--end::Details-->
                                                        <!--begin::Access menu-->
                                                        <div class="ms-2 w-100px">
                                                            <select
                                                                class="form-select form-select-solid form-select-sm"
                                                                data-control="select2" data-hide-search="true">
                                                                <option value="1">Guest</option>
                                                                <option value="2">Owner</option>
                                                                <option value="3" selected="selected">Can Edit
                                                                </option>
                                                            </select>
                                                        </div>
                                                        <!--end::Access menu-->
                                                    </div>
                                                    <!--end::User-->
                                                    <!--begin::Separator-->
                                                    <div class="border-bottom border-gray-300 border-bottom-dashed">
                                                    </div>
                                                    <!--end::Separator-->
                                                    <!--begin::User-->
                                                    <div class="rounded d-flex flex-stack bg-active-lighten p-4"
                                                        data-user-id="7">
                                                        <!--begin::Details-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Checkbox-->
                                                            <label
                                                                class="form-check form-check-custom form-check-solid me-5">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="users" data-kt-check="true"
                                                                    data-kt-check-target="[data-user-id='7']"
                                                                    value="7" />
                                                            </label>
                                                            <!--end::Checkbox-->
                                                            <!--begin::Avatar-->
                                                            <div class="symbol symbol-35px symbol-circle">
                                                                <span
                                                                    class="symbol-label bg-light-danger text-danger fw-semibold">O</span>
                                                            </div>
                                                            <!--end::Avatar-->
                                                            <!--begin::Details-->
                                                            <div class="ms-5">
                                                                <a href="#"
                                                                    class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Olivia
                                                                    Wild</a>
                                                                <div class="fw-semibold text-muted">
                                                                    olivia@corpmail.com</div>
                                                            </div>
                                                            <!--end::Details-->
                                                        </div>
                                                        <!--end::Details-->
                                                        <!--begin::Access menu-->
                                                        <div class="ms-2 w-100px">
                                                            <select
                                                                class="form-select form-select-solid form-select-sm"
                                                                data-control="select2" data-hide-search="true">
                                                                <option value="1">Guest</option>
                                                                <option value="2" selected="selected">Owner
                                                                </option>
                                                                <option value="3">Can Edit</option>
                                                            </select>
                                                        </div>
                                                        <!--end::Access menu-->
                                                    </div>
                                                    <!--end::User-->
                                                    <!--begin::Separator-->
                                                    <div class="border-bottom border-gray-300 border-bottom-dashed">
                                                    </div>
                                                    <!--end::Separator-->
                                                    <!--begin::User-->
                                                    <div class="rounded d-flex flex-stack bg-active-lighten p-4"
                                                        data-user-id="8">
                                                        <!--begin::Details-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Checkbox-->
                                                            <label
                                                                class="form-check form-check-custom form-check-solid me-5">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="users" data-kt-check="true"
                                                                    data-kt-check-target="[data-user-id='8']"
                                                                    value="8" />
                                                            </label>
                                                            <!--end::Checkbox-->
                                                            <!--begin::Avatar-->
                                                            <div class="symbol symbol-35px symbol-circle">
                                                                <span
                                                                    class="symbol-label bg-light-primary text-primary fw-semibold">N</span>
                                                            </div>
                                                            <!--end::Avatar-->
                                                            <!--begin::Details-->
                                                            <div class="ms-5">
                                                                <a href="#"
                                                                    class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Neil
                                                                    Owen</a>
                                                                <div class="fw-semibold text-muted">
                                                                    owen.neil@gmail.com</div>
                                                            </div>
                                                            <!--end::Details-->
                                                        </div>
                                                        <!--end::Details-->
                                                        <!--begin::Access menu-->
                                                        <div class="ms-2 w-100px">
                                                            <select
                                                                class="form-select form-select-solid form-select-sm"
                                                                data-control="select2" data-hide-search="true">
                                                                <option value="1" selected="selected">Guest
                                                                </option>
                                                                <option value="2">Owner</option>
                                                                <option value="3">Can Edit</option>
                                                            </select>
                                                        </div>
                                                        <!--end::Access menu-->
                                                    </div>
                                                    <!--end::User-->
                                                    <!--begin::Separator-->
                                                    <div class="border-bottom border-gray-300 border-bottom-dashed">
                                                    </div>
                                                    <!--end::Separator-->
                                                    <!--begin::User-->
                                                    <div class="rounded d-flex flex-stack bg-active-lighten p-4"
                                                        data-user-id="9">
                                                        <!--begin::Details-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Checkbox-->
                                                            <label
                                                                class="form-check form-check-custom form-check-solid me-5">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="users" data-kt-check="true"
                                                                    data-kt-check-target="[data-user-id='9']"
                                                                    value="9" />
                                                            </label>
                                                            <!--end::Checkbox-->
                                                            <!--begin::Avatar-->
                                                            <div class="symbol symbol-35px symbol-circle">
                                                                <img alt="Pic"
                                                                    src="assets/media/avatars/300-23.jpg" />
                                                            </div>
                                                            <!--end::Avatar-->
                                                            <!--begin::Details-->
                                                            <div class="ms-5">
                                                                <a href="#"
                                                                    class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Dan
                                                                    Wilson</a>
                                                                <div class="fw-semibold text-muted">dam@consilting.com
                                                                </div>
                                                            </div>
                                                            <!--end::Details-->
                                                        </div>
                                                        <!--end::Details-->
                                                        <!--begin::Access menu-->
                                                        <div class="ms-2 w-100px">
                                                            <select
                                                                class="form-select form-select-solid form-select-sm"
                                                                data-control="select2" data-hide-search="true">
                                                                <option value="1">Guest</option>
                                                                <option value="2">Owner</option>
                                                                <option value="3" selected="selected">Can Edit
                                                                </option>
                                                            </select>
                                                        </div>
                                                        <!--end::Access menu-->
                                                    </div>
                                                    <!--end::User-->
                                                    <!--begin::Separator-->
                                                    <div class="border-bottom border-gray-300 border-bottom-dashed">
                                                    </div>
                                                    <!--end::Separator-->
                                                    <!--begin::User-->
                                                    <div class="rounded d-flex flex-stack bg-active-lighten p-4"
                                                        data-user-id="10">
                                                        <!--begin::Details-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Checkbox-->
                                                            <label
                                                                class="form-check form-check-custom form-check-solid me-5">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="users" data-kt-check="true"
                                                                    data-kt-check-target="[data-user-id='10']"
                                                                    value="10" />
                                                            </label>
                                                            <!--end::Checkbox-->
                                                            <!--begin::Avatar-->
                                                            <div class="symbol symbol-35px symbol-circle">
                                                                <span
                                                                    class="symbol-label bg-light-danger text-danger fw-semibold">E</span>
                                                            </div>
                                                            <!--end::Avatar-->
                                                            <!--begin::Details-->
                                                            <div class="ms-5">
                                                                <a href="#"
                                                                    class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Emma
                                                                    Bold</a>
                                                                <div class="fw-semibold text-muted">emma@intenso.com
                                                                </div>
                                                            </div>
                                                            <!--end::Details-->
                                                        </div>
                                                        <!--end::Details-->
                                                        <!--begin::Access menu-->
                                                        <div class="ms-2 w-100px">
                                                            <select
                                                                class="form-select form-select-solid form-select-sm"
                                                                data-control="select2" data-hide-search="true">
                                                                <option value="1">Guest</option>
                                                                <option value="2" selected="selected">Owner
                                                                </option>
                                                                <option value="3">Can Edit</option>
                                                            </select>
                                                        </div>
                                                        <!--end::Access menu-->
                                                    </div>
                                                    <!--end::User-->
                                                    <!--begin::Separator-->
                                                    <div class="border-bottom border-gray-300 border-bottom-dashed">
                                                    </div>
                                                    <!--end::Separator-->
                                                    <!--begin::User-->
                                                    <div class="rounded d-flex flex-stack bg-active-lighten p-4"
                                                        data-user-id="11">
                                                        <!--begin::Details-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Checkbox-->
                                                            <label
                                                                class="form-check form-check-custom form-check-solid me-5">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="users" data-kt-check="true"
                                                                    data-kt-check-target="[data-user-id='11']"
                                                                    value="11" />
                                                            </label>
                                                            <!--end::Checkbox-->
                                                            <!--begin::Avatar-->
                                                            <div class="symbol symbol-35px symbol-circle">
                                                                <img alt="Pic"
                                                                    src="assets/media/avatars/300-12.jpg" />
                                                            </div>
                                                            <!--end::Avatar-->
                                                            <!--begin::Details-->
                                                            <div class="ms-5">
                                                                <a href="#"
                                                                    class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Ana
                                                                    Crown</a>
                                                                <div class="fw-semibold text-muted">ana.cf@limtel.com
                                                                </div>
                                                            </div>
                                                            <!--end::Details-->
                                                        </div>
                                                        <!--end::Details-->
                                                        <!--begin::Access menu-->
                                                        <div class="ms-2 w-100px">
                                                            <select
                                                                class="form-select form-select-solid form-select-sm"
                                                                data-control="select2" data-hide-search="true">
                                                                <option value="1" selected="selected">Guest
                                                                </option>
                                                                <option value="2">Owner</option>
                                                                <option value="3">Can Edit</option>
                                                            </select>
                                                        </div>
                                                        <!--end::Access menu-->
                                                    </div>
                                                    <!--end::User-->
                                                    <!--begin::Separator-->
                                                    <div class="border-bottom border-gray-300 border-bottom-dashed">
                                                    </div>
                                                    <!--end::Separator-->
                                                    <!--begin::User-->
                                                    <div class="rounded d-flex flex-stack bg-active-lighten p-4"
                                                        data-user-id="12">
                                                        <!--begin::Details-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Checkbox-->
                                                            <label
                                                                class="form-check form-check-custom form-check-solid me-5">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="users" data-kt-check="true"
                                                                    data-kt-check-target="[data-user-id='12']"
                                                                    value="12" />
                                                            </label>
                                                            <!--end::Checkbox-->
                                                            <!--begin::Avatar-->
                                                            <div class="symbol symbol-35px symbol-circle">
                                                                <span
                                                                    class="symbol-label bg-light-info text-info fw-semibold">A</span>
                                                            </div>
                                                            <!--end::Avatar-->
                                                            <!--begin::Details-->
                                                            <div class="ms-5">
                                                                <a href="#"
                                                                    class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Robert
                                                                    Doe</a>
                                                                <div class="fw-semibold text-muted">robert@benko.com
                                                                </div>
                                                            </div>
                                                            <!--end::Details-->
                                                        </div>
                                                        <!--end::Details-->
                                                        <!--begin::Access menu-->
                                                        <div class="ms-2 w-100px">
                                                            <select
                                                                class="form-select form-select-solid form-select-sm"
                                                                data-control="select2" data-hide-search="true">
                                                                <option value="1">Guest</option>
                                                                <option value="2">Owner</option>
                                                                <option value="3" selected="selected">Can Edit
                                                                </option>
                                                            </select>
                                                        </div>
                                                        <!--end::Access menu-->
                                                    </div>
                                                    <!--end::User-->
                                                    <!--begin::Separator-->
                                                    <div class="border-bottom border-gray-300 border-bottom-dashed">
                                                    </div>
                                                    <!--end::Separator-->
                                                    <!--begin::User-->
                                                    <div class="rounded d-flex flex-stack bg-active-lighten p-4"
                                                        data-user-id="13">
                                                        <!--begin::Details-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Checkbox-->
                                                            <label
                                                                class="form-check form-check-custom form-check-solid me-5">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="users" data-kt-check="true"
                                                                    data-kt-check-target="[data-user-id='13']"
                                                                    value="13" />
                                                            </label>
                                                            <!--end::Checkbox-->
                                                            <!--begin::Avatar-->
                                                            <div class="symbol symbol-35px symbol-circle">
                                                                <img alt="Pic"
                                                                    src="assets/media/avatars/300-13.jpg" />
                                                            </div>
                                                            <!--end::Avatar-->
                                                            <!--begin::Details-->
                                                            <div class="ms-5">
                                                                <a href="#"
                                                                    class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">John
                                                                    Miller</a>
                                                                <div class="fw-semibold text-muted">miller@mapple.com
                                                                </div>
                                                            </div>
                                                            <!--end::Details-->
                                                        </div>
                                                        <!--end::Details-->
                                                        <!--begin::Access menu-->
                                                        <div class="ms-2 w-100px">
                                                            <select
                                                                class="form-select form-select-solid form-select-sm"
                                                                data-control="select2" data-hide-search="true">
                                                                <option value="1">Guest</option>
                                                                <option value="2">Owner</option>
                                                                <option value="3" selected="selected">Can Edit
                                                                </option>
                                                            </select>
                                                        </div>
                                                        <!--end::Access menu-->
                                                    </div>
                                                    <!--end::User-->
                                                    <!--begin::Separator-->
                                                    <div class="border-bottom border-gray-300 border-bottom-dashed">
                                                    </div>
                                                    <!--end::Separator-->
                                                    <!--begin::User-->
                                                    <div class="rounded d-flex flex-stack bg-active-lighten p-4"
                                                        data-user-id="14">
                                                        <!--begin::Details-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Checkbox-->
                                                            <label
                                                                class="form-check form-check-custom form-check-solid me-5">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="users" data-kt-check="true"
                                                                    data-kt-check-target="[data-user-id='14']"
                                                                    value="14" />
                                                            </label>
                                                            <!--end::Checkbox-->
                                                            <!--begin::Avatar-->
                                                            <div class="symbol symbol-35px symbol-circle">
                                                                <span
                                                                    class="symbol-label bg-light-success text-success fw-semibold">L</span>
                                                            </div>
                                                            <!--end::Avatar-->
                                                            <!--begin::Details-->
                                                            <div class="ms-5">
                                                                <a href="#"
                                                                    class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Lucy
                                                                    Kunic</a>
                                                                <div class="fw-semibold text-muted">lucy.m@fentech.com
                                                                </div>
                                                            </div>
                                                            <!--end::Details-->
                                                        </div>
                                                        <!--end::Details-->
                                                        <!--begin::Access menu-->
                                                        <div class="ms-2 w-100px">
                                                            <select
                                                                class="form-select form-select-solid form-select-sm"
                                                                data-control="select2" data-hide-search="true">
                                                                <option value="1">Guest</option>
                                                                <option value="2" selected="selected">Owner
                                                                </option>
                                                                <option value="3">Can Edit</option>
                                                            </select>
                                                        </div>
                                                        <!--end::Access menu-->
                                                    </div>
                                                    <!--end::User-->
                                                    <!--begin::Separator-->
                                                    <div class="border-bottom border-gray-300 border-bottom-dashed">
                                                    </div>
                                                    <!--end::Separator-->
                                                    <!--begin::User-->
                                                    <div class="rounded d-flex flex-stack bg-active-lighten p-4"
                                                        data-user-id="15">
                                                        <!--begin::Details-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Checkbox-->
                                                            <label
                                                                class="form-check form-check-custom form-check-solid me-5">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="users" data-kt-check="true"
                                                                    data-kt-check-target="[data-user-id='15']"
                                                                    value="15" />
                                                            </label>
                                                            <!--end::Checkbox-->
                                                            <!--begin::Avatar-->
                                                            <div class="symbol symbol-35px symbol-circle">
                                                                <img alt="Pic"
                                                                    src="assets/media/avatars/300-21.jpg" />
                                                            </div>
                                                            <!--end::Avatar-->
                                                            <!--begin::Details-->
                                                            <div class="ms-5">
                                                                <a href="#"
                                                                    class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Ethan
                                                                    Wilder</a>
                                                                <div class="fw-semibold text-muted">ethan@loop.com.au
                                                                </div>
                                                            </div>
                                                            <!--end::Details-->
                                                        </div>
                                                        <!--end::Details-->
                                                        <!--begin::Access menu-->
                                                        <div class="ms-2 w-100px">
                                                            <select
                                                                class="form-select form-select-solid form-select-sm"
                                                                data-control="select2" data-hide-search="true">
                                                                <option value="1" selected="selected">Guest
                                                                </option>
                                                                <option value="2">Owner</option>
                                                                <option value="3">Can Edit</option>
                                                            </select>
                                                        </div>
                                                        <!--end::Access menu-->
                                                    </div>
                                                    <!--end::User-->
                                                    <!--begin::Separator-->
                                                    <div class="border-bottom border-gray-300 border-bottom-dashed">
                                                    </div>
                                                    <!--end::Separator-->
                                                    <!--begin::User-->
                                                    <div class="rounded d-flex flex-stack bg-active-lighten p-4"
                                                        data-user-id="16">
                                                        <!--begin::Details-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Checkbox-->
                                                            <label
                                                                class="form-check form-check-custom form-check-solid me-5">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="users" data-kt-check="true"
                                                                    data-kt-check-target="[data-user-id='16']"
                                                                    value="16" />
                                                            </label>
                                                            <!--end::Checkbox-->
                                                            <!--begin::Avatar-->
                                                            <div class="symbol symbol-35px symbol-circle">
                                                                <img alt="Pic"
                                                                    src="assets/media/avatars/300-12.jpg" />
                                                            </div>
                                                            <!--end::Avatar-->
                                                            <!--begin::Details-->
                                                            <div class="ms-5">
                                                                <a href="#"
                                                                    class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Ana
                                                                    Crown</a>
                                                                <div class="fw-semibold text-muted">ana.cf@limtel.com
                                                                </div>
                                                            </div>
                                                            <!--end::Details-->
                                                        </div>
                                                        <!--end::Details-->
                                                        <!--begin::Access menu-->
                                                        <div class="ms-2 w-100px">
                                                            <select
                                                                class="form-select form-select-solid form-select-sm"
                                                                data-control="select2" data-hide-search="true">
                                                                <option value="1">Guest</option>
                                                                <option value="2">Owner</option>
                                                                <option value="3" selected="selected">Can Edit
                                                                </option>
                                                            </select>
                                                        </div>
                                                        <!--end::Access menu-->
                                                    </div>
                                                    <!--end::User-->
                                                </div>
                                                <!--end::Users-->
                                                <!--begin::Actions-->
                                                <div class="d-flex flex-center mt-15">
                                                    <button type="reset" id="kt_modal_users_search_reset"
                                                        data-bs-dismiss="modal"
                                                        class="btn btn-active-light me-3">Cancel</button>
                                                    <button type="submit" id="kt_modal_users_search_submit"
                                                        class="btn btn-primary">Add Selected Users</button>
                                                </div>
                                                <!--end::Actions-->
                                            </div>
                                            <!--end::Results-->
                                            <!--begin::Empty-->
                                            <div data-kt-search-element="empty" class="text-center d-none">
                                                <!--begin::Message-->
                                                <div class="fw-semibold py-10">
                                                    <div class="text-gray-600 fs-3 mb-2">No users found</div>
                                                    <div class="text-muted fs-6">Try to search by username, full name
                                                        or email...</div>
                                                </div>
                                                <!--end::Message-->
                                                <!--begin::Illustration-->
                                                <div class="text-center px-5">
                                                    <img src="assets/media/illustrations/sketchy-1/1.png"
                                                        alt="" class="w-100 h-200px h-sm-325px" />
                                                </div>
                                                <!--end::Illustration-->
                                            </div>
                                            <!--end::Empty-->
                                        </div>
                                        <!--end::Wrapper-->
                                    </div>
                                    <!--end::Search-->
                                </div>
                                <!--end::Modal body-->
                            </div>
                            <!--end::Modal content-->
                        </div>
                        <!--end::Modal dialog-->
                    </div>
                    <!--end::Modal - Users Search-->
                    <!--end::Modals-->
                </div>
                <!--end::Content container-->
            </div>

        </div>
    </div>
    </div>

</x-tenant-app-layout>

<!--begin::Vendors Javascript(used for this page only)-->
<script src="assets/plugins/custom/datatables/datatables.bundle.js"></script>
<!--end::Vendors Javascript-->
<!--begin::Custom Javascript(used for this page only)-->
<script src="assets/js/custom/apps/projects/list/list.js"></script>
<script src="assets/js/widgets.bundle.js"></script>
<script src="assets/js/custom/apps/chat/chat.js"></script>
<script src="assets/js/custom/utilities/modals/upgrade-plan.js"></script>
<script src="assets/js/custom/utilities/modals/create-campaign.js"></script>
<script src="assets/js/custom/utilities/modals/users-search.js"></script>
<!--end::Custom Javascript-->
<!--end::Javascript-->
