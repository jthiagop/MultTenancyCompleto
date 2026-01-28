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
     * Novo método genérico que atende tanto AJAX quanto OFX
     */
    public function sugerirPorDados(int $companyId, ?string $descricao = null, ?int $parceiroId = null, ?float $valor = null)
    {
        $memo = $descricao ? Str::upper($descricao) : '';

        // Estrutura padrão de retorno
        $sugestao = [
            'lancamento_padrao_id' => null,
            'cost_center_id'       => null,
            'parceiro_id'          => $parceiroId, // Mantém o que veio se houver
            'tipo_documento'       => null,
            'descricao'            => $memo ? $this->limparDescricao($memo) : null,
            'confianca'            => 0,
            'origem_sugestao'      => null
        ];

        // --- NÍVEL 1: Regras Explícitas (Prioridade Máxima) ---
        
        // 1.1 Regra por Parceiro
        // Se escolheu parceiro, verifica se tem regra vinculada a ele
        if ($parceiroId) {
            $regraParceiro = ConciliacaoRegra::where('company_id', $companyId)
                ->where('parceiro_id', $parceiroId)
                ->orderBy('prioridade', 'desc')
                ->first();

            if ($regraParceiro) {
                return $this->formatarRetornoRegra($sugestao, $regraParceiro);
            }
        }

        // 1.2 Regra por Texto
        if ($memo) {
            $regraTexto = $this->buscarRegraAplicavel($companyId, $memo);
            if ($regraTexto) {
                return $this->formatarRetornoRegra($sugestao, $regraTexto);
            }
        }

        // --- NÍVEL 2: Histórico Recente (Aprendizado) ---

        // 2.1 Histórico por Parceiro
        if ($parceiroId) {
            $ultimoLancamento = TransacaoFinanceira::where('company_id', $companyId)
                ->where('parceiro_id', $parceiroId)
                ->whereNotNull('lancamento_padrao_id')
                ->latest()
                ->first();

            if ($ultimoLancamento) {
                $sugestao['lancamento_padrao_id'] = $ultimoLancamento->lancamento_padrao_id;
                $sugestao['cost_center_id'] = $ultimoLancamento->cost_center_id;
                $sugestao['tipo_documento'] = $ultimoLancamento->tipo_documento;
                // Sugerimos a descrição usada anteriormente se o campo atual estiver vazio
                if (empty($sugestao['descricao'])) {
                    $sugestao['descricao'] = $ultimoLancamento->descricao;
                }
                $sugestao['confianca'] = 80;
                $sugestao['origem_sugestao'] = 'historico_parceiro';
                return $sugestao;
            }
        }

        // 2.2 Histórico por Texto e Valor
        if ($memo) {
            $valorCentavos = $valor ? ($valor * 100) : 0;
            $transacaoAnterior = $this->buscarHistorico($companyId, $memo, $valorCentavos);
            
            if ($transacaoAnterior) {
                $sugestao['lancamento_padrao_id'] = $transacaoAnterior->lancamento_padrao_id;
                $sugestao['cost_center_id'] = $transacaoAnterior->cost_center_id;
                $sugestao['origem_sugestao'] = 'historico_texto';
                $sugestao['confianca'] = 70;
                return $sugestao;
            }
        }

        // NÍVEL 3: Padrões do Sistema
        if ($memo) {
            $sugestao['tipo_documento'] = $this->adivinharTipoPorTexto($memo);
            $sugestao['origem_sugestao'] = 'padrao';
            $sugestao['confianca'] = 30;
        }

        return $sugestao;
    }

    // Helper para formatar retorno de regra
    private function formatarRetornoRegra($sugestao, $regra)
    {
        $sugestao['lancamento_padrao_id'] = $regra->lancamento_padrao_id;
        $sugestao['cost_center_id'] = $regra->cost_center_id;
        $sugestao['parceiro_id'] = $regra->parceiro_id ?? $sugestao['parceiro_id'];
        $sugestao['tipo_documento'] = $regra->tipo_documento;
        $sugestao['descricao'] = $regra->descricao_sugerida ?? $sugestao['descricao'];
        $sugestao['origem_sugestao'] = 'regra';
        $sugestao['confianca'] = 95;
        return $sugestao;
    }

    /**
     * Mantém compatibilidade com o código antigo de Conciliação OFX
     */
    public function gerarSugestao(BankStatement $conciliacao)
    {
        return $this->sugerirPorDados(
            $conciliacao->company_id,
            $conciliacao->memo,
            null, // OFX geralmente não tem parceiro vinculado ainda
            (float) $conciliacao->amount
        );
    }

    /**
     * Busca regra aplicável (sem cache - compatibilidade com database driver)
     */
    private function buscarRegraAplicavel($companyId, $memo)
    {
        // TODO: Implementar cache quando migrar para Redis
        $regras = ConciliacaoRegra::where('company_id', $companyId)
            ->whereNotNull('termo_busca')
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
        $valorMin = (abs($valorCentavos) - $margemValor) / 100;
        $valorMax = (abs($valorCentavos) + $margemValor) / 100;
        
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
