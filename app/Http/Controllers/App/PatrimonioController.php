<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Http\Requests\Patrimonio\ForoRequest;
use App\Models\Escritura;
use App\Models\NamePatrimonio;
use App\Models\Patrimonio;
use App\Models\PatrimonioAnexo;
use App\Models\User;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laracasts\Flash\Flash;
use Spatie\Browsershot\Browsershot;


class PatrimonioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $nameForos = NamePatrimonio::all();
        $patrimonios = Patrimonio::all();
        $escrituras = Escritura::all();

        $totalPatrimonios = Patrimonio::count(); // Conta a quantidade de registros


        return view(
            'app.patrimonios.index',
            [
                'nameForos' => $nameForos,
                'patrimonios' => $patrimonios,
                'escrituras' => $escrituras,
                'totalPatrimonios' => $totalPatrimonios, // Passa a contagem para a view
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $nameForos = NamePatrimonio::all();
        $totalPatrimonios = Patrimonio::count(); // Conta a quantidade de registros

        return view('app.patrimonios.create', [
            'nameForos' => $nameForos,
            'totalPatrimonios' => $totalPatrimonios, // Passa a contagem para a view
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    // Função auxiliar para formatar valores numéricos (pode ficar fora do store)
    private function formatNumber(?string $number): ?float
    {
        if (empty($number)) {
            return null;
        }
        // Remove o separador de milhares (ponto) e substitui vírgula por ponto decimal
        $cleaned_number = str_replace('.', '', $number);
        $cleaned_number = str_replace(',', '.', $cleaned_number);
        return (float) $cleaned_number;
    }


    public function store(ForoRequest $request) // Use o seu ForoRequest
    {
        $isAjax = $request->ajax() || $request->wantsJson();
        $saveAction = $request->input('save_action', 'submit'); // Pega a ação (default: submit)

        try {
            $patrimonio = null; // Inicializa para ter acesso fora da transação se precisar

            DB::transaction(function () use ($request, &$patrimonio) { // Passa $patrimonio por referência
                $validatedData = $request->validated();

                // Conversão de datas
                $validatedData['data'] = Carbon::createFromFormat('d/m/Y', $validatedData['data'])->format('Y-m-d');
                // Removido 'aquisicao' pois não está no formulário fornecido
                // if (!empty($validatedData['aquisicao'])) {
                //     $validatedData['aquisicao'] = Carbon::createFromFormat('d/m/Y', $validatedData['aquisicao'])->format('Y-m-d');
                // }

                // Criando o patrimônio
                $patrimonio = new Patrimonio();
                // Use 'fillable' no Model Patrimonio para os campos que podem ser preenchidos massivamente
                $patrimonio->fill($validatedData);
                // Ajuste os campos que não vêm diretamente do request ou não estão no fillable
                $patrimonio->company_id = User::getCompany()->company_id; // Verifique se User::getCompany() existe e funciona como esperado
                $patrimonio->created_by = Auth::id();
                $patrimonio->updated_by = Auth::id();


                // Campos do endereço (CEP, Bairro, etc.) devem estar no fillable de Patrimonio ou serem atribuídos manualmente
                $patrimonio->cep = $validatedData['cep'] ?? null;
                $patrimonio->bairro = $validatedData['bairro'] ?? null;
                $patrimonio->logradouro = $validatedData['logradouro'] ?? null;
                $patrimonio->localidade = $validatedData['localidade'] ?? null;
                $patrimonio->uf = $validatedData['uf'] ?? null;
                $patrimonio->complemento = $validatedData['complemento'] ?? null;
                // Campos Livro, Folha, Registro

                $patrimonio->livro = $validatedData['livro'] ?? null;
                $patrimonio->folha = $validatedData['folha'] ?? null;
                $patrimonio->registro = $validatedData['registro'] ?? null;

                // Adicione o patrimonios_id (vindo do select) se o nome do campo for 'patrimonio'

                $patrimonio->save(); // Salva para obter o ID

                // Gerando e salvando o código RID
                // Certifique-se que numIbge e numForo estão sendo enviados corretamente (estão como hidden no form)
                if (!empty($validatedData['numIbge']) && !empty($validatedData['numForo'])) {
                    $patrimonio->codigo_rid = $this->gerarRID($validatedData['numIbge'], $validatedData['numForo'], $patrimonio->id);
                    $patrimonio->save(); // Salva novamente com o RID
                } else {
                    // Lidar com caso onde numIbge ou numForo estão vazios, se necessário
                    \Log::warning('numIbge ou numForo não encontrados ao tentar gerar RID para patrimonio ID: ' . $patrimonio->id);
                }


                // ----- Criando a Escritura (Removido pois campos não estão no form) -----
                // Se você precisar salvar dados de escritura, adicione os campos
                // correspondentes ao formulário e descomente/adapte esta seção.
                /*
            $escrituraData = collect($validatedData)->only([
                'outorgante', 'matricula', 'aquisicao', 'outorgado', 'valor',
                'area_total', 'area_privativa', 'informacoes', 'outorgante_telefone',
                'outorgante_email', 'outorgado_telefone', 'outorgado_email',
            ])->filter()->toArray(); // filter() remove nulos/vazios se necessário

            if (!empty($escrituraData)) {
                 // Convertendo valores numéricos
                $escrituraData['valor'] = $this->formatNumber($escrituraData['valor'] ?? null);
                $escrituraData['area_total'] = $this->formatNumber($escrituraData['area_total'] ?? null);
                $escrituraData['area_privativa'] = $this->formatNumber($escrituraData['area_privativa'] ?? null);

                $escritura = new Escritura();
                $escritura->fill($escrituraData);
                $escritura->patrimonio_id = $patrimonio->id;
                $escritura->created_by = Auth::id();
                $escritura->updated_by = Auth::id();
                $escritura->save();
            }
            */
                // ----- Fim da Seção Escritura -----

            }); // Fim da transação

            // Resposta baseada no tipo de request
            if ($isAjax) {
                return response()->json([
                    'success' => true,
                    'message' => 'Registro de localização salvo com sucesso!',
                    // 'patrimonio_id' => $patrimonio->id // Opcional: retornar ID se precisar no frontend
                ]);
            } else {
                session()->flash('success', 'Registro de localização salvo com sucesso!');
                return redirect()->back(); // Ou para outra rota, como a de visualização
            }
        } catch (Exception $e) {
            // Log do erro é importante
            \Log::error("Erro ao salvar localização do patrimônio: " . $e->getMessage() . " Stack: " . $e->getTraceAsString());

            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao salvar o registro: ' . $e->getMessage() // Seja cauteloso ao expor mensagens de erro detalhadas
                    // 'message' => 'Ocorreu um erro inesperado ao salvar. Tente novamente.' // Mensagem mais genérica
                ], 500); // Código 500 para erro interno do servidor
            } else {
                session()->flash('error', 'Erro ao salvar o registro: Verifique os dados e tente novamente.'); // Mensagem mais genérica para o usuário
                // session()->flash('error_details', $e->getMessage()); // Para debug, opcional
                return redirect()->back()->withInput(); // Volta com os dados preenchidos
            }
        }
    }

    /**
     * Gera e exibe um relatório em PDF dos patrimônios.
     */
    public function imprimirPDF(Request $request)
    {
        // INÍCIO - LÓGICA DE FILTRO (IDÊNTICA AO MÉTODO ACIMA)
        $query = Patrimonio::query();
        if ($request->filled('filter_field') && $request->filled('filter_value')) {
            $field = $request->input('filter_field');
            $condition = $request->input('filter_condition');
            $value = $request->input('filter_value');

            $allowedFields = ['codigo_rid', 'descricao', 'patrimonio', 'localidade', 'bairro'];
            if (in_array($field, $allowedFields)) {
                if ($condition == 'contains') {
                    $query->where($field, 'LIKE', '%' . $value . '%');
                } elseif ($condition == 'equals') {
                    $query->where($field, $value);
                }
            }
        }
        // FIM - LÓGICA DE FILTRO

        // Para o PDF, pegamos TODOS os resultados do filtro, sem paginar
        $patrimonios = $query->get();

        // Carrega a empresa do usuário logado E o seu endereço relacionado
        $company = Auth::user()->companies()->with('addresses')->first();

        // Converte o logo para Base64 para embutir no PDF
        $companyLogo = $this->logoToBase64($company);

        $totalRegistros = $patrimonios->count();

        $company = Auth::user()->companies()->first();

        $html = view('app.patrimonios.pdf', [
            'patrimonios'    => $patrimonios,
            'totalRegistros' => $totalRegistros,
            'company'        => $company,
            'companyLogo'    => $companyLogo, // <-- Nova variável

        ])->render();

        $pdf = Browsershot::html($html)
            ->format('A4')
            ->showBackground()
            ->margins(10, 10, 10, 10)
            ->pdf();

        return response($pdf, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="relatorio-de-patrimonios.pdf"',
        ]);
    }

    /**
     * Converte o caminho de uma imagem em uma string Base64.
     * (Função auxiliar que você já tinha no outro controller)
     */
    protected function logoToBase64($company): ?string
    {
        if (!$company || !$company->avatar) {
            // Caminho para uma imagem padrão caso a empresa não tenha logo
            $path = public_path('assets/media/png/perfil.svg');
        } else {
            $path = storage_path('app/public/' . $company->avatar);
        }

        if (!file_exists($path)) {
            return null; // Retorna nulo se o arquivo não for encontrado
        }

        return 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($path));
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
    // Em PatrimonioController.php

public function show(string $id)
{
    try {
        // Eager load escrituras and anexos with uploader to avoid N+1 queries
        $patrimonio = Patrimonio::with(['escrituras', 'anexos.uploader'])->findOrFail($id);

        // Fetch the most recent Escritura for the current deed value
        $escrituraAtual = $patrimonio->escrituras()->latest('created_at')->first();

        // Fetch the history of all Escrituras (already loaded via eager loading)
        $escrituras = $patrimonio->escrituras;

        // Fetch anexos (already loaded via eager loading)
        $anexos = $patrimonio->anexos;

        // Fetch NamePatrimonio only if needed (e.g., for a dropdown)
        $nameForos = NamePatrimonio::select('id', 'name')->get(); // Adjust fields as needed

        // Log warning if escrituraAtual is missing or valor is null
        if (!$escrituraAtual) {
            \Log::warning("Nenhuma escritura encontrada para patrimônio ID {$id}.");
        } elseif (is_null($escrituraAtual->valor)) {
            \Log::warning("Escritura atual para patrimônio ID {$id} não possui valor definido.");
        }

        return view('app.patrimonios.show', compact('patrimonio', 'nameForos', 'escrituras', 'anexos', 'escrituraAtual'));
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        \Log::error("Patrimônio com ID {$id} não encontrado.");
        return redirect()->route('patrimonios.index')->with('error', "Patrimônio com ID {$id} não encontrado.");
    } catch (\Exception $e) {
        \Log::error("Erro ao exibir patrimônio ID {$id}: {$e->getMessage()}");
        return redirect()->route('patrimonios.index')->with('error', 'Ocorreu um erro ao carregar o patrimônio. Tente novamente.');
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
    public function update(Request $request, $id)
    {
        // Validação dos dados da requisição
        $request->validate([
            'descricao'   => 'required|string|max:255',
            'cep'         => 'required|string|max:9',
            'logradouro'  => 'required|string|max:255',
            'bairro'      => 'required|string|max:255',
            'localidade'  => 'required|string|max:255',
            'uf'          => 'required|string|size:2',
            'complemento' => 'nullable|string|max:255',
            'folha' => 'nullable|string|max:255',
            'livro' => 'nullable|string|max:255',
            'registro' => 'nullable|string|max:255',
        ]);

        try {
            // Localiza o registro pelo ID
            $patrimonio = Patrimonio::findOrFail($id);

            // Atualiza os campos
            $patrimonio->descricao   = $request->descricao;
            $patrimonio->cep         = $request->cep;
            $patrimonio->logradouro  = $request->logradouro;
            $patrimonio->bairro      = $request->bairro;
            $patrimonio->localidade  = $request->localidade;
            $patrimonio->uf          = $request->uf;
            $patrimonio->complemento = $request->complemento;
            $patrimonio->folha = $request->folha;
            $patrimonio->livro = $request->livro;
            $patrimonio->registro = $request->registro;

            // Salva as alterações no banco de dados
            $patrimonio->save();

            // Configura a mensagem flash de sucesso com título e RID
            // Configura a mensagem flash de sucesso com título e RID
            flash()
                ->option('position', 'top-right')
                ->option('offset', ['x' => 0, 'y' => 80]) // Desloca 80px para baixo
                ->option('timeout', 4000)
                ->success('Registro com RID ' . $patrimonio->codigo_rid . ' foi atualizado com sucesso!', [
                    'title' => 'Atualização Bem-Sucedida'
                ]);

            // Redireciona de volta com a mensagem de sucesso
            return redirect()->back();
        } catch (Exception $e) {
            // Configura a mensagem flash de erro
            flash()
                ->option('position', 'top-right')
                ->option('offset', ['x' => 0, 'y' => 80])
                ->option('timeout', 4000)
                ->error('Erro', 'Erro ao atualizar o registro: ' . $e->getMessage());

            // Redireciona de volta com a mensagem de erro
            return redirect()->back();
        }
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
            'categories' => ['Jan', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'] // Mude conforme necessário
        ]);
    }


    public function filtrar(Request $request)
    {
        // Inicia a query builder
        $query = Patrimonio::query();
        $totalPatrimonios = Patrimonio::count(); // Conta a quantidade de registros

        // Aplica os filtros se eles existirem na requisição
        if ($request->filled('filter_field') && $request->filled('filter_value')) {
            $field = $request->input('filter_field');
            $condition = $request->input('filter_condition');
            $value = $request->input('filter_value');

            // Valida para evitar injeção de SQL em nomes de colunas
            $allowedFields = ['codigo_rid', 'descricao', 'patrimonio', 'localidade', 'bairro'];
            if (in_array($field, $allowedFields)) {
                if ($condition == 'contains') {
                    $query->where($field, 'LIKE', '%' . $value . '%');
                } elseif ($condition == 'equals') {
                    $query->where($field, $value);
                }
            }
        }

        // Pagina os resultados em vez de carregar todos de uma vez
        $patrimonios = $query->latest()->paginate(15);

        return view('app.patrimonios.filtrar', [
            'patrimonios' => $patrimonios,
            'totalPatrimonios' => $totalPatrimonios, // Passa a contagem para a view
        ]);
    }

    public function imoveis(Request $request)
    {
        $companyId = User::getCompany()->company_id ?? null;
        
        $query = \App\Models\Bem::where('tipo', 'imovel');
        
        if ($companyId) {
            $query->where('company_id', $companyId);
        }
        
        $bens = $query->with('imovel')->latest()->paginate(15);
        $totalBens = \App\Models\Bem::where('tipo', 'imovel')
            ->where('company_id', $companyId)
            ->count();

        return view('app.patrimonios.imoveis', [
            'bens' => $bens,
            'totalBens' => $totalBens,
        ]);
    }

    public function updateLocation(Request $request)
    {
        // Validação dos dados
        $data = $request->validate([
            'id'        => 'required|exists:patrimonios,id',
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);


        // Localiza o patrimônio e atualiza os campos
        $patrimonio = Patrimonio::findOrFail($data['id']);
        $patrimonio->latitude = $data['latitude'];
        $patrimonio->longitude = $data['longitude'];
        $patrimonio->save();

        return redirect()->back()->with('Localização atualizada com sucesso!');
    }

    public function bensMoveis(Request $request)
    {
        $companyId = User::getCompany()->company_id ?? null;
        
        $query = \App\Models\Bem::where('tipo', 'movel');
        
        if ($companyId) {
            $query->where('company_id', $companyId);
        }
        
        $bens = $query->with('bemMovel')->latest()->paginate(15);
        $totalBens = \App\Models\Bem::where('tipo', 'movel')
            ->where('company_id', $companyId)
            ->count();
        
        return view('app.patrimonios.bens-moveis', [
            'bens' => $bens,
            'totalBens' => $totalBens,
        ]);
    }

    public function veiculos(Request $request)
    {
        $companyId = User::getCompany()->company_id ?? null;
        
        $query = \App\Models\Bem::where('tipo', 'veiculo');
        
        if ($companyId) {
            $query->where('company_id', $companyId);
        }
        
        $bens = $query->with('veiculo')->latest()->paginate(15);
        $totalBens = \App\Models\Bem::where('tipo', 'veiculo')
            ->where('company_id', $companyId)
            ->count();
        
        return view('app.patrimonios.veiculos', [
            'bens' => $bens,
            'totalBens' => $totalBens,
        ]);
    }
}
