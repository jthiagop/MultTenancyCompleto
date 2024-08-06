<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Anexo;
use App\Models\Banco;
use App\Models\Caixa;
use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        dd($request->all());

        $data = $request->all();
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        Anexo::create($data);

        return redirect()->route('caixa.index');

    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'files.*' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = auth()->user();
        $caixa = Caixa::findOrFail($id);
        $banco = Banco::findOrFail($id);
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

        return response()->json(['success' => 'Arquivos enviados com sucesso!']);
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

        // Delete the file from storage if necessary
        if (file_exists(storage_path('app/public/' . $anexo->caminho_arquivo))) {
            unlink(storage_path('app/public/' . $anexo->caminho_arquivo));
        }

        // Delete the database record
        $anexo->delete();

        return redirect()->back()->with('success', 'Anexo excluído com sucesso!');
    }
}
