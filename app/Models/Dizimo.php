<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dizimo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'dizimos';

    protected $fillable = [
        'company_id',
        'fiel_id',
        'tipo',
        'valor',
        'data_pagamento',
        'forma_pagamento',
        'entidade_financeira_id',
        'movimentacao_id',
        'observacoes',
        'comprovante',
        'integrado_financeiro',
        'created_by',
        'created_by_name',
        'updated_by',
        'updated_by_name',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'data_pagamento' => 'date',
        'integrado_financeiro' => 'boolean',
    ];

    // Relacionamentos
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function fiel()
    {
        return $this->belongsTo(Fiel::class);
    }

    public function entidadeFinanceira()
    {
        return $this->belongsTo(EntidadeFinanceira::class, 'entidade_financeira_id');
    }

    public function movimentacao()
    {
        return $this->belongsTo(Movimentacao::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
