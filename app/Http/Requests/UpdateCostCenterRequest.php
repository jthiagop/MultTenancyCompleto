<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Determine if the user is authorized to make this request.
 */
class UpdateCostCenterRequest extends FormRequest
{
    /**
     * Determina se o usuário está autorizado a fazer esta requisição.
     */
    public function authorize()
    {
        return true; // Altere conforme a lógica de autorização do seu sistema
    }

    /**
     * Regras de validação aplicadas à requisição.
     */
    public function rules()
    {
        return [
            'code' => 'nullable|numeric', // Código opcional
            'name' => 'required|string|max:255', // Nome obrigatório
            'budget' => 'nullable', // Orçamento opcional, deve ser numérico
            'category' => 'nullable|string|max:255', // Categoria opcional
            'start_date' => 'required|date_format:d/m/Y', // Data de criação opcional
            'end_date' => 'nullable|date_format:d/m/Y|after_or_equal:start_date', // Data de encerramento deve ser após ou igual à data de início
            'observations' => 'nullable|string', // Observações opcionais
            'status' => 'boolean', // Status deve ser booleano (1 ou 0)
        ];
    }

    /**
     * Mensagens personalizadas de validação.
     */
    public function messages()
    {
        return [
            'name.required' => 'O campo "Nome" é obrigatório.',
            'name.string' => 'O campo "Nome" deve ser um texto válido.',
            'name.max' => 'O campo "Nome" não pode ter mais que :max caracteres.',

            'code.numeric' => 'O campo "Código" deve ser numérico.',

            'budget.numeric' => 'O campo "Orçamento" deve ser um valor numérico.',

            'category.string' => 'O campo "Categoria" deve ser um texto válido.',
            'category.max' => 'O campo "Categoria" não pode ter mais que :max caracteres.',

            'start_date.date' => 'O campo "Data de Criação" deve ser uma data válida.',
            'end_date.date' => 'O campo "Data de Encerramento" deve ser uma data válida.',
            'end_date.after_or_equal' => 'A "Data de Encerramento" deve ser igual ou posterior à "Data de Criação".',

            'observations.string' => 'O campo "Observações" deve ser um texto válido.',

            'status.boolean' => 'O campo "Status" deve ser verdadeiro ou falso.',
        ];
    }

    /**
     * Nomes amigáveis para os atributos.
     */
    public function attributes()
    {
        return [
            'code' => 'Código',
            'name' => 'Nome',
            'budget' => 'Orçamento',
            'category' => 'Categoria',
            'start_date' => 'Data de Criação',
            'end_date' => 'Data de Encerramento',
            'observations' => 'Observações',
            'status' => 'Status',
        ];
    }
}
