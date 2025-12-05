<?php

namespace App\Http\Controllers\App\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class FirstAccessController extends Controller
{
    /**
     * Exibe a página de primeiro acesso
     */
    public function show(): View
    {
        return view('app.auth.first-access');
    }

    /**
     * Processa a definição da nova senha
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // Validação dos dados
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'confirmed', Password::min(8)],
        ], [
            'current_password.required' => 'A senha atual é obrigatória.',
            'password.required' => 'A nova senha é obrigatória.',
            'password.confirmed' => 'A confirmação da senha não confere.',
            'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
        ]);

        // Verifica se a senha atual está correta
        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors([
                'current_password' => 'A senha atual informada está incorreta.',
            ])->withInput();
        }

        // Verifica se a nova senha é diferente da atual
        if (Hash::check($validated['password'], $user->password)) {
            return back()->withErrors([
                'password' => 'A nova senha deve ser diferente da senha atual.',
            ])->withInput();
        }

        // Atualiza a senha do usuário
        $user->password = Hash::make($validated['password']);
        $user->must_change_password = false;
        $user->password_changed_at = now();
        $user->save();

        return redirect()->route('dashboard')->with('success', 'Senha alterada com sucesso!');
    }
}

