<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Escritura;
use App\Models\Patrimonio;
use Illuminate\Http\Request;

class EscrituraController extends Controller
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
     * Armazena uma nova escritura no banco de dados.
     */
    public function store(Request $request, Patrimonio $patrimonio)
    {
        try {
            $validatedData = $request->validate([
                'outorgante' => 'required|string|max:255',
                'outorgante_email' => 'nullable|email|max:255',
                'outorgante_telefone' => 'nullable|string|max:20',
                'outorgado' => 'required|string|max:255',
                'outorgado_email' => 'nullable|email|max:255',
                'outorgado_telefone' => 'nullable|string|max:20',
                'matricula' => 'nullable|string|max:100',
                'aquisicao' => 'nullable|date',
                'valor' => 'nullable|regex:/^\d{1,3}(\.\d{3})*,\d{2}$/', // Exemplo de formatação: 1.234,56
                'area_total' => 'nullable',
                'area_privativa' => 'nullable',
                'patrimonio_id' => 'nullable|exists:patrimonios,id',
                'informacoes' => 'nullable|string|max:250',
            ]);

            $validatedData['valor'] = str_replace(',', '.', str_replace('.', '', $validatedData['valor']));

            $escritura = Escritura::create($validatedData);

            // Mensagem de sucesso usando flash
            session()->flash('success', 'Escritura Salva com sucesso!');

            // Redireciona de volta para a página anterior
            return redirect()->back();
        } catch (\Exception $e) {
            // Mensagem de erro usando flash
            session()->flash('error', 'Ocorreu um erro ao salvar a escritura: ' . $e->getMessage());

            // Redireciona de volta para a página anterior
            return redirect()->back()->withInput();
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Escritura $escritura)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Escritura $escritura)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Patrimonio $patrimonio, Escritura $escritura)
    {
        try {
            $validatedData = $request->validate([
                'outorgante' => 'nullable|string|max:255',
                'outorgante_email' => 'nullable|email|max:255',
                'outorgante_telefone' => 'nullable|string|max:20',
                'outorgado' => 'nullable|string|max:255',
                'outorgado_email' => 'nullable|email|max:255',
                'outorgado_telefone' => 'nullable|string|max:20',
                'matricula' => 'nullable|string|max:100',
                'aquisicao' => 'nullable|date',
                'valor' => 'nullable|regex:/^\d{1,3}(\.\d{3})*,\d{2}$/', // Exemplo de formatação: 1.234,56
                'area_total' => 'nullable',
                'area_privativa' => 'nullable',
                'patrimonio_id' => 'required|exists:patrimonios,id',
                'informacoes' => 'nullable|string|max:250',
            ]);

            $validatedData['valor'] = str_replace(',', '.', str_replace('.', '', $validatedData['valor']));

            $escritura->update($validatedData);

            // Mensagem de sucesso usando flash
            session()->flash('success', 'Escritura Atualizada com sucesso!');

            // Redireciona de volta para a página anterior
            return redirect()->back();
        } catch (\Exception $e) {
            // Mensagem de erro usando flash
            session()->flash('error', 'Ocorreu um erro ao atualizar a escritura: ' . $e->getMessage());

            // Redireciona de volta para a página anterior
            return redirect()->back()->withInput();
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Escritura $escritura)
    {
        //
    }
}
