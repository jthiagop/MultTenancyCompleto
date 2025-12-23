<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DfeDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'chave_acesso',
        'nsu',
        'modelo',
        'tp_amb',
        'schema_xml',
        'emitente_nome',
        'emitente_cnpj',
        'emitente_ie',
        'data_emissao',
        'valor_total',
        'status_sistema',
        'xml_completo',
        'xml_path',
        'xml_hash',
    ];

    protected $casts = [
        'data_emissao' => 'datetime',
        'valor_total' => 'decimal:2',
        'xml_completo' => 'boolean',
        'modelo' => 'integer',
        'tp_amb' => 'integer',
    ];

    /**
     * Uma nota tem vários eventos
     */
    public function events()
    {
        return $this->hasMany(DfeEvent::class);
    }

    /**
     * Pertence a uma empresa
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Helper para saber se está cancelada
     */
    public function isCancelled()
    {
        return $this->events()->where('tp_evento', 110111)->exists();
    }
}
