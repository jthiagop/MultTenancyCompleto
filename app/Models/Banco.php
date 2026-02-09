<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Financeiro\TransacaoFinanceira;
use Illuminate\Database\Eloquent\Model;
use App\Models\Financeiro\ModulosAnexo;
use Illuminate\Support\Facades\Auth;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use File;

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
        return $this->morphMany(ModulosAnexo::class, 'anexable_id');
    }

    public function bancoCadastrado()
    {
        return $this->belongsTo(CadastroBanco::class, 'banco_id');
    }

    public function transacoes_financeiras()
    {
        return $this->belongsTo(TransacaoFinanceira::class);
    }

    public function modulos_anexos()
    {
        // "anexavel" é o mesmo sufixo usado em "anexavel_id" e "anexavel_type"
        return $this->morphMany(ModulosAnexo::class, 'anexavel');
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

        /**
     * Scope: Filtra a busca para incluir apenas os registros da empresa ativa na sessão.
     */
    public function scopeForActiveCompany($query)
    {
        $activeCompanyId = session('active_company_id');
        if ($activeCompanyId) {
            return $query->where('company_id', $activeCompanyId);
        }
        // Não retorna nada se nenhuma empresa estiver ativa para proteger os dados
        return $query->whereRaw('1 = 0');
    }

    static public function getBancoList()
    {
        $userId = Auth::user()->id; // Recupere o ID do usuário logado

        $entradas = DB::table('bancos')
            ->join('company_user', 'bancos.company_id', '=', 'company_user.company_id')
            ->where('company_user.user_id', $userId)
            ->get();

        return $entradas;
    }

    static public function getEntidadesBanco()
    {
        $companyId = session('active_company_id'); // Recupere a empresa do usuário logado

        return EntidadeFinanceira::where('tipo', 'banco') // Filtra apenas pelo tipo banco
            ->where('company_id', $companyId) // Filtra pela empresa do usuário
            ->get();
    }

    static public function getBancoEntrada()
    {
        $userId = Auth::user()->id; // Recupere o ID do usuário logado

        $companyId = session('active_company_id'); // Recupere a empresa do usuário logado

        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        $entradas = DB::table('transacoes_financeiras')
        ->where('transacoes_financeiras.company_id', $companyId)
        ->join('company_user', 'transacoes_financeiras.company_id', '=', 'company_user.company_id')
        ->where('company_user.user_id', $userId)
        ->where('transacoes_financeiras.tipo', 'entrada') // Filtra apenas as saídas (S para saída, E para entrada)
        ->where('transacoes_financeiras.origem', 'Banco') // Filtra apenas as saídas (S para saída, E para entrada)
        ->whereYear('transacoes_financeiras.data_competencia', $currentYear) // Filtra pelo ano vigente
        ->whereMonth('transacoes_financeiras.data_competencia', $currentMonth) // Filtra pelo mês vigente
        ->whereNull('transacoes_financeiras.deleted_at') // Ignora registros excluídos (Soft Delete)
        ->select('transacoes_financeiras.*', 'transacoes_financeiras.origem')
        ->get();

        $somaEntradas = $entradas->sum('valor'); //soma os valores de entrada

        return $somaEntradas;
    }

    static public function getBancoSaida()
    {

        $userId = Auth::user()->id; // Recupere o ID do usuário logado
        $companyId = session('active_company_id'); // Recupere a empresa do usuário logado
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

    // Consulta principal na tabela transacoes_financeiras
    $saidas = DB::table('transacoes_financeiras')
    ->where('transacoes_financeiras.company_id', $companyId)
        ->join('company_user', 'transacoes_financeiras.company_id', '=', 'company_user.company_id')
        ->where('company_user.user_id', $userId)
        ->where('transacoes_financeiras.tipo', 'saida') // Filtra apenas as saídas (S para saída, E para entrada)
        ->where('transacoes_financeiras.origem', 'Banco') // Filtra apenas as saídas (S para saída, E para entrada)
        ->whereYear('transacoes_financeiras.data_competencia', $currentYear) // Filtra pelo ano vigente
        ->whereMonth('transacoes_financeiras.data_competencia', $currentMonth) // Filtra pelo mês vigente
        ->whereNull('transacoes_financeiras.deleted_at') // Ignora registros excluídos (Soft Delete)
        ->select('transacoes_financeiras.*', 'transacoes_financeiras.origem')
        ->get();

        $SomaSaidas = $saidas->sum('valor'); //soma os valores de entrada

        return $SomaSaidas;
    }

    static public function getBanco()
    {

        $userId = Auth::user()->id; // Recupere o ID do usuário logado

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
        $userId = Auth::user()->id; // Recupere o ID do usuário logado

        $entradas = Banco::join('company_user', 'bancos.company_id', '=', 'company_user.company_id')
            ->where('company_user.user_id', $userId)
            ->get();

        return $entradas;
    }

        /**
     * Retorna a lista de bancos com os ícones (.svg) disponíveis.
     *
     * @return array
     */
    public static function getBancoIcons()
    {
        // Caminho para a pasta com os SVGs
        $svgPath = public_path('tenancy/assets/media/svg/bancos');

        // Verifica se o diretório existe
        if (!File::exists($svgPath)) {
            return [];
        }

        // Lista os arquivos .svg
        $svgFiles = File::files($svgPath);

        // Mapeia os arquivos para um array estruturado
        return collect($svgFiles)->map(function ($file) {
            $filename = pathinfo($file->getFilename(), PATHINFO_FILENAME);
            return [
                'slug' => $filename,
                'label' => ucfirst($filename), // Nome formatado (ex.: "Bradesco")
                'icon' => asset("assets/media/svg/bancos/{$file->getFilename()}"), // Caminho público
            ];
        })->toArray();
    }

    // 1. O Laravel usa isso para gerar a URL (route('banco.show', $banco))
    public function getRouteKey()
    {
        return Hashids::encode($this->getKey());
    }

    // 2. O Laravel usa isso para encontrar o model vindo da URL
    public function resolveRouteBinding($value, $field = null)
    {
        $decoded = Hashids::decode($value);
        return $this->where('id', $decoded[0] ?? null)->firstOrFail();
    }

}
