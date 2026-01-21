<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Integracao extends Model
{
    // Definir nome da tabela explicitamente (Laravel pluraliza incorretamente "integracao" para "integracaos")
    protected $table = 'integracoes';

    protected $fillable = [
        'tipo',
        'status',
        'remetente',
        'destinatario',
        'user_id',
    ];

    protected $casts = [
        'tipo' => 'string',
        'status' => 'string',
    ];

    /**
     * Relacionamento com User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope para filtrar por tipo
     */
    public function scopeTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    /**
     * Scope para filtrar por status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Verificar se está configurado
     */
    public function isConfigurado(): bool
    {
        return $this->status === 'configurado';
    }
}
