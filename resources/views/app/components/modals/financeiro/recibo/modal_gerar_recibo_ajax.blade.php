@php
    $modalId = 'kt_modal_gerar_recibo_ajax';
@endphp

<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered mw-850px">
        <!--begin::Modal content-->
        <div class="modal-content rounded">
            <!--begin::Modal header-->
            <div class="modal-header pb-0 border-0 justify-content-end">
                <!--begin::Close-->
                <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    <span class="svg-icon svg-icon-1">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                transform="rotate(-45 6 17.3137)" fill="currentColor" />
                            <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                transform="rotate(45 7.41422 6)" fill="currentColor" />
                        </svg>
                    </span>
                </div>
                <!--end::Close-->
            </div>
            <!--begin::Modal header-->
            <!--begin::Modal body-->
            <div class="modal-body scroll-y px-10 px-lg-15 pt-0 pb-15">
                <!--begin::Heading-->
                <div class="mb-13 text-center">
                    <!--begin::Title-->
                    <h1 class="mb-3">Gerar Recibo</h1>
                    <!--end::Title-->
                    <!--begin::Description-->
                    <div class="text-gray-400 fw-semibold fs-5">
                        Preencha os dados abaixo para emitir o recibo referente à transação <span id="recibo_transacao_id_display" class="fw-bold"></span>.
                    </div>
                    <!--end::Description-->
                </div>
                <!--end::Heading-->
                
                <!--begin:Form-->
                <form id="form_gerar_recibo_ajax" method="POST" action="#">
                    @csrf
                    <input type="hidden" name="redirect_to_print" value="true">
                    <input type="hidden" name="tipo_transacao" id="recibo_tipo_transacao">
                    <input type="hidden" name="transacao_id" id="recibo_transacao_id">

                    <!--begin::Input group-->
                    <div class="d-flex flex-column align-items-start flex-xxl-row">
                        <!--begin::Input group-->
                        <div class="d-flex align-items-center flex-equal fw-row me-4 order-2">
                            <div class="fs-6 fw-bold text-gray-700 text-nowrap">Data:</div>
                            <div class="position-relative d-flex align-items-center w-150px">
                                <input class="form-control form-control-transparent fw-bold pe-5"
                                    placeholder="Select date" readonly name="data_emissao" id="recibo_data_emissao" />
                            </div>
                        </div>
                        <!--end::Input group-->
                        
                        <!--begin::Input group-->
                        <div class="d-flex flex-center flex-equal fw-row text-nowrap order-1 order-xxl-2 me-4">
                            <span class=" fw-bold readonly text-gray-800">Número #</span>
                            <input type="text" class="form-control form-control-solid" id="recibo_numero_display"
                                value="Novo" readonly />
                        </div>
                        <!--end::Input group-->
                        
                        <!--begin::Input group-->
                        <div class="d-flex align-items-center justify-content-end flex-equal order-3 fw-row">
                            <div class="fs-6 fw-bold text-gray-700 text-nowrap">Valor: R$</div>
                            <div class="position-relative d-flex align-items-center w-150px">
                                <input class="form-control form-control-transparent fw-bold pe-5"
                                    name="valor" id="recibo_valor" readonly />
                            </div>
                        </div>
                        <!--end::Input group-->
                    </div>
                    
                    <div class="separator separator-dashed my-10"></div>
                    
                    <!--begin::Input group-->
                    <div class="row g-9 mb-8">
                        <div class="col-md-8 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Nome</label>
                            <input type="text" class="form-control form-control-solid"
                                placeholder="Nome da Empresa ou da Pessoa" name="nome" id="recibo_nome" />
                        </div>
                        <div class="col-md-4 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span>CPF/CNPJ</span>
                            </label>
                            <input type="text" class="form-control form-control-solid"
                                placeholder="CPF ou CNPJ" name="cpf_cnpj" id="recibo_cpf_cnpj" />
                        </div>
                    </div>
                    
                    <!-- Endereço -->
                    <div class="row g-9 mb-8">
                        <div class="col-md-3 fv-row">
                            <label class="fs-6 fw-semibold mb-2">CEP</label>
                            <input class="form-control form-control-solid" placeholder="00000-000" id="recibo_cep" name="cep" />
                        </div>
                        <div class="col-md-7 fv-row">
                            <label class="fs-6 fw-semibold mb-2">Rua</label>
                            <input class="form-control form-control-solid" placeholder="Logradouro" id="recibo_logradouro" name="logradouro" />
                        </div>
                        <div class="col-md-2 fv-row">
                            <label class="fs-6 fw-semibold mb-2">Número</label>
                            <input class="form-control form-control-solid" placeholder="Nº" id="recibo_numero" name="numero" />
                        </div>
                    </div>
                    
                    <div class="row g-9 mb-8">
                        <div class="col-md-4 fv-row">
                            <label class="fs-6 fw-semibold mb-2">Bairro</label>
                            <input type="text" class="form-control form-control-solid" placeholder="Bairro" id="recibo_bairro" name="bairro" />
                        </div>
                        <div class="col-md-4 fv-row">
                            <label class="fs-6 fw-semibold mb-2">Cidade</label>
                            <input type="text" class="form-control form-control-solid" placeholder="Cidade" id="recibo_localidade" name="localidade" />
                        </div>
                        <div class="col-md-4 fv-row">
                            <label class="fs-6 fw-semibold mb-2">Estado</label>
                            <select id="recibo_uf" name="uf" class="form-select form-select-solid" data-control="select2" data-dropdown-parent="#{{ $modalId }}" data-placeholder="UF">
                                <option value=""></option>
                                <option value="AC">Acre</option>
                                <option value="AL">Alagoas</option>
                                <option value="AP">Amapá</option>
                                <option value="AM">Amazonas</option>
                                <option value="BA">Bahia</option>
                                <option value="CE">Ceará</option>
                                <option value="DF">Distrito Federal</option>
                                <option value="ES">Espírito Santo</option>
                                <option value="GO">Goiás</option>
                                <option value="MA">Maranhão</option>
                                <option value="MT">Mato Grosso</option>
                                <option value="MS">Mato Grosso do Sul</option>
                                <option value="MG">Minas Gerais</option>
                                <option value="PA">Pará</option>
                                <option value="PB">Paraíba</option>
                                <option value="PR">Paraná</option>
                                <option value="PE">Pernambuco</option>
                                <option value="PI">Piauí</option>
                                <option value="RJ">Rio de Janeiro</option>
                                <option value="RN">Rio Grande do Norte</option>
                                <option value="RS">Rio Grande do Sul</option>
                                <option value="RO">Rondônia</option>
                                <option value="RR">Roraima</option>
                                <option value="SC">Santa Catarina</option>
                                <option value="SP">São Paulo</option>
                                <option value="SE">Sergipe</option>
                                <option value="TO">Tocantins</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Referente -->
                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="fs-6 fw-semibold mb-2">Referente</label>
                        <textarea class="form-control form-control-solid" rows="4" name="referente" id="recibo_referente" placeholder="Descreva o serviço prestado"></textarea>
                    </div>
                    
                    <!-- Actions -->
                    <div class="text-center">
                        <button type="reset" data-bs-dismiss="modal" class="btn btn-light me-3">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <span class="indicator-label">Emitir Recibo</span>
                            <span class="indicator-progress">Aguarde... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                </form>
                <!--end:Form-->
            </div>
            <!--end::Modal body-->
        </div>
        <!--end::Modal content-->
    </div>
    <!--end::Modal dialog-->
</div>

<script>
    function abrirModalReciboAjax(transacao, isEditMode = false) {
        // IDs dos elementos
        const ids = {
            idDisplay: 'recibo_transacao_id_display',
            form: 'form_gerar_recibo_ajax',
            tipo: 'recibo_tipo_transacao',
            transacaoId: 'recibo_transacao_id',
            data: 'recibo_data_emissao',
            valor: 'recibo_valor',
            nome: 'recibo_nome',
            cpf: 'recibo_cpf_cnpj',
            referente: 'recibo_referente',
            cep: 'recibo_cep',
            logradouro: 'recibo_logradouro',
            numero: 'recibo_numero',
            bairro: 'recibo_bairro',
            localidade: 'recibo_localidade',
            uf: 'recibo_uf'
        };
        
        // Preencher dados básicos
        document.getElementById(ids.idDisplay).textContent = `#${transacao.id}`;
        document.getElementById(ids.transacaoId).value = transacao.id;
        document.getElementById(ids.tipo).value = transacao.tipo === 'entrada' ? 'Recebimento' : 'Pagamento';
        document.getElementById(ids.data).value = transacao.data_competencia_formatada || '';
        document.getElementById(ids.valor).value = parseFloat(transacao.valor).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        
        // Atualizar action do form com a rota correta (substituindo placeholder)
        // Rota baseada em: Route::post('/relatorios/recibos/gerar/{transacao}', ...)
        const form = document.getElementById(ids.form);
        form.action = `/relatorios/recibos/gerar/${transacao.id}`;
        
        // Se for modo de edição e existir recibo, preencher os campos
        if (isEditMode && transacao.recibo) {
            // Dados do recibo
            document.getElementById(ids.nome).value = transacao.recibo.nome || '';
            document.getElementById(ids.cpf).value = transacao.recibo.cpf_cnpj || '';
            document.getElementById(ids.referente).value = transacao.recibo.referente || '';
            
            // Número do recibo
            document.getElementById('recibo_numero_display').value = transacao.recibo.id;
            
            // Dados do endereço (se existir)
            if (transacao.recibo.address) {
                document.getElementById(ids.cep).value = transacao.recibo.address.cep || '';
                document.getElementById(ids.logradouro).value = transacao.recibo.address.rua || '';
                document.getElementById(ids.numero).value = transacao.recibo.address.numero || '';
                document.getElementById(ids.bairro).value = transacao.recibo.address.bairro || '';
                document.getElementById(ids.localidade).value = transacao.recibo.address.cidade || '';
                $('#' + ids.uf).val(transacao.recibo.address.uf || '').trigger('change');
            } else {
                // Limpar endereço se não existir
                document.getElementById(ids.cep).value = '';
                document.getElementById(ids.logradouro).value = '';
                document.getElementById(ids.numero).value = '';
                document.getElementById(ids.bairro).value = '';
                document.getElementById(ids.localidade).value = '';
                $('#' + ids.uf).val('').trigger('change');
            }
            
            // Atualizar título do modal
            document.querySelector('#{{ $modalId }} .modal-body h1').textContent = 'Editar Recibo';
        } else {
            // Modo criação - limpar campos
            document.getElementById(ids.nome).value = '';
            document.getElementById(ids.cpf).value = '';
            document.getElementById(ids.referente).value = '';
            
            // Número do recibo como "Novo"
            document.getElementById('recibo_numero_display').value = 'Novo';
            
            // Limpar endereço
            document.getElementById(ids.cep).value = '';
            document.getElementById(ids.logradouro).value = '';
            document.getElementById(ids.numero).value = '';
            document.getElementById(ids.bairro).value = '';
            document.getElementById(ids.localidade).value = '';
            $('#' + ids.uf).val('').trigger('change');
            
            // Atualizar título do modal
            document.querySelector('#{{ $modalId }} .modal-body h1').textContent = 'Gerar Recibo';
        }
        
        // Abrir modal
        const modal = new bootstrap.Modal(document.getElementById('{{ $modalId }}'));
        modal.show();
    }
    
    // Script de CEP
    $(document).ready(function() {
        $('#recibo_cep').on('blur', function() {
            var cep = $(this).val().replace(/\D/g, '');
            if (cep !== "") {
                if (/^[0-9]{8}$/.test(cep)) {
                    $('#recibo_logradouro').val('...');
                    $.getJSON("https://viacep.com.br/ws/" + cep + "/json/?callback=?", function(dados) {
                        if (!("erro" in dados)) {
                            $('#recibo_logradouro').val(dados.logradouro);
                            $('#recibo_bairro').val(dados.bairro);
                            $('#recibo_localidade').val(dados.localidade);
                            $('#recibo_uf').val(dados.uf).trigger('change');
                        } else {
                            alert("CEP não encontrado.");
                        }
                    });
                } else {
                    alert("CEP inválido.");
                }
            }
        });
    });

    // Substituir submit do form por AJAX
    document.getElementById('form_gerar_recibo_ajax').addEventListener('submit', function(e) {
        e.preventDefault(); // Prevenir submit padrão
        
        const form = this;
        const submitBtn = form.querySelector('button[type="submit"]');
        const formData = new FormData(form);
        
        // Limpar erros anteriores
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        form.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
        
        // Mostrar loading
        submitBtn.setAttribute('data-kt-indicator', 'on');
        submitBtn.disabled = true;
        
        // Enviar via AJAX
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
            // Sucesso - abrir PDF em nova aba
            if (data.success && data.pdf_url) {
                window.open(data.pdf_url, '_blank');
                
                // Fechar modal
                const modalEl = document.getElementById('{{ $modalId }}');
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();
                
                // Resetar form
                form.reset();
                
                // Mostrar mensagem de sucesso
                Swal.fire({
                    text: data.message || 'Recibo gerado com sucesso!',
                    icon: 'success',
                    buttonsStyling: false,
                    confirmButtonText: 'Ok',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    }
                });
            }
        })
        .catch(error => {
            // Erro de validação
            if (error.errors) {
                // Exibir erros nos campos
                Object.keys(error.errors).forEach(fieldName => {
                    const field = form.querySelector(`[name="${fieldName}"]`);
                    if (field) {
                        field.classList.add('is-invalid');
                        
                        // Criar div de erro
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'invalid-feedback';
                        errorDiv.textContent = error.errors[fieldName][0];
                        
                        // Inserir após o campo
                        field.parentNode.appendChild(errorDiv);
                    }
                });
                
                // Scroll para o primeiro erro
                const firstError = form.querySelector('.is-invalid');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstError.focus();
                }
            } else {
                // Erro genérico
                Swal.fire({
                    text: error.message || 'Erro ao gerar recibo. Tente novamente.',
                    icon: 'error',
                    buttonsStyling: false,
                    confirmButtonText: 'Ok',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    }
                });
            }
        })
        .finally(() => {
            // Remover loading
            submitBtn.removeAttribute('data-kt-indicator');
            submitBtn.disabled = false;
        });
    });

    // Máscara dinâmica para CPF/CNPJ
    document.getElementById('recibo_cpf_cnpj').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, ''); // Remove tudo que não é dígito
        
        if (value.length <= 11) {
            // Máscara CPF: 000.000.000-00
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        } else {
            // Máscara CNPJ: 00.000.000/0000-00
            value = value.substring(0, 14); // Limita a 14 dígitos
            value = value.replace(/^(\d{2})(\d)/, '$1.$2');
            value = value.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
            value = value.replace(/\.(\d{3})(\d)/, '.$1/$2');
            value = value.replace(/(\d{4})(\d)/, '$1-$2');
        }
        
        e.target.value = value;
    });
</script>
