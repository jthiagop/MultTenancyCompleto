<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberFormationPeriod extends Model
{
    protected $fillable = [
        'religious_member_id',
        'formation_stage_id',
        'company_id',
        'place_text',
        'start_date',
        'end_date',
        'notes',
        'is_current',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
    ];

    /**
     * Membro religioso deste período
     */
    public function religiousMember(): BelongsTo
    {
        return $this->belongsTo(ReligiousMember::class);
    }

    /**
     * Etapa de formação
     */
    public function formationStage(): BelongsTo
    {
        return $this->belongsTo(FormationStage::class);
    }

    /**
     * Casa/Convento (se cadastrado)
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Retorna o local formatado (company ou place_text)
     */
    public function getPlaceAttribute(): string
    {
        if ($this->company) {
            return $this->company->name;
        }
        
        return $this->place_text ?? 'Local não informado';
    }

    /**
     * Retorna o período formatado
     */
    public function getPeriodAttribute(): string
    {
        $start = $this->start_date->format('d/m/Y');
        
        if ($this->end_date) {
            return "{$start} a {$this->end_date->format('d/m/Y')}";
        }
        
        return "{$start} - Atual";
    }

    /**
     * Scope para períodos atuais
     */
    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    /**
     * Scope para filtrar por etapa
     */
    public function scopeByStage($query, $stageId)
    {
        return $query->where('formation_stage_id', $stageId);
    }

    /**
     * Scope para filtrar por casa/local
     */
    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }
}
