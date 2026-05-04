{{--
    Partial reutilizável: FRENTE da carteirinha do dizimista.

    Variáveis esperadas no escopo:
      - $fiel                (App\Models\Fiel)
      - $codigo              (string)
      - $qrSvg               (string HTML SVG)
      - $barSvg              (string HTML SVG)
      - $organismo           (string)
      - $avatarDataUrl       (string|null)  data URL base64 do avatar do fiel
      - $companyLogoDataUrl  (string|null)  data URL base64 do logo da organização

    Todas as imagens são data URLs para evitar requisições HTTP externas durante
    a renderização pelo Browsershot (Chrome carrega o HTML via file://).

    O CSS das classes (.card, .header, .body, .photo, .logo-box, .info,
    .codes, .footer) é definido no template pai.
--}}
<div class="card">
    <div class="header">
        <div class="titulo">
            <span class="eyebrow">Carteirinha do Dizimista</span>
            <span class="organismo">{{ $organismo }}</span>
        </div>
        <span class="codigo">{{ $codigo }}</span>
    </div>

    <div class="body">
        {{-- Foto do fiel (esquerda) — data URL embutida, sem HTTP request --}}
        <div class="photo">
            @if (!empty($avatarDataUrl))
                <img src="{{ $avatarDataUrl }}" alt="{{ $fiel->nome_completo }}">
            @else
                {{ \Illuminate\Support\Str::of($fiel->nome_completo)->trim()->explode(' ')->take(2)->map(fn ($p) => strtoupper(substr($p, 0, 1)))->implode('') }}
            @endif
        </div>

        {{-- Centro: nome + códigos --}}
        <div class="info">
            <span class="eyebrow">Dizimista</span>
            <span class="nome">{{ $fiel->nome_completo }}</span>

            <div class="codes">
                <div class="qr">{!! $qrSvg !!}</div>
                <div class="barcode-wrap">
                    <div class="barcode">{!! $barSvg !!}</div>
                    <div class="barcode-meta">
                        <span>Code128</span>
                        <span class="codigo">{{ $codigo }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Logo da company (direita) — data URL embutida, sem HTTP request --}}
        <div class="logo-box">
            @if (!empty($companyLogoDataUrl))
                <img src="{{ $companyLogoDataUrl }}" alt="{{ $organismo }}">
            @else
                <span class="label">Logo</span>
            @endif
        </div>
    </div>

    <div class="footer">
        Apresente esta carteirinha ao realizar a contribuição do dízimo.
    </div>
</div>
