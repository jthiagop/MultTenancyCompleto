{{-- STEP 1: Detalhes --}}
<div class="current" data-kt-stepper-element="content">
    <div class="fv-row">
        <label class="form-label required">Tipo de PtAm</label>
        <div class="row">
            @foreach($categories as $value => $opt)
                <div class="col-4 mb-5">
                    <label class="d-flex flex-stack cursor-pointer p-3 border rounded">
                        <span class="d-flex align-items-center">
                            <span class="symbol symbol-50px me-4">
                                <span class="symbol-label bg-light-{{ $opt['bg'] }}">
                                    @include("components.icons.{$opt['icon']}")
                                </span>
                            </span>
                            <div>
                                <div class="fw-bold">{{ $opt['title'] }}</div>
                                <div class="text-muted fs-7">{{ $opt['desc'] }}</div>
                            </div>
                        </span>
                        <input type="radio"
                               name="category"
                               value="{{ $value }}"
                               class="form-check-input">
                    </label>
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- STEP 2: Proprietário --}}
<div data-kt-stepper-element="content">
    <div class="fv-row mb-5">
        <label class="form-label required">Proprietário</label>
        <div class="d-flex gap-3">
            <div class="form-check">
                <input type="radio" name="owner_type" value="atual" id="ownerAtual" class="form-check-input">
                <label for="ownerAtual" class="form-check-label">Atual</label>
            </div>
            <div class="form-check">
                <input type="radio" name="owner_type" value="terceiro" id="ownerTerceiro" class="form-check-input">
                <label for="ownerTerceiro" class="form-check-label">Terceiro</label>
            </div>
        </div>
    </div>

    <div id="form_owner_atual" class="owner-form d-none">
        <div class="fv-row mb-3">
            <input name="nome_proprietario_atual" type="text" class="form-control" placeholder="Nome completo">
            <label class="form-label">Nome completo</label>
        </div>
        {{-- outros campos do atual… --}}
    </div>
    <div id="form_owner_terceiro" class="owner-form d-none">
        <div class="fv-row mb-3">
            <input name="nome_terceiro" type="text" class="form-control" placeholder="Nome completo">
            <label class="form-label">Nome completo</label>
        </div>
        {{-- campos de RG, CPF, telefone… --}}
    </div>
</div>

{{-- STEP 3: Dados do DB --}}
<div data-kt-stepper-element="content">
    <div class="fv-row mb-10">
        <label class="form-label required">Database Name</label>
        <input name="dbname" type="text" class="form-control form-control-solid" value="master_db">
    </div>
    <div class="fv-row">
        <label class="form-label required">Database Engine</label>
        <select name="dbengine" class="form-select form-select-solid">
            <option value="mysql">MySQL</option>
            <option value="firebase">Firebase</option>
            <option value="dynamodb">DynamoDB</option>
        </select>
    </div>
</div>

{{-- STEP 4: Confirmação --}}
<div data-kt-stepper-element="content" class="text-center">
    <h3 class="mb-3">Pronto para gerar o PtAm?</h3>
    <p class="text-muted">Revise os dados e clique em “Enviar”.</p>
</div>

{{-- Actions --}}
<div class="d-flex flex-stack pt-10">
    <button type="button" class="btn btn-light-primary" data-kt-stepper-action="previous">Voltar</button>
    <button type="button" class="btn btn-primary d-none" data-kt-stepper-action="submit">Enviar</button>
    <button type="button" class="btn btn-primary" data-kt-stepper-action="next">Continuar</button>
</div>
