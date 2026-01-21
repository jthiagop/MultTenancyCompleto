<?php

namespace App\Services;

use App\Models\BankStatementImport;
use App\Models\BankStatementEntry;
use App\Models\BankAccount;
use App\Services\Banks\BancoBrasilService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BankStatementImportService
{
    /**
     * Importa extrato do BB via API
     * Aceita BankAccount model ou objeto simples com bankConfig e company_id
     */
    public function importFromBBApi(
        $bankAccount,
        Carbon $startDate,
        Carbon $endDate
    ): BankStatementImport {
        // Validar se a conta tem configuração BB
        $bankConfig = is_object($bankAccount) && isset($bankAccount->bankConfig) 
            ? $bankAccount->bankConfig 
            : (method_exists($bankAccount, 'bankConfig') ? $bankAccount->bankConfig : null);

        if (!$bankConfig) {
            throw new \Exception("Conta bancária não possui configuração do BB");
        }

        $bbService = new BancoBrasilService($bankConfig);
        
        $companyId = is_object($bankAccount) && isset($bankAccount->company_id)
            ? $bankAccount->company_id
            : (method_exists($bankAccount, 'company_id') ? $bankAccount->company_id : Auth::user()->company_id);
            
        $bankAccountId = is_object($bankAccount) && isset($bankAccount->id)
            ? $bankAccount->id
            : (method_exists($bankAccount, 'id') ? $bankAccount->id : null);

        // Formatar datas para o formato do BB (dmY)
        $dataInicio = $startDate->format('dmY');
        $dataFim = $endDate->format('dmY');

        // Buscar extrato da API
        $extratoData = $bbService->getExtrato($dataInicio, $dataFim);

        return DB::transaction(function () use ($companyId, $bankAccountId, $startDate, $endDate, $extratoData, $bankConfig) {
            // Criar registro de importação
            $importData = [
                'company_id' => $companyId,
                'bank_config_id' => $bankConfig->id, // ID da configuração BB usada
                'source' => 'API_BB',
                'period_start' => $startDate,
                'period_end' => $endDate,
                'imported_by' => Auth::id(),
                'imported_at' => now(),
            ];
            
            // Adiciona bank_account_id apenas se existir e não for null
            if ($bankAccountId) {
                $importData['bank_account_id'] = $bankAccountId;
            }
            
            $import = BankStatementImport::create($importData);

            // Processar lançamentos
            $lancamentos = $extratoData['listaLancamento'] ?? [];
            $imported = 0;
            $duplicates = 0;

            foreach ($lancamentos as $lancamento) {
                // Converter data (formato DDMMAAAA para Y-m-d)
                $dataLancamento = $this->parseBBDate($lancamento['dataLancamento']);

                // Determinar tipo e valor assinado
                $tipo = strtoupper($lancamento['indicadorSinalLancamento']) === 'C' ? 'CREDIT' : 'DEBIT';
                $valor = (float) $lancamento['valorLancamento'];
                $valorAssinado = $tipo === 'CREDIT' ? $valor : -$valor;

                // Gerar hash único
                $hash = BankStatementEntry::generateHash(
                    $dataLancamento,
                    $valor,
                    $tipo,
                    $lancamento['numeroDocumento'] ?? null
                );

                // Tentar inserir (ignora se já existir)
                try {
                    BankStatementEntry::create([
                        'company_id' => $companyId,
                        'import_id' => $import->id,
                        'posted_at' => $dataLancamento,
                        'description' => $lancamento['textoDescricaoHistorico'] ?? 'Sem descrição',
                        'document_number' => $lancamento['numeroDocumento'] ?? null,
                        'amount' => $valor,
                        'type' => $tipo,
                        'amount_signed' => $valorAssinado,
                        'balance_after' => null, // BB não retorna saldo por lançamento
                        'unique_hash' => $hash,
                        'status_conciliacao' => 'pendente',
                        'bank_metadata' => [
                            'codigo_historico' => $lancamento['codigoHistorico'] ?? null,
                            'numero_lote' => $lancamento['numeroLote'] ?? null,
                            'agencia_origem' => $lancamento['codigoAgenciaOrigem'] ?? null,
                            'cpf_cnpj_contrapartida' => $lancamento['numeroCpfCnpjContrapartida'] ?? null,
                            'banco_contrapartida' => $lancamento['codigoBancoContrapartida'] ?? null,
                        ],
                    ]);
                    $imported++;
                } catch (\Illuminate\Database\QueryException $e) {
                    // Violação de unique constraint = duplicado
                    if ($e->getCode() == 23000) {
                        $duplicates++;
                    } else {
                        throw $e;
                    }
                }
            }

            // Log de resultado
            \Log::info('BB Extrato - Importação concluída', [
                'import_id' => $import->id,
                'total_lancamentos' => count($lancamentos),
                'importados' => $imported,
                'duplicados' => $duplicates,
            ]);

            return $import;
        });
    }

    /**
     * Converte data do BB (DDMMAAAA) para Y-m-d
     */
    private function parseBBDate($bbDate): string
    {
        $day = substr($bbDate, 0, 2);
        $month = substr($bbDate, 2, 2);
        $year = substr($bbDate, 4, 4);

        return "{$year}-{$month}-{$day}";
    }
}
