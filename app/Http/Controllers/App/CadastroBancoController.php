<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\CadastroBanco;
use App\Models\LancamentoPadrao;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CadastroBancoController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bancos = CadastroBanco::geCadastroBanco(); // Chama o método para obter os bancos

        return view('app.cadastros.bancos.index', [
            'bancos' => $bancos,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $subsidiaryId = User::getCompany();

        // Validação dos dados
        $validator = Validator::make($request->all(), [
            'banco' => 'required|string|max:255', // Nome do banco, obrigatório e deve ser uma string
            'agencia' => 'required|string|max:10', // Número da agência, obrigatório e até 10 caracteres
            'conta' => 'required|string|max:20', // Número da conta, obrigatório e até 20 caracteres
            'digito' => 'max:5',
            'account_type' => 'required|string|in:corrente,poupanca,aplicacao', // Tipo de conta, obrigatório e deve ser um dos tipos permitidos
            'description' => 'string|min:3|max:500'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = auth()->user(); // Usuário autenticado

        // Dados validados
        $validatedData = $validator->validated();
        $validatedData['company_id'] = $subsidiaryId->company_id;
        $validatedData['created_by'] = $user->id; // ID do usuário autenticado
        $caixa = CadastroBanco::create($validatedData);

        return redirect()->route('cadastroBancos.index');
    }

    /**
     * Display the specified resource.
     */
    public function show($tenant, $filename, $id)
    {
        $bancos = CadastroBanco::with('user')->findOrFail($id);

        return redirect()->route('cadastroBancos.index', compact('bancos'));

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $banco = CadastroBanco::findOrFail($id);
        return response()->json($banco);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $banco = CadastroBanco::findOrFail($id);

        // Validação
        $validated = $request->validate([
            'banco' => 'required',
            'conta' => 'required',
            'agencia' => 'required',
            'account_type' => 'required',
            'description' => 'nullable',
        ]);

        // Atualização do banco existente
        $banco->update($validated);

        return redirect()->route('cadastroBancos.index')->with('success', 'Banco atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            // Localize o registro com base no ID fornecido
            $banco = CadastroBanco::findOrFail($id);

            // Exclua o registro
            $banco->delete();

            // Retorne uma resposta JSON de sucesso
            return response()->json([
                'status' => 'success',
                'message' => 'Banco excluído com sucesso!'
            ], 200);

        } catch (\Exception $e) {
            // Em caso de erro, retorne uma resposta JSON com erro
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao excluir o banco: ' . $e->getMessage()
            ], 500);
        }
    }


}
