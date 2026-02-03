<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberMinistry extends Model
{
    protected $fillable = [
        'member_id',
        'ministry_type_id',
        'date',
        'diocese_name',
        'minister_name',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Relacionamento com o membro religioso
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(ReligiousMember::class, 'member_id');
    }

    /**
     * Relacionamento com o tipo de ministério
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(MinistryType::class, 'ministry_type_id');
    }

    /**
     * Accessor para data formatada
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->date?->format('d/m/Y') ?? '';
    }

    /**
     * Scope para ordenar por data
     */
    public function scopeOrderByDate($query, $direction = 'asc')
    {
        return $query->orderBy('date', $direction);
    }

    /**
     * Scope para filtrar por tipo de ministério
     */
    public function scopeOfType($query, $typeSlug)
    {
        return $query->whereHas('type', fn($q) => $q->where('slug', $typeSlug));
    }
}
