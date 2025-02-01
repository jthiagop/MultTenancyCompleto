<?php

namespace App\Models;

use App\Models\Financeiro\TransacaoFinanceira;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anexo extends Model
{
    use HasFactory;

    protected $fillable = [
        'caixa_id','banco_id',
        'nome_arquivo',
        'size',
        'caminho_arquivo',
        'created_by',
        'updated_by',
        'anexable_id',      // polimórfico
        'anexable_type',    // polimórfico
    ];

        /**
     * Relação polimórfica: um Anexo "pertence" a algo (Banco, TransacaoFinanceira, etc.)
     */
    public function anexable()
    {
        return $this->morphTo();
    }

    public function caixa()
    {
        return $this->belongsTo(TransacaoFinanceira::class);
    }

    public function banco()
    {
        return $this->belongsTo(Banco::class, 'id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
