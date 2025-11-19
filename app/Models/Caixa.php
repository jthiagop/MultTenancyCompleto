<?php

namespace App\Models;

use App\Models\Anexos\ModulosAnexos;
use Illuminate\Support\Facades\Auth;
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
        $companyId = session('active_company_id'); // Recupere a empresa do usuário logado

        return EntidadeFinanceira::where('tipo', 'caixa') // Filtra apenas pelo tipo banco
            ->where('company_id', $companyId) // Filtra pela empresa do usuário
            ->get();
    }

    static public function getEntidadesBanco()
    {
        $companyId = session('active_company_id'); // Recupere a empresa do usuário logado

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

        $entradas = DB::table('transacoes_financeiras')
            ->join('company_user', 'transacoes_financeiras.company_id', '=', 'company_user.company_id')
            ->where('company_user.user_id', $userId)
            ->where('transacoes_financeiras.tipo', 'entrada') // Filtra apenas as saídas (S para saída, E para entrada)
            ->where('transacoes_financeiras.origem', 'Caixa') // Filtra apenas as saídas (S para saída, E para entrada)
            ->whereYear('transacoes_financeiras.data_competencia', $currentYear) // Filtra pelo ano vigente
            ->whereMonth('transacoes_financeiras.data_competencia', $currentMonth) // Filtra pelo mês vigente
            ->whereNull('transacoes_financeiras.deleted_at') // Ignora registros excluídos (Soft Delete)
            ->select('transacoes_financeiras.*', 'transacoes_financeiras.origem')
            ->get();

        $somaEntradas = $entradas->sum('valor'); //soma os valores de entrada

        return $somaEntradas;
    }

    static public function getCaixaSaida()
    {

        $userId = Auth::user()->id; // Recupere o ID do usuário logado

        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        $saidas = DB::table('transacoes_financeiras')
            ->join('company_user', 'transacoes_financeiras.company_id', '=', 'company_user.company_id')
            ->where('company_user.user_id', $userId)
            ->where('transacoes_financeiras.tipo', 'saida') // Filtra apenas as saídas (S para saída, E para entrada)
            ->where('transacoes_financeiras.origem', 'Caixa') // Filtra apenas as saídas (S para saída, E para entrada)
            ->whereYear('transacoes_financeiras.data_competencia', $currentYear) // Filtra pelo ano vigente
            ->whereMonth('transacoes_financeiras.data_competencia', $currentMonth) // Filtra pelo mês vigente
            ->whereNull('transacoes_financeiras.deleted_at') // Ignora registros excluídos (Soft Delete)
            ->select('transacoes_financeiras.*', 'transacoes_financeiras.origem')
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

        /**
     * Retorna os ícones correspondentes para cada tipo de arquivo
     */
    public static function getFileIcons()
    {
        return [
            'pdf' => [
                'icon' => 'bi-file-earmark-pdf-fill',
                'color' => 'text-danger',
            ],
            'jpg' => [
                'icon' => 'bi-file-earmark-image-fill',
                'color' => 'text-warning',
            ],
            'jpeg' => [
                'icon' => 'bi-file-earmark-image-fill',
                'color' => 'text-primary',
            ],
            'png' => [
                'icon' => 'bi-file-earmark-image-fill',
                'color' => 'text-warning',
            ],
            'doc' => [
                'icon' => 'bi-file-earmark-word-fill',
                'color' => 'text-info',
            ],
            'docx' => [
                'icon' => 'bi-file-earmark-word-fill',
                'color' => 'text-info',
            ],
            'xls' => [
                'icon' => 'bi-file-earmark-spreadsheet-fill',
                'color' => 'text-warning',
            ],
            'xlsx' => [
                'icon' => 'bi-file-earmark-spreadsheet-fill',
                'color' => 'text-warning',
            ],
            'txt' => [
                'icon' => 'bi-file-earmark-text-fill',
                'color' => 'text-muted',
            ],
        ];
    }

    /**
     * Retorna o ícone padrão para arquivos desconhecidos
     */
    public static function getDefaultFileIcon()
    {
        return [
            'icon' => 'bi-file-earmark-fill',
            'color' => 'text-secondary',
        ];
    }

    /**
     * Retorna os dados do ícone para um arquivo específico
     */
    public static function getIconForFile($filename)
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $icons = self::getFileIcons();

        return $icons[$extension] ?? self::getDefaultFileIcon();
    }

    /**
     * Relacionamento com anexos
     */
    public function modulos_anexos()
    {
        return $this->hasMany(ModulosAnexos::class);
    }

    /**
     * Retorna os primeiros anexos (para visualização)
     */
    public function getPreviewAnexos($limit = 3)
    {
        return $this->modulos_anexos->take($limit);
    }

    /**
     * Conta anexos restantes além do preview
     */
    public function countRemainingAnexos($limit = 3)
    {
        return max(0, $this->modulos_anexos->count() - $limit);
    }
}
