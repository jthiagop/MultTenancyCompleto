<x-tenant-app-layout pageTitle="Domus IA - Use Inteligência Artificial para os Lançamentos" :breadcrumbs="[['label' => 'Financeiro', 'url' => route('caixa.index')], ['label' => 'Domus IA']]">

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid mt-5">
        <!--begin::Content container-->
        <div id="kt_app_content_container" class="app-container container-fluid">

            <!--begin:::Tabs-->
            <ul class="nav nav-custom nav-tabs nav-line-tabs nav-line-tabs-2x border-0 fs-4 fw-semibold mb-8">
                <!--begin:::Tab item-->
                <li class="nav-item">
                    <a class="nav-link text-active-primary pb-4 {{ ($activeTab ?? 'pendentes') === 'pendentes' ? 'active' : '' }}"
                       href="{{ route('domusia.index', 'pendentes') }}">
                        Pendentes
                    </a>
                </li>
                <!--end:::Tab item-->
                <!--begin:::Tab item-->
                <li class="nav-item">
                    <a class="nav-link text-active-primary pb-4 {{ ($activeTab ?? 'pendentes') === 'integracoes' ? 'active' : '' }}"
                       href="{{ route('domusia.index', 'integracoes') }}">
                        Integrações
                    </a>
                </li>
                <!--end:::Tab item-->
            </ul>
            <!--end:::Tabs-->
            <!--begin:::Tab content-->
            <div class="tab-content" id="myTabContent">
                @include('app.financeiro.domusia.partials.pendentes')
                @include('app.financeiro.domusia.partials.integracoes')
            </div>
            <!--end:::Tab content-->

        </div>
        <!--end::Content container-->
    </div>
    <!--end::Content-->

    <!--begin::Drawer - Configuração de Integração-->
    @include('app.components.drawers.whatsapp-integracao')

    {{--begin::Drawer - Lançamento de Despesa Domusia--}}
    @include('app.components.modals.financeiro.drawer_domusia_despesa')

    @push('scripts')
    <script>
        // Inicializar KTDrawer se disponível
        document.addEventListener('DOMContentLoaded', function() {
            // Função para inicializar KTDrawer quando disponível
            function initKTDrawer() {
                if (typeof KTDrawer !== 'undefined') {
                    try {
                        if (typeof KTDrawer.init === 'function') {
                            KTDrawer.init();
                        }
                    } catch (e) {
                        // Silenciosamente ignora erros do KTDrawer
                    }
                } else {
                    // Tentar novamente após um pequeno delay se ainda não estiver disponível
                    setTimeout(initKTDrawer, 100);
                }
            }

            // Iniciar verificação
            initKTDrawer();
        });

        // Excluir integração
        document.addEventListener('click', function(e) {
            if (e.target.closest('.excluir-integracao-btn')) {
                const btn = e.target.closest('.excluir-integracao-btn');
                const integracaoId = btn.getAttribute('data-integracao-id');
                const integracaoTipo = btn.getAttribute('data-integracao-tipo');

                if (confirm(`Tem certeza que deseja excluir a integração ${integracaoTipo.toUpperCase()}?`)) {
                    fetch(`{{ route('integracoes.excluir', '') }}/${integracaoId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(async response => {
                        // Ler JSON apenas uma vez
                        const data = await response.json();
                        return { ok: response.ok, status: response.status, data: data };
                    })
                    .then(result => {
                        if (result.ok && result.data.success) {
                            // Recarregar página para atualizar lista
                            window.location.reload();
                        } else {
                            alert('Erro ao excluir integração: ' + (result.data.error || 'Erro desconhecido'));
                        }
                    })
                    .catch(error => {
                        console.error('Erro:', error);
                        alert('Erro ao excluir integração');
                    });
                }
            }
        });
    </script>
    @endpush

</x-tenant-app-layout>
