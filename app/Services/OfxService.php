<?php

namespace App\Services;

use App\Models\EntidadeFinanceira;
use App\Models\Financeiro\BankStatement;
use Endeken\OFX\OFX;
use Illuminate\Support\Facades\Storage;


class OfxService
{
    /**
     * Normaliza o número da conta removendo pontuação e zeros à esquerda
     * Preserva letras (como o X do Banco do Brasil)
     * 
     * @param string $conta
     * @return string
     */
    private function normalizarConta($conta)
    {
        // Remove pontuação, mas preserva letras (X do BB)
        $contaNormalizada = preg_replace('/[^a-zA-Z0-9]/i', '', $conta);
        
        // Remove zeros à esquerda, mas apenas se houver dígitos após eles
        // Usa regex para remover zeros do início
        $contaNormalizada = preg_replace('/^0+(?=[0-9a-zA-Z])/', '', $contaNormalizada);
        
        return strtoupper($contaNormalizada);
    }

    /**
     * Busca a entidade financeira usando BANKID + Conta com comparação robusta
     * 
     * @param string $bankIdOFX - BANKID do OFX (ex: "001")
     * @param string $contaOFX - ACCTID do OFX (ex: "12771-X")
     * @param int $companyId - ID da empresa
     * @return EntidadeFinanceira|null
     */
    private function encontrarEntidade($bankIdOFX, $contaOFX, $companyId)
    {
        $contaNormalizadaOFX = $this->normalizarConta($contaOFX);

        // Busca todas as entidades da empresa com relacionamento de banco
        $entidades = EntidadeFinanceira::where('company_id', $companyId)
            ->with('bank')
            ->get();

        foreach ($entidades as $entidade) {
            // Verifica se o banco existe e compara o COMPE code
            if (!$entidade->bank) {
                continue;
            }

            $compeCodeBD = (string) $entidade->bank->compe_code;
            $bankIdOFXStr = (string) $bankIdOFX;

            // Compara os códigos de banco
            if ($compeCodeBD !== $bankIdOFXStr) {
                continue;
            }

            // Normaliza a conta do banco de dados
            $contaBD = $this->normalizarConta($entidade->conta);

            // Compara usando str_ends_with para ser mais robusto
            // Isso permite: 0012771-X (BD) == 12771-X (OFX) ✓
            if (str_ends_with($contaBD, $contaNormalizadaOFX) || $contaBD === $contaNormalizadaOFX) {
                return $entidade;
            }
        }

        return null;
    }

    public function processOfx($file, $usarHorariosMissas = false, $fileHash = null, $fileName = null)
    {
        ini_set('memory_limit', '512M'); // Aumenta o limite para 512MB

        // 1. Salvar arquivo no storage
        $path = $file->store('ofx_uploads');
        $ofxPath = Storage::path($path);

        // 2. Ler e tratar o conteúdo
        $contents = file_get_contents($ofxPath);
        $contents = preg_replace('/[^\x20-\x7E\r\n]/', '', $contents);
        $contents = preg_replace('/\[-?\d+:BRT\]/', '', $contents);
        $contents = preg_replace('/\[-?\d+:GMT\]/', '', $contents);
        $contents = iconv('cp1252', 'utf-8//TRANSLIT', $contents);
        file_put_contents($ofxPath, $contents);

        // 3. Processar o OFX
        $parsedData = OFX::parse($contents);

        $totalTransacoesImportadas = 0;
        $entidadesImportadas = [];

        // 4. Verifica se há contas bancárias no arquivo OFX
        if (empty($parsedData->bankAccounts)) {
            throw new \Exception('Nenhuma conta bancária encontrada no arquivo OFX.');
        }

        // 5. Usar transação de banco de dados para garantir integridade
        \DB::beginTransaction();
        
        try {
            // 6. Iterar sobre as contas do OFX
            foreach ($parsedData->bankAccounts as $account) {
                $bankIdOFX    = $account->routingNumber;   // BANKID do OFX (ex: "001")
                $contaOFX     = $account->accountNumber;   // ACCTID do OFX (ex: "12771-X")
                $companyId    = session('active_company_id'); // Empresa do usuário logado

                // 7. Busca a entidade de forma robusta usando BANKID + Normalização de Conta
                $entidade = $this->encontrarEntidade($bankIdOFX, $contaOFX, $companyId);

                if (!$entidade) {
                    throw new \Exception(
                        "Conta bancária não encontrada! " .
                        "Banco (COMPE): $bankIdOFX, Conta: $contaOFX. " .
                        "Verifique se a conta está cadastrada no sistema."
                    );
                }

                // 8. Iterar sobre as transações da conta
                $transacoesImportadas = [];
                foreach ($account->statement->transactions ?? [] as $transaction) {
                    $bankStatement = BankStatement::storeTransaction($account, $transaction, $entidade->id, $fileHash, $fileName);
                    if ($bankStatement) {
                        $transacoesImportadas[] = $bankStatement;
                        $totalTransacoesImportadas++;
                    }
                }

                // 9. Armazena a entidade se houve importação de transações
                if (!empty($transacoesImportadas)) {
                    if (!in_array($entidade->id, array_column($entidadesImportadas, 'id'))) {
                        $entidadesImportadas[] = [
                            'id' => $entidade->id,
                            'nome' => $entidade->descricao,
                            'conta' => $entidade->conta,
                            'transacoes' => count($transacoesImportadas)
                        ];
                    }
                }

                // 10. Processar conciliação automática com missas (apenas se o usuário escolheu usar horários de missa)
                if ($usarHorariosMissas && !empty($transacoesImportadas)) {
                    try {
                        $conciliacaoService = new \App\Services\ConciliacaoMissasService();
                        $conciliacaoService->processarTransacoes($companyId, collect($transacoesImportadas));
                    } catch (\Exception $e) {
                        // Log do erro mas não interrompe a importação
                        \Log::warning('Erro ao processar conciliação automática de missas', [
                            'company_id' => $companyId,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }

            // Commit da transação
            \DB::commit();

        } catch (\Exception $e) {
            // Rollback em caso de erro
            \DB::rollBack();
            throw $e;
        }

        return [
            'totalTransacoes' => $totalTransacoesImportadas,
            'entidades' => $entidadesImportadas
        ];
    }

}
