<?php

namespace App\Http\Controllers\App\Financeiro;

use App\Http\Controllers\Controller;
use App\Models\FormasRecebimento;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class FormasRecebimentoController extends Controller
{
    public function index()
    {
        $formasRecebimento = FormasRecebimento::all();
        return view('app.cadastros.formasRecebimento.index', compact('formasRecebimento'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:100',
            'codigo' => 'required|string|max:50|unique:formas_recebimento',
            'ativo' => 'required|boolean|in:1,0',
            'observacao' => 'nullable|string',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['created_by_name'] = Auth::user()->name;

        FormasRecebimento::create($validated);

        return redirect()
            ->back()
            ->with('success', 'Forma de Recebimento cadastrada com sucesso!');
    }

    public function update(Request $request, $id)
    {
        $formaRecebimento = FormasRecebimento::findOrFail($id);

        $validated = $request->validate([
            'nome' => 'required|string|max:100',
            'codigo' => 'required|string|max:50|unique:formas_recebimento,codigo,' . $formaRecebimento->id,
            'ativo' => 'required|boolean|in:1,0',
            'observacao' => 'nullable|string',
        ]);

        $validated['updated_by'] = Auth::id();
        $validated['updated_by_name'] = Auth::user()->name;

        $formaRecebimento->update($validated);

        return redirect()->back()->with('success', 'Forma de Recebimento atualizada com sucesso!');
    }

    public function destroy($id)
    {
        $formaRecebimento = FormasRecebimento::findOrFail($id);
        $formaRecebimento->delete();
        return response()->noContent();
    }
}
