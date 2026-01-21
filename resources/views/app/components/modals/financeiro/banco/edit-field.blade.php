<!--begin::Modal - Edit Field-->
<div class="modal fade" tabindex="-1" id="modal_edit_field">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="modal_edit_field_title">Editar Campo</h3>

                <!--begin::Close-->
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                    <i class="bi bi-x-lg fs-1"></i>
                </div>
                <!--end::Close-->
            </div>

            <div class="modal-body">
                <!--begin::Form-->
                <form id="form_edit_field" class="form" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="field_type" id="field_type" value="">

                    <!--begin::Input group - Descrição (oculto por padrão)-->
                    <div class="fv-row mb-7" id="field_descricao" style="display: none;">
                        <x-tenant-input name="descricao" id="edit_descricao" label="Descrição"
                            placeholder="Informe a descrição" required class="" />
                    </div>
                    <!--end::Input group-->

                    <!--begin::Input group - Categoria (oculto por padrão)-->
                    <div class="fv-row mb-7" id="field_lancamento_padrao_id" style="display: none;">
                        <x-tenant-select name="lancamento_padrao_id" id="edit_lancamento_padrao_id"
                            label="Categoria" placeholder="Escolha um Lançamento..." required
                            :allowClear="true" :minimumResultsForSearch="0" dropdown-parent="#modal_edit_field"
                            tooltip="As categorias são utilizadas para formar um Plano de Contas. Muitas destas categorias são demonstradas em Relatórios e também alimentam o DRE Gerencial."
                            labelSize="fs-6" class="">
                            @if (isset($lps))
                                @foreach ($lps as $lp)
                                    @if($lp->type === $banco->tipo)
                                        <option value="{{ $lp->id }}" data-description="{{ $lp->description }}"
                                            data-type="{{ $lp->type }}">{{ $lp->id }} - {{ $lp->description }}</option>
                                    @endif
                                @endforeach
                            @endif
                        </x-tenant-select>
                    </div>
                    <!--end::Input group-->

                    <!--begin::Input group - Centro de Custo (oculto por padrão)-->
                    <div class="fv-row mb-7" id="field_cost_center_id" style="display: none;">
                        <x-tenant-select name="cost_center_id" id="edit_cost_center_id" label="Centro de Custo"
                            placeholder="Selecione o Centro de Custo" :allowClear="true" :minimumResultsForSearch="0"
                            dropdown-parent="#modal_edit_field" labelSize="fs-6" class="">
                            @if (isset($centrosAtivos))
                                @foreach ($centrosAtivos as $centro)
                                    <option value="{{ $centro->id }}">{{ $centro->code }} - {{ $centro->name }}</option>
                                @endforeach
                            @endif
                        </x-tenant-select>
                    </div>
                    <!--end::Input group-->
                </form>
                <!--end::Form-->
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-sm btn-primary" id="btn_save_field">
                    <span class="indicator-label">Salvar</span>
                    <span class="indicator-progress">Aguarde...
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                </button>
            </div>
        </div>
    </div>
</div>
<!--end::Modal - Edit Field-->

<script>
    $(document).ready(function() {
        var transacaoId = {{ $banco->id }};
        var updateUrl = '{{ route("banco.update", $banco->id) }}';


        // Quando o modal é aberto, configura o campo correspondente
        $('#modal_edit_field').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var field = button.data('field');
            var value = button.data('value');

            // Oculta todos os campos
            $('#field_descricao, #field_lancamento_padrao_id, #field_cost_center_id').hide();

            // Configura o formulário baseado no campo
            if (field === 'descricao') {
                $('#modal_edit_field_title').text('Editar Descrição');
                $('#field_type').val('descricao');
                $('#field_descricao').show();
                $('#edit_descricao').val(value).trigger('change');
            } else if (field === 'lancamento_padrao_id') {
                $('#modal_edit_field_title').text('Editar Categoria');
                $('#field_type').val('lancamento_padrao_id');
                $('#field_lancamento_padrao_id').show();

                // Inicializa Select2 se ainda não foi inicializado
                var $select = $('#edit_lancamento_padrao_id');
                if (!$select.hasClass('select2-hidden-accessible')) {
                    $select.select2({
                        dropdownParent: $('#modal_edit_field'),
                        minimumResultsForSearch: 0
                    });
                }
                $select.val(value).trigger('change');
            } else if (field === 'cost_center_id') {
                $('#modal_edit_field_title').text('Editar Centro de Custo');
                $('#field_type').val('cost_center_id');
                $('#field_cost_center_id').show();
                // Inicializa Select2 se ainda não foi inicializado
                if (!$('#edit_cost_center_id').hasClass('select2-hidden-accessible')) {
                    $('#edit_cost_center_id').select2({
                        dropdownParent: $('#modal_edit_field'),
                        minimumResultsForSearch: 0
                    });
                }
                $('#edit_cost_center_id').val(value).trigger('change');
            }

            // Atualiza a action do formulário
            $('#form_edit_field').attr('action', updateUrl);
        });

        // Submit do formulário via botão fora do form
        $('#btn_save_field').on('click', function(e) {
            e.preventDefault();

            var form = $('#form_edit_field');
            var formData = new FormData(form[0]);
            var fieldType = $('#field_type').val();

            // Adiciona apenas o campo sendo editado
            formData.append('_method', 'PUT');

            $.ajax({
                url: updateUrl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                beforeSend: function() {
                    $('#btn_save_field').attr('data-kt-indicator', 'on');
                    $('#btn_save_field').prop('disabled', true);
                },
                success: function(response) {
                    $('#modal_edit_field').modal('hide');

                    // Exibe mensagem de sucesso usando PHP Flasher
                    if (typeof window.flasher !== 'undefined') {
                        window.flasher.success(response.message || 'Campo atualizado com sucesso!');
                    } else if (typeof flasher !== 'undefined' && typeof flasher.success === 'function') {
                        flasher.success(response.message || 'Campo atualizado com sucesso!');
                    }

                    // Recarrega a página para exibir os dados atualizados
                    setTimeout(function() {
                        location.reload();
                    }, 500);
                },
                error: function(xhr) {
                    $('#btn_save_field').removeAttr('data-kt-indicator');
                    $('#btn_save_field').prop('disabled', false);

                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        // Exibe erros de validação
                        $.each(errors, function(field, messages) {
                            var input = $('[name="' + field + '"]');
                            input.addClass('is-invalid');
                            input.siblings('.invalid-feedback').remove();
                            input.after('<div class="invalid-feedback">' + messages[0] + '</div>');
                        });
                    } else {
                        alert('Erro ao salvar. Tente novamente.');
                    }
                }
            });
        });

        // Limpa erros quando o modal é fechado
        $('#modal_edit_field').on('hidden.bs.modal', function() {
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();
        });
    });
</script>
