<?php

namespace App\Http\Requests\Patrimonio;

use Illuminate\Foundation\Http\FormRequest;

class ForoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Permitir que qualquer usuário autorizado use esse request
    }

    public function rules(): array
    {
        return [
            'descricao' => 'required|string|max:255',
            'patrimonio' => 'required|string|max:255',
            'data' => 'required|date_format:d/m/Y',
            'livro' => 'nullable|string|max:50',
            'folha' => 'nullable|string|max:50',
            'registro' => 'nullable|string|max:50',
            'tags' => 'nullable|string',
            'cep' => 'nullable|string|max:10',
            'bairro' => 'nullable|string|max:255',
            'logradouro' => 'nullable|string|max:255',
            'localidade' => 'nullable|string|max:255',
            'uf' => 'nullable|string|max:2',
            'complemento' => 'nullable|string|max:255',
            'numIbge' => 'required|string',
            'numForo' => 'required|string',

            // Campos da escritura
            'outorgante' => 'nullable|string|max:255',
            'matricula' => 'nullable|string|max:50',
            'aquisicao' => 'nullable|date_format:d/m/Y',
            'outorgado' => 'nullable|string|max:255',
            'valor' => 'nullable|string',
            'area_total' => 'nullable|string',
            'area_privativa' => 'nullable|string',
            'informacoes' => 'nullable|string',
            'outorgante_telefone' => 'nullable|string|max:20',
            'outorgante_email' => 'nullable|email|max:255',
            'outorgado_telefone' => 'nullable|string|max:20',
            'outorgado_email' => 'nullable|email|max:255',
        ];
    }
}
