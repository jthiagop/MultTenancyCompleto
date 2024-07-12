<?php

declare(strict_types=1);

use App\Http\Controllers\App\DashboardController;
use App\Http\Controllers\App\PostController;
use App\Http\Controllers\App\ProfileController;
use App\Http\Controllers\App\UserController;
use App\Http\Controllers\App\TenantFilialController;
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

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    Route::get('/', function(){
        return view('auth.login');
    });


    Route::get('/dashboard', [DashboardController::class, 'index'] )->middleware(['auth', 'verified'])->name('dashboard');

    Route::middleware('auth', 'ensureUserHasAccess')->group(function () {

        Route::get('/app/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/app/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/app/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        Route::group(['middleware' => ['role:admin']], function ()
        {
            Route::resource('users', UserController::class );
            Route::resource('filial', TenantFilialController::class);
        });

        Route::get('/file/{path}', function($path) {
            return response()->file(Storage::path($path));
        })->where('path', '.*')->name('file');


        Route::group(['middleware' => ['role:user']], function ()
        {
            Route::resource('post', PostController::class);
        });

    });

    require __DIR__.'/tenant-auth.php';
});


