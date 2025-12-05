<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Bem;
use App\Models\Veiculo;
use App\Models\Imovel;
use App\Models\BemMovel;
use App\Models\User;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $companyId = User::getCompany()->company_id ?? null;
        
        $query = Bem::query();
        
        if ($companyId) {
            $query->where('company_id', $companyId);
        }
        
        // Filtro por tipo
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }
        
        $bens = $query->with(['veiculo', 'imovel', 'bemMovel'])->latest()->paginate(15);
        
        return view('app.bens.index', [
            'bens' => $bens,
            'totalBens' => Bem::where('company_id', $companyId)->count(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tipo = request('tipo', 'movel'); // Default para móvel
        
        return view('app.bens.create', [
            'tipo' => $tipo,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $isAjax = $request->ajax() || $request->wantsJson() || $request->expectsJson();
        
        $validator = $this->validateBem($request);
        
        if ($validator->fails()) {
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro de validação',
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }
        
        try {
            DB::transaction(function () use ($request) {
                $companyId = User::getCompany()->company_id ?? null;
                
                // Criar o Bem principal
                $bem = new Bem();
                $bem->company_id = $companyId;
                $bem->created_by = Auth::id();
                $bem->descricao = $request->descricao;
                $bem->tipo = $request->tipo;
                $bem->centro_custo = $request->centro_custo;
                $bem->valor = $this->formatNumber($request->valor);
                $bem->data_aquisicao = Carbon::createFromFormat('d/m/Y', $request->data_aquisicao)->format('Y-m-d');
                $bem->numero_documento = $request->numero_documento;
                $bem->fornecedor = $request->fornecedor;
                $bem->depreciar = $request->has('depreciar') || $request->depreciar == '1' || $request->depreciar == 1;
                $bem->estado_bem = $request->estado_bem;
                
                // Dados adicionais em JSON
                if ($request->has('dados_adicionais')) {
                    $bem->dados_adicionais = json_decode($request->dados_adicionais, true);
                }
                
                $bem->save();
                
                // Criar registro específico baseado no tipo
                switch ($request->tipo) {
                    case 'veiculo':
                        $this->createVeiculo($bem->id, $request);
                        break;
                    case 'imovel':
                        $this->createImovel($bem->id, $request);
                        break;
                    case 'movel':
                        $this->createBemMovel($bem->id, $request);
                        break;
                }
            });
            
            if ($isAjax) {
                return response()->json([
                    'success' => true,
                    'message' => 'Bem cadastrado com sucesso!'
                ], 200);
            }
            
            return redirect()->route('bem.index')->with('success', 'Bem cadastrado com sucesso!');
            
        } catch (Exception $e) {
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao cadastrar bem: ' . $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Erro ao cadastrar bem: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $bem = Bem::with(['veiculo', 'imovel', 'bemMovel', 'company', 'creator'])
            ->findOrFail($id);
        
        return view('app.bens.show', [
            'bem' => $bem,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $bem = Bem::with(['veiculo', 'imovel', 'bemMovel'])->findOrFail($id);
        
        return view('app.bens.edit', [
            'bem' => $bem,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = $this->validateBem($request, $id);
        
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        try {
            DB::transaction(function () use ($request, $id) {
                $bem = Bem::findOrFail($id);
                
                // Atualizar o Bem principal
                $bem->descricao = $request->descricao;
                $bem->tipo = $request->tipo;
                $bem->centro_custo = $request->centro_custo;
                $bem->valor = $this->formatNumber($request->valor);
                $bem->data_aquisicao = Carbon::createFromFormat('d/m/Y', $request->data_aquisicao)->format('Y-m-d');
                $bem->numero_documento = $request->numero_documento;
                $bem->fornecedor = $request->fornecedor;
                $bem->depreciar = $request->has('depreciar');
                $bem->estado_bem = $request->estado_bem;
                
                if ($request->has('dados_adicionais')) {
                    $bem->dados_adicionais = json_decode($request->dados_adicionais, true);
                }
                
                $bem->save();
                
                // Atualizar registro específico
                switch ($request->tipo) {
                    case 'veiculo':
                        $this->updateVeiculo($bem->id, $request);
                        break;
                    case 'imovel':
                        $this->updateImovel($bem->id, $request);
                        break;
                    case 'movel':
                        $this->updateBemMovel($bem->id, $request);
                        break;
                }
            });
            
            return redirect()->route('bem.index')->with('success', 'Bem atualizado com sucesso!');
            
        } catch (Exception $e) {
            return back()->with('error', 'Erro ao atualizar bem: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $bem = Bem::findOrFail($id);
            $bem->delete(); // Cascade vai deletar os registros relacionados
            
            return redirect()->route('bem.index')->with('success', 'Bem excluído com sucesso!');
            
        } catch (Exception $e) {
            return back()->with('error', 'Erro ao excluir bem: ' . $e->getMessage());
        }
    }

    /**
     * Validação dos dados do Bem
     */
    private function validateBem(Request $request, $id = null)
    {
        $rules = [
            'descricao' => 'required|string|max:255',
            'tipo' => 'required|in:veiculo,imovel,movel',
            'valor' => 'required|numeric|min:0',
            'data_aquisicao' => 'required|date_format:d/m/Y',
            'estado_bem' => 'nullable|in:Novo,Bom,Ruim',
        ];
        
        return Validator::make($request->all(), $rules);
    }

    /**
     * Formata número (remove pontos e vírgulas)
     */
    private function formatNumber(?string $number): float
    {
        if (empty($number)) {
            return 0;
        }
        $cleaned = str_replace('.', '', $number);
        $cleaned = str_replace(',', '.', $cleaned);
        return (float) $cleaned;
    }

    /**
     * Criar registro de Veículo
     */
    private function createVeiculo($bemId, Request $request)
    {
        $veiculo = new Veiculo();
        $veiculo->bem_id = $bemId;
        $veiculo->placa = $request->placa;
        $veiculo->renavam = $request->renavam;
        $veiculo->chassi = $request->chassi;
        $veiculo->combustivel = $request->combustivel;
        $veiculo->ano_modelo = $request->ano_modelo;
        $veiculo->ano_fabricacao = $request->ano_fabricacao;
        $veiculo->cor = $request->cor;
        $veiculo->crlv = $request->crlv;
        
        if ($request->has('dados_adicionais_veiculo')) {
            $veiculo->dados_adicionais = json_decode($request->dados_adicionais_veiculo, true);
        }
        
        $veiculo->save();
    }

    /**
     * Criar registro de Imóvel
     */
    private function createImovel($bemId, Request $request)
    {
        $imovel = new Imovel();
        $imovel->bem_id = $bemId;
        $imovel->inscricao_municipal = $request->inscricao_municipal;
        $imovel->cep = $request->cep;
        $imovel->endereco = $request->endereco;
        $imovel->bairro = $request->bairro;
        $imovel->cidade = $request->cidade;
        $imovel->uf = $request->uf;
        $imovel->matricula = $request->matricula;
        $imovel->cartorio = $request->cartorio;
        $imovel->livro = $request->livro;
        $imovel->folha = $request->folha;
        $imovel->area_total = $this->formatNumber($request->area_total);
        $imovel->area_privativa = $this->formatNumber($request->area_privativa);
        
        if ($request->has('dados_adicionais_imovel')) {
            $imovel->dados_adicionais = json_decode($request->dados_adicionais_imovel, true);
        }
        
        $imovel->save();
    }

    /**
     * Criar registro de Bem Móvel
     */
    private function createBemMovel($bemId, Request $request)
    {
        $bemMovel = new BemMovel();
        $bemMovel->bem_id = $bemId;
        $bemMovel->marca_modelo = $request->marca_modelo;
        $bemMovel->chapa_plaqueta = $request->chapa_plaqueta;
        $bemMovel->garantia = $request->garantia ? Carbon::createFromFormat('d/m/Y', $request->garantia)->format('Y-m-d') : null;
        
        if ($request->has('dados_adicionais_movel')) {
            $bemMovel->dados_adicionais = json_decode($request->dados_adicionais_movel, true);
        }
        
        $bemMovel->save();
    }

    /**
     * Atualizar registro de Veículo
     */
    private function updateVeiculo($bemId, Request $request)
    {
        $veiculo = Veiculo::where('bem_id', $bemId)->first();
        
        if (!$veiculo) {
            $this->createVeiculo($bemId, $request);
            return;
        }
        
        $veiculo->placa = $request->placa;
        $veiculo->renavam = $request->renavam;
        $veiculo->chassi = $request->chassi;
        $veiculo->combustivel = $request->combustivel;
        $veiculo->ano_modelo = $request->ano_modelo;
        $veiculo->ano_fabricacao = $request->ano_fabricacao;
        $veiculo->cor = $request->cor;
        $veiculo->crlv = $request->crlv;
        
        if ($request->has('dados_adicionais_veiculo')) {
            $veiculo->dados_adicionais = json_decode($request->dados_adicionais_veiculo, true);
        }
        
        $veiculo->save();
    }

    /**
     * Atualizar registro de Imóvel
     */
    private function updateImovel($bemId, Request $request)
    {
        $imovel = Imovel::where('bem_id', $bemId)->first();
        
        if (!$imovel) {
            $this->createImovel($bemId, $request);
            return;
        }
        
        $imovel->inscricao_municipal = $request->inscricao_municipal;
        $imovel->cep = $request->cep;
        $imovel->endereco = $request->endereco;
        $imovel->bairro = $request->bairro;
        $imovel->cidade = $request->cidade;
        $imovel->uf = $request->uf;
        $imovel->matricula = $request->matricula;
        $imovel->cartorio = $request->cartorio;
        $imovel->livro = $request->livro;
        $imovel->folha = $request->folha;
        $imovel->area_total = $this->formatNumber($request->area_total);
        $imovel->area_privativa = $this->formatNumber($request->area_privativa);
        
        if ($request->has('dados_adicionais_imovel')) {
            $imovel->dados_adicionais = json_decode($request->dados_adicionais_imovel, true);
        }
        
        $imovel->save();
    }

    /**
     * Atualizar registro de Bem Móvel
     */
    private function updateBemMovel($bemId, Request $request)
    {
        $bemMovel = BemMovel::where('bem_id', $bemId)->first();
        
        if (!$bemMovel) {
            $this->createBemMovel($bemId, $request);
            return;
        }
        
        $bemMovel->marca_modelo = $request->marca_modelo;
        $bemMovel->chapa_plaqueta = $request->chapa_plaqueta;
        $bemMovel->garantia = $request->garantia ? Carbon::createFromFormat('d/m/Y', $request->garantia)->format('Y-m-d') : null;
        
        if ($request->has('dados_adicionais_movel')) {
            $bemMovel->dados_adicionais = json_decode($request->dados_adicionais_movel, true);
        }
        
        $bemMovel->save();
    }
}

