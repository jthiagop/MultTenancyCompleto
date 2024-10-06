<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<!-- DateRangePicker CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">


<x-tenant-app-layout>

    <!--begin::Main-->
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <!--begin::Content wrapper-->
        <div class="d-flex flex-column flex-column-fluid">
            <!--begin::Toolbar-->
            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                <!--begin::Toolbar container-->
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <!--begin::Page title-->
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <!--begin::Title-->
                        <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                            Resumo do Caixa</h1>
                        <!--end::Title-->
                        <!--begin::Breadcrumb-->
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Home</a>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-400 w-5px h-2px"></span>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('caixa.index') }}" class="text-muted text-hover-primary">Financeiro</a>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-400 w-5px h-2px"></span>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('caixa.create') }}" class="text-muted text-hover-primary">Lançamento caixa</a>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-400 w-5px h-2px"></span>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">
                                <aspan class="text-muted text-hover-primary">Movimentações do Caixa</aspan>
                            </li>
                            <!--end::Item-->
                        </ul>
                        <!--end::Breadcrumb-->
                    </div>
                    <!--end::Page title-->
                </div>
                <!--end::Toolbar container-->
            </div>
            <!--end::Toolbar-->
            <!--begin::Content-->
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <!--begin::Content container-->
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <!--begin::Navbar-->
                    <div class="card mb-6 mb-xl-9">
                        <div class="card-body pt-9 pb-0">
                            <!--begin::Details-->
                            <div class="d-flex flex-wrap flex-sm-nowrap mb-6">
                                <!--begin::Image-->
                                <div class="d-flex flex-center flex-shrink-0 bg-light rounded w-100px h-100px w-lg-150px h-lg-150px me-7 mb-4">
                                    <svg version="1.1" id="_x34_" width="100" height="100" id="_x34_" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512 512" xml:space="preserve" fill="#000000">
                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                        <g id="SVGRepo_iconCarrier">
                                            <g>
                                                <path
                                                    style="fill:#F5BAAD;"d="M45.334,230.381c-18.877,0-20.453,0-31.465,0c-11.012,0-20.453,15.73-7.865,28.318 c12.589,12.583,18.883,22.023,18.883,22.023L45.334,230.381z">
                                                </path>
                                                <path style="fill:#F5BAAD;"
                                                    d="M482.717,304.324c-2.777,13.887-10.804,56.007-14.159,66.078 c-6.294,18.883-12.874,24.192-24.775,31.855c-6.548,4.218-13.193,8.208-19.948,12.095l29.033-136.089 C468.604,284.305,485.721,289.282,482.717,304.324z">
                                                </path>
                                                <g>
                                                    <path style="fill:#f7e3de;"
                                                        d="M450.7,277.44c-25.087-9.935-30.914-41.751-56.643-77.882 c-20.979-29.519-58.382-57.746-108.79-69.141c-12.51-2.855-25.794-4.653-39.862-5.197c-11.622-0.441-22.764-0.331-33.445,0.272 c-31.413,1.771-58.804,7.872-82.45,17.099c-87.129,33.983-123.5,110.541-123.5,170.221c0,41.251,14.73,75.753,37.688,102.663 l-0.733-0.24c8.663,11.018,37.481,51.374,44.515,70.017h67.986v-6.275c11.07,2.193,22.16,3.471,33.042,3.75 c7.865,0.214,15.697,0.104,23.451-0.221c19.221-0.805,37.916-3.095,55.202-6.216v8.961h72.45 c4.537-11.278,10.837-26.099,17.327-36.786c24.327-11.518,46.228-22.232,66.902-34.1l29.026-136.103 C452.141,277.978,451.408,277.719,450.7,277.44z">
                                                    </path>
                                                    <path style="fill:#F5BAAD;"
                                                        d="M333.254,500.988c-3.147,6.295-7.871,11.012-22.03,11.012c-14.16,0-22.024,0-31.466,0 c-9.435,0-12.582-9.442-12.582-20.453c0-1.68,0-3.841,0-6.295h72.437C336.48,493.032,334.183,499.132,333.254,500.988z">
                                                    </path>
                                                    <path style="fill:#F5BAAD;"
                                                        d="M311.224,500.203c7.495,0,9.558-1.31,10.739-3.147h-42.581c0.26,1.538,0.603,2.557,0.876,3.147 H311.224z">
                                                    </path>
                                                    <path style="fill:#F5BAAD;"
                                                        d="M155.467,491.547c0,11.012-2.953,20.453-11.811,20.453s-16.242,0-29.531,0 c-13.284,0-17.715-4.717-20.668-11.012c-0.876-1.856-3.03-7.956-5.97-15.736h67.98 C155.467,487.705,155.467,489.866,155.467,491.547z">
                                                    </path>
                                                    <path style="fill:#F5BAAD;"
                                                        d="M142.521,500.203c0.253-0.662,0.545-1.687,0.772-3.147h-38.623 c1.058,1.927,2.648,3.147,9.455,3.147H142.521z">
                                                    </path>
                                                </g>
                                                <circle style="fill:#F3A7A8;" cx="332.664" cy="372.173" r="41.102">
                                                </circle>
                                                <path style="fill:#FFFFFF;"
                                                    d="M346.628,299.996c0,16.294,13.205,29.5,29.5,29.5c16.287,0,29.499-13.205,29.499-29.5 c0-16.287-13.212-29.499-29.499-29.499C359.834,270.497,346.628,283.708,346.628,299.996z">
                                                </path>
                                                <path style="fill:#604C3F;"
                                                    d="M353.228,291.742c0,10.688,8.67,19.35,19.357,19.35c10.688,0,19.356-8.663,19.356-19.35 c0-10.694-8.669-19.357-19.356-19.357C361.898,272.385,353.228,281.048,353.228,291.742z">
                                                </path>
                                                <path style="fill:#CBAA9E;"
                                                    d="M129.511,142.591v12.27h0.986c27.618-11.303,60.959-18.124,100.749-18.124 c4.51,0,9.111,0.091,13.712,0.286c13.854,0.519,27.351,2.401,40.31,5.516v-12.122c-12.511-2.855-25.794-4.653-39.862-5.198 C199.6,123.474,161.203,130.222,129.511,142.591z">
                                                </path>
                                                <g>
                                                    <path style="fill:#FFFFFF;"
                                                        d="M67.157,264.3l-3.965-1.103l3.913,1.278L67.157,264.3l-3.965-1.103l3.913,1.278l-0.889-0.292 l0.882,0.312l0.007-0.02l-0.889-0.292l0.882,0.312c0.007-0.02,0.149-0.403,0.415-0.961c0.493-1.057,1.376-2.738,2.564-4.737 c2.096-3.517,5.139-8.04,8.916-12.887c5.671-7.281,13.03-15.308,21.388-22.193c8.351-6.911,17.683-12.621,27.177-15.755 c7.579-2.518,11.686-10.707,9.168-18.293c-2.518-7.586-10.707-11.693-18.286-9.175c-11.596,3.854-21.959,9.85-31.096,16.671 c-13.698,10.252-24.704,22.387-32.764,33.023c-4.029,5.334-7.307,10.285-9.824,14.574c-1.253,2.155-2.323,4.134-3.219,6.003 c-0.447,0.941-0.857,1.849-1.233,2.784c-0.37,0.941-0.714,1.888-1.032,3.024c-2.135,7.696,2.375,15.671,10.084,17.806 C57.054,276.512,65.022,271.996,67.157,264.3z">
                                                    </path>
                                                </g>
                                                <path style="fill:#F5BAAD;"
                                                    d="M303.151,170.708c7.482-11.648,16.463-32.354,26.93-40.122c10.473-7.76,29.914,0,29.914,11.654 c0,11.648,0,32.355,0,44.002c0,11.648-32.906,18.118-50.855,11.648C291.186,191.421,303.151,170.708,303.151,170.708z">
                                                </path>
                                                <g>
                                                    <path style="fill:#417144;"
                                                        d="M294.56,82.599c0,17.32-5.36,33.419-14.49,46.702c-2.55,3.712-5.405,7.202-8.52,10.46 c-8.683-1.493-17.56-2.414-26.599-2.738c-0.519-0.026-1.038-0.052-1.557-0.052c-4.082-0.163-8.163-0.234-12.154-0.234 c-6.606,0-13.024,0.188-19.279,0.565l-25.723,2.602c-9.721,1.434-18.954,3.322-27.734,5.613c-3.42-2.9-6.606-6.113-9.533-9.533 c-12.245-14.419-19.61-33.042-19.61-53.386c0-45.546,37.053-82.599,82.6-82.599C257.508,0,294.56,37.053,294.56,82.599z">
                                                    </path>
                                                    <g>
                                                        <path style="fill:#FBE4AD;"
                                                            d="M274.781,82.599c0,16.853-6.632,32.121-17.436,43.399c-4.089,4.296-8.78,8.001-13.951,10.973 c-4.082-0.163-8.163-0.234-12.154-0.234c-6.606,0-13.024,0.188-19.279,0.565l-25.723,2.602 c-5.406-2.414-10.408-5.574-14.873-9.37c-0.681-0.571-1.343-1.155-2.005-1.778c-0.046-0.039-0.091-0.09-0.143-0.136 c-3.043-2.835-5.827-5.951-8.254-9.351c-0.565-0.753-1.09-1.557-1.609-2.336c-0.753-1.129-1.46-2.31-2.122-3.491 c-0.377-0.662-0.753-1.343-1.084-2.024c-0.143-0.234-0.286-0.5-0.376-0.734c-0.377-0.733-0.733-1.46-1.064-2.199 c-0.403-0.87-0.779-1.74-1.11-2.635c-0.234-0.546-0.448-1.116-0.636-1.661c-0.357-0.961-0.688-1.934-0.993-2.92 c-0.169-0.474-0.305-0.947-0.422-1.441c-0.143-0.448-0.26-0.895-0.383-1.369c-0.019-0.046-0.045-0.098-0.045-0.163 c-0.189-0.662-0.331-1.324-0.474-2.011c-0.117-0.441-0.208-0.922-0.279-1.389c-0.124-0.454-0.215-0.895-0.26-1.369v-0.02 c-0.097-0.545-0.188-1.064-0.26-1.609c-0.097-0.52-0.168-1.039-0.214-1.558c-0.097-0.662-0.169-1.324-0.214-1.985 c-0.019-0.091-0.045-0.208-0.019-0.325c-0.098-0.688-0.143-1.349-0.169-2.037c-0.072-1.129-0.091-2.259-0.091-3.394 c0-34.69,28.13-62.821,62.821-62.821c1.136,0,2.264,0.026,3.4,0.091c0.682,0.026,1.344,0.078,2.025,0.169 c0.117-0.026,0.24,0,0.331,0.026c0.545,0.039,1.09,0.091,1.629,0.162c0.098,0,0.169,0,0.26,0.02 c0.591,0.078,1.181,0.142,1.771,0.24c0.071,0,0.162,0.019,0.234,0.045c0.259,0.026,0.499,0.072,0.759,0.117 c0.072,0,0.117,0,0.188,0.026c0.539,0.071,1.064,0.188,1.583,0.312c0.513,0.065,1.013,0.181,1.506,0.298 c0.688,0.143,1.35,0.286,2.011,0.48c0.065,0,0.117,0.019,0.162,0.038c0.474,0.124,0.922,0.241,1.369,0.377 c0.493,0.123,0.967,0.266,1.441,0.428c0.986,0.312,1.96,0.636,2.926,0.993c0.539,0.189,1.11,0.403,1.649,0.636 c0.967,0.383,1.934,0.779,2.88,1.227c0.656,0.286,1.318,0.617,1.96,0.948c0.233,0.091,0.493,0.233,0.726,0.376 c0.688,0.331,1.369,0.708,2.031,1.084c0.286,0.143,0.545,0.305,0.824,0.474c0.474,0.26,0.922,0.545,1.37,0.831 c0.195,0.117,0.383,0.234,0.571,0.376c0.26,0.163,0.539,0.325,0.798,0.494c0.358,0.233,0.708,0.467,1.064,0.726 c0.234,0.143,0.448,0.286,0.662,0.48c0.493,0.298,0.993,0.681,1.486,1.058c0.279,0.188,0.565,0.422,0.85,0.636 c1.155,0.902,2.29,1.843,3.374,2.809c0.733,0.636,1.415,1.298,2.102,1.986c0.707,0.681,1.389,1.362,2.051,2.096 c0.071,0.045,0.117,0.124,0.162,0.162C268.461,51.211,274.781,66.15,274.781,82.599z">
                                                        </path>
                                                        <path style="fill:#D4A948;"
                                                            d="M152.697,86.162c0-34.697,28.13-62.827,62.827-62.827c16.437,0,31.401,6.326,42.594,16.664 c-11.473-12.433-27.903-20.22-46.157-20.22c-34.697,0-62.821,28.124-62.821,62.821c0,18.254,7.787,34.685,20.22,46.157 C159.023,117.563,152.697,102.605,152.697,86.162z">
                                                        </path>
                                                    </g>
                                                    <g>
                                                        <path style="fill:#D4A948;"
                                                            d="M238.624,56.377c-0.279-0.279-0.584-0.565-0.87-0.844c-0.071-0.078-0.116-0.117-0.162-0.169 c-4.51-4.01-10.551-6.515-17.092-6.489h-23.737c-2.245,0-4.439,0.896-6.041,2.505c-1.583,1.577-2.504,3.777-2.504,6.035v62.659 c0,1.868,0.59,3.614,1.629,5.009c0.617,0.87,1.395,1.596,2.29,2.168c1.324,0.869,2.926,1.369,4.627,1.369 c2.356,0,4.458-0.941,6.016-2.479l0.026-0.02c1.558-1.557,2.498-3.685,2.498-6.047v-19.941h15.197 c7.06,0,13.523-2.874,18.124-7.508c4.627-4.62,7.508-11.064,7.508-18.117C246.132,67.447,243.251,60.984,238.624,56.377z M226.541,68.459c1.558,1.558,2.505,3.66,2.505,6.048c0,2.382-0.948,4.458-2.505,6.035c-1.577,1.557-3.653,2.504-6.041,2.504 h-15.197V65.961h15.197c1.914,0,3.64,0.617,5.055,1.681C225.906,67.875,226.236,68.154,226.541,68.459z">
                                                        </path>
                                                        <path style="fill:#417144;"
                                                            d="M237.755,55.533c-0.071-0.078-0.116-0.117-0.162-0.169c-0.285-0.305-0.571-0.61-0.85-0.896 c-4.627-4.626-11.096-7.501-18.124-7.501h-23.744c-2.245,0-4.465,0.915-6.041,2.498c-1.609,1.603-2.505,3.796-2.505,6.042v62.659 c0,2.829,1.369,5.36,3.517,6.917c1.421,1.038,3.141,1.648,5.029,1.648c4.718,0,8.54-3.848,8.54-8.565V98.244h15.204 c7.027,0,13.497-2.881,18.124-7.501c4.62-4.627,7.501-11.096,7.501-18.15C244.244,66.078,241.765,60.062,237.755,55.533z M203.415,64.047h15.204c2.382,0.026,4.458,0.947,6.035,2.504c0.331,0.331,0.642,0.707,0.902,1.09 c1.012,1.389,1.603,3.089,1.603,4.951c0,2.408-0.941,4.484-2.504,6.067c-1.577,1.557-3.653,2.479-6.035,2.498h-6.658 l-6.657-0.019h-1.888V64.047z">
                                                        </path>
                                                    </g>
                                                </g>
                                                <path style="opacity:0.22;fill:#FFFFFF;"
                                                    d="M211.961,0v137.302l-25.723,2.602c-9.721,1.434-18.954,3.322-27.734,5.613 c-3.42-2.9-6.606-6.113-9.533-9.533c-12.245-14.419-19.61-33.042-19.61-53.386C129.361,37.053,166.414,0,211.961,0z">
                                                </path>
                                            </g>
                                        </g>
                                    </svg>
                                </div>
                                <!--end::Image-->
                                <!--begin::Wrapper-->
                                <div class="flex-grow-1">
                                    <!--begin::Head-->
                                    <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                                        <!--begin::Details-->
                                        <div class="d-flex flex-column">
                                            <!--begin::Status-->
                                            <div class="d-flex align-items-center mb-1">
                                                <a href="#"
                                                    class="text-gray-800 text-hover-primary fs-2 fw-bold me-3">Busca de
                                                    movimentação de caixa</a>
                                                <span class="badge badge-light-success me-auto">Ativado</span>
                                            </div>
                                            <!--end::Status-->
                                            <!--begin::Description-->
                                            <div class="d-flex flex-wrap fw-semibold mb-4 fs-5 text-gray-400">Todos os lançamentos relacionados ao Caixa</div>
                                            <!--end::Description-->
                                        </div>
                                        <!--end::Details-->
                                        <!--begin::Actions-->
                                        <div class="d-flex mb-4">
                                            <a href="{{ route('caixa.index') }}"
                                                class="btn btn-sm btn-bg-light btn-active-color-primary me-3">Financeiro</a>
                                            <a href="{{ route('caixa.create') }}" class="btn btn-sm btn-success me-3">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
                                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                                    <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
                                                  </svg>
                                                  Lançamento</a>
                                            <!--begin::Menu-->
                                            <div class="me-0">
                                                <button
                                                    class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary"
                                                    data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                                    <i class="bi bi-three-dots fs-3"></i>
                                                </button>
                                                <!--begin::Menu 3-->
                                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px py-3"
                                                    data-kt-menu="true">
                                                    <!--begin::Heading-->
                                                    <div class="menu-item px-3">
                                                        <div
                                                            class="menu-content text-muted pb-2 px-3 fs-7 text-uppercase">
                                                            Payments</div>
                                                    </div>
                                                    <!--end::Heading-->
                                                    <!--begin::Menu item-->
                                                    <div class="menu-item px-3">
                                                        <a href="#" class="menu-link px-3">Create Invoice</a>
                                                    </div>
                                                    <!--end::Menu item-->
                                                    <!--begin::Menu item-->
                                                    <div class="menu-item px-3">
                                                        <a href="#" class="menu-link flex-stack px-3">Create
                                                            Payment
                                                            <i class="fas fa-exclamation-circle ms-2 fs-7"
                                                                data-bs-toggle="tooltip"
                                                                title="Specify a target name for future usage and reference"></i></a>
                                                    </div>
                                                    <!--end::Menu item-->
                                                    <!--begin::Menu item-->
                                                    <div class="menu-item px-3">
                                                        <a href="#" class="menu-link px-3">Generate Bill</a>
                                                    </div>
                                                    <!--end::Menu item-->
                                                    <!--begin::Menu item-->
                                                    <div class="menu-item px-3" data-kt-menu-trigger="hover"
                                                        data-kt-menu-placement="right-end">
                                                        <a href="#" class="menu-link px-3">
                                                            <span class="menu-title">Subscription</span>
                                                            <span class="menu-arrow"></span>
                                                        </a>
                                                        <!--begin::Menu sub-->
                                                        <div class="menu-sub menu-sub-dropdown w-175px py-4">
                                                            <!--begin::Menu item-->
                                                            <div class="menu-item px-3">
                                                                <a href="#" class="menu-link px-3">Plans</a>
                                                            </div>
                                                            <!--end::Menu item-->
                                                            <!--begin::Menu item-->
                                                            <div class="menu-item px-3">
                                                                <a href="#" class="menu-link px-3">Billing</a>
                                                            </div>
                                                            <!--end::Menu item-->
                                                            <!--begin::Menu item-->
                                                            <div class="menu-item px-3">
                                                                <a href="#"
                                                                    class="menu-link px-3">Statements</a>
                                                            </div>
                                                            <!--end::Menu item-->
                                                            <!--begin::Menu separator-->
                                                            <div class="separator my-2"></div>
                                                            <!--end::Menu separator-->
                                                            <!--begin::Menu item-->
                                                            <div class="menu-item px-3">
                                                                <div class="menu-content px-3">
                                                                    <!--begin::Switch-->
                                                                    <label
                                                                        class="form-check form-switch form-check-custom form-check-solid">
                                                                        <!--begin::Input-->
                                                                        <input class="form-check-input w-30px h-20px"
                                                                            type="checkbox" value="1"
                                                                            checked="checked" name="notifications" />
                                                                        <!--end::Input-->
                                                                        <!--end::Label-->
                                                                        <span
                                                                            class="form-check-label text-muted fs-6">Recuring</span>
                                                                        <!--end::Label-->
                                                                    </label>
                                                                    <!--end::Switch-->
                                                                </div>
                                                            </div>
                                                            <!--end::Menu item-->
                                                        </div>
                                                        <!--end::Menu sub-->
                                                    </div>
                                                    <!--end::Menu item-->
                                                    <!--begin::Menu item-->
                                                    <div class="menu-item px-3 my-1">
                                                        <a href="#" class="menu-link px-3">Settings</a>
                                                    </div>
                                                    <!--end::Menu item-->
                                                </div>
                                                <!--end::Menu 3-->
                                            </div>
                                            <!--end::Menu-->
                                        </div>
                                        <!--end::Actions-->
                                    </div>
                                    <!--end::Head-->
                                    <!--begin::Info-->
                                    <div class="d-flex flex-wrap justify-content-start">
                                        <!--begin::Stats-->
                                        <div class="d-flex flex-wrap">
                                            <!--begin::Stat-->
                                            <div
                                                class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                                <!--begin::Number-->
                                                <div class="d-flex align-items-center">
                                                    <div class="fs-4 fw-bold" data-kt-countup="true"
                                                        data-kt-countup-value="{{ $total }}"
                                                        data-kt-countup-prefix="R$ ">0</div>
                                                </div>
                                                <!--end::Number-->
                                                <!--begin::Label-->
                                                <div class="fw-semibold fs-6 text-gray-400">Saldo atua</div>
                                                <!--end::Label-->
                                            </div>
                                            <!--end::Stat-->
                                            <!--begin::Stat-->
                                            <div
                                                class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                                <!--begin::Number-->
                                                <div class="d-flex align-items-center">
                                                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr065.svg-->
                                                    <span class="svg-icon svg-icon-3 svg-icon-danger me-2">
                                                        <svg width="24" height="24" viewBox="0 0 24 24"
                                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <rect opacity="0.5" x="11" y="18" width="13"
                                                                height="2" rx="1"
                                                                transform="rotate(-90 11 18)" fill="currentColor" />
                                                            <path
                                                                d="M11.4343 15.4343L7.25 11.25C6.83579 10.8358 6.16421 10.8358 5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75L11.2929 18.2929C11.6834 18.6834 12.3166 18.6834 12.7071 18.2929L18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25C17.8358 10.8358 17.1642 10.8358 16.75 11.25L12.5657 15.4343C12.2533 15.7467 11.7467 15.7467 11.4343 15.4343Z"
                                                                fill="currentColor" />
                                                        </svg>
                                                    </span>
                                                    <!--end::Svg Icon-->
                                                    <div class="fs-4 fw-bold" data-kt-countup="true"
                                                        data-kt-countup-value="{{ $ValorSaidas }} "data-kt-countup-prefix="R$ ">0</div>
                                                </div>
                                                <!--end::Number-->
                                                <!--begin::Label-->
                                                <div class="fw-semibold fs-6 text-gray-400" >Saída</div>
                                                <!--end::Label-->
                                            </div>
                                            <!--end::Stat-->
                                            <!--begin::Stat-->
                                            <div
                                                class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                                <!--begin::Number-->
                                                <div class="d-flex align-items-center">
                                                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr066.svg-->
                                                    <span class="svg-icon svg-icon-3 svg-icon-success me-2">
                                                        <svg width="24" height="24" viewBox="0 0 24 24"
                                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <rect opacity="0.5" x="13" y="6" width="13"
                                                                height="2" rx="1"
                                                                transform="rotate(90 13 6)" fill="currentColor" />
                                                            <path
                                                                d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z"
                                                                fill="currentColor" />
                                                        </svg>
                                                    </span>
                                                    <!--end::Svg Icon-->
                                                    <div class="fs-4 fw-bold" data-kt-countup="true"
                                                        data-kt-countup-value="{{ $valorEntrada }}"
                                                        data-kt-countup-prefix="$">0</div>
                                                </div>
                                                <!--end::Number-->
                                                <!--begin::Label-->
                                                <div class="fw-semibold fs-6 text-gray-400" data-kt-countup-prefix="R$ ">Entrada</div>
                                                <!--end::Label-->
                                            </div>
                                            <!--end::Stat-->
                                        </div>
                                        <!--end::Stats-->
                                        <!--begin::Users-->
                                        <div class="symbol-group symbol-hover mb-3">
                                            <!--begin::User-->
                                            <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip"
                                                title="Alan Warden">
                                                <span
                                                    class="symbol-label bg-warning text-inverse-warning fw-bold">A</span>
                                            </div>
                                            <!--end::User-->
                                            <!--begin::User-->
                                            <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip"
                                                title="Michael Eberon">
                                                <img alt="Pic" src="/assets/media/avatars/300-11.jpg" />
                                            </div>
                                            <!--end::User-->
                                            <!--begin::User-->
                                            <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip"
                                                title="Michelle Swanston">
                                                <img alt="Pic" src="/assets/media/avatars/300-7.jpg" />
                                            </div>
                                            <!--end::User-->
                                            <!--begin::User-->
                                            <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip"
                                                title="Francis Mitcham">
                                                <img alt="Pic" src="/assets/media/avatars/300-20.jpg" />
                                            </div>
                                            <!--end::User-->
                                            <!--begin::User-->
                                            <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip"
                                                title="Susan Redwood">
                                                <span
                                                    class="symbol-label bg-primary text-inverse-primary fw-bold">S</span>
                                            </div>
                                            <!--end::User-->
                                            <!--begin::User-->
                                            <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip"
                                                title="Melody Macy">
                                                <img alt="Pic" src="/assets/media/avatars/300-2.jpg" />
                                            </div>
                                            <!--end::User-->
                                            <!--begin::User-->
                                            <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip"
                                                title="Perry Matthew">
                                                <span class="symbol-label bg-info text-inverse-info fw-bold">P</span>
                                            </div>
                                            <!--end::User-->
                                            <!--begin::User-->
                                            <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip"
                                                title="Barry Walter">
                                                <img alt="Pic" src="/assets/media/avatars/300-12.jpg" />
                                            </div>
                                            <!--end::User-->
                                            <!--begin::All users-->
                                            <a href="#" class="symbol symbol-35px symbol-circle"
                                                data-bs-toggle="modal" data-bs-target="#kt_modal_view_users">
                                                <span class="symbol-label bg-dark text-inverse-dark fs-8 fw-bold"
                                                    data-bs-toggle="tooltip" data-bs-trigger="hover"
                                                    title="View more users">+42</span>
                                            </a>
                                            <!--end::All users-->
                                        </div>
                                        <!--end::Users-->
                                    </div>
                                    <!--end::Info-->
                                </div>
                                <!--end::Wrapper-->
                            </div>
                            <!--end::Details-->
                            <div class="separator"></div>
                            <!--begin::Nav-->
                            <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold">
                                <!--begin::Nav item-->
                                <li class="nav-item">
                                    <a class="nav-link text-active-primary py-5 me-6 active"
                                        href={{ route('caixa.list') }}>Resulmo</a>
                                </li>
                            </ul>
                            <!--end::Nav-->
                        </div>
                    </div>
                    <!--end::Navbar-->
                    <!--begin::Products-->
                    <div class="card card-flush">
                        <!--begin::Card header-->
                        <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                            <!--begin::Card title-->
                            <div class="card-title">
                                <!--begin::Search-->
                                <div class="d-flex align-items-center position-relative my-1">
                                    <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                                    <span class="svg-icon svg-icon-1 position-absolute ms-4">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546"
                                                height="2" rx="1" transform="rotate(45 17.0365 15.1223)"
                                                fill="currentColor" />
                                            <path
                                                d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z"
                                                fill="currentColor" />
                                        </svg>
                                    </span>
                                    <!--end::Svg Icon-->
                                    <input type="text" data-kt-ecommerce-order-filter="search"
                                        class="form-control form-control-solid w-250px ps-14"
                                        placeholder="Buscar Lançamento" />
                                </div>
                                <!--end::Search-->
                                <!--begin::Export buttons-->
                                <div id="kt_ecommerce_report_shipping_export" class="d-none"></div>
                                <!--end::Export buttons-->
                            </div>
                            <!--end::Card title-->
                            <!--begin::Card toolbar-->
                            <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                                <!--begin::Daterangepicker-->
                                <input class="form-control form-control-solid w-100 mw-250px" placeholder="Pick date range" id="kt_ecommerce_report_shipping_daterangepicker" />

                                <!--end::Daterangepicker-->
                                <!--begin::Filter-->
                                <div class="w-150px">
                                    <!--begin::Select2-->
                                    <select class="form-select form-select-solid" data-control="select2"
                                        data-hide-search="true" data-placeholder="Status"
                                        data-kt-ecommerce-order-filter="status">
                                        <option></option>
                                        <option value="all">Todos</option>
                                        <option value="entrada">Entrada</option>
                                        <option value="saida">Saída</option>
                                        <option value="Pending">Pending</option>
                                        <option value="Cancelled">Cancelled</option>
                                    </select>
                                    <!--end::Select2-->
                                </div>
                                <!--end::Filter-->
                                <!--begin::Export dropdown-->
                                <button type="button" class="btn btn-light-primary" data-kt-menu-trigger="click"
                                    data-kt-menu-placement="bottom-end">
                                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr078.svg-->
                                    <span class="svg-icon svg-icon-2">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <rect opacity="0.3" x="12.75" y="4.25" width="12" height="2"
                                                rx="1" transform="rotate(90 12.75 4.25)"
                                                fill="currentColor" />
                                            <path
                                                d="M12.0573 6.11875L13.5203 7.87435C13.9121 8.34457 14.6232 8.37683 15.056 7.94401C15.4457 7.5543 15.4641 6.92836 15.0979 6.51643L12.4974 3.59084C12.0996 3.14332 11.4004 3.14332 11.0026 3.59084L8.40206 6.51643C8.0359 6.92836 8.0543 7.5543 8.44401 7.94401C8.87683 8.37683 9.58785 8.34458 9.9797 7.87435L11.4427 6.11875C11.6026 5.92684 11.8974 5.92684 12.0573 6.11875Z"
                                                fill="currentColor" />
                                            <path opacity="0.3"
                                                d="M18.75 8.25H17.75C17.1977 8.25 16.75 8.69772 16.75 9.25C16.75 9.80228 17.1977 10.25 17.75 10.25C18.3023 10.25 18.75 10.6977 18.75 11.25V18.25C18.75 18.8023 18.3023 19.25 17.75 19.25H5.75C5.19772 19.25 4.75 18.8023 4.75 18.25V11.25C4.75 10.6977 5.19771 10.25 5.75 10.25C6.30229 10.25 6.75 9.80228 6.75 9.25C6.75 8.69772 6.30229 8.25 5.75 8.25H4.75C3.64543 8.25 2.75 9.14543 2.75 10.25V19.25C2.75 20.3546 3.64543 21.25 4.75 21.25H18.75C19.8546 21.25 20.75 20.3546 20.75 19.25V10.25C20.75 9.14543 19.8546 8.25 18.75 8.25Z"
                                                fill="currentColor" />
                                        </svg>
                                    </span>
                                    <!--end::Svg Icon-->Relatório</button>
                                <!--begin::Menu-->
                                <div id="kt_ecommerce_report_shipping_export_menu"
                                    class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-200px py-4"
                                    data-kt-menu="true">
                                    <!--begin::Menu item-->
                                    <div class="menu-item px-3">
                                        <a href="#" class="menu-link px-3"
                                            data-kt-ecommerce-export="excel">Exporta Excel</a>
                                    </div>
                                    <!--end::Menu item-->
                                    <!--begin::Menu item-->
                                    <div class="menu-item px-3">
                                        <a href="#" class="menu-link px-3"
                                            data-kt-ecommerce-export="csv">Exporta CSV</a>
                                    </div>
                                    <!--end::Menu item-->
                                    <!--begin::Menu item-->
                                    <div class="menu-item px-3">
                                        <a href="#" class="menu-link px-3"
                                            data-kt-ecommerce-export="pdf">Exporta PDF</a>
                                    </div>
                                    <!--end::Menu item-->
                                </div>
                                <!--end::Menu-->
                                <!--end::Export dropdown-->
                            </div>
                            <!--end::Card toolbar-->
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            <!--begin::Table-->
                            <table class="table align-middle table-row-dashed fs-6 gy-5"
                                id="kt_ecommerce_report_shipping_table">
                                <!--begin::Table head-->
                                <thead>
                                    <!--begin::Table row-->
                                    <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                        <th class="min-w-75px">ID</th>
                                        <th class="min-w-100px">Data</th>
                                        <th class="min-w-150px">Tipo Docuemnto</th>
                                        <th class="min-w-500px">Documento</th>
                                        <th class="min-w-125px">Tipo</th>
                                        <th class="min-w-125px">Valor</th>
                                        <th class="min-w-75px">Origem</th>
                                        <th class="text-end min-w-100px">Ações</th>
                                    </tr>
                                    <!--end::Table row-->
                                </thead>
                                <!--end::Table head-->
                                <!--begin::Table body-->
                                <tbody class="fw-semibold text-gray-600">
                                    <!--begin::Table row-->
                                    @foreach ($caixas as $caixa)
                                    <tr>
                                        <!--begin::User=-->
                                        <td>{{ $caixa->id }}</td>
                                        <!--end::User=-->
                                        <!--begin::Role=-->
                                        <td>{{ date(' d-m-Y', strtotime($caixa->data_competencia)) }}</td>
                                        <!--end::Role=-->
                                        <!--begin::Last login=-->
                                        <td>{{ $caixa->tipo_documento }}</td>
                                        <!--end::Last login=-->
                                        <!--begin::Two step=-->
                                        <td>{{ $caixa->lancamento_padrao }}</td>
                                        <!--end::Two step=-->
                                        <!--begin::Joined-->
                                        <td>
                                            <div
                                                class="badge fw-bold {{ $caixa->tipo == 'entrada' ? 'badge-success' : 'badge-danger' }}">
                                                {{ $caixa->tipo }}
                                            </div>
                                        </td>
                                        <!--begin::Joined-->
                                        <td>R$ {{ number_format($caixa->valor, 2, ',', '.') }}</td>
                                        <td class="text-center">{{ $caixa->origem }}</td>
                                        <!--begin::Action=-->
                                        <td class="text-end">
                                            <a href="#"
                                                class="btn btn-light btn-active-light-primary btn-sm"
                                                data-kt-menu-trigger="click"
                                                data-kt-menu-placement="bottom-end">Ações
                                                <!--begin::Svg Icon | path: icons/duotune/arrows/arr072.svg-->
                                                <span class="svg-icon svg-icon-5 m-0">
                                                    <svg width="24" height="24" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z"
                                                            fill="currentColor" />
                                                    </svg>
                                                </span>
                                                <!--end::Svg Icon--></a>
                                            <!--begin::Menu-->
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4"
                                                data-kt-menu="true">
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="{{ route('caixa.edit', $caixa->id) }}"
                                                        class="menu-link px-3">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                                            <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                                            <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                                                          </svg>
                                                        Editar</a>
                                                    <a href="#" class="menu-link px-3 delete-link text-danger"
                                                        data-id="{{ $caixa->id }}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3" viewBox="0 0 16 16">
                                                            <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5M11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47M8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5"/>
                                                          </svg>
                                                        Excluir</a>
                                                    <form id="delete-form-{{ $caixa->id }}"
                                                        action="{{ route('caixa.destroy', $caixa->id) }}"
                                                        method="POST" style="display: none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                </div>
                                                <!--end::Menu item-->
                                            </div>
                                            <!--end::Menu-->
                                        </td>
                                        <!--end::Action=-->
                                    </tr>
                                @endforeach
                                    <!--end::Table row-->
                                </tbody>
                                <!--end::Table body-->
                            </table>
                            <!--end::Table-->
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Products-->
                </div>
                <!--end::Content container-->
            </div>
            <!--end::Content-->
        </div>
        <!--end::Content wrapper-->


</x-tenant-app-layout>


<!--begin::Vendors Javascript(used for this page only)-->
<script src="/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<!--end::Vendors Javascript-->
<!--begin::Custom Javascript(used for this page only)-->
<script src="/assets/js/custom/apps/caixa/shipping.js"></script>

<script src="/assets/js/widgets.bundle.js"></script>
<script src="/assets/js/custom/apps/chat/chat.js"></script>
<script src="/assets/js/custom/utilities/modals/upgrade-plan.js"></script>
<script src="/assets/js/custom/utilities/modals/create-campaign.js"></script>
<script src="/assets/js/custom/utilities/modals/users-search.js"></script>
<!--end::Custom Javascript-->
<script src="/assets/js/custom/apps/user-management/users/list/table.js"></script>

<!--end::Javascript-->

<!-- jQuery -->
<!-- Bootstrap Bundle (includes Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- DataTables -->
<!-- Moment.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<!-- DateRangePicker -->
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<!-- Custom Script -->
<script src="/assets/js/custom_script.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const deleteLinks = document.querySelectorAll('.delete-link');

    deleteLinks.forEach(link => {
        link.addEventListener('click', function (event) {
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
