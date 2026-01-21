<?php

namespace App\Services\Financial;

use App\Models\EntidadeFinanceira;
use App\Models\Financeiro\ModulosAnexo;
use App\Models\Financeiro\TransacaoFinanceira;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransactionDraftService
{
    /**
     * Salva um rascunho de transação financeira a partir dos dados extraídos pela IA
     *
     * @param array $extractedData Dados extraídos pela IA
     * @param string $filePath Caminho do arquivo no storage
     * @param int $companyId ID da empresa (company_id)
     * @return TransacaoFinanceira
     */
    public function saveDraftFromAi(array $extractedData, string $filePath, int $companyId)
    {
        return DB::transaction(function () use ($extractedData, $filePath, $companyId) {
            // 1. Resolver EntidadeFinanceira (fornecedor/cliente)
            $entidade = $this->resolveOrCreateEntidade($extractedData, $companyId);

            // 2. Preparar dados da transação
            $financeiro = $extractedData['financeiro'] ?? [];
            $dataEmissao = $this->parseDate($financeiro['data_emissao'] ?? null);
            $valorTotal = $financeiro['valor_total'] ?? 0.00;
            $juros = $financeiro['juros'] ?? 0.00;
            $multa = $financeiro['multa'] ?? 0.00;
            $desconto = $financeiro['desconto'] ?? 0.00;
            
            // Descrição: usar descrição detalhada se disponível, senão gerar
            $classificacao = $extractedData['classificacao'] ?? [];
            $descricao = $classificacao['descricao_detalhada'] ?? $this->generateDescription($extractedData, $valorTotal);

            // 3. Preparar observações/histórico complementar
            $observacoes = $this->buildObservacoes($extractedData);

            // 4. Criar a Transação Financeira (RASCUNHO)
            $transacao = TransacaoFinanceira::create([
                'company_id' => $companyId,
                'data_competencia' => $dataEmissao ?? now(),
                'data_vencimento' => $dataEmissao ?? now(),
                'entidade_id' => $entidade->id,
                'tipo' => 'saida', // Assumimos que documentos enviados são despesas
                'valor' => $valorTotal,
                'juros' => $juros,
                'multa' => $multa,
                'desconto' => $desconto,
                'descricao' => $descricao,
                'tipo_documento' => $extractedData['tipo_documento'] ?? 'OUTRO',
                'numero_documento' => $financeiro['numero_documento'] ?? null,
                'historico_complementar' => $observacoes,
                'origem' => 'WhatsApp IA',
                'situacao' => null, // Rascunho (sem situação definida)
                'comprovacao_fiscal' => true, // Tem anexo
            ]);

            // 4. Salvar anexo (relacionamento polimórfico)
            $this->attachFile($transacao, $filePath);

            Log::info("Rascunho de transação criado via IA", [
                'transacao_id' => $transacao->id,
                'entidade_id' => $entidade->id,
                'valor' => $valorTotal,
            ]);

            return $transacao;
        });
    }

    /**
     * Resolve ou cria uma EntidadeFinanceira baseado nos dados extraídos
     *
     * @param array $extractedData
     * @param int $companyId
     * @return EntidadeFinanceira
     */
    private function resolveOrCreateEntidade(array $extractedData, int $companyId): EntidadeFinanceira
    {
        $nomeEstabelecimento = $extractedData['estabelecimento']['nome'] ?? 'Fornecedor Diverso';
        $cnpj = $this->cleanCnpj($extractedData['estabelecimento']['cnpj'] ?? null);

        // Tentar encontrar por CNPJ primeiro (se disponível)
        if ($cnpj) {
            // Nota: EntidadeFinanceira não tem campo CNPJ direto, então buscamos por nome
            // Para uma implementação futura, seria ideal adicionar campo CNPJ à tabela
            $entidade = EntidadeFinanceira::where('nome', $nomeEstabelecimento)
                ->where('company_id', $companyId)
                ->first();
            
            if ($entidade) {
                return $entidade;
            }
        }

        // Tentar encontrar por nome
        $entidade = EntidadeFinanceira::where('nome', $nomeEstabelecimento)
            ->where('company_id', $companyId)
            ->first();

        if ($entidade) {
            return $entidade;
        }

        // Criar nova entidade (tipo genérico)
        // Nota: EntidadeFinanceira normalmente é para contas bancárias/caixa,
        // mas usamos como fornecedor temporário. Para produção, considere criar
        // uma tabela separada de fornecedores ou adicionar suporte a fornecedores
        return EntidadeFinanceira::create([
            'nome' => $nomeEstabelecimento,
            'tipo' => 'caixa', // Tipo genérico (pode ser ajustado)
            'company_id' => $companyId,
            'saldo_inicial' => 0,
            'saldo_atual' => 0,
        ]);
    }

    /**
     * Anexa o arquivo à transação usando ModulosAnexo
     *
     * @param TransacaoFinanceira $transacao
     * @param string $filePath
     * @return void
     */
    private function attachFile(TransacaoFinanceira $transacao, string $filePath): void
    {
        $fileName = basename($filePath);

        ModulosAnexo::create([
            'anexavel_id' => $transacao->id,
            'anexavel_type' => TransacaoFinanceira::class,
            'nome_arquivo' => $fileName,
            'caminho_arquivo' => $filePath,
            'tipo_arquivo' => 'application/octet-stream', // Pode ser melhorado detectando MIME
            'status' => 'ativo',
            'data_upload' => now(),
        ]);
    }

    /**
     * Limpa CNPJ (remove caracteres não numéricos)
     *
     * @param string|null $cnpj
     * @return string|null
     */
    private function cleanCnpj(?string $cnpj): ?string
    {
        if (!$cnpj) {
            return null;
        }

        return preg_replace('/[^0-9]/', '', $cnpj);
    }

    /**
     * Faz parse de uma data (YYYY-MM-DD)
     *
     * @param string|null $dateString
     * @return Carbon|null
     */
    private function parseDate(?string $dateString): ?Carbon
    {
        if (!$dateString) {
            return null;
        }

        try {
            return Carbon::parse($dateString);
        } catch (\Exception $e) {
            Log::warning("Erro ao fazer parse da data: {$dateString}", [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Gera uma descrição baseada nos dados extraídos
     *
     * @param array $extractedData
     * @param float $valor
     * @return string
     */
    private function generateDescription(array $extractedData, float $valor): string
    {
        $nomeEstabelecimento = $extractedData['estabelecimento']['nome'] ?? 'Fornecedor';
        $tipoDocumento = $extractedData['tipo_documento'] ?? 'Documento';

        return "{$tipoDocumento} - {$nomeEstabelecimento} (R$ " . number_format($valor, 2, ',', '.') . ")";
    }

    /**
     * Constrói observações/histórico complementar baseado nos dados extraídos
     *
     * @param array $extractedData
     * @return string|null
     */
    private function buildObservacoes(array $extractedData): ?string
    {
        $observacoes = [];
        
        // Observações da IA
        if (!empty($extractedData['observacoes'])) {
            $observacoes[] = $extractedData['observacoes'];
        }
        
        // Parcelamento
        $parcelamento = $extractedData['parcelamento'] ?? [];
        if (!empty($parcelamento['is_parcelado']) && $parcelamento['total_parcelas'] > 1) {
            $observacoes[] = "📦 Parcelamento: {$parcelamento['parcela_atual']}/{$parcelamento['total_parcelas']}";
        }
        
        // Código de referência
        $classificacao = $extractedData['classificacao'] ?? [];
        if (!empty($classificacao['codigo_referencia'])) {
            $observacoes[] = "Código: {$classificacao['codigo_referencia']}";
        }
        
        // Categoria sugerida
        if (!empty($classificacao['categoria_sugerida'])) {
            $observacoes[] = "Categoria sugerida: {$classificacao['categoria_sugerida']}";
        }
        
        return !empty($observacoes) ? implode("\n", $observacoes) : null;
    }
}

