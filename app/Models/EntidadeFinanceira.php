<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class EntidadeFinanceira extends Model
{
    use HasFactory;

    protected $table = 'entidades_financeiras';

    protected $fillable = [
        'nome',
        'tipo',
        'saldo_inicial',
        'saldo_atual',
        'descricao',
        'company_id',
        'created_by_name',
        'created_by',
        'updated_by_name',
        'updated_by',
    ];

    // Relacionamento com movimentações
    public function movimentacoes()
    {
        return $this->hasMany(Movimentacao::class, 'entidade_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id'); // Cada EntidadeFinanceira pertence a uma Company
    }

    // Método para atualizar o saldo atual
    public function atualizarSaldo()
    {
        $this->saldo_atual = $this->movimentacoes()->sum(
            DB::raw("CASE WHEN tipo = 'entrada' THEN valor ELSE -valor END")
        );
        $this->save();
    }

    static public function getValorTotalEntidade()
    {
        $companyId = auth()->user()->company_id; // Recupere a empresa do usuário logado

        // Soma os saldos atuais das entidades financeiras do tipo 'caixa'
        return EntidadeFinanceira::where('tipo', 'caixa') // Filtra pelo tipo desejado
            ->where('company_id', $companyId) // Filtra pela empresa do usuário
            ->sum('saldo_atual'); // Soma a coluna 'saldo_atual'
    }

    static public function getValorTotalEntidadeBC()
    {
        $companyId = auth()->user()->company_id; // Recupere a empresa do usuário logado

        // Soma os saldos atuais das entidades financeiras do tipo 'caixa'
        return EntidadeFinanceira::where('tipo', 'banco') // Filtra pelo tipo desejado
            ->where('company_id', $companyId) // Filtra pela empresa do usuário
            ->sum('saldo_atual'); // Soma a coluna 'saldo_atual'
    }


}

