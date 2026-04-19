<?php

namespace App\Http\Controllers\App\Cemiterio;

use App\Http\Controllers\Controller;
use App\Models\Cemiterio\Sepultura;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SepulturaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $companyId = User::getCompany()->company_id;

        $page    = max(1, (int) $request->query('page', 1));
        $perPage = min(100, max(1, (int) $request->query('per_page', 20)));
        $search  = trim($request->query('search', ''));
        $status  = $request->query('status', '');
        $sortBy  = $request->query('sort_by', 'codigo_tumulo');
        $sortDir = strtolower($request->query('sort_dir', 'asc'));

        $columnMap = [
            'codigo_tumulo' => 'codigo_sepultura',
            'localizacao'   => 'localizacao',
            'tipo'          => 'tipo',
            'status'        => 'status',
        ];
        $dbColumn = $columnMap[$sortBy] ?? 'codigo_sepultura';
        $sortDir  = in_array($sortDir, ['asc', 'desc']) ? $sortDir : 'asc';

        $query = Sepultura::where('company_id', $companyId)
            ->with('latestSepultado');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('codigo_sepultura', 'like', "%{$search}%")
                  ->orWhere('localizacao', 'like', "%{$search}%");
            });
        }

        if ($status !== '') {
            $query->where('status', $status);
        }

        $total = $query->count();

        $sepulturas = $query
            ->orderBy($dbColumn, $sortDir)
            ->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get();

        $data = $sepulturas->map(fn($s) => [
            'id'             => $s->id,
            'codigo_tumulo'  => $s->codigo_sepultura ?? '',
            'localizacao'    => $s->localizacao ?? '',
            'tipo'           => $s->tipo ?? '',
            'tamanho'        => $s->tamanho,
            'status'         => $s->status,
            'ocupante_atual' => $s->latestSepultado?->nome,
        ]);

        return response()->json([
            'success' => true,
            'data'    => $data,
            'total'   => $total,
        ]);
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
        $subsidiaryId = User::getCompany();

        // Validação dos dados
        $validatedData = $request->validate([
            'codigo_sepultura' => 'required|string|max:255',
            'localizacao' => 'nullable|string|max:255',
            'tipo' => 'required|string|max:255',
            'tamanho' => 'string', // Garantir que seja um número válido
            'data_aquisicao' => 'nullable', // Validação para o formato da data
            'status' => 'required|string|max:255',
        ]);


        try {
            // Obtenção do usuário autenticado usando Auth::user()
            $user = Auth::user();

            $validatedData['company_id'] = $subsidiaryId->company_id;
            $validatedData['tamanho'] = str_replace(',', '.', str_replace('.', '', $validatedData['tamanho']));

            // Criação da nova sepultura
            $sepultura = new Sepultura();
            $sepultura->company_id = $validatedData['company_id'];  // Usando o ID da empresa do usuário
            $sepultura->codigo_sepultura = $validatedData['codigo_sepultura'];
            $sepultura->localizacao = $validatedData['localizacao'];
            $sepultura->tipo = $validatedData['tipo'];
            $sepultura->tamanho = $validatedData['tamanho'];
            $sepultura->data_aquisicao =  $validatedData['data_aquisicao']; // Usando a data formatada
            $sepultura->status = $validatedData['status'];

            // Usando o Auth::user() para pegar as informações do usuário autenticado
            $sepultura->created_by = $user->id;
            $sepultura->created_by_name = $user->name;
            $sepultura->updated_by = $user->id;
            $sepultura->updated_by_name = $user->name;
            // Salvando a sepultura no banco de dados
            $sepultura->save();

            // Retornar uma resposta ou redirecionar
            return redirect()->back()->with('success', 'Sepultura cadastrada com sucesso!');
        } catch (Exception $e) {
            // Captura qualquer exceção e exibe a mensagem de erro
            return redirect()->back()->with('error', 'Erro ao cadastrar sepultura: ' . $e->getMessage());
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $companyId = User::getCompany()->company_id;
        $sepultura = Sepultura::where('company_id', $companyId)->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => [
                'id'               => $sepultura->id,
                'codigo_sepultura' => $sepultura->codigo_sepultura ?? '',
                'localizacao'      => $sepultura->localizacao ?? '',
                'tipo'             => $sepultura->tipo ?? '',
                'tamanho'          => $sepultura->tamanho ?? '',
                'data_aquisicao'   => $sepultura->data_aquisicao?->toDateString() ?? '',
                'status'           => $sepultura->status,
            ],
        ]);
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
        // Validar os dados recebidos
        $validatedData = $request->validate([
            'codigo_sepultura' => 'required|string|max:255',
            'localizacao' => 'required|string|max:255',
            'tipo' => 'required|string|max:255',
            'tamanho' => 'nullable|string',  // Exemplo de validação para um campo numérico
            'data_aquisicao' => 'required|date',  // Validação para data
            'status' => 'required|string',
        ]);


        try {
            // Encontrar a sepultura pelo ID
            $sepultura = Sepultura::findOrFail($id);

            // Capturar o usuário autenticado
            $user = Auth::user();
            // Atualizar os dados da sepultura
            $sepultura->codigo_sepultura = $validatedData['codigo_sepultura'];
            $sepultura->localizacao = $validatedData['localizacao'];
            $sepultura->tipo = $validatedData['tipo'];
            $sepultura->tamanho = $validatedData['tamanho'] ?? $sepultura->tamanho;  // Se não for enviado, mantém o valor antigo
            $sepultura->data_aquisicao = $validatedData['data_aquisicao'];
            $sepultura->status = $validatedData['status'];

            // Atualizar os campos de quem fez a alteração
            $sepultura->updated_by = $user->id;
            $sepultura->updated_by_name = $user->name;

            // Salvar as alterações no banco de dados
            $sepultura->save();

            // Retornar uma resposta ou redirecionar com sucesso
            return redirect()->back()->with('success', 'Sepultura atualizada com sucesso!');
        } catch (\Exception $e) {
            // Captura qualquer exceção e exibe a mensagem de erro
            return redirect()->back()->with('error', 'Erro ao atualizar sepultura: ' . $e->getMessage());
        }
    }

    public function storeJson(Request $request)
    {
        $companyId = User::getCompany()->company_id;

        $validator = Validator::make($request->all(), [
            'codigo_sepultura' => 'required|string|max:255',
            'localizacao'      => 'nullable|string|max:255',
            'tipo'             => 'nullable|string|max:255',
            'tamanho'          => 'nullable|string|max:100',
            'data_aquisicao'   => 'nullable|date',
            'status'           => 'required|in:Disponível,Ocupada,Reservada,Manutenção',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user = Auth::user();

        $sepultura = new Sepultura();
        $sepultura->company_id        = $companyId;
        $sepultura->codigo_sepultura  = $request->input('codigo_sepultura');
        $sepultura->localizacao       = $request->input('localizacao');
        $sepultura->tipo              = $request->input('tipo');
        $sepultura->tamanho           = $request->input('tamanho');
        $sepultura->data_aquisicao    = $request->input('data_aquisicao') ?: null;
        $sepultura->status            = $request->input('status');
        $sepultura->created_by        = $user->id;
        $sepultura->created_by_name   = $user->name;
        $sepultura->updated_by        = $user->id;
        $sepultura->updated_by_name   = $user->name;
        $sepultura->save();

        return response()->json([
            'success' => true,
            'message' => 'Túmulo cadastrado com sucesso!',
            'data'    => $sepultura,
        ], 201);
    }

    public function updateJson(Request $request, string $id)
    {
        $companyId = User::getCompany()->company_id;
        $sepultura = Sepultura::where('company_id', $companyId)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'codigo_sepultura' => 'required|string|max:255',
            'localizacao'      => 'nullable|string|max:255',
            'tipo'             => 'nullable|string|max:255',
            'tamanho'          => 'nullable|string|max:100',
            'data_aquisicao'   => 'nullable|date',
            'status'           => 'required|in:Disponível,Ocupada,Reservada,Manutenção',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user = Auth::user();

        $sepultura->codigo_sepultura = $request->input('codigo_sepultura');
        $sepultura->localizacao      = $request->input('localizacao');
        $sepultura->tipo             = $request->input('tipo');
        $sepultura->tamanho          = $request->input('tamanho');
        $sepultura->data_aquisicao   = $request->input('data_aquisicao') ?: null;
        $sepultura->status           = $request->input('status');
        $sepultura->updated_by       = $user->id;
        $sepultura->updated_by_name  = $user->name;
        $sepultura->save();

        return response()->json([
            'success' => true,
            'message' => 'Túmulo atualizado com sucesso!',
            'data'    => $sepultura,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function stats()
    {
        $companyId = User::getCompany()->company_id;

        $total      = Sepultura::where('company_id', $companyId)->count();
        $disponiveis = Sepultura::where('company_id', $companyId)->where('status', 'Disponível')->count();
        $ocupadas   = Sepultura::where('company_id', $companyId)->where('status', 'Ocupada')->count();

        $emAberto = \App\Models\Financeiro\TransacaoFinanceira::where('company_id', $companyId)
            ->whereNotNull('sepultura_id')
            ->whereIn('situacao', ['em_aberto', 'atrasado'])
            ->count();

        return response()->json([
            'success' => true,
            'data'    => [
                'total'      => $total,
                'disponiveis' => $disponiveis,
                'ocupadas'   => $ocupadas,
                'em_aberto'  => $emAberto,
            ],
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->input('query');

        $sepulturas = Sepultura::where('codigo_sepultura', 'like', "%{$query}%")
            ->orWhere('localizacao', 'like', "%{$query}%")
            ->orWhere('tipo', 'like', "%{$query}%")
            ->get();

        return response()->json($sepulturas);
    }
}
