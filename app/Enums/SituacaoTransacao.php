<?php

namespace App\Enums;

enum SituacaoTransacao: string
{
    case EM_ABERTO = 'em_aberto';
    case PAGO = 'pago';
    case RECEBIDO = 'recebido';
    case PAGO_PARCIAL = 'pago_parcial';
    case ATRASADO = 'atrasado';
    case PREVISTO = 'previsto';
    case DESCONSIDERADO = 'desconsiderado';

    /**
     * Retorna o label humanizado da situação
     */
    public function label(): string
    {
        return match($this) {
            self::EM_ABERTO => 'Em Aberto',
            self::PAGO => 'Pago',
            self::RECEBIDO => 'Recebido',
            self::PAGO_PARCIAL => 'Pago Parcial',
            self::ATRASADO => 'Atrasado',
            self::PREVISTO => 'Previsto',
            self::DESCONSIDERADO => 'Desconsiderado',
        };
    }

    /**
     * Retorna a cor badge para a situação
     */
    public function badgeClass(): string
    {
        return match($this) {
            self::EM_ABERTO => 'badge-light-warning',
            self::PAGO => 'badge-light-success',
            self::RECEBIDO => 'badge-light-primary',
            self::PAGO_PARCIAL => 'badge-light-info',
            self::ATRASADO => 'badge-light-danger',
            self::PREVISTO => 'badge-light-secondary',
            self::DESCONSIDERADO => 'badge-light-dark',
        };
    }

    /**
     * Verifica se a transação está quitada
     */
    public function isQuitada(): bool
    {
        return in_array($this, [self::PAGO, self::RECEBIDO]);
    }

    /**
     * Retorna todas as situações como array
     */
    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Retorna situações aplicáveis para ENTRADAS (receitas)
     */
    public static function forEntrada(): array
    {
        return [
            self::EM_ABERTO,
            self::RECEBIDO,      // Específico para entradas
            self::PAGO_PARCIAL,
            self::ATRASADO,
            self::PREVISTO,
            self::DESCONSIDERADO,
        ];
    }

    /**
     * Retorna situações aplicáveis para SAÍDAS (despesas)
     */
    public static function forSaida(): array
    {
        return [
            self::EM_ABERTO,
            self::PAGO,          // Específico para saídas
            self::PAGO_PARCIAL,
            self::ATRASADO,
            self::PREVISTO,
            self::DESCONSIDERADO,
        ];
    }

    /**
     * Retorna situações filtradas por tipo de transação
     */
    public static function forTipo(string $tipo): array
    {
        return match($tipo) {
            'entrada' => self::forEntrada(),
            'saida' => self::forSaida(),
            default => self::cases(),
        };
    }
}
