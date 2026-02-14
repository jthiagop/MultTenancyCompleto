<?php

namespace App\Models;

use App\Enums\StatusDomusDocumento;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DomusDocumento extends Model
{
    use SoftDeletes;

    protected $table = 'domus_documentos';

    protected $fillable = [
        'nome_arquivo',
        'caminho_arquivo',
        'tipo_arquivo',
        'mime_type',
        'tamanho_arquivo',
        'base64_content',
        'tipo_documento',
        'dados_extraidos',
        'estabelecimento_nome',
        'estabelecimento_cnpj',
        'data_emissao',
        'valor_total',
        'forma_pagamento',
        'status',
        'erro_processamento',
        'processado_em',
        'company_id',
        'user_id',
        'user_name',
        'canal_origem',
        'remetente',
    ];

    protected $casts = [
        'dados_extraidos' => 'array',
        'data_emissao' => 'date',
        'valor_total' => 'decimal:2',
        'processado_em' => 'datetime',
        'tamanho_arquivo' => 'integer',
    ];

    /**
     * Accessor que converte status para Enum com proteção contra valores inválidos.
     * Se o banco tiver status vazio/nulo, retorna PENDENTE.
     */
    public function getStatusAttribute($value): StatusDomusDocumento
    {
        if (empty($value)) {
            return StatusDomusDocumento::PENDENTE;
        }

        return StatusDomusDocumento::tryFrom($value) ?? StatusDomusDocumento::PENDENTE;
    }

    /**
     * Mutator que aceita tanto Enum quanto string ao setar status.
     */
    public function setStatusAttribute($value): void
    {
        $this->attributes['status'] = $value instanceof StatusDomusDocumento
            ? $value->value
            : ($value ?: StatusDomusDocumento::PENDENTE->value);
    }

    // Relacionamentos
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // Scopes
    public function scopePendentes($query)
    {
        return $query->where('status', StatusDomusDocumento::PENDENTE);
    }

    public function scopeProcessados($query)
    {
        return $query->where('status', StatusDomusDocumento::PROCESSADO);
    }

    public function scopeLancados($query)
    {
        return $query->where('status', StatusDomusDocumento::LANCADO);
    }

    /**
     * Documentos disponíveis para lançamento (exclui finalizados)
     */
    public function scopeDisponiveis($query)
    {
        return $query->whereNotIn('status', StatusDomusDocumento::valoresFinalizados());
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo_documento', $tipo);
    }

    // Accessors
    public function getTamanhoFormatadoAttribute()
    {
        $bytes = $this->tamanho_arquivo;
        if ($bytes === 0) return '0 Bytes';
        
        $k = 1024;
        $sizes = ['Bytes', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes) / log($k));
        
        return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
    }

    // Mutators
    public function setDadosExtraidosAttribute($value)
    {
        $this->attributes['dados_extraidos'] = is_array($value) ? json_encode($value) : $value;
    }
}
