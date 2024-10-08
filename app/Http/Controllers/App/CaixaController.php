<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Anexo;
use App\Models\Banco;
use App\Models\CadastroBanco;
use App\Models\Caixa;
use App\Models\Company;
use App\Models\LancamentoPadrao;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Facades\Activity; // Importe a facade Activity


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

        $subsidiaryId = User::getCompany();

        // Converte a data do formato 'd-m-Y' para 'Y-m-d'
        $dataCompetencia = Carbon::createFromFormat('d-m-Y', $request->input('data_competencia'))->format('Y-m-d');

        $validator = Validator::make($request->all(), [
            'data_competencia' => 'required|date',
            'descricao' => 'required|string',
            'valor' => 'required',
            'tipo' => 'required|in:entrada,saida',
            'lancamento_padrao' => 'required',
            'centro' => 'required|string',
            'tipo_documento' => 'required|string',
            'numero_documento' => 'nullable|string',
            'files.*' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'historico_complementar' => 'nullable|string|max:500',
            // Campos adicionais
            'banco_id' => 'nullable|exists:cadastro_bancos,id',
        ]);

        $user = auth()->user(); // Usuário autenticado

        $validatedData = $validator->validated();
        // Adiciona a data convertida ao array de dados validados
        $validatedData['data_competencia'] = $dataCompetencia;
        $validatedData['company_id'] = $subsidiaryId->company_id;
        $validatedData['origem'] = 'CX';

        $validatedData['valor'] = str_replace(',', '.', str_replace('.', '', $validatedData['valor']));
        // Adiciona os campos de auditoria
        $validatedData['created_by'] = $user->id;
        $validatedData['updated_by'] = $user->id;


        $caixa = Caixa::create($validatedData);


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
                    'caixa_id' => $caixa->id,
                    'nome_arquivo' => $anexoName,
                    'caminho_arquivo' => $anexoPath,
                    'created_by' => $user->id,
                    'updated_by' => $user->id,
                ]);
            }
        }

        // Verificação para criar lançamento no banco
        if ($validatedData['lancamento_padrao'] === 'Deposito Bancário') {
            // Aqui você pode ajustar para a lógica do seu sistema de criação de lançamentos no banco
            $validatedData['origem'] = 'BC';
            $validatedData['tipo'] = 'entrada';
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
        }


        return redirect()->route('caixa.index')->with('success', ' Lançamento Realizado com Sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Caixa $caixa)
    {
        return view('app.financeiro.caixa.show');
    }

    public function list()
    {
        $user = Auth::user();

        list($somaEntradas, $somaSaida) = caixa::getCaixa();

        $total = $somaEntradas - $somaSaida;

        $caixas = Caixa::getCaixaList();
        $valorEntrada = caixa::getCaixaEntrada();
        $ValorSaidas = caixa::getCaixaSaida();
        $company = $user->companies;


        return view('app.financeiro.caixa.list', [
            'caixas' => $caixas,
            'valorEntrada' => $valorEntrada,
            'ValorSaidas' => $ValorSaidas,
            'total' => $total,
            'company' => $company
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $lps = LancamentoPadrao::all();

        $caixa = Caixa::with('anexos')->findOrFail($id);

        $bancos = CadastroBanco::getCadastroBanco(); // Chama o método para obter os bancos


        return view('app.financeiro.caixa.edit', compact('caixa', 'bancos', 'lps'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $subsidiaryId = User::getCompany();

        $validator = Validator::make($request->all(), [
            'data_competencia' => 'required|date',
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

        $caixa = Caixa::findOrFail($id); // Encontra o registro existente

        $validatedData = $validator->validated();
        $validatedData['company_id'] = $subsidiaryId->company_id;
        $validatedData['valor'] = str_replace(',', '.', str_replace('.', '', $validatedData['valor']));

        // Atualiza o registro existente
        $caixa->update($validatedData);

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
                    'nome_arquivo' => $anexoName,
                    'caminho_arquivo' => $anexoPath,
                    'created_by' => $user->id,
                    'updated_by' => $user->id,
                ]);
            }
        }

        return redirect()->route('caixa.index')->with('success', 'Lançamento Atualizado com Sucesso!');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Localize o registro com base no ID fornecido
        $banco = Caixa::findOrFail($id);

        // Exclua o registro
        $banco->delete();

        return redirect()->route('caixa.index');
    }

    public function destroySelected($id)
    {
        $file = Anexo::findOrFail($id);

        // Excluir o arquivo do sistema de arquivos
        Storage::delete($file->caminho_arquivo);

        // Excluir o registro do banco de dados
        $file->delete();

        return response()->json(['success' => true]);
    }
}
