<?php

namespace App\Enums;

enum StatusDomusDocumento: string
{
    case PENDENTE = 'pendente';
    case PROCESSADO = 'processado';
    case LANCADO = 'lancado';
    case ERRO = 'erro';
    case ARQUIVADO = 'arquivado';

    /**
     * Retorna o label humanizado
     */
    public function label(): string
    {
        return match($this) {
            self::PENDENTE => 'Pendente',
            self::PROCESSADO => 'Processado',
            self::LANCADO => 'Lançado',
            self::ERRO => 'Erro',
            self::ARQUIVADO => 'Arquivado',
        };
    }

    /**
     * Retorna a cor badge
     */
    public function badgeClass(): string
    {
        return match($this) {
            self::PENDENTE => 'badge-light-warning',
            self::PROCESSADO => 'badge-light-info',
            self::LANCADO => 'badge-light-success',
            self::ERRO => 'badge-light-danger',
            self::ARQUIVADO => 'badge-light-secondary',
        };
    }

    /**
     * Retorna o ícone
     */
    public function icon(): string
    {
        return match($this) {
            self::PENDENTE => 'fa-clock',
            self::PROCESSADO => 'fa-microchip',
            self::LANCADO => 'fa-check-circle',
            self::ERRO => 'fa-triangle-exclamation',
            self::ARQUIVADO => 'fa-box-archive',
        };
    }

    /**
     * Status que devem aparecer na lista de pendentes
     */
    public static function statusDisponiveis(): array
    {
        return [
            self::PENDENTE,
            self::PROCESSADO,
            self::ERRO,
        ];
    }

    /**
     * Status que indicam que o documento já foi finalizado
     */
    public static function statusFinalizados(): array
    {
        return [
            self::LANCADO,
            self::ARQUIVADO,
        ];
    }

    /**
     * Valores string dos status disponíveis (para queries)
     */
    public static function valoresDisponiveis(): array
    {
        return array_map(fn($s) => $s->value, self::statusDisponiveis());
    }

    /**
     * Valores string dos status finalizados (para queries)
     */
    public static function valoresFinalizados(): array
    {
        return array_map(fn($s) => $s->value, self::statusFinalizados());
    }
}
