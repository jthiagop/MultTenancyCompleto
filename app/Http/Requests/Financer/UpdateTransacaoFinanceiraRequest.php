<?php

namespace App\Http\Requests\Financer;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTransacaoFinanceiraRequest extends FormRequest
{
    /**
     * Determina se o usuário está autorizado a fazer esta solicitação.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Substitua por sua lógica de autorização, se necessário.
        // Retorne true para permitir a solicitação.
        return true;
    }

    /**
     * Regras de validação para a solicitação.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'data_competencia' => 'required',
            'descricao' => 'required|string',
            'valor' => 'required',
            'tipo' => 'required|in:entrada,saida',
            'lancamento_padrao_id' => 'required|exists:lancamento_padraos,id',
            'cost_center_id' => 'required|string',
            'entidade_id' => 'required|string',
            'tipo_documento' => 'required|string',
            'numero_documento' => 'nullable|string',
            'files.*' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'historico_complementar' => 'nullable|string|max:500',
            'entidade_banco_id' => 'nullable|exists:entidades_financeiras,id',
            'comprovacao_fiscal' => 'required|boolean', // 0 ou 1
            'entidade_id' => 'required|exists:entidades_financeiras,id',
            'banco_id' => 'nullable|exists:cadastro_bancos,id',
        ];
    }

    /**
     * Mensagens de erro personalizadas para validação.
     *
     * @return array
     */
    public function messages()
    {
        return [
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

    protected function prepareForValidation()
    {
        // Se o campo "data_competencia" existe e está em DD/MM/YYYY,
        // converta para YYYY-MM-DD antes da validação
        if ($this->has('data_competencia')) {
            try {
                $dataCompetencia = trim($this->data_competencia);
                Carbon::createFromFormat('d/m/Y', $dataCompetencia)->format('Y-m-d');
                $this->merge([
                    'data_competencia' => $dataCompetencia,
                ]);
            } catch (\Exception $e) {
                // Em caso de erro de formatação, você pode tratar aqui
            }
        }
    }

}
