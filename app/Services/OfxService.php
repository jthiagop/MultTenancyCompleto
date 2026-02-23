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
     * Mapa de tipos de conta OFX → tipos do sistema.
     * Padrão OFX: CHECKING, SAVINGS, MONEYMRKT, CREDITLINE
     */
    private const ACCOUNT_TYPE_MAP = [
        'CHECKING'   => 'corrente',
        'SAVINGS'    => 'poupanca',
        'MONEYMRKT'  => 'aplicacao',
        'CREDITLINE' => 'corrente',
    ];

    /**
     * Converte o ACCTTYPE do OFX para o account_type do sistema.
     *
     * @param string|null $ofxType
     * @return string|null
     */
    private function mapearAccountType(?string $ofxType): ?string
    {
        if (!$ofxType) {
            return null;
        }

        return self::ACCOUNT_TYPE_MAP[strtoupper($ofxType)] ?? null;
    }

    /**
     * Busca a entidade financeira usando BANKID + Conta + AccountType.
     *
     * Quando há múltiplas entidades com mesma conta (ex: corrente e aplicação),
     * o accountType do OFX é usado para desambiguar.
     * Se o tipo não estiver disponível no OFX, retorna a primeira que bater
     * (comportamento legado).
     * 
     * @param string $bankIdOFX   - BANKID do OFX (ex: "001")
     * @param string $contaOFX    - ACCTID do OFX (ex: "12771-X")
     * @param int    $companyId   - ID da empresa
     * @param string|null $accountTypeOFX - ACCTTYPE do OFX (ex: "CHECKING", "SAVINGS")
     * @return EntidadeFinanceira|null
     */
    private function encontrarEntidade($bankIdOFX, $contaOFX, $companyId, $accountTypeOFX = null)
    {
        $contaNormalizadaOFX = $this->normalizarConta($contaOFX);
        $tipoMapeado = $this->mapearAccountType($accountTypeOFX);

        // Busca todas as entidades da empresa com relacionamento de banco
        $entidades = EntidadeFinanceira::where('company_id', $companyId)
            ->with('bank')
            ->get();

        // Primeira passada: busca match exato (banco + conta + tipo)
        $candidatas = [];

        foreach ($entidades as $entidade) {
            // Verifica se o banco existe e compara o COMPE code
            if (!$entidade->bank) {
                continue;
            }

            // Normaliza os códigos COMPE removendo zeros à esquerda
            // (alguns OFX enviam "1" ao invés de "001")
            $compeCodeBD  = ltrim((string) $entidade->bank->compe_code, '0');
            $bankIdOFXStr = ltrim((string) $bankIdOFX, '0');

            // Compara os códigos de banco (sem zeros à esquerda)
            if ($compeCodeBD !== $bankIdOFXStr) {
                continue;
            }

            // Normaliza a conta do banco de dados
            $contaBD = $this->normalizarConta($entidade->conta);

            // Compara usando str_ends_with para ser mais robusto
            if (str_ends_with($contaBD, $contaNormalizadaOFX) || $contaBD === $contaNormalizadaOFX) {
                $candidatas[] = $entidade;
            }
        }

        // Nenhuma candidata encontrada
        if (empty($candidatas)) {
            return null;
        }

        // Se só há uma candidata, retorna direto (não precisa desambiguar)
        if (count($candidatas) === 1) {
            return $candidatas[0];
        }

        // Múltiplas candidatas: tenta desambiguar pelo tipo de conta
        if ($tipoMapeado) {
            foreach ($candidatas as $candidata) {
                if ($candidata->account_type === $tipoMapeado) {
                    return $candidata;
                }
            }
        }

        // Fallback: retorna a primeira candidata (comportamento legado)
        \Log::warning('OFX: Múltiplas entidades encontradas para mesma conta, sem tipo para desambiguar', [
            'bank_id' => $bankIdOFX,
            'conta' => $contaOFX,
            'account_type_ofx' => $accountTypeOFX,
            'candidatas' => collect($candidatas)->pluck('id', 'account_type')->toArray(),
        ]);

        return $candidatas[0];
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
                $bankIdOFX      = $account->routingNumber;   // BANKID do OFX (ex: "001")
                $contaOFX       = $account->accountNumber;   // ACCTID do OFX (ex: "12771-X")
                $accountTypeOFX = $account->accountType ?? null; // ACCTTYPE do OFX (ex: "CHECKING", "SAVINGS")
                $companyId      = session('active_company_id'); // Empresa do usuário logado

                // 7. Busca a entidade usando BANKID + Conta + AccountType para desambiguação
                $entidade = $this->encontrarEntidade($bankIdOFX, $contaOFX, $companyId, $accountTypeOFX);

                if (!$entidade) {
                    $tipoLabel = $accountTypeOFX ? " ({$accountTypeOFX})" : '';
                    throw new \Exception(
                        "Conta bancária não encontrada! " .
                        "Banco (COMPE): {$bankIdOFX}, Conta: {$contaOFX}{$tipoLabel}. " .
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
