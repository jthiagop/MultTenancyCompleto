<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\TelaDeLogin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TelaDeLoginController extends Controller
{
    /**
     * Lista de imagens predefinidas (paths relativos ao `public/`).
     * Usadas tanto pela UI blade legada quanto pela SPA React.
     */
    private const PREDEFINED_IMAGES = [
        ['path' => 'tenancy/assets/media/misc/image1.jpg', 'name' => 'Imagem 1'],
        ['path' => 'tenancy/assets/media/misc/image2.jpg', 'name' => 'Imagem 2'],
        ['path' => 'tenancy/assets/media/misc/image3.jpg', 'name' => 'Imagem 3'],
    ];

    public function index(Request $request)
    {
        // Cliente quer JSON (SPA React) → devolve payload estruturado.
        if ($request->wantsJson()) {
            return response()->json([
                'active_images' => $this->loadActiveImages(),
                'predefined'    => $this->loadPredefinedImages(),
            ]);
        }

        // Fallback Blade (comportamento legado): mostra a tela de personalização.
        return $this->create();
    }

    public function create()
    {
        $backgroundImages  = collect(self::PREDEFINED_IMAGES)->map(fn ($i) => (object) $i);
        $activeImages      = TelaDeLogin::where('status', 'ativo')
            ->orderBy('created_at', 'desc')
            ->get();
        $currentBackground = $activeImages->first();

        return view('app.confs.login.index', compact('backgroundImages', 'activeImages', 'currentBackground'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'backgroundImage' => 'required_without:selectedImage|image|mimes:jpeg,png,jpg,gif,webp|max:4096', // 4MB
            'selectedImage'   => 'nullable|string',
            'descricao'       => 'required|string|max:255',
            'localidade'      => 'required|string|max:255',
        ]);

        if ($request->hasFile('backgroundImage')) {
            $imagePath = $request->file('backgroundImage')->store('tela_login_images', 'public');
        } elseif ($request->filled('selectedImage')) {
            // Sanitizar: `selectedImage` só pode ser um dos paths predefinidos.
            $selected  = (string) $request->input('selectedImage');
            $allowed   = array_column(self::PREDEFINED_IMAGES, 'path');
            if (! in_array($selected, $allowed, true)) {
                return $this->respondError($request, 'Imagem selecionada inválida.', 422);
            }
            $imagePath = $selected;
        } else {
            return $this->respondError($request, 'Nenhuma imagem selecionada.', 422);
        }

        $telaDeLogin = TelaDeLogin::create([
            'imagem_caminho'    => $imagePath,
            'descricao'         => $request->input('descricao'),
            'localidade'        => $request->input('localidade'),
            'data_upload'       => now(),
            'upload_usuario_id' => Auth::id(),
            'status'            => 'ativo',
            'updated_by'        => Auth::id(),
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Imagem de fundo salva com sucesso!',
                'data'    => $this->transformImage($telaDeLogin),
            ], 201);
        }

        return redirect()->back()->with('success', 'Imagem enviada e registrada com sucesso.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'descricao'  => 'required|string|max:255',
            'localidade' => 'required|string|max:255',
        ]);

        $telaDeLogin = TelaDeLogin::findOrFail($id);

        $telaDeLogin->update([
            'descricao'  => $request->input('descricao'),
            'localidade' => $request->input('localidade'),
            'updated_by' => Auth::id(),
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Informações atualizadas com sucesso.',
                'data'    => $this->transformImage($telaDeLogin->fresh()),
            ]);
        }

        return redirect()->back()->with('success', 'Informações atualizadas com sucesso.');
    }

    public function destroy(Request $request, $id)
    {
        $telaDeLogin = TelaDeLogin::findOrFail($id);

        // Soft-disable: mantemos o registro, apenas removemos do slider ativo.
        $telaDeLogin->update([
            'status'     => 'inativo',
            'updated_by' => Auth::id(),
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Imagem removida da galeria com sucesso.',
                'id'      => (int) $id,
            ]);
        }

        return redirect()->back()->with('success', 'Imagem removida da galeria com sucesso.');
    }

    /**
     * Monta o array de imagens ativas já com URL resolvida para o React consumir.
     */
    private function loadActiveImages(): array
    {
        return TelaDeLogin::where('status', 'ativo')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn (TelaDeLogin $t) => $this->transformImage($t))
            ->all();
    }

    /**
     * Predefinidas: o React recebe `path` (para mandar de volta como `selectedImage`)
     * + `url` pública para renderizar.
     */
    private function loadPredefinedImages(): array
    {
        return array_map(function (array $img) {
            return [
                'path' => $img['path'],
                'name' => $img['name'],
                'url'  => global_asset($img['path']),
            ];
        }, self::PREDEFINED_IMAGES);
    }

    /**
     * Transforma um registro de TelaDeLogin em payload JSON consumível pelo React.
     * A URL usa a rota autenticada `file` (mesmo padrão do blade legado em login/index.blade.php).
     */
    private function transformImage(TelaDeLogin $tela): array
    {
        return [
            'id'              => $tela->id,
            'descricao'       => $tela->descricao,
            'localidade'      => $tela->localidade,
            'status'          => $tela->status,
            'imagem_caminho'  => $tela->imagem_caminho,
            'imagem_url'      => route('file', ['path' => $tela->imagem_caminho]),
            'created_at'      => optional($tela->created_at)->toIso8601String(),
        ];
    }

    /**
     * Helper para responder erro consistente em blade/AJAX/SPA.
     */
    private function respondError(Request $request, string $message, int $status = 422): JsonResponse|\Illuminate\Http\RedirectResponse
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => $message,
            ], $status);
        }

        return redirect()->back()->with('error', $message);
    }
}
