<?php

namespace App\Http\Requests\Financer;

use App\Models\LancamentoPadrao;
use Illuminate\Foundation\Http\FormRequest;

class StoreTransacaoFinanceiraRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Defina como `true` para permitir a validação
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */

    public function rules(): array
    {
                // Busca na tabela lancamento_padraos o registro cujo description é 'Deposito Bancário'
        // e pega apenas o campo 'id'.
        $lancamentoPadraoDepositoId = LancamentoPadrao::where('description', 'Deposito Bancário')
            ->value('id');

            //dd($_REQUEST);

        return [

            'data_competencia' => 'required',
            'descricao' => 'required|string',
            'valor' => 'required',
            'tipo' => 'required|in:entrada,saida',
            'lancamento_padrao_id' => 'required|exists:lancamento_padraos,id',
            'cost_center_id' => 'required|string',
            'origem' => 'required|string',
            'tipo_documento' => 'required|string',
            'numero_documento' => 'nullable|string',
            'files.*' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'historico_complementar' => 'nullable|string|max:500',
            'comprovacao_fiscal' => 'required|boolean', // 0 ou 1
            'entidade_id' => 'required|exists:entidades_financeiras,id',
            'banco_id' => 'nullable|exists:cadastro_bancos,id',
            'entidade_banco_id' => [
                'nullable',
                'exists:entidades_financeiras,id',
                // Fica obrigatório se lancamento_padrao_id for igual ao $lancamentoPadraoDepositoId
                'required_if:lancamento_padrao_id,' . $lancamentoPadraoDepositoId
            ],
        ];
    }

    /**
     * Get the custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'entidade_banco_id.required_if' => 'O campo Banco de Depósito é obrigatório quando o Lançamento Padrão é "Deposito Bancário".',
            'entidade_id.required' => 'A entidade é obrigatória.',
            'cost_center_id.required' => 'O centro de Custo é obrigatória.',
            'entidade_id.exists' => 'A entidade selecionada não é válida.',
            'data_competencia.required' => 'A data de competência é obrigatória.',
            'descricao.required' => 'A descrição é obrigatória.',
            'valor.required' => 'O valor é obrigatório.',
            'banco_id.required' => 'Selecione um banco.',
            'valor.numeric' => 'O valor deve ser numérico.',
            'tipo.required' => 'O tipo é obrigatório.',
            'tipo.in' => 'O tipo deve ser "entrada" ou "saida".',
            'lancamento_padrao_id.exists' => 'O lançamento padrão selecionado não é válido.',
            'files.*.mimes' => 'Os arquivos devem ser do tipo: jpeg, png, jpg ou pdf.',
            'files.*.max' => 'O tamanho máximo do arquivo é 2MB.',
        ];
    }
}
