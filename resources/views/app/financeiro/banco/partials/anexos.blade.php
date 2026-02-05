<div class="symbol-group symbol-hover fs-8">
    @foreach($anexos as $anexo)
        @php
            $formaAnexo = $anexo->forma_anexo ?? 'arquivo';
            $isLink = $formaAnexo === 'link';
            
            if ($isLink) {
                $href = $anexo->link ?? '#';
                $tooltip = $anexo->link ?? 'Link';
                $iconData = ['icon' => 'bi-link-45deg', 'color' => 'text-primary'];
            } else {
                $extension = pathinfo($anexo->nome_arquivo ?? '', PATHINFO_EXTENSION);
                $iconData = $icons[strtolower($extension)] ?? $defaultIcon;
                $tooltip = $anexo->nome_arquivo ?? 'Arquivo';
                
                if ($anexo->caminho_arquivo) {
                    $href = route('file', ['path' => $anexo->caminho_arquivo]);
                } else {
                    $href = '#';
                }
            }
        @endphp
        
        <div class="symbol symbol-30px symbol-circle bg-light-primary text-primary d-flex justify-content-center align-items-center" data-bs-toggle="tooltip" title="{{ $tooltip }}">
            <a href="{{ $href }}" target="_blank" class="text-decoration-none">
                <i class="bi {{ $iconData['icon'] }} {{ $iconData['color'] }} fs-3"></i>
            </a>
        </div>
    @endforeach

    @if($remainingAnexos > 0)
        <div class="symbol symbol-25px symbol-circle" data-bs-toggle="tooltip" title="Mais {{ $remainingAnexos }} anexos">
            <a href="#" onclick="abrirDrawerEdicao({{ $transacao->id }}); return false;">
                <span class="symbol-label fs-8 fw-bold bg-light text-gray-800">+{{ $remainingAnexos }}</span>
            </a>
        </div>
    @endif

    @if($transacao->modulos_anexos->isEmpty())
        <div class="symbol symbol-25px symbol-circle text-center" data-bs-toggle="tooltip" title="Nenhum anexo disponível">
            <span class="symbol-label fs-8 fw-bold bg-light text-gray-800">0</span>
        </div>
    @endif
</div>

