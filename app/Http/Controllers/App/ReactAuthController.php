<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\TelaDeLogin;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Serve o mesmo bundle React que /app/*, mas para visitantes (fluxo de login/registro).
 * Injeta __AUTH_APP_DATA__ com URLs e CSRF alinhados às rotas tenant-auth.
 */
class ReactAuthController extends Controller
{
    public function index(Request $request): View
    {
        $bag = session('errors');

        // ── Deep link via ?next= ──────────────────────────────────────────────
        // Quando o `SessionExpiredProvider` (React) detecta sessão expirada,
        // ele captura `pathname + search` e envia como `?next=...` ao redirecionar
        // para esta tela. Salvamos em `url.intended` (mesma chave usada pelo
        // Laravel) para que tanto `redirect()->intended()` (fluxo web tradicional)
        // quanto a resposta JSON de `AuthenticatedSessionController::store`
        // (fluxo React) consumam o destino e levem o usuário de volta exatamente
        // para onde ele estava antes da expiração.
        $intended = self::sanitizeIntendedPath((string) $request->query('next', ''));
        if ($intended !== null) {
            $request->session()->put('url.intended', $intended);
        }

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

    /**
     * Sanitiza o path recebido em `?next=`. Aceita apenas paths relativos
     * dentro do painel (`/app/*`), bloqueando:
     * - URLs absolutas (`https://attacker.com/...`) — open redirect.
     * - Protocol-relative (`//attacker.com/...`) — open redirect.
     * - Esquemas perigosos (`javascript:`, `data:`).
     * - CRLF injection (`\n`/`\r`) — header smuggling.
     * - Paths fora do painel (ex.: `/login`, `/forgot-password`) — evita loop.
     *
     * Espelhado em {@see App\Http\Controllers\App\Auth\AuthenticatedSessionController::sanitizeIntendedPath()}.
     */
    private static function sanitizeIntendedPath(string $raw): ?string
    {
        $raw = trim($raw);
        if ($raw === '' || strlen($raw) > 2048) {
            return null;
        }
        if (! str_starts_with($raw, '/'))     return null;
        if (str_starts_with($raw, '//'))      return null;
        if (str_contains($raw, "\n"))         return null;
        if (str_contains($raw, "\r"))         return null;
        if (! str_starts_with($raw, '/app/')) return null;

        return $raw;
    }
}
