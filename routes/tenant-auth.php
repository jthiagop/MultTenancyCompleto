<?php

use App\Http\Controllers\App\ReactAuthController;
use App\Http\Controllers\App\Auth\AuthenticatedSessionController;
use App\Http\Controllers\App\Auth\ConfirmablePasswordController;
use App\Http\Controllers\App\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\App\Auth\EmailVerificationPromptController;
use App\Http\Controllers\App\Auth\FirstAccessController;
use App\Http\Controllers\App\Auth\NewPasswordController;
use App\Http\Controllers\App\Auth\PasswordController;
use App\Http\Controllers\App\Auth\PasswordResetLinkController;
use App\Http\Controllers\App\Auth\RegisteredUserController;
use App\Http\Controllers\App\Auth\RequestPasswordController;
use App\Http\Controllers\App\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    // Cadastro público DESABILITADO por padrão em produção.
    // Para reativar, setar ALLOW_PUBLIC_REGISTRATION=true no .env.
    // Mantemos a rota nomeada 'register' apontando para um 404 explícito,
    // porque existem links/redirects que usam route('register').
    if (env('ALLOW_PUBLIC_REGISTRATION', false)) {
        Route::get('register', [RegisteredUserController::class, 'create'])
                    ->name('register');

        Route::post('register', [RegisteredUserController::class, 'store']);
    } else {
        Route::match(['get', 'post'], 'register', function () {
            abort(404);
        })->name('register');
    }

    /** GET: SPA React (mesmo shell de /app/auth/*). POST permanece no controller de sessão. */
    Route::get('login', [ReactAuthController::class, 'index'])
                ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
                ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
                ->name('password.email');

    Route::get('request-password', [RequestPasswordController::class, 'create'])
                ->name('password.request.admin');

    Route::post('request-password', [RequestPasswordController::class, 'store'])
                ->name('password.request.admin.store');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
                ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
                ->name('password.store');
});

Route::middleware('auth')->group(function () {
    // Rota para primeiro acesso / troca de senha obrigatória
    Route::get('first-access', [FirstAccessController::class, 'show'])->name('first-access');
    Route::post('first-access', [FirstAccessController::class, 'store']);
    Route::get('verify-email', EmailVerificationPromptController::class)
                ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
                ->middleware(['signed', 'throttle:6,1'])
                ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
                ->middleware('throttle:6,1')
                ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
                ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
                ->name('logout');
});

// Logout da SPA React — rota fora do grupo guest, acessível quando autenticado
Route::middleware('auth')->post('/app/logout', [AuthenticatedSessionController::class, 'destroyFromReact'])
    ->name('react.logout');
