<?php

declare(strict_types=1);

namespace App\Services\Fieis;

use App\Models\Fiel;
use App\Models\FielTithe;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

/**
 * Lógica da carteirinha do dizimista.
 *
 * Responsabilidades:
 *  - Geração automática do código D-XXXX por company (híbrido).
 *  - Geração de SVG para QR Code (lib simple-qrcode) e Code128 (impl. própria).
 *  - Construção do payload textual gravado no QR Code (verificação rápida na recepção).
 */
class CarteirinhaFielService
{
    /** Largura mínima de zeros do número (ex.: 4 → D-0001). */
    public const PAD_LENGTH = 4;

    public const PREFIX = 'D-';

    /**
     * Garante que o fiel possua código de dizimista.
     * Se vazio, gera o próximo sequencial dentro da company.
     *
     * Retorna o código final (existente ou recém gerado).
     */
    public function ensureCodigo(FielTithe $tithe, ?string $codigoInformado, int $companyId): string
    {
        $codigo = trim((string) $codigoInformado);

        if ($codigo !== '') {
            $tithe->codigo = $codigo;
            $tithe->save();
            return $codigo;
        }

        if (! empty($tithe->codigo)) {
            return $tithe->codigo;
        }

        $codigo = $this->proximoCodigo($companyId);

        $tithe->codigo = $codigo;
        $tithe->save();

        Log::info('[carteirinha] Código automático atribuído', [
            'fiel_id'     => $tithe->fiel_id,
            'tithe_id'    => $tithe->id,
            'company_id'  => $companyId,
            'codigo'      => $codigo,
        ]);

        return $codigo;
    }

    /**
     * Retorna o próximo código sequencial para a company (ex.: D-0001 → D-0002).
     * Operação atômica com lock para evitar duplicidade em concorrência.
     */
    public function proximoCodigo(int $companyId): string
    {
        /** @var string $codigo */
        $codigo = DB::transaction(function () use ($companyId): string {
            $ultimo = FielTithe::query()
                ->whereHas('fiel', fn ($q) => $q->where('company_id', $companyId))
                ->where('codigo', 'like', self::PREFIX . '%')
                ->orderByRaw('CAST(SUBSTRING(codigo, ?) AS UNSIGNED) DESC', [strlen(self::PREFIX) + 1])
                ->lockForUpdate()
                ->value('codigo');

            $proximo = 1;
            if ($ultimo && preg_match('/^' . preg_quote(self::PREFIX, '/') . '(\d+)$/', (string) $ultimo, $m)) {
                $proximo = ((int) $m[1]) + 1;
            }

            return self::PREFIX . str_pad((string) $proximo, self::PAD_LENGTH, '0', STR_PAD_LEFT);
        });

        return $codigo;
    }

    /**
     * Payload textual codificado no QR Code da carteirinha.
     * Mantém a URL canônica para que um leitor de QR (celular) abra a tela do fiel.
     */
    public function payloadQr(Fiel $fiel, string $codigo): string
    {
        $base = rtrim((string) config('app.url'), '/');

        return $base . '/relatorios/fieis/' . $fiel->id . '/edit?dz=' . urlencode($codigo);
    }

    /**
     * SVG do QR Code para o payload informado.
     */
    public function qrCodeSvg(string $payload, int $size = 180, int $margin = 1): string
    {
        try {
            return (string) QrCode::format('svg')
                ->size($size)
                ->margin($margin)
                ->errorCorrection('M')
                ->generate($payload);
        } catch (\Throwable $e) {
            Log::error('[carteirinha] qrCodeSvg falhou (SimpleSoftwareIO\QrCode)', [
                'size'          => $size,
                'payload_len'   => strlen($payload),
                'exception'     => $e::class,
                'message'       => $e->getMessage(),
                'trace'         => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Gera SVG de um código de barras Code128 (modo automático A/B/C simplificado).
     *
     * Implementação enxuta — suficiente para conteúdos típicos de carteirinha
     * (letras, números, hífen, ponto). Não cobre o conjunto C otimizado para
     * dígitos pares; isto é aceitável para nosso caso de uso.
     *
     * @param  string  $value      Valor a codificar (ASCII visível).
     * @param  int     $width      Largura total do SVG em px.
     * @param  int     $height     Altura das barras em px.
     * @param  bool    $showText   Renderiza o texto legível abaixo do código.
     */
    public function code128Svg(string $value, int $width = 320, int $height = 70, bool $showText = true): string
    {
        $value = $this->sanitizeForCode128($value);
        $code = $this->encodeCode128($value);
        $totalModules = strlen($code);

        if ($totalModules === 0) {
            return '';
        }

        $moduleWidth = $width / $totalModules;
        $textHeight  = $showText ? 14 : 0;
        $svgHeight   = $height + $textHeight;

        $bars = '';
        for ($i = 0; $i < $totalModules; $i++) {
            if ($code[$i] === '1') {
                $x = $i * $moduleWidth;
                $bars .= sprintf(
                    '<rect x="%s" y="0" width="%s" height="%d" fill="#111" />',
                    $this->fmt($x),
                    $this->fmt($moduleWidth),
                    $height,
                );
            }
        }

        $textNode = '';
        if ($showText) {
            $textNode = sprintf(
                '<text x="%d" y="%d" text-anchor="middle" font-family="ui-monospace, SFMono-Regular, Menlo, monospace" font-size="12" fill="#111">%s</text>',
                (int) ($width / 2),
                $height + 12,
                e($value),
            );
        }

        return sprintf(
            '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 %d %d" width="100%%" height="%d" preserveAspectRatio="none"><rect width="%d" height="%d" fill="#fff" />%s%s</svg>',
            $width,
            $svgHeight,
            $svgHeight,
            $width,
            $svgHeight,
            $bars,
            $textNode,
        );
    }

    /** Normaliza valor para o subset 128B (ASCII 32–127). */
    protected function sanitizeForCode128(string $value): string
    {
        $value = trim($value);
        $value = preg_replace('/[^\x20-\x7e]/', '', $value) ?? '';
        return $value === '' ? '0' : $value;
    }

    /**
     * Codifica string em padrão de barras Code128 subset B.
     * Retorna sequência binária ('1' = barra preta, '0' = espaço).
     */
    protected function encodeCode128(string $value): string
    {
        // Padrões oficiais de Code128 (108 entradas: 0..103, START_B = 104, STOP completo).
        $patterns = [
            '11011001100','11001101100','11001100110','10010011000','10010001100',
            '10001001100','10011001000','10011000100','10001100100','11001001000',
            '11001000100','11000100100','10110011100','10011011100','10011001110',
            '10111001100','10011101100','10011100110','11001110010','11001011100',
            '11001001110','11011100100','11001110100','11101101110','11101001100',
            '11100101100','11100100110','11101100100','11100110100','11100110010',
            '11011011000','11011000110','11000110110','10100011000','10001011000',
            '10001000110','10110001000','10001101000','10001100010','11010001000',
            '11000101000','11000100010','10110111000','10110001110','10001101110',
            '10111011000','10111000110','10001110110','11101110110','11010001110',
            '11000101110','11011101000','11011100010','11011101110','11101011000',
            '11101000110','11100010110','11101101000','11101100010','11100011010',
            '11101111010','11001000010','11110001010','10100110000','10100001100',
            '10010110000','10010000110','10000101100','10000100110','10110010000',
            '10110000100','10011010000','10011000010','10000110100','10000110010',
            '11000010010','11001010000','11110111010','11000010100','10001111010',
            '10100111100','10010111100','10010011110','10111100100','10011110100',
            '10011110010','11110100100','11110010100','11110010010','11011011110',
            '11011110110','11110110110','10101111000','10100011110','10001011110',
            '10111101000','10111100010','11110101000','11110100010','10111011110',
            '10111101110','11101011110','11110101110','11010000100','11010010000',
            '11010011100','11000111010',
        ];
        $startB = 104;
        $stop   = '1100011101011';

        $codes = [$startB];
        $sum   = $startB;
        $len   = strlen($value);

        for ($i = 0; $i < $len; $i++) {
            $c = ord($value[$i]) - 32;
            if ($c < 0 || $c > 95) $c = 0;
            $codes[] = $c;
            $sum    += $c * ($i + 1);
        }

        $codes[] = $sum % 103;

        $bits = '';
        foreach ($codes as $code) {
            if (! isset($patterns[$code])) continue;
            $bits .= $patterns[$code];
        }
        $bits .= $stop;

        return $bits;
    }

    /** Formata float com até 4 casas, sem zeros desnecessários. */
    protected function fmt(float $v): string
    {
        return rtrim(rtrim(number_format($v, 4, '.', ''), '0'), '.') ?: '0';
    }
}
