<?php

namespace App\Services;

/**
 * Detector de Movimentações Internas Bancárias
 *
 * Identifica automaticamente movimentações internas de bancos (aplicações automáticas,
 * resgates, transferências entre contas do mesmo banco) pelo memo/descrição do OFX.
 *
 * Exemplos detectados:
 * - Banco do Brasil: Rende Fácil, Poupança, BB RF Curto Prazo
 * - Caixa: Caixa Fácil, Poupança Automática
 * - Itaú: Super Poupança, Investimento Automático
 * - Bradesco: Poupe Fácil
 *
 * @package App\Services
 */
class MovimentacaoInternaDetector
{
    /**
     * Padrões conhecidos de movimentações internas bancárias
     *
     * Estrutura: regex => [
     *     'destino' => Nome amigável do destino,
     *     'account_type' => Tipo de conta sugerido (aplicacao, poupanca, renda_fixa, etc.),
     *     'banco' => Código do banco (opcional, para matching mais preciso)
     * ]
     */
    private const PADROES = [
        // ═══════════════════════════════════════════════════════════════
        // BANCO DO BRASIL (001)
        // ═══════════════════════════════════════════════════════════════
        '/RENDE\s*FACIL\s*(APL|APLIC|APLICACAO)?/i' => [
            'destino' => 'Rende Fácil',
            'account_type' => 'aplicacao',
            'banco' => '001',
        ],
        '/RENDE\s*FACIL\s*(RES|RESG|RESGATE)/i' => [
            'destino' => 'Rende Fácil',
            'account_type' => 'aplicacao',
            'banco' => '001',
            'force_tipo' => 'resgate',
        ],
        '/APLIC\.?\s*RENDE\s*FACIL/i' => [
            'destino' => 'Rende Fácil',
            'account_type' => 'aplicacao',
            'banco' => '001',
        ],
        '/RESGATE?\s*RENDE\s*FACIL/i' => [
            'destino' => 'Rende Fácil',
            'account_type' => 'aplicacao',
            'banco' => '001',
            'force_tipo' => 'resgate',
        ],
        '/BB\s*RF\s*CURTO\s*PRAZO/i' => [
            'destino' => 'BB RF Curto Prazo',
            'account_type' => 'renda_fixa',
            'banco' => '001',
        ],
        '/BB\s*RENDA\s*FIXA/i' => [
            'destino' => 'BB Renda Fixa',
            'account_type' => 'renda_fixa',
            'banco' => '001',
        ],
        '/POUP\.?\s*(BB|BANCO\s*BRASIL)/i' => [
            'destino' => 'Poupança BB',
            'account_type' => 'poupanca',
            'banco' => '001',
        ],
        '/BB\s*POUPANCA/i' => [
            'destino' => 'Poupança BB',
            'account_type' => 'poupanca',
            'banco' => '001',
        ],

        // ═══════════════════════════════════════════════════════════════
        // CAIXA ECONÔMICA FEDERAL (104)
        // ═══════════════════════════════════════════════════════════════
        '/CAIXA\s*FACIL/i' => [
            'destino' => 'Caixa Fácil',
            'account_type' => 'aplicacao',
            'banco' => '104',
        ],
        '/POUP\.?\s*(CAIXA|CEF)/i' => [
            'destino' => 'Poupança Caixa',
            'account_type' => 'poupanca',
            'banco' => '104',
        ],
        '/CEF\s*POUPANCA/i' => [
            'destino' => 'Poupança Caixa',
            'account_type' => 'poupanca',
            'banco' => '104',
        ],

        // ═══════════════════════════════════════════════════════════════
        // ITAÚ (341)
        // ═══════════════════════════════════════════════════════════════
        '/SUPER\s*POUPE/i' => [
            'destino' => 'Super Poupança Itaú',
            'account_type' => 'poupanca',
            'banco' => '341',
        ],
        '/ITAU\s*POUPANCA/i' => [
            'destino' => 'Poupança Itaú',
            'account_type' => 'poupanca',
            'banco' => '341',
        ],
        '/INVEST\.?\s*FACIL\s*ITAU/i' => [
            'destino' => 'Invest Fácil Itaú',
            'account_type' => 'aplicacao',
            'banco' => '341',
        ],

        // ═══════════════════════════════════════════════════════════════
        // BRADESCO (237)
        // ═══════════════════════════════════════════════════════════════
        '/POUPE\s*FACIL/i' => [
            'destino' => 'Poupe Fácil Bradesco',
            'account_type' => 'poupanca',
            'banco' => '237',
        ],
        '/BRADESCO\s*POUPANCA/i' => [
            'destino' => 'Poupança Bradesco',
            'account_type' => 'poupanca',
            'banco' => '237',
        ],

        // ═══════════════════════════════════════════════════════════════
        // SANTANDER (033)
        // ═══════════════════════════════════════════════════════════════
        '/SANTANDER\s*POUPANCA/i' => [
            'destino' => 'Poupança Santander',
            'account_type' => 'poupanca',
            'banco' => '033',
        ],

        // ═══════════════════════════════════════════════════════════════
        // PADRÕES GENÉRICOS (qualquer banco)
        // ═══════════════════════════════════════════════════════════════
        '/APLIC\.?\s*POUPANCA/i' => [
            'destino' => 'Poupança',
            'account_type' => 'poupanca',
        ],
        '/RESGATE?\s*POUPANCA/i' => [
            'destino' => 'Poupança',
            'account_type' => 'poupanca',
            'force_tipo' => 'resgate',
        ],
        '/APLIC\.?\s*AUTOMATICA/i' => [
            'destino' => 'Aplicação Automática',
            'account_type' => 'aplicacao',
        ],
        '/RESGATE?\s*AUTOMATIC[OA]/i' => [
            'destino' => 'Aplicação Automática',
            'account_type' => 'aplicacao',
            'force_tipo' => 'resgate',
        ],
        '/APLIC\.?\s*CDB/i' => [
            'destino' => 'CDB',
            'account_type' => 'renda_fixa',
        ],
        '/RESGATE?\s*CDB/i' => [
            'destino' => 'CDB',
            'account_type' => 'renda_fixa',
            'force_tipo' => 'resgate',
        ],
        '/APLIC\.?\s*FUNDOS?/i' => [
            'destino' => 'Fundo de Investimento',
            'account_type' => 'aplicacao',
        ],
        '/RESGATE?\s*FUNDOS?/i' => [
            'destino' => 'Fundo de Investimento',
            'account_type' => 'aplicacao',
            'force_tipo' => 'resgate',
        ],
        '/TESOURO\s*DIRETO/i' => [
            'destino' => 'Tesouro Direto',
            'account_type' => 'tesouro_direto',
        ],
        '/TRANSF\.?\s*ENTRE\s*CONTAS?/i' => [
            'destino' => 'Transferência Interna',
            'account_type' => null,
        ],
        '/TRANSF\.?\s*MESMA\s*TITULARIDADE/i' => [
            'destino' => 'Transferência Mesma Titularidade',
            'account_type' => null,
        ],
        '/MOVIM\.?\s*INTERNA/i' => [
            'destino' => 'Movimentação Interna',
            'account_type' => null,
        ],
    ];

    /**
     * Detecta se uma movimentação bancária é interna
     *
     * @param string $memo Descrição/memo do OFX
     * @param float|null $amount Valor da transação (negativo = débito/aplicação, positivo = crédito/resgate)
     * @return array|null Retorna array com detalhes ou null se não detectado
     *
     * Estrutura do retorno:
     * [
     *     'tipo' => 'aplicacao' | 'resgate',
     *     'destino' => 'Rende Fácil',
     *     'account_type' => 'aplicacao' | 'poupanca' | 'renda_fixa' | 'tesouro_direto' | null,
     *     'banco' => '001' | null,
     *     'memo' => 'RENDE FACIL APL',
     *     'acao_label' => 'Aplicação → Rende Fácil',
     *     'icone' => 'fa-arrow-right' | 'fa-arrow-left',
     *     'cor' => 'info' | 'success',
     * ]
     */
    public static function detectar(string $memo, ?float $amount = null): ?array
    {
        $memo = trim($memo);

        if (empty($memo)) {
            return null;
        }

        foreach (self::PADROES as $regex => $config) {
            if (preg_match($regex, $memo)) {
                // Determinar tipo: aplicação (dinheiro sai da corrente) ou resgate (dinheiro volta)
                $tipo = 'aplicacao'; // default

                // Se o padrão força um tipo específico
                if (isset($config['force_tipo'])) {
                    $tipo = $config['force_tipo'];
                }
                // Senão, detectar pelo valor (negativo = aplicação, positivo = resgate)
                elseif ($amount !== null) {
                    $tipo = $amount < 0 ? 'aplicacao' : 'resgate';
                }
                // Ou detectar pelo texto
                elseif (preg_match('/RESGATE?|RESG\.|RES\s/i', $memo)) {
                    $tipo = 'resgate';
                }

                $destino = $config['destino'];

                return [
                    'tipo'         => $tipo,
                    'destino'      => $destino,
                    'account_type' => $config['account_type'] ?? null,
                    'banco'        => $config['banco'] ?? null,
                    'memo'         => $memo,
                    'acao_label'   => $tipo === 'aplicacao'
                        ? "Aplicação → {$destino}"
                        : "Resgate ← {$destino}",
                    'icone'        => $tipo === 'aplicacao'
                        ? 'fa-arrow-right'
                        : 'fa-arrow-left',
                    'cor'          => $tipo === 'aplicacao' ? 'info' : 'success',
                ];
            }
        }

        return null;
    }

    /**
     * Verifica se é movimentação interna (boolean simples)
     */
    public static function isMovimentacaoInterna(string $memo): bool
    {
        return self::detectar($memo) !== null;
    }

    /**
     * Retorna todos os padrões conhecidos (para configuração/admin)
     */
    public static function getPadroes(): array
    {
        return self::PADROES;
    }

    /**
     * Retorna os tipos de conta suportados
     */
    public static function getAccountTypes(): array
    {
        return [
            'aplicacao'      => 'Aplicação',
            'poupanca'       => 'Poupança',
            'renda_fixa'     => 'Renda Fixa',
            'tesouro_direto' => 'Tesouro Direto',
        ];
    }

    /**
     * Tenta encontrar a conta destino sugerida baseada no banco e tipo
     *
     * @param int $companyId
     * @param string|null $bancoCode Código do banco (001, 104, etc.)
     * @param string|null $accountType Tipo de conta (aplicacao, poupanca, etc.)
     * @param int|null $excludeEntidadeId ID da entidade de origem (para excluir da busca)
     * @return \App\Models\EntidadeFinanceira|null
     */
    public static function sugerirContaDestino(
        int $companyId,
        ?string $bancoCode = null,
        ?string $accountType = null,
        ?int $excludeEntidadeId = null
    ): ?\App\Models\EntidadeFinanceira {
        $query = \App\Models\EntidadeFinanceira::where('company_id', $companyId)
            ->where('tipo', 'banco');

        if ($excludeEntidadeId) {
            $query->where('id', '!=', $excludeEntidadeId);
        }

        // Prioridade 1: Mesmo banco + mesmo account_type
        if ($bancoCode && $accountType) {
            $conta = (clone $query)
                ->whereHas('bank', fn($q) => $q->where('code', $bancoCode))
                ->where('account_type', $accountType)
                ->first();

            if ($conta) {
                return $conta;
            }
        }

        // Prioridade 2: Qualquer banco + mesmo account_type
        if ($accountType) {
            $conta = (clone $query)
                ->where('account_type', $accountType)
                ->first();

            if ($conta) {
                return $conta;
            }
        }

        // Prioridade 3: Mesmo banco + qualquer tipo de aplicação
        if ($bancoCode) {
            $conta = (clone $query)
                ->whereHas('bank', fn($q) => $q->where('code', $bancoCode))
                ->whereIn('account_type', ['aplicacao', 'poupanca', 'renda_fixa'])
                ->first();

            if ($conta) {
                return $conta;
            }
        }

        return null;
    }
}
