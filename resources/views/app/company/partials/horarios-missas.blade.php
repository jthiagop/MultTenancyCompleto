<div id="kt_horarios_missas_dias_repeater">
    <form method="POST" action="{{ route('company.update', $company->id) }}" id="kt_horarios_missas_form" class="form">
        @csrf
        @method('PUT')
        <!-- Campo hidden para indicar que estamos enviando horários de missas -->
        <input type="hidden" name="updating_horarios_missas" value="1">
        <div class="card card-flush">
            <!--begin::Card header-->
            <div class="card-header">
                <div class="card-title">
                    <h2>Horários de Missas</h2>
                </div>
            </div>
            <!--end::Card header-->
            <!--begin::Card body-->
            <div class="card-body pt-0">
                <!--begin::Repeater Principal (Dias)-->
                <!-- Campo hidden para garantir que dias[] seja enviado mesmo quando vazio -->
                <input type="hidden" name="dias" value="" id="dias_empty_indicator">
                <div data-repeater-list="dias">
                    @if (isset($company) && $company->horariosMissas->count() > 0)
                        @php
                            $horariosPorDia = $company->horariosMissas->groupBy('dia_semana');
                        @endphp
                        @foreach ($horariosPorDia as $dia => $horarios)
                            <div data-repeater-item class="mb-5">
                                <div class="border border-gray-300 border-dashed rounded p-4">
                                    <!--begin::Linha do Dia-->
                                    <div class="d-flex align-items-start gap-3 mb-3">
                                        <!--begin::Select Dia-->
                                        <div style="width: 200px;">
                                            <select class="form-select form-select-sm select2 horario-missa-dia"
                                                name="dia_semana" data-kt-horarios-missas="dia_semana"
                                                data-placeholder="Selecione o dia">
                                                <option></option>
                                                <option value="domingo" {{ $dia == 'domingo' ? 'selected' : '' }}>
                                                    Domingo</option>
                                                <option value="segunda" {{ $dia == 'segunda' ? 'selected' : '' }}>
                                                    Segunda</option>
                                                <option value="terca" {{ $dia == 'terca' ? 'selected' : '' }}>
                                                    Terça</option>
                                                <option value="quarta" {{ $dia == 'quarta' ? 'selected' : '' }}>
                                                    Quarta</option>
                                                <option value="quinta" {{ $dia == 'quinta' ? 'selected' : '' }}>
                                                    Quinta</option>
                                                <option value="sexta" {{ $dia == 'sexta' ? 'selected' : '' }}>
                                                    Sexta</option>
                                                <option value="sabado" {{ $dia == 'sabado' ? 'selected' : '' }}>
                                                    Sábado</option>
                                            </select>
                                        </div>
                                        <!--end::Select Dia-->

                                        <!--begin::Repeater de Horários-->
                                        <div class="flex-grow-1">
                                            <div class="horarios-repeater d-flex align-items-start gap-2">
                                                <!--begin::Botão Adicionar Horário-->
                                                <div class="mt-1">
                                                    <button type="button" data-repeater-create
                                                        class="btn btn-sm btn-icon btn-light-primary"
                                                        data-bs-toggle="tooltip" title="Adicionar mais um horário">
                                                        <i class="fa-solid fa-plus fs-6"></i>
                                                    </button>
                                                </div>
                                                <!--end::Botão Adicionar Horário-->

                                                <div data-repeater-list="horarios" class="d-flex flex-wrap gap-2">
                                                    @foreach ($horarios as $horario)
                                                        <div data-repeater-item class="d-flex align-items-center gap-2">
                                                            <!--begin::Input Horário-->
                                                            <div class="input-group" style="width: 150px;"
                                                                data-td-target-input="nearest"
                                                                data-td-target-toggle="nearest"
                                                                id="kt_td_picker_{{ $dia }}_{{ $loop->index }}">
                                                                <input type="text"
                                                                    class="form-control form-control-sm horario-input"
                                                                    name="horario"
                                                                    value="{{ \Carbon\Carbon::parse($horario->horario)->format('H:i') }}"
                                                                    data-td-target="#kt_td_picker_{{ $dia }}_{{ $loop->index }}"
                                                                    placeholder="00:00" />
                                                                <span class="input-group-text"
                                                                    data-td-target="#kt_td_picker_{{ $dia }}_{{ $loop->index }}"
                                                                    data-td-toggle="datetimepicker">
                                                                    <i class="fa-solid fa-clock fs-5"></i>
                                                                </span>
                                                            </div>
                                                            <!--end::Input Horário-->

                                                            <!--begin::Botão Remover Horário-->
                                                            <button type="button" data-repeater-delete
                                                                class="btn btn-sm btn-icon btn-light-danger"
                                                                data-bs-toggle="tooltip" title="Remover este horário">
                                                                <i class="fa-solid fa-times fs-5"></i>
                                                            </button>
                                                            <!--end::Botão Remover Horário-->
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        <!--end::Repeater de Horários-->

                                        <!--begin::Botão Remover Dia-->
                                        <button type="button" data-repeater-delete
                                            class="btn btn-sm btn-icon btn-light-danger" data-bs-toggle="tooltip"
                                            title="Remover este dia">
                                            <i class="fa-solid fa-trash fs-5"></i>
                                        </button>
                                        <!--end::Botão Remover Dia-->
                                    </div>
                                    <!--end::Linha do Dia-->
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div data-repeater-item class="mb-5">
                            <div class="border border-gray-300 border-dashed rounded p-4">
                                <!--begin::Linha do Dia-->
                                <div class="d-flex align-items-start gap-3 mb-3">
                                    <!--begin::Select Dia-->
                                    <div style="width: 200px;">
                                        <select class="form-select horario-missa-dia" name="dia_semana"
                                            data-kt-horarios-missas="dia_semana" data-placeholder="Selecione o dia">
                                            <option></option>
                                            <option value="domingo">Domingo</option>
                                            <option value="segunda">Segunda</option>
                                            <option value="terca">Terça</option>
                                            <option value="quarta">Quarta</option>
                                            <option value="quinta">Quinta</option>
                                            <option value="sexta">Sexta</option>
                                            <option value="sabado">Sábado</option>
                                        </select>
                                    </div>
                                    <!--end::Select Dia-->

                                    <!--begin::Repeater de Horários-->
                                    <div class="flex-grow-1">
                                        <div class="horarios-repeater d-flex align-items-start gap-2">
                                            <!--begin::Botão Adicionar Horário-->
                                            <div class="mt-0">
                                                <button type="button" data-repeater-create
                                                    class="btn btn-sm btn-icon btn-light-primary"
                                                    data-bs-toggle="tooltip" title="Adicionar mais um horário">
                                                    <i class="fa-solid fa-plus fs-6"></i>
                                                </button>
                                            </div>
                                            <!--end::Botão Adicionar Horário-->

                                            <div data-repeater-list="horarios" class="d-flex flex-wrap gap-2">
                                                <div data-repeater-item class="d-flex align-items-center gap-2">
                                                    <!--begin::Input Horário-->
                                                    <div class="input-group" style="width: 150px;"
                                                        data-td-target-input="nearest" data-td-target-toggle="nearest"
                                                        id="kt_td_picker_template">
                                                        <input type="text" class="form-control horario-input"
                                                            name="horario" data-td-target="#kt_td_picker_template"
                                                            placeholder="00:00" />
                                                        <span class="input-group-text"
                                                            data-td-target="#kt_td_picker_template"
                                                            data-td-toggle="datetimepicker">
                                                            <i class="fa-solid fa-clock fs-5"></i>
                                                        </span>
                                                    </div>
                                                    <!--end::Input Horário-->

                                                    <!--begin::Botão Remover Horário-->
                                                    <button type="button" data-repeater-delete
                                                        class="btn btn-sm btn-icon btn-light-danger"
                                                        data-bs-toggle="tooltip" title="Remover este horário">
                                                        <i class="fa-solid fa-times fs-5"></i>
                                                    </button>
                                                    <!--end::Botão Remover Horário-->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end::Repeater de Horários-->

                                    <!--begin::Botão Remover Dia-->
                                    <div class="mt-1">
                                        <button type="button" data-repeater-delete
                                            class="btn btn-sm btn-icon btn-light-danger" data-bs-toggle="tooltip"
                                            title="Remover este dia">
                                            <i class="fa-solid fa-trash fs-5"></i>
                                        </button>
                                    </div>
                                    <!--end::Botão Remover Dia-->
                                </div>
                                <!--end::Linha do Dia-->
                            </div>
                        </div>
                    @endif
                </div>

                <!--begin::Botão Adicionar Novo Dia-->
                <div class="form-group mt-5">
                    <button type="button" data-repeater-create class="btn btn-sm btn-light-primary btn-add-dia"
                        data-bs-toggle="tooltip" title="Adicionar novo dia">
                        <i class="fa-solid fa-plus me-2"></i>
                        Adicionar Dia
                    </button>
                </div>
                <!--end::Botão Adicionar Novo Dia-->
            </div>
            <!--end::Repeater Principal-->
            <!--end::Card body-->
            <!--begin::Card footer-->
            <div class="card-footer d-flex justify-content-end align-items-center gap-3 py-6 px-9">
                <!--begin::Input Intervalo-->
                <div class="d-flex align-items-center gap-2">
                    <label for="intervalo_padrao" class="fw-semibold text-gray-600">Intervalo:</label>
                    <div class="input-group" style="width: 150px;" data-td-target-input="nearest"
                        data-td-target-toggle="nearest" id="kt_td_picker_intervalo">
                        <input type="text" class="form-control form-control-sm" name="intervalo_padrao" id="intervalo_padrao"
                            data-td-target="#kt_td_picker_intervalo" placeholder="00:00"
                            value="{{ isset($company) && $company->horariosMissas->first() ? \Carbon\Carbon::createFromTime(0, $company->horariosMissas->first()->intervalo ?? 90)->format('H:i') : '01:30' }}" />
                        <span class="input-group-text" data-td-target="#kt_td_picker_intervalo"
                            data-td-toggle="datetimepicker">
                            <i class="fa-solid fa-clock fs-5"></i>
                        </span>
                    </div>
                </div>
                <!--end::Input Intervalo-->

                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="fa-solid fa-floppy-disk me-2"></i>
                    Salvar Horários
                </button>
            </div>
            <!--end::Card footer-->

        </div>
    </form>
</div>
