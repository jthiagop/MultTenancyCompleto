@php
    $modalId = 'kt_modal_gerar_recibo_ajax';
@endphp

<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered mw-750px">
        <!--begin::Modal content-->
        <div class="modal-content rounded">
            <!--begin::Modal header-->
            <div class="modal-header border-0 pb-0">
                <div>
                    <h5 class="modal-title fw-bold" id="recibo_modal_title">Gerar Recibo</h5>
                    <div class="text-muted fs-7">
                        Transação <span class="badge badge-light-primary">#<span id="recibo_transacao_id_display"></span></span>
                        <span class="badge ms-2" id="recibo_numero_badge" style="display: none;"></span>
                    </div>
                </div>
                <button type="button" class="btn btn-icon btn-sm btn-active-light-primary" data-bs-dismiss="modal" aria-label="Fechar">
                    <i class="fa-solid fa-xmark fs-4"></i>
                </button>
            </div>
            <!--end::Modal header-->

            <form id="form_gerar_recibo_ajax" method="POST" action="#">
                @csrf
                <input type="hidden" name="redirect_to_print" value="true">
                <input type="hidden" name="tipo_transacao" id="recibo_tipo_transacao">
                <input type="hidden" name="transacao_id" id="recibo_transacao_id">
                <input type="hidden" name="data_emissao" id="recibo_data_emissao">
                <input type="hidden" name="valor" id="recibo_valor">

                <!--begin::Modal body-->
                <div class="modal-body scroll-y px-10 px-lg-15 pt-5 pb-10">

                    <!--begin::Resumo da transação-->
                    <div class="d-flex flex-wrap align-items-center bg-light-primary rounded p-4 mb-8">
                        <div class="d-flex align-items-center me-6 mb-2 mb-md-0">
                            <span id="recibo_tipo_icon" class="svg-icon svg-icon-2 me-2"></span>
                            <span class="fw-bold text-gray-700" id="recibo_tipo_display">—</span>
                        </div>
                        <div class="d-flex align-items-center me-6 mb-2 mb-md-0">
                            <i class="fa-solid fa-calendar-day text-gray-500 me-2"></i>
                            <span class="text-gray-700" id="recibo_data_display">—</span>
                        </div>
                        <div class="d-flex align-items-center me-6 mb-2 mb-md-0">
                            <i class="fa-solid fa-brazilian-real-sign text-gray-500 me-2"></i>
                            <span class="fw-bold fs-5 text-primary" id="recibo_valor_display">R$ 0,00</span>
                        </div>
                        <div class="d-flex align-items-center flex-grow-1 text-truncate">
                            <i class="fa-solid fa-file-lines text-gray-500 me-2"></i>
                            <span class="text-gray-600 text-truncate" id="recibo_descricao_display" title="">—</span>
                        </div>
                    </div>
                    <!--end::Resumo da transação-->

                    <!--begin::Alert parceiro auto-preenchido-->
                    <div id="recibo_parceiro_alert" class="alert alert-info d-flex align-items-center p-4 mb-6" style="display: none;">
                        <i class="fa-solid fa-magic-wand-sparkles fs-3 me-3"></i>
                        <div class="d-flex flex-column">
                            <span class="fw-semibold">Dados do parceiro preenchidos</span>
                            <span class="fs-7 text-gray-600">Preenchido com dados de <strong id="recibo_parceiro_nome_alert"></strong>. Revise antes de emitir.</span>
                        </div>
                    </div>
                    <!--end::Alert-->

                    <p class="text-muted fs-7 mb-6">Campos com <span class="text-danger">*</span> são obrigatórios.</p>

                    <!--begin::Dados do destinatário-->
                    <div class="row g-5 mb-6">
                        <x-tenant-input
                            name="nome"
                            id="recibo_nome"
                            label="Nome"
                            placeholder="Nome da empresa ou pessoa"
                            :required="true"
                            class="col-md-8" />

                        <x-tenant-input
                            name="cpf_cnpj"
                            id="recibo_cpf_cnpj"
                            label="CPF/CNPJ"
                            placeholder="000.000.000-00"
                            class="col-md-4" />
                    </div>
                    <!--end::Dados do destinatário-->

                    <!--begin::Endereço (componente reutilizável)-->
                    <x-tenant-endereco
                        prefix="recibo_"
                        dropdownParent="#{{ $modalId }}" />
                    <!--end::Endereço-->

                    <!--begin::Referente-->
                    <x-tenant-textarea
                        name="referente"
                        id="recibo_referente"
                        label="Referente"
                        placeholder="Descrição do serviço prestado ou referência do pagamento"
                        :rows="3"
                        class="mb-4" />
                    <!--end::Referente-->

                </div>
                <!--end::Modal body-->

                <!--begin::Modal footer-->
                <div class="modal-footer border-top pt-5">
                    <button type="button" data-bs-dismiss="modal" class="btn btn-sm btn-light me-3">
                        <i class="fa-solid fa-times me-1"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-sm btn-primary" id="recibo_submit_btn">
                        <span class="indicator-label">
                            <i class="fa-solid fa-file-signature me-2"></i> Emitir Recibo
                        </span>
                        <span class="indicator-progress">
                            Gerando... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                </div>
                <!--end::Modal footer-->
            </form>
        </div>
        <!--end::Modal content-->
    </div>
    <!--end::Modal dialog-->
</div>

<script>
    /**
     * Abre o modal de recibo com auto-preenchimento inteligente.
     *
     * Prioridade de preenchimento:
     * 1. Se modo edição (isEditMode=true) e já existe recibo -> usa dados do recibo existente
     * 2. Se existe parceiro vinculado à transação -> preenche do parceiro automaticamente
     * 3. Senão -> campos vazios para digitação manual
     *
     * O campo "Referente" é sempre preenchido com a descrição + histórico complementar da transação.
     */
    function abrirModalReciboAjax(transacao, isEditMode = false) {
        const ids = {
            form: 'form_gerar_recibo_ajax',
            tipo: 'recibo_tipo_transacao',
            tipoDisplay: 'recibo_tipo_display',
            tipoIcon: 'recibo_tipo_icon',
            transacaoId: 'recibo_transacao_id',
            idDisplay: 'recibo_transacao_id_display',
            data: 'recibo_data_emissao',
            dataDisplay: 'recibo_data_display',
            valor: 'recibo_valor',
            valorDisplay: 'recibo_valor_display',
            descricaoDisplay: 'recibo_descricao_display',
            numeroBadge: 'recibo_numero_badge',
            nome: 'recibo_nome',
            cpf: 'recibo_cpf_cnpj',
            referente: 'recibo_referente',
            title: 'recibo_modal_title',
            alertParceiro: 'recibo_parceiro_alert',
            alertParceiroNome: 'recibo_parceiro_nome_alert',
        };

        // Prefixo do componente de endereço
        const enderecoPrefix = 'recibo_';

        // --- Limpar erros anteriores ---
        const form = document.getElementById(ids.form);
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        form.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');

        // --- Dados básicos ---
        document.getElementById(ids.idDisplay).textContent = transacao.id;
        document.getElementById(ids.transacaoId).value = transacao.id;

        // Tipo (Recebimento/Pagamento)
        const isEntrada = transacao.tipo === 'entrada';
        document.getElementById(ids.tipo).value = isEntrada ? 'Recebimento' : 'Pagamento';
        document.getElementById(ids.tipoDisplay).textContent = isEntrada ? 'Recebimento' : 'Pagamento';
        document.getElementById(ids.tipoIcon).innerHTML = isEntrada
            ? '<i class="fa-solid fa-arrow-down text-success"></i>'
            : '<i class="fa-solid fa-arrow-up text-danger"></i>';

        // Data
        document.getElementById(ids.data).value = transacao.data_competencia_formatada || '';
        document.getElementById(ids.dataDisplay).textContent = transacao.data_competencia_formatada || '—';

        // Valor
        const valorFormatado = parseFloat(transacao.valor).toLocaleString('pt-BR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        document.getElementById(ids.valor).value = valorFormatado;
        document.getElementById(ids.valorDisplay).textContent = 'R$ ' + valorFormatado;

        // Descrição
        const descEl = document.getElementById(ids.descricaoDisplay);
        descEl.textContent = transacao.descricao || '—';
        descEl.title = transacao.descricao || '';

        // Atualizar action do form
        form.action = '/relatorios/recibos/gerar/' + transacao.id;

        // --- Construir "Referente" automaticamente a partir da transação ---
        let referenteAuto = transacao.descricao || '';
        if (transacao.historico_complementar) {
            referenteAuto += (referenteAuto ? ' — ' : '') + transacao.historico_complementar;
        }

        // Esconder alerta de parceiro por padrão
        document.getElementById(ids.alertParceiro).style.display = 'none';

        // Badge de número do recibo
        const badgeEl = document.getElementById(ids.numeroBadge);

        // Campo para receber foco
        let campoFoco = ids.nome;

        if (isEditMode && transacao.recibo) {
            // =============================================
            // MODO EDIÇÃO — preencher do recibo existente
            // =============================================
            document.getElementById(ids.title).textContent = 'Editar Recibo';
            badgeEl.textContent = 'Recibo #' + transacao.recibo.id;
            badgeEl.className = 'badge badge-light-success ms-2';
            badgeEl.style.display = 'inline';

            document.getElementById(ids.nome).value = transacao.recibo.nome || '';
            document.getElementById(ids.cpf).value = transacao.recibo.cpf_cnpj || '';
            document.getElementById(ids.referente).value = transacao.recibo.referente || '';

            if (transacao.recibo.address) {
                preencherEndereco(enderecoPrefix, transacao.recibo.address);
            } else {
                limparEndereco(enderecoPrefix);
            }

            campoFoco = ids.referente;

        } else if (transacao.parceiro) {
            // =============================================
            // MODO CRIAÇÃO COM PARCEIRO — auto-preencher
            // =============================================
            document.getElementById(ids.title).textContent = 'Gerar Recibo';
            badgeEl.textContent = 'Novo';
            badgeEl.className = 'badge badge-light-warning ms-2';
            badgeEl.style.display = 'inline';

            // Preencher do parceiro
            document.getElementById(ids.nome).value = transacao.parceiro.nome || '';
            document.getElementById(ids.cpf).value = transacao.parceiro.cpf_cnpj || '';
            document.getElementById(ids.referente).value = referenteAuto;

            // Preencher endereço do parceiro (usando helper global do componente)
            if (transacao.parceiro.address) {
                preencherEndereco(enderecoPrefix, transacao.parceiro.address);
            } else {
                limparEndereco(enderecoPrefix);
            }

            // Mostrar alerta informativo
            document.getElementById(ids.alertParceiro).style.display = 'flex';
            document.getElementById(ids.alertParceiroNome).textContent = transacao.parceiro.nome;

            campoFoco = ids.referente; // Foco no referente pois dados já vieram

        } else {
            // =============================================
            // MODO CRIAÇÃO SEM PARCEIRO — campos vazios
            // =============================================
            document.getElementById(ids.title).textContent = 'Gerar Recibo';
            badgeEl.textContent = 'Novo';
            badgeEl.className = 'badge badge-light-warning ms-2';
            badgeEl.style.display = 'inline';

            document.getElementById(ids.nome).value = '';
            document.getElementById(ids.cpf).value = '';
            document.getElementById(ids.referente).value = referenteAuto;

            limparEndereco(enderecoPrefix);

            campoFoco = ids.nome; // Foco no nome pois precisa preencher
        }

        // Abrir modal e definir foco
        const modalEl = document.getElementById('kt_modal_gerar_recibo_ajax');
        const modal = new bootstrap.Modal(modalEl);

        modalEl.addEventListener('shown.bs.modal', function handler() {
            document.getElementById(campoFoco).focus();
            modalEl.removeEventListener('shown.bs.modal', handler);
        });

        modal.show();
    }

    // ==========================================
    // Submit AJAX do formulário
    // ==========================================
    document.getElementById('form_gerar_recibo_ajax').addEventListener('submit', function(e) {
        e.preventDefault();

        const form = this;
        const submitBtn = document.getElementById('recibo_submit_btn');
        const formData = new FormData(form);

        // Limpar erros anteriores
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        form.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');

        // Mostrar loading
        submitBtn.setAttribute('data-kt-indicator', 'on');
        submitBtn.disabled = true;

        fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => Promise.reject(data));
                }
                return response.json();
            })
            .then(data => {
                if (data.success && data.pdf_url) {
                    // Fechar modal
                    const modalEl = document.getElementById('kt_modal_gerar_recibo_ajax');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();

                    form.reset();

                    // Toast de sucesso + abrir PDF
                    Swal.fire({
                        title: 'Recibo gerado!',
                        text: 'Abrindo PDF em nova aba...',
                        icon: 'success',
                        timer: 2000,
                        timerProgressBar: true,
                        showConfirmButton: false,
                        didOpen: () => {
                            window.open(data.pdf_url, '_blank');
                        }
                    });
                }
            })
            .catch(error => {
                if (error.errors) {
                    // Exibir erros nos campos usando invalid-feedback existentes
                    Object.keys(error.errors).forEach(fieldName => {
                        const field = form.querySelector('[name="' + fieldName + '"]');
                        if (field) {
                            field.classList.add('is-invalid');
                            const feedback = field.closest('.fv-row')?.querySelector('.invalid-feedback');
                            if (feedback) {
                                feedback.textContent = error.errors[fieldName][0];
                            }
                        }
                    });

                    const firstError = form.querySelector('.is-invalid');
                    if (firstError) {
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        firstError.focus();
                    }
                } else {
                    Swal.fire({
                        text: error.message || 'Erro ao gerar recibo. Tente novamente.',
                        icon: 'error',
                        buttonsStyling: false,
                        confirmButtonText: 'Ok',
                        customClass: { confirmButton: 'btn btn-primary' }
                    });
                }
            })
            .finally(() => {
                submitBtn.removeAttribute('data-kt-indicator');
                submitBtn.disabled = false;
            });
    });

    // ==========================================
    // Máscara dinâmica CPF/CNPJ
    // ==========================================
    document.getElementById('recibo_cpf_cnpj').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');

        if (value.length <= 11) {
            // CPF: 000.000.000-00
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        } else {
            // CNPJ: 00.000.000/0000-00
            value = value.substring(0, 14);
            value = value.replace(/^(\d{2})(\d)/, '$1.$2');
            value = value.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
            value = value.replace(/\.(\d{3})(\d)/, '.$1/$2');
            value = value.replace(/(\d{4})(\d)/, '$1-$2');
        }

        e.target.value = value;
    });
</script>
