<?php

namespace App\Services;

use App\Models\Financeiro\BankStatement;
use App\Models\Financeiro\TransacaoFinanceira;
use App\Models\HorarioMissa;
use App\Models\LancamentoPadrao;
use App\Models\Movimentacao;
use App\Support\Money;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ConciliacaoMissasService
{
    /**
     * Processa transações para conciliação com missas
     * 
     * @param int $companyId
     * @param \Illuminate\Database\Eloquent\Collection|null $bankStatements Se null, processa todas as transações não conciliadas
     * @return array Estatísticas do processamento
     */
    public function processarTransacoes($companyId, $bankStatements = null)
    {
        try {
            DB::beginTransaction();

            // Se não foram passadas transações específicas, busca todas as não conciliadas
            if ($bankStatements === null) {
                $bankStatements = BankStatement::where('company_id', $companyId)
                    ->where('conciliado_com_missa', false)
                    ->where('amount', '>', 0)
                    ->get();
            }

            $transacoesRelevantes = $this->filtrarTransacoesRelevantes($bankStatements);
            
            $estatisticas = [
                'total_processadas' => count($transacoesRelevantes),
                'conciliadas' => 0,
                'valor_total' => 0,
                'missas_envolvidas' => 0,
            ];

            $missasEnvolvidas = [];

            foreach ($transacoesRelevantes as $bankStatement) {
                // Extrai data/hora da transação
                $transactionDatetime = $this->extrairDataHoraDoMemo($bankStatement->memo, $bankStatement->dtposted);
                
                Log::debug('Processando transação para conciliação', [
                    'bank_statement_id' => $bankStatement->id,
                    'memo' => $bankStatement->memo,
                    'dtposted' => $bankStatement->dtposted,
                    'transaction_datetime' => $transactionDatetime['datetime']->format('Y-m-d H:i:s'),
                    'source' => $transactionDatetime['source']
                ]);
                
                // Atualiza o bank_statement com o datetime calculado
                $bankStatement->transaction_datetime = $transactionDatetime['datetime'];
                $bankStatement->source_time = $transactionDatetime['source'];
                $bankStatement->save();

                // Busca missas correspondentes
                $missaCorrespondente = $this->encontrarMissasCorrespondentes(
                    $transactionDatetime['datetime'],
                    $companyId
                );

                if ($missaCorrespondente) {
                    Log::info('Missa correspondente encontrada', [
                        'bank_statement_id' => $bankStatement->id,
                        'horario_missa_id' => $missaCorrespondente->id,
                        'dia_semana' => $missaCorrespondente->dia_semana,
                        'horario' => $missaCorrespondente->horario,
                        'intervalo' => $missaCorrespondente->intervalo
                    ]);
                    
                    // Cria lançamento financeiro
                    $transacaoFinanceira = $this->criarLancamentoFinanceiro($bankStatement, $missaCorrespondente);
                    
                    if ($transacaoFinanceira) {
                        // Atualiza flags de conciliação
                        $bankStatement->conciliado_com_missa = true;
                        $bankStatement->horario_missa_id = $missaCorrespondente->id;
                        $bankStatement->save();

                        // Vincula BankStatement com TransacaoFinanceira
                        $bankStatement->transacoes()->attach($transacaoFinanceira->id, [
                            'valor_conciliado' => $bankStatement->amount,
                            'status_conciliacao' => 'conciliado'
                        ]);

                        $estatisticas['conciliadas']++;
                        $estatisticas['valor_total'] += $bankStatement->amount;
                        
                        if (!in_array($missaCorrespondente->id, $missasEnvolvidas)) {
                            $missasEnvolvidas[] = $missaCorrespondente->id;
                        }
                    } else {
                        Log::warning('Falha ao criar lançamento financeiro', [
                            'bank_statement_id' => $bankStatement->id,
                            'horario_missa_id' => $missaCorrespondente->id
                        ]);
                    }
                } else {
                    Log::debug('Nenhuma missa correspondente encontrada', [
                        'bank_statement_id' => $bankStatement->id,
                        'transaction_datetime' => $transactionDatetime['datetime']->format('Y-m-d H:i:s'),
                        'dia_semana' => $this->calcularDiaSemana($transactionDatetime['datetime'])
                    ]);
                }
            }

            $estatisticas['missas_envolvidas'] = count($missasEnvolvidas);

            DB::commit();

            Log::info('Conciliação de missas processada', [
                'company_id' => $companyId,
                'estatisticas' => $estatisticas
            ]);

            return $estatisticas;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao processar conciliação de missas', [
                'company_id' => $companyId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Extrai data e horário do MEMO usando regex
     * 
     * @param string|null $memo
     * @param string $dtposted Data do OFX no formato Y-m-d ou Ymd
     * @return array ['datetime' => Carbon, 'source' => 'memo'|'dtposted']
     */
    public function extrairDataHoraDoMemo($memo, $dtposted)
    {
        // Parse do DTPOSTED (pode vir como Ymd ou Y-m-d H:i:s)
        try {
            // Tenta parse direto primeiro
            if (strlen($dtposted) === 8 && ctype_digit($dtposted)) {
                // Formato Ymd (ex: 20250107)
                $dtpostedCarbon = Carbon::createFromFormat('Ymd', $dtposted)->setTimezone('America/Sao_Paulo');
            } else {
                // Formato padrão Y-m-d H:i:s
                $dtpostedCarbon = Carbon::parse($dtposted)->setTimezone('America/Sao_Paulo');
            }
        } catch (\Exception $e) {
            Log::warning('Erro ao fazer parse do DTPOSTED', [
                'dtposted' => $dtposted,
                'error' => $e->getMessage()
            ]);
            // Fallback: tenta parse padrão
            $dtpostedCarbon = Carbon::now('America/Sao_Paulo');
        }
        
        // Tenta extrair data/hora do MEMO: padrão DD/MM HH:MM
        if ($memo && preg_match('/(\d{2})\/(\d{2})\s+(\d{2}):(\d{2})/', $memo, $matches)) {
            $dia = (int) $matches[1];
            $mes = (int) $matches[2];
            $hora = (int) $matches[3];
            $minuto = (int) $matches[4];
            
            // Validação básica dos valores extraídos
            if ($dia < 1 || $dia > 31 || $mes < 1 || $mes > 12 || 
                $hora < 0 || $hora > 23 || $minuto < 0 || $minuto > 59) {
                Log::warning('Valores inválidos extraídos do MEMO', [
                    'memo' => $memo,
                    'dia' => $dia,
                    'mes' => $mes,
                    'hora' => $hora,
                    'minuto' => $minuto
                ]);
            } else {
                try {
                    // Tenta criar data com o ano do DTPOSTED
                    $dataMemo = Carbon::create(
                        $dtpostedCarbon->year, 
                        $mes, 
                        $dia, 
                        $hora, 
                        $minuto, 
                        0, 
                        'America/Sao_Paulo'
                    );
                    
                    // Verifica se a data é válida (ex: 31/02 não existe)
                    // Carbon::create pode retornar uma data inválida, então verificamos
                    if ($dataMemo->day != $dia || $dataMemo->month != $mes) {
                        throw new \Exception('Data inválida criada (dia ou mês não corresponde)');
                    }
                    
                    // Lógica inteligente de determinação do ano
                    // Se a data do MEMO (ano atual) for muito anterior ao DTPOSTED, usar ano anterior
                    $diferencaDias = $dtpostedCarbon->diffInDays($dataMemo, false);
                    
                    if ($diferencaDias < -30) {
                        // Data do MEMO é mais de 30 dias anterior ao DTPOSTED
                        // Provavelmente é do ano anterior (caso de virada de ano)
                        $dataMemo = Carbon::create(
                            $dtpostedCarbon->year - 1, 
                            $mes, 
                            $dia, 
                            $hora, 
                            $minuto, 
                            0, 
                            'America/Sao_Paulo'
                        );
                        
                        // Valida novamente
                        if ($dataMemo->day == $dia && $dataMemo->month == $mes) {
                            Log::info('Usando ano anterior para data do MEMO', [
                                'memo' => $memo,
                                'data_memo' => $dataMemo->format('Y-m-d H:i:s'),
                                'dtposted' => $dtpostedCarbon->format('Y-m-d'),
                                'diferenca_dias' => $diferencaDias
                            ]);
                            
                            return [
                                'datetime' => $dataMemo,
                                'source' => 'memo'
                            ];
                        }
                    } else {
                        // Data parece estar no mesmo ano ou próximo
                        return [
                            'datetime' => $dataMemo,
                            'source' => 'memo'
                        ];
                    }
                } catch (\Exception $e) {
                    // Se falhar ao criar data, tenta com ano anterior
                    try {
                        $dataMemoAnterior = Carbon::create(
                            $dtpostedCarbon->year - 1, 
                            $mes, 
                            $dia, 
                            $hora, 
                            $minuto, 
                            0, 
                            'America/Sao_Paulo'
                        );
                        
                        if ($dataMemoAnterior->day == $dia && $dataMemoAnterior->month == $mes) {
                            Log::info('Usando ano anterior após erro na criação da data', [
                                'memo' => $memo,
                                'data_memo' => $dataMemoAnterior->format('Y-m-d H:i:s'),
                                'dtposted' => $dtpostedCarbon->format('Y-m-d'),
                                'error' => $e->getMessage()
                            ]);
                            
                            return [
                                'datetime' => $dataMemoAnterior,
                                'source' => 'memo'
                            ];
                        }
                    } catch (\Exception $e2) {
                        Log::warning('Erro ao extrair data/hora do MEMO (tentativas com ambos os anos falharam)', [
                            'memo' => $memo,
                            'dtposted' => $dtpostedCarbon->format('Y-m-d'),
                            'error_ano_atual' => $e->getMessage(),
                            'error_ano_anterior' => $e2->getMessage()
                        ]);
                    }
                }
            }
        } else {
            // Não encontrou padrão de data/hora no MEMO
            Log::debug('Padrão de data/hora não encontrado no MEMO', [
                'memo' => $memo,
                'dtposted' => $dtpostedCarbon->format('Y-m-d')
            ]);
        }
        
        // Fallback: usa dtposted com horário padrão 12:00
        $datetime = $dtpostedCarbon->copy()->setTime(12, 0, 0);
        return [
            'datetime' => $datetime,
            'source' => 'dtposted'
        ];
    }

    /**
     * Filtra transações relevantes (entradas com PIX RECEBIDO)
     * 
     * @param \Illuminate\Database\Eloquent\Collection $bankStatements
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function filtrarTransacoesRelevantes($bankStatements)
    {
        return $bankStatements->filter(function ($statement) {
            // Apenas entradas (amount > 0)
            if ($statement->amount <= 0) {
                return false;
            }
            
            // MEMO deve conter "PIX - RECEBIDO" ou "PIX-RECEBIDO" (com ou sem espaço)
            if ($statement->memo && (
                stripos($statement->memo, 'PIX - RECEBIDO') !== false ||
                stripos($statement->memo, 'PIX-RECEBIDO') !== false ||
                stripos($statement->memo, 'PIX RECEBIDO') !== false
            )) {
                return true;
            }
            
            return false;
        });
    }

    /**
     * Encontra missas correspondentes para uma transação
     * 
     * @param Carbon $transactionDatetime
     * @param int $companyId
     * @return HorarioMissa|null
     */
    public function encontrarMissasCorrespondentes($transactionDatetime, $companyId)
    {
        $diaSemana = $this->calcularDiaSemana($transactionDatetime);
        
        Log::debug('Buscando missas correspondentes', [
            'transaction_datetime' => $transactionDatetime->format('Y-m-d H:i:s'),
            'dia_semana' => $diaSemana,
            'company_id' => $companyId
        ]);
        
        // Busca missas do mesmo dia da semana
        $missas = HorarioMissa::where('company_id', $companyId)
            ->where('dia_semana', $diaSemana)
            ->get();

        Log::debug('Missas encontradas para o dia da semana', [
            'dia_semana' => $diaSemana,
            'total' => $missas->count(),
            'missas' => $missas->map(function($m) {
                return [
                    'id' => $m->id,
                    'horario' => $m->horario,
                    'intervalo' => $m->intervalo
                ];
            })->toArray()
        ]);

        if ($missas->isEmpty()) {
            Log::debug('Nenhuma missa encontrada para o dia da semana', [
                'dia_semana' => $diaSemana,
                'company_id' => $companyId
            ]);
            return null;
        }

        $missaCorrespondente = null;
        $menorDiferenca = null;

        foreach ($missas as $missa) {
            // Converte horario da missa para Carbon no mesmo dia da transação
            // horario é do tipo time, então precisamos extrair apenas H:i:s
            $horarioTime = $missa->horario instanceof \DateTime 
                ? $missa->horario->format('H:i:s')
                : (is_string($missa->horario) ? $missa->horario : '00:00:00');
            
            // Se horarioTime não tem segundos, adiciona
            if (strlen($horarioTime) === 5) {
                $horarioTime .= ':00';
            }
            
            $horarioMissa = Carbon::parse($transactionDatetime->format('Y-m-d') . ' ' . $horarioTime)
                ->setTimezone('America/Sao_Paulo');
            
            $intervalo = $missa->intervalo ?? 90; // Default 90 minutos
            
            // Calcula intervalo: apenas após o horário da missa até horario_missa + intervalo
            // NUNCA considerar transações antes do horário da missa
            $inicioIntervalo = $horarioMissa; // Início = horário exato da missa
            $fimIntervalo = $horarioMissa->copy()->addMinutes($intervalo); // Fim = missa + intervalo
            
            Log::debug('Verificando missa', [
                'missa_id' => $missa->id,
                'horario_missa' => $horarioMissa->format('H:i:s'),
                'intervalo' => $intervalo,
                'inicio_intervalo' => $inicioIntervalo->format('H:i:s'),
                'fim_intervalo' => $fimIntervalo->format('H:i:s'),
                'transaction_datetime' => $transactionDatetime->format('H:i:s'),
                'dentro_intervalo' => ($transactionDatetime >= $inicioIntervalo && $transactionDatetime <= $fimIntervalo)
            ]);
            
            // Verifica se a transação está no intervalo: >= horário_missa E <= horário_missa + intervalo
            if ($transactionDatetime >= $inicioIntervalo && $transactionDatetime <= $fimIntervalo) {
                // Calcula diferença absoluta em minutos
                $diferenca = abs($transactionDatetime->diffInMinutes($horarioMissa));
                
                Log::info('Missa dentro do intervalo encontrada', [
                    'missa_id' => $missa->id,
                    'diferenca_minutos' => $diferenca
                ]);
                
                // Escolhe a missa mais próxima
                if ($menorDiferenca === null || $diferenca < $menorDiferenca) {
                    $menorDiferenca = $diferenca;
                    $missaCorrespondente = $missa;
                }
            }
        }

        if ($missaCorrespondente) {
            Log::info('Missa correspondente selecionada', [
                'missa_id' => $missaCorrespondente->id,
                'diferenca_minutos' => $menorDiferenca
            ]);
        } else {
            Log::debug('Nenhuma missa dentro do intervalo encontrada', [
                'transaction_datetime' => $transactionDatetime->format('Y-m-d H:i:s'),
                'total_missas_verificadas' => $missas->count()
            ]);
        }

        return $missaCorrespondente;
    }

    /**
     * Converte Carbon para string do dia da semana
     * 
     * @param Carbon $datetime
     * @return string 'domingo', 'segunda', etc.
     */
    public function calcularDiaSemana($datetime)
    {
        $diasSemana = [
            0 => 'domingo',
            1 => 'segunda',
            2 => 'terca',
            3 => 'quarta',
            4 => 'quinta',
            5 => 'sexta',
            6 => 'sabado'
        ];
        
        $diaNumero = $datetime->dayOfWeek;
        return $diasSemana[$diaNumero];
    }

    /**
     * Cria lançamento financeiro para uma transação conciliada
     * 
     * @param BankStatement $bankStatement
     * @param HorarioMissa $horarioMissa
     * @return TransacaoFinanceira|null
     */
    public function criarLancamentoFinanceiro($bankStatement, $horarioMissa)
    {
        try {
            // Busca ou cria Lançamento Padrão
            $lancamentoPadrao = LancamentoPadrao::firstOrCreate(
                ['description' => 'Coletas Realizadas durante as missas para apoio às atividades do Convento'],
                [
                    'type' => 'entrada',
                    'user_id' => Auth::id() ?? 1,
                    'date' => now()
                ]
            );

            // Recarrega o lançamento padrão para garantir que temos os campos contábeis atualizados
            $lancamentoPadrao->refresh();

            // Converte amount do BankStatement usando Money
            // BankStatement->amount está em DECIMAL, precisa converter para centavos (integer)
            $money = Money::fromDatabase((float) $bankStatement->amount);
            
            // Cria Movimentacao com campos contábeis do lançamento padrão
            $movimentacao = Movimentacao::create([
                'entidade_id' => $bankStatement->entidade_financeira_id,
                'tipo' => 'entrada',
                'valor' => $money->toDatabase(), // Movimentacao usa DECIMAL
                'descricao' => $bankStatement->memo ?? 'Coleta de missa',
                'data' => $bankStatement->transaction_datetime ?? $bankStatement->dtposted,
                'company_id' => $bankStatement->company_id,
                'created_by' => Auth::id(),
                'created_by_name' => Auth::user()->name ?? 'Sistema',
                'updated_by' => Auth::id(),
                'updated_by_name' => Auth::user()->name ?? 'Sistema',
                'lancamento_padrao_id' => $lancamentoPadrao->id,
                'conta_debito_id' => $lancamentoPadrao->conta_debito_id ?? null,
                'conta_credito_id' => $lancamentoPadrao->conta_credito_id ?? null,
                'data_competencia' => $bankStatement->transaction_datetime ?? $bankStatement->dtposted,
            ]);

            // Cria TransacaoFinanceira com campos contábeis do lançamento padrão
            // TransacaoFinanceira->valor está em DECIMAL
            $transacaoFinanceira = TransacaoFinanceira::create([
                'company_id' => $bankStatement->company_id,
                'data_competencia' => $bankStatement->transaction_datetime ?? $bankStatement->dtposted,
                'entidade_id' => $bankStatement->entidade_financeira_id,
                'tipo' => 'entrada',
                'valor' => $money->toDatabase(), // TransacaoFinanceira usa DECIMAL
                'descricao' => $bankStatement->memo ?? 'Coleta realizada durante missa',
                'lancamento_padrao_id' => $lancamentoPadrao->id,
                'movimentacao_id' => $movimentacao->id,
                'tipo_documento' => 'Pix',
                'origem' => 'Banco',
                'created_by' => Auth::id(),
                'created_by_name' => Auth::user()->name ?? 'Sistema',
                'updated_by' => Auth::id(),
                'updated_by_name' => Auth::user()->name ?? 'Sistema',
            ]);

            return $transacaoFinanceira;

        } catch (\Exception $e) {
            Log::error('Erro ao criar lançamento financeiro', [
                'bank_statement_id' => $bankStatement->id,
                'horario_missa_id' => $horarioMissa->id,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}

