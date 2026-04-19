<?php

namespace App\Services;

use App\Enums\StatusDomusDocumento;
use App\Models\DomusDocumento;
use App\Models\Financeiro\ModulosAnexo;
use App\Models\Financeiro\TransacaoFinanceira;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Vincula documento Domus IA a lançamentos: escopo por company, status lancado e anexo em modulos_anexos.
 */
class DomusDocumentoLancamentoService
{
    public function __construct(
        private DocumentViewerService $documentViewer
    ) {}

    /**
     * Busca documento Domus garantindo company da sessão ativa (evita IDOR).
     */
    public function findForActiveCompany(?int $id): ?DomusDocumento
    {
        if ($id === null || $id < 1) {
            return null;
        }

        $companyId = session('active_company_id');
        if (! $companyId) {
            return null;
        }

        return $this->documentViewer->getDocument($id, (int) $companyId);
    }

    public function markLancadoAndAttachAnexo(DomusDocumento $domusDoc, TransacaoFinanceira $transacao): void
    {
        $domusDoc->update(['status' => StatusDomusDocumento::LANCADO]);
        $this->attachDomusFileAsAnexo($domusDoc, $transacao);
    }

    public function attachDomusFileAsAnexo(DomusDocumento $domusDoc, TransacaoFinanceira $transacao): void
    {
        try {
            if (empty($domusDoc->caminho_arquivo)) {
                Log::warning('DomusDocumento sem caminho de arquivo', ['id' => $domusDoc->id]);

                return;
            }

            $tipoAnexo = match ($domusDoc->tipo_documento) {
                'NF-e', 'NFC-e', 'NOTA_FISCAL' => 'nota_fiscal',
                'CUPOM', 'CUPOM_FISCAL' => 'cupom_fiscal',
                'BOLETO' => 'boleto',
                'RECIBO' => 'recibo',
                'FATURA_CARTAO' => 'fatura',
                'COMPROVANTE' => 'comprovante',
                default => 'documento',
            };

            $descricao = 'Documento importado via Domus IA';
            if ($domusDoc->estabelecimento_nome) {
                $descricao .= ' - '.$domusDoc->estabelecimento_nome;
            }
            if ($domusDoc->tipo_documento) {
                $descricao = $domusDoc->tipo_documento.' - '.$descricao;
            }

            ModulosAnexo::create([
                'anexavel_id' => $transacao->id,
                'anexavel_type' => TransacaoFinanceira::class,
                'forma_anexo' => 'arquivo',
                'nome_arquivo' => $domusDoc->nome_arquivo,
                'caminho_arquivo' => $domusDoc->caminho_arquivo,
                'tipo_arquivo' => $domusDoc->tipo_arquivo ?? '',
                'extensao_arquivo' => pathinfo($domusDoc->nome_arquivo, PATHINFO_EXTENSION),
                'mime_type' => $domusDoc->mime_type ?? '',
                'tamanho_arquivo' => $domusDoc->tamanho_arquivo ?? 0,
                'tipo_anexo' => $tipoAnexo,
                'descricao' => $descricao,
                'status' => 'ativo',
                'data_upload' => now(),
                'created_by' => Auth::id(),
                'created_by_name' => Auth::user()->name ?? 'Sistema',
            ]);

            Log::info('Anexo criado automaticamente a partir do DomusDocumento', [
                'domus_documento_id' => $domusDoc->id,
                'transacao_id' => $transacao->id,
                'arquivo' => $domusDoc->nome_arquivo,
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao anexar documento Domus à transação: '.$e->getMessage(), [
                'domus_documento_id' => $domusDoc->id,
                'transacao_id' => $transacao->id,
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
