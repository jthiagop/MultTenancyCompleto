<?php

namespace App\Helpers;

use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Vite;

class ViteAssetHelper
{
    /**
     * Renderizar tags de CSS do Vite
     * Com suporte para Metronic theme
     * Hierarquia: plugins.bundle.css → style.bundle.css → vite CSS
     *
     * @return HtmlString
     */
    public static function renderViteStyles(): HtmlString
    {
        $css1 = asset('assets/plugins/global/plugins.bundle.css');
        $css2 = asset('assets/css/style.bundle.css');

        $html = <<<HTML
<!--begin::Metronic CSS - Plugin Bundle (First in hierarchy)-->
<link href="$css1" rel="stylesheet" type="text/css" />
<!--end::Metronic CSS - Plugin Bundle-->

<!--begin::Metronic CSS - Style Bundle (Second in hierarchy)-->
<link href="$css2" rel="stylesheet" type="text/css" />
<!--end::Metronic CSS - Style Bundle-->

<!--begin::Vite Styles (Custom styles override Metronic)-->
@vite(['resources/css/app.css'])
<!--end::Vite Styles-->
HTML;

        return new HtmlString($html);
    }

    /**
     * Renderizar tags de JavaScript do Vite
     * Entry point: resources/js/app.js
     *
     * @return HtmlString
     */
    public static function renderViteScripts(): HtmlString
    {
        $html = <<<'HTML'
<!--begin::Vite Scripts - Main Application Bundle-->
@vite(['resources/js/app.js'])
<!--end::Vite Scripts-->
HTML;

        return new HtmlString($html);
    }

    /**
     * Verificar se está em modo de desenvolvimento
     *
     * @return bool
     */
    public static function isDevelopment(): bool
    {
        return app()->environment('local', 'development');
    }

    /**
     * Obter URL do asset compilado pelo Vite
     *
     * @param string $path
     * @return string
     */
    public static function asset(string $path): string
    {
        if (file_exists(public_path('build/manifest.json'))) {
            return asset(Vite::asset($path));
        }

        return asset($path);
    }
}
