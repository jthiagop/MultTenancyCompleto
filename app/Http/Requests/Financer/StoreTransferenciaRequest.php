<?php

namespace App\Http\Requests\Financer;

use App\Support\Money;
use Illuminate\Foundation\Http\FormRequest;

class StoreTransferenciaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // Converter valor do formato brasileiro (1.500,00) para DECIMAL (1500.00)
        if ($this->has('valor') && $this->input('valor') !== null) {
            $money = Money::fromHumanInput((string) $this->input('valor'));
            $this->merge([
                'valor' => $money->toDatabase(),
            ]);
        }

        // Converter data do formato brasileiro (dd/mm/yyyy) para Y-m-d
        if ($this->has('data') && $this->input('data') !== null) {
            $parsed = \DateTime::createFromFormat('d/m/Y', $this->input('data'));
            if ($parsed) {
                $this->merge([
                    'data' => $parsed->format('Y-m-d'),
                ]);
            }
        }
    }

    public function rules(): array
    {
        return [
            'entidade_origem_id' => 'required|exists:entidades_financeiras,id',
            'entidade_destino_id' => 'required|exists:entidades_financeiras,id|different:entidade_origem_id',
            'valor' => 'required|numeric|gt:0',
            'data' => 'required|date|before_or_equal:today',
            'descricao' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'entidade_origem_id.required' => 'Selecione a conta de origem.',
            'entidade_origem_id.exists' => 'A conta de origem selecionada não existe.',
            'entidade_destino_id.required' => 'Selecione a conta de destino.',
            'entidade_destino_id.exists' => 'A conta de destino selecionada não existe.',
            'entidade_destino_id.different' => 'A conta de destino deve ser diferente da conta de origem.',
            'valor.required' => 'Informe o valor da transferência.',
            'valor.gt' => 'O valor deve ser maior que zero.',
            'data.required' => 'Informe a data da transferência.',
            'data.before_or_equal' => 'A data não pode ser futura.',
            'descricao.required' => 'Informe a descrição da transferência.',
            'descricao.max' => 'A descrição deve ter no máximo 255 caracteres.',
        ];
    }
}
