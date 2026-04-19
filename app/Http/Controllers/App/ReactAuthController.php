<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\TelaDeLogin;
use Illuminate\View\View;

/**
 * Serve o mesmo bundle React que /app/*, mas para visitantes (fluxo de login/registro).
 * Injeta __AUTH_APP_DATA__ com URLs e CSRF alinhados às rotas tenant-auth.
 */
class ReactAuthController extends Controller
{
    public function index(): View
    {
        $bag = session('errors');

        $backgroundImage = TelaDeLogin::where('status', 'ativo')->latest()->value('imagem_caminho');
        $loginBackgroundUrl = $backgroundImage
            ? route('file', ['path' => $backgroundImage])
            : url('/tenancy/assets/media/misc/penha.png');

        $authAppData = [
            'csrfToken' => csrf_token(),
            'appName'   => config('app.name', 'Dominus'),
            'urls'      => [
                'login'         => route('login'),
                'register'      => route('register'),
                'passwordEmail' => route('password.email'),
                'passwordStore' => route('password.store'),
            ],
            'flashStatus'      => session('status'),
            'validationErrors' => $bag ? $bag->getBag('default')->getMessages() : [],
            'loginBackgroundUrl' => $loginBackgroundUrl,
            'loginMiniLogoUrl'   => url('tenancy/assets/media/app/mini-logo.svg'),
        ];

        return view('react-auth', compact('authAppData'));
    }
}
