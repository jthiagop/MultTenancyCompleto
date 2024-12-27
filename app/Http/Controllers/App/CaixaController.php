<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Anexo;
use App\Models\Banco;
use App\Models\CadastroBanco;
use App\Models\Caixa;
use App\Models\Company;
use App\Models\EntidadeFinanceira;
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

        ]);
    }

    public function list()
    {
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
        $caixas = Caixa::getCaixaList($companyId);

        // Obter os valores de entrada e saída de caixa para a empresa
        $valorEntrada = Caixa::getCaixaEntrada($companyId);
        /** @var TYPE_NAME $valorSaidas */
        $valorSaidas = Caixa::getCaixaSaida($companyId);

        // Obter informações da empresa associada ao usuário (ajustando para relacionamento)
        $company = $user->company;

        return view('app.financeiro.caixa.list', [
            'caixas' => $caixas,
            'valorEntrada' => $valorEntrada,
            'valorSaidas' => $valorSaidas,
            'total' => $total,
            'lps' => $lps,
            'bancos' => $bancos,
            'company' => $company,
            'entidades' => $entidades,
            'entidadesBanco' => $entidadesBanco,


        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $lps = LancamentoPadrao::all();

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
    public function store(Request $request)
    {
        try {
            $subsidiaryId = User::getCompany();

            // Converte a data do formato 'd-m-Y' para 'Y-m-d'
            $dataCompetencia = Carbon::createFromFormat('d-m-Y', $request->input('data_competencia'))->format('Y-m-d');
            // Validação dos dados
            $validator = Validator::make($request->all(), [
                'data_competencia' => 'required|date',
                'descricao' => 'required|string',
                'valor' => 'required', // Garante que o valor é numérico
                'tipo' => 'required|in:entrada,saida',
                'lancamento_padrao_id' => 'required|exists:lancamento_padraos,id',
                'centro' => 'required|string',
                'tipo_documento' => 'required|string',
                'numero_documento' => 'nullable|string',
                'files.*' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
                'historico_complementar' => 'nullable|string|max:500',
                'entidade_banco_id' => 'nullable|exists:entidades_financeiras,id',
                'comprovacao_fiscal' => 'required|boolean', // Garante que seja 0 ou 1
                'entidade_id' => 'required|exists:entidades_financeiras,id', // Valida se a entidade existe
            ], [
                'entidade_id.required' => 'A entidade é obrigatória.',
                'entidade_id.exists' => 'A entidade selecionada não é válida.',
            ]);

            // Verifica se a validação falhou
            if ($validator->fails()) {

                // Adiciona os erros ao PHPFlasher
                foreach ($validator->errors()->all() as $error) {
                    Flasher::addError($error);
                }

                return redirect()->back()->withErrors($validator)->withInput();
            }

            $validatedData = $validator->validated();
            // Adiciona a data convertida ao array de dados validados
            $validatedData['data_competencia'] = $dataCompetencia;
            $validatedData['company_id'] = $subsidiaryId->company_id;
            $validatedData['origem'] = 'CX';

            $validatedData['valor'] = str_replace(',', '.', str_replace('.', '', $validatedData['valor']));
            // Adiciona os campos de auditoria
            $validatedData['created_by'] = Auth::user();
            $validatedData['created_by_name'] = Auth::user()->user()->name;
            $validatedData['updated_by'] = Auth::user();
            $validatedData['updated_by_name'] = Auth::user()->user()->name;

            // Cria o lançamento na tabela 'movimentacoes'
            try {
                $movimentacao = Movimentacao::create([
                    'entidade_id' => $validatedData['entidade_id'],
                    'tipo' => $validatedData['tipo'],
                    'valor' => $validatedData['valor'],
                    'data' => $validatedData['data_competencia'],
                    'descricao' => $validatedData['descricao'],
                    'company_id' => $subsidiaryId->company_id,
                    'created_by' => Auth::user(),
                    'created_by_name' => Auth::user()->user()->name,
                    'updated_by' => Auth::user(),
                    'updated_by_name' => Auth::user()->user()->name,
                ]);
            } catch (\Exception $e) {
                // Log de erro
                dd('Erro ao criar movimentação:', $e->getMessage(), $e->getTrace());
                Log::error('Erro ao criar movimentação: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Erro ao criar a movimentação.');
            }

            // Cria o lançamento na tabela 'caixa'
            $validatedData['movimentacao_id'] = $movimentacao->id; // Vincula a movimentação

            // Cria o registro no caixa
            $caixa = Caixa::create($validatedData);

            // Verifica e processa lançamentos padrão
            $lancamentoPadrao = LancamentoPadrao::find($validatedData['lancamento_padrao_id']);
            if ($lancamentoPadrao && $lancamentoPadrao->description === 'Deposito Bancário') {
                $validatedData['origem'] = 'BC';
                $validatedData['tipo'] = 'entrada';
                $validatedData['comprovacao_fiscal'] = $validatedData['comprovacao_fiscal'];

                // Cria o lançamento na tabela 'movimentacoes'
                try {
                    $movimentacao = Movimentacao::create([
                        'entidade_id' => $validatedData['entidade_banco_id'],
                        'tipo' => $validatedData['tipo'],
                        'valor' => $validatedData['valor'],
                        'descricao' => $validatedData['descricao'],
                        'company_id' => $subsidiaryId->company_id,
                        'created_by' => Auth::user()->id(),
                        'created_by_name' => Auth::user()->user()->name,
                        'updated_by' => Auth::user()->id(),
                        'updated_by_name' => Auth::user()->user()->name,
                    ]);
                } catch (\Exception $e) {
                    // Log de erro
                    dd('Erro ao criar movimentação:', $e->getMessage(), $e->getTrace());
                    Log::error('Erro ao criar movimentação: ' . $e->getMessage());
                    return redirect()->back()->with('error', 'Erro ao criar a movimentação.');
                }

                // Cria o lançamento na tabela 'caixa'
                $validatedData['movimentacao_id'] = $movimentacao->id; // Vincula a movimentação

                $banco = Banco::create($validatedData);

                if ($request->hasFile('files')) {
                    foreach ($request->file('files') as $anexo) {
                        $anexoName = time() . '_' . $anexo->getClientOriginalName();
                        $anexoPath = $anexo->storeAs('anexos', $anexoName, 'public');

                        Anexo::create([
                            'banco_id' => $banco->id,
                            'nome_arquivo' => $anexoName,
                            'caminho_arquivo' => $anexoPath,
                            'size' => $anexo->getSize(), // Tamanho do arquivo
                            'created_by' => Auth::user()->id(),
                            'updated_by' => Auth::user()->id(),
                        ]);
                    }
                }
            }


            // Verifica se há arquivos anexos
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $anexo) {
                    $anexoName = time() . '_' . $anexo->getClientOriginalName();
                    $anexoPath = $anexo->storeAs('anexos', $anexoName, 'public');

                    Anexo::create([
                        'caixa_id' => $caixa->id,
                        'nome_arquivo' => $anexoName,
                        'caminho_arquivo' => $anexoPath,
                        'size' => $anexo->getSize(), // Tamanho do arquivo
                        'created_by' => Auth::user()->id(),
                        'updated_by' => Auth::user()->id(),
                    ]);
                }
            }
        // Mensagem de sucesso
        flash()->success('O livro foi salvo com sucesso!');

            // Exibe a mensagem diretamente usando o Flasher e redireciona
            return redirect()->back();
        } catch (\Exception $e) {
            // Adiciona mensagem de erro com detalhes da exceção
            Flasher::addError('Ocorreu um erro ao processar o lançamento: ' . $e->getMessage());

            // Retorna com os dados antigos e exibe as mensagens de erro
            return redirect()->back()->withInput();
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
        $lps = LancamentoPadrao::all();

        //Aqui, movimentacao.entidade busca a entidade associada à movimentação.
        $caixa = Caixa::with(['anexos', 'movimentacao.entidade'])->findOrFail($id);

        $bancos = CadastroBanco::getCadastroBanco(); // Chama o método para obter os bancos


        return view('app.financeiro.caixa.edit', compact('caixa', 'bancos', 'lps'));
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
            $caixa = Caixa::findOrFail($id);
            $movimentacao = Movimentacao::findOrFail($caixa->movimentacao_id);

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
                'updated_by' => Auth::user()->id(),
            ]);

            // Ajusta o impacto do novo valor no saldo
            if ($validatedData['tipo'] === 'entrada') {
                $entidade->saldo_atual += $validatedData['valor'];
            } else {
                $entidade->saldo_atual -= $validatedData['valor'];
            }

            // Salva o novo saldo
            $entidade->save();

            // Atualiza os dados do caixa
            $validatedData['movimentacao_id'] = $movimentacao->id; // Mantém o vínculo com a movimentação
            $caixa->update($validatedData);

            // Verifica se há anexos enviados
            if ($request->hasFile('anexos')) {
                foreach ($request->file('anexos') as $anexo) {
                    // Gera um nome único para o anexo
                    $anexoName = Str::uuid() . '_' . $anexo->getClientOriginalName();

                    // Salva o arquivo no diretório público
                    $anexoPath = $anexo->storeAs('anexos', $anexoName, 'public');

                    // Cria o registro do anexo no banco de dados
                    Anexo::create([
                        'caixa_id' => $caixa->id,
                        'nome_arquivo' => $anexoName,
                        'size' => $anexo->getSize(), // Tamanho do arquivo
                        'caminho_arquivo' => $anexoPath,
                        'created_by' => Auth::user()->id(),
                        'updated_by' => Auth::user()->id(),
                    ]);
                }
            }
                // Adiciona mensagem de sucesso
                Flasher::addSuccess('Lançamento excluído com sucesso!');

            // Exibe mensagem de sucesso
            return redirect()->back();
        } catch (\Exception $e) {
            // Log de erro e mensagem de retorno
            Log::error('Erro ao atualizar movimentação: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Erro ao atualizar: ' . $e->getMessage());
        }
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            // Localize o registro do Caixa com base no ID fornecido
            $caixa = Caixa::with('anexos', 'movimentacao')->findOrFail($id);

            // Exclua os anexos associados (se existirem)
            if ($caixa->anexos) {
                foreach ($caixa->anexos as $anexo) {
                    // Exclua o arquivo do armazenamento
                    Storage::disk('public')->delete($anexo->caminho_arquivo);

                    // Exclua o registro do anexo no banco de dados
                    $anexo->delete();
                }
            }

            // Verifique se há uma movimentação associada e exclua
            if ($caixa->movimentacao) {
                $caixa->movimentacao->delete();

                // Atualize o saldo da entidade associada à movimentação
                $entidade = $caixa->movimentacao->entidade;
                if ($entidade) {
                    $entidade->atualizarSaldo();
                }
            }

            // Exclua o registro do Caixa
            $caixa->delete();

                // Adiciona mensagem de sucesso
            Flasher::addSuccess('Lançamento excluído com sucesso!');
            // Redireciona para a lista de caixas com mensagem de sucesso
            return redirect()
                ->route('caixa.list') // Substitua 'caixa.list' pelo nome correto da rota
                ->with('message', 'Registro excluído com sucesso!');
        } catch (\Exception $e) {
            // Log de erro
            Log::error('Erro ao excluir registro do Caixa: ' . $e->getMessage());

            // Redireciona para a lista de caixas com mensagem de erro
            return redirect()
                ->route('caixa.list') // Substitua 'caixa.list' pelo nome correto da rota
                ->with('message', 'Erro ao excluir o registro.');
        }
    }


    public function destroySelected($id)
    {

        dd($id);

        $file = Anexo::findOrFail($id);

        // Excluir o arquivo do sistema de arquivos
        Storage::delete($file->caminho_arquivo);

        // Excluir o registro do banco de dados
        $file->delete();

        return response()->json(['success' => true]);
    }
}
