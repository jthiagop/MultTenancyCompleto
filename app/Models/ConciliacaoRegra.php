<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConciliacaoRegra extends Model
{
    use HasFactory;

    protected $table = 'conciliacao_regras';

    protected $fillable = [
        'company_id',
        'termo_busca',
        'lancamento_padrao_id',
        'parceiro_id',
        'cost_center_id',
        'tipo_documento',
        'descricao_sugerida',
        'prioridade',
        'created_by',
        'created_by_name',
        'updated_by',
        'updated_by_name',
    ];

    protected $casts = [
        'prioridade' => 'integer',
    ];

    // Relacionamentos
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function lancamentoPadrao()
    {
        return $this->belongsTo(LancamentoPadrao::class, 'lancamento_padrao_id');
    }

    public function parceiro()
    {
        return $this->belongsTo(Parceiro::class, 'parceiro_id');
    }

    public function costCenter()
    {
        return $this->belongsTo(\App\Models\Financeiro\CostCenter::class, 'cost_center_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }
}
