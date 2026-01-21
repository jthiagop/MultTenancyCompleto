<?php

namespace App\Services;

use App\Models\Financeiro\Recorrencia;
use App\Models\Financeiro\TransacaoFinanceira;
use App\Models\Movimentacao;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RecurrenceService
{
    /**
     * Generate an array of occurrence dates based on recurrence parameters.
     *
     * @param Carbon $startDate The first occurrence date
     * @param string $frequency 'diario', 'semanal', 'mensal', 'anual'
     * @param int $interval Number of periods between occurrences (e.g., 1 = every week, 2 = every 2 weeks)
     * @param int $totalOccurrences Total number of occurrences to generate
     * @return array Array of Carbon dates (length = totalOccurrences)
     */
    public function generateOccurrenceDates(Carbon $startDate, string $frequency, int $interval, int $totalOccurrences): array
    {
        $dates = [];
        $currentDate = $startDate->copy();

        // First occurrence is always the start date
        $dates[] = $currentDate->copy();

        // Generate remaining occurrences (totalOccurrences - 1)
        for ($i = 1; $i < $totalOccurrences; $i++) {
            switch ($frequency) {
                case 'diario': // Ajustado de 'diaria' para 'diario'
                    $currentDate->addDays($interval);
                    break;
                case 'semanal':
                    $currentDate->addWeeks($interval);
                    break;
                case 'mensal':
                    $currentDate->addMonthsNoOverflow($interval);
                    break;
                case 'anual':
                    $currentDate->addYearsNoOverflow($interval);
                    break;
                default:
                    throw new \InvalidArgumentException("Unsupported frequency: {$frequency}");
            }

            $dates[] = $currentDate->copy();
        }

        return $dates;
    }

    /**
     * Generate recurring transactions for a given recurrence configuration.
     *
     * @param Recorrencia $recorrencia The recurrence configuration
     * @param TransacaoFinanceira $originalTransaction The first transaction (already created)
     * @param array $validatedData The validated form data
     * @return void
     */
    public function generateRecurringTransactions(
        Recorrencia $recorrencia,
        TransacaoFinanceira $originalTransaction,
        array $validatedData
    ): void {
        Log::info('generateRecurringTransactions - Início', [
            'recorrencia_id' => $recorrencia->id,
            'transacao_id' => $originalTransaction->id,
            'frequencia' => $recorrencia->frequencia,
            'intervalo_repeticao' => $recorrencia->intervalo_repeticao,
            'total_ocorrencias' => $recorrencia->total_ocorrencias
        ]);

        DB::transaction(function () use ($recorrencia, $originalTransaction, $validatedData) {
            $startDate = Carbon::parse($validatedData['data_competencia']);
            $occurrenceDates = $this->generateOccurrenceDates(
                $startDate,
                $recorrencia->frequencia,
                $recorrencia->intervalo_repeticao,
                $recorrencia->total_ocorrencias
            );

            Log::info('Generating recurring transactions', [
                'recorrencia_id' => $recorrencia->id,
                'start_date' => $startDate->format('Y-m-d'),
                'frequency' => $recorrencia->frequencia,
                'interval' => $recorrencia->intervalo_repeticao,
                'total_occurrences' => $recorrencia->total_ocorrencias,
                'first_date' => $occurrenceDates[0]->format('Y-m-d'),
                'last_date' => $occurrenceDates[count($occurrenceDates) - 1]->format('Y-m-d'),
                'total_dates_generated' => count($occurrenceDates)
            ]);

            // Attach the original transaction as occurrence #1
            $recorrencia->transacoesGeradas()->attach($originalTransaction->id, [
                'data_geracao' => $occurrenceDates[0]->format('Y-m-d'),
                'numero_ocorrencia' => 1,
                'movimentacao_id' => $originalTransaction->movimentacao_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $recorrencia->increment('ocorrencias_geradas');

            // Calculate due date difference (if applicable)
            $dueDateDiff = null;
            if (isset($validatedData['data_vencimento'])) {
                $dataCompetenciaOriginal = Carbon::parse($validatedData['data_competencia']);
                $dataVencimentoOriginal = Carbon::parse($validatedData['data_vencimento']);
                $dueDateDiff = $dataCompetenciaOriginal->diffInDays($dataVencimentoOriginal, false);
            }

            // Generate future transactions (starting from occurrence #2)
            for ($i = 1; $i < count($occurrenceDates); $i++) {
                $occurrenceDate = $occurrenceDates[$i];
                $occurrenceNumber = $i + 1;

                // Prepare transaction data
                $novaTransacaoData = $validatedData;
                
                // Adjust due date if exists
                if ($dueDateDiff !== null) {
                    $novaDataVencimento = $occurrenceDate->copy()->addDays($dueDateDiff);
                    $novaTransacaoData['data_vencimento'] = $novaDataVencimento->format('Y-m-d');
                    
                    // Ajustar data_competencia para manter o mesmo dia mas no mês do vencimento
                    $diaCompetenciaOriginal = Carbon::parse($validatedData['data_competencia'])->day;
                    $novaDataCompetencia = $novaDataVencimento->copy();
                    
                    // Tentar definir o dia original, se não existir no mês (ex: 31 em fevereiro), usar o último dia do mês
                    try {
                        $novaDataCompetencia->day($diaCompetenciaOriginal);
                    } catch (\Exception $e) {
                        $novaDataCompetencia = $novaDataCompetencia->endOfMonth();
                    }
                    
                    $novaTransacaoData['data_competencia'] = $novaDataCompetencia->format('Y-m-d');
                } else {
                    // Se não houver vencimento, usar a data de ocorrência
                    $novaTransacaoData['data_competencia'] = $occurrenceDate->format('Y-m-d');
                }

                // Set status for future transactions
                $novaTransacaoData['situacao'] = 'em_aberto';
                $novaTransacaoData['agendado'] = true;

                // Create Movimentacao
                $novaMovimentacao = Movimentacao::create([
                    'entidade_id' => $novaTransacaoData['entidade_id'],
                    'tipo' => $novaTransacaoData['tipo'],
                    'valor' => $novaTransacaoData['valor'],
                    'data' => $novaTransacaoData['data_competencia'],
                    'descricao' => $validatedData['descricao'] . " ({$occurrenceNumber}/{$recorrencia->total_ocorrencias})",
                    'company_id' => $novaTransacaoData['company_id'],
                    'created_by' => Auth::id(),
                    'created_by_name' => Auth::user()->name,
                    'updated_by' => Auth::id(),
                    'updated_by_name' => Auth::user()->name,
                ]);

                $novaTransacaoData['movimentacao_id'] = $novaMovimentacao->id;
                $novaTransacaoData['descricao'] = $validatedData['descricao'] . " ({$occurrenceNumber}/{$recorrencia->total_ocorrencias})";

                // Create TransacaoFinanceira with recorrencia_id
                $novaTransacaoData['recorrencia_id'] = $recorrencia->id;
                $novaTransacao = TransacaoFinanceira::create($novaTransacaoData);

                // Attach to recurrence pivot table
                $recorrencia->transacoesGeradas()->attach($novaTransacao->id, [
                    'data_geracao' => $occurrenceDate->format('Y-m-d'),
                    'numero_ocorrencia' => $occurrenceNumber,
                    'movimentacao_id' => $novaMovimentacao->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $recorrencia->increment('ocorrencias_geradas');
            }

            // Update recurrence metadata
            $lastOccurrenceDate = $occurrenceDates[count($occurrenceDates) - 1];
            $recorrencia->update([
                'data_fim' => $lastOccurrenceDate->format('Y-m-d'),
                'ultima_execucao' => now(),
                'data_proxima_geracao' => null, // All occurrences generated upfront
            ]);

            Log::info('Recurring transactions generated successfully', [
                'recorrencia_id' => $recorrencia->id,
                'occurrences_generated' => $recorrencia->ocorrencias_geradas,
            ]);
        });
    }
}
