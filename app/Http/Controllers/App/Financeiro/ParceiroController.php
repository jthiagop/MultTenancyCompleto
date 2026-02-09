<?php

namespace App\Http\Controllers\App\Financeiro;

use App\Enums\NaturezaParceiro;
use App\Http\Controllers\Controller;
use App\Models\Parceiro;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ParceiroController extends Controller
{
    /**
     * Display the listing page with tabs.
     */
    public function index(Request $request)
    {
        $activeTab = $request->input('tab', 'todos');
        $validTabs = ['todos', 'fornecedores', 'clientes', 'inativos'];

        if (!in_array($activeTab, $validTabs)) {
            return redirect()->route('parceiros.index', ['tab' => 'todos']);
        }

        $companyId = session('active_company_id');
        if (!$companyId) {
            return redirect()->route('dashboard')->with('error', 'Selecione uma empresa.');
        }

        // Passar opções de natureza para o modal
        $naturezaOptions = NaturezaParceiro::options();

        return view('app.financeiro.parceiros.index', compact('activeTab', 'naturezaOptions'));
    }

    /**
     * DataTables server-side data endpoint.
     */
    public function data(Request $request)
    {
        $tab = $request->input('tab', 'todos');
        $search = $request->input('search.value', '');

        $query = Parceiro::forActiveCompany()->with('address');

        // Filtro por tab (natureza)
        switch ($tab) {
            case 'fornecedores':
                $query->natureza('fornecedor')->ativos();
                break;
            case 'clientes':
                $query->natureza('cliente')->ativos();
                break;
            case 'inativos':
                $query->inativos();
                break;
            default: // todos
                $query->ativos();
                break;
        }

        // Filtro por status (sub-filtro da stats tab)
        $status = $request->input('status', 'todos');
        if ($status && $status !== 'todos') {
            switch ($tab) {
                case 'todos':
                    if (in_array($status, ['fornecedor', 'fornecedores'])) {
                        $query->natureza('fornecedor');
                    } elseif (in_array($status, ['cliente', 'clientes'])) {
                        $query->natureza('cliente');
                    } elseif ($status === 'inativos') {
                        $query = Parceiro::forActiveCompany()->with('address')->inativos();
                    }
                    break;
                case 'fornecedores':
                    if ($status === 'com_cnpj') {
                        $query->whereNotNull('cnpj')->where('cnpj', '!=', '');
                    } elseif ($status === 'sem_cnpj') {
                        $query->where(function ($q) { $q->whereNull('cnpj')->orWhere('cnpj', ''); });
                    }
                    break;
                case 'clientes':
                    if ($status === 'com_cpf') {
                        $query->whereNotNull('cpf')->where('cpf', '!=', '');
                    } elseif ($status === 'sem_cpf') {
                        $query->where(function ($q) { $q->whereNull('cpf')->orWhere('cpf', ''); });
                    }
                    break;
                case 'inativos':
                    if (in_array($status, ['fornecedor', 'fornecedores'])) {
                        $query->natureza('fornecedor');
                    } elseif (in_array($status, ['cliente', 'clientes'])) {
                        $query->natureza('cliente');
                    }
                    break;
            }
        }

        // Busca textual
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nome', 'LIKE', "%{$search}%")
                  ->orWhere('nome_fantasia', 'LIKE', "%{$search}%")
                  ->orWhere('cnpj', 'LIKE', "%{$search}%")
                  ->orWhere('cpf', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('telefone', 'LIKE', "%{$search}%");
            });
        }

        // Contagens
        $totalRecords = Parceiro::forActiveCompany()->count();
        $filteredRecords = $query->count();

        // Ordenação
        $orderColumnIndex = (int) $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'asc');

        $sortableColumns = ['nome', 'natureza', 'cnpj', 'email', 'telefone', 'created_at'];
        $orderColumn = $sortableColumns[$orderColumnIndex] ?? 'nome';
        $query->orderBy($orderColumn, $orderDir);

        // Paginação
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 50);
        $parceiros = $query->skip($start)->take($length)->get();

        // Mapear dados para DataTables
        $data = $parceiros->map(function (Parceiro $p) {
            $documento = $p->documento;
            $tipoDoc = ($p->cnpj && strlen($p->cnpj) > 11) ? 'CNPJ' : 'CPF';
            
            $cidade = $p->address ? trim(($p->address->cidade ?? '') . '/' . ($p->address->uf ?? ''), '/') : '';

            return [
                'id' => $p->id,
                'hash_id' => $p->getRouteKey(),
                'nome' => $p->nome,
                'nome_fantasia' => $p->nome_fantasia,
                'tipo' => $p->tipo,
                'tipo_label' => $p->tipo_label,
                'natureza' => $p->natureza,
                'natureza_label' => $p->natureza_label,
                'natureza_badge_class' => $p->natureza_badge_class,
                'documento' => $documento,
                'tipo_documento' => $tipoDoc,
                'email' => $p->email,
                'telefone' => $p->telefone,
                'cidade' => $cidade,
                'active' => $p->active,
                'created_at' => $p->created_at?->format('d/m/Y'),
            ];
        });

        return response()->json([
            'draw' => (int) $request->input('draw', 1),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data->values(),
        ]);
    }

    /**
     * Stats endpoint for tab counts.
     * Retorna contagens conforme a aba ativa.
     */
    public function stats(Request $request)
    {
        $tab = $request->input('tab', 'todos');
        $base = Parceiro::forActiveCompany();

        $todos = (clone $base)->ativos()->count();
        $fornecedores = (clone $base)->ativos()->natureza('fornecedor')->count();
        $clientes = (clone $base)->ativos()->natureza('cliente')->count();
        $inativos = (clone $base)->inativos()->count();

        // Stats granulares por aba
        $stats = match ($tab) {
            'fornecedores' => [
                'todos' => $fornecedores,
                'com_cnpj' => (clone $base)->ativos()->natureza('fornecedor')->whereNotNull('cnpj')->where('cnpj', '!=', '')->count(),
                'sem_cnpj' => (clone $base)->ativos()->natureza('fornecedor')->where(function ($q) { $q->whereNull('cnpj')->orWhere('cnpj', ''); })->count(),
            ],
            'clientes' => [
                'todos' => $clientes,
                'com_cpf' => (clone $base)->ativos()->natureza('cliente')->whereNotNull('cpf')->where('cpf', '!=', '')->count(),
                'sem_cpf' => (clone $base)->ativos()->natureza('cliente')->where(function ($q) { $q->whereNull('cpf')->orWhere('cpf', ''); })->count(),
            ],
            'inativos' => [
                'todos' => $inativos,
                'fornecedores' => (clone $base)->inativos()->natureza('fornecedor')->count(),
                'clientes' => (clone $base)->inativos()->natureza('cliente')->count(),
            ],
            default => [ // todos
                'todos' => $todos,
                'fornecedores' => $fornecedores,
                'clientes' => $clientes,
                'inativos' => $inativos,
            ],
        };

        return response()->json($stats);
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
            'tipo' => 'nullable|in:pj,pf,ambos',
            'natureza' => 'nullable|string|max:50',
            'cnpj' => 'nullable|string|max:18',
            'cpf' => 'nullable|string|max:14',
            'telefone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'observacoes' => 'nullable|string|max:1000',
            // Address fields
            'cep' => 'nullable|string|max:10',
            'address1' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:20',
            'bairro' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:2',
        ]);

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
                $address = Address::create([
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
            $cnpj = $validated['cnpj'] ?? null;
            $cpf = $validated['cpf'] ?? null;
            if ($cnpj) $cnpj = preg_replace('/\D/', '', $cnpj);
            if ($cpf) $cpf = preg_replace('/\D/', '', $cpf);

            // Tipo de pessoa: se não informado, deduzir dos documentos
            $tipo = $validated['tipo'] ?? 'pj';

            // Natureza: se não informada, default = fornecedor
            $natureza = $validated['natureza'] ?? 'fornecedor';

            $parceiro = Parceiro::create([
                'nome' => $finalNome,
                'nome_fantasia' => $validated['nome_fantasia'] ?? null,
                'tipo' => $tipo,
                'natureza' => $natureza,
                'cnpj' => $cnpj,
                'cpf' => $cpf,
                'telefone' => $validated['telefone'] ?? null,
                'email' => $validated['email'] ?? null,
                'observacoes' => $validated['observacoes'] ?? null,
                'company_id' => $activeCompanyId,
                'address_id' => $address?->id,
                'created_by' => Auth::id(),
                'created_by_name' => Auth::user()->name ?? null,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Parceiro cadastrado com sucesso!',
                'data' => [
                    'id' => $parceiro->id,
                    'nome' => $parceiro->nome,
                    'natureza' => $natureza,
                ],
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
     * Update the specified resource.
     */
    public function update(Request $request, Parceiro $parceiro)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'nome_fantasia' => 'nullable|string|max:255',
            'tipo' => 'required|in:pj,pf,ambos',
            'natureza' => 'nullable|string|max:50',
            'cnpj' => 'nullable|string|max:18',
            'cpf' => 'nullable|string|max:14',
            'telefone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'observacoes' => 'nullable|string|max:1000',
        ]);

        if ($validated['cnpj'] ?? null) $validated['cnpj'] = preg_replace('/\D/', '', $validated['cnpj']);
        if ($validated['cpf'] ?? null) $validated['cpf'] = preg_replace('/\D/', '', $validated['cpf']);

        $validated['updated_by'] = Auth::id();
        $validated['updated_by_name'] = Auth::user()->name ?? null;

        $parceiro->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Parceiro atualizado com sucesso!',
        ]);
    }

    /**
     * Toggle active status.
     */
    public function toggleActive(Parceiro $parceiro)
    {
        $parceiro->update([
            'active' => !$parceiro->active,
            'updated_by' => Auth::id(),
            'updated_by_name' => Auth::user()->name ?? null,
        ]);

        $status = $parceiro->active ? 'ativado' : 'desativado';

        return response()->json([
            'success' => true,
            'message' => "Parceiro {$status} com sucesso!",
            'active' => $parceiro->active,
        ]);
    }

    /**
     * Remove the specified resource from storage (soft delete).
     */
    public function destroy(Parceiro $parceiro)
    {
        $parceiro->delete();

        return response()->json([
            'success' => true,
            'message' => 'Parceiro excluído com sucesso!',
        ]);
    }
}
