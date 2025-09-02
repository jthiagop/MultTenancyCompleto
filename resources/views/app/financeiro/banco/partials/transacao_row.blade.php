<tr>
    <td>{{ $transacao->id }}</td>
    <td>{{ \Carbon\Carbon::parse($transacao->data_competencia)->format('d/m/y') }}</td>
    <td>{{ $transacao->tipo_documento }}</td>
    <td>
        {!! $transacao->comprovacao_fiscal
            ? '<i class="fas fa-check-circle text-success" title="Comprovação Fiscal"></i>'
            : '<i class="bi bi-x-circle-fill text-danger" title="Sem Comprovação Fiscal"></i>' !!}
    </td>
    <td>
        <div class="fw-bold">{{ $transacao->descricao }}</div>
        <div class="text-muted small">
            {{ optional($transacao->lancamentoPadrao)->description }}
        </div>
    </td>
    <td>
        <div class="badge fw-bold {{ $transacao->tipo == 'entrada' ? 'badge-success' : 'badge-danger' }}">
            {{ $transacao->tipo }}
        </div>
    </td>
    <td>R$ {{ number_format($transacao->valor, 2, ',', '.') }}</td>
    <td class="text-center">{{ $transacao->origem }}</td>
    <td class="text-center">
        <!--begin::Anexos-->
        <div class="symbol-group symbol-hover fs-8">
            @php
                $anexos = $transacao->modulos_anexos->take(3);
                $remainingAnexos = $transacao->modulos_anexos->count() - 3;
                $icons = [
                    'pdf' => ['icon' => 'bi-file-earmark-pdf-fill', 'color' => 'text-danger'],
                    'jpg' => ['icon' => 'bi-file-earmark-image-fill', 'color' => 'text-warning'],
                    'jpeg' => ['icon' => 'bi-file-earmark-image-fill', 'color' => 'text-primary'],
                    'png' => ['icon' => 'bi-file-earmark-image-fill', 'color' => 'text-warning'],
                    'doc' => ['icon' => 'bi-file-earmark-word-fill', 'color' => 'text-info'],
                    'docx' => ['icon' => 'bi-file-earmark-word-fill', 'color' => 'text-info'],
                    'xls' => ['icon' => 'bi-file-earmark-spreadsheet-fill', 'color' => 'text-warning'],
                    'xlsx' => ['icon' => 'bi-file-earmark-spreadsheet-fill', 'color' => 'text-warning'],
                    'txt' => ['icon' => 'bi-file-earmark-text-fill', 'color' => 'text-muted'],
                ];
                $defaultIcon = ['icon' => 'bi-file-earmark-fill', 'color' => 'text-secondary'];
            @endphp
            @foreach ($anexos as $anexo)
                @php
                    $extension = pathinfo($anexo->nome_arquivo ?? '', PATHINFO_EXTENSION);
                    $iconData = $icons[strtolower($extension)] ?? $defaultIcon;
                @endphp
                <div class="symbol symbol-30px symbol-circle bg-light-primary text-primary d-flex justify-content-center align-items-center"
                    data-bs-toggle="tooltip" title="{{ $anexo->nome_arquivo }}">
                    <a href="{{ route('file', ['path' => $anexo->caminho_arquivo]) }}"
                        target="_blank" class="text-decoration-none">
                        <i class="bi {{ $iconData['icon'] }} {{ $iconData['color'] }} fs-3"></i>
                    </a>
                </div>
            @endforeach
            @if ($remainingAnexos > 0)
                <div class="symbol symbol-25px symbol-circle" data-bs-toggle="tooltip"
                    title="Mais {{ $remainingAnexos }} anexos">
                    <a href="{{ route('banco.edit', $transacao->id) }}">
                        <span class="symbol-label fs-8 fw-bold bg-light text-gray-800">
                            +{{ $remainingAnexos }}
                        </span>
                    </a>
                </div>
            @endif
            @if ($transacao->modulos_anexos->isEmpty())
                <div class="symbol symbol-25px symbol-circle text-center"
                    data-bs-toggle="tooltip" title="Nenhum anexo disponível">
                    <span class="symbol-label fs-8 fw-bold bg-light text-gray-800">
                        {{ 0 }}
                    </span>
                </div>
            @endif
        </div>
        <!--end::Anexos-->
    </td>
    <td class="text-end">
        <div class="d-flex justify-content-end align-items-center">
            <a href="{{ route('banco.edit', $transacao->id) }}"
                class="btn btn-icon btn-active-light-primary w-30px h-30px ms-auto me-5">
                <span class="svg-icon svg-icon-3">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path opacity="0.3"
                            d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z"
                            fill="currentColor" />
                        <path
                            d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z"
                            fill="currentColor" />
                    </svg>
                </span>
            </a>
        </div>
    </td>
</tr>