<?php

namespace App\Enums;

/**
 * Aparência visual das etapas de formação (ícones, emojis, cores)
 */
enum FormationStageAppearance: string
{
    case VOCACIONADO = 'vocacionado';
    case POSTULANTADO_1 = 'postulantado_1';
    case POSTULANTADO_2 = 'postulantado_2';
    case NOVICIADO = 'noviciado';
    case POS_NOVICIADO = 'pos_noviciado';
    case VOTOS_TEMPORARIOS = 'votos_temporarios';
    case VOTOS_PERPETUOS = 'votos_perpetuos';

    /**
     * Ícone Font Awesome da etapa
     */
    public function icon(): string
    {
        return match ($this) {
            self::VOCACIONADO => 'fa-solid fa-compass',
            self::POSTULANTADO_1 => 'fa-solid fa-seedling',
            self::POSTULANTADO_2 => 'fa-solid fa-leaf',
            self::NOVICIADO => 'fa-solid fa-book-open',
            self::POS_NOVICIADO => 'fa-solid fa-graduation-cap',
            self::VOTOS_TEMPORARIOS => 'fa-solid fa-handshake',
            self::VOTOS_PERPETUOS => 'fa-solid fa-infinity',
        };
    }

    /**
     * Emoji da etapa
     */
    public function emoji(): string
    {
        return match ($this) {
            self::VOCACIONADO => '🧭',
            self::POSTULANTADO_1 => '🌱',
            self::POSTULANTADO_2 => '🌿',
            self::NOVICIADO => '🕯️',
            self::POS_NOVICIADO => '🎓',
            self::VOTOS_TEMPORARIOS => '🔗',
            self::VOTOS_PERPETUOS => '♾️',
        };
    }

    /**
     * Cor Bootstrap da etapa
     */
    public function color(): string
    {
        return match ($this) {
            self::VOCACIONADO => 'warning',
            self::POSTULANTADO_1 => 'success',
            self::POSTULANTADO_2 => 'success',
            self::NOVICIADO => 'info',
            self::POS_NOVICIADO => 'primary',
            self::VOTOS_TEMPORARIOS => 'info',
            self::VOTOS_PERPETUOS => 'danger',
        };
    }

    /**
     * Retorna todas as propriedades de aparência
     */
    public function appearance(): array
    {
        return [
            'icon' => $this->icon(),
            'emoji' => $this->emoji(),
            'color' => $this->color(),
        ];
    }

    /**
     * Busca aparência pelo slug
     */
    public static function fromSlug(?string $slug): ?self
    {
        if (!$slug) {
            return null;
        }

        return self::tryFrom($slug);
    }

    /**
     * Retorna aparência padrão (fallback)
     */
    public static function defaultAppearance(): array
    {
        return [
            'icon' => 'fa-solid fa-circle',
            'emoji' => '📌',
            'color' => 'secondary',
        ];
    }

    /**
     * Retorna aparência pelo slug ou o padrão
     */
    public static function getAppearance(?string $slug): array
    {
        $stage = self::fromSlug($slug);
        
        return $stage?->appearance() ?? self::defaultAppearance();
    }
}
