<?php

namespace App\View\Composers;

use Illuminate\View\View;
use App\Models\TelaDeLogin;

class LoginComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        // Buscar uma imagem aleatória ativa para exibir
        $imagemConvento = TelaDeLogin::ativas()
            ->inRandomOrder()
            ->first();

        $view->with('imagemConvento', $imagemConvento);
    }
}
