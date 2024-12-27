<?php

namespace App\Http\Controllers\App\Filter;

use App\Http\Controllers\Controller;
use App\Models\Movimentacao;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RebortController extends Controller
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
        //
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
    public function edit(string $id)
    {
        //
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
    public function destroy(string $id)
    {
        //
    }

    public function generateReport(Request $request)
    {
            // Converter as datas do formato brasileiro para o formato aceito pelo Laravel
        $request->merge([
            'data_inicio' => Carbon::createFromFormat('d/m/Y', $request->input('data_inicio'))->format('Y-m-d'),
            'data_fim' => Carbon::createFromFormat('d/m/Y', $request->input('data_fim'))->format('Y-m-d'),
        ]);

        // Validação dos dados
        $validatedData = $request->validate([
            'entidade_id' => 'required|exists:entidades_financeiras,id',
            'company_id' => 'required|exists:companies,id',
            'data_inicio' => 'required|date',
            'data_fim' => 'required|date|after_or_equal:data_inicio',
                ], [
                        'data_fim.after_or_equal' => 'A data final deve ser igual ou posterior à data inicial.',
        ]);


        // Processar os dados para gerar o relatório
        $entidadeId = $validatedData['entidade_id'];
        $dataInicio = $validatedData['data_inicio'];
        $dataFim = $validatedData['data_fim'];
        $companyId = $validatedData['company_id'];

        // Consulta com JOIN
        $result = DB::table('caixas')
        ->join('lancamento_padraos', 'caixas.lancamento_padrao_id', '=', 'lancamento_padraos.id')
        ->join('movimentacoes', 'caixas.movimentacao_id', '=', 'movimentacoes.id')
        ->where('caixas.company_id', $companyId)
        ->whereBetween('movimentacoes.data', [$dataInicio, $dataFim])
        ->get();

            dd($result);

        // Retornar para a view ou gerar um PDF/Relatório
        return view('relatorios.resultados', compact('movimentacoes'));
    }
}
