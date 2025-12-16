<?php

declare(strict_types=1);

use App\Http\Controllers\Api\NotaFiscalImportController;
use App\Http\Controllers\App\Auth\AuthenticatedSessionController;
use App\Http\Middleware\InitializeTenancyByHostHeader;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant API Routes
|--------------------------------------------------------------------------
|
| Rotas de API específicas para tenants (mobile app)
| Todas as rotas aqui são automaticamente "tenant-aware" pelo middleware
|
*/

// Rotas que requerem tenant inicializado
// Nota: Removemos PreventAccessFromCentralDomains para permitir requisições de IPs
Route::middleware([
    'api',
    InitializeTenancyByHostHeader::class,
])->prefix('api')->group(function () {

    // Autenticação para API (mobile)
    Route::middleware('guest:sanctum')->group(function () {
        Route::post('/login', [AuthenticatedSessionController::class, 'store'])
            ->name('api.login');
    });

    // Rotas protegidas por autenticação
    Route::middleware(['auth:sanctum'])->group(function () {

        // Endpoint de informações do usuário autenticado
        Route::get('/user', function () {
            $user = \Illuminate\Support\Facades\Auth::user();
            return response()->json([
                'user' => $user,
                'tenant' => tenant('id'),
                'domain' => request()->getHost(),
            ]);
        })->name('api.user');

        // Logout
        Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
            ->name('api.logout');

        // Aqui você adicionará todas as outras rotas de API
        // Exemplo:
        // Route::get('/movimentacoes', [TransacaoFinanceiraController::class, 'index']);
        // Route::get('/caixa', [CaixaController::class, 'index']);
        // etc...

        Route::get('/fieis', [\App\Http\Controllers\App\FielController::class, 'apiIndex']);

        // Importação de nota fiscal via QR Code
        Route::post('/nota-fiscal/import', [NotaFiscalImportController::class, 'import'])
            ->name('api.nota-fiscal.import');
    });
});

