<?php

namespace App\View\Components;


use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class Toolbar extends Component
{
    /**
     * A empresa ativa a ser exibida.
     * Propriedades públicas nesta classe são automaticamente passadas para a view.
     *
     * @var \App\Models\Company|null
     */

    /**
     * Create a new component instance.
     */
    public function __construct(public $company)
    {
        // A lógica que estava no View Composer agora vive aqui!
        $this->company = null; // Inicia a variável

        if (Auth::check()) {
            $user = Auth::user();

            // Pega o ID da empresa ativa da sessão
            $activeCompanyId = session('active_company_id');

            // Busca a empresa correspondente
            $this->company = $user->companies()->find($activeCompanyId);

            // Fallback: se não houver empresa na sessão, pega a primeira
            if (!$this->company && $user->companies()->exists()) {
                $this->company = $user->companies()->first();
            }
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        // A classe já disponibilizou a variável $company para a view automaticamente.
        return view('app.components.toolbar');
    }
}
