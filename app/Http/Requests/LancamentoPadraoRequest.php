<?php

namespace App\Http\Requests;

use App\Models\Contabilide\ChartOfAccount;
use Closure;
use Illuminate\Foundation\Http\FormRequest;

class LancamentoPadraoRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'description' => 'required|string|max:255',
            'type' => 'required|in:entrada,saida,ambos',
            'category' => 'required|string|max:255',
            'conta_debito_id' => [
                'required',
                'exists:chart_of_accounts,id',
                $this->validarContaAnalitica('A conta de débito'),
                $this->validarContaPertenceEmpresa('A conta de débito'),
            ],
            'conta_credito_id' => [
                'required',
                $this->validarContaCreditoExiste(),
                $this->validarContaCreditoAnalitica(),
                $this->validarContaCreditoPertenceEmpresa(),
            ],
        ];
    }

    /**
     * Validação customizada que roda após as regras individuais.
     * Aqui verificamos a coerência entre débito e crédito conforme o tipo.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Não valida coerência se já houver erros nos campos individuais
            if ($validator->errors()->has('conta_debito_id') || $validator->errors()->has('conta_credito_id')) {
                return;
            }

            $type = $this->input('type');
            $debitoId = $this->input('conta_debito_id');
            $creditoId = $this->input('conta_credito_id');

            // Conta crédito "0" (usar conta do caixa/banco) não precisa de validação de coerência
            if ($creditoId == '0' || $creditoId === 0) {
                return;
            }

            $contaDebito = ChartOfAccount::find($debitoId);
            $contaCredito = ChartOfAccount::find($creditoId);

            if (!$contaDebito || !$contaCredito) {
                return;
            }

            // Débito e crédito não podem ser a mesma conta
            if ($debitoId == $creditoId) {
                $validator->errors()->add(
                    'conta_credito_id',
                    'A conta de crédito não pode ser a mesma que a conta de débito.'
                );
                return;
            }

            // Validações de coerência conforme o tipo do lançamento
            $this->validarCoerenciaPorTipo($validator, $type, $contaDebito, $contaCredito);
        });
    }

    /**
     * Valida a coerência entre débito e crédito baseado no tipo do lançamento.
     *
     * Regras contábeis:
     * - ENTRADA (receita): D: Ativo (caixa/banco) | C: Receita
     * - SAÍDA (despesa):   D: Despesa             | C: Ativo (caixa/banco)
     * - AMBOS:             Validação flexível - apenas verifica se não são do mesmo grupo
     */
    private function validarCoerenciaPorTipo($validator, string $type, ChartOfAccount $debito, ChartOfAccount $credito): void
    {
        switch ($type) {
            case 'entrada':
                // Para entradas: Débito deve ser Ativo, Crédito deve ser Receita
                if (!in_array($debito->type, ['ativo'])) {
                    $validator->errors()->add(
                        'conta_debito_id',
                        "Para lançamentos de entrada, a conta de débito deve ser do tipo Ativo (caixa/banco). Conta selecionada: {$debito->code} - {$debito->name} ({$debito->type})."
                    );
                }
                if (!in_array($credito->type, ['receita'])) {
                    $validator->errors()->add(
                        'conta_credito_id',
                        "Para lançamentos de entrada, a conta de crédito deve ser do tipo Receita. Conta selecionada: {$credito->code} - {$credito->name} ({$credito->type})."
                    );
                }
                break;

            case 'saida':
                // Para saídas: Débito deve ser Despesa, Crédito deve ser Ativo
                if (!in_array($debito->type, ['despesa'])) {
                    $validator->errors()->add(
                        'conta_debito_id',
                        "Para lançamentos de saída, a conta de débito deve ser do tipo Despesa. Conta selecionada: {$debito->code} - {$debito->name} ({$debito->type})."
                    );
                }
                if (!in_array($credito->type, ['ativo'])) {
                    $validator->errors()->add(
                        'conta_credito_id',
                        "Para lançamentos de saída, a conta de crédito deve ser do tipo Ativo (caixa/banco). Conta selecionada: {$credito->code} - {$credito->name} ({$credito->type})."
                    );
                }
                break;

            case 'ambos':
                // Para "ambos": apenas garante que débito e crédito não sejam ambos do mesmo tipo patrimonial
                if ($debito->type === $credito->type) {
                    $validator->errors()->add(
                        'conta_credito_id',
                        "Para lançamentos do tipo 'ambos', as contas de débito e crédito devem ser de tipos diferentes. Ambas são do tipo '{$debito->type}'."
                    );
                }
                break;
        }
    }

    /**
     * Regra: Verifica se a conta é analítica (aceita lançamentos).
     */
    private function validarContaAnalitica(string $label): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail) use ($label) {
            $conta = ChartOfAccount::find($value);
            if ($conta && !$conta->allowsPosting()) {
                $fail("{$label} deve ser uma conta analítica (que aceita lançamentos). A conta \"{$conta->code} - {$conta->name}\" é sintética (grupo).");
            }
        };
    }

    /**
     * Regra: Verifica se a conta pertence à empresa ativa.
     */
    private function validarContaPertenceEmpresa(string $label): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail) use ($label) {
            $companyId = session('active_company_id');
            if (!$companyId) {
                return;
            }
            $conta = ChartOfAccount::find($value);
            if ($conta && $conta->company_id != $companyId) {
                $fail("{$label} não pertence à empresa ativa.");
            }
        };
    }

    /**
     * Regra: Verifica existência da conta de crédito (permite valor "0").
     */
    private function validarContaCreditoExiste(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail) {
            // Valor "0" significa "usar conta do banco/caixa" — é permitido
            if ($value == '0' || $value === 0) {
                return;
            }
            if ($value && !ChartOfAccount::where('id', $value)->exists()) {
                $fail('A conta de crédito selecionada não existe.');
            }
        };
    }

    /**
     * Regra: Verifica se a conta de crédito é analítica (permite valor "0").
     */
    private function validarContaCreditoAnalitica(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail) {
            if ($value == '0' || $value === 0) {
                return;
            }
            $conta = ChartOfAccount::find($value);
            if ($conta && !$conta->allowsPosting()) {
                $fail("A conta de crédito deve ser uma conta analítica (que aceita lançamentos). A conta \"{$conta->code} - {$conta->name}\" é sintética (grupo).");
            }
        };
    }

    /**
     * Regra: Verifica se a conta de crédito pertence à empresa ativa (permite valor "0").
     */
    private function validarContaCreditoPertenceEmpresa(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail) {
            if ($value == '0' || $value === 0) {
                return;
            }
            $companyId = session('active_company_id');
            if (!$companyId) {
                return;
            }
            $conta = ChartOfAccount::find($value);
            if ($conta && $conta->company_id != $companyId) {
                $fail('A conta de crédito não pertence à empresa ativa.');
            }
        };
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'description.required' => 'O nome do lançamento é obrigatório.',
            'description.max' => 'O nome do lançamento não pode ter mais de 255 caracteres.',
            'type.required' => 'O tipo do lançamento é obrigatório.',
            'type.in' => 'O tipo deve ser "entrada", "saída" ou "ambos".',
            'category.required' => 'A categoria é obrigatória.',
            'conta_debito_id.required' => 'A conta de débito é obrigatória.',
            'conta_debito_id.exists' => 'A conta de débito selecionada não existe.',
            'conta_credito_id.required' => 'A conta de crédito é obrigatória.',
        ];
    }

    /**
     * Handle a failed validation attempt (suporte AJAX).
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        if ($this->ajax() || $this->wantsJson()) {
            throw new \Illuminate\Validation\ValidationException($validator,
                response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422)
            );
        }

        parent::failedValidation($validator);
    }
}
