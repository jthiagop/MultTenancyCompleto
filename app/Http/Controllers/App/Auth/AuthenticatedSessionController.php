<?php

namespace App\Http\Controllers\App\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        $randomImage = null;
        $backgroundImage = null;

        try {
            if (class_exists(\App\Models\TelaDeLogin::class)) {
                 $randomImage = \App\Models\TelaDeLogin::where('status', 'ativo')
                    ->inRandomOrder()
                    ->first();
                
                if ($randomImage) {
                    $backgroundImage = $randomImage->imagem_caminho;
                }
            }
        } catch (\Exception $e) {
            // Fallback gracefully if table doesn't exist
        }

        return view('app.auth.login', compact('randomImage', 'backgroundImage'));
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        // X-React-Web: requisição da SPA web (não mobile nativo)
        $isReactWeb = $request->header('X-React-Web') === '1';
        // Mobile/API: expectsJson ou rota /api/*, excluindo React Web
        $isMobile   = ($request->expectsJson() || $request->is('api/*')) && !$isReactWeb;

        try {
            $request->authenticate();

            $user = Auth::user();

            // Verifica se o usuário está ativo
            if (!$user->active) {
                Auth::logout();

                if (!$isMobile) {
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                }

                if ($isMobile || $isReactWeb) {
                    return response()->json([
                        'message' => 'Sua conta foi desativada. Entre em contato com o administrador.',
                        'error'   => 'USER_INACTIVE',
                    ], 403);
                }

                return redirect()->route('login')->with('error', 'Sua conta foi desativada. Entre em contato com o administrador.');
            }

            // Verifica se o usuário precisa trocar a senha
            if ($user->must_change_password) {
                if ($isMobile || $isReactWeb) {
                    if ($isReactWeb) {
                        // Regenera a sessão (proteção contra session fixation).
                        // regenerate() também troca o _token (CSRF), então retornamos
                        // o novo token para que o frontend use na etapa 2.
                        $request->session()->regenerate();
                        return response()->json([
                            'message'    => 'Você precisa alterar sua senha.',
                            'error'      => 'PASSWORD_CHANGE_REQUIRED',
                            'csrf_token' => $request->session()->token(),
                        ], 422);
                    }
                    return response()->json([
                        'message' => 'Você precisa alterar sua senha.',
                        'error'   => 'PASSWORD_CHANGE_REQUIRED',
                    ], 422);
                }

                return redirect()->route('first-access');
            }

            // ── Sucesso ────────────────────────────────────────────────────────

            if ($isMobile) {
                $token = $user->createToken('mobile-app')->plainTextToken;
                return response()->json([
                    'user'   => $user,
                    'token'  => $token,
                    'tenant' => tenant('id'),
                    'domain' => request()->getHost(),
                ]);
            }

            // ATENÇÃO: ler `url.intended` ANTES de regenerar a sessão.
            // `regenerate()` mantém os dados (`migrate`/`flash`), mas a chave
            // `url.intended` é tratada como flash em algumas versões/contextos
            // — então pull antes evita race com a regeneração.
            $intendedRaw = (string) $request->session()->pull('url.intended', '');

            $request->session()->regenerate();

            $intended = self::sanitizeIntendedPath($intendedRaw) ?? '/app/dashboard';

            if ($isReactWeb) {
                return response()->json([
                    'success'  => true,
                    'redirect' => $intended,
                ]);
            }

            return redirect($intended);

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($isMobile || $isReactWeb) {
                return response()->json([
                    'message' => 'Credenciais inválidas. Verifique seu email e senha.',
                    'errors'  => $e->errors(),
                ], 422);
            }

            throw $e;
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        // Se for requisição de API (mobile), revogar token Sanctum
        if ($request->expectsJson() || $request->is('api/*')) {
            $user = $request->user();
            
            if ($user) {
                // Revogar todos os tokens do usuário
                $user->tokens()->delete();
            }
            
            return response()->json([
                'message' => 'Logout realizado com sucesso!'
            ]);
        }

        // Comportamento normal para web
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Logout originado pelo React SPA — sempre retorna JSON; o frontend faz a navegação.
     */
    public function destroyFromReact(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['success' => true]);
    }

    /**
     * Sanitiza o path consumido de `url.intended` antes de devolvê-lo ao
     * cliente ou usá-lo num `redirect()`. Aceita apenas paths relativos
     * dentro do painel (`/app/*`); rejeita URLs absolutas de outros hosts,
     * protocol-relative, esquemas perigosos, CRLF e paths fora do painel
     * (que causariam loop no /login).
     *
     * Espelhado em {@see App\Http\Controllers\App\ReactAuthController::sanitizeIntendedPath()}.
     */
    private static function sanitizeIntendedPath(string $raw): ?string
    {
        $raw = trim($raw);
        if ($raw === '' || strlen($raw) > 2048) {
            return null;
        }

        // `url.intended` historicamente pode guardar URL absoluta (via url())
        // dependendo de quem populou. Aceitamos absoluta apenas se a host
        // bater com a do request — extraímos só o path nesse caso.
        if (preg_match('#^https?://#i', $raw)) {
            $parts = parse_url($raw);
            if (! is_array($parts) || empty($parts['host']) || $parts['host'] !== request()->getHost()) {
                return null;
            }
            $raw = ($parts['path'] ?? '/') . (isset($parts['query']) ? '?' . $parts['query'] : '');
        }

        if (! str_starts_with($raw, '/'))     return null;
        if (str_starts_with($raw, '//'))      return null;
        if (str_contains($raw, "\n"))         return null;
        if (str_contains($raw, "\r"))         return null;
        if (! str_starts_with($raw, '/app/')) return null;

        return $raw;
    }
}
