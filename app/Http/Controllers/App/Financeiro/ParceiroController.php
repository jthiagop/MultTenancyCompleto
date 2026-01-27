<?php

namespace App\Http\Controllers\App\Financeiro;

use App\Http\Controllers\Controller;
use App\Models\Parceiro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ParceiroController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        $validated = $request->validate([
            'nome' => 'nullable|string|max:255',
            'nome_completo' => 'nullable|string|max:255',
            'nome_fantasia' => 'nullable|string|max:255',
            'cnpj' => 'nullable|string|max:18',
            'cpf' => 'nullable|string|max:14',
            'telefone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            // Address fields
            'cep' => 'nullable|string|max:10',
            'address1' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:20',
            'bairro' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:2', // UF
        ]);

        // Map nome_completo or nome_fantasia to nome if nome is empty
        $finalNome = $validated['nome'] ?? $validated['nome_completo'] ?? $validated['nome_fantasia'] ?? null;
        
        if (!$finalNome) {
             return response()->json([
                'success' => false,
                'message' => 'O campo Nome é obrigatório.',
                'errors' => ['nome' => ['O campo Nome é obrigatório.']]
            ], 422);
        }

        $activeCompanyId = session('active_company_id');

        try {
            DB::beginTransaction();

            // Create Address if any address field is present
            $address = null;
            if (!empty($validated['cep']) || !empty($validated['address1']) || !empty($validated['city'])) {
                $address = \App\Models\Address::create([
                    'company_id' => $activeCompanyId,
                    'cep' => $validated['cep'] ?? null,
                    'rua' => $validated['address1'] ?? null,
                    'numero' => $validated['numero'] ?? null,
                    'bairro' => $validated['bairro'] ?? null,
                    'cidade' => $validated['city'] ?? null,
                    'uf' => $validated['country'] ?? null,
                ]);
            }

            // Clean CNPJ/CPF
            $taxId = $validated['cnpj'] ?? $validated['cpf'] ?? null;
            if ($taxId) {
                $taxId = preg_replace('/\D/', '', $taxId);
            }

            $parceiro = Parceiro::create([
                'nome' => $finalNome,
                'nome_fantasia' => $validated['nome_fantasia'] ?? null,
                'cnpj' => $taxId,
                'telefone' => $validated['telefone'] ?? null,
                'email' => $validated['email'] ?? null,
                'company_id' => $activeCompanyId,
                'address_id' => $address ? $address->id : null,
                'created_by' => Auth::id(),
                'created_by_name' => Auth::user()->name ?? null,
            ]);

            DB::commit();

            // Determine type based on request (if tipo field is sent) or default to fornecedor
            $tipo = $request->input('tipo', 'fornecedor'); // pode ser 'fornecedor' ou 'cliente'
            
            return response()->json([
                'success' => true,
                'message' => 'Parceiro cadastrado com sucesso!',
                'data' => [
                    'id' => $parceiro->id,
                    'nome' => $parceiro->nome,
                    'type' => $tipo, // 'fornecedor' ou 'cliente'
                ],
                // Keep parceiro for backward compatibility
                'parceiro' => [
                    'id' => $parceiro->id,
                    'nome' => $parceiro->nome,
                    'nome_fantasia' => $parceiro->nome_fantasia,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao cadastrar parceiro: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Parceiro $parceiro)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Parceiro $parceiro)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Parceiro $parceiro)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Parceiro $parceiro)
    {
        //
    }
}
