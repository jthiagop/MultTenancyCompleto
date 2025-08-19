<?php

namespace App\Models;

use App\Models\Financeiro\BankStatement;
use App\Models\Financeiro\TransacaoFinanceira;
use Illuminate\Support\Facades\Auth;
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
        'banco_id', 
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
        // Soma todas as entradas e subtrai todas as saídas
        $totalMovimentacoes = $this->movimentacoes()->where('tipo', 'entrada')->sum('valor') - $this->movimentacoes()->where('tipo', 'saida')->sum('valor');

        // O saldo atual é o saldo inicial (que não muda) mais o total de movimentações
        $this->saldo_atual = $this->saldo_inicial + $totalMovimentacoes;
        $this->save(); // Salva o novo saldo no banco
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

    /**
     * Scope: Filtra a busca para incluir apenas os registros da empresa ativa na sessão.
     */
    public function scopeForActiveCompany($query)
    {
        $activeCompanyId = session('active_company_id');

        if ($activeCompanyId) {
            return $query->where('company_id', $activeCompanyId);
        }

        // Se nenhuma empresa estiver ativa, não retorna nada para proteger os dados.
        return $query->whereRaw('1 = 0');
    }

        /**
     * Uma entidade financeira (conta) pertence a um banco (instituição).
     */
    public function bank()
    {
        return $this->belongsTo(Bank::class, 'banco_id');
    }
}
