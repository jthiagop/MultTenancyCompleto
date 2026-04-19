<?php

namespace App\Services;

use App\Models\ConciliacaoFeedback;
use App\Models\ConciliacaoRegra;
use App\Models\Financeiro\BankStatement;
use App\Models\Financeiro\TransacaoFinanceira;
use App\Models\Parceiro;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ConciliacaoSuggestionService
{
    public function sugerirPorDados(int $companyId, ?string $descricao = null, ?int $parceiroId = null, ?float $valor = null): array
    {
        $memo = $descricao ? Str::upper($descricao) : '';

        $sugestao = [
            'lancamento_padrao_id' => null,
            'cost_center_id'       => null,
            'parceiro_id'          => $parceiroId,
            'tipo_documento'       => null,
            'descricao'            => $memo ? $this->limparDescricao($memo) : null,
            'confianca'            => 0,
            'origem_sugestao'      => null,
            'confianca_campos'     => [
                'lancamento_padrao_id' => 0,
                'cost_center_id'       => 0,
                'tipo_documento'       => 0,
                'descricao'            => 0,
                'parceiro_id'          => 0,
            ],
        ];

        // NER: extrair parceiro do memo se não foi fornecido
        if (!$parceiroId && $memo) {
            $parceiroExtraido = $this->extrairParceiroDoMemo($memo, $companyId);
            if ($parceiroExtraido) {
                $parceiroId = $parceiroExtraido;
                $sugestao['parceiro_id'] = $parceiroId;
                $sugestao['confianca_campos']['parceiro_id'] = 65;
            }
        }

        // --- NÍVEL 1: Regras Explícitas (Prioridade Máxima) ---
        if ($parceiroId) {
            $regraParceiro = $this->buscarRegraAplicavel($companyId, null, $parceiroId);
            if ($regraParceiro) {
                return $this->formatarRetornoRegra($sugestao, $regraParceiro);
            }
        }

        if ($memo) {
            $regraTexto = $this->buscarRegraAplicavel($companyId, $memo);
            if ($regraTexto) {
                return $this->formatarRetornoRegra($sugestao, $regraTexto);
            }
        }

        // --- NÍVEL 2: Histórico Recente (Aprendizado) ---
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
                if (empty($sugestao['descricao'])) {
                    $sugestao['descricao'] = $ultimoLancamento->descricao;
                }
                $sugestao['confianca'] = 80;
                $sugestao['origem_sugestao'] = 'historico_parceiro';
                $sugestao['confianca_campos'] = [
                    'lancamento_padrao_id' => 80,
                    'cost_center_id'       => 80,
                    'tipo_documento'       => 60,
                    'descricao'            => 50,
                    'parceiro_id'          => $sugestao['confianca_campos']['parceiro_id'] ?: 80,
                ];
                return $this->ajustarConfiancaPorFeedback($sugestao, $companyId);
            }
        }

        if ($memo) {
            $valorCentavos = $valor ? ($valor * 100) : 0;
            $transacaoAnterior = $this->buscarHistorico($companyId, $memo, $valorCentavos);

            if ($transacaoAnterior) {
                $sugestao['lancamento_padrao_id'] = $transacaoAnterior->lancamento_padrao_id;
                $sugestao['cost_center_id'] = $transacaoAnterior->cost_center_id;
                $sugestao['parceiro_id'] = $transacaoAnterior->parceiro_id ?? $sugestao['parceiro_id'];
                $sugestao['origem_sugestao'] = 'historico_texto';
                $sugestao['confianca'] = 70;
                $sugestao['confianca_campos'] = [
                    'lancamento_padrao_id' => 70,
                    'cost_center_id'       => 70,
                    'tipo_documento'       => 0,
                    'descricao'            => 40,
                    'parceiro_id'          => $transacaoAnterior->parceiro_id ? 60 : ($sugestao['confianca_campos']['parceiro_id'] ?? 0),
                ];
                return $this->ajustarConfiancaPorFeedback($sugestao, $companyId);
            }
        }

        // NÍVEL 3: Padrões do Sistema
        if ($memo) {
            $sugestao['tipo_documento'] = $this->adivinharTipoPorTexto($memo);
            $sugestao['origem_sugestao'] = 'padrao';
            $sugestao['confianca'] = 30;
            $sugestao['confianca_campos'] = [
                'lancamento_padrao_id' => 0,
                'cost_center_id'       => 0,
                'tipo_documento'       => $sugestao['tipo_documento'] ? 30 : 0,
                'descricao'            => $sugestao['descricao'] ? 20 : 0,
                'parceiro_id'          => $sugestao['confianca_campos']['parceiro_id'] ?? 0,
            ];
        }

        return $this->ajustarConfiancaPorFeedback($sugestao, $companyId);
    }

    private function formatarRetornoRegra(array $sugestao, $regra): array
    {
        $sugestao['lancamento_padrao_id'] = $regra->lancamento_padrao_id;
        $sugestao['cost_center_id'] = $regra->cost_center_id;
        $sugestao['parceiro_id'] = $regra->parceiro_id ?? $sugestao['parceiro_id'];
        $sugestao['tipo_documento'] = $regra->tipo_documento;
        $sugestao['descricao'] = $regra->descricao_sugerida ?? $sugestao['descricao'];
        $sugestao['origem_sugestao'] = 'regra';
        $sugestao['confianca'] = 95;
        $sugestao['confianca_campos'] = [
            'lancamento_padrao_id' => $regra->lancamento_padrao_id ? 95 : 0,
            'cost_center_id'       => $regra->cost_center_id ? 95 : 0,
            'tipo_documento'       => $regra->tipo_documento ? 95 : 0,
            'descricao'            => $regra->descricao_sugerida ? 90 : ($sugestao['descricao'] ? 50 : 0),
            'parceiro_id'          => $regra->parceiro_id ? 95 : ($sugestao['confianca_campos']['parceiro_id'] ?? 0),
        ];
        return $sugestao;
    }

    /**
     * Ajusta confiança baseado no feedback histórico do usuário.
     * Se o usuario rejeitou sugestoes similares repetidamente, reduz confianca.
     */
    private function ajustarConfiancaPorFeedback(array $sugestao, int $companyId): array
    {
        if (!$sugestao['origem_sugestao']) {
            return $sugestao;
        }

        try {
            $campos = ['lancamento_padrao_id', 'cost_center_id', 'tipo_documento', 'parceiro_id'];

            foreach ($campos as $campo) {
                $valorSugerido = $sugestao[$campo] ?? null;
                if (!$valorSugerido) continue;

                $feedback = ConciliacaoFeedback::where('company_id', $companyId)
                    ->where('campo', $campo)
                    ->where('valor_sugerido', (string) $valorSugerido)
                    ->where('origem_sugestao', $sugestao['origem_sugestao'])
                    ->selectRaw('COUNT(*) as total, SUM(CASE WHEN aceito = 0 THEN 1 ELSE 0 END) as rejeitados')
                    ->first();

                if ($feedback && $feedback->total >= 3) {
                    $taxaRejeicao = $feedback->rejeitados / $feedback->total;
                    if ($taxaRejeicao > 0.5) {
                        $reducao = (int) round($taxaRejeicao * 30);
                        $sugestao['confianca_campos'][$campo] = max(0, ($sugestao['confianca_campos'][$campo] ?? 0) - $reducao);
                    }
                }
            }

            $sugestao['confianca'] = max(0, ...array_values($sugestao['confianca_campos']));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Feedback de sugestão indisponível: ' . $e->getMessage());
        }

        return $sugestao;
    }

    // ── NER: extrair parceiro do memo bancário ──────────────────────────

    private function extrairParceiroDoMemo(string $memo, int $companyId): ?int
    {
        // Tentar CPF (11 digitos seguidos)
        if (preg_match('/\b(\d{3}\.?\d{3}\.?\d{3}-?\d{2})\b/', $memo, $m)) {
            $cpf = preg_replace('/\D/', '', $m[1]);
            $parceiro = Parceiro::where('company_id', $companyId)->where('cpf', $cpf)->first();
            if ($parceiro) return $parceiro->id;
        }

        // Tentar CNPJ (14 digitos seguidos)
        if (preg_match('/\b(\d{2}\.?\d{3}\.?\d{3}\/?\d{4}-?\d{2})\b/', $memo, $m)) {
            $cnpj = preg_replace('/\D/', '', $m[1]);
            $parceiro = Parceiro::where('company_id', $companyId)->where('cnpj', $cnpj)->first();
            if ($parceiro) return $parceiro->id;
        }

        // Tentar por sequencia de 11 digitos sem formatacao (CPF puro)
        if (preg_match('/\b(\d{11})\b/', $memo, $m)) {
            $cpf = $m[1];
            $parceiro = Parceiro::where('company_id', $companyId)->where('cpf', $cpf)->first();
            if ($parceiro) return $parceiro->id;
        }

        // Tentar por sequencia de 14 digitos sem formatacao (CNPJ puro)
        if (preg_match('/\b(\d{14})\b/', $memo, $m)) {
            $cnpj = $m[1];
            $parceiro = Parceiro::where('company_id', $companyId)->where('cnpj', $cnpj)->first();
            if ($parceiro) return $parceiro->id;
        }

        // Fallback: extrair nome (ultimos tokens apos numeros/data)
        $cleaned = preg_replace('/\d{2}\/\d{2}\s+\d{2}:\d{2}/', '', $memo);
        $cleaned = preg_replace('/\d+/', '', $cleaned);
        $cleaned = preg_replace('/\b(PIX|ENVIADO|RECEBIDO|TRANSF|TED|DOC|PGTO|PAYMENT|TITULARIDADE|COMPRA)\b/i', '', $cleaned);
        $cleaned = trim(preg_replace('/[\s\-]+/', ' ', $cleaned));

        if (strlen($cleaned) >= 4) {
            $parceiro = Parceiro::where('company_id', $companyId)
                ->where('nome', 'LIKE', '%' . $cleaned . '%')
                ->first();
            if ($parceiro) return $parceiro->id;
        }

        return null;
    }

    // ── Compatibilidade OFX ─────────────────────────────────────────────

    public function gerarSugestao(BankStatement $conciliacao): array
    {
        return $this->sugerirPorDados(
            $conciliacao->company_id,
            $conciliacao->memo,
            null,
            (float) $conciliacao->amount
        );
    }

    // ── Busca de regras (com cache) ─────────────────────────────────────

    private function buscarRegraAplicavel(int $companyId, ?string $memo = null, ?int $parceiroId = null)
    {
        if ($parceiroId) {
            return $this->getRegrasCached($companyId)
                ->where('parceiro_id', $parceiroId)
                ->sortByDesc('prioridade')
                ->first();
        }

        if ($memo) {
            return $this->getRegrasCached($companyId)
                ->filter(fn ($regra) => $regra->termo_busca && Str::contains($memo, Str::upper($regra->termo_busca)))
                ->sortByDesc('prioridade')
                ->first();
        }

        return null;
    }

    private function getRegrasCached(int $companyId)
    {
        $cacheKey = "conciliacao_regras:{$companyId}";

        return Cache::remember($cacheKey, 300, function () use ($companyId) {
            try {
                return ConciliacaoRegra::where('company_id', $companyId)
                    ->orderBy('prioridade', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->get();
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Tabela conciliacao_regras indisponível: ' . $e->getMessage());
                return collect();
            }
        });
    }

    public static function invalidarCacheRegras(int $companyId): void
    {
        Cache::forget("conciliacao_regras:{$companyId}");
    }

    // ── Histórico ───────────────────────────────────────────────────────

    private function buscarHistorico(int $companyId, string $memo, float $valorCentavos)
    {
        $termoBusca = substr($memo, 0, 20);

        $margemValor = abs($valorCentavos) * 0.2;
        $valorMin = (abs($valorCentavos) - $margemValor) / 100;
        $valorMax = (abs($valorCentavos) + $margemValor) / 100;

        $transacao = TransacaoFinanceira::where('company_id', $companyId)
            ->where('descricao', 'LIKE', '%' . $termoBusca . '%')
            ->whereBetween('valor', [$valorMin, $valorMax])
            ->whereNotNull('lancamento_padrao_id')
            ->latest()
            ->first();

        if (!$transacao) {
            $transacao = TransacaoFinanceira::where('company_id', $companyId)
                ->where('descricao', 'LIKE', '%' . $termoBusca . '%')
                ->whereNotNull('lancamento_padrao_id')
                ->latest()
                ->first();
        }

        return $transacao;
    }

    // ── Helpers ──────────────────────────────────────────────────────────

    private function limparDescricao(string $memo): string
    {
        $limpo = trim(Str::remove([
            'PIX ENVIADO', 'PIX RECEBIDO', 'TRANSF. TITULARIDADE',
            'PGTO COMPRA', 'PGTO ', 'PAYMENT ', 'TED ', 'DOC ',
        ], $memo));

        return Str::limit($limpo, 100);
    }

    private function adivinharTipoPorTexto(string $memo): ?string
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

    // ── Dashboard de acurácia ───────────────────────────────────────────

    public function getDashboardData(int $companyId): array
    {
        try {
            $total = ConciliacaoFeedback::where('company_id', $companyId)->count();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Tabela conciliacao_feedback indisponível: ' . $e->getMessage());
            return ['insuficiente' => true, 'total_registros' => 0, 'minimo' => 20];
        }

        if ($total < 20) {
            return ['insuficiente' => true, 'total_registros' => $total, 'minimo' => 20];
        }

        $aceitos = ConciliacaoFeedback::where('company_id', $companyId)->where('aceito', true)->count();
        $taxaGeral = $total > 0 ? round(($aceitos / $total) * 100, 1) : 0;

        $porCampo = ConciliacaoFeedback::where('company_id', $companyId)
            ->selectRaw("campo, COUNT(*) as total, SUM(CASE WHEN aceito = 1 THEN 1 ELSE 0 END) as aceitos")
            ->groupBy('campo')
            ->get()
            ->mapWithKeys(fn ($row) => [
                $row->campo => [
                    'total'     => $row->total,
                    'aceitos'   => (int) $row->aceitos,
                    'taxa'      => $row->total > 0 ? round(($row->aceitos / $row->total) * 100, 1) : 0,
                ],
            ]);

        $porOrigem = ConciliacaoFeedback::where('company_id', $companyId)
            ->whereNotNull('origem_sugestao')
            ->selectRaw("origem_sugestao, COUNT(*) as total, SUM(CASE WHEN aceito = 1 THEN 1 ELSE 0 END) as aceitos")
            ->groupBy('origem_sugestao')
            ->get()
            ->mapWithKeys(fn ($row) => [
                $row->origem_sugestao => [
                    'total'   => $row->total,
                    'aceitos' => (int) $row->aceitos,
                    'taxa'    => $row->total > 0 ? round(($row->aceitos / $row->total) * 100, 1) : 0,
                ],
            ]);

        $mensal = ConciliacaoFeedback::where('company_id', $companyId)
            ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as mes, COUNT(*) as total, SUM(CASE WHEN aceito = 1 THEN 1 ELSE 0 END) as aceitos")
            ->groupBy('mes')
            ->orderBy('mes')
            ->get()
            ->map(fn ($row) => [
                'mes'     => $row->mes,
                'total'   => $row->total,
                'aceitos' => (int) $row->aceitos,
                'taxa'    => $row->total > 0 ? round(($row->aceitos / $row->total) * 100, 1) : 0,
            ]);

        $topRejeitados = ConciliacaoFeedback::where('company_id', $companyId)
            ->where('aceito', false)
            ->selectRaw("campo, valor_sugerido, COUNT(*) as rejeicoes")
            ->groupBy('campo', 'valor_sugerido')
            ->orderByDesc('rejeicoes')
            ->limit(5)
            ->get();

        return [
            'insuficiente'  => false,
            'total'         => $total,
            'taxa_geral'    => $taxaGeral,
            'por_campo'     => $porCampo,
            'por_origem'    => $porOrigem,
            'mensal'        => $mensal,
            'top_rejeitados' => $topRejeitados,
        ];
    }

    // ── Registrar feedback ──────────────────────────────────────────────

    public function registrarFeedback(int $companyId, int $bankStatementId, array $sugestaoOriginal, array $dadosEscolhidos): void
    {
        try {
            $campos = [
                'lancamento_padrao_id' => 'lancamento_padrao_id',
                'cost_center_id'       => 'cost_center_id',
                'tipo_documento'       => 'tipo_documento',
                'descricao'            => 'descricao',
                'parceiro_id'          => 'fornecedor_id',
            ];

            $confianca = (int) ($sugestaoOriginal['sug_confianca'] ?? 0);
            $origem = $sugestaoOriginal['sug_origem'] ?? null;

            foreach ($campos as $campoSug => $campoReq) {
                $sugerido = $sugestaoOriginal["sug_{$campoSug}"] ?? null;
                if (!$sugerido) continue;

                $escolhido = $dadosEscolhidos[$campoReq] ?? $dadosEscolhidos[$campoSug] ?? null;
                $aceito = (string) $sugerido === (string) $escolhido;

                ConciliacaoFeedback::create([
                    'company_id'        => $companyId,
                    'bank_statement_id' => $bankStatementId,
                    'campo'             => $campoSug,
                    'valor_sugerido'    => (string) $sugerido,
                    'valor_escolhido'   => $escolhido ? (string) $escolhido : null,
                    'aceito'            => $aceito,
                    'confianca_original' => $confianca,
                    'origem_sugestao'   => $origem,
                ]);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Não foi possível registrar feedback: ' . $e->getMessage());
        }
    }
}
