<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappAuthRequest extends Model
{
    // Forçar uso do banco central
    protected $connection = 'mysql';

    // Tempo de expiração do código em minutos
    const EXPIRATION_MINUTES = 10;

    protected $fillable = [
        'verification_code',
        'tenant_id',
        'waba_id',
        'phone_number_id',
        'wa_id',
        'access_token',
        'user_id',
        'status',
    ];

    /**
     * Verifica se o código de verificação expirou
     * Usa updated_at pois o código pode ter sido atualizado em um registro existente
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->updated_at->addMinutes(self::EXPIRATION_MINUTES)->isPast();
    }

    /**
     * Scope para buscar apenas códigos expirados
     * Usa updated_at pois o código pode ter sido atualizado em um registro existente
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeExpired($query)
    {
        return $query->where('updated_at', '<', now()->subMinutes(self::EXPIRATION_MINUTES));
    }

    /**
     * Scope para buscar por phone_number_id
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $phoneNumberId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByPhoneNumberId($query, $phoneNumberId)
    {
        return $query->where('phone_number_id', $phoneNumberId);
    }

    /**
     * Scope para buscar por wa_id (número do WhatsApp do remetente)
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $waId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByWaId($query, $waId)
    {
        return $query->where('wa_id', $waId);
    }

    /**
     * Scope para buscar apenas registros ativos
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope para buscar apenas registros inativos
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Verifica se o registro pode receber mensagens
     * Um registro pode receber mensagens apenas se está ativo E tem wa_id vinculado
     *
     * @return bool
     */
    public function canReceiveMessages(): bool
    {
        return $this->status === 'active' && !empty($this->wa_id);
    }

    /**
     * Relacionamento com Tenant
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tenant()
    {
        return $this->belongsTo(\App\Models\Tenant::class, 'tenant_id', 'id');
    }
}
