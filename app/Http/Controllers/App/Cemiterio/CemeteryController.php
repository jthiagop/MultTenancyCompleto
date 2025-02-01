<?php

namespace App\Http\Controllers\App\Cemiterio;

use App\Http\Controllers\Controller;
use App\Models\Cemiterio\Sepultado;
use App\Models\Cemiterio\Sepultura;
use App\Models\ModulosAnexo;
use App\Models\User;
use Auth;
use Flasher;
use Illuminate\Http\Request;
use Storage;

class CemeteryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Obter o valor da aba ativa da URL, se presente
        $activeTab = $request->input('tab', 'overview'); // 'overview' é o padrão caso não haja o parâmetro 'tab'

        // Obter o ID da empresa do usuário autenticado (ou de outra fonte se necessário)
        $companyId = Auth::user()->company_id;  // Supondo que o modelo User tenha o relacionamento com company_id

        // Consultar as sepulturas relacionadas ao company_id
        $sepulturas = Sepultura::where('company_id', $companyId)->get();
        $sepultados = Sepultado::where('company_id', $companyId)->get();

        // Retornar a view com as sepulturas e a aba ativa
        return view('app.cemiterio.index', compact('activeTab', 'sepulturas', 'sepultados'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validação dos dados
        $validatedData = $request->validate([
            'sepultura_id' => [
                'required',
                'integer',
                'exists:sepulturas,id', // Verifica se o sepultura_id existe na tabela 'sepulturas'
                function ($attribute, $value, $fail) {
                    // Verifica se a sepultura está com o status "disponível"
                    $sepultura = Sepultura::find($value);
                    if ($sepultura && $sepultura->status != 'disponível') {
                        return $fail('A sepultura não está disponível.');
                    }
                },
            ],
            'nome' => 'required|string|max:255',
            'data_nascimento' => 'required|date',
            'data_falecimento' => 'required|date|after_or_equal:data_nascimento',
            'data_sepultamento' => 'required|date|after_or_equal:data_falecimento',
            'causa_mortis' => 'required|string|max:255',
            'livro_sepultamento' => 'nullable|string|max:20',
            'folha_sepultamento' => 'nullable|string|max:20',
            'numero_sepultamento' => 'nullable|string|max:20',
            'familia_responsavel' => 'nullable|string|max:255',
            'relacionamento' => 'nullable|string|max:100',
            'informacoes_atestado_obito' => 'nullable|string',
            'files' => 'nullable|array|max:10', // Permite no máximo 10 arquivos
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // Avatar (imagem)
            'avatar_remove' => 'nullable|boolean',
        ]);

        // Salvando os arquivos anexados (caso existam)
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                // Salva o arquivo no diretório 'anexos'
                $caminhoArquivo = $file->store('anexos');

                // Criação do anexo
                ModulosAnexo::create([
                    'anexavel_id' => $request->sepultura_id, // Exemplo: sepultura_id
                    'anexavel_type' => 'Sepultura', // Exemplo: tipo de módulo
                    'nome_arquivo' => $file->getClientOriginalName(),
                    'caminho_arquivo' => $caminhoArquivo,
                    'tipo_arquivo' => $file->getClientOriginalExtension(),
                    'extensao_arquivo' => $file->getClientOriginalExtension(),
                    'mime_type' => $file->getMimeType(),
                    'tamanho_arquivo' => $file->getSize(),
                    'descricao' => $request->descricao ?? null,
                    'uploaded_by' => Auth::id(),
                    'status' => 'ativo', // Por padrão, ativo
                    'data_upload' => now(),
                ]);
            }
        }

        $subsidiaryId = User::getCompany();

        $validatedData['company_id'] = $subsidiaryId->company_id;

        // Criando o sepultado
        $sepultado = new Sepultado();
        $sepultado->nome = $validatedData['nome'];
        $sepultado->sepultura_id = $validatedData['sepultura_id'];
        $sepultado->company_id = $validatedData['company_id'];
        $sepultado->data_nascimento = $validatedData['data_nascimento'];
        $sepultado->data_falecimento = $validatedData['data_falecimento'];
        $sepultado->data_sepultamento = $validatedData['data_sepultamento'];
        $sepultado->causa_mortis = $validatedData['causa_mortis'];
        $sepultado->livro_sepultamento = $validatedData['livro_sepultamento'];
        $sepultado->folha_sepultamento = $validatedData['folha_sepultamento'];
        $sepultado->numero_sepultamento = $validatedData['numero_sepultamento'];
        $sepultado->familia_responsavel = $validatedData['familia_responsavel'];
        $sepultado->relacionamento = $validatedData['relacionamento'];
        $sepultado->informacoes_atestado_obito = $validatedData['informacoes_atestado_obito'];

        // Verificar se um avatar foi enviado e fazer o upload
        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $avatarPath = $avatar->store('sepultado', 'public');
            $sepultado->avatar = $avatarPath;  // Atribui o caminho do avatar diretamente
        }

        $sepultado->save(); // Salva o sepultado no banco de dados, incluindo o avatar se enviado


        // Retorna a resposta JSON
        return redirect()->back()->with('success', 'Sepultado cadastrada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    // SepulturasController.php
    public function edit(Request $request, $id)
    {
        // Obter o valor da aba ativa da URL, se presente
        $activeTab = $request->input('tab', 'overview'); // 'overview' é o padrão caso não haja o parâmetro 'tab'
        $sepulturaEdit = Sepultura::findOrFail($id);

        // Obter o ID da empresa do usuário autenticado (ou de outra fonte se necessário)
        $companyId = Auth::user()->company_id;  // Supondo que o modelo User tenha o relacionamento com company_id

        // Consultar as sepulturas relacionadas ao company_id
        $sepulturas = Sepultura::where('company_id', $companyId)->get();

        return view('app.cemiterio.edit', compact('activeTab', 'sepulturaEdit', 'sepulturas'));
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
    public function destroy($id)
    {
        try {
            // Encontrar a sepultura pelo ID e excluí-la usando SoftDeletes
            $sepultura = Sepultura::findOrFail($id);

            // Capturar o usuário autenticado
            $user = Auth::user(); // Usuário autenticado

            // Atualizar os campos updated_by e updated_by_name
            $sepultura->updated_by = $user->id; // ID do usuário que fez a exclusão
            $sepultura->updated_by_name = $user->name; // Nome do usuário que fez a exclusão

            // Excluir a sepultura com SoftDeletes

            // Excluir a sepultura com SoftDeletes
            $sepultura->delete();

            // Redirecionar de volta com sucesso
            return redirect()->route('cemiterio.index')->with('success', 'Sepultura excluída com sucesso!');
        } catch (\Exception $e) {
            // Se algo der errado, redirecionar com erro
            return redirect()->route('cemiterio.index')->with('error', 'Erro ao excluir sepultura: ' . $e->getMessage());
        }
    }
}
