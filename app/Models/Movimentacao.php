<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movimentacao extends Model
{
    use HasFactory;

    protected $table = 'movimentacoes';

    protected $fillable = [
        'entidade_id',
        'tipo',
        'valor',
        'descricao',
        'movimentacao_id',
        'company_id',
        'created_by',
        'updated_by',
        'created_by_name',
        'updated_by_name',
    ];

    // Relacionamento com a entidade financeira
    public function entidade()
    {
        return $this->belongsTo(EntidadeFinanceira::class, 'entidade_id' , 'id');
    }

    public static function boot()
    {
        parent::boot();

        // Atualiza saldo após criar uma movimentação
        static::created(function ($movimentacao) {
            $entidade = EntidadeFinanceira::find($movimentacao->entidade_id);
            if ($movimentacao->tipo === 'entrada') {
                $entidade->saldo_atual += $movimentacao->valor;
            } else {
                $entidade->saldo_atual -= $movimentacao->valor;
            }
            $entidade->save();
        });

        // Reverte saldo ao excluir uma movimentação
        static::deleting(function ($movimentacao) {
            $entidade = EntidadeFinanceira::find($movimentacao->entidade_id);
            if ($movimentacao->tipo === 'entrada') {
                $entidade->saldo_atual -= $movimentacao->valor;
            } else {
                $entidade->saldo_atual += $movimentacao->valor;
            }
            $entidade->save();
        });
    }

    public function calcularSaldoAtual()
    {
        $entradas = $this->movimentacoes()->where('tipo', 'entrada')->sum('valor');
        $saidas = $this->movimentacoes()->where('tipo', 'saida')->sum('valor');

        return $this->saldo_inicial + $entradas - $saidas;
    }
}
