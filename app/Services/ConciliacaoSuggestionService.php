<?php

namespace App\Services;

use App\Models\Financeiro\BankStatement;
use App\Models\ConciliacaoRegra;
use App\Models\Financeiro\TransacaoFinanceira;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class ConciliacaoSuggestionService
{
    /**
     * Gera sugestão inteligente para conciliação bancária
     * Hierarquia: Regras > Histórico > Padrões do Sistema
     */
    public function gerarSugestao(BankStatement $conciliacao)
    {
        $memo = Str::upper($conciliacao->memo ?? '');
        $companyId = $conciliacao->company_id;

        // Estrutura padrão de retorno
        $sugestao = [
            'lancamento_padrao_id' => null,
            'tipo_documento'       => null,
            'descricao'            => $this->limparDescricao($memo),
            'cost_center_id'       => null,
            'parceiro_id'          => null,
            'origem_sugestao'      => null, // 'regra', 'historico' ou 'padrao'
            'confianca'            => 0, // 0-100%
        ];

        // NÍVEL 1: Regras Explícitas (maior prioridade vence)
        $regra = $this->buscarRegraAplicavel($companyId, $memo);
        
        if ($regra) {
            $sugestao['lancamento_padrao_id'] = $regra->lancamento_padrao_id;
            $sugestao['cost_center_id'] = $regra->cost_center_id;
            $sugestao['parceiro_id'] = $regra->parceiro_id;
            $sugestao['tipo_documento'] = $regra->tipo_documento;
            $sugestao['descricao'] = $regra->descricao_sugerida ?? $sugestao['descricao'];
            $sugestao['origem_sugestao'] = 'regra';
            $sugestao['confianca'] = 95;
            return $sugestao;
        }

        // NÍVEL 2: Histórico Recente (aprendizado por uso anterior)
        $transacaoAnterior = $this->buscarHistorico($companyId, $memo, $conciliacao->amount_cents);
        
        if ($transacaoAnterior) {
            $sugestao['lancamento_padrao_id'] = $transacaoAnterior->lancamento_padrao_id;
            $sugestao['cost_center_id'] = $transacaoAnterior->cost_center_id;
            $sugestao['origem_sugestao'] = 'historico';
            $sugestao['confianca'] = 70;
            return $sugestao;
        }

        // NÍVEL 3: Padrões do Sistema (regex hardcoded)
        $sugestao['tipo_documento'] = $this->adivinharTipoPorTexto($memo);
        $sugestao['origem_sugestao'] = 'padrao';
        $sugestao['confianca'] = 30;
        return $sugestao;
    }

    /**
     * Busca regra aplicável (sem cache - compatibilidade com database driver)
     */
    private function buscarRegraAplicavel($companyId, $memo)
    {
        // TODO: Implementar cache quando migrar para Redis
        $regras = ConciliacaoRegra::where('company_id', $companyId)
            ->orderBy('prioridade', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return $regras->first(function ($regra) use ($memo) {
            return Str::contains($memo, Str::upper($regra->termo_busca));
        });
    }

    /**
     * Busca no histórico de transações similares
     * Prioriza: texto idêntico > texto similar e valor próximo > apenas texto similar
     */
    private function buscarHistorico($companyId, $memo, $valorCentavos)
    {
        $termoBusca = substr($memo, 0, 20);
        
        // Primeiro: busca por texto E valor próximo (±20%)
        $margemValor = abs($valorCentavos) * 0.2; // 20% de margem (valor absoluto)
        $valorMin = abs($valorCentavos) - $margemValor;
        $valorMax = abs($valorCentavos) + $margemValor;
        
        $transacao = TransacaoFinanceira::where('company_id', $companyId)
            ->where('descricao', 'LIKE', '%' . $termoBusca . '%')
            ->whereBetween('valor', [$valorMin, $valorMax])
            ->whereNotNull('lancamento_padrao_id')
            ->latest()
            ->first();
        
        // Se não encontrar com valor próximo, busca apenas por texto (aprendizado)
        if (!$transacao) {
            $transacao = TransacaoFinanceira::where('company_id', $companyId)
                ->where('descricao', 'LIKE', '%' . $termoBusca . '%')
                ->whereNotNull('lancamento_padrao_id')
                ->latest()
                ->first();
        }
        
        return $transacao;
    }

    /**
     * Remove lixo bancário da descrição
     */
    private function limparDescricao($memo)
    {
        $limpo = trim(Str::remove([
            'PIX ENVIADO',
            'PIX RECEBIDO',
            'TRANSF. TITULARIDADE',
            'PGTO COMPRA',
            'PGTO ',
            'PAYMENT ',
            'TED ',
            'DOC ',
        ], $memo));
        
        return Str::limit($limpo, 100);
    }

    /**
     * Adivinha tipo de documento por palavras-chave
     */
    private function adivinharTipoPorTexto($memo)
    {
        if (Str::contains($memo, ['PIX', 'TRANSF'])) {
            return 'PIX';
        }
        
        if (Str::contains($memo, ['BOLETO', 'COBRANCA', 'BOL '])) {
            return 'BOL - Boleto';
        }
        
        if (Str::contains($memo, ['CARTAO', 'DEBITO', 'DEB '])) {
            return 'DEB - Débito';
        }
        
        if (Str::contains($memo, ['TED', 'DOC'])) {
            return 'T Banc - Transferência Bancaria';
        }
        
        return null;
    }
}
