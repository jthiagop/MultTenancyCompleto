<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConciliacaoFeedback extends Model
{
    protected $table = 'conciliacao_feedback';

    protected $fillable = [
        'company_id',
        'bank_statement_id',
        'campo',
        'valor_sugerido',
        'valor_escolhido',
        'aceito',
        'confianca_original',
        'origem_sugestao',
    ];

    protected $casts = [
        'aceito' => 'boolean',
        'confianca_original' => 'integer',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function bankStatement()
    {
        return $this->belongsTo(\App\Models\Financeiro\BankStatement::class);
    }
}
