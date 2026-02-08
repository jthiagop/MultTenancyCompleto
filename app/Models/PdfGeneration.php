<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PdfGeneration extends Model
{
    use HasFactory;

    /**
     * Dias para expiração padrão dos PDFs gerados
     */
    public const EXPIRATION_DAYS = 5;

    protected $fillable = [
        'type',
        'user_id',
        'company_id',
        'status',
        'filename',
        'file_name',
        'parameters',
        'error_message',
        'started_at',
        'completed_at',
        'expires_at',
    ];

    protected $casts = [
        'parameters' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class);
    }

    /**
     * Retorna a URL de download do arquivo
     */
    public function getDownloadUrlAttribute()
    {
        if ($this->status === 'completed' && $this->filename) {
            return \Storage::disk('public')->url($this->filename);
        }
        return null;
    }

    /**
     * Verifica se o arquivo expirou
     */
    public function getIsExpiredAttribute(): bool
    {
        if ($this->expires_at) {
            return $this->expires_at->isPast();
        }
        return $this->created_at->addDays(self::EXPIRATION_DAYS)->isPast();
    }

    /**
     * Retorna o tempo restante para expiração formatado
     */
    public function getExpiresInAttribute(): ?string
    {
        if ($this->is_expired) {
            return 'Expirado';
        }

        $expiresAt = $this->expires_at ?? $this->created_at->addDays(self::EXPIRATION_DAYS);
        return $expiresAt->diffForHumans();
    }

    /**
     * Scope para buscar apenas PDFs não expirados
     */
    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->where('expires_at', '>', now())
              ->orWhere(function ($q2) {
                  $q2->whereNull('expires_at')
                     ->where('created_at', '>', now()->subDays(self::EXPIRATION_DAYS));
              });
        });
    }

    /**
     * Scope para buscar apenas PDFs expirados
     */
    public function scopeExpired($query)
    {
        return $query->where(function ($q) {
            $q->where('expires_at', '<', now())
              ->orWhere(function ($q2) {
                  $q2->whereNull('expires_at')
                     ->where('created_at', '<', now()->subDays(self::EXPIRATION_DAYS));
              });
        });
    }
}
