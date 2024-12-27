<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FormRow extends Component
{
    public $label;

    /**
     * Cria uma nova instância do componente.
     */
    public function __construct($label)
    {
        $this->label = $label;
    }

    /**
     * Renderiza o componente.
     */
    public function render()
    {
        return view('app.components.form-row');
    }
}
