<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\PatrimonioAnexo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PatrimonioAnexoController extends Controller
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

        // Verificar se o patrimonio_id foi enviado corretamente
        Log::info('Patrimonio ID:', ['patrimonio_id' => $request->input('patrimonio_id')]);

        // Verifica se um arquivo foi enviado
        if ($request->hasFile('file')) {
            $file = $request->file('file');

            // Armazenar o arquivo no diretório 'anexos' dentro de 'public'
            $caminhoArquivo = $file->store('patrimonioAnexos', 'public');

            // Salvar os detalhes do arquivo no banco de dados
            $anexo = PatrimonioAnexo::create([
                'patrimonio_id' => $request->input('patrimonio_id'), // ID do patrimônio relacionado
                'nome_arquivo' => $file->getClientOriginalName(),
                'caminho_arquivo' => $caminhoArquivo, // Caminho do arquivo armazenado
                'tipo_arquivo' => $file->getMimeType(),
                'tamanho_arquivo' => $file->getSize(),
                'descricao' => $request->input('descricao'), // Descrição opcional do arquivo
                'uploaded_by' => auth()->id(), // ID do usuário autenticado
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

    /**
     * Display the specified resource.
     */
    public function show(PatrimonioAnexo $patrimonioAnexo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PatrimonioAnexo $patrimonioAnexo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    // Método para atualizar o nome do arquivo
    public function update(Request $request, $id)
    {
        // Valida o novo nome do arquivo
        $request->validate([
            'nome_arquivo' => 'required|string|max:255',
        ]);

        // Encontra o anexo pelo ID
        $anexo = PatrimonioAnexo::find($id);

        // Verifica se o anexo existe
        if (!$anexo) {
            return response()->json(['error' => 'Anexo não encontrado.'], 404);
        }

        // Atualiza o nome do arquivo
        $anexo->nome_arquivo = $request->input('nome_arquivo');
        $anexo->save();

        // Retorna uma resposta de sucesso
        return response()->json(['message' => 'Nome do arquivo atualizado com sucesso!', 'anexo' => $anexo], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
{
    $anexo = PatrimonioAnexo::findOrFail($id);

    // Delete the file from storage
    $filePath = storage_path('app/public/' . $anexo->caminho_arquivo);
    if (file_exists($filePath)) {
        unlink($filePath);
    }

    // Delete the database record
    $anexo->delete();

    // Return a JSON response to the frontend
    return response()->json(['success' => true, 'message' => 'Anexo excluído com sucesso!']);
}

}
