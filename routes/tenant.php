<?php

declare(strict_types=1);

use App\Http\Controllers\App\AnexoController;
use App\Http\Controllers\App\BancoController;
use App\Http\Controllers\App\CompanyController;
use App\Http\Controllers\App\DashboardController;
use App\Http\Controllers\App\PostController;
use App\Http\Controllers\App\ProfileController;
use App\Http\Controllers\App\UserController;
use App\Http\Controllers\App\TenantFilialController;
use App\Http\Controllers\App\CaixaController;
use App\Http\Controllers\App\LancamentoPadraoController;
use App\Http\Controllers\App\CadastroBancoController;
use App\Http\Controllers\App\ReportController;
use App\Models\TenantFilial;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

// Middleware aplicado a todas as rotas dentro do grupo
Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {

    // Rota para a página de login
    Route::get('/', function () {
        return view('app.auth.login');
    });

    // Rota para o dashboard, acessível apenas por usuários autenticados e verificados
    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

    // Rotas de perfil de usuário
    Route::get('/app/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/app/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/app/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/file/{path}', function ($path) {
        return response()->file(Storage::path($path));
    })->where('path', '.*')->name('file');


    // Grupo de rotas protegido pelo middleware 'auth' e 'ensureUserHasAccess'
    Route::middleware(['auth', 'ensureUserHasAccess'])->group(function () {


        // Grupo de rotas acessíveis apenas para administradores
        Route::group(['middleware' => ['role:global']], function () {
            Route::resource('filial', TenantFilialController::class);
            Route::resource('caixa', CaixaController::class);
            Route::resource('users', UserController::class);

            Route::resource('company', CompanyController::class);
        });

        // Grupo de rotas acessíveis apenas para administradores
        Route::group(['middleware' => ['role:admin']], function () {
            Route::resource('filial', TenantFilialController::class);
            Route::resource('caixa', CaixaController::class);
            // Rota para a função 'list'
            Route::get('app/financeiro/caixa/list', [CaixaController::class, 'list'])->name('caixa.list');

            Route::resource('users', UserController::class);
        });

        // Grupo de rotas acessíveis apenas para administradores
        Route::group(['middleware' => ['role:admin_user']], function () {
            Route::resource('filial', TenantFilialController::class);
            Route::resource('caixa', CaixaController::class);
            Route::resource('lancamentoPadrao', LancamentoPadraoController::class);
            Route::resource('cadastroBancos', CadastroBancoController::class);

            Route::resource('users', UserController::class);
        });


        // Grupo de rotas acessíveis apenas para usuários com o papel 'user'
        Route::group(['middleware' => ['role:user']], function () {
            Route::delete('/caixas/{id}', [CaixaController::class, 'destroySelected'])->name('caixas.destroySelected');

            Route::resource('caixa', CaixaController::class);
            Route::resource('banco', BancoController::class);
            Route::resource('anexos', AnexoController::class);
            Route::resource('post', PostController::class);

            Route::get('app/financeiro/caixa/list', [CaixaController::class, 'list'])->name('caixa.list');
            Route::get('app/financeiro/banco/list', [BancoController::class, 'list'])->name('banco.list');


            Route::get('/report/shipping', [ReportController::class, 'shippingReport'])->name('report.shipping');
            Route::get('/report/shipping/data', [ReportController::class, 'shippingReportData'])->name('report.shipping.data');
        });
    });

    // Autenticação específica para tenants
    require __DIR__ . '/tenant-auth.php';
});
