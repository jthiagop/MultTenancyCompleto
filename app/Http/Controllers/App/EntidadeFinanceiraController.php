<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\EntidadeFinanceira;
use App\Models\Movimentacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Flasher\Laravel\Facade\Flasher;


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
            // Remover formatação de milhar e substituir vírgulas por pontos
    $request->merge([
        'saldo_inicial' => str_replace(['.', ','], ['', '.'], $request->saldo_inicial),
        'saldo_atual' => str_replace(['.', ','], ['', '.'], $request->saldo_atual),
    ]);
        // Validação dos dados de entrada
        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
            'tipo' => 'required|in:caixa,banco,dizimo,coleta,doacao',
            'saldo_inicial' => 'required|numeric',
            'saldo_atual' => 'nullable|numeric',
            'descricao' => 'nullable|string|max:500',
            'company_id' => 'required|exists:companies,id', // Validação do company_id
        ]);

        // Adicionar campos adicionais
        $validatedData['created_by'] = auth()->id();
        $validatedData['created_by_name'] = auth()->user()->name;
        $validatedData['updated_by'] = auth()->id();
        $validatedData['updated_by_name'] = auth()->user()->name;

        // Se saldo_atual não for informado, utiliza saldo_inicial como valor padrão
        $validatedData['saldo_atual'] = $validatedData['saldo_atual'] ?? $validatedData['saldo_inicial'];

        try {
            // Criar a entidade financeira
            $entidade = EntidadeFinanceira::create($validatedData);

            // Criar a movimentação inicial
            Movimentacao::create([
                'entidade_id' => $entidade->id,
                'tipo' => 'entrada',
                'valor' => $validatedData['saldo_inicial'],
                'descricao' => 'Saldo inicial da entidade financeira',
                'company_id' => $validatedData['company_id'],
                'created_by' => auth()->user()->id,
                'created_by_name' => auth()->user()->name,
                'updated_by' => auth()->user()->id,
                'updated_by_name' => auth()->user()->name,
            ]);

        // Adiciona uma mensagem de sucesso ao Flasher
   // Mensagem de sucesso
            flash()->success('O lançamento foi salvo com sucesso!');

            // Exibe a mensagem diretamente usando o Flasher e redireciona
            return redirect()->back();
            } catch (\Exception $e) {
            // Adiciona mensagem de erro com detalhes da exceção
            Flasher::addError('Ocorreu um erro ao processar o lançamento: ' . $e->getMessage());

            // Retorna com os dados antigos e exibe as mensagens de erro
            return redirect()->back()->withInput();
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
