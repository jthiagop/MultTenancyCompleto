<?php

declare(strict_types=1);

use App\Http\Controllers\App\PrestacaoDeContaController;
use App\Http\Controllers\App\AnexoController;
use App\Http\Controllers\App\Anexos\ModulosAnexosController;
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
use App\Http\Controllers\App\Cemiterio\CemeteryController;
use App\Http\Controllers\App\Cemiterio\SepulturaController;
use App\Http\Controllers\App\EntidadeFinanceiraController;
use App\Http\Controllers\App\EscrituraController;
use App\Http\Controllers\App\FielController;
use App\Http\Controllers\App\Filter\filterController;
use App\Http\Controllers\App\Filter\RebortController;
use App\Http\Controllers\App\Financeiro\ConciliacaoController;
use App\Http\Controllers\App\Financeiro\CostCenterController;
use App\Http\Controllers\App\Financeiro\OfxController;
use App\Http\Controllers\App\Relatorios\ReciboController;
use App\Http\Controllers\App\Financeiro\TransacaoFinanceiraController;
use App\Http\Controllers\App\Frota\CarInsuranceController;
use App\Http\Controllers\App\NamePatrimonioController;
use App\Http\Controllers\App\PatrimonioController;
use App\Http\Controllers\App\ReportController;
use App\Http\Controllers\App\PatrimonioAnexoController;
use App\Http\Controllers\App\TelaDeLoginController;
use App\Models\Financeiro\ModulosAnexo;
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

    // Rota para exibir uma página de erro quando o tenant não é encontrado
    Route::get('/tenant-not-found', function () {
        return view('errors.tenant_not_found');
    })->name('tenant.not.found');

    // Rota para a página de login
    Route::get('/', function () {
        return view('app.auth.login');
    });

    // Rota para o dashboard, acessível apenas por usuários autenticados e verificados
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware(['auth', 'verified'])
        ->name('dashboard');

    // Rotas de perfil de usuário
    Route::prefix('/app/profile')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('profile.destroy');
        Route::match(['put', 'patch'], '/', [ProfileController::class, 'update'])->name('profile.update');
    });

    // Rota para servir arquivos públicos
    Route::get('/file/{path}', function ($path) {
        return response()->file(Storage::disk('public')->path($path));
    })->where('path', '.*')->name('file');



    // Grupo de rotas protegido pelo middleware 'auth' e 'ensureUserHasAccess'
    Route::middleware(['auth', 'ensureUserHasAccess'])->group(function () {

        // Rotas acessíveis apenas para administradores globais
        Route::middleware(['role:global'])->group(function () {
            Route::resource('filial', TenantFilialController::class);
            Route::resource('caixa', CaixaController::class);
            Route::resource('users', UserController::class);
            Route::resource('telaLogin', TelaDeLoginController::class);
        });

        // Rotas acessíveis apenas para administradores
        Route::middleware(['role:admin'])->group(function () {
            Route::resource('filial', TenantFilialController::class);
            Route::resource('caixa', CaixaController::class);
            Route::get('app/financeiro/caixa/list', [CaixaController::class, 'list'])->name('caixa.list');
            Route::resource('users', UserController::class);
            Route::resource('company', CompanyController::class);
            Route::get('/company/edit/{company}', [CompanyController::class, 'editCompany'])->name('company.editCompany');
            Route::post('/filter', [RebortController::class, 'generateReport']);
        });

        // Rotas acessíveis apenas para administradores e usuários específicos
        Route::middleware(['role:admin_user'])->group(function () {
            Route::resource('filial', TenantFilialController::class);
            Route::resource('caixa', CaixaController::class);
            Route::resource('lancamentoPadrao', LancamentoPadraoController::class);
            Route::resource('cadastroBancos', CadastroBancoController::class);
            Route::resource('users', UserController::class);
        });

        // Rotas para gerenciamento de centros de custo
        Route::resource('costCenter', CostCenterController::class);

        // Rotas acessíveis apenas para usuários comuns
        Route::middleware(['role:user'])->group(function () {
            Route::delete('/caixas/{id}', [CaixaController::class, 'destroySelected'])->name('caixas.destroySelected');
            Route::resource('caixa', CaixaController::class);
            Route::resource('banco', BancoController::class);
            Route::resource('anexos', AnexoController::class);
            Route::resource('modulosAnexos', ModulosAnexosController::class);
            Route::resource('post', PostController::class);
            Route::get('/lancamento_padrao/tipo/{tipo}', [LancamentoPadraoController::class, 'getLancamentosByTipo']);
            Route::resource('patrimonio', PatrimonioController::class);
            Route::resource('escritura', EscrituraController::class);
            Route::resource('patrimonioAnexo', PatrimonioAnexoController::class);
            Route::get('patrimonios/imoveis', [PatrimonioController::class, 'imoveis'])->name('patrimonio.imoveis');
            Route::resource('namePatrimonio', NamePatrimonioController::class);
            Route::post('/validar-num-foro', [NamePatrimonioController::class, 'validarNumForo']);
            Route::get('app/financeiro/caixa/list', [CaixaController::class, 'list'])->name('caixa.list');
            Route::get('app/financeiro/banco/list', [BancoController::class, 'list'])->name('banco.list');
            Route::get('/patrimonios/search', [PatrimonioController::class, 'search'])->name('patrimonios.search');
            Route::get('/patrimonios/grafico', [PatrimonioController::class, 'grafico']);
            Route::get('/report/shipping', [ReportController::class, 'shippingReport'])->name('report.shipping');
            Route::get('/report/shipping/data', [ReportController::class, 'shippingReportData'])->name('report.shipping.data');
            Route::resource('cemiterio', CemeteryController::class);
            Route::resource('sepultura', SepulturaController::class);

            Route::post('/upload-ofx', [OfxController::class, 'upload'])->name('upload.ofx');

            Route::patch('/conciliacao/{id}/ignorar', [ConciliacaoController::class, 'ignorar'])->name('conciliacao.ignorar');
            Route::get('/conciliacao', [ConciliacaoController::class, 'index'])->name('conciliacao.index');
            Route::get('/conciliacao/comparar/{id}', [ConciliacaoController::class, 'comparar'])->name('conciliacao.comparar');
            Route::post('/conciliacao/conciliar', [ConciliacaoController::class, 'conciliar'])->name('conciliacao.conciliar');


            // Grupo de rotas para relatórios
            Route::prefix('relatorios')->group(function () {

                Route::resource('recibos', ReciboController::class);
                Route::post('/recibos/gerar/{transacao}', [ReciboController::class, 'gerarRecibo'])->name('gerarRecibo');
                Route::get('/recibo/imprimir/{id}', [ReciboController::class, 'imprimirRecibo'])->name('recibo.imprimir');


                Route::get('/prestacao-de-contas', [PrestacaoDeContaController::class, 'index'])
                    ->name('relatorios.prestacao.de.contas');
                Route::post('bill/{id}/print', [ PrestacaoDeContaController::class, 'print'])->name('bill.print');

                Route::get('/prestacao-de-contas/pdf', [PrestacaoDeContaController::class, 'gerarPdf'])
                    ->name('relatorios.prestacao.de.contas.gerar');

                Route::post('/filter', [ PrestacaoDeContaController::class, 'generateReport']);

                Route::resource('fieis', FielController::class);

                Route::resource('entidades', EntidadeFinanceiraController::class);

                Route::post('entidades/{id}/movimentacao', [EntidadeFinanceiraController::class, 'addMovimentacao'])
                    ->name('entidades.movimentacao');

                Route::resource('car_insurance', CarInsuranceController::class);

                Route::post('car_insurance/{id}/sell', [CarInsuranceController::class, 'sell'])
                    ->name('car_insurance.sell');

                Route::resource('transacoes-financeiras', TransacaoFinanceiraController::class);

                Route::get('/transacoes/data', [TransacaoFinanceiraController::class, 'getData'])
                    ->name('transacoes.data');
            });
        });
    });

    // Autenticação específica para tenants
    require __DIR__ . '/tenant-auth.php';
});
