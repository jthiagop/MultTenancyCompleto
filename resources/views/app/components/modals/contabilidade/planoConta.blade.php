<!--begin::Modal - Cadastrar Conta Contábil-->
<div class="modal fade" id="kt_modal_new_account" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered mw-650px">
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
                <!--begin:Form-->
                {{-- Certifique-se de que a rota ''contabilidade.plano-contas.store')' existe no seu arquivo de rotas --}}
                <form id="kt_modal_new_account_form" class="form" method="POST"
                    action="{{ route('contabilidade.plano-contas.store') }}">
                    @csrf
                    <!--begin::Heading-->
                    <div class="mb-13 text-center">
                        <h1 class="mb-3">Cadastrar Nova Conta Contábil</h1>
                        <div class="text-muted fw-semibold fs-5">Adicione uma nova conta ao seu plano de contas.</div>
                    </div>
                    <!--end::Heading-->

                    <!--begin::Input group-->
                    <div class="row g-9 mb-8">
                        <!--begin::Col-->
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Código da Conta</label>
                            <input type="text" id="account_code_mask" class="form-control form-control-solid"
                                placeholder="Ex: 1.01.01.001" name="code" value="{{ old('code') }}" />
                            @error('code')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                        <!--end::Col-->
                        <!--begin::Col-->
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Tipo</label>
                            <select class="form-select form-select-solid" data-control="select2" data-hide-search="true"
                                data-placeholder="Selecione o tipo" name="type">
                                <option></option>
                                <option value="ativo" {{ old('type') == 'ativo' ? 'selected' : '' }}>Ativo</option>
                                <option value="passivo" {{ old('type') == 'passivo' ? 'selected' : '' }}>Passivo
                                </option>
                                <option value="patrimonio_liquido"
                                    {{ old('type') == 'patrimonio_liquido' ? 'selected' : '' }}>Patrimônio Líquido
                                </option>
                                <option value="receita" {{ old('type') == 'receita' ? 'selected' : '' }}>Receita
                                </option>
                                <option value="despesa" {{ old('type') == 'despesa' ? 'selected' : '' }}>Despesa
                                </option>
                            </select>
                            @error('type')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                        <!--end::Col-->
                    </div>
                    <!--end::Input group-->

                    <!--begin::Input group-->
                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="required d-flex align-items-center fs-6 fw-semibold mb-2">Nome da Conta</label>
                        <input type="text" class="form-control form-control-solid"
                            placeholder="Ex: Caixa Geral da Matriz" name="name" value="{{ old('name') }}" />
                        @error('name')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                    <!--end::Input group-->

                    <!--begin::Input group-->
                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="fs-6 fw-semibold mb-2">Conta Pai (Hierarquia)</label>
                        <select class="form-select form-select-solid" data-control="select2"
                            data-dropdown-parent="#kt_modal_new_account" data-placeholder="Selecione uma conta pai (opcional)" name="parent_id">
                            <option></option>
                            {{-- 
                                Você precisará passar a variável '$contas' do seu controller.
                                Exemplo: $contas = ChartOfAccount::forActiveCompany()->get();
                            --}}
                            @isset($contas)
                                @foreach ($contas as $conta)
                                    <option value="{{ $conta->id }}"
                                        {{ old('parent_id') == $conta->id ? 'selected' : '' }}>
                                        {{ $conta->code }} - {{ $conta->name }}
                                    </option>
                                @endforeach
                            @endisset
                        </select>
                        <div class="text-muted fs-7 mt-2">Selecione uma conta pai se esta for uma sub-conta. Deixe em
                            branco se for uma conta principal.</div>
                    </div>
                    <!--end::Input group-->

                    <!--begin::Actions-->
                    <div class="text-center">
                        <button type="reset" data-bs-dismiss="modal" class="btn btn-light me-3">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <span class="indicator-label">Salvar</span>
                            <span class="indicator-progress">Aguarde...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                    <!--end::Actions-->
                </form>
                <!--end:Form-->
            </div>
            <!--end::Modal body-->
        </div>
        <!--end::Modal content-->
    </div>
    <!--end::Modal dialog-->
</div>
<!--end::Modal - Cadastrar Conta Contábil-->

{{-- Coloque este script no final da sua view index.blade.php --}}
@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // --- CORREÇÃO PRINCIPAL AQUI ---
        // Verifique se o ID '#plano_contas_tabela' corresponde ao ID da sua tabela no HTML.
        const tableBody = document.querySelector('#plano_contas_tabela tbody');
        
        // --- LOG DE DEPURAÇÃO 1 ---
        // Verifique no console do navegador (F12) se a tabela foi encontrada.
        if (!tableBody) {
            console.error("ERRO: A tabela com o seletor '#plano_contas_tabela tbody' não foi encontrada. Verifique o ID da sua tabela.");
            return; // Para o script se a tabela não for encontrada.
        }
        console.log("SUCESSO: Tabela encontrada, o script de ações está ativo.", tableBody);


        const modalElement = document.getElementById('kt_modal_new_account');
        const modal = new bootstrap.Modal(modalElement);
        const form = document.getElementById('kt_modal_new_account_form');
        const modalTitle = document.getElementById('modal-title'); // Dê este ID ao seu <h2> do título do modal
        const createButton = document.querySelector('[data-bs-target="#kt_modal_new_account"]');

        const resetModalToCreate = () => {
            form.reset();
            form.action = "{{ route('contabilidade.plano-contas.store') }}";
            if (form.querySelector('input[name="_method"]')) {
                form.querySelector('input[name="_method"]').remove();
            }
            modalTitle.innerText = "Cadastrar Nova Conta Contábil";
            $(form.querySelector('[name="parent_id"]')).val(null).trigger('change');
        };

        if (createButton) {
            createButton.addEventListener('click', resetModalToCreate);
        }

        tableBody.addEventListener('click', function (e) {
            const button = e.target.closest('.edit-btn, .delete-btn');

            if (!button) {
                return; // Se o clique não foi em um dos nossos botões, ignora.
            }

            e.preventDefault();
            const id = button.dataset.id;
            
            // --- LOG DE DEPURAÇÃO 2 ---
            console.log(`Botão clicado! Ação: ${button.classList.contains('edit-btn') ? 'Editar' : 'Excluir'}, ID: ${id}`);

            // --- LÓGICA DE EDIÇÃO ---
            if (button.classList.contains('edit-btn')) {
                const url = `/plano-contas/${id}/edit`;

                fetch(url, { headers: { 'Accept': 'application/json' } })
                .then(response => response.json())
                .then(data => {
                    console.log("Dados recebidos para edição:", data); // LOG DE DEPURAÇÃO 3
                    
                    resetModalToCreate();
                    modalTitle.innerText = "Editar Conta Contábil";
                    form.action = `/plano-contas/${id}`;

                    if (!form.querySelector('input[name="_method"]')) {
                        const hiddenMethod = document.createElement('input');
                        hiddenMethod.type = 'hidden';
                        hiddenMethod.name = '_method';
                        hiddenMethod.value = 'PUT';
                        form.appendChild(hiddenMethod);
                    } else {
                        form.querySelector('input[name="_method"]').value = 'PUT';
                    }

                    form.querySelector('[name="code"]').value = data.code;
                    form.querySelector('[name="name"]').value = data.name;
                    form.querySelector('[name="type"]').value = data.type;
                    $(form.querySelector('[name="parent_id"]')).val(data.parent_id).trigger('change');

                    console.log("Modal pronto para ser exibido."); // LOG DE DEPURAÇÃO 4
                    modal.show();
                });
            }

            // --- LÓGICA DE EXCLUSÃO ---
            if (button.classList.contains('delete-btn')) {
                const name = button.dataset.name;
                const url = `/plano-contas/${id}`;

                Swal.fire({ /* ... seu código do Swal ... */ }).then(function (result) {
                    if (result.isConfirmed) {
                        // ... sua lógica de fetch para DELETE ...
                    }
                });
            }
        });
    });
</script>
@endsection
