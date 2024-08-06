<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Anexo;
use App\Models\Banco;
use App\Models\CadastroBanco;
use App\Models\LancamentoPadrao;
use App\Models\User;
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

        return view('app.financeiro.index', [
            'valorEntradaBanco' => $valorEntradaBanco,
            'ValorSaidasBanco' => $ValorSaidasBanco
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $company = User::getCompanyName();
        $lps = LancamentoPadrao::all();
        $bancos = CadastroBanco::geCadastroBanco(); // Chama o método para obter os bancos

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

        // Dados validados
        $validatedData = $validator->validated();
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
        return redirect()->route('caixa.index')->with('success', 'Banco registrado com sucesso!');
    }

    public function update(Request $request, $id)
    {

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
        list($somaEntradas, $somaSaida) = banco::getBanco();

        $total = $somaEntradas - $somaSaida;

        $valorEntrada = banco::getBancoEntrada();
        $ValorSaidas = banco::getBancoSaida();

        $bancos = Banco::all();

        return view('app.financeiro.banco.list', [
            'bancos' => $bancos,
            'valorEntrada' => $valorEntrada,
            'ValorSaidas' => $ValorSaidas,
            'total' => $total,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit( $id)
    {
        $bancosCadastro = CadastroBanco::geCadastroBanco(); // Chama o método para obter os bancos

        $lps = LancamentoPadrao::all();

        $banco = Banco::with('anexos')->findOrFail($id);

        return view('app.financeiro.banco.edit', compact('banco', 'lps', 'bancosCadastro'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
    }

}

