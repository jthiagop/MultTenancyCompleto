<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Banco extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'data_competencia',
        'descricao',
        'valor',
        'tipo', // assume que tipo só pode ser "entrada" ou "saida"
        'lancamento_padrao',
        'lancamento_padrao_id',
        'centro',
        'tipo_documento',
        'numero_documento',
        'historico_complementar',
        'banco_id',
        'origem',
        'created_by',
        'updated_by',
        'comprovacao_fiscal',
        'movimentacao_id',
    ];

    public function anexos()
    {
        return $this->hasMany(Anexo::class, 'banco_id');
    }

    public function bancoCadastrado()
    {
        return $this->belongsTo(CadastroBanco::class, 'banco_id');
    }

    public function movimentacao()
    {
        return $this->belongsTo(Movimentacao::class, 'movimentacao_id');
    }


    // Relacionamento com o lançamento padrão
    public function lancamentoPadrao()
    {
        return $this->belongsTo(LancamentoPadrao::class, 'lancamento_padrao_id');
    }

    static public function getBancoList()
    {
        $userId = auth()->user()->id; // Recupere o ID do usuário logado

        $entradas = DB::table('bancos')
            ->join('company_user', 'bancos.company_id', '=', 'company_user.company_id')
            ->where('company_user.user_id', $userId)
            ->get();

        return $entradas;
    }

    static public function getEntidadesBanco()
    {
        $companyId = auth()->user()->company_id; // Recupere a empresa do usuário logado

        return EntidadeFinanceira::where('tipo', 'banco') // Filtra apenas pelo tipo banco
            ->where('company_id', $companyId) // Filtra pela empresa do usuário
            ->get();
    }

    static public function getBancoEntrada()
    {
        $userId = auth()->user()->id; // Recupere o ID do usuário logado

        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        $entradas = DB::table('bancos')
            ->join('company_user', 'bancos.company_id', '=', 'company_user.company_id')
            ->where('company_user.user_id', $userId)
            ->where('bancos.tipo', 'entrada') // Filtra apenas as entradas
            ->whereYear('bancos.data_competencia', $currentYear) // Filtra pelo ano vigente
            ->whereMonth('bancos.data_competencia', $currentMonth) // Filtra pelo mês vigente
            ->select('bancos.*') // Selecione todas as colunas da tabela 'caixa'
            ->get();

        $somaEntradas = $entradas->sum('valor'); //soma os valores de entrada

        return $somaEntradas;
    }

    static public function getBancoSaida()
    {

        $userId = auth()->user()->id; // Recupere o ID do usuário logado

        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        $saidas = DB::table('bancos')
            ->join('company_user', 'bancos.company_id', '=', 'company_user.company_id')
            ->where('company_user.user_id', $userId)
            ->where('bancos.tipo', 'saida') // Filtra apenas as entradas
            ->whereYear('bancos.data_competencia', $currentYear) // Filtra pelo ano vigente
            ->whereMonth('bancos.data_competencia', $currentMonth) // Filtra pelo mês vigente
            ->select('bancos.*') // Selecione todas as colunas da tabela 'caixa'
            ->get();

        $SomaSaidas = $saidas->sum('valor'); //soma os valores de entrada

        return $SomaSaidas;
    }

    static public function getBanco()
    {

        $userId = auth()->user()->id; // Recupere o ID do usuário logado

        $entradas = DB::table('bancos')
            ->join('company_user', 'bancos.company_id', '=', 'company_user.company_id')
            ->where('company_user.user_id', $userId)
            ->where('bancos.tipo', 'entrada') // Filtra apenas as entradas
            ->select('bancos.*') // Selecione todas as colunas da tabela 'caixa'
            ->get();

        $somaEntradas = $entradas->sum('valor'); //soma os valores de entrada

        $saida = DB::table('bancos')
            ->join('company_user', 'bancos.company_id', '=', 'company_user.company_id')
            ->where('company_user.user_id', $userId)
            ->where('bancos.tipo', 'saida') // Filtra apenas as entradas
            ->select('bancos.*') // Selecione todas as colunas da tabela 'caixa'
            ->get();

        $somaSaida = $saida->sum('valor'); //soma os valores de entrada

        return ([$somaEntradas, $somaSaida]); // Retorna o valor para o controlador
    }


    static public function getCadastroBanco()
    {
        $userId = auth()->user()->id; // Recupere o ID do usuário logado

        $entradas = Banco::join('company_user', 'bancos.company_id', '=', 'company_user.company_id')
            ->where('company_user.user_id', $userId)
            ->get();

        return $entradas;
    }


}
