<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\BankStatementImport;
use App\Models\BankStatementEntry;
use App\Models\BankConfig;
use App\Models\EntidadeFinanceira;
use App\Services\BankStatementImportService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class BankStatementController extends Controller
{
    protected $importService;

    public function __construct(BankStatementImportService $importService)
    {
        $this->importService = $importService;
    }

    /**
     * Listagem de extratos importados
     */
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;

        // Buscar importações recentes com paginação
        $imports = BankStatementImport::where('company_id', $companyId)
            ->with(['importedBy', 'bankConfig'])
            ->orderBy('imported_at', 'desc')
            ->paginate(20);

        // Estatísticas gerais
        $totalEntries = BankStatementEntry::where('company_id', $companyId)->count();
        $pendingReconciliation = BankStatementEntry::where('company_id', $companyId)
            ->pendente()
            ->count();

        return view('app.financeiro.banco.extratos.index', compact(
            'imports',
            'totalEntries',
            'pendingReconciliation'
        ));
    }

    /**
     * Buscar extrato do BB via API
     */
    public function fetch(Request $request)
    {
        $request->validate([
            'bank_account_id' => 'required|exists:entidade_financeiras,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        try {
            $bankAccount = EntidadeFinanceira::findOrFail($request->bank_account_id);
            
            // Verificar permissão
            if ($bankAccount->company_id !== auth()->user()->company_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não tem permissão para acessar esta conta.'
                ], 403);
            }

            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);

            // Validar período máximo (31 dias conforme doc BB)
            if ($endDate->diffInDays($startDate) > 31) {
                return response()->json([
                    'success' => false,
                    'message' => 'O período máximo permitido é de 31 dias.'
                ], 422);
            }

            $import = $this->importService->importFromBBApi($bankAccount, $startDate, $endDate);

            return response()->json([
                'success' => true,
                'message' => 'Extrato importado com sucesso!',
                'data' => [
                    'import_id' => $import->id,
                    'total_entries' => $import->entries()->count(),
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Erro ao importar extrato BB', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao importar extrato: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exibir detalhes de uma importação
     */
    public function show($id)
    {
        $companyId = auth()->user()->company_id;

        $import = BankStatementImport::where('company_id', $companyId)
            ->with(['bankConfig', 'importedBy', 'entries'])
            ->findOrFail($id);

        return view('app.financeiro.banco.extratos.show', compact('import'));
    }

    /**
     * Sincroniza automaticamente os últimos 7 dias do Banco do Brasil
     * Busca a primeira conta bancária ativa com configuração BB e sincroniza
     */
    public function sync(Request $request)
    {
        try {
            $companyId = session('active_company_id') ?? Auth::user()->company_id;

            // Buscar configuração BB ativa (sem filtrar por company_id por enquanto)
            $bankConfig = BankConfig::where('ativo', true)->firstOrFail();

            // Buscar entidade financeira relacionada (pela agência e conta)
            // Por enquanto, sem filtrar por company_id para teste
            $entidadeFinanceira = EntidadeFinanceira::where('agencia', $bankConfig->agencia)
                ->where('conta', $bankConfig->conta_corrente)
                ->where('tipo', 'banco')
                ->first();

            // Obter company_id (da entidade encontrada, sessão ou padrão)
            if ($entidadeFinanceira) {
                $companyId = $entidadeFinanceira->company_id ?? (session('active_company_id') ?? Auth::user()->company_id ?? 1);
                $bankAccountId = $entidadeFinanceira->id;
            } else {
                // Se não encontrar entidade, usar company_id da sessão ou padrão
                $companyId = session('active_company_id') ?? Auth::user()->company_id ?? 1;
                $bankAccountId = 1; // ID padrão temporário
            }

            // Criar um objeto BankAccount temporário para usar com o service
            // (O service espera um objeto com bankConfig e company_id)
            $bankAccount = (object) [
                'id' => $bankAccountId,
                'company_id' => $companyId,
                'bankConfig' => $bankConfig,
            ];

            // Período: últimos 7 dias (mesmo formato usado no teste que funciona)
            $startDate = now()->subDays(7);
            $endDate = now();

            // Importar usando o service existente
            $import = $this->importService->importFromBBApi($bankAccount, $startDate, $endDate);

            $totalEntries = $import->entries()->count();

            return redirect()->route('bank-statements.index')
                ->with('success', "Sincronização concluída! {$totalEntries} lançamento(s) importado(s) do período de {$startDate->format('d/m/Y')} a {$endDate->format('d/m/Y')}.");

        } catch (\Exception $e) {
            \Log::error('Erro ao sincronizar extrato BB', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('bank-statements.index')
                ->with('error', 'Erro ao sincronizar: ' . $e->getMessage());
        }
    }
}
