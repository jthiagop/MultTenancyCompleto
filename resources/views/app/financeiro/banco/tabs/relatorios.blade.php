<!--begin::Row-->
<div class="row g-5 g-xl-10 mb-xl-10">
    <!--begin::Col-->
    <div class="col-xl-12 mb-5 mb-xl-6">
        <!--begin::Card-->
        <div class="card card-flush">
            <!--begin::Card header-->
            <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                <div class="card-title">
                    <h3 class="fw-bold">Gerar Relatório de Transações Bancárias</h3>
                    <span class="text-gray-500 fs-6">Selecione os filtros para gerar o relatório em PDF</span>
                </div>
            </div>
            <!--end::Card header-->
            <!--begin::Card body-->
            <div class="card-body pt-0">
                <form id="kt_form_relatorio" action="{{ route('banco.relatorio.gerar') }}" method="POST">
                    @csrf
                    <div class="row g-5">
                        <!--begin::Col - Data Inicial-->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Data Inicial <span
                                    class="text-danger">*</span></label>
                            <input type="date" class="form-control form-control-solid" name="data_inicial"
                                id="data_inicial"
                                value="{{ old('data_inicial', now()->startOfMonth()->format('Y-m-d')) }}" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <!--end::Col-->

                        <!--begin::Col - Data Final-->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Data Final <span class="text-danger">*</span></label>
                            <input type="date" class="form-control form-control-solid" name="data_final"
                                id="data_final" value="{{ old('data_final', now()->format('Y-m-d')) }}" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <!--end::Col-->

                        <!--begin::Col - Entidade Financeira-->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Entidade Financeira <span
                                    class="text-danger">*</span></label>
                            <select class="form-select form-select-solid" name="entidade_id[]" id="entidade_id"
                                data-control="select2" data-placeholder="Selecione as entidades financeiras ou 'Todos'"
                                data-allow-clear="true" multiple="multiple" required>
                                <option value="todos">Todos</option>
                                @foreach ($entidadesRelatorio ?? [] as $entidade)
                                    <option value="{{ $entidade->id }}"
                                        {{ (is_array(old('entidade_id')) && in_array($entidade->id, old('entidade_id'))) ? 'selected' : '' }}>
                                        {{ $entidade->nome }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <!--end::Col-->

                        <!--begin::Col - Centro de Custo-->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Centro de Custo</label>
                            <select class="form-select form-select-solid" name="cost_center_id" id="cost_center_id"
                                data-control="select2" data-placeholder="Selecione um centro de custo (opcional)">
                                <option value="">Todos os centros de custo</option>
                                @foreach ($centrosAtivos ?? [] as $centro)
                                    <option value="{{ $centro->id }}"
                                        {{ old('cost_center_id') == $centro->id ? 'selected' : '' }}>
                                        {{ $centro->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <!--end::Col-->

                        <!--begin::Col - Tipo de Transação-->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tipo de Transação</label>
                            <select class="form-select form-select-solid" name="tipo" id="tipo">
                                <option value="ambos" {{ old('tipo', 'ambos') === 'ambos' ? 'selected' : '' }}>Ambos
                                </option>
                                <option value="entrada" {{ old('tipo') === 'entrada' ? 'selected' : '' }}>Entrada
                                </option>
                                <option value="saida" {{ old('tipo') === 'saida' ? 'selected' : '' }}>Saída</option>
                            </select>
                        </div>
                        <!--end::Col-->

                        <!--begin::Col - Orientação do PDF-->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Orientação do PDF</label>
                            <select class="form-select form-select-solid" name="orientacao" id="orientacao">
                                <option value="horizontal"
                                    {{ old('orientacao', 'horizontal') === 'horizontal' ? 'selected' : '' }}>Horizontal
                                </option>
                                <option value="vertical" {{ old('orientacao') === 'vertical' ? 'selected' : '' }}>
                                    Vertical</option>
                            </select>
                        </div>
                        <!--end::Col-->

                        <!--begin::Card body-->
                        <div class="col-md-12">
                            <!--begin::Input group-->
                            <!--begin::Label-->
                            <label class="form-label">Lançamentos Padrão</label>
                            <!--end::Label-->
                            <!--begin::Select2-->
                            <select class="form-select mb-2" data-control="select2" data-placeholder="Selecione os lançamentos padrão ou 'Todos'"
                                data-allow-clear="true" multiple="multiple" name="lancamentos_padrao[]" id="lancamentos_padrao">
                                <option value="todos">Todos</option>
                                @foreach ($lps as $lp)
                                    <option value="{{ $lp->id }}" data-type="{{ $lp->type }}">{{ $lp->id }} - {{ $lp->description }}</option>
                                @endforeach
                            </select>
                            <!--end::Select2-->
                            <!--end::Input group-->
                        </div>
                        <!--end::Card body-->
                    </div>

                    <!--begin::Actions-->
                    <div class="d-flex justify-content-end mt-8">
                        <button type="submit" class="btn btn-primary" id="kt_btn_gerar_relatorio">
                            <span class="indicator-label">
                                <i class="bi bi-file-earmark-pdf me-2"></i>Gerar Relatório PDF
                            </span>
                            <span class="indicator-progress">
                                Aguarde... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                    </div>
                    <!--end::Actions-->
                </form>
            </div>
            <!--end::Card body-->
        </div>
        <!--end::Card-->
    </div>
    <!--end::Col-->
</div>
<!--end::Row-->

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('kt_form_relatorio');
        const btnGerar = document.getElementById('kt_btn_gerar_relatorio');
        const dataInicial = document.getElementById('data_inicial');
        const dataFinal = document.getElementById('data_final');
        const lancamentosPadrao = document.getElementById('lancamentos_padrao');
        const entidadeId = document.getElementById('entidade_id');

        // Função auxiliar para configurar o comportamento do select2 com "Todos"
        function setupSelect2Todos(selectElement, selectId) {
            if (selectElement && typeof jQuery !== 'undefined') {
                // Aguardar inicialização do Select2 pelo KTApp
                const checkSelect2 = setInterval(() => {
                    if ($(selectElement).hasClass('select2-hidden-accessible')) {
                        clearInterval(checkSelect2);

                        $(selectElement).on('select2:select', function(e) {
                            const selectedValue = e.params.data.id;

                            // Se selecionou "Todos", desmarcar os outros
                            if (selectedValue === 'todos') {
                                $(selectElement).val(['todos']).trigger('change');
                            } else {
                                // Se selecionou um item específico, remover "Todos" se estiver selecionado
                                const currentValues = $(selectElement).val() || [];
                                if (currentValues.includes('todos')) {
                                    const newValues = currentValues.filter(v => v !== 'todos');
                                    $(selectElement).val(newValues).trigger('change');
                                }
                            }
                        });
                    }
                }, 100);

                // Timeout de segurança (5 segundos)
                setTimeout(() => clearInterval(checkSelect2), 5000);
            }
        }

        // Configurar ambos os selects
        setupSelect2Todos(lancamentosPadrao, 'lancamentos_padrao');
        setupSelect2Todos(entidadeId, 'entidade_id');

        // Validação client-side
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            // Resetar validações
            form.classList.remove('was-validated');
            [dataInicial, dataFinal, document.getElementById('entidade_id')].forEach(input => {
                input.classList.remove('is-invalid');
                const feedback = input.nextElementSibling;
                if (feedback && feedback.classList.contains('invalid-feedback')) {
                    feedback.textContent = '';
                }
            });

            let isValid = true;

            // Validar data inicial
            if (!dataInicial.value) {
                dataInicial.classList.add('is-invalid');
                const feedback = dataInicial.nextElementSibling;
                if (feedback) feedback.textContent = 'A data inicial é obrigatória.';
                isValid = false;
            }

            // Validar data final
            if (!dataFinal.value) {
                dataFinal.classList.add('is-invalid');
                const feedback = dataFinal.nextElementSibling;
                if (feedback) feedback.textContent = 'A data final é obrigatória.';
                isValid = false;
            }

            // Validar que data final é maior que data inicial
            if (dataInicial.value && dataFinal.value) {
                const inicio = new Date(dataInicial.value);
                const fim = new Date(dataFinal.value);

                if (fim < inicio) {
                    dataFinal.classList.add('is-invalid');
                    const feedback = dataFinal.nextElementSibling;
                    if (feedback) feedback.textContent =
                        'A data final deve ser maior ou igual à data inicial.';
                    isValid = false;
                }
            }

            // Validar entidade financeira
            const entidadeIdSelect = document.getElementById('entidade_id');
            const entidadeValues = entidadeIdSelect ? (entidadeIdSelect.selectedOptions.length > 0 ? Array.from(entidadeIdSelect.selectedOptions).map(opt => opt.value) : []) : [];
            if (entidadeValues.length === 0) {
                entidadeIdSelect.classList.add('is-invalid');
                const feedback = entidadeIdSelect.nextElementSibling;
                if (feedback) feedback.textContent = 'Selecione pelo menos uma entidade financeira ou "Todos".';
                isValid = false;
            }

            if (!isValid) {
                form.classList.add('was-validated');
                return;
            }

            // Se válido, mostrar loading e submeter
            btnGerar.setAttribute('data-kt-indicator', 'on');
            btnGerar.disabled = true;

            // Criar um form temporário para submissão com target="_blank" para download
            const tempForm = document.createElement('form');
            tempForm.method = 'POST';
            tempForm.action = form.action;
            tempForm.target = '_blank';

            // Adicionar CSRF token
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = document.querySelector('input[name="_token"]').value;
            tempForm.appendChild(csrfInput);

            // Adicionar todos os campos do formulário
            Array.from(form.elements).forEach(element => {
                if (element.name && element.type !== 'submit') {
                    // Se for select múltiplo, adicionar cada valor selecionado
                    if (element.multiple && element.selectedOptions.length > 0) {
                        Array.from(element.selectedOptions).forEach(option => {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = element.name;
                            input.value = option.value;
                            tempForm.appendChild(input);
                        });
                    } else if (element.value) {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = element.name;
                        input.value = element.value;
                        tempForm.appendChild(input);
                    }
                }
            });

            document.body.appendChild(tempForm);
            tempForm.submit();
            document.body.removeChild(tempForm);

            // Resetar botão após um tempo
            setTimeout(() => {
                btnGerar.removeAttribute('data-kt-indicator');
                btnGerar.disabled = false;
            }, 2000);
        });
    });
</script>
