<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Anexo;
use App\Models\Banco;
use App\Models\Caixa;
use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AnexoController extends Controller
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
        // Registrar todos os dados recebidos no log (para depuração)
        Log::info('Dados recebidos no request:', $request->all());

        if ($request->has('caixa_id')) {
            Log::info('Caixa ID:', ['caixa_id' => $request->input('caixa_id')]);
        } elseif ($request->has('banco_id')) {
            Log::info('Banco ID:', ['banco_id' => $request->input('banco_id')]);
        } else {
            Log::warning('Nenhum ID foi enviado na requisição.');
        }


        // Verifica se um arquivo foi enviado
        if ($request->hasFile('file')) {
            $file = $request->file('file');

            // Armazenar o arquivo no diretório 'anexos' dentro de 'public'
            $caminhoArquivo = $file->store('anexos', 'public');

            // Salvar os detalhes do arquivo no banco de dados
            $anexo = Anexo::create([
                'caixa_id' => $request->input('caixa_id'), // ID do caixa relacionado
                'banco_id' => $request->input('banco_id'), // ID opcional do banco
                'nome_arquivo' => $file->getClientOriginalName(),
                'caminho_arquivo' => $caminhoArquivo, // Caminho do arquivo armazenado
                'size' => $file->getSize(), // Tamanho do arquivo
                'created_by' => auth()->id(), // ID do usuário autenticado
                'updated_by' => auth()->id(), // ID do usuário autenticado
            ]);

            // Retornar resposta de sucesso
            return response()->json([
                'message' => 'Arquivo enviado com sucesso!',
                'file_path' => $caminhoArquivo,
            ], 200);
        }

        // Caso nenhum arquivo tenha sido enviado
        return response()->json(['message' => 'Nenhum arquivo foi enviado'], 400);
    }


    public function update(Request $request, $id)
{
    dd($request);
    // Validação do arquivo
    $validator = Validator::make($request->all(), [
        'files.*' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    // Obtém o usuário autenticado
    $user = auth()->user();

    // Tenta encontrar o registro `Caixa` e `Banco`
    $caixa = Caixa::find($id);
    $banco = Banco::find($id);

    if (!$caixa || !$banco) {
        return response()->json(['error' => 'Registro não encontrado'], 404);
    }

    // Processa os novos arquivos anexos, se houver
    if ($request->hasFile('files')) {
        foreach ($request->file('files') as $anexo) {
            $anexoName = time() . '_' . $anexo->getClientOriginalName();
            $anexoPath = $anexo->storeAs('anexos', $anexoName, 'public');

            Anexo::create([
                'caixa_id' => $caixa->id,
                'banco_id' => $banco->id,
                'nome_arquivo' => $anexoName,
                'caminho_arquivo' => $anexoPath,
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);
        }
    }

    return response()->json(['success' => 'Arquivos enviados com sucesso!'], 200);
}





    /**
     * Display the specified resource.
     */
    public function show(Anexo $anexo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Anexo $anexo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $anexo = Anexo::findOrFail($id);

        // Verifica se o arquivo existe no armazenamento e exclui
        if (file_exists(storage_path('app/public/' . $anexo->caminho_arquivo))) {
            unlink(storage_path('app/public/' . $anexo->caminho_arquivo));
        }

        // Exclui o registro do banco de dados
        $anexo->delete();

        return response()->json(['message' => 'File deleted successfully'], 200);
    }

}
