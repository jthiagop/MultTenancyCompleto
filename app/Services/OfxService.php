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
     * Normaliza o número da agência removendo pontuação e zeros à esquerda.
     *
     * @param string|null $agencia
     * @return string|null
     */
    private function normalizarAgencia(?string $agencia): ?string
    {
        if (!$agencia || trim($agencia) === '') {
            return null;
        }

        // Remove pontuação (traços, pontos, espaços)
        $agenciaNormalizada = preg_replace('/[^0-9]/i', '', $agencia);

        // Remove zeros à esquerda
        $agenciaNormalizada = ltrim($agenciaNormalizada, '0');

        return $agenciaNormalizada ?: null;
    }

    /**
     * Busca a entidade financeira usando BANKID + Conta + AccountType + Agência.
     *
     * Estratégia de matching (da mais específica para a mais genérica):
     * 1. Banco + Conta → coleta candidatas
     * 2. Se múltiplas → filtra por agência (BRANCHID), se disponível no OFX
     * 3. Se ainda múltiplas → filtra por tipo de conta (ACCTTYPE)
     * 4. Fallback → primeira candidata (com log de warning)
     *
     * Nem todos os bancos enviam BRANCHID no OFX (ex: Banco do Brasil omite).
     * Nesses casos, a desambiguação acontece apenas pelo tipo de conta.
     * 
     * @param string $bankIdOFX   - BANKID do OFX (ex: "001")
     * @param string $contaOFX    - ACCTID do OFX (ex: "12771-X")
     * @param int    $companyId   - ID da empresa
     * @param string|null $accountTypeOFX - ACCTTYPE do OFX (ex: "CHECKING", "SAVINGS")
     * @param string|null $agenciaOFX     - BRANCHID do OFX (ex: "1851"), pode ser null
     * @return EntidadeFinanceira|null
     */
    private function encontrarEntidade($bankIdOFX, $contaOFX, $companyId, $accountTypeOFX = null, $agenciaOFX = null)
    {
        $contaNormalizadaOFX = $this->normalizarConta($contaOFX);
        $tipoMapeado = $this->mapearAccountType($accountTypeOFX);
        $agenciaNormalizadaOFX = $this->normalizarAgencia($agenciaOFX);

        // Busca todas as entidades da empresa com relacionamento de banco
        $entidades = EntidadeFinanceira::where('company_id', $companyId)
            ->with('bank')
            ->get();

        // Primeira passada: busca candidatas por banco + conta
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

            // Compara as contas normalizadas
            // Primeiro tenta igualdade exata, depois verifica se uma contém a outra
            // (bancos podem enviar conta com ou sem dígito verificador)
            // Exige que a conta menor tenha pelo menos 4 caracteres para evitar falsos positivos
            if ($contaBD === $contaNormalizadaOFX) {
                $candidatas[] = $entidade;
            } elseif (strlen($contaNormalizadaOFX) >= 4 && strlen($contaBD) >= 4) {
                // Aceita match parcial apenas se a diferença for pequena (dígito verificador)
                $menor = strlen($contaBD) <= strlen($contaNormalizadaOFX) ? $contaBD : $contaNormalizadaOFX;
                $maior = strlen($contaBD) > strlen($contaNormalizadaOFX) ? $contaBD : $contaNormalizadaOFX;
                if (str_ends_with($maior, $menor) && (strlen($maior) - strlen($menor)) <= 2) {
                    $candidatas[] = $entidade;
                }
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

        // --- Desambiguação em cascata ---

        // 2. Filtra por agência (BRANCHID), se disponível no OFX
        if ($agenciaNormalizadaOFX) {
            $filtradas = array_filter($candidatas, function ($c) use ($agenciaNormalizadaOFX) {
                $agBD = $this->normalizarAgencia($c->agencia);
                return $agBD && $agBD === $agenciaNormalizadaOFX;
            });

            if (count($filtradas) === 1) {
                return array_values($filtradas)[0];
            }

            // Se filtrou e ainda tem resultados, usa como base para próximo filtro
            if (!empty($filtradas)) {
                $candidatas = array_values($filtradas);
            }
        }

        // 3. Filtra por tipo de conta (ACCTTYPE)
        if ($tipoMapeado) {
            foreach ($candidatas as $candidata) {
                if ($candidata->account_type === $tipoMapeado) {
                    return $candidata;
                }
            }
        }

        // 4. Fallback: retorna a primeira candidata (comportamento legado)
        \Log::warning('OFX: Múltiplas entidades encontradas para mesma conta, sem critério para desambiguar', [
            'bank_id' => $bankIdOFX,
            'conta' => $contaOFX,
            'agencia_ofx' => $agenciaOFX,
            'account_type_ofx' => $accountTypeOFX,
            'candidatas' => collect($candidatas)->pluck('id', 'account_type')->toArray(),
        ]);

        return $candidatas[0];
    }

    public function processOfx($file, $usarHorariosMissas = false, $fileHash = null, $fileName = null, $companyId = null)
    {
        ini_set('memory_limit', '512M'); // Aumenta o limite para 512MB

        // Garante que temos um companyId (fallback para sessão apenas como último recurso)
        $companyId = $companyId ?? session('active_company_id');

        // 1. Salvar arquivo no storage
        $path = $file->store('ofx_uploads');
        $ofxPath = Storage::path($path);

        // 2. Ler e tratar o conteúdo
        $contents = file_get_contents($ofxPath);

        // Remove apenas caracteres de controle (0x00-0x1F exceto \r\n\t), preservando acentos e UTF-8
        $contents = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $contents);

        // Remove timezone labels que causam erro no parser (ex: [-3:BRT], [-3:GMT])
        $contents = preg_replace('/\[-?\d+:BRT\]/', '', $contents);
        $contents = preg_replace('/\[-?\d+:GMT\]/', '', $contents);

        // Detecta encoding e converte para UTF-8 apenas se necessário
        // A biblioteca OFX já faz mb_convert_encoding internamente,
        // mas pré-tratamos aqui para garantir que o conteúdo chegue limpo
        $encoding = mb_detect_encoding($contents, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
        if ($encoding && $encoding !== 'UTF-8') {
            $contents = mb_convert_encoding($contents, 'UTF-8', $encoding);
        }

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
                $agenciaOFX     = $account->agencyNumber ?? null; // BRANCHID do OFX (ex: "1851") — nem todos os bancos enviam

                // 7. Busca a entidade usando BANKID + Conta + AccountType + Agência para desambiguação
                $entidade = $this->encontrarEntidade($bankIdOFX, $contaOFX, $companyId, $accountTypeOFX, $agenciaOFX);

                if (!$entidade) {
                    $tipoLabel = $accountTypeOFX ? " ({$accountTypeOFX})" : '';
                    $agLabel   = $agenciaOFX ? " Ag: {$agenciaOFX}," : '';
                    throw new \Exception(
                        "Conta bancária não encontrada! " .
                        "Banco (COMPE): {$bankIdOFX},{$agLabel} Conta: {$contaOFX}{$tipoLabel}. " .
                        "Verifique se a conta está cadastrada no sistema."
                    );
                }

                // 8. Iterar sobre as transações da conta
                $transacoesImportadas = [];
                $transactions = ($account->statement ?? null)?->transactions ?? [];
                foreach ($transactions as $transaction) {
                    $bankStatement = BankStatement::storeTransaction(
                        $account, $transaction, $entidade->id, $fileHash, $fileName, $companyId
                    );
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
        } finally {
            // Limpa o arquivo OFX do storage após processamento (sucesso ou erro)
            Storage::delete($path);
        }

        return [
            'totalTransacoes' => $totalTransacoesImportadas,
            'entidades' => $entidadesImportadas
        ];
    }

}
