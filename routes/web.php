<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TenantController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

// Rota para Termos de Uso e Política de Privacidade
Route::get('/termos', function () {
    return view('legal.termos-privacidade');
})->name('termos');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/app/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/app/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/app/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rotas de teste do Flasher
    Route::get('/flasher-test', function () {
        return view('flasher-test');
    })->name('flasher.test.page');
    
    Route::get('/flasher-test/{type}', function ($type) {
        $messages = [
            'success' => 'Teste de notificação de sucesso!',
            'error' => 'Teste de notificação de erro!',
            'warning' => 'Teste de notificação de aviso!',
            'info' => 'Teste de notificação informativa!',
        ];
        
        $message = $messages[$type] ?? 'Teste de notificação!';
        
        \Flasher\Laravel\Facade\Flasher::add($type, $message);
        
        \Log::info("Flasher teste disparado: tipo={$type}, mensagem={$message}");
        
        return redirect()->route('flasher.test.page');
    })->name('flasher.test');

    Route::resource('tenants', TenantController::class );
    Route::post('/tenants/{tenant}/generate-code', [TenantController::class, 'generateCode'])->name('tenants.generate-code');
});

// Webhook WhatsApp (Meta) - Rota movida para bootstrap/app.php para funcionar em qualquer domínio
// A rota está registrada globalmente para funcionar com ngrok/localhost/etc

require __DIR__.'/auth.php';
