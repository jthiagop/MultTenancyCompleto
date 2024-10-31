<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Escritura;
use App\Models\NamePatrimonio;
use App\Models\Patrimonio;
use App\Models\PatrimonioAnexo;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PatrimonioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $nameForos = NamePatrimonio::all();
        $patrimonios = Patrimonio::all();

        return view('app.patrimonios.index',
            [
                'nameForos' => $nameForos,
                'patrimonios' => $patrimonios,
            ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $nameForos = NamePatrimonio::all();

        return view('app.patrimonios.create', [
            'nameForos' => $nameForos,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {

                    // Convertendo a data para o formato Y-m-d antes de salvar
        $dataConvertida = Carbon::createFromFormat('d/m/Y', $request->input('data'))->format('Y-m-d');

            // Criação de um novo patrimônio
            $patrimonio = new Patrimonio();
            $patrimonio->descricao = $request->input('descricao');
            $patrimonio->patrimonio = $request->input('patrimonio');
            $patrimonio->data = $dataConvertida; // Usa a data convertida
            $patrimonio->livro = $request->input('livro');
            $patrimonio->folha = $request->input('folha');
            $patrimonio->registro = $request->input('registro');
            $patrimonio->tags = $request->input('tags');
            $patrimonio->cep = $request->input('cep');
            $patrimonio->bairro = $request->input('bairro');
            $patrimonio->logradouro = $request->input('logradouro');
            $patrimonio->localidade = $request->input('localidade');
            $patrimonio->uf = $request->input('uf');
            $patrimonio->complemento = $request->input('complemento');
            $patrimonio['company_id'] = User::getCompany()->company_id;
            $patrimonio['updated_by'] = auth()->user()->id;
            $patrimonio['created_by'] = auth()->user()->id;

            // Salvando o patrimônio no banco de dados
            $patrimonio->save();

            // Usando o `id` como número sequencial para o RID
            $codigoRID = $this->gerarRID($request->input('numIbge'), $request->input('numForo'), $patrimonio->id);

            // Atualizando o patrimônio com o código RID gerado
            $patrimonio->codigo_rid = $codigoRID;
            $patrimonio->save();

            // Verificando se há dados de escritura no request
            if ($request->has(['outorgante', 'matricula', 'aquisicao', 'outorgado', 'valor', 'area_total', 'area_privativa'])) {
                // Criando uma nova escritura
                $escritura = new Escritura();
                $escritura->outorgante = $request->input('outorgante');
                $escritura->matricula = $request->input('matricula');
                $escritura->aquisicao = $request->input('aquisicao');
                $escritura->outorgado = $request->input('outorgado');
                $escritura['valor'] = str_replace(',', '.', str_replace('.', '', $request['valor']));
                $escritura->area_total = $request->input('area_total');
                $escritura->area_privativa = $request->input('area_privativa');
                $escritura->informacoes = $request->input('informacoes');
                $escritura->patrimonio_id = $patrimonio->id;
                $escritura['created_by'] = auth()->user()->id;
                $escritura['updated_by'] = auth()->user()->id;

                // Salvando a escritura no banco de dados
                $escritura->save();
            }

            // Mensagem de sucesso usando flash
            session()->flash('success', 'Patrimônio salvo com sucesso!');

            // Redireciona de volta para a página anterior
            return redirect()->back();
        } catch (\Exception $e) {
            // Flash message de erro
            session()->flash('error', 'Erro ao salvar o Patrimônio: ' . $e->getMessage());

            // Retornar a resposta JSON de erro (se necessário)
            return response()->json(['success' => false, 'message' => 'Erro ao salvar o Patrimônio: ' . $e->getMessage()], 500);
        }
    }



/**
 * Função para gerar o código RID.
 */
private function gerarRID($numIbge, $numForo, $sequencial)
{
    // Código do município
    $codigoMunicipio = str_pad($numIbge, 4, '0', STR_PAD_LEFT);

    // Sequencial dentro do município
    $sequencial = str_pad($sequencial, 4, '0', STR_PAD_LEFT);

    // Formando o número base do RID
    $ridBase = $codigoMunicipio . $sequencial . $numForo;

    // Calculando o dígito verificador
    $digitoVerificador = $this->calcularDV($ridBase);

    // Formando o RID completo
    $rid = $codigoMunicipio . ' ' . $sequencial . '.' . $numForo . '-' . $digitoVerificador;

    return $rid;
}

/**
 * Função para calcular o dígito verificador.
 */
private function calcularDV($ridBase)
{
    // Lógica do módulo 11 para cálculo do dígito verificador
    $soma = 0;
    $peso = 2;

    // Iterar sobre os dígitos do RID base (de trás para frente)
    for ($i = strlen($ridBase) - 1; $i >= 0; $i--) {
        $soma += $ridBase[$i] * $peso;
        $peso++;

        // Reinicia o peso se ultrapassar 9
        if ($peso > 9) {
            $peso = 2;
        }
    }

    // Obter o módulo 11
    $resto = $soma % 11;

    // Verificar as regras para o dígito verificador
    if ($resto == 0 || $resto == 1) {
        $dv = 0;
    } else {
        $dv = 11 - $resto;
    }

    return $dv;
}


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            // Busca o patrimônio pelo ID
            $patrimonio = Patrimonio::findOrFail($id);
            $nameForos = NamePatrimonio::all();
            $escrituras = Escritura::where('patrimonio_id', $id)->get();
            $anexos = PatrimonioAnexo::where('patrimonio_id', $id)->get();


            // Retorna a view padrão 'patrimonios.show' com os detalhes do patrimônio
            return view('app.patrimonios.show', compact('patrimonio', 'nameForos','escrituras', 'anexos'));
        } catch (\Exception $e) {
            // Retorna uma view de erro caso o patrimônio não seja encontrado
            return redirect()->route('app.patrimonios.index')->with('error', 'Patrimônio não encontrado.');
        }
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function search(Request $request)
    {
        $query = $request->input('query');

        $patrimonios = Patrimonio::where('codigo_rid', 'like', "%{$query}%")
            ->orWhere('descricao', 'like', "%{$query}%")
            ->orWhere('patrimonio', 'like', "%{$query}%")
            ->orWhere('logradouro', 'like', "%{$query}%")
            ->orWhere('bairro', 'like', "%{$query}%")
            ->orWhere('localidade', 'like', "%{$query}%")
            ->orWhere('uf', 'like', "%{$query}%")
            ->distinct()
            ->take(20) // Limita a 20 resultados
            ->orderBy('created_at', 'desc') // Ordena por data de criação, ajuste conforme necessário
            ->get(['id', 'codigo_rid', 'descricao', 'patrimonio', 'logradouro', 'bairro', 'localidade', 'uf']);

        return response()->json($patrimonios);
    }

    public function grafico()
    {
    $incompleteData = [70, 70, 80, 80, 75, 75, 75, 75, 75, 75, 94, 150]; // Substitua com dados reais da consulta
    $completeData = [55, 55, 60, 60, 55, 55, 60, 16, 20, 39, 75, 75]; // Substitua com dados reais da consulta

    return response()->json([
        'incomplete' => $incompleteData,
        'complete' => $completeData,
        'categories' => ['Jan','Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'] // Mude conforme necessário
    ]);
    }


    public function imoveis()
    {
        $nameForos = NamePatrimonio::all();
        $patrimonios = Patrimonio::all();

        return view('app.patrimonios.imoveis',
            [
                'nameForos' => $nameForos,
                'patrimonios' => $patrimonios,
            ]);
    }
}
