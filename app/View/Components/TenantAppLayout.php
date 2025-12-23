<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class TenantAppLayout extends Component
{
    public $pageTitle;

    /**
     * Create a new component instance.
     *
     * @param string|null $pageTitle
     * @return void
     */
    public function __construct($pageTitle = null)
    {
        $this->pageTitle = $pageTitle;
    }

    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('app.layouts.app', ['pageTitle' => $this->pageTitle]);
    }
}
