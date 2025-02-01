<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FileIcon extends Component
{
    public $file;
    /**
     * Create a new component instance.
     */
    public function __construct($file)
    {
        $this->file = $file;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('app.components.file-icon');
    }

    public function getIconClass()
    {
        $extension = strtolower(pathinfo($this->file, PATHINFO_EXTENSION));
        $icons = [
            'pdf' => 'bi-file-earmark-pdf-fill',
            'jpg' => 'bi bi-file-earmark-image',
            'jpeg' => 'bi bi-file-earmark-image',
            'png' => 'bi bi-file-earmark-image',
            'doc' => 'bi-file-earmark-word-fill',
            'docx' => 'bi-file-earmark-word-fill',
            'xls' => 'bi-file-earmark-spreadsheet-fill',
            'xlsx' => 'bi-file-earmark-spreadsheet-fill',
            'txt' => 'bi-file-earmark-text-fill',
        ];
        return $icons[$extension] ?? 'bi-file-earmark-fill';
    }
}
