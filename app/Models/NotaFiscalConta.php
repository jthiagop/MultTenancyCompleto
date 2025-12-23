<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotaFiscalConta extends Model
{
    use HasFactory;

    protected $table = 'notafiscal_contas';

    protected $fillable = [
        'company_id',
        'cnpj',
        'certificado_path',
        'certificado_senha',
        'certificado_validade',
        'certificado_cnpj',
        'certificado_nome',
        'ultimo_nsu',
        'ativo',
        'ambiente',
        'created_by',
        'created_by_name',
        'updated_by',
        'updated_by_name',
    ];

    protected $casts = [
        'certificado_validade' => 'date',
        'ativo' => 'boolean',
        'ambiente' => 'integer',
    ];

    /**
     * Relacionamento com Company
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Relacionamento com User (criador)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relacionamento com User (atualizador)
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
