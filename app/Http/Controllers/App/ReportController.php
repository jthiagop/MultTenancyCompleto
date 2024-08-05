<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Caixa;
use Illuminate\Http\Request;

class ReportController extends Controller
{

    public function shippingReport()
    {
        return view('report.shipping');
    }

    public function shippingReportData(Request $request)
    {
        $caixas = Caixa::select('id', 'data_competencia', 'tipo_documento', 'lancamento_padrao', 'tipo', 'valor', 'origem')->get();

        return datatables()->of($caixas)
            ->editColumn('data_competencia', function ($caixa) {
                return date('d-m-Y', strtotime($caixa->data_competencia));
            })
            ->editColumn('tipo', function ($caixa) {
                $badgeClass = $caixa->tipo == 'entrada' ? 'badge-success' : 'badge-danger';
                return '<div class="badge fw-bold ' . $badgeClass . '">' . $caixa->tipo . '</div>';
            })
            ->editColumn('valor', function ($caixa) {
                return 'R$ ' . number_format($caixa->valor, 2, ',', '.');
            })
            ->addColumn('acoes', function ($caixa) {
                $editUrl = route('caixa.edit', $caixa->id);
                $deleteUrl = route('caixa.destroy', $caixa->id);

                return '<a href="' . $editUrl . '" class="menu-link px-3">Editar</a>
                        <a href="#" class="menu-link px-3 delete-link" data-id="' . $caixa->id . '">Excluir</a>
                        <form id="delete-form-' . $caixa->id . '" action="' . $deleteUrl . '" method="POST" style="display: none;">
                            ' . csrf_field() . method_field('DELETE') . '
                        </form>';
            })
            ->rawColumns(['tipo', 'acoes'])
            ->make(true);
    }
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
}
