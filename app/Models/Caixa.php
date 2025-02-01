<?php

namespace App\Models;

use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Contracts\Auditable;


class Caixa extends Model implements Auditable
{
    use HasFactory;

    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'company_id',
        'data_competencia',
        'descricao',
        'valor',
        'tipo', // Tipo de transação (entrada/saida)
        'lancamento_padrao_id',
        'centro',
        'tipo_documento',
        'numero_documento',
        'historico_complementar',
        'origem',
        'created_by',
        'updated_by',
        'comprovacao_fiscal',
        'movimentacao_id'
    ];

    // Relacionamento com anexos
    public function anexos()
    {
        return $this->hasMany(Anexo::class);
    }

    // Relacionamento com o usuário que criou o registro
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relacionamento com o usuário que atualizou o registro
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Relacionamento com o lançamento padrão
    public function lancamentoPadrao()
    {
        return $this->belongsTo(LancamentoPadrao::class, 'lancamento_padrao_id');
    }

    public function movimentacao()
{
    return $this->belongsTo(Movimentacao::class, 'movimentacao_id');
}



    static public function getCaixaList()
    {
        $userId = Auth::user()->id; // Recupere o ID do usuário logado

        // Use Eloquent para carregar caixas com o relacionamento lancamentoPadrao
        return Caixa::with('lancamentoPadrao')
            ->join('company_user', 'caixas.company_id', '=', 'company_user.company_id')
            ->where('company_user.user_id', $userId)
            ->select('caixas.*')
            ->get();
    }


    static public function getEntidadesCaixa()
    {
        $companyId = Auth::user()->company_id; // Recupere a empresa do usuário logado

        return EntidadeFinanceira::where('tipo', 'caixa') // Filtra apenas pelo tipo banco
            ->where('company_id', $companyId) // Filtra pela empresa do usuário
            ->get();
    }

    static public function getEntidadesBanco()
    {
        $companyId = Auth::user()->company_id; // Recupere a empresa do usuário logado

        return EntidadeFinanceira::where('tipo', 'banco') // Filtra apenas pelo tipo banco
            ->where('company_id', $companyId) // Filtra pela empresa do usuário
            ->get();
    }

    /**
     * Relacionamento com Caixa.
     */



    static public function getCaixaEntrada()
    {
        $userId = Auth::user()->id; // Recupere o ID do usuário logado

        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        $entradas = DB::table('caixas')
            ->join('company_user', 'caixas.company_id', '=', 'company_user.company_id')
            ->where('company_user.user_id', $userId)
            ->where('caixas.tipo', 'entrada') // Filtra apenas as entradas
            ->whereYear('caixas.data_competencia', $currentYear) // Filtra pelo ano vigente
            ->whereMonth('caixas.data_competencia', $currentMonth) // Filtra pelo mês vigente
            ->select('caixas.*') // Selecione todas as colunas da tabela 'caixa'
            ->get();

        $somaEntradas = $entradas->sum('valor'); //soma os valores de entrada

        return $somaEntradas;
    }

    static public function getCaixaSaida()
    {

        $userId = Auth::user()->id; // Recupere o ID do usuário logado

        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        $saidas = DB::table('caixas')
            ->join('company_user', 'caixas.company_id', '=', 'company_user.company_id')
            ->where('company_user.user_id', $userId)
            ->where('caixas.tipo', 'saida') // Filtra apenas as entradas
            ->whereYear('caixas.data_competencia', $currentYear) // Filtra pelo ano vigente
            ->whereMonth('caixas.data_competencia', $currentMonth) // Filtra pelo mês vigente
            ->select('caixas.*') // Selecione todas as colunas da tabela 'caixa'
            ->get();

        $SomaSaidas = $saidas->sum('valor'); //soma os valores de entrada

        return $SomaSaidas;
    }

    static public function getCaixa()
    {

        $userId = Auth::user()->id; // Recupere o ID do usuário logado

        $entradas = DB::table('caixas')
            ->join('company_user', 'caixas.company_id', '=', 'company_user.company_id')
            ->where('company_user.user_id', $userId)
            ->where('caixas.tipo', 'entrada') // Filtra apenas as entradas
            ->select('caixas.*') // Selecione todas as colunas da tabela 'caixa'
            ->get();

        $somaEntradas = $entradas->sum('valor'); //soma os valores de entrada

        $saida = DB::table('caixas')
            ->join('company_user', 'caixas.company_id', '=', 'company_user.company_id')
            ->where('company_user.user_id', $userId)
            ->where('caixas.tipo', 'saida') // Filtra apenas as entradas
            ->select('caixas.*') // Selecione todas as colunas da tabela 'caixa'
            ->get();

        $somaSaida = $saida->sum('valor'); //soma os valores de entrada

        return ([$somaEntradas, $somaSaida]); // Retorna o valor para o controlador

    }


}
