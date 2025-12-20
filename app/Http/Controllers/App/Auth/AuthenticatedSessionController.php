<?php

namespace App\Http\Controllers\App\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\TelaDeLogin;
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
        // Buscar uma imagem aleatória ativa para exibir
        $imagemConvento = TelaDeLogin::ativas()
            ->inRandomOrder()
            ->first();
        
        return view('app.auth.login', compact('imagemConvento'));
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        try {
        $request->authenticate();

        // Verifica se o usuário precisa trocar a senha
        $user = Auth::user();
            
            // Verifica se o usuário está ativo
            if (!$user->active) {
                Auth::logout();
                
                // Só usar sessão se não for API
                if (!($request->expectsJson() || $request->is('api/*'))) {
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                }
                
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Sua conta foi desativada. Entre em contato com o administrador.',
                        'error' => 'USER_INACTIVE'
                    ], 403);
                }
                
                return redirect()->route('login')->with('error', 'Sua conta foi desativada. Entre em contato com o administrador.');
            }
            
        if ($user->must_change_password) {
                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json([
                        'message' => 'Você precisa alterar sua senha.',
                        'error' => 'PASSWORD_CHANGE_REQUIRED',
                        'redirect' => route('first-access')
                    ], 422);
                }
                
            return redirect()->route('first-access');
        }

        // Se for requisição de API (mobile), retornar token Sanctum
        if ($request->expectsJson() || $request->is('api/*')) {
            // Revogar tokens anteriores (opcional - para permitir apenas um dispositivo)
            // $user->tokens()->delete();
            
            $token = $user->createToken('mobile-app')->plainTextToken;
            
            return response()->json([
                'user' => $user,
                'token' => $token,
                'tenant' => tenant('id'),
                'domain' => request()->getHost(),
            ]);
        }

        // Apenas para requisições web, regenerar sessão
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Credenciais inválidas. Verifique seu email e senha.',
                    'errors' => $e->errors()
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
}
