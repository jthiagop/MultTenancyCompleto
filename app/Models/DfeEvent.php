<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DfeEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'dfe_document_id',
        'chave_acesso',
        'nsu',
        'tp_evento',
        'descricao_evento',
        'protocolo',
        'data_evento',
        'correcao_texto',
        'xml_path',
    ];

    protected $casts = [
        'data_evento' => 'datetime',
        'tp_evento' => 'integer',
    ];

    /**
     * Pertence a um documento fiscal (pai)
     */
    public function document()
    {
        return $this->belongsTo(DfeDocument::class, 'dfe_document_id');
    }

    /**
     * Pertence a uma empresa
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
