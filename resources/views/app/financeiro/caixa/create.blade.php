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
                <div id="" class="app-container container-xxl d-flex flex-stack">
                    <!--begin::Page title-->
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <!--begin::Title-->
                        <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                            Lançamento de Caixa</h1>
                        <!--end::Title-->
                        <!--begin::Breadcrumb-->
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Ínicio</a>
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
                            <!--begin::Item-->
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-400 w-5px h-2px"></span>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">Lançamento Caixa</li>
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
                    <form method="POST" action="{{ route('caixa.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
                            <div class="card card-flush py-4">
                                <div class="card-header">
                                    <div id="" class="app-container container-xxl d-flex flex-stack">
                                        <!--begin::Page title-->
                                        <div
                                            class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                                            <!--begin::Title-->
                                            <div class="card-title">
                                                <span class="svg-icon svg-icon-3x me-5">
                                                    <svg version="1.1" id="_x34_" width="35" height="35"
                                                        id="_x34_" xmlns="http://www.w3.org/2000/svg"
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
                                                <h2>Informações do lançamento</h2>
                                            </div>
                                            <!--end::Title-->
                                        </div>
                                        <!--end::Page title-->
                                        <!--begin::Actions-->
                                        <div class="d-flex align-items-center gap-2 gap-lg-3">
                                            <!--begin::Stat-->
                                            <div class="rounded min-w-125px py-3 px-4 my-1 me-6"
                                                style="border: 1px dashed rgba(65, 124, 53, 0.979)">
                                                <!--begin::Number-->
                                                <div class="d-flex align-items-center">
                                                    <div class="fs-4 fw-bold" data-kt-countup="true"
                                                        data-kt-countup-value="{{ $total }}"
                                                        data-kt-countup-prefix="R$">
                                                        0</div>
                                                </div>
                                                <!--end::Number-->
                                                <!--begin::Label-->
                                                <div class="fw-semibold fs-6 text-text-success  opacity-50">Saldo atual
                                                </div>
                                                <!--end::Label-->
                                            </div>
                                            <!--end::Stat-->
                                        </div>
                                        <!--end::Actions-->
                                    </div>
                                </div>
                                <div class="modal-body py-10 px-lg-17">
                                    @foreach ($errors->all() as $error)
                                        <div class="alert alert-danger mt-2">
                                            {{ $error }}
                                        </div>
                                    @endforeach
                                    <div class="scroll-y me-n7 pe-7" id="kt_td_picker_simple" data-kt-scroll="true"
                                        data-kt-scroll-activate="{default: false, lg: true}"
                                        data-kt-scroll-max-height="auto"
                                        data-kt-scroll-dependencies="#kt_td_picker_simple"
                                        data-kt-scroll-wrappers="#kt_td_picker_simple" data-kt-scroll-offset="300px">
                                        <div class="row mb-5">
                                            <div class="col-md-2 fv-row">
                                                <label class="required fs-5 fw-semibold mb-2">Data de
                                                    Competência</label>
                                                <div class="input-group" id="kt_td_picker_date_only"
                                                    data-td-target-input="nearest" data-td-target-toggle="nearest">
                                                    <input class="form-control" name="data_competencia"
                                                        type="date" placeholder="Pick a date"
                                                        id="kt_calendar_datepicker_start_date"
                                                        value="{{ old('data_competencia', now()->format('Y-m-d')) }}" />
                                                    <span class="input-group-text"
                                                        data-td-target="#kt_td_picker_date_only"
                                                        data-td-toggle="datetimepicker">
                                                        <i class="ki-duotone ki-calendar fs-2"><span
                                                                class="path1"></span><span class="path2"></span></i>
                                                    </span>
                                                </div>
                                                @error('data_competencia')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 fv-row">
                                                <label class="required fs-5 fw-semibold mb-2">Descrição</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" placeholder=""
                                                        name="descricao" value="{{ old('descricao') }}" />
                                                </div>
                                                @error('descricao')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-4 fv-row">
                                                <label class="required fs-5 fw-semibold mb-2">Valor</label>
                                                <div class="input-group">
                                                    <span class="input-group-text" id="basic-addon1">R$</span>
                                                    <input class="form-control money" placeholder="Valor"
                                                        aria-label="Valor" aria-describedby="basic-addon1"
                                                        id="valor" name="valor" required
                                                        value="{{ old('valor') }}" />
                                                </div>
                                                @error('valor')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row mb-5">
                                            <div class="col-md-2 fv-row">
                                                <label class="d-flex align-items-center fs-5 fw-semibold mb-2">
                                                    <span class="required">Entrada/Saída</span>
                                                    <i class="fas fa-exclamation-circle ms-2 fs-7"
                                                        data-bs-toggle="tooltip"
                                                        title="As categorias são utilizadas para formar um Plano de Contas. Muitas destas categorias são demonstradas em Relatórios e também alimentam o DRE Gerencial."></i>
                                                </label>
                                                <select class="form-select" data-control="select"
                                                    data-dropdown-css-class="w-200px"
                                                    data-placeholder="Selecione o tipo" name="tipo" required
                                                    data-hide-search="true">
                                                    <option value="entrada"
                                                        {{ old('tipo') == 'entrada' ? 'selected' : '' }}>Entrada
                                                    </option>
                                                    <option value="saida"
                                                        {{ old('tipo') == 'saida' ? 'selected' : '' }}>Saída</option>
                                                </select>
                                                @error('tipo')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 fv-row">
                                                <label class="required fs-5 fw-semibold mb-2">Lançamento Padrão</label>
                                                <div class="input-group">
                                                    <select name="lancamento_padrao" aria-label="Select a Country"
                                                        data-control="select2"
                                                        data-placeholder="Escolha um Lançamento..."
                                                        class="form-select  fw-bold" id="lancamento_padrao">
                                                        <option value=""></option>
                                                        @foreach ($lps as $lp)
                                                            <option value="{{ $lp->description }}"
                                                                data-type="{{ $lp->type }}">
                                                                {{ $lp->description }} </option>
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
                                                        class="form-control" placeholder=""
                                                        value="{{ $company->first()->companies_name }}" />
                                                </div>
                                                @error('centro')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row mb-5">
                                            <div class="col-md-4 fv-row">
                                                <label class="d-flex align-items-center fs-5 fw-semibold mb-2">
                                                    <span class="required">Tipo de Documento</span>
                                                    <i class="fas fa-exclamation-circle ms-2 fs-7"
                                                        data-bs-toggle="tooltip"
                                                        title="As categorias são utilizadas para formar um Plano de Contas. Muitas destas categorias são demonstradas em Relatórios e também alimentam o DRE Gerencial."></i>
                                                </label>
                                                <select class="form-control" name="tipo_documento"
                                                    id="tipo_documento">
                                                    <option value="Pix"
                                                        {{ old('tipo_documento') == 'Pix' ? 'selected' : '' }}>Pix
                                                    </option>
                                                    <option value="OUTR - Dafe"
                                                        {{ old('tipo_documento') == 'OUTR - Dafe' ? 'selected' : '' }}>
                                                        OUTR - Dafe</option>
                                                    <option value="NF - Nota Fiscal"
                                                        {{ old('tipo_documento') == 'NF - Nota Fiscal' ? 'selected' : '' }}>
                                                        NF - Nota Fiscal</option>
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
                                                @error('tipo_documento')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 fv-row">
                                                <label class="fs-5 fw-semibold mb-2">Número do Documento</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" placeholder=""
                                                        name="numero_documento"
                                                        value="{{ old('numero_documento') }}" />
                                                </div>
                                                @error('numero_documento')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
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
                                                    <textarea class="form-control" name="historico_complementar" id="complemento" cols="20" rows="3">{{ old('historico_complementar') }}</textarea>
                                                    <p class="mensagem-vermelha">Descreva observações relevantes sobre
                                                        esse lançamento financeiro</p>
                                                    @error('historico_complementar')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
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
                                                                allowedExtensions: ["jpg", "jpeg", "png", "pdf"], // Extensões permitidas
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
                                </div>
                            </div>
                            <!--begin::Modal footer-->

                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('caixa.index') }}" id="kt_ecommerce_add_product_cancel"
                                        class="btn btn-secondary me-2 mb-2">Voltar</a>
                                    <a href="{{ route('caixa.list') }}" class="btn btn-warning me-2 mb-2">
                                        <i class="bi bi-search fs-1"></i>
                                        Pesquisar
                                    </a>
                                    <button type="submit" class="btn btn-primary me-2 mb-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor" class="bi bi-floppy2 fs-1" viewBox="0 0 16 16">
                                            <path
                                                d="M1.5 0h11.586a1.5 1.5 0 0 1 1.06.44l1.415 1.414A1.5 1.5 0 0 1 16 2.914V14.5a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 14.5v-13A1.5 1.5 0 0 1 1.5 0M1 1.5v13a.5.5 0 0 0 .5.5H2v-4.5A1.5 1.5 0 0 1 3.5 9h9a1.5 1.5 0 0 1 1.5 1.5V15h.5a.5.5 0 0 0 .5-.5V2.914a.5.5 0 0 0-.146-.353l-1.415-1.415A.5.5 0 0 0 13.086 1H13v3.5A1.5 1.5 0 0 1 11.5 6h-7A1.5 1.5 0 0 1 3 4.5V1H1.5a.5.5 0 0 0-.5.5m9.5-.5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5z" />
                                        </svg>
                                        <span class="indicator-label">Lançar</span>
                                    </button>
                                </div>
                            </div>
                            <!--end::Modal footer-->
                        </div>
                    </form>
                    <!-- Formulário oculto para exclusão -->

                </div>
                <!--end::Content container-->
            </div>
            <!--end::Content-->
        </div>
        <!--end::Content wrapper-->
    </div>
    <!--end:::Main-->

    <!--begin::Vendors Javascript(used for this page only)-->
    <script src="assets/plugins/custom/datatables/datatables.bundle.js"></script>
    <!--end::Vendors Javascript-->
    <!--begin::Custom Javascript(used for this page only)-->
    <script src="assets/js/custom/pages/user-profile/general.js"></script>
    <script src="assets/js/custom/account/settings/signin-methods.js"></script>
    <script src="assets/js/custom/account/security/security-summary.js"></script>
    <script src="assets/js/custom/account/security/license-usage.js"></script>
    <script src="assets/js/custom/account/settings/deactivate-account.js"></script>
    <script src="assets/js/widgets.bundle.js"></script>
    <script src="assets/js/custom/apps/chat/chat.js"></script>
    <script src="assets/js/custom/utilities/modals/upgrade-plan.js"></script>
    <script src="assets/js/custom/utilities/modals/create-campaign.js"></script>
    <script src="assets/js/custom/utilities/modals/users-search.js"></script>
    <!--end::Custom Javascript-->

    <!--begin::Custom Javascript(used for this page only)-->
    <script src="assets/js/custom/apps/user-management/users/list/table.js"></script>
    <script src="assets/js/custom/apps/user-management/users/list/export-users.js"></script>
    <script src="assets/js/custom/apps/user-management/users/list/add.js"></script>
    <!--end::Custom Javascript-->
    <!--end::Javascript-->

</x-tenant-app-layout>

<script>
    $(document).ready(function() {
        $('#lancamento_padrao').select2({
            templateResult: formatOption,
            templateSelection: formatOption,
            escapeMarkup: function(markup) {
                return markup;
            }
        });
    });

    function formatOption(option) {
        if (!option.id) {
            return option.text;
        }

        var type = $(option.element).data('type');
        var badge = '';

        if (type === 'entrada') {
            badge = '<span class="badge badge-light-success fw-bold fs-8 opacity-75 ps-3 ">Entrada</span>';
        } else if (type === 'saida') {
            badge = '<span class="badge badge-light-danger fw-bold fs-8 opacity-75 ps-3">Saída</span>';
        }

        return badge + ' ' + option.text;
    }
</script>
