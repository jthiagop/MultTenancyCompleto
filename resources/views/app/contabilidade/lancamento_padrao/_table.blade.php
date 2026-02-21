{{-- Em resources/views/app/contabilidade/lancamento_padrao/_table.blade.php --}}

@php
    $tableId = 'kt_lancamento_padrao_table';

    // Tabs de filtro por tipo
    $tabs = [
        ['key' => 'todos', 'label' => 'Todos', 'count' => 0, 'paneId' => "pane-lp-todos-{$tableId}"],
        ['key' => 'entrada', 'label' => 'Receitas (Entradas)', 'count' => 0, 'paneId' => "pane-lp-entrada-{$tableId}"],
        ['key' => 'saida', 'label' => 'Despesas (Saídas)', 'count' => 0, 'paneId' => "pane-lp-saida-{$tableId}"],
    ];
@endphp

<!--begin::Segmented Tabs Wrapper-->
<div id="lp_segmented_wrapper" data-stats-url="{{ route('lancamentoPadrao.stats') }}"
    data-data-url="{{ route('lancamentoPadrao.data') }}">

    <x-tenant.segmented-tabs-toolbar :tabs="$tabs" active="todos" id="status-tabs-{{ $tableId }}"
        :tableId="$tableId" :filterId="$tableId" :showAccountFilter="false" :showMoreFilters="false">

        <x-slot:actionsRight>
            <!--begin::Toolbar-->
            <div class="d-flex justify-content-end" data-kt-lancamento-padrao-table-toolbar="base">
                    <!--begin::Export-->
                    <button type="button" class="btn btn-sm btn-light-primary me-3" data-bs-toggle="modal" 
                        data-bs-target="#kt_modal_export_lancamento_padrao">
                        <!--begin::Svg Icon | path: icons/duotune/arrows/arr078.svg-->
                        <span class="svg-icon svg-icon-2">
                            <i class="bi bi-box-arrow-up fs-3"></i>
                        </span>
                        <!--end::Svg Icon-->Exportar</button>
                    <!--end::Export-->
                <!--begin::Import Bulk-->
                <button type="button" class="btn btn-sm btn-light-primary me-3" data-bs-toggle="modal"
                    data-bs-target="#kt_modal_import_bulk">
                    <span class="svg-icon svg-icon-2">
                        <i class="bi bi-cloud-arrow-up fs-3"></i>
                    </span>
                    Adicionar em Massa
                </button>
                <!--end::Import Bulk-->
            </div>
            <!--end::Toolbar-->
        </x-slot>

        <x-slot:panes>
            @foreach ($tabs as $tab)
                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="{{ $tab['paneId'] }}"
                    role="tabpanel">
                </div>
            @endforeach
        </x-slot:panes>

        <x-slot:tableContent>
            <div class="card card-flush">
                <!--begin::Card body-->
                <div class="card-body card-flush">
                    <!--begin::Table-->
                    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_lancamento_padrao_table">
                        <!--begin::Table head-->
                        <thead>
                            <!--begin::Table row-->
                            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                <th class="w-10px pe-2">
                                    <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                        <input class="form-check-input" type="checkbox" data-kt-check="true"
                                            data-kt-check-target="#kt_lancamento_padrao_table .form-check-input"
                                            value="1" />
                                    </div>
                                </th>
                                <th class="min-w-200px">Descrição</th>
                                <th class="min-w-100px">Tipo</th>
                                <th class="min-w-150px">Categoria</th>
                                <th class="min-w-200px">Conta Débito</th>
                                <th class="min-w-200px">Conta Crédito</th>
                                <th class="text-end min-w-70px">Ações</th>
                            </tr>
                            <!--end::Table row-->
                        </thead>
                        <!--end::Table head-->
                        <!--begin::Table body-->
                        <tbody class="text-gray-600 fw-semibold">
                            <!-- Dados serão carregados via AJAX -->
                        </tbody>
                        <!--end::Table body-->
                    </table>
                    <!--end::Table-->
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Card-->
            <!--begin::Modals-->
            <!--begin::Modal - Export Lancamento Padrao-->
            <div class="modal fade" tabindex="-1" id="kt_modal_export_lancamento_padrao">
                <!--begin::Modal dialog-->
                <div class="modal-dialog modal-dialog-centered mw-650px">
                    <!--begin::Modal content-->
                    <div class="modal-content">
                        <!--begin::Modal header-->
                        <div class="modal-header">
                            <!--begin::Modal title-->
                            <h3 class="modal-title">Exportar Lançamentos Padrão</h3>
                            <!--end::Modal title-->
                            <!--begin::Close-->
                            <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Fechar">
                                <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                            </div>
                            <!--end::Close-->
                        </div>
                        <!--end::Modal header-->
                        <!--begin::Modal body-->
                        <div class="modal-body">
                            <!--begin::Form-->
                            <form id="kt_lancamento_padrao_export_form" class="form" action="#">
                                <!--begin::Input group-->
                                <div class="fv-row mb-10">
                                    <!--begin::Label-->
                                    <label class="fs-5 fw-semibold form-label mb-5">Formato de Exportação:</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <select data-control="select2" data-placeholder="Selecione um formato"
                                        data-hide-search="true" name="format" class="form-select">
                                        <option value="excel">Excel (.xlsx)</option>
                                        <option value="csv">CSV</option>
                                        <option value="pdf">PDF</option>
                                    </select>
                                    <!--end::Input-->
                                </div>
                                <!--end::Input group-->
                                <!--begin::Input group-->
                                <div class="fv-row mb-10">
                                    <!--begin::Label-->
                                    <label class="fs-5 fw-semibold form-label mb-5">Filtrar por Tipo:</label>
                                    <!--end::Label-->
                                    <!--begin::Radio group-->
                                    <div class="d-flex flex-column">
                                        <!--begin::Radio button-->
                                        <label class="form-check form-check-custom form-check-sm mb-3">
                                            <input class="form-check-input" type="radio" value="todos" 
                                                checked="checked" name="tipo_lancamento" />
                                            <span class="form-check-label text-gray-600 fw-semibold">Todos</span>
                                        </label>
                                        <!--end::Radio button-->
                                        <!--begin::Radio button-->
                                        <label class="form-check form-check-custom form-check-sm mb-3">
                                            <input class="form-check-input" type="radio" value="entrada" 
                                                name="tipo_lancamento" />
                                            <span class="form-check-label text-gray-600 fw-semibold">Receitas (Entradas)</span>
                                        </label>
                                        <!--end::Radio button-->
                                        <!--begin::Radio button-->
                                        <label class="form-check form-check-custom form-check-sm">
                                            <input class="form-check-input" type="radio" value="saida" 
                                                name="tipo_lancamento" />
                                            <span class="form-check-label text-gray-600 fw-semibold">Despesas (Saídas)</span>
                                        </label>
                                        <!--end::Radio button-->
                                    </div>
                                    <!--end::Radio group-->
                                </div>
                                <!--end::Input group-->
                            </form>
                            <!--end::Form-->
                        </div>
                        <!--end::Modal body-->
                        
                        <!--begin::Modal footer-->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-primary" onclick="exportarLancamentosPadrao()">
                                <span class="indicator-label">Exportar</span>
                                <span class="indicator-progress d-none">Exportando... 
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>
                        </div>
                        <!--end::Modal footer-->

                    </div>
                    <!--end::Modal content-->
                </div>
                <!--end::Modal dialog-->
            </div>
            <!--end::Modal - New Card-->

            <!--begin::Modal - Import Bulk-->
            <div class="modal fade" id="kt_modal_import_bulk" tabindex="-1" aria-hidden="true">
                <!--begin::Modal dialog-->
                <div class="modal-dialog modal-dialog-centered mw-800px">
                    <!--begin::Modal content-->
                    <div class="modal-content">
                        <!--begin::Modal header-->
                        <div class="modal-header">
                            <!--begin::Modal title-->
                            <h2 class="fw-bold">Adicionar em Massa</h2>
                            <!--end::Modal title-->
                            <!--begin::Close-->
                            <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                                <span class="svg-icon svg-icon-1">
                                    <i class="bi bi-x-lg fs-3"></i>
                                </span>
                            </div>
                            <!--end::Close-->
                        </div>
                        <!--end::Modal header-->
                        <!--begin::Modal body-->
                        <div class="modal-body">
                            <!--begin::Nav tabs-->
                            <ul class="nav nav-tabs nav-line-tabs mb-5 fs-6 border-bottom-0" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active text-danger fw-bold" data-bs-toggle="tab"
                                        href="#kt_tab_download_model" role="tab">
                                        Baixar modelo
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link text-danger fw-bold" data-bs-toggle="tab"
                                        href="#kt_tab_upload_file" role="tab">
                                        Enviar arquivo
                                    </a>
                                </li>
                            </ul>
                            <!--end::Nav tabs-->

                            <!--begin::Tab content-->
                            <div class="tab-content" id="myTabContent">
                                <!--begin::Tab pane - Download Model-->
                                <div class="tab-pane fade show active " id="kt_tab_download_model" role="tabpanel">
                                    <!--begin::Content-->
                                    <div class="modal-body">
                                        <div class="mb-5">
                                            <h5 class="fw-bold mb-3">Formulário Básico</h5>
                                            <p class="text-gray-600 fs-6">O formulário básico contém os campos
                                                obrigatórios para
                                                anunciar seu produto. O formulário pode ser usado para qualquer
                                                categoria.</p>
                                        </div>
                                    </div>
                                    <!--end::Content-->
                                    <!--begin::Actions-->
                                    <div class="d-flex modal-footer">
                                        <button type="button" class="btn btn-danger fw-bold" onclick="baixarTemplateLancamentoPadrao()">
                                            <span class="indicator-label">Baixar</span>
                                            <span class="indicator-progress d-none">Baixando... 
                                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                            </span>
                                        </button>
                                    </div>
                                    <!--end::Actions-->
                                </div>
                                <!--end::Tab pane-->

                                <!--begin::Tab pane - Upload File-->
                                <div class="tab-pane fade" id="kt_tab_upload_file" role="tabpanel">
                                    <!--begin::Content-->
                                    <div class="py-5">
                                        <p class="text-gray-600 fs-6 mb-8">Envie o modelo completo e você pode
                                            verificar os novos
                                            produtos criados na aba Não Publicados quando o envio for completado.</p>

                                        <!--begin::Upload area-->
                                        <div class="border border-dashed border-gray-300 rounded text-center p-10 mb-10"
                                            style="background-color: #f9f9f9;">
                                            <!--begin::Icon-->
                                            <div class="mb-5">
                                                <i class="bi bi-cloud-arrow-up fs-3x text-gray-400"></i>
                                            </div>
                                            <!--end::Icon-->

                                            <!--begin::Info-->
                                            <div class="mb-5">
                                                <p class="text-gray-700 fw-semibold fs-6 mb-1">Selecione o arquivo ou
                                                    insira seus
                                                    arquivos do Excel aqui</p>
                                                <p class="text-gray-500 fs-7">Tamanho máx.: 10.0 MB apenas xlsx</p>
                                            </div>
                                            <!--end::Info-->

                                            <!--begin::Button-->
                                            <button type="button" class="btn btn-danger fw-bold">Selecionar
                                                arquivo</button>
                                            <input type="file" id="kt_import_file_input" class="d-none"
                                                accept=".xlsx,.xls">
                                            <!--end::Button-->
                                        </div>
                                        <!--end::Upload area-->
                                        <!--begin::Table-->
                                        <div class="table-responsive">
                                            <table class="table table-row-bordered align-middle gy-4">
                                                <thead>
                                                    <tr class="fw-bold text-gray-700 bg-light">
                                                        <th class="ps-4">Data</th>
                                                        <th>Nome do arquivo</th>
                                                        <th>Produtos</th>
                                                        <th>Status</th>
                                                        <th class="text-end pe-4">Ação</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="kt_import_records_tbody">
                                                    <tr>
                                                        <td colspan="5" class="text-center py-10">
                                                            <!--begin::Empty state-->
                                                            <div class="d-flex flex-column align-items-center">
                                                                <i
                                                                    class="bi bi-file-earmark-text fs-3x text-gray-400 mb-3"></i>
                                                                <span class="text-gray-500 fs-6">Ainda não há histórico
                                                                    de upload</span>
                                                            </div>
                                                            <!--end::Empty state-->
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <!--end::Table-->
                                    </div>
                                    <!--end::Content-->
                                </div>
                                <!--end::Tab pane-->
                            </div>
                            <!--end::Tab content-->
                        </div>
                        <!--end::Modal body-->
                    </div>
                    <!--end::Modal content-->
                </div>
                <!--end::Modal dialog-->
            </div>
            <!--end::Modal - Import Bulk-->

        </x-slot:tableContent>
    </x-tenant.segmented-tabs-toolbar>
</div>
<!--end::Segmented Tabs Wrapper-->

<script src="/tenancy/assets/js/custom/apps/contabilidade/lancamento-padrao/list.js"></script>
<script>
// Função para exportar lançamentos padrão com download real
function exportarLancamentosPadrao() {
    const btn = document.querySelector('#kt_modal_export_lancamento_padrao .btn-primary');
    const form = document.getElementById('kt_lancamento_padrao_export_form');
    
    if (!form) {
        alert('Formulário não encontrado');
        return;
    }
    
    // Mostra indicador de progresso
    const indicatorLabel = btn.querySelector('.indicator-label');
    const indicatorProgress = btn.querySelector('.indicator-progress');
    
    indicatorLabel.classList.add('d-none');
    indicatorProgress.classList.remove('d-none');
    btn.disabled = true;
    
    // Coleta dados do formulário
    const formData = new FormData(form);
    const format = formData.get('format');
    const tipoLancamento = formData.get('tipo_lancamento');
    
    console.log('Iniciando exportação real:', { format, tipoLancamento });
    
    try {
        // Cria URL com parâmetros para exportação
        const params = new URLSearchParams({
            format: format,
            tipo: tipoLancamento,
            _token: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        });
        
        // URL da rota de exportação (ajuste conforme necessário)
        const exportUrl = `{{ route('lancamentoPadrao.export') }}?${params.toString()}`;
        
        // Método 1: Tentar download direto via fetch
        fetch(exportUrl, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/octet-stream'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro na exportação: ' + response.statusText);
            }
            return response.blob();
        })
        .then(blob => {
            // Cria link para download
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            
            // Define nome do arquivo baseado no formato
            const timestamp = new Date().toISOString().split('T')[0];
            const extension = format === 'excel' ? 'xlsx' : format;
            link.download = `lancamentos-padrao-${tipoLancamento}-${timestamp}.${extension}`;
            
            // Adiciona ao DOM, clica e remove
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            // Libera a URL do blob
            window.URL.revokeObjectURL(url);
            
            console.log('Download concluído com sucesso');
        })
        .catch(error => {
            console.error('Erro no download:', error);
            
            // Fallback: abre em nova aba se fetch falhar
            console.log('Tentando download via abertura de nova aba...');
            window.open(exportUrl, '_blank');
        })
        .finally(() => {
            // Remove indicador de progresso
            indicatorLabel.classList.remove('d-none');
            indicatorProgress.classList.add('d-none');
            btn.disabled = false;
            
            // Fecha o modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('kt_modal_export_lancamento_padrao'));
            modal.hide();
        });
        
    } catch (error) {
        console.error('Erro geral na exportação:', error);
        alert('Erro ao iniciar a exportação. Tente novamente.');
        
        // Remove indicador de progresso em caso de erro
        indicatorLabel.classList.remove('d-none');
        indicatorProgress.classList.add('d-none');
        btn.disabled = false;
    }
}

// Função para baixar template de importação em massa
function baixarTemplateLancamentoPadrao() {
    const btn = event.target.closest('button');
    
    if (!btn) return;
    
    // Mostra indicador de progresso
    const indicatorLabel = btn.querySelector('.indicator-label');
    const indicatorProgress = btn.querySelector('.indicator-progress');
    
    indicatorLabel.classList.add('d-none');
    indicatorProgress.classList.remove('d-none');
    btn.disabled = true;
    
    try {
        // URL da rota de download do template
        const templateUrl = '{{ route('lancamentoPadrao.download-template') }}';
        
        console.log('Iniciando download do template...');
        
        // Método 1: Tentar download direto via fetch
        fetch(templateUrl, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/octet-stream'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro no download: ' + response.statusText);
            }
            return response.blob();
        })
        .then(blob => {
            // Cria link para download
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = `template_lancamentos_padrao_${new Date().toISOString().split('T')[0]}.xlsx`;
            
            // Adiciona ao DOM, clica e remove
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            // Libera a URL do blob
            window.URL.revokeObjectURL(url);
            
            console.log('Download do template concluído com sucesso');
        })
        .catch(error => {
            console.error('Erro no download do template:', error);
            
            // Fallback: abre em nova aba se fetch falhar
            console.log('Tentando download direto via abertura de nova aba...');
            window.open(templateUrl, '_blank');
        })
        .finally(() => {
            // Remove indicador de progresso
            indicatorLabel.classList.remove('d-none');
            indicatorProgress.classList.add('d-none');
            btn.disabled = false;
            
            console.log('Processo de download finalizado');
        });
        
    } catch (error) {
        console.error('Erro geral no download do template:', error);
        alert('Erro ao baixar o template. Tente novamente.');
        
        // Remove indicador de progresso em caso de erro
        indicatorLabel.classList.remove('d-none');
        indicatorProgress.classList.add('d-none');
        btn.disabled = false;
    }
}
</script>
