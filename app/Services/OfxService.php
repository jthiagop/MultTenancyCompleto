<?php

namespace App\Services;

use App\Models\EntidadeFinanceira;
use App\Models\Financeiro\BankStatement;
use Endeken\OFX\OFX;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class OfxService
{
    public function processOfx($file)
    {
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

        // 4. Verifica se a conta bancária está cadastrada na tabela `entidades_financeiras`
        foreach ($parsedData->bankAccounts as $account) {
            $bancoId      = $account->routingNumber;   // BANKID do OFX
            $agencia      = $account->agencyNumber;    // BRANCHID do OFX
            $conta        = $account->accountNumber;   // ACCTID do OFX
            $companyId    = Auth::user()->company_id; // Empresa do usuário logado

            //5. Verifica se a conta está cadastrada no banco de dados
            $entidade = EntidadeFinanceira::where('agencia', $agencia)
                ->where('conta', $conta)
                ->where('company_id', $companyId)
                ->first();

            if (!$entidade) {
                throw new \Exception("Conta bancária não cadastrada no sistema! Banco: $bancoId, Agência: $agencia, Conta: $conta.");
            }

        if (empty($parsedData->bankAccounts)) {
            throw new \Exception('Nenhuma conta bancária encontrada no arquivo OFX.');
        }

        // 6. Iterar sobre as contas e transações
            // 6. Processa as transações e vincula à entidade financeira
            foreach ($account->statement->transactions ?? [] as $transaction) {
                BankStatement::storeTransaction($account, $transaction, $entidade->id);
            }
    }
}

}
