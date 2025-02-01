<?php

namespace App\Http\Controllers\App\Anexos;

use App\Http\Controllers\Controller;
use App\Models\Anexos\ModulosAnexos;
use Auth;
use Flasher;
use Illuminate\Http\Request;
use Log;
use Str;

class ModulosAnexosController extends Controller
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

        // Verifica se arquivos foram enviados
        if ($request->hasFile('files')) {
            try {
                $anexos = [];
                // Iterar sobre cada arquivo enviado
                foreach ($request->file('files') as $file) {
                    // Nome original do arquivo (sem caminho completo)
                    $nomeOriginal = $file->getClientOriginalName();

                    // Gerar um nome único com timestamp antes do nome original
                    $nomeUnico = $nomeOriginal . '_' . time();

                    // Sanitizar o nome do arquivo para evitar caracteres indesejados
                    $nomeUnico = Str::slug(pathinfo($nomeOriginal, PATHINFO_FILENAME)) . '_' . time() . '.' . $file->getClientOriginalExtension();

                    // Armazenar o arquivo no diretório 'anexos' dentro de 'public' com o nome único
                    $caminhoArquivo = $file->storeAs('anexos', $nomeUnico, 'public');

                    // Salvar os detalhes do arquivo no banco de dados
                    $anexo = ModulosAnexos::create([
                        'anexavel_id' => $request->input('anexavel_id'),
                        'anexavel_type' => $request->input('anexavel_type'),
                        'nome_arquivo' => $nomeOriginal, // Nome original sem timestamp
                        'caminho_arquivo' => $caminhoArquivo, // Caminho do arquivo salvo
                        'tipo_arquivo' => $file->getMimeType(),
                        'extensao_arquivo' => $file->getClientOriginalExtension(),
                        'mime_type' => $file->getMimeType(),
                        'tamanho_arquivo' => $file->getSize(),
                        'descricao' => $request->input('descricao'),
                        'status' => 'ativo', // Padrão como ativo
                        'data_upload' => now(),
                        'tags' => $request->input('tags'),
                    ]);

                    $anexos[] = $anexo;
                }

                // Adicionar mensagem de sucesso com o Flasher
                return redirect()->back()->with('success', 'Arquivos enviados com sucesso!');
            } catch (\Exception $e) {
                // Registrar o erro no log
                Log::error('Erro ao salvar anexo:', ['error' => $e->getMessage()]);

                // Adicionar mensagem de erro com o Flasher
                Flasher::addError('Erro ao salvar anexo. Por favor, tente novamente.');

                return redirect()->back()->with('error', 'Erro ao salvar os anexos.');
            }
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            // Busca o anexo pelo ID ou retorna erro 404 se não encontrado
            $anexo = ModulosAnexos::findOrFail($id);

            // Verifica se o arquivo existe no armazenamento e exclui
            $arquivoPath = storage_path('app/public/' . $anexo->caminho_arquivo);
            if (file_exists($arquivoPath)) {
                unlink($arquivoPath);
            }

            // Registra o usuário que realizou a exclusão
            $anexo->updated_by = Auth::id();
            $anexo->updated_by_name = Auth::user()->name;

            // Exclui o registro do banco de dados
            $anexo->delete();

            // Adiciona mensagem de sucesso com Flasher
            Flasher::addSuccess('Arquivo excluído com sucesso!');

            // Redireciona para a página anterior com mensagem de sucesso
            return redirect()->back();
        } catch (\Exception $e) {
            // Loga o erro e adiciona mensagem de falha com Flasher
            Log::error('Erro ao excluir o arquivo:', ['error' => $e->getMessage()]);
            Flasher::addError('Erro ao excluir o arquivo. Tente novamente mais tarde.');

            // Retorna para a página anterior com mensagem de erro
            return redirect()->back();
        }
    }

}
