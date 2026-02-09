<!--begin::Order details page-->
<div class="d-flex flex-column gap-7 gap-lg-10">
    @php
    $lancamentosNaoConciliados = session('lancamentosNaoConciliados', collect()); // Se não houver, cria uma coleção vazia
@endphp
    <div class="d-flex flex-wrap flex-stack gap-5 gap-lg-10">
        <!--begin:::Tabs-->
        <ul class="nav nav-tabs nav-line-tabs nav-line-tabs-2x border-0  mb-lg-n2 me-auto">
            <!--begin:::Tab item-->
            <li class="nav-item">
                <a class="nav-link text-active-primary pb-4 active" data-bs-toggle="tab"
                    href="#kt_ecommerce_sales_order_summary">Order Summary</a>
            </li>
            <!--end:::Tab item-->
            <!--begin:::Tab item-->
            <li class="nav-item">
                <a class="nav-link text-active-primary pb-4" data-bs-toggle="tab"
                    href="#kt_ecommerce_sales_order_history">Order History</a>
            </li>
            <!--end:::Tab item-->
        </ul>
        <!--end:::Tabs-->
        <!--begin::Button-->
        <a href="../../demo1/dist/apps/ecommerce/sales/listing.html"
            class="btn btn-icon btn-light btn-sm ms-auto me-lg-n7">
            <!--begin::Svg Icon | path: icons/duotune/arrows/arr074.svg-->
            <span class="svg-icon svg-icon-2">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M11.2657 11.4343L15.45 7.25C15.8642 6.83579 15.8642 6.16421 15.45 5.75C15.0358 5.33579 14.3642 5.33579 13.95 5.75L8.40712 11.2929C8.01659 11.6834 8.01659 12.3166 8.40712 12.7071L13.95 18.25C14.3642 18.6642 15.0358 18.6642 15.45 18.25C15.8642 17.8358 15.8642 17.1642 15.45 16.75L11.2657 12.5657C10.9533 12.2533 10.9533 11.7467 11.2657 11.4343Z"
                        fill="currentColor" />
                </svg>
            </span>
            <!--end::Svg Icon-->
        </a>
        <!--end::Button-->
        <!--begin::Button-->
        <a href="../../demo1/dist/apps/ecommerce/sales/edit-order.html" class="btn btn-success btn-sm me-lg-n7">Edit
            Order</a>
        <!--end::Button-->
        <!--begin::Button-->
        <a href="../../demo1/dist/apps/ecommerce/sales/add-order.html" class="btn btn-primary btn-sm">Add New Order</a>
        <!--end::Button-->
    </div>

    {{-- <!--begin::Order summary-->
    <div class="d-flex flex-column flex-xl-row gap-7 gap-lg-10">
        <!--begin::Order details-->
        <div class="card card-flush py-6 flex-row-fluid">
            <!--begin::Card header-->
            <div class="card-header">
                <div class="card-title">
                    <h2>Order Details (#14534)</h2>
                </div>
            </div>
            <!--end::Card header-->
            <!--begin::Card body-->
            <div class="card-body pt-0">
                <div class="table-responsive">
                    <!--begin::Table-->
                    <table class="table align-middle table-row-bordered mb-0 fs-6 gy-5 min-w-300px">
                        <!--begin::Table body-->
                        <tbody class="fw-semibold text-gray-600">
                            <!--begin::Date-->
                            <tr>
                                <td class="text-muted">
                                    <div class="d-flex align-items-center">
                                        <!--begin::Svg Icon | path: icons/duotune/files/fil002.svg-->
                                        <span class="svg-icon svg-icon-2 me-2">
                                            <svg width="20" height="21" viewBox="0 0 20 21" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path opacity="0.3"
                                                    d="M19 3.40002C18.4 3.40002 18 3.80002 18 4.40002V8.40002H14V4.40002C14 3.80002 13.6 3.40002 13 3.40002C12.4 3.40002 12 3.80002 12 4.40002V8.40002H8V4.40002C8 3.80002 7.6 3.40002 7 3.40002C6.4 3.40002 6 3.80002 6 4.40002V8.40002H2V4.40002C2 3.80002 1.6 3.40002 1 3.40002C0.4 3.40002 0 3.80002 0 4.40002V19.4C0 20 0.4 20.4 1 20.4H19C19.6 20.4 20 20 20 19.4V4.40002C20 3.80002 19.6 3.40002 19 3.40002ZM18 10.4V13.4H14V10.4H18ZM12 10.4V13.4H8V10.4H12ZM12 15.4V18.4H8V15.4H12ZM6 10.4V13.4H2V10.4H6ZM2 15.4H6V18.4H2V15.4ZM14 18.4V15.4H18V18.4H14Z"
                                                    fill="currentColor" />
                                                <path
                                                    d="M19 0.400024H1C0.4 0.400024 0 0.800024 0 1.40002V4.40002C0 5.00002 0.4 5.40002 1 5.40002H19C19.6 5.40002 20 5.00002 20 4.40002V1.40002C20 0.800024 19.6 0.400024 19 0.400024Z"
                                                    fill="currentColor" />
                                            </svg>
                                        </span>
                                        <!--end::Svg Icon-->Date Added
                                    </div>
                                </td>
                                <td class="fw-bold text-end">31/01/2023</td>
                            </tr>
                            <!--end::Date-->
                            <!--begin::Payment method-->
                            <tr>
                                <td class="text-muted">
                                    <div class="d-flex align-items-center">
                                        <!--begin::Svg Icon | path: icons/duotune/finance/fin008.svg-->
                                        <span class="svg-icon svg-icon-2 me-2">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path opacity="0.3"
                                                    d="M3.20001 5.91897L16.9 3.01895C17.4 2.91895 18 3.219 18.1 3.819L19.2 9.01895L3.20001 5.91897Z"
                                                    fill="currentColor" />
                                                <path opacity="0.3"
                                                    d="M13 13.9189C13 12.2189 14.3 10.9189 16 10.9189H21C21.6 10.9189 22 11.3189 22 11.9189V15.9189C22 16.5189 21.6 16.9189 21 16.9189H16C14.3 16.9189 13 15.6189 13 13.9189ZM16 12.4189C15.2 12.4189 14.5 13.1189 14.5 13.9189C14.5 14.7189 15.2 15.4189 16 15.4189C16.8 15.4189 17.5 14.7189 17.5 13.9189C17.5 13.1189 16.8 12.4189 16 12.4189Z"
                                                    fill="currentColor" />
                                                <path
                                                    d="M13 13.9189C13 12.2189 14.3 10.9189 16 10.9189H21V7.91895C21 6.81895 20.1 5.91895 19 5.91895H3C2.4 5.91895 2 6.31895 2 6.91895V20.9189C2 21.5189 2.4 21.9189 3 21.9189H19C20.1 21.9189 21 21.0189 21 19.9189V16.9189H16C14.3 16.9189 13 15.6189 13 13.9189Z"
                                                    fill="currentColor" />
                                            </svg>
                                        </span>
                                        <!--end::Svg Icon-->Payment Method
                                    </div>
                                </td>
                                <td class="fw-bold text-end">Online
                                    <img src="/tenancy/assets/media/svg/card-logos/visa.svg" class="w-50px ms-2" />
                                </td>
                            </tr>
                            <!--end::Payment method-->
                            <!--begin::Date-->
                            <tr>
                                <td class="text-muted">
                                    <div class="d-flex align-items-center">
                                        <!--begin::Svg Icon | path: icons/duotune/ecommerce/ecm006.svg-->
                                        <span class="svg-icon svg-icon-2 me-2">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M20 8H16C15.4 8 15 8.4 15 9V16H10V17C10 17.6 10.4 18 11 18H16C16 16.9 16.9 16 18 16C19.1 16 20 16.9 20 18H21C21.6 18 22 17.6 22 17V13L20 8Z"
                                                    fill="currentColor" />
                                                <path opacity="0.3"
                                                    d="M20 18C20 19.1 19.1 20 18 20C16.9 20 16 19.1 16 18C16 16.9 16.9 16 18 16C19.1 16 20 16.9 20 18ZM15 4C15 3.4 14.6 3 14 3H3C2.4 3 2 3.4 2 4V13C2 13.6 2.4 14 3 14H15V4ZM6 16C4.9 16 4 16.9 4 18C4 19.1 4.9 20 6 20C7.1 20 8 19.1 8 18C8 16.9 7.1 16 6 16Z"
                                                    fill="currentColor" />
                                            </svg>
                                        </span>
                                        <!--end::Svg Icon-->Shipping Method
                                    </div>
                                </td>
                                <td class="fw-bold text-end">Flat Shipping Rate</td>
                            </tr>
                            <!--end::Date-->
                        </tbody>
                        <!--end::Table body-->
                    </table>
                    <!--end::Table-->
                </div>
            </div>
            <!--end::Card body-->
        </div>
        <!--end::Order details-->
        <!--begin::Customer details-->
        <div class="card card-flush py-6 flex-row-fluid">
            <!--begin::Card header-->
            <div class="card-header">
                <div class="card-title">
                    <h2>Customer Details</h2>
                </div>
            </div>
            <!--end::Card header-->
            <!--begin::Card body-->
            <div class="card-body pt-0">
                <div class="table-responsive">
                    <!--begin::Table-->
                    <table class="table align-middle table-row-bordered mb-0 fs-6 gy-5 min-w-300px">
                        <!--begin::Table body-->
                        <tbody class="fw-semibold text-gray-600">
                            <!--begin::Customer name-->
                            <tr>
                                <td class="text-muted">
                                    <div class="d-flex align-items-center">
                                        <!--begin::Svg Icon | path: icons/duotune/communication/com006.svg-->
                                        <span class="svg-icon svg-icon-2 me-2">
                                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path opacity="0.3"
                                                    d="M16.5 9C16.5 13.125 13.125 16.5 9 16.5C4.875 16.5 1.5 13.125 1.5 9C1.5 4.875 4.875 1.5 9 1.5C13.125 1.5 16.5 4.875 16.5 9Z"
                                                    fill="currentColor" />
                                                <path
                                                    d="M9 16.5C10.95 16.5 12.75 15.75 14.025 14.55C13.425 12.675 11.4 11.25 9 11.25C6.6 11.25 4.57499 12.675 3.97499 14.55C5.24999 15.75 7.05 16.5 9 16.5Z"
                                                    fill="currentColor" />
                                                <rect x="7" y="6" width="4" height="4" rx="2"
                                                    fill="currentColor" />
                                            </svg>
                                        </span>
                                        <!--end::Svg Icon-->Customer
                                    </div>
                                </td>
                                <td class="fw-bold text-end">
                                    <div class="d-flex align-items-center justify-content-end">
                                        <!--begin:: Avatar -->
                                        <div class="symbol symbol-circle symbol-25px overflow-hidden me-3">
                                            <a href="../../demo1/dist/apps/ecommerce/customers/details.html">
                                                <div class="symbol-label">
                                                    <img src="/tenancy/assets/media/avatars/300-23.jpg" alt="Dan Wilson"
                                                        class="w-100" />
                                                </div>
                                            </a>
                                        </div>
                                        <!--end::Avatar-->
                                        <!--begin::Name-->
                                        <a href="../../demo1/dist/apps/ecommerce/customers/details.html"
                                            class="text-gray-600 text-hover-primary">Dan Wilson</a>
                                        <!--end::Name-->
                                    </div>
                                </td>
                            </tr>
                            <!--end::Customer name-->
                            <!--begin::Customer email-->
                            <tr>
                                <td class="text-muted">
                                    <div class="d-flex align-items-center">
                                        <!--begin::Svg Icon | path: icons/duotune/communication/com011.svg-->
                                        <span class="svg-icon svg-icon-2 me-2">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path opacity="0.3"
                                                    d="M21 19H3C2.4 19 2 18.6 2 18V6C2 5.4 2.4 5 3 5H21C21.6 5 22 5.4 22 6V18C22 18.6 21.6 19 21 19Z"
                                                    fill="currentColor" />
                                                <path
                                                    d="M21 5H2.99999C2.69999 5 2.49999 5.10005 2.29999 5.30005L11.2 13.3C11.7 13.7 12.4 13.7 12.8 13.3L21.7 5.30005C21.5 5.10005 21.3 5 21 5Z"
                                                    fill="currentColor" />
                                            </svg>
                                        </span>
                                        <!--end::Svg Icon-->Email
                                    </div>
                                </td>
                                <td class="fw-bold text-end">
                                    <a href="../../demo1/dist/apps/user-management/users/view.html"
                                        class="text-gray-600 text-hover-primary">dam@consilting.com</a>
                                </td>
                            </tr>
                            <!--end::Payment method-->
                            <!--begin::Date-->
                            <tr>
                                <td class="text-muted">
                                    <div class="d-flex align-items-center">
                                        <!--begin::Svg Icon | path: icons/duotune/electronics/elc003.svg-->
                                        <span class="svg-icon svg-icon-2 me-2">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M5 20H19V21C19 21.6 18.6 22 18 22H6C5.4 22 5 21.6 5 21V20ZM19 3C19 2.4 18.6 2 18 2H6C5.4 2 5 2.4 5 3V4H19V3Z"
                                                    fill="currentColor" />
                                                <path opacity="0.3" d="M19 4H5V20H19V4Z" fill="currentColor" />
                                            </svg>
                                        </span>
                                        <!--end::Svg Icon-->Phone
                                    </div>
                                </td>
                                <td class="fw-bold text-end">+6141 234 567</td>
                            </tr>
                            <!--end::Date-->
                        </tbody>
                        <!--end::Table body-->
                    </table>
                    <!--end::Table-->
                </div>
            </div>
            <!--end::Card body-->
        </div>
        <!--end::Customer details-->
    </div>
    <!--end::Order summary--> --}}

    <!--begin::Tab content-->
    <div class="tab-content">
        <!--begin::Tab pane-->
        <div class="tab-pane fade show active" id="kt_ecommerce_sales_order_summary" role="tab-panel">
            <!--begin::Orders-->

            <div class="d-flex flex-column gap-7 gap-lg-10">
                @foreach ($lancamentosNaoConciliados as $lancamento)
                <div class="row g-4">
                    <!--begin::Payment address-->
                    <div class="col-md-5">
                    <!--begin::Payment address-->
                    <div class="card card-flush py-4 flex-row-fluid overflow-hidden border border-hover-primary">
                        <!--begin::User-->
                        <div class=" p-7 rounded ">
                            <!--begin::Info-->
                            <div class="d-flex flex-stack pb-3">
                                <!--begin::Info-->
                                <div class="d-flex">
                                    <!--begin::Avatar-->
                                    <div class="symbol symbol-circle symbol-45px">
                                        <img src="/tenancy/assets/media/avatars/300-11.jpg" alt="" />
                                    </div>
                                    <!--end::Avatar-->
                                    <!--begin::Details-->
                                    <div class="ms-5">
                                        <!--begin::Name-->
                                        <div class="d-flex align-items-center">
                                            <a href="../../demo1/dist/pages/user-profile/overview.html"
                                                class="text-dark fw-bold text-hover-primary fs-5 me-4">Sean Bean</a>
                                            <!--begin::Label-->
                                            <span
                                                class="badge badge-light-success d-flex align-items-center fs-8 fw-semibold">
                                                <!--begin::Svg Icon | path: icons/duotune/general/gen029.svg-->
                                                <span class="svg-icon svg-icon-8 svg-icon-success me-1">
                                                    <svg width="24" height="24" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M11.1359 4.48359C11.5216 3.82132 12.4784 3.82132 12.8641 4.48359L15.011 8.16962C15.1523 8.41222 15.3891 8.58425 15.6635 8.64367L19.8326 9.54646C20.5816 9.70867 20.8773 10.6186 20.3666 11.1901L17.5244 14.371C17.3374 14.5803 17.2469 14.8587 17.2752 15.138L17.7049 19.382C17.7821 20.1445 17.0081 20.7069 16.3067 20.3978L12.4032 18.6777C12.1463 18.5645 11.8537 18.5645 11.5968 18.6777L7.69326 20.3978C6.99192 20.7069 6.21789 20.1445 6.2951 19.382L6.7248 15.138C6.75308 14.8587 6.66264 14.5803 6.47558 14.371L3.63339 11.1901C3.12273 10.6186 3.41838 9.70867 4.16744 9.54646L8.3365 8.64367C8.61089 8.58425 8.84767 8.41222 8.98897 8.16962L11.1359 4.48359Z"
                                                            fill="currentColor" />
                                                    </svg>
                                                </span>
                                                <!--end::Svg Icon-->Author</span>
                                            <!--end::Label-->
                                        </div>
                                        <!--end::Name-->
                                        <!--begin::Desc-->
                                        <span class="text-muted fw-semibold mb-3">Project Manager</span>
                                        <!--end::Desc-->
                                    </div>
                                    <!--end::Details-->
                                </div>
                                <!--end::Info-->
                                <!--begin::Stats-->
                                <div clas="d-flex">
                                    <!--begin::Price-->
                                    <div class="text-end pb-3">
                                        <span class="text-dark fw-bold fs-5">$65.45</span>
                                        <span class="text-muted fs-7">/hr</span>
                                    </div>
                                    <!--end::Price-->
                                </div>
                                <!--end::Stats-->
                            </div>
                            <!--end::Info-->
                            <!--begin::Wrapper-->
                            <div class="p-0">
                                <!--begin::Section-->
                                <div class="d-flex flex-column">
                                    <!--begin::Text-->
                                    <p class="text-gray-700 fw-semibold fs-6 mb-4">Outlines keep you honest. They stop
                                        you from indulging.</p>
                                    <!--end::Text-->
                                    <!--begin::Tags-->
                                    <div class="d-flex text-gray-700 fw-semibold fs-7">
                                        <!--begin::Tag-->
                                        <span class="border border-2 rounded me-3 p-1 px-2">HTML</span>
                                        <!--end::Tag-->
                                        <!--begin::Tag-->
                                        <span class="border border-2 rounded me-3 p-1 px-2">Javascript</span>
                                        <!--end::Tag-->
                                        <!--begin::Tag-->
                                        <span class="border border-2 rounded me-3 p-1 px-2">Python</span>
                                        <!--end::Tag-->
                                    </div>
                                    <!--end::Tags-->
                                </div>
                                <!--end::Section-->
                                <!--begin::Footer-->
                                <div class="d-flex flex-column">
                                    <!--begin::Separator-->
                                    <div class="separator separator-dashed border-muted my-5"></div>
                                    <!--end::Separator-->
                                    <!--begin::Action-->
                                    <div class="d-flex flex-stack">
                                        <!--begin::Progress-->
                                        <div class="d-flex flex-column mw-200px">
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="text-gray-700 fs-6 fw-semibold me-2">58%</span>
                                                <span class="text-muted fs-8">Job Success</span>
                                            </div>
                                            <div class="progress h-6px w-200px">
                                                <div class="progress-bar bg-success" role="progressbar"
                                                    style="width: 58%" aria-valuenow="58" aria-valuemin="0"
                                                    aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                        <!--end::Progress-->
                                        <!--begin::Button-->
                                        <a href="#" class="btn btn-sm btn-primary">Select</a>
                                        <!--end::Button-->
                                    </div>
                                    <!--end::Action-->
                                </div>
                                <!--end::Footer-->
                            </div>
                            <!--end::Wrapper-->
                        </div>
                        <!--end::User-->
                    </div>
                    <!--end::Payment address-->
                    </div>
                    <!--end::Payment address-->

                    <!--begin::Conciliar (Botão Central)-->
                    <div class="col-md-2 d-flex align-items-center justify-content-center">
                        <button class="btn btn-lg btn-primary px-5">Conciliar</button>
                    </div>
                    <!--end::Conciliar-->

                    <!--begin::Shipping address-->
                    <div class="col-md-5">
                        <div class="card card-flush py-4 flex-row-fluid overflow-hidden">
                            <div class="position-absolute top-0 end-0 opacity-10 pe-none text-end">
                                <img src="/tenancy/assets/media/icons/duotune/ecommerce/ecm006.svg" class="w-175px" />
                            </div>
                            <div class="card-header">
                                <div class="card-title">
                                    <h2>Shipping Address</h2>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                Unit 1/23 Hastings Road,<br />
                                Melbourne 3000,<br />
                                Victoria,<br />
                                Australia.
                            </div>
                        </div>
                    </div>
                    <!--end::Shipping address-->
                </div>
                @endforeach

            </div>
            <!--end::Orders-->

        </div>
        <!--end::Tab pane-->
        <!--begin::Tab pane-->
        <div class="tab-pane fade" id="kt_ecommerce_sales_order_history" role="tab-panel">
            <!--begin::Orders-->
            <div class="d-flex flex-column gap-7 gap-lg-10">
                <!--begin::Order history-->
                <div class="card card-flush py-4 flex-row-fluid">
                    <!--begin::Card header-->
                    <div class="card-header">
                        <div class="card-title">
                            <h2>Order History</h2>
                        </div>
                    </div>
                    <!--end::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <!--begin::Table-->
                            <table class="table align-middle table-row-dashed fs-6 gy-5 mb-0">
                                <!--begin::Table head-->
                                <thead>
                                    <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                        <th class="min-w-100px">Date Added</th>
                                        <th class="min-w-175px">Comment</th>
                                        <th class="min-w-70px">Order Status</th>
                                        <th class="min-w-100px">Customer Notifed</th>
                                    </tr>
                                </thead>
                                <!--end::Table head-->
                                <!--begin::Table body-->
                                <tbody class="fw-semibold text-gray-600">
                                    <tr>
                                        <!--begin::Date-->
                                        <td>31/01/2023</td>
                                        <!--end::Date-->
                                        <!--begin::Comment-->
                                        <td>Order completed</td>
                                        <!--end::Comment-->
                                        <!--begin::Status-->
                                        <td>
                                            <!--begin::Badges-->
                                            <div class="badge badge-light-success">Completed</div>
                                            <!--end::Badges-->
                                        </td>
                                        <!--end::Status-->
                                        <!--begin::Customer Notified-->
                                        <td>No</td>
                                        <!--end::Customer Notified-->
                                    </tr>
                                    <tr>
                                        <!--begin::Date-->
                                        <td>30/01/2023</td>
                                        <!--end::Date-->
                                        <!--begin::Comment-->
                                        <td>Order received by customer</td>
                                        <!--end::Comment-->
                                        <!--begin::Status-->
                                        <td>
                                            <!--begin::Badges-->
                                            <div class="badge badge-light-success">Delivered</div>
                                            <!--end::Badges-->
                                        </td>
                                        <!--end::Status-->
                                        <!--begin::Customer Notified-->
                                        <td>Yes</td>
                                        <!--end::Customer Notified-->
                                    </tr>
                                    <tr>
                                        <!--begin::Date-->
                                        <td>29/01/2023</td>
                                        <!--end::Date-->
                                        <!--begin::Comment-->
                                        <td>Order shipped from warehouse</td>
                                        <!--end::Comment-->
                                        <!--begin::Status-->
                                        <td>
                                            <!--begin::Badges-->
                                            <div class="badge badge-light-primary">Delivering</div>
                                            <!--end::Badges-->
                                        </td>
                                        <!--end::Status-->
                                        <!--begin::Customer Notified-->
                                        <td>Yes</td>
                                        <!--end::Customer Notified-->
                                    </tr>
                                    <tr>
                                        <!--begin::Date-->
                                        <td>28/01/2023</td>
                                        <!--end::Date-->
                                        <!--begin::Comment-->
                                        <td>Payment received</td>
                                        <!--end::Comment-->
                                        <!--begin::Status-->
                                        <td>
                                            <!--begin::Badges-->
                                            <div class="badge badge-light-primary">Processing</div>
                                            <!--end::Badges-->
                                        </td>
                                        <!--end::Status-->
                                        <!--begin::Customer Notified-->
                                        <td>No</td>
                                        <!--end::Customer Notified-->
                                    </tr>
                                    <tr>
                                        <!--begin::Date-->
                                        <td>27/01/2023</td>
                                        <!--end::Date-->
                                        <!--begin::Comment-->
                                        <td>Pending payment</td>
                                        <!--end::Comment-->
                                        <!--begin::Status-->
                                        <td>
                                            <!--begin::Badges-->
                                            <div class="badge badge-light-warning">Pending</div>
                                            <!--end::Badges-->
                                        </td>
                                        <!--end::Status-->
                                        <!--begin::Customer Notified-->
                                        <td>No</td>
                                        <!--end::Customer Notified-->
                                    </tr>
                                    <tr>
                                        <!--begin::Date-->
                                        <td>26/01/2023</td>
                                        <!--end::Date-->
                                        <!--begin::Comment-->
                                        <td>Payment method updated</td>
                                        <!--end::Comment-->
                                        <!--begin::Status-->
                                        <td>
                                            <!--begin::Badges-->
                                            <div class="badge badge-light-warning">Pending</div>
                                            <!--end::Badges-->
                                        </td>
                                        <!--end::Status-->
                                        <!--begin::Customer Notified-->
                                        <td>No</td>
                                        <!--end::Customer Notified-->
                                    </tr>
                                    <tr>
                                        <!--begin::Date-->
                                        <td>25/01/2023</td>
                                        <!--end::Date-->
                                        <!--begin::Comment-->
                                        <td>Payment method expired</td>
                                        <!--end::Comment-->
                                        <!--begin::Status-->
                                        <td>
                                            <!--begin::Badges-->
                                            <div class="badge badge-light-danger">Failed</div>
                                            <!--end::Badges-->
                                        </td>
                                        <!--end::Status-->
                                        <!--begin::Customer Notified-->
                                        <td>Yes</td>
                                        <!--end::Customer Notified-->
                                    </tr>
                                    <tr>
                                        <!--begin::Date-->
                                        <td>24/01/2023</td>
                                        <!--end::Date-->
                                        <!--begin::Comment-->
                                        <td>Pending payment</td>
                                        <!--end::Comment-->
                                        <!--begin::Status-->
                                        <td>
                                            <!--begin::Badges-->
                                            <div class="badge badge-light-warning">Pending</div>
                                            <!--end::Badges-->
                                        </td>
                                        <!--end::Status-->
                                        <!--begin::Customer Notified-->
                                        <td>No</td>
                                        <!--end::Customer Notified-->
                                    </tr>
                                    <tr>
                                        <!--begin::Date-->
                                        <td>23/01/2023</td>
                                        <!--end::Date-->
                                        <!--begin::Comment-->
                                        <td>Order received</td>
                                        <!--end::Comment-->
                                        <!--begin::Status-->
                                        <td>
                                            <!--begin::Badges-->
                                            <div class="badge badge-light-warning">Pending</div>
                                            <!--end::Badges-->
                                        </td>
                                        <!--end::Status-->
                                        <!--begin::Customer Notified-->
                                        <td>Yes</td>
                                        <!--end::Customer Notified-->
                                    </tr>
                                </tbody>
                                <!--end::Table head-->
                            </table>
                            <!--end::Table-->
                        </div>
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Order history-->
                <!--begin::Order data-->
                <div class="card card-flush py-4 flex-row-fluid">
                    <!--begin::Card header-->
                    <div class="card-header">
                        <div class="card-title">
                            <h2>Order Data</h2>
                        </div>
                    </div>
                    <!--end::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <!--begin::Table-->
                            <table class="table align-middle table-row-bordered mb-0 fs-6 gy-5">
                                <!--begin::Table body-->
                                <tbody class="fw-semibold text-gray-600">
                                    <!--begin::IP address-->
                                    <tr>
                                        <td class="text-muted">IP Address</td>
                                        <td class="fw-bold text-end">172.68.221.26</td>
                                    </tr>
                                    <!--end::IP address-->
                                    <!--begin::Forwarded IP-->
                                    <tr>
                                        <td class="text-muted">Forwarded IP</td>
                                        <td class="fw-bold text-end">89.201.163.49</td>
                                    </tr>
                                    <!--end::Forwarded IP-->
                                    <!--begin::User agent-->
                                    <tr>
                                        <td class="text-muted">User Agent</td>
                                        <td class="fw-bold text-end">Mozilla/5.0 (Windows NT 10.0; Win64; x64)
                                            AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36
                                        </td>
                                    </tr>
                                    <!--end::User agent-->
                                    <!--begin::Accept language-->
                                    <tr>
                                        <td class="text-muted">Accept Language</td>
                                        <td class="fw-bold text-end">en-GB,en-US;q=0.9,en;q=0.8</td>
                                    </tr>
                                    <!--end::Accept language-->
                                </tbody>
                                <!--end::Table body-->
                            </table>
                            <!--end::Table-->
                        </div>
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Order data-->
            </div>
            <!--end::Orders-->
        </div>
        <!--end::Tab pane-->
    </div>
    <!--end::Tab content-->
</div>
<!--end::Order details page-->
