<!--begin::Row Avatar + Dados-->
<div class="row mb-5">
    <!--begin::Col Avatar-->
    <div class="col-md-3">
        <x-tenant.photo-input name="avatar" label="Fotografia" size="125px" hint="Tipos: png, jpg, jpeg." />
    </div>
    <!--end::Col Avatar-->

    <!--begin::Col Dados-->
    <div class="col-md-9">
        <!--begin::Input group - Nome-->
        <div class="d-flex flex-column mb-5 fv-row">
            <x-tenant-input name="nome" label="Nome Completo" placeholder="Digite o nome do membro" required="true"
                class="" />
        </div>
        <!--end::Input group-->

        <!--begin::Row Etapa e Data Nascimento-->
        <div class="row g-5">
            <!--begin::Col-->
            <div class="col-md-6 fv-row">
                <x-tenant-select name="current_stage_id" label="Etapa de Formação" placeholder="Selecione a etapa"
                    :hideSearch="true" :required="true" class="">
                    @foreach ($formationStages as $stage)
                        <option value="{{ $stage->id }}">{{ $stage->name }}</option>
                    @endforeach
                </x-tenant-select>
            </div>
            <!--end::Col-->
            <!--begin::Col-->
            <div class="col-md-6 fv-row">
                <x-tenant-date name="data_nascimento" label="Data de Nascimento"
                    placeholder="Informe a data de nascimento" required="true" class="" />
            </div>
            <!--end::Col-->
        </div>
        <!--end::Row-->
    </div>
    <!--end::Col Dados-->
</div>

<!--begin::Row ID Ordem e Província-->
<div class="row mb-8">
    <div class="col-md-12">
        <div class="row g-5">
            <!--begin::Col-->
            <div class="col-md-4 fv-row">
                <x-tenant-input name="funcao" label="ID da Ordem" placeholder="Digite código" class="" />
            </div>
            <!--end::Col-->
            <!--begin::Col-->
            <div class="col-md-4 fv-row">
                <x-tenant-input name="provincia" label="ID da Província" placeholder="Digite código" class="" />
            </div>
            <!--end::Col-->
            <!--begin::Col-->
            <div class="col-md-4 fv-row">
                <x-tenant-input name="cpf" label="CPF" placeholder="Digite o CPF" class="" />
            </div>
            <!--end::Col-->
        </div>
    </div>
</div>
<!--end::Row-->



<!--begin::Etapas da formação-->
<div id="kt_modal_member_formativa_wrapper" class="mb-8" style="display: none;">
    <div class="card card-bordered">
        <!--begin::Card header-->
        <div class="modal-header collapsible cursor-pointer rotate" data-bs-toggle="collapse"
            data-bs-target="#kt_card_etapas_formativas">
            <h3 class="card-title fw-bold">Etapas Formativas</h3>
            <div class="card-toolbar rotate-180">
                <i class="bi bi-chevron-down text-primary rotate-180"></i>
            </div>
        </div>
        <!--end::Card header-->

        <!--begin::Card body-->
        <div id="kt_card_etapas_formativas" class="collapse show">
            <div class="card-body">
                <!--begin::Etapas dinâmicas-->
                @foreach ($formationStages as $index => $stage)
                    <div class="formation-stage-block" data-stage-id="{{ $stage->id }}"
                        data-stage-order="{{ $stage->sort_order }}" data-stage-slug="{{ $stage->slug }}"
                        style="display: none;">
                        <!--begin::Header da Etapa-->
                        <div class="d-flex align-items-center justify-content-between mb-5">
                            <div class="d-flex align-items-center">
                                <span class="badge badge-lg badge-primary me-3">{{ $stage->sort_order }}</span>
                                <h4 class="fw-bold mb-0">{{ $stage->name }}</h4>
                            </div>
                            <!--begin::Switch Período Atual-->
                            <div class="form-check form-switch form-check-custom form-check-solid">
                                <input class="form-check-input is-current-checkbox" type="checkbox"
                                    name="stages[{{ $stage->slug }}][is_current]" id="is_current_{{ $stage->slug }}"
                                    data-stage-id="{{ $stage->id }}" data-stage-order="{{ $stage->sort_order }}"
                                    value="1" />
                                <label class="form-check-label fw-semibold text-gray-700"
                                    for="is_current_{{ $stage->slug }}">
                                    Período Atual
                                </label>
                            </div>
                            <!--end::Switch Período Atual-->
                        </div>
                        <!--end::Header da Etapa-->

                        <!--begin::Row Período de Formação-->
                        <div class="row mb-5">
                            <div class="col-md-12">
                                <div class="row g-5">
                                    <!--begin::Col Data Inicial-->
                                    <div class="col-md-3 fv-row">
                                        <x-tenant-date name="stages[{{ $stage->slug }}][start_date]"
                                            label="Data Inicial" placeholder="dd/mm/aaaa" class="" />
                                    </div>
                                    <!--end::Col-->
                                    <!--begin::Col Data Final-->
                                    <div class="col-md-3 fv-row end-date-wrapper"
                                        data-stage-slug="{{ $stage->slug }}">
                                        <x-tenant-date name="stages[{{ $stage->slug }}][end_date]" label="Data Final"
                                            placeholder="dd/mm/aaaa" class="" />
                                    </div>
                                    <!--end::Col-->
                                    <!--begin::Col Local-->
                                    <div class="col-md-6 fv-row">
                                        <x-tenant-select name="stages[{{ $stage->slug }}][company_id]" label="Local"
                                            placeholder="Selecione o local" dropdownParent="#kt_modal_member"
                                            class="">
                                            @foreach ($companies as $company)
                                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                                            @endforeach
                                        </x-tenant-select>
                                    </div>
                                    <!--end::Col-->
                                </div>
                            </div>
                        </div>
                        <!--end::Row-->
                        @if (!$loop->last)
                            <div class="separator separator-dashed my-6 stage-separator"></div>
                        @endif
                    </div>
                @endforeach
                <!--end::Etapas dinâmicas-->
            </div>
            
            <!--begin::Função Religiosa (apenas para Votos Perpétuos)-->
            <div id="kt_religious_role_wrapper" class="card-body pt-0" style="display: none;">
                <div class="separator separator-dashed mb-6"></div>
                
                <!--begin::Heading-->
                <div class="mb-3">
                    <!--begin::Label-->
                    <label class="d-flex align-items-center fs-5 fw-semibold">
                        <span>Função na Ordem</span>
                        <span class="ms-1" data-bs-toggle="tooltip"
                            title="Selecione a função do religioso na Ordem">
                            <i class="ki-duotone ki-information fs-7"><span class="path1"></span><span
                                    class="path2"></span><span class="path3"></span></i>
                        </span>
                    </label>
                    <!--end::Label-->

                    <!--begin::Description-->
                    <div class="fs-7 fw-semibold text-muted">Disponível apenas para religiosos com Votos Perpétuos</div>
                    <!--end::Description-->
                </div>
                <!--end::Heading-->

                <!--begin::Radio group-->
                <div class="btn-group w-100 w-lg-50" data-kt-buttons="true" data-kt-buttons-target="[data-kt-button]">
                    <!--begin::Radio-->
                    <label class="btn btn-outline btn-color-muted btn-active-success" data-kt-button="true">
                        <!--begin::Input-->
                        <input class="btn-check" type="radio" name="religious_role_id" value="1" />
                        <!--end::Input-->
                        Irmão
                    </label>
                    <!--end::Radio-->

                    <!--begin::Radio-->
                    <label class="btn btn-outline btn-color-muted btn-active-success active" data-kt-button="true">
                        <!--begin::Input-->
                        <input class="btn-check" type="radio" name="religious_role_id" checked="checked"
                            value="2" />
                        <!--end::Input-->
                        Presbítero
                    </label>
                    <!--end::Radio-->
                </div>
                <!--end::Radio group-->
            </div>
            <!--end::Função Religiosa-->
        </div>
        <!--end::Card body-->
    </div>
</div>
<!--end::Etapas da formação-->

<!--begin::Endereço de Origem-->
<div class="card card-flush border mb-8">
    <!--begin::Card header-->
    <div class="modal-header collapsible cursor-pointer rotate" data-bs-toggle="collapse"
        data-bs-target="#kt_card_endereco_origem">
        <h3 class="card-title fw-bold"> Endereço de Origem </h3>

        <div class="card-toolbar rotate-180">
            <i class="bi bi-chevron-down text-primary rotate-180"></i>
        </div>
    </div>
    <!--end::Card header-->

    <!--begin::Card body-->
    <div id="kt_card_endereco_origem" class="collapse show">
        <div class="card-body">
            <!--begin::Input group CEP e Bairro-->
            <div class="row g-6 mb-6">
                <div class="col-md-3 fv-row">
                    <x-tenant-input name="cep" label="CEP" placeholder="00000-000" class="" />
                </div>
                <div class="col-md-9 fv-row">
                    <x-tenant-input name="bairro" label="Bairro" placeholder="Digite o bairro" class="" />
                </div>
            </div>
            <!--end::Input group-->

            <!--begin::Input group Rua e Número-->
            <div class="row g-6 mb-6">
                <div class="col-md-8 fv-row">
                    <x-tenant-input name="logradouro" id="logradouro" label="Rua" placeholder="Digite a rua"
                        class="" />
                </div>
                <div class="col-md-4 fv-row">
                    <x-tenant-input name="numero" id="numero" label="Número" placeholder="Nº" class="" />
                </div>
            </div>
            <!--end::Input group-->

            <!--begin::Input group Cidade e Estado-->
            <div class="row g-6">
                <div class="col-md-6 fv-row">
                    <x-tenant-input name="localidade" id="localidade" label="Cidade" placeholder="Digite a cidade"
                        class="" />
                </div>
                <div class="col-md-6 fv-row">
                    <x-tenant-select name="uf" id="uf" label="Estado" placeholder="Selecione o estado"
                        dropdownParent="#kt_modal_member" class="">
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
                        <option value="EX">Estrangeiro</option>
                    </x-tenant-select>
                </div>
            </div>
            <!--end::Input group-->
        </div>
    </div>
    <!--end::Card body-->
</div>
<!--end::Endereço de Origem-->

<!--begin::Observações-->
<x-tenant-textarea name="observacoes" label="Observações" placeholder="Digite observações sobre o membro"
    rows="3" class="mb-8" />
<!--end::Observações-->

<!--begin::Switch Disponibilidade-->
<x-tenant-switch name="disponivel_todas_casas" label="Disponível em Todas as Casas?"
    description="As informações básicas serão exibidas somente nas casas onde o frade passou." :checked="true"
    labelOn="Sim" labelOff="Não" class="mb-8" />
<!--end::Switch Disponibilidade-->
