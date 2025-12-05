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
        return view('app.auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        try {
        $request->authenticate();

        $request->session()->regenerate();

        // Verifica se o usuário precisa trocar a senha
        $user = Auth::user();
            
            // Verifica se o usuário está ativo
            if (!$user->active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Sua conta foi desativada. Entre em contato com o administrador.',
                        'error' => 'USER_INACTIVE'
                    ], 403);
                }
                
                return redirect()->route('login')->with('error', 'Sua conta foi desativada. Entre em contato com o administrador.');
            }
            
        if ($user->must_change_password) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Você precisa alterar sua senha.',
                        'redirect' => route('first-access')
                    ], 200);
                }
                
            return redirect()->route('first-access');
        }

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Login realizado com sucesso!',
                    'redirect' => route('dashboard', absolute: false)
                ], 200);
            }

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
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
