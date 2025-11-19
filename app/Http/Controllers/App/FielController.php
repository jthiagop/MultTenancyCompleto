<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Fiel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class FielController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtém o ID da empresa do usuário autenticado
        $companyId = session('active_company_id');

        // Busca apenas os fiéis que pertencem à empresa do usuário autenticado
        $fieis = Fiel::where('company_id', $companyId)->get();

        return view('app.cadastros.fieis.index', compact('fieis'));
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
        try {
            // Obter o ID da empresa do usuário autenticado
            $companyId = session('active_company_id');

            // Capturar os dados do formulário
            $data = $request->only([
                'nome_completo', 'cpf', 'rg', 'data_nascimento', 'sexo', 'estado_civil',
                'profissao', 'email', 'telefone', 'telefone_secundario', 'endereco',
                'bairro', 'cidade', 'uf', 'cep', 'status'
            ]);

            // Atribuir o ID da empresa
            $data['company_id'] = $companyId;

            // Tratar a data de nascimento (converter do formato "d/m/Y" para "Y-m-d")
            if (!empty($data['data_nascimento'])) {
                $data['data_nascimento'] = \Carbon\Carbon::createFromFormat('d/m/Y', $data['data_nascimento'])->format('Y-m-d');
            }
            // Tratar as notificações
            $data['notifications'] = json_encode($request->input('notifications', []));
            $data['updated_by'] = auth()->user()->id;
            $data['created_by'] = auth()->user()->id;
            $data['created_by_name'] = auth()->user()->name;

            // Verificar se um avatar foi enviado e fazer o upload
            if ($request->hasFile('avatar')) {
                $avatar = $request->file('avatar');
                $avatarPath = $avatar->store('avatars', 'public');
                $data['avatar'] = $avatarPath;
            }
            // Criar o registro usando preenchimento em massa
            $fiel = Fiel::create($data);

        // Redirecionar de volta com uma mensagem de sucesso
        return redirect()->route('fieis.index')
            ->with('success', 'Fiel cadastrado com sucesso!');

        } catch (\Exception $e) {
            // Em caso de erro, retorna uma resposta com Toast de erro
            return response()->json([
                'status' => 'error',
                'message' => 'Ocorreu um erro ao cadastrar o fiel: ' . $e->getMessage()
            ], 500);
        }
    }



    /**
     * Display the specified resource.
     */
    public function show(Fiel $fiel)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Fiel $fiel)
    {
        return view('app.cadastros.fieis.edit');

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Fiel $fiel)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Fiel $fiel)
    {
        //
    }
}
