<?php

namespace App\Http\Controllers\App\Contabilidade;

use App\Http\Controllers\Controller;
use App\Models\Contabilide\AccountMapping;
use App\Models\Contabilide\ChartOfAccount;
use App\Models\LancamentoPadrao;
use Illuminate\Http\Request;

class AccountMappingController extends Controller
{
    /**
     * Exibe a lista de mapeamentos contábeis.
     */
    public function index()
    {
        // Busca os dados necessários para a view, todos filtrados pela empresa ativa.
        $mapeamentos = AccountMapping::with(['lancamentoPadrao', 'contaDebito', 'contaCredito'])
                                     ->forActiveCompany()
                                     ->get();
        
        // Dados para popular os dropdowns do modal de criação/edição.
        $lancamentosPadrao = LancamentoPadrao::forActiveCompany()->get();
        $contas = ChartOfAccount::forActiveCompany()->orderBy('code')->get();

        return view('app.contabilidade.mapeamento.index', compact('mapeamentos', 'lancamentosPadrao', 'contas'));
    }

    /**
     * Salva um novo mapeamento no banco de dados.
     */
    public function store(Request $request)
    {
        $activeCompanyId = session('active_company_id');
        if (!$activeCompanyId) {
            return back()->with('error', 'Nenhuma empresa selecionada.');
        }

        $validatedData = $request->validate([
            'lancamento_padrao_id' => 'required|integer|exists:lancamento_padraos,id',
            'conta_debito_id' => 'required|integer|exists:chart_of_accounts,id',
            'conta_credito_id' => 'required|integer|exists:chart_of_accounts,id',
        ]);

        $validatedData['company_id'] = $activeCompanyId;

        // Evita duplicatas
        AccountMapping::firstOrCreate(
            [
                'company_id' => $activeCompanyId,
                'lancamento_padrao_id' => $validatedData['lancamento_padrao_id'],
            ],
            $validatedData
        );

        return redirect()->back()->with('success', 'Mapeamento salvo com sucesso!');
    }

    /**
     * Remove um mapeamento do banco de dados.
     */
    public function destroy($id)
    {
        $mapeamento = AccountMapping::forActiveCompany()->findOrFail($id);
        $mapeamento->delete();

        return redirect()->back()->with('success', 'Mapeamento excluído com sucesso!');
    }
}
