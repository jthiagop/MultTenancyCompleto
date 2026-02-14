@forelse ($documentos as $doc)
    @php
        $dataFormatada = $doc->created_at ? \Carbon\Carbon::parse($doc->created_at)->format('d/m/Y H:i') : 'Data não informada';
        $userName = $doc->user_name ?? 'Usuário';

        // Detectar canal de origem
        $canalOrigem = $doc->canal_origem ?? 'upload';

        // Determinar ícone
        $iconClass = 'fa-file';
        $iconColor = 'text-primary';

        if ($doc->mime_type === 'application/pdf') {
            $iconClass = 'fa-file-pdf';
            $iconColor = 'text-danger';
        } elseif (Str::startsWith($doc->mime_type ?? '', 'image/')) {
            $iconClass = 'fa-file-image';
            $iconColor = 'text-primary';
        }

        // Título formatado
        $tituloFormatado = $doc->nome_arquivo;
        if ($tituloFormatado) {
            $tituloFormatado = ucfirst(strtolower($tituloFormatado));
        }
        $tituloTruncado = Str::limit($tituloFormatado, 35);
        
        $statusClass = $doc->status === \App\Enums\StatusDomusDocumento::PROCESSADO ? 'success' : 'warning';
    @endphp

    <div class="pending-document-item" 
         data-document-id="{{ $doc->id }}" 
         data-file-name="{{ $doc->nome_arquivo }}"
         onclick="if(typeof window.selectDocumentFromDatabase === 'function') window.selectDocumentFromDatabase({{ $doc->id }})">
        
        <div class="d-flex align-items-center p-3 mb-3 bg-light-primary rounded cursor-pointer document-item position-relative">
            
            {{-- Ícone do Arquivo --}}
            <div class="symbol symbol-40px me-3">
                <i class="fa-solid {{ $iconClass }} fs-2x {{ $iconColor }}"></i>
                
                {{-- Badge do Canal --}}
                <span class="position-absolute top-0 start-0 translate-middle badge badge-circle badge-light-{{ $canalOrigem === 'whatsapp' ? 'success' : 'primary' }} w-15px h-15px p-0" 
                      style="left: 5px !important; top: 5px !important;"
                      title="Via {{ ucfirst($canalOrigem) }}">
                    @if($canalOrigem === 'whatsapp')
                        <i class="fa-brands fa-whatsapp fs-9 text-success"></i>
                    @else
                        <i class="fa-solid fa-upload fs-9 text-primary"></i>
                    @endif
                </span>
            </div>

            <div class="flex-grow-1 overflow-hidden">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <div class="fw-bold text-gray-800 text-nowrap text-truncate document-title-popover"
                         style="max-width: 100%;"
                         data-bs-toggle="popover"
                         data-bs-trigger="hover"
                         data-bs-html="true"
                         data-bs-placement="top"
                         title="Informações do Documento"
                         data-bs-content="
                            <div class='popover-content-document fs-7'>
                                <div class='mb-1'><strong>Nome:</strong> {{ $doc->nome_arquivo ?? '-' }}</div>
                                @if($doc->tipo_documento) <div class='mb-1'><strong>Tipo:</strong> {{ $doc->tipo_documento }}</div> @endif
                                @if($doc->estabelecimento_nome) <div class='mb-1'><strong>Fornecedor:</strong> {{ $doc->estabelecimento_nome }}</div> @endif
                                @if($doc->valor_total) <div class='mb-1'><strong>Valor:</strong> R$ {{ number_format((float)$doc->valor_total, 2, ',', '.') }}</div> @endif
                                <div class='mb-1'><strong>Status:</strong> <span class='badge badge-light-{{ $statusClass }}'>{{ $doc->status?->label() ?? 'Pendente' }}</span></div>
                                <div class='mb-1'><strong>Enviado por:</strong> {{ $userName }}</div>
                                <div><strong>Data:</strong> {{ $dataFormatada }}</div>
                            </div>
                         ">
                        {{ $tituloTruncado }}
                    </div>
                </div>
                <div class="d-flex align-items-center text-muted fs-8">
                   <span class="me-2">{{ $dataFormatada }}</span>
                   <span class="bullet bullet-dot bg-secondary me-2 h-4px w-4px"></span>
                   <span class="text-truncate" style="max-width: 120px;">{{ Str::limit($userName, 15) }}</span>
                </div>
            </div>
            
            {{-- Indicador de Status (Barra lateral) --}}
            <div class="position-absolute end-0 top-0 bottom-0 w-4px rounded-end bg-{{ $statusClass }}"></div>
        </div>
    </div>
@empty
    <div class="text-center py-5 text-muted">
        <i class="fa-regular fa-folder-open fs-1 text-muted opacity-50 mb-2"></i>
        <div>Nenhum documento encontrado</div>
    </div>
@endforelse
