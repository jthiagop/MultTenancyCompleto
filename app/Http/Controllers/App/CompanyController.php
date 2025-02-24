<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Banco;
use App\Models\Company;
use App\Models\Movimentacao;
use App\Models\User;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;


class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        $companyes = Company::all();
        return view('app.company.index', ['companyes' => $companyes, 'users' => $users]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        // Obtendo o nome do banco de dados
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'cnpj' => 'required|max:20|unique:companies,cnpj',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Exemplo de regras para o campo avatar
        ]);

        // Processar e salvar o avatar
        if ($request->hasFile('avatar')) {
            // Obtém o arquivo de avatar do request
            $avatar = $request->file('avatar');

            // Gera um nome único para o arquivo de avatar
            $avatarName = time() . '_' . $avatar->getClientOriginalName();

            // Salva o arquivo na pasta 'perfis' dentro da pasta de armazenamento (storage/app/public)
            $avatarPath = Storage::put('perfis', $request->file('avatar'));

            // Salva o nome do arquivo na coluna 'avatar' do usuário no banco de dados
            $validatedData['avatar'] = $avatarPath;
        }

        $user = Company::create($validatedData);

        return redirect()->route('company.index');
    }

    /**
     * Display the specified resource.
     */
    public function show($companyId, Request $request)
    {
        // Caminho para a pasta com os SVGs
    $svgPath = public_path('assets/media/svg/bancos');

    // Filtra todos os arquivos .svg do diretório
    $svgFiles = File::files($svgPath);

    // Monta um array com 'nome' e 'caminho'
    $listaBancos = [];
    foreach ($svgFiles as $file) {
        // Ex: bradesco.svg => bradesco
        $filename = pathinfo($file->getFilename(), PATHINFO_FILENAME);
        $listaBancos[] = [
            'slug'   => $filename,
            'label'  => ucfirst($filename), // ou algo custom
            // Ex: /assets/media/svg/bancos/bradesco.svg
            'icon'   => asset("assets/media/svg/bancos/{$file->getFilename()}"),
        ];
    }

        // Busca a empresa pelo ID
        $company = Company::findOrFail($companyId);

        // Busca as Entidades Financeiras relacionadas à empresa, com as movimentações
        $entidades = $company->entidadesFinanceiras()->with('movimentacoes')->get();

        // Carrega a empresa específica com suas entidades financeiras
        $company = Company::with('entidadesFinanceiras')->findOrFail($companyId);

        // Soma os saldos atuais de todas as entidades financeiras da empresa
        $totalSaldoAtual = $company->entidadesFinanceiras->sum('saldo_atual');

        // Calcula a receita e despesa do mês para a empresa
        $receitaMes = Movimentacao::getReceitaMes($companyId);
        $despesasMes = Movimentacao::getDespesasMes($companyId);

        // Exibi o resultado do mês
        $saldosBanco = Movimentacao::getSaldoBancoPorMesAno($companyId);
        $saldosCaixa = Movimentacao::getSaldoCaixaPorMesAno($companyId);

        // Inicializar arrays para os anos e meses
        $saldosBancoAnuais = [];
        $saldosCaixaAnuais = [];

        // Preencher os arrays com os valores do banco
        foreach ($saldosBanco as $saldo) {
            $ano = $saldo->ano;
            $mes = $saldo->mes;

            // Certifique-se de que o ano existe no array
            if (!isset($saldosBancoAnuais[$ano])) {
                $saldosBancoAnuais[$ano] = array_fill(1, 12, 0); // Inicializar meses de 1 a 12
            }

            // Atribuir o saldo ao mês correspondente
            $saldosBancoAnuais[$ano][$mes] = $saldo->saldo_banco;
        }

        // Preencher os arrays com os valores do caixa
        foreach ($saldosCaixa as $saldo) {
            $ano = $saldo->ano;
            $mes = $saldo->mes;

            // Certifique-se de que o ano existe no array
            if (!isset($saldosCaixaAnuais[$ano])) {
                $saldosCaixaAnuais[$ano] = array_fill(1, 12, 0); // Inicializar meses de 1 a 12
            }

            // Atribuir o saldo ao mês correspondente
            $saldosCaixaAnuais[$ano][$mes] = $saldo->saldo_caixa;
        }

        $areaChartData = [
            'banco' => $saldosBancoAnuais,
            'caixa' => $saldosCaixaAnuais,
        ];

        // Obtém os usuários associados à empresa
        $users = User::where('company_id', $companyId)->get();
        // Contar o total de usuários dessa empresa
        $totalUsers = $users->count();

        $roleColors = Company::getRoleColors();

        // Carrega a empresa com os usuários associados
        $companyShow = Company::with('users')->findOrFail($companyId);

        // Obtém o parâmetro da aba ativa ou define como 'overview' por padrão
        $activeTab = $request->input('tab', 'overview');

        // Retorna a view com a empresa e os dados relevantes
        return view('app.company.show', [
            'entidades' => $entidades,
            'companyShow' => $companyShow,
            'activeTab' => $activeTab,
            'users' => $users,
            'totalSaldoAtual' => $totalSaldoAtual,
            'receitaMes' => $receitaMes,
            'despesasMes' => $despesasMes,
            'roleColors' => $roleColors,
            'totalUsers' => $totalUsers,
            'areaChartData' => $areaChartData, // Dados do gráfico
            'listaBancos' => $listaBancos,


        ]);
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $company = Company::with('addresses')->findOrFail($id); // Busca a empresa e o endereço relacionado
        return view('app.company.edit', ['company' => $company]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validação dos dados
        $validator = Validator::make($request->all(), [
            // Dados básicos
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-ZÀ-ÿ\s\-]+$/', // Apenas letras, espaços e hífens
            ],
            'cnpj' => [
                'required',
                'string',
                'size:18',
                'regex:/^\d{2}\.\d{3}\.\d{3}\/\d{4}-\d{2}$/', // Formato padrão de CNPJ
            ],
            'email' => [
                'nullable',
                'email',
                'max:255',
            ],
            'details' => [
                'nullable',
                'string',
                'max:500',
            ],

            // Datas
            'data_fundacao' => [
                'nullable',
                'date_format:d/m/Y', // Valida o formato brasileiro (dd/mm/aaaa)
            ],
            'data_cnpj' => [
                'nullable',
                'date_format:d/m/Y', // Valida o formato brasileiro (dd/mm/aaaa)
            ],

            // Status
            'status' => [
                'nullable',
                Rule::in(['active', 'inactive']), // Somente valores permitidos
            ],

            // Endereço
            'cep' => [
                'nullable',
                'string',
                'size:9',
                'regex:/^\d{5}-\d{3}$/', // Valida formato do CEP (#####-###)
            ],
            'logradouro' => [
                'nullable',
                'string',
                'max:255',
            ],
            'numero' => [
                'nullable',
                'string',
                'max:10',
            ],
            'bairro' => [
                'nullable',
                'string',
                'max:255',
            ],
            'complemento' => [
                'nullable',
                'string',
                'max:255',
            ],
            'localidade' => [
                'nullable',
                'string',
                'max:255',
            ],
            'uf' => [
                'nullable',
                Rule::in(['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO']),
            ],

            // Avatar
            'avatar' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg',
                'max:2048', // Tamanho máximo de 2MB
            ],
        ]);

        // Verifica se há erros de validação
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Dados validados
        $validatedData = $validator->validated();

        // Converte as datas para o formato padrão do MySQL
        foreach (['data_fundacao', 'data_cnpj'] as $dateField) {
            if (!empty($validatedData[$dateField])) {
                $validatedData[$dateField] = \Carbon\Carbon::createFromFormat('d/m/Y', $validatedData[$dateField])->format('Y-m-d');
            }
        }

        // Atualiza os dados principais da empresa
        $company = Company::findOrFail($id);

        // Usando $validatedData para garantir que as datas convertidas sejam salvas
        $company->fill([
            'name' => $validatedData['name'] ?? null,
            'data_fundacao' => $validatedData['data_fundacao'] ?? null,
            'data_cnpj' => $validatedData['data_cnpj'] ?? null,
            'cnpj' => $validatedData['cnpj'] ?? null,
            'email' => $validatedData['email'] ?? null,
            'details' => $validatedData['details'] ?? null,
            'status' => $validatedData['status'] ?? null,
        ]);

        // Atualiza o avatar da empresa
        if ($request->hasFile('avatar')) {
            // Remove o avatar anterior, se houver
            if ($company->avatar) {
                Storage::disk('public')->delete($company->avatar);
            }
            $company->avatar = $request->file('avatar')->store('brasao', 'public');
        } elseif ($request->input('avatar_remove') === '1') {
            // Remove o avatar se solicitado
            if ($company->avatar) {
                Storage::disk('public')->delete($company->avatar);
            }
            $company->avatar = null;
        }

        // Salva as alterações na empresa
        $company->save();

        // Atualiza o endereço da empresa
        $address = Address::updateOrCreate(
            ['company_id' => $company->id],
            [
                'cep' => $request->cep,
                'rua' => $request->logradouro,
                'numero' => $request->numero,
                'bairro' => $request->bairro,
                'complemento' => $request->complemento,
                'cidade' => $request->localidade,
                'uf' => $request->uf,
            ]
        );

        // Redireciona com mensagem de sucesso
        return redirect()->back()->with('success', 'Informações atualizadas com sucesso.');
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
