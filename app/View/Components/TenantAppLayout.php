<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class TenantAppLayout extends Component
{
    public $pageTitle;
    public $breadcrumbs;

    /**
     * Create a new component instance.
     *
     * @param string|null $pageTitle
     * @param array $breadcrumbs
     * @return void
     */
    public function __construct($pageTitle = null, $breadcrumbs = [])
    {
        $this->pageTitle = $pageTitle;
        $this->breadcrumbs = $breadcrumbs;
    }

    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('app.layouts.app', [
            'pageTitle' => $this->pageTitle,
            'breadcrumbs' => $this->breadcrumbs
        ]);
    }
}
