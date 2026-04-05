<?php

namespace App\Http\Requests\Financer;

use App\Support\Money;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRepasseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // Converter valor_total do formato brasileiro (1.500,00) para DECIMAL (1500.00)
        if ($this->has('valor_total') && $this->input('valor_total') !== null) {
            $money = Money::fromHumanInput((string) $this->input('valor_total'));
            $this->merge([
                'valor_total' => $money->toDatabase(),
            ]);
        }

        // Converter datas do formato brasileiro (dd/mm/yyyy) para Y-m-d
        foreach (['data_emissao', 'data_entrada', 'data_vencimento'] as $campo) {
            if ($this->has($campo) && $this->input($campo) !== null) {
                $parsed = \DateTime::createFromFormat('d/m/Y', $this->input($campo));
                if ($parsed) {
                    $this->merge([$campo => $parsed->format('Y-m-d')]);
                }
            }
        }

        // Converter valores dos itens
        if ($this->has('itens') && is_array($this->input('itens'))) {
            $itens = $this->input('itens');
            foreach ($itens as $i => $item) {
                if (isset($item['valor'])) {
                    $money = Money::fromHumanInput((string) $item['valor']);
                    $itens[$i]['valor'] = $money->toDatabase();
                }
            }
            $this->merge(['itens' => $itens]);
        }
    }

    public function rules(): array
    {
        return [
            'entidade_origem_id' => 'required|exists:entidades_financeiras,id',
            'valor_total' => 'required|numeric|gt:0',
            'data_emissao' => 'required|date',
            'data_entrada' => 'nullable|date',
            'data_vencimento' => 'nullable|date',
            'competencia' => 'nullable|string|max:20',
            'tipo_documento' => 'nullable|string|max:50',
            'numero_documento' => 'required|string|max:100',
            'forma_pagamento_id' => 'nullable|exists:formas_pagamento,id',
            'forma_recebimento_id' => 'required|exists:formas_recebimento,id',
            'descricao' => 'required|string|max:500',

            // Itens (filiais destino)
            'itens' => 'required|array|min:1',
            'itens.*.company_destino_id' => 'required|exists:companies,id',
            'itens.*.entidade_destino_id' => 'nullable|exists:entidades_financeiras,id',
            'itens.*.percentual' => 'nullable|numeric|min:0|max:100',
            'itens.*.valor' => 'required|numeric|gt:0',
        ];
    }

    public function messages(): array
    {
        return [
            'entidade_origem_id.required' => 'Selecione a conta de origem.',
            'valor_total.required' => 'O valor total é obrigatório.',
            'valor_total.gt' => 'O valor total deve ser maior que zero.',
            'data_emissao.required' => 'A data de emissão é obrigatória.',
            'numero_documento.required' => 'O número do documento é obrigatório.',
            'forma_recebimento_id.required' => 'Selecione a forma de recebimento.',
            'descricao.required' => 'A descrição é obrigatória.',
            'itens.required' => 'Selecione ao menos uma filial destino.',
            'itens.min' => 'Selecione ao menos uma filial destino.',
            'itens.*.company_destino_id.required' => 'Selecione a filial destino.',
            'itens.*.entidade_destino_id.exists' => 'A conta destino selecionada é inválida.',
            'itens.*.valor.required' => 'O valor do item é obrigatório.',
            'itens.*.valor.gt' => 'O valor de cada item deve ser maior que zero.',
        ];
    }
}
