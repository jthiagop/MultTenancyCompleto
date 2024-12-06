<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Anexo;
use App\Models\Banco;
use App\Models\CadastroBanco;
use App\Models\EntidadeFinanceira;
use App\Models\LancamentoPadrao;
use App\Models\Movimentacao;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Flasher\Laravel\Facade\Flasher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class BancoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $valorEntradaBanco = Banco::getBancoEntrada();
        $ValorSaidasBanco = Banco::getBancoSaida();

        $entidadesBanco = Banco::getEntidadesBanco();


        $lps = LancamentoPadrao::all();
        $bancos = CadastroBanco::getCadastroBanco(); // Chama o método para obter os bancos

        return view('app.financeiro.index', [
            'valorEntradaBanco' => $valorEntradaBanco,
            'ValorSaidasBanco' => $ValorSaidasBanco,
            'lps' => $lps,
            'bancos' => $bancos,
            'entidadesBanco' => $entidadesBanco,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $company = User::getCompanyName();
        $lps = LancamentoPadrao::all();
        $bancos = CadastroBanco::getCadastroBanco(); // Chama o método para obter os bancos

        return view('app.financeiro.banco.create', [
            'lps' => $lps,
            'bancos' => $bancos,
            'company' => $company,

        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Recupera a companhia associada ao usuário autenticado
            $subsidiaryId = User::getCompany();

            if (!$subsidiaryId) {
                return redirect()->back()->with('error', 'Companhia não encontrada.');
            }

            // Converte a data de competência para o formato correto
            $dataCompetencia = Carbon::parse($request->input('data_competencia'))->format('Y-m-d');

            // Validação dos dados do request
            $validator = Validator::make($request->all(), [
                'data_competencia' => 'required|date',
                'descricao' => 'nullable|string',
                'valor' => ['required', 'regex:/^\d{1,3}(\.\d{3})*(,\d{2})?$/'],
                'tipo' => 'required|in:entrada,saida',
                'lancamento_padrao_id' => 'required|exists:lancamento_padraos,id',
                'centro' => 'nullable|string',
                'tipo_documento' => 'nullable|string',
                'numero_documento' => 'nullable|string',
                'files.*' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
                'historico_complementar' => 'nullable|string|max:500',
                'banco_id' => 'nullable|exists:cadastro_bancos,id',
                'comprovacao_fiscal' => 'required|boolean', // Garante que seja 0 ou 1
                'entidade_id' => 'required|exists:entidades_financeiras,id', // Valida se a entidade existe

            ], [
                'valor.regex' => 'O valor deve estar no formato correto (exemplo: 1.234,56).',
                'tipo.in' => 'O tipo deve ser "entrada" ou "saída".',
                'files.*.mimes' => 'Os arquivos devem ser do tipo: jpeg, png, jpg, pdf.',
                'files.*.max' => 'Cada arquivo deve ter no máximo 2MB.',
                'entidade_id.required' => 'A entidade é obrigatória.',
                'entidade_id.exists' => 'A entidade selecionada não é válida.',
            ]);
            // Retorna erros de validação, se houver
            // Verifica se a validação falhou
            if ($validator->fails()) {

                // Adiciona os erros ao PHPFlasher
                foreach ($validator->errors()->all() as $error) {
                    Flasher::addError($error);
                }

                return redirect()->back()->withErrors($validator)->withInput();
            }

            // Usuário autenticado
            $user = auth()->user();

            $validatedData = $validator->validated();

            // Adiciona a data convertida ao array de dados validados
            $validatedData['data_competencia'] = $dataCompetencia;
            $validatedData['company_id'] = $subsidiaryId->company_id;
            $validatedData['origem'] = 'BC';

            $validatedData['valor'] = str_replace(',', '.', str_replace('.', '', $validatedData['valor']));

            // Adiciona os campos de auditoria
            $validatedData['created_by'] = auth()->id();
            $validatedData['created_by_name'] = auth()->user()->name;
            $validatedData['updated_by'] = auth()->id();
            $validatedData['updated_by_name'] = auth()->user()->name;

            // Cria o lançamento na tabela 'movimentacoes'
            try {
                $movimentacao = Movimentacao::create([
                    'entidade_id' => $validatedData['entidade_id'],
                    'tipo' => $validatedData['tipo'],
                    'valor' => $validatedData['valor'],
                    'descricao' => $validatedData['descricao'],
                    'company_id' => $subsidiaryId->company_id,
                    'created_by' => auth()->id(),
                    'created_by_name' => auth()->user()->name,
                    'updated_by' => auth()->id(),
                    'updated_by_name' => auth()->user()->name,
                ]);
            } catch (\Exception $e) {
                // Log de erro
                dd('Erro ao criar movimentação:', $e->getMessage(), $e->getTrace());
                Log::error('Erro ao criar movimentação: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Erro ao criar a movimentação.');
            }

            // Cria o lançamento na tabela 'caixa'
            $validatedData['movimentacao_id'] = $movimentacao->id; // Vincula a movimentação


            // Cria o registro no banco de dados
            $banco = Banco::create($validatedData);

            // Verifica se há arquivos anexos
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $anexo) {
                    $anexoName = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $anexo->getClientOriginalName());
                    $anexoPath = $anexo->storeAs('anexos', $anexoName, 'public');

                    Anexo::create([
                        'banco_id' => $banco->id,
                        'nome_arquivo' => $anexoName,
                        'caminho_arquivo' => $anexoPath,
                        'created_by' => $user->id,
                        'updated_by' => $user->id,
                    ]);
                }
            }

            // Adiciona mensagem de sucesso
            Flasher::addSuccess('Lançamento criado com sucesso!');

            // Exibe mensagem de sucesso
            return redirect()->back()->with('message', 'Lançamento criado com sucesso!');
        } catch (\Exception $e) {
            // Adiciona mensagem de erro
            Flasher::addError('Ocorreu um erro ao processar o lançamento: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }




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
                'centro' => 'required|string|max:255',
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
                dd($validator->errors()->all());
                foreach ($validator->errors()->all() as $error) {
                    \Flasher\Laravel\Facade\Flasher::addError($error);
                }
                return redirect()->back()->withInput();
            }

            // Validação bem-sucedida
            $validatedData = $validator->validated();

            // Busca o registro no banco de dados
            $banco = Banco::findOrFail($id);
            $movimentacao = Movimentacao::findOrFail($banco->movimentacao_id);

            // Ajusta o saldo da entidade antes de atualizar os valores
            $entidade = EntidadeFinanceira::findOrFail($validatedData['entidade_id']);

            // Reverte o impacto do lançamento antigo no saldo da entidade
            if ($movimentacao->tipo === 'entrada') {
                $entidade->saldo_atual -= $movimentacao->valor;
            } else {
                $entidade->saldo_atual += $movimentacao->valor;
            }

            // Atualiza os dados da movimentação
            $movimentacao->update([
                'entidade_id' => $validatedData['entidade_id'],
                'tipo' => $validatedData['tipo'],
                'valor' => $validatedData['valor'],
                'descricao' => $validatedData['descricao'],
                'updated_by' => auth()->id(),
            ]);

            // Ajusta o impacto do novo valor no saldo
            if ($validatedData['tipo'] === 'entrada') {
                $entidade->saldo_atual += $validatedData['valor'];
            } else {
                $entidade->saldo_atual -= $validatedData['valor'];
            }

            // Salva o novo saldo
            $entidade->save();

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
                        'created_by' => auth()->id(),
                        'updated_by' => auth()->id(),
                    ]);
                }
            }

            // Exibe mensagem de sucesso
            return redirect()->back()->with('message', 'Atualizado com sucesso!');
        } catch (\Exception $e) {
            // Log de erro e mensagem de retorno
            Log::error('Erro ao atualizar movimentação: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Erro ao atualizar: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Banco $banco)
    {
        //
    }

    public function list()
    {
        // Suponha que você já tenha o ID da empresa disponível
        $companyId = auth()->user()->company_id; // ou $companyId = 1; se o ID for fixo

        $lps = LancamentoPadrao::all();
        $bancos = CadastroBanco::getCadastroBanco(); // Chama o método para obter os bancos

        // Filtrar as entradas e saídas pelos bancos relacionados à empresa
        list($somaEntradas, $somaSaida) = Banco::getBanco();

        $total  = EntidadeFinanceira::getValorTotalEntidadeBC();

        $entidadesBanco = Banco::getEntidadesBanco();


        $valorEntrada = Banco::getBancoEntrada();
        $ValorSaidas = Banco::getBancoSaida();

    // Carregar bancos com entidades financeiras relacionadas
    $IfBancos = Banco::with('movimentacao.entidade')
        ->where('company_id', $companyId)
        ->get();



        return view('app.financeiro.banco.list', [
            'bancos' => $bancos,
            'valorEntrada' => $valorEntrada,
            'ValorSaidas' => $ValorSaidas,
            'total' => $total,
            'lps' => $lps,
            'IfBancos' => $IfBancos,
            'entidadesBanco' => $entidadesBanco
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $bancosCadastro = CadastroBanco::getCadastroBanco(); // Chama o método para obter os bancos

        $lps = LancamentoPadrao::all();

        $banco = Banco::with('anexos')->findOrFail($id);

        $banco->anexos = $banco->anexos ?? collect();


        return view(
            'app.financeiro.banco.edit',
            [
                'banco' => $banco,
                'lps'   => $lps,
                'bancosCadastro' => $bancosCadastro
            ]
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            // Localize o registro do Caixa com base no ID fornecido
            $banco = Banco::with('anexos', 'movimentacao')->findOrFail($id);

            // Exclua os anexos associados (se existirem)
            if ($banco->anexos) {
                foreach ($banco->anexos as $anexo) {
                    // Exclua o arquivo do armazenamento
                    Storage::disk('public')->delete($anexo->caminho_arquivo);

                    // Exclua o registro do anexo no banco de dados
                    $anexo->delete();
                }
            }

            // Verifique se há uma movimentação associada e exclua
            if ($banco->movimentacao) {
                $banco->movimentacao->delete();

                // Atualize o saldo da entidade associada à movimentação
                $entidade = $banco->movimentacao->entidade;
                if ($entidade) {
                    $entidade->atualizarSaldo();
                }
            }

            // Exclua o registro do Caixa
            $banco->delete();

            Flasher::addSuccess('Lançamento excluído com sucesso!');
            // Redireciona para a lista de caixas com mensagem de sucesso
            return redirect()
                ->route('banco.list') // Substitua 'banco.list' pelo nome correto da rota
                ->with('message', 'Registro excluído com sucesso!');
        } catch (\Exception $e) {
            // Log de erro
            Log::error('Erro ao excluir registro do Caixa: ' . $e->getMessage());

            // Redireciona para a lista de bancos com mensagem de erro
            return redirect()
                ->route('banco.list') // Substitua 'banco.list' pelo nome correto da rota
                ->with('message', 'Erro ao excluir o registro.');
        }
    }
}
