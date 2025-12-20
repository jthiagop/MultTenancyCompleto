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
        // Buscar todas as imagens ativas para mostrar na galeria (slider)
        $existingImages = TelaDeLogin::where('status', 'ativo')->latest()->get();

        // Buscar a imagem mais recente para ser o background inicial da visualização
        $currentBackground = $existingImages->first();

        return view('app.confs.login.index', compact('existingImages', 'currentBackground'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'backgroundImage' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB
            'descricao' => 'required|string|max:255',
            'localidade' => 'required|string|max:255',
        ]);

        if ($request->hasFile('backgroundImage')) {
            $imagePath = $request->file('backgroundImage')->store('login-images', 'public');

            // Criar o registro (NÃO desativa os anteriores, pois queremos o slider aleatório)
            TelaDeLogin::create([
                'imagem_caminho' => $imagePath,
                'descricao' => $request->descricao,
                'localidade' => $request->localidade,
                'data_upload' => now(),
                'upload_usuario_id' => Auth::id(),
                'status' => 'ativo',
                'updated_by' => Auth::id(),
            ]);

            return redirect()->back()->with('success', 'Imagem enviada com sucesso! Ela aparecerá aleatoriamente na tela de login.');
        }

        return redirect()->back()->with('error', 'Por favor, selecione uma imagem.');
    }
}
