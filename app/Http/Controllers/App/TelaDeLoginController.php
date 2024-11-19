<?php

namespace App\Http\Controllers\App;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Models\TelaDeLogin;
use Illuminate\Http\Request;

class TelaDeLoginController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('app.confs.login.index');
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
            // Validação dos campos
    $request->validate([
        'backgroundImage' => 'required|image|mimes:jpeg,png,jpg,gif|max:4048', // validação do arquivo
    ]);


    // Processar o upload da imagem
    if ($request->hasFile('backgroundImage')) {
        $imagePath = $request->file('backgroundImage')->store('tela_login_images', 'public');

        // Criar o registro no banco de dados
        $telaDeLogin = TelaDeLogin::create([
            'imagem_caminho' => $imagePath,
            'data_upload' => now(),
            'upload_usuario_id' => auth()->id(), // ID do usuário logado
            'status' => 'ativo',
            'updated_by' => auth()->id()

        ]);

        return redirect()->back()->with('success', 'Imagem enviada e registrada com sucesso.');
    }

    return redirect()->back()->with('error', 'Falha ao enviar a imagem.');
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
    public function destroy(string $id)
    {
        //
    }
}
