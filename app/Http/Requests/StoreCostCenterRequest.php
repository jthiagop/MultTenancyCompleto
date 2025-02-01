<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCostCenterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            // Código do centro de custo (opcional)
            // Se for único, adicione: 'unique:cost_centers,code'
            'code' => 'nullable|numeric|unique:cost_centers,code',

            // Nome do centro de custo (obrigatório)
            // Ajuste o nome do campo se não for "target_title" no form
            'name' => 'required|string|max:255',

            'budget' => 'required|max:255',

            // Categoria (opcional)
            'category' => 'nullable|string|max:255',

            // Data de criação (obrigatória) - validando como data
            // Se deseja um formato específico (ex: dd/mm/YYYY),
            // pode usar 'date_format:d/m/Y' ao invés de apenas 'date'.
            'start_date' => 'required|date_format:d/m/Y',
            'end_date' => 'nullable|date_format:d/m/Y',

            // Observações (opcional)
            'observations' => 'nullable|string',

            // Status (checkbox marcado ou não)
            // Aqui consideramos que o form envia "1" ou nada. Então 'boolean' funciona bem.
            'status' => 'boolean',
        ];
    }

    /**
     * Customizando as mensagens de erro para cada regra.
     *
     * Você pode usar :attribute para inserir o nome do campo dinamicamente.
     */
    public function messages()
    {
        return [
            'code.numeric' => 'O código deve ser um valor numérico.',
            'code.unique' => 'O código deve ser um valor único.',

            'target_title.required' => 'O nome do centro de custo é obrigatório.',
            'target_title.string'   => 'O nome do centro de custo precisa ser texto.',
            'target_title.max'      => 'O nome do centro de custo não pode ter mais de :max caracteres.',

            'start_date.required' => 'A data de criação é obrigatória.',
            'start_date.date'     => 'A data de criação deve ser uma data válida.',

            'status.boolean' => 'O status deve ser verdadeiro ou falso (checkbox).',
        ];
    }

    /**
     * Atributos amigáveis para exibir nas mensagens de erro (opcional).
     */
    public function attributes()
    {
        return [
            'target_title'  => 'Nome do Centro de Custo',
            'start_date'    => 'Data de Criação',
            'observations'  => 'Observações',
            // etc...
        ];
    }

}
