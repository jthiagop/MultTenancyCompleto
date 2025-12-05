<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Banco;
use App\Models\Company;
use App\Models\HorarioMissa;
use App\Models\Movimentacao;
use App\Models\User;
use Carbon\Carbon;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
/**
     * Mostra o formulário para editar a empresa ATIVA NA SESSÃO.
     */
    public function edit() // Removido o parâmetro $id
    {
        // 1. Pega o ID da empresa ativa na sessão.
        $activeCompanyId = session('active_company_id');

        // 2. Garante que uma empresa foi selecionada.
        if (!$activeCompanyId) {
            return redirect()->route('dashboard')->with('error', 'Por favor, selecione uma empresa primeiro.');
        }

        // 3. Busca a empresa e seus dados, garantindo que o usuário tem acesso a ela.
        $company = Auth::user()->companies()
                              ->with(['addresses', 'horariosMissas']) // Busca o endereço e horários de missas relacionados
                              ->findOrFail($activeCompanyId);

        return view('app.company.edit', ['company' => $company]);
    }

    /**
     * Atualiza os dados da empresa ATIVA NA SESSÃO.
     */
    public function update(Request $request) // Removido o parâmetro $id
    {
        // 1. Pega o ID da empresa ativa na sessão para garantir que estamos atualizando a correta.
        $activeCompanyId = session('active_company_id');

        if (!$activeCompanyId) {
            abort(403, 'Nenhuma empresa selecionada para atualização.');
        }

        // 2. Busca a empresa para garantir que o usuário tem permissão.
        $company = Auth::user()->companies()->findOrFail($activeCompanyId);

        // 3. Verificar se é apenas atualização de horários de missas
        $isOnlyHorariosMissas = $request->has('horarios_missas') && !$request->has('name');

        if (!$isOnlyHorariosMissas) {
            // A sua lógica de validação e atualização continua praticamente a mesma.
            //    (O código de validação que você já tem está ótimo e não precisa mudar)
            $validator = Validator::make($request->all(), [
                // ... suas regras de validação aqui ...
                'name' => ['required', 'string', 'max:255'],
                'cnpj' => ['required', 'string', 'size:18'],
                'data_fundacao' => ['nullable', 'date_format:d/m/Y'],
                'data_cnpj' => ['nullable', 'date_format:d/m/Y'],
                'email' => ['nullable', 'email', Rule::unique('companies')->ignore($company->id)],
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $validatedData = $validator->validated();

            // Conversão de datas
            foreach (['data_fundacao', 'data_cnpj'] as $dateField) {
                if (!empty($validatedData[$dateField])) {
                    $validatedData[$dateField] = Carbon::createFromFormat('d/m/Y', $validatedData[$dateField])->format('Y-m-d');
                }
            }

            // Preenche e atualiza a empresa
            $company->fill($validatedData);

            // Lógica do Avatar (seu código está ótimo)
            if ($request->hasFile('avatar')) {
                if ($company->avatar) {
                    Storage::disk('public')->delete($company->avatar);
                }
                $company->avatar = $request->file('avatar')->store('brasao', 'public');
            } elseif ($request->input('avatar_remove') === '1') {
                if ($company->avatar) {
                    Storage::disk('public')->delete($company->avatar);
                }
                $company->avatar = null;
            }

            $company->save();

            // Atualiza o endereço
            Address::updateOrCreate(
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
        }

        // Processar horários de missas
        if ($request->has('horarios_missas')) {
            // Deletar todos os horários existentes
            HorarioMissa::where('company_id', $company->id)->delete();

            // Criar novos horários
            foreach ($request->horarios_missas as $item) {
                if (!empty($item['dia_semana']) && !empty($item['horario'])) {
                    // Converter o horário para o formato correto (H:i:s)
                    $horario = $item['horario'];
                    // Se vier apenas H:i, adicionar :00 para os segundos
                    if (strlen($horario) === 5 && substr_count($horario, ':') === 1) {
                        $horario .= ':00';
                    }
                    
                    HorarioMissa::create([
                        'company_id' => $company->id,
                        'dia_semana' => $item['dia_semana'],
                        'horario' => $horario,
                    ]);
                }
            }
        } elseif (!$isOnlyHorariosMissas) {
            // Se não há horários_missas no request e não é apenas atualização de horários, não fazer nada
            // (mantém os horários existentes)
        }

        return redirect()->back()->with('success', 'Informações da empresa atualizadas com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
