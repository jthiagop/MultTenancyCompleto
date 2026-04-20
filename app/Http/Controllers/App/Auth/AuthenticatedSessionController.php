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

            $request->session()->regenerate();

            if ($isReactWeb) {
                return response()->json([
                    'success'  => true,
                    'redirect' => '/app/dashboard',
                ]);
            }

            return redirect()->intended('/app/dashboard');

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
}
