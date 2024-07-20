<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Anexo;
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
        $data = $request->all();
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        Anexo::create($data);

        // Suas outras lógicas...
    }

    public function update(Request $request, $id)
    {
        $anexo = Anexo::findOrFail($id);
        $data = $request->all();
        $data['updated_by'] = Auth::id();

        $anexo->update($data);

        // Suas outras lógicas...
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
