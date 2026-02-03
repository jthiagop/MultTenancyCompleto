<?php

namespace App\Models;

use App\Traits\HasTimeline;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ReligiousMember extends Model
{
    use SoftDeletes, HasTimeline;

    protected $fillable = [
        'name',
        'order_registration_number',
        'avatar',
        'cpf',
        'province_id',
        'religious_role_id',
        'current_stage_id',
        'birth_date',
        'temporary_profession_date',
        'perpetual_profession_date',
        'diaconal_ordination_date',
        'priestly_ordination_date',
        'observacoes',
        'disponivel_todas_casas',
        'is_active',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'temporary_profession_date' => 'date',
        'perpetual_profession_date' => 'date',
        'diaconal_ordination_date' => 'date',
        'priestly_ordination_date' => 'date',
        'is_active' => 'boolean',
        'disponivel_todas_casas' => 'boolean',
    ];

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(ReligiousRole::class, 'religious_role_id');
    }

    public function currentStage(): BelongsTo
    {
        return $this->belongsTo(FormationStage::class, 'current_stage_id');
    }

    /**
     * Histórico de formação (todas as etapas/períodos)
     */
    public function formationPeriods(): HasMany
    {
        return $this->hasMany(MemberFormationPeriod::class)
            ->orderBy('start_date', 'desc');
    }

    /**
     * Período atual de formação
     */
    public function currentFormationPeriod(): HasOne
    {
        return $this->hasOne(MemberFormationPeriod::class)
            ->where('is_current', true)
            ->latestOfMany('start_date');
    }

    /**
     * Todos os endereços do membro
     */
    public function addresses(): BelongsToMany
    {
        return $this->belongsToMany(Address::class, 'religious_member_address')
            ->withPivot('tipo')
            ->withTimestamps();
    }

    /**
     * Endereço de origem
     */
    public function originAddress(): BelongsToMany
    {
        return $this->belongsToMany(Address::class, 'religious_member_address')
            ->withPivot('tipo')
            ->withTimestamps()
            ->wherePivot('tipo', 'origem');
    }

    /**
     * Notas/Observações do membro
     */
    public function notes(): HasMany
    {
        return $this->hasMany(ReligiousMemberNote::class)
            ->orderBy('created_at', 'desc');
    }

    /**
     * Notas públicas do membro
     */
    public function publicNotes(): HasMany
    {
        return $this->hasMany(ReligiousMemberNote::class)
            ->where('is_private', false)
            ->orderBy('created_at', 'desc');
    }

    /**
     * Ministérios recebidos pelo membro (Leitorado, Acolitato, Diaconato, etc.)
     */
    public function ministries(): HasMany
    {
        return $this->hasMany(MemberMinistry::class, 'member_id')
            ->orderBy('date', 'asc');
    }

    /**
     * Retorna a URL do avatar ou null
     */
    public function getAvatarUrlAttribute(): ?string
    {
        if ($this->avatar) {
            try {
                return route('file', ['path' => $this->avatar]);
            } catch (\Exception $e) {
                return asset('storage/' . $this->avatar);
            }
        }
        return null;
    }
}