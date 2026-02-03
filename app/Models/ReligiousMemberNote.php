<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model para notas/observações de membros religiosos
 * 
 * Usado para registrar acompanhamentos, observações e anotações
 * sobre o membro ao longo do tempo.
 */
class ReligiousMemberNote extends Model
{
    protected $fillable = [
        'religious_member_id',
        'user_id',
        'title',
        'content',
        'type',
        'is_private',
    ];

    protected $casts = [
        'is_private' => 'boolean',
    ];

    /**
     * Tipos de notas disponíveis
     */
    public const TYPES = [
        'general' => 'Geral',
        'formation' => 'Formação',
        'health' => 'Saúde',
        'administrative' => 'Administrativo',
        'spiritual' => 'Espiritual',
    ];

    /**
     * Cores por tipo
     */
    public const TYPE_COLORS = [
        'general' => 'secondary',
        'formation' => 'primary',
        'health' => 'danger',
        'administrative' => 'warning',
        'spiritual' => 'info',
    ];

    /**
     * Ícones por tipo
     */
    public const TYPE_ICONS = [
        'general' => 'fa-solid fa-sticky-note',
        'formation' => 'fa-solid fa-graduation-cap',
        'health' => 'fa-solid fa-heart-pulse',
        'administrative' => 'fa-solid fa-file-signature',
        'spiritual' => 'fa-solid fa-dove',
    ];

    /**
     * Membro religioso
     */
    public function religiousMember(): BelongsTo
    {
        return $this->belongsTo(ReligiousMember::class);
    }

    /**
     * Autor da nota
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Retorna o nome do tipo formatado
     */
    public function getTypeNameAttribute(): string
    {
        return self::TYPES[$this->type] ?? 'Geral';
    }

    /**
     * Retorna a cor do tipo
     */
    public function getTypeColorAttribute(): string
    {
        return self::TYPE_COLORS[$this->type] ?? 'secondary';
    }

    /**
     * Retorna o ícone do tipo
     */
    public function getTypeIconAttribute(): string
    {
        return self::TYPE_ICONS[$this->type] ?? 'fa-solid fa-sticky-note';
    }

    /**
     * Scope para notas públicas
     */
    public function scopePublic($query)
    {
        return $query->where('is_private', false);
    }

    /**
     * Scope por tipo
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
