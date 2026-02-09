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
        // Lista de imagens predefinidas
        $backgroundImages = collect([
            (object)['path' => 'tenancy/assets/media/misc/image1.jpg', 'name' => 'Imagem 1'],
            (object)['path' => 'tenancy/assets/media/misc/image2.jpg', 'name' => 'Imagem 2'],
            (object)['path' => 'tenancy/assets/media/misc/image3.jpg', 'name' => 'Imagem 3'],
        ]);

        // Buscar TODAS as imagens ativas para o slider
        $activeImages = TelaDeLogin::where('status', 'ativo')
            ->orderBy('created_at', 'desc')
            ->get();

        // Mantemos currentBackground caso a view precise de um fallback específico, ou usamos o primeiro da coleção
        $currentBackground = $activeImages->first();

        return view('app.confs.login.index', compact('backgroundImages', 'activeImages', 'currentBackground'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'backgroundImage' => 'required_without:selectedImage|image|mimes:jpeg,png,jpg,gif|max:4096', // 4MB
            'selectedImage' => 'nullable|string',
            'descricao' => 'required|string|max:255', // Nome do Convento
            'localidade' => 'required|string|max:255', // Localidade
        ]);

        // NOTA: Removemos a parte que desativava as imagens anteriores, pois agora queremos múltiplas imagens (slider).

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
            'descricao' => $request->input('descricao'),
            'localidade' => $request->input('localidade'),
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
    public function update(Request $request, $id)
    {
        $request->validate([
            'descricao' => 'required|string|max:255',
            'localidade' => 'required|string|max:255',
        ]);

        $telaDeLogin = TelaDeLogin::findOrFail($id);

        $telaDeLogin->update([
            'descricao' => $request->input('descricao'),
            'localidade' => $request->input('localidade'),
            'updated_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Informações atualizadas com sucesso.');
    }

    public function destroy($id)
    {
        $telaDeLogin = TelaDeLogin::findOrFail($id);
        
        // Podemos deletar fisicamente ou apenas mudar status para 'inativo'
        // Como o status já é usado para filtrar no slide, vamos mudar para 'inativo' e apagar o arquivo se quiser
        
        $telaDeLogin->update(['status' => 'inativo']);
        // $telaDeLogin->delete(); // Se quiser deletar do banco

        return redirect()->back()->with('success', 'Imagem removida da galeria com sucesso.');
    }
}
