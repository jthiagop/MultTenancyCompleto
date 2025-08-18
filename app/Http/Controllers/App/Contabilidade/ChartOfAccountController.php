<?php

namespace App\Http\Controllers\App\Contabilidade;

use App\Http\Controllers\Controller;
use App\Models\Contabilide\ChartOfAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ChartOfAccountController extends Controller
{
    /**
     * Exibe a lista de contas do plano de contas.
     */
    public function index()
    {
        // Busca todas as contas da empresa ativa para a listagem e para o dropdown do modal.
        $contas = ChartOfAccount::forActiveCompany()->orderBy('code')->get();

        return view('app.contabilidade.index', compact('contas'));
    }

    /**
     * Salva uma nova conta contábil no banco de dados.
     */
    public function store(Request $request)
    {
        $activeCompanyId = session('active_company_id');
        if (!$activeCompanyId) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Nenhuma empresa selecionada.'], 403);
            }
            flash()->error('Nenhuma empresa selecionada.');
            return redirect()->back();
        }

        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'type' => 'required|in:ativo,passivo,patrimonio_liquido,receita,despesa',
            'parent_id' => 'nullable|integer|exists:chart_of_accounts,id',
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validatedData = $validator->validated();
        $validatedData['company_id'] = $activeCompanyId;

        // --- INÍCIO DA ADIÇÃO DO TRY-CATCH ---
        try {
            $conta = ChartOfAccount::create($validatedData);

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Conta contábil criada com sucesso!',
                    'conta' => $conta
                ], 201);
            }

            return redirect()->back()->with('success', 'Conta contábil criada com sucesso!');
        } catch (\Exception $e) {
            // Registra o erro detalhado no arquivo de log para o desenvolvedor
            Log::error('Erro ao criar conta contábil: ' . $e->getMessage());

            // Retorna uma resposta de erro amigável para o usuário
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Ocorreu um erro inesperado ao salvar a conta.'], 500);
            }

            return redirect()->back()->with('error', 'Ocorreu um erro inesperado ao salvar a conta. Por favor, tente novamente.');
        }
        // --- FIM DA ADIÇÃO DO TRY-CATCH ---
    }

    /**
     * Atualiza uma conta contábil existente.
     */
    public function update(Request $request, $id)
    {
        $conta = ChartOfAccount::forActiveCompany()->findOrFail($id);

        $validatedData = $request->validate([
            'code' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'type' => 'required|in:ativo,passivo,patrimonio_liquido,receita,despesa',
            'parent_id' => 'nullable|integer|exists:chart_of_accounts,id',
        ]);

        $conta->update($validatedData);

        return redirect()->route('plano-contas.index')->with('success', 'Conta contábil atualizada com sucesso!');
    }

    /**
     * Remove uma conta contábil.
     */
    public function destroy($id)
    {
        $conta = ChartOfAccount::forActiveCompany()->findOrFail($id);

        // Lógica para impedir a exclusão se a conta tiver filhas (opcional, mas recomendado)
        if ($conta->children()->exists()) {
            return redirect()->back()->with('error', 'Não é possível excluir uma conta que possui sub-contas.');
        }

        $conta->delete();

        return redirect()->route('plano-contas.index')->with('success', 'Conta contábil excluída com sucesso!');
    }
}
