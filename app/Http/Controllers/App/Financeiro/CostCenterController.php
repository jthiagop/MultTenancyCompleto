<?php

namespace App\Http\Controllers\App\Financeiro;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCostCenterRequest;
use App\Http\Requests\UpdateCostCenterRequest;
use App\Models\Financeiro\CostCenter;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CostCenterController extends Controller
{
    /**
     * Exibe a lista de centros de custo da empresa ativa na sessão.
     */
    public function index()
    {
        // 1. A consulta agora usa o scope 'forActiveCompany' para filtrar automaticamente
        //    pela empresa que está ativa na sessão. É a forma mais limpa e segura.
        $centroCustos = CostCenter::forActiveCompany()->get();

        // 2. A sua lógica para calcular o progresso continua perfeita.
        //    O método 'transform' é uma ótima maneira de adicionar dados à coleção.
        $centroCustos->transform(function ($centro) {
            $centro->progresso = $this->calcularProgresso($centro->start_date, $centro->end_date);
            return $centro;
        });

        // 3. Retorna a view com os dados já filtrados e formatados.
        return view('app.cadastros.costCenter.index', [
            'centroCustos' => $centroCustos
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
    public function store(StoreCostCenterRequest $request)
    {
        // ++ Lógica correta adicionada ++
        // 1. Recupera o ID da companhia ativa na sessão do usuário.
        $activeCompanyId = session('active_company_id');

        // 2. Verificação de segurança: garante que há uma empresa ativa na sessão.
        if (!$activeCompanyId) {
            return redirect()->back()
                ->with('error', 'Nenhuma empresa selecionada. Por favor, escolha uma empresa antes de continuar.');
        }

        // -> Linha antiga removida
        // $subsidiary = User::getCompany();

        // Recupera os dados já validados pelo StoreCostCenterRequest
        $data = $request->validated();

        // Converte as datas para o formato padrão do banco de dados (boa prática)
        $data['start_date'] = Carbon::createFromFormat('d/m/Y', $data['start_date'])->format('Y-m-d'); // Y-m-d é o padrão
        $data['end_date'] = Carbon::createFromFormat('d/m/Y', $data['end_date'])->format('Y-m-d'); // Y-m-d é o padrão

        // Converte o formato do orçamento para salvar no banco
        $data['budget'] = str_replace(',', '.', str_replace('.', '', $data['budget']));

        // ++ Lógica correta adicionada ++
        // 3. Associa o centro de custo à empresa ativa da sessão.
        $data['company_id'] = $activeCompanyId;

        // Associa o usuário que criou o registro
        $data['created_by'] = Auth::id();
        $data['created_by_name'] = Auth::user()->name;
        $data['updated_by'] = Auth::id();
        $data['updated_by_name'] = Auth::user()->name;

        // Cria o centro de custo
        CostCenter::create($data);

        // Redireciona com uma mensagem de sucesso
        return redirect()->route('costCenter.index')
            ->with('success', 'Centro de custo criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $costCenter = CostCenter::find($id);
        $transacoes = $costCenter->transacoesFinanceiras;

        return view('app.cadastros.costCenter.show', [
            'transacoes' => $transacoes
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Suponha que você já tenha o ID da empresa disponível
        $companyId = Auth::user()->company_id; // ou $companyId = 1; se o ID for fixo
        // Busca todos os centros de custo
        $centroCustos = CostCenter::where('company_id', $companyId)->get();
        $centroCusto = CostCenter::findOrFail($id);


        // Retorna para a view passando o array "centroCustos" já atualizado
        return view('app.cadastros.costCenter.edit', [
            'centroCustos' => $centroCustos,
            'centroCusto' => $centroCusto

        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCostCenterRequest $request, $id)
    {
        // Dados validados automaticamente pelo UpdateCostCenterRequest
        $validated = $request->validated();

        $validated['start_date'] = Carbon::createFromFormat('d/m/Y', $validated['start_date'])->format('Y/m/d');
        $validated['end_date'] = Carbon::createFromFormat('d/m/Y', $validated['end_date'])->format('Y/m/d');
        $validated['budget'] = str_replace(',', '.', str_replace('.', '', $validated['budget']));

        $centroCusto = CostCenter::findOrFail($id);
        $centroCusto->update($validated);

        return redirect()->route('costCenter.index')->with('success', 'Centro de custo atualizado com sucesso!');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }


    function calcularProgresso($start_date, $end_date)
    {
        // Verifica se ambas as datas estão disponíveis
        if (!$start_date || !$end_date) {
            return 0; // Retorna 0% se alguma data estiver faltando
        }

        // Converte as datas em instâncias de Carbon
        $inicio = Carbon::parse($start_date);
        $fim = Carbon::parse($end_date);
        $hoje = Carbon::now();

        // Se a data atual estiver antes da data inicial, progresso é 0%
        if ($hoje->lt($inicio)) {
            return 0;
        }

        // Se a data atual estiver após a data final, progresso é 100%
        if ($hoje->gt($fim)) {
            return 100;
        }

        // Calcula o progresso como porcentagem
        $totalDias = $inicio->diffInDays($fim);
        $diasPassados = $inicio->diffInDays($hoje);

        return round(($diasPassados / $totalDias) * 100, 2); // Arredonda para 2 casas decimais
    }
}
