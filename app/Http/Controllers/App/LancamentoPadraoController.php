<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\LancamentoPadrao;
use Auth;
use Flasher;
use Illuminate\Http\Request;

class LancamentoPadraoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $lps = LancamentoPadrao::all();
        $lancamentoPadrao = LancamentoPadrao::all();


        // Mapeia categorias para classes de cor
        $categoryColors = [
            'Serviços essenciais' => 'badge-light-success',
            'Suprimentos' => 'badge-light-primary',
            'Pessoal' => 'badge-light-warning',
            'Alimentação' => 'badge-light-info',
            'Saúde' => 'badge-light-danger',
            'Manutenção' => 'badge-light-dark',
            'Liturgia' => 'badge-light-muted',
            'Equipamentos' => 'badge-light-secondary',
            'Material de escritório' => 'badge-light-light',
            'Educação' => 'badge-light-orange',
            'Transporte' => 'badge-light-teal',
            'Contribuições' => 'badge-light-purple',
            // Adicione outras categorias e cores conforme necessário
        ];

        return view('app.cadastros.lancamentoPadrao.index', compact('lps', 'categoryColors', 'lancamentoPadrao'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // Obtém o tipo selecionado do request
        $tipo = $request->input('tipo');
        $lps = LancamentoPadrao::all();


        // Se um tipo foi selecionado, busque os lançamentos correspondentes
        $lancamentos = $tipo ? LancamentoPadrao::where('tipo', $tipo)->get() : collect();

        return view('app.cadastros.lancamentoPadrao.create', compact('lancamentos', 'tipo', 'lps'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validação dos dados
        $request->validate([
            'description' => 'required|string|max:255',
            'type' => 'required|in:entrada,saida',
            'date' => 'required|date',
            'category' => 'required|string|max:255',
        ], [
            'description.required' => 'O nome do lançamento é obrigatório.',
            'type.required' => 'O tipo do lançamento é obrigatório.',
            'type.in' => 'O tipo deve ser "entrada" ou "saída".',
            'category.required' => 'A categoria é obrigatória.'
        ]);



        $user = Auth::user(); // Usuário autenticado

        // Criação do lançamento
        LancamentoPadrao::create([
            'type' => $request->input('type'),
            'description' => $request->input('description'),
            'date' => $request->input('date'),
            'category' => $request->input('category'),
            'user_id' => $user->id, // Pegando o ID do usuário autenticado
        ]);

            // Adiciona mensagem de sucesso
        Flasher::addSuccess('Lançamento cadastrado com sucesso!');
        return redirect()->back()->with('message', 'Lançamento Padrão criado com sucesso!');
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $lp = LancamentoPadrao::find($id);

        $lps = LancamentoPadrao::all();

        return view('app.cadastros.lancamentoPadrao.edit', ['lps' => $lps, 'lp' => $lp ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validação dos dados
        $request->validate([
            'description' => 'required|string|max:255',
            'type' => 'required|in:entrada,saida',
            'date' => 'required|date',
            'category' => 'required|string|max:255',
        ], [
            'description.required' => 'O nome do lançamento é obrigatório.',
            'type.required' => 'O tipo do lançamento é obrigatório.',
            'type.in' => 'O tipo deve ser "entrada" ou "saída".',
            'category.required' => 'A categoria é obrigatória.'
        ]);

        // Encontra o lançamento padrão pelo ID
        $lancamento = LancamentoPadrao::findOrFail($id);

        // Atualiza os dados do lançamento
        $lancamento->update([
            'description' => $request->description,
            'type' => $request->type,
            'date' => $request->date,
            'category' => $request->category,
        ]);

        // Redireciona com uma mensagem de sucesso
        return redirect()->route('lancamentoPadrao.create')->with('success', 'Lançamento Padrão atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            // Localiza o registro pelo ID
            $lancamento = LancamentoPadrao::findOrFail($id);

            // Exclui o registro
            $lancamento->delete();

            // Redireciona com uma mensagem de sucesso
            return redirect()->route('lancamentopadrao.index')->with('success', 'Lançamento Padrão excluído com sucesso!');
        } catch (\Exception $e) {
            // Log do erro (opcional)
            \Log::error('Erro ao excluir Lançamento Padrão: ' . $e->getMessage());

            // Redireciona com uma mensagem de erro
            return redirect()->route('lancamentoPadrao.index')->with('error', 'Erro ao excluir Lançamento Padrão.');
        }
    }



}
