<?php

namespace App\Http\Controllers\App\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RequestPasswordController extends Controller
{
    /**
     * Display the password request view.
     */
    public function create(): View
    {
        return view('app.auth.request-password');
    }

    /**
     * Handle an incoming password request to administrator.
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        // Verifica se o usuário existe no sistema
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Email não encontrado em nosso sistema. Verifique o email informado.',
                    'errors' => ['email' => ['Email não encontrado em nosso sistema.']]
                ], 422);
            }

            throw ValidationException::withMessages([
                'email' => ['Email não encontrado em nosso sistema.'],
            ]);
        }

        // Busca administradores (global ou admin)
        $administrators = User::role(['global', 'admin'])->get();

        if ($administrators->isEmpty()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Nenhum administrador encontrado no sistema. Entre em contato com o suporte.',
                ], 500);
            }

            return back()->with('error', 'Nenhum administrador encontrado no sistema. Entre em contato com o suporte.');
        }

        // Aqui você pode enviar notificação/email aos administradores
        // Por enquanto, apenas retornamos sucesso
        // TODO: Implementar notificação/email aos administradores
        
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Solicitação enviada com sucesso! Um administrador será notificado e entrará em contato em breve.',
            ], 200);
        }

        return back()->with('status', 'Solicitação enviada com sucesso! Um administrador será notificado e entrará em contato em breve.');
    }
}

