<?php

namespace App\Models;

use App\Models\Financeiro\BankStatement;
use App\Models\Financeiro\TransacaoFinanceira;
use Auth;
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
        'agencia',
        'conta',
        'saldo_inicial',
        'saldo_atual',
        'descricao',
        'company_id',
        'created_by_name',
        'created_by',
        'updated_by_name',
        'updated_by',
    ];
    public function podeConciliarCom(BankStatement $outraTransacao, $tolerancia = 5.00)
{
    return abs($this->amount + $outraTransacao->amount) <= $tolerancia;
}


    // Relacionamento com movimentações
    public function movimentacoes()
    {
        return $this->hasMany(Movimentacao::class, 'entidade_id');
    }

    public function transacoesFinanceiras()
    {
        return $this->hasMany(TransacaoFinanceira::class, 'entidade_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id'); // Cada EntidadeFinanceira pertence a uma Company
    }

    public function bankStatements()
    {
        return $this->hasMany(BankStatement::class, 'entidade_financeira_id');
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
        $companyId = Auth::user()->company_id; // Recupere a empresa do usuário logado

        // Soma os saldos atuais das entidades financeiras do tipo 'caixa'
        return EntidadeFinanceira::where('tipo', 'caixa') // Filtra pelo tipo desejado
            ->where('company_id', $companyId) // Filtra pela empresa do usuário
            ->sum('saldo_atual'); // Soma a coluna 'saldo_atual'
    }

    static public function getValorTotalEntidadeBC()
    {
        $companyId = Auth::user()->company_id; // Recupere a empresa do usuário logado

        // Soma os saldos atuais das entidades financeiras do tipo 'caixa'
        return EntidadeFinanceira::where('tipo', 'banco') // Filtra pelo tipo desejado
            ->where('company_id', $companyId) // Filtra pela empresa do usuário
            ->sum('saldo_atual'); // Soma a coluna 'saldo_atual'
    }

    static public function getEntidadeFinanceira()
    {
        $companyId = Auth::user()->company_id; // Recupere a empresa do usuário logado

        // Soma os saldos atuais das entidades financeiras do tipo 'caixa'
        return EntidadeFinanceira::where('company_id', $companyId)->get();
    }
}
