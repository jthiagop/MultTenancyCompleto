<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\EntidadeFinanceira;
use App\Models\MovimentacaoFinanceira;
use Illuminate\Http\Request;

class MovimentacaoController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'entidade_id' => 'required|exists:entidades_financeiras,id',
            'descricao' => 'required|string|max:255',
            'valor' => 'required|numeric',
            'tipo' => 'required|in:entrada,saida',
            'data_competencia' => 'required|date',
        ]);

        // Cria a movimentação


        // Atualiza o saldo da entidade
        $entidade = EntidadeFinanceira::findOrFail($validatedData['entidade_id']);
        if ($validatedData['tipo'] === 'entrada') {
            $entidade->saldo_atual += $validatedData['valor'];
        } else {
            $entidade->saldo_atual -= $validatedData['valor'];
        }
        $entidade->save();

        return redirect()->back()->with('message', 'Movimentação registrada com sucesso!');
    }
}
