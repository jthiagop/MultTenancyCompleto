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
            'descricao2' => 'string',
            'valor' => 'required|numeric|gt:0',
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
            'conta_debito_id' => 'nullable|exists:chart_of_accounts,id',
            'conta_credito_id' => 'nullable|exists:chart_of_accounts,id',

            // Validações de recorrência (quando checkbox repetir estiver marcado)
            'intervalo_repeticao' => 'nullable|integer|min:1',
            'frequencia' => 'nullable|in:diario,semanal,mensal,anual',
            'apos_ocorrencias' => 'nullable|integer|min:1|max:366',
            'dia_cobranca' => 'required_if:repetir_lancamento,1',
            'configuracao_recorrencia' => [
                'required_if:repetir_lancamento,1',
                'nullable',
                function ($attribute, $value, $fail) {
                    // Se o valor estiver vazio, null ou for string vazia, permite
                    // (pode ser uma nova configuração sendo criada com campos temporários)
                    if (empty($value) || $value === '' || $value === null) {
                        return;
                    }

                    // Se for string que começa com "temp_", permite (nova configuração do drawer)
                    if (is_string($value) && strpos($value, 'temp_') === 0) {
                        return;
                    }

                    // Se for um ID numérico (string ou int), valida que existe na tabela
                    if (is_numeric($value) || (is_string($value) && ctype_digit($value))) {
                        $id = (int) $value;
                        $companyId = session('active_company_id');

                        if (!$companyId) {
                            $fail('Company ID não encontrado.');
                            return;
                        }

                        $exists = \App\Models\Financeiro\Recorrencia::where('id', $id)
                            ->where('company_id', $companyId)
                            ->exists();
                        if (!$exists) {
                            $fail('A configuração de recorrência selecionada não existe.');
                        }
                    }
                },
            ],
            'configuracao_recorrencia_temp' => 'nullable',

            // Validações de situação e agendamento
            'vencimento' => 'required_if:repetir_lancamento,1|nullable|date_format:d/m/Y', // Campo do formulário
            'data_vencimento' => 'nullable|date', // Campo processado
            'valor_pago' => [
                'nullable',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) {
                    // Validação: valor_pago não pode ser maior que valor
                    if (!empty($value)) {
                        $valorTotal = floatval($this->input('valor'));
                        $valorPago = floatval($value);
                        
                        if ($valorPago > $valorTotal) {
                            $fail('Valor pago não pode ser maior que o valor total.');
                        }
                    }
                    
                    // Validação: valor_pago obrigatório se pago/recebido checked
                    $isPago = $this->input('pago') === '1' || $this->input('pago') === 1;
                    $isRecebido = $this->input('recebido') === '1' || $this->input('recebido') === 1;
                    
                    if (($isPago || $isRecebido) && empty($value)) {
                        $fail('Valor pago é obrigatório quando marcado como pago/recebido.');
                    }
                },
            ],
            'juros' => 'nullable|numeric|min:0',
            'multa' => 'nullable|numeric|min:0',
            'desconto' => 'nullable|numeric|min:0',
            'valor_a_pagar' => 'nullable|numeric|min:0',
            'situacao' => 'nullable|in:em_aberto,desconsiderado,atrasado,pago_parcial,pago,previsto',
            'agendado' => 'nullable|boolean',

            // Validações de campos de pagamento (para lançamentos fracionados)
            'data_pagamento' => [
                'nullable',
                'date',
                function ($attribute, $value, $fail) {
                    $isPago = $this->input('pago') === '1' || $this->input('pago') === 1;
                    $isRecebido = $this->input('recebido') === '1' || $this->input('recebido') === 1;
                    
                    if (($isPago || $isRecebido) && empty($value)) {
                        $fail('Data de pagamento é obrigatória quando marcado como pago/recebido.');
                    }
                },
            ],
            'juros_pagamento' => 'nullable|numeric|min:0',
            'multa_pagamento' => 'nullable|numeric|min:0',
            'desconto_pagamento' => 'nullable|numeric|min:0',

            // Validações de parcelas (quando parcelamento é 2x ou mais)
            'parcelamento' => 'nullable|string',
            'parcelas' => 'nullable|array',
            'parcelas.*.vencimento' => 'required_with:parcelas|date_format:d/m/Y',
            'parcelas.*.valor' => 'required_with:parcelas|numeric|gt:0',
            'parcelas.*.percentual' => 'required_with:parcelas|numeric|gt:0|max:100',
            'parcelas.*.forma_pagamento_id' => 'nullable|exists:formas_pagamento,id',
            'parcelas.*.conta_pagamento_id' => 'nullable|exists:entidades_financeiras,id',
            'parcelas.*.descricao' => 'required_with:parcelas|string',
            'parcelas.*.agendado' => 'nullable|boolean',
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
            'entidade_banco_id.required_if' => 'O campo Banco de Depósito é obrigatório quando a categoria é "Deposito Bancário".',
            'entidade_id.required' => 'A entidade é obrigatória.',
            'cost_center_id.required' => 'O centro de Custo é obrigatória.',
            'entidade_id.exists' => 'A entidade selecionada não é válida.',
            'data_competencia.required' => 'A data de competência é obrigatória.',
            'descricao.required' => 'A descrição é obrigatória.',
            'valor.required' => 'O valor é obrigatório.',
            'banco_id.required' => 'Selecione um banco.',
            'valor.numeric' => 'O valor deve ser numérico.',
            'valor.gt' => 'O valor deve ser maior que zero.',
            'tipo.required' => 'O tipo é obrigatório.',
            'tipo.in' => 'O tipo deve ser "entrada" ou "saida".',
            'lancamento_padrao_id.exists' => 'O categoria selecionada não é válida.',
            'lancamento_padrao_id.required' => 'A categoria é obrigatória.',
            'files.*.mimes' => 'Os arquivos devem ser do tipo: jpeg, png, jpg ou pdf.',
            'files.*.max' => 'O tamanho máximo do arquivo é 2MB.',
            'intervalo_repeticao.required' => 'O intervalo de repetição é obrigatório quando a recorrência está ativa.',
            'dia_cobranca.required_if' => 'O campo Dia de Cobrança é obrigatório para lançamentos recorrentes.',
            'configuracao_recorrencia.required_if' => 'Selecione uma configuração de recorrência ou crie uma nova.',
            'intervalo_repeticao.integer' => 'O intervalo de repetição deve ser um número inteiro.',
            'intervalo_repeticao.min' => 'O intervalo de repetição deve ser no mínimo 1.',
            'frequencia.required' => 'A frequência é obrigatória quando a recorrência está ativa.',
            'frequencia.in' => 'A frequência deve ser: diario, semanal, mensal ou anual.',
            'apos_ocorrencias.required' => 'O número de ocorrências é obrigatório quando a recorrência está ativa.',
            'apos_ocorrencias.integer' => 'O número de ocorrências deve ser um número inteiro.',
            'apos_ocorrencias.min' => 'O número de ocorrências deve ser no mínimo 1.',
            'apos_ocorrencias.max' => 'O número de ocorrências não pode exceder 366.',
            'data_vencimento.date' => 'A data de vencimento deve ser uma data válida.',
            'vencimento.date_format' => 'A data de vencimento deve estar no formato dd/mm/aaaa.',
            'data_pagamento.required_if' => 'A data de pagamento é obrigatória quando o lançamento está marcado como pago.',
            'data_pagamento.date' => 'A data de pagamento deve ser uma data válida.',
            'valor_pago.required_if' => 'O valor pago é obrigatório quando o lançamento está marcado como pago.',
            'valor_pago.numeric' => 'O valor pago deve ser numérico.',
            'valor_pago.min' => 'O valor pago deve ser maior que zero.',
            'juros.numeric' => 'Os juros devem ser numéricos.',
            'juros.min' => 'Os juros não podem ser negativos.',
            'multa.numeric' => 'A multa deve ser numérica.',
            'multa.min' => 'A multa não pode ser negativa.',
            'desconto.numeric' => 'O desconto deve ser numérico.',
            'desconto.min' => 'O desconto não pode ser negativo.',
            'valor_a_pagar.numeric' => 'O valor a pagar deve ser numérico.',
            'valor_a_pagar.min' => 'O valor a pagar não pode ser negativo.',
            'parcelas.array' => 'As parcelas devem ser enviadas como um array.',
            'parcelas.*.vencimento.required_with' => 'A data de vencimento é obrigatória para cada parcela.',
            'parcelas.*.vencimento.date_format' => 'A data de vencimento da parcela deve estar no formato dd/mm/aaaa.',
            'parcelas.*.valor.required_with' => 'O valor é obrigatório para cada parcela.',
            'parcelas.*.valor.numeric' => 'O valor da parcela deve ser numérico.',
            'parcelas.*.valor.gt' => 'O valor da parcela deve ser maior que zero.',
            'parcelas.*.percentual.required_with' => 'O percentual é obrigatório para cada parcela.',
            'parcelas.*.percentual.numeric' => 'O percentual da parcela deve ser numérico.',
            'parcelas.*.percentual.gt' => 'O percentual da parcela deve ser maior que zero.',
            'parcelas.*.percentual.max' => 'O percentual da parcela não pode ser maior que 100%.',
            'parcelas.*.forma_pagamento_id.exists' => 'A forma de pagamento selecionada não é válida.',
            'parcelas.*.conta_pagamento_id.exists' => 'A conta de pagamento selecionada não é válida.',
            'parcelas.*.descricao.required_with' => 'A descrição é obrigatória para cada parcela.',
        ];
    }

    /**
     * Prepare the data for validation.
     * Converte valores brasileiros (com vírgula) para formato numérico antes da validação.
     */
    protected function prepareForValidation(): void
    {
        // Converte configuracao_recorrencia para inteiro se for numérico
        if ($this->has('configuracao_recorrencia') && $this->configuracao_recorrencia) {
            $value = $this->configuracao_recorrencia;
            // Se for string numérica, converte para int
            if (is_string($value) && ctype_digit($value)) {
                $this->merge(['configuracao_recorrencia' => (int) $value]);
            }
        }
        // Converte o campo 'valor' do formato brasileiro para numérico
        if ($this->has('valor') && $this->valor) {
            $valor = $this->valor;

            // Se for string, remove pontos (milhares) e substitui vírgula por ponto
            if (is_string($valor)) {
                $valor = str_replace('.', '', $valor); // Remove pontos de milhar
                $valor = str_replace(',', '.', $valor); // Substitui vírgula por ponto
            }

            // Converte para float absoluto (remove negatividade) e atualiza o request
            $this->merge([
                'valor' => abs((float) $valor)
            ]);
        }

        // Converte o campo 'valor_pago' se existir
        if ($this->has('valor_pago') && $this->valor_pago) {
            $valorPago = $this->valor_pago;

            if (is_string($valorPago)) {
                $valorPago = str_replace('.', '', $valorPago);
                $valorPago = str_replace(',', '.', $valorPago);
            }

            $this->merge([
                'valor_pago' => abs((float) $valorPago)
            ]);
        }

        // Converte o campo 'juros' se existir
        if ($this->has('juros') && $this->juros) {
            $juros = $this->juros;

            if (is_string($juros)) {
                $juros = str_replace('.', '', $juros);
                $juros = str_replace(',', '.', $juros);
            }

            $this->merge([
                'juros' => abs((float) $juros)
            ]);
        }

        // Converte o campo 'multa' se existir
        if ($this->has('multa') && $this->multa) {
            $multa = $this->multa;

            if (is_string($multa)) {
                $multa = str_replace('.', '', $multa);
                $multa = str_replace(',', '.', $multa);
            }

            $this->merge([
                'multa' => abs((float) $multa)
            ]);
        }

        // Converte o campo 'desconto' se existir
        if ($this->has('desconto') && $this->desconto) {
            $desconto = $this->desconto;

            if (is_string($desconto)) {
                $desconto = str_replace('.', '', $desconto);
                $desconto = str_replace(',', '.', $desconto);
            }

            $this->merge([
                'desconto' => abs((float) $desconto)
            ]);
        }

        // Converte os campos de pagamento (para lançamentos fracionados)
        $camposPagamento = ['juros_pagamento', 'multa_pagamento', 'desconto_pagamento'];

        foreach ($camposPagamento as $campo) {
            if ($this->has($campo) && $this->input($campo)) {
                $valor = $this->input($campo);

                if (is_string($valor)) {
                    $valor = str_replace('.', '', $valor);
                    $valor = str_replace(',', '.', $valor);
                }

                $this->merge([
                    $campo => abs((float) $valor)
                ]);
            }
        }

        // Converte o campo 'valor_a_pagar' se existir
        if ($this->has('valor_a_pagar') && $this->valor_a_pagar) {
            $valorAPagar = $this->valor_a_pagar;

            if (is_string($valorAPagar)) {
                $valorAPagar = str_replace('.', '', $valorAPagar);
                $valorAPagar = str_replace(',', '.', $valorAPagar);
            }

            $this->merge([
                'valor_a_pagar' => abs((float) $valorAPagar)
            ]);
        }

        // Processa valores das parcelas - converte do formato brasileiro para numérico
        if ($this->has('parcelas') && is_array($this->parcelas)) {
            $parcelasProcessadas = [];
            foreach ($this->parcelas as $index => $parcela) {
                $parcelaProcessada = $parcela;

                // Converte valor se existir
                if (isset($parcela['valor'])) {
                    if (is_string($parcela['valor'])) {
                        // Se contém vírgula, é formato brasileiro (1.500,00) - converte
                        if (strpos($parcela['valor'], ',') !== false) {
                            $valor = str_replace('.', '', $parcela['valor']); // Remove pontos de milhar
                            $valor = str_replace(',', '.', $valor); // Substitui vírgula por ponto
                            $parcelaProcessada['valor'] = abs((float) $valor);
                        } else {
                            // Se já está com ponto decimal (1500.00), apenas converte para float absoluto
                            $parcelaProcessada['valor'] = abs((float) $parcela['valor']);
                        }
                    } elseif (is_numeric($parcela['valor'])) {
                        $parcelaProcessada['valor'] = abs((float) $parcela['valor']);
                    }
                }

                // Converte percentual se existir
                if (isset($parcela['percentual'])) {
                    if (is_string($parcela['percentual'])) {
                        // Se contém vírgula, é formato brasileiro (25,00) - converte
                        if (strpos($parcela['percentual'], ',') !== false) {
                            $percentual = str_replace('.', '', $parcela['percentual']); // Remove pontos de milhar
                            $percentual = str_replace(',', '.', $percentual); // Substitui vírgula por ponto
                            $parcelaProcessada['percentual'] = abs((float) $percentual);
                        } else {
                            // Se já está com ponto decimal (25.00), apenas converte para float absoluto
                            $parcelaProcessada['percentual'] = abs((float) $parcela['percentual']);
                        }
                    } elseif (is_numeric($parcela['percentual'])) {
                        $parcelaProcessada['percentual'] = abs((float) $parcela['percentual']);
                    }
                }

                // Limpa e normaliza a data de vencimento (remove espaços, garante formato d/m/Y)
                if (isset($parcela['vencimento']) && $parcela['vencimento']) {
                    $vencimento = trim($parcela['vencimento']);
                    // Garante que está no formato d/m/Y (remove espaços extras, normaliza)
                    $vencimento = preg_replace('/\s+/', '', $vencimento);
                    // Se tem hífen, pode estar em formato Y-m-d, não faz nada (será validado)
                    // Se tem barra, mantém como está para validação
                    $parcelaProcessada['vencimento'] = $vencimento;
                }

                $parcelasProcessadas[$index] = $parcelaProcessada;
            }

            $this->merge([
                'parcelas' => $parcelasProcessadas
            ]);
        }

        // Processa data_vencimento se vier como 'vencimento' do formulário
        if ($this->has('vencimento') && $this->vencimento && !$this->has('data_vencimento')) {
            $vencimento = $this->vencimento;

            // Se vier no formato brasileiro (d/m/Y), converte para Y-m-d
            if (strpos($vencimento, '/') !== false) {
                try {
                    $dataVencimento = \Carbon\Carbon::createFromFormat('d/m/Y', $vencimento)->format('Y-m-d');
                    $this->merge([
                        'data_vencimento' => $dataVencimento
                    ]);
                } catch (\Exception $e) {
                    // Se falhar, mantém o valor original
                }
            } else {
                $this->merge([
                    'data_vencimento' => $vencimento
                ]);
            }
        }

        // Processa data_competencia se vier no formato brasileiro
        if ($this->has('data_competencia') && $this->data_competencia) {
            $dataCompetencia = $this->data_competencia;

            // Se vier no formato brasileiro (d/m/Y), converte para Y-m-d
            if (strpos($dataCompetencia, '/') !== false) {
                try {
                    $dataCompetenciaConvertida = \Carbon\Carbon::createFromFormat('d/m/Y', $dataCompetencia)->format('Y-m-d');
                    $this->merge([
                        'data_competencia' => $dataCompetenciaConvertida
                    ]);
                } catch (\Exception $e) {
                    // Se falhar, mantém o valor original e deixa a validação pegar
                }
            }
        }

        // Processa data_pagamento se vier no formato brasileiro
        if ($this->has('data_pagamento') && $this->data_pagamento) {
            $dataPagamento = $this->data_pagamento;

            // Se vier no formato brasileiro (d/m/Y), converte para Y-m-d
            if (strpos($dataPagamento, '/') !== false) {
                try {
                    $dataPagamentoConvertida = \Carbon\Carbon::createFromFormat('d/m/Y', $dataPagamento)->format('Y-m-d');
                    $this->merge([
                        'data_pagamento' => $dataPagamentoConvertida
                    ]);
                } catch (\Exception $e) {
                    // Se falhar, mantém o valor original
                }
            }
        }

    }
}
