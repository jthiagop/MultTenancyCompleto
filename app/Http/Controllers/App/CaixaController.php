<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Http\Requests\Financer\UpdateTransacaoFinanceiraRequest;
use App\Http\Requests\Financer\StoreTransacaoFinanceiraRequest;
use App\Models\Anexo;
use App\Models\Banco;
use App\Models\CadastroBanco;
use App\Models\Caixa;
use App\Models\Company;
use App\Models\EntidadeFinanceira;
use App\Models\Financeiro\CostCenter;
use App\Models\Financeiro\ModulosAnexo;
use App\Models\Financeiro\TransacaoFinanceira;
use App\Models\LancamentoPadrao;
use App\Models\Movimentacao;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Facades\Activity; // Importe a facade Activity
use Illuminate\Support\Str;
use Flasher\Laravel\Facade\Flasher;
use Illuminate\Support\Facades\Log;
use Flasher\Prime\FlasherInterface;


class CaixaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Supondo que você tenha uma forma de obter o company_id, por exemplo, do usuário autenticado
        $companyId = Auth::user()->company_id; // Ajuste conforme sua lógica

        $transacoesFinanceiras = TransacaoFinanceira::with('entidadeFinanceira') // Eager Load
            ->where('company_id', $companyId) // Filtro pelo company_id
            ->orderBy('id', 'DESC')
            ->paginate(10);

        $valorEntradaBanco = Banco::getBancoEntrada();
        $ValorSaidasBanco = Banco::getBancoSaida();

        $valorEntrada = caixa::getCaixaEntrada();
        $ValorSaidas = caixa::getCaixaSaida();

        $caixas = Caixa::getCaixaList();

        $entidades = Caixa::getEntidadesCaixa();
        $entidadesBanco = Caixa::getEntidadesBanco();

        $lps = LancamentoPadrao::all();
        $bancos = CadastroBanco::getCadastroBanco(); // Chama o método para obter os bancos

        list($somaEntradas, $somaSaida) = caixa::getCaixa();
        $total = $somaEntradas - $somaSaida;

        return view('app.financeiro.index', [
            'caixas' => $caixas,
            'valorEntrada' => $valorEntrada,
            'ValorSaidas' => $ValorSaidas,
            'valorEntradaBanco' => $valorEntradaBanco,
            'ValorSaidasBanco' => $ValorSaidasBanco,
            'lps' => $lps,
            'bancos' => $bancos,
            'total' => $total,
            'entidades' => $entidades,
            'entidadesBanco' => $entidadesBanco,
            'transacoesFinanceiras' => $transacoesFinanceiras,

        ]);
    }

    public function list(Request $request)
    {
        // Obter o valor da aba ativa da URL, se presente
        $activeTab = $request->input('tab', 'overview'); // 'overview' é o padrão caso não haja o parâmetro 'tab'

        $user = Auth::user();
        $lps = LancamentoPadrao::all();
        $bancos = CadastroBanco::getCadastroBanco(); // Chama o método para obter os bancos
        $entidades = Caixa::getEntidadesCaixa();
        $entidadesBanco = Caixa::getEntidadesBanco();

        // Verifica se o usuário está autenticado
        if (!$user) {
            return redirect()->route('login')->with('error', 'Usuário não autenticado');
        }

        // Obter o ID da empresa associada ao usuário autenticado
        $companyId = $user->company_id;

        // Verifica se a empresa foi encontrada
        if (!$companyId) {
            return redirect()->back()->with('error', 'Empresa não encontrada para o usuário autenticado.');
        }

        // Obter as somas de entradas e saídas utilizando métodos no modelo Caixa
        list($somaEntradas, $somaSaidas) = Caixa::getCaixa($companyId);

        // Calcular o total (entradas - saídas)
        $total = EntidadeFinanceira::getValorTotalEntidade();
        // Listar todos os registros de caixa para a empresa do usuário
        $transacoes = TransacaoFinanceira::where('origem', 'Caixa')
        ->where('company_id', $companyId)
        ->get();

        $centrosAtivos = CostCenter::getCadastroCentroCusto();

        // Obter os valores de entrada e saída de caixa para a empresa
        $valorEntrada = Caixa::getCaixaEntrada($companyId);

        /** @var TYPE_NAME $valorSaidas */
        $valorSaidas = Caixa::getCaixaSaida($companyId);

        // Obter informações da empresa associada ao usuário (ajustando para relacionamento)
        $company = $user->company;

        return view('app.financeiro.caixa.list', [
            'transacoes' => $transacoes,
            'valorEntrada' => $valorEntrada,
            'valorSaidas' => $valorSaidas,
            'total' => $total,
            'lps' => $lps,
            'bancos' => $bancos,
            'company' => $company,
            'entidades' => $entidades,
            'entidadesBanco' => $entidadesBanco,
            'activeTab' => $activeTab,
            'centrosAtivos' => $centrosAtivos,


        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $lps = LancamentoPadrao::orderBy('created_at', 'desc')->take(6)->get();

        $company = User::getCompanyName();

        list($somaEntradas, $somaSaida) = caixa::getCaixa();

        $total = $somaEntradas - $somaSaida;

        $bancos = CadastroBanco::getCadastroBanco(); // Chama o método para obter os bancos


        return view(
            'app.financeiro.caixa.create',
            [
                'company' => $company,
                'lps' => $lps,
                'bancos' => $bancos,
                'total' => $total,
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTransacaoFinanceiraRequest $request)
    {
        // Recupera a companhia associada ao usuário autenticado
        $subsidiary = User::getCompany();

        if (!$subsidiary) {
            return redirect()->back()->with('error', 'Companhia não encontrada.');
        }

        // Validação dos dados é automática com StoreTransacaoFinanceiraRequest, não é necessário duplicar validações aqui

        // Processa os dados validados
        $validatedData = $request->validated();

        // Converte o valor e a data para os formatos adequados
        $validatedData['data_competencia'] = Carbon::createFromFormat('d/m/Y', $validatedData['data_competencia'])->format('Y-m-d');
        $validatedData['valor'] = str_replace(',', '.', str_replace('.', '', $validatedData['valor']));

        // Adiciona informações padrão
        $validatedData['company_id'] = $subsidiary->company_id;
        $validatedData['created_by'] = Auth::id();
        $validatedData['created_by_name'] = Auth::user()->name;
        $validatedData['updated_by'] = Auth::id();
        $validatedData['updated_by_name'] = Auth::user()->name;

        $validatedData['updated_by_name'] = Auth::user()->name;

        // 1) Chama o método movimentacao() e guarda o retorno
        $movimentacao = $this->movimentacao($validatedData);

        // 2) Atribui o ID retornado à chave movimentacao_id de $validatedData
        $validatedData['movimentacao_id'] = $movimentacao->id;

        // 3) Cria o registro na tabela transacoes_financeiras usando o ID que acabamos de obter
        $caixa = TransacaoFinanceira::create($validatedData);

        // Verifica e processa lançamentos padrão
        $this->processarLancamentoPadrao($validatedData);

        // Processa anexos, se existirem
        $this->processarAnexos($request, $caixa);

        // Adiciona mensagem de sucesso
        Flasher::addSuccess('Lançamento criado com sucesso!');
        return redirect()->back()->with('message', 'Lançamento criado com sucesso!');
    }

    /**
     * Processa movimentacao.
     */
    private function movimentacao(array $validatedData)
    {
        // Cria o lançamento na tabela 'movimentacoes'
        $movimentacao = Movimentacao::create([
            'entidade_id' => $validatedData['entidade_id'],
            'tipo'        => $validatedData['tipo'],
            'valor'       => $validatedData['valor'],
            'data'        => $validatedData['data_competencia'],
            'descricao'   => $validatedData['descricao'],
            'company_id'  => $validatedData['company_id'],
            'created_by'  => $validatedData['created_by'],
            'created_by_name' => $validatedData['created_by_name'],
            'updated_by'      => $validatedData['updated_by'],
            'updated_by_name' => $validatedData['updated_by_name'],
        ]);

        // Retorna o objeto Movimentacao recém-criado, de onde poderemos pegar o ID
        return $movimentacao;
    }

    /**
     * Processa lançamentos padrão.
     */
    private function processarLancamentoPadrao(array $validatedData)
    {
        $lancamentoPadrao = LancamentoPadrao::find($validatedData['lancamento_padrao_id']);
        if ($lancamentoPadrao && $lancamentoPadrao->description === 'Deposito Bancário') {
            $validatedData['origem'] = 'Caixa';
            $validatedData['tipo'] = 'entrada';

            // Cria outra movimentação para "Deposito Bancário"
            $movimentacaoBanco = Movimentacao::create([
                'entidade_id' => $validatedData['entidade_banco_id'],
                'tipo' => $validatedData['tipo'],
                'valor' => $validatedData['valor'],
                'descricao' => $validatedData['descricao'],
                'company_id' => $validatedData['company_id'],
                'created_by' => $validatedData['created_by'],
                'created_by_name' => $validatedData['created_by_name'],
                'updated_by' => $validatedData['updated_by'],
                'updated_by_name' => $validatedData['updated_by_name'],
            ]);

            // Cria o lançamento no banco
            $validatedData['movimentacao_id'] = $movimentacaoBanco->id;
            Banco::create($validatedData);
        }
    }

    /**
     * Processa os anexos enviados.
     */
    private function processarAnexos(Request $request, TransacaoFinanceira $caixa)
    {
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                 // Nome original do arquivo (sem caminho completo)
                $nomeOriginal = $file->getClientOriginalName();
                $anexoName = time() . '_' . $file->getClientOriginalName();
                $anexoPath = $file->storeAs('anexos', $anexoName, 'public');

                ModulosAnexo::create([
                    'anexavel_id'   => $caixa->id,                   // ID da transacao_financeira
                    'anexavel_type' => TransacaoFinanceira::class,   // caminho da classe do Model
                    'nome_arquivo'  => $nomeOriginal,
                    'caminho_arquivo' => $anexoPath,
                    'tamanho_arquivo' => $file->getSize(),
                    'tipo_arquivo'  => $file->getMimeType() ?? '',  // se quiser
                    'created_by'    => Auth::id(),
                    'created_by_name' => Auth::user()->name,
                    // etc., se tiver mais campos
                ]);
            }
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Caixa $caixa)
    {
        return view('app.financeiro.caixa.show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // Obter o ID da empresa do usuário autenticado
        $companyId = Auth::user()->company_id;

        // Buscar o banco com o ID e verificar se pertence à empresa do usuário
        $caixa = TransacaoFinanceira::with('modulos_anexos')
            ->where('company_id', $companyId) // Filtrar pelo company_id do usuário
            ->findOrFail($id);


        $lps = LancamentoPadrao::all();
        $entidadesBanco = Banco::getEntidadesBanco();
        $entidades = Caixa::getEntidadesCaixa();
        $centrosAtivos = CostCenter::where('company_id', $companyId)->get();

        // Retornar a view com os dados filtrados
        return view(
            'app.financeiro.caixa.edit',
            [
                'caixa' => $caixa,
                'lps' => $lps,
                'entidades' => $entidades,
                'entidadesBanco' => $entidadesBanco,
                'centrosAtivos' => $centrosAtivos,
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            // Obtenha a empresa do usuário autenticado
            $subsidiaryId = User::getCompany();

            // Tratar o valor do campo "valor"
            if ($request->has('valor')) {
                $request->merge([
                    'valor' => str_replace(',', '.', str_replace('.', '', $request->input('valor')))
                ]);
            }

            // Converter data_competencia para o formato correto
            if ($request->has('data_competencia')) {
                $request->merge([
                    'data_competencia' => Carbon::createFromFormat('d/m/Y', $request->input('data_competencia'))->format('Y-m-d')
                ]);
            }

            // Validação dos dados
            $validator = Validator::make($request->all(), [
                'data_competencia' => 'required|date',
                'descricao' => 'required|string|max:255',
                'valor' => 'required|regex:/^\d+(\.\d{1,2})?$/',
                'tipo' => 'required|in:entrada,saida',
                'lancamento_padrao_id' => 'required|exists:lancamento_padraos,id',
                'cost_center_id' => 'required|exists:cost_centers,id',
                'tipo_documento' => 'required|string|max:255',
                'numero_documento' => 'nullable|string|max:50',
                'historico_complementar' => 'nullable|string|max:500',
                'comprovacao_fiscal' => 'required|boolean',
                'anexos.*' => 'nullable|file|mimes:jpeg,png,pdf|max:2048',
                'entidade_id' => 'required|exists:entidades_financeiras,id', // Valida entidade
            ], [
                'valor.regex' => 'O valor deve estar no formato correto (exemplo: 1234.56).',
                'tipo.in' => 'O tipo deve ser "entrada" ou "saída".',
            ]);

            // Se a validação falhar
            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    \Flasher\Laravel\Facade\Flasher::addError($error);
                }
                return redirect()->back()->withInput();
            }

            // Validação bem-sucedida
            $validatedData = $validator->validated();

            // Busca o registro no banco de dados
            $banco = TransacaoFinanceira::findOrFail($id);
            $movimentacao = Movimentacao::findOrFail($banco->movimentacao_id);

            // Ajusta o saldo da entidade antes de atualizar os valores
            // 1) Entidade antiga vinculada à movimentação
            $oldEntidade = EntidadeFinanceira::findOrFail($movimentacao->entidade_id);

            // Reverte o impacto do lançamento antigo no saldo da entidade
            // 2) Reverter saldo antigo
            if ($movimentacao->tipo === 'entrada') {
                $oldEntidade->saldo_atual -= $movimentacao->valor;
            } else {
                $oldEntidade->saldo_atual += $movimentacao->valor;
            }
            $oldEntidade->save();

            // 3) Atualiza a movimentação (agora ela aponta para a nova entidade e novo valor)
            $movimentacao->update([
                'entidade_id' => $validatedData['entidade_id'],
                'tipo'        => $validatedData['tipo'],
                'valor'       => $validatedData['valor'],
                'descricao'   => $validatedData['descricao'],
                'updated_by'  => Auth::user()->id,
            ]);

            // 4) Entidade nova escolhida no form
            $newEntidade = EntidadeFinanceira::findOrFail($validatedData['entidade_id']);

            // 5) Aplicar o valor atualizado na nova entidade
            if ($validatedData['tipo'] === 'entrada') {
                $newEntidade->saldo_atual += $validatedData['valor'];
            } else {
                $newEntidade->saldo_atual -= $validatedData['valor'];
            }
            $newEntidade->save();

            // Atualiza os dados do banco
            $validatedData['movimentacao_id'] = $movimentacao->id; // Mantém o vínculo com a movimentação
            $banco->update($validatedData);

            // Verifica se há anexos enviados
            if ($request->hasFile('anexos')) {
                foreach ($request->file('anexos') as $anexo) {
                    // Gera um nome único para o anexo
                    $anexoName = Str::uuid() . '_' . $anexo->getClientOriginalName();

                    // Salva o arquivo no diretório público
                    $anexoPath = $anexo->storeAs('anexos', $anexoName, 'public');

                    // Cria o registro do anexo no banco de dados
                    Anexo::create([
                        'banco_id' => $banco->id,
                        'nome_arquivo' => $anexoName,
                        'caminho_arquivo' => $anexoPath,
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                    ]);
                }
            }

            // Adiciona mensagem de sucesso
            Flasher::addSuccess('Lançamento atualiazado com sucesso!');
            return redirect()->back()->with('message', 'Atualizado com sucesso!');
        } catch (\Exception $e) {
            // Log de erro e mensagem de retorno
            Log::error('Erro ao atualizar movimentação: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Erro ao atualizar: ' . $e->getMessage());
        }
    }

    /**
     * Formata os dados do request antes de processá-los.
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    private function formatarDadosRequest($request)
    {
        // Tratar o valor do campo "valor"
        if ($request->has('valor')) {
            $request->merge([
                'valor' => str_replace(',', '.', str_replace('.', '', $request->input('valor')))
            ]);
        }

        // Converter data_competencia para o formato correto
        if ($request->has('data_competencia')) {
            $request->merge([
                'data_competencia' => Carbon::createFromFormat('d/m/Y', $request->input('data_competencia'))->format('Y-m-d')
            ]);
        }
    }

    /**
     * Reverte o saldo da entidade para antes da atualização.
     *
     * @param \App\Models\Movimentacao $movimentacao
     * @return void
     */
    private function reverterSaldoEntidade($movimentacao)
    {
        $entidade = EntidadeFinanceira::findOrFail($movimentacao->entidade_id);

        if ($movimentacao->tipo === 'entrada') {
            $entidade->saldo_atual -= $movimentacao->valor;
        } else {
            $entidade->saldo_atual += $movimentacao->valor;
        }

        $entidade->save();
    }

    /**
     * Aplica o novo saldo na entidade financeira.
     *
     * @param int $entidadeId
     * @param string $tipo
     * @param float $valor
     * @return void
     */
    private function aplicarSaldoEntidade($entidadeId, $tipo, $valor)
    {
        $entidade = EntidadeFinanceira::findOrFail($entidadeId);

        if ($tipo === 'entrada') {
            $entidade->saldo_atual += $valor;
        } else {
            $entidade->saldo_atual -= $valor;
        }

        $entidade->save();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            // 1) Localiza a transação financeira pelo ID
            $transacao = TransacaoFinanceira::findOrFail($id);

            // 2) Localiza a movimentação associada
            $movimentacao = Movimentacao::findOrFail($transacao->movimentacao_id);

            // 3) Localiza a entidade financeira associada
            $entidade = EntidadeFinanceira::findOrFail($movimentacao->entidade_id);

            // 4) Ajusta o saldo da entidade financeira
            // Obs.: aqui deve subtrair ou somar usando $movimentacao->valor (não $entidade->valor)
            if ($movimentacao->tipo === 'entrada') {
                // Se a movimentação era uma entrada, subtrai o valor do saldo atual
                $entidade->saldo_atual -= $transacao->valor;
            } else {
                // Se a movimentação era uma saída, adiciona o valor ao saldo atual
                $entidade->saldo_atual += $transacao->valor;
            }
            $entidade->save();

            // 5) Excluir anexos associados (se houver)
            $anexos = ModulosAnexo::where('anexavel_id', $transacao->id)
                ->where('anexavel_type', TransacaoFinanceira::class)
                ->get();

            foreach ($anexos as $anexo) {
                // Remove o arquivo do disco, se existir
                if (Storage::disk('public')->exists($anexo->caminho_arquivo)) {
                    Storage::disk('public')->delete($anexo->caminho_arquivo);
                }
                // Exclui o registro no banco
                $anexo->delete();
            }

            // 6) Exclui a movimentação associada
            $movimentacao->delete();

            // 7) Exclui a transação financeira
            $transacao->delete();

            // 8) Mensagem de sucesso e redirecionamento
            Flasher::addSuccess('Transação excluída com sucesso!');
            return redirect()->route('caixa.list');

        } catch (\Exception $e) {
            // 9) Em caso de erro, registra log e retorna com mensagem de erro
            Log::error('Erro ao excluir transação: ' . $e->getMessage());
            Flasher::addError('Erro ao excluir transação: ' . $e->getMessage());
            return redirect()->back();
        }
    }
}
