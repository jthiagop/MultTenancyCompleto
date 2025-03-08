<?php

namespace App\Http\Controllers\App\Financeiro;

use App\Http\Controllers\Controller;
use App\Models\ContasFinanceiras;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ContasFinanceirasController extends Controller
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
        // 1) Validação - Note que ajustamos os nomes para combinar com o que o front envia
        $validated = $request->validate([
            'tipo_financeiro'      => 'required|in:despesa,receita',
            'status_pagamento'     => 'required|in:em aberto,pendente,pago,vencido,cancelado',

            'data_competencia'     => 'required',   // ex: "07/03/2025"
            'descricao'            => 'required|string|max:255',

            // Valor com vírgula e ponto? Precisamos tratar manualmente depois
            'valor'               => 'required',

            // FKs
            'lancamento_padraos_id' => 'nullable|exists:lancamento_padraos,id',
            'cost_centers_id'       => 'nullable|exists:cost_centers,id',

            // Dados de recorrência
            'repetir'             => 'nullable',  // Se é "on" ou não vem
            'repetir_a_cada'      => 'nullable|integer|min:1',  // "1"
            'frequencia'          => 'nullable|in:diario,semana,mensal,anual',
            'apos_ocorrencias'    => 'nullable|integer|min:1',

            'parcelamento'        => 'nullable|integer|min:1',
            'vencimento'          => 'nullable', // "07/03/2025"

            // IDs de forma de pagamento e conta (entidade financeira)
            'forma_pagamento'     => 'nullable',
            'conta_pagamento'     => 'nullable|exists:entidades_financeiras,id',

            'observacoes'         => 'nullable|string|max:500',
        ]);

        // 2) Ajuste de datas
        // 'data' -> vira 'data_competencia'
        $validated['data_competencia'] = Carbon::createFromFormat('d/m/Y', $validated['data_competencia'])
            ->format('Y-m-d');

        // 'vencimento' -> 'data_primeiro_vencimento'
        if (!empty($validated['vencimento'])) {
            $validated['data_primeiro_vencimento'] = Carbon::createFromFormat('d/m/Y', $validated['vencimento'])
                ->format('Y-m-d');
        } else {
            $validated['data_primeiro_vencimento'] = null;
        }

        // 3) Ajuste do campo 'valor' (ex.: "15.000,00" -> 15000.00)
        $valorBr = str_replace('.', '', $validated['valor']);   // remove pontos de milhar
        $valorBr = str_replace(',', '.', $valorBr);            // troca vírgula decimal
        $validated['valor'] = floatval($valorBr);             // converte para float

        // 4) Ajustar booleans e FKs
        // 'repetir' é "on" -> converter em boolean
        $validated['repetir'] = ($request->has('repetir') && $validated['repetir'] === 'on');

        // 'repetir_a_cada' -> vira 'intervalo_repeticao'
        $validated['intervalo_repeticao'] = $validated['repetir_a_cada'] ?? null;

        // 'forma_pagamento' -> 'forma_pagamento_id'
        $validated['forma_pagamento_id'] = $validated['forma_pagamento'] ?? null;

        // 'conta_pagamento' -> 'entidade_financeira_id'
        $validated['entidade_financeira_id'] = $validated['conta_pagamento'] ?? null;

        // 5) Criar um novo registro em 'contas_financeiras'
        $conta = ContasFinanceiras::create([
            'fornecedor_id'           => null, // se houver
            'data_competencia'        => $validated['data_competencia'],
            'descricao'               => $validated['descricao'],
            'valor'                   => $validated['valor'],
            'tipo_financeiro'         => $validated['tipo_financeiro'],
            'cost_centers_id'         => $validated['cost_centers_id'] ?? null,
            'lancamento_padraos_id'   => $validated['lancamento_padraos_id'] ?? null,

            'repetir'                 => $validated['repetir'],
            'intervalo_repeticao'     => $validated['intervalo_repeticao'] ?? null,
            'frequencia'              => $validated['frequencia'] ?? null,
            'parcelamento'            => $validated['parcelamento'] ?? null,

            'data_primeiro_vencimento' => $validated['data_primeiro_vencimento'] ?? null,
            'forma_pagamento_id'      => $validated['forma_pagamento_id'],
            'entidade_financeira_id'  => $validated['entidade_financeira_id'],

            'observacoes'             => $validated['observacoes'] ?? null,

            // Ajustar se quiser
            'valor_pago'              => 0,
            'juros'                   => 0,
            'multa'                   => 0,
            'desconto'                => 0,

            'status_pagamento'        => $validated['status_pagamento'],

            // Campos de auditoria
            'created_by'              => Auth::id(),
            'created_by_name'         => Auth::user()->name ?? 'Sistema',
        ]);

        // 6) Retorna JSON de sucesso
        return response()->json([
            'success' => true,
            'message' => 'Lançamento financeiro cadastrado com sucesso!',
            'data'    => $conta
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ContasFinanceiras $contasFinanceiras)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ContasFinanceiras $contasFinanceiras)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ContasFinanceiras $contasFinanceiras)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContasFinanceiras $contasFinanceiras)
    {
        //
    }
}
