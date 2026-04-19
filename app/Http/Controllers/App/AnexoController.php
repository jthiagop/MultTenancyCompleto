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
use Illuminate\Support\Facades\Storage;

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
        // Verificar ownership antes de associar o anexo
        $companyId = session('active_company_id');

        if ($request->has('caixa_id') && $request->input('caixa_id')) {
            $caixaId = (int) $request->input('caixa_id');
            if (!Caixa::where('id', $caixaId)->where('company_id', $companyId)->exists()) {
                return response()->json(['message' => 'Não autorizado.'], 403);
            }
        }

        if ($request->has('banco_id') && $request->input('banco_id')) {
            $bancoId = (int) $request->input('banco_id');
            if (!Banco::where('id', $bancoId)->where('company_id', $companyId)->exists()) {
                return response()->json(['message' => 'Não autorizado.'], 403);
            }
        }

        if ($request->hasFile('file')) {
            $file = $request->file('file');

            $caminhoArquivo = $file->store('anexos', 'public');

            $anexo = Anexo::create([
                'caixa_id' => $request->input('caixa_id'),
                'banco_id' => $request->input('banco_id'),
                'nome_arquivo' => $file->getClientOriginalName(),
                'caminho_arquivo' => $caminhoArquivo,
                'size' => $file->getSize(),
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            return response()->json([
                'message' => 'Arquivo enviado com sucesso!',
                'file_path' => $caminhoArquivo,
            ], 200);
        }

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
    $user = Auth::user();

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

        // Verificar ownership via recurso relacionado (caixa ou banco)
        $companyId  = session('active_company_id');
        $authorized = false;

        if ($anexo->caixa_id) {
            $authorized = Caixa::where('id', $anexo->caixa_id)
                ->where('company_id', $companyId)
                ->exists();
        } elseif ($anexo->banco_id) {
            $authorized = Banco::where('id', $anexo->banco_id)
                ->where('company_id', $companyId)
                ->exists();
        }

        if (!$authorized) {
            return response()->json(['message' => 'Não autorizado.'], 403);
        }

        // Usar Storage facade em vez de unlink direto (mais seguro e testável)
        Storage::disk('public')->delete($anexo->caminho_arquivo);

        $anexo->delete();

        return response()->json(['message' => 'File deleted successfully'], 200);
    }

}
