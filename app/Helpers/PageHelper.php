<?php

namespace App\Helpers;

class PageHelper
{
    /**
     * Set page title and breadcrumbs for the current request
     * 
     * @param string $title
     * @param array $breadcrumbs
     * @return array
     */
    public static function setPageData(string $title, array $breadcrumbs = []): array
    {
        return [
            'pageTitle' => $title,
            'breadcrumbs' => $breadcrumbs
        ];
    }
    
    /**
     * Create a breadcrumb item with label and optional URL
     * 
     * @param string $label
     * @param string|null $url
     * @return array
     */
    public static function breadcrumb(string $label, ?string $url = null): array
    {
        $item = ['label' => $label];
        
        if ($url !== null) {
            $item['url'] = $url;
        }
        
        return $item;
    }
}
