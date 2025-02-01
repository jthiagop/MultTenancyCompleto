<?php

namespace App\Http\Controllers\App\Cemiterio;

use App\Http\Controllers\Controller;
use App\Models\Cemiterio\Sepultura;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class SepulturaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        $validatedData = $request->validate([
            'codigo_sepultura' => 'required|string|max:255',
            'localizacao' => 'nullable|string|max:255',
            'tipo' => 'required|string|max:255',
            'tamanho' => 'string', // Garantir que seja um número válido
            'data_aquisicao' => 'nullable', // Validação para o formato da data
            'status' => 'required|string|max:255',
        ]);


        try {
            // Obtenção do usuário autenticado usando Auth::user()
            $user = Auth::user();

            $validatedData['company_id'] = $subsidiaryId->company_id;
            $validatedData['tamanho'] = str_replace(',', '.', str_replace('.', '', $validatedData['tamanho']));

            // Criação da nova sepultura
            $sepultura = new Sepultura();
            $sepultura->company_id = $validatedData['company_id'];  // Usando o ID da empresa do usuário
            $sepultura->codigo_sepultura = $validatedData['codigo_sepultura'];
            $sepultura->localizacao = $validatedData['localizacao'];
            $sepultura->tipo = $validatedData['tipo'];
            $sepultura->tamanho = $validatedData['tamanho'];
            $sepultura->data_aquisicao =  $validatedData['data_aquisicao']; // Usando a data formatada
            $sepultura->status = $validatedData['status'];

            // Usando o Auth::user() para pegar as informações do usuário autenticado
            $sepultura->created_by = $user->id;
            $sepultura->created_by_name = $user->name;
            $sepultura->updated_by = $user->id;
            $sepultura->updated_by_name = $user->name;
            // Salvando a sepultura no banco de dados
            $sepultura->save();

            // Retornar uma resposta ou redirecionar
            return redirect()->back()->with('success', 'Sepultura cadastrada com sucesso!');
        } catch (Exception $e) {
            // Captura qualquer exceção e exibe a mensagem de erro
            return redirect()->back()->with('error', 'Erro ao cadastrar sepultura: ' . $e->getMessage());
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validar os dados recebidos
        $validatedData = $request->validate([
            'codigo_sepultura' => 'required|string|max:255',
            'localizacao' => 'required|string|max:255',
            'tipo' => 'required|string|max:255',
            'tamanho' => 'nullable|string',  // Exemplo de validação para um campo numérico
            'data_aquisicao' => 'required|date',  // Validação para data
            'status' => 'required|string',
        ]);


        try {
            // Encontrar a sepultura pelo ID
            $sepultura = Sepultura::findOrFail($id);

            // Capturar o usuário autenticado
            $user = Auth::user();
            // Atualizar os dados da sepultura
            $sepultura->codigo_sepultura = $validatedData['codigo_sepultura'];
            $sepultura->localizacao = $validatedData['localizacao'];
            $sepultura->tipo = $validatedData['tipo'];
            $sepultura->tamanho = $validatedData['tamanho'] ?? $sepultura->tamanho;  // Se não for enviado, mantém o valor antigo
            $sepultura->data_aquisicao = $validatedData['data_aquisicao'];
            $sepultura->status = $validatedData['status'];

            // Atualizar os campos de quem fez a alteração
            $sepultura->updated_by = $user->id;
            $sepultura->updated_by_name = $user->name;

            // Salvar as alterações no banco de dados
            $sepultura->save();

            // Retornar uma resposta ou redirecionar com sucesso
            return redirect()->back()->with('success', 'Sepultura atualizada com sucesso!');
        } catch (\Exception $e) {
            // Captura qualquer exceção e exibe a mensagem de erro
            return redirect()->back()->with('error', 'Erro ao atualizar sepultura: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function search(Request $request)
    {
        $query = $request->input('query');

        $sepulturas = Sepultura::where('codigo_sepultura', 'like', "%{$query}%")
            ->orWhere('localizacao', 'like', "%{$query}%")
            ->orWhere('tipo', 'like', "%{$query}%")
            ->get();

        return response()->json($sepulturas);
    }
}
