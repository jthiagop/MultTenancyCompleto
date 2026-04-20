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

        // Sorteia 1 imagem ativa a cada request — imagem muda a cada reload da tela de login.
        // Usa rota pública (login.background) porque a tela de login é exibida antes
        // da autenticação — route('file', ...) exige auth e retornaria 302 → /login.
        $randomImage = TelaDeLogin::where('status', 'ativo')
            ->inRandomOrder()
            ->first(['id', 'imagem_caminho', 'descricao', 'localidade']);

        $loginBackgroundUrl = $randomImage
            ? route('login.background', ['path' => $randomImage->imagem_caminho])
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
            'loginBackgroundUrl'         => $loginBackgroundUrl,
            'loginBackgroundDescricao'   => $randomImage?->descricao,
            'loginBackgroundLocalidade'  => $randomImage?->localidade,
            'loginMiniLogoUrl'           => url('tenancy/assets/media/app/mini-logo.svg'),
        ];

        return view('react-auth', compact('authAppData'));
    }
}
