@php
    /**
     * Layout do lote: grade 2x2 (4 cartões A6 por página A4 retrato).
     * Para cada chunk de 4, gera DUAS páginas:
     *   - 1ª: FRENTES dos 4 cartões (ordem normal)
     *   - 2ª: VERSOS dos mesmos 4, com colunas INVERTIDAS por linha
     *         (espelhamento horizontal). Isso alinha o verso atrás de cada
     *         frente quando a impressora faz duplex de borda longa.
     *
     * Variáveis recebidas:
     *   - $cards               array<int, ['fiel'=>Fiel, 'codigo'=>string, 'qrSvg'=>string, 'barSvg'=>string, 'avatarDataUrl'=>string|null]>
     *   - $organismo           string
     *   - $companyLogoDataUrl  string|null  (data URL base64 — sem HTTP request pelo Chrome)
     *   - $ano                 int
     */
    $chunks = array_chunk($cards, 4);
@endphp
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Carteirinhas — Lote ({{ count($cards) }})</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        @page { size: A4 portrait; margin: 0; }

        html, body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            color: #18181b;
            background: #fff;
            font-size: 9px;
            line-height: 1.3;
            -webkit-font-smoothing: antialiased;
        }

        /* ========== PÁGINA A4 (grade 2×2 de cartões CR80) ========== */
        /*
         * Cartão padrão CR80 (ISO/IEC 7810 ID-1): 85,6 mm × 54 mm.
         * Cada slot ocupa exatamente esse tamanho; a grade fica centralizada
         * na folha A4 independentemente de quantos cartões há no chunk.
         */
        .sheet {
            width: 210mm;
            height: 297mm;
            padding: 8mm;
            display: grid;
            grid-template-columns: 86mm 86mm;
            grid-template-rows: 54mm 54mm;
            gap: 6mm;
            align-content: center;
            justify-content: center;
            page-break-after: always;
        }
        .sheet:last-child { page-break-after: auto; }

        .slot {
            width: 86mm;
            height: 54mm;
            display: flex;
            flex-direction: column;
        }

        /* Slot vazio — mantém o espaço na grade para alinhamento duplex */
        .slot.empty { background: transparent; }

        /* ========== Card (compartilhado entre frente e verso) ========== */
        .card {
            width: 86mm;
            height: 54mm;
            display: flex;
            flex-direction: column;
            border: 1px solid #d4d4d8;
            border-radius: 6px;
            overflow: hidden;
            background: #fff;
        }

        .header {
            background: #18181b;
            color: #fafafa;
            padding: 2px 4mm;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-shrink: 0;
        }
        .header .titulo { display: flex; flex-direction: column; line-height: 1.1; }
        .header .eyebrow {
            font-size: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            opacity: 0.7;
        }
        .header .organismo { font-size: 8px; font-weight: 700; }
        .header .codigo {
            font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 0.4px;
        }

        /* Frente — conteúdo proporcional ao cartão 86 mm × 54 mm */
        .body {
            flex: 1;
            display: grid;
            grid-template-columns: 14mm 1fr 14mm;
            gap: 3mm;
            padding: 2mm;
            align-items: center;
            overflow: hidden;
        }
        .photo, .logo-box {
            width: 14mm;
            height: 14mm;
            border: 1px solid #d4d4d8;
            border-radius: 3px;
            background: #f4f4f5;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #a1a1aa;
            font-size: 14px;
            font-weight: 700;
            overflow: hidden;
            flex-shrink: 0;
        }
        .photo img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .logo-box img { width: 100%; height: 100%; object-fit: contain; padding: 2px; display: block; }
        .photo .label, .logo-box .label {
            font-size: 6px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            color: #a1a1aa;
            font-weight: 500;
        }

        .info { display: flex; flex-direction: column; gap: 2px; min-width: 0; overflow: hidden; }
        .info .eyebrow {
            font-size: 6px;
            color: #71717a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .info .nome {
            font-size: 9px;
            font-weight: 700;
            color: #18181b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .codes { display: flex; align-items: flex-end; gap: 3px; margin-top: 1px; }
        .codes .qr {
            width: 11mm; height: 11mm;
            border: 1px solid #e4e4e7; border-radius: 3px;
            padding: 1px; background: #fff;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .codes .qr svg { width: 100%; height: 100%; display: block; }
        .codes .barcode-wrap {
            flex: 1; display: flex; flex-direction: column; min-width: 0;
        }
        .codes .barcode {
            border: 1px solid #e4e4e7; border-radius: 3px;
            padding: 1px; background: #fff;
        }
        .codes .barcode svg { width: 100%; height: 7mm; display: block; }
        .codes .barcode-meta {
            display: flex; justify-content: space-between;
            font-size: 6px; color: #71717a; margin-top: 1px;
        }
        .codes .barcode-meta .codigo {
            font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
            color: #18181b; font-weight: 600;
        }

        .footer {
            border-top: 1px solid #e4e4e7;
            background: #f9fafb;
            padding: 1px 5px;
            text-align: center;
            font-size: 6px;
            color: #71717a;
            flex-shrink: 0;
        }

        /* Verso — tabela de 12 meses proporcional ao cartão 86 mm × 54 mm */
        .controle {
            flex: 1;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2mm;
            padding: 1.5mm;
            overflow: hidden;
        }
        table.meses { width: 100%; border-collapse: collapse; font-size: 7px; }
        table.meses th, table.meses td {
            border: 1px solid #d4d4d8; padding: 1px 2px;
        }
        table.meses thead th {
            background: #f4f4f5; color: #52525b;
            font-weight: 700; text-transform: uppercase;
            font-size: 6px; letter-spacing: 0.3px; text-align: left;
        }
        table.meses thead th.center { text-align: center; }
        table.meses thead th.right { text-align: right; }
        table.meses td.mes { font-weight: 600; color: #18181b; width: 36%; }
        table.meses td.check { width: 8%; text-align: center; }
        table.meses td.check .box {
            display: inline-block; width: 6px; height: 6px;
            border: 1px solid #71717a;
        }
        table.meses td.data { width: 28%; height: 10px; }
        table.meses td.valor { width: 28%; text-align: right; height: 10px; }
    </style>
</head>
<body>

@foreach ($chunks as $chunk)
    @php
        // Garante array indexado contínuo de 0..3 (pode ter < 4 no último chunk).
        $chunk = array_values($chunk);
        // Posições da grade 2x2: linha1=[0,1], linha2=[2,3].
        // Para o verso, espelhamos cada linha: linha1=[1,0], linha2=[3,2].
        $frenteOrder = [0, 1, 2, 3];
        $versoOrder  = [1, 0, 3, 2];
    @endphp

    {{-- ===== FRENTES ===== --}}
    <div class="sheet">
        @for ($i = 0; $i < 4; $i++)
            @php $idx = $frenteOrder[$i]; @endphp
            @if (isset($chunk[$idx]))
                @php
                    $card          = $chunk[$idx];
                    $fiel          = $card['fiel'];
                    $codigo        = $card['codigo'];
                    $qrSvg         = $card['qrSvg'];
                    $barSvg        = $card['barSvg'];
                    $avatarDataUrl = $card['avatarDataUrl'] ?? null;
                @endphp
                <div class="slot">
                    @include('app.fieis._carteirinha-frente')
                </div>
            @else
                <div class="slot empty"></div>
            @endif
        @endfor
    </div>

    {{-- ===== VERSOS (colunas espelhadas para alinhar duplex) ===== --}}
    <div class="sheet">
        @for ($i = 0; $i < 4; $i++)
            @php $idx = $versoOrder[$i]; @endphp
            @if (isset($chunk[$idx]))
                <div class="slot">
                    @include('app.fieis._carteirinha-verso')
                </div>
            @else
                <div class="slot empty"></div>
            @endif
        @endfor
    </div>
@endforeach

</body>
</html>
