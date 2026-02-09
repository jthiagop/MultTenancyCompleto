<?php

namespace App\Enums;

/**
 * Natureza do parceiro — indica a função/papel no sistema financeiro.
 * Armazenado como string no banco para permitir novos valores sem migration.
 */
enum NaturezaParceiro: string
{
    case FORNECEDOR = 'fornecedor';
    case CLIENTE = 'cliente';
    case AMBOS = 'ambos';

    /**
     * Label humanizado
     */
    public function label(): string
    {
        return match ($this) {
            self::FORNECEDOR => 'Fornecedor',
            self::CLIENTE => 'Cliente',
            self::AMBOS => 'Fornecedor / Cliente',
        };
    }

    /**
     * Classe do badge para exibição na DataTable
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::FORNECEDOR => 'badge-light-info',
            self::CLIENTE => 'badge-light-success',
            self::AMBOS => 'badge-light-warning',
        };
    }

    /**
     * Ícone Bootstrap Icons
     */
    public function icon(): string
    {
        return match ($this) {
            self::FORNECEDOR => 'bi-building',
            self::CLIENTE => 'bi-person-check',
            self::AMBOS => 'bi-people',
        };
    }

    /**
     * Retorna todos os cases como array [value => label] para selects
     */
    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }
        return $options;
    }

    /**
     * Retorna todos os values válidos
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Tenta criar a partir de um valor string (fallback-safe)
     */
    public static function tryFromValue(?string $value): ?self
    {
        if (!$value) return null;
        return self::tryFrom($value);
    }

    /**
     * Label seguro para valores que podem não estar no enum (extensibilidade)
     */
    public static function labelFor(?string $value): string
    {
        if (!$value) return 'Não definido';

        $enum = self::tryFrom($value);
        if ($enum) return $enum->label();

        // Valor customizado não cadastrado no enum — formatar como ucfirst
        return ucfirst(str_replace('_', ' ', $value));
    }

    /**
     * Badge class seguro para valores que podem não estar no enum
     */
    public static function badgeClassFor(?string $value): string
    {
        if (!$value) return 'badge-light';

        $enum = self::tryFrom($value);
        if ($enum) return $enum->badgeClass();

        return 'badge-light-primary';
    }
}
