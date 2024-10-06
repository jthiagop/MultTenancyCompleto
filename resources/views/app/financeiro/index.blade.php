<link href="https://kendo.cdn.telerik.com/themes/8.0.1/default/default-main.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<script src="https://kendo.cdn.telerik.com/2024.2.514/js/kendo.all.min.js"></script>
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
                            Lançamentos Financeiros</h1>
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
                            <li class="breadcrumb-item text-muted">Financeiro</li>
                            <!--end::Item-->
                        </ul>
                        <!--end::Breadcrumb-->
                    </div>
                    <!--end::Page title-->
                    <!--begin::Actions-->
                    <div class="d-flex align-items-center gap-2 gap-lg-3">
                        <!--begin::Filter menu-->
                        <div class="d-flex">
                            <select name="campaign-type" data-control="select2" data-hide-search="true"
                                class="form-select form-select-sm bg-body border-body w-175px">
                                <option value="Twitter" selected="selected">Select Campaign</option>
                                <option value="Twitter">Twitter Campaign</option>
                                <option value="Twitter">Facebook Campaign</option>
                                <option value="Twitter">Adword Campaign</option>
                                <option value="Twitter">Carbon Campaign</option>
                            </select>
                            <a href="#" class="btn btn-icon btn-sm btn-success flex-shrink-0 ms-4"
                                data-bs-toggle="modal" data-bs-target="#kt_modal_create_campaign">
                                <!--begin::Svg Icon | path: icons/duotune/arrows/arr075.svg-->
                                <span class="svg-icon svg-icon-2">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2"
                                            rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor" />
                                        <rect x="4.36396" y="11.364" width="16" height="2" rx="1"
                                            fill="currentColor" />
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                            </a>
                        </div>
                        <!--end::Filter menu-->
                        <!--begin::Secondary button-->
                        <!--end::Secondary button-->
                        <!--begin::Primary button-->
                        <!--end::Primary button-->
                    </div>
                    <!--end::Actions-->
                </div>
                <!--end::Toolbar container-->
            </div>
            <!--end::Toolbar-->
            <!--begin::Content-->
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <!--begin::Content container-->
                <div id="kt_app_content_container" class="app-container container-xxl">

                    <!--begin::Referral program-->
                    <div class="card mb-5 mb-xl-10">
                        <!--begin::Body-->
                        <div class="card-body py-10">
                            <!--begin::Stats-->
                            <div class="row">
                                <div class="col-sm-6 ">
                                    <!--begin::Menu item-->
                                    <div class="menu-item hover-elevate-up">
                                        <a href="{{ route('caixa.list') }}" class="menu-link py-3">
                                            <span class="menu-icon">
                                                <!--begin::Svg Icon | path: icons/duotune/general/gen031.svg-->
                                                <span class="svg-icon svg-icon-2">
                                                    <svg height="200px" width="200px" version="1.1" id="_x34_"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512 512"
                                                        xml:space="preserve" fill="#000000">
                                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                            stroke-linejoin="round"></g>
                                                        <g id="SVGRepo_iconCarrier">
                                                            <g>
                                                                <path style="fill:#F5BAAD;"
                                                                    d="M45.334,230.381c-18.877,0-20.453,0-31.465,0c-11.012,0-20.453,15.73-7.865,28.318 c12.589,12.583,18.883,22.023,18.883,22.023L45.334,230.381z">
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
                                                                <circle style="fill:#F3A7A8;" cx="332.664"
                                                                    cy="372.173" r="41.102"></circle>
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
                                                </span>
                                            </span>
                                            <span class="menu-title">Busca de movimentação de caixa</span>
                                        </a>
                                    </div>
                                    <!--end::Menu item-->

                                    <!--begin::Col-->
                                    <div class="row">
                                        <div class="col-6 col-sm-6">
                                            <div
                                                class="border border-dashed border-gray-300 text-center min-w-125px rounded pt-4 pb-2 my-3">
                                                <span class="fs-4 fw-semibold text-success pb-1 px-2">Entradas</span>
                                                <span class="fs-lg-1 fw-bold d-flex justify-content-center">R$
                                                    <span data-kt-countup="true"
                                                        data-kt-countup-value="{{ $valorEntrada }}">
                                                        0
                                                    </span>
                                                </span>
                                            </div>
                                        </div>
                                        <!--end::Col-->
                                        <!--begin::Col-->
                                        <div class="col-6 col-sm-6">
                                            <div
                                                class="border border-dashed border-gray-300 text-center min-w-125px rounded pt-4 pb-2 my-3">
                                                <span class="fs-4 fw-semibold text-danger pb-1 px-2">Saídas</span>
                                                <span class="fs-lg-1 fw-bold d-flex justify-content-center">R$
                                                    <span data-kt-countup="true"
                                                        data-kt-countup-value="{{ $ValorSaidas }}">0</span></span>
                                            </div>
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <!--begin::Menu item-->
                                    <div class="menu-item hover-elevate-up">
                                        <a href="{{ route('banco.list') }}" class="menu-link py-3">
                                            <span class="menu-icon">
                                                <!--begin::Svg Icon | path: icons/duotune/general/gen031.svg-->
                                                <span class="svg-icon svg-icon-2">
                                                    <svg version="1.1" id="_x34_"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        xmlns:xlink="http://www.w3.org/1999/xlink"
                                                        viewBox="0 0 512.00 512.00" xml:space="preserve"
                                                        width="256px" height="256px" fill="#000000"
                                                        stroke="#000000" stroke-width="4.096">
                                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                            stroke-linejoin="round" stroke="#CCCCCC"
                                                            stroke-width="1.024"></g>
                                                        <g id="SVGRepo_iconCarrier">
                                                            <g>
                                                                <polygon style="fill:#EFEEEF;"
                                                                    points="474.016,135.427 493.838,135.427 493.838,85.881 256.001,0 18.162,85.881 18.162,135.427 37.985,135.427 ">
                                                                </polygon>
                                                                <polygon style="fill:#E3E1E1;"
                                                                    points="50.81,105.702 256.001,31.602 461.19,105.702 ">
                                                                </polygon>
                                                                <polygon style="fill:#CBCBCB;"
                                                                    points="270.627,36.883 256.001,31.602 50.81,105.702 80.063,105.702 ">
                                                                </polygon>
                                                                <polygon style="fill:#CBCBCB;"
                                                                    points="434.38,189.938 444.283,189.938 444.283,170.114 365.004,170.114 365.004,189.938 374.914,189.938 ">
                                                                </polygon>
                                                                <polygon style="fill:#CBCBCB;"
                                                                    points="374.914,402.988 365.004,402.988 365.004,422.81 444.283,422.81 444.283,402.988 434.38,402.988 ">
                                                                </polygon>
                                                                <rect x="374.914" y="189.938" style="fill:#D8D8D9;"
                                                                    width="59.465" height="213.05"></rect>
                                                                <rect x="226.267" y="189.938" style="fill:#D8D8D9;"
                                                                    width="59.457" height="213.05"></rect>
                                                                <rect x="77.62" y="189.938" style="fill:#D8D8D9;"
                                                                    width="59.465" height="213.05"></rect>
                                                                <g>
                                                                    <rect x="102.397" y="189.938" style="fill:#CBCBCB;"
                                                                        width="9.912" height="213.05"></rect>
                                                                    <rect x="82.575" y="189.938" style="fill:#CBCBCB;"
                                                                        width="9.919" height="213.05"></rect>
                                                                    <rect x="122.219" y="189.938" style="fill:#CBCBCB;"
                                                                        width="9.912" height="213.05"></rect>
                                                                </g>
                                                                <g>
                                                                    <rect x="251.044" y="189.938" style="fill:#CBCBCB;"
                                                                        width="9.912" height="213.05"></rect>
                                                                    <rect x="231.231" y="189.938" style="fill:#CBCBCB;"
                                                                        width="9.903" height="213.05"></rect>
                                                                    <rect x="270.866" y="189.938" style="fill:#CBCBCB;"
                                                                        width="9.903" height="213.05"></rect>
                                                                </g>
                                                                <g>
                                                                    <rect x="399.693" y="189.938" style="fill:#CBCBCB;"
                                                                        width="9.91" height="213.05"></rect>
                                                                    <rect x="379.878" y="189.938" style="fill:#CBCBCB;"
                                                                        width="9.903" height="213.05"></rect>
                                                                    <rect x="419.515" y="189.938" style="fill:#CBCBCB;"
                                                                        width="9.901" height="213.05"></rect>
                                                                </g>
                                                                <polygon style="fill:#CBCBCB;"
                                                                    points="285.724,189.938 295.645,189.938 295.645,170.114 216.364,170.114 216.364,189.938 226.267,189.938 ">
                                                                </polygon>
                                                                <polygon style="fill:#CBCBCB;"
                                                                    points="226.267,402.988 216.364,402.988 216.364,422.81 295.645,422.81 295.645,402.988 285.724,402.988 ">
                                                                </polygon>
                                                                <polygon style="fill:#CBCBCB;"
                                                                    points="137.086,189.938 146.996,189.938 146.996,170.114 67.717,170.114 67.717,189.938 77.62,189.938 ">
                                                                </polygon>
                                                                <polygon style="fill:#CBCBCB;"
                                                                    points="77.62,402.988 67.717,402.988 67.717,422.81 146.996,422.81 146.996,402.988 137.086,402.988 ">
                                                                </polygon>
                                                                <g>
                                                                    <polygon style="fill:#EFEEEF;"
                                                                        points="37.985,462.446 18.162,462.446 18.162,512 493.838,512 493.838,462.446 474.016,462.446 ">
                                                                    </polygon>
                                                                    <rect x="37.985" y="422.81" style="fill:#D8D8D9;"
                                                                        width="436.031" height="39.637"></rect>
                                                                </g>
                                                                <rect x="37.985" y="135.427" style="fill:#D8D8D9;"
                                                                    width="436.031" height="34.687"></rect>
                                                            </g>
                                                        </g>
                                                    </svg>
                                                </span>
                                            </span>
                                            <span class="menu-title">Busca de movimentação de Bancária</span>
                                        </a>
                                    </div>
                                    <!--end::Menu item-->
                                    <!--begin::Col-->
                                    <div class="row">
                                        <div class="col-6 col-sm-6">
                                            <div
                                                class="border border-dashed border-gray-300 text-center min-w-125px rounded pt-4 pb-2 my-3">
                                                <span class="fs-4 fw-semibold text-success d-block">Entradas</span>
                                                <span class="fs-lg-1 fw-bold d-flex justify-content-center">R$
                                                    <span data-kt-countup="true"
                                                        data-kt-countup-value="{{ $valorEntradaBanco }}">0</span>
                                            </div>
                                        </div>
                                        <!--end::Col-->
                                        <!--begin::Col-->
                                        <div class="col-6 col-sm-6">
                                            <div
                                                class="border border-dashed border-gray-300 text-center min-w-125px rounded pt-4 pb-2 my-3">
                                                <span class="fs-4 fw-semibold text-danger pb-1 px-2">Saídas</span>
                                                <span class="fs-lg-1 fw-bold d-flex justify-content-center">R$
                                                    <span data-kt-countup="true"
                                                        data-kt-countup-value="{{ $ValorSaidasBanco }}">0</span></span>
                                            </div>
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Referral program-->
                    <!--begin::Input group-->
                    <!--begin::Row-->
                    <div class="fv-row">
                    <div class="row">

                        <!--begin::Col-->
                        <div class="col-6 col-sm-6 col-lg-6 hover-elevate-up ">
                            <a href="#" data-bs-toggle="modal" data-bs-target="#kt_modal_new_target"
                                class=" btn-outline btn-outline-dashed btn-active-light d-flex align-items-center">
                                <!--begin::Option-->
                                <!--begin::Notice-->
                                <div
                                    class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6">
                                    <!--begin::Svg Icon | path: icons/duotune/communication/com005.svg-->
                                    <span class="svg-icon svg-icon-3x me-5">
                                        <svg version="1.1" id="_x34_" width="35" height="35"
                                            id="_x34_" xmlns="http://www.w3.org/2000/svg"
                                            xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512 512"
                                            xml:space="preserve" fill="#000000">
                                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                stroke-linejoin="round">
                                            </g>
                                            <g id="SVGRepo_iconCarrier">
                                                <g>
                                                    <path style="fill:#F5BAAD;"
                                                        d="M45.334,230.381c-18.877,0-20.453,0-31.465,0c-11.012,0-20.453,15.73-7.865,28.318 c12.589,12.583,18.883,22.023,18.883,22.023L45.334,230.381z">
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
                                                    <circle style="fill:#F3A7A8;" cx="332.664" cy="372.173"
                                                        r="41.102"></circle>
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
                                    </span>
                                    <!--end::Svg Icon-->
                                    <!--begin::Wrapper-->
                                    <div class="d-flex flex-stack flex-grow-1 flex-wrap flex-md-nowrap">
                                        <!--begin::Content-->
                                        <div class="mb-3 mb-md-0 fw-semibold">
                                            <h4 class="text-gray-900 fw-bold">Lançamento de Caixa</h4>
                                            <div class="text-muted fw-semibold fs-6">registre todas as transações em
                                                espécie</div>
                                        </div>
                                        <!--end::Content-->
                                        <!--begin::Action-->
                                        <a href="{{ route('caixa.list') }}"
                                            class="btn btn-primary px-6 align-self-center ">
                                            <span class="svg-icon svg-icon-1">
                                                <svg width="24" height="24" viewBox="0 0 24 24"
                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <rect x="8" y="9" width="3" height="10" rx="1.5"
                                                        fill="currentColor" />
                                                    <rect opacity="0.5" x="13" y="5" width="3" height="14"
                                                        rx="1.5" fill="currentColor" />
                                                    <rect x="18" y="11" width="3" height="8" rx="1.5"
                                                        fill="currentColor" />
                                                    <rect x="3" y="13" width="3" height="6" rx="1.5"
                                                        fill="currentColor" />
                                                </svg>
                                            </span>

                                            Movimentação </a>
                                        <!--end::Action-->
                                    </div>
                                    <!--end::Wrapper-->
                                </div>
                                <!--end::Notice-->
                            </a>
                            <!--end::Option-->
                        </div>
                        <!--end::Col-->
                        <!--begin::Col-->
                        <div class="col-6 col-sm-6  hover-elevate-up ">
                            <a href="{{ route('banco.create') }}" for="kt_create_account_form_account_type_personal"
                                class=" btn-outline btn-outline-dashed btn-active-light d-flex align-items-center">
                                <!--begin::Option-->
                                <!--begin::Notice-->
                                <div
                                    class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6">
                                    <!--begin::Svg Icon | path: icons/duotune/finance/fin006.svg-->
                                    <span class="svg-icon svg-icon-3x me-5">
                                        <svg version="1.1" id="_x34_" xmlns="http://www.w3.org/2000/svg"
                                            xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512.00 512.00"
                                            xml:space="preserve" width="256px" height="256px" fill="#000000"
                                            stroke="#000000" stroke-width="4.096">
                                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                stroke-linejoin="round" stroke="#CCCCCC" stroke-width="1.024">
                                            </g>
                                            <g id="SVGRepo_iconCarrier">
                                                <g>
                                                    <polygon style="fill:#EFEEEF;"
                                                        points="474.016,135.427 493.838,135.427 493.838,85.881 256.001,0 18.162,85.881 18.162,135.427 37.985,135.427 ">
                                                    </polygon>
                                                    <polygon style="fill:#E3E1E1;"
                                                        points="50.81,105.702 256.001,31.602 461.19,105.702 ">
                                                    </polygon>
                                                    <polygon style="fill:#CBCBCB;"
                                                        points="270.627,36.883 256.001,31.602 50.81,105.702 80.063,105.702 ">
                                                    </polygon>
                                                    <polygon style="fill:#CBCBCB;"
                                                        points="434.38,189.938 444.283,189.938 444.283,170.114 365.004,170.114 365.004,189.938 374.914,189.938 ">
                                                    </polygon>
                                                    <polygon style="fill:#CBCBCB;"
                                                        points="374.914,402.988 365.004,402.988 365.004,422.81 444.283,422.81 444.283,402.988 434.38,402.988 ">
                                                    </polygon>
                                                    <rect x="374.914" y="189.938" style="fill:#D8D8D9;" width="59.465"
                                                        height="213.05"></rect>
                                                    <rect x="226.267" y="189.938" style="fill:#D8D8D9;" width="59.457"
                                                        height="213.05"></rect>
                                                    <rect x="77.62" y="189.938" style="fill:#D8D8D9;" width="59.465"
                                                        height="213.05"></rect>
                                                    <g>
                                                        <rect x="102.397" y="189.938" style="fill:#CBCBCB;"
                                                            width="9.912" height="213.05"></rect>
                                                        <rect x="82.575" y="189.938" style="fill:#CBCBCB;"
                                                            width="9.919" height="213.05"></rect>
                                                        <rect x="122.219" y="189.938" style="fill:#CBCBCB;"
                                                            width="9.912" height="213.05"></rect>
                                                    </g>
                                                    <g>
                                                        <rect x="251.044" y="189.938" style="fill:#CBCBCB;"
                                                            width="9.912" height="213.05"></rect>
                                                        <rect x="231.231" y="189.938" style="fill:#CBCBCB;"
                                                            width="9.903" height="213.05"></rect>
                                                        <rect x="270.866" y="189.938" style="fill:#CBCBCB;"
                                                            width="9.903" height="213.05"></rect>
                                                    </g>
                                                    <g>
                                                        <rect x="399.693" y="189.938" style="fill:#CBCBCB;"
                                                            width="9.91" height="213.05"></rect>
                                                        <rect x="379.878" y="189.938" style="fill:#CBCBCB;"
                                                            width="9.903" height="213.05"></rect>
                                                        <rect x="419.515" y="189.938" style="fill:#CBCBCB;"
                                                            width="9.901" height="213.05"></rect>
                                                    </g>
                                                    <polygon style="fill:#CBCBCB;"
                                                        points="285.724,189.938 295.645,189.938 295.645,170.114 216.364,170.114 216.364,189.938 226.267,189.938 ">
                                                    </polygon>
                                                    <polygon style="fill:#CBCBCB;"
                                                        points="226.267,402.988 216.364,402.988 216.364,422.81 295.645,422.81 295.645,402.988 285.724,402.988 ">
                                                    </polygon>
                                                    <polygon style="fill:#CBCBCB;"
                                                        points="137.086,189.938 146.996,189.938 146.996,170.114 67.717,170.114 67.717,189.938 77.62,189.938 ">
                                                    </polygon>
                                                    <polygon style="fill:#CBCBCB;"
                                                        points="77.62,402.988 67.717,402.988 67.717,422.81 146.996,422.81 146.996,402.988 137.086,402.988 ">
                                                    </polygon>
                                                    <g>
                                                        <polygon style="fill:#EFEEEF;"
                                                            points="37.985,462.446 18.162,462.446 18.162,512 493.838,512 493.838,462.446 474.016,462.446 ">
                                                        </polygon>
                                                        <rect x="37.985" y="422.81" style="fill:#D8D8D9;"
                                                            width="436.031" height="39.637"></rect>
                                                    </g>
                                                    <rect x="37.985" y="135.427" style="fill:#D8D8D9;" width="436.031"
                                                        height="34.687"></rect>
                                                </g>
                                            </g>
                                        </svg>
                                    </span>
                                    <!--end::Icon-->
                                    <!--begin::Wrapper-->
                                    <div class="d-flex flex-stack flex-grow-1 flex-wrap flex-md-nowrap">
                                        <!--begin::Content-->
                                        <div class="mb-3 mb-md-0 fw-semibold">
                                            <h4 class="text-gray-900 fw-bold">Lançamentos Bancários</h4>
                                            <div class="text-muted fw-semibold fs-6">Transações realizadas através de
                                                contas bancárias</div>
                                        </div>
                                        <!--end::Content-->
                                        <!--begin::Action-->
                                        <a href="{{ route('banco.list') }}"
                                            class="btn btn-primary px-6 align-self-center ">
                                            <span class="svg-icon svg-icon-1">
                                                <svg width="24" height="24" viewBox="0 0 24 24"
                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <rect x="8" y="9" width="3" height="10" rx="1.5"
                                                        fill="currentColor" />
                                                    <rect opacity="0.5" x="13" y="5" width="3" height="14"
                                                        rx="1.5" fill="currentColor" />
                                                    <rect x="18" y="11" width="3" height="8" rx="1.5"
                                                        fill="currentColor" />
                                                    <rect x="3" y="13" width="3" height="6" rx="1.5"
                                                        fill="currentColor" />
                                                </svg>
                                            </span>

                                            Movimentação </a>
                                        <!--end::Action-->
                                    </div>
                                    <!--end::Wrapper-->
                                </div>
                                <!--end::Notice-->
                            </a>
                            <!--end::Option-->
                        </div>
                        <!--end::Col-->

                    </div>
                </div>
                    <!--end::Row-->
                    <!--end::Input group-->
                    <!--begin::Toolbar-->
                    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                        <!--begin::Toolbar container-->
                        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                            <!--begin::Page title-->
                            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                                <!--begin::Title-->
                                <h1
                                    class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                                    Últimos Lançamentos</h1>
                                <!--end::Title-->
                                <!--begin::Breadcrumb-->
                                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                                    <!--begin::Item-->
                                    <li class="breadcrumb-item text-muted">
                                        Caixa local e bancarios
                                    </li>
                                    <!--end::Item-->
                                </ul>
                                <!--end::Breadcrumb-->
                            </div>
                            <!--end::Page title-->
                            <!--begin::Actions-->
                            {{-- <div class="d-flex align-items-center gap-2 gap-lg-3">
										<!--begin::Filter menu-->
										<div class="d-flex">
											<select name="campaign-type" data-control="select2" data-hide-search="true" class="form-select form-select-sm bg-body border-body w-175px">
												<option value="Twitter" selected="selected">Select Campaign</option>
												<option value="Twitter">Twitter Campaign</option>
												<option value="Twitter">Facebook Campaign</option>
												<option value="Twitter">Adword Campaign</option>
												<option value="Twitter">Carbon Campaign</option>
											</select>
											<a href="#" class="btn btn-icon btn-sm btn-success flex-shrink-0 ms-4" data-bs-toggle="modal" data-bs-target="#kt_modal_create_campaign">
												<!--begin::Svg Icon | path: icons/duotune/arrows/arr075.svg-->
												<span class="svg-icon svg-icon-2">
													<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
														<rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor" />
														<rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor" />
													</svg>
												</span>
												<!--end::Svg Icon-->
											</a>
										</div>
										<!--end::Filter menu-->
										<!--begin::Secondary button-->
										<!--end::Secondary button-->
										<!--begin::Primary button-->
										<!--end::Primary button-->
									</div> --}}
                            <!--end::Actions-->
                        </div>
                        <!--end::Toolbar container-->
                    </div>
                    <!--end::Toolbar-->

                    <!--begin::Card-->
                    <div class="card">
                        <!--begin::Card header-->
                        <div class="card-header border-0 pt-6">
                            <!--begin::Card title-->
                            <div class="card-title">
                                <!--begin::Search-->
                                <div class="d-flex align-items-center position-relative my-1">
                                    <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                                    <span class="svg-icon svg-icon-1 position-absolute ms-6">
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
                                    <input type="text" data-kt-user-table-filter="search"
                                        class="form-control form-control-solid w-250px ps-14"
                                        placeholder="Buscar lançamento" />
                                </div>
                                <!--end::Search-->
                            </div>
                            <!--begin::Card title-->
                            <!--begin::Card toolbar-->
                            <div class="card-toolbar">
                                <!--begin::Toolbar-->
                                <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                                    <!--begin::Filter-->
                                    <button type="button" class="btn btn-light-primary me-3"
                                        data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                        <!--begin::Svg Icon | path: icons/duotune/general/gen031.svg-->
                                        <span class="svg-icon svg-icon-2">
                                            <svg height="200px" width="200px" version="1.1" id="_x34_"
                                                xmlns="http://www.w3.org/2000/svg"
                                                xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512 512"
                                                xml:space="preserve" fill="#000000">
                                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                    stroke-linejoin="round"></g>
                                                <g id="SVGRepo_iconCarrier">
                                                    <g>
                                                        <path style="fill:#F5BAAD;"
                                                            d="M45.334,230.381c-18.877,0-20.453,0-31.465,0c-11.012,0-20.453,15.73-7.865,28.318 c12.589,12.583,18.883,22.023,18.883,22.023L45.334,230.381z">
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
                                                        <circle style="fill:#F3A7A8;" cx="332.664" cy="372.173"
                                                            r="41.102"></circle>
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
                                        </span>
                                        <!--end::Svg Icon-->Caixa</button>
                                    <!--begin::Menu 1-->
                                    <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px"
                                        data-kt-menu="true">
                                        <!--begin::Header-->
                                        <div class="px-7 py-5">
                                            <div class="fs-5 text-dark fw-bold">Opções de Filto</div>
                                        </div>
                                        <!--end::Header-->
                                        <!--begin::Separator-->
                                        <div class="separator border-gray-200"></div>
                                        <!--end::Separator-->
                                        <!--begin::Content-->
                                        <div class="px-7 py-5" data-kt-user-table-filter="form">
                                            <!--begin::Input group-->
                                            <div class="mb-10">
                                                <label class="form-label fs-6 fw-semibold">Tipo:</label>
                                                <select class="form-select form-select-solid fw-bold"
                                                    data-kt-select2="true" data-placeholder="Select option"
                                                    data-allow-clear="true" data-kt-user-table-filter="tipo"
                                                    data-hide-search="true">
                                                    <option></option>
                                                    <option value="entrada">Entrada</option>
                                                    <option value="saida">Saída</option>
                                                </select>
                                            </div>
                                            <!--end::Input group-->
                                            <!--begin::Input group-->
                                            <div class="mb-10">
                                                <label class="form-label fs-6 fw-semibold">Two Step
                                                    Verification:</label>
                                                <select class="form-select form-select-solid fw-bold"
                                                    data-kt-select2="true" data-placeholder="Select option"
                                                    data-allow-clear="true" data-kt-user-table-filter="two-step"
                                                    data-hide-search="true">
                                                    <option></option>
                                                    <option value="Enabled">Filtrar</option>
                                                </select>
                                            </div>
                                            <!--end::Input group-->
                                            <!--begin::Actions-->
                                            <div class="d-flex justify-content-end">
                                                <button type="reset"
                                                    class="btn btn-light btn-active-light-primary fw-semibold me-2 px-6"
                                                    data-kt-menu-dismiss="true"
                                                    data-kt-user-table-filter="reset">Reset</button>
                                                <button type="submit" class="btn btn-primary fw-semibold px-6"
                                                    data-kt-menu-dismiss="true"
                                                    data-kt-user-table-filter="filter">Apply</button>
                                            </div>
                                            <!--end::Actions-->
                                        </div>
                                        <!--end::Content-->
                                    </div>
                                    <!--end::Menu 1-->
                                    <!--end::Filter-->
                                    <!--begin::Export-->
                                    <button type="button" class="btn btn-light-primary me-3" data-bs-toggle="modal"
                                        data-bs-target="#kt_modal_export_users">
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
                                        <!--end::Svg Icon-->Bancos</button>
                                    <!--end::Export-->
                                    <!--begin::Add user-->
                                    {{-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_user">
													<!--begin::Svg Icon | path: icons/duotune/arrows/arr075.svg-->
													<span class="svg-icon svg-icon-2">
														<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
															<rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor" />
															<rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor" />
														</svg>
													</span>
													<!--end::Svg Icon-->Add User</button>
													<!--end::Add user--> --}}
                                </div>
                                <!--end::Toolbar-->
                                <!--begin::Group actions-->
                                <div class="d-flex justify-content-end align-items-center d-none"
                                    data-kt-user-table-toolbar="selected">
                                    <div class="fw-bold me-5">
                                        <span class="me-2"
                                            data-kt-user-table-select="selected_count"></span>Selecioado
                                    </div>
                                    <button type="button" class="btn btn-danger"
                                        data-kt-user-table-select="delete_selected">Excluir Selecionado</button>
                                </div>
                                <!--end::Group actions-->
                                <!--begin::Modal - Adjust Balance-->
                                <div class="modal fade" id="kt_modal_export_users" tabindex="-1"
                                    aria-hidden="true">
                                    <!--begin::Modal dialog-->
                                    <div class="modal-dialog modal-dialog-centered mw-650px">
                                        <!--begin::Modal content-->
                                        <div class="modal-content">
                                            <!--begin::Modal header-->
                                            <div class="modal-header">
                                                <!--begin::Modal title-->
                                                <h2 class="fw-bold">Export Users</h2>
                                                <!--end::Modal title-->
                                                <!--begin::Close-->
                                                <div class="btn btn-icon btn-sm btn-active-icon-primary"
                                                    data-kt-users-modal-action="close">
                                                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
                                                    <span class="svg-icon svg-icon-1">
                                                        <svg width="24" height="24" viewBox="0 0 24 24"
                                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <rect opacity="0.5" x="6" y="17.3137" width="16"
                                                                height="2" rx="1"
                                                                transform="rotate(-45 6 17.3137)"
                                                                fill="currentColor" />
                                                            <rect x="7.41422" y="6" width="16" height="2"
                                                                rx="1" transform="rotate(45 7.41422 6)"
                                                                fill="currentColor" />
                                                        </svg>
                                                    </span>
                                                    <!--end::Svg Icon-->
                                                </div>
                                                <!--end::Close-->
                                            </div>
                                            <!--end::Modal header-->
                                            <!--begin::Modal body-->
                                            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                                                <!--begin::Form-->
                                                <form id="kt_modal_export_users_form" class="form" action="#">
                                                    <!--begin::Input group-->
                                                    <div class="fv-row mb-10">
                                                        <!--begin::Label-->
                                                        <label class="fs-6 fw-semibold form-label mb-2">Select
                                                            Roles:</label>
                                                        <!--end::Label-->
                                                        <!--begin::Input-->
                                                        <select name="tipo" data-control="select2"
                                                            data-placeholder="Select a role" data-hide-search="true"
                                                            class="form-select form-select-solid fw-bold">
                                                            <option></option>
                                                            <option value="entrada">Entrada</option>
                                                            <option value="saida">Saída</option>
                                                        </select>
                                                        <!--end::Input-->
                                                    </div>
                                                    <!--end::Input group-->
                                                    <!--begin::Input group-->
                                                    <div class="fv-row mb-10">
                                                        <!--begin::Label-->
                                                        <label
                                                            class="required fs-6 fw-semibold form-label mb-2">Selecione
                                                            o formato:</label>
                                                        <!--end::Label-->
                                                        <!--begin::Input-->
                                                        <select name="format" data-control="select2"
                                                            data-placeholder="Select a format" data-hide-search="true"
                                                            class="form-select form-select-solid fw-bold">
                                                            <option></option>
                                                            <option value="excel">Excel</option>
                                                            <option value="pdf">PDF</option>
                                                            <option value="cvs">CVS</option>
                                                            <option value="zip">ZIP</option>
                                                        </select>
                                                        <!--end::Input-->
                                                    </div>
                                                    <!--end::Input group-->
                                                    <!--begin::Actions-->
                                                    <div class="text-center">
                                                        <button type="reset" class="btn btn-light me-3"
                                                            data-kt-users-modal-action="cancel">Discard</button>
                                                        <button type="submit" class="btn btn-primary"
                                                            data-kt-users-modal-action="submit">
                                                            <span class="indicator-label">Submit</span>
                                                            <span class="indicator-progress">Please wait...
                                                                <span
                                                                    class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                                        </button>
                                                    </div>
                                                    <!--end::Actions-->
                                                </form>
                                                <!--end::Form-->
                                            </div>
                                            <!--end::Modal body-->
                                        </div>
                                        <!--end::Modal content-->
                                    </div>
                                    <!--end::Modal dialog-->
                                </div>
                                <!--end::Modal - New Card-->
                                <!--begin::Modal - Add task-->
                                <div class="modal fade" id="kt_modal_add_user" tabindex="-1" aria-hidden="true">
                                    <!--begin::Modal dialog-->
                                    <div class="modal-dialog modal-dialog-centered mw-650px">
                                        <!--begin::Modal content-->
                                        <div class="modal-content">
                                            <!--begin::Modal header-->
                                            <div class="modal-header" id="kt_modal_add_user_header">
                                                <!--begin::Modal title-->
                                                <h2 class="fw-bold">Add User</h2>
                                                <!--end::Modal title-->
                                                <!--begin::Close-->
                                                <div class="btn btn-icon btn-sm btn-active-icon-primary"
                                                    data-kt-users-modal-action="close">
                                                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
                                                    <span class="svg-icon svg-icon-1">
                                                        <svg width="24" height="24" viewBox="0 0 24 24"
                                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <rect opacity="0.5" x="6" y="17.3137" width="16"
                                                                height="2" rx="1"
                                                                transform="rotate(-45 6 17.3137)"
                                                                fill="currentColor" />
                                                            <rect x="7.41422" y="6" width="16" height="2"
                                                                rx="1" transform="rotate(45 7.41422 6)"
                                                                fill="currentColor" />
                                                        </svg>
                                                    </span>
                                                    <!--end::Svg Icon-->
                                                </div>
                                                <!--end::Close-->
                                            </div>
                                            <!--end::Modal header-->
                                        </div>
                                        <!--end::Modal content-->
                                    </div>
                                    <!--end::Modal dialog-->
                                </div>
                                <!--end::Modal - Add task-->
                            </div>
                            <!--end::Card toolbar-->
                        </div>
                        <!--end::Card header-->

                        <!--begin::Card body-->
                        <div class="card-body py-4">
                            <!--begin::Table-->
                            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
                                <!--begin::Table head-->
                                <thead>
                                    <!--begin::Table row-->
                                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                        <th class="w-10px pe-2">
                                            <div
                                                class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                                <input class="form-check-input" type="checkbox" data-kt-check="true"
                                                    data-kt-check-target="#kt_table_lancamento .form-check-input"
                                                    value="1" />
                                            </div>
                                        </th>
                                        <th class="min-w-75px">ID</th>
                                        <th class="min-w-100px">Data</th>
                                        <th class="min-w-150px">Tipo Docuemnto</th>
                                        <th class="min-w-400px">Documento</th>
                                        <th class="min-w-125px">Tipo</th>
                                        <th class="min-w-125px">Valor</th>
                                        <th class="min-w-75px">Origem</th>
                                        <th class="text-end min-w-100px">Ações</th>
                                    </tr>
                                    <!--end::Table row-->
                                </thead>
                                <!--end::Table head-->
                                <!--begin::Table body-->
                                <tbody class="text-gray-600 fw-semibold">
                                    <!--begin::Table row-->
                                    @foreach ($caixas as $caixa)
                                        <tr>
                                            <!--begin::Checkbox-->
                                            <td>
                                                <div
                                                    class="form-check form-check-sm form-check-custom form-check-solid">
                                                    <input class="form-check-input" type="checkbox"
                                                        value="{{ $caixa->id }}" />
                                                </div>
                                            </td>
                                            <!--end::Checkbox-->
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
                                                            class="menu-link px-3">Editar</a>
                                                        <a href="#" class="menu-link px-3 delete-link"
                                                            data-id="{{ $caixa->id }}">Excluir</a>
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
                    <!--end::Card-->

                </div>
                <!--end::Content container-->
            </div>
            <!--end::Content-->
        </div>
        <!--end::Content wrapper-->
        <!--begin::Modal - Upgrade plan-->
        <div class="modal fade" id="kt_modal_new_target" data-bs-backdrop="static" data-bs-keyboard="false"
            tabindex="-1" aria-hidden="true">
            <!--begin::Modal dialog-->
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <!--begin::Modal content-->
                <div class="modal-content rounded">
                    <!--begin::Modal header-->
                    <div class="modal-header justify-content-end border-0 pb-0">
                        <!--begin::Close-->
                        <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                            <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
                            <span class="svg-icon svg-icon-1">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <rect opacity="0.5" x="6" y="17.3137" width="16" height="2"
                                        rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                    <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                        transform="rotate(45 7.41422 6)" fill="currentColor" />
                                </svg>
                            </span>
                            <!--end::Svg Icon-->
                        </div>
                        <!--end::Close-->
                    </div>
                    <!--end::Modal header-->
                    <!--begin::Modal body-->
                    <div class="modal-body pt-0 pb-15 px-5 px-xl-20">
                        <!--begin::Heading-->
                        <div class="mb-5 text-center">
                            <span class="svg-icon svg-icon-3x me-5">
                                <h1 class="mb-1">
                                    <svg version="1.1" id="_x34_" width="35" height="35" id="_x34_"
                                        xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                        viewBox="0 0 512 512" xml:space="preserve" fill="#000000">
                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round">
                                        </g>
                                        <g id="SVGRepo_iconCarrier">
                                            <g>
                                                <path style="fill:#F5BAAD;"
                                                    d="M45.334,230.381c-18.877,0-20.453,0-31.465,0c-11.012,0-20.453,15.73-7.865,28.318 c12.589,12.583,18.883,22.023,18.883,22.023L45.334,230.381z">
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
                                                <circle style="fill:#F3A7A8;" cx="332.664" cy="372.173"
                                                    r="41.102"></circle>
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
                                    Informações do lançamento
                                </h1>
                            </span>
                            <div class="text-muted fw-semibold fs-5">Para mais detalhes, consulte as
                                <a href="#" class="link-primary fw-bold">diretrizes de lançamento</a>.
                            </div>
                        </div>
                        <!--end::Heading-->
                        <!--begin::Plans-->
                        <div class="d-flex flex-column">
                            <!--begin::Row-->
                            <div class="row mt-10">
                                <!--begin::Col-->
                                <!--begin:Form-->
                                <form id="kt_modal_new_target_form" class="form"
                                    action="{{ route('caixa.store') }}" method="POST" enctype="multipart/form-data"
                                    novalidate>
                                    @csrf <!-- Token CSRF para Laravel -->
                                    <!--begin::Input group-->
                                    <div class="row g-9 mb-8">
                                        <!--begin::Col-->
                                        <div class="col-md-3 fv-row">
                                            <label class="required fs-6 fw-semibold mb-2">Data</label>
                                            <!--begin::Input-->
                                            <div class="position-relative d-flex align-items-center">
                                                <!--begin::Icon-->
                                                <!--begin::Svg Icon | path: icons/duotune/general/gen014.svg-->
                                                <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                                    <svg width="24" height="24" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path opacity="0.3"
                                                            d="M21 22H3C2.4 22 2 21.6 2 21V5C2 4.4 2.4 4 3 4H21C21.6 4 22 4.4 22 5V21C22 21.6 21.6 22 21 22Z"
                                                            fill="currentColor" />
                                                        <path
                                                            d="M6 6C5.4 6 5 5.6 5 5V3C5 2.4 5.4 2 6 2C6.6 2 7 2.4 7 3V5C7 5.6 6.6 6 6 6ZM11 5V3C11 2.4 10.6 2 10 2C9.4 2 9 2.4 9 3V5C9 5.6 9.4 6 10 6C10.6 6 11 5.6 11 5ZM15 5V3C15 2.4 14.6 2 14 2C13.4 2 13 2.4 13 3V5C13 5.6 13.4 6 14 6C14.6 6 15 5.6 15 5ZM19 5V3C19 2.4 18.6 2 18 2C17.4 2 17 2.4 17 3V5C17 5.6 17.4 6 18 6C18.6 6 19 5.6 19 5Z"
                                                            fill="currentColor" />
                                                        <path
                                                            d="M8.8 13.1C9.2 13.1 9.5 13 9.7 12.8C9.9 12.6 10.1 12.3 10.1 11.9C10.1 11.6 10 11.3 9.8 11.1C9.6 10.9 9.3 10.8 9 10.8C8.8 10.8 8.59999 10.8 8.39999 10.9C8.19999 11 8.1 11.1 8 11.2C7.9 11.3 7.8 11.4 7.7 11.6C7.6 11.8 7.5 11.9 7.5 12.1C7.5 12.2 7.4 12.2 7.3 12.3C7.2 12.4 7.09999 12.4 6.89999 12.4C6.69999 12.4 6.6 12.3 6.5 12.2C6.4 12.1 6.3 11.9 6.3 11.7C6.3 11.5 6.4 11.3 6.5 11.1C6.6 10.9 6.8 10.7 7 10.5C7.2 10.3 7.49999 10.1 7.89999 10C8.29999 9.90003 8.60001 9.80003 9.10001 9.80003C9.50001 9.80003 9.80001 9.90003 10.1 10C10.4 10.1 10.7 10.3 10.9 10.4C11.1 10.5 11.3 10.8 11.4 11.1C11.5 11.4 11.6 11.6 11.6 11.9C11.6 12.3 11.5 12.6 11.3 12.9C11.1 13.2 10.9 13.5 10.6 13.7C10.9 13.9 11.2 14.1 11.4 14.3C11.6 14.5 11.8 14.7 11.9 15C12 15.3 12.1 15.5 12.1 15.8C12.1 16.2 12 16.5 11.9 16.8C11.8 17.1 11.5 17.4 11.3 17.7C11.1 18 10.7 18.2 10.3 18.3C9.9 18.4 9.5 18.5 9 18.5C8.5 18.5 8.1 18.4 7.7 18.2C7.3 18 7 17.8 6.8 17.6C6.6 17.4 6.4 17.1 6.3 16.8C6.2 16.5 6.10001 16.3 6.10001 16.1C6.10001 15.9 6.2 15.7 6.3 15.6C6.4 15.5 6.6 15.4 6.8 15.4C6.9 15.4 7.00001 15.4 7.10001 15.5C7.20001 15.6 7.3 15.6 7.3 15.7C7.5 16.2 7.7 16.6 8 16.9C8.3 17.2 8.6 17.3 9 17.3C9.2 17.3 9.5 17.2 9.7 17.1C9.9 17 10.1 16.8 10.3 16.6C10.5 16.4 10.5 16.1 10.5 15.8C10.5 15.3 10.4 15 10.1 14.7C9.80001 14.4 9.50001 14.3 9.10001 14.3C9.00001 14.3 8.9 14.3 8.7 14.3C8.5 14.3 8.39999 14.3 8.39999 14.3C8.19999 14.3 7.99999 14.2 7.89999 14.1C7.79999 14 7.7 13.8 7.7 13.7C7.7 13.5 7.79999 13.4 7.89999 13.2C7.99999 13 8.2 13 8.5 13H8.8V13.1ZM15.3 17.5V12.2C14.3 13 13.6 13.3 13.3 13.3C13.1 13.3 13 13.2 12.9 13.1C12.8 13 12.7 12.8 12.7 12.6C12.7 12.4 12.8 12.3 12.9 12.2C13 12.1 13.2 12 13.6 11.8C14.1 11.6 14.5 11.3 14.7 11.1C14.9 10.9 15.2 10.6 15.5 10.3C15.8 10 15.9 9.80003 15.9 9.70003C15.9 9.60003 16.1 9.60004 16.3 9.60004C16.5 9.60004 16.7 9.70003 16.8 9.80003C16.9 9.90003 17 10.2 17 10.5V17.2C17 18 16.7 18.4 16.2 18.4C16 18.4 15.8 18.3 15.6 18.2C15.4 18.1 15.3 17.8 15.3 17.5Z"
                                                            fill="currentColor" />
                                                    </svg>
                                                </span>
                                                <!--end::Svg Icon-->
                                                <!--end::Icon-->
                                                <!--begin::Datepicker-->
                                                <input class="form-control form-control-solid ps-12"
                                                    placeholder="Selecione a data" name="data_competencia" />
                                                <!--end::Datepicker-->
                                            </div>
                                            <!--end::Input-->
                                        </div>
                                        <!--end::Col-->
                                        <!--begin::Col-->
                                        <div class="col-md-6 fv-row">
                                            <!--begin::Label-->
                                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                                <span class="">Descrição</span>
                                                <i class="fas fa-exclamation-circle ms-2 fs-7"
                                                    data-bs-toggle="tooltip"
                                                    title="Specify a target name for future usage and reference"></i>
                                            </label>
                                            <!--end::Label-->
                                            <input type="text" class="form-control form-control-solid"
                                                placeholder="Descrição do lançamento" name="descricao" />
                                        </div>
                                        <div class="col-md-3 fv-row">
                                            <label class="required fs-5 fw-semibold mb-2">Valor</label>
                                            <div class="position-relative d-flex align-items-center">
                                                <!--begin::Icon-->
                                                <!--begin::Svg Icon | path: icons/duotune/general/gen014.svg-->
                                                <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                                    <svg class="icon icon-tabler icon-tabler-currency-real"
                                                        fill="none" height="24" stroke="currentColor"
                                                        stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" viewBox="0 0 24 24" width="24"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <!-- O preenchimento inicial não está definido -->
                                                        <path d="M0 0h24v24H0z" fill="none" stroke="none"></path>
                                                        <!-- Desenha a primeira linha que representa o símbolo da moeda -->
                                                        <path d="M21 6h-4a3 3 0 0 0 0 6h1a3 3 0 0 1 0 6h-4"></path>
                                                        <!-- Traça a segunda linha da moeda -->
                                                        <path d="M4 18v-12h3a3 3 0 1 1 0 6h-3c5.5 0 5 4 6 6"></path>
                                                        <!-- Traça duas linhas verticais curtas -->
                                                        <path d="M18 6v-2"></path>
                                                        <path d="M17 20v-2"></path>
                                                    </svg>
                                                </span>
                                                <!--end::Svg Icon-->
                                                <!--end::Icon-->
                                                <input class="form-control form-control-solid ps-12 money"
                                                    placeholder="Valor" aria-label="Valor"
                                                    aria-describedby="basic-addon1" id="valor" name="valor"
                                                    required value="{{ old('valor') }}" />
                                            </div>
                                            @error('valor')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <!--end::Input group-->
                                    <div class="row g-9 mb-5">
                                        <div class="col-md-2 fv-row">
                                            <label class="d-flex align-items-center fs-5 fw-semibold mb-2">
                                                <span class="required">Entrada/Saída</span>
                                                <i class="fas fa-exclamation-circle ms-2 fs-7"
                                                    data-bs-toggle="tooltip"
                                                    title="As categorias são utilizadas para formar um Plano de Contas. Muitas destas categorias são demonstradas em Relatórios e também alimentam o DRE Gerencial."></i>
                                            </label>
                                            <select class="form-select form-select-solid" data-control="select"
                                                data-dropdown-css-class="w-200px" data-placeholder="Selecione o tipo"
                                                name="tipo" required data-hide-search="true" id="tipo_select">
                                                <option value="" disabled selected>Selecione o tipo</option>
                                                <option value="entrada"
                                                    {{ old('tipo') == 'entrada' ? 'selected' : '' }}>Entrada</option>
                                                <option value="saida" {{ old('tipo') == 'saida' ? 'selected' : '' }}>
                                                    Saída</option>
                                            </select>
                                            @error('tipo')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 fv-row">
                                            <label class="required fs-5 fw-semibold mb-2">Lançamento Padrão</label>
                                            <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                                                title="As categorias são utilizadas para formar um Plano de Contas. Muitas destas categorias são demonstradas em Relatórios e também alimentam o DRE Gerencial."></i>
                                            <div class="input-group">
                                                <select name="lancamento_padrao" aria-label="Select a Country"
                                                    data-control="select2" data-placeholder="Escolha um Lançamento..."
                                                    class="form-select form-select-solid fw-bold"
                                                    id="lancamento_padrao">
                                                    <option value=""></option>
                                                    @foreach ($lps as $lp)
                                                        <option value="{{ $lp->description }}"
                                                            data-type="{{ $lp->type }}">{{ $lp->description }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            @error('lancamento_padrao')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4 fv-row">
                                            <label class="fs-5 fw-semibold mb-2">Centro de Custo</label>
                                            <div class="input-group">
                                                <input type="text" name="centro" readonly
                                                    class="form-control form-control-solid" placeholder=""
                                                    value="{{ $company->name }}" />
                                            </div>
                                            @error('centro')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <!--begin::Input group-->
                                    <div class="row g-9 mb-5">
                                        <!--begin::Col-->
                                        <div class="col-md-4 fv-row">
                                            <label class="required fs-6 fw-semibold mb-2">Tipo de Documento</label><i
                                                class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                                                title="As categorias são utilizadas para formar um Plano de Contas. Muitas destas categorias são demonstradas em Relatórios e também alimentam o DRE Gerencial."></i>
                                            <select class="form-select form-select-solid" data-control="select2"
                                                data-hide-search="true" data-placeholder="Select a Team Member"
                                                name="tipo_documento" id="tipo_documento">
                                                <option value="Pix"
                                                    {{ old('tipo_documento') == 'Pix' ? 'selected' : '' }}>Pix
                                                </option>
                                                <option value="OUTR - Dafe"
                                                    {{ old('tipo_documento') == 'OUTR - Dafe' ? 'selected' : '' }}>
                                                    OUTR - Dafe</option>
                                                <option value="NF - Nota Fiscal"
                                                    {{ old('tipo_documento') == 'NF - Nota Fiscal' ? 'selected' : '' }}>
                                                    NF - Nota Fiscal</option>
                                                <option value="CF - Cupom Fiscal"
                                                    {{ old('tipo_documento') == 'CF - Cupom Fiscal' ? 'selected' : '' }}>
                                                    CF - Cupom Fiscal</option>
                                                <option value="DANF - Danfe"
                                                    {{ old('tipo_documento') == 'DANF - Danfe' ? 'selected' : '' }}>
                                                    DANF - Danfe</option>
                                                <option value="BOL - Boleto"
                                                    {{ old('tipo_documento') == 'BOL - Boleto' ? 'selected' : '' }}>
                                                    BOL - Boleto</option>
                                                <option value="REP - Repasse"
                                                    {{ old('tipo_documento') == 'REP - Repasse' ? 'selected' : '' }}>
                                                    REP - Repasse</option>
                                                <option value="CCRD - Cartão de Credito"
                                                    {{ old('tipo_documento') == 'CCRD - Cartão de Credito' ? 'selected' : '' }}>
                                                    CCRD - Cartão de Credito</option>
                                                <option value="CDBT - Cartão de Debito"
                                                    {{ old('tipo_documento') == 'CDBT - Cartão de Debito' ? 'selected' : '' }}>
                                                    CDBT - Cartão de Debito</option>
                                                <option value="CH - Cheque"
                                                    {{ old('tipo_documento') == 'CH - Cheque' ? 'selected' : '' }}>
                                                    CH - Cheque</option>
                                                <option value="REC - Recibo"
                                                    {{ old('tipo_documento') == 'REC - Recibo' ? 'selected' : '' }}>
                                                    REC - Recibo</option>
                                                <option value="CARN - Carnê"
                                                    {{ old('tipo_documento') == 'CARN - Carnê' ? 'selected' : '' }}>
                                                    CARN - Carnê</option>
                                                <option value="FAT - Fatura"
                                                    {{ old('tipo_documento') == 'FAT - Fatura' ? 'selected' : '' }}>
                                                    FAT - Fatura</option>
                                                <option value="APOL - Apólice"
                                                    {{ old('tipo_documento') == 'APOL - Apólice' ? 'selected' : '' }}>
                                                    APOL - Apólice</option>
                                                <option value="DUPL - Duplicata"
                                                    {{ old('tipo_documento') == 'DUPL - Duplicata' ? 'selected' : '' }}>
                                                    DUPL - Duplicata</option>
                                                <option value="TRIB - Tribunal"
                                                    {{ old('tipo_documento') == 'TRIB - Tribunal' ? 'selected' : '' }}>
                                                    TRIB - Tribunal</option>
                                                <option value="Outros"
                                                    {{ old('tipo_documento') == 'Outros' ? 'selected' : '' }}>
                                                    Outros</option>
                                                <option value="T Banc - Transferência Bancaria"
                                                    {{ old('tipo_documento') == 'T Banc - Transferência Bancaria' ? 'selected' : '' }}>
                                                    T Banc - Transferência Bancaria</option>
                                            </select>
                                        </div>
                                        <!--end::Col-->
                                        <div class="col-md-4 fv-row">
                                            <label class="fs-5 fw-semibold mb-2">Número do Documento</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control form-control-solid"
                                                    placeholder="" name="numero_documento"
                                                    value="{{ old('numero_documento') }}" />
                                            </div>
                                            @error('numero_documento')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <!-- Novo campo de entrada para o banco de depósito -->
                                        <div class="col-md-4 fv-row" id="banco-deposito" style="display:none;">
                                            <label class=" fs-5 fw-semibold mb-2">Banco de Depósito</label>
                                            <select id="bancoSelect" name="banco_id" aria-label="Select a Banco"
                                                data-control="select2" data-placeholder="Escolha um banco..."
                                                class="form-select form-select-solid">
                                                <option value=""></option>
                                                @foreach ($bancos as $banco)
                                                    <option data-banco-code="{{ $banco->banco }}"
                                                        value="{{ $banco->id }}"><span
                                                            class="banco-name"></span>{{ $banco->banco }} -
                                                        {{ $banco->name }}/{{ $banco->conta }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <!--end::Input group-->
                                    <!--begin::Input group-->
                                    <div class="d-flex flex-column mb-8">
                                        <div class="d-flex flex-column mb-5 fv-row">
                                            <ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
                                                <li class="nav-item">
                                                    <a class="nav-link active" data-bs-toggle="tab"
                                                        href="#kt_tab_pane_1">Histórico complementar</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" data-bs-toggle="tab"
                                                        href="#kt_tab_pane_2">Anexos</a>
                                                </li>
                                            </ul>
                                            <div class="tab-content" id="myTabContent">
                                                <div class="tab-pane fade show active" id="kt_tab_pane_1"
                                                    role="tabpanel">
                                                    <textarea class="form-control" name="historico_complementar" id="complemento" cmaxlength="250" rows="3"
                                                        name="target_details" placeholder="Mais detalhes sobre o foro"></textarea>
                                                    <span class="fs-6 text-muted">Insira no máximo 250
                                                        caracteres</span>
                                                </div>
                                                <div class="tab-pane fade" id="kt_tab_pane_2" role="tabpanel">
                                                    <input type="file" name="files[]" id="photos" />
                                                    <script>
                                                        $("#photos").kendoUpload({
                                                            async: {
                                                                removeUrl: "{{ url('/remove') }}",
                                                                removeField: "path",
                                                                withCredentials: false
                                                            },
                                                            multiple: true, // Permite a seleção de múltiplos arquivos
                                                            validation: {
                                                                allowedExtensions: ["jpg", "jpeg", "png", "pdf", "page"], // Extensões permitidas
                                                                maxFileSize: 5242880, // Tamanho máximo do arquivo (5 MB)
                                                                minFileSize: 1024 // Tamanho mínimo do arquivo (1 KB)
                                                            },
                                                            localization: {
                                                                uploadSuccess: "Upload bem-sucedido!",
                                                                uploadFail: "Falha no upload",
                                                                invalidFileExtension: "Tipo de arquivo não permitido",
                                                                invalidMaxFileSize: "O arquivo é muito grande",
                                                                invalidMinFileSize: "O arquivo é muito pequeno",
                                                                select: "Anexar Arquivos"

                                                            }
                                                        });
                                                    </script>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end::Input group-->
                                    <!--begin::Actions-->
                                    <div class="d-flex flex-center flex-row-fluid pt-6 ">
                                        <button type="reset" class="btn btn-light me-3"
                                            data-bs-dismiss="modal">Cancelar</button>
                                        <a href="{{ route('caixa.list') }}"
                                            class="btn btn-warning me-3">Pesquisar</a>
                                        <button type="submit" class="btn btn-primary"
                                            id="kt_modal_new_target_submit">
                                            <!--begin::Indicator label-->
                                            <span class="indicator-label">Lançamento</span>
                                            <!--end::Indicator label-->
                                            <!--begin::Indicator progress-->
                                            <span class="indicator-progress">Por favor, espere...
                                                <span
                                                    class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                            <!--end::Indicator progress-->
                                        </button>
                                    </div>
                                    <!--end::Actions-->
                                </form>
                                <!--end:Form-->
                                <!--end::Col-->
                            </div>
                            <!--end::Row-->
                        </div>
                        <!--end::Plans-->
                    </div>
                    <!--end::Modal body-->
                </div>
                <!--end::Modal content-->
            </div>
            <!--end::Modal dialog-->
        </div>
        <!--end::Modal - Upgrade plan-->
    </div>
    <!--end:::Main-->



</x-tenant-app-layout>


<!--begin::Vendors Javascript(used for this page only)-->
<script src="assets/plugins/custom/datatables/datatables.bundle.js"></script>
<!--end::Vendors Javascript-->
<link href="assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
<script src="assets/js/scripts.bundle.js"></script>
<!--begin::Custom Javascript(used for this page only)-->
<script src="assets/js/custom/pages/user-profile/general.js"></script>
<script src="assets/js/custom/account/settings/signin-methods.js"></script>
<script src="assets/js/custom/account/security/security-summary.js"></script>
<script src="assets/js/custom/account/security/license-usage.js"></script>
<script src="assets/js/custom/account/settings/deactivate-account.js"></script>
<script src="assets/js/widgets.bundle.js"></script>
<script src="assets/js/custom/apps/chat/chat.js"></script>

<script src="assets/js/custom/utilities/modals/financeiro/new-caixa.js"></script>

<!--end::Custom Javascript-->
<script src="assets/js/custom/apps/lancamento/excluirCaixa.js"></script>

<!--begin::Custom Javascript(used for this page only)-->
<script src="assets/js/custom/apps/user-management/users/list/table.js"></script>
<script src="assets/js/custom/apps/user-management/users/list/export-users.js"></script>
<script src="assets/js/custom/apps/user-management/users/list/add.js"></script>
<!--end::Custom Javascript-->
<!--end::Javascript-->


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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tipoSelect = document.getElementById('tipo_select');
        const lancamentoPadraoSelect = document.getElementById('lancamento_padrao');

        tipoSelect.addEventListener('change', function() {
            const selectedTipo = tipoSelect.value;

            // Limpa todas as opções do select de Lançamento Padrão
            lancamentoPadraoSelect.innerHTML = '';

            // Adiciona a opção vazia
            const emptyOption = document.createElement('option');
            emptyOption.value = '';
            emptyOption.text = 'Escolha um Lançamento...';
            lancamentoPadraoSelect.appendChild(emptyOption);

            // Filtra e adiciona as opções de acordo com o tipo selecionado
            @foreach ($lps as $lp)
                if ('{{ $lp->type }}' === selectedTipo) {
                    const option = document.createElement('option');
                    option.value = '{{ $lp->description }}';
                    option.text = '{{ $lp->description }}';
                    lancamentoPadraoSelect.appendChild(option);
                }
            @endforeach
        });
    });
</script>
