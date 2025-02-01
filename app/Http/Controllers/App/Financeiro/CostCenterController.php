<?php

namespace App\Http\Controllers\App\Financeiro;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCostCenterRequest;
use App\Http\Requests\UpdateCostCenterRequest;
use App\Models\Financeiro\CostCenter;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CostCenterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Suponha que você já tenha o ID da empresa disponível
        $companyId = Auth::user()->company_id; // ou $companyId = 1; se o ID for fixo
        // Busca todos os centros de custo
        $centroCustos = CostCenter::where('company_id', $companyId)->get();

            // Adiciona o progresso ao resultado
        $centroCustos->transform(function ($centro) {
            $centro->progresso = $this->calcularProgresso($centro->start_date, $centro->end_date);
            return $centro;
        });

        // Retorna para a view passando o array "centroCustos" já atualizado
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
        // Recupera a companhia associada ao usuário autenticado
        $subsidiary = User::getCompany();

        // Recupera os dados já validados
        $data = $request->validated();

        // Exemplo: ajustando data (caso precise formatar antes de salvar)
        // $data['start_date'] = Carbon::createFromFormat('d/m/Y', $data['start_date'])->format('Y-m-d');
        $data['start_date'] = Carbon::createFromFormat('d/m/Y', $data['start_date'])->format('Y/m/d');
        $data['end_date'] = Carbon::createFromFormat('d/m/Y', $data['end_date'])->format('Y/m/d');
        $data['budget'] = str_replace(',', '.', str_replace('.', '', $data['budget']));
        $data['company_id'] = $subsidiary->company_id;
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
