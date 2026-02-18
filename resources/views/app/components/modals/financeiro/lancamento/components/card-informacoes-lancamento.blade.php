<div class="card mb-xl-10 border border-gray-300 border-active active ">
    <div class="card-header">
        <h3 class="card-title">Informações do lançamento</h3>
    </div>
    <div class="card-body px-10">
        <!--begin::Form-->
        <!--begin::Input group - Assign & Due Date-->
        <div class="row g-9 mb-8">
            <!--begin::Col-->
            <x-tenant-select name="fornecedor_id" id="fornecedor_id" label="Fornecedor" data-label-default="Fornecedor"
                placeholder="Selecione um fornecedor" data-placeholder-default="Selecione um fornecedor"
                :minimumResultsForSearch="0" dropdown-parent="{{ $dropdownParent ?? '#Dm_modal_financeiro' }}" labelSize="fs-6"
                class="col-md-3">
                @if (isset($fornecedores))
                    @foreach ($fornecedores as $fornecedor)
                        <option value="{{ $fornecedor->id }}"
                            data-natureza="{{ $fornecedor->natureza }}"
                            {{ old('fornecedor_id') == $fornecedor->id ? 'selected' : '' }}>
                            {{ $fornecedor->nome }}
                        </option>
                    @endforeach
                @endif
            </x-tenant-select>
            <!--end::Col-->
            <!--begin::Col-->
            <x-tenant-date name="data_competencia" label="Data de competência" placeholder="Informe a data" required 
                class="col-md-2" />
            <!--end::Col-->
            <!--begin::Input group - Target Title-->
            <x-tenant-input name="descricao" id="descricao" label="Descrição" placeholder="Informe a descricão" required
                class="col-md-5" showSuggestionStar="true" />

            <!--end::Input group - Target Title-->
            <!--begin::Input group - Valor-->
            <x-tenant-currency name="valor" id="valor2" label="Valor" placeholder="0,00"
                tooltip="Informe o valor da despesa" class="col-md-2" required showSuggestionStar="true" />
            <!--end::Input group - Valor-->
        </div>
        <!--begin::Input group - Assign & Due Date-->
        <div class="row g-9 mb-8">
            <!--begin::Col-->
            <x-tenant-select name="entidade_id" id="entidade_id" label="Entidade Financeira" required :hideSearch="true"
                dropdown-parent="{{ $dropdownParent ?? '#Dm_modal_financeiro' }}" class="col-md-3">
                @if (isset($entidadesBanco) && $entidadesBanco->isNotEmpty())
                    @foreach ($entidadesBanco as $entidade)
                        <option value="{{ $entidade->id }}"
                            data-kt-select2-icon="{{ $entidade->bank->logo_url ?? asset('tenancy/assets/media/svg/bancos/default.svg') }}"
                            data-nome="{{ $entidade->nome }}" data-origem="Banco">
                            {{ $entidade->agencia }} - {{ $entidade->conta }}
                        </option>
                    @endforeach
                @endif
                @if (isset($entidadesCaixa) && $entidadesCaixa->isNotEmpty())
                    @foreach ($entidadesCaixa as $entidade)
                        <option value="{{ $entidade->id }}"
                            data-kt-select2-icon="{{ url('/tenancy/assets/media/svg/bancos/caixa.svg') }}"
                            data-nome="{{ $entidade->nome }}" data-origem="Caixa">
                            {{ $entidade->nome }}
                        </option>
                    @endforeach
                @endif
            </x-tenant-select>
            <!--end::Col-->
            <x-tenant-select name="lancamento_padrao_id" id="lancamento_padraos_id" label="Categoria"
                placeholder="Escolha um Lançamento..." required :allowClear="true" :minimumResultsForSearch="0"
                dropdown-parent="{{ $dropdownParent ?? '#Dm_modal_financeiro' }}" labelSize="fs-6" class="col-md-5"
                showSuggestionStar="true">
                @foreach ($lps as $lp)
                    <option value="{{ $lp->id }}" data-description="{{ $lp->description }}"
                        data-type="{{ $lp->type }}"
                        data-tipo-label="{{ $lp->type === 'entrada' ? 'Receita' : 'Despesa' }}"
                        data-tipo-color="{{ $lp->type === 'entrada' ? 'success' : 'danger' }}">{{ $lp->id }} -
                        {{ $lp->description }}</option>
                @endforeach
            </x-tenant-select>
            <!--begin::Col-->
            <x-tenant-select name="cost_center_id" id="cost_center_id" label="Centro de Custo" :allowClear="true"
                placeholder="Selecione um centro de custo" :minimumResultsForSearch="0" 
                dropdown-parent="{{ $dropdownParent ?? '#Dm_modal_financeiro' }}" labelSize="fs-6"
                class="col-md-4" showSuggestionStar="true">
                @if (isset($centrosAtivos))
                    @foreach ($centrosAtivos as $centro)
                        <option value="{{ $centro->id }}">
                            {{ $centro->code }} - {{ $centro->name }}
                        </option>
                    @endforeach
                @endif
            </x-tenant-select>
            <!--end::Col-->

        </div>
        <!--end::Input group - Assign & Due Date-->
        <!--begin::Input group-->
        <div class="row g-9 mb-5">
            <!--begin::Col-->
            <x-tenant-select name="tipo_documento" id="tipo_documento" label="Forma de pagamento"
                placeholder="Selecione uma forma de pagamento" required :allowClear="true" :minimumResultsForSearch="0"
                dropdown-parent="{{ $dropdownParent ?? '#Dm_modal_financeiro' }}" labelSize="fs-6" class="col-md-4"
                showSuggestionStar="true">
                @if (isset($formasPagamento))
                    @foreach ($formasPagamento as $formaPagamento)
                        <option value="{{ $formaPagamento->codigo }}"
                            {{ old('tipo_documento') == $formaPagamento->codigo ? 'selected' : '' }}>
                            {{ $formaPagamento->id }} - {{ $formaPagamento->nome }}
                        </option>
                    @endforeach
                @endif
            </x-tenant-select>
            <!--end::Col-->
            <x-tenant-input name="numero_documento" id="numero_documento" label="Número do Documento"
                placeholder="1234567890" type="text" class="col-md-4" />


        </div>
        <!--end::Input group-->
    </div>
</div>
