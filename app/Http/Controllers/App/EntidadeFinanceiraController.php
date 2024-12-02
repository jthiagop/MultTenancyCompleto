<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\EntidadeFinanceira;
use App\Models\Movimentacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EntidadeFinanceiraController extends Controller
{
    // Lista todas as entidades financeiras
    public function index()
    {
        $entidades = EntidadeFinanceira::with('movimentacoes')->get();
        return view('app.cadastros.entidades.index', compact('entidades'));
    }

    // Mostra o formulário de criação
    public function create()
    {
        return view('app.cadastros.entidades.index');
    }

    // Salva uma nova entidade financeira
    public function store(Request $request)
    {
        // Obter o ID da empresa do usuário autenticado
        $companyId = Auth::user()->company_id;

        // Validação dos dados de entrada
        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
            'tipo' => 'required|in:caixa,banco,dizimo,coleta,doacao',
            'saldo_inicial' => 'required',
            'saldo_atual' => 'nullable', // Opcional
            'descricao' => 'nullable|string|max:500',
        ]);

        try {
            // Adicionar campos adicionais ao array de dados validados
            $validatedData['company_id'] = $companyId;
            $validatedData['created_by'] = auth()->id();
            $validatedData['created_by_name'] = auth()->user()->name;
            $validatedData['updated_by'] = auth()->id();
            $validatedData['updated_by_name'] = auth()->user()->name;

            // Se saldo_atual não for informado, utiliza saldo_inicial como valor padrão
            $validatedData['saldo_atual'] = $validatedData['saldo_atual'] ?? $validatedData['saldo_inicial'];

            // Criar a entidade financeira
            $entidade = EntidadeFinanceira::create($validatedData);

                    // Criação de uma movimentação inicial para registrar o saldo inicial
        Movimentacao::create([
            'entidade_id' => $entidade->id,
            'tipo' => 'entrada',
            'valor' => $validatedData['saldo_inicial'],
            'descricao' => 'Saldo inicial da entidade financeira',
            'company_id' => $companyId,
            'created_by' => auth()->user()->id,
            'created_by_name' => auth()->user()->name,
            'updated_by' => auth()->user()->id,
            'updated_by_name' => auth()->user()->name,
        ]);

            // Redirecionar com mensagem de sucesso
            return redirect()->back()->with('message', 'Lançamento criado com sucesso!');
        } catch (\Exception $e) {
            // Em caso de erro, redirecionar com mensagem de erro
            return redirect()->back()->with('error', 'Erro ao criar a entidade financeira: ' . $e->getMessage());
        }
    }


    // Adiciona uma movimentação
    public function addMovimentacao(Request $request, $id)
    {
        $request->validate([
            'tipo' => 'required|in:entrada,saida',
            'valor' => 'required|numeric|min:0',
            'descricao' => 'nullable|string|max:255',
        ]);

        $entidade = EntidadeFinanceira::findOrFail($id);

        // Cria a movimentação
        Movimentacao::create([
            'entidade_id' => $entidade->id,
            'tipo' => $request->tipo,
            'valor' => $request->valor,
            'descricao' => $request->descricao,
        ]);

        // Atualiza o saldo atual da entidade
        $entidade->atualizarSaldo();

        return redirect()->route('entidades.index')->with('success', 'Movimentação adicionada com sucesso!');
    }
}
