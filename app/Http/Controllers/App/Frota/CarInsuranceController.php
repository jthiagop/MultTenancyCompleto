<?php

namespace App\Http\Controllers\App\Frota;

use App\Http\Controllers\Controller;
use App\Models\CarInsurance;
use Illuminate\Http\Request;

class CarInsuranceController extends Controller
{
    // Listar todos os veículos
    public function index()
    {
        $veiculos = CarInsurance::doesntHave('vendas')->get(); // Veículos sem registro de venda
        return view('app.patrimonios.car_insurance.index', compact('veiculos'));
    }

    // Exibir formulário de criação
    public function create()
    {
        return view('car_insurance.create');
    }

    // Salvar novo veículo
    public function store(Request $request)
    {
        $validated = $request->validate([
            'placa' => 'required|unique:car_insurance|max:10',
            'modelo' => 'required|string|max:255',
            'ano' => 'required|integer|min:1900|max:' . date('Y'),
            'responsavel' => 'required|string|max:255',
        ]);

        CarInsurance::create($validated);

        return redirect()->route('car_insurance.index')->with('success', 'Veículo adicionado com sucesso!');
    }

    // Exibir detalhes do veículo
    public function show($id)
    {
        $veiculo = CarInsurance::findOrFail($id);
        return view('car_insurance.show', compact('veiculo'));
    }

    // Exibir formulário de edição
    public function edit($id)
    {
        $veiculo = CarInsurance::findOrFail($id);
        return view('car_insurance.edit', compact('veiculo'));
    }

    // Atualizar veículo
    public function update(Request $request, $id)
    {
        $veiculo = CarInsurance::findOrFail($id);

        $validated = $request->validate([
            'modelo' => 'required|string|max:255',
            'ano' => 'required|integer|min:1900|max:' . date('Y'),
        ]);

        $veiculo->update($validated);

        return redirect()->route('car_insurance.index')->with('success', 'Veículo atualizado com sucesso!');
    }

    // Marcar como vendido
    public function sell(Request $request, $id)
    {
        $veiculo = CarInsurance::findOrFail($id);
        $veiculo->update([
            'vendido' => true,
            'data_venda' => now(),
            'valor_venda' => $request->input('valor_venda'),
        ]);

        return redirect()->route('car_insurance.index')->with('success', 'Veículo marcado como vendido!');
    }

    // Excluir (Soft Delete)
    public function destroy($id)
    {
        $veiculo = CarInsurance::findOrFail($id);
        $veiculo->delete();

        return redirect()->route('car_insurance.index')->with('success', 'Veículo excluído com sucesso!');
    }
}
