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
        $user = Auth::user();
        // Obter o ID da empresa associada ao usuário autenticado
        $companyId = $user->company_id;

        // Verifica se a empresa foi encontrada
        if (!$companyId) {
            return redirect()->back()->with('error', 'Empresa não encontrada para o usuário autenticado.');
        }

        $entidades = EntidadeFinanceira::with('movimentacoes')
            ->where('company_id', $companyId)
            ->get();
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
        // 1) Remover formatação de milhar e substituir vírgulas por pontos nos campos de valor
        $request->merge([
            'saldo_inicial' => str_replace(['.', ','], ['', '.'], $request->saldo_inicial),
            'saldo_atual'   => str_replace(['.', ','], ['', '.'], $request->saldo_atual),
        ]);

        // 2) Validação condicional
        $validatedData = $request->validate([
            'tipo'          => 'required|in:caixa,banco,dizimo,coleta,doacao',
            'company_id'    => 'nullable|string|max:20',
            // Se tipo == 'banco', campo 'banco' é obrigatório; caso contrário, 'nome' é obrigatório.
            'nome' => 'required_unless:tipo,banco|nullable|string|max:100',
            'banco' => 'required_if:tipo,banco|nullable|string|max:100',


            'agencia'       => 'nullable|string|max:20',
            'conta'         => 'nullable|string|max:20',

            'saldo_inicial' => 'required|numeric',
            'saldo_atual'   => 'nullable|numeric',
            'descricao'     => 'nullable|string|max:255',
        ], [
            'nome.required_if'  => 'O campo "Nome da Entidade" é obrigatório quando o tipo não for "banco".',
            'banco.required_if' => 'Selecione um banco quando o tipo for "banco".',
        ]);

        // 3) Ajustar “nome” conforme o tipo
        //    Se for "banco", usamos o campo 'banco' como o nome da entidade; caso contrário, usamos 'nome'.
        if ($request->tipo === 'banco') {
            $validatedData['nome'] = $validatedData['banco']; // Atribui o campo 'banco' como nome
        }

        // 4) Se saldo_atual não for informado, usar saldo_inicial como valor padrão
        $validatedData['saldo_atual'] = $validatedData['saldo_atual'] ?? $validatedData['saldo_inicial'];

        // 5) Adicionar campos de auditoria / company
        $validatedData['created_by']       = Auth::id();
        $validatedData['created_by_name']  = Auth::user()->name;
        $validatedData['updated_by']       = Auth::id();
        $validatedData['updated_by_name']  = Auth::user()->name;

        try {
            // 6) Criar a entidade no banco
            $entidade = EntidadeFinanceira::create($validatedData);

            // 7) Criar a movimentação inicial (opcional, conforme sua lógica)
            Movimentacao::create([
                'entidade_id'   => $entidade->id,
                'tipo'          => 'entrada',
                'valor'         => $validatedData['saldo_inicial'],
                'descricao'     => 'Saldo inicial da entidade financeira',
                'company_id'    => $validatedData['company_id'],
                'created_by'    => Auth::id(),
                'created_by_name' => Auth::user()->name,
                'updated_by'    => Auth::id(),
                'updated_by_name' => Auth::user()->name,
            ]);

            // 8) Mensagem de sucesso e redirecionamento
            flash()->success('O lançamento foi salvo com sucesso!');
            return redirect()->back();
        } catch (\Exception $e) {
            // 9) Em caso de erro, registra log (opcional) e retorna com mensagem de erro
            \Log::error('Ocorreu um erro ao processar o lançamento: ' . $e->getMessage());

            Flasher::addError('Ocorreu um erro: ' . $e->getMessage());
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

    public function destroy(string $id)
{
    try {
        // 1) Localiza a entidade financeira pelo ID
        $entidade = EntidadeFinanceira::findOrFail($id);
        // 2) Exclui as movimentações associadas (se necessário)
        $movimentacao = Movimentacao::where('entidade_id', $entidade->id)->delete();

        // 3) Exclui a entidade financeira
        $entidade->delete();

        // 4) Mensagem de sucesso e redirecionamento
        flash()->success('A entidade financeira foi excluída com sucesso!');
        return redirect()->back(); // Redireciona para a lista de entidades
    } catch (\Exception $e) {
        // 5) Em caso de erro, registra log e retorna com mensagem de erro
        \Log::error('Erro ao excluir entidade financeira: ' . $e->getMessage());

        Flasher::addError('Ocorreu um erro ao excluir a entidade financeira: ' . $e->getMessage());
        return redirect()->back();
    }
}
}
