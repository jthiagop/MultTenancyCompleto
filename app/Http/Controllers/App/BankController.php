<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\CadastroBanco;
use App\Models\LancamentoPadrao;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BankController extends Controller // SUGESTÃO: Renomear o arquivo para BankController.php
{
    /**
     * Exibe a lista de todas as instituições bancárias cadastradas.
     * Esta é agora uma lista global, não filtrada por empresa.
     */
    public function index()
    {
        $bancos = Bank::orderBy('name')->get();

        return view('app.cadastros.bancos.index', compact('bancos'));
    }

    /**
     * Salva uma nova instituição bancária no banco de dados.
     */
    public function store(Request $request)
    {
        // A validação agora é muito mais simples.
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:banks,name',
            'logo_path' => 'nullable|string|max:255', // Se você permitir o upload do logo
        ]);

        Bank::create($validatedData);

        return redirect()->route('bancos.index')->with('success', 'Banco cadastrado com sucesso!');
    }

    /**
     * Exibe o formulário para editar uma instituição bancária.
     * Geralmente usado para carregar dados em um modal via AJAX.
     */
    public function edit(Bank $banco) // Usando Route Model Binding
    {
        return response()->json($banco);
    }

    /**
     * Atualiza uma instituição bancária existente.
     */
    public function update(Request $request, Bank $banco) // Usando Route Model Binding
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:banks,name,' . $banco->id,
            'logo_path' => 'nullable|string|max:255',
        ]);

        $banco->update($validatedData);

        return redirect()->route('bancos.index')->with('success', 'Banco atualizado com sucesso!');
    }

    /**
     * Remove uma instituição bancária do banco de dados.
     */
    public function destroy(Bank $banco) // Usando Route Model Binding
    {
        try {
            // Lógica para verificar se o banco não está em uso antes de excluir (opcional, mas recomendado)
            if ($banco->contas()->exists()) {
                 return response()->json([
                    'status' => 'error',
                    'message' => 'Não é possível excluir este banco, pois ele já está associado a uma ou mais contas.'
                ], 409); // 409 Conflict
            }

            $banco->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Banco excluído com sucesso!'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao excluir o banco: ' . $e->getMessage()
            ], 500);
        }
    }
}
