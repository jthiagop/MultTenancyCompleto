<?php

namespace App\Http\Controllers\App;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Models\TelaDeLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TelaDeLoginController extends Controller
{
    public function index()
    {
        // Se o index deve mostrar a mesma página de personalização, redirecione para create
        return $this->create();
    }

    public function create()
    {
        // Lista de imagens predefinidas (pode ser movida para um arquivo de configuração ou banco)
        $backgroundImages = collect([
            (object)['path' => 'assets/media/misc/image1.jpg', 'name' => 'Imagem 1'],
            (object)['path' => 'assets/media/misc/image2.jpg', 'name' => 'Imagem 2'],
            (object)['path' => 'assets/media/misc/image3.jpg', 'name' => 'Imagem 3'],
        ]);

        // Buscar a imagem de fundo atual (ativa)
        $currentBackground = TelaDeLogin::where('status', 'ativo')->latest()->first();

        return view('app.confs.login.index', compact('backgroundImages', 'currentBackground'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'backgroundImage' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096', // 4MB
            'selectedImage' => 'nullable|string',
        ]);

        // Desativar imagens anteriores
        TelaDeLogin::where('status', 'ativo')->update(['status' => 'inativo']);

        // Processar upload de imagem ou seleção predefinida
        if ($request->hasFile('backgroundImage')) {
            $imagePath = $request->file('backgroundImage')->store('tela_login_images', 'public');
        } elseif ($request->filled('selectedImage')) {
            $imagePath = $request->input('selectedImage');
        } else {
            if ($request->ajax()) {
                return response()->json(['message' => 'Nenhuma imagem selecionada.'], 422);
            }
            return redirect()->back()->with('error', 'Nenhuma imagem selecionada.');
        }

        // Criar o registro
        $telaDeLogin = TelaDeLogin::create([
            'imagem_caminho' => $imagePath,
            'data_upload' => now(),
            'upload_usuario_id' => Auth::id(),
            'status' => 'ativo',
            'updated_by' => Auth::id(),
        ]);

        if ($request->ajax()) {
            return response()->json(['message' => 'Imagem de fundo salva com sucesso!', 'data' => $telaDeLogin], 200);
        }

        return redirect()->back()->with('success', 'Imagem enviada e registrada com sucesso.');
    }
}
