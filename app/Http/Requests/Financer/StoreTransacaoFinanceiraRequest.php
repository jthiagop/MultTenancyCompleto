<?php

namespace App\Http\Requests\Financer;

use App\Models\LancamentoPadrao;
use App\Models\Parceiro;
use App\Support\Money;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

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
     * Handle a failed validation attempt.
     * Loga os erros de validação para debug.
     */
    protected function failedValidation(Validator $validator)
    {
        Log::warning('[StoreTransacaoFinanceiraRequest] Validação falhou', [
            'errors' => $validator->errors()->all(),
            'input_keys' => array_keys($this->all()),
            'parcelamento' => $this->input('parcelamento'),
            'parcelas_count' => is_array($this->input('parcelas')) ? count($this->input('parcelas')) : 0,
        ]);

        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Erro de validação',
                'errors' => $validator->errors()
            ], 422)
        );
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
        $lancamentoPadraoDepositoId = LancamentoPadrao::forActiveCompany()
            ->where('description', 'Deposito Bancário')
            ->value('id');

            //dd($_REQUEST);

        return [
            'data_competencia' => 'required',
            'descricao' => 'required|string',
            'descricao2' => 'string',
            'valor' => 'required|numeric|gt:0',  // Em DECIMAL (ex: 1991.44)
            'tipo' => 'required|in:entrada,saida',
            'lancamento_padrao_id' => 'required|exists:lancamento_padraos,id',
            'cost_center_id' => 'nullable|string',
            'origem' => 'required|string',
            'tipo_documento' => 'required|string',
            'numero_documento' => 'nullable|string',
            'files.*' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'historico_complementar' => 'nullable|string|max:500',
            'comprovacao_fiscal' => 'nullable|boolean', // 0 ou 1, default false
            'entidade_id' => 'required|exists:entidades_financeiras,id',
            'fornecedor_id' => 'nullable|exists:parceiros,id', // Alias - mapeado para parceiro_id no prepareForValidation
            'parceiro_id' => 'nullable|exists:parceiros,id',
            'novo_parceiro_nome' => 'nullable|string|max:255', // Auto-cadastro via Domus IA
            'novo_parceiro_cnpj' => 'nullable|string|max:20',  // Auto-cadastro via Domus IA
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
            'repetir_lancamento' => 'nullable|boolean',
            'intervalo_repeticao' => 'nullable|integer|min:1',
            'frequencia' => 'nullable|in:diario,semanal,mensal,anual',
            'apos_ocorrencias' => 'nullable|integer|min:1|max:366',
            'dia_cobranca' => 'required_if:repetir_lancamento,1',
            'configuracao_recorrencia' => [
                'nullable',
                'required_if:repetir_lancamento,1',
                function ($attribute, $value, $fail) {
                    if (empty($value) || $value === '' || $value === null) {
                        return;
                    }

                    // Modal Blade: opção provisória "temp_" (intervalo/frequência vêm em campos separados)
                    if (is_string($value) && strpos($value, 'temp_') === 0) {
                        return;
                    }

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
            // React envia Y-m-d em data_vencimento; Blade usa d/m/Y em "vencimento" — aceita qualquer data válida
            'vencimento' => 'required_if:repetir_lancamento,1|nullable|date',
            'data_vencimento' => 'nullable|date', // Campo processado
            'previsao_pagamento' => 'nullable|date',
            'valor_pago' => 'nullable|numeric|min:0',  // Em DECIMAL (ex: 1991.44)
            'juros' => 'nullable|numeric|min:0',  // Em DECIMAL (ex: 1991.44)
            'multa' => 'nullable|numeric|min:0',  // Em DECIMAL (ex: 1991.44)
            'desconto' => 'nullable|numeric|min:0',  // Em DECIMAL (ex: 1991.44)
            'valor_a_pagar' => 'nullable|numeric|min:0',  // Em DECIMAL (ex: 1991.44)
            'situacao' => 'nullable|in:em_aberto,desconsiderado,atrasado,pago_parcial,pago,recebido,previsto,parcelado',
            'pago' => 'nullable|boolean',
            'recebido' => 'nullable|boolean',
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
            'juros_pagamento' => 'nullable|numeric|min:0',  // Em DECIMAL (ex: 1991.44)
            'multa_pagamento' => 'nullable|numeric|min:0',  // Em DECIMAL (ex: 1991.44)
            'desconto_pagamento' => 'nullable|numeric|min:0',  // Em DECIMAL (ex: 1991.44)

            // Validações de rateio
            'rateios' => 'nullable|array',
            'rateios.*.filial_id' => 'required|exists:companies,id',
            'rateios.*.centro_custo_id' => 'nullable|exists:cost_centers,id',
            'rateios.*.lancamento_padrao_id' => 'nullable|exists:lancamento_padraos,id',
            'rateios.*.valor' => 'required|numeric|min:0.01',
            'rateios.*.percentual' => 'required|numeric|min:0|max:100',

            // Validações de parcelas (quando parcelamento é 2x ou mais)
            // Obrigatórios: vencimento, valor
            // Opcionais: percentual (calculado), descricao (gerada), agendado
            // Nota: entidade_id vem do formulário principal, não por parcela
            'parcelamento' => 'nullable|string',
            'intervalo_parcelas_dias' => 'nullable|integer|min:1|max:366',
            'parcelas' => 'nullable|array',
            'parcelas.*.vencimento' => 'required_with:parcelas|date_format:Y-m-d',
            'parcelas.*.valor' => 'required_with:parcelas|numeric|gt:0',  // Em DECIMAL (ex: 1991.44)
            'parcelas.*.percentual' => 'nullable|numeric|min:0|max:100',  // Calculado automaticamente se não informado
            'parcelas.*.forma_pagamento_id' => 'nullable|exists:entidades_financeiras,id',
            'parcelas.*.conta_pagamento_id' => 'nullable|exists:entidades_financeiras,id',
            'parcelas.*.descricao' => 'nullable|string|max:255',  // Gerada automaticamente se não informada
            'parcelas.*.agendado' => 'nullable|boolean',

            'domus_documento_id' => [
                'nullable',
                'integer',
                Rule::exists('domus_documentos', 'id')->where(function ($query) {
                    $query->whereNull('deleted_at');
                    $companyId = session('active_company_id');

                    return $companyId
                        ? $query->where('company_id', $companyId)
                        : $query->whereRaw('1 = 0');
                }),
            ],
        ];
    }

    /**
     * Validações customizadas adicionais (ex: soma de rateios).
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $repetirAtivo = (int) $this->input('repetir_lancamento') === 1;
            if ($repetirAtivo) {
                $cfg = $this->input('configuracao_recorrencia');
                $isTemp = is_string($cfg) && strpos($cfg, 'temp_') === 0;
                if ($isTemp) {
                    $hasInline = $this->filled('intervalo_repeticao')
                        && $this->filled('frequencia')
                        && $this->filled('apos_ocorrencias');
                    if (! $hasInline) {
                        $validator->errors()->add(
                            'configuracao_recorrencia',
                            'Informe intervalo, frequência e número de ocorrências para a nova configuração.'
                        );
                    }
                }
            }

            $rateios = $this->input('rateios');
            if (!is_array($rateios) || empty($rateios)) {
                return;
            }

            $somaRateios = collect($rateios)->sum(fn ($r) => (float) ($r['valor'] ?? 0));
            $valorTotal = (float) $this->input('valor', 0);
            $diff = abs($somaRateios - $valorTotal);

            if ($diff > 0.01) {
                $validator->errors()->add(
                    'rateios',
                    "A soma dos rateios (R$ " . number_format($somaRateios, 2, ',', '.') . ") deve ser igual ao valor total (R$ " . number_format($valorTotal, 2, ',', '.') . ")."
                );
            }

            $parcelamento = (string) $this->input('parcelamento', '');
            if (preg_match('/^(\d+)x$/', $parcelamento, $m) && (int) $m[1] >= 2) {
                $expected = (int) $m[1];
                $parcelas = $this->input('parcelas');
                if (! is_array($parcelas)) {
                    $validator->errors()->add('parcelas', 'Informe as linhas de parcelas para este parcelamento.');

                    return;
                }
                $count = count($parcelas);
                if ($count !== $expected) {
                    $validator->errors()->add(
                        'parcelas',
                        "Para {$parcelamento} são necessárias exatamente {$expected} parcelas (enviadas: {$count})."
                    );
                }
                $valorLancamento = (float) $this->input('valor', 0);
                $somaParcelas = 0.0;
                foreach ($parcelas as $p) {
                    $somaParcelas += (float) ($p['valor'] ?? 0);
                }
                if ($count > 0 && abs($somaParcelas - $valorLancamento) > 0.02) {
                    $validator->errors()->add(
                        'parcelas',
                        'A soma dos valores das parcelas deve ser igual ao valor total do lançamento.'
                    );
                }
            }
        });
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
            'configuracao_recorrencia.required_if' => 'Selecione uma configuração de recorrência quando "Repetir lançamento" estiver ativo.',
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
            'parcelas.*.vencimento.date_format' => 'A data de vencimento da parcela deve ser uma data válida.',
            'parcelas.*.valor.required_with' => 'O valor é obrigatório para cada parcela.',
            'parcelas.*.valor.numeric' => 'O valor da parcela deve ser numérico.',
            'parcelas.*.valor.gt' => 'O valor da parcela deve ser maior que zero.',
            'parcelas.*.percentual.numeric' => 'O percentual da parcela deve ser numérico.',
            'parcelas.*.percentual.min' => 'O percentual da parcela não pode ser negativo.',
            'parcelas.*.percentual.max' => 'O percentual da parcela não pode ser maior que 100%.',
            'parcelas.*.forma_pagamento_id.required_with' => 'A forma de pagamento é obrigatória para cada parcela.',
            'parcelas.*.forma_pagamento_id.exists' => 'A forma de pagamento selecionada não é válida.',
            'parcelas.*.conta_pagamento_id.exists' => 'A conta de pagamento selecionada não é válida.',
            'parcelas.*.descricao.max' => 'A descrição da parcela não pode ter mais que 255 caracteres.',
            'rateios.*.filial_id.required' => 'A filial é obrigatória em cada linha de rateio.',
            'rateios.*.filial_id.exists' => 'A filial selecionada não é válida.',
            'rateios.*.valor.required' => 'O valor é obrigatório em cada linha de rateio.',
            'rateios.*.valor.numeric' => 'O valor do rateio deve ser numérico.',
            'rateios.*.valor.min' => 'O valor do rateio deve ser maior que zero.',
            'rateios.*.percentual.required' => 'O percentual é obrigatório em cada linha de rateio.',
            'rateios.*.percentual.numeric' => 'O percentual do rateio deve ser numérico.',
            'rateios.*.percentual.min' => 'O percentual do rateio não pode ser negativo.',
            'rateios.*.percentual.max' => 'O percentual do rateio não pode ser maior que 100%.',
        ];
    }

    /**
     * Prepare the data for validation.
     * Converte valores brasileiros (com vírgula) para centavos (inteiro) antes da validação.
     * 
     * Agora usa a classe Money para centralizar toda a lógica de conversão.
     * Exemplo: "1.234,56" → 123456 (centavos)
     */
    protected function prepareForValidation(): void
    {
        // Drawer React (JSON): receita/despesa → entrada/saida
        if ($this->input('tipo') === 'receita') {
            $this->merge(['tipo' => 'entrada']);
        } elseif ($this->input('tipo') === 'despesa') {
            $this->merge(['tipo' => 'saida']);
        }

        if ($this->has('recebido_pago')) {
            $rp = filter_var($this->input('recebido_pago'), FILTER_VALIDATE_BOOLEAN);
            if ($rp) {
                $t = $this->input('tipo');
                if ($t === 'entrada') {
                    $this->merge(['recebido' => true]);
                } elseif ($t === 'saida') {
                    $this->merge(['pago' => true]);
                }
            }
        }

        if (! $this->filled('origem')) {
            $this->merge(['origem' => 'Banco']);
        }

        $rep = $this->input('repetir_lancamento');
        if ($rep === true || $rep === 1 || $rep === '1') {
            $this->merge(['repetir_lancamento' => 1]);
        }

        // Converte configuracao_recorrencia para inteiro se for numérico
        if ($this->has('configuracao_recorrencia') && $this->configuracao_recorrencia) {
            $value = $this->configuracao_recorrencia;
            if (is_string($value) && ctype_digit($value)) {
                $this->merge(['configuracao_recorrencia' => (int) $value]);
            }
        }

        // Campos monetários em DECIMAL - usa Money para conversão
        // IMPORTANTE: Banco usa DECIMAL, não INTEGER (centavos)
        // Usa toDatabase() para retornar float (ex: 1991.44) ao invés de toCents()
        $camposMonetarios = [
            'valor',
            'valor_pago',
            'juros',
            'multa',
            'desconto',
            'valor_a_pagar'
        ];

        foreach ($camposMonetarios as $campo) {
            if ($this->has($campo) && $this->input($campo) !== null) {
                $valorInput = $this->input($campo);
                // Usa Money::fromHumanInput para converter formato brasileiro → DECIMAL
                $money = Money::fromHumanInput((string) $valorInput);
                $this->merge([
                    $campo => $money->toDatabase() // Retorna float (1991.44), não centavos
                ]);
            }
        }

        // Campos de pagamento (fracionado) em DECIMAL - usa Money para conversão
        $camposPagamento = ['juros_pagamento', 'multa_pagamento', 'desconto_pagamento'];

        foreach ($camposPagamento as $campo) {
            if ($this->has($campo) && $this->input($campo) !== null) {
                $valorInput = $this->input($campo);
                // Usa Money::fromHumanInput para converter formato brasileiro → DECIMAL
                $money = Money::fromHumanInput((string) $valorInput);
                $this->merge([
                    $campo => $money->toDatabase() // Retorna float (1991.44), não centavos
                ]);
            }
        }

        // Processa parcelas - converte valores para DECIMAL usando Money
        if ($this->has('parcelas') && is_array($this->parcelas)) {
            $parcelasProcessadas = [];
            
            foreach ($this->parcelas as $index => $parcela) {
                $parcelaProcessada = $parcela;

                // Converte valor em reais para DECIMAL usando Money
                if (isset($parcela['valor'])) {
                    $money = Money::fromHumanInput((string) $parcela['valor']);
                    $parcelaProcessada['valor'] = $money->toDatabase(); // Retorna float (1991.44), não centavos
                }

                // Percentual continua em numeric (não é dinheiro)
                // A máscara usa radixPoint: "." então o valor vem como "50.00" (formato americano)
                if (isset($parcela['percentual']) && $parcela['percentual'] !== '') {
                    $percentual = (string) $parcela['percentual'];
                    
                    // Se contém vírgula, é formato brasileiro (50,00)
                    if (strpos($percentual, ',') !== false) {
                        $percentual = str_replace('.', '', $percentual); // Remove milhares
                        $percentual = str_replace(',', '.', $percentual); // Vírgula → ponto
                    }
                    // Se contém apenas ponto, já está no formato correto (50.00)
                    
                    $parcelaProcessada['percentual'] = (float) $percentual;
                }

                // Normaliza data de vencimento da parcela → Y-m-d (React pode enviar dd/mm/aaaa ou ISO)
                if (isset($parcela['vencimento']) && $parcela['vencimento']) {
                    $vencimento = trim(preg_replace('/\s+/', '', (string) $parcela['vencimento']));
                    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $vencimento)) {
                        $parcelaProcessada['vencimento'] = $vencimento;
                    } elseif (strpos($vencimento, '/') !== false) {
                        try {
                            $parcelaProcessada['vencimento'] = \Carbon\Carbon::createFromFormat('d/m/Y', $vencimento)->format('Y-m-d');
                        } catch (\Exception $e) {
                            $parcelaProcessada['vencimento'] = $vencimento;
                        }
                    } else {
                        $parcelaProcessada['vencimento'] = $vencimento;
                    }
                }

                $parcelasProcessadas[$index] = $parcelaProcessada;
            }

            $this->merge(['parcelas' => $parcelasProcessadas]);
        }

        // Processa rateios - converte valores para DECIMAL usando Money
        if ($this->has('rateios') && is_array($this->rateios)) {
            $rateiosProcessados = [];

            foreach ($this->rateios as $index => $rateio) {
                $r = $rateio;

                if (isset($rateio['valor'])) {
                    $money = Money::fromHumanInput((string) $rateio['valor']);
                    $r['valor'] = $money->toDatabase();
                }

                if (isset($rateio['percentual']) && $rateio['percentual'] !== '') {
                    $pct = (string) $rateio['percentual'];
                    if (strpos($pct, ',') !== false) {
                        $pct = str_replace('.', '', $pct);
                        $pct = str_replace(',', '.', $pct);
                    }
                    $r['percentual'] = (float) $pct;
                }

                $rateiosProcessados[$index] = $r;
            }

            $this->merge(['rateios' => $rateiosProcessados]);
        }

        // Processa data_vencimento se vier como 'vencimento' do formulário
        if ($this->has('vencimento') && $this->vencimento && !$this->has('data_vencimento')) {
            $vencimento = $this->vencimento;

            if (strpos($vencimento, '/') !== false) {
                try {
                    $dataVencimento = \Carbon\Carbon::createFromFormat('d/m/Y', $vencimento)->format('Y-m-d');
                    $this->merge(['data_vencimento' => $dataVencimento]);
                } catch (\Exception $e) {
                    // Se falhar, mantém o valor original
                }
            } else {
                $this->merge(['data_vencimento' => $vencimento]);
            }
        }

        if ($this->filled('previsao_pagamento')) {
            $p = $this->input('previsao_pagamento');
            if (is_string($p) && strpos($p, '/') !== false) {
                try {
                    $this->merge([
                        'previsao_pagamento' => \Carbon\Carbon::createFromFormat('d/m/Y', $p)->format('Y-m-d'),
                    ]);
                } catch (\Exception $e) {
                    // mantém valor original para a validação reportar erro
                }
            }
        }

        // Drawer React: cost_center vazio → null (coluna opcional)
        if ($this->has('cost_center_id') && $this->input('cost_center_id') === '') {
            $this->merge(['cost_center_id' => null]);
        }

        // API React: só data_vencimento (Y-m-d); regra required_if usa "vencimento"
        if ((int) $this->input('repetir_lancamento') === 1 && !$this->filled('vencimento') && $this->filled('data_vencimento')) {
            $this->merge(['vencimento' => $this->input('data_vencimento')]);
        }

        // Processa data_competencia se vier no formato brasileiro
        if ($this->has('data_competencia') && $this->data_competencia) {
            $dataCompetencia = $this->data_competencia;

            if (strpos($dataCompetencia, '/') !== false) {
                try {
                    $dataCompetenciaConvertida = \Carbon\Carbon::createFromFormat('d/m/Y', $dataCompetencia)->format('Y-m-d');
                    $this->merge(['data_competencia' => $dataCompetenciaConvertida]);
                } catch (\Exception $e) {
                    // Se falhar, mantém o valor original
                }
            }
        }

        // ✅ Auto-cadastro de parceiro via Domus IA
        // Se fornecedor_id === '__novo__' e os hidden fields estão preenchidos,
        // cria o parceiro automaticamente e substitui pelo ID real
        // Drawer React envia `parceiro_id`; Blade Domus IA envia `fornecedor_id` — ambos aceitam __novo__
        $isNovoParceiro = ($this->input('fornecedor_id') === '__novo__' || $this->input('parceiro_id') === '__novo__')
            && $this->filled('novo_parceiro_cnpj');

        if ($isNovoParceiro) {
            $parceiroId = $this->autoCreateParceiro();
            if ($parceiroId) {
                $this->merge([
                    'fornecedor_id' => $parceiroId,
                    'parceiro_id' => $parceiroId,
                    'novo_parceiro_nome' => null,
                    'novo_parceiro_cnpj' => null,
                ]);
            } else {
                $this->merge(['fornecedor_id' => null, 'parceiro_id' => null]);
            }
        }

        // ✅ Mapeia fornecedor_id → parceiro_id (nome usado pelo formulário vs nome da coluna no banco)
        if ($this->has('fornecedor_id') && !$this->has('parceiro_id')) {
            $this->merge(['parceiro_id' => $this->input('fornecedor_id')]);
        }

        // ✅ Calcula situação baseada nos checkboxes pago/recebido
        // O checkbox envia 'pago=1' ou 'recebido=1', mas o controller espera 'situacao'
        if (!$this->has('situacao') || empty($this->input('situacao'))) {
            $tipo = $this->input('tipo');
            $isPago = $this->input('pago') === '1' || $this->input('pago') === 1 || $this->input('pago') === true;
            $isRecebido = $this->input('recebido') === '1' || $this->input('recebido') === 1 || $this->input('recebido') === true;

            if ($tipo === 'saida' && $isPago) {
                $this->merge(['situacao' => 'pago']);
            } elseif ($tipo === 'entrada' && $isRecebido) {
                $this->merge(['situacao' => 'recebido']);
            } else {
                $this->merge(['situacao' => 'em_aberto']);
            }

            // ✅ Se marcou como pago/recebido, preencher data_pagamento e valor_pago se ausentes
            if ($isPago || $isRecebido) {
                // data_pagamento: fallback para data_competencia ou hoje
                if (!$this->filled('data_pagamento')) {
                    $dataFallback = $this->input('data_competencia') ?? now()->format('Y-m-d');
                    $this->merge(['data_pagamento' => $dataFallback]);
                }
                
                // valor_pago: fallback para valor + juros + multa - desconto
                if (!$this->filled('valor_pago')) {
                    $valor = (float) ($this->input('valor') ?? 0);
                    $juros = (float) ($this->input('juros') ?? 0);
                    $multa = (float) ($this->input('multa') ?? 0);
                    $desconto = (float) ($this->input('desconto') ?? 0);
                    $this->merge(['valor_pago' => max(0, $valor + $juros + $multa - $desconto)]);
                }
            }

            Log::info('[StoreTransacaoFinanceiraRequest] Situação calculada', [
                'tipo' => $tipo,
                'pago' => $this->input('pago'),
                'recebido' => $this->input('recebido'),
                'situacao_calculada' => $this->input('situacao'),
                'data_pagamento' => $this->input('data_pagamento'),
                'valor_pago' => $this->input('valor_pago'),
            ]);
        }

        // Processa data_pagamento se vier no formato brasileiro
        if ($this->has('data_pagamento') && $this->data_pagamento) {
            $dataPagamento = $this->data_pagamento;

            if (strpos($dataPagamento, '/') !== false) {
                try {
                    $dataPagamentoConvertida = \Carbon\Carbon::createFromFormat('d/m/Y', $dataPagamento)->format('Y-m-d');
                    $this->merge(['data_pagamento' => $dataPagamentoConvertida]);
                } catch (\Exception $e) {
                    // Se falhar, mantém o valor original
                }
            }
        }
    }

    /**
     * Auto-cadastro de parceiro a partir dos dados extraídos pela IA (Domus IA).
     * Verifica se o CNPJ já existe para evitar duplicatas. Se já existir, retorna o ID existente.
     * Se não existir, cria um novo parceiro com os dados mínimos.
     */
    private function autoCreateParceiro(): ?int
    {
        $cnpj = preg_replace('/\D/', '', $this->input('novo_parceiro_cnpj', ''));
        $nome = trim($this->input('novo_parceiro_nome', ''));

        if (empty($cnpj) || empty($nome)) {
            return null;
        }

        $companyId = session('active_company_id');
        if (!$companyId) {
            Log::warning('[AutoCreateParceiro] company_id não encontrado na sessão');
            return null;
        }

        try {
            // Verificar se CNPJ já existe na empresa (pode ter sido cadastrado entre o carregamento da tela e o submit)
            $existente = Parceiro::where('company_id', $companyId)
                ->where('cnpj', $cnpj)
                ->whereNull('deleted_at')
                ->first();

            if ($existente) {
                Log::info('[AutoCreateParceiro] Parceiro já existe, usando ID existente', [
                    'parceiro_id' => $existente->id,
                    'nome' => $existente->nome,
                    'cnpj' => $cnpj,
                ]);
                return $existente->id;
            }

            // Determinar natureza baseada no tipo da transação
            $tipo = $this->input('tipo');
            $natureza = ($tipo === 'entrada') ? 'cliente' : 'fornecedor';

            // Criar novo parceiro com dados mínimos da IA
            $parceiro = Parceiro::create([
                'company_id' => $companyId,
                'nome' => $nome,
                'cnpj' => $cnpj,
                'tipo' => 'pj',
                'natureza' => $natureza,
                'active' => true,
                'created_by' => Auth::id(),
                'created_by_name' => Auth::user()?->name,
                'updated_by' => Auth::id(),
                'updated_by_name' => Auth::user()?->name,
            ]);

            Log::info('[AutoCreateParceiro] Novo parceiro criado automaticamente via Domus IA', [
                'parceiro_id' => $parceiro->id,
                'nome' => $nome,
                'cnpj' => $cnpj,
                'natureza' => $natureza,
            ]);

            return $parceiro->id;

        } catch (\Exception $e) {
            Log::error('[AutoCreateParceiro] Erro ao criar parceiro: ' . $e->getMessage(), [
                'nome' => $nome,
                'cnpj' => $cnpj,
            ]);
            return null;
        }
    }
}
