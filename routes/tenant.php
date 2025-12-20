<?php

declare(strict_types=1);

use App\Http\Controllers\App\Contabilidade\AccountMappingController;
use App\Http\Controllers\App\Contabilidade\ChartOfAccountController;
use App\Http\Controllers\App\PrestacaoDeContaController;
use App\Http\Controllers\App\BoletimFinanceiroController;
use App\Http\Controllers\App\AnexoController;
use App\Http\Controllers\App\Anexos\ModulosAnexosController;
use App\Http\Controllers\App\BancoController;
use App\Http\Controllers\App\CompanyController;
use App\Http\Controllers\App\DashboardController;
use App\Http\Controllers\App\PostController;
use App\Http\Controllers\App\ProfileController;
use App\Http\Controllers\App\SessionController;
use App\Http\Controllers\App\UserController;
use App\Http\Controllers\App\TenantFilialController;
use App\Http\Controllers\App\CaixaController;
use App\Http\Controllers\App\LancamentoPadraoController;
use App\Http\Controllers\App\BankController;
use App\Http\Controllers\App\Cemiterio\CemeteryController;
use App\Http\Controllers\App\Cemiterio\SepulturaController;
use App\Http\Controllers\App\Contabilidade\ContabilidadeController;
use App\Http\Controllers\App\EntidadeFinanceiraController;
use App\Http\Controllers\App\EscrituraController;
use App\Http\Controllers\App\FielController;
use App\Http\Controllers\App\DizimoController;
use App\Http\Controllers\App\FavoriteRouteController;
use App\Http\Controllers\App\Filter\filterController;
use App\Http\Controllers\App\Filter\RebortController;
use App\Http\Controllers\App\Financeiro\ConciliacaoController;
use App\Http\Controllers\App\Financeiro\ContasFinanceirasController;
use App\Http\Controllers\App\Financeiro\CostCenterController;
use App\Http\Controllers\App\Financeiro\FormasPagamentoController;
use App\Http\Controllers\App\Financeiro\OfxController;
use App\Http\Controllers\App\Relatorios\ReciboController;
use App\Http\Controllers\App\Financeiro\TransacaoFinanceiraController;
use App\Http\Controllers\App\Frota\CarInsuranceController;
use App\Http\Controllers\App\NamePatrimonioController;
use App\Http\Controllers\App\Patrimonio\AvaliadorController;
use App\Http\Controllers\App\PatrimonioController;
use App\Http\Controllers\App\BemController;
use App\Http\Controllers\App\ReportController;
use App\Http\Controllers\App\PatrimonioAnexoController;
use App\Http\Controllers\App\TelaDeLoginController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\TenantController;
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
        $randomImage = null;
        $backgroundImage = null;

        try {
            if (class_exists(\App\Models\TelaDeLogin::class)) {
                 $randomImage = \App\Models\TelaDeLogin::where('status', 'ativo')
                    ->inRandomOrder()
                    ->first();

                if ($randomImage) {
                    $backgroundImage = $randomImage->imagem_caminho;
                }
            }
        } catch (\Exception $e) {
            // Fallback gracefully if table doesn't exist
        }

        return view('app.auth.login', compact('randomImage', 'backgroundImage'));
    });

    // Rota para o dashboard, acessível apenas por usuários autenticados e verificados
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware(['auth', 'check.user.active', 'verified'])
        ->name('dashboard');
    Route::get('/dashboard/missas-chart-data', [DashboardController::class, 'getMissasChartData'])
        ->middleware(['auth', 'check.user.active', 'verified'])
        ->name('dashboard.missas-chart-data');

    // Rotas de favoritos de módulos
    Route::middleware(['auth', 'verified'])->group(function () {
        Route::prefix('api/favorites')->name('api.favorites.')->group(function () {
            Route::get('/available', [FavoriteRouteController::class, 'availableModules'])
                ->name('available');
            Route::post('/', [FavoriteRouteController::class, 'store'])
                ->name('store');
            Route::delete('/{id}', [FavoriteRouteController::class, 'destroy'])
                ->name('destroy');
            Route::put('/reorder', [FavoriteRouteController::class, 'reorder'])
                ->name('reorder');
        });
    });

    // Rotas de perfil de usuário
    Route::prefix('/app/profile')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('profile.destroy');
        Route::match(['put', 'patch'], '/', [ProfileController::class, 'update'])->name('profile.update');
    });

    // Rota específica para avatars
    Route::get('/avatar/{id}', function ($id) {
        $filePath = Storage::disk('public')->path($id);

        // Verificar se o arquivo existe
        if (!file_exists($filePath)) {
            $defaultAvatar = public_path('assets/images/avatars/default-avatar.png');

            // Se o avatar padrão não existir, criar um simples
            if (!file_exists($defaultAvatar)) {
                // Criar diretório se não existir
                if (!is_dir(dirname($defaultAvatar))) {
                    mkdir(dirname($defaultAvatar), 0755, true);
                }

                // Criar um avatar padrão simples (SVG)
                $svgContent = '<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg">
                    <rect width="100" height="100" fill="#e5e7eb"/>
                    <circle cx="50" cy="35" r="15" fill="#9ca3af"/>
                    <path d="M20 80 Q50 60 80 80" stroke="#9ca3af" stroke-width="3" fill="none"/>
                </svg>';

                file_put_contents($defaultAvatar, $svgContent);
            }

            return response()->file($defaultAvatar);
        }

        return response()->file($filePath);
    })->name('avatar');

    // Rota para servir arquivos públicos
    Route::get('/file/{path}', function ($path) {
        $filePath = Storage::disk('public')->path($path);

        // Verificar se o arquivo existe
        if (!file_exists($filePath)) {
            // Se for um avatar, retornar avatar padrão
            if (str_contains($path, 'avatar') || is_numeric($path)) {
                $defaultAvatar = public_path('assets/images/avatars/default-avatar.png');

                // Se o avatar padrão não existir, criar um simples
                if (!file_exists($defaultAvatar)) {
                    // Criar diretório se não existir
                    if (!is_dir(dirname($defaultAvatar))) {
                        mkdir(dirname($defaultAvatar), 0755, true);
                    }

                    // Criar um avatar padrão simples (SVG)
                    $svgContent = '<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg">
                        <rect width="100" height="100" fill="#e5e7eb"/>
                        <circle cx="50" cy="35" r="15" fill="#9ca3af"/>
                        <path d="M20 80 Q50 60 80 80" stroke="#9ca3af" stroke-width="3" fill="none"/>
                    </svg>';

                    file_put_contents($defaultAvatar, $svgContent);
                }

                return response()->file($defaultAvatar);
            }

            // Para outros arquivos, retornar erro 404
            return response()->json(['error' => 'Arquivo não encontrado'], 404);
        }

        return response()->file($filePath);
    })->where('path', '.*')->name('file');



    // Grupo de rotas protegido pelo middleware 'auth', 'check.user.active', 'ensureUserHasAccess' e 'password.change.required'
    Route::middleware(['auth', 'check.user.active', 'ensureUserHasAccess', 'password.change.required'])->group(function () {

        // Rota para gerar código de acesso mobile (para o tenant atual)
        Route::post('/generate-app-code', [TenantController::class, 'generateCode'])->name('tenant.generate-app-code');

            // Rota que fornecerá os dados em formato JSON para a DataTable
    // Usaremos POST para enviar os filtros de forma mais robusta
    Route::post('/reports/financial-data', [ReportController::class, 'getFinancialDataServerSide'])->name('reports.financial.data');

        // Rotas acessíveis apenas para administradores globais
        Route::middleware(['role:global'])->group(function () {
            Route::resource('filial', TenantFilialController::class);
            Route::resource('caixa', CaixaController::class);
            Route::resource('users', UserController::class);
            Route::resource('telaLogin', TelaDeLoginController::class);

            Route::resource('formas-pagamento', FormasPagamentoController::class);
        });

        Route::get('/session/switch-company/{company}', [SessionController::class, 'switchCompany'])->name('session.switch-company');
        Route::delete('/profile/sessions/{sessionId}', [SessionController::class, 'destroy'])->name('profile.sessions.destroy');

        Route::get('/company/edit', [CompanyController::class, 'edit'])->name('company.edit');
        Route::put('/company', [CompanyController::class, 'update'])->name('company.update');

        // Rotas de módulos
        Route::get('/modules', [ModuleController::class, 'index'])->name('modules.list');
        Route::get('/modules/data', [ModuleController::class, 'getData'])->name('modules.data');
        Route::post('/modules', [ModuleController::class, 'store'])->name('modules.store');
        Route::put('/modules/{module}', [ModuleController::class, 'update'])->name('modules.update');
        Route::delete('/modules/{module}', [ModuleController::class, 'destroy'])->name('modules.destroy');

        // Rotas de permissões
        Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.list');
        Route::get('/permissions/data', [PermissionController::class, 'getData'])->name('permissions.data');
        Route::post('/permissions', [PermissionController::class, 'store'])->name('permissions.store');
        Route::put('/permissions/{permission}', [PermissionController::class, 'update'])->name('permissions.update');
        Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy');

        // Rotas acessíveis apenas para administradores
        Route::middleware(['role:admin'])->group(function () {
            Route::resource('filial', TenantFilialController::class);
            Route::resource('caixa', CaixaController::class);
            Route::get('app/financeiro/caixa/list', [CaixaController::class, 'list'])->name('caixa.list');
            Route::resource('users', UserController::class);
            // Rota dedicada APENAS para atualizar as permissões de um usuário
            Route::put('/users/{user}/roles', [UserController::class, 'updateRoles'])->name('users.roles.update');
            Route::put('/users/{user}/permissions', [UserController::class, 'updatePermissions'])->name('users.permissions.update');
            Route::post('/users/{user}/assign-all-permissions', [UserController::class, 'assignAllPermissions'])->name('users.assign-all-permissions');
            Route::put('/users/{user}/filiais', [UserController::class, 'updateFiliais'])->name('users.filiais.update');
            // Rota dedicada APENAS para ativar ou desativar um usuário
            Route::put('/users/{user}/status', [UserController::class, 'updateStatus'])->name('users.status.update');
            Route::put('/users/{user}/email', [UserController::class, 'updateEmail'])->name('users.email.update');
            Route::post('/users/{user}/verify-password', [UserController::class, 'verifyPassword'])->name('users.password.verify');
            Route::put('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.password.reset');

            // Rotas para alteração obrigatória de senha
            Route::get('/password/change', [UserController::class, 'showPasswordChange'])->name('password.change.show');
            Route::post('/password/change', [UserController::class, 'updatePasswordChange'])->name('password.change');

            Route::resource('company', CompanyController::class)->except(['edit', 'update']);

            Route::post('/filter', [RebortController::class, 'generateReport']);

            Route::prefix('contabilidade')->name('contabilidade.')->group(function () {

                // Rota principal que exibe a página com as abas.
                Route::get('/', [ContabilidadeController::class, 'index'])->name('index');

                // Rotas para o CRUD do Plano de Contas.
                // O Laravel criará rotas como: /contabilidade/plano-contas, /contabilidade/plano-contas/create, etc.
                Route::post('plano-contas/import', [ChartOfAccountController::class, 'import'])->name('plano-contas.import');
                Route::post('plano-contas/export', [ChartOfAccountController::class, 'export'])->name('plano-contas.export');
                Route::resource('plano-contas', ChartOfAccountController::class)->names('plano-contas');

                // Rotas para o CRUD do Mapeamento (DE/PARA).
                Route::resource('mapeamento', AccountMappingController::class)->only([
                    'index',
                    'store',
                    'destroy'
                ])->names('mapeamento');
            });
        });

        // Rotas acessíveis apenas para administradores e usuários específicos
        Route::middleware(['role:admin_user'])->group(function () {
            Route::resource('filial', TenantFilialController::class);
            Route::resource('caixa', CaixaController::class);
            Route::get('/lancamentoPadrao/data', [LancamentoPadraoController::class, 'getData'])->name('lancamentoPadrao.data');
            Route::post('/lancamentoPadrao/validate-field', [LancamentoPadraoController::class, 'validateField'])->name('lancamentoPadrao.validate-field');
            Route::get('/lancamentoPadrao/download-template', [LancamentoPadraoController::class, 'downloadTemplate'])->name('lancamentoPadrao.download-template');
            Route::post('/lancamentoPadrao/upload-template', [LancamentoPadraoController::class, 'uploadTemplate'])->name('lancamentoPadrao.upload-template');
            Route::resource('lancamentoPadrao', LancamentoPadraoController::class);
            Route::resource('cadastroBancos', BankController::class);
            Route::resource('users', UserController::class);
        });

        // Rotas para gerenciamento de centros de custo
        // IMPORTANT: Custom routes must come BEFORE resource routes to avoid conflicts
        Route::get('/costCenter/modal/data', [CostCenterController::class, 'getDataForModal'])->name('costCenter.modal.data');
        Route::get('/costCenter/contas-financeiras', [CostCenterController::class, 'getContasFinanceiras'])->name('costCenter.contas.financeiras');
        Route::resource('costCenter', CostCenterController::class);

        // Rotas acessíveis apenas para usuários comuns
        Route::middleware(['role:user'])->group(function () {
            Route::delete('/caixas/{id}', [CaixaController::class, 'destroySelected'])->name('caixas.destroySelected');
            Route::resource('caixa', CaixaController::class);
            Route::get('/charts/despesas', [CaixaController::class, 'getDespesasChartData'])->name('charts.despesas.data');

            // Rotas AJAX para funcionalidades da interface financeira
            Route::get('/financeiro/data', [CaixaController::class, 'getFinancialData'])->name('financeiro.data');
            Route::post('/financeiro/mark-as-paid', [CaixaController::class, 'markAsPaid'])->name('financeiro.mark-as-paid');
            Route::post('/financeiro/export', [CaixaController::class, 'export'])->name('financeiro.export');
            Route::delete('/financeiro/delete', [CaixaController::class, 'deleteEntries'])->name('financeiro.delete');
            Route::get('/financeiro/filter-options', [CaixaController::class, 'getFilterOptions'])->name('financeiro.filter-options');
            Route::get('/financeiro/saldos-mensais', [CaixaController::class, 'getSaldosMensais'])->name('financeiro.saldos-mensais');

            Route::get('app/financeiro/banco/list', [BancoController::class, 'list'])->name('banco.list');
            Route::get('/banco/chart-data', [BancoController::class, 'getChartData'])->name('banco.chart.data');
            Route::get('/banco/fluxo-chart-data', [BancoController::class, 'getFluxoBancoChartData'])->name('banco.fluxo.chart.data');
            Route::get('/banco/transacoes-data', [BancoController::class, 'getTransacoesData'])->name('banco.transacoes.data');

            Route::get('/financeiro/transacao/{id}/detalhes', [BancoController::class, 'getDetalhes'])
                ->name('financeiro.transacao.detalhes');
            Route::get('/financeiro/caixa/{id}/detalhes', [CaixaController::class, 'getDetalhes'])
                ->name('financeiro.caixa.detalhes');
            Route::get('/banco/conciliacoes-pendentes', [BancoController::class, 'getConciliacoesPendentes'])->name('banco.conciliacoes.pendentes');
            Route::post('/banco/relatorio/gerar', [BancoController::class, 'gerarRelatorio'])->name('banco.relatorio.gerar');
            Route::resource('banco', BancoController::class);
            Route::resource('anexos', AnexoController::class);
            Route::resource('modulosAnexos', ModulosAnexosController::class);
            Route::resource('post', PostController::class);
            Route::get('/lancamento_padrao/tipo/{tipo}', [LancamentoPadraoController::class, 'getLancamentosByTipo']);



            // *** Editar Patrimônio ***
            Route::resource('patrimonio', PatrimonioController::class);
            Route::post('/save-location', [PatrimonioController::class, 'updateLocation'])->name('patrimonios.updateLocation');

            // *** Rotas para Bens (Novo Sistema) ***
            Route::resource('bem', BemController::class);

            // *** Rotas resource para Contas Financeiras ***
            Route::resource('contas-financeiras', ContasFinanceirasController::class);


            Route::resource('escritura', EscrituraController::class);
            Route::resource('patrimonioAnexo', PatrimonioAnexoController::class);
            Route::get('patrimonios/filtrar', [PatrimonioController::class, 'filtrar'])->name('patrimonio.filtrar');
            Route::get('patrimonios/imoveis', [PatrimonioController::class, 'imoveis'])->name('patrimonio.imoveis');
            Route::get('patrimonios/bens-moveis', [PatrimonioController::class, 'bensMoveis'])->name('patrimonio.bens-moveis');
            Route::get('patrimonios/veiculos', [PatrimonioController::class, 'veiculos'])->name('patrimonio.veiculos');
            Route::resource('namePatrimonio', NamePatrimonioController::class);
            Route::post('/validar-num-foro', [NamePatrimonioController::class, 'validarNumForo']);
            Route::get('app/financeiro/caixa/list', [CaixaController::class, 'list'])->name('caixa.list');

            Route::get('/patrimonios/search', [PatrimonioController::class, 'search'])->name('patrimonios.search');
            Route::get('/patrimonios/grafico', [PatrimonioController::class, 'grafico']);
            Route::get('/report/shipping', [ReportController::class, 'shippingReport'])->name('report.shipping');
            Route::get('/report/shipping/data', [ReportController::class, 'shippingReportData'])->name('report.shipping.data');
            Route::resource('cemiterio', CemeteryController::class);
            Route::resource('sepultura', SepulturaController::class);

            Route::resource('avaliador', AvaliadorController::class);

            Route::post('/upload-ofx', [OfxController::class, 'upload'])->name('upload.ofx');

            Route::patch('/conciliacao/{id}/ignorar', [ConciliacaoController::class, 'ignorar'])->name('conciliacao.ignorar');
            Route::get('/conciliacao', [ConciliacaoController::class, 'index'])->name('conciliacao.index');
            Route::get('/conciliacao/comparar/{id}', [ConciliacaoController::class, 'comparar'])->name('conciliacao.comparar');
            Route::post('/conciliacao/conciliar', [ConciliacaoController::class, 'conciliar'])->name('conciliacao.conciliar');
            Route::post('/conciliacao', [ConciliacaoController::class, 'pivot'])->name('conciliacao.pivot');
            Route::put('/transacoes-financeiras/{id}', [ConciliacaoController::class, 'update'])->name('conciliacao.update');
            Route::get('/conciliacao/contas-disponiveis', [ConciliacaoController::class, 'contasDisponiveis'])->name('conciliacao.contas-disponiveis');
    Route::post('/conciliacao/processar-missas', [ConciliacaoController::class, 'processarConciliacaoMissas'])->name('conciliacao.processar-missas');
    Route::get('/conciliacao/missas', [ConciliacaoController::class, 'getConciliacoesMissas'])->name('conciliacao.missas');
    Route::get('/conciliacao/candidatas', [ConciliacaoController::class, 'getTransacoesCandidatas'])->name('conciliacao.candidatas');
    Route::post('/conciliacao/confirmar-missa', [ConciliacaoController::class, 'confirmarMissa'])->name('conciliacao.confirmar-missa');
    Route::post('/conciliacao/rejeitar-missa', [ConciliacaoController::class, 'rejeitarMissa'])->name('conciliacao.rejeitar-missa');
            Route::post('/conciliacao/transferir', [ConciliacaoController::class, 'transferir'])->name('conciliacao.transferir');




            // Grupo de rotas para relatórios
            Route::prefix('relatorios')->group(function () {

                Route::resource('recibos', ReciboController::class);
                Route::post('/recibos/gerar/{transacao}', [ReciboController::class, 'gerarRecibo'])->name('gerarRecibo');
                Route::get('/recibo/imprimir/{id}', [ReciboController::class, 'imprimirRecibo'])->name('recibo.imprimir');


                Route::get('/prestacao-de-contas', [PrestacaoDeContaController::class, 'index'])
                    ->name('relatorios.prestacao.de.contas');
                Route::post('bill/{id}/print', [PrestacaoDeContaController::class, 'print'])->name('bill.print');

                Route::get('/prestacao-de-contas/pdf', [PrestacaoDeContaController::class, 'gerarPdf'])
                    ->name('relatorios.prestacao.de.contas.gerar');

                Route::get('/conciliacao-bancaria/pdf', [ConciliacaoController::class, 'gerarPdf'])
                    ->name('relatorios.conciliacao.bancaria.gerar');

                // Rotas assíncronas de PDF
                Route::post('/conciliacao-bancaria/pdf-async', [ConciliacaoController::class, 'gerarPdfAsync'])
                    ->name('relatorios.conciliacao.bancaria.gerar.async');

                Route::get('/boletim-financeiro/pdf', [BoletimFinanceiroController::class, 'gerarPdf'])
                    ->name('relatorios.boletim.financeiro.gerar');

                Route::post('/boletim-financeiro/pdf-async', [BoletimFinanceiroController::class, 'gerarPdfAsync'])
                    ->name('relatorios.boletim.financeiro.gerar.async');

                // Rota compartilhada para verificar status de PDF
                Route::get('/pdf/status/{id}', [ConciliacaoController::class, 'checkPdfStatus'])
                    ->name('pdf.status');

                Route::post('/filter', [PrestacaoDeContaController::class, 'generateReport']);

                Route::resource('fieis', FielController::class);
                Route::get('fieis/charts/data', [FielController::class, 'getChartData'])->name('fieis.charts.data');
                Route::post('fieis/relatorio/pdf', [FielController::class, 'relatorioPdf'])->name('fieis.relatorio.pdf');

                Route::resource('dizimos', DizimoController::class);

                Route::resource('entidades', EntidadeFinanceiraController::class);

                Route::post('entidades/{id}/movimentacao', [EntidadeFinanceiraController::class, 'addMovimentacao'])
                    ->name('entidades.movimentacao');

                Route::get('entidades/{id}/json', [EntidadeFinanceiraController::class, 'showJson'])
                    ->name('entidades.show.json');

                Route::resource('car_insurance', CarInsuranceController::class);

                Route::post('car_insurance/{id}/sell', [CarInsuranceController::class, 'sell'])
                    ->name('car_insurance.sell');

                Route::resource('transacoes-financeiras', TransacaoFinanceiraController::class);
                Route::get('/transacao-financeira/grafico', [TransacaoFinanceiraController::class, 'grafico'])
                    ->name('transacao.grafico');

                // Rota dedicada para fornecer dados para o gráfico do dashboard financeiro
                Route::get('/charts/financial-summary', [CaixaController::class, 'getFinancialSummaryChartData'])->name('charts.financial_summary.data');

                // Rota que fornecerá os dados em formato JSON para a DataTable
                Route::get('/reports/financial-data', [ReportController::class, 'getFinancialData'])->name('reports.financial.data');

                // NOVA ROTA: Fornece os dados brutos para a análise da IA
                Route::post('/reports/gemini-analysis', [ReportController::class, 'getDataForGeminiAnalysis'])->name('reports.gemini.analysis');

                // NOVA ROTA: Fornece os dados brutos para a análise da IA
                Route::post('/reports/gemini-analysis', [ReportController::class, 'getDataForGeminiAnalysis'])->name('reports.gemini.analysis');


                Route::get('/transacoes/data', [TransacaoFinanceiraController::class, 'getData'])
                    ->name('transacoes.data');

                Route::get('/patrimonios/imprimir', [PatrimonioController::class, 'imprimirPDF'])->name('patrimonio.imprimir');
            });
        });
    });

    // Autenticação específica para tenants
    require __DIR__ . '/tenant-auth.php';
});
