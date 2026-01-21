<x-tenant-app-layout pageTitle="Editar Organismo" :breadcrumbs="[['label' => 'Configuração'], ['label' => 'Editar Organismo']]">

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <!--begin::Content container-->
        <div id="kt_app_content_container" class="app-container container-fluid py-3 py-lg-6">
            <!--begin::Navbar-->
            <div class="card card-flush mb-9" id="kt_user_profile_panel">
                <!--begin::Hero nav-->
                <div class="card-header rounded-top bgi-size-cover h-200px"
                    style="background-position: 100% 100%; background-image:url('/assets/media/misc/profile-head-bg1.jpg')">
                </div>
                <!--end::Hero nav-->
                <!--begin::Body-->
                <div class="card-body mt-n19">
                    <!--begin::Details-->
                    <div class="m-0">
                        <!--begin: Pic-->
                        <div class="d-flex flex-stack align-items-end pb-4 mt-n19">
                            <div class="symbol symbol-125px symbol-lg-150px symbol-fixed position-relative mt-n3">
                                <img src="{{ $company->avatar ? route('file', ['path' => $company->avatar]) : '/assets/media/avatars/apple-touch-icon.svg' }}"
                                    alt="image" class="border border-white border-4" style="border-radius: 20px" />
                                <div
                                    class="position-absolute translate-middle bottom-0 start-100 ms-n1 mb-9 bg-success rounded-circle h-15px w-15px">
                                </div>
                            </div>
                            <!--begin::Toolbar-->
                            <div class="me-0">
                                <button class="btn btn-icon btn-sm btn-active-color-primary justify-content-end pt-3"
                                    data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                    <i class="fonticon-settings fs-2"></i>
                                </button>
                                <!--begin::Menu 3-->
                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px py-3"
                                    data-kt-menu="true">
                                    <!--begin::Heading-->
                                    <div class="menu-item px-3">
                                        <div class="menu-content text-muted pb-2 px-3 fs-7 text-uppercase">
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
                                        <a href="#" class="menu-link flex-stack px-3">Create Payment
                                            <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                                                title="Specify a target name for future usage and reference"></i></a>
                                    </div>
                                    <!--end::Menu item-->
                                    <!--begin::Menu item-->
                                    <div class="menu-item px-3">
                                        <a href="#" class="menu-link px-3"
                                            onclick="openAppCodeModal(); return false;">
                                            Gerar ID para APP
                                            <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                                                title="Gera um código único para acesso via aplicativo mobile"></i>
                                        </a>
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
                                                <a href="#" class="menu-link px-3">Statements</a>
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
                                                        <input class="form-check-input w-30px h-20px" type="checkbox"
                                                            value="1" checked="checked" name="notifications" />
                                                        <!--end::Input-->
                                                        <!--end::Label-->
                                                        <span class="form-check-label text-muted fs-6">Recuring</span>
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
                            <!--end::Toolbar-->
                        </div>
                        <!--end::Pic-->
                        <!--begin::Info-->
                        <div class="d-flex flex-stack flex-wrap align-items-end">
                            <!--begin::User-->
                            <div class="d-flex flex-column">
                                <!--begin::Name-->
                                <div class="d-flex align-items-center mb-2">
                                    <a href="#"
                                        class="text-gray-800 text-hover-primary fs-2 fw-bolder me-1">{{ $company->name }}</a>
                                    <a href="#" class="" data-bs-toggle="tooltip" data-bs-placement="right"
                                        title="Account is verified">
                                        <!--begin::Svg Icon | path: icons/duotune/general/gen026.svg-->
                                        <i class="bi bi-check-circle-fill text-success fs-2"></i>
                                        <!--end::Svg Icon-->
                                    </a>
                                </div>
                                <!--end::Name-->
                                <!--begin::Text-->
                                @if ($company->addresses)
                                    <span
                                        class="fw-bold text-gray-600 fs-6 mb-2 d-block">{{ $company->addresses->rua }},
                                        {{ $company->addresses->numero }} - {{ $company->addresses->bairro }} -
                                        {{ $company->addresses->cidade }} -
                                        {{ $company->addresses->uf }}</span>
                                @else
                                    <span class="fw-bold text-gray-600 fs-6 mb-2 d-block">Endereço não
                                        informado</span>
                                @endif
                                <!--end::Text-->
                                <!--begin::Info-->
                                <div class="d-flex align-items-center flex-wrap fw-semibold fs-7 pe-2">
                                    <a href="#"
                                        class="d-flex align-items-center text-gray-400 text-hover-primary">CNPJ:
                                        {{ $company->cnpj }}</a>
                                    <span class="bullet bullet-dot h-5px w-5px bg-gray-400 mx-3"></span>
                                    <a href="#"
                                        class="d-flex align-items-center text-gray-400 text-hover-primary">Data de
                                        Fundação: {{ $company->data_fundacao }}</a>
                                    <span class="bullet bullet-dot h-5px w-5px bg-gray-400 mx-3"></span>
                                    <a href="#" class="text-gray-400 text-hover-primary">Data de CNPJ:
                                        {{ $company->data_cnpj }}</a>
                                </div>
                                <!--end::Info-->
                            </div>
                            <!--end::User-->
                        </div>
                        <!--end::Info-->
                    </div>
                    <!--end::Details-->
                </div>
            </div>
            <!--end::Navbar-->
            <!--begin::Nav items-->
            <div id="kt_user_profile_nav" class="rounded bg-gray-200 d-flex flex-stack flex-wrap mb-9 p-2"
                data-kt-page-scroll-position="400" data-kt-sticky="true" data-kt-sticky-name="sticky-profile-navs"
                data-kt-sticky-offset="{default: false, lg: '200px'}"
                data-kt-sticky-width="{target: '#kt_user_profile_panel'}" data-kt-sticky-left="auto"
                data-kt-sticky-top="70px" data-kt-sticky-animation="false" data-kt-sticky-zindex="95">
                <!--begin::Nav-->
                <ul class="nav flex-wrap border-transparent" role="tablist">
                    <!--begin::Nav item-->
                    <li class="nav-item my-1" role="presentation">
                        <a class="btn btn-sm btn-color-gray-600 bg-state-body btn-active-color-gray-800 fw-bolder fw-bold fs-6 fs-lg-base nav-link px-3 px-lg-4 mx-1 @if ($activeTab === 'detalhes') active @endif"
                            href="{{ route('company.edit', ['tab' => 'detalhes']) }}" role="tab">Detalhes</a>
                    </li>
                    <!--end::Nav item-->
                    <!--begin::Nav item-->
                    <li class="nav-item my-1" role="presentation">
                        <a class="btn btn-sm btn-color-gray-600 bg-state-body btn-active-color-gray-800 fw-bolder fw-bold fs-6 fs-lg-base nav-link px-3 px-lg-4 mx-1 @if ($activeTab === 'editar') active @endif"
                            href="{{ route('company.edit', ['tab' => 'editar']) }}" role="tab"
                            id="nav-tab-editar">Editar</a>
                    </li>
                    <!--end::Nav item-->
                    <!--begin::Nav item-->
                    <li class="nav-item my-1" role="presentation">
                        <a class="btn btn-sm btn-color-gray-600 bg-state-body btn-active-color-gray-800 fw-bolder fw-bold fs-6 fs-lg-base nav-link px-3 px-lg-4 mx-1 @if ($activeTab === 'horario-missas') active @endif"
                            href="{{ route('company.edit', ['tab' => 'horario-missas']) }}" role="tab">Horários
                            de Missa</a>
                    </li>
                    <!--end::Nav item-->
                </ul>
                <!--end::Nav-->
            </div>
            <!--end::Nav items-->

            <!--begin::Tab content-->
            <div class="tab-content" id="kt_user_profile_tab_content">
                <!--begin::Tab pane - Detalhes-->
                <div class="tab-pane fade @if ($activeTab === 'detalhes') show active @endif" id="kt_tab_detalhes"
                    role="tabpanel">
                    @include('app.company.partials.details')
                </div>
                <!--end::Tab pane-->

                <!--begin::Tab pane - Editar-->
                <div class="tab-pane fade @if ($activeTab === 'editar') show active @endif" id="kt_tab_editar"
                    role="tabpanel">
                    @include('app.company.partials.edit')
                </div>
                <!--end::Tab pane-->

                <!--begin::Tab pane - Horários de Missa-->
                <div class="tab-pane fade @if ($activeTab === 'horario-missas') show active @endif"
                    id="kt_tab_horario_missas" role="tabpanel">
                    @include('app.company.partials.horarios-missas')
                </div>
                <!--end::Tab pane-->

            </div>
            <!--end::Tab content-->
        </div>
        <!--end::Basic info-->



        <!--begin::Modal - App Access Code-->
        <div class="modal fade" id="kt_modal_app_code" tabindex="-1" aria-hidden="true">
            <!--begin::Modal dialog-->
            <div class="modal-dialog modal-dialog-centered mw-500px">
                <!--begin::Modal content-->
                <div class="modal-content">
                    <!--begin::Modal header-->
                    <div class="modal-header" id="kt_modal_app_code_header">
                        <!--begin::Modal title-->
                        <h2 class="fw-bold">Código de Acesso Mobile</h2>
                        <!--end::Modal title-->
                        <!--begin::Close-->
                        <div id="kt_modal_app_code_close" class="btn btn-icon btn-sm btn-active-icon-primary"
                            data-bs-dismiss="modal">
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
                    <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                        <!--begin::Form-->
                        <div class="text-center">
                            <p class="text-gray-600 mb-5">Use este código para acessar o aplicativo mobile:</p>
                            <div class="mb-5">
                                <span id="display_app_code"
                                    class="fs-2 fw-bold text-primary d-inline-block">---</span>
                            </div>
                            <p class="text-muted fs-7">Copie o código e compartilhe com os usuários do aplicativo</p>
                        </div>
                        <!--end::Form-->
                    </div>
                    <!--end::Modal body-->
                    <!--begin::Modal footer-->
                    <div class="modal-footer flex-center">
                        <!--begin::Button-->
                        <button type="button" id="btn_copy_code" class="btn btn-primary me-3">
                            <span class="indicator-label">
                                <!--begin::Svg Icon | path: icons/duotune/files/fil005.svg-->
                                <span class="svg-icon svg-icon-3 me-2">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path opacity="0.3"
                                            d="M19 22H5C4.4 22 4 21.6 4 21V3C4 2.4 4.4 2 5 2H14L20 8V21C20 21.6 19.6 22 19 22Z"
                                            fill="currentColor" />
                                        <path d="M15 8H20L14 2V7C14 7.6 14.4 8 15 8Z" fill="currentColor" />
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                                Copiar Código
                            </span>
                            <span class="indicator-progress">Copiando...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                        <!--end::Button-->
                        <!--begin::Button-->
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fechar</button>
                        <!--end::Button-->
                    </div>
                    <!--end::Modal footer-->
                </div>
                <!--end::Modal content-->
            </div>
            <!--end::Modal dialog-->
        </div>
        <!--end::Modal - App Access Code-->

        <script>
            // Função para redirecionar para a tab de edição
            document.addEventListener('DOMContentLoaded', function() {
                const btnEditarOrganismo = document.getElementById('btn-editar-organismo');
                const navTabEditar = document.getElementById('nav-tab-editar');

                if (btnEditarOrganismo) {
                    btnEditarOrganismo.addEventListener('click', function(e) {
                        e.preventDefault();

                        // Redireciona para a rota com tab=editar
                        window.location.href = '{{ route('company.edit', ['tab' => 'editar']) }}';
                    });
                }

                // Inicializar Flatpickr nos campos de data (mesmo do settings.blade.php)
                if (typeof $ !== 'undefined' && typeof $.fn.flatpickr !== 'undefined') {
                    // Init Datepicker --- For more info, please check Flatpickr's official documentation: https://flatpickr.js.org/
                    // Inicializa o Flatpickr no padrão Brasil
                    $("#DM_datepicker_1").flatpickr({
                        dateFormat: "d/m/Y", // Formato brasileiro de data
                        locale: "pt" // Define o idioma para português
                    });

                    $("#DM_datepicker_2").flatpickr({
                        dateFormat: "d/m/Y", // Formato brasileiro de data
                        locale: "pt" // Define o idioma para português
                    });
                } else {
                    console.warn('Flatpickr não está disponível. Usando Inputmask como fallback.');
                    // Fallback: Aplicar máscara de data nos campos data_cnpj e data_fundacao
                    const dataCnpjInput = document.getElementById('DM_datepicker_2');
                    const dataFundacaoInput = document.getElementById('DM_datepicker_1');

                    if (dataCnpjInput && typeof Inputmask !== 'undefined') {
                        Inputmask("99/99/9999", {
                            placeholder: "dd/mm/aaaa",
                            clearIncomplete: true
                        }).mask(dataCnpjInput);
                    }

                    if (dataFundacaoInput && typeof Inputmask !== 'undefined') {
                        Inputmask("99/99/9999", {
                            placeholder: "dd/mm/aaaa",
                            clearIncomplete: true
                        }).mask(dataFundacaoInput);
                    }
                }
            });
        </script>

</x-tenant-app-layout>

<!--begin::Vendors Javascript(used for this page only)-->
<script src="/assets/plugins/custom/formrepeater/formrepeater.bundle.js"></script>
<!--end::Vendors Javascript-->

<!--begin::Custom Javascript(used for this page only)-->
<script>
    // Debug: Verificar se scripts foram carregados
    document.addEventListener('DOMContentLoaded', function() {
        console.log('=== DEBUG HORÁRIOS DE MISSAS ===');
        console.log('jQuery carregado:', typeof $ !== 'undefined' && typeof jQuery !== 'undefined');
        console.log('jQuery.repeater carregado:', typeof $.fn.repeater !== 'undefined');
        console.log('KTUtil carregado:', typeof KTUtil !== 'undefined');
        console.log('tempusDominus carregado:', typeof tempusDominus !== 'undefined');
        console.log('Elemento #kt_horarios_missas_repeater existe:', $('#kt_horarios_missas_repeater').length);

        // Inicializar KTHorariosMissas se estivermos na tab de horários de missas
        @if ($activeTab === 'horario-missas')
            // A tab de horários de missas está ativa, inicializar o script
            setTimeout(() => {
                console.log('Tab Horários de Missas está ativa, inicializando...');
                if (typeof KTHorariosMissas !== 'undefined') {
                    console.log('KTHorariosMissas encontrado!');
                    // Verificar se o repeater foi inicializado
                    const repeaterCheck = $('#kt_horarios_missas_repeater').data('repeater');
                    console.log('Repeater já inicializado?', repeaterCheck !== undefined);
                } else {
                    console.error('KTHorariosMissas NÃO está definido!');
                }
            }, 100);
        @endif
    });
</script>
<script src="/assets/js/custom/apps/company/horarios-missas.js?v={{ time() }}"></script>
<!--end::Custom Javascript-->

<!--begin::Script para datepicker do formulário de edição-->
<script>
    "use strict";

    // Inicializar Flatpickr nos campos de data quando a tab de edição estiver ativa
    @if ($activeTab === 'editar')
        document.addEventListener('DOMContentLoaded', function() {
            // Aguarda um pouco para garantir que o jQuery e Flatpickr estejam carregados
            setTimeout(function() {
                if (typeof $ !== 'undefined' && typeof $.fn.flatpickr !== 'undefined') {
                    // Init Datepicker --- For more info, please check Flatpickr's official documentation: https://flatpickr.js.org/
                    // Inicializa o Flatpickr no padrão Brasil
                    $("#DM_datepicker_1").flatpickr({
                        dateFormat: "d/m/Y", // Formato brasileiro de data
                        locale: "pt" // Define o idioma para português
                    });

                    $("#DM_datepicker_2").flatpickr({
                        dateFormat: "d/m/Y", // Formato brasileiro de data
                        locale: "pt" // Define o idioma para português
                    });
                } else {
                    console.warn(
                    'Flatpickr não está disponível. Verifique se o plugin está carregado.');
                }
            }, 300);
        });
    @endif
</script>
<!--end::Script para datepicker do formulário de edição-->

<!--begin::Script para Modal de Código de Acesso Mobile-->
<script>
    let currentAppCode = '';

    /**
     * Abre o modal e gera o código de acesso mobile para o tenant atual
     */
    function openAppCodeModal() {
        // Abrir o modal
        const modalElement = document.getElementById('kt_modal_app_code');
        const modal = new bootstrap.Modal(modalElement);
        modal.show();

        // Mostrar loading
        const displayCode = document.getElementById('display_app_code');
        displayCode.textContent = 'Gerando...';
        displayCode.classList.remove('text-primary');
        displayCode.classList.add('text-muted');

        // Desabilitar botão de copiar
        const btnCopy = document.getElementById('btn_copy_code');
        btnCopy.disabled = true;
        btnCopy.querySelector('.indicator-label').classList.add('d-none');
        btnCopy.querySelector('.indicator-progress').classList.remove('d-none');

        // Fazer requisição AJAX para a rota do tenant atual
        fetch('{{ route('tenant.generate-app-code') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({})
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erro ao gerar código');
                }
                return response.json();
            })
            .then(data => {
                // Atualizar código no modal
                currentAppCode = data.code;
                displayCode.textContent = data.code;
                displayCode.classList.remove('text-muted');
                displayCode.classList.add('text-primary');

                // Habilitar botão de copiar
                btnCopy.disabled = false;
                btnCopy.querySelector('.indicator-label').classList.remove('d-none');
                btnCopy.querySelector('.indicator-progress').classList.add('d-none');
            })
            .catch(error => {
                console.error('Erro:', error);
                displayCode.textContent = 'Erro ao gerar código';
                displayCode.classList.remove('text-primary');
                displayCode.classList.add('text-danger');

                // Habilitar botão de copiar (mesmo com erro)
                btnCopy.disabled = false;
                btnCopy.querySelector('.indicator-label').classList.remove('d-none');
                btnCopy.querySelector('.indicator-progress').classList.add('d-none');
            });
    }

    // Configurar botão de copiar código
    document.addEventListener('DOMContentLoaded', function() {
        const btnCopy = document.getElementById('btn_copy_code');
        if (btnCopy) {
            btnCopy.addEventListener('click', function() {
                if (!currentAppCode) {
                    return;
                }

                // Tentar usar Clipboard API
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(currentAppCode).then(function() {
                        // Feedback visual
                        const originalText = btnCopy.querySelector('.indicator-label')
                        .innerHTML;
                        btnCopy.querySelector('.indicator-label').innerHTML =
                            '<i class="fas fa-check me-2"></i>Código Copiado!';
                        btnCopy.classList.remove('btn-primary');
                        btnCopy.classList.add('btn-success');

                        setTimeout(function() {
                            btnCopy.querySelector('.indicator-label').innerHTML =
                                originalText;
                            btnCopy.classList.remove('btn-success');
                            btnCopy.classList.add('btn-primary');
                        }, 2000);
                    }).catch(function(err) {
                        console.error('Erro ao copiar:', err);
                        fallbackCopyTextToClipboard(currentAppCode);
                    });
                } else {
                    // Fallback para navegadores mais antigos
                    fallbackCopyTextToClipboard(currentAppCode);
                }
            });
        }
    });

    /**
     * Fallback para copiar texto (navegadores antigos)
     */
    function fallbackCopyTextToClipboard(text) {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.top = '0';
        textArea.style.left = '0';
        textArea.style.position = 'fixed';
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();

        try {
            const successful = document.execCommand('copy');
            if (successful) {
                const btnCopy = document.getElementById('btn_copy_code');
                const originalText = btnCopy.querySelector('.indicator-label').innerHTML;
                btnCopy.querySelector('.indicator-label').innerHTML =
                '<i class="fas fa-check me-2"></i>Código Copiado!';
                btnCopy.classList.remove('btn-primary');
                btnCopy.classList.add('btn-success');

                setTimeout(function() {
                    btnCopy.querySelector('.indicator-label').innerHTML = originalText;
                    btnCopy.classList.remove('btn-success');
                    btnCopy.classList.add('btn-primary');
                }, 2000);
            } else {
                alert('Não foi possível copiar o código. Por favor, copie manualmente: ' + text);
            }
        } catch (err) {
            console.error('Erro ao copiar:', err);
            alert('Não foi possível copiar o código. Por favor, copie manualmente: ' + text);
        }

        document.body.removeChild(textArea);
    }
</script>
<!--end::Script para Modal de Código de Acesso Mobile-->
