<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Anexo;
use App\Models\Banco;
use App\Models\CadastroBanco;
use App\Models\LancamentoPadrao;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
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

        $lps = LancamentoPadrao::all();
        $bancos = CadastroBanco::getCadastroBanco(); // Chama o método para obter os bancos

        return view('app.financeiro.index', [
            'valorEntradaBanco' => $valorEntradaBanco,
            'ValorSaidasBanco' => $ValorSaidasBanco,
            'lps' => $lps,
            'bancos' => $bancos,
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

        // Recupera a companhia associada ao usuário autenticado
        $subsidiaryId = User::getCompany();

        $dataCompetencia = Carbon::createFromFormat('d-m-Y', $request->input('data_competencia'))->format('Y-m-d');

        // Validação dos dados do request
        $validator = Validator::make($request->all(), [
            'data_competencia' => 'required|date',
            'descricao' => 'nullable|string',
            'valor' => 'required',
            'tipo' => 'required|in:entrada,saida',
            'lancamento_padrao' => 'required',
            'centro' => 'nullable|string',
            'tipo_documento' => 'nullable|string',
            'numero_documento' => 'nullable|string',
            'files.*' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'historico_complementar' => 'nullable|string|max:500',
            // Campos adicionais
            'banco_id' => 'nullable|exists:cadastro_bancos,id',
        ]);

        // Retorna erros de validação, se houver
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Usuário autenticado
        $user = auth()->user();


        $validatedData = $validator->validated();
        // Adiciona a data convertida ao array de dados validados
        $validatedData['data_competencia'] = $dataCompetencia;

        $validatedData['company_id'] = $subsidiaryId->company_id;
        $validatedData['origem'] = 'BC';

        // Formata o valor para o formato correto
        $validatedData['valor'] = str_replace(',', '.', str_replace('.', '', $validatedData['valor']));
        // Adiciona os campos de auditoria
        $validatedData['created_by'] = $user->id;
        $validatedData['updated_by'] = $user->id;


        // Cria o registro no banco de dados
        $banco = Banco::create($validatedData);

        // Verifica se há arquivos anexos
        if ($request->hasFile('files')) {
            // Itera sobre cada arquivo anexo
            foreach ($request->file('files') as $anexo) {
                // Gera um nome único para o arquivo anexo
                $anexoName = time() . '_' . $anexo->getClientOriginalName();

                // Salva o arquivo na pasta 'anexos' dentro da pasta de armazenamento (storage/app/public)
                $anexoPath = $anexo->storeAs('anexos', $anexoName, 'public');

                // Cria um registro no banco de dados para o anexo
                Anexo::create([
                    'banco_id' => $banco->id,
                    'nome_arquivo' => $anexoName,
                    'caminho_arquivo' => $anexoPath,
                    'created_by' => $user->id,
                    'updated_by' => $user->id,
                ]);
            }
        }

        // Redireciona para a página de índice com uma mensagem de sucesso
        return redirect()->route('caixa.index')->with('success', 'Lançamento registrado com sucesso!');
    }

    public function update(Request $request, $id)
    {
        $subsidiaryId = User::getCompany();

        $validator = Validator::make($request->all(), [
            'data_competencia' => 'required|date',
            'banco_id' => 'required',
            'descricao' => 'required|string',
            'valor' => 'required',
            'tipo' => 'required|in:entrada,saida',
            'lancamento_padrao' => 'required|string',
            'centro' => 'required|string',
            'tipo_documento' => 'required|string',
            'numero_documento' => 'nullable|string',
            'historico_complementar' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = auth()->user(); // Usuário autenticado

        $caixa = Banco::findOrFail($id); // Encontra o registro existente
        $banco = Banco::findOrFail($id);

        $validatedData = $validator->validated();
        $validatedData['company_id'] = $subsidiaryId->company_id;
        $validatedData['valor'] = str_replace(',', '.', str_replace('.', '', $validatedData['valor']));

        // Atualiza o registro existente
        $banco->update($validatedData);

        // Verifica se há arquivos anexos
        if ($request->hasFile('anexos')) {
            // Itera sobre cada arquivo anexo
            foreach ($request->file('anexos') as $anexo) {
                // Gera um nome único para o arquivo anexo
                $anexoName = time() . '_' . $anexo->getClientOriginalName();

                // Salva o arquivo na pasta 'anexos' dentro da pasta de armazenamento (storage/app/public)
                $anexoPath = $anexo->storeAs('anexos', $anexoName, 'public');

                // Cria um registro no banco de dados para o anexo
                Anexo::create([
                    'caixa_id' => $caixa->id,
                    'banco_id' => $banco->id,
                    'nome_arquivo' => $anexoName,
                    'caminho_arquivo' => $anexoPath,
                    'created_by' => $user->id,
                    'updated_by' => $user->id,
                ]);
            }
        }

        return redirect()->route('banco.list')->with('success', 'Lançamento Atualizado com Sucesso!');
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

        $total = $somaEntradas - $somaSaida;

        $valorEntrada = Banco::getBancoEntrada();
        $ValorSaidas = Banco::getBancoSaida();

        // Filtrar os bancos pela empresa
        $bancos = Banco::where('company_id', $companyId)->get();

        return view('app.financeiro.banco.list', [
            'bancos' => $bancos,
            'valorEntrada' => $valorEntrada,
            'ValorSaidas' => $ValorSaidas,
            'total' => $total,
            'lps' => $lps,
            'bancos' => $bancos,
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
        // Localize o registro com base no ID fornecido
        $banco = Banco::findOrFail($id);

        // Exclua o registro
        $banco->delete();

        return redirect()->route('banco.list');
    }
}
