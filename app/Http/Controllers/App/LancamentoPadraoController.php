<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\LancamentoPadrao;
use Illuminate\Http\Request;

class LancamentoPadraoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $lps = LancamentoPadrao::all();

        // Mapeia categorias para classes de cor
        $categoryColors = [
            'Serviços essenciais' => 'badge-light-success',
            'Suprimentos' => 'badge-light-primary',
            'Pessoal' => 'badge-light-warning',
            'Alimentação' => 'badge-light-info',
            'Saúde' => 'badge-light-danger',
            'Manutenção' => 'badge-light-dark',
            'Liturgia' => 'badge-light-muted',
            'Equipamentos' => 'badge-light-secondary',
            'Material de escritório' => 'badge-light-light',
            'Educação' => 'badge-light-orange',
            'Transporte' => 'badge-light-teal',
            'Contribuições' => 'badge-light-purple',
            // Adicione outras categorias e cores conforme necessário
        ];

        return view('app.cadastros.lancamentoPadrao.index', compact('lps', 'categoryColors'));
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